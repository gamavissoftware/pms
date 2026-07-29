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
$user_id =$this->session->userdata['logged_in']['user_id'];	
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$this->load->model('Fms_model','fmsmodel');
		
		$this->load->library('../controllers/Orderstage');
		if($user_id=='66' || $user_id=='67' || $user_id=='1'){
			
		}else{
			
		$ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }
		}
		
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
			  
			  
			      $jumpfabrication = $this->input->post('jumpfabrication');
		   if($jumpfabrication=='1'){
			   $jumpfabrication = "1";
		   }else{
			   $jumpfabrication = "0";
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
			'jumpfabricationappl'=>$jumpfabrication,
			'status'=>strtoupper($this->input->post('status')),
			'stockvalue'=>$this->input->post('stockvalue'),
			'added_by'=>$user_id,
			'added_on'=>$added_time,
			'pdays'=>$this->input->post('pdays'));
			
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
			'stockvalue'=>floatval($row->stockvalue)." %",
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
			
			
			 $jumpfabrication = $this->input->post('jumpfabrication');
		   if($jumpfabrication=='1'){
			   $jumpfabrication = "1";
		   }else{
			  $jumpfabrication = "0";
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
				  'jumpfabricationappl'=>$jumpfabrication,
				   'stockvalue'=>$this->input->post('stockvalue'),
				   'pdays'=>$this->input->post('pdays'),
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
				
				/** CHECK FOR OBSERVER**/
				
				
				$this->db->where('flowid',$this->uri->segment(3));
				$this->db->delete('flowobserver');
				
				$ob=$this->input->post('observer');
               
				for($y=0;$y<count($ob);$y++)
				{
				    
				    
					$checkforob=array('flowid'=>$this->uri->segment(3),'userid'=>$ob[$y],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					$this->db->insert('flowobserver',$checkforob);
					
				}
				/** END **/
				
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
		   
		   $photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
			$cat_image=end($image1);
			$instrumentimg=time().'.'.$cat_image;
			move_uploaded_file($_FILES["picture"]["tmp_name"],UPLOADPATH.'instrumentimg/' . $instrumentimg);
			}else
			{
				$instrumentimg="";
				}	
			
			  if($this->input->post('fabrication')=='1')
			  {
				  $fab=1;
				  
			  }else
			  {
				  $fab=0;
			  }
		   $data=
                    array('type'=>$this->input->post('type'),
                    'instruments_name'=>$this->input->post('instrument_name'),
                    'status'=>$this->input->post('status'),
                    'fabrication'=>$fab,
                    'model_number'=>$this->input->post('instrument_model'),
                    'file_number'=>$this->input->post('instrument_file'),
                    'stock'=>$this->input->post('instrument_stock'),
					'instruments_of'=>$this->input->post('instruments_of'),
                    'minstock'=>$this->input->post('instrument_minstock'),
                    'mvalue'=>$this->input->post('mvalue'),
                    'psize'=>$this->input->post('psize'),
					'image'=>$instrumentimg,
                    'calibrationcost'=>$this->input->post('ccost'),
                    'added_by'=>$user_id,
                    'added_on'=>$added_time);
			
			$res = $this->db->insert('presto_instruments',$data);
			
            $lid=$this->db->insert_id();
            $unit=$this->input->post('unit');
            for($t=0;$t<count($unit);$t++)
            {
            $dataunit=array('mid'=>$lid,'unitid'=>$unit[$t]);
            $this->db->insert('instrument_unit',$dataunit);
            
            }
			
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
		$type=$this->uri->segment(3);
		$fincode=$this->uri->segment(4);
		
		$this->db->select('*')->from('presto_instruments')->where('instruments_of','1')->order_by('type','ASC');
		if($type<>'')
		{
		    $this->db->where('type',$type);
		}
		if($fincode<>'')
		{
		$this->db->where('fincode','');
		}
		
		$query =$this->db->order_by('instruments_name','ASC')->get();
		// /echo $query->num_rows();exit;
		if($query->num_rows() > 0) {
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
			
			if($row->type=='0')
			{
			$Restyuoi=$this->db->select('id')->from('machine_bom')->where('mid',$row->id)->get();
			if($Restyuoi->num_rows()>0)
			{
			    $up="YES";
			}else
			{
			    $up="NO";
			}
			}else
			{
			    $up='';
			}
			if($row->alias==0)
			{
			$al="NO";
			}else
			{
			$al="Yes";
			}
			
			
				$openingstock="<a href='".page_url."FMS/addos/".$row->id."' target='_blank'><span class='btn  btn-xs btn-warning'>Add Opening Stock</span></a>";
			$edit = "<a href='".page_url."FMS/edit_instruments/".$row->id."/".$type."'><i class='fa fa-pencil'></i></a>";
			$similardiversion="<a href='".page_url."FMS/similiarmachinefordiversion/".$row->id."'><span class='btn btn-warning btn-xs'>Similar Machines</span></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments_name'=>strtoupper($row->instruments_name),
			'similardiversion'=>$similardiversion,
			'openingstock'=>$openingstock,
			'costsheet'=>'<a href="'.page_url.'Reporting/generatebomcostsheet/'.$row->id.'"><span class="btn btn-warning btn-xs">Cost Sheet</span></a>',
									  'modelno'=>$row->model_number,
									  'fileno'=>$row->file_number,
									  'bomupdate'=>$up,
									  'bom'=>"<a href='".page_url."Store/bom/".$row->id."' ><span class='btn btn-warning btn-xs'>ADD/VIEW BOM</span></a>",
			'status'=>$sta,
			'aliasupdate'=>$al,
			'mvalue'=>floatval($row->mvalue),
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

public function testronix_instruments_master_list()
	{
		$scheduler_data = array();
		$type=$this->uri->segment(3);
		$fincode=$this->uri->segment(4);
		
		$this->db->select('*')->from('presto_instruments')->where('instruments_of','2')->order_by('type','ASC');
		if($type<>'')
		{
		    $this->db->where('type',$type);
		}
		if($fincode<>'')
		{
		$this->db->where('fincode','');
		}
		
		$query =$this->db->order_by('instruments_name','ASC')->get();
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
			
			if($row->type=='0')
			{
			$Restyuoi=$this->db->select('id')->from('machine_bom')->where('mid',$row->id)->get();
			if($Restyuoi->num_rows()>0)
			{
			    $up="YES";
			}else
			{
			    $up="NO";
			}
			}else
			{
			    $up='';
			}
			if($row->alias==0)
			{
			$al="NO";
			}else
			{
			$al="Yes";
			}
			
			
				$openingstock="<a href='".page_url."FMS/addos/".$row->id."' target='_blank'><span class='btn  btn-xs btn-warning'>Add Opening Stock</span></a>";
			$edit = "<a href='".page_url."FMS/edit_instruments/".$row->id."/".$type."'><i class='fa fa-pencil'></i></a>";
			$similardiversion="<a href='".page_url."FMS/similiarmachinefordiversion/".$row->id."'><span class='btn btn-warning btn-xs'>Similar Machines</span></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments_name'=>strtoupper($row->instruments_name),
			'similardiversion'=>$similardiversion,
			'openingstock'=>$openingstock,
			'costsheet'=>'<a href="'.page_url.'Reporting/generatebomcostsheet/'.$row->id.'"><span class="btn btn-warning btn-xs">Cost Sheet</span></a>',
									  'modelno'=>$row->model_number,
									  'fileno'=>$row->file_number,
									  'bomupdate'=>$up,
									  'bom'=>"<a href='".page_url."Store/bom/".$row->id."' ><span class='btn btn-warning btn-xs'>ADD/VIEW BOM</span></a>",
			'status'=>$sta,
			'aliasupdate'=>$al,
			'mvalue'=>floatval($row->mvalue),
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
			   if($this->input->post('pptype')=='2')
			   {
			       
			      $fincode=$this->input->post('fincode');
			   }else
			   {
			       $fincode='';
			   }
			   
			   
			    if($this->input->post('alias')=='')
			   {
			   	$alia=0;
			   }else
			   {
			   	$alia=$this->input->post('alias');
			   }
			 $photo=$_FILES['picture']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$instrumentimg=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],UPLOADPATH.'instrumentimg/' . $instrumentimg);
			}else
			{
				$instrumentimg=$this->input->post('oldimg');
				}  
			   
		   $data=
			array('type'=>$this->input->post('pptype'),
			'instruments_name'=>$this->input->post('instrument_name'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'fabrication'=>$fab,
				   'model_number'=>$this->input->post('instrument_model'),
				  'file_number'=>$this->input->post('instrument_file'),
				  'stock'=>$this->input->post('instrument_stock'),
				   'minstock'=>$this->input->post('instrument_minstock'),
				   'instruments_of'=>$this->input->post('instruments_of'),
				   'mvalue'=>$this->input->post('mvalue'),
				   'image'=>$instrumentimg,
				   'psize'=>$this->input->post('psize'),
				   'calibrationcost'=>$this->input->post('ccost'),
			'added_on'=>$added_time,
			'added_by'=>$_SESSION['logged_in']['user_id'],
			'fincode'=>$fincode,
			'alias'=>$alia);
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('presto_instruments',$data);
			
			$this->db->where('mid',$this->uri->segment(3));
			$this->db->delete('instrument_unit');
			
			$unit=$this->input->post('unit');
			for($t=0;$t<count($unit);$t++)
			{
				$dataunit=array('mid'=>$this->uri->segment(3),'unitid'=>$unit[$t]);
				$this->db->insert('instrument_unit',$dataunit);
				
			}
			if($res)
			{
				$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/instruments/'.$this->uri->segment(4));
				}
		   }
			
			}
}
public function testronix_order(){
	$this->load->view('FMS/testronixorder');
}
public function order(){
    
     $istex=0;
     $ip = $_SERVER["REMOTE_ADDR"];
     
     if($this->input->post('istestronix')==1)
     {
     $istex=1;
     }else
     {
     $istex=0;
     }
     
   
            $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }


	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('order_type', 'order_type', 'required|trim');
	$this->form_validation->set_rules('person_name', 'person_name', 'required|trim');
	$this->form_validation->set_rules('po_number', 'po_number', 'required|trim');
	$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
//	$this->form_validation->set_rules('email_id', 'email_id', 'required|trim');
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
		    
		    $serviceorder=$this->input->post('serviceorder');
			if($serviceorder==0)
			{
				$serord=0;
				$serodid=0;
			}else{
				
				$serord=1;
				$serodid=$serviceorder;
			}
			
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
				  'phone'=>strtoupper($this->input->post('phone')),
			'contact_person'=>strtoupper($this->input->post('contactperson')),
			'designation'=>strtoupper($this->input->post('designation')),
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
			'added_on'=>$added_time,
			'servicerepairorder'=>$serord,
			'servicerepairid'=>$serodid,
			'testronix'=>$istex);
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
								
								$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
								$res1 = $query111->num_rows();
								$serial = $res1+1;
								$mserial= sprintf("%03d", $serial);
									$machserial=date('my').'-'.$mserial;
							
							 $extserial=$this->chkforextraserial($instruments_attruibute[$x]);
                                
                                if($extserial=='1')
                                {
                                    $mserial= sprintf("%03d", $serial+1);
                                    $extramachserial=date('my').'-'.$mserial.rand(10,99);
                                }else
                                {
                                    $extramachserial='';
                                }
								
								$jobcardnumber = $orderid." (".$plusval."/".$totalqty.")";
								 $data=array('item_id'=>$instruments_attruibute[$x],
							'qty'=>'1',
							'job_card_no'=>$jobcardnumber,
							'order_id'=>$last_id,
							'mserialno'=>$machserial,
							'extraserialno'=>$extramachserial,
							'instrument_addedon'=>$added_time);
							$this->db->insert('order_instruments',$data);
							}
						   
						}
					}
					}
					}
				
				
				if($serviceorder<>0)
					{
						$repdata=array('service'=>'1','servicedoneon'=>date('Y-m-d H:i:s'),'servicedoneby'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('id',$serodid);
						$this->db->update('service_repair_request',$repdata);
						
					}
					
				$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-danger">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/order');
				}
		   
			
			}
}

