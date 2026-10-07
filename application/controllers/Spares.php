<?php
use Dompdf\Dompdf;
use Dompdf\Options;

defined('BASEPATH') OR exit('No direct script access allowed');

class Spares extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
			$this->load->model('Salescrm_model','salescrm');
			$this->load->model('Dashboard_model','dashboardmodel');
			$this->load->model('Spare_pi_model', 'spare_pi_model');
			$this->load->model('Spare_quotation_expiry_model', 'spare_quotation_expiry_model');
		date_default_timezone_set("Asia/Kolkata"); 

		$config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
		$this->email->set_mailtype("html");


// echo "<pre>";print_r($_SESSION);exit;
	if (!$this->session->userdata('logged_in'))
	        {
	            $this->session->set_flashdata('message','Session Logged Out. Login to continue', 'refresh');
	            redirect(page_url);
			}
		}

	    private function refresh_expired_spare_quotations()
    {
        return $this->spare_quotation_expiry_model->expire_stale_quotations(date('Y-m-d'));
    }

    private function get_logged_in_spares_user_id()
    {
        $logged_in = $this->session->userdata('logged_in');
        return !empty($logged_in['user_id']) ? (int) $logged_in['user_id'] : 0;
    }

    private function current_user_can_manage_all_spares_quotations()
    {
        $logged_in = $this->session->userdata('logged_in');
        $user_id = !empty($logged_in['user_id']) ? (int) $logged_in['user_id'] : 0;
        $role_id = !empty($logged_in['role']) ? (int) $logged_in['role'] : 0;

        return pms_is_super_admin()
            || in_array($role_id, array(12, 41), true)
            || in_array($user_id, array(61, 111, 114, 139, 161, 189), true);
    }
		public function leadform(){
		$this->load->view('spares/lead');
	}

	public function generateOppNo()
{
    // 1. Get the opportunity type from the AJAX POST request
    $op_type = $this->input->post('op_type');

    if (empty($op_type)) {
        // Stop if the type is not provided
        echo "0";
        return;
    }

    // 2. Determine the current financial year (e.g., '25-26')
    // The financial year in India runs from April 1st to March 31st.
    if (date('m') >= 4) { // Current month is April or later
        $financial_year = date('y') . '-' . (date('y') + 1);
    } else { // Current month is January, February, or March
        $financial_year = (date('y') - 1) . '-' . date('y');
    }

    // 3. Find the highest existing number for this type and financial year
    $this->db->select_max('op_increment_no', 'last_number');
    $this->db->from('opportunities'); // <-- IMPORTANT: Make sure this is your correct table name
    $this->db->where('op_type', $op_type);
    $this->db->where('financial_year', $financial_year); // <-- IMPORTANT: You may need to add this column to your table
    
    $query = $this->db->get();
    $result = $query->row();

    // 4. Calculate the next number
    $next_number = 1; // Default to 1 if no records are found
    if ($result && !empty($result->last_number)) {
        $next_number = $result->last_number + 1;
    }

    // 5. Send back ONLY the number as a plain text response
    echo $next_number;
}

 public function get_customer_by_company()
    {
        $searchTerm = $this->input->get('searchTerm', TRUE) ? $this->input->get('searchTerm', TRUE) : '';

        $this->db->select('customer_id, company_name, address, contact_person');
        $this->db->from('spares_customers');
        $this->db->where('status', '1');

        if (!empty($searchTerm)) {
            $this->db->group_start();
            $this->db->like('company_name', $searchTerm, 'both');
            $this->db->or_like('address', $searchTerm, 'both');
            $this->db->or_like('contact_person', $searchTerm, 'both');
            $this->db->group_end();
        }

        $this->db->limit(50);
        $query = $this->db->get();
        $json = [];

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $customer) {
                $displayText = $customer->company_name;
                $customerAddress = trim(preg_replace('/\s+/', ' ', (string) $customer->address));
                if ($customerAddress !== '') {
                    $displayText .= " | " . $customerAddress;
                }
                if (!empty($customer->contact_person)) {
                    $displayText .= " (" . $customer->contact_person . ")";
                }
                $json[] = ['id' => $customer->customer_id, 'text' => $displayText];
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }

    public function add_new_ajax_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('company', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('country', 'Country', 'trim|required');
        $this->form_validation->set_rules('brand', 'Brand', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            echo "0~" . strip_tags(validation_errors());
            return;
        }

        $brand_input = $this->input->post('brand');
        $brand_id_to_save = null;

        if (is_numeric($brand_input)) {
            $brand_id_to_save = (int)$brand_input;
        } else {
            $brand_name = trim($brand_input);
            $this->db->select('id')->from('spare_company_brand')->where('name', $brand_name);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $brand_id_to_save = $query->row()->id;
            } else {
                $this->db->insert('spare_company_brand', ['name' => $brand_name]);
                $brand_id_to_save = $this->db->insert_id();
            }
        }

        if (empty($brand_id_to_save)) {
            echo "0~Could not resolve the company brand.";
            return;
        }

        $data = array(
            'company_name'        => trim($this->input->post('company')),
            'brand_id'       => $brand_id_to_save,
            'country_id'          => $this->input->post('country'),
            'email'               => trim($this->input->post('email')),
            'address'             => trim($this->input->post('address')),
            'contact_person'      => trim($this->input->post('contactpersonname')),
            'contact_person_no'          => trim($this->input->post('personcontactno')),
            'alternate_contact_no'=> trim($this->input->post('acontactno')),
            'status'              => 1,
            'created_at'=>date('Y-m-d H:i:s')
        );

        if ($this->db->insert('spares_customers', $data)) {
            $customer_id = $this->db->insert_id();
            $this->sync_customer_to_sap('spares', $customer_id);
            echo "1~" . $customer_id;
        } else {
            echo "0~Failed to save customer to the database.";
        }
    }

    private function sync_customer_to_sap($source, $customer_id)
    {
        $customer_id = (int) $customer_id;
        if ($customer_id <= 0) {
            return;
        }

        $this->load->library('Sap_service');
        $result = $source === 'spares'
            ? $this->sap_service->sync_spares_customer($customer_id)
            : $this->sap_service->sync_marketing_customer($customer_id);

        if (empty($result['success']) && empty($result['skipped'])) {
            log_message('error', 'SAP customer sync failed for ' . $source . ' customer ' . $customer_id . ': ' . (isset($result['message']) ? $result['message'] : 'Unknown error'));
        }
    }

   public function getCustomerDetails_frommaster()
{
    $response_string = '';
    $customer_id = $this->input->post('custid');

    if (!$customer_id) {
        echo $response_string;
        return;
    }

    // Select only the columns needed by the form and JOIN to get the brand name.
    $this->db->select('
        c.address, 
        c.tax_number, 
        c.contact_person_no, 
        c.email, 
        c.country_id, 
        c.brand_id,
        b.name as brand_name 
    ');
    $this->db->from('spares_customers c');
    $this->db->join('spare_company_brand b', 'c.brand_id = b.id', 'left');
    $this->db->where('c.customer_id', $customer_id);

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        $row = $query->row();
        
        // Construct the pipe-delimited string. The order here MUST match the JavaScript parsing.
        $response_string = implode('|', [
            $row->address,  // Index 0
            $row->tax_number,           // Index 1
            $row->contact_person_no,    // Index 2
            $row->email,         // Index 3
            $row->country_id,    // Index 4
            $row->brand_name,    // Index 5
            $row->brand_id  // Index 6 (Brand ID)
        ]);
    }

    echo $response_string;
}

public function getCustomerCountry()
    {
        $customer_id = $this->input->post('customer_id');
        $response = ['success' => false, 'country_id' => null];
        if ($customer_id) {
            $this->db->select('country_id')->from('spares_customers')->where('id', $customer_id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $response['success'] = true;
                $response['country_id'] = $query->row()->country_id;
            }
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


function getcountrytaxinfo(){
	$countryid = $this->input->post('countryid');
	if($countryid<>''){
	$q = $this->db->select('taxtype')->from('countries')->where('country_id',$countryid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $rows);
		echo $rows->taxtype; exit;
	}
	
	}
	
}

 public function add_opportunity()
    {
    	$user_id=$_SESSION['logged_in']['user_id'];	
        // 1. Set validation rules for all the form fields
        // The form_validation library should be autoloaded for this to work.
        $this->form_validation->set_rules('op_date', 'Opportunity Date', 'required');
        $this->form_validation->set_rules('lsource', 'Source', 'required|numeric');
        $this->form_validation->set_rules('op_type', 'Opportunity Type', 'required|numeric');
        $this->form_validation->set_rules('op_no', 'Opportunity Number', 'required');
        $this->form_validation->set_rules('marketing', 'Marketing Person', 'required|numeric');
        $this->form_validation->set_rules('customer', 'Customer', 'required|numeric');
        //$this->form_validation->set_rules('brand', 'Brand', 'required|numeric');
        $this->form_validation->set_rules('country', 'Country', 'required|numeric');
        $this->form_validation->set_rules('address', 'Customer Address', 'required');
        $this->form_validation->set_rules('customercontactno', 'Contact Number', 'required');
        $this->form_validation->set_rules('customeremailid', 'Email ID', 'required');
        $this->form_validation->set_rules('customertype', 'Client Type', 'required|numeric');
        $this->form_validation->set_rules('probability', 'Probability', 'required|numeric');
        $this->form_validation->set_rules('product[]', 'Product', 'required|numeric');
        $this->form_validation->set_rules('qty[]', 'Quantity', 'required|numeric|greater_than[0]');

        // 2. Check if validation fails
        if ($this->form_validation->run() == FALSE) {
            // If validation fails, set an error message and reload the form view
            // The session library must be loaded for flashdata to work.
            $this->session->set_flashdata('message', validation_errors());
            // The url helper must be loaded for redirect() to work.
            redirect(page_url.'Spares/leadform'); 
            return;
        }

        // 3. Calculate the financial year for storage
        if (date('m') >= 4) { // April or later
            $financial_year = date('y') . '-' . (date('y') + 1);
        } else { // Jan, Feb, March
            $financial_year = (date('y') - 1) . '-' . date('y');
        }

        // 4. Prepare the main data array for the 'opportunities' table
        $opportunity_data = [
            'op_date'             => $this->input->post('op_date'),
            'op_no'               => $this->input->post('op_no'),
            'op_increment_no'     => $this->input->post('op_increment_no'),
            'op_type'             => $this->input->post('op_type'),
            'customer_id'         => $this->input->post('customer'),
            'brand_id'            => $this->input->post('brand'),
            'country_id'          => $this->input->post('country'),
            'customer_address'    => $this->input->post('address'),
            'customer_contact_no' => $this->input->post('customercontactno'),
            'customer_email'      => $this->input->post('customeremailid'),
            'source_id'           => $this->input->post('lsource'),
            'exhibition_id'       => ($this->input->post('lsource') == 8) ? $this->input->post('exhibitionname') : NULL,
            'marketing_person_id' => $this->input->post('marketing'),
            'client_type'         => $this->input->post('customertype'),
            'probability'         => $this->input->post('probability'),
            'remarks'             => $this->input->post('remarks'),
            'tax_number'          => $this->input->post('gst'),
            'financial_year'      => $financial_year,
            'added_by'=>$user_id,
            'created_at'=>date('Y-m-d H:i:s')
        ];

        // 5. Use a database transaction to ensure all or nothing is saved
        $this->db->trans_start();

        // Insert into the main opportunities table
        $this->db->insert('opportunities', $opportunity_data);
        $opportunity_id = $this->db->insert_id();

        // Prepare and insert product data into the 'opportunity_products' table
        $products = $this->input->post('product');
        $quantities = $this->input->post('qty');
        $product_batch_data = [];

        if (is_array($products)) {
            foreach ($products as $index => $product_id) {
                if (!empty($product_id) && isset($quantities[$index])) {
                    $product_batch_data[] = [
                        'opportunity_id' => $opportunity_id,
                        'product_id'     => $product_id,
                        'quantity'       => $quantities[$index]
                    ];
                }
            }
        }

        if (!empty($product_batch_data)) {
            $this->db->insert_batch('opportunity_products', $product_batch_data);
        }

        $data1 = array('lead_id'=>$opportunity_id,
        	'lead_stage'=>1,
        	'next_follow_date'=>date('Y-m-d'),
        	'remark_title'=>'New Opportunity',
        	'added_on'=>date('Y-m-d H:i:s'),
        	'added_by'=>$user_id);
        $this->db->insert('spare_progress_remarks',$data1);
        $this->db->trans_complete();

        // 6. Check transaction status and set appropriate message
        if ($this->db->trans_status() === FALSE) {
            // Transaction failed
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Error: Could not save the opportunity. Please try again.</div>');
        } else {
            // Transaction succeeded
            $this->session->set_flashdata('message', '<div class="alert alert-success">Success! Opportunity ' . $opportunity_data['op_no'] . ' has been created.</div>');
        }

        // 7. Redirect back to the form page
        redirect(page_url.'Spares/leadform');
    }

public function get_products_ajax()
{
    // Use CodeIgniter's input class for security
    $searchTerm = $this->input->get('searchTerm', TRUE) ? $this->input->get('searchTerm', TRUE) : '';

    // Select the necessary columns from your spare_parts table
    $this->db->select('id, code, description');
    $this->db->from('spare_parts_for_trading');
    $this->db->where('status', '1'); // Only search for active parts

    // If a search term is provided, search in both code and description
    if (!empty($searchTerm)) {
        $this->db->group_start();
        $this->db->like('code', $searchTerm, 'both');
        $this->db->or_like('description', $searchTerm, 'both');
        $this->db->group_end();
    }

    $this->db->limit(50); // Limit results for performance
    $query = $this->db->get();

    $json = [];

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $part) {
            // Build a descriptive text string: "[CODE] - DESCRIPTION"
            $displayText = '[' . $part->code . '] - ' . trim($part->description);
            $json[] = ['id' => $part->id, 'text' => $displayText];
        }
    }

    // Set the content type header and echo the encoded data
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($json));
}

