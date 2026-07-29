<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {
	
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
        $config['smtp_host'] = 'smtpout.secureserver.net';  
        $config['smtp_user'] = 'mitr@prestomitr.com';  
        $config['smtp_pass'] = 'Presto@123!@#';   
        $config['smtp_port'] = 465;  
        $config['smtp_auth'] = true;  
        $config['smtp_crypto'] = 'ssl';  
        $this->email->initialize($config);  
		$this->email->set_newline("\r\n");  
        $this->load->library('email', $config);
		$ip = $_SERVER["REMOTE_ADDR"];
		/*if($user_id=='66' || $user_id=='67' || $user_id=='1'){
			
		}else{
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }
		}*/
		
	}
	public function local_conveyance_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher');
	}
	
	public function conveyance_voucher(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('date', 'date', 'required|trim');
	$this->form_validation->set_rules('from', 'from', 'required|trim');
		$this->form_validation->set_rules('proceed_to', 'proceed_to', 'required|trim');
	$this->form_validation->set_rules('mode', 'mode', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('conveyance_voucher/local_conveyance_form');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 $engineerremarks = $this->input->post('engineerremarks');
		 if($engineerremarks){
			 $engrmk = $engineerremarks;
		 }else{
			 $engrmk  = "";
		 }
		   $mode = $this->input->post('mode');
		$amount = 0;
		if($mode=='1'){
			$amount  = 0;
			$vehicletype=0;
			$startreading=0;
			$endreading=0;
			$rateperkms=0;
			$bills2 = "";
			$parkingamount= 0;
		}else
		{
		    $amount=$this->input->post('finalamount');
		    $vehicletype=$this->input->post('vtype');
		    $startreading = $this->input->post('start_reading');
			$endreading = $this->input->post('end_reading');
			$rateperkms = $this->input->post('rate_per_km');
			$parkingamount = $this->input->post('parkingamount');
			
			$photo2=$_FILES['parkingbill']['name'];
			if($photo2<>'')
			{
				$image2=explode('.',$photo2);
				$cat_image2=end($image2);
				$bills2=time().'.'.$cat_image2;
				move_uploaded_file($_FILES["parkingbill"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/tourbills/' . $bills2);
			}else
			{
				$bills2="";
				}	
		}
		
		 $photo1=$_FILES['visit_report']['name'];
			if($photo1<>'')
			{
				$image2=explode('.',$photo1);
				$cat_image1=end($image2);
				$visit_report=time().'.'.$cat_image1;
				move_uploaded_file($_FILES["visit_report"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/tourbills/' . $visit_report);
			}else
			{
				$visit_report="";
				}		
				
		   $data=
			array('travel_date'=>date('Y-m-d',strtotime($this->input->post('date'))),
			'from_location'=>strtoupper($this->input->post('from')),
			'proceed_to'=>strtoupper($this->input->post('proceed_to')),
			'mode'=>$mode,
			'start_reading'=>$startreading,
			'end_reading'=>$endreading,
			'rate_per_km'=>$rateperkms,
			'vehicletype'=>$vehicletype,
			'amount'=>$amount,
			'bills'=>$bills2,
			'engineerremarks'=>$engrmk,
			'visit_report'=>$visit_report,
			'parking_charges'=>$parkingamount,
			'added_by'=>$user_id,
			'status'=>'0',
			'added_on'=>$added_time);
			
			$res = $this->db->insert('conveyance_voucher',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				
				
				if($mode==1){
					
					if(isset($_REQUEST['travel_expense'])){	
					$tags1=count($_REQUEST['travel_expense']);
					if($tags1>0)
					{
					$travel_expense=$_REQUEST['travel_expense'];
					$travel_expense_amount=$_REQUEST['travel_expense_amount'];
					$to_location=$_REQUEST['source'];
					$from_location=$_REQUEST['destination'];
					
					for($x=0;$x<$tags1;$x++){
					if($travel_expense[$x]!='')
						{
							
							
						$photo1=$_FILES["bill_attachment"]["name"][$x];
						if($photo1<>'')
						{
						$image2=explode('.',$photo1);
						$cat_image1=end($image2);
						$billattachment=time().$x.'.'.$cat_image1;
						move_uploaded_file($_FILES["bill_attachment"]["tmp_name"][$x],$_SERVER['DOCUMENT_ROOT'].'/image_bank/tourbills/' . $billattachment);
						}else
						{
						$billattachment="";
						}	
					
					
						 $data=array('conveyance_id'=>$last_id,
							'expense_type'=>$travel_expense[$x],
							'amount'=>$travel_expense_amount[$x],
							'to_location'=>$to_location[$x],
							'from_location'=>$from_location[$x],
							'attachment'=>$billattachment);
							$this->db->insert('local_conveyance_expense',$data);
						   
						}
					}
					}
					}
				
				}
				
				
			    
			    /** CHECK IF HOD BY PASS IS ALLOWD **/
			    $query1234451 = $this->db->select('a.team_id')->from('presto_team_members a')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
			    if($query1234451->num_rows()>0)
			    {
                $query123445 = $this->db->select('a.team_id')->from('presto_team_members a')->join('prestogroup_teams b','a.team_id=b.team_id')->where('b.by_pass','1')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
                if($query123445->num_rows()>0)
                {
                    
                    $updata=array('hod_status'=>'1','hod_remarks'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$last_id);
                    $this->db->update('conveyance_voucher',$updata);
                 }
                 
			    }else
			    {
			        
			         $updata=array('hod_status'=>'1','hod_remarks'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$last_id);
                    $this->db->update('conveyance_voucher',$updata);
			        
			        
			    }

			    $first_name =$this->session->userdata['logged_in']['user_name'];	
				$last_name =$this->session->userdata['logged_in']['last_name'];

			    $msgbody = 'Dear Balwinder Ji, '."\n".
			    			$first_name.' '.$last_name.' has initiated a conveyance request.'."\n".
			    		   'Please review this in your panel.';

			    // echo $msgbody;exit;

				 $ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				$post = array(
				// 'receiverMobileNo' => '9560814669',
				'receiverMobileNo' => '9891941007',
				'username' => whatsappuser,
				'password' => whatsapppass,
				'message'=>strip_tags($msgbody)		
				);

				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				$result = curl_exec($ch);
				// echo $result; exit;
				if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);
       
			    /** END **/
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Sales/conveyance_voucher');
				}
		  
			}
	}

public function local_conveyance_hod_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher_hod_dashboard');
	}

public function local_conveyance_account_dashboard(){
		$this->load->view('conveyance_voucher/engineer_local_common_dashboard_account');
	}

public function local_conveyance_hr_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher_hr_dashboard');
	}	
public function conveyance_voucher_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		
		$scheduler_data = array();
		$query = $this->db->select('a.*,b.first_name, b.last_name')->from('conveyance_voucher a')->join('system_users b','a.added_by=b.user_id','left')->where('a.added_by',$user_id)->order_by('a.travel_date','desc')->get();
		$res = $query->result();
		$i=1;
		
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$html="";
			
			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				/*Public conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px; text-align:center;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR NO</th><th style='padding:2px 2px 2px 2px'>EXPENSE</th><th style='padding:2px 2px 2px 2px'>TO</th><th style='padding:2px 2px 2px 2px'>FROM</th><th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th style='padding:2px 2px 2px 2px'>BILL</th></tr>";
			$instrumentsss = array();
			$amount = array();
			$j=1;
			$query = $this->db->select('a.amount, a.attachment, b.options, a.to_location, a.from_location')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$amount[] = $record->amount;
				if($record->attachment<>'')
				{
				$bills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				}else
				{
					$bills='No Bill Uploaded';
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$j."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->to_location)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->from_location)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>";
				$html.="</tr>";
				$j++;
				
			}
		$finaltotal  = array_sum($amount);
			
			
			$html.="<tr>
				<td colspan='4'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finaltotal."</strong><td>
			</tr></table>";
				

				/*Public conveyance calculator*/
				
				
				
				
				
			}else{
				$md = "OWN";
				
				/*Own conveyance calculator*/
				if($row->bills<>'')
				{
					$parkingbills = "<a href='".tourbills.$row->bills."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				} else {
					$parkingbills = 'No Bill Uploaded';
				}

				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			
			if($row->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row->amount+$row->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
			$html.="</table>";
				

				/*Own conveyance calculator*/
				
				
				
			$finaltotal = 	$totalamount;
				
			}
			$hodremarks = "";
			$status = $row->account_status;
			if($status=='0'){
				$sta = "UNDER REVIEW";
			}else{
				$sta = "CLEARED";
			}
			
			$hod_status= $row->hod_status;
			$account_status= $row->account_status;
			$hr_status= $row->hr_status;
			if($hod_status=='0'){
				$hodsta = "UNDER REVIEW";
			}else if($hod_status=='1'){
				$hodsta = "APPROVED";
			}else{
				$hodsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hod_remarks;;
			}
			if($hod_status=='0'){
				$accountsta="";
				$hrsta="";
			}else{
			$account_status= $row->account_status;
			if($account_status=='0'){
				$accountsta = "UNDER REVIEW";
			}else if($hod_status=='1'){
				$accountsta = "APPROVED";
			}else{
				$accountsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->account_remarks;;
			}
			}
			if($account_status=='0' && $hod_status=='0'){
				$hrsta="";
				$accountsta="";
				$hodsta="";
			}else{
			$hr_status= $row->hr_status;
			if($hod_status=='1'){
				$hrsta = "APPROVED";
			}else{
				$hrsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hr_remarks;;
			}
			}
			$html1='';
			if($account_status=='1' && $hod_status=='1')
			{
				$html1.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>Sno</th><th style='padding:2px 2px 2px 2px'>Total Amount</th><th style='padding:2px 2px 2px 2px'>Deduction</th><th style='padding:2px 2px 2px 2px'>Paid Amount</th></tr>";
			$k=1;
			$query2 = $this->db->select('a.amount, a.deduction')->from('conveyance_voucher a')->where('a.id',$row->id)->get();
			foreach($query2->result() as $record2){				
				$paid=($finaltotal)-($row->deduction);
				$html1.="<tr>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$k."</td>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$finaltotal."</td>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->deduction."</td>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$paid."</td>";
				$html1.="</tr>";
				$k++;
			
			
			$html1.="</table>";
				}
			}
			
			/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
                if($row->bills){
				$attached_bills = "<a href='".tourbills.$row->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			 if($row->visit_report){
				$attached_visit_report = "<a href='".tourbills.$row->visit_report."' download>Click Here to Download</a>";
			}else{
				$attached_visit_report="";
			}
			
			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'vehicletype'=>$vtype,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'deduction'=>$row->deduction,
			'status'=>$sta,
			'expensedata'=>$html,
			'acconntpaid'=>$html1,
			'bills'=>$attached_bills,
			'hod_status'=>$hodsta,
			'account_status'=>$accountsta,
			'hr_status'=>$hrsta,
			'attached_visit_report'=>$attached_visit_report,
			'edit'=>$edit,
			'takeprint'=>$takeprint);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function conveyance_voucher_for_hod_list()
	{
		$scheduler_data=array();
	    $query = $this->db->select('a.*,b.first_name, b.last_name')->from('conveyance_voucher a')->join('system_users_view b','a.added_by=b.user_id','left')->where('a.status','0')->where('a.hod_status','1')->where('a.account_status','0')->order_by('a.id','DESC')->get();
		$res = $query->result();
		$m=1;
		foreach($res as $row)
		{
			
			if($row->bills){
				$attached_bills = "<a href='".tourbills.$row->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			
			
			date_default_timezone_set("Asia/Kolkata");
			$addedondate = date('Y-m-d',strtotime($row->added_on));
			$html = "";
			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				/*Public conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR NO</th><th style='padding:2px 2px 2px 2px'>EXPENSE</th><th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th >BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			$amount = array();
			$query = $this->db->select('a.amount, a.attachment, b.options')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
				
				$amount[] = $record->amount;
				//$bills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				if($record->attachment!='')
				{
				$bills = "<a href='".page_url.'Sales/viewbill/'.$row->id."' target='_blank'><span class='btn btn-success btn-xs'>Print Consolidated Bill</span></a>";
				}else
				{
				$bills = "BILL NOT UPLOADED";	
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$i."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>";
				$html.="</tr>";
				$i++;
				
			}
		$finaltotal  = array_sum($amount);
			
			
			$html.="<tr>
				<td colspan='1'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finaltotal."</strong><td>
				<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>
			</tr></table>";
			}	

				/*Public conveyance calculator*/
				
				
				
				
				
			}else{
				$md = "OWN";

			$query = $this->db->select('a.*')->from('conveyance_voucher a')->where('a.id',$row->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
			
				/*Own conveyance calculator*/
				if($record->bills !='')
					{
				$parkingbills = "<a href='".tourbills.$record->bills."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				}else{
					$parkingbills = "BILL NOT UPLOADED";
				}
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			
			if($row->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row->amount+$row->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
			$html.="</table>";
				

				/*Own conveyance calculator*/
							
			}
			}
		}
			$hodremarks = "";
			$pendingforapporaval="";
			$status = $row->account_status;
			//echo $status; exit;
			if($status==0){
				$pendingforapporaval= "<span class='btn btn-success btn-xs' data-toggle='modal' data-target='#hod_app_approved".$row->id."'>MARK AS APPROVED</span>";
				//$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
			}else if($status==2){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else if($status==1){
				$pendingforapporaval= "APPROVED";		
				
			}
			
			$pendingforapporaval.='<div id="hod_app_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_mark_as_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the approval response</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="approvedremarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				 if($row->visit_report){
				$attached_visit_report = "<a href='".tourbills.$row->visit_report."' download>Click Here to Download</a>";
			}else{
				$attached_visit_report="";
			}
				
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$diff = abs(strtotime($row->travel_date) - strtotime($addedondate));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			if($days>7){
				$systemaddeddate = "<span style='color:red; font-weight:bold;'>".$addedondate."</span>";
			}else{
				$systemaddeddate = $addedondate;
			}
			$scheduler_data[] = array('sr_no'=>$m,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'visitdata'=>$html,
			'vehicletype'=>$vtype,
			'bills'=>$attached_bills,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'engineerremarks'=>$row->engineerremarks,
			'takeprint'=>$takeprint,
			'attached_visit_report'=>$attached_visit_report,
			'hodstatus'=>$pendingforapporaval,
			'hodremarks'=>$row->hod_remarks,
			'edit'=>$edit,
			'addedondate'=>$systemaddeddate);
			$m++;
		}
		
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function conveyance_voucher_for_hod_list_unapproved()
	{
	    if($this->uri->segment(3)){
	        $user_id = $this->uri->segment(3);
	    }{
	        $user_id =$this->session->userdata['logged_in']['user_id'];	
	    }
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		
		
		
		$scheduler_data = array();
		if($team<>'NA')
		{
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher_view a')->join('system_users_view b','a.added_by=b.user_id','left')->where_in('a.added_by',$team,false)->order_by('a.travel_date','DESC')->where('a.status',0)->get();
		$res = $query->result();
		$m=1;
		foreach($res as $row)
		{
			
			if($row->bills){
				$attached_bills = "<a href='".tourbills.$row->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			
			
			date_default_timezone_set("Asia/Kolkata");
			$addedondate = date('Y-m-d',strtotime($row->added_on));
			$html = "";
			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				/*Public conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR NO</th><th style='padding:2px 2px 2px 2px'>EXPENSE</th><th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th >BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			$amount = array();
			$query = $this->db->select('a.amount, a.attachment, b.options')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
				
				$amount[] = $record->amount;
				//$bills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				if($record->attachment!='')
				{
				$bills = "<a href='".page_url.'Sales/viewbill/'.$row->id."' target='_blank'><span class='btn btn-success btn-xs'>Print Consolidated Bill</span></a>";
				}else
				{
				$bills = "BILL NOT UPLOADED";	
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$i."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>";
				$html.="</tr>";
				$i++;
				
			}
		$finaltotal  = array_sum($amount);
			
			
			$html.="<tr>
				<td colspan='1'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finaltotal."</strong><td>
				<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>
			</tr></table>";
			}	

				/*Public conveyance calculator*/
				
				
				
				
				
			}else{
				$md = "OWN";

			// $query = $this->db->select('a.amount, a.attachment, b.options,')->from('local_conveyance_expense1 a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			// if($query->num_rows()>0){
			// foreach($query->result() as $record){
			
			// 	if($record->attachment !='')
			// 	{
			// 		$attactment=$record->attachment;
			// 	}else
			// 	{
			// 		$attactment='Not Upload';
			// 	}
				/*Own conveyance calculator*/
				if($record->attachment !='')
					{
				$parkingbills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				}else{
					$parkingbills = "BILL NOT UPLOADED";
				}
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			
			if($row->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row->amount+$row->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
			$html.="</table>";
				

				/*Own conveyance calculator*/
							
			}
			
			$hodremarks = "";
			$pendingforapporaval="";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<span class='btn btn-success btn-xs' data-toggle='modal' data-target='#hod_app_approved".$row->id."'>MARK AS APPROVED</span>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			$pendingforapporaval.='<div id="hod_app_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_mark_as_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the approval response</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="approvedremarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				 if($row->visit_report){
				$attached_visit_report = "<a href='".tourbills.$row->visit_report."' download>Click Here to Download</a>";
			}else{
				$attached_visit_report="";
			}
				
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$diff = abs(strtotime($row->travel_date) - strtotime($addedondate));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			if($days>7){
				$systemaddeddate = "<span style='color:red; font-weight:bold;'>".$addedondate."</span>";
			}else{
				$systemaddeddate = $addedondate;
			}
			$scheduler_data[] = array('sr_no'=>$m,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'visitdata'=>$html,
			'vehicletype'=>$vtype,
			'bills'=>$attached_bills,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'engineerremarks'=>$row->engineerremarks,
			'takeprint'=>$takeprint,
			'attached_visit_report'=>$attached_visit_report,
			'hodremarks'=>$pendingforapporaval."  ".$hodremarks,
			'edit'=>$edit,
			'addedondate'=>$systemaddeddate);
			$m++;
		}
		
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function viewbill()
{
	$this->load->view('store/view_tour_bill');
}
public function membertourbillview()
{
	$this->load->view('store/membertourbillview');
}

public function conveyance_voucher_for_account_list()
	{
		$account_data=array();
		if($this->uri->segment(3)){
	        $user_id = $this->uri->segment(3);
	    }{
	        $user_id =$this->session->userdata['logged_in']['user_id'];	
	    }
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		
		
		
		if($team<>'NA')
		{
		$account_data = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher_view a')->join('system_users_view b','a.added_by=b.user_id','left')->where_in('a.added_by',$team,false)->order_by('a.travel_date','DESC')->where('a.hod_status','0')->where('a.account_status','0')->get();
		$res = $query->result();
		$m=1;
		foreach($res as $row)
		{
	
		
			
			if($row->bills){
				$attached_bills = "<a href='".tourbills.$row->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			
			if($row->visit_report){
				$visit_report = "<a href='".tourbills.$row->visit_report."' download>Click Here to Download</a>";
			}else{
				$visit_report="";
			}
			date_default_timezone_set("Asia/Kolkata");
			$html = "";
			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				/*Public conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR NO</th><th style='padding:2px 2px 2px 2px'>EXPENSE</th><th style='padding:2px 2px 2px 2px'>TO</th><th style='padding:2px 2px 2px 2px'>FROM</th><th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th style='padding:2px 2px 2px 2px'>BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			$query = $this->db->select('a.amount, a.attachment, b.options, a.to_location, a.from_location')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$amount[] = $record->amount;
				
				if($record->attachment <> '') {
				$bills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				} else {
				$bills = "BILL NOT UPLOADED";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$i."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->to_location)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->from_location)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>";
				$html.="</tr>";
				$i++;
				
			}
		$finaltotal  = array_sum($amount);
			
			
			$html.="<tr>
				<td colspan='4'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finaltotal."</strong><td>
			</tr></table>";
				

				/*Public conveyance calculator*/
				
				
				
				
				
			}else{
				$md = "OWN";
				$parkingbills = "";
				$query = $this->db->select('a.amount, a.attachment, b.options, a.to_location, a.from_location')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
				if($query->num_rows() >0){
			foreach($query->result() as $record){
				$parkingbills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
			}
					}
				/*Own conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			
			if($row->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row->amount+$row->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
			$html.="</table>";
				
$finaltotal = $totalamount;
				/*Own conveyance calculator*/
				
				
				
			 	
				
			}
			
			$accountremarks = "";
			$markasreject="";
			$status = $row->account_status;
			if($status=='0'){
				//$pendingforapporaval= "<a href='".page_url."/Sales/account_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$accountremarks.= "<span class='btn btn-success btn-xs' data-toggle='modal' data-target='#account_not_approved".$row->id."'>MARK AS APPROVED</span>";
				$markasreject.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#account_mark_as_rejected".$row->id."'>MARK AS REJECT</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED<br><br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$pendingforapporaval = "APPROVED";
				
				
			}
			
			
			
			$accountremarks.= '<div id="account_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/account_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the approval response</h4>
                                        </div>
                                        <div class="modal-body">
                                        
                                        <div class="row">
                                         <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">AMOUNT</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" style="width:100%" name="amt" required value="'.$finaltotal.'" readonly>
												    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DEDUCTION (if any)</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" style="width:100%" name="deduction" required value="0"></textarea>
												    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
							
							
							$markasreject.= '<div id="account_mark_as_rejected'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/account_marked_as_rejected/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the rejected response</h4>
                                        </div>
                                        <div class="modal-body">
                                        
                                        <div class="row">
                                        
                                         
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			$status = $row->hod_status;
			if($status=='2'){
				$hodstatus = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else if($status==1){
				$hodstatus= "APPROVED";				
				
			}else
			{
				$hodstatus= "";
			}
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
			if($row->account_remarks){
				$acc_remarks=$row->account_remarks;
			}else{
				
				$acc_remarks = $accountremarks."<br>".$markasreject;
			}
			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			$selectall = '<input type="checkbox" name="selectall[]" value="'.$row->id.'">';
			$account_data[] = array('sr_no'=>$m,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>date('d-m-Y',strtotime($row->travel_date)),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'selectall'=>$selectall,
			'visitdata'=>$html,
			'takeprint'=>$takeprint,
			'bills'=>$attached_bills,
			'visit_report'=>$visit_report,
			'vehicletype'=>$vtype,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'hod_status'=>$hodstatus,
			'engineer_remarks'=>$row->engineerremarks,
			'accountremarks'=>$acc_remarks,
			
			'edit'=>$edit);
			$m++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($account_data),
	"iTotalDisplayRecords" => count($account_data),
	"aaData"=>$account_data);
	echo json_encode($results);
}


public function conveyance_voucher_for_hr_list()
	{
		$hr_data = array();
		$query = $this->db->select('a.*,b.first_name, b.last_name')->from('conveyance_voucher a')->join('system_users b','a.added_by=b.user_id','left')->where('a.status','0')->where('a.hod_status','1')->where('a.account_status','1')->order_by('a.account_status','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
			}else{
				$md = "OWN";
			}
			
			$hrremarks = "";
			$status = $row->hr_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/hr_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$hrremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hr_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED<br><br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$pendingforapporaval = "APPROVED";
				
				
			}
			
			
			$hrremarks.= '<div id="hr_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hr_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			$status = $row->hod_status;
			if($status=='2'){
				$hodstatus = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else{
				$hodstatus= "APPROVED";
			}
			
			$status1 = $row->account_status;
			if($status1=='2'){
				$accountstatus = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->account_remarks."</strong>";
				
			}else{
				$accountstatus= "APPROVED";
			}
			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$hr_data[] = array('sr_no'=>$i,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'hod_status'=>$hodstatus,
			'accountstatus'=>$accountstatus,
			'hrremarks'=>$pendingforapporaval."  ".$hrremarks,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($hr_data),
	"iTotalDisplayRecords" => count($hr_data),
	"aaData"=>$hr_data);
	echo json_encode($results);
}

	
	public function hod_mark_as_approved()
	{
		 $table = "conveyance_voucher";
			$data = array('account_status'=>'1','status'=>'1',
			'account_remarks'=>$this->input->post('approvedremarks'));
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$hodremarks=$this->input->post('approvedremarks');

			$q=$this->db->select('added_by,from_location,proceed_to,travel_date')->from('conveyance_voucher')->where('id',$this->uri->segment(3))->get();
			foreach($q->result() as $userid){
				$user_id=$userid->added_by;

				$detail="Travel date :".date('d-M-Y',strtotime($userid->travel_date))."\nFrom :".$userid->from_location."\nTo:".$userid->proceed_to."";
			}

			$sq=$this->db->select('first_name,last_name,contact_number')->from('system_users')->where('user_id',$user_id)->get();
			foreach($sq->result() as $teamdetail)
			{
				$mob=$teamdetail->contact_number;
				$name=$teamdetail->first_name.' '.$teamdetail->last_name;
			}

			$smsmessage = "Dear ".$name.",\n"."Your conveyance amount reimbursement has been approved."."\n".$detail."\n"."Remark : ".$hodremarks;

			

			/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					 //'receiverMobileNo' => 918802467962,
					'receiverMobileNo' => '91'.$mob,
					'username' => 'srsrbh5',
					'password' => 'Manglesh@sd5',
					'message'=>strip_tags($smsmessage));

					//echo "<pre>";print_r($post);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_account_data/');
		}
	
	public function account_mark_as_approved()
	{
		    $table = "conveyance_voucher";
			$data = array('account_status'=>'1','status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_data');
		}
	
	public function hr_mark_as_approved()
	{
		    $table = "conveyance_voucher";
			$data = array('hr_status'=>'1','status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_hr_dashboard');
		}
		
	public function hod_marked_as_not_approved()
	{
		    $table = "conveyance_voucher";
			$remark = $this->input->post('remarks');
			$data = array('hod_status'=>'2',
			'hod_remarks'=>$remark);
			
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);

			$q=$this->db->select('added_by')->from('conveyance_voucher')->where('id',$this->uri->segment(3))->get();
			foreach($q->result() as $userid){
				$user_id=$userid->added_by;
			}

			$sq=$this->db->select('first_name,last_name,contact_number')->from('system_users')->where('user_id',$user_id)->get();
			foreach($sq->result() as $teamdetail)
			{
				$mob=$teamdetail->contact_number;
				$name=$teamdetail->first_name.' '.$teamdetail->last_name;
			}

			$smsmessage = "Dear ".$name.",\n"."Your conveyance amount reimbursement has been rejected for approval. This is due to ".$remark;
			/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					 ////'receiverMobileNo' => 918802467962,
					'receiverMobileNo' => '91'.$mob,
					'username' => 'prestogroup',
					'password' => 'Justaclear1$',
					'message'=>strip_tags($smsmessage));

					//echo "<pre>";print_r($post);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */

			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_hod_dashboard');
		}
		
	public function account_marked_as_not_approved()
	{
		
		    $table = "conveyance_voucher";
			$remark = $this->input->post('remarks');
			$dedu=$this->input->post('deduction');
			$data = array(
						'hod_status'=>'1',
						'status'=>'0',
						'deduction'=>$dedu,
						'hod_remarks'=>$remark,
						'account_status'=>'1',
						'account_remarks'=>$remark
					);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);

			$q=$this->db->select('added_by,deduction')->from('conveyance_voucher')->where('id',$this->uri->segment(3))->get();
			foreach($q->result() as $userid){
				$user_id=$userid->added_by;
			}

			$sq=$this->db->select('first_name,last_name,contact_number')->from('system_users')->where('user_id',$user_id)->get();
			foreach($sq->result() as $teamdetail)
			{
				$mob=$teamdetail->contact_number;
				$name=$teamdetail->first_name.' '.$teamdetail->last_name;
			}
			if($userid->deduction=='0.00'){
			$smsmessage = "Dear ".$name.",\n"."Your reimbursement amount for conveyance has been approved.";
			}else
			{
				$smsmessage = "Dear ".$name.",\n"."There is a deduction of Rs.".$dedu." from your total conveyance amount. This is due to ".$remark;
			}
			

			// echo $smsmessage;exit;
			/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					 // 'receiverMobileNo' => '9560814669',
					'receiverMobileNo' => '91'.$mob,
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($smsmessage));

					//echo "<pre>";print_r($post);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_data/');
		}	
		
		public function account_marked_as_rejected()
	{
		    $table = "conveyance_voucher";
			$remark = $this->input->post('remarks');
			$dedu=$this->input->post('deduction');
			$data = array(
						'status'=>'1',
						'account_status'=>'2',
						'account_remarks'=>$remark,
						'hod_status'=>'2',
						'hod_remarks'=>$remark
						);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);

		$q=$this->db->select('added_by,deduction')->from('conveyance_voucher')->where('id',$this->uri->segment(3))->get();
			foreach($q->result() as $userid){
				$user_id=$userid->added_by;
			}

			$sq=$this->db->select('first_name,last_name,contact_number')->from('system_users')->where('user_id',$user_id)->get();
			foreach($sq->result() as $teamdetail)
			{
				$mob=$teamdetail->contact_number;
				$name=$teamdetail->first_name.' '.$teamdetail->last_name;
			}
			
			$smsmessage = "Dear ".$name.",\n"."Your conveyance amount reimbursement has been rejected. This is due to ".$remark;
			
			

			/**WHATSAPP INTEGRATION**/
					// echo $smsmessage;exit;

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					 // 'receiverMobileNo' => '9560814669',
					'receiverMobileNo' => '91'.$mob,
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($smsmessage));

					//echo "<pre>";print_r($post);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_account_dashboard');
		}
	public function hr_marked_as_not_approved()
	{
		    $table = "conveyance_voucher";
			$remark = $this->input->post('remarks');
			$data = array('hr_status'=>'2',
			'hr_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/local_conveyance_hr_dashboard');
		}	
			
		
		
