<?php
/**
 * case_audit.php
 *
 * Development is on case-insensitive macOS; production is case-sensitive
 * Linux. A load->view('Abom/view') that works here 404s there. This
 * checks every loader call and asset path in the module against its
 * on-disk name, character for character.
 *
 *   php BOMMODULEDEVELOPMENT/tests/case_audit.php
 *
 * Exit code 0 = clean, 1 = at least one mismatch.
 *
 * PHP 7.4 compatible.
 */

chdir(dirname(dirname(__DIR__)));
$files = array_merge(
  glob("application/controllers/Abom.php"),
  glob("application/models/Abom_*.php"),
  glob("application/libraries/Abom_engine.php"),
  glob("application/views/abom/*.php")
);
$calls = array();
foreach ($files as $f) {
  $src = file_get_contents($f);
  if (preg_match_all('/load->(view|model|library|helper|config)\(\s*[\'"]([^\'"]+)[\'"]/', $src, $m, PREG_SET_ORDER)) {
    foreach ($m as $x) { $calls[$x[1].'|'.$x[2]] = true; }
  }
}
ksort($calls);
$dirs = array("view"=>"application/views/","model"=>"application/models/","library"=>"application/libraries/","helper"=>"application/helpers/","config"=>"application/config/");
$bad = 0;
foreach (array_keys($calls) as $key) {
  list($kind,$name) = explode('|', $key, 2);
  $rel = $dirs[$kind] . $name;
  if (substr($rel,-4) !== '.php') { $rel .= '.php'; }
  $dir = dirname($rel); $base = basename($rel);
  $entries = is_dir($dir) ? scandir($dir) : array();
  $exact = in_array($base, $entries, true);
  $ci = null;
  foreach ($entries as $e) { if (strcasecmp($e,$base)===0) { $ci = $e; break; } }
  if ($exact) { printf("  OK          %-8s %-30s -> %s\n", $kind, $name, $rel); }
  else { $bad++; printf("  ** CASE **  %-8s %-30s -> want %s, on disk %s\n", $kind, $name, $base, $ci ? $ci : 'MISSING'); }
}
// Asset paths referenced from the module's views.
$src = '';
foreach (glob('application/views/abom/*.php') as $f) { $src .= file_get_contents($f); }
if (preg_match_all('#assets_url; \?>([A-Za-z0-9_./-]+)#', $src, $m)) {
    $seen = array_unique($m[1]);
    sort($seen);
    foreach ($seen as $rel) {
        $path = 'assets/' . $rel;
        $dir = dirname($path); $base = basename($path);
        $entries = is_dir($dir) ? scandir($dir) : array();
        if (in_array($base, $entries, true)) {
            printf("  OK          %-8s %-30s -> %s\n", 'asset', $rel, $path);
        } else {
            $bad++;
            $ci = null;
            foreach ($entries as $e) { if (strcasecmp($e, $base) === 0) { $ci = $e; break; } }
            printf("  ** CASE **  %-8s %-30s -> want %s, on disk %s\n", 'asset', $rel, $base, $ci ? $ci : 'MISSING');
        }
    }
}

echo "\n  " . ($bad === 0
    ? "All loader calls and asset paths match their on-disk names character for character.\n"
    : "$bad MISMATCH(ES)\n");

exit($bad === 0 ? 0 : 1);
