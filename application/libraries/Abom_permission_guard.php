<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_permission_guard
 *
 * Checks the module's actions against the project's module_capablity
 * ACL, and — just as importantly — checks that the ACL wiring itself is
 * configured before trusting a "no" from it.
 *
 * THE FAILURE MODE THIS EXISTS TO PREVENT
 * ---------------------------------------
 * If application/config/abom.php carries a submodule id that does not
 * match the live `submodule` table, every permission check returns
 * false. The approval buttons vanish, nobody can advance a BOM, and it
 * presents as a permissions problem — someone spends a day in the role
 * matrix before anyone looks at a config file.
 *
 * So a misconfigured id is reported as a CONFIGURATION error, naming the
 * key and the file, in the same spirit as tables_ready().
 *
 * PHP 7.4 compatible.
 */
class Abom_permission_guard
{
    protected $CI;

    /** @var array|null cached diagnostics */
    private $problems = null;

    /** @var array|null resolved key => submodule id, for this request */
    private $resolved = null;

    /**
     * The submodule NAMES Database/abom_003_permissions.sql creates.
     * Constants of that script — it inserts exactly these three strings —
     * so they are a reliable way back to the ids when the config has lost
     * them.
     */
    private $names = array(
        'generator'    => 'AUTOMATION BOM GENERATOR',
        'approvals'    => 'AUTOMATION BOM APPROVALS',
        'master_items' => 'AUTOMATION BOM MASTER ITEMS',
    );

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    // -----------------------------------------------------------------
    // ID RESOLUTION
    // -----------------------------------------------------------------

    /**
     * The submodule id for one permission group.
     *
     * Takes the configured id when there is one. Falls back to LOOKING
     * THE ROW UP BY NAME when there is not.
     *
     * WHY THE FALLBACK EXISTS
     * -----------------------
     * application/config/abom.php carries these ids per site, and the
     * repository ships them null. On 2026-08-11 a deployment copied that
     * file over a working install and blanked all three: every screen in
     * the module dropped to "permissions are not configured" and stayed
     * there until someone read the ids back out of the database by hand.
     *
     * Resolving by name is NOT the guessing the config comment warns
     * against. That warning is about inventing a NUMBER — a guessed id
     * silently points at another module's submodule and every check
     * returns a confident, wrong answer. This looks up an exact string
     * that one script created, verifies the row is attached to this
     * module and is active, and reports a problem if it is not. It
     * cannot resolve to somebody else's row.
     *
     * The configured id still wins when present, so a site that has
     * deliberately pointed a group somewhere else keeps that.
     *
     * @param  string $key  generator | approvals | master_items
     * @return int  0 when it cannot be resolved
     */
    public function submodule_id($key)
    {
        if ($this->resolved === null) {
            $this->resolved = array();
        }

        if (array_key_exists($key, $this->resolved)) {
            return $this->resolved[$key];
        }

        $ids       = $this->CI->config->item('abom_submodule_ids', 'abom');
        $module_id = (int) $this->CI->config->item('abom_module_id', 'abom');

        $configured = (is_array($ids) && isset($ids[$key])) ? (int) $ids[$key] : 0;

        if ($configured > 0) {
            $this->resolved[$key] = $configured;
            return $configured;
        }

        if (!isset($this->names[$key]) || !$this->CI->db->table_exists('submodule')) {
            $this->resolved[$key] = 0;
            return 0;
        }

        $row = $this->CI->db->select('id')
            ->from('submodule')
            ->where('submodule', $this->names[$key])
            ->where('moduleid', $module_id)
            ->where('status', 1)
            ->limit(1)
            ->get()
            ->row();

        $this->resolved[$key] = $row ? (int) $row->id : 0;

        return $this->resolved[$key];
    }

    /**
     * TRUE when a group's id came from the database rather than the
     * config. Surfaced on the guard page so the operator still knows the
     * config needs fixing — the module works, but it is running on a
     * fallback and should not be left there.
     *
     * @return array  keys that were resolved by name
     */
    public function recovered_keys()
    {
        $ids = $this->CI->config->item('abom_submodule_ids', 'abom');
        $out = array();

        foreach ($this->names as $key => $name) {
            $configured = (is_array($ids) && isset($ids[$key])) ? (int) $ids[$key] : 0;

            if ($configured <= 0 && $this->submodule_id($key) > 0) {
                $out[] = $key;
            }
        }

        return $out;
    }

    // -----------------------------------------------------------------
    // CONFIGURATION HEALTH
    // -----------------------------------------------------------------

    /**
     * @return bool true when every configured submodule id exists and is
     *              attached to the module
     */
    public function is_configured()
    {
        return count($this->problems()) === 0;
    }

