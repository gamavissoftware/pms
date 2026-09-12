<?php
/**
 * version_probe.php
 *
 * Drives the REAL Abom_revision_model::diff() — the changelog between
 * two versions of a BOM.
 *
 * This is the piece with no safe failure mode. A diff that misses a
 * removed part tells a reviewer "nothing left the sheet" when something
 * did, and the reviewer approves on that basis. Nothing errors, nothing
 * looks wrong, and a machine is built short of a part.
 *
 * The matching rule is the whole risk: lines are compared by MASTER
 * ITEM, never by position, because inserting one row near the top would
 * otherwise report every row below it as both removed and added.
 *
 *   php BOMMODULEDEVELOPMENT/tests/version_probe.php
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

require __DIR__ . '/seed_parser.php';
require __DIR__ . '/stubs.php';

defined('BASEPATH') OR define('BASEPATH', true);

$combined = tempnam(sys_get_temp_dir(), 'abom');
file_put_contents($combined,
    file_get_contents($root . '/Database/abom_006_seed.sql') . "\n" .
    file_get_contents($root . '/Database/abom_009_seed_1250p.sql') . "\n" .
    file_get_contents($root . '/Database/abom_011_split_fx5je.sql') . "\n" .
    file_get_contents($root . '/Database/abom_012_variant_reference.sql'));

$seed = new Abom_seed_parser($combined);
register_shutdown_function(function () use ($combined) { @unlink($combined); });

$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);

require $root . '/application/libraries/Abom_engine.php';

if (!class_exists('CI_Model')) { class CI_Model {} }

require $root . '/application/models/Abom_revision_model.php';

/**
 * readable() resolves family ids, variant ids and the feature JSON
 * through Abom_master_model. Pointed at the stub, which reads the same
 * shipped seed the live install runs.
 */
#[AllowDynamicProperties]
class Abom_revision_model_probe extends Abom_revision_model {}

$model = (new ReflectionClass('Abom_revision_model_probe'))->newInstanceWithoutConstructor();
Closure::bind(function ($ci) {
    // $this->load is CI's LOADER, not the CI instance — readable()
    // calls $this->load->model(), and the stub loader is a no-op that
    // returns immediately because the model is already attached below.
    $this->load = $ci->load;
    $this->db   = null;
    $this->Abom_master_model = $ci->Abom_master_model;
}, $model, 'Abom_revision_model')($GLOBALS['ABOM_STUB_CI']);

$pass = 0; $fail = 0;
function ok($label, $got, $want)
{
    global $pass, $fail;
    $good = ($got === $want);
    $good ? $pass++ : $fail++;
    printf("  %s  %-56s %s\n",
        $good ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m", $label,
        $good ? '' : ("\n        got  " . var_export($got, true)
                    . "\n        want " . var_export($want, true)));
}

/** A BOM line as abom_bom_line stores it. */
function ln($item_id, $erp, $desc, $qty, $extra = array())
{
    return array_merge(array(
        'item_id' => $item_id, 'erp_code' => $erp, 'description' => $desc,
        'part_no' => 'P-' . $erp, 'manufacturer' => 'MITSUBISHI',
        'qty' => $qty, 'user_remark' => '', 'is_manual_add' => 0,
        'section_name' => 'PLC', 'section_order' => 1,
    ), $extra);
}

function ver(array $lines, array $header = array())
{
    return array(
        'header' => array_merge(array(
            'df_ref' => 'DF-1808', 'machine_model' => 'SPM1200L',
            'machine_side' => 'N/A', 'axes' => 6, 'tracks' => 12,
            'speed_ppm' => 100, 'motion_type' => 'Intermittent',
            'plc_family_id' => 1, 'variant_id' => 10,
            'j4_units' => 0, 'battery_qty' => 6,
            'features_json' => '{"feat_perf":1}', 'notes' => '',
        ), $header),
        'lines' => $lines,
    );
}

$base = ver(array(
    ln(101, '4140023', 'PLC CPU MODULE',       1),
    ln(102, '4010224', 'MAIN BASE UNIT',       1),
    ln(103, '2020122', '4 CH TEMPERATURE CARD', 7),
    ln(104, '4100058', 'CONVERTER MODULE',     1),
));

echo "\nVERSION DIFF\n" . str_repeat('=', 80) . "\n\n";

echo "FIRST VERSION\n";
$d = $model->diff(null, $base);
ok('is flagged as the first', $d['first'], true);
ok('counts every line as added', $d['counts']['added'], 4);
ok('reports nothing removed', $d['counts']['removed'], 0);

echo "\nNO CHANGE\n";
$d = $model->diff($base, $base);
ok('an identical version reports nothing', $model->diff_is_empty($d), true);
ok('  not even a header change', $d['counts']['header'], 0);

