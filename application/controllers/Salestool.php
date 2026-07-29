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
			$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
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
			$uploadFilePath = SITE_ROOT.'exceluploads/'.basename($newname);
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
			'modelimage'=>"<a href='".page_url."Salestool/modelimage/".$restyui1->id."'><span class='btn btn-warning btn-xs'>Add/View</span></a>",
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
		
		
		echo "<option value='' selected>No Model Available</option>";
		
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
		
			
			echo "<option value='".$mo1->id."'>".ucfirst(strtolower($mo1->capacity))."</option>";
			
		}
		
		
		
	}else{
		
		
		echo "<option value='' selected>No Capacity Available</option>";
		
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
	
	$model=$this->uri->segment(7);

	if($model=='NA')
	{
	$model='';
	}else{
	$model=$model;
	}
	//echo $sample;
//	$time=$time+1;
	
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
	
	
	$modells='';
	 $pdf="-";
	$video='-';
	if($model<>'NA')
	{
	$restyu1fff=$this->db->select('model')->from('salestoolmachinesmodel')->where('id',$model)->get();
	if($restyu1fff->num_rows()>0)
	{
		foreach($restyu1fff->result() as $restyu1112);
		$modells=$restyu1112->model;
		
		$mod=$this->db->select('attachment,video')->from('modelwiseattachment')->where('modelid',$model)->get();
		    if($mod->num_rows()>0)
		    {
		        foreach($mod->result() as $modd);
		        $filename=$modd->attachment;
		        $pdf="<a href='".page_url."upload/machineattachment/".$filename."' target='_blank'><img src='".page_url."upload/pdficon.png' style='width:41px;'></a>";
		        if($modd->video<>'')
		        {
		        $video="<a href='".$modd->video."' target='_blank' style='font-size:12px;'><i class='fa fa-youtube' style='font-size:36px;color:red;'></i></a>";
		        }else
		        {
		            $video="-";
		        }
		        
		    }else{
		        $pdf="-";
		        $video='-';
		    }
			
			
			
			$modspe=$this->db->select('specsfile')->from('modelspecificationfile')->where('modelid',$model)->get();
		    if($modspe->num_rows()>0)
		    {
				foreach($modspe->result() as $modspe1);
				
				$modfile="<a href='".page_url."upload/modelspecs/".$modspe1->specsfile."' target='_blank'><img src='".page_url.'/upload/modelspecs/'.$modspe1->specsfile."' style='width:55px'></a>";
				
			}else{
				
				
				$modfile="<img src='".page_url."/upload/image404.png' style='width:55px;'>";
			}
		
		
	}
}else{  
$modells='';
	$pdf="-";
	$video='-';
	}
	
	
	$machinename=$mname->machinename;
	$temp=$this->db->select('aftersamplename')->from('salestooltemplete')->get();
	if($temp->num_rows()>0)
	{
		foreach($temp->result() as $temp1);
		$aftersampple=$temp1->aftersamplename;
	}else{  $aftersampple=''; }
	
	
	if($modells<>'')
	{
		$html2=' Model '.ucfirst(strtolower($modells));
	}else{
		$html2='';
	}
	
	
	if($capacity<>'')
	{
		$html1=' Capacity '.ucfirst(strtolower($capacity));
	}else{
		$html1='';
	}
	
		
	
	$html="<td style='text-align:left;padding: 5px 0px 0px 5px;width:39%;color:#797979;'>".ucwords(strtolower($machinename)).ucwords(strtolower($html2))." ".ucwords(strtolower($html1))."</td><td style='width:100px;'><div class='col-md-4'>".$pdf."</div><div class='col-md-4'><i class='fa fa-whatsapp' style='color:green;font-size:28px;'></i></div><div class='col-md-4'><input type='checkbox' name='inspdf".$machine."' style='width:31px;height:21px' value='".$model."' onchange='checknoofcheckbox();'></div></td><td style='width:100px;'><div class='col-md-4'>".$video."</div>";
	if($video<>'-')
	{
	$html.="<div class='col-md-4'><i class='fa fa-whatsapp' style='color:green;font-size:28px;'></i></div><div class='col-md-4'><input type='checkbox' name='videowhatsapp".$machine."' style='width:31px;height:21px' value='".$model."' onchange='checknoofcheckbox();'></div>";
	}
	
	$html.="</td><td style='width:55px;'>".$modfile."</td>";
	if($mod->num_rows()>0)
		    {
	    if($modd->attachment<>'')
	{
	    	//$share="<i class='fa fa-whatsapp' style='color:green'></i>&nbsp;Send PDF<br/><input type='checkbox' /**onchange='sendpdfwhatsapp(".$machine.",".$model."); **/'>";
			$share="<i class='fa fa-whatsapp' style='color:green;font-size:28px'></i>&nbsp;Send PDF<br/>";
	}else
	{
	    $share="PDF Not Available";
	}
	
    }else
    {
        $share="PDF Not Available";
    }
		    	
	//$html.="<td style='width:60px;'>".$share."</td>";
	
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
	$capacitys='';
	$models='';
	
	//$html.= "<table border='1' style='width:30%'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;'>Machine Name</th><th style='padding:2px 2px 2px 2px;text-align:center;'>Model</th><th style='padding:2px 2px 2px 2px;text-align:center;'>Capacity</th></tr>";

	
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
	$capacitys=$cap->capacity;
	
	}
	}
	
	$machinedetail.=ucfirst(strtolower($machinename)." ".strtolower($models)." ".strtolower($capacitys))." Machine";
	/**$machinedetail.=$times.") Machine: ".strtolower($machinename).'<br/>';
	$machinedetail.="Model: ".strtolower($models).'<br/>';
	$machinedetail.="Capacity: ".strtolower($capacity).'<br/>'; **/
	
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


//$html="You are calling from ".ucfirst(strtolower(str_replace('%20',' ',$usercompany)))." and your address is ".ucfirst(strtolower(str_replace('%20',' ',$location)))." and your contact details are Mobile-".strtolower($mobile)." Email-".strtolower($email);
$html="Mobile-".strtolower($mobile)."<br/> Email-".strtolower($email);
	//$html="<table border='1' style='width:70%'><tr style='background-color:white;'><th style='padding: 7px 8px 0px 8px;text-align:center;'>Name</th><th style='padding: 7px 8px 0px 8px;text-align:center;'>Location.</th><th style='padding: 7px 8px 0px 8px;text-align:center;'>Company.</th><th style='padding: 7px 8px 0px 8px;text-align:center;'>Office Contact.</th><th style='padding: 7px 8px 0px 8px;text-align:center;'>Mobile.</th><th style='padding: 7px 8px 0px 8px;text-align:center;'>Email.</th></tr>";
	//$html.="<tr>";
	//$html.='<td style="text-align:center;padding: 7px 8px 0px 8px;">'.strtolower(str_replace('%20',' ',$contactperson)).'</td>';
	//$html.='<td style="text-align:center;padding: 7px 8px 0px 8px;">'.strtolower(str_replace('%20',' ',$location)).'</td>';
	//$html.='<td style="text-align:center;padding: 7px 8px 0px 8px;">'.strtolower(str_replace('%20',' ',$usercompany)).'</td>';
	//$html.='<td style="text-align:center;padding: 7px 8px 0px 8px;">'.strtolower($officecontact).'</td>';
	//$html.='<td style="text-align:center;padding: 7px 8px 0px 8px;">'.strtolower($mobile).'</td>';
	//$html.='<td style="text-align:center;padding: 7px 8px 0px 8px;">'.strtolower($email).'</td>';
	//$html.='</tr>';
	

	echo $html;
	
	
}