public function opportunity_list()
{
    $this->refresh_expired_spare_quotations();
    $data['cancelled_quotation_stage_id'] = $this->spare_quotation_expiry_model->get_cancelled_stage_id();
    $data['can_reopen_all_cancelled'] = $this->current_user_can_manage_all_spares_quotations();
    $data['current_user_id'] = $this->get_logged_in_spares_user_id();

    // --- 1. Fetch data for filter dropdowns ---
    $this->db->select('user_id, first_name, last_name');
    $this->db->from('system_users');
    $this->db->where('department_id', 31);
    $this->db->where('user_status', 1);
    $data['marketing_persons'] = $this->db->get()->result();

    $this->db->select('lead_id, lead_name');
    $this->db->from('spare_lead_stage');
    $this->db->order_by('sort_order', 'ASC');
    $data['stages'] = $this->db->get()->result();

    // --- 2. Build the main query ---

    // This subquery is used to DISPLAY the current stage name.
    $latest_remark_subquery = "(SELECT MAX(id) FROM spare_progress_remarks WHERE lead_id = op.opportunity_id)";
    
    // ** NEW: Subquery to get the latest quotation ID for the view link **
    $latest_quotation_subquery = "(SELECT quotation_id FROM quotations WHERE opportunity_id = op.opportunity_id ORDER BY quotation_id DESC LIMIT 1)";

    $this->db->select("
        op.opportunity_id, 
        op.op_no, 
        op.op_date, 
        op.op_type,
        op.status,
        op.probability,
        op.marketing_person_id,
        cm.company_name, 
        ls.lead_source,
        CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,
        sls.lead_name as current_stage_name,
        spr.lead_stage as current_stage_id,
        {$latest_quotation_subquery} as latest_quotation_id,
        (SELECT quotation_date FROM quotations WHERE opportunity_id = op.opportunity_id ORDER BY quotation_id DESC LIMIT 1) as latest_quotation_date
    ");
    $this->db->from('opportunities op');
    $this->db->join('spares_customers cm', 'cm.customer_id = op.customer_id', 'left');
    $this->db->join('system_users u', 'u.user_id = op.marketing_person_id', 'left');
    $this->db->join('lead_source ls', 'ls.source_id = op.source_id', 'left');
    // These joins get the stage name for display purposes
    $this->db->join('spare_progress_remarks spr', 'spr.id = '.$latest_remark_subquery, 'left');
    $this->db->join('spare_lead_stage sls', 'sls.lead_id = spr.lead_stage', 'left');

    // --- 3. Apply filters from the GET request ---
    if ($this->input->get('from_date')) {
        $this->db->where('op.op_date >=', $this->input->get('from_date'));
    }
    if ($this->input->get('to_date')) {
        $this->db->where('op.op_date <=', $this->input->get('to_date'));
    }
    if ($this->input->get('marketing_person')) {
        $this->db->where('op.marketing_person_id', $this->input->get('marketing_person'));
    }
    if ($this->input->get('op_type')) {
        $this->db->where('op.op_type', $this->input->get('op_type'));
    }
    if ($this->input->get('status')) {
        $this->db->where('op.status', $this->input->get('status'));
    }

    // ** CORRECTED: A more reliable stage filter using a subquery **
    if ($this->input->get('stage')) {
        $stage_id = (int)$this->input->get('stage');
        $stage_filter_subquery = "(SELECT lead_stage FROM spare_progress_remarks WHERE lead_id = op.opportunity_id ORDER BY id DESC LIMIT 1)";
        // The 'FALSE' argument is crucial to prevent CodeIgniter from escaping the subquery
        $this->db->where("{$stage_filter_subquery} = ", $stage_id, FALSE);
    }

    // --- 4. Order and Execute the query ---
    $this->db->order_by('op.opportunity_id', 'DESC');
    $query = $this->db->get();
    $data['opportunities'] = $query->result();

    // --- 5. Load the view with all the data ---
    $this->load->view('spares/opportunity_list_view', $data);
}


public function opportunity_stage_wise($stage_id = NULL)
{
    $this->refresh_expired_spare_quotations();
    // --- 0. Validate input and get stage info for the title ---
    if (!$stage_id || !is_numeric($stage_id)) {
        // Redirect or show an error if the stage ID is missing or invalid
        redirect(page_url . 'Spares/opportunity_list');
    }

    $this->ensure_spare_pi_dependencies();
    $data['stage_info'] = $this->db->get_where('spare_lead_stage', ['lead_id' => $stage_id])->row();
    $data['cancelled_quotation_stage_id'] = $this->spare_quotation_expiry_model->get_cancelled_stage_id();
    $data['can_reopen_all_cancelled'] = $this->current_user_can_manage_all_spares_quotations();
    $data['current_user_id'] = $this->get_logged_in_spares_user_id();

    // If no stage is found for the given ID, redirect back
    if (!$data['stage_info']) {
        redirect(page_url . 'Spares/opportunity_list');
    }

    $create_pi_stage_id = (int) $this->dashboardmodel->get_spares_create_pi_stage_id();
    $pi_ready_stage_id = (int) $this->dashboardmodel->get_spares_pi_ready_stage_id();
    $data['is_create_pi_stage'] = ((int) $stage_id === $create_pi_stage_id);

    // --- 1. Fetch data for filter dropdowns (same as before) ---
    $this->db->select('user_id, first_name, last_name');
    $this->db->from('system_users');
    $this->db->where('department_id', 31);
    $this->db->where('user_status', 1);
    $data['marketing_persons'] = $this->db->get()->result();
    
    // You don't need to fetch all stages for the filter dropdown on this page, but it doesn't hurt to keep it
    $this->db->select('lead_id, lead_name');
    $this->db->from('spare_lead_stage');
    $this->db->order_by('sort_order', 'ASC');
    $data['stages'] = $this->db->get()->result();


    // --- 2. Build the main query (same as before) ---
    $latest_remark_subquery = "(SELECT MAX(id) FROM spare_progress_remarks WHERE lead_id = op.opportunity_id)";
    $latest_quotation_subquery = "(SELECT quotation_id FROM quotations WHERE opportunity_id = op.opportunity_id ORDER BY quotation_id DESC LIMIT 1)";

    $this->db->select("
        op.opportunity_id, 
        op.op_no, 
        op.op_date, 
        op.op_type,
        op.status,
        op.probability,
        op.marketing_person_id,
        cm.company_name, 
        ls.lead_source,
        CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,
        sls.lead_name as current_stage_name,
        spr.lead_stage as current_stage_id,
        {$latest_quotation_subquery} as latest_quotation_id,
        (SELECT quotation_date FROM quotations WHERE opportunity_id = op.opportunity_id ORDER BY quotation_id DESC LIMIT 1) as latest_quotation_date
    ");
    $this->db->from('opportunities op');
    $this->db->join('spares_customers cm', 'cm.customer_id = op.customer_id', 'left');
    $this->db->join('system_users u', 'u.user_id = op.marketing_person_id', 'left');
    $this->db->join('lead_source ls', 'ls.source_id = op.source_id', 'left');
    $this->db->join('spare_progress_remarks spr', 'spr.id = '.$latest_remark_subquery, 'left');
    $this->db->join('spare_lead_stage sls', 'sls.lead_id = spr.lead_stage', 'left');


    // --- 3. Apply Filters ---

    // **MANDATORY STAGE FILTER FROM URL**
    // This is the main logic for this page. It finds the latest stage for an opportunity and checks if it matches the one from the URL.
    $stage_filter_subquery = "(SELECT lead_stage FROM spare_progress_remarks WHERE lead_id = op.opportunity_id ORDER BY id DESC LIMIT 1)";
    if (!empty($data['is_create_pi_stage'])) {
        $this->db->where("{$stage_filter_subquery} = ", $pi_ready_stage_id, FALSE);
        $this->db->where("EXISTS (SELECT 1 FROM quotations q WHERE q.opportunity_id = op.opportunity_id)", null, false);
        $this->db->where("NOT EXISTS (SELECT 1 FROM spare_proforma_invoices spi WHERE spi.opportunity_id = op.opportunity_id)", null, false);

        if (
            $_SESSION['logged_in']['role'] != 12 &&
            $_SESSION['logged_in']['role'] != 41 &&
            $_SESSION['logged_in']['user_id'] != 114 &&
            $_SESSION['logged_in']['user_id'] != 189 &&
            $_SESSION['logged_in']['user_id'] != 111
        ) {
            $this->db->where('op.added_by', (int) $_SESSION['logged_in']['user_id']);
        }
    } else {
        $this->db->where("{$stage_filter_subquery} = ", (int)$stage_id, FALSE);
    }


    // Apply optional filters from the GET request (form submission)
    if ($this->input->get('from_date')) {
        $this->db->where('op.op_date >=', $this->input->get('from_date'));
    }
    if ($this->input->get('to_date')) {
        $this->db->where('op.op_date <=', $this->input->get('to_date'));
    }
    if ($this->input->get('marketing_person')) {
        $this->db->where('op.marketing_person_id', $this->input->get('marketing_person'));
    }
    if ($this->input->get('op_type')) {
        $this->db->where('op.op_type', $this->input->get('op_type'));
    }
    if ($this->input->get('status')) {
        $this->db->where('op.status', $this->input->get('status'));
    }
    
    // Note: The filter for 'stage' from the GET request is intentionally removed, 
    // as this page is already filtered by the stage in the URL.

    // --- 4. Order and Execute the query ---
    $this->db->order_by('op.opportunity_id', 'DESC');
    $query = $this->db->get();
    $data['opportunities'] = $query->result();

    // --- 5. Load the new view with all the data ---
    $this->load->view('spares/opportunity_stage_wise_view', $data);
}


/**
 * NEW function to get products for a specific opportunity via AJAX.
 * This is called by the JavaScript in the view file.
 * @param int $opportunity_id The ID of the opportunity
 */
public function get_opportunity_products_ajax($opportunity_id)
{
    // Sanitize the input
    $op_id = (int) $opportunity_id;
    if ($op_id <= 0) {
        echo '<p class="text-danger">Invalid Opportunity ID.</p>';
        return;
    }

    // Query to get product name and quantity for the given opportunity
    $this->db->select('sp.code, sp.description, op.quantity');
    $this->db->from('opportunity_products op');
    // Assuming your products table is `spare_products` with columns `id` and `name`
    $this->db->join('spare_parts_for_trading sp', 'sp.id = op.product_id', 'left');
    $this->db->where('op.opportunity_id', $op_id);
    $products = $this->db->get()->result();

    // Build the HTML for the nested table
    if (!empty($products)) {
        $output = '<table class="table table-bordered products-table">';
        $output .= '<thead><tr><th>Product Name</th><th>Quantity</th></tr></thead>';
        $output .= '<tbody>';
        foreach ($products as $product) {
            $output .= '<tr>';
            $output .= '<td>' . htmlspecialchars($product->code." ".$product->description) . '</td>';
            $output .= '<td>' . htmlspecialchars($product->quantity) . '</td>';
            $output .= '</tr>';
        }
        $output .= '</tbody></table>';
    } else {
        $output = '<p class="text-center">No products found for this opportunity.</p>';
    }

    // Echo the final HTML and stop script execution
    echo $output;
    exit;
}


public function opportunity_detail($id)
{
    $this->refresh_expired_spare_quotations();
    $this->load->model('opportunity_model');
    $this->ensure_spare_quotation_charge_mode_columns();

    $data['opportunity'] = $this->opportunity_model->get_opportunity_details($id);

    if (!$data['opportunity']) {
        show_404();
    }

    $data['products'] = $this->opportunity_model->get_opportunity_products($id); 
    $data['history'] = $this->opportunity_model->get_opportunity_history($id);
    $data['quotations'] = $this->opportunity_model->get_opportunity_quotations($id);
    $this->ensure_spare_pi_dependencies();
    $data['spare_pi'] = $this->spare_pi_model->get_by_opportunity($id);
    $data['quotation_cancellation'] = $this->spare_quotation_expiry_model->get_active_cancellation($id);
    $data['is_cancelled_quotation'] = !empty($data['quotation_cancellation']);
    $data['can_reopen_quotation'] = $data['is_cancelled_quotation'] && (
        $this->current_user_can_manage_all_spares_quotations()
        || (int) $data['opportunity']->marketing_person_id === $this->get_logged_in_spares_user_id()
    );

    // Fetch received Purchase Orders with the uploaded file name
    $this->db->select('po_id, po_no, po_date, grand_total, po_copy');
    $this->db->from('purchase_orders');
    $this->db->where('opportunity_id', $id);
    $this->db->order_by('po_id', 'DESC');
    $data['purchase_orders'] = $this->db->get()->result();

    // NEW: Fetch Final Sales Orders (Won Orders)
    $this->db->select('order_id, order_date, order_value, po_id');
    $this->db->from('spares_orders');
    $this->db->where('opportunity_id', $id);
    $this->db->order_by('order_id', 'DESC');
    $data['sales_orders'] = $this->db->get()->result();

    $next_stages_raw = $data['is_cancelled_quotation']
        ? array()
        : $this->opportunity_model->get_next_stages($data['opportunity']->current_stage_id);
    $data['next_stages'] = [];
    if (!empty($next_stages_raw)) {
        $this->db->select('lead_id');
        $this->db->from('spare_lead_stage');
        $this->db->where('followup_date', 1);
        $stages_req_followup = array_column($this->db->get()->result_array(), 'lead_id');

        foreach ($next_stages_raw as $stage) {
            $stage['followup_date_required'] = in_array($stage['lead_id'], $stages_req_followup);
            $data['next_stages'][] = $stage;
        }
    }

    $this->load->view('spares/opportunity_detail_view', $data);
}


public function update_opportunity_progress($id)
{
    $this->output->set_content_type('application/json');

    if ($this->spare_quotation_expiry_model->get_active_cancellation($id)) {
        $this->output->set_status_header(409);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Reopen this cancelled quotation before moving its pipeline stage.'
        ));
        return;
    }

    $logged_in = $this->session->userdata('logged_in');
    if (empty($logged_in['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Authentication error. Please log in again.']);
        return;
    }

    $this->load->model('opportunity_model');
    $this->load->library('form_validation');

    $this->form_validation->set_rules('new_stage_id', 'New Stage', 'required|integer');
    $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');
    
    // If validation fails, return an error message
    if ($this->form_validation->run() == FALSE) {
        echo json_encode(['status' => 'error', 'message' => strip_tags(validation_errors())]);
        return;
    }

    $user_id = (int) $logged_in['user_id'];
    $new_stage_id = (int) $this->input->post('new_stage_id');
    $followup_date = trim((string) $this->input->post('followup_date'));
    $new_stage_details = $this->db->get_where('spare_lead_stage', ['lead_id' => $new_stage_id])->row();

    if (!$new_stage_details) {
         echo json_encode(['status' => 'error', 'message' => 'Invalid stage selected.']);
        return;
    }

    $next_follow_date = null;
    if ($followup_date !== '') {
        $followup_timestamp = strtotime($followup_date);
        if ($followup_timestamp === false) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid follow-up date selected.']);
            return;
        }

        $next_follow_date = date('Y-m-d', $followup_timestamp);
    }

    if ((int) $new_stage_details->followup_date === 1 && empty($next_follow_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Next follow-up date is required for this stage.']);
        return;
    }

    // Data for the history/remarks table
    $remark_data = [
        'lead_id'           => $id,
        'lead_stage'       => $new_stage_id,
        'next_follow_date'  => $next_follow_date,
        'remarks'           => $this->input->post('remarks'),
        'remark_title'      => $new_stage_details->lead_name,
        'added_on'          => date('Y-m-d H:i:s'),
        'added_by'          => $user_id
    ];

    // --- Start Transaction ---
    $this->db->trans_start();

    $this->opportunity_model->add_progress_remark($remark_data);

    $opportunity_update_data = [];
    if ($new_stage_id == 10) { // Order Won
        $opportunity_update_data['status'] = 'Won';
        $opportunity_update_data['probability'] = 100;
    } elseif ($new_stage_details->dead_end == 1) { // Lead Lost
        $opportunity_update_data['status'] = 'Lost';
        $opportunity_update_data['probability'] = 0;
    }

    if ($new_stage_id === 5) {
        $opportunity_update_data['approval_status'] = 'Approved';
    } elseif ($new_stage_id === 6) {
        $opportunity_update_data['approval_status'] = 'Rejected';
    }

    if (!empty($opportunity_update_data)) {
        $this->opportunity_model->update_main_opportunity_status($id, $opportunity_update_data);
    }

    $this->db->trans_complete();
    // --- End Transaction ---

    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: Could not save progress.']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Opportunity progress updated successfully!']);
    }
}

