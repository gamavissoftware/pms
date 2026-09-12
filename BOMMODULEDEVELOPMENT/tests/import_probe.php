<?php
/**
 * import_probe.php
 *
 * Drives the REAL Abom_import_model against REAL spreadsheets written
 * for the test, and — the part that matters — against the actual
 * reference BOMs already in the seed.
 *
 * WHY THE ROUND TRIP IS THE REAL TEST
 * The importer's job is to turn a released DF sheet back into master
 * data. So the strongest possible check is: take a build the module
 * already has, write it out as the spreadsheet it came from, import
 * that, and confirm the importer independently arrives at the SAME
 * quantity formula the seed was hand-authored with.
 *
 * A formula guessed wrong is invisible — the build is right for the
 * machine it came from and silently wrong for every other size. Nothing
 * errors, nothing looks odd, and a panel is wired short.
 *
 *   php BOMMODULEDEVELOPMENT/tests/import_probe.php
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

// PHPExcel is PHP 5-era and this harness runs on 8.x; production is
// 7.4 where none of these fire. Silenced so the assertions are
// readable — the library's own deprecations are not what is under test.
error_reporting(E_ALL & ~E_DEPRECATED);

$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);
$GLOBALS['ABOM_SEED']    = $seed;

if (!class_exists('CI_Model')) { class CI_Model {} }

require $root . '/application/models/Abom_import_model.php';

#[AllowDynamicProperties]
class Abom_import_model_probe extends Abom_import_model {}

/**
 * parse() calls $this->load->library('Excel') to pull PHPExcel in. The
 * stub loader has model() but not library(), so it gets one here rather
 * than in stubs.php — this is the only harness that reads a spreadsheet,
 * and PHPExcel is loaded once for the whole process.
 */
class Abom_import_loader
{
    private $inner;
    private $root;
    public function __construct($inner, $root) { $this->inner = $inner; $this->root = $root; }
    public function model($n, $a = '') { return $this->inner->model($n, $a); }
    public function library($name, $params = null, $alias = null)
    {
        if (strtolower($name) === 'excel' && !class_exists('PHPExcel_IOFactory')) {
            require_once $this->root . '/application/third_party/PHPExcel.php';
        }
    }
}

/**
 * existing_index() asks the live catalogue what ERP codes and part
 * numbers are already taken, so a clash is reported at preview time.
 * Served here from the same seed the live install runs, so the clash
 * warnings are asserted against real master data rather than invented
 * rows.
 */
class Seed_catalogue_db
{
    private $rows;
    public function __construct(Abom_seed_parser $seed)
    {
        $variants = array();
        foreach ($seed->rows('abom_variant') as $v) {
            $variants[(int) $v->id] = (string) $v->code;
        }

        $this->rows = array();
        foreach ($seed->rows('abom_item') as $i) {
            if ((int) $i->is_active !== 1) { continue; }
            $r = new stdClass();
            $r->erp_code    = $i->erp_code;
            $r->part_no     = $i->part_no;
            $r->description = $i->description;
            $vid = (int) $i->variant_id;
            $r->variant = isset($variants[$vid]) ? $variants[$vid] : null;
            $this->rows[] = $r;
        }
    }
    public function select() { return $this; }
    public function from()   { return $this; }
    public function join()   { return $this; }
    public function where()  { return $this; }
    public function get()    { return $this; }
    public function result() { return $this->rows; }
}

$model = (new ReflectionClass('Abom_import_model_probe'))->newInstanceWithoutConstructor();
Closure::bind(function ($ci, $root) {
    $this->load = new Abom_import_loader($ci->load, $root);
    $this->Abom_master_model = $ci->Abom_master_model;
    $this->db = new Seed_catalogue_db($GLOBALS['ABOM_SEED']);
}, $model, 'Abom_import_model')($GLOBALS['ABOM_STUB_CI'], $root);

$pass = 0; $fail = 0;
function ok($label, $got, $want)
{
    global $pass, $fail;
    $good = ($got === $want);
    $good ? $pass++ : $fail++;
    printf("  %s  %-58s %s\n",
        $good ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m", $label,
        $good ? '' : ('got ' . var_export($got, true) . ', want ' . var_export($want, true)));
}

/** The reference machine for a build, as abom_012 recorded it. */
function machine($seed, $code)
{
    foreach ($seed->rows('abom_variant') as $v) {
        if ($v->code === $code) {
            return array(
                'axes' => (int) $v->ref_axes, 'tracks' => (int) $v->ref_tracks,
                'speed_ppm' => (int) $v->ref_speed_ppm,
                'j4_units' => (int) $v->default_j4_units,
                'battery_qty' => (int) $v->default_battery,
                'variant_id' => (int) $v->id,
            );
        }
    }
    return null;
}

