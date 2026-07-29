<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_completion_control_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function module_ready()
    {
        return $this->db->table_exists('task_completion_control_history');
    }

    public function format_user_name($title = '', $first_name = '', $last_name = '')
    {
        $parts = array();
        foreach (array($title, $first_name, $last_name) as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $parts[] = $value;
            }
        }

        if (empty($parts)) {
            return '';
        }

        return ucwords(strtolower(implode(' ', $parts)));
    }

    public function get_df_options()
    {
        $this->db->select('DISTINCT d.id, d.df_no, d.df_description', false)
            ->from('task_department_wise_scheduling t')
            ->join('df_release d', 'd.id = t.df_id', 'left')
            ->where('t.task_status', 1)
            ->where('d.on_hold', 0);
        $this->apply_running_df_status_filter('d');
        $this->db->order_by('d.id', 'desc');

        return $this->db->get()->result_array();
    }

    public function get_department_options()
    {
        $this->db->select('DISTINCT d.department_id, d.department', false);
        $this->db->from('task_department_wise_scheduling t');
        $this->db->join('df_release df', 'df.id = t.df_id', 'left');
        $this->db->join('departments d', 'd.department_id = t.department_id', 'left');
        $this->db->where('t.task_status', 1);
        $this->db->where('df.on_hold', 0);
        $this->db->where('d.status', 1);
        $this->db->where('d.business_loc_id', 2);
        $this->apply_running_df_status_filter('df');
        $this->db->order_by('d.department', 'asc');

        return $this->db->get()->result_array();
    }

    public function get_user_options($department_id = 0)
    {
        $this->db->select('DISTINCT u.user_id, u.title, u.first_name, u.last_name', false);
        $this->db->from('task_department_wise_scheduling t');
        $this->db->join('system_users u', 'u.user_id = t.assigned_user', 'left');
        $this->db->join('df_release d', 'd.id = t.df_id', 'left');
        $this->db->where('t.task_status', 1);
        $this->db->where('d.on_hold', 0);
        $this->apply_running_df_status_filter('d');
        if ((int) $department_id > 0) {
            $this->db->where('t.department_id', (int) $department_id);
        }
        $this->db->order_by('u.first_name', 'asc');
        $this->db->order_by('u.last_name', 'asc');

        return $this->db->get()->result_array();
    }

    public function get_completed_tasks($filters = array())
    {
        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 250;
        if ($limit <= 0) {
            $limit = 250;
        }

        $this->db->select('
            a.id,
            a.df_id,
            a.taskid,
            a.department_id,
            a.assigned_user,
            a.end_date,
            a.task_completed_on,
            a.task_completed_by,
            a.remarks,
            a.changedBy,
            a.changedOn,
            b.task_name,
            c.df_no,
            c.df_description,
            c.df_status,
            d.department,
            owner.title AS owner_title,
            owner.first_name AS owner_first_name,
            owner.last_name AS owner_last_name,
            completer.title AS completer_title,
            completer.first_name AS completer_first_name,
            completer.last_name AS completer_last_name,
            changer.title AS changer_title,
            changer.first_name AS changer_first_name,
            changer.last_name AS changer_last_name
        ');
        $this->db->from('task_department_wise_scheduling a');
        $this->db->join('task_management b', 'b.task_id = a.taskid', 'left');
        $this->db->join('df_release c', 'c.id = a.df_id', 'left');
        $this->db->join('departments d', 'd.department_id = a.department_id', 'left');
        $this->db->join('system_users owner', 'owner.user_id = a.assigned_user', 'left');
        $this->db->join('system_users completer', 'completer.user_id = a.task_completed_by', 'left');
        $this->db->join('system_users changer', 'changer.user_id = a.changedBy', 'left');
        $this->apply_completed_task_filters($filters);
        $this->db->order_by('a.task_completed_on', 'desc');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    public function get_task_record($task_record_id)
    {
        $task = $this->db->select('
                a.*,
                b.task_name,
                b.isitfinalstep,
                b.df_meeting_close,
                c.df_no,
                c.df_description,
                c.df_status,
                d.department,
                owner.title AS owner_title,
                owner.first_name AS owner_first_name,
                owner.last_name AS owner_last_name,
                completer.title AS completer_title,
                completer.first_name AS completer_first_name,
                completer.last_name AS completer_last_name,
                changer.title AS changer_title,
                changer.first_name AS changer_first_name,
                changer.last_name AS changer_last_name
            ')
            ->from('task_department_wise_scheduling a')
            ->join('task_management b', 'b.task_id = a.taskid', 'left')
            ->join('df_release c', 'c.id = a.df_id', 'left')
            ->join('departments d', 'd.department_id = a.department_id', 'left')
            ->join('system_users owner', 'owner.user_id = a.assigned_user', 'left')
            ->join('system_users completer', 'completer.user_id = a.task_completed_by', 'left')
            ->join('system_users changer', 'changer.user_id = a.changedBy', 'left')
            ->where('a.id', (int) $task_record_id)
            ->get()
            ->row_array();

        return !empty($task) ? $task : array();
    }

    public function get_task_history($task_record_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db->select('
                h.*,
                u.title,
                u.first_name,
                u.last_name
            ')
            ->from('task_completion_control_history h')
            ->join('system_users u', 'u.user_id = h.changed_by', 'left')
            ->where('h.task_record_id', (int) $task_record_id)
            ->order_by('h.changed_on', 'desc')
            ->order_by('h.id', 'desc')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['changed_by_name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
        }
        unset($row);

        return $rows;
    }

    public function get_history_count($action_type = '')
    {
        if (!$this->module_ready()) {
            return 0;
        }

        $this->db->select('COUNT(id) AS total', false);
        $this->db->from('task_completion_control_history');
        if (trim((string) $action_type) !== '') {
            $this->db->where('action_type', trim((string) $action_type));
        }

        $row = $this->db->get()->row_array();
        return !empty($row['total']) ? (int) $row['total'] : 0;
    }

    public function update_completed_task($task_record_id, $changed_by, $payload = array())
    {
        if (!$this->module_ready()) {
            return array(
                'success' => false,
                'message' => 'Task completion control history table is not ready. Please run the migration first.'
            );
        }

        $task = $this->get_task_for_mutation($task_record_id, array(1));
        if (empty($task)) {
            return array(
                'success' => false,
                'message' => 'Completed task record not found.'
            );
        }

        $date_value = trim((string) (isset($payload['task_completed_on']) ? $payload['task_completed_on'] : ''));
        if (!$this->is_valid_date($date_value)) {
            return array(
                'success' => false,
                'message' => 'Please select a valid completion date.'
            );
        }

        $remarks = trim((string) (isset($payload['remarks']) ? $payload['remarks'] : ''));
        $change_note = trim((string) (isset($payload['change_note']) ? $payload['change_note'] : ''));
        $now = date('Y-m-d H:i:s');
        $new_completed_on = $this->merge_date_with_existing_time($date_value, $task['task_completed_on']);

        $update_data = array(
            'changedBy' => (int) $changed_by,
            'changedOn' => $now,
            'taskupdatedontime' => $now
        );

        $updated_fields = array();
        if ((string) $task['task_completed_on'] !== $new_completed_on) {
            $update_data['task_completed_on'] = $new_completed_on;
            $update_data['end_date_changed'] = 1;
            $updated_fields[] = 'completion date';
        }

        if ((string) $task['remarks'] !== $remarks) {
            $update_data['remarks'] = $remarks;
            $updated_fields[] = 'remarks';
        }

        if (empty($updated_fields)) {
            return array(
                'success' => true,
                'changed' => false,
                'message' => 'No changes were detected in the completed task.'
            );
        }

        $before = $this->build_history_snapshot($task);
        $after = $this->build_history_snapshot($task, $update_data);

        $this->db->trans_start();
        $this->db->where('id', (int) $task_record_id);
        $this->db->update('task_department_wise_scheduling', $update_data);
        $this->insert_history_row($task, $before, $after, $changed_by, 'UPDATED_COMPLETED_TASK', $change_note);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array(
                'success' => false,
                'message' => 'Unable to update the completed task right now. Please try again.'
            );
        }

        return array(
            'success' => true,
            'changed' => true,
            'message' => 'Completed task updated successfully.'
        );
    }

    public function update_completion_date_quickly($task_record_id, $changed_by, $date_value, $change_note = 'Completion date adjusted from completed task list.')
    {
        if (!$this->module_ready()) {
            return array(
                'success' => false,
                'message' => 'Task completion control history table is not ready. Please run the migration first.'
            );
        }

        $task = $this->get_task_for_mutation($task_record_id, array(1, 2));
        if (empty($task)) {
            return array(
                'success' => false,
                'message' => 'Task record was not found for date adjustment.'
            );
        }

        $date_value = trim((string) $date_value);
        if (!$this->is_valid_date($date_value)) {
            return array(
                'success' => false,
                'message' => 'Please select a valid completion date.'
            );
        }

        $new_completed_on = $this->merge_date_with_existing_time($date_value, $task['task_completed_on']);
        if ((string) $task['task_completed_on'] === $new_completed_on) {
            return array(
                'success' => true,
                'changed' => false,
                'message' => 'Completion date is already up to date.'
            );
        }

        $now = date('Y-m-d H:i:s');
        $update_data = array(
            'task_completed_on' => $new_completed_on,
            'end_date_changed' => 1,
            'changedBy' => (int) $changed_by,
            'changedOn' => $now,
            'taskupdatedontime' => $now
        );

        $before = $this->build_history_snapshot($task);
        $after = $this->build_history_snapshot($task, $update_data);

        $this->db->trans_start();
        $this->db->where('id', (int) $task_record_id);
        $this->db->update('task_department_wise_scheduling', $update_data);
        $this->insert_history_row($task, $before, $after, $changed_by, 'UPDATED_COMPLETION_DATE', $change_note);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array(
                'success' => false,
                'message' => 'Unable to update the completion date right now. Please try again.'
            );
        }

        return array(
            'success' => true,
            'changed' => true,
            'message' => 'Completion date updated successfully.'
        );
    }

    public function rollback_completed_task($task_record_id, $changed_by, $rollback_note)
    {
        if (!$this->module_ready()) {
            return array(
                'success' => false,
                'message' => 'Task completion control history table is not ready. Please run the migration first.'
            );
        }

        $task = $this->get_task_for_mutation($task_record_id, array(1));
        if (empty($task)) {
            return array(
                'success' => false,
                'message' => 'Completed task record not found for rollback.'
            );
        }

        $rollback_note = trim((string) $rollback_note);
        if ($rollback_note === '') {
            return array(
                'success' => false,
                'message' => 'Rollback reason is required before changing task status.'
            );
        }

        $now = date('Y-m-d H:i:s');
        $actor_name = $this->get_user_name_by_id($changed_by);
        $updated_remarks = $this->append_rollback_note((string) $task['remarks'], $rollback_note, $actor_name, $now);
        $update_data = array(
            'task_status' => 0,
            'task_completed_on' => '0000-00-00 00:00:00',
            'task_completed_by' => 0,
            'remarks' => $updated_remarks,
            'changedBy' => (int) $changed_by,
            'changedOn' => $now,
            'taskupdatedontime' => $now
        );

        $before = $this->build_history_snapshot($task);
        $new_df_status = (int) $task['df_status'];

        $this->db->trans_start();

        $this->db->where('id', (int) $task_record_id);
        $this->db->update('task_department_wise_scheduling', $update_data);

        if ((int) $task['df_meeting_close'] === 1) {
            $reopen_data = array(
                'task_status' => 0,
                'task_completed_on' => '0000-00-00 00:00:00',
                'task_completed_by' => 0,
                'changedBy' => (int) $changed_by,
                'changedOn' => $now,
                'taskupdatedontime' => $now
            );

            $this->db->where('df_id', (int) $task['df_id']);
            $this->db->where('taskid', 4);
            $this->db->where('id !=', (int) $task_record_id);
            $this->db->where('task_status', 1);
            $this->db->update('task_department_wise_scheduling', $reopen_data);
        }

        if ((int) $task['isitfinalstep'] === 1 && (int) $task['df_id'] > 0) {
            $new_df_status = 0;
            $this->db->where('id', (int) $task['df_id']);
            $this->db->update('df_release', array(
                'df_status' => 0,
                'completed_on' => '0000-00-00 00:00:00',
                'completed_by' => 0
            ));
        }

        $after = $this->build_history_snapshot($task, array(
            'task_status' => 0,
            'task_completed_on' => '0000-00-00 00:00:00',
            'task_completed_by' => 0,
            'remarks' => $updated_remarks,
            'df_status' => $new_df_status
        ));

        $this->insert_history_row($task, $before, $after, $changed_by, 'ROLLED_BACK_TO_PENDING', $rollback_note);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array(
                'success' => false,
                'message' => 'Unable to roll back the completed task right now. Please try again.'
            );
        }

        return array(
            'success' => true,
            'message' => 'Completed task rolled back to pending successfully.'
        );
    }

    private function apply_completed_task_filters($filters = array())
    {
        $this->db->where('a.task_status', 1);
        $this->db->where('c.on_hold', 0);
        $this->apply_running_df_status_filter('c');

        if (!empty($filters['df_id'])) {
            $this->db->where('a.df_id', (int) $filters['df_id']);
        }

        if (!empty($filters['department_id'])) {
            $this->db->where('a.department_id', (int) $filters['department_id']);
        }

        if (!empty($filters['assigned_user'])) {
            $this->db->where('a.assigned_user', (int) $filters['assigned_user']);
        }

        if (!empty($filters['from_date']) && $this->is_valid_date($filters['from_date'])) {
            $this->db->where("DATE(a.task_completed_on) >= '" . $this->db->escape_str($filters['from_date']) . "'", null, false);
        }

        if (!empty($filters['to_date']) && $this->is_valid_date($filters['to_date'])) {
            $this->db->where("DATE(a.task_completed_on) <= '" . $this->db->escape_str($filters['to_date']) . "'", null, false);
        }

        $keyword = trim((string) (isset($filters['keyword']) ? $filters['keyword'] : ''));
        if ($keyword !== '') {
            $this->db->group_start();
            $this->db->like('c.df_no', $keyword);
            $this->db->or_like('c.df_description', $keyword);
            $this->db->or_like('b.task_name', $keyword);
            $this->db->or_like('d.department', $keyword);
            $this->db->or_like('a.remarks', $keyword);
            $this->db->group_end();
        }
    }

    private function apply_running_df_status_filter($alias)
    {
        $alias = trim((string) $alias);
        if ($alias === '') {
            return;
        }

        $this->db->group_start();
        $this->db->where($alias . '.df_status', 0);
        $this->db->or_where($alias . '.df_status', 'running');
        $this->db->or_where($alias . '.df_status IS NULL', null, false);
        $this->db->group_end();
    }

    private function get_task_for_mutation($task_record_id, $status_filter = array())
    {
        $this->db->select('
            a.id,
            a.df_id,
            a.taskid,
            a.task_status,
            a.task_completed_on,
            a.task_completed_by,
            a.remarks,
            c.df_status,
            b.isitfinalstep,
            b.df_meeting_close
        ');
        $this->db->from('task_department_wise_scheduling a');
        $this->db->join('task_management b', 'b.task_id = a.taskid', 'left');
        $this->db->join('df_release c', 'c.id = a.df_id', 'left');
        $this->db->where('a.id', (int) $task_record_id);
        if (!empty($status_filter)) {
            $this->db->where_in('a.task_status', array_map('intval', $status_filter));
        }

        $row = $this->db->get()->row_array();
        return !empty($row) ? $row : array();
    }

    private function build_history_snapshot($task_row, $override = array())
    {
        return array(
            'task_status' => array_key_exists('task_status', $override) ? (int) $override['task_status'] : (int) $task_row['task_status'],
            'task_completed_on' => array_key_exists('task_completed_on', $override) ? (string) $override['task_completed_on'] : (string) $task_row['task_completed_on'],
            'task_completed_by' => array_key_exists('task_completed_by', $override) ? (int) $override['task_completed_by'] : (int) $task_row['task_completed_by'],
            'remarks' => array_key_exists('remarks', $override) ? (string) $override['remarks'] : (string) $task_row['remarks'],
            'df_status' => array_key_exists('df_status', $override) ? (int) $override['df_status'] : (int) $task_row['df_status']
        );
    }

    private function insert_history_row($task_row, $before, $after, $changed_by, $action_type, $action_note = '')
    {
        return $this->db->insert('task_completion_control_history', array(
            'task_record_id' => (int) $task_row['id'],
            'df_id' => (int) $task_row['df_id'],
            'task_id' => (int) $task_row['taskid'],
            'action_type' => trim((string) $action_type),
            'old_task_status' => (int) $before['task_status'],
            'new_task_status' => (int) $after['task_status'],
            'old_task_completed_on' => (string) $before['task_completed_on'],
            'new_task_completed_on' => (string) $after['task_completed_on'],
            'old_task_completed_by' => (int) $before['task_completed_by'],
            'new_task_completed_by' => (int) $after['task_completed_by'],
            'old_remarks' => (string) $before['remarks'],
            'new_remarks' => (string) $after['remarks'],
            'old_df_status' => (int) $before['df_status'],
            'new_df_status' => (int) $after['df_status'],
            'action_note' => trim((string) $action_note),
            'changed_by' => (int) $changed_by,
            'changed_on' => date('Y-m-d H:i:s')
        ));
    }

    private function merge_date_with_existing_time($date_value, $existing_datetime)
    {
        $date_value = trim((string) $date_value);
        $existing_datetime = trim((string) $existing_datetime);
        $time_value = '00:00:00';

        if ($existing_datetime !== '' && $existing_datetime !== '0000-00-00 00:00:00') {
            $timestamp = strtotime($existing_datetime);
            if (!empty($timestamp)) {
                $time_value = date('H:i:s', $timestamp);
            }
        }

        return $date_value . ' ' . $time_value;
    }

    private function append_rollback_note($existing_remarks, $rollback_note, $actor_name, $changed_on)
    {
        $existing_remarks = trim((string) $existing_remarks);
        $actor_name = trim((string) $actor_name);
        if ($actor_name === '') {
            $actor_name = 'System User';
        }

        $note_line = '[Rollback on ' . date('d-M-Y h:i A', strtotime($changed_on)) . ' by ' . $actor_name . '] ' . trim((string) $rollback_note);
        if ($existing_remarks === '') {
            return $note_line;
        }

        return $existing_remarks . "\n\n" . $note_line;
    }

    private function get_user_name_by_id($user_id)
    {
        $row = $this->db->select('title, first_name, last_name')
            ->from('system_users')
            ->where('user_id', (int) $user_id)
            ->get()
            ->row_array();

        if (empty($row)) {
            return '';
        }

        return $this->format_user_name(
            isset($row['title']) ? $row['title'] : '',
            isset($row['first_name']) ? $row['first_name'] : '',
            isset($row['last_name']) ? $row['last_name'] : ''
        );
    }

    private function is_valid_date($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);
        return $date instanceof DateTime && $date->format('Y-m-d') === $value;
    }
}
