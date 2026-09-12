<?php
/**
 * seed_parser.php
 *
 * Parses abom_seed.sql into plain PHP arrays so the section 5.1 unit
 * tests can exercise Abom_engine with no database and no CodeIgniter
 * bootstrap.
 *
 * The engine is tested against THE SAME BYTES that will be loaded into
 * MySQL. Nothing is re-keyed, so a seed edit that changes the 29 / 42
 * regression counts will fail the tests rather than pass silently.
 *
 * PHP 7.4 compatible.
 */

class Abom_seed_parser
{
    /** @var array [table => [ ['columns'=>[], 'rows'=>[[]]], ... ]] */
    private $tables = array();

    public function __construct($sql_path)
    {
        if (!is_readable($sql_path)) {
            throw new RuntimeException('Cannot read seed file: ' . $sql_path);
        }

        $sql = file_get_contents($sql_path);

        $this->parse($sql);
        $this->apply_updates($sql);          // after, so INSERTs exist to update
    }

    /**
     * @param  string $table
     * @return array  list of stdClass rows
     */
    public function rows($table)
    {
        if (!isset($this->tables[$table])) {
            return array();
        }

        // EACH INSERT KEEPS ITS OWN COLUMN LIST.
        //
        // This used to hold one column list per table and append every
        // INSERT's values to it, which is fine while a table is seeded by
        // a single statement. It stops being fine the moment a later
        // migration adds a column and a second seed file writes it:
        // abom_009 inserts into abom_plc_rule with the machine_model
        // that abom_008 added, and abom_006 inserted without it.
        //
        // MySQL gives every INSERT its own column list. So does this now.
        // A row from a block that did not name a column gets NULL for it,
        // which is exactly what MySQL does with a nullable column.
        $all_columns = array();
        foreach ($this->tables[$table] as $block) {
            foreach ($block['columns'] as $c) {
                $all_columns[$c] = true;
            }
        }
        $all_columns = array_keys($all_columns);

        $out = array();

        foreach ($this->tables[$table] as $block) {
            $columns = $block['columns'];

            foreach ($block['rows'] as $values) {
                if (count($values) !== count($columns)) {
                    throw new RuntimeException(sprintf(
                        'Column/value count mismatch in %s: %d columns, %d values',
                        $table,
                        count($columns),
                        count($values)
                    ));
                }

                $row = array_fill_keys($all_columns, null);

                foreach (array_combine($columns, $values) as $k => $v) {
                    $row[$k] = $v;
                }

                $out[] = (object) $row;
            }
        }

        return $out;
    }

    // -----------------------------------------------------------------

