<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {


 public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: POST');

        $this->load->model('Task_model');
        $this->load->model('Salescrm_model','salescrm');
        $this->load->model('Service_model');
        $this->load->model('Dashboard_model');
        $this->load->model('Opportunity_model','opportunity_model');

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


    }


function index()
{
	echo "hi"; exit;
}

public function auth_login()
{
         header('Content-Type: application/json');

    // ✅ READ RAW JSON INPUT
    $rawInput = json_decode(file_get_contents("php://input"), true);

    $email    = isset($rawInput['email']) ? trim($rawInput['email']) : '';
    $password = isset($rawInput['password']) ? trim($rawInput['password']) : '';

    if (empty($email) || empty($password)) {
        echo json_encode([
            'status' => false,
            'message' => 'Email and password are required'
        ]);
        return;
    }
    

        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     $this->_response(false, 'Invalid email format');
        //     return;
        // }

        /* ===============================
         * 3. FETCH USER
         * =============================== */
        $this->db->select('user_id, first_name, last_name, email, password, user_role_id, department_id, user_status,contact_number,admin_dashboard');
        $this->db->from('system_users');
        $this->db->where('contact_number', $email);
        $user = $this->db->get()->row_array();

        if (!$user) {
            $this->_response(false, 'Invalid email or password');
            return;
        }

        /* ===============================
         * 4. CHECK USER STATUS
         * =============================== */
        if ((int)$user['user_status'] !== 1) {
            $this->_response(false, 'Your account is inactive. Contact administrator.');
            return;
        }

        /* ===============================
         * 5. PASSWORD MATCH (PLAIN TEXT)
         * =============================== */
        if ($user['password'] !== $password) {
            $this->_response(false, 'Invalid email or password');
            return;
        }

        /* ===============================
         * 6. TOKEN GENERATION
         * =============================== */
        $tokenPayload = [
            'uid'  => $user['user_id'],
            'time' => time(),
            'rand' => random_int(1000, 9999)
        ];

        $token = base64_encode(json_encode($tokenPayload));

        /* ===============================
         * 7. SUCCESS RESPONSE
         * =============================== */
        echo json_encode([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'user_id'       => (int)$user['user_id'],
                'name'          => $user['first_name'] . ' ' . $user['last_name'],
                'email'         => $user['email'],
                'role_id'       => (int)$user['user_role_id'],
                'department_id' => (int)$user['department_id'],
                'contact_number'=>$user['contact_number'],
                'admin_dashboard'=>(int)$user['admin_dashboard']
            ]
        ]);
    }

    /* ===============================
     * COMMON RESPONSE HELPER
     * =============================== */
    private function _response($status, $message)
    {
        echo json_encode([
            'status'  => $status,
            'message' => $message
        ]);
    }

//         public function dashboard_counts()
//         {
//         header('Content-Type: application/json');

//         // OPTIONAL: token validation later
//         // $token = $this->input->get_request_header('Authorization');

//         // TEMP: static data (replace with DB logic)
//         $q = $this->db->select('id')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
//         $running_df= $q->num_rows();

//         $q = $this->db->select('id')->from('poreceived')->where('penalityamount!=', 0)->get();
//         $penalty_df=$q->num_rows();
//         $data = [
//         'running_df' => $running_df,
//         'delayed_df' => $this->df_delayed(),
//         'penalty_df' => $penalty_df,
//         ];

//         echo json_encode([
//         'status' => true,
//         'data' => $data
//         ]);
//         }


// function df_delayed()
// {
// $show = array();
// $show[] = 0;
// $m = 1;
// $reportid = $this->uri->segment(3);
// $q = $this->db->select('id, df_no, added_on, df_upload, added_on')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
// if ($q->num_rows() > 0) {
// foreach ($q->result() as $rows) {

// $q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
// if ($q1->num_rows() > 0) {
// foreach ($q1->result() as $r);
// $planneddate = date('d-m-Y', strtotime($r->enddate));
// } else {
// $planneddate = '';
// }


// $q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
// $count = $q1->num_rows();
// $delaycount = array();
// $delaycount[] = 0;
// $totaldayscountarray = array();
// $totaldayscountarray[] = 0;
// $q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->where('task_status', 1)->get();
// $totaldone = $q2->num_rows();
// // $percetage =  round($totaldone * 100 / $count);

// if ($count > 0) {
// $percentage = round($totaldone * 100 / $count);
// } else {
// $percentage = 0; 
// }


// foreach ($q2->result() as $rowss) {
// if (date('Y-m-d', strtotime($rowss->task_completed_on)) > $rowss->end_date) {
// $delaycount[] = 1;

// $daysss = $this->Task_model->getDays($rowss->end_date, date('Y-m-d', strtotime($rowss->task_completed_on)), 1);

// $totaldayscountarray[] = $daysss;
// }
// }
// $delayed = array_sum($delaycount);
// $totaldaysdelayed = array_sum($totaldayscountarray);

// if ($totaldone > 0) {
// $totaldelayedpercentage = round($delayed * 100 / $totaldone);
// } else {
// $totaldelayedpercentage = 0; 
// }

// // $totaldelayedpercentage = round($delayed * 100 / $totaldone);

// $expecteddate =  date('d-m-Y', strtotime($planneddate . ' +' . $totaldaysdelayed . ' Days'));
// $skipped_dates = $this->Task_model->SKIPsingle_holidays($expecteddate);
// if ($totaldelayedpercentage > 0) {
// $show[] = 1;
// } else {
// $show[] = 0;
// }
// }
// }

// return array_sum($show);
// }



// public function get_running_df_list($from_date = null, $to_date = null)
//     {
//         $thisA =& get_instance();
//         $thisA->load->model('Task_model');

//         $this->db->select("
//             df.id,
//             df.df_no,
//             df.added_on as df_release_date,
//             df.df_upload,
//             df.machine_id,

//             po.podate,
//             po.lead_id,
//             po.df_number as po_df_number,

//             CONCAT(u.title,' ',u.first_name,' ',u.last_name) as marketing_person,

//             MIN(tasks.start_date) as df_start_date,
//             MAX(tasks.end_date) as planned_closure,

//             COUNT(tasks.id) as total_tasks,
//             SUM(CASE WHEN tasks.task_status = 1 THEN 1 ELSE 0 END) as completed_tasks,

//             (
//                 SELECT COUNT(id)
//                 FROM communication_ticket_system
//                 WHERE df_id = df.id AND ticket_status = 0
//             ) as open_tickets,

//             GROUP_CONCAT(
//                 CASE
//                     WHEN tasks.task_status = 1
//                     THEN CONCAT(tasks.task_completed_on,'|',tasks.end_date)
//                 END
//             SEPARATOR ';') as completed_tasks_data,

//             MAX(CASE WHEN tasks.task_status = 1 THEN tasks.task_completed_on END) as max_completion_date
//         ", false);

//         $this->db->from('df_release df');
//         $this->db->join('poreceived po', 'po.df_id = df.id', 'left');
//         $this->db->join('system_users u', 'po.added_by = u.user_id', 'left');
//         $this->db->join('task_department_wise_scheduling tasks', 'tasks.df_id = df.id', 'left');

//         $this->db->where('df.df_status', 0);
//         $this->db->where('df.on_hold', 0);

//         if ($from_date && $to_date) {
//             $this->db->where('DATE(df.added_on) >=', $from_date);
//             $this->db->where('DATE(df.added_on) <=', $to_date);
//         }

//         $this->db->group_by('df.id');

//         $query = $this->db->get();
//         $result = [];

//         foreach ($query->result() as $row) {

//             $display_df_no = $row->df_no ?: $row->po_df_number;

//             $completion_percentage = ($row->total_tasks > 0)
//                 ? round($row->completed_tasks * 100 / $row->total_tasks)
//                 : 0;

//             $delaycount = 0;
//             $max_delay_days = 0;

//             if (!empty($row->completed_tasks_data)) {
//                 foreach (explode(';', $row->completed_tasks_data) as $task) {
//                     list($completed_on, $end_date) = explode('|', $task);
//                     if ($completed_on > $end_date) {
//                         $delaycount++;
//                         $delay_days = $thisA->Task_model->getDays($end_date, $completed_on, 1);
//                         $max_delay_days = max($max_delay_days, $delay_days);
//                     }
//                 }
//             }

//             $delay_percentage = ($row->completed_tasks > 0)
//                 ? round($delaycount * 100 / $row->completed_tasks)
//                 : 0;

//             $actual_completion = ($completion_percentage == 100 && $row->max_completion_date)
//                 ? $row->max_completion_date
//                 : $thisA->Task_model->SKIPsingle_holidays(
//                     date('Y-m-d', strtotime($row->planned_closure . " +{$max_delay_days} days"))
//                 );

//             $df_delay_days = $thisA->Task_model->getDays(
//                 $row->planned_closure,
//                 $actual_completion,
//                 1
//             );

//             $result[] = [
//     "df_id" => $row->id,
//     "df_no" => strtoupper($display_df_no),
//     "marketing_person" => strtoupper($row->marketing_person),

//     "po_date" => $this->fmt($row->podate),
//     "df_release_date" => $this->fmt($row->df_release_date),
//     "df_start_date" => $this->fmt($row->df_start_date),
//     "planned_closure" => $this->fmt($row->planned_closure),
//     "actual_completion" => $this->fmt($actual_completion),

//     "df_delay_days" => (int)$df_delay_days,
//     "completion_percentage" => (int)$completion_percentage,
//     "delay_percentage" => (int)$delay_percentage,
//     "open_tickets" => (int)$row->open_tickets,
// ];

//         }

//         return $result;
//     }

//     public function running_df_list()
// {
//     header('Content-Type: application/json');

//     $from_date = $this->input->get('from_date');
//     $to_date   = $this->input->get('to_date');

//     $data = $this->get_running_df_list($from_date, $to_date);

//     echo json_encode([
//         "status" => true,
//         "data" => $data
//     ]);
// }


// function fmt($date) {
//     return ($date && $date != '0000-00-00')
//         ? date('d-M-Y', strtotime($date))
//         : '--';
// }


 function get_customer_by_company(){

header('Content-Type: application/json');

    // 🔥 STEP 1: GET RAW INPUT
    $inputJSON = trim(file_get_contents("php://input")); 

    // 🔥 STEP 2: HANDLE EMPTY INPUT
    if (empty($inputJSON)) {
        echo json_encode([
            "status" => false,
            "msg" => "Empty input received"
        ]);
        exit;
    }

    // 🔥 STEP 3: DECODE JSON
    $rawInput = json_decode($inputJSON, true);


    $searchtrm = $rawInput['searchTerm'] ?? '';

    $this->db->select('a.customer_alias,a.id,a.company_name,a.customer_name')
             ->from('customer_detail a')
             ->where('a.status', '1');

    if (!empty($searchtrm)) {
        $this->db->like('a.company_name', $searchtrm, 'both', false);
    }

    $query = $this->db->get();

    $data = [];

    if ($query->num_rows() > 0) {

        foreach ($query->result() as $customer) {

            $alias = !empty($customer->customer_alias) 
                ? "-" . $customer->customer_alias 
                : "";

            $data[] = [
                'id'   => (string)$customer->id,
                'text' => $customer->company_name . $alias . " " . $customer->customer_name
            ];
        }

        echo json_encode([
            "status" => true,
            "data"   => $data
        ]);

    } else {

        echo json_encode([
            "status" => true,
            "data"   => []
        ]);
    }
}


    public function master_data()
{
    header('Content-Type: application/json');

    // 🔥 STEP 1: GET RAW INPUT
    $inputJSON = trim(file_get_contents("php://input"));

    file_put_contents('debug_master.txt', $inputJSON);

    // 🔥 STEP 2: HANDLE EMPTY INPUT
    if (empty($inputJSON)) {
        echo json_encode([
            "status" => false,
            "msg" => "Empty input received"
        ]);
        exit;
    }

    // 🔥 STEP 3: DECODE JSON
    $rawInput = json_decode($inputJSON, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            "status" => false,
            "msg" => "Invalid JSON",
            "error" => json_last_error_msg(),
            "raw" => $inputJSON
        ]);
        exit;
    }

    // 🔥 STEP 4: FETCH VALUES
    $user_id = isset($rawInput['user_id']) ? trim($rawInput['user_id']) : '';
    $role_id = isset($rawInput['role_id']) ? trim($rawInput['role_id']) : '';
    $department_id = isset($rawInput['department_id']) ? trim($rawInput['department_id']) : '';

    // 🔥 FALLBACK
    if (empty($user_id)) {
        $user_id = $this->input->post('user_id');
        $role_id = $this->input->post('role_id');
        $department_id = $this->input->post('department_id');
    }

    // 🔥 VALIDATION
    if (empty($user_id)) {
        echo json_encode([
            "status" => false,
            "msg" => "User ID missing"
        ]);
        exit;
    }

    // 🔥 VERIFY USER
    $user = $this->db->where('user_id', $user_id)->get('system_users')->row();

    if (!$user) {
        echo json_encode([
            "status" => false,
            "msg" => "Invalid user"
        ]);
        exit;
    }

    $data = [];

    // 🔹 SOURCES
    $data['sources'] = $this->db
        ->select('source_id as id, CONCAT(UCASE(LEFT(lead_source,1)), LCASE(SUBSTRING(lead_source,2))) as name')
        ->from('lead_source')
        ->where('status', 1)
        ->get()->result();

    // 🔹 EXHIBITIONS
    $data['exhibitions'] = $this->db
        ->select('id, CONCAT(UCASE(LEFT(exhibition,1)), LCASE(SUBSTRING(exhibition,2))) as name')
        ->from('exhibition_info')
        ->order_by('exhibition', 'asc')
        ->get()->result();

    // 🔹 MARKETING USERS
    $this->db->select('user_id as id,
        CONCAT(
            CONCAT(UCASE(LEFT(first_name,1)), LCASE(SUBSTRING(first_name,2))),
            " ",
            CONCAT(UCASE(LEFT(last_name,1)), LCASE(SUBSTRING(last_name,2)))
        ) as name
    ');
    $this->db->from('system_users');
    $this->db->where('marketing_person', 1);
    $this->db->where('business_location', 2);
    $this->db->where('user_status', 1);

    if ($role_id != 12) {
        $this->db->where('user_id', $user_id);
    }

    $data['marketing_users'] = $this->db->get()->result();

    // 🔹 PRODUCTS
    $data['products'] = $this->db
        ->select('id,
            CONCAT(
                CONCAT(UCASE(LEFT(instruments_name,1)), LCASE(SUBSTRING(instruments_name,2))),
                " - ",
                model_number
            ) as name
        ')
        ->from('presto_instruments')
        ->where('status', 1)
        ->get()->result();

    // 🔹 COUNTRIES
    $data['countries'] = $this->db
        ->select('country_id as id,
            CONCAT(UCASE(LEFT(country_name,1)), LCASE(SUBSTRING(country_name,2))) as name
        ')
        ->from('countries')
        ->where('country_status', 1)
        ->get()->result();

    // 🔥 FINAL RESPONSE
    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
}


   public function search_customer()
{
    header('Content-Type: application/json');

    // 🔥 GET PARAM (support both)
    $term = $this->input->get('term');
    if (!$term) {
        $term = $this->input->get('searchTerm');
    }

    $term = trim($term);

    if (empty($term)) {
        echo json_encode([]);
        exit;
    }

    // 🔥 QUERY
    $this->db->select('a.id, a.company_name, a.customer_name, a.customer_alias');
    $this->db->from('customer_detail a');
    $this->db->where('a.status', '1');

    $this->db->group_start();
    $this->db->like('a.company_name', $term);
    $this->db->or_like('a.customer_name', $term);
    $this->db->group_end();

    $this->db->order_by('a.company_name', 'asc');
    $this->db->limit(20); // 🔥 performance

    $query = $this->db->get();

    $json = [];

    if ($query->num_rows() > 0) {

        foreach ($query->result() as $customer) {

            // 🔥 Alias handling
            $alias = '';
            if (!empty($customer->customer_alias)) {
                $alias = " - " . $customer->customer_alias;
            }

            // 🔥 Final text
            $text = $customer->company_name . $alias . " " . $customer->customer_name;

            $json[] = [
                'id' => $customer->id,
                'text' => ucwords(strtolower($text))
            ];
        }

    } else {

        // 🔥 IMPORTANT: return empty array (not dummy row)
        $json = [];
    }

    echo json_encode($json);
}



public function customer_full_details()
{
    header('Content-Type: application/json');

    // 🔥 READ JSON INPUT
    $inputJSON = file_get_contents("php://input");
    $input = json_decode($inputJSON, true);

    $customer_id = isset($input['customer_id']) ? $input['customer_id'] : '';

    if (empty($customer_id)) {
        echo json_encode([
            "status" => false,
            "msg" => "Customer ID missing"
        ]);
        exit;
    }

    $row = $this->db->select('
            company_brand,
            customer_alias,
            payment_type,
            customer_ref_no,
            bill_address,
            bill_state,
            bill_city,
            bill_pincode,
            bill_email,
            msme_number,
            gst,
            pan,
            ship_address,
            ship_state,
            ship_city,
            ship_pincode,
            ship_email,
            title,
            company_name,
            state,
            contact_no,
            credit_days,
            email
        ')
        ->from('customer_detail')
        ->where('id', $customer_id)
        ->get()
        ->row();

    if (!$row) {
        echo json_encode([
            "status" => false,
            "msg" => "Customer not found"
        ]);
        exit;
    }

    // 🔥 GET BRAND NAME
    $brand_name = "";
    if (!empty($row->company_brand)) {
        $brand_name = $this->salescrm->getBrandName($row->company_brand);
    }

    // 🔥 FINAL RESPONSE
    echo json_encode([
        "status" => true,
        "data" => [

            "company_name" => $row->company_name ?? "",
            "customer_alias" => $row->customer_alias ?? "",
            "customer_ref_no" => $row->customer_ref_no ?? "",

            "contact_no" => $row->contact_no ?? "",
            "email" => $row->email ?? "",

            "brand_id" => $row->company_brand ?? "",
            "brand_name" => $brand_name ?? "",

            "payment_type" => $row->payment_type ?? "",
            "credit_days" => $row->credit_days ?? "",

            "gst" => $row->gst ?? "",
            "pan" => $row->pan ?? "",
            "msme_number" => $row->msme_number ?? "",

            /// 🔹 BILLING
            "billing" => [
                "address" => $row->bill_address ?? "",
                "state" => $row->bill_state ?? "",
                "city" => $row->bill_city ?? "",
                "pincode" => $row->bill_pincode ?? "",
                "email" => $row->bill_email ?? "",
            ],

            /// 🔹 SHIPPING
            "shipping" => [
                "address" => $row->ship_address ?? "",
                "state" => $row->ship_state ?? "",
                "city" => $row->ship_city ?? "",
                "pincode" => $row->ship_pincode ?? "",
                "email" => $row->ship_email ?? "",
            ],
        ]
    ]);
}



    public function tax()
    {
        $country_id = $this->input->post('country_id');

        // Example logic
        if($country_id == 1){
            $label = "GST Number";
        } else {
            $label = "VAT Number";
        }

        echo json_encode([
            "status" => true,
            "label" => $label,
            "required" => true
        ]);
    }


    public function search_brand()
{
    header('Content-Type: application/json');

    $term = $this->input->get('term');
    $term = trim($term);

    $this->db->select('id, name');
    $this->db->from('company_brand');

    // 🔥 OPTIONAL SEARCH FILTER
    if (!empty($term)) {
        $this->db->like('name', $term);
    }

    $this->db->order_by('name', 'asc');
    $this->db->limit(20);

    $query = $this->db->get();

    $data = [];

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {

            $data[] = [
                "id" => $row->id,
                "text" => ucwords(strtolower($row->name)) // 👈 for dropdown
            ];
        }
    }

    echo json_encode($data);
}


public function add_customer()
{
    header('Content-Type: application/json');

    // 🔥 READ JSON INPUT
    $inputJSON = file_get_contents("php://input");
    $input = json_decode($inputJSON, true);

    $company = trim($input['company'] ?? '');
    $brand_input = $input['brand'] ?? '';
    $email = trim($input['email'] ?? '');
    $address = trim($input['address'] ?? '');
    $contactpersonname = trim($input['contactpersonname'] ?? '');
    $personcontactno = trim($input['personcontactno'] ?? '');
    $acontactno = trim($input['acontactno'] ?? '');

    if (empty($company) || empty($personcontactno)) {
        echo json_encode([
            "status" => false,
            "msg" => "Required fields missing"
        ]);
        exit;
    }

    /// 🔥 BRAND LOGIC (EXISTING OR NEW)
    if (!is_numeric($brand_input)) {

        $resty = $this->db->select('id')
            ->from('company_brand')
            ->where('LOWER(name)', strtolower($brand_input))
            ->get();

        if ($resty->num_rows() == 0) {
            $this->db->insert('company_brand', [
                'name' => $brand_input
            ]);
            $brand_id = $this->db->insert_id();
        } else {
            $brand_id = $resty->row()->id;
        }

    } else {
        $brand_id = $brand_input;
    }

    /// 🔥 INSERT CUSTOMER
    $data = [
        'company_brand' => $brand_id,
        'company_name' => $company,
        'email' => $email,
        'bill_email' => $email,
        'address' => $address,
        'bill_address' => $address,
        'status' => 1,
        'customer_name' => $contactpersonname,
        'contact_no' => $personcontactno,
        'alt_contact' => $acontactno
    ];

    $this->db->insert('customer_detail', $data);
    $customer_id = $this->db->insert_id();

    echo json_encode([
        "status" => true,
        "msg" => "Customer added successfully",
        "data" => [
            "customer_id" => $customer_id,
            "company_name" => $company
        ]
    ]);
}


public function add_opportunity_api()
{
    header('Content-Type: application/json');

    $inputJSON = file_get_contents("php://input");
    $input = json_decode($inputJSON, true);

    /// 🔥 VALIDATION
    $required_fields = [
        'op_date', 'lsource', 'op_type', 'mach_type',
        'marketing', 'customer', 'brand',
        'address', 'product', 'qty',
        'customertype', 'probability'
    ];

    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode([
                "status" => false,
                "msg" => "$field is required"
            ]);
            return;
        }
    }

    /// 🔥 GENERATE OPPORTUNITY NUMBER
    $op_number = $this->runtimegenerateOppNo(
        $input['op_type'],
        $input['mach_type']
    );

    /// 🔥 INSERT LEAD
    $data = [
        'lead_source_id' => $input['lsource'],
        'patient_type_id' => $input['op_type'],
        'create_date' => date('Y-m-d', strtotime($input['op_date'])),
        'company_name' => $input['customer'],
        'machine_type' => $input['mach_type'],
        'unique_id' => $op_number[0],
        'unique_no' => $op_number[1],
        'postal_address' => $input['address'],
        'gst' => $input['gst'] ?? '',
        'country' => $input['country'] ?? '',
        'customer_gstn' => $input['gst'] ?? '',
        'brand' => $input['brand'],
        'added_on' => date('Y-m-d'),
        'vatno' => $input['vatno'] ?? '',
        'customise_remarks' => $input['remarks'] ?? '',
        'merchantexport' => $input['merchantexport'] ?? 0,
        'customer_type' => $input['customertype'],
        'probability' => $input['probability'],
        'added_by' => $input['marketing'],
        'exhibition' => ($input['lsource'] == 8) ? ($input['exhibition_id'] ?? 0) : 0
    ];

    $this->db->insert('leads', $data);
    $lead_id = $this->db->insert_id();

    /// 🔥 PRODUCT LOGIC
    if (is_numeric($input['product']) && strpos($input['product'], '.') === false) {
        $product_id = $input['product'];
    } else {
        $this->db->insert('presto_instruments', [
            'instruments_name' => $input['product'],
            'type' => 0,
            'status' => 1
        ]);
        $product_id = $this->db->insert_id();
    }

    /// 🔥 INSERT PRODUCT
    $this->db->insert('lead_products', [
        'lead_id' => $lead_id,
        'product_id' => $product_id,
        'qty' => $input['qty'],
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $input['marketing']
    ]);

    /// 🔥 INITIAL STATUS
    $initial_step = $this->getintialstep();

    $this->db->insert('progress_remarks', [
        'lead_id' => $lead_id,
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $input['marketing'],
        'lead_status' => $initial_step,
        'next_follow_date' => date('Y-m-d', strtotime("+1 day"))
    ]);

    /// 🔥 SUCCESS RESPONSE
    echo json_encode([
        "status" => true,
        "msg" => "Opportunity added successfully",
        "data" => [
            "lead_id" => $lead_id,
            "opportunity_no" => $op_number[1]
        ]
    ]);
}

function runtimegenerateOppNo($op_type,$mach_type)
{
    $opnumber=$this->salescrm->runtimegetOppNo($op_type,$mach_type);
    $dd = explode('~',$opnumber);
    //echo "<pre>"; print_r($dd); exit;
    return $dd;
}


function getintialstep()
    {
        $initiallead=0;
        $row=$this->db->select('lead_id')->from('lead_stage')->order_by('sort_order','ASC')->limit(1)->get();
        if($row->num_rows()>0)
        {
            foreach($row->result() as $rowss);

            $initiallead=$rowss->lead_id;

        }

        return $initiallead;
    }



public function lead_stage_list_api()
{
    header('Content-Type: application/json');

    $post = json_decode(file_get_contents("php://input"), true);

    $user_id       = $post['user_id'] ?? '';
    $role_id       = $post['role_id'] ?? '';
    $lead_stage    = $post['lead_stage'] ?? '';
    $source_id     = $post['source_id'] ?? '';
    $exhibition_id = $post['exhibition_id'] ?? '';
    $selected_user = $post['selected_user'] ?? '';

    // 🔹 Role filter
    if($role_id != 12 && $role_id != 41){
        $chk = " AND b.added_by = ".$this->db->escape_str($user_id);
    } else {
        if($selected_user != ''){
            $chk = " AND b.added_by = ".$this->db->escape_str($selected_user);
        } else {
            $chk = "";
        }
    }

    // 🔹 Source filter
    $sck = ($source_id != '') ? " AND b.lead_source_id=".$this->db->escape_str($source_id) : '';

    // 🔹 Exhibition filter
    $exhibi = ($exhibition_id != '') ? " AND b.exhibition=".$this->db->escape_str($exhibition_id) : '';

    // 🔹 Main Query
    $query = $this->db->query("
        SELECT 
            b.id as lead_id,
            b.unique_id,
            b.customer_name,
            b.contact_no,
            b.create_date,
            b.machine_type,
            b.customise_remarks,
            b.lead_source_id,
            b.patient_type_id,
            b.customer_gstn,
            b.vatno,
            b.company_name,
            a.remarks,
            a.added_on,
            a.lead_status,
            b.added_by
        FROM progress_remarks a
        JOIN leads b ON a.lead_id = b.id
        WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id)
        AND a.lead_status = '$lead_stage'
        AND b.closed = 0
        $chk $sck $exhibi
        ORDER BY b.id DESC
    ");

    $data = [];

    foreach($query->result() as $row){

        // 🔹 Client Type
        $clienttype = $this->salescrm->getClientTypebyid($row->patient_type_id);

        // 🔹 GST / VAT
        $gstno = ($clienttype == 'Domestic') ? $row->customer_gstn : $row->vatno;

        // 🔹 Source
        $leadsource = $this->salescrm->getleadsourcebyid($row->lead_source_id);

        // 🔹 Machine Type
        if($row->machine_type == 1){
            $type = "Liquid";
        } elseif($row->machine_type == 2){
            $type = "Powder";
        } else {
            $type = "Customise";
        }

        // 🔹 Product
        $products = $this->getProductDetails($row->lead_id);
        $prd_name = $products[0] ?? '';
        $prd_qty  = $products[1] ?? '';

        // 🔹 Manager
        $username = $this->salescrm->getusername($row->added_by);

        // 🔹 Quotation check
        $hasQuotation = $this->salescrm
            ->checkforquotationoraheadsteptogetquotationvisible($row->lead_id);

        // 🔹 Welcome email
        $welcomeSent = ($row->welcome_email_status == 1);

        $data[] = [
            "lead_id"        => $row->lead_id,
            "opp_no"         => $row->unique_id,
            "date"           => date('d-m-Y', strtotime($row->create_date)),
            "customer_name"  => $row->customer_name,
            "contact_no"     => $row->contact_no,
            "stage"          => $row->lead_status,
            "source"         => $leadsource,
            "type"           => $type,
            "product"        => $prd_name,
            "qty"            => $prd_qty,
            "manager"        => $username,
            "last_update"    => date('d-m-Y H:i A', strtotime($row->added_on)),
            "remarks"        => $row->remarks,
            "gst_vat"        => $gstno,
            "is_quotation"   => $hasQuotation,
            "is_welcome_sent"=> $welcomeSent
        ];
    }

    echo json_encode([
        "status" => true,
        "count"  => count($data),
        "data"   => $data
    ]);
}


public function lead_stages_api()
{
    header('Content-Type: application/json');

    $post = json_decode(file_get_contents("php://input"), true);

    $role_id = $post['role_id'] ?? '';
    $user_id = $post['user_id'] ?? '';

    // 🔹 Fetch stages
    $this->db->select('lead_id, lead_name, icon, user_role, step_type, customization_related');
    $this->db->from('lead_stage');
    $this->db->order_by('app_order');

    $query = $this->db->get();

    $data = [];

    foreach($query->result() as $row){

        // 🔥 Get count per stage
        $count = $this->get_lead_stage_count(
            $row->lead_id,
            $user_id,
            $role_id
        );

        $data[] = [
            "stage_id"              => (int)$row->lead_id,
            "stage_name"            => $row->lead_name,
            "icon"                  => $row->icon,
            "step_type"             => $row->step_type,
            "customization_related" => (int)$row->customization_related,
            "count"                 => $count   // 🔥 IMPORTANT
        ];
    }

    echo json_encode([
        "status" => true,
        "count"  => count($data),
        "data"   => $data
    ]);
}

