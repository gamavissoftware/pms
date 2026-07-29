<?php
/**
 * run_tests.php — Automation BOM engine, spec section 5.1 suite
 *
 * Standalone. No CodeIgniter bootstrap, no database, no system/ needed.
 * Loads the REAL application/libraries/Bom_engine.php and drives it with
 * master data parsed straight out of abom_seed.sql.
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

// Bom_engine guards on BASEPATH, as every CI file does.
defined('BASEPATH') OR define('BASEPATH', true);

$seed = new Abom_seed_parser($root . '/BOMMODULEDEVELOPMENT/abom_seed.sql');
$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);

require $root . '/application/libraries/Bom_engine.php';

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
        'features'      => array(
            'feat_perf'  => 1,
            'feat_brake' => 1,
            'feat_dbr'   => 1,
            'feat_imark' => 1,
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

$engine = new Bom_engine();
$items  = $GLOBALS['ABOM_STUB_CI']->Bom_item_model;

echo "\n\033[1m╔══════════════════════════════════════════════════════════════════════╗\033[0m";
echo "\n\033[1m║  Automation BOM — Bom_engine unit tests (spec section 5.1)          ║\033[0m";
echo "\n\033[1m╚══════════════════════════════════════════════════════════════════════╝\033[0m\n";
echo "  PHP " . PHP_VERSION . "   seed: BOMMODULEDEVELOPMENT/abom_seed.sql\n";

// =====================================================================
//  0. Seed integrity — spec section 9, build order step 1
// =====================================================================

heading('0. Seed integrity (spec section 9 step 1 / Appendix A)');

$all_items = $items->all();
$by_family = array(1 => 0, 2 => 0);
$by_formula = array();
$by_severity = array();
$by_feature = array();

foreach ($all_items as $item) {
    $by_family[(int) $item->plc_family_id]++;
    $f = $item->formula_code;
    $by_formula[$f] = isset($by_formula[$f]) ? $by_formula[$f] + 1 : 1;
    $s = $item->issue_severity;
    $by_severity[$s] = isset($by_severity[$s]) ? $by_severity[$s] + 1 : 1;
    if (!empty($item->feature_code)) {
        $c = $item->feature_code;
        $by_feature[$c] = isset($by_feature[$c]) ? $by_feature[$c] + 1 : 1;
    }
}

$master = $GLOBALS['ABOM_STUB_CI']->Bom_master_model;

check('abom_item rows',                71, count($all_items));
check('  family 1 (FX5)',              29, $by_family[1]);
check('  family 2 (iQ-R)',             42, $by_family[2]);
check('abom_section rows',              8, $master->count_sections());
check('abom_plc_rule rows (active)',    4, $master->count_rules());
check('abom_feature rows',              4, $master->count_features());
check('abom_formula rows',              6, $master->count_formulas());
check('formula FIXED',                 37, isset($by_formula['FIXED']) ? $by_formula['FIXED'] : 0);
check('formula MANUAL',                29, isset($by_formula['MANUAL']) ? $by_formula['MANUAL'] : 0);
check('formula J4_STO',                 2, isset($by_formula['J4_STO']) ? $by_formula['J4_STO'] : 0);
check('formula BATTERY',                2, isset($by_formula['BATTERY']) ? $by_formula['BATTERY'] : 0);
check('formula AXES_MINUS_1',           1, isset($by_formula['AXES_MINUS_1']) ? $by_formula['AXES_MINUS_1'] : 0);
check('severity none',                 35, isset($by_severity['none']) ? $by_severity['none'] : 0);
check('severity review',               29, isset($by_severity['review']) ? $by_severity['review'] : 0);
check('severity no_erp',                5, isset($by_severity['no_erp']) ? $by_severity['no_erp'] : 0);
check('severity conflict',              2, isset($by_severity['conflict']) ? $by_severity['conflict'] : 0);
check('gated feat_perf',                4, isset($by_feature['feat_perf']) ? $by_feature['feat_perf'] : 0);
check('gated feat_brake',               1, isset($by_feature['feat_brake']) ? $by_feature['feat_brake'] : 0);
check('gated feat_dbr',                 1, isset($by_feature['feat_dbr']) ? $by_feature['feat_dbr'] : 0);
check('gated feat_imark',               1, isset($by_feature['feat_imark']) ? $by_feature['feat_imark'] : 0);

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
//  Quantity formulas
// =====================================================================

heading('7-9. Quantity formulas (spec 2.2)');

$r = $engine->generate(cfg(array('axes' => 8)));
$sscnet = line_by_part($r['lines'], 'MR-J3-BUS05M');
check('AXES_MINUS_1 at 8 axes',                7, $sscnet ? (int) $sscnet->qty : -1);

$r = $engine->generate(cfg(array('axes' => 1)));
$sscnet = line_by_part($r['lines'], 'MR-J3-BUS05M');
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
//  Row-class precedence
// =====================================================================

heading('13. Row-class precedence');

// Ids 59 and 66 both carry issue_severity = conflict AND formula MANUAL.
// Severity must outrank category.
$conflict_item = $items->get_item(59);
check('conflict + MANUAL -> is-conflict', 'is-conflict', $engine->row_class($conflict_item));
check('  (item is genuinely MANUAL)',        'MANUAL', $conflict_item->formula_code);

$noerp_item = $items->get_item(3);            // FX5-16EX/ES, no ERP code
check('no ERP code -> is-noerp',            'is-noerp', $engine->row_class($noerp_item));

$review_item = $items->get_item(7);           // FX5-4LC, review
check('review severity -> is-review',      'is-review', $engine->row_class($review_item));

$optional_item = $items->get_item(20);        // MR-JE-70B, optional, severity none
check('optional -> is-optional',         'is-optional', $engine->row_class($optional_item));

$manual_item = $items->get_item(17);          // HG-JR353, MANUAL, severity none
check('MANUAL only -> is-manual',          'is-manual', $engine->row_class($manual_item));

$clean_item = $items->get_item(1);            // FX5U-80MT/ESS, clean
check('clean line -> no modifier',                 '', $engine->row_class($clean_item));

// Every flag still reaches the STATUS column even when the row class
// can only show one of them.
$badges = $engine->status_badges($conflict_item);
check('conflict line badges',   array('CONFLICT', 'MANUAL QTY'), $badges);

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
