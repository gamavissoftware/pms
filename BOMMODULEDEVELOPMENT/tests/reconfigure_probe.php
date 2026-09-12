<?php
/**
 * reconfigure_probe.php
 *
 * Drives Abom::save_config()'s two private helpers — carry_line_edits()
 * and reconfigure_summary() — against the REAL controller source and
 * the REAL engine, with no CI bootstrap and no database.
 *
 * Why this harness exists at all: save_config() DELETES a saved BOM's
 * entire line set and writes a new one. Everything an engineer typed by
 * hand — added rows, remarks, quantity overrides — survives only
 * because carry_line_edits() puts it back. That is the single most
 * destructive path in the module, and "it looked right in the browser"
 * is not evidence for it.
 *
 *   php BOMMODULEDEVELOPMENT/tests/reconfigure_probe.php
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

require __DIR__ . '/seed_parser.php';
require __DIR__ . '/stubs.php';

defined('BASEPATH') OR define('BASEPATH', true);
defined('page_url') OR define('page_url', '/index.php/');

// All three seed files as a live install has run them — same
// concatenation run_tests.php uses, so both harnesses see one master
// dataset rather than two that can drift.
$combined = tempnam(sys_get_temp_dir(), 'abom');
file_put_contents($combined,
    file_get_contents($root . '/Database/abom_006_seed.sql') . "\n" .
    file_get_contents($root . '/Database/abom_009_seed_1250p.sql') . "\n" .
    file_get_contents($root . '/Database/abom_011_split_fx5je.sql'));

$seed = new Abom_seed_parser($combined);
register_shutdown_function(function () use ($combined) { @unlink($combined); });

$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);

require $root . '/application/libraries/Abom_engine.php';

/**
 * The controller extends CI_Controller and its constructor loads models
 * and config. Neither is needed to exercise two pure private methods,
 * so the class is instantiated WITHOUT its constructor over a stub base.
 */
if (!class_exists('CI_Controller')) {
    class CI_Controller
    {
        public function __get($k) { return $GLOBALS['ABOM_STUB_CI']->$k; }
    }
}
if (!function_exists('show_404')) { function show_404() {} }

require $root . '/application/controllers/Abom.php';

$ctl = (new ReflectionClass('Abom'))->newInstanceWithoutConstructor();

$carry = new ReflectionMethod('Abom', 'carry_line_edits');
$carry->setAccessible(true);
$sum = new ReflectionMethod('Abom', 'reconfigure_summary');
$sum->setAccessible(true);

$engine = new Abom_engine();
$master = $GLOBALS['ABOM_STUB_CI']->Abom_master_model;

$pass = 0;
$fail = 0;

function ok($label, $got, $want)
{
    global $pass, $fail;
    $good = ($got === $want);
    $good ? $pass++ : $fail++;
    printf("  %s  %-52s %s\n",
        $good ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m",
        $label,
        $good ? '' : ("got " . var_export($got, true) . ", want " . var_export($want, true)));
}

function cfg(array $over = array())
{
    global $master;
    return array_merge(array(
        'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100,
        'motion_type' => 'Intermittent', 'machine_model' => 'SPM1200L',
        'machine_side' => 'N/A', 'j4_units' => 0, 'battery_qty' => 6,
        'df_ref' => 'DF-1808', 'plc_family_id' => null, 'variant_id' => null,
        'features' => $master->default_features(),
    ), $over);
}

function temp_qty(array $lines)
{
    foreach ($lines as $l) {
        if ($l->formula_code === 'TRACK_TEMP') { return (int) $l->qty; }
    }
    return null;
}

echo "\nRECONFIGURE PROBE — save_config() line carry-over\n" . str_repeat('=', 72) . "\n\n";

// ---------------------------------------------------------------------
// The "before" document: DF-1808, 6 axis / 12 track, with an engineer's
// hand work on it — a typed remark, a quantity override, and an added
// row that exists in no master build.
// ---------------------------------------------------------------------
$before = $engine->generate(cfg());
$old    = $before['lines'];

echo "BEFORE  DF-1808  6 axis / 12 track  -> " . count($old) . " lines, build "
   . $before['variant']->code . ", temperature cards = " . temp_qty($old) . "\n";

$remark_item   = (int) $old[2]->item_id;
$override_item = (int) $old[4]->item_id;

