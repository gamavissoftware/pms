<?php
/**
 * design_diff.php — module output vs the approved design document
 *
 *   php BOMMODULEDEVELOPMENT/tests/design_diff.php iqr
 *   php BOMMODULEDEVELOPMENT/tests/design_diff.php fx5
 *
 * Compares the rendered abom/view page against
 * SPM1200L_Automation_BOM_DF1826_DF1827_Review.html on the things that
 * actually constitute design fidelity: the palette, the nine column
 * headers, the section/data row structure, the sign-off block, and the
 * presence of every structural class the design uses.
 *
 * CLASS AND ID RENAME MAP
 * -----------------------
 * Four design class names and four DOM ids were prefixed, because
 * production is known to carry a substantial BOM module which almost
 * certainly ships its own stylesheet. Specificity is not a sufficient
 * defence — .abom-wrap .bom-topbar (0,2,0) loses to #content .bom-topbar
 * (1,1,0) and to !important — and duplicate DOM ids are silently
 * destructive, since getElementById returns whichever element comes
 * first in the document.
 *
 * The map is applied to the REFERENCE before diffing, so this still
 * reports "N of N" and tests same-structure/same-appearance rather than
 * same-spelling.
 *
 * Exit code 0 = no differences, 1 = differences found.
 *
 * PHP 7.4 compatible.
 */

$RENAMES = array(
    // design name      => module name
    'bom-table-wrap'    => 'abom-table-wrap',
    'bom-topbar'        => 'abom-topbar',
    'bom-actions'       => 'abom-actions',
    'bom-area'          => 'abom-area',
    'bomTableHost'      => 'abomTableHost',
    'bomContent'        => 'abomContent',
    'bomTable'          => 'abomTable',
    'bomBody'           => 'abomBody',
);

$root  = dirname(dirname(__DIR__));
$which = isset($argv[1]) ? strtolower($argv[1]) : 'iqr';

$reference = $root . '/BOMMODULEDEVELOPMENT/SPM1200L_Automation_BOM_DF1826_DF1827_Review.html';
$rendered  = sys_get_temp_dir() . '/abom_rendered_' . $which . '.html';

// Render through the real view partials.
exec(sprintf('php %s %s > %s 2>/dev/null',
    escapeshellarg(__DIR__ . '/render_view.php'),
    escapeshellarg($which),
    escapeshellarg($rendered)
), $out, $code);

if ($code !== 0 || !is_readable($rendered)) {
    echo "  Could not render the module view.\n";
    exit(1);
}

$ref  = file_get_contents($reference);
$mine = file_get_contents($rendered);

/** Apply the rename map to the reference so like is compared with like. */
function mapped($text, array $renames)
{
    foreach ($renames as $from => $to) {
        $text = preg_replace('/(?<![A-Za-z0-9_-])' . preg_quote($from, '/') . '(?![A-Za-z0-9_-])/', $to, $text);
    }
    return $text;
}

$diffs = 0;

// ---------------------------------------------------------------------
function palette($css)
{
    preg_match_all('/--([a-z0-9]+):\s*([#A-Za-z0-9]+);/', $css, $m, PREG_SET_ORDER);
    $out = array();
    foreach ($m as $x) { $out[$x[1]] = strtoupper($x[2]); }
    return $out;
}

echo "\n\033[1m1. PALETTE\033[0m\n";
$a = palette($ref); $b = palette($mine);
foreach ($a as $k => $v) {
    $mv = isset($b[$k]) ? $b[$k] : '(absent)';
    if ($v === $mv) {
        printf("  same        --%-9s %s\n", $k, $v);
    } else {
        $diffs++;
        printf("  \033[31mDIFFERS\033[0m     --%-9s reference %s, module %s\n", $k, $v, $mv);
    }
}

// ---------------------------------------------------------------------
function headers($html)
{
    if (!preg_match('/<thead>(.*?)<\/thead>/s', $html, $m)) { return array(); }
    preg_match_all('/<th[^>]*>(.*?)<\/th>/s', $m[1], $t);
    return array_map(function ($s) {
        return trim(html_entity_decode(strip_tags($s)));
    }, $t[1]);
}

