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
 *   php BOMMODULEDEVELOPMENT/tests/render_view.php je  > out.html
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

$seed = new Abom_seed_parser($root . '/Database/abom_006_seed.sql');
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

// 'saved' renders the SAVED-BOM screen: configuration panel locked, but
// quantities and rows editable, which is what a draft BOM looks like.
$saved = isset($argv[2]) && ($argv[2] === 'saved' || $argv[2] === 'config');

// 'config' renders a SAVED, still-editable BOM — a draft, or a clone.
// Same screen as 'saved', but with the machine-configuration panel
// unlocked and the "Apply configuration" button in the topbar. This is
// the mode that has to prove the fields are genuinely typeable without
// the generator's Save BOM (which would fork the document) appearing.
$config_edit = isset($argv[2]) && $argv[2] === 'config';

// 'archived' renders a stored snapshot — a photograph of a document
// that no longer exists in this form. Every control that would act on a
// live BOM must be gone, EXPORT above all: a PDF printed from here
// would be indistinguishable from a current BOM once it left the
// screen, and somebody would order from it.
$archived = isset($argv[2]) && $argv[2] === 'archived';

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
            'plc_family_id' => null, 'variant_id' => null,
            'features' => $master->default_features(),
        ),
    ),
    'je' => array(
        'bom_no' => 'ABOM-REF-JE', 'df_ref' => 'DF-1808', 'revision' => '',
        'title' => 'DF-1808', 'summary' => 'SPM1200L · 6 Axis · 12 Track · 100 PPM',
        'panel' => 'FX5-JE · temperature cards = 7',
        'tags' => array(array('fx5','FX5'), array('int','Intermittent')),
        'cfg' => array(
            'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100,
            'motion_type' => 'Intermittent', 'machine_model' => 'SPM1200L',
            'machine_side' => 'N/A', 'j4_units' => 0, 'battery_qty' => 6,
            'plc_family_id' => null, 'variant_id' => null,
            'features' => $master->default_features(),
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
            'plc_family_id' => null, 'variant_id' => null,
            'features' => $master->default_features(),
        ),
    ),
);

$preset = isset($presets[$which]) ? $presets[$which] : $presets['iqr'];
$cfg    = $preset['cfg'];
$result = $engine->generate($cfg);

$bom = new stdClass();
$bom->id                = $saved ? 1 : 0;
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

$defaults = $master->variant_defaults($result['variant_id'], $result['family_id']);

$bom->variant_id     = $result['variant_id'];
$bom->variant_locked = 0;

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

// The topbar chip row was removed — every value it carried is
// stated in the document title strip, the sidebar or the sign-off
// block instead. Nothing here builds chips any more.

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
    'config_editable'    => $config_edit,
    'archived'           => $archived,
    'notice'             => $archived
        ? '<b>Archived version.</b> This is a record, not the current document.'
        : '',
    'family_code'        => $result['family']['code'],
    'family_explanation' => $result['family']['explanation'],
    'overridden'         => !empty($result['overridden']),
    'variant'            => $result['variant'],
    'variant_explanation'=> !empty($result['variant_detected'])
                                ? $result['variant_detected']['explanation'] : '',
    'variant_overridden' => !empty($result['variant_overridden']),
    'variant_missing'    => !empty($result['variant_missing']),
    'variant_message'    => '',
    'variants'           => $master->get_active_variants(),
    'panel_location'     => $defaults['panel_location'],
    'families'           => $master->get_families(),
    'features'           => $master->get_features(),
    'active_features'    => $cfg['features'],
    'errors'             => array(),
    // The sign-off block, which carries the ONLY export buttons on the
    // page now that the topbar's CSV-only one is gone. It was absent
    // from this harness, so the whole workflow partial silently
    // early-returned and nothing here rendered the exports at all.
    'workflow'           => array(
        'enabled'      => $saved && !$archived,
        'configured'   => true,
        'problems'     => array(),
        'next'         => array('to' => 'submitted', 'label' => 'Submit for checking',
                                'permission' => 'save'),
        'may'          => true,
        'blockers'     => array(),
        'trail'        => array(),
        'can_reject'   => false,
        'can_reopen'   => false,
        'can_revise'   => false,
        'status_label' => 'Draft',
    ),
    'notes'              => $notes,
    'qty_editable'       => ($editable || $saved) && !$archived,
    'rows_editable'      => ($editable || $saved) && !$archived,
    'qty_locked_reason'  => $editable ? '' : 'Quantity editing is disabled until the save path is in place, so that no markup can be lost on refresh.',
    'presets'            => $presets,
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
