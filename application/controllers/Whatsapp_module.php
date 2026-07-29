<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_module extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$this->load->model('Salescrm_model','salescrm');
		$this->load->model('Dashboard_model','dashboardmodel');

}

function sendnotificationforapproval(){
	$user_id=$_SESSION['logged_in']['user_id'];	
	$id = $this->uri->segment(4);
	//$leadid = $this->uri->segment(4);
	$remarks = "Dear Sir, kindly check and approve to proceed further.";
	$stage = 36;
	$remark_title = 'Quotation Sent for Approval';
	$currentDate = new DateTime();
	$currentDate->modify('+2 days');
	$futureDate = $currentDate->format('Y-m-d');
	$data = array('lead_id'=>$id,
	'lead_status'=>$stage,
	'next_follow_date'=>$futureDate,
	'remarks'=>$remarks,
	'remark_title'=>$remark_title,
	'added_on'=>date('Y-m-d H:i:s'),
	'added_by'=>$user_id);

$getopprtunityuniquecode = $this->salescrm->getopportunitygeneraterefno($this->uri->segment(3));

$version = $this->salescrm->getopportunityversionfno($this->uri->segment(3));
//echo $version; exit;
$refrencenumber = str_replace('/', '_', $getopprtunityuniquecode);


 $filelocation = softwarepath.'shubhamquotation/';
 $fileNL=$filelocation.'Quotation_'.$refrencenumber.'_V'.$version.'.pdf';

	$this->db->insert('progress_remarks',$data);
	//$approvelink = "https://pms.shubhampack.in/index.php/User/markasquotationapprove/".$id;
	$rjctlink = "https://pms.shubhampack.in/index.php/User/rejectquotation/".$id;
	
$revisionreason = "";
$q = $this->db->select('version')->from('quotation_customer_data')->where('id',$this->uri->segment(3))->get();
if($q->num_rows()>0){
	foreach($q->result() as $versiondata);
	if($versiondata->version>1){
		$q = $this->db->select('reason')->from('quotation_change_reason')->where('lead_id',$this->uri->segment(4))->limit(1)->order_by('id','desc')->get();
		if($q->num_rows()>0){
			foreach($q->result() as $reson);
			$revisionreason = $reson->reason;
		}else{
			$revisionreason = "";
		}
	}
}

if($revisionreason<>''){
	$revisonremarks = "Revision Reason - *".$revisionreason."*";
}else{
	$revisonremarks = '';
}

	$q = $this->db->select('b.company_name, c.title, c.first_name, c.last_name')->from('leads a')->join('customer_detail b','a.company_name=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.id',$id)->get();
	foreach ($q->result() as $row);
	$leadownername = ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)); 
	$companyname = ucwords(strtolower($row->company_name));

$message="Dear Sir,

🔔 A new quotation for ".$companyname." has been created by ".$leadownername." and sent to you for approval. Kindly check in your PMS panel and approve so that we can proceed further.
Here is the link for Approval/Rejection - $rjctlink
$revisonremarks
Thank you.
Regards,
Shubham Pack 📦

";

$usercontact = '9818505161, 918130192020';
//$usercontact = '8447031736';
/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$usercontact,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'filePathUrl' => $fileNL,
					'message'=>strip_tags($message));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);

					/* end */


// ================= PUSH NOTIFICATION =================

$notifyData = [
'user_id' => 139, // or admin id
'title' => 'Quote Approval Required',
'message' => "Quotation for $companyname by $leadownername needs approval.",
'type' => 'quote_approval',
'reference_id' => $id
];

$this->db->insert('app_notifications', $notifyData);


$push_url = page_url.'mobile/Api/send_push_notification';
// Prepare payload
$pushData = [
    "title" => "📄 Quote Approval Required",
    "message" => "Quotation for $companyname by $leadownername needs approval.",
    "quote_id" => $id
];
// Convert to JSON
$push_json = json_encode($pushData);
// CURL call
$ch = curl_init($push_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $push_json);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // prevent hanging
$push_response = curl_exec($ch);
//echo $push_response; exit;
if (curl_errno($ch)) {
    log_message('error', 'Push Error: ' . curl_error($ch));
} else {
    log_message('error', 'Push Response: ' . $push_response);
}
curl_close($ch);

// ================= END PUSH =================

$this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Quotation successfully sent for approval. </div>', 'refresh');
 redirect(page_url."Leads/lead_stages/39");

}
}