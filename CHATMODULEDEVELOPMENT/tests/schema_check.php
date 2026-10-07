<?php
/**
 * schema_check.php — does every PMS column the chat module reads exist?
 *
 * THE BUG CLASS THIS EXISTS TO CATCH
 * ----------------------------------
 * The module was ported from the CoreTech CRM, which has a different schema
 * under the same framework. A ported query that names a column PMS does not
 * have does not fail at lint, at load, or on the pages that do not use it — it
 * fails at the moment a user clicks the one feature that runs it, in
 * production, as a 500.
 *
 * During the port `leads` and `user_role` turned out to match verbatim while
 * the lead detail URL did not (CoreTech's `Leads/update_lead_information` is a
 * form POST target in PMS, not a page). This test is the systematic version of
 * that check.
 *
 * HOW IT KNOWS THE TRUTH
 * ----------------------
 * Database/u537620103_shubhampckpms.sql, the production dump. Its CREATE TABLE
 * statements are the schema this code will actually meet. The dump is large
 * and gitignored; when it is absent the test SKIPS rather than passes, because
 * a check that silently proves nothing is worse than no check.
 *
 *   php CHATMODULEDEVELOPMENT/tests/schema_check.php
 *
 * Exit code 0 = passed or skipped, 1 = a column is missing.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));
$dump = $root . '/Database/u537620103_shubhampckpms.sql';

/**
 * Every EXISTING PMS table/column the chat module reads.
 *
 * Derived by hand from application/models/Chat_model.php and
 * application/helpers/chat_access_helper.php. The `chat_` tables are NOT
 * listed: they are created by our own schema file, so the dump is silent on
 * them and Chat/health checks them at runtime instead.
 */
$want = array(
    // --- identity + directory --------------------------------------------
    'system_users' => array(
        'user_id', 'first_name', 'last_name', 'email', 'profile_image',
        'user_role_id', 'department_id', 'user_status', 'hide_profile',
        // scopes the department picker to the user's own location, which is
        // what keeps the two MARKETING / ACCOUNTS / DISPATCH departments apart
        'business_location',
    ),
    'user_role'    => array('user_role_id', 'user_role', 'isadmin'),
    // `status` and `business_loc_id` drive pickable_departments()
    'departments'  => array('department_id', 'department', 'status', 'business_loc_id'),

    // --- the permission framework ----------------------------------------
    'system_modules'    => array('id', 'modulename', 'status', 'dynachem', 'shubhampack'),
    'submodule'         => array('id', 'moduleid', 'submodule', 'status', 'addedOn', 'dynachem', 'shubhampack'),
    'module_access'     => array('role_id', 'moduleid', 'access'),
    'module_capablity'  => array('role_id', 'moduleid', 'submoduleid', 'submodule_access'),

    // --- linked records: df ----------------------------------------------
    // A "DF" is df_release, keyed by df_release.id — that is what
    // task_department_wise_scheduling.df_id points at and what /gantt/<id>
    // renders. NOT df_design_form_table, which is the design form and a
    // different record entirely.
    'df_release' => array(
        'id', 'df_no', 'df_description', 'added_on', 'added_by',
        'df_status', 'on_hold',
    ),
    // penalityamount (sic — that is the column's real spelling) is what marks
    // a DF as a penalty DF: the flag beside a DF group's name in the
    // messenger, on its header, and on the DF groups screen. The rule itself
    // is covered by penalty_rule.php.
    'poreceived' => array('df_id', 'company_name', 'penalityamount'),

    // --- linked records: task --------------------------------------------
    'task_department_wise_scheduling' => array(
        'id', 'df_id', 'taskid', 'department_id', 'task_status',
        'assigned_user', 'userid', 'end_date',
    ),
    'task_management' => array('task_id', 'task_name'),

    // --- who lands in a DF group -----------------------------------------
    // PMS records a department's leader here, not on departments.departmenthead.
    'prestogroup_teams' => array('team_id', 'department_id', 'team_leader', 'status'),

    // --- linked records: lead --------------------------------------------
    'leads' => array(
        'id', 'unique_id', 'company_name', 'customer_name', 'status',
        'contact_person', 'added_by',
    ),
    // Both are probed with table_exists() before use, so a site without them
    // degrades rather than breaks — but if they ARE present the columns must
    // be right.
    'lead_stage'       => array('lead_id', 'lead_name'),
    'progress_remarks' => array('id', 'lead_id', 'lead_status'),
);

if (!is_file($dump)) {
    fwrite(STDOUT,
        "SKIP  schema_check — Database/u537620103_shubhampckpms.sql not found.\n" .
        "      That file is the production dump (gitignored, ~53 MB). Without it\n" .
        "      there is nothing to check column names against.\n");
    exit(0);
}

// ---------------------------------------------------------------------
// Parse the dump. Streamed line by line: the file is ~53 MB and reading
// it whole costs more memory than this test deserves.
// ---------------------------------------------------------------------
$schema  = array();
$current = NULL;
$fh = fopen($dump, 'r');
while (($line = fgets($fh)) !== FALSE) {
    if (preg_match('/^CREATE TABLE `([a-zA-Z0-9_]+)`/', $line, $m)) {
        $current = $m[1];
        $schema[$current] = array();
        continue;
    }
    if ($current === NULL) continue;
    if (preg_match('/^\)\s*ENGINE/', $line)) { $current = NULL; continue; }
    if (preg_match('/^\s+`([a-zA-Z0-9_]+)`\s/', $line, $m)) {
        $schema[$current][] = $m[1];
    }
}
fclose($fh);

// ---------------------------------------------------------------------
// Compare
// ---------------------------------------------------------------------
$fail = 0;
$pass = 0;
$missing_tables = array();

foreach ($want as $table => $cols) {
    if (!isset($schema[$table])) {
        $missing_tables[] = $table;
        $fail++;
        continue;
    }
    foreach ($cols as $c) {
        if (in_array($c, $schema[$table], TRUE)) {
            $pass++;
        } else {
            fwrite(STDOUT, sprintf("FAIL  %s.%s does not exist in the production schema\n", $table, $c));
            $fail++;
        }
    }
}

foreach ($missing_tables as $t) {
    fwrite(STDOUT, sprintf("FAIL  table `%s` does not exist in the production schema\n", $t));
}

fwrite(STDOUT, sprintf(
    "\n%s  schema_check — %d columns verified, %d problem%s\n",
    $fail ? 'FAIL' : 'PASS', $pass, $fail, $fail === 1 ? '' : 's'
));

exit($fail ? 1 : 0);