public function edit_conveyance_voucher(){
	$this->form_validation->set_rules('date', 'date', 'required|trim');
	$this->form_validation->set_rules('from', 'from', 'required|trim');
		$this->form_validation->set_rules('proceed_to', 'proceed_to', 'required|trim');
	$this->form_validation->set_rules('mode', 'mode', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/edit_conveyance_voucher');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $startreading = $this->input->post('start_reading');
		   $endreading = $this->input->post('end_reading');
		   $final = $endreading-$startreading;
		   $rateperkms = $this->input->post('rate_per_km');
		   $mode = $this->input->post('mode');
		   if($rateperkms>0){
			   $amount = $final*$rateperkms;
		   }else{
			   $amount = "";
		   }
		if($mode=='1'){
			$amount  = $this->input->post('amount');
		}
		   $data=
			array('travel_date'=>date('Y-m-d',strtotime($this->input->post('date'))),
			'from_location'=>strtoupper($this->input->post('from')),
			'proceed_to'=>strtoupper($this->input->post('proceed_to')),
			'mode'=>$mode,
			'start_reading'=>$startreading,
			'end_reading'=>$endreading,
			'rate_per_km'=>$rateperkms,
			'amount'=>$amount,
			'added_by'=>$user_id,
			'status'=>'0',
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('conveyance_voucher',$data);
			
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
				redirect(page_url.'Sales/conveyance_voucher');
				}
		   
			
			}
	}
	

public function sale_service_conveyance_voucher(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('purpose_of_trip', 'purpose_of_trip', 'required|trim');
	$this->form_validation->set_rules('start_date', 'start_date', 'required|trim');
		$this->form_validation->set_rules('end_date', 'end_date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/tour_conveyance_form');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
		   $end_date = date('Y-m-d',strtotime($this->input->post('end_date')));
		    if($user_id==86){
				$hod_status = 1;
		}else{
			$hod_status= "0";
		}
		   
		   $engineerlateremarks = $this->input->post('engineerlateremarks');
		   if($engineerlateremarks)
		   {
			   $engrmk = $engineerlateremarks;
		   }else{
			   $engrmk = "";
		   }
		   $data=
			array('purpose_of_trip'=>$this->input->post('purpose_of_trip'),
			'employee_id'=>$user_id,
			'tour_start_date'=>$start_date,
			'tour_end_date'=>$end_date,
			'total_days'=>$this->input->post('total_days_of_trip'),
			'status'=>'0',
			'hod_status'=>$hod_status,
			'engineerremarks'=>$engrmk,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('member_conveyance_information',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				
				if(isset($_REQUEST['traveldate'])){	
					$tags1=count($_REQUEST['traveldate']);
					if($tags1>0)
					{
					$traveldate=$_REQUEST['traveldate'];
					$city_name=$_REQUEST['city_name'];
					$company_name=$_REQUEST['company_name'];
					$livingexpense=$_REQUEST['livingexpense'];
					$livingexpense_amount=$_REQUEST['livingexpense_amount'];
					$travel_expense=$_REQUEST['travel_expense'];
					$travel_expense_amount=$_REQUEST['travel_expense_amount'];
					$foodexpense=$_REQUEST['foodexpense'];
					
					
					for($x=0;$x<$tags1;$x++){
					if($traveldate[$x]!='')
						{
						$photo1=$_FILES["bills"]["name"][$x];
						if($photo1<>'')
						{
						$image2=explode('.',$photo1);
						$cat_image1=end($image2);
						$billattachment=time().$x.'.'.$cat_image1;
						move_uploaded_file($_FILES["bills"]["tmp_name"][$x],$_SERVER['DOCUMENT_ROOT'].'/image_bank/tourbills/' . $billattachment);
						}else
						{
						$billattachment="";
						}	
						
						
						$photo2=$_FILES["livingexpensebill"]["name"][$x];
						if($photo2<>'')
						{
						$image3=explode('.',$photo2);
						$cat_image2=end($image3);
						$livingexpensebill="livingbill-".time().$x.'.'.$cat_image2;
						move_uploaded_file($_FILES["livingexpensebill"]["tmp_name"][$x],$_SERVER['DOCUMENT_ROOT'].'/image_bank/tourbills/' . $livingexpensebill);
						}else
						{
						$livingexpensebill="";
						}	
							
							
						$photo3=$_FILES["travellingbill"]["name"][$x];
						if($photo3<>'')
						{
						$image4=explode('.',$photo3);
						$cat_image3=end($image4);
						$travellingbill="travelbill-".time().$x.'.'.$cat_image3;
						move_uploaded_file($_FILES["travellingbill"]["tmp_name"][$x],$_SERVER['DOCUMENT_ROOT'].'/image_bank/tourbills/' . $travellingbill);
						}else
						{
						$travellingbill="";
						}
							
						 $data=array('conv_id'=>$last_id,
							'employee_id'=>$user_id,
							'tour_date'=>date('Y-m-d',strtotime($traveldate[$x])),
							'city'=>$city_name[$x],
							'company_name'=>$company_name[$x],
							'living_expense_id'=>$livingexpense[$x],
							'living_exp_amount'=>$livingexpense_amount[$x],
							'travel_exp_id'=>$travel_expense[$x],
							'travel_exp_amount'=>$travel_expense_amount[$x],
							'foodexpense'=>$foodexpense[$x],
							'bill_attachment'=>$billattachment,
							'livingexpensebill'=>$livingexpensebill,
							'travellingbill'=>$travellingbill,
							'added_on'=>date('Y-m-d H:i:s'));
							$this->db->insert('member_conveyance_brief',$data);
						   
						}
					}
					}
					}
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Sales/sale_service_conveyance_voucher');
				}
		  
			}
	}

public function fetch_expense_options(){
		echo "<option value=''>--Select Expense Options--</option>";
	$expense_type = $this->input->post('expense_type');
		$query =$this->db->select('id, options')->from('conveyance_type_options')->where('conveyance_type_id',$expense_type)->where('status','1')->get();
		foreach($query->result() as $options)
			{
				echo "<option value=".$options->id.">".strtoupper($options->options)."</option>";
				}
	}

public function travel_conveyance_voucher_list()
	{
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->where('a.employee_id',$user_id)->order_by('a.tour_start_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			$status = $row->status;
			if($status=='0'){
				$sta = "UNDER REVIEW";
			}else {
				$sta = "CLEARED";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			if($row->hod_status=='1'){
				$hod_status = "APPROVED";
			}else if($row->hod_status=='2'){
				$hod_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hod_remarks."</strong>";
			}else{
				$hod_status = "PENDING FOR REVIEW";
			}
			
			if($row->account_status=='1'){
				$account_status = "APPROVED";
			}else if($row->account_status=='2'){
				$account_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$account_status = "PENDING FOR REVIEW";
			}
			
			if($row->hr_status=='1'){
				$hr_status = "APPROVED";
			}else if($row->hr_status=='2'){
				$hr_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hr_remarks."</strong>";
			}else{
				$hr_status = "PENDING FOR REVIEW";
			}
			
			
			$livingamt= array();
			$travelamt = array();
			$foodexpenseamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$foodexpenseamt[]=0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>DATE</th><th style='padding:2px 2px 2px 2px'>CITY</th> <th style='padding:2px 2px 2px 2px'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th style='padding:2px 2px 2px 2px'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th><th style='padding:2px 2px 2px 2px'>FOOD CONVEYANCE</th><th style='padding:2px 2px 2px 2px'>BILL EXPENSE</th><th style='padding:2px 2px 2px 2px'>LIVING BILL</th><th style='padding:2px 2px 2px 2px'>TRAVELLING BILL</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id,a.bill_attachment, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype, a.livingexpensebill, a.travellingbill')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				if($record->bill_attachment !='')
				{
				$bills = "<a href='".tourbills.$record->bill_attachment."' class='btn btn-success btn-xs' download>Download</a>";
				}else{
					$bills = "No Bill Upload";
				}
				if($record->bill_attachment !='')
				{
				$livingbill = "<a href='".tourbills.$record->livingexpensebill."' class='btn btn-success btn-xs' download>Download</a>";
				}else{
					$livingbill = "No Bill Upload";
				}
				if($record->bill_attachment !='')
				{
				$travellingbill = "<a href='".tourbills.$record->travellingbill."' class='btn btn-success btn-xs' download>Download</a>";
				}else{
					$travellingbill = "No Bill Upload";	
				}
				$viewbills="<a href='".page_url.'Sales/membertourbillview/'.$record->conv_id."' class='btn btn-success btn-xs' >View Bill</a>";
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$foodexpenseamt[] = $record->foodexpense;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->foodexpense)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$livingbill."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$travellingbill."</td>";
				$html.="</tr>";
				
			}
		
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$foot_amount =  array_sum($foodexpenseamt);
			$grandtotal = $lv_amount+$trv_amount+$foot_amount;
			$html.="<tr>
				<td colspan='8'></td>
				<td colspan='2'>$viewbills</td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";

			$html1='';
			if($row->account_status=='1' && $row->hod_status=='1')
			{
				$html1.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>Sno</th><th style='padding:2px 2px 2px 2px'>Total Amount</th><th style='padding:2px 2px 2px 2px'>Deduction</th><th style='padding:2px 2px 2px 2px'>Paid Amount</th></tr>";
			$k=1;
			$query2 = $this->db->select('a.total_tour_amt, a.deduction')->from('member_conveyance_information a')->where('a.id',$row->id)->get();
			foreach($query2->result() as $record2){				
				$paid=($record2->total_tour_amt)-($record2->deduction);
				$html1.="<tr>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$k."</td>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$record2->total_tour_amt."</td>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$record2->deduction."</td>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$paid."</td>";
				$html1.="</tr>";
				$k++;
			
			
			$html1.="</table>";
				}
			}
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'status'=>$sta,
			'traveldata'=>$html,
			'traveldataaccount'=>$html1,
			'hod_status'=>$hod_status,
			'account_status'=>$account_status);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

function sale_service_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_voucher');
}


function sale_service_conveyance_hod_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_hod_dashboard');
}
public function travel_conveyance_voucher_hod_list()
	{
		
		$CONVEYANCE_DATA = array();
		if($this->uri->segment(3)){
		    $user_id = $this->uri->segment(3);
		}else{
		   $user_id =$this->session->userdata['logged_in']['user_id'];	 
		}
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
		
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
	
	if($team<>'NA')
		{	
		$this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->where('a.status','0');
		if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
	$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$hodremarks = "";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/sale_service_hod_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a><br><br>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			
			$livingamt= array();
			$travelamt = array();
			$foodamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$foodamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th><th style='padding:2px 2px 2px 2px; text-align:center'>FOOD EXPENSE</th><th style='padding:2px 2px 2px 2px'>BILL EXPENSE</th><th style='padding:2px 2px 2px 2px'>LIVING BILL</th><th style='padding:2px 2px 2px 2px'>TRAVELLING BILL</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.livingexpensebill, a.travellingbill,a.conv_id,a.bill_attachment, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){

				$bills='BILL NOT UPLOADED';
				$livingbill='BILL NOT UPLOADED';
				$travellingbill='BILL NOT UPLOADED';
				if($record->bill_attachment<>'')
				{
				$bills = "<a href='".tourbills.$record->bill_attachment."' class='btn btn-success btn-xs' download>Download</a>";
				}
				if($record->livingexpensebill<>'')
				{
				$livingbill = "<a href='".tourbills.$record->livingexpensebill."' class='btn btn-success btn-xs' download>Download</a>";
				}

				if($record->travellingbill<>'')
				{
				$travellingbill = "<a href='".tourbills.$record->travellingbill."' class='btn btn-success btn-xs' download>Download</a>";
				}

				$viewbills="<a href='".page_url.'Sales/membertourbillview/'.$record->conv_id."' class='btn btn-success btn-xs' target='_blank' >View Bill</a>";

				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$foodamt[] = $record->foodexpense;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->foodexpense)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills ."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$livingbill."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$travellingbill."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$food_amount =  array_sum($foodamt);
			$grandtotal = $lv_amount+$trv_amount+$food_amount;
			$html.="<tr>
				<td colspan='8'></td>
				<td colspan='2' style='padding:2px 2px 2px 2px; text-align:center'>$viewbills</td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			$takeprintout = "<a href='".page_url."Sales/tour_take_printout/".$row->id."'><i class='fa fa-print' style='font-size:24px;'></i></a>";
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>$pendingforapporaval." ".$hodremarks,
			'traveldata'=>$html,
			'engineerremarks'=>$row->engineerremarks,
			'takeprintout'=>$takeprintout );
			$i++;
		}
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}
public function sale_service_hod_mark_as_approved()
	{
		 $table = "member_conveyance_information";
			$data = array('hod_status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hod_dashboard');
		}

public function sale_service_hod_marked_as_not_approved()
	{
		    $table = "member_conveyance_information";
			$remark = $this->input->post('remarks');
			$data = array('hod_status'=>'2',
			'hod_remarks'=>$remark);
			
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hod_dashboard');
		}
function sale_service_conveyance_account_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_account_dashboard');
}
public function travel_conveyance_voucher_account_list()
	{
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->where('a.status','0')->where('a.hod_status','1')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$accountremarks = "";
			$status = $row->account_status;
			if($status=='0'){
				$pendingforapporaval= "<span class='btn btn-success btn-xs' data-toggle='modal' data-target='#_account_hod_not_approved".$row->id."'>MARK AS APPROVED</span><br><br>";
				$accountremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->account_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			

			$accountremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_account_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			
			$livingamt= array();
			$travelamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th><th style='padding:2px 2px 2px 2px; text-align:center'>FOOD EXPENSE</th><th style='padding:2px 2px 2px 2px'>BILL BREAKUP</th><th style='padding:2px 2px 2px 2px'>LIVING BILL</th><th style='padding:2px 2px 2px 2px'>TRAVELLING BILL</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.livingexpensebill, a.travellingbill,conv_id,a.bill_attachment, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				$bills = "<a href='".tourbills.$record->bill_attachment."' class='btn btn-success btn-xs' download>Download</a>";
				$livingbill = "<a href='".tourbills.$record->livingexpensebill."' class='btn btn-success btn-xs' download>Download</a>";
				$travellingbill = "<a href='".tourbills.$record->travellingbill."' class='btn btn-success btn-xs' download>Download</a>";
				
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$foodamt[] = $record->foodexpense;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->foodexpense)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$livingbill."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$travellingbill."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$food_amount =  array_sum($foodamt);
			$grandtotal = $lv_amount+$trv_amount+$food_amount;
			$html.="<tr>
				<td colspan='10'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			$takeprintout = "<a href='".page_url."Sales/tour_take_printout/".$row->id."'><i class='fa fa-print' style='font-size:24px;'></i></a>";

			$accountremarks.= '<div id="_account_hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_account_mark_as_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Approved Tour Bill</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                            <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Total Amount</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" name="totalamount" required value="'.$grandtotal.'" readonly> 
												    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">Deduction Amt</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" name="deductionamt" required value="" > 
												    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';

			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>'APPROVED',
			'account_status'=>$pendingforapporaval." ".$accountremarks,
			'traveldata'=>$html,
			'takeprintout'=>$takeprintout);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

public function sale_service_account_mark_as_approved()
	{
		    $table = "member_conveyance_information";
			$data = array('account_status'=>'1',
			'total_tour_amt'=>$this->input->post('totalamount'),
			'deduction'=>$this->input->post('deductionamt'),
			'account_remarks'=>$this->input->post('remarks'),
			'status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$dedu=$this->input->post('deductionamt');
			$remarks=$this->input->post('remarks');
			$q=$this->db->select('employee_id,total_tour_amt,deduction')->from('member_conveyance_information')->where('id',$this->uri->segment(3))->get();
			foreach($q->result() as $userid){
				$user_id=$userid->employee_id;
			}

			$sq=$this->db->select('first_name,last_name,contact_number')->from('system_users')->where('user_id',$user_id)->get();
			foreach($sq->result() as $teamdetail)
			{
				$mob=$teamdetail->contact_number;
				$name=$teamdetail->first_name.' '.$teamdetail->last_name;
			}
			if($userid->deduction=='0.00'){
			$smsmessage = "Dear ".$name.",\n"."Your reimbursement amount for conveyance has been approved.";
			}else
			{
				$smsmessage = "Dear ".$name.",\n"."There is a deduction of Rs.".$dedu." from your total conveyance amount. This is due to ".$remarks;
			}
			

			/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					 //'receiverMobileNo' => 918802467962,
					'receiverMobileNo' => '91'.$mob,
					'username' => 'prestogroup',
					'password' => 'Justaclear1$',
					'message'=>strip_tags($smsmessage));

					//echo "<pre>";print_r($post);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */

			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_account_dashboard');
		}
public function sale_service_account_marked_as_not_approved()
	{
		    $table = "member_conveyance_information";
			$remark = $this->input->post('remarks');
			$data = array('account_status'=>'2',
			'account_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);

			$q=$this->db->select('employee_id,total_tour_amt,deduction')->from('member_conveyance_information')->where('id',$this->uri->segment(3))->get();
			foreach($q->result() as $userid){
				$user_id=$userid->employee_id;
			}

			$sq=$this->db->select('first_name,last_name,contact_number')->from('system_users')->where('user_id',$user_id)->get();
			foreach($sq->result() as $teamdetail)
			{
				$mob=$teamdetail->contact_number;
				$name=$teamdetail->first_name.' '.$teamdetail->last_name;
			}
			$smsmessage = "Dear ".$name.",\n"."Your conveyance amount reimbursement has been rejected. This is due to ".$remark;
			

			/**WHATSAPP INTEGRATION**/
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					 //'receiverMobileNo' => 918802467962,
					'receiverMobileNo' => '91'.$mob,
					'username' => 'prestogroup',
					'password' => 'Justaclear1$',
					'message'=>strip_tags($smsmessage));

					//echo "<pre>";print_r($post);

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_account_dashboard');
		}
public function travel_conveyance_voucher_hr_list()
	{
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.status','0')->where('a.account_status','1')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			
			$hrremarks = "";
			$status = $row->hr_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/sale_service_hr_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a><br><br>";
				$hrremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hr_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hrremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_hr_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			
			$livingamt= array();
			$travelamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id, a.tour_date, a.city, a.company_name, a.living_expense_id, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$grandtotal = $lv_amount+$trv_amount;
			$html.="<tr>
				<td colspan='6'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>'APPROVED',
			'account_status'=>'APPROVED',
			'hr_status'=>$pendingforapporaval." ".$hrremarks,
			'traveldata'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}
function sale_service_conveyance_hr_dashboard(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_hr_dashboard');
}
public function sale_service_hr_mark_as_approved()
	{
		    $table = "member_conveyance_information";
			$data = array('hr_status'=>'1',
			'status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hr_dashboard');
		}
