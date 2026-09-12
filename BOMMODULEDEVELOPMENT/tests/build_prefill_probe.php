<?php
/**
 * build_prefill_probe.php
 *
 * Proves the "Generate BOM" button on the Reference BOMs register lands
 * where it says it will.
 *
 * The button's whole promise is: press it on the FX5-1808 row and you
 * get a generator configured for the FX5-1808 build. That promise is
 * only kept if the reference machine recorded against each build in
 * abom_012_variant_reference.sql actually ROUTES BACK to that build
 * through the real selection rules — which are separate data, written
 * at a different time, by hand.
 *
 * So every active build is round-tripped through the real
 * Abom_master_model::config_for_variant() and the real Abom_engine.
 * Nothing here re-implements either.
 *
 *   php BOMMODULEDEVELOPMENT/tests/build_prefill_probe.php
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

$engine = new Abom_engine();

/**
 * The REAL Abom_master_model, not the stub.
 *
 * config_for_variant() is the method under test and it exists only on
 * the real class. Adding a copy to Abom_stub_master_model would mean
 * asserting against a reimplementation of the thing being checked,
 * which proves nothing — so the real class is instantiated over a fake
 * $db that serves the parsed seed instead.
 *
 * Only the two query shapes the method actually reaches are supported.
 */
class Seed_db
{
    private $seed;
    private $table = '';
    private $where = array();

    public function __construct(Abom_seed_parser $seed) { $this->seed = $seed; }

    public function from($t)   { $this->table = $t; return $this; }
    public function order_by() { return $this; }

    public function where($k, $v = null, $escape = true)
    {
        $this->where[] = array($k, $v);
        return $this;
    }

    public function get()
    {
        $rows = $this->seed->rows($this->table);

        foreach ($this->where as $w) {
            list($k, $v) = $w;
            $rows = array_values(array_filter($rows, function ($r) use ($k, $v) {
                return isset($r->$k) && (string) $r->$k === (string) $v;
            }));
        }

        // The real model orders by priority ASC then id ASC. Applied
        // here rather than in order_by() because the fake cannot know
        // which call meant which — and getting this wrong would make
        // rule_for_variant() read the WRONG rule, which is precisely
        // the thing the round-trip below depends on.
        usort($rows, function ($a, $b) {
            $ap = isset($a->priority) ? (int) $a->priority : (isset($a->sort_order) ? (int) $a->sort_order : 0);
            $bp = isset($b->priority) ? (int) $b->priority : (isset($b->sort_order) ? (int) $b->sort_order : 0);
            if ($ap !== $bp) { return $ap - $bp; }
            return (int) $a->id - (int) $b->id;
        });

        $this->table = ''; $this->where = array();

        return new Seed_result($rows);
    }
}

class Seed_result
{
    private $rows;
    public function __construct(array $rows) { $this->rows = $rows; }
    public function result() { return $this->rows; }
}

if (!class_exists('CI_Model')) { class CI_Model {} }

require $root . '/application/models/Abom_master_model.php';

#[AllowDynamicProperties]
class Abom_master_model_probe extends Abom_master_model {}

$master = (new ReflectionClass('Abom_master_model_probe'))->newInstanceWithoutConstructor();
Closure::bind(function ($db) { $this->db = $db; }, $master, 'Abom_master_model')(new Seed_db($seed));

// default_features() is on the real model too, but it reads
// abom_feature through the same fake — no stub involved.

$defaults = array(
    'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100,
    'motion_type' => 'Intermittent', 'machine_model' => 'SPM1200L',
    'machine_side' => 'N/A', 'j4_units' => 0, 'battery_qty' => 6,
    'df_ref' => '', 'plc_family_id' => null, 'variant_id' => null,
    'features' => $master->default_features(),
);

$pass = 0; $fail = 0;
function ok($label, $got, $want)
{
    global $pass, $fail;
    $good = ($got === $want);
    $good ? $pass++ : $fail++;
    printf("  %s  %-52s %s\n",
        $good ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m", $label,
        $good ? '' : ('got ' . var_export($got, true) . ', want ' . var_export($want, true)));
}

echo "\nBUILD PREFILL — every \"Generate BOM\" button round-trips\n"
   . str_repeat('=', 96) . "\n\n";

printf("  %-11s %-9s %-26s %-9s %6s  %s\n",
    'BUILD', 'MODEL', 'RECORDED MACHINE', 'REACHES', 'LINES', 'RECORDED?');
echo '  ' . str_repeat('-', 92) . "\n";

$active   = $master->get_active_variants();
$exact    = 0;
$roundtrip= 0;

