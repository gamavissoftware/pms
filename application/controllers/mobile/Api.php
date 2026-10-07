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
        /* A contact number is NOT unique in system_users - three of them are
           shared by two staff each. This used to take row_array(), so only
           whichever row the table happened to return first could ever sign
           in, and the other person's correct password was rejected forever.
           Read every match and let the password decide which of them it is. */
        $this->db->select('user_id, first_name, last_name, email, password, user_role_id, department_id, user_status,contact_number,admin_dashboard');
        $this->db->from('system_users');
        $this->db->where('contact_number', $email);
        $this->db->order_by('user_status', 'DESC');
        $this->db->order_by('user_id', 'ASC');
        $candidates = $this->db->get()->result_array();

        if (empty($candidates)) {
            $this->_response(false, 'Invalid email or password');
            return;
        }

        /* ===============================
         * 4. MATCH PASSWORD, THEN STATUS
         * =============================== */
        $user = null;
        $inactive_match = null;

        foreach ($candidates as $candidate) {

            if ($candidate['password'] !== $password) continue;

            /* the password is right, so this is definitely the person -
               tell them their account is off rather than that it is wrong */
            if ((int) $candidate['user_status'] !== 1) {
                if ($inactive_match === null) $inactive_match = $candidate;
                continue;
            }

            $user = $candidate;
            break;
        }

        if (!$user) {

            if ($inactive_match !== null) {
                $this->_response(false, 'Your account is inactive. Contact administrator.');
                return;
            }

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
    $this->sync_customer_to_sap('marketing', $customer_id);

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

        /* A won lead is a closed lead, and a closed lead is not a follow-up.
           followup_counts_api has always carried this line; the list never
           did - so the badge and the list disagreed, and won orders kept
           turning up under Missed, Today and Upcoming. Excluding the
           conversion stage above is not enough on its own: that only looks at
           the stage on the latest progress_remark, so a lead closed by any
           other route, or one that picked up a later remark at a different
           stage, walked straight back into the list. Every equivalent lead
           query in the web CRM filters b.closed too. */
        AND b.closed = 0

        /* 0000-00-00 sorts below every real date, so next_follow_date < today
           quietly counts a lead with no follow-up date at all as Missed -
           which is how SPM/EXP/L/1143/26-27 reads as overdue on the web page
           while its newest remark says Order Won. followup_counts_api skips
           those dates in PHP; the list has to say so in SQL, or the two drift
           apart again. Mind the quoting here: this comment sits inside a
           double-quoted PHP string, so a double quote in it ends the string. */
        AND a.next_follow_date NOT IN ('0000-00-00', '1970-01-01')
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

        /* A won or dead lead is not a follow-up.
         *
         * pipeline_list_api already pinned a.id to MAX(id) and filtered
         * b.closed, but it never excluded the conversion/dead-end stages the
         * way followup_list_api does - so an Order Won lead whose leads.closed
         * was still 0 walked straight into Missed. That is what put
         * SPM/EXP/L/1143/26-27 in front of the salesperson who owns it.
         *
         * Scoped to this branch deliberately. A caller that names a stage_id
         * is asking for that stage, Order Won included, and excluding it there
         * would empty the pipeline card people use to see what they have won.
         */
        $conv     = $this->dashboardmodel->getConversionLeadStage();
        $dead     = $this->salescrm->getDeadEndLeadStage();
        array_push($dead, $conv);
        $dead_sql = "'" . implode("','", $dead) . "'";

        $where .= " AND a.lead_status NOT IN ($dead_sql)";

        /* 0000-00-00 sorts below CURDATE(), so a lead carrying no follow-up
           date at all was being counted as overdue. */
        $where .= " AND a.next_follow_date NOT IN ('0000-00-00','1970-01-01')";

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

    /* The app does not post this yet. When it does, user_devices stops being
       blind to platform - today device_type says 'android' for every row,
       iPhones included, which is why iOS has to be identified from
       app_device_log instead. Defaults to the old value until then. */
    $device_type = 'android';
    if (isset($post['platform']) && trim((string) $post['platform']) !== '') {
        $device_type = substr(trim((string) $post['platform']), 0, 20);
    }

    if ($exists) {
        /* A token identifies one install, so finding it already on file under
           somebody else means the handset changed hands. Move it: otherwise
           the previous user keeps receiving pushes on a phone that is now in
           someone else's pocket. */
        $this->db->where('fcm_token', $token);
        $this->db->update('user_devices', [
            'user_id'     => $user_id,
            'device_type' => $device_type,
            'updated_at'  => date('Y-m-d H:i:s')
        ]);
    } else {

        /* This used to be `DELETE FROM user_devices WHERE user_id = X` with no
           further condition, run before every insert.
         *
         * That capped each person at exactly one device and silently
         * unregistered the rest: a second phone, or a tablet, knocked the
         * first one off and it stopped receiving pushes with nothing anywhere
         * to show why. Keeping the rows is what "capture every token" means -
         * every sender already dedupes by token, so a spare row costs one
         * wasted call and nothing else.
         *
         * Abandoned installs are pruned instead of everything: rows for this
         * user untouched for 60 days are old handsets and reinstalls, whose
         * tokens FCM has long since retired. A row with both timestamps NULL
         * compares NULL and is kept, which is the safe way round. */
        $this->db->where('user_id', $user_id);
        $this->db->where('fcm_token !=', $token);
        $this->db->where(
            'COALESCE(updated_at, created_at) < DATE_SUB(NOW(), INTERVAL 60 DAY)',
            null, false
        );
        $this->db->delete('user_devices');

        /// ➕ INSERT NEW TOKEN
        $this->db->insert('user_devices', [
            'user_id'    => $user_id,
            'fcm_token'  => $token,
            'device_type'=> $device_type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
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
            $stage = $this->salescrm->get_quote_approval_stage_after_won($lead_id, 37);
            $remark_title = ($stage == 35) ? 'Revised Quotation Approved - Order Won.' : 'Quotation Approved.';
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
        if ($status == 1 && $stage == 35) {
            $this->salescrm->notify_revised_quote_approved_for_order_won($lead_id, $remarks);
        }

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
                    if ($stage == 35) {
                        $title = "Revised Quotation Approved";
                        $message = "Hi $name, your revised quotation for $company_name was approved and moved to Order Won again.";
                    } else {
                        $title = "Quotation Approved ✅";
                        $message = "Hi $name, your quotation for $company_name has been approved.";
                    }
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

        /* Scope the counts exactly as service_opportunity_list_api scopes the
         * list. They used to disagree: the card counted the whole company
         * while the list showed only your own opportunities, so a salesperson
         * tapped "Pending for Approval: 106" and landed on an empty screen.
         *
         * Dashboard_model::getServiceStageCounts() takes no user at all, so
         * the count is rebuilt here rather than changed there - the model is
         * shared with the web PMS and is not ours to alter. The role test is
         * copied verbatim from the list so the two cannot drift apart again.
         */
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = $input['user_id'] ?? null;
        $role_id = $input['role_id'] ?? null;

        $scoped = (!empty($user_id) && $role_id != 12 && $role_id != 41 && $user_id != 111);

        $stages = $this->Dashboard_model->getAllServiceLeadStages();

        $data = [];

        foreach ($stages as $stage) {

            $this->db->from('service_opportunities');
            $this->db->where('current_stage_id', $stage->stage_id);
            if ($scoped) $this->db->where('marketing_person_id', $user_id);
            $count = $this->db->count_all_results();

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

        /* Cancelled / PO received / Won / PI are finished, not follow-ups -
           the web's Dashboard_model::getServiceFollowupCounts drops them. */
        $excluded = $this->_svc_followup_excluded_ids();
        if (!empty($excluded)) $this->db->where_not_in('so.current_stage_id', $excluded);

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
        $this->sync_customer_to_sap('spares', $customer_id);

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
            so.marketing_person_id,

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

    $excluded = $this->_svc_followup_excluded_ids();
    if (!empty($excluded)) $this->db->where_not_in('so.current_stage_id', $excluded);

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

        $svc_cancelled_id   = $this->_svc_cancelled_stage_id();
        $svc_can_reopen_all = $this->_svc_can_reopen_all((int)$user_id);

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

            /// CANCELLED QUOTATION -> reopen instead of update
            $row['is_cancelled'] = $svc_cancelled_id > 0 && (int)$row['current_stage_id'] === $svc_cancelled_id;
            $row['can_reopen']   = false;
            if ($row['is_cancelled']) {
                $row['can_update'] = false;
                $row['can_reopen'] = $svc_can_reopen_all
                    || (int)($row['marketing_person_id'] ?? 0) === (int)$user_id;
            }

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

        "next_stages" => $next_stages,

        /// cancelled quotation: what it was before, and whether this user may reopen it
        "cancellation" => $this->_svc_cancellation((int)$opportunity->opportunity_id, (int)($input['user_id'] ?? 0))
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

        // ================= CANCELLED GUARD (web: 409) =================
        $svc_cancelled_id = $this->_svc_cancelled_stage_id();
        if ($svc_cancelled_id > 0) {
            $cur = $this->db->select('current_stage_id')->where('opportunity_id', $opportunity_id)
                ->get('service_opportunities')->row();
            if ($cur && (int)$cur->current_stage_id === $svc_cancelled_id) {
                return $this->_json(false, 'Reopen this cancelled quotation before moving its pipeline stage.');
            }
        }

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
        $ecn_perms = $this->_ecn_perms($user_id, $role_id);
        $ot_perms  = $this->_ot_app_perms($user_id);

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

            /// DF Task and DF MOM.
            ///
            /// Hidden from HR (26) and from the engineers (Service, 22)
            /// as of 2026-09-17: neither raises or reads a DF, and the two
            /// modules were sitting in their bottom nav and drawer taking
            /// up room that their own work needs. The Service HOD (111) is
            /// in department 22 but is not an engineer, so he keeps them -
            /// the same exception `visit` above already makes. Admins keep
            /// everything, or a bad rule here would hide the modules from
            /// whoever has to fix it.
            "tasks" => $this->_df_visible($user_id, $role_id, $department_id),
            "df"    => $this->_df_visible($user_id, $role_id, $department_id),
            "chat"  => true,
            "quote_approval" => (
    $role_id == 12 ||
    in_array($user_id, [139, 161])
),
    "visit" => (
    $department_id == 22 && $user_id != 111
    ),

            /// Delegation: everyone can be delegated to, so the inbox is
            /// always on. Raising a new delegation is gated on the
            /// Delegation/delegation_master setup (admins always may).
            "delegation" => true,
            "delegation_create" => $this->_dlg_can_create($user_id, $role_id),

            /// Task Management: open to everyone, exactly as the web
            /// module is - anybody may raise a task for anybody.
            "task_management" => true,

            /// DF Change Control (ECN / IOM / OTHERS). Same gate as the web
            /// module, so the phone never offers a tile the browser hides.
            /// DF Change Control - the third of the DF family, so it goes
            /// with the other two. _ecn_perms still decides whether this
            /// person has anything to do in it; _df_visible decides whether
            /// the module is theirs to see at all, and both must agree.
            "ecn" => $ecn_perms['visible']
                     && $this->_df_visible($user_id, $role_id, $department_id),

            "ecn_create" => $ecn_perms['can_create']
                     && $this->_df_visible($user_id, $role_id, $department_id),

            /// Attendance: everybody punches, only the admins see the
            /// whole day's sheet.
            "attendance" => true,
            "attendance_admin" => $this->_att_is_admin($user_id, $role_id),

            /// Leading a team in prestogroup_teams is what makes an HOD -
            /// there is no HOD role or flag, so it is derived, not stored.
            "attendance_hod" => $this->_att_is_hod($user_id),

            /// Overtime. Read from the SAME module/submodule grants the
            /// browser reads, so access is granted in PMS and nowhere
            /// else - there is no app-side list of who gets this module.
            "overtime"         => $ot_perms['visible'],
            "overtime_create"  => $ot_perms['create'],
            "overtime_approve" => $ot_perms['decide']
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

        /* Rebuilt here instead of calling Dashboard_model, which is shared
         * with the web PMS and is not ours to change. Two things were wrong
         * with that call:
         *
         *   1. the arguments were passed in the wrong order - the model signs
         *      ($role, $user_id, $department_id) and this passed
         *      ($role_id, $department_id, $user_id) - so it filtered
         *      b.added_by = <a department id> and every single count came
         *      back 0 for every non-admin. That is the whole spare pipeline
         *      reading empty.
         *   2. it scopes by added_by while spare_opportunity_list_api scopes
         *      by marketing_person_id, so even with the order corrected the
         *      card and the list would still be counting different people.
         *
         * Same column and same role test as the list, so they cannot drift.
         * Admin roles are 12 and 41 (user_role.isadmin = 1).
         */
        $spare_admin = in_array((int) $role_id, array(12, 41), true);
        $chk = $spare_admin
            ? ""
            : " AND b.marketing_person_id = " . (int) $user_id . " ";

        $data = $this->db->query("
            SELECT
                s.lead_id      AS stage_id,
                s.lead_name    AS stage_name,
                s.show_in_app  AS show_front,
                s.icon,
                COUNT(DISTINCT b.opportunity_id) AS count
            FROM spare_lead_stage s
            LEFT JOIN spare_progress_remarks a
                ON a.lead_stage = s.lead_id
               AND a.id IN (SELECT MAX(id) FROM spare_progress_remarks GROUP BY lead_id)
            LEFT JOIN opportunities b
                ON a.lead_id = b.opportunity_id
                $chk
            WHERE s.quotation_related_steps = 0
            GROUP BY s.lead_id
            ORDER BY s.sort_order ASC
        ")->result_array();

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
        $this->sync_customer_to_sap('spares', $insert_id);

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

    /* the PDF on disk may be named after this, not the id */
    $latest_quotation_no_subquery = "
        (
            SELECT q2.quotation_no
            FROM quotations q2
            WHERE q2.opportunity_id = op.opportunity_id
            ORDER BY q2.quotation_id DESC
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
        op.marketing_person_id,
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
            as latest_quotation_id,

        {$latest_quotation_no_subquery}
            as latest_quotation_no
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
    /* Admin is roles 12 and 41 (user_role.isadmin = 1). This used to read
       `!= 1 && != 12`, which exempted role 1 - not an admin - from scoping
       while wrongly scoping role 41, which is one. Matches the count in
       get_spare_pipeline_post. */
    if ($role_id != 12 && $role_id != 41) {

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

    /* A follow-up chip lists only opportunities still being worked -
       the same rule as its badge (Dashboard_model::get_spare_followup_countsNew)
       and the web list: open, latest stage not a closed one, and a real
       date. '0000-00-00' compared as a date is "before today", which is
       how 620 auto-cancelled quotations filled the Missed list. */
    if (in_array((int) $followup_type, [1, 2, 3], true)) {
        $this->db->where('op.status', 0);
        $this->db->where(
            "(sls.lead_name IS NULL OR LOWER(TRIM(sls.lead_name)) NOT IN
              ('cancelled quotation','order won','lead lost','pending for po','po created','create pi'))",
            null, false
        );
        $this->db->where(
            "spr.next_follow_date IS NOT NULL AND spr.next_follow_date NOT IN ('0000-00-00', '1970-01-01')",
            null, false
        );
    }

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

    $spare_cancelled_id  = $this->_spare_cancelled_stage_id();
    $spare_can_reopen_all = $this->_spare_can_reopen_all((int) $user_id);

    foreach ($query->result() as $row) {

        $current_stage_id = $row->lead_stage;

        // ✅ UPDATE BUTTON

        if ($current_stage_id == 11 || $current_stage_id == 10) {

            $update_btn = false;

        } else {

            $update_btn = true;
        }

        // ✅ CANCELLED QUOTATION → reopen instead of update
        $is_cancelled = $spare_cancelled_id > 0 && (int) $current_stage_id === $spare_cancelled_id;
        $can_reopen = false;
        if ($is_cancelled) {
            $update_btn = false;
            $can_reopen = $spare_can_reopen_all
                || (int) $row->marketing_person_id === (int) $user_id;
        }

        // ✅ QUOTE URL

        $url = "";

        if (!empty($row->latest_quotation_id)) {

            /*
            The id-named file only exists once somebody has opened the
            quotation on the web; a new one is saved under its number.
            */
            $quote_file =
                $this->spare_quote_pdf_filename(
                    $row->latest_quotation_id,
                    $row->latest_quotation_no ?? ''
                );

            if ($quote_file !== '') {

                $url = page_url1 .
                    'uploads/spare_quotations/' .
                    $quote_file;
            }
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

            "is_cancelled" =>
                $is_cancelled,

            "can_reopen" =>
                $can_reopen,

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


/*
Where a spare quotation's PDF actually is.

Spares::generate_quotation_pdf saves a NEW quotation as <quotation_no>.pdf
(slashes replaced with dashes). Spares::view_quotation_pdf saves the SAME
quotation again as <quotation_id>.pdf, but only the first time somebody
opens it on the web. The app built the id-named URL by hand, so every
quotation nobody had opened on the web pointed at a 404 - 22 of the 25
newest approved ones. Look for both names and return whichever is on disk.
*/
private function spare_quote_pdf_filename(
    $quotation_id,
    $quotation_no = ''
)
{
    $folder =
        FCPATH .
        'uploads/spare_quotations/';

    $by_id =
        $quotation_id . '.pdf';

    if (file_exists($folder . $by_id)) {

        return $by_id;
    }

    $quotation_no =
        trim((string) $quotation_no);

    if ($quotation_no !== '') {

        /* the same sanitising Spares::generate_quotation_pdf uses */
        $safe = preg_replace(
            '/[\/\\\\:*?"<>|]+/',
            '-',
            $quotation_no
        );

        if (
            $safe !== ''
            &&
            file_exists($folder . $safe . '.pdf')
        ) {

            return $safe . '.pdf';
        }
    }

    return '';
}

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

        /*
        Either name counts - see spare_quote_pdf_filename()
        */

        $filename =
            $this->spare_quote_pdf_filename(
                $quote_id,
                $quote->quotation_no ?? ''
            );

        /*
        Generate PDF if missing
        */

        if(
        $filename === ''
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

        $filename =
            $this->spare_quote_pdf_filename(
                $quote_id,
                $quote->quotation_no ?? ''
            );

        if(
        $filename === ''
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
                    $next_stages,

                /// cancelled quotation: previous stage + may this user reopen it
                "cancellation" =>
                    $this->_spare_cancellation((int) $id, (int) ($input['user_id'] ?? 0))
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
                page_url1 . (
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

    /// ================= CANCELLED GUARD (web: 409) =================

    $spare_cancelled_id = $this->_spare_cancelled_stage_id();
    if ($spare_cancelled_id > 0
        && $this->_spare_latest_stage((int) $opportunity_id) === $spare_cancelled_id) {
        echo json_encode([
            'status' => false,
            'message' => 'Reopen this cancelled quotation before moving its pipeline stage.'
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

/* the PDF on disk may be named after this, not the id */
$latest_quote_no_subquery="

(

SELECT
q.quotation_no

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

as revision_no,

{$latest_quote_no_subquery}

as latest_quote_no

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

                /*
                id-named or number-named, whichever is on disk
                */
                $quote_file=
                    $this->spare_quote_pdf_filename(
                        $row['latest_quote_id'],
                        $row['latest_quote_no'] ?? ''
                    );

                if($quote_file !== ''){

                    $quoteUrl=

                        page_url1

                        .'uploads/spare_quotations/'

                        .$quote_file;

                }

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



/* =============================================================================
 *  GROUP CHAT — MOBILE API  (paste INSIDE the `Api` class in
 *  application/controllers/mobile/Api.php, just before the final closing `}` )
 * -----------------------------------------------------------------------------
 *  These endpoints expose the PMS web group-chat (chat_* tables) to the Flutter
 *  app. They REUSE the existing Chat_model — no other file is modified.
 *
 *  Auth = mobile style: every request sends `user_id` in the JSON body (the
 *  app's ApiService.post already appends it). Identity ($me) is built from
 *  system_users, matching the (id,type) shape chat_access_helper produces.
 *
 *  Real-time = poll + FCM:
 *    - chat_poll   : app polls this every few seconds for new events + unread
 *    - FCM push    : _chat_push() notifies participants' devices on every message
 *      (reuses user_devices + sendFCMData(), same as the old chat)
 *
 *  Response envelope is always {"status": true|false, ...}. The app checks
 *  res['status'] exactly like the current chat.
 * ===========================================================================*/

/* ----------------------------------------------------------------------------
 *  INTERNAL HELPERS  (prefixed _chat_ so they never collide with an endpoint)
 * --------------------------------------------------------------------------*/

/** Load the model + access helper once per request and decode the JSON body. */
private function _chat_boot(&$me)
{
    $this->load->model('Chat_model', 'chat');
    $this->load->helper('chat_access');   // chat_name_case / chat_initials / chat_avatar_url / chat_clean_ref_type

    $input = json_decode(file_get_contents("php://input"), true);
    if (!is_array($input)) $input = array();

    $uid = (int)($input['user_id'] ?? 0);
    $me  = $uid ? $this->_chat_me($uid) : null;

    return $input;
}

/** Build the (id,type,name,role,avatar,initials) identity for one user. */
private function _chat_me($user_id)
{
    $u = $this->db->select('user_id, first_name, last_name, profile_image')
        ->from('system_users')->where('user_id', (int)$user_id)->get()->row();
    if (!$u) return null;

    $name = function_exists('chat_name_case')
        ? chat_name_case($u->first_name . ' ' . $u->last_name)
        : trim($u->first_name . ' ' . $u->last_name);
    if ($name === '') $name = 'User #' . $u->user_id;

    return array(
        'id'       => (int)$u->user_id,
        'type'     => 'user',
        'name'     => $name,
        'role'     => '',
        'avatar'   => function_exists('chat_avatar_url') ? chat_avatar_url($u->profile_image, 'user') : '',
        'initials' => function_exists('chat_initials')
                        ? chat_initials($name)
                        : strtoupper(substr($u->first_name, 0, 1) . substr($u->last_name, 0, 1)),
    );
}

/** Auth + membership gate. Echoes the error and returns FALSE when barred. */
private function _chat_guard($conv_id, $me)
{
    if (!$me) {
        echo json_encode(array("status" => false, "message" => "auth"));
        return false;
    }
    if (!$this->chat->is_participant((int)$conv_id, $me)) {
        echo json_encode(array("status" => false, "message" => "forbidden"));
        return false;
    }
    return true;
}

/** Shared tail of send/upload: bell notifications, event fan-out, FCM push. */
private function _chat_after($conv_id, $msg_id, $conv, $preview, $me)
{
    $label = ($conv->type === 'direct') ? 'your direct messages' : $conv->name;
    $title = ($conv->type === 'direct') ? $me['name'] : ($me['name'] . ' in ' . $label);
    $plain = $this->chat->plain(strip_tags($preview));

    // bell feed (also reaches web users) — written BEFORE the event fan-out
    $this->chat->notify_conversation(
        $conv_id, $me, $msg_id,
        $conv->type === 'direct' ? 'message' : ($conv->type === 'job' ? 'job' : 'group'),
        $title, $plain
    );

    // event row → wakes web long-poll AND is what the app's chat_poll reads
    $this->chat->push_event($conv_id, 'message', $me, $msg_id);

    $this->chat->touch_presence($me);

    // FCM to every participant's device except the sender's
    $this->_chat_push($conv_id, $me, $plain, $conv);
}

/** Push a data-only FCM to all active participants (except the sender). */
private function _chat_push($conv_id, $me, $preview, $conv)
{
    $isGroup  = ($conv->type !== 'direct');
    $convName = $isGroup ? $conv->name : $me['name'];

    $rows = $this->db->select('user_id')->from('chat_participant')
        ->where('conversation_id', (int)$conv_id)
        ->where('is_active', 1)->where('user_type', 'user')
        ->get()->result();

    $ids = array();
    foreach ($rows as $r) {
        if ((int)$r->user_id === (int)$me['id']) continue;   // never notify the sender
        $ids[] = (int)$r->user_id;
    }
    if (empty($ids)) return;

    $tokens = $this->db->select('fcm_token')->from('user_devices')
        ->where_in('user_id', $ids)->where('fcm_token !=', '')
        ->get()->result_array();
    if (empty($tokens)) return;

    $title = $isGroup ? $convName : $me['name'];
    $body  = $isGroup ? ($me['name'] . ': ' . $preview) : $preview;

    foreach ($tokens as $t) {
        $dataPayload = array(
            "type"            => "chat_message",             // app already routes this type
            "conversation_id" => (string)$conv_id,
            "chat_id"         => (string)$conv_id,           // backward-compatible alias
            "is_group"        => $isGroup ? "1" : "0",
            "conv_name"       => $isGroup ? $convName : '',
            "sender_name"     => $me['name'],
            "sender_initials" => $me['initials'],
            "message"         => $preview,
            "timestamp"       => date("Y-m-d H:i:s"),
            "screen"          => "chat_detail"
        );
        sendFCMData($t['fcm_token'], $title, $body, $dataPayload);
    }
}

/** Only an owner/admin of a group may manage its members. */
private function _chat_can_manage($conv_id, $me)
{
    $row = $this->chat->participant_row((int)$conv_id, $me);
    return $row && in_array($row->member_role, array('owner', 'admin'), true);
}

/** The group's owner - the one person who may delete it. */
private function _chat_is_owner($conv_id, $me)
{
    $row = $this->chat->participant_row((int)$conv_id, $me);
    return $row && $row->member_role === 'owner';
}

/** An archived group is read-only, as on the web (Chat::archived_guard):
 *  the bundle built at archive time must keep matching the thread.
 *  Echoes the refusal and returns FALSE when the room is archived. */
private function _chat_writable($conv_id)
{
    $conv = $this->chat->get_conversation((int)$conv_id);
    if ($conv && !empty($conv->archived_at) && $conv->archived_at !== '0000-00-00 00:00:00') {
        echo json_encode(array("status" => false, "message" => "archived",
            "message_text" => "This group is archived and read-only. Reopen it first."));
        return false;
    }
    return true;
}

/* ----------------------------------------------------------------------------
 *  ENDPOINTS
 * --------------------------------------------------------------------------*/

/** First screen load: identity + sidebar + unread + realtime cursor. */
public function chat_bootstrap()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $this->chat->touch_presence($me);
    echo json_encode(array(
        "status"        => true,
        "me"            => $me,
        "conversations" => $this->chat->my_conversations($me),
        "unread"        => $this->chat->unread_totals($me),
        "cursor"        => $this->chat->latest_event_id($me),
    ));
}

/** Just the sidebar list + unread (pull-to-refresh). */
public function chat_conversations()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    echo json_encode(array(
        "status"        => true,
        "conversations" => $this->chat->my_conversations($me),
        "unread"        => $this->chat->unread_totals($me),
    ));
}

/**
 * How many messages one thread page returns.
 *
 * Chat_model::PAGE_SIZE is 40 and is shared with the web, so the size is
 * passed per call rather than changed there: the app asks for 50 and pages
 * older ones in as the user scrolls up.
 */
private function _chat_page_limit($input)
{
    $n = (int) (isset($input['limit']) ? $input['limit'] : 0);
    if ($n <= 0) $n = 50;
    return max(10, min(100, $n));
}

/** Full state of one conversation: header, members, a page of messages, my role. */
public function chat_thread()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $conv = $this->chat->get_conversation($conv_id);
    $this->chat->mark_read($conv_id, $me);

    $header = array(
        'id'          => $conv_id,
        'type'        => $conv->type,
        'name'        => $conv->name,
        'description' => $conv->description,
        'ref_type'    => $conv->ref_type,
        'ref_id'      => (int)$conv->ref_id,
        'avatar'      => (isset($conv->avatar) && $conv->avatar !== '') ? chat_upload_url . $conv->avatar : '',
        /* The FLAG, not the figure - the same thing the web DF group list
           shows, and the same thing my_conversations() already puts on
           every row. Without it the app's list marked a penalty DF and the
           thread you opened from it did not. What the penalty comes to
           stays in the penalty report; no money crosses into chat. */
        'penalty'     => false,
    );

    if ($conv->ref_type === 'df' && (int) $conv->ref_id > 0) {
        try {
            $flags = $this->chat->df_penalty_map(array((int) $conv->ref_id));
            $header['penalty'] = isset($flags[(int) $conv->ref_id]);
        } catch (Throwable $e) {
            log_message('error', 'Chat header penalty lookup failed: ' . $e->getMessage());
        }
    }

    $members = $this->chat->members($conv_id);

    // for a DM the header shows the OTHER person
    if ($conv->type === 'direct') {
        foreach ($members as $m) {
            if ((int)$m['id'] === (int)$me['id'] && $m['type'] === $me['type']) continue;
            $header['name']   = $m['name'];
            $header['role']   = $m['role'];
            $header['plant']  = isset($m['plant']) ? $m['plant'] : '';
            $header['avatar'] = $m['avatar'];
            $header['online'] = $m['is_online'];
        }
    }

    $my = $this->chat->participant_row($conv_id, $me);
    $can_manage = $my && in_array($my->member_role, array('owner', 'admin'), true);

    /* Section + department, as the web's Details panel shows them. */
    try {
        $dept_id = isset($conv->department_id) ? (int)$conv->department_id : 0;
        $header['group_kind']    = $this->chat->group_kind($conv);
        $header['department_id'] = $dept_id;
        $header['department']    = $dept_id > 0 ? (string)$this->chat->department_name($dept_id) : '';
    } catch (Throwable $e) {
        $header['group_kind'] = ''; $header['department_id'] = 0; $header['department'] = '';
    }

    /* Archive state (web: Chat::conversation). Archive / reopen is the
       owner-or-admin bar; deleting the group is the OWNER alone. */
    $archived = (isset($conv->archived_at) && $conv->archived_at && $conv->archived_at !== '0000-00-00 00:00:00');
    $header['archived']       = $archived ? 1 : 0;
    $header['archived_at']    = $archived ? (string)$conv->archived_at : '';
    $header['archive_reason'] = isset($conv->archive_reason) ? (string)$conv->archive_reason : '';
    $header['archive_files']  = isset($conv->archive_files) ? (int)$conv->archive_files : 0;
    $header['archive_size']   = (isset($conv->archive_bytes) && $conv->archive_bytes)
                                  ? $this->chat->human_size((int)$conv->archive_bytes) : '';
    $header['has_archive_zip'] = !empty($conv->archive_zip);
    $header['can_archive']    = !$archived && $conv->type !== 'direct' && $can_manage;
    $header['can_unarchive']  = $archived && $can_manage;
    $header['can_delete']     = $conv->type !== 'direct' && $my && $my->member_role === 'owner';

    echo json_encode(array(
        "status"   => true,
        "header"   => $header,
        "members"  => $members,
        "messages" => $this->chat->messages($conv_id, 0, 0, $this->_chat_page_limit($input)),
        "pinned"   => $this->chat->pinned_messages($conv_id),
        "my_role"  => $my ? $my->member_role : 'member',
        "unread"   => $this->chat->unread_totals($me),
    ));
}

/** Message pagination — older page (before) or realtime top-up (after). */
public function chat_history()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $before = (int)($input['before'] ?? 0);
    $after  = (int)($input['after'] ?? 0);

    echo json_encode(array(
        "status"   => true,
        "messages" => $this->chat->messages($conv_id, $before, $after, $this->_chat_page_limit($input)),
    ));
}

/** Find-or-create the DM with one person (sidebar never duplicates). */
public function chat_open_dm()
{
    $input   = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $peer_id = (int)($input['peer_id'] ?? 0);
    if ($peer_id <= 0 || $peer_id === (int)$me['id']) {
        echo json_encode(array("status" => false, "message" => "bad_request")); return;
    }

    $conv_id = $this->chat->ensure_direct($me, $peer_id, 'user');
    echo json_encode(array("status" => true, "conversation_id" => (int)$conv_id));
}

/** Create a named group with an initial member list. */
public function chat_create_group()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $name = trim((string)($input['name'] ?? ''));
    if ($name === '') { echo json_encode(array("status" => false, "message" => "name_required")); return; }

    $desc    = trim((string)($input['description'] ?? ''));
    $raw     = $input['members'] ?? array();
    $members = array();
    foreach ((array)$raw as $mid) {
        $mid = (int)$mid;
        if ($mid > 0) $members[] = array('id' => $mid, 'type' => 'user');
    }

    $conv_id = $this->chat->create_channel($me, $name, $members, $desc);
    echo json_encode(array("status" => true, "conversation_id" => (int)$conv_id));
}

/** Post a text message (supports reply_to and @mentions). */
public function chat_post()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;
    if (!$this->_chat_writable($conv_id)) return;

    $body = trim((string)($input['body'] ?? ''));
    if ($body === '') { echo json_encode(array("status" => false, "message" => "empty")); return; }
    if (function_exists('mb_substr')) $body = mb_substr($body, 0, 8000, 'UTF-8');

    // reply target must live in THIS conversation
    $reply_to = (int)($input['reply_to'] ?? 0);
    if ($reply_to > 0) {
        $ok = $this->db->select('id')->from('chat_message')
            ->where('id', $reply_to)->where('conversation_id', $conv_id)->get()->row();
        if (!$ok) $reply_to = 0;
    }

    // a private reply carries a pointer to a group message in ANOTHER conversation
    $origin_id = (int)($input['origin_id'] ?? 0);
    if ($origin_id > 0) {
        $osrc = $this->db->select('conversation_id')->from('chat_message')
            ->where('id', $origin_id)->get()->row();
        if (!$osrc || !$this->chat->is_participant((int)$osrc->conversation_id, $me)) {
            $origin_id = 0;
        }
    }

    $msg_id = $this->chat->send_message($conv_id, $me, $body, array(
        'type' => 'text', 'reply_to' => $reply_to,
        'origin_id'   => $origin_id,
        'origin_kind' => $origin_id > 0 ? 'private' : '',
    ));

    $conv = $this->chat->get_conversation($conv_id);

    // @mentions — app sends an array of user_ids; "@everyone" via mention_all=1
    $mentions = array();
    foreach ((array)($input['mentions'] ?? array()) as $mid) {
        $mid = (int)$mid;
        if ($mid > 0) $mentions[] = array('id' => $mid, 'type' => 'user');
    }
    if (!empty($input['mention_all']) && $conv->type !== 'direct') {
        $mentions = $this->chat->participant_pairs($conv_id);
    }
    if (!empty($mentions)) {
        $label = ($conv->type === 'direct') ? 'your direct messages' : $conv->name;
        $this->chat->save_mentions($conv_id, $msg_id, $me, $mentions, $label);
    }

    $this->_chat_after($conv_id, $msg_id, $conv, $body, $me);

    echo json_encode(array("status" => true, "message" => $this->chat->message($msg_id)));
}

/** Upload one or more files and post them as a message.  Multipart POST:
 *  fields  user_id, conversation_id, body(optional), reply_to(optional)
 *  files   files[]  */
public function chat_upload_file()
{
    $this->load->model('Chat_model', 'chat');
    $this->load->helper('chat_access');

    $uid = (int)$this->input->post('user_id');
    $me  = $uid ? $this->_chat_me($uid) : null;
    $conv_id = (int)$this->input->post('conversation_id');
    if (!$this->_chat_guard($conv_id, $me)) return;
    if (!$this->_chat_writable($conv_id)) return;

    if (empty($_FILES['files']) || empty($_FILES['files']['name'])) {
        echo json_encode(array("status" => false, "message" => "no_file")); return;
    }

    $dir_rel = date('Y') . '/' . date('m') . '/';
    $dir_abs = chat_upload_path . $dir_rel;
    if (!is_dir($dir_abs) && !@mkdir($dir_abs, 0755, TRUE)) {
        echo json_encode(array("status" => false, "message" => "storage")); return;
    }

    $caption  = trim((string)$this->input->post('body'));
    $reply_to = (int)$this->input->post('reply_to');
    if ($reply_to > 0) {
        $ok = $this->db->select('id')->from('chat_message')
            ->where('id', $reply_to)->where('conversation_id', $conv_id)->get()->row();
        if (!$ok) $reply_to = 0;
    }

    $allowed = array('jpg','jpeg','png','gif','webp','bmp','heic','heif','pdf','doc','docx',
                     'xls','xlsx','ppt','pptx','txt','csv','rtf','odt','ods',
                     'zip','rar','7z','mp3','mp4','m4a','wav','mov','m4v','3gp','webm','mkv','avi');
    $maxUpload = 209715200; // 200 MB (2026-09-30, was 250) - same cap as the app's pre-check and web Chat::MAX_VIDEO_UPLOAD. PHP allows 256M.

    $names  = (array)$_FILES['files']['name'];
    $stored = array();
    $errors = array();

    for ($i = 0; $i < count($names); $i++) {
        if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) { $errors[] = $names[$i]; continue; }

        $orig = $names[$i];
        $size = (int)$_FILES['files']['size'][$i];
        $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

        if ($size > $maxUpload)                 { $errors[] = $orig . ' (too large)'; continue; }
        if (!in_array($ext, $allowed, TRUE))    { $errors[] = $orig . ' (type not allowed)'; continue; }

        $rand = function_exists('random_bytes') ? bin2hex(random_bytes(8)) : md5(uniqid(mt_rand(), TRUE));
        $safe = $rand . '_' . time() . '.' . $ext;
        if (!@move_uploaded_file($_FILES['files']['tmp_name'][$i], $dir_abs . $safe)) { $errors[] = $orig; continue; }
        @chmod($dir_abs . $safe, 0644);

        $stored[] = array(
            'file_name'   => preg_replace('/[^\w.\- ]+/u', '_', $orig),
            'stored_name' => $safe,
            'rel_path'    => $dir_rel,
            'file_ext'    => $ext,
            'mime_type'   => (string)$_FILES['files']['type'][$i],
            'file_size'   => $size,
            'is_image'    => in_array($ext, array('jpg','jpeg','png','gif','webp','bmp','heic','heif'), TRUE) ? 1 : 0,
        );
    }

    if (empty($stored)) {
        echo json_encode(array("status" => false, "message" => "upload_failed", "details" => $errors)); return;
    }

    /* The poster frame for a video, made on the sending phone.
     *
     * This box has no ffmpeg, so PHP cannot pull a frame out of an H.264
     * stream however much GD and Imagick are installed - the only machine in
     * the chain that can decode the video cheaply is the one that just shot
     * it. So the phone sends a still and it is filed here.
     *
     * Stored as <stored_name>.jpg beside the video, and returned in no
     * payload at all: attachment rows are built by the Chat model, which is
     * not ours to change, and a name both sides can derive needs no column.
     * The app asks for video_url + '.jpg' and copes with a 404, which is what
     * every video sent before today will give it.
     *
     * Only for a single video upload. A poster is meaningless against a batch
     * of five files - there would be no way to say which one it belonged to -
     * and it is written by hand, so it is checked like anything else that
     * arrives from outside: it must really be a JPEG, and a small one. */
    if (count($stored) === 1
        && isset($_FILES['thumb'])
        && $_FILES['thumb']['error'] === UPLOAD_ERR_OK
        && in_array($stored[0]['file_ext'], array('mp4','mov','m4v','3gp','webm','mkv','avi'), TRUE)
        && (int) $_FILES['thumb']['size'] > 0
        && (int) $_FILES['thumb']['size'] <= 2097152) {

        $info = @getimagesize($_FILES['thumb']['tmp_name']);

        if ($info && isset($info[2]) && (int) $info[2] === IMAGETYPE_JPEG) {
            $poster = $dir_abs . $stored[0]['stored_name'] . '.jpg';
            if (@move_uploaded_file($_FILES['thumb']['tmp_name'], $poster)) {
                @chmod($poster, 0644);
            }
        }
    }

    $msg_id = $this->chat->send_message($conv_id, $me, $caption, array('type' => 'file', 'reply_to' => $reply_to));
    foreach ($stored as $f) $this->chat->attach_file($conv_id, $msg_id, $me, $f);

    $conv = $this->chat->get_conversation($conv_id);
    $this->_chat_after($conv_id, $msg_id, $conv, $caption !== '' ? $caption : 'shared a file', $me);

    echo json_encode(array("status" => true, "message" => $this->chat->message($msg_id), "skipped" => $errors));
}

/** Mark a conversation read up to its latest message (or a given id). */
public function chat_read()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $this->chat->mark_read($conv_id, $me, (int)($input['up_to_id'] ?? 0));
    echo json_encode(array("status" => true, "unread" => $this->chat->unread_totals($me)));
}

/** Unread totals only (badge on the nav). */
public function chat_unread()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }
    echo json_encode(array("status" => true, "unread" => $this->chat->unread_totals($me)));
}

/** Set my typing state and return who else is typing here. */
public function chat_typing()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $isTyping = !empty($input['typing']);
    $this->chat->touch_presence($me, $isTyping ? $conv_id : 0);

    echo json_encode(array("status" => true, "typing" => $this->chat->typing_in($conv_id, $me)));
}

/** Staff directory for starting DMs / picking group members. */
public function chat_directory()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $search = trim((string)($input['search'] ?? ''));
    echo json_encode(array("status" => true, "people" => $this->chat->directory($me, $search)));
}

/** Add members to a group (owner/admin only). */
public function chat_add_members()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;
    if (!$this->_chat_can_manage($conv_id, $me)) {
        echo json_encode(array("status" => false, "message" => "forbidden")); return;
    }

    $members = array();
    foreach ((array)($input['members'] ?? array()) as $mid) {
        $mid = (int)$mid;
        if ($mid > 0) $members[] = array('id' => $mid, 'type' => 'user');
    }
    if (empty($members)) { echo json_encode(array("status" => false, "message" => "no_members")); return; }

    $this->chat->add_members($conv_id, $me, $members);
    echo json_encode(array("status" => true, "members" => $this->chat->members($conv_id)));
}

/** Remove one member from a group (owner/admin only). */
public function chat_remove_member()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;
    if (!$this->_chat_can_manage($conv_id, $me)) {
        echo json_encode(array("status" => false, "message" => "forbidden")); return;
    }

    $target = (int)($input['member_id'] ?? 0);
    if ($target <= 0) { echo json_encode(array("status" => false, "message" => "bad_request")); return; }

    $trow = $this->chat->participant_row($conv_id, array('id' => $target, 'type' => 'user'));
    if ($trow && $trow->member_role === 'owner') {
        echo json_encode(array("status" => false, "message" => "cannot_remove_owner")); return;
    }

    $this->chat->remove_member($conv_id, $me, $target, 'user');
    echo json_encode(array("status" => true, "members" => $this->chat->members($conv_id)));
}

/** Realtime poll: everything that happened since `cursor`, plus fresh unread.
 *  The app calls this every few seconds; on new events it refetches the
 *  affected conversations (chat_history with after=lastId) and the sidebar. */
public function chat_poll()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $cursor = (int)($input['cursor'] ?? 0);
    $this->chat->touch_presence($me);

    $events = $this->chat->events_since($me, $cursor, 100);

    $out    = array();
    $newCur = $cursor;
    foreach ($events as $e) {
        $newCur = max($newCur, (int)$e->id);
        $out[] = array(
            'id'              => (int)$e->id,
            'conversation_id' => (int)$e->conversation_id,
            'message_id'      => (int)$e->message_id,
            'event_type'      => $e->event_type,
            'actor_id'        => (int)$e->actor_id,
        );
    }

    echo json_encode(array(
        "status" => true,
        "cursor" => $newCur,
        "events" => $out,
        "unread" => $this->chat->unread_totals($me),
    ));
}

/* ----------------------------------------------------------------------------
 *  FULL-PARITY ENDPOINTS  (edit/delete, reactions, pins, forward, private
 *  reply, record links, group admin, seen-by, search, notifications, calls)
 * --------------------------------------------------------------------------*/

/** How long after sending a message its author may still edit/unsend it. */
private function _chat_edit_window() { return 900; } // 15 min, matches web

/** Fetch a message row and gate on membership of its conversation. */
private function _chat_msg_guard($msg_id, $me, &$row)
{
    $row = $this->db->select('*')->from('chat_message')
        ->where('id', (int)$msg_id)->get()->row();
    if (!$row) { echo json_encode(array("status" => false, "message" => "not_found")); return false; }
    return $this->_chat_guard((int)$row->conversation_id, $me);
}

/** Edit own message within the window. */
public function chat_edit()
{
    $input  = $this->_chat_boot($me);
    $msg_id = (int)($input['message_id'] ?? 0);
    $body   = trim((string)($input['body'] ?? ''));
    if ($msg_id <= 0 || $body === '') { echo json_encode(array("status" => false, "message" => "bad_request")); return; }

    $row = null;
    if (!$this->_chat_msg_guard($msg_id, $me, $row)) return;

    if ((int)$row->sender_id !== (int)$me['id']) {
        echo json_encode(array("status" => false, "message" => "forbidden")); return;
    }
    if ($row->is_deleted) { echo json_encode(array("status" => false, "message" => "deleted")); return; }
    if ((time() - strtotime($row->created_at)) > $this->_chat_edit_window()) {
        echo json_encode(array("status" => false, "message" => "edit_window_expired")); return;
    }

    $this->chat->edit_message($msg_id, $body);
    $this->chat->push_event((int)$row->conversation_id, 'edit', $me, $msg_id);
    echo json_encode(array("status" => true, "message" => $this->chat->message($msg_id)));
}

/** Unsend own message within the window (soft delete). */
public function chat_delete()
{
    $input  = $this->_chat_boot($me);
    $msg_id = (int)($input['message_id'] ?? 0);

    $row = null;
    if (!$this->_chat_msg_guard($msg_id, $me, $row)) return;

    if ((int)$row->sender_id !== (int)$me['id']) {
        echo json_encode(array("status" => false, "message" => "forbidden")); return;
    }
    if ($row->is_deleted) { echo json_encode(array("status" => true, "message" => $this->chat->message($msg_id))); return; }
    if ((time() - strtotime($row->created_at)) > $this->_chat_edit_window()) {
        echo json_encode(array("status" => false, "message" => "unsend_window_expired")); return;
    }

    $this->chat->delete_message($msg_id, $me);
    $this->_chat_drop_attachments($msg_id);
    $this->chat->push_event((int)$row->conversation_id, 'delete', $me, $msg_id);
    echo json_encode(array("status" => true, "message" => $this->chat->message($msg_id)));
}

/**
 * Take the media with the message.
 *
 * delete_message() only flags the row, so an unsent photo stayed on screen
 * underneath "This message was deleted", and its URL stayed in the payload -
 * deleted in name and nowhere else. Anyone with the link still had the file.
 *
 * The rows go unconditionally. The FILE on disk goes only when nothing else
 * points at it: forward_message copies the attachment row keeping the same
 * stored_name - "same file, no re-upload" - so one file can back several
 * messages in different conversations, and unlinking blindly would blank
 * every forwarded copy of a photo the sender only meant to unsend here.
 */
private function _chat_drop_attachments($msg_id)
{
    $atts = $this->db->select('id, stored_name, rel_path')
                     ->from('chat_attachment')
                     ->where('message_id', (int) $msg_id)
                     ->get()
                     ->result();

    foreach ($atts as $a) {
        $this->db->where('id', (int) $a->id)->delete('chat_attachment');

        /* Counted AFTER the delete, so the row just removed is not counted
           as a reference to itself. */
        $others = $this->db->where('stored_name', $a->stored_name)
                           ->count_all_results('chat_attachment');

        if ((int) $others === 0) {
            $path = chat_upload_path . $a->rel_path . $a->stored_name;
            if (is_file($path)) @unlink($path);

            /* A video's poster frame is not an attachment row - it is a file
               named after one - so nothing else would ever collect it, and it
               is a readable picture of the thing just unsent. It goes at the
               same moment the video does, and under the same reference count:
               a forwarded copy still pointing at this stored_name keeps
               both. */
            if (is_file($path . '.jpg')) @unlink($path . '.jpg');
        }
    }
}

/** Toggle an emoji reaction on a message. */
public function chat_react()
{
    $input  = $this->_chat_boot($me);
    $msg_id = (int)($input['message_id'] ?? 0);
    $emoji  = trim((string)($input['emoji'] ?? ''));
    if ($msg_id <= 0 || $emoji === '') { echo json_encode(array("status" => false, "message" => "bad_request")); return; }
    if (function_exists('mb_strlen') && mb_strlen($emoji, 'UTF-8') > 4) {
        echo json_encode(array("status" => false, "message" => "bad_emoji")); return;
    }

    $row = null;
    if (!$this->_chat_msg_guard($msg_id, $me, $row)) return;
    if ($row->is_deleted) { echo json_encode(array("status" => false, "message" => "deleted")); return; }

    $action = $this->chat->toggle_reaction($msg_id, $me, $emoji);
    $this->chat->push_event((int)$row->conversation_id, 'reaction', $me, $msg_id);
    echo json_encode(array("status" => true, "action" => $action, "message" => $this->chat->message($msg_id)));
}

/** Pin / unpin a message. */
public function chat_pin()
{
    $input  = $this->_chat_boot($me);
    $msg_id = (int)($input['message_id'] ?? 0);

    $row = null;
    if (!$this->_chat_msg_guard($msg_id, $me, $row)) return;
    if ($row->is_deleted) { echo json_encode(array("status" => false, "message" => "deleted")); return; }

    $days = (int)($input['days'] ?? 0);
    if (!array_key_exists($days, $this->chat->pin_durations())) $days = 0;
    $state = $this->chat->toggle_pin($msg_id, $me, $days);
    $this->chat->push_event((int)$row->conversation_id, 'pin', $me, $msg_id);
    echo json_encode(array(
        "status" => true,
        "pinned" => $state,
        "pinned_list" => $this->chat->pinned_messages((int)$row->conversation_id),
    ));
}

/** Attach a DF / task / lead record to a message. */
public function chat_tag_record()
{
    $input    = $this->_chat_boot($me);
    $msg_id   = (int)($input['message_id'] ?? 0);
    $ref_type = chat_clean_ref_type($input['ref_type'] ?? '');
    $ref_id   = (int)($input['ref_id'] ?? 0);
    if ($msg_id <= 0 || $ref_id <= 0 || $ref_type === '') { echo json_encode(array("status" => false, "message" => "bad_request")); return; }

    $row = null;
    if (!$this->_chat_msg_guard($msg_id, $me, $row)) return;

    $this->chat->tag_record((int)$row->conversation_id, $msg_id, $me, $ref_type, $ref_id);
    $this->chat->push_event((int)$row->conversation_id, 'edit', $me, $msg_id);
    echo json_encode(array("status" => true, "message" => $this->chat->message($msg_id)));
}

/** Forward a message into one or more conversations (optional covering note). */
public function chat_forward()
{
    $input  = $this->_chat_boot($me);
    $msg_id = (int)($input['message_id'] ?? 0);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $src = $this->db->select('*')->from('chat_message')->where('id', $msg_id)->get()->row();
    if (!$src) { echo json_encode(array("status" => false, "message" => "not_found")); return; }
    if (!$this->chat->is_participant((int)$src->conversation_id, $me)) {
        echo json_encode(array("status" => false, "message" => "forbidden")); return;
    }
    if ($src->is_deleted) { echo json_encode(array("status" => false, "message" => "deleted")); return; }

    $targets = $input['targets'] ?? array();
    if (!is_array($targets) || empty($targets)) { echo json_encode(array("status" => false, "message" => "no_target")); return; }
    $targets = array_slice(array_unique(array_map('intval', $targets)), 0, 20);

    $note = trim((string)($input['note'] ?? ''));
    $sent = 0; $refused = 0; $duplicate = 0;

    foreach ($targets as $conv_id) {
        if ($conv_id <= 0 || !$this->chat->is_participant($conv_id, $me)) { $refused++; continue; }
        $tconv = $this->chat->get_conversation($conv_id);
        if ($tconv && !empty($tconv->archived_at) && $tconv->archived_at !== '0000-00-00 00:00:00') { $refused++; continue; }
        if ($this->chat->forwarded_recently($msg_id, $conv_id, $me)) { $duplicate++; $sent++; continue; }

        $new_id = $this->chat->forward_message($src, $conv_id, $me);
        $conv   = $this->chat->get_conversation($conv_id);

        if ($note !== '') {
            $note_id = $this->chat->send_message($conv_id, $me, $note, array('type' => 'text'));
            $this->_chat_after($conv_id, $note_id, $conv, $note, $me);
        } else {
            $this->_chat_after($conv_id, $new_id, $conv, $src->body !== '' ? $src->body : 'Forwarded a file', $me);
        }
        $sent++;
    }

    echo json_encode(array("status" => $sent > 0, "sent" => $sent, "refused" => $refused, "duplicate" => $duplicate));
}

/** Reply privately to a group message: opens the DM with its author + origin. */
public function chat_private_reply()
{
    $input  = $this->_chat_boot($me);
    $msg_id = (int)($input['message_id'] ?? 0);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $src = $this->db->select('*')->from('chat_message')->where('id', $msg_id)->get()->row();
    if (!$src) { echo json_encode(array("status" => false, "message" => "not_found")); return; }
    if (!$this->chat->is_participant((int)$src->conversation_id, $me)) {
        echo json_encode(array("status" => false, "message" => "forbidden")); return;
    }
    if ((int)$src->sender_id === (int)$me['id']) {
        echo json_encode(array("status" => false, "message" => "own_message")); return;
    }

    $conv_id = $this->chat->ensure_direct($me, (int)$src->sender_id, $src->sender_type);
    $origin  = $this->chat->message($msg_id);

    echo json_encode(array(
        "status" => true,
        "conversation_id" => (int)$conv_id,
        "origin" => array(
            "id"   => $msg_id,
            "from" => $origin ? $origin['sender_name'] : '',
            "body" => $origin ? $origin['body'] : '',
        ),
    ));
}

/** Leave a group (owner hands over to the longest-standing member first). */
public function chat_leave()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $conv = $this->chat->get_conversation($conv_id);
    if (!$conv || $conv->type === 'direct') { echo json_encode(array("status" => false, "message" => "not_a_channel")); return; }

    $mine = $this->chat->participant_row($conv_id, $me);
    if ($mine && $mine->member_role === 'owner') {
        $others = array();
        foreach ($this->chat->participant_pairs($conv_id) as $p) {
            if ((int)$p['id'] === (int)$me['id'] && $p['type'] === $me['type']) continue;
            $others[] = $p;
        }
        if (!empty($others)) {
            $this->chat->set_member_role($conv_id, $others[0]['id'], $others[0]['type'], 'owner');
        }
    }

    $this->chat->remove_member($conv_id, $me, $me['id'], $me['type']);
    $this->chat->push_event($conv_id, 'member', $me);
    echo json_encode(array("status" => true));
}

/** Rename a group / edit its description (owner/admin). */
public function chat_rename()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;
    if (!$this->_chat_can_manage($conv_id, $me)) { echo json_encode(array("status" => false, "message" => "forbidden")); return; }

    $name = trim((string)($input['name'] ?? ''));
    if ($name === '') { echo json_encode(array("status" => false, "message" => "name_required")); return; }
    $desc = trim((string)($input['description'] ?? ''));

    $this->chat->rename_channel($conv_id, $me, $name, $desc);
    $this->chat->push_event($conv_id, 'channel', $me);
    echo json_encode(array("status" => true));
}

/** Promote/demote a member to admin (owner only). */
public function chat_set_role()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $conv = $this->chat->get_conversation($conv_id);
    if (!$conv || $conv->type === 'direct') { echo json_encode(array("status" => false, "message" => "not_a_group")); return; }

    $mine = $this->chat->participant_row($conv_id, $me);
    if (!$mine || $mine->member_role !== 'owner') {
        echo json_encode(array("status" => false, "message" => "forbidden")); return;
    }

    $target_id = (int)($input['member_id'] ?? 0);
    $role = (($input['member_role'] ?? '') === 'admin') ? 'admin' : 'member';

    $target = $this->db->select('member_role')->from('chat_participant')
        ->where('conversation_id', $conv_id)->where('user_id', $target_id)
        ->where('user_type', 'user')->where('is_active', 1)->get()->row();
    if (!$target) { echo json_encode(array("status" => false, "message" => "not_a_member")); return; }
    if ($target->member_role === 'owner') { echo json_encode(array("status" => false, "message" => "cannot_change_owner")); return; }

    $this->chat->set_member_role($conv_id, $target_id, 'user', $role);

    $who  = $this->chat->resolve_people(array(array('id' => $target_id, 'type' => 'user')));
    $name = $who['user:' . $target_id]['name'];
    $this->chat->system_message($conv_id, $me, $role === 'admin'
        ? ($me['name'] . ' made ' . $name . ' a group admin')
        : ($me['name'] . ' removed admin rights from ' . $name));
    $this->chat->push_event($conv_id, 'member', $me);

    echo json_encode(array("status" => true, "members" => $this->chat->members($conv_id)));
}

/** Set or clear the group photo (owner/admin). Multipart: photo file, or clear=1. */
public function chat_group_photo()
{
    $this->load->model('Chat_model', 'chat');
    $this->load->helper('chat_access');

    $uid = (int)$this->input->post('user_id');
    $me  = $uid ? $this->_chat_me($uid) : null;
    $conv_id = (int)$this->input->post('conversation_id');
    if (!$this->_chat_guard($conv_id, $me)) return;
    if (!$this->_chat_can_manage($conv_id, $me)) { echo json_encode(array("status" => false, "message" => "forbidden")); return; }

    if ($this->input->post('clear')) {
        $this->chat->set_group_photo($conv_id, $me, '');
        $this->chat->push_event($conv_id, 'channel', $me);
        echo json_encode(array("status" => true, "photo" => "")); return;
    }

    if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(array("status" => false, "message" => "no_file")); return;
    }
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array('jpg','jpeg','png','gif','webp'), true)) {
        echo json_encode(array("status" => false, "message" => "bad_type")); return;
    }
    if ((int)$_FILES['photo']['size'] > 26214400) { echo json_encode(array("status" => false, "message" => "too_large")); return; }
    if (@getimagesize($_FILES['photo']['tmp_name']) === false) { echo json_encode(array("status" => false, "message" => "not_an_image")); return; }

    $dir_rel = 'groups/';
    $dir_abs = chat_upload_path . $dir_rel;
    if (!is_dir($dir_abs) && !@mkdir($dir_abs, 0755, true)) { echo json_encode(array("status" => false, "message" => "storage")); return; }

    $rand = function_exists('random_bytes') ? bin2hex(random_bytes(6)) : md5(uniqid(mt_rand(), true));
    $name = 'g' . $conv_id . '_' . $rand . '.' . $ext;
    if (!@move_uploaded_file($_FILES['photo']['tmp_name'], $dir_abs . $name)) { echo json_encode(array("status" => false, "message" => "storage")); return; }
    @chmod($dir_abs . $name, 0644);

    $this->chat->set_group_photo($conv_id, $me, $dir_rel . $name);
    $this->chat->push_event($conv_id, 'channel', $me);
    echo json_encode(array("status" => true, "photo" => chat_upload_url . $dir_rel . $name));
}

/** Who has seen a message, who has not (author only). */
public function chat_seen_by()
{
    $input  = $this->_chat_boot($me);
    $msg_id = (int)($input['message_id'] ?? 0);

    $row = null;
    if (!$this->_chat_msg_guard($msg_id, $me, $row)) return;
    if ((int)$row->sender_id !== (int)$me['id']) { echo json_encode(array("status" => false, "message" => "forbidden")); return; }

    $r = $this->chat->read_receipts((int)$row->conversation_id, $msg_id, $me);
    echo json_encode(array("status" => true, "seen" => $r['seen'], "pending" => $r['pending']));
}

/** Search message content (optionally within one conversation). */
public function chat_search_messages()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $q    = (string)($input['q'] ?? '');
    $conv = (int)($input['conv'] ?? 0);
    if ($conv > 0 && !$this->chat->is_participant($conv, $me)) $conv = 0;

    echo json_encode(array("status" => true, "results" => $this->chat->search_messages($me, $q, $conv)));
}

/** Type-ahead for the DF/task/lead record picker. */
public function chat_search_records()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $kind = chat_clean_ref_type($input['kind'] ?? '');
    if ($kind === '') $kind = 'lead';
    echo json_encode(array("status" => true, "records" => $this->chat->search_records((string)($input['q'] ?? ''), $kind)));
}

/** The bell feed for this user. */
public function chat_notifications()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }
    echo json_encode(array("status" => true, "notifications" => $this->chat->notifications($me)));
}

/** Each member's read pointer, so "seen by" counts can refresh live. */
public function chat_read_state()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $out = array();
    foreach ($this->chat->participant_pairs($conv_id) as $p) {
        $out[] = array('id' => $p['id'], 'type' => $p['type'], 'read' => $p['last_read_message_id']);
    }
    echo json_encode(array("status" => true, "readers" => $out));
}

/* ---- Calls / meeting links -------------------------------------------- */

/** Validate a join URL actually belongs to the claimed provider (https + host). */
private function _chat_valid_meeting_url($provider, $url)
{
    $url = trim((string)$url);
    if ($url === '' || strlen($url) > 500) return false;
    $parts = @parse_url($url);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return false;
    if (strtolower($parts['scheme']) !== 'https') return false;

    $host = strtolower($parts['host']);
    $defs = chat_providers();
    $provider = chat_clean_provider($provider);
    $allowed = isset($defs[$provider]['hosts']) ? $defs[$provider]['hosts'] : array();
    foreach ($allowed as $a) {
        if ($host === $a || substr($host, -(strlen($a) + 1)) === '.' . $a) return true;
    }
    return false;
}

/** Read or save the current user's personal Zoom / Meet room links. */
public function chat_meeting_links()
{
    $input = $this->_chat_boot($me);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    if (isset($input['provider'])) {
        $provider = chat_clean_provider($input['provider']);
        $url      = trim((string)($input['join_url'] ?? ''));
        if ($url === '') {
            $this->chat->save_meeting_link($me, $provider, '');
        } elseif (!$this->_chat_valid_meeting_url($provider, $url)) {
            echo json_encode(array("status" => false, "message" => "bad_url")); return;
        } else {
            $this->chat->save_meeting_link($me, $provider, $url);
        }
    }
    echo json_encode(array("status" => true, "links" => $this->chat->meeting_links($me)));
}

/** Start a Zoom / Meet call in a conversation. */
public function chat_start_call()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $raw   = strtolower(trim((string)($input['provider'] ?? '')));
    $known = chat_providers();
    if ($raw === '' || !isset($known[$raw])) { echo json_encode(array("status" => false, "message" => "unknown_provider")); return; }
    $provider = $raw;
    $url = trim((string)($input['join_url'] ?? ''));

    if ($url !== '' && !$this->_chat_valid_meeting_url($provider, $url)) {
        echo json_encode(array("status" => false, "message" => "bad_url")); return;
    }
    if ($url === '') {
        $saved = $this->chat->meeting_links($me);
        if (!empty($saved[$provider]) && $this->_chat_valid_meeting_url($provider, $saved[$provider])) {
            $url = $saved[$provider];
        }
    }

    $conv  = $this->chat->get_conversation($conv_id);
    $topic = trim((string)($input['topic'] ?? ''));
    $call  = $this->chat->start_call($conv_id, $me, $provider, $url, $topic);

    $label = chat_provider_label($provider);
    $this->chat->notify_conversation($conv_id, $me, $call['message_id'], 'call',
        $me['name'] . ' started a ' . $label . ' call',
        $conv->type === 'direct' ? 'Tap to join' : ('in ' . $conv->name));
    $this->chat->push_event($conv_id, 'message', $me, $call['message_id']);
    $this->_chat_push($conv_id, $me, $me['name'] . ' started a ' . $label . ' call', $conv);

    echo json_encode(array(
        "status"    => true,
        "call_id"   => $call['call_id'],
        "provider"  => $provider,
        "join_url"  => $url,
        "needs_url" => ($url === ''),
        "message"   => $this->chat->message($call['message_id']),
    ));
}

/** Attach the join link after the starter creates the meeting. */
public function chat_attach_call_url()
{
    $input   = $this->_chat_boot($me);
    $call_id = (int)($input['call_id'] ?? 0);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $call = $this->chat->get_call($call_id);
    if (!$call || !$this->_chat_guard((int)$call->conversation_id, $me)) return;
    if ((int)$call->started_by !== (int)$me['id']) { echo json_encode(array("status" => false, "message" => "forbidden")); return; }

    $url = trim((string)($input['join_url'] ?? ''));
    if (!$this->_chat_valid_meeting_url($call->provider, $url)) { echo json_encode(array("status" => false, "message" => "bad_url")); return; }

    $this->chat->set_call_url($call_id, $url);
    if (!empty($input['remember'])) $this->chat->save_meeting_link($me, $call->provider, $url);

    $this->chat->push_event((int)$call->conversation_id, 'edit', $me, (int)$call->message_id);
    echo json_encode(array("status" => true, "message" => $this->chat->message((int)$call->message_id)));
}

/** End a call (starter only). */
public function chat_end_call()
{
    $input   = $this->_chat_boot($me);
    $call_id = (int)($input['call_id'] ?? 0);
    if (!$me) { echo json_encode(array("status" => false, "message" => "auth")); return; }

    $call = $this->chat->get_call($call_id);
    if (!$call || !$this->_chat_guard((int)$call->conversation_id, $me)) return;
    if ((int)$call->started_by !== (int)$me['id']) { echo json_encode(array("status" => false, "message" => "forbidden")); return; }

    $this->chat->end_call($call_id);
    $this->chat->push_event((int)$call->conversation_id, 'edit', $me, (int)$call->message_id);
    echo json_encode(array("status" => true, "message" => $this->chat->message((int)$call->message_id)));
}



/* ============================================================================
 *  ENGINEER VISIT EXECUTION - mobile mirror of the web pages
 *      ServiceLeads/engineer_visit_list   (calendar + status tabs + KPI)
 *      ServiceLeads/engineer_visit_detail (MOM updates, extend, complete)
 *
 *  These REUSE Service_visit_execution_model, exactly as the web controller
 *  does, so the app and the browser can never drift on what a visit is or
 *  what completing one means. Nothing outside this file is modified.
 *
 *  Scoping, which is the whole point of the module on a phone:
 *      admin    -> every visit, optionally filtered to one engineer
 *      engineer -> only rows where engineer_id is their own user_id
 *  It is enforced HERE, on every read and every write, not by hiding
 *  buttons in the app - a hidden button is not a permission.
 *
 *  Status lifecycle (owned by the model, mirrored here for reference):
 *      Scheduled --first MOM update--> On-Site --MOM + signed doc--> Completed
 * ==========================================================================*/

/** Body may be JSON (normal calls) or multipart form fields (uploads). */
private function _visit_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json) && !empty($json)) return $json;
    }
    $post = $this->input->post();
    return is_array($post) ? $post : array();
}

/** Identity + the admin test used by get_active_visits_api, kept identical. */
private function _visit_me($in)
{
    $id   = (int) ($in['user_id'] ?? 0);
    $role = (int) ($in['role_id'] ?? 0);
    if ($id <= 0) return null;

    return array(
        'id'       => $id,
        'role'     => $role,
        'is_admin' => ($role === 12 || $role === 41 || $id === 111),
    );
}

private function _visit_model()
{
    $this->load->model('Service_visit_execution_model', 'svem');
    return $this->svem;
}

private function _visit_out($payload)
{
    echo json_encode($payload);
}

private function _visit_fail($message, $extra = array())
{
    $this->_visit_out(array_merge(array('status' => false, 'message' => $message), $extra));
}

/** An engineer owns their own visits; an admin may write to any of them. */
private function _visit_can_write($visit, $me)
{
    if (empty($visit) || empty($me)) return false;
    if (!empty($me['is_admin'])) return true;
    return ((int) $visit->engineer_id === (int) $me['id']);
}

private function _visit_status_map()
{
    return array(
        'scheduled' => 'Scheduled',
        'on-site'   => 'On-Site',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    );
}

/**
 * Same overlap test the scheduler uses before it books an engineer, copied
 * rather than called because it is private to ServiceLeads and that file is
 * not ours to touch. An extension that collides with the engineer's next job
 * must be refused here too, or the app becomes the way round the rule.
 */
private function _visit_conflict($engineer_id, $start_date, $end_date, $exclude_visit_id = 0)
{
    $this->db->select("
        v.start_date,
        v.end_date,
        so.op_no,
        IFNULL(cm_spares.company_name, cm_marketing.company_name) as customer_name
    ");
    $this->db->from('service_engineer_visits v');
    $this->db->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id', 'inner');
    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
    $this->db->where('v.engineer_id', (int) $engineer_id);
    $this->db->where('v.start_date <=', $end_date);
    $this->db->where('v.end_date >=', $start_date);
    if ((int) $exclude_visit_id > 0) {
        $this->db->where('v.visit_id !=', (int) $exclude_visit_id);
    }
    if ($this->db->field_exists('visit_status', 'service_engineer_visits')) {
        $this->db->group_start();
        $this->db->where('v.visit_status !=', 'Completed');
        $this->db->where('v.visit_status !=', 'Cancelled');
        $this->db->or_where('v.visit_status IS NULL', null, false);
        $this->db->or_where('v.visit_status', '');
        $this->db->group_end();
    }

    return $this->db->order_by('v.start_date', 'ASC')->get()->row();
}

/** The counts behind the status tabs, over whatever the caller may see. */
private function _visit_kpi($visits)
{
    $kpi = array(
        'total' => 0, 'scheduled' => 0, 'on_site' => 0,
        'completed' => 0, 'cancelled' => 0, 'pending_docs' => 0,
    );

    foreach ($visits as $v) {
        $kpi['total']++;
        switch ($v->status_slug) {
            case 'scheduled': $kpi['scheduled']++; break;
            case 'on-site':   $kpi['on_site']++;   break;
            case 'completed': $kpi['completed']++; break;
            case 'cancelled': $kpi['cancelled']++; break;
        }
        // "finished the work, never sent the signed sheet" - the number the
        // service desk actually chases people about
        if ($v->status_slug !== 'cancelled' && (int) $v->document_count === 0) {
            $kpi['pending_docs']++;
        }
    }

    return $kpi;
}

/** Engineers the scheduler is allowed to book, for the admin filter. */
private function _visit_engineers()
{
    return $this->db->select("user_id, TRIM(CONCAT(
            UPPER(LEFT(first_name,1)), LOWER(SUBSTRING(first_name,2)), ' ',
            UPPER(LEFT(last_name,1)), LOWER(SUBSTRING(last_name,2))
        )) as name")
        ->from('system_users')
        ->where('user_status', 1)
        ->where_in('department_id', array(22, 14))
        ->order_by('first_name', 'ASC')
        ->order_by('last_name', 'ASC')
        ->get()->result_array();
}

/** Attach a tappable URL to each stored document row. */
private function _visit_decorate_documents($documents)
{
    foreach ($documents as $d) {
        $d->file_url = !empty($d->file_path) ? page_url1 . ltrim($d->file_path, '/') : '';
    }
    return $documents;
}

/* ----------------------------------------------------------------------------
 *  ENDPOINTS
 * --------------------------------------------------------------------------*/

/**
 * Every visit the caller may see, in ONE call: rows for the calendar, the
 * KPI counts, the engineer list for the admin filter.
 *
 * Unlike get_active_visits_api this does NOT drop Completed/Cancelled - the
 * app needs the history to show a month that has already been worked. Filter
 * with `status` instead.
 */
public function visit_list_api()
{
    try {
        $in = $this->_visit_input();
        $me = $this->_visit_me($in);
        if (!$me) return $this->_visit_fail('auth');

        $model      = $this->_visit_model();
        $status_map = $this->_visit_status_map();

        // Scope first, filter second: the KPI row must count everything this
        // person may see, not just the tab they happen to be looking at.
        $scope = array();
        if (!$me['is_admin']) {
            $scope['engineer_id'] = $me['id'];
        } elseif (!empty($in['engineer_id'])) {
            $scope['engineer_id'] = (int) $in['engineer_id'];
        }

        $all = $model->get_visit_rows($scope);
        $kpi = $this->_visit_kpi($all);

        $status_slug = strtolower(trim((string) ($in['status'] ?? '')));
        $from_date   = trim((string) ($in['from_date'] ?? ''));
        $to_date     = trim((string) ($in['to_date'] ?? ''));

        $visits = array();
        foreach ($all as $v) {
            if ($status_slug !== '' && $status_slug !== 'all' && $v->status_slug !== $status_slug) continue;
            // overlap, not containment: a visit that straddles the edge of the
            // month on screen still has days inside it
            if ($from_date !== '' && !empty($v->end_date)   && $v->end_date   < $from_date) continue;
            if ($to_date   !== '' && !empty($v->start_date) && $v->start_date > $to_date)   continue;
            $visits[] = $v;
        }

        $this->_visit_out(array(
            'status'     => true,
            'message'    => 'Visits fetched successfully',
            'data'       => $visits,
            'kpi'        => $kpi,
            'status_map' => $status_map,
            'engineers'  => $me['is_admin'] ? $this->_visit_engineers() : array(),
            'is_admin'   => $me['is_admin'],
            'user_id'    => $me['id'],
        ));
    } catch (Throwable $e) {
        $this->_visit_fail('Visit list failed: ' . $e->getMessage());
    }
}

/** One visit with everything the detail screen draws. */
public function visit_detail_api()
{
    try {
        $in = $this->_visit_input();
        $me = $this->_visit_me($in);
        if (!$me) return $this->_visit_fail('auth');

        $visit_id = (int) ($in['visit_id'] ?? 0);
        if ($visit_id <= 0) return $this->_visit_fail('visit_id is required');

        $model = $this->_visit_model();
        $visit = $model->get_visit_by_id($visit_id);
        if (!$visit) return $this->_visit_fail('Visit not found');

        // an engineer may not read another engineer's job card
        if (!$me['is_admin'] && (int) $visit->engineer_id !== (int) $me['id']) {
            return $this->_visit_fail('forbidden');
        }

        $documents = $this->_visit_decorate_documents($model->get_visit_documents($visit_id));
        $can_write = $this->_visit_can_write($visit, $me);
        $is_closed = in_array($visit->status_slug, array('completed', 'cancelled'), true);

        $this->_visit_out(array(
            'status'    => true,
            'message'   => 'Visit fetched successfully',
            'visit'     => $visit,
            'updates'   => $model->get_visit_updates($visit_id),
            'documents' => $documents,
            'versions'  => $model->get_visit_versions($visit_id, $visit),
            'can_write' => $can_write,
            // the app greys the buttons out; the endpoints refuse anyway
            'can_add_update' => $can_write && $visit->status_slug !== 'completed',
            'can_extend'     => $can_write && !$is_closed,
            'can_complete'   => $can_write && $visit->status_slug !== 'completed'
                                && (int) $visit->mom_count > 0 && count($documents) > 0,
            'is_admin'  => $me['is_admin'],
        ));
    } catch (Throwable $e) {
        $this->_visit_fail('Visit detail failed: ' . $e->getMessage());
    }
}

/**
 * Add or amend one day's MOM. Upserts on (visit_id, work_date) exactly as the
 * web form does, and the model flips a Scheduled visit to On-Site on the way
 * through - that is the ONLY thing in the system that sets On-Site.
 */
public function visit_save_update_api()
{
    try {
        $in = $this->_visit_input();
        $me = $this->_visit_me($in);
        if (!$me) return $this->_visit_fail('auth');

        $visit_id = (int) ($in['visit_id'] ?? 0);
        if ($visit_id <= 0) return $this->_visit_fail('visit_id is required');

        $model = $this->_visit_model();
        $visit = $model->get_visit_by_id($visit_id);
        if (!$visit) return $this->_visit_fail('Visit not found');
        if (!$this->_visit_can_write($visit, $me)) return $this->_visit_fail('forbidden');

        if ($visit->status_slug === 'completed') {
            return $this->_visit_fail('This visit is already completed. Please open a new visit if more work is required.');
        }

        $work_date  = trim((string) ($in['work_date'] ?? ''));
        $mom_points = trim((string) ($in['mom_points'] ?? ''));
        $next_plan  = trim((string) ($in['next_plan'] ?? ''));

        if ($work_date === '' || strtotime($work_date) === false || $mom_points === '') {
            return $this->_visit_fail('Please fill a valid work date and MOM update before saving.');
        }

        $saved = $model->save_daily_update($visit_id, array(
            'work_date'  => date('Y-m-d', strtotime($work_date)),
            'mom_points' => $mom_points,
            'next_plan'  => $next_plan,
            'user_id'    => $me['id'],
        ));

        if (!$saved) return $this->_visit_fail('The MOM update could not be saved. Please try again.');

        $this->_visit_out(array(
            'status'  => true,
            'message' => 'Daily MOM update saved successfully.',
            'visit'   => $model->get_visit_by_id($visit_id),
            'updates' => $model->get_visit_updates($visit_id),
        ));
    } catch (Throwable $e) {
        $this->_visit_fail('Save update failed: ' . $e->getMessage());
    }
}

/** Push the end date out, with a reason, recorded as a new schedule version. */
public function visit_extend_api()
{
    try {
        $in = $this->_visit_input();
        $me = $this->_visit_me($in);
        if (!$me) return $this->_visit_fail('auth');

        $visit_id = (int) ($in['visit_id'] ?? 0);
        if ($visit_id <= 0) return $this->_visit_fail('visit_id is required');

        $model = $this->_visit_model();
        $visit = $model->get_visit_by_id($visit_id);
        if (!$visit) return $this->_visit_fail('Visit not found');
        if (!$this->_visit_can_write($visit, $me)) return $this->_visit_fail('forbidden');

        if (in_array($visit->status_slug, array('completed', 'cancelled'), true)) {
            return $this->_visit_fail('Completed or cancelled visits cannot be extended.');
        }

        $new_end_date = trim((string) ($in['new_end_date'] ?? ''));
        $reason       = trim((string) ($in['extension_reason'] ?? ''));

        if ($new_end_date === '' || strtotime($new_end_date) === false) {
            return $this->_visit_fail('Please choose a valid revised end date for the extension.');
        }
        $new_end_date = date('Y-m-d', strtotime($new_end_date));

        if ($reason === '') {
            return $this->_visit_fail('Please mention the reason for extending this visit.');
        }
        if ($new_end_date <= (string) $visit->end_date) {
            return $this->_visit_fail('The revised end date must be after the current end date to extend the visit.');
        }

        /* Optional payment request for the extra days (web: the "Also raise a
           payment request" box). A bad amount is refused BEFORE extending -
           cheaper than an extension saved with a warning attached. */
        $raise_payment = (string) ($in['raise_payment_request'] ?? '') === '1'
                      || ($in['raise_payment_request'] ?? null) === true;
        $pay_amount = (float) str_replace(',', '', trim((string) ($in['extension_payment_amount'] ?? '')));
        if ($raise_payment && $pay_amount <= 0) {
            return $this->_visit_fail('The payment amount must be greater than zero.');
        }

        $conflict = $this->_visit_conflict((int) $visit->engineer_id, (string) $visit->start_date, $new_end_date, $visit_id);
        if (!empty($conflict)) {
            return $this->_visit_fail('This extension overlaps with another booking: '
                . $conflict->op_no . ' (' . $conflict->customer_name . ').');
        }

        if (!$model->extend_visit_schedule($visit_id, $new_end_date, $reason, $me['id'])) {
            return $this->_visit_fail('The engineer visit could not be extended due to a database issue.');
        }

        $message = 'Engineer visit extended successfully. A new schedule version has been captured.';
        $payment = null;
        if ($raise_payment) {
            /* $visit still holds the PRE-extension end date, which is what
               the paid window starts from */
            $payment = $this->_visit_extension_payment(
                $visit, $new_end_date, $reason, $pay_amount,
                trim((string) ($in['extension_payment_purpose'] ?? '')), $me
            );
            $message = $payment['success']
                ? $message . ' ' . $payment['message']
                : 'The visit was extended, but no payment request was raised: ' . $payment['message'];
        }

        $visit = $model->get_visit_by_id($visit_id);
        $this->_visit_out(array(
            'status'   => true,
            'message'  => $message,
            'payment'  => $payment,
            'visit'    => $visit,
            'versions' => $model->get_visit_versions($visit_id, $visit),
        ));
    } catch (Throwable $e) {
        $this->_visit_fail('Extend failed: ' . $e->getMessage());
    }
}

/**
 * The payment request an extension can carry - a copy of the web's
 * ServiceLeads::create_visit_extension_payment_request(). Returns
 * ['success' => bool, 'message' => string, ...]. Never rolls the extension
 * back: the extra days are a fact, the money is a request about them.
 *
 * Who may raise it matches spr_raise_api: the visit's engineer, a holder of
 * SERVICE PAYMENT REQUESTS, or an approver. Goes to the service HOD
 * (prestogroup_teams dept 22, loc 2 = Mr. Suketan, 111), pushed to his phone.
 * Raw queries only - see the builder-merge note on build 62.
 */
private function _visit_extension_payment($visit, $new_end_date, $reason, $amount, $purpose, $visit_me)
{
    try {
        $me = $this->_spr_me(array('user_id' => $visit_me['id']));
        $is_engineer = (int) $visit->engineer_id === (int) $visit_me['id'];
        if (!$is_engineer && empty($me['can_create']) && empty($me['can_approve'])) {
            return array('success' => false, 'message' => 'you do not have permission to raise service payment requests.');
        }
        if ($amount <= 0) {
            return array('success' => false, 'message' => 'the payment amount must be greater than zero.');
        }

        $opportunity_id = (int) $visit->opportunity_id;
        if ($opportunity_id <= 0) {
            return array('success' => false, 'message' => 'this visit is not linked to a won service order, and payment requests are raised against an order.');
        }

        $model = $this->_spr_boot();

        $order = $this->db->query("
            SELECT so.opportunity_id, so.op_no, so.customer_id,
                   COALESCE(NULLIF(sc.contact_person, ''), NULLIF(cd.customer_name, ''),
                            COALESCE(sc.company_name, cd.company_name)) AS customer_name,
                   COALESCE(sc.company_name, cd.company_name) AS company_name,
                   spo.po_number, spo.po_date, spo.po_amount
            FROM service_opportunities so
            INNER JOIN service_purchase_orders spo
                ON spo.id = (SELECT MAX(sp2.id) FROM service_purchase_orders sp2
                             WHERE sp2.opportunity_id = so.opportunity_id)
            LEFT JOIN spares_customers sc ON sc.customer_id = so.customer_id
            LEFT JOIN customer_detail cd ON cd.id = so.customer_id
            WHERE so.opportunity_id = ?
            LIMIT 1", array($opportunity_id))->row();
        if (empty($order)) {
            return array('success' => false, 'message' => 'no customer purchase order is linked to this service order, so the request has nothing to sit on.');
        }

        $hod_id = $this->_spr_hod_id();
        if ($hod_id <= 0) {
            return array('success' => false, 'message' => 'the service HOD is not mapped yet, so there is nobody to approve it.');
        }

        $previous_end = (string) $visit->end_date;
        $from = date('Y-m-d', strtotime($previous_end . ' +1 day'));
        if (strtotime($from) > strtotime($new_end_date)) $from = $new_end_date;

        $reason = trim((string) $reason);
        if ($purpose === '') {
            $purpose = 'Payment request for the visit extension from '
                . date('d M Y', strtotime($previous_end)) . ' to ' . date('d M Y', strtotime($new_end_date)) . '.';
            if ($reason !== '') $purpose .= "\n" . $reason;
        }

        $engineer_id = (int) $visit->engineer_id;
        $code = $model->get_next_request_code();
        $data = array(
            'request_code'          => $code,
            'opportunity_id'        => (int) $order->opportunity_id,
            'visit_id'              => (int) $visit->visit_id,
            'parent_request_id'     => null,
            'request_basis'         => 'VISIT',
            'request_type'          => 'Visit Extension',
            'request_title'         => 'Visit extension up to ' . date('d M Y', strtotime($new_end_date)),
            'op_no'                 => (string) $order->op_no,
            'customer_id'           => (int) $order->customer_id,
            'customer_name'         => (string) $order->customer_name,
            'po_number'             => (string) $order->po_number,
            'po_date'               => !empty($order->po_date) ? $order->po_date : null,
            'po_amount'             => (float) $order->po_amount,
            'engineer_id'           => $engineer_id > 0 ? $engineer_id : null,
            'requested_for_user_id' => $engineer_id > 0 ? $engineer_id : (int) $visit_me['id'],
            'service_from_date'     => $from,
            'service_to_date'       => $new_end_date,
            'request_date'          => date('Y-m-d'),
            'amount'                => $amount,
            'purpose'               => $purpose,
            'extension_reason'      => $reason !== '' ? $reason : null,
            'attachment'            => null,
            'status'                => 'Pending HOD Approval',
            'hod_id'                => $hod_id,
            'created_by'            => (int) $visit_me['id'],
            'created_at'            => date('Y-m-d H:i:s'),
        );

        $this->db->trans_start();
        $request_id = (int) $model->create_request($data);
        $this->db->trans_complete();
        if (!$this->db->trans_status() || $request_id <= 0) {
            return array('success' => false, 'message' => 'the request could not be saved because of a database issue. Please try again from PMS web.');
        }

        $this->_spr_notify(
            $hod_id,
            'Service Payment Approval Required',
            'Visit extension payment request ' . $code . ' for ' . $order->company_name
                . ' (Rs. ' . number_format($amount, 2) . ') needs your approval.',
            $request_id,
            (int) $visit->visit_id,
            'spr_approvals'
        );

        return array(
            'success'      => true,
            'message'      => 'Payment request ' . $code . ' for Rs. ' . number_format($amount, 2)
                            . ' has been sent to the service HOD for approval.',
            'request_id'   => $request_id,
            'request_code' => $code,
        );
    } catch (Throwable $e) {
        return array('success' => false, 'message' => 'the request could not be saved (' . $e->getMessage() . ').');
    }
}

/**
 * Upload one signed completion document (multipart, field `document`).
 *
 * Split out from completing on purpose: on a phone the photo of the signed
 * sheet is the part that fails - bad signal, big file - and an engineer who
 * has uploaded it should not have to redo it because the completion call
 * timed out. Upload, then complete.
 */
public function visit_upload_document_api()
{
    try {
        $in = $this->_visit_input();
        $me = $this->_visit_me($in);
        if (!$me) return $this->_visit_fail('auth');

        $visit_id = (int) ($in['visit_id'] ?? 0);
        if ($visit_id <= 0) return $this->_visit_fail('visit_id is required');

        $model = $this->_visit_model();
        $visit = $model->get_visit_by_id($visit_id);
        if (!$visit) return $this->_visit_fail('Visit not found');
        if (!$this->_visit_can_write($visit, $me)) return $this->_visit_fail('forbidden');

        if (empty($_FILES['document']['name'])) {
            return $this->_visit_fail('No document was received.');
        }

        // same folder and rules the web upload uses, so a document attached
        // from the app opens from the browser like any other
        $upload_path = FCPATH . 'uploads/service_visit_docs/';
        if (!is_dir($upload_path) && !mkdir($upload_path, 0777, true) && !is_dir($upload_path)) {
            return $this->_visit_fail('Unable to prepare the signed document upload folder.');
        }

        $this->load->library('upload');
        $this->upload->initialize(array(
            'upload_path'   => $upload_path,
            'allowed_types' => 'pdf|jpg|jpeg|png|doc|docx',
            'max_size'      => 20480,
            'encrypt_name'  => true,
        ));

        if (!$this->upload->do_upload('document')) {
            return $this->_visit_fail(trim(strip_tags($this->upload->display_errors('', ''))));
        }

        $file = $this->upload->data();
        $model->save_completion_document($visit_id, array(
            'file_name'     => $file['file_name'],
            'original_name' => $_FILES['document']['name'],
            'file_path'     => 'uploads/service_visit_docs/' . $file['file_name'],
            'document_type' => 'SIGNED_COMPLETION',
        ), $me['id']);

        $this->_visit_out(array(
            'status'    => true,
            'message'   => 'Document uploaded successfully.',
            'documents' => $this->_visit_decorate_documents($model->get_visit_documents($visit_id)),
        ));
    } catch (Throwable $e) {
        $this->_visit_fail('Upload failed: ' . $e->getMessage());
    }
}

/**
 * Close the visit. Both web preconditions are re-checked here: at least one
 * MOM update and at least one signed document. The app is not trusted to
 * have checked - it is only trusted to have made it easy.
 */
public function visit_complete_api()
{
    try {
        $in = $this->_visit_input();
        $me = $this->_visit_me($in);
        if (!$me) return $this->_visit_fail('auth');

        $visit_id = (int) ($in['visit_id'] ?? 0);
        if ($visit_id <= 0) return $this->_visit_fail('visit_id is required');

        $model = $this->_visit_model();
        $visit = $model->get_visit_by_id($visit_id);
        if (!$visit) return $this->_visit_fail('Visit not found');
        if (!$this->_visit_can_write($visit, $me)) return $this->_visit_fail('forbidden');

        if ($visit->status_slug === 'completed') {
            return $this->_visit_fail('This visit is already marked as completed.');
        }
        if ($model->get_visit_update_count($visit_id) <= 0) {
            return $this->_visit_fail('Please add at least one daily MOM update before completing this visit.');
        }
        if ($model->get_visit_document_count($visit_id) <= 0) {
            return $this->_visit_fail('Please upload at least one signed completion document before marking this visit as completed.');
        }

        $notes = trim((string) ($in['completion_notes'] ?? ''));
        if (!$model->mark_visit_completed($visit_id, $notes)) {
            return $this->_visit_fail('The visit could not be completed due to a database issue.');
        }

        $this->_visit_out(array(
            'status'  => true,
            'message' => 'Visit marked as completed successfully.',
            'visit'   => $model->get_visit_by_id($visit_id),
        ));
    } catch (Throwable $e) {
        $this->_visit_fail('Complete failed: ' . $e->getMessage());
    }
}


/* =====================================================================
 * DELEGATION MODULE (mobile)
 * ---------------------------------------------------------------------
 * Mirrors application/controllers/Delegation.php:
 *   new_delegation_task / save_delegation_task -> delegation_create_api
 *   delegation_dashboard  (tasks I delegated) -> box = "by_me"
 *   delegated_task        (tasks delegated to me) -> box = "to_me"
 * Every rule below is copied from the web controller so a task created or
 * answered from the app behaves identically on the website.
 * ===================================================================== */

private function _dlg_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json) && !empty($json)) return $json;
    }
    $post = $this->input->post();
    return is_array($post) ? $post : array();
}

private function _dlg_me($in)
{
    $id = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
    if ($id <= 0) return null;

    return array(
        'id'         => $id,
        'role'       => (int) (isset($in['role_id']) ? $in['role_id'] : 0),
        'department' => (int) (isset($in['department_id']) ? $in['department_id'] : 0),
    );
}

private function _dlg_out($payload)
{
    echo json_encode($payload);
}

private function _dlg_fail($message, $extra = array())
{
    $this->_dlg_out(array_merge(array('status' => false, 'message' => $message), $extra));
}

/**
 * Who may raise a delegation. Admins always may; everyone else must carry a
 * row in delegation_master, the setup maintained at
 * Delegation/delegation_master. Membership alone is the grant: every row in
 * that table carries status = 0, so the status column is not a live on/off
 * switch here and filtering on it would lock out every real delegator.
 */
private function _dlg_can_create($user_id, $role_id)
{
    if ((int) $role_id === 12) return true;

    $q = $this->db->select('id')
        ->from('delegation_master')
        ->where('assigned_to', (int) $user_id)
        ->limit(1)
        ->get();

    return $q->num_rows() > 0;
}

private function _dlg_blank_date($d)
{
    return ($d === null || $d === '' || $d === '0000-00-00' || $d === '0000-00-00 00:00:00');
}

/** third_date wins, then second_date, else the original delegated_date. */
private function _dlg_due_date($row)
{
    if (!$this->_dlg_blank_date($row->third_date))  return $row->third_date;
    if (!$this->_dlg_blank_date($row->second_date)) return $row->second_date;
    return $this->_dlg_blank_date($row->delegated_date) ? '' : $row->delegated_date;
}

/**
 * Newest reply on a task. delegation_task_response is the current table;
 * user_response_on_delegated_task is the legacy one and is only consulted
 * when the new table has nothing - identical to
 * Delegation::get_latest_delegation_response().
 */
private function _dlg_latest_response($task_id)
{
    $task_id = (int) $task_id;
    if ($task_id <= 0) return null;

    $new = $this->db->select('id, task_id, user_id, remarks, status, attachment, created_at')
        ->from('delegation_task_response')
        ->where('task_id', $task_id)
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get();

    if ($new->num_rows() > 0) {
        $row = $new->row();
        return array(
            'remarks'    => (string) $row->remarks,
            'status'     => (int) $row->status,
            'attachment' => $this->_dlg_file_url($row->attachment),
            'updated_on' => (string) $row->created_at,
            'user_id'    => (int) $row->user_id,
        );
    }

    // The legacy table's columns vary between installs (it has no user_id
    // here), so it is read whole and picked apart defensively.
    $old = $this->db->from('user_response_on_delegated_task')
        ->where('task_id', $task_id)
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get();

    if ($old->num_rows() > 0) {
        $row = $old->row_array();
        return array(
            'remarks'    => (string) $this->_dlg_pick($row, array('user_response', 'remarks')),
            'status'     => (int) $this->_dlg_pick($row, array('task_status', 'status'), 0),
            'attachment' => $this->_dlg_file_url($this->_dlg_pick($row, array('proof', 'attachment'))),
            'updated_on' => (string) $this->_dlg_pick($row, array('updated_on', 'created_at', 'added_on')),
            'user_id'    => (int) $this->_dlg_pick($row, array('user_id', 'added_by', 'delegate_to'), 0),
        );
    }

    return null;
}

/** First key present in $row, else $default. */
private function _dlg_pick($row, $keys, $default = '')
{
    foreach ($keys as $k) {
        if (array_key_exists($k, $row) && $row[$k] !== null) return $row[$k];
    }
    return $default;
}

/**
 * Which of these task ids have at least one reply, in two queries rather
 * than the two-per-task the web summaries do. Returns an id => true map.
 */
private function _dlg_responded_ids($task_ids)
{
    $found = array();
    $task_ids = array_values(array_unique(array_filter(array_map('intval', $task_ids))));
    if (empty($task_ids)) return $found;

    $new = $this->db->select('task_id')->distinct()
        ->from('delegation_task_response')
        ->where_in('task_id', $task_ids)
        ->get()->result();
    foreach ($new as $r) $found[(int) $r->task_id] = true;

    $remaining = array();
    foreach ($task_ids as $id) {
        if (!isset($found[$id])) $remaining[] = $id;
    }
    if (empty($remaining)) return $found;

    $old = $this->db->select('task_id')->distinct()
        ->from('user_response_on_delegated_task')
        ->where_in('task_id', $remaining)
        ->get()->result();
    foreach ($old as $r) $found[(int) $r->task_id] = true;

    return $found;
}

/**
 * Newest reply for many tasks at once, so a list costs two queries instead
 * of two per row. Returns an id => response map shaped like
 * _dlg_latest_response().
 */
private function _dlg_latest_responses($task_ids)
{
    $out = array();
    $task_ids = array_values(array_unique(array_filter(array_map('intval', $task_ids))));
    if (empty($task_ids)) return $out;

    // ascending, so the last row written for a task wins
    $new = $this->db->select('id, task_id, user_id, remarks, status, attachment, created_at')
        ->from('delegation_task_response')
        ->where_in('task_id', $task_ids)
        ->order_by('id', 'ASC')
        ->get()->result();

    foreach ($new as $r) {
        $out[(int) $r->task_id] = array(
            'remarks'    => (string) $r->remarks,
            'status'     => (int) $r->status,
            'attachment' => $this->_dlg_file_url($r->attachment),
            'updated_on' => (string) $r->created_at,
            'user_id'    => (int) $r->user_id,
        );
    }

    $remaining = array();
    foreach ($task_ids as $id) {
        if (!isset($out[$id])) $remaining[] = $id;
    }
    if (empty($remaining)) return $out;

    $old = $this->db->from('user_response_on_delegated_task')
        ->where_in('task_id', $remaining)
        ->order_by('id', 'ASC')
        ->get()->result_array();

    foreach ($old as $r) {
        $tid = (int) $this->_dlg_pick($r, array('task_id'), 0);
        if ($tid <= 0) continue;
        $out[$tid] = array(
            'remarks'    => (string) $this->_dlg_pick($r, array('user_response', 'remarks')),
            'status'     => (int) $this->_dlg_pick($r, array('task_status', 'status'), 0),
            'attachment' => $this->_dlg_file_url($this->_dlg_pick($r, array('proof', 'attachment'))),
            'updated_on' => (string) $this->_dlg_pick($r, array('updated_on', 'created_at', 'added_on')),
            'user_id'    => (int) $this->_dlg_pick($r, array('user_id', 'added_by', 'delegate_to'), 0),
        );
    }

    return $out;
}

/** Task screenshots and reply attachments share image_bank/delegation/. */
private function _dlg_file_url($file)
{
    $file = trim((string) $file);
    if ($file === '') return '';
    if (stripos($file, 'http://') === 0 || stripos($file, 'https://') === 0) return $file;
    return page_url22 . 'image_bank/delegation/' . rawurlencode($file);
}

private function _dlg_name($first, $last, $title = '')
{
    $name = trim(trim((string) $title) . ' ' . trim((string) $first) . ' ' . trim((string) $last));
    return $name === '' ? '' : ucwords(strtolower($name));
}

/** The select every list/detail endpoint shares. */
private function _dlg_select()
{
    $this->db->select('
        a.id, a.case_no, a.task, a.email_url, a.image, a.urgency, a.task_status,
        a.yourname, a.delegate_to, a.delegated_date, a.targetdate,
        a.second_date, a.third_date, a.added_on, a.task_completed_time,
        a.done_ontime_or_late,
        c.first_name AS to_first, c.last_name AS to_last, c.title AS to_title,
        d.first_name AS by_first, d.last_name AS by_last, d.title AS by_title
    ');
    $this->db->from('delegation_task a');
    $this->db->join('system_users_view c', 'a.delegate_to = c.user_id', 'left');
    $this->db->join('system_users_view d', 'a.yourname = d.user_id', 'left');
}

private function _dlg_decorate($row, $today, $with_response = true)
{
    $due = $this->_dlg_due_date($row);
    $open = ((int) $row->task_status === 0);

    $due_today = (
        $row->delegated_date === $today ||
        $row->second_date === $today ||
        $row->third_date === $today
    );

    $timing = $this->_time_left($due, $open);

    $out = array_merge($timing, array(
        'id'             => (int) $row->id,
        'case_no'        => (string) $row->case_no,
        'task'           => ucfirst(strtolower((string) $row->task)),
        'task_raw'       => (string) $row->task,
        'email_url'      => (string) $row->email_url,
        'image_url'      => $this->_dlg_file_url($row->image),
        'urgency'        => (int) $row->urgency,
        'is_high'        => ((int) $row->urgency === 1),
        'task_status'    => (int) $row->task_status,
        'status_label'   => $open ? 'Open' : 'Done',
        'delegated_by'   => (int) $row->yourname,
        'delegated_by_name' => $this->_dlg_name($row->by_first, $row->by_last, $row->by_title),
        'delegate_to'    => (int) $row->delegate_to,
        'delegate_to_name' => $this->_dlg_name($row->to_first, $row->to_last, $row->to_title),
        'delegated_date' => $this->_dlg_blank_date($row->delegated_date) ? '' : $row->delegated_date,
        'second_date'    => $this->_dlg_blank_date($row->second_date) ? '' : $row->second_date,
        'third_date'     => $this->_dlg_blank_date($row->third_date) ? '' : $row->third_date,
        'due_date'       => $due,
        'is_due_today'   => ($open && $due_today),
        'is_overdue'     => ($open && $due !== '' && $due < $today),
        'added_on'       => (string) $row->added_on,
        'completed_on'   => $this->_dlg_blank_date($row->task_completed_time) ? '' : (string) $row->task_completed_time,
        // only meaningful once the task is closed
        'on_time'        => ($open || $row->done_ontime_or_late === null || $row->done_ontime_or_late === '')
                                ? null : ((int) $row->done_ontime_or_late === 1),
    ));

    if ($with_response) {
        $out['latest_response'] = $this->_dlg_latest_response($row->id);
        $out['has_response'] = !empty($out['latest_response']);
    }

    return $out;
}

/* ------------------------------------------------------------------ *
 * 1. SUMMARY - the card shown on whichever dashboard the user lands on
 * ------------------------------------------------------------------ */
public function delegation_summary_api()
{
    try {
        $in = $this->_dlg_input();
        $me = $this->_dlg_me($in);
        if (!$me) return $this->_dlg_fail('auth');

        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');

        /* ---- tasks delegated TO me (Delegation::task_delegated_to_you_summary) ---- */
        $to_me = array(
            'total_open' => 0, 'due_today' => 0, 'overdue' => 0, 'upcoming' => 0,
            'high_priority' => 0, 'responded' => 0, 'pending_response' => 0,
        );

        $to_me_rows = $this->db->select('id, urgency, second_date, third_date, task_status, delegated_date, added_on')
            ->from('delegation_task')
            ->where('delegate_to', $me['id'])
            ->where('task_status', 0)
            ->get()->result();

        $to_me_ids = array();
        foreach ($to_me_rows as $r) $to_me_ids[] = $r->id;
        $to_me_responded = $this->_dlg_responded_ids($to_me_ids);

        foreach ($to_me_rows as $row) {
            $to_me['total_open']++;
            if ((int) $row->urgency === 1) $to_me['high_priority']++;

            if ($row->delegated_date === $today || $row->second_date === $today || $row->third_date === $today) {
                $to_me['due_today']++;
            }

            $due = $this->_dlg_due_date($row);
            if ($due !== '' && $due < $today) $to_me['overdue']++;
            if ($due !== '' && $due > $today) $to_me['upcoming']++;

            if (isset($to_me_responded[(int) $row->id])) {
                $to_me['responded']++;
            } else {
                $to_me['pending_response']++;
            }
        }

        /* ---- tasks I delegated (Delegation::delegated_dashboard_summary) ---- */
        $by_me = array(
            'total_open' => 0, 'due_today' => 0, 'overdue' => 0, 'high_priority' => 0,
            'second_followup_pending' => 0, 'third_followup_pending' => 0, 'no_response' => 0,
        );
        $user_wise = array();

        $this->db->select('a.id, a.urgency, a.task_status, a.delegated_date, a.second_date,
                           a.third_date, a.added_on, a.delegate_to, a.yourname,
                           c.first_name, c.last_name');
        $this->db->from('delegation_task a');
        $this->db->join('system_users_view c', 'a.delegate_to = c.user_id', 'left');
        $this->db->where('a.task_status', 0);
        $this->db->where('a.yourname', $me['id']);
        $by_me_rows = $this->db->get()->result();

        $by_me_ids = array();
        foreach ($by_me_rows as $r) $by_me_ids[] = $r->id;
        $by_me_responded = $this->_dlg_responded_ids($by_me_ids);

        foreach ($by_me_rows as $row) {
            $by_me['total_open']++;
            if ((int) $row->urgency === 1) $by_me['high_priority']++;

            if ($row->delegated_date === $today || $row->second_date === $today || $row->third_date === $today) {
                $by_me['due_today']++;
            }

            $due = $this->_dlg_due_date($row);
            if ($due !== '' && $due < $today) $by_me['overdue']++;

            if ($this->_dlg_blank_date($row->second_date)) {
                $by_me['second_followup_pending']++;
            } elseif ($this->_dlg_blank_date($row->third_date)) {
                $by_me['third_followup_pending']++;
            }

            if (!isset($by_me_responded[(int) $row->id])) $by_me['no_response']++;

            $name = $this->_dlg_name($row->first_name, $row->last_name);
            if ($name === '') $name = 'Not Assigned';
            if (!isset($user_wise[$name])) $user_wise[$name] = 0;
            $user_wise[$name]++;
        }

        arsort($user_wise);
        $top = array();
        foreach ($user_wise as $name => $count) {
            $top[] = array('name' => strtoupper($name), 'count' => $count);
            if (count($top) >= 5) break;
        }

        $is_ea = $this->_is_ea($me['id']);

        /* An EA's strip counts the company's open delegations, not their
           own - their own are almost always zero, and a zero strip would
           hide exactly the thing they are meant to be watching. */
        $all = array('total_open' => 0, 'overdue' => 0, 'due_today' => 0, 'high_priority' => 0);

        if ($is_ea) {
            $all_rows = $this->db->select('id, urgency, second_date, third_date, task_status, delegated_date, added_on')
                ->from('delegation_task')
                ->where('task_status', 0)
                ->get()->result();

            foreach ($all_rows as $row) {
                $all['total_open']++;
                if ((int) $row->urgency === 1) $all['high_priority']++;

                if ($row->delegated_date === $today || $row->second_date === $today || $row->third_date === $today) {
                    $all['due_today']++;
                }

                $due = $this->_dlg_due_date($row);
                if ($due !== '' && $due < $today) $all['overdue']++;
            }
        }

        $this->_dlg_out(array(
            'status'        => true,
            'can_create'    => $this->_dlg_can_create($me['id'], $me['role']),
            'to_me'         => $to_me,
            'by_me'         => $by_me,
            'is_ea'         => $is_ea,
            'all'           => $all,
            'top_assignees' => $top,
        ));
    } catch (Throwable $e) {
        $this->_dlg_fail('Delegation summary failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 2. LIST - box = "to_me" (delegated to me) or "by_me" (I delegated)
 * ------------------------------------------------------------------ */
public function delegation_list_api()
{
    try {
        $in = $this->_dlg_input();
        $me = $this->_dlg_me($in);
        if (!$me) return $this->_dlg_fail('auth');

        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');

        $box    = isset($in['box']) ? strtolower(trim($in['box'])) : 'to_me';
        $filter = isset($in['filter']) ? strtolower(trim($in['filter'])) : 'open';
        $search = isset($in['search']) ? trim($in['search']) : '';
        $limit  = (int) (isset($in['limit']) ? $in['limit'] : 300);
        if ($limit <= 0 || $limit > 1000) $limit = 300;

        $is_ea = $this->_is_ea($me['id']);

        if ($box === 'all') {
            /* the box exists, but only for the people named in _ea_ids */
            if (!$is_ea) return $this->_dlg_fail('forbidden');
        } elseif ($box !== 'by_me') {
            $box = 'to_me';
        }

        $this->_dlg_select();
        if ($box === 'all') {
            /* no user restriction - everyone's delegations */
        } elseif ($box === 'by_me') {
            $this->db->where('a.yourname', $me['id']);
        } else {
            $this->db->where('a.delegate_to', $me['id']);
        }

        /* a.delegate_to is who it landed on, a.yourname is who raised it */
        $who = $this->_side_of($in);
        if ($who['user_id'] > 0) {
            if ($who['side'] === 'assigned_to') {
                $this->db->where('a.delegate_to', $who['user_id']);
            } elseif ($who['side'] === 'assigned_by') {
                $this->db->where('a.yourname', $who['user_id']);
            } else {
                $this->db->group_start()
                    ->where('a.delegate_to', $who['user_id'])
                    ->or_where('a.yourname', $who['user_id'])
                    ->group_end();
            }
        }

        if ($filter === 'done') {
            $this->db->where('a.task_status !=', 0);
        } elseif ($filter !== 'all') {
            $this->db->where('a.task_status', 0);
        }

        /* The date-shaped filters have to run in SQL, not over the fetched
           page - filtering after LIMIT would drop most of the matches. */
        $due_expr = "COALESCE(
            NULLIF(a.third_date,  '0000-00-00'),
            NULLIF(a.second_date, '0000-00-00'),
            NULLIF(a.delegated_date, '0000-00-00')
        )";

        if ($filter === 'overdue') {
            $this->db->where("($due_expr IS NOT NULL AND $due_expr < " . $this->db->escape($today) . ')', null, false);
        } elseif ($filter === 'due_today') {
            $t = $this->db->escape($today);
            $this->db->where("(a.delegated_date = $t OR a.second_date = $t OR a.third_date = $t)", null, false);
        } elseif ($filter === 'high') {
            $this->db->where('a.urgency', 1);
        } elseif ($filter === 'no_response') {
            $this->db->where('a.id NOT IN (SELECT task_id FROM delegation_task_response)', null, false);
            $this->db->where('a.id NOT IN (SELECT task_id FROM user_response_on_delegated_task)', null, false);
        }

        if ($search !== '') {
            $this->db->group_start()
                ->like('a.task', $search)
                ->or_like('a.case_no', $search)
                ->or_like('c.first_name', $search)
                ->or_like('c.last_name', $search)
                ->or_like('d.first_name', $search)
                ->or_like('d.last_name', $search)
                ->group_end();
        }

        $this->db->order_by('a.id', 'DESC');
        $this->db->limit($limit);
        $rows = $this->db->get()->result();

        $ids = array();
        foreach ($rows as $r) $ids[] = $r->id;
        $latest = $this->_dlg_latest_responses($ids);

        $tasks = array();
        foreach ($rows as $row) {
            $task = $this->_dlg_decorate($row, $today, false);
            $task['latest_response'] = isset($latest[(int) $row->id]) ? $latest[(int) $row->id] : null;
            $task['has_response'] = !empty($task['latest_response']);

            $tasks[] = $task;
        }

        $this->_dlg_out(array(
            'status'     => true,
            'box'        => $box,
            'filter'     => $filter,
            'count'      => count($tasks),
            'can_create' => $this->_dlg_can_create($me['id'], $me['role']),
            'is_ea'      => $is_ea,
            'tasks'      => $tasks,
        ));
    } catch (Throwable $e) {
        $this->_dlg_fail('Delegation list failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 3. DETAIL - one task plus its full reply thread
 * ------------------------------------------------------------------ */
public function delegation_detail_api()
{
    try {
        $in = $this->_dlg_input();
        $me = $this->_dlg_me($in);
        if (!$me) return $this->_dlg_fail('auth');

        $task_id = (int) (isset($in['task_id']) ? $in['task_id'] : 0);
        if ($task_id <= 0) return $this->_dlg_fail('task_id is required');

        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');

        $this->_dlg_select();
        $this->db->where('a.id', $task_id);
        $row = $this->db->get()->row();
        if (!$row) return $this->_dlg_fail('Task not found');

        // same rule save_response_ajax enforces: delegator or assignee only
        if ((int) $row->yourname !== $me['id'] && (int) $row->delegate_to !== $me['id']) {
            return $this->_dlg_fail('forbidden');
        }

        $task = $this->_dlg_decorate($row, $today);
        $task['is_delegator'] = ((int) $row->yourname === $me['id']);
        $task['is_assignee']  = ((int) $row->delegate_to === $me['id']);

        $responses = array();

        $new = $this->db->select('r.id, r.task_id, r.user_id, r.remarks, r.status, r.attachment,
                                  r.created_at, u.first_name, u.last_name, u.title')
            ->from('delegation_task_response r')
            ->join('system_users_view u', 'r.user_id = u.user_id', 'left')
            ->where('r.task_id', $task_id)
            ->order_by('r.id', 'ASC')
            ->get();

        foreach ($new->result() as $r) {
            $responses[] = array(
                'id'         => (int) $r->id,
                'user_id'    => (int) $r->user_id,
                'user_name'  => $this->_dlg_name($r->first_name, $r->last_name, $r->title),
                'remarks'    => (string) $r->remarks,
                'status'     => (int) $r->status,
                'status_label' => ((int) $r->status === 1) ? 'Done' : 'Pending',
                'attachment' => $this->_dlg_file_url($r->attachment),
                'created_at' => (string) $r->created_at,
                'legacy'     => false,
            );
        }

        if (empty($responses)) {
            // legacy rows carry no user_id on this install, so no join here
            $old = $this->db->from('user_response_on_delegated_task')
                ->where('task_id', $task_id)
                ->order_by('id', 'ASC')
                ->get();

            foreach ($old->result_array() as $r) {
                $status = (int) $this->_dlg_pick($r, array('task_status', 'status'), 0);
                $uid    = (int) $this->_dlg_pick($r, array('user_id', 'added_by', 'delegate_to'), 0);

                $name = '';
                if ($uid > 0) {
                    $u = $this->db->select('first_name, last_name, title')
                        ->from('system_users_view')->where('user_id', $uid)->get()->row();
                    if ($u) $name = $this->_dlg_name($u->first_name, $u->last_name, $u->title);
                }

                $responses[] = array(
                    'id'         => (int) $this->_dlg_pick($r, array('id'), 0),
                    'user_id'    => $uid,
                    'user_name'  => $name,
                    'remarks'    => (string) $this->_dlg_pick($r, array('user_response', 'remarks')),
                    'status'     => $status,
                    'status_label' => ($status === 1) ? 'Done' : 'Pending',
                    'attachment' => $this->_dlg_file_url($this->_dlg_pick($r, array('proof', 'attachment'))),
                    'created_at' => (string) $this->_dlg_pick($r, array('updated_on', 'created_at', 'added_on')),
                    'legacy'     => true,
                );
            }
        }

        $this->_dlg_out(array(
            'status'    => true,
            'task'      => $task,
            'responses' => $responses,
        ));
    } catch (Throwable $e) {
        $this->_dlg_fail('Delegation detail failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 4. USER DIRECTORY - the "delegate to" picker
 * ------------------------------------------------------------------ */
public function delegation_users_api()
{
    try {
        $in = $this->_dlg_input();
        $me = $this->_dlg_me($in);
        if (!$me) return $this->_dlg_fail('auth');

        if (!$this->_dlg_can_create($me['id'], $me['role'])) {
            return $this->_dlg_fail('forbidden');
        }

        $department = (int) (isset($in['department_id']) && $in['department_id'] !== ''
            ? $in['department_id'] : 0);

        // same visibility rule as Delegation::user_list_new(), plus the
        // department name so the app can label each pick (the web form
        // shows bare names; the phone list is long enough to need it).
        $this->db->select('u.user_id, u.first_name, u.last_name, u.department_id, u.contact_number, d.department');
        $this->db->from('system_users u');
        $this->db->join('departments d', 'd.department_id = u.department_id', 'left');
        $this->db->where('u.hide_profile', '0');
        $this->db->where('u.user_status', '1');
        if (isset($in['scope']) && $in['scope'] === 'department' && $department > 0) {
            $this->db->where('u.department_id', $department);
        }
        $this->db->order_by('u.first_name', 'ASC');
        $rows = $this->db->get()->result();

        $users = array();
        foreach ($rows as $r) {
            $users[] = array(
                'user_id'       => (int) $r->user_id,
                'name'          => $this->_dlg_name($r->first_name, $r->last_name),
                'department_id' => (int) $r->department_id,
                'department'    => (string) (isset($r->department) ? $r->department : ''),
                'contact_number' => (string) $r->contact_number,
            );
        }

        $this->_dlg_out(array('status' => true, 'users' => $users));
    } catch (Throwable $e) {
        $this->_dlg_fail('Delegation users failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 5. CREATE - the app side of Delegation::save_delegation_task()
 *    Accepts JSON, or multipart when a screenshot rides along (field
 *    "image"). delegate_to may be a list or a single id; every assignee
 *    gets their own row under one shared case_no, as on the web.
 * ------------------------------------------------------------------ */
public function delegation_create_api()
{
    try {
        $in = $this->_dlg_input();
        $me = $this->_dlg_me($in);
        if (!$me) return $this->_dlg_fail('auth');

        if (!$this->_dlg_can_create($me['id'], $me['role'])) {
            return $this->_dlg_fail('You do not have access to delegate a task.');
        }

        $task = trim((string) (isset($in['task']) ? $in['task'] : ''));
        if ($task === '') return $this->_dlg_fail('Task is required');

        $due_raw = trim((string) (isset($in['work_completion_date']) ? $in['work_completion_date'] : ''));
        if ($due_raw === '') return $this->_dlg_fail('Work completion date is required');

        $due_ts = strtotime($due_raw);
        if ($due_ts === false) return $this->_dlg_fail('Work completion date is not a valid date');
        $due = date('Y-m-d', $due_ts);

        $delegate_users = isset($in['delegate_to']) ? $in['delegate_to'] : array();
        if (!is_array($delegate_users)) {
            $delegate_users = ($delegate_users === '' || $delegate_users === null)
                ? array() : explode(',', (string) $delegate_users);
        }
        $delegate_users = array_values(array_unique(array_filter(array_map('intval', $delegate_users))));
        if (empty($delegate_users)) return $this->_dlg_fail('Select at least one person to delegate to');

        // the delegator defaults to the caller; the web lets it be chosen
        $yourname = (int) (isset($in['your_name']) && $in['your_name'] !== '' ? $in['your_name'] : $me['id']);
        $urgency  = (int) (isset($in['urgency']) ? $in['urgency'] : 0);
        $email_url = trim((string) (isset($in['email_url']) ? $in['email_url'] : ''));

        date_default_timezone_set('Asia/Kolkata');
        $added_time = date('Y-m-d H:i:s');

        /* screenshot - same folder the web form writes to */
        $screenshot = '';
        if (!empty($_FILES['image']['name'])) {
            $upload_path = FCPATH . 'image_bank/delegation/';
            if (!is_dir($upload_path) && !mkdir($upload_path, 0777, true) && !is_dir($upload_path)) {
                return $this->_dlg_fail('Unable to prepare the delegation upload folder.');
            }

            $this->load->library('upload');
            $this->upload->initialize(array(
                'upload_path'   => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|gif|pdf',
                'max_size'      => 20480,
                'encrypt_name'  => true,
            ));

            if (!$this->upload->do_upload('image')) {
                return $this->_dlg_fail(trim(strip_tags($this->upload->display_errors('', ''))));
            }

            $file = $this->upload->data();
            $screenshot = $file['file_name'];
        }

        /* case_no: same "SP-n" shape as the web, but numbered off the highest
           existing case instead of a row count, which repeats after a delete */
        $last = $this->db->select('case_no')
            ->from('delegation_task')
            ->like('case_no', 'SP-', 'after')
            ->order_by('id', 'DESC')
            ->limit(50)
            ->get()->result();

        $max = 0;
        foreach ($last as $r) {
            if (preg_match('/^SP-(\d+)$/i', trim((string) $r->case_no), $m)) {
                $max = max($max, (int) $m[1]);
            }
        }
        $case_no = 'SP-' . ($max + 1);

        $delegator = $this->db->select('first_name, last_name')
            ->from('system_users_view')->where('user_id', $yourname)->get()->row();
        $delegator_name = $delegator ? $this->_dlg_name($delegator->first_name, $delegator->last_name) : 'Task Management';

        $created = array();

        foreach ($delegate_users as $delegate_to) {
            $data = array(
                'yourname'       => $yourname,
                'delegate_to'    => $delegate_to,
                'task'           => strtoupper($task),
                'email_url'      => $email_url,
                'image'          => $screenshot,
                'urgency'        => $urgency,
                'delegated_date' => $due,
                'targetdate'     => $due,
                'added_by'       => $me['id'],
                'case_no'        => $case_no,
                'added_on'       => $added_time,
            );

            if (!$this->db->insert('delegation_task', $data)) continue;

            $new_id = (int) $this->db->insert_id();
            $created[] = $new_id;

            $recipient = $this->db->select('user_id, email, contact_number, first_name, last_name')
                ->from('system_users_view')->where('user_id', $delegate_to)->get()->row();
            if (!$recipient) continue;

            $this->_dlg_notify_assigned($recipient, $task, $due, $delegator_name, $me['id'], $new_id);
        }

        if (empty($created)) return $this->_dlg_fail('Could not save the delegation.');

        $this->_dlg_out(array(
            'status'   => true,
            'message'  => 'Task delegated successfully.',
            'case_no'  => $case_no,
            'task_ids' => $created,
        ));
    } catch (Throwable $e) {
        $this->_dlg_fail('Delegation create failed: ' . $e->getMessage());
    }
}

/**
 * WhatsApp + email on assignment, worded exactly as the web form's.
 * Failures here never fail the save - the task is already committed.
 */
private function _dlg_notify_assigned($recipient, $task, $due, $delegator_name, $sender_user_id, $task_id)
{
    try {
        $username = ucwords(strtolower(trim($recipient->first_name)));
        $targetdate = date('d-m-Y', strtotime($due));

        $message = "Dear " . $username . ",<br>
Important Task delegated to you \xE2\x8F\xB1\xEF\xB8\x8F

TASK: *" . $task . "*
Complete it by: *" . $targetdate . "*.
Assigned By : *" . $delegator_name . "*
*Shubham Flexible Packaging* \xF0\x9F\x9A\x80";

        $this->_dlg_whatsapp($recipient->contact_number, $message);

        $html = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">
            <tr><td style="background-color: #4872b8; padding: 10px; text-align: center;">
                <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" />
            </td></tr>
            <tr><td style="padding: 15px; background-color: #ffffff;">
                <h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">WORK DELEGATION NOTIFICATION</h2>
                <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">
                <p style="color: #555555; font-size: 14px; margin: 0;"><strong>Dear ' . $recipient->first_name . ' ' . $recipient->last_name . ',</strong>
                <br> A new task has been delegated to you. Below are the task details:</p>
                <p style="color: #555555; font-size: 14px; margin: 10px 0;"><strong>Task Details:</strong> ' . ucwords(strtolower($task)) . '</p>
                <p style="color: #555555; font-size: 14px; margin: 10px 0;"><strong>Task completion Date:</strong> ' . date('d-m-Y', strtotime($due)) . '</p>
                <p style="color: #555555; font-size: 14px; margin: 10px 0;"><strong>Delegated By:</strong> ' . $delegator_name . '</p>
            </td></tr></table>';

        if (!empty($recipient->email)) {
            $this->email->clear();
            $this->email->set_mailtype('html');
            $this->email->to($recipient->email);
            $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
            $this->email->subject('Work Delegation Notification');
            $this->email->message($html);
            $this->email->send();
        }

        $this->_dlg_push($recipient->user_id, 'New task delegated to you', $task, $task_id);
    } catch (Throwable $e) {
        log_message('error', 'Delegation notify failed for task ' . $task_id . ': ' . $e->getMessage());
    }
}

private function _dlg_whatsapp($contact_number, $message)
{
    $digits = preg_replace('/\D+/', '', (string) $contact_number);
    if (strlen($digits) > 10 && substr($digits, 0, 2) === '91') $digits = substr($digits, 2);
    $digits = ltrim($digits, '0');
    if (strlen($digits) !== 10) return false;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'receiverMobileNo' => '91' . $digits,
        'username' => whatsappuser1,
        'password' => whatsapppass1,
        'message'  => strip_tags($message),
    ));
    curl_exec($ch);
    curl_close($ch);
    return true;
}

/** Phone push, using the same user_devices + fcm helper the chat uses. */
private function _dlg_push($user_id, $title, $body, $task_id)
{
    try {
        if (!function_exists('sendFCMData')) return;

        $devices = $this->db->select('fcm_token')
            ->from('user_devices')
            ->where('user_id', (int) $user_id)
            ->where('fcm_token !=', '')
            ->get()->result();

        $preview = mb_substr(ucfirst(strtolower($body)), 0, 140);

        foreach ($devices as $d) {
            if (empty($d->fcm_token)) continue;
            sendFCMData($d->fcm_token, $title, $preview, array(
                'type'      => 'delegation_task',
                'task_id'   => (string) $task_id,
                'screen'    => 'delegation_detail',
                'timestamp' => date('Y-m-d H:i:s'),
            ));
        }
    } catch (Throwable $e) {
        log_message('error', 'Delegation push failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 6. RESPOND - the app side of Delegation::save_response_ajax()
 *    Optional attachment rides in the "attachment" field.
 * ------------------------------------------------------------------ */
public function delegation_respond_api()
{
    try {
        $in = $this->_dlg_input();
        $me = $this->_dlg_me($in);
        if (!$me) return $this->_dlg_fail('auth');

        $task_id = (int) (isset($in['task_id']) ? $in['task_id'] : 0);
        if ($task_id <= 0) return $this->_dlg_fail('task_id is required');

        $remarks = trim((string) (isset($in['remarks']) ? $in['remarks'] : ''));
        if ($remarks === '') return $this->_dlg_fail('Remarks are required');

        $task_row = $this->db->select('yourname, task, delegate_to')
            ->from('delegation_task')->where('id', $task_id)->get()->row();
        if (!$task_row) return $this->_dlg_fail('Task not found');

        $is_delegator = ((int) $task_row->yourname === $me['id']);
        $is_assignee  = ((int) $task_row->delegate_to === $me['id']);
        if (!$is_delegator && !$is_assignee) {
            return $this->_dlg_fail('You are not allowed to comment on this task.');
        }

        // only the assignee's reply moves the work status; a delegator reply
        // carries the latest status forward, exactly as the web does
        if ($is_delegator) {
            $latest = $this->db->select('status')->from('delegation_task_response')
                ->where('task_id', $task_id)->order_by('id', 'DESC')->limit(1)->get()->row();
            $status = $latest ? (int) $latest->status : 2;
        } else {
            $status = ((int) (isset($in['status']) ? $in['status'] : 2) === 1) ? 1 : 2;
        }

        $screenshot = '';
        if (!empty($_FILES['attachment']['name'])) {
            $upload_path = UPLOADPATH . 'delegation/';
            if (!is_dir($upload_path) && !mkdir($upload_path, 0777, true) && !is_dir($upload_path)) {
                return $this->_dlg_fail('Unable to prepare the delegation upload folder.');
            }

            $this->load->library('upload');
            $this->upload->initialize(array(
                'upload_path'   => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|gif|pdf|doc|docx',
                'max_size'      => 20480,
                'encrypt_name'  => true,
            ));

            if (!$this->upload->do_upload('attachment')) {
                return $this->_dlg_fail(trim(strip_tags($this->upload->display_errors('', ''))));
            }

            $file = $this->upload->data();
            $screenshot = $file['file_name'];
        }

        date_default_timezone_set('Asia/Kolkata');

        $ok = $this->db->insert('delegation_task_response', array(
            'task_id'    => $task_id,
            'user_id'    => $me['id'],
            'remarks'    => strtoupper($remarks),
            'status'     => $status,
            'attachment' => $screenshot,
            'created_at' => date('Y-m-d H:i:s'),
        ));

        if (!$ok) return $this->_dlg_fail('Could not save the response.');

        /* tell the other side */
        $other_id = $is_delegator ? (int) $task_row->delegate_to : (int) $task_row->yourname;
        $sender = $this->db->select('first_name, last_name')
            ->from('system_users_view')->where('user_id', $me['id'])->get()->row();
        $sender_name = $sender ? $this->_dlg_name($sender->first_name, $sender->last_name) : 'A colleague';

        $other = $this->db->select('user_id, first_name, last_name, email, contact_number')
            ->from('system_users_view')->where('user_id', $other_id)->get()->row();

        if ($other) {
            $state = ($status === 1) ? 'Done' : 'Pending';
            $this->_dlg_whatsapp($other->contact_number,
                "Dear " . ucwords(strtolower(trim($other->first_name))) . ",\n\n"
                . "A response was added on a delegated task.\n\n"
                . "TASK: *" . $task_row->task . "*\n"
                . "Status: *" . $state . "*\n"
                . "By: *" . $sender_name . "*\n\n"
                . "*Shubham Flexible Packaging*");

            $this->_dlg_push($other->user_id, $sender_name . ' responded', $task_row->task, $task_id);
        }

        $this->_dlg_out(array(
            'status'  => true,
            'message' => 'Response saved successfully.',
        ));
    } catch (Throwable $e) {
        $this->_dlg_fail('Delegation response failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 7. COMPLETE - the app side of Delegation::update_task_status()
 *    on_time is derived from the same date ladder the web uses.
 * ------------------------------------------------------------------ */
public function delegation_complete_api()
{
    try {
        $in = $this->_dlg_input();
        $me = $this->_dlg_me($in);
        if (!$me) return $this->_dlg_fail('auth');

        $task_id = (int) (isset($in['task_id']) ? $in['task_id'] : 0);
        if ($task_id <= 0) return $this->_dlg_fail('task_id is required');

        $row = $this->db->select('id, yourname, delegate_to, task, task_status,
                                  delegated_date, second_date, third_date')
            ->from('delegation_task')->where('id', $task_id)->get()->row();
        if (!$row) return $this->_dlg_fail('Task not found');

        $is_delegator = ((int) $row->yourname === $me['id']);
        $is_assignee  = ((int) $row->delegate_to === $me['id']);
        if (!$is_delegator && !$is_assignee) return $this->_dlg_fail('forbidden');

        if ((int) $row->task_status !== 0) {
            return $this->_dlg_fail('This task is already closed.');
        }

        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');

        $on_time = '0';
        if (!$this->_dlg_blank_date($row->delegated_date) && $today <= $row->delegated_date) {
            $on_time = '1';
        } elseif (!$this->_dlg_blank_date($row->second_date) && $today <= $row->second_date) {
            $on_time = '1';
        } elseif (!$this->_dlg_blank_date($row->third_date) && $today <= $row->third_date) {
            $on_time = '1';
        }

        $ok = $this->db->where('id', $task_id)->update('delegation_task', array(
            'task_status'         => 1,
            'task_completed_time' => date('Y-m-d H:i:s'),
            'done_ontime_or_late' => $on_time,
        ));

        if (!$ok) return $this->_dlg_fail('Could not close the task.');

        $other_id = $is_delegator ? (int) $row->delegate_to : (int) $row->yourname;
        $other = $this->db->select('user_id, first_name, last_name, contact_number')
            ->from('system_users_view')->where('user_id', $other_id)->get()->row();

        if ($other) {
            $this->_dlg_whatsapp($other->contact_number,
                "Dear " . ucwords(strtolower(trim($other->first_name))) . ",\n\n"
                . "The following task has been marked as COMPLETED:\n\n"
                . "TASK: *" . $row->task . "*\n\n"
                . "*Shubham Flexible Packaging*");

            $this->_dlg_push($other->user_id, 'Task completed', $row->task, $task_id);
        }

        $this->_dlg_out(array(
            'status'  => true,
            'message' => 'Task marked as completed.',
            'on_time' => ($on_time === '1'),
        ));
    } catch (Throwable $e) {
        $this->_dlg_fail('Delegation complete failed: ' . $e->getMessage());
    }
}

/* =====================================================================
 * TASK MANAGEMENT (mobile)
 * ---------------------------------------------------------------------
 * The app side of application/controllers/Task_management.php. Every
 * query and every computed field comes from Task_management_model, which
 * is loaded, not modified - so a task raised or advanced from the phone
 * behaves exactly as it does on the website.
 *
 * Lifecycle: AWAITING_DUE_DATE -> (assignee confirms) OPEN ->
 * IN_PROGRESS -> COMPLETED, and the creator may REOPEN a completed task.
 * Creation is open to everyone, as on the web.
 * ===================================================================== */

/** Mirrors Task_management::$admin_user_ids. */
private function _tm_admins()
{
    return array(61, 139, 161, 162, 167);
}

/** Mirrors Task_management::$task_business_location_id. */
private function _tm_location()
{
    return 2;
}

private function _tm_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json) && !empty($json)) return $json;
    }
    $post = $this->input->post();
    return is_array($post) ? $post : array();
}

private function _tm_me($in)
{
    $id = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
    if ($id <= 0) return null;

    return array(
        'id'       => $id,
        'role'     => (int) (isset($in['role_id']) ? $in['role_id'] : 0),
        'is_admin' => in_array($id, $this->_tm_admins(), true),

        /* An EA sees the All box without becoming a module admin - the
           two are kept apart so nothing else keyed on is_admin widens. */
        'is_ea'    => $this->_is_ea($id),
    );
}

private function _tm_model()
{
    $this->load->model('Task_management_model', 'task_module');
    return $this->task_module;
}

private function _tm_out($payload)
{
    echo json_encode($payload);
}

private function _tm_fail($message, $extra = array())
{
    $this->_tm_out(array_merge(array('status' => false, 'message' => $message), $extra));
}

/** Same visibility rule as Task_management::can_view_task(). */
private function _tm_can_view($me, $task)
{
    if (empty($task) || empty($me)) return false;
    if (!empty($me['is_admin'])) return true;

    /* The EA reads every task in the All box, so she must be able to open
       one (2026-09-22). tm_list_api has let her list them since the box was
       added; this did not, so every row she tapped answered 'forbidden' -
       TM-260915-0071 among them. Read only: nothing keyed on is_admin
       widens, so she still cannot set a due date or post progress. */
    if (!empty($me['is_ea'])) return true;

    $id = (int) $me['id'];
    if ((int) $task['assigned_by_user_id'] === $id) return true;
    if ((int) $task['assigned_to_user_id'] === $id) return true;

    $model = $this->_tm_model();
    $leaders = array_merge(
        $model->get_user_team_leaders((int) $task['assigned_by_user_id']),
        $model->get_user_team_leaders((int) $task['assigned_to_user_id'])
    );

    foreach ($leaders as $leader) {
        if (!empty($leader['user_id']) && (int) $leader['user_id'] === $id) return true;
    }

    return false;
}

private function _tm_blank_date($d)
{
    return ($d === null || $d === '' || $d === '0000-00-00' || $d === '0000-00-00 00:00:00');
}

/** Attachments live where the web form puts them. */
private function _tm_file_url($file)
{
    $file = trim((string) $file);
    if ($file === '') return '';
    if (stripos($file, 'http://') === 0 || stripos($file, 'https://') === 0) return $file;
    return page_url22 . 'image_bank/task_management/' . rawurlencode($file);
}

private function _tm_status_label($status)
{
    $map = array(
        'AWAITING_DUE_DATE' => 'Awaiting Due Date',
        'OPEN'              => 'Open',
        'IN_PROGRESS'       => 'In Progress',
        'COMPLETED'         => 'Completed',
    );
    $status = strtoupper((string) $status);
    return isset($map[$status]) ? $map[$status] : ucfirst(strtolower($status));
}

/** Flatten a hydrated model row into what the app renders. */
private function _tm_shape($t, $me_id)
{
    $status = strtoupper((string) $t['status']);
    $due = $this->_tm_blank_date(isset($t['committed_due_date']) ? $t['committed_due_date'] : '')
        ? '' : $t['committed_due_date'];
    $requested = $this->_tm_blank_date(isset($t['requested_due_date']) ? $t['requested_due_date'] : '')
        ? '' : $t['requested_due_date'];

    $today = date('Y-m-d');
    $open = ($status !== 'COMPLETED');

    $timing = $this->_time_left($due !== '' ? $due : $requested, $open);

    return array_merge($timing, array(
        'id'              => (int) $t['id'],
        'task_code'       => (string) $t['task_code'],
        'title'           => (string) $t['title'],
        'task_details'    => (string) $t['task_details'],
        'reference_url'   => (string) (isset($t['reference_url']) ? $t['reference_url'] : ''),
        'attachment_url'  => $this->_tm_file_url(isset($t['attachment']) ? $t['attachment'] : ''),
        'priority'        => strtoupper((string) $t['priority']),
        'status'          => $status,
        'status_label'    => $this->_tm_status_label($status),
        'progress_percent' => (int) $t['progress_percent'],
        'requested_due_date' => $requested,
        'committed_due_date' => $due,
        'due_date'        => $due !== '' ? $due : $requested,
        'has_committed_due' => ($due !== ''),
        'is_overdue'      => !empty($t['is_overdue']),
        'is_due_today'    => ($open && $due !== '' && $due === $today),
        'assigned_by'     => (int) $t['assigned_by_user_id'],
        'assigned_by_name' => (string) (isset($t['creator_name']) ? $t['creator_name'] : ''),
        'assigned_to'     => (int) $t['assigned_to_user_id'],
        'assigned_to_name' => (string) (isset($t['assignee_name']) ? $t['assignee_name'] : ''),
        'department'      => (string) (isset($t['department']) ? $t['department'] : ''),
        'last_update_note' => (string) (isset($t['last_update_note']) ? $t['last_update_note'] : ''),
        'created_on'      => (string) (isset($t['created_on']) ? $t['created_on'] : ''),
        'updated_on'      => (string) (isset($t['updated_on']) ? $t['updated_on'] : ''),
        'completed_on'    => $this->_tm_blank_date(isset($t['completed_on']) ? $t['completed_on'] : '')
                                ? '' : (string) $t['completed_on'],
        'is_mine'         => ((int) $t['assigned_to_user_id'] === (int) $me_id),
        'i_created'       => ((int) $t['assigned_by_user_id'] === (int) $me_id),

        /* the three web capability flags, decided here so the app never
           has to re-derive them and drift from Task_management::view() */
        'can_set_due_date' => (
            (int) $t['assigned_to_user_id'] === (int) $me_id
            && $status === 'AWAITING_DUE_DATE'
        ),
        'can_update_progress' => (
            (int) $t['assigned_to_user_id'] === (int) $me_id
            && $status !== 'COMPLETED'
            && $due !== ''
        ),
        'can_reopen' => (
            (int) $t['assigned_by_user_id'] === (int) $me_id
            && $status === 'COMPLETED'
        ),
    ));
}

/** Phone push, same transport the chat and delegation modules use. */
private function _tm_push($user_id, $title, $body, $task_id)
{
    try {
        if (!function_exists('sendFCMData')) return;

        $devices = $this->db->select('fcm_token')
            ->from('user_devices')
            ->where('user_id', (int) $user_id)
            ->where('fcm_token !=', '')
            ->get()->result();

        $preview = mb_substr((string) $body, 0, 140);

        foreach ($devices as $d) {
            if (empty($d->fcm_token)) continue;
            sendFCMData($d->fcm_token, $title, $preview, array(
                'type'      => 'task_management',
                'task_id'   => (string) $task_id,
                'screen'    => 'task_detail',
                'timestamp' => date('Y-m-d H:i:s'),
            ));
        }
    } catch (Throwable $e) {
        log_message('error', 'Task push failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 1. SUMMARY - the strip on the landing dashboard
 * ------------------------------------------------------------------ */
public function tm_summary_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $model = $this->_tm_model();

        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        date_default_timezone_set('Asia/Kolkata');

        $stats = $model->get_dashboard_stats($me['id']);
        $unread = $model->get_unread_notifications($me['id']);

        $this->_tm_out(array(
            'status'       => true,
            'can_create'   => true,   // open to everyone, as on the web
            'is_admin'     => $me['is_admin'],
            'is_ea'        => $me['is_ea'],
            'stats'        => $stats,
            'unread_count' => count($unread),
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Task summary failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 2. LIST - box = assigned (to me) | created (by me) | all (admin only)
 * ------------------------------------------------------------------ */
public function tm_list_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        date_default_timezone_set('Asia/Kolkata');

        $box   = isset($in['box']) ? strtolower(trim($in['box'])) : 'assigned';
        $limit = (int) (isset($in['limit']) ? $in['limit'] : 200);
        if ($limit <= 0 || $limit > 1000) $limit = 200;

        $filters = array(
            'keyword'    => isset($in['search']) ? trim((string) $in['search']) : '',
            'status'     => isset($in['status']) ? strtoupper(trim((string) $in['status'])) : '',
            'priority'   => isset($in['priority']) ? strtoupper(trim((string) $in['priority'])) : '',
            'due_state'  => isset($in['due_state']) ? strtoupper(trim((string) $in['due_state'])) : '',
        );

        if ($box === 'all') {
            if (empty($me['is_admin']) && empty($me['is_ea'])) {
                return $this->_tm_fail('forbidden');
            }

            /* The model filters one side at a time, so "either side" is two
               passes merged on task id - the same shape the global search
               uses for a non-admin's tasks. */
            $who = $this->_side_of($in);

            if ($who['user_id'] > 0 && $who['side'] === 'assigned_to') {
                $filters['assigned_to_user_id'] = $who['user_id'];
                $rows = $model->get_all_tasks($filters, $limit);

            } elseif ($who['user_id'] > 0 && $who['side'] === 'assigned_by') {
                $filters['assigned_by_user_id'] = $who['user_id'];
                $rows = $model->get_all_tasks($filters, $limit);

            } elseif ($who['user_id'] > 0) {
                $to = (array) $model->get_all_tasks(
                    array_merge($filters, array('assigned_to_user_id' => $who['user_id'])),
                    $limit
                );
                $by = (array) $model->get_all_tasks(
                    array_merge($filters, array('assigned_by_user_id' => $who['user_id'])),
                    $limit
                );

                $seen = array();
                $rows = array();
                foreach (array_merge($to, $by) as $r) {
                    $tid = (int) (isset($r['id']) ? $r['id'] : 0);
                    if ($tid > 0 && isset($seen[$tid])) continue;
                    $seen[$tid] = true;
                    $rows[] = $r;
                }
                $rows = array_slice($rows, 0, $limit);

            } else {
                $rows = $model->get_all_tasks($filters, $limit);
            }
        } elseif ($box === 'created') {
            $filters['assigned_by_user_id'] = $me['id'];
            $rows = $model->get_all_tasks($filters, $limit);
        } else {
            $box = 'assigned';
            $filters['assigned_to_user_id'] = $me['id'];
            $rows = $model->get_all_tasks($filters, $limit);
        }

        $tasks = array();
        foreach ((array) $rows as $r) {
            $tasks[] = $this->_tm_shape($r, $me['id']);
        }

        $this->_tm_out(array(
            'status'   => true,
            'box'      => $box,
            'count'    => count($tasks),
            'is_admin' => $me['is_admin'],
            'is_ea'    => $me['is_ea'],
            'tasks'    => $tasks,
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Task list failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 3. DETAIL - one task plus its update trail
 * ------------------------------------------------------------------ */
public function tm_detail_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $task_id = (int) (isset($in['task_id']) ? $in['task_id'] : 0);
        if ($task_id <= 0) return $this->_tm_fail('task_id is required');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        date_default_timezone_set('Asia/Kolkata');

        $task = $model->get_task($task_id);
        if (empty($task)) return $this->_tm_fail('Task not found');
        if (!$this->_tm_can_view($me, $task)) return $this->_tm_fail('forbidden');

        $updates = array();
        foreach ((array) $model->get_task_updates($task_id) as $u) {
            $updates[] = array(
                'id'               => (int) $u['id'],
                'update_type'      => (string) $u['update_type'],
                'status'           => strtoupper((string) $u['status']),
                'status_label'     => $this->_tm_status_label($u['status']),
                'progress_percent' => (int) $u['progress_percent'],
                'due_date'         => $this->_tm_blank_date(isset($u['due_date']) ? $u['due_date'] : '')
                                        ? '' : (string) $u['due_date'],
                'update_note'      => (string) $u['update_note'],
                'actor_name'       => (string) (isset($u['actor_name']) ? $u['actor_name'] : ''),
                'created_by'       => (int) $u['created_by'],
                'created_on'       => (string) $u['created_on'],
            );
        }

        $this->_tm_out(array(
            'status'  => true,
            'task'    => $this->_tm_shape($task, $me['id']),
            'updates' => $updates,
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Task detail failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 4. FORM OPTIONS - assignable users plus the web's own filter lists
 * ------------------------------------------------------------------ */
public function tm_options_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        $users = array();
        foreach ((array) $model->get_assignable_users() as $u) {
            $users[] = array(
                'user_id'       => (int) $u['user_id'],
                'name'          => (string) $u['name'],
                'department_id' => (int) $u['department_id'],
                'department'    => (string) (isset($u['department']) ? $u['department'] : ''),
            );
        }

        $this->_tm_out(array(
            'status'      => true,
            'users'       => $users,
            'priorities'  => $model->get_priority_filters(),
            'statuses'    => $model->get_status_filters(),
            'due_states'  => $model->get_due_state_filters(),
            'departments' => $model->get_department_filters(),
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Task options failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 5. CREATE - the app side of Task_management::save()
 *    One row per assignee, each with its own TM- code, all starting in
 *    AWAITING_DUE_DATE. Multipart when an attachment rides along.
 * ------------------------------------------------------------------ */
public function tm_create_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        $title = trim((string) (isset($in['title']) ? $in['title'] : ''));
        if ($title === '') return $this->_tm_fail('Task title is required');

        $details = trim((string) (isset($in['task_details']) ? $in['task_details'] : ''));
        if ($details === '') return $this->_tm_fail('Task details are required');

        $priority = strtoupper(trim((string) (isset($in['priority']) ? $in['priority'] : 'MEDIUM')));
        $allowed_priorities = array_keys($model->get_priority_filters());
        if (!in_array($priority, $allowed_priorities, true)) {
            return $this->_tm_fail('Priority must be one of: ' . implode(', ', $allowed_priorities));
        }

        $due_raw = trim((string) (isset($in['requested_due_date']) ? $in['requested_due_date'] : ''));
        if ($due_raw === '') return $this->_tm_fail('Suggested due date is required');

        $requested_due_date = date('Y-m-d', strtotime($due_raw));
        if ($requested_due_date === '1970-01-01') {
            return $this->_tm_fail('Please select a valid suggested due date.');
        }

        $ids = isset($in['assigned_to_user_id']) ? $in['assigned_to_user_id'] : array();
        if (!is_array($ids)) {
            $ids = ($ids === '' || $ids === null) ? array() : explode(',', (string) $ids);
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (empty($ids)) return $this->_tm_fail('Please select at least one assignee.');

        $assignees = $model->get_users($ids);
        if (count($assignees) !== count($ids)) {
            return $this->_tm_fail('One or more selected assignees could not be found.');
        }

        foreach ($ids as $id) {
            if ((int) $assignees[$id]['business_location'] !== (int) $this->_tm_location()) {
                return $this->_tm_fail('Only Shubham Pack users can be selected in this task form.');
            }
        }

        /* attachment - same folder and naming the web form uses */
        $attachment = '';
        if (!empty($_FILES['attachment']['name'])) {
            $upload_dir = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/') . '/image_bank/task_management/';
            if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
                return $this->_tm_fail('Unable to prepare the task upload folder.');
            }

            $original = (string) $_FILES['attachment']['name'];
            $ext = pathinfo($original, PATHINFO_EXTENSION);
            $filename = 'task-' . time() . '-' . mt_rand(1000, 9999);
            if ($ext !== '') $filename .= '.' . strtolower($ext);

            if (@move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_dir . $filename)) {
                $attachment = $filename;
            }
        }

        date_default_timezone_set('Asia/Kolkata');
        $now = date('Y-m-d H:i:s');

        $base = array(
            'task_code'            => '',
            'business_location_id' => (int) $this->_tm_location(),
            'title'                => $title,
            'task_details'         => $details,
            'reference_url'        => trim((string) (isset($in['reference_url']) ? $in['reference_url'] : '')),
            'attachment'           => $attachment,
            'priority'             => $priority,
            'requested_due_date'   => $requested_due_date,
            'committed_due_date'   => null,
            'assigned_by_user_id'  => $me['id'],
            'status'               => 'AWAITING_DUE_DATE',
            'progress_percent'     => 0,
            'last_update_note'     => 'Task created and waiting for assignee due-date confirmation.',
            'created_on'           => $now,
            'updated_on'           => $now,
        );

        $creator = $model->get_user($me['id']);
        $creator_name = (!empty($creator['name'])) ? $creator['name'] : 'Task owner';

        $this->db->trans_start();
        $created = array();

        foreach ($ids as $assigned_to) {
            $assignee = $assignees[$assigned_to];

            $data = $base;
            $data['assigned_to_user_id'] = $assigned_to;
            $data['assigned_to_department_id'] = !empty($assignee['department_id'])
                ? (int) $assignee['department_id'] : 0;

            $task_id = $model->create_task($data);
            $task_code = $model->generate_task_code($task_id);
            $model->update_task($task_id, array('task_code' => $task_code));

            $model->add_update(array(
                'task_id'          => $task_id,
                'update_type'      => 'CREATED',
                'status'           => 'AWAITING_DUE_DATE',
                'progress_percent' => 0,
                'due_date'         => $requested_due_date,
                'update_note'      => $details,
                'created_by'       => $me['id'],
                'created_on'       => $now,
            ));

            $action_url = page_url . 'Task_management/view/' . $task_id;
            $model->add_notifications(
                $task_id,
                array($assigned_to),
                $task_code . ' assigned by ' . $creator_name . '. Please confirm the due date.',
                $action_url
            );

            $created[] = array('id' => $task_id, 'code' => $task_code, 'to' => $assigned_to);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->_tm_fail('Task could not be created right now. Please try again.');
        }

        foreach ($created as $c) {
            $this->_tm_push(
                $c['to'],
                'New task: ' . $c['code'],
                $title . ' - please confirm the due date.',
                $c['id']
            );
        }

        $this->_tm_out(array(
            'status'   => true,
            'message'  => count($created) . ' task' . (count($created) === 1 ? '' : 's')
                          . ' created: ' . implode(', ', array_column($created, 'code')) . '.',
            'task_ids' => array_column($created, 'id'),
            'codes'    => array_column($created, 'code'),
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Task create failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 6. CONFIRM DUE DATE - assignee only, and only while AWAITING_DUE_DATE
 * ------------------------------------------------------------------ */
public function tm_confirm_due_date_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $task_id = (int) (isset($in['task_id']) ? $in['task_id'] : 0);
        if ($task_id <= 0) return $this->_tm_fail('task_id is required');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        $task = $model->get_task($task_id);
        if (empty($task)) return $this->_tm_fail('Task not found');

        if ((int) $task['assigned_to_user_id'] !== $me['id']) {
            return $this->_tm_fail('Only the assigned person can confirm the due date.');
        }

        if (strtoupper((string) $task['status']) !== 'AWAITING_DUE_DATE') {
            return $this->_tm_fail('Final due date has already been confirmed for this task.');
        }

        $note = trim((string) (isset($in['schedule_note']) ? $in['schedule_note'] : ''));
        if ($note === '') return $this->_tm_fail('Schedule note is required');

        $raw = trim((string) (isset($in['committed_due_date']) ? $in['committed_due_date'] : ''));
        if ($raw === '') return $this->_tm_fail('Final due date is required');

        $committed = date('Y-m-d', strtotime($raw));
        if ($committed === '1970-01-01') {
            return $this->_tm_fail('Please select a valid final due date.');
        }

        date_default_timezone_set('Asia/Kolkata');
        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $model->update_task($task_id, array(
            'committed_due_date' => $committed,
            'status'             => 'OPEN',
            'last_update_note'   => $note,
            'updated_on'         => $now,
        ));

        $model->add_update(array(
            'task_id'          => $task_id,
            'update_type'      => 'DUE_DATE_CONFIRMED',
            'status'           => 'OPEN',
            'progress_percent' => (int) $task['progress_percent'],
            'due_date'         => $committed,
            'update_note'      => $note,
            'created_by'       => $me['id'],
            'created_on'       => $now,
        ));

        $model->add_notifications(
            $task_id,
            array((int) $task['assigned_by_user_id']),
            $task['task_code'] . ' due date confirmed for ' . date('d M Y', strtotime($committed)) . '.',
            page_url . 'Task_management/view/' . $task_id
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->_tm_fail('Final due date could not be confirmed right now. Please try again.');
        }

        $this->_tm_push(
            (int) $task['assigned_by_user_id'],
            'Due date confirmed: ' . $task['task_code'],
            date('d M Y', strtotime($committed)) . ' - ' . $note,
            $task_id
        );

        $this->_tm_out(array(
            'status'  => true,
            'message' => 'Final due date confirmed successfully.',
            'task'    => $this->_tm_shape($model->get_task($task_id), $me['id']),
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Confirm due date failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 7. PROGRESS - assignee posts progress; 100% or COMPLETED closes it.
 *    The status ladder is copied from Task_management::save_progress().
 * ------------------------------------------------------------------ */
public function tm_progress_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $task_id = (int) (isset($in['task_id']) ? $in['task_id'] : 0);
        if ($task_id <= 0) return $this->_tm_fail('task_id is required');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        $task = $model->get_task($task_id);
        if (empty($task)) return $this->_tm_fail('Task not found');

        if ((int) $task['assigned_to_user_id'] !== $me['id']) {
            return $this->_tm_fail('Only the assigned person can update progress.');
        }

        if ($this->_tm_blank_date($task['committed_due_date'])) {
            return $this->_tm_fail('Please confirm the final due date before posting progress updates.');
        }

        $note = trim((string) (isset($in['progress_note']) ? $in['progress_note'] : ''));
        if ($note === '') return $this->_tm_fail('Progress note is required');

        $progress = max(0, min(100, (int) (isset($in['progress_percent']) ? $in['progress_percent'] : 0)));
        $submitted = strtoupper(trim((string) (isset($in['status']) ? $in['status'] : '')));

        $new_status = 'OPEN';
        if ($submitted === 'COMPLETED' || $progress >= 100) {
            $new_status = 'COMPLETED';
            $progress = 100;
        } elseif ($progress > 0 || $submitted === 'IN_PROGRESS') {
            $new_status = 'IN_PROGRESS';
        }

        date_default_timezone_set('Asia/Kolkata');
        $now = date('Y-m-d H:i:s');

        $update_data = array(
            'status'           => $new_status,
            'progress_percent' => $progress,
            'last_update_note' => $note,
            'updated_on'       => $now,
        );
        if ($new_status === 'COMPLETED') {
            $update_data['completed_on'] = $now;
            $update_data['completed_by_user_id'] = $me['id'];
        }

        $this->db->trans_start();

        $model->update_task($task_id, $update_data);
        $model->add_update(array(
            'task_id'          => $task_id,
            'update_type'      => $new_status === 'COMPLETED' ? 'COMPLETED' : 'PROGRESS_UPDATED',
            'status'           => $new_status,
            'progress_percent' => $progress,
            'due_date'         => $task['committed_due_date'],
            'update_note'      => $note,
            'created_by'       => $me['id'],
            'created_on'       => $now,
        ));

        $model->add_notifications(
            $task_id,
            array((int) $task['assigned_by_user_id']),
            $new_status === 'COMPLETED'
                ? $task['task_code'] . ' has been marked completed.'
                : $task['task_code'] . ' progress updated to ' . $progress . '%.',
            page_url . 'Task_management/view/' . $task_id
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->_tm_fail('Progress could not be updated right now. Please try again.');
        }

        $this->_tm_push(
            (int) $task['assigned_by_user_id'],
            ($new_status === 'COMPLETED' ? 'Task completed: ' : 'Task update: ') . $task['task_code'],
            $note,
            $task_id
        );

        $this->_tm_out(array(
            'status'  => true,
            'message' => $new_status === 'COMPLETED'
                            ? 'Task marked completed successfully.'
                            : 'Progress updated successfully.',
            'task'    => $this->_tm_shape($model->get_task($task_id), $me['id']),
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Progress update failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 8. REOPEN - creator only, and only on a COMPLETED task
 * ------------------------------------------------------------------ */
public function tm_reopen_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $task_id = (int) (isset($in['task_id']) ? $in['task_id'] : 0);
        if ($task_id <= 0) return $this->_tm_fail('task_id is required');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_fail('Task Management is not set up on the server yet.');
        }

        $task = $model->get_task($task_id);
        if (empty($task)) return $this->_tm_fail('Task not found');

        if ((int) $task['assigned_by_user_id'] !== $me['id']) {
            return $this->_tm_fail('Only the person who created this task can reopen it.');
        }

        if (strtoupper((string) $task['status']) !== 'COMPLETED') {
            return $this->_tm_fail('Only completed tasks can be reopened.');
        }

        $remark = trim((string) (isset($in['reopen_remark']) ? $in['reopen_remark'] : ''));
        if ($remark === '') return $this->_tm_fail('Reopen remark is required');
        if (mb_strlen($remark) > 2000) return $this->_tm_fail('Reopen remark is too long.');

        date_default_timezone_set('Asia/Kolkata');
        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $model->update_task($task_id, array(
            'status'               => 'OPEN',
            'progress_percent'     => 0,
            'last_update_note'     => $remark,
            'completed_on'         => null,
            'completed_by_user_id' => null,
            'updated_on'           => $now,
        ));

        $model->add_update(array(
            'task_id'          => $task_id,
            'update_type'      => 'REOPENED',
            'status'           => 'OPEN',
            'progress_percent' => 0,
            'due_date'         => $task['committed_due_date'],
            'update_note'      => $remark,
            'created_by'       => $me['id'],
            'created_on'       => $now,
        ));

        $model->add_notifications(
            $task_id,
            array((int) $task['assigned_to_user_id']),
            $task['task_code'] . ' has been reopened by the task creator and assigned back to you.',
            page_url . 'Task_management/view/' . $task_id
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->_tm_fail('Task could not be reopened right now. Please try again.');
        }

        $this->_tm_push(
            (int) $task['assigned_to_user_id'],
            'Task reopened: ' . $task['task_code'],
            $remark,
            $task_id
        );

        $this->_tm_out(array(
            'status'  => true,
            'message' => 'Task reopened and assigned back to ' . $task['assignee_name'] . '.',
            'task'    => $this->_tm_shape($model->get_task($task_id), $me['id']),
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Reopen failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 9. NOTIFICATIONS - read the unread ones, or mark them read
 * ------------------------------------------------------------------ */
public function tm_notifications_api()
{
    try {
        $in = $this->_tm_input();
        $me = $this->_tm_me($in);
        if (!$me) return $this->_tm_fail('auth');

        $model = $this->_tm_model();
        if (!$model->module_ready()) {
            return $this->_tm_out(array('status' => true, 'notifications' => array()));
        }

        $action = isset($in['action']) ? strtolower(trim((string) $in['action'])) : 'list';

        if ($action === 'read') {
            $nid = (int) (isset($in['notification_id']) ? $in['notification_id'] : 0);
            if ($nid > 0) {
                $model->mark_notification_read($nid, $me['id']);
            } else {
                $model->mark_all_notifications_read($me['id']);
            }
            return $this->_tm_out(array('status' => true, 'message' => 'Marked as read.'));
        }

        $rows = $model->get_unread_notifications($me['id']);

        $out = array();
        foreach ((array) $rows as $r) {
            $out[] = array(
                'id'         => (int) $r['id'],
                'task_id'    => (int) $r['task_id'],
                'message'    => (string) $r['message'],
                'created_on' => (string) $r['created_on'],
            );
        }

        $this->_tm_out(array(
            'status'        => true,
            'notifications' => $out,
            'unread_count'  => count($out),
        ));
    } catch (Throwable $e) {
        $this->_tm_fail('Task notifications failed: ' . $e->getMessage());
    }
}

/* =====================================================================
 * PROFILE (mobile)
 * ---------------------------------------------------------------------
 * Reuses system_users.profile_image, the column the web PMS already uses,
 * and the same image_bank/users/ folder — so a photo set from the phone
 * shows on the website and in chat immediately. Chat already returns an
 * `avatar` per message via chat_avatar_url(); nothing there needs to change.
 * ===================================================================== */

private function _pf_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json) && !empty($json)) return $json;
    }
    $post = $this->input->post();
    return is_array($post) ? $post : array();
}

private function _pf_out($payload)
{
    echo json_encode($payload);
}

private function _pf_fail($message)
{
    $this->_pf_out(array('status' => false, 'message' => $message));
}

/** Same URL shape chat_avatar_url() produces, so both agree. */
private function _pf_avatar_url($file)
{
    $file = trim((string) $file);
    if ($file === '') return '';
    if (preg_match('#^https?://#i', $file)) return $file;
    if (defined('user_profile')) return user_profile . rawurlencode($file);
    return page_url22 . 'image_bank/users/' . rawurlencode($file);
}

private function _pf_initials($first, $last)
{
    $a = trim((string) $first);
    $b = trim((string) $last);
    $i = ($a !== '' ? mb_substr($a, 0, 1) : '') . ($b !== '' ? mb_substr($b, 0, 1) : '');
    return $i === '' ? '?' : mb_strtoupper($i);
}

private function _pf_user($user_id)
{
    return $this->db->select('u.user_id, u.first_name, u.last_name, u.email, u.title,
                              u.contact_number, u.profile_image, u.department_id,
                              u.user_role_id, u.business_location, u.employeecode,
                              u.employee_code,
                              d.department')
        ->from('system_users u')
        ->join('departments d', 'd.department_id = u.department_id', 'left')
        ->where('u.user_id', (int) $user_id)
        ->limit(1)
        ->get()->row();
}

/*
 * Two different columns on system_users hold an employee code and they do
 * not agree. employee_code is the real HR number typed into the Add/Edit
 * User form (Hukam Sorout = 757); employeecode is the older 5-character
 * string the web PMS 'generate code' button rolls at random (K8QSE). The
 * HR number is the one people actually use, and since 2026-09-15 it is the
 * ONLY one shown. On 2026-09-14, of 181 active users: 36 had both, 18 only
 * the HR number, 62 only the generated one, and 65 had neither - so this
 * returns '' for most of the company, and the Profile screen is built to
 * leave the row blank rather than fill it with something meaningless.
 */
private function _pf_emp_code($row)
{
    /* Only the HR number, as asked on 2026-09-15: "employee code from
       system_users to be picked, check the field, if not set show blank".
       The generated 5-character string used to stand in for it, which meant
       62 of 181 active users were shown a random code (K8QSE) that nobody
       in the company uses or recognises - worse than an honest blank. */
    return trim((string) (isset($row->employee_code) ? $row->employee_code : ''));
}

private function _pf_shape($row)
{
    $name = trim(trim((string) $row->first_name) . ' ' . trim((string) $row->last_name));

    return array(
        'user_id'        => (int) $row->user_id,
        // the HR number only, blank when it is not on file - see _pf_emp_code
        'employee_code'  => $this->_pf_emp_code($row),
        'name'           => ucwords(strtolower($name)),
        'first_name'     => (string) $row->first_name,
        'last_name'      => (string) $row->last_name,
        'title'          => (string) $row->title,
        'email'          => (string) $row->email,
        'contact_number' => (string) $row->contact_number,
        'department_id'  => (int) $row->department_id,
        'department'     => (string) (isset($row->department) ? $row->department : ''),
        'avatar'         => $this->_pf_avatar_url($row->profile_image),
        'has_photo'      => (trim((string) $row->profile_image) !== ''),
        'initials'       => $this->_pf_initials($row->first_name, $row->last_name),
    );
}

/* ------------------------------------------------------------------ *
 * 1. READ - the profile screen and the drawer header
 * ------------------------------------------------------------------ */
public function profile_get_api()
{
    try {
        $in = $this->_pf_input();
        $uid = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
        if ($uid <= 0) return $this->_pf_fail('auth');

        $row = $this->_pf_user($uid);
        if (!$row) return $this->_pf_fail('User not found');

        $this->_pf_out(array('status' => true, 'profile' => $this->_pf_shape($row)));
    } catch (Throwable $e) {
        $this->_pf_fail('Profile failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 2. UPLOAD PHOTO - multipart, field "photo"
 * ------------------------------------------------------------------ */
public function profile_upload_photo_api()
{
    try {
        $in = $this->_pf_input();
        $uid = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
        if ($uid <= 0) return $this->_pf_fail('auth');

        $row = $this->_pf_user($uid);
        if (!$row) return $this->_pf_fail('User not found');

        if (empty($_FILES['photo']['name'])) {
            return $this->_pf_fail('No photo was received.');
        }

        $upload_dir = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/') . '/image_bank/users/';
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
            return $this->_pf_fail('Unable to prepare the profile photo folder.');
        }

        $orig = (string) $_FILES['photo']['name'];
        $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

        // heic/heif included: that is what an iPhone camera produces
        $allowed = array('jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif');
        if (!in_array($ext, $allowed, true)) {
            return $this->_pf_fail('Please choose a JPG, PNG or WEBP image.');
        }

        if ((int) $_FILES['photo']['size'] > 8388608) {
            return $this->_pf_fail('Please choose an image under 8 MB.');
        }

        // profile_image is varchar(50), so the stored name has to stay short
        $safe = 'u' . $uid . '_' . time() . '.' . $ext;
        if (strlen($safe) > 50) {
            $safe = 'u' . $uid . '_' . substr((string) time(), -6) . '.' . $ext;
        }

        if (!@move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $safe)) {
            return $this->_pf_fail('Could not save the photo.');
        }
        @chmod($upload_dir . $safe, 0644);

        $previous = trim((string) $row->profile_image);

        $ok = $this->db->where('user_id', $uid)
            ->update('system_users', array('profile_image' => $safe));

        if (!$ok) {
            @unlink($upload_dir . $safe);
            return $this->_pf_fail('Could not update your profile.');
        }

        // drop the old file, but never anything that looks like a shared or
        // absolute path - only a bare filename this folder owns
        if ($previous !== '' && $previous !== $safe
            && !preg_match('#[/\\\\]#', $previous)
            && is_file($upload_dir . $previous)) {
            @unlink($upload_dir . $previous);
        }

        $fresh = $this->_pf_user($uid);

        $this->_pf_out(array(
            'status'  => true,
            'message' => 'Profile photo updated.',
            'profile' => $this->_pf_shape($fresh),
        ));
    } catch (Throwable $e) {
        $this->_pf_fail('Photo upload failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 3. REMOVE PHOTO
 * ------------------------------------------------------------------ */
public function profile_remove_photo_api()
{
    try {
        $in = $this->_pf_input();
        $uid = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
        if ($uid <= 0) return $this->_pf_fail('auth');

        $row = $this->_pf_user($uid);
        if (!$row) return $this->_pf_fail('User not found');

        $previous = trim((string) $row->profile_image);

        $this->db->where('user_id', $uid)
            ->update('system_users', array('profile_image' => ''));

        $upload_dir = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/') . '/image_bank/users/';
        if ($previous !== ''
            && !preg_match('#[/\\\\]#', $previous)
            && is_file($upload_dir . $previous)) {
            @unlink($upload_dir . $previous);
        }

        $fresh = $this->_pf_user($uid);

        $this->_pf_out(array(
            'status'  => true,
            'message' => 'Profile photo removed.',
            'profile' => $this->_pf_shape($fresh),
        ));
    } catch (Throwable $e) {
        $this->_pf_fail('Remove failed: ' . $e->getMessage());
    }
}

/* ==================================================================
 * GLOBAL SEARCH
 *
 * One box across every module the app already shows. The governing
 * rule is that search must never widen what a person can see: each
 * section reuses the ownership test its own list endpoint already
 * applies, so anything that comes back is something the user can
 * actually open. An admin sees everything, a team leader sees the
 * departments they lead, and everyone else sees only their own rows.
 * ================================================================== */

private function _gs_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) return $decoded;
    }
    return $_POST;
}

private function _gs_out($payload)
{
    echo json_encode($payload);
}

private function _gs_fail($message, $extra = array())
{
    $this->_gs_out(array_merge(array('status' => false, 'message' => $message), $extra));
}

/**
 * Everything the search needs to know about the caller, resolved once.
 *
 * The "may_*" flags mirror get_user_permissions_api - they decide which
 * sections are searched at all. The "all_*" flags mirror the admin test
 * inside each list endpoint - they decide how wide that section looks.
 * The two are deliberately separate: the visit tab, for instance, is
 * handed out by department while visit_list_api treats role 12/41 as
 * admin, and search has to respect both.
 */
private function _gs_scope($in)
{
    $id = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
    if ($id <= 0) return null;

    $role = (int) (isset($in['role_id']) ? $in['role_id'] : 0);
    $dept = (int) (isset($in['department_id']) ? $in['department_id'] : 0);

    // 1 = super admin, 2 = team leader / HOD, 3 = everyone else.
    $user_type = (int) $this->getUserType($role, $id);
    $super     = ($user_type === 1);

    $led = array();
    if ($user_type === 2) {
        $q = $this->db->select('department_id')
                      ->from('prestogroup_teams')
                      ->where('team_leader', $id)
                      ->get();
        foreach ($q->result() as $r) $led[] = (int) $r->department_id;
        $led = array_values(array_unique($led));
    }

    $trio = in_array($id, array(61, 139, 161));

    return array(
        'id'              => $id,
        'role'            => $role,
        'department'      => $dept,
        'user_type'       => $user_type,
        'super'           => $super,
        'led_departments' => $led,

        /* Which modules this user has at all (get_user_permissions_api). */
        'may_lead'    => ($role == 12 || $dept == 9 || $trio),
        'may_service' => ($role == 12 || ($dept == 22 && $id == 111) || $trio),
        'may_spare'   => ($role == 12 || ($dept == 31 && $id == 89) || $trio),
        'may_visit'   => (($dept == 22 && $id != 111) || $super || $role == 12 || $role == 41 || $id == 111),

        /* Which modules this user sees in full (each list endpoint's own test). */
        'all_df'       => $super,
        'all_lead'     => ($super || $role == 12 || $role == 41),
        'all_customer' => ($super || $role == 12 || $role == 41),
        'all_service'  => ($super || $role == 12 || $role == 41 || $id == 111),
        'all_spare'    => ($super || $role == 1  || $role == 12),
        'all_visit'    => ($super || $role == 12 || $role == 41 || $id == 111),
        'all_task'     => ($super || in_array($id, $this->_tm_admins())),
        'all_deleg'    => $super,
    );
}

/** The sections this user is allowed to search, in the order they render. */
private function _gs_sections($me)
{
    return array(
        'df_record'      => array('label' => 'DF',            'on' => true),
        'df'             => array('label' => 'DF Tasks',      'on' => true),
        'task'           => array('label' => 'Tasks',         'on' => true),
        'delegation'     => array('label' => 'Delegation',    'on' => true),
        'lead'           => array('label' => 'Leads',         'on' => (bool) $me['may_lead']),
        'customer'       => array('label' => 'Customers',     'on' => (bool) ($me['may_lead'] || $me['may_service'] || $me['super'])),
        'service'        => array('label' => 'Service',       'on' => (bool) $me['may_service']),
        'spare'          => array('label' => 'Spares',        'on' => (bool) $me['may_spare']),
        'spare_customer' => array('label' => 'Spare Customers','on' => (bool) $me['may_spare']),
        'visit'          => array('label' => 'Engineer Visits','on' => (bool) $me['may_visit']),
    );
}

private function _gs_date($d)
{
    $d = trim((string) $d);
    if ($d === '' || $d === '0000-00-00' || $d === '0000-00-00 00:00:00') return '';
    $ts = strtotime($d);
    return $ts ? date('d-m-Y', $ts) : '';
}

private function _gs_name($first, $last)
{
    return trim(trim((string) $first) . ' ' . trim((string) $last));
}

/* ==================================================================
 * EXECUTIVE ASSISTANT VIEW
 *
 * An EA chases the whole company's pending work on the admin's behalf,
 * so they need everyone's delegations, tasks and help tickets - not
 * their own. This is deliberately a named list of people rather than a
 * role or a department: other admins carry the same role_id and must
 * NOT inherit this, which is exactly what asking about role would do.
 *
 * Add an id here and that person gets the "All" box in all three
 * modules. Nothing else about their account changes - it grants a
 * wider read, never the right to act on someone else's row.
 * ================================================================== */
/**
 * "3 days left" / "Due today" / "5 days late" for a Y-m-d due date.
 *
 * Computed on the server so delegation, tasks and help tickets all count
 * days the same way and against the same clock - a phone in another
 * timezone, or one whose date is simply wrong, would otherwise disagree
 * with the row it is displaying.
 */
private function _time_left($due, $is_open = true)
{
    $out = array('time_label' => '', 'time_state' => 'none', 'time_days' => null);

    $due = trim((string) $due);
    if ($due === '' || strpos($due, '0000') === 0) {
        $out['time_label'] = $is_open ? 'No due date' : '';
        return $out;
    }

    $d = strtotime(substr($due, 0, 10) . ' 00:00:00');
    if ($d === false) return $out;

    $today = strtotime(date('Y-m-d') . ' 00:00:00');
    $days  = (int) round(($d - $today) / 86400);

    $out['time_days'] = $days;

    /* A finished row is not late or early any more, it is just done. */
    if (!$is_open) {
        $out['time_state'] = 'done';
        return $out;
    }

    if ($days === 0) {
        $out['time_state'] = 'today';
        $out['time_label'] = 'Due today';
    } elseif ($days > 0) {
        $out['time_state'] = 'left';
        $out['time_label'] = $days . ' day' . ($days === 1 ? '' : 's') . ' left';
    } else {
        $late = -$days;
        $out['time_state'] = 'late';
        $out['time_label'] = $late . ' day' . ($late === 1 ? '' : 's') . ' exceeded';
    }

    return $out;
}

/**
 * How long a help ticket has been open.
 *
 * Tickets carry no due date - communication_ticket_system has none - so
 * there is nothing to be early or late against. Age is the honest
 * equivalent, and it is what tells an EA which ones have been sitting.
 */
private function _time_open($from)
{
    $out = array('time_label' => '', 'time_state' => 'open', 'time_days' => null);

    $from = trim((string) $from);
    if ($from === '' || strpos($from, '0000') === 0) return $out;

    $t = strtotime(substr($from, 0, 10) . ' 00:00:00');
    if ($t === false) return $out;

    $today = strtotime(date('Y-m-d') . ' 00:00:00');
    $days  = (int) round(($today - $t) / 86400);
    if ($days < 0) $days = 0;

    $out['time_days'] = $days;
    $out['time_label'] = ($days === 0)
        ? 'Raised today'
        : 'Open ' . $days . ' day' . ($days === 1 ? '' : 's');

    /* a week is the point at which one stops being merely recent */
    if ($days >= 7) $out['time_state'] = 'aging';

    return $out;
}

/**
 * The "whose row is this" filter the All boxes carry.
 *
 * Returns array(user_id, side) where side is one of any|assigned_to|
 * assigned_by. Every module means the same two things by those words even
 * though each stores them in differently named columns, so the caller
 * translates the side and this only has to agree on the vocabulary.
 */
private function _side_of($in)
{
    $uid = (int) (isset($in['filter_user_id']) ? $in['filter_user_id'] : 0);

    $side = isset($in['filter_side'])
        ? strtolower(trim((string) $in['filter_side']))
        : 'any';

    if (!in_array($side, array('any', 'assigned_to', 'assigned_by'), true)) {
        $side = 'any';
    }

    return array('user_id' => $uid > 0 ? $uid : 0, 'side' => $side);
}

private function _ea_ids()
{
    return array(243);   // Riya Sundhir - EA to admin
}

private function _is_ea($user_id)
{
    return in_array((int) $user_id, $this->_ea_ids(), true);
}

/** Where the web serves a released DF's attachment. */
private function _gs_df_file_url($file)
{
    $file = trim((string) $file);
    if ($file === '') return '';
    if (stripos($file, 'http://') === 0 || stripos($file, 'https://') === 0) return $file;
    return sfdocument . 'Taskdocument/dfattachment/' . rawurlencode($file);
}

/**
 * The PO attachment sits directly under Taskdocument/, not in the
 * dfattachment/ subfolder the released DF uses - built the same way the
 * task list builds its own PO button so both open the identical file.
 * Not always a PDF: that directory holds xlsx and png too.
 */
private function _gs_po_file_url($file)
{
    $file = trim((string) $file);
    if ($file === '') return '';
    if (stripos($file, 'http://') === 0 || stripos($file, 'https://') === 0) return $file;
    return sfdocument . 'Taskdocument/' . rawurlencode($file);
}

/**
 * Whether this searcher may pull PO documents at all.
 *
 * The task list gates its PO button on the *task's* department being 9, 10
 * or 20; a DF search hit has no task, so the nearest honest equivalent is
 * the searcher's own department. A PO carries order value and party terms,
 * so this stays a department test rather than falling open to everyone who
 * can see the DF.
 */
private function _gs_may_download_po($me)
{
    if ((int) $me['id'] === 139) return true;  // Shubham Sir - Management, outside the three departments
    return in_array((int) $me['department'], array(9, 10, 20), true);
}

/** delegation_task.urgency: 1 high, 2 medium, 3 low. */
private function _gs_urgency($u)
{
    $map = array(1 => 'High', 2 => 'Medium', 3 => 'Low');
    $u = (int) $u;
    return isset($map[$u]) ? $map[$u] : '';
}

/**
 * One row of the result list. `code` is the record's human identifier
 * (DF number, opportunity number, case number) and is what ranking
 * rewards an exact hit on; `open` carries the ids the app needs to
 * route into the existing detail screen.
 */
private function _gs_hit($type, $id, $code, $title, $subtitle, $meta, $status, $date, $open = array())
{
    return array(
        'type'     => $type,
        'id'       => (int) $id,
        'code'     => (string) $code,
        'title'    => (string) $title,
        'subtitle' => (string) $subtitle,
        'meta'     => (string) $meta,
        'status'   => (string) $status,
        'date'     => (string) $date,
        'open'     => $open,
    );
}

/** Exact code hits first, then prefix, then anything containing the term. */
private function _gs_rank($hits, $term)
{
    $t = strtolower(trim($term));

    foreach ($hits as $i => $h) {
        $code = strtolower(trim($h['code']));
        $hay  = strtolower(trim($h['title'] . ' ' . $h['subtitle'] . ' ' . $h['meta']));

        if ($code !== '' && $code === $t)            $score = 100;
        elseif ($code !== '' && strpos($code, $t) === 0) $score = 80;
        elseif (strpos($hay, $t) === 0)              $score = 60;
        elseif (strpos($hay, ' ' . $t) !== false)    $score = 40;
        elseif (strpos($hay, $t) !== false)          $score = 20;
        else                                         $score = 10;

        $hits[$i]['score'] = $score;
        $hits[$i]['_pos']  = $i;
    }

    usort($hits, function ($a, $b) {
        if ($a['score'] === $b['score']) return $a['_pos'] - $b['_pos'];
        return $b['score'] - $a['score'];
    });

    foreach ($hits as $i => $h) unset($hits[$i]['_pos']);

    return array_values($hits);
}

/* ------------------------------------------------------------------ *
 * DF task board - task_department_wise_scheduling
 * ------------------------------------------------------------------ */
private function _gs_df($me, $term, $limit)
{
    $this->db->select("
        a.id, a.df_id, a.taskid, a.department_id, a.assigned_user,
        a.start_date, a.end_date, a.task_status, a.on_hold, a.remarks,
        b.df_no, b.df_description,
        c.task_name,
        d.department,
        p.pono, p.company_name,
        TRIM(CONCAT(COALESCE(f.first_name, ''), ' ', COALESCE(f.last_name, ''))) AS assigned_name
    ", false);
    $this->db->from('task_department_wise_scheduling a');
    $this->db->join('df_release b', 'a.df_id = b.id', 'left');
    $this->db->join('task_management c', 'a.taskid = c.task_id', 'left');
    $this->db->join('departments d', 'a.department_id = d.department_id', 'left');
    $this->db->join('system_users f', 'a.assigned_user = f.user_id', 'left');
    $this->db->join('poreceived p', 'a.po_id = p.id', 'left');

    /* A team leader is scoped to the departments they lead. If that list
       comes back empty they are treated as an ordinary user rather than
       being left unfiltered - an empty IN () must not mean "everything". */
    if (!$me['all_df']) {
        if ($me['user_type'] === 2 && count($me['led_departments']) > 0) {
            $this->db->where_in('a.department_id', $me['led_departments']);
        } else {
            $this->db->where('a.assigned_user', $me['id']);
        }
    }

    $this->db->group_start()
        ->like('b.df_no', $term)
        ->or_like('b.df_description', $term)
        ->or_like('c.task_name', $term)
        ->or_like('p.pono', $term)
        ->or_like('p.company_name', $term)
        ->or_like('a.remarks', $term)
        ->or_like('f.first_name', $term)
        ->or_like('f.last_name', $term)
        ->group_end();

    $this->db->group_by('a.id');
    $this->db->order_by('a.end_date', 'DESC');
    $this->db->limit($limit);

    $hits = array();
    foreach ($this->db->get()->result() as $r) {

        if ((int) $r->on_hold === 1)          $status = 'On Hold';
        elseif ((int) $r->task_status !== 0)  $status = 'Completed';
        elseif ($r->end_date !== '' && $r->end_date < date('Y-m-d')) $status = 'Overdue';
        else                                  $status = 'Ongoing';

        $df = trim((string) $r->df_no);
        if ($df === '' || $df === '-') $df = trim((string) $r->pono);

        $hits[] = $this->_gs_hit(
            'df',
            $r->id,
            $df,
            trim((string) $r->task_name) !== '' ? $r->task_name : 'DF Task',
            trim(($df !== '' ? $df . ' | ' : '') . (string) $r->company_name),
            trim((string) $r->department . ($r->assigned_name !== '' ? ' | ' . $r->assigned_name : '')),
            $status,
            $this->_gs_date($r->end_date),
            array(
                'task_record_id' => (int) $r->id,
                'df_id'          => (int) $r->df_id,
                'task_id'        => (int) $r->taskid,
                'department_id'  => (int) $r->department_id,
            )
        );
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * Marketing leads - leads
 * ------------------------------------------------------------------ */
private function _gs_lead($me, $term, $limit)
{
    $this->db->select("
        b.id AS lead_id, b.unique_id, b.create_date, b.added_on, b.closed,
        b.customer_name, b.contact_no, b.email, b.contact_person,
        b.email_id, b.mobile_no, b.subject, b.added_by,
        c.id AS customer_id, c.company_name,
        c.customer_name AS customer_contact, c.contact_no AS customer_phone,
        TRIM(CONCAT(COALESCE(su.first_name, ''), ' ', COALESCE(su.last_name, ''))) AS owner_name,
        (
            SELECT pr.lead_status FROM progress_remarks pr
            WHERE pr.lead_id = b.id ORDER BY pr.id DESC LIMIT 1
        ) AS stage_id
    ", false);
    $this->db->from('leads b');
    $this->db->join('customer_detail c', 'c.id = b.company_name', 'left');
    $this->db->join('system_users su', 'su.user_id = b.added_by', 'left');

    if (!$me['all_lead']) {
        $this->db->where('b.added_by', $me['id']);
    }

    $this->db->group_start()
        ->like('b.unique_id', $term)
        ->or_like('c.company_name', $term)
        ->or_like('b.customer_name', $term)
        ->or_like('b.contact_person', $term)
        ->or_like('b.contact_no', $term)
        ->or_like('b.mobile_no', $term)
        ->or_like('b.email', $term)
        ->or_like('b.email_id', $term)
        ->or_like('b.subject', $term)
        ->or_like('c.customer_name', $term)
        ->or_like('c.contact_no', $term)
        ->group_end();

    $this->db->order_by('b.id', 'DESC');
    $this->db->limit($limit);

    $rows   = $this->db->get()->result();
    $stages = $this->_gs_lead_stages();

    $hits = array();
    foreach ($rows as $r) {
        $sid   = (int) $r->stage_id;
        $stage = isset($stages[$sid]) ? $stages[$sid] : '';

        $contact = trim((string) $r->customer_name);
        if ($contact === '') $contact = trim((string) $r->contact_person);
        if ($contact === '') $contact = trim((string) $r->customer_contact);

        $phone = trim((string) $r->contact_no);
        if ($phone === '') $phone = trim((string) $r->mobile_no);
        if ($phone === '') $phone = trim((string) $r->customer_phone);

        $hits[] = $this->_gs_hit(
            'lead',
            $r->lead_id,
            (string) $r->unique_id,
            trim((string) $r->company_name) !== '' ? $r->company_name : $contact,
            trim($contact . ($phone !== '' ? ' | ' . $phone : '')),
            trim((string) $r->owner_name . ($stage !== '' ? ' | ' . $stage : '')),
            ((int) $r->closed === 1 ? 'Closed' : ($stage !== '' ? $stage : 'Open')),
            $this->_gs_date($r->create_date),
            array('lead_id' => (int) $r->lead_id, 'customer_id' => (int) $r->customer_id)
        );
    }

    return $hits;
}

/** Stage id => name. Small reference table, read once per request. */
private function _gs_lead_stages()
{
    $map = array();
    $q = $this->db->select('lead_id, lead_name')->from('lead_stage')->get();
    foreach ($q->result() as $r) $map[(int) $r->lead_id] = (string) $r->lead_name;
    return $map;
}

/* ------------------------------------------------------------------ *
 * Customer master - customer_detail
 * ------------------------------------------------------------------ */
private function _gs_customer($me, $term, $limit)
{
    $this->db->select("
        a.id, a.company_name, a.customer_name, a.customer_alias, a.contact_no,
        a.alt_contact, a.email, a.city, a.gst, a.status, a.added_by, a.assigned_to,
        a.added_on
    ", false);
    $this->db->from('customer_detail a');

    /* A customer belongs to whoever created it or is assigned to it. */
    if (!$me['all_customer']) {
        $this->db->group_start()
            ->where('a.added_by', $me['id'])
            ->or_where('a.assigned_to', $me['id'])
            ->group_end();
    }

    $this->db->group_start()
        ->like('a.company_name', $term)
        ->or_like('a.customer_name', $term)
        ->or_like('a.customer_alias', $term)
        ->or_like('a.contact_no', $term)
        ->or_like('a.alt_contact', $term)
        ->or_like('a.email', $term)
        ->or_like('a.gst', $term)
        ->or_like('a.city', $term)
        ->group_end();

    $this->db->order_by('a.company_name', 'ASC');
    $this->db->limit($limit);

    $hits = array();
    foreach ($this->db->get()->result() as $r) {
        $hits[] = $this->_gs_hit(
            'customer',
            $r->id,
            trim((string) $r->gst),
            trim((string) $r->company_name),
            trim((string) $r->customer_name . (trim((string) $r->contact_no) !== '' ? ' | ' . $r->contact_no : '')),
            trim((string) $r->city . (trim((string) $r->email) !== '' ? ' | ' . $r->email : '')),
            ((int) $r->status === 1 ? 'Active' : 'Inactive'),
            $this->_gs_date($r->added_on),
            array('customer_id' => (int) $r->id)
        );
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * Service CRM - service_opportunities
 * ------------------------------------------------------------------ */
private function _gs_service($me, $term, $limit)
{
    $this->db->select("
        so.opportunity_id, so.op_no, so.op_date, so.op_type, so.customer_id,
        so.customer_name, so.customer_contact_no, so.customer_table_origin,
        so.marketing_person_id, so.current_stage_id,
        IFNULL(cm_spares.company_name, cm_marketing.company_name) AS company_name,
        sls.stage_name,
        TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS owner_name
    ", false);
    $this->db->from('service_opportunities so');
    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
    $this->db->join('service_lead_stages sls', 'sls.stage_id = so.current_stage_id', 'left');

    if (!$me['all_service']) {
        $this->db->where('so.marketing_person_id', $me['id']);
    }

    $this->db->group_start()
        ->like('so.op_no', $term)
        ->or_like('so.customer_name', $term)
        ->or_like('so.customer_contact_no', $term)
        ->or_like('cm_spares.company_name', $term)
        ->or_like('cm_marketing.company_name', $term)
        ->or_like('cm_spares.contact_person', $term)
        ->or_like('cm_marketing.customer_name', $term)
        ->group_end();

    $this->db->order_by('so.opportunity_id', 'DESC');
    $this->db->limit($limit);

    $hits = array();
    foreach ($this->db->get()->result() as $r) {
        $hits[] = $this->_gs_hit(
            'service',
            $r->opportunity_id,
            (string) $r->op_no,
            trim((string) $r->company_name) !== '' ? $r->company_name : (string) $r->customer_name,
            trim((string) $r->op_no . (trim((string) $r->customer_contact_no) !== '' ? ' | ' . $r->customer_contact_no : '')),
            trim((string) $r->owner_name),
            (string) $r->stage_name,
            $this->_gs_date($r->op_date),
            array(
                'opportunity_id' => (int) $r->opportunity_id,
                'customer_id'    => (int) $r->customer_id,
                'stage_id'       => (int) $r->current_stage_id,
            )
        );
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * Spare CRM - opportunities
 * ------------------------------------------------------------------ */
private function _gs_spare($me, $term, $limit)
{
    $this->db->select("
        op.opportunity_id, op.op_no, op.op_date, op.op_type, op.customer_id,
        op.customer_contact_no, op.customer_email, op.marketing_person_id, op.status,
        cm.company_name, cm.contact_person,
        TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS owner_name
    ", false);
    $this->db->from('opportunities op');
    $this->db->join('spares_customers cm', 'cm.customer_id = op.customer_id', 'left');
    $this->db->join('system_users u', 'u.user_id = op.marketing_person_id', 'left');

    if (!$me['all_spare']) {
        $this->db->where('op.marketing_person_id', $me['id']);
    }

    $this->db->group_start()
        ->like('op.op_no', $term)
        ->or_like('cm.company_name', $term)
        ->or_like('cm.contact_person', $term)
        ->or_like('op.customer_contact_no', $term)
        ->or_like('op.customer_email', $term)
        ->group_end();

    $this->db->order_by('op.opportunity_id', 'DESC');
    $this->db->limit($limit);

    $hits = array();
    foreach ($this->db->get()->result() as $r) {
        $hits[] = $this->_gs_hit(
            'spare',
            $r->opportunity_id,
            (string) $r->op_no,
            trim((string) $r->company_name),
            trim((string) $r->op_no . (trim((string) $r->contact_person) !== '' ? ' | ' . $r->contact_person : '')),
            trim((string) $r->owner_name . (trim((string) $r->customer_contact_no) !== '' ? ' | ' . $r->customer_contact_no : '')),
            '',
            $this->_gs_date($r->op_date),
            array('opportunity_id' => (int) $r->opportunity_id, 'customer_id' => (int) $r->customer_id)
        );
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * Spare customer master - spares_customers
 *
 * The table carries no owner column, so a non-admin is scoped to the
 * customers that appear on their own spare opportunities.
 * ------------------------------------------------------------------ */
private function _gs_spare_customer($me, $term, $limit)
{
    $this->db->select('cm.customer_id, cm.company_name, cm.contact_person, cm.contact_person_no, cm.email, cm.address, cm.tax_number, cm.status, cm.created_at');
    $this->db->from('spares_customers cm');
    $this->db->where('cm.status', 1);

    if (!$me['all_spare']) {
        $this->db->where(
            'cm.customer_id IN (SELECT o.customer_id FROM opportunities o WHERE o.marketing_person_id = ' . (int) $me['id'] . ')',
            null,
            false
        );
    }

    $this->db->group_start()
        ->like('cm.company_name', $term)
        ->or_like('cm.contact_person', $term)
        ->or_like('cm.contact_person_no', $term)
        ->or_like('cm.email', $term)
        ->or_like('cm.tax_number', $term)
        ->group_end();

    $this->db->order_by('cm.company_name', 'ASC');
    $this->db->limit($limit);

    $hits = array();
    foreach ($this->db->get()->result() as $r) {
        $hits[] = $this->_gs_hit(
            'spare_customer',
            $r->customer_id,
            trim((string) $r->tax_number),
            trim((string) $r->company_name),
            trim((string) $r->contact_person . (trim((string) $r->contact_person_no) !== '' ? ' | ' . $r->contact_person_no : '')),
            trim((string) $r->email),
            'Active',
            $this->_gs_date($r->created_at),
            array('customer_id' => (int) $r->customer_id)
        );
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * Task Management - reuses Task_management_model's own keyword filter
 * ------------------------------------------------------------------ */
private function _gs_task($me, $term, $limit)
{
    $model = $this->_tm_model();
    if (!$model->module_ready()) return array();

    $rows = array();

    if ($me['all_task']) {
        $rows = (array) $model->get_all_tasks(array('keyword' => $term), $limit);
    } else {
        /* The model filters on one side at a time, so ask twice and merge -
           a person may see a task either because they raised it or because
           it landed on them. */
        $mine = (array) $model->get_all_tasks(
            array('keyword' => $term, 'assigned_to_user_id' => $me['id']), $limit
        );
        $raised = (array) $model->get_all_tasks(
            array('keyword' => $term, 'assigned_by_user_id' => $me['id']), $limit
        );

        $seen = array();
        foreach (array_merge($mine, $raised) as $r) {
            $tid = (int) (isset($r['id']) ? $r['id'] : 0);
            if ($tid > 0 && isset($seen[$tid])) continue;
            $seen[$tid] = true;
            $rows[] = $r;
        }
        $rows = array_slice($rows, 0, $limit);
    }

    $hits = array();
    foreach ($rows as $r) {
        $t = $this->_tm_shape($r, $me['id']);

        $hits[] = $this->_gs_hit(
            'task',
            isset($t['id']) ? $t['id'] : 0,
            isset($t['task_code']) ? $t['task_code'] : '',
            isset($t['title']) ? $t['title'] : '',
            trim(
                (isset($t['assigned_to_name']) ? (string) $t['assigned_to_name'] : '') .
                (isset($t['assigned_by_name']) && $t['assigned_by_name'] !== '' ? ' | by ' . $t['assigned_by_name'] : '')
            ),
            isset($t['department']) ? (string) $t['department'] : '',
            isset($t['status_label']) ? (string) $t['status_label'] : (isset($t['status']) ? (string) $t['status'] : ''),
            $this->_gs_date(isset($t['due_date']) ? $t['due_date'] : ''),
            array('task_id' => (int) (isset($t['id']) ? $t['id'] : 0))
        );
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * Delegation - delegation_task
 * ------------------------------------------------------------------ */
private function _gs_delegation($me, $term, $limit)
{
    $today = date('Y-m-d');

    $this->_dlg_select();

    /* Outside of an admin, a delegation is visible to the two people on
       it - the one who raised it and the one it went to. */
    if (!$me['all_deleg']) {
        $this->db->group_start()
            ->where('a.yourname', $me['id'])
            ->or_where('a.delegate_to', $me['id'])
            ->group_end();
    }

    $this->db->group_start()
        ->like('a.task', $term)
        ->or_like('a.case_no', $term)
        ->or_like('c.first_name', $term)
        ->or_like('c.last_name', $term)
        ->or_like('d.first_name', $term)
        ->or_like('d.last_name', $term)
        ->group_end();

    $this->db->order_by('a.id', 'DESC');
    $this->db->limit($limit);

    $hits = array();
    foreach ($this->db->get()->result() as $r) {
        $task = $this->_dlg_decorate($r, $today, false);

        $hits[] = $this->_gs_hit(
            'delegation',
            $r->id,
            (string) $r->case_no,
            isset($task['task']) ? (string) $task['task'] : (string) $r->task,
            trim(
                'To ' . $this->_gs_name($r->to_first, $r->to_last) .
                ' | by ' . $this->_gs_name($r->by_first, $r->by_last)
            ),
            $this->_gs_urgency(isset($task['urgency']) ? $task['urgency'] : $r->urgency),
            isset($task['status_label']) ? (string) $task['status_label'] : ((int) $r->task_status === 0 ? 'Open' : 'Done'),
            $this->_gs_date(isset($task['due_date']) ? $task['due_date'] : $r->targetdate),
            array('delegation_id' => (int) $r->id)
        );
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * Engineer visits - service_engineer_visits
 *
 * Scoped exactly as visit_list_api scopes it, then matched in PHP over
 * the same decorated rows the visit screen already renders.
 * ------------------------------------------------------------------ */
private function _gs_visit($me, $term, $limit)
{
    $model = $this->_visit_model();

    $scope = array();
    if (!$me['all_visit']) {
        $scope['engineer_id'] = $me['id'];
    }

    $needle = strtolower($term);
    $hits   = array();

    foreach ((array) $model->get_visit_rows($scope) as $v) {

        $hay = strtolower(implode(' ', array(
            (string) (isset($v->op_no) ? $v->op_no : ''),
            (string) (isset($v->customer_name) ? $v->customer_name : ''),
            (string) (isset($v->customer_contact_name) ? $v->customer_contact_name : ''),
            (string) (isset($v->customer_contact_no) ? $v->customer_contact_no : ''),
            (string) (isset($v->engineer_full_name) ? $v->engineer_full_name : ''),
            (string) (isset($v->visit_type) ? $v->visit_type : ''),
            (string) (isset($v->remarks) ? $v->remarks : ''),
        )));

        if (strpos($hay, $needle) === false) continue;

        $hits[] = $this->_gs_hit(
            'visit',
            isset($v->visit_id) ? $v->visit_id : 0,
            (string) (isset($v->op_no) ? $v->op_no : ''),
            (string) (isset($v->customer_name) ? $v->customer_name : ''),
            trim(
                (string) (isset($v->visit_type) ? $v->visit_type : '') .
                (isset($v->engineer_full_name) && $v->engineer_full_name !== '' ? ' | ' . $v->engineer_full_name : '')
            ),
            (string) (isset($v->customer_contact_no) ? $v->customer_contact_no : ''),
            (string) (isset($v->visit_status) ? $v->visit_status : ''),
            $this->_gs_date(isset($v->start_date) ? $v->start_date : ''),
            array(
                'visit_id'       => (int) (isset($v->visit_id) ? $v->visit_id : 0),
                'opportunity_id' => (int) (isset($v->opportunity_id) ? $v->opportunity_id : 0),
            )
        );

        if (count($hits) >= $limit) break;
    }

    return $hits;
}

/* ------------------------------------------------------------------ *
 * 1. SCOPES - what this user is allowed to search
 * ------------------------------------------------------------------ */
public function global_search_scopes_api()
{
    try {
        $in = $this->_gs_input();
        $me = $this->_gs_scope($in);
        if (!$me) return $this->_gs_fail('auth');

        $sections = array();
        foreach ($this->_gs_sections($me) as $key => $s) {
            if (!$s['on']) continue;
            $sections[] = array(
                'type'  => $key,
                'label' => $s['label'],
                'scope' => $this->_gs_section_scope($me, $key),
            );
        }

        $this->_gs_out(array(
            'status'    => true,
            'is_admin'  => (bool) $me['super'],
            'user_type' => $me['user_type'],
            'sections'  => $sections,
        ));
    } catch (Throwable $e) {
        $this->_gs_fail('Search scopes failed: ' . $e->getMessage());
    }
}

/** "all", "team" or "own" - what the app shows under the search box. */
private function _gs_section_scope($me, $type)
{
    switch ($type) {
        case 'df':
        case 'df_record':
            if ($me['all_df']) return 'all';
            return ($me['user_type'] === 2 && count($me['led_departments']) > 0) ? 'team' : 'own';
        case 'lead':           return $me['all_lead']     ? 'all' : 'own';
        case 'customer':       return $me['all_customer'] ? 'all' : 'own';
        case 'service':        return $me['all_service']  ? 'all' : 'own';
        case 'spare':
        case 'spare_customer': return $me['all_spare']    ? 'all' : 'own';
        case 'visit':          return $me['all_visit']    ? 'all' : 'own';
        case 'task':           return $me['all_task']     ? 'all' : 'own';
        case 'delegation':     return $me['all_deleg']    ? 'all' : 'own';
    }
    return 'own';
}

/* ------------------------------------------------------------------ *
 * 2. SEARCH - one term across every section this user may see
 * ------------------------------------------------------------------ */
public function global_search_api()
{
    try {
        $in = $this->_gs_input();
        $me = $this->_gs_scope($in);
        if (!$me) return $this->_gs_fail('auth');

        date_default_timezone_set('Asia/Kolkata');

        $term = '';
        foreach (array('query', 'q', 'search', 'keyword', 'term') as $k) {
            if (isset($in[$k]) && trim((string) $in[$k]) !== '') {
                $term = trim((string) $in[$k]);
                break;
            }
        }

        if (strlen($term) < 2) {
            return $this->_gs_out(array(
                'status'   => true,
                'query'    => $term,
                'message'  => 'Type at least 2 characters to search.',
                'total'    => 0,
                'counts'   => new stdClass(),
                'sections' => array(),
                'results'  => array(),
                'is_admin' => (bool) $me['super'],
            ));
        }

        $limit = (int) (isset($in['limit']) ? $in['limit'] : 8);
        if ($limit <= 0 || $limit > 50) $limit = 8;

        /* An optional type filter narrows the search to one or more
           sections; it can never unlock a section the user lacks. */
        $wanted = array();
        $raw_type = isset($in['type']) ? $in['type'] : (isset($in['types']) ? $in['types'] : '');
        if (is_array($raw_type)) {
            foreach ($raw_type as $t) $wanted[] = strtolower(trim((string) $t));
        } elseif (trim((string) $raw_type) !== '') {
            foreach (explode(',', (string) $raw_type) as $t) $wanted[] = strtolower(trim($t));
        }
        $wanted = array_values(array_filter($wanted));
        if (in_array('all', $wanted)) $wanted = array();

        $runner = array(
            'df_record'      => '_gs_df_record',
            'df'             => '_gs_df',
            'task'           => '_gs_task',
            'delegation'     => '_gs_delegation',
            'lead'           => '_gs_lead',
            'customer'       => '_gs_customer',
            'service'        => '_gs_service',
            'spare'          => '_gs_spare',
            'spare_customer' => '_gs_spare_customer',
            'visit'          => '_gs_visit',
        );

        $sections = array();
        $counts   = array();
        $flat     = array();

        foreach ($this->_gs_sections($me) as $key => $s) {

            if (!$s['on']) continue;
            if (count($wanted) > 0 && !in_array($key, $wanted)) continue;

            $method = $runner[$key];

            /* One broken section must not take the whole search down. */
            try {
                $hits = $this->$method($me, $term, $limit);
            } catch (Throwable $e) {
                $this->db->reset_query();
                continue;
            }

            $hits = $this->_gs_rank((array) $hits, $term);

            $counts[$key] = count($hits);
            if (count($hits) === 0) continue;

            $sections[] = array(
                'type'    => $key,
                'label'   => $s['label'],
                'scope'   => $this->_gs_section_scope($me, $key),
                'count'   => count($hits),
                'results' => $hits,
            );

            foreach ($hits as $h) $flat[] = $h;
        }

        $flat = $this->_gs_rank($flat, $term);

        $this->_gs_out(array(
            'status'    => true,
            'query'     => $term,
            'is_admin'  => (bool) $me['super'],
            'user_type' => $me['user_type'],
            'total'     => count($flat),
            'counts'    => (object) $counts,
            'sections'  => $sections,
            'results'   => $flat,
        ));

    } catch (Throwable $e) {
        $this->_gs_fail('Search failed: ' . $e->getMessage());
    }
}


/* ==================================================================
 * UPDATE BROADCAST
 *
 * The in-app update gate ships inside the APK, so every build older
 * than the one that introduced it can never prompt for itself. This
 * reaches those phones through the two channels an old build already
 * has: a row in app_notifications, which its bell renders and counts
 * as unread, and an FCM message carrying a real notification block,
 * which the OS draws with no app code at all.
 *
 * Neither needs the phone to be updated first, and neither can crash
 * an old build: its notification screen colours an unknown type by a
 * default case and its tap handler only marks the row read.
 *
 * Admin only, and dry by default - call it with dry_run false once
 * the counts look right.
 * ================================================================== */

private function _bc_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) return $decoded;
    }
    return $_POST;
}

private function _bc_out($payload)
{
    echo json_encode($payload);
}

/** Truthy for true/1/"true"/"yes"; anything else is false. */
private function _bc_flag($v, $default = false)
{
    if ($v === null) return $default;
    if (is_bool($v)) return $v;
    $s = strtolower(trim((string) $v));
    if ($s === '') return $default;
    return in_array($s, array('1', 'true', 'yes', 'y', 'on'), true);
}

public function broadcast_update_notice_api()
{
    try {
        $in = $this->_bc_input();

        $me   = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
        $role = (int) (isset($in['role_id']) ? $in['role_id'] : 0);

        if ($me <= 0) {
            return $this->_bc_out(array('status' => false, 'message' => 'auth'));
        }

        /* Sending to every phone in the company is not something an
           ordinary account gets to do. */
        $is_admin = ((int) $this->getUserType($role, $me) === 1)
            || in_array($me, array(61, 139, 161));

        if (!$is_admin) {
            return $this->_bc_out(array('status' => false, 'message' => 'forbidden'));
        }

        date_default_timezone_set('Asia/Kolkata');

        /* Dry by default: a broadcast is not undoable, so it has to be
           asked for explicitly rather than fired by a stray call. */
        $dry = $this->_bc_flag(isset($in['dry_run']) ? $in['dry_run'] : null, true);

        $url = trim((string) (isset($in['url']) ? $in['url'] : ''));
        if ($url === '') $url = 'https://pms.shubhampack.in/App';

        $title = trim((string) (isset($in['title']) ? $in['title'] : ''));
        if ($title === '') $title = 'App update available';

        $message = trim((string) (isset($in['message']) ? $in['message'] : ''));
        if ($message === '') {
            $message = 'A new version of the Shubham Pack app is ready. '
                . 'Open ' . $url . ' on this phone to download and install it.';
        }

        /* A single-recipient test run, so the first real send is not the
           first time this code has ever delivered anything. */
        $only = (int) (isset($in['only_user_id']) ? $in['only_user_id'] : 0);

        /* ---------------- recipients ---------------- */
        $this->db->select('user_id')
            ->from('system_users')
            ->where('user_status', '1')
            ->where('hide_profile', '0');

        if ($only > 0) $this->db->where('user_id', $only);

        $users = $this->db->get()->result_array();

        $user_ids = array();
        foreach ($users as $u) {
            $id = (int) $u['user_id'];
            if ($id > 0) $user_ids[] = $id;
        }

        /* ---------------- devices ---------------- */
        $this->db->select('user_id, fcm_token')
            ->from('user_devices')
            ->where('fcm_token !=', '');

        if ($only > 0) $this->db->where('user_id', $only);

        $devices = $this->db->get()->result_array();

        /* Two phones can carry the same token after a reinstall; sending
           twice to one device is just a duplicate banner. */
        $tokens = array();
        foreach ($devices as $d) {
            $t = trim((string) $d['fcm_token']);
            if ($t !== '') $tokens[$t] = true;
        }
        $tokens = array_keys($tokens);

        if ($dry) {
            return $this->_bc_out(array(
                'status'   => true,
                'dry_run'  => true,
                'message'  => 'Nothing was sent. Call again with dry_run false to send.',
                'users'    => count($user_ids),
                'devices'  => count($tokens),
                'title'    => $title,
                'body'     => $message,
                'url'      => $url,
            ));
        }

        /* ---------------- in-app bell ----------------
           Reaches everyone who opens the app, with or without a push
           token, because get_notifications and get_unread_count read
           this table on every launch. */
        $inserted = 0;
        foreach ($user_ids as $uid) {
            $this->InsertAppNotification(
                $uid, $title, $message, 'app_update', 0
            );
            $inserted++;
        }

        /* ---------------- push ---------------- */
        $sent = 0;
        $failed = 0;

        foreach ($tokens as $t) {
            try {
                sendFCM($t, $title, $message, array(
                    'type' => 'app_update',
                    'url'  => $url,
                ));
                $sent++;
            } catch (Throwable $e) {
                /* one dead token must not stop the rest */
                $failed++;
            }
        }

        $this->_bc_out(array(
            'status'        => true,
            'dry_run'       => false,
            'notifications' => $inserted,
            'pushed'        => $sent,
            'push_failed'   => $failed,
            'users'         => count($user_ids),
            'devices'       => count($tokens),
            'title'         => $title,
            'body'          => $message,
        ));

    } catch (Throwable $e) {
        $this->_bc_out(array(
            'status'  => false,
            'message' => 'Broadcast failed: ' . $e->getMessage(),
        ));
    }
}


/* ==================================================================
 * DF RECORD - the DF itself, not the tasks hanging off it
 *
 * The df section of the search returns task rows, which answers
 * "which of my tasks mention this DF". Searching a DF number to see
 * how that DF is doing is a different question, so it gets its own
 * section and its own detail endpoint - a phone-sized version of
 * Dashboard/df_full_detail, built from the same tables and the same
 * delay expression, without the desktop report's bulk.
 * ================================================================== */

/**
 * Which DF ids this user may look at, or null for "any".
 *
 * Same rule the df task section uses: an admin sees every DF, a team
 * leader sees the DFs their departments are working on, and everyone
 * else sees the DFs they personally hold a task on. Returning the id
 * list rather than a boolean keeps the search and the detail endpoint
 * agreeing with each other.
 */
private function _gs_df_scope_ids($me)
{
    if (!empty($me['all_df'])) return null;

    $this->db->distinct();
    $this->db->select('df_id')->from('task_department_wise_scheduling');

    if ((int) $me['user_type'] === 2 && count($me['led_departments']) > 0) {
        $this->db->where_in('department_id', $me['led_departments']);
    } else {
        $this->db->where('assigned_user', $me['id']);
    }

    $this->db->where('df_id >', 0);

    $ids = array();
    foreach ($this->db->get()->result() as $r) $ids[] = (int) $r->df_id;

    return $ids;
}

/** One row per DF, for the search's DF chip. */
private function _gs_df_record($me, $term, $limit)
{
    $allowed = $this->_gs_df_scope_ids($me);

    /* An empty allow-list means this person holds no DF tasks at all -
       which is no DFs, never all of them. */
    if (is_array($allowed) && count($allowed) === 0) {
        return array();
    }

    $this->db->select("
        df.id, df.df_no, df.df_description, df.df_status, df.df_upload,
        IFNULL(df.on_hold, 0) AS on_hold, df.added_on, df.completed_on,
        (
            SELECT COUNT(1) FROM task_department_wise_scheduling t
            WHERE t.df_id = df.id AND t.taskid > 0
        ) AS task_total,
        (
            SELECT COUNT(1) FROM task_department_wise_scheduling t
            WHERE t.df_id = df.id AND t.taskid > 0 AND t.task_status = 1
        ) AS task_done,
        (
            SELECT p.company_name FROM poreceived p
            WHERE p.df_id = df.id ORDER BY p.id DESC LIMIT 1
        ) AS company_name,
        (
            SELECT p.po_attachment FROM poreceived p
            WHERE p.df_id = df.id ORDER BY p.id DESC LIMIT 1
        ) AS po_attachment
    ", false);
    $this->db->from('df_release df');

    if (is_array($allowed)) {
        $this->db->where_in('df.id', $allowed);
    }

    $this->db->group_start()
        ->like('df.df_no', $term)
        ->or_like('df.df_description', $term)
        ->group_end();

    $this->db->order_by('df.df_sr_no', 'DESC');
    $this->db->limit($limit);

    $may_po = $this->_gs_may_download_po($me);

    $hits = array();
    foreach ($this->db->get()->result() as $r) {

        $total = (int) $r->task_total;
        $done  = (int) $r->task_done;
        $pct   = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        if ((int) $r->on_hold === 1)        $status = 'On Hold';
        elseif ((int) $r->df_status === 1)  $status = 'Closed';
        else                                $status = 'Active';

        $hits[] = $this->_gs_hit(
            'df_record',
            $r->id,
            (string) $r->df_no,
            'DF-' . $r->df_no,
            trim((string) $r->df_description),
            trim((string) $r->company_name . ($total > 0 ? '  |  ' . $done . '/' . $total . ' tasks (' . $pct . '%)' : '')),
            $status,
            $this->_gs_date($r->added_on),
            array(
                'df_id'           => (int) $r->id,
                'df_download_url' => $this->_gs_df_file_url($r->df_upload),
                'po_download_url' => $may_po
                    ? $this->_gs_po_file_url($r->po_attachment)
                    : '',
            )
        );
    }

    return $hits;
}

/**
 * Everything the DF detail screen draws: the DF, its PO, how its tasks
 * are tracking, and where the delay sits.
 */
public function df_detail_api()
{
    try {
        $in = $this->_gs_input();
        $me = $this->_gs_scope($in);
        if (!$me) return $this->_gs_fail('auth');

        date_default_timezone_set('Asia/Kolkata');

        $df_id = (int) (isset($in['df_id']) ? $in['df_id'] : 0);
        if ($df_id <= 0) return $this->_gs_fail('df_id is required');

        $allowed = $this->_gs_df_scope_ids($me);
        if (is_array($allowed) && !in_array($df_id, $allowed, true)) {
            return $this->_gs_fail('forbidden');
        }

        /* ---------------- the DF ---------------- */
        $df = $this->db->select("
                df.id, df.df_no, df.df_description, df.df_upload, df.added_on,
                df.completed_on, df.df_status, IFNULL(df.on_hold, 0) AS on_hold,
                TRIM(CONCAT(COALESCE(c.first_name,''),' ',COALESCE(c.last_name,''))) AS released_by
            ", false)
            ->from('df_release df')
            ->join('system_users c', 'df.added_by = c.user_id', 'left')
            ->where('df.id', $df_id)
            ->get()
            ->row_array();

        if (empty($df)) return $this->_gs_fail('DF not found');

        $df['status_text'] = ((int) $df['on_hold'] === 1)
            ? 'On Hold'
            : (((int) $df['df_status'] === 1) ? 'Closed' : 'Active');

        $df['df_download_url'] = $this->_gs_df_file_url(
            isset($df['df_upload']) ? $df['df_upload'] : ''
        );

        /* ---------------- its PO ---------------- */
        $po = $this->db->select("
                po.pono, po.podate, po.order_value AS po_value,
                po.company_name AS party_name,
                IFNULL(po.basic_machine, '') AS machine_name,
                IFNULL(po.customer_currency, 'INR') AS currency,
                IFNULL(po.po_attachment, '') AS po_attachment,
                TRIM(CONCAT(COALESCE(m.first_name,''),' ',COALESCE(m.last_name,''))) AS marketing_person
            ", false)
            ->from('poreceived po')
            ->join('system_users m', 'po.added_by = m.user_id', 'left')
            ->where('po.df_id', $df_id)
            ->order_by('po.id', 'DESC')
            ->get()
            ->row_array();

        /* The ORDER card names the PO but could not open it. Same rule as
           the search hit's button - see _gs_may_download_po - so a DF
           reached by tapping a result and one reached any other way agree
           on who may have the document. The raw filename does not go out;
           only the url, and only when it is allowed. */
        if ($po) {
            $po['po_download_url'] = $this->_gs_may_download_po($me)
                ? $this->_gs_po_file_url($po['po_attachment'])
                : '';
            unset($po['po_attachment']);
        }

        /* ---------------- its tasks ----------------
           The delay expression is lifted verbatim from
           Dashboard::get_df_details so both screens call a task late on
           exactly the same terms. */
        $this->db->select("
            tdws.id AS task_record_id, tdws.department_id, tdws.task_status,
            IFNULL(tdws.on_hold, 0) AS on_hold,
            tdws.start_date, tdws.end_date, tdws.task_completed_on,
            IFNULL(tdws.remarks, '') AS remarks,
            IFNULL(d.department, '') AS department,
            IFNULL(task.task_name, '') AS task_name,
            IFNULL(task.sortorder, 0) AS sortorder,
            TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS responsible_person,
            (
                SELECT COUNT(cts.id) FROM communication_ticket_system cts
                WHERE cts.task_record_id = tdws.id AND cts.ticket_status = 0
            ) AS open_ticket_count,
            CASE
                WHEN IFNULL(tdws.on_hold, 0) = 1 THEN 0
                WHEN tdws.task_status = 1
                    AND tdws.task_completed_on IS NOT NULL
                    AND tdws.task_completed_on != '0000-00-00 00:00:00'
                    AND DATE(tdws.task_completed_on) > tdws.end_date
                THEN DATEDIFF(DATE(tdws.task_completed_on), tdws.end_date)
                WHEN tdws.task_status IN (0, 2)
                    AND CURDATE() > tdws.end_date
                THEN DATEDIFF(CURDATE(), tdws.end_date)
                ELSE 0
            END AS delay_days
        ", false);
        $this->db->from('task_department_wise_scheduling tdws');
        $this->db->join('departments d', 'tdws.department_id = d.department_id', 'left');
        $this->db->join('task_management task', 'tdws.taskid = task.task_id', 'left');
        $this->db->join('system_users u', 'tdws.assigned_user = u.user_id', 'left');
        $this->db->where('tdws.df_id', $df_id);
        $this->db->where('tdws.taskid >', 0);
        $this->db->order_by('task.sortorder', 'ASC');
        $this->db->order_by('tdws.end_date', 'ASC');
        $this->db->order_by('tdws.id', 'ASC');

        $rows = $this->db->get()->result_array();

        /* ---------------- roll it up ---------------- */
        $m = array(
            'total' => 0, 'completed' => 0, 'open' => 0,
            'on_hold' => 0, 'delayed' => 0, 'open_tickets' => 0,
            'total_delay_days' => 0, 'max_delay_days' => 0,
            'completion_pct' => 0,
        );

        $by_dept = array();
        $tasks   = array();

        foreach ($rows as $r) {

            $delay = (int) $r['delay_days'];
            $done  = ((int) $r['task_status'] === 1);
            $hold  = ((int) $r['on_hold'] === 1);

            $m['total']++;
            if ($done)  $m['completed']++;
            if ($hold)  $m['on_hold']++;
            if (!$done && !$hold) $m['open']++;
            if ($delay > 0) {
                $m['delayed']++;
                $m['total_delay_days'] += $delay;
                if ($delay > $m['max_delay_days']) $m['max_delay_days'] = $delay;
            }
            $m['open_tickets'] += (int) $r['open_ticket_count'];

            $dept = trim((string) $r['department']);
            if ($dept === '') $dept = 'Unassigned';

            if (!isset($by_dept[$dept])) {
                $by_dept[$dept] = array(
                    'department' => $dept,
                    'total' => 0, 'completed' => 0, 'delayed' => 0,
                    'max_delay_days' => 0, 'completion_pct' => 0,
                );
            }
            $by_dept[$dept]['total']++;
            if ($done) $by_dept[$dept]['completed']++;
            if ($delay > 0) {
                $by_dept[$dept]['delayed']++;
                if ($delay > $by_dept[$dept]['max_delay_days']) {
                    $by_dept[$dept]['max_delay_days'] = $delay;
                }
            }

            if ($hold)      $status = 'On Hold';
            elseif ($done)  $status = 'Completed';
            elseif ($delay > 0) $status = 'Delayed';
            else            $status = 'Open';

            $tasks[] = array(
                'task_record_id'   => (int) $r['task_record_id'],
                'task_name'        => (string) $r['task_name'],
                'department'       => $dept,
                'responsible'      => trim((string) $r['responsible_person']),
                'start_date'       => $this->_gs_date($r['start_date']),
                'end_date'         => $this->_gs_date($r['end_date']),
                'completed_on'     => $this->_gs_date($r['task_completed_on']),
                'status'           => $status,
                'delay_days'       => $delay,
                'open_tickets'     => (int) $r['open_ticket_count'],
                'remarks'          => trim(strip_tags((string) $r['remarks'])),
            );
        }

        if ($m['total'] > 0) {
            $m['completion_pct'] = (int) round(($m['completed'] / $m['total']) * 100);
        }

        foreach ($by_dept as $k => $d) {
            $by_dept[$k]['completion_pct'] = $d['total'] > 0
                ? (int) round(($d['completed'] / $d['total']) * 100)
                : 0;
        }

        /* worst delay first - that is the reason anyone opens this screen */
        $departments = array_values($by_dept);
        usort($departments, function ($a, $b) {
            if ($a['max_delay_days'] === $b['max_delay_days']) {
                return $a['completion_pct'] - $b['completion_pct'];
            }
            return $b['max_delay_days'] - $a['max_delay_days'];
        });

        $this->_gs_out(array(
            'status'      => true,
            'df'          => $df,
            'po'          => $po ? $po : null,
            'metrics'     => $m,
            'departments' => $departments,
            'tasks'       => $tasks,
            'overtime'    => $this->_df_overtime($df_id, (int) $me['id']),
        ));

    } catch (Throwable $e) {
        $this->_gs_fail('DF detail failed: ' . $e->getMessage());
    }
}

/* Approved overtime booked against one DF - the web Search DF page's
   "Approved Overtime" tab (Dashboard::df_detail_overtime), repeated here
   rather than called, so the app and the page quote the same hours.
   Everyone who can open the DF sees who worked and for how long; rates
   and cost only reach holders of an overtime reports / cost-rate grant. */
private function _df_overtime($df_id, $me_id)
{
    $overtime = array(
        'rows' => array(), 'by_day' => array(), 'show_cost' => false, 'available' => false,
        'summary' => array('occasions' => 0, 'people_entries' => 0, 'person_minutes' => 0,
                           'total_cost' => 0, 'last_approved_at' => ''),
    );
    try {
        foreach (array('overtime_requests', 'overtime_assignments') as $table) {
            if (!$this->db->table_exists($table)) return $overtime;
        }
        $this->load->helper('overtime');
        $permissions = ot_user_permissions($this->db, (int) $me_id);
        $overtime['available'] = true;
        $overtime['show_cost'] = !empty($permissions['reports']) || !empty($permissions['costs']);
        $cost_columns = $this->db->field_exists('cost_amount', 'overtime_assignments')
            ? 'COALESCE(w.hourly_rate, 0) AS hourly_rate, COALESCE(w.cost_amount, 0) AS cost_amount,'
            : '0 AS hourly_rate, 0 AS cost_amount,';

        $rows = $this->db->query('SELECT r.id AS request_id, r.request_code, r.start_at, r.end_at, r.requested_minutes,
                IFNULL(r.reason, "") AS reason, IFNULL(r.work_reference, "") AS work_reference, r.admin_decided_at,
                COALESCE(w.person_name, CONCAT(COALESCE(req.first_name, ""), " ", COALESCE(req.last_name, ""))) AS person_name,
                CASE WHEN w.id IS NULL THEN r.employee_id ELSE w.user_id END AS worker_id,
                IFNULL(dept.department, "") AS department, ' . $cost_columns . '
                IFNULL(req.first_name, "") AS requested_first_name, IFNULL(req.last_name, "") AS requested_last_name,
                IFNULL(apr.first_name, "") AS approved_first_name, IFNULL(apr.last_name, "") AS approved_last_name
            FROM overtime_requests r
            LEFT JOIN overtime_assignments w ON w.request_id = r.id
            LEFT JOIN system_users req ON req.user_id = r.employee_id
            LEFT JOIN system_users apr ON apr.user_id = r.admin_decided_by
            LEFT JOIN departments dept ON dept.department_id = COALESCE(w.department_id, r.department_id)
            WHERE r.df_id = ? AND r.status = "APPROVED"
            ORDER BY r.start_at DESC, r.id DESC, w.id', array((int) $df_id))->result_array();

        $requests = array();
        $days = array();
        foreach ($rows as $row) {
            $day = substr((string) $row['start_at'], 0, 10);
            $minutes = (int) $row['requested_minutes'];
            $cost = $overtime['show_cost'] ? (float) $row['cost_amount'] : 0;
            $requests[(int) $row['request_id']] = true;
            if (!isset($days[$day])) {
                $days[$day] = array('day' => $day, 'occasions' => array(), 'people_entries' => 0,
                                    'person_minutes' => 0, 'total_cost' => 0);
            }
            $days[$day]['occasions'][(int) $row['request_id']] = true;
            $days[$day]['people_entries']++;
            $days[$day]['person_minutes'] += $minutes;
            $days[$day]['total_cost'] += $cost;
            $overtime['summary']['people_entries']++;
            $overtime['summary']['person_minutes'] += $minutes;
            $overtime['summary']['total_cost'] += $cost;
            if ((string) $row['admin_decided_at'] > $overtime['summary']['last_approved_at']) {
                $overtime['summary']['last_approved_at'] = (string) $row['admin_decided_at'];
            }
            $overtime['rows'][] = array(
                'request_code'   => (string) $row['request_code'],
                'person_name'    => $row['worker_id'] ? ot_person_name($row['person_name']) : (string) $row['person_name'],
                'person_type'    => $row['worker_id'] ? 'PMS user' : 'Manual / contract',
                'department'     => (string) $row['department'],
                'start_at'       => (string) $row['start_at'],
                'end_at'         => (string) $row['end_at'],
                'day'            => $day,
                'minutes'        => $minutes,
                'hourly_rate'    => $overtime['show_cost'] ? (float) $row['hourly_rate'] : null,
                'cost_amount'    => $overtime['show_cost'] ? $cost : null,
                'requested_by'   => ot_person_name($row['requested_first_name'], $row['requested_last_name']),
                'approved_by'    => ot_person_name($row['approved_first_name'], $row['approved_last_name']),
                'approved_at'    => (string) $row['admin_decided_at'],
                'work_reference' => (string) $row['work_reference'],
                'reason'         => (string) $row['reason'],
            );
        }
        $overtime['summary']['occasions'] = count($requests);
        foreach ($days as $d) {
            $d['occasions'] = count($d['occasions']);
            $overtime['by_day'][] = $d;
        }
        usort($overtime['by_day'], function ($a, $b) { return strcmp($a['day'], $b['day']); });
    } catch (Throwable $e) {
        /* overtime must never take the whole DF page down with it */
        log_message('error', 'df overtime: ' . $e->getMessage());
    }
    return $overtime;
}


/* ==================================================================
 * HELP TICKETS
 *
 * The two boxes the web dashboard shows on Task/helpticketsforyou and
 * Task/viewallrunninghelptickets: tickets raised against you, and
 * tickets you raised. Both lists here mirror the web's own scoping and
 * its close rule rather than inventing a looser one - the web lets the
 * person who RAISED a ticket close it (a team leader may close their
 * department's), and the assignee sees "Pending to Close". Mobile must
 * not hand out a button the web would not.
 * ================================================================== */

private function _ht_me($in)
{
    $id = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
    if ($id <= 0) return null;

    $role = (int) (isset($in['role_id']) ? $in['role_id'] : 0);

    /* getUserType: 1 admin, 2 team leader, 3 everyone else. The web reads
       its own session 'adminuser' flag, whose value 2 means team leader -
       the same thing this returns. */
    $type = (int) $this->getUserType($role, $id);

    $led = array();
    if ($type === 2) {
        $q = $this->db->select('department_id')
                      ->from('prestogroup_teams')
                      ->where('team_leader', $id)
                      ->get();
        foreach ($q->result() as $r) $led[] = (int) $r->department_id;
        $led = array_values(array_unique($led));
    }

    return array(
        'id' => $id, 'role' => $role, 'type' => $type,
        'is_leader' => ($type === 2),
        'led_departments' => $led,
    );
}

/** Shared select + joins for both boxes. */
private function _ht_select()
{
    $this->db->select("
        a.id, a.df_id, a.task_id, a.task_record_id, a.department_id,
        a.user_id, a.added_by, a.help_ticket_no, a.remarks,
        a.updated_remarks, a.added_on, a.updated_on, a.ticket_status,
        IFNULL(b.department, '') AS department,
        IFNULL(c.task_name, '')  AS task_name,
        IFNULL(f.df_no, '')      AS df_no,
        TRIM(CONCAT(COALESCE(d.first_name,''),' ',COALESCE(d.last_name,''))) AS raised_by_name,
        TRIM(CONCAT(COALESCE(e.first_name,''),' ',COALESCE(e.last_name,''))) AS assigned_to_name
    ", false);
    $this->db->from('communication_ticket_system a');
    $this->db->join('departments b', 'a.department_id = b.department_id', 'left');
    $this->db->join('task_management c', 'a.task_id = c.task_id', 'left');
    $this->db->join('system_users d', 'a.added_by = d.user_id', 'left');
    $this->db->join('system_users e', 'a.user_id = e.user_id', 'left');
    $this->db->join('df_release f', 'a.df_id = f.id', 'left');

    /* The web shows running tickets only, and only on DFs still live -
       a ticket on a closed or held DF is not something to chase. */
    $this->db->where('a.ticket_status', 0);
    $this->db->where('a.df_id >', 0);
    $this->db->where('f.df_status', 0);
    $this->db->where('(f.on_hold = 0 OR f.on_hold IS NULL)', null, false);
}

/** box: for_you = raised against me, by_you = raised by me, all = everyone's. */
private function _ht_scope($me, $box)
{
    if ($box === 'all') {
        /* no user restriction - only an EA ever reaches this */
        return;
    }

    if ($box === 'by_you') {
        $this->db->where('a.added_by', $me['id']);
    } else {
        $this->db->where('a.user_id', $me['id']);
    }
}

private function _ht_shape($r, $me)
{
    /* Same test the web renders its button on. */
    $can_close = ((int) $r->added_by === (int) $me['id']) || !empty($me['is_leader']);

    $age = $this->_time_open($r->added_on);

    return array_merge($age, array(
        'id'               => (int) $r->id,
        'ticket_no'        => (string) $r->help_ticket_no,
        'remarks'          => trim(strip_tags((string) $r->remarks)),
        'updated_remarks'  => trim(strip_tags((string) $r->updated_remarks)),
        'df_id'            => (int) $r->df_id,
        'df_no'            => (string) $r->df_no,
        'task_record_id'   => (int) $r->task_record_id,
        'task_name'        => (string) $r->task_name,
        'department'       => (string) $r->department,
        'raised_by'        => (int) $r->added_by,
        'raised_by_name'   => (string) $r->raised_by_name,
        'assigned_to'      => (int) $r->user_id,
        'assigned_to_name' => (string) $r->assigned_to_name,
        'added_on'         => $this->_gs_date($r->added_on),
        'updated_on'       => $this->_gs_date($r->updated_on),
        'can_close'        => $can_close,
        'close_hint'       => $can_close ? '' : 'Pending to Close',
    ));
}

/**
 * One box's worth of counts, shaped like the delegation summary's.
 *
 * Delegation splits its open work into overdue and due-today. Tickets
 * carry no due date, so the equivalent split is by age: raised today,
 * and sitting a week or more. Same idea - what is fine, and what needs
 * chasing - counted the only way this table allows.
 */
private function _ht_bucket($rows)
{
    $out = array('open' => 0, 'aging' => 0, 'today' => 0);

    $today = strtotime(date('Y-m-d') . ' 00:00:00');

    foreach ($rows as $r) {
        $out['open']++;

        $added = trim((string) $r->added_on);
        if ($added === '' || strpos($added, '0000') === 0) continue;

        $t = strtotime(substr($added, 0, 10) . ' 00:00:00');
        if ($t === false) continue;

        $days = (int) round(($today - $t) / 86400);

        if ($days <= 0)      $out['today']++;
        elseif ($days >= 7)  $out['aging']++;
    }

    return $out;
}

/** Counts for the dashboard strip. */
public function ht_summary_api()
{
    try {
        $in = $this->_gs_input();
        $me = $this->_ht_me($in);
        if (!$me) return $this->_gs_fail('auth');

        $this->_ht_select();
        $this->_ht_scope($me, 'for_you');
        $b_for_you = $this->_ht_bucket($this->db->get()->result());

        $this->_ht_select();
        $this->_ht_scope($me, 'by_you');
        $b_by_you = $this->_ht_bucket($this->db->get()->result());

        $is_ea = $this->_is_ea($me['id']);

        $b_all = array('open' => 0, 'aging' => 0, 'today' => 0);
        if ($is_ea) {
            $this->_ht_select();
            $this->_ht_scope($me, 'all');
            $b_all = $this->_ht_bucket($this->db->get()->result());
        }

        /* The three flat counts stay exactly as they were: builds already
           on people's phones read them as plain integers, and turning them
           into objects would break the strip on every installed copy. */
        $this->_gs_out(array(
            'status'  => true,
            'for_you' => $b_for_you['open'],
            'by_you'  => $b_by_you['open'],
            'total'   => $b_for_you['open'] + $b_by_you['open'],
            'is_ea'   => $is_ea,
            'all'     => $b_all['open'],

            'buckets' => array(
                'for_you' => $b_for_you,
                'by_you'  => $b_by_you,
                'all'     => $b_all,
            ),
        ));
    } catch (Throwable $e) {
        $this->_gs_fail('Help ticket summary failed: ' . $e->getMessage());
    }
}

public function ht_list_api()
{
    try {
        $in = $this->_gs_input();
        $me = $this->_ht_me($in);
        if (!$me) return $this->_gs_fail('auth');

        $box = isset($in['box']) ? strtolower(trim((string) $in['box'])) : 'for_you';

        if ($box === 'all') {
            /* the box exists, but only for the people named in _ea_ids */
            if (!$this->_is_ea($me['id'])) return $this->_gs_fail('forbidden');
        } elseif ($box !== 'by_you') {
            $box = 'for_you';
        }

        $search = isset($in['search']) ? trim((string) $in['search']) : '';

        $limit = (int) (isset($in['limit']) ? $in['limit'] : 300);
        if ($limit <= 0 || $limit > 1000) $limit = 300;

        $this->_ht_select();
        $this->_ht_scope($me, $box);

        /* a.user_id is who the ticket is on, a.added_by is who raised it */
        $who = $this->_side_of($in);
        if ($who['user_id'] > 0) {
            if ($who['side'] === 'assigned_to') {
                $this->db->where('a.user_id', $who['user_id']);
            } elseif ($who['side'] === 'assigned_by') {
                $this->db->where('a.added_by', $who['user_id']);
            } else {
                $this->db->group_start()
                    ->where('a.user_id', $who['user_id'])
                    ->or_where('a.added_by', $who['user_id'])
                    ->group_end();
            }
        }

        if ($search !== '') {
            $this->db->group_start()
                ->like('a.help_ticket_no', $search)
                ->or_like('a.remarks', $search)
                ->or_like('f.df_no', $search)
                ->or_like('c.task_name', $search)
                ->or_like('d.first_name', $search)
                ->or_like('d.last_name', $search)
                ->or_like('e.first_name', $search)
                ->or_like('e.last_name', $search)
                ->group_end();
        }

        $this->db->order_by('a.id', 'DESC');
        $this->db->limit($limit);

        $tickets = array();
        foreach ($this->db->get()->result() as $r) {
            $tickets[] = $this->_ht_shape($r, $me);
        }

        $this->_gs_out(array(
            'status'  => true,
            'box'     => $box,
            'count'   => count($tickets),
            'is_ea'   => $this->_is_ea($me['id']),
            'tickets' => $tickets,
        ));
    } catch (Throwable $e) {
        $this->_gs_fail('Help ticket list failed: ' . $e->getMessage());
    }
}

/** Closes a ticket, writing the same three columns Task/clicktoclose does. */
public function ht_close_api()
{
    try {
        $in = $this->_gs_input();
        $me = $this->_ht_me($in);
        if (!$me) return $this->_gs_fail('auth');

        $ticket_id = (int) (isset($in['ticket_id']) ? $in['ticket_id'] : 0);
        if ($ticket_id <= 0) return $this->_gs_fail('ticket_id is required');

        $row = $this->db->select('id, added_by, user_id, department_id, ticket_status')
            ->from('communication_ticket_system')
            ->where('id', $ticket_id)
            ->get()
            ->row();

        if (empty($row)) return $this->_gs_fail('Ticket not found');

        if ((int) $row->ticket_status === 1) {
            return $this->_gs_fail('This ticket is already closed');
        }

        /* Re-checked here, not trusted from the list: can_close travels to
           the phone and must never be what decides the write. */
        $allowed = ((int) $row->added_by === (int) $me['id']) || !empty($me['is_leader']);

        if (!$allowed) {
            return $this->_gs_fail('Only the person who raised this ticket can close it');
        }

        $remarks = trim((string) (isset($in['remarks']) ? $in['remarks'] : ''));

        $data = array(
            'ticket_status'    => 1,
            'ticket_closed_by' => $me['id'],
            'ticket_closed_on' => date('Y-m-d H:i:s'),
        );

        if ($remarks !== '') $data['ticket_closing_remarks'] = $remarks;

        $this->db->where('id', $ticket_id);
        $this->db->update('communication_ticket_system', $data);

        $this->_gs_out(array(
            'status'  => true,
            'message' => 'Ticket closed',
        ));
    } catch (Throwable $e) {
        $this->_gs_fail('Help ticket close failed: ' . $e->getMessage());
    }
}


/* ==================================================================
 * MORNING DIGEST
 *
 * One personalised push per person at the start of the day: their
 * followups due and missed across leads, service and spares, and the
 * DF tasks that need closing today or are already past.
 *
 * Every number is produced by the same rule the matching screen uses,
 * so tapping the notification lands on a list that agrees with it. A
 * module the person cannot open is left out entirely rather than
 * counted and then hidden.
 *
 * Called by cron, so it authenticates on a shared key rather than a
 * session; an admin user_id also works for testing by hand. Dry by
 * default - it sends to real phones.
 * ================================================================== */

private function _digest_key()
{
    return 'spm-morning-digest-2026';
}

/** Mirrors get_user_permissions_api, so a push cannot advertise a hidden tab. */
private function _digest_can($module, $role_id, $dept_id, $user_id)
{
    $trio = in_array((int) $user_id, array(61, 139, 161), true);

    switch ($module) {
        case 'lead':
            return ($role_id == 12 || $dept_id == 9 || $trio);
        case 'service':
            return ($role_id == 12 || ($dept_id == 22 && $user_id == 111) || $trio);
        case 'spare':
            return ($role_id == 12 || ($dept_id == 31 && $user_id == 89) || $trio);
    }
    return false;
}

/** Leads: newest progress row per lead, ignoring dead-end and converted stages. */
private function _digest_leads($user_id, $role_id)
{
    $chk = ($role_id != 12 && $role_id != 41)
        ? ' AND b.added_by = ' . (int) $user_id
        : '';

    $conversion = $this->dashboardmodel->getConversionLeadStage();
    $dead = $this->salescrm->getDeadEndLeadStage();
    array_push($dead, $conversion);
    $dead_list = "'" . implode("','", $dead) . "'";

    $q = $this->db->query("
        SELECT a.next_follow_date
        FROM progress_remarks a
        JOIN leads b ON a.lead_id = b.id
        WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id)
        AND a.lead_status NOT IN ($dead_list)
        AND b.closed = 0
        $chk
    ");

    $today = date('Y-m-d');
    $out = array('today' => 0, 'missed' => 0);

    foreach ($q->result() as $row) {
        $d = $row->next_follow_date;
        if (empty($d) || $d === '0000-00-00' || $d === '1970-01-01') continue;
        if ($d === $today)     $out['today']++;
        elseif ($d < $today)   $out['missed']++;
    }

    return $out;
}

/** Service: latest progress row per opportunity. */
private function _digest_service($user_id, $role_id)
{
    $today = date('Y-m-d');
    $out = array('today' => 0, 'missed' => 0);

    foreach (array('today', 'missed') as $which) {

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

        if ($role_id != 12 && $role_id != 41 && $user_id != 111) {
            $this->db->where('so.marketing_person_id', $user_id);
        }

        $this->db->where('sph.next_follow_date IS NOT NULL', null, false);
        $this->db->where(
            'DATE(sph.next_follow_date) ' . ($which === 'today' ? '=' : '<') .
            ' ' . $this->db->escape($today), null, false
        );

        $out[$which] = $this->db->count_all_results();
    }

    return $out;
}

/** DF board: what must be closed today, and what is already past. */
private function _digest_tasks($user_id, $role_id, $company_wide = false)
{
    $today = date('Y-m-d');
    $type  = (int) $this->getUserType($role_id, $user_id);

    $led = array();
    if ($type === 2) {
        $q = $this->db->select('department_id')->from('prestogroup_teams')
                      ->where('team_leader', $user_id)->get();
        foreach ($q->result() as $r) $led[] = (int) $r->department_id;
        $led = array_values(array_unique($led));
    }

    $out = array('today' => 0, 'overdue' => 0);

    foreach (array('today', 'overdue') as $which) {

        $this->db->from('task_department_wise_scheduling a');
        $this->db->join('df_release b', 'a.df_id = b.id', 'left');
        $this->db->where('a.task_status', 0);
        $this->db->where('a.on_hold', 0);
        $this->db->where('a.assigned_user !=', 0);
        $this->db->where('a.department_id !=', 22);

        /* the DF itself must still be live, same as the board */
        $this->db->group_start()
            ->where('a.df_id', 0)
            ->or_where('b.df_status', 0)
            ->group_end();

        if ($which === 'today') {
            $this->db->where('a.end_date', $today);
        } else {
            $this->db->where('a.end_date <', $today);
            $this->db->where("a.end_date != '0000-00-00'", null, false);
        }

        /* Scoped exactly as the board scopes it: a leader sees their
           departments, everyone else only their own row. An admin is
           given their own too - a company-wide number at 10am is noise,
           not a to-do list. */
        if ($company_wide) {
            /* the EA is watching the board, not working it */
        } elseif ($type === 2 && count($led) > 0) {
            $this->db->where_in('a.department_id', $led);
        } else {
            $this->db->where('a.assigned_user', $user_id);
        }

        $out[$which] = $this->db->count_all_results();
    }

    return $out;
}

/**
 * "3 today, 5 missed", or just the half that is non-zero.
 *
 * A zero earns no words: "0 today, 46 missed" makes someone read a number
 * that means nothing before reaching the one that does. Both zero and the
 * caller drops the line entirely.
 */
private function _digest_phrase($a, $a_word, $b, $b_word)
{
    $a = (int) $a;
    $b = (int) $b;

    $parts = array();
    if ($a > 0) $parts[] = $a . ' ' . $a_word;
    if ($b > 0) $parts[] = $b . ' ' . $b_word;

    return implode(', ', $parts);
}

public function daily_followup_push_api()
{
    try {
        $in = $this->_gs_input();

        /* cron carries the key; a human can pass an admin user_id instead */
        $key = trim((string) (isset($in['key']) ? $in['key'] : ''));
        $caller = (int) (isset($in['user_id']) ? $in['user_id'] : 0);

        $authorised = ($key !== '' && hash_equals($this->_digest_key(), $key));

        if (!$authorised && $caller > 0) {
            $authorised = ((int) $this->getUserType(
                (int) (isset($in['role_id']) ? $in['role_id'] : 0), $caller
            ) === 1) || in_array($caller, array(61, 139, 161), true);
        }

        if (!$authorised) return $this->_gs_fail('forbidden');

        date_default_timezone_set('Asia/Kolkata');

        $dry = $this->_bc_flag(isset($in['dry_run']) ? $in['dry_run'] : null, true);
        $only = (int) (isset($in['only_user_id']) ? $in['only_user_id'] : 0);

        if (!isset($this->dashboardmodel)) {
            $this->load->model('Dashboard_model', 'dashboardmodel');
        }

        /* Only people with a phone registered can be pushed at all. */
        $this->db->select('u.user_id, u.first_name, u.last_name, u.user_role_id, u.department_id');
        $this->db->from('system_users u');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.hide_profile', 0);
        $this->db->where('u.user_id IN (SELECT user_id FROM user_devices WHERE fcm_token != "")', null, false);
        if ($only > 0) $this->db->where('u.user_id', $only);

        $people = $this->db->get()->result();

        $sent = 0;
        $skipped = 0;
        $failed = 0;
        $preview = array();

        foreach ($people as $p) {

            $uid  = (int) $p->user_id;
            $role = (int) $p->user_role_id;
            $dept = (int) $p->department_id;

            $is_ea = $this->_is_ea($uid);

            /* An admin's screens show the whole company, so their digest
               would just be the company backlog - the same three numbers
               for every one of them, and nothing they personally owe. The
               EA is the deliberate exception: watching that total IS her
               job, so she gets it and nobody else does. */
            $is_admin = ((int) $this->getUserType($role, $uid) === 1)
                || $role === 12 || $role === 41
                || in_array($uid, array(61, 139, 161), true);

            if ($is_admin && !$is_ea) { $skipped++; continue; }

            $lines = array();
            $total = 0;

            if ($this->_digest_can('lead', $role, $dept, $uid)) {
                $c = $this->_digest_leads($uid, $role);
                if ($c['today'] || $c['missed']) {
                    $lines[] = 'Lead followups: ' . $this->_digest_phrase(
                        $c['today'], 'today', $c['missed'], 'missed');
                    $total += $c['today'] + $c['missed'];
                }
            }

            if ($this->_digest_can('service', $role, $dept, $uid)) {
                $c = $this->_digest_service($uid, $role);
                if ($c['today'] || $c['missed']) {
                    $lines[] = 'Service followups: ' . $this->_digest_phrase(
                        $c['today'], 'today', $c['missed'], 'missed');
                    $total += $c['today'] + $c['missed'];
                }
            }

            if ($this->_digest_can('spare', $role, $dept, $uid)) {
                $c = (array) $this->dashboardmodel->get_spare_followup_countsNew($role, $uid, $dept);
                $t = (int) (isset($c['today']) ? $c['today'] : 0);
                $m = (int) (isset($c['missed']) ? $c['missed'] : 0);
                if ($t || $m) {
                    $lines[] = 'Spare followups: ' . $this->_digest_phrase(
                        $t, 'today', $m, 'missed');
                    $total += $t + $m;
                }
            }

            /* the DF board is open to everyone, so this is never gated */
            $c = $this->_digest_tasks($uid, $role, $is_ea);
            if ($c['today'] || $c['overdue']) {
                $lines[] = 'DF tasks: ' . $this->_digest_phrase(
                    $c['today'], 'to close today', $c['overdue'], 'overdue');
                $total += $c['today'] + $c['overdue'];
            }

            /* Nobody wants to be told they have nothing. */
            if ($total === 0) { $skipped++; continue; }

            $name = trim((string) $p->first_name);
            if ($name === '') $name = trim($p->first_name . ' ' . $p->last_name);

            $title = 'Good morning' . ($name !== '' ? ', ' . $name : '');
            if ($is_ea) $title .= ' - company total';
            $body  = implode("\n", $lines);

            $preview[] = array(
                'user_id' => $uid,
                'name'    => trim($p->first_name . ' ' . $p->last_name),
                'title'   => $title,
                'body'    => $body,
            );

            if ($dry) continue;

            $tokens = $this->db->select('fcm_token')
                ->from('user_devices')
                ->where('user_id', $uid)
                ->where('fcm_token !=', '')
                ->get()->result_array();

            $seen = array();
            foreach ($tokens as $t) {
                $tok = trim((string) $t['fcm_token']);
                if ($tok === '' || isset($seen[$tok])) continue;
                $seen[$tok] = true;

                try {
                    sendFCM($tok, $title, $body, array('type' => 'daily_digest'));
                    $sent++;
                } catch (Throwable $e) {
                    $failed++;
                }
            }
        }

        $this->_gs_out(array(
            'status'    => true,
            'dry_run'   => $dry,
            'people'    => count($people),
            'with_work' => count($preview),
            'skipped'   => $skipped,
            'pushed'    => $sent,
            'failed'    => $failed,
            'preview'   => array_slice($preview, 0, 12),
        ));

    } catch (Throwable $e) {
        $this->_gs_fail('Digest failed: ' . $e->getMessage());
    }
}


/* ======================================================================
 * ATTENDANCE REMINDER PUSH  -  added 2026-09-15
 *
 * One push each morning to the people who have not marked the day yet,
 * tapped straight through to the same popup the app already gates on.
 *
 * Who gets it is worked out here, at send time, rather than from a list
 * prepared earlier: somebody who marks at 09:28 must not be reminded at
 * 09:30. The push itself carries no instruction beyond its type, so a
 * reminder opened in the afternoon re-asks the server and shows whatever
 * is true then.
 *
 * Driven by hPanel cron - see the key below. The server clock is UTC, so
 * 09:30 IST is 04:00 UTC.
 * ====================================================================== */

private function _attrem_key()
{
    return 'spm-attendance-reminder-2026';
}

/**
 * Send one push, with a lifetime of our own choosing.
 *
 * Neither helper would do. sendFCMData() stamps every message ttl=30s, so a
 * phone that is in a pocket, dozing, or on the lift at half nine never gets
 * the reminder at all - and that is exactly the population this exists to
 * reach. sendFCM() sets no ttl and so inherits FCM's four-week default,
 * which is the opposite mistake: "you have not marked today" is wrong by
 * tomorrow and baffling next week.
 *
 * Building the message here also keeps the change inside this file.
 * fcm_helper.php is the one sender for chat, delegation, task management,
 * help tickets and ECN; widening its signature for this single caller would
 * put all five in the blast radius of a typo.
 */
private function _attrem_send($token, $title, $body, $ttl_seconds)
{
    $accessToken = getAccessToken();

    $payload = array(
        'message' => array(
            'token' => $token,
            'notification' => array('title' => $title, 'body' => $body),
            'android' => array(
                'priority' => 'high',
                'ttl' => ((int) $ttl_seconds) . 's',
                'notification' => array(
                    'channel_id' => 'high_importance_channel',
                    'sound' => 'default',
                ),
            ),
            /* The app routes on `type`; `date` is for the log, not for the
               decision - see the note above about re-asking the server. */
            'data' => array(
                'type' => 'attendance_mark',
                'date' => date('Y-m-d'),
            ),
        ),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,
        'https://fcm.googleapis.com/v1/projects/shubhampackapp/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $res = curl_exec($ch);

    /* FCM answers a successful send with {"name":"projects/.../messages/..."}.
       Anything else - an expired token, a 404, curl failing outright - counts
       as a failure rather than being swallowed. */
    $ok = ($res !== false && strpos((string) $res, '"name"') !== false);

    curl_close($ch);

    return $ok;
}

public function attendance_reminder_push_api()
{
    try {
        $in = $this->_gs_input();

        /* cron carries the key; a human can pass an admin user_id instead */
        $key = trim((string) (isset($in['key']) ? $in['key'] : ''));
        $caller = (int) (isset($in['user_id']) ? $in['user_id'] : 0);

        $authorised = ($key !== '' && hash_equals($this->_attrem_key(), $key));

        if (!$authorised && $caller > 0) {
            $authorised = ((int) $this->getUserType(
                (int) (isset($in['role_id']) ? $in['role_id'] : 0), $caller
            ) === 1) || in_array($caller, array(61, 139, 161), true);
        }

        if (!$authorised) return $this->_gs_fail('forbidden');

        /* The mark is stored per IST calendar day and the box runs on UTC.
           Without this the 04:00 UTC cron would ask about the previous date
           every single morning. */
        date_default_timezone_set('Asia/Kolkata');

        $dry  = $this->_bc_flag(isset($in['dry_run']) ? $in['dry_run'] : null, true);
        $only = (int) (isset($in['only_user_id']) ? $in['only_user_id'] : 0);

        /* Target one person by name, for testing from a machine that cannot
           reach the database to look an id up. Matched against either name
           part, so "Manglesh" is enough. */
        $only_name = trim((string) (isset($in['only_name']) ? $in['only_name'] : ''));

        /* Send even to someone who HAS already marked.
         *
         * Only for testing, and only ever alongside only_user_id/only_name -
         * a forced run without one would push the whole company a reminder
         * they have already answered, which is the single worst thing this
         * endpoint could do. Refused below if that is what is asked. */
        $force = $this->_bc_flag(isset($in['force']) ? $in['force'] : null, false);

        if ($force && $only <= 0 && $only_name === '') {
            return $this->_gs_fail(
                'force needs only_user_id or only_name - refusing to push '
                . 'everybody a reminder they have already answered');
        }

        /* Six hours by default: gone by mid-afternoon, so it can never be
           the notification somebody wakes up to the next morning. */
        $ttl = (int) (isset($in['ttl']) ? $in['ttl'] : 21600);
        if ($ttl < 60)    $ttl = 60;
        if ($ttl > 86400) $ttl = 86400;

        $today = date('Y-m-d');

        /* The table is created lazily by the gate; on a morning where nobody
           has marked yet it may not exist, and a missing table would take the
           whole run down instead of reminding everybody. */
        $this->_att_day_ensure();

        $unmarked_sql =
            'NOT EXISTS (SELECT 1 FROM ' . $this->_att_day_table() .
            ' d WHERE d.user_id = u.user_id AND d.work_date = ' .
            $this->db->escape($today) . ')';

        /* Only the four exempt people are left out of the chase, because
         * only they have nothing to answer. They come off the denominator
         * too - "38 unmarked" must not count people who are never going to
         * mark because they are never asked.
         *
         * This is _att_gate_exempt() written in SQL, because this runs over
         * the whole staff table rather than one user; the two lists must be
         * changed together. It used to exclude every role-12 account, which
         * from 2026-09-22 would have quietly stopped chasing administrators
         * who are now expected to mark like everybody else. */
        $not_admin_sql = 'u.user_id NOT IN (61, 62, 139, 167)';

        /* Everybody still unmarked, whether or not we can reach them. This is
           the honest denominator: only a fraction of staff have ever
           registered a token, and "pushed 20" means nothing without it. */
        $this->db->select('COUNT(*) AS c', false);
        $this->db->from('system_users u');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.hide_profile', 0);
        $this->db->where($not_admin_sql, null, false);
        $this->db->where($unmarked_sql, null, false);
        $row = $this->db->get()->row_array();
        $unmarked_total = (int) (isset($row['c']) ? $row['c'] : 0);

        /* Unmarked, reachable, still employed.
         *
         * NOT EXISTS rather than a LEFT JOIN: app_attendance_day is unique on
         * (user_id, work_date) so a join is correct today, but it would start
         * duplicating rows the day that constraint is relaxed.
         *
         * Admins are EXCLUDED, since 2026-09-17: they no longer answer the
         * day mark at all, so a reminder to mark it has nothing to ask for.
         * This reverses the build-43 rule that put everyone behind it. */
        $this->db->select('u.user_id, u.first_name, u.last_name');
        $this->db->from('system_users u');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.hide_profile', 0);
        $this->db->where($not_admin_sql, null, false);
        $this->db->where('u.user_id IN (SELECT user_id FROM user_devices WHERE fcm_token != "")', null, false);

        /* The whole point of the endpoint - relaxed only for a forced test. */
        if (!$force) $this->db->where($unmarked_sql, null, false);

        if ($only > 0) $this->db->where('u.user_id', $only);

        if ($only_name !== '') {
            $like = $this->db->escape_like_str($only_name);
            $this->db->where(
                "(u.first_name LIKE '%" . $like . "%' ESCAPE '!'"
                . " OR u.last_name LIKE '%" . $like . "%' ESCAPE '!'"
                . " OR CONCAT(u.first_name, ' ', u.last_name) LIKE '%" . $like . "%' ESCAPE '!')",
                null, false
            );
        }

        $people = $this->db->get()->result();

        $sent = 0;
        $failed = 0;
        $preview = array();

        foreach ($people as $p) {

            $uid = (int) $p->user_id;

            $name  = trim((string) $p->first_name);
            $title = 'Mark your attendance' . ($name !== '' ? ', ' . $name : '');
            $body  = 'You have not marked today yet. Tap to mark Present, On Duty or Gate Pass.';

            $preview[] = array(
                'user_id' => $uid,
                'name'    => trim($p->first_name . ' ' . $p->last_name),
            );

            if ($dry) continue;

            $tokens = $this->db->select('fcm_token')
                ->from('user_devices')
                ->where('user_id', $uid)
                ->where('fcm_token !=', '')
                ->get()->result_array();

            /* One person can have several rows - an old phone, a reinstall.
               Dedupe, or they get the same reminder twice. */
            $seen = array();
            foreach ($tokens as $t) {
                $tok = trim((string) $t['fcm_token']);
                if ($tok === '' || isset($seen[$tok])) continue;
                $seen[$tok] = true;

                try {
                    if ($this->_attrem_send($tok, $title, $body, $ttl)) $sent++;
                    else $failed++;
                } catch (Throwable $e) {
                    $failed++;
                }
            }
        }

        $this->_gs_out(array(
            'status'         => true,
            'dry_run'        => $dry,
            'date'           => $today,
            'unmarked_total' => $unmarked_total,
            'reachable'      => count($people),
            'unreachable'    => $unmarked_total - count($people),
            'pushed'         => $sent,
            'failed'         => $failed,
            'ttl'            => $ttl . 's',
            'forced'         => $force ? 1 : 0,
            'preview'        => array_slice($preview, 0, 20),
        ));

    } catch (Throwable $e) {
        $this->_gs_fail('Attendance reminder failed: ' . $e->getMessage());
    }
}


/* ======================================================================
 * APP ENTRY LOG  -  added 2026-09-10
 *
 * Every write that reaches the PMS through this controller is the mobile
 * app, by definition: the web PMS never calls mobile/Api. So the honest
 * place to answer "was this entered from the app?" is right here, and the
 * answer costs one row per writing request.
 *
 * Nothing is hooked into the endpoints one by one - _remap() sees every
 * call, and afterwards the executed queries are read back out of
 * $this->db->queries (save_queries is TRUE) to see whether the request
 * actually wrote anything. So an endpoint added tomorrow is logged too,
 * with no line of its own.
 *
 * The write happens in a shutdown function because 39 endpoints in this
 * file end in exit; - code after the call would simply not run. It is
 * wrapped in a buffer and a try/catch, because this is a development
 * ENVIRONMENT and a stray warning printed after the JSON would break the
 * app's parser. The log must never be able to damage a response.
 *
 * Two kinds of row, so the headline number means something:
 *   entry   - a real record created or updated (this is "entries via app")
 *   chat    - messages, reactions, group admin
 *
 * A third kind, "system", is worked out and then thrown away rather than
 * stored: fcm tokens, read receipts, notification rows and the presence
 * row that chat_poll writes on every long-poll. Nobody typed any of it,
 * and chat_poll alone would have put tens of thousands of rows a day in
 * here - it was doing 30 in the first two minutes.
 *
 * Read it at:  <site>/index.php/mobile/Api/app_entry_report   (roles 12, 41)
 * ====================================================================== */

/** Per-request scratch space. Null unless a call has been armed. */
private $_ael_state = null;

/** The log's own table, created on first use so no migration is needed. */
private function _ael_table() { return 'app_entry_log'; }

/** Tables that write themselves - nobody "entered" any of this. */
private function _ael_system_tables()
{
    return array(
        'app_entry_log', 'app_device_log', 'app_activity_log',
        'app_notification', 'app_notifications', 'notification', 'notifications',
        'user_devices', 'user_login_ip_tracking', 'ci_sessions', 'sessions',
        'chat_notification', 'chat_read', 'chat_typing', 'chat_presence',
    );
}

/** Endpoints whose writes are plumbing, whatever table they land in. */
private function _ael_system_endpoints()
{
    return array(
        'index', 'auth_login', 'savefcmtoken',
        'send_push_notification', 'send_chat_push_notification',
        'daily_followup_push_api', 'ongoingtaskcountnotification',
        'overduetaskcountnotification', 'completeddfnotificationcount',
        'unassignednotificationcount',
        'mark_notification_read', 'mark_read', 'mark_chat_read',
        'chat_read', 'chat_typing', 'chat_poll', 'chat_unread',
        'chat_seen_by', 'chat_read_state', 'chat_notifications',
        'unread_chat_count', 'get_notifications', 'get_unread_count',
        /* reads that quietly mark things read on the way out: opening a
           thread calls Chat_model::mark_read, which updates chat_participant
           and chat_mention. Reading a conversation is not an entry. */
        'chat_thread', 'chat_bootstrap', 'chat_conversations', 'chat_history',
        'chat_directory', 'chat_search_messages', 'chat_search_records',
        'app_entry_report', 'app_entry_stats_api', 'app_entry_check_api',
    );
}

/**
 * CI hands every call here once _remap exists, which is exactly the hook
 * this needs. It also means the framework's own "is this callable?" check
 * is skipped, so that check has to be repeated here - without it, every
 * private helper in this file (_chat_push, _dlg_notify_assigned, ...)
 * would become a public URL.
 */
public function _remap($method, $params = array())
{
    $method = (string) $method;

    if ($method === '' || $method[0] === '_'
        || !method_exists($this, $method)
        || method_exists('CI_Controller', $method)) {
        show_404();
        return;
    }

    $callable = false;
    try {
        $ref = new ReflectionMethod($this, $method);
        $callable = $ref->isPublic() && !$ref->isStatic();
    } catch (Throwable $e) {
        $callable = false;
    }
    if (!$callable) {
        show_404();
        return;
    }

    $this->_ael_arm($method);

    return call_user_func_array(array($this, $method), $params);
}

/** Remember where the query log stood before the endpoint ran. */
private function _ael_arm($method)
{
    try {
        $input = array();
        $raw = @file_get_contents('php://input');   /* re-readable since 5.6, except multipart */
        if ($raw !== false && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) $input = $decoded;
        }
        if (!$input && !empty($_POST)) $input = $_POST;
        if (!$input && !empty($_GET))  $input = $_GET;

        $this->_ael_state = array(
            'method' => $method,
            'start'  => (isset($this->db->queries) && is_array($this->db->queries))
                        ? count($this->db->queries) : -1,
            'input'  => is_array($input) ? $input : array(),
        );

        register_shutdown_function(array($this, '_ael_write'));
    } catch (Throwable $e) {
        $this->_ael_state = null;
    }
}

/**
 * Shutdown handler. Deliberately paranoid: anything it prints is thrown
 * away, and anything it throws is swallowed. A broken log is invisible;
 * a log that breaks a response is not.
 */
public function _ael_write()
{
    if (empty($this->_ael_state)) return;

    $state = $this->_ael_state;
    $this->_ael_state = null;

    ob_start();
    try {
        $this->_ael_record($state);
    } catch (Throwable $e) {
        /* nothing: the user's request already succeeded */
    }
    ob_end_clean();
}

/** Pull one write out of a logged query, or null if it only read. */
private function _ael_parse($sql)
{
    $s = ltrim((string) $sql);
    if (!preg_match('/^(INSERT|REPLACE|UPDATE|DELETE)\b/i', $s, $m)) return null;

    $kind  = strtoupper($m[1]);
    $table = '';

    if ($kind === 'INSERT' || $kind === 'REPLACE') {
        $kind = 'INSERT';
        if (preg_match('/^(?:INSERT|REPLACE)\s+(?:LOW_PRIORITY\s+|DELAYED\s+|HIGH_PRIORITY\s+|IGNORE\s+)*(?:INTO\s+)?[`"\']?([a-zA-Z0-9_]+)/i', $s, $t)) {
            $table = $t[1];
        }
    } elseif ($kind === 'UPDATE') {
        if (preg_match('/^UPDATE\s+(?:LOW_PRIORITY\s+|IGNORE\s+)*[`"\']?([a-zA-Z0-9_]+)/i', $s, $t)) {
            $table = $t[1];
        }
    } else {
        if (preg_match('/^DELETE\s+(?:LOW_PRIORITY\s+|QUICK\s+|IGNORE\s+)*FROM\s+[`"\']?([a-zA-Z0-9_]+)/i', $s, $t)) {
            $table = $t[1];
        }
    }

    if ($table === '') return null;

    /* For an UPDATE/DELETE the row is named in the WHERE clause. */
    $row_id = 0;
    if ($kind !== 'INSERT' && preg_match('/\bWHERE\b.*?[`"\']?([a-zA-Z0-9_]*id)[`"\']?\s*=\s*[\'"]?(\d+)/is', $s, $w)) {
        $row_id = (int) $w[2];
    }

    return array('kind' => $kind, 'table' => strtolower($table), 'row_id' => $row_id);
}

/** entry / chat / system. */
private function _ael_type($endpoint, $primary_table, $tables)
{
    if (in_array(strtolower($endpoint), $this->_ael_system_endpoints(), true)) return 'system';

    $system = $this->_ael_system_tables();
    $real   = array();
    foreach ($tables as $t) {
        if (!in_array($t, $system, true)) $real[] = $t;
    }
    if (!$real) return 'system';

    $chat_only = true;
    foreach ($real as $t) {
        if (strpos($t, 'chat_') !== 0) { $chat_only = false; break; }
    }
    if ($chat_only) return 'chat';

    return 'entry';
}

/** Which part of the PMS an endpoint belongs to. */
private function _ael_module($endpoint)
{
    $e = strtolower($endpoint);

    $starts = array(
        'chat_' => 'Chat', 'visit_' => 'Engineer Visit', 'delegation_' => 'Delegation',
        'tm_' => 'Task Management', 'profile_' => 'Profile', 'ht_' => 'Help Ticket',
        'broadcast' => 'Broadcast',
    );
    foreach ($starts as $needle => $label) {
        if (strpos($e, $needle) === 0) return $label;
    }

    /* order matters: mom before task, spare before service */
    $contains = array(
        'mom' => 'MOM', 'spare' => 'Spares', 'service' => 'Service',
        'business_card' => 'Sales CRM', 'exhibition' => 'Sales CRM',
        'early_closure' => 'DF Task', 'task' => 'DF Task', 'df_' => 'DF Task',
        'approval' => 'Approvals', 'quot' => 'Sales CRM', 'lead' => 'Sales CRM',
        'followup' => 'Sales CRM', 'opportunity' => 'Sales CRM',
        'customer' => 'Sales CRM', 'visit' => 'Engineer Visit',
        'chat' => 'Chat', 'message' => 'Chat', 'attachment' => 'Chat',
        'delegation' => 'Delegation', 'notification' => 'System',
    );
    foreach ($contains as $needle => $label) {
        if (strpos($e, $needle) !== false) return $label;
    }

    return 'Other';
}

/** The id the caller named, which is how you find the record again. */
private function _ael_ref($input)
{
    $keys = array(
        /* taskkiid / mastertaskid / progressdfno are what the DF task screens
           actually post - the generic names below never appear in them. */
        'task_id', 'taskkiid', 'mastertaskid', 'progressdfno',
        'df_id', 'df_no', 'delegation_task_id', 'visit_id',
        'opportunity_id', 'op_id', 'lead_id', 'quote_id', 'quotation_id',
        'customer_id', 'ticket_id', 'mom_id', 'spare_id', 'conversation_id',
        'message_id', 'record_id', 'id',
    );
    foreach ($keys as $k) {
        if (isset($input[$k]) && (is_string($input[$k]) || is_numeric($input[$k]))) {
            $v = trim((string) $input[$k]);
            if ($v !== '' && $v !== '0') return array($k, substr($v, 0, 40));
        }
    }
    return array('', '');
}

/** Readable copy of what was posted, with the bulky and the secret removed. */
private function _ael_payload($input)
{
    $clean = array();
    foreach ($input as $k => $v) {
        $lk = strtolower((string) $k);
        if (strpos($lk, 'password') !== false || strpos($lk, 'token') !== false) {
            $clean[$k] = '[hidden]';
        } elseif (is_array($v)) {
            $clean[$k] = '[' . count($v) . ' items]';
        } elseif (is_string($v) && strlen($v) > 300) {
            $clean[$k] = '[' . strlen($v) . ' chars]';       /* base64 photos live here */
        } else {
            $clean[$k] = $v;
        }
    }
    $json = json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) $json = '';
    return substr($json, 0, 1500);
}

/** Work out what the request wrote, and file one row about it. */
private function _ael_record($state)
{
    if ((int) $state['start'] < 0) return;                     /* save_queries off */
    if (!isset($this->db->queries) || !is_array($this->db->queries)) return;

    $slice = array_slice($this->db->queries, (int) $state['start']);
    if (!$slice) return;

    $writes = array();
    $tables = array();
    $kinds  = array();

    foreach ($slice as $sql) {
        $w = $this->_ael_parse($sql);
        if (!$w) continue;
        if ($w['table'] === $this->_ael_table()) continue;     /* never log the log */
        $writes[] = $w;
        $tables[$w['table']] = true;
        $kinds[$w['kind']] = true;
    }
    if (!$writes) return;

    $tables = array_keys($tables);
    $system = $this->_ael_system_tables();

    /* The record the person was working on is the first business table
       touched - not the notification row that follows it. */
    $primary = null;
    $primary_at = 0;
    foreach ($writes as $i => $w) {
        if (!in_array($w['table'], $system, true) && strpos($w['table'], 'chat_') !== 0) { $primary = $w; $primary_at = $i; break; }
    }
    if (!$primary) {
        foreach ($writes as $i => $w) {
            if (!in_array($w['table'], $system, true)) { $primary = $w; $primary_at = $i; break; }
        }
    }
    if (!$primary) { $primary = $writes[0]; $primary_at = 0; }

    $last_insert_at = -1;
    foreach ($writes as $i => $w) {
        if ($w['kind'] === 'INSERT') $last_insert_at = $i;
    }

    $log_type = $this->_ael_type($state['method'], $primary['table'], $tables);
    if ($log_type === 'system') return;                        /* plumbing: not an entry, and far too chatty */

    if (count($kinds) > 1)          $action = 'mixed';
    elseif (isset($kinds['INSERT'])) $action = 'create';
    elseif (isset($kinds['UPDATE'])) $action = 'update';
    else                             $action = 'delete';

    /* An UPDATE names its row in the WHERE clause. A create does not, and
       insert_id() only knows the LAST insert of the request - which is
       usually the notification row written after the record. So the id is
       claimed only when the record's own insert was the last one; a wrong
       id here would be worse than none. */
    $record_id = $primary['row_id'];
    if (!$record_id && $primary['kind'] === 'INSERT' && $primary_at === $last_insert_at) {
        $record_id = (int) $this->db->insert_id();
    }

    $input = $state['input'];
    list($ref_key, $ref_id) = $this->_ael_ref($input);

    $user_id = 0;
    foreach (array('user_id', 'userid', 'created_by', 'login_user_id') as $k) {
        if (isset($input[$k]) && is_numeric($input[$k])) { $user_id = (int) $input[$k]; break; }
    }

    $now = date('Y-m-d H:i:s');
    $row = array(
        'created_at'     => $now,
        'log_date'       => date('Y-m-d'),
        'user_id'        => $user_id,
        'endpoint'       => substr($state['method'], 0, 80),
        'module'         => $this->_ael_module($state['method']),
        'log_type'       => $log_type,
        'action'         => $action,
        'primary_table'  => substr($primary['table'], 0, 64),
        'record_id'      => (int) $record_id,
        'ref_key'        => $ref_key,
        'ref_id'         => $ref_id,
        'tables_touched' => substr(implode(',', $tables), 0, 255),
        'write_count'    => count($writes),
        'payload'        => $this->_ael_payload($input),
        'ip_address'     => substr((string) $this->input->ip_address(), 0, 45),
        'app_version'    => isset($input['app_version']) ? substr((string) $input['app_version'], 0, 30) : '',
        'device_id'      => isset($input['device_id']) ? substr((string) $input['device_id'], 0, 80) : '',
    );

    $debug = $this->db->db_debug;
    $this->db->db_debug = FALSE;                               /* a log error must stay silent */

    if (!$this->db->insert($this->_ael_table(), $row)) {
        $this->_ael_create_table();
        $this->db->insert($this->_ael_table(), $row);
    }

    $this->db->db_debug = $debug;
}

/** First write of the first request creates the table. No migration to run. */
private function _ael_create_table()
{
    $this->db->query(
        "CREATE TABLE IF NOT EXISTS `app_entry_log` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `created_at` DATETIME NOT NULL,
            `log_date` DATE NOT NULL,
            `user_id` INT(11) NOT NULL DEFAULT 0,
            `endpoint` VARCHAR(80) NOT NULL DEFAULT '',
            `module` VARCHAR(40) NOT NULL DEFAULT '',
            `log_type` VARCHAR(10) NOT NULL DEFAULT 'entry',
            `action` VARCHAR(10) NOT NULL DEFAULT '',
            `primary_table` VARCHAR(64) NOT NULL DEFAULT '',
            `record_id` INT(11) NOT NULL DEFAULT 0,
            `ref_key` VARCHAR(40) NOT NULL DEFAULT '',
            `ref_id` VARCHAR(40) NOT NULL DEFAULT '',
            `tables_touched` VARCHAR(255) NOT NULL DEFAULT '',
            `write_count` SMALLINT(6) NOT NULL DEFAULT 0,
            `payload` TEXT NULL,
            `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
            `app_version` VARCHAR(30) NOT NULL DEFAULT '',
            `device_id` VARCHAR(80) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`),
            KEY `k_date` (`log_date`),
            KEY `k_user` (`user_id`, `log_date`),
            KEY `k_record` (`primary_table`, `record_id`),
            KEY `k_ref` (`ref_key`, `ref_id`),
            KEY `k_type` (`log_type`, `log_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

/* ------------------------------------------------------------------
 * READING THE LOG
 * ------------------------------------------------------------------ */

/** Counts for one window, keyed the way the report and the API both want them. */
private function _ael_counts($where_extra = '')
{
    $t = $this->_ael_table();
    $sql = "SELECT
              SUM(log_type='entry') AS entries,
              SUM(log_type='chat')  AS chats,
              COUNT(DISTINCT CASE WHEN log_type='entry' THEN user_id END) AS users
            FROM `$t` WHERE 1=1 $where_extra";
    $res = $this->db->query($sql);
    if (!$res) return array('entries' => 0, 'chats' => 0, 'users' => 0);
    $r = $res->row();
    return array(
        'entries' => (int) ($r ? $r->entries : 0),
        'chats'   => (int) ($r ? $r->chats : 0),
        'users'   => (int) ($r ? $r->users : 0),
    );
}

/** True once the table exists - before the first app write, it does not. */
private function _ael_ready()
{
    $debug = $this->db->db_debug;
    $this->db->db_debug = FALSE;
    $res = $this->db->query("SHOW TABLES LIKE '" . $this->_ael_table() . "'");
    $this->db->db_debug = $debug;
    return ($res && $res->num_rows() > 0);
}

/**
 * JSON: the same numbers the report shows, for anything that wants them
 * later (an app screen, the App Log page, a dashboard tile).
 */
public function app_entry_stats_api()
{
    header('Content-Type: application/json');

    if (!$this->_ael_ready()) {
        echo json_encode(array('status' => true, 'data' => array(
            'today' => array('entries' => 0, 'chats' => 0, 'users' => 0),
            'note'  => 'no app writes recorded yet',
        )));
        return;
    }

    $today = $this->_ael_counts("AND log_date = '" . date('Y-m-d') . "'");
    $week  = $this->_ael_counts("AND log_date >= '" . date('Y-m-d', strtotime('-6 days')) . "'");
    $month = $this->_ael_counts("AND log_date >= '" . date('Y-m-d', strtotime('-29 days')) . "'");
    $all   = $this->_ael_counts('');

    $t = $this->_ael_table();
    $by_module = $this->db->query(
        "SELECT module, COUNT(*) c FROM `$t`
          WHERE log_type='entry' AND log_date >= '" . date('Y-m-d', strtotime('-29 days')) . "'
          GROUP BY module ORDER BY c DESC"
    )->result_array();

    echo json_encode(array(
        'status' => true,
        'data'   => array(
            'today'        => $today,
            'last_7_days'  => $week,
            'last_30_days' => $month,
            'all_time'     => $all,
            'modules_30d'  => $by_module,
        ),
    ));
}

/**
 * JSON: was this record touched from the app?
 * Post any of: table + record_id, or ref_key + ref_id.
 */
public function app_entry_check_api()
{
    header('Content-Type: application/json');

    $in = json_decode(file_get_contents('php://input'), true);
    if (!is_array($in)) $in = $_GET;

    $table  = isset($in['table']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $in['table']) : '';
    $rec    = isset($in['record_id']) ? (int) $in['record_id'] : 0;
    $refkey = isset($in['ref_key']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $in['ref_key']) : '';
    $refid  = isset($in['ref_id']) ? substr(preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $in['ref_id']), 0, 40) : '';

    if ((!$table || !$rec) && (!$refkey || $refid === '')) {
        echo json_encode(array('status' => false, 'message' => 'Send table + record_id, or ref_key + ref_id'));
        return;
    }

    if (!$this->_ael_ready()) {
        echo json_encode(array('status' => true, 'via_app' => false, 'count' => 0, 'entries' => array()));
        return;
    }

    $t = $this->_ael_table();
    $this->db->select("l.created_at, l.endpoint, l.module, l.action, l.user_id,
                       TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS user_name", false);
    $this->db->from("$t l");
    $this->db->join('system_users u', 'u.user_id = l.user_id', 'left');
    if ($table && $rec) {
        $this->db->where('l.primary_table', strtolower($table));
        $this->db->where('l.record_id', $rec);
    } else {
        $this->db->where('l.ref_key', $refkey);
        $this->db->where('l.ref_id', $refid);
    }
    $rows = $this->db->order_by('l.id', 'DESC')->limit(50)->get()->result_array();

    echo json_encode(array(
        'status'  => true,
        'via_app' => !empty($rows),
        'count'   => count($rows),
        'entries' => $rows,
    ));
}

/** A tile for the report. */
private function _ael_card($label, $value, $sub = '')
{
    return '<div class="ael-card"><div class="ael-num">' . (int) $value . '</div>'
         . '<div class="ael-lab">' . htmlspecialchars($label) . '</div>'
         . ($sub !== '' ? '<div class="ael-sub">' . htmlspecialchars($sub) . '</div>' : '')
         . '</div>';
}

/**
 * The report page. Lives here rather than in the App Log controller
 * because this change was to stay inside Api.php - same login, same two
 * roles, just a different URL.
 */
public function app_entry_report()
{
    header('Content-Type: text/html; charset=utf-8');

    $session = $this->session->userdata('logged_in');
    $user_id = (is_array($session) && isset($session['user_id'])) ? (int) $session['user_id'] : 0;
    $role    = 0;
    if ($user_id) {
        $u = $this->db->select('user_role_id')->from('system_users')
                      ->where('user_id', $user_id)->get()->row();
        if ($u) $role = (int) $u->user_role_id;
    }

    if (!in_array($role, array(12, 41), true)) {
        echo '<!doctype html><meta charset="utf-8"><title>App Entry Log</title>'
           . '<div style="font:15px system-ui;padding:40px">Sign in to the PMS as admin or management first, '
           . 'then reopen this page. <a href="' . htmlspecialchars(page_url) . '">Go to login</a></div>';
        return;
    }

    $days = isset($_GET['days']) ? max(1, min(365, (int) $_GET['days'])) : 30;
    $q    = isset($_GET['q']) ? trim($_GET['q']) : '';
    $from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
    $t    = $this->_ael_table();

    $ready = $this->_ael_ready();

    $today = $week = $window = $all = array('entries' => 0, 'chats' => 0, 'users' => 0);
    $modules = $users = $daily = $recent = $found = array();

    if ($ready) {
        $today  = $this->_ael_counts("AND log_date = '" . date('Y-m-d') . "'");
        $week   = $this->_ael_counts("AND log_date >= '" . date('Y-m-d', strtotime('-6 days')) . "'");
        $window = $this->_ael_counts("AND log_date >= '$from'");
        $all    = $this->_ael_counts('');

        $modules = $this->db->query(
            "SELECT module, COUNT(*) c, COUNT(DISTINCT user_id) u
               FROM `$t` WHERE log_type='entry' AND log_date >= '$from'
              GROUP BY module ORDER BY c DESC"
        )->result_array();

        $users = $this->db->query(
            "SELECT l.user_id,
                    TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) name,
                    COUNT(*) c, MAX(l.created_at) last_at
               FROM `$t` l LEFT JOIN system_users u ON u.user_id = l.user_id
              WHERE l.log_type='entry' AND l.log_date >= '$from'
              GROUP BY l.user_id, name ORDER BY c DESC LIMIT 40"
        )->result_array();

        $daily = $this->db->query(
            "SELECT log_date, SUM(log_type='entry') e, SUM(log_type='chat') ch
               FROM `$t` WHERE log_date >= '" . date('Y-m-d', strtotime('-13 days')) . "'
              GROUP BY log_date ORDER BY log_date"
        )->result_array();

        $recent = $this->db->query(
            "SELECT l.*, TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) name
               FROM `$t` l LEFT JOIN system_users u ON u.user_id = l.user_id
              WHERE l.log_type='entry' ORDER BY l.id DESC LIMIT 60"
        )->result_array();

        if ($q !== '') {
            $num = (int) preg_replace('/[^0-9]/', '', $q);
            $this->db->select("l.*, TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) name", false);
            $this->db->from("`$t` l");
            $this->db->join('system_users u', 'u.user_id = l.user_id', 'left');
            $this->db->group_start()
                     ->where('l.ref_id', $q)
                     ->or_like('l.payload', $q)
                     ->or_like('l.endpoint', $q);
            if ($num) $this->db->or_where('l.record_id', $num);
            $this->db->group_end();
            $found = $this->db->order_by('l.id', 'DESC')->limit(100)->get()->result_array();
        }
    }

    $peak = 1;
    foreach ($daily as $d) { $peak = max($peak, (int) $d['e']); }

    $self = htmlspecialchars(page_url . 'mobile/Api/app_entry_report');
    ?>
<!doctype html>
<html><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Entries via App</title>
<style>
  :root { --ink:#1f2937; --muted:#6b7280; --line:#e5e7eb; --accent:#2563eb; --bg:#f6f7f9; }
  * { box-sizing:border-box; }
  body { margin:0; background:var(--bg); color:var(--ink);
         font:14px/1.5 -apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif; }
  .wrap { max-width:1180px; margin:0 auto; padding:24px 16px 60px; }
  h1 { font-size:20px; margin:0 0 4px; }
  .sub { color:var(--muted); margin:0 0 20px; }
  .cards { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:22px; }
  .ael-card { background:#fff; border:1px solid var(--line); border-radius:10px;
              padding:14px 18px; min-width:150px; flex:1 1 150px; }
  .ael-num { font-size:26px; font-weight:600; }
  .ael-lab { color:var(--muted); font-size:12px; text-transform:uppercase; letter-spacing:.04em; }
  .ael-sub { color:var(--muted); font-size:12px; margin-top:2px; }
  .panel { background:#fff; border:1px solid var(--line); border-radius:10px;
           padding:16px 18px; margin-bottom:18px; }
  .panel h2 { font-size:14px; margin:0 0 12px; text-transform:uppercase;
              letter-spacing:.04em; color:var(--muted); }
  table { width:100%; border-collapse:collapse; }
  th, td { text-align:left; padding:7px 8px; border-bottom:1px solid var(--line);
           font-size:13px; vertical-align:top; }
  th { color:var(--muted); font-weight:600; font-size:12px; }
  tr:last-child td { border-bottom:0; }
  .scroll { overflow-x:auto; }
  .bars { display:flex; align-items:flex-end; gap:6px; height:110px; }
  .bar { flex:1; background:var(--accent); border-radius:3px 3px 0 0; min-height:2px; opacity:.85; }
  .barwrap { flex:1; display:flex; flex-direction:column; justify-content:flex-end;
             align-items:center; height:100%; gap:4px; }
  .barnum { font-size:11px; color:var(--muted); }
  .barday { font-size:10px; color:var(--muted); margin-top:6px; white-space:nowrap; }
  .pill { display:inline-block; padding:1px 7px; border-radius:20px; font-size:11px;
          background:#eef2ff; color:#3730a3; }
  .pill.update { background:#fef3c7; color:#92400e; }
  .pill.create { background:#dcfce7; color:#166534; }
  .pill.delete { background:#fee2e2; color:#991b1b; }
  form.f { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:18px; }
  input, select, button { font:13px inherit; padding:7px 10px; border:1px solid var(--line);
                          border-radius:7px; background:#fff; }
  button { background:var(--accent); color:#fff; border-color:var(--accent); cursor:pointer; }
  code { background:#f3f4f6; padding:1px 5px; border-radius:4px; font-size:12px; }
  .muted { color:var(--muted); }
  .empty { color:var(--muted); padding:10px 0; }
</style></head><body><div class="wrap">

<h1>Entries via App</h1>
<p class="sub">Every create, update and delete that came through the mobile API.
   Anything done on the website is not here - that is the point of the page.</p>

<form class="f" method="get" action="<?php echo $self; ?>">
  <select name="days">
    <?php foreach (array(1 => 'Today', 7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days', 365 => 'Last year') as $dv => $dl) { ?>
      <option value="<?php echo $dv; ?>" <?php echo $days == $dv ? 'selected' : ''; ?>><?php echo $dl; ?></option>
    <?php } ?>
  </select>
  <input type="text" name="q" placeholder="find a record: DF no, id, name..." value="<?php echo htmlspecialchars($q); ?>" style="min-width:260px">
  <button type="submit">Show</button>
</form>

<?php if (!$ready) { ?>
  <div class="panel"><b>Nothing recorded yet.</b>
    <p class="muted">The log table is created by the first write that arrives from the app.
    Until someone saves something in the app, this page stays empty.</p></div>
<?php } else { ?>

<div class="cards">
  <?php
    echo $this->_ael_card('Entries today', $today['entries'], $today['users'] . ' people');
    echo $this->_ael_card('Last 7 days', $week['entries'], $week['users'] . ' people');
    echo $this->_ael_card('Last ' . $days . ' days', $window['entries'], $window['users'] . ' people');
    echo $this->_ael_card('All time', $all['entries'], 'since logging began');
    echo $this->_ael_card('Chat messages', $all['chats'], 'counted separately');
  ?>
</div>

<?php if ($q !== '') { ?>
<div class="panel">
  <h2>Search: <?php echo htmlspecialchars($q); ?> (<?php echo count($found); ?> hits)</h2>
  <?php if (!$found) { ?>
    <div class="empty">No app activity matches that. If the record exists in the PMS,
      it was created or last changed on the website.</div>
  <?php } else { ?>
  <div class="scroll"><table>
    <tr><th>When</th><th>Who</th><th>Module</th><th>Action</th><th>Record</th><th>Sent</th></tr>
    <?php foreach ($found as $r) { ?>
      <tr>
        <td><?php echo htmlspecialchars($r['created_at']); ?></td>
        <td><?php echo htmlspecialchars($r['name'] ?: ('#' . $r['user_id'])); ?></td>
        <td><?php echo htmlspecialchars($r['module']); ?> <span class="muted"><?php echo htmlspecialchars($r['endpoint']); ?></span></td>
        <td><span class="pill <?php echo htmlspecialchars($r['action']); ?>"><?php echo htmlspecialchars($r['action']); ?></span></td>
        <td><code><?php echo htmlspecialchars($r['primary_table'] . '#' . $r['record_id']); ?></code></td>
        <td class="muted"><?php echo htmlspecialchars(substr((string) $r['payload'], 0, 160)); ?></td>
      </tr>
    <?php } ?>
  </table></div>
  <?php } ?>
</div>
<?php } ?>

<div class="panel">
  <h2>Entries per day (last 14 days)</h2>
  <?php if (!$daily) { ?><div class="empty">Nothing yet.</div><?php } else { ?>
  <div class="bars">
    <?php foreach ($daily as $d) { $h = max(2, round(((int) $d['e'] / $peak) * 90)); ?>
      <div class="barwrap">
        <div class="barnum"><?php echo (int) $d['e']; ?></div>
        <div class="bar" style="height:<?php echo $h; ?>px"></div>
      </div>
    <?php } ?>
  </div>
  <div style="display:flex;gap:6px">
    <?php foreach ($daily as $d) { ?>
      <div class="barday" style="flex:1;text-align:center"><?php echo htmlspecialchars(date('d M', strtotime($d['log_date']))); ?></div>
    <?php } ?>
  </div>
  <?php } ?>
</div>

<div class="panel">
  <h2>By module - last <?php echo $days; ?> days</h2>
  <?php if (!$modules) { ?><div class="empty">No entries in this window.</div><?php } else { ?>
  <table>
    <tr><th>Module</th><th>Entries</th><th>People</th></tr>
    <?php foreach ($modules as $m) { ?>
      <tr><td><?php echo htmlspecialchars($m['module']); ?></td>
          <td><?php echo (int) $m['c']; ?></td>
          <td><?php echo (int) $m['u']; ?></td></tr>
    <?php } ?>
  </table>
  <?php } ?>
</div>

<div class="panel">
  <h2>By person - last <?php echo $days; ?> days</h2>
  <?php if (!$users) { ?><div class="empty">No entries in this window.</div><?php } else { ?>
  <div class="scroll"><table>
    <tr><th>Person</th><th>Entries</th><th>Last entry</th></tr>
    <?php foreach ($users as $u) { ?>
      <tr><td><?php echo htmlspecialchars(trim($u['name']) ?: ('user #' . $u['user_id'])); ?></td>
          <td><?php echo (int) $u['c']; ?></td>
          <td class="muted"><?php echo htmlspecialchars($u['last_at']); ?></td></tr>
    <?php } ?>
  </table></div>
  <?php } ?>
</div>

<div class="panel">
  <h2>Latest 60 entries</h2>
  <?php if (!$recent) { ?><div class="empty">Nothing yet.</div><?php } else { ?>
  <div class="scroll"><table>
    <tr><th>When</th><th>Who</th><th>Module</th><th>Action</th><th>Record</th><th>Sent</th></tr>
    <?php foreach ($recent as $r) { ?>
      <tr>
        <td style="white-space:nowrap"><?php echo htmlspecialchars($r['created_at']); ?></td>
        <td><?php echo htmlspecialchars(trim($r['name']) ?: ('#' . $r['user_id'])); ?></td>
        <td><?php echo htmlspecialchars($r['module']); ?><br><span class="muted"><?php echo htmlspecialchars($r['endpoint']); ?></span></td>
        <td><span class="pill <?php echo htmlspecialchars($r['action']); ?>"><?php echo htmlspecialchars($r['action']); ?></span></td>
        <td><code><?php echo htmlspecialchars($r['primary_table'] . '#' . $r['record_id']); ?></code>
            <?php if ($r['ref_key']) { ?><br><span class="muted"><?php echo htmlspecialchars($r['ref_key'] . '=' . $r['ref_id']); ?></span><?php } ?></td>
        <td class="muted"><?php echo htmlspecialchars(substr((string) $r['payload'], 0, 160)); ?></td>
      </tr>
    <?php } ?>
  </table></div>
  <?php } ?>
</div>

<?php } ?>

<p class="muted" style="font-size:12px">
  Times are server time (IST). Chat messages are recorded but counted separately.
  Plumbing writes - push tokens, read receipts, notification rows, the presence
  row every chat poll writes - are not recorded at all, so the number above
  stays "records people actually entered".
</p>

</div></body></html>
    <?php
}


/* ======================================================================
 *  DF CHANGE CONTROL  (ECN / IOM / OTHERS)  -  the app side of
 *  application/controllers/Df_change_control.php
 *
 *  Lifecycle, unchanged from the web:
 *      create   -> PENDING_APPROVAL, one row per notified department
 *      approve  -> user 139 (Shubham Sir) only; OPEN, departments released
 *                  to their heads as PENDING_HEAD_ACTION
 *      assign   -> the department head sets a TAT and picks an assignee
 *      execute  -> the assignee moves it IN_PROGRESS then COMPLETED
 *
 *  Df_change_control_model is loaded, never modified: every query the web
 *  runs is reused so the two cannot drift. What lives in the web
 *  *controller* - the permission rules and the four write paths - is
 *  mirrored here, because none of it is reachable from a model.
 *
 *  The web reads the session for identity and admin-ness; there is no
 *  session here, so _ecn_me() rebuilds both from the posted user_id and
 *  getUserType(), the same substitution the help-desk endpoints make.
 * ==================================================================== */

/** Mirrors Df_change_control::$admin_user_ids. */
private function _ecn_admins()
{
    return array(61, 139, 161, 162, 167);
}

/** Only this user may approve. Mirrors the hard-coded 139 on the web. */
private function _ecn_approver() { return 139; }

private function _ecn_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json) && !empty($json)) return $json;
    }
    $post = $this->input->post();
    return is_array($post) ? $post : array();
}

/**
 * Identity plus the admin flag the web takes from the session.
 *
 * getUserType() returns 1 admin / 2 team leader / 3 everyone else, which is
 * exactly what the session's 'adminuser' holds, so is_admin here means the
 * same thing as is_admin_user() there: the flag, or membership of the
 * hard-coded id list.
 */
private function _ecn_me($in)
{
    $id = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
    if ($id <= 0) return null;

    $role = (int) (isset($in['role_id']) ? $in['role_id'] : 0);
    $type = (int) $this->getUserType($role, $id);

    $row = $this->db->select('user_id, title, first_name, last_name, email, department_id')
                    ->from('system_users')->where('user_id', $id)->get()->row_array();
    if (empty($row)) return null;

    return array(
        'id'            => $id,
        'role'          => $role,
        'type'          => $type,
        'is_admin'      => ($type === 1 || in_array($id, $this->_ecn_admins(), true)),
        'is_approver'   => ($id === $this->_ecn_approver()),
        'department_id' => (int) $row['department_id'],
        'name'          => $this->_ecn_name($row['first_name'], $row['last_name'], $row['title']),
        'email'         => (string) $row['email'],
    );
}

private function _ecn_model()
{
    $this->load->model('Df_change_control_model', 'change_model');
    return $this->change_model;
}

private function _ecn_out($payload) { echo json_encode($payload); }

private function _ecn_fail($message, $extra = array())
{
    $this->_ecn_out(array_merge(array('status' => false, 'message' => $message), $extra));
}

private function _ecn_name($first, $last, $title = '')
{
    $name = trim(trim((string) $title) . ' ' . trim((string) $first) . ' ' . trim((string) $last));
    return $name === '' ? '' : ucwords(strtolower($name));
}

/**
 * Mirrors Df_change_control::has_change_control_capability().
 *
 * Note the schema quirk it depends on: module_access.role_id and
 * module_capablity.role_id hold a *user* id, not a role id.
 */
private function _ecn_capability($user_id, $submodule_ids)
{
    $user_id = (int) $user_id;
    $submodule_ids = array_values(array_unique(array_filter(array_map('intval', (array) $submodule_ids))));
    if ($user_id <= 0 || empty($submodule_ids)) return false;

    $has_module = $this->db->select('id')->from('module_access')
        ->where('role_id', $user_id)->where('moduleid', 3)->where('access', '1')
        ->limit(1)->get()->num_rows() > 0;
    if (!$has_module) return false;

    return $this->db->select('submoduleid')->from('module_capablity')
        ->where('role_id', $user_id)->where('moduleid', 3)
        ->where_in('submoduleid', $submodule_ids)->where('submodule_access', '1')
        ->limit(1)->get()->num_rows() > 0;
}

/** Mirrors Df_change_control::can_raise_change_request(). */
private function _ecn_can_create($me)
{
    if (empty($me)) return false;
    if (!empty($me['is_admin'])) return true;
    return $this->_ecn_capability($me['id'], array(41));
}

/** Mirrors Df_change_control::can_access_change_dashboard(). */
private function _ecn_can_dashboard($me)
{
    if (empty($me)) return false;
    if (!empty($me['is_admin'])) return true;
    if ($this->_ecn_capability($me['id'], array(42))) return true;

    $model = $this->_ecn_model();
    if (!$model->module_ready()) return false;
    if ($model->is_department_head($me['id'])) return true;
    if (count($model->get_head_queue($me['id'])) > 0) return true;
    if (count($model->get_assigned_queue($me['id'])) > 0) return true;
    return false;
}

/** Mirrors Df_change_control::can_view_change_request(). */
private function _ecn_can_view($me, $change_id)
{
    $change_id = (int) $change_id;
    if (empty($me) || $change_id <= 0) return false;

    $model = $this->_ecn_model();
    $change = $model->get_change_request($change_id);
    if (empty($change)) return false;

    // Before approval the request is private to its author and the approver.
    if (!$model->allows_department_work($change['status'])) {
        return $me['id'] === $this->_ecn_approver() || (int) $change['created_by'] === (int) $me['id'];
    }

    if (!empty($me['is_admin']) || $this->_ecn_can_dashboard($me) || $this->_ecn_can_create($me)) return true;
    if ((int) $change['created_by'] === (int) $me['id']) return true;

    return $this->db->select('id')->from('df_change_control_departments')
        ->where('change_id', $change_id)
        ->group_start()->where('department_head_id', (int) $me['id'])
        ->or_where('assigned_user_id', (int) $me['id'])->group_end()
        ->limit(1)->get()->num_rows() > 0;
}

private function _ecn_attachment_url($file)
{
    $file = trim((string) $file);
    if ($file === '') return '';
    if (stripos($file, 'http://') === 0 || stripos($file, 'https://') === 0) return $file;
    return page_url22 . 'image_bank/df_change_control/' . rawurlencode($file);
}

/** The web's status vocabulary, spelled for a phone screen. */
private function _ecn_status_label($status)
{
    $map = array(
        'PENDING_APPROVAL'    => 'Awaiting Shubham Sir',
        'OPEN'                => 'Approved',
        'IN_PROGRESS'         => 'In progress',
        'COMPLETED'           => 'Completed',
        'REJECTED'            => 'Rejected',
        'PENDING_HEAD_ACTION' => 'Awaiting HOD',
        'ASSIGNED'            => 'Assigned',
    );
    $status = strtoupper(trim((string) $status));
    return isset($map[$status]) ? $map[$status] : ucwords(strtolower(str_replace('_', ' ', $status)));
}

private function _ecn_date($value, $format = 'd M Y')
{
    $value = trim((string) $value);
    if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') return '';
    $ts = strtotime($value);
    return $ts ? date($format, $ts) : '';
}

/**
 * The DF label, however the row reached us.
 *
 * Most queries wrap df_no in a CASE that turns df_id 0 into "Others", but
 * get_approval_queue() selects d.df_no raw, so an "Others" request arrives
 * there with a NULL and would show a blank DF in the approval list.
 */
private function _ecn_df_no($row)
{
    $df_no = trim((string) (isset($row['df_no']) ? $row['df_no'] : ''));
    if ($df_no !== '') return $df_no;
    return ((int) (isset($row['df_id']) ? $row['df_id'] : 0) === 0) ? 'Others' : '';
}

/**
 * Who raised it. get_recent_changes() and get_change_request() build
 * creator_name themselves; get_approval_queue() returns the raw name
 * columns, so it is assembled here rather than left blank.
 */
private function _ecn_creator_name($row)
{
    $name = trim((string) (isset($row['creator_name']) ? $row['creator_name'] : ''));
    if ($name !== '') return $name;

    return $this->_ecn_name(
        isset($row['creator_first_name']) ? $row['creator_first_name']
            : (isset($row['first_name']) ? $row['first_name'] : ''),
        isset($row['creator_last_name']) ? $row['creator_last_name']
            : (isset($row['last_name']) ? $row['last_name'] : ''),
        /* Never fall back to $row['title'] here: on a change row that is the
           request's own title, not an honorific, and it would be glued onto
           the front of the person's name. */
        isset($row['creator_title']) ? $row['creator_title'] : ''
    );
}

/** One request, flattened for the list and the detail header. */
private function _ecn_shape_change($row)
{
    if (empty($row)) return null;

    $status = strtoupper((string) (isset($row['status']) ? $row['status'] : ''));
    $total     = (int) (isset($row['department_count']) ? $row['department_count'] : 0);
    $completed = (int) (isset($row['completed_department_count']) ? $row['completed_department_count'] : 0);

    return array(
        'id'              => (int) $row['id'],
        'change_no'       => (string) (isset($row['change_no']) ? $row['change_no'] : ''),
        'request_type'    => (string) (isset($row['request_type']) ? $row['request_type'] : ''),
        'change_category' => (string) (isset($row['change_category']) ? $row['change_category'] : ''),
        'priority'        => (string) (isset($row['priority']) ? $row['priority'] : ''),
        'source_of_change'=> (string) (isset($row['source_of_change']) ? $row['source_of_change'] : ''),
        'reference_no'    => (string) (isset($row['reference_no']) ? $row['reference_no'] : ''),
        'revision_no'     => (string) (isset($row['revision_no']) ? $row['revision_no'] : ''),
        'title'           => (string) (isset($row['title']) ? $row['title'] : ''),
        'change_summary'  => (string) (isset($row['change_summary']) ? $row['change_summary'] : ''),
        'impact_note'     => (string) (isset($row['impact_note']) ? $row['impact_note'] : ''),
        'df_id'           => (int) (isset($row['df_id']) ? $row['df_id'] : 0),
        'df_no'           => $this->_ecn_df_no($row),
        'df_description'  => (string) (isset($row['df_description']) ? $row['df_description'] : ''),
        'status'          => $status,
        'status_label'    => $this->_ecn_status_label($status),
        'created_by'      => (int) (isset($row['created_by']) ? $row['created_by'] : 0),
        'creator_name'    => $this->_ecn_creator_name($row),
        'created_on'      => $this->_ecn_date(isset($row['created_on']) ? $row['created_on'] : '', 'd M Y, h:i A'),
        'closed_on'       => $this->_ecn_date(isset($row['closed_on']) ? $row['closed_on'] : ''),
        'attachment'      => $this->_ecn_attachment_url(isset($row['attachment']) ? $row['attachment'] : ''),
        'departments_list'=> (string) (isset($row['departments_list']) ? $row['departments_list'] : ''),
        'department_count'          => $total,
        'completed_department_count'=> $completed,
        'pending_department_count'  => (int) (isset($row['pending_department_count']) ? $row['pending_department_count'] : 0),
        'active_department_count'   => (int) (isset($row['active_department_count']) ? $row['active_department_count'] : 0),
        'overdue_department_count'  => (int) (isset($row['overdue_department_count']) ? $row['overdue_department_count'] : 0),
        'progress'        => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
    );
}

/** One department action row. */
private function _ecn_shape_action($row, $me = null)
{
    if (empty($row)) return null;

    $status  = strtoupper((string) (isset($row['status']) ? $row['status'] : ''));
    $target  = isset($row['target_date']) ? (string) $row['target_date'] : '';
    $overdue = false;
    if (in_array($status, array('ASSIGNED', 'IN_PROGRESS'), true) && $this->_ecn_date($target) !== '') {
        $overdue = strtotime($target) < strtotime(date('Y-m-d'));
    }

    $head_id     = (int) (isset($row['department_head_id']) ? $row['department_head_id'] : 0);
    $assignee_id = (int) (isset($row['assigned_user_id']) ? $row['assigned_user_id'] : 0);
    $my_id       = !empty($me) ? (int) $me['id'] : 0;
    $is_admin    = !empty($me) && !empty($me['is_admin']);

    return array(
        'id'                 => (int) $row['id'],
        'change_id'          => (int) (isset($row['change_id']) ? $row['change_id'] : 0),
        'department_id'      => (int) (isset($row['department_id']) ? $row['department_id'] : 0),
        'department'         => (string) (isset($row['department']) ? $row['department'] : ''),
        'department_head_id' => $head_id,
        'head_name'          => $this->_ecn_name(
            isset($row['head_first_name']) ? $row['head_first_name'] : '',
            isset($row['head_last_name']) ? $row['head_last_name'] : '',
            isset($row['head_title']) ? $row['head_title'] : ''),
        'assigned_user_id'   => $assignee_id,
        'assignee_name'      => $this->_ecn_name(
            isset($row['assignee_first_name']) ? $row['assignee_first_name'] : '',
            isset($row['assignee_last_name']) ? $row['assignee_last_name'] : '',
            isset($row['assignee_title']) ? $row['assignee_title'] : ''),
        'planned_days'       => (int) (isset($row['planned_days']) ? $row['planned_days'] : 0),
        'target_date'        => $this->_ecn_date($target),
        'target_date_raw'    => $this->_ecn_date($target, 'Y-m-d'),
        'is_overdue'         => $overdue,
        'status'             => $status,
        'status_label'       => $this->_ecn_status_label($status),
        'head_remarks'       => (string) (isset($row['head_remarks']) ? $row['head_remarks'] : ''),
        'assignee_remarks'   => (string) (isset($row['assignee_remarks']) ? $row['assignee_remarks'] : ''),
        'assigned_on'        => $this->_ecn_date(isset($row['assigned_on']) ? $row['assigned_on'] : '', 'd M Y, h:i A'),
        'completed_on'       => $this->_ecn_date(isset($row['completed_on']) ? $row['completed_on'] : '', 'd M Y, h:i A'),

        /* What this particular user is allowed to do with this row, so the
           app never draws a button the web would refuse. */
        'can_assign'  => ($status === 'PENDING_HEAD_ACTION') && ($is_admin || $head_id === $my_id),
        'can_execute' => in_array($status, array('ASSIGNED', 'IN_PROGRESS'), true)
                         && ($is_admin || $assignee_id === $my_id),
    );
}

/** Mirrors Df_change_control::notify_users(), plus a push the web has no way to send. */
private function _ecn_notify($user_ids, $subject, $message, $email_html, $change_id = 0)
{
    $user_ids = array_values(array_unique(array_filter(array_map('intval', (array) $user_ids))));
    if (empty($user_ids)) return;

    $users = $this->db->select('user_id, email')->from('system_users')
        ->where_in('user_id', $user_ids)->where('user_status', 1)->get()->result_array();

    foreach ($users as $user) {
        $this->db->insert('df_support_notifications', array(
            'user_id'    => (int) $user['user_id'],
            'message'    => $message,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ));

        if (trim((string) $user['email']) !== '') {
            $this->db->insert('queue_emails', array(
                'to_email'   => trim((string) $user['email']),
                'subject'    => $subject,
                'message'    => $email_html,
                'attachment' => '',
                'status'     => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }

        $this->_ecn_push((int) $user['user_id'], $subject, $message, $change_id);
    }
}

private function _ecn_push($user_id, $title, $body, $change_id)
{
    try {
        if (!function_exists('sendFCMData')) return;

        $devices = $this->db->select('fcm_token')->from('user_devices')
            ->where('user_id', (int) $user_id)->where('fcm_token !=', '')->get()->result();

        $preview = mb_substr((string) $body, 0, 140);

        foreach ($devices as $d) {
            if (empty($d->fcm_token)) continue;
            sendFCMData($d->fcm_token, $title, $preview, array(
                'type'      => 'ecn',
                'change_id' => (string) (int) $change_id,
                'screen'    => 'ecn_detail',
                'timestamp' => date('Y-m-d H:i:s'),
            ));
        }
    } catch (Throwable $e) {
        log_message('error', 'ECN push failed: ' . $e->getMessage());
    }
}

/** A trimmed version of Df_change_control::build_change_email(). */
private function _ecn_email($heading, $change, $extra_html = '')
{
    $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
    $df  = trim(((!empty($change['df_no'])) ? $change['df_no'] : '-') . ' ' .
                ((!empty($change['df_description'])) ? $change['df_description'] : ''));

    $html  = '<p>' . $esc($heading) . '</p>';
    $html .= '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:13px;">';
    $html .= '<tr><td><strong>Reference</strong></td><td>' . $esc($change['change_no']) . '</td></tr>';
    $html .= '<tr><td><strong>Type</strong></td><td>' . $esc($change['request_type']) . ' / ' . $esc($change['change_category']) . '</td></tr>';
    $html .= '<tr><td><strong>DF</strong></td><td>' . $esc($df) . '</td></tr>';
    $html .= '<tr><td><strong>Title</strong></td><td>' . $esc($change['title']) . '</td></tr>';
    $html .= '<tr><td><strong>Priority</strong></td><td>' . $esc($change['priority']) . '</td></tr>';
    $html .= '<tr><td><strong>Summary</strong></td><td>' . nl2br($esc($change['change_summary'])) . '</td></tr>';
    $html .= '</table>';
    if ($extra_html !== '') $html .= $extra_html;
    $html .= '<p><a href="' . page_url . 'Df_change_control/view/' . (int) $change['id'] . '">Open the request</a></p>';

    return $html;
}

/** Everything the app needs to draw the create form and the tab bar. */
public function ecn_bootstrap()
{
    try {
        $in = $this->_ecn_input();
        $me = $this->_ecn_me($in);
        if (!$me) return $this->_ecn_fail('auth');

        $model = $this->_ecn_model();
        if (!$model->module_ready()) return $this->_ecn_fail('module_not_ready');

        $can_create    = $this->_ecn_can_create($me);
        $can_dashboard = $this->_ecn_can_dashboard($me);

        $departments = array();
        foreach ($model->get_department_options() as $d) {
            $departments[] = array(
                'id'    => (int) $d['department_id'],
                'name'  => (string) $d['department'],
                'color' => (string) (isset($d['color']) ? $d['color'] : ''),
            );
        }

        $dfs = array(array('id' => 0, 'label' => 'Others'));
        foreach ($model->get_df_options() as $d) {
            $dfs[] = array(
                'id'    => (int) $d['id'],
                'label' => trim($d['df_no'] . ' - ' . $d['df_description']),
            );
        }

        $stats = $model->get_dashboard_stats($me['id']);

        $this->_ecn_out(array(
            'status' => true,
            'me' => array(
                'id'            => $me['id'],
                'name'          => $me['name'],
                'is_admin'      => (bool) $me['is_admin'],
                'can_create'    => (bool) $can_create,
                'can_dashboard' => (bool) $can_dashboard,
                'can_approve'   => (bool) $me['is_approver'],
            ),
            'counts' => array(
                'approval' => count($model->get_approval_queue($me['id'])),
                'head'     => count($model->get_head_queue($me['id'])),
                'assigned' => count($model->get_assigned_queue($me['id'])),
                'mine'     => count($model->get_my_requests($me['id'])),
            ),
            'stats'   => $stats,
            'options' => array(
                'request_types' => array('ECN', 'IOM', 'OTHERS'),
                'categories'    => array('REWORK', 'ADDON', 'REVISION', 'CLIENT_CHANGE', 'DEPARTMENT_CHANGE', 'CORRECTION'),
                'priorities'    => array('CRITICAL', 'HIGH', 'MEDIUM', 'NORMAL'),
                'sources'       => array('CLIENT', 'OTHER_DEPARTMENT', 'MANAGEMENT', 'SITE_FEEDBACK', 'INTERNAL_TEAM'),
                'departments'   => $departments,
                'dfs'           => $dfs,
            ),
        ));
    } catch (Throwable $e) {
        $this->_ecn_fail('Unable to load change control: ' . $e->getMessage());
    }
}

/**
 * One tab of the dashboard.
 *
 * scope: all | mine | approval | head | assigned. Each one is a model query
 * the web already runs, so a row the app shows is a row the browser shows.
 */
public function ecn_list()
{
    try {
        $in = $this->_ecn_input();
        $me = $this->_ecn_me($in);
        if (!$me) return $this->_ecn_fail('auth');

        $model = $this->_ecn_model();
        if (!$model->module_ready()) return $this->_ecn_fail('module_not_ready');

        $scope = strtolower(trim((string) (isset($in['scope']) ? $in['scope'] : 'all')));
        $rows  = array();
        $kind  = 'change';

        if ($scope === 'mine') {
            $rows = $model->get_my_requests($me['id']);
        } elseif ($scope === 'approval') {
            if (!$me['is_approver']) return $this->_ecn_fail('forbidden');
            $rows = $model->get_approval_queue($me['id']);
        } elseif ($scope === 'head') {
            $rows = $model->get_head_queue($me['id']);
            $kind = 'action';
        } elseif ($scope === 'assigned') {
            $rows = $model->get_assigned_queue($me['id']);
            $kind = 'action';
        } else {
            $scope = 'all';
            if (!$this->_ecn_can_dashboard($me)) {
                // No dashboard rights still means you can see your own.
                $rows = $model->get_my_requests($me['id']);
            } else {
                $rows = $model->get_recent_changes(200, $me['id']);
            }
        }

        $out = array();
        foreach ($rows as $row) {
            if ($kind === 'action') {
                $shaped = $this->_ecn_shape_action($row, $me);
                $shaped['change_no']    = (string) (isset($row['change_no']) ? $row['change_no'] : '');
                $shaped['request_type'] = (string) (isset($row['request_type']) ? $row['request_type'] : '');
                $shaped['title']        = (string) (isset($row['change_title']) ? $row['change_title'] : '');
                $shaped['priority']     = (string) (isset($row['priority']) ? $row['priority'] : '');
                $shaped['df_no']        = (string) (isset($row['df_no']) ? $row['df_no'] : '');
                $shaped['creator_name'] = (string) (isset($row['creator_name']) ? $row['creator_name'] : '');
                $out[] = $shaped;
            } else {
                $out[] = $this->_ecn_shape_change($row);
            }
        }

        $this->_ecn_out(array('status' => true, 'scope' => $scope, 'kind' => $kind, 'rows' => $out));
    } catch (Throwable $e) {
        $this->_ecn_fail('Unable to load the list: ' . $e->getMessage());
    }
}

/** One request in full: header, department actions, history, my buttons. */
public function ecn_detail()
{
    try {
        $in = $this->_ecn_input();
        $me = $this->_ecn_me($in);
        if (!$me) return $this->_ecn_fail('auth');

        $model = $this->_ecn_model();
        if (!$model->module_ready()) return $this->_ecn_fail('module_not_ready');

        $change_id = (int) (isset($in['change_id']) ? $in['change_id'] : 0);
        if ($change_id <= 0) return $this->_ecn_fail('bad_request');
        if (!$this->_ecn_can_view($me, $change_id)) return $this->_ecn_fail('forbidden');

        $change = $model->get_change_request($change_id);
        if (empty($change)) return $this->_ecn_fail('not_found');

        $actions = array();
        $members = array();
        foreach ($model->get_change_request_departments($change_id) as $row) {
            $shaped = $this->_ecn_shape_action($row, $me);
            $actions[] = $shaped;

            // The assignee picker only needs the departments this user can
            // actually assign for, which keeps the payload small.
            $dept_id = (int) $row['department_id'];
            if (!empty($shaped['can_assign']) && !isset($members[$dept_id])) {
                $list = array();
                foreach ($model->get_department_members($dept_id) as $m) {
                    $list[] = array(
                        'user_id' => (int) $m['user_id'],
                        'name'    => $this->_ecn_name($m['first_name'], $m['last_name'], $m['title']),
                    );
                }
                $members[(string) $dept_id] = $list;
            }
        }

        $history = array();
        foreach ($model->get_change_request_history($change_id) as $h) {
            $history[] = array(
                'id'          => (int) $h['id'],
                'action_type' => (string) $h['action_type'],
                'action_role' => (string) $h['action_role'],
                'action_note' => (string) $h['action_note'],
                'by_name'     => (string) (isset($h['action_by_name']) ? $h['action_by_name'] : ''),
                'department'  => (string) (isset($h['department']) ? $h['department'] : ''),
                'created_on'  => $this->_ecn_date($h['created_on'], 'd M Y, h:i A'),
                'label'       => ucwords(strtolower(str_replace('_', ' ', (string) $h['action_type']))),
            );
        }

        $shaped_change = $this->_ecn_shape_change($change);
        $shaped_change['requestor_department'] = (string) (isset($change['requestor_department_name']) ? $change['requestor_department_name'] : '');

        /* get_change_request() carries no per-department rollup - only the
           dashboard query does - so the progress bar would sit at zero on a
           request that is half finished. The rows were just loaded, so the
           counts come from those rather than from a second query. */
        $total = count($actions);
        $done = 0; $pending = 0; $active = 0; $late = 0;
        foreach ($actions as $a) {
            if ($a['status'] === 'COMPLETED') {
                $done++;
            } elseif ($a['status'] === 'PENDING_HEAD_ACTION') {
                $pending++;
            } elseif (in_array($a['status'], array('ASSIGNED', 'IN_PROGRESS'), true)) {
                $active++;
                if (!empty($a['is_overdue'])) $late++;
            }
        }
        $shaped_change['department_count']           = $total;
        $shaped_change['completed_department_count'] = $done;
        $shaped_change['pending_department_count']   = $pending;
        $shaped_change['active_department_count']    = $active;
        $shaped_change['overdue_department_count']   = $late;
        $shaped_change['progress'] = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        $this->_ecn_out(array(
            'status'  => true,
            'change'  => $shaped_change,
            'actions' => $actions,
            'members' => $members,
            'history' => $history,
            'can_approve' => $me['is_approver'] && strtoupper((string) $change['status']) === 'PENDING_APPROVAL',
        ));
    } catch (Throwable $e) {
        $this->_ecn_fail('Unable to open the request: ' . $e->getMessage());
    }
}

/**
 * Raise a request. Multipart, because of the optional attachment;
 * department_ids arrives as a CSV string or as repeated fields.
 *
 * This is Df_change_control::save() without the form_validation plumbing:
 * same insert, same change_no format, same one-row-per-department fan-out,
 * same history entries, and the same two notifications.
 */
public function ecn_create()
{
    try {
        $in = $this->_ecn_input();
        $me = $this->_ecn_me($in);
        if (!$me) return $this->_ecn_fail('auth');
        if (!$this->_ecn_can_create($me)) return $this->_ecn_fail('forbidden');

        $model = $this->_ecn_model();
        if (!$model->module_ready()) return $this->_ecn_fail('module_not_ready');

        $pick = function ($key, $default = '') use ($in) {
            return isset($in[$key]) ? trim((string) $in[$key]) : $default;
        };

        $request_type = strtoupper($pick('request_type'));
        $category     = strtoupper($pick('change_category'));
        $priority     = strtoupper($pick('priority'));
        $source       = strtoupper($pick('source_of_change'));
        $title        = $pick('title');
        $summary      = $pick('change_summary');

        if (!in_array($request_type, array('ECN', 'IOM', 'OTHERS'), true)) return $this->_ecn_fail('Choose a valid request type.');
        if ($category === '' || $priority === '' || $source === '')       return $this->_ecn_fail('Category, priority and source are required.');
        if ($title === '' || $summary === '')                             return $this->_ecn_fail('Title and change summary are required.');

        $raw_departments = isset($in['department_ids']) ? $in['department_ids'] : array();
        if (!is_array($raw_departments)) $raw_departments = explode(',', (string) $raw_departments);
        $department_ids = array_values(array_unique(array_filter(array_map('intval', $raw_departments))));
        if (empty($department_ids)) return $this->_ecn_fail('Select at least one department to notify.');

        $attachment = '';
        if (!empty($_FILES['attachment']['name'])) {
            $folder = UPLOADPATH . 'df_change_control/';
            if (!is_dir($folder)) @mkdir($folder, 0775, true);
            $ext  = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
            $name = 'df-change-' . time() . '-' . rand(1000, 9999) . ($ext !== '' ? '.' . $ext : '');
            if (@move_uploaded_file($_FILES['attachment']['tmp_name'], $folder . $name)) $attachment = $name;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_start();

        $this->db->insert('df_change_control', array(
            'change_no'        => '',
            'df_id'            => (int) $pick('df_id', '0'),
            'request_type'     => $request_type,
            'change_category'  => $category,
            'priority'         => $priority,
            'source_of_change' => $source,
            'reference_no'     => $pick('reference_no'),
            'revision_no'      => $pick('revision_no'),
            'title'            => $title,
            'change_summary'   => $summary,
            'impact_note'      => $pick('impact_note'),
            'requested_from_department_id' => (int) $me['department_id'],
            'attachment'       => $attachment,
            'status'           => 'PENDING_APPROVAL',
            'created_by'       => (int) $me['id'],
            'created_on'       => $now,
        ));
        $change_id = (int) $this->db->insert_id();
        if ($change_id <= 0) {
            $this->db->trans_complete();
            return $this->_ecn_fail('Unable to save the request.');
        }

        $change_no = $request_type . '-' . date('ymd') . '-' . str_pad($change_id, 4, '0', STR_PAD_LEFT);
        $this->db->where('id', $change_id)->update('df_change_control', array('change_no' => $change_no));

        foreach ($department_ids as $department_id) {
            $head = $model->resolve_department_head($department_id);
            $head_id = !empty($head['user_id']) ? (int) $head['user_id'] : 0;

            $this->db->insert('df_change_control_departments', array(
                'change_id'          => $change_id,
                'department_id'      => (int) $department_id,
                'department_head_id' => $head_id,
                'status'             => 'PENDING_APPROVAL',
                'notified_on'        => null,
            ));
            $action_id = (int) $this->db->insert_id();

            $model->add_history($change_id, $action_id, $me['id'], 'REQUESTER', 'DEPARTMENT_APPROVAL_PENDING',
                'Department action awaiting Shubham Sir approval for department ID ' . (int) $department_id . '.');
        }

        $model->add_history($change_id, 0, $me['id'], 'REQUESTER', 'REQUEST_CREATED', $summary);

        $change = $model->get_change_request($change_id);
        $email  = $this->_ecn_email(
            'A new DF change-control request requires Shubham Sir approval before department action.',
            $change,
            '<p><strong>Departments awaiting approval:</strong> ' . count($department_ids) . '</p>'
        );

        $this->_ecn_notify(array($this->_ecn_approver()), 'New ' . $request_type . ' Request: ' . $change_no,
            $change_no . ' is waiting for Shubham Sir approval.', $email, $change_id);
        $this->_ecn_notify(array($me['id']), 'Request Recorded: ' . $change_no,
            'Your change-control request ' . $change_no . ' is awaiting Shubham Sir approval.', $email, $change_id);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return $this->_ecn_fail('Unable to save the request.');

        $this->_ecn_out(array(
            'status' => true, 'change_id' => $change_id, 'change_no' => $change_no,
            'message' => $change_no . ' created. Awaiting Shubham Sir approval.',
        ));
    } catch (Throwable $e) {
        $this->_ecn_fail('Unable to create the request: ' . $e->getMessage());
    }
}

/** Shubham Sir approves or rejects. Mirrors Df_change_control::decide_approval(). */
public function ecn_approve()
{
    try {
        $in = $this->_ecn_input();
        $me = $this->_ecn_me($in);
        if (!$me) return $this->_ecn_fail('auth');
        if (!$me['is_approver']) return $this->_ecn_fail('Only Shubham Sir can approve or reject a change request.');

        $model = $this->_ecn_model();
        if (!$model->module_ready()) return $this->_ecn_fail('module_not_ready');

        $change_id = (int) (isset($in['change_id']) ? $in['change_id'] : 0);
        $decision  = strtoupper(trim((string) (isset($in['decision']) ? $in['decision'] : '')));
        $remarks   = trim((string) (isset($in['remarks']) ? $in['remarks'] : ''));

        if ($change_id <= 0 || !in_array($decision, array('APPROVE', 'REJECT'), true)) return $this->_ecn_fail('bad_request');
        if ($decision === 'REJECT' && $remarks === '') return $this->_ecn_fail('A rejection reason is required.');

        $this->db->trans_start();

        // The model's conditional update is what makes a decision single-use,
        // so a double tap on a slow connection cannot approve twice.
        $decided = $model->record_approval_decision($change_id, $me['id'], $decision, $remarks);

        if ($decided) {
            $change  = $model->get_change_request($change_id);
            $message = $change['change_no'] . ($decision === 'APPROVE' ? ' approved by Shubham Sir.' : ' rejected by Shubham Sir.');
            $email   = $this->_ecn_email($message, $change, '<p>' . nl2br(htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8')) . '</p>');

            if ($decision === 'APPROVE') {
                $heads = array();
                foreach ($model->get_change_request_departments($change_id) as $action) {
                    $heads[] = (int) $action['department_head_id'];
                }
                $this->_ecn_notify($heads, $message, $message . ' Department head action is now required.', $email, $change_id);
            }
            $this->_ecn_notify(array((int) $change['created_by']), $message, $message, $email, $change_id);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return $this->_ecn_fail('Unable to save the decision.');
        if (!$decided) return $this->_ecn_fail('This request is no longer awaiting approval.');

        $this->_ecn_out(array('status' => true, 'message' => $decision === 'APPROVE' ? 'Request approved.' : 'Request rejected.'));
    } catch (Throwable $e) {
        $this->_ecn_fail('Unable to record the decision: ' . $e->getMessage());
    }
}

/** The HOD sets a TAT and picks an assignee. Mirrors take_department_action(). */
public function ecn_assign()
{
    try {
        $in = $this->_ecn_input();
        $me = $this->_ecn_me($in);
        if (!$me) return $this->_ecn_fail('auth');

        $model = $this->_ecn_model();
        if (!$model->module_ready()) return $this->_ecn_fail('module_not_ready');

        $action_id = (int) (isset($in['action_id']) ? $in['action_id'] : 0);
        $action    = $model->get_department_action($action_id);
        if (empty($action)) return $this->_ecn_fail('not_found');

        if (!$model->allows_department_work($action['change_status'])) {
            return $this->_ecn_fail('This request has not been approved by Shubham Sir.');
        }
        if (empty($me['is_admin']) && (int) $action['department_head_id'] !== (int) $me['id']) {
            return $this->_ecn_fail('You are not authorised to act on this department request.');
        }

        $planned_days = (int) (isset($in['planned_days']) ? $in['planned_days'] : 0);
        $assigned_to  = (int) (isset($in['assigned_user_id']) ? $in['assigned_user_id'] : 0);
        $head_remarks = trim((string) (isset($in['head_remarks']) ? $in['head_remarks'] : ''));

        if ($planned_days < 1 || $assigned_to <= 0) {
            return $this->_ecn_fail('Set the number of days and pick a team member.');
        }

        $allowed = false;
        foreach ($model->get_department_members($action['department_id']) as $member) {
            if ((int) $member['user_id'] === $assigned_to) { $allowed = true; break; }
        }
        if (!$allowed) return $this->_ecn_fail('That person is not in the notified department.');

        $target_date = date('Y-m-d', strtotime('+' . $planned_days . ' days'));
        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();
        $this->db->where('id', $action_id)->update('df_change_control_departments', array(
            'planned_days'     => $planned_days,
            'target_date'      => $target_date,
            'assigned_user_id' => $assigned_to,
            'assigned_on'      => $now,
            'head_remarks'     => $head_remarks,
            'status'           => 'ASSIGNED',
        ));

        $assignee = $this->db->select('user_id, title, first_name, last_name')->from('system_users')
            ->where('user_id', $assigned_to)->get()->row_array();
        $assignee_name = $this->_ecn_name(
            isset($assignee['first_name']) ? $assignee['first_name'] : '',
            isset($assignee['last_name']) ? $assignee['last_name'] : '',
            isset($assignee['title']) ? $assignee['title'] : '');

        $note = 'Assigned to ' . $assignee_name . ' with TAT of ' . $planned_days . ' day(s), target date ' . date('d-M-Y', strtotime($target_date)) . '.';
        if ($head_remarks !== '') $note .= ' HOD remarks: ' . $head_remarks;
        $model->add_history($action['change_id'], $action_id, $me['id'], 'DEPARTMENT_HEAD', 'TASK_ASSIGNED', $note);

        $status_update = $model->recompute_change_status($action['change_id']);
        if ($status_update['previous_status'] !== $status_update['current_status']) {
            $model->add_history($action['change_id'], $action_id, $me['id'], 'SYSTEM', 'REQUEST_STATUS_UPDATED',
                'Master request status changed to ' . $status_update['current_status'] . '.');
        }

        $change = $model->get_change_request($action['change_id']);
        $email  = $this->_ecn_email('A department head has assigned this change-control action.', $change,
            '<p><strong>Department:</strong> ' . htmlspecialchars((string) $action['department'], ENT_QUOTES, 'UTF-8') .
            '<br><strong>Target date:</strong> ' . date('d-M-Y', strtotime($target_date)) .
            '<br><strong>Assignee:</strong> ' . htmlspecialchars($assignee_name, ENT_QUOTES, 'UTF-8') . '</p>');

        $this->_ecn_notify(array($assigned_to, (int) $change['created_by']),
            $change['change_no'] . ' assigned for execution',
            $change['change_no'] . ' has been assigned to you by ' . $me['name'] . '.', $email, $action['change_id']);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return $this->_ecn_fail('Unable to assign right now.');

        $this->_ecn_out(array('status' => true, 'message' => 'Assigned to ' . $assignee_name . '.'));
    } catch (Throwable $e) {
        $this->_ecn_fail('Unable to assign: ' . $e->getMessage());
    }
}

/** The assignee reports progress. Mirrors process_department_execution_update(). */
public function ecn_execute()
{
    try {
        $in = $this->_ecn_input();
        $me = $this->_ecn_me($in);
        if (!$me) return $this->_ecn_fail('auth');

        $model = $this->_ecn_model();
        if (!$model->module_ready()) return $this->_ecn_fail('module_not_ready');

        $action_id = (int) (isset($in['action_id']) ? $in['action_id'] : 0);
        $action    = $model->get_department_action($action_id);
        if (empty($action)) return $this->_ecn_fail('not_found');

        if (!$model->allows_department_work($action['change_status'])) {
            return $this->_ecn_fail('This request has not been approved by Shubham Sir.');
        }
        if (empty($me['is_admin']) && (int) $action['assigned_user_id'] !== (int) $me['id']) {
            return $this->_ecn_fail('You are not authorised to update this task.');
        }

        $status  = strtoupper(trim((string) (isset($in['execution_status']) ? $in['execution_status'] : '')));
        $remarks = trim((string) (isset($in['remarks']) ? $in['remarks'] : ''));

        if (!in_array($status, array('IN_PROGRESS', 'COMPLETED'), true) || $remarks === '') {
            return $this->_ecn_fail('Pick a status and add remarks.');
        }
        if (!in_array((string) $action['status'], array('ASSIGNED', 'IN_PROGRESS'), true)) {
            return $this->_ecn_fail('Only an assigned or in-progress task can be updated.');
        }

        $now = date('Y-m-d H:i:s');
        $update = array('status' => $status, 'assignee_remarks' => $remarks);
        if ($status === 'COMPLETED') {
            $update['completed_on'] = $now;
            $update['completed_by'] = (int) $me['id'];
        }

        $this->db->trans_start();
        $this->db->where('id', $action_id)->update('df_change_control_departments', $update);

        $model->add_history($action['change_id'], $action_id, $me['id'], 'ASSIGNEE',
            $status === 'COMPLETED' ? 'TASK_COMPLETED' : 'TASK_IN_PROGRESS', $remarks);

        $status_update = $model->recompute_change_status($action['change_id']);
        if ($status_update['previous_status'] !== $status_update['current_status']) {
            $model->add_history($action['change_id'], $action_id, $me['id'], 'SYSTEM', 'REQUEST_STATUS_UPDATED',
                'Master request status changed to ' . $status_update['current_status'] . '.');
        }
        if ($status_update['current_status'] === 'COMPLETED' && $status_update['previous_status'] !== 'COMPLETED') {
            $this->db->where('id', (int) $action['change_id'])->update('df_change_control', array(
                'closed_by' => (int) $me['id'], 'closed_on' => $now,
            ));
        }

        $change = $model->get_change_request($action['change_id']);
        $email  = $this->_ecn_email('A department execution update has been submitted.', $change,
            '<p><strong>Department:</strong> ' . htmlspecialchars((string) $action['department'], ENT_QUOTES, 'UTF-8') .
            '<br><strong>Status:</strong> ' . str_replace('_', ' ', $status) .
            '<br><strong>Remarks:</strong> ' . nl2br(htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8')) . '</p>');

        $notify = array((int) $action['department_head_id'], (int) $change['created_by']);
        if ($status_update['current_status'] === 'COMPLETED') {
            $notify = array_merge($notify, $model->get_involved_user_ids($action['change_id']));
        }
        $this->_ecn_notify($notify, $change['change_no'] . ' execution update',
            $change['change_no'] . ' has been updated to ' . str_replace('_', ' ', $status) . '.', $email, $action['change_id']);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return $this->_ecn_fail('Unable to update right now.');

        $this->_ecn_out(array('status' => true, 'message' => 'Execution status updated.'));
    } catch (Throwable $e) {
        $this->_ecn_fail('Unable to update: ' . $e->getMessage());
    }
}

/**
 * The two flags get_user_permissions_api hands the app at startup.
 *
 * Kept identical to the web's own gate so the phone never shows a tile the
 * browser would hide: raising needs capability 41, the dashboard needs 42,
 * department-head status, or a queue with something actually in it.
 */
private function _ecn_perms($user_id, $role_id)
{
    try {
        $me = $this->_ecn_me(array('user_id' => $user_id, 'role_id' => $role_id));
        if (!$me) return array('visible' => false, 'can_create' => false);

        $can_create = $this->_ecn_can_create($me);
        $visible    = $can_create || $this->_ecn_can_dashboard($me);

        return array('visible' => (bool) $visible, 'can_create' => (bool) $can_create);
    } catch (Throwable $e) {
        // A module the user cannot reach is a better failure than a crashed
        // permission call that hides every other module too.
        return array('visible' => false, 'can_create' => false);
    }
}


/* ==================================================================
 * NOTIFICATIONS — clear the whole list in one call
 * ================================================================== */

/**
 * Mark every unread notification of this user as read.
 *
 * The app used to fire mark_notification_read once per row to clear a badge,
 * which after a few days away meant hundreds of requests. Scoped to the
 * caller's own user_id - unlike mark_notification_read, which trusts the id
 * it is handed - so one user can never clear another's list.
 */
public function mark_all_notifications_read()
{
    header('Content-Type: application/json');

    $input   = json_decode(file_get_contents("php://input"), true);
    $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;

    if (!$user_id) {
        echo json_encode(array("status" => false, "message" => "User ID required"));
        return;
    }

    $this->db->where('user_id', $user_id)
             ->where('is_read', 0)
             ->update('app_notifications', array('is_read' => 1));

    echo json_encode(array(
        "status"  => true,
        "cleared" => (int) $this->db->affected_rows(),
    ));
}


/* ==================================================================
 * ATTENDANCE - punch in / out, checked against an office geofence
 *
 * The phone sends nothing but raw coordinates. Every verdict - which site,
 * how far, inside or not - is computed here, because a client that reported
 * its own "I am in the office" flag would be trivial to lie to.
 * ================================================================== */

private function _att_site_table() { return 'app_attendance_site'; }
private function _att_log_table()  { return 'app_attendance_log'; }

/** First call creates both tables and seeds the main office. No migration. */
private function _att_ensure()
{
    $debug = $this->db->db_debug;
    $this->db->db_debug = FALSE;

    $this->db->query(
        "CREATE TABLE IF NOT EXISTS `app_attendance_site` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(80) NOT NULL DEFAULT '',
            `latitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
            `longitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
            `radius_m` INT(11) NOT NULL DEFAULT 200,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `k_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $this->db->query(
        "CREATE TABLE IF NOT EXISTS `app_attendance_log` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) NOT NULL DEFAULT 0,
            `punch_date` DATE NOT NULL,
            `punch_type` VARCHAR(3) NOT NULL DEFAULT 'IN',
            `punched_at` DATETIME NOT NULL,
            `latitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
            `longitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
            `accuracy_m` INT(11) NOT NULL DEFAULT 0,
            `site_id` INT(11) NOT NULL DEFAULT 0,
            `site_name` VARCHAR(80) NOT NULL DEFAULT '',
            `distance_m` INT(11) NOT NULL DEFAULT 0,
            `in_premises` TINYINT(1) NOT NULL DEFAULT 0,
            `is_mocked` TINYINT(1) NOT NULL DEFAULT 0,
            `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
            `app_version` VARCHAR(30) NOT NULL DEFAULT '',
            `device_id` VARCHAR(80) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`),
            KEY `k_user_date` (`user_id`, `punch_date`),
            KEY `k_date` (`punch_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    /* The office the user sent on 2026-09-15. Seeded once; after that the
       row is editable in the DB and the phones pick the change up on their
       next launch, so moving the pin or widening the radius needs no APK. */
    if ((int) $this->db->count_all_results($this->_att_site_table()) === 0) {
        $this->db->insert($this->_att_site_table(), array(
            'name'       => 'Main Office',
            'latitude'   => 28.3183325,
            'longitude'  => 77.3127189,
            'radius_m'   => 200,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    $this->db->db_debug = $debug;
}

/** Metres between two WGS84 points (haversine). */
private function _att_distance($lat1, $lon1, $lat2, $lon2)
{
    $R    = 6371000.0;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2)
       + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);

    return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

private function _att_sites()
{
    return $this->db->where('is_active', 1)
                    ->order_by('id', 'ASC')
                    ->get($this->_att_site_table())
                    ->result_array();
}

/** Nearest active site to a point, with the distance to it. */
private function _att_nearest($lat, $lng)
{
    $best = null;
    $bestD = null;

    foreach ($this->_att_sites() as $site) {
        $d = $this->_att_distance($lat, $lng, (float) $site['latitude'], (float) $site['longitude']);
        if ($bestD === null || $d < $bestD) {
            $bestD = $d;
            $best  = $site;
        }
    }

    return array('site' => $best, 'distance' => $bestD);
}

/** One day's punches for one user, oldest first. */
private function _att_punches($user_id, $date)
{
    return $this->db->where('user_id', (int) $user_id)
                    ->where('punch_date', $date)
                    ->order_by('punched_at', 'ASC')
                    ->order_by('id', 'ASC')
                    ->get($this->_att_log_table())
                    ->result_array();
}

/** Whether the next tap should be an IN or an OUT. */
private function _att_next($punches)
{
    if (empty($punches)) return 'IN';
    $last = end($punches);
    return strtoupper($last['punch_type']) === 'IN' ? 'OUT' : 'IN';
}

/** Minutes between each IN and the OUT that follows it. */
private function _att_worked($punches)
{
    $total = 0;
    $open  = null;

    foreach ($punches as $p) {
        if (strtoupper($p['punch_type']) === 'IN') {
            $open = strtotime($p['punched_at']);
        } elseif ($open !== null) {
            $total += (strtotime($p['punched_at']) - $open);
            $open = null;
        }
    }

    return (int) round($total / 60);
}

/** Shape one row the way the app reads it. */
private function _att_row($p)
{
    return array(
        'id'          => (int) $p['id'],
        'punch_type'  => strtoupper($p['punch_type']),
        'punched_at'  => $p['punched_at'],
        'site_name'   => $p['site_name'],
        'distance_m'  => (int) $p['distance_m'],
        'in_premises' => (int) $p['in_premises'],
        'is_mocked'   => (int) $p['is_mocked'],
        'accuracy_m'  => (int) $p['accuracy_m'],
    );
}

/**
 * The geofences a punch is measured against.
 *
 * Kept server-side so a plant or a branch can be added by inserting a row,
 * without a new APK: the app caches this list and re-fetches it on launch.
 */
public function attendance_sites_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $this->_att_ensure();

        $sites = array();
        foreach ($this->_att_sites() as $s) {
            $sites[] = array(
                'id'        => (int) $s['id'],
                'name'      => $s['name'],
                'latitude'  => (float) $s['latitude'],
                'longitude' => (float) $s['longitude'],
                'radius_m'  => (int) $s['radius_m'],
            );
        }

        echo json_encode(array('status' => true, 'sites' => $sites));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/**
 * Record a punch.
 *
 * punch_type is a hint, not an instruction: the server re-derives what the
 * next punch should be from what is already stored, so a stale screen (or a
 * hand-rolled request) cannot log two INs in a row.
 */
public function attendance_punch_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        if (!isset($input['latitude']) || !isset($input['longitude'])) {
            echo json_encode(array('status' => false, 'message' => 'Location is required to punch'));
            return;
        }

        $lat = (float) $input['latitude'];
        $lng = (float) $input['longitude'];

        /* 0,0 is in the Atlantic - it means "the fix failed", not "here". */
        if (abs($lat) > 90 || abs($lng) > 180 || ($lat == 0 && $lng == 0)) {
            echo json_encode(array('status' => false, 'message' => 'Location reading looks wrong. Try again.'));
            return;
        }

        $this->_att_ensure();

        $date    = date('Y-m-d');
        $now     = date('Y-m-d H:i:s');
        $punches = $this->_att_punches($user_id, $date);

        /* A double tap on a slow connection should not become two punches. */
        if (!empty($punches)) {
            $last = end($punches);
            if ((time() - strtotime($last['punched_at'])) < 60) {
                echo json_encode(array(
                    'status'  => false,
                    'message' => 'You just punched ' . strtolower($last['punch_type']) . '. Wait a minute.',
                ));
                return;
            }
        }

        $expected = $this->_att_next($punches);
        $asked    = isset($input['punch_type']) ? strtoupper(trim($input['punch_type'])) : $expected;

        if ($asked !== $expected) {
            echo json_encode(array(
                'status'  => false,
                'message' => $expected === 'IN'
                    ? 'You are not punched in right now.'
                    : 'You are already punched in.',
                'next'    => $expected,
                'punches' => array_map(array($this, '_att_row'), $punches),
            ));
            return;
        }

        $near     = $this->_att_nearest($lat, $lng);
        $site     = $near['site'];
        $distance = $near['distance'];

        $in_premises = ($site !== null && $distance !== null
                        && $distance <= (float) $site['radius_m']) ? 1 : 0;

        $row = array(
            'user_id'     => $user_id,
            'punch_date'  => $date,
            'punch_type'  => $expected,
            'punched_at'  => $now,
            'latitude'    => $lat,
            'longitude'   => $lng,
            'accuracy_m'  => isset($input['accuracy']) ? (int) round($input['accuracy']) : 0,
            'site_id'     => $site ? (int) $site['id'] : 0,
            'site_name'   => $site ? $site['name'] : 'No site configured',
            'distance_m'  => $distance === null ? 0 : (int) round($distance),
            'in_premises' => $in_premises,
            'is_mocked'   => !empty($input['is_mocked']) ? 1 : 0,
            'ip_address'  => substr((string) $this->input->ip_address(), 0, 45),
            'app_version' => isset($input['app_version']) ? substr((string) $input['app_version'], 0, 30) : '',
            'device_id'   => isset($input['device_id']) ? substr((string) $input['device_id'], 0, 80) : '',
        );

        $this->db->insert($this->_att_log_table(), $row);

        $punches = $this->_att_punches($user_id, $date);

        echo json_encode(array(
            'status'         => true,
            'message'        => 'Punched ' . strtolower($expected),
            'punch_type'     => $expected,
            'in_premises'    => $in_premises,
            'distance_m'     => $row['distance_m'],
            'site_name'      => $row['site_name'],
            'next'           => $this->_att_next($punches),
            'punches'        => array_map(array($this, '_att_row'), $punches),
            'worked_minutes' => $this->_att_worked($punches),
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/** One user's punches for a day (today unless a date is passed). */
public function attendance_today_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        $this->_att_ensure();

        $date    = !empty($input['date']) ? date('Y-m-d', strtotime($input['date'])) : date('Y-m-d');
        $punches = $this->_att_punches($user_id, $date);

        echo json_encode(array(
            'status'         => true,
            'date'           => $date,
            'next'           => $this->_att_next($punches),
            'punches'        => array_map(array($this, '_att_row'), $punches),
            'worked_minutes' => $this->_att_worked($punches),
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/** Who may look at everybody's punches, not just their own. */
private function _att_is_admin($user_id, $role_id)
{
    return ((int) $role_id === 12 || in_array((int) $user_id, array(139, 161, 61)));
}

/**
 * Who does NOT have to answer the attendance popup.
 *
 * Four people by name (2026-09-22): Virendra Sharma (61), Aakash Sharma
 * (62), Shubham Sharma (139) and Rishabh Sharma (167). Everybody else
 * marks the day, administrators included.
 *
 * This used to BE _att_is_admin(), which meant every role-12 account and
 * every future admin silently stopped marking as a side effect of being
 * given a reporting view. The two questions are now apart: _att_is_admin
 * decides what a person may SEE, this decides what they must ANSWER, and
 * widening one no longer widens the other.
 *
 * Deliberately a list of ids and not a role or a flag - the ask was for
 * these four and nobody else, and a role would drift.
 */
private function _att_gate_exempt($user_id)
{
    return in_array((int) $user_id, array(61, 62, 139, 167), true);
}

/**
 * Everyone's day, for the admins: first in, last out, and whether each
 * punch was inside the fence.
 */
public function attendance_report_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;
        $role_id = isset($input['role_id']) ? (int) $input['role_id'] : 0;

        if (!$this->_att_is_admin($user_id, $role_id)) {
            echo json_encode(array('status' => false, 'message' => 'Not allowed'));
            return;
        }

        $this->_att_ensure();

        $date = !empty($input['date']) ? date('Y-m-d', strtotime($input['date'])) : date('Y-m-d');

        $rows = $this->db
            ->select("l.*, CONCAT(u.first_name,' ',COALESCE(u.last_name,'')) AS user_name", FALSE)
            ->from($this->_att_log_table() . ' l')
            ->join('system_users u', 'u.user_id = l.user_id', 'left')
            ->where('l.punch_date', $date)
            ->order_by('l.user_id', 'ASC')
            ->order_by('l.punched_at', 'ASC')
            ->get()
            ->result_array();

        $people = array();
        foreach ($rows as $r) {
            $uid = (int) $r['user_id'];

            if (!isset($people[$uid])) {
                $people[$uid] = array(
                    'user_id'        => $uid,
                    'name'           => trim($r['user_name']) !== '' ? trim($r['user_name']) : ('User ' . $uid),
                    'first_in'       => null,
                    'last_out'       => null,
                    'punches'        => array(),
                    'worked_minutes' => 0,
                    'any_outside'    => 0,
                );
            }

            $people[$uid]['punches'][] = $this->_att_row($r);

            if (strtoupper($r['punch_type']) === 'IN' && $people[$uid]['first_in'] === null) {
                $people[$uid]['first_in'] = $r['punched_at'];
            }
            if (strtoupper($r['punch_type']) === 'OUT') {
                $people[$uid]['last_out'] = $r['punched_at'];
            }
            if ((int) $r['in_premises'] === 0) {
                $people[$uid]['any_outside'] = 1;
            }
        }

        foreach ($people as $uid => $p) {
            $people[$uid]['worked_minutes'] = $this->_att_worked($p['punches']);
        }

        echo json_encode(array(
            'status' => true,
            'date'   => $date,
            'people' => array_values($people),
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/* ==================================================================
 * DAILY ATTENDANCE MARK - the gate the app opens behind
 *
 * Separate from the punch log on purpose. A punch is an event ("I arrived
 * at 09:12"); this is the day's *declaration* - Present, Absent, On Duty or
 * Gate Pass - and it is what decides whether the app unlocks at all. One
 * row per user per day, re-markable, because a Gate Pass at noon has to be
 * able to become a Present at three.
 *
 * PRESENT also writes an IN punch, so the existing sheet, the worked-minutes
 * sum and the admin report all keep working untouched.
 * ================================================================== */

private function _att_day_table() { return 'app_attendance_day'; }

/** The four marks the popup offers. Anything else is refused. */
private function _att_statuses()
{
    return array('PRESENT', 'ABSENT', 'ON_DUTY', 'GATE_PASS');
}

/** For display, and for the HOD sheet. */
private function _att_status_label($status)
{
    $map = array(
        'PRESENT'   => 'Present',
        'ABSENT'    => 'Absent',
        'ON_DUTY'   => 'On Duty',
        'GATE_PASS' => 'Gate Pass',
    );
    $s = strtoupper((string) $status);
    return isset($map[$s]) ? $map[$s] : 'Not marked';
}

/**
 * Which marks let the user into the app.
 *
 * Absent is the only one that does not: the other three are all forms of
 * working. This is the single place that rule lives - the app asks rather
 * than deciding for itself, so it can be changed here without an APK.
 */
private function _att_grants_access($status)
{
    return strtoupper((string) $status) !== 'ABSENT';
}

/** Lazy-create, same pattern as _att_ensure(). No migration step. */
private function _att_day_ensure()
{
    $debug = $this->db->db_debug;
    $this->db->db_debug = FALSE;

    $this->db->query(
        "CREATE TABLE IF NOT EXISTS `app_attendance_day` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) NOT NULL DEFAULT 0,
            `work_date` DATE NOT NULL,
            `status` VARCHAR(12) NOT NULL DEFAULT 'PRESENT',
            `marked_at` DATETIME NOT NULL,
            `remark` VARCHAR(190) NOT NULL DEFAULT '',
            `latitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
            `longitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
            `accuracy_m` INT(11) NOT NULL DEFAULT 0,
            `site_id` INT(11) NOT NULL DEFAULT 0,
            `site_name` VARCHAR(80) NOT NULL DEFAULT '',
            `distance_m` INT(11) NOT NULL DEFAULT 0,
            `in_premises` TINYINT(1) NOT NULL DEFAULT 0,
            `is_mocked` TINYINT(1) NOT NULL DEFAULT 0,
            `changed_count` INT(11) NOT NULL DEFAULT 0,
            `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
            `app_version` VARCHAR(30) NOT NULL DEFAULT '',
            `device_id` VARCHAR(80) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_user_date` (`user_id`, `work_date`),
            KEY `k_date` (`work_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $this->db->db_debug = $debug;
}

/** One user's mark for a day, or null when they have not marked yet. */
private function _att_day($user_id, $date)
{
    $row = $this->db->where('user_id', (int) $user_id)
                    ->where('work_date', $date)
                    ->limit(1)
                    ->get($this->_att_day_table())
                    ->row_array();

    return $row ? $row : null;
}

/** Which plant a day mark was made at - 2026-10-01.
 *
 *  The app and the web Attendance Report both say "Marked from Sector 6" /
 *  "Sector 59", from the mark's own GPS point: within 1 km of a plant's pin
 *  is that plant, further from both is "Outside". The distance is always to
 *  the NEAREST plant, so "Outside" reads "2.3 km from Sector 59" instead of
 *  a distance from the one office pin, which means nothing to someone who
 *  works at Sector 6.
 *
 *  Pins given by the user from Google Maps. THE SAME TWO PINS live in
 *  controllers/Attendance_report.php - change them together. */
private function _att_plant_fields($row)
{
    $out = array('plant' => '', 'plant_distance_m' => 0, 'nearest_plant' => '');
    if (!$row || !isset($row['latitude'], $row['longitude'])) return $out;

    $lat = (float) $row['latitude'];
    $lng = (float) $row['longitude'];
    if ($lat == 0.0 && $lng == 0.0) return $out;

    $plants = array(
        'Sector 59' => array(28.31842220205592, 77.31269207540004),
        'Sector 6'  => array(28.35625357202389, 77.32118499748876),
    );

    $best = ''; $bestD = PHP_INT_MAX;
    foreach ($plants as $name => $p) {
        $d = $this->_att_distance($lat, $lng, $p[0], $p[1]);
        if ($d < $bestD) { $bestD = $d; $best = $name; }
    }

    $out['nearest_plant']    = $best;
    $out['plant_distance_m'] = (int) round($bestD);
    $out['plant']            = $bestD <= 1000 ? $best : 'Outside';
    return $out;
}

/** Shape a day row the way the app reads it. */
private function _att_day_row($d)
{
    if (!$d) return null;

    return array(
        'status'       => strtoupper($d['status']),
        'status_label' => $this->_att_status_label($d['status']),
        'marked_at'    => $d['marked_at'],
        'remark'       => (string) $d['remark'],
        'site_name'    => (string) $d['site_name'],
        'distance_m'   => (int) $d['distance_m'],
        'in_premises'  => (int) $d['in_premises'],
        'is_mocked'    => (int) $d['is_mocked'],
        'accuracy_m'   => (int) $d['accuracy_m'],
        'grants_access' => $this->_att_grants_access($d['status']) ? 1 : 0,
    ) + $this->_att_plant_fields($d);
}

/**
 * The people this user leads.
 *
 * A team is `prestogroup_teams` (team_leader) and its roster is
 * `presto_team_members.employee_id` - the same pair Lead_model walks. A user
 * who leads no team gets an empty list, which is what makes them not an HOD.
 */
private function _att_team_member_ids($user_id)
{
    if (!$this->db->table_exists('prestogroup_teams')
        || !$this->db->table_exists('presto_team_members')) {
        return array();
    }

    $rows = $this->db->select('m.employee_id')
                     ->from('prestogroup_teams t')
                     ->join('presto_team_members m', 'm.team_id = t.team_id', 'inner')
                     ->where('t.team_leader', (int) $user_id)
                     ->get()
                     ->result_array();

    $ids = array();
    foreach ($rows as $r) {
        $id = (int) $r['employee_id'];
        if ($id > 0 && $id !== (int) $user_id) $ids[$id] = $id;
    }

    return array_values($ids);
}

/** Leading at least one team is what makes someone an HOD here. */
private function _att_is_hod($user_id)
{
    return count($this->_att_team_member_ids($user_id)) > 0;
}

/**
 * Has this user marked today, and what may they see?
 *
 * The app calls this before it will show anything, so it carries the two
 * role flags as well - that saves a second round trip on every cold start.
 */
public function attendance_day_status_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;
        $role_id = isset($input['role_id']) ? (int) $input['role_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        $this->_att_ensure();
        $this->_att_day_ensure();

        $date = date('Y-m-d');
        $day  = $this->_att_day($user_id, $date);

        $is_admin = $this->_att_is_admin($user_id, $role_id);

        /* Attendance applies to everyone except four named people
         * (2026-09-22): see _att_gate_exempt().
         *
         * From 2026-09-17 until now this branch tested _att_is_admin, so
         * the exemption quietly covered every role-12 account. The ask is
         * that it covers Virendra, Aakash, Shubham Sir and Rishabh and
         * nobody else, so the popup is back for the rest - including the
         * admins who had stopped seeing it.
         *
         * Answered as "marked, and that mark grants access" rather than
         * with a new flag, on purpose: every APK already on a phone
         * understands this shape, so the change is live on the builds
         * people are carrying today instead of waiting for an update.
         * 'exempt' is along for the ride for anything written later.
         *
         * is_admin is answered truthfully rather than forced to 1 - it is
         * what opens the company-wide sheet, and an exempt person who is
         * not an admin must not be handed that view.
         *
         * Their own row is still returned when they have one, so a day an
         * exempt person did mark keeps showing in the history screen. */
        if ($this->_att_gate_exempt($user_id)) {
            echo json_encode(array(
                'status'        => true,
                'date'          => $date,
                'marked'        => true,
                'exempt'        => true,
                'day'           => $this->_att_day_row($day),
                'grants_access' => 1,
                'options'       => $this->_att_statuses(),
                'is_hod'        => $this->_att_is_hod($user_id) ? 1 : 0,
                'is_admin'      => $is_admin ? 1 : 0,
            ));
            return;
        }

        echo json_encode(array(
            'status'        => true,
            'date'          => $date,
            'marked'        => $day ? true : false,
            'day'           => $this->_att_day_row($day),
            'grants_access' => $day ? ($this->_att_grants_access($day['status']) ? 1 : 0) : 0,
            'options'       => $this->_att_statuses(),
            'is_hod'        => $this->_att_is_hod($user_id) ? 1 : 0,
            'is_admin'      => $is_admin ? 1 : 0,
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/**
 * Mark the day - the popup's only action.
 *
 * Location is required for every one of the four, not just Present: the
 * whole point of the mark is that it is anchored somewhere. Being outside
 * the fence never refuses the mark, it is simply recorded and flagged, so a
 * bad GPS fix cannot lock a real employee out of their own app.
 */
public function attendance_mark_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        /* The four exempt people are outside attendance entirely: not
         * asked, not chased, and no row is ever written for them. The popup
         * is already gone for them, so arriving here means a stale build or
         * a direct call - the answer is the same either way.
         *
         * This MUST be the same test the popup uses (2026-09-22). It was
         * _att_is_admin, which matched the gate while the gate was the admin
         * test too; the moment the gate narrowed to four names and left
         * every other administrator marking, a wider refusal here would have
         * asked them for a mark and then refused to store it - a lockout
         * with no way out, because the gate does not open until a row
         * exists. One rule, one place: _att_gate_exempt().
         *
         * It takes only a user id, so there is no field to leave out and no
         * role to look up: a refusal that can be sidestepped by omitting
         * something is not a refusal. */
        if ($this->_att_gate_exempt($user_id)) {
            echo json_encode(array(
                'status'  => false,
                'exempt'  => true,
                'message' => 'Attendance is not marked for your account.',
            ));
            return;
        }

        $status = isset($input['status']) ? strtoupper(trim($input['status'])) : '';
        if (!in_array($status, $this->_att_statuses(), TRUE)) {
            echo json_encode(array('status' => false, 'message' => 'Pick Present, Absent, On Duty or Gate Pass'));
            return;
        }

        if (!isset($input['latitude']) || !isset($input['longitude'])) {
            echo json_encode(array('status' => false, 'message' => 'Location is required to mark attendance'));
            return;
        }

        $lat = (float) $input['latitude'];
        $lng = (float) $input['longitude'];

        /* 0,0 is in the Atlantic - it means "the fix failed", not "here". */
        if (abs($lat) > 90 || abs($lng) > 180 || ($lat == 0 && $lng == 0)) {
            echo json_encode(array('status' => false, 'message' => 'Location reading looks wrong. Try again.'));
            return;
        }

        $this->_att_ensure();
        $this->_att_day_ensure();

        $date = date('Y-m-d');
        $now  = date('Y-m-d H:i:s');

        $near     = $this->_att_nearest($lat, $lng);
        $site     = $near['site'];
        $distance = $near['distance'];

        $in_premises = ($site !== null && $distance !== null
                        && $distance <= (float) $site['radius_m']) ? 1 : 0;

        $existing = $this->_att_day($user_id, $date);

        $row = array(
            'user_id'     => $user_id,
            'work_date'   => $date,
            'status'      => $status,
            'marked_at'   => $now,
            'remark'      => isset($input['remark']) ? substr((string) $input['remark'], 0, 190) : '',
            'latitude'    => $lat,
            'longitude'   => $lng,
            'accuracy_m'  => isset($input['accuracy']) ? (int) round($input['accuracy']) : 0,
            'site_id'     => $site ? (int) $site['id'] : 0,
            'site_name'   => $site ? $site['name'] : 'No site configured',
            'distance_m'  => $distance === null ? 0 : (int) round($distance),
            'in_premises' => $in_premises,
            'is_mocked'   => !empty($input['is_mocked']) ? 1 : 0,
            'ip_address'  => substr((string) $this->input->ip_address(), 0, 45),
            'app_version' => isset($input['app_version']) ? substr((string) $input['app_version'], 0, 30) : '',
            'device_id'   => isset($input['device_id']) ? substr((string) $input['device_id'], 0, 80) : '',
        );

        if ($existing) {
            /* Re-marking is allowed all day - a Gate Pass at noon becomes a
               Present at three - but every change is counted, so a sheet
               that flip-flops is visible rather than silent. */
            $row['changed_count'] = (int) $existing['changed_count'] + 1;
            $this->db->where('id', (int) $existing['id'])
                     ->update($this->_att_day_table(), $row);
        } else {
            $row['changed_count'] = 0;
            $this->db->insert($this->_att_day_table(), $row);
        }

        /* PRESENT is also an arrival, so it writes the IN punch the existing
           sheet and the worked-minutes sum are built on - but only when the
           user is not already punched in, or a re-mark would double it. */
        $punched_in = FALSE;
        if ($status === 'PRESENT') {
            $punches = $this->_att_punches($user_id, $date);

            if ($this->_att_next($punches) === 'IN') {
                $this->db->insert($this->_att_log_table(), array(
                    'user_id'     => $user_id,
                    'punch_date'  => $date,
                    'punch_type'  => 'IN',
                    'punched_at'  => $now,
                    'latitude'    => $lat,
                    'longitude'   => $lng,
                    'accuracy_m'  => $row['accuracy_m'],
                    'site_id'     => $row['site_id'],
                    'site_name'   => $row['site_name'],
                    'distance_m'  => $row['distance_m'],
                    'in_premises' => $in_premises,
                    'is_mocked'   => $row['is_mocked'],
                    'ip_address'  => $row['ip_address'],
                    'app_version' => $row['app_version'],
                    'device_id'   => $row['device_id'],
                ));
                $punched_in = TRUE;
            }
        }

        $day = $this->_att_day($user_id, $date);

        echo json_encode(array(
            'status'        => true,
            'message'       => 'Marked ' . $this->_att_status_label($status),
            'date'          => $date,
            'day'           => $this->_att_day_row($day),
            'grants_access' => $this->_att_grants_access($status) ? 1 : 0,
            'punched_in'    => $punched_in ? 1 : 0,
            'in_premises'   => $in_premises,
            'distance_m'    => $row['distance_m'],
            'site_name'     => $row['site_name'],
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/**
 * The HOD's own sheet: everyone on their teams, for one day.
 *
 * Everybody on the roster appears, including the people who have not marked
 * at all - "nobody reported" and "four people did not turn up" have to look
 * different, and a list that only shows the marks cannot tell them apart.
 */
public function attendance_team_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        /* This endpoint builds its own roster rather than going through
         * _att_scope, so the admin stripping has to be repeated here or an
         * admin sitting in someone's team would show as NOT_MARKED every
         * day forever - absence, when what is meant is exemption. */
        $member_ids = $this->_att_strip_admins($this->_att_team_member_ids($user_id));

        if (empty($member_ids)) {
            echo json_encode(array(
                'status'  => false,
                'message' => 'You do not lead a team.',
                'people'  => array(),
            ));
            return;
        }

        $this->_att_ensure();
        $this->_att_day_ensure();

        $date = !empty($input['date']) ? date('Y-m-d', strtotime($input['date'])) : date('Y-m-d');

        $users = $this->db->select("user_id, CONCAT(first_name,' ',COALESCE(last_name,'')) AS user_name", FALSE)
                          ->from('system_users')
                          ->where_in('user_id', $member_ids)
                          ->get()
                          ->result_array();

        $names = array();
        foreach ($users as $u) {
            $n = trim($u['user_name']);
            $names[(int) $u['user_id']] = $n !== '' ? $n : ('User ' . $u['user_id']);
        }

        $days = $this->db->where_in('user_id', $member_ids)
                         ->where('work_date', $date)
                         ->get($this->_att_day_table())
                         ->result_array();

        $by_user = array();
        foreach ($days as $d) $by_user[(int) $d['user_id']] = $d;

        /* First in / last out come from the punch log, so the HOD sees the
           actual arrival time and not just the moment the popup was answered. */
        $punch_rows = $this->db->where_in('user_id', $member_ids)
                               ->where('punch_date', $date)
                               ->order_by('punched_at', 'ASC')
                               ->get($this->_att_log_table())
                               ->result_array();

        $punches = array();
        foreach ($punch_rows as $p) $punches[(int) $p['user_id']][] = $p;

        $people  = array();
        $summary = array('PRESENT' => 0, 'ABSENT' => 0, 'ON_DUTY' => 0, 'GATE_PASS' => 0, 'NOT_MARKED' => 0);

        foreach ($member_ids as $uid) {
            $d  = isset($by_user[$uid]) ? $by_user[$uid] : null;
            $pl = isset($punches[$uid]) ? $punches[$uid] : array();

            $first_in = null;
            $last_out = null;
            foreach ($pl as $p) {
                if (strtoupper($p['punch_type']) === 'IN' && $first_in === null) $first_in = $p['punched_at'];
                if (strtoupper($p['punch_type']) === 'OUT') $last_out = $p['punched_at'];
            }

            $key = $d ? strtoupper($d['status']) : 'NOT_MARKED';
            if (!isset($summary[$key])) $summary[$key] = 0;
            $summary[$key]++;

            $people[] = array(
                'user_id'        => (int) $uid,
                'name'           => isset($names[$uid]) ? $names[$uid] : ('User ' . $uid),
                'status'         => $key,
                'status_label'   => $d ? $this->_att_status_label($d['status']) : 'Not marked',
                'marked_at'      => $d ? $d['marked_at'] : null,
                'remark'         => $d ? (string) $d['remark'] : '',
                'site_name'      => $d ? (string) $d['site_name'] : '',
                'distance_m'     => $d ? (int) $d['distance_m'] : 0,
                'in_premises'    => $d ? (int) $d['in_premises'] : 0,
                'is_mocked'      => $d ? (int) $d['is_mocked'] : 0,
                'first_in'       => $first_in,
                'last_out'       => $last_out,
                'worked_minutes' => $this->_att_worked($pl),
            ) + $this->_att_plant_fields($d);
        }

        /* Marked first, then the silent ones - the HOD is chasing the people
           at the bottom of this list. */
        usort($people, function ($a, $b) {
            if ($a['status'] === 'NOT_MARKED' && $b['status'] !== 'NOT_MARKED') return 1;
            if ($b['status'] === 'NOT_MARKED' && $a['status'] !== 'NOT_MARKED') return -1;
            return strcasecmp($a['name'], $b['name']);
        });

        echo json_encode(array(
            'status'  => true,
            'date'    => $date,
            'summary' => $summary,
            'total'   => count($people),
            'people'  => $people,
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}


/* ==================================================================
 * ATTENDANCE, AS A THING YOU LOOK AT
 *
 * The punch screen is gone: what people actually asked for is to *see*
 * attendance, and who they may see is decided here, not by the app.
 *
 *   normal user -> themselves
 *   HOD         -> their team (and themselves, so their own row is not
 *                  the one thing they cannot check)
 *   admin       -> everybody
 *
 * The app sends no scope and cannot ask for one. It is derived from the
 * caller's own id every time, so a rebuilt APK that lies about its role
 * still only gets its own row back.
 * ================================================================== */

/** The roster this user is allowed to see, plus the name for the scope. */
/**
 * Administrators have no attendance at all since 2026-09-17 - not asked, not
 * chased, no row ever written - so a sheet that listed them would show people
 * who are permanently "Not marked", which reads as absence rather than as
 * exemption. They come off every roster, including an admin's own view of
 * themselves. Admins still SEE the whole sheet; they are simply not on it.
 */
/**
 * Drop the people who never mark, so a sheet is not padded with rows that
 * can only ever read "Not marked".
 *
 * Keyed on the gate exemption, not on _att_is_admin: since 2026-09-22 an
 * administrator marks the day like everybody else, so their row belongs
 * on the sheet. Only the four exempt people come off it.
 */
private function _att_strip_admins($ids)
{
    if (empty($ids)) return $ids;

    $out = array();
    foreach ($ids as $id) {
        if ($this->_att_gate_exempt((int) $id)) continue;
        $out[] = (int) $id;
    }

    return $out;
}

private function _att_scope($user_id, $role_id)
{
    $user_id = (int) $user_id;

    if ($this->_att_is_admin($user_id, $role_id)) {
        $rows = $this->db->select('user_id')
                         ->from('system_users')
                         ->where('user_status', 1)
                         ->get()
                         ->result_array();

        $ids = array();
        foreach ($rows as $r) $ids[] = (int) $r['user_id'];

        return array('scope' => 'ALL', 'label' => 'Everyone',
                     'ids' => $this->_att_strip_admins($ids));
    }

    $team = $this->_att_team_member_ids($user_id);

    if (!empty($team)) {
        /* their own row belongs on their own sheet - unless they are an
           admin leading a team, in which case they have no row to show */
        $ids = $team;
        if (!in_array($user_id, $ids)) $ids[] = $user_id;

        return array('scope' => 'TEAM', 'label' => 'My team',
                     'ids' => $this->_att_strip_admins($ids));
    }

    return array('scope' => 'SELF', 'label' => 'My attendance', 'ids' => array($user_id));
}

/** user_id => display name, for a set of ids. */
private function _att_names($ids)
{
    if (empty($ids)) return array();

    $rows = $this->db->select("u.user_id, CONCAT(u.first_name,' ',COALESCE(u.last_name,'')) AS user_name, COALESCE(d.department,'') AS department", FALSE)
                     ->from('system_users u')
                     ->join('departments d', 'd.department_id = u.department_id', 'left')
                     ->where_in('u.user_id', $ids)
                     ->get()
                     ->result_array();

    $out = array();
    foreach ($rows as $r) {
        $n = trim($r['user_name']);
        $out[(int) $r['user_id']] = array(
            'name'       => $n !== '' ? ucwords(strtolower($n)) : ('User ' . $r['user_id']),
            'department' => ucwords(strtolower(trim($r['department']))),
        );
    }

    return $out;
}

/**
 * One day's attendance for everyone the caller may see.
 *
 * Everybody in scope appears, including the people who marked nothing:
 * "nobody has reported yet" and "four people did not turn up" have to look
 * different, and a list built only from the marks cannot tell them apart.
 */
public function attendance_sheet_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;
        $role_id = isset($input['role_id']) ? (int) $input['role_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        $this->_att_ensure();
        $this->_att_day_ensure();

        $date  = !empty($input['date']) ? date('Y-m-d', strtotime($input['date'])) : date('Y-m-d');
        $scope = $this->_att_scope($user_id, $role_id);
        $ids   = $scope['ids'];

        $names = $this->_att_names($ids);

        $days = $this->db->where_in('user_id', $ids)
                         ->where('work_date', $date)
                         ->get($this->_att_day_table())
                         ->result_array();

        $by_user = array();
        foreach ($days as $d) $by_user[(int) $d['user_id']] = $d;

        /* first in / last out still come from the punch log where one
           exists - PRESENT writes an IN punch, so this keeps showing the
           real arrival time rather than the moment the popup was answered */
        $punch_rows = $this->db->where_in('user_id', $ids)
                               ->where('punch_date', $date)
                               ->order_by('punched_at', 'ASC')
                               ->get($this->_att_log_table())
                               ->result_array();

        $punches = array();
        foreach ($punch_rows as $p) $punches[(int) $p['user_id']][] = $p;

        $people  = array();
        $summary = array('PRESENT' => 0, 'ABSENT' => 0, 'ON_DUTY' => 0, 'GATE_PASS' => 0, 'NOT_MARKED' => 0);

        foreach ($ids as $uid) {
            $uid = (int) $uid;
            $d   = isset($by_user[$uid]) ? $by_user[$uid] : null;
            $pl  = isset($punches[$uid]) ? $punches[$uid] : array();

            $first_in = null;
            $last_out = null;
            foreach ($pl as $p) {
                if (strtoupper($p['punch_type']) === 'IN' && $first_in === null) $first_in = $p['punched_at'];
                if (strtoupper($p['punch_type']) === 'OUT') $last_out = $p['punched_at'];
            }

            $key = $d ? strtoupper($d['status']) : 'NOT_MARKED';
            if (!isset($summary[$key])) $summary[$key] = 0;
            $summary[$key]++;

            $people[] = array(
                'user_id'        => $uid,
                'name'           => isset($names[$uid]) ? $names[$uid]['name'] : ('User ' . $uid),
                'department'     => isset($names[$uid]) ? $names[$uid]['department'] : '',
                'is_self'        => $uid === (int) $user_id ? 1 : 0,
                'status'         => $key,
                'status_label'   => $d ? $this->_att_status_label($d['status']) : 'Not marked',
                'marked_at'      => $d ? $d['marked_at'] : null,
                'remark'         => $d ? (string) $d['remark'] : '',
                'site_name'      => $d ? (string) $d['site_name'] : '',
                'distance_m'     => $d ? (int) $d['distance_m'] : 0,
                'in_premises'    => $d ? (int) $d['in_premises'] : 0,
                'is_mocked'      => $d ? (int) $d['is_mocked'] : 0,
                'first_in'       => $first_in,
                'last_out'       => $last_out,
                'worked_minutes' => $this->_att_worked($pl),
            ) + $this->_att_plant_fields($d);
        }

        /* marked first, then the silent ones - those are the rows worth a
           phone call, so they are the ones left at the bottom to chase */
        usort($people, function ($a, $b) {
            if ($a['status'] === 'NOT_MARKED' && $b['status'] !== 'NOT_MARKED') return 1;
            if ($b['status'] === 'NOT_MARKED' && $a['status'] !== 'NOT_MARKED') return -1;
            return strcasecmp($a['name'], $b['name']);
        });

        echo json_encode(array(
            'status'      => true,
            'date'        => $date,
            'scope'       => $scope['scope'],
            'scope_label' => $scope['label'],
            'summary'     => $summary,
            'total'       => count($people),
            'people'      => $people,
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/**
 * One person's month, day by day.
 *
 * Reached by tapping a row on the sheet, so it is gated the same way: you
 * may read your own always, your team's if you lead them, anyone's if you
 * are an admin. The check is on the *target*, not on what the app asked for.
 */
public function attendance_history_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;
        $role_id = isset($input['role_id']) ? (int) $input['role_id'] : 0;
        $target  = isset($input['target_id']) ? (int) $input['target_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        if (!$target) $target = $user_id;

        $scope = $this->_att_scope($user_id, $role_id);

        /* Your own month is always yours to open.
         *
         * Admins were taken off the scope list when they stopped having
         * attendance, and the list is also what this guard reads - so
         * without this line the stripping locked every admin out of their
         * own history, including the days they marked before the change.
         * Being exempt from marking is not the same as being denied the
         * record. */
        if ($target !== $user_id
            && !in_array($target, array_map('intval', $scope['ids']))) {
            echo json_encode(array('status' => false, 'message' => 'Not allowed'));
            return;
        }

        $this->_att_ensure();
        $this->_att_day_ensure();

        /* a month, defaulting to this one */
        $month = !empty($input['month']) ? (string) $input['month'] : date('Y-m');
        $first = date('Y-m-01', strtotime($month . '-01'));
        $last  = date('Y-m-t', strtotime($first));

        /* never list days that have not happened yet */
        $today = date('Y-m-d');
        $end   = $last > $today ? $today : $last;

        $rows = $this->db->where('user_id', $target)
                         ->where('work_date >=', $first)
                         ->where('work_date <=', $end)
                         ->get($this->_att_day_table())
                         ->result_array();

        $by_date = array();
        foreach ($rows as $r) $by_date[$r['work_date']] = $r;

        $punch_rows = $this->db->where('user_id', $target)
                               ->where('punch_date >=', $first)
                               ->where('punch_date <=', $end)
                               ->order_by('punched_at', 'ASC')
                               ->get($this->_att_log_table())
                               ->result_array();

        $punches = array();
        foreach ($punch_rows as $p) $punches[$p['punch_date']][] = $p;

        $names = $this->_att_names(array($target));

        $days    = array();
        $summary = array('PRESENT' => 0, 'ABSENT' => 0, 'ON_DUTY' => 0, 'GATE_PASS' => 0, 'NOT_MARKED' => 0);

        /* newest first - the day you are asking about is nearly always a
           recent one, so it should not be at the bottom of a scroll */
        for ($d = strtotime($end); $d >= strtotime($first); $d -= 86400) {
            $date = date('Y-m-d', $d);
            $row  = isset($by_date[$date]) ? $by_date[$date] : null;
            $pl   = isset($punches[$date]) ? $punches[$date] : array();

            $first_in = null;
            $last_out = null;
            foreach ($pl as $p) {
                if (strtoupper($p['punch_type']) === 'IN' && $first_in === null) $first_in = $p['punched_at'];
                if (strtoupper($p['punch_type']) === 'OUT') $last_out = $p['punched_at'];
            }

            $key = $row ? strtoupper($row['status']) : 'NOT_MARKED';
            if (!isset($summary[$key])) $summary[$key] = 0;
            $summary[$key]++;

            $days[] = array(
                'date'           => $date,
                'weekday'        => date('D', $d),
                'status'         => $key,
                'status_label'   => $row ? $this->_att_status_label($row['status']) : 'Not marked',
                'marked_at'      => $row ? $row['marked_at'] : null,
                'remark'         => $row ? (string) $row['remark'] : '',
                'site_name'      => $row ? (string) $row['site_name'] : '',
                'distance_m'     => $row ? (int) $row['distance_m'] : 0,
                'in_premises'    => $row ? (int) $row['in_premises'] : 0,
                'is_mocked'      => $row ? (int) $row['is_mocked'] : 0,
                'first_in'       => $first_in,
                'last_out'       => $last_out,
                'worked_minutes' => $this->_att_worked($pl),
            ) + $this->_att_plant_fields($row);
        }

        echo json_encode(array(
            'status'  => true,
            'target'  => $target,
            'name'    => isset($names[$target]) ? $names[$target]['name'] : ('User ' . $target),
            'is_self' => $target === $user_id ? 1 : 0,
            'month'   => date('Y-m', strtotime($first)),
            'label'   => date('F Y', strtotime($first)),
            'from'    => $first,
            'to'      => $end,
            'summary' => $summary,
            'days'    => $days,
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}


/* ==================================================================
 * THE BOSS DASHBOARD
 *
 * One call behind the extra sections on the app's admin dashboard:
 * today's task progress, the DF portfolio, the next fortnight's
 * commitments, and where the delay actually sits by department.
 *
 * Every figure here is deliberately the SAME query the PMS web dashboard
 * runs (newdesigndashboard.php and Dashboard::index), company-wide with no
 * per-user scoping - two screens quoting the same label at different
 * numbers is worse than one screen not existing. Nothing on the web side is
 * touched; the SQL is repeated here rather than shared, because this
 * controller is the only thing the phone is allowed to reach.
 * ================================================================== */

/**
 * num_rows() on a query that failed.
 *
 * With db_debug off a broken query returns FALSE rather than aborting the
 * request, so this turns that into a 0. One missing table then costs one
 * zeroed box instead of the whole dashboard - which is exactly what
 * happened on the first deploy, when a dead helptickets() query against a
 * table that does not exist took the entire response down with it.
 */
private function _boss_rows($q)
{
    return $q ? (int) $q->num_rows() : 0;
}

/** Who gets the extra sections. Shubham Sir only, as asked. */
private function _boss_ids()
{
    return array(139);
}

public function boss_dashboard_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        /* Not an error, and not an empty payload either: the app asks on
           every admin dashboard load and simply draws nothing for anyone
           who is not on the list. */
        if (!in_array($user_id, $this->_boss_ids(), true)) {
            echo json_encode(array('status' => true, 'visible' => false));
            return;
        }

        $today  = date('Y-m-d');
        $next15 = date('Y-m-d', strtotime('+15 days'));

        /* a failed query must return FALSE, not kill the response */
        $boss_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        /* ---------- TODAY'S TASK PROGRESS ---------- */

        /* due today and still open - the web dashboard's "Due Today" */
        $this->db->select('t.id')
            ->from('task_department_wise_scheduling t')
            ->join('df_release df', 't.df_id = df.id', 'left')
            ->where('df.df_status', 0)
            ->where('t.on_hold', 0)
            ->where('t.task_status', 0)
            ->where('t.end_date', $today);
        $due_today = $this->_boss_rows($this->db->get());

        /* overdue in total, not just today - department 22 is excluded the
           same way the web dashboard excludes it */
        $this->db->select('t.id')
            ->from('task_department_wise_scheduling t')
            ->join('df_release df', 't.df_id = df.id', 'left')
            ->where('t.on_hold', 0)
            ->where('t.task_status', 0)
            ->where('t.end_date <', $today)
            ->where('t.department_id !=', 22)
            ->group_start()
                ->where('t.df_id', 0)
                ->or_where('df.df_status', 0)
            ->group_end();
        $overdue = $this->_boss_rows($this->db->get());

        /* finished today: status 1 is approved, status 2 is done and waiting
           on an approval. Both are work that got finished today - leaving 2
           out would under-report the day every time an approver is slow. */
        $this->db->select('t.id')
            ->from('task_department_wise_scheduling t')
            ->where('t.task_completed_on >=', $today . ' 00:00:00')
            ->where('t.task_completed_on <=', $today . ' 23:59:59')
            ->where_in('t.task_status', array(1, 2));
        $completed_today = $this->_boss_rows($this->db->get());

        /* ---------- THE DF PORTFOLIO ---------- */

        $this->db->select('id')
            ->from('df_release')
            ->where('on_hold', 0)
            ->where('df_status', 0);
        $running_df = $this->_boss_rows($this->db->get());

        /* the same helper the web dashboard uses, so "delayed" means one
           thing in this company and not two */
        $this->load->helper('df_delay');
        $this->db->select('DISTINCT(df.id) AS df_id', false)
            ->from('df_release df')
            ->join('task_department_wise_scheduling t', 't.df_id = df.id', 'inner')
            ->where('df.on_hold', 0)
            ->where('df.df_status', 0)
            ->where(df_open_overdue_sql($this->db, 't', $today), null, false);
        $delayed_df = $this->_boss_rows($this->db->get());

        $this->db->select('id')->from('df_release')->where('df_status', 1);
        $closed_df = $this->_boss_rows($this->db->get());

        $this->db->select('id')->from('poreceived')->where('penalityamount !=', 0);
        $penalty_df = $this->_boss_rows($this->db->get());

        /* ---------- THE NEXT FORTNIGHT ---------- */

        /* task 103 is dispatch and 93 is FAT, as the web dashboard has it */
        $dispatch_15 = $this->_boss_task_window(103, $today, $next15);
        $fat_15      = $this->_boss_task_window(93, $today, $next15);

        /* Open tickets on a running DF, which is what the web dashboard's
           "Open Help Tickets" box counts - an admin sees all of them. The
           table is communication_ticket_system; Dashboard_model still has a
           helptickets() reading dynamic_form_data, but that table does not
           exist on this database and the function is dead code. */
        $this->db->select('a.id')
            ->from('communication_ticket_system a')
            ->join('df_release f', 'a.df_id = f.id', 'left')
            ->where('a.ticket_status', 0)
            ->where('a.df_id >', 0)
            ->where('f.df_status', 0)
            ->where('(f.on_hold = 0 OR f.on_hold IS NULL)', null, false);
        $help_tickets = $this->_boss_rows($this->db->get());

        /* ---------- WHERE THE DELAY SITS ---------- */

        $department_delay_sql = df_department_overdue_sql($this->db, 't', $today);
        $delay_rows = $this->db->query("
            SELECT
                d.department, df.df_no, t.department_id,
                COUNT(t.id) AS total_tasks,
                MAX(
                    CASE
                        WHEN " . $department_delay_sql . " THEN DATEDIFF(CURDATE(), t.end_date)
                        ELSE 0
                    END
                ) AS max_delay_days
            FROM task_department_wise_scheduling t
            JOIN df_release df ON t.df_id = df.id
            JOIN departments d ON t.department_id = d.department_id
            WHERE df.df_status = 0
              AND IFNULL(df.on_hold, 0) = 0
              AND " . $department_delay_sql . "
            GROUP BY t.department_id
            ORDER BY max_delay_days DESC
        ");
        $delay_rows = $delay_rows ? $delay_rows->result_array() : array();

        $this->db->db_debug = $boss_debug;

        $departments = array();
        foreach (($delay_rows ? $delay_rows : array()) as $r) {
            $departments[] = array(
                'department'     => ucwords(strtolower(trim((string) $r['department']))),
                'department_id'  => (int) $r['department_id'],
                'total_tasks'    => (int) $r['total_tasks'],
                'max_delay_days' => (int) $r['max_delay_days'],
            );
        }

        echo json_encode(array(
            'status'  => true,
            'visible' => true,
            'date'    => $today,

            'today' => array(
                'pending'   => (int) $due_today,
                'overdue'   => (int) $overdue,
                'completed' => (int) $completed_today,
            ),

            'df' => array(
                'running' => (int) $running_df,
                'delayed' => (int) $delayed_df,
                'closed'  => (int) $closed_df,
                'penalty' => (int) $penalty_df,
            ),

            'ahead' => array(
                'dispatch_15'  => (int) $dispatch_15,
                'fat_15'       => (int) $fat_15,
                'help_tickets' => (int) $help_tickets,
            ),

            'departments' => $departments,
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/** Open tasks of one type falling inside a date window. */
private function _boss_task_window($taskid, $from, $to)
{
    $this->db->select('t.id')
        ->from('task_department_wise_scheduling t')
        ->join('df_release df', 't.df_id = df.id', 'left')
        ->where('df.df_status', 0)
        ->where('t.on_hold', 0)
        ->where('t.task_status', 0)
        ->where('t.taskid', (int) $taskid)
        ->where('t.end_date >=', $from)
        ->where('t.end_date <=', $to);
    return $this->_boss_rows($this->db->get());
}


/**
 * ONE-OFF: tell every iPhone that a new TestFlight build is waiting.
 *
 * Who is on iOS cannot come from user_devices: saveFcmToken writes
 * device_type = 'android' for every device regardless of platform, so that
 * column is meaningless. The only place a handset ever states its platform
 * is the App Log ping, which lands in app_device_log.
 *
 * user_devices also keeps exactly one token per user - a new token deletes
 * the old rows for that user - so the token on file is whichever device that
 * person opened most recently. This therefore targets users whose *latest*
 * app_device_log row is iOS, not merely anyone who has ever opened it on an
 * iPhone: on a one-token-per-user table those are the only people whose
 * stored token is actually an iPhone's.
 *
 * Call with dry=1 first. It returns the exact audience and sends nothing,
 * which is the only chance to check a blast that cannot be recalled.
 *
 *   .../mobile/Api/ios_update_push_api?key=ios-update-2026-09-16&dry=1
 *   .../mobile/Api/ios_update_push_api?key=ios-update-2026-09-16
 */
public function ios_update_push_api()
{
    header('Content-Type: application/json');

    /// No session reaches this, so it carries its own key.
    if ($this->input->get_post('key') !== 'ios-update-2026-09-16') {
        echo json_encode(array("status" => false, "message" => "forbidden"));
        return;
    }

    $dry = (int) $this->input->get_post('dry') === 1;

    $title = trim((string) $this->input->get_post('title'));
    $body  = trim((string) $this->input->get_post('body'));

    if ($title === '') $title = 'Update available';
    if ($body === '')  $body  = 'A new build of the Shubham PMS app is ready. Open TestFlight to install it.';

    $sql = "SELECT l.user_id, l.app_version, l.build_number, l.last_seen
              FROM app_device_log l
              JOIN (SELECT user_id, MAX(last_seen) AS ms
                      FROM app_device_log GROUP BY user_id) m
                ON m.user_id = l.user_id AND m.ms = l.last_seen
             WHERE l.platform = 'ios'
             GROUP BY l.user_id";

    $ios = $this->db->query($sql)->result_array();

    if (empty($ios)) {
        echo json_encode(array(
            "status"  => true,
            "message" => "no handset has ever reported itself as iOS",
            "sent"    => 0,
        ));
        return;
    }

    $ids = array_map('intval', array_column($ios, 'user_id'));

    $devices = $this->db->select("d.user_id, d.fcm_token, TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS user_name", false)
        ->from('user_devices d')
        ->join('system_users u', 'u.user_id = d.user_id', 'left')
        ->where_in('d.user_id', $ids)
        ->where('d.fcm_token IS NOT NULL')
        ->where('d.fcm_token !=', '')
        ->get()->result_array();

    $payload = array(
        "type"   => "app_update",
        "screen" => "update",
    );

    $recipients = array();
    $sent = 0;

    foreach ($devices as $d) {
        $row = array(
            "user_id"   => (int) $d['user_id'],
            "user_name" => isset($d['user_name']) ? $d['user_name'] : '',
            "token"     => substr($d['fcm_token'], 0, 14) . '...',
        );

        if (!$dry) {
            /// The helper's 30s ttl sits inside the android block only, so it
            /// does not shorten APNs delivery - an iPhone that is asleep still
            /// gets this when it wakes.
            $res = sendFCMData($d['fcm_token'], $title, $body, $payload);
            $row['result'] = json_decode($res, true);
            $sent++;
        }

        $recipients[] = $row;
    }

    $with_token = array_map('intval', array_column($devices, 'user_id'));

    echo json_encode(array(
        "status"       => true,
        "dry_run"      => $dry,
        "ios_users"    => count($ids),
        "with_token"   => count($devices),
        "no_token"     => array_values(array_diff($ids, $with_token)),
        "sent"         => $sent,
        "title"        => $title,
        "body"         => $body,
        "ios_devices"  => $ios,
        "recipients"   => $recipients,
    ));
}

/**
 * Clear one person's day mark, so the gate popup and the 9:30 reminder both
 * treat them as unmarked again. For testing the attendance flow end to end.
 *
 * attendance_mark_api can only update a status - no path in the app removes
 * the row, and removing it is the only thing that makes the gate reappear.
 *
 * Refuses to run without user_id or name: a reset with no target would clear
 * the whole company's attendance for the day, which cannot be undone from
 * here. Also refuses when a name matches more than one person rather than
 * guessing which was meant. `was` echoes the row it removed, so a mistaken
 * reset can be typed back in by hand.
 *
 *   .../mobile/Api/attendance_day_reset_api?key=spm-attendance-reminder-2026&name=Manglesh
 */
public function attendance_day_reset_api()
{
    header('Content-Type: application/json');

    if ($this->input->get_post('key') !== $this->_attrem_key()) {
        echo json_encode(array("status" => false, "message" => "forbidden"));
        return;
    }

    date_default_timezone_set('Asia/Kolkata');

    $uid  = (int) $this->input->get_post('user_id');
    $name = trim((string) $this->input->get_post('name'));
    $date = trim((string) $this->input->get_post('date'));

    if ($date === '') $date = date('Y-m-d');

    if ($uid <= 0 && $name === '') {
        echo json_encode(array(
            "status"  => false,
            "message" => "need user_id or name - refusing to clear everybody's attendance",
        ));
        return;
    }

    if ($uid <= 0) {
        $like = $this->db->escape_like_str($name);
        $hits = $this->db->select('user_id, first_name, last_name')
            ->from('system_users')
            ->where("(first_name LIKE '%" . $like . "%' ESCAPE '!'"
                  . " OR last_name LIKE '%" . $like . "%' ESCAPE '!'"
                  . " OR CONCAT(first_name,' ',last_name) LIKE '%" . $like . "%' ESCAPE '!')", null, false)
            ->where('user_status', 1)
            ->get()->result_array();

        if (count($hits) === 0) {
            echo json_encode(array("status" => false, "message" => "no active user matches '" . $name . "'"));
            return;
        }

        if (count($hits) > 1) {
            echo json_encode(array(
                "status"  => false,
                "message" => "'" . $name . "' matches " . count($hits) . " people - pass user_id",
                "matches" => $hits,
            ));
            return;
        }

        $uid = (int) $hits[0]['user_id'];
    }

    $this->_att_day_ensure();
    $tbl = $this->_att_day_table();

    $who = $this->db->select('user_id, first_name, last_name')
        ->from('system_users')->where('user_id', $uid)->get()->row_array();

    $before = $this->db->from($tbl)
        ->where('user_id', $uid)->where('work_date', $date)
        ->get()->result_array();

    $this->db->where('user_id', $uid)->where('work_date', $date)->delete($tbl);
    $removed = (int) $this->db->affected_rows();

    echo json_encode(array(
        "status"  => true,
        "user_id" => $uid,
        "name"    => $who ? trim($who['first_name'] . ' ' . $who['last_name']) : '',
        "date"    => $date,
        "removed" => $removed,
        "was"     => $before,
        "message" => $removed > 0
            ? "day mark cleared - the gate will ask again and the 9:30 reminder now counts them unmarked"
            : "nothing to clear; they had not marked " . $date,
    ));
}

/**
 * READ-ONLY: why the people the attendance reminder cannot reach have no
 * token, when they plainly have the app.
 *
 * "unreachable" in attendance_reminder_push_api means only "no user_devices
 * row with a non-empty fcm_token". That count on its own cannot say whether
 * the person never installed the app, installed it and never opened a build
 * that registers, or opened it this morning and still has no row - and those
 * three want completely different fixes. app_device_log is the independent
 * witness: a row there is the handset itself saying "I opened, on this build,
 * on this platform", so crossing the two separates the cases.
 *
 * Touches nothing. Deliberately separate from the cron endpoint rather than
 * bolted onto it - that one runs unattended every morning and is not worth
 * risking for a diagnostic.
 *
 *   .../mobile/Api/attendance_reach_diag_api?key=spm-attendance-reminder-2026
 */
public function attendance_reach_diag_api()
{
    header('Content-Type: application/json');

    if ($this->input->get_post('key') !== $this->_attrem_key()) {
        echo json_encode(array("status" => false, "message" => "forbidden"));
        return;
    }

    /* Same IST day the cron asks about - see the note there. */
    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');

    $this->_att_day_ensure();
    $att = $this->_att_day_table();

    $one = function ($sql) {
        $r = $this->db->query($sql)->row_array();
        return (int) (isset($r['c']) ? $r['c'] : 0);
    };

    $staff        = $one("SELECT COUNT(*) c FROM system_users WHERE user_status=1 AND hide_profile=0");
    $dev_rows     = $one("SELECT COUNT(*) c FROM user_devices");
    $dev_users    = $one("SELECT COUNT(DISTINCT user_id) c FROM user_devices");
    $dev_tokened  = $one("SELECT COUNT(DISTINCT user_id) c FROM user_devices WHERE fcm_token IS NOT NULL AND fcm_token != ''");
    $dev_empty    = $one("SELECT COUNT(*) c FROM user_devices WHERE fcm_token IS NULL OR fcm_token = ''");
    $log_users    = $one("SELECT COUNT(DISTINCT user_id) c FROM app_device_log");

    /* Staff who have opened the app at least once (app_device_log) but have
       no usable token. This is the group the question is really about. */
    $opened_no_token = $one(
        "SELECT COUNT(DISTINCT a.user_id) c FROM app_device_log a
          JOIN system_users u ON u.user_id = a.user_id
         WHERE u.user_status=1 AND u.hide_profile=0
           AND a.user_id NOT IN (SELECT user_id FROM user_devices WHERE fcm_token IS NOT NULL AND fcm_token != '')");

    $sql = "SELECT u.user_id, u.first_name, u.last_name,
                   (SELECT COUNT(*) FROM user_devices dv WHERE dv.user_id = u.user_id) AS device_rows,
                   l.platform, l.app_version, l.build_number, l.last_seen
              FROM system_users u
              LEFT JOIN (
                   SELECT a.user_id, a.platform, a.app_version, a.build_number, a.last_seen
                     FROM app_device_log a
                     JOIN (SELECT user_id, MAX(last_seen) ms FROM app_device_log GROUP BY user_id) m
                       ON m.user_id = a.user_id AND m.ms = a.last_seen
              ) l ON l.user_id = u.user_id
             WHERE u.user_status=1 AND u.hide_profile=0
               AND u.user_id NOT IN (SELECT user_id FROM user_devices WHERE fcm_token IS NOT NULL AND fcm_token != '')
               AND NOT EXISTS (SELECT 1 FROM {$att} d WHERE d.user_id = u.user_id AND d.work_date = " . $this->db->escape($today) . ")
             ORDER BY (l.last_seen IS NULL), l.last_seen DESC";

    $rows = $this->db->query($sql)->result_array();

    $has_app = array();
    $never   = array();

    foreach ($rows as $r) {
        $p = array(
            "user_id"     => (int) $r['user_id'],
            "name"        => trim($r['first_name'] . ' ' . $r['last_name']),
            "device_rows" => (int) $r['device_rows'],
        );

        if (!empty($r['last_seen'])) {
            $p['platform']  = $r['platform'];
            $p['version']   = $r['app_version'] . '+' . $r['build_number'];
            $p['last_seen'] = $r['last_seen'];
            $has_app[] = $p;
        } else {
            $never[] = $p;
        }
    }

    echo json_encode(array(
        "status" => true,
        "date"   => $today,
        "totals" => array(
            "active_staff"            => $staff,
            "user_devices_rows"       => $dev_rows,
            "users_with_device_row"   => $dev_users,
            "users_with_usable_token" => $dev_tokened,
            "rows_with_empty_token"   => $dev_empty,
            "users_seen_in_app_log"   => $log_users,
            "opened_app_but_no_token" => $opened_no_token,
        ),
        "unreachable_and_unmarked" => count($rows),
        "has_opened_the_app"       => count($has_app),
        "never_seen_in_app_log"    => count($never),
        "detail_has_app"           => $has_app,
        "detail_never_seen"        => $never,
    ));
}

/**
 * READ-ONLY: why one lead does or does not appear in a follow-up list.
 *
 * The three things that decide it live in three different places - the durable
 * `leads.closed` flag, the stage on the *latest* `progress_remarks` row, and
 * that row's `next_follow_date` - so answering "why is this one in Missed?"
 * otherwise means three lookups and a guess. This reports all three plus the
 * verdict the list query itself would reach.
 *
 *   .../mobile/Api/lead_debug_api?key=spm-attendance-reminder-2026&opp=SPM/EXP/L/1143/26-27
 */
public function lead_debug_api()
{
    header('Content-Type: application/json');

    if ($this->input->get_post('key') !== $this->_attrem_key()) {
        echo json_encode(array("status" => false, "message" => "forbidden"));
        return;
    }

    $opp = trim((string) $this->input->get_post('opp'));
    $lid = (int) $this->input->get_post('lead_id');

    if ($opp === '' && $lid <= 0) {
        echo json_encode(array("status" => false, "message" => "pass opp or lead_id"));
        return;
    }

    $this->db->select('id, unique_id, closed, added_by, create_date');
    $this->db->from('leads');
    if ($lid > 0) $this->db->where('id', $lid);
    else          $this->db->where('unique_id', $opp);
    $lead = $this->db->get()->row_array();

    if (!$lead) {
        echo json_encode(array("status" => false, "message" => "no lead matches"));
        return;
    }

    /* The list keys off the newest remark only, which is the whole subtlety -
       an older remark carrying the conversion stage counts for nothing. */
    $last = $this->db->select('id, lead_status, next_follow_date, added_on, remarks')
        ->from('progress_remarks')
        ->where('lead_id', (int) $lead['id'])
        ->order_by('id', 'desc')->limit(1)
        ->get()->row_array();

    $stage = null;
    if ($last) {
        $stage = $this->db->select('lead_id, lead_name, conversion_step, dead_end')
            ->from('lead_stage')->where('lead_id', $last['lead_status'])
            ->get()->row_array();
    }

    $conv = $this->dashboardmodel->getConversionLeadStage();
    $dead = $this->salescrm->getDeadEndLeadStage();
    array_push($dead, $conv);

    $today  = date('Y-m-d');
    $date   = $last ? $last['next_follow_date'] : null;

    $bucket = 'none';
    if ($date && $date != '0000-00-00' && $date != '1970-01-01') {
        if ($date == $today)     $bucket = 'today';
        elseif ($date < $today)  $bucket = 'missed';
        else                     $bucket = 'upcoming';
    }

    $blocked = array();
    if ((int) $lead['closed'] === 1)                          $blocked[] = 'leads.closed = 1';
    if ($last && in_array($last['lead_status'], $dead, false)) $blocked[] = 'latest stage is dead-end/conversion';
    if ($bucket === 'none')                                    $blocked[] = 'no usable next_follow_date';

    /* The web page at Leads/missed_followup_list runs a completely different
       query: no dead-end/conversion filter, no leads.closed, and - because it
       groups by lead_id over rows already narrowed to a past date rather than
       pinning a.id to MAX(id) - it can answer with an OLD remark while the
       newest one says Order Won. Reproduced here for this one lead so the two
       surfaces can be compared instead of argued about. */
    $web = $this->db->select('a.id, a.lead_id, a.no_followup, a.next_follow_date, b.lead_quality')
        ->from('progress_remarks a')
        ->join('leads b', 'a.lead_id=b.id', 'left')
        ->where('b.lead_quality !=', 5)
        ->where('a.no_followup', 0)
        ->where('a.next_follow_date <', $today)
        ->where('a.lead_id', (int) $lead['id'])
        ->get()->result_array();

    echo json_encode(array(
        "status"           => true,
        "web_missed_rows"  => $web,
        "web_would_show"   => count($web) > 0 ? 1 : 0,
        "lead"             => $lead,
        "latest_remark"    => $last,
        "latest_stage"     => $stage,
        "conversion_stage" => $conv,
        "dead_end_stages"  => $dead,
        "today"            => $today,
        "would_land_in"    => $bucket,
        "excluded_by"      => $blocked,
        "shows_in_list"    => empty($blocked) ? 1 : 0,
    ));
}

/**
 * READ-ONLY: what build and platform one person is actually running.
 *
 * "It does not work on his phone" is usually a build question, and the answer
 * is split across two tables: app_device_log knows the platform and version
 * each handset reported at launch, user_devices knows whether a push can
 * reach it. Neither is visible from the app.
 *
 *   .../mobile/Api/device_debug_api?key=spm-attendance-reminder-2026&name=Shubham
 */
public function device_debug_api()
{
    header('Content-Type: application/json');

    if ($this->input->get_post('key') !== $this->_attrem_key()) {
        echo json_encode(array("status" => false, "message" => "forbidden"));
        return;
    }

    date_default_timezone_set('Asia/Kolkata');

    $uid  = (int) $this->input->get_post('user_id');
    $name = trim((string) $this->input->get_post('name'));

    if ($uid <= 0 && $name === '') {
        echo json_encode(array("status" => false, "message" => "pass user_id or name"));
        return;
    }

    $this->db->select('user_id, first_name, last_name, user_status');
    $this->db->from('system_users');
    if ($uid > 0) {
        $this->db->where('user_id', $uid);
    } else {
        $like = $this->db->escape_like_str($name);
        $this->db->where("(first_name LIKE '%" . $like . "%' ESCAPE '!'"
                       . " OR last_name LIKE '%" . $like . "%' ESCAPE '!'"
                       . " OR CONCAT(first_name,' ',last_name) LIKE '%" . $like . "%' ESCAPE '!')",
                       null, false);
    }
    $users = $this->db->get()->result_array();

    if (empty($users)) {
        echo json_encode(array("status" => false, "message" => "no user matches"));
        return;
    }

    $out = array();

    foreach ($users as $u) {
        $id = (int) $u['user_id'];

        $devices = $this->db->select('device_id, platform, app_version, build_number, os_version, last_seen, open_count')
            ->from('app_device_log')->where('user_id', $id)
            ->order_by('last_seen', 'desc')->get()->result_array();

        $tokens = $this->db->select('device_type, updated_at, created_at')
            ->from('user_devices')->where('user_id', $id)->get()->result_array();

        $day = $this->db->from($this->_att_day_table())
            ->where('user_id', $id)->where('work_date', date('Y-m-d'))
            ->get()->row_array();

        $out[] = array(
            "user_id"       => $id,
            "name"          => trim($u['first_name'] . ' ' . $u['last_name']),
            "active"        => (int) $u['user_status'],
            "devices"       => $devices,
            "push_tokens"   => count($tokens),
            "token_rows"    => $tokens,
            "marked_today"  => $day ? $day['status'] : null,
            "marked_at"     => $day ? $day['marked_at'] : null,
        );
    }

    echo json_encode(array("status" => true, "today" => date("Y-m-d"), "admin_roles" => $this->Dashboard_model->getsuperadminuserole(), "users" => $out));
}

/* ======================================================================
 * ACCOUNT SWITCHING  -  added 2026-09-17
 *
 * Lets a named few open the app as somebody else, to see exactly what that
 * person sees. Deliberately a fixed list rather than a role test: this is
 * impersonation, every permission in the app follows from it, and "whoever
 * is an admin today" is not a list anyone is watching.
 *
 * Every switch is written to app_user_switch_log before the payload is
 * returned, so anything done while switched can be traced back to the person
 * who really did it. Without that, a switched session is indistinguishable
 * from the real user working normally - which is the whole risk.
 * ====================================================================== */

/** The only accounts that may switch. Manglesh 161, Shubham Sharma 139, Deepesh Yadav 82. */
private function _switch_allowed()
{
    return array(161, 139, 82);
}

/** Created lazily, like the attendance tables - no migration step to forget. */
private function _switch_log_ensure()
{
    $this->db->query("
        CREATE TABLE IF NOT EXISTS app_user_switch_log (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            actor_id     INT NOT NULL,
            actor_name   VARCHAR(120) DEFAULT '',
            target_id    INT NOT NULL,
            target_name  VARCHAR(120) DEFAULT '',
            switched_at  DATETIME NOT NULL,
            ip_address   VARCHAR(45) DEFAULT '',
            INDEX (actor_id),
            INDEX (target_id),
            INDEX (switched_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

private function _switch_user_row($user_id)
{
    return $this->db
        ->select('user_id, first_name, last_name, email, user_role_id, department_id, user_status, contact_number, admin_dashboard')
        ->from('system_users')
        ->where('user_id', (int) $user_id)
        ->get()->row_array();
}

/**
 * Who this caller may become.
 *
 * Returns everybody active except themselves. Sorted by name because the
 * picker is a long list and user_id order means nothing to a human.
 */
public function switch_user_list_api()
{
    header('Content-Type: application/json');

    $in  = json_decode(file_get_contents("php://input"), true);
    $me  = (int) ($in['user_id'] ?? 0);

    if (!in_array($me, $this->_switch_allowed(), true)) {
        echo json_encode(array("status" => false, "message" => "not_allowed"));
        return;
    }

    $rows = $this->db
        ->select("user_id, TRIM(CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,''))) AS name, user_role_id, department_id", false)
        ->from('system_users')
        ->where('user_status', 1)
        ->where('hide_profile', 0)
        ->where('user_id !=', $me)
        ->order_by('first_name', 'ASC')
        ->get()->result_array();

    echo json_encode(array(
        "status" => true,
        "count"  => count($rows),
        "users"  => $rows,
    ));
}

/**
 * Become another user.
 *
 * Answers with exactly the shape auth_login answers with, so the app can hand
 * it straight to SessionManager.saveSession() and everything downstream -
 * permissions, dashboards, scoping - behaves as if that person had signed in.
 * Any divergence here would show up as a subtly wrong session rather than an
 * error, so the payload is built from the same columns auth_login reads.
 */
public function switch_user_api()
{
    header('Content-Type: application/json');

    $in     = json_decode(file_get_contents("php://input"), true);
    $me     = (int) ($in['user_id'] ?? 0);
    $target = (int) ($in['target_id'] ?? 0);

    if (!in_array($me, $this->_switch_allowed(), true)) {
        echo json_encode(array("status" => false, "message" => "You are not allowed to switch accounts"));
        return;
    }

    if ($target <= 0 || $target === $me) {
        echo json_encode(array("status" => false, "message" => "Pick someone other than yourself"));
        return;
    }

    $user = $this->_switch_user_row($target);

    if (!$user) {
        echo json_encode(array("status" => false, "message" => "That user no longer exists"));
        return;
    }

    if ((int) $user['user_status'] !== 1) {
        echo json_encode(array("status" => false, "message" => "That account is inactive"));
        return;
    }

    $actor = $this->_switch_user_row($me);

    /* Logged BEFORE the payload goes out: if the write fails we would rather
       know than hand over an untraceable session. */
    $this->_switch_log_ensure();
    $this->db->insert('app_user_switch_log', array(
        'actor_id'    => $me,
        'actor_name'  => $actor ? trim($actor['first_name'] . ' ' . $actor['last_name']) : '',
        'target_id'   => $target,
        'target_name' => trim($user['first_name'] . ' ' . $user['last_name']),
        'switched_at' => date('Y-m-d H:i:s'),
        'ip_address'  => substr((string) $this->input->ip_address(), 0, 45),
    ));

    $token = base64_encode(json_encode(array(
        'uid'  => (int) $user['user_id'],
        'time' => time(),
        'rand' => random_int(1000, 9999),
    )));

    echo json_encode(array(
        'status'  => true,
        'message' => 'Switched',
        'token'   => $token,
        'user'    => array(
            'user_id'         => (int) $user['user_id'],
            'name'            => $user['first_name'] . ' ' . $user['last_name'],
            'email'           => $user['email'],
            'role_id'         => (int) $user['user_role_id'],
            'department_id'   => (int) $user['department_id'],
            'contact_number'  => $user['contact_number'],
            'admin_dashboard' => (int) $user['admin_dashboard'],
        ),
    ));
}

/** The switch log, newest first, for whoever needs to audit it. */
public function switch_user_log_api()
{
    header('Content-Type: application/json');

    $in = json_decode(file_get_contents("php://input"), true);
    $me = (int) ($in['user_id'] ?? 0);

    if (!in_array($me, $this->_switch_allowed(), true)) {
        echo json_encode(array("status" => false, "message" => "not_allowed"));
        return;
    }

    $this->_switch_log_ensure();

    $rows = $this->db->from('app_user_switch_log')
        ->order_by('id', 'desc')->limit(200)->get()->result_array();

    echo json_encode(array("status" => true, "rows" => $rows));
}

/**
 * Whether this person sees the DF family - DF Task, DF MOM and DF Change
 * Control. Hidden from HR and from engineers since 2026-09-17.
 *
 * One helper rather than the test written out three times: the three
 * modules are one family and must appear and disappear together, and three
 * copies of a condition is how one of them ends up disagreeing.
 */
private function _df_visible($user_id, $role_id, $department_id)
{
    if ($this->_att_is_admin($user_id, $role_id)) return true;

    $dept = (int) $department_id;

    if ($dept === 26) return false;                              // HR
    if ($dept === 22 && (int) $user_id !== 111) return false;    // engineers

    return true;
}


/* ======================================================================
 * CURRENCY EXCHANGE RATE  -  added 2026-09-17
 *
 * Accounts enter the day's USD / EUR / AED rate; everyone else reads it.
 * One row per calendar day, re-savable all day - a rate corrected at four
 * o'clock replaces the nine o'clock one rather than adding a second row -
 * and every correction is counted, so a figure that moved three times is
 * visible rather than silent. Same shape as the attendance day mark.
 * ====================================================================== */

private function _fx_table() { return 'app_exchange_rate'; }

/** Created on first use, like the attendance day table - no migration step. */
private function _fx_ensure()
{
    $this->db->query(
        'CREATE TABLE IF NOT EXISTS ' . $this->_fx_table() . ' (
            id            INT(11) NOT NULL AUTO_INCREMENT,
            rate_date     DATE NOT NULL,
            usd           DECIMAL(12,4) NOT NULL DEFAULT 0,
            eur           DECIMAL(12,4) NOT NULL DEFAULT 0,
            aed           DECIMAL(12,4) NOT NULL DEFAULT 0,
            remark        VARCHAR(190) NOT NULL DEFAULT "",
            updated_by    INT(11) NOT NULL DEFAULT 0,
            updated_at    DATETIME NULL,
            changed_count INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_rate_date (rate_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
}

/**
 * Who may type a rate in.
 *
 * Two people by name - Hukam Sorout (63) and Vijay Singh Bisht (92) - and
 * admins. Nobody else, including the rest of Accounts.
 *
 * It was the whole Accounts department (20) until 2026-09-18, on the
 * reasoning that naming people means a code change when somebody joins.
 * That was overruled: the rate is one figure the whole company then quotes
 * against, and the department is a dozen people who have no business
 * setting it. The names are the point, not an approximation of them.
 *
 * $department_id is still taken and still ignored, because the caller has
 * it and the day this becomes a department rule again it belongs here.
 */
private function _fx_can_update($user_id, $role_id, $department_id)
{
    return (in_array((int) $user_id, array(63, 92), true)
            || $this->_att_is_admin($user_id, $role_id));
}

/**
 * Who is REQUIRED to enter it, as opposed to merely allowed.
 *
 * Hukam (63) and Vijay (92), and nobody else. Admins may type a rate in and
 * may correct a wrong one, but the app must not hold them behind a box they
 * were never asked to fill - and an admin locked out by a rule about
 * somebody else's job is exactly the person who would have to unlock it.
 *
 * Narrower than it was: the rest of Accounts used to be held here too, and
 * were being stopped at a form for a number that was never theirs to set.
 */
private function _fx_must_update($user_id, $department_id)
{
    /* Not on a Sunday.
     *
     * The currency markets are shut and nobody is working, so the only
     * figure anyone could enter is Friday's - and holding Accounts at a form
     * on a day there is no new number is asking them to invent one, which is
     * worse than having no rate for the day at all.
     *
     * Only the REQUIREMENT lifts. Sunday can still be typed in by anyone who
     * wants to (_fx_can_update is untouched), it just is not demanded.
     *
     * date('w') is 0 for Sunday and reads the Asia/Kolkata timezone every
     * caller sets before reaching here - the box itself runs on UTC, where
     * Sunday evening is already Monday. */
    if ((int) date('w') === 0) return false;

    return in_array((int) $user_id, array(63, 92), true);
}

private function _fx_row($r)
{
    if (!$r) return null;

    return array(
        'rate_date'     => $r['rate_date'],
        'usd'           => (float) $r['usd'],
        'eur'           => (float) $r['eur'],
        'aed'           => (float) $r['aed'],
        'remark'        => (string) $r['remark'],
        'updated_by'    => (int) $r['updated_by'],
        'updated_name'  => $this->_fx_name((int) $r['updated_by']),
        'updated_at'    => $r['updated_at'],
        'changed_count' => (int) $r['changed_count'],
    );
}

private function _fx_name($user_id)
{
    if ($user_id <= 0) return '';

    $u = $this->db->select("CONCAT(first_name,' ',COALESCE(last_name,'')) AS n", FALSE)
                  ->from('system_users')
                  ->where('user_id', $user_id)
                  ->get()
                  ->row_array();

    return $u ? trim($u['n']) : '';
}

/**
 * Today's rate, for the drawer and for the entry screen.
 *
 * Answers even when nothing has been entered yet - `rate` is null and the
 * app says "not set today" rather than showing a stale figure from last
 * week as if it were current. A rate nobody entered is worse than no rate.
 */
public function exchange_rate_today_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input         = json_decode(file_get_contents("php://input"), true);
        $user_id       = isset($input['user_id']) ? (int) $input['user_id'] : 0;
        $role_id       = isset($input['role_id']) ? (int) $input['role_id'] : 0;
        $department_id = isset($input['department_id']) ? (int) $input['department_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        $this->_fx_ensure();

        $date = date('Y-m-d');

        $row = $this->db->where('rate_date', $date)
                        ->get($this->_fx_table())
                        ->row_array();

        /* The most recent rate before today, so the entry form can prefill
           and the reader can see what it was last set to. Explicitly NOT
           presented as today's. */
        $prev = $this->db->where('rate_date <', $date)
                         ->order_by('rate_date', 'DESC')
                         ->limit(1)
                         ->get($this->_fx_table())
                         ->row_array();

        /* The gate's whole question, answered here rather than worked out
           by the app: Accounts, and today is still empty. The app holds them
           at a form until this goes false. */
        $must = ($this->_fx_must_update($user_id, $department_id) && !$row);

        echo json_encode(array(
            'status'      => true,
            'date'        => $date,
            'rate'        => $this->_fx_row($row),
            'previous'    => $this->_fx_row($prev),
            'can_update'  => $this->_fx_can_update($user_id, $role_id, $department_id) ? 1 : 0,
            'must_update' => $must ? 1 : 0,
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/** Save or correct today's rate. Accounts only. */
public function exchange_rate_save_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input         = json_decode(file_get_contents("php://input"), true);
        $user_id       = isset($input['user_id']) ? (int) $input['user_id'] : 0;
        $role_id       = isset($input['role_id']) ? (int) $input['role_id'] : 0;
        $department_id = isset($input['department_id']) ? (int) $input['department_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        if (!$this->_fx_can_update($user_id, $role_id, $department_id)) {
            echo json_encode(array(
                'status'  => false,
                'message' => 'Only the Accounts department can update the exchange rate.',
            ));
            return;
        }

        $usd = isset($input['usd']) ? (float) $input['usd'] : 0;
        $eur = isset($input['eur']) ? (float) $input['eur'] : 0;
        $aed = isset($input['aed']) ? (float) $input['aed'] : 0;

        /* A rate of zero is not a rate, it is an empty box submitted by
           accident, and it would be published to the whole company as if it
           were real. An upper bound as well: a fat-fingered extra digit is
           the other way this goes wrong. */
        foreach (array('USD' => $usd, 'EUR' => $eur, 'AED' => $aed) as $code => $v) {
            if ($v <= 0) {
                echo json_encode(array(
                    'status'  => false,
                    'message' => 'Enter a rate for ' . $code . ' - it cannot be left at zero.',
                ));
                return;
            }

            if ($v > 10000) {
                echo json_encode(array(
                    'status'  => false,
                    'message' => $code . ' looks wrong (' . $v . '). Check the figure and try again.',
                ));
                return;
            }
        }

        $this->_fx_ensure();

        $date = date('Y-m-d');
        $now  = date('Y-m-d H:i:s');

        $existing = $this->db->where('rate_date', $date)
                             ->get($this->_fx_table())
                             ->row_array();

        $row = array(
            'rate_date'  => $date,
            'usd'        => $usd,
            'eur'        => $eur,
            'aed'        => $aed,
            'remark'     => isset($input['remark']) ? substr((string) $input['remark'], 0, 190) : '',
            'updated_by' => $user_id,
            'updated_at' => $now,
        );

        if ($existing) {
            $row['changed_count'] = (int) $existing['changed_count'] + 1;
            $this->db->where('id', (int) $existing['id'])
                     ->update($this->_fx_table(), $row);
        } else {
            $row['changed_count'] = 0;
            $this->db->insert($this->_fx_table(), $row);
        }

        $saved = $this->db->where('rate_date', $date)
                          ->get($this->_fx_table())
                          ->row_array();

        echo json_encode(array(
            'status'  => true,
            'message' => $existing ? 'Exchange rate updated.' : 'Exchange rate saved.',
            'date'    => $date,
            'rate'    => $this->_fx_row($saved),
        ));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}

/** The last 30 days, for the web page and anyone checking what was set. */
public function exchange_rate_history_api()
{
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Kolkata');

    try {
        $input   = json_decode(file_get_contents("php://input"), true);
        $user_id = isset($input['user_id']) ? (int) $input['user_id'] : 0;

        if (!$user_id) {
            echo json_encode(array('status' => false, 'message' => 'User ID required'));
            return;
        }

        $this->_fx_ensure();

        $limit = isset($input['limit']) ? (int) $input['limit'] : 30;
        if ($limit < 1)   $limit = 1;
        if ($limit > 180) $limit = 180;

        $rows = $this->db->order_by('rate_date', 'DESC')
                         ->limit($limit)
                         ->get($this->_fx_table())
                         ->result_array();

        $out = array();
        foreach ($rows as $r) $out[] = $this->_fx_row($r);

        echo json_encode(array('status' => true, 'count' => count($out), 'rates' => $out));

    } catch (Exception $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}


/* ======================================================================
 * CONSOLIDATED MOM PDF  -  added 2026-09-17
 *
 * Every daily MOM update for one visit, in one document, for whoever needs to
 * read the job rather than work it: admins (roles 12/41) and the Service HOD,
 * Suketan Shukla, who is user 111 and was already inside _visit_me's admin
 * test - so no new permission was needed, only this.
 *
 * Opened by URL rather than posted to, because the app hands it to
 * launchUrl(..., LaunchMode.externalApplication) and the browser or PDF
 * viewer does the rest. That means the identity arrives as query parameters,
 * so this cannot use _visit_input(), which reads a JSON body.
 * ====================================================================== */
public function visit_mom_pdf()
{
    $visit_id = (int) $this->input->get('visit_id');
    $user_id  = (int) $this->input->get('user_id');
    $role_id  = (int) $this->input->get('role_id');

    if ($visit_id <= 0 || $user_id <= 0) {
        show_error('Missing visit or user', 400);
        return;
    }

    $model = $this->_visit_model();
    $visit = $model->get_visit_by_id($visit_id);

    if (!$visit) {
        show_error('Visit not found', 404);
        return;
    }

    /* Same rule as visit_detail_api: an engineer may read their own job card,
       an admin or the Service HOD may read any of them. Repeated rather than
       shared because _visit_me() expects a JSON body this request does not
       have. */
    $is_admin = ($role_id === 12 || $role_id === 41 || $user_id === 111);

    if (!$is_admin && (int) $visit->engineer_id !== $user_id) {
        show_error('You are not allowed to read this visit', 403);
        return;
    }

    $updates = $model->get_visit_updates($visit_id);

    $e = function ($v) {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    };

    /* dompdf only understands a narrow slice of CSS - no flexbox, no grid -
       so this is tables and inline styles on purpose. */
    $html  = '<html><head><meta charset="utf-8"><style>';
    $html .= 'body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1F2937;}';
    $html .= 'h1{font-size:16px;margin:0 0 2px;color:#2C7DB7;}';
    $html .= '.sub{color:#6B7280;font-size:10px;margin-bottom:10px;}';
    $html .= 'table.meta{width:100%;border-collapse:collapse;margin-bottom:14px;}';
    $html .= 'table.meta td{padding:3px 6px;border:1px solid #E5E7EB;vertical-align:top;}';
    $html .= 'table.meta td.k{background:#F4F6F9;width:22%;font-weight:bold;}';
    $html .= '.day{border:1px solid #E5E7EB;border-left:3px solid #2C7DB7;padding:8px 10px;margin-bottom:9px;}';
    $html .= '.dayhead{font-weight:bold;font-size:11.5px;margin-bottom:4px;}';
    $html .= '.by{color:#6B7280;font-weight:normal;font-size:9.5px;}';
    $html .= '.plan{margin-top:5px;padding-top:5px;border-top:1px dashed #E5E7EB;color:#4B5563;}';
    $html .= '.none{color:#9CA3AF;font-style:italic;}';
    $html .= '</style></head><body>';

    $html .= '<h1>Minutes of Meeting - Engineer Visit</h1>';
    $html .= '<div class="sub">' . $e($visit->op_no) . ' &middot; generated '
           . $e(date('d-m-Y H:i')) . '</div>';

    $html .= '<table class="meta">';
    $html .= '<tr><td class="k">Customer</td><td>' . $e($visit->customer_name) . '</td>'
           . '<td class="k">Engineer</td><td>' . $e($visit->engineer_full_name) . '</td></tr>';
    $html .= '<tr><td class="k">Visit window</td><td>'
           . $e($visit->start_date) . ' to ' . $e($visit->end_date) . '</td>'
           . '<td class="k">Status</td><td>' . $e(ucfirst((string) $visit->status_slug)) . '</td></tr>';
    $html .= '<tr><td class="k">Contact</td><td>' . $e($visit->customer_contact_name)
           . ' ' . $e($visit->customer_contact_no) . '</td>'
           . '<td class="k">MOM entries</td><td>' . count($updates) . '</td></tr>';
    $html .= '<tr><td class="k">Site</td><td colspan="3">' . $e($visit->customer_address) . '</td></tr>';
    $html .= '</table>';

    if (empty($updates)) {
        $html .= '<div class="none">No MOM has been recorded for this visit yet.</div>';
    } else {
        /* get_visit_updates returns newest first, which is right for a screen
           you scroll and wrong for a document you read - a site diary should
           run forwards. */
        $ordered = array_reverse($updates);

        foreach ($ordered as $u) {
            $u = (array) $u;

            $html .= '<div class="day">';
            $html .= '<div class="dayhead">'
                   . $e(date('d-m-Y', strtotime($u['work_date'])))
                   . ' <span class="by">- ' . $e($u['added_by_name']) . '</span></div>';
            $html .= '<div>' . nl2br($e($u['mom_points'])) . '</div>';

            if (trim((string) $u['next_plan']) !== '') {
                $html .= '<div class="plan"><b>Next plan:</b> '
                       . nl2br($e($u['next_plan'])) . '</div>';
            }

            $html .= '</div>';
        }
    }

    if (trim((string) $visit->completion_notes) !== '') {
        $html .= '<div class="day"><div class="dayhead">Completion notes</div><div>'
               . nl2br($e($visit->completion_notes)) . '</div></div>';
    }

    $html .= '</body></html>';

    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $name = 'MOM_' . preg_replace('/[^A-Za-z0-9]+/', '_', (string) $visit->op_no)
          . '_' . $visit_id . '.pdf';

    /* Inline, not attachment: the app opens this in a viewer and a forced
       download would leave people hunting in their Downloads folder. */
    $dompdf->stream($name, array('Attachment' => false));
}

/* ==================================================================
 * OVERTIME  -  the web Overtime module, on the phone
 *
 * Everything here delegates to Overtime_model and the overtime helper,
 * which are the same two files the browser goes through. Nothing about
 * who may raise, who may decide, how long a shift may be or who gets
 * told is re-implemented: a rule that lived in two places would
 * eventually answer the two clients differently.
 *
 * The approver is user 139 (Shubham Sharma) and the model hard-codes
 * that. This file only asks.
 * ================================================================== */

/** Model + helper, loaded under the SAME alias the libraries expect. */
private function _ot_boot()
{
    $this->load->helper('overtime');
    /* Overtime_mailer and Overtime_chat both call $this->CI->overtime,
       so the alias is not a free choice. */
    $this->load->model('Overtime_model', 'overtime');
    return $this->overtime;
}

private function _ot_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json) && !empty($json)) return $json;
    }
    $post = $this->input->post();
    return is_array($post) ? $post : array();
}

private function _ot_out($payload)
{
    header('Content-Type: application/json');
    echo json_encode($payload);
}

private function _ot_fail($message, $extra = array())
{
    $this->_ot_out(array_merge(array('status' => false, 'message' => $message), $extra));
}

/** The one person who decides every overtime request. */
private function _ot_approver() { return 139; }

/**
 * The caller as the overtime module sees them - the full system_users row
 * the model's own can_view()/can_decide() take, not the id in the body.
 */
private function _ot_me($in)
{
    $id = (int) (isset($in['user_id']) ? $in['user_id'] : 0);
    if ($id <= 0) return null;

    $user = $this->overtime->user($id);
    if (!$user || empty($user['business_location'])) return null;

    return $user;
}

/** Module grants, with the same team-leader gate the web controller adds. */
private function _ot_perms($user)
{
    $p = ot_user_permissions($this->db, $user['user_id']);
    $p['create'] = $p['create'] && $this->overtime->can_request_for_team($user);
    return $p;
}

/**
 * Overtime access for get_user_permissions_api.
 *
 * Runs on every startup for every user, so it never throws and never
 * leaves the connection in the state Overtime_model's constructor puts it
 * in (db_debug off) - a later query in the same request would then fail
 * silently instead of reporting.
 */
private function _ot_app_perms($user_id)
{
    $blank = array('visible' => false, 'create' => false, 'decide' => false);
    $debug = $this->db->db_debug;

    try {
        if (!$this->db->table_exists('overtime_requests')) return $blank;

        $this->_ot_boot();
        $user = $this->overtime->user((int) $user_id);
        if (!$user || empty($user['business_location'])) return $blank;

        $p = $this->_ot_perms($user);

        return array(
            'visible' => !empty($p['requests']) || !empty($p['approvals'])
                      || !empty($p['reports'])  || !empty($p['decide']),
            'create'  => !empty($p['create']),
            'decide'  => !empty($p['decide']),
        );
    } catch (Throwable $e) {
        log_message('error', 'Overtime permission probe failed: ' . $e->getMessage());
        return $blank;
    } finally {
        $this->db->db_debug = $debug;
    }
}

private function _ot_blank($v)
{
    return ($v === null || $v === '' || $v === '0000-00-00 00:00:00');
}

/**
 * Collapse Overtime_model::listing() into one card per request.
 *
 * listing() answers ONE ROW PER PERSON, because the reports price overtime
 * by head. A phone list wants the request, so the people are folded back
 * into it here rather than by changing a query the CSV export shares.
 */
private function _ot_group($rows)
{
    $out = array();

    foreach ($rows as $r) {
        $id = (int) $r['id'];

        if (!isset($out[$id])) {
            $out[$id] = array(
                'id'                => $id,
                'request_code'      => (string) $r['request_code'],
                'status'            => (string) $r['status'],
                'status_label'      => ot_status($r['status']),
                'df_id'             => (int) $r['df_id'],
                'df_no'             => (string) $r['df_no'],
                'start_at'          => (string) $r['start_at'],
                'end_at'            => (string) $r['end_at'],
                'requested_minutes' => (int) $r['requested_minutes'],
                'hours'             => ot_hours($r['requested_minutes']),
                'reason'            => (string) $r['reason'],
                'work_reference'    => (string) $r['work_reference'],
                'department'        => (string) $r['department'],
                'requested_by'      => (int) $r['employee_id'],
                'requested_by_name' => ot_person_name($r['first_name'], $r['last_name']),
                'created_at'        => (string) $r['created_at'],
                'decided_at'        => $this->_ot_blank($r['admin_decided_at']) ? '' : (string) $r['admin_decided_at'],
                'is_pending'        => in_array($r['status'], array('PENDING_LEADER', 'PENDING_ADMIN'), true) ? 1 : 0,
                'people'            => array(),
                'people_count'      => 0,
                'cost_amount'       => 0.0,
            );
        }

        if (trim((string) $r['person_name']) !== '') {
            $out[$id]['people'][] = array(
                'user_id'     => (int) $r['worker_id'],
                'name'        => ot_person_name($r['person_name']),
                'assigned'    => empty($r['assigned_at']) ? 0 : 1,
                'cost_amount' => (float) $r['cost_amount'],
            );
            $out[$id]['people_count']++;
            $out[$id]['cost_amount'] += (float) $r['cost_amount'];
        }
    }

    foreach ($out as $id => $row) {
        $out[$id]['cost_amount']  = round($row['cost_amount'], 2);
        $out[$id]['cost_label']   = $row['cost_amount'] > 0 ? ot_money($row['cost_amount']) : '';
        /* one person for N hours reads differently from N people for one
           hour, and the approver is deciding on the second number */
        $out[$id]['person_hours'] = ot_hours(max(1, $row['people_count']) * $row['requested_minutes']);
        $out[$id]['people_label'] = $this->_ot_people_label($row['people']);
    }

    return array_values($out);
}

/** "Ramesh, Suresh +3 more" - the card has one line for this. */
private function _ot_people_label($people)
{
    $names = array();
    foreach ($people as $p) $names[] = $p['name'];
    if (!$names) return '';
    if (count($names) <= 3) return implode(', ', $names);
    return implode(', ', array_slice($names, 0, 2)) . ' +' . (count($names) - 2) . ' more';
}

/** One request, in full, for the detail screen. */
private function _ot_shape($request)
{
    $people = array();
    $cost = 0.0;

    foreach ((array) $request['assignments'] as $p) {
        $people[] = array(
            'user_id'     => (int) $p['user_id'],
            'name'        => ot_person_name($p['person_name']),
            'is_manual'   => empty($p['user_id']) ? 1 : 0,
            'assigned'    => empty($p['assigned_at']) ? 0 : 1,
            'cost_amount' => (float) $p['cost_amount'],
            'cost_label'  => ((float) $p['cost_amount']) > 0 ? ot_money($p['cost_amount']) : '',
        );
        $cost += (float) $p['cost_amount'];
    }

    $count = max(1, count($people));

    return array(
        'id'                => (int) $request['id'],
        'request_code'      => (string) $request['request_code'],
        'status'            => (string) $request['status'],
        'status_label'      => ot_status($request['status']),
        'df_id'             => (int) $request['df_id'],
        'df_no'             => (string) $request['df_no'],
        'start_at'          => (string) $request['start_at'],
        'end_at'            => (string) $request['end_at'],
        'break_minutes'     => (int) $request['break_minutes'],
        'requested_minutes' => (int) $request['requested_minutes'],
        'hours'             => ot_hours($request['requested_minutes']),
        'person_hours'      => ot_hours($count * $request['requested_minutes']),
        'reason'            => (string) $request['reason'],
        'work_reference'    => (string) $request['work_reference'],
        'department'        => (string) $request['department'],
        'requested_by'      => (int) $request['employee_id'],
        'requested_by_name' => ot_person_name($request['first_name'], $request['last_name']),
        'created_at'        => (string) $request['created_at'],
        'decided_at'        => $this->_ot_blank($request['admin_decided_at']) ? '' : (string) $request['admin_decided_at'],
        'decided_by'        => (int) $request['admin_decided_by'],
        'people'            => $people,
        'people_count'      => count($people),
        'cost_amount'       => round($cost, 2),
        'cost_label'        => $cost > 0 ? ot_money($cost) : '',
        'people_label'      => $this->_ot_people_label($people),
    );
}

private function _ot_history($id)
{
    $out = array();

    foreach ((array) $this->overtime->history((int) $id) as $h) {
        $out[] = array(
            'id'          => (int) $h['id'],
            'action'      => (string) $h['action'],
            'from_status' => (string) $h['from_status'],
            'to_status'   => (string) $h['to_status'],
            'note'        => (string) $h['note'],
            'actor_id'    => (int) $h['actor_id'],
            'actor_name'  => ot_person_name($h['first_name'], $h['last_name']),
            'created_at'  => (string) $h['created_at'],
        );
    }

    return $out;
}

/**
 * Email + chat, exactly as Overtime::announce() does it.
 *
 * Both are a courtesy on top of the in-module notification the model has
 * already written inside the transaction, so every failure is swallowed:
 * a dead SMTP host must not turn an approved request into an error.
 */
private function _ot_announce($kind, $id, $note = '')
{
    $context = null;

    try {
        $context = $this->overtime->email_context((int) $id);
    } catch (Throwable $e) {
        log_message('error', 'Overtime app notification context failed: ' . $e->getMessage());
    }

    if (!$context) return;

    foreach (array('Overtime_mailer' => 'overtime_mailer', 'Overtime_chat' => 'overtime_chat') as $class => $alias) {
        try {
            $this->load->library($class, null, $alias);
            if ($kind === 'REQUESTED') $this->$alias->request_raised($context);
            else                       $this->$alias->decision($context, $kind, $note);
        } catch (Throwable $e) {
            log_message('error', 'Overtime app ' . $alias . ' dispatch failed: ' . $e->getMessage());
        }
    }
}

/** A banner on the phone. The web module has no push, so this is app-only. */
private function _ot_push($user_id, $title, $body, $request_id)
{
    try {
        if ((int) $user_id <= 0) return;
        if (!function_exists('sendFCMData')) return;

        $devices = $this->db->select('fcm_token')->from('user_devices')
            ->where('user_id', (int) $user_id)->where('fcm_token !=', '')->get()->result();

        $preview = mb_substr((string) $body, 0, 140);

        foreach ($devices as $d) {
            if (empty($d->fcm_token)) continue;
            sendFCMData($d->fcm_token, $title, $preview, array(
                'type'       => 'overtime',
                'request_id' => (string) (int) $request_id,
                'screen'     => 'overtime_detail',
                'timestamp'  => date('Y-m-d H:i:s'),
            ));
        }
    } catch (Throwable $e) {
        log_message('error', 'Overtime push failed: ' . $e->getMessage());
    }
}

/** One line of facts, short enough for a notification tray. */
private function _ot_push_line($card)
{
    $line = ($card['df_no'] !== '' ? 'DF ' . $card['df_no'] : 'No DF') . ' · '
          . substr($card['start_at'], 0, 16) . ' · '
          . $card['hours'] . ' h';

    if ($card['people_count'] > 0) {
        $line .= ' · ' . $card['people_count']
              . ($card['people_count'] === 1 ? ' person' : ' people');
    }

    return $line;
}

/* ------------------------------------------------------------------ *
 * 1. BOOTSTRAP - what this account may do, and how much is waiting
 * ------------------------------------------------------------------ */
public function ot_bootstrap()
{
    try {
        $this->_ot_boot();
        $in = $this->_ot_input();
        $me = $this->_ot_me($in);
        if (!$me) return $this->_ot_fail('auth');

        if (!$this->overtime->module_ready()) {
            return $this->_ot_fail('Overtime is not set up on the server yet.', array('ready' => false));
        }

        date_default_timezone_set('Asia/Kolkata');

        $p = $this->_ot_perms($me);

        $inbox = 0;
        if (!empty($p['decide'])) {
            $row = $this->db->query(
                "SELECT COUNT(*) AS total FROM overtime_requests WHERE status IN ('PENDING_LEADER','PENDING_ADMIN')"
            )->row_array();
            $inbox = $row ? (int) $row['total'] : 0;
        }

        $mine = $this->overtime->summary($me, array('mine' => true));
        $policy = $this->overtime->policy($me['business_location']);

        $this->_ot_out(array(
            'status' => true,
            'ready'  => true,
            'me'     => array(
                'user_id'     => (int) $me['user_id'],
                'name'        => ot_person_name($me['first_name'], $me['last_name']),
                'department'  => (string) $me['department'],
                'is_approver' => (int) $me['user_id'] === $this->_ot_approver() ? 1 : 0,
                'is_admin'    => (int) $me['is_admin'] === 1 ? 1 : 0,
            ),
            'can' => array(
                'view'    => !empty($p['requests']) || !empty($p['approvals']) || !empty($p['reports']),
                'create'  => !empty($p['create']),
                'decide'  => !empty($p['decide']),
                'reports' => !empty($p['reports']),
            ),
            'counts' => array(
                'inbox'   => $inbox,
                'mine'    => $mine ? (int) $mine['request_count'] : 0,
                'pending' => $mine ? (int) $mine['pending_count'] : 0,
            ),
            'policy' => array(
                'max_request_minutes' => (int) $policy['max_request_minutes'],
                'max_daily_minutes'   => (int) $policy['max_daily_minutes'],
                'max_request_hours'   => ot_hours($policy['max_request_minutes']),
                'past_days'           => (int) $policy['past_days'],
                'future_days'         => (int) $policy['future_days'],
            ),
        ));
    } catch (Throwable $e) {
        $this->_ot_fail('Overtime bootstrap failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 2. OPTIONS - the two pick-lists the create form needs
 * ------------------------------------------------------------------ */
public function ot_options()
{
    try {
        $this->_ot_boot();
        $in = $this->_ot_input();
        $me = $this->_ot_me($in);
        if (!$me) return $this->_ot_fail('auth');

        if (!$this->overtime->module_ready()) {
            return $this->_ot_fail('Overtime is not set up on the server yet.', array('ready' => false));
        }

        $p = $this->_ot_perms($me);
        if (empty($p['create'])) return $this->_ot_fail('forbidden');

        $dfs = array();
        foreach ((array) $this->overtime->df_options() as $d) {
            $dfs[] = array(
                'id'    => (int) $d['id'],
                'df_no' => (string) $d['df_no'],
                'title' => trim(strip_tags((string) $d['df_description'])),
            );
        }

        $users = array();
        foreach ((array) $this->overtime->users($me['business_location']) as $u) {
            $users[] = array(
                'user_id' => (int) $u['user_id'],
                'name'    => ot_person_name($u['first_name'], $u['last_name']),
            );
        }

        $this->_ot_out(array(
            'status' => true,
            'dfs'    => $dfs,
            'users'  => $users,
            'policy' => $this->overtime->policy($me['business_location']),
        ));
    } catch (Throwable $e) {
        $this->_ot_fail('Overtime options failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 3. LIST - inbox (waiting on me) | mine | all I may see
 * ------------------------------------------------------------------ */
public function ot_list()
{
    try {
        $this->_ot_boot();
        $in = $this->_ot_input();
        $me = $this->_ot_me($in);
        if (!$me) return $this->_ot_fail('auth');

        if (!$this->overtime->module_ready()) {
            return $this->_ot_fail('Overtime is not set up on the server yet.', array('ready' => false));
        }

        date_default_timezone_set('Asia/Kolkata');

        $scope = isset($in['scope']) ? strtolower(trim((string) $in['scope'])) : 'mine';
        if (!in_array($scope, array('inbox', 'mine', 'all'), true)) $scope = 'mine';

        $p = $this->_ot_perms($me);
        if ($scope === 'inbox' && empty($p['decide'])) return $this->_ot_fail('forbidden');

        $filters = array();
        if ($scope === 'inbox')      $filters['inbox'] = true;
        elseif ($scope === 'mine')   $filters['mine']  = true;

        $status = isset($in['status']) ? strtoupper(trim((string) $in['status'])) : '';
        if (in_array($status, array('PENDING_LEADER', 'PENDING_ADMIN', 'APPROVED', 'REJECTED', 'CANCELLED'), true)) {
            $filters['status'] = $status;
        }

        /* the limit is on PERSON rows, and one request can carry a hundred
           of them, so it is deliberately generous */
        $rows = $this->overtime->listing($me, $filters, 800);

        $this->_ot_out(array(
            'status'   => true,
            'scope'    => $scope,
            'can'      => array('create' => !empty($p['create']), 'decide' => !empty($p['decide'])),
            'requests' => $this->_ot_group($rows),
        ));
    } catch (Throwable $e) {
        $this->_ot_fail('Overtime list failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 4. DETAIL - one request, its people, and its audit trail
 * ------------------------------------------------------------------ */
public function ot_detail()
{
    try {
        $this->_ot_boot();
        $in = $this->_ot_input();
        $me = $this->_ot_me($in);
        if (!$me) return $this->_ot_fail('auth');

        if (!$this->overtime->module_ready()) {
            return $this->_ot_fail('Overtime is not set up on the server yet.', array('ready' => false));
        }

        date_default_timezone_set('Asia/Kolkata');

        $id = (int) (isset($in['request_id']) ? $in['request_id'] : 0);
        if ($id <= 0) return $this->_ot_fail('request_id is required');

        $request = $this->overtime->get_request($id);
        if (!$request) return $this->_ot_fail('Request not found');
        if (!Overtime_model::can_view($request, $me)) return $this->_ot_fail('forbidden');

        $p = $this->_ot_perms($me);

        /* Mirrors Overtime::view(): the requester may pull a request back
           until it has started, and only until then. */
        $can_cancel = !empty($p['create'])
            && (int) $request['employee_id'] === (int) $me['user_id']
            && in_array($request['status'], array('PENDING_LEADER', 'PENDING_ADMIN', 'APPROVED'), true)
            && ($request['status'] !== 'APPROVED' || $request['start_at'] > date('Y-m-d H:i:s'));

        $this->_ot_out(array(
            'status'     => true,
            'request'    => $this->_ot_shape($request),
            'history'    => $this->_ot_history($id),
            'can_decide' => !empty($p['decide']) && Overtime_model::can_decide($request, $me),
            'can_cancel' => $can_cancel,
        ));
    } catch (Throwable $e) {
        $this->_ot_fail('Overtime detail failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 5. CREATE - raise a request for a team
 * ------------------------------------------------------------------ */
public function ot_create()
{
    try {
        $this->_ot_boot();
        $in = $this->_ot_input();
        $me = $this->_ot_me($in);
        if (!$me) return $this->_ot_fail('auth');

        if (!$this->overtime->module_ready()) {
            return $this->_ot_fail('Overtime is not set up on the server yet.', array('ready' => false));
        }

        date_default_timezone_set('Asia/Kolkata');

        /* The submission key is what makes a retry safe: the model returns
           the SAME request for a key it has already stored, so a timeout on
           a slow radio cannot raise the shift twice. The phone sends one
           per form; a missing one still works, it just loses that promise. */
        $key = isset($in['submission_key']) ? (string) $in['submission_key'] : '';
        if (!preg_match('/^[a-f0-9]{64}$/D', $key)) $key = bin2hex(random_bytes(32));

        $ids = array();
        if (isset($in['user_ids']) && is_array($in['user_ids'])) {
            foreach ($in['user_ids'] as $id) $ids[] = (string) (int) $id;
        }

        $values = array(
            'df_id'          => isset($in['df_id']) ? (int) $in['df_id'] : 0,
            'start_at'       => isset($in['start_at']) ? (string) $in['start_at'] : '',
            'hours'          => isset($in['hours']) ? (string) $in['hours'] : '',
            'manual_people'  => isset($in['manual_people']) ? (string) $in['manual_people'] : '',
            'reason'         => isset($in['reason']) ? (string) $in['reason'] : '',
            'work_reference' => isset($in['work_reference']) ? (string) $in['work_reference'] : '',
            'user_ids'       => $ids,
        );

        $id = (int) $this->overtime->submit((int) $me['user_id'], $values, $key);

        $this->_ot_announce('REQUESTED', $id);

        /* The push is the only part of this that is app-only, so it is
           sent here rather than from the shared announce path. */
        try {
            $card = $this->_ot_shape($this->overtime->get_request($id));
            $this->_ot_push(
                $this->_ot_approver(),
                'Overtime approval needed - ' . $card['request_code'],
                $card['requested_by_name'] . ' · ' . $this->_ot_push_line($card),
                $id
            );
        } catch (Throwable $e) {
            log_message('error', 'Overtime request push failed: ' . $e->getMessage());
        }

        $this->_ot_out(array(
            'status'     => true,
            'request_id' => $id,
            'message'    => 'Sent to Shubham Sharma for approval, by push, chat and email.',
        ));
    } catch (InvalidArgumentException $e) {
        $this->_ot_fail($e->getMessage());
    } catch (Throwable $e) {
        log_message('error', 'Overtime app submission failed: ' . $e->getMessage());
        $this->_ot_fail('Could not save the request. Please try again.');
    }
}

/* ------------------------------------------------------------------ *
 * 6. DECIDE - approve, reject (with remarks) or cancel
 *
 * The same endpoint the in-chat buttons call. Who may do what is the
 * model's answer, not this file's, so an approve tapped from a chat
 * bubble goes through exactly the checks the web form goes through.
 * ------------------------------------------------------------------ */
public function ot_decide()
{
    try {
        $this->_ot_boot();
        $in = $this->_ot_input();
        $me = $this->_ot_me($in);
        if (!$me) return $this->_ot_fail('auth');

        if (!$this->overtime->module_ready()) {
            return $this->_ot_fail('Overtime is not set up on the server yet.', array('ready' => false));
        }

        date_default_timezone_set('Asia/Kolkata');

        $id = (int) (isset($in['request_id']) ? $in['request_id'] : 0);
        if ($id <= 0) return $this->_ot_fail('request_id is required');

        $decision = strtoupper(trim((string) (isset($in['decision']) ? $in['decision'] : '')));
        if (!in_array($decision, array('APPROVE', 'REJECT', 'CANCEL'), true)) {
            return $this->_ot_fail('Invalid action.');
        }

        $note = isset($in['remarks']) ? (string) $in['remarks'] : '';

        $request = $this->overtime->get_request($id);
        if (!$request) return $this->_ot_fail('Request not found');
        if (!Overtime_model::can_view($request, $me)) return $this->_ot_fail('forbidden');

        $status = $this->overtime->decide($id, (int) $me['user_id'], $decision, $note);

        if (in_array($status, array('APPROVED', 'REJECTED'), true)) {
            $this->_ot_announce($status, $id, $note);

            try {
                $card = $this->_ot_shape($this->overtime->get_request($id));
                $verb = $status === 'APPROVED' ? 'approved' : 'rejected';

                $told = array();
                $recipients = array((int) $request['employee_id'], (int) $request['leader_id']);
                if ($status === 'APPROVED') {
                    foreach ($card['people'] as $person) {
                        if ($person['user_id'] > 0) $recipients[] = (int) $person['user_id'];
                    }
                }

                foreach ($recipients as $uid) {
                    if ($uid <= 0 || $uid === (int) $me['user_id'] || isset($told[$uid])) continue;
                    $told[$uid] = true;
                    $this->_ot_push(
                        $uid,
                        'Overtime ' . $verb . ' - ' . $card['request_code'],
                        $this->_ot_push_line($card) . (trim($note) === '' ? '' : ' · ' . $note),
                        $id
                    );
                }
            } catch (Throwable $e) {
                log_message('error', 'Overtime decision push failed: ' . $e->getMessage());
            }
        }

        $this->_ot_out(array(
            'status'     => true,
            'new_status' => $status,
            'message'    => 'Request updated: ' . ot_status($status) . '.',
        ));
    } catch (InvalidArgumentException $e) {
        $this->_ot_fail($e->getMessage());
    } catch (Throwable $e) {
        log_message('error', 'Overtime app decision failed: ' . $e->getMessage());
        $this->_ot_fail('Could not save the decision. Reload the request and try again.');
    }
}


/* ==================================================================== *
 * CANCELLED QUOTATIONS - SERVICE
 * The web auto-cancels a quotation a month old and keeps the stage it
 * was in (Service_quotation_expiry_model). Reopen puts it back there.
 * Who may reopen = ServiceLeads::reopen_cancelled_quotation: the service
 * HOD / an admin, or the opportunity's own marketing person.
 * ==================================================================== */

private function _svc_expiry_model()
{
    $this->load->model('Service_quotation_expiry_model', 'svc_expiry');
    return $this->svc_expiry;
}

private function _svc_cancelled_stage_id()
{
    static $id = null;
    if ($id !== null) return $id;
    /* raw query(), never the builder: these helpers are called while a
       caller's builder query is half-built, and would merge into it */
    $row = $this->db->query("SELECT stage_id FROM service_lead_stages
        WHERE LOWER(TRIM(stage_name)) = 'cancelled quotation' LIMIT 1")->row();
    return $id = $row ? (int)$row->stage_id : 0;
}

/** Web ServiceLeads follow-up exclusion, by NAME as the web resolves it. */
private function _svc_followup_excluded_ids()
{
    static $ids = null;
    if ($ids !== null) return $ids;
    $rows = $this->db->query("SELECT stage_id FROM service_lead_stages
        WHERE LOWER(TRIM(stage_name)) IN ('cancelled quotation','po received','order won','create pi')")->result();
    $ids = array();
    foreach ($rows as $r) $ids[] = (int)$r->stage_id;
    return $ids;
}

private function _svc_can_reopen_all($user_id)
{
    if ($user_id <= 0) return false;
    $u = $this->db->select('user_role_id')->from('system_users')->where('user_id', $user_id)->get()->row();
    $role = $u ? (int)$u->user_role_id : 0;
    if (in_array($role, array(12, 41), true)) return true;
    if (!function_exists('pms_role_is_super_admin')) $this->load->helper('admin_access');
    if (function_exists('pms_role_is_super_admin') && pms_role_is_super_admin($role, $user_id)) return true;

    $hod = $this->db->select('team_leader')->from('prestogroup_teams')
        ->where('business_loc_id', 2)->where('department_id', 22)->where('status', 1)
        ->order_by('team_id', 'ASC')->limit(1)->get()->row();
    return $hod && (int)$hod->team_leader === $user_id;
}

private function _svc_cancellation($opportunity_id, $user_id)
{
    $out = array('is_cancelled' => false, 'previous_stage_id' => 0, 'previous_stage_name' => '',
                 'expired_on' => '', 'quotation_date' => '', 'can_reopen' => false);
    try {
        $cid = $this->_svc_cancelled_stage_id();
        $opp = $this->db->select('current_stage_id, marketing_person_id')->from('service_opportunities')
            ->where('opportunity_id', $opportunity_id)->get()->row();
        if (!$opp || $cid <= 0 || (int)$opp->current_stage_id !== $cid) return $out;

        $out['is_cancelled'] = true;
        $c = $this->_svc_expiry_model()->get_active_cancellation($opportunity_id);
        if ($c) {
            $out['previous_stage_id']   = (int)$c->previous_stage_id;
            $out['previous_stage_name'] = (string)$c->previous_stage_name;
            $out['expired_on']          = (string)$c->expired_on;
            $out['quotation_date']      = (string)$c->quotation_date;
        }
        $out['can_reopen'] = $c && ($this->_svc_can_reopen_all($user_id)
                                    || (int)$opp->marketing_person_id === $user_id);
    } catch (Throwable $e) {
        log_message('error', 'service cancellation lookup: ' . $e->getMessage());
    }
    return $out;
}

public function service_reopen_quotation_api()
{
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input)) $input = $this->input->post();
        $user_id = (int)($input['user_id'] ?? 0);
        $opportunity_id = (int)($input['opportunity_id'] ?? 0);
        if ($user_id <= 0 || $opportunity_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Invalid request')); return;
        }

        $info = $this->_svc_cancellation($opportunity_id, $user_id);
        if (!$info['is_cancelled']) {
            echo json_encode(array('status' => false, 'message' => 'This quotation is not cancelled.')); return;
        }
        if (!$info['can_reopen']) {
            echo json_encode(array('status' => false, 'message' => 'You do not have permission to reopen this quotation.')); return;
        }

        $r = $this->_svc_expiry_model()->reopen($opportunity_id, $user_id);
        echo json_encode(array(
            'status'   => !empty($r['success']),
            'message'  => (string)($r['message'] ?? ''),
            'stage_id' => (int)($r['stage_id'] ?? 0),
        ));
    } catch (Throwable $e) {
        echo json_encode(array('status' => false, 'message' => 'Reopen failed: ' . $e->getMessage()));
    }
}


/* ==================================================================== *
 * CANCELLED QUOTATIONS - SPARE
 * Spare_quotation_expiry_model keeps the stage, status and probability
 * a quotation had before the auto-cancel. Who may reopen =
 * Spares::reopen_cancelled_quotation: the all-quotations managers, or
 * the opportunity's own marketing person.
 * ==================================================================== */

private function _spare_expiry_model()
{
    $this->load->model('Spare_quotation_expiry_model', 'spare_expiry');
    return $this->spare_expiry;
}

private function _spare_cancelled_stage_id()
{
    static $id = null;
    if ($id !== null) return $id;
    $row = $this->db->query("SELECT lead_id FROM spare_lead_stage
        WHERE LOWER(TRIM(lead_name)) = 'cancelled quotation' LIMIT 1")->row();
    return $id = $row ? (int)$row->lead_id : 0;
}

/** Spare keeps no current-stage column: it is the latest remark's stage. */
private function _spare_latest_stage($opportunity_id)
{
    $row = $this->db->select('lead_stage')->from('spare_progress_remarks')
        ->where('lead_id', (int)$opportunity_id)->order_by('id', 'DESC')->limit(1)->get()->row();
    return $row ? (int)$row->lead_stage : 0;
}

/** Spares::current_user_can_manage_all_spares_quotations, by user id. */
private function _spare_can_reopen_all($user_id)
{
    if ($user_id <= 0) return false;
    $u = $this->db->select('user_role_id')->from('system_users')->where('user_id', $user_id)->get()->row();
    $role = $u ? (int)$u->user_role_id : 0;
    if (in_array($role, array(12, 41), true)) return true;
    if (in_array($user_id, array(61, 111, 114, 139, 161, 189), true)) return true;
    if (!function_exists('pms_role_is_super_admin')) $this->load->helper('admin_access');
    return function_exists('pms_role_is_super_admin') && pms_role_is_super_admin($role, $user_id);
}

private function _spare_cancellation($opportunity_id, $user_id)
{
    $out = array('is_cancelled' => false, 'previous_stage_id' => 0, 'previous_stage_name' => '',
                 'expired_on' => '', 'quotation_date' => '', 'can_reopen' => false);
    try {
        $cid = $this->_spare_cancelled_stage_id();
        if ($cid <= 0 || $this->_spare_latest_stage($opportunity_id) !== $cid) return $out;

        $out['is_cancelled'] = true;
        $c = $this->_spare_expiry_model()->get_active_cancellation($opportunity_id);
        if ($c) {
            $out['previous_stage_id']   = (int)$c->previous_stage_id;
            $out['previous_stage_name'] = (string)($c->previous_stage_name ?? '');
            $out['expired_on']          = (string)$c->expired_on;
            $out['quotation_date']      = (string)$c->quotation_date;
        }
        $opp = $this->db->select('marketing_person_id')->from('opportunities')
            ->where('opportunity_id', $opportunity_id)->get()->row();
        $out['can_reopen'] = $c && ($this->_spare_can_reopen_all($user_id)
                                    || ($opp && (int)$opp->marketing_person_id === $user_id));
    } catch (Throwable $e) {
        log_message('error', 'spare cancellation lookup: ' . $e->getMessage());
    }
    return $out;
}

public function spare_reopen_quotation_api()
{
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input)) $input = $this->input->post();
        $user_id = (int)($input['user_id'] ?? 0);
        $opportunity_id = (int)($input['opportunity_id'] ?? 0);
        if ($user_id <= 0 || $opportunity_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Invalid request')); return;
        }

        $info = $this->_spare_cancellation($opportunity_id, $user_id);
        if (!$info['is_cancelled']) {
            echo json_encode(array('status' => false, 'message' => 'This quotation is not cancelled.')); return;
        }
        if (!$info['can_reopen']) {
            echo json_encode(array('status' => false, 'message' => 'You do not have permission to reopen this quotation.')); return;
        }

        $r = $this->_spare_expiry_model()->reopen($opportunity_id, $user_id);
        echo json_encode(array(
            'status'   => !empty($r['success']),
            'message'  => (string)($r['message'] ?? ''),
            'stage_id' => (int)($r['stage_id'] ?? 0),
        ));
    } catch (Throwable $e) {
        echo json_encode(array('status' => false, 'message' => 'Reopen failed: ' . $e->getMessage()));
    }
}

/* ==================================================================== *
 * CHAT - archive / reopen / bundle download / delete group
 * Same model calls as the web (Chat::archive_channel, unarchive_channel,
 * archive_zip, delete_channel). One deliberate difference: deleting a
 * group is the OWNER only, with no admin override.
 * ==================================================================== */

public function chat_archive()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $conv = $this->chat->get_conversation($conv_id);
    if (!$conv || $conv->type === 'direct') {
        echo json_encode(array("status" => false, "message" => "Only groups can be archived.")); return;
    }
    if (!$this->_chat_can_manage($conv_id, $me)) {
        echo json_encode(array("status" => false, "message" => "Only the group owner or an admin can archive it.")); return;
    }

    $r = $this->chat->archive_conversation($conv_id, $me, trim((string)($input['reason'] ?? '')));
    if (empty($r['ok'])) { echo json_encode(array("status" => false, "message" => $r['error'])); return; }

    echo json_encode(array("status" => true, "files" => (int)$r['files'], "bytes" => (int)$r['bytes']));
}

public function chat_unarchive()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    if (!$this->_chat_can_manage($conv_id, $me)) {
        echo json_encode(array("status" => false, "message" => "Only the group owner or an admin can reopen it.")); return;
    }

    $r = $this->chat->unarchive_conversation($conv_id, $me);
    if (empty($r['ok'])) { echo json_encode(array("status" => false, "message" => $r['error'])); return; }

    echo json_encode(array("status" => true, "restored" => $r['restored']));
}

/** The archived group's file bundle. GET ?user_id=&conversation_id= so a
 *  browser can open it; membership-checked like every other chat read.
 *  Streamed, never read into memory - a busy DF group's zip is large. */
public function chat_archive_zip()
{
    $this->load->model('Chat_model', 'chat');
    $this->load->helper('chat_access');

    $uid     = (int)$this->input->get_post('user_id');
    $conv_id = (int)$this->input->get_post('conversation_id');
    $me      = $uid ? $this->_chat_me($uid) : null;

    if (!$me || !$this->chat->is_participant($conv_id, $me)) {
        show_error('You do not have access to this group.', 403); return;
    }
    $conv = $this->chat->get_conversation($conv_id);
    if (!$conv || empty($conv->archive_zip)) {
        show_error('This group has no archived files.', 404); return;
    }
    $path = chat_upload_path . $conv->archive_zip;
    if (!is_file($path)) {
        show_error('The archive bundle for this group is missing from the server.', 404); return;
    }

    $name = preg_replace('/[^A-Za-z0-9._-]+/', '-', $conv->name) . '-files.zip';
    if (ob_get_level()) @ob_end_clean();
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Content-Length: ' . filesize($path));
    header('X-Content-Type-Options: nosniff');
    readfile($path);
    exit;
}

/** Delete a group - its OWNER only. Soft delete, as on the web. */
public function chat_delete_group()
{
    $input   = $this->_chat_boot($me);
    $conv_id = (int)($input['conversation_id'] ?? 0);
    if (!$this->_chat_guard($conv_id, $me)) return;

    $conv = $this->chat->get_conversation($conv_id);
    if (!$conv || $conv->type === 'direct') {
        echo json_encode(array("status" => false, "message" => "Only groups can be deleted.")); return;
    }
    if (!$this->_chat_is_owner($conv_id, $me)) {
        echo json_encode(array("status" => false, "message" => "Only the group owner can delete this group.")); return;
    }

    $this->chat->delete_channel($conv_id, $me);
    echo json_encode(array("status" => true));
}


/* ==================================================================== *
 * SERVICE PAYMENT REQUESTS - additional amount against a visit
 *
 * The web module (ServiceLeads::save_service_payment_request /
 * update_service_payment_request_status, Service_payment_request_model)
 * already has "Linked Request" (parent_request_id) and the HOD's
 * revise-on-approve (approved_amount). These endpoints expose that to
 * the app for one visit, with three rules the web does not have:
 *
 *  - the engineer who owns the visit may raise an additional amount,
 *    even without submodule 72 (no service engineer holds it);
 *  - an approved additional amount is ADDED to the previous request:
 *    the child row keeps its own figures (amount is always what was
 *    asked for), the parent carries the merged total, and a log line
 *    is written on the parent;
 *  - Gurpreet (141, who raises every payment request in practice) is
 *    notified of each approval, as well as the people the web notifies.
 * ==================================================================== */

/** Gurpreet Kaur - Accounts for service payments. By id, as agreed. */
private $spr_accounts_user_id = 141;

private function _spr_boot()
{
    $this->load->model('Service_payment_request_model', 'service_payment_request_model');
    $this->service_payment_request_model->ensure_tables();
    return $this->service_payment_request_model;
}

private function _spr_input()
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json) && !empty($json)) return $json;
    }
    $post = $this->input->post();
    return is_array($post) ? $post : array();
}

private function _spr_out($payload) { echo json_encode($payload); }

private function _spr_fail($message, $extra = array())
{
    $this->_spr_out(array_merge(array('status' => false, 'message' => $message), $extra));
}

/** Submodule by NAME under module 17, read only - never inserts one. */
private function _spr_has_submodule($user_id, $name)
{
    $sub = $this->db->select('id')->from('submodule')
        ->where('moduleid', 17)->where('submodule', $name)->limit(1)->get()->row();
    if (empty($sub)) return false;

    return $this->db->select('id')->from('module_capablity')
        ->where('role_id', (int) $user_id)
        ->where('moduleid', 17)
        ->where('submoduleid', (int) $sub->id)
        ->where('submodule_access', '1')
        ->limit(1)->get()->num_rows() > 0;
}

private function _spr_hod_id()
{
    $row = $this->db->select('team_leader')->from('prestogroup_teams')
        ->where('business_loc_id', 2)->where('department_id', 22)->where('status', 1)
        ->order_by('team_id', 'ASC')->limit(1)->get()->row();
    return $row ? (int) $row->team_leader : 0;
}

private function _spr_me($in)
{
    $id = (int) ($in['user_id'] ?? 0);
    if ($id <= 0) return null;
    $hod = $this->_spr_hod_id();

    return array(
        'id'          => $id,
        'can_create'  => $this->_spr_has_submodule($id, 'SERVICE PAYMENT REQUESTS'),
        /* the web's approver test is submodule 73; the service HOD is
           Mr. Suketan and is always an approver here */
        'can_approve' => ($hod > 0 && $id === $hod)
                          || $this->_spr_has_submodule($id, 'SERVICE PAYMENT APPROVALS'),
    );
}

private function _spr_can_view($row, $me)
{
    if (!empty($me['can_approve'])) return true;
    return in_array((int) $me['id'], array(
        (int) $row->created_by, (int) $row->requested_for_user_id, (int) $row->engineer_id,
    ), true);
}

private function _spr_notify($user_id, $title, $message, $request_id, $visit_id, $screen = 'spr_visit')
{
    if ((int) $user_id <= 0) return;
    try {
        $this->db->insert('app_notifications', array(
            'user_id'      => (int) $user_id,
            'title'        => $title,
            'message'      => $message,
            'type'         => 'service_payment_request',
            'reference_id' => (int) $request_id,
            'created_at'   => date('Y-m-d H:i:s'),
        ));

        if (!function_exists('sendFCMData')) return;
        $devices = $this->db->select('fcm_token')->from('user_devices')
            ->where('user_id', (int) $user_id)->where('fcm_token !=', '')->get()->result();
        foreach ($devices as $d) {
            if (empty($d->fcm_token)) continue;
            sendFCMData($d->fcm_token, $title, mb_substr($message, 0, 160), array(
                'type'       => 'service_payment_request',
                'request_id' => (string) (int) $request_id,
                'visit_id'   => (string) (int) $visit_id,
                /* spr_approvals = the approver's queue; spr_visit = the visit */
                'screen'     => $screen,
                'timestamp'  => date('Y-m-d H:i:s'),
            ));
        }
    } catch (Throwable $e) {
        log_message('error', 'SPR notify failed: ' . $e->getMessage());
    }
}

private function _spr_shape($r)
{
    return array(
        'request_id'          => (int) $r->request_id,
        'request_code'        => (string) $r->request_code,
        'parent_request_id'   => (int) $r->parent_request_id,
        'parent_request_code' => (string) ($r->parent_request_code ?? ''),
        'visit_id'            => (int) $r->visit_id,
        'opportunity_id'      => (int) $r->opportunity_id,
        'op_no'               => (string) $r->op_no,
        'customer_name'       => (string) ($r->customer_company_name ?? $r->customer_name),
        'request_type'        => (string) $r->request_type,
        'request_basis'       => (string) $r->request_basis,
        'amount'              => (float) $r->amount,
        'approved_amount'     => $r->approved_amount === null ? null : (float) $r->approved_amount,
        'payable_amount'      => (float) $r->payable_amount,
        'amount_revised'      => (bool) $r->amount_revised,
        'purpose'             => (string) $r->purpose,
        'note'                => (string) ($r->extension_reason ?? ''),
        'status'              => (string) $r->status,
        'engineer_name'       => (string) $r->engineer_name,
        'created_by_name'     => (string) $r->created_by_name,
        'hod_name'            => (string) $r->hod_name,
        'hod_remarks'         => (string) ($r->hod_remarks ?? ''),
        'hod_action_on'       => (string) ($r->hod_action_on ?? ''),
        'request_date'        => (string) $r->request_date,
        'created_at'          => (string) $r->created_at,
    );
}

/**
 * Requests on one visit's order, folded parent -> additional children.
 * A parent's `merged_total` = its payable amount + every APPROVED
 * additional amount linked to it: that is "added to the previous request".
 */
private function _spr_visit_rows($visit, $me)
{
    $model = $this->service_payment_request_model;
    $rows = $model->get_request_rows(array('opportunity_id' => (int) $visit->opportunity_id));

    $by_id = array();
    foreach ($rows as $r) {
        /* this visit's requests, plus order-level ones (no visit) it can top up */
        if ((int) $r->visit_id > 0 && (int) $r->visit_id !== (int) $visit->visit_id) continue;
        if (!$this->_spr_can_view($r, $me)) continue;
        $by_id[(int) $r->request_id] = $r;
    }

    $parents = array();
    $orphans = array();
    foreach ($by_id as $id => $r) {
        if ((int) $r->parent_request_id > 0) continue;
        $p = $this->_spr_shape($r);
        $p['additional'] = array();
        $p['additional_approved_total'] = 0.0;
        $p['additional_pending_total'] = 0.0;
        $parents[$id] = $p;
    }
    foreach ($by_id as $id => $r) {
        $pid = (int) $r->parent_request_id;
        if ($pid <= 0) continue;
        $c = $this->_spr_shape($r);
        if (!isset($parents[$pid])) { $orphans[] = $c; continue; }
        $parents[$pid]['additional'][] = $c;
        if ($c['status'] === 'Approved') $parents[$pid]['additional_approved_total'] += $c['payable_amount'];
        if ($c['status'] === 'Pending HOD Approval') $parents[$pid]['additional_pending_total'] += $c['amount'];
    }
    foreach ($parents as &$p) {
        $p['merged_total'] = ($p['status'] === 'Approved' ? $p['payable_amount'] : 0)
                           + $p['additional_approved_total'];
        /* an additional amount can only top up a request that was approved */
        $p['can_add_to'] = $p['status'] === 'Approved';
    }
    unset($p);

    return array('requests' => array_values($parents), 'unlinked' => $orphans);
}

/* ------------------------------------------------------------------ *
 * 1. The payment requests of one visit, and what this user may do
 * ------------------------------------------------------------------ */
public function spr_visit_api()
{
    try {
        $in = $this->_spr_input();
        $me = $this->_spr_me($in);
        if (!$me) return $this->_spr_fail('auth');
        $this->_spr_boot();
        date_default_timezone_set('Asia/Kolkata');

        $visit_id = (int) ($in['visit_id'] ?? 0);
        $visit = $this->_visit_model()->get_visit_by_id($visit_id);
        if (empty($visit)) return $this->_spr_fail('Visit not found');

        $is_engineer = (int) $visit->engineer_id === (int) $me['id'];
        if (!$is_engineer && !$me['can_create'] && !$me['can_approve']) {
            return $this->_spr_out(array('status' => true, 'visible' => false, 'requests' => array()));
        }

        $data = $this->_spr_visit_rows($visit, $me);
        $this->_spr_out(array(
            'status'         => true,
            'visible'        => true,
            'can_raise'      => $is_engineer || $me['can_create'] || $me['can_approve'],
            'can_approve'    => $me['can_approve'],
            'requests'       => $data['requests'],
            'unlinked'       => $data['unlinked'],
        ));
    } catch (Throwable $e) {
        $this->_spr_fail('Payment requests failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 2. Raise an additional amount, linked to a previous request
 *    (web rules: same order; a visit-basis parent must be this visit)
 * ------------------------------------------------------------------ */
public function spr_raise_api()
{
    try {
        $in = $this->_spr_input();
        $me = $this->_spr_me($in);
        if (!$me) return $this->_spr_fail('auth');
        $model = $this->_spr_boot();
        date_default_timezone_set('Asia/Kolkata');

        $visit_id  = (int) ($in['visit_id'] ?? 0);
        $parent_id = (int) ($in['parent_request_id'] ?? 0);
        $amount    = (float) str_replace(',', '', (string) ($in['amount'] ?? '0'));
        $purpose   = trim((string) ($in['purpose'] ?? ''));
        $note      = trim((string) ($in['note'] ?? ''));

        $visit = $this->_visit_model()->get_visit_by_id($visit_id);
        if (empty($visit)) return $this->_spr_fail('Visit not found');

        $is_engineer = (int) $visit->engineer_id === (int) $me['id'];
        if (!$is_engineer && !$me['can_create'] && !$me['can_approve']) {
            return $this->_spr_fail('You cannot raise a payment request for this visit.');
        }
        if ($amount <= 0) return $this->_spr_fail('Enter an amount greater than zero.');
        if ($purpose === '') return $this->_spr_fail('Purpose is required.');

        $parent = $model->get_request_by_id($parent_id);
        if (empty($parent)) return $this->_spr_fail('Select the previous payment request to add this to.');
        if ((int) $parent->parent_request_id > 0) {
            return $this->_spr_fail('Add the amount to the original request, not to another additional amount.');
        }
        if (trim((string) $parent->status) !== 'Approved') {
            return $this->_spr_fail('Only an approved request can take an additional amount.');
        }
        if ((int) $parent->opportunity_id !== (int) $visit->opportunity_id) {
            return $this->_spr_fail('Additional funding can only be linked with requests from the same won order.');
        }
        if ((int) $parent->visit_id > 0 && (int) $parent->visit_id !== (int) $visit->visit_id) {
            return $this->_spr_fail('Visit-based linked requests must stay linked to the same engineer visit.');
        }

        $hod_id = $this->_spr_hod_id();
        if ($hod_id <= 0) return $this->_spr_fail('Service HOD is not mapped yet.');

        $code = $model->get_next_request_code();
        $data = array(
            'request_code'          => $code,
            'opportunity_id'        => (int) $parent->opportunity_id,
            'visit_id'              => (int) $visit->visit_id,
            'parent_request_id'     => (int) $parent->request_id,
            'request_basis'         => (string) $parent->request_basis,
            'request_type'          => (string) $parent->request_type,
            'request_title'         => 'Additional amount',
            'op_no'                 => (string) $parent->op_no,
            'customer_id'           => (int) $parent->customer_id,
            'customer_name'         => (string) $parent->customer_name,
            'po_number'             => (string) $parent->po_number,
            'po_date'               => !empty($parent->po_date) ? $parent->po_date : null,
            'po_amount'             => (float) $parent->po_amount,
            'engineer_id'           => (int) $visit->engineer_id,
            'requested_for_user_id' => (int) $visit->engineer_id,
            'service_from_date'     => $visit->start_date,
            'service_to_date'       => $visit->end_date,
            'request_date'          => date('Y-m-d'),
            'amount'                => $amount,
            'purpose'               => $purpose,
            'extension_reason'      => $note !== '' ? $note : null,
            'status'                => 'Pending HOD Approval',
            'hod_id'                => $hod_id,
            'created_by'            => (int) $me['id'],
            'created_at'            => date('Y-m-d H:i:s'),
        );

        $this->db->trans_start();
        $request_id = $model->create_request($data);
        $this->db->trans_complete();
        if (!$this->db->trans_status() || $request_id <= 0) {
            return $this->_spr_fail('The request could not be saved. Please try again.');
        }

        $this->_spr_notify(
            $hod_id,
            'Additional Amount Approval Required',
            'Additional Rs. ' . number_format($amount, 2) . ' requested (' . $code . ') on '
                . $parent->request_code . ' for ' . $parent->customer_name . ': ' . $purpose,
            $request_id,
            (int) $visit->visit_id,
            'spr_approvals'
        );

        $this->_spr_out(array(
            'status'       => true,
            'message'      => 'Additional amount ' . $code . ' sent for approval.',
            'request_id'   => $request_id,
            'request_code' => $code,
        ));
    } catch (Throwable $e) {
        $this->_spr_fail('Could not raise the request: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 3. Approver's queue - every request waiting on the HOD
 * ------------------------------------------------------------------ */
public function spr_pending_api()
{
    try {
        $in = $this->_spr_input();
        $me = $this->_spr_me($in);
        if (!$me) return $this->_spr_fail('auth');
        if (!$me['can_approve']) return $this->_spr_fail('forbidden');
        $model = $this->_spr_boot();

        $out = array();
        foreach ($model->get_request_rows(array('status' => 'Pending HOD Approval')) as $r) {
            $row = $this->_spr_shape($r);
            $row['parent_request_amount'] = (float) $r->parent_request_amount;
            $out[] = $row;
        }
        $this->_spr_out(array('status' => true, 'requests' => $out, 'count' => count($out)));
    } catch (Throwable $e) {
        $this->_spr_fail('Pending requests failed: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ *
 * 4. Approve (optionally at a revised amount) or reject.
 *    Mirrors ServiceLeads::update_service_payment_request_status.
 * ------------------------------------------------------------------ */
public function spr_decide_api()
{
    try {
        $in = $this->_spr_input();
        $me = $this->_spr_me($in);
        if (!$me) return $this->_spr_fail('auth');
        if (!$me['can_approve']) return $this->_spr_fail('You do not have permission to approve service payment requests.');
        $model = $this->_spr_boot();
        date_default_timezone_set('Asia/Kolkata');

        $request_id = (int) ($in['request_id'] ?? 0);
        $action  = strtolower(trim((string) ($in['action'] ?? '')));
        $remarks = trim((string) ($in['remarks'] ?? ''));

        $request = $model->get_request_by_id($request_id);
        if (empty($request)) return $this->_spr_fail('Payment request not found.');
        if (trim((string) $request->status) !== 'Pending HOD Approval') {
            return $this->_spr_fail('This request has already been processed.');
        }
        if (!in_array($action, array('approved', 'rejected'), true)) return $this->_spr_fail('Invalid approval action.');
        if ($action === 'rejected' && $remarks === '') return $this->_spr_fail('Rejection remarks are required.');

        $requested_amount = (float) $request->amount;
        $approved_amount = $requested_amount;
        if ($action === 'approved') {
            $posted = trim((string) ($in['approved_amount'] ?? ''));
            if ($posted !== '') $approved_amount = (float) str_replace(',', '', $posted);
            if ($approved_amount <= 0) return $this->_spr_fail('The approved amount must be greater than zero.');
        }
        $amount_revised = $action === 'approved' && abs($approved_amount - $requested_amount) > 0.009;
        if ($amount_revised && $remarks === '') {
            return $this->_spr_fail('Please add remarks explaining the revised amount.');
        }

        $status_label = $action === 'approved' ? 'Approved' : 'Rejected';
        $actor_id = (int) $me['id'];
        $now = date('Y-m-d H:i:s');

        $parent = null;
        if ($action === 'approved' && (int) $request->parent_request_id > 0) {
            $parent = $model->get_request_by_id((int) $request->parent_request_id);
        }

        $this->db->trans_start();
        $model->update_request($request_id, array(
            'status'          => $status_label,
            'approved_amount' => $action === 'approved' ? $approved_amount : null,
            'hod_id'          => $actor_id,
            'hod_remarks'     => $remarks !== '' ? $remarks : null,
            'hod_action_on'   => $now,
            'updated_by'      => $actor_id,
            'updated_at'      => $now,
        ));
        if ($amount_revised) {
            $model->log_action($request_id, 'AMOUNT_REVISED',
                'Amount revised by HOD from Rs. ' . number_format($requested_amount, 2)
                    . ' to Rs. ' . number_format($approved_amount, 2) . '.',
                $actor_id,
                array('requested_amount' => $requested_amount, 'approved_amount' => $approved_amount,
                      'request_code' => (string) $request->request_code, 'source' => 'app'),
                $status_label);
        }
        $model->log_action($request_id, $action === 'approved' ? 'HOD_APPROVED' : 'HOD_REJECTED',
            $remarks, $actor_id,
            array('amount' => $requested_amount,
                  'approved_amount' => $action === 'approved' ? $approved_amount : null,
                  'request_code' => (string) $request->request_code, 'source' => 'app'),
            $status_label);
        if (!empty($parent)) {
            /* the parent row's own figures are never rewritten - the web
               reads approved_amount != amount as "HOD revised this" */
            $model->log_action((int) $parent->request_id, 'ADDITIONAL_APPROVED',
                'Additional Rs. ' . number_format($approved_amount, 2) . ' (' . $request->request_code
                    . ') approved and added to this request.',
                $actor_id,
                array('child_request_id' => $request_id, 'child_request_code' => (string) $request->request_code,
                      'approved_amount' => $approved_amount, 'source' => 'app'),
                (string) $parent->status);
        }
        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            return $this->_spr_fail('The request could not be updated due to a database issue.');
        }

        $merged_total = null;
        if (!empty($parent)) {
            $row = $this->db->query(
                "SELECT SUM(COALESCE(NULLIF(approved_amount, 0), amount)) AS t
                   FROM service_payment_requests
                  WHERE status = 'Approved' AND (request_id = ? OR parent_request_id = ?)",
                array((int) $parent->request_id, (int) $parent->request_id))->row();
            $merged_total = $row ? (float) $row->t : null;
        }

        $message = 'Service payment request ' . $request->request_code . ' has been ' . strtolower($status_label) . '.';
        if ($action === 'approved') {
            $message .= ' Approved amount: Rs. ' . number_format($approved_amount, 2) . '.';
            if ($amount_revised) $message .= ' (Requested Rs. ' . number_format($requested_amount, 2) . '.)';
            if (!empty($parent) && $merged_total !== null) {
                $message .= ' Added to ' . $parent->request_code . ' - total now Rs. '
                          . number_format($merged_total, 2) . '.';
            }
        }
        if ($remarks !== '') $message .= ' Remarks: ' . $remarks;

        $notify = array((int) $request->created_by, (int) $request->requested_for_user_id, (int) $request->engineer_id);
        if ($action === 'approved') $notify[] = (int) $this->spr_accounts_user_id;
        foreach (array_unique(array_filter($notify)) as $uid) {
            if ($uid === $actor_id) continue;
            $this->_spr_notify($uid, 'Service Payment Request ' . $status_label, $message,
                $request_id, (int) $request->visit_id);
        }

        $this->_spr_out(array(
            'status'          => true,
            'message'         => 'Request ' . strtolower($status_label) . ' successfully.',
            'approved_amount' => $action === 'approved' ? $approved_amount : null,
            'merged_total'    => $merged_total,
        ));
    } catch (Throwable $e) {
        $this->_spr_fail('Could not save the decision: ' . $e->getMessage());
    }
}


}
