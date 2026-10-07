<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_management extends CI_Controller
{
    private $admin_user_ids = array(61, 139, 161, 162, 167);
    private $task_business_location_id = 2;

    public function __construct()
    {
        parent::__construct();

        $session = $this->session->userdata('logged_in');
        if ($session == FALSE) {
            redirect(page_url);
        }

        $user_id = $this->get_current_user_id();
        if (empty($user_id)) {
            redirect(site_url(), 'refresh');
        }

        $this->load->model('Task_management_model', 'task_module');
        $this->load->library('email');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $user_id = $this->get_current_user_id();
        $is_admin_user = $this->is_admin_user($user_id);
        $data = array(
            'module_ready' => $this->task_module->module_ready(),
            'migration_file' => 'Database/task_management_module_001.sql',
            'stats' => $this->task_module->get_dashboard_stats($user_id),
            // the same unread updates the navigation badge counts, listed in
            // full at the top of the dashboard
            'inbox' => $this->task_module->get_recent_notifications($user_id, 8),
            'unread_count' => $this->task_module->get_unread_notification_count($user_id),
            'assigned_tasks' => $this->task_module->get_assigned_tasks($user_id, 30),
            'created_tasks' => $this->task_module->get_created_tasks($user_id, 30),
            'module_nav' => $this->get_module_navigation('dashboard'),
            'current_user_id' => $user_id,
            'is_admin_user' => $is_admin_user,
            'admin_task_filters' => array(),
            'admin_filter_options' => array(),
            'all_tasks' => array(),
            'all_task_summary' => array(
                'visible' => 0,
                'open' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'overdue' => 0
            )
        );

        if ($is_admin_user && $data['module_ready']) {
            $admin_task_filters = $this->get_admin_task_filters();
            $all_tasks = $this->task_module->get_all_tasks($admin_task_filters);

            $data['admin_task_filters'] = $admin_task_filters;
            $data['all_tasks'] = $all_tasks;
            $data['all_task_summary'] = $this->build_task_summary($all_tasks);
            $data['admin_filter_options'] = array(
                'users' => $this->task_module->get_assignable_users(),
                'departments' => $this->task_module->get_department_filters(),
                'statuses' => $this->task_module->get_status_filters(),
                'priorities' => $this->task_module->get_priority_filters(),
                'due_states' => $this->task_module->get_due_state_filters()
            );
        }

        $this->load->view('task_management/dashboard', $data);
    }

    public function rnd_design_dashboard()
    {
        $user_id = $this->get_current_user_id();
        $has_access = $this->db->select('acessid')
            ->from('module_capablity')
            ->where('role_id', $user_id)
            ->where('moduleid', '20')
            ->where('submoduleid', '82')
            ->where('submodule_access', '1')
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        if (!$has_access) {
            show_error('You do not have permission to view the R&D - Design Dashboard.', 403, 'Access Denied');
            return;
        }

        $module_ready = $this->task_module->module_ready();
        $data = array(
            'module_ready' => $module_ready,
            'department_id' => 37,
            'department' => $this->task_module->get_department(37),
            'tasks' => $module_ready ? $this->task_module->get_all_tasks(array('department_id' => 37)) : array()
        );

        $this->load->view('task_management/rnd_design_dashboard', $data);
    }

    public function create()
    {
        $data = array(
            'module_ready' => $this->task_module->module_ready(),
            'migration_file' => 'Database/task_management_module_001.sql',
            'assignable_users' => $this->task_module->get_assignable_users(),
            'current_user' => $this->task_module->get_user($this->get_current_user_id()),
            'module_nav' => $this->get_module_navigation('create'),
            'task_company_name' => $this->task_module->get_business_location_name($this->task_business_location_id)
        );

        $this->load->view('task_management/create', $data);
    }

    public function save()
    {
        if (!$this->task_module->module_ready()) {
            $this->set_flash_message('danger', 'Task Management tables are not ready yet. Please upload and run Database/task_management_module_001.sql first.');
            redirect(page_url . 'Task_management/create');
        }

        $this->form_validation->set_error_delimiters('<div class="tm-form-error">', '</div>');
        $this->form_validation->set_rules('assigned_to_user_id[]', 'Assign To', 'required|integer');
        $this->form_validation->set_rules('title', 'Task Title', 'required|trim');
        $this->form_validation->set_rules('task_details', 'Task Details', 'required|trim');
        $this->form_validation->set_rules('priority', 'Priority', 'required|trim');
        $this->form_validation->set_rules('requested_due_date', 'Suggested Due Date', 'required|trim|callback_not_past_date');

        if ($this->form_validation->run() == FALSE) {
            $this->create();
            return;
        }

        $assigned_to_user_ids = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) $this->input->post('assigned_to_user_id')
        ))));
        if (empty($assigned_to_user_ids)) {
            $this->set_flash_message('danger', 'Please select at least one assignee.');
            redirect(page_url . 'Task_management/create');
        }

        $assignees = $this->task_module->get_users($assigned_to_user_ids);
        if (count($assignees) !== count($assigned_to_user_ids)) {
            $this->set_flash_message('danger', 'One or more selected assignees could not be found.');
            redirect(page_url . 'Task_management/create');
        }

        foreach ($assigned_to_user_ids as $assigned_to_user_id) {
            if ((int) $assignees[$assigned_to_user_id]['business_location'] !== (int) $this->task_business_location_id) {
                $this->set_flash_message('danger', 'Only Shubham Pack users can be selected in this task form.');
                redirect(page_url . 'Task_management/create');
            }
        }

        $requested_due_date = date('Y-m-d', strtotime((string) $this->input->post('requested_due_date')));
        if ($requested_due_date === '1970-01-01') {
            $this->set_flash_message('danger', 'Please select a valid suggested due date.');
            redirect(page_url . 'Task_management/create');
        }

        $attachment_error = '';
        $attachment = $this->upload_task_attachment('attachment', $attachment_error);
        $now = date('Y-m-d H:i:s');
        $current_user_id = $this->get_current_user_id();

        $base_task_data = array(
            'task_code' => '',
            'business_location_id' => (int) $this->task_business_location_id,
            'title' => trim((string) $this->input->post('title')),
            'task_details' => trim((string) $this->input->post('task_details')),
            'reference_url' => trim((string) $this->input->post('reference_url')),
            'attachment' => $attachment,
            'priority' => strtoupper(trim((string) $this->input->post('priority'))),
            'requested_due_date' => $requested_due_date,
            'committed_due_date' => null,
            'assigned_by_user_id' => $current_user_id,
            'status' => 'AWAITING_DUE_DATE',
            'progress_percent' => 0,
            'last_update_note' => 'Task created and waiting for assignee due-date confirmation.',
            'created_on' => $now,
            'updated_on' => $now
        );

        $this->db->trans_start();
        $creator = $this->task_module->get_user($current_user_id);
        $created_tasks = array();

        foreach ($assigned_to_user_ids as $assigned_to_user_id) {
            $assignee = $assignees[$assigned_to_user_id];
            $task_data = $base_task_data;
            $task_data['assigned_to_user_id'] = $assigned_to_user_id;
            $task_data['assigned_to_department_id'] = !empty($assignee['department_id']) ? (int) $assignee['department_id'] : 0;
            $is_self_assigned = (int) $assigned_to_user_id === (int) $current_user_id;
            if ($is_self_assigned) {
                $task_data['committed_due_date'] = $requested_due_date;
                $task_data['status'] = 'OPEN';
                $task_data['last_update_note'] = 'Self-assigned task created with the selected due date confirmed automatically.';
            }

            $task_id = $this->task_module->create_task($task_data);
            $task_code = $this->task_module->generate_task_code($task_id);
            $this->task_module->update_task($task_id, array('task_code' => $task_code));

            $this->task_module->add_update(array(
                'task_id' => $task_id,
                'update_type' => 'CREATED',
                'status' => $task_data['status'],
                'progress_percent' => 0,
                'due_date' => $requested_due_date,
                'update_note' => trim((string) $this->input->post('task_details')),
                'created_by' => $current_user_id,
                'created_on' => $now
            ));

            $action_url = page_url . 'Task_management/view/' . $task_id;
            $notification_message = $is_self_assigned
                ? $task_code . ' created for yourself. The selected due date is confirmed and you can start posting progress updates.'
                : $task_code . ' assigned by ' . (!empty($creator['name']) ? $creator['name'] : 'Task owner') . '. Please confirm the due date.';
            $this->task_module->add_notifications($task_id, array($assigned_to_user_id), $notification_message, $action_url);
            $created_tasks[] = array('id' => $task_id, 'code' => $task_code);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Task could not be created right now. Please try again.');
            redirect(page_url . 'Task_management/create');
        }

        foreach ($created_tasks as $created_task) {
            $task = $this->task_module->get_task($created_task['id']);
            $is_self_assigned = (int) $task['assigned_to_user_id'] === (int) $task['assigned_by_user_id'];
            $assignment_message = $is_self_assigned
                ? 'Your self-assigned task has been created. The selected due date is confirmed and you can start posting progress updates.'
                : 'A new task has been created and assigned. The assignee needs to confirm the due date before execution starts.';
            $this->send_task_email(
                $task,
                'New Task Assigned: ' . $created_task['code'],
                $assignment_message,
                trim((string) $this->input->post('task_details'))
            );
            $this->send_task_whatsapp(
                $task,
                (int) $task['assigned_to_user_id'],
                $is_self_assigned ? $assignment_message : 'A new task has been assigned to you. Please confirm the due date.',
                trim((string) $this->input->post('task_details')),
                'assigned'
            );
        }

        $task_codes = array_column($created_tasks, 'code');
        // A rejected attachment used to disappear without a word - the task
        // saved, the file did not, and nobody found out until somebody went
        // looking for it.
        $this->set_flash_message(
            $attachment_error !== '' ? 'warning' : 'success',
            count($created_tasks) . ' task' . (count($created_tasks) === 1 ? '' : 's') . ' created successfully: ' . implode(', ', $task_codes) . '.'
                . ($attachment_error !== '' ? '<br>' . $attachment_error : '')
        );
        if (count($created_tasks) === 1) {
            redirect(page_url . 'Task_management/view/' . $created_tasks[0]['id']);
        }
        redirect(page_url . 'Task_management');
    }

    public function view($task_id = 0)
    {
        if (!$this->task_module->module_ready()) {
            $this->set_flash_message('danger', 'Task Management tables are not ready yet. Please upload and run Database/task_management_module_001.sql first.');
            redirect(page_url . 'Task_management');
        }

        $task = $this->task_module->get_task((int) $task_id);
        if (empty($task)) {
            $this->set_flash_message('danger', 'Requested task could not be found.');
            redirect(page_url . 'Task_management');
        }

        if (!$this->can_view_task($this->get_current_user_id(), $task)) {
            $this->set_flash_message('danger', 'You are not allowed to view this task.');
            redirect(page_url . 'Task_management');
        }

        $current_user_id = $this->get_current_user_id();

        // Reading the task IS reading its notifications. Previously they were
        // only cleared by pressing Dismiss or Open on the toast, so anyone who
        // arrived from the dashboard or a mail link left them unread and the
        // navigation badge kept counting updates they had already seen.
        $this->task_module->mark_task_notifications_read((int) $task_id, $current_user_id);

        $data = array(
            'task' => $task,
            'updates' => $this->task_module->get_task_updates((int) $task_id),
            'module_nav' => $this->get_module_navigation('dashboard'),
            'current_user_id' => $current_user_id,
            'can_set_due_date' => ((int) $task['assigned_to_user_id'] === $current_user_id && strtoupper((string) $task['status']) === 'AWAITING_DUE_DATE'),
            'can_update_progress' => (
                (int) $task['assigned_to_user_id'] === $current_user_id
                && strtoupper((string) $task['status']) !== 'COMPLETED'
                && !empty($task['committed_due_date'])
                && $task['committed_due_date'] !== '0000-00-00'
            ),
            'can_reopen_task' => (
                (int) $task['assigned_by_user_id'] === $current_user_id
                && strtoupper((string) $task['status']) === 'COMPLETED'
            )
        );

        $this->load->view('task_management/view', $data);
    }

    public function confirm_due_date($task_id = 0)
    {
        if (!$this->task_module->module_ready()) {
            redirect(page_url . 'Task_management');
        }

        $task = $this->task_module->get_task((int) $task_id);
        if (empty($task)) {
            $this->set_flash_message('danger', 'Requested task could not be found.');
            redirect(page_url . 'Task_management');
        }

        $current_user_id = $this->get_current_user_id();
        if ((int) $task['assigned_to_user_id'] !== $current_user_id) {
            $this->set_flash_message('danger', 'Only the assigned person can confirm the due date.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        if (strtoupper((string) $task['status']) !== 'AWAITING_DUE_DATE') {
            $this->set_flash_message('danger', 'Final due date has already been confirmed for this task.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('committed_due_date', 'Final Due Date', 'required|trim|callback_not_past_date');
        $this->form_validation->set_rules('schedule_note', 'Schedule Note', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->set_flash_message('danger', validation_errors());
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $committed_due_date = date('Y-m-d', strtotime((string) $this->input->post('committed_due_date')));
        if ($committed_due_date === '1970-01-01') {
            $this->set_flash_message('danger', 'Please select a valid final due date.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $now = date('Y-m-d H:i:s');
        $note = trim((string) $this->input->post('schedule_note'));

        $this->db->trans_start();

        $this->task_module->update_task((int) $task_id, array(
            'committed_due_date' => $committed_due_date,
            'status' => 'OPEN',
            'last_update_note' => $note,
            'updated_on' => $now
        ));

        $this->task_module->add_update(array(
            'task_id' => (int) $task_id,
            'update_type' => 'DUE_DATE_CONFIRMED',
            'status' => 'OPEN',
            'progress_percent' => (int) $task['progress_percent'],
            'due_date' => $committed_due_date,
            'update_note' => $note,
            'created_by' => $current_user_id,
            'created_on' => $now
        ));

        $action_url = page_url . 'Task_management/view/' . (int) $task_id;
        $message = $task['task_code'] . ' due date confirmed for ' . date('d M Y', strtotime($committed_due_date)) . '.';
        $this->task_module->add_notifications((int) $task_id, array((int) $task['assigned_by_user_id']), $message, $action_url);

        $updated_task = $this->task_module->get_task((int) $task_id);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Final due date could not be confirmed right now. Please try again.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $this->send_task_email(
            $updated_task,
            'Due Date Confirmed: ' . $updated_task['task_code'],
            'The assignee has confirmed the final due date for this task.',
            $note
        );
        $this->send_task_whatsapp($updated_task, (int) $updated_task['assigned_by_user_id'], 'The assignee has confirmed the final due date.', $note, 'due_date_confirmed');

        $this->set_flash_message('success', 'Final due date confirmed successfully.');
        redirect(page_url . 'Task_management/view/' . (int) $task_id);
    }

    public function save_progress($task_id = 0)
    {
        if (!$this->task_module->module_ready()) {
            redirect(page_url . 'Task_management');
        }

        $task = $this->task_module->get_task((int) $task_id);
        if (empty($task)) {
            $this->set_flash_message('danger', 'Requested task could not be found.');
            redirect(page_url . 'Task_management');
        }

        $current_user_id = $this->get_current_user_id();
        if ((int) $task['assigned_to_user_id'] !== $current_user_id) {
            $this->set_flash_message('danger', 'Only the assigned person can update progress.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        if (empty($task['committed_due_date']) || $task['committed_due_date'] === '0000-00-00') {
            $this->set_flash_message('danger', 'Please confirm the final due date before posting progress updates.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('progress_percent', 'Progress', 'required|trim|integer');
        $this->form_validation->set_rules('progress_note', 'Progress Note', 'required|trim');
        $this->form_validation->set_rules('status', 'Status', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->set_flash_message('danger', validation_errors());
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $progress_percent = max(0, min(100, (int) $this->input->post('progress_percent')));
        $submitted_status = strtoupper(trim((string) $this->input->post('status')));
        $note = trim((string) $this->input->post('progress_note'));

        $new_status = 'OPEN';
        if ($submitted_status === 'COMPLETED' || $progress_percent >= 100) {
            $new_status = 'COMPLETED';
            $progress_percent = 100;
        } elseif ($progress_percent > 0 || $submitted_status === 'IN_PROGRESS') {
            $new_status = 'IN_PROGRESS';
        }

        $now = date('Y-m-d H:i:s');
        $update_data = array(
            'status' => $new_status,
            'progress_percent' => $progress_percent,
            'last_update_note' => $note,
            'updated_on' => $now
        );
        if ($new_status === 'COMPLETED') {
            $update_data['completed_on'] = $now;
            $update_data['completed_by_user_id'] = $current_user_id;
        }

        $this->db->trans_start();

        $this->task_module->update_task((int) $task_id, $update_data);
        $this->task_module->add_update(array(
            'task_id' => (int) $task_id,
            'update_type' => $new_status === 'COMPLETED' ? 'COMPLETED' : 'PROGRESS_UPDATED',
            'status' => $new_status,
            'progress_percent' => $progress_percent,
            'due_date' => $task['committed_due_date'],
            'update_note' => $note,
            'created_by' => $current_user_id,
            'created_on' => $now
        ));

        $action_url = page_url . 'Task_management/view/' . (int) $task_id;
        $message = $new_status === 'COMPLETED'
            ? $task['task_code'] . ' has been marked completed.'
            : $task['task_code'] . ' progress updated to ' . $progress_percent . '%.';
        $this->task_module->add_notifications((int) $task_id, array((int) $task['assigned_by_user_id']), $message, $action_url);

        $updated_task = $this->task_module->get_task((int) $task_id);
        $email_heading = $new_status === 'COMPLETED'
            ? 'The task has been completed by the assignee.'
            : 'The assignee has posted a live progress update on this task.';
        $email_subject = ($new_status === 'COMPLETED' ? 'Task Completed: ' : 'Task Update: ') . $updated_task['task_code'];

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Progress could not be updated right now. Please try again.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $this->send_task_email($updated_task, $email_subject, $email_heading, $note);
        $this->send_task_whatsapp($updated_task, (int) $updated_task['assigned_by_user_id'], $email_heading, $note, strtolower($new_status));

        $this->set_flash_message('success', $new_status === 'COMPLETED' ? 'Task marked completed successfully.' : 'Progress updated successfully.');
        redirect(page_url . 'Task_management/view/' . (int) $task_id);
    }

    public function reopen($task_id = 0)
    {
        if (!$this->task_module->module_ready()) {
            redirect(page_url . 'Task_management');
        }

        $task = $this->task_module->get_task((int) $task_id);
        if (empty($task)) {
            $this->set_flash_message('danger', 'Requested task could not be found.');
            redirect(page_url . 'Task_management');
        }

        $current_user_id = $this->get_current_user_id();
        if ((int) $task['assigned_by_user_id'] !== $current_user_id) {
            $this->set_flash_message('danger', 'Only the person who created this task can reopen it.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        if (strtoupper((string) $task['status']) !== 'COMPLETED') {
            $this->set_flash_message('danger', 'Only completed tasks can be reopened.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('reopen_remark', 'Reopen Remark', 'required|trim|max_length[2000]');

        if ($this->form_validation->run() == FALSE) {
            $this->set_flash_message('danger', validation_errors());
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $remark = trim((string) $this->input->post('reopen_remark'));
        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        /* A REOPENED TASK GOES BACK TO THE START OF THE FLOW.
         *
         * Status AWAITING_DUE_DATE and the committed date cleared, exactly as
         * a newly assigned task arrives - so the assignee commits to a NEW
         * date for the work that is actually left, and confirm_due_date()
         * (which only runs in this status) is what reactivates the task.
         *
         * Keeping the old date, as this did before, reopened the task already
         * overdue against a deadline nobody had agreed to, and let progress
         * updates resume against it.
         *
         * The previous commitment is not lost: it stays on the
         * DUE_DATE_CONFIRMED row in task_management_updates, which the update
         * history shows.
         */
        $this->task_module->update_task((int) $task_id, array(
            'status' => 'AWAITING_DUE_DATE',
            'progress_percent' => 0,
            'committed_due_date' => null,
            'last_update_note' => $remark,
            'completed_on' => null,
            'completed_by_user_id' => null,
            'updated_on' => $now
        ));

        $this->task_module->add_update(array(
            'task_id' => (int) $task_id,
            'update_type' => 'REOPENED',
            'status' => 'AWAITING_DUE_DATE',
            'progress_percent' => 0,
            // no committed date any more - the next history row is the
            // assignee's new one
            'due_date' => null,
            'update_note' => $remark,
            'created_by' => $current_user_id,
            'created_on' => $now
        ));

        $action_url = page_url . 'Task_management/view/' . (int) $task_id;
        $message = $task['task_code'] . ' has been reopened and assigned back to you. Please confirm a new due date.';
        $this->task_module->add_notifications(
            (int) $task_id,
            array((int) $task['assigned_to_user_id']),
            $message,
            $action_url
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Task could not be reopened right now. Please try again.');
            redirect(page_url . 'Task_management/view/' . (int) $task_id);
        }

        $updated_task = $this->task_module->get_task((int) $task_id);
        $this->send_task_email(
            $updated_task,
            'Task Reopened: ' . $updated_task['task_code'],
            'The task creator has reopened this task. It is back with the same assignee, who now needs to confirm a new final due date before work resumes.',
            $remark
        );
        $this->send_task_whatsapp($updated_task, (int) $updated_task['assigned_to_user_id'], 'This task has been reopened and is back with you. Please confirm a new final due date.', $remark, 'reopened');

        $this->set_flash_message('success', 'Task reopened and assigned back to ' . $updated_task['assignee_name'] . '. They will be asked to confirm a new due date.');
        redirect(page_url . 'Task_management/view/' . (int) $task_id);
    }

    public function fetch_notifications()
    {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: 0');
        $this->output->set_content_type('application/json');

        if (!$this->task_module->module_ready()) {
            $this->output->set_output(json_encode(array('items' => array(), 'unread_count' => 0)));
            return;
        }

        $user_id = $this->get_current_user_id();
        $notifications = $this->task_module->get_unread_notifications($user_id);

        // An OBJECT now, not a bare array: the poller needs the total as well
        // as the page of items, so it can keep the navigation badge in step
        // without a second request. The poller accepts either shape, so an
        // old cached copy of the page keeps working until it is reloaded.
        $this->output->set_output(json_encode(array(
            'items' => $notifications,
            'unread_count' => $this->task_module->get_unread_notification_count($user_id)
        )));
    }

    public function mark_notification_read()
    {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: 0');
        $this->output->set_content_type('application/json');

        if (!$this->task_module->module_ready()) {
            $this->output->set_output(json_encode(array('status' => 'success')));
            return;
        }

        $user_id = $this->get_current_user_id();
        $notification_id = (int) $this->input->post('notification_id');
        if ($notification_id > 0) {
            $this->task_module->mark_notification_read($notification_id, $user_id);
        } else {
            $this->task_module->mark_all_notifications_read($user_id);
        }

        // the caller updates the navigation badge from this, so it never has
        // to guess what the count became
        $this->output->set_output(json_encode(array(
            'status' => 'success',
            'unread_count' => $this->task_module->get_unread_notification_count($user_id)
        )));
    }

    /**
     * Form validation rule: a due date may be today or later, never earlier.
     *
     * PUBLIC because CodeIgniter reaches it as `callback_not_past_date` - it
     * is not part of the controller's URL surface (CI does not route methods
     * it cannot match to a segment, and nothing links here).
     *
     * Empty passes: `required` is the rule that reports a missing date, and
     * having two rules complain about the same empty field just produces two
     * error messages.
     *
     * "Past" is measured in the SERVER's day, which is also the day the form's
     * min= attribute was rendered from, so the browser and the server always
     * agree on where the line is.
     */
    public function not_past_date($date_value)
    {
        $date_value = trim((string) $date_value);
        if ($date_value === '') {
            return TRUE;
        }

        $stamp = strtotime($date_value);
        if (!$stamp) {
            $this->form_validation->set_message('not_past_date', 'Please select a valid {field}.');
            return FALSE;
        }

        if (date('Y-m-d', $stamp) < date('Y-m-d')) {
            $this->form_validation->set_message(
                'not_past_date',
                '{field} cannot be in the past. Choose today or a later date.'
            );
            return FALSE;
        }

        return TRUE;
    }

    private function can_view_task($user_id, $task)
    {
        $user_id = (int) $user_id;
        if ($user_id <= 0 || empty($task)) {
            return false;
        }

        if ($this->is_admin_user($user_id)) {
            return true;
        }

        if ((int) $task['assigned_by_user_id'] === $user_id || (int) $task['assigned_to_user_id'] === $user_id) {
            return true;
        }

        $leaders = array_merge(
            $this->task_module->get_user_team_leaders((int) $task['assigned_by_user_id']),
            $this->task_module->get_user_team_leaders((int) $task['assigned_to_user_id'])
        );

        foreach ($leaders as $leader) {
            if (!empty($leader['user_id']) && (int) $leader['user_id'] === $user_id) {
                return true;
            }
        }

        return false;
    }

    private function send_task_email($task, $subject, $heading, $update_note)
    {
        if (empty($task)) {
            return;
        }

        $this->initialize_task_mailer();

        $creator = $this->task_module->get_user((int) $task['assigned_by_user_id']);
        $assignee = $this->task_module->get_user((int) $task['assigned_to_user_id']);

        $primary_emails = array();
        if (!empty($creator['email'])) {
            $primary_emails[] = trim((string) $creator['email']);
        }
        if (!empty($assignee['email'])) {
            $primary_emails[] = trim((string) $assignee['email']);
        }
        $primary_emails = array_values(array_unique(array_filter($primary_emails)));

        if (empty($primary_emails)) {
            return;
        }

        $cc_emails = array();
        $leaders = array_merge(
            $this->task_module->get_user_team_leaders((int) $task['assigned_by_user_id']),
            $this->task_module->get_user_team_leaders((int) $task['assigned_to_user_id'])
        );
        foreach ($leaders as $leader) {
            if (empty($leader['email'])) {
                continue;
            }
            $email = trim((string) $leader['email']);
            if ($email === '' || in_array($email, $primary_emails, true) || in_array($email, $cc_emails, true)) {
                continue;
            }
            $cc_emails[] = $email;
        }

        $message = $this->build_task_email($task, $heading, $update_note);

        $this->email->clear(true);
        $this->email->set_mailtype('html');
        $this->email->from('taskmanagement@shubhampack.com', 'Shubham Task Management');
        $this->email->to($primary_emails);
        if (!empty($cc_emails)) {
            $this->email->cc($cc_emails);
        }
        $this->email->subject($subject);
        $this->email->message($message);
        if (!$this->email->send()) {
            log_message('error', 'Task Management email failed: task=' . (int)$task['id'] . ', subject=' . $subject);
        }
    }

    private function initialize_task_mailer()
    {
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'taskmanagement@shubhampack.com',
            'smtp_pass' => 'ficihlqnfcdrrqkb',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        );

        $this->email->initialize($config);
    }

    /**
     * MOBILE PUSH (added for the app). Called from send_task_whatsapp(),
     * which every task event already routes through, so create / due-date
     * confirmed / progress / completed / reopened are all covered.
     */
    private function mobile_push_task($task, $recipient_user_id, $heading, $update_note, $event)
    {
        try {
            if (empty($task) || (int) $recipient_user_id <= 0) return;
            if (!function_exists('sendFCMData')) return;

            $devices = $this->db->select('fcm_token')
                ->from('user_devices')
                ->where('user_id', (int) $recipient_user_id)
                ->where('fcm_token !=', '')
                ->get()->result();

            if (empty($devices)) return;

            $code = !empty($task['task_code']) ? $task['task_code'] : 'Task';
            $titles = array(
                'assigned'           => 'New task: ' . $code,
                'due_date_confirmed' => 'Due date confirmed: ' . $code,
                'completed'          => 'Task completed: ' . $code,
                'reopened'           => 'Task reopened: ' . $code,
            );
            $title = isset($titles[$event]) ? $titles[$event] : 'Task update: ' . $code;

            $body = trim((string) $update_note) !== '' ? $update_note : $heading;
            $preview = mb_substr(trim(strip_tags((string) $body)), 0, 140);

            foreach ($devices as $d) {
                if (empty($d->fcm_token)) continue;
                sendFCMData($d->fcm_token, $title, $preview, array(
                    'type'      => 'task_management',
                    'task_id'   => (string) (int) $task['id'],
                    'screen'    => 'task_detail',
                    'timestamp' => date('Y-m-d H:i:s'),
                ));
            }
        } catch (Throwable $e) {
            log_message('error', 'Task web push failed: ' . $e->getMessage());
        }
    }
    private function send_task_whatsapp($task, $recipient_user_id, $heading, $update_note, $event)
    {
        if (empty($task) || (int) $recipient_user_id <= 0) return false;

        // MOBILE PUSH - every task event routes through here
        $this->mobile_push_task($task, $recipient_user_id, $heading, $update_note, $event);
        $recipient = $this->task_module->get_user((int) $recipient_user_id);
        if (empty($recipient)) return false;

        $digits = preg_replace('/\D+/', '', isset($recipient['contact_number']) ? (string) $recipient['contact_number'] : '');
        if (strlen($digits) > 10 && substr($digits, 0, 2) === '91') $digits = substr($digits, 2);
        $digits = ltrim($digits, '0');
        if (strlen($digits) !== 10) {
            log_message('error', 'Task Management WhatsApp skipped: invalid mobile for user ' . (int)$recipient_user_id . ', task ' . (int)$task['id']);
            return false;
        }

        // both date columns are nullable, and strtotime(null) is deprecated
        // in PHP 8 - a task with neither date set used to emit a notice here
        // and then send "01-01-1970" to the assignee
        $due_source = '';
        $due_is_committed = FALSE;
        foreach (array('committed_due_date', 'requested_due_date') as $date_field) {
            $candidate = isset($task[$date_field]) ? trim((string) $task[$date_field]) : '';
            if ($candidate !== '' && $candidate !== '0000-00-00') {
                $due_source = $candidate;
                $due_is_committed = ($date_field === 'committed_due_date');
                break;
            }
        }
        // Marked as a suggestion when it is not the agreed date. A reopened
        // task has no agreed date at all, and its original suggestion is
        // usually in the past - sending that as "Due Date" would read as a
        // deadline the assignee had already missed.
        $due_date = $due_source !== ''
            ? date('d-m-Y', strtotime($due_source)) . ($due_is_committed ? '' : ' (suggested)')
            : 'Not set';
        $message = 'Dear ' . (!empty($recipient['name']) ? $recipient['name'] : 'Team Member') . ",\n\n"
            . trim((string)$heading) . "\n\n"
            . 'Task: *' . trim((string)$task['task_code']) . ' - ' . trim((string)$task['title']) . "*\n"
            . 'Assigned By: *' . trim((string)$task['creator_name']) . "*\n"
            . 'Assigned To: *' . trim((string)$task['assignee_name']) . "*\n"
            . 'Priority: *' . ucwords(strtolower((string)$task['priority'])) . "*\n"
            . 'Due Date: *' . $due_date . "*\n"
            . 'Status: *' . ucwords(strtolower(str_replace('_', ' ', (string)$task['status']))) . ' (' . (int)$task['progress_percent'] . "%)*\n\n"
            . 'Latest Note: ' . trim((string)$update_note) . "\n\n"
            . 'Open Task: ' . page_url . 'Task_management/view/' . (int)$task['id'] . "\n\n"
            . '*Shubham Pack Task Management* 🚀';

        $ch = curl_init('https://app.messageautosender.com/api/v1/message/create');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array(
                'receiverMobileNo' => '91' . $digits,
                'username' => whatsappuser1,
                'password' => whatsapppass1,
                'message' => $message
            ),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 25
        ));
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $curl_error !== '' || $http_code < 200 || $http_code >= 300) {
            log_message('error', 'Task Management WhatsApp failed: event=' . $event . ', task=' . (int)$task['id'] . ', HTTP=' . $http_code . ', curl=' . $curl_error . ', response=' . substr((string)$response, 0, 500));
            return false;
        }
        log_message('info', 'Task Management WhatsApp accepted: event=' . $event . ', task=' . (int)$task['id'] . ', HTTP=' . $http_code . ', response=' . substr((string)$response, 0, 300));
        return true;
    }

    private function build_task_email($task, $heading, $update_note)
    {
        $task_url = page_url . 'Task_management/view/' . (int) $task['id'];
        $priority = ucwords(strtolower((string) $task['priority']));
        $status = ucwords(strtolower(str_replace('_', ' ', (string) $task['status'])));

        return '
            <table width="640" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f5f8fc; border: 1px solid #d9e4ef; border-radius: 10px; overflow: hidden;">
                <tr>
                    <td style="background:#16365c; padding:18px 24px;">
                        <h2 style="margin:0; color:#ffffff; font-size:22px;">Task Management Update</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px; background:#ffffff; color:#243447;">
                        <p style="margin:0 0 14px; font-size:14px; line-height:1.7;">' . htmlspecialchars($heading) . '</p>
                        <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; border:1px solid #e6edf5;">
                            <tr>
                                <td style="border:1px solid #e6edf5; width:180px; font-weight:bold;">Task Code</td>
                                <td style="border:1px solid #e6edf5;">' . htmlspecialchars((string) $task['task_code']) . '</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #e6edf5; font-weight:bold;">Task Title</td>
                                <td style="border:1px solid #e6edf5;">' . htmlspecialchars((string) $task['title']) . '</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #e6edf5; font-weight:bold;">Assigned By</td>
                                <td style="border:1px solid #e6edf5;">' . htmlspecialchars((string) $task['creator_name']) . '</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #e6edf5; font-weight:bold;">Assigned To</td>
                                <td style="border:1px solid #e6edf5;">' . htmlspecialchars((string) $task['assignee_name']) . '</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #e6edf5; font-weight:bold;">Priority</td>
                                <td style="border:1px solid #e6edf5;">' . htmlspecialchars($priority) . '</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #e6edf5; font-weight:bold;">Status</td>
                                <td style="border:1px solid #e6edf5;">' . htmlspecialchars($status) . ' (' . (int) $task['progress_percent'] . '%)</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #e6edf5; font-weight:bold;">Suggested Due Date</td>
                                <td style="border:1px solid #e6edf5;">' . $this->display_date($task['requested_due_date']) . '</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #e6edf5; font-weight:bold;">Final Due Date</td>
                                <td style="border:1px solid #e6edf5;">' . $this->display_date($task['committed_due_date']) . '</td>
                            </tr>
                        </table>
                        <div style="margin-top:16px; padding:14px 16px; background:#f8fbff; border:1px solid #e3ebf4; border-radius:8px;">
                            <div style="font-weight:bold; margin-bottom:6px;">Latest Note</div>
                            <div style="font-size:14px; line-height:1.7;">' . nl2br(htmlspecialchars((string) $update_note)) . '</div>
                        </div>
                        <div style="margin-top:22px;">
                            <a href="' . htmlspecialchars($task_url) . '" style="display:inline-block; padding:11px 18px; background:#1d4ed8; color:#ffffff; text-decoration:none; border-radius:999px; font-weight:bold;">Open Task</a>
                        </div>
                    </td>
                </tr>
            </table>';
    }

    /**
     * File types a task attachment may be.
     *
     * An ALLOW list, not a block list. The uploaded file lands under
     * /image_bank/task_management/, which the web server serves directly, so
     * any extension it is willing to execute is a way to run code on this
     * host: before this list existed, `payload.php` was a valid attachment and
     * hitting its URL ran it. `svg` and `html` are left out for the same
     * reason in miniature - both can carry script and would run on this
     * origin, with the user's session.
     */
    private $allowed_attachment_extensions = array(
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'txt', 'rtf',
        'zip', 'rar', '7z'
    );

    /**
     * Move one uploaded attachment into place.
     *
     * @param  string $field_name  the $_FILES key
     * @param  string $error       filled in with a reason when nothing is
     *                             stored, so the caller can say so instead of
     *                             silently dropping the file
     * @return string              stored filename, or '' when there is none
     */
    private function upload_task_attachment($field_name, &$error = '')
    {
        $error = '';

        if (empty($_FILES[$field_name]['name'])) {
            return '';
        }

        $original_name = (string) $_FILES[$field_name]['name'];
        $extension = strtolower((string) pathinfo($original_name, PATHINFO_EXTENSION));

        if ($extension === '' || !in_array($extension, $this->allowed_attachment_extensions, true)) {
            $error = 'The attachment was not saved: "' . htmlspecialchars($original_name) . '" is not an allowed file type. '
                   . 'Allowed types are ' . strtoupper(implode(', ', $this->allowed_attachment_extensions)) . '.';
            return '';
        }

        if (!empty($_FILES[$field_name]['error'])) {
            $error = 'The attachment could not be uploaded. Please try again with a smaller file.';
            return '';
        }

        $upload_dir = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/') . '/image_bank/task_management/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }

        $filename = 'task-' . time() . '-' . mt_rand(1000, 9999) . '.' . $extension;

        if (!@move_uploaded_file($_FILES[$field_name]['tmp_name'], $upload_dir . $filename)) {
            $error = 'The attachment could not be saved on the server. The task itself was created.';
            return '';
        }

        return $filename;
    }

    private function display_date($date_value)
    {
        $date_value = trim((string) $date_value);
        if ($date_value === '' || $date_value === '0000-00-00' || $date_value === '0000-00-00 00:00:00') {
            return '-';
        }

        $stamp = strtotime($date_value);
        return $stamp ? date('d M Y', $stamp) : '-';
    }

    private function get_module_navigation($active_key)
    {
        $navigation = array(
            array(
                'key' => 'dashboard',
                'label' => 'Overview',
                'url' => page_url . 'Task_management'
            ),
            array(
                'key' => 'create',
                'label' => 'Create Task',
                'url' => page_url . 'Task_management/create'
            ),
            array(
                'key' => 'assigned',
                'label' => 'Assigned To Me',
                'url' => page_url . 'Task_management#assigned-table'
            ),
            array(
                'key' => 'created',
                'label' => 'Created By Me',
                'url' => page_url . 'Task_management#created-table'
            )
        );

        if ($this->is_admin_user($this->get_current_user_id())) {
            $navigation[] = array(
                'key' => 'all_tasks',
                'label' => 'All Tasks',
                'url' => page_url . 'Task_management#all-tasks-table'
            );
        }

        return $navigation;
    }

    private function get_admin_task_filters()
    {
        return array(
            'keyword' => trim((string) $this->input->get('keyword')),
            'assigned_to_user_id' => (int) $this->input->get('assigned_to_user_id'),
            'assigned_by_user_id' => (int) $this->input->get('assigned_by_user_id'),
            'department_id' => (int) $this->input->get('department_id'),
            'status' => strtoupper(trim((string) $this->input->get('status'))),
            'priority' => strtoupper(trim((string) $this->input->get('priority'))),
            'due_state' => strtoupper(trim((string) $this->input->get('due_state')))
        );
    }

    private function build_task_summary($tasks)
    {
        $summary = array(
            'visible' => 0,
            'open' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'overdue' => 0
        );

        foreach ((array) $tasks as $task) {
            $summary['visible']++;
            $status = strtoupper((string) $task['status']);

            if ($status === 'COMPLETED') {
                $summary['completed']++;
            } elseif ($status === 'IN_PROGRESS') {
                $summary['in_progress']++;
            } else {
                $summary['open']++;
            }

            if (!empty($task['is_overdue'])) {
                $summary['overdue']++;
            }
        }

        return $summary;
    }

    private function set_flash_message($type, $message)
    {
        $type = trim((string) $type) !== '' ? trim((string) $type) : 'info';
        $this->session->set_flashdata('message', '<div class="alert alert-' . htmlspecialchars($type) . '">' . $message . '</div>');
    }

    private function get_current_user_id()
    {
        return !empty($this->session->userdata['logged_in']['user_id'])
            ? (int) $this->session->userdata['logged_in']['user_id']
            : 0;
    }

    private function is_admin_user($user_id)
    {
        if (!empty($this->session->userdata['logged_in']['adminuser']) && (int) $this->session->userdata['logged_in']['adminuser'] === 1) {
            return true;
        }

        return in_array((int) $user_id, $this->admin_user_ids, true);
    }
}
