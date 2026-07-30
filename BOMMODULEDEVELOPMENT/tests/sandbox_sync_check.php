<?php
/**
 * sandbox_sync_check.php — refuse to trust a probe run against stale code.
 *
 * WHY THIS EXISTS
 * ---------------
 * The sandbox web container serves ~/pms-sandbox, NOT this working tree.
 * A module file edited here and not copied there means every HTTP probe
 * silently measures the OLD code and reports it as current.
 *
 * This happened: a guard-order fix was made, the probe re-run, and the
 * probe reported the unfixed behaviour. It was caught only because the
 * result was surprising. A less surprising stale result would have been
 * believed — and this whole audit rests on measured results being real.
 *
 * Run this before any HTTP probe, or call assert_sandbox_current().
 *
 *   php BOMMODULEDEVELOPMENT/tests/sandbox_sync_check.php
 *
 * application/config/abom.php is EXPECTED to differ: the shipped file has
 * abom_submodule_ids as null by design (it must fail loudly rather than
 * guess), and the sandbox has the real ids wired in.
 *
 * PHP 7.4 compatible.
 */

$root    = dirname(dirname(__DIR__));
$sandbox = dirname($root) . '/pms-sandbox';

/** Expected to differ, with the reason. */
$expected_diff = array(
    'application/config/abom.php' =>
        'abom_submodule_ids: shipped null by design, wired in the sandbox',
);

function module_files($root)
{
    $patterns = array(
        'application/controllers/Abom*.php',
        'application/models/Abom*.php',
        'application/libraries/Abom*.php',
        'application/helpers/abom*.php',
        'application/config/abom.php',
        'application/views/abom/*',
        'assets/abom/*',
    );

    $out = array();
    foreach ($patterns as $p) {
        foreach (glob($root . '/' . $p) as $path) {
            if (is_file($path)) {
                $out[] = ltrim(str_replace($root, '', $path), '/');
            }
        }
    }
    sort($out);

    return $out;
}

function assert_sandbox_current($verbose = true)
{
    global $root, $sandbox, $expected_diff;

    if (!is_dir($sandbox)) {
        if ($verbose) { echo "  sandbox not present at $sandbox — nothing to check\n"; }
        return true;
    }

    $stale = array();
    $missing = array();
    $ok = 0;

    foreach (module_files($root) as $rel) {
        $there = $sandbox . '/' . $rel;

        if (!file_exists($there)) {
            $missing[] = $rel;
            continue;
        }

        if (md5_file($root . '/' . $rel) === md5_file($there)) {
            $ok++;
        } elseif (isset($expected_diff[$rel])) {
            if ($verbose) {
                printf("  expected-diff  %-46s %s\n", $rel, $expected_diff[$rel]);
            }
            $ok++;
        } else {
            $stale[] = $rel;
        }
    }

    if ($verbose) {
        printf("  %d module file(s) identical or expected-diff\n", $ok);
    }

    foreach ($missing as $rel) {
        printf("  MISSING FROM SANDBOX  %s\n", $rel);
    }
    foreach ($stale as $rel) {
        printf("  STALE IN SANDBOX      %s\n", $rel);
    }

    if ($stale || $missing) {
        echo "\n  *** DO NOT TRUST HTTP PROBE RESULTS ***\n";
        echo "  The container serves the sandbox copy. Sync first:\n\n";
        foreach (array_merge($stale, $missing) as $rel) {
            echo "    cp " . escapeshellarg($rel) . " "
               . escapeshellarg(str_replace($root . '/', '', $sandbox) . '/' . $rel) . "\n";
        }
        echo "\n";
        return false;
    }

    if ($verbose) { echo "  Sandbox is current. Probe results are trustworthy.\n"; }

    return true;
}

// Run standalone.
if (realpath($argv[0]) === realpath(__FILE__)) {
    echo "\nSANDBOX SYNC CHECK\n" . str_repeat('=', 66) . "\n";
    $ok = assert_sandbox_current(true);
    echo str_repeat('=', 66) . "\n";
    exit($ok ? 0 : 1);
}
