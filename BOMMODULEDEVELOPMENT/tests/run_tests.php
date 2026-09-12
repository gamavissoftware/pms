<?php
/**
 * run_tests.php — Automation BOM engine, spec section 5.1 suite
 *
 * Standalone. No CodeIgniter bootstrap, no database, no system/ needed.
 * Loads the REAL application/libraries/Abom_engine.php and drives it with
 * master data parsed straight out of the SHIPPED seed file,
 * Database/abom_006_seed.sql — the same bytes MySQL will be given.
 *
 *   php BOMMODULEDEVELOPMENT/tests/run_tests.php
 *
 * Exit code 0 = all passed, 1 = at least one failure.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

require __DIR__ . '/seed_parser.php';
require __DIR__ . '/stubs.php';

// Abom_engine guards on BASEPATH, as every CI file does.
defined('BASEPATH') OR define('BASEPATH', true);

// Both seed files, as a live install has run them: 006 lays down the
// nine original reference builds, 009 adds the two SPM1250P machines.
// Concatenated rather than parsed separately so that a row added by 009
// which contradicts 006 shows up here rather than on the server.
$combined = tempnam(sys_get_temp_dir(), 'abom');
file_put_contents(
    $combined,
    file_get_contents($root . '/Database/abom_006_seed.sql') . "\n" .
    file_get_contents($root . '/Database/abom_009_seed_1250p.sql') . "\n" .
    file_get_contents($root . '/Database/abom_011_split_fx5je.sql')
);

$seed = new Abom_seed_parser($combined);
register_shutdown_function(function () use ($combined) { @unlink($combined); });
$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);

require $root . '/application/libraries/Abom_engine.php';

// =====================================================================
//  Tiny assertion harness
// =====================================================================

$RESULTS = array('pass' => 0, 'fail' => 0, 'failures' => array());

function check($label, $expected, $actual)
{
    global $RESULTS;

    $ok = ($expected === $actual);

    if ($ok) {
        $RESULTS['pass']++;
        printf("  \033[32mPASS\033[0m  %-46s expected %s\n", $label, fmt($expected));
    } else {
        $RESULTS['fail']++;
        $RESULTS['failures'][] = $label;
        printf(
            "  \033[31mFAIL\033[0m  %-46s expected %s, got %s\n",
            $label,
            fmt($expected),
            fmt($actual)
        );
    }

    return $ok;
}

function fmt($v)
{
    if (is_bool($v))  return $v ? 'true' : 'false';
    if (is_null($v))  return 'null';
    if (is_array($v)) return '[' . implode(', ', array_map('fmt', $v)) . ']';
    if (is_string($v)) return "'" . $v . "'";
    return (string) $v;
}

function heading($text)
{
    echo "\n\033[1m" . $text . "\033[0m\n";
}

/** Default configuration; individual tests override what they exercise. */
function cfg(array $over = array())
{
    $base = array(
        'axes'          => 8,
        'tracks'        => 12,
        'speed_ppm'     => 140,
        'motion_type'   => 'Intermittent',
        'machine_model' => 'SPM1200L',
        'machine_side'  => 'LHS',
        'j4_units'      => 7,
        'battery_qty'   => 7,
        'plc_family_id' => null,
        'variant_id'    => null,
        'features'      => array(
            'feat_perf'          => 1,
            'feat_brake'         => 1,
            'feat_dbr'           => 1,
            'feat_imark'         => 1,
            'feat_autonics_temp' => 0,
            'feat_io32'          => 0,
        ),
    );

    return array_merge($base, $over);
}

/** First generated line whose part number matches. */
function line_by_part(array $lines, $part_no)
{
    foreach ($lines as $line) {
        if ($line->part_no === $part_no) {
            return $line;
        }
    }
    return null;
}

function part_numbers(array $lines)
{
    $out = array();
    foreach ($lines as $line) {
        $out[] = $line->part_no;
    }
    return $out;
}

$engine = new Abom_engine();
$items  = $GLOBALS['ABOM_STUB_CI']->Abom_item_model;

echo "\n\033[1m╔══════════════════════════════════════════════════════════════════════╗\033[0m";
echo "\n\033[1m║  Automation BOM — Abom_engine unit tests (spec section 5.1)          ║\033[0m";
echo "\n\033[1m╚══════════════════════════════════════════════════════════════════════╝\033[0m\n";
echo "  PHP " . PHP_VERSION . "   seed: abom_006 + abom_009 + abom_011\n";

// =====================================================================
//  0. Seed integrity — spec section 9, build order step 1
// =====================================================================

heading('0. Seed integrity (spec section 9 step 1 / Appendix A)');

$all_items = $items->all();
$by_family = array(1 => 0, 2 => 0);
$by_variant = array();
$by_formula = array();
$by_severity = array();
$by_feature = array();
$erp_parts = array();

