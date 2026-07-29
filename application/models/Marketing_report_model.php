<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Marketing_report_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();

	}

public function fetch_total_sales() {
    $this->db->select_sum('order_value'); // Replace 'sales_amount' with your sales column
    $query = $this->db->get('poreceived'); // Replace 'sales_table' with your table name
    $result = $query->row();
    return $result->order_value ? $result->order_value : 0; // Return total sales amount or 0 if no data
}


// Dashboard_model.php (Model)
public function fetch_total_quotations() {
    $this->db->select('lead_id'); // Assuming lead_id identifies unique leads
    $this->db->where('lead_status', 39); // Filter by lead_status = 39
    $this->db->group_by('lead_id'); // Ensure unique records
    $query = $this->db->get('progress_remarks'); // Replace with your table name

    return $query->num_rows(); // Return the count of unique quotations
}


public function fetch_total_orders_won() {
    $this->db->select('lead_id'); // Assuming lead_id identifies unique leads
    $this->db->where('lead_status', 35); // Replace 'order_status' and 'won' with actual column and value
    $this->db->group_by('lead_id'); // Ensure unique records
    $query = $this->db->get('progress_remarks'); // Replace with your table name

    return $query->num_rows(); // Return the count of unique orders won
}


public function fetch_new_clients() {
    $date_30_days_ago = date('Y-m-d', strtotime('-30 days')); // Calculate 30 days ago
    $this->db->select('COUNT(*) as total');
    $this->db->from('leads'); // Replace 'clients' with your actual table name
    $this->db->where('create_date >=', $date_30_days_ago); // Adjust 'created_at' based on your table column
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->row()->total;
    }
    return 0;
}

public function fetch_order_won() {
    $this->db->select('*') as total');
    $this->db->from('progress_remarks'); // Replace with the actual table name
    $this->db->where('lead_status', 35); // Fetch where lead_status is 35
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->row()->total;
    }
    return 0;
}

public function fetch_quotation_follow_up() {
    $subquery = $this->db->select('MAX(id) as last_id')
                         ->from('progress_remarks')
                         ->group_by('lead_id')
                         ->get_compiled_select();

    $this->db->select('COUNT(DISTINCT lead_id) as total');
    $this->db->from('progress_remarks');
    $this->db->where('lead_status', 33);
    $this->db->where("id IN ($subquery)", null, false); // Fetch only the last entry for each lead_id
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->row()->total;
    }
    return 0;
}

public function fetch_order_breakdown() {
    // Count Domestic Orders
    $this->db->select('COUNT(*) as total');
    $this->db->from('poreceived');
    $this->db->where('customer_currency', 'INR'); // Domestic condition
    $domestic_query = $this->db->get();
    $domestic_orders = $domestic_query->num_rows() > 0 ? $domestic_query->row()->total : 0;

    // Count International Orders
    $this->db->select('COUNT(*) as total');
    $this->db->from('poreceived');
    $this->db->where('customer_currency !=', 'INR'); // International condition
    $international_query = $this->db->get();
    $international_orders = $international_query->num_rows() > 0 ? $international_query->row()->total : 0;

    return [
        'domestic' => $domestic_orders,
        'international' => $international_orders
    ];
}





}