public function reopen_cancelled_quotation($opportunity_id)
{
    if (strtoupper((string) $this->input->method()) !== 'POST') {
        show_error('Method not allowed.', 405);
        return;
    }

    $opportunity_id = (int) $opportunity_id;
    $opportunity = $this->db->select('opportunity_id, marketing_person_id')
        ->from('opportunities')
        ->where('opportunity_id', $opportunity_id)
        ->limit(1)
        ->get()
        ->row();

    if (empty($opportunity)) {
        show_404();
        return;
    }

    $current_user_id = $this->get_logged_in_spares_user_id();
    $can_reopen = $this->current_user_can_manage_all_spares_quotations()
        || (int) $opportunity->marketing_person_id === $current_user_id;

    if (!$can_reopen) {
        $this->session->set_flashdata('error', 'You are not allowed to reopen this quotation.');
        redirect(page_url . 'Spares/opportunity_detail/' . $opportunity_id);
        return;
    }

    $result = $this->spare_quotation_expiry_model->reopen($opportunity_id, $current_user_id);
    $this->session->set_flashdata($result['success'] ? 'success' : 'error', $result['message']);

    $return_url = trim((string) $this->input->post('return_url'));
    if ($return_url === '' || strpos($return_url, page_url . 'Spares/') !== 0) {
        $return_url = page_url . 'Spares/opportunity_detail/' . $opportunity_id;
    }

    redirect($return_url);
}

public function create_quotation($opportunity_id)
{
    if ($this->spare_quotation_expiry_model->get_active_cancellation($opportunity_id)) {
        $this->session->set_flashdata('error', 'Reopen this cancelled quotation before creating or revising it.');
        redirect(page_url . 'Spares/opportunity_detail/' . (int) $opportunity_id);
        return;
    }

    $this->ensure_spare_quotation_charge_mode_columns();
    $this->load->model('opportunity_model');
    $data['opportunity'] = $this->opportunity_model->get_opportunity_details($opportunity_id);

    if (!$data['opportunity']) {
        show_404();
    }

    $data['opportunity_products'] = $this->opportunity_model->get_opportunity_products($opportunity_id);
    $data['products'] = $data['opportunity_products'];
    $data['recent_price_history_map'] = $this->build_recent_price_history_map($data['products']);
    $data['quotation_type_options'] = $this->get_spare_quotation_type_options();
    $data['dispatch_mode_options'] = $this->get_spare_dispatch_mode_options();
    $data['custom_engg_type_options'] = $this->get_spare_custom_engg_type_options();
    $data['selected_quotation_type'] = 'CONSUMABLE';
    $data['selected_dispatch_mode'] = 'COURIER';
    $data['selected_custom_engg_type'] = '';

    // Generate a new, unique quotation number (you can customize this logic)
    $this->db->select('quotation_id');
    $this->db->from('quotations');
    $this->db->order_by('quotation_id', 'DESC');
    $this->db->limit(1);
    $last_quote = $this->db->get()->row();
    $last_id = $last_quote ? $last_quote->quotation_id : 0;
    $data['new_quotation_no'] = 'SQRF' . (27134 + $last_id + 1) . '/' . date('Y');

    // Fetch all products for the "Add More Products" dropdown
    $data['all_products'] = $this->db->get('spare_parts_for_trading')->result(); // Change 'products' to your actual product master table name

    $this->load->view('spares/create_quotation_view', $data);
}

private function build_recent_price_history_map($products)
{
    $product_ids = [];

    foreach ((array) $products as $product) {
        $product_id = 0;

        if (is_object($product)) {
            if (isset($product->product_id)) {
                $product_id = (int) $product->product_id;
            } elseif (isset($product->id)) {
                $product_id = (int) $product->id;
            }
        } elseif (is_array($product)) {
            if (isset($product['product_id'])) {
                $product_id = (int) $product['product_id'];
            } elseif (isset($product['id'])) {
                $product_id = (int) $product['id'];
            }
        }

        if ($product_id > 0) {
            $product_ids[] = $product_id;
        }
    }

    return $this->opportunity_model->get_recent_customer_item_quotes($product_ids, 0, 2);
}

private function ensure_spare_pi_dependencies()
{
    $this->spare_pi_model->ensure_tables();

    $stage = $this->db->query(
        "SELECT lead_id, lead_name
         FROM spare_lead_stage
         WHERE lead_id = 13 OR LOWER(TRIM(lead_name)) = ?
         ORDER BY lead_id ASC
         LIMIT 1",
        ['pending for po']
    )->row();

    return (int) ($stage->lead_id ?? 13);
}

private function ensure_spare_quotation_charge_mode_columns()
{
    if (!$this->db->table_exists('quotations')) {
        return;
    }

    $columns = [
        'packing_charge_mode' => "ALTER TABLE `quotations` ADD `packing_charge_mode` VARCHAR(20) NOT NULL DEFAULT 'included' AFTER `packing_charge`",
        'freight_charge_mode' => "ALTER TABLE `quotations` ADD `freight_charge_mode` VARCHAR(20) NOT NULL DEFAULT 'included' AFTER `freight_charge`",
        'ex_work_charge_mode' => "ALTER TABLE `quotations` ADD `ex_work_charge_mode` VARCHAR(20) NOT NULL DEFAULT 'included' AFTER `ex_work_charge`",
        'insurance_charge_mode' => "ALTER TABLE `quotations` ADD `insurance_charge_mode` VARCHAR(20) NOT NULL DEFAULT 'included' AFTER `insurance_charge`",
    ];

    foreach ($columns as $field => $sql) {
        if (!$this->db->field_exists($field, 'quotations')) {
            $this->db->query($sql);
        }
    }

    if ($this->db->table_exists('quotation_products') && !$this->db->field_exists('gst_percent', 'quotation_products')) {
        $this->db->query(
            "ALTER TABLE `quotation_products`
             ADD `gst_percent` DECIMAL(5,2) DEFAULT NULL
             AFTER `discount_percent`"
        );
    }

    if (!$this->db->field_exists('currency', 'quotations')) {
        $this->db->query(
            "ALTER TABLE `quotations`
             ADD `currency` VARCHAR(5) NOT NULL DEFAULT 'INR'
             AFTER `attention`"
        );
        $this->db->query(
            "UPDATE `quotations` q
             JOIN `spares_customers` c ON c.`customer_id` = q.`customer_id`
             SET q.`currency` = 'USD'
             WHERE c.`country_id` <> 101"
        );
    }

    $sf_columns = [
        'quotation_type' => "ALTER TABLE `quotations` ADD `quotation_type` VARCHAR(30) NOT NULL DEFAULT 'CONSUMABLE' AFTER `currency`",
        'custom_engg_type' => "ALTER TABLE `quotations` ADD `custom_engg_type` VARCHAR(30) DEFAULT NULL AFTER `quotation_type`",
        'dispatch_mode' => "ALTER TABLE `quotations` ADD `dispatch_mode` VARCHAR(30) NOT NULL DEFAULT 'COURIER' AFTER `custom_engg_type`",
        'execution_workflow_type' => "ALTER TABLE `quotations` ADD `execution_workflow_type` VARCHAR(40) NOT NULL DEFAULT 'CONSUMABLE' AFTER `dispatch_mode`",
    ];

    foreach ($sf_columns as $field => $sql) {
        if (!$this->db->field_exists($field, 'quotations')) {
            $this->db->query($sql);
        }
    }
}

private function get_spare_quotation_type_options()
{
    return [
        'CONSUMABLE' => 'Consumable',
        'CRITICAL' => 'Critical',
        'CONS_CRITICAL' => 'Consumable + Critical',
        'CUSTOM_ENGG' => 'Custom Engg.',
    ];
}

private function get_spare_dispatch_mode_options()
{
    return [
        'SELF_PICKUP' => 'Self Pickup',
        'COURIER' => 'Courier',
    ];
}

private function get_spare_custom_engg_type_options()
{
    return [
        'CHANGEOVER' => 'Changeover',
        'SPEED_UPGRADATION' => 'Speed Upgradation',
    ];
}

private function normalize_spare_quotation_type($quotation_type)
{
    $quotation_type = strtoupper(trim((string) $quotation_type));
    $options = $this->get_spare_quotation_type_options();

    return isset($options[$quotation_type]) ? $quotation_type : '';
}

private function normalize_spare_dispatch_mode($dispatch_mode)
{
    $dispatch_mode = strtoupper(trim((string) $dispatch_mode));
    $options = $this->get_spare_dispatch_mode_options();

    return isset($options[$dispatch_mode]) ? $dispatch_mode : '';
}

private function normalize_spare_custom_engg_type($custom_engg_type)
{
    $custom_engg_type = strtoupper(trim((string) $custom_engg_type));
    $options = $this->get_spare_custom_engg_type_options();

    return isset($options[$custom_engg_type]) ? $custom_engg_type : '';
}

private function map_spare_quotation_to_execution_workflow($quotation_type, $custom_engg_type = '')
{
    $quotation_type = $this->normalize_spare_quotation_type($quotation_type);

    if ($quotation_type === 'CUSTOM_ENGG') {
        $custom_engg_type = $this->normalize_spare_custom_engg_type($custom_engg_type);
        return $custom_engg_type === 'SPEED_UPGRADATION' ? 'CUSTOM_SPEED_UPGRADATION' : 'CUSTOM_CHANGEOVER';
    }

    return $quotation_type !== '' ? $quotation_type : 'CONSUMABLE';
}

private function get_spare_quotation_type_label($quotation_type, $custom_engg_type = '')
{
    $quotation_type = $this->normalize_spare_quotation_type($quotation_type);
    $quotation_options = $this->get_spare_quotation_type_options();

    if ($quotation_type !== 'CUSTOM_ENGG') {
        return isset($quotation_options[$quotation_type]) ? $quotation_options[$quotation_type] : 'Consumable';
    }

    $custom_engg_type = $this->normalize_spare_custom_engg_type($custom_engg_type);
    $custom_options = $this->get_spare_custom_engg_type_options();
    $custom_label = isset($custom_options[$custom_engg_type]) ? $custom_options[$custom_engg_type] : '';

    return $custom_label !== '' ? 'Custom Engg. - ' . $custom_label : 'Custom Engg.';
}

private function get_spare_dispatch_mode_label($dispatch_mode)
{
    $dispatch_mode = $this->normalize_spare_dispatch_mode($dispatch_mode);
    $options = $this->get_spare_dispatch_mode_options();

    return isset($options[$dispatch_mode]) ? $options[$dispatch_mode] : 'Courier';
}

private function normalize_spare_currency($currency)
{
    $currency = strtoupper(trim((string) $currency));
    return in_array($currency, ['INR', 'USD', 'EUR'], true) ? $currency : 'INR';
}

private function get_spare_currency_symbol($currency)
{
    $symbols = ['INR' => '₹', 'USD' => '$', 'EUR' => '€'];
    $currency = $this->normalize_spare_currency($currency);
    return $symbols[$currency];
}

private function normalize_spare_quote_charge_mode($mode)
{
    return strtolower(trim((string) $mode)) === 'extra' ? 'extra' : 'included';
}

private function is_spare_quote_charge_extra($mode)
{
    return $this->normalize_spare_quote_charge_mode($mode) === 'extra';
}

private function get_spare_quote_default_item_gst_percent($currency)
{
    return $this->normalize_spare_currency($currency) === 'INR' ? 18.00 : 0.00;
}

private function normalize_spare_quote_item_gst_percent($gst_percent, $default_gst_percent = 18.00)
{
    if ($gst_percent === '' || $gst_percent === null || !is_numeric($gst_percent)) {
        $gst_percent = $default_gst_percent;
    }

    $gst_percent = (float) $gst_percent;

    if ($gst_percent < 0) {
        $gst_percent = 0;
    }

    return round($gst_percent, 2);
}

private function allocate_spare_quote_amount($amount, array $weights)
{
    $keys = array_keys($weights);
    $allocations = [];

    foreach ($keys as $key) {
        $allocations[$key] = 0.00;
    }

    $amount = round((float) $amount, 2);
    if (empty($keys) || abs($amount) < 0.01) {
        return $allocations;
    }

    $absolute_amount = abs($amount);
    $normalized_weights = [];
    $total_weight = 0.00;

    foreach ($weights as $key => $weight) {
        $normalized_weight = max(0, (float) $weight);
        $normalized_weights[$key] = $normalized_weight;
        $total_weight += $normalized_weight;
    }

    if ($total_weight <= 0) {
        foreach ($keys as $key) {
            $normalized_weights[$key] = 1;
        }
        $total_weight = (float) count($keys);
    }

    $distributed = 0.00;
    $last_index = count($keys) - 1;

    foreach ($keys as $index => $key) {
        if ($index === $last_index) {
            $share = round($absolute_amount - $distributed, 2);
        } else {
            $share = round(($absolute_amount * $normalized_weights[$key]) / $total_weight, 2);
            $distributed += $share;
        }

        $allocations[$key] = $amount < 0 ? -$share : $share;
    }

    return $allocations;
}