echo "\n\033[1m2. COLUMN HEADERS\033[0m\n";
$ha = headers($ref); $hb = headers($mine);
printf("  reference %d columns, module %d columns\n", count($ha), count($hb));
$n = max(count($ha), count($hb));
for ($i = 0; $i < $n; $i++) {
    $x = isset($ha[$i]) ? $ha[$i] : '(none)';
    $y = isset($hb[$i]) ? $hb[$i] : '(none)';
    if ($x === $y) { printf("  same        %d. %s\n", $i + 1, $x); }
    else { $diffs++; printf("  \033[31mDIFFERS\033[0m     %d. reference '%s', module '%s'\n", $i + 1, $x, $y); }
}

// ---------------------------------------------------------------------
echo "\n\033[1m3. STRUCTURAL CLASSES AND IDS (rename map applied)\033[0m\n";
$structural = array(
    'app-header','badge','layout','sidebar','sidebar-title','sidebar-body','form-group',
    'cfg-2col','plc-badge','divider','legend','lg-chip','main','bom-topbar','config-chip',
    'bom-actions','btn-sm','btn-print','bom-area','doc-title','dt-date','stats-row','stat-card',
    'bom-table-wrap','sec-row','data-row','erp-cell','erp-new','desc-cell','part-cell',
    'formula-tag','tag-pending','tag-edited','tag-ok','remarks-cell','filter-pills','pill',
    'action-banner','approval-block','ap-note','ap-grid','ap-box','ap-role','ap-line','ap-lbl',
    'bomTable','bomBody',
);
$missing = array();
foreach ($structural as $name) {
    $expect = isset($RENAMES[$name]) ? $RENAMES[$name] : $name;
    if (strpos($mine, $expect) === false) { $missing[] = $name . ' (as ' . $expect . ')'; }
}
printf("  %d of %d present\n", count($structural) - count($missing), count($structural));
if (!empty($missing)) {
    $diffs += count($missing);
    foreach ($missing as $m) { printf("  \033[31mMISSING\033[0m     %s\n", $m); }
}

// ---------------------------------------------------------------------
echo "\n\033[1m4. SIGN-OFF BLOCK\033[0m\n";
preg_match_all('/<div class="ap-role">(.*?)<\/div>/', $ref,  $ra);
preg_match_all('/<div class="ap-role">(.*?)<\/div>/', $mine, $rb);
$ra = array_map('html_entity_decode', $ra[1]);
$rb = array_map('html_entity_decode', $rb[1]);
printf("  reference %d boxes, module %d boxes\n", count($ra), count($rb));
$n = max(count($ra), count($rb));
for ($i = 0; $i < $n; $i++) {
    $x = isset($ra[$i]) ? $ra[$i] : '(none)';
    $y = isset($rb[$i]) ? $rb[$i] : '(none)';
    if ($x === $y) { printf("  same        %s\n", $x); }
    else { $diffs++; printf("  \033[31mDIFFERS\033[0m     reference '%s', module '%s'\n", $x, $y); }
}

// ---------------------------------------------------------------------
echo "\n\033[1m5. BODY TYPOGRAPHY\033[0m\n";
preg_match('/body\s*\{([^}]*)\}/', $ref, $rbody);
preg_match('/\.abom-wrap\s*\{(.*?)\}/s', $mine, $mbody);
$clean = function ($s) {
    $s = preg_replace('/--[a-z0-9]+:[^;]+;/', '', $s);
    $s = preg_replace('#/\*.*?\*/#s', '', $s);
    return trim(preg_replace('/\s+/', ' ', $s));
};
foreach (array('font-family','font-size','background','color') as $prop) {
    preg_match('/' . $prop . ':\s*([^;]+);/', $clean($rbody[1]), $x);
    preg_match('/' . $prop . ':\s*([^;]+);/', $clean($mbody[1]), $y);
    $xv = isset($x[1]) ? trim($x[1]) : '(none)';
    $yv = isset($y[1]) ? trim($y[1]) : '(none)';
    if (strcasecmp($xv, $yv) === 0) { printf("  same        %-12s %s\n", $prop, $xv); }
    else { $diffs++; printf("  \033[31mDIFFERS\033[0m     %-12s reference '%s', module '%s'\n", $prop, $xv, $yv); }
}

// ---------------------------------------------------------------------
echo "\n" . str_repeat('-', 66) . "\n";
if ($diffs === 0) {
    echo "  \033[32mNo differences. Structure, palette and typography match the design.\033[0m\n";
} else {
    printf("  \033[31m%d DIFFERENCE(S)\033[0m\n", $diffs);
}
echo str_repeat('-', 66) . "\n\n";

exit($diffs === 0 ? 0 : 1);
