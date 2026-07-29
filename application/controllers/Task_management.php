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
        $this->form_validation->set_rules('assigned_to_user_id', 'Assign To', 'required|trim|integer');
        $this->form_validation->set_rules('title', 'Task Title', 'required|trim');
        $this->form_validation->set_rules('task_details', 'Task Details', 'required|trim');
        $this->form_validation->set_rules('priority', 'Priority', 'required|trim');
        $this->form_validation->set_rules('requested_due_date', 'Suggested Due Date', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->create();
            return;
        }

        $assigned_to_user_id = (int) $this->input->post('assigned_to_user_id');
        $assignee = $this->task_module->get_user($assigned_to_user_id);
        if (empty($assignee)) {
            $this->set_flash_message('danger', 'Selected assignee could not be found.');
            redirect(page_url . 'Task_management/create');
        }

        if ((int) $assignee['business_location'] !== (int) $this->task_business_location_id) {
            $this->set_flash_message('danger', 'Only Shubham Pack users can be selected in this task form.');
            redirect(page_url . 'Task_management/create');
        }

        $requested_due_date = date('Y-m-d', strtotime((string) $this->input->post('requested_due_date')));
        if ($requested_due_date === '1970-01-01') {
            $this->set_flash_message('danger', 'Please select a valid suggested due date.');
            redirect(page_url . 'Task_management/create');
        }

        $attachment = $this->upload_task_attachment('attachment');
        $now = date('Y-m-d H:i:s');
        $current_user_id = $this->get_current_user_id();

        $task_data = array(
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
            'assigned_to_user_id' => $assigned_to_user_id,
            'assigned_to_department_id' => !empty($assignee['department_id']) ? (int) $assignee['department_id'] : 0,
            'status' => 'AWAITING_DUE_DATE',
            'progress_percent' => 0,
            'last_update_note' => 'Task created and waiting for assignee due-date confirmation.',
            'created_on' => $now,
            'updated_on' => $now
        );

        $this->db->trans_start();

        $task_id = $this->task_module->create_task($task_data);
        $task_code = $this->task_module->generate_task_code($task_id);
        $this->task_module->update_task($task_id, array('task_code' => $task_code));

        $this->task_module->add_update(array(
            'task_id' => $task_id,
            'update_type' => 'CREATED',
            'status' => 'AWAITING_DUE_DATE',
            'progress_percent' => 0,
            'due_date' => $requested_due_date,
            'update_note' => trim((string) $this->input->post('task_details')),
            'created_by' => $current_user_id,
            'created_on' => $now
        ));

        $action_url = page_url . 'Task_management/view/' . $task_id;
        $creator = $this->task_module->get_user($current_user_id);
        $notification_message = $task_code . ' assigned by ' . (!empty($creator['name']) ? $creator['name'] : 'Task owner') . '. Please confirm the due date.';
        $this->task_module->add_notifications($task_id, array($assigned_to_user_id), $notification_message, $action_url);

        $task = $this->task_module->get_task($task_id);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Task could not be created right now. Please try again.');
            redirect(page_url . 'Task_management/create');
        }

        $this->send_task_email(
            $task,
            'New Task Assigned: ' . $task_code,
            'A new task has been created and assigned. The assignee needs to confirm the due date before execution starts.',
            trim((string) $this->input->post('task_details'))
        );

        $this->set_flash_message('success', 'Task created successfully with reference ' . $task_code . '.');
        redirect(page_url . 'Task_management/view/' . $task_id);
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
        $this->form_validation->set_rules('committed_due_date', 'Final Due Date', 'required|trim');
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

        $this->set_flash_message('success', $new_status === 'COMPLETED' ? 'Task marked completed successfully.' : 'Progress updated successfully.');
        redirect(page_url . 'Task_management/view/' . (int) $task_id);
    }

    public function fetch_notifications()
    {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: 0');
        $this->output->set_content_type('application/json');

        if (!$this->task_module->module_ready()) {
            $this->output->set_output(json_encode(array()));
            return;
        }

        $notifications = $this->task_module->get_unread_notifications($this->get_current_user_id());
        $this->output->set_output(json_encode($notifications));
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

        $notification_id = (int) $this->input->post('notification_id');
        if ($notification_id > 0) {
            $this->task_module->mark_notification_read($notification_id, $this->get_current_user_id());
        } else {
            $this->task_module->mark_all_notifications_read($this->get_current_user_id());
        }

        $this->output->set_output(json_encode(array('status' => 'success')));
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
        $this->email->send();
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

    private function upload_task_attachment($field_name)
    {
        if (empty($_FILES[$field_name]['name'])) {
            return '';
        }

        $upload_dir = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/') . '/image_bank/task_management/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }

        $original_name = (string) $_FILES[$field_name]['name'];
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $filename = 'task-' . time() . '-' . mt_rand(1000, 9999);
        if ($extension !== '') {
            $filename .= '.' . strtolower($extension);
        }

        if (!@move_uploaded_file($_FILES[$field_name]['tmp_name'], $upload_dir . $filename)) {
            return '';
        }

        return $filename;
    }

    private function display_date($date_value)
    {
        if (empty($date_value) || $date_value === '0000-00-00') {
            return '-';
        }

        return date('d M Y', strtotime($date_value));
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