function getothermodeldetailsOlddddddd()
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
			
		
		
		//$html='For: '.strtolower($machinename).'<br/>';
		
		$this->db->select('id,model')->from('salestoolmachinesmodel')->where('mid',$machine);
		if($model<>'NA')
		{
			$this->db->where('id !=',$model);
		}
		$mo=$this->db->get();
		
		$html.="<table border='1' style='width:100%;'>
		<tr  style='background-color:#EDFAFC;'><th colspan='".$mo->num_rows()."' style='padding: 7px 8px 0px 8px;text-align:center;'>For ".ucwords(strtolower($machinename))."</th></tr>";
		
		if($mo->num_rows()>0)
		{
		$nrow=$mo->num_rows();
		$t=1;
		$html.="<tr style='background-color:#fff;'>";
		foreach($mo->result() as $mo1)
		{
		   
			//$html.='<tr><td style="padding: 7px 8px 0px 8px;">'.strtolower($mo1->model).'</td><td style="padding: 7px 8px 5px 8px;text-align:center;">'.$pdf.'</td><td style="padding: 7px 8px 5px 8px;text-align:center;">'.$video.'</td></tr>';
			
			$html.='<td style="padding: 7px 8px 0px 8px;width:200px;text-align:center;">'.ucwords(strtolower($mo1->model)).'</td>';
			
		$t++;
		}
		
		$html.='</tr><tr style="background-color:#fff;">';
		
		foreach($mo->result() as $mo1)
		{
			
			 $mod=$this->db->select('video,attachment')->from('modelwiseattachment')->where('modelid',$mo1->id)->get();
		    if($mod->num_rows()>0)
		    {
		        foreach($mod->result() as $modd);
		        $filename=$modd->attachment;
		        $pdf="<a href='".page_url."upload/machineattachment/".$filename."' target='_blank'><img src='".page_url."upload/pdficon.png' style='width:41px;'></a>";
		        if($modd->video<>'')
		        {
		        $video="<a href='".$modd->video."' target='_blank' style='font-size:16px;'>Video</a>";
		        }else
		        {
		            $video="-";
		        }
		        
		    }else{
		        $pdf="-";
		        $video='-';
		    }
			
			
			$html.='<td><div class="col-md-4">'.$pdf.'</div><div class="col-md-4"><i class="fa fa-whatsapp" style="color:green;font-size:28px;"></i></div><div class="col-md-4"><input type="checkbox" name="othermodpdf'.$machine.'" style="width:31px;height:21px" value="'.$mo1->id.'" onchange="checknoofcheckbox();"></div></td>';
			
		}
		
		$html.='</tr><tr style="background-color:#fff;">';
		
		
		foreach($mo->result() as $mo1)
		{
			
			 $mod=$this->db->select('video,attachment')->from('modelwiseattachment')->where('modelid',$mo1->id)->get();
		    if($mod->num_rows()>0)
		    {
		        foreach($mod->result() as $modd);
		        $filename=$modd->attachment;
		        $pdf="<a href='".page_url."upload/machineattachment/".$filename."' target='_blank'><img src='".page_url."upload/pdficon.png' style='width:41px;'></a>";
		        if($modd->video<>'')
		        {
		        $video="<div class='col-md-4'><a href='".$modd->video."' target='_blank' style='font-size:14px;'>Video</a></div>";
		        }else
		        {
		            $video="-";
		        }
		        
		    }else{
		        $pdf="-";
		        $video='-';
		    }
			
			
			$html.='<td style="text-align:center;"><div class="col-md-4"><i class="fa fa-youtube" style="color:red;font-size:28px;"></i></div>'.$video.'<div class="col-md-4"><input type="checkbox" name="othervideo'.$machine.'" style="width:31px;height:21px" value="'.$mo1->id.'" onchange="checknoofcheckbox();"></div></td>';
			
		}
		
		$html.="</tr><tr style='background-color:#fff;'>";
		
		foreach($mo->result() as $mo1)
		{
			
			
			
			$html.='<td style="padding: 7px 8px 5px 8px;text-align:center;"><input type="checkbox" name="othermodel[]" class="omodel" value="'.$mo1->id.'" onchange="checkforothermodels(); checknoofcheckbox();"></td>';
			
		}
		
		$html.='</tr><tr style="background-color:#fff;">';
		
		
		
		foreach($mo->result() as $mo1)
		{
			
			$modspe=$this->db->select('specsfile')->from('modelspecificationfile')->where('modelid',$mo1->id)->get();
		    if($modspe->num_rows()>0)
		    {
				foreach($modspe->result() as $modspe1);
				
				$modfile="<a href='".page_url."upload/modelspecs/".$modspe1->specsfile."' target='_blank'><img src='".page_url.'/upload/modelspecs/'.$modspe1->specsfile."' style='width:100px'></a>";
				
			}else{
				
				
				$modfile="<img src='".page_url."/upload/image404.png' style='width:100px;'>";
			}
			
			
                      $html.='<td style="padding: 7px 8px 5px 8px;text-align:center;">'.$modfile.'</td>';
		
		
		}
		
		$html.='</tr>';
		
		
		}else{
			$html.='<tr colspan="3" style="padding: 7px 8px 0px 8px;"><td style="padding: 7px 8px 0px 8px;">No model available</td><tr/>';
		}
	
	$html.="</table>";
	
	}
	
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
			
		
		
		//$html='For: '.strtolower($machinename).'<br/>';
		
		$this->db->select('id,model')->from('salestoolmachinesmodel')->where('mid',$machine);
		if($model<>'NA')
		{
			$this->db->where('id !=',$model);
		}
		$mo=$this->db->get();
		
		
		
		if($mo->num_rows()>0)
		{
			$html.="<table border='1' style='width:100%;'>
		<tr  style='background-color:#EDFAFC;'><th colspan='6' style='text-align:center;'>For ".ucwords(strtolower($machinename))."</th></tr>";
		
		$nrow=$mo->num_rows();
		$t=1;
		
		
		foreach($mo->result() as $mo1)
		{
		
			$html.="<tr style='background-color:#fff;'>";
			
			 $mod=$this->db->select('video,attachment')->from('modelwiseattachment')->where('modelid',$mo1->id)->get();
		    if($mod->num_rows()>0)
		    {
		        foreach($mod->result() as $modd);
		        $filename=$modd->attachment;
		        $pdf="<a href='".page_url."upload/machineattachment/".$filename."' target='_blank'><img src='".page_url."upload/pdficon.png' style='width:41px;'></a>";
		        if($modd->video<>'')
		        {
		        $video="<a href='".$modd->video."' target='_blank' style='font-size:16px;'>Video</a>";
		        }else
		        {
		            $video="-";
		        }
		        
		    }else{
		        $pdf="-";
		        $video='-';
		    }
			
			
			$html.='<td style="text-align:center;width:39%">'.ucwords(strtolower($mo1->model)).'</td><td style="width:100px"><div class="col-md-4">'.$pdf.'</div><div class="col-md-4"><i class="fa fa-whatsapp" style="color:green;font-size:28px;"></i></div><div class="col-md-4"><input type="checkbox" name="othermodpdf'.$machine.'" value="'.$mo1->id.'" style="width:31px;height:21px" onchange="checknoofcheckbox();"></div></td>';
			
		
	
			
			 $mod=$this->db->select('video,attachment')->from('modelwiseattachment')->where('modelid',$mo1->id)->get();
		    if($mod->num_rows()>0)
		    {
		        foreach($mod->result() as $modd);
		        $filename=$modd->attachment;
		        $pdf="<a href='".page_url."upload/machineattachment/".$filename."' target='_blank'><img src='".page_url."upload/pdficon.png' style='width:41px;></a>";
		        if($modd->video<>'')
		        {
		        $video="<div class='col-md-4'><a href='".$modd->video."' target='_blank' style='font-size:14px;'><i class='fa fa-whatsapp' style='color:green;font-size:28px;'></i></a></div>";
		        }else
		        {
		            $video="-";
		        }
		        
		    }else{
		        $pdf="-";
		        $video='-';
		    }
			
			
			$html.='<td style="text-align:center;width:100px"><div class="col-md-4"><i class="fa fa-youtube" style="color:red;font-size:28px;"></i></div>'.$video.'<div class="col-md-4"><input type="checkbox" name="othervideo'.$machine.'"  value="'.$mo1->id.'" style="width:31px;height:21px" onchange="checknoofcheckbox();"></div></td>';
			
			$modspe=$this->db->select('specsfile')->from('modelspecificationfile')->where('modelid',$mo1->id)->get();
		    if($modspe->num_rows()>0)
		    {
				foreach($modspe->result() as $modspe1);
				
				$modfile="<a href='".page_url."upload/modelspecs/".$modspe1->specsfile."' target='_blank'><img src='".page_url.'/upload/modelspecs/'.$modspe1->specsfile."' style='width:40px;height:40px'></a>";
				
			}else{
				
				
				$modfile="<img src='".page_url."/upload/image404.png' style='width:100px;'>";
			}
			
			
                      $html.='<td style="text-align:center;width:55px">'.$modfile.'</td>';
		
		
		
				$html.='</tr>';
				
		}
		
		
		
		$html.="</table>";
		
		}else{
			$html='';
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
   
    if($this->input->post('othermachine')<>'')
    {
	$intmach=$this->input->post('othermachine');
	if(count($intmach)>0)
	{
		$intrestmachine=implode(',',$intmach);
	}else{
		
		$intrestmachine='';
	}
    }else
    {
        $intrestmachine='';
    }
    
	
	

	$intothermodel=$this->input->post('othermodel');
	 if($this->input->post('othermodel')<>'')
    {
	if(count($intothermodel)>0)
	{
		$intrestmode=implode(',',$intothermodel);
	}else{
		
		$intrestmode='';
	}
    }else
    {
        $intrestmode='';
    }
	
    
	
	
	$data=array('companyname'=>$this->input->post('usercompanyname'),'title'=>$this->input->post('title'),'contactperson'=>$this->input->post('contactperson'),'officecontact'=>$this->input->post('officecontact'),'mobile'=>$this->input->post('mobile'),'email'=>$this->input->post('email'),'location'=>$this->input->post('location'),'industry'=>$this->input->post('industry'),'subindustry'=>$this->input->post('subindustry'),'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'),'intrestedmachine'=>$intrestmachine,'intrestedmodel'=>$intrestmode);
	$this->db->insert('salestooldata',$data);
	$lid=$this->db->insert_id();
	
/** INTRODUCTORY WHATSAPP MESSAGE **/

$aksis=$this->getnameforso($_SESSION['logged_in']['user_id']);
if(count($aksis)>0)
{
    $name=$aksis[0];
    $email=$aksis[1];
    $contact=$aksis[2];
}else
{
    $name='';
    $email='';
    $contact='';
}
$smsmessage="Dear ".$this->input->post('title')." ".$this->input->post('contactperson')."\n It was a pleasure talking to you. Please feel free to contact me for any further details \n Regards, \n ".$name."\n Email- ".$email."\n Mobile-".$contact;

