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
			$uploadFilePath = $_SERVER['DOCUMENT_ROOT'].'/presto/exceluploads/'.basename($newname);
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
			
			
		$scheduler_data[] = array('sr_no'=>$i,
			'instruments'=>$restyui1->machinename,
			'model'=>$html,
			'capacity'=>$html1);
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

} 