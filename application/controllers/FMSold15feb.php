<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(dirname(__FILE__)."/Orderstage.php");
class FMS extends Orderstage {
	
	public function __construct()
	{
		parent::__construct();
			$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		
		$this->load->library('../controllers/Orderstage');
		
	}
	
	public function fms_flow(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('production_flow', 'production_flow', 'required|trim');
	$this->form_validation->set_rules('flowname', 'flowname', 'required|trim');
	$this->form_validation->set_rules('who', 'who', 'required|trim');
	$this->form_validation->set_rules('what', 'what', 'required|trim');
	$this->form_validation->set_rules('how', 'how', 'required|trim');
	$this->form_validation->set_rules('when', 'when', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/fms_flow');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
			$moveto = $this->input->post('moveto');
			if($moveto<>''){
		   $movetostep = implode(',',$moveto);
			}else{
				$movetostep="";
			}
			
			 $final = $this->input->post('final');
			    if($final=='1'){
			   $fstep = "1";
		   }else{
			   $fstep = "0";
		   }
			if($fstep=='1')
			{
			 $querycheckd = $this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$this->input->post('production_flow'))->where('finalstep','1')->get();
		   $rescheck = $querycheckd->num_rows();
		   if($rescheck<>0){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Same flow cannot have more than 1 final stage</span><br/>');
				redirect(page_url.'FMS/fms_flow');
			   
		   }
			
		}
			
		   $query = $this->db->select('fms_flow,production_flow_id')->from('fms_flow')->where('fms_flow',$this->input->post('flowname'))->where('production_flow_id',$this->input->post('production_flow'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'FMS/fms_flow');
			   
		   }else{
			   
			   $dependent = $this->input->post('dependent');
		   if($dependent=='1'){
			   $depval = "1";
		   }else{
			   $depval = "0";
		   }
			  
			   
			  
			   
		   
		   $data=
			array('fms_flow'=>strtoupper($this->input->post('flowname')),
			'production_flow_id'=>strtoupper($this->input->post('production_flow')),
			'who_wedo'=>strtoupper($this->input->post('who')),
			'what_wedo'=>strtoupper($this->input->post('what')),
			'how_wedo'=>strtoupper($this->input->post('how')),
			'total_days'=>strtoupper($this->input->post('days')),
			'set_time'=>strtoupper($this->input->post('time')),
			'when_wedo'=>strtoupper($this->input->post('when')),
			'video_link'=>strtoupper($this->input->post('video_link')),
			'setorder'=>strtoupper($this->input->post('set_order')),
			'moveto'=>$movetostep,
			'dependency'=>$depval,
			'finalstep'=>$fstep,
			'uitype'=>$this->input->post('response_type'),
			'actiontobetaken'=>$this->input->post('actiontobetaken'),
			'status'=>strtoupper($this->input->post('status')),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('fms_flow',$data);
			   $last_id = $this->db->insert_id();
			if($res)
			{
				$dependent = $this->input->post('dependent');
				if($dependent=='1'){
					if(isset($_REQUEST['dependentto'])){	
					$tags1=count($_REQUEST['dependentto']);
					if($tags1>0)
					{
					$dependentto=$_REQUEST['dependentto'];
					
					
					for($x=0;$x<$tags1;$x++){
					if($dependentto[$x]!='')
						{
							$data=array('dependentflowid'=>$dependentto[$x],
							'flowid'=>$last_id);
							$this->db->insert('flowdependency',$data);
							
						   
						}
					}
					}
					}
					
					
				}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/fms_flow');
				}
		   }
			
			}
}
public function update_fms_flow_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "flow_id";
		$table = "fms_flow";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect(page_url.'FMS/fms_flow');
		}
	public function edit_fms_flow()
	{
	$this->load->view('FMS/edit_fmsflow');
		
	}
