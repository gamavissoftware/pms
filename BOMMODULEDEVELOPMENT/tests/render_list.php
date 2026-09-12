<?php
/**
 * render_list.php
 *
 * Renders the saved-BOM register (application/views/abom/list.php) with
 * no CodeIgniter bootstrap and no database, so the column layout and the
 * filter controls can be checked without a live install.
 *
 * The register is the screen everyone starts from, and it is the one
 * screen with no test coverage at all — it renders straight from a
 * model call, so nothing in run_tests.php ever touches it. A column
 * added to <thead> and not to <tbody> silently shears the whole table.
 *
 *   php BOMMODULEDEVELOPMENT/tests/render_list.php          # 3 brands
 *   php BOMMODULEDEVELOPMENT/tests/render_list.php single   # 1 brand
 *   php BOMMODULEDEVELOPMENT/tests/render_list.php --html   # dump markup
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

defined('BASEPATH')   OR define('BASEPATH', true);
defined('sitetitle')  OR define('sitetitle', 'SHUBHAM PACKAGING PMS');
defined('assets_url') OR define('assets_url', '/assets/');
defined('page_url')   OR define('page_url', '/index.php/');

require __DIR__ . '/stubs.php';
require $root . '/application/helpers/abom_helper.php';

class Abom_list_loader
{
    public $load;
    public $vars = array();
    private $root;

    public function __construct($root) { $this->load = $this; $this->root = $root; }

    public function view($name, $data = array())
    {
        // common/footer is application chrome this harness does not have.
        if (strpos($name, 'common/') === 0) { return; }
        $this->vars = array_merge($this->vars, $data);
        extract($this->vars);
        include $this->root . '/application/views/' . $name . '.php';
    }
}

$single = isset($argv[1]) && $argv[1] === 'single';
$dump   = in_array('--html', $argv, true);

/** One saved BOM row, shaped exactly as Abom_model::get_all() returns it. */
function bom($id, $no, $df, $model, $axes, $tracks, $ppm, $motion,
             $family, $variant, $lines, $qty, $issues, $status, $mfrs)
{
    $b = new stdClass();
    $b->id = $id; $b->bom_no = $no; $b->revision = '00'; $b->df_ref = $df;
    $b->machine_model = $model; $b->axes = $axes; $b->tracks = $tracks;
    $b->speed_ppm = $ppm; $b->motion_type = $motion;
    $b->family_code = $family; $b->plc_family_locked = 0;
    $b->variant_code = $variant; $b->variant_locked = 0;
    $b->total_lines = $lines; $b->total_qty = $qty; $b->open_issues = $issues;
    $b->status = $status; $b->created_at = '2026-08-18 09:00:00';
    $b->manufacturers = $mfrs;
    return $b;
}

$boms = array(
    bom(10, 'ABOM-10', 'DF-1805A', 'SPM1250P', 11, 6, 120, 'Continuous',
        'iQ-R', 'IQR-TCF', 40, 117, 1, 'draft',
        array('MITSUBISHI', 'RECKON', 'AUTONICS')),
    bom(9, 'ABOM-9', 'DF-1808', 'SPM1200L', 6, 12, 100, 'Intermittent',
        'FX5', 'FX5-1808', 29, 74, 0, 'approved',
        array('MITSUBISHI', 'RECKON')),
    bom(8, 'ABOM-8', null, 'SPM1200L', 15, 12, 180, 'Continuous',
        'iQ-R', 'IQR-FLM', 42, 129, 0, 'submitted',
        array('MITSUBISHI')),
    // A BOM whose lines carry no brand at all — must render an em dash,
    // not a PHP notice or a blank cell.
    bom(7, 'ABOM-7', 'DF-1883', 'SPM1250P', 11, 6, 70, 'Intermittent',
        'FX5', 'FX5-TCF', 31, 88, 0, 'rejected', array()),
);

$manufacturers = $single
    ? array('MITSUBISHI')
    : array('AUTONICS', 'MITSUBISHI', 'RECKON');

$loader = new Abom_list_loader($root);