$old[2]->user_remark    = 'Customer asked for the 24V version.';
$old[4]->is_overridden  = 1;
$old[4]->qty            = 99;
$old[4]->override_reason= 'spare held at site';

// A remark on an item that only the 6-axis JE build carries, so a jump
// to a 15-axis iQ-R machine cannot keep it.
$last = $old[count($old) - 1];
$last->user_remark = 'JE-only note.';
$je_only_item = (int) $last->item_id;

$manual = new stdClass();
foreach (get_object_vars($old[0]) as $k => $v) { $manual->$k = $v; }
$manual->item_id       = null;
$manual->is_manual_add = 1;
$manual->description   = 'CUSTOMER SUPPLIED ISOLATOR';
$manual->part_no       = 'CS-ISO-01';
$manual->erp_code      = '';
$manual->qty           = 2;
$manual->user_remark   = 'free issue';
$old[] = $manual;

echo "        + 1 hand-added row, 2 remarks, 1 quantity override\n\n";

// ---------------------------------------------------------------------
// CASE 1 — same build, more tracks. TRACK_TEMP must move; everything
// hand-typed must survive, because every item is still present.
// ---------------------------------------------------------------------
echo "CASE 1  tracks 12 -> 6, same build\n";
$after = $engine->generate(cfg(array('tracks' => 6)));
$c     = $carry->invoke($ctl, $old, $after['lines']);

ok('build unchanged', $after['variant']->code, $before['variant']->code);
ok('temperature cards re-derived for 6 tracks', temp_qty($c['lines']), 4);
ok('hand-added row carried', $c['manual'], 1);
ok('both remarks carried', $c['remarks'], 2);
ok('quantity override carried', $c['overrides'], 1);
ok('nothing dropped', $c['dropped'], array());
ok('line count = generated + manual', count($c['lines']), count($after['lines']) + 1);

$byitem = array();
foreach ($c['lines'] as $l) { if (!empty($l->item_id)) { $byitem[(int) $l->item_id] = $l; } }
ok('remark landed on the SAME item', $byitem[$remark_item]->user_remark,
   'Customer asked for the 24V version.');
ok('override qty landed on the same item', (int) $byitem[$override_item]->qty, 99);
ok('override reason survived', $byitem[$override_item]->override_reason, 'spare held at site');
ok('override flag set', (int) $byitem[$override_item]->is_overridden, 1);

$tail = $c['lines'][count($c['lines']) - 1];
ok('added row is last and intact', $tail->part_no, 'CS-ISO-01');
ok('added row keeps is_manual_add', (int) $tail->is_manual_add, 1);
ok('added row keeps its remark', $tail->user_remark, 'free issue');

$nos = array();
foreach ($c['lines'] as $l) { $nos[] = (int) $l->line_no; }
ok('line numbers are 1..n with no gap', $nos, range(1, count($c['lines'])));

// The override must NOT overwrite what the rule would have said.
ok('computed_qty is the NEW engine answer, not the old override',
   (int) $byitem[$override_item]->computed_qty !== 99, true);

echo "\n";

// ---------------------------------------------------------------------
// CASE 2 — a different machine entirely. The build changes, so some
// hand work has nowhere to land. It must be REPORTED, not lost quietly.
// ---------------------------------------------------------------------
echo "CASE 2  6 axis FX5-JE -> 15 axis iQ-R (build changes)\n";
$big = $engine->generate(cfg(array('axes' => 15, 'speed_ppm' => 180,
    'motion_type' => 'Continuous', 'j4_units' => 11, 'battery_qty' => 12)));
$c2  = $carry->invoke($ctl, $old, $big['lines']);

ok('build did change', $big['variant']->code !== $before['variant']->code, true);
ok('hand-added row still carried', $c2['manual'], 1);
ok('something could not be carried', count($c2['dropped']) > 0, true);
ok('the JE-only remark is the thing reported',
   in_array('remark on item #' . $je_only_item, $c2['dropped'], true), true);

$byitem2 = array();
foreach ($c2['lines'] as $l) { if (!empty($l->item_id)) { $byitem2[(int) $l->item_id] = $l; } }
$reported = count($c2['dropped']);
$landed   = $c2['remarks'] + $c2['overrides'];
ok('carried + dropped accounts for all 3 edits', $landed + $reported, 3);

echo "\n";