$data = [
    'phone' => "91".$this->input->post('mobile'), // Receivers phone
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

/** END **/
	
	
	$mach=$this->input->post('machine');
	for($i=0;$i<count($mach);$i++)
	{
		$model=$this->input->post('model');
		$capacity=$this->input->post('capacity');
		$sample=$this->input->post('sample');
		$data1=array('callid'=>$lid,'mid'=>$mach[$i],'model'=>$model[$i],'capacity'=>$capacity[$i],'sample'=>$sample[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('salestoolmachinedata',$data1);
		
		
		/** MACHINE PDF **/
            $mod=$this->input->post('inspdf'.$mach[$i]);
            if($mod<>'')
            {
            $this->sendmachinepdf($mach[$i],$model[$i],$this->input->post('mobile'));
            }
         
         /** END **/  
         
         	/** MACHINE VIDEO **/
            $videooooo=$this->input->post('videowhatsapp'.$mach[$i]);
            if($videooooo<>'')
            {
            $this->sendmachinevideo($mach[$i],$videooooo[$i],$this->input->post('mobile'));
            }
         
         /** END **/  
         
         
         /** MACHINE OTHER MODEL PDF **/
          $othermod=$this->input->post('othermodpdf'.$mach[$i]);
          if($othermod<>'')
          {
               $this->sendmachinepdf($mach[$i],$othermod,$this->input->post('mobile'));
          }
         
         /** END **/
         
           /** MACHINE OTHER MODEL VIDEO **/
          $othervid=$this->input->post('othervideo'.$mach[$i]);
          if($othermod<>'')
          {
               $this->sendmachinevideo($mach[$i],$othervid,$this->input->post('mobile'));
          }
         
         /** END **/
		
    }
    
  
	
	
		/** SCHEDULE DATE FOR THE CUSTOMER **/
		$todaydate=date('Y-m-d');
		$custome=$restyu=$this->db->select('a.id,a.days')->from('salestoolsettings a')->order_by('days','ASC')->get();
		if($custome->num_rows()>0)
		{
			foreach($custome->result() as $custome1)
			{
				$days=$custome1->days;
				$scheduleddate=date('Y-m-d',strtotime($todaydate."+".$days." days"));
				$datasc=array('callid'=>$lid,'scheduleddate'=>$scheduleddate,'addedOn'=>date('Y-m-d H:i:s'));
				$this->db->insert('salestoolscheduleddates',$datasc);
				
			}
		}
		/** END **/
		
		/** Send Whatsapp after the call **/
	    
	    
		
		/** end **/
	
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
		$newmachinedetails='';
		$restyui=$this->db->select('*')->from('salestooldata')->order_by('id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
			foreach($restyui->result() as $instruments)
			{
			
				
				$html1 = "<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;'>MACHINE.</th><th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>MODEL.</th>
				<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>CAPACITY.</th>
				<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>SAMPLE TO TEST.</th></tr>";
				$machinesat=$this->db->select('a.*,b.machinename')->from('salestoolmachinedata a')->join('salestoolmachines b','a.mid=b.id')->where('a.callid',$instruments->id)->get();
				if($machinesat->num_rows()>0)
				{
					
					foreach($machinesat->result() as $machinesat1)
					{
						if($machinesat1->model=='0')
						{
							$model="";
						}else{
							
							$moddel=$this->db->select('model')->from('salestoolmachinesmodel')->where('id',$machinesat1->model)->get();
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
							$capacity="";
						}else{
							
							$moddel1=$this->db->select('capacity')->from('salestoolmachinescapacity')->where('id',$machinesat1->capacity)->get();
							if($moddel1->num_rows()>0)
							{
								foreach($moddel1->result() as $modddel12);
								
								$capacity="-".$modddel12->capacity;

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
					
				
$newmachinedetails.=strtoupper($model)." ".strtoupper($machinesat1->machinename).strtoupper($capacity)."<br/><br/>";				
					
				
					}		
					
				}
				
				$indus='';
				$ewsrst=$this->db->select('industry')->from('salestoolindustry')->where('id',$instruments->industry)->get();
				if($ewsrst->num_rows()>0)
				{
					foreach($ewsrst->result() as $ewsrst1);
					$indus=$ewsrst1->industry;
				}
				
				$subindus='';
				$ewsrst1123=$this->db->select('subtype')->from('salestoolindustrysubtype')->where('id',$instruments->subindustry)->get();
				if($ewsrst1123->num_rows()>0)
				{
					foreach($ewsrst1123->result() as $ewsrst12);
					$subindus=$ewsrst12->subtype;
				}
			
			
				$intmachines='';
				if($instruments->intrestedmachine<>'')
				{
					$intmach=explode(',',$instruments->intrestedmachine);
					foreach($intmach as $intmach)
					{
						$intmachines.=$this->getmachinename($intmach).'<br/>';
						
					}
					
				}
				
				
				$intmod='';
				if($instruments->intrestedmodel<>'')
				{
					$intmode=explode(',',$instruments->intrestedmodel);
					foreach($intmode as $intmodel)
					{
						$intmod.=$this->getmodels($intmodel).'<br/>';
						
					}
					
				}
			
			
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'customername'=>strtoupper($instruments->title)." ".strtoupper($instruments->contactperson),
			'contact'=>strtoupper($instruments->mobile).",".strtoupper($instruments->officecontact),
			'email'=>strtoupper($instruments->email),
			'company'=>strtoupper($instruments->companyname),
			'industry'=>strtoupper($indus),
			'subindustry'=>strtoupper($subindus),
			'machinedetail'=>$newmachinedetails,
			'intmach'=>$intmachines,
			'intmodel'=>$intmod,
			'callername'=>strtoupper($fname),
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


function allcallingdataOldd()
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
				$html.="<td style='padding: 7px 8px 0px 8px; text-align:center;'>".strtoupper($instruments->title)." ".strtoupper($instruments->contactperson)."</td>";
			
				$html.="<td style='padding: 7px 8px 0px 8px; text-align:center'>".strtoupper($instruments->mobile)."</td>";
					$html.="<td style='padding: 7px 8px 0px 8px; text-align:center;'>".strtoupper($instruments->email)."</td>";
						$html.="<td style='padding: 7px 8px 0px 8px; text-align:center;'>".strtoupper($instruments->officecontact)."</td>";
							$html.="<td style='padding: 7px 8px 0px 8px; text-align:center;'>".strtoupper($instruments->location)."</td>";
				$html.="</tr>";
				
				$html1 = "<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;'>MACHINE.</th><th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>MODEL.</th>
				<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>CAPACITY.</th>
				<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>SAMPLE TO TEST.</th></tr>";
				$machinesat=$this->db->select('a.*,b.machinename')->from('salestoolmachinedata a')->join('salestoolmachines b','a.mid=b.id')->where('a.callid',$instruments->id)->get();
				if($machinesat->num_rows()>0)
				{
					
					foreach($machinesat->result() as $machinesat1)
					{
						if($machinesat1->model=='0')
						{
							$model="NO MODEL AVAILABLE";
						}else{
							
							$moddel=$this->db->select('model')->from('salestoolmachinesmodel')->where('id',$machinesat1->model)->get();
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
							
							$moddel1=$this->db->select('capacity')->from('salestoolmachinescapacity')->where('id',$machinesat1->capacity)->get();
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
				
				
				$indus='';
				$ewsrst=$this->db->select('industry')->from('salestoolindustry')->where('id',$instruments->industry)->get();
				if($ewsrst->num_rows()>0)
				{
					foreach($ewsrst->result() as $ewsrst1);
					$indus=$ewsrst1->industry;
				}
				
				$subindus='';
				$ewsrst1123=$this->db->select('subtype')->from('salestoolindustrysubtype')->where('id',$instruments->subindustry)->get();
				if($ewsrst1123->num_rows()>0)
				{
					foreach($ewsrst1123->result() as $ewsrst12);
					$subindus=$ewsrst12->subtype;
				}
				
					
					
					$html1.="<tr>";
				$html1.="<td style='padding: 7px 8px 0px 8px; text-align:center;'>".strtoupper($machinesat1->machinename)."</td>";
			
				$html1.="<td style='padding: 7px 8px 0px 8px; text-align:center'>".strtoupper($model)."</td>";
					$html1.="<td style='padding: 7px 8px 0px 8px; text-align:center;'>".strtoupper($capacity)."</td>";
						$html1.="<td style='padding: 7px 8px 0px 8px; text-align:center;'>".strtoupper($machinesat1->sample)."</td>";
				$html.="</tr>";
					}
					
					
				}
				
			
			
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'customerdetail'=>$html,
			'machinedetail'=>$html1,
			'industry'=>strtoupper($indus),
			'subindustry'=>strtoupper($subindus),
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
$exm=$this->input->post('existingmodel');
if(isset($exm))
{
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
		$existingvideo=$this->input->post('existingvideo'.$modelid);
		if($existingattachment=='')
		{
			
			$file=$_FILES['extsingattachment'.$modelid]['name'];
			if($file<>'')
			{
				$part=explode('.',$file);
				$ext=end($part);
				$fname=$part[0];
				$newname=$fname.'_'.rand(10,9999).'.'.$ext;
				
				move_uploaded_file($_FILES['extsingattachment'.$modelid]["tmp_name"],upload_url.'machineattachment/'.$newname);
				//@unlink($_SERVER['DOCUMENT_ROOT'].'/presto/upload/machineattachment/'.$existingattachment);
				
				
				$data3=array('mid'=>$mid,'modelid'=>$modelid,'attachment'=>$newname,'video'=>$existingvideo,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('modelwiseattachment',$data3);
			}else
			{
			    
			    	$data3=array('mid'=>$mid,'modelid'=>$modelid,'attachment'=>'','video'=>$existingvideo,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
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
				
				move_uploaded_file($_FILES['extsingattachment'.$modelid]["tmp_name"],upload_url.'machineattachment/'.$newname);
				@unlink(upload_url.'machineattachment/'.$existingattachment);
			}else{
				
				$newname=$existingattachment;
			}
			
			$data3=array('attachment'=>$newname,'updatedOn'=>date('Y-m-d H:i:s'),'video'=>$existingvideo);
			$this->db->where('mid',$mid);
			$this->db->where('modelid',$modelid);
			$this->db->update('modelwiseattachment',$data3);
		}
		
		
		
		

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
			$videosss=$this->input->post('video');
			
			if($file1<>'')
			{
			$part=explode('.',$file1);
			$ext=end($part);
			$fname=$part[0];
			$newname=$fname.'_'.rand(10,9999).'.'.$ext;

			move_uploaded_file($_FILES['extsingattachment']["tmp_name"][$t],upload_url.'machineattachment/'.$newname);
			
			
	
		$data5=array('mid'=>$mid,'modelid'=>$lid,'attachment'=>$newname,'video'=>$videosss[$t],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('modelwiseattachment',$data5);
		
			}
		
		}
		
		
	}
	
	
	
}


/** END **/

/*** OLD CAPACITY **/
$exca=$this->input->post('existingcapacity');
if(isset($exca))
{
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
	$subindustry=$this->input->post('subindustry');
	if(count($comp)>0)
	{
		
		for($c=0;$c<count($comp);$c++)
		{
			$data8=array('mid'=>$mid,'companyname'=>$comp[$c],'location'=>$location[$c],'industry'=>$industry[$c],'subindustry'=>$subindustry[$c],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			
			$this->db->insert('salestoolcompanyhistory',$data8);
			
		
		}
		
		
	}

}

/** END **/


/** OLD COMPANY **/
$excompp=$this->input->post('existcom');
if(isset($excompp))
{

	$existco=$this->input->post('existcom');
	if(count($existco)>0)
	{
		
		for($d=0;$d<count($existco);$d++)
		{
			$id=$existco[$d];
			$existingcompany=$this->input->post('exitingcompany'.$id);
			$existinglocation=$this->input->post('existinglocation'.$id);
			$existingindustry=$this->input->post('existingindustry'.$id);
			$existingsubindustry=$this->input->post('existingsubindustry'.$id);
			$data9=array('companyname'=>$existingcompany,'location'=>$existinglocation,'industry'=>$existingindustry,'subindustry'=>$existingsubindustry,'updatedOn'=>date('Y-m-d H:i:s'));
			
			$this->db->where('id',$id);
			$this->db->where('mid',$mid);
			$this->db->update('salestoolcompanyhistory',$data9);
			
		
		}
		
		
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
	$sample=$this->uri->segment(5);
	$html='';
	$restsy=$this->db->select('b.machinename,b.id')->from('relatedmachines a')->join('salestoolmachines b','a.relatedmid=b.id')->where('a.mid',$machine)->get();
	
	
	if($restsy->num_rows()>0)
	{
		$html .= "<table border='1' style='width:60%;'><tr  style='background-color:#EDFAFC'><th colspan='".$restsy->num_rows()."' style='padding: 7px 8px 0px 8px;'>For ".ucfirst(strtolower($sample))."</th></tr>";
	
	
		$i=1;
		$html.="<tr>";
		foreach($restsy->result() as $restsy1)
		{
			$html.="<td>".ucwords(strtolower($restsy1->machinename)).'</td>';
			
		$i++;
		}
		$html.="<tr/>";
		//$html.="<tr>";
		//foreach($restsy->result() as $restsy1)
		//{
		//$html.='<td style="text-align:center;"><input type="checkbox" name="othermachine[]" value="'.$restsy1->id.'" onchange="checkforothermodels();"></td>';
		//}
		//$html.="</tr>";
		
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
	$subindustry=$this->uri->segment(6);
	$subindus=$this->getsubindustryname($subindustry);
	$indus=$this->getindustryname($industry);
	$html='';
	$html1='';
	$machinename='';
	$filter1row=0;
	$filterrow2=0;
	$filterrow3=0;
	$filterrow4=0;
	$filterrow5=0;
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
	
$html1.="<table border='1' style='width:100%;'>
<tr style='background-color:white;text-align:center;'><th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;background-color:#EDFAFC;'>COMPANY.</th><!--<th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;background-color:#EDFAFC;''>MACHINE.</th>--><th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;background-color:#EDFAFC;''>LOCATION.</th>
		<!--<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>SUB INDUSTRY.</th>
		<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>INDUSTRY</th>--></tr>";
	
	$html=$machinename." ".$aftersampple.' '.$afterlocation.'<br/>';
	$this->db->select('companyname,location')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('subindustry',$subindustry)->order_by('companyname','ASC');
	//$this->db->like('location',urldecode($location),'both',false);
	$this->db->limit('10');
	$qye=$this->db->get();
	$filter1row=$qye->num_rows();
	if($qye->num_rows()>0)
	{
		
		
		
		
		foreach($qye->result() as $que1)
		{
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->companyname))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>-->
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->location))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>-->
			</tr>";
		}
		
	}
	
	
	/** END LEVEL 1 **/
	
	
	/** LEVEL 2 category with city**/
	$all="10";
	$remain=$all-$filter1row;
	if($filter1row<>'10')
	{
	$this->db->select('location,companyname,industry')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',$industry);
	$this->db->like('location',urldecode($location),'both',false)->order_by('companyname','ASC');
	$this->db->limit($remain);
	$qye=$this->db->get();
	$filterrow2=$qye->num_rows();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->companyname))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>-->
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($location))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>-->
			</tr>";
		}
		
		
   }

   
   /** END **/
   
   
    /** LEVEL 2 category all city**/
  /** if($filter1row+$filterrow2<10)
   {
	   $rem="10";
	   $other=$filter1row+$filterrow2;
	   $threerdfilter=$rem-$other;
  

	$this->db->select('location,companyname,industry')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',$industry)->order_by('companyname','ASC');
	$this->db->limit($threerdfilter);
	$qye=$this->db->get();
	$filterrow3=$qye->num_rows();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->companyname))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>-->
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->location))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>-->
			</tr>";
		}
		
		
   }
   
   }**/
   
   
   
   /** CHECK FOR MACHINE ONLY **/
   
  /**  if($filter1row+$filterrow2+$filterrow3<10)
   {
	   $rem="10";
	   $other=$filter1row+$filterrow2+$filterrow3;
	   $threerdfilter1=$rem-$other;
  

	$this->db->select('location,companyname,industry')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine)->order_by('companyname','ASC');
	$this->db->limit($threerdfilter1);
	$qye=$this->db->get();
	$filterrow4=$qye->num_rows();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->companyname))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>-->
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->location))."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>-->
			</tr>";
		}
		
		
   }
   
   }**/
   
   /** END **/
   
   /** Default Names **/
   
   
    
    if($filter1row+$filterrow2<10)
   {
	   $rem="10";
	   $other=$filter1row+$filterrow2+$filterrow3+$filterrow4;
	   $threerdfilter2=$rem-$other;
   /** LEVEL 3 ONLY MACHINE**/

	$this->db->select('companyname')->from('salestooldefaultcompany')->where('industry',$industry)->order_by('companyname','ASC');
	$this->db->limit($threerdfilter2);
	$qye=$this->db->get();
	$filterrow5=$qye->num_rows();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".ucwords(strtolower($que1->companyname))."</td>
			<td style='padding: 7px 8px 0px 8px;'>-</td>
		
			</tr>";
		}
		
		
   }
   
   }
   
   
   /** END **/
   
   
}
   /** END **/
	
echo $html.$html1;

}



function generatemachineusagedetailsoldbeforedefaultcompany()
{
	$machine=$this->uri->segment(3);
	$industry=$this->uri->segment(4);
	$location=$this->uri->segment(5);
	$subindustry=$this->uri->segment(6);
	$subindus=$this->getsubindustryname($subindustry);
	$indus=$this->getindustryname($industry);
	$html='';
	$html1='';
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
	
	$html1.="<table border='1' style='width:100%;'>
	<tr style='background-color:white;text-align:center;'><th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;'>COMPANY.</th><!--<th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;'>MACHINE.</th>--><th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>LOCATION.</th>
				<!--<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>SUB INDUSTRY.</th>
				<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>INDUSTRY</th>--></tr>";
	
	$html=$machinename." ".$aftersampple.' '.$afterlocation.'<br/>';
	$this->db->select('companyname,location')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('subindustry',$subindustry);
	//$this->db->like('location',urldecode($location),'both',false);
	$this->db->limit('10');
	$qye=$this->db->get();
	$filter1row=$qye->num_rows();
	if($qye->num_rows()>0)
	{
		
		
		
		
		foreach($qye->result() as $que1)
		{
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>-->
			<td style='padding: 7px 8px 0px 8px;'>".$que1->location."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>-->
			</tr>";
		}
		
	}
	
	
	/** END LEVEL 1 **/
	
	
	/** LEVEL 2 category with city**/
	$all="10";
	$remain=$all-$filter1row;
	if($filter1row<>'10')
	{
	$this->db->select('location,companyname,industry')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',$industry);
	$this->db->like('location',urldecode($location),'both',false);
	$this->db->limit($remain);
	$qye=$this->db->get();
	$filterrow2=$qye->num_rows();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>-->
			<td style='padding: 7px 8px 0px 8px;'>".$location."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>-->
			</tr>";
		}
		
		
   }

   
   /** END **/
   
   
   if($filter1row+$filterrow2<10)
   {
	   $rem="10";
	   $other=$filter1row+$filterrow2;
	   $threerdfilter=$rem-$other;
   /** LEVEL 2 category all city**/

	$this->db->select('location,companyname,industry')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',$industry);
	$this->db->limit($threerdfilter);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>-->
			<td style='padding: 7px 8px 0px 8px;'>".$que1->location."</td>
			<!--<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>-->
			</tr>";
		}
		
		
   }
   
   }
   
   
}
   /** END **/
	
	
	
	
	
	
	
	