private function calculate_spare_quote_totals($basicValue, array $quote_master_data, array $products_data = [], $defaultItemGstPercent = 18.00, $defaultChargeGstPercent = 18.00)
{
    $packingCharge = round(($basicValue * (float) ($quote_master_data['packing_percent'] ?? 0)) / 100, 2);
    $insuranceCharge = round(($basicValue * (float) ($quote_master_data['insurance_percent'] ?? 0)) / 100, 2);
    $freightCharge = round((float) ($quote_master_data['freight_charge'] ?? 0), 2);
    $exWorkCharge = round((float) ($quote_master_data['ex_work_charge'] ?? 0), 2);

    $packingChargeForTotal = $this->is_spare_quote_charge_extra($quote_master_data['packing_charge_mode'] ?? 'included') ? 0 : $packingCharge;
    $freightChargeForTotal = $this->is_spare_quote_charge_extra($quote_master_data['freight_charge_mode'] ?? 'included') ? 0 : $freightCharge;
    $exWorkChargeForTotal = $this->is_spare_quote_charge_extra($quote_master_data['ex_work_charge_mode'] ?? 'included') ? 0 : $exWorkCharge;
    $insuranceChargeForTotal = $this->is_spare_quote_charge_extra($quote_master_data['insurance_charge_mode'] ?? 'included') ? 0 : $insuranceCharge;

    $subTotal = round($basicValue + $packingChargeForTotal + $freightChargeForTotal + $exWorkChargeForTotal + $insuranceChargeForTotal, 2);
    $overallDiscountAmount = round(($subTotal * (float) ($quote_master_data['overall_discount_percent'] ?? 0)) / 100, 2);
    $finalTotalValue = round($subTotal - $overallDiscountAmount, 2);
    $gstAmount = 0.00;
    $effectiveGstPercent = 0.00;
    $gstSummaryLabel = 'GST';
    $enrichedProducts = $products_data;
    $defaultItemGstPercent = $this->normalize_spare_quote_item_gst_percent($defaultItemGstPercent, $quote_master_data['gst_percent'] ?? 18.00);
    $defaultChargeGstPercent = $this->normalize_spare_quote_item_gst_percent($defaultChargeGstPercent, $defaultItemGstPercent);
    $componentWeights = [];
    $uniqueGstRates = [];

    foreach ($products_data as $index => $product) {
        $lineTotal = round((float) ($product['total_price'] ?? 0), 2);
        if ($lineTotal > 0) {
            $componentWeights['row_' . $index] = $lineTotal;
        }
    }

    $chargeComponents = [
        'charge_packing' => $packingChargeForTotal,
        'charge_freight' => $freightChargeForTotal,
        'charge_ex_work' => $exWorkChargeForTotal,
        'charge_insurance' => $insuranceChargeForTotal,
    ];

    foreach ($chargeComponents as $componentKey => $componentAmount) {
        $componentAmount = round((float) $componentAmount, 2);
        if ($componentAmount > 0) {
            $componentWeights[$componentKey] = $componentAmount;
        }
    }

    $discountAllocations = $this->allocate_spare_quote_amount($overallDiscountAmount, $componentWeights);

    foreach ($products_data as $index => $product) {
        $lineTotal = round((float) ($product['total_price'] ?? 0), 2);
        $lineGstPercent = $this->normalize_spare_quote_item_gst_percent($product['gst_percent'] ?? null, $defaultItemGstPercent);
        $allocatedDiscountAmount = round((float) ($discountAllocations['row_' . $index] ?? 0), 2);
        $taxableValue = round(max(0, $lineTotal - $allocatedDiscountAmount), 2);
        $lineGstAmount = round(($taxableValue * $lineGstPercent) / 100, 2);

        $enrichedProducts[$index]['gst_percent'] = $lineGstPercent;
        $enrichedProducts[$index]['allocated_charge_amount'] = 0.00;
        $enrichedProducts[$index]['allocated_discount_amount'] = $allocatedDiscountAmount;
        $enrichedProducts[$index]['taxable_value'] = $taxableValue;
        $enrichedProducts[$index]['gst_amount'] = $lineGstAmount;
        $enrichedProducts[$index]['total_with_gst'] = round($taxableValue + $lineGstAmount, 2);

        if ($taxableValue > 0 && $lineGstPercent > 0) {
            $uniqueGstRates[number_format($lineGstPercent, 2, '.', '')] = $lineGstPercent;
        }

        $gstAmount += $lineGstAmount;
    }

    foreach ($chargeComponents as $componentKey => $componentAmount) {
        $componentAmount = round((float) $componentAmount, 2);
        if ($componentAmount <= 0) {
            continue;
        }

        $allocatedDiscountAmount = round((float) ($discountAllocations[$componentKey] ?? 0), 2);
        $taxableValue = round(max(0, $componentAmount - $allocatedDiscountAmount), 2);
        $chargeGstAmount = round(($taxableValue * $defaultChargeGstPercent) / 100, 2);

        if ($taxableValue > 0 && $defaultChargeGstPercent > 0) {
            $uniqueGstRates[number_format($defaultChargeGstPercent, 2, '.', '')] = $defaultChargeGstPercent;
        }

        $gstAmount += $chargeGstAmount;
    }

    $gstAmount = round($gstAmount, 2);
    $effectiveGstPercent = $finalTotalValue > 0
        ? round(($gstAmount / $finalTotalValue) * 100, 2)
        : 0.00;

    if (count($uniqueGstRates) === 1) {
        $singleRate = (float) reset($uniqueGstRates);
        if ($singleRate > 0) {
            $gstSummaryLabel = 'GST (' . number_format($singleRate, 2) . '%)';
        }
    } elseif (count($uniqueGstRates) > 1) {
        $gstSummaryLabel = 'GST (Item-wise)';
    }

    if ($gstAmount <= 0 && $finalTotalValue > 0) {
        $effectiveGstPercent = $this->normalize_spare_quote_item_gst_percent($quote_master_data['gst_percent'] ?? 0, 0);
        $gstAmount = round(($finalTotalValue * $effectiveGstPercent) / 100, 2);

        if ($effectiveGstPercent > 0) {
            $gstSummaryLabel = 'GST (' . number_format($effectiveGstPercent, 2) . '%)';
        }
    }

    $grandTotal = round($finalTotalValue + $gstAmount, 2);

    return [
        'packing_charge' => $packingCharge,
        'insurance_charge' => $insuranceCharge,
        'sub_total' => $subTotal,
        'overall_discount_amount' => $overallDiscountAmount,
        'total_value' => $finalTotalValue,
        'gst_amount' => $gstAmount,
        'grand_total' => $grandTotal,
        'effective_gst_percent' => $effectiveGstPercent,
        'gst_summary_label' => $gstSummaryLabel,
        'products' => $enrichedProducts,
    ];
}

private function get_spares_company_profile()
{
    return [
        'name' => 'Shubham Flexible Packaging Machines Pvt. Ltd.',
        'address' => 'B-8A, Sector 59 Part II, Ballabgarh, Faridabad - 121004, Haryana, India',
        'contact' => 'Board Nos.: 0091 129 4272350/4272352 | Fax: 0091 129 4272351',
        'phone' => '+91-8377900471',
        'email' => 'spares@shubhampack.com',
        'website' => 'www.shubhampack.com',
        'logo' => 'https://pms.shubhampack.in/assets/images/shubhampack.png',
        'footer_image' => 'https://pms.shubhampack.in/image_bank/footerlogoimage.png',
        'colorcode' => '#003366',
        'gst_no' => '06AAFCS9908R1ZH',
        'cin_no' => 'U29299DL2002PTC114679',
        'iec_code' => '0503025216',
        'bank_name' => 'Axis Bank Ltd.',
        'account_no' => '920030068344715',
        'bank_address' => 'SCO-40 Sec-7 Market Ballabhgarh Faridabad',
        'ifsc_code' => 'UTIB0000039',
        'account_type' => 'Current Account',
        'account_holder' => 'Shubham Flexible Packaging Machines Pvt. Ltd.',
        'swift_code' => 'AXISINBB039',
        'corporate_office' => 'B-8A, Sector 59 Part II, Ballabgarh, Faridabad - 121004, Haryana (India)',
        'registered_office' => '2, Central Road, Bhogal, New Delhi - 110014',
    ];
}

private function get_spares_customer_snapshot($opportunity)
{
    $customer = $this->db->select('company_name, address, contact_person, contact_person_no, email, tax_number, country_id')
        ->get_where('spares_customers', ['customer_id' => (int) ($opportunity->customer_id ?? 0)])
        ->row();

    if (!$customer) {
        $customer = (object) [
            'company_name' => $opportunity->company_name ?? '',
            'address' => $opportunity->customer_address ?? '',
            'contact_person' => $opportunity->contact_person ?? '',
            'contact_person_no' => $opportunity->contact_person_no ?? '',
            'email' => $opportunity->email ?? '',
            'tax_number' => $opportunity->customer_gst ?? '',
            'country_id' => $opportunity->op_type == 1 ? 101 : 0,
        ];
    }

    return $customer;
}

private function get_latest_spare_quote_with_items($opportunity_id)
{
    $quote = $this->db->from('quotations')
        ->where('opportunity_id', (int) $opportunity_id)
        ->order_by('quotation_id', 'DESC')
        ->limit(1)
        ->get()
        ->row();

    $items = [];

    if ($quote) {
        $items = $this->db->select('
                qp.*,
                p.code as product_code,
                p.description as product_master_description
            ')
            ->from('quotation_products qp')
            ->join('spare_parts_for_trading p', 'p.id = qp.product_id', 'left')
            ->where('qp.quotation_id', (int) $quote->quotation_id)
            ->order_by('qp.quot_product_id', 'ASC')
            ->get()
            ->result();
    }

    return [
        'quote' => $quote,
        'items' => $items,
    ];
}

private function get_spare_pi_financial_year()
{
    $year = (int) date('y');

    if ((int) date('n') >= 4) {
        return sprintf('%02d-%02d', $year, ($year + 1) % 100);
    }

    return sprintf('%02d-%02d', ($year + 99) % 100, $year);
}

private function build_spare_pi_number()
{
    $sequence = $this->spare_pi_model->get_next_pi_sequence();
    return sprintf('SPI/%s/%03d', $this->get_spare_pi_financial_year(), $sequence);
}

private function get_spare_pi_default_currency($customer)
{
    return ((int) ($customer->country_id ?? 101) === 101) ? 'INR' : 'USD';
}

private function get_spare_pi_unit_options()
{
    return ['NOS', 'PCS', 'SET', 'KG', 'MTR', 'LTR', 'ROLL'];
}

private function normalize_spare_pi_amount_in_words($amount_in_words, $currency)
{
    $amount_in_words = trim((string) $amount_in_words);

    if (strtoupper(trim((string) $currency)) === 'INR' && $amount_in_words !== '') {
        $amount_in_words = preg_replace('/\bPaise\b/', 'Paisa', $amount_in_words);
    }

    return $amount_in_words;
}

private function get_spare_pi_amount_in_words($amount, $currency)
{
    $amount_in_words = function_exists('get_amount_in_words')
        ? get_amount_in_words((float) $amount, $currency)
        : number_format((float) $amount, 2);

    return $this->normalize_spare_pi_amount_in_words($amount_in_words, $currency);
}

private function build_spare_pi_items_from_quote($quote_items)
{
    $items = [];
    $allowed_units = $this->get_spare_pi_unit_options();

    foreach ((array) $quote_items as $item) {
        $unit = strtoupper(trim((string) ($item->unit ?? ($item->quantity_packed_unit ?? 'NOS'))));
        if ($unit === '' || !in_array($unit, $allowed_units, true)) {
            $unit = 'NOS';
        }

        $items[] = (object) [
            'product_id' => (int) ($item->product_id ?? 0),
            'product_code' => trim((string) ($item->product_code ?? '')),
            'hsn_code' => trim((string) ($item->hsn_code ?? '')),
            'description' => trim((string) (($item->description ?? '') !== '' ? $item->description : ($item->product_master_description ?? ''))),
            'quantity' => (float) ($item->quantity ?? 0),
            'unit' => $unit,
            'unit_price' => (float) ($item->unit_price ?? 0),
            'discount_percent' => (float) ($item->discount_percent ?? 0),
            'discount_amount' => (float) ($item->discount_amount ?? 0),
            'total_price' => (float) ($item->total_price ?? 0),
        ];
    }

    return $items;
}

private function parse_spare_pi_items_from_post($post)
{
    $items = [];
    $basic_value = 0;
    $descriptions = $post['description'] ?? [];
    $allowed_units = $this->get_spare_pi_unit_options();

    foreach ($descriptions as $index => $description) {
        $description = trim((string) $description);
        $product_id = (int) ($post['product_id'][$index] ?? 0);
        $product_code = trim((string) ($post['product_code'][$index] ?? ''));
        $unit = strtoupper(trim((string) ($post['unit'][$index] ?? 'NOS')));
        $quantity = (float) ($post['quantity'][$index] ?? 0);
        $unit_price = (float) ($post['unit_price'][$index] ?? 0);
        $discount_percent = (float) ($post['discount_percent'][$index] ?? 0);

        if ($description === '' && $product_code === '' && $product_id <= 0) {
            continue;
        }

        if ($quantity <= 0) {
            continue;
        }

        if ($unit === '' || !in_array($unit, $allowed_units, true)) {
            $unit = 'NOS';
        }

        $line_before_discount = $quantity * $unit_price;
        $discount_amount = round(($line_before_discount * $discount_percent) / 100, 2);
        $total_price = round($line_before_discount - $discount_amount, 2);

        $items[] = [
            'sort_order' => count($items) + 1,
            'product_id' => $product_id > 0 ? $product_id : null,
            'product_code' => $product_code,
            'hsn_code' => trim((string) ($post['hsn_code'][$index] ?? '')),
            'description' => $description,
            'quantity' => $quantity,
            'unit' => $unit,
            'unit_price' => $unit_price,
            'discount_percent' => $discount_percent,
            'discount_amount' => $discount_amount,
            'total_price' => $total_price,
        ];

        $basic_value += $total_price;
    }

    $packing_percent = (float) ($post['packing_percent'] ?? 0);
    $freight_charge = (float) ($post['freight_charge'] ?? 0);
    $ex_work_charge = (float) ($post['ex_work_charge'] ?? 0);
    $insurance_percent = (float) ($post['insurance_percent'] ?? 0);
    $overall_discount_percent = (float) ($post['overall_discount_percent'] ?? 0);
    $gst_percent = (float) ($post['gst_percent'] ?? 0);

    $packing_charge = round(($basic_value * $packing_percent) / 100, 2);
    $insurance_charge = round(($basic_value * $insurance_percent) / 100, 2);
    $sub_total = round($basic_value + $packing_charge + $freight_charge + $ex_work_charge + $insurance_charge, 2);
    $overall_discount_amount = round(($sub_total * $overall_discount_percent) / 100, 2);
    $total_value = round($sub_total - $overall_discount_amount, 2);
    $gst_amount = round(($total_value * $gst_percent) / 100, 2);
    $grand_total = round($total_value + $gst_amount, 2);

    return [
        'items' => $items,
        'basic_value' => round($basic_value, 2),
        'packing_percent' => $packing_percent,
        'packing_charge' => $packing_charge,
        'freight_charge' => round($freight_charge, 2),
        'ex_work_charge' => round($ex_work_charge, 2),
        'insurance_percent' => $insurance_percent,
        'insurance_charge' => $insurance_charge,
        'overall_discount_percent' => $overall_discount_percent,
        'overall_discount_amount' => $overall_discount_amount,
        'total_value' => $total_value,
        'gst_percent' => $gst_percent,
        'gst_amount' => $gst_amount,
        'grand_total' => $grand_total,
    ];
}

private function get_spare_pi_default_notes()
{
    return "1. This PI is based on the latest approved commercial discussion.\n2. Kindly share your PO against this PI to start order processing.\n3. All disputes are subject to Faridabad Jurisdiction.";
}

private function get_spare_pi_default_declaration()
{
    return 'We declare that this proforma invoice reflects the discussed commercial terms for the listed items and all particulars stated above are true to the best of our knowledge.';
}

private function insert_spare_progress_history($opportunity_id, $stage_id, $remarks, $title = 'Pending for PO')
{
    $user_id = $this->session->userdata['logged_in']['user_id'] ?? null;

    $this->db->insert('spare_progress_remarks', [
        'lead_id' => (int) $opportunity_id,
        'lead_stage' => (int) $stage_id,
        'next_follow_date' => date('Y-m-d', strtotime('+3 day')),
        'remarks' => $remarks,
        'remark_title' => $title,
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $user_id,
    ]);
}

public function product_quote_history_ajax()
{
    $this->output->set_content_type('application/json');

    $product_id = (int) $this->input->get_post('product_id');

    if ($product_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product is required.',
            'history' => []
        ]);
        return;
    }

    $this->load->model('opportunity_model');
    $history = $this->opportunity_model->get_recent_customer_item_quote_history($product_id, 0, 2);

    echo json_encode([
        'status' => 'success',
        'history' => $history
    ]);
}