public function get_lead_stage_count($lead_stage_id, $user_id = null, $role_id = null)
{
    // 🔹 Role filter
    $chk = "";

    if($role_id != 12 && $role_id != 41){
        if(!empty($user_id)){
            $chk = " AND b.added_by = ".$this->db->escape_str($user_id);
        }
    }

    // 🔹 Query
    $sql = $this->db->query("
        SELECT b.id 
        FROM progress_remarks a 
        JOIN leads b ON a.lead_id = b.id 
        WHERE a.id IN (
            SELECT MAX(id) FROM progress_remarks GROUP BY lead_id
        )
        AND a.lead_status = '".$this->db->escape_str($lead_stage_id)."'
        AND b.closed = 0
        $chk
        GROUP BY b.id
    ");

    return $sql->num_rows();
}


public function followup_counts_api()
{
    header('Content-Type: application/json');

    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = $post['user_id'] ?? '';
    $role_id = $post['role_id'] ?? '';

    // 🔹 Role filter
    if($role_id != 12 && $role_id != 41){
        $chk = " AND b.added_by = ".$this->db->escape_str($user_id);
    } else {
        $chk = "";
    }

    // 🔹 Dead stages
    $conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
    $getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
    array_push($getDeadEndLeadStage, $conversion_lead_stage);

    $dead_end_lead_stage = "'" . implode("','", $getDeadEndLeadStage) . "'";

    // 🔹 Main Query (single fetch)
    $query = $this->db->query("
        SELECT a.next_follow_date
        FROM progress_remarks a
        JOIN leads b ON a.lead_id = b.id
        WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id)
        AND a.lead_status NOT IN ($dead_end_lead_stage)
        AND b.closed = 0
        $chk
    ");

    $today_count = 0;
    $missed_count = 0;
    $upcoming_count = 0;

    $today = date('Y-m-d');

    foreach($query->result() as $row){

        $date = $row->next_follow_date;

        if($date == '0000-00-00' || $date == '1970-01-01' || empty($date)){
            continue;
        }

        if($date == $today){
            $today_count++;
        } 
        else if($date < $today){
            $missed_count++;
        } 
        else if($date > $today){
            $upcoming_count++;
        }
    }

    echo json_encode([
        "status" => true,
        "data" => [
            "today"    => $today_count,
            "missed"   => $missed_count,
            "upcoming" => $upcoming_count
        ]
    ]);
}


public function followup_list_api()
{
    header('Content-Type: application/json');

    $post = json_decode(file_get_contents("php://input"), true);

    $type = $post['type']; // 1=Today, 2=Missed, 3=Upcoming
    $user_id = $post['user_id'];
    $role_id = $post['role_id'];

    /// 🔐 ROLE FILTER
    if ($role_id != 12 && $role_id != 41) {
        $chk = " AND b.added_by = $user_id";
    } else {
        $chk = "";
    }

    /// 🔥 DEAD STAGES
    $conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
    $getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
    array_push($getDeadEndLeadStage, $conversion_lead_stage);
    $dead_end_lead_stage = "'" . implode("','", $getDeadEndLeadStage) . "'";

    /// 🔥 DATE FILTER
    if ($type == 1) {
        $dr = "AND a.next_follow_date = '" . date('Y-m-d') . "'";
    } elseif ($type == 2) {
        $dr = "AND a.next_follow_date < '" . date('Y-m-d') . "'";
    } else {
        $dr = "AND a.next_follow_date > '" . date('Y-m-d') . "'";
    }

    /// 🔥 MAIN QUERY
    $query = $this->db->query("
        SELECT 
            b.id as leadid,
            b.unique_id,
            b.create_date,
            c.customer_name,
            c.contact_no,
            b.postal_address,
            b.machine_type,
            a.next_follow_date,
            a.remarks,
            c.company_name,
            a.lead_status,
            su.first_name,su.last_name
        FROM progress_remarks a
        JOIN leads b ON a.lead_id = b.id
        LEFT JOIN system_users su ON b.added_by = su.user_id
        LEFT JOIN customer_detail c ON c.id = b.company_name
        WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id)
        AND a.lead_status NOT IN ($dead_end_lead_stage)
        $chk $dr
        GROUP BY b.id
        ORDER BY b.id DESC
    ");

    $data = [];

    foreach ($query->result() as $row) {

        $stage = $this->db->select('lead_name')
            ->from('lead_stage')
            ->where('lead_id', $row->lead_status)
            ->get()
            ->row();


        $machine=$this->getProductDetails($row->leadid);


        $data[] = [
            "lead_id" => $row->leadid,
            "company" => $row->company_name,
            "contact" => $row->customer_name,
            "phone" => $row->contact_no,
            "opp" => $row->unique_id,
            "date" => date('d-m-Y', strtotime($row->next_follow_date)),
            "machine" => ($row->machine_type == 1) ? "Liquid" : "Powder",
            "product" => $machine[0],
            "address" => $row->postal_address,
            "remarks" => $row->remarks,
            "stage" => $stage ? $stage->lead_name : "",
            'assigned'=>ucwords(strtolower($row->first_name." ".$row->last_name))
        ];
    }

    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
}


function getProductDetails($lead_id)
{
    $data=array();
    $sql = $this->db->select('a.id, a.qty, c.instruments_name,c.id as product_id')
                        ->from('lead_products a')
                        ->join('leads b', 'b.id=a.lead_id')
                        ->join('presto_instruments c', 'c.id=a.product_id')
                        ->where('a.lead_id', $lead_id)
                        ->get();

            if($sql->num_rows() > 0) {
                $i=1;
                foreach($sql->result() as $row);
                $data[]=$row->instruments_name;
                $data[]=$row->qty;
                $data[]=$row->id;
                $data[]=$row->product_id;

            }

            return $data;
}


public function pipeline_list_api()
{
    header('Content-Type: application/json');

    $post = json_decode(file_get_contents("php://input"), true);

    $stage_id      = $post['stage_id'] ?? null;
    $followup_type = $post['followup_type'] ?? null;
    $user_id       = $post['user_id'];
    $role_id       = $post['role_id'];

    /// 🔐 ROLE FILTER
    if ($role_id != 12 && $role_id != 41) {
        $chk = " AND b.added_by = $user_id";
    } else {
        $chk = "";
    }

    /// 🔥 DYNAMIC WHERE
    $where = "";

    // ✅ STAGE FILTER
    if (!empty($stage_id)) {
        $where .= " AND a.lead_status = '$stage_id'";
    }

    // ✅ FOLLOWUP FILTER
    if (!empty($followup_type)) {

        if ($followup_type == 1) {
            $where .= " AND DATE(a.next_follow_date) = CURDATE()";
        } elseif ($followup_type == 2) {
            $where .= " AND DATE(a.next_follow_date) < CURDATE()";
        } elseif ($followup_type == 3) {
            $where .= " AND DATE(a.next_follow_date) > CURDATE()";
        }
    }

    $query = $this->db->query("
        SELECT 
            b.id as leadid,
            b.unique_id,
            b.create_date,
            c.customer_name,
            c.contact_no,
            b.postal_address,
            b.machine_type,
            b.added_by,
            b.welcome_email_status,
            a.next_follow_date,
            a.remarks,
            c.company_name,
            su.first_name,
            su.last_name,
            b.added_by
        FROM progress_remarks a
        JOIN leads b ON a.lead_id = b.id
        LEFT JOIN system_users su ON b.added_by = su.user_id
        LEFT JOIN customer_detail c ON c.id = b.company_name
        WHERE a.id IN (
            SELECT MAX(id) FROM progress_remarks GROUP BY lead_id
        )
        $where
        AND b.closed = 0
        $chk
        GROUP BY b.id
        ORDER BY b.id DESC
    ");

    $data = [];

    foreach ($query->result() as $row) {

        /// 🔹 PRODUCT
        $productArr = $this->getProductDetails($row->leadid);
        $product = (count($productArr) > 0) ? $productArr[0] : "";

        /// 🔹 MACHINE
        if ($row->machine_type == 1) {
            $machine = "Liquid";
        } elseif ($row->machine_type == 2) {
            $machine = "Powder";
        } else {
            $machine = "Custom";
        }

        /// 🔹 REMARKS
        $fullRemarks = strip_tags($row->remarks);
        $shortRemarks = (strlen($fullRemarks) > 60)
            ? substr($fullRemarks, 0, 60) . "..."
            : $fullRemarks;

        /// 🔹 ASSIGNED
        $assigned = ucwords(strtolower($row->first_name . " " . $row->last_name));

        /// 🔥 BUTTON LOGIC

        // ✅ Update Progress
        if ($stage_id == 34 || $stage_id == 35) {
            $update_btn = false;
        } else {
            $update_btn = true;
        }

        // ✅ Send Intro Email
        $send_intro = ($row->welcome_email_status == 0);

        // ✅ Quote Logic
        $record_id = $this->salescrm->getRecordID($row->leadid);

        if ($this->salescrm->checkforquotationoraheadsteptogetquotationvisible($row->leadid)) {

            if ($stage_id == 39) {
                $quote_type = "send_for_approval";
            } else {
                $quote_type = "view_quote";
            }

            $quote_btn = true;

        } else {
            $quote_btn = false;
            $quote_type = "";
        }

        /// 🔗 QUOTE LINK
        $quoteLink = '';
        if ($quote_btn == true) {
            $rest = $this->db->select('id')
                ->from('quotation_customer_data')
                ->where('lead_id', $row->leadid)
                ->get();

            if ($rest->num_rows() > 0) {
                $d = $rest->row()->id;
            } else {
                $d = 0;
            }

            $quoteLink = page_url . 'Opportunity/GeneratedQuote/' . $d . '/Open';
        }

        /// 🔽 DOWNLOAD FLAG
        $notallowed = array('39','36','38');
        $currentstatus = $stage_id;

        if (in_array($currentstatus, $notallowed)) {
            $download = false;
        } else {
            $download = true;
        }

        $data[] = [
            "lead_id" => $row->leadid,
            "company" => ucwords(strtolower($row->company_name)),
            "contact" => ucwords(strtolower($row->customer_name)),
            "phone"   => $row->contact_no,
            "opp"     => $row->unique_id,
            "date"    => ($row->next_follow_date)
                ? date('d-m-Y', strtotime($row->next_follow_date))
                : "",
            "machine" => $machine,
            "product" => $product,
            "address" => $row->postal_address,
            "assigned"=> $assigned,

            /// 🔥 REMARKS
            "remarks_short" => $shortRemarks,
            "remarks_full"  => $fullRemarks,

            /// 🔥 BUTTON FLAGS
            "can_update"    => $update_btn,
            "can_quote"     => $quote_btn,
            "quote_type"    => $quote_type,
            "can_send_intro"=> $send_intro,
            "lead_stage"    => $stage_id,
            "quoteLink"     => $quoteLink,
            "quoteDownload" => $download,
            "added_by"      => $row->added_by
        ];
    }

    echo json_encode([
        "status" => true,
        "data"   => $data
    ]);
}

   public function details()
{
    header('Content-Type: application/json');

    $post = json_decode(file_get_contents("php://input"), true);

    $lead_id = $post['lead_id'] ?? 0;

    if (!$lead_id) {
        echo json_encode(["status" => false, "message" => "Lead ID required"]);
        return;
    }

    $this->load->model('Dashboard_model');
    $this->load->model('Salescrm_model', 'salescrm');

    /// =========================================
    /// 1. LEAD BASIC DETAILS
    /// =========================================
    $lead = $this->db->select('
            a.*,
            e.company_name,
            e.email,
            e.customer_name,
            e.contact_no,
            b.country_name,
            c.state_name,
            d.zone
        ')
        ->from('leads a')
        ->join('customer_detail e','a.company_name=e.id')
        ->join('countries b','a.country=b.country_id','left')
        ->join('states c','a.state=c.state_id','left')
        ->join('saleszone d','a.client_location=d.id','left')
        ->where('a.id', $lead_id)
        ->get()
        ->row_array();

    /// =========================================
    /// 2. CURRENT STAGE
    /// =========================================
    $statusData = $this->salescrm->getLeadStatus($lead_id);

    $current_stage_id = '';
    $current_stage_name = '';

    if (!empty($statusData)) {
        foreach ($statusData as $s);
        $current_stage_id = $s->lead_id;
        $current_stage_name = $s->lead_name;
    }

    /// =========================================
    /// 3. STAGE RELATIONS
    /// =========================================
    $stageRelation = $this->salescrm->getLeadStageRelation($current_stage_id);

    $stages = [];
    if (!empty($stageRelation)) {
        foreach ($stageRelation as $s) {
            $stages[] = [
                "id" => (int)$s->lead_id,
                "name" => $s->lead_name
            ];
        }
    }

    $ty='';
    $productName='';
    $productdetail=$this->Dashboard_model->getproductdetails($lead_id);
    if(count($productdetail)>0)
    { 
    for($i=0;$i<count($productdetail);$i++)
    {
    if($productdetail[$i]['qty']==0)
    {
    $qty=1;
    }else
    {
    $qty=$productdetail[$i]['qty'];
    }

    if($lead['machine_type']==1){
    $ty="Liquid";
    }
    else if($lead['machine_type']==2){
    $ty= "Powder";
    }
    else{
    $ty="Customise";
    }
    $productName=$productdetail[$i]['name'];
    }
    }

    /// =========================================
    /// 4. LAST FOLLOWUP
    /// =========================================
    $last = $this->db->select('a.*, c.lead_name,d.first_name,d.last_name')
        ->from('progress_remarks a')
        ->join('lead_stage c','a.lead_status=c.lead_id')
        ->join('system_users d','d.user_id=a.added_by','left')
        ->where('a.lead_id', $lead_id)
        ->order_by('a.id','desc')
        ->limit(1)
        ->get()
        ->row_array();


        /// =========================================
/// 5. LAST FOLLOWUP FILES
/// =========================================
$files = [];

if (!empty($last)) {

    $filesQuery = $this->db
        ->select('upload_file')
        ->from('lead_remarks_files')
        ->where('remarks_id', $last['id'])
        ->get();

    foreach ($filesQuery->result() as $f) {
        $files[] = [
            "file_name" => $f->upload_file,
            "url" => page_url1.'assets/lead_uploads/' . $f->upload_file];
    }
}



    echo json_encode([
        "status" => true,
        "data" => [
            "lead_id" => $lead['id'],
            "lead_no" => $lead['unique_id'],
            "company" => ucwords(strtolower($lead['company_name'])),
            "contact" => ucwords(strtolower($lead['customer_name'])),
            "phone" => $lead['contact_no'],
            "stage_id" => $current_stage_id,
            "stage_name" => ucwords(strtolower($current_stage_name)),
            "product_name" => $productName.' '.$ty,
            "remarks" => ucwords(strtolower($last['remarks'])) ?? "",
            'followup_date'=>date('d-M-Y g:i A',strtotime($last['added_on'])),
            'added_by'=>ucwords(strtolower($last['first_name']." ".$last['last_name'])),
            "files" => $files, // 🔥 ADDED HERE
            "stages" => $stages,
            'current_stage_id'=>$current_stage_id
        ]
    ]);
}

public function lead_timeline()
{
    header('Content-Type: application/json');

    $post = json_decode(file_get_contents("php://input"), true);
    $lead_id = $post['lead_id'] ?? 0;

    if (!$lead_id) {
        echo json_encode([
            "status" => false,
            "message" => "Lead ID required"
        ]);
        return;
    }

    $this->db->select('
        a.id,
        a.remarks,
        a.next_follow_date,
        a.added_on,
        b.first_name,
        b.last_name,
        c.lead_name
    ');
    $this->db->from('progress_remarks a');
    $this->db->join('system_users b', 'a.added_by = b.user_id', 'left');
    $this->db->join('lead_stage c', 'a.lead_status = c.lead_id', 'left');
    $this->db->where('a.lead_id', $lead_id);
    $this->db->order_by('a.id', 'desc');

    $query = $this->db->get();

    $timeline = [];

    foreach ($query->result() as $row) {

        /// 🔥 FETCH FILES FOR THIS REMARK
        $filesQuery = $this->db
            ->select('upload_file')
            ->from('lead_remarks_files')
            ->where('remarks_id', $row->id)
            ->get();

        $files = [];

        foreach ($filesQuery->result() as $f) {
            $files[] = [
                "file_name" => $f->upload_file,
                "url" => page_url1.'assets/lead_uploads/' . $f->upload_file
                // OR if you want page_url1:
                // "url" => page_url1.'assets/lead_uploads/'.$f->upload_file
            ];
        }

        $timeline[] = [
            "id" => $row->id,
            "stage" => ucwords(strtolower($row->lead_name)),
            "remarks" => ucwords(strtolower($row->remarks)),
            "date" => date('d-M-Y g:i A', strtotime($row->added_on)),
            "next_followup" => date('d-M-Y g:i A', strtotime($row->next_follow_date)),
            "added_by" => ucwords(strtolower(trim($row->first_name . ' ' . $row->last_name))),
            "files" => $files // ✅ ATTACHED HERE
        ];
    }

    echo json_encode([
        "status" => true,
        "data" => $timeline
    ]);
}


public function lost_reasons()
{
    header('Content-Type: application/json');

    try {

        $query = $this->db
            ->select('reason_id, reason')
            ->from('leads_unqualified_reason')
            ->where('status', 1)
            ->order_by('reason', 'asc')
            ->get();

        $data = [];

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {
                $data[] = [
                    "id" => (int)$row->reason_id,
                    "reason" => $row->reason
                ];
            }
        }

        echo json_encode([
            "status" => true,
            "data" => $data
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong"
        ]);
    }
}


public function updateLeadProgress()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => false, 'message' => 'Invalid request method']);
        return;
    }

    $lead_id   = $this->input->post('lead_id');
    $stage_id  = $this->input->post('stage_id');
    $remarks   = $this->input->post('remarks');
    $reason_id = $this->input->post('reason_id') ?: 0;
    $user_id   = $this->input->post('user_id') ?: 0;
    $next_followup_date = $this->input->post('next_followup_date');
   
    date_default_timezone_set("Asia/Kolkata");
    $date = date('Y-m-d H:i:s');


    if (empty($lead_id) || empty($stage_id) || empty($remarks)) {
        echo json_encode(['status' => false, 'message' => 'Required fields missing']);
        return;
    }

    $is_lost = !empty($reason_id);

    if (!$is_lost && empty($next_followup_date)) {
        echo json_encode(['status' => false, 'message' => 'Next follow-up date required']);
        return;
    }

    /// INSERT REMARK
    $this->db->insert('progress_remarks', [
        'lead_id'            => $lead_id,
        'lead_status'        => $stage_id,
        'remarks'            => $remarks,
        'next_follow_date'   => $next_followup_date,
        'nonqualifiedreason' => $reason_id,
        'added_on'           => $date,
        'added_by'           => $user_id
    ]);

    $insert_id = $this->db->insert_id();

    /// UPDATE LEAD STAGE
    // $this->db->where('id', $lead_id);
    // $this->db->update('leads', ['lead_stage' => $stage_id]);

    /// FILE UPLOAD
    $uploaded_files = [];

    if (!empty($_FILES['files']['name'][0])) {

        $this->load->library('upload');

        $upload_path = './assets/lead_uploads/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        foreach ($_FILES['files']['name'] as $key => $name) {

            $clean_name = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $name);

            $_FILES['file']['name']     = $clean_name;
            $_FILES['file']['type']     = $_FILES['files']['type'][$key];
            $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$key];
            $_FILES['file']['error']    = $_FILES['files']['error'][$key];
            $_FILES['file']['size']     = $_FILES['files']['size'][$key];

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
            $config['file_name']     = time() . '_' . $clean_name;

            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {

                $fileData = $this->upload->data();
                $file_path = 'assets/lead_uploads/' . $fileData['file_name'];

                $this->db->insert('lead_remarks_files', [
                    'lead_id'    => $lead_id,
                    'remarks_id'  => $insert_id,
                    'lead_status'=> $stage_id,
                    'upload_file'  => $fileData['file_name']
                ]);

                $uploaded_files[] = $file_path;

            } else {
                log_message('error', $this->upload->display_errors());
            }
        }
    }

    echo json_encode([
        'status' => true,
        'message' => 'Progress updated successfully',
        'files_uploaded' => $uploaded_files
    ]);
}

public function extendFollowupDate()
{
    header('Content-Type: application/json');

    /// 🔥 READ RAW JSON (IMPORTANT)
    $post = json_decode(file_get_contents("php://input"), true);

    $lead_id = $post['lead_id'] ?? 0;
    $next_followup_date = $post['next_followup_date'] ?? '';

    /// 🔥 VALIDATION
    if (empty($lead_id)) {
        echo json_encode([
            "status" => false,
            "message" => "Lead ID required"
        ]);
        return;
    }

    if (empty($next_followup_date)) {
        echo json_encode([
            "status" => false,
            "message" => "Next follow-up date required"
        ]);
        return;
    }

    /// 🔥 GET LAST ENTRY
    $this->db->where('lead_id', $lead_id);
    $this->db->order_by('id', 'DESC');
    $last = $this->db->get('progress_remarks')->row();

    if (!$last) {
        echo json_encode([
            'status' => false,
            'message' => 'No record found'
        ]);
        return;
    }

    /// 🔥 UPDATE LAST ENTRY
    $this->db->where('id', $last->id);
    $this->db->update('progress_remarks', [
        'next_follow_date' => $next_followup_date
    ]);

    echo json_encode([
        'status' => true,
        'message' => 'Follow-up date updated successfully'
    ]);
}