public function sale_service_hr_marked_as_not_approved()
	{
		    $table = "member_conveyance_information";
			$remark = $this->input->post('remarks');
			$data = array('hr_status'=>'2',
			'hr_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/sale_service_conveyance_hr_dashboard');
		}
		
		function getperkmrate()
		{
		    $id=$this->uri->segment(3);
		   
		    $rate='';
                $vtypes=$this->db->select('rate')->from('conveyance_vehicle_rate')->where('id',$id)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $rate=floatval($vtypes1->rate);
                }
                
                echo $rate;
		    
		    
		    
		}
		
		public function visit_form(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('sales_force', 'sales_force', 'required|trim');
		$this->form_validation->set_rules('warrenty_status', 'warrenty_status', 'required|trim');
		$this->form_validation->set_rules('nature_of_complaints', 'nature_of_complaints', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/visit_form');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
           $serviceid=$this->input->post('serviceid');
            $charges="";
            $bill_number="";
            $payment_to_collect="";
		   $warranty = $this->input->post('warrenty_status');
		   if($warranty=='1'){
		       $w = "Under Warranty";
		   }else{
		      $w = "Warranty Expired"; 
		   }
		   if($warranty=='1'){
		       $w = "Under Warranty";
		   }else{
		      $w = "Warranty Expired"; 
		   }
		   if($warranty=='2'){
		       
		       $chargable = $this->input->post('chargable');
		       if($chargable=='1'){
		           $charges= $this->input->post('charges');
		           $bill_number= $this->input->post('bill_number');
		           $payment_to_collect = $this->input->post('payment_to_collect');
		       }else{
		           $charges="0";
		           $bill_number="";
		           $payment_to_collect="0";
		       }
		       
		   }else{
		        $chargable="0";
		        $charges="0";
		           $bill_number="";
		           $payment_to_collect="0";
		   }
		   $data=
			array('user_id'=>$user_id,
			'company_name'=>$this->input->post('company_name'),
			'contact_person'=>$this->input->post('contact_person'),
			'contact_number'=>$this->input->post('contact_number'),
			'visit_date'=>date('Y-m-d',strtotime($this->input->post('visit_date'))),
			'sale_force_no'=>$this->input->post('sales_force'),
			'warrenty_status'=>$w,
			'charges'=>$charges,
			'bill_number'=>$bill_number,
			'payment_to_collect'=>$payment_to_collect,
			'chargable'=>$chargable,
			'nature_of_complaints'=>$this->input->post('nature_of_complaints'),
			'engineer'=>$this->input->post('engineer'),
			'status'=>'0',
			'final_status'=>'0',
			'added_on'=>$added_time,
			'odid'=>$this->input->post('odid'),
			'service_case_id'=>$serviceid);
			
			$res = $this->db->insert('engineer_visit',$data);
			
			$engineerid = $this->input->post('engineer');
			$q= $this->db->select('first_name, last_name, email, contact_number')->from('system_users')->where('user_id',$engineerid)->get();
			foreach($q->result() as $engineerdata);
			$username = $engineerdata->first_name." ".$engineerdata->last_name;
			$c= $engineerdata->contact_number;
			$customername = $this->input->post('contact_person');
			$contact_no= $this->input->post('contact_number');
			$companyname = $this->input->post('company_name');
			$visit_date = date('d-m-Y',strtotime($this->input->post('visit_date')));
			$natureofcompaints = $this->input->post('nature_of_complaints');
			
$ms = "New visit has been scheduled for you.\n";
$customerdetail = "Customer Name - ".ucfirst($customername)."\nContact No- ".$contact_no."\nCompany Name- ".$companyname."\nVisit Date- ".$visit_date."\nNature of Complaints- ".$natureofcompaints."\n Presto Testing Instruments.";

$smsmessage = "Hello ".$username."\n".$ms."\n".$customerdetail."\n";
//echo $smsmessage; exit;
/**** SMS INTEGRATION***/

	$postData = array(
    'authkey' => '266631AuRXQ3UyZ5c839e9b',
    'mobiles' => $c,
    'message' => $smsmessage,
    'sender' => 'PRESTQ',
	'DLT_TE_ID'=>"1407162408818012213",	
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
  echo $response;
} 
	
/**** SMS INTEGRATION***/



/***WHATSAPP INTEGRATION***/
$data = [
    'phone' => "91".$c, // Receivers phone
    'body' => $smsmessage, // Message
];
$json = json_encode($data); // Encode data to JSON
// URL for request POST /message
$url = 'https://eu17.chat-api.com/instance88514//message?token=lwpwzff7ubbp6dc6';
// Make a POST request
$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);
// Send a request
$result = file_get_contents($url, false, $options);
/***WHATSAPP INTEGRATION***/


/** EMAIL INTEGRATION **/

$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://prestomitr.com/assets/images/logo-1.png" width="200px;" alt="Prestomitr" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Visit Scheduled</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear, '.ucfirst($engineerdata->first_name).' '.ucfirst($engineerdata->last_name).' <br></td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Message </strong> : '.$smsmessage.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
				
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				//echo $Message; exit;
				$subjectname = "New Visit Scheduled ";
					$this->email->set_mailtype("html");
					$this->email->to($rows->email);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
/** EMAIL INTEGRATION**/
			
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! record successfully added.</span></div>');
			redirect(page_url.'Sales/visit_form');
			
		  
			}
	}
	
	public function engineer_visit_dashboard(){
	$this->load->view('conveyance_voucher/visit_dashboard');
}
public function visit_form_list()
	{
		$visit_data = array();
		$date = date('Y-m-d');
$d2 = date('Y-m-d', strtotime('-30 days'));
$seconddate = $d2." 00:00:00";
		$user_id = $this->uri->segment(3);
	   $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit_view a')->join('system_users_view b','a.user_id=b.user_id','left')->join('system_users_view c','a.engineer=c.user_id','left')->where('a.visit_date>',$seconddate);
		if($user_id){
		    $this->db->where('a.engineer',$user_id);
		}
		//$this->db->where('a.case_status','0');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else if($row->next_status){
			    $edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			

			/** create table **/
			$html='';
			$resteure=$this->db->select('*')->from('visit_schedule_instruments')->where('visitid',$row->id)->get();
			if($resteure->num_rows()>0)
			{

				$html.='<table class="table table-bordered">
				<thead>
				<tr>
				<th>Sno.</th>
				<th>Instrument</th>
				<th>Actual Observation</th>
				<th>Action Taken</th>
				<th>Spare</th>
				</tr>
				</thead>
				<tbody>';

				$j=1;
				foreach($resteure->result() as $rowsss)
				{
					$as='';
					if($rowsss->sparepart==1)
					{
						$as.="YES<br/>";

						$as.=$rowsss->partname."<br/>";

						$as.="<a href='".service_visit_report.$rowsss->picture."' download>Click to download</a>";
					}else
					{
						$as="NO";
					}
				$html.='<tr>
				<td>'.$j.'</td>
				<td>'.$rowsss->instrumentname.'</td>
				<td>'.$rowsss->actual_observation.'</td>
				<td>'.$rowsss->action_taken.'</td>
				<td>'.$as.'</td>
				</tr>';
				$j++;
				}


				$html.='</tbody>
				</table>';
			}

			/** end **/
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no)."<br/>".strtoupper($row->caserefno),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'instrumentdetail'=>$html,
			'chargable'=>$c,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'spare_parts'=>$row->spares_parts,
			'part_name'=>$row->part_name,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function visit_form_list_history()
	{
		$visit_data = array();
	
		$user_id = $this->uri->segment(3);
	   $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		if($user_id){
		    $this->db->where('a.engineer',$user_id);
		}
		$this->db->where('a.case_status','0');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else if($row->next_status){
			    $edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function edit_engineer_visit_data(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('sales_force', 'sales_force', 'required|trim');
		$this->form_validation->set_rules('warrenty_status', 'warrenty_status', 'required|trim');
		$this->form_validation->set_rules('nature_of_complaints', 'nature_of_complaints', 'required|trim');
		$this->form_validation->set_rules('case', 'case', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/edit_engineer_visit_data');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   
		    
		   $photo=$_FILES['service_visit_report']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$report=time().'.'.$cat_image;
				move_uploaded_file($_FILES["service_visit_report"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/service_visit_report/'.$report);
			}else
			{
				$report=$this->input->post('old_files');
				}
				
				
			$spare_parts= $this->input->post('spare_parts');
			if($spare_parts=='1'){
			$photo1=$_FILES['picture']['name'];
			if($photo1<>'')
			{
				$image2=explode('.',$photo1);
				$cat_image1=end($image2);
				$picture=time().'.'.$cat_image1;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/service_visit_report/'.$picture);
			}else
			{
				$picture="";
				}
			$parts_name= $this->input->post('parts_name');
			}else{
			  $picture="";  
			  $parts_name="";
			  $picture="";
			}
		   
		   $data=
			array('user_id'=>$user_id,
			'company_name'=>$this->input->post('company_name'),
			'sale_force_no'=>$this->input->post('sales_force'),
			'warrenty_status'=>$this->input->post('warrenty_status'),
			'nature_of_complaints'=>$this->input->post('nature_of_complaints'),
			'service_report'=>$report,
			'case_status'=>$this->input->post('case'),
			'status'=>'0',
			'spares_parts'=>$spare_parts,
			'part_name'=>$parts_name,
			'picture'=>$picture,
			'observation_of_engineer'=>$this->input->post('actual_ovservation'),
			'engineer'=>$this->input->post('engineer'),
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('engineer_visit',$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! record successfully updated.</span></div>');
			redirect(page_url.'Sales/visit_form');
			
		  
			}
	}
	
	public function engineer_progress(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('case', 'case', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/engineer_progress');
			}else
		{
			
			$leaddetail = $this->input->post('checklead');
			if($leaddetail==1){
				$data = array(
				'visit_id'=>$this->uri->segment(3),
				'engineer_id'=>$user_id,
				'company_name'=>$this->input->post('companyname'),
				'contact_person'=>$this->input->post('contact_person'),
				'contact_number'=>$this->input->post('contact_number'),
				'sales_or_service'=>$this->input->post('sales_Service_lead'),
				'added_on'=>date('Y-m-d H:i:s'));
				$this->db->insert('leads_by_service_team',$data);
				
			}
			
			
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   
		    $photo=$_FILES['service_visit_report']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$report=time().'.'.$cat_image;
				$path=$_SERVER['DOCUMENT_ROOT'].'/image_bank/visitreport/'.$report;
				//echo $path;exit;
			move_uploaded_file($_FILES['service_visit_report']['tmp_name'], $path);
			}else
			{
				$report="";
				}
				 
			
			
		   
		   
		   if($this->input->post('visittype')=='1')
		   {
		    $mode = $this->input->post('mode');
		
		if($mode=='1'){
			$amount  = $this->input->post('amount');
			$vehicletype=0;
			$startreading=0;
			$endreading=0;
			$rateperkms=0;
			
		}else
		{
		    //$amount=$this->input->post('finalamount');
		    $vehicletype=$this->input->post('vtype');
		    $startreading = $this->input->post('start_reading');
		   $endreading = $this->input->post('end_reading');
		   $totaldistance = $endreading-$startreading;
		   $rateperkms = $this->input->post('rate_per_km');
		   $amount = $totaldistance*$rateperkms;
		   
		}
		
		  }else
		  {
		  $mode='';
            $amount =0;
            $vehicletype=0;
            $startreading=0;
            $endreading=0;
            $rateperkms=0;
		      
		  }
		
		$paymentcollected =$this->input->post('payment_collected');
		if($paymentcollected=='0'){
		    $remarks = $this->input->post('remarks');
		    $finalstatus= "1";
		}else{
		    $remarks="";
		    $finalstatus="0";
		} 
		
		
		   $data=
			array(
			'case_status'=>$this->input->post('case'),
			'status'=>'0',
			// 'spares_parts'=>$spare_parts,
			// 'part_name'=>$parts_name,
			// 'picture'=>$picture,
			'service_report'=>$report,
			'payment_collected'=>$paymentcollected,
			'payment_remarks'=>$remarks,
			'final_status'=>$finalstatus,
			'next_status'=>$this->input->post('next_action'),
			// 'observation_of_engineer'=>$this->input->post('actual_ovservation'),
			'engineer'=>$this->input->post('engineer'),
			'engineer_updatetime'=>$added_time,
			'updated_by_engineer'=>$user_id,
			'mode'=>$mode,
			'visittype'=>$this->input->post('visittype'),
			'start_reading'=>$startreading,
			'end_reading'=>$endreading,
			'rate_per_km'=>$rateperkms,
			'vehicletype'=>$vehicletype,
			'amount'=>$amount);
			$this->db->where('id',$this->uri->segment(3));
		$res = $this->db->update('engineer_visit',$data);
		$last_id = $this->uri->segment(3);


		/** UPDATE INSTRUMENT DATA **/
		$insid=$this->input->post('insid');
		$assetid=$this->input->post('assetid');
		for($hj=0;$hj<count($insid);$hj++)
		{
			$instisd=$insid[$hj];
			$assetsid=$assetid[$hj];
			$actualob=$this->input->post('actual_ovservation'.$instisd);
			$actiontaken=$this->input->post('action_taken'.$instisd);
			$spare_parts=$this->input->post('spare_parts'.$instisd);
			if($spare_parts=='1'){
			$photo1=$_FILES['picture'.$instisd]['name'];
			if($photo1<>'')
			{
				$image2=explode('.',$photo1);
				$cat_image1=end($image2);
				$picture=time().rand(1,9999).'.'.$cat_image1;
					$path1=$_SERVER['DOCUMENT_ROOT'].'/image_bank/visitreport/'.$picture;
				move_uploaded_file($_FILES['picture'.$instisd]['tmp_name'],$path1);
			}else
			{
				$picture="";
				}
			$parts_name= $this->input->post('parts_name'.$instisd);
			}else{
			  $picture="";  
			  $parts_name="";
			 
			}


			$updat=array('actual_observation'=>$actualob,'action_taken'=>$actiontaken,'sparepart'=>$spare_parts,'partname'=>$parts_name,'picture'=>$picture);
			$this->db->where('id',$instisd);
			$this->db->update('visit_schedule_instruments',$updat);


			//$this->updatevisitinstrumentobservation($assetsid,$actualob,$actiontaken,$spare_parts,$picture,$parts_name);


		}
		/** END **/


		if($mode=='1'){
			if($res)
			{
				if(isset($_REQUEST['travel_expense'])){	
					$tags1=count($_REQUEST['travel_expense']);
					if($tags1>0)
					{
					$travel_expense=$_REQUEST['travel_expense'];
					$travel_expense_amount=$_REQUEST['travel_expense_amount'];
					
					for($x=0;$x<$tags1;$x++){
					if($travel_expense[$x]!='')
						{
						 $data=array('record_id'=>$last_id,
							'vehicle_type'=>$travel_expense[$x],
							'amount'=>$travel_expense_amount[$x],
							'added_by'=>$user_id,
							'added_on'=>date('Y-m-d H:i:s'));
							$this->db->insert('local_conveyance_detail',$data);
						   
						}
					}
					}
					}
			
			
			
			
		}	
		}	
			if($this->input->post('visittype')=='1')
		   {
		       
		       
		        /** CHECK IF HOD BY PASS IS ALLOWD **/
			    $query1234451 = $this->db->select('a.team_id')->from('presto_team_members a')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
			    if($query1234451->num_rows()>0)
			    {
                $query123445 = $this->db->select('a.team_id')->from('presto_team_members a')->join('prestogroup_teams b','a.team_id=b.team_id')->where('b.by_pass','1')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
                
                if($query123445->num_rows()>0)
                {
                    
                    $updata=array('hod_status'=>'1','hod_reason'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$this->uri->segment(3));
                    $this->db->update('engineer_visit',$updata);
                 }
                 
			    }else
			    {
			        
			         $updata=array('hod_status'=>'1','hod_reason'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$this->uri->segment(3));
                    $this->db->update('engineer_visit',$updata);
			        
			        
			    }
       
			    /** END **/
			    
			    
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! record successfully updated.</span></div>');
			redirect(page_url.'Sales/view_your_dashboard');
		   }else
		   {
		       
		     
		      /** CHECK IF HOD BY PASS IS ALLOWD **/
			    $query1234451 = $this->db->select('a.team_id')->from('presto_team_members a')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
			  
			    if($query1234451->num_rows()>0)
			    {
                $query123445 = $this->db->select('a.team_id')->from('presto_team_members a')->join('prestogroup_teams b','a.team_id=b.team_id')->where('b.by_pass','1')->where('a.employee_id',$_SESSION['logged_in']['user_id'])->get();
                if($query123445->num_rows()>0)
                {
                    
                    $updata=array('hod_status'=>'1','hod_reason'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$this->uri->segment(3));
                    $this->db->update('engineer_visit',$updata);
                 }
                 
			    }else
			    {
			       
			         $updata=array('hod_status'=>'1','hod_reason'=>'AUTO BYPASSED HOD');
                    $this->db->where('id',$this->uri->segment(3));
                    $this->db->update('engineer_visit',$updata);
			        
			        
			    }
       
			    /** END **/
			    
                $this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! record successfully updated.</span></div>');
                redirect(page_url.'Sales/view_your_dashboard');
		   }
		  
			}
	}
	
	public function service_dashboard(){
	$this->load->view('conveyance_voucher/service_dashboard');
}
public function tech_support_dashboard_list()
	{
		$visit_data = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left')->where('case_status','1')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			
			$edit = "<a href='".page_url."Sales/service_progress_update/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			
			$added_time = date('Y-m-d H:i:A',strtotime($row->added_on));
			
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'engineer'=>$engineer,
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'warrenty_status'=>strtoupper($row->warrenty_status),
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}


public function service_progress_update(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('case', 'case', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/service_progress_update');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		  
		   $data=
			array(
			'status'=>'1',
			'case_status'=>$this->input->post('case'),
			'service_update_time'=>$added_time,
			'updatedby'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('engineer_visit',$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! record successfully updated.</span></div>');
			redirect(page_url.'Sales/service_dashboard');
			
		  
			}
	}

public function engineer_history_dashboard(){
	$this->load->view('conveyance_voucher/engineer_history_dashboard');
}
public function engineer_history_dashboard_list()
	{
		$visit_data = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left')->where('updated_by_engineer!=','0')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			
			$edit = "<a href='".page_url."Sales/service_progress_update/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			
			$added_time = date('Y-m-d H:i:A',strtotime($row->added_on));
			$engineer_updatetime = date('Y-m-d H:i:A',strtotime($row->engineer_updatetime));
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'engineer'=>$engineer,
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'warrenty_status'=>strtoupper($row->warrenty_status),
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'updated_on'=>$engineer_updatetime,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function service_history_dashboard(){
	$this->load->view('conveyance_voucher/service_history_dashboard');
}
public function service_history_dashboard_list()
	{
		$visit_data = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left')->where('updatedby!=','0')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			
			$edit = "<a href='".page_url."Sales/service_progress_update/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			
			$added_time = date('Y-m-d H:i:A',strtotime($row->added_on));
			$engineer_updatetime = date('Y-m-d H:i:A',strtotime($row->engineer_updatetime));
			$service_updatetime = date('Y-m-d H:i:A',strtotime($row->service_update_time));
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'engineer'=>$engineer,
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'warrenty_status'=>strtoupper($row->warrenty_status),
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'updated_on'=>$engineer_updatetime,
			'service_updatetime'=>$service_updatetime,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}
public function view_your_dashboard(){
	$this->load->view('conveyance_voucher/view_your_dashboard');
}
public function view_your_dashboard_history(){
	$this->load->view('conveyance_voucher/view_your_dashboard_history');
}

public function hod_engineervisit_dashboard(){
    
	$this->load->view('conveyance_voucher/hod_engineervisit_dashboard');
}

public function hod_visit_form_list()
	{
		$visit_data = array();
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		
		}else
		{
		    $team='NA';
		}
		
		
		
		$user_id = $this->uri->segment(3);
		
		

if($team<>'NA')
		{		
	   $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit_view a')->join('system_users_view b','a.user_id=b.user_id','left')->join('system_users_view c','a.engineer=c.user_id','left')->where('a.warrenty_status','Warranty Expired')->where('a.chargable','2');
		    $this->db->where_in('a.engineer',$team,false);
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{  
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->hod_reason_flag;
			if($casestatus=='1'){
				$edit=$row->hod_reason;
			}else{
			    if($row->chargable=='2'){
				$edit = "<span class='btn btn-success btn-xs' data-toggle='modal' data-target='#account_not_approved".$row->id."'>UPDATE REASON</span>";
			    }else{
			        $edit="";
			    }
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
		    
		  
		    	$edit.= '<div id="account_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/reasonfornotcharging/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Reason</h4>
                                        </div>
                                        <div class="modal-body">
                                       
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
               
              	$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			             
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

function reasonfornotcharging()
{
    $id=$this->uri->segment(3);
    
    $data=array('hod_reason_flag'=>'1','hod_reason'=>$this->input->post('remarks'));
    
        $this->db->where('id',$id);
        $this->db->update('engineer_visit',$data);
        
        $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Reason Updated.</span></div><br/>');
        redirect(page_url.'Sales/hod_engineervisit_dashboard');
    
    
    
}

public function payment_followup_dashboard(){
    $this->load->view('conveyance_voucher/service_followup_team');
}

public function payment_followup_date()
	{
		$visit_data = array();
		
	$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
	    $this->db->where('a.next_status','Payment follow-up');
		$this->db->where('a.final_status','1');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
		    
		    $markasdone = "<a href='".page_url."Sales/task_mark_as_done/".$row->id."'><span class='btn btn-success btn-xs'>Mark as Done</span></a>";
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>strtoupper($row->warrenty_status),
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'payment_remarks'=>$row->payment_remarks,
			'next_status'=>$row->next_status,
			'markasdone'=>$markasdone,
			'addedon'=>$added_time);
			$i++;
		}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function task_mark_as_done()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$field_name = "id";
		$table = "engineer_visit";
	    $finalstatus = "0";
			$data = array('final_status'=>$finalstatus);
			$this->db->where($field_name,$identifier);
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect(page_url.'Sales/payment_followup_dashboard');
		}	

public function service_mark_as_done()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$field_name = "id";
		$table = "engineer_visit";
	    $finalstatus = "1";
			$data = array('case_status'=>$finalstatus);
			$this->db->where($field_name,$identifier);
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect(page_url.'Sales/service_dashboard');
		}	
		
	public function service_related_dashboard()
	{
		$visit_data = array();
		
	$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		$this->db->where('a.case_status','2');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='2'){
			
				$markasclosed = "<a href='".page_url."Sales/service_mark_as_done/".$row->id."' class='btn btn-success btn-xs'>Mark as Done</a>";
			}else{
			    $markasclosed="";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
		    
		    $markasdone = "<a href='".page_url."Sales/task_mark_as_done/".$row->id."'><span class='btn btn-success btn-xs'>Mark as Done</span></a>";
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>strtoupper($row->warrenty_status),
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'payment_remarks'=>$row->payment_remarks,
			'next_status'=>$row->next_status,
			'markasdone'=>$markasdone,
			'markasclosed'=>$markasclosed,
			'addedon'=>$added_time);
			$i++;
		}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function gettotaldays(){
    $first_date = date('Y-m-d',strtotime($this->input->post('date1')));
    $second_date = date('Y-m-d',strtotime($this->input->post('date2')));

$date1 = $first_date;
$date2 = $second_date;
$diff = abs(strtotime($date2) - strtotime($date1));
$years = floor($diff / (365*60*60*24));
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
echo $days+1; exit;
}


public function send_quotation_dashboard(){
    $this->load->view('conveyance_voucher/send_quotation');
}

public function send_quotation_list()
	{
		$visit_data = array();
		$user_id = $this->uri->segment(3);
	$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		$this->db->where('next_status','Send Quotation')->where('final_status','0');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function revisit_dashboard(){
    $this->load->view('conveyance_voucher/revisit_dashboard');
}

public function revisit_list()
	{
		$visit_data = array();
		$user_id = $this->uri->segment(3);
	$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		$this->db->where('next_status','Revisit')->where('final_status','0');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}
public function technical_support_dashboard(){
    $this->load->view('conveyance_voucher/phone_call_technical_support');
}

public function technical_support_list()
	{
		$visit_data = array();
		$user_id = $this->uri->segment(3);
	$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		$this->db->where('next_status','Phone Call')->where('final_status','0');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function recommended_machine_send_to_factory_dashboard(){
    $this->load->view('conveyance_voucher/recommended_machine_send_to_factory_dashboard');
}

public function recommended_machine_send_to_factory_dashboard_list()
	{
		$visit_data = array();
		$user_id = $this->uri->segment(3);
	$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		$this->db->where('next_status','Recommended Machine send to Factory')->where('final_status','0');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<a href='".page_url."Sales/engineer_progress/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			}
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$edit,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}
public function visit_schedule(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('customer_name', 'customer_name', 'required|trim');
	$this->form_validation->set_rules('contact_number', 'contact_number', 'required|trim');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('address', 'address', 'required|trim');
	$this->form_validation->set_rules('visit_date', 'visit_date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('conveyance_voucher/visit_schedule');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		   
		   $data1=
			array('visit_date'=>date('Y-m-d',strtotime($this->input->post('visit_date'))),
			'employee_id'=>strtoupper($this->input->post('who_will_visit')),
			'customer_name'=>strtoupper($this->input->post('customer_name')),
			'contact_number'=>strtoupper($this->input->post('contact_number')),
			'company_name'=>strtoupper($this->input->post('company_name')),
			'address'=>strtoupper($this->input->post('address')),
			'meeting_time'=>date('H:i:s',strtotime($this->input->post('schedule_time'))),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$query = $this->db->select('first_name, last_name, contact_number')->from('system_users')->where('user_id',$this->input->post('who_will_visit'))->get();
			
			foreach($query->result() as $userinfo);
			$customername = $this->input->post('customer_name');
			$address = $this->input->post('address');
			$companyname = $this->input->post('company_name');
			$scheduledtime = date('H:i:s',strtotime($this->input->post('schedule_time')));
			$contact_no = $this->input->post('contact_number');
			$visit_date = date('d-m-Y',strtotime($this->input->post('visit_date')));
			$username = ucfirst($userinfo->first_name)." ".ucfirst($userinfo->last_name);
			
			$ms = "New visit has been scheduled for you.\n";
			$customerdetail = "Customer Name - ".ucfirst($customername)."\n Contact No- ".$contact_no."\n Company Name- ".$companyname." Address- ".$address."\n Visit Date- ".$visit_date."\n Scheduled Time- ".$scheduledtime;
			
				$smsmessage = "Hello ".$username."\n".$ms."\n".$customerdetail."\n";
				
				//echo $smsmessage; exit;
			$qrr = $this->db->select('module_id, user_id, sms, email, whatsaap')->from('module_email_sms_whatsapp_notofication')->where('module_id','1')->where('user_id',$this->input->post('who_will_visit'))->get();
			if($qrr->num_rows()>0){
			   $c = $userinfo->contact_number; 
foreach($qrr->result() as $accesscheck);
	if($accesscheck->sms=='1'){		
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
  echo $response;
} 
	
/**** SMS INTEGRATION***/
			    
			    
	}
	
	
	if($accesscheck->whatsaap=='1'){
	    
	    	$q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','3')->get();
			foreach($q1->result() as $smsdata);
			
			
	    
	    
	    
/***WHATSAPP INTEGRATION***/
$data = [
    'phone' => "91".$c, // Receivers phone
    'body' => $smsmessage, // Message
];
$json = json_encode($data); // Encode data to JSON
// URL for request POST /message
$url = 'https://eu17.chat-api.com/instance88514//message?token=lwpwzff7ubbp6dc6';
// Make a POST request
$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);
// Send a request
$result = file_get_contents($url, false, $options);
/***WHATSAPP INTEGRATION***/
	}
	
	
	if($accesscheck->email=='1'){
	    
	    $q1 = $this->db->select('*')->from('email_sms_whatsapp_template')->where('sms_id','2')->get();
			foreach($q1->result() as $smsdata);
			
			

/** EMAIL INTEGRATION **/

$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://prestomitr.com/assets/images/logo-1.png" width="200px;" alt="Prestomitr" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>Sales Visit Scheduled</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear, '.ucfirst($userinfo->first_name).' '.ucfirst($userinfo->last_name).' <br></td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;"><strong>Message </strong> : '.$smsmessage.'</td>
					  </tr>
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
				
					 <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td>
					   </td>
					  </tr>
					</table>
					</td>
				  </tr>
				</table>';
				//echo $Message; exit;
				$subjectname = "New Sales Visit Scheduled ";
					$this->email->set_mailtype("html");
					$this->email->to($rows->email);
					//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
    				$result11=$this->email->send();
					
/** EMAIL INTEGRATION**/
	}
	
			}
			
			
			
			$res = $this->db->insert('visit_schedule',$data1);
			$this->session->set_flashdata('message','<span class="alert alert-success">Record successfully added.</span>');
			redirect(page_url.'Sales/visit_schedule');
			}
	}
public function visit_schedule_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as fname, c.last_name as lname')->from('visit_schedule a')->join('system_users b','a.employee_id=b.user_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.employee_id',$user_id)->order_by('a.visit_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$edit = "<a href='".page_url."Sales/edit_visit_schedule/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			if($row->status!==''){
			    $updatestatus=$row->status."<br><br>".$row->remarks;
			}else{
			    	$updatestatus = "<a href='".page_url."Sales/update_visit_status/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span></a>";
			}
		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'visit_date'=>$row->visit_date,
			'meeting_time'=>date('h:i A',strtotime($row->meeting_time)),
			'employee'=>strtoupper($row->first_name." ".$row->last_name),
			'added_by'=>strtoupper($row->fname." ".$row->lname),
			'added_time'=>$added_time,
			'edit'=>$edit,
			'updatestatus'=>$updatestatus);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function visit_schedule_dashboard(){
	$this->load->view('conveyance_voucher/visit_schedule_list');
}

public function sales_daily_update(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('customer_name', 'customer_name', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'contact_number', 'required|trim');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('visit_number', 'visit_number', 'required|trim');
		$this->form_validation->set_rules('action_taken', 'action_taken', 'required|trim');
		$this->form_validation->set_rules('stage', 'stage', 'required|trim');
		$this->form_validation->set_rules('value', 'value', 'required|trim');
		$this->form_validation->set_rules('machine_name', 'machine_name', 'required|trim');
		$this->form_validation->set_rules('next_action_plan', 'next_action_plan', 'required|trim');
		$this->form_validation->set_rules('date_of_next_plan', 'date_of_next_plan', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('conveyance_voucher/sales_daily_update');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		 $photo1=$_FILES['visiting_card']['name'];
			if($photo1<>'')
			{
				$image2=explode('.',$photo1);
				$cat_image1=end($image2);
				$picture=time().rand(1,9999).'.'.$cat_image1;
					$path1=$_SERVER['DOCUMENT_ROOT'].'/image_bank/sale_visit/'.$picture;
				move_uploaded_file($_FILES['visiting_card']['tmp_name'],$path1);
			}else
			{
				$picture="";
				}
				
		   $data=
			array('date_of_next_plan'=>date('Y-m-d',strtotime($this->input->post('date_of_next_plan'))),
			'employee_id'=>$user_id,
			'customer_name'=>strtoupper($this->input->post('customer_name')),
			'contact_number'=>strtoupper($this->input->post('contact_number')),
			'company_name'=>strtoupper($this->input->post('company_name')),
			'visit_number'=>strtoupper($this->input->post('visit_number')),
			'action_taken'=>strtoupper($this->input->post('action_taken')),
			'stage'=>strtoupper($this->input->post('stage')),
			'sale_value'=>strtoupper($this->input->post('value')),
			'machine_name'=>strtoupper($this->input->post('machine_name')),
			'remarks'=>strtoupper($this->input->post('remarks')),
			'next_action_plan'=>strtoupper($this->input->post('next_action_plan')),
			'visiting_card'=>$picture,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('field_sales_daily_update',$data);
			$this->session->set_flashdata('message','<span class="alert alert-success">Record successfully added.</span>');
			redirect(page_url.'Sales/sales_daily_update');
			}
	}
public function sales_daily_update_dashboard(){
	$this->load->view('conveyance_voucher/sales_daily_update_dashboard');
}
public function sales_daily_update_dashboard_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$scheduler_data = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('field_sales_daily_update a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.employee_id',$user_id)->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			if($row->visiting_card){
				$visitingcard = "<a href='".sale_visit."".$row->visiting_card."' download>Download</a>";
			}else{
				$visitingcard="";
			}
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$edit = "<a href='".page_url."Sales/edit_sales_daily_update/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'employee'=>strtoupper($row->first_name." ".$row->last_name),
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'company_name'=>strtoupper($row->company_name),
			'visit_number'=>strtoupper($row->visit_number),
			'action_taken'=>strtoupper($row->action_taken),
			'stage'=>strtoupper($row->stage),
			'remarks'=>$row->remarks,
			'sale_value'=>strtoupper($row->sale_value),
			'machine_name'=>strtoupper($row->machine_name),
			'next_action_plan'=>strtoupper($row->next_action_plan),
			'date_of_next_plan'=>$row->date_of_next_plan,
			'visitingcard'=>$visitingcard,
			'added_time'=>$added_time,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function edit_visit_schedule(){
	$this->load->view('conveyance_voucher/edit_visit_schedule');
}
public function update_visit_schedule(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('customer_name', 'customer_name', 'required|trim');
	$this->form_validation->set_rules('contact_number', 'contact_number', 'required|trim');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('address', 'address', 'required|trim');
	$this->form_validation->set_rules('visit_date', 'visit_date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('conveyance_voucher/edit_visit_schedule');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		   
		   $data=
			array('visit_date'=>date('Y-m-d',strtotime($this->input->post('visit_date'))),
			'employee_id'=>strtoupper($this->input->post('who_will_visit')),
			'customer_name'=>strtoupper($this->input->post('customer_name')),
			'contact_number'=>strtoupper($this->input->post('contact_number')),
			'company_name'=>strtoupper($this->input->post('company_name')),
			'address'=>strtoupper($this->input->post('address')),
			'meeting_time'=>date('H:i:s',strtotime($this->input->post('schedule_time'))),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('visit_schedule',$data);
			$this->session->set_flashdata('message','<span class="alert alert-success">Record successfully updated.</span>');
			redirect(page_url.'Sales/visit_schedule_dashboard');
			}
	}
public function edit_sales_daily_update(){
	$this->load->view('conveyance_voucher/edit_sales_daily_update');
}
public function update_sales_daily_update(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('customer_name', 'customer_name', 'required|trim');
		$this->form_validation->set_rules('contact_number', 'contact_number', 'required|trim');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('visit_number', 'visit_number', 'required|trim');
		$this->form_validation->set_rules('action_taken', 'action_taken', 'required|trim');
		$this->form_validation->set_rules('stage', 'stage', 'required|trim');
		$this->form_validation->set_rules('value', 'value', 'required|trim');
		$this->form_validation->set_rules('machine_name', 'machine_name', 'required|trim');
		$this->form_validation->set_rules('next_action_plan', 'next_action_plan', 'required|trim');
		$this->form_validation->set_rules('date_of_next_plan', 'date_of_next_plan', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('conveyance_voucher/edit_sales_daily_update');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		 
		 $photo1=$_FILES['visiting_card']['name'];
			if($photo1<>'')
			{
				$image2=explode('.',$photo1);
				$cat_image1=end($image2);
				$picture=time().rand(1,9999).'.'.$cat_image1;
					$path1=$_SERVER['DOCUMENT_ROOT'].'/image_bank/sale_visit/'.$picture;
				move_uploaded_file($_FILES['visiting_card']['tmp_name'],$path1);
			}else
			{
				$picture=$this->input->post('old_file');
				}
				
		   $data=
			array('date_of_next_plan'=>date('Y-m-d',strtotime($this->input->post('date_of_next_plan'))),
			'employee_id'=>$user_id,
			'customer_name'=>strtoupper($this->input->post('customer_name')),
			'contact_number'=>strtoupper($this->input->post('contact_number')),
			'company_name'=>strtoupper($this->input->post('company_name')),
			'visit_number'=>strtoupper($this->input->post('visit_number')),
			'action_taken'=>strtoupper($this->input->post('action_taken')),
			'stage'=>strtoupper($this->input->post('stage')),
			'sale_value'=>strtoupper($this->input->post('value')),
			'machine_name'=>strtoupper($this->input->post('machine_name')),
			'next_action_plan'=>strtoupper($this->input->post('next_action_plan')),
			'visiting_card'=>$picture,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('field_sales_daily_update',$data);
			$this->session->set_flashdata('message','<span class="alert alert-success">Record successfully added.</span>');
			redirect(page_url.'Sales/sales_daily_update_dashboard');
			}
	}
	
	public function local_conveyance_data(){
		$this->load->view('conveyance_voucher/conveyance_voucher_account_dashboard');
	}
	
public function engineer_visit_hod_dashboard(){
	$this->load->view('conveyance_voucher/engineer_visit_hod_dashboard');
}
public function engineer_visit_hod_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		
		
		
		$scheduler_data = array();
		if($team<>'NA')
		{
		$query = $this->db->select('a.visit_date, a.id, a.company_name,a.contact_person, a.sale_force_no, a.warrenty_status, a.charges, a.engineer, a.visittype,a.mode,a.start_reading, a.end_reading,a.rate_per_km, a.vehicletype, a.amount, b.first_name, b.last_name,a.hod_payment_status, a.hod_conveyance_reamrk')->from('engineer_visit a')->join('system_users b','a.engineer=b.user_id','left')->where_in('a.engineer',$team,false)->where('a.hod_payment_status','0')->where('a.hod_conveyance_reamrk','')->order_by('a.visit_date','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				
				$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px; background-color:yellow'>Vehicle Type</th><th style='padding:2px 2px 2px 2px;  background-color:yellow'>Amount</th></tr>";
				
				$query = $this->db->select('a.vehicle_type, a.amount, b.options')->from('local_conveyance_detail a	')->join('conveyance_type_options b','a.vehicle_type=b.id','left')->where('record_id',$row->id)->get();
				foreach($query->result() as $vehicledata){
				$finalval[] = $vehicledata->amount;
				$totalval = array_sum($finalval);
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($vehicledata->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($vehicledata->amount)."</td>";
				$html.="</tr>";
				}
				if($query->num_rows()>0){
				$html.="<tr><td style='text-align:center; font-weight:bold;'>TOTAL</td><td style='text-align:center; background-color:red; color:#fff; font-weight:bold;'>".$totalval."</td></tr>";
				}
				$html.="</table>";
					
				}else{
					if($row->vehicletype=='1'){
						$tp = "CAR";
					}else{
						$tp="BIKE";
					}
					$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px; background-color:yellow'>Vehicle Type</th><th style='padding:2px 2px 2px 2px;  background-color:yellow'>Start Reading</th><th style='padding:2px 2px 2px 2px; background-color:yellow'>End Reading</th><th style='padding:2px 2px 2px 2px; background-color:yellow'>Rate Per KM</th><th style='padding:2px 2px 2px 2px; background-color:yellow'>AMOUNT</th></tr>";
					$html.="<tr>
					<td>".$tp."</td>
					<td>".$row->start_reading."</td>
					<td>".$row->end_reading."</td>
					<td>".$row->rate_per_km."</td>
					<td style='background-color:red; color:#fff; font-weight:bold;'>".$row->amount."</td>
					</tr></table>";
					
					$md = "OWN";
				}
			
			$hodremarks = "";
			$status = $row->hod_payment_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/hod_mark_as_approved_visit_data/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>REJECT</span>";
				
				
			}else if($status=='0' && $row->hod_conveyance_reamrk!==''){
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
				
				
				}else{
				$pendingforapporaval= "APPROVED";
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_marked_as_not_approved_visit_data/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				
				
			$scheduler_data[] = array('sr_no'=>$i,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'visit_date'=>strtoupper($row->visit_date),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'mode'=>$md,
			'vehicletype'=>$vtype,
			'sale_force_no'=>$row->sale_force_no,
			'vehicle_data'=>$html,
			'hodremarks'=>$pendingforapporaval."  ".$hodremarks);
			$i++;
		}
		}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function hod_mark_as_approved_visit_data()
	{
		 $table = "engineer_visit";
			$data = array('hod_payment_status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/engineer_visit_hod_dashboard');
		}
public function hod_marked_as_not_approved_visit_data()
	{
		    $table = "engineer_visit";
			$remark = $this->input->post('remarks');
			$data = array('hod_status'=>'0',
			'hod_conveyance_reamrk'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/engineer_visit_hod_dashboard');
		}
	
	public function local_conveyance_account_data(){
		$this->load->view('conveyance_voucher/local_conveyance_data');
	}
	
public function engineer_visit_account_hr_dashboard(){
	$this->load->view('conveyance_voucher/engineer_visit_account_dashboard');
}

public function engineer_visit_account_hr_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.visit_date, a.id, a.company_name,a.contact_person, a.sale_force_no, a.warrenty_status, a.charges, a.engineer, a.visittype,a.mode,a.start_reading, a.end_reading,a.rate_per_km, a.vehicletype, a.amount, b.first_name, b.last_name,a.hod_payment_status, a.hod_conveyance_reamrk, a.account_payment_status')->from('engineer_visit_view a')->join('system_users_view b','a.engineer=b.user_id','left')->where('a.hod_payment_status','1')->where('a.account_payment_status','0')->order_by('a.visit_date','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				
				$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px; background-color:yellow'>Vehicle Type</th><th style='padding:2px 2px 2px 2px;  background-color:yellow'>Amount</th></tr>";
				
				$query = $this->db->select('a.vehicle_type, a.amount, b.options')->from('local_conveyance_detail a	')->join('conveyance_type_options b','a.vehicle_type=b.id','left')->where('record_id',$row->id)->get();
				foreach($query->result() as $vehicledata){
				$finalval[] = $vehicledata->amount;
				$totalval = array_sum($finalval);
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($vehicledata->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($vehicledata->amount)."</td>";
				$html.="</tr>";
				}
				if($query->num_rows()>0){
				$html.="<tr><td style='text-align:center; font-weight:bold;'>TOTAL</td><td style='text-align:center; background-color:red; color:#fff; font-weight:bold;'>".$totalval."</td></tr>";
				}
				$html.="</table>";
					
				}else{
					if($row->vehicletype=='1'){
						$tp = "CAR";
					}else{
						$tp="BIKE";
					}
					$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px; background-color:yellow'>Vehicle Type</th><th style='padding:2px 2px 2px 2px;  background-color:yellow'>Start Reading</th><th style='padding:2px 2px 2px 2px; background-color:yellow'>End Reading</th><th style='padding:2px 2px 2px 2px; background-color:yellow'>Rate Per KM</th><th style='padding:2px 2px 2px 2px; background-color:yellow'>AMOUNT</th></tr>";
					$html.="<tr>
					<td>".$tp."</td>
					<td>".$row->start_reading."</td>
					<td>".$row->end_reading."</td>
					<td>".$row->rate_per_km."</td>
					<td style='background-color:red; color:#fff; font-weight:bold;'>".$row->amount."</td>
					</tr></table>";
					
					$md = "OWN";
				}
			
			$hodremarks = "";
			$accountremarks = "";
			$status = $row->account_payment_status;
			if($status=='0'){
				//$pendingforapporaval= "<a href='".page_url."/Sales/account_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$accountremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#account_not_approved".$row->id."'>MARK AS APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED<br><br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$pendingforapporaval = "APPROVED";
				
				
			}
			$accountremarks.= '<div id="account_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<form id="loginForm" method="post" action="'.page_url.'Sales/engineer_visit_account_marked_done_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">MARK AS APPROVED</h4>
                                        </div>
                                        <div class="modal-body">
                                        
                                        <div class="row">
                                         
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">DEDUCTION (if any)</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<input type="text" class="form-control" style="width:100%" name="deduction" required value="0"></textarea>
												    </div>
                                                </div>
                                            
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:300px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			$status = $row->hod_payment_status;
			if($status=='2'){
				$hodstatus = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_conveyance_reamrk."</strong>";
				
			}else{
				$hodstatus= "APPROVED";
				
				
			}
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				
				
			$scheduler_data[] = array('sr_no'=>$i,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'visit_date'=>strtoupper($row->visit_date),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'mode'=>$md,
			'vehicletype'=>$vtype,
			'sale_force_no'=>$row->sale_force_no,
			'vehicle_data'=>$html,
			'hodremarks'=>$hodstatus,
			'account_remarks'=>$accountremarks);
			$i++;
		}
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function engineer_visit_account_marked_done_approved()
	{
		    $table = "engineer_visit";
			$remark = $this->input->post('remarks');
			$dedu=$this->input->post('deduction');
			$data = array('account_payment_status'=>'1',
			'deduction'=>$dedu,
			'account_payment_conveyance_remarks'=>$remark);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update($table,$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><span style="color:#000">Thank You! Your status successfully updated.</span></div>');
			redirect(page_url.'Sales/engineer_visit_account_hr_dashboard');
		}
		
	public function update_visit_status(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('status', 'status', 'required|trim');
		$this->form_validation->set_rules('remarks', 'remarks', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('conveyance_voucher/visit_update_status');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
            $added_time = date('Y-m-d H:i:s');
		
			
			$data=
			array('status'=>$this->input->post('status'),
			'remarks'=>$this->input->post('remarks'));
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('visit_schedule',$data);
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
			redirect(page_url.'Sales/visit_schedule_dashboard/');
		
			
		  }
	}
	
	public function filter_visit_data(){
	    $data = array('employee'=>$this->input->post('engineer'),
	    'start_date'=>date('Y-m-d',strtotime($this->input->post('start_date'))),
	    'end_date'=>date('Y-m-d',strtotime($this->input->post('end_date'))));
	    //echo "<pre>"; print_r($data); exit;
	$this->load->view('conveyance_voucher/filter_visit_data',$data);
}

public function filter_visit_data_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$employee = $this->uri->segment(3);
		$startdate = $this->uri->segment(4);
		$enddate = $this->uri->segment(5);
		$scheduler_data = array();
		$query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as fname, c.last_name as lname')->from('visit_schedule a')->join('system_users b','a.employee_id=b.user_id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.employee_id',$employee)->where('visit_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->order_by('a.visit_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$added_time = date('Y-m-d H:i A',strtotime($row->added_on));
			$edit = "<a href='".page_url."Sales/edit_visit_schedule/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			if($row->status!==''){
			    $updatestatus=$row->status."<br><br>".$row->remarks;
			}else{
			    	$updatestatus = "<span style='color:red; font-weight:bold;'>Status Not Updated.</span>";
			}
		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'customer_name'=>strtoupper($row->customer_name),
			'contact_number'=>strtoupper($row->contact_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'visit_date'=>$row->visit_date,
			'meeting_time'=>date('h:i A',strtotime($row->meeting_time)),
			'employee'=>strtoupper($row->first_name." ".$row->last_name),
			'added_by'=>strtoupper($row->fname." ".$row->lname),
			'added_time'=>$added_time,
			'edit'=>$edit,
			'updatestatus'=>$updatestatus);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function view_history_of_travel_conveyance(){
    $this->load->view('conveyance_voucher/sale_service_conveyance_voucher_history.php');
}

public function member_travel_tour_conveyance_history()
	{
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information a')->join('system_users b','a.employee_id=b.user_id','left')->where('a.employee_id',$user_id)->where('a.account_status','1')->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			$status = $row->status;
			if($status=='0'){
				$sta = "UNDER REVIEW";
			}else{
				$sta = "CLEARED";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			if($row->hod_status=='1'){
				$hod_status = "APPROVED";
			}else if($row->hod_status=='2'){
				$hod_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hod_remarks."</strong>";
			}else{
				$hod_status = "PENDING FOR REVIEW";
			}
			
			if($row->account_status=='1'){
				$account_status = "APPROVED";
			}else if($row->account_status=='2'){
				$account_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$account_status = "PENDING FOR REVIEW";
			}
			
			if($row->hr_status=='1'){
				$hr_status = "APPROVED";
			}else if($row->hr_status=='2'){
				$hr_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hr_remarks."</strong>";
			}else{
				$hr_status = "PENDING FOR REVIEW";
			}
			
			
			$livingamt= array();
			$travelamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>DATE</th><th style='padding:2px 2px 2px 2px'>CITY</th> <th style='padding:2px 2px 2px 2px'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th style='padding:2px 2px 2px 2px'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id, a.tour_date, a.city, a.company_name, a.living_expense_id, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="</tr>";
				
			}
		
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$grandtotal = $lv_amount+$trv_amount;
			$html.="<tr>
				<td colspan='6'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'status'=>$sta,
			'traveldata'=>$html,
			'hod_status'=>$hod_status,
			'account_status'=>$account_status);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

	public function local_conveyance_history_dashboard(){
		$this->load->view('conveyance_voucher/conveyance_voucher_history');
	}
	
	public function conveyance_voucher_history_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		
		$scheduler_data = array();
		$query = $this->db->select('*')->from('conveyance_voucher')->where('added_by',$user_id)->where('account_status','1')->order_by('travel_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
			}else{
				$md = "OWN";
			}
			$hodremarks = "";
			$status = $row->account_status;
			if($status=='0'){
				$sta = "UNDER REVIEW";
			}else{
				$sta = "CLEARED";
			}
			
			$hod_status= $row->hod_status;
			$account_status= $row->account_status;
			$hr_status= $row->hr_status;
			if($hod_status=='0'){
				$hodsta = "UNDER REVIEW";
			}else if($hod_status=='1'){
				$hodsta = "APPROVED";
			}else{
				$hodsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hod_remarks;;
			}
			if($hod_status=='0'){
				$accountsta="";
				$hrsta="";
			}else{
			$account_status= $row->account_status;
			if($account_status=='0'){
				$accountsta = "UNDER REVIEW";
			}else if($hod_status=='1'){
				$accountsta = "APPROVED";
			}else{
				$accountsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->account_remarks;;
			}
			}
			if($account_status=='0' && $hod_status=='0'){
				$hrsta="";
				$accountsta="";
				$hodsta="";
			}else{
			$hr_status= $row->hr_status;
			if($hod_status=='1'){
				$hrsta = "APPROVED";
			}else{
				$hrsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hr_remarks;;
			}
			}
			
			/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
                
			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'vehicletype'=>$vtype,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'deduction'=>$row->deduction,
			'status'=>$sta,
			'hod_status'=>$hodsta,
			'account_status'=>$accountsta,
			'hr_status'=>$hrsta,
			'edit'=>$edit);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function visit_review_for_hods()
	{
		$visit_data = array();
		$date = date('Y-m-d');
		$d2 = date('Y-m-d', strtotime('-60 days'));
		$seconddate = $d2." 00-00-00";
		$user_id = $this->uri->segment(3);
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit_view a')->join('system_users_view b','a.user_id=b.user_id','left')->join('system_users_view c','a.engineer=c.user_id','left')->where('a.visit_date>=',$d2);
		if($user_id){
		    $this->db->where('a.engineer',$user_id);
		}
		$this->db->where('a.final_status','0');
		//$this->db->where('a.engineer_progress_status','1');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
				$updatevisitprogress = "Updated";
			}else if($row->next_status){
			    $edit="PROGRESS HAS BEEN UPDATED";
			    $updatevisitprogress = "Updated";
			}else{
				$edit = "<span style='color:red'>Pending at Engineer end</span>";
				$updatevisitprogress = "Pending at Engineer end";
			}
			
			
			
			
			//$hodsta = "<a href='".page_url."Sales/hodmarkasdone/".$row->id."/".$row->service_case_id."' class='btn btn-success btn-xs'>MARK AS DONE</a>";
			
			$hodsta = "<span class='btn btn-success btn-xs' onclick='showmodal(".$row->id.",".$row->service_case_id.")'>MARK AS DONE</span>";
			if($this->session->userdata['logged_in']['user_id']==5){
			$changeengieer = "<span class='btn btn-success btn-xs' onclick='showmodalchangeeng(".$row->id.",".$row->service_case_id.")'>Change Engineer</span>";
			}else{
				$changeengieer = "";
			}
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$q= $this->db->select('sales_or_service')->from('leads_by_service_team')->where('visit_id',$row->id)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rows);
				$lead = $rows->sales_or_service;
			}else{
				$lead = "";
			}

			/** create table **/
			$html='';
			$resteure=$this->db->select('*')->from('visit_schedule_instruments')->where('visitid',$row->id)->get();
			if($resteure->num_rows()>0)
			{

				$html.='<table class="table table-bordered">
				<thead>
				<tr>
				<th>Sno.</th>
				<th>Instrument</th>
				<th>Actual Observation</th>
				<th>Action Taken</th>
				<th>Spare</th>
				</tr>
				</thead>
				<tbody>';

				$k=1;
				foreach($resteure->result() as $rowsss)
				{
					$as='';
					if($rowsss->sparepart==1)
					{
						$as.="YES<br/>";

						$as.=$rowsss->partname."<br/>";

						$as.="<a href='".service_visit_report.$rowsss->picture."' download>Click to download</a>";
					}else
					{
						$as="NO";
					}
				$html.='<tr>
				<td>'.$k.'</td>
				<td>'.$rowsss->instrumentname.'</td>
				<td>'.$rowsss->actual_observation.'</td>
				<td>'.$rowsss->action_taken.'</td>
				<td>'.$as.'</td>
				</tr>';
				$k++;
				}


				$html.='</tbody>
				</table>';
			}

			/** end **/
			
			$visit_data[] = array('sr_no'=>$i.'<br>'.$changeengieer,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no)."<br/>".$row->caserefno,
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'lead'=>$lead,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$updatevisitprogress,
			'hod_sta'=>$hodsta,
			'addedon'=>$added_time,
			'instrumentdetail'=>$html);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function hodmarkasdone(){
    $id = $this->input->post('leadid');
    $serviceid = $this->input->post('servicecaseid');

/** SF PART **/
$sf_synced=$this->checkifsf_synced_visit($id);
/** END **/

    $data = array('final_status'=>'1','action_taken'=>$this->input->post('actiontaken'),
	'customer_remarks'=>$this->input->post('customer_remarks'));
	//echo "<pre>"; print_r($data); exit;
   		$this->db->where('id',$id);
    	$this->db->update('engineer_visit',$data);

// /** SF PART **/
    if(count($sf_synced)>0)
    {


    	$this->updatevisitinsalesforce($sf_synced);

		$resteure=$this->db->select('*')->from('visit_schedule_instruments')->where('visitid',$id)->get();
		if($resteure->num_rows()>0)
		{
		$i=1;
		foreach($resteure->result() as $rowsss)
		{
			if($rowsss->sparepart==1)
			{
				$sp="Yes";
			}else
			{
				$sp="NO";
			}
		$this->updatevisitinstrumentobservation($rowsss->assetid,$rowsss->actual_observation,$rowsss->action_taken,$sp,$rowsss->picture,$rowsss->partname);
		}
		}

    }




    $resertt=$this->checkifthisisfromorder($id);
   if($resertt>0)
    {
        $fdststs=array('closedbyhod'=>'1');
        $this->db->where('record_id',$resertt);
        $this->db->update('service_request_followup',$fdststs);

		$data2=array('close_status'=>1,
		'close_date'=>date('Y-m-d'),
		'close_by'=>$user_id);
		$this->db->where('sr_id',$serviceid);
		$this->db->update('service_detail',$data2);
        
    }
    $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
			redirect(page_url.'Sales/engineer_visit_dashboard/');
}


function checkifthisisfromorder($id)
{
    
    $restyy=$this->db->select('odid')->from('engineer_visit')->where('id',$id)->where('odid !=','0')->get();
    if($restyy->num_rows()>0)
    {
    foreach($restyy->result() as $restyy1);
    return $restyy1->odid;
    }else
    {
        return 0;
        
    }
    
}

function payment_collected()
{
    $this->load->view('sampletest/paymentcollected');
}

function addpaymentcollected()
{
    
	$this->form_validation->set_rules('pname', 'Party Name', 'required|trim');
	$this->form_validation->set_rules('ptype', 'Payment Type', 'required|trim');
		$this->form_validation->set_rules('amt', 'Amount', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
        if ($this->form_validation->run() == FALSE)
        {
        
        $this->load->view('sampletest/paymentcollected');
        }else
        {
        
            $data=array('partyname'=>$this->input->post('pname'),'payment'=>$this->input->post('amt'),'type'=>$this->input->post('ptype'),'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
            $this->db->insert('payment_collected',$data);
        
        	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Payment Collection Added.</span></div><br/>');
				redirect(page_url.'Sales/payment_collected');
        
        }
    
}

function paymentcollectedlist()
{
    $scheduler_data=array();
    $restye=$this->db->select('*')->from('payment_collected')->where('addedBy',$_SESSION['logged_in']['user_id'])->order_by('id','ASC')->get();
    if($restye->num_rows()>0)
{
    $i=1;
    foreach($restye->result() as $row)
    {
        if($row->type=='1')
        {
            $typ="Advance Amount";
        }else
        {
             $typ="Balance Amount";
        }
			$edit = "<a href='javascript:;' onclick='revertdata(".$row->id.")'><i class='fa fa-backward'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'addedOn'=>date('d-m-Y H:i:s',strtotime($row->addedOn)),
			'party'=>strtoupper($row->partyname),
			'type'=>strtoupper($typ),
			'payment'=>strtoupper($row->payment),
			'edit'=>$edit);
			$i++;
    }
			
}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

}


function salespaymentcollection()
{
    $this->load->view('sampletest/allpaymentcollected');
}
function paymentcollectedalllist()
{
    $scheduler_data=array();
    $restye=$this->db->select('*')->from('payment_collected')->order_by('id','ASC')->get();
    if($restye->num_rows()>0)
{
    $i=1;
    foreach($restye->result() as $row)
    {
        if($row->type=='1')
        {
            $typ="Advance Amount";
        }else
        {
             $typ="Balance Amount";
        }
        
       $Rest= $this->db->select('first_name,last_name')->from('system_users_view')->where('user_id',$row->addedBy)->get();
       
       if($Rest->num_rows()>0)
       {
           foreach($Rest->result() as $Rest1);
           $naaam=$Rest1->first_name.' '.$Rest1->last_name;
           
       }else
       {
           $naaam='';
       }
			$edit = "<a href='javascript:;' onclick='revertdata(".$row->id.")'><i class='fa fa-backward'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'addedOn'=>date('d-m-Y H:i:s',strtotime($row->addedOn)),
			'addedby'=>$naaam,
			'party'=>strtoupper($row->partyname),
			'type'=>strtoupper($typ),
			'payment'=>floatval($row->payment),
			'edit'=>$edit);
			$i++;
    }
			
}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

}



function revertpayment()
{
    
   $id= $this->uri->segment(3);
   $this->db->where('id',$id);
   $this->db->delete('payment_collected');
   
   	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Record Reverted.</span></div><br/>');
				redirect(page_url.'Sales/payment_collected');
}

	public function engineer_progress_hod(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('case', 'case', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('conveyance_voucher/update_progress_hod');
			}else
		{
		   date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   		
			$spare_parts= $this->input->post('spare_parts');
			if($spare_parts=='1'){
			
			$parts_name= $this->input->post('parts_name');
			}else{
			  $picture="";  
			  $parts_name="";
			  $picture="";
			}
		   
		   
		   if($this->input->post('visittype')=='1')
		   {
		    $mode = $this->input->post('mode');
		
		if($mode=='1'){
			$amount  = $this->input->post('amount');
			$vehicletype=0;
			$startreading=0;
			$endreading=0;
			$rateperkms=0;
			
		}else
		{
		    //$amount=$this->input->post('finalamount');
		    $vehicletype=$this->input->post('vtype');
		    $startreading = $this->input->post('start_reading');
		   $endreading = $this->input->post('end_reading');
		   $totaldistance = $endreading-$startreading;
		   $rateperkms = $this->input->post('rate_per_km');
		   $amount = $totaldistance*$rateperkms;
		   
		}
		
		  }else
		  {
		  $mode='';
            $amount =0;
            $vehicletype=0;
            $startreading=0;
            $endreading=0;
            $rateperkms=0;
		      
		  }
		
		$paymentcollected =$this->input->post('payment_collected');
	
		$finalstatus = "0";
		   $data=
			array(
			'payment_collected'=>$paymentcollected,
			'payment_remarks'=>$remarks,
			'next_status'=>$this->input->post('next_action'));
			$this->db->where('id',$this->uri->segment(3));
		$res = $this->db->update('engineer_visit',$data);
		$last_id = $this->uri->segment(3);
		
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Record successfully updated.</span></div><br/>');
				redirect(page_url.'Sales/engineer_visit_dashboard');
		  
			}
	}
	
	public function engineer_visit_dashboard_history(){
	$this->load->view('conveyance_voucher/visit_dashboard_history');
}

public function visit_review_for_hods_history()
	{
		$visit_data = array();
		$user_id = $this->uri->segment(3);
	   $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit_view a')->join('system_users_view b','a.user_id=b.user_id','left')->join('system_users_view c','a.engineer=c.user_id','left');
		if($user_id){
		    $this->db->where('a.engineer',$user_id);
		}
		$this->db->where('a.final_status','1');
		//$this->db->where('a.engineer_progress_status','1');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else if($row->next_status){
			    $edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<span style='color:red'>Pending at Engineer end</span>";
			}
			
			if($row->engineer_progress_status=='1' && $row->final_status=='0'){
			    $updatevisitprogress = "<a href='".page_url."Sales/engineer_progress_hod/".$row->id."'><span class='btn btn-success btn-xs'>Update Progress</span></a>";
			}else{
			    $updatevisitprogress="updated";
			}
			
			$hodsta = "<a href='".page_url."Sales/hodmarkasdone/".$row->id."' class='btn btn-success btn-xs'>MARK AS DONE</a>";
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$updatevisitprogress,
			'hod_sta'=>$hodsta,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function engineer_visit_dashboard_history_filter(){
	$fromdate=date('Y-m-d',strtotime($this->input->post('frmdate')));
		$todate=date('Y-m-d',strtotime($this->input->post('todate')));
		$data = array('first_date'=>$fromdate,
		'last_date'=>$todate);
	$this->load->view('conveyance_voucher/visit_dashboard_history_filtereddata',$data);
}
public function visit_review_for_hods_history_filterdata()
	{
		$fromdate=$this->uri->segment(3);
		$todate=$this->uri->segment(4);
		
		
		$visit_data = array();
		
	   $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		
		//$this->db->where('a.final_status','1');
		$this->db->where('a.visit_date BETWEEN "'. date('Y-m-d', strtotime($fromdate)). '" and "'. date('Y-m-d', strtotime($todate)).'"');
		//$this->db->where('a.engineer_progress_status','1');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else if($row->next_status){
			    $edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<span style='color:red'>Pending at Engineer end</span>";
			}
			
			if($row->engineer_progress_status=='1' && $row->final_status=='0'){
			    $updatevisitprogress = "<a href='".page_url."Sales/engineer_progress_hod/".$row->id."'><span class='btn btn-success btn-xs'>Update Progress</span></a>";
			}else{
			    $updatevisitprogress="updated";
			}
			
			$hodsta = "<a href='".page_url."Sales/hodmarkasdone/".$row->id."' class='btn btn-success btn-xs'>MARK AS DONE</a>";
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$updatevisitprogress,
			'hod_sta'=>$hodsta,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function ftr_report(){
	$this->load->view('conveyance_voucher/ftr_report');
}

public function ftr_report_list()
	{
		$visit_data = array();
		//$user_id = $this->uri->segment(3);
	   $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		$this->db->where('a.visit_date=a.engineer_updatetime');
		$this->db->where('a.case_status','0');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else if($row->next_status){
			    $edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<span style='color:red'>Pending at Engineer end</span>";
			}
			
			if($row->engineer_progress_status=='1' && $row->final_status=='0'){
			    $updatevisitprogress = "<a href='".page_url."Sales/engineer_progress_hod/".$row->id."'><span class='btn btn-success btn-xs'>Update Progress</span></a>";
			}else{
			    $updatevisitprogress="updated";
			}
			
			$hodsta = "<a href='".page_url."Sales/hodmarkasdone/".$row->id."/".$row->service_case_id."' class='btn btn-success btn-xs'>MARK AS DONE</a>";
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$updatevisitprogress,
			'hod_sta'=>$hodsta,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}

public function ftr_filter(){
	$fromdate=date('Y-m-d',strtotime($this->input->post('frmdate')));
		$todate=date('Y-m-d',strtotime($this->input->post('todate')));
		$data = array('first_date'=>$fromdate,
		'last_date'=>$todate);
	$this->load->view('conveyance_voucher/ftr_filter_report',$data);
}

public function ftr_filter_reporting()
	{
		$fromdate=$this->uri->segment(3);
		$todate=$this->uri->segment(4);
		
		
		$visit_data = array();
		
	   $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit a')->join('system_users b','a.user_id=b.user_id','left')->join('system_users c','a.engineer=c.user_id','left');
		
		$this->db->where('a.case_status',0);
		$this->db->where('a.visit_date=a.engineer_updatetime');
		$this->db->where('a.visit_date BETWEEN "'. date('Y-m-d', strtotime($fromdate)). '" and "'. date('Y-m-d', strtotime($todate)).'"');
		//$this->db->where('a.engineer_progress_status','1');
		$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		$i=1;
		foreach($res as $row)
		{
		    $engineer = $row->efname." ".$row->e_lname;
			date_default_timezone_set("Asia/Kolkata");
			if($row->case_status){
				$case = "OPEN";
			}else{
				$case = "CLOSED";
			}
			
			if($row->service_report){
				$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
			}else{
				$attached_report="";
			}
			
			
			$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
			
			if($row->picture){
				$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
			}else{
				$picture="";
			}
			$spare_parts = $row->spares_parts;
			if($spare_parts=='1'){
			    $s_part = "Yes";
			}else{
			   $s_part = "No"; 
			}
			
			$casestatus = $row->case_status;
			if($casestatus=='1'){
				$edit="PROGRESS HAS BEEN UPDATED";
			}else if($row->next_status){
			    $edit="PROGRESS HAS BEEN UPDATED";
			}else{
				$edit = "<span style='color:red'>Pending at Engineer end</span>";
			}
			
			if($row->engineer_progress_status=='1' && $row->final_status=='0'){
			    $updatevisitprogress = "<a href='".page_url."Sales/engineer_progress_hod/".$row->id."'><span class='btn btn-success btn-xs'>Update Progress</span></a>";
			}else{
			    $updatevisitprogress="updated";
			}
			
			$hodsta = "<a href='".page_url."Sales/hodmarkasdone/".$row->id."' class='btn btn-success btn-xs'>MARK AS DONE</a>";
			
			$chargable = $row->chargable;
			
		    if($chargable=='0'){
		        $c = "No";
		    }else{
		        $c="Yes";
		    }
		    $payment_to_collect = $row->payment_to_collect;
			
		    if($payment_to_collect=='0'){
		        $p = "No";
		    }else{
		        $p="Yes";
		    }
			
			$warranty = $row->warrenty_status;
			if($warranty=='Warranty Expired'){
			    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
			}else{
			    $wa = strtoupper($warranty);
			}
			
			$visit_data[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'company_name'=>strtoupper($row->company_name),
			'contact_person'=>strtoupper($row->contact_person),
			'contact_number'=>strtoupper($row->contact_number),
			'sale_force_no'=>strtoupper($row->sale_force_no),
			'engineer'=>strtoupper($engineer),
			'warrenty_status'=>$wa,
			'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
			'service_report'=>$attached_report,
			'case_status'=>$case,
			'picture'=>$picture,
			'spare_parts'=>$s_part,
			'part_name'=>$row->part_name,
			'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
			'chargable'=>$c,
			'payment_to_collect'=>$p,
			'charges'=>$row->charges,
			'bill_number'=>$row->bill_number,
			'next_action'=>$row->next_status,
			'observation_of_engineer'=>$row->observation_of_engineer,
			'edit'=>$updatevisitprogress,
			'hod_sta'=>$hodsta,
			'addedon'=>$added_time);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($visit_data),
	"iTotalDisplayRecords" => count($visit_data),
	"aaData"=>$visit_data);
	echo json_encode($results);
}	


function checkifsf_synced_visit($id)
{
	$data=array();
	$restruru=$this->db->select('id,observation_of_engineer,caseid,service_visit_id,spares_parts,part_name,picture,service_report,case_status,action_taken,customer_remarks,caseid')->from('engineer_visit')->where('id',$id)->where('service_visit_id !=','')->get();
	if($restruru->num_rows()>0)
	{

		foreach($restruru->result() as $row);

$reportlinkpath=service_visit_report.$row->service_report;
$picturepath=service_visit_report.$row->service_report;
		$data['observation_of_engineer']=$row->observation_of_engineer;
		$data['caseid']=$row->caseid;
		$data['service_visit_id']=$row->service_visit_id;
		$data['spares_parts']=$row->spares_parts;
		$data['picture']=$picturepath;
		$data['part_name']=$row->part_name;
		$data['service_report']=$reportlinkpath;
		$data['case_status']=$row->case_status;
		$data['action_taken']=$row->action_taken;
		$data['customer_remarks']=$row->customer_remarks;

	}

	return $data;

}


function updatevisitinsalesforce($data)
{
	

$post = [
			'username' => 'gaurav@prestogroup.in',
			'password' => sfpass,
			'grant_type'   => 'password',
			'client_id'=>sfclientid,
			'client_secret'=>sfkey
			];



$cURLConnection = curl_init('https://login.salesforce.com/services/oauth2/token');
curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $post);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$apiResponse = curl_exec($cURLConnection);
curl_close($cURLConnection);
$resultssssssssssss=json_decode($apiResponse,true);
if(count($resultssssssssssss)>0)
{

		$acctoken=$resultssssssssssss['access_token'];	
		if($acctoken<>'')
		{

if($data['case_status']==0)
{
	$c="Closed";

}else
{
	$c="Open";

}

if($data['spares_parts']==0)
{
	$sp="No";

}else
{
$sp="Yes";

}

//echo "<pre>"; print_r($data);exit;

				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'https://presto.my.salesforce.com/services/data/v50.0/sobjects/Visit__c/'.$data['service_visit_id']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

				$datas=array("Scanned_Visit_report_link__c"=>$data['service_report'],"Visit_Result__c"=>$c,"Customer_Remark__c"=>$data['customer_remarks']);
				
				$esjson=stripcslashes(json_encode($datas,JSON_UNESCAPED_SLASHES));
				curl_setopt($ch, CURLOPT_POSTFIELDS,$esjson);
				$headers = array();
				$headers[] = 'Authorization: Bearer '.$acctoken;
				$headers[] = 'Content-Type: application/json';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);

				if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);

		}


}




}

public function take_localconveyance_print(){
	$this->load->view('conveyance_voucher/take_print_out');
}

function updatevisitinstrumentobservation($assetsid,$actualob,$actiontaken,$spare_parts,$picture,$parts_name)
{
	

    $post = [
            'username' => 'gaurav@prestogroup.in',
            'password' => sfpass,
            'grant_type'   => 'password',
            'client_id'=>sfclientid,
            'client_secret'=>sfkey
            ];



$cURLConnection = curl_init('https://login.salesforce.com/services/oauth2/token');
curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $post);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$apiResponse = curl_exec($cURLConnection);
curl_close($cURLConnection);
$resultssssssssssss=json_decode($apiResponse,true);
if(count($resultssssssssssss)>0)
{

		$acctoken=$resultssssssssssss['access_token'];

		if($acctoken<>'')
		{
			$pic=service_visit_report.$picture;

			$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'https://presto.my.salesforce.com/services/data/v50.0/sobjects/Asset_for_Case__c/'.$assetsid);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

				$datas=array("Asset_Actual_Observation__c"=>$actualob,"Asset_Action_Taken__c"=>$actiontaken,"Asset_Part_Name__c"=>$parts_name,"Asset_Spare_Part__c"=>$spare_parts,"Asset_Part_Picture__c"=>$pic);
				//echo "<pre>"; print_r($datas);exit;
				$esjson=stripcslashes(json_encode($datas,JSON_UNESCAPED_SLASHES));
				curl_setopt($ch, CURLOPT_POSTFIELDS,$esjson);
				$headers = array();
				$headers[] = 'Authorization: Bearer '.$acctoken;
				$headers[] = 'Content-Type: application/json';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);
				//echo $result; exit;
				if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);


		}



}



}
public function conveyance_form_dashboard(){
		$this->load->view('conveyance_voucher/form_dashboard');
	}
	