echo "\nREFERENCE-BOM IMPORT\n" . str_repeat('=', 86) . "\n";

// =====================================================================
// THE ROUND TRIP
// =====================================================================
echo "\nROUND TRIP — can the importer re-derive what the seed was hand-authored with?\n\n";

$builds = array('FX5-1808', 'FX5-1858', 'IQR-HS', 'IQR-TCF', 'FX5-1778', 'IQR-FLM');

$total = 0; $agreed = 0; $flagged = 0;
$wrong = array();

printf("  %-10s %6s %8s %9s %9s  %s\n",
    'BUILD', 'ITEMS', 'AGREED', 'ASKED', 'DISAGREED', 'MACHINE');
echo '  ' . str_repeat('-', 80) . "\n";

foreach ($builds as $code) {
    $m = machine($seed, $code);
    if (!$m) { continue; }

    $b_total = 0; $b_agree = 0; $b_ask = 0; $b_wrong = 0;

    foreach ($seed->rows('abom_item') as $it) {
        if ((int) $it->variant_id !== $m['variant_id'] || (int) $it->is_active !== 1) {
            continue;
        }

        // MANUAL means "an engineer decides every time" — there is no
        // rule for the importer to find, so it is out of scope.
        if ($it->formula_code === 'MANUAL') { continue; }

        // What the sheet for this reference machine would have shown.
        $qty = abom_expected_qty($it->formula_code, (int) $it->base_qty, $m);
        if ($qty === null || $qty < 1) { continue; }

        $b_total++; $total++;

        $inf = $model->infer_formula($qty, $it->description, $it->part_no, $m);

        if ($inf['formula'] === $it->formula_code) {
            $b_agree++; $agreed++;
        } elseif (!$inf['confident']) {
            // Not agreed, but the importer said so and asked. That is a
            // safe outcome: the screen puts it in front of a person.
            $b_ask++; $flagged++;
        } else {
            $b_wrong++;
            $wrong[] = sprintf('%s row "%s" qty %d: said %s, seed says %s — %s',
                $code, $it->description, $qty, $inf['formula'], $it->formula_code, $inf['reason']);
        }
    }

    printf("  %-10s %6d %8d %9d %9d  %dA/%dT\n",
        $code, $b_total, $b_agree, $b_ask, $b_wrong, $m['axes'], $m['tracks']);
}

/** What a formula produces for a given machine. Mirrors Abom_engine. */
function abom_expected_qty($formula, $base, array $m)
{
    switch ($formula) {
        case 'FIXED':        return $base;
        case 'AXES':         return $m['axes'];
        case 'AXES_MINUS_1': return $m['axes'] - 1;
        case 'TRACKS':       return $m['tracks'];
        case 'TRACK_TEMP':   return (int) ceil(((($m['tracks'] + 1) * 2) + 2) / 4);
        case 'J4_STO':       return $m['j4_units'];
        case 'BATTERY':      return $m['battery_qty'];
    }
    return null;
}

echo "\n";
printf("  %d rows across %d builds: %d agreed, %d asked, %d silently disagreed\n\n",
    $total, count($builds), $agreed, $flagged, count($wrong));

ok('the importer NEVER silently contradicts the seed', count($wrong), 0);

if (!empty($wrong)) {
    echo "\n  Silent disagreements — each one would be an invisible defect:\n";
    foreach ($wrong as $w) { echo "    - " . $w . "\n"; }
    echo "\n";
}

ok('and it agrees outright on most rows', $agreed > ($total * 0.6), true);
ok('every remaining row was flagged for a person, not guessed',
   $agreed + $flagged, $total);

// =====================================================================
// THE RULES INDIVIDUALLY
// =====================================================================
echo "\nTHE PART DECIDES, NOT THE NUMBER\n";

$m6 = array('axes' => 6, 'tracks' => 12, 'speed_ppm' => 100,
            'j4_units' => 0, 'battery_qty' => 6);

$t = $model->infer_formula(7, '4 CH. TEMPERATURE CARD', 'FX5-4LC', $m6);
ok('a temperature card is track-driven', $t['formula'], 'TRACK_TEMP');
ok('  and says so with confidence', $t['confident'], true);