foreach ($all_items as $item) {
    $by_family[(int) $item->plc_family_id]++;
    $v = (int) $item->variant_id;
    $by_variant[$v] = isset($by_variant[$v]) ? $by_variant[$v] + 1 : 1;
    $f = $item->formula_code;
    $by_formula[$f] = isset($by_formula[$f]) ? $by_formula[$f] + 1 : 1;
    $s = $item->issue_severity;
    $by_severity[$s] = isset($by_severity[$s]) ? $by_severity[$s] + 1 : 1;
    if (!empty($item->feature_code)) {
        $c = $item->feature_code;
        $by_feature[$c] = isset($by_feature[$c]) ? $by_feature[$c] + 1 : 1;
    }
    if ($item->erp_code !== null && $item->erp_code !== '') {
        $erp_parts[$item->erp_code][$item->part_no] = true;
    }
}

$master = $GLOBALS['ABOM_STUB_CI']->Abom_master_model;

check('abom_item rows',               380, count($all_items));
check('  family 1 (FX5)',             222, $by_family[1]);
check('  family 2 (iQ-R)',            158, $by_family[2]);
check('  variant 1 FX5-JE',            31, isset($by_variant[1]) ? $by_variant[1] : 0);
check('  variant 2 FX5-J4',            29, isset($by_variant[2]) ? $by_variant[2] : 0);
check('  variant 3 IQR-STD',           34, isset($by_variant[3]) ? $by_variant[3] : 0);
check('  variant 4 IQR-HS',            42, isset($by_variant[4]) ? $by_variant[4] : 0);
check('  variant 5 IQR-FLM',           42, isset($by_variant[5]) ? $by_variant[5] : 0);
check('  variant 6 IQR-TCF',           40, isset($by_variant[6]) ? $by_variant[6] : 0);
check('  variant 7 FX5-TCF',           31, isset($by_variant[7]) ? $by_variant[7] : 0);
check('  variant 8 FX5-1723',          25, isset($by_variant[8]) ? $by_variant[8] : 0);
check('  variant 9 FX5-1778',          29, isset($by_variant[9]) ? $by_variant[9] : 0);
check('  variant 10 FX5-1808',         29, isset($by_variant[10]) ? $by_variant[10] : 0);
check('  variant 11 FX5-1858',         20, isset($by_variant[11]) ? $by_variant[11] : 0);
check('  variant 12 FX5-1864',         28, isset($by_variant[12]) ? $by_variant[12] : 0);
// FX5-JE is retired, not deleted: saved BOM lines point at its item ids.
check('  variant 1 FX5-JE retired',  false, (bool) $master->get_variant(1)->is_active);
check('  every item has a variant', false, isset($by_variant[0]));
check('abom_section rows',             10, $master->count_sections());
check('abom_plc_rule rows (active)',    5, $master->count_rules());
check('abom_variant rows (active)',    11, $master->count_variants());
check('abom_variant_rule rows',        13, count($master->get_active_variant_rules()));
check('abom_feature rows',              6, $master->count_features());
check('abom_formula rows',              8, $master->count_formulas());
check('formula FIXED',                157, isset($by_formula['FIXED']) ? $by_formula['FIXED'] : 0);
check('formula MANUAL',               184, isset($by_formula['MANUAL']) ? $by_formula['MANUAL'] : 0);
check('formula TRACK_TEMP',            13, isset($by_formula['TRACK_TEMP']) ? $by_formula['TRACK_TEMP'] : 0);
check('formula TRACKS',                 2, isset($by_formula['TRACKS']) ? $by_formula['TRACKS'] : 0);
check('formula J4_STO',                 5, isset($by_formula['J4_STO']) ? $by_formula['J4_STO'] : 0);
check('formula BATTERY',               12, isset($by_formula['BATTERY']) ? $by_formula['BATTERY'] : 0);
check('formula AXES_MINUS_1',           7, isset($by_formula['AXES_MINUS_1']) ? $by_formula['AXES_MINUS_1'] : 0);
check('severity none',                163, isset($by_severity['none']) ? $by_severity['none'] : 0);
check('severity review',              182, isset($by_severity['review']) ? $by_severity['review'] : 0);
check('severity no_erp',               15, isset($by_severity['no_erp']) ? $by_severity['no_erp'] : 0);
check('severity conflict',             20, isset($by_severity['conflict']) ? $by_severity['conflict'] : 0);
check('gated feat_perf',               19, isset($by_feature['feat_perf']) ? $by_feature['feat_perf'] : 0);
check('gated feat_brake',               3, isset($by_feature['feat_brake']) ? $by_feature['feat_brake'] : 0);
check('gated feat_dbr',                 9, isset($by_feature['feat_dbr']) ? $by_feature['feat_dbr'] : 0);
check('gated feat_imark',               4, isset($by_feature['feat_imark']) ? $by_feature['feat_imark'] : 0);
check('gated feat_autonics_temp',       1, isset($by_feature['feat_autonics_temp']) ? $by_feature['feat_autonics_temp'] : 0);
check('gated feat_io32',                1, isset($by_feature['feat_io32']) ? $by_feature['feat_io32'] : 0);

