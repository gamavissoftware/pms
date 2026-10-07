<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		//echo "<pre>"; print_r($session); exit;
		if($session == FALSE)
		{
		redirect(page_url);
		}
		$this->load->model('User_model','user');
		$this->load->model('Dashboard_model','reportingdata');
		$this->load->model('Store_model','store');
		$this->load->model('Fms_model','Fms_model');
		$user_id =$this->session->userdata['logged_in']['user_id'];
		if(empty($user_id))
		     {
		     redirect(site_url(),'refresh');
		     }
		}

		function newdashboardreport(){
		$delegatedtask = $this->reportingdata->delegated_task();
		$poapproval = $this->reportingdata->po_approval();
		$helptickets = $this->reportingdata->helptickets();
		$escalated_ticket = $this->reportingdata->escalated_ticket();
		$account_helpticket = $this->reportingdata->account_helpticket();
		$service_helpticket = $this->reportingdata->service_helpticket();
		$it_helpticket = $this->reportingdata->it_helpticket();
		$sales_helpticket = $this->reportingdata->sales_helpticket();
		$production_helpticket = $this->reportingdata->production_helpticket();
		$dispatch_helpticket = $this->reportingdata->dispatch_helpticket();
		$ea_gm = $this->reportingdata->ea_gm();
		$purchase_helpticket = $this->reportingdata->purchase_helpticket();
		$hr_helpticket = $this->reportingdata->hr_helpticket();
		$unclaimed_payment = $this->reportingdata->unclaimed_payment();
		$payment_history = $this->reportingdata->payment_history();
		$leave_application = $this->reportingdata->leave_application();
		$on_leave_today = $this->reportingdata->on_leave_today();
		/*Total Order*/
		$userinfo = "0";
		$lcountall=$this->Fms_model->nondispatchedorders($userinfo);
		$data = array('report_count'=>$lcountall);
		$this->db->where('id','26');
		$this->db->update('administrator_dashboard',$data);
		/*Total Order*/
		//$this->load->view('dashboard/newdashboardreport');
	}



function dashboard1(){
$pendicount=$this->Fms_model->pendingordercount();
$this->load->view('dashboard/newdashboardreport');
	}
	public function index()
	{
		$data['sales_data'] = $this->user->get_monthly_sales();
		$data['order_data'] = $this->user->checkorderdomesticorintl();

		$data['sales_data1'] = $this->user->get_sales_data_for_current_month();
		$data['order_data_by_brand'] = $this->user->get_order_values_and_counts_by_brand();
		if ($this->is_administrator_income_user()) {
			$data = array_merge($data, $this->get_administrator_income_dashboard_data());
		}
        

		$user_id =$this->session->userdata['logged_in']['user_id'];
		$this->reportingdata->delegated_task($user_id);
		$this->reportingdata->ea_delegated_task($user_id);
	    $department_id =$this->session->userdata['logged_in']['department_id'];
	    $role_id =$this->session->userdata['logged_in']['role'];
	    //$role_id =$this->session->userdata['logged_in']['role'];
	    $businesslocation =$this->session->userdata['logged_in']['business_location'];

	    $this->load->helper('df_delay');
	    $today = date('Y-m-d');
	    $departmentDelaySql = df_department_overdue_sql($this->db, 't', $today);
	    $query = "
        SELECT 
            d.department, df.df_no, t.department_id,
            COUNT(t.id) AS total_tasks,
            MAX(
                CASE 
                    WHEN " . $departmentDelaySql . " THEN DATEDIFF(CURDATE(), t.end_date)
                    ELSE 0
                END
            ) AS max_delay_days
        FROM task_department_wise_scheduling t
        JOIN df_release df ON t.df_id = df.id
        JOIN departments d ON t.department_id = d.department_id
        WHERE df.df_status = 0
          AND IFNULL(df.on_hold, 0) = 0
          AND " . $departmentDelaySql . "
        GROUP BY t.department_id
        ORDER BY max_delay_days DESC;
    ";

	    $data['department_delay_report'] = $this->db->query($query)->result_array();
	    $data['active_dfs_delay_report'] = $this->reportingdata->get_active_dfs();


    /*Show Task Assign Notification to User*/

    //$data['notification'] = $this->reportingdata->get_unshown_notification($user_id);



	    if($businesslocation==1){
	    	$this->load->view('dashboard/dashboard');  
	    }else{
			//$this->load->view('dashboard/subhampack_dashboard',$data); 
			$this->load->view('dashboard/newdesigndashboard',$data); 
	    }
		

	}


	public function newdesignofdashboard()
	{
		$data['sales_data'] = $this->user->get_monthly_sales();
		$data['order_data'] = $this->user->checkorderdomesticorintl();
		if ($this->is_administrator_income_user()) {
			$data = array_merge($data, $this->get_administrator_income_dashboard_data());
		}

		$user_id =$this->session->userdata['logged_in']['user_id'];
		$this->reportingdata->delegated_task($user_id);
		$this->reportingdata->ea_delegated_task($user_id);
	    $department_id =$this->session->userdata['logged_in']['department_id'];
	    $role_id =$this->session->userdata['logged_in']['role'];
	    $role_id =$this->session->userdata['logged_in']['role'];
	    $businesslocation =$this->session->userdata['logged_in']['business_location'];
	    $data['active_dfs_delay_report'] = $this->reportingdata->get_active_dfs();
	   $this->load->view('dashboard/newdesigndashboard',$data); 
		

	}

	private function get_administrator_income_dashboard_data()
	{
		$financial_year_options = $this->user->get_available_financial_years();
		$available_values = array();
		foreach ($financial_year_options as $option) {
			$available_values[] = $option['value'];
		}

		$selected_financial_year = trim((string) $this->input->get('income_financial_year', true));
		if ($selected_financial_year === '' || !in_array($selected_financial_year, $available_values, true)) {
			$selected_financial_year = !empty($financial_year_options)
				? $financial_year_options[0]['value']
				: $this->user->get_financial_year_details('')['value'];
		}

		$allowed_departments = array('all', 'marketing', 'spares', 'service');
		$selected_department = strtolower(trim((string) $this->input->get('income_department', true)));
		if (!in_array($selected_department, $allowed_departments, true)) {
			$selected_department = 'all';
		}

		$marketing = $this->user->get_financial_year_order_summary($selected_financial_year);
		$marketing_df = $this->user->get_marketing_df_income_breakdown_by_financial_year($selected_financial_year);
		$spares = $this->user->get_spares_order_summary_by_financial_year($selected_financial_year);
		$spares_breakdown = $this->user->get_spares_income_breakdown_by_financial_year($selected_financial_year);
		$service_rows = $this->user->get_service_order_summary_by_financial_year($selected_financial_year);
		$service_breakdown = $this->user->get_service_income_breakdown_by_financial_year($selected_financial_year);
		$service = array();
		foreach ($service_rows as $row) {
			$currency = strtoupper(trim((string) $row['currency']));
			$service[$currency !== '' ? $currency : 'INR'] = $row;
		}

		$marketing_value = !empty($marketing_df['running_total_value']) ? (float) $marketing_df['running_total_value'] : 0;
		$spares_value = !empty($spares_breakdown['domestic_value']) ? (float) $spares_breakdown['domestic_value'] : 0;
		$service_inr_value = !empty($service['INR']['total_order_value']) ? (float) $service['INR']['total_order_value'] : 0;

		return array(
			'admin_income_financial_year_options' => $financial_year_options,
			'admin_income_financial_year' => $selected_financial_year,
			'admin_income_financial_year_details' => $this->user->get_financial_year_details($selected_financial_year),
			'admin_income_department' => $selected_department,
			'admin_income_marketing' => $marketing,
			'admin_income_marketing_df' => $marketing_df,
			'admin_income_spares' => $spares,
			'admin_income_spares_breakdown' => $spares_breakdown,
			'admin_income_service' => $service,
			'admin_income_service_breakdown' => $service_breakdown,
			'admin_income_total_inr' => $marketing_value + $spares_value + $service_inr_value,
		);
	}

	private function is_administrator_income_user()
	{
		$role_id = (int) $this->session->userdata['logged_in']['role'];
		if ($role_id === 1) {
			return true;
		}

		$administrator_roles = array_map('intval', $this->reportingdata->getsuperadminuserole());
		return in_array($role_id, $administrator_roles, true);
	}
	
		 public function get_active_dfs() {
	        $dfs = $this->reportingdata->get_active_dfs();
	        $this->output
	            ->set_content_type('application/json')
	            ->set_output(json_encode(array('data' => $dfs)));
	    }

	    public function get_tasks($df_id, $departmentid) {
	        $tasks = $this->reportingdata->get_tasks_by_df($df_id, $departmentid);
	        $this->output
	            ->set_content_type('application/json')
	            ->set_output(json_encode($tasks));
	    }

	     public function get_tasks_dfwise($df_id) {
	        $tasks = $this->reportingdata->get_tasks_only_df_wise($df_id);
	        $this->output
	            ->set_content_type('application/json')
	            ->set_output(json_encode($tasks));
	    }


		
	public function add_Quote()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('quote_of_the_day', 'quote_of_the_day', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/dashboard');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "quote_of_the_day";		
			$data = array('quote'=>strtoupper($this->input->post('quote_of_the_day')),
			'quote_date'=>date('Y-m-d'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');
			redirect(page_url.'Dashboard');
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Dashboard');
		}
	}
	}
	public function add_news_events()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('event_date', 'event_date', 'required|trim');
		$this->form_validation->set_rules('news_events', 'news_events', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/dashboard');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_news_events";		
		$photo=$_FILES['event_file']['name'];
		if($photo<>'')
		{
		$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["event_file"]["tmp_name"],UPLOADPATH.'eventimg/' . $screenshot);
			}else
			{
				$screenshot="";
				}	
			$data = array('news_events'=>strtoupper($this->input->post('news_events')),
			'event_date'=>date('Y-m-d',strtotime($this->input->post('event_date'))),
			'image'=>$screenshot,
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->db->insert($table,$data);	
		
if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');
			redirect(page_url.'Dashboard');
	}else
	{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Dashboard');
		}
	}
	}
public function view_all_events(){
	   $this->load->view('dashboard/events_list'); 

	}
	public function view_all_events_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$body = array();
		$query = $this->db->select('id,event_date, news_events, image')->from('presto_news_events')->order_by('id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$edit = "<a href='".page_url."Dashboard/edit_events/".$row->id."'><i class='fa fa-pencil'></i></a>";
				$delete = "<a href='".page_url."Dashboard/delete_events/".$row->id."'><i class='fa fa-trash' title='remove'></i></a>";
			$img = "<img src='".eventimgpath.$row->image."' width='100px'>";
			$body[] = array('sr_no'=>$i,
			'date'=>date('d-m-Y',strtotime($row->event_date)),
			'event_name'=>$row->news_events,
			'image'=>$img,
			'edit'=>$edit,
			'remove'=>$delete);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($body),
	"iTotalDisplayRecords" => count($body),
	"aaData"=>$body);
	echo json_encode($results);
}
function delete_events(){
    $id = $this->uri->segment(3);
    $this->db->where('id',$id);
    $this->db->delete('presto_news_events');
    	$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully removed.</div>');
	redirect(page_url.'Dashboard/view_all_events');
}

	public function edit_events(){
	    $this->load->view('dashboard/edit_events');
	}
	public function update_news_events()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('event_date', 'event_date', 'required|trim');
		$this->form_validation->set_rules('news_events', 'news_events', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/edit_events');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_news_events";		
        $photo=$_FILES['event_file']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["event_file"]["tmp_name"],UPLOADPATH.'eventimg/' . $screenshot);
			}else
			{
			$screenshot=$this->input->post('old_image');
				}	
			$data = array('news_events'=>strtoupper($this->input->post('news_events')),
			'event_date'=>date('Y-m-d',strtotime($this->input->post('event_date'))),
			'image'=>$screenshot,
			'added_on'=>$date,
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');
			redirect(page_url.'Dashboard');
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Dashboard');

		}

	}
	}
	public function body_template(){
	$this->load->view('dashboard/mail_template');

}
public function sms_template_list()

	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$body = array();
		$this->db->select('*')->from('email_sms_whatsapp_template');
		$query = $this->db->order_by('sms_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			if($row->sms_id=='1'){
				$smsfor = "TEXT SMS TEMPLATE";
			}else if($row->sms_id=='2'){
				$smsfor = "EMAIL TEMPLATE";
			}else if($row->sms_id=='3'){
		$smsfor = "WHATSAPP TEMPLATE";
			}else{
				$smsfor="";
			}
			date_default_timezone_set("Asia/Kolkata");
			$edit = "<a href='".page_url."Dashboard/edit_template/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$body[] = array('sr_no'=>$i,
			'sms_type'=>$smsfor,
			'sms_body'=>$row->first_field." ".$row->second_field." ".$row->third_field." ".$row->fourth_field." ".$row->fifth_field." ".$row->sixth_field." ".$row->seventh_field." ".$row->eighth_field." ".$row->ninth_field,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($body),
	"iTotalDisplayRecords" => count($body),
	"aaData"=>$body);
	echo json_encode($results);
}
function edit_template(){
	$this->load->view('dashboard/edit_sms_template');
}	
public function update_body_sms()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('first_field', 'first_field', 'required|trim');
		$this->form_validation->set_rules('third_field', 'third_field', 'required|trim');
		$this->form_validation->set_rules('sixth_field', 'sixth_field', 'required|trim');
		$this->form_validation->set_rules('eighth_field', 'eighth_field', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/edit_sms_template');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "email_sms_whatsapp_template";		
			$data = array('body_template'=>$this->input->post('body_message'),
		'first_field'=>$this->input->post('first_field'),
	'third_field'=>$this->input->post('third_field'),
			'sixth_field'=>$this->input->post('sixth_field'),
			'eighth_field'=>$this->input->post('eighth_field'),
			'added_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully updated.</div>');
			redirect(page_url.'Dashboard/body_template');
			}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Dashboard/body_template');
}
}

}
	public function remove_picture(){
$date = date('Y-m-d', strtotime('-7 days'));
	    $query = $this->db->select('evening_selfie,morning_selfie,id')->from('mark_your_attendance')->where('attendance_date<',$date)->get();
	    foreach($query->result() as $row){
	        if($row->morning_selfie!==''){
	            $path = $_SERVER['DOCUMENT_ROOT']."/image_bank/attendance/".$row->morning_selfie;
	            unlink($path);
	        }
	       if($row->evening_selfie!==''){
	          $path1 = $_SERVER['DOCUMENT_ROOT']."/image_bank/attendance/".$row->evening_selfie; 
	          unlink($path1);
	       }
	       $data = array('morning_selfie'=>'',
	       'evening_selfie'=>'');
	       $this->db->where('id',$row->id);
	       $this->db->update('mark_your_attendance',$data);
	    }
	}
		public function add_new_images()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "dashboard_image";		
        $photo=$_FILES['image']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$screenshot=time().'.'.$cat_image;
				move_uploaded_file($_FILES["image"]["tmp_name"],UPLOADPATH.'dashboardimg/' . $screenshot);
			}else
			{
				$screenshot="";
				}	
			$data = array('image'=>$screenshot,
			'added_on'=>$date,
			'added_by'=>$user_id);
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');
		redirect(page_url.'Dashboard');
		}else
		{
		$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Dashboard');
		}
	}
	public function remove_dashboard_image(){
	    $id = $this->uri->segment(3);
	    $this->db->where('id',$id);
	    $this->db->delete('dashboard_image');
	    	$this->session->set_flashdata('message','<div class="alert alert-info">Thank You! record successfully removed.</div><br/>');
        redirect(page_url.'Dashboard');
	}
	public function user_dashboard()
	{
		$this->load->view('dashboard/user_dashboard');
	}
	public function hod_consolidate_dashboard()
	{
		$this->load->view('dashboard/hod_consolidate_dashboard');
	}	
	public function storenotes()
	{
	    $restyu=$this->input->post('cont');
	     $data=array('userid'=>$_SESSION['logged_in']['user_id'],'notes'=>$restyu,'updatedOn'=>date('Y-m-d H:i:s'));
	    $rest=$this->db->select('id')->from('storenotes')->where('userid',$_SESSION['logged_in']['user_id'])->get();
	    if($rest->num_rows()>0)
	    {
	        $this->db->where('userid',$_SESSION['logged_in']['user_id']);
	        $this->db->update('storenotes',$data);
	    }else
	    {
	    $this->db->insert('storenotes',$data);
	    }
	}

	public function dashboard_access() {
	    $data['dashboardAccessModules'] = $this->reportingdata->getDashboardAccessModules($this->uri->segment(3));
	    $data['dashboardModules'] = $this->reportingdata->getDashboardModules($data['dashboardAccessModules']);
		$this->load->view('dashboard/dashboard_access', $data);
	}

	public function add_dashboard_access() {
		$user_id=$this->uri->segment(3);
	    $submodule = $this->input->post('submodule');
	    $module = $this->input->post('module');
	    $this->reportingdata->deleteDashboardAccess($user_id);
	    //echo "<pre>";print_r($submodule);exit;
	    for($i=0; $i<count($submodule); $i++) {
	    $data = array(
	            'userid' => $user_id,
	            'submoduleid' => $submodule[$i],
	            'moduleid' => $module[$i]
	            );
	      $this->reportingdata->saveDashboardAccess($data);
	    }

	       $this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Dashboard successfully updated.</div>');
	       redirect(page_url.'Dashboard/dashboard_access/'.$user_id);
	}

	public function view_docs(){
	    $this->load->view('dashboard/sub-reference');
	}
	public function sub_reference_list()
	{
		$i=1;
		$ids = array('11','26','31','34');
		$department_data= array();
		$this->db->select('*')->from('department_sub_reference')->where_in('ref_id',$ids,false);
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$status = $row->status;
if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_reference_status/".$row->sub_ref_id."/".$row->status."/".$row->ref_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_reference_status/".$row->sub_ref_id."/".$row->status."/".$row->ref_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_sub_reference/".$row->sub_ref_id."'><i class='fa fa-pencil'></i></a>";	
			$add = "<a href='".page_url."Master/User_management/sub_subreference/".$row->ref_id."/".$row->sub_ref_id."'><span class='btn btn-danger btn-xs'>Add 3rd Level Sub Reference</span></a>";
			$attachment = "<a href='".referencefilepath.$row->attachment."' class='btn btn-success btn-xs' target='_blank'>Click here to download</a>";
			$attachment_link  = referencefilepath.$row->attachment;
			$department_data[] = array('sr_no'=>$i,
			'reference'=>$row->sub_ref_title,
			'attachment'=>$attachment,
			'video_link'=>$row->video_link,
			'add'=>$add,
			'attachment_link'=>$attachment_link,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
"sEcho" => 1,
"iTotalRecords" => count($department_data),
"iTotalDisplayRecords" => count($department_data),
"aaData"=>$department_data);
echo json_encode($results);
	}
	public function view_videos(){		
		$this->load->view('dashboard/view_videos');
	}
	public function sub_reference_video_list()
	{
		$i=1;
		$ids = array('27');
		$department_data= array();
		$this->db->select('*')->from('department_sub_reference')->where_in('ref_id',$ids,false);
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_reference_status/".$row->sub_ref_id."/".$row->status."/".$row->ref_id."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/User_management/update_sub_reference_status/".$row->sub_ref_id."/".$row->status."/".$row->ref_id."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/User_management/edit_sub_reference/".$row->sub_ref_id."'><i class='fa fa-pencil'></i></a>";	
			$add = "<a href='".page_url."Master/User_management/sub_subreference/".$row->ref_id."/".$row->sub_ref_id."'><span class='btn btn-danger btn-xs'>Add 3rd Level Sub Reference</span></a>";
			$attachment = "<a href='".referencefilepath.$row->attachment."' class='btn btn-success btn-xs' target='_blank'>Click here to download</a>";

			$department_data[] = array('sr_no'=>$i,
			'reference'=>$row->sub_ref_title,
			'attachment'=>$attachment,
			'videourl'=>$row->video_link,
			'video_link'=>"<a href='".$row->video_link."' target='_blank'>click here to view videos</a>",
			'add'=>$add,
			'status'=>$sta,
			'edit'=>$edit);
$i++;
		}

$results = array(

			"sEcho" => 1,
			"iTotalRecords" => count($department_data),
			"iTotalDisplayRecords" => count($department_data),
			"aaData"=>$department_data);
		echo json_encode($results);
	}

	public function departmentwise_report(){
		$this->load->view('dashboard/departmentwise_report');
	}

	public function store_report(){
		$this->load->view('dashboard/store_report');
	}

	public function admin_report(){
		$this->load->view('dashboard/admin_report');
	}

	public function purchase_report(){
		$this->load->view('dashboard/purchase_report');
	}

	public function sales_report(){
		$this->load->view('dashboard/sales_report');
	}

	public function service_report(){
		$this->load->view('dashboard/service_report');
	}

	public function user_report(){
		$this->load->view('dashboard/user_report');
	}

	public function fms_report(){
		$this->load->view('dashboard/fms_report');
	}

	public function hr_report(){
		$this->load->view('dashboard/hr_report');
	}

	public function checklist_dashboard(){
		$this->load->view('dashboard/checklist_dashboard');
	}

	public function sales_forms(){
		$this->load->view('dashboard/sales_forms');
	}

	public function accounts_forms(){
		$this->load->view('dashboard/accounts_forms');
	}

	public function service_forms(){
		$this->load->view('dashboard/service_forms');
	}
	public function common_forms(){
		$this->load->view('dashboard/common_forms');
	}

	public function staff_forms(){
		$this->load->view('dashboard/staff_forms');
	}

	public function purchase_forms(){
		$this->load->view('dashboard/purchase_forms');
	}

	public function salestool_report(){
		$this->load->view('dashboard/salestool_report');
	}

	public function checklist_master(){
		$this->load->view('dashboard/checklist_master');
	}

	public function sampletesting_master(){
		$this->load->view('dashboard/sampletesting_master');
	}

	public function storepurchase_master(){
		$this->load->view('dashboard/storepurchase_master');
	}

	public function form_master(){
		$this->load->view('dashboard/form_master');
	}

	public function calibration_master(){
		$this->load->view('dashboard/calibration_master');
	}



public function supplier(){

    $this->load->view('dashboard/supply_form.php');

}



public function supplier_detail(){

    $this->load->view('dashboard/supplier_detail.php');

}

public function flutter(){

    $this->load->view('dashboard/flutter.php');

}

public function flutter2(){

    $this->load->view('dashboard/flutter2.php');

}

public function forecasting_report()
{
	$this->load->view('dashboard/forecasting');
}

