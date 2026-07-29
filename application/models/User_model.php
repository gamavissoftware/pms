<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
	}

	public function authentication($username,$password)
	{
		$this->db->select('user_id,first_name,last_name,email,password,business_location,department_id,user_role_id,user_status, profile_image, employeecode, smtp_email, smtp_password, dynachem_id')->where('contact_number',$username)->where('password',$password)->where('user_status','1')->where('hide_profile','0');
		 $res = $this->db->get('system_users');
		 $res1 = $res->result();
		 return $res1;
		
	}
	
	public function recover_pass($email_ID)
	{
		$this->db->select('*');
		$this->db->from('lms_users');
		$this->db->where('email',$email_ID);
		$query = $this->db->get();
		return $query->result();
		
		
	}
	
	
	public function authenticationforwallmart($username,$password)
	{
		$this->db->select('id,username,password')->where('username',$username)->where('password',$password)->where('status','1');
		 $res = $this->db->get('wallmartlogin');
		 $res1 = $res->result();
		 return $res1;
		
	}
	
	
public function send_notification(){
    $q = $this->db->select('a.id,a.task_name,a.assign_to, a.billing_date, a.reminder_date, b.first_name, b.last_name, b.email, b.contact_number, b.department_id')->from('reminder_tasks a')->join('system_users b','a.assign_to=b.user_id','left')->where('a.reminder_date',date('Y-m-d'))->where('todays_status','0')->get();
        if($q->num_rows()){
        $message = "Dear Sir/ Madam <br> Please find your assigned task reminder.<br>";
        
        $message.='<table cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px"> 
        <thead>    
        <tr>      
        <th bgcolor="#900C3F" style="color:#fff; width:5%">SR NO.</th> 
        <th bgcolor="#900C3F" style="color:#fff; width:10%">TASK NAME</th>
        <th bgcolor="#900C3F" style="color:#fff; width:10%">DUE DATE</th>
        <th bgcolor="#900C3F" style="color:#fff; width:10%">REMINDER DATE</th>
        </tr> 
        </thead>
        <tbody>';
        $i=1;
        foreach($q->result() as $row){
            $message.='<tr align="left">
   <td scope="row" style="text-align:center;">'.$i.'</td>
      <td style="text-align:center">'.strtoupper($row->task_name).'</td>
      <td style="text-align:center">'.strtoupper(date('d-M-Y', strtotime($row->billing_date))).'</td>
      <td style="text-align:center">'.strtoupper(date('d-M-Y', strtotime($row->reminder_date))).'</td>
     
    </tr>';
           $data = array('todays_status'=>'1');
           $this->db->where('id',$row->id);
           $this->db->update('reminder_tasks',$data);
           
            
                    $username= $row->first_name." ".$row->last_name;
                    $subjectname = "TASK REMINDER ";
					$this->email->set_mailtype("html");
					$this->email->to($row->email);
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($message);
    				$result11=$this->email->send();
    			    $c = $row->contact_number;	
    				
    			$ms = "Assigned Task Reminder.\n";
$customerdetail = "Task Name - ".ucfirst($row->task_name)."\nBilling Date- ".strtoupper(date('d-M-Y', strtotime($row->billing_date)))."\nReminder Date- ".strtoupper(date('d-M-Y', strtotime($row->reminder_date)));

$smsmessage = "Hello ".$username."\n".$ms."\n".$customerdetail."\n";
//echo $smsmessage; exit;
/**** SMS INTEGRATION***/

	$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $c,
    'message' => $smsmessage,
    'sender' => 'PRESTO',
    'route' => '4'
);


$url="http://api.msg91.com/api/sendhttp.php";
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData
));



curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch); 

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  //echo $response;
} 
	
/**** SMS INTEGRATION***/

/** TASK DELEGATE TO THE PERSON **/

$user_id ='66';
date_default_timezone_set("Asia/Kolkata");
$added_time = date('Y-m-d H:i:s');
$query = $this->db->select('id')->from('delegation_task')->get();
$res = count($query->result());
if($res<=0){
$caseno = "PD-1";
}else{
$caseno= "PD-".$res;
}
$data = array('yourname'=>$user_id,
'department_id'=>$row->department_id,
'delegate_to'=>$row->assign_to,
'task'=>$row->task_name,
'delegated_date'=>$row->billing_date,
'targetdate'=>$row->billing_date,
'added_on'=>$added_time,
'added_by'=>$user_id,
'case_no'=>$caseno
);

