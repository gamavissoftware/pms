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

    public function __construct()
    {
        $this->CI =& get_instance();
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
                $this->problems[] = sprintf(
                    'Submodule id for "%s" is not set. Run Database/abom_003_permissions.sql, '
                    . 'then set $config[\'abom_submodule_ids\'][\'%s\'] in application/config/abom.php '
                    . 'to the id it created.',
                    $key, $key
                );
                continue;
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

        $ids          = $this->CI->config->item('abom_submodule_ids', 'abom');
        $group        = $perms[$action];
        $submodule_id = isset($ids[$group]) ? (int) $ids[$group] : 0;
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