public function search_products_ajax()
    {
        // Ensure this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $searchTerm = $this->input->get('q');
        $excludeIds = $this->input->get('exclude'); // Get array of IDs to exclude
        $results = [];

        if (!is_null($searchTerm)) {
            $this->db->select('id, code, description, price');
            $this->db->from('spare_parts_for_trading'); // ** IMPORTANT: Change 'products' to your actual product table name **
            
            $this->db->group_start();
            $this->db->like('code', $searchTerm);
            $this->db->or_like('description', $searchTerm);
            $this->db->group_end();

            // ** NEW: Exclude already selected products from the search **
            if (!empty($excludeIds) && is_array($excludeIds)) {
                $this->db->where_not_in('id', $excludeIds);
            }
            
            $this->db->limit(20);
            $query = $this->db->get();
            
            foreach ($query->result() as $row) {
                $results[] = [
                    'id' => $row->id,
                    'text' => $row->code . ' - ' . $row->description,
                    'price' => $row->price ?? 0,
                    'hsn_code' => '' // Add HSN code here if it's in your products table
                ];
            }
        }

        echo json_encode(['results' => $results]);
    }
	
   public function generate_quotation_pdf()
    {
        // This line is only needed if you are not using Composer autoload
        require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';
        $this->load->helper('number');
        $this->ensure_spare_quotation_charge_mode_columns();

        // --- 1. GATHER DATA FROM FORM ---
        $post = $this->input->post();
        $opportunity_id = (int) ($post['opportunity_id'] ?? 0);
        if ($this->spare_quotation_expiry_model->get_active_cancellation($opportunity_id)) {
            show_error('Reopen this cancelled quotation before creating or revising it.', 409);
            return;
        }
        $user_id = $this->session->userdata('logged_in')['user_id'];
        $currency = $this->normalize_spare_currency($post['currency'] ?? 'INR');
        $quotation_type = $this->normalize_spare_quotation_type($post['quotation_type'] ?? '');
        $dispatch_mode = $this->normalize_spare_dispatch_mode($post['dispatch_mode'] ?? '');
        $custom_engg_type = $this->normalize_spare_custom_engg_type($post['custom_engg_type'] ?? '');

        if ($quotation_type === '') {
            show_error('Please select a valid quotation type.');
            return;
        }

        if ($dispatch_mode === '') {
            show_error('Please select a valid dispatch mode.');
            return;
        }

        if ($quotation_type === 'CUSTOM_ENGG' && $custom_engg_type === '') {
            show_error('Please select Changeover or Speed Upgradation for Custom Engg. quotations.');
            return;
        }

        if ($quotation_type !== 'CUSTOM_ENGG') {
            $custom_engg_type = null;
        }

        if (empty($post['product_id']) || !is_array($post['product_id'])) {
            show_error('Cannot generate a quotation with no products.');
            return;
        }

        // --- 2. PREPARE & RECALCULATE DATA ON THE SERVER ---
        $quote_master_data = [
            'opportunity_id'           => $post['opportunity_id'],
            'quotation_no'             => $post['quotation_no'],
            'quotation_date'           => $post['quotation_date'],
            'customer_id'              => $post['customer_id'],
            'attention'                => $post['attention'],
            'currency'                 => $currency,
            'quotation_type'           => $quotation_type,
            'custom_engg_type'         => $custom_engg_type,
            'dispatch_mode'            => $dispatch_mode,
            'execution_workflow_type'  => $this->map_spare_quotation_to_execution_workflow($quotation_type, $custom_engg_type),
            'packing_percent'          => (float)($post['packing_percent'] ?? 0),
            'packing_charge_mode'      => $this->normalize_spare_quote_charge_mode($post['packing_charge_mode'] ?? 'included'),
            'freight_charge'           => (float)($post['freight_charge'] ?? 0),
            'freight_charge_mode'      => $this->normalize_spare_quote_charge_mode($post['freight_charge_mode'] ?? 'included'),
            'ex_work_charge'           => (float)($post['ex_work_charge'] ?? 0), // NEW FIELD ADDED HERE
            'ex_work_charge_mode'      => $this->normalize_spare_quote_charge_mode($post['ex_work_charge_mode'] ?? 'included'),
            'insurance_percent'        => (float)($post['insurance_percent'] ?? 0),
            'insurance_charge_mode'    => $this->normalize_spare_quote_charge_mode($post['insurance_charge_mode'] ?? 'included'),
            'overall_discount_percent' => (float)($post['overall_discount_percent'] ?? 0),
            'gst_percent'              => 0,
            'payment_terms'            => $post['payment_terms'],
            'validity'                 => $post['validity'],
            'delivery_terms'           => $post['delivery_terms'],
            'created_by'               => $user_id,
        ];

        // Handle revision data if it exists
        if ($this->input->post('revised_from_quotation_id')) {
            $quote_master_data['revised_from_quotation_id'] = $this->input->post('revised_from_quotation_id');
            $quote_master_data['revision_no'] = $this->input->post('revision_no');
        }

        $products_data = [];
        $basicValue = 0;
        $has_line_item_discount = false;
        $default_item_gst_percent = $this->get_spare_quote_default_item_gst_percent($currency);

        foreach ($post['product_id'] as $key => $pid) {
            $pid = trim((string) $pid);
            $description = trim((string) ($post['description'][$key] ?? ''));
            $qty = (float)($post['quantity'][$key] ?? 0);
            $price = (float)($post['unit_price'][$key] ?? 0);
            $disc_percent = (float)($post['discount_percent'][$key] ?? 0);

            if ($pid === '' && $description === '') {
                continue;
            }

            if ($qty <= 0) {
                continue;
            }

            if ($disc_percent > 0) { $has_line_item_discount = true; }

            $line_total_before_disc = $qty * $price;
            $disc_amount = ($line_total_before_disc * $disc_percent) / 100;
            $final_line_total = $line_total_before_disc - $disc_amount;

            $products_data[] = [
                'product_id'        => (int) $pid,
                'description'       => $description,
                'hsn_code'          => trim((string) ($post['hsn_code'][$key] ?? '')),
                'quantity'          => $qty,
                'unit_price'        => $price,
                'discount_percent'  => $disc_percent,
                'gst_percent'       => $this->normalize_spare_quote_item_gst_percent($post['item_gst_percent'][$key] ?? null, $default_item_gst_percent),
                'discount_amount'   => $disc_amount,
                'total_price'       => $final_line_total,
            ];
            $basicValue += $final_line_total;
        }

        if (empty($products_data)) {
            show_error('Cannot generate a quotation with no valid products.');
            return;
        }
        
        $totals = $this->calculate_spare_quote_totals(
            $basicValue,
            $quote_master_data,
            $products_data,
            $default_item_gst_percent,
            $default_item_gst_percent
        );

        $quote_master_data['basic_value'] = $basicValue;
        $quote_master_data['packing_charge'] = $totals['packing_charge'];
        $quote_master_data['insurance_charge'] = $totals['insurance_charge'];
        $quote_master_data['overall_discount_amount'] = $totals['overall_discount_amount'];
        $quote_master_data['total_value'] = $totals['total_value'];
        $quote_master_data['gst_percent'] = $totals['effective_gst_percent'];

        // --- 3. SAVE TO DATABASE ---
        $this->db->trans_start();
        
        $this->db->insert('quotations', $quote_master_data);
        $quotation_id = $this->db->insert_id();

        foreach ($products_data as &$p) { $p['quotation_id'] = $quotation_id; }
        if(!empty($products_data)) { $this->db->insert_batch('quotation_products', $products_data); }
        
        // Conditional Progress Remarks
        $nextDate = date("Y-m-d", strtotime("+1 day"));
        $progress_remarksdata = [
            'lead_id' => $post['opportunity_id'],
            'next_follow_date' => $nextDate,
            'added_on' => date('Y-m-d H:i:s'),
            'added_by' => $user_id
        ];

        if ($this->input->post('revised_from_quotation_id')) {
            // This is a REVISED quotation
            $progress_remarksdata['lead_stage'] = 4; // ** CORRECTED STAGE ID **
            $progress_remarksdata['remarks'] = 'Revised Quotation ' . $post['quotation_no'] . ' generated. Following up.';
            $progress_remarksdata['remark_title'] = 'Revised Quotation Generated';
        } else {
            // This is a NEW quotation
            $progress_remarksdata['lead_stage'] = 4;
            $progress_remarksdata['remarks'] = 'Quotation ' . $post['quotation_no'] . ' generated. Pending for Approval.';
            $progress_remarksdata['remark_title'] = 'Quotation Generated';
        }
        $this->db->insert('spare_progress_remarks', $progress_remarksdata);
        
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            show_error('A database error occurred while saving the quotation.'); return;
        }

        // --- 4. PREPARE DATA FOR PDF TEMPLATE ---
        $pdf_data = $quote_master_data;
        $pdf_data['products'] = $totals['products'];
        $pdf_data['has_line_item_discount'] = $has_line_item_discount;
        $pdf_data['customer'] = $this->db->get_where('spares_customers', ['customer_id' => $pdf_data['customer_id']])->row();
        
        // ** ADDED: Pass final calculation results to the view **
        $pdf_data['gst_amount'] = $totals['gst_amount'];
        $pdf_data['grand_total'] = $totals['grand_total'];
        $pdf_data['sub_total'] = $totals['sub_total'];
        $pdf_data['gst_percent'] = $totals['effective_gst_percent'];
        $pdf_data['gst_summary_label'] = $totals['gst_summary_label'];
        $pdf_data['currency'] = $currency;
        $pdf_data['currency_type'] = $pdf_data['currency'];
        $pdf_data['curr_symbol'] = $this->get_spare_currency_symbol($currency);
        $pdf_data['quotation_type_label'] = $this->get_spare_quotation_type_label($quotation_type, $custom_engg_type);
        $pdf_data['dispatch_mode_label'] = $this->get_spare_dispatch_mode_label($dispatch_mode);
        $pdf_data['shipping_customer'] = $pdf_data['customer'];
        $pdf_data['amount_in_words'] = get_amount_in_words($totals['grand_total'], $pdf_data['currency']);
        
        $pdf_data['company_info'] = [
            'name' => 'Shubham Flexible Packaging Machines Pvt. Ltd.', 'address' => 'B-8A, Sector 59 Part II, Ballabgarh, Faridabad - 121004, Haryana, India',
            'contact' => 'Board Nos.: 0091 129 4272350/4272352 | Fax: 0091 129 4272351', 'email' => 'marketing@shubhampack.com', 'website' => 'www.shubhampack.com',
            'logo' => 'https://pms.shubhampack.in/assets/images/shubhampack.png',
            'footer_image' => 'https://pms.shubhampack.in/image_bank/footerlogoimage.png',
            'colorcode' => '#003366'
        ];

        // --- 5. GENERATE THE PDF ---
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE); $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $html = $this->load->view('spares/quotation_pdf_template', $pdf_data, true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();



/// 📄 FILE NAME
$safe_storage_name = preg_replace('/[\/\\\\:*?"<>|]+/', '-', trim((string) $pdf_data['quotation_no']));
$safe_storage_name = $safe_storage_name !== '' ? $safe_storage_name : ('quotation-' . $quotation_id);
$filename = $safe_storage_name . ".pdf";

/// 📁 SAVE FOLDER
$folder_path = FCPATH . 'uploads/spare_quotations/';

/// 📁 CREATE FOLDER IF NOT EXISTS
if (!is_dir($folder_path)) {
    mkdir($folder_path, 0777, true);
}

/// 📄 FULL FILE PATH
$file_path = $folder_path . $filename;

/// 💾 SAVE PDF
file_put_contents(
    $file_path,
    $dompdf->output()
);

        $filename = "Quotation-" . str_replace('/', '-', $pdf_data['quotation_no']) . ".pdf";
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $dompdf->stream($filename, ['Attachment' => 0]);
        exit;
    }

    public function view_quotation_pdf($quotation_id)
    {
        require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';
        $this->ensure_spare_quotation_charge_mode_columns();
        
        // 1. Load the number helper
        $this->load->helper('number');

        // 2. Fetch saved data from the database
        $quote_data = $this->db->get_where('quotations', array('quotation_id' => $quotation_id))->row_array();
        if (!$quote_data) {
            show_error('The requested quotation was not found.');
            return;
        }

        $products_data = $this->db->get_where('quotation_products', array('quotation_id' => $quotation_id))->result_array();

        // 3. Prepare data for the PDF template
        $pdf_data = $quote_data;
        $pdf_data['customer'] = $this->db->get_where('spares_customers', array('customer_id' => $pdf_data['customer_id']))->row();

        // Use the currency saved with the quotation. The migration backfills
        // legacy export rows using their former country-based USD behavior.
        $legacy_currency = ((int) ($pdf_data['customer']->country_id ?? 101) === 101) ? 'INR' : 'USD';
        $pdf_data['currency'] = $this->normalize_spare_currency($pdf_data['currency'] ?? $legacy_currency);
        $pdf_data['currency_type'] = $pdf_data['currency'];
        $pdf_data['curr_symbol'] = $this->get_spare_currency_symbol($pdf_data['currency']);
        $pdf_data['quotation_type'] = $this->normalize_spare_quotation_type($pdf_data['quotation_type'] ?? 'CONSUMABLE') ?: 'CONSUMABLE';
        $pdf_data['custom_engg_type'] = $pdf_data['quotation_type'] === 'CUSTOM_ENGG'
            ? $this->normalize_spare_custom_engg_type($pdf_data['custom_engg_type'] ?? '')
            : null;
        $pdf_data['dispatch_mode'] = $this->normalize_spare_dispatch_mode($pdf_data['dispatch_mode'] ?? 'COURIER') ?: 'COURIER';
        $pdf_data['quotation_type_label'] = $this->get_spare_quotation_type_label($pdf_data['quotation_type'], $pdf_data['custom_engg_type']);
        $pdf_data['dispatch_mode_label'] = $this->get_spare_dispatch_mode_label($pdf_data['dispatch_mode']);

        // ** NEW: Fetch Shipping Info **
        if (isset($pdf_data['shipping_customer_id']) && $pdf_data['shipping_customer_id'] > 0 && $pdf_data['shipping_customer_id'] != $pdf_data['customer_id']) {
            $pdf_data['shipping_customer'] = $this->db->get_where('spares_customers', array('customer_id' => $pdf_data['shipping_customer_id']))->row();
        } else {
            $pdf_data['shipping_customer'] = $pdf_data['customer'];
        }
        
        // Check for line item discounts
        $pdf_data['has_line_item_discount'] = false;
        foreach($products_data as $p) {
            if ($p['discount_percent'] > 0) {
                $pdf_data['has_line_item_discount'] = true;
                break;
            }
        }

        $pdf_data['packing_charge_mode'] = $this->normalize_spare_quote_charge_mode($pdf_data['packing_charge_mode'] ?? 'included');
        $pdf_data['freight_charge_mode'] = $this->normalize_spare_quote_charge_mode($pdf_data['freight_charge_mode'] ?? 'included');
        $pdf_data['ex_work_charge_mode'] = $this->normalize_spare_quote_charge_mode($pdf_data['ex_work_charge_mode'] ?? 'included');
        $pdf_data['insurance_charge_mode'] = $this->normalize_spare_quote_charge_mode($pdf_data['insurance_charge_mode'] ?? 'included');

        $default_item_gst_percent = !empty($pdf_data['gst_percent'])
            ? (float) $pdf_data['gst_percent']
            : $this->get_spare_quote_default_item_gst_percent($pdf_data['currency']);
        $default_charge_gst_percent = $this->get_spare_quote_default_item_gst_percent($pdf_data['currency']);

        $totals = $this->calculate_spare_quote_totals(
            (float) ($pdf_data['basic_value'] ?? 0),
            $pdf_data,
            $products_data,
            $default_item_gst_percent,
            $default_charge_gst_percent
        );
        $pdf_data['products'] = $totals['products'];
        $pdf_data['sub_total'] = $totals['sub_total'];
        $pdf_data['total_value'] = $totals['total_value'];
        $pdf_data['gst_amount'] = $totals['gst_amount'];
        $pdf_data['grand_total'] = $totals['grand_total'];
        $pdf_data['gst_percent'] = $totals['effective_gst_percent'];
        $pdf_data['gst_summary_label'] = $totals['gst_summary_label'];
        
        // Convert amount to words based on the detected currency
        // Note: ensure your get_amount_in_words supports a second parameter for currency
        $pdf_data['amount_in_words'] = get_amount_in_words($pdf_data['grand_total'], $pdf_data['currency']);

        $pdf_data['company_info'] = [
            'name' => 'Shubham Flexible Packaging Machines Pvt. Ltd.',
            'address' => 'B-8A Sector 59, Part II, Ballabhgarh, Faridabad, Haryana India 121004 ',
            'contact' => 'Phone : +91-8377900471',
            'email' => 'spares@shubhampack.com',
            'website' => 'www.shubhampack.com',
            'logo' => 'https://pms.shubhampack.in/assets/images/shubhampack.png',
            'footer_image' => 'https://pms.shubhampack.in/image_bank/footerlogoimage.png',
            'colorcode' => '#003366'
        ];

        // 4. Generate the PDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new \Dompdf\Dompdf($options);
        $html = $this->load->view('spares/quotation_pdf_template', $pdf_data, true);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        /// 📄 FILE NAME
    $filename = $quotation_id.".pdf";

    /// 📁 SAVE FOLDER
    $folder_path = FCPATH . 'uploads/spare_quotations/';

    /// 📁 CREATE FOLDER IF NOT EXISTS
    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    /// 📄 FULL FILE PATH
    $file_path = $folder_path . $filename;

    /// 💾 SAVE PDF
    file_put_contents(
        $file_path,
        $dompdf->output()
    );



        $filename = "Quotation-" . str_replace('/', '-', $pdf_data['quotation_no']) . ".pdf";
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $dompdf->stream($filename, array('Attachment' => 0));
        exit;
    }