public function saveFcmToken()
{
    header('Content-Type: application/json');

    /// 🔥 GET JSON BODY (IMPORTANT for your Flutter post)
    $post = json_decode(file_get_contents("php://input"), true);

    $user_id = $post['user_id'] ?? 0;
    $token   = $post['fcm_token'] ?? '';

    if (!$user_id || empty($token)) {
        echo json_encode([
            "status" => false,
            "message" => "User ID or token missing"
        ]);
        return;
    }

    /// 🔥 CHECK IF TOKEN ALREADY EXISTS
    $this->db->where('fcm_token', $token);
    $exists = $this->db->get('user_devices')->row();

    if ($exists) {
        /// 🔁 UPDATE USER_ID (in case user re-login)
        $this->db->where('fcm_token', $token);
        $this->db->update('user_devices', [
            'user_id' => $user_id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    } else {

        /// 🧹 OPTIONAL: REMOVE OLD TOKENS FOR THIS USER (clean DB)
        $this->db->where('user_id', $user_id);
        $this->db->delete('user_devices');

        /// ➕ INSERT NEW TOKEN
        $this->db->insert('user_devices', [
            'user_id'    => $user_id,
            'fcm_token'  => $token,
            'device_type'=> 'android',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    echo json_encode([
        "status" => true,
        "message" => "Token saved successfully"
    ]);
}



public function send_push_notification()
{
    //echo "hi"; exit;
    // ================= READ RAW JSON =================
    $input = json_decode(file_get_contents("php://input"), true);
   // echo "<pre>"; print_r($input); exit;
    // ================= INPUT =================
    $title    = $input['title'] ?? '';
    $message  = $input['message'] ?? '';
    $quote_id = $input['quote_id'] ?? '';

    // ================= VALIDATION =================
    if (empty($quote_id)) {
        echo json_encode([
            "status" => false,
            "msg" => "quote_id required"
        ]);
        return;
    }

    // ================= GET TOKENS =================
    $tokens = $this->db->select('fcm_token')
        ->from('user_devices')
        ->where('fcm_token !=', '')
        ->where('user_id',139)
        ->get()
        ->result_array();

    if (empty($tokens)) {
        echo json_encode([
            "status" => false,
            "msg" => "No tokens"
        ]);
        return;
    }

    $count = 0;
    $responses = [];

    foreach ($tokens as $t) {

        $dataPayload = [
            "type" => "quote_approval",
            "quote_id" => (string)$quote_id,
            "screen" => "quote_detail"
        ];

        $res = sendFCM(
            $t['fcm_token'],
            $title ?: "Quote Approval",
            $message ?: "Quote #$quote_id needs approval",
            $dataPayload
        );

        $responses[] = json_decode($res, true);
        $count++;
    }

    // ================= FINAL RESPONSE =================
    echo json_encode([
        "status" => true,
        "sent" => $count,
        "firebase_response" => $responses
    ]);
}


public function get_notifications()
{
    $input = json_decode(file_get_contents("php://input"), true);

    $user_id = $input['user_id'] ?? 0;

    $data = $this->db
        ->where('user_id', $user_id)
        ->order_by('id', 'DESC')
        ->get('app_notifications')
        ->result_array(); // 🔥 IMPORTANT

    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
}


public function get_unread_count()
{
    $input = json_decode(file_get_contents("php://input"), true);

    $user_id = $input['user_id'] ?? 0;

    $count = $this->db
        ->where('user_id', $user_id)
        ->where('is_read', 0)
        ->count_all_results('app_notifications');

    echo json_encode([
        "status" => true,
        "count" => $count
    ]);
}

public function mark_notification_read()
{
   $input = json_decode(file_get_contents("php://input"), true);

    $id = $input['id'] ?? 0;

    $this->db->where('id', $id)->update('app_notifications', [
        'is_read' => 1
    ]);

    echo json_encode(["status" => true]);
}



public function chat_list()
{
    /// 🔥 READ RAW JSON INPUT
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    $user_id = isset($input['user_id'])
        ? (int)$input['user_id']
        : 0;

    /// ❌ VALIDATION
    if (!$user_id) {

        echo json_encode([
            "status" => false,
            "message" => "User ID required"
        ]);

        return;
    }

    /// 🔥 MAIN QUERY
    $this->db->select("
        cr.id as chat_id,

        u.user_id,

        CONCAT(
            u.first_name,
            ' ',
            u.last_name
        ) as name,

        IFNULL(
            clm.last_message,
            ''
        ) as last_message,

        IFNULL(
            clm.last_message_time,
            ''
        ) as last_message_time,

        (
            SELECT COUNT(*)
            FROM messages m
            WHERE m.chat_id = cr.id
            AND m.sender_id != ".$user_id."
            AND m.read_reciept = 0
        ) as unread_count
    ");

    $this->db->from('chat_rooms cr');

    /// ✅ ONLY MY CHATS
    $this->db->join(
        'chat_participants cp',
        'cp.chat_id = cr.id 
        AND cp.user_id = '.$user_id
    );

    /// ✅ OTHER USER
    $this->db->join(
        'chat_participants cp2',
        'cp2.chat_id = cr.id 
        AND cp2.user_id != '.$user_id
    );

    /// ✅ USER DETAILS
    $this->db->join(
        'system_users u',
        'u.user_id = cp2.user_id'
    );

    /// ✅ LAST MESSAGE
    $this->db->join(
        'chat_last_message clm',
        'clm.chat_id = cr.id',
        'left'
    );

    /// ✅ ONLY SINGLE CHAT
    $this->db->where(
        'cr.type',
        'single'
    );

    /// ✅ GROUPING
    $this->db->group_by([
        'cr.id',
        'cp2.user_id'
    ]);

    /// ✅ SORT LATEST FIRST
    $this->db->order_by(
        'clm.last_message_time',
        'DESC'
    );

    $result = $this->db
        ->get()
        ->result_array();

    $data = [];

    foreach ($result as $row) {

        $data[] = [

            "chat_id" =>
                (int)$row['chat_id'],

            "user_id" =>
                (int)$row['user_id'],

            "name" =>
                ucwords(strtolower(trim($row['name']))),

            "last_message" =>
                $row['last_message'] ?? "",

            "last_message_time" =>
                $row['last_message_time'] ?? "",

            "unread_count" =>
                (int)$row['unread_count'],
        ];
    }

    echo json_encode([
        "status" => true,
        "message" => "Chat list fetched",
        "data" => $data
    ]);
}


// public function chat_list()
// {
//     /// 🔥 READ RAW JSON INPUT
//     $input = json_decode(file_get_contents("php://input"), true);
//     $user_id = isset($input['user_id']) ? (int)$input['user_id'] : 0;
   

//     if (!$user_id) {
//         echo json_encode([
//             "status" => false,
//             "message" => "User ID required"
//         ]);
//         return;
//     }

//     /// 🔥 MAIN QUERY
//     $this->db->select("
//         cr.id as chat_id,
//         u.user_id,
//         CONCAT(u.first_name,' ',u.last_name) as name,
//         IFNULL(clm.last_message,'') as last_message,
//         IFNULL(clm.last_message_time,'') as last_message_time,

//         (
//             SELECT COUNT(*)
//             FROM messages m
//             LEFT JOIN message_read mr 
//                 ON mr.message_id = m.id AND mr.user_id = $user_id
//             WHERE m.chat_id = cr.id
//             AND m.sender_id != $user_id
//             AND mr.id IS NULL
//         ) as unread_count
//     ");

//     $this->db->from('chat_rooms cr');

//     /// ✅ ONLY MY CHATS
//     $this->db->join('chat_participants cp', 'cp.chat_id = cr.id AND cp.user_id = '.$user_id);

//     /// ✅ OTHER USER (SAFE JOIN)
//     $this->db->join('chat_participants cp2', 'cp2.chat_id = cr.id AND cp2.user_id != '.$user_id);

//     $this->db->join('system_users u', 'u.user_id = cp2.user_id');

//     /// ✅ LAST MESSAGE
//     $this->db->join('chat_last_message clm', 'clm.chat_id = cr.id', 'left');

//     /// ✅ ONLY SINGLE CHAT (IMPORTANT)
//     $this->db->where('cr.type', 'single');

//     /// ✅ GROUPING
//     $this->db->group_by(['cr.id', 'cp2.user_id']);

//     /// ✅ SORT
//     $this->db->order_by('clm.last_message_time', 'DESC');

//     $result = $this->db->get()->result_array();

//     $data = [];

//     foreach ($result as $row) {
//         $data[] = [
//             "chat_id" => (int)$row['chat_id'],
//             "user_id" => (int)$row['user_id'],
//             "name" => $row['name'],
//             "last_message" => $row['last_message'],
//             "last_message_time" => $row['last_message_time'],
//             "unread_count" => (int)$row['unread_count'],
//         ];
//     }

//     echo json_encode([
//         "status" => true,
//         "message" => "Chat list fetched",
//         "data" => $data
//     ]);
// }

    
public function messages()
{
    /// 🔥 READ RAW JSON
    $input = json_decode(file_get_contents("php://input"), true);

    $chat_id = isset($input['chat_id']) ? (int)$input['chat_id'] : 0;
    $page    = isset($input['page']) ? (int)$input['page'] : 1;

    /// 🔥 VALIDATION
    if (!$chat_id) {
        echo json_encode([
            "status" => false,
            "message" => "Chat ID required"
        ]);
        return;
    }

    /// 🔥 PAGINATION DEFAULT
    $limit  = 20;
    $offset = ($page > 0 ? ($page - 1) : 0) * $limit;

    /// 🔥 FETCH MESSAGES
    $this->db->select("
        id,
        chat_id,
        sender_id,
        receiver_id,
        message,
        message_type,
        created_at
    ");
    $this->db->from("messages");
    $this->db->where("chat_id", $chat_id);
    $this->db->order_by("id", "DESC");
    $this->db->limit($limit, $offset);

    $result = $this->db->get()->result_array();

    /// 🔥 REVERSE FOR CHAT FLOW (OLD → NEW)
    $messages = array_reverse($result);

    echo json_encode([
        "status" => true,
        "message" => "Messages fetched",
        "data" => $messages
    ]);
}

public function send_message()
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid JSON"
        ]);
        return;
    }

    $chat_id     = (int)($input['chat_id'] ?? 0);
    $sender_id   = (int)($input['sender_id'] ?? 0);
    $receiver_id = (int)($input['receiver_id'] ?? 0);
    $message     = trim($input['message'] ?? '');
    $type        = $input['type'] ?? 'text';

    if (!$chat_id || !$sender_id || $message == '') {
        echo json_encode([
            "status" => false,
            "message" => "Missing required fields"
        ]);
        return;
    }

    /// 🔥 INSERT MESSAGE
    $this->db->insert("messages", [
        "chat_id" => $chat_id,
        "sender_id" => $sender_id,
        "receiver_id" => $receiver_id,
        "message" => $message,
        "message_type" => $type,
        "created_at" => date("Y-m-d H:i:s"),
        "status" => "sent"
    ]);

    $message_id = $this->db->insert_id();

    /// 🔥 UPDATE LAST MESSAGE
    $this->db->replace("chat_last_message", [
        "chat_id" => $chat_id,
        "last_message" => $message,
        "last_message_time" => date("Y-m-d H:i:s"),
        "last_sender_id" => $sender_id
    ]);

    /// 🔔 SEND PUSH (SAFE)
    $this->send_chat_push_notification_internal(
    $receiver_id,
    $chat_id,
    $message,
    "New Message",
    $sender_id   // 🔥 ADD THIS
);
    /// ✅ CLEAN RESPONSE (VERY IMPORTANT)
    echo json_encode([
        "status" => true,
        "message" => "Message sent",
        "message_id" => $message_id
    ]);
}

public function mark_read()
{
    $chat_id = $this->input->post('chat_id');
    $user_id = $this->input->post('user_id');

    $messages = $this->db
        ->where("chat_id", $chat_id)
        ->where("sender_id !=", $user_id)
        ->get("messages")
        ->result_array();

    foreach ($messages as $msg) {
        $this->db->insert("message_read", [
            "message_id" => $msg['id'],
            "user_id" => $user_id,
            "read_at" => date("Y-m-d H:i:s")
        ]);
    }

    echo json_encode(["status" => true]);
}


public function create_chat()
{
    /// 🔥 READ RAW JSON
    $input = json_decode(file_get_contents("php://input"), true);

    $user1 = isset($input['user1']) ? (int)$input['user1'] : 0;
    $user2 = isset($input['user2']) ? (int)$input['user2'] : 0;

    /// 🔥 VALIDATION
    if (!$user1 || !$user2) {
        echo json_encode([
            "status" => false,
            "message" => "Both users required"
        ]);
        return;
    }

    if ($user1 == $user2) {
        echo json_encode([
            "status" => false,
            "message" => "Cannot create chat with same user"
        ]);
        return;
    }

    /// 🔥 CHECK EXISTING CHAT
    $this->db->select("chat_id");
    $this->db->from("chat_participants");
    $this->db->where_in("user_id", [$user1, $user2]);
    $this->db->group_by("chat_id");
    $this->db->having("COUNT(DISTINCT user_id) =", 2);

    $existing = $this->db->get()->row_array();

    if ($existing) {
        echo json_encode([
            "status" => true,
            "message" => "Chat already exists",
            "chat_id" => (int)$existing['chat_id']
        ]);
        return;
    }

    /// 🔥 CREATE NEW CHAT
    $this->db->insert("chat_rooms", [
        "created_at" => date("Y-m-d H:i:s")
    ]);

    $chat_id = $this->db->insert_id();

    /// 🔥 INSERT PARTICIPANTS
    $this->db->insert_batch("chat_participants", [
        ["chat_id" => $chat_id, "user_id" => $user1],
        ["chat_id" => $chat_id, "user_id" => $user2],
    ]);

    /// 🔥 INSERT INITIAL LAST MESSAGE (IMPORTANT)
    $this->db->replace("chat_last_message", [
        "chat_id" => $chat_id,
        "last_message" => "",
        "last_message_time" => date("Y-m-d H:i:s"),
        "last_sender_id" => $user1
    ]);

    echo json_encode([
        "status" => true,
        "message" => "Chat created",
        "chat_id" => (int)$chat_id
    ]);
}



public function user_list()
{
    $input = json_decode(file_get_contents("php://input"), true);
    $user_id = (int)$input['user_id'];

    $this->db->select("
        u.user_id,
        CONCAT(u.first_name,' ',u.last_name) as name,
        d.department
    ");

    $this->db->from("system_users u");
    $this->db->join("departments d", "d.department_id = u.department_id", "left");
    $this->db->where("u.user_id !=", $user_id);
    $this->db->where("u.business_location", 2);

    $result = $this->db->get()->result_array();

    /// 🔥 FORMAT NAME (Title Case)
    $data = [];

    foreach ($result as $row) {

        $name = strtolower($row['name']);         // make all lowercase
        $name = ucwords($name);                   // capitalize each word

        $data[] = [
            "user_id" => (int)$row['user_id'],
            "name" => $name,
            "department" => ucwords(strtolower($row['department'])) ?? "",
        ];
    }

    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
}



public function send_chat_push_notification()
{
    /// 🔥 READ INPUT
    $input = json_decode(file_get_contents("php://input"), true);

    $receiver_id = $input['receiver_id'] ?? 0;
    $chat_id     = $input['chat_id'] ?? 0;
    $message     = $input['message'] ?? '';
    $sender_name = $input['sender_name'] ?? 'New Message';

    if (!$receiver_id || !$chat_id || !$message) {
        echo json_encode([
            "status" => false,
            "msg" => "Missing fields"
        ]);
        return;
    }

    /// 🔥 GET RECEIVER TOKENS
    $tokens = $this->db->select('fcm_token')
        ->from('user_devices')
        ->where('user_id', $receiver_id)
        ->where('fcm_token !=', '')
        ->get()
        ->result_array();

    if (empty($tokens)) {
        echo json_encode([
            "status" => false,
            "msg" => "No tokens for user"
        ]);
        return;
    }

    $count = 0;
    $responses = [];

    foreach ($tokens as $t) {

        /// 🔥 CHAT PAYLOAD
        $dataPayload = [
            "type" => "chat_message",              // 🔥 IMPORTANT
            "chat_id" => (string)$chat_id,
            "receiver_id" => (string)$receiver_id,
            "screen" => "chat_detail"
        ];

        $res = sendFCM(
            $t['fcm_token'],
            $sender_name,     // title (sender name)
            $message,         // body
            $dataPayload
        );

        $responses[] = json_decode($res, true);
        $count++;
    }

    echo json_encode([
        "status" => true,
        "sent" => $count,
        "firebase_response" => $responses
    ]);
}

private function send_chat_push_notification_internal(
    $receiver_id,
    $chat_id,
    $message,
    $title,
    $sender_id
)
{
    /// 🔥 GET SENDER DETAILS
    $sender = $this->db->select('first_name, last_name')
        ->from('system_users')
        ->where('user_id', $sender_id)
        ->get()
        ->row();

    $first_name = $sender->first_name ?? '';
    $last_name  = $sender->last_name ?? '';

    $full_name = trim($first_name . ' ' . $last_name);

    /// 🔥 INITIALS
    $initials = strtoupper(
        substr($first_name, 0, 1) .
        substr($last_name, 0, 1)
    );

    /// 🔥 GET TOKENS
    $tokens = $this->db->select('fcm_token')
        ->from('user_devices')
        ->where('user_id', $receiver_id)
        ->where('fcm_token !=', '')
        ->get()
        ->result_array();

    if (empty($tokens)) return;

    foreach ($tokens as $t) {

        $dataPayload = [
            "type" => "chat_message",
            "chat_id" => (string)$chat_id,
            "sender_name" => $full_name,
            "sender_initials" => $initials,
            "message" => $message,
            "timestamp" => date("Y-m-d H:i:s"),
            "screen" => "chat_detail"
        ];
        sendFCMData(
            $t['fcm_token'],
            $full_name,
            $message,
            $dataPayload
        );
    }
}



public function approval_counts_api()
{
    $post = json_decode(file_get_contents('php://input'), true);

    try {

        /// 🔥 MARKETING QUOTES
         $marketing = $this->get_lead_stage_count(
            36,
            null,
            null
        );

        /// 🔥 SERVICE QUOTES
         $service =$this->Dashboard_model->getServiceStageCounts(3);
         $spare=$this->Dashboard_model->spares_lead_stage_counts_app(4);

        /// 🔥 SPARE QUOTES
        // $spare = $this->db
        //     ->where('approval_type', 'spare')
        //     ->where('status', 'pending')
        //     ->count_all_results('quote_approvals');


        echo json_encode([
            "status" => true,
            "data" => [
                "marketing" => $marketing,
                "service"   => $service,
                "spare"     => $spare
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong"
        ]);
    }
}



public function approval_or_rejection_api()
{
    header('Content-Type: application/json');

    try {

        /// 🔥 GET JSON INPUT
        $post = json_decode(file_get_contents("php://input"), true);
        // echo "<pre>"; print_r($post); exit;
        $user_id = isset($post['user_id']) ? $post['user_id'] : 0;
        $lead_id = isset($post['lead_id']) ? $post['lead_id'] : 0;
        $status  = isset($post['status']) ? $post['status'] : 0; // 1=approve, 0=reject
        $remarks = isset($post['remarks']) ? trim($post['remarks']) : "";
        $approved_by = isset($post['approved_by']) ? trim($post['approved_by']) : "";
        $marketing_person_id = isset($post['marketing_person_id']) ? trim($post['marketing_person_id']) : "";

        /// ❌ VALIDATION
        if (!$lead_id) {
            echo json_encode([
                "status" => false,
                "message" => "Lead ID missing"
            ]);
            return;
        }

        if ($status == 0 && $remarks == "") {
            echo json_encode([
                "status" => false,
                "message" => "Remarks required for rejection"
            ]);
            return;
        }

        $company_name='';
        $rest=$this->db->select('b.company_name')->from('leads a')->join('customer_detail b','a.company_name=b.id')->where('a.id',$lead_id)->get();
        if($rest->num_rows()>0)
        {
            foreach($rest->result() as $rrow);
            $company_name=ucwords(strtolower($rrow->company_name));
        }

        /// 🔥 STAGE LOGIC
        if ($status == 1) {
            $stage = 37;
            $remark_title = 'Quotation Approved.';
        } else {
            $stage = 38;
            $remark_title = 'Quotation Rejected.';
        }

        /// 📅 NEXT FOLLOWUP (+2 days)
        $currentDate = new DateTime();
        $currentDate->modify('+2 days');
        $futureDate = $currentDate->format('Y-m-d');

        /// 🔥 INSERT PROGRESS
        $data = [
            'lead_id'          => $lead_id,
            'lead_status'      => $stage,
            'next_follow_date' => $futureDate,
            'remarks'          => $remarks,
            'remark_title'     => $remark_title,
            'added_on'         => date('Y-m-d H:i:s'),
            'added_by'         => $approved_by
        ];

        $this->db->insert('progress_remarks', $data);

        /// 🔥 GET LEAD OWNER + FCM TOKEN
        $q = $this->db->select('b.title, b.first_name, b.last_name, c.fcm_token')
            ->from('system_users b')
            ->join('user_devices c','b.user_id=c.user_id')
            ->where('b.user_id', $marketing_person_id)
            ->get();

        if ($q->num_rows() > 0) {

            $row = $q->row();

            $name = ucwords(strtolower(
                $row->first_name . " " . $row->last_name
            ));

            $token = $row->fcm_token;

            /// 🔔 SEND PUSH ONLY IF TOKEN EXISTS
            if (!empty($token)) {

                if ($status == 1) {
                    $title = "Quotation Approved ✅";
                    $message = "Hi $name, your quotation for $company_name has been approved.";
                } else {
                    $title = "Quotation Rejected ❌";
                    $message = "Hi $name, your quotation for $company_name was rejected.";
                }

                $dataPayload = [
                    "type"    => "quote_approval",
                    "lead_id" => (string)$lead_id,
                    "screen"  => "quote_detail"
                ];

                sendFCM($token, $title, $message, $dataPayload);
            }
        }

        /// ✅ SUCCESS RESPONSE
        echo json_encode([
            "status" => true,
            "message" => ($status == 1)
                ? "Quotation Approved Successfully"
                : "Quotation Rejected Successfully"
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Server error"
        ]);
    }
}



// public function get_quote_approval_status()
// {
//     header('Content-Type: application/json');

//     try {

//         $post = json_decode(file_get_contents("php://input"), true);
//         $lead_id = isset($post['lead_id']) ? $post['lead_id'] : 0;

//         if (!$lead_id) {
//             echo json_encode([
//                 "status" => false,
//                 "message" => "Lead ID missing"
//             ]);
//             return;
//         }

//         /// 🔥 GET CURRENT STAGE
//         $lead = $this->db
//             ->select('lead_status as lead_stage')
//             ->from('progress_remarks')
//             ->where('id', $lead_id)
//             ->order_by('id','DESC')
//             ->limit(1)
//             ->get();

//         if ($lead->num_rows() == 0) {
//             echo json_encode([
//                 "status" => false,
//                 "message" => "Lead not found"
//             ]);
//             return;
//         }

//         $lead_stage = $lead->row()->lead_stage;

//         /// 🎯 CASE 1: PENDING (SEND FOR APPROVAL)
//         if ($lead_stage == 36) {
//             echo json_encode([
//                 "status" => true,
//                 "approval_status" => "pending"
//             ]);
//             return;
//         }

//         /// 🎯 CASE 2: CHECK LATEST APPROVAL / REJECTION
//         $q = $this->db
//             ->select('lead_status')
//             ->from('progress_remarks')
//             ->where('lead_id', $lead_id)
//             ->where_in('lead_status', [37, 38])
//             ->order_by('id', 'DESC')
//             ->limit(1)
//             ->get();

//         if ($q->num_rows() > 0) {
//             $row = $q->row();

//             if ($row->lead_status == 37) {
//                 echo json_encode([
//                     "status" => true,
//                     "approval_status" => "approved"
//                 ]);
//                 return;
//             }

//             if ($row->lead_status == 38) {
//                 echo json_encode([
//                     "status" => true,
//                     "approval_status" => "rejected"
//                 ]);
//                 return;
//             }
//         }

//         /// 🎯 CASE 3: NO BADGE, NO ACTION
//         echo json_encode([
//             "status" => true,
//             "approval_status" => "none"
//         ]);

//     } catch (Exception $e) {

//         echo json_encode([
//             "status" => false,
//             "message" => "Server error"
//         ]);
//     }
// }




public function get_quote_approval_status()
{
    header('Content-Type: application/json');

    try {

        $post = json_decode(file_get_contents("php://input"), true);

        $lead_id = isset($post['lead_id'])
            ? (int)$post['lead_id']
            : 0;

        if (!$lead_id) {

            echo json_encode([
                "status" => false,
                "message" => "Lead ID missing"
            ]);

            return;
        }

        /// 🔥 GET CURRENT STAGE
        $lead = $this->db
            ->select('lead_status')
            ->from('progress_remarks')
            ->where('lead_id', $lead_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get();

        if ($lead->num_rows() == 0) {

            echo json_encode([
                "status" => false,
                "message" => "Lead not found"
            ]);

            return;
        }

        $lead_stage = (int)$lead->row()->lead_status;

        /**
         * =====================================================
         * 33 = UNDER APPROVAL
         * SHOW ACTIONS ONLY
         * NO BADGE
         * =====================================================
         */
        if ($lead_stage == 33) {

            echo json_encode([
                "status" => true,
                "approval_status" => "pending"
            ]);

            return;
        }

        /**
         * =====================================================
         * 36 = PENDING
         * SHOW BADGE ONLY
         * =====================================================
         */
        if ($lead_stage == 36) {

            echo json_encode([
                "status" => true,
                "approval_status" => "pending"
            ]);

            return;
        }

        /**
         * =====================================================
         * 37 = APPROVED
         * =====================================================
         */
        if ($lead_stage == 37) {

            echo json_encode([
                "status" => true,
                "approval_status" => "approved"
            ]);

            return;
        }

        /**
         * =====================================================
         * 38 = REJECTED
         * =====================================================
         */
        if ($lead_stage == 38) {

            echo json_encode([
                "status" => true,
                "approval_status" => "rejected"
            ]);

            return;
        }

        /**
         * =====================================================
         * DEFAULT
         * =====================================================
         */
        echo json_encode([
            "status" => true,
            "approval_status" => "none"
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Server error"
        ]);
    }
}


public function getServicePipelineCounts()
{
    header('Content-Type: application/json');

    try {

        $stages = $this->Dashboard_model->getAllServiceLeadStages();

        $data = [];

        foreach ($stages as $stage) {

            $count = $this->Dashboard_model->getServiceStageCounts($stage->stage_id);

            $data[] = [
                "stage_id"   => $stage->stage_id,
                "stage_name" => $stage->stage_name,
                "count"      => (int)$count,
                "color"      => $this->getServiceColor($stage->stage_name),
                "icon"       => $this->getServiceIcon($stage->stage_name),
            ];
        }

        echo json_encode([
            "status" => true,
            "data"   => $data
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }
}


private function getServiceColor($name)
{
    $name = strtolower($name);

    if (strpos($name, 'new') !== false) return '#9E9E9E';
    if (strpos($name, 'assign') !== false) return '#2196F3';
    if (strpos($name, 'progress') !== false) return '#FF9800';
    if (strpos($name, 'pending') !== false) return '#9C27B0';
    if (strpos($name, 'closed') !== false) return '#4CAF50';

    return '#2C7DB7';
}

private function getServiceIcon($name)
{
    $name = strtolower($name);

    if (strpos($name, 'new') !== false) return 'new';
    if (strpos($name, 'assign') !== false) return 'assign';
    if (strpos($name, 'progress') !== false) return 'progress';
    if (strpos($name, 'pending') !== false) return 'pending';
    if (strpos($name, 'closed') !== false) return 'closed';

    return 'default';
}


public function getServiceFollowupCounts()
{
    header('Content-Type: application/json');

    try {

        $input = json_decode(file_get_contents("php://input"), true);

        $user_id = $input['user_id'] ?? null;
        $role_id = $input['role_id'] ?? null;

        // ================= BASE QUERY =================
        $this->db->from('service_opportunities so');

        $this->db->join("
            (
                SELECT p1.*
                FROM service_progress_history p1
                INNER JOIN (
                    SELECT opportunity_id, MAX(history_id) as max_id
                    FROM service_progress_history
                    GROUP BY opportunity_id
                ) p2 ON p1.history_id = p2.max_id
            ) sph
        ", "sph.opportunity_id = so.opportunity_id", "left");

        // ================= ROLE FILTER =================
        if ($role_id != 12 && $role_id != 41 && $user_id != 111) {
            if (!empty($user_id)) {
                $this->db->where('so.marketing_person_id', $user_id);
            }
        }

        /// 🔥 IMPORTANT: NOT NULL FILTER
        $this->db->where('sph.next_follow_date IS NOT NULL', null, false);

        $date = date('Y-m-d');

        // ================= BASE QUERY =================
        $base_query = $this->db->get_compiled_select();

        // ================= TODAY =================
        $today_count = $this->db
            ->query($base_query . " AND DATE(sph.next_follow_date) = '$date'")
            ->num_rows();

        // ================= MISSED =================
        $missed_count = $this->db
            ->query($base_query . " AND DATE(sph.next_follow_date) < '$date'")
            ->num_rows();

        // ================= UPCOMING =================
        $upcoming_count = $this->db
            ->query($base_query . " AND DATE(sph.next_follow_date) > '$date'")
            ->num_rows();

        echo json_encode([
            "status" => true,
            "data" => [
                "today" => $today_count,
                "missed" => $missed_count,
                "upcoming" => $upcoming_count
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }
}

public function service_master_data()
{
    header('Content-Type: application/json');

    try {

        /// 🔥 READ INPUT (JSON FIRST)
        $inputJSON = trim(file_get_contents("php://input"));
        $input = json_decode($inputJSON, true);

        $user_id = $input['user_id'] ?? $this->input->post('user_id');
        $role_id = $input['role_id'] ?? $this->input->post('role_id');

        /// 🔥 VALIDATION
        if (empty($user_id)) {
            echo json_encode([
                "status" => false,
                "msg" => "User ID missing"
            ]);
            exit;
        }

        /// 🔥 VERIFY USER
        $user = $this->db->where('user_id', $user_id)->get('system_users')->row();

        if (!$user) {
            echo json_encode([
                "status" => false,
                "msg" => "Invalid user"
            ]);
            exit;
        }

        $data = [];

        // =========================
        // 🔹 LEAD SOURCES
        // =========================
        $data['sources'] = $this->db
            ->select('source_id as id,
                CONCAT(UCASE(LEFT(lead_source,1)), LCASE(SUBSTRING(lead_source,2))) as name')
            ->from('lead_source')
            ->where('status', 1)
            ->order_by('lead_source', 'ASC')
            ->get()->result();

        // =========================
        // 🔹 MARKETING USERS
        // =========================
        $this->db->select('user_id as id,
            CONCAT(
                CONCAT(UCASE(LEFT(first_name,1)), LCASE(SUBSTRING(first_name,2))),
                " ",
                CONCAT(UCASE(LEFT(last_name,1)), LCASE(SUBSTRING(last_name,2)))
            ) as name
        ');
        $this->db->from('system_users');
        $this->db->where('user_status', 1);
        $this->db->where('department_id', 22); // 🔥 SERVICE TEAM

        /// OPTIONAL ROLE FILTER
        if ($role_id != 12) {
            $this->db->where('user_id', $user_id);
        }

        $this->db->order_by('first_name', 'ASC');

        $data['marketing_users'] = $this->db->get()->result();

        // =========================
        // 🔹 COUNTRIES
        // =========================
        $data['countries'] = $this->db
            ->select('country_id as id,
                CONCAT(UCASE(LEFT(country_name,1)), LCASE(SUBSTRING(country_name,2))) as name')
            ->from('countries')
            ->where('country_status', 1)
            ->order_by('country_name', 'ASC')
            ->get()->result();


        // =========================
        // 🔥 FINAL RESPONSE
        // =========================
        echo json_encode([
            "status" => true,
            "data" => $data
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "msg" => $e->getMessage()
        ]);
    }
}


public function search_customer_api()
{
    header('Content-Type: application/json');

    try {

        /// 🔥 INPUT
        $inputJSON = trim(file_get_contents("php://input"));
        $input = json_decode($inputJSON, true);

        if (!$input) {
            $input = $this->input->post();
        }

        $search = isset($input['term']) ? trim($input['term']) : '';

        if (strlen($search) < 2) {
            echo json_encode([
                "status" => true,
                "data" => []
            ]);
            return;
        }

        /// 🔥 MARKETING TABLE
        $this->db->select("
            CONCAT(company_name, ' | ', address) as text,
            id as raw_id,
            CONCAT('M_', id) as id,
            'marketing' as source_type,
            'customer_detail' as source_table
        ", false);
        $this->db->from('customer_detail');
        $this->db->group_start();
        $this->db->like('company_name', $search);
        $this->db->or_like('address', $search);
        $this->db->group_end();
        $query1 = $this->db->get_compiled_select();

        /// 🔥 SPARES TABLE
        $this->db->select("
            CONCAT(company_name, ' | ', address) as text,
            customer_id as raw_id,
            CONCAT('S_', customer_id) as id,
            'spares' as source_type,
            'spares_customers' as source_table
        ", false);
        $this->db->from('spares_customers');
        $this->db->group_start();
        $this->db->like('company_name', $search);
        $this->db->or_like('address', $search);
        $this->db->group_end();
        $query2 = $this->db->get_compiled_select();

        /// 🔥 UNION
        $result = $this->db
            ->query($query1 . " UNION " . $query2 . " LIMIT 50")
            ->result_array();

        /// 🔥 RESPONSE
        echo json_encode([
            "status" => true,
            "data" => $result
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }
}
public function add_new_customer_api()
{
    header('Content-Type: application/json');

    try {

        // 🔹 GET RAW INPUT (important for consistency)
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $this->input->post();
        }

        // 🔹 SAFE FETCH
        $company  = trim($input['company'] ?? '');
        $brand    = $input['brand'] ?? null;
        $country  = $input['country'] ?? null;
        $email    = trim($input['email'] ?? '');
        $address  = trim($input['address'] ?? '');
        $person   = trim($input['contactpersonname'] ?? '');
        $phone    = trim($input['personcontactno'] ?? '');
        $altphone = trim($input['acontactno'] ?? '');

        // 🔴 VALIDATION
        if ($company == '') {
            echo json_encode([
                "status" => false,
                "message" => "Company name required"
            ]);
            return;
        }

        if ($country == '' || $country == null) {
            echo json_encode([
                "status" => false,
                "message" => "Country required"
            ]);
            return;
        }

        // 🔹 BRAND LOGIC
        $brand_id = is_numeric($brand) ? (int)$brand : 0;

        if (!$brand_id && !empty($brand)) {
            $this->db->insert('spare_company_brand', [
                'name' => trim($brand)
            ]);
            $brand_id = $this->db->insert_id();
        }

        // 🔹 INSERT
        $insert = [
            'company_name'         => $company,
            'brand_id'             => $brand_id,
            'country_id'           => $country,
            'email'                => $email,
            'address'              => $address,
            'contact_person'       => $person,
            'contact_person_no'    => $phone,
            'alternate_contact_no' => $altphone,
            'status'               => 1,
            'created_at'           => date('Y-m-d H:i:s')
        ];

        $this->db->insert('spares_customers', $insert);
        $customer_id = $this->db->insert_id();

        // 🔹 RESPONSE (STRICT FORMAT)
        echo json_encode([
            "status" => true,
            "data" => [
                "id" => (string)$customer_id,
                "company_name" => $company,
                "email" => $email,
                "address" => $address,
                "contact" => $phone,
                "country_id" => (string)$country
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }
}

public function get_customer_details_api()
{
    header('Content-Type: application/json');

    try {

        // 🔥 GET RAW JSON
        $input = json_decode(file_get_contents('php://input'), true);

        // 🔥 FALLBACK
        if (!$input) {
            $input = $_POST;
        }

        // 🔹 INPUTS
        $customer_id   = isset($input['customer_id']) ? $input['customer_id'] : null;
        $source_type   = isset($input['source_type']) ? $input['source_type'] : null;

        $user_id       = isset($input['user_id']) ? $input['user_id'] : null;
        $role_id       = isset($input['role_id']) ? $input['role_id'] : null;
        $department_id = isset($input['department_id']) ? $input['department_id'] : null;

        // 🔹 VALIDATION
        if (empty($customer_id) || !is_numeric($customer_id)) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid Customer ID"
            ]);
            return;
        }

        if (empty($source_type)) {
            echo json_encode([
                "status" => false,
                "message" => "source_type required"
            ]);
            return;
        }

        // 🔥 SWITCH TABLE BASED ON SOURCE
        if ($source_type == "spares") {

            $customer = $this->db
                ->select("
                    customer_id AS id,
                    company_name,
                    IFNULL(email, '') AS email,
                    IFNULL(address, '') AS address,
                    IFNULL(contact_person_no, '') AS contact,
                    IFNULL(country_id, '') AS country_id
                ")
                ->from('spares_customers')
                ->where('customer_id', $customer_id)
                ->limit(1)
                ->get()
                ->row_array();

        } else if ($source_type == "marketing") {

            $customer = $this->db
                ->select("
                    id,
                    company_name,
                    IFNULL(email, '') AS email,
                    IFNULL(address, '') AS address,
                    IFNULL(contact_no, '') AS contact,
                    IFNULL(country, '') AS country_id
                ")
                ->from('customer_detail')
                ->where('id', $customer_id)
                ->limit(1)
                ->get()
                ->row_array();

        } else {

            echo json_encode([
                "status" => false,
                "message" => "Invalid source_type"
            ]);
            return;
        }

        // 🔹 RESPONSE
        if (!empty($customer)) {

            echo json_encode([
                "status" => true,
                "data" => $customer,
                "meta" => [
                    "source_type" => $source_type,
                    "user_id" => $user_id,
                    "role_id" => $role_id,
                    "department_id" => $department_id
                ]
            ]);

        } else {

            echo json_encode([
                "status" => false,
                "message" => "Customer not found"
            ]);
        }

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Server error",
            "error" => $e->getMessage()
        ]);
    }
}


public function save_service_opportunity_api()
{
    header('Content-Type: application/json');

    try {

        // 🔥 GET RAW JSON INPUT
        $input = json_decode(file_get_contents('php://input'), true);

        // 🔥 FALLBACK (important for form-data)
        if (!$input) {
            $input = $_POST;
        }

        // 🔹 READ INPUTS
        $op_no      = $this->generateOppNo($input['op_type']);
        $op_type    = $input['op_type'] ?? '';
        $customer   = $input['customer'] ?? '';
        $marketing  = $input['marketing'] ?? '';
        $source     = $input['lsource'] ?? '';
        $address    = $input['address'] ?? '';
        $contact    = $input['customercontactno'] ?? '';
        $email      = $input['customeremailid'] ?? '';
        $remarks    = $input['remarks'] ?? '';
        $op_date=date('Y-m-d');

        $user_id    = $input['user_id'] ?? 0; // 🔥 from app

        // 🔹 VALIDATION
        if (empty($op_date)) {
            echo json_encode(["status" => false, "message" => "Opportunity date required"]);
            return;
        }

        if (empty($source)) {
            echo json_encode(["status" => false, "message" => "Source required"]);
            return;
        }

        if (empty($op_type)) {
            echo json_encode(["status" => false, "message" => "Type required"]);
            return;
        }

        if (empty($customer)) {
            echo json_encode(["status" => false, "message" => "Customer required"]);
            return;
        }

        if (empty($remarks)) {
            echo json_encode(["status" => false, "message" => "Remarks required"]);
            return;
        }

        // 🔹 PREPARE DATA
        $data = [
            'op_no'               => $op_no,
            'op_date'             => date('Y-m-d'),
            'op_type'             => $op_type,
            'customer_id'         => $customer,
            'marketing_person_id' => $marketing,
            'source_id'           => $source,
            'customer_address'    => $address,
            'customer_contact_no' => $contact,
            'customer_email'      => $email,
            'remarks'             => $remarks,
            'current_stage_id'    => 1,
            'status'              => 'Open',
            'created_by'          => $user_id,
            'created_at'          => date('Y-m-d H:i:s')
        ];

        // 🔹 TRANSACTION
        $this->db->trans_start();
        $this->db->insert('service_opportunities', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        // 🔹 RESPONSE
        if ($this->db->trans_status() === FALSE) {

            echo json_encode([
                "status" => false,
                "message" => "Database error"
            ]);

        } else {

            echo json_encode([
                "status" => true,
                "message" => "Service Opportunity created successfully",
                "data" => [
                    "id" => $insert_id,
                    "op_no" => $op_no
                ]
            ]);
        }

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Server error",
            "error" => $e->getMessage()
        ]);
    }
}


public function generateOppNo($op_type=null,$flag=0) {
    if($op_type==null)
    {
    $op_type = $this->input->post('op_type'); // 1 = Domestic, 2 = International
    $flag = $this->input->post('flag'); // 1 = Domestic, 2 = International
    }else
    {
        $op_type=$op_type;
        $flag=$flag;
    }

    // 1. Financial Year
    if (date('m') >= 4) { 
        $financial_year = date('y') . '-' . (date('y') + 1);
    } else { 
        $financial_year = (date('y') - 1) . '-' . date('y');
    }

    // 2. Prefix (only for display, NOT for sequence logic)
    $type_prefix = ($op_type == '1') ? "DOM" : "EXP";

    // 3. Get LAST record of this financial year (ignore DOM/EXP)
    $this->db->select('op_no');
    $this->db->from('service_opportunities');
    $this->db->like('op_no', "/$financial_year", 'before'); // only FY filter
    $this->db->order_by('opportunity_id', 'DESC');
    $this->db->limit(1);

    $query = $this->db->get();

    $last_number = 0;

    if ($query->num_rows() > 0) {
        $row = $query->row();
        $last_opp_no = $row->op_no;

        // Example: SPM/EXP/0005/26-27 OR SPM/DOM/0004/26-27
        $parts = explode('/', $last_opp_no);

        if (isset($parts[2])) {
            $last_number = (int)$parts[2];
        }
    }

    // 4. Increment globally
    $next_number = $last_number + 1;

    // 5. Pad
    $padded_number = str_pad($next_number, 4, '0', STR_PAD_LEFT);

    // 6. Final format
    $formatted_opp_no = "SPM/" . $type_prefix . "/" . $padded_number . "/" . $financial_year;
    if($flag==1)
    {
    echo $formatted_opp_no;
    }else
    {
        return $formatted_opp_no;
    }   
}


public function service_opportunity_list_api()
{
    header('Content-Type: application/json');

    try {

        $input = json_decode(file_get_contents("php://input"), true);

        $user_id  = $input['user_id'] ?? null;
        $stage_id = $input['stage_id'] ?? null;
        $role_id  = $input['role_id'] ?? null;
        $followup_type  = $input['followup_type'] ?? null;

        /// 🔥 QUERY
        $this->db->select("
            so.opportunity_id, 
            so.op_no, 
            so.op_date, 
            so.op_type, 
            so.probability,
            so.customer_table_origin,
            so.customer_contact_no,
            so.customer_name,

            IFNULL(cm_spares.company_name, cm_marketing.company_name) as company_name,

            /* 🔥 NAME BASED ON ORIGIN */
            CASE 
                WHEN so.customer_table_origin = 'spares' 
                    THEN cm_spares.contact_person
                ELSE cm_marketing.customer_name
            END as contact_name,

            CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,

            so.current_stage_id,
            sls.stage_name as current_stage_name,

            sph.remarks as latest_remarks,
            sph.next_follow_date,

            (SELECT id 
             FROM service_quotations 
             WHERE opportunity_id = so.opportunity_id 
             ORDER BY id DESC LIMIT 1) as latest_quote_id
        ");

        $this->db->from('service_opportunities so');

        /// 🔥 LATEST PROGRESS HISTORY
        $this->db->join("
            (
                SELECT p1.*
                FROM service_progress_history p1
                INNER JOIN (
                    SELECT opportunity_id, MAX(history_id) as max_id
                    FROM service_progress_history
                    GROUP BY opportunity_id
                ) p2 ON p1.history_id = p2.max_id
            ) sph
        ", "sph.opportunity_id = so.opportunity_id", "left");

        /// 🔥 JOINS
        $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
        $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
        $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
        $this->db->join('service_lead_stages sls', 'sls.stage_id = so.current_stage_id', 'left');

        /// 🔥 FILTERS
        if ($role_id != 12 && $role_id != 41 && $user_id!=111 ) {
            if (!empty($user_id)) {
                $this->db->where('so.marketing_person_id', $user_id);
            }
        }

        if (!empty($stage_id)) {
            $this->db->where('so.current_stage_id', $stage_id);
        }

        // ================= FOLLOWUP FILTER =================
if (!empty($followup_type)) {

    if ($followup_type == 1) {
        // TODAY
        $this->db->where("DATE(sph.next_follow_date) =", date('Y-m-d'));
    }

    elseif ($followup_type == 2) {
        // MISSED
        $this->db->where("DATE(sph.next_follow_date) <", date('Y-m-d'));
    }

    elseif ($followup_type == 3) {
        // UPCOMING
        $this->db->where("DATE(sph.next_follow_date) >", date('Y-m-d'));
    }
}

        $this->db->order_by('so.opportunity_id', 'DESC');

        $result = $this->db->get()->result_array();

        /// 🔥 FORMAT RESPONSE
        foreach ($result as &$row) {

            $row['opportunity_id'] = (int)$row['opportunity_id'];

            $row['latest_quote_id'] = !empty($row['latest_quote_id'])
                ? (int)$row['latest_quote_id']
                : null;

            /// TYPE LABEL
            $row['op_type_label'] = ($row['op_type'] == 1)
                ? "Domestic"
                : "International";

            /// UI FIELDS
            $row['company']   = $row['company_name'] ?? '';
            $row['contact']   = $row['customer_name'] ?? '';

            /// 🔥 PHONE ONLY FROM SERVICE OPPORTUNITIES
            $row['contact_no'] = $row['customer_contact_no'] ?? '';

            $row['assigned']  = $row['marketing_person_name'] ?? '';
            $row['stage']     = $row['current_stage_name'] ?? '';

            /// FOLLOWUP DATE
            $row['date'] = !empty($row['next_follow_date'])
                ? date('d-m-Y', strtotime($row['next_follow_date']))
                : '';

            /// REMARKS
            $remarks = $row['latest_remarks'] ?? '';

            $row['remarks_short'] = strlen($remarks) > 80
                ? substr($remarks, 0, 80) . '...'
                : $remarks;

            $row['remarks_full'] = $remarks;

            $row['opp'] = $row['op_no'];

            /// FLAGS
            $row['can_update'] = true;
            $row['can_quote']  = !empty($row['latest_quote_id']);
            $row['quote_id']  = $row['latest_quote_id'];
            $row['can_send_intro'] = true;

            /// OPTIONAL
            $row['address'] = "";
            $row['product'] = "";
            $row['machine'] = "";
        }

        echo json_encode([
            "status" => true,
            "data"   => $result
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }
}




public function opportunity_detail_api()
{
    $this->output->set_content_type('application/json');

    $input = json_decode(file_get_contents("php://input"), true);
    $id = $input['opportunity_id'] ?? null;

    if (empty($id) || !is_numeric($id)) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid opportunity ID"
        ]);
        return;
    }

    /// 🔥 MAIN QUERY (UPDATED)
    $this->db->select("
        so.opportunity_id,
        so.op_no,
        so.op_type,
        so.customer_contact_no,
        so.customer_name,

        IFNULL(cm_spares.company_name, cm_marketing.company_name) as company_name,

        CASE 
            WHEN so.customer_table_origin = 'spares' 
                THEN cm_spares.contact_person
            ELSE cm_marketing.customer_name
        END as contact_name,

        CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,

        so.current_stage_id,
        sls.stage_name as current_stage_name,

        sph.remarks,
        sph.next_follow_date,
        sph.added_on,
        u2.first_name as added_by_fname,
        u2.last_name as added_by_lname
    ");

    $this->db->from('service_opportunities so');

    /// 🔥 LATEST HISTORY JOIN
    $this->db->join("
        (
            SELECT p1.*
            FROM service_progress_history p1
            INNER JOIN (
                SELECT opportunity_id, MAX(history_id) as max_id
                FROM service_progress_history
                GROUP BY opportunity_id
            ) p2 ON p1.history_id = p2.max_id
        ) sph
    ", "sph.opportunity_id = so.opportunity_id", "left");

    /// 🔥 JOIN USER FOR ADDED BY
    $this->db->join('system_users u2', 'u2.user_id = sph.added_by', 'left');

    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
    $this->db->join('service_lead_stages sls', 'sls.stage_id = so.current_stage_id', 'left');

    $this->db->where('so.opportunity_id', $id);

    $opportunity = $this->db->get()->row();

    $latest_history_id = null;

$latest_history = $this->db
    ->select('history_id')
    ->where('opportunity_id', $id)
    ->order_by('history_id', 'DESC')
    ->limit(1)
    ->get('service_progress_history')
    ->row();

if ($latest_history) {
    $latest_history_id = $latest_history->history_id;
}


$latest_files = [];

if ($latest_history_id) {

    $filesRaw = $this->db
        ->where('service_progress_history_id', $latest_history_id)
        ->get('service_progress_files')
        ->result();

    foreach ($filesRaw as $f) {
        $latest_files[] = [
            "file_name" => $f->file_name,
            "file_url"  => page_url1.'uploads/service_opp/' . $f->file_name
        ];
    }
}

    if (!$opportunity) {
        echo json_encode([
            "status" => false,
            "message" => "Opportunity not found"
        ]);
        return;
    }

    /// FLAGS
    $remarksLower = strtolower($opportunity->remarks ?? '');
    $is_rejected = (strpos($remarksLower, 'rejected') !== false);
    $is_approved = (strpos($remarksLower, 'approved') !== false);

    /// NEXT STAGES
    if ($opportunity->current_stage_id == 8) {
        if ($is_rejected) {
            $next_stages = [
                ["id" => 4, "name" => "Revised Quotation","access"=>0]
            ];
        } else {
            $next_stages = [
                ["id" => 5, "name" => "Quotation Shared & Followup","access"=>1]
            ];
        }
    } else {
        $stages = $this->Service_model
            ->get_next_stages($opportunity->current_stage_id);

        $next_stages = [];

        foreach ($stages as $s) {
            $next_stages[] = [
                "id" => (int)$s['lead_id'],
                "name" => $s['lead_name'],
                "access"=>$s['app_access']
            ];
        }
    }

    /// 🔥 FORMAT DATE + ADDED BY
    $updated_date = !empty($opportunity->added_on)
        ? date('d-m-Y H:i', strtotime($opportunity->added_on))
        : "";

    $added_by = trim(
        ($opportunity->added_by_fname ?? '') . ' ' .
        ($opportunity->added_by_lname ?? '')
    );

    /// FINAL RESPONSE
    echo json_encode([
    "status" => true,
    "data" => [

        "id" => (int)$opportunity->opportunity_id,
        "op_no" => $opportunity->op_no,

        "company_name" => $opportunity->company_name,
        "contact_name" => $opportunity->customer_name,
        "mobile" => $opportunity->customer_contact_no,
        "marketing_person" => ucwords(strtolower($opportunity->marketing_person_name)),

        "type" => (int)$opportunity->op_type,

        "current_stage_id" => (int)$opportunity->current_stage_id,
        "current_stage_name" => $opportunity->current_stage_name,

        "remarks" => $opportunity->remarks,
        "updated_date" => $updated_date,
        "updated_by" => $added_by,

        "followup_date" => $opportunity->next_follow_date ?? "",

        /// 🔥 ADD THIS
        "files" => $latest_files,

        "is_rejected" => $is_rejected,
        "is_approved" => $is_approved,

        "next_stages" => $next_stages
    ]
]);
}

public function service_opportunity_timeline_api()
{
    header('Content-Type: application/json');

    try {

        $input = json_decode(file_get_contents("php://input"), true);
        $opportunity_id = $input['opportunity_id'] ?? null;

        if (empty($opportunity_id) || !is_numeric($opportunity_id)) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid opportunity ID"
            ]);
            return;
        }

        $this->load->model('Service_model');

        $historyRaw = $this->Service_model
            ->get_opportunity_history($opportunity_id);

        $history = [];

        if (!empty($historyRaw)) {

            foreach ($historyRaw as $item) {

                /// 🔥 GET FILES FOR THIS HISTORY ID
                $filesRaw = $this->db
                    ->where('service_progress_history_id', $item->history_id)
                    ->get('service_progress_files')
                    ->result();

                $files = [];

                if (!empty($filesRaw)) {
                    foreach ($filesRaw as $f) {
                        $files[] = [
                            "file_name" => $f->file_name,
                            "file_url"  => page_url1.'uploads/service_opp/'.$f->file_name
                        ];
                    }
                }

                $history[] = [

                    "stage_name" => $item->stage_name,
                    "remarks"    => $item->remarks,

                    /// OLD (SAFE)
                    "added_by" => $item->added_by_name,
                    "date"     => !empty($item->added_on)
                        ? date('d-m-Y H:i', strtotime($item->added_on))
                        : "",

                    /// NEW
                    "updated_by" => $item->added_by_name,
                    "updated_date" => !empty($item->added_on)
                        ? date('d-m-Y H:i', strtotime($item->added_on))
                        : "",

                    "followup_date" => !empty($item->next_follow_date)
                        ? date('d-m-Y', strtotime($item->next_follow_date))
                        : "",

                    /// 🔥 ATTACH FILES HERE
                    "files" => $files
                ];
            }
        }

        echo json_encode([
            "status" => true,
            "data"   => $history
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {

        echo json_encode([
            "status"  => false,
            "message" => "Something went wrong",
            "error"   => $e->getMessage()
        ]);
    }
}




public function update_service_opportunity_post_json()
{
    header('Content-Type: application/json');

    try {

        // ================= GET FORM DATA =================
        $input = $this->input->post();

        if (empty($input)) {
            return $this->_json(false, 'Invalid form data');
        }

        // ================= USER =================
        $user_id = !empty($input['user_id']) ? (int)$input['user_id'] : 0;

        if ($user_id <= 0) {
            return $this->_json(false, 'Invalid user');
        }

        // ================= VALIDATION =================
        $required = ['opportunity_id', 'stage_id'];

        foreach ($required as $field) {
            if (empty($input[$field])) {
                return $this->_json(false, "$field is required");
            }
        }

        $opportunity_id = (int)$input['opportunity_id'];
        $stage_id       = (int)$input['stage_id'];
        $remarks        = trim($input['remarks'] ?? '');
        $followup_date  = !empty($input['next_date'])
            ? date('Y-m-d', strtotime($input['next_date']))
            : null;

        // ================= STAGE VALIDATION =================
        $stage = $this->db
            ->where('stage_id', $stage_id)
            ->get('service_lead_stages')
            ->row();

        if (!$stage) {
            return $this->_json(false, 'Invalid stage selected');
        }

        $new_prob = $stage->default_probability ?? 0;

        // ================= TRANSACTION =================
        $this->db->trans_begin();

        // ================= UPDATE OPPORTUNITY =================
        $this->db->where('opportunity_id', $opportunity_id)
            ->update('service_opportunities', [
                'current_stage_id' => $stage_id,
                'probability'      => $new_prob,
                'remarks'          => $remarks,
                'updated_by'       => $user_id,
                'updated_date'     => date('Y-m-d H:i:s'),
            ]);

        if ($this->db->affected_rows() < 0) {
            throw new Exception('Failed to update opportunity');
        }

        // ================= INSERT HISTORY =================
        $this->db->insert('service_progress_history', [
            'opportunity_id'   => $opportunity_id,
            'stage_id'         => $stage_id,
            'remarks'          => $remarks,
            'next_follow_date' => $followup_date,
            'added_by'         => $user_id,
            'added_on'         => date('Y-m-d H:i:s'),
        ]);

        $history_id = $this->db->insert_id();

        if (!$history_id) {
            throw new Exception('Failed to create history');
        }

        // ================= FILE UPLOAD =================
        if (!empty($_FILES['files'])) {

            $files = $_FILES['files'];
            $upload_path = FCPATH . 'uploads/service_opp/';

            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
            $max_size = 5 * 1024 * 1024; // 5MB

            for ($i = 0; $i < count($files['name']); $i++) {

                if ($files['error'][$i] != 0) continue;

                $original_name = $files['name'][$i];
                $size          = $files['size'][$i];
                $tmp           = $files['tmp_name'][$i];

                // ===== SIZE CHECK =====
                if ($size > $max_size) continue;

                // ===== EXTENSION CHECK =====
                $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_ext)) continue;

                // ===== SAFE FILE NAME =====
                $clean_name = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $original_name);
                $file_name  = uniqid() . '_' . $clean_name;

                // ===== MOVE FILE =====
                if (move_uploaded_file($tmp, $upload_path . $file_name)) {

                    $this->db->insert('service_progress_files', [
                        'opportunity_id'               => $opportunity_id,
                        'service_progress_history_id' => $history_id,
                        'file_name'                   => $file_name,
                        'created_at'                  => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // ================= COMMIT =================
        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Transaction failed');
        }

        $this->db->trans_commit();

        return $this->_json(true, 'Opportunity updated successfully', [
            'history_id' => $history_id
        ]);

    } catch (Exception $e) {

        $this->db->trans_rollback();

        return $this->_json(false, $e->getMessage());
    }
}


private function _json($status, $message, $data = [])
{
    echo json_encode([
        'status'  => $status,
        'message' => $message,
        'data'    => $data
    ]);
    exit;
}

public function get_quote_pdf_api()
{
    header('Content-Type: application/json');

    try {

        // ================= GET RAW JSON =================
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input)) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid JSON payload"
            ]);
            return;
        }

        $quote_id = $input['quote_id'] ?? null;
        $user_id  = $input['user_id'] ?? null;
        $role_id  = $input['role_id'] ?? null;

        if (empty($quote_id) || !is_numeric($quote_id)) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid Quote ID"
            ]);
            return;
        }

        // ================= FETCH QUOTE =================
        $quote = $this->db
            ->where('id', $quote_id)
            ->get('service_quotations')
            ->row();

        if (!$quote) {
            echo json_encode([
                "status" => false,
                "message" => "Quote not found"
            ]);
            return;
        }

        // ================= FETCH OPPORTUNITY =================
        $opportunity = $this->db
            ->where('opportunity_id', $quote->opportunity_id)
            ->get('service_opportunities')
            ->row();

        // ================= APPROVAL STATUS =================
        $approval_status = "pending";

        if (!empty($opportunity->approval_status)) {
            if ($opportunity->approval_status == "Approved") {
                $approval_status = "approved";
            } elseif ($opportunity->approval_status == "Rejected") {
                $approval_status = "rejected";
            }
        }

        // ================= APPROVAL PERMISSION =================
        $can_approve = false;

        if ($role_id == 12 || $role_id == 41 || $user_id == 111) {
            $can_approve = true;
        }

        // ================= GENERATE PDF VIA CURL =================
        // $generate_url = page_url . "ServiceLeads/view_quotation_pdf/" . $quote_id . "/1";

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $generate_url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // $pdf_content = curl_exec($ch);
        // // echo $pdf_content; exit;
        // if (curl_errno($ch)) {
        //     curl_close($ch);
        //     echo json_encode([
        //         "status" => false,
        //         "message" => "PDF generation failed"
        //     ]);
        //     return;
        // }

        // curl_close($ch);

        // ================= SAVE PDF =================
        // $folder = FCPATH . 'uploads/service_quotations/';
        // if (!is_dir($folder)) {
        //     mkdir($folder, 0777, true);
        // }

         $filename = $quote_id . ".pdf";
        // $file_path = $folder . $filename;

        // file_put_contents($file_path, $pdf_content);

        // ================= FINAL URL =================
        $pdf_url = page_url1.'uploads/service_quotations/' . $filename;

        // ================= RETURN =================
        echo json_encode([
            "status" => true,
            "message" => "Generated successfully",
            "pdf_url" => $pdf_url,
            "can_approve_reject" => $can_approve,
            "approval_status" => $approval_status
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }
}


public function update_quotation_status_api()
{
    header('Content-Type: application/json');

    try {

        // ================= GET RAW JSON =================
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input)) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid JSON payload"
            ]);
            return;
        }

        $opp_id  = $input['opportunity_id'] ?? null;
        $status  = $input['status'] ?? null; // Approved / Rejected
        $remarks = trim($input['remarks'] ?? '');
        $user_id = $input['user_id'] ?? null;

        // ================= VALIDATION =================
        if (empty($opp_id) || !is_numeric($opp_id)) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid opportunity ID"
            ]);
            return;
        }

        if (!in_array($status, ['Approved', 'Rejected'])) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid status"
            ]);
            return;
        }

        // 🔥 REMARKS REQUIRED ONLY FOR REJECTION
        if ($status == 'Rejected' && empty($remarks)) {
            echo json_encode([
                "status" => false,
                "message" => "Remarks required for rejection"
            ]);
            return;
        }

        // ================= PREPARE DATA =================
        $finalRemarks = "Quotation " . $status;

        if ($status == 'Rejected' && !empty($remarks)) {
            $finalRemarks .= ". Reason: " . $remarks;
        }

        // ================= TRANSACTION =================
        $this->db->trans_begin();

        // UPDATE MAIN TABLE
        $this->db->where('opportunity_id', $opp_id)->update('service_opportunities', [
            'current_stage_id' => 8,
            'approval_status'  => $status,
            'remarks'          => $finalRemarks,
            'updated_by'       => $user_id,
            'updated_date'     => date('Y-m-d H:i:s')
        ]);

        // INSERT HISTORY
        $this->db->insert('service_progress_history', [
            'opportunity_id'   => $opp_id,
            'stage_id'         => 8,
            'remarks'          => "STATUS: " . $status . ($remarks ? " | ".$remarks : ""),
            'added_by'         => $user_id,
            'added_on'         => date('Y-m-d H:i:s')
        ]);

        // ================= COMMIT =================
        if ($this->db->trans_status() === FALSE) {
            throw new Exception("Database error");
        }

        $this->db->trans_commit();

        echo json_encode([
            "status" => true,
            "message" => "Quotation " . $status . " successfully"
        ]);

    } catch (Exception $e) {

        $this->db->trans_rollback();

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }
}

public function getEngineerSchedulerMasterData()
 {
    header('Content-Type: application/json');

    try {

        /// 🔥 READ RAW INPUT (optional for future use)
        $input = json_decode(file_get_contents("php://input"), true);

        /// ================= ENGINEERS =================
        $engineers = $this->db->select('user_id, first_name, last_name')
            ->from('system_users')
            ->where('department_id', 22)
            ->where('user_status', 1)
            ->get()
            ->result_array();

        foreach ($engineers as &$e) {
            $e['name'] = trim(ucwords(strtolower($e['first_name'] . ' ' . $e['last_name'])));
        }

        /// ================= WON ORDERS =================
        $this->db->select("
            so.opportunity_id,
            so.op_no,
            IFNULL(cm_spares.company_name, cm_marketing.company_name) as company_name
        ");
        $this->db->from('service_opportunities so');
        $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
        $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
        $this->db->where('so.current_stage_id', 7);

        $orders = $this->db->get()->result_array();

        foreach ($orders as &$o) {
            $o['label'] = trim($o['op_no'] . ' | ' . ucwords(strtolower($o['company_name'])));
        }

        /// ================= DEPLOYMENT TYPES =================
        $deployment_types = [
            ["id" => "Installation", "name" => "New Installation"],
            ["id" => "Breakdown", "name" => "Emergency Breakdown"],
            ["id" => "AMC", "name" => "AMC Service Visit"],
            ["id" => "Training", "name" => "Technical Training"],
        ];

        /// ================= FINAL RESPONSE =================
        echo json_encode([
            "status" => true,
            "message" => "Data fetched successfully",
            "data" => [
                "engineers" => $engineers,
                "orders" => $orders,
                "deployment_types" => $deployment_types
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }
}


public function save_visit_plan_api()
{
    /// 🔒 ALLOW ONLY POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "status" => false,
            "message" => "Invalid request method"
        ]);
        return;
    }

    /// 🔥 GET RAW JSON INPUT
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input)) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid JSON input"
        ]);
        return;
    }

    try {

        /// ================= VALIDATION =================
        if (empty($input['opportunity_id'])) {
            throw new Exception("Opportunity is required");
        }

        if (empty($input['engineer_id'])) {
            throw new Exception("Engineer is required");
        }

        if (empty($input['start_date'])) {
            throw new Exception("Start date is required");
        }

        if (empty($input['end_date'])) {
            throw new Exception("End date is required");
        }

        /// 🔥 DATE VALIDATION
        if (strtotime($input['end_date']) < strtotime($input['start_date'])) {
            throw new Exception("End date cannot be before start date");
        }

        $engineer_id = $input['engineer_id'];
        $start_date  = $input['start_date'];
        $end_date    = $input['end_date'];

        /// ================= 🔥 CONFLICT CHECK =================
        $this->db->where('engineer_id', $engineer_id);
        $this->db->where('start_date <=', $end_date);
        $this->db->where('end_date >=', $start_date);

        $conflict = $this->db->get('service_engineer_visits')->row();

        if ($conflict) {
            throw new Exception("Engineer is already assigned to another visit in this date range");
        }

        /// ================= INSERT DATA =================
        $data = [
            'opportunity_id' => $input['opportunity_id'],
            'engineer_id'    => $engineer_id,
            'start_date'     => $start_date,
            'end_date'       => $end_date,
            'visit_type'     => $input['visit_type'] ?? '',
            'remarks'        => $input['remarks'] ?? '',
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $this->db->insert('service_engineer_visits', $data);

        /// ================= RESPONSE =================
        echo json_encode([
            "status" => true,
            "message" => "Visit scheduled successfully"
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }
}

public function get_visit_pending_count_api()
{
    try {

        $today = date('Y-m-d');

        /// ================= COUNT QUERY =================
        $this->db->from('service_engineer_visits v');

        /// 🔥 STATUS FILTER
        $this->db->where_not_in('v.visit_status', ['Completed', 'Cancelled']);

        /// 🔥 DATE BETWEEN LOGIC (BEST WAY)
        $this->db->where("'$today' BETWEEN v.start_date AND v.end_date", null, false);

        $count = $this->db->count_all_results();

        /// ================= RESPONSE =================
        echo json_encode([
            "status" => true,
            "message" => "Today deployment count fetched",
            "data" => [
                "today_deployments" => $count
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong"
        ]);
    }
}


public function get_active_visits_api()
{
    try {

        $input = json_decode(file_get_contents("php://input"), true);

        $user_id = $input['user_id'] ?? 0;
        $role_id = $input['role_id'] ?? 0;

        /// 🔥 CHECK ADMIN
        $is_admin = ($role_id == 12 || $role_id == 41 || $user_id == 111);

        /// ================= VISITS QUERY =================
        $this->db->select("
            v.*, 
            v.remarks as internal_notes,
            u.first_name as engineer_name, 
            u.last_name as engineer_lname,
            IFNULL(cm_spares.company_name, cm_marketing.company_name) as customer_name
        ");

        $this->db->from('service_engineer_visits v');

        $this->db->join('system_users u', 'u.user_id = v.engineer_id', 'inner');
        $this->db->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id', 'inner');
        $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
        $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');

        /// 🔥 STATUS FILTER
        $this->db->where_not_in('v.visit_status', ['Completed', 'Cancelled']);

        /// 🔥 ENGINEER FILTER PRIORITY
        if (!empty($input['engineer_id'])) {

            // 👉 If filter applied → ALWAYS use it
            $this->db->where('v.engineer_id', $input['engineer_id']);

        } else {

            // 👉 Default behavior
            if (!$is_admin) {
                $this->db->where('v.engineer_id', $user_id);
            }
            // Admin → no restriction
        }

        /// 🔥 DATE FILTERS (for all cases)
        if (!empty($input['from_date'])) {
            $this->db->where('v.start_date >=', $input['from_date']);
        }

        if (!empty($input['to_date'])) {
            $this->db->where('v.end_date <=', $input['to_date']);
        }

        $result = $this->db
            ->order_by('v.start_date', 'DESC')
            ->get()
            ->result_array();


        /// ================= 🔥 ENGINEERS LIST =================
        $engineers = $this->db
            ->select("user_id, CONCAT(
                UPPER(LEFT(first_name,1)), LOWER(SUBSTRING(first_name,2)),
                ' ',
                UPPER(LEFT(last_name,1)), LOWER(SUBSTRING(last_name,2))
            ) as name")
            ->from("system_users")
            ->where("user_status", 1)
            ->where("department_id", 22)
            ->order_by("first_name", "ASC")
            ->get()
            ->result_array();


        /// 🔥 FORMAT DATA (Sentence Case)
        foreach ($result as &$r) {

            $r['engineer_name']  = ucwords(strtolower($r['engineer_name']));
            $r['engineer_lname'] = ucwords(strtolower($r['engineer_lname']));
            $r['customer_name']  = ucwords(strtolower($r['customer_name']));
            $r['internal_notes'] = ucwords(strtolower($r['internal_notes']));
            $r['visit_type']     = ucwords(strtolower($r['visit_type']));
            $r['visit_status']   = ucwords(strtolower($r['visit_status']));
        }


        /// ================= RESPONSE =================
        echo json_encode([
            "status" => true,
            "message" => "Active visits fetched successfully",
            "data" => $result,
            "engineers" => $engineers,
            "is_admin" => $is_admin
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong"
        ]);
    }
}


public function get_user_permissions_api()
{
    /// 🔒 ONLY POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "status" => false,
            "message" => "Invalid request method"
        ]);
        return;
    }

    /// 🔥 RAW JSON INPUT
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input)) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid JSON input"
        ]);
        return;
    }

    try {

        /// ================= VALIDATION =================
        if (empty($input['user_id'])) {
            throw new Exception("User ID is required");
        }

        if (!isset($input['role_id'])) {
            throw new Exception("Role ID is required");
        }

        if (!isset($input['department_id'])) {
            throw new Exception("Department ID is required");
        }

        $user_id = $input['user_id'];
        $role_id = $input['role_id'];
        $department_id = $input['department_id'];

        /// ================= 🔥 PERMISSION LOGIC =================
        $permissions = [

            /// Opportunities
            "opportunities" =>
                ($role_id == 12 ||
                 $department_id == 9 ||
                 in_array($user_id, [161, 139, 61])),

            /// Service CRM
           "service" =>
    ($role_id == 12 ||
     ($department_id == 22 && $user_id == 111) ||
     in_array($user_id, [161, 139, 61])),

     "spare" =>
    ($role_id == 12 ||
     ($department_id == 31 && $user_id == 89) ||
     in_array($user_id, [161, 139, 61])),

            /// Always allowed (for now)
            "tasks" => true,
            "df"    => true,
            "chat"  => true,
            "quote_approval" => (
    $role_id == 12 ||
    in_array($user_id, [139, 161])
),
    "visit" => (
    $department_id == 22 && $user_id != 111
    )
        ];

        /// ================= RESPONSE =================
        echo json_encode([
            "status" => true,
            "permissions" => $permissions
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => $e->getMessage()
        ]);
    }
}


