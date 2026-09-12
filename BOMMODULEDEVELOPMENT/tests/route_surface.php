<?php
/**
 * route_surface.php — confirm the route surface and the guard matrix agree.
 *
 * The matrix is exhaustive over the CONTROLLER. That only makes it
 * exhaustive over the ENTRY SURFACE if every route lands on a method in
 * it and, more importantly, no route reaches anything that is not.
 *
 * Both directions are checked:
 *   forward  — every abom route resolves to a public method
 *   reverse  — every reachable public method is in the matrix
 *
 * CodeIgniter's default routing also exposes /abom/<method> for any public
 * method whether or not a route line exists, so the reverse direction is
 * the one that matters.
 *
 * PHP 7.4 compatible.
 */

$root  = dirname(dirname(__DIR__));
$ctl   = $root . '/application/controllers/Abom.php';
$rfile = $root . '/application/config/routes.php';

$src = file_get_contents($ctl);
preg_match_all('/^\s*public function (\w+)\s*\(/m', $src, $m);
$public = $m[1];

$routes = array();
foreach (file($rfile) as $line) {
    if (preg_match("/^\\\$route\\['([^']*abom[^']*)'\\]\\s*=\\s*'([^']+)'/i", $line, $r)) {
        $routes[$r[1]] = $r[2];
    }
}

$fail = 0;

echo "\nROUTE SURFACE vs GUARD MATRIX\n" . str_repeat('=', 82) . "\n";
printf("  %d abom route line(s), %d public method(s)\n\n", count($routes), count($public));

echo "FORWARD — every route resolves to a public method on Abom\n";
foreach ($routes as $uri => $target) {
    $parts  = explode('/', $target);
    $class  = array_shift($parts);
    $method = count($parts) ? preg_replace('/\$\d+/', '', array_shift($parts)) : 'index';
    $method = $method === '' ? 'index' : $method;

    $ok = strcasecmp($class, 'Abom') === 0 && in_array($method, $public, true);
    if (!$ok) { $fail++; }
    printf("  %-6s %-34s -> %-22s %s\n", $ok ? 'ok' : 'FAIL', $uri, $target,
        $ok ? '' : '*** not a public method on Abom ***');
}

echo "\nREVERSE — every public method is accounted for in the matrix\n";
echo "  (CI default routing exposes /abom/<method> whether routed or not)\n";

/** Methods the matrix documents. Kept literal so a NEW method fails loudly. */
$matrix = array(
    '__construct', 'index', 'generate', 'generate_ajax', 'save', 'save_line_qty',
    'submit', 'approve', 'reject', 'reopen', 'create_revision', 'acknowledge_line',
    'delete_bom', 'duplicate', 'save_lines', 'save_config', 'check_df_ref', 'history', 'version',
    'import', 'import_preview', 'import_commit', 'import_template',
    'master', 'master_form', 'master_save', 'master_toggle',
    'config', 'config_list', 'config_form', 'config_save', 'config_delete',
    'master_bom', 'master_bom_save', 'master_bom_delete',
    'export', 'bom_list', 'view', 'reference', 'printable', 'guide',
);

foreach ($public as $method) {
    $in = in_array($method, $matrix, true);
    if (!$in) { $fail++; }
    $routed = false;
    foreach ($routes as $target) {
        if (preg_match('#^Abom/' . preg_quote($method, '#') . '\b#i', $target)) { $routed = true; }
    }

    // CI3 refuses any method whose name starts with an underscore, so the
    // constructor is not an entry point. Measured, not assumed:
    // /abom/__construct and /abom/_remap both answer 404.
    if ($method[0] === '_') {
        $how = 'NOT reachable — CI3 blocks _-prefixed methods (measured: 404)';
    } elseif ($routed) {
        // Note: an explicitly routed method is ALSO reachable under its own
        // name. /abom/print/4 and /abom/printable/4 both answer 200.
        $how = 'routed explicitly (and under its own name via default routing)';
    } else {
        $how = 'reachable via CI default routing only';
    }

    printf("  %-6s %-22s %s%s\n", $in ? 'ok' : 'FAIL', $method, $how,
        $in ? '' : '   *** NOT IN THE MATRIX ***');
}

$orphans = array_diff($matrix, $public);
if ($orphans) {
    echo "\nSTALE — in the matrix but no longer a public method:\n";
    foreach ($orphans as $o) { printf("  FAIL   %s\n", $o); $fail++; }
}

echo "\n" . str_repeat('=', 82) . "\n";
if ($fail === 0) {
    printf("  Agreed. %d routes, %d public methods, %d matrix rows — no route reaches\n",
        count($routes), count($public), count($matrix));
    echo "  anything undocumented and no matrix row is stale.\n";
} else {
    printf("  %d DISAGREEMENT(S) — the matrix is not exhaustive over the entry surface.\n", $fail);
}
echo str_repeat('=', 82) . "\n";
exit($fail === 0 ? 0 : 1);