$this->db->insert('delegation_task',$data);

/** TASK DELEGATE TO THE PERSON **/


            
        $i++;    
            
        }
        
      
        
    }
    
    
}

public function reminder_on_time_period(){
    $q = $this->db->select('a.id,a.task_name,a.assign_to, a.billing_date, a.reminder_date, b.first_name, b.last_name, b.email, b.contact_number, b.department_id')->from('reminder_tasks a')->join('system_users b','a.assign_to=b.user_id','left')->where('a.reminder_date1',date('Y-m-d'))->or_where('a.reminder_date2',date('Y-m-d'))->or_where('a.reminder_3',date('Y-m-d'))->where('todays_status','0')->get();
        if($q->num_rows()){
        $message = "Dear Sir/ Madam <br> Please find your assigned task reminder.<br>";
        
        $message.='<table cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px"> 
        <thead>    
        <tr>      
        <th bgcolor="#900C3F" style="color:#fff; width:5%">SR NO.</th> 
        <th bgcolor="#900C3F" style="color:#fff; width:10%">TASK NAME</th>
        <th bgcolor="#900C3F" style="color:#fff; width:10%">DUE DATE</th>
        <th bgcolor="#900C3F" style="color:#fff; width:10%">REMINDER DATE</th>
        </tr> 
        </thead>
        <tbody>';
        $i=1;
        foreach($q->result() as $row){
$message.='<tr align="left">
<td scope="row" style="text-align:center;">'.$i.'</td>
<td style="text-align:center">'.strtoupper($row->task_name).'</td>
<td style="text-align:center">'.strtoupper(date('d-M-Y', strtotime($row->billing_date))).'</td>
<td style="text-align:center">'.strtoupper(date('d-M-Y', strtotime($row->reminder_date))).'</td>

</tr>';
$data = array('todays_status'=>'1');
$this->db->where('id',$row->id);
$this->db->update('reminder_tasks',$data);
$username= $row->first_name." ".$row->last_name;
$subjectname = "TASK REMINDER ";
$this->email->set_mailtype("html");
$this->email->to($row->email);
$this->email->from('mitr@prestomitr.com');
$this->email->subject($subjectname);
$this->email->message($message);
$result11=$this->email->send();
$c = $row->contact_number;	
    				
$ms = "Assigned Task Reminder.\n";
$customerdetail = "Task Name - ".ucfirst($row->task_name)."\nBilling Date- ".strtoupper(date('d-M-Y', strtotime($row->billing_date)))."\nReminder Date- ".strtoupper(date('d-M-Y', strtotime($row->reminder_date)));

$smsmessage = "Hello ".$username."\n".$ms."\n".$customerdetail."\n";
//echo $smsmessage; exit;
/**** SMS INTEGRATION***/

	$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $c,
    'message' => $smsmessage,
    'sender' => 'PRESTO',
    'route' => '4'
);


$url="http://api.msg91.com/api/sendhttp.php";
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData
));



curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch); 

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  //echo $response;
} 
	
/**** SMS INTEGRATION***/


        $i++;    
            
        }
        
      
        
    }
    
    
}