public function tour_take_printout(){
		$this->load->view('conveyance_voucher/tour_take_printout');
	}

	public function change_engineerid()
	{
		$leadid=$this->input->post('leadid1');
		$servicecaseid=$this->input->post('servicecaseid1');
		$engineerid=$this->input->post('actiontaken');

		 //echo $servicecaseid; exit;

		$data=array(
			'engineer'=>$engineerid
		);

		$this->db->where('id',$leadid);
		$this->db->update('engineer_visit',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Engineer successfully updated.</span></div><br/>');
				redirect(page_url.'Sales/engineer_visit_dashboard');
	}

	public function local_conveyance_filter_data()
	{
		$userid=$this->input->post('user');
		$startdate=$this->input->post('startdate');
		$enddate=$this->input->post('enddate');
		//$this->load->view('conveyance_voucher/local_conveyance_filter_data/'.$userid.'/'.$startdate.'/'.$enddate);
		redirect(page_url.'Sales/local_conveyance_filter_data_view/'.$userid.'/'.$startdate.'/'.$enddate);
	}
	public function local_conveyance_filter_data_view()
	{
		$this->load->view('conveyance_voucher/local_conveyance_filter_data');
	}

	public function conveyance_voucher_for_hod_filter_public_list()
	{
	     $user_id = $this->uri->segment(3);
	    $startdate=date('Y-m-d',strtotime($this->uri->segment(4)));
	    $enddate=date('Y-m-d',strtotime($this->uri->segment(5)));
		$scheduler_data = array();
		$query1 = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher_view a')->join('system_users_view b','a.added_by=b.user_id','left')->where('a.mode',1)->where('hod_status',1)->where('a.added_by',$user_id)->where('a.status','0')->where('travel_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->order_by('a.travel_date','DESC')->get();
		$res1 = $query1->result();
		$m=1;
		foreach($res1 as $row1)
		{
			
			if($row1->bills){
				$attached_bills = "<a href='".tourbills.$row1->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			if($row1->visit_report){
				$attached_visit_report = "<a href='".tourbills.$row1->visit_report."' download>Click Here to Download</a>";
			}else{
				$attached_visit_report="";
			}
			date_default_timezone_set("Asia/Kolkata");
			$html = "";
			$html1= "";

			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row1->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
				if($row1->mode==1){
				$md = "PUBLIC CONVEYANCE";
			/*Public conveyance calculator*/		
				
			$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR NO</th><th style='padding:2px 2px 2px 2px'>Date</th><th style='padding:2px 2px 2px 2px'>From</th><th style='padding:2px 2px 2px 2px'>Proceed To</th><th style='padding:2px 2px 2px 2px'>Mode</th><th style='padding:2px 2px 2px 2px'>EXPENSE</th><th style='padding:2px 2px 2px 2px'>AMOUNT</th></tr>";
			$instrumentsss = array();
			$i=1;
			$amount = array();
			$query = $this->db->select('a.amount, a.attachment, b.options,')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row1->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
				
				$amount[] = $record->amount;
				//$bills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				$bills = "<a href='".page_url.'Sales/viewbill/'.$row1->id."' target='_blank'><span class='btn btn-success btn-xs'>Print Consolidated Bill</span></a>";
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$i."</td>";
				
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row1->travel_date)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row1->from_location)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row1->proceed_to)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$md."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->amount)."</td>";
				// $html.="<td style='padding:2px 2px 2px 2px; text-align:center'></td>";
				// $html.="<td style='padding:2px 2px 2px 2px; text-align:center'></td>";
				// $html.="<td style='padding:2px 2px 2px 2px; text-align:center'></td>";
				$html.="</tr>";
				$i++;
				
			}
		$finaltotal  = array_sum($amount);
			
			
			$html.="<tr>
				<td colspan='6'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finaltotal."</strong><td>
			</tr></table>";
			}
				}
				
			/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row1->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }


			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row1->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$m,
			'user_name'=>strtoupper($row1->first_name." ".$row1->last_name),
			'travel_date'=>$row1->travel_date,
			'mode'=>$md,
			'visitdata'=>$html,
			'vehicletype'=>$vtype,
			'bills'=>$attached_bills,
			'takeprint'=>$takeprint,
			'attached_visit_report'=>$attached_visit_report,
			'edit'=>$edit);
			$m++;
		
		}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	}
	public function conveyance_voucher_for_hod_filter_own_list()
	{
	     $user_id = $this->uri->segment(3);
	    $startdate=date('Y-m-d',strtotime($this->uri->segment(4)));
	    $enddate=date('Y-m-d',strtotime($this->uri->segment(5)));
		$scheduler_data = array();
		$query1 = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher_view a')->join('system_users_view b','a.added_by=b.user_id','left')->where('a.mode',2)->where('hod_status',1)->where('a.added_by',$user_id)->where('a.status','0')->where('travel_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->order_by('a.travel_date','DESC')->get();
		$res1 = $query1->result();

		$m=1;
		foreach($res1 as $row1)
		{
			
			if($row1->bills){
				$attached_bills = "<a href='".tourbills.$row1->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			if($row1->visit_report){
				$attached_visit_report = "<a href='".tourbills.$row1->visit_report."' download>Click Here to Download</a>";
			}else{
				$attached_visit_report="";
			}
			date_default_timezone_set("Asia/Kolkata");
			$html = "";
			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row1->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
			$takeprint = "";

				$md = "OWN";

				$j=1;
				
				
				/*Own conveyance calculator*/
				if($row1->bills!=''){
				$parkingbills = "<a href='".tourbills.$row1->bills."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
					}else{
					$parkingbills = "Not Upload";	
					}
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			
			
			if($row1->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row1->amount+$row1->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row1->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row1->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row1->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row1->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
			$html.="</table>";
				

				/*Own conveyance calculator*/
				
				
								
			/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row1->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
                

			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row1->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$m,
			'user_name'=>strtoupper($row1->first_name." ".$row1->last_name),
			'travel_date'=>$row1->travel_date,
			'mode'=>$md,
			'visitdata'=>$html,
			'vehicletype'=>$vtype,
			'bills'=>$attached_bills,
			'takeprint'=>$takeprint,
			'attached_visit_report'=>$attached_visit_report,
			'edit'=>$edit);
			$m++;
		
		}
	
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	}
	public function take_localconveyance_public_print(){
	$this->load->view('conveyance_voucher/take_print_out_filter_public');
}
public function take_localconveyance_own_print(){
	$this->load->view('conveyance_voucher/take_print_out_filter_own');
}
public function viewbillpublictour()
{
	$this->load->view('store/view_tour_bill_public');
}

public function viewbillparkingbill()
{
	$this->load->view('store/view_tour_bill_own');
}
public function viewvisitreport()
{
	$this->load->view('store/view_visit_reported');
}



public function travel_conveyance_voucher_list_overall()
	{
		$CONVEYANCE_DATA = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->order_by('a.tour_start_date','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			$status = $row->status;
			if($status=='0'){
				$sta = "UNDER REVIEW";
			}else {
				$sta = "CLEARED";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			if($row->hod_status=='1'){
				$hod_status = "APPROVED";
			}else if($row->hod_status=='2'){
				$hod_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hod_remarks."</strong>";
			}else{
				$hod_status = "PENDING FOR REVIEW";
			}
			
			if($row->account_status=='1'){
				$account_status = "APPROVED";
			}else if($row->account_status=='2'){
				$account_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->account_remarks."</strong>";
			}else{
				$account_status = "PENDING FOR REVIEW";
			}
			
			if($row->hr_status=='1'){
				$hr_status = "APPROVED";
			}else if($row->hr_status=='2'){
				$hr_status="MARKED AS NOT APPROVED<br><strong style='color:red'>".$row->hr_remarks."</strong>";
			}else{
				$hr_status = "PENDING FOR REVIEW";
			}
			
			
			$livingamt= array();
			$travelamt = array();
			$foodexpenseamt =array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$foodexpenseamt[] =0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>DATE</th><th style='padding:2px 2px 2px 2px'>CITY</th> <th style='padding:2px 2px 2px 2px'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th style='padding:2px 2px 2px 2px'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th><th style='padding:2px 2px 2px 2px'>FOOD CONVEYANCE</th><th style='padding:2px 2px 2px 2px'>BILL EXPENSE</th><th style='padding:2px 2px 2px 2px'>LIVING BILL</th><th style='padding:2px 2px 2px 2px'>TRAVELLING BILL</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.conv_id,a.bill_attachment, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype, a.livingexpensebill, a.travellingbill')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){
				if($record->bill_attachment !='')
				{
				$bills = "<a href='".tourbills.$record->bill_attachment."' class='btn btn-success btn-xs' download>Download</a>";
				}else{
					$bills = "No Bill Upload";
				}
				if($record->bill_attachment !='')
				{
				$livingbill = "<a href='".tourbills.$record->livingexpensebill."' class='btn btn-success btn-xs' download>Download</a>";
				}else{
					$livingbill = "No Bill Upload";
				}
				if($record->bill_attachment !='')
				{
				$travellingbill = "<a href='".tourbills.$record->travellingbill."' class='btn btn-success btn-xs' download>Download</a>";
				}else{
					$travellingbill = "No Bill Upload";	
				}
				$viewbills="<a href='".page_url.'Sales/membertourbillview/'.$record->conv_id."' class='btn btn-success btn-xs' >View Bill</a>";
				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$foodexpenseamt[] = $record->foodexpense;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->foodexpense)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$livingbill."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$travellingbill."</td>";
				$html.="</tr>";
				
			}
		
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$foot_amount =  array_sum($foodexpenseamt);
			$grandtotal = $lv_amount+$trv_amount+$foot_amount;
			$html.="<tr>
				<td colspan='8'></td>
				<td colspan='2'>$viewbills</td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'status'=>$sta,
			'traveldata'=>$html,
			'hod_status'=>$hod_status,
			'account_status'=>$account_status);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}
 function sale_service_dashboard_overall(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_voucher_overall');
}