public function revise_quotation($opportunity_id)
    {
        if ($this->spare_quotation_expiry_model->get_active_cancellation($opportunity_id)) {
            $this->session->set_flashdata('error', 'Reopen this cancelled quotation before creating a revision.');
            redirect(page_url . 'Spares/opportunity_detail/' . (int) $opportunity_id);
            return;
        }

        $this->ensure_spare_quotation_charge_mode_columns();
        $this->load->model('opportunity_model');
        
        // 1. Find the latest quotation for this opportunity
        $latest_quote = $this->opportunity_model->get_latest_quotation_by_opportunity($opportunity_id);

        if (!$latest_quote) {
            $this->session->set_flashdata('error', 'No quotation found to revise. Please create one first.');
            redirect('Spares/create_quotation/' . $opportunity_id);
            return;
        }

        // 2. Get data for the quote to be revised
        $data['original_quotation'] = $latest_quote;
        $data['products'] = $this->opportunity_model->get_quotation_products($latest_quote->quotation_id);
        $data['opportunity'] = $this->opportunity_model->get_opportunity_details($opportunity_id);
        $data['recent_price_history_map'] = $this->build_recent_price_history_map($data['products']);
        $data['quotation_type_options'] = $this->get_spare_quotation_type_options();
        $data['dispatch_mode_options'] = $this->get_spare_dispatch_mode_options();
        $data['custom_engg_type_options'] = $this->get_spare_custom_engg_type_options();
        $data['selected_quotation_type'] = $this->normalize_spare_quotation_type($latest_quote->quotation_type ?? 'CONSUMABLE') ?: 'CONSUMABLE';
        $data['selected_dispatch_mode'] = $this->normalize_spare_dispatch_mode($latest_quote->dispatch_mode ?? 'COURIER') ?: 'COURIER';
        $data['selected_custom_engg_type'] = $data['selected_quotation_type'] === 'CUSTOM_ENGG'
            ? $this->normalize_spare_custom_engg_type($latest_quote->custom_engg_type ?? '')
            : '';
        
        // This is only needed for the hidden template row, which is no longer used with AJAX Select2
        // $data['all_products'] = $this->db->get('products')->result();

        // 3. ** UPDATED: Generate the new REVISED quotation number in the correct format **
        // Find the base number (e.g., SQRF27135)
        $base_quote_no = strtok($latest_quote->quotation_no, '/');
        
        // Find the highest revision number for this base number
        $this->db->select_max('revision_no');
        $this->db->like('quotation_no', $base_quote_no, 'after');
        $max_rev = $this->db->get('quotations')->row()->revision_no;

        $data['new_revision_no'] = $max_rev + 1;
        // Construct the new format: SQRF27135/2025/R1
        $data['new_quotation_no'] = $base_quote_no . '/' . date('Y') . '/R' . $data['new_revision_no'];
        
        // 4. Load the revision view
        $this->load->view('spares/revise_quotation_view', $data);
    }

    public function edit_opportunity($id = 0)
{
    // 1. Fetch the main opportunity data
    $this->load->model('Opportunity_model'); // Assuming you have a model named Spares_model
    $data['opportunity'] = $this->Opportunity_model->get_opportunity_by_id($id);

    if (empty($data['opportunity'])) {
        $this->session->set_flashdata('error', 'Opportunity not found!');
        redirect('Spares/opportunity_list');
    }

    // 2. Fetch associated products for this opportunity
    $data['opportunity_products'] = $this->Opportunity_model->get_opportunity_products($id);

    // 3. Fetch necessary data for dropdowns (same as your add form)
    $data['marketing_persons'] = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id', 31)->where('user_status', 1)->get()->result();
    $data['sources'] = $this->db->select('source_id, lead_source')->from('lead_source')->where('status', 1)->get()->result();
    $data['exhibitions'] = $this->db->select('id, exhibition')->from('exhibition_info')->order_by('exhibition', 'asc')->get()->result();
    $data['countries'] = $this->db->select('country_name, country_id')->from('countries')->where('country_status', 1)->order_by('country_name', 'asc')->get()->result();
    
    // 4. Load the view (we will create/modify this file in the next step)
    $this->load->view('spares/opportunity_form', $data); 
}

public function update_opportunity()
{
    $user_id = $_SESSION['logged_in']['user_id'];
    $opportunity_id = $this->input->post('opportunity_id');
    
    // Re-enable op_type if it was disabled on the form
    if ($this->input->post('op_type')) {
        $_POST['op_type'] = $this->input->post('op_type');
    } else {
        $temp_op = $this->db->select('op_type')->get_where('opportunities', array('opportunity_id' => $opportunity_id))->row();
        if($temp_op){ $_POST['op_type'] = $temp_op->op_type; }
    }

    if (!$opportunity_id) {
        $this->session->set_flashdata('message', '<div class="alert alert-danger">Error: Invalid Opportunity ID.</div>');
        redirect('Spares/opportunity_list');
    }

    // Set validation rules (mirroring your add function)
    $this->form_validation->set_rules('op_date', 'Opportunity Date', 'required');
    $this->form_validation->set_rules('lsource', 'Source', 'required|numeric');
    $this->form_validation->set_rules('op_type', 'Opportunity Type', 'required|numeric');
    $this->form_validation->set_rules('marketing', 'Marketing Person', 'required|numeric');
    $this->form_validation->set_rules('customer', 'Customer', 'required|numeric');
    $this->form_validation->set_rules('brand', 'Brand', 'required|numeric');
    $this->form_validation->set_rules('country', 'Country', 'required|numeric');
    $this->form_validation->set_rules('address', 'Customer Address', 'required');
    $this->form_validation->set_rules('customercontactno', 'Contact Number', 'required');
    $this->form_validation->set_rules('customeremailid', 'Email ID', 'required');
    $this->form_validation->set_rules('customertype', 'Client Type', 'required|numeric');
    $this->form_validation->set_rules('probability', 'Probability', 'required|numeric');
    $this->form_validation->set_rules('product[]', 'Product', 'required');
    $this->form_validation->set_rules('qty[]', 'Quantity', 'required|numeric|greater_than[0]');

    if ($this->form_validation->run() == FALSE) {
        $this->session->set_flashdata('message', validation_errors());
        redirect(page_url.'Spares/edit_opportunity/' . $opportunity_id);
        return;
    }

    $opportunity_data = [
        'op_date'             => $this->input->post('op_date'),
        'op_type'             => $this->input->post('op_type'), // Cannot be changed, but needed for consistency
        'customer_id'         => $this->input->post('customer'),
        'brand_id'            => $this->input->post('brand'),
        'country_id'          => $this->input->post('country'),
        'customer_address'    => $this->input->post('address'),
        'customer_contact_no' => $this->input->post('customercontactno'),
        'customer_email'      => $this->input->post('customeremailid'),
        'source_id'           => $this->input->post('lsource'),
        'exhibition_id'       => ($this->input->post('lsource') == 8) ? $this->input->post('exhibitionname') : NULL,
        'marketing_person_id' => $this->input->post('marketing'),
        'client_type'         => $this->input->post('customertype'),
        'probability'         => $this->input->post('probability'),
        'remarks'             => $this->input->post('remarks'),
        'tax_number'          => $this->input->post('gst')];

    $products = $this->input->post('product');
    $quantities = $this->input->post('qty');
    $product_data = [];
    if (!empty($products)) {
        for ($i = 0; $i < count($products); $i++) {
            if (!empty($products[$i]) && !empty($quantities[$i])) {
                $product_data[] = [
                    'opportunity_id' => $opportunity_id,
                    'product_id'     => $products[$i],
                    'quantity'       => $quantities[$i]
                ];
            }
        }
    }

    $this->load->model('Opportunity_model');
    if ($this->Opportunity_model->update_opportunity($opportunity_id, $opportunity_data, $product_data)) {
        $this->session->set_flashdata('success', 'Opportunity updated successfully!');
    } else {
        $this->session->set_flashdata('error', 'Failed to update opportunity. Please try again.');
    }

    redirect(page_url.'Spares/opportunity_list');
}

public function create_pi($opportunity_id)
{
    if ($this->spare_quotation_expiry_model->get_active_cancellation($opportunity_id)) {
        $this->session->set_flashdata('error', 'Reopen this cancelled quotation before creating a PI.');
        redirect(page_url . 'Spares/opportunity_detail/' . (int) $opportunity_id);
        return;
    }

    $this->load->model('opportunity_model');
    $this->load->helper('number');
    $this->ensure_spare_pi_dependencies();

    $data['opportunity'] = $this->opportunity_model->get_opportunity_details($opportunity_id);

    if (!$data['opportunity']) {
        show_404();
    }

    $quote_bundle = $this->get_latest_spare_quote_with_items($opportunity_id);
    $latest_quote = $quote_bundle['quote'];

    if (!$latest_quote) {
        $this->session->set_flashdata('error', 'Create a quotation before generating PI for this opportunity.');
        redirect(page_url . 'Spares/create_quotation/' . (int) $opportunity_id);
    }

    $existing_pi = $this->spare_pi_model->get_by_opportunity($opportunity_id);
    $customer = $this->get_spares_customer_snapshot($data['opportunity']);
    $company_profile = $this->get_spares_company_profile();

    if ($existing_pi) {
        $pi = $existing_pi;
        $pi_items = $this->spare_pi_model->get_items($existing_pi->id);
    } else {
        $currency = $this->normalize_spare_currency(
            $latest_quote->currency ?? $this->get_spare_pi_default_currency($customer)
        );
        $default_gst_percent = isset($latest_quote->gst_percent)
            ? (float) $latest_quote->gst_percent
            : ($currency === 'INR' ? 18 : 0);

        $packing_mode = $this->normalize_spare_quote_charge_mode($latest_quote->packing_charge_mode ?? 'included');
        $freight_mode = $this->normalize_spare_quote_charge_mode($latest_quote->freight_charge_mode ?? 'included');
        $ex_work_mode = $this->normalize_spare_quote_charge_mode($latest_quote->ex_work_charge_mode ?? 'included');
        $insurance_mode = $this->normalize_spare_quote_charge_mode($latest_quote->insurance_charge_mode ?? 'included');

        $pi = (object) [
            'id' => 0,
            'quote_id' => $latest_quote->quotation_id ?? null,
            'pi_no' => $this->build_spare_pi_number(),
            'pi_date' => date('Y-m-d'),
            'currency' => $currency,
            'attention' => $latest_quote->attention ?? ($customer->contact_person ?? ''),
            'buyer_name' => $customer->company_name ?? '',
            'buyer_contact' => $customer->contact_person ?? '',
            'buyer_email' => $customer->email ?? '',
            'buyer_address' => $customer->address ?? '',
            'buyer_gstin' => $customer->tax_number ?? '',
            'consignee_name' => $customer->company_name ?? '',
            'consignee_contact' => $customer->contact_person ?? '',
            'consignee_phone' => $customer->contact_person_no ?? '',
            'consignee_address' => $customer->address ?? '',
            'consignee_gstin' => $customer->tax_number ?? '',
            'reference_quote_no' => $latest_quote->quotation_no ?? '',
            'reference_quote_date' => $latest_quote->quotation_date ?? null,
            'payment_terms' => $latest_quote->payment_terms ?? '',
            'validity' => $latest_quote->validity ?? '',
            'delivery_terms' => $latest_quote->delivery_terms ?? '',
            'packing_percent' => $packing_mode === 'extra' ? 0 : (float) ($latest_quote->packing_percent ?? 0),
            'packing_charge' => $packing_mode === 'extra' ? 0 : (float) ($latest_quote->packing_charge ?? 0),
            'freight_charge' => $freight_mode === 'extra' ? 0 : (float) ($latest_quote->freight_charge ?? 0),
            'ex_work_charge' => $ex_work_mode === 'extra' ? 0 : (float) ($latest_quote->ex_work_charge ?? 0),
            'insurance_percent' => $insurance_mode === 'extra' ? 0 : (float) ($latest_quote->insurance_percent ?? 0),
            'insurance_charge' => $insurance_mode === 'extra' ? 0 : (float) ($latest_quote->insurance_charge ?? 0),
            'overall_discount_percent' => (float) ($latest_quote->overall_discount_percent ?? 0),
            'overall_discount_amount' => (float) ($latest_quote->overall_discount_amount ?? 0),
            'basic_value' => (float) ($latest_quote->basic_value ?? 0),
            'total_value' => (float) ($latest_quote->total_value ?? 0),
            'gst_percent' => $default_gst_percent,
            'gst_amount' => 0,
            'grand_total' => 0,
            'amount_in_words' => '',
            'notes' => $this->get_spare_pi_default_notes(),
            'declaration_text' => $this->get_spare_pi_default_declaration(),
        ];

        $pi_items = $this->build_spare_pi_items_from_quote($quote_bundle['items']);

        if ((float) $pi->basic_value <= 0) {
            foreach ($pi_items as $item) {
                $pi->basic_value += (float) ($item->total_price ?? 0);
            }
        }

        $pi->packing_charge = round(($pi->basic_value * (float) $pi->packing_percent) / 100, 2);
        $pi->insurance_charge = round(($pi->basic_value * (float) $pi->insurance_percent) / 100, 2);
        $pi->total_value = round(
            $pi->basic_value
            + (float) $pi->packing_charge
            + (float) $pi->freight_charge
            + (float) $pi->ex_work_charge
            + (float) $pi->insurance_charge
            - (float) $pi->overall_discount_amount,
            2
        );
        $pi->gst_amount = round(($pi->total_value * (float) $pi->gst_percent) / 100, 2);
        $pi->grand_total = round($pi->total_value + $pi->gst_amount, 2);
    }

    if (empty($pi->amount_in_words)) {
        $pi->amount_in_words = $this->get_spare_pi_amount_in_words((float) $pi->grand_total, $pi->currency);
    } else {
        $pi->amount_in_words = $this->normalize_spare_pi_amount_in_words($pi->amount_in_words, $pi->currency);
    }

    $data['customer'] = $customer;
    $data['company_profile'] = $company_profile;
    $data['pi'] = $pi;
    $data['pi_items'] = $pi_items;
    $data['latest_quote'] = $latest_quote;

    $this->load->view('spares/create_pi_view', $data);
}