public function fms_flow_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.*, b.user_id, b.title, b.first_name, b.last_name, c.id, c.production_flow, c.sortorder')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id','left')->join('production_flow c','a.production_flow_id=c.id','left')->order_by('a.setorder','ASC')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."FMS/update_fms_flow_status/".$row->flow_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."FMS/update_fms_flow_status/".$row->flow_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			$hour = $row->set_time;
			if($hour>0){
			$hours = "and ".$hour." Hour";
			}else{
				$hours="";
			}
			
			$edit = "<a href='".page_url."FMS/edit_fms_flow/".$row->flow_id."'><i class='fa fa-pencil'></i></a>";
			$movetofms = "";
			$movetodata = explode(',',$row->moveto);
			$query = $this->db->select('flow_id,fms_flow ')->from('fms_flow')->where_in('flow_id',$movetodata)->get();
			foreach($query->result() as $moveto){
			   $movetofms.="<span style='color:red; font-weight:bold'>".$moveto->fms_flow."</span><br>";
			}
			$dependentdata="";
			$query23 = $this->db->select('a.id,a.flowid, a.dependentflowid, b.flow_id,b.fms_flow ')->from('flowdependency a')->join('fms_flow b','a.dependentflowid=b.flow_id','left')->where('a.flowid',$row->flow_id)->get();
														foreach($query23->result() as $dependentto){
															$dependentdata.="<span style='color:red; font-weight:bold'>".$dependentto->fms_flow."</span><br>";
														}
			$tatfrom="";
			$restyu=$this->db->select('flow_id,fms_flow')->from('fms_flow')->where('production_flow_id',$row->production_flow_id)->where('flow_id !=',$row->flow_id)->get();
			if($restyu->num_rows()>0)
														{
														
															foreach($restyu->result() as $flowp)
															{
																$tatfrom.= $flowp->fms_flow;
															}
				
			}
			
			$scheduler_data[] = array('sr_no'=>$i,
			'production_flow'=>strtoupper($row->production_flow),
			'fms_flow'=>strtoupper($row->fms_flow),
			'who'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'what_wedo'=>strtoupper($row->what_wedo),
			'how_wedo'=>strtoupper($row->how_wedo),
			'when_wedo'=>strtoupper($row->total_days." Days ".$hours." ".$row->when_wedo),
			'video_link'=>$row->video_link,
			'setorder'=>$row->setorder,
			'moveto'=>$movetofms,
			'dependent'=>$dependentdata,
			'tatfrom'=>$tatfrom,
			'status'=>$sta,
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



public function update_fms_flow(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('flowname', 'flowname', 'required|trim');
	$this->form_validation->set_rules('who', 'who', 'required|trim');
	$this->form_validation->set_rules('what', 'what', 'required|trim');
	$this->form_validation->set_rules('how', 'how', 'required|trim');
	$this->form_validation->set_rules('when', 'when', 'required|trim');
	$this->form_validation->set_rules('tat', 'TAT', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/edit_fmsflow');
			}else
		{
			
			 $final = $this->input->post('final');
			    if($final=='1'){
			   $fstep = "1";
		   }else{
			   $fstep = "0";
		   }
			if($fstep=='1')
			{
			 $querycheckd = $this->db->select('flow_id')->from('fms_flow')->where('production_flow_id',$this->input->post('production_flow'))->where('finalstep','1')->where('flow_id!=',$this->uri->segment(3))->get();
		   $rescheck = $querycheckd->num_rows();
		   if($rescheck<>0){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Same flow cannot have more than 1 final stage</span><br/>');
				redirect(page_url.'FMS/edit_fms_flow/'.$this->uri->segment(3));
			   
		   }
				
			}
				
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $existingvalue = $this->input->post('existingvalue');
		   $moveto = $this->input->post('moveto');
			if($moveto<>'')
			{
		   $movetostep = implode(',',$moveto);
		   if($existingvalue>0){
			  $movetostep =  $existingvalue.",".$movetostep;
		   }else{
			   $movetostep= $movetostep;
		   }
			}else
			{
				$movetostep=$existingvalue;
			}
			
			
			$dependent = $this->input->post('dependent');
		   if($dependent=='1'){
			   $depval = "1";
		   }else{
			  $depval = "0";
		   }
			
		   $data=
			array('fms_flow'=>strtoupper($this->input->post('flowname')),
			'who_wedo'=>strtoupper($this->input->post('who')),
			'production_flow_id'=>strtoupper($this->input->post('production_flow')),
			'what_wedo'=>strtoupper($this->input->post('what')),
			'how_wedo'=>strtoupper($this->input->post('how')),
			'total_days'=>strtoupper($this->input->post('days')),
			'set_time'=>strtoupper($this->input->post('time')),
			'when_wedo'=>strtoupper($this->input->post('when')),
			'video_link'=>strtoupper($this->input->post('video_link')),
			'setorder'=>strtoupper($this->input->post('set_order')),
		    'moveto'=>$movetostep,
			'dependency'=>$depval,
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'uitype'=>$this->input->post('response_type'),
		    'actiontobetaken'=>$this->input->post('actiontobetaken'),
			'tat'=>$this->input->post('tat'),
			'finalstep'=>$fstep,
			'added_on'=>$added_time);
			$this->db->where('flow_id',$this->uri->segment(3));
			$res = $this->db->update('fms_flow',$data);
			if($res)
			{
				$dependent = $this->input->post('dependent');
				if($dependent=='1'){
					if(isset($_REQUEST['dependentto'])){	
					$tags1=count($_REQUEST['dependentto']);
					if($tags1>0)
					{
					$dependentto=$_REQUEST['dependentto'];
					
					
					for($x=0;$x<$tags1;$x++){
					if($dependentto[$x]!='')
						{
							$data=array('dependentflowid'=>$dependentto[$x],
							'flowid'=>$this->uri->segment(3));
							$this->db->insert('flowdependency',$data);
							
						   
						}
					}
					}
					}
					
					
				}
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/fms_flow');
				}
		   
			
			}
}
public function instruments(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('instrument_name', 'instrument_name', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/instruments_master');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('instruments_name')->from('presto_instruments')->where('instruments_name',$this->input->post('instrument_name'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'FMS/instruments');
			   
		   }else{
		   
			  if($this->input->post('fabrication')=='1')
			  {
				  $fab=1;
				  
			  }else
			  {
				  $fab=0;
			  }
		   $data=
			array('instruments_name'=>$this->input->post('instrument_name'),
			'status'=>$this->input->post('status'),
				  'fabrication'=>$fab,
				  'model_number'=>$this->input->post('instrument_model'),
				  'file_number'=>$this->input->post('instrument_file'),
				  'stock'=>$this->input->post('instrument_stock'),
				  'minstock'=>$this->input->post('instrument_minstock'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('presto_instruments',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/instruments');
				}
		   }
			
			}
}
public function instruments_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('*')->from('presto_instruments')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."FMS/update_instruments_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."FMS/update_instruments_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			
			
			$edit = "<a href='".page_url."FMS/edit_instruments/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments_name'=>strtoupper($row->instruments_name),
									  'modelno'=>$row->model_number,
									  'fileno'=>$row->file_number,
			'status'=>$sta,
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
public function update_instruments_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "presto_instruments";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect(page_url.'FMS/instruments');
		}
	public function edit_instruments()
	{
	$this->load->view('FMS/edit_instruments');
		
	}
	public function update_instruments(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('instrument_name', 'instrument_name', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/edit_instruments');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('instruments_name')->from('presto_instruments')->where('instruments_name',$this->input->post('instrument_name').'1')->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'FMS/instruments');
			   
		   }else{
		   
			   if($this->input->post('fabrication')=='')
			   { $fab=0;
			   }else
			   {
				   $fab=1;
			   }
		   $data=
			array('instruments_name'=>$this->input->post('instrument_name'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'fabrication'=>$fab,
				   'model_number'=>$this->input->post('instrument_model'),
				  'file_number'=>$this->input->post('instrument_file'),
				  'stock'=>$this->input->post('instrument_stock'),
				   'minstock'=>$this->input->post('instrument_minstock'),
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('presto_instruments',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/instruments');
				}
		   }
			
			}
}
public function order(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('order_type', 'order_type', 'required|trim');
	$this->form_validation->set_rules('person_name', 'person_name', 'required|trim');
	$this->form_validation->set_rules('po_number', 'po_number', 'required|trim');
	$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
	$this->form_validation->set_rules('email_id', 'email_id', 'required|trim');
	$this->form_validation->set_rules('mobile_number', 'mobile_number', 'required|trim');
	$this->form_validation->set_rules('payment_term', 'payment_term', 'required|trim');
	$this->form_validation->set_rules('installation_charges', 'installation_charges', 'required|trim');
	$this->form_validation->set_rules('status', 'status', 'required|trim');
	
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/order');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   
		   $internal_order_no = $this->input->post('internal_order_no');
		   if($internal_order_no==''){
			   $io = $this->input->post('auto_generated_io');
			   
		   }else{
			   $io = $internal_order_no;
		   }
		   $payment_terms = $this->input->post('payment_term');
		   if($payment_terms=='OTHER'){
			   $paymentterms = $this->input->post('otherpaymentoption');
		   }else{
			   $paymentterms=$payment_terms;
		   }
		   
		   $data=
			array('order_type'=>strtoupper($this->input->post('order_type')),
			'marketing_person'=>strtoupper($this->input->post('person_name')),
			'po_number'=>strtoupper($this->input->post('po_number')),
			'company_name'=>strtoupper($this->input->post('company_name')),
			'address'=>strtoupper($this->input->post('address')),
			'pincode'=>strtoupper($this->input->post('pincode')),
			'email'=>strtoupper($this->input->post('email_id')),
			'mobile_number'=>strtoupper($this->input->post('mobile_number')),
			'internal_order_no'=>$io,
			'discount'=>strtoupper($this->input->post('discount')),
			'order_value_after_discount'=>strtoupper($this->input->post('order_value_after_discount')),
			'advance_amount'=>strtoupper($this->input->post('advance_received')),
			'payment_terms'=>strtoupper($paymentterms),
			'installation_charges'=>strtoupper($this->input->post('installation_charges')),
			'installation_type'=>strtoupper($this->input->post('installation_type')),
			'installation_amount'=>$this->input->post('installation_charges_Amt'),
			'packing_type'=>$this->input->post('packing_type'),
			'packing_charges'=>$this->input->post('packing_charges'),
			'packing_amount'=>$this->input->post('packing_amount'),
			'freight_type'=>$this->input->post('freight_type'),
			'freight_amount'=>$this->input->post('freight_amount'),
			'remarks'=>$this->input->post('remarks'),
			'order_status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$orderid = $io;
			$res = $this->db->insert('prestogroup_orders',$data);
			$last_id = $this->db->insert_id();
			if($res)
			{
				$i=1;
				if(isset($_REQUEST['instruments'])){	
					$tags1=count($_REQUEST['instruments']);
					if($tags1>0)
					{
					$instruments_attruibute=$_REQUEST['instruments'];
					$qtty = $_REQUEST['qty'];
					if(count($qtty)>0){
						$totalqty = array_sum($qtty);
					}else{
						$totalqty="0";
					}
					
					for($x=0;$x<$tags1;$x++){
					if($instruments_attruibute[$x]!='')
						{
							$qty=$_REQUEST['qty'][$x];
							for($i=1; $i<=$qty; $i++){
								$query11 = $this->db->select('id')->from('order_instruments')->where('order_id',$last_id)->get();
								$res = $query11->num_rows();
								$plusval = $res+1;
								$jobcardnumber = $orderid." (".$plusval."/".$totalqty.")";
								 $data=array('item_id'=>$instruments_attruibute[$x],
							'qty'=>'1',
							'job_card_no'=>$jobcardnumber,
							'order_id'=>$last_id,
							'instrument_addedon'=>$added_time);
							$this->db->insert('order_instruments',$data);
							}
						   
						}
					}
					}
					}
				
				$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-danger">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/order');
				}
		   
			
			}
}

public function order_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->order_by('a.order_id','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->added_on));
			$time = date('H:i:s', strtotime($row->added_on));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			$status = $row->order_status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."FMS/update_instruments_status/".$row->order_id."/".$row->order_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."FMS/update_instruments_status/".$row->order_id."/".$row->order_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			$query = $this->db->select('a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			foreach($query->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:green; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="</tr>";
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			$restt1=$this->db->select('count(id) as totaljobcardplanned')->from('order_planning')->where('order_id',$row->order_id)->get();
			foreach($restt1->result() as $instcount1);
			$totjobcardplanned= $instcount1->totaljobcardplanned;
			if($totjobcard==$totjobcardplanned)
			{
				$planaction="<span class='btn btn-sm btn-success'>PLANNED</span>";
			}else{
			$planaction="<a href='".page_url."FMS/planorder/".$row->order_id."' class='btn btn-sm btn-warning'>PLAN (".$totjobcardplanned."/".$totjobcard.")</a>";
			}
			
			/** End **/
			$edit = "<a href='".page_url."FMS/edit_order/".$row->order_id."'><i class='fa fa-pencil'></i></a>";
			
			if($row->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
			if($row->packing_charges=='1'){
				$packcharges = "PAID BY PARTY";
				$packingcharges= $packcharges."<br> Amount - <strong>".$row->packing_amount."</strong>";
			}else{
					$packingcharges = "INCLUSIVE";
			}
			
			if($row->freight_type=='1'){
				$freigntcharges = "TO PAY BASIS";
				
			}else if($row->freight_type=='2'){
				$freigntcharges = "PAID BY PRESTO";
			}else if($row->freight_type=='3'){
				$frtcharges = "BILLED IN INVOICE";
				$freigntcharges= $frtcharges."<br> Amount - <strong>".$row->freight_amount."</strong>";
			}else{
				$freigntcharges = "OWN PICK-UP";
					
			}
			
			$scheduler_data[] = array('sr_no'=>$i,
			'planorder'=>$planaction,
			'added_on'=>$addeddate."".$addedtime,
			'order_type'=>strtoupper($row->order_type),
			'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'po_number'=>strtoupper($row->po_number),
			'company_name'=>strtoupper($row->company_name),
			'address'=>strtoupper($row->address),
			'email'=>strtoupper($row->email),
			'mobile_number'=>strtoupper($row->mobile_number),
			'internal_order_no'=>strtoupper($row->internal_order_no),
			'itemname'=>$html,
			'discount'=>strtoupper($row->discount)."%",
			'order_value_after_discount'=>strtoupper($row->order_value_after_discount),
			'advance_amount'=>strtoupper($row->advance_amount),
			'payment_terms'=>strtoupper($row->payment_terms),
			'installation_charges'=>strtoupper($ins_charges),
			'packingcharges'=>"<strong>Packing Type</strong> -".$row->packing_type."<br>".$packingcharges,
			'freigntcharges'=>$freigntcharges,
			'remarks'=>$row->remarks,
			'status'=>$sta,
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
public function edit_order()
	{
	$this->load->view('FMS/edit_order');
		
	}
public function remove_instruments(){
	$id = $this->uri->segment(3);
	$orderid = $this->uri->segment(4);
	
	$this->db->where('id',$id);
	$this->db->where('order_id',$orderid);
	$res = $this->db->delete('order_instruments');
	
	$this->db->where('jobcard_id',$id);
	$this->db->where('order_id',$orderid);
	$res = $this->db->delete('order_planning');
	
	$this->db->where('jobcardid',$id);
	$this->db->where('orderid',$orderid);
	$res = $this->db->delete('order_stage');
	
	if($res){
	$this->session->set_flashdata('message','<span style="color:blank; float-left:20px;" class="alert alert-danger">Thank you, Your record successfully deleted.</span><br/>');
	redirect(page_url.'FMS/edit_order/'.$this->uri->segment(4));	
	}
	
}

public function update_orderinfo(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('order_type', 'order_type', 'required|trim');
	$this->form_validation->set_rules('person_name', 'person_name', 'required|trim');
	$this->form_validation->set_rules('po_number', 'po_number', 'required|trim');
	$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
	$this->form_validation->set_rules('email_id', 'email_id', 'required|trim');
	$this->form_validation->set_rules('mobile_number', 'mobile_number', 'required|trim');
	$this->form_validation->set_rules('payment_term', 'payment_term', 'required|trim');
	$this->form_validation->set_rules('installation_charges', 'installation_charges', 'required|trim');
	$this->form_validation->set_rules('status', 'status', 'required|trim');
	
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/edit_order');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $payment_terms = $this->input->post('payment_term');
		   if($payment_terms=='OTHER'){
			   $paymentterms = $this->input->post('otherpaymentoption');
		   }else{
			   $paymentterms=$payment_terms;
		   }
		    $data=
			array('order_type'=>strtoupper($this->input->post('order_type')),
			'marketing_person'=>strtoupper($this->input->post('person_name')),
			'po_number'=>strtoupper($this->input->post('po_number')),
			'company_name'=>strtoupper($this->input->post('company_name')),
			'address'=>strtoupper($this->input->post('address')),
			'pincode'=>strtoupper($this->input->post('pincode')),
			'email'=>strtoupper($this->input->post('email_id')),
			'mobile_number'=>strtoupper($this->input->post('mobile_number')),
			'discount'=>strtoupper($this->input->post('discount')),
			'order_value_after_discount'=>strtoupper($this->input->post('order_value_after_discount')),
			'advance_amount'=>strtoupper($this->input->post('advance_received')),
			'payment_terms'=>strtoupper($paymentterms),
			'installation_charges'=>strtoupper($this->input->post('installation_charges')),
			'installation_type'=>strtoupper($this->input->post('installation_type')),
			'installation_amount'=>$this->input->post('installation_charges_Amt'),
			'packing_type'=>$this->input->post('packing_type'),
			'packing_charges'=>$this->input->post('packing_charges'),
			'packing_amount'=>$this->input->post('packing_amount'),
			'freight_type'=>$this->input->post('freight_type'),
			'freight_amount'=>$this->input->post('freight_amount'),
			'remarks'=>$this->input->post('remarks'),
			'order_status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'updated_on'=>$added_time);
			$io = $this->input->post('internal_order_no');
			$this->db->where('order_id',$this->uri->segment(3));
			$res = $this->db->update('prestogroup_orders',$data);
			$last_id = $this->uri->segment(3);
			if($res)
			{
				
				if(isset($_REQUEST['instruments'])){	
					$tags1=count($_REQUEST['instruments']);
					if($tags1>0)
					{
					$instruments_attruibute=$_REQUEST['instruments'];
					$qtty = $_REQUEST['qty'];
					if(count($qtty)>0){
						$totalqty = array_sum($qtty);
					}else{
						$totalqty="0";
					}
					
					for($x=0;$x<$tags1;$x++){
					if($instruments_attruibute[$x]!='')
						{
							$qty=$_REQUEST['qty'][$x];
							for($i=1; $i<=$qty; $i++){
								$query11 = $this->db->select('id')->from('order_instruments')->where('order_id',$last_id)->get();
								$result = count($query11->result());
								$res = $query11->num_rows();
								$grandtotal = $result+1;
								$plusval = $res+1;
								$jobcardnumber = $io." (".$plusval."/".$grandtotal.")";
								 $data=array('item_id'=>$instruments_attruibute[$x],
							'qty'=>'1',
							'job_card_no'=>$jobcardnumber,
							'order_id'=>$last_id);
							$this->db->insert('order_instruments',$data);
							}
						   
						}
					}
					}
					}
				
				$m=1;
					$query = $this->db->select('id')->from('order_instruments')->where('order_id',$this->uri->segment(3))->get();
					$total = count($query->result());
					foreach($query->result() as $updatedata){
					
					$jobcardnumber = $io." (".$m."/".$total.")";
					$data = array('job_card_no'=>$jobcardnumber);
					$this->db->where('id',$updatedata->id);
					$this->db->update('order_instruments',$data);
					$m++;	
					}
				
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
				redirect(page_url.'FMS/order');
				}
		   
			
			}
}
function getinstruments(){
	$term = $this->input->get('q');
	$query = $this->db->select('id, instruments_name, status')->from('presto_instruments')->where('status','1')->like('instruments_name',$term,'both')->get();
	if($query->num_rows()>0){
	foreach($query->result() as $instruments){
			$json[] = ['id'=>$prd1->id, 'text'=>$prd1->instruments_name];
	}
	echo json_encode($json);
	
	}
}

public function add_color_combination(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('purpose', 'Purpose', 'required|trim');
	$this->form_validation->set_rules('color_name', 'color_name', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/color_combination');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('purpose,color_code')->from('master_color_combination')->where('purpose',$this->input->post('purpose'))->where('color_code',$this->input->post('color_name'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'FMS/add_color_combination');
			   
		   }else{
		   
		   $data=
			array('purpose'=>strtoupper($this->input->post('purpose')),
			'color_code'=>strtoupper($this->input->post('color_name')),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('master_color_combination',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/add_color_combination');
				}
		   }
			
			}
}
public function color_combination_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('*')->from('master_color_combination')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."FMS/update_color_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."FMS/update_color_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			$color = "<div style='background-color:".$row->color_code."'>
			<span stle='height:20px; width:40px'>&nbsp;</span>
			</div>";
			
			$edit = "<a href='".page_url."FMS/edit_color_combination/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'purpose'=>strtoupper($row->purpose),
			'color_code'=>$color,
			'status'=>$sta,
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

public function update_color_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "master_color_combination";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect(page_url.'FMS/add_color_combination');
		}
public function edit_color_combination()
	{
	$this->load->view('FMS/edit_color_combination');
		
	}	

public function update_color_combination(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('purpose', 'Purpose', 'required|trim');
	$this->form_validation->set_rules('color_name', 'color_name', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/color_combination');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('purpose,color_code')->from('master_color_combination')->where('purpose',$this->input->post('purpose'))->where('color_code',$this->input->post('color_name'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-danger">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'FMS/add_color_combination');
			   
		   }else{
		   
		   $data=
			array('purpose'=>strtoupper($this->input->post('purpose')),
			'color_code'=>strtoupper($this->input->post('color_name')),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('master_color_combination',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/add_color_combination');
				}
		   }
		}
}		
		
public function production_plan_flow(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('flow_name', 'flow_name', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/production_flow');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('production_flow')->from('production_flow')->where('production_flow',$this->input->post('flow_name'))->or_where('sortorder',$this->input->post('flow_order'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Sorry,This record already exist.</span><br/>');
				redirect(page_url.'FMS/production_plan_flow');
			   
		   }else{
		   
		 if($this->input->post('parfms')=='1')
		   {
			   $pdms=1;
		   }else{
			   $pdms=0;
		   }
		   $data=
			array('production_flow'=>strtoupper($this->input->post('flow_name')),
			'status'=>$this->input->post('status'),
			'sortorder'=>$this->input->post('flow_order'),
			'parallel'=>$pdms,
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('production_flow',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/production_plan_flow');
				}
		   }
			
			}
}	
public function production_flow_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('*')->from('production_flow')->order_by('sortorder','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."FMS/update_production_flow_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."FMS/update_production_flow_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			
			
			$edit = "<a href='".page_url."FMS/edit_production_flow/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'production_flow'=>strtoupper($row->production_flow),
			'sort_order'=>$row->sortorder,
			'status'=>$sta,
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
public function update_production_flow_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "production_flow";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
			redirect(page_url.'FMS/production_plan_flow');
		}
public function edit_production_flow()
{
$this->load->view('FMS/edit_production_flow');
	
}
public function update_production_plan_flow(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('flow_name', 'flow_name', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/edit_production_flow');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('production_flow')->from('production_flow')->where('sortorder','4000')->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-danger">Sorry,You can not assign the same number to other flow.</span><br/>');
				redirect(page_url.'FMS/edit_production_flow/'.$this->uri->segment(3));
			   
		   }else{
		   
		if($this->input->post('parfms')=='1')
		   {
			   $parms='1';
			   
		   }else{
			   
			   $parms='0';
		   }
			   
if($this->input->post('mergefms')=='1')
		   {
			   $merge='1';
			   
		   }else{
			   
			   $merge='0';
		   }
		   $data=
			array('production_flow'=>strtoupper($this->input->post('flow_name')),
			'sortorder'=>$this->input->post('flow_order'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'parallel'=>$parms,
			'mergefms'=>$merge,
			'added_on'=>$added_time);
			  // echo "<pre>"; print_r($data);exit;
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('production_flow',$data);
			if($res)
			{
				if($merge=='1')
				{
					$mergedproductionflow=$this->input->post('mergefmswith');
					$mergedflow=$this->input->post('mergedflow');
					$restui=$this->db->select('id')->from('fmsmerge')->where('productionflow',$this->uri->segment(3))->where('mergewith',$mergedproductionflow)->get();
					if($restui->num_rows()>0)
					{
						$dataayaysu=array('flowid'=>$mergedflow);
						$this->db->where('productionflow',$this->uri->segment(3));
						$this->db->where('mergewith',$mergedproductionflow);
						$this->db->update('fmsmerge',$dataayaysu);
						
					}else{
						
						$dataayaysu=array('productionflow'=>$this->uri->segment(3),'mergewith'=>$mergedproductionflow,'flowid'=>$mergedflow,'addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmsmerge',$dataayaysu);
							
						}
					
				}else{ 
									
						$this->db->where('productionflow',$this->uri->segment(3));
						$this->db->delete('fmsmerge');						
					}
				
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/production_plan_flow');
				}
		   }
			
			}
}	
		function planorder()
		{
			$this->load->view('FMS/plan_order');
		}
		
		function jobcardautocomplete()
		{
			$oid=$this->uri->segment(3);
			$q=$_GET['q'];
			$qw=$this->db->select('a.id,a.job_card_no,a.item_id,b.instruments_name,b.model_number')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->like('a.job_card_no',$q)->where('a.order_id',$oid)->or_like('b.instruments_name',$q)->where('a.order_id',$oid)->get();
			if($qw->num_rows()>0)
			{
				
			$json=[];
				//echo "<pre>"; print_r($qw->result());exit;
			foreach($qw->result() as $qw1)
			{
				$qw2=$this->db->select('id')->from('order_planning')->where('jobcard_id',$qw1->id)->where('order_id',$oid)->get();
				if($qw2->num_rows()=='0')
				{
					
				$json[] = ['id'=>$qw1->id, 'text'=>$qw1->job_card_no.'-'.$qw1->instruments_name.'-'.$qw1->model_number];
					
				}
			}

				//echo "<pre>";print_r($json);exit;
			echo json_encode($json);
	}
	}
	
	
	function orderplanstepone()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		//$this->form_validation->set_rules('jobcardno', 'Job Card Required', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$orderid=$this->uri->segment(3);
			
			$jbcard=$this->input->post('jobcardno');
			$otype=$this->input->post('ordertype');
			$factory=$this->input->post('factory');
			$fileno=$this->input->post('fileno');
			$planstartsfrom=$this->input->post('pfms');
			$fabricreq=$this->input->post('fabricationreq');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('jobcard_id'=>$jbcard[$i],'order_id'=>$orderid,'orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'plannedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->insert('order_planning',$data);
				
				/** if Order Status is Recieved **/
				if($this->input->post('orderstatus')=='1')
				{
					/** Check for Parralel **/
				$resttts=$this->db->select('id')->from('production_flow')->where('id',$factory)->where('parallel','1')->get();
				$nrow=$resttts->num_rows();
				//echo $nrow;exit;
					/** End **/
					
					if($nrow==1)
					{
					$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					/** Check for next **/
						$restyuwew=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$planstartsfrom)->get();
						foreach($restyuwew->result() as $restyuwew112);
						$selectedfmsorder=$restyuwew112->setorder;
						if($selectedfmsorder==1)
						{
							$nextfmsorder=$selectedfmsorder+1;
							//echo $nextfmsorder;exit;
							$restyuwew=$this->db->select('flow_id')->from('fms_flow')->where('setorder',$nextfmsorder)->where('production_flow_id',$factory)->get();
							if($restyuwew->num_rows()>0)
							{
								foreach($restyuwew->result() as $resttssa);
								$nextflow=$resttssa->flow_id;
							}else
							{
								$nextflow=0;
							}
							
							if($nextflow!=0)
							{
							$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$nextflow);
							$this->db->insert('order_stage',$stage);

								
							}
							
							
						}
						/** End **/
						
						
					}else
					{
						$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					}
					
					
					if($fabricreq=='1')
					{
						$fabrice=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id','5')->where('setorder','1')->get();
						if($fabrice->num_rows()>0)
						{
							foreach($fabrice->result() as $fabrice1);
							
							$planstartsfrom=$fabrice1->flow_id;
						}else{
							$planstartsfrom=0;
						}
						
						
							/** Check for Parralel **/
				$resttts=$this->db->select('id')->from('production_flow')->where('id','5')->where('parallel','1')->get();
				$nrow=$resttts->num_rows();
				//echo $nrow;exit;
					/** End **/
				if($planstartsfrom<>0)
				{					
					if($nrow==1)
					{
						
					$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					/** Check for next **/
						$restyuwew=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$planstartsfrom)->get();
						foreach($restyuwew->result() as $restyuwew112);
						$selectedfmsorder=$restyuwew112->setorder;
						if($selectedfmsorder==1)
						{
							$nextfmsorder=$selectedfmsorder+1;
							//echo $nextfmsorder;exit;
							$restyuwew=$this->db->select('flow_id')->from('fms_flow')->where('setorder',$nextfmsorder)->where('production_flow_id','5')->get();
							if($restyuwew->num_rows()>0)
							{
								foreach($restyuwew->result() as $resttssa);
								$nextflow=$resttssa->flow_id;
							}else
							{
								$nextflow=0;
							}
							
							if($nextflow!=0)
							{
							$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$nextflow);
							$this->db->insert('order_stage',$stage);

								
							}
							
							
						}
						/** End **/
						
						
					}else
					{
						$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					}
					
				}else{  $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Unable to move to Fabrication since flow is not available</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);  }					
						
						
						
					}
					
					
					
				}
				
				/** End **/
				
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
			}else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000">Please select job card no.</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
				
			}
			
		
		
	} 
	
	function orderplansteponeolfbeforefabrication()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		//$this->form_validation->set_rules('jobcardno', 'Job Card Required', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$orderid=$this->uri->segment(3);
			
			$jbcard=$this->input->post('jobcardno');
			$otype=$this->input->post('ordertype');
			$factory=$this->input->post('factory');
			$fileno=$this->input->post('fileno');
			$planstartsfrom=$this->input->post('pfms');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('jobcard_id'=>$jbcard[$i],'order_id'=>$orderid,'orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'plannedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->insert('order_planning',$data);
				
				/** if Order Status is Recieved **/
				if($this->input->post('orderstatus')=='1')
				{
					/** Check for Parralel **/
				$resttts=$this->db->select('id')->from('production_flow')->where('id',$factory)->where('parallel','1')->get();
				$nrow=$resttts->num_rows();
				//echo $nrow;exit;
					/** End **/
					
					if($nrow==1)
					{
					$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					/** Check for next **/
						$restyuwew=$this->db->select('setorder')->from('fms_flow')->where('flow_id',$planstartsfrom)->get();
						foreach($restyuwew->result() as $restyuwew112);
						$selectedfmsorder=$restyuwew112->setorder;
						if($selectedfmsorder==1)
						{
							$nextfmsorder=$selectedfmsorder+1;
							//echo $nextfmsorder;exit;
							$restyuwew=$this->db->select('flow_id')->from('fms_flow')->where('setorder',$nextfmsorder)->where('production_flow_id',$factory)->get();
							if($restyuwew->num_rows()>0)
							{
								foreach($restyuwew->result() as $resttssa);
								$nextflow=$resttssa->flow_id;
							}else
							{
								$nextflow=0;
							}
							
							if($nextflow!=0)
							{
							$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$nextflow);
							$this->db->insert('order_stage',$stage);

								
							}
							
							
						}
						/** End **/
						
						
					}else
					{
						$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					}
				}
				
				/** End **/
				
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
			}else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000">Please select job card no.</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
				
			}
			
		
		
	}

	function orderplansteponeOlddd()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		//$this->form_validation->set_rules('jobcardno', 'Job Card Required', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$orderid=$this->uri->segment(3);
			
			$jbcard=$this->input->post('jobcardno');
			$otype=$this->input->post('ordertype');
			$factory=$this->input->post('factory');
			$fileno=$this->input->post('fileno');
			$planstartsfrom=$this->input->post('pfms');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('jobcard_id'=>$jbcard[$i],'order_id'=>$orderid,'orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->insert('order_planning',$data);
				
				/** if Order Status is Recieved **/
				if($this->input->post('orderstatus')=='1')
				{
					$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
				}
				
				/** End **/
				
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
			}else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000">Please select job card no.</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
				
			}
			
		
		
	}