public function save_business_card()
{
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input)) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid JSON"
        ]);
        return;
    }

    // 🔍 VALIDATION
    if (empty($input['type'])) {
        echo json_encode([
            "status" => false,
            "message" => "Type is required"
        ]);
        return;
    }

    if (empty($input['exhibition_id'])) {
        echo json_encode([
            "status" => false,
            "message" => "Exhibition missing"
        ]);
        return;
    }

    // 🔥 PREPARE DATA
    $data = [
        "name" => $input['name'] ?? null,
        "company" => $input['company'] ?? null,
        "mobile" => $input['mobile'] ?? null,
        "email" => $input['email'] ?? null,
        "address" => $input['address'] ?? null,
        "remarks" => $input['remarks'] ?? null,
        "exhibition_id" => $input['exhibition_id'],
        "lead_type" => $input['type'],
        "created_by" => $input['user_id'] ?? null,
        "created_at" => date('Y-m-d H:i:s'),
    ];

    // 🚫 DUPLICATE CHECK (optional but recommended)
    if (!empty($data['mobile'])) {
        $this->db->where('mobile', $data['mobile']);
        $existing = $this->db->get('business_card_leads')->row();

        if ($existing) {
            echo json_encode([
                "status" => false,
                "message" => "Lead already exists with this mobile"
            ]);
            return;
        }
    }

    // 💾 INSERT
    $this->db->insert('business_card_leads', $data);

    if ($this->db->affected_rows() > 0) {
        echo json_encode([
            "status" => true,
            "message" => "Saved successfully",
            "insert_id" => $this->db->insert_id()
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "DB insert failed"
        ]);
    }
}