public function save_pi_details()
{
    $this->load->model('opportunity_model');
    $this->load->helper('number');
    $pi_stage_id = $this->ensure_spare_pi_dependencies();

    $post = $this->input->post();
    $opportunity_id = (int) ($post['opportunity_id'] ?? 0);
    if ($this->spare_quotation_expiry_model->get_active_cancellation($opportunity_id)) {
        $this->session->set_flashdata('error', 'Reopen this cancelled quotation before saving a PI.');
        redirect(page_url . 'Spares/opportunity_detail/' . $opportunity_id);
        return;
    }
    $pi_id = (int) ($post['pi_id'] ?? 0);
    $action_type = trim((string) ($post['action_type'] ?? 'save'));
    $user_id = $this->session->userdata['logged_in']['user_id'] ?? null;

    $opportunity = $this->opportunity_model->get_opportunity_details($opportunity_id);
    if (!$opportunity) {
        show_404();
    }

    $customer = $this->get_spares_customer_snapshot($opportunity);
    $quote_bundle = $this->get_latest_spare_quote_with_items($opportunity_id);
    $latest_quote = $quote_bundle['quote'];
    $totals = $this->parse_spare_pi_items_from_post($post);

    if (empty($totals['items'])) {
        $this->session->set_flashdata('error', 'Please add at least one PI line item.');
        redirect(page_url . 'Spares/create_pi/' . $opportunity_id);
    }

    $currency = strtoupper(trim((string) ($post['currency'] ?? $this->get_spare_pi_default_currency($customer))));
    $currency = $this->normalize_spare_currency($currency);

    $pi_no = trim((string) ($post['pi_no'] ?? ''));
    if ($pi_no === '') {
        $pi_no = $this->build_spare_pi_number();
    }

    $reference_quote_no = trim((string) ($post['reference_quote_no'] ?? ($latest_quote->quotation_no ?? '')));
    $reference_quote_date = !empty($post['reference_quote_date'])
        ? date('Y-m-d', strtotime($post['reference_quote_date']))
        : (!empty($latest_quote->quotation_date) ? $latest_quote->quotation_date : null);

    $notes = trim((string) ($post['notes'] ?? ''));
    if ($notes === '') {
        $notes = $this->get_spare_pi_default_notes();
    }

    $declaration_text = trim((string) ($post['declaration_text'] ?? ''));
    if ($declaration_text === '') {
        $declaration_text = $this->get_spare_pi_default_declaration();
    }

    $pi_data = [
        'opportunity_id' => $opportunity_id,
        'quote_id' => (int) ($post['quote_id'] ?? ($latest_quote->quotation_id ?? 0)) ?: null,
        'customer_id' => (int) ($opportunity->customer_id ?? 0) ?: null,
        'pi_no' => $pi_no,
        'pi_date' => !empty($post['pi_date']) ? date('Y-m-d', strtotime($post['pi_date'])) : date('Y-m-d'),
        'currency' => $currency,
        'attention' => trim((string) ($post['attention'] ?? '')),
        'buyer_name' => trim((string) ($post['buyer_name'] ?? '')),
        'buyer_contact' => trim((string) ($post['buyer_contact'] ?? '')),
        'buyer_email' => trim((string) ($post['buyer_email'] ?? '')),
        'buyer_address' => trim((string) ($post['buyer_address'] ?? '')),
        'buyer_gstin' => trim((string) ($post['buyer_gstin'] ?? '')),
        'consignee_name' => trim((string) ($post['consignee_name'] ?? '')),
        'consignee_contact' => trim((string) ($post['consignee_contact'] ?? '')),
        'consignee_phone' => trim((string) ($post['consignee_phone'] ?? '')),
        'consignee_address' => trim((string) ($post['consignee_address'] ?? '')),
        'consignee_gstin' => trim((string) ($post['consignee_gstin'] ?? '')),
        'reference_quote_no' => $reference_quote_no,
        'reference_quote_date' => $reference_quote_date,
        'payment_terms' => trim((string) ($post['payment_terms'] ?? '')),
        'validity' => trim((string) ($post['validity'] ?? '')),
        'delivery_terms' => trim((string) ($post['delivery_terms'] ?? '')),
        'packing_percent' => $totals['packing_percent'],
        'packing_charge' => $totals['packing_charge'],
        'freight_charge' => $totals['freight_charge'],
        'ex_work_charge' => $totals['ex_work_charge'],
        'insurance_percent' => $totals['insurance_percent'],
        'insurance_charge' => $totals['insurance_charge'],
        'overall_discount_percent' => $totals['overall_discount_percent'],
        'overall_discount_amount' => $totals['overall_discount_amount'],
        'basic_value' => $totals['basic_value'],
        'total_value' => $totals['total_value'],
        'gst_percent' => $totals['gst_percent'],
        'gst_amount' => $totals['gst_amount'],
        'grand_total' => $totals['grand_total'],
        'amount_in_words' => $this->get_spare_pi_amount_in_words($totals['grand_total'], $currency),
        'notes' => $notes,
        'declaration_text' => $declaration_text,
        'updated_by' => $user_id,
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    $this->db->trans_start();

    if ($pi_id > 0 && $this->spare_pi_model->get_by_id($pi_id)) {
        $this->db->where('id', $pi_id)->update($this->spare_pi_model->get_table_name(), $pi_data);
        $history_message = 'PI updated: ' . $pi_data['pi_no'] . '. Pending customer PO.';
    } else {
        $pi_data['created_by'] = $user_id;
        $pi_data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->spare_pi_model->get_table_name(), $pi_data);
        $pi_id = (int) $this->db->insert_id();
        $history_message = 'PI created: ' . $pi_data['pi_no'] . '. Pending customer PO.';
    }

    $this->db->where('spare_pi_id', $pi_id)->delete($this->spare_pi_model->get_item_table_name());

    $item_rows = [];
    foreach ($totals['items'] as $item) {
        $item_rows[] = [
            'spare_pi_id' => $pi_id,
            'sort_order' => $item['sort_order'],
            'product_id' => $item['product_id'],
            'product_code' => $item['product_code'],
            'hsn_code' => $item['hsn_code'],
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'unit' => $item['unit'],
            'unit_price' => $item['unit_price'],
            'discount_percent' => $item['discount_percent'],
            'discount_amount' => $item['discount_amount'],
            'total_price' => $item['total_price'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    if (!empty($item_rows)) {
        $this->db->insert_batch($this->spare_pi_model->get_item_table_name(), $item_rows);
    }

    $this->insert_spare_progress_history($opportunity_id, $pi_stage_id, $history_message, 'Pending for PO');

    $this->db->trans_complete();

    if ($this->db->trans_status() === false) {
        $this->session->set_flashdata('error', 'Failed to save PI details. Please try again.');
        redirect(page_url . 'Spares/create_pi/' . $opportunity_id);
    }

    $this->session->set_flashdata('success', 'PI details saved successfully.');

    if ($action_type === 'pdf') {
        redirect(page_url . 'Spares/view_pi_pdf/' . $pi_id);
    }

    redirect(page_url . 'Spares/opportunity_detail/' . $opportunity_id);
}

public function view_pi_pdf($pi_id = null)
{
    $this->load->model('opportunity_model');
    $this->load->helper('number');
    $this->ensure_spare_pi_dependencies();

    $pi_id = $pi_id !== null ? (int) $pi_id : (int) $this->uri->segment(3);
    $pi = $this->spare_pi_model->get_by_id($pi_id);

    if (!$pi) {
        show_404();
    }

    $opportunity = $this->opportunity_model->get_opportunity_details($pi->opportunity_id);

    if (!$opportunity) {
        show_404();
    }

    $currency = strtoupper(trim((string) ($pi->currency ?? 'INR')));

    $pdf_data = [
        'pi' => $pi,
        'items' => $this->spare_pi_model->get_items($pi_id),
        'opportunity' => $opportunity,
        'customer' => $this->get_spares_customer_snapshot($opportunity),
        'company_profile' => $this->get_spares_company_profile(),
        'currency_symbol' => $this->get_spare_currency_symbol($currency),
        'amount_in_words' => !empty($pi->amount_in_words)
            ? $this->normalize_spare_pi_amount_in_words($pi->amount_in_words, $currency)
            : $this->get_spare_pi_amount_in_words((float) $pi->grand_total, $currency),
    ];

    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);
    $html = $this->load->view('spares/pi_pdf_template', $pdf_data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length()) {
        ob_end_clean();
    }

    $folder_path = FCPATH . 'uploads/spare_pis/';
    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    file_put_contents($folder_path . $pi_id . '.pdf', $dompdf->output());

    $safe_file_name = preg_replace('/[\/\\\\:*?"<>|]+/', '-', 'PI-' . trim((string) $pi->pi_no) . '-' . trim((string) $opportunity->op_no));
    $safe_file_name = rtrim($safe_file_name, '-');

    $dompdf->stream($safe_file_name . '.pdf', ['Attachment' => 0]);
}


public function create_po($opportunity_id)
{
    if ($this->spare_quotation_expiry_model->get_active_cancellation($opportunity_id)) {
        $this->session->set_flashdata('error', 'Reopen this cancelled quotation before recording a PO.');
        redirect(page_url . 'Spares/opportunity_detail/' . (int) $opportunity_id);
        return;
    }

    $this->load->model('opportunity_model');
    $data = $this->opportunity_model->get_data_for_po($opportunity_id);

    if (!$data) {
        show_404();
    }

    $this->load->view('spares/create_po_view', $data);
}

public function save_po()
{
    // Ensure this is an AJAX request
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $opportunity_id = (int) $this->input->post('opportunity_id');
    if ($this->spare_quotation_expiry_model->get_active_cancellation($opportunity_id)) {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Reopen this cancelled quotation before recording a PO.'
        ));
        return;
    }

    // 1. Server-side Validation
    $this->load->library('form_validation');
    $this->form_validation->set_rules('po_no', 'PO Number', 'required|trim');
    $this->form_validation->set_rules('po_date', 'PO Date', 'required');
    $this->form_validation->set_rules('products[]', 'Products', 'required');

    if ($this->form_validation->run() == FALSE) {
        echo json_encode(['status' => 'error', 'message' => strip_tags(validation_errors())]);
        return;
    }

    // 2. Handle File Upload (PO Copy)
    $po_file_name = NULL;
    if (!empty($_FILES['po_file']['name'])) {
        $config['upload_path']   = './uploads/po_copies/'; // Ensure this folder exists and is writable
        $config['allowed_types'] = 'pdf|jpg|jpeg|png|xls';
        $config['max_size']      = 50120; // 5MB
        $config['file_name']     = 'PO_' . time() . '_' . $_FILES['po_file']['name'];

        // Create directory if not exists
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('po_file')) {
            echo json_encode(['status' => 'error', 'message' => 'File Upload Error: ' . $this->upload->display_errors('', '')]);
            return;
        } else {
            $upload_data = $this->upload->data();
            $po_file_name = $upload_data['file_name'];
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please upload a copy of the PO.']);
        return;
    }

    $this->db->trans_start(); // Start transaction
    
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $opportunity_id = $this->input->post('opportunity_id');
    $customer_id = $this->input->post('customer_id');

    // 3. Insert into purchase_orders table
   $po_data = [
    'opportunity_id'    => $opportunity_id,
    'customer_id'       => $customer_id,
    'po_no'             => $this->input->post('po_no'),
    'po_date'           => date('Y-m-d', strtotime($this->input->post('po_date'))),
    'expected_delivery_date' => $this->input->post('delivery_date') ? date('Y-m-d', strtotime($this->input->post('delivery_date'))) : NULL,
    'sub_total'         => $this->input->post('sub_total'),
    'packing_percent'   => $this->input->post('packing_percent'),
    'freight_charge'    => $this->input->post('freight_charge'),
    'insurance_percent' => $this->input->post('insurance_percent'),
    'taxable_value'     => $this->input->post('taxable_value'), // <--- Ensure this is here
    'gst_amount'        => $this->input->post('gst_amount'),
    'grand_total'       => $this->input->post('grand_total'),
    'po_copy'           => $po_file_name,
    'created_by'        => $user_id,
    'created_at'        => date('Y-m-d H:i:s')
];
    
    $this->db->insert('purchase_orders', $po_data);
    $po_id = $this->db->insert_id();

    // 4. Insert into po_products table
    $products = $this->input->post('products');
    if (!empty($products)) {
        $po_products_batch = [];
        foreach ($products as $prod) {
            $po_products_batch[] = [
                'po_id'       => $po_id,
                'product_id'  => $prod['product_id'],
                'description' => $prod['description'],
                'quantity'    => $prod['quantity'],
                'price'       => $prod['price'],
                'total'       => $prod['quantity'] * $prod['price']
            ];
        }
        $this->db->insert_batch('po_products', $po_products_batch);
    }

    // 5. Update Opportunity Status to 'Won'
    $this->db->where('opportunity_id', $opportunity_id);
    $this->db->update('opportunities', ['status' => 'Won']);

    // 6. Add progress remark for receiving PO (Stage ID 14)
    $remark_data = [
        'lead_id'      => $opportunity_id,
        'lead_stage'   => 14, 
        'remarks'      => 'Purchase Order ' . $this->input->post('po_no') . ' received and uploaded.',
        'remark_title' => 'PO has been Created.',
        'added_by'     => $user_id,
        'added_on'     => date('Y-m-d H:i:s')
    ];
    $this->db->insert('spare_progress_remarks', $remark_data);

    $this->db->trans_complete(); // Complete transaction

    if ($this->db->trans_status() === FALSE) {
        // Remove uploaded file if DB fails
        if ($po_file_name) { @unlink($config['upload_path'] . $po_file_name); }
        echo json_encode(['status' => 'error', 'message' => 'Failed to save PO. Database error.']);
    } else {
        echo json_encode([
            'status'       => 'success', 
            'message'      => 'Purchase Order saved and file uploaded successfully!',
            'redirect_url' => page_url . 'Spares/opportunity_detail/' . $opportunity_id,
            'pdf_url'      => page_url . 'Spares/view_po_pdf/' . $po_id
        ]);
    }
}


public function view_po_pdf($po_id)
{
    // Load necessary components
    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';
    $this->load->model('opportunity_model');
    $this->load->helper('number');

    // 1. Fetch all data for the PO
    $pdf_data = $this->opportunity_model->get_po_details_for_pdf($po_id);

    if (!$pdf_data) {
        show_404();
        return;
    }
    
    // 2. Prepare additional data for the PDF template
    // This structure matches your quotation PDF for easy reuse
    $pdf_data['amount_in_words'] = get_amount_in_words($pdf_data['po_details']->grand_total);
    $pdf_data['company_info'] = [
        'name' => 'Shubham Flexible Packaging Machine Pvt. Ltd.', 
        'address' => 'B-8A, Sector 59 Part II, Ballabgarh, Faridabad - 121004, Haryana, India',
        'contact' => 'Phone: +91-8377900471', 
        'email' => 'spares@shubhampack.com', 
        'website' => 'www.shubhampack.com',
        'logo' => 'https://pms.shubhampack.in/assets/images/shubhampack.png'
    ];
     // Assume GST is a flat rate for simplicity, or get it from a config/DB if it varies
    $pdf_data['gst_percent'] = 18.00; 

    // 3. Generate the PDF
    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);
    $options->set('defaultFont', 'Helvetica');
    
    $dompdf = new Dompdf($options);
    
    // Load the new PO PDF template view
    $html = $this->load->view('spares/po_pdf_template', $pdf_data, true);
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $filename = "PO-" . str_replace('/', '-', $pdf_data['po_details']->po_no) . ".pdf";
    $dompdf->stream($filename, ['Attachment' => 0]); // Display in browser
}

