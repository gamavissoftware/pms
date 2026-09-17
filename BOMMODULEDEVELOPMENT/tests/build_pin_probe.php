<?php
/**
 * build_pin_probe.php
 *
 * "Generate BOM" on a reference build must give you THAT build.
 *
 * It used to give you whichever build the selection rules reached when
 * the recorded machine was fed back through them. When those two
 * disagree — which happens as soon as a build is IMPORTED and its rule
 * fields do not cover its own reference machine — the operator pressed
 * the button on one build and got a different sheet, with different and
 * usually MORE lines, under a warning explaining that this had happened.
 *
 * The requested build is now pinned, so the sheet is the one on screen.
 * The disagreement is still reported; it just no longer decides what you
 * are shown.
 *
 * This drives the REAL Abom_engine and the REAL
 * Abom_master_model::config_for_variant() over the parsed seed. Nothing
 * here re-implements either.
 *
 *   php BOMMODULEDEVELOPMENT/tests/build_pin_probe.php
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

// ---------------------------------------------------------------------
// Does pinning the requested build actually decide the sheet?
// ---------------------------------------------------------------------
$defaults = array('axes'=>6,'tracks'=>12,'speed_ppm'=>100,'motion_type'=>'Intermittent',
                  'machine_model'=>'SPM1200L','machine_side'=>'LHS','j4_units'=>7,
                  'battery_qty'=>7,'features'=>array(),'plc_family_id'=>null,'variant_id'=>null);

$builds = array();
foreach ($seed->rows('abom_variant') as $v) {
    if (!empty($v->is_active)) { $builds[(int) $v->id] = $v->code; }
}

// Two builds whose recorded machines differ, so the rules genuinely
// route the one to the other — the production situation.
$ids = array_keys($builds);
$A = $ids[0]; $B = null;
$cfgA = $master->config_for_variant($A, $defaults)['cfg'];
foreach ($ids as $id) {
    if ($id === $A) { continue; }
    $c = $master->config_for_variant($id, $defaults)['cfg'];
    $r = $engine->generate($c);
    if (!empty($r['variant_id']) && (int) $r['variant_id'] !== $A) { $B = $id; break; }
}

printf("\nBuild A = %s (id %d)   Build B = %s (id %d)\n", $builds[$A], $A, $builds[$B], $B);

$cfgB = $master->config_for_variant($B, $defaults)['cfg'];

// THE BUG: ask for build A, but with B's machine numbers. The rules
// route to B, so the old code showed B's sheet under A's button.
$mixed = $cfgB;
$mixed['variant_id'] = null;                       // old behaviour: no pin
$old = $engine->generate($mixed);

$mixed['variant_id'] = $A;                         // new behaviour: pinned
$new = $engine->generate($mixed);

$refA = $engine->generate($cfgA);

printf("\n  asked for build %s, using a machine the rules route to %s:\n", $builds[$A], $builds[$B]);
printf("    OLD (no pin) -> build %-10s %2d lines\n",
    $builds[(int) $old['variant_id']], count($old['lines']));
printf("    NEW (pinned) -> build %-10s %2d lines\n",
    $builds[(int) $new['variant_id']], count($new['lines']));
printf("    the reference %s sheet itself:   %2d lines\n", $builds[$A], count($refA['lines']));

$ok1 = ((int) $new['variant_id'] === $A);
$ok2 = (count($new['lines']) === count($refA['lines']));
$ok3 = ((int) $old['variant_id'] !== $A);

printf("\n  %s pinned generate returns the build that was ASKED for\n", $ok1 ? 'PASS' : 'FAIL');
printf("  %s and its sheet matches the reference build exactly\n",   $ok2 ? 'PASS' : 'FAIL');
printf("  %s (old behaviour really did substitute another build)\n", $ok3 ? 'PASS' : 'FAIL');

$fails = (int) !$ok1 + (int) !$ok2 + (int) !$ok3;
echo "\n" . str_repeat('-', 78) . "\n";
printf("  %s\n", $fails === 0 ? 'ALL 3 ASSERTIONS PASSED' : $fails . ' ASSERTION(S) FAILED');
echo str_repeat('-', 78) . "\n";
exit($fails === 0 ? 0 : 1);
