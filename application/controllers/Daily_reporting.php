<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_reporting extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
	$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$config = array();  
        $config['protocol'] = 'smtp';  
        $config['smtp_host'] = 'smtp.googlemail.com';  
        $config['smtp_user'] = 'sundarindustrialsoftware@gmail.com';  
        $config['smtp_pass'] = 'SundarIndst@323';   
        $config['smtp_port'] = 465;  
        $config['smtp_auth'] = true;  
        $config['smtp_crypto'] = 'ssl';  
        $this->email->initialize($config);  
		$this->email->set_newline("\r\n");  
        $this->load->library('email', $config); 
		
		$ip = $_SERVER["REMOTE_ADDR"];
		
	}
	
	public function index(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('employee_name', 'employee_name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$department_id =$this->session->userdata['logged_in']['department_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    $this->load->view('Daily_report/index');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           
		    $data=
			array('reporting_date'=>date('Y-m-d'),
			'employee_id'=>$user_id,
			'department_id'=>$department_id,
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$res = $this->db->insert('staff_daily_reporting',$data);
			$id = $this->db->insert_id();
			$table = "staff_daily_work_list";
			if(isset($_REQUEST['description'])){

			/*Check if pending task*/
			$recordid = $_REQUEST['recordid'];
			if(isset($recordid)){
				$tags=count($_REQUEST['description']);
				if($tags>0){
					$description=$_REQUEST['description'];
					$remarks = $_REQUEST['remarks'];
					$status=$_REQUEST['status'];
					for($x=0;$x<$tags;$x++){
					if($description[$x]!='')
						{
						    $data=array('description'=>$description[$x],
							'report_id'=>$id,
							'remarks'=>$remarks[$x],
							'work_status'=>$status[$x],
							'old_task'=>'1',
							'added_by'=>$user_id);
							$this->db->where('id',$recordid[$x]);
							$this->db->update($table,$data);
					}
					}
					
				}
			}
			/*Check if pending task*/


				
					$tags1=count($_REQUEST['description']);
					if($tags1>0)
					{
					$description=$_REQUEST['description'];
					$remarks = $_REQUEST['remarks'];
					$status=$_REQUEST['status'];
					$helpticket = $_REQUEST['helpticket'];
					for($x=0;$x<$tags1;$x++){
					if($description[$x]!='')
						{
						    
							$data=array('description'=>$description[$x],
							'report_id'=>$id,
							'remarks'=>$remarks[$x],
							'helpticket'=>$helpticket[$x],
							'work_status'=>$status[$x],
							'old_task'=>'0',
							'added_by'=>$user_id);
							$this->db->insert($table,$data);
							
							if($helpticket[$x]=='1'){
								
							$q = $this->db->select('first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
							foreach($q->result() as $userdata);

							$dt = array('yourname'=>$userdata->first_name." ".$userdata->last_name,
							'descriptionofissue'=>$description[$x],
							'remarks'=>$remarks[$x],
							'uploadimageorvideo(ifany)'=>'');
							$response = json_encode($dt);
							$data = array('form_id'=>'12',
							'responseid'=>'HTGMS',
							'formdata'=>$response,
							'work_status'=>'0',
							'added_by'=>$user_id, 
							'added_on'=>$added_time);

							$this->db->insert('dynamic_form_data',$data);	
								
							}
						}
					}
					}
					}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Daily_reporting');
			
		}
		
		
				
	}
	
	
	
	
	public function dashboard(){
		$this->load->view('Daily_report/dashboard');
	}
	public function daily_reporting_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		$query = $this->db->select('a.workstatus, a.feedback_by_sir,a.reporting_date, b.first_name, b.last_name, a.id')->from('staff_daily_reporting a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.employee_id',$user_id)->order_by('a.reporting_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$html = "<table border='1' style='width:900px;'><tr><th style='padding:2px 2px 2px 2px; text-align:center; width:5% '>Sr No</th><th style='padding:2px 2px 2px 2px; text-align:center; width:70%'>Description</th><th style='padding:2px 2px 2px 2px; text-align:center; width:30%'>Remarks</th> <th style='padding:2px 2px 2px 2px; text-align:center; width:5%'>Work Status</th></tr>";
			$k=1;
			$q = $this->db->select('description, remarks, helpticket, work_status')->from('staff_daily_work_list')->where('report_id',$row->id)->where('old_task','0')->order_by('work_status','asc')->get();
			foreach($q->result() as $record){
				$helpticket = $record->helpticket;
				
				if($record->work_status=='1'){
					$workstatus = "Done";
					$backgroundcolor = "";
				}else{
					$workstatus="Pending";
					$backgroundcolor = "background-color:#F5FC0D";
					
				}
				
				$html.="<tr style='".$backgroundcolor."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$k."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$record->description."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$record->remarks."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$workstatus."</td>";
				
				$html.="</tr>";
				$k++;
			}
		
			
			$html.="</table>";
			
			$scheduler_data[] = array('sr_no'=>$i,
			'employee_name'=>$row->first_name." ".$row->last_name,
			'date'=>date('d-m-Y',strtotime($row->reporting_date)),
			'work_descriptions'=>$html,
			'remarks_by_sir'=>$row->workstatus."<br><br>".$row->feedback_by_sir);
			$i++;
		}
		//echo "<pre>"; print_r($scheduler_data); exit;
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function reporting_dashboard(){

	$url="http://crm.gamavis.com/Mitr_api/daily_reporting/";
$ch = curl_init();
$post = array('flag'=>'1');
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post
));
//Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
$data['dailyreport'] = json_decode($response,true);
//echo "<pre>"; print_r($data); exit;
$err = curl_error($ch);

curl_close($ch);


	$this->load->view('Daily_report/master_dashboard',$data);
}

public function yesterday_dashboard(){
	
	$url="http://crm.gamavis.com/Mitr_api/yesterday_daily_reporting/";
$ch = curl_init();
$post = array('flag'=>'1');
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post
));
//Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
$data['dailyreport'] = json_decode($response,true);
//echo "<pre>"; print_r($data); exit;
$err = curl_error($ch);

curl_close($ch);


	$this->load->view('Daily_report/yesterday_dashboard',$data);
}

public function update_remarks(){
	$status = $this->input->post('approval_status');
	$data = array('workstatus'=>$status,
	'feedback_by_sir'=>$this->input->post('remarks'));
	
	$this->db->where('id',$this->uri->segment(3));
	$this->db->update('staff_daily_reporting',$data);
	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="class="alert alert-info">Thank you, Your record successfully added.</span></div><br/>');
	redirect(page_url.'Daily_reporting/reporting_dashboard');
}

public function update_yesterday_remarks(){
	$status = $this->input->post('approval_status');
	$data = array('workstatus'=>$status,
	'feedback_by_sir'=>$this->input->post('remarks'));
	
	$this->db->where('id',$this->uri->segment(3));
	$this->db->update('staff_daily_reporting',$data);
	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="class="alert alert-info">Thank you, Your record successfully added.</span></div><br/>');
	redirect(page_url.'Daily_reporting/yesterday_dashboard');
}

public function filter_reporting_dashboard(){
	$userid = $this->input->post('user_name');
	$date = date('Y-m-d',strtotime($this->input->post('date')));
	$enddate = date('Y-m-d',strtotime($this->input->post('enddate')));
	$data = array('username'=>$userid,
	'date'=>$date,
	'enddate'=>$enddate);
	if($userid){
		$this->load->view('Daily_report/daily_reporting_by_user',$data);
	}else{
		$this->load->view('Daily_report/datewise_master_dashboard',$data);
	}
	
}

}