public function local_conveyance_data_history(){
$this->load->view('conveyance_voucher/local_conveyance_data_history');
}


public function conveyance_voucher_for_hod_list_history()
	{
	    if($this->uri->segment(3)){
	        $user_id = $this->uri->segment(3);
	    }{
	        $user_id =$this->session->userdata['logged_in']['user_id'];	
	    }
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		
		
		
		$scheduler_data = array();
		if($team<>'NA')
		{
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher_view a')->join('system_users_view b','a.added_by=b.user_id','left')->where_in('a.added_by',$team,false)->where('a.hod_status',1)->order_by('a.travel_date','DESC')->get();
		$res = $query->result();
		$m=1;
		foreach($res as $row)
		{
			
			if($row->bills){
				$attached_bills = "<a href='".tourbills.$row->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			
			
			date_default_timezone_set("Asia/Kolkata");
			$addedondate = date('Y-m-d',strtotime($row->added_on));
			$html = "";
			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				/*Public conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR NO</th><th style='padding:2px 2px 2px 2px'>EXPENSE</th><th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th >BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			$amount = array();
			$query = $this->db->select('a.amount, a.attachment, b.options,')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
				
				$amount[] = $record->amount;
				//$bills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				$bills = "<a href='".page_url.'Sales/viewbill/'.$row->id."' target='_blank'><span class='btn btn-success btn-xs'>Print Consolidated Bill</span></a>";
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$i."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'></td>";
				$html.="</tr>";
				$i++;
				
			}
		$finaltotal  = array_sum($amount);
			
			
			$html.="<tr>
				<td colspan='1'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finaltotal."</strong><td>
				<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>
			</tr></table>";
			}	

				/*Public conveyance calculator*/
				
				
				
				
				
			}else{
				$md = "OWN";

				$query = $this->db->select('a.amount, a.attachment, b.options,')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
			
				if($record->attachment !='')
				{
					$attactment=$record->attachment;
				}else
				{
					$attactment='Not Upload';
				}
				/*Own conveyance calculator*/
				$parkingbills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			
			if($row->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row->amount+$row->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
			$html.="</table>";
				

				/*Own conveyance calculator*/
				
				
			}
			}
				
			}
			
			$hodremarks = "";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/hod_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				 if($row->visit_report){
				$attached_visit_report = "<a href='".tourbills.$row->visit_report."' download>Click Here to Download</a>";
			}else{
				$attached_visit_report="";
			}
				
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$diff = abs(strtotime($row->travel_date) - strtotime($addedondate));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			if($days>7){
				$systemaddeddate = "<span style='color:red; font-weight:bold;'>".$addedondate."</span>";
			}else{
				$systemaddeddate = $addedondate;
			}
			$scheduler_data[] = array('sr_no'=>$m,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'visitdata'=>$html,
			'vehicletype'=>$vtype,
			'bills'=>$attached_bills,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'engineerremarks'=>$row->engineerremarks,
			'takeprint'=>$takeprint,
			'attached_visit_report'=>$attached_visit_report,
			'hodremarks'=>$pendingforapporaval."  ".$hodremarks,
			'edit'=>$edit,
			'addedondate'=>$systemaddeddate);
			$m++;
		}
		
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

public function approved_conveyance_dashboard(){
                                $this->load->view('conveyance_voucher/approved_conveyance_voucher');
                }


                public function approved_conveyance_voucher_list()
                {
                                $user_id =$this->session->userdata['logged_in']['user_id'];        
                                
                                $scheduler_data = array();
                                $query = $this->db->select('*')->from('conveyance_voucher')->where('added_by',$user_id)->where('account_status','0')->where('hod_status','1')->order_by('travel_date','desc')->get();
                                $res = $query->result();
                                $i=1;
                                foreach($res as $row)
                                {
                                                date_default_timezone_set("Asia/Kolkata");
                                                
                                                if($row->mode=='1'){
                                                                $md = "PUBLIC CONVEYANCE";
                                                }else{
                                                                $md = "OWN";
                                                }
                                                $hodremarks = "";
                                                $status = $row->account_status;
                                                if($status=='0'){
                                                                $sta = "UNDER REVIEW";
                                                }else{
                                                                $sta = "CLEARED";
                                                }
                                                
                                                $hod_status= $row->hod_status;
                                                $account_status= $row->account_status;
                                                $hr_status= $row->hr_status;
                                                if($hod_status=='0'){
                                                                $hodsta = "UNDER REVIEW";
                                                }else if($hod_status=='1'){
                                                                $hodsta = "APPROVED";
                                                }else{
                                                                $hodsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hod_remarks;;
                                                }
                                                if($hod_status=='0'){
                                                                $accountsta="";
                                                                $hrsta="";
                                                }else{
                                                $account_status= $row->account_status;
                                                if($account_status=='0'){
                                                                $accountsta = "UNDER REVIEW";
                                                }else if($hod_status=='1'){
                                                                $accountsta = "APPROVED";
                                                }else{
                                                                $accountsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->account_remarks;;
                                                }
                                                }
                                                if($account_status=='0' && $hod_status=='0'){
                                                                $hrsta="";
                                                                $accountsta="";
                                                                $hodsta="";
                                                }else{
                                                $hr_status= $row->hr_status;
                                                if($hod_status=='1'){
                                                                $hrsta = "APPROVED";
                                                }else{
                                                                $hrsta = "MARKED AS NOT APPROVED"."<br><br>Reason: ".$row->hr_remarks;;
                                                }
                                                }
                                                
                                                /** vehicle Type **/
                                                
                                                $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
                
                                                
                                                $edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
                                                $scheduler_data[] = array('sr_no'=>$i,
                                                'travel_date'=>strtoupper($row->travel_date),
                                                'from_location'=>strtoupper($row->from_location),
                                                'proceed_to'=>strtoupper($row->proceed_to),
                                                'mode'=>$md,
                                                'vehicletype'=>$vtype,
                                                'start_reading'=>$row->start_reading,
                                                'end_reading'=>$row->end_reading,
                                                'rate_per_km'=>$row->rate_per_km,
                                                'amount'=>$row->amount,
                                                'deduction'=>$row->deduction,
                                                'status'=>$sta,
                                                'hod_status'=>$hodsta,
                                                'account_status'=>$accountsta,
                                                'hr_status'=>$hrsta,
                                                'edit'=>$edit);
                                                $i++;
                                }
                $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($scheduler_data),
                "iTotalDisplayRecords" => count($scheduler_data),
                "aaData"=>$scheduler_data);
                echo json_encode($results);
}