// ---------------------------------------------------------------------
// CASE 3 — no hand work at all. The commonest case must not invent any.
// ---------------------------------------------------------------------
echo "CASE 3  a clean BOM, nothing typed on it\n";
$clean = $engine->generate(cfg());
$c3    = $carry->invoke($ctl, $clean['lines'], $after['lines']);
ok('no manual rows', $c3['manual'], 0);
ok('no remarks carried', $c3['remarks'], 0);
ok('no overrides carried', $c3['overrides'], 0);
ok('nothing reported dropped', $c3['dropped'], array());
ok('line set is exactly the new generation', count($c3['lines']), count($after['lines']));

echo "\n";

// ---------------------------------------------------------------------
// CASE 4 — the message the user is shown. It has to name what changed
// and what was kept; "Saved." after rewriting a parts list is not
// enough for anyone to know whether the result is what they meant.
// ---------------------------------------------------------------------
echo "CASE 4  the confirmation message\n";
$bom = new stdClass();
$bom->bom_no = 'ABOM-14';
$bom->df_ref = 'DF-1808'; $bom->machine_model = 'SPM1200L'; $bom->machine_side = 'N/A';
$bom->axes = 6; $bom->tracks = 12; $bom->speed_ppm = 100; $bom->motion_type = 'Intermittent';

$header = array('df_ref' => 'DF-1808', 'machine_model' => 'SPM1200L', 'machine_side' => 'N/A',
    'axes' => 6, 'tracks' => 6, 'speed_ppm' => 100, 'motion_type' => 'Intermittent');

$msg = $sum->invoke($ctl, $bom, $header, $c);
echo "  \"" . $msg . "\"\n\n";

ok('names the BOM', strpos($msg, 'ABOM-14') !== false, true);
ok('names the changed field and both values', strpos($msg, 'tracks 12 -> 6') !== false, true);
ok('does not claim unchanged fields changed', strpos($msg, 'axes') === false, true);
ok('states the regenerated count', strpos($msg, count($c['lines']) . ' lines regenerated') !== false, true);
ok('states what was carried', strpos($msg, 'Carried over: 1 added row, 2 remarks, 1 quantity override') !== false, true);

$msg2 = $sum->invoke($ctl, $bom, $header, $c2);
ok('reports what could not be carried', strpos($msg2, 'Could not be carried') !== false, true);

// ---------------------------------------------------------------------
// CASE 5 — the seeded-remark boundary. Abom_engine fills user_remark
// from the master item, so "not empty" cannot mean "typed by hand".
// Only a DIFFERENCE from the seeded text counts — including a
// deliberate clear, which must not be quietly undone.
// ---------------------------------------------------------------------
echo "CASE 5  seeded remarks vs. hand-set ones\n";

$seedy = $engine->generate(cfg());
$rows  = $seedy['lines'];

$seeded_line = null;
foreach ($rows as $l) {
    if (trim((string) $l->user_remark) !== '') { $seeded_line = $l; break; }
}
ok('the engine does seed remarks (else this case proves nothing)',
   $seeded_line !== null, true);

$c5 = $carry->invoke($ctl, $rows, $engine->generate(cfg())['lines']);
ok('untouched seeded remarks are NOT counted as hand-set', $c5['remarks'], 0);

$cleared_item = (int) $seeded_line->item_id;
$seeded_line->user_remark = '';
$c6 = $carry->invoke($ctl, $rows, $engine->generate(cfg())['lines']);
ok('clearing a seeded remark IS a hand edit', $c6['remarks'], 1);

foreach ($c6['lines'] as $l) {
    if (!empty($l->item_id) && (int) $l->item_id === $cleared_item) {
        ok('the cleared remark stays cleared', trim((string) $l->user_remark), '');
    }
}

$seeded_line->user_remark = trim((string) $seeded_line->usage_remark) . ' — checked on site';
$c7 = $carry->invoke($ctl, $rows, $engine->generate(cfg())['lines']);
ok('appending to a seeded remark IS a hand edit', $c7['remarks'], 1);

echo "\n" . str_repeat('-', 72) . "\n";
if ($fail === 0) {
    echo "\033[32m\033[1m  ALL {$pass} ASSERTIONS PASSED\033[0m\n";
} else {
    echo "\033[31m\033[1m  {$fail} FAILED, {$pass} passed\033[0m\n";
}
echo str_repeat('-', 72) . "\n\n";

exit($fail === 0 ? 0 : 1);