public function exhibition_leads_list()
{
     $rawInput = json_decode(file_get_contents("php://input"), true);
     $role_id=$rawInput['role_id'];
     $department_id=$rawInput['department_id'];
     $user_id=$rawInput['user_id'];
    /// 🔥 CHECK ADMIN
    $is_admin = ($role_id == 12 || $role_id == 41);

    try {

        $this->db->select("
    b.id,
    b.name as customer,
    b.company,
    b.mobile,
    b.address,
    b.lead_type as type,
    e.exhibition as exhibition_name,
   DATE_FORMAT(
    b.scanned_at,
    '%d-%m-%Y'
) as date,
   CONCAT(
  UPPER(LEFT(CONCAT(s.first_name, ' ', s.last_name), 1)),
  LOWER(SUBSTRING(CONCAT(s.first_name, ' ', s.last_name), 2))
) AS full_name
");

        $this->db->from('business_card_leads b');

        /// 🔥 JOIN EXHIBITION
        $this->db->join('exhibition_info e', 'e.id = b.exhibition_id', 'left');
        $this->db->join('system_users s', 's.user_id = b.created_by', 'left');

        /// 🔥 CONDITIONS
       // $this->db->where('b.status', 1);

        /// 🔥 USER FILTER (optional)
        if (!$is_admin) {
            $this->db->where('b.created_by', $user_id);
        }

        /// 🔥 ORDER (latest first)
        $this->db->order_by('b.id', 'DESC');

        $result = $this->db->get()->result_array();

        echo json_encode([
            "status" => true,
            "data" => $result
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong"
        ]);
    }
}


// spare starts 

public function get_spare_pipeline_post()
{
    $rawInput = json_decode(file_get_contents("php://input"), true);
     $role_id=$rawInput['role_id'];
     $department_id=$rawInput['department_id'];
     $user_id=$rawInput['user_id'];

    try {

        $data = $this->Dashboard_model->get_spare_pipeline_countsNew($role_id,$department_id,$user_id);

        echo json_encode([
            "status" => true,
            "data" => $data
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }
}

public function get_spare_followup_counts_post()
{
    $rawInput = json_decode(file_get_contents("php://input"), true);
     $role_id=$rawInput['role_id'];
     $department_id=$rawInput['department_id'];
     $user_id=$rawInput['user_id'];


    try {

        $data = $this->Dashboard_model->get_spare_followup_countsNew($role_id,$user_id,$department_id);

        echo json_encode([
            "status" => true,
            "data" => $data
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }
}


    public function master_data_spares()
{
    header('Content-Type: application/json');

    // 🔥 STEP 1: GET RAW INPUT
    $inputJSON = trim(file_get_contents("php://input"));

    file_put_contents('debug_master.txt', $inputJSON);

    // 🔥 STEP 2: HANDLE EMPTY INPUT
    if (empty($inputJSON)) {
        echo json_encode([
            "status" => false,
            "msg" => "Empty input received"
        ]);
        exit;
    }

    // 🔥 STEP 3: DECODE JSON
    $rawInput = json_decode($inputJSON, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            "status" => false,
            "msg" => "Invalid JSON",
            "error" => json_last_error_msg(),
            "raw" => $inputJSON
        ]);
        exit;
    }

    // 🔥 STEP 4: FETCH VALUES
    $user_id = isset($rawInput['user_id']) ? trim($rawInput['user_id']) : '';
    $role_id = isset($rawInput['role_id']) ? trim($rawInput['role_id']) : '';
    $department_id = isset($rawInput['department_id']) ? trim($rawInput['department_id']) : '';

    // 🔥 FALLBACK
    if (empty($user_id)) {
        $user_id = $this->input->post('user_id');
        $role_id = $this->input->post('role_id');
        $department_id = $this->input->post('department_id');
    }

    // 🔥 VALIDATION
    if (empty($user_id)) {
        echo json_encode([
            "status" => false,
            "msg" => "User ID missing"
        ]);
        exit;
    }

    // 🔥 VERIFY USER
    $user = $this->db->where('user_id', $user_id)->get('system_users')->row();

    if (!$user) {
        echo json_encode([
            "status" => false,
            "msg" => "Invalid user"
        ]);
        exit;
    }

    $data = [];

    // 🔹 SOURCES
    $data['sources'] = $this->db
        ->select('source_id as id, CONCAT(UCASE(LEFT(lead_source,1)), LCASE(SUBSTRING(lead_source,2))) as name')
        ->from('lead_source')
        ->where('status', 1)
        ->get()->result();

    // 🔹 EXHIBITIONS
    $data['exhibitions'] = $this->db
        ->select('id, CONCAT(UCASE(LEFT(exhibition,1)), LCASE(SUBSTRING(exhibition,2))) as name')
        ->from('exhibition_info')
        ->order_by('exhibition', 'asc')
        ->get()->result();

    // 🔹 MARKETING USERS
    $this->db->select('user_id as id,
        CONCAT(
            CONCAT(UCASE(LEFT(first_name,1)), LCASE(SUBSTRING(first_name,2))),
            " ",
            CONCAT(UCASE(LEFT(last_name,1)), LCASE(SUBSTRING(last_name,2)))
        ) as name
    ');
    $this->db->from('system_users');
    $this->db->where('business_location', 2);
    $this->db->where('user_status', 1);

    if ($role_id != 12) {
        $this->db->where('user_id', $user_id);
    }
    $this->db->where_in('department_id','31,22');

    $data['marketing_users'] = $this->db->get()->result();

    // 🔹 COUNTRIES
    $data['countries'] = $this->db
        ->select('country_id as id,
            CONCAT(UCASE(LEFT(country_name,1)), LCASE(SUBSTRING(country_name,2))) as name
        ')
        ->from('countries')
        ->where('country_status', 1)
        ->get()->result();

    // 🔥 FINAL RESPONSE
    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
}




public function get_products_ajax()
{
    $this->output->set_content_type('application/json');

    $input = json_decode(file_get_contents("php://input"), true);
    $searchTerm = $input['searchTerm'] ?? '';

    $this->db->select('id, code, description');
    $this->db->from('spare_parts_for_trading');
    $this->db->where('status', 1);

    if (!empty($searchTerm)) {
        $this->db->group_start();
        $this->db->like('code', $searchTerm);
        $this->db->or_like('description', $searchTerm);
        $this->db->group_end();
    }

    $this->db->limit(50);
    $query = $this->db->get();

    $data = [];

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $part) {

            $data[] = [
                "id" => $part->id,
                "text" => "[" . $part->code . "] - " . trim($part->description)
            ];
        }
    }

    return $this->output->set_output(json_encode([
        "status" => true,
        "message" => "Products fetched",
        "data" => $data
    ]));
}

public function add_Spare_opportunity_api()
{
    $this->output->set_content_type('application/json');

    $user_id = $_SESSION['logged_in']['user_id'] ?? 0;

    $input = json_decode(file_get_contents("php://input"), true);

    /// 🔴 BASIC VALIDATION
    if (
        empty($input['op_date']) ||
        empty($input['lsource']) ||
        empty($input['op_type']) ||
        empty($input['marketing']) ||
        empty($input['customer']) ||
        empty($input['country']) ||
        empty($input['address']) ||
        empty($input['customercontactno']) ||
        empty($input['customeremailid']) ||
        empty($input['customertype']) ||
        empty($input['probability'])
    ) {
        return $this->output->set_output(json_encode([
            "status" => false,
            "message" => "Required fields missing"
        ]));
    }

    /// 🔴 PRODUCT VALIDATION (AT LEAST ONE)
    if (empty($input['products']) || !is_array($input['products'])) {
        return $this->output->set_output(json_encode([
            "status" => false,
            "message" => "At least one product is required"
        ]));
    }

    /// 🔴 CHECK VALID PRODUCT
    $validProducts = array_filter($input['products'], function ($p) {
        return !empty($p['product_id']) && !empty($p['qty']) && $p['qty'] > 0;
    });

    if (count($validProducts) == 0) {
        return $this->output->set_output(json_encode([
            "status" => false,
            "message" => "Invalid product data"
        ]));
    }

    /// 📅 FINANCIAL YEAR
    if (date('m') >= 4) {
        $financial_year = date('y') . '-' . (date('y') + 1);
    } else {
        $financial_year = (date('y') - 1) . '-' . date('y');
    }


    $opp_data=$this->generateWholeOppNo($input['op_type']);
    if ($opp_data === false) {
    // handle error
    }

    $op_no = $opp_data['op_no'];
    $op_increment_no = $opp_data['op_increment_no'];


    /// 🧾 MAIN DATA
    $opportunity_data = [
        'op_date'             => date('Y-m-d'),
        'op_no'               => $op_no ?? "",
        'op_increment_no'     => $op_increment_no ?? "",
        'op_type'             => $input['op_type'],
        'customer_id'         => $input['customer'],
        'country_id'          => $input['country'],
        'customer_address'    => $input['address'],
        'customer_contact_no' => $input['customercontactno'],
        'customer_email'      => $input['customeremailid'],
        'source_id'           => $input['lsource'],
        'exhibition_id'       => ($input['lsource'] == 8) ? ($input['exhibitionname'] ?? null) : null,
        'marketing_person_id' => $input['marketing'],
        'client_type'         => $input['customertype'],
        'probability'         => $input['probability'],
        'remarks'             => $input['remarks'] ?? "",
        
        /// ✅ GST OPTIONAL
        'tax_number'          => $input['gst'] ?? null,

        'financial_year'      => $financial_year,
        'added_by'            => $user_id,
        'created_at'          => date('Y-m-d H:i:s')
    ];

    $this->db->trans_start();

    /// 🧾 INSERT OPPORTUNITY
    $this->db->insert('opportunities', $opportunity_data);
    $opportunity_id = $this->db->insert_id();

    /// 📦 PRODUCTS INSERT
    $product_batch = [];

    foreach ($validProducts as $p) {
        $product_batch[] = [
            'opportunity_id' => $opportunity_id,
            'product_id'     => $p['product_id'],
            'quantity'       => $p['qty']
        ];
    }

    $this->db->insert_batch('opportunity_products', $product_batch);

    /// 📝 REMARK ENTRY
    $this->db->insert('spare_progress_remarks', [
        'lead_id' => $opportunity_id,
        'lead_stage' => 1,
        'next_follow_date' => date('Y-m-d'),
        'remark_title' => 'New Opportunity',
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $user_id
    ]);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        return $this->output->set_output(json_encode([
            "status" => false,
            "message" => "Failed to create opportunity"
        ]));
    }

    return $this->output->set_output(json_encode([
        "status" => true,
        "message" => "Opportunity created successfully",
        "opportunity_id" => $opportunity_id
    ]));
}



public function generateWholeOppNo($op_type)
{
    if (empty($op_type)) {
        return false;
    }

    /// 🔢 GET NEXT INCREMENT NUMBER
    $this->db->select_max('op_increment_no');
    $this->db->from('opportunities');
    $this->db->where('op_type', $op_type);

    $row = $this->db->get()->row();

    $next_number = ($row && $row->op_increment_no)
        ? $row->op_increment_no + 1
        : 1;

    /// 📌 PREFIX + TYPE
    $prefix = 'SPM/SPARES/';
    $typeString = ($op_type == 1) ? 'DOM' : 'EXP';

    /// 📅 FINANCIAL YEAR
    $month = date('n');
    $year = date('Y');

    if ($month >= 4) {
        $financial_year = substr($year, -2) . '-' . substr($year + 1, -2);
    } else {
        $financial_year = substr($year - 1, -2) . '-' . substr($year, -2);
    }

    /// 🔢 PAD NUMBER
    $padded_number = str_pad($next_number, 4, '0', STR_PAD_LEFT);

    /// 🎯 FINAL OP NO
    $full_op_no = $prefix . $typeString . '/' . $padded_number . '/' . $financial_year;

    /// ✅ RETURN ARRAY
    return [
        "op_no" => $full_op_no,
        "op_increment_no" => $next_number
    ];
}


public function get_spare_customer_list()
{
    header('Content-Type: application/json');

    /// 🔥 STEP 1: GET RAW INPUT
    $inputJSON = trim(file_get_contents("php://input")); 

    /// 🔥 STEP 2: HANDLE EMPTY INPUT
    if (empty($inputJSON)) {
        echo json_encode([
            "status" => false,
            "msg" => "Empty input received"
        ]);
        exit;
    }

    /// 🔥 STEP 3: DECODE JSON
    $rawInput = json_decode($inputJSON, true);

    $searchTerm = $rawInput['searchTerm'] ?? '';

    /// 🔥 QUERY
    $this->db->select('customer_id, company_name, contact_person');
    $this->db->from('spares_customers');
    $this->db->where('status', '1');

    if (!empty($searchTerm)) {
        $this->db->group_start();
        $this->db->like('company_name', $searchTerm, 'both');
        $this->db->or_like('contact_person', $searchTerm, 'both');
        $this->db->group_end();
    }

    $this->db->limit(50);
    $query = $this->db->get();

    $data = [];

    if ($query->num_rows() > 0) {

        foreach ($query->result() as $row) {

            $text = $row->company_name;

            if (!empty($row->contact_person)) {
                $text .= " - " . $row->contact_person;
            }

            $data[] = [
                'id'   => (string)$row->customer_id,
                'text' => $text
            ];
        }
    }

    /// ✅ RESPONSE
    echo json_encode([
        "status" => true,
        "data"   => $data
    ]);
}


public function getCustomerDetails_Spare_frommaster()
{
    header('Content-Type: application/json');

    // ✅ Read RAW JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $customer_id = isset($input['custid']) ? $input['custid'] : '';

    // ✅ Validation
    if (empty($customer_id)) {

        echo json_encode([
            'status' => false,
            'message' => 'Customer ID missing'
        ]);

        return;
    }

    // ✅ Fetch customer details
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

    // ✅ Data found
    if ($query->num_rows() > 0) {

        $row = $query->row();

        echo json_encode([
            'status' => true,
            'data' => [
                'address' => $row->address,
                'tax_number' => $row->tax_number,
                'contact_person_no' => $row->contact_person_no,
                'email' => $row->email,
                'country_id' => $row->country_id,
                'brand_name' => $row->brand_name,
                'brand_id' => $row->brand_id
            ]
        ]);

    } else {

        // ❌ No data found
        echo json_encode([
            'status' => false,
            'message' => 'Customer not found'
        ]);
    }
}


public function add_new_spare_customer()
{
    header('Content-Type: application/json');

    // ✅ READ RAW JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // ✅ FETCH VALUES
    $company            = trim($input['company_name'] ?? '');
    $country            = trim($input['country_id'] ?? '');
    $brand_input        = trim($input['brand'] ?? '');
    $email              = trim($input['email'] ?? '');
    $address            = trim($input['address'] ?? '');
    $contact_person     = trim($input['contact_person'] ?? '');
    $contact_no         = trim($input['contact_no'] ?? '');
    $alternate_contact  = trim($input['alternate_contact_no'] ?? '');

    // ✅ VALIDATION
    if (empty($company)) {
        echo json_encode([
            'status' => false,
            'message' => 'Company Name is required'
        ]);
        return;
    }

    if (empty($country)) {
        echo json_encode([
            'status' => false,
            'message' => 'Country is required'
        ]);
        return;
    }


    if (empty($email)) {
    echo json_encode([
        'status' => false,
        'message' => 'Email is required'
    ]);
    return;
}

if (empty($address)) {
    echo json_encode([
        'status' => false,
        'message' => 'Address is required'
    ]);
    return;
}

if (empty($contact_person)) {
    echo json_encode([
        'status' => false,
        'message' => 'Contact Person is required'
    ]);
    return;
}

if (empty($contact_no)) {
    echo json_encode([
        'status' => false,
        'message' => 'Contact Number is required'
    ]);
    return;
}



    /// 🔥 OPTIONAL NOW (SINCE YOU REMOVED BRAND IN FLUTTER)
    $brand_id_to_save = null;

    // if (!empty($brand_input)) {

    //     if (is_numeric($brand_input)) {

    //         $brand_id_to_save = (int)$brand_input;

    //     } else {

    //         $brand_name = trim($brand_input);

    //         $this->db->select('id');
    //         $this->db->from('spare_company_brand');
    //         $this->db->where('name', $brand_name);

    //         $query = $this->db->get();

    //         if ($query->num_rows() > 0) {

    //             $brand_id_to_save = $query->row()->id;

    //         } else {

    //             $this->db->insert('spare_company_brand', [
    //                 'name' => $brand_name
    //             ]);

    //             $brand_id_to_save = $this->db->insert_id();
    //         }
    //     }
    // }

    // ✅ INSERT DATA
    $data = array(
        'company_name'         => $company,
       // 'brand_id'             => $brand_id_to_save,
        'country_id'           => $country,
        'email'                => $email,
        'address'              => $address,
        'contact_person'       => $contact_person,
        'contact_person_no'    => $contact_no,
        'alternate_contact_no' => $alternate_contact,
        'status'               => 1,
        'created_at'           => date('Y-m-d H:i:s')
    );

    // ✅ INSERT
    if ($this->db->insert('spares_customers', $data)) {

        $insert_id = $this->db->insert_id();

        echo json_encode([
            'status' => true,
            'message' => 'Customer added successfully',
            'customer_id' => $insert_id
        ]);

    } else {

        echo json_encode([
            'status' => false,
            'message' => 'Failed to save customer'
        ]);
    }
}

public function add_new_spare_part()
{
    header('Content-Type: application/json');

    // ✅ READ RAW JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // ✅ FETCH VALUES
    $code        = trim($input['part_code'] ?? '');
    $description = trim($input['description'] ?? '');
    $price       = trim($input['price'] ?? '');
    $user_id       = trim($input['user_id'] ?? '');

    // ✅ VALIDATION
    if (empty($code)) {

        echo json_encode([
            'status' => false,
            'message' => 'Part Code is required'
        ]);

        return;
    }

    if (empty($description)) {

        echo json_encode([
            'status' => false,
            'message' => 'Description is required'
        ]);

        return;
    }

    if (empty($price)) {

        echo json_encode([
            'status' => false,
            'message' => 'Price is required'
        ]);

        return;
    }

    // ✅ INSERT DATA
    $data = array(
        'code'        => $code,
        'description' => $description,
        'price'       => $price,
        'added_by'    => $user_id,
        'added_on'    => date('Y-m-d H:i:s')
    );

    // ✅ INSERT
    if ($this->db->insert('spare_parts', $data)) {

        $insert_id = $this->db->insert_id();

        echo json_encode([
            'status' => true,
            'message' => 'Spare part added successfully',
            'product_id' => $insert_id,
            'product_name' => $code . ' - ' . $description
        ]);

    } else {

        echo json_encode([
            'status' => false,
            'message' => 'Failed to save spare part'
        ]);
    }
}


public function spare_opportunity_list_api()
{
    header('Content-Type: application/json');

    // ✅ READ RAW JSON
    $input = json_decode(file_get_contents('php://input'), true);

    $stage_id       = $input['stage_id'] ?? 0;
    $followup_type  = $input['followup_type'] ?? null; // 1=Today,2=Missed,3=Upcoming
    $user_id        = $input['user_id'] ?? 0;
    $role_id        = $input['role_id'] ?? 0;
    $department_id  = $input['department_id'] ?? 0;

    // ✅ SUBQUERIES

    $latest_remark_subquery = "
        (
            SELECT MAX(spr1.id)
            FROM spare_progress_remarks spr1
            WHERE spr1.lead_id = op.opportunity_id
        )
    ";

    $latest_quotation_subquery = "
        (
            SELECT q1.quotation_id
            FROM quotations q1
            WHERE q1.opportunity_id = op.opportunity_id
            ORDER BY q1.quotation_id DESC
            LIMIT 1
        )
    ";

    $stage_filter_subquery = "
        (
            SELECT spr2.lead_stage
            FROM spare_progress_remarks spr2
            WHERE spr2.lead_id = op.opportunity_id
            ORDER BY spr2.id DESC
            LIMIT 1
        )
    ";

    // ✅ MAIN QUERY
    $this->db->select("
        op.opportunity_id,
        op.op_no,
        op.op_date,
        op.op_type,
        op.status,
        op.probability,

        cm.company_name,
        cm.contact_person,
        cm.contact_person_no,

        ls.lead_source,

        CONCAT(u.first_name, ' ', u.last_name)
            as marketing_person_name,

        sls.lead_name as current_stage_name,

        spr.next_follow_date,
        spr.lead_stage,

        {$latest_quotation_subquery}
            as latest_quotation_id
    ", false);

    $this->db->from('opportunities op');

    $this->db->join(
        'spares_customers cm',
        'cm.customer_id = op.customer_id',
        'left'
    );

    $this->db->join(
        'system_users u',
        'u.user_id = op.marketing_person_id',
        'left'
    );

    $this->db->join(
        'lead_source ls',
        'ls.source_id = op.source_id',
        'left'
    );

    $this->db->join(
        'spare_progress_remarks spr',
        "spr.id = {$latest_remark_subquery}",
        'left',
        false
    );

    $this->db->join(
        'spare_lead_stage sls',
        'sls.lead_id = spr.lead_stage',
        'left'
    );

    // ✅ ROLE FILTER
    if ($role_id != 1 && $role_id != 12) {

        $this->db->where(
            'op.marketing_person_id',
            $user_id
        );
    }

    // ✅ STAGE FILTER
    // Apply only if stage_id is not null and not 0

    if (!empty($stage_id) && $stage_id != 0) {

        $this->db->where(
            "{$stage_filter_subquery} = " . (int)$stage_id,
            null,
            false
        );
    }

    // ✅ FOLLOWUP FILTER

    if ($followup_type == 1) {

        // TODAY FOLLOWUP

        $this->db->where(
            "DATE(spr.next_follow_date) = '" . date('Y-m-d') . "'",
            null,
            false
        );

    } elseif ($followup_type == 2) {

        // MISSED FOLLOWUP

        $this->db->where(
            "DATE(spr.next_follow_date) < '" . date('Y-m-d') . "'",
            null,
            false
        );

    } elseif ($followup_type == 3) {

        // UPCOMING FOLLOWUP

        $this->db->where(
            "DATE(spr.next_follow_date) > '" . date('Y-m-d') . "'",
            null,
            false
        );
    }

    // ✅ ORDER

    $this->db->order_by(
        'op.opportunity_id',
        'DESC'
    );

    $query = $this->db->get();

    $rows = [];

    foreach ($query->result() as $row) {

        $current_stage_id = $row->lead_stage;

        // ✅ UPDATE BUTTON

        if ($current_stage_id == 11 || $current_stage_id == 10) {

            $update_btn = false;

        } else {

            $update_btn = true;
        }

        // ✅ QUOTE URL

        $url = "";

        if (!empty($row->latest_quotation_id)) {

            $url = page_url1 .
                'uploads/spare_quotations/' .
                $row->latest_quotation_id .
                ".pdf";
        }

        // ✅ APPROVAL RIGHTS

        $can_approve = false;

        if ($role_id == 12 || $role_id == 41) {

            $can_approve = true;
        }

        // ✅ FOLLOWUP STATUS

        $followup_status = "No Followup";

        if (!empty($row->next_follow_date)) {

            $follow_date = date(
                'Y-m-d',
                strtotime($row->next_follow_date)
            );

            $today = date('Y-m-d');

            if ($follow_date == $today) {

                $followup_status = "Today";

            } elseif ($follow_date < $today) {

                $followup_status = "Missed";

            } elseif ($follow_date > $today) {

                $followup_status = "Upcoming";
            }
        }

        $rows[] = [

            "opportunity_id" =>
                $row->opportunity_id,

            "op_no" =>
                $row->op_no,

            "op_date" =>
                !empty($row->op_date)
                    ? date(
                        'd-m-Y',
                        strtotime($row->op_date)
                    )
                    : '',

            "company_name" =>
                $row->company_name ?? '',

            "customer_name" =>
                $row->contact_person ?? '',

            "mobile" =>
                $row->contact_person_no ?? '',

            "lead_source" =>
                $row->lead_source ?? '',

            "marketing_person_name" =>
                $row->marketing_person_name ?? '',

            "current_stage_name" =>
                $row->current_stage_name ?? '',

            "current_stage_id" =>
                $row->lead_stage,

            "op_type" =>
                ($row->op_type == 1)
                    ? "Domestic"
                    : "Export",

            "status" =>
                $row->status ?? 'Open',

            "probability" =>
                $row->probability ?? 0,

            "latest_quotation_id" =>
                $row->latest_quotation_id,

            "next_follow_date" =>
                !empty($row->next_follow_date)
                    ? date(
                        'd-M-Y',
                        strtotime($row->next_follow_date)
                    )
                    : "",

            "followup_status" =>
                $followup_status,

            "can_quote" =>
                ($row->latest_quotation_id > 0) ? 1 : 0,

            "can_update" =>
                $update_btn,

            "quoteLink" =>
                $url,

            "can_approve" =>
                $can_approve
        ];
    }

    echo json_encode([
        "status" => true,
        "data" => $rows
    ]);
}

// public function get_spare_quote_pdf_api()
// {
//     header('Content-Type: application/json');

//     try {

//         // ================= GET RAW JSON =================
//         $input = json_decode(file_get_contents("php://input"), true);

//         if (empty($input)) {

//             echo json_encode([
//                 "status" => false,
//                 "message" => "Invalid JSON payload"
//             ]);
//             return;
//         }

//         $quote_id = $input['quote_id'] ?? null;
//         $user_id  = $input['user_id'] ?? null;
//         $role_id  = $input['role_id'] ?? null;

//         if (empty($quote_id) || !is_numeric($quote_id)) {

//             echo json_encode([
//                 "status" => false,
//                 "message" => "Invalid Quote ID"
//             ]);
//             return;
//         }

//         // ================= FETCH QUOTE =================
//         $quote = $this->db
//             ->where('quotation_id', $quote_id)
//             ->get('quotations')
//             ->row();

//         if (!$quote) {

//             echo json_encode([
//                 "status" => false,
//                 "message" => "Quote not found"
//             ]);
//             return;
//         }

//         // ================= FETCH OPPORTUNITY =================
//         $opportunity = $this->db
//             ->where('opportunity_id', $quote->opportunity_id)
//             ->get('opportunities')
//             ->row();

//         // ================= APPROVAL STATUS =================
//         $approval_status = "pending";

//         if (!empty($opportunity->approval_status)) {

//             if ($opportunity->approval_status == "Approved") {

//                 $approval_status = "approved";

//             } elseif ($opportunity->approval_status == "Rejected") {

//                 $approval_status = "rejected";
//             }
//         }

//         // ================= APPROVAL PERMISSION =================
//         $can_approve = false;

//         if (
//             $role_id == 12 ||
//             $role_id == 41 
//         ) {
//             $can_approve = true;
//         }

//        // ================= FILE CHECK =================

// $filename =
//     $quote_id.".pdf";

// $file_path =

//     FCPATH.
//     'uploads/spare_quotations/'.
//     $filename;

// /*
// Generate file if missing
// */

// if(
// !file_exists(
// $file_path
// )
// ){

//     $generate_url=

//         page_url1.

//         "index.php/Spares/view_quotation_pdf/".

//         $quote_id;

//     try{

//         /*
//         Hit URL silently
//         */

//         @file_get_contents(
//             $generate_url
//         );

//         /*
//         wait small time
//         */

//         clearstatcache();

//         sleep(1);

//     }catch(Exception $e){

//     }

// }

// /*
// Still missing
// */

// if(
// !file_exists(
// $file_path
// )
// ){

//     echo json_encode([

//         "status"=>false,

//         "message"=>
//         "Unable to generate PDF"

//     ]);

//     return;

// }

//         // ================= PDF URL =================
//         $pdf_url =
//             page_url1 . 'uploads/spare_quotations/' . $filename;

//         // ================= RESPONSE =================
//         echo json_encode([
//             "status" => true,
//             "message" => "Success",
//             "pdf_url" => $pdf_url,
//             "can_approve_reject" => $can_approve,
//             "approval_status" => $approval_status
//         ]);

//     } catch (Exception $e) {

//         echo json_encode([
//             "status" => false,
//             "message" => "Something went wrong",
//             "error" => $e->getMessage()
//         ]);
//     }
// }


public function get_spare_quote_pdf_api()
{
    header('Content-Type: application/json');

    try {

        // ================= GET RAW JSON =================

        $input = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (empty($input)) {

            echo json_encode([

                "status" => false,

                "message" =>
                "Invalid JSON payload"

            ]);

            return;
        }

        $quote_id =
            $input['quote_id']
            ?? null;

        $user_id =
            $input['user_id']
            ?? null;

        $role_id =
            $input['role_id']
            ?? null;

        if (
            empty($quote_id)
            ||
            !is_numeric($quote_id)
        ) {

            echo json_encode([

                "status" => false,

                "message" =>
                "Invalid Quote ID"

            ]);

            return;
        }

        // ================= FETCH QUOTE =================

        $quote =
            $this->db

                ->where(
                    'quotation_id',
                    $quote_id
                )

                ->get(
                    'quotations'
                )

                ->row();

        if (!$quote) {

            echo json_encode([

                "status" => false,

                "message" =>
                "Quote not found"

            ]);

            return;
        }

        // ================= FETCH OPPORTUNITY =================

        $opportunity =
            $this->db

                ->where(
                    'opportunity_id',
                    $quote->opportunity_id
                )

                ->get(
                    'opportunities'
                )

                ->row();

        // ================= APPROVAL STATUS =================

        $approval_status =
            "pending";

        if (
        !empty(
        $opportunity->approval_status
        )) {

            if (
            $opportunity->approval_status
            ==
            "Approved"
            ) {

                $approval_status =
                    "approved";

            }

            elseif (

            $opportunity->approval_status

            ==

            "Rejected"

            ) {

                $approval_status =
                    "rejected";

            }

        }

        // ================= APPROVAL RIGHTS =================

        $can_approve = false;

        if(

        $role_id==12

        ||

        $role_id==41

        ){

            $can_approve=true;

        }

        // ================= FILE CHECK =================

        $filename =
            $quote_id.".pdf";

        $file_path =

            FCPATH.

            'uploads/spare_quotations/'.

            $filename;

        /*
        Generate PDF if missing
        */

        if(
        !file_exists(
        $file_path
        )
        ){

            $generate_url=

                page_url1.

                "index.php/Spares/view_quotation_pdf/".

                $quote_id;

            try{

                $ch=
                curl_init();

                curl_setopt_array(

                    $ch,

                    [

                        CURLOPT_URL =>
                        $generate_url,

                        CURLOPT_RETURNTRANSFER =>
                        true,

                        CURLOPT_FOLLOWLOCATION =>
                        true,

                        CURLOPT_TIMEOUT =>
                        25,

                        CURLOPT_CONNECTTIMEOUT =>
                        10,

                        CURLOPT_SSL_VERIFYPEER =>
                        false,

                        CURLOPT_SSL_VERIFYHOST =>
                        false,

                        CURLOPT_USERAGENT =>
                        "PMS Quote Generator"

                    ]

                );

                curl_exec(
                    $ch
                );

                curl_close(
                    $ch
                );

                clearstatcache();

                usleep(
                    700000
                );

            }catch(Exception $e){

            }

        }

        /*
        still missing
        */

        clearstatcache();

        if(
        !file_exists(
        $file_path
        )
        ){

            echo json_encode([

                "status"=>false,

                "message"=>

                "Unable to generate PDF"

            ]);

            return;

        }

        // ================= PDF URL =================

        $pdf_url=

            page_url1.

            'uploads/spare_quotations/'.

            $filename;

        // ================= RESPONSE =================

        echo json_encode([

            "status" => true,

            "message" =>
            "Success",

            "pdf_url" =>
            $pdf_url,

            "can_approve_reject" =>
            $can_approve,

            "approval_status" =>
            $approval_status

        ]);

    }

    catch(Exception $e){

        echo json_encode([

            "status"=>false,

            "message"=>

            "Something went wrong",

            "error"=>

            $e->getMessage()

        ]);

    }

}

public function spare_opportunity_detail_api()
{
    $this->output->set_content_type('application/json');

    try {

        $input = json_decode(file_get_contents("php://input"), true);

        $id = $input['opportunity_id'] ?? null;

        if (empty($id) || !is_numeric($id)) {

            echo json_encode([
                "status" => false,
                "message" => "Invalid opportunity ID"
            ]);
            return;
        }

        /// ================= MAIN OPPORTUNITY =================

        $opportunity = $this->opportunity_model
            ->get_opportunity_details($id);
           // echo "<pre>"; print_r($opportunity); exit;

        if (!$opportunity) {

            echo json_encode([
                "status" => false,
                "message" => "Opportunity not found"
            ]);
            return;
        }

        /// ================= PRODUCTS =================

        $products = $this->opportunity_model
            ->get_opportunity_products($id);

        $formatted_products = [];

        if (!empty($products)) {

            foreach ($products as $p) {

                $formatted_products[] = [

                    "code" =>
                        $p->code ?? '',

                    "description" =>
                        $p->description ?? '',

                    "quantity" =>
                        $p->quantity ?? 0
                ];
            }
        }

       /// ================= HISTORY =================

$history = $this->opportunity_model
    ->get_opportunity_history($id);

$formatted_history = [];

if (!empty($history)) {

    foreach ($history as $h) {

        /// ===== GET FILES =====

        $this->db->select('
            id,
            file_name
        ');

        $this->db->from(
            'spare_progress_files'
        );

        $this->db->where(
            'service_progress_history_id',
            $h->id
        );

        $files_res =
            $this->db->get()->result();

        $files = [];

        if (!empty($files_res)) {

            foreach ($files_res as $f) {

                $files[] = [

                    "id" =>
                        $f->id,

                    "file_name" =>
                        $f->file_name,

                    "file_url" =>
                        uploadsurl .
                        "spare_progress_files/" .
                        $f->file_name
                ];
            }
        }

        $formatted_history[] = [

            "stage_name" =>
                $h->stage_name ?? '',

            "remarks" =>
                $h->remarks ?? '',

            "added_by_name" =>
                $h->added_by_name ?? '',

            "added_on" =>
                !empty($h->added_on)
                    ? date(
                        'd-M-Y h:i A',
                        strtotime($h->added_on)
                    )
                    : "",

            "next_follow_date" =>
                (
                    !empty($h->next_follow_date) &&
                    $h->next_follow_date != '0000-00-00'
                )
                    ? date(
                        'd-M-Y',
                        strtotime($h->next_follow_date)
                    )
                    : "",

            "files" => $files
        ];
    }
}

        /// ================= QUOTATIONS =================

        $quotations = $this->opportunity_model
            ->get_opportunity_quotations($id);

        $formatted_quotations = [];

        if (!empty($quotations)) {

            foreach ($quotations as $q) {

                $formatted_quotations[] = [

                    "quotation_id" =>
                        $q->quotation_id,

                    "quotation_no" =>
                        $q->quotation_no,

                    "quotation_date" =>
                        !empty($q->quotation_date)
                            ? date(
                                'd-M-Y',
                                strtotime($q->quotation_date)
                            )
                            : "",

                    "total_value" =>
                        $q->total_value,

                    "pdf_url" =>
                        page_url .
                        "Spares/view_quotation_pdf/" .
                        $q->quotation_id
                ];
            }
        }

        /// ================= PURCHASE ORDERS =================

        $this->db->select('
            po_id,
            po_no,
            po_date,
            grand_total,
            po_copy
        ');

        $this->db->from('purchase_orders');

        $this->db->where(
            'opportunity_id',
            $id
        );

        $this->db->order_by(
            'po_id',
            'DESC'
        );

        $purchase_orders =
            $this->db->get()->result();

        $formatted_po = [];

        if (!empty($purchase_orders)) {

            foreach ($purchase_orders as $po) {

                $formatted_po[] = [

                    "po_id" =>
                        $po->po_id,

                    "po_no" =>
                        $po->po_no,

                    "po_date" =>
                        !empty($po->po_date)
                            ? date(
                                'd-M-Y',
                                strtotime($po->po_date)
                            )
                            : "",

                    "grand_total" =>
                        $po->grand_total,

                    "view_so_pdf" =>
                        page_url .
                        "Spares/view_po_pdf/" .
                        $po->po_id,

                    "original_po" =>
                        !empty($po->po_copy)
                            ? uploadsurl .
                                'po_copies/' .
                                $po->po_copy
                            : ""
                ];
            }
        }

        /// ================= SALES ORDERS =================

        $this->db->select('
            order_id,
            order_date,
            order_value,
            po_id
        ');

        $this->db->from('spares_orders');

        $this->db->where(
            'opportunity_id',
            $id
        );

        $this->db->order_by(
            'order_id',
            'DESC'
        );

        $sales_orders =
            $this->db->get()->result();

        $formatted_so = [];

        if (!empty($sales_orders)) {

            foreach ($sales_orders as $so) {

                $formatted_so[] = [

                    "order_id" =>
                        $so->order_id,

                    "so_no" =>
                        "SO-" . $so->po_id,

                    "order_date" =>
                        !empty($so->order_date)
                            ? date(
                                'd-M-Y',
                                strtotime($so->order_date)
                            )
                            : "",

                    "order_value" =>
                        $so->order_value
                ];
            }
        }

        /// ================= NEXT STAGES =================

        $next_stages_raw =
            $this->opportunity_model
                ->get_next_stages(
                    $opportunity->current_stage_id
                );

        $next_stages = [];

        if (!empty($next_stages_raw)) {

            $this->db->select('lead_id');

            $this->db->from('spare_lead_stage');

            $this->db->where(
                'followup_date',
                1
            );

            $stages_req_followup =
                array_column(
                    $this->db
                        ->get()
                        ->result_array(),
                    'lead_id'
                );

            foreach ($next_stages_raw as $stage) {

                $next_stages[] = [

                    "id" =>
                        (int)$stage['lead_id'],

                    "name" =>
                        $stage['lead_name'],

                    "access" =>
                        $stage['app_access'] ?? 1,

                    "followup_date_required" =>
                        in_array(
                            $stage['lead_id'],
                            $stages_req_followup
                        )
                ];
            }
        }

        /// ================= FINAL RESPONSE =================

        echo json_encode([

            "status" => true,

            "data" => [

                /// BASIC
                "opportunity_id" =>
                    (int)$opportunity->opportunity_id,

                "op_no" =>
                    $opportunity->op_no,

                "company_name" =>
                    $opportunity->company_name,

                "customer_name" =>
                    $opportunity->contact_person,

                "mobile" =>
                    $opportunity->customer_contact_no,

                "marketing_person_name" =>
                    $opportunity->marketing_person_name,

                "op_type" =>
                    $opportunity->op_type,

                "probability" =>
                    $opportunity->probability,

                "status" =>
                    $opportunity->status,

                "op_date" =>
                    !empty($opportunity->op_date)
                        ? date(
                            'd-M-Y',
                            strtotime($opportunity->op_date)
                        )
                        : "",

                /// STAGE
                "current_stage_id" =>
                    (int)$opportunity->current_stage_id,

                "current_stage_name" =>
                    $opportunity->current_stage_name,

                /// LAST UPDATE
                "remarks" =>
                    $opportunity->remarks ?? "",

                "followup_date" =>
                    (
                        !empty($opportunity->next_follow_date) &&
                        $opportunity->next_follow_date != '0000-00-00'
                    )
                        ? date(
                            'd-M-Y',
                            strtotime(
                                $opportunity->next_follow_date
                            )
                        )
                        : "",

                /// DATA TABS
                "products" =>
                    $formatted_products,

                "history" =>
                    $formatted_history,

                "quotations" =>
                    $formatted_quotations,

                "purchase_orders" =>
                    $formatted_po,

                "sales_orders" =>
                    $formatted_so,

                "next_stages" =>
                    $next_stages
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }
}



public function spare_opportunity_timeline_api()
{
    $this->output->set_content_type('application/json');

    try {

        $input = json_decode(
            file_get_contents("php://input"),
            true
        );

        $id = $input['opportunity_id'] ?? null;

        if (empty($id) || !is_numeric($id)) {

            echo json_encode([
                "status" => false,
                "message" => "Invalid opportunity ID"
            ]);
            return;
        }

        /// ================= HISTORY =================

        $history = $this->opportunity_model
            ->get_opportunity_history($id);

        $formatted_history = [];

        if (!empty($history)) {

            foreach ($history as $h) {

/// ================= FILES =================

$this->db->select('
    id,
    file_name
');

$this->db->from(
    'spare_progress_files'
);

$this->db->where(
    'service_progress_history_id',
    $h->id
);

$files_data =
    $this->db->get()->result();

$files = [];

if (!empty($files_data)) {

    foreach ($files_data as $f) {

        $files[] = [

            "id" =>
                $f->id,

            "file_name" =>
                $f->file_name,

            "file_url" =>
                base_url(
                    'uploads/spare_progress_files/' .
                    $f->file_name
                )
        ];
    }
}

                $formatted_history[] = [

                    "stage_name" =>
                        $h->stage_name ?? '',

                    "remarks" =>
                        $h->remarks ?? '',

                    "added_by_name" =>
                        $h->added_by_name ?? '',

                    "added_on" =>
                        !empty($h->added_on)
                            ? date(
                                'd-M-Y h:i A',
                                strtotime($h->added_on)
                            )
                            : "",

                    "next_follow_date" =>
                        (
                            !empty($h->next_follow_date) &&
                            $h->next_follow_date != '0000-00-00'
                        )
                            ? date(
                                'd-M-Y',
                                strtotime($h->next_follow_date)
                            )
                            : "",

                    "files" =>$files
                       
                ];
            }
        }

        /// ================= RESPONSE =================

        echo json_encode([

            "status" => true,

            "data" => [

                "opportunity_id" => (int)$id,

                "history" => $formatted_history
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([

            "status" => false,

            "message" => "Something went wrong",

            "error" => $e->getMessage()
        ]);
    }
}


public function update_opportunity_progress_api()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        echo json_encode([
            'status' => false,
            'message' => 'Invalid request method'
        ]);
        return;
    }

    /// ================= USER =================

    $user_id =
        $this->input->post('user_id');



    /// ================= INPUT =================

    $opportunity_id =
        $this->input->post(
            'opportunity_id'
        );

    $new_stage_id =
        $this->input->post(
            'new_stage_id'
        );

    $remarks =
        trim(
            $this->input->post(
                'remarks'
            )
        );

    $followup_date =
        $this->input->post(
            'followup_date'
        );

    date_default_timezone_set(
        "Asia/Kolkata"
    );

    $date = date('Y-m-d H:i:s');

    /// ================= VALIDATION =================

    if (
        empty($opportunity_id) ||
        empty($new_stage_id) ||
        empty($remarks)
    ) {

        echo json_encode([
            'status' => false,
            'message' => 'Required fields missing'
        ]);
        return;
    }

    /// ================= STAGE DETAILS =================

    $new_stage_details =
        $this->db
            ->get_where(
                'spare_lead_stage',
                [
                    'lead_id' =>
                        $new_stage_id
                ]
            )
            ->row();

    if (!$new_stage_details) {

        echo json_encode([
            "status" => false,
            "message" => "Stage not found"
        ]);
        return;
    }

    /// ================= FOLLOWUP CHECK =================

    $is_lost =
        (
            isset(
                $new_stage_details->dead_end
            ) &&
            $new_stage_details->dead_end == 1
        );

    if (
        !$is_lost &&
        empty($followup_date)
    ) {

        echo json_encode([
            'status' => false,
            'message' =>
                'Next follow-up date required'
        ]);
        return;
    }

    /// ================= INSERT REMARK =================

    $remark_data = [

        'lead_id' =>
            $opportunity_id,

        'lead_stage' =>
            $new_stage_id,

        'next_follow_date' =>
            !empty($followup_date)

                ? date(
                    'Y-m-d',
                    strtotime(
                        $followup_date
                    )
                )

                : null,

        'remarks' =>
            $remarks,

        'remark_title' =>
            $new_stage_details->lead_name,

        'added_on' =>
            $date,

        'added_by' =>
            $user_id
    ];

    $this->db->trans_start();

    $this->db->insert(
        'spare_progress_remarks',
        $remark_data
    );

    $insert_id =
        $this->db->insert_id();

    /// ================= FILE UPLOAD =================

    $uploaded_files = [];

    if (
        !empty(
            $_FILES['files']['name'][0]
        )
    ) {

        $this->load->library(
            'upload'
        );

        $upload_path =
            './uploads/spare_progress_files/';

        if (!is_dir($upload_path)) {

            mkdir(
                $upload_path,
                0777,
                true
            );
        }

        foreach (
            $_FILES['files']['name']
            as $key => $name
        ) {

            $clean_name =
                preg_replace(
                    '/[^A-Za-z0-9\.\-_]/',
                    '_',
                    $name
                );

            $_FILES['file']['name'] =
                $clean_name;

            $_FILES['file']['type'] =
                $_FILES['files']['type'][$key];

            $_FILES['file']['tmp_name'] =
                $_FILES['files']['tmp_name'][$key];

            $_FILES['file']['error'] =
                $_FILES['files']['error'][$key];

            $_FILES['file']['size'] =
                $_FILES['files']['size'][$key];

            $config['upload_path'] =
                $upload_path;

            $config['allowed_types'] =
                'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';

            $config['file_name'] =
                time() .
                '_' .
                $clean_name;

            $this->upload->initialize(
                $config
            );

            if (
                $this->upload->do_upload(
                    'file'
                )
            ) {

                $fileData =
                    $this->upload->data();

                $this->db->insert(
                    'spare_progress_files',
                    [

                        'opportunity_id' =>
                            $opportunity_id,

                        'service_progress_history_id' =>
                            $insert_id,

                        'file_name' =>
                            $fileData['file_name'],

                        'created_at' =>
                            $date,

                        'createdBy' =>
                            $user_id
                    ]
                );

                $uploaded_files[] =
                    'uploads/spare_progress_files/' .
                    $fileData['file_name'];

            } else {

                log_message(
                    'error',
                    $this->upload
                        ->display_errors()
                );
            }
        }
    }

    /// ================= STATUS UPDATE =================

    $opportunity_update_data = [];

    if ($new_stage_id == 10) {

        $opportunity_update_data[
            'status'
        ] = 'Won';

        $opportunity_update_data[
            'probability'
        ] = 100;

    } elseif ($is_lost) {

        $opportunity_update_data[
            'status'
        ] = 'Lost';

        $opportunity_update_data[
            'probability'
        ] = 0;
    }

    if (!empty($opportunity_update_data)) {

        $this->opportunity_model
            ->update_main_opportunity_status(
                $opportunity_id,
                $opportunity_update_data
            );
    }

    $this->db->trans_complete();

    /// ================= RESPONSE =================

    if (
        $this->db->trans_status()
        === false
    ) {

        echo json_encode([

            "status" => false,

            "message" =>
                "Database error"
        ]);

    } else {

        echo json_encode([

            "status" => true,

            "message" =>
                "Opportunity progress updated successfully",

            "files_uploaded" =>
                $uploaded_files
        ]);
    }
}

public function update_quotation_status_api_spare()
{
    header('Content-Type: application/json');

    try {

        // ================= GET RAW JSON =================

        $input = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (empty($input)) {

            echo json_encode([
                "status" => false,
                "message" => "Invalid JSON payload"
            ]);

            return;
        }

        $opp_id  = $input['opportunity_id'] ?? null;
        $status  = $input['status'] ?? null; // Approved / Rejected
        $remarks = trim($input['remarks'] ?? '');
        $user_id = $input['user_id'] ?? null;

        // ================= VALIDATION =================

        if (empty($opp_id) || !is_numeric($opp_id)) {

            echo json_encode([
                "status" => false,
                "message" => "Invalid opportunity ID"
            ]);

            return;
        }

        if (!in_array($status, ['Approved', 'Rejected'])) {

            echo json_encode([
                "status" => false,
                "message" => "Invalid status"
            ]);

            return;
        }

        // ✅ REMARKS REQUIRED FOR REJECTION

        if ($status == 'Rejected' && empty($remarks)) {

            echo json_encode([
                "status" => false,
                "message" => "Remarks required for rejection"
            ]);

            return;
        }

        // ================= TRANSACTION =================

        $this->db->trans_begin();

        // ======================================================
        // APPROVED
        // ======================================================

        if ($status == 'Approved') {

            // INSERT REMARK ENTRY

            $insert_data = [

                'lead_id'            => $opp_id,

                'next_follow_date'   => date(
                    'Y-m-d',
                    strtotime('+1 day')
                ),

                'lead_stage'         => 5,

                'remarks'            => 'Quotation Approved',

                'added_on'           => date('Y-m-d H:i:s'),

                'added_by'           => $user_id
            ];

            $this->db->insert(
                'spare_progress_remarks',
                $insert_data
            );
        }

        // ======================================================
        // REJECTED
        // ======================================================

        if ($status == 'Rejected') {

            // INSERT REMARK ENTRY

            $insert_data = [

                'lead_id'            => $opp_id,

                'next_follow_date'   => date(
                    'Y-m-d',
                    strtotime('+1 day')
                ),

                'lead_stage'         => 6,

                'remarks'            => $remarks,

                'added_on'           => date('Y-m-d H:i:s'),

                'added_by'           => $user_id
            ];

            $this->db->insert(
                'spare_progress_remarks',
                $insert_data
            );
        }

        // ======================================================
        // UPDATE OPPORTUNITY
        // ======================================================

        $this->db->where(
            'opportunity_id',
            $opp_id
        );

        $this->db->update(
            'opportunities',
            [
                'approval_status' => $status
            ]
        );

        // ================= CHECK TRANSACTION =================

        if ($this->db->trans_status() === FALSE) {

            throw new Exception("Database error");
        }

        // ================= COMMIT =================

        $this->db->trans_commit();

        $opDetail=$this->getOppNo_UserID($opp_id);
        if(count($opDetail)>0)
        {
            $op_no=$opDetail[0];
            $marketing_person_id=$opDetail[1];
            $company_name=$opDetail[2];
        }
        $this->send_push_notification_for_Approval_Rejection($opp_id,$status,$marketing_person_id,$company_name,$op_no);

        /** INSERT INTO APP_NOTIFICATION **/
        $message  = "Your Quotation. {$op_no} for {$company_name} Has been {$status}";
        $this->InsertAppNotification($marketing_person_id,"Spare Quote Approval Request Response",$message,'quote_approval_result_spare',$opp_id);

        /** END **/

        echo json_encode([
            "status" => true,
            "message" => "Quotation " . $status . " successfully"
        ]);

    } catch (Exception $e) {

        $this->db->trans_rollback();

        echo json_encode([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage()
        ]);
    }


}


public function send_push_notification_for_Approval_Rejection($quote_id,$status,$user_id,$company_name,$op_no)
{
    //echo "hi"; exit;
    // ================= READ RAW JSON =================
    $input = json_decode(file_get_contents("php://input"), true);
   // echo "<pre>"; print_r($input); exit;
    // ================= INPUT =================
    $title    = "Quote ".$status;
    $message  = "Your Quotation. {$op_no} for {$company_name} Has been {$status}";
    $quote_id = $quote_id;

    // ================= VALIDATION =================
    if (empty($quote_id)) {
        echo json_encode([
            "status" => false,
            "msg" => "quote_id required"
        ]);
        return;
    }

    // ================= GET TOKENS =================
    $tokens = $this->db->select('fcm_token')
        ->from('user_devices')
        ->where('fcm_token !=', '')
        ->where('user_id',$user_id)
        ->get()
        ->result_array();

    if (empty($tokens)) {
        echo json_encode([
            "status" => false,
            "msg" => "No tokens"
        ]);
        return;
    }

    $count = 0;
    $responses = [];

    foreach ($tokens as $t) {

        $dataPayload = [
            "type" => "quote_approval_result_spare",
            "quote_id" => (string)$quote_id,
            "screen" => "quote_detail_spare"
        ];

        $res = sendFCM(
            $t['fcm_token'],
            $title,
            $message,
            $dataPayload
        );

        $responses[] = json_decode($res, true);
        $count++;
    }

    // ================= FINAL RESPONSE =================
    echo json_encode([
        "status" => true,
        "sent" => $count,
        "firebase_response" => $responses
    ]);
}

function getOppNo_UserID($opid)
{
    $data=array();
    $rest=$this->db->select('a.op_no,a.marketing_person_id,b.company_name')->from('opportunities a')->join('spares_customers b','a.customer_id=b.customer_id')->where('a.opportunity_id',$opid)->get();
    if($rest->num_rows()>0)
    {
        foreach($rest->result() as $rowwww)
        {
            $data[]=$rowwww->op_no;
            $data[]=$rowwww->marketing_person_id;
            $data[]=ucwords(strtolower($rowwww->company_name));
        }
    }

    return $data;

}

 function InsertAppNotification($user_id,$title,$message,$type,$refrence)
    {
        $data=array(    'user_id'=>$user_id,
                        'title'=>$title,
                        'message'=>$message,
                        'type'=>$type,
                        'reference_id'=>$refrence,
                        'created_at'=>date('Y-m-d H:i:s'));

        $this->db->insert('app_notifications',$data);

    }


    public function mark_chat_read()
{
    /// 🔥 RAW INPUT
    $input = json_decode(file_get_contents("php://input"), true);


    $chat_id = isset($input['chat_id'])
        ? (int)$input['chat_id']
        : 0;

    $user_id = isset($input['chat_user_id'])
        ? (int)$input['chat_user_id']
        : 0;

    $last_message_time =
        $input['last_message_time'] ?? '';

    /// ❌ VALIDATION
    if (
        !$chat_id ||
        !$user_id ||
        empty($last_message_time)
    ) {

        echo json_encode([
            "status" => false,
            "message" => "Invalid parameters"
        ]);

        return;
    }

    /// 🔥 UPDATE READ RECEIPT
    $this->db->where(
        'chat_id',
        $chat_id
    );

    $this->db->where(
        'sender_id !=',
        $user_id
    );

    $this->db->where(
        'created_at <=',
        $last_message_time
    );

    $this->db->update(
        'messages',
        [
            "read_reciept" => 1,
            "read_at"=>date('Y-m-d H:i:s')
        ]
    );

    echo json_encode([
        "status" => true,
        "message" => "Messages marked as read"
    ]);
}


public function send_attachment()
{
    header('Content-Type: application/json');

    try {

        $chat_id       = $this->input->post('chat_id');
        $sender_id     = $this->input->post('sender_id');
        $receiver_id   = $this->input->post('receiver_id');
        $message_type  = $this->input->post('message_type');

        /// ✅ VALIDATION
        if (
            empty($chat_id) ||
            empty($sender_id) ||
            empty($receiver_id) ||
            empty($message_type)
        ) {

            echo json_encode([
                "status" => false,
                "message" => "Required fields missing"
            ]);

            return;
        }

        /// ✅ CHECK FILE
        if (!isset($_FILES['attachment'])) {

            echo json_encode([
                "status" => false,
                "message" => "No file uploaded"
            ]);

            return;
        }

        /// ✅ UPLOAD PATH
        $upload_path =
            FCPATH . 'uploads/chat/';

        if (!is_dir($upload_path)) {

            mkdir(
                $upload_path,
                0777,
                true
            );
        }

        /// ✅ FILE CONFIG
        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = '*';
        $config['max_size']      = 25600; // 25MB
        $config['encrypt_name']  = true;

        $this->load->library(
            'upload',
            $config
        );

        /// ✅ UPLOAD
        if (
            !$this->upload->do_upload(
                'attachment'
            )
        ) {

            echo json_encode([
                "status" => false,
                "message" =>
                    strip_tags(
                        $this->upload->display_errors()
                    )
            ]);

            return;
        }

        /// ✅ FILE DATA
        $upload_data =
            $this->upload->data();

        $file_name =
            $upload_data['file_name'];

        /// ✅ FILE URL
        $file_url =
            page_url1.'uploads/chat/' . $file_name;

        /// ✅ SAVE MESSAGE
        $insert = [

            "chat_id"      => $chat_id,

            "sender_id"    => $sender_id,

            "receiver_id"  => $receiver_id,

            "message"      => $file_url,

            "message_type" => $message_type,

            "created_at"   => date(
                'Y-m-d H:i:s'
            )
        ];

        $this->db->insert(
            'messages',
            $insert
        );

        $message_id =
            $this->db->insert_id();

        /// ✅ UPDATE LAST MESSAGE TABLE
        $last_message = [

            "chat_id" => $chat_id,

            "last_message" => strtoupper(
                $message_type
            ),

            "last_message_time" =>
                date('Y-m-d H:i:s'),
        ];

        /// 🔥 CHECK EXISTS
        $exists =
            $this->db
                ->where(
                    'chat_id',
                    $chat_id
                )
                ->get(
                    'chat_last_message'
                )
                ->row_array();

        if ($exists) {

            $this->db
                ->where(
                    'chat_id',
                    $chat_id
                )
                ->update(
                    'chat_last_message',
                    $last_message
                );

        } else {

            $this->db->insert(
                'chat_last_message',
                $last_message
            );
        }

        /// ✅ RESPONSE
        echo json_encode([

            "status" => true,

            "message" =>
                "Attachment uploaded successfully",

            "message_id" =>
                $message_id,

            "file_url" =>
                $file_url,

            "message_type" =>
                $message_type,
        ]);

    } catch (Exception $e) {

        echo json_encode([

            "status" => false,

            "message" =>
                $e->getMessage()
        ]);
    }
}



public function unread_chat_count()
{
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    $user_id =
        isset($input['user_id'])
        ? (int)$input['user_id']
        : 0;

    if (!$user_id) {

        echo json_encode([
            "status" => false,
            "message" => "User ID required"
        ]);

        return;
    }

    $this->db->select(
        'COUNT(DISTINCT chat_id) as total'
    );

    $this->db->from('messages');

    $this->db->where(
        'receiver_id',
        $user_id
    );

    $this->db->where(
        'read_reciept',
        0
    );

    $this->db->where(
        'sender_id !=',
        $user_id
    );

    $count =
        $this->db->get()
        ->row_array();

    echo json_encode([

        "status" => true,

        "unread_count" =>
        (int)$count['total']
    ]);
}




public function task_dashboard_count_api()
{
    header('Content-Type: application/json');

    try {

        // =====================================================
        // RAW JSON INPUT
        // =====================================================

        $json = json_decode(
            file_get_contents("php://input"),
            true
        );

        $user_id = isset($json['user_id'])
            ? $json['user_id']
            : 0;

        $user_role_id = isset($json['role_id'])
            ? $json['role_id']
            : 0;

        // =====================================================
        // USER TYPE
        // 1 = ADMIN
        // 2 = HOD
        // 3 = NORMAL USER
        // =====================================================

        $user_type = $this->getUserType(
            $user_role_id,
            $user_id
        );

        // =====================================================
        // COUNTS
        // =====================================================

        $ongoing_count =
            $this->ongoingtaskcountnotification(
                $user_type,
                $user_id
            );

        $overdue_count =
            $this->overduetaskcountnotification(
                $user_type,
                $user_id
            );

        $completed_count =
            $this->completeddfnotificationcount(
                $user_type,
                $user_id
            );



        // =====================================================
        // RESPONSE
        // =====================================================

        $papproval=false;
        if($user_type==1 ||  $user_type==2 || $user_id == 209)
        {
        $papproval=true;
        }

        $unassignedTask=false;
        if($user_type == 1 || $user_type== 2 ) { 
             $unassignedTask=true;
        }

        $pending_approval_count=0;
        if($papproval==true)
        {
            $pending_approval_count =
            $this->completeddfnotificationcountpendingforapproval(
                $user_type,
                $user_id
            );

        }

        $pending_unassigned_task=0;
        if($unassignedTask==true)
        {
            $pending_unassigned_task=$this->unassignednotificationcount(
                $user_type,
                $user_id
            );
        }

        echo json_encode([

            'status' => true,

            'message' =>
                'Task Dashboard Counts Found',

            'data' => [

                'ongoing' => [
                    'total' =>
                        (int)$ongoing_count,
                        'status'=>true
                ],

                'overdue' => [
                    'total' =>
                        (int)$overdue_count,
                        'status'=>true
                ],

                'completed' => [
                    'total' =>
                        (int)$completed_count,
                        'status'=>true
                ],

                'pending_approval' => [
                    'total' =>
                        (int)$pending_approval_count,
                        'status'=>$papproval
                ],

                'unassigned_task' => [
                    'total' =>
                        (int)$pending_unassigned_task,
                        'status'=>$unassignedTask
                ],

                'user_type' => $user_type
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([

            'status' => false,

            'message' =>
                $e->getMessage()
        ]);
    }
}

private function getUserType(
    $user_role_id,
    $user_id
) {


    // =====================================================
    // DEFAULT
    // =====================================================

    $user_type = 3;

    // =====================================================
    // CHECK SUPER ADMIN
    // =====================================================

    $admin_roles =
        $this->Dashboard_model->getsuperadminuserole();

    if (count($admin_roles) > 0) {

        if (in_array($user_role_id, $admin_roles)) {

            // =============================================
            // ADMIN
            // =============================================

            $user_type = 1;

            return $user_type;
        }
    }

    // =====================================================
    // CHECK HOD / TEAM LEADER
    // =====================================================

    $q = $this->db
        ->select('department_id')
        ->from('prestogroup_teams')
        ->where('team_leader', $user_id)
        ->get();

    if ($q->num_rows() > 0) {

        // =============================================
        // HOD / MULTI DEPARTMENT USER
        // =============================================

        $user_type = 2;

    } else {

        // =============================================
        // NORMAL USER
        // =============================================

        $user_type = 3;
    }

    return $user_type;
}


public function ongoingtaskcountnotification($user_type,$user_id){
        $count = 0;
        $department_id=array();
        $self_user=0;
        if($user_type==2)
        {
        $department_id=$this->task->getAssignedDepartment($user_id);
        }

         $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('task_status',0)->where('b.df_status',0)->where('b.df_status',0)->where('assigned_user!=',0)->where('end_date>=',date('Y-m-d'));

        if(count($department_id)>0)
            {
                $this->db->where_in('department_id',$department_id,'false');
            }
            if($user_type==3)
            {
                $this->db->where('assigned_user',$user_id);
            }

            $this->db->where('department_id!=','22');
            $q =$this->db->get();
        if($q->num_rows()>0){
            $count = count($q->result());
        }
        return $count;
    }

public function overduetaskcountnotification($user_type,$user_id){
        $count = 0;
        $department_id=array();
        $self_user=0;
        if($user_type==2)
        {
        $department_id=$this->task->getAssignedDepartment($user_id);
        }

        $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.on_hold',0)->where('a.task_status',0)->where('b.df_status',0)->where('a.end_date<',date('Y-m-d'));
        if(count($department_id)>0)
            {
                $this->db->where_in('department_id',$department_id,'false');
            }
            if($user_type==3)
            {
                $this->db->where('assigned_user',$user_id);
            }

            $this->db->where('department_id!=','22');
            $q = $this->db->get();
        if($q->num_rows()>0){
            $count = count($q->result());
        }
        return $count;
    }

public function completeddfnotificationcount($user_type,$user_id){
        $count = 0;
        $department_id=array();
        $self_user=0;
        if($user_type==2)
        {
        $department_id=$this->task->getAssignedDepartment($user_id);
        }

        $q = $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.task_status',1)->where('a.on_hold',0);
        if(count($department_id)>0)
            {
                $this->db->where_in('department_id',$department_id,'false');
            }
            if($user_type==3)
            {
                $this->db->where('assigned_user',$user_id);
            }
            $this->db->limit(100);
        $q=$this->db->get();
        if($q->num_rows()>0){
            $count = count($q->result());
        }
        return $count;
    }


    public function completeddfnotificationcountpendingforapproval($user_type,$user_id){
        $count = 0;
        $department_id=array();
        $self_user=0;
        if($user_type==2)
        {
        $department_id=$this->task->getAssignedDepartment($user_id);
        }

        $q = $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.task_status',2)->where('a.on_hold',0);
        if(count($department_id)>0)
            {
                $this->db->where_in('department_id',$department_id,'false');
            }
            if($user_type==3)
            {
                $this->db->where('assigned_user',$user_id);
            }

        $q=$this->db->get();
        if($q->num_rows()>0){
            $count = count($q->result());
        }
        return$count; exit;
    }



public function unassignednotificationcount($user_type,$user_id){
        $departmentid = array(); 
        $department_id=array();
        if($user_type==2)
        {
        $department_id=$this->task->getAssignedDepartment($user_id);
        }


        $count = 0;
        $this->db->select('id')->from('task_department_wise_scheduling');
        if(count($department_id)>0)
            {
                $this->db->where_in('department_id',$department_id,'false');
            }
            
            $this->db->where('department_id!=','22');
        $q = $this->db->where('task_status',0)->where('assigned_user',0)->group_by('df_id')->get();
        if($q->num_rows()>0){
            $count = count($q->result());
        }
        return $count;
    }


        public function ongoing_task_list_api()
        {
        header('Content-Type: application/json');

        try {

        // =====================================================
        // RAW JSON INPUT
        // =====================================================

        $json = json_decode(
        file_get_contents("php://input"),
        true
        );

        $user_id = isset($json['user_id'])
        ? $json['user_id']
        : 0;

        $role_id = isset($json['role_id'])
        ? $json['role_id']
        : 0;

        $department_id = isset($json['department_id'])
        ? $json['department_id']
        : 0;

        $filter_req = isset($json['filter'])
        ? $json['filter']
        : '';

        $deptid = isset($json['department_filter'])
        ? $json['department_filter']
        : '';

        $usrid = isset($json['user_filter'])
        ? $json['user_filter']
        : '';

        $df_ids = isset($json['df_id'])
        ? $json['df_id']
        : '';

        // =====================================================
        // USER TYPE
        // 1 = ADMIN
        // 2 = HOD
        // 3 = NORMAL USER
        // =====================================================

        $admin_user_type = $this->getUserType(
        $role_id,
        $user_id
        );

        // =====================================================
        // DEPARTMENT FILTER
        // =====================================================

        $department_ids_filter = [];

        if ($admin_user_type == 2) {

        $department_ids_filter =
        $this->task->getAssignedDepartment(
        $user_id
        );
        }

        // =====================================================
        // QUERY
        // =====================================================

        $this->db->select('
        a.id,
        a.taskid,
        a.df_id,
        a.po_id,
        a.department_id,
        a.assigned_user,
        a.start_date,
        a.end_date,
        a.taskupdatedontime,
        a.remarks,
        a.on_hold,
        a.task_status,

        b.df_no,
        b.df_upload,
        b.added_on as df_added_on,
        b.df_description,
        b.df_status,

        c.task_name,
        c.sortorder,
        c.is_it_mom,
        c.task_frequency,

        d.department,

        f.first_name,
        f.last_name,

        p.lead_id,
        p.pono,
        p.df_number,
        p.basic_machine as po_basic_machine,
        p.po_attachment,
        p.company_name,

        k.first_name as marketingpersonfname,
        k.last_name as marketingpersonlname,

        qcd.mach_model_no,
        annex.product_to_be_packed,

        l.patient_type_id,

        (
        SELECT COUNT(id)
        FROM communication_ticket_system
        WHERE task_record_id = a.id
        AND df_id = a.df_id
        ) as ticket_count
        ');

        $this->db->from(
        'task_department_wise_scheduling a'
        );

        $this->db->join(
        'df_release b',
        'a.df_id=b.id',
        'left'
        );

        $this->db->join(
        'task_management c',
        'a.taskid=c.task_id',
        'left'
        );

        $this->db->join(
        'departments d',
        'a.department_id=d.department_id',
        'left'
        );

        $this->db->join(
        'system_users f',
        'a.assigned_user=f.user_id',
        'left'
        );

        $this->db->join(
        'poreceived p',
        'a.po_id=p.id',
        'left'
        );

        $this->db->join(
        'system_users k',
        'p.added_by=k.user_id',
        'left'
        );

        $this->db->join(
        'quotation_customer_data qcd',
        'p.lead_id=qcd.lead_id',
        'left'
        );

        $this->db->join(
        'quotation_annexture_1 annex',
        'qcd.id=annex.record_id',
        'left'
        );

        $this->db->join(
        'leads l',
        'p.lead_id=l.id',
        'left'
        );

        // =====================================================
        // FILTERS
        // =====================================================

        $this->db->where('a.task_status', 0);

        $this->db->where('a.on_hold', 0);

        $this->db->where(
        'a.assigned_user !=',
        0
        );

        $this->db->where(
        'a.end_date >=',
        date('Y-m-d')
        );

        $this->db->where(
        'a.department_id !=',
        22
        );

        // =====================================================
        // HOD FILTER
        // =====================================================

        if (
        count($department_ids_filter) > 0
        ) {

        $this->db->where_in(
        'a.department_id',
        $department_ids_filter
        );
        }

        // =====================================================
        // NORMAL USER FILTER
        // =====================================================

        if ($admin_user_type == 3) {

        $this->db->where(
        'a.assigned_user',
        $user_id
        );
        }

        // =====================================================
        // EXTRA FILTERS
        // =====================================================

        if (
        $df_ids != ''
        &&
        $df_ids != 'ALL'
        ) {

        $this->db->where(
        'a.df_id',
        $df_ids
        );
        }

        if (
        $deptid != ''
        &&
        $deptid != 'ALL'
        ) {

        $this->db->where(
        'a.department_id',
        $deptid
        );
        }

        if (
        $usrid != ''
        &&
        $usrid != 'ALL'
        ) {

        $this->db->where(
        'a.assigned_user',
        $usrid
        );
        }

        // =====================================================
        // DF STATUS
        // =====================================================

        $this->db->group_start();

        $this->db->where('a.df_id', 0);

        $this->db->or_where(
        'b.df_status',
        0
        );

        $this->db->group_end();

        $this->db->group_by('a.id');

        $this->db->order_by(
        'b.df_no',
        'ASC'
        );

        $query = $this->db->get();

        $res = $query->result();

        // =====================================================
        // FINAL DATA
        // =====================================================

        $today_str = date('Y-m-d');

        $taskdata = [];

        $i = 1;

        foreach ($res as $row) {

               // =================================================
        // TODAY / THIS WEEK FILTER
        // =================================================

        $show = 1;

        $today_date =
            date('Y-m-d');

        $task_start_date =
            date(
                'Y-m-d',
                strtotime($row->start_date)
            );

        $task_end_date =
            date(
                'Y-m-d',
                strtotime($row->end_date)
            );

        // =============================================
        // TODAY
        // END DATE SHOULD BE TODAY
        // =============================================

        if (
            $filter_req == 'today'
        ) {

            if (
                $task_end_date
                !=
                $today_date
            ) {

                $show = 0;
            }
        }

        // =============================================
        // THIS WEEK
        // START OR END DATE SHOULD
        // LIE IN CURRENT WEEK
        // =============================================

        else if (
            $filter_req == 'week'
        ) {

            $week_start =
                date(
                    'Y-m-d',
                    strtotime('monday this week')
                );

            $week_end =
                date(
                    'Y-m-d',
                    strtotime('sunday this week')
                );

            $start_in_week =
                (
                    $task_start_date >= $week_start
                    &&
                    $task_start_date <= $week_end
                );

            $end_in_week =
                (
                    $task_end_date >= $week_start
                    &&
                    $task_end_date <= $week_end
                );

            if (
                !$start_in_week
                &&
                !$end_in_week
            ) {

                $show = 0;
            }
        }

        if ($show == 1) {

        // =============================================
        // DAYS
        // =============================================

        $pendingdays =
        $this->task->getDays(
        $today_str,
        $row->end_date,
        2
        );

        // =============================================
        // DF NO
        // =============================================

        $df_no = '';

        if ($row->df_id == 0) {

        $df_no = '-';

        } else {

        $df_no =
        strtoupper($row->df_no);
        }

        // =============================================
        // LAST UPDATE
        // =============================================

        $last_update = '';

        if (
        !empty(
        $row->taskupdatedontime
        )
        &&
        $row->taskupdatedontime
        !=
        '0000-00-00 00:00:00'
        ) {

        $last_update =
        date(
        'd-m-Y h:i A',
        strtotime(
        $row->taskupdatedontime
        )
        );
        }

        // =============================================
        // DEFAULT FLAGS
        // =============================================

        $action_type = '';
        $action_label = '';
        $action_url = '';

        $can_update_progress = false;
        $can_release_df = false;
        $can_reassign = false;
        $can_download_po = false;
        $can_download_df = false;
        $can_view_ticket = false;
        $is_admin_override = false;

        // =============================================
        // DOWNLOAD DF
        // =============================================

        $df_download_url = '';

        if (
        !empty($row->df_upload)
        &&
        $row->df_id > 0
        ) {

        $can_download_df = true;

        $df_download_url =
        sfdocument .
        'Taskdocument/dfattachment/' .
        $row->df_upload;
        }

        // =============================================
        // DOWNLOAD PO
        // =============================================

        $po_download_url = '';

        if (
        in_array(
        $department_id,
        [9, 10, 20]
        )
        &&
        !empty($row->po_attachment)
        ) {

        $can_download_po = true;

        $po_download_url =
        sfdocument .
        'Taskdocument/' .
        $row->po_attachment;
        }

        // =============================================
        // VIEW TICKET
        // =============================================

        if ($row->ticket_count > 0) {

        $can_view_ticket = true;
        }

        // =============================================
        // HOD REASSIGN
        // =============================================

        if (
        $admin_user_type == 2
        &&
        $user_id != $row->assigned_user
        ) {

        $can_reassign = true;
        }

        // =============================================
        // ADMIN OVERRIDE
        // =============================================

        if (
        $user_id == 61
        ||
        $user_id == 161
        ) {

        $is_admin_override = true;
        }

       // =============================================
// UPDATE PROGRESS ACCESS LOGIC
// =============================================

$pms_action_required = false;
$pms_action_message = '';

if (
    $user_id == $row->assigned_user
    ||
    $is_admin_override == true
) {

    // =========================================
    // RELEASE DF
    // PMS ONLY
    // =========================================

    if (
        $row->df_id == 0
        &&
        $row->taskid == 2
    ) {

        $can_release_df = true;

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'release_df';

        $action_label = 'Release DF';

        $action_url =
            'Dashboard/releasedf/' .
            $row->id .
            '/' .
            $row->df_number;
    }

    // =========================================
    // DF MEETING MOM
    // MOBILE ACCESSIBLE
    // =========================================

    else if ($row->is_it_mom == 1) {

        $can_update_progress = true;

        $action_type = 'df_meeting_mom';

        $action_label = 'DF Meeting MOM';

        $action_url =
            'Task/dfmeeting/' .
            $row->id .
            '/' .
            $row->df_id;
    }

    // =========================================
    // FILL DESIGN FORM
    // PMS ONLY
    // =========================================

    else if (
        $row->df_id == 0
        &&
        $row->taskid == 114
    ) {

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'fill_df';

        $action_label = 'Fill Design Form';

        if (
            in_array(
                $row->mach_model_no,
                [300, 600]
            )
        ) {

            $action_url =
                'Dashboard/df_form_600/' .
                $row->id .
                '/' .
                $row->po_id .
                '/' .
                $row->lead_id;

        } else {

            if (
                $row->product_to_be_packed == 2
            ) {

                $action_url =
                    'Dashboard/powder_df_form_design/' .
                    $row->id .
                    '/' .
                    $row->po_id .
                    '/' .
                    $row->lead_id;

            } else {

                $action_url =
                    'Dashboard/df_project_form/' .
                    $row->id .
                    '/' .
                    $row->po_id .
                    '/' .
                    $row->lead_id;
            }
        }
    }

    // =========================================
    // DF REVIEW
    // PMS ONLY
    // =========================================

    else if (
        $row->df_id == 0
        &&
        $row->taskid == 86
    ) {

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'df_review';

        $action_label =
            'DF Review Meeting';

        if ($row->po_basic_machine == 1) {

            $action_url =
                'Dashboard/edit_df_project_form/' .
                $row->po_id .
                '/' .
                $row->lead_id .
                '/' .
                $row->id;

        } else {

            if (
                in_array(
                    $row->mach_model_no,
                    [300, 600]
                )
            ) {

                $action_url =
                    'Dashboard/design_form_600_edit/' .
                    $row->po_id .
                    '/' .
                    $row->lead_id .
                    '/' .
                    $row->id;

            } else {

                if (
                    $row->product_to_be_packed == 2
                ) {

                    $action_url =
                        'Dashboard/edit_powder_df_form/' .
                        $row->po_id .
                        '/' .
                        $row->lead_id .
                        '/' .
                        $row->id;

                } else {

                    $action_url =
                        'Dashboard/edit_df_project_form/' .
                        $row->po_id .
                        '/' .
                        $row->lead_id .
                        '/' .
                        $row->id;
                }
            }
        }
    }

    // =========================================
    // CREATE PI
    // PMS ONLY
    // =========================================

    else if (
        $row->df_id == 0
        &&
        $row->taskid == 3
    ) {

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'create_pi';

        $action_label = 'Create PI';

        if (
            $row->patient_type_id == 1
        ) {

            $action_url =
                'Form/performa_invoice/' .
                $row->po_id .
                '/' .
                $row->lead_id .
                '/' .
                $row->id;

        } else {

            $action_url =
                'Form/export_performa_invoice/' .
                $row->po_id .
                '/' .
                $row->lead_id .
                '/' .
                $row->id;
        }
    }

    // =========================================
    // NORMAL UPDATE
    // MOBILE ACCESSIBLE
    // =========================================

    else {

        $can_update_progress = true;

        $action_type = 'update_progress';

        $action_label =
            'Update Progress';
    }
}

        $taskdata[] = [

        'sr_no' => $i,

        'task_record_id' =>
        $row->id,

        'task_id' =>
        $row->taskid,

        'df_id' =>
        $row->df_id,

        'po_id' =>
        $row->po_id,

        'df_no' =>
        $df_no,

        'df_description' =>
        $row->df_description,

        'task_name' =>
        ucwords(
        strtolower(
        $row->task_name
        )
        ),

        'department' =>
        ucwords(
        strtolower(
        $row->department
        )
        ),

        'company_name' =>
        $row->company_name,

        'marketing_person' =>
        ucwords(
        strtolower(
        $row->marketingpersonfname
        .
        ' '
        .
        $row->marketingpersonlname
        )
        ),

        'assigned_to' =>
        ucwords(
        strtolower(
        $row->first_name
        .
        ' '
        .
        $row->last_name
        )
        ),

        'remarks' =>
        $row->remarks,

        'start_date' =>
        date(
        'd-m-Y',
        strtotime(
        $row->start_date
        )
        ),

        'end_date' =>
        date(
        'd-m-Y',
        strtotime(
        $row->end_date
        )
        ),

        'pending_days' =>
        $pendingdays,

        'last_update' =>
        $last_update,

        'ticket_count' =>
        (int)$row->ticket_count,

        // =====================================
        // PERMISSIONS
        // =====================================

        'permissions' => [

    'can_update_progress' =>
        $can_update_progress,

    'can_release_df' =>
        $can_release_df,

    'can_reassign' =>
        $can_reassign,

    'can_download_po' =>
        $can_download_po,

    'can_download_df' =>
        $can_download_df,

    'can_view_ticket' =>
        $can_view_ticket,

    'is_admin_override' =>
        $is_admin_override,

    'pms_action_required' =>
        $pms_action_required,

    'pms_action_message' =>
        $pms_action_message,
],

        // =====================================
        // DOWNLOADS
        // =====================================

        'downloads' => [

        'df_download_url' =>
        $df_download_url,

        'po_download_url' =>
        $po_download_url,
        ],

        // =====================================
        // ACTION
        // =====================================

        'action' => [

        'type' =>
        $action_type,

        'label' =>
        $action_label,

        'url' =>
        $action_url,
        ]
        ];
        $i++;
        }
        }

        // =====================================================
        // RESPONSE
        // =====================================================

        echo json_encode([

        'status' => true,

        'message' =>
        'Ongoing Task List Found',

        'total_records' =>
        count($taskdata),

        'data' => $taskdata
        ]);

        } catch (Exception $e) {

        echo json_encode([

        'status' => false,

        'message' =>
        $e->getMessage(),

        'data' => []
        ]);
        }
}


public function task_help_ticket_dropdown_api()
{
    header('Content-Type: application/json');

    try {

        // ============================================
        // GET ALL PMS DEPARTMENTS
        // ============================================

        $departments = $this->db

            ->select('
                department_id,
                department
            ')

            ->from('departments')

            ->where('status', 1)

            ->where('show_in_pms', 1)

            ->order_by(
                'department',
                'ASC'
            )

            ->get()

            ->result();

        $final_departments = [];

        // ============================================
        // LOOP DEPARTMENTS
        // ============================================

        foreach ($departments as $dept) {

            // ========================================
            // GET USERS
            // ========================================

            $users = $this->db

                ->select('
                    user_id,
                    first_name,
                    last_name
                ')

                ->from('system_users')

                ->where(
                    'department_id',
                    $dept->department_id
                )

                ->where(
                    'user_status',
                    1
                )

                ->order_by(
                    'first_name',
                    'ASC'
                )

                ->get()

                ->result();

            $user_data = [];

            foreach ($users as $usr) {

                $user_data[] = [

                    'user_id' =>
                        $usr->user_id,

                    'user_name' =>
                        ucwords(
                            strtolower(
                                trim(
                                    $usr->first_name .
                                    ' ' .
                                    $usr->last_name
                                )
                            )
                        ),
                ];
            }

            $final_departments[] = [

                'department_id' =>
                    $dept->department_id,

                'department_name' =>
                    ucwords(
                        strtolower(
                            $dept->department
                        )
                    ),

                'users' =>
                    $user_data,
            ];
        }

        // ============================================
        // RESPONSE
        // ============================================

        echo json_encode([

            'status' => true,

            'message' =>
                'Department Dropdown Found',

            'data' =>
                $final_departments,
        ]);

    } catch (Exception $e) {

        echo json_encode([

            'status' => false,

            'message' =>
                $e->getMessage(),

            'data' => [],
        ]);
    }
}


public function submit_task_update_api()
{
   // exit;
    header('Content-Type: application/json');

    try {

        // =====================================================
        // RAW JSON INPUT
        // =====================================================

        $json = json_decode(
            file_get_contents("php://input"),
            true
        );

        $user_id =
            $json['user_id'] ?? 0;

        $id =
            $json['taskkiid'] ?? 0;

        $mastertaskid =
            $json['mastertaskid'] ?? 0;

        $status =
            $json['taskstatus'] ?? '';

        $taskremarks =
            $json['taskremarks'] ?? '';

        $dfid =
            $json['progressdfno'] ?? 0;

        $ticketselection =
            $json['ticketcondition'] ?? 0;

        $selecteddepartmentid =
            $json['selectdepartment'] ?? 0;

        $selecteduserinfo =
            $json['departmentuser'] ?? 0;

        $previous_task_id =
            $json['previousstep'] ?? 0;

        // =====================================================
        // VALIDATION
        // =====================================================

        if (empty($id)) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Task ID Missing',
            ]);

            return;
        }

        if (
            trim($taskremarks) == ''
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Remarks Required',
            ]);

            return;
        }

        // =====================================================
        // SPECIAL CASES
        // =====================================================

        if ($mastertaskid == 3) {

            $result =
                $this->updatecreationofperformainvoice(
                    $id,
                    $mastertaskid,
                    $status,
                    $taskremarks
                );

            if ($result) {

                echo json_encode([

                    'status' => true,

                    'message' =>
                        'Task Updated Successfully',
                ]);

            } else {

                echo json_encode([

                    'status' => false,

                    'message' =>
                        'Unable To Update Task',
                ]);
            }

            return;
        }

        else if ($mastertaskid == 87) {

            $result =
                $this->updatedfreviewmeetingapprove(
                    $id,
                    $mastertaskid,
                    $status,
                    $taskremarks
                );

            if ($result) {

                echo json_encode([

                    'status' => true,

                    'message' =>
                        'Task Updated Successfully',
                ]);

            } else {

                echo json_encode([

                    'status' => false,

                    'message' =>
                        'Unable To Update Task',
                ]);
            }

            return;
        }

        // =====================================================
        // GET TASK INFO
        // =====================================================

        $this->db->select('
            tdws.start_date,
            tdws.remarks,
            tdws.taskupdatedontime,
            tm.df_meeting_close,
            tm.isitfinalstep
        ');

        $this->db->from(
            'task_department_wise_scheduling tdws'
        );

        $this->db->join(
            'task_management tm',
            'tdws.taskid = tm.task_id',
            'left'
        );

        $this->db->where(
            'tdws.id',
            $id
        );

        $task_info =
            $this->db->get()->row();

        if (!$task_info) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Task Not Found',
            ]);

            return;
        }

        // =====================================================
        // PENDING
        // =====================================================

        if (
            $status == 0
            &&
            $taskremarks <> ''
        ) {

            $data = [

                'task_status' =>
                    $status,

                'taskupdatedontime' =>
                    date('Y-m-d H:i:s'),

                'remarks' =>
                    $taskremarks
            ];

            // =============================================
            // STORE OLD REMARKS
            // =============================================

            if (
                !empty(
                    $task_info->remarks
                )
            ) {

                $datass = [

                    'recordid' =>
                        $id,

                    'taskupdatedontime' =>
                        date('Y-m-d H:i:s'),

                    'remarks' =>
                        $task_info->remarks,

                    'added_on' =>
                        date('Y-m-d H:i:s'),

                    'added_by' =>
                        $user_id
                ];

                $this->db->insert(
                    'task_pending_status',
                    $datass
                );
            }

            // =============================================
            // UPDATE TASK
            // =============================================

            $this->db->where(
                'id',
                $id
            );

            $this->db->update(
                'task_department_wise_scheduling',
                $data
            );

            // =============================================
            // CREATE TICKET
            // =============================================

            if ($ticketselection == 1) {

                $this->task->createnewticket(
                    $selecteddepartmentid,
                    $selecteduserinfo,
                    $dfid,
                    $mastertaskid,
                    $id,
                    $taskremarks,$user_id
                );
            }

            echo json_encode([

                'status' => true,

                'message' =>
                    'Task Updated Successfully',
            ]);

            return;
        }

        // =====================================================
        // TASK COMPLETED
        // =====================================================

        else if (
            $status <> ''
            &&
            $status == 1
        ) {

            $start_date =
                $task_info->start_date;

            $tdadate =
                date('Y-m-d');

            // =============================================
            // PRE CLOSER
            // =============================================

            if ($start_date > $tdadate) {

                $precloserdata = [

                    'df_id' =>
                        $dfid,

                    'task_id' =>
                        $mastertaskid,

                    'record_id' =>
                        $id,

                    'added_on' =>
                        date('Y-m-d H:i:s'),

                    'added_by' =>
                        $user_id,

                    'remarks' =>
                        $taskremarks,

                    'current_status' =>
                        0
                ];

                $this->db->insert(
                    'precloser_task_request',
                    $precloserdata
                );

                $dataupdate11 = [

                    'task_status' => 2,

                    'task_completed_on' =>
                        date('Y-m-d H:i:s'),

                    'taskupdatedontime' =>
                        date('Y-m-d H:i:s'),

                    'task_completed_by' =>
                        $user_id
                ];

                $this->db->where(
                    'id',
                    $id
                );

                $this->db->update(
                    'task_department_wise_scheduling',
                    $dataupdate11
                );

                // =========================================
                // QUEUE INSERT
                // =========================================

                $payload = json_encode([

                    'df_id' =>
                        $dfid,

                    'task_id' =>
                        $mastertaskid,

                    'user_id' =>
                        $user_id,

                    'remarks' =>
                        $taskremarks
                ]);

                $this->db->insert(
                    'notification_queue',
                    [

                        'notification_type' =>
                            'pre_closer',

                        'payload' =>
                            $payload,

                        'status' =>
                            'pending',

                        'created_at' =>
                            date('Y-m-d H:i:s')
                    ]
                );

                echo json_encode([

                    'status' => true,

                    'message' =>
                        'Task Updated And Pending For Approval',
                ]);

                return;
            }

            // =============================================
            // NORMAL COMPLETE
            // =============================================

            $data = [

                'task_status' =>
                    $status,
                    'remarks'=>$taskremarks,

                'task_completed_on' =>
                    date('Y-m-d H:i:s'),

                'taskupdatedontime' =>
                    date('Y-m-d H:i:s'),

                'task_completed_by' =>
                    $user_id
            ];

            $this->db->where(
                'id',
                $id
            );

            $this->db->update(
                'task_department_wise_scheduling',
                $data
            );

            // =============================================
            // TASK COMPLETION QUEUE
            // =============================================

            $payload = json_encode([

                'record_id' =>
                    $id,

                'mastertaskid' =>
                    $mastertaskid,

                'user_id' =>
                    $user_id,

                'dfid' =>
                    $dfid
            ]);

            $this->db->insert(
                'notification_queue',
                [

                    'notification_type' =>
                        'task_completion',

                    'payload' =>
                        $payload,

                    'status' =>
                        'pending',

                    'created_at' =>
                        date('Y-m-d H:i:s')
                ]
            );

            // =============================================
            // CLOSE DF REVIEW MEETING
            // =============================================

            if (
                $task_info->df_meeting_close == 1
            ) {

                $data3 = [

                    'task_status' => 1,

                    'task_completed_on' =>
                        date('Y-m-d H:i:s'),

                    'taskupdatedontime' =>
                        date('Y-m-d H:i:s')
                ];

                $this->db->where(
                    'taskid',
                    4
                );

                $this->db->where(
                    'df_id',
                    $dfid
                );

                $this->db->update(
                    'task_department_wise_scheduling',
                    $data3
                );
            }

            // =============================================
            // FINAL STEP DF CLOSE
            // =============================================

            if (
                $task_info->isitfinalstep == 1
            ) {

                $dfdataarray = [

                    'df_status' => 1,

                    'completed_on' =>
                        date('Y-m-d H:i:s'),

                    'completed_by' =>
                        $user_id
                ];

                $this->db->where(
                    'id',
                    $dfid
                );

                $this->db->update(
                    'df_release',
                    $dfdataarray
                );
            }

            echo json_encode([

                'status' => true,

                'message' =>
                    'Task Completed Successfully',
            ]);

            return;
        }

        // =====================================================
        // SEND TO PREVIOUS STEP
        // =====================================================

        else if (
            $status <> ''
            &&
            $status == 2
        ) {

            // =============================================
            // STORE OLD REMARKS
            // =============================================

            if (
                !empty(
                    $task_info->remarks
                )
            ) {

                $datass = [

                    'recordid' =>
                        $id,

                    'taskupdatedontime' =>
                        date('Y-m-d H:i:s'),

                    'remarks' =>
                        $task_info->remarks,

                    'added_on' =>
                        date('Y-m-d H:i:s'),

                    'added_by' =>
                        $user_id
                ];

                $this->db->insert(
                    'task_pending_status',
                    $datass
                );
            }

            // =============================================
            // GET TASK DATA
            // =============================================

            $q = $this->db->select('
                    df_id,
                    taskid,
                    department_id,
                    start_date,
                    end_date,
                    po_id,
                    userid,
                    assigned_user
                ')
                ->from(
                    'task_department_wise_scheduling'
                )
                ->where(
                    'id',
                    $id
                )
                ->get();

            if (
                $q->num_rows() > 0
            ) {

                $row3 =
                    $q->row();

                // =========================================
                // COPY TASK
                // =========================================

                $data = [

                    'df_id' =>
                        $row3->df_id,

                    'taskid' =>
                        $row3->taskid,

                    'department_id' =>
                        $row3->department_id,

                    'start_date' =>
                        $row3->start_date,

                    'end_date' =>
                        $row3->end_date,

                    'added_on' =>
                        date('Y-m-d H:i:s'),

                    'added_by' =>
                        $user_id,

                    'po_id' =>
                        $row3->po_id,

                    'task_status' =>
                        0,

                    'remarks' =>
                        $taskremarks,

                    'taskupdatedontime' =>
                        date('Y-m-d H:i:s'),

                    'userid' =>
                        $row3->userid,

                    'assigned_user' =>
                        $row3->assigned_user,

                    'assigned_by' =>
                        $user_id,

                    'assigned_on' =>
                        date('Y-m-d H:i:s')
                ];

                $this->db->insert(
                    'task_department_wise_scheduling',
                    $data
                );

                $lastinsertid =
                    $this->db->insert_id();

                // =========================================
                // UPDATE PREVIOUS TASK
                // =========================================

                $data1 = [

                    'task_status' => 0,

                    'remarks' =>
                        $taskremarks
                ];

                $this->db->where(
                    'id',
                    $previous_task_id
                );

                $this->db->update(
                    'task_department_wise_scheduling',
                    $data1
                );

                // =========================================
                // NOTIFICATION QUEUE
                // =========================================

                $payload = json_encode([

                    'df_id' =>
                        $row3->df_id,

                    'task_remark' =>
                        $taskremarks,

                    'user_id' =>
                        $user_id,

                    'department_id' =>
                        $row3->department_id,

                    'assigned_user' =>
                        $row3->assigned_user,

                    'last_insert_id' =>
                        $lastinsertid
                ]);

                $this->db->insert(
                    'notification_queue',
                    [

                        'notification_type' =>
                            'previous_step',

                        'payload' =>
                            $payload,

                        'status' =>
                            'pending',

                        'created_at' =>
                            date('Y-m-d H:i:s')
                    ]
                );

                echo json_encode([

                    'status' => true,

                    'message' =>
                        'Task Sent To Previous Step',
                ]);

                return;
            }

            else {

                echo json_encode([

                    'status' => false,

                    'message' =>
                        'Unable To Find Previous Step',
                ]);

                return;
            }
        }

        // =====================================================
        // INVALID
        // =====================================================

        else {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Something Went Wrong',
            ]);

            return;
        }

    } catch (Exception $e) {

        echo json_encode([

            'status' => false,

            'message' =>
                $e->getMessage(),
        ]);
    }
}


public function approval_users_list_api()
{
    header('Content-Type: application/json');

    $stage_id = 36;

    $query = $this->db->query("

        SELECT

            su.user_id,

            CONCAT(
                COALESCE(su.first_name,''),
                ' ',
                COALESCE(su.last_name,'')
            ) as user_name,

            COUNT(DISTINCT b.id) as total_count

        FROM progress_remarks a

        INNER JOIN leads b
            ON a.lead_id = b.id

        INNER JOIN system_users su
            ON su.user_id = b.added_by

        WHERE a.id IN (

            SELECT MAX(id)
            FROM progress_remarks
            GROUP BY lead_id

        )

        AND a.lead_status = '$stage_id'

        AND b.closed = 0

        GROUP BY b.added_by

        ORDER BY total_count DESC

    ");

    $data=[];

    foreach($query->result() as $row)
    {

        $data[]=[

            "user_id" => (int)$row->user_id,

            "name" => trim(
                ucwords(
                    strtolower($row->user_name)
                )
            ),

            "count" => (int)$row->total_count
        ];
    }

    echo json_encode([

        "status"=>true,

        "data"=>$data

    ]);
}


public function approval_leads_list_api()
{

header('Content-Type: application/json');

$post = json_decode(
    file_get_contents("php://input"),
    true
);

$user_id = $post['marketing_user_id'] ?? 0;

if(empty($user_id)){

    echo json_encode([
        "status"=>false,
        "message"=>"User Required"
    ]);
    return;
}

$stage_id = 36;

$query = $this->db->query("

    SELECT

        b.id as leadid,
        b.unique_id,
        b.create_date,
        b.machine_type,
        b.postal_address,
        b.added_by,
        b.customise_remarks,

        c.company_name,
        c.customer_name,
        c.contact_no,

        a.remarks,
        a.next_follow_date,
        a.added_on,

        su.first_name,
        su.last_name

    FROM progress_remarks a

    JOIN leads b
    ON a.lead_id=b.id

    LEFT JOIN customer_detail c
    ON c.id=b.company_name

    LEFT JOIN system_users su
    ON su.user_id=b.added_by

    WHERE a.id IN (

        SELECT MAX(id)
        FROM progress_remarks
        GROUP BY lead_id

    )

    AND a.lead_status='$stage_id'

    AND b.closed=0

    AND b.added_by='$user_id'

    GROUP BY b.id

    ORDER BY b.id DESC

");

$data=[];

foreach($query->result() as $row){

    /// MACHINE

    $products=$this->getProductDetails($row->leadid);
            if(count($products)>0)
            {
                $prd_name=$products[0];
                $prd_qty=$products[1];
            }else
            {
                $prd_name='';
                $prd_qty='';
            }


    if($row->machine_type==1){

        $machine="Liquid";

    }elseif($row->machine_type==2){

        $machine="Powder";

    }else{

        $machine="Custom";
    }

    /// REMARKS

    $remarks =
    strip_tags(
        $row->remarks
    );

    /// VERSION

    $quotation =
    $this->db
    ->select('version')
    ->from('quotation_customer_data')
    ->where(
        'lead_id',
        $row->leadid
    )
    ->order_by(
        'id',
        'DESC'
    )
    ->limit(1)
    ->get();

    $version="V1";

    if(
    $quotation->num_rows()>0
    ){

        $version=
        "V".
        $quotation
        ->row()
        ->version;
    }

    /// QUOTE LINK

    $quoteLink='';

    $quote=
    $this->db
    ->select('id')
    ->from(
        'quotation_customer_data'
    )
    ->where(
        'lead_id',
        $row->leadid
    )
    ->order_by(
        'id',
        'DESC'
    )
    ->limit(1)
    ->get();

    if(
    $quote->num_rows()>0
    ){

        $quoteId=
        $quote->row()->id;

        $quoteLink=
        page_url.
        'Opportunity/GeneratedQuote/'.
        $quoteId.
        '/Open';
    }

    $data[]=[

        "lead_id"=>$row->leadid,

        "company"=>
        ucwords(
        strtolower(
        $row->company_name
        )),

        "opp"=>
        $row->unique_id,

        "machine"=>
        $prd_name,

        "quote_date"=>
        $row->added_on
        ?
        date(
        'd M Y',
        strtotime(
        $row->added_on
        ))
        :
        "",

        "remarks"=>
        $remarks,

        "version"=>
        $version,

        "quote_link"=>
        $quoteLink,

        "assigned"=>
        trim(
        $row->first_name.
        ' '.
        $row->last_name
        ),
"lead_stage"   => 36,
"quote_type"   => "send_for_approval",
"can_approve"  => true,

    ];
}

echo json_encode([

    "status"=>true,

    "data"=>$data

]);

}



public function service_approval_users_api()
{
    header('Content-Type: application/json');

    try {

        $this->db->select("
            u.user_id,
            CONCAT(
                IFNULL(u.first_name,''),' ',
                IFNULL(u.last_name,'')
            ) as name,

            COUNT(
                DISTINCT so.opportunity_id
            ) as count
        ");

        $this->db->from(
            'service_opportunities so'
        );

        $this->db->join(

            'system_users u',

            'u.user_id = so.marketing_person_id',

            'left'

        );

        /// ONLY APPROVAL STAGE

        $this->db->where(
            'so.current_stage_id',
            3
        );

        /// OPTIONAL ONLY ACTIVE USERS

        $this->db->where(
            'u.user_id IS NOT NULL',
            null,
            false
        );

        $this->db->group_by(
            'u.user_id'
        );

        $this->db->order_by(
            'count',
            'DESC'
        );

        $result =
        $this->db
            ->get()
            ->result_array();

        foreach($result as &$row){

            $row['user_id']=
            (int)$row['user_id'];

            $row['count']=
            (int)$row['count'];

            $row['name']=
            trim(
                $row['name']
            );
        }

        echo json_encode([

            "status"=>true,

            "data"=>$result

        ]);

    }

    catch(Exception $e){

        echo json_encode([

            "status"=>false,

            "message"=>"Something went wrong",

            "error"=>$e->getMessage()

        ]);

    }
}


public function service_lead_approval_api()
{
    header('Content-Type: application/json');

    try {

        $input = json_decode(file_get_contents("php://input"), true);

        $marketing_user_id = $input['marketing_user_id'] ?? null;

        $this->db->select("
            so.opportunity_id,
            so.op_no,
            so.op_date,
            so.op_type,
            so.probability,
            so.customer_table_origin,
            so.customer_contact_no,
            so.customer_name,

            IFNULL(cm_spares.company_name, cm_marketing.company_name) as company_name,

            CASE 
                WHEN so.customer_table_origin='spares'
                    THEN cm_spares.contact_person
                ELSE cm_marketing.customer_name
            END as contact_name,

            CONCAT(u.first_name,' ',u.last_name) as marketing_person_name,

            so.current_stage_id,
            sls.stage_name as current_stage_name,

            sph.remarks as latest_remarks,
            sph.next_follow_date,
            sph.added_on as latest_added_on,

            (
                SELECT id
                FROM service_quotations
                WHERE opportunity_id=so.opportunity_id
                ORDER BY id DESC
                LIMIT 1
            ) as latest_quote_id
        ");

        $this->db->from('service_opportunities so');

        /*
        Latest progress history
        */
        $this->db->join("
            (
                SELECT p1.*
                FROM service_progress_history p1
                INNER JOIN
                (
                    SELECT opportunity_id,
                    MAX(history_id) max_id
                    FROM service_progress_history
                    GROUP BY opportunity_id
                ) p2
                ON p1.history_id=p2.max_id
            ) sph
        ","sph.opportunity_id=so.opportunity_id","left");

        $this->db->join(
            'spares_customers cm_spares',
            'cm_spares.customer_id=so.customer_id',
            'left'
        );

        $this->db->join(
            'customer_detail cm_marketing',
            'cm_marketing.id=so.customer_id',
            'left'
        );

        $this->db->join(
            'system_users u',
            'u.user_id=so.marketing_person_id',
            'left'
        );

        $this->db->join(
            'service_lead_stages sls',
            'sls.stage_id=so.current_stage_id',
            'left'
        );

        /*
        FIXED STAGE = 3
        */
        $this->db->where('so.current_stage_id',3);

        /*
        Selected user filter
        */
        if(!empty($marketing_user_id))
        {
            $this->db->where(
                'so.marketing_person_id',
                $marketing_user_id
            );
        }

        $this->db->order_by(
            'so.opportunity_id',
            'DESC'
        );

        $result=$this->db->get()->result_array();

        foreach($result as &$row)
        {

            $countingnum = '';
            $version="V1";
            $quotation_count = $this->db
            ->where('opportunity_id', $row['opportunity_id'])
            ->count_all_results('service_quotations');
            if ($quotation_count > 0) {
            $versioncount = $quotation_count;
            if($versioncount>1){
            $countingnum = $versioncount-1;
            }else{
            $countingnum = '';
            }
            if($countingnum!==''){
            $version="V" . $countingnum;
            }
            }


            $quoteId=$row['latest_quote_id'];

        $quoteLink=
        page_url.
        'ServiceLeads/view_quotation_pdf/'.
        $quoteId.
        '/Open';

            $remarks=$row['latest_remarks'] ?? '';

            $row['opportunity_id']=(int)$row['opportunity_id'];

            $row['company']=ucwords(strtolower($row['company_name'])) ?? '';

            $row['contact']=$row['customer_name'] ?? '';

            $row['contact_no']=$row['customer_contact_no'] ?? '';

            $row['assigned']=$row['marketing_person_name'] ?? '';

            $row['stage']=$row['current_stage_name'] ?? '';

            $row['date']=!empty($row['next_follow_date'])
                ? date(
                    'd-m-Y',
                    strtotime($row['next_follow_date'])
                )
                : '';

            $row['remarks_short']=
                strlen($remarks)>80
                ? substr($remarks,0,80).'...'
                : $remarks;

            $row['remarks_full']=$remarks;

            $row['opp']=$row['op_no'];

            $row['quote_id']=!empty($row['latest_quote_id'])
                ? (int)$row['latest_quote_id']
                : null;

            $row['can_update']=true;
            $row['can_quote']=!empty($row['quote_id']);
            $row['can_send_intro']=true;
            $row['quoteLink']=$quoteLink;

           $row['address']="";

$row['op_type'] =
    ($row['op_type']=="1")
    ? "Domestic"
    : "Export";

$row['product']="";
$row['generatedOn']=date('d-M-Y',strtotime($row['latest_added_on']));
$row['machine']="";
$row['version']=$version;
        }

        echo json_encode([
            "status"=>true,
            "total"=>count($result),
            "data"=>$result
        ]);

    }
    catch(Exception $e)
    {

        echo json_encode([
            "status"=>false,
            "message"=>"Something went wrong",
            "error"=>$e->getMessage()
        ]);

    }
}


public function spare_approval_users_api()
{
    header('Content-Type: application/json');

    $input = json_decode(
        file_get_contents('php://input'),
        true
    );

    $role_id =
        $input['role_id'] ?? 0;

    $user_id =
        $input['user_id'] ?? 0;

    /*
    Fixed approval stage
    */

    $stage_id = 4;

    $stage_filter_subquery = "
    (
        SELECT spr2.lead_stage
        FROM spare_progress_remarks spr2
        WHERE spr2.lead_id =
        op.opportunity_id
        ORDER BY spr2.id DESC
        LIMIT 1
    )
    ";

    try{

        $this->db->select("

        u.user_id,

        CONCAT(

        u.first_name,

        ' ',

        u.last_name

        ) as name,

        COUNT(
        op.opportunity_id
        ) as count

        ",false);

        $this->db->from(
            'opportunities op'
        );

        $this->db->join(

            'system_users u',

            'u.user_id=
            op.marketing_person_id',

            'left'

        );

        /*
        Role filtering
        */

      
        /*
        Stage 4 only
        */

        $this->db->where(

            "{$stage_filter_subquery} = ".$stage_id,

            null,

            false

        );

        $this->db->group_by(
            'u.user_id'
        );

        $this->db->order_by(
            'name',
            'ASC'
        );

        $result=
        $this->db
            ->get()
            ->result_array();

       foreach(
$result
as &$row
){

    $row['user_id']=
        (int)
        $row['user_id'];

    $row['count']=
        (int)
        $row['count'];

    $row['name']=

        ucwords(

            strtolower(

                trim(
                    $row['name']
                )

            )

        );

}

        echo json_encode([

            "status"=>true,

            "data"=>$result

        ]);

    }

    catch(Exception $e){

        echo json_encode([

            "status"=>false,

            "message"=>
            "Something went wrong",

            "error"=>
            $e->getMessage()

        ]);

    }

}


public function spare_approval_list_api()
{

    header(
        'Content-Type: application/json'
    );

    try{

        $input=
        json_decode(
            file_get_contents(
                'php://input'
            ),
            true
        );

        $marketing_user_id=

            $input[
            'marketing_user_id'
            ]

            ?? null;

        /*
        latest remark
        */

        $latest_remark_subquery="

        (

            SELECT MAX(
            spr1.id
            )

            FROM
            spare_progress_remarks spr1

            WHERE
            spr1.lead_id=
            op.opportunity_id

        )

        ";

        /*
        latest quote
        */

       $latest_quote_id_subquery="

(

SELECT
q.quotation_id

FROM quotations q

WHERE
q.opportunity_id=
op.opportunity_id

ORDER BY
q.quotation_id DESC

LIMIT 1

)

";


$latest_revision_subquery="

(

SELECT
q.revision_no

FROM quotations q

WHERE
q.opportunity_id=
op.opportunity_id

ORDER BY
q.quotation_id DESC

LIMIT 1

)

";
        $this->db->select("

        op.opportunity_id,

        op.op_no,

        op.op_date,

        op.op_type,

        op.status,

        op.probability,

        cm.company_name,

        cm.contact_person,

        cm.contact_person_no,

        CONCAT(
        u.first_name,
        ' ',
        u.last_name
        ) as marketing_person_name,

        spr.id,

        spr.lead_stage,

        spr.next_follow_date,

        spr.remarks,

        spr.added_on,

        sls.lead_name
        as stage_name,

       {$latest_quote_id_subquery}

as latest_quote_id,

{$latest_revision_subquery}

as revision_no

        ",false);

        $this->db->from(
            'opportunities op'
        );

        $this->db->join(

            'spares_customers cm',

            'cm.customer_id=
            op.customer_id',

            'left'

        );

        $this->db->join(

            'system_users u',

            'u.user_id=
            op.marketing_person_id',

            'left'

        );

        $this->db->join(

            'spare_progress_remarks spr',

            "spr.id=
            {$latest_remark_subquery}",

            'left',

            false

        );

        $this->db->join(

            'spare_lead_stage sls',

            'sls.lead_id=
            spr.lead_stage',

            'left'

        );

        /*
        stage 4 fixed
        */

        $this->db->where(
            'spr.lead_stage',
            4
        );

        /*
        user filter
        */

        if(
        !empty(
        $marketing_user_id
        )
        ){

            $this->db->where(

                'op.marketing_person_id',

                $marketing_user_id

            );

        }

        $this->db->order_by(
            'op.opportunity_id',
            'DESC'
        );

        $result=
        $this->db
            ->get()
            ->result_array();

        $rows=[];

        foreach(
        $result
        as $row
        ){

            $quoteUrl="";

            if(
            !empty(
            $row[
            'latest_quote_id'
            ]
            )){

                $quoteUrl=

                    page_url1

                    .'uploads/spare_quotations/'

                    .$row[
                    'latest_quote_id'
                    ]

                    .".pdf";

            }


            if($row['revision_no']=='')
            {
                $version=0;
            }else
            {
                $version=$row['revision_no']+1;
            }

            $rows[]=[

                "opportunity_id"=>

                (int)
                $row[
                'opportunity_id'
                ],

                "opp"=>

                $row[
                'op_no'
                ],

                "company"=>

                ucwords(strtolower($row[
                'company_name'
                ])) ?? "",

                "contact"=>

                $row[
                'contact_person'
                ] ?? "",

                "contact_no"=>

                $row[
                'contact_person_no'
                ] ?? "",

                "assigned"=>

                $row[
                'marketing_person_name'
                ] ?? "",

                "stage"=>

                $row[
                'stage_name'
                ] ?? "",

                "op_type"=>

                $row[
                'op_type'
                ]==1

                ?

                "Domestic"

                :

                "Export",

                "remarks_short"=>

                strlen(
                $row[
                'remarks'
                ] ?? ""
                )>80

                ?

                substr(
                $row[
                'remarks'
                ],
                0,
                80
                )."..."

                :

                $row[
                'remarks'
                ],

                "remarks_full"=>

                $row[
                'remarks'
                ] ?? "",

                "generatedOn"=>

                !empty(
                $row[
                'added_on'
                ])

                ?

                date(

                    'd-M-Y',

                    strtotime(

                        $row[
                        'added_on'
                        ]

                    )

                )

                : "",

                "quote_id"=>

                (int)
                $row[
                'latest_quote_id'
                ],

                "quoteLink"=>

                $quoteUrl,

                "version"=>

                "V".$version,

                "machine"=>"",

                "can_quote"=>

                !empty(
                $row[
                'latest_quote_id'
                ]),

                "can_approve"=>
                true

            ];

        }

        echo json_encode([

            "status"=>true,

            "total"=>

            count(
            $rows
            ),

            "data"=>$rows

        ]);

    }

    catch(Exception $e){

        echo json_encode([

            "status"=>false,

            "message"=>
            "Something went wrong",

            "error"=>
            $e->getMessage()

        ]);

    }

}



 public function overdue_task_list_api()
        {
        header('Content-Type: application/json');

        try {

        // =====================================================
        // RAW JSON INPUT
        // =====================================================

        $json = json_decode(
        file_get_contents("php://input"),
        true
        );

        $user_id = isset($json['user_id'])
        ? $json['user_id']
        : 0;

        $role_id = isset($json['role_id'])
        ? $json['role_id']
        : 0;

        $department_id = isset($json['department_id'])
        ? $json['department_id']
        : 0;

        $filter_req = isset($json['filter'])
        ? $json['filter']
        : '';

        $deptid = isset($json['department_filter'])
        ? $json['department_filter']
        : '';

        $usrid = isset($json['user_filter'])
        ? $json['user_filter']
        : '';

        $df_ids = isset($json['df_id'])
        ? $json['df_id']
        : '';

        // =====================================================
        // USER TYPE
        // 1 = ADMIN
        // 2 = HOD
        // 3 = NORMAL USER
        // =====================================================

        $admin_user_type = $this->getUserType(
        $role_id,
        $user_id
        );

        // =====================================================
        // DEPARTMENT FILTER
        // =====================================================

        $department_ids_filter = [];

        if ($admin_user_type == 2) {

        $department_ids_filter =
        $this->task->getAssignedDepartment(
        $user_id
        );
        }

        // =====================================================
        // QUERY
        // =====================================================

        $this->db->select('
        a.id,
        a.taskid,
        a.df_id,
        a.po_id,
        a.department_id,
        a.assigned_user,
        a.start_date,
        a.end_date,
        a.taskupdatedontime,
        a.remarks,
        a.on_hold,
        a.task_status,

        b.df_no,
        b.df_upload,
        b.added_on as df_added_on,
        b.df_description,
        b.df_status,

        c.task_name,
        c.sortorder,
        c.is_it_mom,
        c.task_frequency,

        d.department,

        f.first_name,
        f.last_name,

        p.lead_id,
        p.pono,
        p.df_number,
        p.basic_machine as po_basic_machine,
        p.po_attachment,
        p.company_name,

        k.first_name as marketingpersonfname,
        k.last_name as marketingpersonlname,

        MAX(qcd.mach_model_no) as mach_model_no,
MAX(annex.product_to_be_packed) as product_to_be_packed,
MAX(l.patient_type_id) as patient_type_id,


        (
        SELECT COUNT(id)
        FROM communication_ticket_system
        WHERE task_record_id = a.id
        AND df_id = a.df_id
        ) as ticket_count
        ');

        $this->db->from(
        'task_department_wise_scheduling a'
        );

        $this->db->join(
        'df_release b',
        'a.df_id=b.id',
        'left'
        );

        $this->db->join(
        'task_management c',
        'a.taskid=c.task_id',
        'left'
        );

        $this->db->join(
        'departments d',
        'a.department_id=d.department_id',
        'left'
        );

        $this->db->join(
        'system_users f',
        'a.assigned_user=f.user_id',
        'left'
        );

        $this->db->join(
        'poreceived p',
        'a.po_id=p.id',
        'left'
        );

        $this->db->join(
        'system_users k',
        'p.added_by=k.user_id',
        'left'
        );

        $this->db->join(
        'quotation_customer_data qcd',
        'p.lead_id=qcd.lead_id',
        'left'
        );

        $this->db->join(
        'quotation_annexture_1 annex',
        'qcd.id=annex.record_id',
        'left'
        );

        $this->db->join(
        'leads l',
        'p.lead_id=l.id',
        'left'
        );

        // =====================================================
        // FILTERS
        // =====================================================

        $this->db->where('a.task_status', 0);

        $this->db->where('a.on_hold', 0);

        $this->db->where(
        'a.assigned_user !=',
        0
        );

        $this->db->where(
        'a.end_date <',
        date('Y-m-d')
        );

        $this->db->where(
        'a.department_id !=',
        22
        );

        // =====================================================
        // HOD FILTER
        // =====================================================

        if (
        count($department_ids_filter) > 0
        ) {

        $this->db->where_in(
        'a.department_id',
        $department_ids_filter
        );
        }

        // =====================================================
        // NORMAL USER FILTER
        // =====================================================

        if ($admin_user_type == 3) {

        $this->db->where(
        'a.assigned_user',
        $user_id
        );
        }

        // =====================================================
        // EXTRA FILTERS
        // =====================================================

        if (
        $df_ids != ''
        &&
        $df_ids != 'ALL'
        ) {

        $this->db->where(
        'a.df_id',
        $df_ids
        );
        }

        if (
        $deptid != ''
        &&
        $deptid != 'ALL'
        ) {

        $this->db->where(
        'a.department_id',
        $deptid
        );
        }

        if (
        $usrid != ''
        &&
        $usrid != 'ALL'
        ) {

        $this->db->where(
        'a.assigned_user',
        $usrid
        );
        }

        // =====================================================
        // DF STATUS
        // =====================================================

        $this->db->group_start();

        $this->db->where('a.df_id', 0);

        $this->db->or_where(
        'b.df_status',
        0
        );

        $this->db->group_end();

        $this->db->group_by(array(

'a.id',
'a.taskid',
'a.df_id',
'a.po_id',
'a.department_id',
'a.assigned_user',

'a.start_date',
'a.end_date',
'a.taskupdatedontime',

'a.remarks',
'a.on_hold',
'a.task_status',

'b.df_no',
'b.df_upload',
'b.added_on',
'b.df_description',
'b.df_status',

'c.task_name',
'c.is_it_mom',
'c.sortorder',

'd.department',

'f.first_name',
'f.last_name',

'p.lead_id',
'p.pono',
'p.df_number',
'p.basic_machine',
'p.po_attachment',
'p.company_name',

'k.first_name',
'k.last_name'

));

       $this->db->order_by(
'a.end_date',
'ASC'
);

        $query = $this->db->get();

        $res = $query->result();

        // =====================================================
        // FINAL DATA
        // =====================================================

        $today_str = date('Y-m-d');

        $taskdata = [];

        $i = 1;

        foreach ($res as $row) {

          // =============================================
// OVERDUE FILTER LOGIC
// =============================================

$show = 1;

$days_overdue =
floor(
(
strtotime(date('Y-m-d'))
-
strtotime(
date(
'Y-m-d',
strtotime($row->end_date)
)
)
)
/86400
);

// =============================================
// 1-15 DAYS
// =============================================

if(
$filter_req=="1_15"
){

if(

$days_overdue < 1

||

$days_overdue > 15

){

$show=0;

}

}

// =============================================
// 16-30 DAYS
// =============================================

else if(
$filter_req=="16_30"
){

if(

$days_overdue < 16

||

$days_overdue > 30

){

$show=0;

}

}

// =============================================
// 30+ DAYS
// =============================================

else if(
$filter_req=="30_plus"
){

if(

$days_overdue <=30

){

$show=0;

}

}

        if ($show == 1) {

        // =============================================
        // DAYS
        // =============================================

        $pendingdays =
$this->task->getDays(
date('Y-m-d'),
$row->end_date,
1
);
        // =============================================
        // DF NO
        // =============================================

        $df_no = '';

        if ($row->df_id == 0) {

        $df_no = '-';

        } else {

        $df_no =
        strtoupper($row->df_no);
        }

        // =============================================
        // LAST UPDATE
        // =============================================

        $last_update = '';

        if (
        !empty(
        $row->taskupdatedontime
        )
        &&
        $row->taskupdatedontime
        !=
        '0000-00-00 00:00:00'
        ) {

        $last_update =
        date(
        'd-m-Y h:i A',
        strtotime(
        $row->taskupdatedontime
        )
        );
        }

        // =============================================
        // DEFAULT FLAGS
        // =============================================

        $action_type = '';
        $action_label = '';
        $action_url = '';

        $can_update_progress = false;
        $can_release_df = false;
        $can_reassign = false;
        $can_download_po = false;
        $can_download_df = false;
        $can_view_ticket = false;
        $is_admin_override = false;

        // =============================================
        // DOWNLOAD DF
        // =============================================

        $df_download_url = '';

        if (
        !empty($row->df_upload)
        &&
        $row->df_id > 0
        ) {

        $can_download_df = true;

        $df_download_url =
        sfdocument .
        'Taskdocument/dfattachment/' .
        $row->df_upload;
        }

        // =============================================
        // DOWNLOAD PO
        // =============================================

        $po_download_url = '';

        if (
        in_array(
        $department_id,
        [9, 10, 20]
        )
        &&
        !empty($row->po_attachment)
        ) {

        $can_download_po = true;

        $po_download_url =
        sfdocument .
        'Taskdocument/' .
        $row->po_attachment;
        }

        // =============================================
        // VIEW TICKET
        // =============================================

        if ($row->ticket_count > 0) {

        $can_view_ticket = true;
        }

        // =============================================
        // HOD REASSIGN
        // =============================================

        if (
        $admin_user_type == 2
        &&
        $user_id != $row->assigned_user
        ) {

        $can_reassign = true;
        }

        // =============================================
        // ADMIN OVERRIDE
        // =============================================

        if (
        $user_id == 61
        ||
        $user_id == 161
        ) {

        $is_admin_override = true;
        }

       // =============================================
// UPDATE PROGRESS ACCESS LOGIC
// =============================================

$pms_action_required = false;
$pms_action_message = '';

if (
    $user_id == $row->assigned_user
    ||
    $is_admin_override == true
) {

    // =========================================
    // RELEASE DF
    // PMS ONLY
    // =========================================

    if (
        $row->df_id == 0
        &&
        $row->taskid == 2
    ) {

        $can_release_df = true;

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'release_df';

        $action_label = 'Release DF';

        $action_url =
            'Dashboard/releasedf/' .
            $row->id .
            '/' .
            $row->df_number;
    }

    // =========================================
    // DF MEETING MOM
    // MOBILE ACCESSIBLE
    // =========================================

    else if ($row->is_it_mom == 1) {

        $can_update_progress = true;

        $action_type = 'df_meeting_mom';

        $action_label = 'DF Meeting MOM';

        $action_url =
            'Task/dfmeeting/' .
            $row->id .
            '/' .
            $row->df_id;
    }

    // =========================================
    // FILL DESIGN FORM
    // PMS ONLY
    // =========================================

    else if (
        $row->df_id == 0
        &&
        $row->taskid == 114
    ) {

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'fill_df';

        $action_label = 'Fill Design Form';

        if (
            in_array(
                $row->mach_model_no,
                [300, 600]
            )
        ) {

            $action_url =
                'Dashboard/df_form_600/' .
                $row->id .
                '/' .
                $row->po_id .
                '/' .
                $row->lead_id;

        } else {

            if (
                $row->product_to_be_packed == 2
            ) {

                $action_url =
                    'Dashboard/powder_df_form_design/' .
                    $row->id .
                    '/' .
                    $row->po_id .
                    '/' .
                    $row->lead_id;

            } else {

                $action_url =
                    'Dashboard/df_project_form/' .
                    $row->id .
                    '/' .
                    $row->po_id .
                    '/' .
                    $row->lead_id;
            }
        }
    }

    // =========================================
    // DF REVIEW
    // PMS ONLY
    // =========================================

    else if (
        $row->df_id == 0
        &&
        $row->taskid == 86
    ) {

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'df_review';

        $action_label =
            'DF Review Meeting';

        if ($row->po_basic_machine == 1) {

            $action_url =
                'Dashboard/edit_df_project_form/' .
                $row->po_id .
                '/' .
                $row->lead_id .
                '/' .
                $row->id;

        } else {

            if (
                in_array(
                    $row->mach_model_no,
                    [300, 600]
                )
            ) {

                $action_url =
                    'Dashboard/design_form_600_edit/' .
                    $row->po_id .
                    '/' .
                    $row->lead_id .
                    '/' .
                    $row->id;

            } else {

                if (
                    $row->product_to_be_packed == 2
                ) {

                    $action_url =
                        'Dashboard/edit_powder_df_form/' .
                        $row->po_id .
                        '/' .
                        $row->lead_id .
                        '/' .
                        $row->id;

                } else {

                    $action_url =
                        'Dashboard/edit_df_project_form/' .
                        $row->po_id .
                        '/' .
                        $row->lead_id .
                        '/' .
                        $row->id;
                }
            }
        }
    }

    // =========================================
    // CREATE PI
    // PMS ONLY
    // =========================================

    else if (
        $row->df_id == 0
        &&
        $row->taskid == 3
    ) {

        $can_update_progress = false;

        $pms_action_required = true;

        $pms_action_message =
            'Can be done in PMS Software';

        $action_type = 'create_pi';

        $action_label = 'Create PI';

        if (
            $row->patient_type_id == 1
        ) {

            $action_url =
                'Form/performa_invoice/' .
                $row->po_id .
                '/' .
                $row->lead_id .
                '/' .
                $row->id;

        } else {

            $action_url =
                'Form/export_performa_invoice/' .
                $row->po_id .
                '/' .
                $row->lead_id .
                '/' .
                $row->id;
        }
    }

    // =========================================
    // NORMAL UPDATE
    // MOBILE ACCESSIBLE
    // =========================================

    else {

        $can_update_progress = true;

        $action_type = 'update_progress';

        $action_label =
            'Update Progress';
    }
}

        $taskdata[] = [

        'sr_no' => $i,

        'task_record_id' =>
        $row->id,

        'task_id' =>
        $row->taskid,

        'df_id' =>
        $row->df_id,

        'po_id' =>
        $row->po_id,

        'df_no' =>
        $df_no,

        'df_description' =>
        $row->df_description,

        'task_name' =>
        ucwords(
        strtolower(
        $row->task_name
        )
        ),

        'department' =>
        ucwords(
        strtolower(
        $row->department
        )
        ),

        'company_name' =>
        $row->company_name,

        'marketing_person' =>
        ucwords(
        strtolower(
        $row->marketingpersonfname
        .
        ' '
        .
        $row->marketingpersonlname
        )
        ),

        'assigned_to' =>
        ucwords(
        strtolower(
        $row->first_name
        .
        ' '
        .
        $row->last_name
        )
        ),

        'remarks' =>
        $row->remarks,

        'start_date' =>
        date(
        'd-m-Y',
        strtotime(
        $row->start_date
        )
        ),

        'end_date' =>
        date(
        'd-m-Y',
        strtotime(
        $row->end_date
        )
        ),

        'pending_days' =>
        $pendingdays,

        'last_update' =>
        $last_update,

        'ticket_count' =>
        (int)$row->ticket_count,

        // =====================================
        // PERMISSIONS
        // =====================================

        'permissions' => [

    'can_update_progress' =>
        $can_update_progress,

    'can_release_df' =>
        $can_release_df,

    'can_reassign' =>
        $can_reassign,

    'can_download_po' =>
        $can_download_po,

    'can_download_df' =>
        $can_download_df,

    'can_view_ticket' =>
        $can_view_ticket,

    'is_admin_override' =>
        $is_admin_override,

    'pms_action_required' =>
        $pms_action_required,

    'pms_action_message' =>
        $pms_action_message,
],

        // =====================================
        // DOWNLOADS
        // =====================================

        'downloads' => [

        'df_download_url' =>
        $df_download_url,

        'po_download_url' =>
        $po_download_url,
        ],

        // =====================================
        // ACTION
        // =====================================

        'action' => [

        'type' =>
        $action_type,

        'label' =>
        $action_label,

        'url' =>
        $action_url,
        ]
        ];
        $i++;
        }
        }

        // =====================================================
        // RESPONSE
        // =====================================================

        echo json_encode([

        'status' => true,

        'message' =>
        'Overdue Task List Found',

        'total_records' =>
        count($taskdata),

        'data' => $taskdata
        ]);

        } catch (Exception $e) {

        echo json_encode([

        'status' => false,

        'message' =>
        $e->getMessage(),

        'data' => []
        ]);
        }
}


public function completed_task_list_api()
{

header('Content-Type: application/json');

try{

$json=json_decode(
file_get_contents("php://input"),
true
);

$user_id=$json['user_id'] ?? 0;

$role_id=$json['role_id'] ?? 0;

$department_id=
$json['department_id'] ?? 0;

$filter_req=
$json['filter'] ?? '';

$deptid=
$json['department_filter'] ?? '';

$usrid=
$json['user_filter'] ?? '';

$df_ids=
$json['df_id'] ?? '';

$admin_user_type=
$this->getUserType(
$role_id,
$user_id
);

$department_ids_filter=[];

if($admin_user_type==2){

$department_ids_filter=
$this->task->getAssignedDepartment(
$user_id
);

}

$this->db->select('

a.id,
a.taskid,
a.df_id,
a.po_id,
a.department_id,
a.assigned_user,
a.start_date,
a.end_date,
a.taskupdatedontime,
a.remarks,
a.on_hold,
a.task_status,

b.df_no,
b.df_upload,
b.df_description,

c.task_name,
c.sortorder,
c.is_it_mom,

d.department,

f.first_name,
f.last_name,

p.lead_id,
p.df_number,
p.basic_machine,
p.po_attachment,
p.company_name,

k.first_name as marketingpersonfname,
k.last_name as marketingpersonlname,

(
SELECT COUNT(id)

FROM communication_ticket_system

WHERE task_record_id=a.id

AND df_id=a.df_id

) ticket_count

');

$this->db->from(
'task_department_wise_scheduling a'
);

$this->db->join(
'df_release b',
'a.df_id=b.id',
'left'
);

$this->db->join(
'task_management c',
'a.taskid=c.task_id',
'left'
);

$this->db->join(
'departments d',
'a.department_id=d.department_id',
'left'
);

$this->db->join(
'system_users f',
'a.assigned_user=f.user_id',
'left'
);

$this->db->join(
'poreceived p',
'a.po_id=p.id',
'left'
);

$this->db->join(
'system_users k',
'p.added_by=k.user_id',
'left'
);


/// COMPLETED TASKS

$this->db->where(
'a.task_status',
1
);

$this->db->where(
'a.assigned_user !=',
0
);

$this->db->where(
'a.department_id !=',
22
);

if(
count($department_ids_filter)>0
){

$this->db->where_in(
'a.department_id',
$department_ids_filter
);

}

if(
$admin_user_type==3
){

$this->db->where(
'a.assigned_user',
$user_id
);

}

if(
$df_ids!=''
&&
$df_ids!='ALL'
){

$this->db->where(
'a.df_id',
$df_ids
);

}

if(
$deptid!=''
&&
$deptid!='ALL'
){

$this->db->where(
'a.department_id',
$deptid
);

}

if(
$usrid!=''
&&
$usrid!='ALL'
){

$this->db->where(
'a.assigned_user',
$usrid
);

}

$this->db->order_by(
'a.taskupdatedontime',
'DESC'
);

$res=
$this->db->get()
->result();

$taskdata=[];

$i=1;

foreach($res as $row){

$show=1;

$completed_date=
date(
'Y-m-d',
strtotime(
$row->taskupdatedontime
)
);


/// TODAY

if(
$filter_req=="today"
){

if(
$completed_date
!=
date('Y-m-d')
){

$show=0;

}

}

/// WEEK

else if(
$filter_req=="week"
){

$week_start=
date(
'Y-m-d',
strtotime(
'monday this week'
)
);

if(

$completed_date
<
$week_start

){

$show=0;

}

}

/// MONTH

else if(
$filter_req=="month"
){

if(

date(
'Y-m',
strtotime(
$completed_date
)
)

!=

date('Y-m')

){

$show=0;

}

}

if($show==0){

continue;

}

/// EARLY/LATE CALCULATION

$diff=

floor(

(

strtotime($row->taskupdatedontime)

-

strtotime($row->end_date)

)

/86400

);

if($diff<0){

$completion_text=

abs($diff)
.
" Days Early";

}

else if($diff>0){

$completion_text=

$diff
.
" Days Late";

}

else{

$completion_text=
"On Time";

}

$taskdata[]=[

'sr_no'=>$i++,

'task_record_id'=>$row->id,

'task_id'=>$row->taskid,

'df_id'=>$row->df_id,

'po_id'=>$row->po_id,

'df_no'=>$row->df_no,

'df_description'=>
$row->df_description,

'task_name'=>
ucwords(
strtolower(
$row->task_name
)
),

'department'=>
$row->department,

'assigned_to'=>

trim(

$row->first_name
.
' '
.
$row->last_name

),

'company_name'=>
$row->company_name,

'remarks'=>
$row->remarks,

'start_date'=>

date(
'd-m-Y',
strtotime(
$row->start_date
)
),

'end_date'=>

date(
'd-m-Y',
strtotime(
$row->end_date
)
),

'pending_days'=>

$completion_text,

'last_update'=>

date(

'd-m-Y h:i A',

strtotime(
$row->taskupdatedontime
)

),

'ticket_count'=>

(int)$row->ticket_count,

'permissions'=>[],

'downloads'=>[],

'action'=>[]

];

}

echo json_encode([

'status'=>true,

'message'=>

'Completed Tasks Found',

'total_records'=>

count($taskdata),

'data'=>

$taskdata

]);

}catch(Exception $e){

echo json_encode([

'status'=>false,

'message'=>$e->getMessage(),

'data'=>[]

]);

}

}


public function unassigned_task_list_api()
{

    $request = json_decode(
        file_get_contents("php://input"),
        true
    );

    $user_id =
    (int)($request['user_id'] ?? 0);

    $roleid =
    (int)($request['role_id'] ?? 0);

    $departmentid = [];

    /*
    1 = ADMIN
    2 = DEPARTMENT HEAD
    */

    $admin_user_type = 2;

    $this->load->model(
        'Dashboard_model',
        'dashboardmodel'
    );

    $super_admin_roles =
    $this->dashboardmodel
        ->getsuperadminuserole();

    if (

        in_array(
            $roleid,
            $super_admin_roles,
            true
        )

        ||

        in_array(
            $user_id,
            [189,209],
            true
        )

    ) {

        $admin_user_type = 1;

    }

    /*
    DEPARTMENT HEAD LOGIC
    */

    if($admin_user_type == 2){

        if($user_id == 215){

            $departmentid[] = 12;

        }

        $q =

        $this->db

        ->select(
            'department_id'
        )

        ->from(
            'prestogroup_teams'
        )

        ->where(
            'team_leader',
            $user_id
        )

        ->get();

        if(
        $q->num_rows()>0
        ){

            foreach(
            $q->result()
            as $row
            ){

                $departmentid[] =
                $row->department_id;

            }

        }

        $departmentid =
        array_unique(
            $departmentid
        );

    }


    /*
    DF LIST
    */

    $this->db

    ->select('

        b.id df_id,

        b.df_no,

        b.df_description,

        b.added_on,

        b.df_upload

    ')

    ->from(
        'task_department_wise_scheduling a'
    )

    ->join(
        'df_release b',
        'a.df_id=b.id'
    );

    if(
    count($departmentid)>0
    ){

        $this->db->where_in(

            'a.department_id',

            $departmentid

        );

    }

    $this->db
        ->where(
            'a.department_id !=',
            22
        );

    $this->db
        ->where(
            'a.df_id !=',
            0
        );

    $this->db
        ->group_start()

        ->where(
            'a.assigned_user',
            ''
        )

        ->or_where(
            'a.assigned_user',
            0
        )

        ->or_where(
            'a.assigned_user IS NULL',
            null,
            false
        )

        ->group_end();

    $this->db
        ->where(
            'b.df_status',
            0
        );

    $this->db
        ->group_by(
            'b.id'
        );

    $this->db
        ->order_by(
            'b.df_no',
            'ASC'
        );

    $dfs =

    $this->db
        ->get()
        ->result();

    $response=[];


    foreach(
    $dfs as $df
    ){

        $departments=[];

        $this->db

        ->select('

            a.department_id,

            b.department

        ')

        ->from(
            'task_department_wise_scheduling a'
        )

        ->join(

            'departments b',

            'a.department_id=
             b.department_id'

        )

        ->where(
            'a.df_id',
            $df->df_id
        );

        if(
        count($departmentid)>0
        ){

            $this->db->where_in(

                'a.department_id',

                $departmentid

            );

        }

        $this->db

        ->group_start()

        ->where(
            'a.assigned_user',
            ''
        )

        ->or_where(
            'a.assigned_user',
            0
        )

        ->or_where(
            'a.assigned_user IS NULL',
            null,
            false
        )

        ->group_end();

        $deps =

        $this->db

        ->group_by(
            'a.department_id'
        )

        ->get()

        ->result();


        foreach(
        $deps as $dep
        ){

            /*
            DEPARTMENT HEAD
            */

            $departmenthead="";

            $head=

            $this->db

            ->select('

                s.first_name,

                s.last_name

            ')

            ->from(
                'prestogroup_teams p'
            )

            ->join(

                'system_users s',

                'p.team_leader=
                 s.user_id',

                'left'

            )

            ->where(

                'p.department_id',

                $dep->department_id

            )

            ->get()

            ->row();


            if($head){

                $departmenthead=

                strtoupper(

                    trim(

                        $head->first_name

                        ." ".

                        $head->last_name

                    )

                );

            }


            /*
            DEPARTMENT USERS
            */

            $departmentUsers=

            $this->db

            ->select('

                user_id,

                CONCAT(

                IFNULL(first_name,""),

                " ",

                IFNULL(last_name,"")

                ) user_name

            ')

            ->from(
                'system_users'
            )

            ->where(
                'department_id',
                $dep->department_id
            )

            ->where(
                'user_status',
                1
            )

            ->order_by(
                'first_name',
                'ASC'
            )

            ->get()

            ->result_array();


            foreach(
            $departmentUsers
            as &$usr
            ){

                $usr['user_name']=

                trim(
                    $usr['user_name']
                );

            }

            unset($usr);


            /*
            TASKS
            */

            $tasks=

            $this->db

            ->select('

                a.id task_record_id,

                b.task_name,

                a.start_date,

                a.end_date

            ')

            ->from(
                'task_department_wise_scheduling a'
            )

            ->join(

                'task_management b',

                'a.taskid=b.task_id'

            )

            ->where(
                'a.df_id',
                $df->df_id
            )

            ->where(
                'a.department_id',
                $dep->department_id
            )

            ->group_start()

            ->where(
                'a.assigned_user',
                ''
            )

            ->or_where(
                'a.assigned_user',
                0
            )

            ->or_where(
                'a.assigned_user IS NULL',
                null,
                false
            )

            ->group_end()

            ->get()

            ->result_array();


            $departments[]=[

                "department_id"=>
                $dep->department_id,

                "department"=>
                $dep->department,

                "department_head"=>
                $departmenthead,

                "department_users"=>

$departmentUsers ?? [],

                "pending_task_count"=>
                count($tasks),

                "tasks"=>
                $tasks,

            ];

        }


        $response[]=[

            "df_id"=>
            $df->df_id,

            "df_no"=>
            $df->df_no,

            "df_description"=>
            $df->df_description,

            "release_date"=>

            date(

                'd-m-Y',

                strtotime(
                    $df->added_on
                )

            ),

            "df_download_url"=>

            sfdocument

            ."Taskdocument/dfattachment/"

            .$df->df_upload,

            "department_count"=>

            count(
                $departments
            ),

            "departments"=>

            $departments,

        ];

    }


    echo json_encode([

        "status"=>true,

        "count"=>count(
            $response
        ),

        "data"=>$response,

    ]);

}


public function assign_unassigned_tasks_api()
{


    $request = json_decode(
        file_get_contents("php://input"),
        true
    );

    $assigned_by =
    (int)($request['user_id'] ?? 0);

    $message ="Hey everyone, a new task has been assigned to me in our task management software. Please log in to check the details. Let me know if you need anything.";

    $assignments =
    $request['assignments'] ?? [];

    if(
        empty($assignments)
    ){

        echo json_encode([

            "status"=>false,

            "message"=>
            "No task selected"

        ]);

        return;

    }

    /*
    ===================================
    GET ASSIGNER NAME
    ===================================
    */

    $assigner =

    $this->db

    ->select('first_name,last_name')

    ->from('system_users')

    ->where(
        'user_id',
        $assigned_by
    )

    ->get()

    ->row();

    $assignedbyuser =

    $assigner

        ?

    ucfirst(

        $assigner->first_name

        ." ".

        $assigner->last_name

    )

        :

    "PMS Software";



    /*
    ===================================
    USER CACHE
    ===================================
    */

    $userIds=[];

    foreach(
    $assignments as $a
    ){

        if(
        !empty(
            $a['assigned_user']
        )
        ){

            $userIds[]=
            (int)$a['assigned_user'];

        }

    }

    $userIds=
    array_unique(
        $userIds
    );

    $userCache=[];

    if(
    !empty($userIds)
    ){

        $users=

        $this->db

        ->select(
            'user_id,
             department_id'
        )

        ->from(
            'system_users'
        )

        ->where_in(
            'user_id',
            $userIds
        )

        ->get()

        ->result();

        foreach(
        $users as $u
        ){

            $userCache[
            $u->user_id
            ]=

            $u->department_id;

        }

    }

    /*
    ===================================
    PREPARE BATCH
    ===================================
    */

    $updateBatch=[];

    $insertBatch=[];

    $notificationDigest=[];



    foreach(
    $assignments as $row
    ){

        $assignedUser=

        (int)($row[
        'user_id'
        ] ?? 0);

        if(
        $assignedUser<=0
        ){
            continue;
        }

        $taskRecordId=

        (int)($row[
        'task_record_id'
        ] ?? 0);

        $dfId=

        (int)($row[
        'df_id'
        ] ?? 0);

        /*
        UPDATE TASK
        */

        $updateBatch[]=[

            "id"=>
            $taskRecordId,

            "assigned_user"=>
            $assignedUser,

            "assigned_by"=>
            $assigned_by,

            "assigned_on"=>
            date(
                'Y-m-d H:i:s'
            )

        ];

        /*
        ALERT
        */

        $insertBatch[]=[

            "df_id"=>
            $dfId,

            "message"=>
            $message,

            "added_by"=>
            $assigned_by,

            "added_on"=>
            date(
                'Y-m-d H:i:s'
            ),

            "department_id"=>

            $userCache[
            $assignedUser
            ] ?? null,

            "user_id"=>
            $assignedUser

        ];

        /*
        NOTIFICATION
        */

        $notificationDigest[
        $assignedUser
        ]['df_ids'][]

            =

        $dfId;

    }

    if(
    empty($updateBatch)
    ){

        echo json_encode([

            "status"=>false,

            "message"=>
            "No valid assignment found"

        ]);

        return;

    }

    /*
    ===================================
    DB TRANSACTION
    ===================================
    */

    $this->db->trans_start();

    $this->db->update_batch(

        'task_department_wise_scheduling',

        $updateBatch,

        'id'

    );

    $this->db->insert_batch(

        'task_intimation_alert',

        $insertBatch

    );

    $this->db->trans_complete();

    if(
    !$this->db
          ->trans_status()
    ){

        echo json_encode([

            "status"=>false,

            "message"=>
            "Assignment failed"

        ]);

        return;

    }

    /*
    ===================================
    DF CACHE
    ===================================
    */

    $allDfIds=[];

    foreach(
    $notificationDigest
    as $n
    ){

        $allDfIds=
        array_merge(

            $allDfIds,

            $n['df_ids']

        );

    }

    $dfCache=[];

    if(
    !empty($allDfIds)
    ){

        $dfs=

        $this->db

        ->select(
            'id,df_no'
        )

        ->from(
            'df_release'
        )

        ->where_in(

            'id',

            array_unique(
                $allDfIds
            )

        )

        ->get()

        ->result();

        foreach(
        $dfs as $d
        ){

            $dfCache[
            $d->id
            ]=

            $d->df_no;

        }

    }

    /*
    ===================================
    SEND NOTIFICATION
    ===================================
    */

    foreach(
    $notificationDigest
    as $userId=>$data
    ){

        $dfList=[];

        foreach(

        array_unique(
            $data['df_ids']
        )

        as $dfId

        ){

            $dfList[] =

            $dfCache[
            $dfId
            ]

            ??

            "DF#".$dfId;

        }

        $this->task
             ->send_digest_notification(

                 $userId,

                 $dfList,

                 $message,

                 $assignedbyuser

             );

    }

    echo json_encode([

        "status"=>true,

        "message"=>

        "Tasks assigned successfully"

    ]);

}


public function early_closure_approval_list_api()
{
    $input = json_decode(file_get_contents("php://input"), true);

    $user_id = isset($input['user_id']) ? (int)$input['user_id'] : 0;
    $role_id = isset($input['role_id']) ? (int)$input['role_id'] : 0;
   // echo $role_id; exit;

    $department_id = array();

    $this->load->model('Dashboard_model', 'dashboardmodel');

    $admin_user_type = 0;

    $super_admin_roles = $this->dashboardmodel->getsuperadminuserole();
    //echo "<pre>"; print_r($super_admin_roles); exit;
   

    if (
        in_array($role_id, $super_admin_roles)
        || in_array($user_id, array(189,209))
    ) {

        $admin_user_type = 1;

    } else {

        $assigned_departments =
            $this->task->getAssignedDepartment($user_id);

        if (!empty($assigned_departments)) {

            $admin_user_type = 2;

            $department_id = $assigned_departments;

        } else {

            $admin_user_type = 3;
        }
    }

//    echo $admin_user_type; exit;

    $this->db
        ->select("
            a.id as task_id,
            a.df_id,
            a.taskid,
            a.department_id,
            a.assigned_user,
            a.start_date,
            a.end_date as planned_completion_date,
            a.task_completed_on as actual_completion_date,
            a.remarks,

            b.task_name,

            c.df_no,
            c.df_description,

            d.department,

            CONCAT(u.first_name,' ',u.last_name)
            as accountable_person
        ")
        ->from('task_department_wise_scheduling a')
        ->join('task_management b','a.taskid=b.task_id','left')
        ->join('df_release c','a.df_id=c.id','left')
        ->join('departments d','a.department_id=d.department_id','left')
        ->join('system_users u','a.assigned_user=u.user_id','left')

        ->where('a.task_status',2)

        // only EARLY completed tasks
        ->where('DATE(a.task_completed_on) < DATE(a.end_date)');

    if (!empty($department_id)) {

        $this->db->where_in(
            'a.department_id',
            $department_id
        );
    }

    if ($admin_user_type == 3) {

        $this->db->where(
            'a.assigned_user',
            $user_id
        );
    }

    $this->db->order_by(
        'a.task_completed_on',
        'DESC'
    );

    $rows = $this->db->get()->result_array();

    $data = array();

    foreach($rows as $row)
    {
        $planned =
            strtotime(
                $row['planned_completion_date']
            );

        $actual =
            strtotime(
                $row['actual_completion_date']
            );

        $early_days =
            floor(
                ($planned - $actual) / 86400
            );

        $data[] = array(

            "task_id" =>
                $row['task_id'],

            "df_no" =>
                $row['df_no'],

            "task_name" =>
                $row['task_name'],

            "department" =>
                strtoupper(
                    $row['department']
                ),

            "accountable_person" =>
                $row['accountable_person'],

            "machine_name" =>
                $row['df_description'],

            "start_date" =>
                date(
                    'Y-m-d',
                    strtotime(
                        $row['start_date']
                    )
                ),

            "planned_completion_date" =>
                date(
                    'Y-m-d',
                    strtotime(
                        $row['planned_completion_date']
                    )
                ),

            "actual_completion_date" =>
                date(
                    'Y-m-d',
                    strtotime(
                        $row['actual_completion_date']
                    )
                ),

            "early_days" =>
                $early_days,

            "remarks" =>
                $row['remarks']
        );
    }

    echo json_encode(array(
        "status" => true,
        "data" => $data
    ));
}


public function early_closure_approval_action_api()
{
    //exit;
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    $task_id = isset($input['task_id'])
        ? (int)$input['task_id']
        : 0;

    $action = strtoupper(
        trim($input['action'] ?? '')
    );

    $user_id =
         isset($input['user_id'])
        ? (int)$input['user_id']
        : 0;
        // echo $user_id; exit;

    if(empty($task_id))
    {
        echo json_encode(array(
            'status' => false,
            'message' => 'Task ID Missing'
        ));
        return;
    }

    $q = $this->db
        ->select('
            a.id,
            c.task_name,
            d.df_no,
            b.user_id,
            b.first_name,
            b.title,
            b.last_name,
            b.email
        ')
        ->from('task_department_wise_scheduling a')
        ->join(
            'task_management c',
            'a.taskid=c.task_id',
            'left'
        )
        ->join(
            'df_release d',
            'a.df_id=d.id',
            'left'
        )
        ->join(
            'system_users b',
            'a.assigned_user=b.user_id',
            'left'
        )
        ->where('a.id', $task_id)
        ->get();

    if($q->num_rows() == 0)
    {
        echo json_encode(array(
            'status' => false,
            'message' => 'Task Not Found'
        ));
        return;
    }

    $row = $q->row();

    $task_status =
        ($action == 'APPROVE')
        ? 1
        : 0;

    $update_data = array(

        'task_status'    => $task_status,

        'checked_by_sir' => $user_id,

        'checked_on'     => date('Y-m-d H:i:s')

    );

    $res = $this->db
        ->where('id', $task_id)
        ->update(
            'task_department_wise_scheduling',
            $update_data
        );

    if(!$res)
    {
        echo json_encode(array(
            'status' => false,
            'message' => 'Update Failed'
        ));
        return;
    }

    $username =
        trim(
            $row->title.' '.
            $row->first_name.' '.
            $row->last_name
        );

    if($action == 'APPROVE')
    {
        $this->sendTaskApprovalEmail(
            $row->email,
            $username,
            $row->task_name,
            $row->df_no,
            $user_id
        );

        log_message(
            'info',
            'Task approved. Email sent to '.$row->email
        );

        echo json_encode(array(
            'status' => true,
            'message' => 'Task Approved Successfully'
        ));
    }
    else
    {
        $this->sendTaskRejectionEmail(
            $row->email,
            $username,
            $row->task_name,
            $row->df_no,
            $user_id
        );

        log_message(
            'info',
            'Task rejected. Email sent to '.$row->email
        );

        echo json_encode(array(
            'status' => true,
            'message' => 'Task Rejected Successfully'
        ));
    }
}


private function sendTaskRejectionEmail($email, $name, $task_name, $df_no,$user_id)
{
    // Load email library if not already loaded
    $this->load->library('email');
    $user_id = $user_id;
    $q= $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $doneby);
    $markedby = ucwords(strtolower($doneby->title." ".$doneby->first_name." ".$doneby->last_name));
    // Set email sender, recipient, and subject
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging - Task Rejected');
    $this->email->to($email);
    $this->email->cc('mangleshup@gmail.com');
    $this->email->subject('Task Rejection Notification - ' . $task_name);

    // Email message
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task Rejection Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
        <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="background-color: #4872b8; padding: 20px; text-align: center;">
                <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Company Logo" style="max-width: 150px;">
            </div>
            
            <div style="padding: 20px;">
                <h2 style="color: #e74c3c; text-align: center;">Task Rejected</h2>

                <p style="font-size: 16px; line-height: 1.6;">Dear ' . ucwords($name) . ',</p>

                <p style="font-size: 16px; line-height: 1.6;">
                    We would like to inform you that your task titled <strong>' . $task_name . '</strong> with DF No. <strong>' . $df_no . '</strong> has been <strong>rejected</strong> by <strong>'.$markedby.'</strong>.
                </p>

                <p style="font-size: 16px; line-height: 1.6;">
                    Please review the task details and make the necessary adjustments to meet the required standards. We encourage you to review the feedback and resubmit the task for approval.
                </p>

                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">DF No.</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $df_no . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Task Name</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $task_name . '</td>
                    </tr>
                   
                </table>

                <p style="font-size: 16px; line-height: 1.6;">
                    If you have any questions or need further guidance, please reach out to us.
                </p>

               
            </div>

            <div style="background-color: #4872b8; padding: 10px; text-align: center; color: #fff;">
                &copy; ' . date('Y') . ' Shubham Flexible Packaging. All Rights Reserved.
            </div>
        </div>
    </body>
    </html>
    ';

    // Set email message
    $this->email->message($message);

    // Send the email
    if ($this->email->send()) {
        log_message('info', 'Rejection email successfully sent to ' . $email);
    } else {
        log_message('error', 'Failed to send rejection email to ' . $email);
    }
}


private function sendTaskApprovalEmail($email, $name, $task_name, $df_no,$user_id)
{


    $q= $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $doneby);
    $markedby = ucwords(strtolower($doneby->title." ".$doneby->first_name." ".$doneby->last_name));
    // Set email sender, recipient, and subject
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging - Task Approved Notification');
    $this->email->to($email);
    $this->email->cc('mangleshup@gmail.com');
    $this->email->subject('Task Approval Notification - ' . $task_name);

    // Email message
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task Approval Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
        <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="background-color: #4872b8; padding: 20px; text-align: center;">
                <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Company Logo" style="max-width: 150px;">
            </div>
            
            <div style="padding: 20px;">
                <h2 style="color: #4872b8; text-align: center;">Task Approved!</h2>

                <p style="font-size: 16px; line-height: 1.6;">Dear ' . ucwords(strtolower($name)) . ',</p>

                <p style="font-size: 16px; line-height: 1.6;">
                    Congratulations! Your task titled <strong>' . ucwords(strtolower($task_name)) . '</strong> with DF No. <strong>' . $df_no . '</strong> has been approved by <strong>'.$markedby.'</strong>.
                </p>

                <p style="font-size: 16px; line-height: 1.6;">
                    We appreciate your hard work and dedication to complete this task on time. Please continue to maintain this level of excellence in the future.
                </p>

                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">DF No.</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $df_no . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Task Name</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . ucwords(strtolower($task_name)) . '</td>
                    </tr>
                    
                </table>

                <p style="font-size: 16px; line-height: 1.6;">
                    If you have any questions or need further details, please do not hesitate to contact us.
                </p>

               
            </div>

            <div style="background-color: #4872b8; padding: 10px; text-align: center; color: #fff;">
                &copy; ' . date('Y') . ' Shubham Flexible Packaging. All Rights Reserved.
            </div>
        </div>
    </body>
    </html>
    ';

    // Set email message
    $this->email->message($message);

    // Send the email
    if ($this->email->send()) {
        log_message('info', 'Approval email successfully sent to ' . $email);
    } else {
        log_message('error', 'Failed to send approval email to ' . $email);
    }
}


public function get_mom_participants()
{
    header('Content-Type: application/json');

    $json = json_decode(file_get_contents('php://input'), true);

    $user_id = isset($json['user_id'])
        ? (int)$json['user_id']
        : 0;

    if(empty($user_id))
    {
        echo json_encode([
            'status'  => false,
            'message' => 'User ID required'
        ]);
        exit;
    }

    $default_user = $this->db
    ->select("
        user_id,
        CONCAT_WS(' ',
            CONCAT(
                UCASE(LEFT(first_name,1)),
                LCASE(SUBSTRING(first_name,2))
            ),
            CONCAT(
                UCASE(LEFT(last_name,1)),
                LCASE(SUBSTRING(last_name,2))
            )
        ) AS name,
        email
    ")
    ->from('system_users')
    ->where('user_id', $user_id)
    ->where('user_status', 1)
    ->get()
    ->row_array();


    if($default_user)
    {
        $default_user['selected'] = true;
        $default_user['is_default'] = true;
    }

  $users = $this->db
    ->select("
        user_id,
        CONCAT_WS(' ',
            CONCAT(
                UCASE(LEFT(first_name,1)),
                LCASE(SUBSTRING(first_name,2))
            ),
            CONCAT(
                UCASE(LEFT(last_name,1)),
                LCASE(SUBSTRING(last_name,2))
            )
        ) AS name,
        email
    ")
    ->from('system_users')
    ->where('user_status', 1)
    ->where('user_id !=', $user_id)
    ->order_by('first_name', 'ASC')
    ->get()
    ->result_array();

    echo json_encode([
        'status'  => true,
        'message' => 'Users loaded successfully',
        'data'    => [
            'default_user' => $default_user,
            'users'        => $users
        ]
    ]);
}   


public function save_df_meeting_mom()
{
   
    header('Content-Type: application/json');

    try
    {
        $json = json_decode(file_get_contents('php://input'), true);

        $user_id      = (int)($json['user_id'] ?? 0);
        $record_id    = (int)($json['record_id'] ?? 0);
        $df_id        = (int)($json['df_id'] ?? 0);

        $agenda       = trim($json['agenda'] ?? '');
        $meeting_date = trim($json['meeting_date'] ?? '');
        $meeting_time = trim($json['meeting_time'] ?? '');
        $emailcc      = trim($json['emailcc'] ?? '');

        $participants = $json['participants'] ?? [];
        $dfmompoints  = $json['mom_points'] ?? [];

        if(
            empty($user_id) ||
            empty($record_id) ||
            empty($df_id) ||
            empty($agenda)
        )
        {
            echo json_encode([
                'status' => false,
                'message' => 'Required fields missing'
            ]);
            exit;
        }

        date_default_timezone_set("Asia/Kolkata");

        $date = date('Y-m-d H:i:s');

        $data = array(
            'dfid'       => $df_id,
            'recordid'   => $record_id,
            'particular' => $agenda,
            'date'       => date('Y-m-d', strtotime($meeting_date)),
            'time'       => date('H:i:s', strtotime($meeting_time)),
            'cc_record'  => $emailcc,
            'added_on'   => $date,
            'added_by'   => $user_id
        );

        $q = $this->db
            ->select('id')
            ->from('dfwise_mom')
            ->where('dfid', $df_id)
            ->get();

        if($q->num_rows() > 0)
        {
            $row = $q->row();
            $id = $row->id;
        }
        else
        {
            $this->db->insert('dfwise_mom', $data);
            $id = $this->db->insert_id();
        }

        /*
        |--------------------------------------------------------------------------
        | Participants
        |--------------------------------------------------------------------------
        */

        if(!empty($participants))
        {
            foreach($participants as $participant_id)
            {
                if(!empty($participant_id))
                {
                    $participantData = array(
                        'dfid'      => $df_id,
                        'user_id'   => $participant_id,
                        'added_on'  => date('Y-m-d H:i:s'),
                        'added_by'  => $user_id
                    );

                    $this->db->insert(
                        'dfwise_mom_participant',
                        $participantData
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MOM History
        |--------------------------------------------------------------------------
        */

        $data1 = array(
            'df_id'     => $df_id,
            'added_on'  => date('Y-m-d H:i:s'),
            'added_by'  => $user_id
        );

        $this->db->insert(
            'dfmom_history_date',
            $data1
        );

        /*
        |--------------------------------------------------------------------------
        | Email Body
        |--------------------------------------------------------------------------
        */

        $pcular = $agenda;

        $message =
        "Dear Members,<br>
        Please find the MOM of todays DF Meeting.
        ".$pcular."<br/><br/>";

        $message .= '
        <table style="width:100%;border-collapse:collapse;">
            <thead>

            <tr>
                <td align="left" colspan="2"
                    style="background:#fff;padding:15px 0px;">
                    <img
                    src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png"
                    width="200px;">
                </td>
            </tr>

            <tr>
                <th colspan="2"
                    style="
                    background-color:#2c86b8;
                    color:white;
                    padding:15px;
                    text-align:left;
                    border:1px solid #fff;">
                    '.$pcular.'
                </th>
            </tr>

            <tr>
                <th style="
                background-color:#2c86b8;
                color:white;
                padding:15px;
                width:20%;
                border:1px solid #fff;">
                Sr No
                </th>

                <th style="
                background-color:#2c86b8;
                color:white;
                padding:15px;
                width:80%;
                border:1px solid #fff;">
                MOM Points
                </th>
            </tr>

            </thead>
            <tbody>
        ';

        /*
        |--------------------------------------------------------------------------
        | MOM Points
        |--------------------------------------------------------------------------
        */

        if(!empty($dfmompoints))
        {
            $i = 1;

            foreach($dfmompoints as $point)
            {
                if(trim($point) != '')
                {
                    $pointData = array(
                        'df_id'     => $df_id,
                        'record_id' => $record_id,
                        'mom_point' => $point,
                        'added_on'  => date('Y-m-d H:i:s'),
                        'added_by'  => $user_id
                    );

                    $this->db->insert(
                        'dfmom_points',
                        $pointData
                    );

                    $message .= "
                    <tr>
                        <td style='border:1px solid #ddd;padding:8px;'>
                            ".$i."
                        </td>

                        <td style='border:1px solid #ddd;padding:8px;'>
                            ".$point."
                        </td>
                    </tr>";

                    $i++;
                }
            }
        }

        $message .= "</tbody></table>";

        /*
        |--------------------------------------------------------------------------
        | Email Recipients
        |--------------------------------------------------------------------------
        */

        $participantperson = array();

        if(!empty($participants))
        {
            $q = $this->db
                ->select('email')
                ->from('system_users')
                ->where_in('user_id', $participants)
                ->get();

            foreach($q->result() as $participantinfo)
            {
                $participantperson[] =
                    $participantinfo->email;
            }
        }

        $ccemail =
            count($participantperson) > 0
            ? implode(',', $participantperson)
            : '';

        /*
        |--------------------------------------------------------------------------
        | Send Email
        |--------------------------------------------------------------------------
        */

        $SUB = "DF Meeting MOM Points ".$pcular;

        $this->email->set_mailtype("html");

        $this->email->to('mangleshup@gmail.com,sdsrbh5@gmail.com');

        // Uncomment if required
        // if($ccemail){
        //     $this->email->cc($ccemail);
        // }

        if(!empty($emailcc))
        {
            $this->email->cc($emailcc);
        }

        $this->email->from(
            'taskmanagement@shubhampack.com'
        );

        $this->email->subject($SUB);
        $this->email->message($message);

        $email_sent = $this->email->send();

        /*
        |--------------------------------------------------------------------------
        | Update Scheduling
        |--------------------------------------------------------------------------
        */

        $qq = $this->db
            ->select('end_date,taskid')
            ->from('task_department_wise_scheduling')
            ->where('id', $record_id)
            ->where('df_id', $df_id)
            ->get();

        if($qq->num_rows() > 0)
        {
            $o = $qq->row();

            $q4 = $this->db
                ->select('tat')
                ->from('task_management')
                ->where('task_id', $o->taskid)
                ->get();

            if($q4->num_rows() > 0)
            {
                $ross = $q4->row();

                $dateObj =
                    new DateTime($o->end_date);

                $dateObj->modify(
                    '+'.$ross->tat.' days'
                );

                $enddate1 =
                    $dateObj->format('Y-m-d');

                $q5 = $this->db
                    ->select('holiday_date')
                    ->from('prestogroup_holidays')
                    ->where(
                        'holiday_date',
                        $enddate1
                    )
                    ->get();

                if($q5->num_rows() > 0)
                {
                    $dateObj2 =
                        new DateTime($enddate1);

                    $dateObj2->modify('+1 day');

                    $enddate2 =
                        $dateObj2->format('Y-m-d');
                }
                else
                {
                    $enddate2 = $enddate1;
                }

                $updateData = array(
                    'start_date' => $o->end_date,
                    'end_date'   => $enddate2
                );

                $this->db
                    ->where('id', $record_id)
                    ->where('df_id', $df_id)
                    ->update(
                        'task_department_wise_scheduling',
                        $updateData
                    );
            }
        }

        echo json_encode([
            'status'      => true,
            'message'     => 'MOM saved successfully',
            'mom_id'      => $id,
            'email_sent'  => $email_sent
        ]);
    }
    catch(Exception $e)
    {
        echo json_encode([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}


public function get_df_mom_history()
{
    header('Content-Type: application/json');

    $json = json_decode(file_get_contents("php://input"), true);

    $df_id = isset($json['df_id']) ? intval($json['df_id']) : 0;

    if($df_id <= 0)
    {
        echo json_encode([
            'status' => false,
            'message' => 'DF ID is required'
        ]);
        exit;
    }

    /*-------------------------------------------------------
    DF HEADER
    -------------------------------------------------------*/

    $header = $this->db
        ->select('
            a.particular,
            a.date,
            a.time,
            a.cc_record,
            CONCAT(b.first_name," ",b.last_name) as created_by
        ')
        ->from('dfwise_mom a')
        ->join('system_users b','a.added_by=b.user_id','left')
        ->where('a.dfid',$df_id)
        ->order_by('a.id','desc')
        ->limit(1)
        ->get()
        ->row_array();

    if(empty($header))
    {
        echo json_encode([
            'status' => false,
            'message' => 'No MOM Found'
        ]);
        exit;
    }

    /*-------------------------------------------------------
    LAST MOM DATE
    -------------------------------------------------------*/

    $lastMom = $this->db
        ->select('added_on')
        ->from('dfmom_points')
        ->where('df_id',$df_id)
        ->order_by('id','desc')
        ->limit(1)
        ->get()
        ->row_array();

    /*-------------------------------------------------------
    PARTICIPANTS
    -------------------------------------------------------*/

    $participants = $this->db
    ->select("
        a.user_id,
        CONCAT(
            UCASE(LEFT(TRIM(CONCAT_WS(' ', b.first_name, b.last_name)),1)),
            LCASE(SUBSTRING(TRIM(CONCAT_WS(' ', b.first_name, b.last_name)),2))
        ) as name
    ", false)
        ->from('dfwise_mom_participant a')
        ->join('system_users b','a.user_id=b.user_id','left')
        ->where('a.dfid',$df_id)
        ->group_by('a.user_id')
        ->get()
        ->result_array();

    /*-------------------------------------------------------
    MEETING DATE GROUPS
    -------------------------------------------------------*/

    $meetingDates = $this->db
        ->query("
            SELECT DATE(added_on) as meeting_date
            FROM dfmom_points
            WHERE df_id='".$df_id."'
            GROUP BY DATE(added_on)
            ORDER BY meeting_date DESC
        ")
        ->result_array();

    $momHistory = [];

    foreach($meetingDates as $meeting)
    {
        $meetingDate = $meeting['meeting_date'];

        $points = $this->db
            ->select('
                a.id,
                a.mom_point,
                a.added_on,
                CONCAT(
                    IFNULL(b.title,""),
                    " ",
                    IFNULL(b.first_name,""),
                    " ",
                    IFNULL(b.last_name,"")
                ) as added_by
            ')
            ->from('dfmom_points a')
            ->join('system_users b','a.added_by=b.user_id','left')
            ->where('a.df_id',$df_id)
            ->where('DATE(a.added_on)',$meetingDate)
            ->order_by('a.id','asc')
            ->get()
            ->result_array();

        $formattedPoints = [];
        $sr = 1;

        foreach($points as $row)
        {
            $formattedPoints[] = [
                'id'         => $row['id'],
                'sr_no'      => $sr++,
                'mom_point'  => $row['mom_point'],
                'added_by'   => trim($row['added_by']),
                'added_on'   => $row['added_on']
            ];
        }

        $momHistory[] = [
            'meeting_date'  => date('d-M-Y',strtotime($meetingDate)),
            'total_points'  => count($formattedPoints),
            'points'        => $formattedPoints
        ];
    }

    /*-------------------------------------------------------
    SUMMARY
    -------------------------------------------------------*/

    $totalPoints = $this->db
        ->where('df_id',$df_id)
        ->count_all_results('dfmom_points');

    /*-------------------------------------------------------
    RESPONSE
    -------------------------------------------------------*/

    echo json_encode([
        'status' => true,
        'message' => 'MOM History Found',
        'data' => [

            'df_id' => $df_id,

            'particular' => $header['particular'],

            'created_by' => $header['created_by'],

            'created_date' => !empty($lastMom)
                ? date('d-m-Y',strtotime($lastMom['added_on']))
                : '',

            'created_time' => !empty($lastMom)
                ? date('h:i A',strtotime($lastMom['added_on']))
                : '',

            'participants' => $participants,

            'mom_history' => $momHistory,

            'summary' => [
                'total_meetings'     => count($momHistory),
                'total_points'       => $totalPoints,
                'total_participants' => count($participants)
            ],

            'pdf_url' => base_url('DF/view_df_mom/'.$df_id)
        ]
    ]);
}

public function get_df_dropdown_list()
{
    $rows = $this->db
        ->select('id, df_no, df_sr_no, df_description')
        ->from('df_release')
        ->order_by('df_sr_no','DESC')
        ->get()
        ->result_array();

    echo json_encode([
        'status' => true,
        'data'   => $rows
    ]);
}

}