echo $html.$html1;

}



function generatemachineusagedetailsOld27apr()
{
	$machine=$this->uri->segment(3);
	$industry=$this->uri->segment(4);
	$location=$this->uri->segment(5);
	$subindustry=$this->uri->segment(6);
	$subindus=$this->getsubindustryname($subindustry);
	$indus=$this->getindustryname($industry);
	$html='';
	$html1='';
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
	
	$html1.="<table border='1' style='width:100%;'>
	<tr style='background-color:white;text-align:center;'><th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;'>COMPANY.</th><th style='padding: 7px 8px 0px 8px;  text-align:center;font-weight:bold;'>MACHINE.</th><th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>LOCATION.</th>
				<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>SUB INDUSTRY.</th>
				<th style='padding: 7px 8px 0px 8px; text-align:center;font-weight:bold;'>INDUSTRY</th></tr>";
	
	$html=$machinename." ".$aftersampple.' '.$afterlocation.'<br/>';
	$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('subindustry',$subindustry);
	$this->db->like('location',urldecode($location),'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		
		
		
		
		foreach($qye->result() as $que1)
		{
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$location."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>
			</tr>";
		}
		
		
}else{
	
	
	$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('subindustry',$subindustry);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>
			<td style='padding: 7px 8px 0px 8px;'>--</td>
			<td style='padding: 7px 8px 0px 8px;'>".$subindus."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>
			</tr>";
		}
		
		
   }else{
	   
	   
	   $this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',$industry);
	$this->db->like('location',urldecode($location),'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
	
		foreach($qye->result() as $que1)
		{
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$location."</td>
			<td style='padding: 7px 8px 0px 8px;'>--</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>
			</tr>";
		}
	   
	  
	}else{

$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',urldecode($industry));
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>
			<td style='padding: 7px 8px 0px 8px;'>--</td>
			<td style='padding: 7px 8px 0px 8px;'>--</td>
			<td style='padding: 7px 8px 0px 8px;'>".$indus."</td>
			</tr>";
		}
		
		
	}else
	{
		
		$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
$this->db->like('location',urldecode($location),'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$machinename."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$location."</td>
			<td style='padding: 7px 8px 0px 8px;'>--</td>
			<td style='padding: 7px 8px 0px 8px;'>---</td>
			</tr>";
		}
		
		
	}else{
		
		$this->db->select('companyname,mid')->from('salestoolcompanyhistory');
        $this->db->like('location',urldecode($location),'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			$macname=$this->getmachinename($que1->mid);
			$html1.="<tr>
			<td style='padding: 7px 8px 0px 8px;'>".$que1->companyname."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$macname."</td>
			<td style='padding: 7px 8px 0px 8px;'>".$location."</td>
			<td style='padding: 7px 8px 0px 8px;'>--</td>
			<td style='padding: 7px 8px 0px 8px;'>---</td>
			</tr>";
		}
		
	}
	}
	}
	}		
	}
	}

