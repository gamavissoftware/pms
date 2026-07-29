<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_completion_control extends CI_Controller
{
    private $allowed_user_ids = array(61, 161);

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Session logged out. Please login again to continue.</div>');
            redirect(page_url);
        }

        $this->load->model('Task_completion_control_model', 'task_completion_control');
    }

    public function index()
    {
        $current_user_id = $this->require_access();
        $filters = $this->get_filters();
        $tasks = $this->task_completion_control->get_completed_tasks($filters);

        $df_ids = array();
        foreach ($tasks as $task_row) {
            if (!empty($task_row['df_id'])) {
                $df_ids[] = (int) $task_row['df_id'];
            }
        }

        $data = array(
            'current_user_id' => $current_user_id,
            'module_ready' => $this->task_completion_control->module_ready(),
            'migration_file' => 'Database/task_completion_control_001.sql',
            'filters' => $filters,
            'df_options' => $this->task_completion_control->get_df_options(),
            'department_options' => $this->task_completion_control->get_department_options(),
            'user_options' => $this->task_completion_control->get_user_options(isset($filters['department_id']) ? (int) $filters['department_id'] : 0),
            'tasks' => $tasks,
            'stats' => array(
                'visible_tasks' => count($tasks),
                'visible_dfs' => count(array_unique($df_ids)),
                'history_entries' => $this->task_completion_control->get_history_count(),
                'rollback_entries' => $this->task_completion_control->get_history_count('ROLLED_BACK_TO_PENDING')
            )
        );

        $this->load->view('task_completion_control/index', $data);
    }

    public function view($task_record_id = 0)
    {
        $current_user_id = $this->require_access();
        $task_record_id = (int) $task_record_id;
        $task = $this->task_completion_control->get_task_record($task_record_id);

        if (empty($task)) {
            $this->set_flash_message('danger', 'Requested task record was not found.');
            redirect(page_url . 'Task_completion_control');
        }

        $data = array(
            'current_user_id' => $current_user_id,
            'module_ready' => $this->task_completion_control->module_ready(),
            'migration_file' => 'Database/task_completion_control_001.sql',
            'task' => $task,
            'history' => $this->task_completion_control->get_task_history($task_record_id)
        );

        $this->load->view('task_completion_control/view', $data);
    }

    public function update($task_record_id = 0)
    {
        $this->require_access();
        $task_record_id = (int) $task_record_id;

        if (!$this->task_completion_control->module_ready()) {
            $this->set_flash_message('danger', 'Task completion control history table is not ready. Please run the migration first.');
            redirect(page_url . 'Task_completion_control/view/' . $task_record_id);
        }

        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('task_completed_on', 'Completion Date', 'required|trim');
        $this->form_validation->set_rules('remarks', 'Remarks', 'trim');
        $this->form_validation->set_rules('change_note', 'Change Note', 'trim');

        if ($this->form_validation->run() === false) {
            $this->set_flash_message('danger', validation_errors());
            redirect(page_url . 'Task_completion_control/view/' . $task_record_id);
        }

        $result = $this->task_completion_control->update_completed_task(
            $task_record_id,
            $this->get_current_user_id(),
            array(
                'task_completed_on' => $this->input->post('task_completed_on', true),
                'remarks' => $this->input->post('remarks', true),
                'change_note' => $this->input->post('change_note', true)
            )
        );

        if (!empty($result['success'])) {
            $message_type = !empty($result['changed']) ? 'success' : 'warning';
            $this->set_flash_message($message_type, $result['message']);
        } else {
            $this->set_flash_message('danger', $result['message']);
        }

        redirect(page_url . 'Task_completion_control/view/' . $task_record_id);
    }

    public function rollback($task_record_id = 0)
    {
        $this->require_access();
        $task_record_id = (int) $task_record_id;

        if (!$this->task_completion_control->module_ready()) {
            $this->set_flash_message('danger', 'Task completion control history table is not ready. Please run the migration first.');
            redirect(page_url . 'Task_completion_control/view/' . $task_record_id);
        }

        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('rollback_note', 'Rollback Reason', 'required|trim');

        if ($this->form_validation->run() === false) {
            $this->set_flash_message('danger', validation_errors());
            redirect(page_url . 'Task_completion_control/view/' . $task_record_id);
        }

        $result = $this->task_completion_control->rollback_completed_task(
            $task_record_id,
            $this->get_current_user_id(),
            $this->input->post('rollback_note', true)
        );

        if (!empty($result['success'])) {
            $this->set_flash_message('success', $result['message']);
        } else {
            $this->set_flash_message('danger', $result['message']);
        }

        redirect(page_url . 'Task_completion_control/view/' . $task_record_id);
    }

    private function get_filters()
    {
        return array(
            'df_id' => (int) $this->input->get('df_id', true),
            'department_id' => (int) $this->input->get('department_id', true),
            'assigned_user' => (int) $this->input->get('assigned_user', true),
            'from_date' => trim((string) $this->input->get('from_date', true)),
            'to_date' => trim((string) $this->input->get('to_date', true)),
            'keyword' => trim((string) $this->input->get('keyword', true)),
            'limit' => 250
        );
    }

    private function require_access()
    {
        $current_user_id = $this->get_current_user_id();
        if (!in_array($current_user_id, $this->allowed_user_ids, true)) {
            $this->set_flash_message('danger', 'You do not have access to the completed task control module.');
            redirect(page_url . 'Dashboard');
        }

        return $current_user_id;
    }

    private function get_current_user_id()
    {
        return isset($this->session->userdata['logged_in']['user_id'])
            ? (int) $this->session->userdata['logged_in']['user_id']
            : 0;
    }

    private function set_flash_message($type, $message)
    {
        $type = trim((string) $type);
        if ($type === '') {
            $type = 'info';
        }

        $message = trim((string) $message);
        if ($message === '') {
            return;
        }

        $this->session->set_flashdata('message', '<div class="alert alert-' . $type . ' alert-dismissable">' . $message . '</div>');
    }
}