foreach ($active as $id => $variant) {
    $from = $master->config_for_variant($id, $defaults);
    $cfg  = $from['cfg'];

    $result = $engine->generate($cfg);
    $got    = !empty($result['variant']) ? $result['variant']->code : '(none)';
    $lands  = (!empty($result['variant_id']) && (int) $result['variant_id'] === (int) $id);

    if ($lands)          { $roundtrip++; }
    if ($from['exact'])  { $exact++; }

    printf("  %-11s %-9s %-26s %-9s %6d  %s\n",
        $variant->code,
        $cfg['machine_model'],
        (int) $cfg['axes'] . 'A / ' . (int) $cfg['tracks'] . 'T / '
            . (int) $cfg['speed_ppm'] . ' PPM ' . substr($cfg['motion_type'], 0, 4),
        ($lands ? $got : "\033[31m" . $got . "\033[0m"),
        count($result['lines']),
        $from['exact'] ? 'full' : ('missing ' . implode('+', $from['missing'])));
}

echo "\n";

ok('every active build has a recorded reference machine',
   $exact, count($active));
ok('every recorded machine routes back to its own build',
   $roundtrip, count($active));
ok('all 11 builds are covered', count($active), 11);

echo "\nEACH BUILD GENERATES A NON-EMPTY SHEET\n";
$empty = array();
foreach ($active as $id => $variant) {
    $r = $engine->generate($master->config_for_variant($id, $defaults)['cfg']);
    if (empty($r['lines'])) { $empty[] = $variant->code; }
}
ok('no build prefills into an empty BOM', $empty, array());

echo "\nTHE RECORDED MACHINE MATCHES THE BUILD'S OWN NAME, WHERE IT STATES ONE\n";
// Five builds spell the machine out in their name. Those are free
// cross-checks on the migration: the name and the ref_* columns were
// written at different times from the same DF, so they must agree.
$checked = 0;
foreach ($active as $id => $variant) {
    if (!preg_match('/(\d+)\s*axis\s*\/\s*(\d+)\s*track\s*\/\s*(\d+)\s*PPM/i',
                    $variant->name, $m)) {
        continue;
    }
    $checked++;
    $cfg = $master->config_for_variant($id, $defaults)['cfg'];
    ok($variant->code . ' — name says ' . $m[1] . 'A/' . $m[2] . 'T/' . $m[3],
       (int) $cfg['axes'] . '/' . (int) $cfg['tracks'] . '/' . (int) $cfg['speed_ppm'],
       (int) $m[1] . '/' . (int) $m[2] . '/' . (int) $m[3]);
}
ok('five builds carried a cross-checkable name', $checked, 5);

echo "\nFALLBACK — a build with NO recorded machine still works\n";
// Exactly the case of a build added later and not yet filled in. It
// must still produce a usable configuration AND say what it guessed.
$bare = clone $active[10];
$bare->ref_axes = null; $bare->ref_tracks = null;
$bare->ref_speed_ppm = null; $bare->ref_motion_type = null;
$bare->ref_machine_side = null;

$reflect = new ReflectionClass($master);
$prop = $reflect->getParentClass()->getProperty('variant_cache');
$prop->setAccessible(true);
$cache = $prop->getValue($master);
$cache[10] = $bare;
$prop->setValue($master, $cache);

$from = $master->config_for_variant(10, $defaults);
ok('reports itself as NOT exact', $from['exact'], false);
ok('names every missing value', $from['missing'],
   array('axes', 'speed', 'motion type', 'tracks'));
ok('axes still come from the selection rule', (int) $from['cfg']['axes'], 6);
ok('speed still comes from the selection rule', (int) $from['cfg']['speed_ppm'], 90);
ok('tracks fall back to the module default', (int) $from['cfg']['tracks'], 12);
$r = $engine->generate($from['cfg']);
ok('and it still generates a real sheet', count($r['lines']) > 0, true);
ok('reaching the build it was asked for',
   (int) $r['variant_id'], 10);

echo "\nUNKNOWN BUILD\n";
ok('an id that does not exist returns null',
   $master->config_for_variant(9999, $defaults), null);

echo "\n" . str_repeat('-', 96) . "\n";
echo $fail === 0
    ? "\033[32m\033[1m  ALL {$pass} ASSERTIONS PASSED\033[0m\n"
    : "\033[31m\033[1m  {$fail} FAILED, {$pass} passed\033[0m\n";
echo str_repeat('-', 96) . "\n\n";

exit($fail === 0 ? 0 : 1);
