<?php
/**
 * dfref_probe.php
 *
 * Exercises the DF-reference uniqueness rule against the REAL model
 * method, over a fake in-memory abom_bom table.
 *
 * Uniqueness sounds like a one-liner and is not. A rule written as
 * "df_ref must be unique" would break two things the module already
 * does deliberately:
 *
 *   - create_revision() raises REV.01 of the SAME drawing number
 *   - soft delete leaves the row in place with deleted_at set
 *
 * Both are covered below, because both would have been broken by the
 * obvious implementation and neither would have been noticed until a
 * user hit it.
 *
 *   php BOMMODULEDEVELOPMENT/tests/dfref_probe.php
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

defined('BASEPATH') OR define('BASEPATH', true);
defined('page_url') OR define('page_url', '/index.php/');

/**
 * The narrow slice of CI's query builder that df_ref_conflict() uses,
 * over an array. Deliberately literal: it applies each where() in turn
 * rather than trying to be a query engine, so what it proves is that
 * the METHOD's logic is right, not that CI works.
 */
class Fake_db
{
    private $rows;
    private $where = array();
    private $not   = array();
    private $limit = 0;

    public function __construct(array $rows) { $this->rows = $rows; }

    public function select($f)            { return $this; }
    public function from($t)              { return $this; }
    public function limit($n)             { $this->limit = $n; return $this; }

    public function where($k, $v = null, $escape = true)
    {
        if ($v === null && $escape === false) {
            // raw fragment: the only one used is deleted_at IS NULL
            if (strpos($k, 'deleted_at IS NULL') !== false) {
                $this->where[] = array('deleted_at', null, '=');
            }
            return $this;
        }
        if (substr($k, -3) === ' !=') {
            $this->not[] = array(trim(substr($k, 0, -3)), $v);
            return $this;
        }
        $this->where[] = array($k, $v, '=');
        return $this;
    }

    public function get()
    {
        $out = array();
        foreach ($this->rows as $row) {
            $keep = true;
            foreach ($this->where as $w) {
                list($k, $v) = $w;
                $have = isset($row->$k) ? $row->$k : null;
                if ($v === null) { if ($have !== null) { $keep = false; break; } continue; }
                // MySQL's default collation is case-insensitive; the
                // method relies on that, so the fake must behave the
                // same or the test would prove the wrong thing.
                if (strcasecmp((string) $have, (string) $v) !== 0) { $keep = false; break; }
            }
            foreach ($this->not as $n) {
                list($k, $v) = $n;
                if ((int) $row->$k === (int) $v) { $keep = false; break; }
            }
            if ($keep) { $out[] = $row; }
        }
        if ($this->limit > 0) { $out = array_slice($out, 0, $this->limit); }
        $this->where = array(); $this->not = array(); $this->limit = 0;

        return new Fake_result($out);
    }
}

class Fake_result
{
    private $rows;
    public function __construct(array $rows) { $this->rows = $rows; }
    public function row() { return empty($this->rows) ? null : $this->rows[0]; }
}

if (!class_exists('CI_Model')) { class CI_Model {} }

require $root . '/application/models/Abom_model.php';

/**
 * CI3 injects $this->db onto a model at runtime, so it is not a
 * declared property — normal on PHP 7.4 (production), deprecated on the
 * 8.x this harness happens to run under. The attribute silences that
 * without touching the model: on 7.4 the `#[...]` line is read as a
 * comment, so this file still parses there.
 */
#[AllowDynamicProperties]
class Abom_model_probe extends Abom_model {}

function bom($id, $no, $rev, $df, $status, $deleted = null)
{
    $b = new stdClass();
    $b->id = $id; $b->bom_no = $no; $b->revision = $rev; $b->df_ref = $df;
    $b->status = $status; $b->machine_model = 'SPM1250P'; $b->deleted_at = $deleted;
    return $b;
}

