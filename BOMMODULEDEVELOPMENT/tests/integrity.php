<?php
/**
 * integrity.php — module file manifest and tamper check
 *
 * The working tree is shared: an unrelated change to
 * application/controllers/Spares.php appeared mid-build and was only
 * noticed because it surfaced in `git status`. This makes that detection
 * immediate and mechanical rather than incidental.
 *
 *   php BOMMODULEDEVELOPMENT/tests/integrity.php --write   # record manifest
 *   php BOMMODULEDEVELOPMENT/tests/integrity.php           # verify
 *
 * Verify reports three things:
 *   CHANGED  a module file whose checksum no longer matches
 *   MISSING  a module file that has been deleted
 *   NEW      a file matching the module's own patterns that is not in
 *            the manifest (so a stray file cannot hide either)
 *
 * Exit code 0 = clean, 1 = something differs.
 *
 * The manifest doubles as the DEPLOYMENT FILE LIST. This tree must never
 * be synced to the server wholesale — it is an incomplete copy of
 * production and would delete BOM.php, views/BOM/ and anything else
 * missing here. Deploy exactly the files listed in manifest.json, by
 * name.
 *
 * PHP 7.4 compatible.
 */

$root     = dirname(dirname(__DIR__));
$manifest = __DIR__ . '/manifest.json';
$write    = in_array('--write', $argv, true);

/** Files the module owns. Everything else in the tree is not ours. */
$patterns = array(
    'application/config/abom.php',
    'application/controllers/Abom.php',
    'application/models/Abom_*.php',
    'application/libraries/Abom_*.php',
    'application/helpers/abom_helper.php',
    'application/views/abom/*.php',
    'assets/abom/*',
    'Database/abom_*.sql',
    'BOMMODULEDEVELOPMENT/tests/*.php',
    'BOMMODULEDEVELOPMENT/MODULE_CHANGELOG.md',
);

/**
 * Files the module APPENDS to but does not own. Tracked separately —
 * a change here is expected, but it must be a change we made.
 */
$appended = array(
    'application/config/routes.php',
);

/** Read-only inputs. A checksum change here means the spec moved. */
$inputs = array(
    'BOMMODULEDEVELOPMENT/Automation_BOM_Module_CI3_Spec.md',
    'BOMMODULEDEVELOPMENT/SPM1200L_Automation_BOM_DF1826_DF1827_Review.html',
    'BOMMODULEDEVELOPMENT/abom_schema.sql',
    'BOMMODULEDEVELOPMENT/abom_seed.sql',
);

function collect($root, array $patterns)
{
    $files = array();

    foreach ($patterns as $pattern) {
        foreach (glob($root . '/' . $pattern) as $path) {
            if (is_file($path)) {
                $files[] = ltrim(str_replace($root, '', $path), '/');
            }
        }
    }

    $files = array_values(array_unique($files));
    sort($files);

    return $files;
}

$owned = collect($root, $patterns);

// ---------------------------------------------------------------------
if ($write) {
    $out = array('owned' => array(), 'appended' => array(), 'inputs' => array());

    foreach ($owned as $rel) {
        $out['owned'][$rel] = md5_file($root . '/' . $rel);
    }
    foreach ($appended as $rel) {
        if (is_file($root . '/' . $rel)) {
            $out['appended'][$rel] = md5_file($root . '/' . $rel);
        }
    }
    foreach ($inputs as $rel) {
        if (is_file($root . '/' . $rel)) {
            $out['inputs'][$rel] = md5_file($root . '/' . $rel);
        }
    }

    file_put_contents($manifest, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

    printf("  manifest written: %d owned, %d appended, %d inputs\n",
        count($out['owned']), count($out['appended']), count($out['inputs']));
    exit(0);
}

// ---------------------------------------------------------------------
if (!is_readable($manifest)) {
    echo "  No manifest. Run with --write first.\n";
    exit(1);
}

$saved = json_decode(file_get_contents($manifest), true);
$bad   = 0;

foreach (array('owned' => 'module', 'appended' => 'appended', 'inputs' => 'INPUT') as $group => $label) {
    foreach ($saved[$group] as $rel => $hash) {
        $path = $root . '/' . $rel;

        if (!is_file($path)) {
            $bad++;
            printf("  MISSING   %-9s %s\n", $label, $rel);
            continue;
        }

        if (md5_file($path) !== $hash) {
            $bad++;
            printf("  CHANGED   %-9s %s\n", $label, $rel);
        }
    }
}

// A stray file matching our own patterns must not hide.
foreach ($owned as $rel) {
    if (!isset($saved['owned'][$rel])) {
        $bad++;
        printf("  NEW       %-9s %s\n", 'module', $rel);
    }
}

if ($bad === 0) {
    printf("  Module intact: %d owned files, %d appended, %d read-only inputs unchanged.\n",
        count($saved['owned']), count($saved['appended']), count($saved['inputs']));
} else {
    printf("\n  %d DIFFERENCE(S). If you did not make them, something else is writing to this tree.\n", $bad);
}

exit($bad === 0 ? 0 : 1);