public function orderplanninglist()
{
     $ip = $_SERVER["REMOTE_ADDR"];
            $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }else{
    $this->load->view('FMS/planorder_list');
            }
     
}
public function order_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.sono,a.pono,a.orderduetodiversion,a.diversionreasonref,a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->order_by('a.order_id','desc')->get();
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
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th><th style='padding:2px 2px 2px 2px;width:10%;'>DIVERTED</th><th style='padding:2px 2px 2px 2px;width:15%;'>REFERENCE</th></tr>";
			$instrumentsss = array();
			
			$query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			foreach($query->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="background-color:#F4F8FB";
				}
				
				 /** check for diversion **/
               
               $rdivestyu=$this->db->select('reference,diverted')->from('order_instruments')->where('id',$instruments->jcardid)->get(); 
               if($rdivestyu->num_rows()>0)
               {
                   foreach($rdivestyu->result() as $erdiv);
                   if($erdiv->diverted=='1')
                   {
                       $div="DIVERTED";
                       $ref=$erdiv->reference;
					   /** GET INSTRUMENT NAME IF NOT SAME **/
						$othermachineid="(".$this->fmsmodel->getrefrencemachinename($ref).")";
                   }else
                   {
                        $div="NA";
                        $ref="";
						$othermachineid="";
                   }
                    }else
                    {
                    $div="";
                    $ref="";
					$othermachineid="";
                    }
               
               
                /** end **/
                
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."<br/>".$othermachineid."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".$div."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".$ref."</td>";
				//	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
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
				$planaction="<a href='".page_url."FMS/planorder/".$row->order_id."'><span class='btn btn-sm btn-success'>PLANNED</span></a>";
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
				if($row->orderduetodiversion=='1')
			{
			$div="YES<br/>"." (".$row->diversionreasonref.")";
			}else
			{
			    $div="NO";
			}
			$internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-success'>I/0 Slip</span></a>";
			
			$po='';
			
			if(file_exists(sfpo.$row->internal_order_no.'.pdf'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".pdf' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			if(file_exists(sfpo.$row->pono.'.PDF'))
			{
			$po="<a href='".page_url."sfpo/".$row->internal_order_no.".PDF' target='_blank'>".$row->internal_order_no."</a>";
			}else
			{
			
			
			}
			}
		
			
			$scheduler_data[] = array('sr_no'=>$i,
									  'generatebutton'=>$internalordersheet,
									  'planorder'=>$planaction,
									  'added_on'=>$addeddate."".$addedtime,
									   'oduetodiversion'=>$div,
									  'order_type'=>strtoupper($row->order_type),
									  'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
									  'po_number'=>strtoupper($row->po_number),
									  'company_name'=>strtoupper($row->company_name),
									  'contactperson'=>strtoupper($row->contact_person),
									  'designation'=>strtoupper($row->company_name),
									
									  'address'=>strtoupper($row->address),
									  'email'=>strtoupper($row->email),
									  'mobile_number'=>strtoupper($row->mobile_number),
									    'phone'=>strtoupper($row->phone),
									  
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
									  'sono'=>$row->sono,
									  'pono'=>$po,
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
//	$this->form_validation->set_rules('email_id', 'email_id', 'required|trim');
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
				  'contact_person'=>strtoupper($this->input->post('contactperson')),
			'designation'=>strtoupper($this->input->post('designation')),
			'phone'=>strtoupper($this->input->post('phone')),
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
			'internal_order_no'=>$this->input->post('internal_order_no'),
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
								
								
								$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
								$res1 = $query111->num_rows();
								$serial = $res1+1;
								$mserial= sprintf("%03d", $serial);
								
									$machserial=date('my').'-'.$mserial;
							
								 $extserial=$this->chkforextraserial($instruments_attruibute[$x]);
                                
                                if($extserial=='1')
                                {
                                    $mserial= sprintf("%03d", $serial+1);
                                    $extramachserial=date('my').'-'.$mserial;
                                }else
                                {
                                    $extramachserial='';
                                }
								
								
								 $data=array('item_id'=>$instruments_attruibute[$x],
							'qty'=>'1',
							'job_card_no'=>$jobcardnumber,
							'mserialno'=>$machserial,
							'extraserialno'=>$extramachserial,
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
		   
		    if($this->input->post('longreport')=='1')
		   {
			   $long=1;
		   }else{
			   $long=0;
		   }
		   
		   
		    if($this->input->post('includemis')=='1')
		   {
			   $mis=1;
		   }else{
			   $mis=0;
		   }
		   
		   
		   $data=
			array('production_flow'=>strtoupper($this->input->post('flow_name')),
			'status'=>$this->input->post('status'),
			'sortorder'=>$this->input->post('flow_order'),
			'parallel'=>$pdms,
			'longreport'=>$long,
			'mis'=>$mis,
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('production_flow',$data);
			if($res)
			{
			    
			    if($long==1)
				{
					
				 $term="FMS ".strtoupper($this->input->post('flow_name'))." REPORTING";
				
					$qye=$this->db->select('id')->from('submodule')->where('submodule',$term)->get();
					
					$isavai=$qye->num_rows();
					if($isavai==0)
					{
						$subdata=array('moduleid'=>'11','submodule'=>$term,'status'=>'1','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('submodule',$subdata);
						
						
					}
					
				}
				
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
			
			$production_pdf =  "<a href='".page_url."fms_flow/".$row->fms_image."' target='_blank'><span>".strtoupper($row->production_flow)."</span></a>";
			
			
			$edit = "<a href='".page_url."FMS/edit_production_flow/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'production_flow'=>$production_pdf,
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
		   
		   if($this->input->post('longreport')=='1')
		   {
			   $long=1;
		   }else{
			   $long=0;
		   }
		   
		   
		    if($this->input->post('includemis')=='1')
		   {
			   $mis=1;
		   }else{
			   $mis=0;
		   }
		   
		   
		   $data=
			array('production_flow'=>strtoupper($this->input->post('flow_name')),
			'sortorder'=>$this->input->post('flow_order'),
			'status'=>$this->input->post('status'),
			'added_by'=>$user_id,
			'parallel'=>$parms,
			'mergefms'=>$merge,
			'longreport'=>$long,
			'mis'=>$mis,
			'added_on'=>$added_time);
			  // echo "<pre>"; print_r($data);exit;
			$this->db->where('id',$this->uri->segment(3));
			$res = $this->db->update('production_flow',$data);
			if($res)
			{
			    
			    if($long==1)
				{
					
				 $term="FMS ".strtoupper($this->input->post('flow_name'))." REPORTING";
				
					$qye=$this->db->select('id')->from('submodule')->where('submodule',$term)->get();
					
					$isavai=$qye->num_rows();
					if($isavai==0)
					{
						$subdata=array('moduleid'=>'11','submodule'=>$term,'status'=>'1','addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('submodule',$subdata);
						
						
					}
					
				}
				
				
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
			$jbforreload=$jbcard[0];
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('jobcard_id'=>$jbcard[$i],'order_id'=>$orderid,'orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'plannedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->insert('order_planning',$data);
				
					/** GET TAT **/
                      	$getset=$this->db->select('finalstep,total_days,set_time')->from('fms_flow')->where('production_flow_id',$factory)->where('flow_id',$planstartsfrom)->get();
			
                            foreach($getset->result() as $ggetset);
                            $isfinalstep=$ggetset->finalstep;
                            $tatday=$ggetset->total_days;
                            $tathours=$ggetset->set_time;
                        
					/** END **/
					
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
					
				
					/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,$isfinalstep,$jbcard[$i],$orderid);
						
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
					
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/
					
					
						/** NOW CHECK IF THIS IS AUTO PR STEP **/
					
						$isthisautopr=$this->fmsmodel->checkifthisstepisautoprstep($planstartsfrom);
						
						if($isthisautopr=='1')
						{
						
						$this->fmsmodel->raiseprforlowitems($jbcard[$i],$planstartsfrom,$factory,$orderid,$tatday,$tathours);
						}
						
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
								
						/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$nextflow,0,$jbcard[$i],$orderid);
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$nextflow,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/

								
							}
							
							
						}
						/** End **/
						
						
						
						
					}else
					{
						$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,0,$jbcard[$i],$orderid);
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/
					}
					
					
					/** CHECK IF FLOW HAS JUMPFAB APPL **/
					$fabricjumpreq=$this->checkiffabricationallowed($planstartsfrom);
					/** END **/
					
					if(($fabricreq=='1') && ($fabricjumpreq=='1'))
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
					/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,0,$jbcard[$i],$orderid);
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/
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
							/** Add Tat **/
							$stageid=$this->db->insert_id();
							$settatdate=$this->fmsmodel->gettatformis($stageid,$nextflow,0,$jbcard[$i],$orderid);
							if($settatdate<>'')
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
							$settat=array('orderstageid'=>$stageid,'flowstage'=>$nextflow,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
							$this->db->insert('fmstatdate',$settat);
							}
							/** END **/

								
							}
							
							
						}
						/** End **/
						
						
					}else
					{
						$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					/** Add Tat **/
					$stageid=$this->db->insert_id();
					$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,0,$jbcard[$i],$orderid);
					if($settatdate<>'')
					{
					$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
					$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'daystat'=>$tatday,'hourtat'=>$tathours);
					$this->db->insert('fmstatdate',$settat);
					}
					/** END **/
					}
					
				}else{  $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Unable to move to Fabrication since flow is not available</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);  }					
						
						
						
					}
					
					
					
				}
				
				/** End **/
				
			}
			
		
			if($factory==1 || $factory==2 || $factory==4 )
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid.'/'.$jbforreload);
			}else{
				
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
			}
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
		$this->db->select('d.mserialno,a.jobcard_id,a.id, a.order_id,a.planstartsfrom, a.orderstatus, a.remarks, a.ordertype, a.factory, a.fileno, a.plannedOn, a.plannedby, b.user_id, b.title, b.first_name, b.last_name, c.id, c.production_flow, d.id, d.item_id, d.qty, d.job_card_no,d.instrument_addedon, e.id, e.instruments_name, f.order_id, f.order_status, g.flow_id, g.fms_flow')->from('order_planning a')->join('system_users b','a.plannedby=b.user_id','left')->join('production_flow c','a.factory=c.id','left')->join('order_instruments d','a.jobcard_id=d.id','left')->join('presto_instruments e','d.item_id=e.id','left')->join('prestogroup_orders f','a.order_id=f.order_id','left')->join('fms_flow g','a.planstartsfrom=g.flow_id','left');
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
			
			
			 if($row->factory==1 || $row->factory==2 || $row->factory==4 || $row->factory==3 )
			 {
				 $jbcard="<a href='".page_url."Reporting/generatejobcard/".$row->jobcard_id."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a>";
			 }else
			 {
				 $jbcard='';
			 }
			 
			 
			$date1 = new DateTime($timestamp_show); 
			$date2 = new DateTime($addeddate); 
			$interval = $date1->diff($date2); 
			 $days = $interval->d; 
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp_show.$timestamptime_show,
			'instruments_name'=>strtoupper($row->instruments_name),
			'jobcard'=>$jbcard,
			'serialno'=>$row->mserialno,
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
	$query1=$this->db->select('item_id')->from('order_instruments')->where('id',$jobcardno)->get();
	if($query1->num_rows()>0)
	{
	    foreach($query1->result() as $query2);
		$query = $this->db->select('id,file_number')->from('presto_instruments')->where('id',$query2->item_id)->get();
		if($query->num_rows()>0)
{
		foreach($query->result() as $fileinfo);
		echo $fileinfo->file_number;
			}else
{
	echo "";
}

}else
{
    echo "";
}
		
		
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
	
	$f=array('1,4,5,8,9');
	$todaysdate=date('Y-m-d');
		$scheduler_data = array();
		$query = $this->db->select('a.production_flow_id,a.actiontobetaken,a.fms_flow,a.tat,a.moveto,a.dependency, a.flow_id as recordid, b.user_id, b.title, b.first_name, b.last_name, b.department_id, c.id, c.production_flow, c.sortorder, d.department_id, d.department')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id','left')->join('production_flow c','a.production_flow_id=c.id','left')->join('departments d','b.department_id=d.department_id','left')->where('a.status','1')->where_in('a.production_flow_id',$f,false)->order_by('c.sortorder','asc')->get();
		$res = $query->result();
		$i=1;
		
	
		foreach($res as $row)
		{
		    
			$todaypending=array();
			$todaypendingorderstageid=array();
			
            /*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,set_time, tat')->from('fms_flow')->where('flow_id',$row->recordid)->where('production_flow_id',$row->production_flow_id)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
		if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
			$tatstage = $fmsinformation->tat;
			/** End **/

		/** Get Pending Count **/
	 $mmer=$this->fmsmodel->checkifmsmerge($row->recordid);
	$skkipl=$this->fmsmodel->checkifstepisskippable($row->recordid);
	
		/** CHECK FOR DEPENDENCY **/
    $checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$row->recordid)->where('dependency','1')->get();
    $depend=$checkdepend->num_rows();
	
	
	 
		if($mmer==0 && $skkipl==0 && $depend==0)
		 {
			$pend=$this->db->select('id as orderstageid,jobcardid')->from('order_stage')->where('userstatus','0')->where('flowstage',$row->recordid)->get();
			$pendcount=$pend->num_rows();
		 }else{
			 
			 $pendcount=$this->fmsmodel->menunotificationsformergeflowoverall($row->recordid,$row->production_flow_id);
			
		 }
		
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
$tatdate=$this->getupcomingtatdatenew($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
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
			$lastup=$this->db->select('max(addedOn) as latestcompdate')->from('order_stage')->where('flowstage',$row->recordid)->where('userstatus','1')->order_by('id','DESC')->limit(1)->get();
			if($lastup->num_rows()>0)
			{
				foreach($lastup->result() as $lastup12);
				
				$lastupdatedOn=date('Y-m-d',strtotime($lastup12->latestcompdate));
				
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
	
	
	
	public function planned_actual_listoldbeforeinprocessfittissue()
	{
	
	$f=array('1,4,5');
	$todaysdate=date('Y-m-d');
		$scheduler_data = array();
		$query = $this->db->select('a.production_flow_id,a.actiontobetaken,a.fms_flow,a.tat,a.moveto,a.dependency, a.flow_id as recordid, b.user_id, b.title, b.first_name, b.last_name, b.department_id, c.id, c.production_flow, c.sortorder, d.department_id, d.department')->from('fms_flow a')->join('system_users b','a.who_wedo=b.user_id','left')->join('production_flow c','a.production_flow_id=c.id','left')->join('departments d','b.department_id=d.department_id','left')->where('a.status','1')->where_in('a.production_flow_id',$f,false)->order_by('c.sortorder','asc')->get();
		$res = $query->result();
		$i=1;
		
	
		foreach($res as $row)
		{
		    
			$todaypending=array();
			$todaypendingorderstageid=array();
			
/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,set_time, tat')->from('fms_flow')->where('flow_id',$row->recordid)->where('production_flow_id',$row->production_flow_id)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
		if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
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
$tatdate=$this->getupcomingtatdatenew($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
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
			$lastup=$this->db->select('max(addedOn) as latestcompdate')->from('order_stage')->where('flowstage',$row->recordid)->where('userstatus','1')->order_by('id','DESC')->limit(1)->get();
			if($lastup->num_rows()>0)
			{
				foreach($lastup->result() as $lastup12);
				
				$lastupdatedOn=date('Y-m-d',strtotime($lastup12->latestcompdate));
				
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
$tatdate=$this->getupcomingtatdatenew($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid);
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
	$odtype=$this->uri->segment(5);
	
	/** CHECK IF DATA ITS FOR MERGED FMS **/
	$mmer=$this->fmsmodel->checkifmsmerge($flowstage);
	$skkipl=$this->fmsmodel->checkifstepisskippable($flowstage);
	
	/** CHECK FOR DEPENDENCY **/
    $checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
    $depend=$checkdepend->num_rows();
	
	if($mmer==0 && $skkipl==0 && $depend==0)
	{
	    
	$this->db->select('a.id as orderstageid,a.jobcardid,a.orderid')->from('order_stage a')->join('prestogroup_orders b','a.orderid=b.order_id')->where('userstatus','0')->where('flowstage',$flowstage);
	if($odtype<>'')
	{

		$this->db->where('b.order_type','SALE');
		$this->db->where('b.selforder','0');
	}

	$pend=$this->db->order_by('b.selforder','ASC')->get();
	$pendcount=$pend->num_rows();
	
	if($pendcount<>'0')
	{		
		$i=1;
		foreach($pend->result() as $proc)
		{
		    
		    /** GET ORDER TYPE **/
		    $odtype=$this->getordertype($proc->orderid);
		    /** END **/
		    	$htmldata='';
			/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
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
		$tatdate=$this->getupcomingtatdatenew($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
		
		$planndate = date('Y-m-d',strtotime($tatdate));
        $todaydate = date('Y-m-d');
        
        
        if($todaydate>$planndate){
        $textcolor="color:red; font-weight:bold;";
        }else if($todaydate=$planndate){
        $textcolor="color:blue; font-weight:bold;";
        }else{
        $textcolor="color:black; font-weight:bold;";
        }
	
		$totdays=$this->gettotaldaysforpend($status,$timestamp,$completedon);
	
	if($flowstage=='8')
	{
	    	$htmldata='';
        /** IF MATERIAL PAINT OUT **/
        $QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
        $res = $QRY->result();
        if($QRY->num_rows()>0){
        foreach($QRY->result() as $outinfo);
        $challanno = $outinfo->challanno;
        $weight = $outinfo->weight;
        $pcs = $outinfo->pcs;
        }else{
        $challanno="";
        $weight="";
        $pcs="";	
        }
        
       
       $QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_in')->where('jobcardid',$proc->jobcardid)->get();
	$m=1;

 $allpcs=array();
 $allweight=array();

	$result = $QRY1->result();
	if($result>0){
	   
	   	$htmldata.= "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	   	
	    	$allpcs[]=0;
	    	$allweight[]=0;
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$allpcs[]=$indata->pcs;
		$allweight[]=$indata->weight;
		$m++;
		}
		
			$htmldata.="</tbody></table>";
	}
	

        
        
	}else if($flowstage=='10')
	{
	    
	 
	 	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs; 
	
	
	$QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_in')->where('jobcardid',$proc->jobcardid)->get();
	$m=1;

 $allpcs=array();
 $allweight=array();

	$result = $QRY1->result();
	if($result>0){
	   
	   	$htmldata.= "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	    	$allpcs[]=0;
	    	$allweight[]=0;
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$allpcs[]=$indata->pcs;
		$allweight[]=$indata->weight;
		$m++;
		}
		
			$htmldata.="</tbody></table>";
	}
	
	
	

	
	
	
	    
	 }else
	{
	   $challanno="";
        $weight="";
        $pcs="";  
	}
	/** END **/
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp,
			'ordertype'=>$odtype,
			'orderno'=>$odno,
			'instrumentname'=>$insname,
			'plandate'=>$tatdate,
			'itemdetail'=>'Weight -'.$weight.'<br/>'.'Pcs -'.$pcs.'<br/>'.'Challan No. -'.$challanno,
			'inwarddetails'=>$htmldata,
			'totaldays'=>"<span style='".$textcolor."'>".$totdays."</span>");
			
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
	else{
	

		
			$flowstage=$this->uri->segment(3);
		
		/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder,production_flow_id')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			
			foreach($res as $fmsinformation);
			$productionflowid=$fmsinformation->production_flow_id;
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
			/** End **/
			
			
			$user_id =$this->session->userdata['logged_in']['user_id'];
			$fmsprocess=$productionflowid;
	
	if($fmsprocess<>0)
	{
		
		/** Get Process Name **/
		$this->db->select('a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('b.userstatus','0');

			if($odtype<>'')
			{
			$this->db->where('e.order_type','SALE');
			$this->db->where('e.selforder','0');
			}
			$process=$this->db->order_by('b.addedOn','DESC')->get();
		/** End **/
		if($process->num_rows()>0)
		{
			//	echo "<pre>"; print_r($process->result());exit;
			$i=1;
			foreach($process->result() as $proc)
		{
		    
		     /** GET ORDER TYPE **/
		    $odtype=$this->getordertype($proc->originalorderid);
		    /** END **/
		    
				if($proc->userstatus=='0')
				{
				$prestui="<span class='btn btn-danger btn-xs'>Pending</span>";
				//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".','."'".$productionflowid."'".','."'".$proc->job_card_no."'".','."'".$proc->instruments_name."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
				$completedate="";
				$completetime="";
				}else{
				$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
				$prestui="<span class='btn btn-success btn-xs'>Machine Started</span>";
				//$action="This Task has been completed by You.";
				date_default_timezone_set("Asia/Kolkata");
				$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
				$time1 = date('H:i:s', strtotime($proc->stagecompletedate));
				$completetime = "<br>". date('g:i A', strtotime($time1));
				}
				
			
				
				
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
			
				if($depend==1)
				{
					/** Check if Dependent flow has any data in orderstage **/
					$availableflowstage=array();
				/** Check if plan has been started from setorder 1 **/
				$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($proc->planstartsfrom);
				if($plannedstageorderno!='1')
				{
					$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$proc->jobcardid)->where('a.flowid',$flowstage)->get();
					if($ifdependexist->num_rows()>0)
					{
						foreach($ifdependexist->result() as $ifdependexist1)
						{
							$availableflowstage[]=$ifdependexist1->dependentflowid;
						}
						
					}
				}else
				{
					$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowstage)->get();
					if($ifdependexist->num_rows()>0)
					{
						foreach($ifdependexist->result() as $ifdependexist1)
						{
							$availableflowstage[]=$ifdependexist1->dependentflowid;
						}
						
					}
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($proc->jobcardid,$flowstage);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
				}
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
					/** Get Last Step of Fabrication **/
					/**if($fabreq==0)
							{
						
								$isffin=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id','5')->where('finalstep','1')->get();
								$isfinal=$isffin->num_rows();
								if($isfinal!=0)
								{
									foreach($isffin->result() as $isffin1);
									$ignoreflow=$isffin1->flow_id;
								}
												
							}else{ $isfinal=0; } **/
					/** End **/
					
				
					
if(count($availableflowstage)>0)
					{	
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($plannedstageorderno!='1')
				{
				$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);
				

					//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

					//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

                    $this->db->where_in('a.dependentflowid',$availableflowstages,false);
					$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);
					
				}else
				{
				     $this->db->select('a.dependentflowid')->from('flowdependency a')->where('a.flowid',$flowstage);
				     $this->db->where_in('a.dependentflowid',$availableflowstages,false);
				}
					
					
					$restt=$this->db->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							
							
							$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid);
						
							$this->db->order_by('id','DESC');
							$this->db->limit(1);
							$isdone=$this->db->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					}else{ $markapp=1; }
					
				}else{
					
					$markapp=1;
				}
				/** End **/
		
				/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
				
				if($markapp==1)
				{
			   $timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
				$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
				$finaltattime=$tattime->format('g:i A');
			   $tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
				
					if($proc->userstatus=='0')
					{
						$completedon="";
					}else
					{
						$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
					}
					
					$planndate = date('Y-m-d',strtotime($tatdate));
        $todaydate = date('Y-m-d');
        
        
        if($todaydate>$planndate){
        $textcolor="color:red; font-weight:bold;";
        }else if($todaydate=$planndate){
        $textcolor="color:blue; font-weight:bold;";
        }else{
        $textcolor="color:black; font-weight:bold;";
        }
					
		$totdays=$this->gettotaldaysforpend($status,$timestamp,$completedon);	
	
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp,
			'ordertype'=>$odtype,
			'orderno'=>$proc->job_card_no,
			'instrumentname'=>$proc->instruments_name,
			'plandate'=>$tatdate,
			'totaldays'=>"<span style='".$textcolor."'>".$totdays."</span>");
				$i++;
				}			
										  
										  
		
		}
		
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
			
		}else
		{
			
			$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
			
		}
		
	}
	}
}


function planned_actual_completelist()
{
	$scheduler_data=array();
	$diff='';
	$diff1='';
	$status=0;
	$completedon=0;
	$flowstage=$this->uri->segment(3);
	$type=$this->uri->segment(4);
	
	$today=date('Y-m-d')." 23:59:59";
	$twentydayago=date('Y-m-d', strtotime('-20 days', strtotime($today)))." 00:00:00";
	/** CHECK IF DATA ITS FOR MERGED FMS **/
	$mmer=$this->fmsmodel->checkifmsmerge($flowstage);
	$skkipl=$this->fmsmodel->checkifstepisskippable($flowstage);
	
	/** CHECK FOR DEPENDENCY **/
    $checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
    $depend=$checkdepend->num_rows();
	
	if($mmer==0 && $skkipl==0 && $depend==0)
	{
	    
	$pend=$this->db->select('id as orderstageid,jobcardid,orderid,addedOn')->from('order_stage')->where('userstatus','1')->where('flowstage',$flowstage)->where('addedOn BETWEEN "'.$twentydayago. '" and "'.$today.'"')->get();
	$pendcount=$pend->num_rows();
	
	
	if($pendcount<>'0')
	{		
		$i=1;
		
		foreach($pend->result() as $proc)
		{
			/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
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
		$tatdate=$this->getupcomingtatdatenew($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
		
		$planndate = date('Y-m-d',strtotime($tatdate));
        $todaydate = date('Y-m-d');
        
        
        if($todaydate>$planndate){
        $textcolor="color:red; font-weight:bold;";
        }else if($todaydate=$planndate){
        $textcolor="color:blue; font-weight:bold;";
        }else{
        $textcolor="color:black; font-weight:bold;";
        }
	
	$itemdetail='';
	$htmldata='';
	$vendordays='';
	    if($flowstage=='7')
	    {
	        	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
	$res = $QRY->result();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}
	
	$itemdetail="Weight- ".$weight.'<br/>'."Pcs- ".$pcs.'<br/>'."Challan no- ".$challanno;
	$diff='';
	 }else if($flowstage=='8')
	 {
	     
	     
	        	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_out')->where('jobcardid',$proc->jobcardid)->get();
	$res = $QRY->result();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}
	
	$itemdetail="Weight- ".$weight.'<br/>'."Pcs- ".$pcs.'<br/>'."Challan no- ".$challanno;
	
	
		$QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_paint_in')->where('jobcardid',$proc->jobcardid)->get();
	$m=1;

 $allpcs=array();
 $allweight=array();
	$htmldata.= "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	$result = $QRY1->result();
	if($result>0){
	   
	    	$allpcs[]=0;
	    	$allweight[]=0;
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$allpcs[]=$indata->pcs;
		$allweight[]=$indata->weight;
		$m++;
		}
	}
	
	
	$htmldata.="</tbody></table>";
	
	if($pcs<>'')
	{
		if($pcs==array_sum($allpcs))
	{
	    $diff="<span style=''>PCS DIFF-0</span>";
	}else
	{
	  $d=$pcs-array_sum($allpcs);
	   $diff="<span style='color:red;font-weight:bold;'>PCS DIFF- ".$d."</span>";
	   
	}
	}else
	{
	    $diff="<span style=''>PCS DIFF-0</span>";
	}
	
	
		if($weight<>'')
	{
		if(array_sum($allweight)>=$weight)
	{
	    $diff1="<span style=''>WEIGHT DIFF-0</span>";
	}else
	{
	   $d=array_sum($allweight)-$weight;
	   
	   $diff1="<span style='color:red;font-weight:bold;'>WEIGHT DIFF".$d."</span>";
	   
	}
	}else
	{
	    $diff1="<span style=''>WEIGHT DIFF 0</span>";
	}
	 
	 /** time taken by vendor **/
	 $start=date('Y-m-d',strtotime($timestamp));
	 $endti=date('Y-m-d',strtotime($tatdate));
	 $vendordays=$this->getdaysdiff($start,$endti);
	
	 /** end **/
	
	 }else if($flowstage=='9')
	 {
	     
	     	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
	$res = $QRY->result();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}
	  
	  	$itemdetail="Weight- ".$weight.'<br/>'."Pcs- ".$pcs.'<br/>'."Challan no- ".$challanno; 
	  	$diff='';
	 }else if ($flowstage=='10')
	 {
	     
	     	$QRY = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_out')->where('jobcardid',$proc->jobcardid)->get();
	if($QRY->num_rows()>0){
	foreach($QRY->result() as $outinfo);
	$challanno = $outinfo->challanno;
	$weight = $outinfo->weight;
	$pcs = $outinfo->pcs;
	}else{
	$challanno="";
	$weight="";
	$pcs="";	
	}
	     
	     
	     
	 $itemdetail="Weight- ".$weight.'<br/>'."Pcs- ".$pcs.'<br/>'."Challan no- ".$challanno;
	 
	  $allpcs=array();
	   $allweight=array();
	 	$QRY1 = $this->db->select('jobcardid, challanno, weight, pcs')->from('material_plating_in')->where('jobcardid',$proc->jobcardid)->get();
	 	$allpcs[]=0;
	 	$allweight[]=0;
	$m=1;
	$htmldata .= "<table border='1' style='width:200px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center'>WEIGHT IN KG</th><th style='padding:2px 2px 2px 2px; text-align:center'>PCS</th></tr><tbody>";
	$result = $QRY1->result();
	if($result>0){
		foreach($result as $indata){
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->weight."</td><td style='padding:2px 2px 2px 2px; text-align:center'>".$indata->pcs."</td></tr>";
		$allpcs[]=$indata->pcs;
		$allweight[]=$indata->weight;
		$m++;
		}
	}
	$htmldata.="</tbody></table>";
	
	if($pcs<>'')
	{
		if($pcs==array_sum($allpcs))
	{
	    $diff="<span style=''>PCS DIFF-0</span>";
	}else
	{
	  $d=$pcs-array_sum($allpcs);
	   $diff="<span style='color:red;font-weight:bold;'>PCS DIFF- ".$d."</span>";
	   
	}
	}else
	{
	    $diff="<span style=''>PCS DIFF-0</span>";
	}
	
	
		if($weight<>'')
	{
		if(array_sum($allweight)>=$weight)
	{
	    $diff1="<span style=''>WEIGHT DIFF-0</span>";
	}else
	{
	   $d=array_sum($allweight)-$weight;
	   
	   $diff1="<span style='color:red;font-weight:bold;'>WEIGHT DIFF".$d."</span>";
	   
	}
	}else
	{
	    $diff1="<span style=''>WEIGHT DIFF 0</span>";
	}
	 
	
		 /** time taken by vendor **/
	 $start=date('Y-m-d',strtotime($timestamp));
	 $endti=date('Y-m-d',strtotime($tatdate));
	 $vendordays=$this->getdaysdiff($start,$endti);
	
	 /** end **/
	     
	 }
		$totdays=$this->gettotaldaysforpend($status,$timestamp,$completedon);
	    
	    $actualdate=date('d-M-y g:i A',strtotime($proc->addedOn));
		
		$comdate=date('Y-m-d',strtotime($proc->addedOn));
		$plndate=date('Y-m-d',strtotime($tatdate));
			/** DIFF BETWEEN Actual & Planned **/
			$date1=new DateTime($comdate);
			$date2=new DateTime($plndate);
			$diff1233= $date1->diff($date2);
			$dif=$diff1233->days;
			/** END**/
			if($comdate>$plndate)
			{
				$diffre="<span style='color:red;'>".$dif."</span>";
			}else{
				$diffre="<span style='color:green;'>0</span>";
			}
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp,
			'orderno'=>$odno,
			'instrumentname'=>$insname, 
			'plandate'=>$tatdate,
			'completed'=>$actualdate,
			'donediff'=>$diffre,
			'totaldays'=>"<span style='".$textcolor."'>".$totdays."</span>",
			'itemdetails'=>$itemdetail,
			'outitemdetails'=>$htmldata,
			'diff'=>$diff.'<br/>'.$diff1,
			'vendortime'=>$vendordays);
			
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
	else{
	

		
			$flowstage=$this->uri->segment(3);
		
		/*GET CURRENT PROCESS SORT ORDER*/
			$QRY = $this->db->select('total_days,tat,set_time, setorder,production_flow_id')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			
			foreach($res as $fmsinformation);
			$productionflowid=$fmsinformation->production_flow_id;
			if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
			$tatstage = $fmsinformation->tat;
			$currentprocessorder = $flowstage;
		$setorder=$fmsinformation->setorder;
			/** End **/
			
			
			$user_id =$this->session->userdata['logged_in']['user_id'];
			$fmsprocess=$productionflowid;
	
	if($fmsprocess<>0)
	{
		
		/** Get Process Name **/
		$process=$this->db->select('a.jumpfabricationappl,a.fms_flow,a.flow_id,b.id as orderstageid,b.flowstage,b.addedOn as stagecompletedate,b.orderid as originalorderid,b.jobcardid,c.item_id,c.qty,c.job_card_no,d.instruments_name,d.id as instrumentid,e.added_on,e.internal_order_no,f.plannedOn,b.userstatus,g.production_flow,d.fabrication,f.planstartsfrom')->from('fms_flow a')->join('order_stage b','a.flow_id=b.flowstage')->join('order_instruments c','b.jobcardid=c.id','left')->join('presto_instruments d','c.item_id=d.id','left')->join('prestogroup_orders e','b.orderid=e.order_id','left')->join('order_planning f','b.jobcardid=f.jobcard_id')->join('production_flow g','f.factory=g.id')->where('a.production_flow_id',$fmsprocess)->where('b.flowstage',$flowstage)->where('b.userstatus','1')->where('b.addedOn BETWEEN "'.$twentydayago.'" and "'.$today.'"')->order_by('b.addedOn','DESC')->get();
		
		/** End **/
		if($process->num_rows()>0)
		{
			//	echo "<pre>"; print_r($process->result());exit;
			$i=1;
			foreach($process->result() as $proc)
		{
		    
		    
				if($proc->userstatus=='0')
				{
				$prestui="<span class='btn btn-danger btn-xs'>Pending</span>";
				//$action='<a href="javascript:;" onclick="markstagedone('."'".$proc->orderstageid."'".','."'".$proc->flowstage."'".','."'".$proc->originalorderid."'".','."'".$proc->jobcardid."'".','."'".$productionflowid."'".','."'".$proc->job_card_no."'".','."'".$proc->instruments_name."'".')"><span class="btn btn-xs btn-warning">Mark as done</span></a>';
				$completedate="";
				$completetime="";
				}else{
				$compdate=date('d-M-Y H:i:s',strtotime($proc->stagecompletedate));
				$prestui="<span class='btn btn-success btn-xs'>Machine Started</span>";
				//$action="This Task has been completed by You.";
				date_default_timezone_set("Asia/Kolkata");
				$completedate = date('d-M-Y', strtotime($proc->stagecompletedate));
				$time1 = date('H:i:s', strtotime($proc->stagecompletedate));
				$completetime = "<br>". date('g:i A', strtotime($time1));
				}
				
			
				
				
				
				/** Check for dependency **/
				
				$checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$flowstage)->where('dependency','1')->get();
				$depend=$checkdepend->num_rows();
			
				if($depend==1)
				{
					/** Check if Dependent flow has any data in orderstage **/
					$availableflowstage=array();
				/** Check if plan has been started from setorder 1 **/
				$plannedstageorderno=$this->fmsmodel->getplanstartsfromorderno($proc->planstartsfrom);
				if($plannedstageorderno!='1')
				{
					$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('b.jobcardid',$proc->jobcardid)->where('a.flowid',$flowstage)->get();
					if($ifdependexist->num_rows()>0)
					{
						foreach($ifdependexist->result() as $ifdependexist1)
						{
							$availableflowstage[]=$ifdependexist1->dependentflowid;
						}
						
					}
				}else
				{
					$ifdependexist=$this->db->select("a.dependentflowid")->from('flowdependency a')->where('a.flowid',$flowstage)->get();
					if($ifdependexist->num_rows()>0)
					{
						foreach($ifdependexist->result() as $ifdependexist1)
						{
							$availableflowstage[]=$ifdependexist1->dependentflowid;
						}
						
					}
					
					$checkskiiped=$this->fmsmodel->checkifdatahascomeskipped($proc->jobcardid,$flowstage);
					
					if(count($checkskiiped)>0)
					{
						$availableflowstage = array_diff($availableflowstage,$checkskiiped);
					}
					
				}
					
					/** End **/
					
					/** Check if instrument requires fabrication **/
					/**$ryt=$this->db->select('b.fabrication')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$proc->jobcardid)->get();
					if($ryt->num_rows()>0)
					{
						foreach($ryt->result() as $rtyeyte);
						$fabreq=$rtyeyte->fabrication;
						
					}else{ $fabreq=0 ;} **/
					
					/** Get Last Step of Fabrication **/
					/**if($fabreq==0)
							{
						
								$isffin=$this->db->select('flow_id')->from('fms_flow')->where('production_flow_id','5')->where('finalstep','1')->get();
								$isfinal=$isffin->num_rows();
								if($isfinal!=0)
								{
									foreach($isffin->result() as $isffin1);
									$ignoreflow=$isffin1->flow_id;
								}
												
							}else{ $isfinal=0; } **/
					/** End **/
					
				
					
if(count($availableflowstage)>0)
					{	
				$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";
					if($plannedstageorderno!='1')
				{
				$this->db->select('a.dependentflowid')->from('flowdependency a')->join('order_stage b','a.dependentflowid=b.flowstage')->where('a.flowid',$flowstage);
				

					//$availableflowstages = "'" . implode ( "', '", $availableflowstage ) . "'";

					//$this->db->where_in('a.dependentflowid',$availableflowstages,false);

                    $this->db->where_in('a.dependentflowid',$availableflowstages,false);
					$this->db->where('b.jobcardid',$proc->jobcardid)->where('b.orderid',$proc->originalorderid);
					
				}else
				{
				     $this->db->select('a.dependentflowid')->from('flowdependency a')->where('a.flowid',$flowstage);
				     $this->db->where_in('a.dependentflowid',$availableflowstages,false);
				}
					
					
					$restt=$this->db->get();
					if($restt->num_rows()>0)
					{
						$dependcount=$restt->num_rows();
						$donarr=array();
						$donarr[]=0;
						//echo "<pre>";print_r($restt->result());exit;
						foreach($restt->result() as $resttyu)
						{
							
							
							$this->db->select('id,userstatus')->from('order_stage')->where('flowstage',$resttyu->dependentflowid)->where('jobcardid',$proc->jobcardid)->where('orderid',$proc->originalorderid);
						
							$this->db->order_by('id','DESC');
							$this->db->limit(1);
							$isdone=$this->db->get();
							if($isdone->num_rows()>0)
							{
								foreach($isdone->result() as $isdone11);
								$donarr[]=$isdone11->userstatus;
								
							}else
							{
								$markapp=1;
							}
							
						}
						
						if($dependcount==array_sum($donarr))
						{
							$markapp=1;
						}else{
							$markapp=0;
						}
						
					}else{
						$markapp=1;
					}
					}else{ $markapp=1; }
					
				}else{
					
					$markapp=1;
				}
				/** End **/
		
				/** Check for merge fms intersection **/
				if($markapp==1)
				{
					
				$markapp=$this->fmsmodel->checkformergerpoint($flowstage,$proc->jobcardid,$proc->originalorderid,$proc->fabrication,$proc->jumpfabricationappl);
				}
				

				
				/** End **/
				
				if($markapp==1)
				{
			   $timestamp=$this->getprevioustimestamp($tatstage,$proc->plannedOn,$proc->jobcardid,$flowstage,$proc->orderstageid);
				$tattime = DateTime::createFromFormat('d-M-Y g:i A', $timestamp);
				$finaltattime=$tattime->format('g:i A');
			   $tatdate=$this->getupcomingtatdatenew($tatstage,$proc->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
				
					if($proc->userstatus=='0')
					{
						$completedon="";
					}else
					{
						$completedon=date('d-M-Y g:i A',strtotime($proc->stagecompletedate));
					}
					
					$planndate = date('Y-m-d',strtotime($tatdate));
					 $actualdate=date('d-M-y g:i A',strtotime($proc->stagecompletedate));
        $todaydate = date('Y-m-d');
        
        
        if($todaydate>$planndate){
        $textcolor="color:red; font-weight:bold;";
        }else if($todaydate=$planndate){
        $textcolor="color:blue; font-weight:bold;";
        }else{
        $textcolor="color:black; font-weight:bold;";
        }
		
			$comdate=date('Y-m-d',strtotime($proc->stagecompletedate));
		$plndate=date('Y-m-d',strtotime($tatdate));
			/** DIFF BETWEEN Actual & Planned **/
			$date1=new DateTime($comdate);
			$date2=new DateTime($plndate);
			$diff1233= $date1->diff($date2);
			$dif=$diff1233->days;
			/** END**/
			if($comdate>$plndate)
			{
				$diffre="<span style='color:red;'>".$dif."</span>";
			}else{
				$diffre="<span style='color:green;'>0</span>";
			}
			
			
		$totdays=$this->gettotaldaysforpend($status,$timestamp,$completedon);	
	
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp,
			'orderno'=>$proc->job_card_no,
			'instrumentname'=>$proc->instruments_name,
			'completed'=>$actualdate,
			'donediff'=>$diffre,
			'plandate'=>$tatdate,
			'totaldays'=>"<span style='".$textcolor."'>".$totdays."</span>");
				$i++;
				}			
										  
										  
		
		}
		
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
			
		}else
		{
			
			$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
			
		}
		
	}
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
			$QRY = $this->db->select('total_days,set_time,tat')->from('fms_flow')->where('flow_id',$flowstage)->get();
			$res = $QRY->result();
			foreach($res as $fmsinformation);
		if($fmsinformation->set_time<>0)
			{
			$totaldaysslave = $fmsinformation->set_time;
			$day=0;
			}else{
				$totaldaysslave = $fmsinformation->total_days;
				$day=1;
			}
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
		$tatdate=$this->getupcomingtatdatenew($tatstage,$odst1->plannedOn,$proc->jobcardid,$timestamp,$totaldaysslave,$proc->orderstageid,$day);
		
	$planndate = date('Y-m-d',strtotime($tatdate));
        $todaydate = date('Y-m-d');
        
        
        if($todaydate>$planndate){
        $textcolor="color:red; font-weight:bold;";
        }else if($todaydate=$planndate){
        $textcolor="color:blue; font-weight:bold;";
        }else{
        $textcolor="color:black; font-weight:bold;";
        }
		
	
	
		$totdays=$this->gettotaldays($status,$timestamp,$completedon);		
			
			$scheduler_data[] = array('sr_no'=>$i,
			'timestamp'=>$timestamp,
			'orderno'=>$odno,
			'instrumentname'=>$insname,
			'plandate'=>"<span style='".$textcolor."'>".$tatdate.' '.$finaltattime."</span>",
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
					  /** MOVE TO DISPATCH **/
					  
					$rty=$this->db->select('a.stock,b.item_id,a.minstock')->from('presto_instruments a')->join('order_instruments
 b','a.id=b.item_id')->where('b.id',$jbcard[$i])->get();
					if($rty->num_rows()>0)
					{
						foreach($rty->result() as $rty1);
						$instrumentid=$rty1->item_id;
						$currstock=$rty1->stock;
						$minstock=$rty1->minstock;
						
						$selforder=$this->checkifselforder($orderid);
						
						if($selforder==0)
						{
						$newstock=$currstock-1;
						//echo $newstock;exit;
						$datanew=array('stock'=>$newstock);
						$this->db->where('id',$instrumentid);
						$this->db->update('presto_instruments',$datanew);
						}
					}}
					
					$datacompqli=array('complete'=>'1','packed'=>'1');
					$this->db->where('id',$jbcard[$i]);
					$this->db->update('order_instruments',$datacompqli);


					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);
					/** END **/



					
			    /**$closeo=array('movetodispatch'=>'1','movedOn'=>date('Y-m-d H:i:s'),'movedby'=>$_SESSSION['logged_in']['user_id']);
					$this->db->where('order_id',$orderid);
					$this->db->update('prestogroup_orders',$closeo); **/
					
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
	
	
	function fmsmis()
	{
		
		$this->load->view('FMS/fmsmis');
		
	}
	
	function nonfmsmis()
	{
	    	$this->load->view('FMS/nonfmsmis');
	    
	}

	
	
	function jumpjobcard()
	{
		
		$oldflowstage=array();
		$oldorderstageid=array();
		$jobcardid=$this->uri->segment(3);
		$productionflowid=$this->input->post('production_flow_id');
		$orderid=$this->input->post('orderid');
		$fms=$this->input->post('fms');
		$currflow=$this->db->select('a.id,a.flowstage')->from('order_stage a')->join('fms_flow b','a.flowstage=b.flow_id')->where('a.jobcardid',$jobcardid)->where('a.userstatus','0')->where('b.production_flow_id',$productionflowid)->get();
		if($currflow->num_rows()>0)
		{
			foreach($currflow->result() as $currflow1)
			{
				$oldflowstage[]=$currflow1->flowstage;
				$oldorderstageid[]=$currflow1->id;
				
			}
			
				$alloldflowstage = "'" . implode ( "', '", $oldflowstage ) . "'";
				
			
				$upd=array('userstatus'=>'1','addedOn'=>date('Y-m-d H:i:s'));
				$this->db->where_in('flowstage',$alloldflowstage,false);
				$this->db->where('jobcardid',$jobcardid);
				$this->db->where('orderid',$orderid);
				$this->db->update('order_stage',$upd);
				
				$neo=array('orderid'=>$orderid,'jobcardid'=>$jobcardid,'flowstage'=>$fms,'userstatus'=>'0','remarks'=>'','addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('order_stage',$neo);
				$lid=$this->db->insert_id();
				for($i=0;$i<count($oldflowstage); $i++)
				{
					
					$data=array('neworderstageid'=>$oldorderstageid[$i],'jobcardid'=>$jobcardid,'jumpfrom'=>$oldflowstage[$i],'jumpto'=>$fms,'addedOn'=>date('Y-m-d H:i:s'));
					$this->db->insert('jumpjobcard',$data);
					
				}
				
				$this->session->set_flashdata('message','Jobcard Jumped');
				redirect(page_url.'FMS/planorder/'.$orderid);
		
		}else{
			
			
			$this->session->set_flashdata('message','NO Previous Data Available');
				redirect(page_url.'FMS/planorder/'.$orderid);
		}
		
		
	}

function notplannedorders()
{
	
	$this->load->view('FMS/notrecievedorders');
}

function nonrecievedorder()
	{
		$scheduler_data=array();
		$odplan=$this->db->select('a.id,a.remarks,a.revertremarks,a.addedOn,b.po_number,b.company_name,c.job_card_no,d.instruments_name,d.model_number,e.first_name,e.last_name')->from('order_planning_remarks a')->join('prestogroup_orders b','a.orderid=b.order_id')->join('order_instruments c','a.jobcardid=c.id')->join('presto_instruments d','c.item_id=d.id')->join('system_users e','a.addedBy=e.user_id')->order_by('a.status','ASC')->order_by('a.addedOn','DESC')->get();
		if($odplan->num_rows()>0)
		{
			$i=1;
			foreach($odplan->result() as $oldplan1)
			{
				
					$ods="<span class='btn btn-danger btn-xs'>ORDER NOT CLEAR</span>";
				
				
		$udremarks='';		
	

if($oldplan1->revertremarks<>'')
{
$udremarks.=$oldplan1->revertremarks.'<br/> Updated On <BR/>'.date('d-M-Y H:i',strtotime($oldplan1->addedOn));
$udremarks1='';
}else{
$udremarks1='<span class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal'.$oldplan1->id.'">Update Remarks</span>';
}

$udremarks1.='<div id="myModal'.$oldplan1->id.'" class="modal fade" role="dialog">

  <div class="modal-dialog">
<form name="frm" action="'.page_url.'FMS/addplanremarks/'.$oldplan1->id.'" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">'.$oldplan1->instruments_name.'-'. $oldplan1->job_card_no.'</h4>
      </div>
      <div class="modal-body">
        <p><textarea name="remarks'.$oldplan1->id.'" class="form-control" style="width:100%" placeholder="Add Remarks" required></textarea></p>
		<p><input type="submit" name="" class="btn btn-success"></p>
      </div>
	  </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
 
</div>';

			$scheduler_data[] = array('sr_no'=>$i,
			'jobcard'=>$oldplan1->job_card_no,
			'company_name'=>$oldplan1->company_name,
			'mname'=>$oldplan1->instruments_name,
			'addedBy'=>$oldplan1->first_name.' '.$oldplan1->last_name,
			'orderstatus'=>$ods,
			'recvdremarks'=>$oldplan1->remarks,
			'remarks'=>$udremarks,
			'addremarks'=>$udremarks1);
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




function addplanremarks()
{
	$planid=$this->uri->segment(3);
	$remarks=$this->input->post('remarks'.$planid);
	$data=array('status'=>'1','revertremarks'=>$remarks,'revertby'=>$_SESSION['logged_in']['user_id'],'reverton'=>date('Y-m-d H:i:s'));
	$this->db->where('id',$planid);
	$this->db->update('order_planning_remarks',$data);
	$this->session->set_flashdata('message','Remarks Updated');
	redirect(page_url.'FMS/notplannedorders');
	
}


function orderremarks()
{
	
	$this->load->view('FMS/notrecievedorderswithremarks');
	
	
}


function remarksrecievedorder()
	{
		$scheduler_data=array();
		//$odplan=$this->db->select('b.po_number,a.orderstatus1,b.company_name,a.id,c.job_card_no,d.instruments_name,d.model_number,e.first_name,e.last_name,a.remarks')->from('order_planning a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_instruments c','a.jobcard_id=c.id')->join('presto_instruments d','c.item_id=d.id')->join('system_users e','a.plannedby=e.user_id')->join('order_planning_remarksf','a.id=f.planid')->where_in('a.orderstatus','2,3',false)->where('f.addedBy',$_SESSION['logged_in']['user_id'])->get();
		
		$odplan=$this->db->select('a.remarks as recvdremarks,e.jobcard_id,b.po_number,b.company_name,e.id as planid,e.orderstatus,d.instruments_name,d.model_number,c.job_card_no,e.order_id')->from('order_planning_remarks
 a')->join('order_planning e','a.planid=e.id')->join('prestogroup_orders b','e.order_id=b.order_id')->join('order_instruments c','e.jobcard_id=c.id')->join('presto_instruments d','c.item_id=d.id')->where_in('e.orderstatus','2,3',false)->where('e.plannedby',$_SESSION['logged_in']['user_id'])->order_by('a.id','DESC')->limit(1)->get();
 
		if($odplan->num_rows()>0)
		{
			$i=1;
			foreach($odplan->result() as $oldplan1)
			{
				if($oldplan1->orderstatus=='2')
				{
					$ods="<span class='btn btn-danger btn-xs'>NOT RECIEVED</span>";
				}else{
					$ods="<span class='btn btn-warning btn-xs'>NOT CLEAR</span>";
				}
		$udremarks='<a href="'.page_url.'FMS/replanorder/'.$oldplan1->order_id.'/'.$oldplan1->jobcard_id.'/'.$oldplan1->planid.'"><span class="btn btn-warning">Replan Order</span>';		
	
			$scheduler_data[] = array('sr_no'=>$i,
			'jobcard'=>$oldplan1->job_card_no,
			'company_name'=>$oldplan1->company_name,
			'mname'=>$oldplan1->instruments_name,
			'orderstatus'=>$ods,
			'recvdremarks'=>$oldplan1->recvdremarks,
			'remarks'=>$udremarks);
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
	
	function replanorder()
	{
		
		$this->load->view('FMS/replan_order');
		
	}


function orderreplanstepone()
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
			$planid=$this->input->post('planid');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->where('id',$planid);
				$this->db->where('jobcard_id',$jbcard[$i]);
				$this->db->where('order_id',$orderid);
				$this->db->update('order_planning',$data);
				
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
					/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,0,$jbcard[$i],$orderid);
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/
					
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
								
						/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$nextflow,0,$jbcard[$i],$orderid);
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$nextflow,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/

								
							}
							
							
						}
						/** End **/
						
						
					}else
					{
						$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,0,$jbcard[$i],$orderid);
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/
					}
					
					/** CHECK IF FLOW HAS JUMPFAB APPL **/
					$fabricjumpreq=$this->checkiffabricationallowed($planstartsfrom);
					echo $fabricjumpreq;exit;
					/** END **/
					
					if(($fabricreq=='1') && ($fabricjumpreq=='1'))
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
					/** Add Tat **/
						$stageid=$this->db->insert_id();
						$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,0,$jbcard[$i],$orderid);
						if($settatdate<>'')
						{
						$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
						$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('fmstatdate',$settat);
						}
						/** END **/
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
							/** Add Tat **/
							$stageid=$this->db->insert_id();
							$settatdate=$this->fmsmodel->gettatformis($stageid,$nextflow,0,$jbcard[$i],$orderid);
							if($settatdate<>'')
							{
							$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
							$settat=array('orderstageid'=>$stageid,'flowstage'=>$nextflow,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('fmstatdate',$settat);
							}
							/** END **/

								
							}
							
							
						}
						/** End **/
						
						
					}else
					{
						$stage=array('orderid'=>$orderid,'jobcardid'=>$jbcard[$i],'flowstage'=>$planstartsfrom);
					$this->db->insert('order_stage',$stage);
					/** Add Tat **/
					$stageid=$this->db->insert_id();
					$settatdate=$this->fmsmodel->gettatformis($stageid,$planstartsfrom,0,$jbcard[$i],$orderid);
					if($settatdate<>'')
					{
					$returnedtatdate=date('Y-m-d H:i:s',strtotime($settatdate));
					$settat=array('orderstageid'=>$stageid,'flowstage'=>$planstartsfrom,'jobcardid'=>$jbcard[$i],'tasktat'=>$returnedtatdate,'addedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'));
					$this->db->insert('fmstatdate',$settat);
					}
					/** END **/
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
	
	
	function orderreplanstepthree()
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
			$planid=$this->input->post('planid');
			//$fabricreq=$this->input->post('fabricationreq');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->where('id',$planid);
				$this->db->where('jobcard_id',$jbcard[$i]);
				$this->db->where('order_id',$orderid);
				$this->db->update('order_planning',$data);
				$lid=$planid;
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
	
	
	
	function orderreplansteptwo()
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
			$planid=$this->input->post('planid');
			//$fabricreq=$this->input->post('fabricationreq');
			
			if(count($jbcard)>0)
			{
			for($i=0;$i<count($jbcard);$i++)
			{
				$data=array('orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
				//echo "<pre>"; print_r($data);exit;
				$this->db->where('id',$planid);
				$this->db->where('jobcard_id',$jbcard[$i]);
				$this->db->where('order_id',$orderid);
				$this->db->update('order_planning',$data);
				$lid=$planid;
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
	

	
	function checkiffabricationallowed($flowid)
	{
		
		$fflows=$this->db->select('flow_id')->from('fms_flow')->where('jumpfabricationappl','1')->where('flow_id',$flowid)->get();
		return $fflows->num_rows();
		
		
	}
	
public function fms_fabricationreporting()
	{
	$this->load->view('FMS/fabrication_fms_reporting');
		
	}
	
	
	public function fms_korraxreporting()
	{
	$this->load->view('FMS/korrax_fms_reporting');
		
	}
public function dynamic_form_planned_actual_list()
	{
	
	$todaysdate=date('Y-m-d');
		$scheduler_data = array();
		$query = $this->db->select('a.id as recordid, a.dashboard_title, a.description , a.action_to_be_taken,a.status, b.first_name,b.last_name, b.title, c.department_id, c.department')->from('dynamic_forms a')->join('system_users b','a.user_id=b.user_id','left')->join('departments c','a.department_id=c.department_id','left')->where('a.form_running_status','0')->order_by('a.dashboard_title','asc')->get();
		$res = $query->result();
		$i=1;
		
	
		foreach($res as $row)
		{	
			$todaypending=array();
			$todaypendingorderstageid=array();
			$remarks=' <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$i.'">UPDATE REMARKS</button>';
			$remarks.= '<div id="con-close-modal'.$i.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
           <form id="loginForm" method="post" action="'.page_url.'FMS/update_form_followup/'.$row->recordid.'">
  
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">UPDATE '.strtoupper($row->dashboard_title).' FOLLOW-UP REMARKS</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                               
												 <div class="col-md-12">
                                                    <div class="form-group">
                                                        
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
			
			
			$qry = $this->db->select('a.id, a.form_id, a.remarks, a.added_on, a.added_by, b.user_id, b.title, b.first_name, b.last_name')->from('dynamic_form_task_followup_detail a')->join('system_users b','a.added_by=b.user_id','left')->where('a.form_id',$row->recordid)->limit(1)->order_by('a.id','desc')->get();
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
			$todaysdate = date('Y-m-d');
			$time = "23:59:59";
			$finaldate = $todaysdate." ".$time;
			$starttime= date('Y-m-d')." 00:00:00";
			$q = $this->db->select('id, planned_date')->from('dynamic_form_data')->where('form_id',$row->recordid)->where('work_status','0')->where('planned_date<=',$finaldate)->get();
			$totalpending = count($q->result());
			$q1 = $this->db->select('id, planned_date')->from('dynamic_form_data')->where('form_id',$row->recordid)->where('work_status','0')->where('planned_date BETWEEN "'.$starttime. '" and "'.$finaldate.'"')->get();
			$todayspending = count($q1->result());
			
			$todayspendingtask = "<a href='".page_url."Form/data_report/".$row->recordid."'>".$todayspending."</a>";
			$totalspendingtask = "<a href='".page_url."Form/data_report/".$row->recordid."'>".$totalpending."</a>";
			
			/** Calculate days**/
			$q2 = $this->db->select('id, task_complition_time')->from('dynamic_form_data')->where('form_id',$row->recordid)->where('work_status','1')->order_by('task_complition_time','desc')->limit(1)->get(); 
			if($q2->num_rows()>0){
			foreach($q2->result() as $rowss);
			
			$last_completedon =  date('Y-m-d',strtotime($rowss->task_complition_time));
			} else{
				$last_completedon = date('Y-m-d');
			}
			$date1 = $last_completedon;
			$date2 = date('Y-m-d');

			$diff = abs(strtotime($date2) - strtotime($date1));

			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			
			/** Calculate days**/
				
			
			$auditor_remarks = "";
			$scheduler_data[] = array('sr_no'=>$i,
			'dashboard_title'=>strtoupper($row->dashboard_title),
			'assigned_to'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
			'department'=>strtoupper($row->department),
			'pending_from'=>$days." Days ago",
			'totalpending'=>$totalspendingtask,
			'todayspending'=>$todayspendingtask,
			'action_to_be_taken'=>$row->action_to_be_taken,
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

public function update_form_followup(){
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		   $fms_id = $this->uri->segment(3);
		   $data=
			array('form_id'=>$fms_id,
			'remarks'=>strtoupper($this->input->post('fms_remarks')),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('dynamic_form_task_followup_detail',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'FMS/planned_actual');
				}
		   
			
		
}

function setdate()
{
	
	$start=base64_encode(date('Y-m-d',strtotime($this->input->post('start'))));
	$end=base64_encode(date('Y-m-d',strtotime($this->input->post('end'))));

	redirect(page_url.'FMS/fmsmis/'.$start.'/'.$end);
	
}

function setdatefornonfms()
{
	
	$start=base64_encode(date('Y-m-d',strtotime($this->input->post('start'))));
	$end=base64_encode(date('Y-m-d',strtotime($this->input->post('end'))));

	redirect(page_url.'FMS/nonfmsmis/'.$start.'/'.$end);
	
}


function machinecostprice()
	{
		
		$this->load->view('FMS/machinecp');
	}
	
	function listmachinecostprice()
	{
		$scheduler_data = array();
		$resty=$this->db->select('*')->from('machinecp')->get();
		if($resty->num_rows()>0)
		{
	
		foreach($resty->result() as $resty1);
		
		$edit = "<a href='".page_url."FMS/edit_machinecp/".$resty1->id."'><i class='fa fa-pencil'></i></a>";
		$scheduler_data[] = array('sr_no'=>'1',
			'cp'=>strtoupper(floatval($resty1->cp))." %",
			'addedOn'=>date('d-M-Y g:i A',strtotime($resty1->addedOn)),
			'edit'=>$edit);
			
		}
		
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($scheduler_data),
		"iTotalDisplayRecords" => count($scheduler_data),
		"aaData"=>$scheduler_data);
		echo json_encode($results);
		
		
	}
	
	function edit_machinecp()
	{
		
		$this->load->view('FMS/edit_machine_cp');
	}
	
	function update_cp()
	{
		$id=$this->uri->segment(3);
		
		$data=array('cp'=>$this->input->post('cp'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		
		$this->db->where('id',$id);
		$this->db->update('machinecp',$data);
		
		$this->session->set_flashdata('message','Record Updated');
		redirect(page_url.'FMS/machinecostprice');
		
	}



function partsdetail()
{
	$mid=$this->uri->segment('3');
	$html='';
	$resty=$this->db->select('a.fincode,a.specification,a.id,a.picture,a.part,a.unit,a.current_stock,b.qty,b.partid')->from('machine_parts_with_picture a')->join('machine_bom b','a.id=b.partid')->where('b.mid',$mid)->group_by('b.partid')->order_by('a.current_stock','ASC')->get();
if($resty->num_rows()>0)
{
	foreach($resty->result() as $restyu1)
	{
		$uname=$this->storemodel->getunit($restyu1->unit);
		
		/** Get Available Stock **/
		$restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$restyu1->partid)->where('active','1')->get();
		foreach($restblockedst->result() as $restblockedstock);
		$blockedparts=$restblockedstock->blockstock;
		/** END **/
		if($restyu1->current_stock==0)
		{
		$finalstock=0;	
		}else{
		$finalstock=$restyu1->current_stock-$blockedparts;
		}
		if(trim($finalstock)==0)
		{
			$a="checked";
		}else{
			
			$a="";
		}
		

	if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.jpg')){
			    
			 $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
			}else if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.JPG')){
			    
			 $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
			}else if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.jpeg')){
			    $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.JPEG')){
			    
			 $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
			}else{
			 $path=page_url.'upload/image404.png'; 
			}
			
			

		
		//$qtytoraise=floatval($restyu1->bomqty);
			$html.='<tr>
			<td><input type="checkbox" class="partselection" name="itemselected[]" id="selectcheckbox'.$restyu1->partid.'" value="'.$restyu1->partid.'" '.$a.' onchange="checkifcheckedforpr('.$restyu1->partid.');"></td>
			<td>'.strtoupper($restyu1->part).'</td>
			<td>'.strtoupper($restyu1->fincode).'</td>
			<td>'.strtoupper($restyu1->specification).'</td>
			<td><input type="text" class="form-control" class="qtyforpart" name="qtyparts'.$restyu1->partid.'" id="partsqty'.$restyu1->partid.'" placeholder="QTY" value="'.floatval($restyu1->qty).'" readonly style="display:none;width:50%">&nbsp;<span id="udata'.$restyu1->partid.'" style="display:none">'.strtoupper($uname).'</span></td>
			<td><span style="color:red;">'.$finalstock.' '.strtoupper($uname).'</span><input type="hidden" name="unit'.$restyu1->partid.'" id="unit'.$restyu1->partid.'" value="'.$restyu1->unit.'"><input type="hidden" name="currentstock'.$restyu1->partid.'" id="currentstock'.$restyu1->partid.'" value="'.$finalstock.'"><input type="hidden" name="masterid'.$restyu1->partid.'" id="masterid'.$restyu1->partid.'" value="'.$restyu1->id.'"></td>
			<td><img src="'.$path.'" width="50px"></td>
			<td><textarea class="form-control" style="resize:none;display:none" name="prreason'.$restyu1->partid.'" id="prreason'.$restyu1->partid.'" placeholder="Reason of raising Pr since item is in stock"></textarea></td>
			</tr>';
		
		
	}
	
}else{
	

$html.='<tr>
			<td colspan="3">NO PARTS AVAILABLE</td>
			</tr>';	
	
}

echo $html;
	
	
}



function getimportedstockdetails()
{
	$jobcardid=$this->uri->segment(3);
	$restty=$this->db->select('a.stock,a.id')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->where('b.id',$jobcardid)->get();
	if($restty->num_rows()>0)
	{
		foreach($restty->result() as $restty1);
		
		/** CHECK FOR BLOCKED STOCK **/
		$restye=$this->db->select('id')->from('imported_item_blocked')->where('item_id',$restty1->id)->where('dispatched','0')->get();
		$bl=$restye->num_rows();
		/** END **/
		if($restty1->stock==0)
		{
		$stock=0;
		}else
		{
		   
		    $stock=$restty1->stock-$bl;
		}
		
		
	}else
	{
		$stock=0;
	}
	
	
	echo $stock;
}


function getimporteditemdetail()
{
	$instr=$this->uri->segment(3);
	
	$rest=$this->db->select('a.item_id')->from('order_instruments a')->where('a.id',$instr)->get();
    if($rest->num_rows()>0)
	{
		foreach($rest->result() as $restyuii);
		
		$check=$this->db->select('id')->from('presto_instruments')->where('id',$restyuii->item_id)->where('type','1')->get();
		
		echo $check->num_rows();exit;
		
	}else
	{
		echo "0";
		
	}
}


function orderplanstepfour()
{
	
		$orderid=$this->uri->segment(3);
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$jbcard=$this->input->post('jobcardno');
		$otype=$this->input->post('ordertype');
		$factory=$this->input->post('factory');
		$import=$this->input->post('imported');
		$fileno=$this->input->post('fileno');
		$planstartsfrom=$this->input->post('pfms');
		
		if(count($jbcard)>0)
		{
			for($i=0;$i<count($jbcard);$i++)
		{
		/** GET ORDER DETAILS **/
		$oddetail=$this->db->select('company_name')->from('prestogroup_orders')->where('order_id',$orderid)->get();
		if($oddetail->num_rows()>0)
		{
			foreach($oddetail->result() as $oddetails);
			 $ocompany=$oddetails->company_name;
			
		}else
		{
			$ocompany='';
		}
		
				$res=$this->db->select('item_id')->from('order_instruments')->where('id',$jbcard[$i])->get();
				if($res->num_rows()>0)
				{
				foreach($res->result() as $resitem);
				
				$itemid=$resitem->item_id;
				}else{
					$itemid=0;
					echo "INVALID JOBCARD";EXIT;
				}
					/** END **/
					
					/** PLAN ORDER **/
					$itemstock=$this->db->select('stock')->from('presto_instruments')->where('id',$itemid)->get();
					if($itemstock->num_rows()>0)
					{
						foreach($itemstock->result() as $itemstocks);
						$importedmachinestock=$itemstocks->stock;
						
					}else{ $importedmachinestock=0; }
					if($itemid<>0 && $importedmachinestock<>0)
					{
					$data=array('jobcard_id'=>$jbcard[$i],'order_id'=>$orderid,'orderstatus'=>$this->input->post('orderstatus'),'remarks'=>$this->input->post('remarks'),'ordertype'=>$otype,'factory'=>$factory,'fileno'=>$fileno,'plannedOn'=>date('Y-m-d H:i:s'),'plannedby'=>$user_id,'plannedOn'=>date('Y-m-d H:i:s'),'updatedOn'=>date('Y-m-d H:i:s'),'planstartsfrom'=>$planstartsfrom);
					//echo "<pre>"; print_r($data);exit;
					$this->db->insert('order_planning',$data);


					/** END **/

					/** BLOCK AND SUBTRACT STOCK **/
                     $selforder=$this->checkifselforder($orderid);
						
						if($selforder==0)
						{

                        $blockdata=array('item_id'=>$itemid,'party_name'=>$ocompany,'blocked_qty'=>'1','jobcard'=>$jbcard[$i],'source'=>'1','added_on'=>date('Y-m-d H:i:s'),'added_by'=>$_SESSION['logged_in']['user_id']);
                        $this->db->insert('imported_item_blocked',$blockdata);
                        $affect=$this->db->affected_rows();
                        if($affect<>0)
                        {
                        /** SUBTRACT STOCK **/
                        
                       /** $curr=$importedmachinestock-1;
                        $newstock=array('stock'=>$curr);
                        $this->db->where('id',$itemid);
                        $this->db->update('presto_instruments',$newstock); **/
					}

                    $odinsdata=array('complete'=>'1','packed'=>'1');
                    $this->db->where('id',$jbcard[$i]);
                    $this->db->update('order_instruments',$odinsdata);

					/** IF THIS IS SALESFORCE ORDER **/
					$this->checkifthisissforderandlastonetogetcompleted($orderid);
					/** END **/
                    
                    /**$cldata=array('movetodispatch'=>'1','movedOn'=>date('Y-m-d H:i:s'),'movedby'=>$_SESSION['logged_in']['user_id']);
                    $this->db->where('order_id',$orderid);
                    $this->db->update('prestogroup_orders',$cldata);**/

					/** END **/
				     }
				     
			
				
				}else{
					
					$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Unable to plan the order</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
				}
				/** END **/
			
		}
			
		}
		
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Thank You! This Order has been planned</span></div>');
				redirect(page_url.'FMS/planorder/'.$orderid);
		
	

	
}



function rollback()
{
	$jobcard=$this->uri->segment(3);
	$orderid=$this->uri->segment(4);
	$planid=$this->uri->segment(5);
	
/** ORDER PLANNING **/
$this->db->where('jobcard_id',$jobcard);
$this->db->delete('order_planning');
/** END **/

/** ORDER PLANNING REMARKS **/
$this->db->where('jobcardid',$jobcard);
$this->db->where('orderid',$orderid);
$this->db->delete('order_planning_remarks');
/** END **/

/** ORDER Stage **/
$this->db->where('jobcardid',$jobcard);
$this->db->where('orderid',$orderid);
$this->db->delete('order_stage');
/** END **/

/** kitting_bop_details**/
$this->db->where('jobcardid',$jobcard);
$this->db->delete('kitting_bop_details');
/** END **/

/** jumpjobcard**/
$this->db->where('jobcardid',$jobcard);
$this->db->delete('jumpjobcard');
/** END **/

/** fmsmergehistory**/
$this->db->where('jobcardid',$jobcard);
$this->db->delete('fmsmergehistory');
/** END **/

/** fmstatdate**/
$this->db->where('jobcardid',$jobcard);
$this->db->delete('fmstatdate');
/** END **/
	
/** ADD FMS ROLLBACK HISTORY **/
$data=array('jobcardid'=>$jobcard,'orderid'=>$orderid,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
$this->db->insert('fmsrollbackhistory',$data);

/** END **/	

$this->session->set_flashdata('message','Jobcard Roll Backed');
redirect(page_url.'FMS/planorder/'.$orderid);
	
}


function ordernotrecieved()
{
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		$orderid=$this->uri->segment(3);
		$jbcard=$this->input->post('jobcardno');
		$orderstatus=$this->input->post('orderstatus');
		$remarks=$this->input->post('remarks');
		
		$data=array('orderid'=>$orderid,'jobcardid'=>$jbcard[0],'remarks'=>$remarks,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
		$this->db->insert('order_planning_remarks',$data);
		$this->session->set_flashdata('message','Remarks Updated');
		redirect(page_url.'FMS/planorder/'.$orderid);
	
}




function getmachines()
{
	
		$searchtrm= $_GET['q'];
	

		$query = $this->db->select('id, instruments_name, status')->from('presto_instruments')->like('instruments_name',$searchtrm,'both',false)->where('status','1')->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments){

		$json[] = array('id'=>$instruments->id, 'text'=>$instruments->instruments_name);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}

		echo json_encode($json);
	
	
}


function getusers()
{
	
		$searchtrm= $_GET['q'];
	

		$query = $this->db->select('user_id, first_name,last_name')->from('system_users')->like('first_name',$searchtrm,'both',false)->where('user_status','1')->where('hide_profile','0')->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments){

		$json[] = array('id'=>$instruments->user_id, 'text'=>$instruments->first_name." ".$instruments->last_name);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}

		echo json_encode($json);
	
	
}


function getmachinesontype()
{
	
	
		$searchtrm= $_GET['searchTerm'];
		$type= $_GET['type'];
	if($type<>'')
	{
if($type=='1' || $type=='2')
{


		 $this->db->select('id, instruments_name, status')->from('presto_instruments')->like('instruments_name',$searchtrm,'both',false)->where('status','1');
		 if($type=='2')
		 {
			 $this->db->where('type',$type);
		 }else{
			  $this->db->where_in('type','0','1');
		 }
		
		$query =$this->db->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments){

		$json[] = array('id'=>$instruments->id, 'text'=>$instruments->instruments_name);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}
}else{
	
	 $this->db->select('id, part')->from('machine_parts_with_picture')->like('part',$searchtrm,'both',false);
		$query =$this->db->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments){

		$json[] = array('id'=>$instruments->id, 'text'=>$instruments->part);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}
	
	}
	
	}else{
		$json=array();
			}

		echo json_encode($json);
	
	
}

public function unplannedorder()
{
     $ip = $_SERVER["REMOTE_ADDR"];
            $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }

else{
    $this->load->view('FMS/unplannedorder');
}
}

