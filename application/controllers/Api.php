<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
	}
	

	
	
	function send()
	{
	
		$post = [
        'source' => 'https://www.prestogroup.com/',
        'department' => 'SALES',
        'name'   => 'SAURABH DUBEY',
        'email'   => 'SDSRBH5@GMAIL.COM',
        'phone'   => '8978767656',
        'company'   => 'YMM',
        'city'   => 'DELHI',
        'country'   => 'INDIA',
        'pincode'   =>'121003',
        'requirement'   => 'PRODUCT ',
        'product'   => 'PRODUCT1,PROUCT2',
];

$ch = curl_init(page_url.'Api/indexforapi');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

$response = curl_exec($ch);

curl_close($ch);




	}
	
	
	
function indexforapi()
{
		
		if(isset($_REQUEST))
		{

		$data=array('source'=>$_REQUEST['source'],'department'=>$_REQUEST['department'],'name'=>$_REQUEST['name'],'email'=>$_REQUEST['email'],'phone'=>$_REQUEST['phone'],'company'=>$_REQUEST['company'],'city'=>$_REQUEST['city'],'country'=>$_REQUEST['country'],'pincode'=>$_REQUEST['pincode'],'requirement'=>$_REQUEST['requirement'],'product'=>$_REQUEST['product'],'addedOn'=>date('Y-m-d H:i:s'));
		
		    $resty=$this->db->insert('websitequeries',$data);
		    $lid=$this->db->insert_id();
		    if($lid!=0 || $lid!='')
		    {
		        echo true;
		    }else
		    {
		        echo false;
		    }
            
		    
		
		}else
		{
		    echo false;
		}
		
		
		
}

function gamavis_form_data(){ 
        $json_params = file_get_contents("php://input");
	       //echo $json_params; exit;
	       $formdata=json_decode($json_params,true);
	     
	       $data = array('form_id'=>$formdata['form_id'],
		'responseid'=>$formdata['responseid'],
	'formdata'=>$formdata['formdata'],
	'added_by'=>$formdata['added_by'],
	'planned_date'=>$formdata['planned_date'],
	'completely_done'=>$formdata['completely_done'],			  
	'added_on'=>$formdata['added_on'],
	'business_location'=>'9');
	
	$this->db->insert('dynamic_form_data',$data);
	$last_id = $this->db->insert_id();
	$formid = $formdata['form_id'];
			
			$markasdone = "<a href='".page_url."Form/task_marked_as_done/".$last_id."/".$formid."'><span class='btn btn-success btn-xs'>MARK AS DONE</span></a>";
	$data = array('record_id'=>$last_id,
	'mark_as_done'=>$markasdone);
	$res = $this->db->insert('dynamic_form_data_mark_done',$data);
	
	
            if($res){
                echo "1";
            }
    }	


function leave_applicationdata(){

	$url="http://crm.gamavis.com/Mitr_api/leave_application_report/";
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
$data['leavedata'] = json_decode($response,true);
//echo "<pre>"; print_r($data); exit;
$err = curl_error($ch);

curl_close($ch);
		$this->load->view('hr/gamavis_leaveapplication',$data);
		
    }

	
}