<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_management_model extends CI_Model
{
    private $task_table = 'task_management_items';
    private $update_table = 'task_management_updates';
    private $notification_table = 'task_management_notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function module_ready()
    {
        return $this->db->table_exists($this->task_table)
            && $this->db->table_exists($this->update_table)
            && $this->db->table_exists($this->notification_table);
    }

    public function format_user_name($title = '', $first_name = '', $last_name = '')
    {
        $parts = array_filter(array($title, $first_name, $last_name), function ($value) {
            return trim((string) $value) !== '';
        });

        return ucwords(strtolower(trim(implode(' ', $parts))));
    }

    public function generate_task_code($task_id)
    {
        return 'TM-' . date('ymd') . '-' . str_pad((int) $task_id, 4, '0', STR_PAD_LEFT);
    }

    public function get_business_locations()
    {
        $this->db->select('business_loc_id, company_name');
        $this->db->from('business_location');
        $this->db->where('business_loc_status', 1);
        $this->db->order_by('company_name', 'asc');

        return $this->db->get()->result_array();
    }

    public function get_department_filters()
    {
        return $this->db->select('d.department_id, d.department')
            ->from('departments d')
            ->join('system_users u', 'u.department_id = d.department_id AND u.user_status = 1 AND u.hide_profile = 0 AND u.business_location = 2', 'inner')
            ->where('d.status', 1)
            ->group_by('d.department_id')
            ->order_by('d.sort_order_for_customize', 'asc')
            ->order_by('d.department', 'asc')
            ->get()
            ->result_array();
    }

    public function get_assignable_users()
    {
        $this->db->select('u.user_id, u.title, u.first_name, u.last_name, u.email, u.contact_number, u.department_id, u.business_location, d.department');
        $this->db->from('system_users u');
        $this->db->join('departments d', 'd.department_id = u.department_id', 'left');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.hide_profile', 0);
        $this->db->where('u.business_location', 2);
        $this->db->order_by('d.sort_order_for_customize', 'asc');
        $this->db->order_by('d.department', 'asc');
        $this->db->order_by('u.first_name', 'asc');
        $this->db->order_by('u.last_name', 'asc');

        $rows = $this->db->get()->result_array();
        foreach ($rows as &$row) {
            $row['name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    public function get_user($user_id)
    {
        $row = $this->db->select('u.user_id, u.title, u.first_name, u.last_name, u.email, u.contact_number, u.department_id, u.business_location, d.department')
            ->from('system_users u')
            ->join('departments d', 'd.department_id = u.department_id', 'left')
            ->where('u.user_id', (int) $user_id)
            ->limit(1)
            ->get()
            ->row_array();

        if (!empty($row)) {
            $row['name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
        }

        return $row;
    }

    public function get_users($user_ids)
    {
        $user_ids = array_values(array_unique(array_filter(array_map('intval', (array) $user_ids))));
        if (empty($user_ids)) {
            return array();
        }

        $rows = $this->db->select('u.user_id, u.title, u.first_name, u.last_name, u.email, u.contact_number, u.department_id, u.business_location, d.department')
            ->from('system_users u')
            ->join('departments d', 'd.department_id = u.department_id', 'left')
            ->where_in('u.user_id', $user_ids)
            ->get()
            ->result_array();

        $users = array();
        foreach ($rows as $row) {
            $row['name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
            $users[(int) $row['user_id']] = $row;
        }

        return $users;
    }

    public function get_department($department_id)
    {
        return $this->db->select('department_id, department, departmenthead')
            ->from('departments')
            ->where('department_id', (int) $department_id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_business_location_name($business_location_id)
    {
        $row = $this->db->select('company_name')
            ->from('business_location')
            ->where('business_loc_id', (int) $business_location_id)
            ->limit(1)
            ->get()
            ->row_array();

        return !empty($row['company_name']) ? (string) $row['company_name'] : '';
    }

    public function get_dashboard_stats($user_id)
    {
        if (!$this->module_ready()) {
            return array(
                'assigned_open' => 0,
                'created_open' => 0,
                'awaiting_due' => 0,
                'overdue' => 0,
                'completed_this_week' => 0
            );
        }

        $user_id = (int) $user_id;
        $today = date('Y-m-d');
        $week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));

        $assigned_open = $this->db->where('assigned_to_user_id', $user_id)
            ->where('status !=', 'COMPLETED')
            ->count_all_results($this->task_table);

        $created_open = $this->db->where('assigned_by_user_id', $user_id)
            ->where('status !=', 'COMPLETED')
            ->count_all_results($this->task_table);

        $awaiting_due = $this->db->where('assigned_to_user_id', $user_id)
            ->where('status', 'AWAITING_DUE_DATE')
            ->count_all_results($this->task_table);

        $overdue = $this->db->where('assigned_to_user_id', $user_id)
            ->where('status !=', 'COMPLETED')
            ->where('committed_due_date IS NOT NULL', null, false)
            ->where('committed_due_date !=', '0000-00-00')
            ->where('committed_due_date <', $today)
            ->count_all_results($this->task_table);

        $completed_this_week = $this->db->where('assigned_to_user_id', $user_id)
            ->where('status', 'COMPLETED')
            ->where('completed_on >=', $week_start)
            ->count_all_results($this->task_table);

        return array(
            'assigned_open' => (int) $assigned_open,
            'created_open' => (int) $created_open,
            'awaiting_due' => (int) $awaiting_due,
            'overdue' => (int) $overdue,
            'completed_this_week' => (int) $completed_this_week
        );
    }

    public function get_open_assignment_count($user_id)
    {
        if (!$this->module_ready()) {
            return 0;
        }

        return (int) $this->db->where('assigned_to_user_id', (int) $user_id)
            ->where('status !=', 'COMPLETED')
            ->count_all_results($this->task_table);
    }

    public function get_assigned_tasks($user_id, $limit = 25)
    {
        return $this->get_task_list(array('t.assigned_to_user_id' => (int) $user_id), $limit);
    }

    public function get_created_tasks($user_id, $limit = 25)
    {
        return $this->get_task_list(array('t.assigned_by_user_id' => (int) $user_id), $limit);
    }

    public function get_all_tasks($filters = array(), $limit = 0)
    {
        return $this->get_task_list(array(), $limit, $filters);
    }

    public function get_status_filters()
    {
        return array(
            'AWAITING_DUE_DATE' => 'Awaiting Due Date',
            'OPEN' => 'Open',
            'IN_PROGRESS' => 'In Progress',
            'COMPLETED' => 'Completed'
        );
    }

    public function get_priority_filters()
    {
        return array(
            'LOW' => 'Low',
            'MEDIUM' => 'Medium',
            'HIGH' => 'High',
            'CRITICAL' => 'Critical'
        );
    }

    public function get_due_state_filters()
    {
        return array(
            'OVERDUE' => 'Overdue',
            'DUE_TODAY' => 'Due Today',
            'DUE_THIS_WEEK' => 'Due This Week',
            'NO_DUE_DATE' => 'No Due Date'
        );
    }

    private function get_task_list($where = array(), $limit = 25, $filters = array())
    {
        if (!$this->module_ready()) {
            return array();
        }

        $this->db->select('
            t.*,
            creator.title AS creator_title,
            creator.first_name AS creator_first_name,
            creator.last_name AS creator_last_name,
            creator.email AS creator_email,
            assignee.title AS assignee_title,
            assignee.first_name AS assignee_first_name,
            assignee.last_name AS assignee_last_name,
            assignee.email AS assignee_email,
            d.department,
            b.company_name
        ');
        $this->db->from($this->task_table . ' t');
        $this->db->join('system_users creator', 'creator.user_id = t.assigned_by_user_id', 'left');
        $this->db->join('system_users assignee', 'assignee.user_id = t.assigned_to_user_id', 'left');
        $this->db->join('departments d', 'd.department_id = t.assigned_to_department_id', 'left');
        $this->db->join('business_location b', 'b.business_loc_id = t.business_location_id', 'left');

        foreach ($where as $field => $value) {
            $this->db->where($field, $value);
        }

        if (!empty($filters['assigned_to_user_id'])) {
            $this->db->where('t.assigned_to_user_id', (int) $filters['assigned_to_user_id']);
        }

        if (!empty($filters['assigned_by_user_id'])) {
            $this->db->where('t.assigned_by_user_id', (int) $filters['assigned_by_user_id']);
        }

        if (!empty($filters['department_id'])) {
            $this->db->where('t.assigned_to_department_id', (int) $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('t.status', strtoupper((string) $filters['status']));
        }

        if (!empty($filters['priority'])) {
            $this->db->where('t.priority', strtoupper((string) $filters['priority']));
        }

        if (!empty($filters['keyword'])) {
            $keyword = trim((string) $filters['keyword']);
            $this->db->group_start();
            $this->db->like('t.task_code', $keyword);
            $this->db->or_like('t.title', $keyword);
            $this->db->or_like('t.task_details', $keyword);
            $this->db->or_like('creator.first_name', $keyword);
            $this->db->or_like('creator.last_name', $keyword);
            $this->db->or_like('assignee.first_name', $keyword);
            $this->db->or_like('assignee.last_name', $keyword);
            $this->db->or_like('d.department', $keyword);
            $this->db->group_end();
        }

        if (!empty($filters['due_state'])) {
            $today = date('Y-m-d');
            $week_end = date('Y-m-d', strtotime('+6 days'));
            $due_state = strtoupper((string) $filters['due_state']);

            if ($due_state === 'OVERDUE') {
                $this->db->where('t.status !=', 'COMPLETED');
                $this->db->where('t.committed_due_date IS NOT NULL', null, false);
                $this->db->where('t.committed_due_date !=', '0000-00-00');
                $this->db->where('t.committed_due_date <', $today);
            } elseif ($due_state === 'DUE_TODAY') {
                $this->db->where('t.committed_due_date', $today);
            } elseif ($due_state === 'DUE_THIS_WEEK') {
                $this->db->where('t.committed_due_date >=', $today);
                $this->db->where('t.committed_due_date <=', $week_end);
            } elseif ($due_state === 'NO_DUE_DATE') {
                $this->db->group_start();
                $this->db->where('t.committed_due_date IS NULL', null, false);
                $this->db->or_where('t.committed_due_date', '0000-00-00');
                $this->db->group_end();
            }
        }

        $this->db->order_by("CASE WHEN t.status = 'AWAITING_DUE_DATE' THEN 0 WHEN t.status = 'IN_PROGRESS' THEN 1 WHEN t.status = 'OPEN' THEN 2 WHEN t.status = 'COMPLETED' THEN 4 ELSE 3 END", '', false);
        $this->db->order_by("CASE WHEN t.committed_due_date IS NULL OR t.committed_due_date = '0000-00-00' THEN 1 ELSE 0 END", '', false);
        $this->db->order_by('t.committed_due_date', 'asc');
        $this->db->order_by('t.updated_on', 'desc');
        if ((int) $limit > 0) {
            $this->db->limit((int) $limit);
        }

        $rows = $this->db->get()->result_array();
        foreach ($rows as &$row) {
            $row = $this->hydrate_task_row($row);
        }
        unset($row);

        return $rows;
    }

    public function get_task($task_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $row = $this->db->select('
            t.*,
            creator.title AS creator_title,
            creator.first_name AS creator_first_name,
            creator.last_name AS creator_last_name,
            creator.email AS creator_email,
            creator.department_id AS creator_department_id,
            assignee.title AS assignee_title,
            assignee.first_name AS assignee_first_name,
            assignee.last_name AS assignee_last_name,
            assignee.email AS assignee_email,
            assignee.department_id AS assignee_department_id,
            d.department,
            b.company_name
        ')
            ->from($this->task_table . ' t')
            ->join('system_users creator', 'creator.user_id = t.assigned_by_user_id', 'left')
            ->join('system_users assignee', 'assignee.user_id = t.assigned_to_user_id', 'left')
            ->join('departments d', 'd.department_id = t.assigned_to_department_id', 'left')
            ->join('business_location b', 'b.business_loc_id = t.business_location_id', 'left')
            ->where('t.id', (int) $task_id)
            ->limit(1)
            ->get()
            ->row_array();

        return !empty($row) ? $this->hydrate_task_row($row) : array();
    }

    private function hydrate_task_row($row)
    {
        $row['creator_name'] = $this->format_user_name(
            isset($row['creator_title']) ? $row['creator_title'] : '',
            isset($row['creator_first_name']) ? $row['creator_first_name'] : '',
            isset($row['creator_last_name']) ? $row['creator_last_name'] : ''
        );
        $row['assignee_name'] = $this->format_user_name(
            isset($row['assignee_title']) ? $row['assignee_title'] : '',
            isset($row['assignee_first_name']) ? $row['assignee_first_name'] : '',
            isset($row['assignee_last_name']) ? $row['assignee_last_name'] : ''
        );
        $row['is_overdue'] = (
            !empty($row['committed_due_date'])
            && $row['committed_due_date'] !== '0000-00-00'
            && strtoupper((string) $row['status']) !== 'COMPLETED'
            && $row['committed_due_date'] < date('Y-m-d')
        );

        return $row;
    }

    public function create_task($data)
    {
        $this->db->insert($this->task_table, $data);
        return (int) $this->db->insert_id();
    }

    public function update_task($task_id, $data)
    {
        return $this->db->where('id', (int) $task_id)->update($this->task_table, $data);
    }

    public function add_update($data)
    {
        return $this->db->insert($this->update_table, $data);
    }

    public function get_task_updates($task_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('u.*, actor.title, actor.first_name, actor.last_name')
            ->from($this->update_table . ' u')
            ->join('system_users actor', 'actor.user_id = u.created_by', 'left')
            ->where('u.task_id', (int) $task_id)
            ->order_by('u.created_on', 'desc')
            ->order_by('u.id', 'desc')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['actor_name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    public function add_notifications($task_id, $user_ids, $message, $action_url = '')
    {
        if (!$this->module_ready()) {
            return;
        }

        $user_ids = array_values(array_unique(array_filter(array_map('intval', (array) $user_ids))));
        if (empty($user_ids)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($user_ids as $user_id) {
            $this->db->insert($this->notification_table, array(
                'task_id' => (int) $task_id,
                'user_id' => (int) $user_id,
                'message' => trim((string) $message),
                'action_url' => trim((string) $action_url),
                'is_read' => 0,
                'created_on' => $now
            ));
        }
    }

    public function get_unread_notifications($user_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        return $this->db->select('id, task_id, message, action_url, created_on')
            ->from($this->notification_table)
            ->where('user_id', (int) $user_id)
            ->where('is_read', 0)
            ->order_by('created_on', 'desc')
            ->limit(15)
            ->get()
            ->result_array();
    }

    /**
     * How many task updates this user has not seen yet.
     *
     * This is what the Task Management item in the main navigation shows: a
     * response ON a task the user is involved in - the assignee confirmed a
     * due date, posted progress, completed the work, or the creator reopened
     * it. Every one of those writes a row here through add_notifications().
     *
     * NOT the same number as get_open_assignment_count(), which is a workload
     * figure (how much is on my plate) and stays constant until the work
     * moves. This one is an inbox figure: it appears when somebody responds
     * and clears when the user has seen it.
     */
    public function get_unread_notification_count($user_id)
    {
        if (!$this->module_ready()) {
            return 0;
        }

        return (int) $this->db->where('user_id', (int) $user_id)
            ->where('is_read', 0)
            ->count_all_results($this->notification_table);
    }

    /**
     * Unread updates with the task they belong to, for the dashboard inbox.
     *
     * Joined to the task so the panel can show the task title and status
     * rather than only the notification sentence, and so a notification whose
     * task has since been deleted simply disappears instead of linking into a
     * 404.
     */
    public function get_recent_notifications($user_id, $limit = 8)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('n.id, n.task_id, n.message, n.action_url, n.created_on,
                                   t.task_code, t.title, t.status, t.progress_percent', FALSE)
            ->from($this->notification_table . ' n')
            ->join($this->task_table . ' t', 't.id = n.task_id', 'inner')
            ->where('n.user_id', (int) $user_id)
            ->where('n.is_read', 0)
            ->order_by('n.created_on', 'desc')
            ->order_by('n.id', 'desc')
            ->limit((int) $limit > 0 ? (int) $limit : 8)
            ->get()
            ->result_array();

        return $rows;
    }

    /**
     * Clear the notifications for one task once the user has actually opened
     * it.
     *
     * Without this the only way to clear a notification was to press Dismiss
     * or Open on the toast: somebody who reached the task from the dashboard,
     * a mail link or the update history left it unread for ever, and the
     * navigation badge stayed lit for something they had already read.
     */
    public function mark_task_notifications_read($task_id, $user_id)
    {
        if (!$this->module_ready()) {
            return false;
        }

        return $this->db->where('task_id', (int) $task_id)
            ->where('user_id', (int) $user_id)
            ->where('is_read', 0)
            ->update($this->notification_table, array(
                'is_read' => 1,
                'read_on' => date('Y-m-d H:i:s')
            ));
    }

    public function mark_notification_read($notification_id, $user_id)
    {
        return $this->db->where('id', (int) $notification_id)
            ->where('user_id', (int) $user_id)
            ->update($this->notification_table, array(
                'is_read' => 1,
                'read_on' => date('Y-m-d H:i:s')
            ));
    }

    public function mark_all_notifications_read($user_id)
    {
        return $this->db->where('user_id', (int) $user_id)
            ->where('is_read', 0)
            ->update($this->notification_table, array(
                'is_read' => 1,
                'read_on' => date('Y-m-d H:i:s')
            ));
    }

    public function get_user_team_leaders($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id <= 0) {
            return array();
        }

        $leaders = array();
        $seen = array();

        $team_leaders = $this->db->select('leader.user_id, leader.title, leader.first_name, leader.last_name, leader.email')
            ->from('presto_team_members m')
            ->join('prestogroup_teams t', 't.team_id = m.team_id AND t.status = 1', 'inner')
            ->join('system_users leader', 'leader.user_id = t.team_leader AND leader.user_status = 1', 'inner')
            ->where('m.employee_id', $user_id)
            ->get()
            ->result_array();

        foreach ($team_leaders as $row) {
            if (empty($row['user_id']) || isset($seen[$row['user_id']])) {
                continue;
            }
            $row['name'] = $this->format_user_name($row['title'], $row['first_name'], $row['last_name']);
            $leaders[] = $row;
            $seen[$row['user_id']] = true;
        }

        $employee_row = $this->db->select('reporting_manager')
            ->from('prestogroup_employees')
            ->where('user_id', $user_id)
            ->limit(1)
            ->get()
            ->row_array();

        if (!empty($employee_row['reporting_manager']) && !isset($seen[$employee_row['reporting_manager']])) {
            $manager = $this->get_user($employee_row['reporting_manager']);
            if (!empty($manager)) {
                $leaders[] = $manager;
                $seen[$manager['user_id']] = true;
            }
        }

        $user = $this->get_user($user_id);
        if (!empty($user['department_id'])) {
            $department = $this->get_department($user['department_id']);
            if (!empty($department['departmenthead']) && !isset($seen[$department['departmenthead']])) {
                $department_head = $this->get_user($department['departmenthead']);
                if (!empty($department_head)) {
                    $leaders[] = $department_head;
                    $seen[$department_head['user_id']] = true;
                }
            }
        }

        return $leaders;
    }
}