echo "\nA PART LEAVES THE SHEET — the change that must never be missed\n";
$gone = ver(array(
    ln(101, '4140023', 'PLC CPU MODULE', 1),
    ln(102, '4010224', 'MAIN BASE UNIT', 1),
    ln(104, '4100058', 'CONVERTER MODULE', 1),
));
$d = $model->diff($base, $gone);
ok('exactly one removal', $d['counts']['removed'], 1);
ok('  and it is the right part',
   $d['removed'][0]['erp_code'], '2020122');
ok('  with its old quantity, so the loss is quantified',
   (int) $d['removed'][0]['qty'], 7);
ok('nothing reported as added', $d['counts']['added'], 0);

echo "\nPOSITION MUST NOT MATTER — the trap this design exists to avoid\n";
// One row inserted at the TOP. Compared by position this would read as
// four removals and five additions; compared by item it is one addition.
$inserted = ver(array(
    ln(999, '9999999', 'NEW ISOLATOR',        2),
    ln(101, '4140023', 'PLC CPU MODULE',      1),
    ln(102, '4010224', 'MAIN BASE UNIT',      1),
    ln(103, '2020122', '4 CH TEMPERATURE CARD', 7),
    ln(104, '4100058', 'CONVERTER MODULE',    1),
));
$d = $model->diff($base, $inserted);
ok('one addition, not five', $d['counts']['added'], 1);
ok('and NO spurious removals', $d['counts']['removed'], 0);
ok('the added part is the new one', $d['added'][0]['erp_code'], '9999999');

// Same lines, shuffled. Nothing changed at all.
$shuffled = ver(array(
    ln(104, '4100058', 'CONVERTER MODULE',    1),
    ln(103, '2020122', '4 CH TEMPERATURE CARD', 7),
    ln(101, '4140023', 'PLC CPU MODULE',      1),
    ln(102, '4010224', 'MAIN BASE UNIT',      1),
));
ok('reordering alone reports nothing', $model->diff_is_empty($model->diff($base, $shuffled)), true);

echo "\nQUANTITIES\n";
$qty = ver(array(
    ln(101, '4140023', 'PLC CPU MODULE',      1),
    ln(102, '4010224', 'MAIN BASE UNIT',      1),
    ln(103, '2020122', '4 CH TEMPERATURE CARD', 4),   // 12 track -> 6 track
    ln(104, '4100058', 'CONVERTER MODULE',    1),
));
$d = $model->diff($base, $qty);
ok('one quantity change', $d['counts']['qty'], 1);
ok('  from', (int) $d['qty'][0]['was'], 7);
ok('  to',   (int) $d['qty'][0]['now'], 4);
ok('  and it is not also reported as add/remove',
   $d['counts']['added'] + $d['counts']['removed'], 0);

echo "\nMASTER-DATA CORRECTIONS PICKED UP BY A NEW REVISION\n";
// A frozen line duplicates description, part no and ERP on purpose. If
// one moves between revisions the master item was corrected, and that
// changes what gets ordered.
// part_no is pinned to the ORIGINAL value on the ERP-corrected row.
// ln() derives it from the ERP code, so leaving it to derive would
// change two fields on one line and the test would be asserting
// against its own fixture rather than against the diff.
$corrected = ver(array(
    ln(101, '4140023', 'PLC CPU MODULE',      1),
    ln(102, '4010224', 'MAIN BASE UNIT, 12 SLOT', 1),
    ln(103, '2020123', '4 CH TEMPERATURE CARD', 7, array('part_no' => 'P-2020122')),
    ln(104, '4100058', 'CONVERTER MODULE',    1),
));
$d = $model->diff($base, $corrected);
ok('two detail corrections', $d['counts']['detail'], 2);
$labels = array();
foreach ($d['detail'] as $x) { $labels[] = $x['label']; }
sort($labels);
ok('  a description and an ERP code', $labels, array('Description', 'ERP code'));
ok('  neither is mistaken for a new part', $d['counts']['added'], 0);

echo "\nREMARKS\n";
$rem = ver(array(
    ln(101, '4140023', 'PLC CPU MODULE', 1, array('user_remark' => 'customer wants 24V')),
    ln(102, '4010224', 'MAIN BASE UNIT', 1),
    ln(103, '2020122', '4 CH TEMPERATURE CARD', 7),
    ln(104, '4100058', 'CONVERTER MODULE', 1),
));
$d = $model->diff($base, $rem);
ok('one remark change', $d['counts']['remark'], 1);
ok('  from blank', $d['remark'][0]['was'], '');
ok('  to the typed text', $d['remark'][0]['now'], 'customer wants 24V');
ok('clearing a remark is also reported',
   $model->diff($rem, $base)['counts']['remark'], 1);