public function unplannedorderforservice()
{
    $this->load->view('FMS/unplannedorderforservice');
}

public function unplanned_order_listOldd()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->order_by('a.order_id','desc')->get();
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
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
			$instrumentsss = array();
			
			$query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
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
				//	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
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
				$planaction="<a href='".page_url."FMS/planorder/".$row->order_id."'><span class='btn btn-sm btn-success'>PLANNED</span></a>";
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
			$internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-primary'>I/0 Slip</span></a>";
			
			if($totjobcard<>$totjobcardplanned)
			{
			$scheduler_data[] = array('sr_no'=>$i,
									  'generatebutton'=>$internalordersheet,
									  'planorder'=>$planaction,
									  'added_on'=>$addeddate."".$addedtime,
									  'order_type'=>strtoupper($row->order_type),
									  'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
									  'po_number'=>strtoupper($row->po_number),
									  'company_name'=>strtoupper($row->company_name),
									  'contactperson'=>strtoupper($row->contact_person),
									  'designation'=>strtoupper($row->company_name),
									
									  'address'=>strtoupper($row->address),
									  'email'=>strtoupper($row->email),
									  'mobile_number'=>strtoupper($row->mobile_number),
									    'phone'=>strtoupper($row->phone),
									  
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
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


public function unplanned_order_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.order_id')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->order_by('a.order_id','DESC')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$query1 = $this->db->select('a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_id',$row->order_id)->get();
			
			foreach($query1->result() as $row1);
			
                date_default_timezone_set("Asia/Kolkata");
                $addeddate = date('d-M-Y', strtotime($row1->added_on));
                $time = date('H:i:s', strtotime($row1->added_on));
                $addedtime = "<br>". date('g:i A', strtotime($time)); 
                
                $status = $row1->order_status;
                if($status=='1')
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
                }else
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
                }
                $html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
                $instrumentsss = array();
                
                $query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
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
                //	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
                $html.="</tr>";
                
                }
                $html.="</table>";
                $ins_charges = "";
			
			
         
            $ins_charges = "";
            /** Get order planned **/
            $restt=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->get();
            $totjobcard= $restt->num_rows();
            
            $restt1=$this->db->select('id')->from('order_planning')->where('order_id',$row->order_id)->get();
          
            $totjobcardplanned= $restt1->num_rows();
            if($totjobcard<>$totjobcardplanned)
            {
                $planaction="<a href='".page_url."FMS/planorder/".$row->order_id."' class='btn btn-sm btn-warning'>PLAN (".$totjobcardplanned."/".$totjobcard.")</a>";
                
                $edit = "<a href='".page_url."FMS/edit_order/".$row->order_id."'><i class='fa fa-pencil'></i></a>";
			
			if($row1->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row1->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row1->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
            if($row1->packing_charges=='1'){
            $packcharges = "PAID BY PARTY";
            $packingcharges= $packcharges."<br> Amount - <strong>".$row1->packing_amount."</strong>";
            }else{
            $packingcharges = "INCLUSIVE";
            }
            
            if($row1->freight_type=='1'){
            $freigntcharges = "TO PAY BASIS";
            
            }else if($row1->freight_type=='2'){
            $freigntcharges = "PAID BY PRESTO";
            }else if($row1->freight_type=='3'){
            $frtcharges = "BILLED IN INVOICE";
            $freigntcharges= $frtcharges."<br> Amount - <strong>".$row1->freight_amount."</strong>";
            }else{
            $freigntcharges = "OWN PICK-UP";
            
            }
            $internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-primary'>I/0 Slip</span></a>";
			
		$scheduler_data[] = array('sr_no'=>$i,
									  'generatebutton'=>$internalordersheet,
									  'planorder'=>$planaction,
									  'added_on'=>$addeddate."".$addedtime,
									  'order_type'=>strtoupper($row1->order_type),
									  'marketing_person'=>strtoupper($row1->title." ".$row1->first_name." ".$row1->last_name),
									  'po_number'=>strtoupper($row1->po_number),
									  'company_name'=>strtoupper($row1->company_name),
									  'contactperson'=>strtoupper($row1->contact_person),
									  'designation'=>strtoupper($row1->company_name),
									
									  'address'=>strtoupper($row1->address),
									  'email'=>strtoupper($row1->email),
									  'mobile_number'=>strtoupper($row1->mobile_number),
									    'phone'=>strtoupper($row1->phone),
									  
									  'internal_order_no'=>strtoupper($row1->internal_order_no),
									  'itemname'=>$html,
									  'discount'=>strtoupper($row1->discount)."%",
									  'order_value_after_discount'=>strtoupper($row1->order_value_after_discount),
									  'advance_amount'=>strtoupper($row1->advance_amount),
									  'payment_terms'=>strtoupper($row1->payment_terms),
									  'installation_charges'=>strtoupper($ins_charges),
									  'packingcharges'=>"<strong>Packing Type</strong> -".$row1->packing_type."<br>".$packingcharges,
									  'freigntcharges'=>$freigntcharges,
									  'remarks'=>$row1->remarks,
									  'status'=>$sta,
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
    
    
    public function unplanned_order_listservice()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.order_id')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->where('a.order_type','SERVICE')->order_by('a.order_id','DESC')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$query1 = $this->db->select('a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_id',$row->order_id)->get();
			
			foreach($query1->result() as $row1);
			
                date_default_timezone_set("Asia/Kolkata");
                $addeddate = date('d-M-Y', strtotime($row1->added_on));
                $time = date('H:i:s', strtotime($row1->added_on));
                $addedtime = "<br>". date('g:i A', strtotime($time)); 
                
                $status = $row1->order_status;
                if($status=='1')
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
                }else
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
                }
                $html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
                $instrumentsss = array();
                
                $query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
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
                //	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
                $html.="</tr>";
                
                }
                $html.="</table>";
                $ins_charges = "";
			
			
         
            $ins_charges = "";
            /** Get order planned **/
            $restt=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->get();
            $totjobcard= $restt->num_rows();
            
            $restt1=$this->db->select('id')->from('order_planning')->where('order_id',$row->order_id)->get();
          
            $totjobcardplanned= $restt1->num_rows();
            if($totjobcard<>$totjobcardplanned)
            {
                $planaction="<a href='".page_url."FMS/planorder/".$row->order_id."' class='btn btn-sm btn-warning'>PLAN (".$totjobcardplanned."/".$totjobcard.")</a>";
                
                $edit = "<a href='".page_url."FMS/edit_order/".$row->order_id."'><i class='fa fa-pencil'></i></a>";
			
			if($row1->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row1->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row1->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
            if($row1->packing_charges=='1'){
            $packcharges = "PAID BY PARTY";
            $packingcharges= $packcharges."<br> Amount - <strong>".$row1->packing_amount."</strong>";
            }else{
            $packingcharges = "INCLUSIVE";
            }
            
            if($row1->freight_type=='1'){
            $freigntcharges = "TO PAY BASIS";
            
            }else if($row1->freight_type=='2'){
            $freigntcharges = "PAID BY PRESTO";
            }else if($row1->freight_type=='3'){
            $frtcharges = "BILLED IN INVOICE";
            $freigntcharges= $frtcharges."<br> Amount - <strong>".$row1->freight_amount."</strong>";
            }else{
            $freigntcharges = "OWN PICK-UP";
            
            }
            $internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-primary'>I/0 Slip</span></a>";
			
		$scheduler_data[] = array('sr_no'=>$i,
									  'generatebutton'=>$internalordersheet,
									  'planorder'=>$planaction,
									  'added_on'=>$addeddate."".$addedtime,
									  'order_type'=>strtoupper($row1->order_type),
									  'marketing_person'=>strtoupper($row1->title." ".$row1->first_name." ".$row1->last_name),
									  'po_number'=>strtoupper($row1->po_number),
									  'company_name'=>strtoupper($row1->company_name),
									  'contactperson'=>strtoupper($row1->contact_person),
									  'designation'=>strtoupper($row1->company_name),
									
									  'address'=>strtoupper($row1->address),
									  'email'=>strtoupper($row1->email),
									  'mobile_number'=>strtoupper($row1->mobile_number),
									    'phone'=>strtoupper($row1->phone),
									  
									  'internal_order_no'=>strtoupper($row1->internal_order_no),
									  'itemname'=>$html,
									  'discount'=>strtoupper($row1->discount)."%",
									  'order_value_after_discount'=>strtoupper($row1->order_value_after_discount),
									  'advance_amount'=>strtoupper($row1->advance_amount),
									  'payment_terms'=>strtoupper($row1->payment_terms),
									  'installation_charges'=>strtoupper($ins_charges),
									  'packingcharges'=>"<strong>Packing Type</strong> -".$row1->packing_type."<br>".$packingcharges,
									  'freigntcharges'=>$freigntcharges,
									  'remarks'=>$row1->remarks,
									  'status'=>$sta,
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
    
    
    
    public function nondispatched_orderlist()
	{
		$scheduler_data = array();
		$uri = $this->uri->segment(3);
		$zoneid = $this->uri->segment(4);
			
		if($zoneid<>'')
		{
			$user=$this->getallzoneusers($zoneid);
			$users= "'" . implode ( "', '", $user ) . "'";
		}
		
		$this->db->select('a.order_id')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0');
		if($uri<>'' && $uri<>'NA'){
		    $this->db->where('a.marketing_person',$uri);
		}
		
		if($zoneid<>'')
		{
		
			 $this->db->where_in('a.marketing_person',$users,false);
		}
		$query = $this->db->order_by('a.order_id','DESC')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$query1 = $this->db->select('a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_id',$row->order_id)->get();
			
			foreach($query1->result() as $row1);
			
                date_default_timezone_set("Asia/Kolkata");
                $addeddate = date('d-M-Y', strtotime($row1->added_on));
                $time = date('H:i:s', strtotime($row1->added_on));
                $addedtime = "<br>". date('g:i A', strtotime($time)); 
                
                $status = $row1->order_status;
                if($status=='1')
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
                }else
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
                }
                $html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
                $instrumentsss = array();
                
                $query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
                foreach($query->result() as $instruments){
                if($instruments->complete=='1'){
                $backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
                }else{
                $backgroundcolor="";
                }
                $html.="<tr>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
                //$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
                //	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
                $html.="</tr>";
                
                }
                $html.="</table>";
                $ins_charges = "";
			
			
         
            $ins_charges = "";
            /** Get order planned **/
            $restt=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('finalpacked','0')->get();
            $totjobcard= $restt->num_rows();
            
         
             
            if($totjobcard>0)
            {
                
			
			if($row1->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row1->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row1->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
            if($row1->packing_charges=='1'){
            $packcharges = "PAID BY PARTY";
            $packingcharges= $packcharges."<br> Amount - <strong>".$row1->packing_amount."</strong>";
            }else{
            $packingcharges = "INCLUSIVE";
            }
            
            if($row1->freight_type=='1'){
            $freigntcharges = "TO PAY BASIS";
            
            }else if($row1->freight_type=='2'){
            $freigntcharges = "PAID BY PRESTO";
            }else if($row1->freight_type=='3'){
            $frtcharges = "BILLED IN INVOICE";
            $freigntcharges= $frtcharges."<br> Amount - <strong>".$row1->freight_amount."</strong>";
            }else{
            $freigntcharges = "OWN PICK-UP";
            
            }
            $internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-primary'>I/0 Slip</span></a>";
			
		$scheduler_data[] = array('sr_no'=>$i,
									 
									  'added_on'=>$addeddate."".$addedtime,
									  'order_type'=>strtoupper($row1->order_type),
									  'marketing_person'=>strtoupper($row1->title." ".$row1->first_name." ".$row1->last_name),
									  'po_number'=>strtoupper($row1->po_number),
									  'company_name'=>strtoupper($row1->company_name),
									  'contactperson'=>strtoupper($row1->contact_person),
									  'designation'=>strtoupper($row1->company_name),
									
									  'address'=>strtoupper($row1->address),
									  'email'=>strtoupper($row1->email),
									  'mobile_number'=>strtoupper($row1->mobile_number),
									    'phone'=>strtoupper($row1->phone),
									  
									  'internal_order_no'=>strtoupper($row1->internal_order_no),
									  'itemname'=>$html,
									  'discount'=>strtoupper($row1->discount)."%",
									  'order_value_after_discount'=>strtoupper($row1->order_value_after_discount),
									  'advance_amount'=>strtoupper($row1->advance_amount),
									  'payment_terms'=>strtoupper($row1->payment_terms),
									  'installation_charges'=>strtoupper($ins_charges),
									  'packingcharges'=>"<strong>Packing Type</strong> -".$row1->packing_type."<br>".$packingcharges,
									  'freigntcharges'=>$freigntcharges,
									  'remarks'=>$row1->remarks,
									  'status'=>$sta);
									  
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
    
    
    public function nondispatched_orderlistforservice()
	{
		$scheduler_data = array();
		$uri = $this->uri->segment(3);
		$zoneid = $this->uri->segment(4);
			
		if($zoneid<>'')
		{
			$user=$this->getallzoneusers($zoneid);
			$users= "'" . implode ( "', '", $user ) . "'";
		}
		
		$this->db->select('a.order_id')->from('prestogroup_orders a')->where('a.order_status','1')->where('a.closeorder','0')->where('a.order_type','SERVICE');
		if($uri<>'' && $uri<>'NA'){
		    $this->db->where('a.marketing_person',$uri);
		}
		
		if($zoneid<>'')
		{
		
			 $this->db->where_in('a.marketing_person',$users,false);
		}
		$query = $this->db->order_by('a.order_id','DESC')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$query1 = $this->db->select('a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_id',$row->order_id)->get();
			
			foreach($query1->result() as $row1);
			
                date_default_timezone_set("Asia/Kolkata");
                $addeddate = date('d-M-Y', strtotime($row1->added_on));
                $time = date('H:i:s', strtotime($row1->added_on));
                $addedtime = "<br>". date('g:i A', strtotime($time)); 
                
                $status = $row1->order_status;
                if($status=='1')
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-success btn-xs'>Active</span></a>";
                }else
                {
                $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row1->order_id."/".$row1->order_status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
                }
                $html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th></tr>";
                $instrumentsss = array();
                
                $query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
                foreach($query->result() as $instruments){
                if($instruments->complete=='1'){
                $backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
                }else{
                $backgroundcolor="";
                }
                $html.="<tr>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
                //$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
                $html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
                //	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
                $html.="</tr>";
                
                }
                $html.="</table>";
                $ins_charges = "";
			
			
         
            $ins_charges = "";
            /** Get order planned **/
            $restt=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('finalpacked','0')->get();
            $totjobcard= $restt->num_rows();
            
         
             
            if($totjobcard>0)
            {
                
			
			if($row1->installation_charges=='1'){
				$charges= "<strong>INSTALLATION TYPE </strong>".$row1->installation_type."<br> <strong>INSTALLATION CHARGES</strong> - REQUIRED";
				
				$ins_charges= $charges."<br> AMOUNT - ".$row1->installation_amount;
			}else{
					$ins_charges = "NOT REQUIRED";
			}
			
            if($row1->packing_charges=='1'){
            $packcharges = "PAID BY PARTY";
            $packingcharges= $packcharges."<br> Amount - <strong>".$row1->packing_amount."</strong>";
            }else{
            $packingcharges = "INCLUSIVE";
            }
            
            if($row1->freight_type=='1'){
            $freigntcharges = "TO PAY BASIS";
            
            }else if($row1->freight_type=='2'){
            $freigntcharges = "PAID BY PRESTO";
            }else if($row1->freight_type=='3'){
            $frtcharges = "BILLED IN INVOICE";
            $freigntcharges= $frtcharges."<br> Amount - <strong>".$row1->freight_amount."</strong>";
            }else{
            $freigntcharges = "OWN PICK-UP";
            
            }
            $internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-primary'>I/0 Slip</span></a>";
			
		$scheduler_data[] = array('sr_no'=>$i,
									 
									  'added_on'=>$addeddate."".$addedtime,
									  'order_type'=>strtoupper($row1->order_type),
									  'marketing_person'=>strtoupper($row1->title." ".$row1->first_name." ".$row1->last_name),
									  'po_number'=>strtoupper($row1->po_number),
									  'company_name'=>strtoupper($row1->company_name),
									  'contactperson'=>strtoupper($row1->contact_person),
									  'designation'=>strtoupper($row1->company_name),
									
									  'address'=>strtoupper($row1->address),
									  'email'=>strtoupper($row1->email),
									  'mobile_number'=>strtoupper($row1->mobile_number),
									    'phone'=>strtoupper($row1->phone),
									  
									  'internal_order_no'=>strtoupper($row1->internal_order_no),
									  'itemname'=>$html,
									  'discount'=>strtoupper($row1->discount)."%",
									  'order_value_after_discount'=>strtoupper($row1->order_value_after_discount),
									  'advance_amount'=>strtoupper($row1->advance_amount),
									  'payment_terms'=>strtoupper($row1->payment_terms),
									  'installation_charges'=>strtoupper($ins_charges),
									  'packingcharges'=>"<strong>Packing Type</strong> -".$row1->packing_type."<br>".$packingcharges,
									  'freigntcharges'=>$freigntcharges,
									  'remarks'=>$row1->remarks,
									  'status'=>$sta);
									  
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
    
    
    function nondispatchedlist()
    {
        
        $ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }

else{
        $this->load->view('FMS/nondispatchedorder');
}
    }
    
     function nondispatchedlistforservice()
    {
        $this->load->view('FMS/nondispatchedorderforservice');
    }
   
   
function divertorder()
{
	$this->load->view('FMS/divertorder');

}

function diverttoexitingorderOldddd()
{
	$orderid=$this->uri->segment(3);
	$ready=$this->uri->segment(4);
	$type=$this->input->post('type');
	$reason=$this->input->post('reason');
	$expdate=$this->input->post('expo');
	$dcust=$this->input->post('dcustname');
	$diono=$this->input->post('dcustiono');
	
	$resty=$this->db->select('*')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($resty->num_rows()>0)
	{
		
		foreach($resty->result() as $row);

	
			$data=
			array('order_type'=>$row->order_type,
			'marketing_person'=>$row->marketing_person,
			'po_number'=>$row->po_number,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'pincode'=>$row->pincode,
			'email'=>$row->email,
			'mobile_number'=>$row->mobile_number,
			'phone'=>$row->phone,
			'contact_person'=>$row->contact_person,
			'designation'=>$row->designation,
			'internal_order_no'=>$row->internal_order_no,
			'discount'=>$row->discount,
			'order_value_after_discount'=>$row->order_value_after_discount,
			'advance_amount'=>$row->advance_amount,
			'payment_terms'=>$row->payment_terms,
			'installation_charges'=>$row->installation_charges,
			'installation_type'=>$row->installation_type,
			'installation_amount'=>$row->installation_type,
			'packing_type'=>$row->packing_type,
			'packing_charges'=>$row->packing_charges,
			'packing_amount'=>$row->packing_amount,
			'freight_type'=>$row->packing_amount,
			'freight_amount'=>$row->freight_amount,
			'remarks'=>$row->remarks,
			'order_status'=>$row->order_status,
			'closeorder'=>$row->closeorder,
			'closedby'=>$row->closedby,
			'closedOn'=>$row->closedOn,
			'selforder'=>$row->selforder,
			'movetodispatch'=>$row->movetodispatch,
			'movedby'=>$row->movedby,
			'movedOn'=>$row->movedOn,
			'delivery_date'=>$row->delivery_date,
			'updated_on'=>$row->updated_on);
			$res = $this->db->insert('prestogroup_divertedordershisory',$data);
			$last_id = $this->db->insert_id();
	
			
			if($last_id>0)
			{
			if($this->input->post('type')=='2')
			{
				$holddate=date('Y-m-d',strtotime($this->input->post('expo')));
			}else{
				$holddate='';
			}
			
			$divertdata=array('previousorderid'=>$orderid,'divertedorderid'=>$last_id,'type'=>$this->input->post('type'),'hdate'=>$holddate,'reason'=>$this->input->post('remarks'),'divertcustomer'=>$dcust,'divertiono'=>$diono,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('divertedorders',$divertdata);
			if($this->db->affected_rows()>0)
			{
				
					
					$updata=array('order_type'=>strtoupper('SALE'),
					'marketing_person'=>strtoupper(3),
					'company_name'=>strtoupper('PRESTO STANTEST PVT LTD'),
					'address'=>strtoupper('Phase-1, I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana'),
					'pincode'=>strtoupper('121003'),
					'email'=>strtoupper('info@prestogroup.com'),
					'mobile_number'=>strtoupper('1294272727'),
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
					'selforder'=>'1',
					'divertorder'=>'1');
					
					$this->db->where('order_id',$orderid);
					$this->db->update('prestogroup_orders',$updata);
					
					
					$odi=$this->db->select('id,item_id')->from('order_instruments')->where('order_id',$orderid)->where('complete','1')->get();
					if($odi->num_rows()>0)
					{
						//echo "<pre>"; print_r($odi->result());exit;
						foreach($odi->result() as $odi1)
						{
							
							$fact=$this->checkfactorytype($odi1->id,$orderid);
							
							if($fact<>0)
							{
								
								if($fact<>'7')
								{
								
							$itemid=$odi1->item_id;
							
							$qty='1';
							$currst=$this->getcurrentstock($itemid);

							$newstock=$currst+$qty;

							$upnewstock=array('stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('presto_instruments',$upnewstock);
							
							}else
							{
								
							$itemid=$odi1->item_id;
							$jobcardid=$odi1->id;
							$qty='1';
							$currst=$this->getcurrentstock($itemid);

							$newstock=$currst+$qty;
							
							$upnewstock=array('stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('presto_instruments',$upnewstock);
							
							
							$datablockckc = array('cancelled_order'=>'1',
							'cancellation_remarks'=>$this->input->post('remarks'));

							$this->db->where('item_id',$itemid);
							$this->db->where('jobcard',$jobcardid);
							$res = $this->db->update('imported_item_blocked',$datablockckc);
									
						}
							
					}
							
							
						}
						
					}
					
					
				
				
				
				
			}else{
				
				echo "UNABLE TO ADD DIVERT HISTORY";exit;
				
				
			}
			
			}else{
				
				echo "CANNOT DIVERT ORDER"; exit;
				
			}




			$this->session->set_flashdata('message','Order Diverted');
			redirect(page_url.'Reporting/lotorderlist');
		
		
		
	}else{
		
		echo "INVALID ORDER ID";exit;
		
		
	}
	
	
	

	
	
		
	
}



function diverttoexitingorder()
{
	$orderid=$this->uri->segment(3);
	$ready=$this->uri->segment(4);
	$type=$this->input->post('type');
	$reason=$this->input->post('reason');
	$expdate=$this->input->post('expo');
	$dcust=$this->input->post('dcustname');
	$diono=$this->input->post('dcustiono');
	$dtype=$this->input->post('dtype');
	
	$isorderready=$this->input->post('readyorder');
	$selforder=$this->input->post('selforder');
	
	if($dtype=='1')
	{
	$resty=$this->db->select('*')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($resty->num_rows()>0)
	{
		
		foreach($resty->result() as $row);

	
			$data=
			array('order_type'=>$row->order_type,
			'marketing_person'=>$row->marketing_person,
			'po_number'=>$row->po_number,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'pincode'=>$row->pincode,
			'email'=>$row->email,
			'mobile_number'=>$row->mobile_number,
			'phone'=>$row->phone,
			'contact_person'=>$row->contact_person,
			'designation'=>$row->designation,
			'internal_order_no'=>$row->internal_order_no,
			'discount'=>$row->discount,
			'order_value_after_discount'=>$row->order_value_after_discount,
			'advance_amount'=>$row->advance_amount,
			'payment_terms'=>$row->payment_terms,
			'installation_charges'=>$row->installation_charges,
			'installation_type'=>$row->installation_type,
			'installation_amount'=>$row->installation_type,
			'packing_type'=>$row->packing_type,
			'packing_charges'=>$row->packing_charges,
			'packing_amount'=>$row->packing_amount,
			'freight_type'=>$row->packing_amount,
			'freight_amount'=>$row->freight_amount,
			'remarks'=>$row->remarks,
			'order_status'=>$row->order_status,
			'closeorder'=>$row->closeorder,
			'closedby'=>$row->closedby,
			'closedOn'=>$row->closedOn,
			'selforder'=>$row->selforder,
			'movetodispatch'=>$row->movetodispatch,
			'movedby'=>$row->movedby,
			'movedOn'=>$row->movedOn,
			'delivery_date'=>$row->delivery_date,
			'updated_on'=>$row->updated_on);
			$res = $this->db->insert('prestogroup_divertedordershisory',$data);
			$last_id = $this->db->insert_id();
	
			
			if($last_id>0)
			{
			if($this->input->post('type')=='2')
			{
				$holddate=date('Y-m-d',strtotime($this->input->post('expo')));
			}else{
				$holddate='';
			}
			
			$divertdata=array('previousorderid'=>$orderid,'divertedorderid'=>$last_id,'type'=>$this->input->post('type'),'hdate'=>$holddate,'reason'=>$this->input->post('remarks'),'divertcustomer'=>$dcust,'divertiono'=>$diono,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('divertedorders',$divertdata);
			if($this->db->affected_rows()>0)
			{
				
					
					$updata=array('order_type'=>strtoupper('SALE'),
					'marketing_person'=>strtoupper(3),
					'company_name'=>strtoupper('PRESTO STANTEST PVT LTD'),
					'address'=>strtoupper('Phase-1, I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana'),
					'pincode'=>strtoupper('121003'),
					'email'=>strtoupper('info@prestogroup.com'),
					'mobile_number'=>strtoupper('1294272727'),
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
					'selforder'=>'1',
					'divertorder'=>'1');
					
					$this->db->where('order_id',$orderid);
					$this->db->update('prestogroup_orders',$updata);
					
					
					$odi=$this->db->select('id,item_id')->from('order_instruments')->where('order_id',$orderid)->where('complete','1')->get();
					if($odi->num_rows()>0)
					{
						//echo "<pre>"; print_r($odi->result());exit;
						foreach($odi->result() as $odi1)
						{
							
							$fact=$this->checkfactorytype($odi1->id,$orderid);
							
							if($fact<>0)
							{
								
								if($fact<>'7')
								{
								
							$itemid=$odi1->item_id;
							
							$qty='1';
							$currst=$this->getcurrentstock($itemid);

							$newstock=$currst+$qty;

							$upnewstock=array('stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('presto_instruments',$upnewstock);
							
							}else
							{
								
							$itemid=$odi1->item_id;
							$jobcardid=$odi1->id;
							$qty='1';
							$currst=$this->getcurrentstock($itemid);

							$newstock=$currst+$qty;
							
							$upnewstock=array('stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('presto_instruments',$upnewstock);
							
							
							$datablockckc = array('cancelled_order'=>'1',
							'cancellation_remarks'=>$this->input->post('remarks'));

							$this->db->where('item_id',$itemid);
							$this->db->where('jobcard',$jobcardid);
							$res = $this->db->update('imported_item_blocked',$datablockckc);
									
						}
							
					}
							
							
						}
						
					}
					
					
				
				
				
				
			}else{
				
				echo "UNABLE TO ADD DIVERT HISTORY";exit;
				
				
			}
			
			}else{
				
				echo "CANNOT DIVERT ORDER"; exit;
				
			}




			$this->session->set_flashdata('message','Order Diverted');
			redirect(page_url.'Reporting/lotorderlist');
		
		
		
	}else{
			
		echo "INVALID ORDER ID";exit;
		
		
	}
	
	
	}else{
	//echo "hi";exit;
	if($this->input->post('type')=='2')
			{
				$holddate=date('Y-m-d',strtotime($this->input->post('expo')));
			}else{
				$holddate='';
			}
			
/** JOBCARD WISE **/
$jobcardtodivert=$this->input->post('jobcard');

$jobcardtogetreplaced=$diono;
$itemins=$this->getinstrumentid($jobcardtogetreplaced);

$sourceoid=$orderid;
$destinationoid=$this->getorderid($jobcardtogetreplaced);
/** END **/	

	/** ADD Divert History **/
	$data=array('sourcejobcardid'=>$jobcardtodivert,'sourceorderid'=>$sourceoid,'destinationjobcardid'=>$jobcardtogetreplaced,'destinationorderid'=>$destinationoid,'type'=>$this->input->post('type'),'hdate'=>$holddate,'reason'=>$this->input->post('remarks'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
            $this->db->insert('divertedjobcards',$data);
	/** end **/
	
	$jobcardnosource=$this->getjobcardno($jobcardtodivert);
	$jobcardnodestination=$this->getjobcardno($jobcardtogetreplaced);
//echo $jobcardnosource.'<br/>'.$jobcardnodestination;exit;
	/** ORDER INSTRUMENT**/
	$data1=array('order_id'=>$destinationoid,'diverted'=>'1','reference'=>$jobcardnodestination);
	
	//	echo "<pre>"; print_r($data1);exit;
		
	$this->db->where('id',$jobcardtodivert);
	$this->db->update('order_instruments',$data1);
	

	
	/** ORDER PLANNING REMARKS **/
	$data2=array('orderid'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('order_planning_remarks',$data2);
	
	/** ORDER PLANNING **/
	$data3=array('order_id'=>$destinationoid);
	$this->db->where('jobcard_id',$jobcardtodivert);
	$this->db->update('order_planning',$data3);
	
	/** ORDER STAGE **/
	$data4=array('orderid'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('order_stage',$data4);	
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data5=array('orderid'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('fmsrollbackhistory',$data5);
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data6=array('order_id'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('skipflow',$data6);
	/** END **/

	$checkifitwasplanned=$this->checkifjobcardwasplanned($jobcardtogetreplaced);
	if($checkifitwasplanned>0)
	{
		
	/** ADD Divert History **/
	$data=array('sourcejobcardid'=>$jobcardtogetreplaced,'sourceorderid'=>$destinationoid,'destinationjobcardid'=>$jobcardtodivert,'destinationorderid'=>$sourceoid,'type'=>$this->input->post('type'),'hdate'=>$holddate,'reason'=>'SINCE JOB CARD WAS PLANNED IT GET CONNECTED TO SOURCE','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('divertedjobcards',$data);
	/** end **/

	/** ORDER INSTRUMENT**/
	$data1=array('order_id'=>$sourceoid,'diverted'=>'1','reference'=>$jobcardnosource);
	$this->db->where('id',$jobcardtogetreplaced);
	$this->db->update('order_instruments',$data1);
	
	/** ORDER PLANNING REMARKS **/
	$data2=array('orderid'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('order_planning_remarks',$data2);
	
	/** ORDER PLANNING **/
	$data3=array('order_id'=>$sourceoid);
	$this->db->where('jobcard_id',$jobcardtogetreplaced);
	$this->db->update('order_planning',$data3);
	
	/** ORDER STAGE **/
	$data4=array('orderid'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('order_stage',$data4);	
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data5=array('orderid'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('fmsrollbackhistory',$data5);
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data6=array('order_id'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('skipflow',$data6);
	/** END **/
		
	}else{
		
		
	
		
		
		$this->db->where('id',$jobcardtogetreplaced);
		$this->db->where('order_id',$destinationoid);
		$this->db->delete('order_instruments');
			
			
		if($type=='3')
		{
			/** CREATE NEW ORDER AND ADD JOBCARD **/
			
			
			$resty=$this->db->select('*')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($resty->num_rows()>0)
	{
		
		$prestio=$this->db->select('internal_order_no')->from('prestogroup_orders')->where('orderduetodiversion','1')->get();
$io=$prestio->num_rows();
if($io==0)
{
$internalolll="0001";
}else{
foreach($prestio->result() as $prestio1);
$io=$io+1;
$internalolll='000'.$io;
}

		foreach($resty->result() as $row);

	
			$neworder=
			array('order_type'=>$row->order_type,
			'marketing_person'=>$row->marketing_person,
			'po_number'=>$row->po_number,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'pincode'=>$row->pincode,
			'email'=>$row->email,
			'mobile_number'=>$row->mobile_number,
			'phone'=>$row->phone,
			'contact_person'=>$row->contact_person,
			'designation'=>$row->designation,
			'internal_order_no'=>$internalolll,
			'discount'=>$row->discount,
			'order_value_after_discount'=>$row->order_value_after_discount,
			'advance_amount'=>$row->advance_amount,
			'payment_terms'=>$row->payment_terms,
			'installation_charges'=>$row->installation_charges,
			'installation_type'=>$row->installation_type,
			'installation_amount'=>$row->installation_type,
			'packing_type'=>$row->packing_type,
			'packing_charges'=>$row->packing_charges,
			'packing_amount'=>$row->packing_amount,
			'freight_type'=>$row->packing_amount,
			'freight_amount'=>$row->freight_amount,
			'remarks'=>$row->remarks,
			'order_status'=>$row->order_status,
			'closeorder'=>$row->closeorder,
			'closedby'=>$row->closedby,
			'closedOn'=>$row->closedOn,
			'selforder'=>$row->selforder,
			'movetodispatch'=>$row->movetodispatch,
			'movedby'=>$row->movedby,
			'movedOn'=>$row->movedOn,
			'delivery_date'=>$row->delivery_date,
			'updated_on'=>$row->updated_on,
			'added_on'=>date('Y-m-d H:i:s'),
			'orderduetodiversion'=>'1',
			'diversionreasonref'=>$jobcardnosource);
			$res = $this->db->insert('prestogroup_orders',$neworder);
			$last_id = $this->db->insert_id();
			
				$jobcardnumber = $internalolll." (1/1)";
						
							$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
								$res1 = $query111->num_rows();
								$serial = $res1+1;
								$mserial= sprintf("%03d", $serial);
								
								$machserial=date('my').'-'.$mserial;
								
								
						$dataorins=array('item_id'=>$itemins,
						'qty'=>'1',
						'job_card_no'=>$jobcardnumber,
						'mserialno'=>$machserial,
						'order_id'=>$last_id,
						'instrument_addedon'=>date('Y-m-d H:i:s'));
						//echo "<pre>"; print_r($dataorins);exit;
						$this->db->insert('order_instruments',$dataorins);
			
			
			 /** CHECK IF SOURCE JOBCARD ID AVAILABLE **/
                        $ifvaail=$this->checkifsourcejobcardidmorethanone($sourceoid);
                        if($ifvaail==0)
                        {
                            
                            $odupdate=array('order_status'=>'0');
                            
                            $this->db->where('order_id',$sourceoid);
                            $this->db->update('prestogroup_orders',$odupdate);
                            
                        }
                        /** END **/
			
			
			
			
	}
			
		}
		
}


if($isorderready==1 &&  $selforder==1)
	{
	$stocksss=$this->getcurrentstock($itemins);
	if($stocksss>0)
	{
	/** MINUS ONE **/
	$newstock=$stocksss-1;
	$rdyddata=array('stock'=>$newstock);
	$this->db->where('id',$itemins);
	$this->db->update('presto_instruments',$rdyddata);
	}
	}
		
		
}

	
	redirect(page_url.'Reporting/lotorderlist');
}


function diverttoexitingorderfaultyy()
{
	$orderid=$this->uri->segment(3);
	$ready=$this->uri->segment(4);
	$type=$this->input->post('type');
	$reason=$this->input->post('reason');
	$expdate=$this->input->post('expo');
	$dcust=$this->input->post('dcustname');
	$diono=$this->input->post('dcustiono');
	$dtype=$this->input->post('dtype');
	
	if($dtype=='1')
	{
	$resty=$this->db->select('*')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($resty->num_rows()>0)
	{
		
		foreach($resty->result() as $row);

	
			$data=
			array('order_type'=>$row->order_type,
			'marketing_person'=>$row->marketing_person,
			'po_number'=>$row->po_number,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'pincode'=>$row->pincode,
			'email'=>$row->email,
			'mobile_number'=>$row->mobile_number,
			'phone'=>$row->phone,
			'contact_person'=>$row->contact_person,
			'designation'=>$row->designation,
			'internal_order_no'=>$row->internal_order_no,
			'discount'=>$row->discount,
			'order_value_after_discount'=>$row->order_value_after_discount,
			'advance_amount'=>$row->advance_amount,
			'payment_terms'=>$row->payment_terms,
			'installation_charges'=>$row->installation_charges,
			'installation_type'=>$row->installation_type,
			'installation_amount'=>$row->installation_type,
			'packing_type'=>$row->packing_type,
			'packing_charges'=>$row->packing_charges,
			'packing_amount'=>$row->packing_amount,
			'freight_type'=>$row->packing_amount,
			'freight_amount'=>$row->freight_amount,
			'remarks'=>$row->remarks,
			'order_status'=>$row->order_status,
			'closeorder'=>$row->closeorder,
			'closedby'=>$row->closedby,
			'closedOn'=>$row->closedOn,
			'selforder'=>$row->selforder,
			'movetodispatch'=>$row->movetodispatch,
			'movedby'=>$row->movedby,
			'movedOn'=>$row->movedOn,
			'delivery_date'=>$row->delivery_date,
			'updated_on'=>$row->updated_on);
			$res = $this->db->insert('prestogroup_divertedordershisory',$data);
			$last_id = $this->db->insert_id();
	
			
			if($last_id>0)
			{
			if($this->input->post('type')=='2')
			{
				$holddate=date('Y-m-d',strtotime($this->input->post('expo')));
			}else{
				$holddate='';
			}
			
			$divertdata=array('previousorderid'=>$orderid,'divertedorderid'=>$last_id,'type'=>$this->input->post('type'),'hdate'=>$holddate,'reason'=>$this->input->post('remarks'),'divertcustomer'=>$dcust,'divertiono'=>$diono,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('divertedorders',$divertdata);
			if($this->db->affected_rows()>0)
			{
				
					
					$updata=array('order_type'=>strtoupper('SALE'),
					'marketing_person'=>strtoupper(3),
					'company_name'=>strtoupper('PRESTO STANTEST PVT LTD'),
					'address'=>strtoupper('Phase-1, I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana'),
					'pincode'=>strtoupper('121003'),
					'email'=>strtoupper('info@prestogroup.com'),
					'mobile_number'=>strtoupper('1294272727'),
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
					'selforder'=>'1',
					'divertorder'=>'1');
					
					$this->db->where('order_id',$orderid);
					$this->db->update('prestogroup_orders',$updata);
					
					
					$odi=$this->db->select('id,item_id')->from('order_instruments')->where('order_id',$orderid)->where('complete','1')->get();
					if($odi->num_rows()>0)
					{
						//echo "<pre>"; print_r($odi->result());exit;
						foreach($odi->result() as $odi1)
						{
							
							$fact=$this->checkfactorytype($odi1->id,$orderid);
							
							if($fact<>0)
							{
								
								if($fact<>'7')
								{
								
							$itemid=$odi1->item_id;
							
							$qty='1';
							$currst=$this->getcurrentstock($itemid);

							$newstock=$currst+$qty;

							$upnewstock=array('stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('presto_instruments',$upnewstock);
							
							}else
							{
								
							$itemid=$odi1->item_id;
							$jobcardid=$odi1->id;
							$qty='1';
							$currst=$this->getcurrentstock($itemid);

							$newstock=$currst+$qty;
							
							$upnewstock=array('stock'=>$newstock);
							$this->db->where('id',$itemid);
							$this->db->update('presto_instruments',$upnewstock);
							
							
							$datablockckc = array('cancelled_order'=>'1',
							'cancellation_remarks'=>$this->input->post('remarks'));

							$this->db->where('item_id',$itemid);
							$this->db->where('jobcard',$jobcardid);
							$res = $this->db->update('imported_item_blocked',$datablockckc);
									
						}
							
					}
							
							
						}
						
					}
					
					
				
				
				
				
			}else{
				
				echo "UNABLE TO ADD DIVERT HISTORY";exit;
				
				
			}
			
			}else{
				
				echo "CANNOT DIVERT ORDER"; exit;
				
			}




			$this->session->set_flashdata('message','Order Diverted');
			redirect(page_url.'Reporting/lotorderlist');
		
		
		
	}else{
			
		echo "INVALID ORDER ID";exit;
		
		
	}
	
	
	}else{
	//echo "hi";exit;
	if($this->input->post('type')=='2')
			{
				$holddate=date('Y-m-d',strtotime($this->input->post('expo')));
			}else{
				$holddate='';
			}
			
/** JOBCARD WISE **/
$jobcardtodivert=$this->input->post('jobcard');

$jobcardtogetreplaced=$diono;
$itemins=$this->getinstrumentid($jobcardtogetreplaced);

$sourceoid=$orderid;
$destinationoid=$this->getorderid($jobcardtogetreplaced);
/** END **/	

	/** ADD Divert History **/
	$data=array('sourcejobcardid'=>$jobcardtodivert,'sourceorderid'=>$sourceoid,'destinationjobcardid'=>$jobcardtogetreplaced,'destinationorderid'=>$destinationoid,'type'=>$this->input->post('type'),'hdate'=>$holddate,'reason'=>$this->input->post('remarks'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
//	$this->db->insert('divertedjobcards',$data);
	/** end **/
	
	$jobcardnosource=$this->getjobcardno($jobcardtodivert);
	$jobcardnodestination=$this->getjobcardno($jobcardtogetreplaced);
//echo $jobcardnosource.'<br/>'.$jobcardnodestination;exit;
	/** ORDER INSTRUMENT**/
	$data1=array('order_id'=>$destinationoid,'diverted'=>'1','reference'=>$jobcardnodestination);
	
	//	echo "<pre>"; print_r($data1);exit;
		
	$this->db->where('id',$jobcardtodivert);
	$this->db->update('order_instruments',$data1);
	

	
	/** ORDER PLANNING REMARKS **/
	$data2=array('orderid'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('order_planning_remarks',$data2);
	
	/** ORDER PLANNING **/
	$data3=array('order_id'=>$destinationoid);
	$this->db->where('jobcard_id',$jobcardtodivert);
	$this->db->update('order_planning',$data3);
	
	/** ORDER STAGE **/
	$data4=array('orderid'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('order_stage',$data4);	
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data5=array('orderid'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('fmsrollbackhistory',$data5);
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data6=array('order_id'=>$destinationoid);
	$this->db->where('jobcardid',$jobcardtodivert);
	$this->db->update('skipflow',$data6);
	/** END **/

	$checkifitwasplanned=$this->checkifjobcardwasplanned($jobcardtogetreplaced);
	if($checkifitwasplanned>0)
	{
		
	/** ADD Divert History **/
	$data=array('sourcejobcardid'=>$jobcardtogetreplaced,'sourceorderid'=>$destinationoid,'destinationjobcardid'=>$jobcardtodivert,'destinationorderid'=>$sourceoid,'type'=>$this->input->post('type'),'hdate'=>$holddate,'reason'=>'SINCE JOB CARD WAS PLANNED IT GET CONNECTED TO SOURCE','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('divertedjobcards',$data);
	/** end **/

	/** ORDER INSTRUMENT**/
	$data1=array('order_id'=>$sourceoid,'diverted'=>'1','reference'=>$jobcardnosource);
	$this->db->where('id',$jobcardtogetreplaced);
	$this->db->update('order_instruments',$data1);
	
	/** ORDER PLANNING REMARKS **/
	$data2=array('orderid'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('order_planning_remarks',$data2);
	
	/** ORDER PLANNING **/
	$data3=array('order_id'=>$sourceoid);
	$this->db->where('jobcard_id',$jobcardtogetreplaced);
	$this->db->update('order_planning',$data3);
	
	/** ORDER STAGE **/
	$data4=array('orderid'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('order_stage',$data4);	
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data5=array('orderid'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('fmsrollbackhistory',$data5);
	/** END **/
	
	/** ROLL BACK HISTORY **/
	$data6=array('order_id'=>$sourceoid);
	$this->db->where('jobcardid',$jobcardtogetreplaced);
	$this->db->update('skipflow',$data6);
	/** END **/
		
	}else{
		
		
	
		
		
		$this->db->where('id',$jobcardtogetreplaced);
		$this->db->where('order_id',$destinationoid);
		$this->db->delete('order_instruments');
			
			
		if($type=='3')
		{
			/** CREATE NEW ORDER AND ADD JOBCARD **/
			
			
			$resty=$this->db->select('*')->from('prestogroup_orders')->where('order_id',$orderid)->get();
	if($resty->num_rows()>0)
	{
		
		$prestio=$this->db->select('internal_order_no')->from('prestogroup_orders')->where('orderduetodiversion','1')->get();
$io=$prestio->num_rows();
if($io==0)
{
$internalolll="000001";
}else{
foreach($prestio->result() as $prestio1);
$io=$io+1;
$internalolll='00000'.$io;
}

		foreach($resty->result() as $row);

	
			$neworder=
			array('order_type'=>$row->order_type,
			'marketing_person'=>$row->marketing_person,
			'po_number'=>$row->po_number,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'pincode'=>$row->pincode,
			'email'=>$row->email,
			'mobile_number'=>$row->mobile_number,
			'phone'=>$row->phone,
			'contact_person'=>$row->contact_person,
			'designation'=>$row->designation,
			'internal_order_no'=>$internalolll,
			'discount'=>$row->discount,
			'order_value_after_discount'=>$row->order_value_after_discount,
			'advance_amount'=>$row->advance_amount,
			'payment_terms'=>$row->payment_terms,
			'installation_charges'=>$row->installation_charges,
			'installation_type'=>$row->installation_type,
			'installation_amount'=>$row->installation_type,
			'packing_type'=>$row->packing_type,
			'packing_charges'=>$row->packing_charges,
			'packing_amount'=>$row->packing_amount,
			'freight_type'=>$row->packing_amount,
			'freight_amount'=>$row->freight_amount,
			'remarks'=>$row->remarks,
			'order_status'=>$row->order_status,
			'closeorder'=>$row->closeorder,
			'closedby'=>$row->closedby,
			'closedOn'=>$row->closedOn,
			'selforder'=>$row->selforder,
			'movetodispatch'=>$row->movetodispatch,
			'movedby'=>$row->movedby,
			'movedOn'=>$row->movedOn,
			'delivery_date'=>$row->delivery_date,
			'updated_on'=>$row->updated_on,
			'added_on'=>date('Y-m-d H:i:s'),
			'orderduetodiversion'=>'1',
			'diversionreasonref'=>$jobcardnosource);
			$res = $this->db->insert('prestogroup_orders',$neworder);
			$last_id = $this->db->insert_id();
			
				$jobcardnumber = $internalolll." (1/1)";
						
							$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
								$res1 = $query111->num_rows();
								$serial = $res1+1;
								$mserial= sprintf("%03d", $serial);
								
								$machserial=date('my').'-'.$mserial;
								
								
						$dataorins=array('item_id'=>$itemins,
						'qty'=>'1',
						'job_card_no'=>$jobcardnumber,
						'mserialno'=>$machserial,
						'order_id'=>$last_id,
						'instrument_addedon'=>date('Y-m-d H:i:s'));
						//echo "<pre>"; print_r($dataorins);exit;
						$this->db->insert('order_instruments',$dataorins);
			
			
			 /** CHECK IF SOURCE JOBCARD ID AVAILABLE **/
                        $ifvaail=$this->checkifsourcejobcardidmorethanone($sourceoid);
                        if($ifvaail==0)
                        {
                            
                            $odupdate=array('status'=>'0');
                            
                            $this->db->where('order_id',$sourceoid);
                            $this->db->update('prestogroup_orders',$odupdate);
                            
                        }
                        /** END **/
			
			
			
			
	}
			
			
			
			
			
			
			
		}
		
		
		
		
	}

		
		
}

	
	redirect(page_url.'Reporting/lotorderlist');
	
		
	
}






function getcurrentstock($itemid)
{
	$stock=0;
	$resty=$this->db->select('stock')->from('presto_instruments')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		$stock=$resty1->stock;
		
		
	}
	
	return $stock;
	
	
	
}




function checkfactorytype($id,$orderid)
{
	
	$resty=$this->db->select('factory')->from('order_planning')->where('jobcard_id',$id)->where('order_id',$orderid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		
		return $resty1->factory;
		
	}else{
		
		return 0;
	}
}



 function checkifselforder($order_id)
        {
        
            $rqt=$this->db->select('order_id')->from('prestogroup_orders')->where('order_id',$order_id)->where('selforder','1')->get();
            
            return $rqt->num_rows();
            
            
        }


function getallzoneusers($zoneid)
{
	$user=array();
	$resty=$this->db->select('userid')->from('saleszoneusers')->where('zoneid',$zoneid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1)
		{
		$user[]=$resty1->userid;
		}
	}
	
	return $user;
	
}

    
    
    function reordermachine()
{
$instrumentid=$this->uri->segment(3);	
$reorderqty=$this->uri->segment(4);	
$prestio=$this->db->select('internal_order_no')->from('prestogroup_orders')->where('selforder','1')->get();
$io=$prestio->num_rows();
if($io==0)
{
$internalolll="000001";
}else{
foreach($prestio->result() as $prestio1);
$io=$io+1;
$internalolll='00000'.$io;
}
	
	
      
                                
                                
$datareorder=array('order_type'=>strtoupper('SALE'),
'marketing_person'=>strtoupper('3'),
'po_number'=>strtoupper('0'),
'company_name'=>strtoupper('PRESTO STANTEST PVT LTD.'),
'address'=>strtoupper('Phase-1, I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana'),
'pincode'=>strtoupper('121003'),
'email'=>strtoupper('surender@prestogroup.com'),
'mobile_number'=>strtoupper('9958229878'),
'internal_order_no'=>$internalolll,
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
'added_by'=>'3',
'added_on'=>date('Y-m-d H:i:s'),
'selforder'=>'1');
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
$jobcardnumber = $internalolll." (".$plusval."/".$reorderqty.")";

$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
								$res1 = $query111->num_rows();
								$serial = $res1+1;
								$mserial= sprintf("%03d", $serial);
									$machserial=date('my').'-'.$mserial;
							
							 $extserial=$this->chkforextraserial($instruments_attruibute[$x]);
                                
                                if($extserial=='1')
                                {
                                    $mserial= sprintf("%03d", $serial+1);
                                    $extramachserial=date('my').'-'.$mserial;
                                }else
                                {
                                    $extramachserial='';
                                }
                                
                                
$dataorins=array('item_id'=>$instrumentid,
'qty'=>'1',
'job_card_no'=>$jobcardnumber,
'order_id'=>$last_id,
'mserialno'=>$machserial,
'extraserialno'=>$extramachserial,
'instrument_addedon'=>date('Y-m-d H:i:s'));
//echo "<pre>"; print_r($dataorins);exit;
$this->db->insert('order_instruments',$dataorins);

}

}else{

echo "Unable to add reorder quantity";exit;

}

$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Reorder Done</span></div>');
	redirect(page_url.'Reporting/reorderlist');
		

}

/** END REORDER **/
	
public function service_orders(){
     $ip = $_SERVER["REMOTE_ADDR"];
            $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }else{
    $this->load->view('FMS/service_order');
            }
}

public function service_order_list()
	{
	    $prno='';
		$prstage='';
		$podelidate='';
		$scheduler_data = array();
		$query = $this->db->select('a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.order_type','SERVICE')->order_by('a.order_id','desc')->get();
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
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px;width:250px;'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px;width:125px;'>QTY</th><th style='padding:2px 2px 2px 2px;width:125px;'>JOB CARD NO.</th><th style='padding:2px 2px 2px 2px;width:125px;'>PR NO.</th><th style='padding:2px 2px 2px 2px;width:125px;'>PR STAGE</th><th style='padding:2px 2px 2px 2px;width:125px;'>EXP DATE</th>";
$html .= "<th style='padding:2px 2px 2px 2px;width:125px;'>COMPLETION DATE</th></tr>";

			$instrumentsss = array();
			$backgroundcolor='';
			$query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			foreach($query->result() as $instruments){
				$service_date = array();
				if($instruments->complete=='1'){
				/** CHECK FOR THE PLANNING **/
					$checkforplanned=$this->checkforplaningandpr($instruments->jcardid);
					
					if(count($checkforplanned)>0)
					{
						$prno=$checkforplanned['prno'];
						if($prno<>'')
						{
							$prstage=$this->getprstatus($prno);
							$podelidate=$this->checkdeliveryday($prno);
							if($prstage=='COMPLETED')
							{
								$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
							}
							
						}
					
					}
					
					
					/** END **/
				}else{
					$backgroundcolor="background-color:#F4F8FB";
				}
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".$prno."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".$prstage."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".$podelidate."</td>";
					
							$service_comp_date = "";
							$service_date_link = "";
							$getData = $this->db->select('completion_date')
							->from('service_completion_date')
							->where('order_id', $row->order_id)
							->where('job_card_id', $instruments->jcardid)
							->order_by('id', 'desc')
							->get();

							if($getData->num_rows() > 0) {
							foreach ($getData->result() as $rows) {
							$service_date[] = date('d-M-Y', strtotime($rows->completion_date));
							}

							if (count($service_date) > 0) {
							$service_comp_date = implode('<br>', $service_date);
							}
							}

								
				if ($_SESSION['logged_in']['user_id'] == 143) {


					if($getData->num_rows() < 2) {
						$service_date_link = "<a href='javascript:;' id='hide_date".$instruments->jcardid."' onclick='completionDate(".$row->order_id.",".$instruments->jcardid.")'><i class='fa fa-pencil'></i></a>";
					} else {
						$service_date_link = "";
					}
				}
				
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."' id='serviceDateID".$instruments->jcardid."'><span id='service_date".$instruments->jcardid."'>".$service_comp_date."</span><br>".$service_date_link."<br></td>";

				//	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
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
				$planaction="<a href='".page_url."FMS/planorder/".$row->order_id."'><span class='btn btn-sm btn-success'>PLANNED</span></a>";
			}else{
			$planaction="<a href='".page_url."FMS/planorder/".$row->order_id."' class='btn btn-sm btn-warning'>PLAN (".$totjobcardplanned."/".$totjobcard.")</a>";
			}
			
			/** End **/
			$edit = "<a href='".page_url."FMS/edit_serviceorder/".$row->order_id."'><i class='fa fa-pencil'></i></a>";
			
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
			$internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-success'>I/0 Slip</span></a>";
			
			$scheduler_data[] = array('sr_no'=>$i,
									  'generatebutton'=>$internalordersheet,
									  'planorder'=>$planaction,
									  'added_on'=>$addeddate."".$addedtime,
									  'order_type'=>strtoupper($row->order_type),
									  'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
									  'po_number'=>strtoupper($row->po_number),
									  'company_name'=>strtoupper($row->company_name),
									  'contactperson'=>strtoupper($row->contact_person),
									  'designation'=>strtoupper($row->company_name),
									
									  'address'=>strtoupper($row->address),
									  'email'=>strtoupper($row->email),
									  'mobile_number'=>strtoupper($row->mobile_number),
									    'phone'=>strtoupper($row->phone),
									  
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


public function sales_orders(){
     $ip = $_SERVER["REMOTE_ADDR"];
            $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }else{
    $this->load->view('FMS/sales_orders');
            }
}

public function sales_order_list()
	{
		$scheduler_data = array();
		$query = $this->db->select('a.selforder,a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('prestogroup_orders a')->join('system_users b','a.marketing_person=b.user_id','left')->where('a.order_status','1')->where('a.order_type','SALE')->order_by('a.order_id','desc')->get();
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
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>JOB CARD NO.</th><th style='padding:2px 2px 2px 2px;width:10%;'>DIVERTED</th><th style='padding:2px 2px 2px 2px;width:15%;'>REFERENCE</th></tr>";
			$instrumentsss = array();
			
			$query = $this->db->select('a.id as jcardid,a.order_id, a.complete, a.qty, a.item_id, b.id, b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			foreach($query->result() as $instruments){
				if($instruments->complete=='1'){
					$backgroundcolor = "background-color:#10C469; color:white !important; font-weight:bold;";
				}else{
					$backgroundcolor="background-color:#F4F8FB";
				}
				
				/** check for diversion **/
               
               $rdivestyu=$this->db->select('reference,diverted')->from('order_instruments')->where('id',$instruments->jcardid)->get(); 
               if($rdivestyu->num_rows()>0)
               {
                   foreach($rdivestyu->result() as $erdiv);
                   if($erdiv->diverted=='1')
                   {
                       $div="DIVERTED";
                       $ref=$erdiv->reference; 
						/** GET INSTRUMENT NAME IF NOT SAME **/
						$othermachineid="(".$this->fmsmodel->getrefrencemachinename($ref).")";
                   }else
                   {
                        $div="NA";
                        $ref="";
						$othermachineid="";
                   }
                    }else
                    {
                    $div="";
                    $ref="";
					$othermachineid="";
                    }
               
			 
                /** end **/
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."<br/>".$othermachineid."</td>";
				//$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->job_card_no)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($div)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($ref)."</td>";
				//	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
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
				$planaction="<a href='".page_url."FMS/planorder/".$row->order_id."'><span class='btn btn-sm btn-success'>PLANNED</span></a>";
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
			$internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-success'>I/0 Slip</span></a>";
			
			if($row->selforder=='1')
			{
			    $a="PRESTO INTERNAL";
			}else
			{
			    $a='';
			}
			$scheduler_data[] = array('sr_no'=>$i,
									  'generatebutton'=>$internalordersheet,
									  'planorder'=>$planaction,
									  'added_on'=>$addeddate."".$addedtime,
									  'order_type'=>strtoupper($row->order_type)." ".strtoupper($a),
									  'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
									  'po_number'=>strtoupper($row->po_number),
									  'company_name'=>strtoupper($row->company_name),
									  'contactperson'=>strtoupper($row->contact_person),
									  'designation'=>strtoupper($row->company_name),
									
									  'address'=>strtoupper($row->address),
									  'email'=>strtoupper($row->email),
									  'mobile_number'=>strtoupper($row->mobile_number),
									    'phone'=>strtoupper($row->phone),
									  
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


function getionoOldd()
{
	
		$itemid=$this->input->post('itemid');
		$orderid=$this->input->post('orderid');
		$restyui=$this->db->select('a.id,a.job_card_no,b.company_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where('a.item_id',$itemid)->where('a.order_id !=',$orderid)->where('a.finalpacked','0')->get();
		echo "<option value=''>Select Jobcard</option>"; 
		if($restyui->num_rows()>0)
		{
			foreach($restyui->result() as $restyui1)
			{
				
				echo "<option value='".$restyui1->id."'>".$restyui1->job_card_no."-".$restyui1->company_name."</option>";
			}
			
		}

}



function getiono()
{
	
		
		$itemid=$this->input->post('itemid');
		$restyueu=$this->fmsmodel->checkifanyothermachineissimilar($itemid);
		if(count($restyueu)>0)
		{
			$result = "'".implode ( "', '", $restyueu ) . "'";
			$final=$result.",'".$itemid."'";
		}else{
			
			$final="'".$itemid."'";
		}
	
		$orderid=$this->input->post('orderid');
		$restyui=$this->db->select('a.id,a.job_card_no,b.company_name')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->where_in('a.item_id',$final,false)->where('a.order_id !=',$orderid)->where('a.finalpacked','0')->get();
		echo "<option value=''>Select Jobcard</option>"; 
		if($restyui->num_rows()>0)
		{
			foreach($restyui->result() as $restyui1)
			{
				
				echo "<option value='".$restyui1->id."'>".$restyui1->job_card_no."-".$restyui1->company_name."</option>";
			}
			
		}

}


function getinstrumentid($jbcard)
{
	$rtyui=$this->db->select('item_id')->from('order_instruments')->where('id',$jbcard)->get();
	if($rtyui->num_rows()>0)
	{
		foreach($rtyui->result() as $rtyui1);
		
		return $rtyui1->item_id;
		
		
	}else{
		
		echo "NO ITEM FOUND";exit;
	}
	
	
}


function getorderid($jbid)
{
	$restuii=$this->db->select('order_id')->from('order_instruments')->where('id',$jbid)->get();
	if($restuii->num_rows()>0)
	{
		
		foreach($restuii->result() as $restuii1);
		
		return $restuii1->order_id;
		
	}else{
		
		
		echo "ORDER ID NOT FOUND";exit;
	
	
	}
}

function checkifjobcardwasplanned($jbcard)
{	
	$restyuii=$this->db->select('id')->from('order_planning')->where('jobcard_id',$jbcard)->get();
	
	return $restyuii->num_rows();
	
	
}


function getjobcardno($jbcard)
{
	$restyu=$this->db->select('job_card_no')->from('order_instruments')->where('id',$jbcard)->get();
	if($restyu->num_rows()>0)
	{
		foreach($restyu->result() as $restyu1);


		return $restyu1->job_card_no;

	}else{
		echo "JOBCARD NOT FOUND";exit;
	}		
	
}


function checkifsourcejobcardidmorethanone($orderid)
{
    $restyui=$this->db->select('item_id')->from('order_instruments')->where('order_id',$orderid)->get();
    return $restyui->num_rows();
    
    
    
    
}


function chkforextraserial($insid)
{
    $restyu=$this->db->select('id')->from('presto_instruments')->where('id',$insid)->where('extraserial','1')->get();
    
    return $restyu->num_rows();
    
    
}


function cancelorder()
{
	$this->load->view('FMS/canceljobcard');	
	
}

function cancelexistingprder()
{
	
	$orderid=$this->uri->segment(3);
	$jbid=$this->input->post('jobcard');
	$rmk=$this->input->post('remarks');

	for($i=0;$i<count($jbid);$i++)
	{
		$jobcardid=$jbid[$i];
		
		$prestio=$this->db->select('internal_order_no')->from('prestogroup_orders')->where('orderduetocancellation','1')->get();
			$io=$prestio->num_rows();
			if($io==0)
			{
			$internalolll="0000001";
			}else{
			foreach($prestio->result() as $prestio1);
			$io=$io+1;
			$internalolll='000000'.$io;
			}
		$restyu=$this->checkifjobcardwascompleted($jobcardid);
		if($restyu==1)
		{ 
		/** Ready **/
		
				$updata=array('order_type'=>strtoupper('SALE'),
				    'added_on'=>date('Y-m-d H:i:s'),
					'marketing_person'=>strtoupper(3),
					'company_name'=>strtoupper('PRESTO STANTEST PVT LTD'),
					'address'=>strtoupper('Phase-1, I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana'),
					'internal_order_no'=>$internalolll,
					'pincode'=>strtoupper('121003'),
					'email'=>strtoupper('info@prestogroup.com'),
					'mobile_number'=>strtoupper('1294272727'),
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
					'remarks'=>'PRESTO INTERNAL ORDER DUE TO CANCELLATION',
					'selforder'=>'1',
					'orderduetocancellation'=>'1',
					'order_status'=>'1',
					'closeorder'=>'1',
					'closedby'=>'1',
					'movetodispatch'=>'1',
					'movedby'=>'3',
					'movedOn'=>date('Y-m-d H:i:s'),
					'closedby'=>'1',
					'closedOn'=>date('Y-m-d H:i:s'));
					/** END **/

			}else{

					$updata=array('order_type'=>strtoupper('SALE'),
					'added_on'=>date('Y-m-d H:i:s'),
					'marketing_person'=>strtoupper(3),
					'company_name'=>strtoupper('PRESTO STANTEST PVT LTD'),
					'address'=>strtoupper('Phase-1, I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana'),
					'internal_order_no'=>$internalolll,
					'pincode'=>strtoupper('121003'),
					'email'=>strtoupper('info@prestogroup.com'),
					'mobile_number'=>strtoupper('1294272727'),
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
					'remarks'=>'PRESTO INTERNAL ORDER DUE TO CANCELLATION',
					'selforder'=>'1',
					'orderduetocancellation'=>'1',
					'order_status'=>'1',
					'closeorder'=>'0',
					'closedby'=>'0',
					'movetodispatch'=>'0',
					'movedby'=>'0',
					'movedOn'=>'',
					'closedby'=>'0',
					'closedOn'=>'');



			}				
			
			
			//echo "<pre>"; print_r($updata);exit;
			$this->db->insert('prestogroup_orders',$updata);
			$neworderid=$this->db->insert_id();

			/** GENERATE HISTORY **/
			$canceljb=array('oldorderid'=>$orderid,'neworderid'=>$neworderid,'jobcardid'=>$jobcardid,'canceledby'=>$_SESSION['logged_in']['user_id'],'canceledreason'=>$rmk,'cancelOn'=>date('Y-m-d H:i:s'));

			$this->db->insert('canceljobcard',$canceljb);

			/** CHANGE ORDER ID OF JOBCARD **/
			$odchange=array('order_id'=>$neworderid);
			$this->db->where('order_id',$orderid);
			$this->db->where('id',$jobcardid);
			$this->db->update('order_instruments',$odchange);
			/** END **/
			
			/** CHANGE PLANNING DATA **/
			$plndata=array('order_id'=>$neworderid);
			$this->db->where('jobcard_id',$jobcardid);
			$this->db->where('order_id',$orderid);
			$this->db->update('order_planning',$plndata);
			/** END **/
			
			/** CHANGE ORDER STAGE DATA **/
			
			$odstage=array('orderid'=>$neworderid);
			$this->db->where('jobcardid',$jobcardid);
			$this->db->where('orderid',$orderid);
			$this->db->update('order_stage',$odstage);
			/** END **/
			
			$ifavail=$this->getoldjbjobcard($orderid);
			if($ifavail==0)
			{
			    $plln=array('order_status'=>'0');
			    $this->db->where('order_id',$orderid);
			    $this->db->update('prestogroup_orders',$plln);
			}
			
			
		
	}
	
	$this->session->set_flashdata('message','Jobcard Cancelled');
	redirect(page_url.'Reporting/lotorderlist');
	
}


function checkifjobcardwascompleted($jobcardid)
{
	
	$restyuiooii=$this->db->select('id')->from('order_instruments')->where('id',$jobcardid)->where('complete','1')->get();
	
	return $restyuiooii->num_rows();
	
	
}


function getoldjbjobcard($oddid)
{
    
    $resty=$this->db->select('id')->from('order_instruments')->where('order_id',$oddid)->get();
    
    return $resty->num_rows();
    
}

	function gettotaldaysforpend($status,$startdate,$enddate)
{
   
	$startdate=date('Y-m-d',strtotime($startdate));
	
		$enddate=date('Y-m-d');

	
$date1 = new DateTime($startdate);
$date2 = new DateTime($enddate);
$interval = $date1->diff($date2);
$totinterval=$interval->days;

/** Check for Holiday **/
$betweendates=array();
					for ($g=strtotime($startdate); $g<=strtotime($enddate); $g+=86400) {  
						$betweendates[]= date("Y-m-d", $g);  
					} 
					$alldates="'" . implode ( "','", $betweendates ) . "'";
				
				if(count($betweendates)>0)
				{
					$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
					$holiday=$ifholiday->num_rows();
					
				}else{ $holiday=0;  }
				
			
return $totinterval-$holiday;
/** END **/

	
}

function getdaysdiff($start,$end)
{
    $date1 = new DateTime($start);
$date2 = new DateTime($end);
$interval = $date1->diff($date2);
$totinterval=$interval->days;

return $totinterval;
    
}




public function serviceorder(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('order_type', 'order_type', 'required|trim');
	$this->form_validation->set_rules('person_name', 'person_name', 'required|trim');
	$this->form_validation->set_rules('po_number', 'po_number', 'required|trim');
	$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
//	$this->form_validation->set_rules('email_id', 'email_id', 'required|trim');
	$this->form_validation->set_rules('mobile_number', 'mobile_number', 'required|trim');
	$this->form_validation->set_rules('payment_term', 'payment_term', 'required|trim');
	$this->form_validation->set_rules('installation_charges', 'installation_charges', 'required|trim');
	$this->form_validation->set_rules('status', 'status', 'required|trim');
	
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/service_order');
			}else
		{
		    
		    $serviceorder=$this->input->post('serviceorder');
			if($serviceorder==0)
			{
				$serord=0;
				$serodid=0;
			}else{
				
				$serord=1;
				$serodid=$serviceorder;
			}
			
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
				  'phone'=>strtoupper($this->input->post('phone')),
			'contact_person'=>strtoupper($this->input->post('contactperson')),
			'designation'=>strtoupper($this->input->post('designation')),
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
			'added_on'=>$added_time,
			'servicerepairorder'=>$serord,
			'servicerepairid'=>$serodid );
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
							$query11 = $this->db->select('id')->from('order_instruments')->where('order_id',$last_id)->get();
							$res = $query11->num_rows();
							$plusval = $res+1;

							$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
							$res1 = $query111->num_rows();
							$serial = $res1+1;
							$mserial= sprintf("%03d", $serial);
							$machserial=date('my').'-'.$mserial;
							
							 $extserial=$this->chkforextraserial($instruments_attruibute[$x]);
                                
                                if($extserial=='1')
                                {
                                    $mserial= sprintf("%03d", $serial+1);
                                    $extramachserial=date('my').'-'.$mserial;
                                }else
                                {
                                    $extramachserial='';
                                }
								
								$jobcardnumber = $orderid." (".$plusval."/".$tags1.")";
								 $data=array('item_id'=>$instruments_attruibute[$x],
							'qty'=>$qty,
							'job_card_no'=>$jobcardnumber,
							'order_id'=>$last_id,
							'mserialno'=>$machserial,
							'extraserialno'=>$extramachserial,
							'instrument_addedon'=>$added_time);
			
							$this->db->insert('order_instruments',$data);
							
						   
						}
					}
					}
					}
				
				
				if($serviceorder<>0)
					{
						$repdata=array('service'=>'1','servicedoneon'=>date('Y-m-d H:i:s'),'servicedoneby'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('id',$serodid);
						$this->db->update('service_repair_request',$repdata);
						
					}
					
				$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-danger">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/service_orders');
				}
		   
			
			}
}




function servicepartsdetail()
{
	$mid=$this->uri->segment('3');
	$requiredstock=$this->input->post('qty');
	$html='';
	$resty=$this->db->select('a.fincode,a.specification,a.id as partid,a.picture,a.part,a.unit,a.current_stock,b.instruments_name,a.min_stock')->from('machine_parts_with_picture a')->join('presto_instruments b','a.fincode=b.fincode')->where('a.id',$mid)->get();
if($resty->num_rows()>0)
{
	foreach($resty->result() as $restyu1)
	{
		$uname=$this->storemodel->getunit($restyu1->unit);
		
		/** Get Available Stock **/
		$restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$restyu1->partid)->where('active','1')->get();
		foreach($restblockedst->result() as $restblockedstock);
		$blockedparts=$restblockedstock->blockstock;
		/** END **/
		if($restyu1->current_stock==0)
		{
		$finalstock=0;	
		}else{
		$finalstock=$restyu1->current_stock-$blockedparts;
		}
		if(trim($finalstock)==0)
		{
			$a="checked";
		}else{
			
			$a="";
		}
		
		/** Get final stock **/
		
		if($finalstock>=$requiredstock)
		{
			$reqstock=$requiredstock;
			$a='';
			$ar="";
		}else{
			
			$reqstock=$requiredstock-$finalstock;
			$a="checked";
			$ar="readonly";
		}
		
		/** END **/

	if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.jpg')){
			    
			 $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
			}else if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.JPG')){
			    
			 $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
			}else if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.jpeg')){
			    $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
		    }else if(file_exists(UPLOADPATH.'product_item/'.$restyu1->fincode.'.JPEG')){
			    
			 $path=page_url.'image_bank/product_item/'.$restyu1->fincode.".jpg";
			}else{
			 $path=page_url.'upload/image404.png'; 
			}
			
			

		
		//$qtytoraise=floatval($restyu1->bomqty);
			$html.='<tr>
			<td><input type="radio" class="partselection" name="itemselected[]" id="selectcheckbox'.$restyu1->partid.'" value="'.$restyu1->partid.'" '.$a.'  onchange="checkifcheckedforpr('.$restyu1->partid.');"></td>
			<td>'.strtoupper($restyu1->instruments_name).'</td>
			<td>'.strtoupper($restyu1->fincode).'</td>
			<td>'.strtoupper($restyu1->specification).'</td>
			<td><input type="text" class="form-control" class="qtyforpart" name="qtyparts'.$restyu1->partid.'" id="partsqty'.$restyu1->partid.'" placeholder="QTY" value="'.$reqstock.'" readonly style="display:none;width:50%">&nbsp;<span id="udata'.$restyu1->partid.'" style="display:none">'.strtoupper($uname).'</span></td>
			<td><span style="color:red;">'.$finalstock.' '.strtoupper($uname).'</span><input type="hidden" name="unit'.$restyu1->partid.'" id="unit'.$restyu1->partid.'" value="'.$restyu1->unit.'"><input type="hidden" name="currentstock'.$restyu1->partid.'" id="currentstock'.$restyu1->partid.'" value="'.$finalstock.'"><input type="hidden" name="masterid'.$restyu1->partid.'" id="masterid'.$restyu1->partid.'" value="'.$restyu1->partid.'"></td>
			<td><img src="'.$path.'" width="50px"></td>
			<td><textarea class="form-control" style="resize:none;display:none" name="prreason'.$restyu1->partid.'" id="prreason'.$restyu1->partid.'" placeholder="Reason of raising Pr since item is in stock"></textarea></td>
			</tr>';
		
		
	}
	
}else{
	

$html.='<tr>
			<td colspan="3">NO PARTS AVAILABLE</td>
			</tr>';	
	
}

echo $html;
	
	
}



function getstockandblockeditemlist()
{
	$jobcardid=$this->input->post('inst');
	$instfinc=$this->getinstrumentfincode($jobcardid);
	if($instfinc<>'')
	{
	
	$bomstock=$this->getstockandidfrombombyfincode($instfinc);
		if(count($bomstock)>0)
		{
		foreach($bomstock as $bomstock1);

		$itemid=$bomstock1->id;
		$current_stocks=$bomstock1->current_stock;
		}else{
			
			$itemid=0;
			$current_stocks=0;
		}
		
		/** GET BLOCKED LIST **/
		$html='';
		

		$restyur=$this->db->select('a.id,a.jobcardid,b.job_card_no,a.stock')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id')->where('a.active','1')->where('a.itemid',$itemid)->get();
		if($restyur->num_rows()>0)
		{
			$html.="<h4 class='text-center'>Blocked Item List</h4>";
			$html.="<table border='1' style='width:100%;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px;text-align:center;background-color:green;color:white;' colspan='3'>Current Stock - ".$current_stocks."</th></tr><tr style='background-color:white'><th style='padding:2px 2px 2px 2px;text-align:center;'>Sno.</th><th style='padding:2px 2px 2px 2px;text-align:center;'>JOB CARD NO.</th><th style='padding:2px 2px 2px 2px;text-align:center;'>QTY</th></tr>";
			
			$r=1;
			foreach($restyur->result() as $restyur1)
			{
			
			$html.="<tr>
			<td style='text-align:center;'>".$r."</td>
			<td style='text-align:center;'>".$restyur1->job_card_no."</td>
			<td style='text-align:center;'>".floatval($restyur1->stock)."</td>
			</tr>";
			
			$r++;
			}
			
			
			$html.="</table>";
		}
		
		
		echo $html;exit;
		/** END **/
		
	}else
	{
		echo 'Item not matched/available'; exit;
	}
	
	
	
}


function getstockandidfrombombyfincode($fincode)
	{
		
		$restyu=$this->db->select('id,current_stock')->from('machine_parts_with_picture')->where('fincode',$fincode)->get();
		return $restyu->result();
		
	}
	
	

function getinstrumentfincode($inst)
{
	$restyeueiu=$this->db->select('a.fincode')->from('presto_instruments a')->join('order_instruments b','a.id=b.item_id')->where('b.id',$inst)->get();
	if($restyeueiu->num_rows()>0)
	{
		foreach($restyeueiu->result() as $restyeueiu1);
		
		return $restyeueiu1->fincode;
		
	}else{
		
		return '';
	}
	
}


function edit_serviceorder()
	{
		
	$this->load->view('FMS/edit_serviceorders');
		
	}
	
	
	
		
	public function update_serviceorderinfo(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('order_type', 'order_type', 'required|trim');
	$this->form_validation->set_rules('person_name', 'person_name', 'required|trim');
	$this->form_validation->set_rules('po_number', 'po_number', 'required|trim');
	$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
//	$this->form_validation->set_rules('email_id', 'email_id', 'required|trim');
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
				  'contact_person'=>strtoupper($this->input->post('contactperson')),
			'designation'=>strtoupper($this->input->post('designation')),
			'phone'=>strtoupper($this->input->post('phone')),
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
			'internal_order_no'=>$this->input->post('internal_order_no'),
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
							$qty=$_REQUEST['qty'];
							
								$query11 = $this->db->select('id')->from('order_instruments')->where('order_id',$last_id)->get();
								$result = count($query11->result());
								$res = $query11->num_rows();
								$grandtotal = $result+1;
								$plusval = $res+1;
								$jobcardnumber = $io." (".$plusval."/".$grandtotal.")";
								
								
								$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
								$res1 = $query111->num_rows();
								$serial = $res1+1;
								$mserial= sprintf("%03d", $serial);
								
									$machserial=date('my').'-'.$mserial;
							
								 $extserial=$this->chkforextraserial($instruments_attruibute[$x]);
                                
                                if($extserial=='1')
                                {
                                    $mserial= sprintf("%03d", $serial+1);
                                    $extramachserial=date('my').'-'.$mserial;
                                }else
                                {
                                    $extramachserial='';
                                }
								
								
								 $data=array('item_id'=>$instruments_attruibute[$x],
							'qty'=>'1',
							'job_card_no'=>$jobcardnumber,
							'mserialno'=>$machserial,
							'extraserialno'=>$extramachserial,
							'order_id'=>$last_id);
							$this->db->insert('order_instruments',$data);
							
						   
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
				redirect(page_url.'FMS/serviceorder');
				}
		   
			
			}
}





function checkforplaningandpr($jobcardid)
{
	$artyyu=array();
	$Reseyrur=$this->db->select('id')->from('order_planning')->where('factory','9')->where('jobcard_id',$jobcardid)->get();
	if($Reseyrur->num_rows()>0)
	{
	$restyure=$this->db->select('stock,prno')->from('kitting_bop_details')->where('stock','0')->where('jobcardid',$jobcardid)->get();
	
	if($restyure->num_rows()>0)
	{
		foreach($restyure->result() as $artyyu1)
		{
			
			$artyyu['prno']=$artyyu1->prno;
			$artyyu['stock']=$artyyu1->stock;
			
		}
		
		
		
	}
	
	
	}
	return $artyyu;


	
	
}

 
function getprstatus($prno)
{
	$prstatus='';
	$poraisedate='';
	$resteyur=$this->db->select('id,approved,completed,gateentrycomplete,pono,vendor,addedOn')->from('purchase_order')->where('prno',$prno)->get();
	if($resteyur->num_rows()>0)
	{
		foreach($resteyur->result() as $resteyur1);
		if($resteyur1->completed=='1')
		{
			$prstatus= "COMPLETED"; 
		}
		
		
		if($resteyur1->gateentrycomplete=='1')
		{
			$prstatus= "QC MRN"; 
		}
		
		if($resteyur1->approved=='1')
		{
			$prstatus= "PO APPROVED"; 
		}else{
			$prstatus= "UNAPPROVED PO";
		}
		
	
	}else{
		
		$prstatus= "PO YET TO BE RAISED";
	}



        return $prstatus;

	
	
}
  


function getporaisedate($addedOn,$vendor,$prno)
{
    
    $enddate='';
    $Restrey=$this->db->select('deliverytime,otherdeliverytime')->from('vendors')->where('id',$vendor)->get();
    
    if($Restrey->num_rows()>0)
    {
        foreach($Restrey->result() as $Restrey1);
        if($Restrey1->deliverytime=='Other')
        {
        $deliday=$Restrey1->otherdeliverytime;
        }else
        {
        $deliday=$Restrey1->deliverytime;   
        }
    
   
        $addedOn=date('Y-m-d',strtotime($addedOn));
     

         $enddate =date('d-m-Y',strtotime($addedOn . ' +'.$deliday.' day'));
       
        
    }
    
        return $enddate;
    
    
}




function checkdeliveryday($prno)
{
    $poraisedate='';
    $resteyur=$this->db->select('id,approved,completed,gateentrycomplete,pono,vendor,addedOn')->from('purchase_order')->where('prno',$prno)->get();
    if($resteyur->num_rows()>0)
    {
        foreach($resteyur->result() as $resteyur1);
        $poraisedate=$this->getporaisedate($resteyur1->addedOn,$resteyur1->vendor,$prno);
        
    }
    
        return $poraisedate;
		  
}


function genrateproductionhelpticket()
{
   
    $jobcardid=$this->uri->segment(3);
    $formid=51;
    $yourname=$this->getusername($_SESSION['logged_in']['user_id']);
    $instrument=$this->getinstrumentname($jobcardid);
    $planneddate=$this->getformdata();
    
    $des=$instrument." Bom is incorrect please resolve";
     $ar=array("yourname"=>$yourname,"descriptionofissue"=>$des,"remarks"=>$des,"uploadimageorvideo(ifany)"=>'');
     
     $rem=json_encode($ar);
    
    $formresp=$this->getnewresponseid();
    $l=strlen($formresp);
    if($l==1)
    {
        $tra='00';
    }else if($l==2)
    {
        $tra='0';
    }else if($l>2)
    {
        $tra='';
    }
    $newnum=$formresp+1;
    $newnum='HTP'.$newnum;
    $data=array('form_id'=>'51','responseid'=>$newnum,'formdata'=>$rem,'work_status'=>'0','added_by'=>$_SESSION['logged_in']['user_id'],'added_on'=>date('Y-m-d H:i:s'),'planned_date'=>date('Y-m-d H:i:s'));
    $this->db->insert('dynamic_form_data',$data); 
    if($this->db->affected_rows()>0)
    {
    echo "1";
    }else
    {
         echo "0";
    }
    
    
}



function getusername($userid)
{
    $Resteyue=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->get();
    if($Resteyue->num_rows()>0)
    {
        foreach($Resteyue->result() as $Resteyue1);
        
        return $Resteyue1->first_name.' '.$Resteyue1->last_name;
    }else
    {
        return '';
    }
 
}


function getinstrumentname($userid)
{
    $Resteyue=$this->db->select('a.item_id,b.instruments_name')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.id',$userid)->get();
    if($Resteyue->num_rows()>0)
    {
        foreach($Resteyue->result() as $Resteyue1);
        return $Resteyue1->instruments_name;
        
    }else
    {
        return '';
    }
 
}


function getformdata()
{
  $query = $this->db->select('tatdays, tattime')->from('dynamic_forms')->where('id','51')->get();
	if($query->num_rows()>0){
		foreach($query->result() as $row);
		$totaldays = $row->tatdays;
		$totaltime = $row->tattime;
		$currenttime = date('H:i');
		$todaysdate = date('Y-m-d');
		if($totaltime){
		$time = date('H:i:s', strtotime($currenttime.'+'.$totaltime.' hour'));
		}else{
			$time = date('H:i:s');
		}
		
			$officestarttime=date('Y-m-d',strtotime($todaysdate."+1 days"))." "."9:30";
		$officestime="9:30";
        $officeendtime=date('Y-m-d')." 18:00";
        $officeetime="18:00";
        
        
		if($totaldays>0){
			
			  $planneddate = date('Y-m-d', strtotime("+".$totaldays." day", strtotime($todaysdate)));
		  $finaldate=$planneddate." ".$currenttime;
		
			
		}else{
			
			$previoussteptime=date('H:i',strtotime($currenttime));
				$tattime = date('Y-m-d H:i', strtotime($previoussteptime.'+'.$totaltime.' hour'));
				
				
			//	echo $officeendtime.'<br/>'.$tattime;exit;
				if(strtotime($officeendtime)<strtotime($tattime))
					{
					   
						    
						    $timediff=round(abs(strtotime($tattime)-strtotime($officeendtime))/60,2);
						  
						    $TATDATE=date('Y-m-d',strtotime($todaysdate."+1 days"));
						    $newtime=date('g:i A',strtotime($officestarttime.'+'.$timediff.' minutes'));
						    
						    
						}else
						{
						   
                            $TATDATE=$todaysdate;
                            $newtime=date('g:i A',strtotime($tattime)); 
						}
			    
			$finaldate = $TATDATE." ".$newtime;
		}
		
			$finaltime=date('H:i:s',strtotime($finaldate));
		$planneddate=date('Y-m-d',strtotime($finaldate));

		
			$q = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$planneddate)->get();
			if($q->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($planneddate)));
				$q1 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($q1->num_rows>0){
					$nextdate1 = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
					$q2 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate1)->get();
					if($q2->num_rows>0){
						$finaldate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate1)));
					}else{
						$finaldate=$nextdate1." ".$finaltime;
					}
				}else{
					$finaldate = $nextdate." ".$finaltime;
				}
			}else{
				$finaldate= $planneddate." ".$finaltime;
			
			}
			
			
		$finaldate =  $finaldate; 
	}
	
	
	return $finaldate;
	
	
    
}

function getnewresponseid()
{
    $int='';
    $resteye=$this->db->select('responseid')->from('dynamic_form_data')->where('form_id','51')->order_by('id','DESC')->limit(1)->get();
    if($resteye->num_rows()>0)
    {
        foreach($resteye->result() as $resteye1);
    
        $int = $str = preg_replace('/\D/', '', $resteye1->responseid);
        
        
        
    }
    
    return $int;
}



function getcurrentrunningorders()
{
	
	$html='';
	$inst=$this->input->post('inst');
	$orderid=$this->input->post('orderid');
	$jobcardid=$this->getjobcardid($inst);
	$extrastock=$this->checkforextrastock($jobcardid);
	$restye=$this->getcurrentruningod($jobcardid);
	if(count($extrastock)>0)
	{
		
		foreach($extrastock as $extrastock1);
		$exstock=$extrastock1->stock;
		$insname=$extrastock1->instruments_name;
	}else{
		
		$exstock='';;
		$insname='';
	}
	
					$html.= '<table class="table table-bordered" style="width:60%;">
					<tr>
					<th colspan="4" class="text-center">Running Presto Orders for '.$insname.' | Extra Stock- '.$exstock.'</th>


					</tr>
					<tr>
					<th>Sno.</th>
					<th>Jobcard No.</th>
					<th>Party Name</th>
					<th>Divert</th>
					</tr>';

					if(count($restye)>0)
					{
						$i=1;
						foreach($restye as $data)
						{
							$diver="<a href='".page_url."FMS/jobcardwisedivert/".$orderid."/1/".$inst."' class='btn btn-success btn-xs'>Divert</a>";
						
							$html.='<tr>
							<td>'.$i.'</td>
							<td>'.$data->job_card_no.'</td>
							<td>'.$data->company_name.'</td>
							<td>'.$diver.'</td>';
						$i++;
						}
						
					
					}else{
						
						$html.='<tr>
							<td colspan="4">No Data Available</td>';
						
						
					}

					$html.='</tr>
					
					</table>';
								

echo $html;exit;								
	
}
  
  
  function checkforextrastock($inst)
  {
	$Resteyue=array();  
	  $rest=$this->db->select('stock,instruments_name')->from('presto_instruments')->where('id',$inst)->get();
	  if($rest->num_rows()>0)
	  {
		  return $rest->result();
	  }else{
	  
	  return $Resteyue;
	  }
  }
  
  
  function getcurrentruningod($inst)
  {
	  $as=array();
	  $Restey=$this->db->select('a.company_name,b.job_card_no,b.id')->from('prestogroup_orders a')->join('order_instruments b','a.order_id=b.order_id')->where('b.finalpacked','0')->where('closeorder','0')->where('selforder','1')->where('item_id',$inst)->get();
	  if($Restey->num_rows()>0)
	  {
		  return $Restey->result();
	  }else{
		  
		  return $as;
	  }
	  
	  
  }
  
  function getjobcardid($inst)
  {
	  $iid=0;
	  $restyy=$this->db->select('item_id')->from('order_instruments')->where('id',$inst)->get();
	  if($restyy->num_rows()>0)
	  {
		  foreach($restyy->result() as $restyy1);
		  
		  return $restyy1->item_id;
		  
	  }else{
		  return $iid;
	  }
	  
  }
  
  
  function jobcardwisedivert()
  {
	  $this->load->view('FMS/jobcardwisedivert');
	  
  }


    function getordertype($odid)
    {
        $od='';
        $reste=$this->db->select('order_type,selforder')->from('prestogroup_orders')->where('order_id',$odid)->get();
        if($reste->num_rows()>0)
        {
            foreach($reste->result() as $reste1);
            if($reste1->selforder=='1')
            {
                $od="PRESTO INTERNAL";
            }else
        {
            $od=strtoupper($reste1->order_type);
            
        }
        }
        
        return $od;
        
    }



	function similiarmachinefordiversion()
{
	$this->load->view('FMS/similardivertedmachines');
}

function updatesimilardiversion()
{
	$uri=$this->uri->segment(3);
	$this->form_validation->set_rules('machines[]', 'Machine', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/similardivertedmachines');
		}else{
			
			$mach=$this->input->post('machines');
			
			for($i=0;$i<count($mach);$i++)
			{
				$similarmach=$mach[$i];
				$source=$uri;
				
				$data=array('basemachine'=>$source,'similarmachine'=>$similarmach,'addedOn'=>date('Y-m-d H:i;s'),'addedBy'=>$user_id);
				$this->db->insert('similardiversionmachines',$data);
				
				
			}
			
		}
		

		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Record Updated</span></div><br/>');
		redirect(page_url.'/FMS/instruments');
	
}


  function addos()
{
	$this->load->view('FMS/addos');
	
}

function updatedos()
{
	$seri=$this->input->post('serial');
	$machi=$this->uri->segment('3');
	for($i=0;$i<count($seri);$i++)
	{
		
		$data=array('itemid'=>$machi,'stock'=>'1','serialno'=>$seri[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('instrumentopeningstock',$data);
	}
	
		$this->db->set('stock', 'stock+'.count($seri), false);
		$this->db->where('id',$machi);
		$this->db->update('presto_instruments');
		
		$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Record Updated</span></div><br/>');
		redirect(page_url.'FMS/addos/'.$machi);

	
}


function getimsitems()
{

		$searchtrm= $_GET['q'];
	

		$query = $this->db->select('id, part')->from('machine_parts_with_picture')->like('part',$searchtrm,'both',false)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments){

		$json[] = array('id'=>$instruments->id, 'text'=>$instruments->part);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}

		echo json_encode($json);


}


public function ti_order(){
    
     $ip = $_SERVER["REMOTE_ADDR"];
            $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }


	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('order_type', 'order_type', 'required|trim');
	$this->form_validation->set_rules('person_name', 'person_name', 'required|trim');
	$this->form_validation->set_rules('po_number', 'po_number', 'required|trim');
	$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
//	$this->form_validation->set_rules('email_id', 'email_id', 'required|trim');
	$this->form_validation->set_rules('mobile_number', 'mobile_number', 'required|trim');
	$this->form_validation->set_rules('payment_term', 'payment_term', 'required|trim');
	$this->form_validation->set_rules('installation_charges', 'installation_charges', 'required|trim');
	$this->form_validation->set_rules('status', 'status', 'required|trim');
	
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('FMS/ti_order');
			}else
		{
		    
		    $serviceorder=$this->input->post('serviceorder');
			if($serviceorder==0)
			{
				$serord=0;
				$serodid=0;
			}else{
				
				$serord=1;
				$serodid=$serviceorder;
			}
			
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
				  'phone'=>strtoupper($this->input->post('phone')),
			'contact_person'=>strtoupper($this->input->post('contactperson')),
			'designation'=>strtoupper($this->input->post('designation')),
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
			'added_on'=>$added_time,
			'servicerepairorder'=>$serord,
			'servicerepairid'=>$serodid );
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
								
								$query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
								$res1 = $query111->num_rows();
								$serial = $res1+1;
								$mserial= sprintf("%03d", $serial);
									$machserial=date('my').'-'.$mserial;
							
							 $extserial=$this->chkforextraserial($instruments_attruibute[$x]);
                                
                                if($extserial=='1')
                                {
                                    $mserial= sprintf("%03d", $serial+1);
                                    $extramachserial=date('my').'-'.$mserial;
                                }else
                                {
                                    $extramachserial='';
                                }
								
								$jobcardnumber = $orderid." (".$plusval."/".$totalqty.")";
								 $data=array('item_id'=>$instruments_attruibute[$x],
							'qty'=>'1',
							'job_card_no'=>$jobcardnumber,
							'order_id'=>$last_id,
							'mserialno'=>$machserial,
							'extraserialno'=>$extramachserial,
							'instrument_addedon'=>$added_time);
							$this->db->insert('order_instruments',$data);
							}
						   
						}
					}
					}
					}
				
				
				if($serviceorder<>0)
					{
						$repdata=array('service'=>'1','servicedoneon'=>date('Y-m-d H:i:s'),'servicedoneby'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('id',$serodid);
						$this->db->update('service_repair_request',$repdata);
						
					}
					
				$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-danger">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'FMS/order');
				}
		   
			
			}
}


function filtered_instrument()
{

	redirect(page_url.'FMS/instruments/'.$this->uri->segment(3).'/'.$this->uri->segment(4));
}


function salesforceorder()
{

$this->load->view('FMS/salesforceorder');
}


public function sforder_list()
	{
		$scheduler_data = array();
		$type=$this->uri->segment(3);
		$this->db->select('a.orderduetodiversion,a.diversionreasonref,a.contact_person,a.designation,a.phone,a.order_id, a.order_type, a.marketing_person,a.po_number, a.company_name, a.address, a.email, a.mobile_number, a.internal_order_no, a.discount, a.order_value_after_discount, a.advance_amount, a.payment_terms, a.installation_charges, a.order_status, a.added_on,a.packing_type, a.installation_type, b.user_id, b.title, b.first_name, b.last_name, a.installation_amount, a.packing_charges, a.packing_amount, a.freight_type, a.freight_amount,a.remarks')->from('salesforce_orders a')->join('system_users b','a.marketing_person=b.user_id','left');

if($type<>'')
{
	if($type=='1')
	{
$this->db->where('a.order_type','SALE');
	}else
	{
$this->db->where('a.order_type','SERVICE');
	}


}

		$query = $this->db->where('order_status','1')->order_by('a.order_id','desc')->get();
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
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>INSTRUMENT</th><th style='padding:2px 2px 2px 2px'>ACTION</th></tr>";
			$instrumentsss = array();
			
			$query = $this->db->select('a.id as jcardid,a.order_id, a.qty, a.item_id, b.id, b.instruments_name,')->from('salesforce_order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$row->order_id)->get();
			foreach($query->result() as $instruments){
			
				$backgroundcolor='';
				
                
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'>".strtoupper($instruments->instruments_name)."</td>";
				
			$html.="<td style='padding:2px 2px 2px 2px; text-align:center; ".$backgroundcolor."'><a  href='javascript:;' onclick='showModalInstruments(".$row->internal_order_no.",".$instruments->jcardid.")'><i class='fa fa-pencil'></i></a>        |                 <a href='javascript:;' onclick='deleteOrderInstruments(".$instruments->jcardid.")'><i class='fa fa-trash'></i></a></td>";
		
				//	$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'><a href='".page_url."Reporting/generatejobcard/".$instruments->jcardid."' class='btn btn-primary btn-xs' target='_blank'>Jobcard</a></td>";
				$html.="</tr>";
				
			}
			$html.="</table>";
			$ins_charges = "";
			/** Get order planned **/
			$restt=$this->db->select('count(id) as totaljobcard')->from('salesforce_order_instruments')->where('order_id',$row->order_id)->get();
			foreach($restt->result() as $instcount);
			$totjobcard= $instcount->totaljobcard;
			
			
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
			}else if($row->freight_type=='4'){
				$freigntcharges = "OWN PICK-UP";
					 
			}else
			{
				$freigntcharges='';
			}





				if($row->orderduetodiversion=='1')
			{
			$div="YES<br/>"." (".$row->diversionreasonref.")";
			}else
			{
			    $div="NO";
			}
			$internalordersheet="<a href='".page_url."Reporting/generateinternalorderslip/".$row->order_id."' target='_blank'><span class='btn btn-xs btn-success'>I/0 Slip</span></a>";
			
			$scheduler_data[] = array('sr_no'=>$i,
								
									  'planorder'=>"<a href='javascript:;' onclick='agreethis(".$row->order_id.");'><span class='btn btn-warning btn-sm'>Approve and send to MItr</span></a>",
									  'added_on'=>$addeddate."".$addedtime,
									   'oduetodiversion'=>$div,
									  'order_type'=>strtoupper($row->order_type),
									  'marketing_person'=>strtoupper($row->title." ".$row->first_name." ".$row->last_name),
									  'po_number'=>strtoupper($row->po_number),
									  'company_name'=>strtoupper($row->company_name),
									  'contactperson'=>strtoupper($row->contact_person),
									  'designation'=>strtoupper($row->company_name),
									
									  'address'=>strtoupper($row->address),
									  'email'=>strtoupper($row->email),
									  'mobile_number'=>strtoupper($row->mobile_number),
									    'phone'=>strtoupper($row->phone),
									  
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

 

function migrateorder()
{

$id=$this->uri->segment(3);
$ty=$this->uri->segment(4);

$Resty=$this->db->select('*')->from('salesforce_orders')->where('order_id',$id)->get();

if($Resty->num_rows()>0)
{

	foreach($Resty->result() as $Resty1);

    $data=array('order_type'=>$Resty1->order_type,'marketing_person'=>$Resty1->marketing_person,'po_number'=>$Resty1->po_number,'company_name'=>$Resty1->company_name,'address'=>$Resty1->address,'pincode'=>$Resty1->pincode,'designation'=>$Resty1->designation,'contact_person'=>$Resty1->contact_person,'email'=>$Resty1->email,'mobile_number'=>$Resty1->mobile_number,'phone'=>$Resty1->phone,'internal_order_no'=>$Resty1->internal_order_no,'discount'=>$Resty1->discount, 'order_value_after_discount'=>$Resty1->order_value_after_discount,'advance_amount'=>$Resty1->advance_amount,'payment_terms'=>$Resty1->payment_terms,'installation_type'=>$Resty1->installation_type,'installation_charges'=>$Resty1->installation_charges,'installation_amount'=>$Resty1->installation_amount,'packing_charges'=>$Resty1->packing_charges,'packing_type'=>$Resty1->packing_type,'packing_amount'=>$Resty1->packing_amount,'freight_type'=>$Resty1->freight_type,'freight_amount'=>$Resty1->freight_amount,'order_status'=>'1','remarks'=>$Resty1->remarks,'closeorder'=>$Resty1->closeorder,'closedby'=>$Resty1->closedby,'closedOn'=>$Resty1->closedOn,'selforder'=>$Resty1->selforder,'added_on'=>$Resty1->added_on,'movetodispatch'=>$Resty1->movetodispatch,'movedby'=>$Resty1->movedby,'movedOn'=>$Resty1->movedOn,'added_by'=>$Resty1->added_by,'delivery_date'=>$Resty1->delivery_date,'updated_on'=>$Resty1->updated_on,'divertorder'=>$Resty1->divertorder,'servicerepairorder'=>$Resty1->servicerepairorder,'servicerepairid'=>$Resty1->servicerepairid,'docketno'=>$Resty1->docketno,'orderduetodiversion'=>$Resty1->orderduetodiversion,'diversionreasonref'=>$Resty1->diversionreasonref,'orderduetocancellation'=>$Resty1->orderduetocancellation,'docketnumber'=>$Resty1->docketnumber,'billno'=>$Resty1->billno,'billdate'=>$Resty1->billdate,'totalpacket'=>$Resty1->totalpacket,'shipmentmode'=>$Resty1->shipmentmode,'dodamount'=>$Resty1->dodamount,'frieghtamount'=>$Resty1->frieghtamount,'sforder'=>$Resty1->order_id,'oppid'=>$Resty1->opportunityid,'sforderno'=>$Resty1->sforderno,'sono'=>$Resty1->sono,'gstno'=>$Resty1->gstno);

    //echo "<pre>"; print_r($data); exit; 
        $this->db->insert('prestogroup_orders',$data);
        $lid=$this->db->insert_id();

        /** START PRODUCT ADD **/ 
            if($lid>0)
            {

            $restyue=$this->db->select('*')->from('salesforce_order_instruments')->where('order_id',$Resty1->order_id)->get();
            if($restyue->num_rows()>0)
            {

            $i=1;
            foreach($restyue->result() as $restyue1)
            {
            $ion=$Resty1->internal_order_no;
            $total=$restyue->num_rows();                    
            $jobcardnumber = $ion." (".$i."/".$total.")";


            $query111 = $this->db->select('id')->from('order_instruments')->where('mserialno !=','')->get();
                                $res1 = $query111->num_rows();
                                $serial = $res1+1;
                                $mserial= sprintf("%03d", $serial);
                                    $machserial=date('my').'-'.$mserial;
                            
                             $extserial=$this->chkforextraserial($restyue1->item_id);
                                
                                if($extserial=='1')
                                {
                                    $mserial= sprintf("%03d", $serial+1);
                                    $extramachserial=date('my').'-'.$mserial.rand(10,99);
                                }else
                                {
                                    $extramachserial='';
                                }



            $dataaaa=array('order_id'=>$lid,'item_id'=>$restyue1->item_id,'qty'=>$restyue1->qty,'job_card_no'=>$jobcardnumber,'mserialno'=>$machserial,'extraserialno'=>$extramachserial,'instrument_addedon'=>date('Y-m-d H:i:s'),'complete'=>'0','packed'=>'0','finalpacked'=>'0','diverted'=>'0','iono'=>'','reference'=>'','lineitemno'=>$restyue1->lineitemid);

           // echo "<pre>"; print_r($dataaaa); exit;
            $this->db->insert('order_instruments',$dataaaa);
            $i++;
            }

            /** UPDATE SF ORDER STATUS **/
            $uppdata=array('order_status'=>'0');
            $this->db->where('order_id',$id);
            $this->db->update('salesforce_orders',$uppdata);
            

            /** END **/


            }




            }


       /** END **/


$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-danger">Order Migrated</span><br/>');
    redirect(page_url.'FMS/salesforceorder/'.$ty);
}else
{

	$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-danger">Unable to migrate order</span><br/>');
	redirect(page_url.'FMS/salesforceorder/'.$ty);
}

}



	
public function getAllInstruments() {
			$q=$_GET['q'];
			$query = $this->db->select('id, instruments_name')
							 ->from('presto_instruments')
							 ->like('instruments_name', $q, 'both')
							 ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $getNames) {
					$json[] = array('id'=>$getNames->id, 'text'=>$getNames->instruments_name);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
				echo json_encode($json);
		}

public function updateInstrumentID() {
	$id = $this->input->post('hidden_id');
	$stype=$this->uri->segment(3);
	$data = array(
			'item_id' => $this->input->post('getInstruments')
			);
	$result = $this->master->updateInstrument($data, $id);

	if ($result > 0) {
		redirect(page_url.'FMS/salesforceorder/'.$stype);
	}
}

public function deleteOrderInstrument() {
	$id = $this->input->post('id');
	$result = $this->master->deleteInstrument($id);

	if ($result > 0) {
		echo '1';
	} else {
		echo '0';
	}
}

function manualpo()
	{
		
		$this->load->view('master/manualpo');
		
	}
		public function getInternalOrderNo() {
			$q=$_GET['q'];
			$query = $this->db->select('order_id, internal_order_no')
							 ->from('prestogroup_orders')
							 ->like('internal_order_no', $q, 'both')
							 ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $order) {
					$json[] = array('id'=>$order->order_id, 'text'=>$order->internal_order_no);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
				echo json_encode($json);
		}

		public function add_po() {
			$order_id = $this->input->post('internal_order_no');
			$sonumber=$this->input->post('sonumber');
			$getOrderID = $this->master->getInternalOrderID($order_id);
			
					
			$file = $_FILES['fileupload']['name'];
			if($file <> '')
			{
			$po = explode('.', $file);
			$ext = end($po);
			$newname = $getOrderID.'.'.$ext;
			
			move_uploaded_file($_FILES["fileupload"]["tmp_name"],sfpo.$newname);
			} else {
			$newname='';
   			 }
   			 //echo $newname;exit;
   			 $data = array(
				'pono' => $newname,
				'sono'=>$sonumber
				);

   			 //echo "<pre>";print_r($data);exit;
		
			$result = $this->master->update_po($data, $order_id);
//echo "<pre>"; print_r($result); exit;
				if($result > 0 || $result==0) {
					$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Record successfully updated.</span></div><br/>');
					redirect(page_url.'FMS/manualpo');
				}

		}

function testronix_instruments(){
	$this->load->view('FMS/testronix_instruments');
}

function testronix_instruments_master(){
	$this->load->view('FMS/testronix_instruments_master');
}

public function testronix_instruments_list()
	{
		$scheduler_data = array();
		$type=$this->uri->segment(3);
		$fincode=$this->uri->segment(4);
		
		$this->db->select('*')->from('presto_instruments')->where('instruments_of','2')->order_by('type','ASC');
		if($type<>'')
		{
		    $this->db->where('type',$type);
		}
		if($fincode<>'')
		{
		$this->db->where('fincode','');
		}
		
		$query =$this->db->order_by('instruments_name','ASC')->get();
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
			
			if($row->type=='0')
			{
			$Restyuoi=$this->db->select('id')->from('machine_bom')->where('mid',$row->id)->get();
			if($Restyuoi->num_rows()>0)
			{
			    $up="YES";
			}else
			{
			    $up="NO";
			}
			}else
			{
			    $up='';
			}
			if($row->alias==0)
			{
			$al="NO";
			}else
			{
			$al="Yes";
			}
			
			
				$openingstock="<a href='".page_url."FMS/addos/".$row->id."' target='_blank'><span class='btn  btn-xs btn-warning'>Add Opening Stock</span></a>";
			$edit = "<a href='".page_url."FMS/edit_instruments/".$row->id."/".$type."'><i class='fa fa-pencil'></i></a>";
			$similardiversion="<a href='".page_url."FMS/similiarmachinefordiversion/".$row->id."'><span class='btn btn-warning btn-xs'>Similar Machines</span></a>";
			$scheduler_data[] = array('sr_no'=>$i,
			'instruments_name'=>strtoupper($row->instruments_name),
			'modelno'=>$row->model_number,
			'fileno'=>$row->file_number,
			'aliasupdate'=>$al,
			'mvalue'=>floatval($row->mvalue));
			$i++;
		}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}


public function save_completion_date() {
	$completion_date = $this->input->post('completion_date');
	$order_id = $this->input->post('order_id');
	$job_card_id = $this->input->post('jcardid');

	$data = array(
			'order_id' => $order_id,
			'job_card_id' => $job_card_id,
			'completion_date' => $completion_date
			);

$str = '';
	//echo "<pre>";print_r($data);exit;
	$result = $this->fmsmodel->saveCompletionDate($data);
	if ($result > 0) {
		$results = $this->fmsmodel->getServiceDates($order_id, $job_card_id);
	

		

			if (count($results) > 0) {
		
		foreach ($results as $dates) {
		
			$str.=$dates."\n";
		}
					
			}

	}

	echo $str;
}

function flowchart()
{
$this->load->view('FMS/fmstree');    
}

}

