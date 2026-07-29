<?php
class Production_model extends CI_Model {

    //var $table = 'production_logs';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Calculates the total time spent and total quantity produced for a specific drawing number.
     *
     * @param string $drawing_no The drawing number to summarize.
     * @return object An object containing the summary data.
     */
    public function get_drawing_summary($drawing_no) {
        // CORRECTED: Using `current_time` for the sum.
        $this->db->select_sum('cycle_time', 'total_time_spent'); 
        $this->db->select_sum('actual_qty', 'total_parts_produced');
        $this->db->where('drawing_no', $drawing_no);
        
        $query = $this->db->get('sector_6_misdata');
        
        return $query->row();
    }

    /**
     * Retrieves the detailed journey of a drawing number, including date, machine, operator, and time.
     *
     * @param string $drawing_no The drawing number to track.
     * @return array An array of objects, where each object is a step in the journey.
     */
    public function get_drawing_journey($drawing_no) {
        // Select all columns needed for the report view
        $this->db->select('
            working_date, 
            machine_no, 
            operator_name, 
            drawing_no,
            part_name,
            df_no,
            cycle_time, 
            planned_qty,
            actual_qty,
            current_time,
            operations
        ');
        $this->db->from('sector_6_misdata');
        $this->db->where('drawing_no', $drawing_no);
        $this->db->where('cycle_time >', 0); 
        $this->db->order_by('working_date', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }


    public function get_kpi_data($startDate, $endDate) {
        $this->db->select_sum('planned_qty', 'total_planned');
        $this->db->select_sum('actual_qty', 'total_produced');
        $this->db->select_sum('quantity_rejected', 'total_rejected');

        // Summing all downtime columns.
        // NOTE: This assumes your downtime columns store numeric values (minutes).
        $this->db->select_sum('b_d', 'downtime_breakdown');
        $this->db->select_sum('setting_s', 'downtime_setup');
        $this->db->select_sum('rm_short', 'downtime_rm_shortage');
        $this->db->select_sum('`no operator`', 'downtime_no_operator'); // Use backticks for column name with space
        $this->db->select_sum('other', 'downtime_other');

        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);

        $query = $this->db->get();
        $result = $query->row_array();

        // Calculate derived KPIs
        $kpis = [];
        $kpis['total_produced'] = (int) $result['total_produced'];
        $kpis['total_planned'] = (int) $result['total_planned'];
        $kpis['total_rejected'] = (int) $result['total_rejected'];
        $kpis['total_good_parts'] = $kpis['total_produced'] - $kpis['total_rejected'];

        // Calculate Plan Achievement
        $kpis['plan_achievement_percentage'] = ($kpis['total_planned'] > 0)
            ? round(($kpis['total_produced'] / $kpis['total_planned']) * 100, 2)
            : 0;

        // Calculate Overall Quality Yield
        $kpis['quality_yield_percentage'] = ($kpis['total_produced'] > 0)
            ? round(($kpis['total_good_parts'] / $kpis['total_produced']) * 100, 2)
            : 0;

        // Calculate Total Downtime in minutes
        $kpis['total_downtime_minutes'] = $result['downtime_breakdown'] + $result['downtime_setup'] + $result['downtime_rm_shortage'] + $result['downtime_no_operator'] + $result['downtime_other'];

        return $kpis;
    }

    /**
     * Fetches data for the daily production line chart.
     *
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array Data formatted for a chart library like Chart.js.
     */
    public function get_daily_production_trend($startDate, $endDate) {
        $this->db->select('production_date, SUM(actual_qty) as daily_total');
        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);
        $this->db->group_by('production_date');
        $this->db->order_by('production_date', 'ASC');

        $query = $this->db->get();
        $results = $query->result_array();

        // Format data for chart libraries (e.g., Chart.js)
        $chart_data = [
            'labels' => [],
            'data'   => [],
        ];
        foreach ($results as $row) {
            $chart_data['labels'][] = date('d M', strtotime($row['production_date'])); // Format date like "01 Jun"
            $chart_data['data'][] = (int) $row['daily_total'];
        }
        return $chart_data;
    }

    /**
     * Fetches aggregated data for the downtime reasons bar chart.
     *
     * @param string $startDate The start date of the period.
     * @param string $endDate   The end date of the period.
     * @return array Data formatted for a chart library.
     */
    public function get_downtime_by_reason($startDate, $endDate) {
        $this->db->select_sum('b_d', 'Breakdown');
        $this->db->select_sum('setting_s', 'Setup');
        $this->db->select_sum('rm_short', 'RM Shortage');
        $this->db->select_sum('`no operator`', 'No Operator'); // Use backticks
        $this->db->select_sum('other', 'Other');

        $this->db->from('production_logs');
        $this->db->where('production_date >=', $startDate);
        $this->db->where('production_date <=', $endDate);

        $query = $this->db->get();
        // Returns a single row with aliased sums, perfect for a bar chart
        return $query->row_array();
    }



    var $table = 'production_logs';
    // All 26 columns included for sorting reference
    var $column_order = array('log_id', 'production_date', 'shift', 'machine_no', 'operator_name', 'available_time', 'drawing_no', 'part_name', 'df_no', 'cycle_time_minutes', 'planned_qty', 'actual_qty', 'quantity_rejected', 'rejection_reason', 'b_d', 'setting_s', 'rm_short', '`no_operator`', 'other', 'total_time_minutes', 'setup', 'tool_used', 'operation', 'remarks', 'currenttime', 'created_at'); 
    var $column_search = array('log_id', 'machine_no', 'operator_name', 'part_name', 'drawing_no', 'df_no'); // Subset for main search query
    var $order = array('log_id' => 'asc'); // Default order

