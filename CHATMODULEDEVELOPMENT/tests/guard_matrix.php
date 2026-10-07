<?php
/**
 * guard_matrix.php — what is each kind of user actually allowed to do?
 *
 * Exercises the REAL application/helpers/chat_access_helper.php against a
 * stubbed CI. Every case is run in its own PHP process, because the helper
 * caches identity, admin-ness and grants in function-local statics for the
 * life of a request — two cases in one process would have the second silently
 * assert the first one's answer.
 *
 *   php CHATMODULEDEVELOPMENT/tests/guard_matrix.php          # all cases
 *   php CHATMODULEDEVELOPMENT/tests/guard_matrix.php granted  # one case
 *
 * Exit code 0 = all passed, 1 = at least one failure.
 *
 * WHAT MATTERS MOST HERE
 * ----------------------
 * `perm_key_is_user_id`. In PMS both module_access.role_id and
 * module_capablity.role_id hold a USER id despite the column name; the
 * CoreTech original passed the session ROLE. Getting that wrong does not
 * throw — it reads somebody else's grants and returns a confident wrong
 * answer, which is the hardest kind of permission bug to notice. That case
 * plants DIFFERENT grants against the user id and the role id and asserts
 * which one is honoured.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));
require __DIR__ . '/stubs.php';

// ---------------------------------------------------------------------
// Fixtures
// ---------------------------------------------------------------------
define('T_USER_ID', 57);      // the person signed in
define('T_ROLE_ID', 12);      // their user_role_id — deliberately different
define('T_MODULE_ID', 31);    // the CHAT row in system_modules

/** The submodule rows chat_002_permissions.sql creates, with stable ids. */
function t_submodules()
{
    return array(
        array('id' => 501, 'moduleid' => T_MODULE_ID, 'submodule' => 'Chat - Messenger',      'status' => 1),
        array('id' => 502, 'moduleid' => T_MODULE_ID, 'submodule' => 'Chat - Create Group',   'status' => 1),
        array('id' => 503, 'moduleid' => T_MODULE_ID, 'submodule' => 'Chat - Manage Members', 'status' => 1),
        array('id' => 504, 'moduleid' => T_MODULE_ID, 'submodule' => 'Chat - Pin Message',    'status' => 1),
        array('id' => 505, 'moduleid' => T_MODULE_ID, 'submodule' => 'Chat - File Sharing',   'status' => 1),
        array('id' => 506, 'moduleid' => T_MODULE_ID, 'submodule' => 'Chat - Link Records',   'status' => 1),
    );
}

function t_session($user_id = T_USER_ID, $role = T_ROLE_ID)
{
    return array(
        'user_id'       => $user_id,
        'user_name'     => 'Ravi',
        'last_name'     => 'Kumar',
        'role'          => $role,
        'profile_image' => '',
    );
}

/**
 * Departments, mirroring production's shape: MARKETING appears TWICE (ids 6
 * and 9), which is why chat_marketing_department_ids() resolves by name.
 */
function t_departments()
{
    return array(
        array('department_id' => 6,  'department' => 'MARKETING', 'status' => 1),
        array('department_id' => 9,  'department' => 'MARKETING', 'status' => 1),
        array('department_id' => 12, 'department' => 'DESIGN',    'status' => 1),
        array('department_id' => 20, 'department' => 'PURCHASE',  'status' => 1),
    );
}

/** user_role rows; $isadmin drives chat_is_admin(). */
function t_roles($isadmin = 0)
{
    return array(
        array('user_role_id' => T_ROLE_ID, 'user_role' => 'Project Engineer', 'isadmin' => $isadmin),
    );
}

// ---------------------------------------------------------------------
// The cases. Each returns array($data, $session, $expected_capabilities).
// ---------------------------------------------------------------------
$CASES = array();

/* An ordinary user with the module registered but NO grants of their own.
   chat_can() falls back to chat_default_caps(): everything everyday is
   allowed so chat works on day one; linking records is not. */
$CASES['no_grants'] = function () {
    return array(
        array(
            'system_modules' => array(array('id' => T_MODULE_ID, 'modulename' => 'CHAT', 'status' => 1)),
            'submodule'      => t_submodules(),
            'user_role'      => t_roles(0),
            'module_access'  => array(),   // never granted the CHAT module
        ),
        t_session(),
        array(
            'messenger' => TRUE, 'file_share' => TRUE, 'create_channel' => TRUE,
            'manage_members' => TRUE, 'pin' => TRUE, 'start_dm' => TRUE,
            'start_call' => TRUE, 'tech_chat' => TRUE,
            'lead_tag' => FALSE,           // the one thing that must be earned
        ),
    );
};

/* The module has been granted and specific capabilities ticked. Once ANY
   grant exists the defaults stop applying and the ticks are the whole
   truth — so an unticked capability is denied even though it is in
   chat_default_caps(). */