echo "\nHAND-ADDED ROWS — no item_id, so identity is part + description\n";
$m1 = ver(array_merge($base['lines'], array(
    ln(null, '', 'CUSTOMER SUPPLIED ISOLATOR', 2,
       array('is_manual_add' => 1, 'part_no' => 'CS-ISO-01')),
)));
$d = $model->diff($base, $m1);
ok('a hand-added row registers as added', $d['counts']['added'], 1);
ok('  and is marked as hand-added', (int) $d['added'][0]['is_manual_add'], 1);
ok('the same row next revision is NOT re-reported',
   $model->diff_is_empty($model->diff($m1, $m1)), true);
ok('removing it is reported', $model->diff($m1, $base)['counts']['removed'], 1);

// Two hand-added rows with the same part number but different text must
// not collapse into one.
$m2 = ver(array_merge($base['lines'], array(
    ln(null, '', 'CUSTOMER SUPPLIED ISOLATOR', 2, array('is_manual_add' => 1, 'part_no' => 'CS-ISO-01')),
    ln(null, '', 'CUSTOMER SUPPLIED ISOLATOR, SPARE', 1, array('is_manual_add' => 1, 'part_no' => 'CS-ISO-01')),
)));
ok('two similar hand-added rows stay distinct',
   $model->diff($base, $m2)['counts']['added'], 2);

echo "\nMACHINE CONFIGURATION\n";
$cfg = ver($base['lines'], array('tracks' => 6, 'speed_ppm' => 120));
$d = $model->diff($base, $cfg);
ok('two header changes', $d['counts']['header'], 2);
$by = array();
foreach ($d['header'] as $h) { $by[$h['field']] = $h; }
ok('  tracks 12 -> 6', $by['tracks']['was'] . '->' . $by['tracks']['now'], '12->6');
ok('  speed 100 -> 120', $by['speed_ppm']['was'] . '->' . $by['speed_ppm']['now'], '100->120');

echo "\n  ids and JSON are rendered as words, not as stored values\n";
$sw = ver($base['lines'], array('variant_id' => 8, 'plc_family_id' => 2));
$d  = $model->diff($base, $sw);
$by = array();
foreach ($d['header'] as $h) { $by[$h['field']] = $h; }
ok('build variant shows codes', $by['variant_id']['was'] . ' -> ' . $by['variant_id']['now'],
   'FX5-1808 -> FX5-1723');
ok('PLC family shows codes', $by['plc_family_id']['was'] . ' -> ' . $by['plc_family_id']['now'],
   'FX5 -> iQ-R');

$feat = ver($base['lines'], array('features_json' => '{"feat_perf":0,"feat_brake":1}'));
$d = $model->diff($base, $feat);
$got = $d['header'][0];
ok('features show labels, not JSON',
   strpos($got['now'], '{') === false && strpos($got['now'], 'feat_') === false, true);
ok('  and an empty set reads as "none"',
   $model->diff($base, ver($base['lines'],
       array('features_json' => '{"feat_perf":0}')))['header'][0]['now'], 'none');

echo "\n  a field that did not move is never reported\n";
ok('changing tracks alone reports only tracks',
   count($model->diff($base, ver($base['lines'], array('tracks' => 9)))['header']), 1);
ok('timestamps are not in the reported set',
   count($model->diff($base, ver($base['lines'],
       array('updated_at' => '2026-01-01 00:00:00')))['header']), 0);

echo "\nEVERYTHING AT ONCE\n";
$all = ver(array(
    ln(101, '4140023', 'PLC CPU MODULE', 1, array('user_remark' => 'checked')),
    ln(103, '2020122', '4 CH TEMPERATURE CARD', 4),
    ln(104, '4100059', 'CONVERTER MODULE', 1, array('part_no' => 'P-4100058')),
    ln(200, '5000001', 'EXTRA I/O CARD', 3),
), array('tracks' => 6));
$d = $model->diff($base, $all);
ok('removed the base unit',   $d['counts']['removed'], 1);
ok('added the I/O card',      $d['counts']['added'], 1);
ok('one quantity moved',      $d['counts']['qty'], 1);
ok('one remark typed',        $d['counts']['remark'], 1);
ok('one ERP corrected',       $d['counts']['detail'], 1);
ok('one config field moved',  $d['counts']['header'], 1);
ok('and it does not read as empty', $model->diff_is_empty($d), false);

echo "\n" . str_repeat('-', 80) . "\n";
echo $fail === 0
    ? "\033[32m\033[1m  ALL {$pass} ASSERTIONS PASSED\033[0m\n"
    : "\033[31m\033[1m  {$fail} FAILED, {$pass} passed\033[0m\n";
echo str_repeat('-', 80) . "\n\n";

exit($fail === 0 ? 0 : 1);