// One ERP code on two DIFFERENT parts is the defect. The same part
// appearing once per variant that fits it is not, and must not be
// counted as one — five variants legitimately carry VFD 2040140.
$conflict_erps = array();
foreach ($erp_parts as $erp => $parts) {
    if (count($parts) > 1) {
        $conflict_erps[] = (string) $erp;   // PHP intifies numeric keys
    }
}
sort($conflict_erps);
// Nine now. The five added by DF-1883 are all of one kind: that sheet
// reuses FX5-era codes for MR-J4 parts, and its worksheet tab is named
// "Old BOM" — a BOM re-parted without re-coding. Seeded verbatim and
// flagged rather than silently corrected; that is an engineering call.
check('ERP codes on two different parts',
      array('2030449', '2030505', '2110163', '4030012', '4060431',
            '4060525', '4060620', '4120007', '4120009'), $conflict_erps);
check('items with no ERP code', 15,
      count(array_filter($all_items, function ($i) { return $i->erp_code === null; })));

// =====================================================================
//  PLC family detection
// =====================================================================

heading('1-6. PLC family detection (spec 2.1)');

$t = $engine->detect_plc_family(cfg(array('axes' => 8, 'speed_ppm' => 140, 'motion_type' => 'Intermittent')));
check('FX5 baseline — family',            'FX5', $t['code']);
check('FX5 baseline — rule priority',          99, $t['priority']);

$t = $engine->detect_plc_family(cfg(array('axes' => 10, 'speed_ppm' => 100, 'motion_type' => 'Intermittent')));
check('iQ-R by axis count — family',     'iQ-R', $t['code']);
check('iQ-R by axis count — rule id',          1, $t['rule_id']);

$t = $engine->detect_plc_family(cfg(array('axes' => 6, 'speed_ppm' => 160, 'motion_type' => 'Intermittent')));
check('iQ-R by speed — family',          'iQ-R', $t['code']);
check('iQ-R by speed — rule id',               2, $t['rule_id']);

// Rule 3 is the continuous-motion rule, but rule 1 (axes >= 10) matches
// first at 12 axes. The spec says assert the family, not the rule.
$t = $engine->detect_plc_family(cfg(array('axes' => 12, 'speed_ppm' => 120, 'motion_type' => 'Continuous')));
check('iQ-R by continuous motion — family', 'iQ-R', $t['code']);

$t = $engine->detect_plc_family(cfg(array('axes' => 9, 'speed_ppm' => 140, 'motion_type' => 'Intermittent')));
check('Boundary 9 axes — family',         'FX5', $t['code']);

$t = $engine->detect_plc_family(cfg(array('axes' => 8, 'speed_ppm' => 159, 'motion_type' => 'Intermittent')));
check('Boundary 159 PPM — family',        'FX5', $t['code']);

// =====================================================================
//  Build variant detection
// =====================================================================

heading('6b. Build variant detection');

// The family says which CPU; the variant says which machine. Two FX5
// builds carry entirely different servo ranges, so getting this wrong is
// how procurement ends up ordering two sets of drives.
$v = $engine->detect_variant(cfg(array('axes' => 5, 'tracks' => 6,  'speed_ppm' => 70)),  1);
check('FX5 5 axis 70 PPM -> FX5-1858', 'FX5-1858', $v['code']);
$v = $engine->detect_variant(cfg(array('axes' => 7, 'tracks' => 8,  'speed_ppm' => 80)),  1);
check('FX5 7 axis       -> FX5-1778', 'FX5-1778', $v['code']);
$v = $engine->detect_variant(cfg(array('axes' => 8, 'tracks' => 12, 'speed_ppm' => 140)), 1);
check('FX5 8 axis  -> FX5-J4',      'FX5-J4',  $v['code']);
$v = $engine->detect_variant(cfg(array('axes' => 6, 'tracks' => 12, 'speed_ppm' => 120)), 1);
check('FX5 120 PPM -> FX5-J4',      'FX5-J4',  $v['code']);
$v = $engine->detect_variant(cfg(array('axes' => 7, 'tracks' => 12, 'speed_ppm' => 119)), 1);
check('  boundary 119 PPM -> FX5-1778', 'FX5-1778', $v['code']);