$CASES['granted'] = function () {
    return array(
        array(
            'system_modules'   => array(array('id' => T_MODULE_ID, 'modulename' => 'CHAT', 'status' => 1)),
            'submodule'        => t_submodules(),
            'user_role'        => t_roles(0),
            'module_access'    => array(array('role_id' => T_USER_ID, 'moduleid' => T_MODULE_ID, 'access' => 1)),
            'module_capablity' => array(
                array('role_id' => T_USER_ID, 'moduleid' => T_MODULE_ID, 'submoduleid' => 501, 'submodule_access' => 1),
                array('role_id' => T_USER_ID, 'moduleid' => T_MODULE_ID, 'submoduleid' => 506, 'submodule_access' => 1),
                array('role_id' => T_USER_ID, 'moduleid' => T_MODULE_ID, 'submoduleid' => 504, 'submodule_access' => 0),
            ),
        ),
        t_session(),
        array(
            'messenger' => TRUE,           // ticked
            'lead_tag'  => TRUE,           // ticked
            'start_dm'  => TRUE,           // maps to the messenger row
            'pin'       => FALSE,          // explicitly un-ticked
            'file_share'     => FALSE,     // no row at all
            'create_channel' => FALSE,
            'manage_members' => FALSE,
            'tech_chat' => TRUE,           // always true in PMS
        ),
    );
};

/* THE PORT'S CRITICAL CASE.
   Grants are planted against BOTH the user id and the role id, granting
   opposite things. Whichever set comes back tells us which column the
   helper keyed on. PMS keys on the USER id. */
$CASES['perm_key_is_user_id'] = function () {
    return array(
        array(
            'system_modules'   => array(array('id' => T_MODULE_ID, 'modulename' => 'CHAT', 'status' => 1)),
            'submodule'        => t_submodules(),
            'user_role'        => t_roles(0),
            'module_access'    => array(
                array('role_id' => T_USER_ID, 'moduleid' => T_MODULE_ID, 'access' => 1),
                array('role_id' => T_ROLE_ID, 'moduleid' => T_MODULE_ID, 'access' => 1),
            ),
            'module_capablity' => array(
                // keyed by USER id — this is what PMS must honour
                array('role_id' => T_USER_ID, 'moduleid' => T_MODULE_ID, 'submoduleid' => 506, 'submodule_access' => 1),
                // keyed by ROLE id — the CoreTech reading; must be ignored
                array('role_id' => T_ROLE_ID, 'moduleid' => T_MODULE_ID, 'submoduleid' => 504, 'submodule_access' => 1),
            ),
        ),
        t_session(),
        array(
            'lead_tag' => TRUE,    // the user-id grant was honoured
            'pin'      => FALSE,   // the role-id grant was NOT
        ),
    );
};

/* An admin role (user_role.isadmin = 1) bypasses the grant tables. */
$CASES['admin'] = function () {
    return array(
        array(
            'system_modules' => array(array('id' => T_MODULE_ID, 'modulename' => 'CHAT', 'status' => 1)),
            'submodule'      => t_submodules(),
            'user_role'      => t_roles(1),
            'module_access'  => array(),
        ),
        t_session(),
        array(
            'messenger' => TRUE, 'lead_tag' => TRUE, 'pin' => TRUE,
            'create_channel' => TRUE, 'manage_members' => TRUE,
            'file_share' => TRUE, 'start_call' => TRUE,
        ),
    );
};

/* chat_002_permissions.sql has not been run. The module must stay usable
   rather than locking everyone out of a feature they cannot yet be
   granted. */
$CASES['module_not_registered'] = function () {
    return array(
        array(
            'system_modules' => array(),   // no CHAT row
            'user_role'      => t_roles(0),
        ),
        t_session(),
        array(
            'messenger' => TRUE, 'lead_tag' => TRUE, 'pin' => TRUE,
            'create_channel' => TRUE,
        ),
    );
};

/* Nobody signed in: every capability is denied, and denied without
   consulting the database. */
$CASES['logged_out'] = function () {
    return array(
        array('system_modules' => array(array('id' => T_MODULE_ID, 'modulename' => 'CHAT', 'status' => 1))),
        NULL,
        array(
            'messenger' => FALSE, 'lead_tag' => FALSE, 'pin' => FALSE,
            'create_channel' => FALSE, 'file_share' => FALSE,
            // even the always-true one, because there is no identity at all
            'tech_chat' => FALSE,
        ),
    );
};

/* ---------------------------------------------------------------------
   DF GROUP GATE — who may create DF chat groups, and over which DFs.

   Marketing and administrators only. Everybody else still takes part in a
   DF group once added; they simply may not create them.
   ------------------------------------------------------------------ */

