<?php
/**
 * render_view.php
 *
 * Renders the module's read-only document view (the .abom-wrap block of
 * application/views/abom/view.php) with no CodeIgniter bootstrap and no
 * database, so the output can be diffed against the approved design
 * document.
 *
 * It loads the REAL view partials and the REAL helper — nothing is
 * re-implemented here. Only the CI loader, config and constants are
 * stubbed.
 *
 *   php BOMMODULEDEVELOPMENT/tests/render_view.php iqr > out.html
 *   php BOMMODULEDEVELOPMENT/tests/render_view.php fx5 > out.html
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

require __DIR__ . '/seed_parser.php';
require __DIR__ . '/stubs.php';

defined('BASEPATH') OR define('BASEPATH', true);

defined('sitetitle')  OR define('sitetitle', 'SHUBHAM PACKAGING PMS');
defined('assets_url') OR define('assets_url', '/assets/');
defined('page_url')   OR define('page_url', '/index.php/');

$seed = new Abom_seed_parser($root . '/BOMMODULEDEVELOPMENT/abom_seed.sql');
$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);

require $root . '/application/libraries/Abom_engine.php';
require $root . '/application/helpers/abom_helper.php';

/**
 * Stands in for CI_Loader inside a view. $this->load->view(...) works
 * because $load points back at this object, and cached vars carry down
 * into nested partials exactly as CI does it.
 */
class Abom_render_loader
{
    public $load;
    public $vars = array();
    private $root;

    public function __construct($root)
    {
        $this->load = $this;
        $this->root = $root;
    }

    public function view($name, $data = array())
    {
        if (!empty($data)) {
            $this->vars = array_merge($this->vars, $data);
        }

        $file = $this->root . '/application/views/' . $name . '.php';
        if (!is_readable($file)) {
            throw new RuntimeException('View not found: ' . $file);
        }

        extract($this->vars);
        include $file;
    }
}

// ---------------------------------------------------------------------

$engine = new Abom_engine();
$master = $GLOBALS['ABOM_STUB_CI']->Abom_master_model;

$which    = isset($argv[1]) ? strtolower($argv[1]) : 'iqr';
$editable = isset($argv[2]) && $argv[2] === 'generate';

$presets = array(
    'fx5' => array(
        'bom_no' => 'ABOM-REF-FX5', 'df_ref' => 'DF-1827', 'revision' => '',
        'title' => 'DF-1827', 'summary' => 'SPM1200L · 8 Axis · 12 Track · 140 PPM · LHS',
        'panel' => 'Panel With Machine · expect 29 line items',
        'tags' => array(array('fx5','FX5'), array('int','Intermittent')),
        'cfg' => array(
            'axes' => 8, 'tracks' => 12, 'speed_ppm' => 140,
            'motion_type' => 'Intermittent', 'machine_model' => 'SPM1200L',
            'machine_side' => 'LHS', 'j4_units' => 7, 'battery_qty' => 7,
            'plc_family_id' => null, 'features' => $master->default_features(),
        ),
    ),
    'iqr' => array(
        'bom_no' => 'ABOM-REF-IQR', 'df_ref' => 'DF-1826', 'revision' => '02',
        'title' => 'DF-1826 · REV.02', 'summary' => 'SPM1200L · 15 Axis · 12 Track · 180 PPM',
        'panel' => 'Standalone Panel · expect 42 line items',
        'tags' => array(array('iqr','iQ-R'), array('cont','Continuous')),
        'cfg' => array(
            'axes' => 15, 'tracks' => 12, 'speed_ppm' => 180,
            'motion_type' => 'Continuous', 'machine_model' => 'SPM1200L',
            'machine_side' => 'N/A', 'j4_units' => 11, 'battery_qty' => 12,
            'plc_family_id' => null, 'features' => $master->default_features(),
        ),
    ),
);

$preset = isset($presets[$which]) ? $presets[$which] : $presets['iqr'];
$cfg    = $preset['cfg'];
$result = $engine->generate($cfg);

$bom = new stdClass();
$bom->id                = 0;
$bom->bom_no            = $preset['bom_no'];
$bom->revision          = $preset['revision'];
$bom->df_ref            = $preset['df_ref'];
$bom->machine_model     = $cfg['machine_model'];
$bom->machine_side      = $cfg['machine_side'];
$bom->axes              = $cfg['axes'];
$bom->tracks            = $cfg['tracks'];
$bom->speed_ppm         = $cfg['speed_ppm'];
$bom->motion_type       = $cfg['motion_type'];
$bom->plc_family_id     = $result['family_id'];
$bom->plc_family_locked = 0;
$bom->j4_units          = $cfg['j4_units'];
$bom->battery_qty       = $cfg['battery_qty'];
$bom->features_json     = json_encode($cfg['features']);
$bom->status            = 'draft';
$bom->created_at        = '2026-02-25 09:00:00';
foreach (array('prepared_by','prepared_at','checked_by','checked_at',
               'eng_approved_by','eng_approved_at',
               'proc_approved_by','proc_approved_at') as $k) {
    $bom->$k = null;
}

