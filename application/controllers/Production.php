<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('production_model');
        $this->load->helper('url');
    }

    // Loads the main report view
    public function index() {
        // Fetch dropdown options for filters (optional, but highly recommended)
        $data['machines'] = $this->production_model->get_distinct('machine_no');
        $data['operators'] = $this->production_model->get_distinct('operator_name');
        $data['df_nos'] = $this->production_model->get_distinct('df_no');
        $data['drawing_nos'] = $this->production_model->get_distinct('drawing_no');
        $data['monthly_summary'] = $this->production_model->get_monthly_production_summary();
        $data['top_5_machines'] = $this->production_model->get_top_5_machine_performance();
        $this->load->view('production_logs_view', $data);
    }

    


    // Endpoint for DataTables server-side processing
    public function datatable_ajax() {
        
        // --- 1. Apply Custom Filters ---
        // $custom_filters will contain the data sent from the AJAX call's `data` function in the view.
        $custom_filters = $this->input->post('custom_filters');
        
        if ($custom_filters) {
            // Filter by Month (e.g., '2025-08')
            if (!empty($custom_filters['month'])) {
                $month = $custom_filters['month'];
                $this->db->where("DATE_FORMAT(production_date, '%Y-%m') = ", $month);
            }
            // Filter by Machine
            if (!empty($custom_filters['machine'])) {
                $this->db->where('machine_no', $custom_filters['machine']);
            }
            // Filter by Operator
            if (!empty($custom_filters['operator'])) {
                $this->db->where('operator_name', $custom_filters['operator']);
            }
            // Filter by DF No
            if (!empty($custom_filters['df_no'])) {
                $this->db->where('df_no', $custom_filters['df_no']);
            }
            // Filter by Drawing No
            if (!empty($custom_filters['drawing_no'])) {
                $this->db->where('drawing_no', $custom_filters['drawing_no']);
            }
        }
        
        $list = $this->production_model->get_datatables();
        $data = array();
        $no = $_POST['start'];
        
        // --- 2. Loop and Prepare Data (Using the Corrected Column Name) ---
        foreach ($list as $record) {
            $no++;
            $row = array();
            $id = $record->log_id;
            
            // Editable fields with 'data-name' matching database column
            // We use the new, correct field name: 'no_operator'
            
            $row[] = $id; // 0. ID
            $row[] = '<span contenteditable="true" data-name="production_date" data-id="'.$id.'">'.$record->production_date.'</span>'; // 1. Prod. Date
            $row[] = '<span contenteditable="true" data-name="shift" data-id="'.$id.'">'.$record->shift.'</span>'; // 2. Shift
            $row[] = $record->machine_no; // 3. Machine No.
            $row[] = '<span contenteditable="true" data-name="operator_name" data-id="'.$id.'">'.$record->operator_name.'</span>'; // 4. Operator Name
            $row[] = $record->available_time; // 5. Available Time
            $row[] = $record->drawing_no; // 6. Drawing No
            $row[] = $record->part_name; // 7. Part Name
            $row[] = $record->df_no; // 8. DF No
            $row[] = $record->cycle_time_minutes; // 9. Cycle Time Minutes
            $row[] = $record->planned_qty; // 10. Planned Qty
            $row[] = '<span contenteditable="true" data-name="actual_qty" data-id="'.$id.'">'.$record->actual_qty.'</span>'; // 11. Actual Qty
            $row[] = '<span contenteditable="true" data-name="quantity_rejected" data-id="'.$id.'">'.$record->quantity_rejected.'</span>'; // 12. Rejected Qty
            $row[] = $record->rejection_reason; // 13. Rejection Reason
            $row[] = '<span contenteditable="true" data-name="b_d" data-id="'.$id.'">'.$record->b_d.'</span>'; // 14. BD (Break Down)
            $row[] = '<span contenteditable="true" data-name="setting_s" data-id="'.$id.'">'.$record->setting_s.'</span>'; // 15. Setting
            $row[] = '<span contenteditable="true" data-name="rm_short" data-id="'.$id.'">'.$record->rm_short.'</span>'; // 16. RM Short
            
            // NOTE THE CHANGE: Accessing the corrected database column name.
            $row[] = '<span contenteditable="true" data-name="no_operator" data-id="'.$id.'">'.$record->no_operator.'</span>'; // 17. No Operator
            
            $row[] = '<span contenteditable="true" data-name="other" data-id="'.$id.'">'.$record->other.'</span>'; // 18. Other
            $row[] = $record->total_time_minutes; // 19. Total Time Minutes
            $row[] = $record->setup; // 20. Setup
            $row[] = $record->tool_used; // 21. Tools Used
            $row[] = $record->operation; // 22. Operation
            $row[] = '<span contenteditable="true" data-name="remarks" data-id="'.$id.'">'.$record->remarks.'</span>'; // 23. Remarks
            $row[] = $record->currenttime; // 24. Current Time
            $row[] = $record->created_at; // 25. Created At
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->production_model->count_all(),
            "recordsFiltered" => $this->production_model->count_filtered(),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    // Endpoint for inline saving (Update this to use 'no_operator')
    public function update_record_ajax() {
        $log_id = $this->input->post('pk');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        
        if ($log_id && $name && isset($value)) {
            // Note: Updated allowed_fields to include 'no_operator'
            $allowed_fields = array('production_date', 'shift', 'operator_name', 'actual_qty', 'quantity_rejected', 'rejection_reason', 'b_d', 'setting_s', 'rm_short', 'no_operator', 'other', 'remarks');
            
            if (!in_array($name, $allowed_fields)) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array('status' => 'error', 'message' => 'Invalid column name for editing.'));
                return;
            }

            $data = array($name => $value);
            
            if ($this->production_model->update_record($log_id, $data)) {
                echo json_encode(array('status' => 'success'));
                return;
            }
        }
        
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(array('status' => 'error', 'message' => 'Failed to save data.'));
    }

  public function entry() {
        // Fetch all necessary data for the view, returning arrays of strings
        $data['machines'] = $this->production_model->get_distinct('machine_no');
        $data['operators'] = $this->production_model->get_distinct('operator_name');
        $data['shifts'] = ['A', 'B', 'C'];
        $data['setup_options'] = $this->production_model->get_distinct('setup');
        $data['tool_options'] = $this->production_model->get_distinct('tool_used');
        $data['operation_options'] = $this->production_model->get_distinct('operation');
        
        // Note: The get_distinct function in the model returns a simple array of strings now.

        $default_date = date('Y-m-d');
        $default_shift = $data['shifts'][0] ?? 'A';
        
        // --- DEFINITIVE FIX FOR 'UNKNOWN COLUMN ARRAY' ERROR ---
        $default_operator = 'N/A'; // Default to a safe string
        if (!empty($data['operators'])) {
             // Use the first operator's name if the array is populated
             $default_operator = $data['operators'][0];
        }
        // --- END FIX ---
        
        // Initial log fetch for the default session
        $data['initial_logs'] = $this->production_model->get_logs_by_date_and_user(
            $default_date, 
            $default_operator, 
            $default_shift
        );

        $this->load->view('production/production_entry_view', $data);
    }

public function fetch_existing_logs_ajax() {
        $date = $this->input->post('production_date');
        $shift = $this->input->post('shift');
        $operator = $this->input->post('operator_name');

        if (empty($date) || empty($shift) || empty($operator)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing selection data.']);
            return;
        }

        $logs = $this->production_model->get_logs_by_date_and_user($date, $operator, $shift);
        
        echo json_encode(['status' => 'success', 'records' => $logs]);
    }
    
    

    public function auto_save_ajax() {
        $record_id = (int)$this->input->post('log_id');
        $field_name = $this->input->post('name');
        $field_value = $this->input->post('value');
        $base_data = $this->input->post('base_data'); 

        // Basic validation
        if (empty($field_name) || !isset($field_value)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing field name or value.']);
            return;
        }
        
        // Prevent inserting a new row if the user hasn't provided a machine number
        if ($record_id === 0 && empty($base_data['machine_no'])) {
             echo json_encode(['status' => 'error', 'message' => 'Machine No. is required to create a new log.']);
             return;
        }
        
        $new_id = $this->production_model->save_or_update_record($record_id, $field_name, $field_value, $base_data);

        if ($new_id) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Data saved.',
                'log_id' => $new_id 
            ]);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        }
    }



}