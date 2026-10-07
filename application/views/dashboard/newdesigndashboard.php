<?php
$loggedinuserdepartment = $this->session->userdata['logged_in']['department_id'];
$user_id = $this->session->userdata['logged_in']['user_id'];

// PO download visibility: MARKETING (9), ADMINISTRATOR (10) and ACCOUNTS (20)
// departments only, plus the explicitly allowed user ids. Everyone else gets the
// DF download only. Used by both the "All Running DFs" table and the
// "Running DF Task-Wise Delay Report" table.
$po_download_department_ids = array(9, 10, 20);
$po_download_user_ids       = array(139);
$can_download_po = in_array((int) $loggedinuserdepartment, $po_download_department_ids, true)
                || in_array((int) $user_id, $po_download_user_ids, true);

$dynachem_id = $this->session->userdata['logged_in']['dynachem_id'];
$is_admin = 0;
$user_role = $this->session->userdata['logged_in']['role'];
$CI = &get_instance();
$CI->load->model('Dashboard_model');
$adminuserrole = $CI->Dashboard_model->getsuperadminuserole();

if (count($adminuserrole) > 0) {
    if (in_array($user_role, $adminuserrole)) {
        $is_admin = 1;
        $_SESSION['logged_in']['adminuser'] = 1;
    } else {
        $_SESSION['logged_in']['adminuser'] = 0;
    }
}

if($_SESSION['logged_in']['user_id']==189 || $_SESSION['logged_in']['user_id']==209)
{
    $is_admin=1;
    $_SESSION['logged_in']['adminuser'] = 1;
}

$CIA = &get_instance();
$CIA->load->model('Task_model');
$runningdfno = $CIA->Task_model->getallrunningdf();

if ($is_admin == 0) {
    $department = $this->session->userdata['logged_in']['department_id'];
    $departmentid = array();
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
    if ($q->num_rows() > 0) {
        foreach ($q->result() as $row) {
            $departmentid[] = $row->department_id;
            //HOD 
            $_SESSION['logged_in']['adminuser'] = 2;
            $usertyp = 2;
        }
    } else {
        // normal user
        $_SESSION['logged_in']['adminuser'] = 3;
        $usertyp = 3;
    }
} else {
    $usertyp = 1;
}

$DI = &get_instance();
$DI->load->model('Dashboard_model');
$counthelptickets = $CI->Dashboard_model->communication_ticket_system_count();
$counthelpticketsforyou = $CI->Dashboard_model->communication_ticket_for_you();

$USERIDSS = base64_encode($this->session->userdata['logged_in']['user_id']);

$taskLeaderVisibleDepartments = ($is_admin == 0) ? $CIA->Task_model->getLeaderViewDepartmentIds($user_id) : array();
$taskLeaderAssignableDepartments = ($is_admin == 0) ? $CIA->Task_model->getLeaderAssignmentDepartmentIds($user_id) : array();
$canDashboardViewTeamTasks = ($is_admin == 1) || !empty($taskLeaderVisibleDepartments);
$canDashboardAssignTasks = ($is_admin == 1) || !empty($taskLeaderAssignableDepartments);
$showAdminStyleDashboardTabs = ($_SESSION['logged_in']['adminuser'] == 1 || $_SESSION['logged_in']['adminuser'] == 2);
$taskDepartmentFilterIds = array();

if ($is_admin == 0 && $_SESSION['logged_in']['adminuser'] == 2) {
    $taskDepartmentFilterIds = !empty($departmentid) ? array_values(array_unique(array_map('intval', $departmentid))) : array();
} elseif (!empty($loggedinuserdepartment)) {
    $taskDepartmentFilterIds = array((int) $loggedinuserdepartment);
}

$currentTaskUser = $this->db->select('user_id, first_name, last_name, department_id')
    ->from('system_users')
    ->where('user_id', $user_id)
    ->limit(1)
    ->get()
    ->row();
$currentTaskUserName = 'ME';
$taskDashboardVisibleUserRows = array();
$taskDashboardVisibleUserIds = array((int) $user_id);
$taskDashboardVisibleDepartmentIds = array();
if ($currentTaskUser) {
    $currentTaskUserName = strtoupper(trim($currentTaskUser->first_name . ' ' . $currentTaskUser->last_name));
    $taskDashboardVisibleUserRows[(int) $currentTaskUser->user_id] = $currentTaskUser;
    if (!empty($currentTaskUser->department_id)) {
        $taskDashboardVisibleDepartmentIds[] = (int) $currentTaskUser->department_id;
    }
}

if ($_SESSION['logged_in']['adminuser'] == 3 && $this->db->field_exists('project_coordinator_user_id', 'system_users')) {
    $taskDashboardCoworkers = $this->db->select('user_id, first_name, last_name, department_id')
        ->from('system_users')
        ->where('project_coordinator_user_id', (int) $user_id)
        ->where('user_status', 1)
        ->order_by('first_name', 'ASC')
        ->order_by('last_name', 'ASC')
        ->get();

    if ($taskDashboardCoworkers->num_rows() > 0) {
        foreach ($taskDashboardCoworkers->result() as $taskDashboardCoworkerRow) {
            $taskDashboardCoworkerId = (int) $taskDashboardCoworkerRow->user_id;
            if ($taskDashboardCoworkerId <= 0) {
                continue;
            }

            if (!in_array($taskDashboardCoworkerId, $taskDashboardVisibleUserIds, true)) {
                $taskDashboardVisibleUserIds[] = $taskDashboardCoworkerId;
            }

            $taskDashboardVisibleUserRows[$taskDashboardCoworkerId] = $taskDashboardCoworkerRow;

            if (!empty($taskDashboardCoworkerRow->department_id)) {
                $taskDashboardVisibleDepartmentIds[] = (int) $taskDashboardCoworkerRow->department_id;
            }
        }
    }
}

$taskDashboardVisibleDepartmentIds = array_values(array_unique(array_map('intval', $taskDashboardVisibleDepartmentIds)));
if (empty($taskDashboardVisibleDepartmentIds) && !empty($loggedinuserdepartment)) {
    $taskDashboardVisibleDepartmentIds[] = (int) $loggedinuserdepartment;
}

$taskDashboardHasCoworkerScope = ($_SESSION['logged_in']['adminuser'] == 3 && count($taskDashboardVisibleUserIds) > 1);
$taskDashboardPersonalUserIdSqlList = implode(',', array_map('intval', $taskDashboardVisibleUserIds));

$taskUserFilterDefaultOption = '<option value="ALL">ALL</option>';
if ($_SESSION['logged_in']['adminuser'] == 3) {
    if ($taskDashboardHasCoworkerScope) {
        $taskUserFilterDefaultOption = '<option value="ALL">ALL</option>';

        if (!empty($taskDashboardVisibleUserRows)) {
            foreach ($taskDashboardVisibleUserRows as $taskDashboardVisibleUserRow) {
                $taskDashboardVisibleUserName = trim($taskDashboardVisibleUserRow->first_name . ' ' . $taskDashboardVisibleUserRow->last_name);
                if ($taskDashboardVisibleUserName === '') {
                    $taskDashboardVisibleUserName = ($taskDashboardVisibleUserRow->user_id == $user_id) ? 'ME' : ('USER ' . (int) $taskDashboardVisibleUserRow->user_id);
                }

                $taskUserFilterDefaultOption .= '<option value="' . (int) $taskDashboardVisibleUserRow->user_id . '">' . htmlspecialchars(strtoupper($taskDashboardVisibleUserName), ENT_QUOTES, 'UTF-8') . '</option>';
            }
        } else {
            $taskUserFilterDefaultOption .= '<option value="' . (int) $user_id . '">' . htmlspecialchars($currentTaskUserName, ENT_QUOTES, 'UTF-8') . '</option>';
        }
    } else {
        $taskUserFilterDefaultOption = '<option value="' . (int) $user_id . '">' . htmlspecialchars($currentTaskUserName, ENT_QUOTES, 'UTF-8') . '</option>';
    }
} elseif ($_SESSION['logged_in']['adminuser'] == 2) {
    $taskUserFilterDefaultOption = '';
    $currentDepartmentUsers = $this->db->select('user_id, first_name, last_name')
        ->from('system_users')
        ->where('department_id', (int) $loggedinuserdepartment)
        ->where('user_status', 1)
        ->order_by('first_name', 'ASC')
        ->get();

    if ($currentDepartmentUsers->num_rows() > 0) {
        foreach ($currentDepartmentUsers->result() as $teamUserRow) {
            $taskUserFilterDefaultOption .= '<option value="' . (int) $teamUserRow->user_id . '">' . strtoupper(trim($teamUserRow->first_name . ' ' . $teamUserRow->last_name)) . '</option>';
        }
    } else {
        $taskUserFilterDefaultOption = '<option value="' . (int) $user_id . '">' . htmlspecialchars($currentTaskUserName, ENT_QUOTES, 'UTF-8') . '</option>';
    }
}

$taskDepartmentOptionsHtml = '';
if ($_SESSION['logged_in']['adminuser'] != 3 || $taskDashboardHasCoworkerScope) {
    $taskDepartmentOptionsHtml .= '<option value="ALL">ALL</option>';
}

$this->db->select('department, department_id')
    ->from('departments')
    ->where('status', 1)
    ->where('department_id !=', 10)
    ->where('business_loc_id', 2);

if ($is_admin == 0) {
    if ($_SESSION['logged_in']['adminuser'] == 2 && !empty($taskDepartmentFilterIds)) {
        $this->db->where_in('department_id', $taskDepartmentFilterIds, false);
    } elseif ($_SESSION['logged_in']['adminuser'] == 3 && $taskDashboardHasCoworkerScope && !empty($taskDashboardVisibleDepartmentIds)) {
        $this->db->where_in('department_id', $taskDashboardVisibleDepartmentIds, false);
    } elseif (!empty($loggedinuserdepartment)) {
        $this->db->where('department_id', (int) $loggedinuserdepartment);
    }
}

$taskDepartmentQuery = $this->db->order_by('department', 'ASC')->get();
foreach ($taskDepartmentQuery->result() as $taskDepartmentRow) {
    $taskDepartmentOptionsHtml .= '<option value="' . (int) $taskDepartmentRow->department_id . '">' . strtoupper($taskDepartmentRow->department) . '</option>';
}

/*
|--------------------------------------------------------------------------
| World Class Dashboard KPI Layer - Additive Only
|--------------------------------------------------------------------------
| This section is intentionally permission-safe.
| Existing dashboard permission conditions, tabs, popups, charts and tables
| are not modified. We only calculate KPI values according to the existing
| $usertyp and $_SESSION['logged_in']['adminuser'] logic.
*/

date_default_timezone_set('Asia/Kolkata');

$today = date('Y-m-d');
$next15Date = date('Y-m-d', strtotime('+15 days'));
$currentUserId = $this->session->userdata['logged_in']['user_id'];
$currentDepartmentId = $this->session->userdata['logged_in']['department_id'];
$dfDashboardAdminUserIds = array(161, 114, 139, 61, 162, 167);
$isDfDashboardAdmin = in_array((int) $currentUserId, $dfDashboardAdminUserIds, true);

$visibleDepartmentIds = array();

if (isset($departmentid) && is_array($departmentid) && count($departmentid) > 0) {
    $visibleDepartmentIds = $departmentid;
} elseif (!empty($currentDepartmentId)) {
    $visibleDepartmentIds[] = $currentDepartmentId;
}

$dfChangeControlDashboardPermission = false;
$dfChangeControlAssignedTaskCount = 0;

if ($this->db->table_exists('module_access')) {
    $dfModuleAccessEnabled = $this->db->select('id')
        ->from('module_access')
        ->where('role_id', $currentUserId)
        ->where('moduleid', 3)
        ->where('access', '1')
        ->limit(1)
        ->get()
        ->num_rows() > 0;

    if ($dfModuleAccessEnabled && $this->db->table_exists('module_capablity')) {
        $dfChangeControlDashboardPermission = $this->db->select('id')
            ->from('module_capablity')
            ->where('role_id', $currentUserId)
            ->where('moduleid', 3)
            ->where('submoduleid', 42)
            ->where('submodule_access', '1')
            ->limit(1)
            ->get()
            ->num_rows() > 0;
    }
}

if ($this->db->table_exists('df_change_control_departments') && $this->db->table_exists('df_change_control')) {
    $this->db->from('df_change_control_departments a')
        ->join('df_change_control c', 'c.id = a.change_id', 'left')
        ->where_in('a.status', array('ASSIGNED', 'IN_PROGRESS'));

    if ($usertyp == 1) {
        // Admin can monitor all active change-control assignments from the dashboard.
    } elseif ($usertyp == 2 && !empty($visibleDepartmentIds)) {
        $this->db->group_start();
        $this->db->where_in('a.department_id', $visibleDepartmentIds);
        $this->db->or_where('a.assigned_user_id', $currentUserId);
        $this->db->or_where('c.created_by', $currentUserId);
        $this->db->group_end();
    } else {
        $this->db->group_start();
        $this->db->where('a.assigned_user_id', $currentUserId);
        $this->db->or_where('c.created_by', $currentUserId);
        $this->db->group_end();
    }

    $dfChangeControlAssignedTaskCount = (int)$this->db->count_all_results();
}

$dfChangeControlAssignedLabel = ($usertyp == 1)
    ? 'Assigned actions across the dashboard'
    : (($usertyp == 2) ? 'Assigned to your team, directly to you, or requested by you' : 'Assigned to you or requested by you');

if ($usertyp == 1) {
    $dashboardUserTypeLabel = 'ADMIN DASHBOARD';
    $dashboardScopeText = 'Full Management View';
    $dashboardScopeDescription = 'This score is calculated from the full management view across all visible dashboard data.';
    $dashboardScopePoints = array(
        'Overdue Tasks includes all overdue open tasks visible in the management dashboard.',
        'Delayed DF includes all running DFs that currently have at least one delayed open task.',
        'Help Tickets includes all open help tickets visible in the dashboard.',
        'This view is not limited to one person or one department.'
    );
} elseif ($usertyp == 2) {
    $dashboardUserTypeLabel = 'HOD DASHBOARD';
    $dashboardScopeText = 'Department / Team View';
    $dashboardScopeDescription = 'This score is calculated only for the departments or teams mapped to your login.';
    $dashboardScopePoints = array(
        'Overdue Tasks includes overdue open tasks from your mapped departments or teams only.',
        'Delayed DF includes running DFs linked to your mapped departments or teams only.',
        'Help Tickets includes tickets visible inside your department / team dashboard scope.',
        'Other departments outside your access are not included.'
    );
} else {
    $dashboardUserTypeLabel = 'USER DASHBOARD';
    if ($taskDashboardHasCoworkerScope) {
        $dashboardScopeText = 'Personal + Coworker View';
        $dashboardScopeDescription = 'This score is calculated from work directly linked to your login, including users mapped to you as project coordinator.';
        $dashboardScopePoints = array(
            'Overdue Tasks includes your own open tasks plus mapped coworker tasks already overdue.',
            'Delayed DF includes running DFs where at least one delayed open task is assigned inside your visible coworker scope.',
            'Help Tickets still follows the existing ticket visibility rules for your login.',
            'Department or company totals outside your mapped coworker scope are not included.'
        );
    } else {
        $dashboardScopeText = 'Personal Work View';
        $dashboardScopeDescription = 'This score is calculated only from work directly linked to your login, not from department or company totals.';
        $dashboardScopePoints = array(
            'Overdue Tasks includes only your own open tasks whose due date is already crossed.',
            'Delayed DF includes only those running DFs where at least one delayed open task is assigned to you.',
            'Help Tickets includes only help tickets shown for you in your dashboard.',
            'Teammates\' work, department totals, and company totals are not included.'
        );
    }
}

/* Running DF Count - company-wide for every dashboard account */
$this->db->select('df.id');
$this->db->from('df_release df');
$this->db->where('df.on_hold', 0);
$this->db->where('df.df_status', 0);
$kpi_running_df = $this->db->get()->num_rows();

/* Delayed DF Count - company-wide for every dashboard account */
$this->db->select('DISTINCT(df.id) AS df_id', false);
$this->db->from('df_release df');
$this->db->join('task_department_wise_scheduling t', 't.df_id = df.id', 'inner');
$this->db->where('df.on_hold', 0);
$this->db->where('df.df_status', 0);
$this->load->helper('df_delay');
$this->db->where(df_open_overdue_sql($this->db, 't', $today), null, false);

// Reuse this exact set for the portfolio count and row health badges.
$dashboardDelayedDfRows = $this->db->get()->result_array();
$dashboardDelayedDfIds = array_fill_keys(array_map('intval', array_column($dashboardDelayedDfRows, 'df_id')), true);
$kpi_delayed_df = count($dashboardDelayedDfIds);

/* Closed DF Count */
$this->db->select('df.id');
$this->db->from('df_release df');
$this->db->where('df.df_status', 1);

if (!$isDfDashboardAdmin) {
    if ($usertyp == 3 && !empty($taskDashboardPersonalUserIdSqlList)) {
        $this->db->where(
            'EXISTS (
                SELECT 1
                FROM task_department_wise_scheduling user_tasks
                WHERE user_tasks.df_id = df.id
                AND user_tasks.assigned_user IN (' . $taskDashboardPersonalUserIdSqlList . ')
            )',
            null,
            false
        );
    } else {
        $this->db->where(
            'EXISTS (
                SELECT 1
                FROM task_department_wise_scheduling user_tasks
                WHERE user_tasks.df_id = df.id
                AND user_tasks.assigned_user = ' . (int) $currentUserId . '
            )',
            null,
            false
        );
    }
}

$kpi_closed_df = $this->db->get()->num_rows();

/* Overdue Task Count */
$this->db->select('t.id');
$this->db->from('task_department_wise_scheduling t');
$this->db->join('df_release df', 't.df_id = df.id', 'left');
$this->db->where('t.on_hold', 0);
$this->db->where('t.task_status', 0);
$this->db->where('t.end_date <', $today);
$this->db->where('t.department_id !=', 22);
$this->db->group_start();
$this->db->where('t.df_id', 0);
$this->db->or_where('df.df_status', 0);
$this->db->group_end();

if ($usertyp == 2 && !empty($visibleDepartmentIds)) {
    $this->db->where_in('t.department_id', $visibleDepartmentIds);
} elseif ($usertyp == 3) {
    $this->db->where_in('t.assigned_user', $taskDashboardVisibleUserIds, false);
}

$kpi_overdue_tasks = $this->db->get()->num_rows();

/* Ongoing Task Count */
$this->db->select('t.id');
$this->db->from('task_department_wise_scheduling t');
$this->db->join('df_release df', 't.df_id = df.id', 'left');
$this->db->where('t.on_hold', 0);
$this->db->where('t.task_status', 0);
$this->db->where('t.assigned_user !=', 0);
$this->db->where('t.end_date >=', $today);
$this->db->where('t.department_id !=', 22);
$this->db->group_start();
$this->db->where('t.df_id', 0);
$this->db->or_where('df.df_status', 0);
$this->db->group_end();

if ($usertyp == 2 && !empty($visibleDepartmentIds)) {
    $this->db->where_in('t.department_id', $visibleDepartmentIds);
} elseif ($usertyp == 3) {
    $this->db->where_in('t.assigned_user', $taskDashboardVisibleUserIds, false);
}

$kpi_ongoing_tasks = $this->db->get()->num_rows();

/* Tasks Due Today */
$this->db->select('t.id');
$this->db->from('task_department_wise_scheduling t');
$this->db->join('df_release df', 't.df_id = df.id', 'left');
$this->db->where('df.df_status', 0);
$this->db->where('t.on_hold', 0);
$this->db->where('t.task_status', 0);
$this->db->where('t.end_date', $today);

if ($usertyp == 2 && !empty($visibleDepartmentIds)) {
    $this->db->where_in('t.department_id', $visibleDepartmentIds);
} elseif ($usertyp == 3) {
    $this->db->where_in('t.assigned_user', $taskDashboardVisibleUserIds, false);
}

$kpi_due_today_tasks = $this->db->get()->num_rows();

/* Dispatch in Next 15 Days - Task ID 103 as per existing dispatch workflow usage */
$this->db->select('t.id');
$this->db->from('task_department_wise_scheduling t');
$this->db->join('df_release df', 't.df_id = df.id', 'left');
$this->db->where('df.df_status', 0);
$this->db->where('t.on_hold', 0);
$this->db->where('t.task_status', 0);
$this->db->where('t.taskid', 103);
$this->db->where('t.end_date >=', $today);
$this->db->where('t.end_date <=', $next15Date);

if ($usertyp == 2 && !empty($visibleDepartmentIds)) {
    $this->db->where_in('t.department_id', $visibleDepartmentIds);
} elseif ($usertyp == 3) {
    $this->db->where_in('t.assigned_user', $taskDashboardVisibleUserIds, false);
}

$kpi_dispatch_next_15 = $this->db->get()->num_rows();

/* FAT in Next 15 Days - Task ID 93 */
$this->db->select('t.id');
$this->db->from('task_department_wise_scheduling t');
$this->db->join('df_release df', 't.df_id = df.id', 'left');
$this->db->where('df.df_status', 0);
$this->db->where('t.on_hold', 0);
$this->db->where('t.task_status', 0);
$this->db->where('t.taskid', 93);
$this->db->where('t.end_date >=', $today);
$this->db->where('t.end_date <=', $next15Date);

if ($usertyp == 2 && !empty($visibleDepartmentIds)) {
    $this->db->where_in('t.department_id', $visibleDepartmentIds);
} elseif ($usertyp == 3) {
    $this->db->where_in('t.assigned_user', $taskDashboardVisibleUserIds, false);
}

$kpi_fat_next_15 = $this->db->get()->num_rows();

/* My Delegated Tasks */
$this->db->select('id');
$this->db->from('delegation_task');
$this->db->where('task_status', 0);
$this->db->where('delegate_to', $currentUserId);
$kpi_my_delegated_tasks = $this->db->get()->num_rows();

/* Help Tickets - uses existing dashboard model values */
$kpi_help_tickets = ($usertyp == 1) ? intval($counthelptickets) : intval($counthelpticketsforyou);

/* Payment Overdue */
$kpi_payment_overdue_available = $DI->Dashboard_model->payment_overdue_module_available();
$paymentOverdueReportUserId = ($is_admin == 1) ? 'NA' : $currentUserId;
$paymentOverdueReportLink = $kpi_payment_overdue_available
    ? page_url . 'Reporting/paymentoverdue/' . $paymentOverdueReportUserId . '/ALL/ALL'
    : 'javascript:void(0);';
$kpi_payment_overdue = $kpi_payment_overdue_available
    ? (($is_admin == 1)
        ? intval($DI->Dashboard_model->order_payment_overdue())
        : intval($DI->Dashboard_model->order_payment_overdue_agent_wise($currentUserId)))
    : 0;

/* Dashboard Health Score */
$healthBaseScore = 100;
$healthOverdueMultiplier = 3;
$healthOverdueCap = 35;
$healthDelayedDfMultiplier = 5;
$healthDelayedDfCap = 30;
$healthHelpTicketMultiplier = 2;
$healthHelpTicketCap = 20;

$healthOverdueDeduction = min($healthOverdueCap, ($kpi_overdue_tasks * $healthOverdueMultiplier));
$healthDelayedDfDeduction = min($healthDelayedDfCap, ($kpi_delayed_df * $healthDelayedDfMultiplier));
$healthHelpTicketDeduction = min($healthHelpTicketCap, ($kpi_help_tickets * $healthHelpTicketMultiplier));
$healthMinimumScore = max(0, $healthBaseScore - $healthOverdueCap - $healthDelayedDfCap - $healthHelpTicketCap);
$healthTotalDeduction = $healthOverdueDeduction + $healthDelayedDfDeduction + $healthHelpTicketDeduction;

$healthScore = $healthBaseScore - $healthTotalDeduction;
if ($healthScore < 0) {
    $healthScore = 0;
}

$healthBreakdownRows = array(
    array(
        'metric' => 'Overdue Tasks',
        'count' => $kpi_overdue_tasks,
        'multiplier' => $healthOverdueMultiplier,
        'cap' => $healthOverdueCap,
        'deduction' => $healthOverdueDeduction,
        'note' => 'Open tasks whose due date is already crossed.'
    ),
    array(
        'metric' => 'Delayed DF',
        'count' => $kpi_delayed_df,
        'multiplier' => $healthDelayedDfMultiplier,
        'cap' => $healthDelayedDfCap,
        'deduction' => $healthDelayedDfDeduction,
        'note' => 'Running DFs having at least one delayed open task.'
    ),
    array(
        'metric' => 'Help Tickets',
        'count' => $kpi_help_tickets,
        'multiplier' => $healthHelpTicketMultiplier,
        'cap' => $healthHelpTicketCap,
        'deduction' => $healthHelpTicketDeduction,
        'note' => 'Open help tickets visible in your dashboard scope.'
    )
);

