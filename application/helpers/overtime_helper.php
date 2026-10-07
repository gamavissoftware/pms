<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function ot_e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function ot_hours($minutes) { return number_format((int) $minutes / 60, 2); }
/** Indian digit grouping, kept inside the module so the helper loads without the app's own. */
function ot_money($amount)
{
    $amount = number_format((float) $amount, 2, '.', '');
    $negative = $amount[0] === '-';
    if ($negative) $amount = substr($amount, 1);
    list($whole, $paise) = explode('.', $amount);
    if (strlen($whole) > 3) {
        $last = substr($whole, -3); $rest = substr($whole, 0, -3);
        $whole = preg_replace('/\B(?=(\d{2})+$)/', ',', $rest) . ',' . $last;
    }
    return ($negative ? '-' : '') . $whole . '.' . $paise;
}
/**
 * Employee names as people expect to read them. system_users holds a mix of ALL CAPS,
 * lower case and mixed entries, so every screen title-cases at render time rather than
 * rewriting the master data. Apostrophes and hyphens start a new word too.
 */
function ot_person_name($first, $last = '')
{
    $name = trim(preg_replace('/\s+/', ' ', trim((string) $first) . ' ' . trim((string) $last)));
    if ($name === '') return '';
    if (function_exists('mb_convert_case')) {
        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        return preg_replace_callback("/['\-]\p{Ll}/u", function ($m) { return strtoupper($m[0]); }, $name);
    }
    return ucwords(strtolower($name), " \t\r\n\f\v'-");
}

function ot_status($status) {
    $labels = array('PENDING_LEADER'=>'Awaiting Shubham Sharma (legacy)','PENDING_ADMIN'=>'Awaiting Shubham Sharma','APPROVED'=>'Approved / Assigned','REJECTED'=>'Rejected','CANCELLED'=>'Cancelled');
    return isset($labels[$status]) ? $labels[$status] : $status;
}
function ot_link($action = '', $query = array()) {
    return page_url . 'Overtime' . ($action !== '' ? '/' . $action : '') . ($query ? '?' . http_build_query($query) : '');
}

/** Existing permission tables use role_id as a USER id; user 139 is the fixed approver. */
function ot_user_permissions($db, $user_id)
{
    $map = array('requests'=>array('OVERTIME','OVERTIME REQUESTS'), 'approvals'=>array('OVERTIME','OVERTIME APPROVALS'),
        'reports'=>array('OVERTIME','OVERTIME REPORTS'), 'policy'=>array('MASTER','OVERTIME REQUEST LIMITS'),
        'leaders'=>array('MASTER','OVERTIME REPORTING LEADERS'), 'costs'=>array('MASTER','OVERTIME COST RATES'));
    $permissions = array_fill_keys(array('requests','create','approvals','decide','reports','policy','policy_edit','leaders','leaders_edit','costs','costs_edit'), false);
    // The designated approver must be able to reach and decide every overtime request.
    if ((int)$user_id === 139) foreach (array('requests','approvals','decide','reports') as $key) $permissions[$key] = true;
    // Approval assigns work to a PMS user even if they have no module grants yet.
    if ($db->table_exists('overtime_assignments')) {
        $assigned = $db->query('SELECT id FROM overtime_assignments WHERE user_id=? AND assigned_at IS NOT NULL LIMIT 1', array((int)$user_id));
        if ($assigned && $assigned->row_array()) $permissions['requests'] = true;
    }
    foreach (array('system_modules','submodule','module_access','module_capablity') as $table) if (!$db->table_exists($table)) return $permissions;
    $query = $db->query('SELECT m.modulename,s.submodule,c.madd,c.medit FROM system_modules m
        JOIN submodule s ON s.moduleid=m.id AND s.status=1
        JOIN module_access a ON a.moduleid=m.id AND a.role_id=? AND a.access=1
        JOIN module_capablity c ON c.moduleid=m.id AND c.submoduleid=s.id AND c.role_id=a.role_id AND c.submodule_access=1
        WHERE m.status=1 AND m.modulename IN (?,?)', array((int)$user_id,'OVERTIME','MASTER'));
    if (!$query) return $permissions;
    foreach ($query->result_array() as $row) foreach ($map as $key=>$names) {
        if (strtoupper(trim($row['modulename'])) !== $names[0] || strtoupper(trim($row['submodule'])) !== $names[1]) continue;
        $permissions[$key] = true;
        if ($key === 'requests' && (int)$row['madd'] === 1) $permissions['create'] = true;
        if ($key === 'approvals' && (int)$row['medit'] === 1 && (int)$user_id === 139) $permissions['decide'] = true;
        if (in_array($key,array('policy','leaders','costs'),true) && (int)$row['medit'] === 1) $permissions[$key.'_edit'] = true;
    }
    return $permissions;
}

/**
 * The number on the Overtime menu entry. This runs on every page load for every user,
 * so it stays two indexed counts and returns zero rather than throwing when the module
 * is not installed yet.
 *
 * The approver sees work waiting on them (red); everyone else sees unread updates on
 * their own requests and assignments.
 */
function ot_nav_badge($db, $user_id, $permissions = null)
{
    $badge = array('count' => 0, 'alert' => false, 'title' => '');
    $permissions = $permissions === null ? ot_user_permissions($db, $user_id) : $permissions;
    if (!empty($permissions['decide']) && $db->table_exists('overtime_requests')) {
        $pending = $db->query("SELECT COUNT(*) AS total FROM overtime_requests WHERE status IN ('PENDING_LEADER','PENDING_ADMIN')");
        $pending = $pending ? (int) $pending->row_array()['total'] : 0;
        if ($pending > 0) {
            return array('count' => $pending, 'alert' => true,
                'title' => $pending . ' overtime request' . ($pending === 1 ? '' : 's') . ' waiting for your approval');
        }
    }
    if ($db->table_exists('overtime_notifications')) {
        $unread = $db->query('SELECT COUNT(*) AS total FROM overtime_notifications WHERE user_id=? AND read_at IS NULL', array((int) $user_id));
        $unread = $unread ? (int) $unread->row_array()['total'] : 0;
        if ($unread > 0) $badge = array('count' => $unread, 'alert' => false,
            'title' => $unread . ' unread overtime update' . ($unread === 1 ? '' : 's'));
    }
    return $badge;
}