public function view_planned_orders(){
	$this->load->view('FMS/planned_order_list');
}
public function planned_order_list()
	{
		$orderid = $this->uri->segment(3);
		$scheduler_data = array();
		$this->db->select('a.jobcard_id,a.id, a.order_id,a.planstartsfrom, a.orderstatus, a.remarks, a.ordertype, a.factory, a.fileno, a.plannedOn, a.plannedby, b.user_id, b.title, b.first_name, b.last_name, c.id, c.production_flow, d.id, d.item_id, d.qty, d.job_card_no,d.instrument_addedon, e.id, e.instruments_name, f.order_id, f.order_status, g.flow_id, g.fms_flow')->from('order_planning a')->join('system_users b','a.plannedby=b.user_id','left')->join('production_flow c','a.factory=c.id','left')->join('order_instruments d','a.jobcard_id=d.id','left')->join('presto_instruments e','d.item_id=e.id','left')->join('prestogroup_orders f','a.order_id=f.order_id','left')->join('fms_flow g','a.planstartsfrom=g.flow_id','left');
		if($orderid){
			$this->db->where('a.order_id',$orderid);
		}
		$this->db->where('f.order_status','1');
		$query = $this->db->order_by('a.plannedOn','desc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$ordertype = $row->ordertype;
			if($ordertype=='1'){
				$ordertypee = "STANDARD";
			}else{
				$ordertypee = "CUSTOMIZED";
			}
			$orderstatus = $row->orderstatus;
			if($orderstatus=='1'){
				$orderstatuss = "RECIEVED";
			}else if($orderstatus=='2'){
				$orderstatuss = "NOT RECIEVED";
			}else{
				$orderstatuss = "NOT CLEAR";
			}
			
			date_default_timezone_set("Asia/Kolkata");
			$addeddate = date('d-M-Y', strtotime($row->plannedOn));
			$time = date('H:i:s', strtotime($row->plannedOn));
			$addedtime = "<br>". date('g:i A', strtotime($time)); 
			
			 
			$actualtime = "NEED TO DISCUSS";
			$timestamp_show = date('d-M-Y', strtotime($row->instrument_addedon));
			$timestamp_time = date('H:i:s', strtotime($row->instrument_addedon));
			$timestamptime_show = "<br>". date('g:i A', strtotime($timestamp_time));
			
			$date1 = new DateTime($timestamp_show); 
			$date2 = new DateTime($addeddate); 
			$interval = $date1->diff($date2); 
			 $days = $interval->d; 
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp_show.$timestamptime_show,
			'instruments_name'=>strtoupper($row->instruments_name),
			'added_on'=>$addeddate."".$addedtime,
			'addedby'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'job_card_no'=>strtoupper($row->job_card_no),
			'production_flow'=>strtoupper($row->production_flow),
			'ordertypee'=>strtoupper($ordertypee),
			'orderstatuss'=>strtoupper($orderstatuss),
			'fileno'=>strtoupper($row->fileno),
			'actualtime'=>$actualtime,
			'remarks'=>strtoupper($row->remarks),
			'fms_flow'=>strtoupper($row->fms_flow),
			'totaldays'=>$days);
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

function getfmsslowprocesswise()
	{
		$fmsid=$this->uri->segment(3);

		$query = $this->db->select('a.fms_flow,a.flow_id,a.setorder')->from('fms_flow a')->where('a.production_flow_id',$fmsid)->order_by('setorder','ASC')->get();
		if($query->num_rows()>0)
		{
			$i=0;
		foreach($query->result() as $row){
if($i==0)
{
	$sel="selected";
}else{
	$sel="";
}
		echo '<option value="'.$row->flow_id.'" '.$sel.'>P'.strtoupper($row->setorder).'. '.strtoupper($row->fms_flow).'</option>';
		$i++;
		}
		}
		
	}
	
	

function getfmsslowprocesswiseforproductionedit()
	{
		
		$fmsid=$this->uri->segment(3);
		$restp=$this->db->select('flowid')->from('fmsmerge')->where('mergewith',$fmsid)->get();
														if($restp->num_rows()>0)
														{
															foreach($restp->result() as $restp12);
															$existingmerge=$restp12->flowid;
														}else { $existingmerge=""; }
														
		

		$query = $this->db->select('a.fms_flow,a.flow_id,a.setorder')->from('fms_flow a')->where('a.production_flow_id',$fmsid)->order_by('setorder','ASC')->get();
		if($query->num_rows()>0)
		{
			$i=0;
		foreach($query->result() as $row){
			if($existingmerge=='' || $existingmerge=='0')
			{
if($i==0)
{
	$sel="selected";
}else{
	$sel="";
}
}else
{
	if($row->flow_id==$existingmerge)
	{
		$sel="selected";
	}else{ 
	$sel="";
 }
	
}
		echo '<option value="'.$row->flow_id.'" '.$sel.'>P'.strtoupper($row->setorder).'. '.strtoupper($row->fms_flow).'</option>';
		$i++;
		}
		}
		
	}
	
	
	public function select_filenumber()
	{
	
	$jobcardno = $this->input->post('jobcardno');
		$query = $this->db->select('id, file_number')->from('presto_instruments')->where('id',$jobcardno)->get();
		foreach($query->result() as $fileinfo);
		echo $fileinfo->file_number; exit;
		
		
		}
	
	public function fms_reporting()
	{
	$this->load->view('FMS/fms_reporting');
		
	}
	
public function planned_actual()
	{
	$this->load->view('FMS/planned_actual');
		
	}	

	
	public function planned_actual_list()
	{
	
	$todaysdate=date('Y-m-d');
		$scheduler_data = array();
		$query = $this->db->select('a.production_flow_id,a.actiontobetaken,a.fms_flow,a.tat,a.moveto,a.dependency, a.flow_id as recordid, b.user_id, b.title, b.first_name, b.last_name, b.department_id, c.id, c.production_flow, c.sortorder, d.department_id, d.department')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id','left')->join('production_flow c','a.production_flow_id=c.id','left')->join('departments d','b.department_id=d.department_id','left')->order_by('c.sortorder','asc')->get();
		$res = $query->result();
		$i=1;
		
	
		foreach($res as $row)
		{	
			$todaypending=array();
			$todaypendingorderstageid=array();
			
/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat')->from('fms_flow')->where('flow_id',$row->recordid)->where('production_flow_id',$row->production_flow_id)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			/** End **/

		/** Get Pending Count **/
			$pend=$this->db->select('id as orderstageid,jobcardid')->from('order_stage')->where('userstatus','0')->where('flowstage',$row->recordid)->get();
			$pendcount=$pend->num_rows();
		
			/** End **/
	
			
/** Get Todays Pending Count **/
if($pendcount<>0)
{
	

	foreach($pend->result() as $proc)
{
		
		$odst=$this->db->select('plannedOn')->from('order_planning')->where('jobcard_id',$proc->jobcardid)->get();
		foreach($odst->result() as $odst1);
$timestamp=$this->getprevioustimestamp($tatstage,$odst1->plannedOn,$proc->jobcardid,$row->recordid,$proc->orderstageid);

$tattime = DateTime::createFromFormat('d-M-Y g:i A',$timestamp);
$finaltattime=date('g:i A',strtotime($timestamp));
	
$tatdate=$this->getupcomingtatdate($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid);
$formattedtatdate=date('Y-m-d',strtotime($tatdate));
if($todaysdate==$formattedtatdate)
{
$todaypending[]=1;
$todaypendingorderstageid[]=$proc->orderstageid;
}else {  
$todaypending[]=0; 
}
}

}else
{
$todaypending[]=0;
}


/** End **/
							
			$remarks=' <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">UPDATE REMARKS</button>';
			$remarks.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'FMS/update_fms_followup/'.$row->recordid.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE '.strtoupper($row->fms_flow).' FOLLOW-UP REMARKS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FMS FOLLOW-UP REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="fms_remarks" id="remarks" style="width:800px" required></textarea>
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
			
			
			$qry = $this->db->select('a.id, a.fms_id, a.remarks, a.added_on, a.added_by, b.user_id, b.title, b.first_name, b.last_name')->from(' fms_task_followup_detail a')->join('system_users b','a.added_by=b.user_id','left')->where('a.fms_id',$row->recordid)->limit(1)->order_by('a.id','desc')->get();
			if($qry->num_rows()>0){
				foreach($qry->result() as $auditremark);
				date_default_timezone_set("Asia/Kolkata");
				$addeddate = date('d-M-Y', strtotime($auditremark->added_on));
				$time = date('H:i:s', strtotime($auditremark->added_on));
				$addedtime = "<br>". date('g:i A', strtotime($time)); 
				$addedby = "UPDATED BY <strong>".$auditremark->title." ".$auditremark->first_name." ".$auditremark->last_name."</strong><br>";
				$updatetiming = "UPDATED ON <strong>".$addeddate.$addedtime."</strong>";
				$rmk= $auditremark->remarks."<br>".$addedby.$updatetiming;
			}else{
				$rmk = "";
			}
		
			/** Get Last Updated Time **/
			$lastup=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$row->recordid)->where('userstatus','1')->order_by('id','DESC')->limit(1)->get();
			if($lastup->num_rows()>0)
			{
				foreach($lastup->result() as $lastup12);
				
				$lastupdatedOn=date('Y-m-d',strtotime($lastup12->addedOn));
				
				$todaydates=date('Y-m-d');
				
				$date1 = new DateTime($todaydates);
				$date2 = new DateTime($lastupdatedOn);
				
				$updateinterval = $date1->diff($date2);
				$lupdated=$updateinterval->days;
		
				
			}else {  $lupdated=0; }
			/** End **/
			
			if($pendcount<>0)
			{
				$pendcount="<a href='".page_url."FMS/pendingorder/".$row->recordid."/0' style='color:white;'>".$pendcount."</a>";
			}else
			{
				$pendcount=$pendcount;
			}
			
			if(array_sum($todaypending)==0)
			{
				$tpending=0;
			}else
			{
				if(count($todaypendingorderstageid)>0)
				{
					$odrid="'" . implode ( "','", $todaypendingorderstageid ) . "'";
					 $allodrid=base64_encode($odrid);
					$tpending="<a href='".page_url."FMS/todayspendingorder/".$row->recordid."/".$allodrid."'>".array_sum($todaypending)."</a>";
				}else{
					$tpending=array_sum($todaypending);
				}
				
				
				
			}
				
			$actiontobetaken = $row->actiontobetaken;
			$auditor_remarks = "";
			$scheduler_data[] = array('sr_no'=>$i,
			'production_flow'=>strtoupper($row->production_flow),
			'fms_flow'=>strtoupper($row->fms_flow),
			'who'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'department'=>strtoupper($row->department),
			'totalpending'=>$pendcount,
			'todayspending'=>$tpending,
			'lastupdatedon'=>$lupdated.' Days ago',
			'actiontobetaken'=>strtoupper($actiontobetaken),
			'latestauditorremarks'=>$rmk,
			'remarks'=>$remarks);
			$i++;
		}
		
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	
	
public function planned_actual_listolf04feb()
	{
	
	$todaysdate=date('Y-m-d');
		$scheduler_data = array();
		$query = $this->db->select('a.production_flow_id,a.actiontobetaken,a.fms_flow,a.tat,a.moveto,a.dependency, a.flow_id as recordid, b.user_id, b.title, b.first_name, b.last_name, b.department_id, c.id, c.production_flow, c.sortorder, d.department_id, d.department')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id','left')->join('production_flow c','a.production_flow_id=c.id','left')->join('departments d','b.department_id=d.department_id','left')->order_by('c.sortorder','asc')->get();
		$res = $query->result();
		$i=1;
		
	
		foreach($res as $row)
		{	
			$todaypending=array();
			
/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat')->from('fms_flow')->where('flow_id',$row->recordid)->where('production_flow_id',$row->production_flow_id)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			/** End **/

		/** Get Pending Count **/
			$pend=$this->db->select('id as orderstageid,jobcardid')->from('order_stage')->where('userstatus','0')->where('flowstage',$row->recordid)->get();
			$pendcount=$pend->num_rows();
		
			/** End **/
	
			
/** Get Todays Pending Count **/
if($pendcount<>0)
{
	

	foreach($pend->result() as $proc)
{
		
		$odst=$this->db->select('plannedOn')->from('order_planning')->where('jobcard_id',$proc->jobcardid)->get();
		foreach($odst->result() as $odst1);
$timestamp=$this->getprevioustimestamp($tatstage,$odst1->plannedOn,$proc->jobcardid,$row->recordid,$proc->orderstageid);
$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
$finaltattime=$tattime->format('g:i A');
$tatdate=$this->getupcomingtatdate($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid);
$formattedtatdate=date('Y-m-d',strtotime($tatdate));
if($todaysdate==$formattedtatdate)
{
$todaypending[]=1;
}else {  
$todaypending[]=0; 
}
}

}else
{
$todaypending[]=0;
}


/** End **/
							
			$remarks=' <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">UPDATE REMARKS</button>';
			$remarks.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'FMS/update_fms_followup/'.$row->recordid.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE '.strtoupper($row->fms_flow).' FOLLOW-UP REMARKS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FMS FOLLOW-UP REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="fms_remarks" id="remarks" style="width:800px" required></textarea>
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
			
			
			$qry = $this->db->select('a.id, a.fms_id, a.remarks, a.added_on, a.added_by, b.user_id, b.title, b.first_name, b.last_name')->from(' fms_task_followup_detail a')->join('system_users b','a.added_by=b.user_id','left')->where('a.fms_id',$row->recordid)->limit(1)->order_by('a.id','desc')->get();
			if($qry->num_rows()>0){
				foreach($qry->result() as $auditremark);
				date_default_timezone_set("Asia/Kolkata");
				$addeddate = date('d-M-Y', strtotime($auditremark->added_on));
				$time = date('H:i:s', strtotime($auditremark->added_on));
				$addedtime = "<br>". date('g:i A', strtotime($time)); 
				$addedby = "UPDATED BY <strong>".$auditremark->title." ".$auditremark->first_name." ".$auditremark->last_name."</strong><br>";
				$updatetiming = "UPDATED ON <strong>".$addeddate.$addedtime."</strong>";
				$rmk= $auditremark->remarks."<br>".$addedby.$updatetiming;
			}else{
				$rmk = "";
			}
		
			/** Get Last Updated Time **/
			$lastup=$this->db->select('addedOn')->from('order_stage')->where('flowstage',$row->recordid)->where('userstatus','1')->order_by('id','DESC')->limit(1)->get();
			if($lastup->num_rows()>0)
			{
				foreach($lastup->result() as $lastup12);
				
				$lastupdatedOn=date('Y-m-d',strtotime($lastup12->addedOn));
				
				$todaydates=date('Y-m-d');
				
				$date1 = new DateTime($todaydates);
				$date2 = new DateTime($lastupdatedOn);
				
				$updateinterval = $date1->diff($date2);
				$lupdated=$updateinterval->days;
		
				
			}else {  $lupdated=0; }
			/** End **/
			
				
			$actiontobetaken = $row->actiontobetaken;
			$auditor_remarks = "";
			$scheduler_data[] = array('sr_no'=>$i,
			'production_flow'=>strtoupper($row->production_flow),
			'fms_flow'=>strtoupper($row->fms_flow),
			'who'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'department'=>strtoupper($row->department),
			'totalpending'=>$pendcount,
			'todayspending'=>array_sum($todaypending),
			'lastupdatedon'=>$lupdated.' Days ago',
			'actiontobetaken'=>strtoupper($actiontobetaken).'-'.$row->recordid,
			'latestauditorremarks'=>$rmk,
			'remarks'=>$remarks);
			$i++;
		}
		
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
	
	
	
public function planned_actual_listolddd()
	{
	$todayspending="";
		$scheduler_data = array();
		$query = $this->db->select('a.*, a.flow_id as recordid, b.user_id, b.title, b.first_name, b.last_name, b.department_id, c.id, c.production_flow, c.sortorder, d.department_id, d.department')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id','left')->join('production_flow c','a.production_flow_id=c.id','left')->join('departments d','b.department_id=d.department_id','left')->order_by('c.sortorder','asc')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$tatstaze = $row->tat;
			$previousorder = $tatstaze;
			$query = $this->db->select('flowstage, userstatus')->from('order_stage')->where('userstatus','0')->where('flowstage',$row->flow_id)->get();
			
			$totalpendingtask = count($query->result());
			if($totalpendingtask>0){
				$qry = $this->db->select('color_code')->from('master_color_combination')->where('id',3)->where('status','1')->get();
				$resultt = $qry->result();
				foreach($resultt as $color);
				$totalpendingfms = "<div style='background-color:".$color->color_code."; color:#fff; font-weight:bold;'>".$totalpendingtask."</div>";
			}else{
				$totalpendingfms = "0";
			}
			if($query->num_rows()>0){
					foreach($query->result() as $flowstageinformation){
					if($previousorder<>0){
					$abc = $this->db->select('addedOn')->from('order_stage')->where('flowstage',$previousorder)->get();
						if($abc->num_rows()>0){
							
					foreach($abc->result() as $pastinfo);
					$lasttaskcompleteddatetime = $pastinfo->addedOn;
					date_default_timezone_set("Asia/Kolkata");
					$taskcompletedate = date('Y-m-d', strtotime($lasttaskcompleteddatetime));
					$totaldaysslave = $row->total_days;
					$duedate=date('Y-m-d', strtotime($taskcompletedate."+".$totaldaysslave." days"));
					$today = date('Y-m-d');
					if($duedate==$today){
						$qry = $this->db->select('color_code')->from('master_color_combination')->where('id',4)->where('status','1')->get();
						$resultt = $qry->result();
						foreach($resultt as $color);
						$todpending = count($duedate);
						$todayspending="<div style='background-color:".$color->color_code."; font-weight:bold;'>".$todpending."</div>";
					}
					
					
						}else{
						$lasttaskcompleteddatetime = $row->added_on;
						
						date_default_timezone_set("Asia/Kolkata");
						$taskcompletedate = date('Y-m-d', strtotime($lasttaskcompleteddatetime));
						$totaldaysslave = $row->total_days;
						$duedate=date('Y-m-d', strtotime($taskcompletedate."+".$totaldaysslave." days"));
						$today = date('Y-m-d');
					if($duedate==$today){
						$qry = $this->db->select('color_code')->from('master_color_combination')->where('id',4)->where('status','1')->get();
						$resultt = $qry->result();
						foreach($resultt as $color);
						$todpending = count($duedate);
						$todayspending="<div style='background-color:".$color->color_code."; font-weight:bold;'>".$todpending."</div>";
					}
						}
					}else{
					$lasttaskcompleteddatetime = $row->added_on;
					$todayspending= "0";
					$duedate = "";					
					}
					$flowid = $flowstageinformation->flowstage;
			}
			}
			else{
				$todayspending= "0";
				$duedate = "";	
				$flowid = "";
			}
			
			$remarks=' <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">UPDATE REMARKS</button>';
			$remarks.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'FMS/update_fms_followup/'.$row->recordid.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE '.strtoupper($row->fms_flow).' FOLLOW-UP REMARKS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">FMS FOLLOW-UP REMARKS</label><br>
														<span id="error_color_name" style="color:red;"></span>
                                                       <textarea class="form-control" name="fms_remarks" id="remarks" style="width:800px" required></textarea>
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
			
			
			$qry = $this->db->select('a.id, a.fms_id, a.remarks, a.added_on, a.added_by, b.user_id, b.title, b.first_name, b.last_name')->from(' fms_task_followup_detail a')->join('system_users b','a.added_by=b.user_id','left')->where('a.fms_id',$row->recordid)->limit(1)->order_by('a.id','desc')->get();
			if($qry->num_rows()>0){
				foreach($qry->result() as $auditremark);
				date_default_timezone_set("Asia/Kolkata");
				$addeddate = date('d-M-Y', strtotime($auditremark->added_on));
				$time = date('H:i:s', strtotime($auditremark->added_on));
				$addedtime = "<br>". date('g:i A', strtotime($time)); 
				$addedby = "UPDATED BY <strong>".$auditremark->title." ".$auditremark->first_name." ".$auditremark->last_name."</strong><br>";
				$updatetiming = "UPDATED ON <strong>".$addeddate.$addedtime."</strong>";
				$rmk= $auditremark->remarks."<br>".$addedby.$updatetiming;
			}else{
				$rmk = "";
			}
			
			$actiontobetaken = $row->actiontobetaken;
			$auditor_remarks = "";
			$scheduler_data[] = array('sr_no'=>$i,
			'production_flow'=>strtoupper($row->production_flow),
			'fms_flow'=>strtoupper($row->fms_flow),
			'who'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'department'=>strtoupper($row->department),
			'totalpending'=>"<center>".$totalpendingfms."</center>",
			'todayspending'=>"<center>".$todayspending."</center>",
			'actiontobetaken'=>strtoupper($actiontobetaken),
			'remarks'=>$rmk."<br>".$remarks);
			$i++;
		}
		
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}
public function update_fms_followup(){
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $fms_id = $this->uri->segment(3);
		   $data=
			array('fms_id'=>$fms_id,
			'remarks'=>strtoupper($this->input->post('fms_remarks')),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('fms_task_followup_detail',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'FMS/planned_actual');
				}
		   
			
		
}	
	
public function holidays()
	{
	
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('holidayname', 'holidayname', 'required|trim');
	$this->form_validation->set_rules('holiday_date', 'color_name', 'required|trim');
	$this->form_validation->set_rules('holiday_days', 'holiday_days', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/holidays');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('holidays,holiday_date')->from('prestogroup_holidays')->where('holidays',strtoupper($this->input->post('holidayname')))->where('holiday_date',date('Y-m-d',strtotime($this->input->post('holiday_date'))))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Sorry,This record already exist.</span></div><br/>');
				redirect(page_url.'FMS/holidays');
			   
		   }else{
		   
		   $data=
			array('holidays'=>strtoupper($this->input->post('holidayname')),
			'holiday_date'=>date('Y-m-d',strtotime($this->input->post('holiday_date'))),
			'holiday_days'=>strtoupper($this->input->post('holiday_days')),
			'added_on'=>$added_time);
			
			$res = $this->db->insert('prestogroup_holidays',$data);
			if($res)
			{

				$year = date('Y');
				$start_date=strtotime("01 Jan ".$year);
				$end_date=strtotime("31 Dec ".$year);
				while(1){
				$start_date=strtotime('next sunday', $start_date);
				if($start_date>$end_date)
				break;
				$sundaydate =  date("Y-m-d",$start_date);
				$qry = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$sundaydate)->get();
				if($qry->num_rows()>0){
					
				}else{
					$data=
					array('holidays'=>"SUNDAY HOLIDAY",
					'holiday_date'=>$sundaydate,
					'holiday_days'=>"SUNDAY",
					'added_on'=>$added_time,
					'is_it_sunday'=>'1');
					$this->db->insert('prestogroup_holidays',$data);
				}
				}
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'FMS/holidays');
				}
		   }
			
			}
		
	}
