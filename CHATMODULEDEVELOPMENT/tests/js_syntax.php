<?php
/**
 * js_syntax.php — does the JavaScript inside the views actually parse?
 *
 * THE GAP THIS FILLS
 * ------------------
 * The messenger is a 3,400-line PHP view that is mostly JavaScript. `php -l`
 * proves the PHP parses and says nothing at all about the JS — so a stray
 * brace or a half-finished edit inside a <script> block passes every other
 * check in this suite and then breaks the entire messenger in the browser,
 * silently, with no server-side error to find.
 *
 * The port edited JS in that view by hand (the sidebar tabs, the people
 * pickers, the record picker), which is exactly the situation this guards.
 *
 * HOW
 * ---
 * Extract each <script> block, replace the PHP short-echo tags with a JSON
 * placeholder so the result is valid JS, and hand it to `node --check`.
 *
 * SKIPS when node is not installed — this is a convenience check, not a
 * gate that should stop someone without a node install from running the
 * suite.
 *
 *   php CHATMODULEDEVELOPMENT/tests/js_syntax.php
 *
 * Exit code 0 = parsed or skipped, 1 = a syntax error.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

// ---- is node available? ---------------------------------------------
$out = array();
$rc  = 0;
exec('command -v node 2>/dev/null', $out, $rc);
if ($rc !== 0 || empty($out)) {
    print "SKIP  js_syntax — node is not installed, cannot parse the script blocks.\n";
    exit(0);
}
$node = trim($out[0]);

$views = array(
    'application/views/chatmodule/index.php',
    'application/views/chatmodule/_dock.php',
    'application/views/chatmodule/_navwidget.php',
);

$fail = 0;
$pass = 0;

foreach ($views as $rel) {
    $src = @file_get_contents($root . '/' . $rel);
    if ($src === FALSE) {
        printf("  FAIL  missing view %s\n", $rel);
        $fail++;
        continue;
    }

    // <script> blocks that are OURS: skip anything with a src= attribute.
    preg_match_all('#<script(?![^>]*\bsrc=)[^>]*>(.*?)</script>#is', $src, $m);

    foreach ($m[1] as $i => $js) {
        // PHP interpolation inside JS is always a VALUE position here
        // (a string, a number, a JSON object). A JSON literal is a valid
        // stand-in for every one of them.
        $js = preg_replace('/<\?php.*?\?>/s', '0', $js);
        $js = preg_replace('/<\?=.*?\?>/s', '0', $js);
        $js = preg_replace('/<\?.*?\?>/s', '0', $js);

        $tmp = tempnam(sys_get_temp_dir(), 'chatjs') . '.js';
        file_put_contents($tmp, $js);

        $o = array();
        $r = 0;
        exec(escapeshellarg($node) . ' --check ' . escapeshellarg($tmp) . ' 2>&1', $o, $r);
        @unlink($tmp);

        $label = sprintf('%s block %d', basename($rel), $i + 1);
        if ($r === 0) {
            printf("  ok    %s\n", $label);
            $pass++;
        } else {
            printf("  FAIL  %s\n", $label);
            foreach (array_slice($o, 0, 6) as $line) printf("        %s\n", $line);
            $fail++;
        }
    }
}

printf("\n%s  js_syntax — %d block%s parsed, %d failed\n",
    $fail ? 'FAIL' : 'PASS', $pass, $pass === 1 ? '' : 's', $fail);

exit($fail ? 1 : 0);
