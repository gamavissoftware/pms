<?php
/**
 * verify_docs.php — re-run every factual claim in ROLLOUT.md,
 * PERMISSIONS_WORKSHEET.md and MODULE_CHANGELOG.md against the running
 * system. Counts, file lists, SQL results, behavioural statements: run,
 * not re-read.
 *
 * WHY THIS EXISTS
 * ---------------
 * Two document defects were found by doing exactly this, and neither was
 * visible on a careful re-read:
 *
 *   - ROLLOUT.md's tar command was checked path by path (all present).
 *   - MODULE_CHANGELOG.md claimed "131 of 132 distinct role_id values
 *     match system_users.user_id". The real figure is 131 of 131 — the
 *     one exception never existed. Measured against a pristine copy of
 *     module_capablity, because this project's own test grants write to
 *     that table and would otherwise skew the count.
 *
 * A prose document drifts from the system silently. This makes it fail
 * loudly instead.
 *
 * Requires: the abom-mysql sandbox container. The role_id claims
 * additionally need a pristine `abom_baseline.module_capablity`; they are
 * SKIPPED (not silently passed) if it is absent. To provision it:
 *
 *   docker exec abom-mysql mariadb -uroot -p... -e \
 *     "DROP DATABASE IF EXISTS abom_baseline; CREATE DATABASE abom_baseline;"
 *   sed -n '<line range for module_capablity>p' Database/<production dump>.sql \
 *     | docker exec -i abom-mysql mariadb -uroot -p... abom_baseline
 *
 * PHP 7.4 compatible.
 */

$ROOT = '/Users/manglesh/pms';
$SB   = '/Users/manglesh/pms-sandbox';

$ok = 0; $bad = 0; $skipped = 0; $fails = array();
function c($doc, $claim, $expected, $actual) {
    global $ok, $bad, $fails;
    if ($expected === $actual) { $ok++; printf("  ok    [%s] %-52s %s\n", $doc, $claim, json_encode($expected)); }
    else { $bad++; $fails[] = "[$doc] $claim"; printf("  WRONG [%s] %-52s expected %s got %s\n", $doc, $claim, json_encode($expected), json_encode($actual)); }
}
function q($s) {
    return trim((string) shell_exec('docker exec abom-mysql mariadb -uroot -pabomdev_local_only abom_dev -N -e '
        . escapeshellarg($s) . ' 2>/dev/null'));
}
function doc($f) { global $ROOT; return file_get_contents($ROOT . '/BOMMODULEDEVELOPMENT/' . $f); }

$R = doc('ROLLOUT.md');
$W = doc('PERMISSIONS_WORKSHEET.md');
$C = doc('MODULE_CHANGELOG.md');

// ---------------------------------------------------------------- ROLLOUT
echo "\n--- ROLLOUT.md ---\n";

// "27 files" claim, three places
preg_match_all('/\b27\b/', $R, $m);
$listed = 0;
if (preg_match('/```\n(application\/config\/abom\.php\n.*?)```/s', $R, $lm)) {
    $listed = count(array_filter(array_map('trim', explode("\n", $lm[1]))));
}
c('ROLLOUT', 'the copy list contains 27 entries', 27, $listed);

// every listed file must exist
$missing = array();
foreach (explode("\n", $lm[1]) as $f) {
    $f = trim($f);
    if ($f === '') { continue; }
    if (!file_exists($ROOT . '/' . $f)) { $missing[] = $f; }
}
c('ROLLOUT', 'every listed file exists', array(), $missing);

// the tar command's own file list must match the copy list
preg_match('/tar czf abom-module\.tar\.gz(.*?)\n\ntar tzf/s', $R, $tm);
$tar_paths = array();
if (isset($tm[1])) {
    foreach (preg_split('/[\s\\\\]+/', trim($tm[1])) as $t) {
        $t = trim($t);
        if ($t !== '') { $tar_paths[] = $t; }
    }
}
$tar_missing = array();
foreach ($tar_paths as $t) { if (!file_exists($ROOT . '/' . $t)) { $tar_missing[] = $t; } }
c('ROLLOUT', 'every tar path exists', array(), $tar_missing);
c('ROLLOUT', 'tar command lists 12 paths', 12, count($tar_paths));

// collision-check list must cover the same ground
c('ROLLOUT', 'collision check covers views/abom + assets/abom', true,
    strpos($R, 'application/views/abom \\') !== false && strpos($R, 'assets/abom ;') !== false);