$v = $engine->detect_variant(cfg(array('axes' => 10, 'tracks' => 12, 'speed_ppm' => 140)), 2);
check('iQ-R 10 axis -> IQR-STD',    'IQR-STD', $v['code']);
$v = $engine->detect_variant(cfg(array('axes' => 15, 'tracks' => 12, 'speed_ppm' => 180,
                                       'motion_type' => 'Continuous')), 2);
check('iQ-R 180 PPM -> IQR-HS',     'IQR-HS',  $v['code']);
$v = $engine->detect_variant(cfg(array('axes' => 13, 'tracks' => 12, 'speed_ppm' => 100)), 2);
check('iQ-R 13 axis -> IQR-HS',     'IQR-HS',  $v['code']);
$v = $engine->detect_variant(cfg(array('axes' => 12, 'tracks' => 12, 'speed_ppm' => 100)), 2);
check('  boundary 12 axis -> IQR-STD', 'IQR-STD', $v['code']);

// Model beats everything: the SPM1250P is a different machine.
$v = $engine->detect_variant(cfg(array('axes' => 15, 'tracks' => 9, 'speed_ppm' => 80,
                                       'motion_type' => 'Continuous',
                                       'machine_model' => 'SPM1250P')), 2);
check('SPM1250P -> IQR-FLM',        'IQR-FLM', $v['code']);

// The two FX5 variants must not share parts they should not share.
$je = $engine->generate(cfg(array('axes' => 6, 'tracks' => 12, 'speed_ppm' => 80,  'battery_qty' => 6)));
$j4 = $engine->generate(cfg(array('axes' => 8, 'tracks' => 12, 'speed_ppm' => 140, 'j4_units' => 7, 'battery_qty' => 7)));
check('  6 axis FX5 has MR-JE-300B',  true,  in_array('MR-JE-300B', part_numbers($je['lines']), true));
check('  6 axis FX5 has NO MR-J4-350B', false, in_array('MR-J4-350B', part_numbers($je['lines']), true));
check('  8 axis FX5 has MR-J4-350B',  true,  in_array('MR-J4-350B', part_numbers($j4['lines']), true));
check('  8 axis FX5 has NO MR-JE-300B', false, in_array('MR-JE-300B', part_numbers($j4['lines']), true));

// An engineer's manual variant override must be honoured AND flagged.
$forced = $engine->generate(cfg(array('axes' => 6, 'tracks' => 12, 'speed_ppm' => 80, 'variant_id' => 2)));
check('override -> forced variant',  'FX5-J4', $forced['variant']->code);
check('override is flagged',              true, (bool) $forced['variant_overridden']);
check('no override -> not flagged',      false, (bool) $je['variant_overridden']);

// No variant means NO LINES. Falling back to the whole family would put
// two incompatible servo ranges on one purchasable document.
//
// A SIX-axis SPM1250P matches nothing: the FX5 rules are scoped to the
// SPM1200L, and the two SPM1250P rules are bounded to 9-12 axes. No
// reference BOM describes such a machine, so the module must not invent
// one by lending it the eleven-axis tilting-cup item list.
$orphan = $engine->generate(cfg(array('machine_model' => 'SPM1250P', 'axes' => 6,
                                      'tracks' => 12, 'speed_ppm' => 80)));
check('unmatched model -> no variant',    true, (bool) $orphan['variant_missing']);
check('unmatched model -> zero lines',       0, count($orphan['lines']));
check('  and no variant object',          null, $orphan['variant']);

// =====================================================================
//  Track-driven temperature card rule
// =====================================================================

heading('6c. TRACK_TEMP — (((tracks + 1) * 2) + 2) / 4, rounded up');

// Every source BOM that lists a 4-channel temperature or RTD card, run
// through the engine. These are the numbers on the released sheets.
$temp_cases = array(
    // label,            cfg override,                                        expected
    array('DF-1858  6 track', array('axes' => 5,  'tracks' => 6,  'speed_ppm' => 70),  4),
    // DF-1778 carries a supplementary AUTONICS card ALONGSIDE the five
    // track-driven Mitsubishi ones. Only one item per build may be
    // TRACK_TEMP or the card count doubles — abom_011 demotes the
    // AUTONICS one to a fixed quantity of 1, so the rule still answers 5.
    array('DF-1778  8 track', array('axes' => 7,  'tracks' => 8,  'speed_ppm' => 80),  5),
    array('DF-1808 12 track', array('axes' => 6,  'tracks' => 12, 'speed_ppm' => 100), 7),
    array('DF-1827 12 track', array('axes' => 8,  'tracks' => 12, 'speed_ppm' => 140), 7),
    array('DF-1770 12 track', array('axes' => 10, 'tracks' => 12, 'speed_ppm' => 140), 7),
    array('DF-1826 12 track', array('axes' => 15, 'tracks' => 12, 'speed_ppm' => 180,
                                    'motion_type' => 'Continuous'),                    7),
    array('DF-1855  9 track', array('axes' => 15, 'tracks' => 9,  'speed_ppm' => 80,
                                    'motion_type' => 'Continuous',
                                    'machine_model' => 'SPM1250P'),                    6),
);

