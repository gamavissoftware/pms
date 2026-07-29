<?php
/**
 * dump_reference.php
 *
 * Generates the two Appendix B reference configurations and prints the
 * full line list for item-by-item comparison against DF-1827 and
 * DF-1826 REV.02, in the nine columns of the approved design.
 *
 *   php BOMMODULEDEVELOPMENT/tests/dump_reference.php
 *   php BOMMODULEDEVELOPMENT/tests/dump_reference.php fx5
 *   php BOMMODULEDEVELOPMENT/tests/dump_reference.php iqr
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

require __DIR__ . '/seed_parser.php';
require __DIR__ . '/stubs.php';

defined('BASEPATH') OR define('BASEPATH', true);

$seed = new Abom_seed_parser($root . '/BOMMODULEDEVELOPMENT/abom_seed.sql');
$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);

require $root . '/application/libraries/Abom_engine.php';

$engine = new Abom_engine();

$which = isset($argv[1]) ? strtolower($argv[1]) : 'both';

$configs = array(
    'fx5' => array(
        'title'  => 'DF-1827  ·  SPM1200L  ·  8 Axis / 12 Track / 140 PPM / Intermittent / LHS',
        'expect' => 29,
        'cfg'    => array(
            'axes' => 8, 'tracks' => 12, 'speed_ppm' => 140,
            'motion_type' => 'Intermittent', 'machine_model' => 'SPM1200L',
            'machine_side' => 'LHS', 'j4_units' => 7, 'battery_qty' => 7,
            'plc_family_id' => null,
            'features' => array('feat_perf' => 1, 'feat_brake' => 1, 'feat_dbr' => 1, 'feat_imark' => 1),
        ),
    ),
    'iqr' => array(
        'title'  => 'DF-1826 REV.02  ·  SPM1200L  ·  15 Axis / 12 Track / 180 PPM / Continuous',
        'expect' => 42,
        'cfg'    => array(
            'axes' => 15, 'tracks' => 12, 'speed_ppm' => 180,
            'motion_type' => 'Continuous', 'machine_model' => 'SPM1200L',
            'machine_side' => 'N/A', 'j4_units' => 11, 'battery_qty' => 12,
            'plc_family_id' => null,
            'features' => array('feat_perf' => 1, 'feat_brake' => 1, 'feat_dbr' => 1, 'feat_imark' => 1),
        ),
    ),
);

/**
 * REMARKS column, per the agreed nine-column mapping:
 * usage_remark, then panel_location, then OPTIONAL.
 */
function remarks_cell($line)
{
    $parts = array();

    if (!empty($line->usage_remark)) {
        $parts[] = $line->usage_remark;
    }
    if (!empty($line->panel_location)) {
        $parts[] = $line->panel_location;
    }
    if (!empty($line->is_optional)) {
        $parts[] = 'OPTIONAL';
    }

    return implode(' · ', $parts);
}

function clip($s, $n)
{
    $s = (string) $s;
    if (mb_strlen($s) <= $n) {
        return $s;
    }
    return mb_substr($s, 0, $n - 1) . '…';
}

/**
 * printf's %-Ns pads by BYTES. The data contains — – · Ω, so pad by
 * CHARACTERS instead or the columns drift. (mb_str_pad is PHP 8.3+.)
 */
function pad($s, $n)
{
    $s = clip($s, $n);
    $gap = $n - mb_strlen($s);

    return $s . ($gap > 0 ? str_repeat(' ', $gap) : '');
}

foreach ($configs as $key => $spec) {
    if ($which !== 'both' && $which !== $key) {
        continue;
    }

    $r = $engine->generate($spec['cfg']);

    echo "\n\033[1m" . $spec['title'] . "\033[0m\n";
    printf(
        "PLC family: %s   (%s)\n",
        $r['family']['code'],
        $r['family']['explanation']
    );
    printf(
        "Lines: %d  (Appendix B expects %d)  %s   Total qty: %d\n\n",
        count($r['lines']),
        $spec['expect'],
        count($r['lines']) === $spec['expect'] ? "\033[32mMATCH\033[0m" : "\033[31mMISMATCH\033[0m",
        $r['stats']['total_qty']
    );

    printf(
        "%-4s %-10s %-42s %-22s %-12s %5s %-6s %-34s %s\n",
        'S.NO', 'ERP CODE', 'DESCRIPTION', 'MODEL NO. / PART NO.',
        'MFR', 'QTY.', 'UOM', 'REMARKS', 'STATUS'
    );
    echo str_repeat('─', 170) . "\n";

    $current_section = null;
    foreach ($r['lines'] as $line) {
        if ($line->section_name !== $current_section) {
            $current_section = $line->section_name;
            $count = 0;
            foreach ($r['lines'] as $l) {
                if ($l->section_name === $current_section) {
                    $count++;
                }
            }
            printf("\n\033[1m▶ %s (%d items)\033[0m\n", strtoupper($current_section), $count);
        }

        printf(
            "%-4d %s %s %s %s %5d %s %s %s\n",
            $line->line_no,
            pad($line->erp_code !== null && $line->erp_code !== '' ? $line->erp_code : 'PENDING', 10),
            pad($line->description, 42),
            pad($line->part_no, 22),
            pad($line->manufacturer, 12),
            $line->qty,
            pad($line->uom, 6),
            pad(remarks_cell($line), 34),
            implode(', ', $line->status_badges)
        );
    }

    echo "\n" . str_repeat('─', 170) . "\n";
    printf(
        "Stats — lines %d · total qty %d · manual %d · review %d · missing ERP %d · conflict %d · optional %d · open issues %d\n",
        $r['stats']['lines'],
        $r['stats']['total_qty'],
        $r['stats']['manual'],
        $r['stats']['review'],
        $r['stats']['no_erp'],
        $r['stats']['conflict'],
        $r['stats']['optional'],
        $r['stats']['open_issues']
    );
}

echo "\n";
