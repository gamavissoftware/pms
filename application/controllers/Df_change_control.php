<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Df_change_control extends CI_Controller
{
    private $admin_user_ids = array(61, 139, 161, 162, 167);

    public function __construct()
    {
        parent::__construct();

        $session = $this->session->userdata('logged_in');
        if ($session == FALSE) {
            redirect(page_url);
        }

        $user_id = $this->session->userdata['logged_in']['user_id'];
        if (empty($user_id)) {
            redirect(site_url(), 'refresh');
        }

        $this->load->model('Df_change_control_model', 'change_model');
    }

    public function index()
    {
        $user_id = $this->get_current_user_id();
        if (!$this->can_access_change_dashboard($user_id)) {
            if ($this->can_raise_change_request($user_id)) {
                redirect(page_url . 'Df_change_control/create');
            }
            $this->deny_change_control_access();
        }

        $data = array(
            'module_ready' => $this->change_model->module_ready(),
            'migration_file' => 'Database/df_change_control_001.sql',
            'stats' => $this->change_model->get_dashboard_stats($user_id),
            'recent_changes' => $this->change_model->get_recent_changes(),
            'head_queue' => $this->change_model->get_head_queue($user_id),
            'assigned_queue' => $this->change_model->get_assigned_queue($user_id),
            'my_requests' => $this->change_model->get_my_requests($user_id),
            'department_load' => $this->change_model->get_department_load_snapshot(),
            'current_user_id' => $user_id,
            'is_admin' => $this->is_admin_user($user_id),
            'module_nav' => $this->get_module_navigation($user_id, 'dashboard')
        );

        $this->load->view('df_change_control/dashboard', $data);
    }

    public function dashboard()
    {
        $this->index();
    }

    public function create($df_id = 0)
    {
        $user_id = $this->get_current_user_id();
        if (!$this->can_raise_change_request($user_id)) {
            if ($this->can_access_change_dashboard($user_id)) {
                redirect(page_url . 'Df_change_control');
            }
            $this->deny_change_control_access();
        }

        $data = array(
            'module_ready' => $this->change_model->module_ready(),
            'migration_file' => 'Database/df_change_control_001.sql',
            'df_options' => $this->change_model->get_df_options((int)$df_id),
            'department_options' => $this->change_model->get_department_options(),
            'selected_df_id' => (int)$df_id,
            'module_nav' => $this->get_module_navigation($user_id, 'create')
        );

        $this->load->view('df_change_control/create', $data);
    }

    public function save()
    {
        $user_id = $this->get_current_user_id();
        if (!$this->can_raise_change_request($user_id)) {
            $this->deny_change_control_access($this->can_access_change_dashboard($user_id) ? page_url . 'Df_change_control' : page_url . 'Dashboard');
        }

        if (!$this->change_model->module_ready()) {
            $this->set_flash_message('danger', 'DF Change Control tables are not ready. Please run the migration first.');
            redirect(page_url . 'Df_change_control/create');
        }

        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('df_id', 'DF', 'required|trim|integer');
        $this->form_validation->set_rules('request_type', 'Request Type', 'required|trim');
        $this->form_validation->set_rules('change_category', 'Change Category', 'required|trim');
        $this->form_validation->set_rules('priority', 'Priority', 'required|trim');
        $this->form_validation->set_rules('source_of_change', 'Source Of Change', 'required|trim');
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('change_summary', 'Change Summary', 'required|trim');

        $department_ids = $this->input->post('department_ids');
        $department_ids = is_array($department_ids) ? array_values(array_unique(array_filter(array_map('intval', $department_ids)))) : array();

        if ($this->form_validation->run() == FALSE || empty($department_ids)) {
            if (empty($department_ids)) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Please select at least one department to notify.</div>');
            }
            $this->create((int)$this->input->post('df_id'));
            return;
        }

        $attachment = $this->upload_change_attachment('attachment');
        $now = date('Y-m-d H:i:s');

        $change_data = array(
            'change_no' => '',
            'df_id' => (int)$this->input->post('df_id'),
            'request_type' => strtoupper(trim($this->input->post('request_type'))),
            'change_category' => strtoupper(trim($this->input->post('change_category'))),
            'priority' => strtoupper(trim($this->input->post('priority'))),
            'source_of_change' => strtoupper(trim($this->input->post('source_of_change'))),
            'reference_no' => trim((string)$this->input->post('reference_no')),
            'revision_no' => trim((string)$this->input->post('revision_no')),
            'title' => trim((string)$this->input->post('title')),
            'change_summary' => trim((string)$this->input->post('change_summary')),
            'impact_note' => trim((string)$this->input->post('impact_note')),
            'requested_from_department_id' => $this->get_current_department_id(),
            'attachment' => $attachment,
            'status' => 'OPEN',
            'created_by' => $user_id,
            'created_on' => $now
        );

        $this->db->trans_start();
        $this->db->insert('df_change_control', $change_data);
        $change_id = $this->db->insert_id();

        $change_no = strtoupper($change_data['request_type']) . '-' . date('ymd') . '-' . str_pad($change_id, 4, '0', STR_PAD_LEFT);
        $this->db->where('id', $change_id);
        $this->db->update('df_change_control', array('change_no' => $change_no));

        $head_user_ids = array();
        foreach ($department_ids as $department_id) {
            $head = $this->change_model->resolve_department_head($department_id);
            $department_head_id = !empty($head['user_id']) ? (int)$head['user_id'] : 0;

            $action_data = array(
                'change_id' => $change_id,
                'department_id' => (int)$department_id,
                'department_head_id' => $department_head_id,
                'status' => 'PENDING_HEAD_ACTION',
                'notified_on' => $now
            );
            $this->db->insert('df_change_control_departments', $action_data);
            $action_id = $this->db->insert_id();

            if ($department_head_id > 0) {
                $head_user_ids[] = $department_head_id;
            }

            $notification_note = 'Department notification created for department ID ' . $department_id . '.';
            $this->change_model->add_history($change_id, $action_id, $user_id, 'REQUESTER', 'DEPARTMENT_NOTIFIED', $notification_note);
        }

        $this->change_model->add_history($change_id, 0, $user_id, 'REQUESTER', 'REQUEST_CREATED', trim((string)$this->input->post('change_summary')));

        $change = $this->change_model->get_change_request($change_id);
        $subject = 'New ' . $change['request_type'] . ' Request: ' . $change_no;
        $notification_message = $change_no . ' is waiting for department head action.';
        $email_body = $this->build_change_email(
            'A new DF change-control request has been created.',
            $change,
            array(),
            '<p><strong>Department Heads Notified:</strong> ' . count($department_ids) . '</p>'
        );

        $this->notify_users($head_user_ids, $subject, $notification_message, $email_body);
        $this->notify_users(array($user_id), 'Request Recorded: ' . $change_no, 'Your change-control request ' . $change_no . ' has been created.', $email_body);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Unable to save the change-control request right now. Please try again.');
            redirect(page_url . 'Df_change_control/create/' . (int)$this->input->post('df_id'));
        }

        $this->set_flash_message('success', 'DF change-control request created successfully with reference ' . $change_no . '.');
        redirect(page_url . 'Df_change_control/view/' . $change_id);
    }

    public function view($change_id = 0)
    {
        if (!$this->change_model->module_ready()) {
            $this->set_flash_message('danger', 'DF Change Control tables are not ready. Please run the migration first.');
            redirect(page_url . 'Df_change_control');
        }

        $change = $this->change_model->get_change_request((int)$change_id);
        if (empty($change)) {
            $this->set_flash_message('danger', 'Requested change-control record was not found.');
            redirect(page_url . 'Df_change_control');
        }

        $current_user_id = $this->get_current_user_id();
        if (!$this->can_view_change_request($current_user_id, (int)$change_id)) {
            if ($this->can_access_change_dashboard($current_user_id)) {
                $this->deny_change_control_access(page_url . 'Df_change_control');
            }
            if ($this->can_raise_change_request($current_user_id)) {
                $this->deny_change_control_access(page_url . 'Df_change_control/create');
            }
            $this->deny_change_control_access();
        }

        $actions = $this->change_model->get_change_request_departments((int)$change_id);
        $history = $this->change_model->get_change_request_history((int)$change_id);
        $member_map = array();
        foreach ($actions as $action) {
            if (!isset($member_map[$action['department_id']])) {
                $member_map[$action['department_id']] = $this->change_model->get_department_members($action['department_id']);
            }
        }

        $data = array(
            'change' => $change,
            'actions' => $actions,
            'history' => $history,
            'member_map' => $member_map,
            'current_user_id' => $current_user_id,
            'is_admin' => $this->is_admin_user($current_user_id),
            'module_nav' => $this->get_module_navigation($current_user_id, 'view')
        );

        $this->load->view('df_change_control/view', $data);
    }

    public function take_department_action($action_id = 0)
    {
        if (!$this->change_model->module_ready()) {
            $this->set_flash_message('danger', 'DF Change Control tables are not ready. Please run the migration first.');
            redirect(page_url . 'Df_change_control');
        }

        $action = $this->change_model->get_department_action((int)$action_id);
        if (empty($action)) {
            $this->set_flash_message('danger', 'Department action record not found.');
            redirect(page_url . 'Df_change_control');
        }

        $current_user_id = $this->get_current_user_id();
        if (!$this->is_admin_user($current_user_id) && (int)$action['department_head_id'] !== $current_user_id) {
            $this->set_flash_message('danger', 'You are not authorized to take action on this department request.');
            redirect(page_url . 'Df_change_control/view/' . $action['change_id']);
        }

        $planned_days = (int)$this->input->post('planned_days');
        $assigned_user_id = (int)$this->input->post('assigned_user_id');
        $head_remarks = trim((string)$this->input->post('head_remarks'));

        if ($planned_days < 1 || $assigned_user_id <= 0) {
            $this->set_flash_message('danger', 'Please define the number of days and assign the department task to a team member.');
            redirect(page_url . 'Df_change_control/view/' . $action['change_id']);
        }

        $allowed_user = false;
        $department_members = $this->change_model->get_department_members($action['department_id']);
        foreach ($department_members as $member) {
            if ((int)$member['user_id'] === $assigned_user_id) {
                $allowed_user = true;
                break;
            }
        }

        if (!$allowed_user) {
            $this->set_flash_message('danger', 'Selected assignee does not belong to the notified department.');
            redirect(page_url . 'Df_change_control/view/' . $action['change_id']);
        }

        $target_date = date('Y-m-d', strtotime('+' . $planned_days . ' days'));
        $now = date('Y-m-d H:i:s');

        $update_data = array(
            'planned_days' => $planned_days,
            'target_date' => $target_date,
            'assigned_user_id' => $assigned_user_id,
            'assigned_on' => $now,
            'head_remarks' => $head_remarks,
            'status' => 'ASSIGNED'
        );

        $this->db->trans_start();
        $this->db->where('id', (int)$action_id);
        $this->db->update('df_change_control_departments', $update_data);

        $assignee = $this->get_user_info($assigned_user_id);
        $history_note = 'Assigned to ' . $this->format_user_name_from_array($assignee) . ' with TAT of ' . $planned_days . ' day(s), target date ' . date('d-M-Y', strtotime($target_date)) . '.';
        if ($head_remarks !== '') {
            $history_note .= ' HOD remarks: ' . $head_remarks;
        }
        $this->change_model->add_history($action['change_id'], $action_id, $current_user_id, 'DEPARTMENT_HEAD', 'TASK_ASSIGNED', $history_note);

        $status_update = $this->change_model->recompute_change_status($action['change_id']);
        $change = $this->change_model->get_change_request($action['change_id']);

        $subject = $change['change_no'] . ' assigned for execution';
        $notification_message = $change['change_no'] . ' has been assigned to you by ' . $this->get_current_user_name() . '.';
        $email_body = $this->build_change_email(
            'A department head has assigned this change-control action.',
            $change,
            array(
                'department' => $action['department'],
                'status' => 'ASSIGNED',
                'planned_days' => $planned_days,
                'target_date' => $target_date,
                'assignee_name' => $this->format_user_name_from_array($assignee),
                'head_remarks' => $head_remarks
            )
        );

        $notify_user_ids = array($assigned_user_id, (int)$change['created_by']);
        $this->notify_users($notify_user_ids, $subject, $notification_message, $email_body);

        if ($status_update['previous_status'] !== $status_update['current_status']) {
            $this->change_model->add_history($action['change_id'], $action_id, $current_user_id, 'SYSTEM', 'REQUEST_STATUS_UPDATED', 'Master request status changed to ' . $status_update['current_status'] . '.');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Unable to assign the department action right now. Please try again.');
        } else {
            $this->set_flash_message('success', 'Department action assigned successfully.');
        }

        redirect(page_url . 'Df_change_control/view/' . $action['change_id']);
    }

    public function update_department_execution($action_id = 0)
    {
        if (!$this->change_model->module_ready()) {
            $this->set_flash_message('danger', 'DF Change Control tables are not ready. Please run the migration first.');
            redirect(page_url . 'Df_change_control');
        }

        $action = $this->change_model->get_department_action((int)$action_id);
        if (empty($action)) {
            $this->set_flash_message('danger', 'Department execution record not found.');
            redirect(page_url . 'Df_change_control');
        }

        $current_user_id = $this->get_current_user_id();
        if (!$this->is_admin_user($current_user_id) && (int)$action['assigned_user_id'] !== $current_user_id) {
            $this->set_flash_message('danger', 'You are not authorized to update this execution task.');
            redirect(page_url . 'Df_change_control/view/' . $action['change_id']);
        }

        $execution_status = strtoupper(trim((string)$this->input->post('execution_status')));
        $assignee_remarks = trim((string)$this->input->post('assignee_remarks'));

        $result = $this->process_department_execution_update($action, $current_user_id, $execution_status, $assignee_remarks);

        $this->set_flash_message($result['success'] ? 'success' : 'danger', $result['message']);

        redirect(page_url . 'Df_change_control/view/' . $action['change_id']);
    }

    public function post_action_message($action_id = 0)
    {
        if (!$this->change_model->module_ready()) {
            $this->set_flash_message('danger', 'DF Change Control tables are not ready. Please run the migration first.');
            redirect(page_url . 'Df_change_control');
        }

        $action = $this->change_model->get_department_action((int)$action_id);
        if (empty($action)) {
            $this->set_flash_message('danger', 'Department communication thread was not found.');
            redirect(page_url . 'Df_change_control');
        }

        $current_user_id = $this->get_current_user_id();
        $message = trim((string)$this->input->post('message_note'));
        $change = $this->change_model->get_change_request((int)$action['change_id']);

        if ($message === '') {
            $this->set_flash_message('danger', 'Please enter a message before sending.');
            redirect(page_url . 'Df_change_control/view/' . $action['change_id'] . '#action-' . (int)$action['id']);
        }

        if (empty($change)) {
            $this->set_flash_message('danger', 'Change request was not found.');
            redirect(page_url . 'Df_change_control');
        }

        if ((int)$action['assigned_user_id'] <= 0) {
            $this->set_flash_message('danger', 'Communication starts after this department task is assigned to a team member.');
            redirect(page_url . 'Df_change_control/view/' . $action['change_id'] . '#action-' . (int)$action['id']);
        }

        $is_requester = (int)$change['created_by'] === $current_user_id;
        $is_assignee = (int)$action['assigned_user_id'] === $current_user_id;

        if (!$is_requester && !$is_assignee) {
            $this->set_flash_message('danger', 'You are not authorized to send a message in this communication thread.');
            redirect(page_url . 'Df_change_control/view/' . $action['change_id'] . '#action-' . (int)$action['id']);
        }

        $message_role = $is_assignee ? 'ASSIGNEE' : 'REQUESTER';
        $message_type = $is_assignee ? 'ASSIGNEE_MESSAGE' : 'REQUESTER_MESSAGE';

        $this->db->trans_start();
        $this->change_model->add_history($action['change_id'], $action['id'], $current_user_id, $message_role, $message_type, $message);

        $counterparty_ids = array();
        if ($is_assignee) {
            $counterparty_ids[] = (int)$change['created_by'];
        } else {
            $counterparty_ids[] = (int)$action['assigned_user_id'];
        }

        $sender_name = $this->get_current_user_name();
        $subject = $change['change_no'] . ' new communication update';
        $notification_message = $change['change_no'] . ' has a new message from ' . $sender_name . ' for ' . $action['department'] . '.';
        $email_body = $this->build_change_email(
            'A new requester / assignee communication update has been added.',
            $change,
            array(
                'department' => $action['department'],
                'status' => $action['status'],
                'planned_days' => $action['planned_days'],
                'target_date' => $action['target_date'],
                'assignee_name' => $action['assignee_name'],
                'head_remarks' => $action['head_remarks'],
                'assignee_remarks' => $action['assignee_remarks']
            ),
            '<div style="margin-top:18px; padding:14px 16px; border-radius:10px; background:#eff6ff; border:1px solid #bfdbfe;">
                <div style="font-size:13px; color:#17365d; font-weight:bold; margin-bottom:6px;">Latest Message From ' . htmlspecialchars($sender_name, ENT_QUOTES, 'UTF-8') . '</div>
                <div style="font-size:14px; color:#1f2937;">' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</div>
            </div>'
        );
        $this->notify_users($counterparty_ids, $subject, $notification_message, $email_body);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->set_flash_message('danger', 'Unable to send the communication update right now. Please try again.');
        } else {
            $this->set_flash_message('success', 'Communication update sent successfully.');
        }

        redirect(page_url . 'Df_change_control/view/' . $action['change_id'] . '#action-' . (int)$action['id']);
    }

    public function dashboard_assigned_tasks()
    {
        $results = array(
            'sEcho' => 1,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'aaData' => array()
        );

        $json_flags = 0;
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $json_flags |= JSON_UNESCAPED_UNICODE;
        }
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }
        if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
            $json_flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
        }
        $string_json_flags = $json_flags;
        if (defined('JSON_HEX_APOS')) {
            $string_json_flags |= JSON_HEX_APOS;
        }
        if (defined('JSON_HEX_QUOT')) {
            $string_json_flags |= JSON_HEX_QUOT;
        }

        if (!$this->change_model->module_ready()) {
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($results, $json_flags));
            return;
        }

        $current_user_id = $this->get_current_user_id();
        $rows = $this->get_dashboard_change_task_rows($current_user_id);

        $today = date('Y-m-d');
        $data = array();
        $i = 1;

        foreach ($rows as $row) {
            $is_requester = (int)$row['created_by'] === $current_user_id;
            $is_assignee = (int)$row['assigned_user_id'] === $current_user_id;
            $is_overdue = !empty($row['target_date']) && $row['target_date'] !== '0000-00-00' && $row['target_date'] < $today;
            $delay_label = '-';
            if (!empty($row['target_date']) && $row['target_date'] !== '0000-00-00') {
                $today_date = new DateTime($today);
                $target_date = new DateTime($row['target_date']);
                $diff_days = (int)$today_date->diff($target_date)->format('%a');
                if ($is_overdue) {
                    $delay_label = '<span style="color:red;font-weight:bold;">' . $diff_days . ' Day' . ($diff_days === 1 ? '' : 's') . ' Overdue</span>';
                } else {
                    $delay_label = '<span style="color:green;font-weight:bold;">Due on ' . date('d-m-Y', strtotime($row['target_date'])) . '</span>';
                }
            }

            $df_upload = '--';
            if (!empty($row['df_upload'])) {
                $df_upload = '<a href="' . sfdocument . 'Taskdocument/dfattachment/' . rawurlencode($row['df_upload']) . '" download><span><u>DOWNLOAD DF</u></span></a>';
            }

            $status_class = 'label-default';
            if ($row['status'] === 'COMPLETED') {
                $status_class = 'label-success';
            } elseif ($row['status'] === 'IN_PROGRESS') {
                $status_class = 'label-primary';
            } elseif ($row['status'] === 'ASSIGNED') {
                $status_class = 'label-info';
            }

            $scope_label = 'Team Execution';
            if ($is_requester && $is_assignee) {
                $scope_label = 'Requested By You and Assigned To You';
            } elseif ($is_assignee) {
                $scope_label = 'Assigned To You';
            } elseif ($is_requester) {
                $scope_label = 'Requested By You';
            } elseif ($this->is_admin_user($current_user_id)) {
                $scope_label = 'Admin Monitoring';
            }

            $progress_summary = ((int)$row['department_count'] > 0)
                ? (int)$row['completed_department_count'] . ' / ' . (int)$row['department_count'] . ' departments completed'
                : 'Progress will appear after departments are linked';

            $request_block = '<strong>' . htmlspecialchars((string)$row['change_no']) . '</strong><br>'
                . '<span class="label label-default" style="display:inline-block;margin-top:6px;">' . htmlspecialchars($scope_label, ENT_QUOTES, 'UTF-8') . '</span><br><br>'
                . htmlspecialchars((string)$row['change_title']) . '<br><br>'
                . '<strong>DF:</strong> ' . htmlspecialchars((string)$row['df_no']) . '<br>'
                . htmlspecialchars((string)$row['df_description']) . '<br><br>'
                . '<strong>Requester:</strong> ' . htmlspecialchars((string)$row['creator_name']) . '<br>'
                . '<strong>Progress:</strong> ' . htmlspecialchars($progress_summary, ENT_QUOTES, 'UTF-8');

            $remarks = '<strong>HOD:</strong> ' . (trim((string)$row['head_remarks']) !== '' ? nl2br(htmlspecialchars((string)$row['head_remarks'])) : '-') . '<br><br>'
                . '<strong>Execution:</strong> ' . (trim((string)$row['assignee_remarks']) !== '' ? nl2br(htmlspecialchars((string)$row['assignee_remarks'])) : '-') . '<br><br>'
                . '<strong>Communication:</strong> Open the request to view the requester / assignee conversation chain.';

            $view_url = page_url . 'Df_change_control/view/' . (int)$row['change_id'] . '#action-' . (int)$row['id'];
            $action_buttons = '<a href="' . $view_url . '" target="_blank" class="btn btn-primary btn-xs">Open Request</a>';
            if ($is_requester || $is_assignee) {
                $action_buttons .= '<br><a href="' . $view_url . '" target="_blank" class="btn btn-default btn-xs" style="margin-top:6px;">Open Conversation</a>';
            }
            if ($this->is_admin_user($current_user_id) || (int)$row['assigned_user_id'] === $current_user_id) {
                $encoded_change_no = json_encode((string)$row['change_no'], $string_json_flags);
                $encoded_department = json_encode((string)$row['department'], $string_json_flags);
                if ($encoded_change_no === false) {
                    $encoded_change_no = '""';
                }
                if ($encoded_department === false) {
                    $encoded_department = '""';
                }
                $completion_onclick = 'openChangeControlTaskCompletionModal(' . (int)$row['id'] . ', ' . $encoded_change_no . ', ' . $encoded_department . ');';
                $action_buttons .= '<br><button type="button" class="btn btn-success btn-xs" style="margin-top:6px;" onclick="' . htmlspecialchars($completion_onclick, ENT_QUOTES, 'UTF-8') . '">Mark as Done</button>';
            }

            $data[] = array(
                'sr_no' => $i,
                'request_df' => $request_block,
                'dfupload' => $df_upload,
                'department' => strtoupper((string)$row['department']),
                'membername' => strtoupper(trim((string)$row['assignee_name']) !== '' ? (string)$row['assignee_name'] : '-'),
                'requesttype' => strtoupper((string)$row['request_type']) . '<br><span class="label label-warning" style="display:inline-block;margin-top:6px;">' . strtoupper((string)$row['priority']) . '</span>',
                'targetdate' => !empty($row['target_date']) && $row['target_date'] !== '0000-00-00' ? date('d-m-Y', strtotime($row['target_date'])) : '-',
                'delay' => $delay_label,
                'status' => '<span class="label ' . $status_class . '">' . str_replace('_', ' ', strtoupper((string)$row['status'])) . '</span>',
                'remarks' => $remarks,
                'updateprogress' => $action_buttons
            );
            $i++;
        }

        $results['iTotalRecords'] = count($data);
        $results['iTotalDisplayRecords'] = count($data);
        $results['aaData'] = $data;

        $json_output = json_encode($results, $json_flags);
        if ($json_output === false) {
            log_message('error', 'dashboard_assigned_tasks json_encode failed: ' . json_last_error_msg());
            $json_output = '{"sEcho":1,"iTotalRecords":0,"iTotalDisplayRecords":0,"aaData":[]}';
        }

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output($json_output);
        return;
    }

    public function dashboard_assigned_task_count()
    {
        if (!$this->change_model->module_ready()) {
            echo 0;
            return;
        }

        $current_user_id = $this->get_current_user_id();
        echo count($this->get_dashboard_change_task_rows($current_user_id));
    }

    public function dashboard_mark_done()
    {
        $response = array(
            'success' => false,
            'message' => 'Unable to update this task right now.'
        );

        if (!$this->change_model->module_ready()) {
            $response['message'] = 'DF Change Control tables are not ready. Please run the migration first.';
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $action_id = (int)$this->input->post('action_id');
        $assignee_remarks = trim((string)$this->input->post('assignee_remarks'));
        $execution_status = 'COMPLETED';

        $action = $this->change_model->get_department_action($action_id);
        if (empty($action)) {
            $response['message'] = 'Department execution record not found.';
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $current_user_id = $this->get_current_user_id();
        if (!$this->is_admin_user($current_user_id) && (int)$action['assigned_user_id'] !== $current_user_id) {
            $response['message'] = 'You are not authorized to update this execution task.';
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $result = $this->process_department_execution_update($action, $current_user_id, $execution_status, $assignee_remarks);
        $response['success'] = $result['success'];
        $response['message'] = $result['message'];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function process_department_execution_update($action, $current_user_id, $execution_status, $assignee_remarks)
    {
        $execution_status = strtoupper(trim((string)$execution_status));
        $assignee_remarks = trim((string)$assignee_remarks);

        if (empty($action) || empty($action['change_id'])) {
            return array('success' => false, 'message' => 'Department execution record not found.');
        }

        if (!in_array($execution_status, array('IN_PROGRESS', 'COMPLETED'), true) || $assignee_remarks === '') {
            return array('success' => false, 'message' => 'Please select a valid execution status and add remarks.');
        }

        if (!in_array((string)$action['status'], array('ASSIGNED', 'IN_PROGRESS'), true)) {
            return array('success' => false, 'message' => 'Only assigned or in-progress tasks can be updated from here.');
        }

        $now = date('Y-m-d H:i:s');
        $update_data = array(
            'status' => $execution_status,
            'assignee_remarks' => $assignee_remarks
        );

        if ($execution_status === 'COMPLETED') {
            $update_data['completed_on'] = $now;
            $update_data['completed_by'] = $current_user_id;
        }

        $this->db->trans_start();
        $this->db->where('id', (int)$action['id']);
        $this->db->update('df_change_control_departments', $update_data);

        $history_type = ($execution_status === 'COMPLETED') ? 'TASK_COMPLETED' : 'TASK_IN_PROGRESS';
        $this->change_model->add_history($action['change_id'], $action['id'], $current_user_id, 'ASSIGNEE', $history_type, $assignee_remarks);

        $status_update = $this->change_model->recompute_change_status($action['change_id']);
        if ($status_update['previous_status'] !== $status_update['current_status']) {
            $this->change_model->add_history($action['change_id'], $action['id'], $current_user_id, 'SYSTEM', 'REQUEST_STATUS_UPDATED', 'Master request status changed to ' . $status_update['current_status'] . '.');
        }

        if ($status_update['current_status'] === 'COMPLETED' && $status_update['previous_status'] !== 'COMPLETED') {
            $this->db->where('id', (int)$action['change_id']);
            $this->db->update('df_change_control', array(
                'closed_by' => $current_user_id,
                'closed_on' => $now
            ));
        }

        $change = $this->change_model->get_change_request($action['change_id']);
        $subject = $change['change_no'] . ' execution update';
        $notification_message = $change['change_no'] . ' has been updated to ' . str_replace('_', ' ', $execution_status) . '.';
        $email_body = $this->build_change_email(
            'A department execution update has been submitted.',
            $change,
            array(
                'department' => $action['department'],
                'status' => $execution_status,
                'target_date' => $action['target_date'],
                'assignee_name' => $this->get_current_user_name(),
                'assignee_remarks' => $assignee_remarks
            )
        );

        $notify_user_ids = array((int)$action['department_head_id'], (int)$change['created_by']);
        if ($status_update['current_status'] === 'COMPLETED') {
            $notify_user_ids = array_merge($notify_user_ids, $this->change_model->get_involved_user_ids($action['change_id']));
        }
        $this->notify_users($notify_user_ids, $subject, $notification_message, $email_body);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array('success' => false, 'message' => 'Unable to update execution status right now. Please try again.');
        }

        return array('success' => true, 'message' => 'Execution status updated successfully.');
    }

    private function upload_change_attachment($field_name)
    {
        if (empty($_FILES[$field_name]['name'])) {
            return '';
        }

        $folder = UPLOADPATH . 'df_change_control/';
        if (!is_dir($folder)) {
            @mkdir($folder, 0775, true);
        }

        $original_name = $_FILES[$field_name]['name'];
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $filename = 'df-change-' . time() . '-' . rand(1000, 9999);
        if ($extension !== '') {
            $filename .= '.' . strtolower($extension);
        }

        if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $folder . $filename)) {
            return $filename;
        }

        return '';
    }

    private function notify_users($user_ids, $subject, $notification_message, $email_message)
    {
        $user_ids = array_values(array_unique(array_filter(array_map('intval', (array)$user_ids))));
        if (empty($user_ids)) {
            return;
        }

        $users = $this->db->select('user_id, email')
            ->from('system_users')
            ->where_in('user_id', $user_ids)
            ->where('user_status', 1)
            ->get()
            ->result_array();

        foreach ($users as $user) {
            $this->db->insert('df_support_notifications', array(
                'user_id' => (int)$user['user_id'],
                'message' => $notification_message,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ));

            if (trim((string)$user['email']) !== '') {
                $this->db->insert('queue_emails', array(
                    'to_email' => trim((string)$user['email']),
                    'subject' => $subject,
                    'message' => $email_message,
                    'attachment' => '',
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ));
            }
        }
    }

    private function build_change_email($heading, $change, $action = array(), $extra_html = '')
    {
        $df_label = trim(($change['df_no'] ? $change['df_no'] : '-') . ' ' . ($change['df_description'] ? $change['df_description'] : ''));
        $view_link = page_url . 'Df_change_control/view/' . $change['id'];
        $attachment_link = '';

        if (!empty($change['attachment'])) {
            $attachment_link = '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Attachment</td><td style="padding:8px 10px; border:1px solid #d9d9d9;"><a href="' . site_http_root . 'image_bank/df_change_control/' . rawurlencode($change['attachment']) . '" target="_blank">Open supporting document</a></td></tr>';
        }

        $action_html = '';
        if (!empty($action)) {
            $action_html .= '<h3 style="font-size:16px; margin:20px 0 10px; color:#1f2937;">Department Action</h3>';
            $action_html .= '<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:14px;">';
            if (!empty($action['department'])) {
                $action_html .= '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Department</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . ucwords(strtolower($action['department'])) . '</td></tr>';
            }
            if (!empty($action['status'])) {
                $action_html .= '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Status</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . str_replace('_', ' ', $action['status']) . '</td></tr>';
            }
            if (!empty($action['planned_days'])) {
                $action_html .= '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Defined TAT</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . (int)$action['planned_days'] . ' day(s)</td></tr>';
            }
            if (!empty($action['target_date']) && $action['target_date'] !== '0000-00-00') {
                $action_html .= '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Target Date</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . date('d-M-Y', strtotime($action['target_date'])) . '</td></tr>';
            }
            if (!empty($action['assignee_name'])) {
                $action_html .= '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Assigned To</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . $action['assignee_name'] . '</td></tr>';
            }
            if (!empty($action['head_remarks'])) {
                $action_html .= '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">HOD Remarks</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . nl2br(htmlspecialchars($action['head_remarks'])) . '</td></tr>';
            }
            if (!empty($action['assignee_remarks'])) {
                $action_html .= '<tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Execution Remarks</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . nl2br(htmlspecialchars($action['assignee_remarks'])) . '</td></tr>';
            }
            $action_html .= '</table>';
        }

        return '
        <table cellpadding="0" cellspacing="0" width="100%" style="font-family:Arial, Helvetica, sans-serif; background:#f4f7fb; padding:20px 0;">
            <tr>
                <td>
                    <table align="center" cellpadding="0" cellspacing="0" width="760" style="max-width:760px; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dde3ec;">
                        <tr>
                            <td style="background:linear-gradient(135deg, #214f8a 0%, #0f172a 100%); color:#ffffff; padding:24px;">
                                <img src="' . assets_url . 'images/shubhampack.png" width="170" alt="Shubham Pack" style="display:block; margin-bottom:16px;">
                                <div style="font-size:24px; font-weight:bold;">DF Change Control</div>
                                <div style="font-size:14px; margin-top:6px;">' . $heading . '</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:24px;">
                                <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:14px;">
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Reference No</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . htmlspecialchars($change['change_no']) . '</td></tr>
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">DF</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . htmlspecialchars($df_label) . '</td></tr>
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Type</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . htmlspecialchars($change['request_type']) . '</td></tr>
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Category</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . htmlspecialchars($change['change_category']) . '</td></tr>
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Priority</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . htmlspecialchars($change['priority']) . '</td></tr>
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Title</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . htmlspecialchars($change['title']) . '</td></tr>
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Requester</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . htmlspecialchars($change['creator_name']) . '</td></tr>
                                    <tr><td style="padding:8px 10px; border:1px solid #d9d9d9; font-weight:bold;">Summary</td><td style="padding:8px 10px; border:1px solid #d9d9d9;">' . nl2br(htmlspecialchars($change['change_summary'])) . '</td></tr>
                                    ' . $attachment_link . '
                                </table>
                                ' . $action_html . '
                                ' . $extra_html . '
                                <div style="margin-top:24px;">
                                    <a href="' . $view_link . '" style="display:inline-block; padding:12px 20px; background:#214f8a; color:#ffffff; text-decoration:none; border-radius:6px; font-weight:bold;">Open DF Change Control</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';
    }

    private function get_user_info($user_id)
    {
        return $this->db->select('user_id, title, first_name, last_name, email')
            ->from('system_users')
            ->where('user_id', (int)$user_id)
            ->get()
            ->row_array();
    }

    private function format_user_name_from_array($user)
    {
        return $this->change_model->format_user_name(
            isset($user['title']) ? $user['title'] : '',
            isset($user['first_name']) ? $user['first_name'] : '',
            isset($user['last_name']) ? $user['last_name'] : ''
        );
    }

    private function get_current_user_id()
    {
        return (int)$this->session->userdata['logged_in']['user_id'];
    }

    private function get_current_department_id()
    {
        return (int)$this->session->userdata['logged_in']['department_id'];
    }

    private function get_current_user_name()
    {
        return $this->change_model->format_user_name(
            isset($this->session->userdata['logged_in']['title']) ? $this->session->userdata['logged_in']['title'] : '',
            isset($this->session->userdata['logged_in']['user_name']) ? $this->session->userdata['logged_in']['user_name'] : '',
            isset($this->session->userdata['logged_in']['last_name']) ? $this->session->userdata['logged_in']['last_name'] : ''
        );
    }

    private function get_module_navigation($user_id, $current_page)
    {
        $head_queue_count = 0;
        $assigned_queue_count = 0;
        $show_hod_menu = $this->is_admin_user($user_id);
        $can_view_dashboard = $this->can_access_change_dashboard($user_id);
        $can_raise_request = $this->can_raise_change_request($user_id);
        $has_explicit_dashboard_access = $this->is_admin_user($user_id) || $this->has_change_control_capability($user_id, array(42));

        if ($this->change_model->module_ready()) {
            $head_queue_count = count($this->change_model->get_head_queue($user_id));
            $assigned_queue_count = count($this->change_model->get_assigned_queue($user_id));

            if (!$show_hod_menu) {
                $show_hod_menu = $this->change_model->is_department_head($user_id);
            }
        }

        return array(
            'current_page' => (string)$current_page,
            'show_hod_menu' => $show_hod_menu,
            'head_queue_count' => $head_queue_count,
            'assigned_queue_count' => $assigned_queue_count,
            'can_view_dashboard' => $can_view_dashboard,
            'can_raise_request' => $can_raise_request,
            'assigned_only_access' => !$has_explicit_dashboard_access && !$can_raise_request && !$show_hod_menu && $assigned_queue_count > 0
        );
    }

    private function get_dashboard_change_task_rows($user_id)
    {
        $scope = $this->get_dashboard_change_task_scope($user_id);
        $assigned_rows = $this->change_model->get_dashboard_assigned_actions($user_id, $scope['admin_user_type'], $scope['department_ids']);
        $requester_rows = $this->change_model->get_dashboard_requester_actions($user_id, array('ASSIGNED', 'IN_PROGRESS'));

        $merged_rows = array();
        foreach (array_merge($assigned_rows, $requester_rows) as $row) {
            $row_id = isset($row['id']) ? (int)$row['id'] : 0;
            if ($row_id > 0) {
                $merged_rows[$row_id] = $row;
            }
        }

        $rows = array_values($merged_rows);
        usort($rows, function ($left, $right) {
            $left_overdue = !empty($left['target_date']) && $left['target_date'] !== '0000-00-00' && $left['target_date'] < date('Y-m-d') ? 0 : 1;
            $right_overdue = !empty($right['target_date']) && $right['target_date'] !== '0000-00-00' && $right['target_date'] < date('Y-m-d') ? 0 : 1;

            if ($left_overdue !== $right_overdue) {
                return $left_overdue - $right_overdue;
            }

            $left_target = !empty($left['target_date']) && $left['target_date'] !== '0000-00-00' ? $left['target_date'] : '9999-12-31';
            $right_target = !empty($right['target_date']) && $right['target_date'] !== '0000-00-00' ? $right['target_date'] : '9999-12-31';

            if ($left_target !== $right_target) {
                return strcmp($left_target, $right_target);
            }

            $left_created = !empty($left['change_created_on']) ? $left['change_created_on'] : '';
            $right_created = !empty($right['change_created_on']) ? $right['change_created_on'] : '';

            return strcmp($right_created, $left_created);
        });

        return $rows;
    }

    private function get_dashboard_change_task_scope($user_id)
    {
        $admin_user_type = $this->is_admin_user($user_id)
            ? 1
            : (int)(isset($this->session->userdata['logged_in']['adminuser']) ? $this->session->userdata['logged_in']['adminuser'] : 3);

        $department_ids = array();
        if ($admin_user_type === 2) {
            $department_ids = $this->get_team_lead_department_ids($user_id);
        }

        return array(
            'admin_user_type' => $admin_user_type,
            'department_ids' => $department_ids
        );
    }

    private function get_team_lead_department_ids($user_id)
    {
        $rows = $this->db->select('department_id')
            ->from('prestogroup_teams')
            ->where('team_leader', (int)$user_id)
            ->get()
            ->result_array();

        return array_values(array_unique(array_filter(array_map(function ($row) {
            return isset($row['department_id']) ? (int)$row['department_id'] : 0;
        }, $rows))));
    }

    private function can_raise_change_request($user_id)
    {
        if ($this->is_admin_user($user_id)) {
            return true;
        }

        return $this->has_change_control_capability($user_id, array(41));
    }

    private function can_access_change_dashboard($user_id)
    {
        if ($this->is_admin_user($user_id)) {
            return true;
        }

        if ($this->has_change_control_capability($user_id, array(42))) {
            return true;
        }

        if ($this->change_model->module_ready()) {
            if ($this->change_model->is_department_head($user_id)) {
                return true;
            }

            if (count($this->change_model->get_head_queue($user_id)) > 0) {
                return true;
            }

            if (count($this->change_model->get_assigned_queue($user_id)) > 0) {
                return true;
            }
        }

        return false;
    }

    private function has_change_control_capability($user_id, $submodule_ids)
    {
        $user_id = (int)$user_id;
        $submodule_ids = array_values(array_unique(array_filter(array_map('intval', (array)$submodule_ids))));
        if ($user_id <= 0 || empty($submodule_ids)) {
            return false;
        }

        $module_access = $this->db->select('id')
            ->from('module_access')
            ->where('role_id', $user_id)
            ->where('moduleid', 3)
            ->where('access', '1')
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        if (!$module_access) {
            return false;
        }

        return $this->db->select('submoduleid')
            ->from('module_capablity')
            ->where('role_id', $user_id)
            ->where('moduleid', 3)
            ->where_in('submoduleid', $submodule_ids)
            ->where('submodule_access', '1')
            ->limit(1)
            ->get()
            ->num_rows() > 0;
    }

    private function can_view_change_request($user_id, $change_id)
    {
        $user_id = (int)$user_id;
        $change_id = (int)$change_id;
        if ($user_id <= 0 || $change_id <= 0) {
            return false;
        }

        if ($this->is_admin_user($user_id) || $this->can_access_change_dashboard($user_id) || $this->can_raise_change_request($user_id)) {
            return true;
        }

        $is_creator = $this->db->select('id')
            ->from('df_change_control')
            ->where('id', $change_id)
            ->where('created_by', $user_id)
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        if ($is_creator) {
            return true;
        }

        return $this->db->select('id')
            ->from('df_change_control_departments')
            ->where('change_id', $change_id)
            ->group_start()
            ->where('department_head_id', $user_id)
            ->or_where('assigned_user_id', $user_id)
            ->group_end()
            ->limit(1)
            ->get()
            ->num_rows() > 0;
    }

    private function deny_change_control_access($redirect_url = '', $message = '')
    {
        $redirect_url = $redirect_url !== '' ? $redirect_url : page_url . 'Dashboard';
        $message = $message !== '' ? $message : 'You do not have permission to access DF Change Control.';
        $this->set_flash_message('danger', $message);
        redirect($redirect_url);
    }

    private function is_admin_user($user_id)
    {
        $admin_flag = isset($this->session->userdata['logged_in']['adminuser']) ? (int)$this->session->userdata['logged_in']['adminuser'] : 0;
        return $admin_flag === 1 || in_array((int)$user_id, $this->admin_user_ids, true);
    }

    private function set_flash_message($type, $message)
    {
        $this->session->set_flashdata('message', '<div class="alert alert-' . $type . '">' . $message . '</div>');
    }
}