public function sales_master()
{
	$this->load->view('dashboard/sales_master');
}

	public function sales_dash_board(){
		$this->load->view('dashboard/sales_dash_board'); 

	}

	public function dashboard_akash(){
		$this->load->view('dashboard/dashboard_akash'); 

	}

	public function sales_stats(){
		$this->load->view('dashboard/sales_stats_report');
	}

	public function ledger(){
		$this->load->view('dashboard/ledger');
	}

	public function funnel(){
		$this->load->view('dashboard/funnel');
	}

	public function acceptnotification(){
$user_id = $_SESSION['logged_in']['user_id'];
		$data = array('record_id'=>$this->input->post('recordid'),
			'accepted_by'=>$user_id,
			'accepted_on'=>date('Y-m-d H:i:s'));

		$this->db->insert('notification_accept',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success">Thank You for the acceptance.</div><br/>');
			redirect(page_url.'Dashboard/');


	}

	public function task_master_dashboard(){
		$this->load->view('dashboard/task_master_dashboard');
	}

	function opportunity_dashboard()
	{
		$financial_year_options = $this->user->get_available_financial_years();
		$selected_financial_year = trim((string) $this->input->get('financial_year', true));
		$available_financial_year_values = array();

		foreach ($financial_year_options as $financial_year_option) {
			$available_financial_year_values[] = $financial_year_option['value'];
		}

		if (empty($selected_financial_year) || !in_array($selected_financial_year, $available_financial_year_values, true)) {
			$selected_financial_year = !empty($financial_year_options) ? $financial_year_options[0]['value'] : $this->user->get_financial_year_details('')['value'];
		}

		$selected_financial_year_details = $this->user->get_financial_year_details($selected_financial_year);
		$sales_data = $this->user->get_sales_data_by_financial_year($selected_financial_year);
		$order_data_by_brand = $this->user->get_order_values_and_counts_by_brand_for_financial_year($selected_financial_year);
		$order_summary = $this->user->get_financial_year_order_summary($selected_financial_year);
		$marketing_df_income = $this->user->get_marketing_df_income_breakdown_by_financial_year($selected_financial_year);
		$spares_sales_data = $this->user->get_spares_sales_data_by_financial_year($selected_financial_year);
		$spares_order_summary = $this->user->get_spares_order_summary_by_financial_year($selected_financial_year);
		$spares_income_breakdown = $this->user->get_spares_income_breakdown_by_financial_year($selected_financial_year);
		$spares_top_brand = $this->user->get_spares_top_brand_by_financial_year($selected_financial_year);
		$service_sales_data = $this->user->get_service_sales_data_by_financial_year($selected_financial_year);
		$service_order_summary = $this->user->get_service_order_summary_by_financial_year($selected_financial_year);
		$service_income_breakdown = $this->user->get_service_income_breakdown_by_financial_year($selected_financial_year);
		$service_top_brands = $this->user->get_service_top_brands_by_financial_year($selected_financial_year);

		$top_agent_name = 'No sales data';
		$top_agent_value = 0;
		if (!empty($sales_data)) {
			$top_agent_name = $sales_data[0]->agent_name;
			$top_agent_value = (float) $sales_data[0]->total_sales;
		}

		$top_brand_name = 'No brand data';
		$top_brand_value = 0;
		if (!empty($order_data_by_brand)) {
			$top_brand_name = $order_data_by_brand[0]->name;
			$top_brand_value = (float) $order_data_by_brand[0]->total_order_value;
		}

		$data['sales_data'] = $sales_data;
		$data['order_data_by_brand'] = $order_data_by_brand;
		$data['spares_sales_data'] = $spares_sales_data;
		$data['spares_order_summary'] = $spares_order_summary;
		$data['marketing_df_income'] = $marketing_df_income;
		$data['spares_income_breakdown'] = $spares_income_breakdown;
		$data['spares_top_brand'] = $spares_top_brand;
		$data['service_sales_data'] = $service_sales_data;
		$data['service_order_summary'] = $service_order_summary;
		$data['service_income_breakdown'] = $service_income_breakdown;
		$data['service_top_brands'] = $service_top_brands;
		$data['financial_year_options'] = $financial_year_options;
		$data['selected_financial_year'] = $selected_financial_year_details['value'];
		$data['selected_financial_year_label'] = $selected_financial_year_details['label'];
		$data['selected_financial_year_start_date'] = $selected_financial_year_details['start_date'];
		$data['selected_financial_year_end_date'] = $selected_financial_year_details['end_date'];
		$data['order_analytics_summary'] = array(
			'total_orders' => !empty($order_summary['total_orders']) ? (int) $order_summary['total_orders'] : 0,
			'total_order_value' => !empty($order_summary['total_order_value']) ? (float) $order_summary['total_order_value'] : 0,
			'avg_order_value' => !empty($order_summary['avg_order_value']) ? (float) $order_summary['avg_order_value'] : 0,
			'active_agents' => !empty($order_summary['active_agents']) ? (int) $order_summary['active_agents'] : 0,
			'active_brands' => !empty($order_summary['active_brands']) ? (int) $order_summary['active_brands'] : 0,
			'latest_order_date' => !empty($order_summary['latest_order_date']) ? $order_summary['latest_order_date'] : '',
			'top_agent_name' => $top_agent_name,
			'top_agent_value' => $top_agent_value,
			'top_brand_name' => $top_brand_name,
			'top_brand_value' => $top_brand_value,
		);

		$this->load->view('dashboard/new_opportunity_dashboard',$data);
	}

	function superadminDashboard()
	{
		$data['sales_data'] = $this->user->get_monthly_sales();
		$data['order_data'] = $this->user->checkorderdomesticorintl();
		//echo "<pre>"; print_r($data['order_data']); exit;

		$this->load->view('dashboard/superadmindashboard',$data);
	}

	function closeddf(){
		redirect(page_url . 'Df_reports/closed_df_report');
	}

	function runningdftaskdelay(){
		$this->load->view('dashboard/runningdftaskdelay');
	}

	function df_dispatch_report(){
		$this->load->view('master/df_dispatch_report');
	}

	function filterdfdispatchviamonth(){
		$startdate= $this->input->post('startdate');
		$enddate= $this->input->post('enddate');
		redirect(page_url."Dashboard/df_dispatch_report/".$startdate."/".$enddate);
	}

	function filterorderbybrandname(){
		$this->load->view('dashboard/filterorderbybrand');
	}

	function setdfpriority(){
		$dfid = $this->input->post('taskkiid');
		$priority = $this->input->post('prioritymarked');

		$data = array('priority_marked'=>$priority);
		$this->db->where('id',$dfid);
		$this->db->update('df_release',$data);
		$this->session->set_flashdata('message','<div class="alert alert-success" style="color:#000">Thank you, record successfully added.</div>');
			redirect(page_url.'Dashboard/df_dispatch_report/'.$this->uri->segment(3)."/".$this->uri->segment(4));
	}


public function filteropportunitybysource(){
	$this->load->view('leads/filteropportunitybysource');	
}

  public function get_tasksNew() {

  		$df_id=$this->uri->segment(3);
  		$departmentid=$this->uri->segment(4);
        $tasks = $this->reportingdata->get_tasks_by_df($df_id, $departmentid);
        echo json_encode($tasks);
    }

	public function basic_machine_df_form(){

		$this->load->model('Task_model');
	$lead_id=$this->uri->segment(5);

    $result = $this->Task_model->getSpecificTaskDates(date('Y-m-d'));

    // Initialize both dates as null
    $task_103_date = null;
    $task_52_date = null;

    foreach ($result as $task) {
        if ($task['task_id'] == 103) {
            $task_103_date = $task['expected_date'];
        }
        if ($task['task_id'] == 52) {
            $task_52_date = $task['expected_date'];
        }
    }

    // Pass variables to view
    $data['task_103_date'] = $task_103_date;
    $data['task_52_date'] = $task_52_date;

    $today = new DateTime();

// Clone the object to avoid modifying the original
$trialDate = clone $today;
$dispatchDate = clone $today;

// Add 103 and 110 days respectively
$trialDate->modify('+103 days');
$dispatchDate->modify('+110 days');

// Format and store in variables
$trial_date = $trialDate->format('Y-m-d');
$dispatch_date = $dispatchDate->format('Y-m-d');

$data['task_103_date'] = $dispatch_date;
    $data['task_52_date'] = $trial_date;
		$this->load->view('dashboard/basic_machine_df',$data);
	}


	public function edit_basic_machine_df_project_form(){

		$today = new DateTime();

		// Clone the object to avoid modifying the original
		$trialDate = clone $today;
		$dispatchDate = clone $today;

		// Add 103 and 110 days respectively
		$trialDate->modify('+103 days');
		$dispatchDate->modify('+110 days');

		// Format and store in variables
		$trial_date = $trialDate->format('Y-m-d');
		$dispatch_date = $dispatchDate->format('Y-m-d');

		$data['task_103_date'] = $trial_date;
		$data['task_52_date'] = $dispatch_date;
    

		$this->load->view('dashboard/edit_basic_machine_df_project_form',$data);

	}




	public function df_project_form(){

		$this->load->model('Task_model');
	$lead_id=$this->uri->segment(5);

    $result = $this->Task_model->getSpecificTaskDates(date('Y-m-d'));

    // Initialize both dates as null
    $task_103_date = null;
    $task_52_date = null;

    foreach ($result as $task) {
        if ($task['task_id'] == 103) {
            $task_103_date = $task['expected_date'];
        }
        if ($task['task_id'] == 52) {
            $task_52_date = $task['expected_date'];
        }
    }

    // Pass variables to view
    $data['task_103_date'] = $task_103_date;
    $data['task_52_date'] = $task_52_date;

    $today = new DateTime();

// Clone the object to avoid modifying the original
$trialDate = clone $today;
$dispatchDate = clone $today;

// Add 103 and 110 days respectively
$trialDate->modify('+103 days');
$dispatchDate->modify('+110 days');

// Format and store in variables
$trial_date = $trialDate->format('Y-m-d');
$dispatch_date = $dispatchDate->format('Y-m-d');

$data['task_103_date'] = $dispatch_date;
    $data['task_52_date'] = $trial_date;

		$this->load->view('dashboard/df_project_form',$data);

	}

	public function df_project_form_copy(){

		$this->load->view('dashboard/df_project_form_copy');

	}


	public function edit_df_project_form(){

		$today = new DateTime();

		// Clone the object to avoid modifying the original
		$trialDate = clone $today;
		$dispatchDate = clone $today;

		// Add 103 and 110 days respectively
		$trialDate->modify('+103 days');
		$dispatchDate->modify('+110 days');

		// Format and store in variables
		$trial_date = $trialDate->format('Y-m-d');
		$dispatch_date = $dispatchDate->format('Y-m-d');

		$data['task_103_date'] = $trial_date;
		$data['task_52_date'] = $dispatch_date;
    

		$this->load->view('dashboard/edit_df_project_form',$data);

	}


	public function df_project_form_add() {
    // --- 1. SETUP & VALIDATION ---
    $this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');

    $norml_id = $this->uri->segment(3);
    $po_id    = $this->uri->segment(4);
    $lead_id  = $this->uri->segment(5);
    $user_id  = $this->session->userdata['logged_in']['user_id'];

    // --- DUPLICATE CHECK: Prevent double submission ---
    $check_duplicate = $this->db->get_where('df_design_form_table', array('po_id' => $po_id));
    if ($check_duplicate->num_rows() > 0) {
        $this->session->set_flashdata('error', '<div class="alert alert-warning">This record has already been saved.</div>');
        redirect(page_url . 'Dashboard');
        return;
    }

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('dashboard/df_project_form');
        return;
    }

    date_default_timezone_set("Asia/Kolkata");

    // --- 2. START DATABASE TRANSACTION ---
    $this->db->trans_start();

    try {
        // [Table 1] Main Design Form
        $data = array(
            'po_id'            => $po_id,
            'lead_id'          => $lead_id,
            // 'design_form_name' => $this->input->post('design_form_name'),
            'design_form_date' => $this->input->post('design_form_date'),
            'reference_no'     => $this->input->post('reference_no'),
            'ref_df_date'      => $this->input->post('ref_df_date'),
            'added_on'         => date("Y-m-d H:i:s"),
            'added_by'         => $user_id
        );
        $this->db->insert('df_design_form_table', $data);
        $latest_id = $this->db->insert_id();

        // [Table 2] Multi Track Machine
        $data1 = array(
            'record_id'                   => $latest_id,
            'ce_complied'                 => $this->input->post('ce_complied'),
            'ce_complied_remarks'         => $this->input->post('ce_complied_remarks'),
            // 'date_of_po'               => $this->input->post('date_of_po'),
            'date_of_po_remarks'          => $this->input->post('date_of_po_remarks'),
            'penalty_clause'              => $this->input->post('penalty_clause'),
            'penalty_clause_remarks'      => $this->input->post('penalty_clause_remarks'),
            'dispatch_date'               => date('Y-m-d', strtotime($this->input->post('dispatch_date'))),
            'dispatch_date_remarks'       => $this->input->post('dispatch_date_remarks'),
            'trial_date'                  => date('Y-m-d', strtotime($this->input->post('trial_date'))),
            'trial_date_remarks'          => $this->input->post('trial_date_remarks'),
            // 'machine_mode_no'          => $this->input->post('machine_mode_no'),
            'machine_mode_no_remarks'     => $this->input->post('machine_mode_no_remarks'),
            // 'machine_type'             => $this->input->post('machine_type'),
            'machine_type_remarks'        => $this->input->post('machine_type_remarks'),
            'machine_orientation'         => $this->input->post('machine_orientation'),
            'machine_orientation_remarks' => $this->input->post('machine_orientation_remarks'),
            'added_on'                    => date("Y-m-d H:i:s"),
            'added_by'                    => $user_id
        );
        $this->db->insert('df_form_multi_track_machine', $data1);

        // [Table 3] Machine Specification
        $data3 = array(
            'record_id'                     => $latest_id,
            'tracks_remarks'                => $this->input->post('tracks_remarks'),
            'product_packed_remarks'        => $this->input->post('product_packed_remarks'),
            'filling_unit_remarks'          => $this->input->post('filling_unit_remarks'),
            'product_specification_remarks' => $this->input->post('product_specification_remarks'),
            'profle_sealing_remarks'        => $this->input->post('profle_sealing_remarks'),
            'added_on'                      => date("Y-m-d H:i:s"),
            'added_by'                      => $user_id
        );
        $this->db->insert('df_form_machine_specification', $data3);

        // [Table 4] Machine 1
        $data4 = array(
            'record_id' => $latest_id,
            'notching_option' => $this->input->post('notching_option'),
            'notching_option_remarks' => $this->input->post('notching_option_remarks'),
            'hooper_details' => $this->input->post('hooper_details'),
            'openable_option' => $this->input->post('openable_option'),
            'closed_option' => $this->input->post('closed_option'),
            'pressurised_option' => $this->input->post('pressurised_option'),
            'closed_pressurised_option' => $this->input->post('closed_pressurised_option'),
            'non_pressurised_option' => $this->input->post('non_pressurised_option'),
            'non_closed_pressurised_option' => $this->input->post('non_closed_pressurised_option'),
            'hooper_details_remarks' => $this->input->post('hooper_details_remarks'),
            'cladding_provision' => $this->input->post('cladding_provision'),
            'cladding_provision_remarks' => $this->input->post('cladding_provision_remarks'),
            'embossing' => $this->input->post('embossing'),
            'embossing_option' => $this->input->post('embossing_option'),
            'linear_option' => $this->input->post('linear_option'),
            'rotary_option' => $this->input->post('rotary_option'),
            'provision_remarks' => $this->input->post('provision_remarks'),
            'web_aligner' => $this->input->post('web_aligner'),
            'web_aligner_option' => $this->input->post('web_aligner_option'),
            'web_aligner_remarks' => $this->input->post('web_aligner_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine1', $data4);

        // [Table 5] Machine 2
        $data5 = array(
            'record_id' => $latest_id,
            'center_slitting' => $this->input->post('center_slitting'),
            'center_slitting_remarks' => $this->input->post('center_slitting_remarks'),
            'vertical_slitting' => $this->input->post('vertical_slitting'),
            'vertical_slitting_remarks' => $this->input->post('vertical_slitting_remarks'),
            'vertical_sealer' => $this->input->post('vertical_sealer'),
            'vertical_sealer_remarks' => $this->input->post('vertical_sealer_remarks'),
            'laminate_pulling' => $this->input->post('laminate_pulling'),
            'laminate_pulling_remarks' => $this->input->post('laminate_pulling_remarks'),
            'up_down' => $this->input->post('up_down'),
            'up_down_remarks' => $this->input->post('up_down_remarks'),
            'embossing_coding' => $this->input->post('embossing_coding'),
            'embossing_coding_remarks' => $this->input->post('embossing_coding_remarks'),
            'cooling_station' => $this->input->post('cooling_station'),
            'cooling_station_remarks' => $this->input->post('cooling_station_remarks'),
            'horizontal_sealer' => $this->input->post('horizontal_sealer'),
            'horizontal_sealer_remarks' => $this->input->post('horizontal_sealer_remarks'),
            'perforation_blade' => $this->input->post('perforation_blade'),
            'perforation_blade_remarks' => $this->input->post('perforation_blade_remarks'),
            'priston_drive' => $this->input->post('priston_drive'),
            'priston_drive_remarks' => $this->input->post('priston_drive_remarks'),
            'shutt_off_nozzle' => $this->input->post('shutt_off_nozzle'),
            'shutt_off_nozzle_remarks' => $this->input->post('shutt_off_nozzle_remarks'),
            'filling_plate_drive' => $this->input->post('filling_plate_drive'),
            'filling_plate_drive_remarks' => $this->input->post('filling_plate_drive_remarks'),
            'individual_weight' => $this->input->post('individual_weight'),
            'individual_weight_remarks' => $this->input->post('individual_weight_remarks'),
            'overall_weight_adjust' => $this->input->post('overall_weight_adjust'),
            'overall_weight_adjust_remarks' => $this->input->post('overall_weight_adjust_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine2', $data5);

        // [Table 6] Machine 3
        $data6 = array(
            'record_id' => $latest_id,
            'traverse_drive' => $this->input->post('traverse_drive'),
            'yes_traverse_drive' => $this->input->post('yes_traverse_drive'),
            'traverse_drive_remarks' => $this->input->post('traverse_drive_remarks'),
            'printer_yes_no' => $this->input->post('printer_yes_no'),
            'printer' => $this->input->post('printer'),
            'inkjet_option' => $this->input->post('inkjet_option'),
            'tto_option' => $this->input->post('tto_option'),
            'thermal_inkjet_option' => $this->input->post('thermal_inkjet_option'),
            'printer_remarks' => $this->input->post('printer_remarks'),
            'case_packer_drive' => $this->input->post('case_packer_drive'),
            'case_packer_drive_remarks' => $this->input->post('case_packer_drive_remarks'),
            'nozzle_funnel' => $this->input->post('nozzle_funnel'),
            'powder_option1' => $this->input->post('powder_option1'),
            'liquid_option1' => $this->input->post('liquid_option1'),
            'liquid_shut_option1' => $this->input->post('liquid_shut_option1'),
            'nozzle_funnel_remarks' => $this->input->post('nozzle_funnel_remarks'),
            'hose_pipe' => $this->input->post('hose_pipe'),
            'hose_pipe_remarks' => $this->input->post('hose_pipe_remarks'),
            'string_option' => $this->input->post('string_option'),
            'batch_cut_format_remarks' => $this->input->post('batch_cut_format_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine3', $data6);

        // [Table 7] Machine 4
        $data7 = array(
            'record_id' => $latest_id,
            'horizontal_sealer1_remarks' => $this->input->post('horizontal_sealer1_remarks'),
            'vertical_sealer1_remarks' => $this->input->post('vertical_sealer1_remarks'),
            'rotary_valve_coating' => $this->input->post('rotary_valve_coating'),
            'rotary_valve_coating_remarks' => $this->input->post('rotary_valve_coating_remarks'),
            'working_speed' => $this->input->post('working_speed'),
            'working_speed_remarks' => $this->input->post('working_speed_remarks'),
            'reel_shaft_type' => $this->input->post('reel_shaft_type'),
            'reel_shaft_type_remarks' => $this->input->post('reel_shaft_type_remarks'),
            'reel_core_diameter' => $this->input->post('reel_core_diameter'),
            'reel_core_diameter_remarks' => $this->input->post('reel_core_diameter_remarks'),
            'trial_material' => $this->input->post('trial_material'),
            'trial_material_remarks' => $this->input->post('trial_material_remarks'),
            'laminate_detail' => $this->input->post('laminate_detail'),
            'laminate_detail_remarks' => $this->input->post('laminate_detail_remarks'),
            'heater_control_system' => $this->input->post('heater_control_system'),
            'heater_control_system_remarks' => $this->input->post('heater_control_system_remarks'),
            'beacon_light' => $this->input->post('beacon_light'),
            'beacon_light_remarks' => $this->input->post('beacon_light_remarks'),
            'hooper_level' => $this->input->post('hooper_level'),
            'hooper_level_option' => $this->input->post('hooper_level_option'),
            'hooper_level_remarks' => $this->input->post('hooper_level_remarks'),
            'safety_relay' => $this->input->post('safety_relay'),
            'safety_relay_remarks' => $this->input->post('safety_relay_remarks'),
            'plc_maker' => $this->input->post('plc_maker'),
            'plc_maker_remarks' => $this->input->post('plc_maker_remarks'),
            'hmi_size' => $this->input->post('hmi_size'),
            'hmi_size_remarks' => $this->input->post('hmi_size_remarks'),
            'cip_system' => $this->input->post('cip_system'),
            'cip_system_option' => $this->input->post('cip_system_option'),
            'cip_system_option_remarks' => $this->input->post('cip_system_option_remarks'),
            'tool_kit' => $this->input->post('tool_kit'),
            'tool_kit_remarks' => $this->input->post('tool_kit_remarks'),
            'changeover_part' => $this->input->post('changeover_part'),
            'changeover_part_remarks' => $this->input->post('changeover_part_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine4', $data7);

        // [Table 8] Machine 5
        $data8 = array(
            'record_id' => $latest_id,
            'secondary_pack' => $this->input->post('secondary_pack'),
            'case_packer' => $this->input->post('case_packer'),
            'secondary_pack_remarks' => $this->input->post('secondary_pack_remarks'),
            'ladder_platform' => $this->input->post('ladder_platform'),
            'ladder_platform_remarks' => $this->input->post('ladder_platform_remarks'),
            'machine_guarding' => $this->input->post('machine_guarding'),
            'aluminium_option' => $this->input->post('aluminium_option'),
            'ss_304_option' => $this->input->post('ss_304_option'),
            'machine_guarding_remarks' => $this->input->post('machine_guarding_remarks'),
            'special_notes' => $this->input->post('special_notes'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine5', $data8);

        // [Secondary Pack Loop]
        $yes_secondary_pack = $this->input->post('yes_secondary_pack');
        $qty = $this->input->post('yes_secondary_pack_qty');
		$yes_secondary_pack_remark = $this->input->post('yes_secondary_pack_remark');
        
        // Ensure arrays are valid before looping
        if (!empty($yes_secondary_pack)) {
            // Force to array if single string
            if (!is_array($yes_secondary_pack)) {
                $yes_secondary_pack = [$yes_secondary_pack];
            }
            if (!is_array($qty)) {
                $qty = [$qty];
            }

            for ($i = 0; $i < count($yes_secondary_pack); $i++) {
                if (empty($yes_secondary_pack[$i])) continue; // Skip empty entries

                $pack = array(
                    'record_id' => $latest_id,
                    'yes_secondary_pack' => $yes_secondary_pack[$i],
                    'yes_secondary_pack_qty' => $qty[$i] ?? '',
					 'yes_secondary_pack_remark' => $yes_secondary_pack_remark[$i],
                    'added_on' => date("Y-m-d h:i:s"),
                    'added_by' => $user_id
                );
                $this->db->insert('df_form_multi_track_machine_pack', $pack);
            }
        }

        // [Tasks] Update current and Create new
        $data34 = array(
            'df_id' => 0,
            'task_status' => 1,
            'task_completed_on' => date('Y-m-d H:i:s'),
            'task_completed_by' => $user_id,
            'userid' => $user_id
        );
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('task_department_wise_scheduling', $data34);

        $pono = $this->getpono($this->uri->segment(3));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $nextWorkingDate = $this->iftomorrowisholiday($tomorrow);

        $newTaskData = array(
            'df_id' => 0,
            'taskid' => 86,
            'department_id' => 9,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime($nextWorkingDate)),
            'added_on' => date('Y-m-d H:i:s'),
            'added_by' => $user_id,
            'po_id' => $pono,
            'task_status' => 0,
            'remarks' => '',
            'assigned_user' => $user_id,
            'userid' => $user_id,
            'assigned_by' => $user_id,
            'assigned_on' => date('Y-m-d H:i:s')
        );
        $this->db->insert('task_department_wise_scheduling', $newTaskData);

        // --- 3. COMMIT TRANSACTION ---
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">Database error. Please try again.</div>');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            // Success! Load the Bridge View
            $this->session->set_flashdata('success', '<div class="alert alert-success">Thank You! Data successfully added.</div>');

            // URL for PDF (Uses df_form_pdf as confirmed)
            $pdf_url = page_url . 'Formats/df_form_dompdf/' . $po_id . '/' . $lead_id . '/' . $latest_id;
            
            // URL for Dashboard
            $redirect_url = page_url . 'Dashboard';

            $data_view = [
                'pdf_url'      => $pdf_url,
                'redirect_url' => $redirect_url,
            ];
            
            $this->load->view('dashboard/df_submit_bridge', $data_view);
        }
    } catch (Exception $e) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('error', '<div class="alert alert-danger">System Error: ' . $e->getMessage() . '</div>');
        redirect($_SERVER['HTTP_REFERER']);
    }
}

	

function getpono($recordid){
	$q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('id',$recordid)->get();
	foreach($q->result() as $row);
	$pono = $row->po_id;
	return $pono;

}


	//-----------------------------Edit DF Form----------------------------------------


	public function df_project_form_edit_old_one(){

		//$this->form_validation->set_rules('design_form_name', 'Design Form', 'required|trim');
		$this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');

		// $norml_id=$this->uri->segment(3);
		$po_id = $this->uri->segment(3);
		$lead_id = $this->uri->segment(4);
		 $record_id = $this->uri->segment(5);

			if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/df_project_form');
		}else{

			date_default_timezone_set("Asia/Kolkata");

			$data =array(
				'po_id'=>$po_id,
				'lead_id'=>$lead_id,
				//'design_form_name'=>$this->input->post('design_form_name'),
				'design_form_date'=>$this->input->post('design_form_date'),
				'reference_no'=>$this->input->post('reference_no'),
				'ref_df_date'=>date('Y-m-d',strtotime($this->input->post('ref_df_date'))),
				'iom_no'=>$this->input->post('iom_no'),
				'invoice_no'=>$this->input->post('invoice_no'),
				'invoice_date'=>$this->input->post('invoice_date'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);


			 $this->db->where('id', $record_id);
        $this->db->update('df_design_form_table', $data);

			

			$data1 = array(

				'ce_complied'=>$this->input->post('ce_complied'),
				'ce_complied_remarks'=>$this->input->post('ce_complied_remarks'),
				//'date_of_po'=>$this->input->post('date_of_po'),
				'date_of_po_remarks'=>$this->input->post('date_of_po_remarks'),
				'penalty_clause'=>$this->input->post('penalty_clause'),
				'penalty_clause_remarks'=>$this->input->post('penalty_clause_remarks'),
				//'dispatch_date'=>$this->input->post('dispatch_date'),
				'dispatch_date_remarks'=>$this->input->post('dispatch_date_remarks'),
				//'trial_date'=>$this->input->post('trial_date'),
				'trial_date_remarks'=>$this->input->post('trial_date_remarks'),
				//'machine_mode_no'=>$this->input->post('machine_mode_no'),
				'machine_mode_no_remarks'=>$this->input->post('machine_mode_no_remarks'),
				//'machine_type'=>$this->input->post('machine_type'),
				'machine_type_remarks'=>$this->input->post('machine_type_remarks'),
				'machine_orientation'=>$this->input->post('machine_orientation'),
				'machine_orientation_remarks'=>$this->input->post('machine_orientation_remarks'),
'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine', $data1);


  		// $pouch_widths = $this->input->post('pouch_width');
        // $pouch_lengths = $this->input->post('pouch_length');
        // $pouch_heights = $this->input->post('pouch_height');
		// $quantity_packed = $this->input->post('quantity_packed');
        // $quantity_units = $this->input->post('quantity_packed_unit');

		//   if (!empty($pouch_widths)) {
        //     for ($i = 0; $i < count($pouch_widths); $i++) {
                $data2 = array(
                    // 'pouch_width' => $pouch_widths[$i],
                    // 'pouch_length' => $pouch_lengths[$i],
                    // 'pouch_height' => $pouch_heights[$i],
                    'pouch_size_remarks' => $this->input->post('pouch_size_remarks'),
					// 'quantity_packed' => $quantity_packed[$i],
                  //  'quantity_packed_unit' => $quantity_units[$i],
                    'quantity_packed_remarks' => $this->input->post('quantity_packed_remarks'),
                    'added_on' => date("Y-m-d h:i:s"),
                    'added_by' => $this->session->userdata['logged_in']['user_id']
                );
              $this->db->where('record_id', $record_id);
                $this->db->update('df_form_machine_specification_size_qnty', $data2);
        //     }
        // }


			$data3 = array(
				
				// 'tracks'=>$this->input->post('tracks'),
				// 'product_packed'=>$this->input->post('product_packed'),
				// 'powder_option'=>$this->input->post('powder_option'),
				// 'liquid_option'=>$this->input->post('liquid_option'),
				// 'non_viscous_option'=>$this->input->post('non_viscous_option'),
				// 'viscous_option'=>$this->input->post('viscous_option'),
				// 'piston_filler_option'=>$this->input->post('piston_filler_option'),
				// 'follow_meter_option'=>$this->input->post('follow_meter_option'),
				// 'non_free_flow_option'=>$this->input->post('non_free_flow_option'),
				// 'cup_filler_option'=>$this->input->post('cup_filler_option'),
				// 'free_flow_option'=>$this->input->post('free_flow_option'),
				// 'weigher_system_option'=>$this->input->post('weigher_system_option'),
				// 'liner_weigher_option'=>$this->input->post('liner_weigher_option'),
				// 'mult_head_weigher_option'=>$this->input->post('mult_head_weigher_option'),
				// 'volumetric_cap_option'=>$this->input->post('volumetric_cap_option'),
				// 'profile_of_sealing'=>$this->input->post('profile_of_sealing'),
				'tracks_remarks'=>$this->input->post('tracks_remarks'),
				'product_packed_remarks'=>$this->input->post('product_packed_remarks'),
				'filling_unit_remarks'=>$this->input->post('filling_unit_remarks'),
				//'density'=>$this->input->post('density'),
				// 'viscosity'=>$this->input->post('viscosity'),
				'product_specification_remarks'=>$this->input->post('product_specification_remarks'),
				'profle_sealing_remarks'=>$this->input->post('profle_sealing_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
        $this->db->update('df_form_machine_specification', $data3);

			$data4 = array(
				'notching_option'=>$this->input->post('notching_option'),
				'notching_option_remarks'=>$this->input->post('notching_option_remarks'),
				'hooper_details'=>$this->input->post('hooper_details'),
				'openable_option'=>$this->input->post('openable_option'),
				'closed_option'=>$this->input->post('closed_option'),
				'pressurised_option'=>$this->input->post('pressurised_option'),
				'closed_pressurised_option'=>$this->input->post('closed_pressurised_option'),
				'non_pressurised_option'=>$this->input->post('non_pressurised_option'),
				'non_closed_pressurised_option'=>$this->input->post('non_closed_pressurised_option'),
				'hooper_details_remarks'=>$this->input->post('hooper_details_remarks'),
				'cladding_provision'=>$this->input->post('cladding_provision'),
				'cladding_provision_remarks'=>$this->input->post('cladding_provision_remarks'),
				'embossing'=>$this->input->post('embossing'),
				'embossing_option'=>$this->input->post('embossing_option'),
				'linear_option'=>$this->input->post('linear_option'),
				'rotary_option'=>$this->input->post('rotary_option'),
				'provision_remarks'=>$this->input->post('provision_remarks'),
				'web_aligner'=>$this->input->post('web_aligner'),
				'web_aligner_option'=>$this->input->post('web_aligner_option'),
				'web_aligner_remarks'=>$this->input->post('web_aligner_remarks'),	
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine1', $data4);


			$data5 = array(
				
				'center_slitting'=>$this->input->post('center_slitting'),
				'center_slitting_remarks'=>$this->input->post('center_slitting_remarks'),
				'vertical_slitting'=>$this->input->post('vertical_slitting'),
				'vertical_slitting_remarks'=>$this->input->post('vertical_slitting_remarks'),
				'vertical_sealer'=>$this->input->post('vertical_sealer'),
				'vertical_sealer_remarks'=>$this->input->post('vertical_sealer_remarks'),
				'laminate_pulling'=>$this->input->post('laminate_pulling'),
				'laminate_pulling_remarks'=>$this->input->post('laminate_pulling_remarks'),
				'up_down'=>$this->input->post('up_down'),
				'up_down_remarks'=>$this->input->post('up_down_remarks'),
				'embossing_coding'=>$this->input->post('embossing_coding'),
				'embossing_coding_remarks'=>$this->input->post('embossing_coding_remarks'),
				'cooling_station'=>$this->input->post('cooling_station'),
				'cooling_station_remarks'=>$this->input->post('cooling_station_remarks'),
				'horizontal_sealer'=>$this->input->post('horizontal_sealer'),
				'horizontal_sealer_remarks'=>$this->input->post('horizontal_sealer_remarks'),
				'perforation_blade'=>$this->input->post('perforation_blade'),
				'perforation_blade_remarks'=>$this->input->post('perforation_blade_remarks'),
				'priston_drive'=>$this->input->post('priston_drive'),
				'priston_drive_remarks'=>$this->input->post('priston_drive_remarks'),
				
				//'individual_option'=>$this->input->post('individual_option'),
				//'rotary_option1'=>$this->input->post('rotary_option1'),
			
				'shutt_off_nozzle'=>$this->input->post('shutt_off_nozzle'),
				'shutt_off_nozzle_remarks'=>$this->input->post('shutt_off_nozzle_remarks'),
				'filling_plate_drive'=>$this->input->post('filling_plate_drive'),
				'filling_plate_drive_remarks'=>$this->input->post('filling_plate_drive_remarks'),
				'individual_weight'=>$this->input->post('individual_weight'),
				'individual_weight_remarks'=>$this->input->post('individual_weight_remarks'),
				'overall_weight_adjust'=>$this->input->post('overall_weight_adjust'),
				'overall_weight_adjust_remarks'=>$this->input->post('overall_weight_adjust_remarks'),
					'vertical_sealer_width'=>$this->input->post('vertical_sealer_width'),
					'horizontal_sealer_width'=>$this->input->post('horizontal_sealer_width'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine2', $data5);


			$data6 =array(
				'traverse_drive'=>$this->input->post('traverse_drive'),
				'yes_traverse_drive'=>$this->input->post('yes_traverse_drive'),
				'traverse_drive_remarks'=>$this->input->post('traverse_drive_remarks'),
				'printer_yes_no'=>$this->input->post('printer_yes_no'),
				'printer'=>$this->input->post('printer'),
				'inkjet_option'=>$this->input->post('inkjet_option'),
				'tto_option'=>$this->input->post('tto_option'),
				'thermal_inkjet_option'=>$this->input->post('thermal_inkjet_option'),
				'printer_remarks'=>$this->input->post('printer_remarks'),
				'case_packer_drive'=>$this->input->post('case_packer_drive'),
				'case_packer_drive_remarks'=>$this->input->post('case_packer_drive_remarks'),
				'nozzle_funnel'=>$this->input->post('nozzle_funnel'),
				'powder_option1'=>$this->input->post('powder_option1'),
				'liquid_option1'=>$this->input->post('liquid_option1'),
				'liquid_shut_option1'=>$this->input->post('liquid_shut_option1'),
				'nozzle_funnel_remarks'=>$this->input->post('nozzle_funnel_remarks'),
				'hose_pipe'=>$this->input->post('hose_pipe'),
				'hose_pipe_remarks'=>$this->input->post('hose_pipe_remarks'),
				// 'batch_cut_format'=>$this->input->post('batch_cut_format'),
				'string_option'=>$this->input->post('string_option'),
				'batch_cut_format_remarks'=>$this->input->post('batch_cut_format_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine3', $data6);

			$data7 =array(
				
				// 'horizontal_sealer1'=>$this->input->post('horizontal_sealer1'),
				'horizontal_sealer1_remarks'=>$this->input->post('horizontal_sealer1_remarks'),
				// 'vertical_sealer1'=>$this->input->post('vertical_sealer1'),
				'vertical_sealer1_remarks'=>$this->input->post('vertical_sealer1_remarks'),
				'rotary_valve_coating'=>$this->input->post('rotary_valve_coating'),
				'rotary_valve_coating_remarks'=>$this->input->post('rotary_valve_coating_remarks'),
				'working_speed'=>$this->input->post('working_speed'),
				'working_speed_remarks'=>$this->input->post('working_speed_remarks'),
				'reel_shaft_type'=>$this->input->post('reel_shaft_type'),
				'reel_shaft_type_remarks'=>$this->input->post('reel_shaft_type_remarks'),
				'reel_core_diameter'=>$this->input->post('reel_core_diameter'),
				'reel_core_diameter_remarks'=>$this->input->post('reel_core_diameter_remarks'),
				'trial_material'=>$this->input->post('trial_material'),
				'trial_material_remarks'=>$this->input->post('trial_material_remarks'),
				'laminate_detail'=>$this->input->post('laminate_detail'),
				'laminate_detail_remarks'=>$this->input->post('laminate_detail_remarks'),
				'heater_control_system'=>$this->input->post('heater_control_system'),
				'heater_control_system_remarks'=>$this->input->post('heater_control_system_remarks'),
				'beacon_light'=>$this->input->post('beacon_light'),
				'beacon_light_remarks'=>$this->input->post('beacon_light_remarks'),
				'hooper_level'=>$this->input->post('hooper_level'),
				'hooper_level_option'=>$this->input->post('hooper_level_option'),
				'hooper_level_remarks'=>$this->input->post('hooper_level_remarks'),
				'safety_relay'=>$this->input->post('safety_relay'),
				'safety_relay_remarks'=>$this->input->post('safety_relay_remarks'),
				// 'supply_voltage'=>$this->input->post('supply_voltage'),
				'supply_voltage_remarks'=>$this->input->post('supply_voltage_remarks'),
				'hmi_size'=>$this->input->post('hmi_size'),
				'hmi_size_remarks'=>$this->input->post('hmi_size_remarks'),
				'plc_maker'=>$this->input->post('plc_maker'),
				'plc_maker_remarks'=>$this->input->post('plc_maker_remarks'),
				'cip_system'=>$this->input->post('cip_system'),
				'cip_system_option'=>$this->input->post('cip_system_option'),
				'cip_system_option_remarks'=>$this->input->post('cip_system_option_remarks'),
				'tool_kit'=>$this->input->post('tool_kit'),
				'tool_kit_remarks'=>$this->input->post('tool_kit_remarks'),
				'changeover_part'=>$this->input->post('changeover_part'),
				'changeover_part_remarks'=>$this->input->post('changeover_part_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine4', $data7);

			 

			$data8 =array(
				'secondary_pack'=>$this->input->post('secondary_pack'),
				//'yes_secondary_pack'=>implode(', ', $yes_secondary_pack),
				'case_packer'=>$this->input->post('case_packer'),
				'secondary_pack_remarks'=>$this->input->post('secondary_pack_remarks'),
				'ladder_platform'=>$this->input->post('ladder_platform'),
				'ladder_platform_remarks'=>$this->input->post('ladder_platform_remarks'),
				'machine_guarding'=>$this->input->post('machine_guarding'),
				'aluminium_option'=>$this->input->post('aluminium_option'),
				'ss_304_option'=>$this->input->post('ss_304_option'),
				'machine_guarding_remarks'=>$this->input->post('machine_guarding_remarks'),
				//'trial_comments'=>$this->input->post('trial_comments'),
				'special_notes'=>$this->input->post('special_notes'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine5', $data8);

			// echo $record_id; exit;

		$idd=	$this->input->post('multi_track_machine_id');

		for($y=0; $y<count($idd); $y++){
			$miid =$idd[$y]; 
			$yes_secondary_packed = $this->input->post('yes_secondary_pack'.$miid);
			$qtyy = $this->input->post('yes_secondary_pack_qty'.$miid);

			$da =array(

				'yes_secondary_pack'=>$yes_secondary_packed,

				'yes_secondary_pack_qty'=>$qtyy,

			);

			$this->db->where('id',$miid);
			$this->db->update('df_form_multi_track_machine_pack', $da);
		}
			 

// Add NEW SECONDARY PACK


					if($this->input->post('add_more_pack')==1){

					$yes_secondary_pack = $this->input->post('yes_secondary_pack_add');

					$qty = $this->input->post('yes_secondary_pack_qty_add');



					for($i = 0; $i<count($yes_secondary_pack); $i++){
					$pack =array(
					'record_id'=>$record_id,
					'yes_secondary_pack'=>$yes_secondary_pack[$i],
					'yes_secondary_pack_qty'=>$qty[$i],
					'added_on' => date("Y-m-d h:i:s"),
					'added_by' => $this->session->userdata['logged_in']['user_id']
					);


					$this->db->insert('df_form_multi_track_machine_pack', $pack);
					}


					}


		// 	$part_desc = $this->input->post('special_notes_list');
		// 	$part_qty = $this->input->post('special_notes_qty');

		// 	  if (!empty($part_desc)) {
        //     for ($i = 0; $i < count($part_desc); $i++) {
        //         $data9 = array(
        //             'special_notes_list' => $part_desc[$i], 
        //             'special_notes_qty' => $part_qty[$i],
        //             'added_on' => date("Y-m-d h:i:s"),
        //             'added_by' => $this->session->userdata['logged_in']['user_id']
        //         );

		// 		 $this->db->where('record_id', $record_id);
        //         $this->db->update('df_form_multi_track_machine6', $data9);
        //     }
        // }


		if($this->uri->segment(6)!= '' && $this->uri->segment(6) >0){

			$taskupdatedata = array('task_completed_on'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$this->session->userdata['logged_in']['user_id'],
				'task_status'=>1);

			$this->db->where('id',$this->uri->segment(6));
			$this->db->update('task_department_wise_scheduling',$taskupdatedata);
			$qqq = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('id',$this->uri->segment(6))->get();

			foreach($qqq->result() as $porecords);
			$ponos = $porecords->po_id;
			// echo "<pre>"; print_r($ponos); exit;

			// echo $po_id;
			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);

			$addnewdata = array('df_id'=>0,
				'taskid'=>87,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$this->session->userdata['logged_in']['user_id'],
				'po_id'=>$ponos,
				'task_status'=>0,
				'userid'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_user'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_by'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$addnewdata);
		}
			$this->session->set_flashdata('success','<div class="alert alert-success"> Thank You! Data successfully Updated.</div>');
		redirect(page_url.'Formats/df_form/'.$po_id.'/'.$lead_id);

		}

	}



	public function df_project_form_edit() {

    // --- 1. SETUP & VALIDATION ---
    // $this->form_validation->set_rules('design_form_name', 'Design Form', 'required|trim');
    $this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');

    // $norml_id = $this->uri->segment(3);
    $po_id     = $this->uri->segment(3);
    $lead_id   = $this->uri->segment(4);
    $record_id = $this->uri->segment(5);

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('dashboard/df_project_form');
        return;
    }

    date_default_timezone_set("Asia/Kolkata");
    $user_id = $this->session->userdata['logged_in']['user_id'];

    // --- 2. START DATABASE TRANSACTION ---
    $this->db->trans_start();

    try {
        // [Table 1] Main Design Form Update
        $data = array(
            'po_id'            => $po_id,
            'lead_id'          => $lead_id,
            // 'design_form_name' => $this->input->post('design_form_name'),
            'design_form_date' => $this->input->post('design_form_date'),
            'reference_no'     => $this->input->post('reference_no'),
            'ref_df_date'      => date('Y-m-d', strtotime($this->input->post('ref_df_date'))),
            'iom_no'           => $this->input->post('iom_no'),
            'invoice_no'       => $this->input->post('invoice_no'),
            'invoice_date'     => $this->input->post('invoice_date'),
            'added_on'         => date("Y-m-d h:i:s"),
            'added_by'         => $user_id
        );

        $this->db->where('id', $record_id);
        $this->db->update('df_design_form_table', $data);


        // [Table 2] Multi Track Machine Update
        $data1 = array(
            'ce_complied'                 => $this->input->post('ce_complied'),
            'ce_complied_remarks'         => $this->input->post('ce_complied_remarks'),
            // 'date_of_po'               => $this->input->post('date_of_po'),
            'date_of_po_remarks'          => $this->input->post('date_of_po_remarks'),
            'penalty_clause'              => $this->input->post('penalty_clause'),
            'penalty_clause_remarks'      => $this->input->post('penalty_clause_remarks'),
            // 'dispatch_date'            => $this->input->post('dispatch_date'),
            'dispatch_date_remarks'       => $this->input->post('dispatch_date_remarks'),
            // 'trial_date'               => $this->input->post('trial_date'),
            'trial_date_remarks'          => $this->input->post('trial_date_remarks'),
            // 'machine_mode_no'          => $this->input->post('machine_mode_no'),
            'machine_mode_no_remarks'     => $this->input->post('machine_mode_no_remarks'),
            // 'machine_type'             => $this->input->post('machine_type'),
            'machine_type_remarks'        => $this->input->post('machine_type_remarks'),
            'machine_orientation'         => $this->input->post('machine_orientation'),
            'machine_orientation_remarks' => $this->input->post('machine_orientation_remarks'),
            'added_on'                    => date("Y-m-d h:i:s"),
            'added_by'                    => $user_id
        );

        $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine', $data1);


        // [Table 3] Pouch Size & Quantity Remarks
        $data2 = array(
            'pouch_size_remarks'     => $this->input->post('pouch_size_remarks'),
            'quantity_packed_remarks' => $this->input->post('quantity_packed_remarks'),
            'added_on'               => date("Y-m-d h:i:s"),
            'added_by'               => $user_id
        );

        $size_quantity_exists = $this->db
            ->select('id')
            ->from('df_form_machine_specification_size_qnty')
            ->where('record_id', $record_id)
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        if ($size_quantity_exists) {
            $this->db->where('record_id', $record_id);
            $this->db->update('df_form_machine_specification_size_qnty', $data2);
        } else {
            $data2['record_id'] = $record_id;
            $this->db->insert('df_form_machine_specification_size_qnty', $data2);
        }

        // [Table 4] Machine Specification Update
        $data3 = array(
            // 'tracks' => $this->input->post('tracks'),
            // 'product_packed' => $this->input->post('product_packed'),
            // 'powder_option' => $this->input->post('powder_option'),
            // ... (other commented fields)
            'tracks_remarks'                => $this->input->post('tracks_remarks'),
            'product_packed_remarks'        => $this->input->post('product_packed_remarks'),
            'filling_unit_remarks'          => $this->input->post('filling_unit_remarks'),
            // 'density'                    => $this->input->post('density'),
            // 'viscosity'                  => $this->input->post('viscosity'),
            'product_specification_remarks' => $this->input->post('product_specification_remarks'),
            'profle_sealing_remarks'        => $this->input->post('profle_sealing_remarks'),
            'added_on'                      => date("Y-m-d h:i:s"),
            'added_by'                      => $user_id
        );

        $this->db->where('record_id', $record_id);
        $this->db->update('df_form_machine_specification', $data3);


        // [Table 4] Machine 1 Update
        $data4 = array(
            'notching_option'               => $this->input->post('notching_option'),
            'notching_option_remarks'       => $this->input->post('notching_option_remarks'),
            'hooper_details'                => $this->input->post('hooper_details'),
            'openable_option'               => $this->input->post('openable_option'),
            'closed_option'                 => $this->input->post('closed_option'),
            'pressurised_option'            => $this->input->post('pressurised_option'),
            'closed_pressurised_option'     => $this->input->post('closed_pressurised_option'),
            'non_pressurised_option'        => $this->input->post('non_pressurised_option'),
            'non_closed_pressurised_option' => $this->input->post('non_closed_pressurised_option'),
            'hooper_details_remarks'        => $this->input->post('hooper_details_remarks'),
            'cladding_provision'            => $this->input->post('cladding_provision'),
            'cladding_provision_remarks'    => $this->input->post('cladding_provision_remarks'),
            'embossing'                     => $this->input->post('embossing'),
            'embossing_option'              => $this->input->post('embossing_option'),
            'linear_option'                 => $this->input->post('linear_option'),
            'rotary_option'                 => $this->input->post('rotary_option'),
            'provision_remarks'             => $this->input->post('provision_remarks'),
            'web_aligner'                   => $this->input->post('web_aligner'),
            'web_aligner_option'            => $this->input->post('web_aligner_option'),
            'web_aligner_remarks'           => $this->input->post('web_aligner_remarks'),
            'added_on'                      => date("Y-m-d h:i:s"),
            'added_by'                      => $user_id
        );

        $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine1', $data4);


        // [Table 5] Machine 2 Update
        $data5 = array(
            'center_slitting'               => $this->input->post('center_slitting'),
            'center_slitting_remarks'       => $this->input->post('center_slitting_remarks'),
            'vertical_slitting'             => $this->input->post('vertical_slitting'),
            'vertical_slitting_remarks'     => $this->input->post('vertical_slitting_remarks'),
            'vertical_sealer'               => $this->input->post('vertical_sealer'),
            'vertical_sealer_remarks'       => $this->input->post('vertical_sealer_remarks'),
            'laminate_pulling'              => $this->input->post('laminate_pulling'),
            'laminate_pulling_remarks'      => $this->input->post('laminate_pulling_remarks'),
            'up_down'                       => $this->input->post('up_down'),
            'up_down_remarks'               => $this->input->post('up_down_remarks'),
            'embossing_coding'              => $this->input->post('embossing_coding'),
            'embossing_coding_remarks'      => $this->input->post('embossing_coding_remarks'),
            'cooling_station'               => $this->input->post('cooling_station'),
            'cooling_station_remarks'       => $this->input->post('cooling_station_remarks'),
            'horizontal_sealer'             => $this->input->post('horizontal_sealer'),
            'horizontal_sealer_remarks'     => $this->input->post('horizontal_sealer_remarks'),
            'perforation_blade'             => $this->input->post('perforation_blade'),
            'perforation_blade_remarks'     => $this->input->post('perforation_blade_remarks'),
            'priston_drive'                 => $this->input->post('priston_drive'),
            'priston_drive_remarks'         => $this->input->post('priston_drive_remarks'),
            
            // 'individual_option'          => $this->input->post('individual_option'),
            // 'rotary_option1'             => $this->input->post('rotary_option1'),
            
            'shutt_off_nozzle'              => $this->input->post('shutt_off_nozzle'),
            'shutt_off_nozzle_remarks'      => $this->input->post('shutt_off_nozzle_remarks'),
            'filling_plate_drive'           => $this->input->post('filling_plate_drive'),
            'filling_plate_drive_remarks'   => $this->input->post('filling_plate_drive_remarks'),
            'individual_weight'             => $this->input->post('individual_weight'),
            'individual_weight_remarks'     => $this->input->post('individual_weight_remarks'),
            'overall_weight_adjust'         => $this->input->post('overall_weight_adjust'),
            'overall_weight_adjust_remarks' => $this->input->post('overall_weight_adjust_remarks'),
            // 'vertical_sealer_width'      => $this->input->post('vertical_sealer_width'),
            // 'horizontal_sealer_width'    => $this->input->post('horizontal_sealer_width'),
            'added_on'                      => date("Y-m-d h:i:s"),
            'added_by'                      => $user_id
        );

        $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine2', $data5);


        // [Table 6] Machine 3 Update
        $data6 = array(
            'traverse_drive'            => $this->input->post('traverse_drive'),
            'yes_traverse_drive'        => $this->input->post('yes_traverse_drive'),
            'traverse_drive_remarks'    => $this->input->post('traverse_drive_remarks'),
            'printer_yes_no'            => $this->input->post('printer_yes_no'),
            'printer'                   => $this->input->post('printer'),
            'inkjet_option'             => $this->input->post('inkjet_option'),
            'tto_option'                => $this->input->post('tto_option'),
            'thermal_inkjet_option'     => $this->input->post('thermal_inkjet_option'),
            'printer_remarks'           => $this->input->post('printer_remarks'),
            'case_packer_drive'         => $this->input->post('case_packer_drive'),
            'case_packer_drive_remarks' => $this->input->post('case_packer_drive_remarks'),
            'nozzle_funnel'             => $this->input->post('nozzle_funnel'),
            'powder_option1'            => $this->input->post('powder_option1'),
            'liquid_option1'            => $this->input->post('liquid_option1'),
            'liquid_shut_option1'       => $this->input->post('liquid_shut_option1'),
            'nozzle_funnel_remarks'     => $this->input->post('nozzle_funnel_remarks'),
            'hose_pipe'                 => $this->input->post('hose_pipe'),
            'hose_pipe_remarks'         => $this->input->post('hose_pipe_remarks'),
            // 'batch_cut_format'       => $this->input->post('batch_cut_format'),
            'string_option'             => $this->input->post('string_option'),
            'batch_cut_format_remarks'  => $this->input->post('batch_cut_format_remarks'),
            'added_on'                  => date("Y-m-d h:i:s"),
            'added_by'                  => $user_id
        );

        $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine3', $data6);


        // [Table 7] Machine 4 Update
        $data7 = array(
            // 'horizontal_sealer1'          => $this->input->post('horizontal_sealer1'),
            'horizontal_sealer1_remarks'     => $this->input->post('horizontal_sealer1_remarks'),
            // 'vertical_sealer1'            => $this->input->post('vertical_sealer1'),
            'vertical_sealer1_remarks'       => $this->input->post('vertical_sealer1_remarks'),
            'rotary_valve_coating'           => $this->input->post('rotary_valve_coating'),
            'rotary_valve_coating_remarks'   => $this->input->post('rotary_valve_coating_remarks'),
            'working_speed'                  => $this->input->post('working_speed'),
            'working_speed_remarks'          => $this->input->post('working_speed_remarks'),
            'reel_shaft_type'                => $this->input->post('reel_shaft_type'),
            'reel_shaft_type_remarks'        => $this->input->post('reel_shaft_type_remarks'),
            'reel_core_diameter'             => $this->input->post('reel_core_diameter'),
            'reel_core_diameter_remarks'     => $this->input->post('reel_core_diameter_remarks'),
            'trial_material'                 => $this->input->post('trial_material'),
            'trial_material_remarks'         => $this->input->post('trial_material_remarks'),
            'laminate_detail'                => $this->input->post('laminate_detail'),
            'laminate_detail_remarks'        => $this->input->post('laminate_detail_remarks'),
            'heater_control_system'          => $this->input->post('heater_control_system'),
            'heater_control_system_remarks'  => $this->input->post('heater_control_system_remarks'),
            'beacon_light'                   => $this->input->post('beacon_light'),
            'beacon_light_remarks'           => $this->input->post('beacon_light_remarks'),
            'hooper_level'                   => $this->input->post('hooper_level'),
            'hooper_level_option'            => $this->input->post('hooper_level_option'),
            'hooper_level_remarks'           => $this->input->post('hooper_level_remarks'),
            'safety_relay'                   => $this->input->post('safety_relay'),
            'safety_relay_remarks'           => $this->input->post('safety_relay_remarks'),
            // 'supply_voltage'              => $this->input->post('supply_voltage'),
            'supply_voltage_remarks'         => $this->input->post('supply_voltage_remarks'),
            'hmi_size'                       => $this->input->post('hmi_size'),
            'hmi_size_remarks'               => $this->input->post('hmi_size_remarks'),
            'plc_maker'                      => $this->input->post('plc_maker'),
            'plc_maker_remarks'              => $this->input->post('plc_maker_remarks'),
            'cip_system'                     => $this->input->post('cip_system'),
            'cip_system_option'              => $this->input->post('cip_system_option'),
            'cip_system_option_remarks'      => $this->input->post('cip_system_option_remarks'),
            'tool_kit'                       => $this->input->post('tool_kit'),
            'tool_kit_remarks'               => $this->input->post('tool_kit_remarks'),
            'changeover_part'                => $this->input->post('changeover_part'),
            'changeover_part_remarks'        => $this->input->post('changeover_part_remarks'),
            'added_on'                       => date("Y-m-d h:i:s"),
            'added_by'                       => $user_id
        );

        $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine4', $data7);


        // [Table 8] Machine 5 Update
        $data8 = array(
            'secondary_pack'           => $this->input->post('secondary_pack'),
            // 'yes_secondary_pack'    => handled in loop
            'case_packer'              => $this->input->post('case_packer'),
            'secondary_pack_remarks'   => $this->input->post('secondary_pack_remarks'),
            'ladder_platform'          => $this->input->post('ladder_platform'),
            'ladder_platform_remarks'  => $this->input->post('ladder_platform_remarks'),
            'machine_guarding'         => $this->input->post('machine_guarding'),
            'aluminium_option'         => $this->input->post('aluminium_option'),
            'ss_304_option'            => $this->input->post('ss_304_option'),
            'machine_guarding_remarks' => $this->input->post('machine_guarding_remarks'),
            // 'trial_comments'        => $this->input->post('trial_comments'),
            'special_notes'            => $this->input->post('special_notes'),
            'added_on'                 => date("Y-m-d h:i:s"),
            'added_by'                 => $user_id
        );

        $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine5', $data8);


        // [Update Existing Secondary Packs]
        $idd = $this->input->post('multi_track_machine_id');

        if (!empty($idd)) {
            for ($y = 0; $y < count($idd); $y++) {
                $miid = $idd[$y];
                $yes_secondary_packed = $this->input->post('yes_secondary_pack' . $miid);
                $qtyy = $this->input->post('yes_secondary_pack_qty' . $miid);
				 $yes_secondary_pack_remark = $this->input->post('yes_secondary_pack_remark' . $miid);

                $da = array(
                    'yes_secondary_pack'     => $yes_secondary_packed,
                    'yes_secondary_pack_qty' => $qtyy,
					 'yes_secondary_pack_remark' => $yes_secondary_pack_remark
                );

                $this->db->where('id', $miid);
                $this->db->update('df_form_multi_track_machine_pack', $da);
            }
        }


        // [Add NEW Secondary Packs]
        if ($this->input->post('add_more_pack') == 1) {
            $yes_secondary_pack = $this->input->post('yes_secondary_pack_add');
            $qty = $this->input->post('yes_secondary_pack_qty_add');

            if (!empty($yes_secondary_pack)) {
                // Ensure arrays are valid
                if (!is_array($yes_secondary_pack)) $yes_secondary_pack = [$yes_secondary_pack];
                if (!is_array($qty)) $qty = [$qty];

                for ($i = 0; $i < count($yes_secondary_pack); $i++) {
                    if (empty($yes_secondary_pack[$i])) continue; // Skip empty

                    $pack = array(
                        'record_id'              => $record_id,
                        'yes_secondary_pack'     => $yes_secondary_pack[$i],
                        'yes_secondary_pack_qty' => $qty[$i] ?? '',
                        'added_on'               => date("Y-m-d h:i:s"),
                        'added_by'               => $user_id
                    );

                    $this->db->insert('df_form_multi_track_machine_pack', $pack);
                }
            }
        }

        // [Optional: Special Notes List] (Kept commented as per original)
        /*
        $part_desc = $this->input->post('special_notes_list');
        $part_qty = $this->input->post('special_notes_qty');

        if (!empty($part_desc)) {
            for ($i = 0; $i < count($part_desc); $i++) {
                $data9 = array(
                    'special_notes_list' => $part_desc[$i],
                    'special_notes_qty' => $part_qty[$i],
                    'added_on' => date("Y-m-d h:i:s"),
                    'added_by' => $user_id
                );
                $this->db->where('record_id', $record_id);
                $this->db->update('df_form_multi_track_machine6', $data9);
            }
        }
        */

        // [Task Logic: Conditional Updates]
        if ($this->uri->segment(6) != '' && $this->uri->segment(6) > 0) {

            $taskupdatedata = array(
                'task_completed_on' => date('Y-m-d h:i:s'),
                'task_completed_by' => $user_id,
                'task_status'       => 1
            );

            $this->db->where('id', $this->uri->segment(6));
            $this->db->update('task_department_wise_scheduling', $taskupdatedata);

            // Fetch PO ID from the scheduling table
            $qqq = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('id', $this->uri->segment(6))->get();

            if ($qqq->num_rows() > 0) {
                foreach ($qqq->result() as $porecords);
                $ponos = $porecords->po_id;

                $tomorrow = date('Y-m-d', strtotime('+1 day'));
                $nextWorkingDate = $this->iftomorrowisholiday($tomorrow);

                $addnewdata = array(
                    'df_id'         => 0,
                    'taskid'        => 87,
                    'department_id' => 9,
                    'start_date'    => date('Y-m-d'),
                    'end_date'      => date('Y-m-d', strtotime($nextWorkingDate)),
                    'added_on'      => date('Y-m-d H:i:s'),
                    'added_by'      => $user_id,
                    'po_id'         => $ponos,
                    'task_status'   => 0,
                    'userid'        => $user_id,
                    'assigned_user' => $user_id,
                    'assigned_by'   => $user_id,
                    'assigned_on'   => date('Y-m-d H:i:s')
                );

                $this->db->insert('task_department_wise_scheduling', $addnewdata);
            }
        }

        // --- 3. COMMIT TRANSACTION & BRIDGE LOGIC ---
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">Error updating data. Please try again.</div>');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('success', '<div class="alert alert-success"> Thank You! Data successfully Updated.</div>');

            // --- REDIRECTION LOGIC (Open PDF + Redirect) ---
            
            // URL to generate PDF (using the edited record ID)
            $pdf_url = page_url . 'Formats/df_form_dompdf/' . $po_id . '/' . $lead_id . '/' . $record_id;
            
            // Redirect URL (Dashboard)
            $redirect_url = page_url . 'Dashboard';

            $data_view = [
                'pdf_url'      => $pdf_url,
                'redirect_url' => $redirect_url,
            ];
            
            $this->load->view('dashboard/df_submit_bridge', $data_view);
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('error', '<div class="alert alert-danger">System Error: ' . $e->getMessage() . '</div>');
        redirect($_SERVER['HTTP_REFERER']);
    }
}

	function iftomorrowisholiday($date){
    while (true) {
        $q = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date', $date)->get();

        if ($q->num_rows() > 0) {
            // If it's a holiday, move to the next day
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        } else {
            // Found a working day
            break;
        }
    }
    return $date;
}


	//---------------------------------------------------------------------------------


	public function secondary_pack_delete(){
		   $id = $this->input->post('id');  // Retrieve the ID from POST data
        
        if (!empty($id) && is_numeric($id)) {
            // Perform the deletion query directly
            $this->db->where('id', $id);  // Assuming 'id' is the primary key
            $delete = $this->db->delete('basci_machine_df_form_multi_track_machine_pack');  // Delete the record

            if ($delete) {
                echo 'success';  // Return success if deletion is successful
            } else {
                echo 'error';  // Return error if deletion failed
            }
        } else {
            echo 'error';  // Return error if no valid ID is provided
        }
	}


	public function delete_pouch_entry()
{
    $id = $this->input->post('id');

    if ($id) {
        $this->db->where('id', $id);
        if ($this->db->delete('basic_machine_df_form_machine_specification_size_qnty')) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'invalid';
    }
}

public function delete_qty_entry()
{
    $id = $this->input->post('id');

    if ($id) {
        $this->db->where('id', $id);
        if ($this->db->delete('basic_machine_df_form_machine_spec_qty')) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'invalid';
    }
}

	public function mark_notification_shown($id) {
    
    $this->reportingdata->mark_as_shown($id);
    echo json_encode(['status' => 'success']);
}



public function df_form_600(){

			$this->load->model('Task_model');
	$lead_id=$this->uri->segment(5);

    $result = $this->Task_model->getSpecificTaskDates(date('Y-m-d'));


	 $task_103_date = null;
    $task_52_date = null;

    foreach ($result as $task) {
        if ($task['task_id'] == 103) {
            $task_103_date = $task['expected_date'];
        }
        if ($task['task_id'] == 52) {
            $task_52_date = $task['expected_date'];
        }
    }

    // Pass variables to view
    $data['task_103_date'] = $task_103_date;
    $data['task_52_date'] = $task_52_date;
$this->load->view('dashboard/df_project_form', $data);

}

public function powder_df_form_design(){

		$this->load->model('Task_model');
	$lead_id=$this->uri->segment(5);

    $result = $this->Task_model->getSpecificTaskDates(date('Y-m-d'));


	 $task_103_date = null;
    $task_52_date = null;

    foreach ($result as $task) {
        if ($task['task_id'] == 103) {
            $task_103_date = $task['expected_date'];
        }
        if ($task['task_id'] == 52) {
            $task_52_date = $task['expected_date'];
        }
    }

    // Pass variables to view
    $data['task_103_date'] = $task_103_date;
    $data['task_52_date'] = $task_52_date;

$this->load->view('dashboard/powder_df_form_design', $data);

}

// Powder DF Form Add==========================================================================================

public function powder_df_project_form_add(){

		// $this->form_validation->set_rules('design_form_name', 'Design Form', 'required|trim');
		$this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');
		$norml_id=$this->uri->segment(3);
		$po_id = $this->uri->segment(4);
		$lead_id = $this->uri->segment(5);
			if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/df_project_form');
		}else{

			date_default_timezone_set("Asia/Kolkata");

			$data =array(
				'po_id'=>$po_id,
				'lead_id'=>$lead_id,
				// 'design_form_name'=>$this->input->post('design_form_name'),
				'design_form_date'=>$this->input->post('design_form_date'),
				'reference_no'=>$this->input->post('reference_no'),
				'ref_df_date'=>$this->input->post('ref_df_date'),
				// 'iom_no'=>$this->input->post('iom_no'),
				// 'invoice_no'=>$this->input->post('invoice_no'),
				// 'invoice_date'=>$this->input->post('invoice_date'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);



			$this->db->insert('df_design_form_table', $data);

			$latest_id =$this->db->insert_id();

			$data1 = array(

				'record_id'=>$latest_id,
				'ce_complied'=>$this->input->post('ce_complied'),
				'ce_complied_remarks'=>$this->input->post('ce_complied_remarks'),
				'bom_no'=>$this->input->post('bom_no'),
				'bom_no_remarks'=>$this->input->post('bom_no_remarks'),
				// 'date_of_po'=>$this->input->post('date_of_po'),
				'date_of_po_remarks'=>$this->input->post('date_of_po_remarks'),
				'penalty_clause'=>$this->input->post('penalty_clause'),
				'penalty_clause_remarks'=>$this->input->post('penalty_clause_remarks'),
				 'dispatch_date'=>date('Y-m-d',strtotime($this->input->post('dispatch_date'))),
				'dispatch_date_remarks'=>$this->input->post('dispatch_date_remarks'),
				 'trial_date'=>date('Y-m-d',strtotime($this->input->post('trial_date'))),
				'trial_date_remarks'=>$this->input->post('trial_date_remarks'),
				// 'machine_mode_no'=>$this->input->post('machine_mode_no'),
				'machine_mode_no_remarks'=>$this->input->post('machine_mode_no_remarks'),
				// 'machine_type'=>$this->input->post('machine_type'),
				'machine_type_remarks'=>$this->input->post('machine_type_remarks'),
				'machine_orientation'=>$this->input->post('machine_orientation'),
				'machine_orientation_remarks'=>$this->input->post('machine_orientation_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_multi_track_machine', $data1);


  		// $pouch_widths = $this->input->post('pouch_width');
        // $pouch_lengths = $this->input->post('pouch_length');
        // $pouch_heights = $this->input->post('pouch_height');
		// $quantity_packed = $this->input->post('quantity_packed');
        // $quantity_units = $this->input->post('quantity_packed_unit');

		//   if (!empty($pouch_widths)) {
        //     for ($i = 0; $i < count($pouch_widths); $i++) {
                $data2 = array(
                    'record_id' => $latest_id,
                    // 'pouch_width' => $pouch_widths[$i],
                    // 'pouch_length' => $pouch_lengths[$i],
                    // 'pouch_height' => $pouch_heights[$i],
                    'pouch_size_remarks' => $this->input->post('pouch_size_remarks'),
					//  'quantity_packed' => $quantity_packed[$i],
                    // 'quantity_packed_unit' => $quantity_units[$i],
                    'quantity_packed_remarks' => $this->input->post('quantity_packed_remarks'),
                    'added_on' => date("Y-m-d h:i:s"),
                    'added_by' => $this->session->userdata['logged_in']['user_id']
                );
                $this->db->insert('df_form_machine_specification_size_qnty', $data2);
        //     }
        // }


			$data3 = array(
				'record_id'=>$latest_id,
				// 'tracks'=>$this->input->post('tracks'),
				// 'product_packed'=>$this->input->post('product_packed'),
				// 'powder_option'=>$this->input->post('powder_option'),
				// 'liquid_option'=>$this->input->post('liquid_option'),
				// 'non_viscous_option'=>$this->input->post('non_viscous_option'),
				// 'viscous_option'=>$this->input->post('viscous_option'),
				// 'piston_filler_option'=>$this->input->post('piston_filler_option'),
				// 'follow_meter_option'=>$this->input->post('follow_meter_option'),
				// 'non_free_flow_option'=>$this->input->post('non_free_flow_option'),
				// 'cup_filler_option'=>$this->input->post('cup_filler_option'),
				// 'free_flow_option'=>$this->input->post('free_flow_option'),
				// 'weigher_system_option'=>$this->input->post('weigher_system_option'),
				// 'liner_weigher_option'=>$this->input->post('liner_weigher_option'),
				// 'mult_head_weigher_option'=>$this->input->post('mult_head_weigher_option'),
				// 'volumetric_cap_option'=>$this->input->post('volumetric_cap_option'),
					'profile_of_sealing'=>$this->input->post('profile_of_sealing'),
				'tracks_remarks'=>$this->input->post('tracks_remarks'),
				'product_packed_remarks'=>$this->input->post('product_packed_remarks'),
				'filling_unit_remarks'=>$this->input->post('filling_unit_remarks'),
				// 'density'=>$this->input->post('density'),
				// 'viscosity'=>$this->input->post('viscosity'),
				'product_specification_remarks'=>$this->input->post('product_specification_remarks'),
				'profle_sealing_remarks'=>$this->input->post('profle_sealing_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_machine_specification', $data3);

			$data4 = array(
				'record_id'=>$latest_id,
				'notching_option'=>$this->input->post('notching_option'),
				'notching_option_remarks'=>$this->input->post('notching_option_remarks'),
				'hooper_details'=>$this->input->post('hooper_details'),
				'openable_option'=>$this->input->post('openable_option'),
				'closed_option'=>$this->input->post('closed_option'),
				'pressurised_option'=>$this->input->post('pressurised_option'),
				'closed_pressurised_option'=>$this->input->post('closed_pressurised_option'),
				'non_pressurised_option'=>$this->input->post('non_pressurised_option'),
				'non_closed_pressurised_option'=>$this->input->post('non_closed_pressurised_option'),
				'hooper_details_remarks'=>$this->input->post('hooper_details_remarks'),
				'cladding_provision'=>$this->input->post('cladding_provision'),
				'cladding_provision_remarks'=>$this->input->post('cladding_provision_remarks'),
				'embossing'=>$this->input->post('embossing'),
				'embossing_option'=>$this->input->post('embossing_option'),
				'linear_option'=>$this->input->post('linear_option'),
				'rotary_option'=>$this->input->post('rotary_option'),
				'provision_remarks'=>$this->input->post('provision_remarks'),
				'web_aligner'=>$this->input->post('web_aligner'),
				'web_aligner_option'=>$this->input->post('web_aligner_option'),
				'web_aligner_remarks'=>$this->input->post('web_aligner_remarks'),
				
				

				'unwind_main_motor'=>$this->input->post('unwind_main_motor'),
				'unwind_main_motor_remarks'=>$this->input->post('unwind_main_motor_remarks'),
				'unwind_dancing_roll'=>$this->input->post('unwind_dancing_roll'),
				'unwind_dancing_roll_remarks'=>$this->input->post('unwind_dancing_roll_remarks'),
				'rotary_plain_cut_assembly'=>$this->input->post('rotary_plain_cut_assembly'),
				'rotary_plain_cut_assembly_remarks'=>$this->input->post('rotary_plain_cut_assembly_remarks'),
				'auger_drive'=>$this->input->post('auger_drive'),
				'auger_drive_remarks'=>$this->input->post('auger_drive_remarks'),
				'static_charge_eliminator'=>$this->input->post('static_charge_eliminator'),
				'static_charge_eliminator_remarks'=>$this->input->post('static_charge_eliminator_remarks'),
				'kld_clearance'=>$this->input->post('kld_clearance'),
				'kld_clearance_remarks'=>$this->input->post('kld_clearance_remarks'),
				'heater_ssr_fault'=>$this->input->post('heater_ssr_fault'),
				'heater_ssr_fault_remarks'=>$this->input->post('heater_ssr_fault_remarks'),
				'pin_hole_assembly'=>$this->input->post('pin_hole_assembly'),
				'pin_hole_assembly_remarks'=>$this->input->post('pin_hole_assembly_remarks'),
				'nitrogen_purging'=>$this->input->post('nitrogen_purging'),
				'nitrogen_purging_remarks'=>$this->input->post('nitrogen_purging_remarks'),
				'bagging_unit'=>$this->input->post('bagging_unit'),
				'bagging_unit_remarks'=>$this->input->post('bagging_unit_remarks'),

				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_multi_track_machine1', $data4);


			$data5 = array(
				'record_id'=>$latest_id,
				'center_slitting'=>$this->input->post('center_slitting'),
				'center_slitting_remarks'=>$this->input->post('center_slitting_remarks'),
				'vertical_slitting'=>$this->input->post('vertical_slitting'),
				'vertical_slitting_remarks'=>$this->input->post('vertical_slitting_remarks'),
				'vertical_sealer'=>$this->input->post('vertical_sealer'),
				'vertical_sealer_remarks'=>$this->input->post('vertical_sealer_remarks'),
				'laminate_pulling'=>$this->input->post('laminate_pulling'),
				'laminate_pulling_remarks'=>$this->input->post('laminate_pulling_remarks'),
				'embossing_coding'=>$this->input->post('embossing_coding'),
				'embossing_coding_remarks'=>$this->input->post('embossing_coding_remarks'),
				'cooling_station'=>$this->input->post('cooling_station'),
				'cooling_station_remarks'=>$this->input->post('cooling_station_remarks'),
				'horizontal_sealer'=>$this->input->post('horizontal_sealer'),
				'horizontal_sealer_remarks'=>$this->input->post('horizontal_sealer_remarks'),
				'perforation_blade'=>$this->input->post('perforation_blade'),
				'perforation_blade_remarks'=>$this->input->post('perforation_blade_remarks'),
				//'priston_drive'=>$this->input->post('priston_drive'),
				//'priston_drive_remarks'=>$this->input->post('priston_drive_remarks'),
				
				//'individual_option'=>$this->input->post('individual_option'),
				//'rotary_option1'=>$this->input->post('rotary_option1'),
			
				//'shutt_off_nozzle'=>$this->input->post('shutt_off_nozzle'),
				//'shutt_off_nozzle_remarks'=>$this->input->post('shutt_off_nozzle_remarks'),
				//'filling_plate_drive'=>$this->input->post('filling_plate_drive'),
				//'filling_plate_drive_remarks'=>$this->input->post('filling_plate_drive_remarks'),
				'individual_weight'=>$this->input->post('individual_weight'),
				'individual_weight_remarks'=>$this->input->post('individual_weight_remarks'),
				'overall_weight_adjust'=>$this->input->post('overall_weight_adjust'),
				'overall_weight_adjust_remarks'=>$this->input->post('overall_weight_adjust_remarks'),
				'vertical_sealer_width'=>$this->input->post('vertical_sealer_width'),
				'horizontal_sealer_width'=>$this->input->post('horizontal_sealer_width'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_multi_track_machine2', $data5);


			$data6 =array(
				'record_id'=>$latest_id,
				'traverse_drive'=>$this->input->post('traverse_drive'),
				'yes_traverse_drive'=>$this->input->post('yes_traverse_drive'),
				'traverse_drive_remarks'=>$this->input->post('traverse_drive_remarks'),
				'printer_yes_no'=>$this->input->post('printer_yes_no'),
				//'printer'=>$this->input->post('printer'),
				//'inkjet_option'=>$this->input->post('inkjet_option'),
				//'tto_option'=>$this->input->post('tto_option'),
				//'thermal_inkjet_option'=>$this->input->post('thermal_inkjet_option'),
				//'printer_remarks'=>$this->input->post('printer_remarks'),
				'case_packer_drive'=>$this->input->post('case_packer_drive'),
				'case_packer_drive_remarks'=>$this->input->post('case_packer_drive_remarks'),
				'nozzle_funnel'=>$this->input->post('nozzle_funnel'),
				'powder_option1'=>$this->input->post('powder_option1'),
				'liquid_option1'=>$this->input->post('liquid_option1'),
				'liquid_shut_option1'=>$this->input->post('liquid_shut_option1'),
				'nozzle_funnel_remarks'=>$this->input->post('nozzle_funnel_remarks'),
				'hose_pipe'=>$this->input->post('hose_pipe'),
				'hose_pipe_remarks'=>$this->input->post('hose_pipe_remarks'),
				// 'batch_cut_format'=>$this->input->post('batch_cut_format'),
				'string_option'=>$this->input->post('string_option'),
				'batch_cut_format_remarks'=>$this->input->post('batch_cut_format_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_multi_track_machine3', $data6);

			$data7 =array(
				'record_id'=>$latest_id,
				// 'horizontal_sealer1'=>$this->input->post('horizontal_sealer1'),
				'horizontal_sealer1_remarks'=>$this->input->post('horizontal_sealer1_remarks'),
				// 'vertical_sealer1'=>$this->input->post('vertical_sealer1'),
				'vertical_sealer1_remarks'=>$this->input->post('vertical_sealer1_remarks'),
				'rotary_valve_coating'=>$this->input->post('rotary_valve_coating'),
				'rotary_valve_coating_remarks'=>$this->input->post('rotary_valve_coating_remarks'),
				'working_speed'=>$this->input->post('working_speed'),
				'working_speed_remarks'=>$this->input->post('working_speed_remarks'),
				'reel_shaft_type'=>$this->input->post('reel_shaft_type'),
				'reel_shaft_type_remarks'=>$this->input->post('reel_shaft_type_remarks'),
				'reel_core_diameter'=>$this->input->post('reel_core_diameter'),
				'reel_core_diameter_remarks'=>$this->input->post('reel_core_diameter_remarks'),
				'trial_material'=>$this->input->post('trial_material'),
				'trial_material_remarks'=>$this->input->post('trial_material_remarks'),
				'laminate_detail'=>$this->input->post('laminate_detail'),
				'laminate_detail_remarks'=>$this->input->post('laminate_detail_remarks'),
				'heater_control_system'=>$this->input->post('heater_control_system'),
				'heater_control_system_remarks'=>$this->input->post('heater_control_system_remarks'),
				'beacon_light'=>$this->input->post('beacon_light'),
				'beacon_light_remarks'=>$this->input->post('beacon_light_remarks'),
				'hooper_level'=>$this->input->post('hooper_level'),
				'hooper_level_option'=>$this->input->post('hooper_level_option'),
				'hooper_level_remarks'=>$this->input->post('hooper_level_remarks'),
				'safety_relay'=>$this->input->post('safety_relay'),
				'safety_relay_remarks'=>$this->input->post('safety_relay_remarks'),
				'plc_maker'=>$this->input->post('plc_maker'),
				'plc_maker_remarks'=>$this->input->post('plc_maker_remarks'),
				'hmi_size'=>$this->input->post('hmi_size'),
				'hmi_size_remarks'=>$this->input->post('hmi_size_remarks'),
				'tool_kit'=>$this->input->post('tool_kit'),
				'tool_kit_remarks'=>$this->input->post('tool_kit_remarks'),
				'changeover_part'=>$this->input->post('changeover_part'),
				'changeover_part_remarks'=>$this->input->post('changeover_part_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_multi_track_machine4', $data7);

			  

			$data8 =array(
				'record_id'=>$latest_id,
				'secondary_pack'=>$this->input->post('secondary_pack'),
				//'yes_secondary_pack'=>implode(', ', $yes_secondary_pack),
				'case_packer'=>$this->input->post('case_packer'),
				'secondary_pack_remarks'=>$this->input->post('secondary_pack_remarks'),
				'ladder_platform'=>$this->input->post('ladder_platform'),
				'ladder_platform_remarks'=>$this->input->post('ladder_platform_remarks'),
				'machine_guarding'=>$this->input->post('machine_guarding'),
				'aluminium_option'=>$this->input->post('aluminium_option'),
				'ss_304_option'=>$this->input->post('ss_304_option'),
				'machine_guarding_remarks'=>$this->input->post('machine_guarding_remarks'),
				//'trial_comments'=>$this->input->post('trial_comments'),

				'tilting_flaps'=>$this->input->post('tilting_flaps'),
				'tilting_flaps_remarks'=>$this->input->post('tilting_flaps_remarks'),
				'tilting_movement_drive'=>$this->input->post('tilting_movement_drive'),
				'tilting_movement_drive_remarks'=>$this->input->post('tilting_movement_drive_remarks'),
				'vert_hori_movement'=>$this->input->post('vert_hori_movement'),
				'vert_hori_movement_remarks'=>$this->input->post('vert_hori_movement_remarks'),
				// 'perforation_drive'=>$this->input->post('perforation_drive'),
				// 'perforation_drive_remarks'=>$this->input->post('perforation_drive_remarks'),
				'collating_conveyor'=>$this->input->post('collating_conveyor'),
				'collating_conveyor_remarks'=>$this->input->post('collating_conveyor_remarks'),
				'rope_conveyor'=>$this->input->post('rope_conveyor'),
				'rope_conveyor_remarks'=>$this->input->post('rope_conveyor_remarks'),
				'take_off_conveyor'=>$this->input->post('take_off_conveyor'),
				'take_off_conveyor_remarks'=>$this->input->post('take_off_conveyor_remarks'),
				'weighing_conveyor_drive'=>$this->input->post('weighing_conveyor_drive'),
				'weighing_conveyor_drive_remarks'=>$this->input->post('weighing_conveyor_drive_remarks'),


				'special_notes'=>$this->input->post('special_notes'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_multi_track_machine5', $data8);


			 $yes_secondary_pack = $this->input->post('yes_secondary_pack');

			  $qty = $this->input->post('yes_secondary_pack_qty');

			 if(!empty($yes_secondary_pack)){

				for($i = 0; $i<count($yes_secondary_pack); $i++){
$pack =array(
'record_id'=>$latest_id,
	'yes_secondary_pack'=>$yes_secondary_pack[$i],
	'yes_secondary_pack_qty'=>$qty[$i],
	'added_on' => date("Y-m-d h:i:s"),
'added_by' => $this->session->userdata['logged_in']['user_id']
);


$this->db->insert('df_form_multi_track_machine_pack', $pack);
				}

			 }

		// 	$part_desc = $this->input->post('special_notes_list');
		// 	$part_qty = $this->input->post('special_notes_qty');

		// 	  if (!empty($part_desc)) {
        //     for ($i = 0; $i < count($part_desc); $i++) {
        //         $data9 = array(
        //             'record_id' => $latest_id,
        //             'special_notes_list' => $part_desc[$i], 
        //             'special_notes_qty' => $part_qty[$i],
        //             'added_on' => date("Y-m-d h:i:s"),
        //             'added_by' => $this->session->userdata['logged_in']['user_id']
        //         );
        //         $this->db->insert('df_form_multi_track_machine6', $data9);
        //     }
        // }

		$user_id=$_SESSION['logged_in']['user_id'];
			$data34 = array('df_id'=>0,
			'task_status'=>1,
			'task_completed_on'=>date('Y-m-d H:i:s'),
			'task_completed_by'=>$user_id,
			'userid'=>$user_id);

			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('task_department_wise_scheduling',$data34);

			/*Select PO No*/
			$pono = $this->getpono($this->uri->segment(3));
			/*Select PO No*/

			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);
			$data = array('df_id'=>0,
				'taskid'=>86,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$pono,
				'task_status'=>0,
				'remarks'=>'',
				'assigned_user'=>$user_id,
				'userid'=>$user_id,
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$data);


			$this->session->set_flashdata('success','<div class="alert alert-success"> Thank You! Data successfully added.</div>');
		//redirect(page_url.'Formats/df_form/'.$po_id.'/'.$lead_id);
		redirect(page_url.'Formats/powder_df_form/'.$po_id.'/'.$lead_id);
		}

	}


// Powder DF Form Add======================================================================




public function edit_powder_df_form(){

		$this->load->model('Task_model');
    $result = $this->Task_model->getSpecificTaskDates('2025-04-06');

    // Initialize both dates as null
    $task_103_date = null;
    $task_52_date = null;

    foreach ($result as $task) {
        if ($task['task_id'] == 103) {
            $task_103_date = $task['expected_date'];
        }
        if ($task['task_id'] == 52) {
            $task_52_date = $task['expected_date'];
        }
    }

    // Pass variables to view
    $data['task_103_date'] = $task_103_date;
    $data['task_52_date'] = $task_52_date;
	$this->load->view('dashboard/edit_powder_df_form',$data);
}


// Powder DF Form edit==========================================================================================

public function powder_df_project_form_edit(){

		// $this->form_validation->set_rules('design_form_name', 'Design Form', 'required|trim');
		$this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');
		$po_id = $this->uri->segment(3);
		$lead_id = $this->uri->segment(4);
		 $record_id = $this->uri->segment(6);
			if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/df_project_form');
		}else{

			date_default_timezone_set("Asia/Kolkata");

			$data =array(
				'po_id'=>$po_id,
				'lead_id'=>$lead_id,
				// 'design_form_name'=>$this->input->post('design_form_name'),
				'design_form_date'=>$this->input->post('design_form_date'),
				'reference_no'=>$this->input->post('reference_no'),
				'ref_df_date'=>$this->input->post('ref_df_date'),
				// 'iom_no'=>$this->input->post('iom_no'),
				// 'invoice_no'=>$this->input->post('invoice_no'),
				// 'invoice_date'=>$this->input->post('invoice_date'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);



			 $this->db->where('id', $record_id);
        $this->db->update('df_design_form_table', $data);

			$data1 = array(

				'ce_complied'=>$this->input->post('ce_complied'),
				'ce_complied_remarks'=>$this->input->post('ce_complied_remarks'),
				'bom_no'=>$this->input->post('bom_no'),
				'bom_no_remarks'=>$this->input->post('bom_no_remarks'),
				// 'date_of_po'=>$this->input->post('date_of_po'),
				'date_of_po_remarks'=>$this->input->post('date_of_po_remarks'),
				'penalty_clause'=>$this->input->post('penalty_clause'),
				'penalty_clause_remarks'=>$this->input->post('penalty_clause_remarks'),
				 'dispatch_date'=>date('Y-m-d',strtotime($this->input->post('dispatch_date'))),
				'dispatch_date_remarks'=>$this->input->post('dispatch_date_remarks'),
				 'trial_date'=>date('Y-m-d',strtotime($this->input->post('trial_date'))),
				'trial_date_remarks'=>$this->input->post('trial_date_remarks'),
				// 'machine_mode_no'=>$this->input->post('machine_mode_no'),
				'machine_mode_no_remarks'=>$this->input->post('machine_mode_no_remarks'),
				// 'machine_type'=>$this->input->post('machine_type'),
				'machine_type_remarks'=>$this->input->post('machine_type_remarks'),
				'machine_orientation'=>$this->input->post('machine_orientation'),
				'machine_orientation_remarks'=>$this->input->post('machine_orientation_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
        $this->db->update('df_form_multi_track_machine', $data1);


  		// $pouch_widths = $this->input->post('pouch_width');
        // $pouch_lengths = $this->input->post('pouch_length');
        // $pouch_heights = $this->input->post('pouch_height');
		// $quantity_packed = $this->input->post('quantity_packed');
        // $quantity_units = $this->input->post('quantity_packed_unit');

		//   if (!empty($pouch_widths)) {
        //     for ($i = 0; $i < count($pouch_widths); $i++) {
                $pouch_size_remarks = $this->input->post('pouch_size_remarks');
                $quantity_packed_remarks = $this->input->post('quantity_packed_remarks');
                if (strpos((string) $pouch_size_remarks, 'Undefined variable: pouch_size_remarks') !== false) {
                    $pouch_size_remarks = '';
                }
                if (strpos((string) $quantity_packed_remarks, 'Undefined variable: quantity_packed_remarks') !== false) {
                    $quantity_packed_remarks = '';
                }

                $data2 = array(
                    // 'pouch_width' => $pouch_widths[$i],
                    // 'pouch_length' => $pouch_lengths[$i],
                    // 'pouch_height' => $pouch_heights[$i],
                    'pouch_size_remarks' => $pouch_size_remarks,
					//  'quantity_packed' => $quantity_packed[$i],
                    // 'quantity_packed_unit' => $quantity_units[$i],
                    'quantity_packed_remarks' => $quantity_packed_remarks,
                    'added_on' => date("Y-m-d h:i:s"),
                    'added_by' => $this->session->userdata['logged_in']['user_id']
                );
                $size_quantity_exists = $this->db
                    ->select('id')
                    ->from('df_form_machine_specification_size_qnty')
                    ->where('record_id', $record_id)
                    ->limit(1)
                    ->get()
                    ->num_rows() > 0;

                if ($size_quantity_exists) {
                    $this->db->where('record_id', $record_id);
                    $this->db->update('df_form_machine_specification_size_qnty', $data2);
                } else {
                    $data2['record_id'] = $record_id;
                    $this->db->insert('df_form_machine_specification_size_qnty', $data2);
                }
        //     }
        // }


			$data3 = array(
				// 'tracks'=>$this->input->post('tracks'),
				// 'product_packed'=>$this->input->post('product_packed'),
				// 'powder_option'=>$this->input->post('powder_option'),
				// 'liquid_option'=>$this->input->post('liquid_option'),
				// 'non_viscous_option'=>$this->input->post('non_viscous_option'),
				// 'viscous_option'=>$this->input->post('viscous_option'),
				// 'piston_filler_option'=>$this->input->post('piston_filler_option'),
				// 'follow_meter_option'=>$this->input->post('follow_meter_option'),
				// 'non_free_flow_option'=>$this->input->post('non_free_flow_option'),
				// 'cup_filler_option'=>$this->input->post('cup_filler_option'),
				// 'free_flow_option'=>$this->input->post('free_flow_option'),
				// 'weigher_system_option'=>$this->input->post('weigher_system_option'),
				// 'liner_weigher_option'=>$this->input->post('liner_weigher_option'),
				// 'mult_head_weigher_option'=>$this->input->post('mult_head_weigher_option'),
				// 'volumetric_cap_option'=>$this->input->post('volumetric_cap_option'),
				'profile_of_sealing'=>$this->input->post('profile_of_sealing'),
				'tracks_remarks'=>$this->input->post('tracks_remarks'),
				'product_packed_remarks'=>$this->input->post('product_packed_remarks'),
				'filling_unit_remarks'=>$this->input->post('filling_unit_remarks'),
				// 'density'=>$this->input->post('density'),
				// 'viscosity'=>$this->input->post('viscosity'),
				'product_specification_remarks'=>$this->input->post('product_specification_remarks'),
				'profle_sealing_remarks'=>$this->input->post('profle_sealing_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
        $this->db->update('df_form_machine_specification', $data3);


			$data4 = array(
				'notching_option'=>$this->input->post('notching_option'),
				'notching_option_remarks'=>$this->input->post('notching_option_remarks'),
				'hooper_details'=>$this->input->post('hooper_details'),
				'openable_option'=>$this->input->post('openable_option'),
				'closed_option'=>$this->input->post('closed_option'),
				'pressurised_option'=>$this->input->post('pressurised_option'),
				'closed_pressurised_option'=>$this->input->post('closed_pressurised_option'),
				'non_pressurised_option'=>$this->input->post('non_pressurised_option'),
				'non_closed_pressurised_option'=>$this->input->post('non_closed_pressurised_option'),
				'hooper_details_remarks'=>$this->input->post('hooper_details_remarks'),
				'cladding_provision'=>$this->input->post('cladding_provision'),
				'cladding_provision_remarks'=>$this->input->post('cladding_provision_remarks'),
				'embossing'=>$this->input->post('embossing'),
				'embossing_option'=>$this->input->post('embossing_option'),
				'linear_option'=>$this->input->post('linear_option'),
				'rotary_option'=>$this->input->post('rotary_option'),
				'provision_remarks'=>$this->input->post('provision_remarks'),
				'web_aligner'=>$this->input->post('web_aligner'),
				'web_aligner_option'=>$this->input->post('web_aligner_option'),
				'web_aligner_remarks'=>$this->input->post('web_aligner_remarks'),
				'unwind_main_motor'=>$this->input->post('unwind_main_motor'),
				'unwind_main_motor_remarks'=>$this->input->post('unwind_main_motor_remarks'),
				'unwind_dancing_roll'=>$this->input->post('unwind_dancing_roll'),
				'unwind_dancing_roll_remarks'=>$this->input->post('unwind_dancing_roll_remarks'),
				'rotary_plain_cut_assembly'=>$this->input->post('rotary_plain_cut_assembly'),
				'rotary_plain_cut_assembly_remarks'=>$this->input->post('rotary_plain_cut_assembly_remarks'),
				'auger_drive'=>$this->input->post('auger_drive'),
				'auger_drive_remarks'=>$this->input->post('auger_drive_remarks'),
				'static_charge_eliminator'=>$this->input->post('static_charge_eliminator'),
				'static_charge_eliminator_remarks'=>$this->input->post('static_charge_eliminator_remarks'),
				'kld_clearance'=>$this->input->post('kld_clearance'),
				'kld_clearance_remarks'=>$this->input->post('kld_clearance_remarks'),
				'heater_ssr_fault'=>$this->input->post('heater_ssr_fault'),
				'heater_ssr_fault_remarks'=>$this->input->post('heater_ssr_fault_remarks'),
				'pin_hole_assembly'=>$this->input->post('pin_hole_assembly'),
				'pin_hole_assembly_remarks'=>$this->input->post('pin_hole_assembly_remarks'),
				'nitrogen_purging'=>$this->input->post('nitrogen_purging'),
				'nitrogen_purging_remarks'=>$this->input->post('nitrogen_purging_remarks'),
				'bagging_unit'=>$this->input->post('bagging_unit'),
				'bagging_unit_remarks'=>$this->input->post('bagging_unit_remarks'),

				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

		 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine1', $data4);


			$data5 = array(
				
				'center_slitting'=>$this->input->post('center_slitting'),
				'center_slitting_remarks'=>$this->input->post('center_slitting_remarks'),
				'vertical_slitting'=>$this->input->post('vertical_slitting'),
				'vertical_slitting_remarks'=>$this->input->post('vertical_slitting_remarks'),
				'vertical_sealer'=>$this->input->post('vertical_sealer'),
				'vertical_sealer_remarks'=>$this->input->post('vertical_sealer_remarks'),
				'laminate_pulling'=>$this->input->post('laminate_pulling'),
				'laminate_pulling_remarks'=>$this->input->post('laminate_pulling_remarks'),
				'embossing_coding'=>$this->input->post('embossing_coding'),
				'embossing_coding_remarks'=>$this->input->post('embossing_coding_remarks'),
				'cooling_station'=>$this->input->post('cooling_station'),
				'cooling_station_remarks'=>$this->input->post('cooling_station_remarks'),
				'horizontal_sealer'=>$this->input->post('horizontal_sealer'),
				'horizontal_sealer_remarks'=>$this->input->post('horizontal_sealer_remarks'),
				'perforation_blade'=>$this->input->post('perforation_blade'),
				'perforation_blade_remarks'=>$this->input->post('perforation_blade_remarks'),
				//'priston_drive'=>$this->input->post('priston_drive'),
				//'priston_drive_remarks'=>$this->input->post('priston_drive_remarks'),
				
				//'individual_option'=>$this->input->post('individual_option'),
				//'rotary_option1'=>$this->input->post('rotary_option1'),
			
				//'shutt_off_nozzle'=>$this->input->post('shutt_off_nozzle'),
				//'shutt_off_nozzle_remarks'=>$this->input->post('shutt_off_nozzle_remarks'),
				//'filling_plate_drive'=>$this->input->post('filling_plate_drive'),
				//'filling_plate_drive_remarks'=>$this->input->post('filling_plate_drive_remarks'),
				'individual_weight'=>$this->input->post('individual_weight'),
				'individual_weight_remarks'=>$this->input->post('individual_weight_remarks'),
				'overall_weight_adjust'=>$this->input->post('overall_weight_adjust'),
				'overall_weight_adjust_remarks'=>$this->input->post('overall_weight_adjust_remarks'),
				'vertical_sealer_width'=>$this->input->post('vertical_sealer_width'),
				'horizontal_sealer_width'=>$this->input->post('horizontal_sealer_width'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine2', $data5);


			$data6 =array(
				
				'traverse_drive'=>$this->input->post('traverse_drive'),
				'yes_traverse_drive'=>$this->input->post('yes_traverse_drive'),
				'traverse_drive_remarks'=>$this->input->post('traverse_drive_remarks'),
				'printer_yes_no'=>$this->input->post('printer_yes_no'),
				//'printer'=>$this->input->post('printer'),
				//'inkjet_option'=>$this->input->post('inkjet_option'),
				//'tto_option'=>$this->input->post('tto_option'),
				//'thermal_inkjet_option'=>$this->input->post('thermal_inkjet_option'),
				//'printer_remarks'=>$this->input->post('printer_remarks'),
				'case_packer_drive'=>$this->input->post('case_packer_drive'),
				'case_packer_drive_remarks'=>$this->input->post('case_packer_drive_remarks'),
				'nozzle_funnel'=>$this->input->post('nozzle_funnel'),
				'powder_option1'=>$this->input->post('powder_option1'),
				'liquid_option1'=>$this->input->post('liquid_option1'),
				'liquid_shut_option1'=>$this->input->post('liquid_shut_option1'),
				'nozzle_funnel_remarks'=>$this->input->post('nozzle_funnel_remarks'),
				'hose_pipe'=>$this->input->post('hose_pipe'),
				'hose_pipe_remarks'=>$this->input->post('hose_pipe_remarks'),
				// 'batch_cut_format'=>$this->input->post('batch_cut_format'),
				'string_option'=>$this->input->post('string_option'),
				'batch_cut_format_remarks'=>$this->input->post('batch_cut_format_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine3', $data6);

			$data7 =array(
				
				// 'horizontal_sealer1'=>$this->input->post('horizontal_sealer1'),
				'horizontal_sealer1_remarks'=>$this->input->post('horizontal_sealer1_remarks'),
				// 'vertical_sealer1'=>$this->input->post('vertical_sealer1'),
				'vertical_sealer1_remarks'=>$this->input->post('vertical_sealer1_remarks'),
				'rotary_valve_coating'=>$this->input->post('rotary_valve_coating'),
				'rotary_valve_coating_remarks'=>$this->input->post('rotary_valve_coating_remarks'),
				'working_speed'=>$this->input->post('working_speed'),
				'working_speed_remarks'=>$this->input->post('working_speed_remarks'),
				'reel_shaft_type'=>$this->input->post('reel_shaft_type'),
				'reel_shaft_type_remarks'=>$this->input->post('reel_shaft_type_remarks'),
				'reel_core_diameter'=>$this->input->post('reel_core_diameter'),
				'reel_core_diameter_remarks'=>$this->input->post('reel_core_diameter_remarks'),
				'trial_material'=>$this->input->post('trial_material'),
				'trial_material_remarks'=>$this->input->post('trial_material_remarks'),
				'laminate_detail'=>$this->input->post('laminate_detail'),
				'laminate_detail_remarks'=>$this->input->post('laminate_detail_remarks'),
				'heater_control_system'=>$this->input->post('heater_control_system'),
				'heater_control_system_remarks'=>$this->input->post('heater_control_system_remarks'),
				'beacon_light'=>$this->input->post('beacon_light'),
				'beacon_light_remarks'=>$this->input->post('beacon_light_remarks'),
				'hooper_level'=>$this->input->post('hooper_level'),
				'hooper_level_option'=>$this->input->post('hooper_level_option'),
				'hooper_level_remarks'=>$this->input->post('hooper_level_remarks'),
				'safety_relay'=>$this->input->post('safety_relay'),
				'safety_relay_remarks'=>$this->input->post('safety_relay_remarks'),
				'plc_maker'=>$this->input->post('plc_maker'),
				'plc_maker_remarks'=>$this->input->post('plc_maker_remarks'),
				'hmi_size'=>$this->input->post('hmi_size'),
				'hmi_size_remarks'=>$this->input->post('hmi_size_remarks'),
				'tool_kit'=>$this->input->post('tool_kit'),
				'tool_kit_remarks'=>$this->input->post('tool_kit_remarks'),
				'changeover_part'=>$this->input->post('changeover_part'),
				'changeover_part_remarks'=>$this->input->post('changeover_part_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine4', $data7);

			  

			$data8 =array(
				
				'secondary_pack'=>$this->input->post('secondary_pack'),
				//'yes_secondary_pack'=>implode(', ', $yes_secondary_pack),
				'case_packer'=>$this->input->post('case_packer'),
				'secondary_pack_remarks'=>$this->input->post('secondary_pack_remarks'),
				'ladder_platform'=>$this->input->post('ladder_platform'),
				'ladder_platform_remarks'=>$this->input->post('ladder_platform_remarks'),
				'machine_guarding'=>$this->input->post('machine_guarding'),
				'aluminium_option'=>$this->input->post('aluminium_option'),
				'ss_304_option'=>$this->input->post('ss_304_option'),
				'machine_guarding_remarks'=>$this->input->post('machine_guarding_remarks'),
				//'trial_comments'=>$this->input->post('trial_comments'),


				'tilting_flaps'=>$this->input->post('tilting_flaps'),
				'tilting_flaps_remarks'=>$this->input->post('tilting_flaps_remarks'),
				'tilting_movement_drive'=>$this->input->post('tilting_movement_drive'),
				'tilting_movement_drive_remarks'=>$this->input->post('tilting_movement_drive_remarks'),
				'vert_hori_movement'=>$this->input->post('vert_hori_movement'),
				'vert_hori_movement_remarks'=>$this->input->post('vert_hori_movement_remarks'),
				// 'perforation_drive'=>$this->input->post('perforation_drive'),
				// 'perforation_drive_remarks'=>$this->input->post('perforation_drive_remarks'),
				'collating_conveyor'=>$this->input->post('collating_conveyor'),
				'collating_conveyor_remarks'=>$this->input->post('collating_conveyor_remarks'),
				'rope_conveyor'=>$this->input->post('rope_conveyor'),
				'rope_conveyor_remarks'=>$this->input->post('rope_conveyor_remarks'),
				'take_off_conveyor'=>$this->input->post('take_off_conveyor'),
				'take_off_conveyor_remarks'=>$this->input->post('take_off_conveyor_remarks'),
				'weighing_conveyor_drive'=>$this->input->post('weighing_conveyor_drive'),
				'weighing_conveyor_drive_remarks'=>$this->input->post('weighing_conveyor_drive_remarks'),

				'special_notes'=>$this->input->post('special_notes'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

		 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_multi_track_machine5', $data8);


			 $idd=	$this->input->post('multi_track_machine_id');

		for($y=0; $y<count($idd); $y++){
			$miid =$idd[$y]; 
			$yes_secondary_packed = $this->input->post('yes_secondary_pack'.$miid);
			$qtyy = $this->input->post('yes_secondary_pack_qty'.$miid);

			$da =array(

				'yes_secondary_pack'=>$yes_secondary_packed,

				'yes_secondary_pack_qty'=>$qtyy,

			);

			$this->db->where('id',$miid);
			$this->db->update('df_form_multi_track_machine_pack', $da);
		}
			 

// Add NEW SECONDARY PACK


					if($this->input->post('add_more_pack')==1){

					$yes_secondary_pack = $this->input->post('yes_secondary_pack_add');

					$qty = $this->input->post('yes_secondary_pack_qty_add');



					for($i = 0; $i<count($yes_secondary_pack); $i++){
					$pack =array(
					'record_id'=>$record_id,
					'yes_secondary_pack'=>$yes_secondary_pack[$i],
					'yes_secondary_pack_qty'=>$qty[$i],
					'added_on' => date("Y-m-d h:i:s"),
					'added_by' => $this->session->userdata['logged_in']['user_id']
					);


					$this->db->insert('df_form_multi_track_machine_pack', $pack);
					}


					}

		// 	$part_desc = $this->input->post('special_notes_list');
		// 	$part_qty = $this->input->post('special_notes_qty');

		// 	  if (!empty($part_desc)) {
        //     for ($i = 0; $i < count($part_desc); $i++) {
        //         $data9 = array(
        //             'record_id' => $latest_id,
        //             'special_notes_list' => $part_desc[$i], 
        //             'special_notes_qty' => $part_qty[$i],
        //             'added_on' => date("Y-m-d h:i:s"),
        //             'added_by' => $this->session->userdata['logged_in']['user_id']
        //         );
        //         $this->db->insert('df_form_multi_track_machine6', $data9);
        //     }
        // }

		$user_id=$_SESSION['logged_in']['user_id'];
			$data34 = array('df_id'=>0,
			'task_status'=>1,
			'task_completed_on'=>date('Y-m-d H:i:s'),
			'task_completed_by'=>$user_id,
			'userid'=>$user_id);

			$this->db->where('id',$this->uri->segment(5));
			$this->db->update('task_department_wise_scheduling',$data34);

			/*Select PO No*/
			$pono = $this->getpono($this->uri->segment(5));
			/*Select PO No*/

			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);
			$data = array('df_id'=>0,
				'taskid'=>87,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$pono,
				'task_status'=>0,
				'remarks'=>'',
				'assigned_user'=>$user_id,
				'userid'=>$user_id,
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$data);


			$this->session->set_flashdata('success','<div class="alert alert-success"> Thank You! Data successfully added.</div>');
		//redirect(page_url.'Formats/df_form/'.$po_id.'/'.$lead_id);
		redirect(page_url.'Formats/powder_df_form/'.$po_id.'/'.$lead_id);
		}

	}


// Powder DF Form edit======================================================================

public function releasedf(){

	
	$this->load->view('taskview/release_df');
}

public function checkHolidays() {
        // Get the start and end dates from the POST request
        $startDate = $this->input->post('start_date');
        $endDate = $this->input->post('end_date');

        // Query to get all holidays from the 'prestogroup_holidays' table
        $this->db->select('holiday_date');
        $this->db->from('prestogroup_holidays');
        $this->db->where('holiday_date >=', $startDate);
        $this->db->where('holiday_date <=', $endDate);
        $query = $this->db->get();

        // Check if there are holidays within the date range
        if ($query->num_rows() > 0) {
            // Get holidays as an array
            $holidays = $query->result_array();
            // Return the holidays as JSON
            echo json_encode(['holidays' => array_column($holidays, 'holiday_date')]);
        } else {
            // No holidays found, return an empty array
            echo json_encode(['holidays' => []]);
        }
    }




	public function add_design_form_600(){

		$this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');
		$norml_id=$this->uri->segment(3);
		$po_id = $this->uri->segment(4);
		$lead_id = $this->uri->segment(5);
			if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/design_form_new');
		}else{

			date_default_timezone_set("Asia/Kolkata"); 

			$data =array(
				'po_id'=>$po_id,
				'lead_id'=>$lead_id,
				'design_form_date'=>$this->input->post('design_form_date'),
				'reference_no'=>$this->input->post('reference_no'),
				'ref_df_date'=>$this->input->post('ref_df_date'),
				'bom_no'=>$this->input->post('bom_no'),
				'bom_no_remarks'=>$this->input->post('bom_no_remarks'),
				'date_of_po_remarks'=>$this->input->post('date_of_po_remarks'),
				'dispatch_date'=>$this->input->post('dispatch_date'),
				'dispatch_date_remarks'=>$this->input->post('dispatch_date_remarks'),
				'trial_date'=>$this->input->post('trial_date'),
				'trial_date_remarks'=>$this->input->post('trial_date_remarks'),
				'machine_qty'=>$this->input->post('machine_qty'),
				'machine_qty_remarks'=>$this->input->post('machine_qty_remarks'),
				'machine_mode_no_remarks'=>$this->input->post('machine_mode_no_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);



			$this->db->insert('df_design_form_600_table', $data);

			$latest_id =$this->db->insert_id();


			$data1 =array(
				'record_id'=>$latest_id,
				'tracks_remarks'=>$this->input->post('tracks_remarks'),
				'product_packed_remarks'=>$this->input->post('product_packed_remarks'),
				'filling_unit_remarks'=>$this->input->post('filling_unit_remarks'),
				'quantity_packed_remarks'=>$this->input->post('quantity_packed_remarks'),
				'pouch_size_remarks'=>$this->input->post('pouch_size_remarks'),
				'profle_sealing_remarks'=>$this->input->post('profle_sealing_remarks'),
				'notching_option'=>$this->input->post('notching_option'),
				'notching_option_remarks'=>$this->input->post('notching_option_remarks'),
				'filling_tank'=>$this->input->post('filling_tank'),
				'header_option'=>$this->input->post('header_option'),
				'filling_tank_remarks'=>$this->input->post('filling_tank_remarks'),
				'provision_coding'=>$this->input->post('provision_coding'),
				'provision_coding_option'=>$this->input->post('provision_coding_option'),
				'provision_coding_remarks'=>$this->input->post('provision_coding_remarks'),
				'hooper_details'=>$this->input->post('hooper_details'),
				'openable_option'=>$this->input->post('openable_option'),
				'closed_option'=>$this->input->post('closed_option'),
				'pressurised_option'=>$this->input->post('pressurised_option'),
				'closed_pressurised_option'=>$this->input->post('closed_pressurised_option'),
				'non_pressurised_option'=>$this->input->post('non_pressurised_option'),
				'non_closed_pressurised_option'=>$this->input->post('non_closed_pressurised_option'),
				'hooper_details_remarks'=>$this->input->post('hooper_details_remarks'),
				'web_aligner'=>$this->input->post('web_aligner'),
				'web_aligner_option'=>$this->input->post('web_aligner_option'),
				'web_aligner_remarks'=>$this->input->post('web_aligner_remarks'),
				'center_slitting'=>$this->input->post('center_slitting'),
				'center_slitting_remarks'=>$this->input->post('center_slitting_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_600_machine_specification', $data1);


			$data2 =array(
				'record_id'=>$latest_id,
				'vertical_slitting'=>$this->input->post('vertical_slitting'),
				'vertical_slitting_remarks'=>$this->input->post('vertical_slitting_remarks'),
				'pulling_machine'=>$this->input->post('pulling_machine'),
				'pulling_machine_remarks'=>$this->input->post('pulling_machine_remarks'),
				'hose_pipe'=>$this->input->post('hose_pipe'),
				'hose_pipe_remarks'=>$this->input->post('hose_pipe_remarks'),
				'horizontal_sealer'=>$this->input->post('horizontal_sealer'),
				'horizontal_sealer_remarks'=>$this->input->post('horizontal_sealer_remarks'),
				'vertical_sealer'=>$this->input->post('vertical_sealer'),
				'vertical_sealer_remarks'=>$this->input->post('vertical_sealer_remarks'),
				'priston_drive'=>$this->input->post('priston_drive'),
				'priston_drive_remarks'=>$this->input->post('priston_drive_remarks'),
				'valve_movement'=>$this->input->post('valve_movement'),
				'valve_movement_remarks'=>$this->input->post('valve_movement_remarks'),
				'nozzle_funnel'=>$this->input->post('nozzle_funnel'),
				'powder_option1'=>$this->input->post('powder_option1'),
				'liquid_option1'=>$this->input->post('liquid_option1'),
				'liquid_shut_option1'=>$this->input->post('liquid_shut_option1'),
				'nozzle_funnel_remarks'=>$this->input->post('nozzle_funnel_remarks'),
				'horizontal_sealer1_remarks'=>$this->input->post('horizontal_sealer1_remarks'),
				'vertical_sealer1_remarks'=>$this->input->post('vertical_sealer1_remarks'),
				'working_speed'=>$this->input->post('working_speed'),
				'working_speed_remarks'=>$this->input->post('working_speed_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('df_form_600_machine_specification_1', $data2);

			$data3 =array(
				'record_id'=>$latest_id,
				'reel_core_diameter'=>$this->input->post('reel_core_diameter'),
				'reel_core_diameter_remarks'=>$this->input->post('reel_core_diameter_remarks'),
				'trial_material'=>$this->input->post('trial_material'),
				'trial_material_remarks'=>$this->input->post('trial_material_remarks'),
				'laminate_trial'=>$this->input->post('laminate_trial'),
				'laminate_trail_remarks'=>$this->input->post('laminate_trail_remarks'),
				'laminate_detail'=>$this->input->post('laminate_detail'),
				'laminate_detail_remarks'=>$this->input->post('laminate_detail_remarks'),
				'heater_ssr_box'=>$this->input->post('heater_ssr_box'),
				'heater_ssr_box_remarks'=>$this->input->post('heater_ssr_box_remarks'),
				'beacon_light'=>$this->input->post('beacon_light'),
				'beacon_light_remarks'=>$this->input->post('beacon_light_remarks'),
				'hooper_level'=>$this->input->post('hooper_level'),
				'hooper_level_option'=>$this->input->post('hooper_level_option'),
				'hooper_level_remarks'=>$this->input->post('hooper_level_remarks'),
				'safety_relay'=>$this->input->post('safety_relay'),
				'safety_relay_remarks'=>$this->input->post('safety_relay_remarks'),
				'plc_maker'=>$this->input->post('plc_maker'),
				'plc_maker_remarks'=>$this->input->post('plc_maker_remarks'),
				'conveyor'=>$this->input->post('conveyor'),
				'conveyor_option_yes'=>$this->input->post('conveyor_option_yes'),
				'conveyor_remarks'=>$this->input->post('conveyor_remarks'),
				'standard'=>$this->input->post('standard'),
				'standard_remarks'=>$this->input->post('standard_remarks'),
				'changeover_part'=>$this->input->post('changeover_part'),
				'changeover_part_remarks'=>$this->input->post('changeover_part_remarks'),
				'special_notes'=>$this->input->post('special_notes'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']);

			$this->db->insert('df_form_600_machine_specification_2', $data3);

			$user_id=$_SESSION['logged_in']['user_id'];
			$data34 = array('df_id'=>0,
			'task_status'=>1,
			'task_completed_on'=>date('Y-m-d H:i:s'),
			'task_completed_by'=>$user_id,
			'userid'=>$user_id);

			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('task_department_wise_scheduling',$data34);

			/*Select PO No*/
			$pono = $this->getpono($this->uri->segment(3));
			/*Select PO No*/

			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);
			$data = array('df_id'=>0,
				'taskid'=>86,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$pono,
				'task_status'=>0,
				'remarks'=>'',
				'assigned_user'=>$user_id,
				'userid'=>$user_id,
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$data);

			$this->session->set_flashdata('success','<div class="alert alert-success"> Thank You! Data successfully added.</div>');
		redirect(page_url.'Formats/df_form_600/'.$po_id.'/'.$lead_id);

		}

	}


	public function design_form_600_edit(){

		
	$this->load->model('Task_model');
	$lead_id=$this->uri->segment(5);

    $result = $this->Task_model->getSpecificTaskDates(date('Y-m-d'));


	 $task_103_date = null;
    $task_52_date = null;

    foreach ($result as $task) {
        if ($task['task_id'] == 103) {
            $task_103_date = $task['expected_date'];
        }
        if ($task['task_id'] == 52) {
            $task_52_date = $task['expected_date'];
        }
    }

    // Pass variables to view
    $data['task_103_date'] = $task_103_date;
    $data['task_52_date'] = $task_52_date;

		$this->load->view('dashboard/edit_df_project_form');

	}



	public function edit_design_form_600(){

		$this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');
		$po_id = $this->uri->segment(3);
		$lead_id = $this->uri->segment(4);
		 $record_id = $this->uri->segment(5);
			if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/design_form_600_edit');
		}else{

			date_default_timezone_set("Asia/Kolkata"); 

			$data =array(
				'design_form_date'=>$this->input->post('design_form_date'),
				'reference_no'=>$this->input->post('reference_no'),
				'ref_df_date'=>$this->input->post('ref_df_date'),
				'bom_no'=>$this->input->post('bom_no'),
				'bom_no_remarks'=>$this->input->post('bom_no_remarks'),
				'date_of_po_remarks'=>$this->input->post('date_of_po_remarks'),
				'dispatch_date'=>$this->input->post('dispatch_date'),
				'dispatch_date_remarks'=>$this->input->post('dispatch_date_remarks'),
				'trial_date'=>$this->input->post('trial_date'),
				'trial_date_remarks'=>$this->input->post('trial_date_remarks'),
				'machine_qty'=>$this->input->post('machine_qty'),
				'machine_qty_remarks'=>$this->input->post('machine_qty_remarks'),
				'machine_mode_no_remarks'=>$this->input->post('machine_mode_no_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);



			 $this->db->where('id', $record_id);
			$this->db->update('df_design_form_600_table', $data);

			


			$data1 =array(
				
				'tracks_remarks'=>$this->input->post('tracks_remarks'),
				'product_packed_remarks'=>$this->input->post('product_packed_remarks'),
				'filling_unit_remarks'=>$this->input->post('filling_unit_remarks'),
				'quantity_packed_remarks'=>$this->input->post('quantity_packed_remarks'),
				'pouch_size_remarks'=>$this->input->post('pouch_size_remarks'),
				'profle_sealing_remarks'=>$this->input->post('profle_sealing_remarks'),
				'notching_option'=>$this->input->post('notching_option'),
				'notching_option_remarks'=>$this->input->post('notching_option_remarks'),
				'filling_tank'=>$this->input->post('filling_tank'),
				'filling_tank_remarks'=>$this->input->post('filling_tank_remarks'),
				'provision_coding'=>$this->input->post('provision_coding'),
				'provision_coding_option'=>$this->input->post('provision_coding_option'),
				'provision_coding_remarks'=>$this->input->post('provision_coding_remarks'),
				'hooper_details'=>$this->input->post('hooper_details'),
				'openable_option'=>$this->input->post('openable_option'),
				'closed_option'=>$this->input->post('closed_option'),
				'pressurised_option'=>$this->input->post('pressurised_option'),
				'closed_pressurised_option'=>$this->input->post('closed_pressurised_option'),
				'non_pressurised_option'=>$this->input->post('non_pressurised_option'),
				'non_closed_pressurised_option'=>$this->input->post('non_closed_pressurised_option'),
				'hooper_details_remarks'=>$this->input->post('hooper_details_remarks'),
				'web_aligner'=>$this->input->post('web_aligner'),
				'web_aligner_option'=>$this->input->post('web_aligner_option'),
				'web_aligner_remarks'=>$this->input->post('web_aligner_remarks'),
				'center_slitting'=>$this->input->post('center_slitting'),
				'center_slitting_remarks'=>$this->input->post('center_slitting_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_600_machine_specification', $data1);


			$data2 =array(
				
				'vertical_slitting'=>$this->input->post('vertical_slitting'),
				'vertical_slitting_remarks'=>$this->input->post('vertical_slitting_remarks'),
				'pulling_machine'=>$this->input->post('pulling_machine'),
				'pulling_machine_remarks'=>$this->input->post('pulling_machine_remarks'),
				'hose_pipe'=>$this->input->post('hose_pipe'),
				'hose_pipe_remarks'=>$this->input->post('hose_pipe_remarks'),
				'horizontal_sealer'=>$this->input->post('horizontal_sealer'),
				'horizontal_sealer_remarks'=>$this->input->post('horizontal_sealer_remarks'),
				'vertical_sealer'=>$this->input->post('vertical_sealer'),
				'vertical_sealer_remarks'=>$this->input->post('vertical_sealer_remarks'),
				'priston_drive'=>$this->input->post('priston_drive'),
				'priston_drive_remarks'=>$this->input->post('priston_drive_remarks'),
				'valve_movement'=>$this->input->post('valve_movement'),
				'valve_movement_remarks'=>$this->input->post('valve_movement_remarks'),
				'nozzle_funnel'=>$this->input->post('nozzle_funnel'),
				'powder_option1'=>$this->input->post('powder_option1'),
				'liquid_option1'=>$this->input->post('liquid_option1'),
				'liquid_shut_option1'=>$this->input->post('liquid_shut_option1'),
				'nozzle_funnel_remarks'=>$this->input->post('nozzle_funnel_remarks'),
				'horizontal_sealer1_remarks'=>$this->input->post('horizontal_sealer1_remarks'),
				'vertical_sealer1_remarks'=>$this->input->post('vertical_sealer1_remarks'),
				'working_speed'=>$this->input->post('working_speed'),
				'working_speed_remarks'=>$this->input->post('working_speed_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);


			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_600_machine_specification_1', $data2);

			$data3 =array(
				
				'reel_core_diameter'=>$this->input->post('reel_core_diameter'),
				'reel_core_diameter_remarks'=>$this->input->post('reel_core_diameter_remarks'),
				'trial_material'=>$this->input->post('trial_material'),
				'trial_material_remarks'=>$this->input->post('trial_material_remarks'),
				'laminate_trial'=>$this->input->post('laminate_trial'),
				'laminate_trail_remarks'=>$this->input->post('laminate_trail_remarks'),
				'laminate_detail'=>$this->input->post('laminate_detail'),
				'laminate_detail_remarks'=>$this->input->post('laminate_detail_remarks'),
				'heater_ssr_box'=>$this->input->post('heater_ssr_box'),
				'heater_ssr_box_remarks'=>$this->input->post('heater_ssr_box_remarks'),
				'beacon_light'=>$this->input->post('beacon_light'),
				'beacon_light_remarks'=>$this->input->post('beacon_light_remarks'),
				'hooper_level'=>$this->input->post('hooper_level'),
				'hooper_level_option'=>$this->input->post('hooper_level_option'),
				'hooper_level_remarks'=>$this->input->post('hooper_level_remarks'),
				'safety_relay'=>$this->input->post('safety_relay'),
				'safety_relay_remarks'=>$this->input->post('safety_relay_remarks'),
				'plc_maker'=>$this->input->post('plc_maker'),
				'plc_maker_remarks'=>$this->input->post('plc_maker_remarks'),
				'conveyor'=>$this->input->post('conveyor'),
				'conveyor_option_yes'=>$this->input->post('conveyor_option_yes'),
				'conveyor_remarks'=>$this->input->post('conveyor_remarks'),
				'standard'=>$this->input->post('standard'),
				'standard_remarks'=>$this->input->post('standard_remarks'),
				'changeover_part'=>$this->input->post('changeover_part'),
				'changeover_part_remarks'=>$this->input->post('changeover_part_remarks'),
				'special_notes'=>$this->input->post('special_notes'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			 $this->db->where('record_id', $record_id);
			$this->db->update('df_form_600_machine_specification_2', $data3);

			$taskupdatedata = array('task_completed_on'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$this->session->userdata['logged_in']['user_id'],
				'task_status'=>1);

			$this->db->where('id',$this->uri->segment(6));
			$this->db->update('task_department_wise_scheduling',$taskupdatedata);
			//$qqq = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('id',$this->uri->segment(6))->get();

			//foreach($qqq->result() as $porecords);
			//$ponos = $porecords->po_id;
			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);

			

		$tsk =	$this->db->select('id')->from('task_department_wise_scheduling')->where('taskid', 87)->where('po_id',$po_id)->get();

		if($tsk->num_rows()==0){



			$addnewdata = array('df_id'=>0,
				'taskid'=>87,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$this->session->userdata['logged_in']['user_id'],
				'po_id'=>$po_id,
				'task_status'=>0,
				'userid'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_user'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_by'=>$this->session->userdata['logged_in']['user_id'],
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$addnewdata);

		}

			$this->session->set_flashdata('success','<div class="alert alert-success"> Thank You! Data successfully Updated.</div>');
			redirect(page_url.'Formats/df_form_600/'.$po_id.'/'.$lead_id);

		}

	}



	public function df_full_detail(){
		$this->load->view('dashboard/df_full_detail');
	}

	public function df_delay_management_report() {
		$data = array(
			'df_options' => $this->reportingdata->get_active_dfs(),
			'preset_df_id' => (int) $this->input->get('df_id')
		);

		$this->load->view('dashboard/df_delay_management_report', $data);
	}


	private function empty_df_overtime()
	{
		return array(
			'rows' => array(),
			'by_day' => array(),
			'show_cost' => false,
			'available' => false,
			'summary' => array('occasions' => 0, 'people_entries' => 0, 'person_minutes' => 0, 'total_cost' => 0, 'last_approved_at' => '')
		);
	}

	/**
	 * Approved overtime booked against one DF, one row per person, plus the day-wise
	 * rollup the overtime reports use. Only APPROVED requests count - pending and
	 * rejected hours were never authorised work.
	 *
	 * Rates and amounts are money, so they only reach users who already hold an overtime
	 * reporting or cost-rate grant. Everyone who can open this page still sees who worked
	 * and for how long.
	 */
	private function df_detail_overtime($df_id)
	{
		$overtime = $this->empty_df_overtime();
		foreach (array('overtime_requests', 'overtime_assignments') as $table) {
			if (!$this->db->table_exists($table)) return $overtime;
		}
		$this->load->helper('overtime');
		$permissions = ot_user_permissions($this->db, (int) $this->session->userdata['logged_in']['user_id']);
		$overtime['available'] = true;
		$overtime['show_cost'] = !empty($permissions['reports']) || !empty($permissions['costs']);
		// A site that has not yet run overtime_004 has no cost columns to read.
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
			if (!isset($days[$day])) $days[$day] = array('day' => $day, 'occasions' => array(), 'people_entries' => 0, 'person_minutes' => 0, 'total_cost' => 0);
			$days[$day]['occasions'][(int) $row['request_id']] = true;
			$days[$day]['people_entries']++;
			$days[$day]['person_minutes'] += $minutes;
			$days[$day]['total_cost'] += $cost;
			$overtime['summary']['people_entries']++;
			$overtime['summary']['person_minutes'] += $minutes;
			$overtime['summary']['total_cost'] += $cost;
			if ($row['admin_decided_at'] > $overtime['summary']['last_approved_at']) $overtime['summary']['last_approved_at'] = $row['admin_decided_at'];
			// A manual labour name is kept exactly as typed; it may carry a contractor
			// reference that is the only thing separating two people with the same name.
			$overtime['rows'][] = array(
				'request_code' => $row['request_code'],
				'person_name' => $row['worker_id'] ? ot_person_name($row['person_name']) : $row['person_name'],
				'person_type' => $row['worker_id'] ? 'PMS user' : 'Manual / contract',
				'department' => $row['department'],
				'start_at' => $row['start_at'],
				'end_at' => $row['end_at'],
				'day' => $day,
				'minutes' => $minutes,
				'hourly_rate' => $overtime['show_cost'] ? (float) $row['hourly_rate'] : null,
				'cost_amount' => $overtime['show_cost'] ? $cost : null,
				'requested_by' => ot_person_name($row['requested_first_name'], $row['requested_last_name']),
				'approved_by' => ot_person_name($row['approved_first_name'], $row['approved_last_name']),
				'approved_at' => $row['admin_decided_at'],
				'work_reference' => $row['work_reference'],
				'reason' => $row['reason']
			);
		}
		$overtime['summary']['occasions'] = count($requests);
		foreach ($days as $day) {
			$day['occasions'] = count($day['occasions']);
			$overtime['by_day'][] = $day;
		}
		usort($overtime['by_day'], function ($a, $b) { return strcmp($a['day'], $b['day']); });
		return $overtime;
	}

	public function get_df_details($df_id) {
		$this->output->set_content_type('application/json');
		$df_id = (int) $df_id;

		$output = array(
			'df_info' => null,
			'po_info' => null,
			'help_tickets' => array(),
			'overtime' => $this->empty_df_overtime(),
			'plan_vs_actual' => array(),
			'department_users' => array(),
			'metrics' => array(
				'total' => 0,
				'completed' => 0,
				'open' => 0,
				'approval_pending' => 0,
				'delayed' => 0,
				'open_delayed' => 0,
				'closed_delayed' => 0,
				'on_hold' => 0,
				'completion_pct' => 0
			),
			'summary' => array(
				'department_count' => 0,
				'owner_count' => 0,
				'open_ticket_count' => 0,
				'closed_ticket_count' => 0,
				'max_delay_days' => 0,
				'total_delay_days' => 0,
				'avg_delay_days' => 0,
				'delay_task_count' => 0,
				'health' => 'Waiting',
				'health_key' => 'waiting',
				'health_note' => 'Select a DF to generate the latest management picture.',
				'planned_start' => '',
				'planned_end' => '',
				'actual_end' => '',
				'forecast_end' => '',
				'overall_delay_days' => 0,
				'latest_activity' => null,
				'current_focus' => null,
				'next_due_task' => null,
				'highest_delay_task' => null,
				'top_delay_departments' => array()
			),
			'department_summary' => array(),
			'owner_summary' => array(),
			'timeline' => array(),
			'recent_activity' => array(),
			'priority_tasks' => array(),
			'delay_report' => array()
		);

		if ($df_id <= 0) {
			$this->output->set_output(json_encode($output));
			return;
		}

		$output['df_info'] = $this->db->select('
				df.id,
				df.df_no,
				df.df_description,
				df.df_upload,
				df.added_on,
				df.completed_on,
				df.df_status,
				IFNULL(df.on_hold, 0) as on_hold,
				creator.title as released_title,
				creator.first_name as released_first_name,
				creator.last_name as released_last_name
			')
			->from('df_release as df')
			->join('system_users as creator', 'df.added_by = creator.user_id', 'left')
			->where('df.id', $df_id)
			->get()
			->row_array();

		if (empty($output['df_info'])) {
			$this->output->set_output(json_encode($output));
			return;
		}

		$output['df_info']['released_by'] = $this->format_df_detail_person_name(
			$output['df_info']['released_title'],
			$output['df_info']['released_first_name'],
			$output['df_info']['released_last_name']
		);
		$output['df_info']['status_text'] = ((int) $output['df_info']['on_hold'] === 1)
			? 'On Hold'
			: (((int) $output['df_info']['df_status'] === 1) ? 'Closed' : 'Active');
		$output['df_info']['df_download_url'] = '';
		if (!empty($output['df_info']['df_upload'])) {
			$output['df_info']['df_download_url'] = sfdocument . 'Taskdocument/dfattachment/' . $output['df_info']['df_upload'];
		}

		$output['po_info'] = $this->db->select('
				po.id,
				po.po_attachment,
				po.pono,
				po.podate,
				po.order_value as povalue,
				po.company_name as party_name,
				IFNULL(po.basic_machine, "") as machine_name,
				IFNULL(po.customer_currency, "INR") as currency,
				marketing.title as marketing_title,
				marketing.first_name as marketing_first_name,
				marketing.last_name as marketing_last_name
			')
			->from('poreceived as po')
			->join('system_users as marketing', 'po.added_by = marketing.user_id', 'left')
			->where('po.df_id', $df_id)
			->order_by('po.id', 'DESC')
			->get()
			->row_array();

		if (!empty($output['po_info'])) {
			$output['po_info']['marketing_person'] = $this->format_df_detail_person_name(
				$output['po_info']['marketing_title'],
				$output['po_info']['marketing_first_name'],
				$output['po_info']['marketing_last_name']
			);
			$output['po_info']['po_download_url'] = '';
			if (!empty($output['po_info']['po_attachment'])) {
				$output['po_info']['po_download_url'] = sfdocument . 'Taskdocument/' . $output['po_info']['po_attachment'];
			}
			$output['po_info']['po_count'] = (int) $this->db
				->where('df_id', $df_id)
				->count_all_results('poreceived');
		}

		$output['help_tickets'] = $this->db->select('
				cts.id,
				cts.help_ticket_no,
				IFNULL(cts.remarks, "") as remarks,
				IFNULL(cts.updated_remarks, "") as updated_remarks,
				IFNULL(cts.ticket_closing_remarks, "") as ticket_closing_remarks,
				cts.added_on,
				cts.updated_on,
				cts.ticket_closed_on,
				cts.ticket_status as status,
				IFNULL(d.department, "") as dept_name,
				IFNULL(tm.task_name, "") as task_name,
				raised.title as raised_title,
				raised.first_name as raised_first_name,
				raised.last_name as raised_last_name,
				owner.title as owner_title,
				owner.first_name as owner_first_name,
				owner.last_name as owner_last_name,
				closer.title as closed_title,
				closer.first_name as closed_first_name,
				closer.last_name as closed_last_name
			')
			->from('communication_ticket_system as cts')
			->join('departments as d', 'cts.department_id = d.department_id', 'left')
			->join('task_management as tm', 'cts.task_id = tm.task_id', 'left')
			->join('system_users as raised', 'cts.added_by = raised.user_id', 'left')
			->join('system_users as owner', 'cts.user_id = owner.user_id', 'left')
			->join('system_users as closer', 'cts.ticket_closed_by = closer.user_id', 'left')
			->where('cts.df_id', $df_id)
			->order_by('cts.added_on', 'DESC')
			->order_by('cts.id', 'DESC')
			->get()
			->result_array();

		$history_rows = $this->db->select('
				tps.id,
				tps.recordid as task_record_id,
				IFNULL(tps.remarks, "") as remarks,
				tps.added_on,
				IFNULL(tm.task_name, "") as task_name,
				IFNULL(d.department, "") as department,
				actor.title as actor_title,
				actor.first_name as actor_first_name,
				actor.last_name as actor_last_name
			')
			->from('task_pending_status as tps')
			->join('task_department_wise_scheduling as tdws', 'tdws.id = tps.recordid', 'inner')
			->join('task_management as tm', 'tm.task_id = tdws.taskid', 'left')
			->join('departments as d', 'd.department_id = tdws.department_id', 'left')
			->join('system_users as actor', 'actor.user_id = tps.added_by', 'left')
			->where('tdws.df_id', $df_id)
			->where('tdws.taskid >', 0)
			->order_by('tps.added_on', 'DESC')
			->order_by('tps.id', 'DESC')
			->limit(18)
			->get()
			->result_array();

		$this->load->helper('df_delay');
		$departmentDelaySql = df_department_overdue_sql($this->db, 'tdws', date('Y-m-d'));

		$this->db->select("
			tdws.id as task_record_id,
			tdws.df_id,
			tdws.taskid,
			tdws.department_id,
			tdws.assigned_user,
			tdws.start_date,
			tdws.end_date,
			tdws.task_completed_on,
			tdws.task_completed_by,
			tdws.task_status,
			IFNULL(tdws.on_hold, 0) as on_hold,
			IFNULL(tdws.remarks, '') as remarks,
			tdws.taskupdatedontime,
			tdws.changedOn,
			tdws.assigned_on,
			IFNULL(d.department, '') as department,
			IFNULL(task.task_name, '') as task_name,
			IFNULL(task.sortorder, 0) as sortorder,
			CONCAT(IFNULL(u.title, ''), ' ', IFNULL(u.first_name, ''), ' ', IFNULL(u.last_name, '')) as responsible_person,
			completer.title as completed_title,
			completer.first_name as completed_first_name,
			completer.last_name as completed_last_name,
			(
				SELECT COUNT(cts.id)
				FROM communication_ticket_system cts
				WHERE cts.task_record_id = tdws.id
				AND cts.ticket_status = 0
			) as open_ticket_count,
			(
				SELECT COUNT(cts.id)
				FROM communication_ticket_system cts
				WHERE cts.task_record_id = tdws.id
			) as total_ticket_count,
			(
				SELECT tps.remarks
				FROM task_pending_status tps
				WHERE tps.recordid = tdws.id
				ORDER BY tps.added_on DESC, tps.id DESC
				LIMIT 1
			) as latest_history_remark,
			(
				SELECT tps.added_on
				FROM task_pending_status tps
				WHERE tps.recordid = tdws.id
				ORDER BY tps.added_on DESC, tps.id DESC
				LIMIT 1
			) as latest_history_on,
			CASE
				WHEN IFNULL(tdws.on_hold, 0) = 1 THEN 0
				WHEN tdws.task_status = 1
					AND tdws.task_completed_on IS NOT NULL
					AND tdws.task_completed_on != '0000-00-00 00:00:00'
					AND DATE(tdws.task_completed_on) > tdws.end_date
				THEN DATEDIFF(DATE(tdws.task_completed_on), tdws.end_date)
				WHEN $departmentDelaySql
				THEN DATEDIFF(CURDATE(), tdws.end_date)
				ELSE 0
			END as delay_days
		", false);
		$this->db->from('task_department_wise_scheduling as tdws');
		$this->db->join('departments as d', 'tdws.department_id = d.department_id', 'left');
		$this->db->join('task_management as task', 'tdws.taskid = task.task_id', 'left');
		$this->db->join('system_users as u', 'tdws.assigned_user = u.user_id', 'left');
		$this->db->join('system_users as completer', 'tdws.task_completed_by = completer.user_id', 'left');
		$this->db->where('tdws.df_id', $df_id);
		$this->db->where('tdws.taskid >', 0);
		$this->db->order_by('task.sortorder', 'ASC');
		$this->db->order_by('tdws.end_date', 'ASC');
		$this->db->order_by('tdws.id', 'ASC');
		$output['plan_vs_actual'] = $this->db->get()->result_array();

		$department_summary = array();
		$owner_summary = array();
		$priority_tasks = array();
		$timeline = array();
		$department_ids_for_users = array();
		$planned_start = '';
		$planned_end = '';
		$actual_end = '';
		$total_delay_days = 0;

		foreach ($output['help_tickets'] as $ticket_key => $ticket_row) {
			$output['help_tickets'][$ticket_key]['dept_name'] = $this->format_df_live_manager_title_case($ticket_row['dept_name']);
			$output['help_tickets'][$ticket_key]['raised_by'] = $this->format_df_detail_person_name(
				$ticket_row['raised_title'],
				$ticket_row['raised_first_name'],
				$ticket_row['raised_last_name']
			);
			$output['help_tickets'][$ticket_key]['assigned_to'] = $this->format_df_detail_person_name(
				$ticket_row['owner_title'],
				$ticket_row['owner_first_name'],
				$ticket_row['owner_last_name']
			);
			$output['help_tickets'][$ticket_key]['closed_by'] = $this->format_df_detail_person_name(
				$ticket_row['closed_title'],
				$ticket_row['closed_first_name'],
				$ticket_row['closed_last_name']
			);
			$output['help_tickets'][$ticket_key]['status_text'] = ((int) $ticket_row['status'] === 1) ? 'Closed' : 'Open';
			$output['help_tickets'][$ticket_key]['status_key'] = ((int) $ticket_row['status'] === 1) ? 'closed' : 'open';

			if ((int) $ticket_row['status'] === 1) {
				$output['summary']['closed_ticket_count']++;
			} else {
				$output['summary']['open_ticket_count']++;
			}

			$this->append_df_detail_timeline_event(
				$timeline,
				$ticket_row['added_on'],
				'Ticket Logged',
				'Help Ticket #' . $ticket_row['help_ticket_no'] . ' raised for ' . ($ticket_row['task_name'] !== '' ? $ticket_row['task_name'] : 'DF support'),
				$output['help_tickets'][$ticket_key]['raised_by'],
				$ticket_row['task_name'],
				$output['help_tickets'][$ticket_key]['dept_name'],
				'ticket'
			);

			if ((int) $ticket_row['status'] === 1) {
				$this->append_df_detail_timeline_event(
					$timeline,
					$ticket_row['ticket_closed_on'],
					'Ticket Closed',
					'Help Ticket #' . $ticket_row['help_ticket_no'] . ' closed',
					$output['help_tickets'][$ticket_key]['closed_by'],
					$ticket_row['task_name'],
					$output['help_tickets'][$ticket_key]['dept_name'],
					'ticket-closed'
				);
			}
		}

		foreach ($output['plan_vs_actual'] as $row_key => $task_row) {
			$task_row['department'] = $this->format_df_live_manager_title_case($task_row['department']);
			$task_row['responsible_person'] = $this->format_df_live_manager_title_case($task_row['responsible_person']);
			$department_ids_for_users[] = (int) $task_row['department_id'];
			if ($task_row['responsible_person'] === '') {
				$task_row['responsible_person'] = 'Unassigned';
			}

			$task_row['completed_by_name'] = $this->format_df_detail_person_name(
				$task_row['completed_title'],
				$task_row['completed_first_name'],
				$task_row['completed_last_name']
			);

			$is_completed = ((int) $task_row['task_status'] === 1 && $this->is_valid_df_detail_datetime($task_row['task_completed_on']));
			$is_on_hold = ((int) $task_row['on_hold'] === 1);
			$is_approval_pending = ((int) $task_row['task_status'] === 2);
			$delay_days = (int) $task_row['delay_days'];

			if ($is_on_hold) {
				$task_row['status_key'] = 'hold';
				$task_row['status_label'] = 'On Hold';
			} elseif ($is_completed) {
				$task_row['status_key'] = 'completed';
				$task_row['status_label'] = ($delay_days > 0) ? 'Completed Late' : 'Completed';
			} elseif ($is_approval_pending) {
				$task_row['status_key'] = ($delay_days > 0) ? 'approval-delay' : 'approval';
				$task_row['status_label'] = 'Waiting Approval';
			} elseif ($delay_days > 0) {
				$task_row['status_key'] = 'delay';
				$task_row['status_label'] = 'Delayed';
			} else {
				$task_row['status_key'] = 'ongoing';
				$task_row['status_label'] = 'Ongoing';
			}

			$latest_update_text = trim((string) $task_row['latest_history_remark']);
			if ($latest_update_text === '') {
				$latest_update_text = trim((string) $task_row['remarks']);
			}
			$task_row['latest_update_text'] = $latest_update_text;
			$latest_update_on = '';
			foreach (array($task_row['latest_history_on'], $task_row['changedOn'], $task_row['taskupdatedontime'], $task_row['assigned_on']) as $candidate_time) {
				if ($this->is_valid_df_detail_datetime($candidate_time)) {
					if ($latest_update_on === '' || strtotime($candidate_time) > strtotime($latest_update_on)) {
						$latest_update_on = $candidate_time;
					}
				}
			}
			$task_row['latest_update_on'] = $latest_update_on;

			if ($this->is_valid_df_detail_datetime($task_row['start_date'])) {
				if ($planned_start === '' || strtotime($task_row['start_date']) < strtotime($planned_start)) {
					$planned_start = $task_row['start_date'];
				}
			}

			if ($this->is_valid_df_detail_datetime($task_row['end_date'])) {
				if ($planned_end === '' || strtotime($task_row['end_date']) > strtotime($planned_end)) {
					$planned_end = $task_row['end_date'];
				}
			}

			if ($is_completed && ($actual_end === '' || strtotime($task_row['task_completed_on']) > strtotime($actual_end))) {
				$actual_end = $task_row['task_completed_on'];
			}

			$output['metrics']['total']++;
			if ($is_on_hold) {
				$output['metrics']['on_hold']++;
			} elseif ($is_completed) {
				$output['metrics']['completed']++;
			} else {
				$output['metrics']['open']++;
				if ($is_approval_pending) {
					$output['metrics']['approval_pending']++;
				}
			}

			if ($delay_days > 0) {
				$output['metrics']['delayed']++;
				$output['summary']['max_delay_days'] = max($output['summary']['max_delay_days'], $delay_days);
				if ($is_completed) {
					$output['metrics']['closed_delayed']++;
				} elseif (!$is_on_hold) {
					$output['metrics']['open_delayed']++;
				}
			}

			$department_key = ($task_row['department'] !== '') ? $task_row['department'] : 'Unmapped';
			if (!isset($department_summary[$department_key])) {
				$department_summary[$department_key] = array(
					'department' => $department_key,
					'total' => 0,
					'completed' => 0,
					'open' => 0,
					'delayed' => 0,
					'on_hold' => 0,
					'completion_pct' => 0,
					'total_delay_days' => 0,
					'avg_delay_days' => 0,
					'max_delay_days' => 0,
					'planned_start' => '',
					'planned_end' => '',
					'current_end' => ''
				);
			}
			$department_summary[$department_key]['total']++;
			if ($is_on_hold) {
				$department_summary[$department_key]['on_hold']++;
			} elseif ($is_completed) {
				$department_summary[$department_key]['completed']++;
			} else {
				$department_summary[$department_key]['open']++;
			}
			if ($delay_days > 0) {
				$department_summary[$department_key]['delayed']++;
				$department_summary[$department_key]['total_delay_days'] += $delay_days;
				$department_summary[$department_key]['max_delay_days'] = max((int) $department_summary[$department_key]['max_delay_days'], $delay_days);
				$total_delay_days += $delay_days;
			}

			if ($this->is_valid_df_detail_datetime($task_row['start_date'])) {
				if ($department_summary[$department_key]['planned_start'] === '' || strtotime($task_row['start_date']) < strtotime($department_summary[$department_key]['planned_start'])) {
					$department_summary[$department_key]['planned_start'] = $task_row['start_date'];
				}
			}

			if ($this->is_valid_df_detail_datetime($task_row['end_date'])) {
				if ($department_summary[$department_key]['planned_end'] === '' || strtotime($task_row['end_date']) > strtotime($department_summary[$department_key]['planned_end'])) {
					$department_summary[$department_key]['planned_end'] = $task_row['end_date'];
				}
			}

			$current_end_candidate = '';
			if ($is_completed && $this->is_valid_df_detail_datetime($task_row['task_completed_on'])) {
				$current_end_candidate = $task_row['task_completed_on'];
			} elseif (!$is_on_hold) {
				$current_end_candidate = date('Y-m-d');
			} elseif ($this->is_valid_df_detail_datetime($task_row['end_date'])) {
				$current_end_candidate = $task_row['end_date'];
			}

			if ($this->is_valid_df_detail_datetime($current_end_candidate)) {
				if ($department_summary[$department_key]['current_end'] === '' || strtotime($current_end_candidate) > strtotime($department_summary[$department_key]['current_end'])) {
					$department_summary[$department_key]['current_end'] = $current_end_candidate;
				}
			}

			if (!$is_completed && !$is_on_hold) {
				$owner_key = trim((string) $task_row['responsible_person']);
				if ($owner_key !== '' && $owner_key !== 'Unassigned') {
					if (!isset($owner_summary[$owner_key])) {
						$owner_summary[$owner_key] = array(
							'owner' => $owner_key,
							'open_tasks' => 0,
							'delayed_tasks' => 0,
							'approval_tasks' => 0,
							'departments' => array(),
							'next_due_date' => ''
						);
					}
					$owner_summary[$owner_key]['open_tasks']++;
					if ($delay_days > 0) {
						$owner_summary[$owner_key]['delayed_tasks']++;
					}
					if ($is_approval_pending) {
						$owner_summary[$owner_key]['approval_tasks']++;
					}
					if ($task_row['department'] !== '') {
						$owner_summary[$owner_key]['departments'][$task_row['department']] = true;
					}
					if ($this->is_valid_df_detail_datetime($task_row['end_date'])) {
						if ($owner_summary[$owner_key]['next_due_date'] === '' || strtotime($task_row['end_date']) < strtotime($owner_summary[$owner_key]['next_due_date'])) {
							$owner_summary[$owner_key]['next_due_date'] = $task_row['end_date'];
						}
					}
				}

				$priority_tasks[] = array(
					'task_name' => $task_row['task_name'],
					'department' => $task_row['department'],
					'responsible_person' => $task_row['responsible_person'],
					'end_date' => $task_row['end_date'],
					'delay_days' => $delay_days,
					'open_ticket_count' => (int) $task_row['open_ticket_count'],
					'status_label' => $task_row['status_label'],
					'status_key' => $task_row['status_key'],
					'latest_update_text' => $task_row['latest_update_text'],
					'latest_update_on' => $task_row['latest_update_on']
				);
			}

			if ($this->is_valid_df_detail_datetime($task_row['task_completed_on']) && $is_completed) {
				$this->append_df_detail_timeline_event(
					$timeline,
					$task_row['task_completed_on'],
					'Task Completed',
					$task_row['task_name'] . (($delay_days > 0) ? ' completed with delay of ' . $delay_days . ' day(s)' : ' completed on time'),
					$task_row['completed_by_name'],
					$task_row['task_name'],
					$task_row['department'],
					'completion'
				);
			}

			$output['plan_vs_actual'][$row_key] = $task_row;
		}

		foreach ($history_rows as $history_row) {
			$history_actor = $this->format_df_detail_person_name(
				$history_row['actor_title'],
				$history_row['actor_first_name'],
				$history_row['actor_last_name']
			);
			$this->append_df_detail_timeline_event(
				$timeline,
				$history_row['added_on'],
				'Task Update',
				trim((string) $history_row['remarks']),
				$history_actor,
				$history_row['task_name'],
				$this->format_df_live_manager_title_case($history_row['department']),
				'update'
			);
		}

		if ($output['summary']['open_ticket_count'] === 0 && !empty($output['help_tickets'])) {
			$output['summary']['open_ticket_count'] = 0;
		}

		foreach ($department_summary as $dept_key => $dept_row) {
			$active_total = max(0, $dept_row['total'] - $dept_row['on_hold']);
			$department_summary[$dept_key]['completion_pct'] = ($active_total > 0)
				? round(($dept_row['completed'] / $active_total) * 100, 2)
				: 0;
			$department_summary[$dept_key]['avg_delay_days'] = ((int) $dept_row['delayed'] > 0)
				? round(((int) $dept_row['total_delay_days']) / (int) $dept_row['delayed'], 1)
				: 0;
		}

		foreach ($owner_summary as $owner_key => $owner_row) {
			$owner_summary[$owner_key]['departments'] = implode(', ', array_keys($owner_row['departments']));
		}

		$department_ids_for_users = array_values(array_unique(array_filter($department_ids_for_users)));
		if (!empty($department_ids_for_users)) {
			$output['department_users'] = $this->get_df_live_manager_users_by_department(
				$this->get_df_live_manager_scope(),
				$department_ids_for_users
			);
		}

		$output['summary']['department_count'] = count($department_summary);
		$output['summary']['owner_count'] = count($owner_summary);
		$output['summary']['planned_start'] = $planned_start;
		$output['summary']['planned_end'] = $planned_end;
		$output['summary']['actual_end'] = $actual_end;
		$output['summary']['total_delay_days'] = $total_delay_days;
		$output['summary']['delay_task_count'] = (int) $output['metrics']['delayed'];
		$output['summary']['avg_delay_days'] = ((int) $output['metrics']['delayed'] > 0)
			? round($total_delay_days / (int) $output['metrics']['delayed'], 1)
			: 0;

		$effective_total = max(0, $output['metrics']['total'] - $output['metrics']['on_hold']);
		$output['metrics']['completion_pct'] = ($effective_total > 0)
			? round(($output['metrics']['completed'] / $effective_total) * 100, 2)
			: 0;

		if ($planned_end !== '') {
			$forecast_end = $planned_end;
			if ($actual_end !== '') {
				$forecast_end = $actual_end;
			} elseif ((int) $output['summary']['max_delay_days'] > 0) {
				$forecast_end = date('Y-m-d', strtotime(date('Y-m-d', strtotime($planned_end)) . ' +' . (int) $output['summary']['max_delay_days'] . ' days'));
			}

			$output['summary']['forecast_end'] = $forecast_end;

			if ($this->is_valid_df_detail_datetime($forecast_end) && strtotime(date('Y-m-d', strtotime($forecast_end))) > strtotime(date('Y-m-d', strtotime($planned_end)))) {
				$output['summary']['overall_delay_days'] = (int) floor((strtotime(date('Y-m-d', strtotime($forecast_end))) - strtotime(date('Y-m-d', strtotime($planned_end)))) / 86400);
			}
		}

		usort($priority_tasks, function ($left, $right) {
			$left_score = ((int) $left['delay_days'] * 100) + ((int) $left['open_ticket_count'] * 10) + (($left['status_key'] === 'approval' || $left['status_key'] === 'approval-delay') ? 5 : 0);
			$right_score = ((int) $right['delay_days'] * 100) + ((int) $right['open_ticket_count'] * 10) + (($right['status_key'] === 'approval' || $right['status_key'] === 'approval-delay') ? 5 : 0);
			if ($left_score === $right_score) {
				$left_time = strtotime($left['end_date']);
				$right_time = strtotime($right['end_date']);
				if ($left_time === $right_time) {
					return 0;
				}
				return ($left_time < $right_time) ? -1 : 1;
			}
			return ($left_score > $right_score) ? -1 : 1;
		});

		$next_due_tasks = $priority_tasks;
		usort($next_due_tasks, function ($left, $right) {
			$left_time = strtotime($left['end_date']);
			$right_time = strtotime($right['end_date']);
			if ($left_time === $right_time) {
				return 0;
			}
			return ($left_time < $right_time) ? -1 : 1;
		});

		$output['priority_tasks'] = array_slice($priority_tasks, 0, 6);
		$output['delay_report'] = array_values(array_filter($output['priority_tasks'], function ($row) {
			return (int) $row['delay_days'] > 0;
		}));
		$output['summary']['current_focus'] = !empty($priority_tasks) ? $priority_tasks[0] : null;
		$output['summary']['next_due_task'] = !empty($next_due_tasks) ? $next_due_tasks[0] : null;

		foreach ($priority_tasks as $priority_row) {
			if ((int) $priority_row['delay_days'] > 0) {
				$output['summary']['highest_delay_task'] = $priority_row;
				break;
			}
		}

			usort($timeline, function ($left, $right) {
				$left_time = isset($left['_sort_time']) ? (int) $left['_sort_time'] : 0;
				$right_time = isset($right['_sort_time']) ? (int) $right['_sort_time'] : 0;
				if ($left_time === $right_time) {
					return 0;
			}
			return ($left_time > $right_time) ? -1 : 1;
		});

			if (empty($timeline)) {
				$this->append_df_detail_timeline_event(
					$timeline,
					$output['df_info']['added_on'],
					'DF Released',
				'DF released into execution flow',
				$output['df_info']['released_by'],
				'',
				'',
					'release'
				);
			}

			foreach ($timeline as $timeline_key => $timeline_row) {
				unset($timeline[$timeline_key]['_sort_time']);
			}

			$output['timeline'] = $timeline;
		$output['recent_activity'] = array_slice($timeline, 0, 8);
		$output['summary']['latest_activity'] = !empty($output['recent_activity']) ? $output['recent_activity'][0] : null;

		$output['department_summary'] = array_values($department_summary);
		usort($output['department_summary'], function ($left, $right) {
			if ((int) $left['total_delay_days'] === (int) $right['total_delay_days']) {
				if ((int) $left['delayed'] === (int) $right['delayed']) {
					if ((int) $left['open'] === (int) $right['open']) {
						return strcmp($left['department'], $right['department']);
					}
					return ((int) $left['open'] > (int) $right['open']) ? -1 : 1;
				}
				return ((int) $left['delayed'] > (int) $right['delayed']) ? -1 : 1;
			}
			return ((int) $left['total_delay_days'] > (int) $right['total_delay_days']) ? -1 : 1;
		});

		$output['summary']['top_delay_departments'] = array_slice(array_values(array_filter($output['department_summary'], function ($row) {
			return (int) $row['delayed'] > 0 || (int) $row['total_delay_days'] > 0;
		})), 0, 3);

		$output['owner_summary'] = array_values($owner_summary);
		usort($output['owner_summary'], function ($left, $right) {
			if ((int) $left['delayed_tasks'] === (int) $right['delayed_tasks']) {
				if ((int) $left['open_tasks'] === (int) $right['open_tasks']) {
					return strcmp($left['owner'], $right['owner']);
				}
				return ((int) $left['open_tasks'] > (int) $right['open_tasks']) ? -1 : 1;
			}
			return ((int) $left['delayed_tasks'] > (int) $right['delayed_tasks']) ? -1 : 1;
		});

		if ((int) $output['df_info']['on_hold'] === 1) {
			$output['summary']['health'] = 'On Hold';
			$output['summary']['health_key'] = 'hold';
			$output['summary']['health_note'] = 'Execution is paused on this DF until hold conditions are cleared.';
		} elseif ((int) $output['df_info']['df_status'] === 1) {
			$output['summary']['health'] = 'Closed';
			$output['summary']['health_key'] = 'closed';
			$output['summary']['health_note'] = ((int) $output['metrics']['closed_delayed'] > 0)
				? 'DF is closed, but one or more tasks were completed after the planned date.'
				: 'DF is closed and the execution trail is available for review.';
		} elseif ((int) $output['metrics']['open_delayed'] > 0 && (int) $output['summary']['open_ticket_count'] > 0) {
			$output['summary']['health'] = 'Critical';
			$output['summary']['health_key'] = 'critical';
			$output['summary']['health_note'] = 'Open delayed tasks and active help tickets both need management attention.';
		} elseif ((int) $output['metrics']['open_delayed'] > 0) {
			$output['summary']['health'] = 'Attention';
			$output['summary']['health_key'] = 'attention';
			$output['summary']['health_note'] = 'Delayed open tasks are impacting the execution plan.';
		} elseif ((int) $output['metrics']['approval_pending'] > 0) {
			$output['summary']['health'] = 'Review Pending';
			$output['summary']['health_key'] = 'review';
			$output['summary']['health_note'] = 'Some work is completed at team level and is waiting for approval closure.';
		} else {
			$output['summary']['health'] = 'Stable';
			$output['summary']['health_key'] = 'stable';
			$output['summary']['health_note'] = 'Execution is moving without visible delay risk right now.';
		}

		$output['overtime'] = $this->df_detail_overtime($df_id);

		$this->output->set_output(json_encode($output));
	}

	private function is_valid_df_detail_datetime($value)
	{
		$value = trim((string) $value);
		if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
			return false;
		}

		return (strtotime($value) !== false);
	}

	private function format_df_detail_person_name($title, $first_name, $last_name)
	{
		$parts = array();
		foreach (array($title, $first_name, $last_name) as $value) {
			$value = trim((string) $value);
			if ($value !== '') {
				$parts[] = $this->format_df_live_manager_title_case($value);
			}
		}

		return trim(implode(' ', $parts));
	}

	private function append_df_detail_timeline_event(&$timeline, $time_value, $label, $message, $actor, $task_name, $department, $event_key)
	{
		if (!$this->is_valid_df_detail_datetime($time_value)) {
			return;
		}

		$timeline[] = array(
			'time' => $time_value,
			'label' => $label,
			'message' => trim((string) $message),
			'actor' => trim((string) $actor),
			'task_name' => trim((string) $task_name),
			'department' => trim((string) $department),
			'event_key' => $event_key,
			'_sort_time' => strtotime($time_value)
		);
	}



		function sparesdashboard()
		{
			 $this->load->model('Spare_quotation_expiry_model', 'spare_quotation_expiry_model');
			 $this->spare_quotation_expiry_model->expire_stale_quotations(date('Y-m-d'));
			 $data['sales_data'] = $this->user->get_sales_data_for_current_month();
		 $data['order_data_by_brand'] = $this->user->get_order_values_and_counts_by_brand();
        
       

		$this->load->view('dashboard/sparesdashboard',$data);
	}
	

	/**
     * UPDATED: Team Performance Dashboard
     * - Super Admins see all departments, grouped, or can filter.
     * - Dept Heads see only their own department.
     */
    public function team_dashboard() {
   
    $this->load->model('Df_Report_model');
   
    $data = [];
    $filters = [];
    $data['department_groups'] = [];

    // --- 1. Get Logged-in User and Define Super Admins ---
    $logged_in_user_id = $this->session->userdata['logged_in']['user_id'];
    $manager_dept_id = $this->session->userdata['logged_in']['department_id'];
    $super_admin_ids = [61, 62, 167, 161, 139, 162, 114,230,243];
   
    $is_super_admin = in_array($logged_in_user_id, $super_admin_ids);
    $data['is_super_admin'] = $is_super_admin;

    // --- 2. Get Date Filters ---
    $start_date_str = $this->input->post('start_date');
    $end_date_str = $this->input->post('end_date');
    $data['selected_start_date'] = $start_date_str;
    $data['selected_end_date'] = $end_date_str;

    if ($start_date_str && $end_date_str) {
        $start_date_obj = DateTime::createFromFormat('d-m-Y', $start_date_str);
        $end_date_obj = DateTime::createFromFormat('d-m-Y', $end_date_str);
        $filters['start_date'] = $start_date_obj ? $start_date_obj->format('Y-m-d') : null;
        $filters['end_date'] = $end_date_obj ? $end_date_obj->format('Y-m-d') : null;
        $data['page_title_suffix'] = " (" . htmlspecialchars($start_date_str) . " to " . htmlspecialchars($end_date_str) . ")";
    } else {
        $data['page_title_suffix'] = " (All-Time)";
    }

    // --- 3. Apply Department Filter Logic (Your Existing Logic) ---
        
    // Special check for Design Super Head (User 167)
    if ($logged_in_user_id == 167 || $logged_in_user_id==189) {
        $filters['department_id'] = 11; 
        $data['selected_department_id'] = 11;
        $data['is_super_admin'] = false; 
    }
    else if ($is_super_admin) {
        $filters['department_id'] = $this->input->post('department_id');
        $data['selected_department_id'] = $filters['department_id'];
    } else {
        // Department Head or Team Leader Logic
        if (empty($manager_dept_id)) {
            $team_leader_info = $this->db->select('department_id')
                                         ->where('team_leader', $logged_in_user_id)
                                         ->where('status', 1)
                                         ->get('prestogroup_teams')
                                         ->row();
            
            if ($team_leader_info) {
                $manager_dept_id = $team_leader_info->department_id;
            } else {
                $data['error_message'] = "Error: No department associated with your account.";
                $data['departments'] = [];
                $this->load->view('reports/team_dashboard_view', $data);
                return;
            }
        }
        $filters['department_id'] = $manager_dept_id;
        $data['selected_department_id'] = $manager_dept_id;
    }

    // ============================================================
    // --- CSV EXPORT WITH SUMMARY & DELAY CALCULATION ---
    // ============================================================
    if ($this->input->post('export_btn') == 'true') {
        
        // 1. Fetch Data
        $export_data = $this->Df_Report_model->get_all_tasks_export($filters);
        $filename = 'Team_Task_Report_' . date('Ymd_His') . '.csv';

        // 2. Calculate Summary Counts
        $total_tasks = count($export_data);
        $total_completed = 0;
        $total_pending = 0;

        if (!empty($export_data)) {
            foreach ($export_data as $row) {
                if (isset($row['task_status']) && $row['task_status'] == 1) {
                    $total_completed++;
                } else {
                    $total_pending++;
                }
            }
        }

        // 3. Clean Buffer
        if (ob_get_length()) ob_clean();

        // 4. Set Headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $fp = fopen('php://output', 'w');

        // --- SECTION A: SUMMARY ---
        fputcsv($fp, ['--- REPORT SUMMARY ---']);
        fputcsv($fp, ['Total Tasks', 'Completed', 'Ongoing Task']);
        fputcsv($fp, [$total_tasks, $total_completed, $total_pending]);
        fputcsv($fp, []); // Empty row
        fputcsv($fp, []); 

        // --- SECTION B: COLUMN HEADERS ---
        fputcsv($fp, [
            'Task Name', 
            'Ticket #', 
            'Assigned To', 
            'Department', 
            'Start Date', 
            'Due Date', 
            'Completed Date', 
            'Status', 
            'Days Delayed', // New Column
            'Remarks'
        ]);

        // --- SECTION C: DATA ROWS ---
        if (!empty($export_data)) {
            $today_ts = strtotime(date('Y-m-d')); // Current timestamp (midnight)

            foreach ($export_data as $row) {
                
                // --- 1. Format Completed Date (Remove Time) ---
                $raw_completed = $row['completed_date'] ?? '';
                $display_completed_date = '';
                
                if (!empty($raw_completed)) {
                    $display_completed_date = date('Y-m-d', strtotime($raw_completed));
                }

                // --- 2. Calculate Days Delayed ---
                $due_ts = !empty($row['due_date']) ? strtotime($row['due_date']) : 0;
                $completed_ts = !empty($raw_completed) ? strtotime(date('Y-m-d', strtotime($raw_completed))) : 0;
                $days_delayed = 0;

                $status_val = $row['task_status'] ?? 0;
                $status_text = ($status_val == 1) ? 'Completed' : 'Ongoing';

                if ($due_ts > 0) {
                    if ($status_val == 1) {
                        // Case: Completed. Check if late.
                        if ($completed_ts > $due_ts) {
                            $diff = $completed_ts - $due_ts;
                            $days_delayed = floor($diff / (60 * 60 * 24));
                        }
                    } else {
                        // Case: Pending. Check if overdue compared to TODAY.
                        if ($today_ts > $due_ts) {
                            $diff = $today_ts - $due_ts;
                            $days_delayed = floor($diff / (60 * 60 * 24));
                        }
                    }
                }

                // --- 3. Write Row ---
                $csv_row = [
                    $row['task_name'] ?? '',
                    $row['ticket_number'] ?? '',
                    $row['assigned_to'] ?? '',
                    $row['department_name'] ?? '',
                    $row['start_date'] ?? '',
                    $row['due_date'] ?? '',
                    $display_completed_date,  // Using formatted date
                    $status_text,
                    $days_delayed,            // New Calculation
                    $row['remarks'] ?? ''
                ];

                fputcsv($fp, $csv_row);
            }
        }

        fclose($fp);
        exit;
    }

    // --- 4. Get Raw Data from Model (For View) ---
    $raw_data = $this->Df_Report_model->get_user_performance_data($filters);
    
    // ... (The rest of your existing code: Process Data into Department Groups, Sorting, View Loading) ...
    // --- 5. Process Data into Department Groups ---
    $page_title_main = "Team Dashboard";
    
    if (!empty($raw_data)) {
      // ... (Rest of logic) ...
      if (!empty($filters['department_id'])) {
        $page_title_main = $raw_data[0]['department'] . " Dashboard";
      } else if ($is_super_admin) {
        $page_title_main = "All Departments Dashboard";
      } else {
        $page_title_main = $raw_data[0]['department'] . " Dashboard";
      }
      
      foreach ($raw_data as $row) {
        $dept_id = $row['department_id'];

        if (!isset($data['department_groups'][$dept_id])) {
          $data['department_groups'][$dept_id] = [
            'department_name' => $row['department'],
            'members' => []
          ];
        }

        if ($row['total_completed'] > 0) {
          $row['on_time_percent'] = round(($row['tasks_on_time'] / $row['total_completed']) * 100, 1);
        } else { $row['on_time_percent'] = 0; }

        $row['total_pending'] = $row['tasks_pending_delayed'] + $row['tasks_pending_on_time'];
        $row['total_assigned'] = $row['total_completed'] + $row['total_pending'];
        $row['total_delayed'] = $row['tasks_delayed_closed'];
        
        if ($row['total_assigned'] > 0) {
           $row['percent_not_done'] = round(($row['total_pending'] / $row['total_assigned']) * 100, 1);
        } else { $row['percent_not_done'] = 0; }
        
        if ($row['total_completed'] > 0) {
           $row['percent_work_delayed'] = round(($row['tasks_delayed_closed'] / $row['total_completed']) * 100, 1);
        } else { $row['percent_work_delayed'] = 0; }
        
        $data['department_groups'][$dept_id]['members'][] = $row;
      }
      
     foreach ($data['department_groups'] as $dept_id => $group) {
        usort($group['members'], function($a, $b) {
          return $b['on_time_percent'] <=> $a['on_time_percent'];
        });
        $data['department_groups'][$dept_id]['members'] = $group['members'];
      }

      uasort($data['department_groups'], function($a, $b) {
        return strcasecmp($a['department_name'], $b['department_name']);
      });
    }
   
    $data['page_title'] = $page_title_main . $data['page_title_suffix'];
    $data['departments'] = $this->Df_Report_model->get_all_departments();

    $this->load->view('reports/team_dashboard_view', $data);
}

    /**
     * UPDATED: Team Task Detail Report
     * - Now includes filters.
     * - Fetches Top 5 Delayed Tasks.
     * - Fetches Open Ticket information.
     */
    public function team_task_detail($user_id = 0, $report_type = 'assigned') {
        
        $this->load->model('Df_Report_model');
        
        $data = [];
        $filters = [];

        // --- 1. Get Filters from URL (using GET) ---
        $start_date_str = $this->input->get('start_date');
        $end_date_str = $this->input->get('end_date');
        
        if ($start_date_str && $end_date_str) {
            $start_date_obj = DateTime::createFromFormat('d-m-Y', $start_date_str);
            $end_date_obj = DateTime::createFromFormat('d-m-Y', $end_date_str);
            $filters['start_date'] = $start_date_obj ? $start_date_obj->format('Y-m-d') : null;
            $filters['end_date'] = $end_date_obj ? $end_date_obj->format('Y-m-d') : null;
        }
        
        // --- 2. Get User & Report Info ---
        $user_info = $this->db->select("CONCAT(first_name, ' ', last_name) as user_name")->where('user_id', $user_id)->get('system_users')->row();
        
        switch ($report_type) {
            case 'pending':
                $data['report_type_title'] = 'Ongoing Task';
                break;

            case 'done':
                $data['report_type_title'] = 'Completed';
                break;

            case 'delayed':
                $data['report_type_title'] = 'Completed By Delayed';
                break;

            case 'assigned':
            default:
                $data['report_type_title'] = 'Assigned';
                break;
        }
        $data['user_name'] = $user_info ? $user_info->user_name : 'Unknown User';
        $data['page_title'] = $data['report_type_title'] . ' Report for ' . $data['user_name'];

        // --- 3. Get Task List ---
        $data['tasks'] = $this->Df_Report_model->get_user_task_list_for_dashboard($user_id, $report_type, $filters);
        
        // --- 4. NEW: Get Tickets & Top 5 ---
        $task_record_ids = array_column($data['tasks'], 'id');
        
        // Re-use the ticket function from our other report
        $data['tickets'] = $this->Df_Report_model->get_open_tickets_for_tasks($task_record_ids);

        // Sort tasks by delay_days to find the top 5
        $sorted_tasks = $data['tasks'];
        usort($sorted_tasks, function($a, $b) {
            return $b['delay_days'] <=> $a['delay_days'];
        });
        
        // Get the top 5, but only if they are actually delayed
        $data['top_5_delayed_tasks'] = [];
        foreach(array_slice($sorted_tasks, 0, 5) as $task) {
            if ($task['delay_days'] > 0) {
                $data['top_5_delayed_tasks'][] = $task;
            }
        }
        
        // --- 5. Pass data back to the view for filters ---
        $data['user_id'] = $user_id;
        $data['report_type'] = $report_type;
        $data['selected_start_date'] = $start_date_str;
        $data['selected_end_date'] = $end_date_str;
        
        $this->load->view('reports/team_task_detail_view', $data);
    }

    public function ajax_get_task_details() {
    // Security check
    if (!$this->input->is_ajax_request()) {
        show_404();
        return;
    }

    $df_id = $this->input->get('df_id');
    $user_id = $this->input->get('user_id');

    // Validate input
    if (empty($df_id) || empty($user_id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
        exit;
    }
    
    $this->load->model('Df_Report_model');
    $tasks = $this->Df_Report_model->get_due_task_details($df_id, $user_id);
    
    // Format dates and sanitize data for output
    foreach ($tasks as &$task) {
        $start_obj = $task['start_date'] ? DateTime::createFromFormat('Y-m-d', $task['start_date']) : false;
        $end_obj = $task['end_date'] ? DateTime::createFromFormat('Y-m-d', $task['end_date']) : false;
        
        $task['start_date'] = $start_obj ? $start_obj->format('d-m-Y') : 'N/A';
        $task['end_date'] = $end_obj ? $end_obj->format('d-m-Y') : 'N/A';
        
        // Handle ticket number
        $task['ticket_number'] = !empty($task['ticket_number']) 
            ? htmlspecialchars($task['ticket_number']) 
            : '<span class="text-muted" style="font-style: italic;">N/A</span>';
            
        $task['task_name'] = htmlspecialchars($task['task_name'] ?? 'Untitled Task');
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'tasks' => $tasks]);
    exit;
}


/**
 * AJAX function to assign unassigned tasks of a DF in a specific department
 * to a specific user.
 */
public function ajax_assign_task_to_user()
{
    // Set the output content type to JSON
    header('Content-Type: application/json');

    // 1. Basic Security and Validation
    if (!$this->input->is_ajax_request()) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    // You should also add a check here to ensure the logged-in user
    // is an admin/HOD and has permission to do this.
    // if ($this->session->userdata['logged_in']['role'] != 'HOD_ROLE_ID') { ... }

    // 2. Get the POST data sent from JavaScript
    $df_id = $this->input->post('df_id');
    $dept_id = $this->input->post('department_id');
    $user_id = $this->input->post('user_id');

    // 3. Validate the inputs
    if (empty($df_id) || empty($dept_id) || empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'Missing required information.']);
        exit;
    }

    // 4. Prepare the data for the database update
    $update_data = [
        'assigned_user' => $user_id
        // You could also set an 'assigned_on' date here if you have one
        // 'assigned_on' => date('Y-m-d H:i:s')
    ];

    // 5. Build the WHERE clause
    $this->db->where('df_id', $df_id);
    $this->db->where('department_id', $dept_id);

    // This is important: We only update tasks that are currently unassigned.
    // Based on your 'pendingtoassigndf' function, unassigned tasks
    // might have '' (empty string) or 0. This covers all unassigned cases.
    $this->db->group_start();
    $this->db->where('assigned_user', 0);
    $this->db->or_where('assigned_user', '');
    $this->db->or_where('assigned_user IS NULL');
    $this->db->group_end();

    // 6. Execute the update on your tasks table
    $success = $this->db->update('task_department_wise_scheduling', $update_data);

    // 7. Send the JSON response back to the JavaScript
    if ($success) {
        if ($this->db->affected_rows() > 0) {
            echo json_encode(['success' => true, 'message' => 'Tasks assigned successfully.']);
        } else {
            // This means the query ran, but no rows were unassigned (maybe someone else just assigned them)
            echo json_encode(['success' => false, 'message' => 'No unassigned tasks were found for this DF and Department.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
    }

    // Stop script execution after sending the JSON response
    exit;
}


public function management_report()
{
    $this->load->model('Task_model');

    $start_date_str = $this->input->post('start_date');
    $end_date_str = $this->input->post('end_date');

    $start_date = DateTime::createFromFormat('d-m-Y', $start_date_str)->format('Y-m-d');
    $end_date = DateTime::createFromFormat('d-m-Y', $end_date_str)->format('Y-m-d');

    // 1. Get Main KPIs
    $kpi_data = $this->Task_model->get_management_kpi_data($start_date, $end_date);
    
    // 2. NEW: Get Daily Trend Data for Charts
    $trend_data = $this->Task_model->get_daily_completion_trend($start_date, $end_date);

    $data = [
        'start_date' => $start_date_str,
        'end_date' => $end_date_str,
        'kpis' => $kpi_data,
        'trend_data' => $trend_data, // Pass this to the view
        'page_title' => 'Management Report (' . $start_date_str . ' to ' . $end_date_str . ')'
    ];

    $this->load->view('reports/management_report_view', $data);
}

public function management_report_details()
{
    $this->load->model('Task_model');

    // 1. Get parameters from the URL
    $type = $this->input->get('type');
    $start_date_str = $this->input->get('start_date');
    $end_date_str = $this->input->get('end_date');
    
    $start_date = DateTime::createFromFormat('d-m-Y', $start_date_str)->format('Y-m-d');
    $end_date = DateTime::createFromFormat('d-m-Y', $end_date_str)->format('Y-m-d');

    // 2. Get the correct data based on the type
    $data = [
        'start_date' => $start_date_str,
        'end_date' => $end_date_str,
    ];

    switch ($type) {
        case 'released':
            $data['title'] = 'DFs Released';
            $data['results'] = $this->Task_model->get_kpi_details_released($start_date, $end_date);
            break;
        case 'assigned':
            $data['title'] = 'Tasks Assigned';
            $data['results'] = $this->Task_model->get_kpi_details_assigned($start_date, $end_date);
            break;
        case 'completed':
            $data['title'] = 'Tasks Completed';
            $data['results'] = $this->Task_model->get_kpi_details_completed($start_date, $end_date);
            break;
        case 'missed':
            $data['title'] = 'Tasks Missed / Carry Forward';
            $data['results'] = $this->Task_model->get_kpi_details_missed($start_date, $end_date);
            break;
    }

    // 3. Load the details view
    $this->load->view('reports/management_report_details_view', $data);
}

public function daily_df_progress_report()
{
    $this->load->model('Report_model');

    $report_date_input = trim((string) $this->input->get('report_date', true));
    $scope = trim((string) $this->input->get('scope', true));

    $date_object = DateTime::createFromFormat('Y-m-d', $report_date_input);
    $report_date = ($date_object && $date_object->format('Y-m-d') === $report_date_input)
        ? $report_date_input
        : date('Y-m-d');

    $allowed_scopes = [
        'active' => 'Running DF + Closed On Selected Date',
        'running' => 'Running DF Only',
        'closed_today' => 'Closed On Selected Date'
    ];

    if (!isset($allowed_scopes[$scope])) {
        $scope = 'active';
    }

    $report = $this->Report_model->get_daily_df_progress_report($report_date, $scope);
    $summary = $report['summary'];
    $user_summary = $report['user_summary'];

    $insights = [];
    $insights[] = $summary['movement_df_count'] . ' DFs captured visible movement on ' . date('d M Y', strtotime($report_date)) . '.';

    if (!empty($report['top_department'])) {
        $insights[] = $report['top_department']['department'] . ' led the activity pulse with ' . $report['top_department']['count'] . ' logged movements.';
    }

    if (!empty($report['top_active_df'])) {
        $insights[] = $report['top_active_df']['df_no'] . ' was the most active DF with ' . $report['top_active_df']['today_touched_count'] . ' task touches.';
    }

    if ($summary['no_update_df_count'] > 0) {
        $insights[] = $summary['no_update_df_count'] . ' running DFs had no visible update and need leadership follow-up.';
    }

    if ($summary['open_delayed_task_count'] > 0) {
        $insights[] = $summary['open_delayed_task_count'] . ' open delayed tasks are still pending across the visible DF portfolio.';
    }

    if ($summary['new_ticket_count'] > 0) {
        $insights[] = $summary['new_ticket_count'] . ' new open help tickets were raised on the selected date.';
    }

    if ($user_summary['users_with_active_df_tasks'] > 0) {
        $insights[] = $user_summary['users_working_today'] . ' active users showed visible progress updates, while ' . $user_summary['users_no_update_today'] . ' users with running DF task responsibility showed no visible update.';
    }

    $data = [
        'page_title' => 'Daily DF Progress Intelligence Report',
        'report_date' => $report_date,
        'report_date_display' => date('d M Y', strtotime($report_date)),
        'scope' => $scope,
        'scope_options' => $allowed_scopes,
        'scope_label' => $allowed_scopes[$scope],
        'summary' => $summary,
        'user_summary' => $user_summary,
        'department_activity' => $report['department_activity'],
        'movement_breakdown' => $report['movement_breakdown'],
        'user_activity' => $report['user_activity'],
        'top_active_users' => $report['top_active_users'],
        'idle_users' => $report['idle_users'],
        'rows' => $report['rows'],
        'top_attention' => $report['top_attention'],
        'insights' => $insights
    ];

    $this->load->view('reports/daily_df_progress_report_view', $data);
}

public function daily_planned_task_report()
{
    $this->load->model('Report_model');

    $report_date_input = trim((string) $this->input->get('report_date', true));
    $date_object = DateTime::createFromFormat('Y-m-d', $report_date_input);
    $report_date = ($date_object && $date_object->format('Y-m-d') === $report_date_input)
        ? $report_date_input
        : date('Y-m-d');

    $department_input = trim((string) $this->input->get('department_id', true));
    $department_ids = array_values(array_unique(array_filter(array_map(
        'intval',
        explode(',', $department_input)
    ))));
    $selected_department_ids = implode(',', $department_ids);
    $user_id = max(0, (int) $this->input->get('user_id', true));
    $df_id = max(0, (int) $this->input->get('df_id', true));

    $report = $this->Report_model->get_daily_planned_task_report(
        $report_date,
        $department_ids,
        $user_id,
        $df_id
    );
    $filters = $this->Report_model->get_daily_planned_task_filters();

    $data = [
        'page_title' => 'Daily Planned Task Intelligence Report',
        'report_date' => $report_date,
        'report_date_display' => date('d M Y', strtotime($report_date)),
        'selected_department_ids' => $selected_department_ids,
        'user_id' => $user_id,
        'df_id' => $df_id,
        'departments' => $filters['departments'],
        'users' => $filters['users'],
        'running_dfs' => $filters['running_dfs'],
        'summary' => $report['summary'],
        'rows' => $report['rows']
    ];

    $this->load->view('reports/daily_planned_task_report_view', $data);
}

/**
 * AJAX function for the management report to get ticket details
 * for a specific task record.
 */
public function ajax_get_task_tickets($task_record_id = 0)
{
    if ($task_record_id == 0 || !$this->input->is_ajax_request()) {
        show_404();
        return;
    }

    $this->db->select(
        'cts.help_ticket_no, cts.remarks, ' .
        'DATE_FORMAT(cts.added_on, "%d-%m-%Y %h:%i %p") as added_date, ' .
        'CONCAT(u.first_name, " ", u.last_name) as added_by_name, ' .
        'cts.ticket_status' // To show if it's open or closed
    );
    $this->db->from('communication_ticket_system cts');
    $this->db->join('system_users u', 'cts.added_by = u.user_id', 'left');
    $this->db->where('cts.task_record_id', $task_record_id);
    $this->db->order_by('cts.added_on', 'DESC');
    
    $query = $this->db->get();
    $tickets = $query->result_array();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'tickets' => $tickets]);
    exit;
}


public function clone_df_project_form(){

		$this->load->model('Task_model');
	$lead_id=$this->uri->segment(5);

    $result = $this->Task_model->getSpecificTaskDates(date('Y-m-d'));

    // Initialize both dates as null
    $task_103_date = null;
    $task_52_date = null;

    foreach ($result as $task) {
        if ($task['task_id'] == 103) {
            $task_103_date = $task['expected_date'];
        }
        if ($task['task_id'] == 52) {
            $task_52_date = $task['expected_date'];
        }
    }

    // Pass variables to view
    $data['task_103_date'] = $task_103_date;
    $data['task_52_date'] = $task_52_date;

    $today = new DateTime();

// Clone the object to avoid modifying the original
$trialDate = clone $today;
$dispatchDate = clone $today;

// Add 103 and 110 days respectively
$trialDate->modify('+103 days');
$dispatchDate->modify('+110 days');

// Format and store in variables
$trial_date = $trialDate->format('Y-m-d');
$dispatch_date = $dispatchDate->format('Y-m-d');

$data['task_103_date'] = $dispatch_date;
    $data['task_52_date'] = $trial_date;
    

		$this->load->view('dashboard/clone_df_project_form',$data);

	}



	public function add_clone_df_project_form() {
    // --- 1. SETUP & INPUTS ---
    $this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');

    $norml_id = $this->uri->segment(3);
    $po_id    = $this->uri->segment(4);
    $lead_id  = $this->uri->segment(5);
    $user_id  = $this->session->userdata['logged_in']['user_id'];

    // --- 2. BACK BUTTON / DUPLICATE CHECK ---
    // We check if a record with this PO ID already exists before doing anything else.
    $check_duplicate = $this->db->get_where('df_design_form_table', array('po_id' => $po_id));
    
    if ($check_duplicate->num_rows() > 0) {
        // Record already exists!
        $this->session->set_flashdata('error', '<div class="alert alert-warning">This record has already been saved. You cannot submit it twice.</div>');
        redirect(page_url . 'Dashboard'); // Redirect to Dashboard immediately
        return;
    }

    // --- 3. VALIDATION ---
    if ($this->form_validation->run() == FALSE) {
        $this->load->view('dashboard/df_project_form');
        return;
    }

    date_default_timezone_set("Asia/Kolkata");

    // --- 4. DATABASE TRANSACTION START ---
    $this->db->trans_start();

    try {
        // [Table 1] Main Design Form
        $data = array(
            'po_id'            => $po_id,
            'lead_id'          => $lead_id,
            'design_form_date' => $this->input->post('design_form_date'),
            'reference_no'     => $this->input->post('reference_no'),
            'ref_df_date'      => $this->input->post('ref_df_date'),
            'added_on'         => date("Y-m-d H:i:s"),
            'added_by'         => $user_id
        );
        $this->db->insert('df_design_form_table', $data);
        $latest_id = $this->db->insert_id(); 

        // [Table 2] Multi Track Machine
        $data1 = array(
            'record_id'                   => $latest_id,
            'ce_complied'                 => $this->input->post('ce_complied'),
            'ce_complied_remarks'         => $this->input->post('ce_complied_remarks'),
            'date_of_po_remarks'          => $this->input->post('date_of_po_remarks'),
            'penalty_clause'              => $this->input->post('penalty_clause'),
            'penalty_clause_remarks'      => $this->input->post('penalty_clause_remarks'),
            'dispatch_date'               => date('Y-m-d', strtotime($this->input->post('dispatch_date'))),
            'dispatch_date_remarks'       => $this->input->post('dispatch_date_remarks'),
            'trial_date'                  => date('Y-m-d', strtotime($this->input->post('trial_date'))),
            'trial_date_remarks'          => $this->input->post('trial_date_remarks'),
            'machine_mode_no_remarks'     => $this->input->post('machine_mode_no_remarks'),
            'machine_type_remarks'        => $this->input->post('machine_type_remarks'),
            'machine_orientation'         => $this->input->post('machine_orientation'),
            'machine_orientation_remarks' => $this->input->post('machine_orientation_remarks'),
            'added_on'                    => date("Y-m-d H:i:s"),
            'added_by'                    => $user_id
        );
        $this->db->insert('df_form_multi_track_machine', $data1);

        // [Table 3] Machine Specification
        $data3 = array(
            'record_id'                     => $latest_id,
            'tracks_remarks'                => $this->input->post('tracks_remarks'),
            'product_packed_remarks'        => $this->input->post('product_packed_remarks'),
            'filling_unit_remarks'          => $this->input->post('filling_unit_remarks'),
            'product_specification_remarks' => $this->input->post('product_specification_remarks'),
            'profle_sealing_remarks'        => $this->input->post('profle_sealing_remarks'),
            'added_on'                      => date("Y-m-d H:i:s"),
            'added_by'                      => $user_id
        );
        $this->db->insert('df_form_machine_specification', $data3);

        // [Table 4] Multi Track Machine 1
        $data4 = array(
            'record_id' => $latest_id,
            'notching_option' => $this->input->post('notching_option'),
            'notching_option_remarks' => $this->input->post('notching_option_remarks'),
            'hooper_details' => $this->input->post('hooper_details'),
            'openable_option' => $this->input->post('openable_option'),
            'closed_option' => $this->input->post('closed_option'),
            'pressurised_option' => $this->input->post('pressurised_option'),
            'closed_pressurised_option' => $this->input->post('closed_pressurised_option'),
            'non_pressurised_option' => $this->input->post('non_pressurised_option'),
            'non_closed_pressurised_option' => $this->input->post('non_closed_pressurised_option'),
            'hooper_details_remarks' => $this->input->post('hooper_details_remarks'),
            'cladding_provision' => $this->input->post('cladding_provision'),
            'cladding_provision_remarks' => $this->input->post('cladding_provision_remarks'),
            'embossing' => $this->input->post('embossing'),
            'embossing_option' => $this->input->post('embossing_option'),
            'linear_option' => $this->input->post('linear_option'),
            'rotary_option' => $this->input->post('rotary_option'),
            'provision_remarks' => $this->input->post('provision_remarks'),
            'web_aligner' => $this->input->post('web_aligner'),
            'web_aligner_option' => $this->input->post('web_aligner_option'),
            'web_aligner_remarks' => $this->input->post('web_aligner_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine1', $data4);

        // [Table 5] Multi Track Machine 2
        $data5 = array(
            'record_id' => $latest_id,
            'center_slitting' => $this->input->post('center_slitting'),
            'center_slitting_remarks' => $this->input->post('center_slitting_remarks'),
            'vertical_slitting' => $this->input->post('vertical_slitting'),
            'vertical_slitting_remarks' => $this->input->post('vertical_slitting_remarks'),
            'vertical_sealer' => $this->input->post('vertical_sealer'),
            'vertical_sealer_remarks' => $this->input->post('vertical_sealer_remarks'),
            'laminate_pulling' => $this->input->post('laminate_pulling'),
            'laminate_pulling_remarks' => $this->input->post('laminate_pulling_remarks'),
            'up_down' => $this->input->post('up_down'),
            'up_down_remarks' => $this->input->post('up_down_remarks'),
            'embossing_coding' => $this->input->post('embossing_coding'),
            'embossing_coding_remarks' => $this->input->post('embossing_coding_remarks'),
            'cooling_station' => $this->input->post('cooling_station'),
            'cooling_station_remarks' => $this->input->post('cooling_station_remarks'),
            'horizontal_sealer' => $this->input->post('horizontal_sealer'),
            'horizontal_sealer_remarks' => $this->input->post('horizontal_sealer_remarks'),
            'perforation_blade' => $this->input->post('perforation_blade'),
            'perforation_blade_remarks' => $this->input->post('perforation_blade_remarks'),
            'priston_drive' => $this->input->post('priston_drive'),
            'priston_drive_remarks' => $this->input->post('priston_drive_remarks'),
            'shutt_off_nozzle' => $this->input->post('shutt_off_nozzle'),
            'shutt_off_nozzle_remarks' => $this->input->post('shutt_off_nozzle_remarks'),
            'filling_plate_drive' => $this->input->post('filling_plate_drive'),
            'filling_plate_drive_remarks' => $this->input->post('filling_plate_drive_remarks'),
            'individual_weight' => $this->input->post('individual_weight'),
            'individual_weight_remarks' => $this->input->post('individual_weight_remarks'),
            'overall_weight_adjust' => $this->input->post('overall_weight_adjust'),
            'overall_weight_adjust_remarks' => $this->input->post('overall_weight_adjust_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine2', $data5);

        // [Table 6] Multi Track Machine 3
        $data6 = array(
            'record_id' => $latest_id,
            'traverse_drive' => $this->input->post('traverse_drive'),
            'yes_traverse_drive' => $this->input->post('yes_traverse_drive'),
            'traverse_drive_remarks' => $this->input->post('traverse_drive_remarks'),
            'printer_yes_no' => $this->input->post('printer_yes_no'),
            'printer' => $this->input->post('printer'),
            'inkjet_option' => $this->input->post('inkjet_option'),
            'tto_option' => $this->input->post('tto_option'),
            'thermal_inkjet_option' => $this->input->post('thermal_inkjet_option'),
            'printer_remarks' => $this->input->post('printer_remarks'),
            'case_packer_drive' => $this->input->post('case_packer_drive'),
            'case_packer_drive_remarks' => $this->input->post('case_packer_drive_remarks'),
            'nozzle_funnel' => $this->input->post('nozzle_funnel'),
            'powder_option1' => $this->input->post('powder_option1'),
            'liquid_option1' => $this->input->post('liquid_option1'),
            'liquid_shut_option1' => $this->input->post('liquid_shut_option1'),
            'nozzle_funnel_remarks' => $this->input->post('nozzle_funnel_remarks'),
            'hose_pipe' => $this->input->post('hose_pipe'),
            'hose_pipe_remarks' => $this->input->post('hose_pipe_remarks'),
            'string_option' => $this->input->post('string_option'),
            'batch_cut_format_remarks' => $this->input->post('batch_cut_format_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine3', $data6);

        // [Table 7] Multi Track Machine 4
        $data7 = array(
            'record_id' => $latest_id,
            'horizontal_sealer1_remarks' => $this->input->post('horizontal_sealer1_remarks'),
            'vertical_sealer1_remarks' => $this->input->post('vertical_sealer1_remarks'),
            'rotary_valve_coating' => $this->input->post('rotary_valve_coating'),
            'rotary_valve_coating_remarks' => $this->input->post('rotary_valve_coating_remarks'),
            'working_speed' => $this->input->post('working_speed'),
            'working_speed_remarks' => $this->input->post('working_speed_remarks'),
            'reel_shaft_type' => $this->input->post('reel_shaft_type'),
            'reel_shaft_type_remarks' => $this->input->post('reel_shaft_type_remarks'),
            'reel_core_diameter' => $this->input->post('reel_core_diameter'),
            'reel_core_diameter_remarks' => $this->input->post('reel_core_diameter_remarks'),
            'trial_material' => $this->input->post('trial_material'),
            'trial_material_remarks' => $this->input->post('trial_material_remarks'),
            'laminate_detail' => $this->input->post('laminate_detail'),
            'laminate_detail_remarks' => $this->input->post('laminate_detail_remarks'),
            'heater_control_system' => $this->input->post('heater_control_system'),
            'heater_control_system_remarks' => $this->input->post('heater_control_system_remarks'),
            'beacon_light' => $this->input->post('beacon_light'),
            'beacon_light_remarks' => $this->input->post('beacon_light_remarks'),
            'hooper_level' => $this->input->post('hooper_level'),
            'hooper_level_option' => $this->input->post('hooper_level_option'),
            'hooper_level_remarks' => $this->input->post('hooper_level_remarks'),
            'safety_relay' => $this->input->post('safety_relay'),
            'safety_relay_remarks' => $this->input->post('safety_relay_remarks'),
            'plc_maker' => $this->input->post('plc_maker'),
            'plc_maker_remarks' => $this->input->post('plc_maker_remarks'),
            'hmi_size' => $this->input->post('hmi_size'),
            'hmi_size_remarks' => $this->input->post('hmi_size_remarks'),
            'cip_system' => $this->input->post('cip_system'),
            'cip_system_option' => $this->input->post('cip_system_option'),
            'cip_system_option_remarks' => $this->input->post('cip_system_option_remarks'),
            'tool_kit' => $this->input->post('tool_kit'),
            'tool_kit_remarks' => $this->input->post('tool_kit_remarks'),
            'changeover_part' => $this->input->post('changeover_part'),
            'changeover_part_remarks' => $this->input->post('changeover_part_remarks'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine4', $data7);

        // [Table 8] Multi Track Machine 5
        $data8 = array(
            'record_id' => $latest_id,
            'secondary_pack' => $this->input->post('secondary_pack'),
            'case_packer' => $this->input->post('case_packer'),
            'secondary_pack_remarks' => $this->input->post('secondary_pack_remarks'),
            'ladder_platform' => $this->input->post('ladder_platform'),
            'ladder_platform_remarks' => $this->input->post('ladder_platform_remarks'),
            'machine_guarding' => $this->input->post('machine_guarding'),
            'aluminium_option' => $this->input->post('aluminium_option'),
            'ss_304_option' => $this->input->post('ss_304_option'),
            'machine_guarding_remarks' => $this->input->post('machine_guarding_remarks'),
            'special_notes' => $this->input->post('special_notes'),
            'added_on' => date("Y-m-d H:i:s"),
            'added_by' => $user_id
        );
        $this->db->insert('df_form_multi_track_machine5', $data8);

        // [Secondary Packs Loop]
        $yes_secondary_pack = $this->input->post('yes_secondary_pack'); 
        $yes_secondary_pack_add = $this->input->post('yes_secondary_pack_add');
        $qty = $this->input->post('yes_secondary_pack_qty');
        $qty_add = $this->input->post('yes_secondary_pack_qty_add');
		$remark = $this->input->post('yes_secondary_pack_remark');
$remark_add = $this->input->post('yes_secondary_pack_remark_add');

        if(is_array($yes_secondary_pack_add)) {
            if(!is_array($yes_secondary_pack)) $yes_secondary_pack = [];
            $yes_secondary_pack = array_merge($yes_secondary_pack, $yes_secondary_pack_add);
        }
        if(is_array($qty_add)) {
             if(!is_array($qty)) $qty = [];
             $qty = array_merge($qty, $qty_add);
        }

		if(is_array($remark_add)) {
    if(!is_array($remark)) $remark = [];
    $remark = array_merge($remark, $remark_add);
}

        if (!empty($yes_secondary_pack)) {
            if (!is_array($yes_secondary_pack)) $yes_secondary_pack = [$yes_secondary_pack];
            if (!is_array($qty)) $qty = [$qty];

            for ($i = 0; $i < count($yes_secondary_pack); $i++) {
                if (empty($yes_secondary_pack[$i])) continue;

                $pack = [
                    'record_id' => $latest_id,
                    'yes_secondary_pack' => $yes_secondary_pack[$i],
                    'yes_secondary_pack_qty' => $qty[$i] ?? '',
					'yes_secondary_pack_remark' => $remark[$i] ?? '',
                    'added_on' => date("Y-m-d H:i:s"),
                    'added_by' => $user_id
                ];
                $this->db->insert('df_form_multi_track_machine_pack', $pack);
            }
        }

        // [Tasks Updates]
        $data34 = array(
            'df_id' => 0,
            'task_status' => 1,
            'task_completed_on' => date('Y-m-d H:i:s'),
            'task_completed_by' => $user_id,
            'userid' => $user_id
        );
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('task_department_wise_scheduling', $data34);

        $pono = $this->getpono($this->uri->segment(3));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $nextWorkingDate = $this->iftomorrowisholiday($tomorrow);

        $newTaskData = array(
            'df_id' => 0,
            'taskid' => 86,
            'department_id' => 9,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime($nextWorkingDate)),
            'added_on' => date('Y-m-d H:i:s'),
            'added_by' => $user_id,
            'po_id' => $pono,
            'task_status' => 0,
            'remarks' => '',
            'assigned_user' => $user_id,
            'userid' => $user_id,
            'assigned_by' => $user_id,
            'assigned_on' => date('Y-m-d H:i:s')
        );
        $this->db->insert('task_department_wise_scheduling', $newTaskData);

        // --- 5. TRANSACTION COMPLETE & BRIDGE LOGIC ---
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">Error saving data. Please try again.</div>');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('success', '<div class="alert alert-success"> Thank You! Data successfully added.</div>');

            // URL for PDF (To be opened in New Tab)
            $pdf_url = page_url . 'Formats/df_form/' . $po_id . '/' . $lead_id . '/' . $latest_id;
            
            // URL for Dashboard (To be redirected to on Current Tab)
            $dashboard_url = page_url . 'Dashboard';

            // Pass both URLs to the View
            $data_view = [
                'pdf_url'      => $pdf_url,
                'redirect_url' => $dashboard_url,
            ];
            
            $this->load->view('dashboard/df_submit_bridge', $data_view);
        }
    } catch (Exception $e) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('error', '<div class="alert alert-danger">System Error: ' . $e->getMessage() . '</div>');
        redirect($_SERVER['HTTP_REFERER']);
    }
}


    public function customised_df_workspace($po_id = 0, $lead_id = 0, $record_id = 0)
    {
        $po_id = (int) $po_id;
        $lead_id = (int) $lead_id;
        $record_id = (int) $record_id;

        $access = $this->get_customised_df_access_context(0, $po_id, $lead_id);

        if (empty($access['context']) || empty($access['context']['df_id'])) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">DF context could not be identified for this order.</div>');
            redirect(page_url . 'Dashboard');
            return;
        }

        if (!$access['allowed']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Only the marketing owner or DF management users can open Customised DF.</div>');
            redirect(page_url . 'Dashboard/edit_df_project_form/' . $po_id . '/' . $lead_id . '/' . $record_id);
            return;
        }

        $df_id = (int) $access['context']['df_id'];
        $data = array(
            'page_title' => 'Customised DF Workspace',
            'df_id' => $df_id,
            'po_id' => $po_id,
            'lead_id' => $lead_id,
            'record_id' => $record_id,
            'scope' => $access['scope'],
            'workspace_context' => $access['context'],
            'is_marketing_owner' => $access['is_marketing_owner'],
            'back_url' => page_url . 'Dashboard/edit_df_project_form/' . $po_id . '/' . $lead_id . '/' . $record_id,
            'detail_url' => page_url . 'Dashboard/df_full_detail?df_id=' . $df_id
        );

        $this->load->view('dashboard/customised_df_workspace', $data);
    }

    public function ajax_customised_df_save()
    {
        $df_id = (int) $this->input->post('df_id');
        $save_mode = trim((string) $this->input->post('save_mode'));
        if (!in_array($save_mode, array('workspace_update', 'department_update', 'department_schedule'), true)) {
            $save_mode = 'workspace_update';
        }
        $access = $this->get_customised_df_access_context($df_id, 0, 0);

        if (empty($access['context']) || empty($access['context']['df_id'])) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'DF workspace context could not be found.'
                )));
        }

        if (!$access['allowed']) {
            return $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'You are not allowed to update this Customised DF plan.'
                )));
        }

        $tasks = $this->input->post('tasks');
        if (!is_array($tasks)) {
            $decoded_tasks = json_decode((string) $tasks, true);
            $tasks = is_array($decoded_tasks) ? $decoded_tasks : array();
        }

        if (empty($tasks)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Please update at least one task before saving.'
                )));
        }

        $task_ids = array();
        foreach ($tasks as $task_payload) {
            $task_record_id = isset($task_payload['task_id']) ? (int) $task_payload['task_id'] : 0;
            if ($task_record_id > 0) {
                $task_ids[] = $task_record_id;
            }
        }

        $task_ids = array_values(array_unique($task_ids));
        if (empty($task_ids)) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'No valid task rows were submitted for saving.'
                )));
        }

        $task_query = $this->db->select('id, df_id, department_id, assigned_user, start_date, end_date, task_status')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $df_id)
            ->where_in('id', $task_ids)
            ->where('IFNULL(on_hold, 0) = 0', null, false)
            ->where_in('task_status', array(0, 2))
            ->get();

        $task_rows = array();
        foreach ($task_query->result_array() as $row) {
            $task_rows[(int) $row['id']] = $row;
        }

        if (count($task_rows) !== count($task_ids)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'One or more task rows are no longer editable. Please refresh the workspace.'
                )));
        }

        $this->load->model('Customised_df_model', 'customisedDfModel');
        if (!$this->customisedDfModel->ensure_activity_table()) {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Customised DF tracking storage could not be prepared.'
                )));
        }

        date_default_timezone_set("Asia/Kolkata");
        $current_user_id = (int) $access['user_id'];
        $now = date('Y-m-d H:i:s');
        $pending_notifications = array();
        $scheduled_department_ids = array();
        $common_schedule_date = null;
        $has_common_schedule_date = ($save_mode === 'department_schedule');

        $this->db->trans_begin();

        foreach ($tasks as $task_payload) {
            $task_record_id = isset($task_payload['task_id']) ? (int) $task_payload['task_id'] : 0;
            if ($task_record_id <= 0 || !isset($task_rows[$task_record_id])) {
                $this->db->trans_rollback();
                return $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'success' => false,
                        'message' => 'A submitted task row could not be matched to the current DF plan.'
                    )));
            }

            $task_row = $task_rows[$task_record_id];
            $scheduled_department_ids[(int) $task_row['department_id']] = true;
            $assigned_user_id = isset($task_payload['assigned_user']) && $task_payload['assigned_user'] !== ''
                ? (int) $task_payload['assigned_user']
                : 0;
            $start_date = trim((string) (isset($task_payload['start_date']) ? $task_payload['start_date'] : ''));
            $end_date = trim((string) (isset($task_payload['end_date']) ? $task_payload['end_date'] : ''));

            if ($start_date === '' || $end_date === '') {
                $this->db->trans_rollback();
                return $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'success' => false,
                        'message' => 'Start date and due date are required for every Customised DF task.'
                    )));
            }

            $start_obj = DateTime::createFromFormat('Y-m-d', $start_date);
            $end_obj = DateTime::createFromFormat('Y-m-d', $end_date);

            if (!$start_obj || $start_obj->format('Y-m-d') !== $start_date || !$end_obj || $end_obj->format('Y-m-d') !== $end_date) {
                $this->db->trans_rollback();
                return $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'success' => false,
                        'message' => 'Please use valid task dates in YYYY-MM-DD format.'
                    )));
            }

            if ($start_date > $end_date) {
                $this->db->trans_rollback();
                return $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'success' => false,
                        'message' => 'Task start date cannot be after the due date.'
                    )));
            }

            if ($has_common_schedule_date) {
                if ($start_date !== $end_date) {
                    $has_common_schedule_date = false;
                } elseif ($common_schedule_date === null) {
                    $common_schedule_date = $start_date;
                } elseif ($common_schedule_date !== $start_date) {
                    $has_common_schedule_date = false;
                }
            }

            if ($assigned_user_id > 0) {
                $user_row = $this->db->select('user_id')
                    ->from('system_users')
                    ->where('user_id', $assigned_user_id)
                    ->where('department_id', (int) $task_row['department_id'])
                    ->where('user_status', 1)
                    ->where('hide_profile', 0)
                    ->limit(1)
                    ->get()
                    ->row();

                if (!$user_row) {
                    $this->db->trans_rollback();
                    return $this->output
                        ->set_status_header(422)
                        ->set_content_type('application/json')
                        ->set_output(json_encode(array(
                            'success' => false,
                            'message' => 'Selected owner is not active in the mapped department for one of the tasks.'
                        )));
                }
            }

            $assignment_changed = ((int) $task_row['assigned_user'] !== $assigned_user_id);
            $date_changed = ($task_row['start_date'] !== $start_date || $task_row['end_date'] !== $end_date);

            if (!$assignment_changed && !$date_changed) {
                continue;
            }

            $update_data = array(
                'assigned_user' => $assigned_user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'changedBy' => $current_user_id,
                'changedOn' => $now
            );

            if ($assignment_changed) {
                $update_data['assigned_by'] = $current_user_id;
                $update_data['assigned_on'] = $now;
            }

            if ($date_changed) {
                $update_data['end_date_changed'] = 1;
            }

            $this->db->where('id', $task_record_id);
            $this->db->where('df_id', $df_id);
            $this->db->update('task_department_wise_scheduling', $update_data);

            if ($assignment_changed && $assigned_user_id > 0) {
                $pending_notifications[] = array(
                    'df_id' => $df_id,
                    'assigned_user_id' => $assigned_user_id
                );
            }
        }

        $activity_saved = $this->customisedDfModel->record_activity(array(
            'df_id' => $df_id,
            'activity_type' => $save_mode,
            'task_count' => count($task_ids),
            'department_count' => count($scheduled_department_ids),
            'schedule_date' => $has_common_schedule_date ? $common_schedule_date : null,
            'scheduled_by' => $current_user_id,
            'scheduled_on' => $now
        ));

        if (!$activity_saved) {
            $this->db->trans_rollback();
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'The Customised DF plan could not be tracked, so no changes were saved.'
                )));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'The Customised DF plan could not be saved right now.'
                )));
        }

        $this->db->trans_commit();

        if (!empty($pending_notifications)) {
            $this->load->model('Task_model', 'taskModel');
            $notification_message = 'A task has been updated in Customised DF. Please review the latest owner and dates.';
            foreach ($pending_notifications as $notification_row) {
                $this->taskModel->sendnotificationtorespectiveteammemberforassignment(
                    $notification_row['df_id'],
                    $notification_message,
                    $notification_row['assigned_user_id'],
                    $current_user_id
                );
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success' => true,
                'message' => 'Customised DF dates saved successfully.'
            )));
    }

    public function df_live_manager()
    {
        $scope = $this->get_df_live_manager_scope();

        if (!$scope['can_manage']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">You do not have access to manage DF dates and assignments.</div>');
            redirect(page_url . 'Dashboard');
            return;
        }

        $data = array(
            'page_title' => 'Live DF Date & Assignment Manager',
            'scope' => $scope,
            'departments' => $this->get_df_live_manager_departments($scope),
            'dfs' => $this->get_df_live_manager_dfs($scope)
        );

        $this->load->view('dashboard/df_live_manager_view', $data);
    }

    public function ajax_df_live_manager_data()
    {
        $scope = $this->get_df_live_manager_scope();

        if (!$scope['can_manage']) {
            return $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Access denied.'
                )));
        }

        $today = date('Y-m-d');
        $next_seven_days = date('Y-m-d', strtotime('+7 days'));

        $filters = array(
            'df_id' => trim((string) $this->input->get('df_id')),
            'department_id' => trim((string) $this->input->get('department_id')),
            'assigned_user' => trim((string) $this->input->get('assigned_user')),
            'due_view' => trim((string) $this->input->get('due_view'))
        );

        $this->db->select("
            a.id as task_record_id,
            a.df_id,
            a.department_id,
            a.task_status,
            a.assigned_user,
            a.start_date,
            a.end_date,
            a.assigned_on,
            a.changedOn,
            a.task_completed_on,
            b.df_no,
            b.df_description,
            c.task_name,
            d.department,
            p.company_name,
            CONCAT(IFNULL(u.first_name, ''), ' ', IFNULL(u.last_name, '')) as assigned_user_name,
            CONCAT(IFNULL(m.first_name, ''), ' ', IFNULL(m.last_name, '')) as marketing_person
        ", false);
        $this->db->from('task_department_wise_scheduling a');
        $this->db->join('df_release b', 'a.df_id = b.id', 'inner');
        $this->db->join('task_management c', 'a.taskid = c.task_id', 'left');
        $this->db->join('departments d', 'a.department_id = d.department_id', 'left');
        $this->db->join('system_users u', 'a.assigned_user = u.user_id', 'left');
        $this->db->join('poreceived p', 'a.po_id = p.id', 'left');
        $this->db->join('system_users m', 'p.added_by = m.user_id', 'left');
        $this->db->where('IFNULL(a.on_hold, 0) = 0', null, false);
        $this->db->where('a.df_id >', 0);
        $this->db->where('IFNULL(b.df_status, 0) = 0', null, false);
        $this->db->where('IFNULL(b.on_hold, 0) = 0', null, false);

        if ($scope['user_type'] == 2 && !empty($scope['department_ids'])) {
            $this->db->where_in('a.department_id', $scope['department_ids']);
        }

        if ($filters['df_id'] !== '') {
            $this->db->where('a.df_id', (int) $filters['df_id']);
        }
        $this->db->where('a.task_status', 0);

        if ($filters['department_id'] !== '') {
            $this->db->where('a.department_id', (int) $filters['department_id']);
        }

        if ($filters['assigned_user'] !== '') {
            if ($filters['assigned_user'] === '0') {
                $this->db->group_start();
                $this->db->where('a.assigned_user', 0);
                $this->db->or_where('a.assigned_user', '');
                $this->db->or_where('a.assigned_user IS NULL', null, false);
                $this->db->group_end();
            } else {
                $this->db->where('a.assigned_user', (int) $filters['assigned_user']);
            }
        }

        if ($filters['due_view'] === 'overdue') {
            $this->db->where('a.end_date <', $today);
        } elseif ($filters['due_view'] === 'today') {
            $this->db->where('a.end_date', $today);
        } elseif ($filters['due_view'] === 'week') {
            $this->db->where('a.end_date >=', $today);
            $this->db->where('a.end_date <=', $next_seven_days);
        }

        $this->db->order_by('a.end_date', 'asc');
        $this->db->order_by('b.df_no', 'asc');
        $query = $this->db->get();

        $rows = array();
        $summary = array(
            'visible_rows' => 0,
            'overdue_rows' => 0,
            'today_rows' => 0,
            'unassigned_rows' => 0
        );

        $selected_department_ids = array();
        $row_department_ids = array();

        foreach ($query->result_array() as $row) {
            $summary['visible_rows']++;
            $row_department_ids[] = (int) $row['department_id'];

            $assigned_user_id = !empty($row['assigned_user']) ? (int) $row['assigned_user'] : 0;
            $assigned_user_name = $this->format_df_live_manager_title_case($row['assigned_user_name']);
            if ($assigned_user_name === '') {
                $assigned_user_name = 'Unassigned';
            }

            $task_status = (int) $row['task_status'];
            $is_editable = ($task_status === 0);
            $end_date = $row['end_date'];
            $diff_days = 0;
            $due_state = 'On Track';
            $due_class = 'status-on-track';

            if (!empty($end_date)) {
                $today_ts = strtotime($today);
                $end_ts = strtotime($end_date);

                if ($end_ts < $today_ts) {
                    $diff_days = (int) floor(($today_ts - $end_ts) / 86400);
                    $due_state = 'Overdue by ' . $diff_days . ' day' . ($diff_days === 1 ? '' : 's');
                    $due_class = 'status-overdue';
                    $summary['overdue_rows']++;
                } elseif ($end_date === $today) {
                    $due_state = 'Due Today';
                    $due_class = 'status-today';
                    $summary['today_rows']++;
                } else {
                    $diff_days = (int) floor(($end_ts - $today_ts) / 86400);
                    if ($diff_days <= 7) {
                        $due_state = 'Due in ' . $diff_days . ' day' . ($diff_days === 1 ? '' : 's');
                        $due_class = 'status-soon';
                    }
                }
            }

            if ($assigned_user_id === 0 && $is_editable) {
                $summary['unassigned_rows']++;
            }

            $last_updated_raw = !empty($row['changedOn']) ? $row['changedOn'] : $row['assigned_on'];
            $last_updated_label = 'Not updated yet';
            if (!empty($last_updated_raw) && $last_updated_raw !== '0000-00-00 00:00:00' && strtotime($last_updated_raw) !== false) {
                $last_updated_label = date('d-M-Y h:i A', strtotime($last_updated_raw));
            }

            $marketing_person = $this->format_df_live_manager_title_case($row['marketing_person']);
            if ($marketing_person === '') {
                $marketing_person = 'N/A';
            }

            $rows[] = array(
                'task_record_id' => (int) $row['task_record_id'],
                'df_id' => (int) $row['df_id'],
                'df_no' => $row['df_no'],
                'df_description' => $row['df_description'],
                'department_id' => (int) $row['department_id'],
                'department_name' => $this->format_df_live_manager_title_case($row['department']),
                'task_name' => $row['task_name'],
                'company_name' => $row['company_name'],
                'marketing_person' => $marketing_person,
                'task_status' => $task_status,
                'task_status_label' => 'Pending',
                'is_editable' => $is_editable,
                'assigned_user_id' => $assigned_user_id,
                'assigned_user_name' => $assigned_user_name,
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'due_state' => $due_state,
                'due_class' => $due_class,
                'last_updated_label' => $last_updated_label
            );
        }

        if ($filters['department_id'] !== '') {
            $selected_department_ids[] = (int) $filters['department_id'];
        } elseif ($filters['df_id'] !== '') {
            $selected_department_ids = array_values(array_unique(array_filter($row_department_ids)));
        }

        $response = array(
            'success' => true,
            'meta' => array(
                'scope_label' => $scope['scope_label'],
                'refreshed_at' => date('d-M-Y h:i:s A'),
                'selected_df_id' => $filters['df_id']
            ),
            'summary' => $summary,
            'department_users' => $this->get_df_live_manager_users_by_department($scope, $selected_department_ids),
            'rows' => $rows
        );

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function ajax_df_live_manager_update()
    {
        $scope = $this->get_df_live_manager_scope();

        if (!$scope['can_manage']) {
            return $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Access denied.'
                )));
        }

        $task_id = (int) $this->input->post('task_id');
        $assigned_user = trim((string) $this->input->post('assigned_user'));
        $start_date = trim((string) $this->input->post('start_date'));
        $end_date = trim((string) $this->input->post('end_date'));

        $assigned_user_id = ($assigned_user === '') ? 0 : (int) $assigned_user;

        if ($task_id <= 0 || $start_date === '' || $end_date === '') {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Task, start date and due date are required.'
                )));
        }

        $start_obj = DateTime::createFromFormat('Y-m-d', $start_date);
        $end_obj = DateTime::createFromFormat('Y-m-d', $end_date);

        if (!$start_obj || $start_obj->format('Y-m-d') !== $start_date || !$end_obj || $end_obj->format('Y-m-d') !== $end_date) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Please enter valid dates.'
                )));
        }

        if ($start_date > $end_date) {
            return $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Start date cannot be after due date.'
                )));
        }

        $this->db->select('a.id, a.df_id, a.department_id, a.assigned_user, a.start_date, a.end_date');
        $this->db->from('task_department_wise_scheduling a');
        $this->db->join('df_release b', 'a.df_id = b.id', 'inner');
        $this->db->where('a.id', $task_id);
        $this->db->where('a.task_status', 0);
        $this->db->where('IFNULL(a.on_hold, 0) = 0', null, false);
        $this->db->where('a.df_id >', 0);
        $this->db->where('IFNULL(b.df_status, 0) = 0', null, false);
        $this->db->where('IFNULL(b.on_hold, 0) = 0', null, false);

        if ($scope['user_type'] == 2 && !empty($scope['department_ids'])) {
            $this->db->where_in('a.department_id', $scope['department_ids']);
        }

        $task_row = $this->db->get()->row();

        if (!$task_row) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Task not found in your visible scope.'
                )));
        }

        if ($assigned_user_id > 0) {
            $user_row = $this->db->select('user_id')
                ->from('system_users')
                ->where('user_id', $assigned_user_id)
                ->where('department_id', $task_row->department_id)
                ->where('user_status', 1)
                ->where('hide_profile', 0)
                ->get()
                ->row();

            if (!$user_row) {
                return $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'success' => false,
                        'message' => 'Selected user is not active in this department.'
                    )));
            }
        }

        $current_user_id = (int) $scope['user_id'];
        $now = date('Y-m-d H:i:s');
        $assignment_changed = ((int) $task_row->assigned_user !== $assigned_user_id);
        $date_changed = ($task_row->start_date !== $start_date || $task_row->end_date !== $end_date);

        $update_data = array(
            'assigned_user' => $assigned_user_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'changedBy' => $current_user_id,
            'changedOn' => $now
        );

        if ($assignment_changed) {
            $update_data['assigned_by'] = $current_user_id;
            $update_data['assigned_on'] = $now;
        }

        if ($date_changed) {
            $update_data['end_date_changed'] = 1;
        }

        $this->db->where('id', $task_id);
        $this->db->update('task_department_wise_scheduling', $update_data);

        if ($assignment_changed && $assigned_user_id > 0) {
            $this->load->model('Task_model', 'taskModel');
            $message = 'A task has been updated in Live DF Manager. Please login to review the latest assigned owner and dates.';
            $this->taskModel->sendnotificationtorespectiveteammemberforassignment($task_row->df_id, $message, $assigned_user_id, $current_user_id);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success' => true,
                'message' => 'Task updated successfully.'
            )));
    }

    private function get_df_live_manager_scope()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $user_role = $this->session->userdata['logged_in']['role'];
        $super_admin_ids = array(189, 209);
        $super_admin_roles = $this->Dashboard_model->getsuperadminuserole();

        $is_super_admin = false;
        if (!empty($super_admin_roles) && in_array($user_role, $super_admin_roles)) {
            $is_super_admin = true;
        }
        if (in_array($user_id, $super_admin_ids)) {
            $is_super_admin = true;
        }

        if ($is_super_admin) {
            return array(
                'user_id' => $user_id,
                'user_type' => 1,
                'scope_label' => 'Full Management View',
                'can_manage' => true,
                'department_ids' => array()
            );
        }

        $department_ids = array();
        $dept_query = $this->db->select('department_id')
            ->from('prestogroup_teams')
            ->where('team_leader', $user_id)
            ->get();

        if ($dept_query->num_rows() > 0) {
            foreach ($dept_query->result() as $dept_row) {
                $department_ids[] = (int) $dept_row->department_id;
            }
        }

        if ($user_id === 215) {
            $department_ids[] = 12;
        }

        $department_ids = array_values(array_unique(array_filter($department_ids)));

        if (!empty($department_ids)) {
            return array(
                'user_id' => $user_id,
                'user_type' => 2,
                'scope_label' => 'Department / Team View',
                'can_manage' => true,
                'department_ids' => $department_ids
            );
        }

        return array(
            'user_id' => $user_id,
            'user_type' => 3,
            'scope_label' => 'Personal Work View',
            'can_manage' => false,
            'department_ids' => array()
        );
    }

    private function get_customised_df_access_context($df_id = 0, $po_id = 0, $lead_id = 0)
    {
        $context = $this->get_customised_df_context($df_id, $po_id, $lead_id);
        $scope = $this->get_df_live_manager_scope();
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];
        $marketing_user_id = !empty($context['marketing_user_id']) ? (int) $context['marketing_user_id'] : 0;
        $is_marketing_owner = ($marketing_user_id > 0 && $marketing_user_id === $user_id);

        return array(
            'allowed' => ($scope['can_manage'] || $is_marketing_owner),
            'context' => $context,
            'scope' => $scope,
            'user_id' => $user_id,
            'is_marketing_owner' => $is_marketing_owner
        );
    }

    private function get_customised_df_context($df_id = 0, $po_id = 0, $lead_id = 0)
    {
        $this->db->select('
            po.id as po_id,
            po.lead_id,
            po.df_id,
            po.pono,
            po.company_name,
            IFNULL(po.basic_machine, "") as machine_name,
            po.added_by as marketing_user_id,
            df.df_no,
            IFNULL(df.df_description, "") as df_description,
            marketing.title as marketing_title,
            marketing.first_name as marketing_first_name,
            marketing.last_name as marketing_last_name
        ');
        $this->db->from('poreceived as po');
        $this->db->join('df_release as df', 'df.id = po.df_id', 'left');
        $this->db->join('system_users as marketing', 'marketing.user_id = po.added_by', 'left');

        if ((int) $po_id > 0) {
            $this->db->where('po.id', (int) $po_id);
        } elseif ((int) $df_id > 0) {
            $this->db->where('po.df_id', (int) $df_id);
        }

        if ((int) $lead_id > 0) {
            $this->db->where('po.lead_id', (int) $lead_id);
        }

        $this->db->order_by('po.id', 'DESC');
        $context = $this->db->get()->row_array();

        if (!empty($context)) {
            $context['marketing_person'] = $this->format_df_detail_person_name(
                isset($context['marketing_title']) ? $context['marketing_title'] : '',
                isset($context['marketing_first_name']) ? $context['marketing_first_name'] : '',
                isset($context['marketing_last_name']) ? $context['marketing_last_name'] : ''
            );
        }

        return $context;
    }

    private function get_df_live_manager_departments($scope)
    {
        $this->db->distinct();
        $this->db->select('d.department_id, d.department');
        $this->db->from('task_department_wise_scheduling a');
        $this->db->join('df_release b', 'a.df_id = b.id', 'inner');
        $this->db->join('departments d', 'a.department_id = d.department_id', 'inner');
        $this->db->where('a.task_status', 0);
        $this->db->where('IFNULL(a.on_hold, 0) = 0', null, false);
        $this->db->where('a.df_id >', 0);
        $this->db->where('IFNULL(b.df_status, 0) = 0', null, false);
        $this->db->where('IFNULL(b.on_hold, 0) = 0', null, false);

        if ($scope['user_type'] == 2 && !empty($scope['department_ids'])) {
            $this->db->where_in('a.department_id', $scope['department_ids']);
        }

        $this->db->order_by('d.department', 'asc');
        return $this->db->get()->result_array();
    }

    private function get_df_live_manager_dfs($scope)
    {
        $this->db->distinct();
        $this->db->select('b.id as df_id, b.df_no, b.df_description');
        $this->db->from('task_department_wise_scheduling a');
        $this->db->join('df_release b', 'a.df_id = b.id', 'inner');
        $this->db->where('a.df_id >', 0);
        $this->db->where('a.task_status', 0);
        $this->db->where('IFNULL(a.on_hold, 0) = 0', null, false);
        $this->db->where('IFNULL(b.df_status, 0) = 0', null, false);
        $this->db->where('IFNULL(b.on_hold, 0) = 0', null, false);

        if ($scope['user_type'] == 2 && !empty($scope['department_ids'])) {
            $this->db->where_in('a.department_id', $scope['department_ids']);
        }

        $this->db->order_by('b.df_no', 'asc');
        return $this->db->get()->result_array();
    }

    private function get_df_live_manager_users_by_department($scope, $department_ids = array())
    {
        $use_department_filter = !empty($department_ids);

        if (!$use_department_filter && $scope['user_type'] == 2 && !empty($scope['department_ids'])) {
            $department_ids = $scope['department_ids'];
            $use_department_filter = true;
        }

        if (!$use_department_filter && $scope['user_type'] == 1) {
            $active_departments = $this->get_df_live_manager_departments($scope);
            foreach ($active_departments as $department) {
                $department_ids[] = (int) $department['department_id'];
            }
            $department_ids = array_values(array_unique(array_filter($department_ids)));
            $use_department_filter = !empty($department_ids);
        }

        $this->db->select("
            u.user_id,
            u.department_id,
            CONCAT(u.first_name, ' ', u.last_name) as user_name,
            COUNT(DISTINCT CASE
                WHEN t.df_id > 0
                AND t.task_status IN (0, 2)
                AND IFNULL(t.on_hold, 0) = 0
                AND IFNULL(df.df_status, 0) = 0
                AND IFNULL(df.on_hold, 0) = 0
                THEN t.df_id
                ELSE NULL
            END) as active_df_count
        ", false);
        $this->db->from('system_users u');
        $this->db->join('task_department_wise_scheduling t', 'u.user_id = t.assigned_user', 'left');
        $this->db->join('df_release df', 'df.id = t.df_id', 'left');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.hide_profile', 0);

        if ($use_department_filter) {
            $this->db->where_in('u.department_id', $department_ids);
        }

        $this->db->group_by('u.user_id, u.department_id, user_name');
        $this->db->order_by('u.department_id', 'asc');
        $this->db->order_by('active_df_count', 'asc');
        $this->db->order_by('user_name', 'asc');
        $query = $this->db->get();

        $users_by_department = array();
        foreach ($query->result_array() as $row) {
            $department_id = (int) $row['department_id'];
            if (!isset($users_by_department[$department_id])) {
                $users_by_department[$department_id] = array();
            }

            $label = $this->format_df_live_manager_title_case($row['user_name']);
            if ($label === '') {
                continue;
            }

            $users_by_department[$department_id][] = array(
                'user_id' => (int) $row['user_id'],
                'name' => $label,
                'active_df_count' => (int) $row['active_df_count'],
                'label' => $label . ' (' . (int) $row['active_df_count'] . ' Active DFs)'
            );
        }

        return $users_by_department;
    }

    private function format_df_live_manager_title_case($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = preg_replace('/\s+/', ' ', $value);
        return ucwords(strtolower($value));
    }

public function df_message(){
	$this->load->view('dashboard/df_message');
}

function service_spare_dashboard()
{
	$this->load->view('dashboard/service_spare_dashboard');
}

}