// The trap: on THIS machine, qty 6 matches axes AND battery. Only the
// description separates them.
$b = $model->infer_formula(6, 'SERVO BATTERY', 'MR-BAT6V1SET', $m6);
ok('a battery is the battery count, not the axis count', $b['formula'], 'BATTERY');

$s = $model->infer_formula(5, 'SSCNET III CABLE 0.5 M', 'MR-J3BUS05M', $m6);
ok('SSCNET links are one fewer than the axes', $s['formula'], 'AXES_MINUS_1');

$a = $model->infer_formula(6, 'SERVO MOTOR 0.75 KW', 'HG-SN52J', $m6);
ok('a servo motor is one per axis', $a['formula'], 'AXES');

$f = $model->infer_formula(1, 'MAIN BASE UNIT, 12 SLOT', 'R312B', $m6);
ok('a base unit is a fixed quantity', $f['formula'], 'FIXED');

echo "\nAMBIGUITY IS ASKED ABOUT, NEVER GUESSED\n";
// qty 6 with 6 axes and 6 batteries, and a description that says
// nothing. Two rules fit. The importer must not pick one.
$amb = $model->infer_formula(6, 'TERMINAL BLOCK', 'TB-6', $m6);
ok('an ambiguous row is not confident', $amb['confident'], false);
ok('  it offers every candidate', count($amb['candidates']) > 2, true);
ok('  and defaults to FIXED, the only safe choice',
   $amb['formula'], 'FIXED');
ok('  because FIXED cannot be wrong on a different machine size',
   in_array('FIXED', $amb['candidates'], true), true);

echo "\nA SHEET THAT DISAGREES WITH ITS OWN MACHINE\n";
// Unmistakably a temperature card, but 9 is not what 12 tracks gives.
// Either the machine above is wrong or the quantity is. Neither can be
// assumed.
$bad = $model->infer_formula(9, '4 CH. TEMPERATURE CARD', 'FX5-4LC', $m6);
ok('is refused confidence', $bad['confident'], false);
ok('  and the reason names both numbers',
   strpos($bad['reason'], '7') !== false && strpos($bad['reason'], '9') !== false, true);

echo "\nNO MATCH AT ALL IS A CONFIDENT FIXED\n";
$odd = $model->infer_formula(37, 'CABLE TIE PACK', 'CT-100', $m6);
ok('37 matches no rule here', $odd['formula'], 'FIXED');
ok('  and that is a confident answer', $odd['confident'], true);

echo "\nZERO-VALUED RULES ARE NEVER OFFERED\n";
// j4_units is 0 on an MR-JE machine. A qty of 0 is impossible, but a
// rule that produces 0 must not be matchable either.
$je = $model->infer_formula(1, 'CONVERTER MODULE', 'FX5-CNV-BC', $m6);
ok('J4_STO is not offered when the machine has no MR-J4 units',
   in_array('J4_STO', $je['candidates'], true), false);

// =====================================================================
// PARSING A REAL FILE
// =====================================================================
echo "\nPARSING — a CSV written the way a released DF is laid out\n";

$dir = sys_get_temp_dir() . '/abom_import_probe';
@mkdir($dir, 0775, true);

/** Writes a CSV and returns its path. */
function sheet($dir, $name, array $rows)
{
    $path = $dir . '/' . $name;
    $fh = fopen($path, 'w');
    foreach ($rows as $r) { fputcsv($fh, $r); }
    fclose($fh);

    return $path;
}

$HEAD = array('S.NO.', 'ERP CODE', 'DESCRIPTION', 'MODEL NO. / PART NO.',
              'MANUFACTURER', 'QTY.', 'UOM', 'REMARKS', 'STATUS');

// A title block above the headings, section headings between groups,
// a blank spacer row — exactly what a real DF carries.
$normal = sheet($dir, 'normal.csv', array(
    array('SHUBHAM PACKAGING', '', '', '', '', '', '', '', ''),
    array('AUTOMATION BOM — DF-1808', '', '', '', '', '', '', '', ''),
    array('', '', '', '', '', '', '', '', ''),
    $HEAD,
    array('', '', "PLC, I/O's, HMI, RTD, VFD", '', '', '', '', '', ''),
    array('1', '4140023', 'PLC CPU MODULE', 'R04ENCPU', 'MITSUBISHI', '1', 'NO(S)', '', ''),
    array('2', '2020122', '4 CH. TEMPERATURE CARD', 'FX5-4LC', 'MITSUBISHI', '7', 'NO(S)', '', ''),
    array('', '', '', '', '', '', '', '', ''),
    array('', '', 'Servo Amplifiers & Motors', '', '', '', '', '', ''),
    array('3', '4060524', 'SERVO MOTOR 0.75 KW', 'HG-SN52J', 'MITSUBISHI', '6', 'NO(S)', '', ''),
    array('4', '', 'SERVO BATTERY', 'MR-BAT6V1SET', 'MITSUBISHI', '6', 'NO(S)', 'ERP pending', 'REVIEW'),
));