public function add_sundays(){
		$year = date('Y');
		$start_date=strtotime("01 Jan ".$year);
		$end_date=strtotime("31 Dec ".$year);
		while(1){
		$start_date=strtotime('next sunday', $start_date);
		if($start_date>$end_date)
		break;
		echo date("Y-m-d",$start_date)."</br>"; 

		}
}

public function holiday_list()
	{
		$start_date = date('Y')."-01-01";
		$end_date = date('Y')."-12-31";
		$scheduler_data = array();
		$query = $this->db->select('*')->from('prestogroup_holidays')->where('is_it_sunday','0')->where('holiday_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			date_default_timezone_set("Asia/Kolkata");
			$edit = "<a href='".page_url."FMS/edit_holidays/".$row->holiday_id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'holidays'=>strtoupper($row->holidays),
			'holiday_date'=>$row->holiday_date,
			'holiday_days'=>$row->holiday_days,
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

	public function edit_holidays()
	{
	
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('holidayname', 'holidayname', 'required|trim');
	$this->form_validation->set_rules('holiday_date', 'color_name', 'required|trim');
	$this->form_validation->set_rules('holiday_days', 'holiday_days', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/edit_holidays');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $query = $this->db->select('holidays,holiday_date')->from('prestogroup_holidays')->where('holidays',strtoupper($this->input->post('holidayname')))->where('holiday_date',date('Y-m-d',strtotime($this->input->post('holiday_date'))))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">Sorry,This record already exist.</span></div><br/>');
				redirect(page_url.'FMS/holidays');
			   
		   }else{
		   
		   $data=
			array('holidays'=>strtoupper($this->input->post('holidayname')),
			'holiday_date'=>date('Y-m-d',strtotime($this->input->post('holiday_date'))),
			'holiday_days'=>strtoupper($this->input->post('holiday_days')),
			'added_on'=>$added_time);
			$this->db->where('holiday_id',$this->uri->segment(3));
			$res = $this->db->update('prestogroup_holidays',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
				redirect(page_url.'FMS/holidays');
				}
		   }
			
			}
		
	}
	public function delete_moveto(){
	$id = $this->uri->segment(3);
	$data = array('moveto'=>'0');
	$this->db->where('flow_id',$id);
	$this->db->update('fms_flow',$data);
	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank You! Record successfully removed.</span></div><br/>');
				redirect(page_url.'FMS/edit_fms_flow/'.$id);
}

