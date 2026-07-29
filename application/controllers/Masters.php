<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masters extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Security: Ensure user is logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login'); 
        }
        
        // Load necessary libraries and helpers if not autoloaded
       $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('Master_profile_guard');
        $this->load->model('Service_deployment_type_model', 'deployment_type_model');
        $this->master_profile_guard->block_methods(
            array(
                'manage_lines',
                'add_line',
                'save_line',
                'get_line_details',
                'update_line',
                'manage_machines',
                'add_machine',
                'save_machine',
                'get_machine_details',
                'update_machine',
                'manage_supervisors',
                'get_users_by_dept',
                'get_system_user_details',
                'save_supervisor',
                'get_supervisor_details',
                'manage_deployment_types',
                'save_deployment_type',
                'get_deployment_type_details',
                'update_deployment_type'
            ),
            'This EA profile can work in the software but cannot change master setup data.'
        );
    }

    // ==============================================================
    // MODULE 1: PRODUCTION LINES (Category > Line)
    // ==============================================================

    /**
     * List all lines with DataTables support and Edit Modal
     */
    public function manage_lines() {
        $data['page_title'] = 'Manage Production Lines';
        
        // Order by latest created
        $this->db->order_by('line_id', 'DESC');
        $data['lines'] = $this->db->get('mst_lines')->result();
        
        $this->load->view('masters/manage_lines_view', $data);
    }

    /**
     * Show Add Line Form
     */
    public function add_line() {
        $data['page_title'] = 'Add Production Line';
        $this->load->view('masters/add_line_view', $data);
    }

    /**
     * Save New Line (AJAX)
     */
    public function save_line() {
        if ($this->input->is_ajax_request()) {
            
            $this->form_validation->set_rules('line_category', 'Category', 'required|trim');
            $this->form_validation->set_rules('line_name', 'Line Name', 'required|trim');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 0, 'message' => validation_errors()]);
                return;
            }

            $data = array(
                'line_category' => $this->input->post('line_category'),
                'line_name'     => $this->input->post('line_name'),
                'line_code'     => $this->input->post('line_code'),
                'description'   => $this->input->post('description'),
                'created_by'    => $this->session->userdata['logged_in']['user_id'],
                'status'        => 1
            );

            // Check for duplicates in same category
            $exist = $this->db->get_where('mst_lines', [
                'line_category' => $data['line_category'], 
                'line_name'     => $data['line_name']
            ])->num_rows();

            if($exist > 0){
                echo json_encode(['status' => 0, 'message' => 'This Line Name already exists in this Category.']);
            } else {
                if ($this->db->insert('mst_lines', $data)) {
                    // Success Flashdata handled by view via JS, or set here for next request
                    $this->session->set_flashdata('success', 'Line added successfully!');
                    echo json_encode(['status' => 1, 'redirect_url' => base_url('Masters/manage_lines')]);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Database Error: Could not save.']);
                }
            }
        }
    }

    /**
     * Get Single Line Data for Edit Modal (AJAX)
     */
    public function get_line_details() {
        $line_id = $this->input->post('line_id');
        $data = $this->db->get_where('mst_lines', ['line_id' => $line_id])->row();
        echo json_encode($data);
    }

    /**
     * Update Line Logic (AJAX)
     */
    public function update_line() {
        if ($this->input->is_ajax_request()) {
            $line_id = $this->input->post('edit_line_id');
            
            $this->form_validation->set_rules('edit_line_category', 'Category', 'required|trim');
            $this->form_validation->set_rules('edit_line_name', 'Line Name', 'required|trim');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 0, 'message' => validation_errors()]);
                return;
            }

            $data = array(
                'line_category' => $this->input->post('edit_line_category'),
                'line_name'     => $this->input->post('edit_line_name'),
                'line_code'     => $this->input->post('edit_line_code'),
                'description'   => $this->input->post('edit_description'),
                'status'        => $this->input->post('edit_status')
            );

            // Check Duplicate (excluding current record)
            $this->db->where('line_category', $data['line_category']);
            $this->db->where('line_name', $data['line_name']);
            $this->db->where('line_id !=', $line_id);
            $exist = $this->db->get('mst_lines')->num_rows();

            if($exist > 0){
                echo json_encode(['status' => 0, 'message' => 'Duplicate Line Name in this Category.']);
            } else {
                $this->db->where('line_id', $line_id);
                if ($this->db->update('mst_lines', $data)) {
                    $this->session->set_flashdata('success', 'Line updated successfully!');
                    echo json_encode(['status' => 1]);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'No changes made.']);
                }
            }
        }
    }


    // ==============================================================
    // MODULE 2: MACHINES (Linked to Lines)
    // ==============================================================

    public function manage_machines() {
        $data['page_title'] = 'Manage Machines';
        
        // Join with Lines to show Line Name in the table
        $this->db->select('mst_machines.*, mst_lines.line_name, mst_lines.line_category');
        $this->db->from('mst_machines');
        $this->db->join('mst_lines', 'mst_lines.line_id = mst_machines.line_id', 'left');
        $this->db->order_by('mst_machines.machine_id', 'DESC');
        $data['machines'] = $this->db->get()->result();
        
        // Fetch Lines for the Edit Modal Dropdown
        $data['lines'] = $this->db->where('status', 1)->get('mst_lines')->result();
        
        $this->load->view('masters/manage_machines_view', $data);
    }

    public function add_machine() {
        $data['page_title'] = 'Add New Machine';
        // Need Lines for the dropdown
        $data['lines'] = $this->db->where('status', 1)->get('mst_lines')->result();
        $this->load->view('masters/add_machine_view', $data);
    }

    public function save_machine() {
        if ($this->input->is_ajax_request()) {
            
            $this->form_validation->set_rules('line_id', 'Line', 'required');
            $this->form_validation->set_rules('machine_name', 'Machine Name', 'required|trim');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 0, 'message' => validation_errors()]);
                return;
            }

            $data = array(
                'line_id'      => $this->input->post('line_id'),
                'machine_name' => $this->input->post('machine_name'),
                'machine_code' => $this->input->post('machine_code'),
                'model_no'     => $this->input->post('model_no'),
                'description'  => $this->input->post('description'),
                'status'       => 1
            );

            if ($this->db->insert('mst_machines', $data)) {
                $this->session->set_flashdata('success', 'Machine added successfully!');
                echo json_encode(['status' => 1, 'redirect_url' => base_url('Masters/manage_machines')]);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Database Error']);
            }
        }
    }

    public function get_machine_details() {
        $id = $this->input->post('machine_id');
        $data = $this->db->get_where('mst_machines', ['machine_id' => $id])->row();
        echo json_encode($data);
    }

    public function update_machine() {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('edit_machine_id');
            
            $data = array(
                'line_id'      => $this->input->post('edit_line_id'),
                'machine_name' => $this->input->post('edit_machine_name'),
                'machine_code' => $this->input->post('edit_machine_code'),
                'model_no'     => $this->input->post('edit_model_no'),
                'status'       => $this->input->post('edit_status')
            );

            $this->db->where('machine_id', $id);
            if($this->db->update('mst_machines', $data)) {
                $this->session->set_flashdata('success', 'Machine updated!');
                echo json_encode(['status' => 1]);
            } else {
                echo json_encode(['status' => 0, 'message' => 'No changes made']);
            }
        }
    }


    // ==============================================================
    // MODULE 3: SUPERVISORS
    // ==============================================================

   public function manage_supervisors() {
        $data['page_title'] = 'Manage Supervisors';
        
        // 1. Fetch existing supervisors
        $data['supervisors'] = $this->db->get('mst_supervisors')->result();
        
        // 2. Fetch Departments for the Filter Dropdown
        $this->db->where('status', 1);
        $this->db->where('business_loc_id', 2); // Added as requested
        $this->db->order_by('department', 'ASC'); // Added as requested
        $data['departments'] = $this->db->get('departments')->result();

        $this->load->view('masters/manage_supervisors_view', $data);
    }

    public function get_users_by_dept() {
        $dept_id = $this->input->post('dept_id');
        
        // Fetch users active in this department
        $this->db->select('user_id, first_name, last_name');
        $this->db->where('department_id', $dept_id);
        $this->db->where('user_status', 1); // Assuming 1 = Active
        $this->db->order_by('first_name', 'ASC');
        $users = $this->db->get('system_users')->result();
        
        echo json_encode($users);
    }

    public function get_system_user_details() {
        $user_id = $this->input->post('user_id');
        
        // Fetch email, contact, and employee ID
        // Note: Adjust 'employee_id' / 'contact_number' based on your exact column names in system_users
        $this->db->select('email, contact_number as mobile, employee_code as emp_code, first_name, last_name');
        $this->db->where('user_id', $user_id);
        $user = $this->db->get('system_users')->row();
                         
        if ($user) {
            echo json_encode(['status' => 1, 'data' => $user]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Unified Function to Save OR Update Supervisor
     */
    public function save_supervisor() {
        if ($this->input->is_ajax_request()) {
            
            $this->form_validation->set_rules('user_id', 'System User', 'required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 0, 'message' => validation_errors()]);
                return;
            }

            $id = $this->input->post('supervisor_id'); // Hidden field
            $user_id = $this->input->post('user_id');

            // Check if this user is ALREADY a supervisor (to prevent duplicates)
            // Only check if we are ADDING a new record (id is empty) or changing the user
            if(empty($id)) {
                $exists = $this->db->get_where('mst_supervisors', ['user_id' => $user_id])->num_rows();
                if($exists > 0) {
                    echo json_encode(['status' => 0, 'message' => 'This user is already added as a Supervisor.']);
                    return;
                }
            }

            $data = array(
                'user_id'         => $user_id,
                'supervisor_name' => $this->input->post('supervisor_name'), // Copied from user selection
                'emp_code'        => $this->input->post('emp_code'),
                'contact_no'      => $this->input->post('contact_no'),
                'email'           => $this->input->post('email'),
                'status'          => 1
            );

            if(!empty($id)) {
                // UPDATE
                $this->db->where('supervisor_id', $id);
                $this->db->update('mst_supervisors', $data);
                $msg = 'Supervisor updated successfully!';
            } else {
                // INSERT
                $this->db->insert('mst_supervisors', $data);
                $msg = 'Supervisor added successfully!';
            }
            
            $this->session->set_flashdata('success', $msg);
            echo json_encode(['status' => 1, 'message' => $msg]);
        }
    }

    public function get_supervisor_details() {
        $id = $this->input->post('id');
        // We also join to get the department ID to pre-fill the dropdowns correctly
        $this->db->select('mst_supervisors.*, system_users.department_id');
        $this->db->join('system_users', 'system_users.user_id = mst_supervisors.user_id', 'left');
        $this->db->where('supervisor_id', $id);
        $data = $this->db->get('mst_supervisors')->row();
        
        echo json_encode($data);
    }


   // ... (Previous code remains same) ...

    // ==============================================================
    // MODULE 4: SERVICE DEPLOYMENT TYPES
    // ==============================================================

    public function manage_deployment_types() {
        $data['page_title'] = 'Manage Deployment Types';
        $data['deployment_types'] = $this->deployment_type_model->get_all();

        $this->load->view('masters/manage_deployment_types_view', $data);
    }

    public function save_deployment_type() {
        if ($this->input->is_ajax_request()) {
            $this->deployment_type_model->ensure_table();

            $this->form_validation->set_rules('deployment_type_value', 'Deployment Type Value', 'required|trim');
            $this->form_validation->set_rules('deployment_type_label', 'Deployment Type Label', 'required|trim');
            $this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|integer');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 0, 'message' => validation_errors()]);
                return;
            }

            $value = trim($this->input->post('deployment_type_value'));
            $label = trim($this->input->post('deployment_type_label'));

            if ($this->deployment_type_model->value_exists($value)) {
                echo json_encode(['status' => 0, 'message' => 'This deployment type value already exists.']);
                return;
            }

            if ($this->deployment_type_model->label_exists($label)) {
                echo json_encode(['status' => 0, 'message' => 'This deployment type label already exists.']);
                return;
            }

            $data = [
                'deployment_type_value' => $value,
                'deployment_type_label' => $label,
                'sort_order' => (int) $this->input->post('sort_order'),
                'status' => (int) $this->input->post('status') === 0 ? 0 : 1,
                'created_by' => $this->session->userdata['logged_in']['user_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->db->insert($this->deployment_type_model->get_table_name(), $data)) {
                echo json_encode(['status' => 1, 'message' => 'Deployment type added successfully!']);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Database error: could not save deployment type.']);
            }
        }
    }

    public function get_deployment_type_details() {
        if ($this->input->is_ajax_request()) {
            $deployment_type_id = (int) $this->input->post('deployment_type_id');
            $data = $this->deployment_type_model->get_by_id($deployment_type_id);
            echo json_encode($data);
        }
    }

    public function update_deployment_type() {
        if ($this->input->is_ajax_request()) {
            $this->deployment_type_model->ensure_table();

            $deployment_type_id = (int) $this->input->post('edit_deployment_type_id');

            $this->form_validation->set_rules('edit_deployment_type_value', 'Deployment Type Value', 'required|trim');
            $this->form_validation->set_rules('edit_deployment_type_label', 'Deployment Type Label', 'required|trim');
            $this->form_validation->set_rules('edit_sort_order', 'Sort Order', 'trim|integer');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 0, 'message' => validation_errors()]);
                return;
            }

            $value = trim($this->input->post('edit_deployment_type_value'));
            $label = trim($this->input->post('edit_deployment_type_label'));

            if ($this->deployment_type_model->value_exists($value, $deployment_type_id)) {
                echo json_encode(['status' => 0, 'message' => 'This deployment type value already exists.']);
                return;
            }

            if ($this->deployment_type_model->label_exists($label, $deployment_type_id)) {
                echo json_encode(['status' => 0, 'message' => 'This deployment type label already exists.']);
                return;
            }

            $data = [
                'deployment_type_value' => $value,
                'deployment_type_label' => $label,
                'sort_order' => (int) $this->input->post('edit_sort_order'),
                'status' => (int) $this->input->post('edit_status') === 0 ? 0 : 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->where('deployment_type_id', $deployment_type_id);
            if ($this->db->update($this->deployment_type_model->get_table_name(), $data)) {
                echo json_encode(['status' => 1, 'message' => 'Deployment type updated successfully!']);
            } else {
                echo json_encode(['status' => 0, 'message' => 'No changes were saved.']);
            }
        }
    }

    // ==============================================================
    // MODULE 5: JOB ALLOCATION
    // ==============================================================

    public function manage_jobs() {
        $data['page_title'] = 'Manage Job Allocations';
        
        // --- 1. SAFE SESSION HANDLING ---
        $user_session = $this->session->userdata('logged_in');
        $user_id = isset($user_session['user_id']) ? $user_session['user_id'] : 0;
        
        if (isset($user_session['role'])) {
            $role_id = $user_session['role'];
        } elseif (isset($user_session['role_id'])) {
            $role_id = $user_session['role_id'];
        } else {
            $role_id = 0; 
        }
        
        $ADMIN_ROLE_ID = 12;
        $shubhamsir = 139;
        $SPECIAL_USER_ID = 180; // The specific user granted admin rights
        $ravjeetnegi = 209;
        $himanshuid = 143;
        $negiji = 155;
         
        // --- 2. PERMISSION CHECK ---
        // Check if user is Role 12 OR User 180
        $is_admin = ($role_id == $ADMIN_ROLE_ID || $user_id == $SPECIAL_USER_ID || $user_id==$shubhamsir || $user_id==$ravjeetnegi || $user_id==$himanshuid|| $user_id==$negiji);

        // --- 3. PREPARE FILTER LOGIC ---
        $filter_supervisor_id = null;
        $should_show_data = true; 

        if (!$is_admin) {
            // Run a clean, separate query to check if user is a supervisor
            $supervisor_rec = $this->db->get_where('mst_supervisors', ['user_id' => $user_id])->row();
            
            if (!empty($supervisor_rec)) {
                // User IS a supervisor
                $filter_supervisor_id = $supervisor_rec->supervisor_id;
            } else {
                // User is NOT a supervisor and NOT admin/special user -> Block Data
                $should_show_data = false;
            }
        }

        // --- 4. BUILD MAIN QUERY ---
        $this->db->select('trn.*, m.machine_name, l.line_name, s.supervisor_name');
        $this->db->from('trn_machine_assignments trn');
        $this->db->join('mst_machines m', 'm.machine_id = trn.machine_id');
        $this->db->join('mst_lines l', 'l.line_id = trn.line_id');
        $this->db->join('mst_supervisors s', 's.supervisor_id = trn.supervisor_id');
        
        // Apply Permissions
        if (!$is_admin) {
            if ($should_show_data && $filter_supervisor_id) {
                // Filter by specific supervisor ID
                $this->db->where('trn.supervisor_id', $filter_supervisor_id);
            } else {
                // Show nothing (Safety catch)
                $this->db->where('1=0'); 
            }
        }

        // Sorting
        $this->db->order_by('trn.is_active', 'DESC');
        $this->db->order_by('trn.assignment_id', 'DESC');
        
        $data['jobs'] = $this->db->get()->result();
        
        // Pass the admin status to the view in case you need to show/hide buttons
        $data['is_admin'] = $is_admin; 
        $data['current_role'] = $role_id; 

        $this->load->view('production/manage_jobs_view', $data);
    }

   public function allocate_job() {
        $data['page_title'] = 'Allocate New Job';
        
        // 1. Fetch Active Lines
        $data['lines'] = $this->db->where('status', 1)->get('mst_lines')->result();
        
        // 2. Fetch Active Supervisors
        $this->db->select('mst_supervisors.*, d.department');
        $this->db->join('system_users u', 'u.user_id = mst_supervisors.user_id');
        $this->db->join('departments d', 'd.department_id = u.department_id', 'left');
        $this->db->where('mst_supervisors.status', 1);
        $data['supervisors'] = $this->db->get('mst_supervisors')->result();

        // 3. FETCH OPEN DFs (Filtered)
        // Step A: Get list of DFs currently running (is_active = 1)
        $this->db->select('df_number');
        $this->db->where('is_active', 1);
        $active_jobs = $this->db->get('trn_machine_assignments')->result_array();
        
        // Create a simple array of busy DF numbers
        $busy_dfs = [];
        if(!empty($active_jobs)) {
            $busy_dfs = array_column($active_jobs, 'df_number');
        }

        // Step B: Fetch DFs from Master that are NOT in the busy list
        $this->db->select('id, df_no, df_description, added_on');
        $this->db->where('df_status', 0);
        
        if (!empty($busy_dfs)) {
            $this->db->where_not_in('df_no', $busy_dfs);
        }
        
        $this->db->order_by('id', 'DESC');
        $data['dfs'] = $this->db->get('df_release')->result();

        $this->load->view('production/allocate_job_view', $data);
    }

   public function get_machines_by_line() {
        $line_id = $this->input->post('line_id');
        $machines = $this->db->where('line_id', $line_id)->where('status', 1)->get('mst_machines')->result();
        echo json_encode($machines);
    }

    public function save_allocation() {
        if ($this->input->is_ajax_request()) {
            
            // Set header to JSON to prevent parsing errors
            header('Content-Type: application/json');

            $this->form_validation->set_rules('line_id', 'Line', 'required');
            $this->form_validation->set_rules('machine_id', 'Machine', 'required');
            $this->form_validation->set_rules('df_no', 'DF Number', 'required|trim');
            $this->form_validation->set_rules('supervisor_id', 'Supervisor', 'required');
            $this->form_validation->set_rules('release_date', 'Release Date', 'required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 0, 'message' => validation_errors()]);
                exit; // STOP EXECUTION
            }

            // 1. Validation: Check if DF is already assigned
            $df_no = $this->input->post('df_no');
            $is_busy = $this->db->where('df_number', $df_no)
                                ->where('is_active', 1)
                                ->count_all_results('trn_machine_assignments');

            if($is_busy > 0) {
                echo json_encode(['status' => 0, 'message' => 'Error: This DF is already assigned to a running job!']);
                exit; // STOP EXECUTION
            }

            $data = array(
                'line_id'       => $this->input->post('line_id'),
                'machine_id'    => $this->input->post('machine_id'),
                'df_number'     => $this->input->post('df_no'),
                'supervisor_id' => $this->input->post('supervisor_id'),
                'release_date'  => $this->input->post('release_date'),
                'remarks'       => $this->input->post('remarks'),
                'is_active'     => 1, 
                'created_by'    => $this->session->userdata['logged_in']['user_id']
            );

            if ($this->db->insert('trn_machine_assignments', $data)) {
                $this->session->set_flashdata('success', 'Job allocated successfully!');
                echo json_encode(['status' => 1, 'redirect_url' => page_url.'Masters/manage_jobs']);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Database Error']);
            }
            exit; // STOP EXECUTION
        }
    }

    public function close_job() {
        $id = $this->input->post('id');
        $this->db->where('assignment_id', $id)->update('trn_machine_assignments', ['is_active' => 0]);
        echo json_encode(['status' => 1]);
    }

public function save_daily_progress() {
    if ($this->input->is_ajax_request()) {
        
        header('Content-Type: application/json');

        // --- SECURITY CHECK START ---
        $user_session = $this->session->userdata('logged_in');
        $user_id = isset($user_session['user_id']) ? $user_session['user_id'] : 0;
        $role_id = isset($user_session['role']) ? $user_session['role'] : (isset($user_session['role_id']) ? $user_session['role_id'] : 0);
        
        $ADMIN_ROLE_ID = 12; 
        $SPECIAL_USER_ID = 180;
        
        $assignment_id = $this->input->post('assignment_id');

        if (empty($assignment_id)) {
            echo json_encode(['status' => 0, 'message' => 'Invalid request. Assignment ID missing.']);
            exit;
        }

        // Check if user is NOT Admin and NOT the special user
        if ($role_id != $ADMIN_ROLE_ID && $user_id != $SPECIAL_USER_ID) {
            $job = $this->db->get_where('trn_machine_assignments', ['assignment_id' => $assignment_id])->row();
            $me = $this->db->get_where('mst_supervisors', ['user_id' => $user_id])->row();

            // Uncomment this when you want strict supervisor ownership validation
            /*
            if (!$job || !$me || $job->supervisor_id != $me->supervisor_id) {
                echo json_encode(['status' => 0, 'message' => 'Permission Denied: You can only update your assigned jobs.']);
                exit;
            }
            */
        }
        // --- SECURITY CHECK END ---

        $remarks = trim((string) $this->input->post('remarks'));
        if ($remarks === '') {
            echo json_encode(['status' => 0, 'message' => 'Error: Remarks are required.']);
            exit;
        }

        // Upload folder
        $upload_path = FCPATH . 'uploads/daily_logs/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // -------------------------------------------------
        // 1. Upload Site Image - Optional
        // -------------------------------------------------
        $site_image_name = '';

        $this->load->library('upload');
        if (!empty($_FILES['site_image']['name'])) {
            $image_config = array();
            $image_config['upload_path']   = $upload_path;
            $image_config['allowed_types'] = 'gif|jpg|jpeg|png';
            $image_config['max_size']      = 5120; // 5MB
            $image_config['encrypt_name']  = TRUE;

            $this->upload->initialize($image_config);

            if (!$this->upload->do_upload('site_image')) {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Image Upload Failed: ' . $this->upload->display_errors('', '')
                ]);
                exit;
            }

            $image_data = $this->upload->data();
            $site_image_name = $image_data['file_name'];
        }

        // -------------------------------------------------
        // 2. Upload Report Attachment - Optional
        // Allowed: PDF, Excel, Word, Images, CSV
        // -------------------------------------------------
        $report_attachment     = null;
        $report_original_name  = null;
        $report_file_type      = null;
        $report_file_size      = null;

        if (!empty($_FILES['report_attachment']['name'])) {

            $report_config = array();
            $report_config['upload_path']   = $upload_path;
            $report_config['allowed_types'] = 'pdf|xls|xlsx|csv|doc|docx|jpg|jpeg|png|gif';
            $report_config['max_size']      = 1024000; // 10MB
            $report_config['encrypt_name']  = TRUE;

            $this->upload->initialize($report_config);

            if (!$this->upload->do_upload('report_attachment')) {

                // Delete already uploaded site image if report upload fails
                if (!empty($site_image_name) && file_exists($upload_path . $site_image_name)) {
                    unlink($upload_path . $site_image_name);
                }

                echo json_encode([
                    'status' => 0,
                    'message' => 'Report Attachment Upload Failed: ' . $this->upload->display_errors('', '')
                ]);
                exit;

            } else {
                $report_data = $this->upload->data();

                $report_attachment    = $report_data['file_name'];
                $report_original_name = $report_data['orig_name'];
                $report_file_type     = $report_data['file_type'];
                $report_file_size     = $report_data['file_size']; // KB
            }
        }

        // -------------------------------------------------
        // 3. Save Log Data
        // -------------------------------------------------
        $data = array(
            'assignment_id'         => $assignment_id,
            'log_date'              => date('Y-m-d'),
            'log_time'              => date('H:i:s'),
            'progress_percent'      => $this->input->post('progress_percent'),
            'remarks'               => $remarks,
            'image_path'            => $site_image_name,

            // New report attachment fields
            'report_attachment'     => $report_attachment,
            'report_original_name'  => $report_original_name,
            'report_file_type'      => $report_file_type,
            'report_file_size'      => $report_file_size,

            'created_by'            => $user_id,
            'created_at'            => date('Y-m-d H:i:s')
        );

        if ($this->db->insert('trn_daily_logs', $data)) {
            $this->session->set_flashdata('success', 'Progress updated successfully!');
            echo json_encode([
                'status' => 1,
                'message' => 'Progress updated successfully!'
            ]);
        } else {

            // Delete uploaded files if DB insert fails
            if (!empty($site_image_name) && file_exists($upload_path . $site_image_name)) {
                unlink($upload_path . $site_image_name);
            }

            if (!empty($report_attachment) && file_exists($upload_path . $report_attachment)) {
                unlink($upload_path . $report_attachment);
            }

            echo json_encode([
                'status' => 0,
                'message' => 'Database Error. Please try again.'
            ]);
        }

        exit;
    }
}

    /**
     * 2. The "Beautiful" AI Timeline Report
     */
    public function view_job_timeline($assignment_id) {
        $data['page_title'] = 'Job Progress Timeline';
        
        // 1. Fetch Job Details
        $this->db->select('trn.*, m.machine_name, l.line_name, s.supervisor_name, df.df_description');
        $this->db->from('trn_machine_assignments trn');
        $this->db->join('mst_machines m', 'm.machine_id = trn.machine_id');
        $this->db->join('mst_lines l', 'l.line_id = trn.line_id');
        $this->db->join('mst_supervisors s', 's.supervisor_id = trn.supervisor_id');
        $this->db->join('df_release df', 'df.df_no = trn.df_number', 'left'); // Join for description
        $this->db->where('trn.assignment_id', $assignment_id);
        $data['job'] = $this->db->get()->row();

        if(!$data['job']) { show_404(); }

        // 2. Fetch Daily Logs (Newest First)
        $this->db->where('assignment_id', $assignment_id);
        $this->db->order_by('log_date', 'DESC');
        $this->db->order_by('log_time', 'DESC');
        $this->db->order_by('log_id', 'DESC');
        $data['logs'] = $this->db->get('trn_daily_logs')->result();

        // 3. "AI" Insight Calculation
        // Calculate "Days Since Last Update"
        $last_update = isset($data['logs'][0]) ? $data['logs'][0]->log_date : $data['job']->created_at;
        $days_diff = (strtotime(date('Y-m-d')) - strtotime($last_update)) / (60 * 60 * 24);
        
        if($days_diff > 2) {
            $data['ai_status'] = 'DELAYED';
            $data['ai_color'] = 'danger';
            $data['ai_message'] = 'Attention: No progress reported in 2+ days.';
        } elseif($days_diff > 1) {
             $data['ai_status'] = 'SLOW';
             $data['ai_color'] = 'warning';
             $data['ai_message'] = 'Warning: No update yesterday.';
        } else {
            $data['ai_status'] = 'ON TRACK';
            $data['ai_color'] = 'success';
            $data['ai_message'] = 'Good Job! Progress is being reported daily.';
        }

        $this->load->view('production/job_timeline_view', $data);
    }

    public function combined_live_progress(){
         $this->load->view('production/combined_live_progress');
    }


}