public function get_monthly_sales() {
    $data = array();

    $this->db->select('month, month_date, order_value');
    $this->db->from('monthly_sale_stats');
    $this->db->order_by('month_date', 'desc');
    $this->db->limit(10);
    $query = $this->db->get();

    foreach($query->result() as $row){
        $data[] = array(
            'month' => $row->month,
            'month_date' => $row->month_date,
            'order_value' => $row->order_value
        );
    }

    // Reverse to get ascending order (oldest to newest)
    $data = array_reverse($data);

    return $data;
}



    public function checkorderdomesticorintl() {
        $this->db->select('order_type, COUNT(*) as count');
        $this->db->group_by('order_type');
        $query = $this->db->get('df_release');

        $result = $query->result();
        $total = array_sum(array_column($result, 'count'));

        foreach ($result as &$row) {
            $row->percentage = ($row->count / $total) * 100;
        }

        return $result;
    }

     public function get_sales_data_for_current_month() {
    $this->db->select('b.title, b.first_name, b.last_name, SUM(a.order_value) as total_sales');
    $this->db->from('poreceived a');
    $this->db->join('system_users b', 'b.user_id = a.added_by');
    // Uncomment the following lines if you want to filter by the current month and year
    // $this->db->where('MONTH(a.added_on)', date('m'));
    // $this->db->where('YEAR(a.added_on)', date('Y'));
    $this->db->group_by('a.added_by');
    $this->db->order_by('total_sales', 'DESC'); // Order by total_sales in descending order
    
    $query = $this->db->get();
    return $query->result();
}


    public function get_order_values_and_counts_by_brand() {
        $this->db->select('company_brand.name, SUM(poreceived.order_value) as total_order_value, COUNT(poreceived.id) as order_count');
        $this->db->from('poreceived');
        $this->db->join('company_brand', 'poreceived.brand_tag = company_brand.id', 'left');
        //$this->db->where('YEAR(poreceived.added_on)', date('Y'));
        $this->db->group_by('company_brand.name');
        $this->db->order_by('total_order_value', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();

        return $query->result();
    }

    private function get_order_dashboard_date_expression($alias = 'poreceived')
    {
        return "COALESCE(NULLIF(DATE(" . $alias . ".podate), '0000-00-00'), NULLIF(DATE(" . $alias . ".added_on), '0000-00-00'))";
    }

    private function build_financial_year_meta($financial_year_value = '')
    {
        if (!preg_match('/^(\d{4})-(\d{4})$/', (string) $financial_year_value, $matches)) {
            $current_year = (int) date('Y');
            $current_month = (int) date('n');
            $start_year = $current_month >= 4 ? $current_year : $current_year - 1;
            $end_year = $start_year + 1;
        } else {
            $start_year = (int) $matches[1];
            $end_year = (int) $matches[2];

            if ($end_year !== ($start_year + 1)) {
                $current_year = (int) date('Y');
                $current_month = (int) date('n');
                $start_year = $current_month >= 4 ? $current_year : $current_year - 1;
                $end_year = $start_year + 1;
            }
        }

        return array(
            'value' => $start_year . '-' . $end_year,
            'label' => 'FY ' . $start_year . '-' . substr((string) $end_year, -2),
            'short_label' => $start_year . '-' . substr((string) $end_year, -2),
            'start_year' => $start_year,
            'end_year' => $end_year,
            'start_date' => $start_year . '-04-01',
            'end_date' => $end_year . '-03-31',
        );
    }

    public function get_available_financial_years()
    {
        $date_expression = $this->get_order_dashboard_date_expression('poreceived');
        $query = $this->db->query(
            "SELECT MIN(" . $date_expression . ") as min_order_date, MAX(" . $date_expression . ") as max_order_date
             FROM poreceived
             WHERE " . $date_expression . " IS NOT NULL"
        );
        $row = $query->row();

        $financial_years = array();

        if (empty($row) || empty($row->max_order_date)) {
            $financial_years[] = $this->build_financial_year_meta('');
            return $financial_years;
        }

        $min_timestamp = strtotime($row->min_order_date);
        $max_timestamp = strtotime($row->max_order_date);

        if ($min_timestamp === false || $max_timestamp === false) {
            $financial_years[] = $this->build_financial_year_meta('');
            return $financial_years;
        }

        $start_financial_year = ((int) date('n', $min_timestamp) >= 4) ? (int) date('Y', $min_timestamp) : ((int) date('Y', $min_timestamp) - 1);
        $end_financial_year = ((int) date('n', $max_timestamp) >= 4) ? (int) date('Y', $max_timestamp) : ((int) date('Y', $max_timestamp) - 1);

        for ($financial_year = $end_financial_year; $financial_year >= $start_financial_year; $financial_year--) {
            $financial_years[] = $this->build_financial_year_meta($financial_year . '-' . ($financial_year + 1));
        }

        if (empty($financial_years)) {
            $financial_years[] = $this->build_financial_year_meta('');
        }

        return $financial_years;
    }

    public function get_financial_year_details($financial_year_value = '')
    {
        return $this->build_financial_year_meta($financial_year_value);
    }

    public function get_sales_data_by_financial_year($financial_year_value = '')
    {
        $financial_year = $this->build_financial_year_meta($financial_year_value);
        $date_expression = $this->get_order_dashboard_date_expression('a');
        $sql = "SELECT
                    a.added_by as agent_id,
                    COALESCE(NULLIF(CONCAT_WS(' ', b.title, b.first_name, b.last_name), ''), 'Unassigned') as agent_name,
                    SUM(COALESCE(a.order_value, 0)) as total_sales,
                    COUNT(a.id) as order_count,
                    AVG(COALESCE(a.order_value, 0)) as avg_order_value
                FROM poreceived a
                LEFT JOIN system_users b ON b.user_id = a.added_by
                WHERE " . $date_expression . " >= ?
                  AND " . $date_expression . " <= ?
                GROUP BY a.added_by, b.title, b.first_name, b.last_name
                ORDER BY total_sales DESC, agent_name ASC";

        return $this->db->query($sql, array($financial_year['start_date'], $financial_year['end_date']))->result();
    }

    public function get_order_values_and_counts_by_brand_for_financial_year($financial_year_value = '')
    {
        $financial_year = $this->build_financial_year_meta($financial_year_value);
        $date_expression = $this->get_order_dashboard_date_expression('a');
        $sql = "SELECT
                    a.brand_tag as brand_id,
                    COALESCE(NULLIF(company_brand.name, ''), 'Unmapped Brand') as name,
                    SUM(COALESCE(a.order_value, 0)) as total_order_value,
                    COUNT(a.id) as order_count,
                    AVG(COALESCE(a.order_value, 0)) as avg_order_value
                FROM poreceived a
                LEFT JOIN company_brand ON a.brand_tag = company_brand.id
                WHERE " . $date_expression . " >= ?
                  AND " . $date_expression . " <= ?
                GROUP BY a.brand_tag, company_brand.name
                ORDER BY total_order_value DESC, name ASC
                LIMIT 5";

        return $this->db->query($sql, array($financial_year['start_date'], $financial_year['end_date']))->result();
    }

    public function get_financial_year_order_summary($financial_year_value = '')
    {
        $financial_year = $this->build_financial_year_meta($financial_year_value);
        $date_expression = $this->get_order_dashboard_date_expression('a');
        $sql = "SELECT
                    COUNT(a.id) as total_orders,
                    SUM(COALESCE(a.order_value, 0)) as total_order_value,
                    AVG(COALESCE(a.order_value, 0)) as avg_order_value,
                    COUNT(DISTINCT CASE WHEN a.added_by IS NOT NULL AND a.added_by <> 0 THEN a.added_by END) as active_agents,
                    COUNT(DISTINCT CASE WHEN a.brand_tag IS NOT NULL AND a.brand_tag <> 0 THEN a.brand_tag END) as active_brands,
                    MAX(" . $date_expression . ") as latest_order_date
                FROM poreceived a
                WHERE " . $date_expression . " >= ?
                  AND " . $date_expression . " <= ?";

        return $this->db->query($sql, array($financial_year['start_date'], $financial_year['end_date']))->row_array();
    }


public function get_ticket_by_id($ticket_id)
{
    $q = $this->db->select('a.help_ticket_no,a.remarks,a.added_on, a.ticket_closing_remarks, a.user_id, b.title, b.first_name, b.last_name, c.email, c.title as ptitle, c.first_name as fname, c.last_name as lname')->from('communication_ticket_system a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('id', $ticket_id)->get();
    $result = $q->result();
    return $result;
}

public function mark_ticket_closed($ticket_id, $remark)
{
     $user_id = $this->session->userdata['logged_in']['user_id'];
    $data = [
        'ticket_status' =>1,
        'ticket_closing_remarks' => $remark,
        'ticket_closed_by' =>$user_id ,
        'ticket_closed_on' => date('Y-m-d H:i:s')
    ];
    return $this->db->where('id', $ticket_id)->update('communication_ticket_system', $data);
}

public function gethodemail($userid){
    $q = $this->db->select('c.title, c.first_name, c.last_name, c.email')->from('presto_team_members a')->join('prestogroup_teams b','a.team_id=b.team_id','left')->join('system_users c','b.team_leader=c.user_id','left')->where('a.employee_id',$userid)->get();
    $result= $q->result();
    return $result;
}

public function getuserinfo($user_id){
    $q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
    return $q->result();

}




}
	
	