foreach ($temp_cases as $case) {
    list($label, $over, $expected) = $case;
    $r = $engine->generate(cfg($over));

    $qty = 0;
    foreach ($r['lines'] as $line) {
        if ($line->formula_code === 'TRACK_TEMP') {
            $qty += (int) $line->qty;
        }
    }
    check($label, $expected, $qty);
}

// The rounding is UP, and it is the half-card case that proves it:
// 9 tracks gives 5.5 and half a card cannot be bought.
$card = (object) array('base_qty' => 7, 'formula_code' => 'TRACK_TEMP');
check('  7 tracks -> 4.5 rounds to 5',  5, $engine->calc_qty($card, array('tracks' => 7)));
check('  9 tracks -> 5.5 rounds to 6',  6, $engine->calc_qty($card, array('tracks' => 9)));
check('  11 tracks -> 6.5 rounds to 7', 7, $engine->calc_qty($card, array('tracks' => 11)));
check('  1 track  -> 2',                2, $engine->calc_qty($card, array('tracks' => 1)));
check('  0 tracks -> 0, not the base',  0, $engine->calc_qty($card, array('tracks' => 0)));

// TRACK_TEMP must ignore the base quantity entirely — the reference
// number is kept on the row for provenance, not as a fallback.
check('  base_qty is not used',          7, $engine->calc_qty($card, array('tracks' => 12)));

// TRACKS: one unit per track (the SPM1250P cut-off drives).
$drive = (object) array('base_qty' => 9, 'formula_code' => 'TRACKS');
check('TRACKS at 9 tracks',              9, $engine->calc_qty($drive, array('tracks' => 9)));
check('TRACKS at 12 tracks',            12, $engine->calc_qty($drive, array('tracks' => 12)));

$flm = $engine->generate(cfg(array('axes' => 15, 'tracks' => 9, 'speed_ppm' => 80,
                                   'motion_type' => 'Continuous',
                                   'machine_model' => 'SPM1250P', 'j4_units' => 15,
                                   'battery_qty' => 15)));
$cut = line_by_part($flm['lines'], 'MR-J4-40B');
check('  SPM1250P cut-off drives at 9T', 9, $cut ? (int) $cut->qty : -1);

$flm12 = $engine->generate(cfg(array('axes' => 15, 'tracks' => 12, 'speed_ppm' => 80,
                                     'motion_type' => 'Continuous',
                                     'machine_model' => 'SPM1250P', 'j4_units' => 15,
                                     'battery_qty' => 15)));
$cut12 = line_by_part($flm12['lines'], 'MR-J4-40B');
check('  and 12 at 12 tracks',          12, $cut12 ? (int) $cut12->qty : -1);

// =====================================================================
//  Quantity formulas
// =====================================================================

heading('7-9. Quantity formulas (spec 2.2)');

$r = $engine->generate(cfg(array('axes' => 8)));
$sscnet = line_by_part($r['lines'], 'MR-J3BUS05M');
check('AXES_MINUS_1 at 8 axes',                7, $sscnet ? (int) $sscnet->qty : -1);

$r = $engine->generate(cfg(array('axes' => 1)));
$sscnet = line_by_part($r['lines'], 'MR-J3BUS05M');
check('AXES_MINUS_1 floor at 1 axis',          1, $sscnet ? (int) $sscnet->qty : -1);

// J4_STO and BATTERY are separate inputs and must never be derived from
// one number — DF-1826 shows 11 MR-J4 units but 12 battery sets.
$r = $engine->generate(cfg(array(
    'axes' => 15, 'speed_ppm' => 180, 'motion_type' => 'Continuous',
    'j4_units' => 11, 'battery_qty' => 12,
)));
$sto = line_by_part($r['lines'], 'MR-D05ULD3M-B');
$bat = line_by_part($r['lines'], 'MR-BAT6V1SET');
check('J4_STO line qty',                      11, $sto ? (int) $sto->qty : -1);
check('BATTERY line qty',                     12, $bat ? (int) $bat->qty : -1);
check('J4_STO and BATTERY independent',     true, ($sto && $bat && $sto->qty !== $bat->qty));

// =====================================================================
//  Feature gating
// =====================================================================

heading('10. Feature gating (spec 2.4)');

$full = $engine->generate(cfg());
$gated = $engine->generate(cfg(array(
    'features' => array('feat_perf' => 0, 'feat_brake' => 1, 'feat_dbr' => 1, 'feat_imark' => 1),
)));

