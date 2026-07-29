<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_Api extends CI_Controller {


 public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: POST');

        $this->load->model('Task_model');
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
    

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_response(false, 'Invalid email format');
            return;
        }

        /* ===============================
         * 3. FETCH USER
         * =============================== */
        $this->db->select('user_id, first_name, last_name, email, password, user_role_id, department_id, user_status');
        $this->db->from('system_users');
        $this->db->where('email', $email);
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
                'department_id' => (int)$user['department_id']
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

        public function dashboard_counts()
        {
        header('Content-Type: application/json');

        // OPTIONAL: token validation later
        // $token = $this->input->get_request_header('Authorization');

        // TEMP: static data (replace with DB logic)
        $q = $this->db->select('id')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
        $running_df= $q->num_rows();

        $q = $this->db->select('id')->from('poreceived')->where('penalityamount!=', 0)->get();
        $penalty_df=$q->num_rows();
        $data = [
        'running_df' => $running_df,
        'delayed_df' => $this->df_delayed(),
        'penalty_df' => $penalty_df,
        ];

        echo json_encode([
        'status' => true,
        'data' => $data
        ]);
        }


function df_delayed()
{
$show = array();
$show[] = 0;
$m = 1;
$reportid = $this->uri->segment(3);
$q = $this->db->select('id, df_no, added_on, df_upload, added_on')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
if ($q->num_rows() > 0) {
foreach ($q->result() as $rows) {

$q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
if ($q1->num_rows() > 0) {
foreach ($q1->result() as $r);
$planneddate = date('d-m-Y', strtotime($r->enddate));
} else {
$planneddate = '';
}


$q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
$count = $q1->num_rows();
$delaycount = array();
$delaycount[] = 0;
$totaldayscountarray = array();
$totaldayscountarray[] = 0;
$q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->where('task_status', 1)->get();
$totaldone = $q2->num_rows();
// $percetage =  round($totaldone * 100 / $count);

if ($count > 0) {
$percentage = round($totaldone * 100 / $count);
} else {
$percentage = 0; 
}


foreach ($q2->result() as $rowss) {
if (date('Y-m-d', strtotime($rowss->task_completed_on)) > $rowss->end_date) {
$delaycount[] = 1;

$daysss = $this->Task_model->getDays($rowss->end_date, date('Y-m-d', strtotime($rowss->task_completed_on)), 1);

$totaldayscountarray[] = $daysss;
}
}
$delayed = array_sum($delaycount);
$totaldaysdelayed = array_sum($totaldayscountarray);

if ($totaldone > 0) {
$totaldelayedpercentage = round($delayed * 100 / $totaldone);
} else {
$totaldelayedpercentage = 0; 
}

// $totaldelayedpercentage = round($delayed * 100 / $totaldone);

$expecteddate =  date('d-m-Y', strtotime($planneddate . ' +' . $totaldaysdelayed . ' Days'));
$skipped_dates = $this->Task_model->SKIPsingle_holidays($expecteddate);
if ($totaldelayedpercentage > 0) {
$show[] = 1;
} else {
$show[] = 0;
}
}
}

return array_sum($show);
}



public function get_running_df_list($from_date = null, $to_date = null)
    {
        $CIA =& get_instance();
        $CIA->load->model('Task_model');

        $this->db->select("
            df.id,
            df.df_no,
            df.added_on as df_release_date,
            df.df_upload,
            df.machine_id,

            po.podate,
            po.lead_id,
            po.df_number as po_df_number,

            CONCAT(u.title,' ',u.first_name,' ',u.last_name) as marketing_person,

            MIN(tasks.start_date) as df_start_date,
            MAX(tasks.end_date) as planned_closure,

            COUNT(tasks.id) as total_tasks,
            SUM(CASE WHEN tasks.task_status = 1 THEN 1 ELSE 0 END) as completed_tasks,

            (
                SELECT COUNT(id)
                FROM communication_ticket_system
                WHERE df_id = df.id AND ticket_status = 0
            ) as open_tickets,

            GROUP_CONCAT(
                CASE
                    WHEN tasks.task_status = 1
                    THEN CONCAT(tasks.task_completed_on,'|',tasks.end_date)
                END
            SEPARATOR ';') as completed_tasks_data,

            MAX(CASE WHEN tasks.task_status = 1 THEN tasks.task_completed_on END) as max_completion_date
        ", false);

        $this->db->from('df_release df');
        $this->db->join('poreceived po', 'po.df_id = df.id', 'left');
        $this->db->join('system_users u', 'po.added_by = u.user_id', 'left');
        $this->db->join('task_department_wise_scheduling tasks', 'tasks.df_id = df.id', 'left');

        $this->db->where('df.df_status', 0);
        $this->db->where('df.on_hold', 0);

        if ($from_date && $to_date) {
            $this->db->where('DATE(df.added_on) >=', $from_date);
            $this->db->where('DATE(df.added_on) <=', $to_date);
        }

        $this->db->group_by('df.id');

        $query = $this->db->get();
        $result = [];

        foreach ($query->result() as $row) {

            $display_df_no = $row->df_no ?: $row->po_df_number;

            $completion_percentage = ($row->total_tasks > 0)
                ? round($row->completed_tasks * 100 / $row->total_tasks)
                : 0;

            $delaycount = 0;
            $max_delay_days = 0;

            if (!empty($row->completed_tasks_data)) {
                foreach (explode(';', $row->completed_tasks_data) as $task) {
                    list($completed_on, $end_date) = explode('|', $task);
                    if ($completed_on > $end_date) {
                        $delaycount++;
                        $delay_days = $CIA->Task_model->getDays($end_date, $completed_on, 1);
                        $max_delay_days = max($max_delay_days, $delay_days);
                    }
                }
            }

            $delay_percentage = ($row->completed_tasks > 0)
                ? round($delaycount * 100 / $row->completed_tasks)
                : 0;

            $actual_completion = ($completion_percentage == 100 && $row->max_completion_date)
                ? $row->max_completion_date
                : $CIA->Task_model->SKIPsingle_holidays(
                    date('Y-m-d', strtotime($row->planned_closure . " +{$max_delay_days} days"))
                );

            $df_delay_days = $CIA->Task_model->getDays(
                $row->planned_closure,
                $actual_completion,
                1
            );

            $result[] = [
    "df_id" => $row->id,
    "df_no" => strtoupper($display_df_no),
    "marketing_person" => strtoupper($row->marketing_person),

    "po_date" => $this->fmt($row->podate),
    "df_release_date" => $this->fmt($row->df_release_date),
    "df_start_date" => $this->fmt($row->df_start_date),
    "planned_closure" => $this->fmt($row->planned_closure),
    "actual_completion" => $this->fmt($actual_completion),

    "df_delay_days" => (int)$df_delay_days,
    "completion_percentage" => (int)$completion_percentage,
    "delay_percentage" => (int)$delay_percentage,
    "open_tickets" => (int)$row->open_tickets,
];

        }

        return $result;
    }

    public function running_df_list()
{
    header('Content-Type: application/json');

    $from_date = $this->input->get('from_date');
    $to_date   = $this->input->get('to_date');

    $data = $this->get_running_df_list($from_date, $to_date);

    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
}


function fmt($date) {
    return ($date && $date != '0000-00-00')
        ? date('d-M-Y', strtotime($date))
        : '--';
}



}