function checkdays(){
	$selectedate = date('Y-m-d',strtotime($this->input->post('selectedate')));
	$today = date('Y-m-d');
$diff = abs(strtotime($selectedate) - strtotime($today));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			echo $days; exit;
}
function sale_service_conveyance_hod_dashboard_history(){
	$this->load->view('conveyance_voucher/sale_service_conveyance_hod_dashboard_history');
}

public function travel_conveyance_voucher_hod_list_history()
	{
		
		$CONVEYANCE_DATA = array();
		if($this->uri->segment(3)){
		    $user_id = $this->uri->segment(3);
		}else{
		   $user_id =$this->session->userdata['logged_in']['user_id'];	 
		}
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
		
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
	
	if($team<>'NA')
		{	
		$this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->where('a.hod_status','1');
		if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
	$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$hodremarks = "";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/sale_service_hod_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a><br><br>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			
			$livingamt= array();
			$travelamt = array();
			$foodamt=array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$foodamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th><th style='padding:2px 2px 2px 2px; text-align:center'>FOOD EXPENSE</th><th style='padding:2px 2px 2px 2px'>BILL EXPENSE</th><th style='padding:2px 2px 2px 2px'>LIVING BILL</th><th style='padding:2px 2px 2px 2px'>TRAVELLING BILL</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.livingexpensebill, a.travellingbill,a.conv_id,a.bill_attachment, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){

				$bills='BILL NOT UPLOADED';
				$livingbill='BILL NOT UPLOADED';
				$travellingbill='BILL NOT UPLOADED';
				if($record->bill_attachment<>'')
				{
				$bills = "<a href='".tourbills.$record->bill_attachment."' class='btn btn-success btn-xs' download>Download</a>";
				}
				if($record->livingexpensebill<>'')
				{
				$livingbill = "<a href='".tourbills.$record->livingexpensebill."' class='btn btn-success btn-xs' download>Download</a>";
				}

				if($record->travellingbill<>'')
				{
				$travellingbill = "<a href='".tourbills.$record->travellingbill."' class='btn btn-success btn-xs' download>Download</a>";
				}

				$viewbills="<a href='".page_url.'Sales/membertourbillview/'.$record->conv_id."' class='btn btn-success btn-xs' target='_blank' >View Bill</a>";

				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$foodamt[] = $record->foodexpense;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->foodexpense)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills ."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$livingbill."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$travellingbill."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$food_amount =  array_sum($foodamt);
			$grandtotal = $lv_amount+$trv_amount+$food_amount;
			$html.="<tr>
				<td colspan='8'></td>
				<td colspan='2' style='padding:2px 2px 2px 2px; text-align:center'>$viewbills</td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			$takeprintout = "<a href='".page_url."Sales/tour_take_printout/".$row->id."'><i class='fa fa-print' style='font-size:24px;'></i></a>";
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>$pendingforapporaval." ".$hodremarks,
			'traveldata'=>$html,
			'engineerremarks'=>$row->engineerremarks,
			'takeprintout'=>$takeprintout );
			$i++;
		}
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

	public function rejeted_local_conveyance_dashboard(){
		$this->load->view('conveyance_voucher/rejected_local_conveyance_voucher');
	}

	
	public function rejected_conveyance_voucher_list()
	{
	    if($this->uri->segment(3)){
	        $user_id = $this->uri->segment(3);
	    }{
	        $user_id =$this->session->userdata['logged_in']['user_id'];	
	    }
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
	
		//$team = implode(',',$teammembers);
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
		
		
		
		$scheduler_data = array();
		if($team<>'NA')
		{
		$query = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher_view a')->join('system_users_view b','a.added_by=b.user_id','left')->where_in('a.added_by',$team,false)->where('a.hod_status',2)->order_by('a.travel_date','DESC')->get();
		$res = $query->result();
		$m=1;
		foreach($res as $row)
		{
			
			if($row->bills){
				$attached_bills = "<a href='".tourbills.$row->bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			
			
			date_default_timezone_set("Asia/Kolkata");
			$addedondate = date('Y-m-d',strtotime($row->added_on));
			$html = "";
			$takeprint = "<a href='".page_url."/Sales/take_localconveyance_print/".$row->id."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i> Take Print</span></a>";
			if($row->mode=='1'){
				$md = "PUBLIC CONVEYANCE";
				/*Public conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR NO</th><th style='padding:2px 2px 2px 2px'>EXPENSE</th><th style='padding:2px 2px 2px 2px'>AMOUNT</th> <th >BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			$amount = array();
			$query = $this->db->select('a.amount, a.attachment, b.options,')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
				
				$amount[] = $record->amount;
				//$bills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				$bills = "<a href='".page_url.'Sales/viewbill/'.$row->id."' target='_blank'><span class='btn btn-success btn-xs'>Print Consolidated Bill</span></a>";
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$i."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'></td>";
				$html.="</tr>";
				$i++;
				
			}
		$finaltotal  = array_sum($amount);
			
			
			$html.="<tr>
				<td colspan='1'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finaltotal."</strong><td>
				<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills."</td>
			</tr></table>";
			}	

				/*Public conveyance calculator*/
				
				
				
				
				
			}else{
				$md = "OWN";

				$query = $this->db->select('a.amount, a.attachment, b.options,')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
			if($query->num_rows()>0){
			foreach($query->result() as $record){
			
				if($record->attachment !='')
				{
					$attactment=$record->attachment;
				}else
				{
					$attactment='Not Upload';
				}
				/*Own conveyance calculator*/
				$parkingbills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			
			if($row->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row->amount+$row->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
			$html.="</table>";
				

				/*Own conveyance calculator*/
				
				
			}
			}
				
			}
			
			$hodremarks = "";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/hod_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
				/** vehicle Type **/
			
			 $vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				 if($row->visit_report){
				$attached_visit_report = "<a href='".tourbills.$row->visit_report."' download>Click Here to Download</a>";
			}else{
				$attached_visit_report="";
			}
				
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$diff = abs(strtotime($row->travel_date) - strtotime($addedondate));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			if($days>7){
				$systemaddeddate = "<span style='color:red; font-weight:bold;'>".$addedondate."</span>";
			}else{
				$systemaddeddate = $addedondate;
			}
			$scheduler_data[] = array('sr_no'=>$m,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>strtoupper($row->travel_date),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'visitdata'=>$html,
			'vehicletype'=>$vtype,
			'bills'=>$attached_bills,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount,
			'engineerremarks'=>$row->engineerremarks,
			'takeprint'=>$takeprint,
			'attached_visit_report'=>$attached_visit_report,
			'hodremarks'=>$pendingforapporaval."  ".$hodremarks,
			'edit'=>$edit,
			'addedondate'=>$systemaddeddate);
			$m++;
		}
		
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function rejeted_tour_conveyance_dashboard(){
		$this->load->view('conveyance_voucher/rejected_tour_conveynce_data');
	}

	
	public function rejected_tour_conveyance_voucher_list()
	{
		
		$CONVEYANCE_DATA = array();
		if($this->uri->segment(3)){
		    $user_id = $this->uri->segment(3);
		}else{
		   $user_id =$this->session->userdata['logged_in']['user_id'];	 
		}
		
		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $teamdetail);
		 $teammembers = array();
		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
        $res = $query->result();
         foreach($res as $teaminfo){
            $teammembers[] = $teaminfo->employee_id;
            
        }
		
		$team = "'" . implode ( "', '", $teammembers ) . "'";
		}else
		{
		    $team='NA';
		}
	
	if($team<>'NA')
		{	
		$this->db->select('a.*, b.first_name, b.last_name')->from('member_conveyance_information_view a')->join('system_users_view b','a.employee_id=b.user_id','left')->where('a.hod_status','2');
		if($q->num_rows()>0){
		$this->db->where_in('a.employee_id',$team, false);
		}
	$query = 	$this->db->order_by('a.id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			
			if($row->purpose_of_trip=='1'){
				$purpose = "SALE";
			}else{
				$purpose = "SERVICE";
			}
			
			if($row->tour_bills){
				$attached_bills = "<a href='".tourbills.$row->tour_bills."' download>Click Here to Download</a>";
			}else{
				$attached_bills="";
			}
			$hodremarks = "";
			$status = $row->hod_status;
			if($status=='0'){
				$pendingforapporaval= "<a href='".page_url."/Sales/sale_service_hod_mark_as_approved/".$row->id."'><span class='btn btn-success btn-xs'>MARK AS APPROVED</span></a><br><br>";
				$hodremarks.= "<span class='btn btn-danger btn-xs' data-toggle='modal' data-target='#hod_not_approved".$row->id."'>MARK AS NOT APPROVED</span><br><br>";
			}else if($status=='2'){
				$pendingforapporaval = "NOT APPROVED.</br><br><strong style='color:red; font-weight:bold;'>".$row->hod_remarks."</strong><br><br>";
				
			}else{
				$pendingforapporaval= "APPROVED";
				
				
			}
			
			
			$hodremarks.= '<div id="hod_not_approved'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Sales/sale_service_hod_marked_as_not_approved/'.$row->id.'">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Please Specify the reason?</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">REMARK</label><br>
														<span id="error_business_loc" style="color:red;"></span>
														<textarea class="form-control" style="width:500px" name="remarks" required></textarea>
												    </div>
                                                </div>
                                            </div>
											
											
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
			
			
			$livingamt= array();
			$travelamt = array();
			$foodamt = array();
			$livingamt[] = 0;
			$travelamt[] = 0;
			$foodamt[] = 0;
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>DATE</th><th style='padding:2px 2px 2px 2px; text-align:center'>CITY</th> <th style='padding:2px 2px 2px 2px; text-align:center'>COMPANY NAME</th> <th style='padding:2px 2px 2px 2px; text-align:center'>LIVING EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th> <th style='padding:2px 2px 2px 2px; text-align:center'>TRAVEL EXPENSE</th> <th style='padding:2px 2px 2px 2px; text-align:center'>AMOUNT</th><th style='padding:2px 2px 2px 2px; text-align:center'>FOOD EXPENSE</th><th style='padding:2px 2px 2px 2px'>BILL EXPENSE</th><th style='padding:2px 2px 2px 2px'>LIVING BILL</th><th style='padding:2px 2px 2px 2px'>TRAVELLING BILL</th></tr>";
			$instrumentsss = array();
			$query = $this->db->select('a.livingexpensebill, a.travellingbill,a.conv_id,a.bill_attachment, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$row->id)->get();
			foreach($query->result() as $record){

				$bills='BILL NOT UPLOADED';
				$livingbill='BILL NOT UPLOADED';
				$travellingbill='BILL NOT UPLOADED';
				if($record->bill_attachment<>'')
				{
				$bills = "<a href='".tourbills.$record->bill_attachment."' class='btn btn-success btn-xs' download>Download</a>";
				}
				if($record->livingexpensebill<>'')
				{
				$livingbill = "<a href='".tourbills.$record->livingexpensebill."' class='btn btn-success btn-xs' download>Download</a>";
				}

				if($record->travellingbill<>'')
				{
				$travellingbill = "<a href='".tourbills.$record->travellingbill."' class='btn btn-success btn-xs' download>Download</a>";
				}

				$viewbills="<a href='".page_url.'Sales/membertourbillview/'.$record->conv_id."' class='btn btn-success btn-xs' target='_blank' >View Bill</a>";

				$livingamt[] = $record->living_exp_amount;
				$travelamt[] = $record->travel_exp_amount;
				$foodamt[] = $record->foodexpense;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".date('d-M-Y',strtotime($record->tour_date))."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->city)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->company_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->options)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->living_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->expensetype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->travel_exp_amount)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->foodexpense)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$bills ."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$livingbill."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$travellingbill."</td>";
				$html.="</tr>";
				
			}
			$lv_amount = array_sum($livingamt);
			$trv_amount =  array_sum($travelamt);
			$food_amount =  array_sum($foodamt);
			$grandtotal = $lv_amount+$trv_amount+$food_amount;
			$html.="<tr>
				<td colspan='8'></td>
				<td colspan='2' style='padding:2px 2px 2px 2px; text-align:center'>$viewbills</td>
				<td> <strong style='color:red;'>TOTAL INR-".$grandtotal."</strong><td>
			</tr></table>";
			$takeprintout = "<a href='".page_url."Sales/tour_take_printout/".$row->id."'><i class='fa fa-print' style='font-size:24px;'></i></a>";
			$CONVEYANCE_DATA[] = array('sr_no'=>$i,
			'name'=>strtoupper($row->first_name." ".$row->last_name),
			'purpose'=>strtoupper($purpose),
			'start_date'=>date('d-M-Y',strtotime($row->tour_start_date)),
			'end_date'=>date('d-M-Y',strtotime($row->tour_end_date)),
			'departure_time'=>date('H:i:a',strtotime($row->departure_time)),
			'arrival_time'=>date('H:i:a',strtotime($row->arrival_time)),
			'total_days'=>$row->total_days,
			'attached_bills'=>$attached_bills,
			'hod_status'=>$pendingforapporaval." ".$hodremarks,
			'traveldata'=>$html,
			'engineerremarks'=>$row->engineerremarks,
			'takeprintout'=>$takeprintout );
			$i++;
		}
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($CONVEYANCE_DATA),
	"iTotalDisplayRecords" => count($CONVEYANCE_DATA),
	"aaData"=>$CONVEYANCE_DATA);
	echo json_encode($results);
}

	public function engineer_visit_feedback_dashboard(){
	$this->load->view('conveyance_voucher/visit_dashboard_feeback');
	}

		public function visit_review_feedback_list()
		{
			$visit_data = array();
			$date = date('Y-m-d');
			$d2 = date('Y-m-d', strtotime('-60 days'));
			$seconddate = $d2." 00-00-00";
			$user_id = $this->uri->segment(3);
			$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit_view a')->join('system_users_view b','a.user_id=b.user_id','left')->join('system_users_view c','a.engineer=c.user_id','left')->where('a.visit_date>=',$d2);
			if($user_id){
			    $this->db->where('a.engineer',$user_id);
			}
			$this->db->where('a.final_status',1);
			$this->db->where('a.feedbackform',0);
			$query = 	$this->db->order_by('a.id','desc')->get();
			$res = $query->result();
			//echo "<pre>"; print_r($res); exit;
			$i=1;
			foreach($res as $row)
			{
			    $engineer = $row->efname." ".$row->e_lname;
				date_default_timezone_set("Asia/Kolkata");
				if($row->case_status){
					$case = "OPEN";
				}else{
					$case = "CLOSED";
				}
				
				if($row->service_report){
					$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
				}else{
					$attached_report="";
				}
				
				
				$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
				
				if($row->picture){
					$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
				}else{
					$picture="";
				}
				$spare_parts = $row->spares_parts;
				if($spare_parts=='1'){
				    $s_part = "Yes";
				}else{
				   $s_part = "No"; 
				}
				
				$casestatus = $row->case_status;
				if($casestatus=='1'){
					$edit="PROGRESS HAS BEEN UPDATED";
					$updatevisitprogress = "Updated";
				}else if($row->next_status){
				    $edit="PROGRESS HAS BEEN UPDATED";
				    $updatevisitprogress = "Updated";
				}else{
					$edit = "<span style='color:red'>Pending at Engineer end</span>";
					$updatevisitprogress = "Pending at Engineer end";
				}
				
				
				
				
				$hodsta = "<a href='".page_url."Sales/feedback/".$row->id."/".$row->service_case_id."' class='btn btn-success btn-xs'>FEEDBACK</a>";
				
				//$hodsta = "<span class='btn btn-success btn-xs' onclick='showmodal(".$row->id.",".$row->service_case_id.")'>FEEDBACK</span>";
				if($this->session->userdata['logged_in']['user_id']==5){
				$changeengieer = "<span class='btn btn-success btn-xs' onclick='showmodalchangeeng(".$row->id.",".$row->service_case_id.")'>Change Engineer</span>";
				}else{
					$changeengieer = "";
				}
				$chargable = $row->chargable;
				
			    if($chargable=='0'){
			        $c = "No";
			    }else{
			        $c="Yes";
			    }
			    $payment_to_collect = $row->payment_to_collect;
				
			    if($payment_to_collect=='0'){
			        $p = "No";
			    }else{
			        $p="Yes";
			    }
				
				$warranty = $row->warrenty_status;
				if($warranty=='Warranty Expired'){
				    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
				}else{
				    $wa = strtoupper($warranty);
				}
				
				$q= $this->db->select('sales_or_service')->from('leads_by_service_team')->where('visit_id',$row->id)->get();
				if($q->num_rows()>0){
					foreach($q->result() as $rows);
					$lead = $rows->sales_or_service;
				}else{
					$lead = "";
				}

				/** create table **/
				$html='';
				$resteure=$this->db->select('*')->from('visit_schedule_instruments')->where('visitid',$row->id)->get();
				if($resteure->num_rows()>0)
				{

					$html.='<table class="table table-bordered">
					<thead>
					<tr>
					<th>Sno.</th>
					<th>Instrument</th>
					<th>Actual Observation</th>
					<th>Action Taken</th>
					<th>Spare</th>
					</tr>
					</thead>
					<tbody>';

					$k=1;
					foreach($resteure->result() as $rowsss)
					{
						$as='';
						if($rowsss->sparepart==1)
						{
							$as.="YES<br/>";

							$as.=$rowsss->partname."<br/>";

							$as.="<a href='".service_visit_report.$rowsss->picture."' download>Click to download</a>";
						}else
						{
							$as="NO";
						}
					$html.='<tr>
					<td>'.$k.'</td>
					<td>'.$rowsss->instrumentname.'</td>
					<td>'.$rowsss->actual_observation.'</td>
					<td>'.$rowsss->action_taken.'</td>
					<td>'.$as.'</td>
					</tr>';
					$k++;
					}


					$html.='</tbody>
					</table>';
				}

				/** end **/
				
				$visit_data[] = array('sr_no'=>$i.'<br>'.$changeengieer,
				'name'=>strtoupper($row->first_name." ".$row->last_name),
				'company_name'=>strtoupper($row->company_name),
				'contact_person'=>strtoupper($row->contact_person),
				'contact_number'=>strtoupper($row->contact_number),
				'sale_force_no'=>strtoupper($row->sale_force_no)."<br/>".$row->caserefno,
				'engineer'=>strtoupper($engineer),
				'warrenty_status'=>$wa,
				'lead'=>$lead,
				'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
				'service_report'=>$attached_report,
				'case_status'=>$case,
				'picture'=>$picture,
				'spare_parts'=>$s_part,
				'part_name'=>$row->part_name,
				'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
				'chargable'=>$c,
				'payment_to_collect'=>$p,
				'charges'=>$row->charges,
				'bill_number'=>$row->bill_number,
				'next_action'=>$row->next_status,
				'observation_of_engineer'=>$row->observation_of_engineer,
				'edit'=>$updatevisitprogress,
				'hod_sta'=>$hodsta,
				'addedon'=>$added_time,
				'instrumentdetail'=>$html);
				$i++;
			}
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($visit_data),
		"iTotalDisplayRecords" => count($visit_data),
		"aaData"=>$visit_data);
		echo json_encode($results);
		}


		public function feedback(){
		$this->load->view('conveyance_voucher/feedback');
		}

		public function visitedfeedback()
		{
			$user_id=$_SESSION['logged_in']['user_id'];
			$visitedid=$this->uri->segment(3);
			$behave=$this->input->post('engineerbehave');
			$productperformance=$this->input->post('productperformance');
			$coordination=$this->input->post('coordination');
			$chkproduct=$this->input->post('chkproduct');
			if($chkproduct==1){
			$recommendproduct=$this->input->post('recommendproduct');
			}else
			{
			$recommendproduct="";	
			}
			$otherremarks=$this->input->post('otherremarks');

			$data=array(
				'visited_id'=>$visitedid,
				'engineer_behave'=>$behave,
				'product_performance'=>$productperformance,
				'backend_team_cordination'=>$coordination,
				'product_recommend'=>$chkproduct,
				'product_recommend_remark'=>$recommendproduct,
				'other_remarks'=>$otherremarks,
				'added_on'=>date('Y-m-d h:i:s'),
				'added_by'=>$user_id
			);
			$this->db->insert('visited_feedback_form',$data);
			$data1=array(
				'feedbackform'=>1
			);
			$this->db->where('id',$visitedid);
			$this->db->update('engineer_visit',$data1);

			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Sales/engineer_visit_feedback_dashboard');
		}

		public function visit_review_feedback_list_history()
		{
			$visit_data = array();
			$date = date('Y-m-d');
			$d2 = date('Y-m-d', strtotime('-60 days'));
			$seconddate = $d2." 00-00-00";
			$user_id = $this->uri->segment(3);
			$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit_view a')->join('system_users_view b','a.user_id=b.user_id','left')->join('system_users_view c','a.engineer=c.user_id','left')->where('a.visit_date>=',$d2);
			if($user_id){
			    $this->db->where('a.engineer',$user_id);
			}
			$this->db->where('a.final_status',1);
			$this->db->where('a.feedbackform',1);
			$query = 	$this->db->order_by('a.id','desc')->get();
			$res = $query->result();
			//echo "<pre>"; print_r($res); exit;
			$i=1;
			foreach($res as $row)
			{
			    $engineer = $row->efname." ".$row->e_lname;
				date_default_timezone_set("Asia/Kolkata");
				if($row->case_status){
					$case = "OPEN";
				}else{
					$case = "CLOSED";
				}
				
				if($row->service_report){
					$attached_report = "<a href='".service_visit_report.$row->service_report."' download>Click Here to Download</a>";
				}else{
					$attached_report="";
				}
				
				
				$added_time = date('Y-m-d h:i A',strtotime($row->added_on));
				
				if($row->picture){
					$picture = "<a href='".service_visit_report.$row->picture."' download>Click Here to Picture</a>";
				}else{
					$picture="";
				}
				$spare_parts = $row->spares_parts;
				if($spare_parts=='1'){
				    $s_part = "Yes";
				}else{
				   $s_part = "No"; 
				}
				
				$casestatus = $row->case_status;
				if($casestatus=='1'){
					$edit="PROGRESS HAS BEEN UPDATED";
					$updatevisitprogress = "Updated";
				}else if($row->next_status){
				    $edit="PROGRESS HAS BEEN UPDATED";
				    $updatevisitprogress = "Updated";
				}else{
					$edit = "<span style='color:red'>Pending at Engineer end</span>";
					$updatevisitprogress = "Pending at Engineer end";
				}
				
				
				
				
				$hodsta = "<a href='".page_url."Sales/feedbackview/".$row->id."/".$row->service_case_id."' class='btn btn-success btn-xs'>FEEDBACK VIEW</a>";
				
				//$hodsta = "<span class='btn btn-success btn-xs' onclick='showmodal(".$row->id.",".$row->service_case_id.")'>FEEDBACK</span>";
				if($this->session->userdata['logged_in']['user_id']==5){
				$changeengieer = "<span class='btn btn-success btn-xs' onclick='showmodalchangeeng(".$row->id.",".$row->service_case_id.")'>Change Engineer</span>";
				}else{
					$changeengieer = "";
				}
				$chargable = $row->chargable;
				
			    if($chargable=='0'){
			        $c = "No";
			    }else{
			        $c="Yes";
			    }
			    $payment_to_collect = $row->payment_to_collect;
				
			    if($payment_to_collect=='0'){
			        $p = "No";
			    }else{
			        $p="Yes";
			    }
				
				$warranty = $row->warrenty_status;
				if($warranty=='Warranty Expired'){
				    $wa  = "<span style='color:red; font-weight:bold;'>".strtoupper($warranty)."</span>";
				}else{
				    $wa = strtoupper($warranty);
				}
				
				$q= $this->db->select('sales_or_service')->from('leads_by_service_team')->where('visit_id',$row->id)->get();
				if($q->num_rows()>0){
					foreach($q->result() as $rows);
					$lead = $rows->sales_or_service;
				}else{
					$lead = "";
				}

				/** create table **/
				$html='';
				$resteure=$this->db->select('*')->from('visit_schedule_instruments')->where('visitid',$row->id)->get();
				if($resteure->num_rows()>0)
				{

					$html.='<table class="table table-bordered">
					<thead>
					<tr>
					<th>Sno.</th>
					<th>Instrument</th>
					<th>Actual Observation</th>
					<th>Action Taken</th>
					<th>Spare</th>
					</tr>
					</thead>
					<tbody>';

					$k=1;
					foreach($resteure->result() as $rowsss)
					{
						$as='';
						if($rowsss->sparepart==1)
						{
							$as.="YES<br/>";

							$as.=$rowsss->partname."<br/>";

							$as.="<a href='".service_visit_report.$rowsss->picture."' download>Click to download</a>";
						}else
						{
							$as="NO";
						}
					$html.='<tr>
					<td>'.$k.'</td>
					<td>'.$rowsss->instrumentname.'</td>
					<td>'.$rowsss->actual_observation.'</td>
					<td>'.$rowsss->action_taken.'</td>
					<td>'.$as.'</td>
					</tr>';
					$k++;
					}


					$html.='</tbody>
					</table>';
				}

				/** end **/
				
				$visit_data[] = array('sr_no'=>$i.'<br>'.$changeengieer,
				'name'=>strtoupper($row->first_name." ".$row->last_name),
				'company_name'=>strtoupper($row->company_name),
				'contact_person'=>strtoupper($row->contact_person),
				'contact_number'=>strtoupper($row->contact_number),
				'sale_force_no'=>strtoupper($row->sale_force_no)."<br/>".$row->caserefno,
				'engineer'=>strtoupper($engineer),
				'warrenty_status'=>$wa,
				'lead'=>$lead,
				'nature_of_complaints'=>strtoupper($row->nature_of_complaints),
				'service_report'=>$attached_report,
				'case_status'=>$case,
				'picture'=>$picture,
				'spare_parts'=>$s_part,
				'part_name'=>$row->part_name,
				'visit_date'=>date('d-m-Y',strtotime($row->visit_date)),
				'chargable'=>$c,
				'payment_to_collect'=>$p,
				'charges'=>$row->charges,
				'bill_number'=>$row->bill_number,
				'next_action'=>$row->next_status,
				'observation_of_engineer'=>$row->observation_of_engineer,
				'edit'=>$updatevisitprogress,
				'hod_sta'=>$hodsta,
				'addedon'=>$added_time,
				'instrumentdetail'=>$html);
				$i++;
			}
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($visit_data),
		"iTotalDisplayRecords" => count($visit_data),
		"aaData"=>$visit_data);
		echo json_encode($results);
		}

		public function feedbackview(){
		$this->load->view('conveyance_voucher/feedback_view');
		}

	public function conveyance_data_for_accounts(){
		$this->load->view('conveyance_voucher/conveyance_data_for_accounts');
	}