check('FX5 lines with feat_perf ON',          29, count($full['lines']));
check('FX5 lines with feat_perf OFF',         25, count($gated['lines']));
check('  difference is exactly 4',             4, count($full['lines']) - count($gated['lines']));

$perf_parts = array('MR-JE-70B', 'HG-KN73JK', 'MR-J3ENCBL5M-A2-L', 'MR-PWS1CBL03M-A2-L');
$remaining  = part_numbers($gated['lines']);
foreach ($perf_parts as $part) {
    check('  ' . $part . ' removed entirely', false, in_array($part, $remaining, true));
}

// Line numbers must stay contiguous after a gate-out — no gaps.
$expected_no = 0;
$contiguous  = true;
foreach ($gated['lines'] as $line) {
    $expected_no++;
    if ((int) $line->line_no !== $expected_no) {
        $contiguous = false;
        break;
    }
}
check('  line numbering stays contiguous',  true, $contiguous);

// =====================================================================
//  Full generation — THE REGRESSION GUARD
// =====================================================================

heading('11-12. Full generation — regression guard (spec Appendix B)');

$fx5 = $engine->generate(cfg(array(
    'axes' => 8, 'tracks' => 12, 'speed_ppm' => 140, 'motion_type' => 'Intermittent',
    'machine_side' => 'LHS', 'j4_units' => 7, 'battery_qty' => 7,
)));
check('Full FX5 generation — family',      'FX5', $fx5['family']['code']);
check('Full FX5 generation — LINES',           29, count($fx5['lines']));

$iqr = $engine->generate(cfg(array(
    'axes' => 15, 'tracks' => 12, 'speed_ppm' => 180, 'motion_type' => 'Continuous',
    'machine_side' => 'N/A', 'j4_units' => 11, 'battery_qty' => 12,
)));
check('Full iQ-R generation — family',    'iQ-R', $iqr['family']['code']);
check('Full iQ-R generation — LINES',          42, count($iqr['lines']));

// =====================================================================
//  The two SPM1250P tilting-cup builds (abom_009)
// =====================================================================

heading('12d. SPM1250P — DF-1805 and DF-1883, and the nine before them');

// Same model, same axis count, same track count — DIFFERENT FAMILIES.
// DF-1883 runs 11 axes on FX5 by fitting a second simple-motion card;
// DF-1805 at the same axis count is iQ-R because it runs continuously at
// 120 PPM. Nothing but the model + axis band + motion type separates
// them, which is exactly what the new rules test.
$tcf_fx5 = $engine->generate(cfg(array(
    'axes' => 11, 'tracks' => 6, 'speed_ppm' => 70, 'motion_type' => 'Intermittent',
    'machine_model' => 'SPM1250P', 'j4_units' => 11, 'battery_qty' => 11,
)));
check('DF-1883 — family',                 'FX5', $tcf_fx5['family']['code']);
check('DF-1883 — build',               'FX5-TCF', $tcf_fx5['variant']->code);
check('DF-1883 — lines',                      31, count($tcf_fx5['lines']));
check('  and it carries the 4-axis card',   true,
      in_array('FX5-40SSC-S', part_numbers($tcf_fx5['lines']), true));

$tcf_iqr = $engine->generate(cfg(array(
    'axes' => 11, 'tracks' => 6, 'speed_ppm' => 120, 'motion_type' => 'Continuous',
    'machine_model' => 'SPM1250P', 'j4_units' => 13, 'battery_qty' => 13,
)));
check('DF-1805 — family',                'iQ-R', $tcf_iqr['family']['code']);
check('DF-1805 — build',              'IQR-TCF', $tcf_iqr['variant']->code);
check('DF-1805 — lines',                     40, count($tcf_iqr['lines']));

// 6 tracks -> 4 cards on both, by the same rule as everything else.
foreach (array('DF-1883' => $tcf_fx5, 'DF-1805' => $tcf_iqr) as $df => $res) {
    $tc = 0;
    foreach ($res['lines'] as $line) {
        if ($line->formula_code === 'TRACK_TEMP') { $tc += (int) $line->qty; }
    }
    check('  ' . $df . ' temperature cards at 6 track', 4, $tc);
}