    /**
     * Human-readable diagnostics, one per misconfigured id.
     *
     * @return array
     */
    public function problems()
    {
        if ($this->problems !== null) {
            return $this->problems;
        }

        $this->problems = array();

        $module_id = (int) $this->CI->config->item('abom_module_id', 'abom');
        $ids       = $this->CI->config->item('abom_submodule_ids', 'abom');

        if (!is_array($ids) || empty($ids)) {
            $this->problems[] = 'abom_submodule_ids is missing from application/config/abom.php.';
            return $this->problems;
        }

        if (!$this->CI->db->table_exists('submodule')) {
            $this->problems[] = 'The `submodule` table does not exist in this database.';
            return $this->problems;
        }

        foreach ($ids as $key => $id) {
            if ($id === null || $id === '' || (int) $id <= 0) {
                // Not set — try to recover it by name before declaring
                // the module unusable. A recovered id is NOT a problem:
                // the checks below still validate the row it found, so a
                // wrong one is caught the same way a wrong configured one
                // would be.
                $recovered = $this->submodule_id($key);

                if ($recovered > 0) {
                    $id = $recovered;
                } else {
                    $this->problems[] = sprintf(
                        'Submodule id for "%s" is not set, and no active submodule named "%s" '
                        . 'exists under module %d to fall back to. Run '
                        . 'Database/abom_003_permissions.sql, then set '
                        . '$config[\'abom_submodule_ids\'][\'%s\'] in application/config/abom.php '
                        . 'to the id it created.',
                        $key,
                        isset($this->names[$key]) ? $this->names[$key] : $key,
                        $module_id,
                        $key
                    );
                    continue;
                }
            }

            $row = $this->CI->db->select('id, moduleid, submodule, status')
                ->from('submodule')
                ->where('id', (int) $id)
                ->limit(1)
                ->get()
                ->row();

            if (!$row) {
                $this->problems[] = sprintf(
                    'Configured submodule id %d ("%s") was not found in the `submodule` table. '
                    . 'Set $config[\'abom_submodule_ids\'][\'%s\'] in application/config/abom.php '
                    . 'to the correct id.',
                    (int) $id, $key, $key
                );
                continue;
            }

            if ((int) $row->moduleid !== $module_id) {
                $this->problems[] = sprintf(
                    'Submodule id %d ("%s") belongs to module %d, not module %d (%s). '
                    . 'Check $config[\'abom_submodule_ids\'] and $config[\'abom_module_id\'] '
                    . 'in application/config/abom.php.',
                    (int) $id, $key, (int) $row->moduleid, $module_id, trim($row->submodule)
                );
                continue;
            }

            if ((int) $row->status !== 1) {
                $this->problems[] = sprintf(
                    'Submodule id %d ("%s", %s) exists but is disabled (status = %d). '
                    . 'Enable it in the `submodule` table.',
                    (int) $id, $key, trim($row->submodule), (int) $row->status
                );
            }
        }

        return $this->problems;
    }

    // -----------------------------------------------------------------
    // PERMISSION CHECK
    // -----------------------------------------------------------------

    /**
     * May the current user perform this action?
     *
     * Returns FALSE when the wiring is unconfigured — callers must ask
     * is_configured() first and show the diagnostic, so an unconfigured
     * module never masquerades as an access denial.
     *
     * @param  string $action  generate|save|check|eng_approve|proc_approve|master_edit
     * @return bool
     */
    public function allows($action)
    {
        if (!$this->is_configured()) {
            return false;
        }

        $perms = $this->CI->config->item('abom_perms', 'abom');
        if (!isset($perms[$action])) {
            return false;
        }

        // Through submodule_id(), so a group whose config id is unset
        // resolves by name rather than denying every user. is_configured()
        // above has already validated whatever this returns.
        $group        = $perms[$action];
        $submodule_id = $this->submodule_id($group);
        $module_id    = (int) $this->CI->config->item('abom_module_id', 'abom');

        if ($submodule_id <= 0) {
            return false;
        }

        if (!$this->CI->db->table_exists('module_capablity')) {
            return false;
        }

        // `module_capablity`.`role_id` holds a USER id, not a role id,
        // despite the column name. Verified against production data:
        // 131 of its 132 distinct values match system_users.user_id,
        // only 40 match user_role.user_role_id, and the values run to
        // 238 while user_role tops out at 104.
        //
        // application/views/common/nav-menu.php does the same thing —
        // `->where('role_id', $user_id)` — so this matches the
        // application's own convention, not just the data.
        //
        // (Master_profile_guard's use of $session['role'] is a different
        // check against a different table, user_role, and is correct
        // there.)
        $session = $this->CI->session->userdata('logged_in');
        $user_id = !empty($session['user_id']) ? (int) $session['user_id'] : 0;

        if ($user_id <= 0) {
            return false;
        }

        return $this->CI->db->from('module_capablity')
            ->where('role_id', $user_id)
            ->where('moduleid', $module_id)
            ->where('submoduleid', $submodule_id)
            ->where('submodule_access', 1)
            ->count_all_results() > 0;
    }
}