public function conveyance_data_for_accounts_list()
	{
		$account_data=array();
		$vtype='';
		if($this->uri->segment(3) != ''){
	        $user_id = $this->uri->segment(3);
	    } else{
	        $user_id = '';	
	    }

			 $this->db->select('a.*, b.first_name, b.last_name')
					  ->from('conveyance_voucher_view a')
					  ->join('system_users_view b','a.added_by=b.user_id','left');
			if($user_id != '') {
			 $this->db->where('a.added_by', $user_id);
			}

			if($this->uri->segment(4) != '') {
			 $this->db->where('a.travel_date >=', date('Y-m-d', strtotime($this->uri->segment(4))));
			}


			if($this->uri->segment(5) != '') {
			 $this->db->where('a.travel_date <=',  date('Y-m-d', strtotime($this->uri->segment(5))));
			}
			
			$query = $this->db->order_by('a.travel_date','ASC')
	    					  ->where('a.hod_status','1')
	    					  ->where('a.account_status','1')
	    					  ->get();

		$m=1;
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				if($row->bills){
					$attached_bills = "<a href='".tourbills.$row->bills."' download>Click Here to Download</a>";
				}else{
					$attached_bills="";
				}
				
				if($row->visit_report){
					$visit_report = "<a href='".tourbills.$row->visit_report."' download>Click Here to Download</a>";
				}else{
					$visit_report="";
				}
			date_default_timezone_set("Asia/Kolkata");
			$html = "";

				$md = "OWN";
				$parkingbills = "";
				$query = $this->db->select('a.amount, a.attachment, b.options, a.to_location, a.from_location')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();
				if($query->num_rows() >0){
			foreach($query->result() as $record){
				$parkingbills = "<a href='".tourbills.$record->attachment."' download><span class='btn btn-success btn-xs'>Download Bill</span></a>";
			}
					}
				/*Own conveyance calculator*/
				
				$html.= "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>VEHICLE TYPE</th><th style='padding:2px 2px 2px 2px'>START READING</th> <th style='padding:2px 2px 2px 2px'>END READING</th><th style='padding:2px 2px 2px 2px'>PARKING CHARGES</th><th style='padding:2px 2px 2px 2px'>TOTAL AMOUNT</th><th style='padding:2px 2px 2px 2px'>PARKING BILL</th></tr>";
			$instrumentsss = array();
			$i=1;
			
			if($row->vehicletype=='1'){
				$vehicletype = "CAR";
			}else{
				$vehicletype = "BIKE";
			}
			$totalamount = $row->amount+$row->parking_charges;
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($vehicletype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->start_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->end_reading)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($row->parking_charges)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$row->amount."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".$parkingbills."</td>";
				$html.="</tr>";
				
			
			
				$html.="</table>";
				
				$finaltotal = $totalamount;
				/*Own conveyance calculator*/
				
				$vtypes=$this->db->select('type')->from('conveyance_vehicle_rate')->where('id',$row->vehicletype)->get();
                if($vtypes->num_rows()>0)
                {
                foreach($vtypes->result() as $vtypes1);
                $vtype=$vtypes1->type;
                }else
                {
                    $vtype='';
                }
				
		
	    	$m = 1;
			$account_data[] = array(
			'sr_no'=>$m,
			'user_name'=>strtoupper($row->first_name." ".$row->last_name),
			'travel_date'=>date('d-m-Y',strtotime($row->travel_date)),
			'from_location'=>strtoupper($row->from_location),
			'proceed_to'=>strtoupper($row->proceed_to),
			'mode'=>$md,
			'visitdata'=>$html,
			'bills'=>$attached_bills,
			'visit_report'=>$visit_report,
			'vehicletype'=>$vtype,
			'start_reading'=>$row->start_reading,
			'end_reading'=>$row->end_reading,
			'rate_per_km'=>$row->rate_per_km,
			'amount'=>$row->amount
		);
			$m++;
		}
	}

		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($account_data),
		"iTotalDisplayRecords" => count($account_data),
		"aaData"=>$account_data);
		echo json_encode($results);
	}

	function filter_conveyance_report() {
		$user_id = $this->input->post('user_id');
		$startdate = date('Y-m-d', strtotime($this->input->post('startdate')));
		$enddate = date('Y-m-d', strtotime($this->input->post('enddate')));

		redirect(page_url.'Sales/conveyance_data_for_accounts/'.$user_id.'/'.$startdate.'/'.$enddate);
	}
		
	function oneclickapproval(){
		 $table = "conveyance_voucher";
		$id = $this->input->post('selectall');
		if(isset($id)){
		for($i=0;$i<count($id);$i++){
		$taskid=$id[$i];
		$data = array(
						'hod_status'=>'1',
						'status'=>'0',
						'account_status'=>'1');
			$this->db->where('id',$taskid);
			$res = $this->db->update($table,$data);
			
		}
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Records marked as approved.</span></div><br/>');
		redirect(page_url.'Sales/local_conveyance_data/');
		
	}else{
		$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Please select the records to proceed.</span></div><br/>');
		redirect(page_url.'Sales/local_conveyance_data/');
	}
	}

	public function conveyance_voucher_new(){
		$this->load->view('conveyance_voucher/local_conveyance_form_new');
	}


	public function conveyance_voucher_add_new(){
	
		$date=date('Y-m-d',strtotime($this->input->post('date')));
		$start_read=$this->input->post('start_read');
		$end_read=$this->input->post('end_read');
		$convence_rate=$this->input->post('convence_rate');

		$data=array('convence_date'=>$date,'user_id'=>$_SESSION['logged_in']['user_id'],'start_read'=>$start_read,'end_read'=>$end_read,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'rate'=>$convence_rate);
		$this->db->insert('employee_convence',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Record Added</span></div><br/>');
		
		redirect(page_url.'Sales/conveyance_voucher_new');



	}

	function user_convence_list()
	{
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	

		$query11 = $this->db->select('min(a.convence_date) as lastunapproved')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.user_id',$user_id)->where('send_for_approval',0)->order_by('a.convence_date','ASC')->get();
		if($query11->num_rows()>0)
		{
			foreach($query11->result() as $dd);
			if($dd<>'')
			{
				$start_date=date('Y-m-01',strtotime($dd->lastunapproved));
				$end_date=date('Y-m-t',strtotime($dd->lastunapproved));
				
			}else
			{
				$start_date=date('Y-m-01');
				$end_date=date('Y-m-t');
			}
		}else
		{
				$start_date=date('Y-m-01');
				$end_date=date('Y-m-t');	
		}	

		$scheduler_data = array();
		$query = $this->db->select('a.*')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('a.user_id',$user_id)->where('send_for_approval',0)->order_by('a.convence_date','desc')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;
			
			if($row->send_for_approval==0)
			{
				$snd="NO";
			}else
			{	
				$snd="YES";

			}

			if($row->approved==0)
			{
				$snd1="NO";
			}else
			{	
				$snd1="YES";

			}
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher_new/".$row->id."'><i class='fa fa-pencil'></i></a>";

			$selectall="<input type='checkbox' class='data_row' name='select_row[]' id='select_row[]' value='".$row->id."'>";

			$scheduler_data[] = array('sr_no'=>$i."<br/>".$selectall,
  			'date'=>date('d-M-Y',strtotime($row->convence_date)),
  			'start_read'=>$row->start_read,
  			'end_read'=>$row->end_read,
  			'net_km'=>"<strong style='color:green;font-weight:bold;font-size:17px;'>".$diff."</strong>",
  			'per_km_rate'=>"<strong style='color:orange;font-weight:bold;font-size:17px;'>".$row->rate."</strong>",
  			'amount'=>"<strong style='color:green;font-weight:bold;font-size:17px;'>".$amt."</strong>",
  			'sent_for_approval'=>$snd,
  			'approval_status'=>$snd1,
        'edit'=> $edit
      );
			$i++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}

	function send_for_approval()
	{

		$select_row=$this->input->post('select_row');
		$convence_type= $this->input->post('conveyancetype');
		//echo $convence_type; exit;
		$select_row=$this->input->post('select_row');
		
		if($convence_type==2)
		{
			$petrol=$this->input->post('petrol');
		}else
		{
			$petrol=0;
		}

		
		$additional_part=$this->input->post('additional_part');
		$additional_charge=$this->input->post('additional_charge');

		
		if(count($select_row)>0)
		{


			for($i=0;$i<count($select_row);$i++)
			{
				$rowid=$select_row[$i];

				$data=array('send_for_approval'=>1,
					'send_for_approval_On'=>date('Y-m-d'),
					'petrol_used'=>$petrol,
					'misc_particular'=>$additional_part,
					'misc_charges'=>$additional_charge);
				$this->db->where('id',$rowid);
				$this->db->update('employee_convence',$data);
			}
		}


		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Send ForApproval</span></div><br/>');
		
		redirect(page_url.'Sales/conveyance_voucher_new');
	}

	public function local_conveyance_dashboard_new(){
		$this->load->view('conveyance_voucher/admin_approval');
	}

	function approval_for_admin()
	{

		$flag=$this->uri->segment(3);
	
		$scheduler_data = array();

for ($u = 0; $u <= 6; $u++) 
{
		$start_date = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$u months"));
		$end_date=date('Y-m-t',strtotime($start_date));

		
		$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->get();
		if($q->num_rows()>0)
		{
			foreach($q->result() as $q1)
			{
		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->where('a.user_id',$q1->user_id)->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
			
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data($q1->user_id,$start_date,$end_date);
			//echo "<pre>"; print_r($readings); exit;

			$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;

			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					if($i==0)
					{
					$misc_charge[]=$readings['misc_charges'][$i];
					}
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
			}





			$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				$puse='';
				$total_amount=array_sum($total_am)+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}

				if($puse<>0)
				{
				$average=$diff/$puse;
				}else
				{
					$average=0;
				}
			}


			if($flag<>'')
			{
				$flag=1;
			}else
			{
				$flag=0;
			}
		
			$edit='<a href="'.page_url.'Sales/approve_data/'.$start_date.'/'.$end_date.'/'.$total_amount.'/'.$q1->user_id.'/'.$row->convence_type.'/'.$start_read.'/'.$end_read.'/'.$diff.'/'.$mis_charge.'/'.$puse.'/'.$average.'/'.$flag.'" class="btn btn-sm btn-warning">Approve</a>';
			
			

			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$q1->first_name." ".$q1->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'bifurcation'=>'<a href="'.page_url.'Sales/view_data_bifurcation/'.date('Y-m-d',strtotime($start_date)).'/'.date('Y-m-d',strtotime($end_date)).'/'.$q1->user_id.'">View</a>',
			'status'=>$edit,
			'view'=>"<a href='".page_url."Sales/view_data_bifurcation/".$start_date."/".$end_date."/".$q1->user_id."/1'>View Bifurcation</a>"
			);

			$i++;
		}
	}
	}
	}
}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}

	function get_readings_data($user_id,$start_date,$end_date)
	{

		$reading_data = array();
		$query = $this->db->select('a.*')->from('employee_convence a')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('user_id',$user_id)->where('send_for_approval',1)->where('approved',0)->order_by('a.convence_date','ASC')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			$reading_data['start'][]=$row->start_read;
			$reading_data['end'][]=$row->end_read;
			$reading_data['rate'][]=$row->rate;
			$reading_data['petrol'][]=$row->petrol_used;
			$reading_data['misc_charges'][]=$row->misc_charges;
			$reading_data['misc_particular'][]=$row->misc_particular;
			$reading_data['total_amount'][]=($row->end_read-$row->start_read)*$row->rate;
			$reading_data['total_distance'][]=$row->end_read-$row->start_read;
		}

		}

		return $reading_data; 

	}



	function approve_data()
	{
		$this->load->view('conveyance_voucher/approve_data');
	}
	function approve_convence_data()
	{

		$user_id=$this->input->post('user_id');
		$sdate=date('Y-m-d',strtotime($this->input->post('sdate')));
		$edate=date('Y-m-d',strtotime($this->input->post('edate')));
		$app_amount=$this->input->post('app_amount');
		$flag=$this->input->post('flag');


		$data=array('approved'=>1,'approved_On'=>date('Y-m-d H:i:s'),'approved_By'=>$_SESSION['logged_in']['user_id'],'approved_amt'=>$app_amount);
		$this->db->where('convence_date>=',$sdate);
		$this->db->where('convence_date<=',$edate);
		$this->db->where('send_for_approval',1);
		$this->db->where('user_id',$user_id);
		$this->db->update('employee_convence',$data);


		if($flag==0)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Conveyance Approved</span></div><br/>');
				redirect(page_url.'Sales/local_conveyance_dashboard_new');
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Conveyance Approved</span></div><br/>');
				redirect(page_url.'Leads/common_approval');
		}



	}


	function approval_for_admin_history()
	{

				
		$scheduler_data = array();

		for ($l = -6; $l <= 0; $l++){

			$m1=date('Y-m', strtotime("$l month"));

			$start_date=date($m1.'-01');
			$end_date=date($m1.'-t');

			
			$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->get();
		if($q->num_rows()>0)
		{
			foreach($q->result() as $q1)
			{

			$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',1)->where('a.user_id',$q1->user_id)->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data_approved($row->user_id,$start_date,$end_date);

			$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;
			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					$misc_charge[]=$readings['misc_charges'][$i];
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$approved_amt=$readings['approved_amt'][0];
				$approved_On=date('d-M-Y',strtotime($readings['approved_On'][0]));
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
				$perkm_rate=0;
				$approved_On='';
			}

			$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				$puse='';
				
				$total_amount=array_sum($total_am)+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}
				if($puse>0)
				{
				$average=$diff/$puse;
				}else
				{
					$average=0;
				}
			}


		
			$edit='Approved On-';
			
			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$row->first_name." ".$row->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'approved_amt'=>"<strong style='color:green;font-weight:bold;font-size:22px;'>".$approved_amt."</strong>",
			'status'=>$approved_On,
			'view'=>"<a href='".page_url."Sales/view_data_bifurcation/".$start_date."/".$end_date."/".$row->user_id."/1'>View Bifurcation</a>"
			);

			$i++;
		}
	}
}
	}
}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}


	function get_readings_data_approved($user_id,$start_date,$end_date)
	{

		$reading_data = array();
		$query = $this->db->select('a.*')->from('employee_convence a')->where('user_id',$user_id)->where('send_for_approval',1)->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('approved',1)->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			$reading_data['start'][]=$row->start_read;
			$reading_data['end'][]=$row->end_read;
			$reading_data['rate'][]=$row->rate;
			$reading_data['petrol'][]=$row->petrol_used;
			$reading_data['misc_charges'][]=$row->misc_charges;
			$reading_data['misc_particular'][]=$row->misc_particular;
			$reading_data['approved_amt'][]=$row->approved_amt;
			$reading_data['approved_On'][]=$row->approved_On;
			$reading_data['approved_By'][]=$row->approved_By;
			$reading_data['total_amount'][]=($row->end_read-$row->start_read)*$row->rate;
			$reading_data['total_distance'][]=$row->end_read-$row->start_read;
		}

		}

		return $reading_data; 

	}


	function user_wise_approval()
	{
		$this->load->view('conveyance_voucher/user_wise_convence_approval');
	}


	function approval_for_admin_history_user_wise()
	{

		$user_id=$_SESSION['logged_in']['user_id'];
		$scheduler_data = array();
		$r=1;
	for ($u = 0; $u <= 6; $u++) 
{
		$start_date = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$u months"));
		$end_date=date('Y-m-t',strtotime($start_date));
		
		$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_id',$_SESSION['logged_in']['user_id'])->where('convence',1)->where('user_status',1)->get();
		if($q->num_rows()>0)
		{
			foreach($q->result() as $q1)
			{
				
				// if($u==2)
				// {
				// 	echo $end_date; exit;
				// }
		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type,approved_amt')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',1)->where('a.user_id',$user_id)->order_by('a.convence_date','DESC')->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data_approved($row->user_id,$start_date,$end_date);

				$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;
			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					if($i==0)
					{
					$misc_charge[]=$readings['misc_charges'][$i];
					}
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$approved_amt=$readings['approved_amt'][0];
				$approved_On=date('d-M-Y',strtotime($readings['approved_On'][0]));
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
				$perkm_rate=0;
				$approved_On='';
			}


			$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				
				$puse='';
				$total_amount=array_sum($total_am)+$mis_charge;
				//$total_amount=$total_amount+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}
				$average=$diff/$puse;
			}


			$diff=array_sum($total_dis);
			$edit='Approved On-';
			
			$scheduler_data[] = array('sr_no'=>$r,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$row->first_name." ".$row->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'approved_amt'=>"<strong style='color:green;font-weight:bold;font-size:22px;'>".$approved_amt."</strong>",
			'status'=>$approved_On,
			'view'=>"<a href='".page_url."Sales/view_data_bifurcation/".$start_date."/".$end_date."/".$_SESSION['logged_in']['user_id']."/1'>View Bifurcation</a>");

			$i++;
			$r++;
		}
	}
}
	}

}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}

	function accounts_convence_dashboard()
	{
		$this->load->view('conveyance_voucher/account_convence_data');
	}





		function approval_for_admin_history_account()
	{

		$user_id=$this->uri->segment(3);

		$scheduler_data = array();
		for ($u = 0; $u <= 6; $u++) 
{
		$start_date = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$u months"));
		$end_date=date('Y-m-t',strtotime($start_date));

			

$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->get();
		if($q->num_rows()>0)
		{
			foreach($q->result() as $q1)
			{
		 $this->db->select('a.*,b.first_name,b.last_name,b.convence_type,approved_amt')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',1)->where('paid',0)->where('a.user_id',$q1->user_id);

		 if($user_id<>'ALL' && $user_id<>'')
		 {
		 	$this->db->where('a.user_id',$user_id);
		 }

		$query=$this->db->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data_approved($row->user_id,$start_date,$end_date);

			$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;
			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					$misc_charge[]=$readings['misc_charges'][$i];
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$approved_amt=$readings['approved_amt'][0];
				$approved_On=date('d-M-Y',strtotime($readings['approved_On'][0]));
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
				$perkm_rate=0;
				$approved_On='';
			}

			$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				$puse='';
				$total_amount=array_sum($total_am)+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}
				if($puse>0)
				{
				$average=$diff/$puse;
				}else
				{
					$average=0;
				}
			}


		
			$edit='Approved On-';
			
			$action="<a href='".page_url."Sales/markaspaid/".$start_date."/".$end_date."' class='btn btn-warning'>Mark as Paid</a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$row->first_name." ".$row->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'approved_amt'=>"<strong style='color:green;font-weight:bold;font-size:22px;'>".$approved_amt."</strong>",
			'status'=>$approved_On,
			'action'=>$action);

			$i++;
		}
	}
}
	}
}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}

	function filter_accounts_report()
	{
		$user_id=$this->input->post('user_id');

		redirect(page_url.'Sales/accounts_convence_dashboard/'.$user_id);
	}

	function markaspaid()
	{
		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		//echo $start_date."<br/>".$end_date; exit;
		$updata=array('paid'=>1,'paidOn'=>date('Y-m-d H:i:s'));
		$this->db->where('convence_date>=',$start_date);
		$this->db->where('convence_date<=',$end_date);
		$this->db->update('employee_convence',$updata);

		  /** END **/
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Marked as Paid.</span></div><br/>');
				redirect(page_url.'Sales/accounts_convence_dashboard');
	}



	function accounts_convence_dashboard_history()
	{
		$this->load->view('conveyance_voucher/account_convence_data_paid');
	}



	function approval_for_admin_history_account_paid()
	{

		$user_id=$this->uri->segment(3);

		$scheduler_data = array();
		for ($u = 0; $u <= 6; $u++) 
{
		$start_date = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$u months"));
		$end_date=date('Y-m-t',strtotime($start_date));

			

			$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->get();
		if($q->num_rows()>0)
		{
			foreach($q->result() as $q1)
			{
		 $this->db->select('a.*,b.first_name,b.last_name,b.convence_type,approved_amt')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',1)->where('paid',1)->where('a.user_id',$q1->user_id);

		 if($user_id<>'ALL' && $user_id<>'')
		 {
		 	$this->db->where('a.user_id',$user_id);
		 }

		$query=$this->db->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data_approved($row->user_id,$start_date,$end_date);

				$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;
			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					$misc_charge[]=$readings['misc_charges'][$i];
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$approved_amt=$readings['approved_amt'][0];
				$approved_On=date('d-M-Y',strtotime($readings['approved_On'][0]));
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
				$perkm_rate=0;
				$approved_On='';
			}

		$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				$puse='';
				$total_amount=array_sum($total_am)+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}

				if($puse>0)
				{
				$average=$diff/$puse;
				}else
				{
					$average=0;
				}
			}
		
			$edit='Approved On-';
		
			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$row->first_name." ".$row->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'approved_amt'=>"<strong style='color:green;font-weight:bold;font-size:22px;'>".$approved_amt."</strong>",
			'status'=>$approved_On,
			'view'=>"<a href='".page_url."Sales/view_data_bifurcation/".$start_date."/".$end_date."/".$row->user_id."/1'>View Bifurcation</a>",
			'action'=>"Paid On-".date('d-M-Y',strtotime($row->paidOn)));

			$i++;
		}
	}
}
	}
}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}


	function approval_for_userOldd()
	{

		$flag=$this->uri->segment(3);
		$scheduler_data = array();


		for ($l = -3; $l <= 0; $l++){

			$m1=date('Y-m', strtotime("$l month"));

			$start_date=date($m1.'-01');
			$end_date=date($m1.'-t');

			

		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->where('a.user_id',$_SESSION['logged_in']['user_id'])->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data($row->user_id,$start_date,$end_date);

			$misc_charge=array();
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;
			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					if($i==0)
					{
					$misc_charge[]=$readings['misc_charges'][$i];
					}

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
			}

			$diff=$end_read-$start_read;
			if($row->convence_type==1)
			{
				$puse='';
				$total_amount=$diff*$perkm_rate;
				$total_amount=$total_amount+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}
				$average=$diff/$puse;
			}


			if($flag<>'')
			{
				$flag=1;
			}else
			{
				$flag=0;
			}
		
			$edit='<a href="'.page_url.'Sales/approve_data/'.$start_date.'/'.$end_date.'/'.$total_amount.'/'.$row->user_id.'/'.$row->convence_type.'/'.$start_read.'/'.$end_read.'/'.$diff.'/'.$mis_charge.'/'.$puse.'/'.$average.'/'.$flag.'" class="btn btn-sm btn-warning">Approve</a>';
			
			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$row->first_name." ".$row->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'status'=>$edit);

			$i++;
		}
	}
}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}

	function approval_for_user()
	{
		$flag=$this->uri->segment(3);
		$scheduler_data = array();


		for ($u = 0; $u <= 6; $u++) 
{
		$start_date = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$u months"));
		$end_date=date('Y-m-t',strtotime($start_date));



			
		$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->where('user_id',$_SESSION['logged_in']['user_id'])->get();
		if($q->num_rows()>0)
		{
			foreach($q->result() as $q1)
			{
		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->where('a.user_id',$q1->user_id)->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
			
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data($q1->user_id,$start_date,$end_date);
			//echo "<pre>"; print_r($readings); exit;

			$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;

			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					if($i==0)
					{
					$misc_charge[]=$readings['misc_charges'][$i];
					}
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
			}





			$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				$puse='';
				$total_amount=array_sum($total_am)+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}

				if($puse<>0)
				{
				$average=$diff/$puse;
				}else
				{
					$average=0;
				}
			}


			if($flag<>'')
			{
				$flag=1;
			}else
			{
				$flag=0;
			}
		
			$edit='<a href="'.page_url.'Sales/approve_data/'.$start_date.'/'.$end_date.'/'.$total_amount.'/'.$q1->user_id.'/'.$row->convence_type.'/'.$start_read.'/'.$end_read.'/'.$diff.'/'.$mis_charge.'/'.$puse.'/'.$average.'/'.$flag.'" class="btn btn-sm btn-warning">Approve</a>';
			
			

			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$q1->first_name." ".$q1->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'view'=>"<a href='".page_url."Sales/view_data_bifurcation/".$start_date."/".$end_date."/".$q1->user_id."/1'>View Bifurcation</a>",
			'status'=>$edit);

			$i++;
		}
	}
	}
	}
}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	}

	function user_wise_approval_pending()
	{
		$this->load->view('conveyance_voucher/user_wise_convence_approval_pending');
	}


	function user_convence_list_pending()
	{
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	

		$scheduler_data = array();
		$query = $this->db->select('a.*')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.user_id',$user_id)->where('send_for_approval',1)->where('approved',0)->order_by('a.convence_date','desc')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;
			
			if($row->send_for_approval==0)
			{
				$snd="NO";
			}else
			{	
				$snd="YES";

			}

			if($row->approved==0)
			{
				$snd1="Pending";
			}else
			{	
				$snd1="Approved";

			}
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";

			$start_date=date('Y-m-01',strtotime($row->convence_date));
			$end_date=date('Y-m-t',strtotime($row->convence_date));
			

			$scheduler_data[] = array('sr_no'=>$i,
			'date'=>date('d-M-Y',strtotime($row->convence_date)),
			'start_read'=>$row->start_read,
			'end_read'=>$row->end_read,
			'net_km'=>"<strong style='color:green;font-weight:bold;font-size:17px;'>".$diff."</strong>",
			'per_km_rate'=>"<strong style='color:orange;font-weight:bold;font-size:17px;'>".$row->rate."</strong>",
			'amount'=>"<strong style='color:green;font-weight:bold;font-size:17px;'>".$amt."</strong>",
			'sent_for_approval'=>$snd,
			'view'=>"<a href='".page_url."Sales/view_data_bifurcation/".$start_date."/".$end_date."/".$row->user_id."/1'>View Bifurcation</a>",

			'approval_status'=>$snd1);
			$i++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}

  function edit_conveyance_voucher_new(){
    $this->load->view('conveyance_voucher/edit_conveyance_voucher_new');
  }

 function update_conveyance_voucher_new(){
  $date=date('Y-m-d',strtotime($this->input->post('date')));
  $start_read=$this->input->post('start_read');
  $end_read=$this->input->post('end_read');
  $id=$this->input->post('conveyance_voucher_id');

  $data=array('convence_date'=>$date,
    'start_read'=>$start_read,
    'end_read'=>$end_read,
  );
        $this->db->where('id',$id);
        $this->db->update('employee_convence',$data);

        redirect(page_url.'Sales/conveyance_voucher_new');
 }