function t_df_env($isadmin = 0)
{
    return array(
        'system_modules' => array(array('id' => T_MODULE_ID, 'modulename' => 'CHAT', 'status' => 1)),
        'submodule'      => t_submodules(),
        'user_role'      => t_roles($isadmin),
        'departments'    => t_departments(),
        'module_access'  => array(),   // no grants -> create_channel via defaults
    );
}

/* Administrator: every DF. */
$CASES['df_groups_admin'] = function () {
    return array(t_df_env(1), t_session(), array(), 'all');
};

/* Marketing (department 9): only the DFs they released. */
$CASES['df_groups_marketing_9'] = function () {
    return array(t_df_env(0), t_session(T_USER_ID, T_ROLE_ID) + array('department_id' => 9), array(), 'own');
};

/* Marketing's SECOND department id must work identically — this is the
   case a hardcoded id would get wrong. */
$CASES['df_groups_marketing_6'] = function () {
    return array(t_df_env(0), t_session(T_USER_ID, T_ROLE_ID) + array('department_id' => 6), array(), 'own');
};

/* Design: shut out entirely. */
$CASES['df_groups_other_dept'] = function () {
    return array(t_df_env(0), t_session(T_USER_ID, T_ROLE_ID) + array('department_id' => 12), array(), '');
};

/* No department on the account at all: shut out, not defaulted in. */
$CASES['df_groups_no_dept'] = function () {
    return array(t_df_env(0), t_session(), array(), '');
};

// ---------------------------------------------------------------------
// Runner
// ---------------------------------------------------------------------

/** Run ONE case in this process and print its results. */
function run_case($name, $factory, $root)
{
    $spec    = $factory();
    $data    = $spec[0];
    $session = $spec[1];
    $expect  = $spec[2];
    // 4th element, when present, is the expected chat_df_group_scope()
    $want_scope = array_key_exists(3, $spec) ? $spec[3] : NULL;

    $GLOBALS['__ci'] = new Chat_stub_ci(
        new Chat_stub_db($data),
        new Chat_stub_session($session)
    );

    defined('BASEPATH') OR define('BASEPATH', TRUE);
    // chat_avatar_url() dereferences this constant; nothing here calls it,
    // but the file is parsed as a whole.
    defined('user_profile') OR define('user_profile', 'https://example.invalid/users/');

    chat_test_require_fresh_process();
    require $root . '/application/helpers/chat_access_helper.php';

    $CI = $GLOBALS['__ci'];
    $fail = 0;

    foreach ($expect as $cap => $want) {
        $got = chat_can($CI, $cap);
        if ($got === $want) {
            printf("  ok    %-16s %s\n", $cap, $want ? 'allowed' : 'denied');
        } else {
            printf("  FAIL  %-16s expected %s, got %s\n",
                $cap, $want ? 'allowed' : 'denied', $got ? 'allowed' : 'denied');
            $fail++;
        }
    }

    if ($want_scope !== NULL) {
        $got = chat_df_group_scope($CI);
        if ($got === $want_scope) {
            printf("  ok    %-16s %s\n", 'df_group_scope', $got === '' ? '(denied)' : $got);
        } else {
            printf("  FAIL  %-16s expected '%s', got '%s'\n", 'df_group_scope', $want_scope, $got);
            $fail++;
        }
    }

    // Invariants that hold in every case, checked once per case so a
    // regression cannot hide in a case that happens not to assert them.
    if ($session !== NULL) {
        if (chat_is_external($CI) !== FALSE) {
            print "  FAIL  chat_is_external must be FALSE in PMS — there is one account table\n";
            $fail++;
        } else {
            print "  ok    chat_is_external  FALSE\n";
        }

        if (chat_perm_key($CI) !== T_USER_ID) {
            printf("  FAIL  chat_perm_key returned %s, expected the user id %d\n",
                var_export(chat_perm_key($CI), TRUE), T_USER_ID);
            $fail++;
        } else {
            print "  ok    chat_perm_key    user id\n";
        }
    }

    return $fail;
}

// ---- one case, in this process --------------------------------------
if ($argc > 1) {
    $name = $argv[1];
    if (!isset($CASES[$name])) {
        fwrite(STDERR, "unknown case: $name\n");
        exit(2);
    }
    printf("%s:\n", $name);
    exit(run_case($name, $CASES[$name], $root) ? 1 : 0);
}

// ---- all cases, one child process each ------------------------------
$php   = PHP_BINARY;
$self  = __FILE__;
$fails = 0;

foreach (array_keys($CASES) as $name) {
    $cmd = escapeshellarg($php) . ' ' . escapeshellarg($self) . ' ' . escapeshellarg($name);
    passthru($cmd, $rc);
    if ($rc !== 0) $fails++;
}

printf("\n%s  guard_matrix — %d case%s, %d failed\n",
    $fails ? 'FAIL' : 'PASS',
    count($CASES), count($CASES) === 1 ? '' : 's', $fails);

exit($fails ? 1 : 0);