    private function _get_datatables_query() {
    
    // Select all columns explicitly
    $this->db->select('log_id, production_date, shift, machine_no, operator_name, available_time, drawing_no, part_name, df_no, cycle_time_minutes, planned_qty, actual_qty, quantity_rejected, rejection_reason, b_d, setting_s, rm_short, `no_operator`, other, total_time_minutes, setup, tool_used, operation, remarks, currenttime, created_at');
    $this->db->from($this->table);

    $i = 0;
    
    // START OF FIX: Check for the existence of the search value in the POST array
    if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
        foreach ($this->column_search as $item) {
            if($i===0) {
                $this->db->group_start(); 
                $this->db->like($item, $_POST['search']['value']);
            } else {
                $this->db->or_like($item, $_POST['search']['value']);
            }
            if(count($this->column_search) - 1 == $i)
                $this->db->group_end();
            $i++; // Increment moved inside the loop
        }
    }
    // END OF FIX
    
    if(isset($_POST['order'])) {
        $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if(isset($this->order)) {
        $order = $this->order;
        $this->db->order_by(key($order), $order[key($order)]);
    }
}

    public function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function update_record($log_id, $data) {
        $this->db->where('log_id', $log_id);
        return $this->db->update($this->table, $data);
    }

     public function get_distinct($column) {
        // Use backticks for columns that are SQL reserved words or have spaces
        $select_col = ($column === 'no_operator' || $column === 'setup' || $column === 'other' || $column === 'shift') ? "`$column`" : $column;
        $this->db->distinct();
        $this->db->select($select_col);
        $this->db->from($this->table);
        $this->db->where($select_col . ' IS NOT NULL');
        $this->db->where($select_col . ' != ""');
        $this->db->order_by($select_col, 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_monthly_production_summary() {
    $this->db->select("DATE_FORMAT(production_date, '%Y-%m') AS month, SUM(planned_qty) AS total_planned, SUM(actual_qty) AS total_actual");
    $this->db->from('production_logs');
    $this->db->where('production_date !=', '0000-00-00'); // Exclude null/invalid dates
    $this->db->group_by("DATE_FORMAT(production_date, '%Y-%m')");
    $this->db->order_by('month', 'ASC');
    
    $query = $this->db->get();
    return $query->result();
}

public function get_top_5_machine_performance() {
    // Select machine number and sum of planned/actual quantities
    $this->db->select("machine_no, SUM(planned_qty) AS total_planned, SUM(actual_qty) AS total_actual");
    $this->db->from('production_logs');
    // Ensure machine_no is valid
    $this->db->where('machine_no IS NOT NULL');
    $this->db->where('machine_no !=', '');
    // Group and sort to find the top performers by actual quantity
    $this->db->group_by('machine_no');
    $this->db->order_by('total_actual', 'DESC');
    $this->db->limit(5); 
    
    $query = $this->db->get();
    return $query->result();
}

public function insert_batch($data) {
    // CI's insert_batch function is optimized for inserting many rows at once
    return $this->db->insert_batch('production_logs', $data);
}


public function save_or_update_record($record_id, $field_name, $field_value, $base_data) {
        
        // Map friendly JS name 'no_operator' back to DB column name 'no operator'
        $db_field_name = ($field_name === 'no_operator') ? '`no operator`' : $field_name;
        $data = [$db_field_name => $field_value];
        
        // --- 1. Update Existing Record ---
        if ($record_id > 0) {
            $this->db->where('log_id', $record_id);
            $this->db->update('production_logs', $data);
            return $record_id;
        } 
        
        // --- 2. Insert New Record ---
        else {
            // Must contain default/base data for mandatory columns
            $insert_data = array_merge([
                'production_date' => $base_data['production_date'] ?? date('Y-m-d'),
                'shift'           => $base_data['shift'] ?? 'A', 
                'operator_name'   => $base_data['operator_name'] ?? 'N/A', 
                'machine_no'      => $base_data['machine_no'] ?? '',
                // Set defaults for mandatory integer fields to prevent SQL errors
                'available_time'  => (int)($base_data['available_time'] ?? 0),
                'b_d'             => (int)($base_data['b_d'] ?? 0),
                'setting_s'       => (int)($base_data['setting_s'] ?? 0),
                'rm_short'        => (int)($base_data['rm_short'] ?? 0),
                'other'           => (int)($base_data['other'] ?? 0),
                'created_at'      => date('Y-m-d H:i:s'),
                'currenttime'     => date('H:i:s')
            ], $data);
            
            // Cannot insert a log without a machine number
            if (empty($insert_data['machine_no'])) {
                 return false; 
            }

            $this->db->insert('production_logs', $insert_data);
            return $this->db->insert_id();
        }
    }
public function get_logs_by_date_and_user($date, $operator_name, $shift) {
        $this->db->select('
            log_id, production_date, shift, machine_no, operator_name, available_time, drawing_no, part_name, df_no, 
            cycle_time_minutes, planned_qty, actual_qty, quantity_rejected, rejection_reason, b_d, setting_s, 
            rm_short, `no_operator` AS no_operator, other, total_time_minutes, setup, tool_used, operation, 
            remarks, currenttime, created_at
        ');
        $this->db->from('production_logs');
        $this->db->where('production_date', $date);
        $this->db->where('operator_name', $operator_name);
        $this->db->where('shift', $shift);
        $this->db->order_by('log_id', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
}