$p = $model->parse($normal);
ok('the file parses', $p['ok'], true);
ok('the heading row is found below the title block', $p['header_row'], 4);
ok('only real data rows are returned', count($p['rows']), 4);
ok('section headings are not rows', (bool) preg_grep('/^Servo/',
   array_column($p['rows'], 'description')), false);
ok('the first two rows carry their section',
   $p['rows'][0]['section_text'], "PLC, I/O's, HMI, RTD, VFD");
ok('and the last two carry theirs',
   $p['rows'][3]['section_text'], 'Servo Amplifiers & Motors');
ok('the blank spacer row is skipped', $p['rows'][2]['description'], 'SERVO MOTOR 0.75 KW');
ok('excel row numbers are 1-based, as the operator sees them',
   $p['rows'][0]['excel_row'], 6);

echo "\n  header aliases\n";
$alias = sheet($dir, 'alias.csv', array(
    array('Sr No', 'Item Code', 'Item Description', 'Part Number', 'Make', 'Quantity', 'Unit', 'Note'),
    array('1', 'X1', 'PLC CPU MODULE', 'R04ENCPU', 'MITSUBISHI', '1', 'NOS', ''),
));
$pa = $model->parse($alias);
ok('a differently-worded heading row still parses', $pa['ok'], true);
ok('  and maps every column', count($pa['rows']), 1);
ok('  including the make column', $pa['rows'][0]['manufacturer'], 'MITSUBISHI');

echo "\n  files that must be REFUSED, clearly\n";
$noheads = sheet($dir, 'noheads.csv', array(
    array('some', 'random', 'export'),
    array('1', '2', '3'),
));
$pn = $model->parse($noheads);
ok('a sheet with no heading row is refused', $pn['ok'], false);
ok('  and the message says what is needed',
   strpos($pn['error'], 'DESCRIPTION') !== false, true);

$partial = sheet($dir, 'partial.csv', array(
    array('S.NO.', 'ERP CODE', 'DESCRIPTION'),
    array('1', '4140023', 'PLC CPU MODULE'),
));
$pp = $model->parse($partial);
ok('a sheet missing QTY is refused', $pp['ok'], false);
ok('  and names the missing columns',
   strpos($pp['error'], 'QTY') !== false && strpos($pp['error'], 'PART NO') !== false, true);

$headonly = sheet($dir, 'headonly.csv', array($HEAD));
$ph = $model->parse($headonly);
ok('headings with no data beneath are refused', $ph['ok'], false);

ok('a file that is not a spreadsheet at all is refused',
   $model->parse($dir . '/does-not-exist.csv')['ok'], false);

// =====================================================================
// ANALYSIS — the full pipeline over the parsed rows
// =====================================================================
echo "\nANALYSIS\n";

$meta = array(
    'plc_family_id' => 1, 'machine_model' => 'SPM1200L',
    'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100,
    'j4_units' => 0, 'battery_qty' => 6,
);

$a = $model->analyse($p['rows'], $meta);

ok('every row is analysed', count($a['items']), 4);
ok('all four can be imported', $a['stats']['ok'], 4);
ok('none is blocked', $a['stats']['errors'], 0);

$by = array();
foreach ($a['items'] as $it) { $by[$it['description']] = $it; }

ok('the temperature card is track-driven',
   $by['4 CH. TEMPERATURE CARD']['formula'], 'TRACK_TEMP');
ok('the servo motor is per-axis',
   $by['SERVO MOTOR 0.75 KW']['formula'], 'AXES');
ok('the battery is the battery count',
   $by['SERVO BATTERY']['formula'], 'BATTERY');
ok('the CPU module is fixed',
   $by['PLC CPU MODULE']['formula'], 'FIXED');

ok('a missing ERP code becomes no_erp, not an error',
   $by['SERVO BATTERY']['erp_code'], null);
ok('  and is flagged for engineering',
   $by['SERVO BATTERY']['severity'], 'no_erp');
ok('  and counted', $a['stats']['no_erp'], 1);

ok('sections are resolved to real ids',
   $by['PLC CPU MODULE']['section_id'] > 0, true);