echo $html.$html1;

}


function generatemachineusagedetailsoldddddddddddddddd()
{
	$machine=$this->uri->segment(3);
	$industry=$this->uri->segment(4);
	$location=$this->uri->segment(5);
	$subindustry=$this->uri->segment(6);
	$html='';
	$html1='';
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
	
	$html1.="<table border='1' style='width:350px;'>";
	$html=$machinename." ".$aftersampple.' '.$location.' '.$afterlocation.'<br/>';
	$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('subindustry',$subindustry);
	$this->db->like('location',$location,'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		
		
		
		foreach($qye->result() as $que1)
		{
			$html1.="<tr><td style='padding: 7px 8px 0px 8px;'>".$que1->companyname.'</td></tr>';
		}
		
		
}else{
	
	
$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->where('industry',$industry);
	$this->db->like('location',$location,'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
	
		foreach($qye->result() as $que1)
		{
			$html1.="<tr><td style='padding: 7px 8px 0px 8px;'>".$que1->companyname.'</td></tr>';
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
			$html1.="<tr><td style='padding: 7px 8px 0px 8px;'>".$que1->companyname.'</td></tr>';
		}
		
		
	}else{
		
		
		$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
$this->db->like('location',$location,'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			$html1.="<tr><td style='padding: 7px 8px 0px 8px;'>".$que1->companyname.'</td></tr>';
		}
		
		
	}else{
		
		
		$this->db->select('companyname')->from('salestoolcompanyhistory');
        $this->db->like('location',$location,'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		foreach($qye->result() as $que1)
		{
			$html1.="<tr><td style='padding: 7px 8px 0px 8px;'>".$que1->companyname.'</td></tr>';
		}
		
		
	}
		
	}
		
	}
	
}
}

echo $html.$html1;

}


function generatemachineusagedetailsOldforbackup()
{
	$machine=$this->uri->segment(3);
	$industry=$this->uri->segment(4);
	$location=$this->uri->segment(5);
	$html='';
	$html1='';
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
	
	$html1.="<table border='1' style='width:350px;'>";
	$html=$machinename." ".$aftersampple.' '.$location.' '.$afterlocation.'<br/>';
	$this->db->select('companyname')->from('salestoolcompanyhistory');
	$this->db->where('mid',$machine);
	$this->db->like('location',$location,'both',false);
	$qye=$this->db->get();
	if($qye->num_rows()>0)
	{
		
		
		
		foreach($qye->result() as $que1)
		{
			$html1.="<tr><td style='padding: 7px 8px 0px 8px;'>".$que1->companyname.'</td></tr>';
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
			$html1.="<tr><td style='padding: 7px 8px 0px 8px;'>".$que1->companyname.'</td></tr>';
		}
		
		
	}
	
}

echo $html.$html1;
	
}

function library()
{
	
	$this->load->view('salestool/salestoollibrary');
	
}

function updatelibrary()
{
	/** EXISTING EDIT **/
	$exi=$this->input->post('existing');
	for($k=0;$k<count($exi);$k++)
	{
		$extype=$this->input->post('existingtype'.$exi[$k]);
		$exdays=$this->input->post('exitingdays'.$exi[$k]);
		$exw=$this->input->post('exitingintrow'.$exi[$k]);
		if($exw<>'')
		{
			$ww='1';
		}else{ $ww='0'; }
		
		$exe=$this->input->post('existingintroe'.$exi[$k]);
		if($exe<>'')
		{
			$ee='1';
		}else{ $ee='0'; }
		
		$exs=$this->input->post('existingintros'.$exi[$k]);
		if($exs<>'')
		{
			$ss='1';
		}else{ $ss='0'; }
		
		
		$datas=array('type'=>$extype,'days'=>$exdays,'whatsapp'=>$ww,'sms'=>$ss,'email'=>$ee,'updatedOn'=>date('Y-m-d H:i:s'),'updatedBy'=>$_SESSION['logged_in']['user_id']);

			$this->db->where('id',$exi[$k]);
		$this->db->update('salestoolsettings',$datas);
		
	}
	/** end **/
	if($this->input->post('addnew')==1)
	{
	$type=$this->input->post('type');
	$days=$this->input->post('days');
		
		for($i=0;$i<count($type);$i++)
		{
			$w=0; $e=0; $s=0;
		
		
			$introw=$this->input->post('introw'.$i);
			
			if($introw=='')
			{
				$w=0;
			}else{ $w=1; }
			
			$introwe=$this->input->post('introe'.$i);
			if($introwe=='')
			{
				$e=0;
			}else{ $e=1; }
			$introws=$this->input->post('intros'.$i);
		
			if($introws=='')
			{
				$s=0;
			}else{ $s=1; }
			
			$data=array('type'=>$type[$i],'days'=>$days[$i],'whatsapp'=>$w,'sms'=>$s,'email'=>$e,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);

$this->db->insert('salestoolsettings',$data);
		}
		
	}
		
		$this->session->flashdata('response','Added');
		redirect(page_url.'Salestool/library');

}

function deletelib()
{
	
	$id=$this->uri->segment(3);
	
	$this->db->where('id',$id);
	$this->db->delete('salestoolsettings');
	
	$this->session->set_flashdata('message','Deleted');
	redirect(page_url.'Salestool/library');
	
}

function salestoollibrary()
{
	
	$this->load->view('salestool/library');
	
}

function getlibrary()
{
	
	$scheduler_data = array();
		
		$restyui=$this->db->select('a.*,b.type')->from('salestoolsettings a')->join('salestoolcommunication b','a.type=b.id')->order_by('a.id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white;text-align:center;'><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>TYPE.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>DAYS.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>WHATSAPP.</th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>EMAIL </th><th style='padding:2px 2px 2px 2px;text-align:center;font-weight:bold;'>SMS.</th></tr>";
		
			foreach($restyui->result() as $instruments)
			{
				if($instruments->whatsapp=='1')
				{
					$w="<i class='fa fa-check'></i>";
				}else{
					
					$w="<i class='fa fa-close'></i>";
				}
				
				if($instruments->email=='1')
				{
					$e="<i class='fa fa-check'></i>";
				}else{
					
					$e="<i class='fa fa-close'></i>";
				}
				
				if($instruments->sms=='1')
				{
					$s="<i class='fa fa-check'></i>";
				}else{
					
					$s="<i class='fa fa-close'></i>";
				}
			
				
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($instruments->type)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($instruments->days)."</td>";
					$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$w."</td>";
						$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$e."</td>";
							$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$s."</td>";
				$html.="</tr>";
				
					
					
					
				}
				
			
			$edit='<a href="'.page_url.'Salestool/library"><span class="btn btn-sm btn-success">Edit</span></a>';
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'customerdetail'=>$html,
			'action'=>$edit);
			$i++;
			
		
			}
			
		
		
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
	
}