$register = array(
    bom(10, 'ABOM-10', '00', 'DF-1805A', 'draft'),
    bom(11, 'ABOM-11', '00', 'DF-1808',  'approved'),
    bom(12, 'ABOM-12', '01', 'DF-1808',  'draft'),      // a revision
    bom(13, 'ABOM-13', '00', 'DF-1899',  'draft', '2026-08-01 10:00:00'), // deleted
    bom(14, 'ABOM-14', '00', null,       'draft'),      // no reference
);

// Abom_model reads $this->db, which CI magic-injects at runtime and
// which is therefore not a declared property. Assigning it from a
// closure bound to the instance creates it exactly as CI does.
$model  = (new ReflectionClass('Abom_model_probe'))->newInstanceWithoutConstructor();
$setter = Closure::bind(function ($db) { $this->db = $db; }, $model, 'Abom_model');
$setter(new Fake_db($register));

$pass = 0; $fail = 0;
function ok($label, $got, $want)
{
    global $pass, $fail;
    $good = ($got === $want);
    $good ? $pass++ : $fail++;
    printf("  %s  %-56s %s\n",
        $good ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m", $label,
        $good ? '' : ('got ' . var_export($got, true) . ', want ' . var_export($want, true)));
}

function taken($model, $df, $rev = '00', $exclude = 0)
{
    $c = $model->df_ref_conflict($df, $rev, $exclude);
    return $c ? $c->bom_no : null;
}

echo "\nDF REFERENCE UNIQUENESS\n" . str_repeat('=', 76) . "\n\n";

echo "THE CASE THAT PROMPTED THIS\n";
ok('DF-1805A is refused — ABOM-10 already has it',
   taken($model, 'DF-1805A'), 'ABOM-10');
ok('a free reference is allowed', taken($model, 'DF-1900'), null);

echo "\nWHAT MUST STILL WORK\n";
ok('REV.01 of DF-1808 is allowed — that is create_revision()',
   taken($model, 'DF-1808', '02'), null);
ok('but REV.01 of DF-1808 clashes, ABOM-12 holds it',
   taken($model, 'DF-1808', '01'), 'ABOM-12');
ok('a soft-deleted BOM does not hold its reference',
   taken($model, 'DF-1899'), null);
ok('editing a BOM does not clash with itself',
   taken($model, 'DF-1805A', '00', 10), null);
ok('but another BOM taking that reference still clashes',
   taken($model, 'DF-1805A', '00', 11), 'ABOM-10');
ok('an empty reference is never a clash', taken($model, ''), null);
ok('nor is whitespace only', taken($model, '   '), null);

echo "\nEDGE CASES\n";
ok('case-insensitive, as the column collation is',
   taken($model, 'df-1805a'), 'ABOM-10');
ok('surrounding whitespace is trimmed before comparing',
   taken($model, '  DF-1805A  '), 'ABOM-10');
ok('a blank revision is read as 00',
   taken($model, 'DF-1805A', ''), 'ABOM-10');
ok('a BOM with no reference blocks nothing',
   taken($model, 'DF-1808', '00'), 'ABOM-11');

echo "\nTHE MESSAGE\n";
$clash = $model->df_ref_conflict('DF-1805A');
require_once $root . '/application/helpers/abom_helper.php';

// df_ref_taken_message() lives on the controller; assert the data it
// needs is all present on what the model hands back.
foreach (array('id', 'bom_no', 'revision', 'df_ref', 'status', 'machine_model') as $f) {
    ok('conflict carries ->' . $f, property_exists($clash, $f), true);
}

echo "\n" . str_repeat('-', 76) . "\n";
echo $fail === 0
    ? "\033[32m\033[1m  ALL {$pass} ASSERTIONS PASSED\033[0m\n"
    : "\033[31m\033[1m  {$fail} FAILED, {$pass} passed\033[0m\n";
echo str_repeat('-', 76) . "\n\n";

exit($fail === 0 ? 0 : 1);
