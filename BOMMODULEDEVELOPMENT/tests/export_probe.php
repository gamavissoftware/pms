<?php
/**
 * export_probe.php — drives the REAL Abom_exporter end to end.
 *
 * Generates CSV, XLSX and PDF from a live engine result, using the
 * project's own PHPExcel and TCPDF, with no CodeIgniter bootstrap and no
 * database.
 *
 *   php BOMMODULEDEVELOPMENT/tests/export_probe.php [outdir]
 *
 * WHY THIS EXISTS
 * ---------------
 * The three export paths were never executed by any test — they were
 * read and reasoned about. Running them found two things reading did
 * not:
 *
 *   1. PHPExcel's Excel2007 autofilter writer contains
 *      `if (count($columns > 0))` (Worksheet.php:771), with the
 *      comparison inside count(). Warning on PHP 7.4, FATAL on PHP 8 —
 *      so the XLSX export would have died on the day of a PHP upgrade.
 *   2. The autofilter range was built after the sign-off block was
 *      written, so it covered the signature rows as though they were
 *      BOM items.
 *
 * The BOM it builds deliberately carries an edited quantity, a typed
 * remark and a hand-added row, because those are the paths that carry no
 * master data behind them.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

require $root.'/BOMMODULEDEVELOPMENT/tests/seed_parser.php';
require $root.'/BOMMODULEDEVELOPMENT/tests/stubs.php';

defined('BASEPATH') OR define('BASEPATH', true);
defined('APPPATH')  OR define('APPPATH',  $root.'/application/');
defined('FCPATH')   OR define('FCPATH',   $root.'/');
defined('assets_url') OR define('assets_url', '/assets/');
defined('page_url')   OR define('page_url', '/index.php/');

$seed = new Abom_seed_parser($root.'/Database/abom_006_seed.sql');
$GLOBALS['ABOM_STUB_CI'] = new Abom_stub_ci($seed);

require $root.'/application/libraries/Abom_engine.php';
require $root.'/application/helpers/abom_helper.php';

// The stub loader must actually load Excel and Pdf for the exporter.
class Abom_export_loader {
    public function helper($n) {}
    public function model($n, $alias = '') {}
    public function library($n) {
        $n = strtolower($n);
        if ($n === 'excel') {
            require_once dirname(dirname(__DIR__)).'/application/libraries/Excel.php';
            // CI assigns the instance onto the super-object; do the same.
            $GLOBALS['ABOM_STUB_CI']->excel = new Excel();
        }
        if ($n === 'pdf') { require_once dirname(dirname(__DIR__)).'/application/libraries/Pdf.php'; }
    }
}
$GLOBALS['ABOM_STUB_CI']->load = new Abom_export_loader();

require $root.'/application/libraries/Abom_exporter.php';

$engine = new Abom_engine();
$master = $GLOBALS['ABOM_STUB_CI']->Abom_master_model;
$f = $master->default_features();

$cfg = array('axes'=>6,'tracks'=>12,'speed_ppm'=>100,'motion_type'=>'Intermittent',
  'machine_model'=>'SPM1200L','machine_side'=>'N/A','j4_units'=>0,'battery_qty'=>6,
  'plc_family_id'=>null,'variant_id'=>null,'features'=>$f);

$res   = $engine->generate($cfg);
$lines = $res['lines'];

// Simulate what a saved BOM looks like after the engineer edits it:
// a typed remark, a quantity override, and one hand-added row.
$lines[3]->user_remark  = 'Confirmed with Mitsubishi 11-08';
$lines[7]->qty          = 4;
$lines[7]->is_overridden = 1;

$manual = clone $lines[5];
$manual->erp_code      = null;
$manual->description   = 'CUSTOM ADD-ON BRACKET, FABRICATED';
$manual->part_no       = 'SP-BRK-1200L';
$manual->manufacturer  = 'SHUBHAM';
$manual->qty           = 2;
$manual->computed_qty  = 2;
$manual->is_manual_add = 1;
$manual->is_overridden = 0;
$manual->user_remark   = 'Site fit — see sketch';
$manual->usage_remark  = null;
$manual->issue_severity= 'no_erp';
$manual->row_class     = $engine->row_class($manual);
$manual->status_badges = $engine->status_badges($manual);
$manual->is_optional   = 0;
array_splice($lines, 6, 0, array($manual));

$n = 0; foreach ($lines as $l) { $l->line_no = ++$n; }

$bom = new stdClass();
$bom->id = 1; $bom->bom_no = 'ABOM-5'; $bom->revision = '00'; $bom->df_ref = 'DF-1808';
$bom->machine_model='SPM1200L'; $bom->machine_side='N/A'; $bom->axes=6; $bom->tracks=12;
$bom->speed_ppm=100; $bom->motion_type='Intermittent'; $bom->status='draft';
$bom->variant_id = $res['variant_id'];

$ex = new Abom_exporter();
$out = isset($argv[1]) ? $argv[1] : sys_get_temp_dir().'/abom_exports';
@mkdir($out, 0777, true);

$csv = $ex->csv($bom, $lines);
file_put_contents($out.'/'.$csv['filename'], $csv['body']);
echo "CSV  : {$csv['filename']} (".strlen($csv['body'])." bytes)\n";

try {
  $x = $ex->xlsx($bom, $lines);
  copy($x['path'], $out.'/'.$x['filename']);
  echo "XLSX : {$x['filename']} (".filesize($x['path'])." bytes)\n";
} catch (Throwable $e) {
  echo "XLSX : FAILED — ".$e->getMessage()."\n";
  echo "        at ".$e->getFile().":".$e->getLine()."\n";
  foreach (array_slice($e->getTrace(),0,4) as $t) {
    echo "        # ".(isset($t['file'])?basename($t['file']):'?').":".(isset($t['line'])?$t['line']:'?')
       ." ".(isset($t['class'])?$t['class'].'::':'').$t['function']."\n";
  }
}

try {
  $p = $ex->pdf($bom, $lines);
  file_put_contents($out.'/'.$p['filename'], $p['body']);
  echo "PDF  : {$p['filename']} (".strlen($p['body'])." bytes)\n";
} catch (Throwable $e) { echo "PDF  : FAILED — ".$e->getMessage()."\n"; }