public function deletedependency(){
	$id = $this->uri->segment(3);
	$fms_id = $this->uri->segment(4);
	$data = array('moveto'=>'0');
	$this->db->where('id',$id);
	$this->db->delete('flowdependency');
	
	$query = $this->db->select('flowid')->from('flowdependency')->where('flowid',$fms_id)->get();
	if($query->num_rows()>0){
	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank You! Record successfully removed.</span></div><br/>');
	redirect(page_url.'FMS/edit_fms_flow/'.$fms_id);	
	}else{
	$data = array('dependency'=>'0');
	$this->db->where('flow_id',$id);
	$this->db->update('fms_flow',$data);
	$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank You! Record successfully removed.</span></div><br/>');
	redirect(page_url.'FMS/edit_fms_flow/'.$fms_id);
	}
	
	
	
	
}
	

function getfabricationdetails()
{
	$ins=$this->uri->segment(3);
//echo $ins;exit;
$rest=$this->db->select('a.item_id,b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$ins)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $rest12);
		
		$fabrication=$rest12->fabrication;
		
		echo $fabrication;exit;
}else
{
  echo "0";exit;
}

}
	


function pendingorder()
{
	
	$this->load->view('FMS/planned_actual_order_details');
	
}

function planned_actual_pendinglist()
{
	$scheduler_data=array();
	$status=0;
	$completedon=0;
	$flowstage=$this->uri->segment(3);
	$type=$this->uri->segment(4);
	$pend=$this->db->select('id as orderstageid,jobcardid,orderid')->from('order_stage')->where('userstatus','0')->where('flowstage',$flowstage)->get();
	$pendcount=$pend->num_rows();
	
	if($pendcount<>'0')
	{		
$i=1;
		foreach($pend->result() as $proc)
		{
			/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			/** End **/
			
		$restyu=$this->db->select('a.job_card_no,b.instruments_name')->from('order_instruments a')->join('presto_instruments
		b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
			if($restyu->num_rows()>0)
			{
				foreach($restyu->result() as $restyu1);
				$odno=$restyu1->job_card_no;
				$insname=$restyu1->instruments_name;
			}else{ $odno=''; $insname=''; }
			
			
			$odst=$this->db->select('plannedOn')->from('order_planning')->where('jobcard_id',$proc->jobcardid)->get();
		foreach($odst->result() as $odst1);
		$timestamp=$this->getprevioustimestamp($tatstage,$odst1->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
		$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		$finaltattime=$tattime->format('g:i A');
		$tatdate=$this->getupcomingtatdate($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid);
		
	
		$totdays=$this->gettotaldays($status,$timestamp,$completedon);		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp,
			'orderno'=>$odno,
			'instrumentname'=>$insname,
			'plandate'=>$tatdate.' '.$finaltattime,
			'totaldays'=>$totdays);
			
			$i++;
		}
		
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
			

			
	}else{
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
		
	}
	
	
	
}

function todayspendingorder()
{
	
	$this->load->view('FMS/planned_actual_todayorder_details');
	
}



function planned_actual_todayspendinglist()
{
	$scheduler_data=array();
	$status=0;
	$completedon=0;
	$flowstage=$this->uri->segment(3);
	$orderstageids=base64_decode($this->uri->segment(4));

	$pend=$this->db->select('id as orderstageid,jobcardid,orderid')->from('order_stage')->where('userstatus','0')->where('flowstage',$flowstage)->where_in('id',$orderstageids,false)->get();
	$pendcount=$pend->num_rows();
	
	if($pendcount<>'0')
	{		
$i=1;
		foreach($pend->result() as $proc)
		{
			/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days, tat')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			$totaldaysslave = $fmsinformation->total_days;
			$tatstage = $fmsinformation->tat;
			/** End **/
			
		$restyu=$this->db->select('a.job_card_no,b.instruments_name')->from('order_instruments a')->join('presto_instruments
		b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
			if($restyu->num_rows()>0)
			{
				foreach($restyu->result() as $restyu1);
				$odno=$restyu1->job_card_no;
				$insname=$restyu1->instruments_name;
			}else{ $odno=''; $insname=''; }
			
			
			$odst=$this->db->select('plannedOn')->from('order_planning')->where('jobcard_id',$proc->jobcardid)->get();
		foreach($odst->result() as $odst1);
		$timestamp=$this->getprevioustimestamp($tatstage,$odst1->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
		$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
		$finaltattime=$tattime->format('g:i A');
		$tatdate=$this->getupcomingtatdate($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid);
		
	
		$totdays=$this->gettotaldays($status,$timestamp,$completedon);		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp,
			'orderno'=>$odno,
			'instrumentname'=>$insname,
			'plandate'=>$tatdate.' '.$finaltattime,
			'totaldays'=>$totdays);
			
			$i++;
		}
		
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($scheduler_data),
			"iTotalDisplayRecords" => count($scheduler_data),
			"aaData"=>$scheduler_data);
			echo json_encode($results);
			

			
	}else{
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
		
	}
	
	
	
}
	


function getstockdetails()
{
	$jobcardid=$this->uri->segment(3);
	$restty=$this->db->select('a.stock')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->where('b.id',$jobcardid)->get();
	if($restty->num_rows()>0)
	{
		foreach($restty->result() as $restty1);
		
		$stock=$restty1->stock;
		
		
	}else
	{
		$stock=0;
	}
	
	
	echo $stock;
}


function getminstockdetails()
{
	$jobcardid=$this->uri->segment(3);
	$restty=$this->db->select('a.minstock')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->where('b.id',$jobcardid)->get();
	if($restty->num_rows()>0)
	{
		foreach($restty->result() as $restty1);
		
		$minstock=$restty1->minstock;
		
		
	}else
	{
		$minstock=0;
	}
	
	
	echo $minstock;
}


function orderplansteptwo()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		//$this->form_validation->set_rules('jobcardno', 'Job Card Required', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$orderid=$this->uri->segment(3);
			
			$jbcard=$this->input->post('jobcardno');
			$otype=$this->input->post('ordertype');
			$factory=$this->input->post('factory');
			$fileno=$this->input->post('fileno');
			$planstartsfrom=$this->input->post('pfms');
			$reorder=$this->input->post('reorder');
			//$fabricreq=$this->input->post('fabricationreq');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('jobcard_id'=>$jbcard[$i],'order_id'=>$orderid,'orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'plannedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->insert('order_planning',$data);
				$lid=$this->db->insert_id();
				/** if Order Status is Recieved **/
				if($this->input->post('orderstatus')=='1')
				{
					
					$stockdata=array('planid'=>$lid,'qty'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					$this->db->insert('instockfms',$stockdata);
					$resty=$this->db->insert_id();
					if($resty<>0)
					{
					$rty=$this->db->select('a.stock,b.item_id,a.minstock')->from('presto_instruments a')->join('order_instruments
 b','a.id=b.item_id')->where('b.id',$jbcard[$i])->get();
					if($rty->num_rows()>0)
					{
						foreach($rty->result() as $rty1);
						$instrumentid=$rty1->item_id;
						$currstock=$rty1->stock;
						$minstock=$rty1->minstock;
						$newstock=$currstock-1;
						//echo $newstock;exit;
						$datanew=array('stock'=>$newstock);
						$this->db->where('id',$instrumentid);
						$this->db->update('presto_instruments',$datanew);
					}}
					
					$datacompqli=array('complete'=>'1');
					$this->db->where('id',$jbcard[$i]);
					$this->db->update('order_instruments',$datacompqli);
					
					if($reorder=='1')
					{
						$reorderqty=$minstock-$newstock;
				
					
$prestio=$this->db->select('order_id')->from('prestogroup_orders')->where('company_name','PRESTO STANTEST PVT LTD')->get();
$io=$prestio->num_rows();
$internalolll="100000";
$iono=$internalolll+1;				
						
					$datareorder=array('order_type'=>strtoupper('SALE'),
					'marketing_person'=>strtoupper('11'),
					'po_number'=>strtoupper('1234'),
					'company_name'=>strtoupper('PRESTO STANTEST PVT LTD'),
					'address'=>strtoupper('Phase-1, I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana'),
					'pincode'=>strtoupper('121003'),
					'email'=>strtoupper('info@prestogroup.com'),
					'mobile_number'=>strtoupper('1294272727'),
					'internal_order_no'=>'1000000',
					'discount'=>0,
					'order_value_after_discount'=>strtoupper('0.00'),
					'advance_amount'=>strtoupper('0.00'),
					'payment_terms'=>strtoupper('100% AGAINST DELIVERY.'),
					'installation_charges'=>'0',
					'installation_type'=>'',
					'installation_amount'=>'0.00',
					'packing_type'=>'WOODEN',
					'packing_charges'=>'0',
					'packing_amount'=>'0.00',
					'freight_type'=>'2',
					'freight_amount'=>'0.00',
					'remarks'=>'PRESTO INTERNAL ORDER',
					'order_status'=>'1',
					'added_by'=>$_SESSION['logged_in']['user_id'],
					'added_on'=>date('Y-m-d H:i:s'),
					'selforder'=>'1');
					$orderid =$orderid;
					$res = $this->db->insert('prestogroup_orders',$datareorder);
					$last_id = $this->db->insert_id();
					if($last_id<>0)
					{

$qty=$reorderqty;
					
						//$iono=
						for($i=1; $i<=$qty; $i++){
						$query11 = $this->db->select('id')->from('order_instruments')->where('order_id',$last_id)->get();
						$res = $query11->num_rows();
						$plusval = $res+1;
						$jobcardnumber = $iono." (".$plusval."/".$reorderqty.")";
						
						$dataorins=array('item_id'=>$instrumentid,
						'qty'=>'1',
						'job_card_no'=>$jobcardnumber,
						'order_id'=>$last_id,
						'instrument_addedon'=>date('Y-m-d H:i:s'));
						//echo "<pre>"; print_r($dataorins);exit;
						$this->db->insert('order_instruments',$dataorins);
						
						}
						
						
						
						



					}else{

						echo "Unable to add reorder quantity";exit;
			
					}}
					
					/** END REORDER **/
					
					
				}
				
			
				
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
			}else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000">Please select job card no.</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
				
			}
			
		
		
	} 
	
	
	
function orderplanstepthree()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		//$this->form_validation->set_rules('jobcardno', 'Job Card Required', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$orderid=$this->uri->segment(3);
			
			$jbcard=$this->input->post('jobcardno');
			$otype=$this->input->post('ordertype');
			$factory=$this->input->post('factory');
			$fileno=$this->input->post('fileno');
			$planstartsfrom=$this->input->post('pfms');
			$reorder=$this->input->post('reorder');
			//$fabricreq=$this->input->post('fabricationreq');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('jobcard_id'=>$jbcard[$i],'order_id'=>$orderid,'orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'plannedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->insert('order_planning',$data);
				$lid=$this->db->insert_id();
				/** if Order Status is Recieved **/
				
					
					$stockdata=array('planid'=>$lid,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					$this->db->insert('boughtoutfms',$stockdata);
					$resty=$this->db->insert_id();
				
			
				
			}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
			}else
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000">Please select job card no.</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
				
			}
			
		
		
	} 
	




}