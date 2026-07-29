<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sampletest  extends CI_Controller { 

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
			$this->load->model('Store_model','storemodel');
			$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
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
		
	public function index(){

$this->load->view('specificsampletest/sampletest');
}


public function samplerequest()
{
	
	$resty=$this->db->select('id')->from('specificsampletestrequest')->get();
	$al=$resty->num_rows();
	$no=$al+1;
	$num_padded = sprintf("%03d", $no);
	$code='PRESSAMP'.$num_padded;
	
	$company=$this->input->post('company');
	$cperson=$this->input->post('cperson');
	$email=$this->input->post('email');
	$mobile=$this->input->post('mobile');
	$add=$this->input->post('address');
	$instrument=$this->input->post('instruments');
	$prd=$this->input->post('productname');
	$dis=$this->input->post('disclaimer');
	$labcondition=$this->input->post('labcondition');
	$ins=$this->input->post('cins');

	$data=array('sampletestid'=>$code,'companyname'=>$company,'personname'=>$cperson,'email'=>$email,'mobile'=>$mobile,'address'=>$add,'instrument'=>$instrument,'productname'=>$prd,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'disclaimer'=>$dis,'labcondition'=>$labcondition,'instruction'=>$ins);

	$this->db->insert('specificsampletestrequest',$data);
	$lid=$this->db->insert_id();
	
	$sample=$this->input->post('sample');
	if(count($sample)>0)
	{
	for($i=0;$i<count($sample);$i++)
	{
		$data1=array('samplereqid'=>$lid,'sample'=>$sample[$i]);
		$this->db->insert('specificsampletobetested',$data1);
		
	}

	
	}
	
	
	$this->session->set_flashdata('message','Request Raised');
	redirect(page_url.'Sampletest');
	
	
	
}

function sampletestrequests()
{
	
	$this->load->view('specificsampletest/sampletestrequest');
}

function sampleallrequests()
{
	$scheduler_data=array();
	$userid = $this->uri->segment(3);
	$this->db->select('a.*')->from('specificsampletestrequest a');
	if($userid){
	    $this->db->where('a.addedBy',$userid);
	}
	$restyui=$this->db->order_by('a.status','ASC')->order_by('a.addedOn','DESC')->get();
		if($restyui->num_rows()>0)
		{
			
	$i=1;
	foreach($restyui->result() as $instruments)
		{
			$items='';	
				
	$restyui12=$this->db->select('*')->from('specificsampletobetested')->where('samplereqid',$instruments->id)->order_by('sample','ASC')->get();
		if($restyui12->num_rows()>0)
		{
			foreach($restyui12->result() as $restyui121)
			{
				$items.=strtoupper($restyui121->sample).'<br/>';
			}
	
	
		}
		if($instruments->status==0)
		{
			$sta="<span class='btn btn-warning btn-xs'>PENDING</span>";
			$rep="<a href='".page_url."Sampletest/addresults/".$instruments->id."'><span class='btn btn-xs'>CREATE</span></a>";
		$reset='';
		}else
		{
			$sta="<span class='btn btn-warning'>COMPLETED</span>";
			$rep="<a href='".page_url."Sampletest/generatesampletestingreport/".$instruments->id."'><span class='btn btn-xs'>VIEW REPORT</span></a>";
			
			$reset="<a href='javascript:;' onclick='deleteresult(".$instruments->id.");'><span class='btn btn-xs'>RESET RESULT</span></a>";
		}
		
	$sales=$this->storemodel->getudata($instruments->sales);
	
	$scheduler_data[] = array('sr_no'=>$i,
	'raisedon'=>date('d-m-Y g:i A',strtotime($instruments->addedOn)),
			'sampleid'=>$instruments->sampletestid,
			'companyname'=>$instruments->companyname,
			'items'=>$items,
			'status'=>$sta,
			'report'=>$rep,
			'reset'=>$reset);
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


function sampleallrequestsforsales()
{
	$scheduler_data=array();
	$userid = $this->uri->segment(3);
	$this->db->select('a.*')->from('specificsampletestrequest a');
	$this->db->where('a.sales',$userid);

	$restyui=$this->db->order_by('a.status','ASC')->order_by('a.addedOn','DESC')->get();
		if($restyui->num_rows()>0)
		{
			
	$i=1;
	foreach($restyui->result() as $instruments)
		{
			$items='';	
				
	$restyui12=$this->db->select('*')->from('specificsampletobetested')->where('samplereqid',$instruments->id)->order_by('sample','ASC')->get();
		if($restyui12->num_rows()>0)
		{
			foreach($restyui12->result() as $restyui121)
			{
				$items.=strtoupper($restyui121->sample).'<br/>';
			}
	
	
		}
		if($instruments->status==0)
		{
			$sta="<span class='btn btn-warning btn-xs'>PENDING</span>";
			$rep=$sta;
		}else
		{
			$sta="<span class='btn btn-warning'>COMPLETED</span>";
			$rep="<a href='".page_url."Sampletesting/generatesampletestingreport/".$instruments->id."'><span class='btn btn-xs'>VIEW REPORT</span></a>";
		}
		
	$sales=$this->storemodel->getudata($instruments->sales);
	
	$scheduler_data[] = array('sr_no'=>$i,
	'raisedon'=>date('d-m-Y g:i A',strtotime($instruments->addedOn)),
		'salesperson'=>$sales,
			'sampleid'=>$instruments->sampletestid,
			'companyname'=>$instruments->companyname,
			'items'=>$items,
			'status'=>$sta,
			'report'=>$rep);
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

function addresults()
{
	
	$this->load->view('specificsampletest/sampletestresults');
	
}

function getmachineunits()
{
	$ins=$this->input->post('instrument');
	if($ins<>'')
	{
		echo "<option value=''>Select Unit</option>";
	$restyu=$this->db->select('a.id,a.shortname')->from('units a')->join('instrument_unit b','a.id=b.unitid')->where('b.mid',$ins)->get();
	if($restyu->num_rows()>0)
	{
		
		foreach($restyu->result() as $restyu1)
		{
			
			echo "<option value='".$restyu1->id."'>".strtoupper($restyu1->shortname)."</option>";
			
			
		}
		
		
	}
	}
	
}


function sampletestresults()
{
	$data=array('samplereqid'=>$this->uri->segment(3),
	'lotno'=>$this->input->post('lno'),
	'batchno'=>$this->input->post('bno'),
	'samplelength'=>$this->input->post('length'),
	'samplewidth'=>$this->input->post('width'),
	'thickness'=>$this->input->post('thickness'),
	'shapegsm'=>$this->input->post('shape'),
	'testspeed'=>$this->input->post('speed'),
	'testvideo'=>$this->input->post('vlink'),
	'remarks'=>$this->input->post('remarks'),
	'addedOn'=>date('Y-m-d H:i:s'),
	'addedBY'=>$_SESSION['logged_in']['user_id']);
	
	$this->db->insert('specificsampletestresults',$data);
	$lid=$this->db->insert_id();
	if($lid<>'' || $lid<>'0')
	{
		
		$samplid=$this->input->post('sampleid');
		if(count($samplid)>0)
		{
			for($i=0;$i<count($samplid);$i++)
			{
				$sampleid=$samplid[$i];
				
				$sampletimes=$this->input->post('sampletest'.$sampleid);
				
				$sampletimesunits=$this->input->post('sampletestunit'.$sampleid);
				
				
				
				if(count($sampletimes)>0)
				{
					
					for($j=0;$j<count($sampletimes);$j++)
					{
						$sampletestdata=$sampletimes[$j];
						$sampletunit=$sampletimesunits[$j];
						
					$data1=array('samplereqid'=>$this->uri->segment(3),
					'sampleresultid'=>$lid,
					'sampleid'=>$sampleid,
					'resultdata'=>$sampletestdata,
					'sampleunit'=>$sampletunit,
					'addedOn'=>date('Y-m-d H:i:s'),
					'addedBy'=>$_SESSION['logged_in']['user_id']);
					
					$this->db->insert('specificsampletobetestedresults',$data1);
					
					
					
					}
					
					
					
				}
			}
			
		
			if(count($_FILES["file"]["name"])>0)
	{
		for($i=0;$i<count($_FILES["file"]["name"]);$i++)
		{
	
		$name=$_FILES["file"]["name"][$i];
		if($name<>'')
		{
		
		$tmpname=explode('.',$name);
		$extn=end($tmpname);
		$newname=time().$i.'.'.$extn;
		$uploadFilePath = $_SERVER['DOCUMENT_ROOT'].'/upload/sampletesting/'.basename($newname);
		move_uploaded_file($_FILES['file']['tmp_name'][$i], $uploadFilePath);
		}else{
		
			$newname='';
		}
		
		if($i==0)
		{
			$field='testimage';
		}else{
			$field='testimage1';
			
		}
			$imgfdata=array($field=>$newname);
			$this->db->where('id',$lid);
			$this->db->update('specificsampletestresults',$imgfdata);
		
		
		}
	
	}
	
	
	
		/** UPDATE STATUS **/
			$fdata=array('status'=>'1','report'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('specificsampletestrequest',$fdata);
			/** END **/
			
		$this->session->set_flashdata('message','Record Added');
		redirect(page_url.'Sampletest/generatesampletestingreport/'.$this->uri->segment(3).'/1');
		}
	}else{
		
		
		$this->session->set_flashdata('message','Record not inserted');
		redirect(page_url.'Sampletest/addresults/'.$this->uri->segment(3));
		
	}
	
	
}


function generatesampletestingreport()
{
	
	$this->load->view('specificsampletest/sampletest/report');
	
	
	
}

function sampletestscripts()
{
	$this->load->view('specificsampletest/setscript');
}

function sampletestingscripts()
{
		$scheduler_data=array();
		
		$restyui12=$this->db->select('*')->from('specificsampletestscript')->get();
		if($restyui12->num_rows()>0)
		{
			$i=1;
			foreach($restyui12->result() as $restyui121)
			{
				if($restyui121->type==1)
				{
					$type="DISCALIMER";
				}else{
					$type="LAB CONDITION";
				}
				$edit="<a href='".page_url."Sampletest/editscript/".$restyui121->id."'><span class='btn btn-xs btn-warning'>EDIT</span></a>";
		$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$type,
			'script'=>$restyui121->script,
			'action'=>$edit);
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


function editscript()
{

	
	$this->load->view('specificsampletest/edit_script');
	
}



function updatesampletestscripts()
{
	
	
	$data=array('script'=>$this->input->post('script'));
	$this->db->where('id',$this->uri->segment('3'));
	$this->db->update('specificsampletestscript',$data);
	
	$this->session->set_flashdata('message','Record Updated');
	redirect(page_url.'Sampletest/sampletestscripts');
	
}


function updatesamplescript()
{
	
	$type=$this->input->post('type');
	if($type<>'1' && $type<>'2')
	{
	
	$data=array('content'=>$this->input->post('script'));
	
	}else{
		
		

		$name=$_FILES["script"]["name"];
		if($name<>'')
		{
		
		$tmpname=explode('.',$name);
		$extn=end($tmpname);
		$newname=time().$i.'.'.$extn;
		$uploadFilePath = $_SERVER['DOCUMENT_ROOT'].'/upload/sampletesting/'.basename($newname);
		move_uploaded_file($_FILES['script']['tmp_name'], $uploadFilePath);
		}else{
		
			$newname='';
		}
		
		
		$data=array('content'=>$newname);
	
		
	}
	
	$this->db->where('id',$this->uri->segment('3'));
	$this->db->update('specificsampletestotherdata',$data);
	
	$this->session->set_flashdata('message','Record Updated');
	redirect(page_url.'Sampletest/setotherdetails');
	
}

function sendonwhatsapp()
{
$uid=$_POST['userids'];
$samplefilename=$_POST['samplefilename'];
$sampleid=$_POST['sampleid'];
$restyu=$this->db->select('contact_number,first_name,last_name')->from('system_users')->where('user_id',$uid)->get();
foreach($restyu->result() as $rest);

$restyu1=$this->db->select('companyname,email,mobile,personname')->from('specificsampletestrequest')->where('id',$sampleid)->get();
foreach($restyu1->result() as $restyu11);
$compname=$restyu11->companyname;
$compemail=$restyu11->email;
$compmobile=$restyu11->mobile;
$personname=$restyu11->personname;

$contact="91".$rest->contact_number;
$image = $_POST['image'];
$location = $_SERVER['DOCUMENT_ROOT']."/image_bank/sampletestreport/";
$image_parts = explode(";base64,", $image);
$image_base64 = base64_decode($image_parts[1]);
$filename = $samplefilename.'.jpg';
$file = $location . $filename;
file_put_contents($file, $image_base64);
$overallpath=page_url."image_bank/sampletestreport/".$filename;

/** SEND EMAIL TO SALESOFFICES **/

$Message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.prestomitr.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>SAMPLE TESTING REPORT GENERATED</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear '.$rest->first_name.' '.$rest->last_name.',<br/> Please find Sample Testing Report for Customer '.$compname.'</td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Prestogroup Team
						</td>
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
			
		    	$subjectname = "SAMPLE TESTING REPORT GENERATED FOR ".$compname;
					$this->email->set_mailtype("html");
					$this->email->to('sdsrbh5@gmail.com');
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message);
					$this->email->attach($file);
    				$result11=$this->email->send(); 
/* END **/


/** SEND EMAIL TO Client **/

$Message1 = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
				   <tr>
					<td style="padding: 20px; border: 1px solid #c3c3c3; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.prestomitr.com/assets/images/logo-1.png" width="200px;" alt="Prestogroup" /></td>
					  </tr>
					   <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br><strong>SAMPLE TESTING REPORT GENERATED</strong><br></td>
					  </tr> 
					  <tr>
						<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" /><br>Dear '.$personname.',<br/> Please find Your Sample Testing Report attached </td>
					  </tr> 
					  
					   <tr>
						<td>&nbsp;</td>
					  </tr>
					 
					<tr>
						<td style="font-family: "Montserrat", sans-serif; font-size: 18px; color: #666666;;">Best Regards, <br>
						Prestogroup Team
						</td>
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
			
		    	$subjectname = "SAMPLE TESTING REPORT | PRESTO STANTEST PVT. LTD.";
					$this->email->set_mailtype("html");
					$this->email->to($compemail);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($Message1);
					$this->email->attach($file);
    				$result11=$this->email->send(); 
/* END **/

/***WHATSAPP INTEGRATION***/
$contact="918447031736";
$data = [
    'phone' => $contact, 
    'body' => $overallpath,
	'filename'=>$filename,
	'caption'=>"Please find your Sample Testing Report\nRegards\nTeam Presto"
];  

	
$json = json_encode($data); 
//echo $json;exit;
$url = 'https://api.chat-api.com/instance88514/sendFile?token=lwpwzff7ubbp6dc6';
$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);
$result = file_get_contents($url, false, $options);

echo $result;exit;

/***WHATSAPP INTEGRATION***/
	
	
}

function setotherdetails()
{	
	$this->load->view('specificsampletest/setotherdetails');
}



function sampletestingotherscripts()
{
		$scheduler_data=array();
		
		$restyui12=$this->db->select('*')->from('specificsampletestotherdata')->get();
		if($restyui12->num_rows()>0)
		{
			$i=1;
			foreach($restyui12->result() as $restyui121)
			{
				if($restyui121->type==1)
				{
					$type="LOGO";
					$content="<img src='".page_url."upload/sampletesting/".$restyui121->content."' style='height:30px'>";
				}else if($restyui121->type==2){
					$type="SIGNATURE";
					$content="<img src='".page_url."upload/sampletesting/".$restyui121->content."' style='height:30px'>";
				}else if($restyui121->type==3)
				{
					$type="EMAIL";
					$content=$restyui121->content;
					
				}else if($restyui121->type==4)
				{
					$type="WEBSITE";
					$content=$restyui121->content;
				}else{
					
					$type="CHECKED BY";
					$content=$restyui121->content;
				}
				
				$edit="<a href='".page_url."Sampletest/editotherscript/".$restyui121->id."'><span class='btn btn-xs btn-warning'>EDIT</span></a>";
		$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$type,
			'script'=>$content,
			'action'=>$edit);
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

function editotherscript()
{

	
	$this->load->view('specificsampletest/edit_otherscript');
	
}	

function resetresults()
{
    $id=$this->uri->segment(3);
   
   $this->db->where('samplereqid',$id);
   $this->db->delete('specificsampletestresults');
   
    $this->db->where('samplereqid',$id);
   $this->db->delete('specificsampletobetestedresults');
   
   
    /** UPDATE STATUS **/
    $fdata=array('status'=>'0','report'=>'0');
    $this->db->where('id',$id);
    $this->db->update('specificsampletestrequest',$fdata);
    /** END **/
   
    $this->session->set_flashdata('message','Reset Succesfull');
    redirect(page_url.'Sampletest/sampletestrequests');
    
    
}

	
}