// seed verification numbers
c('ROLLOUT', 'seed: 71 items',    '71', q("SELECT COUNT(*) FROM abom_item"));
c('ROLLOUT', 'seed: 29 FX5',      '29', q("SELECT COUNT(*) FROM abom_item WHERE plc_family_id=1"));
c('ROLLOUT', 'seed: 42 iQ-R',     '42', q("SELECT COUNT(*) FROM abom_item WHERE plc_family_id=2"));
c('ROLLOUT', 'seed: 8 sections',  '8',  q("SELECT COUNT(*) FROM abom_section"));
c('ROLLOUT', 'seed: 4 rules',     '4',  q("SELECT COUNT(*) FROM abom_plc_rule WHERE is_active=1"));
c('ROLLOUT', 'seed: 4 features',  '4',  q("SELECT COUNT(*) FROM abom_feature"));
c('ROLLOUT', 'seed: 6 formulas',  '6',  q("SELECT COUNT(*) FROM abom_formula"));
c('ROLLOUT', 'doc states 71 . 29 . 42 . 8 . 4 . 4 . 6', true,
    strpos($R, '71 · 29 · 42 · 8 · 4 · 4 · 6') !== false);

// rollback: eleven tables, all named, and all eleven exist
preg_match_all('/DROP TABLE IF EXISTS `(abom_\w+)`/', $R, $dm);
$dropped = array_unique($dm[1]);
c('ROLLOUT', 'rollback names 11 tables', 11, count($dropped));
$live = explode("\n", q("SELECT table_name FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name LIKE 'abom\\_%' ORDER BY table_name"));
$live = array_values(array_filter(array_map('trim', $live)));
sort($dropped);
c('ROLLOUT', 'rollback list == live abom_ tables', $live, array_values($dropped));

// the 16 route lines
preg_match_all("/^\\\$route\\['abom[^\\]]*'\\]/m", $R, $rm);
c('ROLLOUT', 'routes block lists 16 abom routes', 16, count(array_unique($rm[0])));
$actual_routes = (int) trim(shell_exec("grep -cE \"^.route\\['abom\" " . escapeshellarg($ROOT . '/application/config/routes.php')));
c('ROLLOUT', 'routes.php actually has 16', 16, $actual_routes);

// nav insertion point
c('ROLLOUT', 'nav anchored on text not just line no', true,
    strpos($R, 'line numbers are a hint, not the anchor') !== false);

// PDF facts
c('ROLLOUT', 'states signoff mode customer', true, strpos($R, "abom_signoff_mode = 'customer'") !== false);
c('ROLLOUT', 'DF-1827 = 29 items', true, strpos($R, 'DF-1827_FX5_29items.pdf') !== false);
c('ROLLOUT', 'DF-1826 = 42 items', true, strpos($R, 'DF-1826_iQ-R_42items.pdf') !== false);

// files it points at must exist
foreach (array('POST_DEPLOY_CHECK.md', 'PERMISSIONS_WORKSHEET.md', 'NAV_SNIPPET.md',
               'MODULE_CHANGELOG.md', 'abom_004_views_invoker.sql',
               'abom_004_views_invoker_ROLLBACK.sql', 'tests/manifest.json',
               'tests/integrity.php') as $f) {
    c('ROLLOUT', 'referenced file exists: ' . $f, true, file_exists($ROOT . '/BOMMODULEDEVELOPMENT/' . $f));
}
foreach (array('Database/abom_001.sql', 'Database/abom_002_seed.sql',
               'Database/abom_003_permissions.sql') as $f) {
    c('ROLLOUT', 'referenced file exists: ' . $f, true, file_exists($ROOT . '/' . $f));
}

// --------------------------------------------------------------- WORKSHEET
echo "\n--- PERMISSIONS_WORKSHEET.md ---\n";

// the candidate query must actually run and return names
$cnt = q("SELECT COUNT(*) FROM system_users u LEFT JOIN user_role r ON r.user_role_id=u.user_role_id LEFT JOIN departments d ON d.department_id=u.department_id WHERE u.user_status=1");
c('WORKSHEET', 'candidate query runs and returns rows', true, (int) $cnt > 0);
c('WORKSHEET', 'query uses user_role + departments', true,
    strpos($W, 'LEFT JOIN user_role') !== false && strpos($W, 'LEFT JOIN departments') !== false);

// the columns it selects must exist
foreach (array('system_users.user_status' => "SHOW COLUMNS FROM system_users LIKE 'user_status'",
               'user_role.user_role'      => "SHOW COLUMNS FROM user_role LIKE 'user_role'",
               'departments.department'    => "SHOW COLUMNS FROM departments LIKE 'department'") as $label => $sql) {
    c('WORKSHEET', 'column exists: ' . $label, true, q($sql) !== '');
}

// the three submodule names must match what 003 creates
foreach (array('AUTOMATION BOM GENERATOR', 'AUTOMATION BOM APPROVALS', 'AUTOMATION BOM MASTER ITEMS') as $sm) {
    c('WORKSHEET', 'submodule exists: ' . $sm, '1', q("SELECT COUNT(*) FROM submodule WHERE submodule='$sm'"));
    c('WORKSHEET', 'doc mentions ' . $sm, true, strpos($W, $sm) !== false);
}

