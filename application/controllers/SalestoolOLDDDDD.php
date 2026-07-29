<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salestool  extends CI_Controller { 

public function __construct()
		{
			parent::__construct();
			$session = $this->session->userdata('logged_in');
			if($session == FALSE)
			{
			redirect(page_url);
			}
			$this->load->model('Fms_model','fmsmodel');
			$this->load->model('Fms_mismodel','fmsmismodel');
			$this->load->model('Delegation_model');
			
		}
		
	public function index(){

$this->load->view('salestool/salestool');

		}
		
	function machinedata()
	{
		
		$this->load->view('salestool/importdata');
		
	}
	
	
		function machinedataimport()
	{
		
		
		require('library/php-excel-reader/excel_reader2.php');
    require('library/SpreadsheetReader.php');
	
	$mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.oasis.opendocument.spreadsheet'];
		
		if($_FILES["machinefile"]['name']<>'')
	{

	if(in_array($_FILES["machinefile"]["type"],$mimes))
	{
		
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			$name=$_FILES["machinefile"]["name"];
			$tmp_name=explode('.',$name);
			$extn=end($tmp_name);
			$newname=time().'.'.$extn;
			$uploadFilePath = $_SERVER['DOCUMENT_ROOT'].'/exceluploads/'.basename($newname);
			move_uploaded_file($_FILES['machinefile']['tmp_name'], $uploadFilePath);
		
			$Reader = new SpreadsheetReader($uploadFilePath);
			$totalSheet = count($Reader->sheets());

			/* For Loop for all sheets */
			for($i=0;$i<$totalSheet;$i++){
				
			$Reader->ChangeSheet($i);

			$r=0;

			foreach ($Reader as $Row)
			{

			if($r<>0)
			{
				
			if(isset($Row[0]))
			{ 
			$machinename=trim($Row[0]); 

			}else{ $machinename=''; };

			if($machinename<>'')
			{
				
				
				$datamachine=array('machinename'=>strtoupper($machinename),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('salestoolmachines',$datamachine);
				$lid=$this->db->insert_id();
				
				if($lid<>0 || $lid<>'')
				{
					/** MODEL **/
				if(isset($Row[1]))
				{ 
				$model=trim($Row[1]); 

				}else{ $model=''; };

						if($model<>'')
						{

						$allmodel=explode(',',$model);
						if(count($allmodel)>0)
						{
						for($u=0;$u<count($allmodel);$u++)
						{

						$moddata=array('mid'=>$lid,'model'=>$allmodel[$u],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);

						$this->db->insert('salestoolmachinesmodel',$moddata);
						} } }
					/** END MODEL **/
					
					/** CAPACITY **/
					
						if(isset($Row[2]))
						{ 
						$capacity=trim($Row[2]); 

						}else{ $capacity=''; }

						if($capacity<>'')
						{

						$allcapacity=explode(',',$capacity);
						if(count($allcapacity)>0)
						{
						for($v=0;$v<count($allcapacity);$v++)
						{

						$capdata=array('mid'=>$lid,'capacity'=>$allcapacity[$v],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);

						$this->db->insert('salestoolmachinescapacity',$capdata);
						}


						}

						}
					/** END **/	
					
					
					
					
				}
				
				
				
				
				
				
			}






			}



$r++;

}	}

$this->session->set_flashdata('message','File Imported');
redirect(page_url.'Salestool/machinedata');
		
		
	}else
	{
		
		$this->session->set_flashdata('message','File Type Not Allowed');

		redirect(page_url.'Salestool/machinedata');
		
	}
	
	
	
	
	}else{ 
	
	$this->session->set_flashdata('message','Upload file to import');
	
		redirect(page_url.'Salestool/machinedata');	  
		
		}

		
		
		
	}
	
	
	function allmachinesdata()
	{
		
		$scheduler_data = array();
		
		$restyui=$this->db->select('machinename,id')->from('salestoolmachines')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
			
			foreach($restyui->result() as $restyui1)
			{
				
			$html='';	
			$html1='';	
				$query1=$this->db->select('model')->from('salestoolmachinesmodel')->where('mid',$restyui1->id)->get();
			if($query1->num_rows()>0)
			{
				
			
			
			
			foreach($query1->result() as $instruments){
				
				
				$html.=$instruments->model."<br/>";
			
				
			}
			
			
			}else{
				
				$html="";
			}
			
			
			$query1=$this->db->select('capacity')->from('salestoolmachinescapacity')->where('mid',$restyui1->id)->get();
			if($query1->num_rows()>0)
			{
			
			
			
			foreach($query1->result() as $instruments1){
				
				
				$html1.=$instruments1->capacity."<br/>";
			
				
			}
			
			
			}else{
				
				$html1="";
			}
			
			$edit="<a href='".page_url."Salestool/edit_machinedata/".$restyui1->id."'><i class='fa fa-pencil'></i></a>";
			
		$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->machinename,
			'model'=>$html,
			'capacity'=>$html1,
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

	

function salestooltemplate()
{

$this->load->view('salestool/template');

}	

function salesscript()
{
	
	$scheduler_data = array();
		$script='';
		$restyui=$this->db->select('*')->from('salestooltemplete')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
			foreach($restyui->result() as $restyui1);
			
			$scriptintro=$restyui1->intro.'<br/>';
				$script1=$restyui1->first_field." ".$restyui1->person_name.'<br/>';
				$script2=$restyui1->salutation."<br/>";
				$script3=$restyui1->second_field." ".$restyui1->first_sample_name." ".$restyui1->aftersamplename." ".$restyui1->machine_detail."<br/>";
				$script4=$restyui1->third_field."<br/>";
				$script5=$restyui1->machine_model."<br/>";
				$script6=$restyui1->fourth_field." ".$restyui1->sample_name." ".$restyui1->fifth_field."<br/>";
				$script7=$restyui1->related_machines." ".$restyui1->sixth_field." ".$restyui1->location." ".$restyui1->afterlocation."<br/>";
				$script8=$restyui1->company_detail."<br/>";
				$script9=$restyui1->seventh_line;
				$script10=$restyui1->again_person_name."<br/>";
				$script11=$restyui1->again_machine_details."<br/>";
				$script12=$restyui1->again_customer_overall_details."<br/>";
				$script13=$restyui1->lastline."<br/>";
				
				
				$script=$scriptintro.$script1.$script2.$script3.$script4.$script5.$script6.$script7.$script8.$script9.$script10.$script11.$script12.$script13;
				$edit="<a href='".page_url."Salestool/editscript/".$restyui1->id."'><span class='btn btn-sm btn-success'>Edit</span></a>";
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'script'=>$script,
			'edit'=>$edit);
			
		}
		
			$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	
	
}

function editscript()
{
	
	
	$this->load->view('salestool/edit_script');
	
}

function updatescript()
{
	
	$data=array('intro'=>$this->input->post('intro'),
	'first_field'=>$this->input->post('first_field'),
	'second_field'=>$this->input->post('fourth_field'),
	'aftersamplename'=>$this->input->post('sixth_field'),
	'third_field'=>$this->input->post('eighth_field'),
	'fourth_field'=>$this->input->post('tenth_field'),
	'fifth_field'=>$this->input->post('twelveth_field'),
	'sixth_field'=>$this->input->post('fourteen_field'),
	'afterlocation'=>$this->input->post('sixteen_field'),
	'seventh_line'=>$this->input->post('eigthteen_field'),
	'lastline'=>$this->input->post('twentytwo_field')
	);
	
	
	$this->db->where('id','1');
	$this->db->update('salestooltemplete
',$data);

$this->session->set_flashdata('response','Record Updated');
redirect(page_url.'Salestool/salestooltemplate');
	
}

function getmodel()
{
	$mid=$this->uri->segment(3);
	
	$mo=$this->db->select('model,id')->from('salestoolmachinesmodel')->where('mid',$mid)->get();
	
	
	if($mo->num_rows()>0)
	{
		$nrow=$mo->num_rows();
		foreach($mo->result() as $mo1)
		{
			
			
			echo "<option value='".$mo1->id."'>".ucfirst($mo1->model)."</option>";
			
		}
		
		
		
	}else{
		
		
		echo "<option value='' selected>NO MODEL AVAILABLE</option>";
		
	}
	
}



function getcapacity()
{
	$mid=$this->uri->segment(3);
	
	$mo=$this->db->select('capacity,id')->from('salestoolmachinescapacity')->where('mid',$mid)->get();
	
	
	if($mo->num_rows()>0)
	{
		$nrow=$mo->num_rows();
		foreach($mo->result() as $mo1)
		{
		
			
			echo "<option value='".$mo1->id."'>".ucfirst($mo1->capacity)."</option>";
			
		}
		
		
		
	}else{
		
		
		echo "<option value='' selected>NO CAPACITY AVAILABLE</option>";
		
	}
	
}


function getintroline()
{
	$html='';
	$machine=$this->uri->segment(3);
	$sample=$this->uri->segment(4);
	if($sample=='NA')
	{
		$sample='';
	}else{ $sample=$sample;}
	
	$time=$this->uri->segment(5);
	$capacity=$this->uri->segment(6);
	if($capacity=='NA')
	{
		$capacity='';
	}else{
		$capacity=$capacity;
	}
	//echo $sample;
	$time=$time+1;
	
	$restyu=$this->db->select('machinename')->from('salestoolmachines')->where('id',$machine)->get();
	if($restyu->num_rows()>0)
	{
	foreach($restyu->result() as $mname);
	
	if($capacity<>'NA')
	{
	$restyu1=$this->db->select('capacity')->from('salestoolmachinescapacity')->where('id',$capacity)->get();
	if($restyu1->num_rows()>0)
	{
		foreach($restyu1->result() as $restyu11);
		$capacity=$restyu11->capacity;
	}
	}else{  $capacity='';
	}
	$machinename=$mname->machinename;
	$temp=$this->db->select('aftersamplename')->from('salestooltemplete')->get();
	if($temp->num_rows()>0)
	{
		foreach($temp->result() as $temp1);
		$aftersampple=$temp1->aftersamplename;
	}else{  $aftersampple=''; }
	
	
	$html=$time.'- '.strtolower($sample).' '.$aftersampple.' '.strtolower($machinename).' Capacity '.strtolower($capacity);
	
	echo $html;
	
	}
	
	
}

function getmachinedetails()
{
	
	$html='';
	$machine=$this->uri->segment(3);
	$model=$this->uri->segment(4);
	$capacity=$this->uri->segment(5);
	$sample=$this->uri->segment(6);
	$times=$this->uri->segment(7);

$times=$times+1;	
	$machinename='';
	$machinedetail='';
	$capacity='';
	$models='';
	
	if($machine<>'NA')
	{
	$restyu=$this->db->select('machinename')->from('salestoolmachines')->where('id',$machine)->get();
	if($restyu->num_rows()>0)
	{
	foreach($restyu->result() as $mname);
	
	$machinename=$mname->machinename;
	}
	}
	
	
	if($model<>'NA')
	{
	$restyu1=$this->db->select('model')->from('salestoolmachinesmodel')->where('id',$model)->get();
	if($restyu1->num_rows()>0)
	{
	foreach($restyu1->result() as $mod);
	$models=$mod->model;
	}
	}
	
	
	if($capacity<>'NA')
	{
	$restyu2=$this->db->select('capacity')->from('salestoolmachinescapacity')->where('id',$capacity)->get();
	if($restyu2->num_rows()>0)
	{
	foreach($restyu2->result() as $cap);
	$capacity=$cap->capacity;
	}
	}
	
	
	$machinedetail.=$times.") Machine: ".strtolower($machinename).'<br/>';
	$machinedetail.="Model: ".strtolower($models).'<br/>';
	$machinedetail.="Capacity: ".strtolower($capacity).'<br/>';
	
	echo $machinedetail;exit;
	
	
}

function getcustomerinfo()
{
	
	$usercompany=$this->uri->segment(3);
	if($usercompany=='NA')
	{
		$usercompany='';
	}else{ $usercompany=$usercompany;  }
	$officecontact=$this->uri->segment(4);
	if($officecontact=='NA')
	{
		$officecontact='';
	}else{  $officecontact=$officecontact;
	}
	
	$mobile=$this->uri->segment(5);
	if($mobile=="NA")
	{
		$mobile='';
}else{ $mobile=$mobile; }

	$email=$this->uri->segment(6);
	if($email=="NA")
	{
		$email='';
}else{ $email=$email; }
	$title=$this->uri->segment(7);
	if($title=="NA")
	{
		$title='';
}else{ $title=$title; }
	$contactperson=$this->uri->segment(8);
	if($contactperson=="NA")
	{
		$contactperson='';
}else{ $contactperson=$contactperson; }

	$location=$this->uri->segment(9);
	if($location=="NA")
	{
		$location='';
}else{ $location=$location; }



	$html='';
	$html.='Customer Name: '.strtolower(str_replace('%20',' ',$contactperson))."<br/>";
	$html.='Location: '.strtolower(str_replace('%20',' ',$location))."<br/>";
	$html.='Company Name: '.strtolower(str_replace('%20',' ',$usercompany))."<br/>";
	$html.='Office Contact: '.strtolower($officecontact)."<br/>";
	$html.='Mobile: '.strtolower($mobile)."<br/>";
	$html.='Email: '.strtolower($email);
	
	echo $html;
	
	
}


function getothermodeldetails()
{
	$machinename='';
	$html='';
	$machine=$this->uri->segment('3');
	$time=$this->uri->segment('4');
	$model=$this->uri->segment('5');
	//echo $model;exit;
	if($machine<>'NA')
	{
		$restyu=$this->db->select('machinename')->from('salestoolmachines')->where('id',$machine)->get();
		if($restyu->num_rows()>0)
		{
		foreach($restyu->result() as $mname);
		$machinename=$mname->machinename;
		}

		$html='For: '.strtolower($machinename).'<br/>';
		
		$this->db->select('model')->from('salestoolmachinesmodel')->where('mid',$machine);
		if($model<>'NA')
		{
			$this->db->where('id !=',$model);
		}
		$mo=$this->db->get();
		if($mo->num_rows()>0)
		{
		$nrow=$mo->num_rows();
		$t=1;
		foreach($mo->result() as $mo1)
		{
			$html.='- '.strtolower($mo1->model).'<br/>';
		$t++;
		}
		
		}else{
			$html.=strtolower("No Model Available");
		}
	
	
	}
	
	echo $html;
	
	
}	

function edit_machinedata()
{
	$this->load->view('salestool/edit_machine_data');
	
}

function savedata()
{
	$data=array('companyname'=>$this->input->post('usercompanyname'),'title'=>$this->input->post('title'),'contactperson'=>$this->input->post('contactperson'),'officecontact'=>$this->input->post('officecontact'),'mobile'=>$this->input->post('mobile'),'email'=>$this->input->post('email'),'location'=>$this->input->post('location'),'industry'=>$this->input->post('industry'),'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d'));
	$this->db->insert('salestooldata',$data);
	$lid=$this->db->insert_id();
	
	$mach=$this->input->post('machine');
	for($i=0;$i<count($mach);$i++)
	{
		$model=$this->input->post('model');
		$capacity=$this->input->post('capacity');
		$sample=$this->input->post('sample');
		$data1=array('callid'=>$lid,'mid'=>$mach[$i],'model'=>$model[$i],'capacity'=>$capacity[$i],'sample'=>$sample[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('salestoolmachinedata',$data1);
	}
	
		$this->session->set_flashdata('message','Record Added');
		redirect(page_url.'Salestool');
	
}

function callhistory()
{
	
	$this->load->view('salestool/callingdata');
}

function allcallingdata()
{
	
	$scheduler_data = array();
		
		$restyui=$this->db->select('*')->from('salestooldata')->order_by('id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
			foreach($restyui->result() as $instruments)
			{
				$html = "<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>NAME.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>MOBILE.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>EMAIL.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>OFFICE CONTACT.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>LOCATION.</th></tr>";
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($instruments->title)." ".strtoupper($instruments->contactperson)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($instruments->mobile)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($instruments->email)."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($instruments->officecontact)."</td>";
							$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($instruments->location)."</td>";
				$html.="</tr>";
				
				$html1 = "<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>MACHINE.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>MODEL.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>CAPACITY.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>SAMPLE TO TEST.</th></tr>";
				$machinesat=$this->db->select('a.*,b.machinename')->from('salestoolmachinedata a')->join('salestoolmachines b','a.mid=b.id')->where('a.callid',$instruments->id)->get();
				if($machinesat->num_rows()>0)
				{
					
					foreach($machinesat->result() as $machinesat1)
					{
						if($machinesat1->model=='0')
						{
							$model="NO MODEL AVAILABLE";
						}else{
							
							$moddel=$this->db->select('model')->from('salestoolmachinesmodel')->where('mid',$machinesat1->mid)->get();
							if($moddel->num_rows()>0)
							{
								foreach($moddel->result() as $modddel);
								
								$model=$modddel->model;

							}else{

							$model='';
							}
						}
												
						if($machinesat1->capacity=='0')
						{
							$capacity="NO CAPACITY AVAILABLE";
						}else{
							
							$moddel1=$this->db->select('capacity')->from('salestoolmachinescapacity')->where('mid',$machinesat1->mid)->get();
							if($moddel1->num_rows()>0)
							{
								foreach($moddel1->result() as $modddel12);
								
								$capacity=$modddel12->capacity;

							}else{

							$capacity='';
							}
							
						}
					
					
					$rest=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$instruments->addedBy)->order_by('first_name','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1);
			$fname=$rest1->first_name.' '.$rest1->last_name;
			
		}else{
			$fname='';
		}
					
					
					
					$html1.="<tr>";
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($machinesat1->machinename)."</td>";
			
				$html1.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($model)."</td>";
					$html1.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($capacity)."</td>";
						$html1.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($machinesat1->sample)."</td>";
				$html.="</tr>";
					}
					
					
				}
				
			
			
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'customerdetail'=>$html,
			'machinedetail'=>$html1,
			'callername'=>$fname,
			'calledOn'=>date('d-M-Y g:i A',strtotime($instruments->addedOn)));
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


function machinedataupdate()
{
	
$mid=$this->uri->segment(3);
$machine=$this->input->post('machine');
$data1=array('machinename'=>$machine,'updatedOn'=>date('Y-m-d H:i:s'));
$this->db->where('id',$mid);
$this->db->update('salestoolmachines',$data1);

/** GET EXISTING MODEL **/

$eximodel=$this->input->post('existingmodel');
if(count($eximodel)>0)
{

	for($i=0;$i<count($eximodel);$i++)
	{
		$modelid=$eximodel[$i];
		
		
		$data2=array('model'=>$this->input->post('existmodalname'.$modelid));
		$this->db->where('mid',$mid);
		$this->db->where('id',$modelid);
		$this->db->update('salestoolmachinesmodel',$data2);
		
		$existingattachment=$this->input->post('existingmodelattchment'.$modelid);
		if($existingattachment=='')
		{
			
			$file=$_FILES['extsingattachment'.$modelid]['name'];
			if($file<>'')
			{
				$part=explode('.',$file);
				$ext=end($part);
				$fname=$part[0];
				$newname=$fname.'_'.rand(10,9999).'.'.$ext;
				
				move_uploaded_file($_FILES['extsingattachment'.$modelid]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/upload/machineattachment/'.$newname);
				//@unlink($_SERVER['DOCUMENT_ROOT'].'/presto/upload/machineattachment/'.$existingattachment);
				
				
				$data3=array('mid'=>$mid,'modelid'=>$modelid,'attachment'=>$newname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('modelwiseattachment',$data3);
			}
			
			
			
		}else{
			
			
			$file=$_FILES['extsingattachment'.$modelid]['name'];
			if($file<>'')
			{
				$part=explode('.',$file);
				$ext=end($part);
				$fname=$part[0];
				$newname=$fname.'_'.rand(10,9999).'.'.$ext;
				
				move_uploaded_file($_FILES['extsingattachment'.$modelid]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/upload/machineattachment/'.$newname);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/machineattachment/'.$existingattachment);
			}else{
				
				$newname=$existingattachment;
			}
			
			$data3=array('attachment'=>$newname,'updatedOn'=>date('Y-m-d H:i:s'));
			$this->db->where('mid',$mid);
			$this->db->where('modelid',$modelid);
			$this->db->update('modelwiseattachment',$data3);
		}
		
		
		
		

	}		
	
	
}

/** END **/

/** NEW Model **/
$newmodel=$this->input->post('newmodelcheck');
if($newmodel=='1')
{
	$model=$this->input->post('model');
	if(count($model)>0)
	{
		for($t=0;$t<count($model);$t++)
		{
			$data4=array('mid'=>$mid,'model'=>$model[$t],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('salestoolmachinesmodel',$data4);
		$lid=$this->db->insert_id();
		
			$file1=$_FILES['attachment']['name'][$t];
			
			if($file1<>'')
			{
			$part=explode('.',$file1);
			$ext=end($part);
			$fname=$part[0];
			$newname=$fname.'_'.rand(10,9999).'.'.$ext;

			move_uploaded_file($_FILES['extsingattachment']["tmp_name"][$t],$_SERVER['DOCUMENT_ROOT'].'/upload/machineattachment/'.$newname);
			
			
	
		$data5=array('mid'=>$mid,'modelid'=>$lid,'attachment'=>$newname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('modelwiseattachment',$data5);
		
			}
		
		}
		
		
	}
	
	
	
}


/** END **/

/*** OLD CAPACITY **/
$capa=$this->input->post('existingcapacity');
$cap=$this->input->post('existcapacity');
if(count($capa)>0)
{
	for($s=0;$s<count($capa);$s++)
	{
	$data6=array('capacity'=>$cap[$s]);
	$this->db->where('id',$capa[$s]);
	$this->db->update('salestoolmachinescapacity',$data6);
	
	}
	
}
/** END **/


/*** New CAPACITY **/
if($this->input->post('newcapacitycheck')==1)
{
$capa1=$this->input->post('capacity');
if(count($capa1)>0)
{
	for($s=0;$s<count($capa1);$s++)
	{
	$data7=array('mid'=>$mid,'capacity'=>$capa1[$s],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);

	$this->db->insert('salestoolmachinescapacity',$data7);
	
	}
	
}
}
/** END **/

/** NEW COMPANY **/
if($this->input->post('newcompaycheck')==1)
{
	
	$comp=$this->input->post('company');
	$location=$this->input->post('location');
	$industry=$this->input->post('industry');
	if(count($comp)>0)
	{
		
		for($c=0;$c<count($comp);$c++)
		{
			$data8=array('mid'=>$mid,'companyname'=>$comp[$c],'location'=>$location[$c],'industry'=>$industry[$c],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			
			$this->db->insert('salestoolcompanyhistory',$data8);
			
		
		}
		
		
	}

}

/** END **/


/** OLD COMPANY **/
	

	$existco=$this->input->post('existcom');
	if(count($existco)>0)
	{
		
		for($d=0;$d<count($existco);$d++)
		{
			$id=$existco[$d];
			$existingcompany=$this->input->post('exitingcompany'.$id);
			$existinglocation=$this->input->post('existinglocation'.$id);
			$existingindustry=$this->input->post('existingindustry'.$id);
			$data9=array('companyname'=>$existingcompany,'location'=>$existinglocation,'industry'=>$existingindustry,'updatedOn'=>date('Y-m-d H:i:s'));
			
			$this->db->where('id',$id);
			$this->db->where('mid',$mid);
			$this->db->update('salestoolcompanyhistory',$data9);
			
		
		}
		
		
	}

/** END **/
	
	
/** Related Machines **/
$related=$this->input->post('relatedmachine');
$this->db->where('mid',$mid);
$this->db->delete('relatedmachines');
if(count($related)>0)
{
	
	
	for($e=0;$e<count($related);$e++)
	{
		
		$data10=array('mid'=>$mid,'relatedmid'=>$related[$e],'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->insert('relatedmachines',$data10);
		
		
	}
	
	
}

/** END **/


redirect(page_url.'Salestool/edit_machinedata/'.$mid);	
	
	
}


function getmodalattachment()
{
	$machine=$this->uri->segment(3);
	$model=$this->uri->segment(4);
	$html='';
	$resty=$this->db->select('attachment')->from('modelwiseattachment')->where('mid',$machine)->where('modelid',$model)->where('attachment!=','')->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $restyy)
		
		$html="<span><a href='".page_url."upload/machineattachment/".$restyy->attachment."' target='_blank'>MODEL SPECIFICATION FILE</a></span>";
		
		echo $html;
		
		
	}else{  echo $html; }

} 


function getrelatedmachine()
{
	$machine=$this->uri->segment(3);
	$id=$this->uri->segment(4);
	$html='';
	$restsy=$this->db->select('b.machinename')->from('relatedmachines a')->join('salestoolmachines b','a.relatedmid=b.id')->where('a.mid',$machine)->get();
	if($restsy->num_rows()>0)
	{
		$i=1;
		foreach($restsy->result() as $restsy1)
		{
			$html.="- ".$restsy1->machinename.'<br/>';
			
		$i++;
		}
		
		echo $html;
	}else{
		echo $html;
		
	}

	
}



function generatemachineusagedetails()
{
	$machine=$this->uri->segment(3);
	$industry=$this->uri->segment(4);
	$location=$this->uri->segment(5);
	$html='';
	$machinename='';
	$temp=$this->db->select('sixth_field,afterlocation')->from('salestooltemplete')->get();
	if($temp->num_rows()>0)
	{
		foreach($temp->result() as $temp1);
		$aftersampple=$temp1->sixth_field;
		$afterlocation=$temp1->afterlocation;
	}else{  $aftersampple=''; $afterlocation=''; }
	
	if($machine<>'NA')
	{
	$restyu=$this->db->select('machinename')->from('salestoolmachines')->where('id',$machine)->get();
	if($restyu->num_rows()>0)
	{
	foreach($restyu->result() as $mname);
	
	$machinename=$mname->machinename;
	}
	}
	
	$html=$machinename." ".$aftersampple.' '.$location.' '.$afterlocation.'<br/>';
	$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->like('location',$location,'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		
		
		
		foreach($qye->result() as $que1)
		{
			$html.=$que1->companyname.'<br/>';
		}
		
		
}else
{
	$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',$industry);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			$html.=$que1->companyname.'<br/>';
		}
		
		
	}
	
}

echo $html;
	
	
	
}


} 