$defaults = $master->family_defaults($result['family_id']);

// Same "Points to verify" derivation the controller uses.
$notes = array();
$pending = array();
$conflict = array();
foreach ($result['lines'] as $line) {
    if (empty($line->erp_code)) {
        $pending[] = 'S.No ' . $line->line_no . ' — ' . $line->description . ' (' . $line->part_no . ')';
    }
    if ($line->issue_severity === 'conflict') {
        $conflict[$line->erp_code][] = 'S.No ' . $line->line_no . ' (' . $line->part_no . ')';
    }
}
if (!empty($pending)) {
    $notes[] = count($pending) . ' item' . (count($pending) === 1 ? '' : 's')
        . ' pending ERP code: ' . implode('; ', $pending) . '.';
}
foreach ($conflict as $erp => $where) {
    if (count($where) > 1) {
        $notes[] = 'ERP ' . $erp . ' appears on ' . count($where) . ' different parts: '
            . implode(' and ', $where) . ' — please verify.';
    }
}

$chips = array();
$chips[] = array('class' => '', 'label' => $bom->bom_no . ($bom->revision !== '' ? ' · REV.' . $bom->revision : ''));
$chips[] = array('class' => 'lite', 'label' => $bom->machine_model);
$chips[] = array('class' => 'lite', 'label' => $bom->axes . ' Axis');
$chips[] = array('class' => 'lite', 'label' => $bom->tracks . ' Track');
$chips[] = array('class' => 'lite', 'label' => $bom->speed_ppm . ' PPM');
if ($bom->machine_side !== '' && $bom->machine_side !== 'N/A') { $chips[] = array('class' => 'lite', 'label' => $bom->machine_side); }
$chips[] = array('class' => 'cont', 'label' => $bom->motion_type);
foreach ($master->get_features() as $code => $feature) {
    if (!empty($cfg['features'][$code])) { $chips[] = array('class' => 'cont', 'label' => $feature->label); }
}
$chips[] = array('class' => ($result['family']['code'] === 'FX5' ? 'fx5' : 'iqr'),
                 'label' => ($result['family']['code'] === 'FX5' ? 'FX5 Series' : 'iQ-R Series'));
if ($defaults['panel_location'] !== '') { $chips[] = array('class' => 'lite', 'label' => $defaults['panel_location']); }
$chips[] = array('class' => 'lite', 'label' => 'Draft');

foreach ($presets as $k => $pv) {
    $presets[$k]['cfg'] = $pv['cfg'];
}

$loader = new Abom_render_loader($root);

ob_start();
$loader->view('abom/_document', array(
    'bom'                => $bom,
    'lines'              => $result['lines'],
    'sections'           => $result['sections'],
    'stats'              => $result['stats'],
    'editable'           => $editable,
    'family_code'        => $result['family']['code'],
    'family_explanation' => $result['family']['explanation'],
    'overridden'         => !empty($result['overridden']),
    'panel_location'     => $defaults['panel_location'],
    'families'           => $master->get_families(),
    'features'           => $master->get_features(),
    'active_features'    => $cfg['features'],
    'errors'             => array(),
    'notes'              => $notes,
    'qty_editable'       => $editable,
    'qty_locked_reason'  => $editable ? '' : 'Quantity editing is disabled until the save path is in place, so that no markup can be lost on refresh.',
    'presets'            => $presets,
    'chips'              => $chips,
    'signoff_mode'       => 'customer',
    'signoff_names'      => array(),
    'models'             => array('SPM1200L', 'SPM1250P'),
    'sides'              => array('LHS', 'RHS', 'N/A'),
    'motion_types'       => array('Intermittent', 'Continuous'),
    'axes_min' => 1, 'axes_max' => 16,
    'tracks_min' => 1, 'tracks_max' => 24,
    'speed_min' => 40, 'speed_max' => 400,
));
$document = ob_get_clean();

$css       = file_get_contents($root . '/assets/abom/abom.css');
$print_css = file_get_contents($root . '/assets/abom/abom-print.css');

echo '<!DOCTYPE html>' . "\n";
echo '<html lang="en"><head><meta charset="UTF-8">' . "\n";
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
echo '<title>Automation BOM — ' . htmlspecialchars($bom->bom_no, ENT_QUOTES, 'UTF-8')
   . ' — rendered abom/view</title>' . "\n";
echo '<style>' . $css . "\n" . $print_css . '</style>' . "\n";
echo '<style>body{margin:0;background:#ECF0F5;}</style>' . "\n";
echo '</head><body>' . "\n";
echo '<div class="abom-wrap">' . "\n";
echo $document;
echo "\n" . '</div>' . "\n";
echo '</body></html>' . "\n";
