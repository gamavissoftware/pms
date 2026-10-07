<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Spares_execution extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $method = $this->router->fetch_method();
        $is_cron_method = ($method === 'cron_daily_alerts');

        if (!$is_cron_method && !$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('Spares_execution_model', 'spares_execution');
    }

    public function index()
    {
        redirect(page_url . 'Spares_execution/dashboard');
    }

    public function dashboard()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $filters = $this->get_dashboard_filters();
        $dashboard_filters = $this->build_dashboard_query_filters($filters, $user_id);
        $execution_filters = $dashboard_filters['execution_filters'];
        $queue_filters = $dashboard_filters['queue_filters'];
        $unscheduled_filters = $dashboard_filters['unscheduled_filters'];
        $execution_filters['limit'] = 100;

        $data['page_title'] = 'Spares Execution Dashboard';
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['filters'] = $filters;
        $data['status_options'] = array('Scheduled', 'In Progress', 'On Hold', 'Completed', 'Cancelled');
        $data['metrics'] = $this->spares_execution->get_execution_dashboard_metrics();
        $data['unscheduled_orders'] = $this->spares_execution->get_unscheduled_orders(25, $unscheduled_filters);
        $data['execution_orders'] = $this->spares_execution->get_execution_orders($execution_filters);
        $data['overdue_tasks'] = $this->spares_execution->get_overdue_tasks(15, $queue_filters);
        $data['pending_extensions'] = $this->spares_execution->get_pending_extension_queue(15, $queue_filters);

        $this->load->view('spares_execution/dashboard_view', $data);
    }

    public function export_csv($export_type = 'execution_orders')
    {
        $allowed_types = array('execution_orders', 'pending_setup', 'overdue_tasks', 'stale_tasks', 'pending_extensions');
        if (!in_array($export_type, $allowed_types, true)) {
            show_404();
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $filters = $this->get_dashboard_filters();
        $dashboard_filters = $this->build_dashboard_query_filters($filters, $user_id);
        $timestamp = date('Ymd_His');

        if ($export_type === 'pending_setup') {
            $rows = $this->spares_execution->get_unscheduled_orders(5000, $dashboard_filters['unscheduled_filters']);
            $headers = array('SO Reference', 'Customer', 'Opportunity No', 'PO No', 'Marketing', 'Order Date', 'Current Stage', 'Status', 'Order Value');
            $data_rows = array();

            foreach ($rows as $row) {
                $data_rows[] = array(
                    'SO-' . (int) $row->order_id,
                    $row->company_name,
                    $row->op_no,
                    $row->po_no,
                    $row->marketing_person_name,
                    $this->csv_date($row->order_date),
                    $row->current_progress_stage,
                    $row->spare_order_status,
                    !empty($row->order_value) ? number_format((float) $row->order_value, 2, '.', '') : '0.00',
                );
            }

            $this->output_csv_download('spares_execution_pending_setup_' . $timestamp . '.csv', $headers, $data_rows);
        }

        if ($export_type === 'overdue_tasks') {
            $rows = $this->spares_execution->get_overdue_tasks(5000, $dashboard_filters['queue_filters']);
            $headers = array('SO Reference', 'Customer', 'Opportunity No', 'Workflow', 'Task', 'Department', 'Owner', 'Task Status', 'Due Date', 'Delay Days', 'Commit Date', 'Priority');
            $data_rows = array();

            foreach ($rows as $row) {
                $data_rows[] = array(
                    'SO-' . (int) $row->order_id,
                    $row->company_name,
                    $row->op_no,
                    $row->workflow_type,
                    $row->task_name,
                    $row->department,
                    $row->owner_name,
                    $row->task_status,
                    $this->csv_date($row->planned_end_date),
                    (int) $row->delay_days,
                    $this->csv_date($row->commit_date),
                    $row->priority,
                );
            }

            $this->output_csv_download('spares_execution_overdue_tasks_' . $timestamp . '.csv', $headers, $data_rows);
        }

        if ($export_type === 'stale_tasks') {
            $stale_after_days = 3;
            $rows = $this->spares_execution->get_stale_tasks(5000, $dashboard_filters['queue_filters'], $stale_after_days);
            $headers = array('SO Reference', 'Customer', 'Opportunity No', 'Workflow', 'Task', 'Department', 'Owner', 'Task Status', 'Last Activity', 'Stale Days', 'Commit Date', 'Priority');
            $data_rows = array();

            foreach ($rows as $row) {
                $data_rows[] = array(
                    'SO-' . (int) $row->order_id,
                    $row->company_name,
                    $row->op_no,
                    $row->workflow_type,
                    $row->task_name,
                    $row->department,
                    $row->owner_name,
                    $row->task_status,
                    $this->csv_datetime($row->last_activity_at),
                    (int) $row->stale_days,
                    $this->csv_date($row->commit_date),
                    $row->priority,
                );
            }

            $this->output_csv_download('spares_execution_stale_tasks_' . $timestamp . '.csv', $headers, $data_rows);
        }

        if ($export_type === 'pending_extensions') {
            $rows = $this->spares_execution->get_pending_extension_queue(5000, $dashboard_filters['queue_filters']);
            $headers = array('SO Reference', 'Customer', 'Opportunity No', 'Workflow', 'Task', 'Current Due Date', 'Requested Due Date', 'Requested By', 'Requested On', 'Commit Date', 'Reason');
            $data_rows = array();

            foreach ($rows as $row) {
                $data_rows[] = array(
                    'SO-' . (int) $row->order_id,
                    $row->company_name,
                    $row->op_no,
                    $row->workflow_type,
                    $row->task_name,
                    $this->csv_date($row->current_due_date),
                    $this->csv_date($row->requested_due_date),
                    $row->requested_by_name,
                    $this->csv_datetime($row->requested_on),
                    $this->csv_date($row->commit_date),
                    $row->reason,
                );
            }

            $this->output_csv_download('spares_execution_pending_extensions_' . $timestamp . '.csv', $headers, $data_rows);
        }

        $rows = $this->spares_execution->get_execution_orders($dashboard_filters['execution_filters']);
        $headers = array('SO Reference', 'Customer', 'Opportunity No', 'PO No', 'Workflow', 'Marketing', 'Order Date', 'Commit Date', 'Priority', 'Execution Status', 'Progress %', 'Total Tasks', 'Completed Tasks', 'Cancelled Tasks', 'In Progress Tasks', 'Open Tasks', 'Pending Tasks', 'Blocked Tasks', 'On Hold Tasks', 'Overdue Tasks', 'Pending Extensions', 'Next Task', 'Next Task Status', 'Next Task Owner', 'Last Activity', 'Order Value');
        $data_rows = array();

        foreach ($rows as $row) {
            $data_rows[] = array(
                'SO-' . (int) $row->order_id,
                $row->company_name,
                $row->op_no,
                $row->po_no,
                $row->workflow_type,
                $row->marketing_person_name,
                $this->csv_date($row->order_date),
                $this->csv_date($row->commit_date),
                $row->priority,
                $row->execution_status,
                (int) $row->progress_percent,
                (int) $row->total_tasks,
                (int) $row->completed_tasks,
                (int) $row->cancelled_tasks,
                (int) $row->in_progress_tasks,
                (int) $row->open_tasks,
                (int) $row->pending_tasks,
                (int) $row->blocked_tasks,
                (int) $row->on_hold_tasks,
                (int) $row->overdue_tasks,
                (int) $row->pending_extensions,
                !empty($row->next_task_name) ? $row->next_task_name : '',
                !empty($row->next_task_status) ? $row->next_task_status : '',
                !empty($row->next_task_owner_name) ? $row->next_task_owner_name : '',
                $this->csv_datetime($row->order_last_activity_at),
                !empty($row->order_value) ? number_format((float) $row->order_value, 2, '.', '') : '0.00',
            );
        }

        $this->output_csv_download('spares_execution_orders_' . $timestamp . '.csv', $headers, $data_rows);
    }

    public function my_tasks()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $filters = $this->get_task_queue_filters(false, 200);

        $data['page_title'] = 'My Spares Tasks';
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['filters'] = $filters;
        $data['task_status_options'] = array('Pending', 'Open', 'In Progress', 'Completed', 'Blocked', 'On Hold', 'Cancelled');
        $data['queue_counts'] = $this->spares_execution->get_user_task_queue_counts($user_id);
        $data['tasks'] = $this->spares_execution->get_user_task_queue($user_id, $filters);
        $data['unread_execution_alerts'] = $this->spares_execution->count_user_execution_alerts($user_id, true);

        $this->load->view('spares_execution/my_tasks_view', $data);
    }

    public function export_my_tasks()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $filters = $this->get_task_queue_filters(false, 5000);
        $rows = $this->spares_execution->get_user_task_queue($user_id, $filters);
        $headers = array('SO Reference', 'Customer', 'Opportunity No', 'Workflow', 'Task', 'Department', 'Task Status', 'Progress %', 'Planned End', 'Commit Date', 'Priority', 'Execution Status', 'Dependency', 'Last Activity', 'Last Note');
        $data_rows = array();

        foreach ($rows as $row) {
            $dependency_label = !empty($row->dependency_task_name) ? $row->dependency_task_name : 'Ready to start';
            if (!empty($row->dependency_task_name) && (int) $row->dependency_blocked === 1) {
                $dependency_label .= ' (Blocked)';
            } elseif (!empty($row->dependency_task_name) && (int) $row->can_start_parallel === 1) {
                $dependency_label .= ' (Parallel allowed)';
            }

            $data_rows[] = array(
                'SO-' . (int) $row->order_id,
                $row->company_name,
                $row->op_no,
                $row->workflow_type,
                $row->task_name,
                $row->department,
                $row->task_status,
                (int) $row->completion_percent,
                $this->csv_date($row->planned_end_date),
                $this->csv_date($row->commit_date),
                $row->priority,
                $row->execution_status,
                $dependency_label,
                $this->csv_datetime($row->last_activity_at),
                !empty($row->last_remark) ? trim(preg_replace('/\s+/', ' ', $row->last_remark)) : '',
            );
        }

        $this->output_csv_download('spares_execution_my_tasks_' . date('Ymd_His') . '.csv', $headers, $data_rows);
    }

    public function department_tasks()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $department_id = (int) $this->session->userdata['logged_in']['department_id'];

        if (empty($department_id)) {
            $this->session->set_flashdata('error', 'Your user profile is not mapped to any department.');
            redirect(page_url . 'Spares_execution/dashboard');
        }

        $department = $this->spares_execution->get_department_by_id($department_id);
        if (!$department) {
            $this->session->set_flashdata('error', 'Department details were not found for your profile.');
            redirect(page_url . 'Spares_execution/dashboard');
        }

        $filters = $this->get_task_queue_filters(true, 250);

        $data['page_title'] = $department->department . ' Spares Tasks';
        $data['department'] = $department;
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['filters'] = $filters;
        $data['task_status_options'] = array('Pending', 'Open', 'In Progress', 'Completed', 'Blocked', 'On Hold', 'Cancelled');
        $data['department_users'] = $this->spares_execution->get_users($department_id);
        $data['queue_counts'] = $this->spares_execution->get_department_task_queue_counts($department_id);
        $data['tasks'] = $this->spares_execution->get_department_task_queue($department_id, $filters);
        $data['unread_execution_alerts'] = $this->spares_execution->count_user_execution_alerts($user_id, true);
        $is_admin = !empty($this->session->userdata['logged_in']['adminuser']) && (int) $this->session->userdata['logged_in']['adminuser'] === 1;
        $has_mrp_permission = $is_admin || $this->spares_execution->can_user_access_spares_submodule($user_id, Spares_execution_model::SPARES_EXECUTION_MRP_SUBMODULE, true);
        $department_has_mrp_work = $this->spares_execution->is_ppc_department_name($department->department) || $this->spares_execution->department_has_mrp_tasks($department_id);
        $data['show_mrp_window'] = $has_mrp_permission && $department_has_mrp_work;
        $data['mrp_orders'] = $data['show_mrp_window'] ? $this->spares_execution->get_department_mrp_queue($department_id, 25) : array();

        $this->load->view('spares_execution/department_tasks_view', $data);
    }

    public function export_department_tasks()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $department_id = (int) $this->session->userdata['logged_in']['department_id'];

        if (empty($department_id)) {
            $this->session->set_flashdata('error', 'Your user profile is not mapped to any department.');
            redirect(page_url . 'Spares_execution/dashboard');
        }

        $department = $this->spares_execution->get_department_by_id($department_id);
        if (!$department) {
            $this->session->set_flashdata('error', 'Department details were not found for your profile.');
            redirect(page_url . 'Spares_execution/dashboard');
        }

        $filters = $this->get_task_queue_filters(true, 5000);
        $rows = $this->spares_execution->get_department_task_queue($department_id, $filters);
        $headers = array('SO Reference', 'Customer', 'Opportunity No', 'Workflow', 'Task', 'Owner', 'Task Status', 'Progress %', 'Planned End', 'Commit Date', 'Priority', 'Execution Status', 'Dependency', 'Last Activity', 'Last Note');
        $data_rows = array();

        foreach ($rows as $row) {
            $dependency_label = !empty($row->dependency_task_name) ? $row->dependency_task_name : 'Ready to start';
            if (!empty($row->dependency_task_name) && (int) $row->dependency_blocked === 1) {
                $dependency_label .= ' (Blocked)';
            } elseif (!empty($row->dependency_task_name) && (int) $row->can_start_parallel === 1) {
                $dependency_label .= ' (Parallel allowed)';
            }

            $data_rows[] = array(
                'SO-' . (int) $row->order_id,
                $row->company_name,
                $row->op_no,
                $row->workflow_type,
                $row->task_name,
                !empty($row->owner_name) ? $row->owner_name : 'Unassigned',
                $row->task_status,
                (int) $row->completion_percent,
                $this->csv_date($row->planned_end_date),
                $this->csv_date($row->commit_date),
                $row->priority,
                $row->execution_status,
                $dependency_label,
                $this->csv_datetime($row->last_activity_at),
                !empty($row->last_remark) ? trim(preg_replace('/\s+/', ' ', $row->last_remark)) : '',
            );
        }

        $filename = 'spares_execution_department_tasks_' . preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($department->department)) . '_' . date('Ymd_His') . '.csv';
        $this->output_csv_download($filename, $headers, $data_rows);
    }

    public function alerts()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $show = $this->input->get('show', true) === 'all' ? 'all' : 'unread';

        $data['page_title'] = 'Spares Execution Alerts';
        $data['show'] = $show;
        $data['alerts'] = $this->spares_execution->get_user_execution_alerts($user_id, $show !== 'all', 200);
        $data['unread_execution_alerts'] = $this->spares_execution->count_user_execution_alerts($user_id, true);

        $this->load->view('spares_execution/alerts_view', $data);
    }

    public function mark_alert_read($notification_id)
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $marked = $this->spares_execution->mark_user_execution_alert_read((int) $notification_id, $user_id);

        if ($marked) {
            $this->session->set_flashdata('success', 'Execution alert marked as read.');
        } else {
            $this->session->set_flashdata('error', 'Unable to mark execution alert as read.');
        }

        $redirect_to = $this->input->get('redirect', true) === 'dashboard' ? 'dashboard' : 'alerts';
        if ($redirect_to === 'dashboard') {
            redirect(page_url . 'Spares_execution/dashboard');
        }

        $query = $this->input->get('show', true) === 'all' ? '?show=all' : '';
        redirect(page_url . 'Spares_execution/alerts' . $query);
    }

    public function mark_all_alerts_read()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $marked_count = $this->spares_execution->mark_all_user_execution_alerts_read($user_id);

        if ($marked_count > 0) {
            $this->session->set_flashdata('success', $marked_count . ' execution alert(s) marked as read.');
        } else {
            $this->session->set_flashdata('error', 'No unread execution alerts were available to mark as read.');
        }

        $query = $this->input->get('show', true) === 'all' ? '?show=all' : '';
        redirect(page_url . 'Spares_execution/alerts' . $query);
    }

    public function send_alerts()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $summary = $this->spares_execution->dispatch_execution_alerts($user_id);
        $this->spares_execution->log_alert_dispatch_run($this->build_alert_dispatch_run_payload(
            'MANUAL',
            $summary,
            $user_id,
            'Triggered from Spares execution dashboard. Stale task alerts: ' . (int) $summary->stale_sent . '.'
        ));

        if ((int) $summary->sent > 0) {
            $message = $summary->sent . ' alert(s) sent. Pending setup: ' . $summary->unscheduled_sent . ', overdue: ' . $summary->overdue_sent . ', stale: ' . $summary->stale_sent . ', extensions: ' . $summary->pending_extension_sent . '.';
            if ((int) $summary->skipped > 0) {
                $message .= ' ' . $summary->skipped . ' duplicate alert(s) were skipped.';
            }
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'No new alerts were sent. Existing alerts for today were already generated or no alert conditions matched.');
        }

        redirect(page_url . 'Spares_execution/dashboard');
    }

    public function cron_daily_alerts()
    {
        $is_cli = $this->input->is_cli_request();
        $web_key = trim((string) $this->input->get('key', true));
        $expected_key = $this->get_execution_cron_key();

        if (!$is_cli) {
            if (!empty($expected_key) && $web_key !== $expected_key) {
                show_error('Unauthorized cron access.', 403);
            }
        }

        $summary = $this->spares_execution->dispatch_execution_alerts(0);
        $run_mode = $is_cli ? 'CRON_CLI' : 'CRON_WEB';
        $message = 'Spares execution alerts processed. Sent=' . (int) $summary->sent . ', skipped=' . (int) $summary->skipped . ', pending_setup=' . (int) $summary->unscheduled_sent . ', overdue=' . (int) $summary->overdue_sent . ', stale=' . (int) $summary->stale_sent . ', pending_extensions=' . (int) $summary->pending_extension_sent . '.';

        $this->spares_execution->log_alert_dispatch_run($this->build_alert_dispatch_run_payload(
            $run_mode,
            $summary,
            0,
            ($is_cli ? 'Triggered by CLI cron.' : 'Triggered by secured web cron.') . ' Stale task alerts: ' . (int) $summary->stale_sent . '.'
        ));

        log_message('info', $message);

        $this->output
            ->set_content_type('text/plain')
            ->set_output($message);
    }

    public function task_master($task_master_id = null)
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $data['page_title'] = 'Spares Execution Task Master';
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['quotation_workflows'] = $this->spares_execution->get_quotation_workflow_types();
        $data['departments'] = $this->spares_execution->get_departments();
        $data['tasks'] = $this->spares_execution->get_task_master();
        $data['edit_task'] = !empty($task_master_id) ? $this->spares_execution->get_task_master_by_id($task_master_id) : null;

        $this->load->view('spares_execution/task_master_view', $data);
    }

    public function save_task_master()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access($user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $this->form_validation->set_rules('workflow_type', 'Quotation Type', 'required|trim');
        $this->form_validation->set_rules('task_code', 'Task Code', 'required|trim');
        $this->form_validation->set_rules('task_name', 'Task Name', 'required|trim');
        $this->form_validation->set_rules('sequence_no', 'Sequence', 'required|integer');
        $this->form_validation->set_rules('sla_days', 'TAT Days', 'required|integer');

        $task_master_id = $this->input->post('task_master_id');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(page_url . 'Spares_execution/task_master' . (!empty($task_master_id) ? '/' . $task_master_id : ''));
        }

        $user_id = $this->session->userdata['logged_in']['user_id'];
        $now = date('Y-m-d H:i:s');
        $workflow_type = $this->input->post('workflow_type', true);

        if (!$this->spares_execution->is_valid_workflow_type($workflow_type)) {
            $this->session->set_flashdata('error', 'Please select a valid quotation type.');
            redirect(page_url . 'Spares_execution/task_master' . (!empty($task_master_id) ? '/' . $task_master_id : ''));
        }

        $data = array(
            'workflow_type' => $workflow_type,
            'task_code' => strtoupper(trim($this->input->post('task_code', true))),
            'task_name' => trim($this->input->post('task_name', true)),
            'department_id' => $this->input->post('department_id') ? (int) $this->input->post('department_id') : null,
            'default_owner_id' => $this->input->post('default_owner_id') ? (int) $this->input->post('default_owner_id') : null,
            'sequence_no' => (int) $this->input->post('sequence_no'),
            'sla_days' => max(1, (int) $this->input->post('sla_days')),
            'depends_on_code' => trim($this->input->post('depends_on_code', true)) ?: null,
            'can_start_parallel' => $this->input->post('can_start_parallel') ? 1 : 0,
            'extension_allowed' => $this->input->post('extension_allowed') ? 1 : 0,
            'is_active' => $this->input->post('is_active') ? (int) $this->input->post('is_active') : 1,
        );

        $assignment_error = $this->validate_department_owner_selection($data['department_id'], $data['default_owner_id'], 'Task master');
        if ($assignment_error !== '') {
            $this->session->set_flashdata('error', $assignment_error);
            redirect(page_url . 'Spares_execution/task_master' . (!empty($task_master_id) ? '/' . $task_master_id : ''));
        }

        if (!empty($task_master_id)) {
            $data['updated_by'] = $user_id;
            $data['updated_at'] = $now;
        } else {
            $data['created_by'] = $user_id;
            $data['created_at'] = $now;
        }

        $saved = $this->spares_execution->save_task_master($data, !empty($task_master_id) ? (int) $task_master_id : null);

        if ($saved) {
            $this->session->set_flashdata('success', 'Task master saved successfully.');
        } else {
            $this->session->set_flashdata('error', 'Unable to save task master.');
        }

        redirect(page_url . 'Spares_execution/task_master');
    }

    public function toggle_task_master($task_master_id, $status)
    {
        $user_id = $this->session->userdata['logged_in']['user_id'];
        if ($this->spares_execution->user_has_only_spares_execution_mrp_access((int) $user_id)) {
            redirect(page_url . 'Spares_execution/mrp_shortages');
        }

        $this->spares_execution->set_task_master_status((int) $task_master_id, (int) $status, $user_id);
        $this->session->set_flashdata('success', 'Task master status updated.');
        redirect(page_url . 'Spares_execution/task_master');
    }

    public function get_template_tasks()
    {
        $workflow_type = $this->input->get('workflow_type', true);
        $commit_date = $this->input->get('commit_date', true);

        $tasks = array();
        if ($this->spares_execution->is_valid_workflow_type($workflow_type)) {
            $tasks = $this->spares_execution->build_schedule_preview($workflow_type, $commit_date);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => !empty($tasks) ? 1 : 0,
                'tasks' => $tasks,
            )));
    }

    public function get_department_users()
    {
        $department_id = (int) $this->input->get('department_id', true);
        $users = $department_id > 0 ? $this->spares_execution->get_users($department_id) : array();
        $payload = array();

        foreach ($users as $user) {
            $payload[] = array(
                'user_id' => (int) $user->user_id,
                'name' => trim($user->title . ' ' . $user->first_name . ' ' . $user->last_name),
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => !empty($payload) ? 1 : 0,
                'users' => $payload,
            )));
    }

    public function schedule($order_id)
    {
        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if ($execution_order) {
            redirect(page_url . 'Spares_execution/order/' . (int) $order_id);
        }

        $data['page_title'] = 'Schedule Spares Order Tasks';
        $data['order_snapshot'] = $order_snapshot;
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['default_workflow_type'] = $this->spares_execution->map_quote_fields_to_workflow(
            $order_snapshot->quotation_type ?? '',
            $order_snapshot->custom_engg_type ?? '',
            $order_snapshot->execution_workflow_type ?? ''
        );
        $data['departments'] = $this->spares_execution->get_departments();

        $this->load->view('spares_execution/schedule_order_view', $data);
    }

    public function edit_schedule($order_id)
    {
        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . (int) $order_id);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if (!$this->spares_execution->can_user_manage_execution_order((int) $execution_order->execution_order_id, $user_id)) {
            $this->session->set_flashdata('error', 'You are not authorized to edit this execution schedule.');
            redirect(page_url . 'Spares_execution/order/' . (int) $order_id);
        }

        $data['page_title'] = 'Edit Spares Execution Schedule';
        $data['order_snapshot'] = $order_snapshot;
        $data['execution_order'] = $execution_order;
        $data['tasks'] = $this->spares_execution->get_execution_tasks((int) $execution_order->execution_order_id);
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['default_workflow_type'] = $execution_order->workflow_type;
        $data['departments'] = $this->spares_execution->get_departments();

        $this->load->view('spares_execution/schedule_order_view', $data);
    }

    public function save_schedule()
    {
        $this->form_validation->set_rules('order_id', 'Order', 'required|integer');
        $this->form_validation->set_rules('workflow_type', 'Quotation Type', 'required|trim');
        $this->form_validation->set_rules('commit_date', 'Commit Date', 'required|trim');
        $this->form_validation->set_rules('priority', 'Priority', 'required|trim');

        $order_id = (int) $this->input->post('order_id');
        $order_snapshot = $this->spares_execution->get_order_snapshot($order_id);

        if (!$order_snapshot) {
            show_404();
        }

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(page_url . 'Spares_execution/schedule/' . $order_id);
        }

        $commit_date = trim((string) $this->input->post('commit_date', true));
        $workflow_type = $this->input->post('workflow_type', true);
        if (!$this->is_valid_iso_date($commit_date)) {
            $this->session->set_flashdata('error', 'Please enter a valid commit date.');
            redirect(page_url . 'Spares_execution/schedule/' . $order_id);
        }

        if (!$this->spares_execution->is_valid_workflow_type($workflow_type)) {
            $this->session->set_flashdata('error', 'Please select a valid quotation type.');
            redirect(page_url . 'Spares_execution/schedule/' . $order_id);
        }

        $user_id = $this->session->userdata['logged_in']['user_id'];
        $now = date('Y-m-d H:i:s');

        $task_master_ids = $this->input->post('task_master_id');
        $planned_start_date = $this->input->post('planned_start_date');
        $planned_end_date = $this->input->post('planned_end_date');

        $task_rows = array();

        if (!empty($task_master_ids) && is_array($task_master_ids)) {
            foreach ($task_master_ids as $index => $task_master_id) {
                $task_master = !empty($task_master_id) ? $this->spares_execution->get_task_master_by_id((int) $task_master_id) : null;
                if (!$task_master || $task_master->workflow_type !== $workflow_type) {
                    continue;
                }

                $task_rows[] = array(
                    'task_master_id' => (int) $task_master->task_master_id,
                    'task_code' => $task_master->task_code,
                    'task_name' => $task_master->task_name,
                    'department_id' => !empty($task_master->department_id) ? (int) $task_master->department_id : null,
                    'assigned_to' => !empty($task_master->default_owner_id) ? (int) $task_master->default_owner_id : null,
                    'sequence_no' => !empty($task_master->sequence_no) ? (int) $task_master->sequence_no : 0,
                    'depends_on_code' => !empty($task_master->depends_on_code) ? trim($task_master->depends_on_code) : null,
                    'can_start_parallel' => !empty($task_master->can_start_parallel) ? 1 : 0,
                    'planned_start_date' => !empty($planned_start_date[$index]) ? $planned_start_date[$index] : null,
                    'planned_end_date' => !empty($planned_end_date[$index]) ? $planned_end_date[$index] : null,
                    'created_by' => $user_id,
                    'created_at' => $now,
                );
            }
        }

        if (empty($task_rows)) {
            $this->session->set_flashdata('error', 'Please load and save at least one task.');
            redirect(page_url . 'Spares_execution/schedule/' . $order_id);
        }

        $task_row_error = $this->validate_schedule_task_rows($task_rows, $commit_date);
        if ($task_row_error !== '') {
            $this->session->set_flashdata('error', $task_row_error);
            redirect(page_url . 'Spares_execution/schedule/' . $order_id);
        }

        $execution_data = array(
            'order_id' => $order_id,
            'workflow_type' => $workflow_type,
            'commit_date' => $commit_date,
            'priority' => $this->input->post('priority', true),
            'execution_status' => 'Scheduled',
            'marketing_owner_id' => (int) $order_snapshot->marketing_person_id,
            'schedule_notes' => $this->input->post('schedule_notes', true),
            'created_by' => $user_id,
            'created_at' => $now,
        );

        $created = $this->spares_execution->create_execution_schedule($execution_data, $task_rows);

        if ($created) {
            $this->session->set_flashdata('success', 'Execution schedule created successfully.');
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $this->session->set_flashdata('error', 'Unable to create execution schedule.');
        redirect(page_url . 'Spares_execution/schedule/' . $order_id);
    }

    public function update_schedule()
    {
        $this->form_validation->set_rules('order_id', 'Order', 'required|integer');
        $this->form_validation->set_rules('execution_order_id', 'Execution Order', 'required|integer');
        $this->form_validation->set_rules('commit_date', 'Commit Date', 'required|trim');
        $this->form_validation->set_rules('priority', 'Priority', 'required|trim');

        $order_id = (int) $this->input->post('order_id');
        $execution_order_id = (int) $this->input->post('execution_order_id');
        $order_snapshot = $this->spares_execution->get_order_snapshot($order_id);
        $execution_order = $this->spares_execution->get_execution_order($execution_order_id);

        if (!$order_snapshot || !$execution_order || (int) $execution_order->order_id !== $order_id) {
            show_404();
        }

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(page_url . 'Spares_execution/edit_schedule/' . $order_id);
        }

        $commit_date = trim((string) $this->input->post('commit_date', true));
        if (!$this->is_valid_iso_date($commit_date)) {
            $this->session->set_flashdata('error', 'Please enter a valid commit date.');
            redirect(page_url . 'Spares_execution/edit_schedule/' . $order_id);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if (!$this->spares_execution->can_user_manage_execution_order($execution_order_id, $user_id)) {
            $this->session->set_flashdata('error', 'You are not authorized to update this execution schedule.');
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $task_rows = $this->collect_schedule_task_rows(true);

        if (empty($task_rows)) {
            $this->session->set_flashdata('error', 'Please keep at least one planned task in the execution schedule.');
            redirect(page_url . 'Spares_execution/edit_schedule/' . $order_id);
        }

        $task_row_error = $this->validate_schedule_task_rows($task_rows, $commit_date);
        if ($task_row_error !== '') {
            $this->session->set_flashdata('error', $task_row_error);
            redirect(page_url . 'Spares_execution/edit_schedule/' . $order_id);
        }

        $execution_data = array(
            'commit_date' => $commit_date,
            'priority' => $this->input->post('priority', true),
            'schedule_notes' => $this->input->post('schedule_notes', true),
        );

        $updated = $this->spares_execution->update_execution_schedule($execution_order_id, $execution_data, $task_rows, $user_id);

        if ($updated) {
            $this->session->set_flashdata('success', 'Execution schedule updated successfully.');
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $this->session->set_flashdata('error', 'Unable to update execution schedule.');
        redirect(page_url . 'Spares_execution/edit_schedule/' . $order_id);
    }

    public function order($order_id)
    {
        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . (int) $order_id);
        }

        $user_id = $this->session->userdata['logged_in']['user_id'];

        $data['page_title'] = 'Spares Execution Tracker';
        $data['order_snapshot'] = $order_snapshot;
        $data['execution_order'] = $execution_order;
        $data['tasks'] = $this->spares_execution->get_execution_tasks((int) $execution_order->execution_order_id);
        $data['task_counters'] = $this->spares_execution->get_task_counters((int) $execution_order->execution_order_id);
        $data['extension_requests'] = $this->spares_execution->get_extension_requests((int) $execution_order->execution_order_id);
        $data['updates'] = $this->spares_execution->get_task_updates((int) $execution_order->execution_order_id);
        $data['can_review_extensions'] = ((int) $execution_order->marketing_owner_id === (int) $user_id) || ((int) $execution_order->created_by === (int) $user_id);
        $data['can_manage_schedule'] = $this->spares_execution->can_user_manage_execution_order((int) $execution_order->execution_order_id, $user_id);
        $data['focus_task_id'] = (int) $this->input->get('focus_task_id');
        $data['focus_extension_request_id'] = (int) $this->input->get('focus_extension_request_id');
        $data['focus_update_id'] = (int) $this->input->get('focus_update_id');

        $this->load->view('spares_execution/order_tasks_view', $data);
    }

    public function sf_form($order_id)
    {
        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . (int) $order_id);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $sf_form = $this->spares_execution->get_sf_form_by_order((int) $order_id);
        $items = $sf_form ? $this->spares_execution->get_sf_form_items((int) $sf_form->sf_form_id) : array();

        if (!$sf_form) {
            $sf_form = $this->build_default_sf_form($order_snapshot, $execution_order, $user_id);
            $items = $this->build_default_sf_items($order_snapshot, $execution_order);
        }

        $data['page_title'] = 'Spares SF Form';
        $data['order_snapshot'] = $order_snapshot;
        $data['execution_order'] = $execution_order;
        $data['sf_form'] = $sf_form;
        $data['items'] = $items;
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['prepared_by_name'] = $this->get_logged_in_user_name();
        $data['can_manage_schedule'] = $this->spares_execution->can_user_manage_execution_order((int) $execution_order->execution_order_id, $user_id);

        $this->load->view('spares_execution/sf_form_view', $data);
    }

    public function save_sf_form()
    {
        $order_id = (int) $this->input->post('order_id');
        $order_snapshot = $this->spares_execution->get_order_snapshot($order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order($order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . $order_id);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if (!$this->spares_execution->can_user_manage_execution_order((int) $execution_order->execution_order_id, $user_id)) {
            $this->session->set_flashdata('error', 'You are not authorized to update this SF form.');
            redirect(page_url . 'Spares_execution/sf_form/' . $order_id);
        }

        $release = $this->input->post('sf_action', true) === 'release';
        $release_date = trim((string) $this->input->post('release_date', true));

        if (!$this->is_valid_iso_date($release_date)) {
            $this->session->set_flashdata('error', 'Please select a valid SF release date.');
            redirect(page_url . 'Spares_execution/sf_form/' . $order_id);
        }

        $po_date = $this->normalize_optional_iso_date($this->input->post('po_date', true));
        if ($po_date === false) {
            $this->session->set_flashdata('error', 'Please select a valid PO date.');
            redirect(page_url . 'Spares_execution/sf_form/' . $order_id);
        }

        $items = $this->collect_sf_item_rows();
        if (empty($items)) {
            $this->session->set_flashdata('error', 'Please add at least one spare item in SF details.');
            redirect(page_url . 'Spares_execution/sf_form/' . $order_id);
        }

        $date_error = $this->validate_sf_item_dates($items);
        if ($date_error !== '') {
            $this->session->set_flashdata('error', $date_error);
            redirect(page_url . 'Spares_execution/sf_form/' . $order_id);
        }

        $sf_no = trim((string) $this->input->post('sf_no', true));
        if ($sf_no === '') {
            $sf_no = $this->spares_execution->build_sf_number($order_id);
        }

        $sf_data = array(
            'order_id' => $order_id,
            'execution_order_id' => (int) $execution_order->execution_order_id,
            'sf_no' => $sf_no,
            'release_date' => $release_date,
            'prepared_by' => $user_id,
            'scope_of_supply' => $this->input->post('scope_of_supply', true),
            'document_requirement' => $this->input->post('document_requirement', true),
            'packing_duration_days' => max(0, (int) $this->input->post('packing_duration_days')),
            'dispatch_duration_days' => max(0, (int) $this->input->post('dispatch_duration_days')),
            'dispatch_mode' => $this->input->post('dispatch_mode', true),
            'order_description' => $this->input->post('order_description', true),
            'po_no' => $this->input->post('po_no', true),
            'po_date' => $po_date,
            'po_revision' => $this->input->post('po_revision', true),
            'quotation_ref' => $this->input->post('quotation_ref', true),
            'quotation_revision' => $this->input->post('quotation_revision', true),
            'machine_description' => $this->input->post('machine_description', true),
            'machine_df' => $this->input->post('machine_df', true),
            'no_of_tracks' => $this->input->post('no_of_tracks', true),
            'product_to_be_packed' => $this->input->post('product_to_be_packed', true),
            'quantity_to_be_packed' => $this->input->post('quantity_to_be_packed', true),
            'pouch_size_type' => $this->input->post('pouch_size_type', true),
            'no_of_axis' => $this->input->post('no_of_axis', true),
            'plc_make' => $this->input->post('plc_make', true),
            'specification_note' => $this->input->post('specification_note', true),
        );

        $saved = $this->spares_execution->save_sf_form($sf_data, $items, $user_id, $release);
        if ($saved) {
            $this->session->set_flashdata('success', $release ? 'SF form released successfully.' : 'SF form saved as draft.');
            redirect(page_url . 'Spares_execution/sf_form/' . $order_id);
        }

        $this->session->set_flashdata('error', 'Unable to save SF form. Please make sure the SF form migration has been run.');
        redirect(page_url . 'Spares_execution/sf_form/' . $order_id);
    }

    public function mrp_shortages($order_id = null)
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];

        if (empty($order_id)) {
            $is_admin = !empty($this->session->userdata['logged_in']['adminuser']) && (int) $this->session->userdata['logged_in']['adminuser'] === 1;
            if (!$is_admin && !$this->spares_execution->can_user_access_spares_submodule($user_id, Spares_execution_model::SPARES_EXECUTION_MRP_SUBMODULE, true)) {
                $this->session->set_flashdata('error', 'You are not authorized to open the PPC MRP shortage queue.');
                redirect(page_url . 'Spares_execution/department_tasks');
            }

            $filters = $this->get_mrp_queue_filters(250);
            $data['page_title'] = 'PPC MRP Shortage Report';
            $data['workflows'] = $this->spares_execution->get_workflow_types();
            $data['filters'] = $filters;
            $data['orders'] = $this->spares_execution->get_mrp_report_orders($filters['limit'], $filters);
            $data['can_run_mrp'] = $this->spares_execution->can_user_run_mrp_queue($user_id);

            $this->load->view('spares_execution/mrp_shortages_view', $data);
            return;
        }

        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . (int) $order_id);
        }

        $sf_form = $this->spares_execution->get_sf_form_by_order((int) $order_id);
        $latest_run = $this->spares_execution->get_latest_mrp_run((int) $order_id);
        $latest_run_items = $latest_run ? $this->spares_execution->get_mrp_run_items((int) $latest_run->mrp_run_id) : array();
        $preview = ($sf_form && $sf_form->form_status === 'Released')
            ? $this->spares_execution->build_mrp_shortage_preview((int) $order_id)
            : array('items' => array(), 'summary' => (object) array(
                'total_items' => 0,
                'shortage_items' => 0,
                'missing_master_items' => 0,
                'total_required_qty' => 0,
                'total_available_qty' => 0,
                'total_shortage_qty' => 0,
            ));

        $data['page_title'] = 'PPC MRP Shortage Report';
        $data['order_snapshot'] = $order_snapshot;
        $data['execution_order'] = $execution_order;
        $data['sf_form'] = $sf_form;
        $data['latest_run'] = $latest_run;
        $data['latest_run_items'] = $latest_run_items;
        $data['preview'] = $preview;
        $data['workflows'] = $this->spares_execution->get_workflow_types();
        $data['can_run_mrp'] = $this->spares_execution->can_user_run_mrp((int) $execution_order->execution_order_id, $user_id);

        $this->load->view('spares_execution/mrp_shortage_detail_view', $data);
    }

    public function run_mrp($order_id)
    {
        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . (int) $order_id);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if (!$this->spares_execution->can_user_run_mrp((int) $execution_order->execution_order_id, $user_id)) {
            $this->session->set_flashdata('error', 'You are not authorized to run MRP for this SF.');
            redirect(page_url . 'Spares_execution/mrp_shortages/' . (int) $order_id);
        }

        $sf_form = $this->spares_execution->get_sf_form_by_order((int) $order_id);
        if (!$sf_form || $sf_form->form_status !== 'Released') {
            $this->session->set_flashdata('error', 'Please release the SF form before running MRP.');
            redirect(page_url . 'Spares_execution/mrp_shortages/' . (int) $order_id);
        }

        $mrp_run_id = $this->spares_execution->save_mrp_run((int) $order_id, $user_id);
        if ($mrp_run_id) {
            $this->session->set_flashdata('success', 'MRP run completed and shortage report generated.');
            redirect(page_url . 'Spares_execution/mrp_shortages/' . (int) $order_id);
        }

        $this->session->set_flashdata('error', 'Unable to run MRP. Please make sure the MRP shortage migration has been run.');
        redirect(page_url . 'Spares_execution/mrp_shortages/' . (int) $order_id);
    }

    public function export_mrp_shortages()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $is_admin = !empty($this->session->userdata['logged_in']['adminuser']) && (int) $this->session->userdata['logged_in']['adminuser'] === 1;
        if (!$is_admin && !$this->spares_execution->can_user_access_spares_submodule($user_id, Spares_execution_model::SPARES_EXECUTION_MRP_SUBMODULE, true)) {
            $this->session->set_flashdata('error', 'You are not authorized to export the PPC MRP shortage queue.');
            redirect(page_url . 'Spares_execution/department_tasks');
        }

        $filters = $this->get_mrp_queue_filters(5000);
        $rows = $this->spares_execution->get_mrp_report_orders($filters['limit'], $filters);
        $headers = array('SO Reference', 'Customer', 'Opportunity No', 'PO No', 'Workflow', 'SF No', 'SF Release Date', 'MRP Status', 'Last MRP Run', 'BOM Items', 'Shortage Items', 'Missing Master Items', 'Total Shortage Qty', 'Commit Date', 'Priority');
        $data_rows = array();

        foreach ($rows as $row) {
            $has_shortage = !empty($row->shortage_items) && (int) $row->shortage_items > 0;
            $has_missing = !empty($row->missing_master_items) && (int) $row->missing_master_items > 0;
            $mrp_status = empty($row->mrp_run_id) ? 'Pending MRP' : ($has_shortage || $has_missing ? 'Shortage' : 'Available');

            $data_rows[] = array(
                'SO-' . (int) $row->order_id,
                $row->company_name,
                $row->op_no,
                $row->po_no,
                $row->workflow_type,
                $row->sf_no,
                $this->csv_date($row->release_date),
                $mrp_status,
                $this->csv_datetime($row->latest_mrp_run_at),
                (int) $row->total_items,
                (int) $row->shortage_items,
                (int) $row->missing_master_items,
                number_format((float) $row->total_shortage_qty, 3, '.', ''),
                $this->csv_date($row->commit_date),
                $row->priority,
            );
        }

        $this->output_csv_download('spares_execution_mrp_shortages_' . date('Ymd_His') . '.csv', $headers, $data_rows);
    }

    public function gantt($order_id)
    {
        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . (int) $order_id);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];

        $data['page_title'] = 'Spares Execution Gantt';
        $data['order_snapshot'] = $order_snapshot;
        $data['execution_order'] = $execution_order;
        $data['tasks'] = $this->spares_execution->get_execution_tasks((int) $execution_order->execution_order_id);
        $data['task_counters'] = $this->spares_execution->get_task_counters((int) $execution_order->execution_order_id);
        $data['can_manage_schedule'] = $this->spares_execution->can_user_manage_execution_order((int) $execution_order->execution_order_id, $user_id);

        $this->load->view('spares_execution/gantt_view', $data);
    }

    public function export_order_tracker($order_id)
    {
        $order_snapshot = $this->spares_execution->get_order_snapshot((int) $order_id);
        if (!$order_snapshot) {
            show_404();
        }

        $execution_order = $this->spares_execution->get_execution_order_by_order((int) $order_id);
        if (!$execution_order) {
            redirect(page_url . 'Spares_execution/schedule/' . (int) $order_id);
        }

        $task_counters = $this->spares_execution->get_task_counters((int) $execution_order->execution_order_id);
        $tasks = $this->spares_execution->get_execution_tasks((int) $execution_order->execution_order_id);
        $extension_requests = $this->spares_execution->get_extension_requests((int) $execution_order->execution_order_id);
        $updates = $this->spares_execution->get_task_updates((int) $execution_order->execution_order_id);

        $rows = array();
        $rows[] = array('Spares Execution Tracker Export');
        $rows[] = array();
        $rows[] = array('Order Summary');
        $rows[] = array('Field', 'Value');
        $rows[] = array('SO Reference', 'SO-' . (int) $order_snapshot->order_id);
        $rows[] = array('Customer', $order_snapshot->company_name);
        $rows[] = array('Opportunity No', !empty($order_snapshot->op_no) ? $order_snapshot->op_no : '');
        $rows[] = array('PO No', !empty($order_snapshot->po_no) ? $order_snapshot->po_no : '');
        $rows[] = array('Order Date', $this->csv_date($order_snapshot->order_date));
        $rows[] = array('Workflow', $execution_order->workflow_type);
        $rows[] = array('Commit Date', $this->csv_date($execution_order->commit_date));
        $rows[] = array('Priority', $execution_order->priority);
        $rows[] = array('Execution Status', $execution_order->execution_status);
        $rows[] = array('Marketing Owner', trim($order_snapshot->marketing_title . ' ' . $order_snapshot->marketing_first_name . ' ' . $order_snapshot->marketing_last_name));
        $rows[] = array('Schedule Notes', !empty($execution_order->schedule_notes) ? trim(preg_replace('/\s+/', ' ', $execution_order->schedule_notes)) : '');
        $rows[] = array('Total Tasks', (int) $task_counters->total_tasks);
        $rows[] = array('Completed Tasks', (int) $task_counters->completed_tasks);
        $rows[] = array('In Progress Tasks', (int) $task_counters->in_progress_tasks);
        $rows[] = array('Open Tasks', (int) $task_counters->open_tasks);
        $rows[] = array('Pending Tasks', (int) $task_counters->pending_tasks);
        $rows[] = array('Blocked Tasks', (int) $task_counters->blocked_tasks);
        $rows[] = array('On Hold Tasks', (int) $task_counters->on_hold_tasks);
        $rows[] = array('Cancelled Tasks', (int) $task_counters->cancelled_tasks);
        $rows[] = array('Overdue Tasks', (int) $task_counters->overdue_tasks);
        $rows[] = array();
        $rows[] = array('Task Tracking');
        $rows[] = array('Seq', 'Task Code', 'Task Name', 'Department', 'Owner', 'Status', 'Progress %', 'Planned Start', 'Planned End', 'Actual Start', 'Actual End', 'Last Activity', 'Dependency', 'Pending Extension', 'Last Note');

        foreach ($tasks as $task) {
            $dependency_label = !empty($task->dependency_task_name) ? $task->dependency_task_name : 'None';
            if (!empty($task->dependency_task_name) && (int) $task->dependency_blocked === 1) {
                $dependency_label .= ' (Blocked)';
            } elseif (!empty($task->dependency_task_name) && (int) $task->can_start_parallel === 1) {
                $dependency_label .= ' (Parallel allowed)';
            }

            $owner_name = !empty($task->first_name) ? trim($task->title . ' ' . $task->first_name . ' ' . $task->last_name) : 'Unassigned';
            $pending_extension_label = (int) $task->pending_extension_count > 0
                ? 'Pending until ' . $this->csv_date($task->pending_extension_requested_due_date)
                : '';

            $rows[] = array(
                (int) $task->sequence_no,
                $task->task_code,
                $task->task_name,
                !empty($task->department) ? $task->department : '',
                $owner_name,
                $task->task_status,
                (int) $task->completion_percent,
                $this->csv_date($task->planned_start_date),
                $this->csv_date($task->planned_end_date),
                $this->csv_datetime($task->actual_start_date),
                $this->csv_datetime($task->actual_end_date),
                $this->csv_datetime($task->last_activity_at),
                $dependency_label,
                $pending_extension_label,
                !empty($task->last_remark) ? trim(preg_replace('/\s+/', ' ', $task->last_remark)) : '',
            );
        }

        $rows[] = array();
        $rows[] = array('Extension Requests');
        $rows[] = array('Task', 'Current Due', 'Requested Due', 'Status', 'Requested By', 'Requested On', 'Reviewed By', 'Review Remarks', 'Reason');

        if (!empty($extension_requests)) {
            foreach ($extension_requests as $request) {
                $rows[] = array(
                    $request->task_name,
                    $this->csv_date($request->current_due_date),
                    $this->csv_date($request->requested_due_date),
                    $request->request_status,
                    trim($request->requested_title . ' ' . $request->requested_first_name . ' ' . $request->requested_last_name),
                    $this->csv_datetime($request->requested_on),
                    trim($request->reviewed_title . ' ' . $request->reviewed_first_name . ' ' . $request->reviewed_last_name),
                    !empty($request->review_remarks) ? trim(preg_replace('/\s+/', ' ', $request->review_remarks)) : '',
                    !empty($request->reason) ? trim(preg_replace('/\s+/', ' ', $request->reason)) : '',
                );
            }
        } else {
            $rows[] = array('No extension requests recorded.');
        }

        $rows[] = array();
        $rows[] = array('Recent Task Updates');
        $rows[] = array('When', 'Task', 'Type', 'Previous Status', 'New Status', 'Remarks', 'Attachment', 'By');

        if (!empty($updates)) {
            foreach ($updates as $update) {
                $rows[] = array(
                    $this->csv_datetime($update->added_on),
                    $update->task_name,
                    $update->update_type,
                    !empty($update->previous_status) ? $update->previous_status : '',
                    !empty($update->new_status) ? $update->new_status : '',
                    !empty($update->remarks) ? trim(preg_replace('/\s+/', ' ', $update->remarks)) : '',
                    !empty($update->attachment_original_name) ? $update->attachment_original_name : (!empty($update->attachment_name) ? $update->attachment_name : ''),
                    trim($update->title . ' ' . $update->first_name . ' ' . $update->last_name),
                );
            }
        } else {
            $rows[] = array('No task updates recorded.');
        }

        $filename = 'spares_execution_tracker_SO_' . (int) $order_snapshot->order_id . '_' . date('Ymd_His') . '.csv';
        $this->output_raw_csv_download($filename, $rows);
    }

    public function view_attachment($update_id)
    {
        $attachment = $this->spares_execution->get_task_update_attachment((int) $update_id);

        if (!$attachment) {
            $this->session->set_flashdata('error', 'Attachment details were not found or are no longer available.');
            redirect(page_url . 'Spares_execution/dashboard');
        }

        $file_name = basename((string) $attachment->attachment_name);
        $original_name = !empty($attachment->attachment_original_name) ? basename((string) $attachment->attachment_original_name) : $file_name;
        $file_path = FCPATH . 'uploads/spares_execution/' . $file_name;

        if ($file_name === '' || !is_file($file_path)) {
            $redirect_order_id = !empty($attachment->order_id) ? (int) $attachment->order_id : 0;
            $this->session->set_flashdata('error', 'The requested attachment file was not found on the server.');

            if ($redirect_order_id > 0) {
                redirect(page_url . 'Spares_execution/order/' . $redirect_order_id);
            }

            redirect(page_url . 'Spares_execution/dashboard');
        }

        $mime_type = 'application/octet-stream';
        if (function_exists('mime_content_type')) {
            $detected_type = @mime_content_type($file_path);
            if (!empty($detected_type)) {
                $mime_type = $detected_type;
            }
        } elseif (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detected_type = @finfo_file($finfo, $file_path);
                @finfo_close($finfo);
                if (!empty($detected_type)) {
                    $mime_type = $detected_type;
                }
            }
        }

        $safe_name = str_replace(array('"', "\r", "\n"), '', $original_name);

        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($file_path));
        header('Content-Disposition: inline; filename="' . $safe_name . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($file_path);
        exit;
    }

    public function update_task()
    {
        $this->form_validation->set_rules('order_id', 'Order', 'required|integer');
        $this->form_validation->set_rules('execution_task_id', 'Task', 'required|integer');
        $this->form_validation->set_rules('task_status', 'Task Status', 'required|trim');

        $order_id = (int) $this->input->post('order_id');
        $redirect_to = $this->input->post('redirect_to', true);
        if (!in_array($redirect_to, array('my_tasks', 'department_tasks'), true)) {
            $redirect_to = 'order';
        }
        $redirect_filters = array(
            'show' => trim((string) $this->input->post('redirect_show', true)),
            'workflow_type' => trim((string) $this->input->post('redirect_workflow_type', true)),
            'task_status' => trim((string) $this->input->post('redirect_task_status', true)),
            'search' => trim((string) $this->input->post('redirect_search', true)),
        );

        if ($redirect_to === 'department_tasks') {
            $redirect_filters['assigned_to'] = trim((string) $this->input->post('redirect_assigned_to', true));
        }

        $redirect_query = http_build_query(array_filter($redirect_filters, function ($value) {
            return $value !== '';
        }));
        $redirect_urls = array(
            'my_tasks' => page_url . 'Spares_execution/my_tasks' . (!empty($redirect_query) ? '?' . $redirect_query : ''),
            'department_tasks' => page_url . 'Spares_execution/department_tasks' . (!empty($redirect_query) ? '?' . $redirect_query : ''),
        );

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            if ($redirect_to !== 'order') {
                redirect($redirect_urls[$redirect_to]);
            }
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $execution_task_id = (int) $this->input->post('execution_task_id');
        $task_context = $this->spares_execution->get_execution_task_context($execution_task_id);
        if (!$task_context || (int) $task_context->order_id !== $order_id) {
            $this->session->set_flashdata('error', 'Invalid execution task selected.');
            if ($redirect_to !== 'order') {
                redirect($redirect_urls[$redirect_to]);
            }
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $assigned_to = $this->input->post('assigned_to') ? (int) $this->input->post('assigned_to') : null;
        $assignment_error = $this->validate_department_owner_selection((int) $task_context->department_id, $assigned_to, 'Task update');
        if ($assignment_error !== '') {
            $this->session->set_flashdata('error', $assignment_error);
            if ($redirect_to !== 'order') {
                redirect($redirect_urls[$redirect_to]);
            }
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $new_status = trim((string) $this->input->post('task_status', true));
        if ((int) $task_context->dependency_blocked === 1 && in_array($new_status, array('Open', 'In Progress', 'Completed'), true)) {
            $dependency_name = !empty($task_context->dependency_task_name) ? $task_context->dependency_task_name : 'the dependency task';
            $this->session->set_flashdata('error', 'This task cannot move to ' . $new_status . ' until "' . $dependency_name . '" is completed.');
            if ($redirect_to !== 'order') {
                redirect($redirect_urls[$redirect_to]);
            }
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $payload = array(
            'task_status' => $new_status,
            'completion_percent' => max(0, min(100, (int) $this->input->post('completion_percent'))),
            'assigned_to' => $assigned_to,
        );

        $remark_text = trim($this->input->post('remarks', true));
        $user_id = $this->session->userdata['logged_in']['user_id'];
        $attachment_data = $this->handle_task_update_attachment('task_attachment');

        if (isset($attachment_data['error'])) {
            $this->session->set_flashdata('error', $attachment_data['error']);
            if ($redirect_to !== 'order') {
                redirect($redirect_urls[$redirect_to]);
            }
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $updated = $this->spares_execution->update_execution_task((int) $this->input->post('execution_task_id'), $payload, $remark_text, $user_id, $attachment_data);

        if ($updated) {
            $this->session->set_flashdata('success', 'Task updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Unable to update task.');
        }

        if ($redirect_to !== 'order') {
            redirect($redirect_urls[$redirect_to]);
        }
        redirect(page_url . 'Spares_execution/order/' . $order_id);
    }

    public function bulk_order_action()
    {
        $this->form_validation->set_rules('order_id', 'Order', 'required|integer');
        $this->form_validation->set_rules('action_key', 'Order Action', 'required|trim');
        $this->form_validation->set_rules('remarks', 'Action Remarks', 'required|trim');

        $order_id = (int) $this->input->post('order_id');
        $execution_order = $this->spares_execution->get_execution_order_by_order($order_id);

        if (!$execution_order) {
            show_404();
        }

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if (!$this->spares_execution->can_user_manage_execution_order((int) $execution_order->execution_order_id, $user_id)) {
            $this->session->set_flashdata('error', 'You are not authorized to manage this execution order.');
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $action_key = trim((string) $this->input->post('action_key', true));
        $action_labels = array(
            'hold_remaining' => 'Put Remaining Tasks On Hold',
            'resume_held' => 'Resume Held Tasks',
            'cancel_remaining' => 'Cancel Remaining Tasks',
        );

        if (!isset($action_labels[$action_key])) {
            $this->session->set_flashdata('error', 'Invalid order action selected.');
            redirect(page_url . 'Spares_execution/order/' . $order_id);
        }

        $affected = $this->spares_execution->bulk_update_execution_order_tasks(
            (int) $execution_order->execution_order_id,
            $action_key,
            trim($this->input->post('remarks', true)),
            $user_id
        );

        if ($affected === false) {
            $this->session->set_flashdata('error', 'Unable to process the selected order action.');
        } elseif ((int) $affected === 0) {
            $this->session->set_flashdata('error', $action_labels[$action_key] . ' did not change any tasks.');
        } else {
            $this->session->set_flashdata('success', $action_labels[$action_key] . ' applied to ' . (int) $affected . ' task(s).');
        }

        redirect(page_url . 'Spares_execution/order/' . $order_id);
    }

    public function request_extension()
    {
        $this->form_validation->set_rules('order_id', 'Order', 'required|integer');
        $this->form_validation->set_rules('execution_task_id', 'Task', 'required|integer');
        $this->form_validation->set_rules('requested_due_date', 'Requested Due Date', 'required|trim');
        $this->form_validation->set_rules('reason', 'Reason', 'required|trim');

        $order_id = (int) $this->input->post('order_id');
        $redirect_to = $this->input->post('redirect_to', true);
        if (!in_array($redirect_to, array('my_tasks', 'department_tasks'), true)) {
            $redirect_to = 'order';
        }

        $redirect_filters = array(
            'show' => trim((string) $this->input->post('redirect_show', true)),
            'workflow_type' => trim((string) $this->input->post('redirect_workflow_type', true)),
            'task_status' => trim((string) $this->input->post('redirect_task_status', true)),
            'search' => trim((string) $this->input->post('redirect_search', true)),
        );

        if ($redirect_to === 'department_tasks') {
            $redirect_filters['assigned_to'] = trim((string) $this->input->post('redirect_assigned_to', true));
        }

        $redirect_query = http_build_query(array_filter($redirect_filters, function ($value) {
            return $value !== '';
        }));
        $redirect_urls = array(
            'my_tasks' => page_url . 'Spares_execution/my_tasks' . (!empty($redirect_query) ? '?' . $redirect_query : ''),
            'department_tasks' => page_url . 'Spares_execution/department_tasks' . (!empty($redirect_query) ? '?' . $redirect_query : ''),
        );
        $redirect_order = function () use ($redirect_to, $redirect_urls, $order_id) {
            if ($redirect_to !== 'order') {
                redirect($redirect_urls[$redirect_to]);
            }

            redirect(page_url . 'Spares_execution/order/' . $order_id);
        };

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            $redirect_order();
        }

        $execution_task_id = (int) $this->input->post('execution_task_id');
        $task_context = $this->spares_execution->get_extension_request_context($execution_task_id);
        if (!$task_context || (int) $task_context->order_id !== $order_id) {
            $this->session->set_flashdata('error', 'Invalid execution task selected for extension request.');
            $redirect_order();
        }

        if ((int) $task_context->extension_allowed !== 1) {
            $this->session->set_flashdata('error', 'Extension request is not allowed for "' . $task_context->task_name . '".');
            $redirect_order();
        }

        if (in_array($task_context->task_status, array('Completed', 'Cancelled'), true)) {
            $this->session->set_flashdata('error', 'Extension request cannot be raised for a completed or cancelled task.');
            $redirect_order();
        }

        if ((int) $task_context->pending_request_count > 0) {
            $this->session->set_flashdata('error', 'A pending extension request already exists for "' . $task_context->task_name . '".');
            $redirect_order();
        }

        $requested_due_date = trim((string) $this->input->post('requested_due_date', true));
        if (!$this->is_valid_iso_date($requested_due_date)) {
            $this->session->set_flashdata('error', 'Please enter a valid requested due date.');
            $redirect_order();
        }

        if (!empty($task_context->planned_end_date) && $requested_due_date <= $task_context->planned_end_date) {
            $this->session->set_flashdata('error', 'Requested due date must be later than the current planned end date for "' . $task_context->task_name . '".');
            $redirect_order();
        }

        $created = $this->spares_execution->create_extension_request((int) $this->input->post('execution_task_id'), array(
            'requested_due_date' => $requested_due_date,
            'reason' => trim($this->input->post('reason', true)),
            'requested_by' => (int) $this->session->userdata['logged_in']['user_id'],
        ));

        if ($created) {
            $this->session->set_flashdata('success', 'Extension request submitted.');
        } else {
            $this->session->set_flashdata('error', 'Unable to submit extension request.');
        }

        $redirect_order();
    }

    public function review_extension()
    {
        $this->form_validation->set_rules('order_id', 'Order', 'required|integer');
        $this->form_validation->set_rules('extension_request_id', 'Extension Request', 'required|integer');
        $this->form_validation->set_rules('decision', 'Decision', 'required|trim');

        $order_id = (int) $this->input->post('order_id');
        $extension_request_id = (int) $this->input->post('extension_request_id');
        $redirect_to = $this->input->post('redirect_to', true) === 'dashboard' ? 'dashboard' : 'order';
        $dashboard_redirect = $this->build_dashboard_redirect_url_from_post();
        $redirect_response = function () use ($redirect_to, $dashboard_redirect, $order_id) {
            if ($redirect_to === 'dashboard') {
                redirect($dashboard_redirect);
            }

            redirect(page_url . 'Spares_execution/order/' . $order_id);
        };

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            $redirect_response();
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        if (!$this->spares_execution->can_user_review_extension_request($extension_request_id, $user_id)) {
            $this->session->set_flashdata('error', 'You are not authorized to review this extension request.');
            $redirect_response();
        }

        $decision = $this->input->post('decision', true) === 'Approved' ? 'Approved' : 'Rejected';
        $review_context = $this->spares_execution->get_extension_review_context($extension_request_id);
        if (!$review_context || (int) $review_context->order_id !== $order_id) {
            $this->session->set_flashdata('error', 'Invalid extension request selected for review.');
            $redirect_response();
        }

        if ($review_context->request_status !== 'Pending') {
            $this->session->set_flashdata('error', 'This extension request has already been reviewed.');
            $redirect_response();
        }

        if (in_array($review_context->task_status, array('Completed', 'Cancelled'), true)) {
            $this->session->set_flashdata('error', 'This extension request can no longer be reviewed because the task is already closed.');
            $redirect_response();
        }

        if ($decision === 'Approved' && !empty($review_context->planned_end_date) && $review_context->requested_due_date <= $review_context->planned_end_date) {
            $this->session->set_flashdata('error', 'This request is now stale because the task due date has already been updated to the same or a later date.');
            $redirect_response();
        }

        $reviewed = $this->spares_execution->review_extension_request(
            $extension_request_id,
            $decision,
            trim($this->input->post('review_remarks', true)),
            $user_id
        );

        if ($reviewed) {
            $this->session->set_flashdata('success', 'Extension request reviewed successfully.');
        } else {
            $this->session->set_flashdata('error', 'Unable to review extension request.');
        }

        $redirect_response();
    }

    private function build_default_sf_form($order_snapshot, $execution_order, $user_id)
    {
        $is_international = !empty($order_snapshot->op_type) && (int) $order_snapshot->op_type !== 1;
        $dispatch_mode = !empty($order_snapshot->dispatch_mode) ? strtoupper(trim((string) $order_snapshot->dispatch_mode)) : 'COURIER';
        $dispatch_mode = str_replace(' ', '_', $dispatch_mode);
        if (!in_array($dispatch_mode, array('COURIER', 'SELF_PICKUP'), true)) {
            $dispatch_mode = 'COURIER';
        }
        $po_no = !empty($order_snapshot->po_no) ? $order_snapshot->po_no : '';

        return (object) array(
            'sf_form_id' => 0,
            'order_id' => (int) $order_snapshot->order_id,
            'execution_order_id' => (int) $execution_order->execution_order_id,
            'sf_no' => $this->spares_execution->build_sf_number((int) $order_snapshot->order_id),
            'release_date' => date('Y-m-d'),
            'prepared_by' => (int) $user_id,
            'form_status' => 'Draft',
            'scope_of_supply' => 'Spares supply against customer purchase order',
            'document_requirement' => $is_international ? 'International dispatch documents as per country requirement' : 'Domestic dispatch documents',
            'packing_duration_days' => $is_international ? 3 : 1,
            'dispatch_duration_days' => $is_international ? 7 : 2,
            'dispatch_mode' => $dispatch_mode,
            'order_description' => trim('Spares order' . ($po_no !== '' ? ' against PO ' . $po_no : '')),
            'po_no' => $po_no,
            'po_date' => !empty($order_snapshot->po_date) ? $order_snapshot->po_date : null,
            'po_revision' => '',
            'quotation_ref' => !empty($order_snapshot->latest_quotation_no) ? $order_snapshot->latest_quotation_no : '',
            'quotation_revision' => '',
            'machine_description' => '',
            'machine_df' => '',
            'no_of_tracks' => '',
            'product_to_be_packed' => '',
            'quantity_to_be_packed' => '',
            'pouch_size_type' => '',
            'no_of_axis' => '',
            'plc_make' => '',
            'specification_note' => '',
        );
    }

    private function build_default_sf_items($order_snapshot, $execution_order)
    {
        $items = array();
        $po_items = $this->spares_execution->get_po_items_for_sf((int) $order_snapshot->po_id);
        $target_date = !empty($execution_order->commit_date) ? $execution_order->commit_date : null;

        foreach ($po_items as $po_item) {
            $items[] = (object) array(
                'line_no' => count($items) + 1,
                'item_description' => !empty($po_item->description) ? $po_item->description : $po_item->product_master_description,
                'part_no_erp' => !empty($po_item->product_code) ? $po_item->product_code : '',
                'drg_rev_no' => isset($po_item->product_revision) ? (string) $po_item->product_revision : '',
                'quantity' => !empty($po_item->quantity) ? (float) $po_item->quantity : 0,
                'target_date' => $target_date,
                'dispatch_1_date' => $target_date,
                'dispatch_2_date' => null,
                'dispatch_3_date' => null,
            );
        }

        if (empty($items)) {
            $items[] = (object) array(
                'line_no' => 1,
                'item_description' => '',
                'part_no_erp' => '',
                'drg_rev_no' => '',
                'quantity' => 0,
                'target_date' => $target_date,
                'dispatch_1_date' => $target_date,
                'dispatch_2_date' => null,
                'dispatch_3_date' => null,
            );
        }

        return $items;
    }

    private function collect_sf_item_rows()
    {
        $descriptions = $this->input->post('item_description');
        $part_numbers = $this->input->post('part_no_erp');
        $drawing_revisions = $this->input->post('drg_rev_no');
        $quantities = $this->input->post('quantity');
        $target_dates = $this->input->post('target_date');
        $dispatch_1_dates = $this->input->post('dispatch_1_date');
        $dispatch_2_dates = $this->input->post('dispatch_2_date');
        $dispatch_3_dates = $this->input->post('dispatch_3_date');
        $rows = array();

        if (empty($descriptions) || !is_array($descriptions)) {
            return $rows;
        }

        foreach ($descriptions as $index => $description) {
            $description = trim((string) $description);
            if ($description === '') {
                continue;
            }

            $rows[] = array(
                'item_description' => $description,
                'part_no_erp' => trim((string) ($part_numbers[$index] ?? '')),
                'drg_rev_no' => trim((string) ($drawing_revisions[$index] ?? '')),
                'quantity' => max(0, (float) ($quantities[$index] ?? 0)),
                'target_date' => $this->normalize_optional_iso_date($target_dates[$index] ?? ''),
                'dispatch_1_date' => $this->normalize_optional_iso_date($dispatch_1_dates[$index] ?? ''),
                'dispatch_2_date' => $this->normalize_optional_iso_date($dispatch_2_dates[$index] ?? ''),
                'dispatch_3_date' => $this->normalize_optional_iso_date($dispatch_3_dates[$index] ?? ''),
            );
        }

        return $rows;
    }

    private function validate_sf_item_dates($items)
    {
        foreach ($items as $index => $item) {
            foreach (array('target_date', 'dispatch_1_date', 'dispatch_2_date', 'dispatch_3_date') as $field) {
                if ($item[$field] === false) {
                    return 'Please check the date format in SF item row ' . ($index + 1) . '.';
                }
            }
        }

        return '';
    }

    private function normalize_optional_iso_date($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        return $this->is_valid_iso_date($value) ? $value : false;
    }

    private function get_logged_in_user_name()
    {
        $session_user = $this->session->userdata('logged_in');
        if (!is_array($session_user)) {
            return '';
        }

        return trim(
            (isset($session_user['title']) ? $session_user['title'] . ' ' : '') .
            (isset($session_user['first_name']) ? $session_user['first_name'] . ' ' : '') .
            (isset($session_user['last_name']) ? $session_user['last_name'] : '')
        );
    }

    private function collect_schedule_task_rows($include_execution_task_id = false)
    {
        $execution_task_ids = $this->input->post('execution_task_id');
        $task_master_ids = $this->input->post('task_master_id');
        $task_codes = $this->input->post('task_code');
        $depends_on_code = $this->input->post('depends_on_code');
        $can_start_parallel = $this->input->post('can_start_parallel');
        $department_ids = $this->input->post('department_id');
        $assigned_to = $this->input->post('assigned_to');
        $planned_start_date = $this->input->post('planned_start_date');
        $planned_end_date = $this->input->post('planned_end_date');
        $task_names = $this->input->post('task_name');

        $task_rows = array();

        if (empty($task_names) || !is_array($task_names)) {
            return $task_rows;
        }

        foreach ($task_names as $index => $task_name) {
            if (trim($task_name) === '') {
                continue;
            }

            $row = array(
                'task_master_id' => !empty($task_master_ids[$index]) ? (int) $task_master_ids[$index] : null,
                'task_name' => trim($task_name),
                'task_code' => !empty($task_codes[$index]) ? trim($task_codes[$index]) : null,
                'depends_on_code' => !empty($depends_on_code[$index]) ? trim($depends_on_code[$index]) : null,
                'can_start_parallel' => !empty($can_start_parallel[$index]) ? 1 : 0,
                'department_id' => !empty($department_ids[$index]) ? (int) $department_ids[$index] : null,
                'assigned_to' => !empty($assigned_to[$index]) ? (int) $assigned_to[$index] : null,
                'planned_start_date' => !empty($planned_start_date[$index]) ? $planned_start_date[$index] : null,
                'planned_end_date' => !empty($planned_end_date[$index]) ? $planned_end_date[$index] : null,
            );

            if ($include_execution_task_id) {
                $row['execution_task_id'] = !empty($execution_task_ids[$index]) ? (int) $execution_task_ids[$index] : null;
            }

            if (!empty($row['task_master_id'])) {
                $task_master = $this->spares_execution->get_task_master_by_id((int) $row['task_master_id']);
                if ($task_master) {
                    $row['task_name'] = $task_master->task_name;
                    $row['task_code'] = $task_master->task_code;
                    $row['depends_on_code'] = !empty($task_master->depends_on_code) ? trim($task_master->depends_on_code) : null;
                    $row['can_start_parallel'] = !empty($task_master->can_start_parallel) ? 1 : 0;
                    $row['department_id'] = !empty($task_master->department_id) ? (int) $task_master->department_id : null;
                    $row['assigned_to'] = !empty($task_master->default_owner_id) ? (int) $task_master->default_owner_id : null;
                }
            }

            $task_rows[] = $row;
        }

        return $task_rows;
    }

    private function validate_department_owner_selection($department_id, $user_id, $context_label = 'Selection')
    {
        if (!empty($department_id) && !$this->spares_execution->is_valid_scoped_department($department_id)) {
            return $context_label . ' uses a department outside business location 2.';
        }

        if (empty($department_id) && !empty($user_id)) {
            return $context_label . ' requires a department before choosing an owner.';
        }

        if (!empty($user_id) && !$this->spares_execution->is_valid_user_for_department($user_id, $department_id)) {
            return $context_label . ' owner does not belong to the selected department.';
        }

        return '';
    }

    private function get_task_queue_filters($include_assigned_to = false, $limit = 200)
    {
        $filters = array(
            'show' => $this->input->get('show', true) ?: 'active',
            'workflow_type' => trim((string) $this->input->get('workflow_type', true)),
            'task_status' => trim((string) $this->input->get('task_status', true)),
            'search' => trim((string) $this->input->get('search', true)),
            'limit' => (int) $limit,
        );

        if ($include_assigned_to) {
            $filters['assigned_to'] = trim((string) $this->input->get('assigned_to', true));
        }

        return $filters;
    }

    private function get_mrp_queue_filters($limit = 200)
    {
        $mrp_status = trim((string) $this->input->get('mrp_status', true));
        $allowed_statuses = array('all', 'pending', 'shortage', 'available', 'missing_master');
        if (!in_array($mrp_status, $allowed_statuses, true)) {
            $mrp_status = 'all';
        }

        return array(
            'mrp_status' => $mrp_status,
            'workflow_type' => trim((string) $this->input->get('workflow_type', true)),
            'search' => trim((string) $this->input->get('search', true)),
            'limit' => (int) $limit,
        );
    }

    private function get_dashboard_filters()
    {
        return array(
            'view_mode' => $this->input->get('view_mode', true) ?: 'all',
            'workflow_type' => trim((string) $this->input->get('workflow_type', true)),
            'execution_status' => trim((string) $this->input->get('execution_status', true)),
            'priority' => trim((string) $this->input->get('priority', true)),
            'search' => trim((string) $this->input->get('search', true)),
        );
    }

    private function build_dashboard_query_filters($filters, $user_id)
    {
        $execution_filters = array();
        $queue_filters = array();
        $unscheduled_filters = array();

        if (!empty($filters['workflow_type'])) {
            $execution_filters['workflow_type'] = $filters['workflow_type'];
        }

        if (!empty($filters['execution_status'])) {
            $execution_filters['execution_status'] = $filters['execution_status'];
        }

        if (!empty($filters['priority'])) {
            $execution_filters['priority'] = $filters['priority'];
        }

        if (!empty($filters['search'])) {
            $execution_filters['search'] = $filters['search'];
            $unscheduled_filters['search'] = $filters['search'];
        }

        if (!empty($filters['view_mode']) && $filters['view_mode'] === 'my') {
            $execution_filters['manager_user_id'] = $user_id;
            $queue_filters['manager_user_id'] = $user_id;
            $unscheduled_filters['marketing_owner_id'] = $user_id;
        } elseif (!empty($filters['view_mode']) && $filters['view_mode'] === 'overdue') {
            $execution_filters['overdue_only'] = 1;
            $execution_filters['active_only'] = 1;
        } elseif (!empty($filters['view_mode']) && $filters['view_mode'] === 'pending_extensions') {
            $execution_filters['pending_extensions_only'] = 1;
            $execution_filters['active_only'] = 1;
        }

        return array(
            'execution_filters' => $execution_filters,
            'queue_filters' => $queue_filters,
            'unscheduled_filters' => $unscheduled_filters,
        );
    }

    private function validate_schedule_task_rows($task_rows, $commit_date = null)
    {
        if (!empty($commit_date) && !$this->is_valid_iso_date($commit_date)) {
            return 'Commit date is invalid.';
        }

        $task_map = array();

        foreach ($task_rows as $index => $task_row) {
            $task_label = !empty($task_row['task_name']) ? 'Task "' . $task_row['task_name'] . '"' : 'Task row ' . ($index + 1);

            if (empty($task_row['department_id'])) {
                return $task_label . ' requires department mapping in Task Master.';
            }

            $assignment_error = $this->validate_department_owner_selection(
                !empty($task_row['department_id']) ? (int) $task_row['department_id'] : null,
                !empty($task_row['assigned_to']) ? (int) $task_row['assigned_to'] : null,
                $task_label
            );

            if ($assignment_error !== '') {
                return $assignment_error;
            }

            if (empty($task_row['planned_start_date']) || empty($task_row['planned_end_date'])) {
                return $task_label . ' requires both planned start date and planned end date.';
            }

            if (!$this->is_valid_iso_date($task_row['planned_start_date']) || !$this->is_valid_iso_date($task_row['planned_end_date'])) {
                return $task_label . ' has an invalid planned start or end date.';
            }

            if (!$this->spares_execution->is_execution_working_date($task_row['planned_start_date'])) {
                return $task_label . ' planned start date is Sunday or holiday. Please reload TAT template or select a working date.';
            }

            if (!$this->spares_execution->is_execution_working_date($task_row['planned_end_date'])) {
                return $task_label . ' planned end date is Sunday or holiday. Please reload TAT template or select a working date.';
            }

            if ($task_row['planned_start_date'] > $task_row['planned_end_date']) {
                return $task_label . ' has a planned start date after its planned end date.';
            }

            if (!empty($commit_date) && $task_row['planned_end_date'] > $commit_date) {
                return $task_label . ' ends after the order commit date.';
            }

            if (!empty($task_row['task_code'])) {
                $task_map[$task_row['task_code']] = $task_row;
            }
        }

        foreach ($task_rows as $index => $task_row) {
            if (empty($task_row['depends_on_code'])) {
                continue;
            }

            $task_label = !empty($task_row['task_name']) ? 'Task "' . $task_row['task_name'] . '"' : 'Task row ' . ($index + 1);
            if (empty($task_map[$task_row['depends_on_code']])) {
                return $task_label . ' depends on missing task code "' . $task_row['depends_on_code'] . '".';
            }

            if (!empty($task_row['can_start_parallel'])) {
                continue;
            }

            $dependency_row = $task_map[$task_row['depends_on_code']];
            if (!empty($dependency_row['planned_end_date']) && $task_row['planned_start_date'] < $dependency_row['planned_end_date']) {
                return $task_label . ' must start on or after the planned end date of "' . $dependency_row['task_name'] . '".';
            }
        }

        return '';
    }

    private function is_valid_iso_date($value)
    {
        if (!is_string($value) || trim($value) === '') {
            return false;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);

        return $date instanceof DateTime && $date->format('Y-m-d') === $value;
    }

    private function build_dashboard_redirect_url_from_post()
    {
        $filters = array(
            'view_mode' => trim((string) $this->input->post('redirect_view_mode', true)),
            'workflow_type' => trim((string) $this->input->post('redirect_workflow_type', true)),
            'execution_status' => trim((string) $this->input->post('redirect_execution_status', true)),
            'priority' => trim((string) $this->input->post('redirect_priority', true)),
            'search' => trim((string) $this->input->post('redirect_search', true)),
        );

        foreach ($filters as $key => $value) {
            if ($value === '' || $value === null || ($key === 'view_mode' && $value === 'all')) {
                unset($filters[$key]);
            }
        }

        $query = http_build_query($filters);

        return page_url . 'Spares_execution/dashboard' . (!empty($query) ? '?' . $query : '');
    }

    private function csv_date($value)
    {
        if (empty($value) || $value === '0000-00-00') {
            return '';
        }

        return date('d M Y', strtotime($value));
    }

    private function csv_datetime($value)
    {
        if (empty($value) || $value === '0000-00-00 00:00:00') {
            return '';
        }

        return date('d M Y h:i A', strtotime($value));
    }

    private function output_csv_download($filename, $headers, $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    private function output_raw_csv_download($filename, $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    private function handle_task_update_attachment($field_name)
    {
        if (empty($_FILES[$field_name]) || empty($_FILES[$field_name]['name'])) {
            return null;
        }

        $upload_path = FCPATH . 'uploads/spares_execution/';
        if (!is_dir($upload_path) && !mkdir($upload_path, 0777, true) && !is_dir($upload_path)) {
            return array('error' => 'Unable to prepare attachment folder for task updates.');
        }

        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|txt|zip',
            'max_size' => 10240,
            'encrypt_name' => true,
        );

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($field_name)) {
            return array('error' => 'Attachment upload failed: ' . $this->upload->display_errors('', ''));
        }

        $upload_data = $this->upload->data();

        return array(
            'attachment_name' => $upload_data['file_name'],
            'attachment_original_name' => $upload_data['client_name'],
        );
    }

    private function build_alert_dispatch_run_payload($run_mode, $summary, $triggered_by, $run_note)
    {
        $payload = array(
            'run_mode' => $run_mode,
            'sent_count' => (int) $summary->sent,
            'skipped_count' => (int) $summary->skipped,
            'unscheduled_sent_count' => (int) $summary->unscheduled_sent,
            'overdue_sent_count' => (int) $summary->overdue_sent,
            'pending_extension_sent_count' => (int) $summary->pending_extension_sent,
            'triggered_by' => (int) $triggered_by,
            'run_note' => $run_note,
            'created_at' => date('Y-m-d H:i:s'),
        );

        if ($this->db->table_exists('spares_execution_alert_runs') && $this->db->field_exists('stale_sent_count', 'spares_execution_alert_runs')) {
            $payload['stale_sent_count'] = (int) $summary->stale_sent;
        }

        return $payload;
    }

    private function build_module_health_snapshot($metrics, $recent_alert_runs)
    {
        $health = array();
        $latest_run = !empty($recent_alert_runs) ? $recent_alert_runs[0] : null;
        $uploads_dir = FCPATH . 'uploads/spares_execution/';
        $uploads_parent = dirname(rtrim($uploads_dir, '/'));
        $upload_dir_exists = is_dir($uploads_dir);
        $upload_dir_ready = $upload_dir_exists && is_writable($uploads_dir);
        $upload_dir_can_autocreate = !$upload_dir_exists && is_dir($uploads_parent) && is_writable($uploads_parent);

        $health['uploads'] = array(
            'label' => 'Attachment Folder',
            'status' => $upload_dir_ready ? 'success' : ($upload_dir_can_autocreate ? 'warning' : 'danger'),
            'value' => $upload_dir_ready ? 'Ready' : ($upload_dir_can_autocreate ? 'Auto-create' : 'Blocked'),
            'note' => $upload_dir_ready
                ? 'Task update attachments can be uploaded.'
                : ($upload_dir_can_autocreate ? 'Folder will be created automatically on first attachment upload.' : 'Server write permission is needed for uploads/spares_execution/.'),
        );

        if (!empty($latest_run) && !empty($latest_run->created_at)) {
            $run_age_hours = (int) floor((time() - strtotime($latest_run->created_at)) / 3600);
            $health['automation'] = array(
                'label' => 'Alert Automation',
                'status' => $run_age_hours <= 26 ? 'success' : 'warning',
                'value' => strtoupper(str_replace('_', ' ', $latest_run->run_mode)),
                'note' => 'Last run ' . date('d M Y h:i A', strtotime($latest_run->created_at)) . ($run_age_hours > 26 ? ' - older than 24 hours.' : ' - recent and healthy.'),
            );
        } else {
            $health['automation'] = array(
                'label' => 'Alert Automation',
                'status' => 'warning',
                'value' => 'Not Run Yet',
                'note' => 'Use "Send Today\'s Alerts" once or wait for cron to create the first automation run entry.',
            );
        }

        $pending_setup_count = !empty($metrics->unscheduled_orders) ? (int) $metrics->unscheduled_orders : 0;
        $health['pending_setup'] = array(
            'label' => 'Pending Setup Orders',
            'status' => $pending_setup_count > 0 ? 'warning' : 'success',
            'value' => $pending_setup_count,
            'note' => $pending_setup_count > 0 ? 'Won or running spare orders still need execution scheduling.' : 'All running spare orders have execution schedules.',
        );

        $overdue_task_count = !empty($metrics->overdue_tasks) ? (int) $metrics->overdue_tasks : 0;
        $health['overdue_tasks'] = array(
            'label' => 'Overdue Tasks',
            'status' => $overdue_task_count > 0 ? 'danger' : 'success',
            'value' => $overdue_task_count,
            'note' => $overdue_task_count > 0 ? 'Department action is required on delayed execution tasks.' : 'No overdue active execution tasks right now.',
        );

        $schema_missing = array();
        if (!$this->db->table_exists('spares_execution_alert_log')) {
            $schema_missing[] = 'alert log table';
        }
        if (!$this->db->table_exists('spares_execution_task_updates')) {
            $schema_missing[] = 'task updates table';
        }
        if (!$this->db->table_exists('spares_execution_alert_runs')) {
            $schema_missing[] = 'alert run table';
        }
        if (!$this->db->field_exists('stale_sent_count', 'spares_execution_alert_runs')) {
            $schema_missing[] = 'stale_sent_count column';
        }
        if (!$this->column_type_contains('spares_execution_alert_log', 'alert_type', "'STALE_TASK'")) {
            $schema_missing[] = 'STALE_TASK alert type';
        }
        if (!$this->column_type_contains('spares_execution_task_updates', 'update_type', "'Bulk Status'")) {
            $schema_missing[] = 'Bulk Status update type';
        }

        $health['schema'] = array(
            'label' => 'Schema Alignment',
            'status' => empty($schema_missing) ? 'success' : 'warning',
            'value' => empty($schema_missing) ? 'Aligned' : 'Patch Needed',
            'note' => empty($schema_missing)
                ? 'Current Spares execution database patches are aligned with the latest module code.'
                : 'Missing support for ' . implode(', ', $schema_missing) . '. Import the latest Spares execution DB patch file.',
        );

        return $health;
    }

    private function column_type_contains($table_name, $column_name, $needle)
    {
        if (!$this->db->table_exists($table_name) || !$this->db->field_exists($column_name, $table_name)) {
            return false;
        }

        $row = $this->db
            ->select('COLUMN_TYPE')
            ->from('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_SCHEMA', $this->db->database)
            ->where('TABLE_NAME', $table_name)
            ->where('COLUMN_NAME', $column_name)
            ->get()
            ->row();

        if (!$row || !isset($row->COLUMN_TYPE)) {
            return false;
        }

        return strpos((string) $row->COLUMN_TYPE, (string) $needle) !== false;
    }

    private function get_execution_cron_key()
    {
        $encryption_key = (string) config_item('encryption_key');

        if (trim($encryption_key) === '') {
            return '';
        }

        return sha1($encryption_key . '|spares_execution_daily_alerts');
    }
}