// THE POINT OF THIS BLOCK. Adding two builds must not move any of the
// nine that were already there — same family, same build, same count.
$before = array(
    // df        axes tracks speed  motion          model        family   build      lines
    array('DF-1858',  5,  6,  70, 'Intermittent', 'SPM1200L', 'FX5',  'FX5-1858', 20),
    array('DF-1723',  5, 12, 100, 'Intermittent', 'SPM1200L', 'FX5',  'FX5-1723', 25),
    array('DF-1808',  6, 12, 100, 'Intermittent', 'SPM1200L', 'FX5',  'FX5-1808', 29),
    array('DF-1864',  6, 12,  80, 'Intermittent', 'SPM1200L', 'FX5',  'FX5-1864', 28),
    array('DF-1778',  7,  8,  80, 'Intermittent', 'SPM1200L', 'FX5',  'FX5-1778', 29),
    array('DF-1827',  8, 12, 140, 'Intermittent', 'SPM1200L', 'FX5',  'FX5-J4',  29),
    array('DF-1770', 10, 12, 140, 'Intermittent', 'SPM1200L', 'iQ-R', 'IQR-STD', 34),
    array('DF-1826', 15, 12, 180, 'Continuous',   'SPM1200L', 'iQ-R', 'IQR-HS',  42),
    array('DF-1855', 15,  9,  80, 'Continuous',   'SPM1250P', 'iQ-R', 'IQR-FLM', 42),
);

foreach ($before as $row) {
    list($df, $ax, $tr, $sp, $mo, $model, $fam, $build, $lines) = $row;

    $r = $engine->generate(cfg(array(
        'axes' => $ax, 'tracks' => $tr, 'speed_ppm' => $sp,
        'motion_type' => $mo, 'machine_model' => $model,
    )));

    check($df . ' still ' . $build,  $build, $r['variant'] ? $r['variant']->code : '-');
    check('  ' . $df . ' still ' . $fam . ' / ' . $lines . ' lines',
          $fam . '/' . $lines, $r['family']['code'] . '/' . count($r['lines']));
}

// =====================================================================
//  Variant override must carry its family
// =====================================================================

heading('12c. A variant override moves the PLC family with it');

// ABOM-5, 2026-08-11: a 6-axis SPM1200L detected FX5, was overridden to
// IQR-STD, and the sheet then showed "FX5 Series" in the header with
// nothing but iQ-R parts under it. The variant decides which items are
// on the document, so the family label has to follow it.
$forced_iqr = $engine->generate(cfg(array(
    'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100, 'battery_qty' => 6,
    'variant_id' => 3,                       // IQR-STD, family 2
)));
check('override to IQR-STD — variant',   'IQR-STD', $forced_iqr['variant']->code);
check('  family follows the variant',            2, (int) $forced_iqr['family_id']);
check('  and is flagged as overridden',       true, (bool) $forced_iqr['overridden']);
check('  lines come from that build',           34, count($forced_iqr['lines']));

// Detection alone must still leave the family alone.
$natural = $engine->generate(cfg(array(
    'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100, 'battery_qty' => 6,
)));
check('no override — family untouched',          1, (int) $natural['family_id']);
check('  and not flagged',                   false, (bool) $natural['overridden']);

// An override WITHIN the same family is a build change, not a family one.
$same_family = $engine->generate(cfg(array(
    'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100, 'battery_qty' => 6,
    'variant_id' => 2,                       // FX5-J4, family 1
)));
check('override within FX5 — family stays',      1, (int) $same_family['family_id']);
check('  family not flagged',                false, (bool) $same_family['overridden']);
check('  but the build IS flagged',           true, (bool) $same_family['variant_overridden']);

// =====================================================================
//  Editable REMARKS and hand-added rows
// =====================================================================

heading('12b. Row edits — the engine\'s side of the contract');

// The engineer's remark is SEEDED from the master item's usage note and
// editable from there on. It was briefly left empty with usage_remark
// shown only as a grey placeholder; engineering overruled that on
// 2026-08-11 — the released DFs carry that text in this column, and
// retyping it per line to get it onto the print is friction, not a
// safeguard.
$missing   = 0;
$mismatch  = 0;
$with_text = 0;

foreach ($fx5['lines'] as $line) {
    if (!property_exists($line, 'user_remark')) { $missing++; continue; }

    // Seeded verbatim — never normalised, re-cased or abbreviated.
    if ($line->user_remark !== (string) $line->usage_remark) { $mismatch++; }
    if ($line->user_remark !== '')                           { $with_text++; }
}

check('every line carries user_remark',        0, $missing);
check('  seeded verbatim from usage_remark',   0, $mismatch);
check('  and some lines actually carry text', true, $with_text > 0);

// A line with no usage note starts blank rather than carrying a default
// nobody chose.
$blank = 0;
foreach ($fx5['lines'] as $line) {
    if (empty($line->usage_remark) && $line->user_remark === '') { $blank++; }
}
check('  no usage note -> blank remark',    true, $blank > 0);

// usage_remark itself must survive alongside it — it is the provenance
// of the seeded text, and the master-item screens edit it.
$with_usage = 0;
foreach ($fx5['lines'] as $line) {
    if (!empty($line->usage_remark)) { $with_usage++; }
}
check('usage_remark still on the lines',  true, $with_usage > 0);

