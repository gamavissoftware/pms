<?php
/**
 * build_filter_probe.php
 *
 * Drives the REAL Abom::filter_variants() over the REAL seeded build
 * register, and renders the REAL master_bom_list.php view.
 *
 * A filter that returns the wrong rows is silent — nothing errors, the
 * table just quietly omits a build somebody needed, or shows one they
 * excluded. The only way to know is to assert the exact set that comes
 * back for each query, which is what this does.
 *
 *   php BOMMODULEDEVELOPMENT/tests/build_filter_probe.php
 *   php BOMMODULEDEVELOPMENT/tests/build_filter_probe.php --html
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

require __DIR__ . '/seed_parser.php';
require __DIR__ . '/stubs.php';

defined('BASEPATH')   OR define('BASEPATH', true);
defined('sitetitle')  OR define('sitetitle', 'SHUBHAM PACKAGING PMS');
defined('assets_url') OR define('assets_url', '/assets/');
defined('page_url')   OR define('page_url', '/index.php/');

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
require $root . '/application/helpers/abom_helper.php';

if (!class_exists('CI_Controller')) {
    class CI_Controller { public function __get($k) { return $GLOBALS['ABOM_STUB_CI']->$k; } }
}
if (!function_exists('show_404')) { function show_404() {} }

require $root . '/application/controllers/Abom.php';

$ctl    = (new ReflectionClass('Abom'))->newInstanceWithoutConstructor();
$filter = new ReflectionMethod('Abom', 'filter_variants');
$filter->setAccessible(true);
$field  = new ReflectionMethod('Abom', 'distinct_variant_field');
$field->setAccessible(true);

$master = $GLOBALS['ABOM_STUB_CI']->Abom_master_model;

/** Every build, keyed by id, exactly as the controller loads them. */
$variants = $master->get_variants();

/**
 * Item and flag counts per build. Computed here from the seed rather
 * than stubbed with invented numbers, so the flag filters are asserted
 * against what the register will really show.
 */
$summary = array();
foreach ($seed->rows('abom_item') as $it) {
    $vid = (int) $it->variant_id;
    if (!isset($summary[$vid])) {
        $summary[$vid] = array('items' => 0, 'active' => 0,
                               'conflict' => 0, 'no_erp' => 0, 'review' => 0);
    }
    $summary[$vid]['items']++;
    if ((int) $it->is_active === 1)            { $summary[$vid]['active']++; }
    if ($it->issue_severity === 'conflict')    { $summary[$vid]['conflict']++; }
    if ($it->issue_severity === 'review')      { $summary[$vid]['review']++; }
    if ($it->erp_code === null || $it->erp_code === '') { $summary[$vid]['no_erp']++; }
}

function q(array $over = array())
{
    return array_merge(array('search' => '', 'family' => 0, 'model' => '',
                             'panel' => '', 'flag' => '', 'active' => ''), $over);
}

$pass = 0; $fail = 0;
function ok($label, $got, $want)
{
    global $pass, $fail;
    $good = ($got === $want);
    $good ? $pass++ : $fail++;
    printf("  %s  %-54s %s\n",
        $good ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m", $label,
        $good ? '' : ("\n        got  " . var_export($got, true)
                    . "\n        want " . var_export($want, true)));
}

/** Filter, and return the matching build CODES, sorted. */
function codes($ctl, $filter, $variants, $summary, array $over = array())
{
    $out = array();
    foreach ($filter->invoke($ctl, $variants, $summary, q($over)) as $v) {
        $out[] = $v->code;
    }
    sort($out);
    return $out;
}

$all = codes($ctl, $filter, $variants, $summary);

echo "\nBUILD REGISTER FILTERS\n" . str_repeat('=', 78) . "\n\n";
echo "  register holds " . count($variants) . " builds ("
   . count($all) . " after an empty filter)\n\n";

ok('an empty filter changes nothing', count($all), count($variants));

echo "SEARCH\n";
ok('by build code', codes($ctl, $filter, $variants, $summary, array('search' => 'FX5-1808')),
   array('FX5-1808'));
ok('by DF number', codes($ctl, $filter, $variants, $summary, array('search' => 'DF-1826')),
   array('IQR-HS'));
// FX5-JE is the RETIRED combined build, and its source_df names every
// DF it was assembled from — DF-1858 among them. Finding it is correct:
// somebody chasing an old drawing number wants to know which build
// absorbed it, retired or not.
ok('by DF number without the prefix',
   codes($ctl, $filter, $variants, $summary, array('search' => '1858')),
   array('FX5-1858', 'FX5-JE'));
ok('  and hiding retired builds narrows it to the live one',
   codes($ctl, $filter, $variants, $summary, array('search' => '1858', 'active' => '1')),
   array('FX5-1858'));
ok('by machine model', codes($ctl, $filter, $variants, $summary, array('search' => 'SPM1250P')),
   array('FX5-TCF', 'IQR-FLM', 'IQR-TCF'));
