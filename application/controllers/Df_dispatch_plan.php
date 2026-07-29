<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Df_dispatch_plan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in') == false) {
            redirect(page_url);
        }

        $this->load->model('Df_dispatch_plan_model', 'dispatch_plan');
    }

    public function index()
    {
        $financial_year = trim((string) $this->input->get('fy'));
        $month = trim((string) $this->input->get('month'));
        $status = trim((string) $this->input->get('status'));
        $search = trim((string) $this->input->get('search'));

        $range = $this->dispatch_plan->financial_year_range($financial_year);
        $current_month = date('Y-m');
        $requested_month_is_valid = preg_match('/^\d{4}-\d{2}$/', $month)
            && $month >= substr($range['start'], 0, 7)
            && $month <= substr($range['end'], 0, 7);

        if ($month !== 'all' && !$requested_month_is_valid) {
            $current_month_is_in_fy = $current_month >= substr($range['start'], 0, 7)
                && $current_month <= substr($range['end'], 0, 7);
            $month = $current_month_is_in_fy ? $current_month : substr($range['start'], 0, 7);
        }

        $data = array(
            'page_title' => 'DF Dispatch Morning Meeting',
            'financial_year' => $range['label'],
            'selected_month' => $month,
            'selected_status' => $status,
            'search' => $search,
            'tables_ready' => $this->dispatch_plan->tables_ready(),
            'plans' => array(),
            'summary' => null,
            'available_dfs' => array(),
            'users' => array()
        );

        if ($data['tables_ready']) {
            $data['plans'] = $this->dispatch_plan->get_plans($range['label'], $month, $status, $search);
            $data['summary'] = $this->dispatch_plan->get_summary($range['label']);
            $data['available_dfs'] = $this->dispatch_plan->get_available_dfs();
            $data['users'] = $this->dispatch_plan->get_users();
        }

        $this->load->view('production/df_dispatch_plan/index', $data);
    }

    public function save_plan()
    {
        $this->json_only();
        $this->require_tables();

        $plan_id = (int) $this->input->post('plan_id');
        $df_id = (int) $this->input->post('df_id');
        $df_no = trim((string) $this->input->post('df_no'));
        $planned_date = trim((string) $this->input->post('planned_dispatch_date'));
        $status = trim((string) $this->input->post('status'));
        $user_id = $this->current_user_id();

        if ($df_no === '' || !$this->valid_date($planned_date)) {
            $this->respond(false, 'DF number and a valid planned dispatch date are required.');
        }

        $allowed_statuses = array('Planned', 'In Progress', 'At Risk', 'Ready', 'Dispatched', 'On Hold');
        if (!in_array($status, $allowed_statuses)) {
            $status = 'Planned';
        }

        $data = array(
            'df_id' => $df_id > 0 ? $df_id : null,
            'df_no' => $df_no,
            'priority' => max(0, (int) $this->input->post('priority')),
            'model' => trim((string) $this->input->post('model')),
            'product' => trim((string) $this->input->post('product')),
            'automation' => trim((string) $this->input->post('automation')),
            'planned_dispatch_date' => $planned_date,
            'fat_date' => $this->nullable_date($this->input->post('fat_date')),
            'completion_percent' => max(0, min(100, (int) $this->input->post('completion_percent'))),
            'frame_status' => trim((string) $this->input->post('frame_status')),
            'design_owner' => trim((string) $this->input->post('design_owner')),
            'marketing_owner' => trim((string) $this->input->post('marketing_owner')),
            'status' => $status,
            'remarks' => trim((string) $this->input->post('remarks')),
            'updated_by' => $user_id,
            'updated_on' => date('Y-m-d H:i:s')
        );

        if ($plan_id <= 0) {
            $data['created_by'] = $user_id;
            $data['created_on'] = date('Y-m-d H:i:s');
        }

        $result = $this->dispatch_plan->save_plan($data, $plan_id);
        if (!$result) {
            $this->respond(false, 'Unable to save the dispatch plan.');
        }

        $this->respond(true, 'Dispatch plan saved successfully.', array('plan_id' => $plan_id > 0 ? $plan_id : $result));
    }

    public function sync_schedule()
    {
        $this->json_only();
        $this->require_tables();

        $financial_year = trim((string) $this->input->post('financial_year'));
        $result = $this->dispatch_plan->sync_from_pms_schedule($financial_year, $this->current_user_id());

        $this->respond(true, $result['inserted'] . ' new DF plan(s) imported from the PMS dispatch schedule.', $result);
    }

    public function dependencies($plan_id)
    {
        $this->json_only();
        $this->require_tables();

        $plan = $this->dispatch_plan->get_plan((int) $plan_id);
        if (!$plan) {
            $this->respond(false, 'Dispatch plan not found.');
        }

        $this->respond(true, 'Dependencies loaded.', array(
            'plan' => $plan,
            'dependencies' => $this->dispatch_plan->get_dependencies((int) $plan_id)
        ));
    }

    public function add_dependency()
    {
        $this->json_only();
        $this->require_tables();

        $plan_id = (int) $this->input->post('plan_id');
        $title = trim((string) $this->input->post('title'));
        $due_date = trim((string) $this->input->post('due_date'));

        if (!$this->dispatch_plan->get_plan($plan_id) || $title === '' || !$this->valid_date($due_date)) {
            $this->respond(false, 'Plan, dependency and due date are required.');
        }

        $allowed_departments = array('Design', 'Section 6', 'Purchase', 'BOP', 'Automation', 'Electrical', 'Gear Box', 'Production', 'Marketing', 'Other');
        $department = trim((string) $this->input->post('department'));
        if (!in_array($department, $allowed_departments)) {
            $department = 'Other';
        }

        $dependency_id = $this->dispatch_plan->add_dependency(array(
            'plan_id' => $plan_id,
            'department' => $department,
            'title' => $title,
            'owner_user_id' => (int) $this->input->post('owner_user_id'),
            'due_date' => $due_date,
            'priority' => in_array($this->input->post('priority'), array('Low', 'Medium', 'High', 'Critical')) ? $this->input->post('priority') : 'Medium',
            'status' => 'Open',
            'remarks' => trim((string) $this->input->post('remarks')),
            'created_by' => $this->current_user_id(),
            'created_on' => date('Y-m-d H:i:s'),
            'updated_by' => $this->current_user_id(),
            'updated_on' => date('Y-m-d H:i:s')
        ));

        if (!$dependency_id) {
            $this->respond(false, 'Unable to add the dependency.');
        }

        $this->respond(true, 'Dependency delegated successfully.', array('dependency_id' => $dependency_id));
    }

    public function update_dependency()
    {
        $this->json_only();
        $this->require_tables();

        $dependency_id = (int) $this->input->post('dependency_id');
        $status = trim((string) $this->input->post('status'));
        if ($dependency_id <= 0 || !in_array($status, array('Open', 'In Progress', 'Blocked', 'Closed'))) {
            $this->respond(false, 'Invalid dependency update.');
        }

        $data = array(
            'status' => $status,
            'updated_by' => $this->current_user_id(),
            'updated_on' => date('Y-m-d H:i:s')
        );
        if ($status === 'Closed') {
            $data['completed_on'] = date('Y-m-d H:i:s');
        } else {
            $data['completed_on'] = null;
        }

        if (!$this->dispatch_plan->update_dependency($dependency_id, $data)) {
            $this->respond(false, 'Unable to update the dependency.');
        }

        $this->respond(true, 'Dependency status updated.');
    }

    private function current_user_id()
    {
        $session = $this->session->userdata('logged_in');
        return isset($session['user_id']) ? (int) $session['user_id'] : 0;
    }

    private function valid_date($date)
    {
        $parsed = DateTime::createFromFormat('Y-m-d', (string) $date);
        return $parsed && $parsed->format('Y-m-d') === $date;
    }

    private function nullable_date($date)
    {
        $date = trim((string) $date);
        return $this->valid_date($date) ? $date : null;
    }

    private function json_only()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
        }
        $this->output->set_content_type('application/json');
    }

    private function require_tables()
    {
        if (!$this->dispatch_plan->tables_ready()) {
            $this->respond(false, 'Database tables are not ready. Run Database/df_dispatch_plan_001.sql first.');
        }
    }

    private function respond($status, $message, $data = array())
    {
        echo json_encode(array_merge(array('status' => $status, 'message' => $message), $data));
        exit;
    }
}