// Nothing the engine produces is a manual row. Manual rows exist only
// where an engineer added one, and only on that BOM.
$manual = 0;
foreach ($fx5['lines'] as $line) {
    if (!empty($line->is_manual_add)) { $manual++; }
}
check('engine emits no manual rows',         0, $manual);

// =====================================================================
//  Row-class precedence
// =====================================================================

heading('13. Row-class precedence');

// Ids 124 and 131 both carry issue_severity = conflict AND formula
// MANUAL. Severity must outrank category.
$conflict_item = $items->get_item(124);
check('conflict + MANUAL -> is-conflict', 'is-conflict', $engine->row_class($conflict_item));
check('  (item is genuinely MANUAL)',        'MANUAL', $conflict_item->formula_code);

$noerp_item = $items->get_item(34);           // FX5-16EX/ES, no ERP code
check('no ERP code -> is-noerp',            'is-noerp', $engine->row_class($noerp_item));

$optional_item = $items->get_item(51);        // MR-JE-70B, optional, severity none
check('optional -> is-optional',         'is-optional', $engine->row_class($optional_item));

$manual_item = $items->get_item(48);          // HG-JR353, MANUAL, severity none
check('MANUAL only -> is-manual',          'is-manual', $engine->row_class($manual_item));

$clean_item = $items->get_item(1);            // FX5U-80MT/ESS, clean
check('clean line -> no modifier',                 '', $engine->row_class($clean_item));

// 'review' severity must NOT tint the row — it is a STATUS badge only.
// The 1HP VFD is MANUAL + review: it tints as MANUAL, not as review.
$review_item = $items->get_item(12);
check('review does NOT tint the row',      'is-manual', $engine->row_class($review_item));
check('  review still badges',
    array('REVIEW', 'MANUAL QTY'), $engine->status_badges($review_item));

// MR-D05ULD3M-B is J4_STO + review, so no category applies either.
$review_only = $items->get_item(45);
check('review + non-MANUAL -> no tint',            '', $engine->row_class($review_only));
check('  but still badges REVIEW',    array('REVIEW'), $engine->status_badges($review_only));

// A TRACK_TEMP line is computed, not manual — it must NOT badge
// MANUAL QTY, or the whole point of giving it a formula is lost.
$temp_item = $items->get_item(5);             // FX5-4LC, TRACK_TEMP + review
check('TRACK_TEMP does not tint the row',          '', $engine->row_class($temp_item));
check('  and badges REVIEW only',     array('REVIEW'), $engine->status_badges($temp_item));

$temp_clean = $items->get_item(38);           // FX5-4LC on DF-1827, severity none
check('clean TRACK_TEMP badges nothing',     array(), $engine->status_badges($temp_clean));

// Every flag still reaches the STATUS column even when the row class
// can only show one of them.
$badges = $engine->status_badges($conflict_item);
check('conflict line badges',   array('CONFLICT', 'MANUAL QTY'), $badges);

// --lyellow belongs to the QTY cell, never the row.
$sample = $fx5['lines'][0];
check('qty cell clean by default',                 '', $engine->qty_cell_class($sample));
$sample->is_overridden = 1;
check('qty cell when overridden',        'qty-edited', $engine->qty_cell_class($sample));
$sample->is_overridden = 0;

// Row-class distribution across the whole master set — this is the
// check that keeps the sheet from drifting back to 41% tinted.
$dist = array('is-conflict' => 0, 'is-noerp' => 0, 'is-optional' => 0, 'is-manual' => 0, '' => 0);
foreach ($items->all() as $it) {
    $dist[$engine->row_class($it)]++;
}
check('distribution — is-conflict',  20, $dist['is-conflict']);
check('distribution — is-noerp',     15, $dist['is-noerp']);
check('distribution — is-optional',  33, $dist['is-optional']);
check('distribution — is-manual',   137, $dist['is-manual']);
check('distribution — untinted',    175, $dist['']);
check('distribution — sums to 380', 380, array_sum($dist));

// =====================================================================
//  Summary
// =====================================================================

$total = $RESULTS['pass'] + $RESULTS['fail'];

echo "\n" . str_repeat('─', 72) . "\n";
if ($RESULTS['fail'] === 0) {
    printf("\033[32m\033[1m  ALL %d ASSERTIONS PASSED\033[0m\n", $total);
} else {
    printf("\033[31m\033[1m  %d of %d FAILED\033[0m\n", $RESULTS['fail'], $total);
    foreach ($RESULTS['failures'] as $f) {
        echo "    - " . $f . "\n";
    }
}
printf("  FX5  full generation: %d lines   (spec Appendix B: 29)\n", count($fx5['lines']));
printf("  iQ-R full generation: %d lines   (spec Appendix B: 42)\n", count($iqr['lines']));
echo str_repeat('─', 72) . "\n\n";

exit($RESULTS['fail'] === 0 ? 0 : 1);