ok('by words in the name',
   codes($ctl, $filter, $variants, $summary, array('search' => 'tilting cup')),
   array('FX5-TCF', 'IQR-TCF'));
// THE PHRASE TRAP. Build names read "SPM1200L 6 axis / 12 track /
// 100 PPM", so a naive all-words match finds "6" in "6 axis" and
// "track" in "12 track" and hands back a 12-track machine to somebody
// who asked for 6. Phrase-first exists for exactly this.
ok('by the recorded machine — "6 track"',
   codes($ctl, $filter, $variants, $summary, array('search' => '6 track')),
   array('FX5-1858', 'FX5-TCF', 'IQR-TCF'));
ok('  "12 track" does not return the 6-track builds',
   in_array('FX5-1858', codes($ctl, $filter, $variants, $summary,
        array('search' => '12 track')), true), false);
ok('  and "12 track" does return a 12-track build',
   in_array('FX5-1808', codes($ctl, $filter, $variants, $summary,
        array('search' => '12 track')), true), true);
// No build contains "fx5 1808" verbatim, so this exercises the
// fallback. FX5-JE comes back on the same reasoning as above: its code
// carries "fx5" and its source DFs carry 1808.
ok('a phrase that matches nothing falls back to words',
   codes($ctl, $filter, $variants, $summary, array('search' => 'fx5 1808')),
   array('FX5-1808', 'FX5-JE'));
ok('  the fallback is genuinely reached, not a phrase hit',
   count(codes($ctl, $filter, $variants, $summary, array('search' => 'fx5 1808')))
   > count(codes($ctl, $filter, $variants, $summary, array('search' => 'fx5-1808'))), true);
ok('case does not matter',
   codes($ctl, $filter, $variants, $summary, array('search' => 'fx5-1808')),
   array('FX5-1808'));
ok('two words, any order',
   codes($ctl, $filter, $variants, $summary, array('search' => '1808 fx5')),
   codes($ctl, $filter, $variants, $summary, array('search' => 'fx5 1808')));
ok('a word that matches nothing returns nothing',
   codes($ctl, $filter, $variants, $summary, array('search' => 'zzzz')), array());
ok('surrounding whitespace is ignored',
   codes($ctl, $filter, $variants, $summary, array('search' => '  FX5-1808  ')),
   array('FX5-1808'));

echo "\nFAMILY / MODEL / PANEL\n";
$fx5 = codes($ctl, $filter, $variants, $summary, array('family' => 1));
$iqr = codes($ctl, $filter, $variants, $summary, array('family' => 2));
ok('FX5 and iQ-R partition the register', count($fx5) + count($iqr), count($all));
ok('no build appears in both', array_intersect($fx5, $iqr), array());
ok('model SPM1250P',
   codes($ctl, $filter, $variants, $summary, array('model' => 'SPM1250P')),
   array('FX5-TCF', 'IQR-FLM', 'IQR-TCF'));
ok('panel location Standalone is a subset of all',
   count(array_diff(codes($ctl, $filter, $variants, $summary,
        array('panel' => 'Standalone')), $all)), 0);

echo "\nCOMBINED — filters must AND, never OR\n";
$both = codes($ctl, $filter, $variants, $summary,
              array('family' => 1, 'model' => 'SPM1250P'));
ok('FX5 + SPM1250P is the intersection', $both, array('FX5-TCF'));
ok('and is a subset of each side',
   array_diff($both, array_intersect($fx5,
       codes($ctl, $filter, $variants, $summary, array('model' => 'SPM1250P')))),
   array());
ok('search + family together',
   codes($ctl, $filter, $variants, $summary,
         array('search' => 'tilting cup', 'family' => 2)),
   array('IQR-TCF'));
ok('a contradiction returns nothing',
   codes($ctl, $filter, $variants, $summary,
         array('search' => 'FX5-1808', 'model' => 'SPM1250P')),
   array());

echo "\nFLAGS\n";
$clean = codes($ctl, $filter, $variants, $summary, array('flag' => 'clean'));
foreach (array('conflict', 'no_erp', 'review') as $f) {
    $got = codes($ctl, $filter, $variants, $summary, array('flag' => $f));
    ok('"' . $f . '" returns only builds that have some', true, (function () use ($got, $variants, $summary, $f) {
        foreach ($variants as $id => $v) {
            if (in_array($v->code, $got, true) && empty($summary[$id][$f])) { return false; }
        }
        return true;
    })());
    ok('  and none of them are "clean"', array_intersect($got, $clean), array());
}
ok('"clean" builds really have nothing flagged', true, (function () use ($clean, $variants, $summary) {
    foreach ($variants as $id => $v) {
        if (!in_array($v->code, $clean, true)) { continue; }
        $c = isset($summary[$id]) ? $summary[$id] : array('conflict'=>0,'no_erp'=>0,'review'=>0);
        if ($c['conflict'] || $c['no_erp'] || $c['review']) { return false; }
    }
    return true;
})());