// the INSERT column list must match the real table
$cols = q("SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='module_capablity'");
foreach (array('acessid','role_id','moduleid','submoduleid','submodule_access','madd','medit','mremove','addedOn','upadtedOn') as $col) {
    c('WORKSHEET', 'module_capablity has ' . $col, true, strpos($cols, $col) !== false);
}
c('WORKSHEET', 'states APPROVALS needs 2+ people', true,
    stripos($W, 'at least TWO people') !== false || stripos($W, 'at least two names') !== false);
c('WORKSHEET', 'states role_id holds a user id', true, strpos($W, 'user id') !== false);

// --------------------------------------------------------------- CHANGELOG
echo "\n--- MODULE_CHANGELOG.md ---\n";

c('CHANGELOG', 'design doc md5 as stated', 'bf9c0500f1939e4cbb11110addb766d2',
    md5_file($ROOT . '/BOMMODULEDEVELOPMENT/SPM1200L_Automation_BOM_DF1826_DF1827_Review.html'));
c('CHANGELOG', 'md5 appears in the doc', true, strpos($C, 'bf9c0500f1939e4cbb11110addb766d2') !== false);

// row-class distribution
$dist = array('is-conflict' => 2, 'is-noerp' => 5, 'is-optional' => 7, 'is-manual' => 24);
c('CHANGELOG', 'conflict items = 2', '2', q("SELECT COUNT(*) FROM abom_item WHERE issue_severity='conflict'"));
c('CHANGELOG', 'no_erp items = 5', '5', q("SELECT COUNT(*) FROM abom_item WHERE issue_severity='no_erp'"));
c('CHANGELOG', 'optional items = 7', '7', q("SELECT COUNT(*) FROM abom_item WHERE is_optional=1"));
c('CHANGELOG', 'review items = 29', '29', q("SELECT COUNT(*) FROM abom_item WHERE issue_severity='review'"));
foreach ($dist as $cls => $n) {
    c('CHANGELOG', 'doc states ' . $cls . ' = ' . $n, true,
        preg_match('/`' . preg_quote($cls, '/') . '`[^|]*\|[^|]*\|\s*' . $n . '\s*\|/', $C) === 1);
}

// six views, definer
c('CHANGELOG', 'six views exist', '6', q("SELECT COUNT(*) FROM information_schema.VIEWS WHERE TABLE_SCHEMA=DATABASE()"));
c('CHANGELOG', 'all six share one definer', '1',
    q("SELECT COUNT(DISTINCT DEFINER) FROM information_schema.VIEWS WHERE TABLE_SCHEMA=DATABASE()"));
c('CHANGELOG', 'definer is as documented', 'u537620103_shuser@127.0.0.1',
    q("SELECT DISTINCT DEFINER FROM information_schema.VIEWS WHERE TABLE_SCHEMA=DATABASE()"));
c('CHANGELOG', 'system_users_view has 25 live callers', 25,
    (int) trim(shell_exec("cd " . escapeshellarg($ROOT) . " && grep -rl system_users_view application/ 2>/dev/null | grep -viE 'backup|_old|Old|OLDDD' | wc -l")));

// module_capablity role_id semantics
// Measured against the PRISTINE dump, not the sandbox: this suite's own
// test grants add rows to module_capablity, which is exactly what made the
// first reading of this claim unreliable.
$B = 'abom_baseline.module_capablity';
$have_baseline = q("SELECT COUNT(*) FROM information_schema.TABLES "
    . "WHERE TABLE_SCHEMA='abom_baseline' AND TABLE_NAME='module_capablity'") === '1';
if (!$have_baseline) {
    echo "  SKIP  [CHANGELOG] role_id claims — abom_baseline.module_capablity absent\n";
    echo "        (provision it from the production dump; see the header of this file)\n";
    $skipped += 7;
}
if ($have_baseline) {
c('CHANGELOG', 'role_id: 2,614 rows', '2614', q("SELECT COUNT(*) FROM $B"));
c('CHANGELOG', 'role_id: 131 distinct', '131', q("SELECT COUNT(DISTINCT role_id) FROM $B"));
c('CHANGELOG', 'role_id: range 61-238', '61|238', q("SELECT CONCAT(MIN(role_id),'|',MAX(role_id)) FROM $B"));
c('CHANGELOG', 'role_id: 131 of 131 match user_id', '131',
    q("SELECT COUNT(*) FROM (SELECT DISTINCT role_id FROM $B) m JOIN system_users s ON s.user_id=m.role_id"));
c('CHANGELOG', 'role_id: 39 of 131 match user_role', '39',
    q("SELECT COUNT(*) FROM (SELECT DISTINCT role_id FROM $B) m JOIN user_role r ON r.user_role_id=m.role_id"));
c('CHANGELOG', 'role_id: 92 above MAX(user_role_id)', '92',
    q("SELECT COUNT(DISTINCT role_id) FROM $B WHERE role_id > (SELECT MAX(user_role_id) FROM user_role)"));
c('CHANGELOG', 'user_role: 102 rows, max 104', '102|104', q("SELECT CONCAT(COUNT(*),'|',MAX(user_role_id)) FROM user_role"));
}
c('CHANGELOG', 'doc states 131 of 131', true, strpos($C, '131 of 131') !== false);
c('CHANGELOG', 'doc no longer claims 131 of 132', false, strpos($C, '**131 of 132**') !== false);

