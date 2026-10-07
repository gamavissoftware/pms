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
        $this->load->model('Task_management_model', 'task_module');
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
            'users' => array(),
            'missing_dfs' => array()
        );

        if ($data['tables_ready']) {
            // Pull the PMS dispatch schedule in on every load. The board used to wait
            // for someone to press Sync, so a DF released after the last sync - or one
            // whose dispatch date moved when it slipped - simply never showed up.
            $this->dispatch_plan->sync_from_pms_schedule($range['label'], $this->current_user_id(), false);

            $data['missing_dfs'] = $this->dispatch_plan->get_dfs_missing_from_board($range['label']);
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
            'machine_type' => trim((string) $this->input->post('machine_type')),
            'product' => trim((string) $this->input->post('product')),
            'automation' => trim((string) $this->input->post('automation')),
            'loading_status' => trim((string) $this->input->post('loading_status')),
            'planned_dispatch_date' => $planned_date,
            'dispatch_schedule' => trim((string) $this->input->post('dispatch_schedule')),
            'fat_date' => $this->nullable_date($this->input->post('fat_date')),
            'completion_percent' => max(0, min(100, (int) $this->input->post('completion_percent'))),
            'actual_planned_completion_date' => $this->nullable_date($this->input->post('actual_planned_completion_date')),
            'frame_status' => trim((string) $this->input->post('frame_status')),
            'design_owner' => trim((string) $this->input->post('design_owner')),
            'marketing_owner' => trim((string) $this->input->post('marketing_owner')),
            'section_6_shortage' => trim((string) $this->input->post('section_6_shortage')),
            'section_6_eol' => trim((string) $this->input->post('section_6_eol')),
            'purchase_shortage' => trim((string) $this->input->post('purchase_shortage')),
            'purchase_eol' => trim((string) $this->input->post('purchase_eol')),
            'bop_shortage' => trim((string) $this->input->post('bop_shortage')),
            'bop_eol' => trim((string) $this->input->post('bop_eol')),
            'automation_status' => trim((string) $this->input->post('automation_status')),
            'electrical_status' => trim((string) $this->input->post('electrical_status')),
            'gear_box_status' => trim((string) $this->input->post('gear_box_status')),
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

        $message = $result['inserted'] . ' new DF plan(s) imported from the PMS dispatch schedule.';
        if ($result['rescheduled'] > 0) {
            $message .= ' ' . $result['rescheduled'] . ' plan(s) moved to their revised dispatch date.';
        }

        $this->respond(true, $message, $result);
    }

    public function update_field()
    {
        $this->json_only();
        $this->require_tables();

        $plan_id = (int) $this->input->post('plan_id');
        $field = trim((string) $this->input->post('field'));
        $value = trim((string) $this->input->post('value'));
        $plan = $this->dispatch_plan->get_plan($plan_id);

        if (!$plan) {
            $this->respond(false, 'Dispatch plan not found.');
        }

        $text_fields = array(
            'machine_type' => 100,
            'product' => 255,
            'automation' => 100,
            'loading_status' => 2000,
            'frame_status' => 255,
            'section_6_shortage' => 255,
            'section_6_eol' => 255,
            'purchase_shortage' => 255,
            'purchase_eol' => 255,
            'bop_shortage' => 255,
            'bop_eol' => 255,
            'automation_status' => 255,
            'electrical_status' => 255,
            'gear_box_status' => 255,
            'remarks' => 5000
        );

        if ($field === 'fat_date' || $field === 'actual_planned_completion_date' || $field === 'planned_dispatch_date') {
            if ($value !== '' && !$this->valid_date($value)) {
                $this->respond(false, 'Select a valid date.');
            }
            if ($field === 'planned_dispatch_date' && $value === '') {
                $this->respond(false, 'Planned dispatch date is required.');
            }
            $value = $value === '' ? null : $value;
        } elseif ($field === 'priority') {
            $value = (string) max(0, min(9999, (int) $value));
        } elseif ($field === 'completion_percent') {
            $value = (string) max(0, min(100, (int) $value));
        } elseif ($field === 'status') {
            $allowed_statuses = array('Planned', 'In Progress', 'At Risk', 'Ready', 'Dispatched', 'On Hold');
            if (!in_array($value, $allowed_statuses, true)) {
                $this->respond(false, 'Invalid plan status.');
            }
        } elseif (isset($text_fields[$field])) {
            $value_length = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
            if ($value_length > $text_fields[$field]) {
                $this->respond(false, 'Value is too long for this field.');
            }
        } else {
            $this->respond(false, 'This field cannot be edited inline.');
        }

        if (!$this->dispatch_plan->update_plan_field($plan_id, $field, $value, $this->current_user_id())) {
            $this->respond(false, 'Unable to save this change. It may conflict with another record.');
        }

        $this->respond(true, 'Saved.', array('field' => $field, 'value' => $value === null ? '' : $value));
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

    public function history($plan_id)
    {
        $this->json_only();
        $this->require_tables();

        $plan = $this->dispatch_plan->get_plan((int) $plan_id);
        if (!$plan) {
            $this->respond(false, 'Dispatch plan not found.');
        }

        $this->respond(true, 'History loaded.', array(
            'plan' => array('id' => (int) $plan->id, 'df_no' => $plan->df_no),
            'logs' => $this->dispatch_plan->get_audit_logs((int) $plan_id)
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

        $allowed_departments = array('Design', 'Section 6', 'Section 6 EOL', 'Purchase', 'Purchase EOL', 'BOP', 'BOP EOL', 'Automation', 'Electrical', 'Gear Box', 'Production', 'Marketing', 'Other');
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

    public function add_shortage_tasks()
    {
        $this->json_only();
        $this->require_tables();

        $plan_id = (int) $this->input->post('plan_id');
        $category = trim((string) $this->input->post('category'));
        $tasks = json_decode((string) $this->input->post('tasks'), true);
        $allowed_categories = array('Section 6', 'Section 6 EOL', 'Purchase', 'Purchase EOL', 'BOP', 'BOP EOL');

        $plan = $this->dispatch_plan->get_plan($plan_id);
        if (!$plan) {
            $this->respond(false, 'Select a valid DF plan.');
        }
        if (!in_array($category, $allowed_categories, true)) {
            $this->respond(false, 'Select a valid shortage category.');
        }
        if (!is_array($tasks) || count($tasks) < 1 || count($tasks) > 25) {
            $this->respond(false, 'Add between 1 and 25 tasks.');
        }
        $rows = array();
        $now = date('Y-m-d H:i:s');
        $user_id = $this->current_user_id();
        foreach ($tasks as $index => $task) {
            $title = trim(isset($task['title']) ? (string) $task['title'] : '');
            $item_qty = trim(isset($task['item_qty']) ? (string) $task['item_qty'] : '');
            $pendency_from_date = trim(isset($task['pendency_from_date']) ? (string) $task['pendency_from_date'] : '');
            $owner_user_id = isset($task['owner_user_id']) ? (int) $task['owner_user_id'] : 0;
            $priority = trim(isset($task['priority']) ? (string) $task['priority'] : 'Medium');
            $remarks = trim(isset($task['remarks']) ? (string) $task['remarks'] : '');

            if ($title === '' || strlen($title) > 500) {
                $this->respond(false, 'Item ' . ($index + 1) . ' requires a valid description.');
            }
            if (!is_numeric($item_qty) || (float) $item_qty <= 0 || (float) $item_qty > 9999999999.99) {
                $this->respond(false, 'Item ' . ($index + 1) . ' requires a valid quantity.');
            }
            if (!$this->valid_date($pendency_from_date)) {
                $this->respond(false, 'Item ' . ($index + 1) . ' requires a valid pendency-from date.');
            }
            if (!in_array($priority, array('Low', 'Medium', 'High', 'Critical'), true)) {
                $priority = 'Medium';
            }
            if (strlen($remarks) > 5000) {
                $this->respond(false, 'Remarks are too long in task ' . ($index + 1) . '.');
            }

            $assignee = null;
            if ($owner_user_id > 0) {
                if (!$this->task_module->module_ready()) {
                    $this->respond(false, 'Task Management is not ready. Run its database migration before delegating shortage items.');
                }
                $assignee = $this->task_module->get_user($owner_user_id);
                if (empty($assignee)) {
                    $this->respond(false, 'The selected assignee for item ' . ($index + 1) . ' could not be found. Please select the user again.');
                }
            }

            $rows[] = array(
                'plan_id' => $plan_id,
                'department' => $category,
                'title' => $title,
                'item_qty' => number_format((float) $item_qty, 2, '.', ''),
                'pendency_from_date' => $pendency_from_date,
                'owner_user_id' => $owner_user_id > 0 ? $owner_user_id : null,
                // A delegated item is due by the DF's planned dispatch date; the
                // separately stored pendency date is the date the item became pending.
                'due_date' => $plan->planned_dispatch_date,
                'priority' => $priority,
                'status' => 'Open',
                'remarks' => $remarks,
                'created_by' => $user_id,
                'created_on' => $now,
                'updated_by' => $user_id,
                'updated_on' => $now,
                '_assignee' => $assignee
            );
        }

        $this->db->trans_start();
        $delegated_count = 0;
        foreach ($rows as &$row) {
            $assignee = $row['_assignee'];
            unset($row['_assignee']);
            if (empty($assignee)) {
                continue;
            }
            $task_details = $category . ' shortage for DF ' . $plan->df_no;
            $task_details .= "\nItem quantity: " . $row['item_qty'];
            $task_details .= "\nItem pendency from: " . date('d M Y', strtotime($row['pendency_from_date']));
            if ($row['remarks'] !== '') {
                $task_details .= "\n\n" . $row['remarks'];
            }
            $task_management_id = $this->task_module->create_task(array(
                'task_code' => '',
                'business_location_id' => !empty($assignee['business_location']) ? (int) $assignee['business_location'] : 2,
                'title' => $row['title'],
                'task_details' => $task_details,
                'reference_url' => page_url . 'Df_dispatch_plan?month=all',
                'attachment' => null,
                'priority' => strtoupper($row['priority']),
                'requested_due_date' => $row['due_date'],
                'committed_due_date' => $row['due_date'],
                'assigned_by_user_id' => $user_id,
                'assigned_to_user_id' => $row['owner_user_id'],
                'assigned_to_department_id' => !empty($assignee['department_id']) ? (int) $assignee['department_id'] : 0,
                'status' => 'OPEN',
                'progress_percent' => 0,
                'last_update_note' => 'Created from DF Dispatch Plan shortage delegation.',
                'created_on' => $now,
                'updated_on' => $now
            ));
            if ($task_management_id <= 0) {
                $this->db->trans_rollback();
                $this->respond(false, 'Unable to create the linked Task Management item.');
            }
            $task_code = $this->task_module->generate_task_code($task_management_id);
            $this->task_module->update_task($task_management_id, array('task_code' => $task_code));
            $this->task_module->add_update(array(
                'task_id' => $task_management_id,
                'update_type' => 'CREATED',
                'status' => 'OPEN',
                'progress_percent' => 0,
                'due_date' => $row['due_date'],
                'update_note' => $task_details,
                'created_by' => $user_id,
                'created_on' => $now
            ));
            $this->task_module->add_notifications($task_management_id, array($row['owner_user_id']), $task_code . ' assigned from DF Dispatch Plan.', page_url . 'Task_management/view/' . $task_management_id);
            $row['task_management_item_id'] = $task_management_id;
            $delegated_count++;
        }
        unset($row);

        if (!$this->dispatch_plan->add_dependencies($rows)) {
            $this->db->trans_rollback();
            $this->respond(false, 'Unable to save the shortage items. No items were saved.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->respond(false, 'Unable to save the shortage items. No items were saved.');
        }

        $submitted_count = count($rows) - $delegated_count;
        $message = count($rows) . ' shortage item(s) saved successfully.';
        if ($delegated_count > 0) {
            $message .= ' ' . $delegated_count . ' task(s) delegated.';
        }
        if ($submitted_count > 0 && $delegated_count > 0) {
            $message .= ' ' . $submitted_count . ' item(s) submitted without delegation.';
        }
        $this->respond(true, $message, array('count' => count($rows), 'delegated_count' => $delegated_count));
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
            $this->respond(false, 'Database setup is incomplete. Run the DF dispatch plan database migrations first.');
        }
    }

    private function respond($status, $message, $data = array())
    {
        echo json_encode(array_merge(array('status' => $status, 'message' => $message), $data));
        exit;
    }
}