function cal_days_in_year($year){
    $days=0; 
    for($month=1;$month<=12;$month++){ 
        $days = $days + cal_days_in_month(CAL_GREGORIAN,$month,$year);
     }
 return $days;
}

function view_data_bifurcation()
{
	$this->load->view('conveyance_voucher/conveyance_data_bifurcation');
}


function user_convence_list_bifurcation()
	{
		$scheduler_data = array();
		$start_date =date('Y-m-d',strtotime($this->uri->segment(3)));	
		$end_date =date('Y-m-d',strtotime($this->uri->segment(4)));	
		$user_id =$this->uri->segment(5);	
		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('a.user_id',$user_id)->where('send_for_approval',1)->order_by('a.convence_date','desc')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			if($query->num_rows()==$i)
			{
				$misparti=$row->misc_particular;
				if($row->convence_type==1)
				{
				$misamt=$row->misc_charges;
				}else
				{
					$misamt=$row->petrol_used." LTR";
				}
			}else
			{
				$misparti='';
				$misamt='';
			}
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;
			
			if($row->send_for_approval==0)
			{
				$snd="NO";
			}else
			{	
				$snd="YES";

			}

			if($row->approved==0)
			{
				$snd1="NO";
			}else
			{	
				$snd1="YES";

			}

			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher_admin/".$row->id."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."'><i class='fa fa-pencil'></i></a>";

			$selectall="<input type='checkbox' class='data_row' name='select_row[]' id='select_row[]' value='".$row->id."'>";

			$scheduler_data[] = array('sr_no'=>$i."<br/>",
  			'date'=>date('d-M-Y',strtotime($row->convence_date)),
  			'user_name'=>$row->first_name." ".$row->last_name,
  			'start_read'=>$row->start_read,
  			'end_read'=>$row->end_read,
  			'net_km'=>"<strong style='color:green;font-weight:bold;font-size:17px;'>".$diff."</strong>",
  			'per_km_rate'=>"<strong style='color:orange;font-weight:bold;font-size:17px;'>".$row->rate."</strong>",
  			'amount'=>"<strong style='color:green;font-weight:bold;font-size:17px;'>".$amt."</strong>",
  			'sent_for_approval'=>$snd,
  			'approval_status'=>$snd1,
  			'misc_charges'=>"<strong style='color:red;font-size:17px;'>".$misparti."<br/><br/>".$misamt."</strong>",
        	'edit'=> $edit
      );
			$i++;
		}
	}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}


  function edit_conveyance_voucher_admin(){
    $this->load->view('conveyance_voucher/edit_convence_voucher_admin');
  }

  function update_conveyance_voucher_new_admin(){
  $date=date('Y-m-d',strtotime($this->input->post('date')));
  $start_read=$this->input->post('start_read');
  $end_read=$this->input->post('end_read');
  $id=$this->input->post('conveyance_voucher_id');

  $data=array('convence_date'=>$date,
    'start_read'=>$start_read,
    'end_read'=>$end_read,
  );
        $this->db->where('id',$id);
        $this->db->update('employee_convence',$data);


        $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
        redirect(page_url.'Sales/view_data_bifurcation/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
 }

 function update_misc_data()
 {
 	$start_date=$this->uri->segment(3);
 	$end_date=$this->uri->segment(4);
 	$user_id=$this->uri->segment(5);

 	$miscp=$this->input->post('miscp');
 	$mischarge=$this->input->post('mischarge');
 	$petrol=$this->input->post('petrol');
 	$rate=$this->input->post('rate');

 	$data=array('rate'=>$rate,'misc_particular'=>$miscp,'misc_charges'=>$mischarge,'petrol_used'=>$petrol);
 	$this->db->where('convence_date>=',date('Y-m-d',strtotime($start_date)))
 			 ->where('convence_date<=',date('Y-m-d',strtotime($end_date)))
 			 ->where('user_id',$user_id)
 			 ->update('employee_convence',$data);

	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
	redirect(page_url.'Sales/view_data_bifurcation/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5));


 }

 function backtrackdata()
 {
 	$start_date=date('Y-m-d',strtotime($this->uri->segment(3)));
 	$end_date=date('Y-m-d',strtotime($this->uri->segment(4)));
 	$user_id=$this->uri->segment(5);

 		$data=array('send_for_approval'=>0);
 	$this->db->where('convence_date>=',date('Y-m-d',strtotime($start_date)))
 			 ->where('convence_date<=',date('Y-m-d',strtotime($end_date)))
 			 ->where('user_id',$user_id)
 			 ->update('employee_convence',$data);

 			 $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
	redirect(page_url.'Sales/view_data_bifurcation/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5));
 }

 function filterconveyancereportbyusers()
	{
		$this->load->view('conveyance_voucher/userwiseoverallconveyancereport');
	}

function filterconveyancereportbyusers_report()
	{

		
		$scheduler_data = array();
		$r=1;

		$startdate = $this->uri->segment(3);
		$enddate = $this->uri->segment(4);
		$userid = $this->uri->segment(5);
		
		
				
		$this->db->select('a.*,b.first_name,b.last_name,b.convence_type,approved_amt')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$startdate)->where('a.convence_date<=',$enddate);
		if($userid<>'ALL'){
		$this->db->where('a.user_id',$userid);
		}
		$query = $this->db->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data_approved($row->user_id,$startdate,$enddate);
			echo "<pre>"; print_r($readings); exit;

			$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;
			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					if($i==0)
					{
					$misc_charge[]=$readings['misc_charges'][$i];
					}
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$approved_amt=$readings['approved_amt'][0];
				$approved_On=date('d-M-Y',strtotime($readings['approved_On'][0]));
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
				$perkm_rate=0;
				$approved_On='';
			}


			$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				
				$puse='';
				$total_amount=array_sum($total_am)+$mis_charge;
				//$total_amount=$total_amount+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}
				$average=$diff/$puse;
			}


			$diff=array_sum($total_dis);
			$edit='Approved On-';
			
			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($startdate))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$row->first_name." ".$row->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'approved_amt'=>"<strong style='color:green;font-weight:bold;font-size:22px;'>".$approved_amt."</strong>",
			'status'=>$approved_On,
			'view'=>"<a href='".page_url."Sales/view_data_bifurcation/".$startdate."/".$enddate."/".$_SESSION['logged_in']['user_id']."/1'>View Bifurcation</a>");

			$i++;
		}
	}

$r++;

	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}


	function conveyancecommonreporting()
	{

		$flag=$this->uri->segment(3);
		$scheduler_data = array();


		$start_date = $this->uri->segment(4);
		$end_date=$this->uri->segment(5);
		$userid=$this->uri->segment(6);

		
		$this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0);
		if($userid<>'ALL'){
		$this->db->where('a.user_id',$userid);
		}
		$query = $this->db->group_by('a.user_id')->get();
		
		
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{
			
			
			$diff=$row->end_read-$row->start_read;

			$amt=$diff*$row->rate;

			if($row->convence_type==1)
			{
				$type="Km Based";
			}else
			{
				$type="Petrol Based";
			}
			
			

			
			$edit = "<a href='".page_url."Sales/edit_conveyance_voucher/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$readings=$this->get_readings_data($userid,$start_date,$end_date);
			
			$misc_charge=array();
			$misc_charge[]=0;
			$total_am=array();
			$total_am[]=0;
			$total_dis=array();
			$total_dis[]=0;
			 //echo "<pre>"; print_r($readings); exit;
			$c=count($readings['start']);

			$c=count($readings['start'])-1;

			if(count($readings['start'])>0)
			{
				for($i=0;$i<count($readings['start']);$i++)
				{

					$start=$readings['start'][$i];
					if($i==0)
					{
					$misc_charge[]=$readings['misc_charges'][$i];
					}
					$total_am[]=$readings['total_amount'][$i];
					$total_dis[]=$readings['total_distance'][$i];

				}

				$start_read=$readings['start'][0];
				$perkm_rate=$readings['rate'][0];
				$end_read=$readings['end'][$c];
				$misc_particular=$readings['misc_particular'][$c];

				$mis_charge=array_sum($misc_charge);
				

			}else
			{
				$start_read=0;
				$end_read=0;
				$misc_particular='';
				$misc_charges='';
				$mis_charge=0;
			}





			$diff=array_sum($total_dis);
			if($row->convence_type==1)
			{
				$puse='';
				$total_amount=array_sum($total_am)+$mis_charge;
				$average='';
			}else
			{
				$puse=$readings['petrol'][0];
				if($mis_charge>0)
				{
				$total_amount=0;
				}else
				{
				$total_amount=$mis_charge;
				}

				if($puse<>0)
				{
				$average=$diff/$puse;
				}else
				{
					$average=0;
				}
			}


			if($flag<>'')
			{
				$flag=1;
			}else
			{
				$flag=0;
			}
		
			$edit='<a href="'.page_url.'Sales/approve_data/'.$start_date.'/'.$end_date.'/'.$total_amount.'/'.$userid.'/'.$row->convence_type.'/'.$start_read.'/'.$end_read.'/'.$diff.'/'.$mis_charge.'/'.$puse.'/'.$average.'/'.$flag.'" class="btn btn-sm btn-warning">Approve</a>';
			
			

			$scheduler_data[] = array('sr_no'=>$i,
			'period'=>date('d-M-Y',strtotime($start_date))."-".date('d-M-Y',strtotime($end_date)),
			'user'=>$row->first_name." ".$row->last_name,
			'type'=>$type,
			'start_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$start_read."</strong>",
			'end_reading'=>"<strong style='font-weight:bold;font-size:15px;'>".$end_read."</strong>",
			'per_km_rate'=>$perkm_rate,
			'net_km'=>"<strong style='font-weight:bold;font-size:15px;'>".$diff."</strong>",
			'petrol_used'=>$puse,
			'misc_charges'=>$mis_charge,
			'total_amount'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$total_amount."</strong>",
			'average'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$average."</strong>",
			'bifurcation'=>'<a href="'.page_url.'Sales/view_data_bifurcation/'.date('Y-m-d',strtotime($start_date)).'/'.date('Y-m-d',strtotime($end_date)).'/'.$userid.'">View</a>',
			'status'=>$edit);

			$i++;
		}


	

	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);

	}
}