ok('  and the two groups differ',
   $by['PLC CPU MODULE']['section_id'] !== $by['SERVO MOTOR 0.75 KW']['section_id'], true);

echo "\n  rows that must NOT be written\n";
$bad = sheet($dir, 'bad.csv', array(
    $HEAD,
    array('', '', "PLC, I/O's, HMI, RTD, VFD", '', '', '', '', '', ''),
    array('1', '4140023', '', 'R04ENCPU', 'MITSUBISHI', '1', 'NO(S)', '', ''),
    array('2', '4140024', 'NO PART NUMBER', '', 'MITSUBISHI', '1', 'NO(S)', '', ''),
    array('3', '4140025', 'FRACTIONAL QTY', 'X-1', 'MITSUBISHI', '2.5', 'NO(S)', '', ''),
    array('4', '4140026', 'ZERO QTY', 'X-2', 'MITSUBISHI', '0', 'NO(S)', '', ''),
    array('5', '4140027', 'GOOD ROW', 'X-3', 'MITSUBISHI', '1', 'NO(S)', '', ''),
));
$pb = $model->parse($bad);
$ab = $model->analyse($pb['rows'], $meta);

ok('four bad rows are blocked', $ab['stats']['errors'], 4);
ok('  and the good one still imports', $ab['stats']['ok'], 1);
$msgs = array();
foreach ($ab['items'] as $it) { $msgs = array_merge($msgs, $it['errors']); }
ok('  a missing description is named',
   (bool) preg_grep('/No description/', $msgs), true);
ok('  a missing part number is named',
   (bool) preg_grep('/No model . part number/', $msgs), true);
ok('  a fractional quantity is refused, not rounded',
   (bool) preg_grep('/not a whole number/', $msgs), true);
ok('  and a zero quantity is refused',
   (bool) preg_grep('/should.*be removed/', $msgs), true);

echo "\n  a manufacturer is defaulted, with a warning — never left blank\n";
$nom = sheet($dir, 'nomake.csv', array(
    $HEAD,
    array('', '', "PLC, I/O's, HMI, RTD, VFD", '', '', '', '', '', ''),
    array('1', '4140023', 'PLC CPU MODULE', 'R04ENCPU', '', '1', 'NO(S)', '', ''),
));
$an = $model->analyse($model->parse($nom)['rows'], $meta);
ok('defaults to MITSUBISHI', $an['items'][0]['manufacturer'], 'MITSUBISHI');
ok('  and says so', (bool) preg_grep('/recorded as MITSUBISHI/', $an['items'][0]['warnings']), true);

echo "\n  an unknown section heading is filed, not dropped\n";
$us = sheet($dir, 'unknownsec.csv', array(
    $HEAD,
    array('', '', 'PNEUMATICS AND VALVES', '', '', '', '', '', ''),
    array('1', '4140023', 'SOLENOID VALVE', 'SV-1', 'MITSUBISHI', '1', 'NO(S)', '', ''),
));
$au = $model->analyse($model->parse($us)['rows'], $meta);
ok('the row survives', $au['stats']['ok'], 1);
ok('  gets a real section', $au['items'][0]['section_id'] > 0, true);
ok('  and the operator is told', (bool) preg_grep('/is not one this module knows/',
   $au['items'][0]['warnings']), true);

echo "\n  duplicates inside one sheet are reported\n";
$dup = sheet($dir, 'dup.csv', array(
    $HEAD,
    array('', '', "PLC, I/O's, HMI, RTD, VFD", '', '', '', '', '', ''),
    array('1', '4140023', 'PLC CPU MODULE', 'R04ENCPU', 'MITSUBISHI', '1', 'NO(S)', '', ''),
    array('2', '4140023', 'A DIFFERENT PART', 'R04ENCPU-B', 'MITSUBISHI', '1', 'NO(S)', '', ''),
));
$ad = $model->analyse($model->parse($dup)['rows'], $meta);
ok('one ERP on two parts is warned about',
   (bool) preg_grep('/appears on rows/', $ad['warnings']), true);
ok('  but both rows still import', $ad['stats']['ok'], 2);

array_map('unlink', glob($dir . '/*.csv'));
@rmdir($dir);

echo "\n" . str_repeat('-', 86) . "\n";
echo $fail === 0
    ? "\033[32m\033[1m  ALL {$pass} ASSERTIONS PASSED\033[0m\n"
    : "\033[31m\033[1m  {$fail} FAILED, {$pass} passed\033[0m\n";
echo str_repeat('-', 86) . "\n\n";

exit($fail === 0 ? 0 : 1);
