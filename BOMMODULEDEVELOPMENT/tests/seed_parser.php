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
    /** @var array [table => ['columns'=>[], 'rows'=>[[]]]] */
    private $tables = array();

    public function __construct($sql_path)
    {
        if (!is_readable($sql_path)) {
            throw new RuntimeException('Cannot read seed file: ' . $sql_path);
        }

        $this->parse(file_get_contents($sql_path));
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

        $columns = $this->tables[$table]['columns'];
        $out     = array();

        foreach ($this->tables[$table]['rows'] as $values) {
            if (count($values) !== count($columns)) {
                throw new RuntimeException(sprintf(
                    'Column/value count mismatch in %s: %d columns, %d values',
                    $table,
                    count($columns),
                    count($values)
                ));
            }
            $out[] = (object) array_combine($columns, $values);
        }

        return $out;
    }

    // -----------------------------------------------------------------

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
                $this->tables[$table] = array('columns' => $columns, 'rows' => array());
            }
            foreach ($rows as $row) {
                $this->tables[$table]['rows'][] = $row;
            }

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