if ($healthScore >= 80) {
    $healthLabel = 'GOOD';
    $healthClass = 'health-good';
} elseif ($healthScore >= 50) {
    $healthLabel = 'ATTENTION';
    $healthClass = 'health-warning';
} else {
    $healthLabel = 'CRITICAL';
    $healthClass = 'health-danger';
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> PMS Dashboard</title>
    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo assets_url; ?>css/our_custom_css.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <!-- <link href="<?php echo assets_url; ?>css/html_design.css" rel="stylesheet" type="text/css" /> -->
    <link href="
https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.css
" rel="stylesheet">
    <!-- <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://pms.shubhampack.in/styleasset/dashboard.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/jquery.gritter/css/jquery.gritter.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/jquery.gritter/js/jquery.gritter.min.js"></script>


    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery-noty/2.4.1/packaged/jquery.noty.packaged.min.js"></script>


    <script>
document.addEventListener('DOMContentLoaded', function() {

  var canvas = document.getElementById('orderChart');
  if (!canvas) return;

  var ctx = canvas.getContext('2d');

  var orderData = [{
      "order_type": "0",
      "count": "6",
      "percentage": 42.857142857142854
  }, {
      "order_type": "1",
      "count": "8",
      "percentage": 57.14285714285714
  }];

  var labels = [];
  var data = [];

  orderData.forEach(function(order) {
      labels.push(order.order_type === '1' ? 'Dom' : 'Exp');
      data.push(Number(order.percentage || 0));
  });

  if (window.orderChartInstance) window.orderChartInstance.destroy();

  window.orderChartInstance = new Chart(ctx, {
      type: 'pie',
      data: {
          labels: labels,
          datasets: [{
              data: data,
              backgroundColor: ['#FF6384', '#36A2EB'],
              borderColor: '#ffffff',
              borderWidth: 2,
              radius: '92%'   // ✅ makes pie bigger
          }]
      },
      options: {
          responsive: true,
          maintainAspectRatio: false, // ✅ must for wrapper sizing
          layout: { padding: 6 },
          plugins: {
              legend: {
                  position: 'top',
                  labels: {
                      boxWidth: 12,
                      padding: 12,
                      font: { weight: 'bold', size: 12 }
                  }
              },
              datalabels: {
                  formatter: (value, ctx) => {
                      return ctx.chart.data.labels[ctx.dataIndex] + ': ' + value.toFixed(2) + '%';
                  },
                  color: '#fff',
                  font: function(context){
                      var w = context.chart.width || 400;
                      return { weight: 'bold', size: w < 420 ? 12 : 16 };
                  }
              }
          }
      },
      plugins: [ChartDataLabels]
  });

  // Helps when inside Bootstrap columns/cards
  setTimeout(function(){
    window.orderChartInstance.resize();
  }, 200);

});
</script>



    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
   
    


    <script>
      $(document).ready(function () {
    <?php
    $activeDfDelayRowsForJs = (isset($active_dfs_delay_report) && is_array($active_dfs_delay_report))
        ? $active_dfs_delay_report
        : array();

    // Drop the PO file name entirely rather than hiding the button in JS, so the
    // name never reaches the page source for users without PO download rights.
    if (!$can_download_po) {
        foreach ($activeDfDelayRowsForJs as $delayRowIndex => $delayRow) {
            unset($activeDfDelayRowsForJs[$delayRowIndex]['po_attachment']);
        }
        unset($delayRowIndex, $delayRow);
    }
    ?>
    var activeDfDelayRows = <?php echo json_encode($activeDfDelayRowsForJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    // Function to convert text to uppercase
    function toUpperCase(str) {
        return str ? str.toUpperCase() : ''; // Ensure the string is not null
    }

    // Format date function
    function formatDateToDMY(date) {
        if (!date || date === '0000-00-00' || date === '0000-00-00 00:00:00') {
            return '';
        }
        var match = String(date).match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (!match) {
            return '';
        }
        return match[3] + '-' + match[2] + '-' + match[1];
    }

    function parseLocalDate(date) {
        var match = String(date || '').match(/^(\d{4})-(\d{2})-(\d{2})/);
        return match ? new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3])) : null;
    }

    // Initialize DataTable
    if (!$('#dfTablealldata').length) {
        return;
    }

    var delayedDfCount = 0;
    var overdueTaskCount = 0;
    var pendingApprovalCount = 0;
    $.each(activeDfDelayRows, function (_, dfRow) {
        var overdue = parseInt(dfRow.overdue_task_count || 0, 10);
        if (overdue > 0) { delayedDfCount++; }
        overdueTaskCount += overdue;
        pendingApprovalCount += parseInt(dfRow.pending_approval_count || 0, 10);
    });
    $('#delayReportDfCount').text(activeDfDelayRows.length);
    $('#delayReportAtRiskCount').text(delayedDfCount);
    $('#delayReportTaskCount').text(overdueTaskCount);
    $('#delayReportApprovalCount').text(pendingApprovalCount);

    var table = $('#dfTablealldata').DataTable({
        data: activeDfDelayRows,
        columns: [
            { data: "df_no", render: function(data) { return '<strong>' + toUpperCase(data) + '</strong>'; } },
            { data: "df_description", render: function(data) { return data ? $('<div>').text(data).html() : '-'; } },
            {
                data: "added_on",
                render: function (data) {
                    var formattedDate = formatDateToDMY(data);
                    return formattedDate ? toUpperCase(formattedDate) : '-';
                },
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (type !== 'display') { return parseInt(row.max_delay_days || 0, 10); }
                    if (parseInt(row.on_hold || 0, 10) === 1) { return '<span class="df-health-badge df-health-hold">On Hold</span>'; }
                    if (parseInt(row.overdue_task_count || 0, 10) > 0) { return '<span class="df-health-badge df-health-critical">Needs Attention</span>'; }
                    if (parseInt(row.pending_approval_count || 0, 10) > 0) { return '<span class="df-health-badge df-health-watch">Approval Pending</span>'; }
                    return '<span class="df-health-badge df-health-ontrack">On Track</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    var open = parseInt(row.open_task_count || 0, 10);
                    var overdue = parseInt(row.overdue_task_count || 0, 10);
                    return type === 'display' ? '<strong>' + open + '</strong> open / <span class="df-delay-count">' + overdue + ' delayed</span>' : overdue;
                }
            },
            {
                data: 'max_delay_days',
                render: function(data, type) {
                    var days = parseInt(data || 0, 10);
                    return type === 'display' ? (days > 0 ? '<span class="df-delay-count">' + days + ' days</span>' : '—') : days;
                }
            },
            {
                data: 'next_due_date',
                render: function(data) { return formatDateToDMY(data) || '—'; }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (type !== 'display') { return ''; }
                    var html = row.po_attachment ? '<a href="<?php echo sfdocument; ?>Taskdocument/' + encodeURIComponent(row.po_attachment) + '" class="btn btn-warning btn-xs" target="_blank"><i class="fa fa-file-text-o"></i> PO</a> ' : '';
                    html += row.df_upload ? '<a href="<?php echo sfdocument; ?>Taskdocument/dfattachment/' + encodeURIComponent(row.df_upload) + '" class="btn btn-success btn-xs" target="_blank"><i class="fa fa-file-pdf-o"></i> DF</a>' : '';
                    return html || '—';
                }, orderable:false
            },
            {
                data: null,
                defaultContent:'<button class="btn btn-primary btn-xs view-all-tasks"><i class="fa fa-list"></i> View Tasks</button>',
                orderable: false,
            },
        ],
        order: [[5, 'desc'], [4, 'desc']],
        pageLength: 25,
    });

    // Handle task details popup
    $('#dfTablealldata tbody').on('click', '.view-all-tasks', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var df_id = row.data().id;

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            $.ajax({
                url: '<?php echo page_url . "Dashboard/get_tasks_dfwise/"; ?>' + df_id,
                method: 'GET',
                success: function (response) {
                    var tasks = Array.isArray(response) ? response : JSON.parse(response);

                    // Sort tasks by task order
                    tasks.sort(function (a, b) {
                        var leftOrder = parseInt(a.task_order || a.taskid || 0, 10);
                        var rightOrder = parseInt(b.task_order || b.taskid || 0, 10);
                        return leftOrder - rightOrder;
                    });

                    if (!tasks.length) {
                        row.child('<div class="alert alert-info" style="margin:8px 0;">No scheduled tasks were found for this DF.</div>').show();
                        tr.addClass('shown');
                        return;
                    }

                    var taskHtml =
                        '<table class="table table-bordered tasks-table df-task-child">';
                    taskHtml +=
                        '<thead><tr><th>Department</th><th>Task</th><th>Due Date</th><th>Completed On</th><th>Assigned To</th><th>Status / Delay</th><th>Remarks</th></tr></thead>';
                    taskHtml += '<tbody>';

                    tasks.forEach(function (task) {
                        var rowClass = '';
                        var today = new Date(); today.setHours(0,0,0,0);
                        var dueDate = parseLocalDate(task.end_date);
                        var completedDate = parseLocalDate(task.task_completed_on);
                        var statusHtml = '';
                        var taskStatus = parseInt(task.task_status || 0, 10);
                        if (taskStatus === 0) {
                            if (dueDate && dueDate < today) {
                                var overdueDays = Math.floor((today - dueDate) / 86400000);
                                rowClass = 'overdue';
                                statusHtml = '<span class="task-state task-state-overdue">Open · ' + overdueDays + ' days overdue</span>';
                            } else {
                                statusHtml = '<span class="task-state task-state-open">Open</span>';
                            }
                        } else if (taskStatus === 2) {
                            rowClass = 'preclosure-pending';
                            statusHtml = '<span class="task-state task-state-approval">Pending Approval</span>';
                        } else if (completedDate && dueDate && completedDate > dueDate) {
                            var lateDays = Math.floor((completedDate - dueDate) / 86400000);
                            rowClass = 'late';
                            statusHtml = '<span class="task-state task-state-late">Completed · ' + lateDays + ' days late</span>';
                        } else {
                            rowClass = 'on-time';
                            statusHtml = '<span class="task-state task-state-completed">Completed On Time</span>';
                        }
                        var assignee = $.trim((task.first_name || '') + ' ' + (task.last_name || '')) || 'Unassigned';
                        taskHtml += `<tr class="${rowClass}">
                            <td>${$('<div>').text(task.department_name || 'Unmapped').html()}</td>
                            <td><strong>${$('<div>').text(task.task_name || '').html()}</strong></td>
                            <td>${formatDateToDMY(task.end_date) || '—'}</td>
                            <td>${formatDateToDMY(task.task_completed_on) || '—'}</td>
                            <td>${$('<div>').text(assignee.toLowerCase().replace(/\b\w/g, function(c){return c.toUpperCase();})).html()}</td>
                            <td>${statusHtml}</td>
                            <td>${$('<div>').text(task.remarks || '—').html()}</td>
                        </tr>
                       `;
                    });

                    taskHtml += '</tbody></table>';

                    row.child(taskHtml).show();
                    tr.addClass('shown');
                },
                error: function (xhr, status, error) {
                    row.child('<div class="alert alert-danger" style="margin:8px 0;">Unable to load task details. Please retry.</div>').show();
                    tr.addClass('shown');
                },
            });
        }
    });
});






    </script>





    <style>
        body {
            background: #eef3f9;
        }

        .world-dashboard-hero {
            background: linear-gradient(135deg, #18283b 0%, #315f9d 55%, #4872b8 100%);
            color: #fff;
            border-radius: 22px;
            padding: 26px 28px;
            margin: 18px 0 22px;
            box-shadow: 0 14px 38px rgba(24,40,59,0.28);
            position: relative;
            overflow: hidden;
        }

        .world-dashboard-hero:before {
            content: "";
            position: absolute;
            right: -80px;
            top: -90px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
        }

        .world-dashboard-hero:after {
            content: "";
            position: absolute;
            right: 95px;
            bottom: -95px;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }

        .admin-income-panel {
            margin: 0 0 22px;
            padding: 22px;
            background: #fff;
            border: 1px solid #dfe7f1;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(30, 55, 90, .08);
        }
        .admin-income-head { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; margin-bottom:18px; }
        .admin-income-title { margin:0 0 5px; font-size:21px; font-weight:700; color:#183153; }
        .admin-income-subtitle { margin:0; color:#68778b; font-size:13px; }
        .admin-income-filter { display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
        .admin-income-filter label { display:block; margin-bottom:4px; color:#53657d; font-size:11px; font-weight:700; text-transform:uppercase; }
        .admin-income-filter .form-control { min-width:155px; height:36px; }
        .admin-income-grid { display:grid; grid-template-columns:repeat(5, minmax(0, 1fr)); gap:12px; }
        .admin-income-card { padding:17px; border-radius:12px; border:1px solid #e3eaf3; background:#f8fafc; }
        .admin-income-card.total { color:#fff; border-color:#2454a6; background:linear-gradient(135deg, #173f7a, #2871d5); }
        .admin-income-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; opacity:.82; }
        .admin-income-value { margin:7px 0 5px; font-size:22px; line-height:1.2; font-weight:800; white-space:nowrap; }
        .admin-income-note { font-size:12px; opacity:.76; }
        .admin-income-machine-rows { margin-top:9px; border-top:1px solid #e0e7f0; }
        .admin-income-machine-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px solid #e8edf4; }
        .admin-income-machine-row:last-child { border-bottom:0; }
        .admin-income-machine-label { color:#66788b; font-size:11px; font-weight:700; }
        .admin-income-machine-label small { display:block; margin-top:2px; color:#94a3b2; font-weight:500; }
        .admin-income-machine-value { color:#173f7a; font-size:15px; font-weight:800; white-space:nowrap; }
        .df-delay-panel { background:#fff; border:1px solid #dfe7f1; border-radius:16px; overflow:hidden; box-shadow:0 10px 28px rgba(31,65,108,.08); }
        .df-delay-head { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; padding:18px 20px; color:#fff; background:linear-gradient(135deg,#173f7a,#2871d5); }
        .df-delay-head h3 { margin:0 0 4px; color:#fff; font-size:18px; font-weight:800; }
        .df-delay-head p { margin:0; color:rgba(255,255,255,.82); font-size:11px; }
        .df-delay-kpis { display:flex; flex-wrap:wrap; gap:8px; }
        .df-delay-kpi { min-width:92px; padding:7px 10px; border:1px solid rgba(255,255,255,.25); border-radius:10px; background:rgba(255,255,255,.12); text-align:center; }
        .df-delay-kpi strong { display:block; color:#fff; font-size:18px; line-height:20px; }
        .df-delay-kpi span { color:rgba(255,255,255,.78); font-size:9px; text-transform:uppercase; letter-spacing:.4px; }
        .df-delay-table-wrap { padding:14px; }
        .df-health-badge { display:inline-block; padding:4px 8px; border-radius:12px; font-size:9px; font-weight:800; text-transform:uppercase; }
        .df-health-critical { background:#fee2e2; color:#b91c1c; }
        .df-health-watch { background:#fef3c7; color:#92400e; }
        .df-health-ontrack { background:#dcfce7; color:#166534; }
        .df-health-hold { background:#e2e8f0; color:#475569; }
        .df-delay-count { color:#b91c1c; font-weight:800; }
        .df-task-child { margin:8px 0 !important; background:#fbfdff; }
        .df-task-child th { background:#eaf1fb !important; color:#3a536d !important; }
        .task-state { display:inline-block; padding:3px 7px; border-radius:10px; font-size:9px; font-weight:700; }
        .task-state-overdue,.task-state-late { background:#fee2e2; color:#b91c1c; }
        .task-state-open { background:#dbeafe; color:#1d4ed8; }
        .task-state-completed { background:#dcfce7; color:#166534; }
        .task-state-approval { background:#fef3c7; color:#92400e; }
        .running-portfolio-panel { background:#fff; border:1px solid #dfe7f1; border-radius:16px; overflow:hidden; box-shadow:0 10px 28px rgba(31,65,108,.08); }
        .running-portfolio-head { padding:18px 20px; background:linear-gradient(135deg,#123c69,#1f6fae); color:#fff; }
        .running-portfolio-head h3 { margin:0 0 4px; color:#fff; font-size:18px; font-weight:800; }
        .running-portfolio-head p { margin:0; color:rgba(255,255,255,.8); font-size:11px; }
        .running-portfolio-kpis { display:grid; grid-template-columns:repeat(4,minmax(120px,1fr)); gap:10px; padding:14px; background:#f5f8fc; border-bottom:1px solid #e4ebf3; }
        .running-portfolio-kpi { padding:10px 12px; background:#fff; border:1px solid #e1e8f0; border-radius:10px; }
        .running-portfolio-kpi span { display:block; color:#7b8da0; font-size:9px; font-weight:700; text-transform:uppercase; }
        .running-portfolio-kpi strong { display:block; margin-top:2px; color:#193a5b; font-size:20px; }
        .running-portfolio-table { padding:14px; }
        .portfolio-progress { min-width:95px; }
        .portfolio-progress-bar { height:6px; margin-top:4px; background:#e7edf4; border-radius:8px; overflow:hidden; }
        .portfolio-progress-fill { height:100%; border-radius:8px; background:linear-gradient(90deg,#2f80ed,#56cc9d); }
        .portfolio-delay { display:inline-block; padding:4px 8px; border-radius:12px; font-size:9px; font-weight:800; }
        .portfolio-delay.late { background:#fee2e2; color:#b91c1c; }
        .portfolio-delay.clear { background:#dcfce7; color:#166534; }
        @media(max-width:767px){.df-delay-head{display:block}.df-delay-kpis{margin-top:12px}}
        @media(max-width:767px){.running-portfolio-kpis{grid-template-columns:repeat(2,1fr)}}
        .admin-income-label-row { display:flex; align-items:center; justify-content:space-between; gap:8px; }
        .admin-income-info { position:relative; display:inline-flex; }
        .admin-income-info-trigger { display:inline-flex; align-items:center; justify-content:center; width:23px; height:23px; padding:0; border:1px solid #c9d6e5; border-radius:50%; background:#fff; color:#2454a6; cursor:help; }
        .admin-income-tooltip { position:absolute; z-index:30; top:31px; right:0; width:245px; padding:12px 14px; border-radius:10px; background:#17324f; color:#fff; box-shadow:0 12px 28px rgba(20,47,77,.24); opacity:0; visibility:hidden; transform:translateY(-4px); transition:opacity .15s ease, transform .15s ease; pointer-events:none; text-transform:none; letter-spacing:0; font-size:12px; font-weight:500; }
        .admin-income-tooltip:before { content:""; position:absolute; top:-6px; right:6px; border-width:0 6px 6px; border-style:solid; border-color:transparent transparent #17324f; }
        .admin-income-tooltip-row { display:flex; justify-content:space-between; gap:12px; padding:5px 0; }
        .admin-income-tooltip-row + .admin-income-tooltip-row { border-top:1px solid rgba(255,255,255,.16); }
        .admin-income-tooltip-row strong { white-space:nowrap; }
        .admin-income-info:hover .admin-income-tooltip, .admin-income-info:focus-within .admin-income-tooltip { opacity:1; visibility:visible; transform:translateY(0); }
        @media (max-width:1199px) { .admin-income-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); } }
        @media (max-width:991px) { .admin-income-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); } .admin-income-head { align-items:flex-start; flex-direction:column; } }
        @media (max-width:575px) { .admin-income-grid { grid-template-columns:1fr; } }

        .world-dashboard-title {
            margin: 0;
            color: #fff;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: .4px;
        }

        .world-dashboard-subtitle {
            margin: 8px 0 0;
            color: rgba(255,255,255,0.86);
            font-size: 14px;
        }

        .change-control-tab-trigger {
            width: 100% !important;
            min-height: 78px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 12px;
            white-space: normal;
        }

        .change-control-tab-icon {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .change-control-tab-icon svg {
            width: 24px;
            margin-right: 0 !important;
            fill: #1f2937;
            display: block;
        }

        .change-control-tab-copy {
            flex: 1 1 auto;
            min-width: 0;
            color: #000 !important;
            text-align: left;
            line-height: 1.35;
        }

        .change-control-tab-title {
            display: block;
            font-weight: 700;
        }

        .change-control-tab-meta {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            color: #5f6b7b;
            font-weight: 600;
        }

        .change-control-tab-trigger.has-items {
            background: #eefbf4;
            border-bottom: 1px solid #2f8f5b;
        }

        .change-control-tab-trigger.has-items .change-control-tab-title,
        .change-control-tab-trigger.has-items .change-control-tab-meta {
            color: #14532d !important;
        }

        .change-control-tab-trigger.has-items .change-control-tab-icon svg {
            fill: #14532d;
        }

        .dashboard-task-tab-strip {
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            gap: 14px;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 4px;
            margin-left: 0;
            margin-right: 0;
            -webkit-overflow-scrolling: touch;
        }

        .dashboard-task-tab-strip::-webkit-scrollbar {
            height: 8px;
        }

        .dashboard-task-tab-strip::-webkit-scrollbar-thumb {
            background: #c8d5e6;
            border-radius: 999px;
        }

        .dashboard-task-tab-item {
            flex: 1 1 0;
            min-width: 200px;
            padding-left: 0;
            padding-right: 0;
        }

        .dashboard-task-tab-strip .tablinks {
            float: none;
            width: 100%;
            min-height: 84px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            white-space: normal;
            text-align: left;
            border-bottom: 1px solid transparent;
        }

        .dashboard-task-tab-strip .tablinks > div:first-child,
        .dashboard-task-tab-strip .tablinks > span:first-child {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-task-tab-strip .tablinks > div:last-child,
        .dashboard-task-tab-strip .tablinks > span:last-child {
            flex: 1 1 auto;
            min-width: 0;
            line-height: 1.3;
        }

        .dashboard-task-tab-strip .tablinks svg {
            width: 32px;
            height: 32px;
            display: block;
            fill: currentColor;
        }

        .dashboard-task-tab-strip .counter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            margin-left: 8px;
        }

        @media (max-width: 991px) {
            .change-control-tab-trigger {
                justify-content: center;
                text-align: center;
            }

            .change-control-tab-copy {
                text-align: center;
            }

            .dashboard-task-tab-item {
                min-width: 220px;
            }

            .dashboard-task-tab-strip .tablinks {
                text-align: center;
            }

            .dashboard-task-tab-strip .tablinks > div:last-child,
            .dashboard-task-tab-strip .tablinks > span:last-child {
                text-align: center;
            }
        }

        .world-dashboard-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
            position: relative;
            z-index: 2;
        }

        .world-chip {
            display: inline-block;
            padding: 7px 13px;
/*            border-radius: 22px;*/
            background: #31609d;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
/*            border: 1px solid rgba(255,255,255,0.18);*/
        }

        a.world-chip:hover,
        a.world-chip:focus {
            color: #fff;
            text-decoration: none;
            background: #244f85;
        }

        .world-health-card {
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.20);
            border-radius: 18px;
            padding: 18px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .world-health-score {
            font-size: 38px;
            font-weight: 900;
            color: #fff;
            line-height: 1;
        }

        .world-health-label {
            margin-top: 8px;
            display: inline-block;
            padding: 6px 12px;
            border-radius: 18px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .5px;
        }

        .health-good { background: #2ecc71; color: #fff; }
        .health-warning { background: #f39c12; color: #fff; }
        .health-danger { background: #e74c3c; color: #fff; }
        .world-health-trigger {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 20px;
            background: rgba(255,255,255,0.10);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .4px;
            text-transform: uppercase;
            transition: all .18s ease;
        }

        .world-health-trigger:hover,
        .world-health-trigger:focus {
            color: #fff;
            text-decoration: none;
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.38);
        }

        .health-report-modal .modal-content {
            border: none;
            border-radius: 18px;
            overflow: hidden;
        }

        .health-report-modal .modal-header {
            background: linear-gradient(135deg, #243447 0%, #31609d 100%);
            color: #fff;
            border-bottom: none;
        }

        .health-report-modal .modal-title {
            color: #fff;
            font-weight: 800;
            letter-spacing: .3px;
        }

        .health-report-modal .close {
            color: #fff;
            opacity: 1;
        }

        .health-report-summary {
            background: #f8fbff;
            border: 1px solid #dce8f5;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .health-report-formula {
            font-size: 14px;
            font-weight: 800;
            color: #243447;
            margin-bottom: 8px;
        }

        .health-report-help {
            font-size: 12px;
            color: #5f6f81;
            margin-bottom: 0;
        }

        .health-scope-box {
            background: #fffaf1;
            border: 1px solid #f2dfb0;
            border-radius: 16px;
            padding: 16px 18px 8px;
            margin-bottom: 18px;
        }

        .health-scope-title {
            font-size: 14px;
            font-weight: 800;
            color: #243447;
            margin-bottom: 6px;
        }

        .health-scope-desc {
            font-size: 13px;
            color: #5f6f81;
            margin-bottom: 10px;
        }

        .health-scope-points {
            margin: 0;
            padding-left: 18px;
            color: #3f4e5f;
            font-size: 13px;
        }

        .health-scope-points li {
            margin-bottom: 8px;
        }

        .health-summary-box {
            background: #fff;
            border: 1px solid #e4ebf3;
            border-radius: 14px;
            padding: 14px 12px;
            text-align: center;
            margin-bottom: 14px;
        }

        .health-summary-box .value {
            display: block;
            font-size: 24px;
            font-weight: 900;
            color: #243447;
            line-height: 1.1;
        }

        .health-summary-box .label {
            display: block;
            margin-top: 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6c7b8c;
            font-weight: 700;
        }

        .health-thresholds {
            margin-top: 6px;
            font-size: 12px;
            color: #5f6f81;
        }

        .health-report-table > thead > tr > th {
            background: #eef4fb;
            color: #243447;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: none;
        }

        .health-report-table > tbody > tr > td {
            vertical-align: middle;
        }

        .health-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            background: #eef4fb;
            color: #243447;
        }

        .world-kpi-card {
            background: #fff;
            border-radius: 18px;
            padding: 18px 16px;
            min-height: 122px;
            margin-bottom: 18px;
            border: 1px solid #e4ebf3;
            box-shadow: 0 8px 24px rgba(36,52,71,0.08);
            position: relative;
            overflow: hidden;
            transition: all .22s ease;
        }

        .world-kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 34px rgba(36,52,71,0.16);
        }

        .world-kpi-card:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 6px;
            height: 100%;
        }

        .kpi-blue:before { background: #4872b8; }
        .kpi-red:before { background: #e74c3c; }
        .kpi-orange:before { background: #f39c12; }
        .kpi-green:before { background: #2ecc71; }
        .kpi-purple:before { background: #8e44ad; }
        .kpi-dark:before { background: #243447; }

        .world-kpi-card h3 {
            margin: 0;
            font-size: 31px;
            font-weight: 900;
            color: #243447;
        }

        .world-kpi-card p {
            margin: 8px 0 0;
            color: #738196;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .world-kpi-card small {
            display: block;
            margin-top: 7px;
            color: #8a97a8;
            font-size: 11px;
        }

        .world-kpi-icon {
            position: absolute;
            right: 16px;
            top: 18px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f3f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4872b8;
            font-size: 18px;
        }

        .world-kpi-card a {
            color: inherit;
            text-decoration: none;
        }

        .world-section-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e4ebf3;
            box-shadow: 0 8px 24px rgba(36,52,71,0.08);
            padding: 18px;
            margin-bottom: 22px;
        }

        .world-section-title {
            font-size: 15px;
            font-weight: 900;
            color: #243447;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 14px;
        }

        .world-quick-action {
            display: block;
            background: #f8fbff;
            border: 1px solid #e1eaf4;
            border-radius: 14px;
            padding: 13px 14px;
            margin-bottom: 10px;
            color: #243447;
            font-weight: 800;
            transition: .2s;
        }

        .world-quick-action:hover {
            background: #eef5ff;
            color: #243447;
            transform: translateX(4px);
        }

        .world-risk-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .world-risk-list li {
            padding: 11px 0;
            border-bottom: 1px solid #eef2f7;
            font-size: 13px;
            color: #4a5568;
        }

        .world-risk-list li:last-child { border-bottom: none; }

        .risk-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .risk-red { background: #e74c3c; }
        .risk-orange { background: #f39c12; }
        .risk-green { background: #2ecc71; }

        @media(max-width: 768px) {
            .world-dashboard-title { font-size: 20px; }
            .world-dashboard-hero { padding: 22px; }
            .world-kpi-card { min-height: auto; }
            .world-health-card { margin-top: 18px; }
        }

        .dashboard-page-loader {
            position: fixed;
            inset: 0;
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at 18% 20%, rgba(35, 176, 96, .13), transparent 30%),
                radial-gradient(circle at 82% 78%, rgba(43, 105, 205, .11), transparent 32%),
                rgba(247, 250, 252, .98);
            opacity: 1;
            visibility: visible;
            transition: opacity .38s ease, visibility .38s ease;
        }

        .dashboard-page-loader.is-loaded {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .dashboard-loader-card {
            width: min(360px, 100%);
            padding: 34px 30px 30px;
            text-align: center;
            background: rgba(255, 255, 255, .94);
            border: 1px solid rgba(216, 226, 236, .9);
            border-radius: 22px;
            box-shadow: 0 24px 65px rgba(25, 45, 70, .14);
        }

        .dashboard-loader-mark {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto 22px;
        }

        .dashboard-loader-ring {
            position: absolute;
            inset: 0;
            border: 4px solid #e3f4ea;
            border-top-color: #1cab5e;
            border-right-color: #2575c7;
            border-radius: 50%;
            animation: dashboardLoaderSpin .9s linear infinite;
        }

        .dashboard-loader-icon {
            position: absolute;
            inset: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: linear-gradient(135deg, #168a4a, #20ad60);
            border-radius: 50%;
            box-shadow: 0 7px 18px rgba(22, 138, 74, .25);
            font-size: 21px;
        }

        .dashboard-loader-title {
            margin: 0 0 7px;
            color: #203047;
            font-size: 20px;
            font-weight: 800;
        }

        .dashboard-loader-message {
            margin: 0;
            color: #718096;
            font-size: 13px;
        }

        .dashboard-loader-dots {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 18px;
        }

        .dashboard-loader-dots span {
            width: 7px;
            height: 7px;
            background: #1cab5e;
            border-radius: 50%;
            animation: dashboardLoaderPulse 1.2s ease-in-out infinite;
        }

        .dashboard-loader-dots span:nth-child(2) { animation-delay: .15s; }
        .dashboard-loader-dots span:nth-child(3) { animation-delay: .3s; }

        @keyframes dashboardLoaderSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes dashboardLoaderPulse {
            0%, 70%, 100% { opacity: .3; transform: translateY(0); }
            35% { opacity: 1; transform: translateY(-5px); }
        }

        @media (prefers-reduced-motion: reduce) {
            .dashboard-loader-ring,
            .dashboard-loader-dots span {
                animation: none;
            }
        }
    </style>
</head>


<body>

    <div id="dashboardPageLoader" class="dashboard-page-loader" role="status" aria-live="polite" aria-label="Dashboard is loading">
        <div class="dashboard-loader-card">
            <div class="dashboard-loader-mark" aria-hidden="true">
                <span class="dashboard-loader-ring"></span>
                <span class="dashboard-loader-icon"><i class="fa fa-line-chart"></i></span>
            </div>
            <p class="dashboard-loader-title">Preparing your dashboard</p>
            <p class="dashboard-loader-message">Loading the latest tasks and insights...</p>
            <div class="dashboard-loader-dots" aria-hidden="true">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
    <noscript><style>#dashboardPageLoader { display: none; }</style></noscript>
    <script>
        (function () {
            var loaderStartedAt = Date.now();

            function hideDashboardLoader() {
                var loader = document.getElementById('dashboardPageLoader');
                if (!loader || loader.classList.contains('is-loaded')) return;

                var minimumDisplayTime = 450;
                var remainingTime = Math.max(0, minimumDisplayTime - (Date.now() - loaderStartedAt));
                window.setTimeout(function () {
                    loader.classList.add('is-loaded');
                    window.setTimeout(function () {
                        loader.setAttribute('aria-hidden', 'true');
                    }, 400);
                }, remainingTime);
            }

            window.addEventListener('load', hideDashboardLoader);
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) hideDashboardLoader();
            });

            // Never leave the interface blocked if a third-party asset stalls.
            window.setTimeout(hideDashboardLoader, 8000);
        })();
    </script>


    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>

        <?php $this->load->view('dashboard/preclosertasknotificaiton');?>
        <?php $this->load->view('dashboard/preclosertaskforrishisirapproval');?>
       


 <?php
        if ($user_id == 162 || $user_id == 139 || $user_id==209) {
        } else {?>

        <?php 
$q = $this->db->select('id')->from('popup_logs')->where('user_id', base64_decode($USERIDSS))->where('closedate', date('Y-m-d'))->get();
if ($q->num_rows() > 0) {
    // Popup already shown for the day
} else {

$q1= $this->db->select('id')->from('task_department_wise_scheduling')->where('assigned_user',base64_decode($USERIDSS))->where('task_status',0)->get();

$q2 = $this->db->select('id')->from('communication_ticket_system')->where('user_id',base64_decode($USERIDSS))->where('ticket_status',0)->get();

if($q->num_rows()>0 || $q2->num_rows()>0){

?>

<script>
$(document).ready(function () {
    const rowsPerPage = 5;
    let currentPageTickets = 1;
    let currentPageTasks = 1;

    // Initialize countdown timer
    let countdown = 60;
    const countdownInterval = setInterval(() => {
        $('#countdown').text(`Close available in ${countdown} seconds`);
        countdown--;

        if (countdown < 0) {
            clearInterval(countdownInterval);
            $('#countdown').hide(); // Hide countdown
            $('#close-popup').fadeIn(); // Show close button
        }
    }, 1000);

    // Hide the close button initially
    $('#close-popup').hide();

    // Search functionality
    const searchTable = (inputId, tableBodyId) => {
        $(`#${inputId}`).on('input', function () {
            const value = $(this).val().toLowerCase();
            $(`#${tableBodyId} tr`).filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    };

    // Fetch unresolved tickets and overdue tasks
    $.ajax({
        url: '<?php echo page_url."Task/fetchUnresolvedTickets"; ?>',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log("Response received:", response);
            if (response.status && (response.tickets.length > 0 || response.overdue_tasks.length > 0)) {
                const tickets = response.tickets;
                const tasks = response.overdue_tasks;

                // Populate tickets table
                tickets.forEach((ticket, index) => {
                    $('#tickets-table-body').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${ticket.help_ticket_no}</td>
                            <td>${ticket.df_no}</td>
                            <td>${ticket.task_name}</td>
                            <td>${ticket.remarks}</td>
                            <td>${ticket.title} ${ticket.first_name} ${ticket.last_name}</td>
                            <td>${ticket.added_on}</td>
                        </tr>
                    `);
                });

                // Populate tasks table
                tasks.forEach((task, index) => {
                    $('#tasks-table-body').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${task.df_no}</td>
                            <td>${task.task_name}</td>
                            <td>${task.end_date}</td>
                        </tr>
                    `);
                });

                // Initialize search functionality
                searchTable('ticket-search-input', 'tickets-table-body');
                searchTable('task-search-input', 'tasks-table-body');

                // Show Popup
                $('#blocking-overlay').fadeIn();

                // Close Popup logic
                $(document).on('click', '#close-popup', function () {
                    $('#blocking-overlay').fadeOut();

                    const today = new Date().toISOString().slice(0, 10);
                    localStorage.setItem('popupDisabled', today);

                    $.ajax({
                        url: '<?php echo page_url."Task/closePopup"; ?>',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ tickets }),
                        success: function (response) {
                            console.log('Popup closed and records sent to the server.');
                        },
                        error: function () {
                            console.error('Failed to send ticket data to the server.');
                        }
                    });
                });

                // Button actions
                $('#resolve-btn').on('click', function () {
                    window.location.href = '<?php echo page_url."Task/helpticketsforyou/<?php echo $USERIDSS;?>"; ?>';
                });

                $('#update-task-btn').on('click', function () {
                    window.location.href = '<?php echo page_url."Dashboard"; ?>';
                });
            } else {
                console.log("No unresolved tickets or overdue tasks to show.");
            }
        },
        error: function () {
            console.error('Failed to fetch unresolved tickets and overdue tasks.');
        }
    });
});
</script>

        <div id="blocking-overlay">
            <div id="blocking-overlay-content">
                <div id="countdown">Close available in 60 seconds</div>
                <span id="close-popup">&times;</span>

                <!-- Tickets Section -->
                <h4>Unresolved Help Tickets</h4>
                <input type="text" id="ticket-search-input" placeholder="Search tickets..."
                    style="margin-bottom: 10px; width: 100%; padding: 10px;">
                <table id="tickets-popup-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ticket No</th>
                            <th>DF No</th>
                            <th>Task Name</th>
                            <th>Remarks</th>
                            <th>Created By</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-table-body"></tbody>
                </table>
                <div class="table-button">
                    <button id="resolve-btn" class="btn btn-primary">Resolve Tickets</button>
                </div>

                <!-- Tasks Section -->
                <h4>Overdue Tasks</h4>
                <input type="text" id="task-search-input" placeholder="Search tasks..."
                    style="margin-bottom: 10px; width: 100%; padding: 10px;">
                <table id="tasks-popup-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>DF No</th>
                            <th>Task Name</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody id="tasks-table-body"></tbody>
                </table>
                <div class="table-button">
                    <button id="update-task-btn" class="btn btn-warning">Update Overdue Task</button>
                </div>
            </div>
        </div>

        <?php }?>

<?php }?>

  <?php } ?>

    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <style>
            .chart-container {
                width: 100%;
                max-width: 800px;
                margin: auto;
                background-color: #ffffff;
                padding: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .stats-box {
                padding: 10px 10px;
                color: white;
                border: none;
                border-radius: 15px;
                text-align: center;
                margin-bottom: 30px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s, box-shadow 0.3s;
            }

            .stats-box:hover {
                transform: translateY(-10px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            }

            .stats-box1 {
                padding: 10px 10px;
                color: black;
                border: none;
                border-radius: 15px;
                text-align: center;
                margin-bottom: 30px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s, box-shadow 0.3s;
            }

            .stats-box1:hover {
                transform: translateY(-10px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            }

            /* .stats-value1 {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 10px;
            } */

            .stats-label1 {
                font-size: 14px;
                color: black;
                font-weight: bold;
            }

            .stats-icon {
                font-size: 26px;
                /*      margin-bottom: 20px;*/
                color: rgba(255, 255, 255, 0.8);
            }

            /* .stats-value {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 10px;
            } */

            .stats-label {
                font-size: 14px;
                color: rgba(255, 255, 255, 0.8);
                font-weight: bold;
            }

            hr {
                margin-top: 0px;
                margin-bottom: 10px;
                color: black !important;
                border: 0;
                border-top: 1px solid #c6c2c2;
            }

            .hrclas {
                margin-top: 0px;
                margin-bottom: 10px;
                color: black !important;
                border: 0;
                border-top: 1px solid #000;
            }
        </style>

        <div class="container-fluid">
            <?php $this->load->view('df_change_control/_approval_queue'); ?>

            <!-- WORLD CLASS COMMAND CENTER - ADDITIVE PERMISSION SAFE SECTION -->
            <div class="world-dashboard-hero">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="world-dashboard-title">
                            <i class="fa fa-line-chart"></i> PMS Command Center
                        </h3>

                        <p class="world-dashboard-subtitle">
                            Live overview of DF execution, overdue tasks, help tickets, dispatch planning, FAT planning, and delegation workload.
                        </p>

                        <div class="world-dashboard-meta">
                            <span class="world-chip">
                                <i class="fa fa-user"></i> <?php echo $dashboardUserTypeLabel; ?>
                            </span>

                            <span class="world-chip">
                                <i class="fa fa-calendar"></i> <?php echo date('d-M-Y h:i A'); ?>
                            </span>

                            <span class="world-chip">
                                <i class="fa fa-shield"></i> <?php echo $dashboardScopeText; ?>
                            </span>

                            <span class="world-chip">
                                <i class="fa fa-lock"></i> Permission Safe View
                            </span>

                            <?php if ($usertyp != 3): ?>
                            <a href="<?php echo page_url; ?>Dashboard/df_live_manager" class="world-chip">
                                <i class="fa fa-exchange"></i> Live DF Manager
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="world-health-card">
                            <div class="world-health-score"><?php echo $healthScore; ?>%</div>
                            <div class="world-health-label <?php echo $healthClass; ?>">
                                <?php echo $healthLabel; ?>
                            </div>
                            <div style="font-size:12px; margin-top:8px; color:rgba(255,255,255,.84);">
                                Dashboard Health
                            </div>
                            <button type="button" class="world-health-trigger" data-toggle="modal" data-target="#dashboardHealthReportModal">
                                <i class="fa fa-line-chart"></i> View Calculation Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Temporarily hidden by management request. Set to true to restore the income panel.
            $show_admin_income_panel = false;
            ?>
            <?php if ($show_admin_income_panel && ($usertyp === 1 || (int) $user_role === 1) && isset($admin_income_total_inr)):
                $income_department = isset($admin_income_department) ? $admin_income_department : 'all';
                $income_marketing = isset($admin_income_marketing) && is_array($admin_income_marketing) ? $admin_income_marketing : array();
                $income_marketing_df = isset($admin_income_marketing_df) && is_array($admin_income_marketing_df) ? $admin_income_marketing_df : array();
                $income_spares = isset($admin_income_spares) && is_array($admin_income_spares) ? $admin_income_spares : array();
                $income_spares_breakdown = isset($admin_income_spares_breakdown) && is_array($admin_income_spares_breakdown) ? $admin_income_spares_breakdown : array();
                $income_service = isset($admin_income_service) && is_array($admin_income_service) ? $admin_income_service : array();
                $income_service_breakdown = isset($admin_income_service_breakdown) && is_array($admin_income_service_breakdown) ? $admin_income_service_breakdown : array();
                $income_service_inr = isset($income_service['INR']) ? $income_service['INR'] : array();
                $income_service_usd = isset($income_service['USD']) ? $income_service['USD'] : array();
                $income_fy_label = !empty($admin_income_financial_year_details['label']) ? $admin_income_financial_year_details['label'] : $admin_income_financial_year;
            ?>
            <section class="admin-income-panel">
                <div class="admin-income-head">
                    <div>
                        <h3 class="admin-income-title"><i class="fa fa-money"></i> Total Income — All Departments</h3>
                        <p class="admin-income-subtitle">Confirmed Marketing, Spares and Service orders for <?php echo htmlspecialchars($income_fy_label, ENT_QUOTES, 'UTF-8'); ?>.</p>
                    </div>
                    <form method="get" action="<?php echo page_url; ?>Dashboard" class="admin-income-filter">
                        <div>
                            <label for="income_financial_year">Financial Year</label>
                            <select id="income_financial_year" name="income_financial_year" class="form-control">
                                <?php foreach ($admin_income_financial_year_options as $income_fy_option): ?>
                                    <option value="<?php echo htmlspecialchars($income_fy_option['value'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $admin_income_financial_year === $income_fy_option['value'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($income_fy_option['label'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="income_department">Department</label>
                            <select id="income_department" name="income_department" class="form-control">
                                <option value="all" <?php echo $income_department === 'all' ? 'selected' : ''; ?>>All Departments</option>
                                <option value="marketing" <?php echo $income_department === 'marketing' ? 'selected' : ''; ?>>Marketing Sales</option>
                                <option value="spares" <?php echo $income_department === 'spares' ? 'selected' : ''; ?>>Spares Income</option>
                                <option value="service" <?php echo $income_department === 'service' ? 'selected' : ''; ?>>Service Income</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Show</button>
                    </form>
                </div>
                <div class="admin-income-grid">
                    <div class="admin-income-card total">
                        <div class="admin-income-label">Overall INR Income</div>
                        <div class="admin-income-value">Rs <?php echo number_format((float) $admin_income_total_inr, 0); ?></div>
                        <div class="admin-income-note">Running Marketing DFs + Spares + INR Service; hold DFs excluded</div>
                    </div>
                    <?php if ($income_department === 'all' || $income_department === 'marketing'): ?>
                    <div class="admin-income-card">
                        <div class="admin-income-label-row">
                            <div class="admin-income-label">Marketing - Running DF</div>
                            <span class="admin-income-info">
                                <button type="button" class="admin-income-info-trigger" aria-label="Show Running and Hold DF values by Domestic and International type"><i class="fa fa-info"></i></button>
                                <span class="admin-income-tooltip" role="tooltip">
                                    <span class="admin-income-tooltip-row"><span>Running - Domestic (<?php echo (int) ($income_marketing_df['running_domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_marketing_df['running_domestic_value'] ?? 0), 0); ?></strong></span>
                                    <span class="admin-income-tooltip-row"><span>Running - International (<?php echo (int) ($income_marketing_df['running_international_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_marketing_df['running_international_value'] ?? 0), 0); ?></strong></span>
                                    <span class="admin-income-tooltip-row"><span>Selected FY Active (<?php echo (int) ($income_marketing_df['running_current_fy_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_marketing_df['running_current_fy_value'] ?? 0), 0); ?></strong></span>
                                    <?php if (!empty($income_marketing_df['is_current_financial_year'])): ?><span class="admin-income-tooltip-row"><span>Carry Forward Active (<?php echo (int) ($income_marketing_df['running_carry_forward_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_marketing_df['running_carry_forward_value'] ?? 0), 0); ?></strong></span><?php endif; ?>
                                    <span class="admin-income-tooltip-row"><span>Hold - Domestic (<?php echo (int) ($income_marketing_df['hold_domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_marketing_df['hold_domestic_value'] ?? 0), 0); ?></strong></span>
                                    <span class="admin-income-tooltip-row"><span>Hold - International (<?php echo (int) ($income_marketing_df['hold_international_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_marketing_df['hold_international_value'] ?? 0), 0); ?></strong></span>
                                </span>
                            </span>
                        </div>
                        <div class="admin-income-machine-rows">
                            <div class="admin-income-machine-row">
                                <div class="admin-income-machine-label">Running Machine Value<small><?php echo (int) ($income_marketing_df['running_total_orders'] ?? 0); ?> active DFs</small></div>
                                <div class="admin-income-machine-value">Rs <?php echo number_format((float) ($income_marketing_df['running_total_value'] ?? 0), 0); ?></div>
                            </div>
                            <div class="admin-income-machine-row">
                                <div class="admin-income-machine-label">Dispatched Machine Value<small><?php echo (int) ($income_marketing_df['dispatched_total_orders'] ?? 0); ?> dispatched in <?php echo htmlspecialchars($income_fy_label, ENT_QUOTES, 'UTF-8'); ?></small></div>
                                <div class="admin-income-machine-value">Rs <?php echo number_format((float) ($income_marketing_df['dispatched_total_value'] ?? 0), 0); ?></div>
                            </div>
                        </div>
                        <div class="admin-income-note">
                            <?php if (!empty($income_marketing_df['is_current_financial_year'])): ?>
                                Active DFs: <?php echo (int) ($income_marketing_df['running_current_fy_orders'] ?? 0); ?> selected FY + <?php echo (int) ($income_marketing_df['running_carry_forward_orders'] ?? 0); ?> carry forward = <?php echo (int) ($income_marketing_df['running_total_orders'] ?? 0); ?> total.
                            <?php else: ?>
                                <?php echo (int) ($income_marketing_df['running_total_orders'] ?? 0); ?> active DFs belonging to <?php echo htmlspecialchars($income_fy_label, ENT_QUOTES, 'UTF-8'); ?>. Carry-forward machines are not included for historical FY filters.
                            <?php endif; ?>
                            Hold value is available in the tooltip.
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($income_department === 'all' || $income_department === 'spares'): ?>
                    <div class="admin-income-card">
                        <div class="admin-income-label-row">
                            <div class="admin-income-label">Spares Income (INR)</div>
                            <span class="admin-income-info">
                                <button type="button" class="admin-income-info-trigger" aria-label="Show Domestic and International Spares values"><i class="fa fa-info"></i></button>
                                <span class="admin-income-tooltip" role="tooltip">
                                    <span class="admin-income-tooltip-row"><span>Domestic (<?php echo (int) ($income_spares_breakdown['domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_spares_breakdown['domestic_value'] ?? 0), 0); ?></strong></span>
                                    <span class="admin-income-tooltip-row"><span>International (<?php echo (int) ($income_spares_breakdown['international_orders'] ?? 0); ?>)</span><strong>$<?php echo number_format((float) ($income_spares_breakdown['international_value'] ?? 0), 0); ?></strong></span>
                                </span>
                            </span>
                        </div>
                        <div class="admin-income-value">Rs <?php echo number_format((float) ($income_spares_breakdown['domestic_value'] ?? 0), 0); ?></div>
                        <div class="admin-income-note"><?php echo (int) ($income_spares_breakdown['domestic_orders'] ?? 0); ?> domestic finalized orders</div>
                    </div>
                    <?php endif; ?>
                    <?php if ($income_department === 'all' || $income_department === 'service'): ?>
                    <div class="admin-income-card">
                        <div class="admin-income-label-row">
                            <div class="admin-income-label">Service Income (INR)</div>
                            <span class="admin-income-info">
                                <button type="button" class="admin-income-info-trigger" aria-label="Show Domestic and International Service values"><i class="fa fa-info"></i></button>
                                <span class="admin-income-tooltip" role="tooltip">
                                    <span class="admin-income-tooltip-row"><span>Domestic (<?php echo (int) ($income_service_breakdown['domestic_orders'] ?? 0); ?>)</span><strong>Rs <?php echo number_format((float) ($income_service_breakdown['domestic_value'] ?? 0), 0); ?></strong></span>
                                    <span class="admin-income-tooltip-row"><span>International (<?php echo (int) ($income_service_breakdown['international_orders'] ?? 0); ?>)</span><strong>$<?php echo number_format((float) ($income_service_breakdown['international_value'] ?? 0), 0); ?></strong></span>
                                </span>
                            </span>
                        </div>
                        <div class="admin-income-value">Rs <?php echo number_format(!empty($income_service_inr['total_order_value']) ? (float) $income_service_inr['total_order_value'] : 0, 0); ?></div>
                        <div class="admin-income-note"><?php echo !empty($income_service_inr['total_orders']) ? (int) $income_service_inr['total_orders'] : 0; ?> won orders</div>
                    </div>
                    <?php if (!empty($income_service_usd['total_order_value'])): ?>
                    <div class="admin-income-card">
                        <div class="admin-income-label">Service Income (USD)</div>
                        <div class="admin-income-value">$<?php echo number_format((float) $income_service_usd['total_order_value'], 0); ?></div>
                        <div class="admin-income-note">Shown separately; not added to the INR total</div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; ?>

            <div class="row">

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-blue">
                        <a href="<?php echo page_url; ?>Task/dfreleasedashboard/">
                            <div class="world-kpi-icon"><i class="fa fa-folder-open"></i></div>
                            <h3><?php echo $kpi_running_df; ?></h3>
                            <p>Running DF</p>
                            <small>All active execution files company-wide</small>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-red">
                        <a href="<?php echo page_url; ?>Task/dfreleasedashboard/1">
                            <div class="world-kpi-icon"><i class="fa fa-warning"></i></div>
                            <h3><?php echo $kpi_delayed_df; ?></h3>
                            <p>Delayed DF</p>
                            <small>All DFs impacted by delayed tasks company-wide</small>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-orange">
                        <a href="<?php echo page_url; ?>Dashboard/runningdftaskdelay">
                            <div class="world-kpi-icon"><i class="fa fa-clock-o"></i></div>
                            <h3><?php echo $kpi_closed_df; ?></h3>
                            <p>Closed DF</p>
                            <small><?php echo $isDfDashboardAdmin ? 'DF completed till now' : 'Closed DFs with tasks assigned to you'; ?></small>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-green">
                        <a href="<?php echo page_url; ?>Reporting/duetodaytasks">
                            <div class="world-kpi-icon"><i class="fa fa-calendar-check-o"></i></div>
                            <h3><?php echo $kpi_due_today_tasks; ?></h3>
                            <p>Due Today</p>
                            <small>Tasks requiring action today</small>
                        </a>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-purple">
                        <a href="<?php echo page_url; ?>Reporting/dispatchinnext15days">
                            <div class="world-kpi-icon"><i class="fa fa-truck"></i></div>
                            <h3><?php echo $kpi_dispatch_next_15; ?></h3>
                            <p>Dispatch Next 15 Days</p>
                            <small>Upcoming dispatch schedule</small>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-dark">
                        <a href="<?php echo page_url; ?>Reporting/fatsinnext15days">
                            <div class="world-kpi-icon"><i class="fa fa-cogs"></i></div>
                            <h3><?php echo $kpi_fat_next_15; ?></h3>
                            <p>FAT Next 15 Days</p>
                            <small>Factory acceptance planning</small>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-blue">
                        <a href="<?php echo page_url; ?>Delegation/delegated_task">
                            <div class="world-kpi-icon"><i class="fa fa-share-square-o"></i></div>
                            <h3><?php echo $kpi_my_delegated_tasks; ?></h3>
                            <p>My Delegated Tasks</p>
                            <small>Tasks assigned to you</small>
                        </a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="world-kpi-card kpi-red">
                        <a href="<?php echo page_url; ?>Task/helpticketsforyou/<?php echo $USERIDSS; ?>">
                            <div class="world-kpi-icon"><i class="fa fa-life-ring"></i></div>
                            <h3><?php echo $kpi_help_tickets; ?></h3>
                            <p><?php echo ($usertyp == 1) ? 'Open Help Tickets' : 'Tickets For You'; ?></p>
                            <small>Pending communication tickets on active DF only</small>
                        </a>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-8">
                    <div class="world-section-card">
                        <div class="world-section-title">
                            <i class="fa fa-bolt"></i> Quick Actions
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <a class="world-quick-action" href="<?php echo page_url; ?>Delegation/new_delegation_task">
                                    <i class="fa fa-plus-circle"></i> Delegate New Task
                                </a>
                            </div>

                            <div class="col-md-4">
                                <a class="world-quick-action" href="<?php echo page_url; ?>Delegation/delegated_task">
                                    <i class="fa fa-tasks"></i> My Task Dashboard
                                </a>
                            </div>

                            <div class="col-md-4">
                                <a class="world-quick-action" href="<?php echo page_url; ?>Delegation/delegation_history/NA/NA/All/All">
                                    <i class="fa fa-history"></i> Delegation History
                                </a>
                            </div>

                            <?php if($usertyp == 1 || $usertyp == 2) { ?>
                                <div class="col-md-4">
                                    <a class="world-quick-action" href="<?php echo page_url; ?>Dashboard/df_full_detail" target="_blank">
                                        <i class="fa fa-filter"></i> Filter Information by DF
                                    </a>
                                </div>

                                <div class="col-md-4">
                                    <a class="world-quick-action" href="<?php echo page_url; ?>Dashboard/closeddf" target="_blank">
                                        <i class="fa fa-archive"></i> Closed DF
                                    </a>
                                </div>

                                <div class="col-md-4">
                                    <a class="world-quick-action" href="<?php echo page_url; ?>Df_reports/weekly_delay_report">
                                        <i class="fa fa-bar-chart"></i> Weekly Dept. Delay
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="world-section-card">
                        <div class="world-section-title">
                            <i class="fa fa-exclamation-circle"></i> Risk Summary
                        </div>

                        <ul class="world-risk-list">
                            <li>
                                <span class="risk-dot <?php echo ($kpi_overdue_tasks > 0) ? 'risk-red' : 'risk-green'; ?>"></span>
                                Overdue Tasks: <strong><?php echo $kpi_overdue_tasks; ?></strong>
                            </li>

                            <!-- <li>
                                <span class="risk-dot <?php echo ($kpi_delayed_df > 0) ? 'risk-red' : 'risk-green'; ?>"></span>
                                Delayed DF: <strong><?php echo $kpi_delayed_df; ?></strong>
                            </li> -->

                            <li>
                                <span class="risk-dot <?php echo ($kpi_help_tickets > 0) ? 'risk-orange' : 'risk-green'; ?>"></span>
                                Pending Tickets: <strong><?php echo $kpi_help_tickets; ?></strong>
                            </li>

                            <li>
                                <span class="risk-dot <?php echo ($kpi_due_today_tasks > 0) ? 'risk-orange' : 'risk-green'; ?>"></span>
                                Due Today: <strong><?php echo $kpi_due_today_tasks; ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- END WORLD CLASS COMMAND CENTER -->


        <?php 
        if($user_id==139 || $user_id==61 || $user_id== 161){

            $dynachem_id = $this->session->userdata['logged_in']['dynachem_id'];

            $dyid = base64_encode($dynachem_id);
        ?>
<!-- <div class="row">
    <div class="col-sm-12">
        <a href="https://crm.dynachemdeepindia.com/index.php/User/switch_to_dynachem/<?php echo $dyid; ?>" target="_blank" ><span class="btn btn-sm btn-dark pull-right">Click to Login Dynachem</span></a>
    </div>
</div> -->
             <?php }?>
             <!----************************=================================*******************----->
            <?php
            if (pms_is_super_admin() || $this->session->userdata['logged_in']['role']==27 || $this->session->userdata['logged_in']['user_id'] == 139 || $this->session->userdata['logged_in']['user_id'] == 209) {

                $year = date('Y');
                $month = date('m');
                $date = new DateTime("$year-$month-01");
                $date->modify('last day of this month');
                $ldate = $date->format('Y-m-d');
                $ssdate = date('Y-m')."-01";
            ?>

            <div class="neo-section-shell">
                <div class="neo-section-head">
                    <div>
                        <h3 class="neo-section-title">DF & Opportunity Information</h3>
                        <div class="neo-section-subtitle">Operational DF visibility with delay tracking, running value, purchase linkage, and department-wise delay insight.</div>
                    </div>

                    <div class="neo-badge-group">
                        <a href="<?php echo page_url; ?>Dashboard/daily_df_progress_report" target="_blank" class="neo-action-btn neo-action-blue">Daily DF Progress</a>
                        <a href="<?php echo page_url; ?>Dashboard/daily_planned_task_report" target="_blank" class="neo-action-btn neo-action-blue">Daily Planned Tasks</a>
                        <a href="<?php echo page_url; ?>Dashboard/closeddf" target="_blank" class="neo-action-btn neo-action-red">Closed DF</a>
                        <a href="<?php echo page_url; ?>Dashboard/df_full_detail" target="_blank" class="neo-action-btn neo-action-blue">Filter Information by DF</a>
                    </div>
                </div>

                <div class="row neo-grid-gap">
                    <div class="col-md-9">
                        <div class="row neo-mini-grid">

                            <div class="col-md-4 col-sm-6">
                                <a href="<?php echo page_url; ?>Task/dfreleasedashboard/" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-blue">
                                        <div class="neo-metric-icon"><i class="fa fa-folder-open-o"></i></div>
                                        <div class="neo-metric-label">Running DF</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $q = $this->db->select('id')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
                                            echo $q->num_rows();
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">Active DFs in progress</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <a href="<?php echo page_url; ?>Task/dfreleasedashboard/1" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-red">
                                        <div class="neo-metric-icon"><i class="fa fa-exclamation-triangle"></i></div>
                                        <div class="neo-metric-label">DF Delayed</div>
                                        <div class="neo-metric-value">
                                            <?php echo $kpi_delayed_df; ?>
                                        </div>
                                        <div class="neo-metric-foot">Delayed running DFs</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <a href="<?php echo page_url; ?>Reporting/penalitydf" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-orange">
                                        <div class="neo-metric-icon"><i class="fa fa-gavel"></i></div>
                                        <div class="neo-metric-label">Penalty DF</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $q = $this->db->select('id')->from('poreceived')->where('penalityamount!=', 0)->get();
                                            echo $q->num_rows();
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">Penalty-linked DFs</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <a href="<?php echo page_url;?>Df_reports/weekly_delay_report" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-purple">
                                        <div class="neo-metric-icon"><i class="fa fa-line-chart"></i></div>
                                        <div class="neo-metric-label">Weekly Dept. Delay</div>
                                        <div class="neo-metric-value">View</div>
                                        <div class="neo-metric-foot">Weekly department delay summary</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="neo-metric-card neo-metric-dark">
                                    <div class="neo-metric-icon"><i class="fa fa-random"></i></div>
                                    <div class="neo-metric-label">DF With IOM</div>
                                    <div class="neo-metric-value">0</div>
                                    <div class="neo-metric-foot">Mapped with IOM</div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="neo-metric-card neo-metric-green">
                                    <div class="neo-metric-icon"><i class="fa fa-inr"></i></div>
                                    <div class="neo-metric-label">Running DF(s) Value</div>
                                    <div class="neo-metric-value">
                                        <?php
                                        /* Safe lead_id based query. Avoids Database Error 1054 caused by missing poreceived.df_id column. */
                                        $amount = array();
                                        $amount[] = 0;

                                        $q = $this->db->select('id')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
                                        if ($q->num_rows() > 0) {
                                            foreach ($q->result() as $dfrow) {
                                                if (!empty($dfrow->id)) {
                                                    $q2 = $this->db->select('order_value')->from('poreceived')->where('df_id', $dfrow->id)->get();
                                                    if ($q2->num_rows() > 0) {
                                                        foreach ($q2->result() as $porow) {
                                                            $amount[] = $porow->order_value;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        echo $DI->Dashboard_model->formatIndianNumber(array_sum($amount));
                                        ?>
                                    </div>
                                    <div class="neo-metric-foot">Open DF commercial value</div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <a href="<?php echo page_url;?>Task/new_dfreleasedashboard/" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-blue">
                                        <div class="neo-metric-icon"><i class="fa fa-shopping-basket"></i></div>
                                        <div class="neo-metric-label">Purchased Items</div>
                                        <div class="neo-metric-value">View</div>
                                        <div class="neo-metric-foot">Purchased material dashboard</div>
                                    </div>
                                </a>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="neo-leaderboard-card">
                            <div class="neo-chart-title">Dept. Delay</div>
                            <div class="neo-chart-caption">Department-wise pending task pressure</div>

                            <div class="neo-table-wrap">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Dept Name</th>
                                            <th>Total Task</th>
                                            <th>Delay In Days</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($department_delay_report as $row): ?>
                                            <tr>
                                                <td><?= $row['department']; ?></td>
                                                <td><a href="<?= page_url.'Task/pending_tasks/' . $row['department_id']; ?>"><?= $row['total_tasks']; ?></a></td>
                                                <td><?= $row['max_delay_days']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="neo-section-shell">
                        <div class="neo-section-head">
                            <div>
                                <h3 class="neo-section-title">New Business Opportunity</h3>
                                <div class="neo-section-subtitle">Lead pipeline movement in the last 15 days.</div>
                            </div>
                        </div>

                        <div class="row neo-mini-grid">
                            <div class="col-md-6 col-sm-6">
                                <a href="<?php echo page_url; ?>Leads/lead_stages/1" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-blue neo-wide-card">
                                        <div class="neo-metric-icon"><i class="fa fa-bullseye"></i></div>
                                        <div class="neo-metric-label">New Opportunity In Last 15 Days</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $date = new DateTime();
                                            $date->modify('-15 days');
                                            $createdate =  $date->format('Y-m-d');
                                            $q = $this->db->select('id')->from('leads')->where('create_date>=', $createdate)->get();
                                            echo $q->num_rows();
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">Fresh opportunities added</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <a href="<?php echo page_url; ?>Leads/lead_stages/33" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-green neo-wide-card">
                                        <div class="neo-metric-icon"><i class="fa fa-file-text-o"></i></div>
                                        <div class="neo-metric-label">Quotation Sent In Last 15 Days</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $date = new DateTime();
                                            $date->modify('-15 days');
                                            $createdate =  $date->format('Y-m-d');
                                            $stageid = 33;
                                            $last15daysquotationcount = $DI->Dashboard_model->lead_stage_counts_in_15days($createdate, $stageid);
                                            echo $last15daysquotationcount;
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">Quotation stage movement</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-12">
                                <?php
                                $date = new DateTime();
                                $date->modify('-15 days');
                                $enddate =  $date->format('Y-m-d');
                                $startdate = date('Y-m-d');
                                ?>
                                <a href="<?php echo page_url; ?>Task/receivedpolist/<?php echo base64_encode($startdate); ?>/<?php echo base64_encode($enddate); ?>" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-purple neo-wide-card">
                                        <div class="neo-metric-icon"><i class="fa fa-check-square-o"></i></div>
                                        <div class="neo-metric-label">Opportunity Converted In Last 15 Days</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $convertedOpportunity = array();
                                            $convertedOpportunity[] = 0;
                                            $q = $this->db->select('id')->from('progress_remarks')->where('lead_status', 35)->where('added_on >=', $enddate)->get();
                                            echo $q->num_rows();
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">PO conversion in the last 15 days</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="neo-section-shell">
                        <div class="neo-section-head">
                            <div>
                                <h3 class="neo-section-title">Factory Information</h3>
                                <div class="neo-section-subtitle">Execution readiness, dispatch planning, and FAT visibility.</div>
                            </div>
                        </div>

                        <div class="row neo-mini-grid">
                            <div class="col-md-6 col-sm-6">
                                <a href="<?php echo page_url; ?>Reporting/machinereadyonfloor" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-blue neo-wide-card">
                                        <div class="neo-metric-icon"><i class="fa fa-industry"></i></div>
                                        <div class="neo-metric-label">Ready Machine(s) On Floor</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $readyonfloor = array();
                                            $readyonfloor[] = 0;
                                            $q = $this->db->select('task_id')->from('task_management')->where('machinereadyonfloor', 1)->get();
                                            if ($q->num_rows() > 0) {
                                                foreach ($q->result() as $row);
                                                $q2 = $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b', 'a.df_id=b.id', 'left')->where('a.taskid', $row->task_id)->where('a.task_status', 1)->where('b.df_status', 0)->get();
                                                if ($q2->num_rows() > 0) {
                                                    foreach ($q2->result() as $row2) {
                                                        $readyonfloor[] = 1;
                                                    }
                                                }
                                            }
                                            echo array_sum($readyonfloor);
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">Completed and ready on floor</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <a href="<?php echo page_url; ?>Reporting/dispatchinnext15days" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-green neo-wide-card">
                                        <div class="neo-metric-icon"><i class="fa fa-truck"></i></div>
                                        <div class="neo-metric-label">Dispatch In Next 15 Days</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $dispatchcount = array();
                                            $dispatchcount[] = 0;
                                            $originalDate = date('Y-m-d');
                                            $date = new DateTime($originalDate);
                                            $date->modify('+15 days');
                                            $newDate = $date->format('Y-m-d');
                                            $q = $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid', 103)->where('end_date BETWEEN "' . date('Y-m-d', strtotime($originalDate)) . '" and "' . date('Y-m-d', strtotime($newDate)) . '"')->get();
                                            if ($q->num_rows() > 0) {
                                                foreach ($q->result() as $row) {
                                                    $dispatchcount[] = 1;
                                                }
                                            }
                                            echo array_sum($dispatchcount);
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">Upcoming dispatch line-up</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-12">
                                <a href="<?php echo page_url; ?>Reporting/fatsinnext15days" class="neo-metric-link">
                                    <div class="neo-metric-card neo-metric-orange neo-wide-card">
                                        <div class="neo-metric-icon"><i class="fa fa-cogs"></i></div>
                                        <div class="neo-metric-label">FAT(s) In Next 15 Days</div>
                                        <div class="neo-metric-value">
                                            <?php
                                            $dispatchcount = array();
                                            $dispatchcount[] = 0;
                                            $originalDate = date('Y-m-d');
                                            $date = new DateTime($originalDate);
                                            $date->modify('+15 days');
                                            $newDate = $date->format('Y-m-d');
                                            $q = $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid', 93)->where('end_date BETWEEN "' . date('Y-m-d', strtotime($originalDate)) . '" and "' . date('Y-m-d', strtotime($newDate)) . '"')->get();
                                            if ($q->num_rows() > 0) {
                                                foreach ($q->result() as $row) {
                                                    $dispatchcount[] = 1;
                                                }
                                            }
                                            echo array_sum($dispatchcount);
                                            ?>
                                        </div>
                                        <div class="neo-metric-foot">Factory acceptance planning horizon</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="neo-section-shell">
                <div class="neo-section-head">
                    <div>
                        <h3 class="neo-section-title">Sales & Revenue Overview</h3>
                        <div class="neo-section-subtitle">Live commercial insights for export/domestic split and monthly company revenue performance.</div>
                    </div>

                    <div class="neo-badge-group">
                        <span class="neo-head-badge"><i class="fa fa-pie-chart"></i> Export vs Domestic</span>
                        <span class="neo-head-badge"><i class="fa fa-bar-chart"></i> Monthly Revenue</span>
                        <a href="<?php echo page_url;?>Dashboard/df_dispatch_report/<?php echo $ssdate;?>/<?php echo $ldate;?>" class="neo-action-btn neo-action-red">DF Dispatch Report</a>
                    </div>
                </div>

                <div class="row neo-grid-gap">
                    <div class="col-md-6">
                        <div class="neo-chart-card">
                            <div class="neo-chart-header">
                                <div>
                                    <h4 class="neo-chart-title">Sales (Export/Domestic)</h4>
                                    <div class="neo-chart-caption">Current order mix distribution</div>
                                </div>

                                <div class="neo-legend-wrap">
                                    <span class="neo-legend-item"><span class="neo-legend-dot neo-dot-pink"></span> Export</span>
                                    <span class="neo-legend-item"><span class="neo-legend-dot neo-dot-blue"></span> Domestic</span>
                                </div>
                            </div>

                            <div class="neo-chart-body">
                                <div class="neo-chart-wrap pieChartWrap">
                                    <canvas id="orderChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="neo-chart-card">
                            <div class="neo-chart-header">
                                <div>
                                    <h4 class="neo-chart-title">Company Revenue Information</h4>
                                    <div class="neo-chart-caption">Monthly target: 10 CR.</div>
                                </div>

                                <div class="neo-legend-wrap">
                                    <span class="neo-legend-item"><span class="neo-legend-dot neo-dot-red"></span> Up to 5 CR.</span>
                                    <span class="neo-legend-item"><span class="neo-legend-dot neo-dot-yellow"></span> Up to 9 CR.</span>
                                    <span class="neo-legend-item"><span class="neo-legend-dot neo-dot-green"></span> Above 9 CR.</span>
                                </div>
                            </div>

                            <div class="neo-chart-body">
                                <div class="neo-chart-wrap chart-wrap">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function () {

              var canvas = document.getElementById('salesChart');
              if (!canvas) return;

              var ctx = canvas.getContext('2d');

              const salesData = <?php echo json_encode($sales_data); ?>;
              const months = salesData.map(d => d.month);
              const orderValues = salesData.map(d => parseFloat(d.order_value || 0));

              const colors = orderValues.map(value => {
                if (value <= 50000000) return '#FF5733';
                if (value < 90000000) return '#E8EC00';
                return '#2B8B3B';
              });

              const formatIndianNumber = (num) => {
                num = Math.round(num).toString();
                let lastThree = num.substring(num.length - 3);
                let otherNumbers = num.substring(0, num.length - 3);
                if (otherNumbers !== '') lastThree = ',' + lastThree;
                let res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
                return '₹' + res;
              };

              if (window.salesChartInstance) window.salesChartInstance.destroy();

              window.salesChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: months,
                  datasets: [{
                    label: 'Monthly Sales (in ₹)',
                    data: orderValues,
                    backgroundColor: colors,
                    borderRadius: 8,
                    borderSkipped: false
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  layout: { padding: { top: 10, left: 8, right: 12, bottom: 0 } },
                  scales: {
                    x: {
                      grid: { display: false },
                      ticks: { font: { size: 11, weight: 'bold' }, maxRotation: 0, autoSkip: true }
                    },
                    y: {
                      beginAtZero: true,
                      ticks: {
                        callback: (value) => formatIndianNumber(value),
                        font: { size: 11, weight: 'bold' }
                      }
                    }
                  },
                  plugins: {
                    legend: { display: false },
                    tooltip: {
                      callbacks: {
                        label: (context) => {
                          let label = context.dataset.label ? context.dataset.label + ': ' : '';
                          return label + formatIndianNumber(context.parsed.y || 0);
                        }
                      }
                    }
                  }
                }
              });

              const wrapper = canvas.closest('.chart-wrap');
              if (wrapper && window.ResizeObserver) {
                const ro = new ResizeObserver(() => window.salesChartInstance.resize());
                ro.observe(wrapper);
              }

            });
            </script>

            <div class="neo-section-shell">
                <div class="neo-section-head">
                    <div>
                        <h3 class="neo-section-title">Payment Information</h3>
                        <div class="neo-section-subtitle">Payment pipeline, overdue visibility and recent collection summary.</div>
                    </div>
                </div>

                <div class="row neo-mini-grid">
                    <div class="col-md-4 col-sm-6">
                        <div class="neo-metric-card neo-metric-blue">
                            <div class="neo-metric-icon"><i class="fa fa-inr"></i></div>
                            <div class="neo-metric-label">Payment Credited In Last 15 Days</div>
                            <div class="neo-metric-value">
                                <?php
                                $totalrecaivedamount = array();
                                $totalrecaivedamount[] = 0;
                                $paymentcreditedin15days = $DI->Dashboard_model->paymentcreditedin15days();
                                if (!empty($paymentcreditedin15days)) {
                                    foreach ($paymentcreditedin15days as $row) {
                                        $totalrecaivedamount[] = $row->amount_received;
                                    }
                                }
                                $ttlrcvamt = array_sum($totalrecaivedamount);
                                echo $DI->Dashboard_model->formatIndianNumber($ttlrcvamt);
                                ?>
                            </div>
                            <div class="neo-metric-foot">Recent credited amount</div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <a href="<?php echo page_url; ?>Reporting/paymentsinnext15days" class="neo-metric-link">
                            <div class="neo-metric-card neo-metric-green">
                                <div class="neo-metric-icon"><i class="fa fa-calendar"></i></div>
                                <div class="neo-metric-label">Payment In Next 15 Days</div>
                                <div class="neo-metric-value">
                                    <?php
                                    $next15dayspayment = array();
                                    $next15dayspayment[] = 0;

                                    $date = new DateTime();
                                    $date->modify('+15 days');
                                    $createdate =  $date->format('Y-m-d');
                                    $enddate = $createdate;
                                    $startdate = date('Y-m-d');
                                    $q = $this->db->select('b.id, b.df_no, b.added_on, b.df_upload, b.added_on')->from('task_department_wise_scheduling a')->join('df_release b', 'a.df_id=b.id', 'left')->where('a.paymentstage', 1)->where('a.end_date BETWEEN "' . date('Y-m-d', strtotime($startdate)) . '" and "' . date('Y-m-d', strtotime($enddate)) . '"')->where('b.df_status', 0)->get();
                                    if ($q->num_rows() > 0) {
                                        foreach ($q->result() as $row) {
                                            $next15dayspayment[] = 1;
                                        }
                                    }

                                    echo array_sum($next15dayspayment);
                                    ?>
                                </div>
                                <div class="neo-metric-foot">Expected payment follow-up</div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <a href="<?php echo $paymentOverdueReportLink; ?>" class="neo-metric-link<?php echo !$kpi_payment_overdue_available ? ' disabled-payment-link' : ''; ?>">
                            <div class="neo-metric-card neo-metric-orange">
                                <div class="neo-metric-icon"><i class="fa fa-warning"></i></div>
                                <div class="neo-metric-label">Payment Overdue</div>
                                <div class="neo-metric-value"><?php echo $kpi_payment_overdue; ?></div>
                                <div class="neo-metric-foot"><?php echo $kpi_payment_overdue_available ? 'Invoices beyond expected payment date' : 'Billing tables not available in this database'; ?></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!----************************=================================*******************----->
            <?php
            $class = ($this->session->userdata('logged_in')['user_id'] == 161 || $this->session->userdata('logged_in')['user_id'] == 61 || $this->session->userdata('logged_in')['user_id'] == 209) ? 'col-md-12' : 'col-md-12';

            if ($is_admin == 1) {
                $useriddata = "";
            } else {
                $useriddata = $user_id;
            }
            $enuser = base64_encode($useriddata);
            ?>

            <div class="neo-section-shell">
                <div class="neo-section-head">
                    <div>
                        <h3 class="neo-section-title">DF Support & Ticket Overview</h3>
                        <div class="neo-section-subtitle">Running DF status, on-hold files, and pending support ticket visibility.</div>
                    </div>

                    <div class="neo-badge-group">
                        <a href="<?php echo page_url; ?>Maintenance_support/" class="neo-action-btn neo-action-green">Raise Help Ticket Against DF</a>
                    </div>
                </div>

                <div class="row neo-mini-grid">
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo page_url; ?>Task/dfreleasedashboard/" class="neo-metric-link">
                            <div class="neo-metric-card neo-metric-dark">
                                <div class="neo-metric-icon"><i class="fa fa-folder-open"></i></div>
                                <div class="neo-metric-label">Running DF</div>
                                <div class="neo-metric-value">
                                    <?php
                                    $q = $this->db->select('id')->from('df_release')->where('df_status', 0)->where('on_hold', 0)->get();
                                    echo $q->num_rows();
                                    ?>
                                </div>
                                <div class="neo-metric-foot">Currently active</div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo page_url; ?>Task/onholdf/" class="neo-metric-link">
                            <div class="neo-metric-card neo-metric-red">
                                <div class="neo-metric-icon"><i class="fa fa-pause-circle"></i></div>
                                <div class="neo-metric-label">On Hold DF</div>
                                <div class="neo-metric-value">
                                    <?php
                                    $q = $this->db->select('id')->from('df_release')->where('df_status', 0)->where('on_hold', 1)->get();
                                    echo $q->num_rows();
                                    ?>
                                </div>
                                <div class="neo-metric-foot">Paused cases</div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo page_url; ?>Task/viewallrunninghelptickets/<?php echo $enuser; ?>" class="neo-metric-link">
                            <div class="neo-metric-card neo-metric-purple">
                                <div class="neo-metric-icon"><i class="fa fa-life-ring"></i></div>
                                <div class="neo-metric-label"><?php if ($is_admin != 1) { echo "Your "; } ?>Pending Tickets</div>
                                <div class="neo-metric-value"><?php echo $counthelptickets; ?></div>
                                <div class="neo-metric-foot">Open communication issues on active DF</div>
                            </div>
                        </a>
                    </div>

                    <?php if ($is_admin != 1) { ?>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo page_url; ?>Task/helpticketsforyou/<?php echo $enuser; ?>" class="neo-metric-link">
                            <div class="neo-metric-card neo-metric-blue">
                                <div class="neo-metric-icon"><i class="fa fa-user-circle"></i></div>
                                <div class="neo-metric-label">PMS Help Tickets For <?php echo ($is_admin == 2) ? 'Team' : 'You'; ?></div>
                                <div class="neo-metric-value"><?php echo $counthelpticketsforyou; ?></div>
                                <div class="neo-metric-foot">Directly assigned active DF tickets</div>
                            </div>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>



                <div class="col-sm-12">

                    <div class="margin_btw"></div>
                    <div class="card_box">
                        <div class="tab">
                            <div class="row no-gutters dashboard-task-tab-strip">
                                <?php
                                if ($showAdminStyleDashboardTabs) {
                                    $default1 = "defaultOpen";
                                    $default2 = '';
                                } else {
                                    $default2 = "defaultOpen";
                                    $default1 = '';
                                }
                                $df_change_control_dashboard_tab_enabled = $dfChangeControlDashboardPermission
                                    || $dfChangeControlAssignedTaskCount > 0
                                    || $_SESSION['logged_in']['adminuser'] == 1
                                    || $_SESSION['logged_in']['adminuser'] == 2
                                    || $this->session->userdata('logged_in')['user_id'] == 209;
                                if ($showAdminStyleDashboardTabs) { ?>
                                <div class="col dashboard-task-tab-item">
                                    <button class="tablinks" onclick="openCity(event, 'unassigned')" id="<?php echo $default1; ?>">
                                        <!-- <img src="<?php echo assets_url; ?>task.png"alt=""> -->
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m4,7h3c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2Zm-1-5c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1v-3Zm6,12v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2h3c1.103,0,2-.897,2-2Zm-6,0v-3c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1Zm10-7h3c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2Zm-1-5c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1v-3Zm1.5,3h2c.276,0,.5-.224.5-.5v-2c0-.276-.224-.5-.5-.5h-2c-.276,0-.5.224-.5.5v2c0,.276.224.5.5.5Zm.5-2h1v1h-1v-1Zm10,20.5c0,.276-.224.5-.5.5s-.5-.224-.5-.5c0-1.637-.994-3.026-2.596-3.627l-5.08-1.905c-.195-.073-.324-.26-.324-.468v-5.893c0-.789-.535-1.471-1.244-1.587-.45-.07-.886.046-1.227.336-.337.286-.529.703-.529,1.144v8.424c0,.421-.235.796-.615.979-.379.18-.819.132-1.146-.13,0,0-1.716-1.367-1.719-1.371-.606-.562-1.553-.529-2.115.073-.565.604-.534,1.557.064,2.118l1.633,1.551c.325.309.107.856-.342.856-.127,0-.249-.048-.341-.135l-1.64-1.548c-1-.937-1.048-2.518-.106-3.524.928-.994,2.482-1.054,3.49-.149.003.002,1.698,1.347,1.698,1.347l.138-.066v-8.424c0-.734.321-1.429.881-1.905.561-.476,1.307-.68,2.035-.561,1.188.193,2.084,1.3,2.084,2.573v5.546l4.756,1.784c2.001.75,3.244,2.498,3.244,4.562Z" />
                                            </svg></div>
                                        <div style="color:#000 !important">
                                            UN-ASSIGNED DF<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="unassignednotificationcount"></span>
                                        </div>
                                    </button>
                                </div>
  <?php } ?>
                                <div class="col dashboard-task-tab-item">
                                    <button class="tablinks" onclick="openCity(event, 'going')" id="<?php echo $default2; ?>">
                                        <!-- <img src="<?php echo assets_url; ?>task.png" alt=""> -->
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m13,11h4v1h-4v-1ZM21,2v19.5c0,1.378-1.121,2.5-2.5,2.5H5.5c-1.379,0-2.5-1.122-2.5-2.5V2h5.05c.232-1.14,1.243-2,2.45-2h3c1.207,0,2.218.86,2.45,2h5.05Zm-1,1h-5v-.5c0-.827-.673-1.5-1.5-1.5h-3c-.827,0-1.5.673-1.5,1.5v.5h-5v18.5c0,.827.673,1.5,1.5,1.5h13c.827,0,1.5-.673,1.5-1.5V3Zm-11.216,7.952c-.056.056-.18.055-.241-.006l-1.778-1.721-.695.719,1.772,1.716c.221.22.514.341.825.341s.604-.122.821-.339l3.362-3.305-.701-.713-3.365,3.308Zm4.216,7.048h4v-1h-4v1Zm-4.216-1.048c-.056.057-.18.055-.241-.006l-1.778-1.721-.695.719,1.772,1.716c.221.22.514.341.825.341s.604-.122.821-.339l3.362-3.305-.701-.713-3.365,3.308Z" />
                                            </svg>
                                        </div>
                                        <div style="color:#000 !important">
                                            ON GOING TASK<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="ongoingtaskcountnotification"><?php echo (int)$kpi_ongoing_tasks; ?></span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col dashboard-task-tab-item">
                                    <button class="tablinks" onclick="openCity(event, 'overdue')">

                                        <!-- <img src="<?php echo assets_url; ?>overdue.png" alt=""> -->

                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m13,12h-6v-1h6v1ZM0,17h5v5H0v-5Zm1,4h3v-3H1v3Zm6-1h10v-1H7v1Zm0-17v1h17v-1H7ZM0,9h5v5H0v-5Zm1,4h3v-3H1v3ZM0,1h5v5H0V1Zm1,4h3v-3H1v3Zm17.439,1.439l-3.442,3.442.707.707,3.296-3.295v16.707h1V7.292l3.276,3.277.707-.707-3.423-3.424c-.586-.583-1.537-.583-2.121,0Z" />
                                            </svg></div>
                                        <div style="color:#000 !important">
                                            OVERDUE TASKS<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="overduetaskcountnotification"><?php echo (int)$kpi_overdue_tasks; ?></span>
                                        </div>


                                    </button>
                                </div>
                                <div class="col dashboard-task-tab-item">
                                    <button class="tablinks" onclick="openCity(event, 'complete')">
                                        <!-- <img src="<?php echo assets_url; ?>complete.png" alt=""> -->

                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m15.95,2c-.232-1.14-1.243-2-2.45-2h-3c-1.208,0-2.217.86-2.45,2H3v19.5c0,1.378,1.122,2.5,2.5,2.5h13c1.379,0,2.5-1.122,2.5-2.5V2h-5.05Zm4.05,19.5c0,.827-.673,1.5-1.5,1.5H5.5c-.827,0-1.5-.673-1.5-1.5V3h5v-.5c0-.827.673-1.5,1.5-1.5h3c.827,0,1.5.673,1.5,1.5v.5h5v18.5Zm-9.021-6.714l5.901-5.797.701.714-5.898,5.793c-.336.335-.778.503-1.22.503s-.887-.168-1.225-.506l-2.815-2.71.693-.721,2.822,2.717c.292.292.756.292,1.041.007Z" />
                                            </svg>
                                        </div>
                                        <div style="color:#000 !important">COMPLETED TASKS<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="completeddfnotificationcount">20</span></div>
                                    </button>
                                </div>


                            <?php 
                            if($_SESSION['logged_in']['adminuser']==1 ||  $_SESSION['logged_in']['adminuser']==2 || $this->session->userdata('logged_in')['user_id'] == 209)
                            {
                            ?>

                                <div class="col dashboard-task-tab-item">
                                    <button class="tablinks" onclick="openCity(event, 'completeandpendingforapproval')">
                                        <!-- <img src="<?php echo assets_url; ?>complete.png" alt=""> -->

                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m17.5,7c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h3.692c-2.023-3.091-5.474-5-9.192-5C5.935,1,1,5.935,1,12s4.935,11,11,11c1.5,0,2.289-.22,2.901-.387.276-.07.542.086.614.351.072.267-.085.542-.351.614-.668.182-1.665.422-3.165.422C5.383,24,0,18.617,0,12S5.383,0,12,0c4.04,0,7.789,2.066,10,5.414V1.5c0-.276.224-.5.5-.5s.5.224.5.5v4c0,.827-.673,1.5-1.5,1.5h-4Zm6,4.5c-.276,0-.5.224-.5.5,0,.64-.055,1.28-.163,1.903-.048.272.135.531.406.578.029.006.058.008.087.008.238,0,.449-.171.491-.414.119-.681.179-1.378.179-2.075,0-.276-.224-.5-.5-.5Zm-.855,4.891c-.25-.118-.548-.009-.664.242-.269.577-.589,1.134-.953,1.655-.158.227-.103.538.124.696.087.061.187.09.286.09.157,0,.313-.074.41-.214.397-.569.747-1.177,1.039-1.806.116-.251.008-.548-.242-.664Zm-3.547,4.013c-.486.412-1.011.782-1.56,1.103-.238.139-.319.444-.18.684.093.159.26.248.432.248.086,0,.173-.021.252-.068.599-.349,1.171-.753,1.702-1.202.211-.179.237-.494.059-.705-.178-.209-.494-.234-.705-.059Zm-2.357-6.342l-4.24-2.32v-6.241c0-.276-.224-.5-.5-.5s-.5.224-.5.5v6.538c0,.183.1.351.26.438l4.5,2.462c.076.042.158.062.24.062.177,0,.348-.094.438-.26.133-.242.044-.546-.198-.679Z" />
                                            </svg></div>
                                        <div style="color:#000 !important"> PENDING FOR APPROVAL<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="completeddfnotificationcountforapproval">35</span>
                                        </div>

                                    </button>
                                </div>

                            <?php } ?>

                            <?php if ($df_change_control_dashboard_tab_enabled) { ?>
                                <div class="col dashboard-task-tab-item">
                                    <button class="tablinks change-control-tab-trigger <?php echo $dfChangeControlAssignedTaskCount > 0 ? 'has-items' : ''; ?>" onclick="openCity(event, 'changecontrolassigned')">
                                        <span class="change-control-tab-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M19 3h-3.18C15.4 1.84 14.3 1 13 1h-2c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm-8-1h2a1 1 0 0 1 1 1h-4a1 1 0 0 1 1-1Zm8 19H5V5h14v16Zm-9.71-3.29-2.29-2.3.71-.7 1.58 1.58 4.59-4.59.71.71-5.3 5.3Zm0-6-2.29-2.3.71-.7 1.58 1.58 4.59-4.59.71.71-5.3 5.3Z"></path>
                                            </svg>
                                        </span>
                                        <span class="change-control-tab-copy">
                                            <span class="change-control-tab-title">
                                                CHANGE CONTROL TASKS<span class="counter"
                                                    style="background-color:orange;font-weight: bold;color:white;"
                                                    id="changecontroltaskcountnotification"><?php echo $dfChangeControlAssignedTaskCount > 0 ? (int)$dfChangeControlAssignedTaskCount : ''; ?></span>
                                            </span>
                                            <!-- <span class="change-control-tab-meta"><?php echo htmlspecialchars($dfChangeControlAssignedLabel); ?></span> -->
                                        </span>
                                    </button>
                                </div>
                            <?php } ?>

                            </div>
                        </div>
                         <?php
                        if ($showAdminStyleDashboardTabs) { ?>
                        <div id="unassigned" class="tabcontent">
                            <div class="table__header table-responsive">
                                <table id="example4" class="table table-striped pretty">
                                    <thead>
                                        <tr>
                                            <th>S.NO.</th>
                                            <th>DF NO.</th>
                                            <th>DF RELEASE DATE</th>
                                            <th>DOWNLOAD DF</th>
                                            <th>ASSIGNMENT</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                           <?php } ?>
                        <div id="going" class="tabcontent">
                            <div class="row" style="margin-top:20px;">

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DF NO.</label>
                                            <select class="form-control task_dfno_filter" id="task_dfno_filter"
                                                onchange="filter_ongoing_task();">
                                                <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY (DUE DATE)</label>
                                            <select class="form-control" id="task_date_filter"
                                                onchange="filter_ongoing_task();">
                                                <option value="ALL">ALL</option>
                                                <option value="1">TASK DUE TODAY</option>
                                                <option value="2">TASK DUE THIS WEEK</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                            <select class="form-control task_dfno_filter" id="task_department_filter"
                                                onchange="filter_ongoing_task(); getusers();">
                                                <?php echo $taskDepartmentOptionsHtml; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getusers() {
                                            var department = $("#task_department_filter").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
                                                    $("#task_user_filter").html(data);
                                                }
                                            });
                                        }
                                    </script>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY USERS</label>
                                            <select class="form-control" id="task_user_filter"
                                                onchange="filter_ongoing_task();">
                                                <?php echo $taskUserFilterDefaultOption; ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="table__header table-responsive">
                                <table id="example" class="table table-striped pretty">
                                    <thead>
                                        <tr>
                                            <th>S. NO.</th>
                                            <th>DF NO.</th>
                                            <th>DOWNLOAD DF</th>
                                            <th>DF RELEASE<br>DATE</th>
                                            <th>DEPARTMENT</th>
                                            <th>ACCOUNTABLE PERSON</th>
                                            <th>TASK NAME</th>
                                            <th>START DATE</th>
                                            <th>TAT</th>
                                            <th>TIME REMAINING</th>
                                            <th>TASK STATUS UPDATE</th>
                                            <th>LAST REMARKS<br>(If Any)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>



                        <div id="overdue" class="tabcontent">

                            <div class="row" style="margin-top:20px;">

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BYDF NO.</label>
                                            <select class="form-control task_dfno_filter" id="task_dfno_filter_overdue"
                                                onchange="filter_overdue_task();">
                                               <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY</label>
                                            <select class="form-control" id="task_number_filter_overdue"
                                                onchange="filter_overdue_task(); checkother();">
                                                <option value="ALL">ALL</option>
                                                <option value="1">OTHER</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function checkother() {
                                            var task_number_filter_overdue = $("#task_number_filter_overdue").val();
                                            if (task_number_filter_overdue == 1) {
                                                $("#showno").show();
                                            } else {
                                                $("#showno").hide();
                                            }
                                        }
                                    </script>
                                    <div class="col-md-2" style="display: none;" id="showno">
                                        <div class="form-group">
                                            <label style="color:#000;">NO OF DAYS</label>
                                            <input type="number" class="form-control" id="noofdaysdue" value="2"
                                                onblur="filter_overdue_task();">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                            <select class="form-control task_dfno_filter"
                                                id="task_department_filter_overdue"
                                                onchange="filter_overdue_task(); getusersss();">
                                                <?php echo $taskDepartmentOptionsHtml; ?>
                                            </select>

                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getusers() {
                                            var department = $("#task_department_filter").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
                                                    $("#task_user_filter").html(data);
                                                }
                                            });
                                        }

                                        function getusersss() {
                                            var department = $("#task_department_filter_overdue").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
                                                    $("#task_user_filter_overdue").html(data);
                                                }
                                            });
                                        }
                                    </script>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY USERS</label>
                                             <select class="form-control" id="task_user_filter_overdue" onchange="filter_overdue_task();">
                                                <?php echo $taskUserFilterDefaultOption; ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="table__header table-responsive">
                                <table id="example2" class="table table-striped pretty">
                                    <thead>
                                        <tr>
                                            <th>S. NO.</th>
                                            <th>DF NO.</th>
                                            <th>DOWNLOAD DF</th>
                                            <th>DF RELEASE<br>DATE</th>
                                            <th>DEPARTMENT</th>
                                            <th>ACCOUNTABLE PERSON</th>
                                            <th>TASK NAME</th>
                                            <th>TAT</th>
                                            <th>TIME EXCEEDED</th>
                                            <th>UPDATE PROGRESS</th>
                                            <th>LAST REMARKS<br>(If Any)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>
                        </div>


                        <div id="complete" class="tabcontent">
                            <div class="row" style="margin-top:20px;">

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DF NO.</label>
                                            <select class="form-control task_dfno_filter" id="task_dfno_filter_completed" onchange="filter_completed_task();">
                                                <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                           <select class="form-control task_dfno_filter" id="task_department_filter_completed" onchange="filter_completed_task(); getuserscompleted();">
                                                <?php echo $taskDepartmentOptionsHtml; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getuserscompleted() {
                                            var department = $("#task_department_filter_completed").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
                                                    $("#task_user_filter_completed").html(data);
                                                }
                                            });
                                        }
                                    </script>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY USER</label>
                                            <select class="form-control" id="task_user_filter_completed" onchange="filter_completed_task();">
                                                <?php echo $taskUserFilterDefaultOption; ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <?php if ($_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) { ?>
                                <div class="row" style="margin-top:15px;">
                                    <div class="col-md-12 text-right">
                                        <a href="<?php echo page_url; ?>Task_completion_control" class="btn btn-warning">
                                            Open Completed Task Control
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="table__header">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        <table style="width:100%;border:none !important;">
                                            <thead>
                                                <tr>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c1">&nbsp;</span>&nbsp;ON TIME</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c2">&nbsp;</span>&nbsp;DELAYED</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c3">&nbsp;</span>&nbsp;EARLY</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <table id="example3" class="table pretty2">
                                    <thead>
                                        <tr>
                                            <th>S. NO.</th>
                                            <th>DF NO.</th>
                                            <th>DOWNLOAD DF</th>
                                            <th>DF RELEASE<br> DATE</th>
                                            <th>DEPARTMENT</th>
                                            <th>ACCOUNTABLE PERSON</th>
                                            <th>TASK NAME</th>
                                            <th>PLANNED COMPLETION DATE</th>
                                            <th>ACTUAL COMPLETION DATE</th>
                                            <th>DELAY</th>
                                            <th>STATUS</th>
                                            <th>REMARKS</th>
                                            <th>COMPLETED BY</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>

                        </div>

                        <div id="completeandpendingforapproval" class="tabcontent">
                            <div class="row" style="margin-top:20px;">

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DF NO.</label>
                                          <select class="form-control task_dfno_filter" id="task_dfno_filter_completed_and_pending_for_approval" onchange="filter_completed_task_pending_for_approval();">
                                                <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                             <select class="form-control task_dfno_filter" id="task_department_filter_completed_for_approval" onchange="filter_completed_task_pending_for_approval(); getuserscompletedandpending();">
                                                <?php echo $taskDepartmentOptionsHtml; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getuserscompletedandpending() {
                                            var department = $("#task_department_filter_completed_for_approval").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
                                                    $("#task_user_filter_completed_pending_for_approval").html(data);
                                                }
                                            });
                                        }
                                    </script>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY USER</label>
                                            <select class="form-control" id="task_user_filter_completed_pending_for_approval" onchange="filter_completed_task();">
                                                <?php echo $taskUserFilterDefaultOption; ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="table__header">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        <table style="width:100%;border:none !important;">
                                            <thead>
                                                <tr>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c1">&nbsp;</span>&nbsp;ON TIME</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c2">&nbsp;</span>&nbsp;DELAYED</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c3">&nbsp;</span>&nbsp;EARLY</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <table id="example8" class="table pretty2">
                                    <thead>
                                        <tr>
                                            <th>S. NO.</th>
                                            <th>DF No.</th>
                                            <th>DOWNLOAD DF</th>
                                            <th>DF RELEASE<br> DATE</th>
                                            <th>DEPARTMENT</th>
                                            <th>ACCOUNTABLE PERSON</th>
                                            <th>TASK NAME</th>
                                            <th>PLANNED COMPLETION DATE</th>
                                            <th>ACTUAL COMPLETION DATE</th>
                                            <th>APPROVE/REJECT</th>
                                            <th>DELAY</th>
                                            <th>STATUS</th>
                                            <th>REMARKS</th>
                                            <th>COMPLETED BY</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>

                        </div>

                        <?php if ($df_change_control_dashboard_tab_enabled) { ?>
                            <div id="changecontrolassigned" class="tabcontent">
                                <div class="row" style="margin-top:20px;">
                                    <div class="col-md-12" style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin-bottom:0;">
                                                <label style="color:#000;">ACTIVE DF CHANGE CONTROL TASKS</label>
                                                <p style="margin:6px 0 0; color:#555;">
                                                    Active DF change-control actions assigned to you, your team, or
                                                    requested by you are shown here. Open the request to follow live
                                                    progress and the requester / assignee communication chain.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table__header table-responsive">
                                    <table id="example9" class="table table-striped pretty">
                                        <thead>
                                            <tr>
                                                <th>S. NO.</th>
                                                <th>REQUEST / DF</th>
                                                <th>DOWNLOAD DF</th>
                                                <th>DEPARTMENT</th>
                                                <th>ACCOUNTABLE PERSON</th>
                                                <th>REQUEST TYPE</th>
                                                <th>TARGET DATE</th>
                                                <th>TIME STATUS</th>
                                                <th>CURRENT STATUS</th>
                                                <th>REMARKS</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>

                    </div>



                   
                    <?php
                    if (pms_is_super_admin()  || $this->session->userdata['logged_in']['role']==27|| $user_id == 139 || $this->session->userdata('logged_in')['user_id'] == 209) {
                    ?>
                     <div class="margin_btw"></div>
                    <section class="df-delay-panel">
                        <div class="df-delay-head">
                            <div>
                                <h3><i class="fa fa-clock-o"></i> Running DF Task-Wise Delay Report</h3>
                                <p>Live attention view of open overdue tasks, pending approvals and the next committed task date for every running DF.</p>
                            </div>
                            <div class="df-delay-kpis">
                                <div class="df-delay-kpi"><strong id="delayReportDfCount">0</strong><span>Running DFs</span></div>
                                <div class="df-delay-kpi"><strong id="delayReportAtRiskCount">0</strong><span>Delayed DFs</span></div>
                                <div class="df-delay-kpi"><strong id="delayReportTaskCount">0</strong><span>Overdue Tasks</span></div>
                                <div class="df-delay-kpi"><strong id="delayReportApprovalCount">0</strong><span>Approvals</span></div>
                            </div>
                        </div>
                        <div class="df-delay-table-wrap">
                            <div class="table-responsive">
                                <table id="dfTablealldata" class="table table-striped table-bordered pretty5"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>DF NO</th>
                                            <th>DF DESCRIPTION</th>
                                            <th>DF RELEASE DATE</th>
                                            <th>HEALTH</th>
                                            <th>OPEN / DELAYED</th>
                                            <th>MAX DELAY</th>
                                            <th>NEXT DUE</th>
                                            <th>DOCUMENTS</th>
                                            <th>TASK DETAILS</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </section>
                    <?php
                    }
                    ?>


                    
                      <?php
                    if (pms_is_super_admin() || $this->session->userdata['logged_in']['role']==27 || $this->session->userdata('logged_in')['user_id'] == 209) {

                    ?>
                    <div class="margin_btw"></div>

<?php
// --- ALL PHP LOGIC MOVED TO THE TOP OF THIS BLOCK ---

// 1. Initialize variables
$grand_total_running_loss = 0;
$running_table_rows_html = '';
$m = 1;
$running_df_total = 0;
$running_df_delayed = 0;
$running_df_on_track = 0;
$running_progress_sum = 0;

// 2. Main Query for Running DFs 
// UPDATE: Added 'DISTINCT' to the subquery to prevent duplicate designer names
$q = $this->db->select('a.id, a.df_no, a.added_on, a.df_upload, b.title, b.first_name, b.last_name, 
                        (SELECT GROUP_CONCAT(DISTINCT CONCAT(su.first_name, " ", su.last_name) SEPARATOR ", ") 
                         FROM task_department_wise_scheduling td 
                         JOIN system_users su ON td.assigned_user = su.user_id 
                         WHERE td.df_id = a.id AND td.department_id = 11) as designer_names')
             ->from('df_release a')
             ->join('system_users b', 'a.added_by=b.user_id', 'left')
             ->where('a.df_status', 0) // Running DFs
             ->where('a.on_hold', 0) // Exclude hold DFs
             ->order_by('a.id', 'desc')
             ->get();

if ($q->num_rows() > 0) {
    foreach ($q->result() as $rows) {
        $dfowner = $rows->title . " " . $rows->first_name . " " . $rows->last_name;

        // --- Get Planned Closer Date (from Dispatch Task ID 103) ---
        $q_end = $this->db->select('end_date')
                        ->from('task_department_wise_scheduling')
                        ->where('df_id', $rows->id)
                        ->where('taskid', 103) // Specifically getting the dispatch task
                        ->get();
        $planneddate_raw = $q_end->row()->end_date ?? '';
        
        // $planneddate_calc is 'Y-m-d' for calculations
        $planneddate_calc = ($planneddate_raw && strtotime($planneddate_raw)) ? date('Y-m-d', strtotime($planneddate_raw)) : '';
        // $planneddate_display is 'd-m-Y' for display
        $planneddate_display = ($planneddate_calc) ? date('d-m-Y', strtotime($planneddate_calc)) : '--';


        // --- Get PO Details and Order Value ---
        $q5 = $this->db->select('a.podate, a.po_attachment, a.order_value')
                    ->from('poreceived a')
                    ->where('a.df_id', $rows->id)
                    ->get();
        
        $podate = '';
        $po_attachment = '';
        $order_value = 0;

        if ($q5->num_rows() > 0) {
            $row5 = $q5->row();
            $podate = ($row5->podate && strtotime($row5->podate)) ? date('d-m-Y', strtotime($row5->podate)) : '';
            if ($can_download_po && !empty($row5->po_attachment)) {
                $po_attachment = '<a href="' . sfdocument . 'Taskdocument/' . $row5->po_attachment . '" download><span class="btn btn-warning btn-xs">Click to download PO</span></a>';
            }
            $order_value = (float)$row5->order_value;
        }

        // --- Calculate Percentage Completion ---
        $q_total_tasks = $this->db->select('id')
                                  ->from('task_department_wise_scheduling')
                                  ->where('df_id', $rows->id)
                                  ->get();
        $count = $q_total_tasks->num_rows();

        $q_done_tasks = $this->db->select('id')
                                 ->from('task_department_wise_scheduling')
                                 ->where('df_id', $rows->id)
                                 ->where('task_status', 1)
                                 ->get();
        $totaldone = $q_done_tasks->num_rows();
        
        $percetage = ($count > 0) ? @round($totaldone * 100 / $count) : 0;

        // --- Get Projected Delay ---
        $maxdays = $CIA->Task_model->workDelayed($rows->id, 0);
        $running_df_total++;
        $running_progress_sum += (int) $percetage;
        $hasCurrentDelay = isset($dashboardDelayedDfIds[(int)$rows->id]);
        if ($hasCurrentDelay) {
            $running_df_delayed++;
        } else {
            $running_df_on_track++;
        }

        // --- Calculate Projected Completion Date ---
        $nextcompletiondate = '--';
        if ($planneddate_calc) {
            if ($maxdays > 0) {
                $date = new DateTime($planneddate_calc);
                $date->modify("+$maxdays days");
                $newDate = $date->format('Y-m-d');
                $nextcompletiondate = date('d-m-Y', strtotime($newDate));
            } else {
                $nextcompletiondate = $planneddate_display;
            }
        }
        
        // --- Loss Calculation ---
// Formula: Loss Against DF = Order Value / No. of Extra Delayed Days
$loss_display = "N/A";
$loss_amount = 0;
$df_delay_days = (int)$maxdays;
$order_value = (float)$order_value;

if ($order_value > 0 && $df_delay_days > 0) {
    $loss_amount = round($order_value / $df_delay_days);

    if (method_exists($CIA->Task_model, 'formatIndianCurrency')) {
        $loss_display = "<span style='color:#b91c1c; font-weight:bold;'>
            <i class='fa fa-inr'></i> " . $CIA->Task_model->formatIndianCurrency($loss_amount) . "
        </span>
        <br>
        <small style='color:#6b7280; font-weight:600;'>
            Order Value / " . $df_delay_days . " delayed days
        </small>";
    } else {
        $loss_display = "<span style='color:#b91c1c; font-weight:bold;'>
            <i class='fa fa-inr'></i> " . number_format($loss_amount, 2) . "
        </span>
        <br>
        <small style='color:#6b7280; font-weight:600;'>
            Order Value / " . $df_delay_days . " delayed days
        </small>";
    }
} else {
    $loss_display = "<span style='color:#15803d; font-weight:bold;'>
        <i class='fa fa-inr'></i> 0
    </span>
    <br>
    <small style='color:#6b7280; font-weight:600;'>
        No delayed days
    </small>";
}
$grand_total_running_loss += $loss_amount;

        // --- Prepare Designer Display HTML ---
        $designer_html = '';
        if (!empty($rows->designer_names)) {
            $designer_html .= '<hr style="margin: 5px 0; border-top: 1px solid #ccc;">';
            $designer_html .= '<span style="font-size: 11px; font-weight: bold; color: #555;">DESIGNER</span><hr>';
            $designer_html .= '<span style="color: #000;">' . htmlspecialchars(ucwords(strtolower($rows->designer_names)), ENT_QUOTES, 'UTF-8') . '</span>';
        }

        // --- Build HTML row ---
        $running_table_rows_html .= '<tr>';
        $running_table_rows_html .= '<td>' . $m . '</td>';
        $running_table_rows_html .= '<td><strong style="color:#1f5f9f;">' . htmlspecialchars(strtoupper($rows->df_no), ENT_QUOTES, 'UTF-8') . '</strong></td>';
        $running_table_rows_html .= '<td>
                                        <a href="' . sfdocument . 'Taskdocument/dfattachment/' . $rows->df_upload . '" download>
                                            <span class="btn btn-primary btn-xs">DOWNLOAD DF</span>
                                        </a><br><br>' . $po_attachment . '
                                    </td>';
        $running_table_rows_html .= '<td>' . $podate . '</td>';
        
        // Updated Marketing Person Cell with Designer Info
        $running_table_rows_html .= '<td>' 
                                    . htmlspecialchars(ucwords(strtolower(trim($dfowner))), ENT_QUOTES, 'UTF-8')
                                    . $designer_html 
                                    . '<br><br>
                                    <a href="' . page_url . 'Task/viewdfmeetingmom/' . $rows->id . '">
                                        <span class="btn btn-success btn-xs">DF MEETING MOM</span>
                                    </a>
                                    </td>';
                                    
        $running_table_rows_html .= '<td>' . date('d-m-Y', strtotime($rows->added_on)) . '</td>';
        $running_table_rows_html .= '<td>' . $planneddate_display . '</td>';
        $running_table_rows_html .= '<td>' . $nextcompletiondate . '</td>';
        $running_table_rows_html .= '<td><div class="portfolio-progress"><strong>' . (int) $percetage . '%</strong><div class="portfolio-progress-bar"><div class="portfolio-progress-fill" style="width:' . max(0, min(100, (int) $percetage)) . '%;"></div></div></div></td>';
        $running_table_rows_html .= '<td>';
        if ($hasCurrentDelay) {
            $running_table_rows_html .= '<span class="portfolio-delay late">Delayed — overdue tasks</span>';
        } else {
            $running_table_rows_html .= '<span class="portfolio-delay clear"><i class="fa fa-check"></i> On track</span>';
        }
        $running_table_rows_html .= '</td>';
        //$running_table_rows_html .= '<td>' . $loss_display . '</td>'; 
        $running_table_rows_html .= '<td>
                                        <a href="' . page_url . 'gantt/' . $rows->id . '" target="_blank">
                                            <span class="btn btn-warning btn-xs"><i class="fa fa-bar-chart"></i> Gantt</span>
                                        </a>
                                    </td>';
        $running_table_rows_html .= '</tr>';

        $m++;
    }
}
?>

<?php $running_average_progress = $running_df_total > 0 ? round($running_progress_sum / $running_df_total) : 0; ?>
<div class="row">
    <div class="col-sm-12">
        <section class="running-portfolio-panel">
            <div class="running-portfolio-head">
                <h3><i class="fa fa-cogs"></i> All Running DFs</h3>
                <p>Portfolio view of schedule commitment, completion progress and ownership. Delay status uses current overdue pending tasks, matching the dashboard total.</p>
            </div>
            <div class="running-portfolio-kpis">
                <div class="running-portfolio-kpi"><span>Running DFs</span><strong><?php echo (int) $running_df_total; ?></strong></div>
                <div class="running-portfolio-kpi"><span>On Track</span><strong style="color:#15803d;"><?php echo (int) $running_df_on_track; ?></strong></div>
                <div class="running-portfolio-kpi"><span>Delayed</span><strong style="color:#b91c1c;"><?php echo (int) $running_df_delayed; ?></strong></div>
                <div class="running-portfolio-kpi"><span>Average Progress</span><strong><?php echo (int) $running_average_progress; ?>%</strong></div>
            </div>
            <div class="running-portfolio-table table-responsive">
                <table id="example5" class="table pretty5 table-striped table-bordered ">
                    <thead>
                        <tr>
                            <th>S. NO.</th>
                            <th>DF No.</th>
                            <th>DOCUMENTS</th>
                            <th>PO DATE</th>
                            <th>MARKETING/DESIGN PERSON</th>
                            <th>DF RELEASE DATE</th>
                            <th>DISPATCH PLAN DATE</th>
                            <th>FORECAST COMPLETION</th>
                            <th>PROGRESS</th>
                            <th>SCHEDULE HEALTH</th>
                            <th>PLANNING</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Print the pre-built HTML rows
                            echo $running_table_rows_html;
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

  <?php } ?>

 

                <?php
                if ($user_id == 139) {
                } else {
                $Q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                if ($Q->num_rows() > 0) {
                ?>
                <div class="margin_btw"></div>
                <div class="row ">
                <div class="col-sm-12">
                <h3 class="global_heading">
                DF Wise Task Due of Your Team
                </h3>
                <div class="card_box">
                <div class="table-responsive">
                <table id="dfTable" class="table table-striped table-bordered pretty5" style="width:100%">
                <thead>
                <tr>

                <th>DF NO</th>
                <th>DF Description</th>
                <th>Added On</th>
                <th>Download</th>
                <th>Action</th>
                </tr>
                </thead>

                </table>

                </div>

                </div>
                </div>



                </div>

                <?php
                }
                } ?>



                    <?php
                    $Q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                    if ($Q->num_rows() > 0) {

                    ?>
                    <div class="margin_btw"></div>
                    <div class="row ">
                        <div class="col-sm-12">
                            <h3 class="global_heading">
                                All Running DFs
                            </h3>
                            <div class="card_box">
                                <div class="table-responsive">
                                    <?php
$grand_total_running_loss = 0;
?>

<table id="example5" class="table pretty5 table-striped table-bordered">
    <thead>
        <tr>
            <th>S. NO.</th>
            <th>DF No.</th>
            <th>DOWNLOAD</th>
            <th>PO DATE</th>
            <th>MARKETING PERSON</th>
            <th>DF RELEASE DATE</th>
            <th>PROJECTED COMPLETION DATE</th>
            <th>ACTUAL COMPLETION DATE</th>
            <th>DF STATUS</th>
            <th>DF DELAYED</th>
            <!-- <th>LOSS AGAINST DF</th> -->
            <th>VIEW GANTT CHART</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $m = 1;

        $q = $this->db
            ->select('a.id, a.df_no, a.added_on, a.df_upload, b.title, b.first_name, b.last_name')
            ->from('df_release a')
            ->join('system_users b', 'a.added_by = b.user_id', 'left')
            ->where('a.df_status', 0)
            ->order_by('a.id', 'desc')
            ->get();

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {

                $df_id = (int)$rows->id;

                $dfowner = trim($rows->title . " " . $rows->first_name . " " . $rows->last_name);

                /*
                |--------------------------------------------------------------------------
                | Projected Completion Date
                |--------------------------------------------------------------------------
                */
                $q_end = $this->db
                    ->select('MAX(end_date) as enddate')
                    ->from('task_department_wise_scheduling')
                    ->where('df_id', $df_id)
                    ->get();

                $planneddate = '';

                if ($q_end->num_rows() > 0 && !empty($q_end->row()->enddate) && $q_end->row()->enddate != '0000-00-00') {
                    $planneddate = date('d-m-Y', strtotime($q_end->row()->enddate));
                    $planneddateofcompletion = date('Y-m-d', strtotime($q_end->row()->enddate));
                } else {
                    $planneddate = '';
                    $planneddateofcompletion = '';
                }

                /*
                |--------------------------------------------------------------------------
                | PO Info + Marketing Person
                |--------------------------------------------------------------------------
                */
                $podate = '';
                $order_value = 0;

                $q5 = $this->db
                    ->select('a.id, a.company_name, a.podate, a.order_value, b.title, b.first_name, b.last_name')
                    ->from('poreceived a')
                    ->join('system_users b', 'a.added_by = b.user_id', 'left')
                    ->where('a.df_id', $df_id)
                    ->order_by('a.id', 'desc')
                    ->limit(1)
                    ->get();

                if ($q5->num_rows() > 0) {
                    $row5 = $q5->row();

                    if (!empty($row5->podate) && $row5->podate != '0000-00-00') {
                        $podate = date('d-m-Y', strtotime($row5->podate));
                    }

                    $dfowner = trim($row5->title . " " . $row5->first_name . " " . $row5->last_name);
                    $order_value = (float)$row5->order_value;
                }

                /*
                |--------------------------------------------------------------------------
                | Total Tasks + Completed Tasks
                |--------------------------------------------------------------------------
                */
                $count = $this->db
                    ->where('df_id', $df_id)
                    ->count_all_results('task_department_wise_scheduling');

                $q2 = $this->db
                    ->select('id, task_completed_on, end_date')
                    ->from('task_department_wise_scheduling')
                    ->where('df_id', $df_id)
                    ->where('task_status', 1)
                    ->get();

                $totaldone = $q2->num_rows();

                $percetage = 0;

                if ($count > 0) {
                    $percetage = round(($totaldone * 100) / $count);
                }

                /*
                |--------------------------------------------------------------------------
                | Delayed Completed Tasks Count
                |--------------------------------------------------------------------------
                */
                $delaycount = 0;
                $totaldayscount = 0;

                if ($q2->num_rows() > 0) {
                    foreach ($q2->result() as $rowss) {

                        if (
                            !empty($rowss->task_completed_on) &&
                            $rowss->task_completed_on != '0000-00-00 00:00:00' &&
                            !empty($rowss->end_date) &&
                            $rowss->end_date != '0000-00-00'
                        ) {
                            $completed_on_date = date('Y-m-d', strtotime($rowss->task_completed_on));

                            if ($completed_on_date > $rowss->end_date) {
                                $delaycount++;

                                if (method_exists($CIA->Task_model, 'getDays')) {
                                    $daysss = $CIA->Task_model->getDays($rowss->end_date, $completed_on_date, 1);
                                } else {
                                    $daysss = abs(round((strtotime($completed_on_date) - strtotime($rowss->end_date)) / 86400));
                                }

                                $totaldayscount += (int)$daysss;
                            }
                        }
                    }
                }

                $totaldelayedpercentage = 0;

                if ($totaldone > 0) {
                    $totaldelayedpercentage = round(($delaycount * 100) / $totaldone);
                }

                /*
                |--------------------------------------------------------------------------
                | Actual Completion Date Based on Max Delayed Days
                |--------------------------------------------------------------------------
                */
                $maxdays = 0;

                if (method_exists($CIA->Task_model, 'workDelayed')) {
                    $maxdays = (int)$CIA->Task_model->workDelayed($df_id, 0);
                }

                $nextcompletiondate = $planneddate;

                if (!empty($planneddateofcompletion) && $maxdays > 0) {
                    $date = new DateTime($planneddateofcompletion);
                    $date->modify("+$maxdays days");
                    $nextcompletiondate = $date->format('d-m-Y');
                }

                /*
                |--------------------------------------------------------------------------
                | Corrected Loss Calculation
                |--------------------------------------------------------------------------
                | Formula:
                | Loss Against DF = Order Value / No. of Extra Delayed Days
                |--------------------------------------------------------------------------
                */
                $loss = 0;
                $lossdisplay = '';

                if ($order_value > 0 && $maxdays > 0) {

                    $loss = round($order_value / $maxdays);
                    $grand_total_running_loss += $loss;

                    if (method_exists($CIA->Task_model, 'formatIndianCurrency')) {
                        $lossamount = $CIA->Task_model->formatIndianCurrency($loss);
                    } else {
                        $lossamount = number_format($loss, 2);
                    }

                    $lossdisplay = "
                        <span style='color:#b91c1c; font-weight:bold;'>
                            <i class='fa fa-inr'></i> {$lossamount}
                        </span>
                        <br>
                        <small style='color:#6b7280; font-weight:600;'>
                            Order Value / {$maxdays} delayed days
                        </small>
                    ";

                } else {

                    $lossdisplay = "
                        <span style='color:#15803d; font-weight:bold;'>
                            <i class='fa fa-inr'></i> 0
                        </span>
                        <br>
                        <small style='color:#6b7280; font-weight:600;'>
                            No delayed days
                        </small>
                    ";
                }

                /*
                |--------------------------------------------------------------------------
                | Display Helpers
                |--------------------------------------------------------------------------
                */
                $df_no_display = !empty($rows->df_no) ? strtoupper($rows->df_no) : '-';

                $df_download = '-';

                if (!empty($rows->df_upload)) {
                    $df_download = '
                        <a href="' . sfdocument . 'Taskdocument/dfattachment/' . $rows->df_upload . '" download>
                            <span class="btn btn-primary btn-xs">DOWNLOAD DF</span>
                        </a>
                    ';
                }

                $df_release_date = '';

                if (!empty($rows->added_on) && $rows->added_on != '0000-00-00 00:00:00') {
                    $df_release_date = date('d-m-Y', strtotime($rows->added_on));
                }

                $df_status_display = $percetage . '%';

                if ($percetage >= 100) {
                    $df_status_display = "<span style='color:#15803d; font-weight:bold;'>{$percetage}%</span>";
                } elseif ($percetage >= 50) {
                    $df_status_display = "<span style='color:#c2410c; font-weight:bold;'>{$percetage}%</span>";
                } else {
                    $df_status_display = "<span style='color:#b91c1c; font-weight:bold;'>{$percetage}%</span>";
                }

                $df_delay_display = '';

                if ($maxdays > 0) {
                    $df_delay_display = "<strong style='color:red; font-weight:bold;'>{$maxdays} DAYS</strong>";
                } else {
                    $df_delay_display = "<span style='color:#15803d; font-weight:bold;'>ON TIME</span>";
                }
        ?>

                <tr>
                    <td><?php echo $m++; ?></td>

                    <td>
                        <strong><?php echo $df_no_display; ?></strong>
                    </td>

                    <td>
                        <?php echo $df_download; ?>
                    </td>

                    <td>
                        <?php echo !empty($podate) ? $podate : '-'; ?>
                    </td>

                    <td>
                        <?php echo !empty($dfowner) ? strtoupper($dfowner) : '-'; ?>
                        <br>
                        <a href="<?php echo page_url; ?>Task/viewdfmeetingmom/<?php echo $df_id; ?>">
                            <span class="btn btn-success btn-xs">DF MEETING MOM</span>
                        </a>
                    </td>

                    <td>
                        <?php echo !empty($df_release_date) ? $df_release_date : '-'; ?>
                    </td>

                    <td>
                        <?php echo !empty($planneddate) ? $planneddate : '-'; ?>
                    </td>

                    <td>
                        <?php echo !empty($nextcompletiondate) ? $nextcompletiondate : '-'; ?>
                    </td>

                    <td>
                        <?php echo $df_status_display; ?>
                    </td>

                    <td>
                        <?php echo $df_delay_display; ?>
                    </td>

                    <!-- <td>
                        <?php //echo $lossdisplay; ?>
                    </td> -->

                    <td>
                        <a href="<?php echo page_url; ?>gantt/<?php echo $df_id; ?>" target="_blank">
                            <span class="btn btn-warning btn-xs">GANTT CHART</span>
                        </a>
                    </td>
                </tr>

        <?php
            }
        }
        ?>
    </tbody>

   <!--  <tfoot>
        <tr>
            <th colspan="10" style="text-align:right; font-size:14px;">
                CONSOLIDATED RUNNING LOSS
            </th>

            <th style="text-align:center; font-size:14px; color:#b91c1c;">
                <?php
                //if (method_exists($CIA->Task_model, 'formatIndianCurrency')) {
                  //  echo '<i class="fa fa-inr"></i> ' . $CIA->Task_model->formatIndianCurrency($grand_total_running_loss);
                //} else {
                  //  echo '<i class="fa fa-inr"></i> ' . number_format($grand_total_running_loss, 2);
                //}
                ?>
            </th>

            <th></th>
        </tr>
    </tfoot> -->
</table>


                                </div>

                            </div>
                        </div>



                    </div>

  <?php } ?>
                </div>

                <div class="col-sm-3" style="display:none">
                    <div class="main__box" style="overflow-y:scroll;height: 400px;background-color: whitesmoke;">
                        <div class="reminder">
                            <h6><i class="fa fa-bell"></i>&nbsp;MISSED </h6>
                        </div>
  <?php
                        //$q = $this->db->select('')->from('task_department_wise_scheduling')->join('')->where('')->get();

                        for ($i = 0; $i < 1; $i++) {
                            $back = "#d0fffe";
                        ?>
                        <div class="read_box">
                            <div class="top_box" style="background-color:#d0fffe">
                                <p><i>UPCOMING DF MEETING FOR Upcoming DF Meeting For DF-1200 On 4Th March 2024 at 14:00
                                        PM</i></p>
                                <!-- <div class="time_btn">
<div class="row" style="align-items: right;">


<p>10:00Am</p>

</div>

</div> -->
                            </div>

                        </div>
 <?php } ?>
                    </div>
                    <!-- <div class="main__box" style="overflow-y:scroll;height: 400px;background-color: whitesmoke;">
<div class="reminder">
<h6><i class="fa fa-bullhorn"></i> Notifications</h6>
</div>

<div class="read_box" id="showlivenotifications">


</div>


</div> -->
                </div>

                <!-- <div class="col-md-12 card-box" style="background-color:aliceblue;">
<div class="col-md-12">
<h3 class="page-title text-center" style="font-weight: 600;color:black;">DF Task Progress</h3>
</div>
<div class="col-md-12">
<div class="col-md-3"></div>
<div class="col-md-6">
<div class="form-group">
<select class="form-control">
<option value="1200">DF 1200</option>
<option value="1201">DF 1201</option>

</select>
</div>
</div>
</div>
<div class="col-md-12"><div id="chart-container"></div></div>

</div> -->
            </div>  


            <style>
                /* Update Task Status modal: scoped to avoid changing other dashboard modals. */
                #updateprogress .task-status-dialog {
                    width: min(620px, calc(100% - 30px));
                    margin: 48px auto;
                }

                #updateprogress .task-status-content {
                    overflow: hidden;
                    border: 0;
                    border-radius: 16px;
                    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.28);
                }

                #updateprogress .task-status-header {
                    position: relative;
                    padding: 22px 72px 20px 28px;
                    color: #ffffff;
                    border: 0;
                    background: linear-gradient(135deg, #168a4a 0%, #20ad60 100%);
                }

                #updateprogress .task-status-header .modal-title {
                    margin: 0;
                    color: #ffffff;
                    font-size: 21px;
                    font-weight: 700;
                    line-height: 1.35;
                    text-align: left;
                }

                #updateprogress .task-status-close {
                    position: absolute;
                    top: 50%;
                    right: 22px;
                    display: flex;
                    width: 36px;
                    height: 36px;
                    padding: 0;
                    align-items: center;
                    justify-content: center;
                    transform: translateY(-50%);
                    color: #ffffff;
                    background: rgba(255, 255, 255, 0.16);
                    border: 1px solid rgba(255, 255, 255, 0.42);
                    border-radius: 50%;
                    font-family: Arial, sans-serif;
                    font-size: 27px;
                    font-weight: 400;
                    line-height: 1;
                    opacity: 1;
                    text-shadow: none;
                    transition: background-color .2s ease, transform .2s ease;
                }

                #updateprogress .task-status-close:hover,
                #updateprogress .task-status-close:focus {
                    color: #ffffff;
                    background: rgba(255, 255, 255, 0.28);
                    opacity: 1;
                    outline: 2px solid rgba(255, 255, 255, 0.75);
                    outline-offset: 2px;
                    transform: translateY(-50%) scale(1.06);
                }

                #updateprogress .task-status-body {
                    padding: 26px 30px 30px;
                    background: #ffffff;
                }

                #updateprogress .task-status-body .form-group {
                    margin-bottom: 20px;
                }

                #updateprogress .task-status-body label {
                    margin-bottom: 8px;
                    color: #334155;
                    font-size: 13px;
                    font-weight: 700;
                    letter-spacing: .02em;
                }

                #updateprogress .task-status-body .form-control {
                    min-height: 46px;
                    padding: 10px 14px;
                    color: #1e293b;
                    background-color: #f8fafc;
                    border: 1px solid #dbe3ec;
                    border-radius: 9px;
                    box-shadow: none;
                    transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
                }

                #updateprogress .task-status-body .form-control:focus {
                    background-color: #ffffff;
                    border-color: #1baa5d;
                    box-shadow: 0 0 0 3px rgba(27, 170, 93, 0.13);
                }

                #updateprogress .task-status-body textarea.form-control {
                    min-height: 112px;
                    resize: vertical;
                }

                #updateprogress .task-status-submit {
                    width: 100%;
                    min-height: 44px;
                    color: #ffffff;
                    background: linear-gradient(135deg, #168a4a 0%, #20ad60 100%);
                    border: 0;
                    border-radius: 9px;
                    box-shadow: 0 8px 18px rgba(22, 138, 74, 0.22);
                    font-size: 15px;
                    font-weight: 700;
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                #updateprogress .task-status-submit:hover,
                #updateprogress .task-status-submit:focus {
                    color: #ffffff;
                    transform: translateY(-1px);
                    box-shadow: 0 10px 22px rgba(22, 138, 74, 0.3);
                }

                @media (max-width: 767px) {
                    #updateprogress .task-status-dialog {
                        margin: 20px auto;
                    }

                    #updateprogress .task-status-header {
                        padding: 18px 64px 18px 22px;
                    }

                    #updateprogress .task-status-body {
                        padding: 22px 20px 24px;
                    }
                }
            </style>

            <div id="updateprogress" class="modal fade" role="dialog" aria-labelledby="updateTaskStatusTitle">
                <form id="updateprogressform" method="post"
                    action="<?php echo page_url; ?>Task/updatetaskremarks"
                    enctype="multipart/form-data">
                    <div id="pageloader1">
                        <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
                    </div>
                    <div class="modal-dialog task-status-dialog">
                        <!-- Modal content-->
                        <div class="modal-content task-status-content">
                            <div class="modal-header task-status-header">
                                <button type="button" class="close task-status-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="updateTaskStatusTitle">Update Task Status
                                </h4>
                            </div>
                            <div class="modal-body task-status-body">
                                <div class="row">

                                    <div class="col-md-12">
                                        <input type="hidden" id="taskkiid" value="" name="taskkiid">
                                        <input type="hidden" id="mastertaskid" value="" name="mastertaskid">
                                        <input type="hidden" id="shortorder" value="" name="shortorder">
                                        <input type="hidden" id="progressdfno" value="" name="progressdfno">
                                        <input type="hidden" id="start_date" value="" name="start_date">
                                        <div class="form-group">
                                            <label>Status <span style="color:red" id="error_taskstatus">*</span></label>
                                            <select name="taskstatus" id="taskstatus" onchange="checkifpendingdue(); <?php if ($_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) { ?>checkifdoneDate(); <?php } ?>" class="form-control" required>
                                                <option value="1">Done</option>
                                                <option value="0">Pending</option>
                                                <!-- <option value="2">Send to Previous Step</option> -->
                                            </select>
                                        </div>
                                    </div>

                                </div>

                               <?php if ($_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) { ?>
                                    <div class="row" id="doneDiv" style="">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="date" name="doneDate" id="doneDate" class="form-control" max="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row" id="helpticketdiv" style="display:none">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Do you want to Raise help ticket?</label>
                                            <select class="form-control" name="ticketcondition" id="ticketcondition"
                                                onchange="checkifticket();">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function checkifticket() {
                                            var ticketcondition = $("#ticketcondition").val();

                                            if (ticketcondition == 1) {
                                                $("#departmentdiv").show();
                                                $("#userdiv").show();

                                            } else {
                                                $("#departmentdiv").hide();
                                                $("#userdiv").hide();

                                            }
                                        }
                                    </script>

                                    <div class="col-md-6" style="display:none" id="departmentdiv">
                                        <div class="form-group">
                                            <label>Department</label>
                                           <select class="form-control" name="selectdepartment" id="selectdepartment" onchange="getalldepartmentwiseuser();">
                                                <option value="">Select Department</option>
                                                    <?php
                                                    $q = $this->db->select('b.department_id, b.department')
                                                    ->from('task_management a')
                                                    ->join('departments b', 'a.department_id = b.department_id', 'left')
                                                    ->where('b.business_loc_id', 2)
                                                    ->where('b.status', 1)
                                                    ->group_by('b.department_id')  // Changed to b.department_id
                                                    ->order_by('b.department', 'asc') // Changed to b.department
                                                    ->get();

                                                    foreach ($q->result() as $dpt) { ?>
                                                    <option value="<?php echo $dpt->department_id; ?>">
                                                    <?php echo ucfirst(strtolower($dpt->department)); ?>
                                                    </option>
                                                    <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function getalldepartmentwiseuser() {
                                            var departmentid = $("#selectdepartment").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getdepartmentwiseusers",
                                                data: "departmentid=" + departmentid,
                                                success: function(data) {
                                                    $("#departmentuser").html(data);
                                                }
                                            });
                                        }
                                    </script>
                                    <div class="col-md-6" style="display:none" id="userdiv">
                                        <div class="form-group">
                                            <label>Select User</label>
                                            <select class="form-control" name="departmentuser" id="departmentuser">
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="display:none" id="sendtopreviousstep">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Send Previous Step</label>
                                            <select class="form-control task_dfno_filter" name="previousstep"
                                                id="previousstep">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Remarks <span style="color:red"
                                                    id="error_taskremarks"></span></label>
                                            <textarea name="taskremarks" id="taskremarks" class="form-control"
                                                required></textarea>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <input type="submit" name=""
                                            onclick="taskupdationvalidation();" value="Submit" class="btn btn-success task-status-submit">
                                    </div>
                                </div>

                            </div>
                            <!-- <div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
                        </div>

                    </div>
                </form>
            </div>

            <div id="updateprogress1" class="modal fade" role="dialog">
                <form id="updateprogressform" method="post"
                    action="<?php echo page_url; ?>Task/reassignselectedtask"
                    enctype="multipart/form-data">
                    <div id="pageloader1">
                        <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
                    </div>
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" style="font-weight: bold; text-align:center;">Reassign Task</h4>
                                <h5 style="text-align:center; font-weight:bold;" id="currenctlyassigned"></h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="col-md-12">
                                        <input type="hidden" id="selectedtasktoreassign" value=""
                                            name="selectedtasktoreassign">
                                        <div class="form-group">
                                            <label>Reassign to <span style="color:red"
                                                    id="error_taskstatus">*</span></label>
                                            <select name="showuserstoreassign" id="showuserstoreassign"
                                                class="form-control" required>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="display:none">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea class="form-control" name="predefinedmessage"
                                                id="predefinedmessage"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Remarks <span style="color:red"
                                                    id="error_taskremarks"></span></label>
                                            <textarea name="reassignremarks" id="reassignremarks"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <input type="submit" style="width: 100%;" name="" value="Submit"
                                            class="btn btn-success">
                                    </div>
                                </div>

                            </div>
                            <!-- <div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
                        </div>

                    </div>
                </form>
            </div>


            <script type="text/javascript">
                function checkifpendingdue() {
                    var taskstatus = $("#taskstatus").val();
                    var sortorder = $("#shortorder").val();
                    $("#doneDiv").css('display', 'none');
                    $("#doneDate").attr('required', false);
                    if (taskstatus == 0) {
                        $("#helpticketdiv").show();
                    } else {
                        $("#helpticketdiv").hide();
                        $("#doneDiv").css('display', '');
                        $("#doneDate").attr('required', true);
                    }
                    if (taskstatus == 2) {
                        $("#sendtopreviousstep").show();

                        $.ajax({
                            type: "post",
                            url: "<?php echo page_url; ?>Task/getallprevioussteps",
                            data: "sortorder=" + sortorder,
                            success: function(data) {
                                $("#previousstep").html(data);
                            }
                        });
                        $("#taskremarks").attr('Required', true);
                    } else if (taskstatus == 0) {

                        $("#taskremarks").attr('Required', true);
                    } else {
                        $("#sendtopreviousstep").hide();

                    }
                }


                function checkifdoneDate() {
                    var taskstatus = $("#taskstatus").val();
                    $("#doneDiv").css('display', 'none');
                    $("#doneDate").attr('required', false);
                    if (taskstatus == 0) {
                        // $("#helpticketdiv").show();
                    } else {
                        $("#doneDiv").css('display', '');
                        $("#doneDate").attr('required', true);
                    }

                }
            </script>

            <div id="dfmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" style="display: none;">

                <form id="loginForm" method="post" action="<?php echo page_url; ?>Task/dfrelease"
                    enctype="multipart/form-data">
                    <div id="pageloader">
                        <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
                    </div>
                    <input type="hidden" name="dfrecordid" id="dfrecordid" value="">
                    <input type="hidden" name="poid" id="poid" value="">

                    <div class="modal-dialog">

                        <div class="modal-content">

                            <div class="modal-header">

                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                <h4 class="modal-title">Release DF</h4>

                            </div>

                            <div class="modal-body">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Upload DF  <span id="error_uploaddf" style="color:red;">*</span></label>
                                           
                                            <input type="file" class="form-control" name="uploaddf" id="uploaddf">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">DF No. <span id="error_dfno" style="color:red;">*</span></label>
                                           
                                            <input type="text" class="form-control" name="dfno" id="dfno" value="" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>DF Description <span id="error_df_description"
                                                    style="color:red;">*</span></label>

                                            <input type="text" class="form-control" id="df_description"
                                                name="df_description" value="" required>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="button" class="btn btn-default waves-effect"
                                    data-dismiss="modal">Close</button>

                                <input type="submit" id="savedata" class="btn btn-info" value="Submit">

                            </div>

                        </div>



                </form>

            </div><!-- /.modal -->



   <?php $this->load->view('common/footer'); ?><!-- End Footer -->

        </div> <!-- end container -->
    </div>
    <!-- end wrapper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <!-- jQuery  -->
    <!-- <script src="<?php echo assets_url; ?>js/jquery.min.js"></script> -->
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

    <!-- Datatables-->

    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>




    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>

    <script>
        var exampleAjaxUrl = "<?php echo page_url; ?>Task/ongoingtasklist";
        var example2AjaxUrl = "<?php echo page_url; ?>Task/outdatedtask";
        var example3AjaxUrl = "<?php echo page_url; ?>Task/completeddf";
        var example8AjaxUrl = "<?php echo page_url; ?>Task/completeddfpendingforapproval";
        var example9AjaxUrl = "<?php echo page_url; ?>Df_change_control/dashboard_assigned_tasks";

        // A table whose data could not be fetched must never throw an alert at
        // the user. The loader below retries quietly and logs to the console.
        if ($.fn.dataTable) {
            $.fn.dataTable.ext.errMode = 'none';
        }

        $(document).on('error.dt', function(e, settings, techNote, message) {
            console.error('DataTables error', techNote, message);
        });

        function getDashboardTableRows(parsedResponse) {
            if (parsedResponse && $.isArray(parsedResponse.aaData)) {
                return parsedResponse.aaData;
            }

            if (parsedResponse && $.isArray(parsedResponse.data)) {
                return parsedResponse.data;
            }

            if ($.isArray(parsedResponse)) {
                return parsedResponse;
            }

            return [];
        }

        // Every dashboard table used to fire its request the moment the page
        // loaded, so one dashboard opened six database connections at once.
        // With several people on the dashboard together the server starts
        // refusing connections, the request comes back as an HTML error page
        // instead of JSON, and DataTables shows its "Ajax error" warning.
        // Run at most two of these requests at a time to keep that from
        // happening, and retry the ones that still fail.
        var dashboardAjaxQueue = {
            active: 0,
            maxActive: 2,
            waiting: [],

            add: function(job) {
                this.waiting.push(job);
                this.run();
            },

            run: function() {
                var self = this;

                while (self.active < self.maxActive && self.waiting.length > 0) {
                    var job = self.waiting.shift();
                    self.active++;

                    job(function() {
                        self.active--;
                        self.run();
                    });
                }
            }
        };

        var DASHBOARD_TABLE_MAX_ATTEMPTS = 3;

        function retryDashboardTable(tableId, ajaxUrl, attempt, done) {
            // Back off, and stagger the retry so that every browser sitting on
            // the dashboard does not hit the server again at the same moment.
            var waitFor = (1200 * attempt) + Math.floor(Math.random() * 800);

            setTimeout(function() {
                requestDashboardTable(tableId, ajaxUrl, attempt + 1, done);
            }, waitFor);
        }

        function requestDashboardTable(tableId, ajaxUrl, attempt, done) {
            $.ajax({
                url: ajaxUrl,
                method: "GET",
                dataType: "text",
                cache: false
            }).done(function(responseText) {
                var parsedResponse = null;

                try {
                    parsedResponse = JSON.parse(responseText);
                } catch (error) {
                    console.error(tableId + ' invalid JSON response (attempt ' + attempt + ')', error);
                }

                if (parsedResponse === null && attempt < DASHBOARD_TABLE_MAX_ATTEMPTS) {
                    retryDashboardTable(tableId, ajaxUrl, attempt, done);
                    return;
                }

                done(getDashboardTableRows(parsedResponse));
            }).fail(function(xhr, status, error) {
                console.error(tableId + ' ajax failed (attempt ' + attempt + ')', status, error);

                if (attempt < DASHBOARD_TABLE_MAX_ATTEMPTS) {
                    retryDashboardTable(tableId, ajaxUrl, attempt, done);
                    return;
                }

                done([]);
            });
        }

        function loadDashboardTableSafely(tableId, ajaxUrl, callback, afterLoad) {
            dashboardAjaxQueue.add(function(jobDone) {
                requestDashboardTable(tableId, ajaxUrl, 1, function(rows) {
                    jobDone();

                    callback({
                        data: rows
                    });

                    if (typeof afterLoad === 'function') {
                        afterLoad();
                    }
                });
            });
        }

        function changeDoneDate(id) {

            var ddate = $("#donedate" + id).val();
            if (ddate != '') {
                $.ajax({
                    url: '<?php echo page_url . 'Task/changeTaskCompleteDate/'; ?>' + id + "/" + ddate,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            $('#example3').DataTable().ajax.reload(getcolors, false);
                            if ($.fn.DataTable.isDataTable('#example8')) {
                                $('#example8').DataTable().ajax.reload(getcolors, false);
                            }
                        } else if (response && response.message) {
                            alert(response.message);
                        } else {
                            alert('Unable to update the completion date right now.');
                        }
                    },
                    error: function() {
                        alert('Unable to update the completion date right now.');
                    }

                });
            }


        }



        $(document).ready(function() {
            var a = $("#task_date_filter").val();

            $('#example').dataTable({

                "bProcessing": true,
                "pagination": true,
                "ajax": function(data, callback) {
                    loadDashboardTableSafely('example', exampleAjaxUrl, callback);
                },
                stateSave: true,
                "aoColumns": [

                    {
                        mData: 'sr_no'
                    },
                    {
                        mData: 'df_no'
                    },
                    {
                        mData: 'dfupload'
                    },
                    {
                        mData: 'df_release_date'
                    },
                    {
                        mData: 'department'
                    },
                    {
                        mData: 'membername'
                    },
                    {
                        mData: 'task_name'
                    },
                    {
                        mData: 'startdate'
                    },
                    {
                        mData: 'end_date'
                    },
                    {
                        mData: 'pendingdays'
                    },
                    {
                        mData: 'updateprogress'
                    },
                    {
                        mData: 'remarks'
                    },
                ]

            });

            if ($('#example2').length) {
                $('#example2').DataTable({
                    processing: true,
                    paging: true,
                    stateSave: true,
                    ajax: function(data, callback) {
                        loadDashboardTableSafely('example2', example2AjaxUrl, callback);
                    },
                    columns: [{
                            data: 'sr_no',
                            defaultContent: ''
                        },
                        {
                            data: 'df_no',
                            defaultContent: ''
                        },
                        {
                            data: 'dfupload',
                            defaultContent: ''
                        },
                        {
                            data: 'df_release_date',
                            defaultContent: ''
                        },
                        {
                            data: 'department',
                            defaultContent: ''
                        },
                        {
                            data: 'membername',
                            defaultContent: ''
                        },
                        {
                            data: 'task_name',
                            defaultContent: ''
                        },
                        {
                            data: 'end_date',
                            defaultContent: ''
                        },
                        {
                            data: 'pendingdays',
                            defaultContent: ''
                        },
                        {
                            data: 'updateprogress',
                            defaultContent: ''
                        },
                        {
                            data: 'remarks',
                            defaultContent: ''
                        }
                    ]
                });
            }

            if ($('#example3').length) {
                $('#example3').DataTable({
                    processing: true,
                    paging: true,
                    scrollX: true,
                    stateSave: true,
                    ajax: function(data, callback) {
                        loadDashboardTableSafely('example3', example3AjaxUrl, callback, getcolors);
                    },
                    columns: [{
                            data: 'sr_no',
                            defaultContent: ''
                        },
                        {
                            data: 'df_no',
                            defaultContent: ''
                        },
                        {
                            data: 'dfupload',
                            defaultContent: ''
                        },
                        {
                            data: 'df_release_date',
                            defaultContent: ''
                        },
                        {
                            data: 'department',
                            defaultContent: ''
                        },
                        {
                            data: 'membername',
                            defaultContent: ''
                        },
                        {
                            data: 'taskname',
                            defaultContent: ''
                        },
                        {
                            data: 'tasktat',
                            defaultContent: ''
                        },
                        {
                            data: 'addedon',
                            defaultContent: ''
                        },
                        {
                            data: 'taskdelay',
                            defaultContent: ''
                        },
                        {
                            data: 'status',
                            defaultContent: ''
                        },
                        {
                            data: 'remarks',
                            defaultContent: ''
                        },
                        {
                            data: 'addedby',
                            defaultContent: ''
                        }
                    ]
                });
            }


            if ($('#example8').length) {
                $('#example8').DataTable({
                    processing: true,
                    paging: true,
                    scrollX: true,
                    stateSave: true,
                    ajax: function(data, callback) {
                        loadDashboardTableSafely('example8', example8AjaxUrl, callback, getcolors);
                    },
                    columns: [{
                            data: 'sr_no',
                            defaultContent: ''
                        },
                        {
                            data: 'df_no',
                            defaultContent: ''
                        },
                        {
                            data: 'dfupload',
                            defaultContent: ''
                        },
                        {
                            data: 'df_release_date',
                            defaultContent: ''
                        },
                        {
                            data: 'department',
                            defaultContent: ''
                        },
                        {
                            data: 'membername',
                            defaultContent: ''
                        },
                        {
                            data: 'taskname',
                            defaultContent: ''
                        },
                        {
                            data: 'tasktat',
                            defaultContent: ''
                        },
                        {
                            data: 'addedon',
                            defaultContent: ''
                        },
                        {
                            data: 'approveorreject',
                            defaultContent: ''
                        },
                        {
                            data: 'taskdelay',
                            defaultContent: ''
                        },
                        {
                            data: 'status',
                            defaultContent: ''
                        },
                        {
                            data: 'remarks',
                            defaultContent: ''
                        },
                        {
                            data: 'addedby',
                            defaultContent: ''
                        }
                    ]
                });
            }

            <?php if ($df_change_control_dashboard_tab_enabled) { ?>
            if ($('#example9').length) {
                $('#example9').DataTable({
                    processing: true,
                    paging: true,
                    scrollX: true,
                    stateSave: true,
                    ajax: function(data, callback) {
                        loadDashboardTableSafely('example9', example9AjaxUrl, callback);
                    },
                    columns: [{
                            data: 'sr_no',
                            defaultContent: ''
                        },
                        {
                            data: 'request_df',
                            defaultContent: ''
                        },
                        {
                            data: 'dfupload',
                            defaultContent: ''
                        },
                        {
                            data: 'department',
                            defaultContent: ''
                        },
                        {
                            data: 'membername',
                            defaultContent: ''
                        },
                        {
                            data: 'requesttype',
                            defaultContent: ''
                        },
                        {
                            data: 'targetdate',
                            defaultContent: ''
                        },
                        {
                            data: 'delay',
                            defaultContent: ''
                        },
                        {
                            data: 'status',
                            defaultContent: ''
                        },
                        {
                            data: 'remarks',
                            defaultContent: ''
                        },
                        {
                            data: 'updateprogress',
                            defaultContent: ''
                        }
                    ]
                });
            }
            <?php } ?>

            $('#example3').on('draw.dt', function() {
                // do action here

                getcolors();
            });

            $('#example3').on('search.dt', function() {

                getcolors();
            });




            $('#example5').dataTable({
                "bProcessing": true,
                "pagination": true,
                stateSave: true,
                "dom": '<"top"lf>rt<"bottom"ip><"clear">'
            });

            $('#example6').dataTable({
                "bProcessing": true,
                "pagination": true,
                stateSave: true,
                "dom": '<"top"lf>rt<"bottom"ip><"clear">'
            });


            $('#example').on('draw.dt', function() {
                // do action here

                getcolors();
            });

            $('#example').on('search.dt', function() {

                getcolors();
            });

            if ($('#example4').length) {
                $('#example4').DataTable({
                    processing: true,
                    paging: true,
                    ajax: function(data, callback) {
                        loadDashboardTableSafely('example4', "<?php echo page_url; ?>Task/pendingtoassigndf", callback);
                    },
                    columns: [{
                            data: 'sr_no',
                            defaultContent: ''
                        },
                        {
                            data: 'df_no',
                            defaultContent: ''
                        },
                        {
                            data: 'df_release_date',
                            defaultContent: ''
                        },
                        {
                            data: 'downloaddf',
                            defaultContent: ''
                        },
                        {
                            data: 'assigment',
                            defaultContent: ''
                        }
                    ]
                });
            }
        });


        // function getcolors() {
        // $("#example3 tr").each(function() {
        // var currentRow = $(this);
        // var col1_value = currentRow.find("td:eq(10)").text();
        //  if (col1_value == 'On Time') {
        //                     currentRow.addClass("c11");
        //                 } else if (col1_value == 'Delayed') {
        //                     currentRow.addClass("c22")
        //                 } else if (col1_value == 'Early') {
        //                     currentRow.addClass("c33")
        //                 }



        // });
        // }
    </script>



    <script>
        function openCity(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        document.getElementById("defaultOpen").click();
    </script>

    <script>
        //setInterval('updateTimer()', 1000);


        function updateTimer1() {
            future = Date.parse("April 22, 2024 11:30:00");
            now = new Date();
            diff = now - future;

            days = Math.floor(diff / (1000 * 60 * 60 * 24));
            hours = Math.floor(diff / (1000 * 60 * 60));
            mins = Math.floor(diff / (1000 * 60));
            secs = Math.floor(diff / 1000);

            d = days;
            h = hours - days * 24;
            m = mins - hours * 60;
            s = secs - mins * 60;

            document.getElementById("timer1")
                .innerHTML =
                '<div>' + d + '<span> Days </span>' + m + '<span> min</span></div>' +
                // '<div>' + h + '</div>' +
                // '<div>' + m + '<span> min</span></div>';
                '<div>' + s + ' sec</div>';


        }
        //setInterval('updateTimer1()', 1000);



        function show_update_popup() {
            if ($('input[class=update_task]:checked')) {
                $("#myModal").modal('show');
            } else {

            }

        }
    </script>


    <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            unassignednotificationcount();
            ongoingtaskcountnotification();
            overduetaskcountnotification();
            completeddfnotificationcount();
            completeddfnotificationcountforapproval();
            <?php if ($df_change_control_dashboard_tab_enabled) { ?>
            changecontroltaskcountnotification();
            <?php } ?>
            // setInterval(tiggernotification, 100000);
            //setInterval(getnotificationdata, 10000);
            setInterval(unassignednotificationcount, 30000);
            setInterval(ongoingtaskcountnotification, 30000);
            setInterval(overduetaskcountnotification, 30000);
            setInterval(completeddfnotificationcount, 30000);
            setInterval(completeddfnotificationcountforapproval, 30000);
            <?php if ($df_change_control_dashboard_tab_enabled) { ?>
            setInterval(changecontroltaskcountnotification, 30000);
            <?php } ?>
            // toastr.options.onCloseClick= function() { alert("You clicked close button!"); };
            // tiggernotification();
            $("#savedata").click(function() {
                var uploaddf = $("#uploaddf").val();
                if (uploaddf == '') {
                    $("#error_uploaddf").html('Required!');
                    $("#uploaddf").css("border", "1px solid red");
                }

                var dfno = $("#dfno").val();
                if (dfno == '') {
                    $("#error_dfno").html('Required!');
                    $("#dfno").css("border", "1px solid red");
                }

                if (uploaddf == '' || dfno == '') {

                    return false;
                }

            });

        });

        function taskupdationvalidation() {
            var taskstatus = $("#taskstatus").val();
            if (taskstatus == '') {
                $("#error_taskstatus").html('Required!');
                $("#taskstatus").css("border", "1px solid red");
            } else {

                if (taskstatus == 0) {
                    $("#error_taskremarks").html('Required!');
                    $("#taskremarks").css("border", "1px solid red");
                    return false;
                }
            }


            if (taskstatus == '') {

                return false;
            }
        }
    </script>

    <script type="text/javascript">
        function updatedfrelease(id, poid, dfno) {

            $("#dfmodal").modal('show');
            $("#dfrecordid").val(id);
            $("#poid").val(poid);
            $("#dfno").val(dfno);
            //getdfinattachment(poid);
        }
    </script>
    <script type="text/javascript">
        function getdfinattachment(id){
           var poid = id;
           
           $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/fetchcreateddffile",
                data: "poid=" + poid,
                success: function(data) {
                    $("#showuserstoreassign").html(data);
                }
            }); 

        }
    </script>

    <script type="text/javascript">
        function updateyourprogressremarks(id, sortorder, dfno, taskid, start_date) {

            $("#updateprogress").modal('show');
            $("#taskkiid").val(id);
            $("#mastertaskid").val(taskid);
            $("#shortorder").val(sortorder);
            $("#progressdfno").val(dfno);
            $("#start_date").val(start_date);


        }
    </script>
    <script type="text/javascript">
        function reassigntasktoanotheruser(id, departmentid, assigneduser) {
            $("#updateprogress1").modal('show');
            $("#selectedtasktoreassign").val(id);
            //$("#currenctlyassigned").html(currenctlyassigned);
            var department = departmentid;

            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/fetchreassignuserlist",
                data: "department=" + department + "&assigneduser=" + assigneduser,
                success: function(data) {
                    $("#showuserstoreassign").html(data);
                }
            });

            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/currentlyassignedto",
                data: "id=" + id,
                success: function(taskinfo) {
                    $("#currenctlyassigned").html(taskinfo);
                }
            });


        }
    </script>

    <?php if ($df_change_control_dashboard_tab_enabled) { ?>
    <div id="changeControlTaskModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="changeControlTaskForm">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" style="font-weight:bold; text-align:center;">Mark Change Control Task as Done</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action_id" id="changeControlActionId" value="">
                        <div class="alert alert-info" style="margin-bottom:15px;">
                            <strong id="changeControlTaskModalRef"></strong>
                            <div id="changeControlTaskModalDept" style="margin-top:4px;"></div>
                        </div>
                        <div class="form-group">
                            <label>Completion Remarks <span style="color:red;">*</span></label>
                            <textarea class="form-control" name="assignee_remarks" id="changeControlTaskRemarks" rows="4" required placeholder="Add the final completion note, delivery detail, or execution summary."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="changeControlTaskSubmitBtn">Mark as Done</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>

    <div id="dashboardHealthReportModal" class="modal fade health-report-modal" tabindex="-1" role="dialog" aria-labelledby="dashboardHealthReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="modal-title" id="dashboardHealthReportModalLabel">How is your dashboard health score calculated?</h4>
                </div>
                <div class="modal-body">
                    <div class="health-report-summary">
                        <div class="health-report-formula">
                            Your score: <?php echo $healthBaseScore; ?> - <?php echo $healthOverdueDeduction; ?> - <?php echo $healthDelayedDfDeduction; ?> - <?php echo $healthHelpTicketDeduction; ?> = <?php echo $healthScore; ?>%
                        </div>
                        <p class="health-report-help">
                            Start with <?php echo $healthBaseScore; ?> points. Subtract points for overdue tasks, delayed DFs, and open help tickets. The points left are your health score out of <?php echo $healthBaseScore; ?>, shown as a percentage.
                        </p>
                        <p class="health-report-help">
                            <strong>Formula:</strong> <?php echo $healthBaseScore; ?> &minus; overdue task points &minus; delayed DF points &minus; open help ticket points. Each category has a maximum number of points it can subtract, as shown below.
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="health-summary-box">
                                <span class="value"><?php echo $healthBaseScore; ?></span>
                                <span class="label">Starting Points</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="health-summary-box">
                                <span class="value"><?php echo $healthTotalDeduction; ?></span>
                                <span class="label">Points Subtracted</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="health-summary-box">
                                <span class="value"><?php echo $healthScore; ?>%</span>
                                <span class="label">Final Score</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="health-summary-box">
                                <span class="value"><?php echo $healthLabel; ?></span>
                                <span class="label">Health Status</span>
                            </div>
                        </div>
                    </div>

                    <div class="health-scope-box">
                        <div class="health-scope-title">What "<?php echo $dashboardScopeText; ?>" Means</div>
                        <div class="health-scope-desc"><?php echo $dashboardScopeDescription; ?></div>
                        <ul class="health-scope-points">
                            <?php foreach ($dashboardScopePoints as $scopePoint): ?>
                            <li><?php echo $scopePoint; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered health-report-table">
                            <thead>
                                <tr>
                                    <th>What we count</th>
                                    <th>Number now</th>
                                    <th>How the points are calculated</th>
                                    <th>Points subtracted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($healthBreakdownRows as $healthRow): ?>
                                <tr>
                                    <td><strong><?php echo $healthRow['metric']; ?></strong><br><?php echo $healthRow['note']; ?></td>
                                    <td><span class="health-pill"><?php echo $healthRow['count']; ?></span></td>
                                    <td>
                                        <?php echo $healthRow['count']; ?> &times; <?php echo $healthRow['multiplier']; ?> points = <?php echo $healthRow['count'] * $healthRow['multiplier']; ?> points.<br>
                                        We subtract at most <?php echo $healthRow['cap']; ?> points for this category.
                                        <?php if (($healthRow['count'] * $healthRow['multiplier']) > $healthRow['cap']): ?>
                                        <br>The total exceeds this limit, so only <?php echo $healthRow['cap']; ?> points are subtracted.
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo $healthRow['deduction']; ?> points</strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="health-thresholds">
                        <strong>Why the score cannot go below <?php echo $healthMinimumScore; ?>%:</strong>
                        The three category limits add up to <?php echo $healthOverdueCap + $healthDelayedDfCap + $healthHelpTicketCap; ?> points (<?php echo $healthOverdueCap; ?> + <?php echo $healthDelayedDfCap; ?> + <?php echo $healthHelpTicketCap; ?>). Even with more issues, the current formula subtracts no more than this total.
                        <br>
                        <strong>What your score means:</strong> GOOD = 80% and above, ATTENTION = 50% to 79%, CRITICAL = below 50%.
                        <br>
                        <strong>Scope:</strong> <?php echo $dashboardScopeText; ?> on <?php echo date('d-M-Y h:i A'); ?>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function assigntasktousers(id, departmentid) {
            $("#assigntaskwindow").modal('show');
            $("#assigntaskdata").prop('disabled', true);
            $("#showdynamictask").html('<div class="col-md-12"><div class="alert alert-info" style="margin-bottom:0;">Loading assignable tasks...</div></div>');
            var dfid = id;
            var department = departmentid;
            //alert(department);
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/featch_dynamic_tasks",
                data: "dfid=" + dfid + "&department=" + department,
                success: function(data) {
                    $("#showdynamictask").html(data);
                    $("#assigntaskdata").prop('disabled', $("#showdynamictask").find('select[name=\"assignuser[]\"]').length === 0);
                },
                error: function() {
                    $("#showdynamictask").html('<div class="col-md-12"><div class="alert alert-danger" style="margin-bottom:0;">Task details could not be loaded. Please try again.</div></div>');
                    $("#assigntaskdata").prop('disabled', true);
                }
            });

        }
    </script>

    <script src="
https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.js
"></script>
    <script type="text/javascript">
        function tiggernotification() {
            var departmentid = '10';
            var userid = '161';
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/checknotification",
                data: "departmentid=" + departmentid + "&userid=" + userid,
                dataType: 'json',
                success: function(data) {
                    //alert(data);
                    jQuery.each(data, function(i, item) {
                        //alert(data[i].notification);
                        const bootoast = window.Bootoast;
                        bootoast.toast({
                            "message": data[i].notification,
                            "type": "danger",
                            "position": "top-right",
                            "icon": "",
                            "animationDuration": "300",
                            "dismissable": true
                        });
                    });
                }
            });



        }

        // function getnotificationdata(){
        //      var departmentid = '10';
        //     var userid = '161';
        //      $.ajax({
        //         type:"post",
        //         url:"https://shubhampack.in/beta1/index.php/Task/notificationuserwise",
        //         data:"departmentid="+departmentid+"&userid="+userid,
        //         success:function(data){
        //             //alert(data);
        //            $("#showlivenotifications").html(data);

        //         }
        //     });
        // }

        function unassignednotificationcount() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/unassignednotificationcount",
                success: function(data) {

                    $("#unassignednotificationcount").html(data);

                }
            });
        }

        function ongoingtaskcountnotification() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/ongoingtaskcountnotification",
                success: function(data) {
                    var badgeValue = $.trim(String(data));
                    $("#ongoingtaskcountnotification").html(badgeValue === '' ? ($("#ongoingtaskcountnotification").text() || '0') : badgeValue);
                },
                error: function() {
                    if ($.trim($("#ongoingtaskcountnotification").text()) === '') {
                        $("#ongoingtaskcountnotification").html('0');
                    }
                }
            });
        }

        function overduetaskcountnotification() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/overduetaskcountnotification",
                success: function(data) {
                    var badgeValue = $.trim(String(data));
                    $("#overduetaskcountnotification").html(badgeValue === '' ? ($("#overduetaskcountnotification").text() || '0') : badgeValue);
                },
                error: function() {
                    if ($.trim($("#overduetaskcountnotification").text()) === '') {
                        $("#overduetaskcountnotification").html('0');
                    }
                }
            });
        }

        function completeddfnotificationcount() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/completeddfnotificationcount",
                success: function(data) {
                    // alert(data);
                    $("#completeddfnotificationcount").html(data);

                }
            });
        }

        function completeddfnotificationcountforapproval() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/completeddfnotificationcountpendingforapproval",
                success: function(data) {
                    // alert(data);
                    $("#completeddfnotificationcountforapproval").html(data);

                }
            });
        }

        <?php if ($df_change_control_dashboard_tab_enabled) { ?>
        function changecontroltaskcountnotification() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Df_change_control/dashboard_assigned_task_count",
                success: function(data) {
                    $("#changecontroltaskcountnotification").html(data);
                }
            });
        }

        function openChangeControlTaskCompletionModal(actionId, changeNo, departmentName) {
            $("#changeControlActionId").val(actionId);
            $("#changeControlTaskModalRef").text(changeNo);
            $("#changeControlTaskModalDept").text(departmentName !== '' ? 'Department: ' + departmentName : '');
            $("#changeControlTaskRemarks").val('');
            $("#changeControlTaskSubmitBtn").prop('disabled', false).text('Mark as Done');
            $("#changeControlTaskModal").modal('show');
        }

        $("#changeControlTaskForm").on("submit", function(e) {
            e.preventDefault();

            var remarks = $.trim($("#changeControlTaskRemarks").val());
            if (remarks === '') {
                $("#changeControlTaskRemarks").css("border", "1px solid red");
                return false;
            }

            $("#changeControlTaskRemarks").css("border", "");
            $("#changeControlTaskSubmitBtn").prop('disabled', true).text('Saving...');

            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Df_change_control/dashboard_mark_done",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response && response.success) {
                        $("#changeControlTaskModal").modal('hide');
                        if ($.fn.DataTable.isDataTable('#example9')) {
                            $('#example9').DataTable().ajax.reload(null, false);
                        }
                        changecontroltaskcountnotification();
                        if (window.Bootoast && window.Bootoast.toast) {
                            window.Bootoast.toast({
                                message: response.message,
                                type: "success",
                                position: "top-right",
                                dismissable: true
                            });
                        } else {
                            alert(response.message);
                        }
                    } else {
                        if (window.Bootoast && window.Bootoast.toast) {
                            window.Bootoast.toast({
                                message: response && response.message ? response.message : 'Unable to update task.',
                                type: "danger",
                                position: "top-right",
                                dismissable: true
                            });
                        } else {
                            alert(response && response.message ? response.message : 'Unable to update task.');
                        }
                    }
                },
                error: function() {
                    if (window.Bootoast && window.Bootoast.toast) {
                        window.Bootoast.toast({
                            message: 'Unable to update task right now. Please try again.',
                            type: "danger",
                            position: "top-right",
                            dismissable: true
                        });
                    } else {
                        alert('Unable to update task right now. Please try again.');
                    }
                },
                complete: function() {
                    $("#changeControlTaskSubmitBtn").prop('disabled', false).text('Mark as Done');
                }
            });
        });
        <?php } ?>

        function filter_ongoing_task() {
            var filter = $("#task_date_filter").val();
            var departmentfilter = $("#task_department_filter").val();
            var usefilter = $("#task_user_filter").val();
            var dfno = $("#task_dfno_filter").val();
            var activeusertype = '1';
            exampleAjaxUrl = "<?php echo page_url; ?>Task/ongoingtasklist/" + filter + "/" + departmentfilter + "/" + usefilter + "/" + dfno + "/" + activeusertype;
            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().ajax.reload(null, false);
            }
            //$('#example').DataTable().ajax.reload();
        }

        function filter_overdue_task() {
            var filter = $("#task_number_filter_overdue").val();
            var fiterindays = $("#noofdaysdue").val();
            var departmentfilter = $("#task_department_filter_overdue").val();
            var usefilter = $("#task_user_filter_overdue").val();
            var dfno = $("#task_dfno_filter_overdue").val();
            example2AjaxUrl = "<?php echo page_url; ?>Task/outdatedtask/" + filter + "/" + departmentfilter + "/" + usefilter + "/" + dfno + "/" + fiterindays;
            if ($.fn.DataTable.isDataTable('#example2')) {
                $('#example2').DataTable().ajax.reload(null, false);
            }
            //$('#example').DataTable().ajax.reload();
        }

        function filter_completed_task() {
            var departmentfilter = $("#task_department_filter_completed").val();
            var usefilter = $("#task_user_filter_completed").val();
            var dfno = $("#task_dfno_filter_completed").val();
            example3AjaxUrl = "<?php echo page_url; ?>Task/completeddf/" + departmentfilter + "/" + usefilter + "/" + dfno;
            if ($.fn.DataTable.isDataTable('#example3')) {
                $('#example3').DataTable().ajax.reload(getcolors, false);
            }
            //$('#example').DataTable().ajax.reload();
        }

        function filter_completed_task_pending_for_approval() {
            var departmentfilter = $("#task_department_filter_completed_for_approval").val();
            var usefilter = $("#task_user_filter_completed_pending_for_approval").val();
            var dfno = $("#task_dfno_filter_completed_and_pending_for_approval").val();
            example8AjaxUrl = "<?php echo page_url; ?>Task/completeddfpendingforapproval/" + departmentfilter + "/" + usefilter + "/" + dfno;
            if ($.fn.DataTable.isDataTable('#example8')) {
                $('#example8').DataTable().ajax.reload(getcolors, false);
            }
            //$('#example').DataTable().ajax.reload();
        }


        function getcolors() {
            $("#example3 tr").each(function() {
                var currentRow = $(this);
                var col1_value = currentRow.find("td:eq(10)").text();
                // alert(col1_value);

                if (col1_value == 'On Time') {
                    currentRow.addClass("c11");
                } else if (col1_value == 'Delayed') {
                    currentRow.addClass("c22")
                } else if (col1_value == 'Early') {
                    currentRow.addClass("c33")
                }



            });
        }
    </script>


    <style>
        /* Keep assignment actions visible while long task lists scroll. */
        #assigntaskwindow .modal-dialog {
            width: calc(100% - 20px);
            max-width: 900px;
            margin: 10px auto;
        }

        #assigntaskwindow .modal-content {
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 20px);
            max-height: calc(100dvh - 20px);
        }

        #assigntaskwindow .modal-header,
        #assigntaskwindow .modal-footer {
            flex: 0 0 auto;
        }

        #assigntaskwindow .modal-body {
            min-height: 0;
            overflow-y: auto;
        }
    </style>

    <div id="assigntaskwindow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="assignTaskModalLabel"
        aria-hidden="true" style="display: none;">

        <form id="assigntasktoteammember" method="post"
            action="<?php echo page_url; ?>Task/assigntasktoteammember" enctype="multipart/form-data">
            <div id="pageloader2">
                <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
            </div>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="assignTaskModalLabel">Assign Task to Your Team Members</h4>
                    </div>

                    <div class="modal-body">

                        <div class="row">
                            <div id="showdynamictask"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <input type="submit" id="assigntaskdata" class="btn btn-info" value="Submit">

	                    </div>

	                </div>
	            </div>

	        </form>

	    </div>


    <script>
        $(document).ready(function() {
            $("#updateprogressform").on("submit", function() {
                $("#pageloader1").fadeIn();
            }); //submit
        }); //document ready
    </script>
    <script>
        $(document).ready(function() {
            $("#loginForm").on("submit", function() {
                $("#pageloader").fadeIn();
            }); //submit
        }); //document ready
    </script>
    <script>
        $(document).ready(function() {
            $("#assigntasktoteammember").on("submit", function() {
                $("#pageloader2").fadeIn();
            }); //submit
        }); //document ready

        function applysameUser() {
            if ($('#appall').is(':checked')) {
                var d = $(".firstClass").val();
                if (d != '') {
                    $(".allassignUser").val(d);
                } else {
                    $("#appall").prop('checked', false);
                }

            } else {
                //alert('bye');
            }

        }
    </script>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $('.task_dfno_filter').select2();
        });
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <script>
        (function() {
            var shownTicketNotifications = {};
            var shownDfSupportNotifications = {};

            function showBottomRightToast(message, onHidden, onClick) {
                if (!window.toastr) {
                    return;
                }

                var toast = window.toastr.info(message, "New Notification", {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    closeButton: true,
                    tapToDismiss: false,
                    positionClass: "toast-bottom-right",
                    onclick: onClick || null,
                    onHidden: onHidden
                });

                if (toast && typeof toast.css === "function") {
                    toast.css("cursor", onClick ? "pointer" : "default");
                }
            }

            function postJson(url, body) {
                return fetch(url, {
                    method: "POST",
                    credentials: "same-origin",
                    cache: "no-store",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: body || ""
                }).then(function(response) {
                    return response.json();
                }).catch(function() {
                    return [];
                });
            }

            function postQuiet(url, body) {
                if (navigator.sendBeacon) {
                    var payload = new Blob([body || ""], {
                        type: "application/x-www-form-urlencoded; charset=UTF-8"
                    });
                    navigator.sendBeacon(url, payload);
                    return;
                }

                fetch(url, {
                    method: "POST",
                    credentials: "same-origin",
                    cache: "no-store",
                    keepalive: true,
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: body || ""
                }).catch(function() {});
            }

            function markTicketNotificationAsRead(notificationId) {
                postQuiet(
                    "<?php echo page_url . 'Task/markNotificationAsRead'; ?>",
                    "notification_id=" + encodeURIComponent(notificationId)
                );
            }

            function markDfSupportNotificationAsRead(notificationId) {
                postQuiet(
                    "<?php echo page_url . 'Maintenance_support/mark_notifications_read'; ?>",
                    "notification_id=" + encodeURIComponent(notificationId)
                );
            }

            function pollTicketNotifications() {
                postJson("<?php echo page_url . 'Task/fetchNotifications'; ?>").then(function(data) {
                    var notifications = Array.isArray(data) ? data : [];

                    notifications.forEach(function(notification) {
                        if (shownTicketNotifications[notification.id]) {
                            return;
                        }

                        shownTicketNotifications[notification.id] = true;
                        showBottomRightToast(notification.message, function() {
                            markTicketNotificationAsRead(notification.id);
                        }, function() {
                            markTicketNotificationAsRead(notification.id);
                            if (notification.redirect_url) {
                                window.location.href = notification.redirect_url;
                            }
                        });
                    });
                });
            }

            function pollDfSupportNotifications() {
                postJson("<?php echo page_url . 'Maintenance_support/fetch_notificationsofusers'; ?>").then(function(data) {
                    var notifications = Array.isArray(data) ? data : [];

                    notifications.forEach(function(notification) {
                        if (shownDfSupportNotifications[notification.id]) {
                            return;
                        }

                        shownDfSupportNotifications[notification.id] = true;
                        showBottomRightToast(notification.message, function() {
                            markDfSupportNotificationAsRead(notification.id);
                        });
                    });
                });
            }

            function startDashboardNotifications() {
                pollTicketNotifications();
                pollDfSupportNotifications();
                setInterval(pollTicketNotifications, 5000);
                setInterval(pollDfSupportNotifications, 15000);
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", startDashboardNotifications);
            } else {
                startDashboardNotifications();
            }
        })();
    </script>

  <?php if ($this->session->userdata('growl_notification')): ?>
        <script>
            $(document).ready(function() {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr.info("<?php echo $this->session->userdata('growl_notification'); ?>");
            });
        </script>
        <?php $this->session->unset_userdata('growl_notification'); ?>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            // Handle collapse opening and closing
            $('.right_arrow').click(function() {
                var icon = $(this).find('.collapse_arrow');
                var collapseElement = $(this).next('.collapse');

                // Toggle the collapse
                collapseElement.on('show.bs.collapse', function() {
                    icon.css('transform', 'rotate(90deg)'); // Rotate the arrow when opening
                });

                collapseElement.on('hide.bs.collapse', function() {
                    icon.css('transform', 'rotate(0deg)'); // Reset the arrow when closing
                });
            });
        });
    </script>

  
   
</body>

</html>