ob_start();
$loader->view('abom/list', array(
    'boms'             => $boms,
    'filters'          => array('status' => '', 'machine_model' => '',
                                'manufacturer' => 'RECKON', 'search' => ''),
    'models'           => array('SPM1200L', 'SPM1250P'),
    'manufacturers'    => $manufacturers,
    'statuses'         => array('draft', 'submitted', 'checked', 'eng_approved',
                                'approved', 'rejected', 'superseded'),
    'deletable_status' => array('draft', 'rejected'),
));
$html = ob_get_clean();

if ($dump) { echo $html; exit(0); }

// ---------------------------------------------------------------------

$pass = 0; $fail = 0;
function ok($label, $got, $want)
{
    global $pass, $fail;
    $good = ($got === $want);
    $good ? $pass++ : $fail++;
    printf("  %s  %-50s %s\n",
        $good ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m", $label,
        $good ? '' : ('got ' . var_export($got, true) . ', want ' . var_export($want, true)));
}

echo "\nSAVED-BOM REGISTER — " . ($single ? 'ONE manufacturer' : 'THREE manufacturers')
   . "\n" . str_repeat('=', 72) . "\n\n";

// A sheared table is the whole risk of adding a column, so count first.
preg_match('~<thead>(.*?)</thead>~s', $html, $h);
$ths = substr_count($h[1], '<th');

preg_match_all('~<tr class="data-row[^"]*">(.*?)</tr>~s', $html, $rows);
$counts = array();
foreach ($rows[1] as $r) { $counts[] = substr_count($r, '<td'); }

ok('every row has as many cells as the header has columns',
   array_unique($counts), array($ths));
ok('all four BOMs rendered', count($rows[1]), 4);

echo "\n  header columns: {$ths}\n\n";

ok('MANUFACTURER header present',
   strpos($html, '>MANUFACTURER</th>') !== false, true);

// The brand must sit between BUILD and LINES, not tacked on the end.
$order = array();
preg_match_all('~<th[^>]*>([A-Z ./]+)</th>~', $h[1], $names);
foreach ($names[1] as $n) { $order[] = trim($n); }
$i = array_search('MANUFACTURER', $order, true);
ok('MANUFACTURER sits directly after BUILD', $order[$i - 1], 'BUILD');
ok('and directly before LINES', $order[$i + 1], 'LINES');

echo "\n";

ok('primary brand shown for a 3-brand BOM',
   strpos($rows[1][0], 'MITSUBISHI') !== false, true);
ok('the other two are a +2 count, not three names',
   strpos($rows[1][0], '>+2</span>') !== false, true);
ok('full list is on hover',
   strpos($rows[1][0], 'title="MITSUBISHI, RECKON, AUTONICS"') !== false, true);
ok('a 2-brand BOM reads +1',
   strpos($rows[1][1], '>+1</span>') !== false, true);
ok('a single-brand BOM has no count badge',
   strpos($rows[1][2], 'mfr-more') === false, true);
ok('a BOM with no brands renders an em dash', (bool) preg_match(
   '~<td>\s*&mdash;\s*</td>~', $rows[1][3]), true);

echo "\n";

$has_filter = strpos($html, '<select name="mfr">') !== false;
ok($single ? 'filter HIDDEN while only one brand exists'
           : 'manufacturer filter rendered', $has_filter, !$single);

if (!$single) {
    ok('every known brand is an option',
       substr_count($html, '<option value="AUTONICS"')
       + substr_count($html, '<option value="MITSUBISHI"')
       + substr_count($html, '<option value="RECKON"'), 3);
    ok('the active filter is preselected',
       strpos($html, '<option value="RECKON" selected>') !== false, true);
    ok('and an "all" option exists',
       strpos($html, '<option value="">All manufacturers</option>') !== false, true);
    ok('the filter posts as GET on the same form',
       substr_count($html, '<form method="get"'), 1);
}

echo "\n" . str_repeat('-', 72) . "\n";
echo $fail === 0
    ? "\033[32m\033[1m  ALL {$pass} ASSERTIONS PASSED\033[0m\n"
    : "\033[31m\033[1m  {$fail} FAILED, {$pass} passed\033[0m\n";
echo str_repeat('-', 72) . "\n\n";

exit($fail === 0 ? 0 : 1);
