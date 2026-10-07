<?php
/**
 * run_tests.php — the chat module's whole suite.
 *
 * Standalone: no CodeIgniter bootstrap, no database, no system/ directory.
 * The PMS repo cannot boot CI on a developer machine, so each test drives the
 * real module files directly — see the individual headers.
 *
 *   php CHATMODULEDEVELOPMENT/tests/run_tests.php
 *
 * Exit code 0 = everything passed, 1 = at least one failure.
 *
 * PHP 7.4 compatible.
 */

$root  = dirname(dirname(__DIR__));
$here  = __DIR__;

$suite = array(
    'lint'          => NULL,     // handled inline below
    'js_syntax'     => $here . '/js_syntax.php',
    'schema_check'  => $here . '/schema_check.php',
    'name_case'     => $here . '/name_case.php',
    'guard_matrix'  => $here . '/guard_matrix.php',
    'route_surface' => $here . '/route_surface.php',
    'penalty_rule'  => $here . '/penalty_rule.php',
);

$failed = array();

// ---------------------------------------------------------------------
// 0. LINT — every file the module ships.
//    Cheap, and it is the only check that covers the 3,400-line view.
// ---------------------------------------------------------------------
print "=== lint ===\n";
$files = array(
    'application/controllers/Chat.php',
    'application/models/Chat_model.php',
    'application/helpers/chat_access_helper.php',
    'application/views/chatmodule/index.php',
    'application/views/chatmodule/_dock.php',
    'application/views/chatmodule/_navwidget.php',
    'application/views/chatmodule/df_groups.php',
    // touched, not owned — a syntax error in either of these takes down
    // every page of the app, not just chat
    'application/views/common/nav-menu.php',
    'application/views/gantt/df_gantt_board.php',
    'application/controllers/Task.php',
    'application/config/constants.php',
    'application/config/chat_ai.php',
    'application/config/routes.php',
);
$lint_fail = 0;
foreach ($files as $f) {
    $path = $root . '/' . $f;
    if (!is_file($path)) {
        printf("  FAIL  %s is missing\n", $f);
        $lint_fail++;
        continue;
    }
    $out = array();
    $rc  = 0;
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($path) . ' 2>&1', $out, $rc);
    if ($rc === 0) {
        printf("  ok    %s\n", $f);
    } else {
        printf("  FAIL  %s\n        %s\n", $f, implode("\n        ", $out));
        $lint_fail++;
    }
}
printf("%s  lint — %d file%s, %d failed\n\n",
    $lint_fail ? 'FAIL' : 'PASS', count($files), count($files) === 1 ? '' : 's', $lint_fail);
if ($lint_fail) $failed[] = 'lint';

// ---------------------------------------------------------------------
// The rest
// ---------------------------------------------------------------------
foreach ($suite as $name => $script) {
    if ($script === NULL) continue;
    printf("=== %s ===\n", $name);
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($script), $rc);
    print "\n";
    if ($rc !== 0) $failed[] = $name;
}

// ---------------------------------------------------------------------
print str_repeat('=', 60) . "\n";
if (empty($failed)) {
    print "ALL PASSED\n";
    exit(0);
}
printf("FAILED: %s\n", implode(', ', $failed));
exit(1);