// PHP 7.4 claim
$v = trim(shell_exec("docker run --rm php:7.4-cli php -r 'echo PHP_VERSION;' 2>/dev/null"));
c('CHANGELOG', 'verified on PHP 7.4.33', '7.4.33', $v);

// MariaDB claim
c('CHANGELOG', 'MariaDB 11.4 in use', true,
    strpos(q("SELECT VERSION()"), '11.4') !== false);

// master_edit is declared but nothing consumes it
$mc = (int) trim(shell_exec("cd " . escapeshellarg($ROOT) . " && grep -c \"master_edit\" application/controllers/Abom.php"));
c('CHANGELOG', 'master_edit referenced in controller (via require_any_perm)', true, $mc > 0);
$screens = glob($ROOT . '/application/views/abom/master*');
c('CHANGELOG', 'master-item admin screens do NOT exist', array(), $screens);

// --- claims added by the guard audit (section 0.14) ---
c('CHANGELOG', 'csrf_protection is FALSE application-wide', true,
    (bool) preg_match("/'csrf_protection'\\s*\\]?\\s*=\\s*FALSE/i",
        file_get_contents($ROOT . '/application/config/config.php')));
c('CHANGELOG', 'doc records CSRF as accepted, not fixed', true,
    strpos($C, '0.14.3 CSRF') !== false && strpos($C, 'deliberately not fixed') !== false);
c('CHANGELOG', 'Abom_engine.php exists as referenced', true,
    file_exists($ROOT . '/application/libraries/Abom_engine.php'));
c('CHANGELOG', 'no stale Bom_engine.php reference', false, strpos($C, 'libraries/Bom_engine.php`.') !== false);
c('CHANGELOG', 'reject requires check', true, (bool) preg_match(
    "/reject.*?has_perm\\('check'\\)/s", file_get_contents($ROOT . '/application/controllers/Abom.php')));
c('CHANGELOG', 'require_any_perm exists', true, (bool) strpos(
    file_get_contents($ROOT . '/application/controllers/Abom.php'), 'private function require_any_perm'));
$ctl = file_get_contents($ROOT . '/application/controllers/Abom.php');
c('CHANGELOG', 'require_any_perm called on 4 read paths', 4,
    preg_match_all('/require_any_perm\\(\\)/', $ctl) - 1);
c('CHANGELOG', '17 public entry points as documented', 17,
    preg_match_all('/^\\s*public function (\\w+)/m', $ctl));
c('CHANGELOG', 'section 0.13 marked superseded', true, strpos($C, 'Superseded by §0.14') !== false);
c('WORKSHEET', 'says MASTER ITEMS grants nothing today', true,
    strpos($W, 'today this permission grants nothing') !== false);
c('WORKSHEET', 'says the list may be left empty', true, strpos($W, 'may leave this list empty') !== false);
c('WORKSHEET', 'warns any-one-of-three grants read', true,
    strpos($W, 'lets a person read every BOM') !== false);
c('CHANGELOG', 'future work: master_edit must be wired first', true,
    strpos($C, 'Wire `AUTOMATION BOM MASTER ITEMS` before any master-item screen') !== false);
c('CHANGELOG', 'ownership recorded as accepted decision', true,
    strpos($C, 'ACCEPTED, DELIBERATE: BOMs have no per-user ownership') !== false);
c('CHANGELOG', 'abom_bom has no owner_id (as documented)', '',
    q("SHOW COLUMNS FROM abom_bom LIKE 'owner_id'"));

echo "\n" . str_repeat('=', 70) . "\n";
printf("  %d verified, %d wrong, %d skipped  (%d claims)\n",
    $ok, $bad, $skipped, $ok + $bad + $skipped);
foreach ($fails as $f) { echo "    - $f\n"; }
echo str_repeat('=', 70) . "\n";
exit($bad === 0 ? 0 : 1);
