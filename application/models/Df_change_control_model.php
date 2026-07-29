<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Df_change_control_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function module_ready()
    {
        return $this->db->table_exists('df_change_control')
            && $this->db->table_exists('df_change_control_departments')
            && $this->db->table_exists('df_change_control_history');
    }

    public function format_user_name($title = '', $first_name = '', $last_name = '')
    {
        $parts = array_filter(array($title, $first_name, $last_name), function ($value) {
            return trim((string)$value) !== '';
        });

        return ucwords(strtolower(trim(implode(' ', $parts))));
    }

    public function get_df_options($selected_df_id = 0)
    {
        $this->db->select('id, df_no, df_description, df_status');
        $this->db->from('df_release');
        if ($selected_df_id > 0) {
            $this->db->group_start();
            $this->db->where('df_status', 0);
            $this->db->or_where('id', (int)$selected_df_id);
            $this->db->group_end();
        }
        $this->db->order_by('id', 'desc');

        return $this->db->get()->result_array();
    }

    public function get_department_options()
    {
        $this->db->select('department_id, department, departmenthead, color');
        $this->db->from('departments');
        $this->db->where('business_loc_id', 2);
        $this->db->where('status', 1);
        $this->db->order_by('sort_order_for_customize', 'asc');
        $this->db->order_by('department', 'asc');

        return $this->db->get()->result_array();
    }

    public function get_department_members($department_id)
    {
        $this->db->select('user_id, title, first_name, last_name, email');
        $this->db->from('system_users');
        $this->db->where('department_id', (int)$department_id);
        $this->db->where('business_location', 2);
        $this->db->where('user_status', 1);
        $this->db->where('hide_profile', 0);
        $this->db->order_by('first_name', 'asc');
        $this->db->order_by('last_name', 'asc');

        return $this->db->get()->result_array();
    }

    public function resolve_department_head($department_id)
    {
        $department_id = (int)$department_id;

        $department = $this->db->select('departmenthead')
            ->from('departments')
            ->where('department_id', $department_id)
            ->get()
            ->row_array();

        if (!empty($department['departmenthead'])) {
            $head = $this->db->select('user_id, title, first_name, last_name, email')
                ->from('system_users')
                ->where('user_id', (int)$department['departmenthead'])
                ->where('user_status', 1)
                ->get()
                ->row_array();

            if (!empty($head)) {
                return $head;
            }
        }

        $team_head = $this->db->select('u.user_id, u.title, u.first_name, u.last_name, u.email')
            ->from('prestogroup_teams t')
            ->join('system_users u', 'u.user_id = t.team_leader', 'left')
            ->where('t.department_id', $department_id)
            ->where('t.status', 1)
            ->where('u.user_status', 1)
            ->order_by('t.team_id', 'asc')
            ->get()
            ->row_array();

        if (!empty($team_head)) {
            return $team_head;
        }

        return $this->db->select('user_id, title, first_name, last_name, email')
            ->from('system_users')
            ->where('department_id', $department_id)
            ->where('business_location', 2)
            ->where('user_status', 1)
            ->order_by('first_name', 'asc')
            ->order_by('last_name', 'asc')
            ->get()
            ->row_array();
    }

    public function get_change_request($change_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $change = $this->db->select('c.*, d.df_no, d.df_description, creator.title AS creator_title, creator.first_name AS creator_first_name, creator.last_name AS creator_last_name, creator.email AS creator_email, rd.department AS requestor_department_name')
            ->from('df_change_control c')
            ->join('df_release d', 'd.id = c.df_id', 'left')
            ->join('system_users creator', 'creator.user_id = c.created_by', 'left')
            ->join('departments rd', 'rd.department_id = c.requested_from_department_id', 'left')
            ->where('c.id', (int)$change_id)
            ->get()
            ->row_array();

        if (!empty($change)) {
            $change['creator_name'] = $this->format_user_name(
                isset($change['creator_title']) ? $change['creator_title'] : '',
                isset($change['creator_first_name']) ? $change['creator_first_name'] : '',
                isset($change['creator_last_name']) ? $change['creator_last_name'] : ''
            );
        }

        return $change;
    }

    public function get_department_action($action_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $action = $this->db->select('a.*, c.change_no, c.request_type, c.change_category, c.title AS change_title, c.df_id, c.created_by, c.status AS change_status, d.department, df.df_no, df.df_description, head.title AS head_title, head.first_name AS head_first_name, head.last_name AS head_last_name, assignee.title AS assignee_title, assignee.first_name AS assignee_first_name, assignee.last_name AS assignee_last_name')
            ->from('df_change_control_departments a')
            ->join('df_change_control c', 'c.id = a.change_id', 'left')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->join('df_release df', 'df.id = c.df_id', 'left')
            ->join('system_users head', 'head.user_id = a.department_head_id', 'left')
            ->join('system_users assignee', 'assignee.user_id = a.assigned_user_id', 'left')
            ->where('a.id', (int)$action_id)
            ->get()
            ->row_array();

        if (!empty($action)) {
            $action['head_name'] = $this->format_user_name(
                isset($action['head_title']) ? $action['head_title'] : '',
                isset($action['head_first_name']) ? $action['head_first_name'] : '',
                isset($action['head_last_name']) ? $action['head_last_name'] : ''
            );
            $action['assignee_name'] = $this->format_user_name(
                isset($action['assignee_title']) ? $action['assignee_title'] : '',
                isset($action['assignee_first_name']) ? $action['assignee_first_name'] : '',
                isset($action['assignee_last_name']) ? $action['assignee_last_name'] : ''
            );
        }

        return $action;
    }

    public function get_change_request_departments($change_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('a.*, d.department, d.color, head.title AS head_title, head.first_name AS head_first_name, head.last_name AS head_last_name, assignee.title AS assignee_title, assignee.first_name AS assignee_first_name, assignee.last_name AS assignee_last_name')
            ->from('df_change_control_departments a')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->join('system_users head', 'head.user_id = a.department_head_id', 'left')
            ->join('system_users assignee', 'assignee.user_id = a.assigned_user_id', 'left')
            ->where('a.change_id', (int)$change_id)
            ->order_by('d.sort_order_for_customize', 'asc')
            ->order_by('d.department', 'asc')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['head_name'] = $this->format_user_name(
                isset($row['head_title']) ? $row['head_title'] : '',
                isset($row['head_first_name']) ? $row['head_first_name'] : '',
                isset($row['head_last_name']) ? $row['head_last_name'] : ''
            );
            $row['assignee_name'] = $this->format_user_name(
                isset($row['assignee_title']) ? $row['assignee_title'] : '',
                isset($row['assignee_first_name']) ? $row['assignee_first_name'] : '',
                isset($row['assignee_last_name']) ? $row['assignee_last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    public function get_change_request_history($change_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('h.*, u.title, u.first_name, u.last_name, d.department')
            ->from('df_change_control_history h')
            ->join('system_users u', 'u.user_id = h.action_by', 'left')
            ->join('df_change_control_departments a', 'a.id = h.department_action_id', 'left')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->where('h.change_id', (int)$change_id)
            ->order_by('h.created_on', 'desc')
            ->order_by('h.id', 'desc')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['action_by_name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    public function add_history($change_id, $department_action_id, $action_by, $action_role, $action_type, $action_note = '')
    {
        if (!$this->module_ready()) {
            return false;
        }

        return $this->db->insert('df_change_control_history', array(
            'change_id' => (int)$change_id,
            'department_action_id' => !empty($department_action_id) ? (int)$department_action_id : 0,
            'action_by' => (int)$action_by,
            'action_role' => (string)$action_role,
            'action_type' => (string)$action_type,
            'action_note' => trim((string)$action_note),
            'created_on' => date('Y-m-d H:i:s')
        ));
    }

    public function recompute_change_status($change_id)
    {
        if (!$this->module_ready()) {
            return array('previous_status' => '', 'current_status' => '');
        }

        $change_id = (int)$change_id;
        $current = $this->db->select('status')->from('df_change_control')->where('id', $change_id)->get()->row_array();
        $previous_status = !empty($current['status']) ? $current['status'] : '';

        $statuses = $this->db->select('status')
            ->from('df_change_control_departments')
            ->where('change_id', $change_id)
            ->get()
            ->result_array();

        $status_list = array_map(function ($row) {
            return isset($row['status']) ? $row['status'] : '';
        }, $statuses);

        $new_status = 'OPEN';
        if (!empty($status_list)) {
            $completed_count = 0;
            $pending_count = 0;
            foreach ($status_list as $status) {
                if ($status === 'COMPLETED') {
                    $completed_count++;
                }
                if ($status === 'PENDING_HEAD_ACTION') {
                    $pending_count++;
                }
            }

            if ($completed_count === count($status_list)) {
                $new_status = 'COMPLETED';
            } elseif ($pending_count === count($status_list)) {
                $new_status = 'OPEN';
            } else {
                $new_status = 'IN_PROGRESS';
            }
        }

        $update_data = array('status' => $new_status);
        if ($new_status === 'COMPLETED') {
            $update_data['closed_on'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $change_id);
        $this->db->update('df_change_control', $update_data);

        return array(
            'previous_status' => $previous_status,
            'current_status' => $new_status
        );
    }

    public function get_involved_user_ids($change_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $user_ids = array();
        $change = $this->db->select('created_by')
            ->from('df_change_control')
            ->where('id', (int)$change_id)
            ->get()
            ->row_array();

        if (!empty($change['created_by'])) {
            $user_ids[] = (int)$change['created_by'];
        }

        $rows = $this->db->select('department_head_id, assigned_user_id')
            ->from('df_change_control_departments')
            ->where('change_id', (int)$change_id)
            ->get()
            ->result_array();

        foreach ($rows as $row) {
            if (!empty($row['department_head_id'])) {
                $user_ids[] = (int)$row['department_head_id'];
            }
            if (!empty($row['assigned_user_id'])) {
                $user_ids[] = (int)$row['assigned_user_id'];
            }
        }

        return array_values(array_unique(array_filter($user_ids)));
    }

    public function get_recent_changes($limit = 200)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select("c.*, d.df_no, d.df_description, creator.title AS creator_title, creator.first_name AS creator_first_name, creator.last_name AS creator_last_name, COUNT(a.id) AS department_count, SUM(CASE WHEN a.status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed_department_count, SUM(CASE WHEN a.status = 'PENDING_HEAD_ACTION' THEN 1 ELSE 0 END) AS pending_department_count, SUM(CASE WHEN a.status IN ('ASSIGNED','IN_PROGRESS') THEN 1 ELSE 0 END) AS active_department_count, SUM(CASE WHEN a.status IN ('ASSIGNED','IN_PROGRESS') AND a.target_date <> '0000-00-00' AND a.target_date < CURDATE() THEN 1 ELSE 0 END) AS overdue_department_count, GROUP_CONCAT(DISTINCT dept.department ORDER BY dept.department SEPARATOR ', ') AS departments_list", false)
            ->from('df_change_control c')
            ->join('df_release d', 'd.id = c.df_id', 'left')
            ->join('system_users creator', 'creator.user_id = c.created_by', 'left')
            ->join('df_change_control_departments a', 'a.change_id = c.id', 'left')
            ->join('departments dept', 'dept.department_id = a.department_id', 'left')
            ->group_by('c.id')
            ->order_by('c.created_on', 'desc')
            ->limit((int)$limit)
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['creator_name'] = $this->format_user_name(
                isset($row['creator_title']) ? $row['creator_title'] : '',
                isset($row['creator_first_name']) ? $row['creator_first_name'] : '',
                isset($row['creator_last_name']) ? $row['creator_last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    public function get_head_queue($user_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('a.*, c.change_no, c.request_type, c.change_category, c.title AS change_title, c.priority, c.source_of_change, c.created_by, c.created_on AS change_created_on, df.df_no, df.df_description, d.department, creator.title AS creator_title, creator.first_name AS creator_first_name, creator.last_name AS creator_last_name')
            ->from('df_change_control_departments a')
            ->join('df_change_control c', 'c.id = a.change_id', 'left')
            ->join('df_release df', 'df.id = c.df_id', 'left')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->join('system_users creator', 'creator.user_id = c.created_by', 'left')
            ->where('a.department_head_id', (int)$user_id)
            ->where('a.status', 'PENDING_HEAD_ACTION')
            ->order_by('c.created_on', 'desc')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['creator_name'] = $this->format_user_name(
                isset($row['creator_title']) ? $row['creator_title'] : '',
                isset($row['creator_first_name']) ? $row['creator_first_name'] : '',
                isset($row['creator_last_name']) ? $row['creator_last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    public function get_assigned_queue($user_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('a.*, c.change_no, c.request_type, c.change_category, c.title AS change_title, c.priority, c.source_of_change, c.created_by, df.df_no, df.df_description, d.department, creator.title AS creator_title, creator.first_name AS creator_first_name, creator.last_name AS creator_last_name')
            ->from('df_change_control_departments a')
            ->join('df_change_control c', 'c.id = a.change_id', 'left')
            ->join('df_release df', 'df.id = c.df_id', 'left')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->join('system_users creator', 'creator.user_id = c.created_by', 'left')
            ->where('a.assigned_user_id', (int)$user_id)
            ->where_in('a.status', array('ASSIGNED', 'IN_PROGRESS'))
            ->order_by('a.target_date', 'asc')
            ->order_by('c.created_on', 'desc')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['creator_name'] = $this->format_user_name(
                isset($row['creator_title']) ? $row['creator_title'] : '',
                isset($row['creator_first_name']) ? $row['creator_first_name'] : '',
                isset($row['creator_last_name']) ? $row['creator_last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    private function get_dashboard_action_summary_subquery()
    {
        return "(SELECT change_id, COUNT(id) AS department_count, SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed_department_count, SUM(CASE WHEN status = 'PENDING_HEAD_ACTION' THEN 1 ELSE 0 END) AS pending_department_count, SUM(CASE WHEN status IN ('ASSIGNED','IN_PROGRESS') THEN 1 ELSE 0 END) AS active_department_count FROM df_change_control_departments GROUP BY change_id) change_summary";
    }

    private function enrich_dashboard_action_rows($rows)
    {
        foreach ($rows as &$row) {
            $row['creator_name'] = $this->format_user_name(
                isset($row['creator_title']) ? $row['creator_title'] : '',
                isset($row['creator_first_name']) ? $row['creator_first_name'] : '',
                isset($row['creator_last_name']) ? $row['creator_last_name'] : ''
            );
            $row['head_name'] = $this->format_user_name(
                isset($row['head_title']) ? $row['head_title'] : '',
                isset($row['head_first_name']) ? $row['head_first_name'] : '',
                isset($row['head_last_name']) ? $row['head_last_name'] : ''
            );
            $row['assignee_name'] = $this->format_user_name(
                isset($row['assignee_title']) ? $row['assignee_title'] : '',
                isset($row['assignee_first_name']) ? $row['assignee_first_name'] : '',
                isset($row['assignee_last_name']) ? $row['assignee_last_name'] : ''
            );
            $row['department_count'] = isset($row['department_count']) ? (int)$row['department_count'] : 0;
            $row['completed_department_count'] = isset($row['completed_department_count']) ? (int)$row['completed_department_count'] : 0;
            $row['pending_department_count'] = isset($row['pending_department_count']) ? (int)$row['pending_department_count'] : 0;
            $row['active_department_count'] = isset($row['active_department_count']) ? (int)$row['active_department_count'] : 0;
        }
        unset($row);

        return $rows;
    }

    private function apply_dashboard_action_scope($user_id, $admin_user_type, $department_ids = array())
    {
        $user_id = (int)$user_id;
        $admin_user_type = (int)$admin_user_type;
        $department_ids = array_values(array_unique(array_filter(array_map('intval', (array)$department_ids))));

        if ($admin_user_type === 1) {
            return;
        }

        if ($admin_user_type === 2) {
            if (!empty($department_ids)) {
                $this->db->group_start();
                $this->db->where_in('a.department_id', $department_ids);
                $this->db->or_where('a.assigned_user_id', $user_id);
                $this->db->group_end();
            } else {
                $this->db->where('a.assigned_user_id', $user_id);
            }

            return;
        }

        $this->db->where('a.assigned_user_id', $user_id);
    }

    public function get_dashboard_assigned_actions($user_id, $admin_user_type = 3, $department_ids = array())
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('a.*, c.change_no, c.request_type, c.change_category, c.title AS change_title, c.priority, c.source_of_change, c.created_by, c.created_on AS change_created_on, df.df_no, df.df_description, df.df_upload, df.added_on AS df_added_on, d.department, head.title AS head_title, head.first_name AS head_first_name, head.last_name AS head_last_name, assignee.title AS assignee_title, assignee.first_name AS assignee_first_name, assignee.last_name AS assignee_last_name, creator.title AS creator_title, creator.first_name AS creator_first_name, creator.last_name AS creator_last_name, change_summary.department_count, change_summary.completed_department_count, change_summary.pending_department_count, change_summary.active_department_count')
            ->from('df_change_control_departments a')
            ->join('df_change_control c', 'c.id = a.change_id', 'left')
            ->join('df_release df', 'df.id = c.df_id', 'left')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->join('system_users head', 'head.user_id = a.department_head_id', 'left')
            ->join('system_users assignee', 'assignee.user_id = a.assigned_user_id', 'left')
            ->join('system_users creator', 'creator.user_id = c.created_by', 'left')
            ->join($this->get_dashboard_action_summary_subquery(), 'change_summary.change_id = c.id', 'left', false)
            ->where_in('a.status', array('ASSIGNED', 'IN_PROGRESS'));

        $this->apply_dashboard_action_scope($user_id, $admin_user_type, $department_ids);

        $rows = $this->db
            ->order_by('CASE WHEN a.target_date <> "0000-00-00" AND a.target_date < CURDATE() THEN 0 ELSE 1 END', '', false)
            ->order_by('a.target_date', 'asc')
            ->order_by('c.created_on', 'desc')
            ->get()
            ->result_array();

        return $this->enrich_dashboard_action_rows($rows);
    }

    public function count_dashboard_assigned_actions($user_id, $admin_user_type = 3, $department_ids = array())
    {
        if (!$this->module_ready()) {
            return 0;
        }

        $this->db->from('df_change_control_departments a')
            ->where_in('a.status', array('ASSIGNED', 'IN_PROGRESS'));

        $this->apply_dashboard_action_scope($user_id, $admin_user_type, $department_ids);

        return (int)$this->db->count_all_results();
    }

    public function get_dashboard_requester_actions($user_id, $statuses = array('ASSIGNED', 'IN_PROGRESS'))
    {
        if (!$this->module_ready()) {
            return array();
        }

        $user_id = (int)$user_id;
        $statuses = array_values(array_unique(array_filter((array)$statuses)));
        if ($user_id <= 0 || empty($statuses)) {
            return array();
        }

        $rows = $this->db->select('a.*, c.change_no, c.request_type, c.change_category, c.title AS change_title, c.priority, c.source_of_change, c.created_by, c.created_on AS change_created_on, df.df_no, df.df_description, df.df_upload, df.added_on AS df_added_on, d.department, head.title AS head_title, head.first_name AS head_first_name, head.last_name AS head_last_name, assignee.title AS assignee_title, assignee.first_name AS assignee_first_name, assignee.last_name AS assignee_last_name, creator.title AS creator_title, creator.first_name AS creator_first_name, creator.last_name AS creator_last_name, change_summary.department_count, change_summary.completed_department_count, change_summary.pending_department_count, change_summary.active_department_count')
            ->from('df_change_control_departments a')
            ->join('df_change_control c', 'c.id = a.change_id', 'left')
            ->join('df_release df', 'df.id = c.df_id', 'left')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->join('system_users head', 'head.user_id = a.department_head_id', 'left')
            ->join('system_users assignee', 'assignee.user_id = a.assigned_user_id', 'left')
            ->join('system_users creator', 'creator.user_id = c.created_by', 'left')
            ->join($this->get_dashboard_action_summary_subquery(), 'change_summary.change_id = c.id', 'left', false)
            ->where('c.created_by', $user_id)
            ->where('a.assigned_user_id >', 0)
            ->where_in('a.status', $statuses)
            ->order_by('CASE WHEN a.target_date <> "0000-00-00" AND a.target_date < CURDATE() THEN 0 ELSE 1 END', '', false)
            ->order_by('a.target_date', 'asc')
            ->order_by('c.created_on', 'desc')
            ->get()
            ->result_array();

        return $this->enrich_dashboard_action_rows($rows);
    }

    public function get_my_requests($user_id, $limit = 100)
    {
        if (!$this->module_ready()) {
            return array();
        }

        return $this->db->select('c.*, d.df_no, d.df_description')
            ->from('df_change_control c')
            ->join('df_release d', 'd.id = c.df_id', 'left')
            ->where('c.created_by', (int)$user_id)
            ->order_by('c.created_on', 'desc')
            ->limit((int)$limit)
            ->get()
            ->result_array();
    }

    public function get_dashboard_stats($user_id)
    {
        if (!$this->module_ready()) {
            return array(
                'total_requests' => 0,
                'active_requests' => 0,
                'completed_requests' => 0,
                'pending_head_actions' => 0,
                'delayed_actions' => 0,
                'my_head_queue' => 0,
                'my_execution_queue' => 0
            );
        }

        $all = $this->db->select('COUNT(*) AS total_requests, SUM(CASE WHEN status <> "COMPLETED" THEN 1 ELSE 0 END) AS active_requests, SUM(CASE WHEN status = "COMPLETED" THEN 1 ELSE 0 END) AS completed_requests', false)
            ->from('df_change_control')
            ->get()
            ->row_array();

        $pending = $this->db->select('COUNT(*) AS pending_head_actions', false)
            ->from('df_change_control_departments')
            ->where('status', 'PENDING_HEAD_ACTION')
            ->get()
            ->row_array();

        $delayed = $this->db->select('COUNT(*) AS delayed_actions', false)
            ->from('df_change_control_departments')
            ->where_in('status', array('ASSIGNED', 'IN_PROGRESS'))
            ->where('target_date <>', '0000-00-00')
            ->where('target_date <', date('Y-m-d'))
            ->get()
            ->row_array();

        return array(
            'total_requests' => !empty($all['total_requests']) ? (int)$all['total_requests'] : 0,
            'active_requests' => !empty($all['active_requests']) ? (int)$all['active_requests'] : 0,
            'completed_requests' => !empty($all['completed_requests']) ? (int)$all['completed_requests'] : 0,
            'pending_head_actions' => !empty($pending['pending_head_actions']) ? (int)$pending['pending_head_actions'] : 0,
            'delayed_actions' => !empty($delayed['delayed_actions']) ? (int)$delayed['delayed_actions'] : 0,
            'my_head_queue' => count($this->get_head_queue($user_id)),
            'my_execution_queue' => count($this->get_assigned_queue($user_id))
        );
    }

    public function get_department_load_snapshot()
    {
        if (!$this->module_ready()) {
            return array();
        }

        return $this->db->select("d.department, COUNT(a.id) AS total_actions, SUM(CASE WHEN a.status = 'PENDING_HEAD_ACTION' THEN 1 ELSE 0 END) AS pending_head_actions, SUM(CASE WHEN a.status IN ('ASSIGNED','IN_PROGRESS') THEN 1 ELSE 0 END) AS execution_actions, SUM(CASE WHEN a.status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed_actions", false)
            ->from('df_change_control_departments a')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->group_by('a.department_id')
            ->order_by('execution_actions', 'desc')
            ->order_by('pending_head_actions', 'desc')
            ->get()
            ->result_array();
    }

    public function get_user_alert_count($user_id)
    {
        if (!$this->module_ready()) {
            return 0;
        }

        $result = $this->db->select("SUM(CASE WHEN department_head_id = " . (int)$user_id . " AND status = 'PENDING_HEAD_ACTION' THEN 1 ELSE 0 END) + SUM(CASE WHEN assigned_user_id = " . (int)$user_id . " AND status IN ('ASSIGNED','IN_PROGRESS') THEN 1 ELSE 0 END) AS total_alerts", false)
            ->from('df_change_control_departments')
            ->get()
            ->row_array();

        return !empty($result['total_alerts']) ? (int)$result['total_alerts'] : 0;
    }

    public function is_department_head($user_id)
    {
        $user_id = (int)$user_id;
        if ($user_id <= 0) {
            return false;
        }

        $department_head = $this->db->select('department_id')
            ->from('departments')
            ->where('business_loc_id', 2)
            ->where('status', 1)
            ->where('departmenthead', $user_id)
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        if ($department_head) {
            return true;
        }

        return $this->db->select('team_id')
            ->from('prestogroup_teams')
            ->where('status', 1)
            ->where('team_leader', $user_id)
            ->limit(1)
            ->get()
            ->num_rows() > 0;
    }

    public function get_gantt_change_summary($df_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select("c.id, c.change_no, c.request_type, c.change_category, c.title, c.priority, c.status, c.created_on, SUM(CASE WHEN a.status IN ('ASSIGNED','IN_PROGRESS') THEN 1 ELSE 0 END) AS active_departments, COUNT(a.id) AS total_departments", false)
            ->from('df_change_control c')
            ->join('df_change_control_departments a', 'a.change_id = c.id', 'left')
            ->where('c.df_id', (int)$df_id)
            ->group_by('c.id')
            ->order_by('c.created_on', 'desc')
            ->get()
            ->result_array();

        return $rows;
    }

    public function get_gantt_department_map($df_id)
    {
        $map = array();

        if (!$this->module_ready()) {
            return $map;
        }

        $rows = $this->db->select('a.department_id, c.id AS change_id, c.change_no, c.request_type, c.change_category, c.title, c.status AS change_status, a.status AS department_status')
            ->from('df_change_control_departments a')
            ->join('df_change_control c', 'c.id = a.change_id', 'left')
            ->where('c.df_id', (int)$df_id)
            ->order_by('c.created_on', 'desc')
            ->get()
            ->result_array();

        foreach ($rows as $row) {
            $department_id = (int)$row['department_id'];

            if (!isset($map[$department_id])) {
                $map[$department_id] = array(
                    'has_requests' => false,
                    'has_active_requests' => false,
                    'items' => array()
                );
            }

            $map[$department_id]['has_requests'] = true;
            if ($row['department_status'] !== 'COMPLETED') {
                $map[$department_id]['has_active_requests'] = true;
            }

            $map[$department_id]['items'][] = array(
                'change_id' => (int)$row['change_id'],
                'change_no' => $row['change_no'],
                'request_type' => $row['request_type'],
                'change_category' => $row['change_category'],
                'title' => $row['title'],
                'change_status' => $row['change_status'],
                'department_status' => $row['department_status']
            );
        }

        return $map;
    }
}