    /**
     * Simple UPDATE support: `UPDATE `t` SET `c` = v WHERE `c` = v`.
     *
     * The migrations use exactly that shape and nothing more — they
     * deactivate a row, or correct one column on one id. Without this
     * the fixture reflects only the INSERTs, so a build the migration
     * RETIRES still looks live here and the suite tests a database that
     * does not exist. abom_011 retires FX5-JE that way.
     *
     * Deliberately not a SQL engine. Anything more complex is ignored
     * rather than half-applied, and applying is announced in the header
     * of any migration that relies on it.
     *
     * @return void
     */
    private function apply_updates($sql)
    {
        $re = '/UPDATE\s+`(\w+)`\s+SET\s+(.+?)\s+WHERE\s+`(\w+)`\s*=\s*([0-9]+|\'[^\']*\')\s*(?:AND[^;]*)?;/is';

        if (!preg_match_all($re, $sql, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $m) {
            list(, $table, $sets, $where_col, $where_val) = $m;

            if (!isset($this->tables[$table])) {
                continue;
            }

            $where_val = trim($where_val, "'");

            // SET clause: `col` = value, `col` = value
            $assign = array();
            foreach (explode(',', $sets) as $pair) {
                if (!preg_match('/`(\w+)`\s*=\s*(NULL|[0-9]+|\'(?:[^\']|\\\')*\')/is', $pair, $a)) {
                    continue;
                }
                $v = $a[2];
                $assign[$a[1]] = (strcasecmp($v, 'NULL') === 0)
                    ? null
                    : str_replace("\\'", "'", trim($v, "'"));
            }

            if (empty($assign)) {
                continue;
            }

            foreach ($this->tables[$table] as $bi => $block) {
                $ci = array_search($where_col, $block['columns'], true);

                if ($ci === false) {
                    continue;
                }

                foreach ($block['rows'] as $ri => $values) {
                    if ((string) $values[$ci] !== (string) $where_val) {
                        continue;
                    }

                    foreach ($assign as $col => $val) {
                        $t = array_search($col, $block['columns'], true);

                        // A column the INSERT never mentioned. That is
                        // the ALTER TABLE ... ADD COLUMN + UPDATE
                        // pattern the later migrations use, and
                        // dropping the assignment would make the
                        // migration look like it had done nothing.
                        //
                        // Added to the block with NULL for every other
                        // row, which is what the ALTER itself does.
                        if ($t === false) {
                            $this->tables[$table][$bi]['columns'][] = $col;
                            $block['columns'][] = $col;
                            $t = count($block['columns']) - 1;

                            foreach ($this->tables[$table][$bi]['rows'] as $k => $unused) {
                                $this->tables[$table][$bi]['rows'][$k][$t] = null;
                            }
                        }

                        $this->tables[$table][$bi]['rows'][$ri][$t] = $val;
                    }
                }
            }
        }
    }

    private function parse($sql)
    {
        $offset = 0;

        while (true) {
            $pos = strpos($sql, 'INSERT INTO', $offset);
            if ($pos === false) {
                break;
            }

            // Table name: INSERT INTO `table`
            if (!preg_match('/INSERT INTO\s+`([^`]+)`/A', $sql, $m, 0, $pos)) {
                $offset = $pos + 11;
                continue;
            }
            $table  = $m[1];
            $cursor = $pos + strlen($m[0]);

            // Column list, then the VALUES keyword.
            $columns = $this->read_column_list($sql, $cursor);
            $vpos    = stripos($sql, 'VALUES', $cursor);
            if ($vpos === false) {
                break;
            }
            $cursor = $vpos + 6;

            $rows = $this->read_value_tuples($sql, $cursor);

            if (!isset($this->tables[$table])) {
                $this->tables[$table] = array();
            }

            // One block per INSERT statement, carrying that statement's
            // own column list. See rows().
            $this->tables[$table][] = array('columns' => $columns, 'rows' => $rows);

            $offset = $cursor;
        }
    }

    /**
     * Reads the ( `a`,`b`,... ) list that follows the table name.
     * Advances $cursor past the closing bracket.
     */
    private function read_column_list($sql, &$cursor)
    {
        $open = strpos($sql, '(', $cursor);
        if ($open === false) {
            return array();
        }

        $close = strpos($sql, ')', $open);
        $raw   = substr($sql, $open + 1, $close - $open - 1);
        $cursor = $close + 1;

        $columns = array();
        foreach (explode(',', $raw) as $part) {
            $columns[] = trim(trim($part), "` \t\r\n");
        }

        return $columns;
    }

    /**
     * Character scanner over the (...),(...); value list.
     *
     * Handles single-quoted strings with backslash escapes (the seed
     * contains \' inside remark text), NULL, integers, and the unicode
     * characters ° Ω ⚠ − ▶ which must survive untouched.
     *
     * Advances $cursor past the terminating semicolon.
     *
     * @return array  list of value arrays
     */
    private function read_value_tuples($sql, &$cursor)
    {
        $rows    = array();
        $len     = strlen($sql);
        $i       = $cursor;
        $current = null;
        $field   = '';
        $quoted  = false;
        $in_str  = false;

        while ($i < $len) {
            $ch = $sql[$i];

            if ($in_str) {
                if ($ch === '\\' && $i + 1 < $len) {
                    // Backslash escape — keep the escaped character raw.
                    $field .= $sql[$i + 1];
                    $i += 2;
                    continue;
                }
                if ($ch === "'") {
                    // Doubled '' is a literal quote inside the string.
                    if ($i + 1 < $len && $sql[$i + 1] === "'") {
                        $field .= "'";
                        $i += 2;
                        continue;
                    }
                    $in_str = false;
                    $i++;
                    continue;
                }
                $field .= $ch;
                $i++;
                continue;
            }

            if ($ch === "'") {
                $in_str = true;
                $quoted = true;
                $i++;
                continue;
            }

            if ($ch === '(' && $current === null) {
                $current = array();
                $field   = '';
                $quoted  = false;
                $i++;
                continue;
            }

            if ($current !== null && ($ch === ',' || $ch === ')')) {
                $current[] = $this->cast($field, $quoted);
                $field     = '';
                $quoted    = false;

                if ($ch === ')') {
                    $rows[]  = $current;
                    $current = null;
                }
                $i++;
                continue;
            }

            if ($ch === ';' && $current === null) {
                $i++;
                break;
            }

            if ($current !== null) {
                $field .= $ch;
            }
            $i++;
        }

        $cursor = $i;

        return $rows;
    }

    /**
     * An unquoted NULL becomes PHP null; everything else stays a string,
     * matching what the mysqli driver hands CodeIgniter.
     *
     * @return string|null
     */
    private function cast($raw, $was_quoted)
    {
        if ($was_quoted) {
            return $raw;
        }

        $trimmed = trim($raw);

        if (strcasecmp($trimmed, 'NULL') === 0 || $trimmed === '') {
            return null;
        }

        return $trimmed;
    }
}