// In Spares.php controller

public function followup_list($type, $user_id = NULL)
{
    $this->refresh_expired_spare_quotations();
    $this->load->model('Dashboard_model');
    $data = [];

    // Set a dynamic page title based on the followup type
    switch ($type) {
        case 1:
            $data['page_title'] = "Today's Followups";
            break;
        case 2:
            $data['page_title'] = "Missed Followups";
            break;
        case 3:
            $data['page_title'] = "Upcoming Followups";
            break;
        default:
            redirect(base_url('spares/dashboard')); // Redirect if type is invalid
            break;
    }
    
    $data['followup_type'] = $type; // Pass the type to the view for styling
    
    // Get the list of opportunities from the model
    $data['opportunities'] = $this->Dashboard_model->get_followup_opportunities($type, $user_id);

    // Load the new view file
    $this->load->view('spares/followup_list_view', $data);
}


public function orderwon($opportunity_id)
{
    $this->load->model('opportunity_model');
    $data['order_data'] = $this->opportunity_model->get_data_for_order_won($opportunity_id);

    // If no PO has been created for this opportunity, the user cannot mark it as "won".
    if (!$data['order_data']) {
        $this->session->set_flashdata('error', 'Cannot mark as "Order Won" because no Purchase Order has been saved for this opportunity yet.');
        redirect(page_url.'Spares/opportunity_detail/' . $opportunity_id);
    }
    
    // Check if this order has already been saved to prevent duplicates
    $existing_order = $this->db->get_where('spares_orders', ['po_id' => $data['order_data']->po_id])->row();
    if ($existing_order) {
        $existing_execution = $this->db
            ->select('execution_order_id')
            ->from('spares_execution_orders')
            ->where('order_id', (int) $existing_order->order_id)
            ->get()
            ->row();

        $this->session->set_flashdata('success', 'This order was already marked as won earlier. Continue with the Spares execution flow.');

        if (!empty($existing_execution)) {
            redirect(page_url . 'Spares_execution/order/' . (int) $existing_order->order_id);
        }

        redirect(page_url . 'Spares_execution/schedule/' . (int) $existing_order->order_id);
    }

    $this->load->view('spares/order_won_view', $data);
}


public function save_won_order()
{
    $user_id = (int) $_SESSION['logged_in']['user_id'];
    // Ensure this is an AJAX request
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $po_id = (int) $this->input->post('po_id');
    $opportunity_id = (int) $this->input->post('opportunity_id');

    $existing_order = $this->db->get_where('spares_orders', ['po_id' => $po_id])->row();
    if ($existing_order) {
        $existing_execution = $this->db
            ->select('execution_order_id')
            ->from('spares_execution_orders')
            ->where('order_id', (int) $existing_order->order_id)
            ->get()
            ->row();

        $redirect_url = !empty($existing_execution)
            ? page_url . 'Spares_execution/order/' . (int) $existing_order->order_id
            : page_url . 'Spares_execution/schedule/' . (int) $existing_order->order_id;

        $this->session->set_flashdata('success', 'This won order was already saved earlier. Continue with the Spares execution flow.');

        echo json_encode([
            'status' => 'success',
            'message' => 'This order was already saved earlier. Opening its Spares execution flow.',
            'redirect_url' => $redirect_url,
            'order_id' => (int) $existing_order->order_id,
        ]);
        return;
    }

    $this->load->library('form_validation');
    $this->form_validation->set_rules('po_id', 'Purchase Order ID', 'required');
    $this->form_validation->set_rules('order_date', 'Order Date', 'required');

    if ($this->form_validation->run() == FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'The order data is invalid. Please refresh and try again.']);
        return;
    }

    $this->db->trans_start(); // Start transaction

    // 1. Insert into the new spares_orders table
    $order_data = [
        'opportunity_id'      => $opportunity_id,
        'po_id'               => $po_id,
        'customer_id'         => $this->input->post('customer_id'),
        'marketing_person_id' => $this->input->post('marketing_person_id'),
        'order_value'         => $this->input->post('order_value'),
        'order_date'          => date('Y-m-d', strtotime($this->input->post('order_date'))),
        'created_by'          => $user_id,
        'created_at'=> date('Y-m-d H:i:s')
    ];
    $this->db->insert('spares_orders', $order_data);
    $saved_order_id = (int) $this->db->insert_id();

    /*Update Opportunity Table as well */
    $leaddata = array('status'=>1,
'probability'=>100);
    $this->db->where('opportunity_id',$opportunity_id);
    $this->db->update('opportunities',$leaddata);

    // 2. Add final progress remark for "Order Won" (Stage ID 10)
    $remark_data = [
        'lead_id'    => $opportunity_id,
        'lead_stage' => 10, // 'Order Won' stage ID
        'remarks'    => 'Order confirmed and saved based on PO ' . $this->input->post('po_no') . '.',
        'added_by'   => $user_id,
        'added_on'   => date('Y-m-d H:i:s')
    ];
    $this->db->insert('spare_progress_remarks', $remark_data);

    $this->db->trans_complete(); // Complete transaction

    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save the order due to a database error.']);
    } else {
        $this->session->set_flashdata('success', 'Order successfully saved. Please schedule the Spares execution tasks to start tracking this order.');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Order successfully saved. Opening Spares execution scheduling...',
            'redirect_url' => page_url . 'Spares_execution/schedule/' . $saved_order_id,
            'order_id' => $saved_order_id
        ]);
    }
}

public function modify_po($po_id)
{
    $this->load->model('opportunity_model');
    // We can reuse the same model function from the PDF generation
    $data = $this->opportunity_model->get_po_details_for_pdf($po_id);

    if (!$data) {
        show_404();
    }

    $this->load->view('spares/modify_po_view', $data);
}


public function update_po()
{
    // Ensure this is an AJAX request
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }
    
    $po_id = $this->input->post('po_id');
    $opportunity_id = $this->input->post('opportunity_id');

    $this->db->trans_start(); // Start transaction

    // 1. Update the main purchase_orders table
    $po_data = [
        'po_no'                  => $this->input->post('po_no'),
        'po_date'                => date('Y-m-d', strtotime($this->input->post('po_date'))),
        'expected_delivery_date' => $this->input->post('delivery_date') ? date('Y-m-d', strtotime($this->input->post('delivery_date'))) : NULL,
        'sub_total'              => $this->input->post('sub_total'),
        'grand_total'            => $this->input->post('grand_total')
    ];
    $this->db->where('po_id', $po_id);
    $this->db->update('purchase_orders', $po_data);

    // 2. Delete old products for this PO
    $this->db->where('po_id', $po_id);
    $this->db->delete('po_products');

    // 3. Re-insert the updated list of products
    $products = $this->input->post('products');
    $po_products_batch = [];
    if (!empty($products)) {
        foreach ($products as $prod) {
            $po_products_batch[] = [
                'po_id'       => $po_id,
                'product_id'  => $prod['product_id'],
                'description' => $prod['description'],
                'quantity'    => $prod['quantity'],
                'price'       => $prod['price'],
                'total'       => $prod['quantity'] * $prod['price']
            ];
        }
        $this->db->insert_batch('po_products', $po_products_batch);
    }
    
    // Also update the related final order if it exists
    $this->db->where('po_id', $po_id);
    $this->db->update('spares_orders', ['order_value' => $this->input->post('grand_total')]);


    $this->db->trans_complete(); // Complete transaction

    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update PO. Database error.']);
    } else {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Purchase Order updated successfully!',
            'redirect_url' => page_url . 'Spares/opportunity_detail/' . $opportunity_id
        ]);
    }
}

// In Spares.php controller

public function running_orders_list()
{
    // You can reuse the Opportunity_model or create a new Order_model
    $this->load->model('opportunity_model'); 
    
    // Fetch the data from the new model function
    $data['running_orders'] = $this->opportunity_model->get_running_orders();

    // Load the new view file
    $this->load->view('spares/running_orders_list_view', $data);
}

// In Spares.php controller

public function order_detail($order_id)
{
    $this->load->model('opportunity_model');
    $data = $this->opportunity_model->get_order_details_for_tracking($order_id);

    if (!$data) {
        show_404();
    }
    
    // Add a flash message if one exists from another page
    if($this->session->flashdata('info')) {
        $data['info_message'] = $this->session->flashdata('info');
    }
     if($this->session->flashdata('error')) {
        $data['error_message'] = $this->session->flashdata('error');
    }

    $this->load->view('spares/order_detail_view', $data);
}

public function save_order_progress($order_id)
{
        $user_id=$_SESSION['logged_in']['user_id'];
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $this->load->library('form_validation');
    $this->form_validation->set_rules('new_stage_id', 'New Stage', 'required|numeric');
    $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');

    if ($this->form_validation->run() == FALSE) {
        echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        return;
    }

    $this->db->trans_start();

    $new_stage_id = $this->input->post('new_stage_id');
     $followup_date = $this->input->post('followup_date');
    // 1. Insert the progress remark into the history table
    $remark_data = [
        'order_id'       => $order_id,
        'order_stage_id' => $new_stage_id,
        'remarks'        => $this->input->post('remarks'),
        'added_by'       => $user_id,
        'next_follow_date' => $followup_date ? date('Y-m-d', strtotime($followup_date)) : NULL,
        'added_on'=> date('Y-m-d H:i:s')
    ];
    $this->db->insert('spares_order_remarks', $remark_data);

    // 2. Update the main order record
    $order_update_data = [
        'current_stage_id' => $new_stage_id
    ];
    
    // 3. If the stage is "Completed" or "Cancelled", update the main status
    // This will remove it from the "Running Orders" list
    if ($new_stage_id == 5) { // Assuming 5 is 'Completed'
        $order_update_data['status'] = 'Completed';
    } else if ($new_stage_id == 6) { // Assuming 6 is 'Cancelled'
         $order_update_data['status'] = 'Cancelled';
    } else {
        $order_update_data['status'] = 'Running'; // Or get status from stage table
    }

    $this->db->where('order_id', $order_id);
    $this->db->update('spares_orders', $order_update_data);
    
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Database error. Could not save progress.']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Order progress updated successfully!']);
    }
}

public function kpi_report()
{
    $this->load->model('opportunity_model');

    // 1. Capture Date Filters (Default to current month if empty)
    $from_date = $this->input->get('from_date') ? date('Y-m-d', strtotime($this->input->get('from_date'))) : date('Y-m-01');
    $to_date = $this->input->get('to_date') ? date('Y-m-d', strtotime($this->input->get('to_date'))) : date('Y-m-t');

    $filters = [
        'from_date' => $from_date,
        'to_date'   => $to_date
    ];

    // 2. Fetch Aggregated KPI Data from Model
    $kpi_data = $this->opportunity_model->get_kpi_report_data($filters);

    // 3. Fetch Running Orders separately for the "Active Load" metric
    // We reuse your existing model function for this
    $kpi_data['running_orders'] = $this->opportunity_model->get_running_orders();

    // 4. Pass the filter values back to the view for the input fields
    $kpi_data['from_date'] = $from_date;
    $kpi_data['to_date'] = $to_date;

    // 5. Load the View
    $this->load->view('spares/spare_kpi_report_view', $kpi_data);
}

public function get_customer_by_company_new_for_app()
{
    // 🔥 Read RAW JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    $searchTerm = isset($input['searchTerm']) ? trim($input['searchTerm']) : '';

    $this->db->select('customer_id, company_name, contact_person');
    $this->db->from('spares_customers');
    $this->db->where('status', '1');

    if (!empty($searchTerm)) {
        $this->db->group_start();
        $this->db->like('company_name', $searchTerm);
        $this->db->or_like('contact_person', $searchTerm);
        $this->db->group_end();
    }

    $this->db->limit(50);
    $query = $this->db->get();

    $json = [];

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $customer) {

            $displayText = $customer->company_name;

            if (!empty($customer->contact_person)) {
                $displayText .= " (" . $customer->contact_person . ")";
            }

            $json[] = [
                'id' => $customer->customer_id,
                'text' => $displayText
            ];
        }
    }

    // 🔥 FINAL RESPONSE
    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            "status" => true,
            "data" => $json
        ]));
}



// public function bulkApproveSpareOpportunities(
//      $opportunity_ids=[5, 7, 9, 10, 11, 12, 13, 15, 16, 17, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 156, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239, 240, 241, 243, 244, 245, 246, 247, 248, 249, 250, 251, 252, 254, 255, 256, 257, 258, 259, 260, 261, 262, 263, 264, 265, 266, 267, 268, 269, 270, 271, 272, 273, 274, 275, 276, 277, 278, 279, 280, 281, 282, 283, 284, 285, 286, 287, 288, 289, 290, 291, 292, 293, 294, 295, 296, 297, 298, 299, 300, 301, 302, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 335, 336, 338, 339, 340, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 365, 366, 367, 368, 369, 370, 371, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 404, 406, 407, 408, 409, 410, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 466],
//     //$opportunity_ids=[4],
//     $remarks="Bulk Approved",
//     $followup_date='2026-06-03'
// ){

//     if(
//     empty(
//     $opportunity_ids
//     )
//     ){

//         return false;

//     }

//     $followup_date=

//         !empty(
//         $followup_date
//         )

//         ?

//         date(

//             'Y-m-d',

//             strtotime(
//                 $followup_date
//             )

//         )

//         :

//         NULL;

//     $user_id=

//     $this->session
//         ->userdata(
//             'logged_in'
//         )['user_id'];

//     /*
//     approved stage
//     */

//     $stage=

//     $this->db

//         ->where(
//             'lead_id',
//             5
//         )

//         ->get(
//             'spare_lead_stage'
//         )

//         ->row();

//     if(
//     !$stage
//     ){

//         return false;

//     }

//     $this->db
//         ->trans_start();

//     foreach(
//     $opportunity_ids
//     as $opportunity_id
//     ){

//         /*
//         remarks history
//         */

//         $this->db->insert(

//             'spare_progress_remarks',

//             [

//                 'lead_id'=>

//                 $opportunity_id,

//                 'lead_stage'=>

//                 5,

//                 'next_follow_date'=>

//                 $followup_date,

//                 'remarks'=>

//                 $remarks,

//                 'remark_title'=>

//                 $stage->lead_name,

//                 'added_on'=>

//                 date(
//                     'Y-m-d H:i:s'
//                 ),

//                 'added_by'=>

//                 $user_id

//             ]

//         );

//         /*
//         update opportunity
//         */

//         $this->db

//             ->where(

//                 'opportunity_id',

//                 $opportunity_id

//             )

//             ->update(

//                 'opportunities',

//                 [

//                     'approval_status'=>

//                     'Approved'


//                 ]

//             );

//     }

//     $this->db
//         ->trans_complete();

//     return

//     $this->db
//         ->trans_status();

// }
}