function communication()
{
	$this->load->view('salestool/communication');
	
}

function addcommunication()
{
	$type=$this->input->post('type');
	$pdf=$this->input->post('pdf');
	if($pdf<>'')
	{
		$p=1;
	}else{ $p=0; }
	$video=$this->input->post('video');
	if($video<>'')
	{
		$v=1;
	}else{ $v=0; }
	
	
	
	$message=$this->escapeString($this->input->post('msg'));
	$fmessage=$this->escapeString($this->input->post('fmsg'));
	$status=$this->input->post('status');
	
	$data=array('type'=>$type,'communication'=>$message,'endline'=>$fmessage,'pdf'=>$p,'video'=>$v,'addedOn'=>date('Y-m-d H:is'),'addedBy'=>$_SESSION['logged_in']['user_id'],'status'=>$status);
	
	$this->db->insert('salestoolmessages',$data);
	
	$this->session->flashdata('message','Record Added');
	redirect(page_url.'Salestool/communication');
	
	
}


function escapeString($val) {
    $db = get_instance()->db->conn_id;
    $val = mysqli_real_escape_string($db, $val);
    return $val;
}

function commdata()
{
	
	$scheduler_data = array();
		
		$restyui=$this->db->select('a.*,b.type')->from('salestoolmessages a')->join('salestoolcommunication b','a.type=b.id')->order_by('a.id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
			foreach($restyui->result() as $instruments)
			{
				if($instruments->pdf=='1')
				{
					$p="Yes";
				}else{ $p="No"; }
				
				if($instruments->video=='1')
				{
					$v="Yes";
				}else{ $v="No"; }
				
			$edit='<a href="'.page_url.'Salestool/editcommunication/'.$instruments->id.'"><span class="btn btn-sm btn-success">Edit</span></a>';
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$instruments->type,
			'pdf'=>$p,
			'video'=>$v,
			'message'=>$instruments->communication,
			'endline'=>$instruments->endline,
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

function editcommunication()
{
	$this->load->view('salestool/editcommunication');
	
}

function updatecommunication()
{
	$id=$this->uri->segment('3');
	$type=$this->input->post('type');
	$pdf=$this->input->post('pdf');
	if($pdf<>'')
	{
		$p=1;
	}else{ $p=0; }
	$video=$this->input->post('video');
	if($video<>'')
	{
		$v=1;
	}else{ $v=0; }
	
	$message=$this->escapeString($this->input->post('msg'));
	$fmessage=$this->escapeString($this->input->post('fmsg'));
	$status=$this->input->post('status');
	
	$data=array('type'=>$type,'communication'=>$message,'endline'=>$fmessage,'pdf'=>$p,'video'=>$v,'addedOn'=>date('Y-m-d H:is'),'addedBy'=>$_SESSION['logged_in']['user_id'],'status'=>$status);
	
	$this->db->where('id',$id);
	$this->db->update('salestoolmessages',$data);
	
	$this->session->flashdata('message','Updated');
	redirect(page_url.'Salestool/communication');
	
}

function communicationtype()
{
	$this->load->view('salestool/communicationtype');
	
}

function addcommunicationtype()
{
	$type=$this->input->post('type');
	$status=$this->input->post('status');

	$data=array('type'=>$type,'addedOn'=>date('Y-m-d H:is'),'addedBy'=>$_SESSION['logged_in']['user_id'],'status'=>$status);
	
	$this->db->insert('salestoolcommunication',$data);
	
	$this->session->flashdata('message','Record Added');
	redirect(page_url.'Salestool/communicationtype');
	
}
function commdatatypelist()
{
	
	$scheduler_data = array();
		
		$restyui=$this->db->select('a.*')->from('salestoolcommunication
 a')->order_by('a.id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
			foreach($restyui->result() as $instruments)
			{
		if($instruments->status=='1')
		{
			$sta="ACTIVE";
		}else{
			
			$sta="INACTIVE";
		}
				
			$edit='<a href="'.page_url.'Salestool/editcommtype/'.$instruments->id.'"><span class="btn btn-sm btn-success">Edit</span></a>';
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$instruments->type,
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

function editcommtype()
{
	
	$this->load->view('salestool/editcommunicationtype');
}

function updatecommunicationtype()
{
	
	$id=$this->uri->segment('3');
	$type=$this->input->post('type');
	$status=$this->input->post('status');

	$data=array('type'=>$type,'addedOn'=>date('Y-m-d H:is'),'addedBy'=>$_SESSION['logged_in']['user_id'],'status'=>$status);
	
	$this->db->where('id',$id);
	$this->db->update('salestoolcommunication',$data);
	
	$this->session->flashdata('message','Record Added');
	redirect(page_url.'Salestool/communicationtype');
	
	
}

function industrytype()
{
	$this->load->view('salestool/industrytype');
	
	
}

function list_industryOld()
{
	
	$scheduler_data = array();
		
		$restyui=$this->db->select('a.*')->from('salestoolindustry a')->order_by('a.id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
			foreach($restyui->result() as $instruments)
			{	
			$edit='<a href="'.page_url.'Salestool/edit_industry/'.$instruments->id.'"><span class="btn btn-sm btn-success">Edit</span></a>';
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$instruments->industry,
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

function addindustryOld()
{
	
	$unitname=trim($this->input->post('unitname'));
		$restyyuu=$this->db->select('id')->from('salestoolindustry')->where('industry',$unitname)->get();
		if($restyyuu->num_rows()==0)
		{
			
			$data=array('industry'=>$unitname,'status'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('salestoolindustry',$data);
			
			$this->session->set_flashdata('message','Record Added');
			redirect(page_url.'Salestool/industrytype');	
			
			
		}else
		{
			$this->session->set_flashdata('message','Record Exists');
				redirect(page_url.'Salestool/industrytype');	
		}
		
	
	
}


function list_industry()
{
	
	$scheduler_data = array();
		
		
		$restyui=$this->db->select('a.*')->from('salestoolindustry a')->order_by('a.id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			
		
	
	
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
		
			foreach($restyui->result() as $instruments)
			{	
			$htm='';
		$htm1='';
			
			$indussubty=$this->db->select('subtype,id')->from('salestoolindustrysubtype')->where('industryid',$instruments->id)->get();
												if($indussubty->num_rows()>0)
												{
													foreach($indussubty->result() as $indussubty1)
													{
			$htm.=strtoupper($indussubty1->subtype)."<br/>";
													}
													
												}else{
													
													$htm.="NO SUBTYPE AVAILABLE";
									
												}
												
												
													$indussubty1=$this->db->select('sampletype,id')->from('salestoolindustrysampletype')->where('industryid',$instruments->id)->get();
												if($indussubty1->num_rows()>0)
												{
													foreach($indussubty1->result() as $indussubty12)
													{
			$htm1.=strtoupper($indussubty12->sampletype)."<br/>";
													}
													
												}else{
													
													$htm1.="NO SUBTYPE AVAILABLE";
									
												}
							
							
			$edit='<a href="'.page_url.'Salestool/edit_industry/'.$instruments->id.'"><span class="btn btn-sm btn-success">Edit</span></a>';
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$instruments->industry,
			'subtype'=>$htm,
			'sampletype'=>$htm1,
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


function list_industryOldbeforesample()
{
	
	$scheduler_data = array();
		
		$restyui=$this->db->select('a.*')->from('salestoolindustry a')->order_by('a.id','DESC')->get();
		if($restyui->num_rows()>0)
		{
			
		
	
	
			$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;
		
			foreach($restyui->result() as $instruments)
			{	
			
			$htm='';
		$htm.="<table border='1' style='width:60%;'><tr style='background-color:white;text-align:left;'><th style='padding:2px 2px 2px 2px;text-align:left;font-weight:bold;width:70%;'>SUBTYPE.</th></tr>";
		
			$indussubty=$this->db->select('subtype,id')->from('salestoolindustrysubtype')->where('industryid',$instruments->id)->get();
												if($indussubty->num_rows()>0)
												{
													foreach($indussubty->result() as $indussubty1)
													{
			$htm.="<tr><td style='padding:2px 2px 2px 2px; text-align:left;'>".strtoupper($indussubty1->subtype)."</td></tr>";
													}
													
												}else{
													
													$htm.="<tr><td style='padding:2px 2px 2px 2px; text-align:left;'>No Subtype Available</td></tr>";
									
												}
							
							
			$edit='<a href="'.page_url.'Salestool/edit_industry/'.$instruments->id.'"><span class="btn btn-sm btn-success">Edit</span></a>';
				//echo $script;exit;
			$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$instruments->industry,
			'subtype'=>$htm,
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


function addindustry()
{
	
	$unitname=trim($this->input->post('unitname'));
		$restyyuu=$this->db->select('id')->from('salestoolindustry')->where('industry',$unitname)->get();
		if($restyyuu->num_rows()==0)
		{
			
			$data=array('industry'=>$unitname,'status'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('salestoolindustry',$data);
			$lid=$this->db->insert_id();
			/** ADD SUB INDUSTRY **/
			$stype=$this->input->post('subtype');
			for($i=0;$i<count($stype);$i++)
			{
				$data1=array('industryid'=>$lid,'subtype'=>$stype[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('salestoolindustrysubtype',$data1);
			}
			/** END **/
			
			/** ADD SAMPLE TYPE **/
			$stype1=$this->input->post('sampletype');
			for($i=0;$i<count($stype1);$i++)
			{
				$data1=array('industryid'=>$lid,'sampletype'=>$stype1[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('salestoolindustrysampletype',$data1);
			}
			/** END **/
			
			$this->session->set_flashdata('message','Record Added');
			redirect(page_url.'Salestool/industrytype');	
			
			
		}else
		{
			$this->session->set_flashdata('message','Record Exists');
				redirect(page_url.'Salestool/industrytype');	
		}
		
	
	
}



function addindustryolebeforesample()
{
	
	$unitname=trim($this->input->post('unitname'));
		$restyyuu=$this->db->select('id')->from('salestoolindustry')->where('industry',$unitname)->get();
		if($restyyuu->num_rows()==0)
		{
			
			$data=array('industry'=>$unitname,'status'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('salestoolindustry',$data);
			$lid=$this->db->insert_id();
			/** ADD SUB INDUSTRY **/
			$stype=$this->input->post('subtype');
			for($i=0;$i<count($stype);$i++)
			{
				$data1=array('industryid'=>$lid,'subtype'=>$stype[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('salestoolindustrysubtype',$data1);
			}
			/** END **/
			$this->session->set_flashdata('message','Record Added');
			redirect(page_url.'Salestool/industrytype');	
			
			
		}else
		{
			$this->session->set_flashdata('message','Record Exists');
				redirect(page_url.'Salestool/industrytype');	
		}
		
	
	
}


function edit_industry()
{
	
	$this->load->view('salestool/edit_industrytype');
	
	
}

function update_industry()
{
	$unitname=trim($this->input->post('unitname'));
	$id=$this->uri->segment(3);
	
			$data=array('industry'=>$unitname);
			$this->db->where('id',$id);
			$this->db->update('salestoolindustry',$data);
			
			/** EXISTING SUBTYPE **/
			
			if(isset($_POST['existsubtypeid']))
			{
			$existsubtypeid=$this->input->post('existsubtypeid');
			for($i=0;$i<count($existsubtypeid);$i++)
			{
				$ids=$existsubtypeid[$i];
				$subtype=$this->input->post('existsubtype'.$ids);
				$data1=array('subtype'=>$subtype,'updatedOn'=>date('Y-m-d H:i:s'),'updatedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->where('id',$ids);
				$this->db->update('salestoolindustrysubtype',$data1);
			}
			}
			/** END **/
			
			/** EXISTING SAMPLETYPE **/
			
			if(isset($_POST['existsampleid']))
			{
			$existsampletypeid=$this->input->post('existsampleid');
			for($i=0;$i<count($existsampletypeid);$i++)
			{
				$ids=$existsampletypeid[$i];
				$subtype=$this->input->post('existsampletype'.$ids);
				$data1=array('sampletype'=>$subtype,'updatedOn'=>date('Y-m-d H:i:s'),'updatedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->where('id',$ids);
				$this->db->update('salestoolindustrysampletype',$data1);
			}
			}
			/** END **/
			
			
			
			/** ADD NEW SUBTYPE **/
			if($this->input->post('addnewcheck')=='1')
			{
				
				$stype=$this->input->post('subtype');
			for($k=0;$k<count($stype);$k++)
			{
				$data2=array('industryid'=>$id,'subtype'=>$stype[$k],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('salestoolindustrysubtype',$data2);
			}
				
				
				
			}
			/** END **/
			
			
			/** ADD NEW SUBTYPE **/
			if($this->input->post('addnewsamplecheck')=='1')
			{
				
				$stype=$this->input->post('sampletype');
			for($k=0;$k<count($stype);$k++)
			{
				$data2=array('industryid'=>$id,'sampletype'=>$stype[$k],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('salestoolindustrysampletype',$data2);
			}
				
				
				
			}
			/** END **/
			
			$this->session->set_flashdata('message','Record Added');
			redirect(page_url.'Salestool/industrytype');	
	
	
}



function update_industryOldbefsampletype()
{
	
	$unitname=trim($this->input->post('unitname'));
	$id=$this->uri->segment(3);
	
			$data=array('industry'=>$unitname);
			$this->db->where('id',$id);
			$this->db->update('salestoolindustry',$data);
			
			/** EXISTING SUBTYPE **/
			
			if(isset($_POST['existsubtypeid']))
			{
			$existsubtypeid=$this->input->post('existsubtypeid');
			for($i=0;$i<count($existsubtypeid);$i++)
			{
				$ids=$existsubtypeid[$i];
				$subtype=$this->input->post('existsubtype'.$ids);
				$data1=array('subtype'=>$subtype,'updatedOn'=>date('Y-m-d H:i:s'),'updatedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->where('id',$ids);
				$this->db->update('salestoolindustrysubtype',$data1);
			}
			}
			/** END **/
			
			/** ADD NEW SUBTYPE **/
			if($this->input->post('addnewcheck')=='1')
			{
				
				$stype=$this->input->post('subtype');
			for($k=0;$k<count($stype);$k++)
			{
				$data2=array('industryid'=>$id,'subtype'=>$stype[$k],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('salestoolindustrysubtype',$data2);
			}
				
				
				
			}
			/** END **/
			
			$this->session->set_flashdata('message','Record Added');
			redirect(page_url.'Salestool/industrytype');	
	
	
}


function update_industryOld()
{
	
	$unitname=trim($this->input->post('unitname'));
	$id=$this->uri->segment(3);
	
			$data=array('industry'=>$unitname);
			$this->db->where('id',$id);
			$this->db->update('salestoolindustry',$data);
			
			$this->session->set_flashdata('message','Record Added');
			redirect(page_url.'Salestool/industrytype');	
	
	
}

function getsubindustry()
{
	$industry=$this->uri->segment(3);
	
	$restyu=$this->db->select('subtype,id')->from('salestoolindustrysubtype')->where('industryid',$industry)->get();
	
	echo '<option value="">Select Sub Industry</option>';
	if($restyu->num_rows()>0)
	{
		
		foreach($restyu->result() as $restyu1)
		{
			echo '<option value="'.$restyu1->id.'">'.strtoupper($restyu1->subtype).'</option>';
			
		}
		
		
	}


}


function getsubindustryname($id)
{
	$subind='';
	$sub=$this->db->select('subtype')->from('salestoolindustrysubtype')->where('id',$id)->get();
	if($sub->num_rows()>0)
	{
		foreach($sub->result() as $subb);
		
		$subind=$subb->subtype;
		
		
	}
	
	return $subind;
	
	
}

function getindustryname($id)
{

	$subind='';
	$sub=$this->db->select('industry')->from('salestoolindustry	')->where('id',$id)->get();
	if($sub->num_rows()>0)
	{
	foreach($sub->result() as $subb);

	$subind=$subb->industry;


	}

	return $subind;


}


function getmachinename($mid)
{
	
	$subind='';
	$sub=$this->db->select('machinename')->from('salestoolmachines
	')->where('id',$mid)->get();
	if($sub->num_rows()>0)
	{
	foreach($sub->result() as $subb);

	$subind=$subb->machinename;
    }

	return $subind;
	
	
}


function getmodels($mid)
{
	
	$subind='';
	$sub=$this->db->select('model')->from('salestoolmachinesmodel
	')->where('id',$mid)->get();
	if($sub->num_rows()>0)
	{
	foreach($sub->result() as $subb);

	$subind=$subb->model;
    }

	return $subind;
	
	
}

function getlocations()
{
	 $prevbom=array();
		$searchterm= $_GET['q'];
		
	//$que=$this->db->select('location')->from('salestoolcompanyhistory')->like('LOWER(location)',strtolower($searchterm),'both',false)->group_by('location')->get();
	$que=$this->db->select('city_name')->from('cities')->like('LOWER(city_name)',strtolower($searchterm),'both',false)->group_by('city_name')->get();
	
	if($que->num_rows()>0)
	{
		foreach($que->result() as $itemdata)
		{
			$json[] = array('id'=>$itemdata->city_name, 'text'=>STRTOUPPER($itemdata->city_name));
		}
	}else{
	$json[] = array('id'=>"", 'text'=>"No Data Available");
	}
	
	echo json_encode($json);
	
}

function othermodelscustomerlike()
{
	$html='';
	$othermodel=$this->uri->segment(3);
	$othermachine=$this->uri->segment(4);
	if($othermodel<>'')
	{
	
		$rety=explode(',',$othermodel);
		
		if(count($rety)>0)
		{
			$html.="You also Interested in ";
			 $i=0;
			foreach($rety as $rety1)
			{
				
				$mmosl=$this->db->select('a.model,b.machinename')->from('salestoolmachinesmodel a')->join('salestoolmachines
 b','a.mid=b.id')->where('a.id',$rety1)->get();
 if($mmosl->num_rows()>0)
 {
	
	 foreach($mmosl->result() as $mmosl1);
	 
	 if($i==0)
	 {
		 $comm='';
	 }else
	 {
		 $comm=",";
	 }
	 $html.=$comm.ucfirst($mmosl1->machinename)." ".ucfirst($mmosl1->model);
 }
				
				
			$i++;
			}
			
			
			
		}
		
		
		
		
		
		
	}
	
	$htmlma='';
	if($othermachine<>'')
	{
		
			$retyma=explode(',',$othermachine);
		
		if(count($retyma)>0)
		{
			
			
			 $j=0;
			foreach($retyma as $retyma1)
			{
				
				$mmosl2=$this->db->select('b.machinename')->from('salestoolmachines
 b')->where('b.id',$retyma1)->get();
 if($mmosl2->num_rows()>0)
 {
	
	 foreach($mmosl2->result() as $mmosl21);
	 
	
			if($j==0)
			{
			$comm1='';
			}else
			{
			$comm1=",";
			}
			
	 $htmlma.=$comm1.ucfirst($mmosl21->machinename);
 }
				
				
				
			$j++;
			}
			
			
			
		}
		
		
		
		
		
	}
	
	
	
		if($html<>'')
		{
		$scesep=",";
		}else
		{
		$scesep="";
		}
	
	
	echo ucfirst(strtolower($html.$scesep.$htmlma));
	
	
	
	
}


function getsamples()
{
	$ind=$_GET['q'];
	
	$que=$this->db->select('sampletype')->from('salestoolindustrysampletype')->like('sampletype',$ind)->group_by('sampletype')->get();
	
		if($que->num_rows()>0)
	{
		foreach($que->result() as $itemdata)
		{
			$json[] = array('id'=>$itemdata->sampletype, 'text'=>STRTOUPPER($itemdata->sampletype));
		}
	}else{
	$json[] = array('id'=>"", 'text'=>"No Data Available");
	}
	
	echo json_encode($json);
	
	
}

function pdffromwhatsapp()
{
    $title= $this->input->post('title');
    $machine= $this->input->post('machine');
    $mob= $this->input->post('mob');
    $model= $this->input->post('model');
    $name= $this->input->post('name');
    /** GET PDF **/
    $modat=$this->db->select('attachment')->from('modelwiseattachment
')->where('modelid',$model)->where('mid',$machine)->get();
if($modat->num_rows()>0)
{
    foreach($modat->result() as $modat1);
    $filename=$modat1->attachment;
    
    $machinename=$this->getmachinename($machine);
    $modelname=$this->getmodels($model);
    $caption="Hello ".$title." ".$name." You are intested in knowing the specification of our ".$machinename." Model-".$modelname." Please find the specification file for the same";
    /** END **/
    
    /***WHATSAPP INTEGRATION***/
    
    $contact="91".$mob;

$overallpath=page_url."upload/machineattachment/".$filename;

$data = [
    'phone' => $contact, // Receivers phone
    'body' => $overallpath,
	'filename'=>$filename,
	'caption'=>$caption
	// Message
];

//echo "<pre>"; print_r($data);exit;
	
$json = json_encode($data); // Encode data to JSON
// URL for request POST /message
$url = 'https://api.chat-api.com/instance88514/sendFile?token=lwpwzff7ubbp6dc6';
// Make a POST request
/**$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);**/
// Send a request
//$result = file_get_contents($url, false, $options);

/** CURL **/

// API URL
$url = $url;

// Create a new cURL resource
$ch = curl_init($url);

// Setup request to send json via POST
$payload = $json;

// Attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

// Return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the POST request
$result = curl_exec($ch);

// Close cURL resource
curl_close($ch);

/** END **/
echo $result;

}else
{
    
    echo "PDF NOT AVAILABLE";
}
/***WHATSAPP INTEGRATION***/
    
    
} 




function modelimage()
{
	
	$this->load->view('salestool/modelimage');
	
}

function update_specsimage()
{
	
	$mid=$this->uri->segment(3);
	
	$model=$this->input->post('existsubtypeid');
	if(count($model)>0)
	{
		
	for($i=0;$i<count($model);$i++)
	{
		$mod=$model[$i];
		
		if($_FILES["specsimage"]['name'][$i]<>'')
		{
			$resty=$this->db->select('id,specsfile')->from('modelspecificationfile')->where('mid',$mid)->where('modelid',$mod)->get();
			if($resty->num_rows()==0)
			{
			$name=$_FILES["specsimage"]['name'][$i];
			if($name<>'')
			{
			$tmp_name=explode('.',$name);
			$extn=end($tmp_name);
			$newname=time().$i.'.'.$extn;
			$uploadFilePath = upload_url.'modelspecs/'.$newname;
			move_uploaded_file($_FILES['specsimage']['tmp_name'][$i], $uploadFilePath);
			}else{
				$newname='';
			}
			$data=array('mid'=>$mid,'modelid'=>$mod,'specsfile'=>$newname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('modelspecificationfile',$data);
			}else{
				
				foreach($resty->result() as $resty1);
				$oldfile=$resty1->specsfile;
				
				$name=$_FILES["specsimage"]['name'][$i];
					if($name<>'')
					{
					$tmp_name=explode('.',$name);
					$extn=end($tmp_name);
					$newname=time().$i.'.'.$extn;
					$uploadFilePath = upload_url.'modelspecs/'.$newname;
					move_uploaded_file($_FILES['specsimage']['tmp_name'][$i], $uploadFilePath);
					unlink(upload_url.'modelspecs/'.$oldfile);
					}else{
					$newname=$oldfile;
					}
					
					$data1=array('specsfile'=>$newname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					
					$this->db->where('mid',$mid);
					$this->db->where('modelid',$mod);
					$this->db->update('modelspecificationfile',$data1);

				
			}

		}
		
		
	}
	
	$this->session->set_flashdata('message','Record Updated');
	redirect(page_url.'Salestool/modelimage/'.$mid);
		
		
		
		
		
	}
	
	
	
}

function getnameforso($admin)
{
    $asd=array();
    $restsys=$this->db->select('first_name,last_name,email,contact_number')->from('system_users')->where('user_id',$admin)->get();
    if($restsys->num_rows()>0)
    {
        foreach($restsys->result() as $restsys1);
        
        $asd[]=$restsys1->first_name." ".$restsys1->last_name;
        $asd[]=$restsys1->email;
        $asd[]=$restsys1->contact_number;
        
    }
    
    return $asd;
    
    
}


function sendmachinepdf($machine,$model,$mob)
{
  /** GET PDF **/
    $modat=$this->db->select('attachment')->from('modelwiseattachment
')->where('modelid',$model)->where('mid',$machine)->get();
if($modat->num_rows()>0)
{
    foreach($modat->result() as $modat1);
    $filename=$modat1->attachment;
    

    /***WHATSAPP INTEGRATION***/
    
    $contact="91".$mob;

$overallpath=page_url."upload/machineattachment/".$filename;

$data = [
    'phone' => $contact, // Receivers phone
    'body' => $overallpath,
	'filename'=>$filename,
	'caption'=>''
	// Message
];

	
$json = json_encode($data); // Encode data to JSON
// URL for request POST /message
$url = 'https://api.chat-api.com/instance88514/sendFile?token=lwpwzff7ubbp6dc6';
// Make a POST request
/**$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);**/
// Send a request
//$result = file_get_contents($url, false, $options);

/** CURL **/

// API URL
$url = $url;

// Create a new cURL resource
$ch = curl_init($url);

// Setup request to send json via POST
$payload = $json;

// Attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

// Return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the POST request
$result = curl_exec($ch);

// Close cURL resource
curl_close($ch);

/** END **/
echo $result;

}else
{
    
    echo "PDF NOT AVAILABLE";
}
/***WHATSAPP INTEGRATION***/
    
    
} 


function sendmachinevideo($machine,$model,$mob)
{
    	$attach=$this->db->select('video,attachment')->from('modelwiseattachment')->where('modelid',$model)->where('mid',$machine)->get();
					if($attach->num_rows()>0)
					{
						foreach($attach->result() as $attach1);
						$video=$attach1->video;
					}else{
						
						$video='';
						
					}
    
    $smsmessage=$video;

$data = [
    'phone' => "91".$mob, // Receivers phone
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




function defaultcompany()
{
	
	$this->load->view('salestool/defaultcompany');
	
}

function defaultcompanylist()
{
	
		$scheduler_data = array();


		$restyui=$this->db->select('a.*')->from('salestooldefaultcompany a')->order_by('a.id','DESC')->get();
		if($restyui->num_rows()>0)
		{
		$i=1;
		//echo "<pre>"; print_r($restyui->result());exit;

		foreach($restyui->result() as $instruments)
		{	
		$htm='';
		$htm1='';

		$indussubty=$this->db->select('industry,id')->from('salestoolindustry')->where('id',$instruments->industry)->get();
		if($indussubty->num_rows()>0)
		{
		foreach($indussubty->result() as $indussubty1)
		{
		$industry=strtoupper($indussubty1->industry)."<br/>";
		}

		}else{

		$industry="";

		}


	

		$edit='<a href="'.page_url.'Salestool/edit_defaultcompany/'.$instruments->id.'"><span class="btn btn-sm btn-success">Edit</span></a>';
		//echo $script;exit;
		$scheduler_data[] = array('sr_no'=>$i,
		'type'=>$instruments->companyname,
		'subtype'=>$industry,
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

function adddefaultcompany()
{
	
	$data=array('companyname'=>$this->input->post('unitname'),'industry'=>$this->input->post('subtype'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
	$this->db->insert('salestooldefaultcompany',$data);
	
    $this->session->set_flashdata('message','Added');
	redirect(page_url.'Salestool/defaultcompany');	
	
}


function edit_defaultcompany()
{
	$this->load->view('salestool/edit_defaultcompany');
	
}

function update_defaultcompany()
{
	$id=$this->uri->segment(3);
	
	$data=array('companyname'=>$this->input->post('unitname'),'industry'=>$this->input->post('subtype'));
	$this->db->where('id',$id);
	$this->db->update('salestooldefaultcompany',$data);
	
	  $this->session->set_flashdata('message','Updated');
	redirect(page_url.'Salestool/defaultcompany');
	
	
}
}