echo "\nACTIVE / RETIRED\n";
$act = codes($ctl, $filter, $variants, $summary, array('active' => '1'));
$ret = codes($ctl, $filter, $variants, $summary, array('active' => '0'));
ok('active + retired = every build', count($act) + count($ret), count($all));
ok('none in both', array_intersect($act, $ret), array());
ok('the 11 shipped builds are the active ones', count($act), 11);
ok('"" means active AND retired, not "active"', count($all) !== count($act), true);

echo "\nDROPDOWN OPTIONS ARE DERIVED\n";
ok('models come from the data',
   $field->invoke($ctl, $variants, 'machine_model'),
   array('SPM1200L', 'SPM1250P'));
$p = $field->invoke($ctl, $variants, 'default_panel_location');
ok('panel locations come from the data', count($p) > 1, true);
ok('  and carry no blanks', in_array('', $p, true), false);

// ---------------------------------------------------------------------
// The view itself: a filter form that does not round-trip its own state
// silently loses the operator's query on every submit.
// ---------------------------------------------------------------------
class Abom_bf_loader
{
    public $load; public $vars = array(); private $root;
    public function __construct($r) { $this->load = $this; $this->root = $r; }
    public function view($name, $data = array())
    {
        if (strpos($name, 'common/') === 0) { return; }
        $this->vars = array_merge($this->vars, $data);
        extract($this->vars);
        include $this->root . '/application/views/' . $name . '.php';
    }
}

$applied  = q(array('search' => 'tilting cup', 'family' => 2, 'model' => 'SPM1250P',
                    'flag' => 'review', 'active' => '1'));
$shown    = $filter->invoke($ctl, $variants, $summary, $applied);

$families = $master->get_families();
$loader   = new Abom_bf_loader($root);

ob_start();
$loader->view('abom/master_bom_list', array(
    'variants' => $shown,
    'total'    => count($variants),
    'filters'  => $applied,
    'families' => $families,
    'summary'  => $summary,
    'models'   => $field->invoke($ctl, $variants, 'machine_model'),
    'panels'   => $field->invoke($ctl, $variants, 'default_panel_location'),
    'message'  => '',
));
$html = ob_get_clean();

if (in_array('--html', $argv, true)) { echo $html; exit(0); }

echo "\nTHE FORM REMEMBERS WHAT WAS ASKED\n";
ok('search text is put back in the box',
   strpos($html, 'value="tilting cup"') !== false, true);
ok('family stays selected',
   (bool) preg_match('/<option value="2" selected>/', $html), true);
ok('model stays selected',
   strpos($html, '<option value="SPM1250P" selected>') !== false, true);
ok('flag stays selected',
   strpos($html, '<option value="review" selected>') !== false, true);
ok('active stays selected',
   strpos($html, '<option value="1" selected>') !== false, true);
ok('the count says "of", not just a total',
   (bool) preg_match('/\d+ of ' . count($variants) . ' builds/', $html), true);
ok('a clear-filters link is offered',
   strpos($html, 'mb-clear') !== false, true);

ob_start();
$loader2 = new Abom_bf_loader($root);
$loader2->view('abom/master_bom_list', array(
    'variants' => $variants, 'total' => count($variants), 'filters' => q(),
    'families' => $families, 'summary' => $summary,
    'models' => $field->invoke($ctl, $variants, 'machine_model'),
    'panels' => $field->invoke($ctl, $variants, 'default_panel_location'),
    'message' => '',
));
$plain = ob_get_clean();

ok('unfiltered shows a plain total',
   (bool) preg_match('/>\s*' . count($variants) . ' builds\s*</', $plain), true);
ok('and offers no clear link', strpos($plain, 'mb-clear') === false, true);

ob_start();
$loader3 = new Abom_bf_loader($root);
$loader3->view('abom/master_bom_list', array(
    'variants' => array(), 'total' => count($variants),
    'filters'  => q(array('search' => 'zzzz')),
    'families' => $families, 'summary' => $summary,
    'models' => array('SPM1200L', 'SPM1250P'), 'panels' => array('Standalone'),
    'message' => '',
));
$none = ob_get_clean();

ok('no matches renders an empty state, not a bare table',
   strpos($none, 'No build matches that filter') !== false, true);
ok('  with a way back to the full list',
   strpos($none, 'Show all ' . count($variants)) !== false, true);
ok('  and no table headers left stranded',
   strpos($none, '<table id="abomTable">') === false, true);

echo "\n" . str_repeat('-', 78) . "\n";
echo $fail === 0
    ? "\033[32m\033[1m  ALL {$pass} ASSERTIONS PASSED\033[0m\n"
    : "\033[31m\033[1m  {$fail} FAILED, {$pass} passed\033[0m\n";
echo str_repeat('-', 78) . "\n\n";

exit($fail === 0 ? 0 : 1);
