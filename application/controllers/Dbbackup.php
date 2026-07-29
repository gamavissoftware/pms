<?php
date_default_timezone_set("Asia/Calcutta"); 
defined('whatsappuser') OR define('whatsappuser','shubhampacktaskmanagement');
defined('whatsapppass') OR define('whatsapppass','$shuBhampckTask@2024');
ini_set('memory_limit','256M');
defined('BASEPATH') OR exit('No direct script access allowed');

class Dbbackup extends CI_Controller {
	
	public function __construct()
	{

parent::__construct();
	
$this->load->helper('file');
$this->load->helper('download');
$this->load->library('zip');
$this->load->model('Dashboard_model','reportingdata');
$this->load->model('Fms_model','Fms_model');
$this->load->model('Task_model','task');
$this->load->model('Store_model');
$this->load->model('Notification_model');
$this->load->model('Salescrm_model','salescrm');
	

$config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
		$this->email->set_mailtype("html");


	}
		
	function index()	
	{

	    
		$this->getdbbackup();
	}
	
function getdbbackup(){
    // Load the DB utility class
	$fileName='db_backup_'.date('d-M-Y').'.zip';
    $this->load->dbutil();
    // Backup your entire database and assign it to a variable
    $backup =$this->dbutil->backup();
    // Load the file helper and write the file to your server
    $this->load->helper('file');
    write_file(FCPATH.'/dailydbbackup/'.$fileName, $backup);
    // Load the download helper and send the file to your desktop
    //$this->load->helper('download');
    //force_download($fileName, $backup);
}


function dbarchivelist()
{
	$this->load->view('archives/archivelist');	
}

function getlist()
{
$directory = FCPATH.'/dailydbbackup/';
$files = scandir($directory); 
$i=1;	
foreach(array_reverse($files) as $file)
{  
	if($file!='.' && $file!='..')
	{

$scheduler_data[] = array('sr_no'=>$i,
'file'=>$file,
'download'=>'<a href="'.page_url.'/dailydbbackup/'.$file.'" download><span>Download</span></a>');
	
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

public function checklogindetail(){
	$password=$this->input->post('password');
	if($password=='Manglesh@sd5'){
		$segment = "Backudbup";
		redirect(page_url.'Dbbackup/dbarchivelist/'.$segment);
	}else{
		echo "Access denied."; exit;
	}
}	




function updateuserchecklist()
{

		
			$yesterday = date('Y-m-d',strtotime("-1 days"));
			$qry1 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$yesterday)->get();
			if($qry1->num_rows()>0){
			$status = "1";
			}else{
			$status= "0";
			}


			$qry = $this->db->select('a.task_id, a.dateforemail, b.task_id, b.status')->from('compliance_set_date a')->join('compliance_task_report b','a.task_id=b.task_id','left')->where('b.status','1')->where('a.dateforemail<=',$yesterday)->get();
			if($qry->num_rows()>0){


			foreach($qry->result() as $row){

			$data = array('task_id'=>$row->task_id,
			'status'=>$status,
			'task_date'=>$yesterday);
			$this->db->insert('checklist_done_notdone',$data);

			$today = date('Y-m-d');
			$data1 = array('dateforemail'=>$today);
			$this->db->where('task_id',$row->task_id);
			$this->db->update('compliance_set_date',$data1);
			}
			}
			
				


}



	function newdashboardreport(){
	
	//echo "hi";exit;
	$todays_date = date('Y-m-d');
	$data=array('runningtime'=>date('Y-m-d H:i:s'));
	$this->db->insert('dashboardcronstatus',$data);
/** $postData = array(
'authkey' => '266631AuRXQ3UyZ5c839e9b',
'mobiles' => '91918447031736',
'message' => 'Hello',
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
}  **/

		//$delegatedtask = $this->reportingdata->delegated_task();
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
		$rejection_vs_action = $this->reportingdata->rejection_vs_action();
		$daily_work_report = $this->reportingdata->daily_work_report();
		/*Total Order*/
		$userinfo = "0";
		$lcountall=$this->Fms_model->nondispatchedorders($userinfo);
		$data = array('report_count'=>$lcountall);
		$this->db->where('id','26');
		$this->db->update('administrator_dashboard',$data);
		/*Total Order*/
		/*Pending order count*/
		$pendicount=$this->Fms_model->pendingordercount();
		$data = array('report_count'=>$pendicount);
		$this->db->where('id','28');
		$this->db->update('administrator_dashboard',$data);
		/*Pending order count*/
		/*BOM Count*/
		$jobcardbom=$this->Store_model->bompr();
		$data = array('report_count'=>$jobcardbom);
		$this->db->where('id','17');
		$this->db->update('administrator_dashboard',$data);
		/*BOM Count*/
		/*IMS VS PR*/
		$imspr=$this->Store_model->imspr();
		$data = array('report_count'=>$imspr);
		$this->db->where('id','18');
		$this->db->update('administrator_dashboard',$data);
		/*IMS VS PR*/
		/*Indent vs PR*/
		$indvspr=$this->Fms_model->indentvspo();
		$data = array('report_count'=>$indvspr);
		$this->db->where('id','19');
		$this->db->update('administrator_dashboard',$data);
		/*Indent vs PR*/
		/*RGP VS RETURN*/
		 $grp=$this->Store_model->rgpchallannotclosedcount();
		 $data = array('report_count'=>$grp);
		$this->db->where('id','20');
		$this->db->update('administrator_dashboard',$data);
		 /*RGP VS RETURN*/
		 /*Ready order count*/
		$userinfo = "0";
		$readyordercount=$this->Fms_model->readyordercount($userinfo);
		 $data = array('report_count'=>$readyordercount);
		$this->db->where('id','29');
		$this->db->update('administrator_dashboard',$data);
		 /*Ready order count*/
		 /*Unplanned Orders*/
		 $unplannedordercount=$this->Fms_model->unplannedordercount();
		  $data = array('report_count'=>$unplannedordercount);
		$this->db->where('id','30');
		$this->db->update('administrator_dashboard',$data);
		 
		 
		 $pendicount=$this->Fms_model->pendingordercountsales();
		$data = array('report_count'=>$pendicount);
		$this->db->where('id','38');
		$this->db->update('administrator_dashboard',$data);
		
		 /*Unplanned Orders*/
		 
		 /*Dispatch for Tomorrow*/
		$userinfo = "0";
		$dispatchcount=$this->Fms_model->dispatchfortommorowcount($userinfo);
		$q=$this->db->select('a.id')->from('dynamic_form_data a')->where('a.form_id','6')->where('a.work_status','0')->get();
		$count = count($q->result());
		$total = $dispatchcount+$count;
		 $data = array('report_count'=>$total);
		$this->db->where('id','31');
		$this->db->update('administrator_dashboard',$data);
		 /*Dispatch for Tomorrow*/
		 
		 /*Reorder */
		 $reorderm=$this->Fms_model->reordermachine();
		 $data = array('report_count'=>$reorderm);
		$this->db->where('id','32');
		$this->db->update('administrator_dashboard',$data);
		 
		 /*Reorder */
		 /*PO VS PR*/
		$povspr=$this->Fms_model->povsprcount();
		 $data = array('report_count'=>$povspr);
		$this->db->where('id','33');
		$this->db->update('administrator_dashboard',$data);
		/*PO VS PR*/
		/*po vs delivery*/
		$povsdeli=$this->Fms_model->povsdelivery();
		 $data = array('report_count'=>$povsdeli);
		$this->db->where('id','34');
		$this->db->update('administrator_dashboard',$data);
		/*po vs delivery*/
		/*PO vs MRN*/
		$povsmrn=$this->Store_model->pendingmrn();
		 $data = array('report_count'=>$povsmrn);
		$this->db->where('id','35');
		$this->db->update('administrator_dashboard',$data);
		/*PO vs MRN*/
		
		/*Vendor for review*/
		$vendoirapproval=$this->Store_model->vendorforreviewcount();
		$data = array('report_count'=>$vendoirapproval);
		$this->db->where('id','36');
		$this->db->update('administrator_dashboard',$data);
		/*Vendor for review*/
		
		/*Sales Visit*/
		$salesvisitcount=$this->Notification_model->getSalesVisitReportCount();
		$data = array('report_count'=>$salesvisitcount);
		$this->db->where('id','1');
		$this->db->update('sales_reporting_dashboard',$data);
		
		/*Daily Update*/
		// $dailyupdatecount=$this->Notification_model->getDailyUpdateReportCount();
		// $data = array('report_count'=>$dailyupdatecount);
		// $this->db->where('id','2');
		// $this->db->update('sales_reporting_dashboard',$data);
		
		// /*Payment Collection*/
		// $paymentcollectioncount=$this->Notification_model->getPaymentCollectionReportCount();
		// $data = array('report_count'=>$paymentcollectioncount);
		// $this->db->where('id','3');
		// $this->db->update('sales_reporting_dashboard',$data);
		
		// /*Hod Dashboard of all Engineers*/
		// $hoddashboardcount=$this->Notification_model->getHodDashboardReportCount();
		// $data = array('report_count'=>$hoddashboardcount);
		// $this->db->where('id','1');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*Visit History*/
		// $visithistorycount=$this->Notification_model->getVisitHistoryReportCount();
		// $data = array('report_count'=>$visithistorycount);
		// $this->db->where('id','2');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*View Your Dashboard*/
		// $viewdashboardcount=$this->Notification_model->getViewDashboardReportCount();
		// $data = array('report_count'=>$viewdashboardcount);
		// $this->db->where('id','3');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*FOC Dashboard*/
		// $focdashboard=$this->Notification_model->getFocDashboardReportCount();
		// $data = array('report_count'=>$focdashboard);
		// $this->db->where('id','4');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*Payment Follow-up Dashboard*/
		// $paymentfollowupcount=$this->Notification_model->getPaymentFollowupReportCount();
		// $data = array('report_count'=>$paymentfollowupcount);
		// $this->db->where('id','5');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*Technical Support Dashboard*/
		// $technicalsupportcount=$this->Notification_model->getTechnicalSupportReportCount();
		// $data = array('report_count'=>$technicalsupportcount);
		// $this->db->where('id','6');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*QC Service Repair Request*/
		// $qcrepaircount=$this->Notification_model->getQcRepairReportCount();
		// $data = array('report_count'=>$qcrepaircount);
		// $this->db->where('id','7');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*Service Repair Request for Service*/
		// $servicerepaircount=$this->Notification_model->getServiceRepairReportCount();
		// $data = array('report_count'=>$servicerepaircount);
		// $this->db->where('id','8');
		// $this->db->update('service_reporting_dashboard',$data);
		
		// /*Pending Order Report*/
		// $pendingordercount=$this->Notification_model->getPendingOrderReportCount();
		// $data = array('report_count'=>$pendingordercount);
		// $this->db->where('id','1');
		// $this->db->update('production_reporting_dashboard',$data);
		
		
		// /*Sales Pending Order Report*/
		// $salespendingordercount=$this->Notification_model->getSalesPendingOrderReportCount();
		// $data = array('report_count'=>$salespendingordercount);
		// $this->db->where('id','2');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Machines for Packing*/
		// $machinepackingcount=$this->Notification_model->getMachinePackingReportCount();
		// $data = array('report_count'=>$machinepackingcount);
		// $this->db->where('id','3');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Machines for Packing for Service*/
		// $servicemachinepackingcount=$this->Notification_model->getServiceMachinePackingReportCount();
		// $data = array('report_count'=>$servicemachinepackingcount);
		// $this->db->where('id','4');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Ready for Dispatch Complete Report*/
		// $readydispatchcount=$this->Notification_model->getReadyDispatchReportCount();
		// $data = array('report_count'=>$readydispatchcount);
		// $this->db->where('id','5');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Reorder Report*/
		// $reordercount=$this->Notification_model->getReorderReportCount();
		// $data = array('report_count'=>$reordercount);
		// $this->db->where('id','6');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Dispatch for Tomorrow*/
		// $dispatchtomcount=$this->Notification_model->getDispatchTomReportCount();
		// $data = array('report_count'=>$dispatchtomcount);
		// $this->db->where('id','7');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Ready For Billing*/
		// $readybillingcount=$this->Notification_model->getReadyBillingReportCount();
		// $data = array('report_count'=>$readybillingcount);
		// $this->db->where('id','8');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Sales Dispatch for Tomorrow*/
		// $salesdispatchcount=$this->Notification_model->getSalesDispatchReportCount();
		// $data = array('report_count'=>$salesdispatchcount);
		// $this->db->where('id','9');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Service Request*/
		// $servicerequestcount=$this->Notification_model->getServiceRequestReportCount();
		// $data = array('report_count'=>$servicerequestcount);
		// $this->db->where('id','10');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Lot Order List*/
		// $lotordercount=$this->Notification_model->getLotOrderReportCount();
		// $data = array('report_count'=>$lotordercount);
		// $this->db->where('id','11');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*Orders Start to End Report*/
		// $starttoendcount=$this->Notification_model->getStartEndReportCount();
		// $data = array('report_count'=>$starttoendcount);
		// $this->db->where('id','12');
		// $this->db->update('production_reporting_dashboard',$data);
		
		// /*MRN Report*/
		// $mrncount=$this->Notification_model->getMrnReportCount();
		// $data = array('report_count'=>$mrncount);
		// $this->db->where('id','1');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*QC Report*/
		// $qccount=$this->Notification_model->getQcReportCount();
		// $data = array('report_count'=>$qccount);
		// $this->db->where('id','2');
		// $this->db->update('store_reporting_dashboard',$data);
		 
		//  /*STORE RECIEPT*/
		// $storereceiptcount=$this->Notification_model->getStoreReceiptReportCount();
		// $data = array('report_count'=>$storereceiptcount);
		// $this->db->where('id','3');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Todays Issued Items*/
		// $todaysissuedcount=$this->Notification_model->getTodaysIssuedReportCount();
		// $data = array('report_count'=>$todaysissuedcount);
		// $this->db->where('id','5');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Audit Material Issued not updated*/
		// $auditmaterialcount=$this->Notification_model->getAuditMaterialReportCount();
		// $data = array('report_count'=>$auditmaterialcount);
		// $this->db->where('id','6');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Material Issued not updated for store*/
		// $materialissuedcount=$this->Notification_model->getMaterialIssuedReportCount();
		// $data = array('report_count'=>$materialissuedcount);
		// $this->db->where('id','7');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*QC Rejected Items*/
		// $qcrejectedcount=$this->Notification_model->getQcRejectedReportCount();
		// $data = array('report_count'=>$qcrejectedcount);
		// $this->db->where('id','8');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Open Service Issue for Inward*/
		// $issueinwardcount=$this->Notification_model->getIssueInwardReportCount();
		// $data = array('report_count'=>$issueinwardcount);
		// $this->db->where('id','9');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*RGP Challan*/
		// $rgpchallancount=$this->Notification_model->getRgpChallanReportCount();
		// $data = array('report_count'=>$rgpchallancount);
		// $this->db->where('id','10');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Anytime Rejection Store Dashboard*/
		// $anytimestorecount=$this->Notification_model->getAnytimeStoreReportCount();
		// $data = array('report_count'=>$anytimestorecount);
		// $this->db->where('id','11');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Anytime Rejection RGP Dashboard*/
		// $anytimergpcount=$this->Notification_model->getAnytimeRgpReportCount();
		// $data = array('report_count'=>$anytimergpcount);
		// $this->db->where('id','12');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Anytime Rejection Gate Entry Dashboard*/
		// $anytimegatecount=$this->Notification_model->getAnytimeGateReportCount();
		// $data = array('report_count'=>$anytimegatecount);
		// $this->db->where('id','13');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Anytime Rejection QC Dashboard*/
		// $anytimeqccount=$this->Notification_model->getAnytimeQcReportCount();
		// $data = array('report_count'=>$anytimeqccount);
		// $this->db->where('id','14');
		// $this->db->update('store_reporting_dashboard',$data);
		
		// /*Pending Indents*/
		// $pendingindentcount=$this->Notification_model->getPendingIndentReportCount();
		// $data = array('report_count'=>$pendingindentcount);
		// $this->db->where('id','1');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*Pending PR for PO Generation*/
		// $prpocount=$this->Notification_model->getPrPoReportCount();
		// $data = array('report_count'=>$prpocount);
		// $this->db->where('id','2');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*Pending PO for Approval Request*/
		// $pendingpoappcount=$this->Notification_model->getPendingPoApprovalReportCount();
		// $data = array('report_count'=>$pendingpoappcount);
		// $this->db->where('id','3');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*Pending Payments*/
		// $pendingpaymentcount=$this->Notification_model->getPendingPaymentReportCount();
		// $data = array('report_count'=>$pendingpaymentcount);
		// $this->db->where('id','4');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*Pending Emails*/
		// $pendingemailcount=$this->Notification_model->getPendingEmailReportCount();
		// $data = array('report_count'=>$pendingemailcount);
		// $this->db->where('id','5');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*PO Follow-up*/
		// $pofollowupcount=$this->Notification_model->getPoFollowupReportCount();
		// $data = array('report_count'=>$pofollowupcount);
		// $this->db->where('id','6');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*Pending Order Dashboard*/
		// $pendingorderpurcount=$this->Notification_model->getPendingOrderPurReportCount();
		// $data = array('report_count'=>$pendingorderpurcount);
		// $this->db->where('id','7');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*Indent VS PR Report*/
		// $indentprcount=$this->Notification_model->getIndentPrReportCount();
		// $data = array('report_count'=>$indentprcount);
		// $this->db->where('id','8');
		// $this->db->update('purchase_reporting_dashboard',$data);
		
		// /*PR vs PO Report*/
		// $prvspocount=$this->Notification_model->getPrVsPoReportCount();
		// $data = array('report_count'=>$prvspocount);
		// $this->db->where('id','9');
		// $this->db->update('purchase_reporting_dashboard',$data);


	
	
	}

		function todays_followup_reminders() {
			$cur_date = date('Y-m-d');

			$conversion_lead_stage = $this->getConversionLeadStage();
			$getDeadEndLeadStage = $this->getDeadEndLeadStage();
			array_push($getDeadEndLeadStage, $conversion_lead_stage);
			$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

			$q = $this->db->select('user_id, first_name,user_role_id, last_name, email, contact_number')
						  ->from('system_users_view')
						  ->where('user_status',1)
						  ->get();

			if($q->num_rows()>0) {
				foreach($q->result() as $rowss) {
						$smsmessage = "Dear ".ucfirst($rowss->first_name." ".$rowss->last_name).","."\n\nToday you have follow-up with these Customers.\n";
					$sql1=$this->db->query("SELECT a.id,a.added_on,a.added_by,a.lead_status,a.remarks,a.remark_title,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) AND a.next_follow_date='$cur_date' AND c.member_id=$rowss->user_id GROUP BY a.lead_id");

						
					if($sql1->num_rows() > 0) {
						foreach($sql1->result() as $row1) {
							$smsmessage.="------------------\nCustomer Name- *".ucfirst($row1->customer_name)."*\n"."Company Name - *".ucfirst($row1->company_name)."*\nYour Last discussion was "."*".$row1->remarks."*"."\n------------------\n";
							// echo $smsmessage;exit;
							/*Whatsapp Notification*/
						}
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							'receiverMobileNo' => '91'.$rowss->contact_number.",8447031736",
							//'receiverMobileNo' => '8447031736',
							'username' => whatsappuser,
							'password' => whatsapppass,
							'message'=>strip_tags($smsmessage));
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$result = curl_exec($ch);
							//echo $result; exit;
							if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
							}
							curl_close($ch);
						/*Whatsapp Notification*/
					}
				}
			}
		}

		function getConversionLeadStage() {
			$res = '';
			$sql = $this->db->select('lead_id')
							->from('lead_stage')
							->where('conversion_step', 1)
							->get();

			if ($sql->num_rows() > 0) {
				foreach ($sql->result() as $row1);
				$res = $row1->lead_id;
			}

			return $res;
		}

		function getDeadEndLeadStage() {
				$lead_stages = array();
				$sql = $this->db->select('lead_id')
								->from('lead_stage')
								->where('dead_end', 1)
								->get();

				if($sql->num_rows() > 0) {
					foreach($sql->result() as $row) {
						$lead_stages[] = $row->lead_id;
					}
				} else {
					$lead_stages[] = 0;
				}

				return $lead_stages;
		}

		function send_quote_expiring_today()
		{
			$msgbody = "";
			$emailbody = "";
			$todays_date=date('Y-m-d');
				/*-------------------------SEND QUOTATION EXPIRY NOTIFICATION---------------------------------*/

				$sql1=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status',1)->get();
				if($sql1->num_rows()>0)
				{
					foreach($sql1->result() as $row)
					{
						$msgbody= 'Dear '.$row->first_name.' '.$row->last_name.','."\n\n";

		$sql = $this->db->select('b.validity_date, b.customer_name,b.company_name')
						->from('leads b')
						->where('b.validity_date', $todays_date)
						->where('b.added_by',$row->user_id)
						->get();

		if($sql->num_rows() > 0) {
			
			$msgbody .= "Your following Company(s) quotation are expiring today. Please reach out to customer regarding the same\n";

			$i=1;
			foreach ($sql->result() as $rows) {
							
					$msgbody .= '*'.$i.') '.$rows->company_name."*\n";
							
				$i++;
			}
				
				$msgbody .="Thank You";
					 //echo $msgbody;exit;
							/***WHATSAPP INTEGRATION***/
					  $ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							//'receiverMobileNo' => '918447031736',
							 'receiverMobileNo' => '91'.$official_no,
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

				}
			}

					
					// $config = array();  
					// $config['protocol'] = 'smtp';  
					// $config['smtp_host'] = 'mail.sunderindoil.com';  
					// $config['smtp_user'] = 'info@sunderindoil.com';  
					// $config['smtp_pass'] = 'Manglesh@sd5';   
					// $config['smtp_port'] = 587;   
					// $config['newline'] = "\r\n";
			  //       $this->email->initialize($config);  
			  //       $this->load->library('email', $config);

					// $subjectname = "Quotations Expiring Today";
					// $this->email->set_mailtype("html");
					// // $this->email->to('webdevelopment1@gamavis.com');
					// $this->email->to('sdsrbh5@gmail.com');
					// $this->email->from('info@sunderindoil.com');
    	// 			$this->email->subject($subjectname);
    	// 			$this->email->message($emailbody);
    	// 			$result11=$this->email->send();
    				// echo $this->email->print_debugger(); exit;
				
			
		}

		}
		/*------------------------END-----------------------------------------------*/

		function send_prior_notification_for_quote_expiry()
		{
			$msgbody = "";
			$emailbody = "";
			$date_ahead=date('Y-m-d', strtotime("+3 day"));
			// $date_ahead = ;
			// echo $date_ahead;exit;
				/*-------------------------SEND QUOTATION EXPIRY NOTIFICATION---------------------------------*/

		$sql = $this->db->select('a.validity_date, b.customer_name')
						->from('customer_quotation a')
						->join('customer_detail b', 'b.id=a.customer_id')
						->where('a.validity_date', $date_ahead)
						->get();

		if($sql->num_rows() > 0) {
			$msgbody = 'Dear Sir,'."\n\n".
			$msgbody .= 'This is a reminder message that the validity of ';
			$emailbody = 'Dear Sir,'."<br><br>".
			$emailbody .= 'This is a reminder message that the validity of ';
			foreach ($sql->result() as $rows) {
				if($sql->num_rows() > 1) {
					$a = ", ";
				} else {
					$a = "";
				}
							$msgbody .= $rows->customer_name.$a;
							$emailbody .= $rows->customer_name.$a;

				}
				$msgbody .= ' quotation is ending on '.date('d-m-Y', strtotime($date_ahead)).'.'."\n\n".
							'Team HPCL';
				$emailbody .= ' quotation is ending on '.date('d-m-Y', strtotime($date_ahead)).'.'."<br><br>".
							'Team HPCL';
					// echo $msgbody;exit;
							/***WHATSAPP INTEGRATION***/
					  $ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							'receiverMobileNo' => '918447031736',
							// 'receiverMobileNo' => '91'.$official_no,
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

					$config = array();  
					$config['protocol'] = 'smtp';  
					$config['smtp_host'] = 'mail.sunderindoil.com';  
					$config['smtp_user'] = 'info@sunderindoil.com';  
					$config['smtp_pass'] = 'Manglesh@sd5';   
					$config['smtp_port'] = 587;   
					$config['newline'] = "\r\n";
			        $this->email->initialize($config);  
			        $this->load->library('email', $config);

					$subjectname = "Quotations Expiring In 3 Days";
					$this->email->set_mailtype("html");
					// $this->email->to('webdevelopment1@gamavis.com');
					$this->email->to('sdsrbh5@gmail.com');
					$this->email->from('info@sunderindoil.com');
    				$this->email->subject($subjectname);
    				$this->email->message($emailbody);
    				$result11=$this->email->send();
				
			
		}

		/*------------------------END-----------------------------------------------*/
		}
	
	
	public function pendingdelegatedtaskreminder(){
	
	
	$qq  = $this->db->select(	'user_id, first_name, last_name, contact_number')->from('system_users_view')->where('user_status',1)->get();
	foreach($qq->result() as $row){
		$smsmessage="";
		$personname = $row->first_name." ".$row->last_name;
		
	$q = $this->db->select('a.urgency,a.task, a.targetdate, c.first_name, c.last_name,a.id')->from('delegation_task a')->join('system_users b','a.delegate_to=b.user_id','left')->join('system_users c','a.yourname=c.user_id','left')->where('a.delegate_to',$row->user_id)->where('a.task_status',0)->get();
	if($q->num_rows()>0){
		$i=0;
	foreach($q->result() as $rows){
		$ifdone=$this->checkiftaskmarkedasdone($rows->id);
		if($ifdone==0)
		{
			if($i==0)
			{
			$smsmessage.="\nHello ".$personname.",\n\nYour following Delegated task are pending. Please complete them before due date.";
			}

		$assignedby = ucfirst($rows->first_name." ".$rows->last_name);
		
		$urgency=$rows->urgency;
		if($urgency==1)
			{
				$ur="HIGH 🟤";
			}else if($urgency==2)
			{
				$ur="MEDIUM 🟠";
			}else if($urgency==3)
			{
				$ur="LOW ⚪";
			}else
			{
				$ur='';
			}

		$smsmessage.="\n\nTask- *".ucfirst($rows->task)."*\n"."Due Date - *".date('d-m-Y',strtotime($rows->targetdate))."*\nAssigned By "."*".$assignedby."*"."\n";

			if($ur<>'')
			{
			$smsmessage.="Priority: ".ucwords(strtolower($ur));
			}

			$smsmessage.="\n\n\n";
		$i++;
	}
	
}
	
	//echo $smsmessage; exit;
	/*Whatsapp Notification*/
						
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							'receiverMobileNo' => '91'.$row->contact_number,
							//'receiverMobileNo' => '918447031736',
							'username' => whatsappuser,
							'password' => whatsapppass,
							'message'=>strip_tags($smsmessage));
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$result = curl_exec($ch);
							//echo $result; exit;
							if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
							}
							curl_close($ch);
						/*Whatsapp Notification*/
	}
	
	
	
	}
	
	
	
	$this->todaysvisit_for_user();
	$this->todaysvisit_for_admin();
}


function checkiftaskmarkedasdone($id)
{
	$a=0;
	$resteye=$this->db->select('task_status')->from('user_response_on_delegated_task')->where('task_id',$id)->order_by('id','DESC')->limit(1)->get();
	if($resteye->num_rows()>0)
	{
		foreach($resteye->result() as $rowss);
		if($rowss->task_status==1)
		{
			$a=1;
		}else
		{
			$a=0;
		}
	}


	return $a;
}


function todaysvisit_for_user()
{
	$sms='';
	$startdate=date('Y-m-d');

	$qq  = $this->db->select('user_id, first_name, last_name, contact_number')->from('system_users_view')->where('user_status',1)->get();
	if($qq->num_rows()>0)
	{
			foreach($qq->result() as $row)
			{
					$chk="AND b.followup_date='$startdate'";
					$chk1="AND b.added_by='$row->user_id'";

				$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 $chk $chk1 ORDER BY b.id DESC");
					if($resty->num_rows()>0)
					{

						$sms.='Dear '.$row->first_name." ".$row->last_name.",\n\n";
						$sms.="Your Today's (".date('d-M-Y',strtotime($startdate)).") Scheduled Visits Detail\n\n";

						foreach($resty->result() as $rows)
						{
						$sms.="-------------------------------\n";
						$sms.="Company Name:*".$rows->company_name."*\n";
						$sms.="Client Name:*".$rows->customer_name."*\n";
						$sms.="Client Contact:*".$rows->contact_person."<br/>".$rows->contact_no."*\n";
						$sms.="Address:".$rows->postal_address."-".$rows->city."\n";
						$sms.="Last Visited On:".date('d-M-Y',strtotime($rows->create_date))."*\n";
						$sms.="Last Remarks:".$rows->clientremarks."\n\n";
						
						}

						$sms.="Thank You"."\n";

						/*Whatsapp Notification*/
						
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							'receiverMobileNo' => '91'.$row->contact_number,
							//'receiverMobileNo' => '918447031736',
							'username' => whatsappuser,
							'password' => whatsapppass,
							'message'=>strip_tags($sms));
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$result = curl_exec($ch);
							//echo $result; exit;
							if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
							}
							curl_close($ch);
						/*Whatsapp Notification*/

				}

				

			}
	}	


	

}


	function todaysvisit_for_admin()
{
	$sms='';
	$sms.='Dear Balwinder Sir'."\n\n";
	$sms.='Please find Employees Visit for Employees Today'."\n\n";

	$startdate=date('Y-m-d');
	$qq  = $this->db->select('user_id, first_name, last_name, contact_number')->from('system_users_view')->where('user_status',1)->where('department_id',6)->order_by('first_name','ASC')->get();
	if($qq->num_rows()>0)
	{
			foreach($qq->result() as $row)
			{
					$chk="AND b.followup_date='$startdate'";
					$chk1="AND b.added_by='$row->user_id'";

					$resty=$this->db->query("SELECT b.id FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 $chk $chk1 ORDER BY b.id DESC");
						$fcount=$resty->num_rows();
						
						$sms.=ucwords(strtolower($row->first_name." ".$row->last_name))."-*".$fcount."*\n\n";

						

						

			}

				

			}


								$sms.="Thank You"."\n";

						

								/*Whatsapp Notification*/

								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_POST, 1);
								$post = array(
								'receiverMobileNo' => '9891941007,918447031736',
								'username' => whatsappuser,
								'password' => whatsapppass,
								'message'=>strip_tags($sms));
								curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
								$result = curl_exec($ch);
								//echo $result; exit;
								if (curl_errno($ch)) {
								echo 'Error:' . curl_error($ch);
								}
								curl_close($ch);
								/*Whatsapp Notification*/
	}	


	


	function attendence_for_the_day()
	{
		$sms='';
		
		$day=date('Y-m-d');
		$dt1 = strtotime($day);
		$dt2 = date("l", $dt1);
		$dt3 = strtolower($dt2);



		if(strtolower($dt3)<>"sun")
		{
		$reste=$this->db->select('a.employee_id,a.morning_time,b.first_name,b.last_name')->from('mark_your_attendance a')->join('system_users b','a.employee_id=b.user_id')->where('a.attendance_date',$day)->get();
		if($reste->num_rows()>0)
		{
			$sms.='Dear Balwinder Sir'."\n\n";
			$sms.='Please find Employees Attendance Details for *'.date('d-m-Y').'*'."\n\n";

			$i=1;
			foreach($reste->result() as $row)
			{

			$sms.=$i.") ".ucwords(strtolower($row->first_name." ".$row->last_name))."-*".date('H:i A',strtotime($row->morning_time))."*\n\n";



			$i++;

			}


			$sms.="Thank You"."\n";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '9891941007,9891941001,918447031736',
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($sms));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);

		}

		}
	
	}


	function check_for_no_attendence()
	{
		$day=date('Y-m-d');
		$dt1 = strtotime($day);
		$dt2 = date("l", $dt1);
		$dt3 = strtolower($dt2);

		if(strtolower($dt3)<>"sun")
		{
		$esteyu=$this->db->select('user_id,first_name,last_name,contact_number')->from('system_users')->where('department_id',6)->where_not_in('user_id','36,37',false)->where('user_status',1)->get();
		if($esteyu->num_rows()>0)
		{
			foreach($esteyu->result() as $row)
			{
				$sms='';

				$reste=$this->db->select('a.employee_id')->from('mark_your_attendance a')->where('a.employee_id',$row->user_id)->where('a.attendance_date',$day)->get();
				if($reste->num_rows()==0)
				{

					$leave=$this->checkforleave($row->user_id);
					if($leave==0)
					{
						
					$sms.='Dear '.ucwords(strtolower($row->first_name)).' '.ucwords(strtolower($row->last_name)).','."\n";
					$sms.='Good Morning 😊'."\n\n";
					$sms.='Your attendance for '.date('d-m-Y').' is not marked yet.'."\n\n".' *Please mark it to avoid and salary deductions*'."\n\n";


						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						$post = array(
						'receiverMobileNo' => $row->contact_number.',918447031736',
						//'receiverMobileNo' => '918447031736',
						'username' => whatsappuser,
						'password' => whatsapppass,
						'message'=>strip_tags($sms));
						curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
						$result = curl_exec($ch);
						//echo $result; exit;
						if (curl_errno($ch)) {
						echo 'Error:' . curl_error($ch);
						}
						curl_close($ch);


					}

				}


			}

		}

	}

	}


	function minutecron()
	{
		$todays_date = date('Y-m-d');
	$data=array('runningtime'=>date('Y-m-d H:i:s'));
	$this->db->insert('dashboardcronstatus',$data);
	
		$d=date('H:i'); 
		

		
		if($d=="11:00")
		{
		$this->attendence_for_the_day();
		$this->check_for_no_attendence();
		}



		if($d=="10:00")
		{
		$this->todays_followup_reminders();
				}
		if($d=="21:00")
		{
			$this->attendence_for_previous_day_the_day();
		}

		if($d=="09:00")
		{
			$this->pendingdelegatedtaskreminder();
		}

		if($d=="11:00")
		{
			//$this->customer_payment_reminders_to_admin_new();
		
			$this->order_on_hold_admin();

			
			
		}

		if($d=="08:30")
		{
			
			$this->cheque_to_be_recieved_user();
			$this->cheque_to_be_deposited();
			$this->order_on_hold_agent();
			$this->trail_reminder();
		}

		

		$this->send_agent_payment_notification();

		//$this->customer_payment_reminders_to_admin();

		$this->admin_approval_data();

	}


	function checkforleave($user_id)
	{
		$day=date('Y-m-d');
		$restey=$this->db->query("SELECT id FROM leave_application
		WHERE employee_id=$user_id AND '{$day}' between from_loc and to_loc");

		return $restey->num_rows();
	}


	function customer_payment_reminders_to_admin()
	{

		$flag=$this->uri->segment(3);
		$currentday=date('Y-m-d');
		$this->load->library("Excel");
		$object = new PHPExcel();
		$object->createSheet(1);
		$object->setActiveSheetIndex(0);
		$table_columns = array("S.No.","Customer Name","Invoice No.","Billing Company","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");
		$column = 0;
		$object->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true);
		
		$object->getActiveSheet()->setTitle("Payment Overdue");
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('J')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('K')->setWidth(40);
	

		
	
		

		for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
			$object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
		}

		foreach ($table_columns as $field) {
			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
			$column++;
		}


		// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.send_to_tally',1);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				$excel_row = 2;
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{

						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;


						$table_columns = array("S.No.","Customer Name","Invoice No.","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");

						$object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(20);

						$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->company_name);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,$row->invoice_no);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,$row->companyname);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,$order_value);
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $partial);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $payment_due);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,date('d-M-Y',strtotime($row->send_to_tally_On)));
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $creditdays);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,date('d-M-Y',strtotime($expected_payment_days)));
				        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row,$row->first_name." ".$row->last_name);

				        $object->getActiveSheet()->getStyle("J".$excel_row)->getFont()->setBold(true);
				       

				$i++;
				$excel_row++;
				}

			}

		}





		$object->createSheet(2);
		$object->setActiveSheetIndex(1);

		$table_columns = array("S.No.","Customer Name","Invoice No.","Billing Company","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");
		$column = 0;
		$object->getProperties()->setCreator("Saurabh Dubey")
        ->setLastModifiedBy("Saurabh Dubey")
        ->setTitle("Payment Overdue")
        ->setSubject("Payment Overdue")
        ->setDescription("Payment Overdue")
        ->setKeywords("")
        ->setCategory("");
		$object->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true);
		$object->getActiveSheet()->setTitle("Payment Due in 10 Days");
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('J')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('K')->setWidth(40);
	

		
	
		

		for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
			$object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
		}

		foreach ($table_columns as $field) {
			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
			$column++;
		}


		// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				$excel_row = 2;
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{

						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;


						$table_columns = array("S.No.","Customer Name","Invoice No.","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");

						$object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(20);

						$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->company_name);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,$row->invoice_no);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,$row->companyname);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,$order_value);
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $partial);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $payment_due);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,date('d-M-Y',strtotime($row->send_to_tally_On)));
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $creditdays);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,date('d-M-Y',strtotime($expected_payment_days)));
				        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row,$row->first_name." ".$row->last_name);

				        $object->getActiveSheet()->getStyle("J".$excel_row)->getFont()->setBold(true);
				       

				$i++;
				$excel_row++;
				}
		

			}

		}



			$fileName = 'Customer_Payments-'.date('d-M-Y').'.xls'; 
			$savepath=SITE_ROOT.'/customer_payments/'.$fileName;
			$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
			$object_writer->save($savepath);
			// header("Content-type:application/vnd.ms-excel");
			// header('Content-Disposition: attachment; filename=' . $fileName);
			//readfile( $savepath );


			/** SEND WHATSAPP **/

			$msgbody="Hello\n\n";
			$msgbody.="Please find *Payment Overdue/Payment Due (10 Days)* Details for the day ".date('d-M-Y')."\n\n";
			$msgbody.="Team Sunder Industrial Oil";

			$file_url = site_http_root."/customer_payments/Customer_Payments-".date('d-M-Y').".xls";
		
		$extra='';
		if($flag==1)
		{
			if($this->input->post('mobile')<>''){
			$extra=",".$this->input->post('mobile');
			}
		}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '918447031736'.$extra,
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($msgbody),
			'filePathUrl' => $file_url		
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			// echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			/** END **/

			if($flag==1)
				{

					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Reminder Sent</div>');
					redirect(page_url.'Leads/triggers');
				}

	}


	function getOrderAmountWithGST($quotation, $buyer_gst, $seller_gst)
	{
		
		  $price = array();
		  $sql = $this->db->select('agreed_price, qty')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows()>0) {
				foreach($sql->result() as $row) {
					$price[] = $row->agreed_price * $row->qty;
				}
			
			}

			$total_price = array_sum($price);

			$total_gst = 0;

		    if($buyer_gst<>'' && $seller_gst <>'') {
		        $bscode = substr($buyer_gst,0,2);
		        $sscode = substr($seller_gst,0,2);

		        if($bscode == $sscode) {
            		$gst = ($total_price*9)/100;
            		$total_gst = $gst + $gst;
		        } else {
		        	$gst = ($total_price*18)/100;
		        	$total_gst = $gst;
        		}
        	} else {
		        $igst=0;
		        $cgst=0;
		    }

		    $final_amt = $total_price + $total_gst;

		    return $final_amt;
	}



	function customer_previous_payment($order_id)
	{
		$recvd=array();
		$recvd[]=0;
		$Resteuy=$this->db->select('order_amount,recieved_amount')->from('customer_order_to_payments')->where('order_id',$order_id)->get();
		if($Resteuy->num_rows()>0){
			foreach($Resteuy->result() as $row)
			{
				$recvd[]=$row->recieved_amount;
			}
		}

		return array_sum($recvd);
	}



	function customer_payment_reminders_to_sales()
	{

		$currentday=date('Y-m-d');
		$this->load->library("Excel");
		

		$qq  = $this->db->select('user_id, first_name, last_name, contact_number')->from('system_users_view')->where('user_status',1)->where('department_id',6)->get();
	if($qq->num_rows()>0)
	{
			foreach($qq->result() as $salesuser)
			{


		$object = new PHPExcel();
		$object->createSheet(1);
		$object->setActiveSheetIndex(0);
		$table_columns = array("S.No.","Customer Name","Invoice No.","Billing Company","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");
		$column = 0;
		$object->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true);
		
		$object->getActiveSheet()->setTitle("Payment Overdue");
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('J')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('K')->setWidth(40);
	

		
	
		

		for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
			$object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
		}

		foreach ($table_columns as $field) {
			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
			$column++;
		}


		// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.agent',$salesuser->user_id);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				$excel_row = 2;
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{

						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;


						$table_columns = array("S.No.","Customer Name","Invoice No.","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");

						$object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(20);

						$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->company_name);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,$row->invoice_no);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,$row->companyname);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,$order_value);
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $partial);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $payment_due);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,date('d-M-Y',strtotime($row->send_to_tally_On)));
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $creditdays);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,date('d-M-Y',strtotime($expected_payment_days)));
				        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row,$row->first_name." ".$row->last_name);

				        $object->getActiveSheet()->getStyle("J".$excel_row)->getFont()->setBold(true);
				       

				$i++;
				$excel_row++;
				}

			}

		}





		$object->createSheet(2);
		$object->setActiveSheetIndex(1);

		$table_columns = array("S.No.","Customer Name","Invoice No.","Billing Company","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");
		$column = 0;
		$object->getProperties()->setCreator("Saurabh Dubey")
        ->setLastModifiedBy("Saurabh Dubey")
        ->setTitle("Payment Overdue")
        ->setSubject("Payment Overdue")
        ->setDescription("Payment Overdue")
        ->setKeywords("")
        ->setCategory("");
		$object->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true);
		$object->getActiveSheet()->setTitle("Payment Due in 10 Days");
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('J')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('K')->setWidth(40);
	

		
	
		

		for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
			$object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
		}

		foreach ($table_columns as $field) {
			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
			$column++;
		}


		// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$salesuser->user_id)
						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);
				
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				$excel_row = 2;
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{

						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;


						$table_columns = array("S.No.","Customer Name","Invoice No.","Order Amount","Partial Payment","Payment Due","Billing Date","Credit Days","Expected Payment Date","Sales Agent");

						$object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(20);

						$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->company_name);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,$row->invoice_no);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,$row->companyname);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,$order_value);
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $partial);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $payment_due);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,date('d-M-Y',strtotime($row->send_to_tally_On)));
				     
				        $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $creditdays);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,date('d-M-Y',strtotime($expected_payment_days)));
				        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row,$row->first_name." ".$row->last_name);

				        $object->getActiveSheet()->getStyle("J".$excel_row)->getFont()->setBold(true);
				       

				$i++;
				$excel_row++;
				}
		

			}

		}



			$fileName = 'Customer_Payments_'.$salesuser->user_id."_".date('d-M-Y').'.xls'; 
			$savepath=SITE_ROOT.'/customer_payments/for_sales/'.$fileName;
			$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
			$object_writer->save($savepath);
			//  header("Content-type:application/vnd.ms-excel");
			//  header('Content-Disposition: attachment; filename=' . $fileName);
			// readfile( $savepath );


			/** SEND WHATSAPP **/

			$msgbody="Hello ".$salesuser->first_name." ".$salesuser->last_name."\n\n";
			$msgbody.="Please find your customers *Payment Overdue/Payment Due (10 Days)* Details for the day ".date('d-M-Y')."\n\n";
			$msgbody.="Team Sunder Industrial Oil";

			$file_url = site_http_root."/customer_payments/for_sales/Customer_Payments_".$salesuser->user_id."_".date('d-M-Y').".xls";
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '918447031736',
			//'receiverMobileNo' => $salesuser->contact_number,
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($msgbody),
			'filePathUrl' => $file_url		
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


	}
			/** END **/

	}

}


	function customer_payment_reminders_to_customer()
	{
		$flag=$this->uri->segment(3);

		$currentday=date('Y-m-d');
		$message='';
		// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.id as customer_id,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.send_to_tally',1);

						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			$customer=array();
			if($query->num_rows() > 0) {
				$excel_row = 2;
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{
							

							

						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

						$message="*Invoice No. ".$row->invoice_no."*\n    *Due Amt ".$payment_due."* \n    *Invoice Date ".date('d-M-Y',strtotime($row->send_to_tally_On))."*\n";

						$customer[$row->customer_id][]=array('customer_id'=>$row->customer_id,'invoice_details'=>$message);
						
						$i++;

						
					}

						

					
						
					}

				}


				foreach ($customer as $key => $value)
				{
					$cust_data=$this->getcustomer_details($key);
					if(count($cust_data)>0)
					{
					$message="Hello Sir, \n\n";
					$message.="Payment against following Invoice(s) are Overdue. Request you to release the payments.\n\n";
					$ik=1;
					foreach($customer[$key] as $data)
					{
						$message.=$ik.") ".$data['invoice_details']."\n";
						//echo "<pre>"; print_r($data); 
						 
					$ik++;
					}

					$message.="\n\nTeam Sunder Industrial Oil";

					/** SEND WHATSAPP **/

					$extra='';
					if($flag==1)
					{
					if($this->input->post('mobile')<>''){
					$extra=",".$this->input->post('mobile');
					}
					}
					

					$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							//'receiverMobileNo' => '91'.$cust_data[0],
							'receiverMobileNo' => '918447031736'.$extra,
							'username' => whatsappuser,
							'password' => whatsapppass,
							'message'=>strip_tags($message));
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$result = curl_exec($ch);
							//echo $result; exit;
							if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
							}
							curl_close($ch);
				

					/** END **/
					
				}
					
					
					
					
				}



		// FOR UPCOMING PAYMENTS

		$customer1 = array();
		 $this->db->select('d.id as customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);


						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {

					if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}



					$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);

						$partial=$this->customer_previous_payment($row->id);
	
						$payment_due=$order_value-$partial;


						$message="*Invoice No. ".$row->invoice_no."*\n    *Due Amt ".$payment_due."* \n    *Invoice Date ".date('d-M-Y',strtotime($row->send_to_tally_On))."*\n    *Due On ".date('d-M-Y',strtotime($expected_payment_days))."* \n";

						$customer1[$row->customer_id][]=array('customer_id'=>$row->customer_id,'invoice_details'=>$message);

				$i++;
				}

				}

			}

				

				foreach ($customer1 as $key => $value)
				{
					$cust_data=$this->getcustomer_details($key);
					if(count($cust_data)>0)
					{
					$message="Hello Sir, \n\n";
					$message.="Payment against following Invoice(s) are Due in next 10 days. Request you to release the payments.\n\n";
					$ik=1;
					foreach($customer1[$key] as $data)
					{
						$message.=$ik.") ".$data['invoice_details']."\n";
						//echo "<pre>"; print_r($data); 
						 
					$ik++;
					}

					$message.="\n\nTeam Sunder Industrial Oil";


				
					/** SEND WHATSAPP **/

					if($flag==1)
					{
						$extra=",".$this->input->post('mobile');
					}else
					{
						$extra='';
					}

					$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							//'receiverMobileNo' => '91'.$cust_data[0],
							'receiverMobileNo' => '918447031736'.$extra,
							'username' => whatsappuser,
							'password' => whatsapppass,
							'message'=>strip_tags($message));
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$result = curl_exec($ch);
							//echo $result; exit;
							if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
							}
							curl_close($ch);
					
					

					/** END **/
					

				}
					
					
					
					
				}


				if($flag==1)
				{

					$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Reminder Sent</span><br/>');
					redirect(page_url.'Leads/triggers');
				}
			
	}

	function getcustomer_details($custid)
	{
		$data=array();
		$restey=$this->db->select('contact_no')->from('customer_detail')->where('id',$custid)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);	
			$data[]=$row->contact_no;

		}

		return $data;
	}


	function order_on_hold_admin(){

		$flag=$this->uri->segment(3);
		$message='';
		$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
		b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
		->from('order_punch a')
		->join('order_punch_mailing_details d','a.id=d.order_id')
		->join('order_punch_tax_details f','a.id=f.order_id')
		->join('lead_source g','g.source_id=a.source','left')
		->join('system_users h','h.user_id=a.agent')
		->join('order_punch_tax_details e','a.id=e.order_id')
		->join('customer_quotation b','b.id=a.quotation_id')
		->join('store_rack_location c', 'c.id=b.company_id', 'left')
		->where('a.cancelled', 0)
		->where('a.billing', 0)
		->order_by('a.id','DESC')
		->get();

		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}

			$this_order_amount=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->seller_gst);
			$order_amount = $this->getunpaid_order_amount($row->customer_id);
			

			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;

			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{				
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="Max Order Limit Reached";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}
		
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

					}
				}
			 
			 	$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="Previous Invoice(s) (".$prev_pdc.") PDC not recieved";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$hold_type="Previous Invoices (".$prev_pdc.") PDC date exceeds payment terms";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}


				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						
						$hold_type="Current Order PDC not recieved";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					
						$hold_type="Current Order PDC Date Exceeds Payment Terms";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}

			if($show == 0) {

				if($i==1)
				{
				$message.="Hello Sir,\n\n";
				$message.="Following Orders are on Hold as on ".date('d-m-Y').",\n\n";
				}

				$message.="Company Name- "."*".$companyname."*\n";
				$message.="Invoice No.- "."*".$row->invoice_no."*\n";
				$message.="Reason - "."*".$hold_type."*\n\n\n";



				$i++;
		}

		




		
	}

			if($message<>'')
			{
			$message.="Team Sunder Industrial Oil";
			}
		
	}




		if($message<>'')
		{

			$extra='';
		if($flag==1)
		{
			if($this->input->post('mobile')<>''){
			$extra=",".$this->input->post('mobile');
			}
		}


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			//'receiverMobileNo' => '918447031736',
			'receiverMobileNo' => '918447031736,919891941007,919891941001'.$extra,
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($message));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);

		}



		if($flag==1)
				{

					$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Reminder Sent</span><br/>');
					redirect(page_url.'Leads/triggers');
				}


	}



	function order_on_hold_agent(){


		$flag=$this->uri->segment(3);
		$qq  = $this->db->select('user_id, first_name, last_name, contact_number')->from('system_users_view')->where('user_status',1)->order_by('first_name','ASC')->get();
		//->where('department_id',6)
	if($qq->num_rows()>0)
	{
			foreach($qq->result() as $rowuser)
			{	


				$message='';
		$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
		b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
		->from('order_punch a')
		->join('order_punch_mailing_details d','a.id=d.order_id')
		->join('order_punch_tax_details f','a.id=f.order_id')
		->join('lead_source g','g.source_id=a.source','left')
		->join('system_users h','h.user_id=a.agent')
		->join('order_punch_tax_details e','a.id=e.order_id')
		->join('customer_quotation b','b.id=a.quotation_id')
		->join('store_rack_location c', 'c.id=b.company_id', 'left')
		->where('a.cancelled', 0)
		->where('a.billing', 0)
		->where('a.agent',$rowuser->user_id)
		->order_by('a.id','DESC')
		->get();

		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}

			$this_order_amount=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->seller_gst);
			$order_amount = $this->getunpaid_order_amount($row->customer_id);
			

			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;

			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{				
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="Max Order Limit Reached";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}
		
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}

						}

					}
				}
			 
			 	$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="Previous Invoice(s) (".$prev_pdc.") PDC not recieved";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$hold_type="Previous Invoices (".$prev_pdc.") PDC date exceeds payment terms";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}


				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						
						$hold_type="Current Order PDC not recieved";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					
						$hold_type="Current Order PDC Date Exceeds Payment Terms";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}

			if($show == 0) {

				if($i==1)
				{
				$message.="Hello ".$rowuser->first_name." ".$rowuser->last_name.",\n\n";
				$message.="Your Following Orders are on Hold as on ".date('d-m-Y').",\n\n";
				}

				$message.="Company Name- "."*".$companyname."*\n";
				$message.="Invoice No.- "."*".$row->invoice_no."*\n";
				$message.="Reason - "."*".$hold_type."*\n\n\n";



				$i++;
		}

		




		
	}

			if($message<>'')
			{
			$message.="Team Sunder Industrial Oil";
			}
		
	}




		if($message<>'')
		{
			$extra='';
		if($flag==1)
		{
			if($this->input->post('mobile')<>''){
			$extra=",".$this->input->post('mobile');
			}
		}


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '91'.$rowss->contact_number.',8447031736',
			//'receiverMobileNo' => '918447031736'.$extra,
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($message));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);

		}




			}

}


	if($flag==1)
				{

					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Reminder Sent</div><br/>');
					redirect(page_url.'Leads/triggers');
				}

}

		function check_remove_hold($order_id) {
		$sql = $this->db->select('id')
						->from('order_remove_hold')
						->where('order_id', $order_id)
						->get();

		return $sql->num_rows();
	}


	function getCustomerdetail($customerid)
	{
		$data=array();
		$r=$this->db->select('customer_name,company_name, order_max_limit')->from('customer_detail')->where('id',$customerid)->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $row);
			$data[]=$row->customer_name;
			$data[]=$row->company_name;
			$data[]=$row->order_max_limit;
		}

		return  $data;

	}

	function getunpaid_order_amount($customer_id)
	{
		$payment_due_sum=array();
		$query = $this->db->select('a.id,a.quotation_id,e.gst_no,c.gst')
		->from('order_punch a')
		->join('order_punch_mailing_details d','a.id=d.order_id')
		->join('order_punch_tax_details f','a.id=f.order_id')
		->join('lead_source g','g.source_id=a.source','left')
		->join('system_users h','h.user_id=a.agent')
		->join('order_punch_tax_details e','a.id=e.order_id')
		->join('customer_quotation b','b.id=a.quotation_id')
		->join('store_rack_location c', 'c.id=b.company_id', 'left')
		->where('a.payment', 0)
		->where('b.customer_id',$customer_id)
		->order_by('a.id','DESC')
		->get();

		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
			
				$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
				$partial=$this->customer_previous_payment($row->id);
				$payment_due=$order_value-$partial;
				$payment_due_sum[]=$payment_due;

			}
		}
		

		return array_sum($payment_due_sum);
	}



	
	function admin_approval_data()
	{
		$resty=$this->db->select('a.*,b.first_name,b.last_name')->from('product_competitor_files a')->join('system_users b','a.addedby=b.user_id')->where('status',0)->get();

		$a1=$resty->num_rows();

		$sql1 = $this->db->select('a.id')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
						->get();
		$a2=$sql1->num_rows();

		$this->db->select('a.id,a.customer_name,a.company_name,a.payment_type,a.credit_days,c.companyname,a.added_on')->from('customer_detail a');
		$this->db->join('store_rack_location c','a.company_id=c.id');
		$sql2=$this->db->where('a.payment_term_approval','0')->get();
		$a3=$sql2->num_rows();

		$a4=$this->convence_for_approval();

		$a1=$a1+$a2+$a3+$a4;


	
		$data=array('report_count'=>$a1);
		$this->db->where('id',58);
		$this->db->update('administrator_dashboard',$data);
	}



	function check_for_previous_pdc($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.received',0)->where('a.order_id !=',$order_id)->where('b.cancelled',0)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}


function check_for_current_pdc($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.received',0)->where('b.cancelled',0)->where('a.order_id',$order_id)->get();
	return $rest->num_rows();
	
	}


	function check_for_previous_pdc_hold_due_to_date($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.hold_due_to_pdc',1)->where('a.order_id!=',$order_id)->where('b.cancelled',0)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}


	function check_for_current_pdc_hold_due_to_date($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.hold_due_to_pdc',1)->where('order_id',$order_id)->where('b.cancelled',0)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}

	function checkfor_hold_release($order_id,$hold_type)
	{
		
		$restey=$this->db->select('id')->from('order_remove_hold')->where('order_id',$order_id)->where('hold_type',$hold_type)->get();
		return $restey->num_rows();


	}


	function cheque_to_be_recieved_user()
	{
		$flag=$this->uri->segment(3);
		$message='';


// $q = $this->db->select('user_id, first_name,user_role_id, last_name, email, contact_number')
// ->from('system_users_view')
// ->where('user_status',1)
// ->get();

// if($q->num_rows()>0) {



// foreach($q->result() as $rowss) {

			$sql = $this->db->select('a.id, b.invoice_no,a.expected_pdc_date, d.company_name,b.added_on,b.quotation_id,c.gst_no as buyer_gst,e.gst as seller_gst')
							->from('customer_cheque_details a')
							->join('order_punch b', 'b.id=a.order_id')
							->join('order_punch_tax_details c','c.order_id=b.id')
							->join('customer_quotation f','f.id=b.quotation_id')
							->join('customer_detail d', 'd.id=f.customer_id')
							 ->join('store_rack_location e', 'e.id=b.hpcl_billing_company', 'left')
							->where('a.received', 0)
							->where('a.order_punch_date>','2023-06-30')
						
							->get();
			if($sql->num_rows() > 0) {
			$i=1;
				$message="Hello\n\n";
				$message.="*ALERT 🚨*\n\n";
				$message.="Following PDC are not recieved. Kindly Followup with the customer\n\n";
				foreach($sql->result() as $row) {

					
				$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->buyer_gst,$row->seller_gst);
					/*Whatsapp Notification*/
					$message .= 'Company Name: *'.$row->company_name.'*'."\n";
					$message .= 'Invoice No.- '.$row->invoice_no."\n";
					$message .= 'Order Amount: '.$order_value."\n";
					$message .= 'Order Date: '.date('d-m-Y', strtotime($row->added_on))."\n\n\n";

				$i++;
				}
					
					$message .= "Team Sunder Industrial Oil"."\n\n\n";
			

					echo $message; exit;
					$extra='';
					if($flag==1)
					{
					if($this->input->post('mobile')<>''){
					$extra=",".$this->input->post('mobile');
					}
					}


					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					'receiverMobileNo' => '918447031736,919891941007,919891941001,919953139281'.$extra,
					// 'receiverMobileNo' => '9560814669',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($message));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
						/*Whatsapp Notification*/				


			}

	// 	}

	// }

			if($flag==1)
				{

					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Reminder Sent</div><br/>');
					redirect(page_url.'Leads/triggers');
				}
}





	function cheque_to_be_deposited()
	{
		$message='';
$flag=$this->uri->segment(3);

// $q = $this->db->select('user_id, first_name,user_role_id, last_name, email, contact_number')
// ->from('system_users_view')
// ->where('user_status',1)
// ->get();

// if($q->num_rows()>0) {



// foreach($q->result() as $rowss) {

			$sql = $this->db->select('a.id, b.invoice_no,a.cheque_amount,a.expected_pdc_date, d.company_name,b.added_on,b.quotation_id,c.gst_no as buyer_gst,e.gst as seller_gst,e.companyname as billing_company,a.cheque_no')
							->from('customer_cheque_details a')
							->join('order_punch b', 'b.id=a.order_id')
							->join('order_punch_tax_details c','c.order_id=b.id')
							->join('customer_quotation f','f.id=b.quotation_id')
							->join('customer_detail d', 'd.id=f.customer_id')
							 ->join('store_rack_location e', 'e.id=b.hpcl_billing_company', 'left')
							->where('a.received', 1)
							->where('a.expected_pdc_date',date('Y-m-d'))
							->where('a.deposited', 0)

							->get();
			if($sql->num_rows() > 0) {

				
			$i=1;
				$message="Hello\n\n";
				$message.="*ALERT 🚨*\n\n";
				$message.="Following PDC are pending for deposit today. Kindly deposit and update in CRM\n\n";
				foreach($sql->result() as $row) {

					/*Whatsapp Notification*/
					$message .= 'From: *'.$row->company_name.'*'."\n";
					$message .= 'Cheque No.: '.$row->cheque_no."\n";
					$message .= 'Cheque Amount: '.$row->cheque_amount."\n";
					$message .= 'Cheque Date: '.date('d-m-Y', strtotime($row->expected_pdc_date))."\n";
					$message .= 'Billing Company: '.$row->billing_company."\n\n\n";
					

				$i++;
				}
					
					$message .= "Team Sunder Industrial Oil";
			
					$extra='';
		if($flag==1)
		{
			if($this->input->post('mobile')<>''){
			$extra=",".$this->input->post('mobile');
			}
		}




		if($i>1)
		{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					'receiverMobileNo' => '918447031736,919891941007,919891941001,919953139281'.$extra,
					// 'receiverMobileNo' => '9560814669',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($message));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
						/*Whatsapp Notification*/
		}				


			}

	// 	}

	// }

			if($flag==1)
				{

					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Reminder Sent</div><br/>');
					redirect(page_url.'Leads/triggers');
				}
}






		function customer_payment_reminders_to_admin_new()
	{

		$flag=$this->uri->segment(3);
		$flag1=$this->uri->segment(4);

		
		$currentday=date('Y-m-d');
		$this->load->library("Excel");
		$object = new PHPExcel();

		$sql = $this->db->select('id,companyname')
						->from('store_rack_location')
						// ->where('id',3)
						->order_by('sort','ASC')
						->get();

		$i=0;
		

		if($sql->num_rows() > 0) {
			
	    	foreach($sql->result() as $rowcompany) {

	    $customers=array();
		$objWorkSheet=$object->createSheet($i);
		$objWorkSheet->setTitle($this->clean($rowcompany->companyname));
	
	    	
		$table_columns = array("S.No.","Customer Name","Invoices","Payment Overdue","Payment Due in 10 Days",'Total Payment Due','Do Not Follow Customer','Do Not Follow By','Invoice Date','Overdue Days');
		$column = 0;
		$objWorkSheet->getStyle("A1:J1")->getFont()->setBold(true);
		$object->getActiveSheet()->getStyle("A1:J1")->getFont()->setBold(true);
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('J')->setWidth(40);

		$style_cell = array( 'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, ) );

		$objWorkSheet->getStyle("A1:J1")->applyFromArray(
					$style_cell
				
					);

		$object
    ->getActiveSheet()
    ->getStyle('A1:J1')
    ->getFill()
    ->getStartColor()
    ->setRGB('FFDBE2F1');
	



		for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
					$objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
				}
	    		
		foreach ($table_columns as $field) {
		$objWorkSheet->setCellValueByColumnAndRow($column, 1, $field);
		$column++;
		}



			 $this->db->select('a.unfollow_customer,a.unfollow_added_by,b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.cancelled',0)
						  //->where('a.unfollow_customer',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.hpcl_billing_company',$rowcompany->id);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{


							$customers[]=$row->customer_id;

						}

					}

				}


		
				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.cancelled',0)
						 // ->where('a.unfollow_customer',0)
						  // ->where('a.invoice_no',848)
						  ->where('a.hpcl_billing_company',$rowcompany->id)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


		
			if($query->num_rows() > 0) {
			
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
					 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
					{
						$customers[]=$row->customer_id;
					}

				}


			}

		

				
		//	echo "<pre>"; print_r($customers); exit;

			$overdue = array();
		if(count($customers)>0)
		{
			$unique_cust=array_unique($customers);


			$k=0;
			$r=0;
			foreach($unique_cust as $customers_data)
			{


			$overdue_data=array();
				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.cancelled',0)
						  // ->where('a.unfollow_customer',0)
						  ->where('a.send_to_tally',1)
						  ->where('b.customer_id',$customers_data);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


				if($query->num_rows() > 0) {
					$l=1;
					
				$excel_row=2;
				foreach($query->result() as $row) {
						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{

							/** UNFOLLOW DATA **/
							if($row->unfollow_customer==1)
							{
								$unfname=$this->getusername($row->unfollow_added_by);
								$unfollow="Yes";
							}else{
								$unfname='';
								$unfollow='';
							}

						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

							$overdue['type'][]=1;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;
						$overdue['unfollow'][]=$unfollow;
						$overdue['unfollowBy'][]=$unfname;
						$overdue['send_to_tally_On'][]=date('d-M-Y',strtotime($row->send_to_tally_On));
						$overdue['overdueday'][]=$exceed_days;

						}
				
				}

				}

				// PAYMENT DUE IN 10 DAYS

				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.cancelled',0)
						  // ->where('a.unfollow_customer',0)
						  ->where('b.customer_id',$customers_data)
						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{
					/** UNFOLLOW DATA **/
							if($row->unfollow_customer==1)
							{
								$unfname=$this->getusername($row->unfollow_added_by);
								$unfollow="Yes";
							}else{
								$unfname='';
								$unfollow='';
							}

					$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

						$overdue['type'][]=2;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;
						$overdue['unfollow'][]=$unfollow;
						$overdue['unfollowBy'][]=$unfname;
						$overdue['send_to_tally_On'][]=date('d-M-Y',strtotime($row->send_to_tally_On));
						$overdue['overdueday'][]=0;

				}
			}

		}


		

			$excel_row=2;
		
			
			$totalrow=count($overdue['type']);
			$cust='';
			$d=array();
			$cust=0;
			$overdue_sum=array();
			$overdue_sum[]=0;
			$due_sum=array();
			$due_sum[]=0;
			$start=2;
			$st=2;
			for($t=0;$t<count($overdue['type']);$t++)
			{

				
				
				
				if($t<>0 && $cust!=$overdue['customer'][$t])
				{
				
			
				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,array_sum($overdue_sum));
				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,array_sum($due_sum));
				$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row,'');
				

				$object->getActiveSheet()->mergeCells('F'.$st.':F'.$excel_row);

				$payment_sum=array_sum($overdue_sum)+array_sum($due_sum);
				$object->getActiveSheet()->getCell('F'.$st)->setValue($payment_sum);
				$object->getActiveSheet()->getStyle('F'.$st)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$object->getActiveSheet()->getStyle('F'.$st)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

					$styleArray = array(
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
  );

					$objWorkSheet->getStyle("A".$st.":J".$excel_row)->applyFromArray(
				$styleArray );

					
				$style_cell = array( 'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, ) );

				$objWorkSheet->getStyle("A".$st.":I".$excel_row)->applyFromArray(
				$style_cell );


				$excel_row=$excel_row+4;
				
				
					$overdue_sum=array();
					$overdue_sum[]=0;
					$due_sum=array();
					$due_sum[]=0;
				}
				

				
				


				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,'');
			
				
						if($cust!=$overdue['customer'][$t])
						{
						$start=$excel_row;
						$st=$start;
						}

					$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $overdue['customer'][$t]);
				

				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $overdue['invoice'][$t]);

				if($overdue['type'][$t]==1){

					$odue=$overdue['due'][$t];
					$pdue=0;

				}else
				{
					$pdue=$overdue['due'][$t];
					$odue=0;
				}
				$overdue_sum[]=$odue;
				$due_sum[]=$pdue;
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $odue);

				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $pdue);
				$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $overdue['unfollow'][$t]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $overdue['unfollowBy'][$t]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $overdue['send_to_tally_On'][$t]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $overdue['overdueday'][$t]);

				

				
			$cust=$overdue['customer'][$t];

			$excel_row++;

			
		}


		
		} 



$k++;
}

}





				$object->setActiveSheetIndex(0);
				$fileName = 'Customer_Payments-'.date('d-M-Y').'.xls'; 


	if($flag1==1)
	{
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=".$fileName);
	header("Pragma: no-cache");
	header("Expires: 0");
	}

			//$fileName = 'New Test'.date('d-M-Y').'.xls'; 
			$savepath=SITE_ROOT.'/customer_payments/'.$fileName;
			$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
			$object_writer->save($savepath);

			if($flag1==1)
			{
				$object_writer->save('php://output');

			
			}

	

			/** SEND WHATSAPP **/
			if($flag1=='')
			{

			$msgbody="Hello\n\n";
			$msgbody.="Please find *Payment Overdue/Payment Due (10 Days)* Details for the day ".date('d-M-Y')."\n\n";
			$msgbody.="Team Sunder Industrial Oil";

			$file_url = site_http_root."/customer_payments/Customer_Payments-".date('d-M-Y').".xls";
		
		$extra='';
		if($flag==1)
		{
			if($this->input->post('mobile')<>''){
			$extra=",".$this->input->post('mobile');
			}
		}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '918447031736,919891941007,919891941001,919953139281'.$extra,
			//'receiverMobileNo' => '918447031736',
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($msgbody),
			'filePathUrl' => $file_url		
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);
		}


			/** END **/

			if($flag==1)
				{

					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Reminder Sent</div>');
					redirect(page_url.'Leads/triggers');
				}


}
}



function clean($string) {


   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function convence_for_approval()
	{
		
			
		$scheduler_data = array();
		$scheduler_data[]=0;


		for ($l = -3; $l <= 0; $l++){

			$m1=date('Y-m', strtotime("$l month"));

			$start_date=date($m1.'-01');
			$end_date=date($m1.'-t');

			

		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{ $scheduler_data[] =1;
		}
		}
	}
		return array_sum($scheduler_data);
	}
		


	function customer_payment_reminders_to_sales_new()
	{

		$flag=$this->uri->segment(3);
		
		$currentday=date('Y-m-d');
		

		$qq  = $this->db->select('user_id, first_name, last_name, contact_number')->from('system_users_view')->where('user_status',1)->where('department_id',6)->get();
	if($qq->num_rows()>0)
	{
			foreach($qq->result() as $salesuser)
			{

				$this->load->library("Excel");
		$object = new PHPExcel();


		$sql = $this->db->select('id,companyname')
						->from('store_rack_location')
						// ->where('id',3)
						->order_by('sort','ASC')
						->get();

		$i=0;
		if($sql->num_rows() > 0) {
			
	    	foreach($sql->result() as $rowcompany) {

	    		$customers=array();
		$objWorkSheet=$object->createSheet($i);
		$objWorkSheet->setTitle($this->clean($rowcompany->companyname));
	
	    	
		$table_columns = array("S.No.","Customer Name","Invoices","Payment Overdue","Payment Due in 10 Days");
		$column = 0;
		$objWorkSheet->getStyle("A1:F1")->getFont()->setBold(true);
		$object->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true);
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
	

		for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
					$objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
				}
	    		
		foreach ($table_columns as $field) {
		$objWorkSheet->setCellValueByColumnAndRow($column, 1, $field);
		$column++;
		}


			 $this->db->select('b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.agent',$salesuser->user_id)
						  ->where('a.hpcl_billing_company',$rowcompany->id);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{


							$customers[]=$row->customer_id;

						}

					}

				}



				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$salesuser->user_id)
						  // ->where('a.invoice_no',848)
						  ->where('a.hpcl_billing_company',$rowcompany->id)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


		
			if($query->num_rows() > 0) {
			
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
					 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
					{
						$customers[]=$row->customer_id;
					}

				}


			}

		

				
		//	echo "<pre>"; print_r($customers); exit;

		if(count($customers)>0)
		{
			$unique_cust=array_unique($customers);


			$k=0;
			foreach($unique_cust as $customers_data)
			{

				$overdue_data=array();
				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.agent',$salesuser->user_id)
						  ->where('a.payment',0)
						  ->where('a.send_to_tally',1)
						  ->where('b.customer_id',$customers_data);

						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


				if($query->num_rows() > 0) {
					$l=1;
					
				$excel_row=2;
				foreach($query->result() as $row) {
						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{

						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

							$overdue['type'][]=1;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;

						}
				
				}

				}

				// PAYMENT DUE IN 10 DAYS

				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.agent',$salesuser->user_id)
						  ->where('a.payment',0)
						  ->where('b.customer_id',$customers_data)

						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{
					$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

						$overdue['type'][]=2;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;

				}
			}

		}


		
			$excel_row=2;
		
			
			$totalrow=count($overdue['type']);
			for($t=0;$t<count($overdue['type']);$t++)
			{
				
					//echo $excel_row; exit;
				
				$excel_row=$excel_row;

				
				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,'');
			
				
					$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $overdue['customer'][$t]);
				

				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $overdue['invoice'][$t]);

				if($overdue['type'][$t]==1){

					$odue=$overdue['due'][$t];
					$pdue=0;

				}else
				{
					$pdue=$overdue['due'][$t];
					$odue=0;
				}
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $odue);

				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $pdue);

				$excel_row++;
				$r=$excel_row;

				

		
		
		}


		
		}

		





$k++;
}

}



				$object->setActiveSheetIndex(0);
			// 	$fileName = 'Customer_Payments-'.date('d-M-Y').'.xls'; 
			// //$fileName = 'New Test'.date('d-M-Y').'.xls'; 
			// $savepath=SITE_ROOT.'/customer_payments/'.$fileName;
			// $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
			// $object_writer->save($savepath);
			//  header("Content-type:application/vnd.ms-excel");
			//  header('Content-Disposition: attachment; filename=' . $fileName);
			// readfile( $savepath );

			$fileName = 'Customer_Payments_'.$salesuser->user_id."_".date('d-M-Y').'.xls'; 
			$savepath=SITE_ROOT.'/customer_payments/for_sales/'.$fileName;
			$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
			$object_writer->save($savepath);


			/** SEND WHATSAPP **/

			$msgbody="Hello ".$salesuser->first_name." ".$salesuser->last_name."\n\n";
			$msgbody.="Please find *Payment Overdue/Payment Due (10 Days)* Details for the day ".date('d-M-Y')."\n\n";
			$msgbody.="Team Sunder Industrial Oil";

			$file_url = site_http_root."/customer_payments/for_sales/".$fileName;
			//echo $file_url; exit;
		$extra='';
		if($flag==1)
		{
			if($this->input->post('mobile')<>''){
			$extra=",".$this->input->post('mobile');
			}
		}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			//'receiverMobileNo' => '918447031736'.$extra,
			'receiverMobileNo' =>$salesuser->contact_no.$extra,
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($msgbody),
			'filePathUrl' => $file_url		
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			 //echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			/** END **/

		


}


} }

		if($flag==1)
				{

					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Reminder Sent</div>');
					redirect(page_url.'Leads/triggers');
				}

}




	function attendence_for_previous_day_the_day()
	{
		$sms='';
		
		$day=date('Y-m-d');
		$dt1 = strtotime($day);
		$dt2 = date("l", $dt1);
		$dt3 = strtolower($dt2);



		if(strtolower($dt3)<>"sun")
		{
		$reste=$this->db->select('a.employee_id,a.morning_time,evening_time,b.first_name,b.last_name')->from('mark_your_attendance a')->join('system_users b','a.employee_id=b.user_id')->where('b.user_role_id!=',1)->where('a.attendance_date',$day)->where('a.absent_status!=',1)->get();
		if($reste->num_rows()>0)
		{
			$sms.='Dear Balwinder/Karanbir Sir'."\n\n";
			$sms.='Please find Employees Log In-Out Details for *'.date('d-m-Y',strtotime($day)).'*'."\n\n";

			$i=1;
			foreach($reste->result() as $row)
			{
				if($row->evening_time=="00:00:00")
				{
					$eve="NA";
				}else
				{
					$eve=date('H:i',strtotime($row->evening_time));
				}

			$sms.=$i.") ".ucwords(strtolower($row->first_name." ".$row->last_name))."-*".date('H:i',strtotime($row->morning_time))."*"."-"."*".$eve."*"."\n";



			$i++;

			}



			$reste11=$this->db->select('a.employee_id,a.morning_time,evening_time,b.first_name,b.last_name')->from('mark_your_attendance a')->join('system_users b','a.employee_id=b.user_id')->where('a.attendance_date',$day)->where('b.user_role_id!=',1)->where('a.absent_status',1)->get();
			if($reste11->num_rows()>0)
			{
			$sms.="\n\n"."*Absentee Detail*"."\n";

			$t=1;
			foreach($reste11->result() as $row11)
			{
			
			$sms.=$t.") ".ucwords(strtolower($row11->first_name." ".$row11->last_name))."\n";



			$t++;

			}

			}


			$sms.="\nThank You"."\n";

			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '9891941007,9891941001,918447031736',
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($sms));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);

		}

		}
	
	}


	 function check_for_previous_payment($customer_id,$current_order_id)
  {
  	$stop=array();
  	$invoice=array();
  	$reste=$this->db->select('a.id,a.credit_days,a.send_to_tally_On as added_on,a.invoice_no')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->where('a.payment',0)->where('a.id!=',$current_order_id)->where('b.customer_id',$customer_id)->where('send_to_tally',1)->where('a.cancelled',0)->get();
  	if($reste->num_rows()>0)
  	{
  		foreach($reste->result() as $row)
  		{
  			$credit_days=$row->credit_days;
  			$billed_date=date('Y-m-d',strtotime($row->added_on));
  			$finalpayabledate=date('Y-m-d',strtotime($billed_date. '+'.$credit_days.' Days'));
  			$current_date=date('Y-m-d');
  			if(strtotime($current_date)>strtotime($finalpayabledate))
  			{
  				$stop[]=1;
  				$invoice[]=$row->invoice_no;

  			}


  		}

  	}


  	$in='';
  	if(count($invoice)>0)
  	{
  		$in=implode(',',$invoice);
  	}


  	return array_sum($stop)."|".$in;

  }


function send_daily_work_report()
{

	$d=date('H:i');
	if($d=="22:00")
	{
	/** HIT DAILY REPORT URL **/ 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,page_url."Pdfexample/daily_reports/".date('Y-m-d'));  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);
        /** END **/


	$curr=date('d-m-Y');
	$loc=page_url1.'/image_bank/daily_reports';
	$file_name="CRM_Daily_Report_".date('d-m-Y').".pdf";
	$smsmessage="Hello Sir,\nPlease find *CRM report for the day (".date('d-M-Y').")*\n\nGood Night 😴";
	$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							'receiverMobileNo' => '9891941007,9891941001,918447031736',
							//'receiverMobileNo' => '8447031736',
							'username' => whatsappuser,
							'password' => whatsapppass,
							'message'=>strip_tags($smsmessage),
							'filePathUrl'=>$loc."/".$file_name);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$result = curl_exec($ch);
							//echo $result; exit;
							if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
							}
							curl_close($ch);
			}

}


	function send_payment_excel()
	{
	// 	$todays_date = date('Y-m-d');
	// $data=array('runningtime'=>date('Y-m-d H:i:s'));
	// $this->db->insert('dashboardcronstatus',$data);
	
		$d=date('H:i'); 
		if($d=="10:00")
		{
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_USERAGENT   => "Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0",
			CURLOPT_URL => page_url.'Dbbackup/customer_payment_reminders_to_admin_new',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER=>false,
			CURLOPT_CUSTOMREQUEST => "GET",
			));
			$response = curl_exec($curl);
			$error = curl_error($curl);
			curl_close($curl);
		}

	}

	function getusername($cust)
	{	$un='';
		$u=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$cust)->get();
		if($u->num_rows()>0)
		{
			foreach($u->result() as $unn);
			$un=$unn->first_name." ".$unn->last_name;
		}

		return $un;

	}



	function customer_payment_reminders_to_salesAgent()
	{


		
		$flag=$this->uri->segment(3);
		$flag1=$this->uri->segment(4);
		$user_id=$this->uri->segment(5);
		//echo $user_id; exit;
		//$user_id=29;

		
		$currentday=date('Y-m-d');
		$this->load->library("Excel");
		$object = new PHPExcel();

		$sql = $this->db->select('id,companyname')
						->from('store_rack_location')
						// ->where('id',3)
						->order_by('sort','ASC')
						->get();

		$i=0;
		

		if($sql->num_rows() > 0) {
			
	    	foreach($sql->result() as $rowcompany) {

	    		$customers=array();
		$objWorkSheet=$object->createSheet($i);
		$objWorkSheet->setTitle($this->clean($rowcompany->companyname));
	
	    	
		$table_columns = array("S.No.","Customer Name","Invoices","Payment Overdue","Payment Due in 10 Days",'Total Payment Due','Do Not Follow Customer','Do Not Follow By','Invoice Date');
		$column = 0;
		$objWorkSheet->getStyle("A1:I1")->getFont()->setBold(true);
		$object->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);

		$style_cell = array( 'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, ) );

		$objWorkSheet->getStyle("A1:I1")->applyFromArray(
					$style_cell
				
					);

		$object
    ->getActiveSheet()
    ->getStyle('A1:I1')
    ->getFill()
    ->getStartColor()
    ->setRGB('FFDBE2F1');
	



		for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
					$objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
				}
	    		
		foreach ($table_columns as $field) {
		$objWorkSheet->setCellValueByColumnAndRow($column, 1, $field);
		$column++;
		}



			 $this->db->select('a.unfollow_customer,a.unfollow_added_by,b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  ->where('a.cancelled',0)
						  //->where('a.unfollow_customer',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.hpcl_billing_company',$rowcompany->id);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{


							$customers[]=$row->customer_id;

						}

					}

				}


		
				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  ->where('a.cancelled',0)
						 // ->where('a.unfollow_customer',0)
						  // ->where('a.invoice_no',848)
						  ->where('a.hpcl_billing_company',$rowcompany->id)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


		
			if($query->num_rows() > 0) {
			
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
					 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
					{
						$customers[]=$row->customer_id;
					}

				}


			}

		

				
		//	echo "<pre>"; print_r($customers); exit;

			$overdue = array();
		if(count($customers)>0)
		{
			$unique_cust=array_unique($customers);


			$k=0;
			$r=0;
			foreach($unique_cust as $customers_data)
			{


			$overdue_data=array();
				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  // ->where('a.unfollow_customer',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.cancelled',0)
						  ->where('b.customer_id',$customers_data);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


				if($query->num_rows() > 0) {
					$l=1;
					
				$excel_row=2;
				foreach($query->result() as $row) {
						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{

							/** UNFOLLOW DATA **/
							if($row->unfollow_customer==1)
							{
								$unfname=$this->getusername($row->unfollow_added_by);
								$unfollow="Yes";
							}else{
								$unfname='';
								$unfollow='';
							}

						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

							$overdue['type'][]=1;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;
						$overdue['unfollow'][]=$unfollow;
						$overdue['unfollowBy'][]=$unfname;
						$overdue['send_to_tally_On'][]=date('d-M-Y',strtotime($row->send_to_tally_On));

						}
				
				}

				}

				// PAYMENT DUE IN 10 DAYS

				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  ->where('a.cancelled',0)
						  // ->where('a.unfollow_customer',0)
						  ->where('b.customer_id',$customers_data)
						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{
					/** UNFOLLOW DATA **/
							if($row->unfollow_customer==1)
							{
								$unfname=$this->getusername($row->unfollow_added_by);
								$unfollow="Yes";
							}else{
								$unfname='';
								$unfollow='';
							}

					$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

						$overdue['type'][]=2;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;
						$overdue['unfollow'][]=$unfollow;
						$overdue['unfollowBy'][]=$unfname;
						$overdue['send_to_tally_On'][]=date('d-M-Y',strtotime($row->send_to_tally_On));

				}
			}

		}


		

			$excel_row=2;
		
			
			$totalrow=count($overdue['type']);
			$cust='';
			$d=array();
			$cust=0;
			$overdue_sum=array();
			$overdue_sum[]=0;
			$due_sum=array();
			$due_sum[]=0;
			$start=2;
			$st=2;
			for($t=0;$t<count($overdue['type']);$t++)
			{

				
				
				
				if($t<>0 && $cust!=$overdue['customer'][$t])
				{
				
			
				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,array_sum($overdue_sum));
				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,array_sum($due_sum));
				$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,'');
				

				$object->getActiveSheet()->mergeCells('F'.$st.':F'.$excel_row);

				$payment_sum=array_sum($overdue_sum)+array_sum($due_sum);
				$object->getActiveSheet()->getCell('F'.$st)->setValue($payment_sum);
				$object->getActiveSheet()->getStyle('F'.$st)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$object->getActiveSheet()->getStyle('F'.$st)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

					$styleArray = array(
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
  );

					$objWorkSheet->getStyle("A".$st.":I".$excel_row)->applyFromArray(
				$styleArray );

					
				$style_cell = array( 'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, ) );

				$objWorkSheet->getStyle("A".$st.":I".$excel_row)->applyFromArray(
				$style_cell );


				$excel_row=$excel_row+4;
				
				
					$overdue_sum=array();
					$overdue_sum[]=0;
					$due_sum=array();
					$due_sum[]=0;
				}
				

				
				


				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,'');
			
				
						if($cust!=$overdue['customer'][$t])
						{
						$start=$excel_row;
						$st=$start;
						}

					$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $overdue['customer'][$t]);
				

				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $overdue['invoice'][$t]);

				if($overdue['type'][$t]==1){

					$odue=$overdue['due'][$t];
					$pdue=0;

				}else
				{
					$pdue=$overdue['due'][$t];
					$odue=0;
				}
				$overdue_sum[]=$odue;
				$due_sum[]=$pdue;
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $odue);

				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $pdue);
				$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $overdue['unfollow'][$t]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $overdue['unfollowBy'][$t]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $overdue['send_to_tally_On'][$t]);

				

				
			$cust=$overdue['customer'][$t];

			$excel_row++;

			
		}


		
		} 



$k++;
}

}



				$object->setActiveSheetIndex(0);
				$fileName = "CustomerPayments_".date('d-M-Y')."_".$user_id.".xls"; 
				

				if($flag1==1)
	{
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=".$fileName);
	header("Pragma: no-cache");
	header("Expires: 0");
	}



			//$fileName = 'New Test'.date('d-M-Y').'.xls'; 
			$savepath=SITE_ROOT.'/customer_payments/'.$fileName;
			$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
			$object_writer->save($savepath);
			$url=page_url1.'customer_payments/'.$fileName;
			if($flag1==1)
			{

				$object_writer->save('php://output');

			}
			

			/** SEND WHATSAPP **/
			if($flag1=='')
			{

			$msgbody="Hello\n\n";
			$msgbody.="Please find *Payment Overdue/Payment Due (10 Days)* Details for the day ".date('d-M-Y')."\n\n";
			$msgbody.="Team Sunder Industrial Oil";

			$file_url = site_http_root."/customer_payments/Customer_Payments_".date('d-M-Y')."_".$user_id.".xls";
		
		$extra='';
		if($flag==1)
		{
			if($this->input->post('mobile')<>''){
			$extra=",".$this->input->post('mobile');
			}
		}

		//echo $msgbody; exit;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			//'receiverMobileNo' => '918447031736,919891941007,919891941001,919953139281'.$extra,
			'receiverMobileNo' => '918447031736',
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($msgbody),
			'filePathUrl' => $file_url		
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);
		}
			/** END **/

			if($flag==1)
				{

					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Reminder Sent</div>');
					redirect(page_url.'Leads/triggers');
				}


}
}


function trail_reminder()
{
	$res=$this->db->select('user_id,first_name,last_name,contact_number')->from('system_users')->where('department_id',7)->where('user_id',30)->get();
	if($res->num_rows()>0)
	{
		foreach($res->result() as $row)
		{
	$restey=$this->db->select('b.assignedperson,a.trail_id,a.visitschedule,c.instruments_name,d.company_name')->from('old_customer_visit_schedule a')->join('trial_to_be_sent b','a.trail_id=b.id')->join('presto_instruments c','b.lead_product_id=c.id')->join('customer_detail d','d.id=customer_id')->where('a.visitschedule',date('Y-m-d'))->where('b.assignedperson',$row->user_id)->group_by('a.trail_id')->get();
	if($restey->num_rows()>0)
	{
	$msg="Hello ".$row->first_name." ".$row->last_name." \n\n";
	$msg.="You have following trial scheduled for today. Please update the readings by end of the day.\n\n";
	foreach($restey->result() as $resty)
	{
	$msg.="*".strtoupper(trim($resty->company_name))."*"."\n";
	$msg.="*".strtoupper(trim($resty->instruments_name))."*"."\n";
	$msg.="\n\n";
	}
	$msg.="Thank You";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	$post = array(
	'receiverMobileNo' => $row->contact_number.",8447031736",
	//'receiverMobileNo' => '8447031736',
	'username' => whatsappuser,
	'password' => whatsapppass,
	'message'=>strip_tags($msg));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch);
	//echo $result; exit;
	if (curl_errno($ch)) {
	echo 'Error:' . curl_error($ch);
	}
	curl_close($ch);


}
	}

}
}

function calculate_costprice()
{
	$rate=$this->gettransport_drum_rate();
	if(count($rate)>0)
	{
		$transport=$rate[0];
		$drum=$rate[1];
	}else
	{
		$transport=0;
		$drum=0;
	}
	$rest=$this->db->select('id,pack_size,volume')->from('presto_instruments')->where('status',1)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $rowss)
		{

			$rest=$this->db->select('a.rate,a.bulkproducttype')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->where('a.product',$rowss->id)->order_by('b.currentdate','DESC')->limit(1)->get();
			if($rest->num_rows()>0)
			{

				foreach($rest->result() as $rowdata)
				$billing_price=$rowdata->rate;
				$cr_note=$this->check_for_approval_CRNOTE($rowss->id);

				$finalbprice=$billing_price-$cr_note;
				$finalbprice=$finalbprice-$transport;
				if($rowss->pack_size=='BULK')
				{
					$finalcp=$finalbprice;
				}else
				{
					$finalcp=$finalbprice+$drum;
				}

				$dd=array('product_id'=>$rowss->id,'billing_price'=>$billing_price,'vli'=>$cr_note,'drum'=>$drum,'transport'=>$transport,'addedOn'=>date('Y-m-d h:i:s'),'costprice'=>$finalcp);
				$this->db->insert('presto_instruments_cp',$dd);

				

			$msp=$finalcp;
			$mmmvalue=15/100;
			$margin=$msp+($msp*$mmmvalue);
			$mtype=1;
			$mValues=15;

			$data=array('discount_price'=>$margin,'cp_updatedOn'=>date('Y-m-d'));
				$this->db->where('id',$rowss->id);
				$this->db->update('presto_instruments',$data);

			$drytey=array('product_id'=>$rowss->id,'margintype'=>$mtype,'marginvalue'=>$mValues,'costprice'=>$msp,'msp'=>$margin,'addedOn'=>date('Y-m-d H:i:s'));
			$this->db->insert('presto_instruments_margin_sheet',$drytey);



			}


		}

	}
}

	function check_for_approval_CRNOTE($product_id)
	{
		$vli=0;
		$rest=$this->db->select('a.credit_vli')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->where('a.product_id',$product_id)->order_by('current_date','DESC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rr);
			$vli=$rr->credit_vli;
		}

		return $vli;

	}

	function gettransport_drum_rate()
	{
		$d=array();
		$res=$this->db->select('cost')->from('drum_transportation_cost')->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $ress)
			{
			$d[]=$ress->cost;
		
			}
		}

		return $d;
	}

	function send_agent_payment_notification()
	{
		$qq  = $this->db->select('user_id')->from('system_users_view')->where('user_status',1)->where('department_id',6)->order_by('first_name','ASC')->get();
	if($qq->num_rows()>0)
	{
			foreach($qq->result() as $row)
			{

				$ch = curl_init();
				$timeout = 60;
				$url=page_url."Dbbackup/customer_payment_reminders_to_salesAgent///".$row->user_id;
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$data = curl_exec($ch);
				echo $data; exit;
				curl_close($ch);

			}

		}

	}



	function agent_wise_customer_pending_report_to_agent()
	{
		
		$currentday=date('Y-m-d');
		$this->load->library("Excel");


			$qq  = $this->db->select('user_id, first_name, last_name, contact_number')->from('system_users_view')->where('user_status',1)->where('department_id',6)->get();
	if($qq->num_rows()>0)
	{
			foreach($qq->result() as $salesuser)
			{
			$user_id=$salesuser->user_id;
		$object = new PHPExcel();

		$sql = $this->db->select('id,companyname')
						->from('store_rack_location')
						// ->where('id',3)
						->order_by('sort','ASC')
						->get();

		$i=0;
		

		if($sql->num_rows() > 0) {
			
	    	foreach($sql->result() as $rowcompany) {

	    		$customers=array();
		$objWorkSheet=$object->createSheet($i);
		$objWorkSheet->setTitle($this->clean($rowcompany->companyname));
	
	    	
		$table_columns = array("S.No.","Customer Name","Invoices","Payment Overdue","Payment Due in 10 Days",'Total Payment Due','Do Not Follow Customer','Do Not Follow By','Invoice Date');
		$column = 0;
		$objWorkSheet->getStyle("A1:I1")->getFont()->setBold(true);
		$object->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);
		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);

		$style_cell = array( 'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, ) );

		$objWorkSheet->getStyle("A1:I1")->applyFromArray(
					$style_cell
				
					);

		$object
    ->getActiveSheet()
    ->getStyle('A1:I1')
    ->getFill()
    ->getStartColor()
    ->setRGB('FFDBE2F1');
	



		for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
					$objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
				}
	    		
		foreach ($table_columns as $field) {
		$objWorkSheet->setCellValueByColumnAndRow($column, 1, $field);
		$column++;
		}



			 $this->db->select('a.unfollow_customer,a.unfollow_added_by,b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  ->where('a.cancelled',0)
						  //->where('a.unfollow_customer',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.hpcl_billing_company',$rowcompany->id);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{


							$customers[]=$row->customer_id;

						}

					}

				}


		
				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,b.customer_id,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  ->where('a.cancelled',0)
						 // ->where('a.unfollow_customer',0)
						  // ->where('a.invoice_no',848)
						  ->where('a.hpcl_billing_company',$rowcompany->id)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


		
			if($query->num_rows() > 0) {
			
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
					 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
					{
						$customers[]=$row->customer_id;
					}

				}


			}

		

				
		//	echo "<pre>"; print_r($customers); exit;

			$overdue = array();
		if(count($customers)>0)
		{
			$unique_cust=array_unique($customers);


			$k=0;
			$r=0;
			foreach($unique_cust as $customers_data)
			{


			$overdue_data=array();
				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  // ->where('a.unfollow_customer',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.cancelled',0)
						  ->where('b.customer_id',$customers_data);
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


				if($query->num_rows() > 0) {
					$l=1;
					
				$excel_row=2;
				foreach($query->result() as $row) {
						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{

							/** UNFOLLOW DATA **/
							if($row->unfollow_customer==1)
							{
								$unfname=$this->getusername($row->unfollow_added_by);
								$unfollow="Yes";
							}else{
								$unfname='';
								$unfollow='';
							}

						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

							$overdue['type'][]=1;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;
						$overdue['unfollow'][]=$unfollow;
						$overdue['unfollowBy'][]=$unfname;
						$overdue['send_to_tally_On'][]=date('d-M-Y',strtotime($row->send_to_tally_On));

						}
				
				}

				}

				// PAYMENT DUE IN 10 DAYS

				// CUSTOMER PAYMENT OVER DUE
		 $this->db->select('a.unfollow_customer,a.unfollow_added_by,a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.agent',$user_id)
						  ->where('a.cancelled',0)
						  // ->where('a.unfollow_customer',0)
						  ->where('b.customer_id',$customers_data)
						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);


						
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				
				foreach($query->result() as $row) {

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{
					/** UNFOLLOW DATA **/
							if($row->unfollow_customer==1)
							{
								$unfname=$this->getusername($row->unfollow_added_by);
								$unfollow="Yes";
							}else{
								$unfname='';
								$unfollow='';
							}

					$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$partial=$this->customer_previous_payment($row->id);
						$payment_due=$order_value-$partial;

						$overdue['type'][]=2;
						$overdue['invoice'][]=$row->invoice_no;
						$overdue['due'][]=$payment_due;
						$overdue['customer'][]=$row->company_name;
						$overdue['unfollow'][]=$unfollow;
						$overdue['unfollowBy'][]=$unfname;
						$overdue['send_to_tally_On'][]=date('d-M-Y',strtotime($row->send_to_tally_On));

				}
			}

		}


		

			$excel_row=2;
		
			
			$totalrow=count($overdue['type']);
			$cust='';
			$d=array();
			$cust=0;
			$overdue_sum=array();
			$overdue_sum[]=0;
			$due_sum=array();
			$due_sum[]=0;
			$start=2;
			$st=2;
			for($t=0;$t<count($overdue['type']);$t++)
			{

				
				
				
				if($t<>0 && $cust!=$overdue['customer'][$t])
				{
				
			
				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,array_sum($overdue_sum));
				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,array_sum($due_sum));
				$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row,'');
				$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,'');
				

				$object->getActiveSheet()->mergeCells('F'.$st.':F'.$excel_row);

				$payment_sum=array_sum($overdue_sum)+array_sum($due_sum);
				$object->getActiveSheet()->getCell('F'.$st)->setValue($payment_sum);
				$object->getActiveSheet()->getStyle('F'.$st)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$object->getActiveSheet()->getStyle('F'.$st)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

					$styleArray = array(
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
  );

					$objWorkSheet->getStyle("A".$st.":I".$excel_row)->applyFromArray(
				$styleArray );

					
				$style_cell = array( 'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, ) );

				$objWorkSheet->getStyle("A".$st.":I".$excel_row)->applyFromArray(
				$style_cell );


				$excel_row=$excel_row+4;
				
				
					$overdue_sum=array();
					$overdue_sum[]=0;
					$due_sum=array();
					$due_sum[]=0;
				}
				

				
				


				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,'');
			
				
						if($cust!=$overdue['customer'][$t])
						{
						$start=$excel_row;
						$st=$start;
						}

					$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $overdue['customer'][$t]);
				

				$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $overdue['invoice'][$t]);

				if($overdue['type'][$t]==1){

					$odue=$overdue['due'][$t];
					$pdue=0;

				}else
				{
					$pdue=$overdue['due'][$t];
					$odue=0;
				}
				$overdue_sum[]=$odue;
				$due_sum[]=$pdue;
				$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $odue);

				$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $pdue);
				$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $overdue['unfollow'][$t]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $overdue['unfollowBy'][$t]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $overdue['send_to_tally_On'][$t]);

				

				
			$cust=$overdue['customer'][$t];

			$excel_row++;

			
		}


		
		} 



$k++;
}

}
			$object->setActiveSheetIndex(0);
				$fileName = "Customer_Payments_".date('d-M-Y')."_".$user_id."_agent_wise.xls"; 
			//$fileName = 'New Test'.date('d-M-Y').'.xls'; 
			$savepath=SITE_ROOT.'/customer_payments/'.$fileName;
			$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
			$object_writer->save($savepath);
			$url=page_url1.'customer_payments/'.$fileName;
			
			/** SEND WHATSAPP **/
			$msgbody="Hello ".$salesuser->first_name." ".$salesuser->last_name."\n\n";
			$msgbody.="Please find *Payment Overdue/Payment Due (10 Days)* Details for the day ".date('d-M-Y')."\n\n";
			$msgbody.="Team Sunder Industrial Oil";
			$file_url = site_http_root."/customer_payments/Customer_Payments_".date('d-M-Y')."_".$user_id."_agent_wise.xls";
			//echo $msgbody; exit;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			//'receiverMobileNo' => '918447031736',
			'receiverMobileNo' =>$salesuser->contact_number.',918447031736',
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags($msgbody),
			'filePathUrl' => $file_url		
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);

			/** END **/

			

}

}

}

}


function send_payment_excel_to_agent()
	{
	// 	$todays_date = date('Y-m-d');
	// $data=array('runningtime'=>date('Y-m-d H:i:s'));
	// $this->db->insert('dashboardcronstatus',$data);
	
		$d=date('H:i'); 
		if($d=="09:00")
		{
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_USERAGENT   => "Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0",
			CURLOPT_URL => page_url.'Dbbackup/agent_wise_customer_pending_report_to_agent',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER=>false,
			CURLOPT_CUSTOMREQUEST => "GET",
			));
			$response = curl_exec($curl);
			$error = curl_error($curl);
			curl_close($curl);
		}

	}


	function stock_data_new()
	{
		exit;
		$resty=$this->db->select('product_id,model,stock')->from('sio_stock')->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $row)
			{

				$company=3;
				$product_id=$row->product_id;
				/** INSERT INTO INVENTORY INFOR **/
				$d=array('inventory_particular_id'=>0,'item_id'=>$product_id,'qty'=>$row->stock,'company_id'=>$company,'exhausted'=>0,'balance_left'=>$row->stock,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i'));
				$this->db->insert('company_wise_inventory_info',$d);
				/** ADD COMPANY STOCK **/
				$updated_stock=$row->stock;
				$stdata=array('company_id'=>$company,'itemid'=>$product_id,'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('company_wise_inventory',$stdata);
				/** END **/
			}
		}
	}


	function stock_data_new_gkn()
	{
		exit;
		$resty=$this->db->select('product_id,model,stock')->from('gkn_stock')->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $row)
			{

				$company=1;
				$product_id=$row->product_id;
				/** INSERT INTO INVENTORY INFOR **/
				$d=array('inventory_particular_id'=>0,'item_id'=>$product_id,'qty'=>$row->stock,'company_id'=>$company,'exhausted'=>0,'balance_left'=>$row->stock,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i'));
				$this->db->insert('company_wise_inventory_info',$d);
				/** ADD COMPANY STOCK **/
				$updated_stock=$row->stock;
				$stdata=array('company_id'=>$company,'itemid'=>$product_id,'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('company_wise_inventory',$stdata);
				/** END **/
			}
		}
	}


	function stock_data_new_satin()
	{
		exit;
		$resty=$this->db->select('product_id,model,stock')->from('satin_stock')->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $row)
			{

				$company=2;
				$product_id=$row->product_id;
				/** INSERT INTO INVENTORY INFOR **/
				$d=array('inventory_particular_id'=>0,'item_id'=>$product_id,'qty'=>$row->stock,'company_id'=>$company,'exhausted'=>0,'balance_left'=>$row->stock,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i'));
				$this->db->insert('company_wise_inventory_info',$d);
				/** ADD COMPANY STOCK **/
				$updated_stock=$row->stock;
				$stdata=array('company_id'=>$company,'itemid'=>$product_id,'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('company_wise_inventory',$stdata);
				/** END **/
			}
		}
	}


	function get_product_id($model)
	{
		$product=0;
		$rest=$this->db->select('id')->from('presto_instruments')->where('model_number',$model)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);
			$product=$row->id;
		}

		return $product;

	}



	function auto_cancell_sales_order()
				{
					// 24 hours 
					$restey=$this->db->select('id,sales_order_addedOn,sales_order_no')->from('order_punch')->where('send_so_to_billing',0)->where('cancelled',0)->get();
					if($restey->num_rows()>0)
					{
					foreach($restey->result() as $resteyy)
					{
						$so_addedOn=date('Y-m-d H:i',strtotime($resteyy->sales_order_addedOn));
						$current_time=date('Y-m-d H:i');
						$start_datetime = new DateTime($so_addedOn); 
						$diff = $start_datetime->diff(new DateTime($current_time)); 

						// echo $diff->days.' Days total<br>'; 
						// echo $diff->y.' Years<br>'; 
						// echo $diff->m.' Months<br>'; 
						// echo $diff->d.' Days<br>'; 
						// echo $diff->h.' Hours<br>'; 
						// echo $diff->i.' Minutes<br>'; 
						// echo $diff->s.' Seconds<br>';

						$total_minutes = ($diff->days * 24 * 60); 
						$total_minutes += ($diff->h * 60); 
						$total_minutes += $diff->i; 
						$total_minutes=abs($total_minutes);
						if($total_minutes>1440)
						{

							$order_id=$resteyy->id;
							$cancell_rmk="SO Auto Cancelled By System. SO Created On ".date('d-M-Y H:i',strtotime($so_addedOn))." Cancelled On ".date('d-M-Y H:i');

							$data=array('cancelled'=>1,'cancelledOn'=>date('Y-m-d H:i:s'),'cancel_reason'=>$cancell_rmk,'cancelled_by'=>1);
							$this->db->where('id',$order_id);
							$this->db->update('order_punch',$data);


							
						}


					}
					}

				}

public function sendchecklistreminderonwhatsapp(){

				$msgbody='';
				$date = date('Y-m-d');
				$rest=$this->db->select('user_id,first_name,last_name,contact_number as official_no')->from('system_users')->where('user_status',1)->get();
				if($rest->num_rows()>0)
				{
					foreach($rest->result() as $rowss)
					{
						$a=0;
				$q = $this->db->select('task_id')->from('compliance_set_date')->where('dateforemail',$date)->get();
				if($q->num_rows()>0){
					$td = date('d-M-Y');
					$msgbody= "\n\nDear ".ucwords(strtolower($rowss->first_name." ".$rowss->last_name)).",\n\nPlease find the todays (".$td.") scheduled checklist.\n\n";
					foreach($q->result() as $row){


					$q1 = $this->db->select('a.task, a.complition_time')->from('compliance_task_report a')->where('a.task_id',$row->task_id)->where('a.whatsapp_notification',1)->where('user_id',$rowss->user_id)->get();

					if($q1->num_rows()>0){

						$a=1;
						$i=1;
						foreach($q1->result() as $row1){
						$msgbody.="Task - *".ucfirst(strtolower($row1->task))."*\n";
						$msgbody.='Completion Time - '.date('H:i A',strtotime($row1->complition_time))."\n\n";
						$i++;
						}

					}

					}

				

				
						/* MESSAGE */
						if($a==1)
						{
						$official_no = $rowss->official_no;
						/**WHATSAPP INTEGRATION**/
						// echo $msgbody;exit;
						$ch = curl_init();
						//echo $msgbody; exit;
						curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						$post = array(
						//'receiverMobileNo' => '8447031736',
						'receiverMobileNo' => '91'.$official_no,
						'username' => whatsappuser,
						'password' => whatsapppass,
						'message'=>$msgbody		
						);


						curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
						$result = curl_exec($ch);
						//echo $result; exit;
						if (curl_errno($ch)) {
						echo 'Error:' . curl_error($ch);
						}
						curl_close($ch);
						/* END */
						}


				
				}


				




		}
}
}

function senddfmeetingreminders()
{

	$d=date('H:i');
	if($d=="11:00")
	{
	/** HIT DAILY REPORT URL **/ 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,page_url."Task/dfmeetingreminders/");  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);
        /** END **/

			}

}

function senddailyscheduledtaskreminder()
{
    $todayDate = date('Y-m-d');
    $holi = $this->CheckForHoliday($todayDate);
    $d = date('H:i');

    if ($d === '11:00' && $holi === 0) {
        $q = $this->db->select('a.assigned_user, b.title, b.first_name, b.last_name, b.email, b.contact_number')
                      ->from('task_department_wise_scheduling a')
                      ->join('system_users b', 'a.assigned_user = b.user_id')
                      ->where('b.user_status', 1)
                      ->where('a.end_date', $todayDate)
                      ->where('a.assigned_user !=', 0)
                      ->group_by('a.assigned_user')
                      ->get();

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                // Check if PDF generation is required
                $hasTasks = $this->task->yourtodaysduetaskreminderpdf($row->assigned_user);

                if (!$hasTasks) {
                    continue; // Skip if no tasks are found
                }

                $mobileno = $row->contact_number;
                $username = ucwords(strtolower($row->title . " " . $row->first_name . " " . $row->last_name));
                $emailid = $row->email;
                $fullname = str_replace(' ', '-', ucwords(strtolower($username)));
                $loc = page_url1 . '/image_bank/daily_reports';
                $file_name = $fullname . "_" . $row->assigned_user . "_Today_Scheduled_Task" . date('Y-m-d') . ".pdf";

                $smsmessage = 'Hello ' . $username . ',

Hope you are doing well! 🌟

Please find today\'s scheduled task list in the attachment. If you have any questions or need further assistance, please feel free to connect with your HOD!

Best regards,
Shubham Flexible Packaging';

                // WhatsApp Trigger
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                $post = [
                    'receiverMobileNo' => '91' . $mobileno,
                    'username' => whatsappuser1,
                    'password' => whatsapppass1,
                    'message' => strip_tags($smsmessage),
                    'filePathUrl' => $loc . "/" . $file_name
                ];
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);

                // Email Trigger
                $subjectname = "Kind Attention! " . $username . " Your Today's Scheduled Task List Attached.";
                $this->email->set_mailtype("html");
                $this->email->to($emailid);
                $this->email->from('taskmanagement@shubhampack.com');
                $this->email->subject($subjectname);
                $this->email->attach($loc . "/" . $file_name);
                $this->email->send();
                $this->email->clear(TRUE);
            }
        }
    }
}


function overduetaskreportsharewithusers()
{
    $todayDate = date('Y-m-d');
    $todayDisplay = date('d M Y');
    $holi = $this->CheckForHoliday($todayDate);
    $currentTime = date('H:i');

    if ($currentTime == '10:00' && $holi == 0) {
        $usersWithTasks = $this->db->select('a.assigned_user, b.title, b.first_name, b.last_name, b.email, b.contact_number')
            ->from('task_department_wise_scheduling a')
            ->join('system_users b', 'a.assigned_user = b.user_id')
            ->join('df_release m', 'a.df_id = m.id', 'left')
            ->where('b.user_status', 1)
            ->where('a.task_status', 0)
            ->where('a.on_hold', 0)
            ->where('a.end_date <', $todayDate)
            ->where('a.assigned_user !=', 0)
            ->group_start()
                ->where('m.df_status', 0)
                ->or_where('m.df_status', 'running')
                ->or_where('m.df_status IS NULL', null, false)
            ->group_end()
            ->group_by('a.assigned_user')
            ->get();

        if ($usersWithTasks->num_rows() > 0) {
            foreach ($usersWithTasks->result() as $user) {
                $reportData = $this->task->overduetaskreportsuserwise($user->assigned_user);
                if (!$reportData || empty($reportData['attachment_path'])) {
                    continue;
                }

                $username = !empty($reportData['employee_name'])
                    ? $reportData['employee_name']
                    : ucwords(strtolower(trim($user->title . ' ' . $user->first_name . ' ' . $user->last_name)));
                $firstName = ucwords(strtolower(trim((string) $user->first_name)));
                $mobileno = $this->normalizeMobileNumber($user->contact_number);

                $smsMessage = "Hello {$username},\n\nYour overdue task report for {$todayDisplay} is ready.\nPending overdue tasks: {$reportData['task_count']}\nMaximum delay: {$reportData['max_delay_days']} day(s)\n\nPlease review the attached PDF and close the pending tasks as soon as possible.\n\nRegards,\nShubham Pack PMS";
                if ($mobileno !== '') {
                    $this->sendSmsNotification($mobileno, $smsMessage, $reportData['public_url']);
                }

                if (!empty($user->email)) {
                    $emailSubject = 'Overdue Task Report | ' . date('d M Y') . ' | ' . $username;
                    $emailBodyMessage = $this->buildOverdueTaskEmailBody([
                        'employee_name' => $username,
                        'first_name' => ($firstName !== '' ? $firstName : $username),
                        'task_count' => $reportData['task_count'],
                        'max_delay_days' => $reportData['max_delay_days'],
                        'oldest_due_date' => $reportData['oldest_due_date'],
                        'generated_on' => $reportData['generated_on'],
                    ]);

                    $this->sendEmail($user->email, '', $reportData['attachment_path'], $emailBodyMessage, $emailSubject);
                }
            }
        }
    }
}


private function sendSmsNotification($mobileNo, $message, $filePath)
{
    if (empty($mobileNo)) {
        return false;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $post = [
        'receiverMobileNo' => '91' . $mobileNo,
        'username' => whatsappuser1,
        'password' => whatsapppass1,
        'message' => strip_tags($message),
        'filePathUrl' => $filePath
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

private function sendEmail($toEmail, $ccEmail, $attachment, $message, $subject)
{
    if (empty($toEmail) || empty($subject) || empty($message)) {
        return false;
    }

    $this->email->clear(TRUE);
    $this->email->set_mailtype("html");
    $this->email->to($toEmail);
    if (!empty($ccEmail)) {
        $this->email->cc($ccEmail . ',shubham@shubhampack.com');
    }
    $this->email->bcc('mangleshup@gmail.com');
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack PMS');
    $this->email->reply_to('taskmanagement@shubhampack.com', 'Shubham Pack PMS');
    $this->email->subject($subject);
    $this->email->message($message);
    if (!empty($attachment) && file_exists($attachment)) {
        $this->email->attach($attachment);
    }
    $result = $this->email->send();
    $this->email->clear(TRUE);
    return $result;
}

private function buildOverdueTaskEmailBody($data)
{
    return $this->load->view('reports/overdue_task_report_email', $data, true);
}

private function normalizeMobileNumber($mobileNo)
{
    $mobileNo = preg_replace('/\D+/', '', (string) $mobileNo);
    if (strpos($mobileNo, '91') === 0 && strlen($mobileNo) > 10) {
        $mobileNo = substr($mobileNo, -10);
    }

    return $mobileNo;
}



function CheckForHoliday($dat)
{
		$q5 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$dat)->get();
		return $q5->num_rows();
}


function login_report()
{
$todayDate=date('Y-m-d');
$holi=$this->CheckForHoliday($todayDate);
$d = date('H:i');
if($d=='19:30' && $holi==0){
$d=date('Y-m-d', strtotime('-4 days'));
$this->db->select('a.first_name, a.last_name, a.user_id, MAX(b.login_date) AS latest_login_date');
$this->db->from('system_users a');
$this->db->join('user_login_ip_tracking b', 'a.user_id = b.user_id', 'left');
$this->db->where('a.business_location', 2);
$this->db->where('a.user_status', 1);
$this->db->where('a.hide_profile', 0);
$this->db->group_by('a.user_id');
$this->db->having('(MAX(b.login_date) < DATE_SUB(CURDATE(), INTERVAL 4 DAY) OR MAX(b.login_date) IS NULL)', NULL, FALSE);
$this->db->where_not_in('DATE(b.login_date)', "SELECT holiday_date FROM prestogroup_holidays WHERE holiday_date BETWEEN CURDATE() - INTERVAL 4 DAY AND CURDATE()");


$query = $this->db->get();
$data=array();
if($query->num_rows()>0)
{

	foreach($query->result() as $row)
	{
		$data[]=array('name'=>$row->first_name." ".$row->last_name,'user_id'=>$row->user_id,'login_date'=>$row->latest_login_date);
	}

}

// Extract the 'login_date' column from the array
$loginDates = array_column($data, 'login_date');
// Sort the original array based on 'login_date'
array_multisort($loginDates, SORT_ASC, $data);
//echo "<pre>"; print_r($data); exit;
$msg='';
if(count($data))
{
	$r=1;
	$msg.="Hello Sir\n\n";
	$msg.="Please find users who haven't logged into the PMS for the past 4 days with their last login date.\n\n";
	foreach($data as $roww)
	{
		if($roww['user_id']<>61)
		{
		$msg.=ucwords(strtolower($roww['name']))."\n";
		$msg.=date('d-m-Y',strtotime($roww['login_date']))."\n\n";
		$r++;
		}
	}
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$post = array(
'receiverMobileNo' => '8130192001,8447031736',
//'receiverMobileNo' => '8447031736',
 'username' => whatsappuser,
'password' => whatsapppass,
//'username' => 'sdsrbh5',
//'password' =>'Saurabh@8196',
'message'=>strip_tags($msg)		
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$result = curl_exec($ch);
//echo $result; exit;
if (curl_errno($ch)) {
echo 'Error:' . curl_error($ch);
}
curl_close($ch);


// /** RISHI **/
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$post = array(
'receiverMobileNo' => '8447031736,8130192020',
//'receiverMobileNo' => '8447031736',
// 'username' => whatsappuser,
// 'password' => whatsapppass,
'username' => 'sdsrbh5',
'password' =>'Saurabh@8196',
'message'=>strip_tags($msg)		
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$result = curl_exec($ch);
//echo $result; exit;
if (curl_errno($ch)) {
echo 'Error:' . curl_error($ch);
}
curl_close($ch);

}

}

function calculatesalevalue() {
    date_default_timezone_set("Asia/Kolkata");

    // Set month and year for November 2024
    $month = date('m');
    $year = date('Y');
   	$monthname =  date('F');

    // Get the first and last day of November 2024
    $start_date = new DateTime("$year-$month-01");
    $end_date = new DateTime("$year-$month-" . $start_date->format('t')); // 't' gives last day of the month
   // echo "<pre>"; print_r($end_date); exit;
    // Format for MySQL datetime
    $startdate = $start_date->format('Y-m-d');
    $enddate = $end_date->format('Y-m-d');
    $startdateofthemonth = $start_date->format('Y-m-d');
    $tilldateorder = [0]; // Initialize array with 0 to avoid empty sum

    // Fetch order values from `poreceived` table
    $query = $this->db
        ->select('a.order_value')
        ->from('poreceived a')
        ->join('task_department_wise_scheduling b', 'a.id = b.po_id', 'left')
        ->where('b.taskid', 103)
        //->where('b.task_status', 1)
        ->where('b.end_date >=', $startdate)
        ->where('b.end_date <=', $enddate)
        ->get();

        //echo "<pre>"; print_r($query->result()); exit;

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {
            $tilldateorder[] = $row->order_value;
        }
    }

    // Calculate total order value
    $totalorderval = array_sum($tilldateorder);
    //echo $totalorderval; exit;

    $q = $this->db->select('id')->from('monthly_sale_stats')->where('month_no',$month)->where('year',$year)->get();
    if($q->num_rows()>0){


    // Update the `monthly_sale_stats` table for November 2024
    $this->db->where('month_no', $month);
    $this->db->where('year', $year); // Ensure you have a 'year' column in `monthly_sale_stats`
    $this->db->update('monthly_sale_stats', ['order_value' => $totalorderval]);
}else{
	$data = array('month_no'=>$month,
'month'=>$monthname,
'order_value'=>$totalorderval,
'month_date'=>$startdateofthemonth,
'year'=>date('Y'));
	$this->db->insert('monthly_sale_stats',$data);
	//echo "<pre>"; print_r($data); exit;

}


}


public function sendMissedFollowupReport() {
    $time = date('H:i');
    if ($time == '09:00') {

        // Fetch all active users in the marketing department
        $users_query = $this->db->select('email, user_id')
                                ->from('system_users')
                                ->where('department_id', 9)
                                ->where('user_status', 1) // Active users only
                                ->get();

        if ($users_query->num_rows() > 0) {
            $users = $users_query->result();

            // Iterate through each user to fetch their missed follow-ups
            foreach ($users as $user) {
                $query = $this->db->query("
                    SELECT 
                        a.lead_id, 
                        b.create_date,
                        b.company_name, 
                        a.next_follow_date, 
                        a.remarks, 
                        b.added_by AS leadmanager, 
                        c.company_name AS mastercompanyname,
                        c.customer_name, 
                        c.contact_no
                    FROM progress_remarks a
                    JOIN leads b ON a.lead_id = b.id
                    LEFT JOIN customer_detail c ON c.id = b.company_name
                    WHERE a.id IN (
                        -- Get the latest status for each lead
                        SELECT MAX(id)
                        FROM progress_remarks
                        WHERE next_follow_date IS NOT NULL 
                            AND next_follow_date <> '0000-00-00' 
                            AND next_follow_date <> '1970-01-01'
                            AND added_by <> 139 
                        GROUP BY lead_id
                    )
                    AND a.next_follow_date < '" . date('Y-m-d') . "'
                    AND a.next_follow_date <> '0000-00-00'
                    AND a.next_follow_date <> '1970-01-01'
                    AND a.lead_id NOT IN (
                        -- Exclude leads that ever had lead_status 34, 35, or 36
                        SELECT DISTINCT lead_id 
                        FROM progress_remarks 
                        WHERE CAST(lead_status AS UNSIGNED) IN (34, 35, 36)
                    )
                    AND a.added_by <> 139 
                    AND b.added_by = {$user->user_id}
                    ORDER BY a.next_follow_date ASC
                ");

                if ($query->num_rows() > 0) {
                    $data = $query->result();

                    // Prepare HTML table
                    $html = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">';
                    $html .= '<thead style="background-color: #4872b8; color: #ffffff; text-align: left;">
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Create Date</th>
                                    <th>Customer Name</th>
                                    <th>Company Name</th>
                                    <th>Contact No</th>
                                    <th>Next Follow-Up Date</th>
                                    <th>Last Discussion with Client</th>
                                </tr>
                              </thead>';
                    $html .= '<tbody>';
                    $i = 1;
                    foreach ($data as $row) {
                        $customername = ucwords(strtolower(trim($row->customer_name)));
                        $companyname = ucwords(strtolower(trim($row->mastercompanyname)));
                        $remarks = ucwords(strtolower(trim($row->remarks)));
                        $leadcreatedate = date('d-m-Y', strtotime($row->create_date));
                        $html .= "<tr>
                                    <td>{$i}</td>
                                    <td>{$leadcreatedate}</td>
                                    <td>{$customername}</td>
                                    <td>{$companyname}</td>
                                    <td>{$row->contact_no}</td>
                                    <td style='color: #ff0000;'>" . date('d-M-Y', strtotime($row->next_follow_date)) . "</td>
                                    <td>{$remarks}</td>
                                  </tr>";
                        $i++;
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';

                    // Send email to the respective lead manager
                    $this->load->library('email');
                    $this->email->from('taskmanagement@shubhampack.com', 'Missed Follow-Up Report - PMS');
                    $this->email->to($user->email);
                    $this->email->cc('shubham@shubhampack.com');
                    $this->email->bcc('mangleshup@gmail.com');
                    $this->email->subject('🔴 Missed Follow-Up Report');

                    // Compose email body
                    $email_body = "
                        <div style='font-family: Arial, sans-serif; color: #333333;'>
                            <div style='text-align: center; margin-bottom: 20px;'>
                                <img src='https://pms.shubhampack.in/assets/images/shubhampack.png' alt='Shubham Pack Logo' style='max-width: 150px;'>
                            </div>
                            <p>Dear <strong>{$this->salescrm->getusername($user->user_id)}</strong>,</p>
                            <p>Here is your report of missed follow-ups for your leads:</p>
                            {$html}
                            <p style='margin-top: 20px;'>Please take the necessary actions to ensure timely follow-ups with your leads.</p>
                            <p>Best regards,</p>
                            <p><strong>Shubham Flexible Packaging - PMS</strong></p>
                            <hr style='border: 0; border-top: 1px solid #ddd;'>
                            <p style='font-size: 12px; color: #666;'>This is an automated email. Please do not reply.</p>
                        </div>
                    ";
                    echo $email_body; exit;

                    // $this->email->message($email_body);

                    // if ($this->email->send()) {
                    //     echo "Email sent successfully to {$user->email}!<br>";
                    // } else {
                    //     echo "Failed to send email to {$user->email}. " . $this->email->print_debugger() . "<br>";
                    // }
                } else {
                    echo "No missed follow-ups found for {$user->email}.<br>";
                }
            }
        } else {
            echo "No users found in the marketing department.";
        }
    }
}



public function checkiftaskiscompleted(){
	$time = date('H:i');
	if($time=='19:00'){
	$q = $this->db->select('id, taskupdatedontime')->from('task_department_wise_scheduling')->where('task_completed_on','0000-00-00 00:00:00')->where('task_status',1)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){

			$data = array('task_completed_on'=>$row->taskupdatedontime);
			$this->db->where('id',$row->id);
			$this->db->update('task_department_wise_scheduling',$data);

		}
	}
	}
}


public function checkifmachineisdispatched()
{
    $time = date('H:i');
     if ($time == '19:00') { // Uncomment in production

    // Step 1: Get all df_ids where dispatch task (taskid 103) is marked as done
    $this->db->select('tds.df_id, tds.task_completed_on');
    $this->db->from('task_department_wise_scheduling as tds');
    $this->db->join('df_release as df', 'tds.df_id = df.id');
    $this->db->where('tds.taskid', 103); // Dispatch task
    $this->db->where('tds.task_status', 1); // Marked as done
    $query = $this->db->get();
    $dispatched_dfs = $query->result();

    if (!empty($dispatched_dfs)) {
        foreach ($dispatched_dfs as $row) {
            $df_id = $row->df_id;
            $completed_on = $row->task_completed_on;

            // Step 2: Get each pending task for this DF
            $this->db->select('id, assigned_user');
            $this->db->from('task_department_wise_scheduling');
            $this->db->where('df_id', $df_id);
            $this->db->where('task_status', 0);
            $pending_tasks = $this->db->get()->result();

            // Step 3: Update each task individually
            foreach ($pending_tasks as $task) {
                $this->db->where('id', $task->id);
                $this->db->update('task_department_wise_scheduling', [
                    'task_status' => 1,
                    'task_completed_by' => $task->assigned_user,
                    'task_completed_on' => $completed_on
                ]);
            }
        }
    }

     } 
}





public function sent_department_wise_list(){

	$d=date('H:i');
	if($d=="18:00"){
	 $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,page_url."Formats/department_wise_report");  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);

		$toEmail = "deepesh@shubhampack.com";
		$ccEmail = "virendra@shubhampack.com, ea@shubhampack.com,rishibhatnagar@shubhampack.com, ea.ed@shubhampack.com";
		$bccEmail ="sdsrbh5@gmail.com";

		// $toEmail = "sdsrbh5@gmail.com";
		// $ccEmail = "";
		

		$attachment = page_url1. 'image_bank/daily_reports/overduetask/Department-Overdue-Task-list-' . date('Y-m-d') . '.pdf'; 

		$attachment = $_SERVER['DOCUMENT_ROOT'].'/image_bank/daily_reports/overduetask/Department-Overdue-Task-list-' . date('Y-m-d') . '.pdf';

		// echo $attachment; exit;

		$message ="Hello Sir/Ma'am,<br><br>Attention Please 🚨🚨<br><br>The Department wise overdue report is now ready for review. Please find it attached in PDF format. <br><br>Let's prioritize action on this promptly. Thanks!";

		$subject = "Department Wise Overdue Task List " . date('d-m-Y'); 

		$this->sendEmail($toEmail, $ccEmail, $attachment, $message, $subject);
	}


}


// public function preview_department18_reschedule($df_id = 202)
// {
//     $department_id = 18;

//     $preview = [];

//     $pendingTaskExists = $this->db
//     ->where('df_id',$df_id)
//     ->where('department_id',18)
//     ->where('task_status',0)
//     ->count_all_results('task_department_wise_scheduling');

// if($pendingTaskExists == 0)
// {
//     echo 'All tasks completed. No reschedule required.';
//     exit;
// }


//     /*
//     |--------------------------------------------------------------------------
//     | DF DETAILS
//     |--------------------------------------------------------------------------
//     */
//     $df = $this->db
//         ->select('id, df_no')
//         ->from('df_release')
//         ->where('id', $df_id)
//         ->get()
//         ->row_array();

//     if (empty($df))
//     {
//         echo "DF not found";
//         exit;
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | MASTER TASKS
//     |--------------------------------------------------------------------------
//     */
//     $masterTasks = $this->db
//         ->select('task_id, task_name, tat, tat_start_from')
//         ->from('task_management')
//         ->where('department_id', $department_id)
//         ->where('status', 1)
//         ->get()
//         ->result_array();

//     $taskMap = [];
//     $childrenMap = [];

//     foreach ($masterTasks as $task)
//     {
//         $taskMap[$task['task_id']] = $task;
//         $childrenMap[$task['tat_start_from']][] = $task['task_id'];
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | EXISTING SCHEDULE
//     |--------------------------------------------------------------------------
//     */
//     $existingRows = $this->db
//         ->select("
//             id,
//             taskid,
//             start_date,
//             end_date,
//             task_status,
//             task_completed_on
//         ")
//         ->from('task_department_wise_scheduling')
//         ->where('df_id', $df_id)
//         ->where('department_id', $department_id)
//         ->get()
//         ->result_array();

//     $scheduleMap = [];

//     foreach ($existingRows as $row)
//     {
//         $scheduleMap[$row['taskid']] = $row;
//     }


// /*
// |--------------------------------------------------------------------------
// | HOLIDAYS
// |--------------------------------------------------------------------------
// */
// $holidayRows = $this->db
//     ->select('holiday_date')
//     ->from('prestogroup_holidays')
//     ->get()
//     ->result_array();

// $holidayMap = [];

// foreach($holidayRows as $row)
// {
//     $holidayMap[$row['holiday_date']] = true;
// }

// /*
// |--------------------------------------------------------------------------
// | NEXT WORKING DAY
// |--------------------------------------------------------------------------
// */
// $getNextWorkingDay = function($date) use ($holidayMap)
// {
//     $nextDate = date(
//         'Y-m-d',
//         strtotime($date.' +1 day')
//     );

//     while(
//         date('w', strtotime($nextDate)) == 0 ||
//         isset($holidayMap[$nextDate])
//     )
//     {
//         $nextDate = date(
//             'Y-m-d',
//             strtotime($nextDate.' +1 day')
//         );
//     }

//     return $nextDate;
// };

// /*
// |--------------------------------------------------------------------------
// | ADD WORKING DAYS
// |--------------------------------------------------------------------------
// */
// $addWorkingDays = function($startDate,$tat) use ($holidayMap)
// {
//     $currentDate = $startDate;

//     if($tat <= 1)
//     {
//         return $currentDate;
//     }

//     $workingDays = 1;

//     while($workingDays < $tat)
//     {
//         $currentDate = date(
//             'Y-m-d',
//             strtotime($currentDate.' +1 day')
//         );

//         if(
//             date('w', strtotime($currentDate)) == 0 ||
//             isset($holidayMap[$currentDate])
//         )
//         {
//             continue;
//         }

//         $workingDays++;
//     }

//     return $currentDate;
// };


//    /*
// |--------------------------------------------------------------------------
// | CALCULATE ALL TASKS FROM MAIN FRAME START DATE
// |--------------------------------------------------------------------------
// */

// $virtualDates = [];

// if(!isset($scheduleMap[54]))
// {
//     echo 'Main Frame not found in scheduling.';
//     exit;
// }

// $departmentAnchorDate = $scheduleMap[54]['start_date'];



// $calculateTask = function($taskId) use (
//     &$calculateTask,
//     &$taskMap,
//     &$virtualDates,
//     &$scheduleMap,
//     $departmentAnchorDate,
//     $getNextWorkingDay,
//     $addWorkingDays
// )
// {
//     if(isset($virtualDates[$taskId]))
//     {
//         return;
//     }

//     $task = $taskMap[$taskId];
//     $parentTaskId = $task['tat_start_from'];

//     /*
//     |--------------------------------------------------------------------------
//     | PARENT OUTSIDE DEPARTMENT
//     |--------------------------------------------------------------------------
//     */
//     if(!isset($taskMap[$parentTaskId]))
//     {
//         $startDate = $departmentAnchorDate;
//     }
//     else
//     {
//         $calculateTask($parentTaskId);

//         $startDate = $getNextWorkingDay(
//     $virtualDates[$parentTaskId]['end_date']
// );
//     }

//     $endDate = $addWorkingDays(
//     $startDate,
//     $task['tat']
// );

//     $completedDate = '';

//     if(
//         isset($scheduleMap[$taskId]) &&
//         $scheduleMap[$taskId]['task_status'] == 1 &&
//         !empty($scheduleMap[$taskId]['task_completed_on'])
//     )
//     {
//         $delayDays = round(
//             (
//                 strtotime($scheduleMap[$taskId]['task_completed_on'])
//                 -
//                 strtotime($scheduleMap[$taskId]['end_date'])
//             ) / 86400
//         );

//         $completedDate = date(
//             'Y-m-d H:i:s',
//             strtotime(
//                 $endDate .
//                 ($delayDays >= 0 ? ' +' : ' ') .
//                 $delayDays .
//                 ' day'
//             )
//         );
//     }

//     $virtualDates[$taskId] = [
//         'start_date'     => $startDate,
//         'end_date'       => $endDate,
//         'completed_date' => $completedDate
//     ];
// };

// foreach($taskMap as $taskId => $task)
// {
//     $calculateTask($taskId);
// }

//     /*
//     |--------------------------------------------------------------------------
//     | PREVIEW DATA
//     |--------------------------------------------------------------------------
//     */
//     foreach ($masterTasks as $task)
//     {
//         $taskId = $task['task_id'];

//         if (!isset($virtualDates[$taskId]))
//         {
//             continue;
//         }

//     $action = 'INSERT';

// $currentStart = '';
// $currentEnd = '';
// $currentCompleted = '';

// if (isset($scheduleMap[$taskId]))
// {
//     $currentStart = $scheduleMap[$taskId]['start_date'];
//     $currentEnd = $scheduleMap[$taskId]['end_date'];
//     $currentCompleted = $scheduleMap[$taskId]['task_completed_on'];

//     $action = 'UPDATE';
// }


//         $preview[] = [
//             'df_id'              => $df_id,
//             'df_no'              => $df['df_no'],
//             'task_id'            => $taskId,
//             'task_name'          => $task['task_name'],
//             'current_start'      => date('d-M-Y',strtotime($currentStart)),
//             'current_end'        => date('d-M-Y',strtotime($currentEnd)),
//             'current_completed'  => date('d-M-Y',strtotime($currentCompleted)),
//             'proposed_start'     => date('d-M-Y',strtotime($virtualDates[$taskId]['start_date'])),
//             'proposed_end'       => date('d-M-Y',strtotime($virtualDates[$taskId]['end_date'])),
//             'proposed_completed' => $virtualDates[$taskId]['completed_date'],
//             'action'             => $action
//         ];
//     }

//    echo '<table border="1" cellpadding="5" cellspacing="0">';
// echo '<tr style="background:#eee;">';

// echo '<th>DF NO</th>';
// echo '<th>TASK ID</th>';
// echo '<th>TASK NAME</th>';

// echo '<th>CURRENT START</th>';
// echo '<th>CURRENT END</th>';


// echo '<th>PROPOSED START</th>';
// echo '<th>PROPOSED END</th>';
// echo '<th>CURRENT COMPLETED</th>';
// echo '<th>PROPOSED COMPLETED</th>';

// echo '<th>ACTION</th>';

// echo '</tr>';

// foreach($preview as $row)
// {
//     $bg = '';

//     if($row['action']=='INSERT')
//     {
//         $bg='style="background:#d4edda;"';
//     }
//     elseif($row['action']=='UPDATE')
//     {
//         $bg='style="background:#fff3cd;"';
//     }

//     echo "<tr {$bg}>";

//     echo '<td>'.$row['df_no'].'</td>';
//     echo '<td>'.$row['task_id'].'</td>';
//     echo '<td>'.$row['task_name'].'</td>';

//     echo '<td>'.$row['current_start'].'</td>';
//     echo '<td>'.$row['current_end'].'</td>';
//     echo '<td>'.$row['proposed_start'].'</td>';
//     echo '<td>'.$row['proposed_end'].'</td>';
//     echo '<td>'.$row['current_completed'].'</td>';
//     echo '<td>'.$row['proposed_completed'].'</td>';

//     echo '<td>'.$row['action'].'</td>';

//     echo '</tr>';
// }

// echo '</table>';

// exit;
   
// }


public function preview_department18_reschedule($df_id = 203)
{
    $department_id = 18;

    $preview = [];

    $pendingTaskExists = $this->db
    ->where('df_id',$df_id)
    ->where('department_id',18)
    ->where('task_status',0)
    ->count_all_results('task_department_wise_scheduling');

if($pendingTaskExists == 0)
{
    echo 'All tasks completed. No reschedule required.';
    exit;
}



/*
|--------------------------------------------------------------------------
| MAIN FRAME REFERENCE ROW
|--------------------------------------------------------------------------
*/
$mainFrameRow = $this->db
    ->where('df_id', $df_id)
    ->where('department_id', $department_id)
    ->where('taskid', 54)
    ->get('task_department_wise_scheduling')
    ->row_array();

if(empty($mainFrameRow))
{
    echo 'Main Frame scheduling row not found.';
    exit;
}



    /*
    |--------------------------------------------------------------------------
    | DF DETAILS
    |--------------------------------------------------------------------------
    */
    $df = $this->db
        ->select('id, df_no')
        ->from('df_release')
        ->where('id', $df_id)
        ->get()
        ->row_array();

    if (empty($df))
    {
        echo "DF not found";
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | MASTER TASKS
    |--------------------------------------------------------------------------
    */
    $masterTasks = $this->db
        ->select('task_id, task_name, tat, tat_start_from')
        ->from('task_management')
        ->where('department_id', $department_id)
        ->where('status', 1)
        ->get()
        ->result_array();

    $taskMap = [];
    $childrenMap = [];

    foreach ($masterTasks as $task)
    {
        $taskMap[$task['task_id']] = $task;
        $childrenMap[$task['tat_start_from']][] = $task['task_id'];
    }

    /*
    |--------------------------------------------------------------------------
    | EXISTING SCHEDULE
    |--------------------------------------------------------------------------
    */
    $existingRows = $this->db
        ->select("
            id,
            taskid,
            start_date,
            end_date,
            task_status,
            task_completed_on
        ")
        ->from('task_department_wise_scheduling')
        ->where('df_id', $df_id)
        ->where('department_id', $department_id)
        ->get()
        ->result_array();

    $scheduleMap = [];

    foreach ($existingRows as $row)
    {
        $scheduleMap[$row['taskid']] = $row;
    }


/*
|--------------------------------------------------------------------------
| HOLIDAYS
|--------------------------------------------------------------------------
*/
$holidayRows = $this->db
    ->select('holiday_date')
    ->from('prestogroup_holidays')
    ->get()
    ->result_array();

$holidayMap = [];

foreach($holidayRows as $row)
{
    $holidayMap[$row['holiday_date']] = true;
}

/*
|--------------------------------------------------------------------------
| NEXT WORKING DAY
|--------------------------------------------------------------------------
*/
$getNextWorkingDay = function($date) use ($holidayMap)
{
    $nextDate = date(
        'Y-m-d',
        strtotime($date.' +1 day')
    );

    while(
        date('w', strtotime($nextDate)) == 0 ||
        isset($holidayMap[$nextDate])
    )
    {
        $nextDate = date(
            'Y-m-d',
            strtotime($nextDate.' +1 day')
        );
    }

    return $nextDate;
};

/*
|--------------------------------------------------------------------------
| ADD WORKING DAYS
|--------------------------------------------------------------------------
*/
$addWorkingDays = function($startDate,$tat) use ($holidayMap)
{
    $currentDate = $startDate;

    if($tat <= 1)
    {
        return $currentDate;
    }

    $workingDays = 1;

    while($workingDays < $tat)
    {
        $currentDate = date(
            'Y-m-d',
            strtotime($currentDate.' +1 day')
        );

        if(
            date('w', strtotime($currentDate)) == 0 ||
            isset($holidayMap[$currentDate])
        )
        {
            continue;
        }

        $workingDays++;
    }

    return $currentDate;
};


   /*
|--------------------------------------------------------------------------
| CALCULATE ALL TASKS FROM MAIN FRAME START DATE
|--------------------------------------------------------------------------
*/

$virtualDates = [];

if(!isset($scheduleMap[54]))
{
    echo 'Main Frame not found in scheduling.';
    exit;
}

$departmentAnchorDate = $scheduleMap[54]['start_date'];



$calculateTask = function($taskId) use (
    &$calculateTask,
    &$taskMap,
    &$virtualDates,
    &$scheduleMap,
    $departmentAnchorDate,
    $getNextWorkingDay,
    $addWorkingDays
)
{
    if(isset($virtualDates[$taskId]))
    {
        return;
    }

    $task = $taskMap[$taskId];
    $parentTaskId = $task['tat_start_from'];

    /*
    |--------------------------------------------------------------------------
    | PARENT OUTSIDE DEPARTMENT
    |--------------------------------------------------------------------------
    */
    if(!isset($taskMap[$parentTaskId]))
    {
        $startDate = $departmentAnchorDate;
    }
    else
    {
        $calculateTask($parentTaskId);

        $startDate = $getNextWorkingDay(
    $virtualDates[$parentTaskId]['end_date']
);
    }

    $endDate = $addWorkingDays(
    $startDate,
    $task['tat']
);

    $completedDate = '';

    if(
        isset($scheduleMap[$taskId]) &&
        $scheduleMap[$taskId]['task_status'] == 1 &&
        !empty($scheduleMap[$taskId]['task_completed_on'])
    )
    {
        $delayDays = round(
            (
                strtotime($scheduleMap[$taskId]['task_completed_on'])
                -
                strtotime($scheduleMap[$taskId]['end_date'])
            ) / 86400
        );

        $completedDate = date(
            'Y-m-d H:i:s',
            strtotime(
                $endDate .
                ($delayDays >= 0 ? ' +' : ' ') .
                $delayDays .
                ' day'
            )
        );
    }

    $virtualDates[$taskId] = [
        'start_date'     => $startDate,
        'end_date'       => $endDate,
        'completed_date' => $completedDate
    ];
};

foreach($taskMap as $taskId => $task)
{
    $calculateTask($taskId);
}


/*
|--------------------------------------------------------------------------
| SAVE CHANGES
|--------------------------------------------------------------------------
*/
foreach($virtualDates as $taskId => $dates)
{
    if(isset($scheduleMap[$taskId]))
    {
        $saveData = [
            'start_date' => $dates['start_date'],
            'end_date'   => $dates['end_date']
        ];

        /*
        |--------------------------------------------------------------------------
        | UPDATE COMPLETED DATE ALSO
        |--------------------------------------------------------------------------
        */
        if(
            $scheduleMap[$taskId]['task_status'] == 1 &&
            !empty($dates['completed_date'])
        )
        {
            $saveData['task_completed_on']
                = $dates['completed_date'];
        }

        $this->db
            ->where('id',$scheduleMap[$taskId]['id'])
            ->update(
                'task_department_wise_scheduling',
                $saveData
            );
    }
    else
    {
        /*
        |--------------------------------------------------------------------------
        | NEW TASK INSERT
        |--------------------------------------------------------------------------
        */
        $insertData = [

            'df_id'         => $df_id,
            'department_id' => $department_id,
            'taskid'        => $taskId,

            'start_date'    => $dates['start_date'],
            'end_date'      => $dates['end_date'],

            'task_status'   => 0,

            'added_on'      => date('Y-m-d H:i:s'),
            'added_by'      => 161,
			/* copied from Main Frame */
			'po_id'              => $mainFrameRow['po_id'],
			'userid'            => $mainFrameRow['userid'],
			'assigned_user'      => $mainFrameRow['assigned_user'],
			'assigned_by'        => $mainFrameRow['assigned_by'],

			'assigned_on'        => date('Y-m-d H:i:s')
        ];

        $this->db->insert(
            'task_department_wise_scheduling',
            $insertData
        );
    }
}



    /*
    |--------------------------------------------------------------------------
    | PREVIEW DATA
    |--------------------------------------------------------------------------
    */
    foreach ($masterTasks as $task)
    {
        $taskId = $task['task_id'];

        if (!isset($virtualDates[$taskId]))
        {
            continue;
        }

    $action = 'INSERT';

$currentStart = '';
$currentEnd = '';
$currentCompleted = '';

if (isset($scheduleMap[$taskId]))
{
    $currentStart = $scheduleMap[$taskId]['start_date'];
    $currentEnd = $scheduleMap[$taskId]['end_date'];
    $currentCompleted = $scheduleMap[$taskId]['task_completed_on'];

    $action = 'UPDATE';
}


        $preview[] = [
            'df_id'              => $df_id,
            'df_no'              => $df['df_no'],
            'task_id'            => $taskId,
            'task_name'          => $task['task_name'],
            'current_start'      => date('d-M-Y',strtotime($currentStart)),
            'current_end'        => date('d-M-Y',strtotime($currentEnd)),
            'current_completed'  => date('d-M-Y',strtotime($currentCompleted)),
            'proposed_start'     => date('d-M-Y',strtotime($virtualDates[$taskId]['start_date'])),
            'proposed_end'       => date('d-M-Y',strtotime($virtualDates[$taskId]['end_date'])),
            'proposed_completed' => $virtualDates[$taskId]['completed_date'],
            'action'             => $action
        ];
    }

 echo '<h2>Department 18 Reschedule Completed</h2>';
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr style="background:#eee;">';

echo '<th>DF NO</th>';
echo '<th>TASK ID</th>';
echo '<th>TASK NAME</th>';

echo '<th>CURRENT START</th>';
echo '<th>CURRENT END</th>';


echo '<th>PROPOSED START</th>';
echo '<th>PROPOSED END</th>';
echo '<th>CURRENT COMPLETED</th>';
echo '<th>PROPOSED COMPLETED</th>';

echo '<th>ACTION</th>';

echo '</tr>';

foreach($preview as $row)
{
    $bg = '';

    if($row['action']=='INSERT')
    {
        $bg='style="background:#d4edda;"';
    }
    elseif($row['action']=='UPDATE')
    {
        $bg='style="background:#fff3cd;"';
    }

    echo "<tr {$bg}>";

    echo '<td>'.$row['df_no'].'</td>';
    echo '<td>'.$row['task_id'].'</td>';
    echo '<td>'.$row['task_name'].'</td>';

    echo '<td>'.$row['current_start'].'</td>';
    echo '<td>'.$row['current_end'].'</td>';
    echo '<td>'.$row['proposed_start'].'</td>';
    echo '<td>'.$row['proposed_end'].'</td>';
    echo '<td>'.$row['current_completed'].'</td>';
    echo '<td>'.$row['proposed_completed'].'</td>';

    echo '<td>'.$row['action'].'</td>';

    echo '</tr>';
}

echo '</table>';

exit;
   
}


public function preview_tat_correction()
{
    ini_set('memory_limit', '512M');
    set_time_limit(0);

    $preview = [];

    // -----------------------------------
    // GET ALL ACTIVE DFS
    // -----------------------------------
    $dfs = $this->db
        ->where('df_status', 0)
        ->where('id', 206)
        ->get('df_release')
        ->result();

    if (empty($dfs)) {
        echo "No active DF found";
        return;
    }

    // -----------------------------------
    // LOAD HOLIDAYS
    // -----------------------------------
    $holidayRows = $this->db
        ->select('holiday_date')
        ->get('prestogroup_holidays')
        ->result_array();

    $holidays = array_column($holidayRows, 'holiday_date');

    foreach ($dfs as $df)
    {
        // -----------------------------------
        // FIND BASE TASK (TASK ID = 2)
        // -----------------------------------
      $baseTask = $this->db
    ->where('df_id', $df->id)
    ->where('taskid', 2)
    ->get('task_department_wise_scheduling')
    ->row();

if (!$baseTask) {
    continue;
}

$baseDate = $baseTask->start_date;

$tasks = $this->db
   ->select('
    s.id,
    s.df_id,
    s.taskid as task_id,
    s.department_id,
    d.department,
    s.start_date,
    s.end_date,
    s.task_status,
    s.task_completed_on,
    tm.task_name,
    tm.tat,
    tm.tat_start_from,
    tm.sortorder,
    tm.system_created_sort_order
')
    ->from('task_department_wise_scheduling s')
    ->join('task_management tm','tm.task_id=s.taskid')
    ->join('departments d','d.department_id=s.department_id','left')
    ->where('s.df_id',$df->id)
    ->order_by('tm.sortorder','ASC')
    ->get()
    ->result();

        $calculatedDates = [];

foreach ($tasks as $task)
{
    if ($task->task_id == 2)
    {
        // KEEP ORIGINAL START DATE
        $newStart = $task->start_date;

        // FIX END DATE
        $newEnd = $this->_addWorkingDays(
            $newStart,
            max(0, $task->tat - 1),
            $holidays
        );
    }
    else
    {
        $parentTaskId = $task->tat_start_from;

       if (isset($calculatedDates[$parentTaskId]))
{
    $parentEnd = $calculatedDates[$parentTaskId]['end'];

    $newStart = $this->_nextWorkingDay(
        date('Y-m-d', strtotime($parentEnd . ' +1 day')),
        $holidays
    );
}
else
{
    // fallback
    $newStart = $task->start_date;
}

        // CORRECTED TAT
        $newEnd = $this->_addWorkingDays(
            $newStart,
            max(0, $task->tat - 1),
            $holidays
        );


        $currentCompletedDate = '';
$newCompletedDate = '';

if(
    $task->task_status == 1 &&
    !empty($task->task_completed_on) &&
    $task->task_completed_on != '0000-00-00 00:00:00'
)
{
    $currentCompletedDate = date(
        'Y-m-d',
        strtotime($task->task_completed_on)
    );

    $dayDiff = floor(
        (
            strtotime($currentCompletedDate)
            -
            strtotime($task->end_date)
        ) / 86400
    );

    $newCompletedDate = date(
        'Y-m-d',
        strtotime($newEnd.' '.$dayDiff.' days')
    );
}


    }

    $calculatedDates[$task->task_id] = [
        'start' => $newStart,
        'end'   => $newEnd
    ];

    $preview[] = [
        'df_no'          => $df->df_no,
        'task_id'        => $task->task_id,
        'task_name'      => $task->task_name,
        'department_id'  => $task->department_id,
        'parent_task' => $task->tat_start_from,
'system_order' => $task->system_created_sort_order,
          'department'     => $task->department,
        'tat'            => $task->tat,
        'current_start'  => $task->start_date,
        'current_end'    => $task->end_date,
        'new_start_date' => $newStart,
        'new_end_date'   => $newEnd,
        'current_completed_date' => $currentCompletedDate,
'new_completed_date'     => $newCompletedDate,
'task_status'            => $task->task_status
    ];
}
    }


//echo "<pre>"; print_r($preview); exit;
usort($preview, function($a, $b) {

    $dateCompare = strtotime($a['new_start_date']) - strtotime($b['new_start_date']);

    if ($dateCompare != 0) {
        return $dateCompare;
    }

    // same start date -> sort by end date
    $endCompare = strtotime($a['new_end_date']) - strtotime($b['new_end_date']);

    if ($endCompare != 0) {
        return $endCompare;
    }

    // same dates -> task id
    return $a['task_id'] - $b['task_id'];

});


$departments = $this->db
    ->where('business_loc_id',2)
    ->where('status',1)
    ->order_by('department','ASC')
    ->get('departments')
    ->result();


  $departments = $this->db
    ->where('business_loc_id', 2)
    ->where('status', 1)
    ->order_by('department', 'ASC')
    ->get('departments')
    ->result();

echo '
<!DOCTYPE html>
<html>
<head>
    <title>TAT Correction Preview</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
        .changed{
            background:#fff3cd !important;
        }

        .filter-box{
            margin-bottom:15px;
        }

        .table>tbody>tr.changed>td{
            background:#fff3cd !important;
        }

        .summary-box{
            margin-bottom:15px;
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <h3>TAT Correction Preview</h3>

    <div class="row filter-box">

        <div class="col-md-3">
            <label>Department Filter</label>
            <select class="form-control" id="departmentFilter">
                <option value="">All Departments</option>';

foreach($departments as $dept)
{
    echo '<option value="'.$dept->department_id.'">'.$dept->department.'</option>';
}

echo '
            </select>
        </div>

    </div>

    <table class="table table-bordered table-striped table-condensed" id="previewTable">

        <thead>

            <tr>
                <th>DF No</th>
                <th>Department</th>
                <th>Task ID</th>
                <th>Task Name</th>
                <th>TAT</th>
				<th>System Order</th>
				<th>Parent Task</th>
                <th>Current Start</th>
                <th>Current End</th>
                  <th>Current Completed</th>
                <th>New Start</th>
               <th>New End</th>
              <th>New Completed</th>
                <th>Status</th>
            </tr>

        </thead>

        <tbody>';

        foreach ($preview as $row)
{
    $changed = (
        $row['current_start'] != $row['new_start_date']
        ||
        $row['current_end'] != $row['new_end_date']
    );

    echo '<tr
            data-department="'.$row['department_id'].'"
            class="'.($changed ? 'changed' : '').'"
          >';

    echo '<td>'.$row['df_no'].'</td>';

    echo '<td>'.$row['department'].'</td>';

    echo '<td>'.$row['task_id'].'</td>';

    echo '<td>'.$row['task_name'].'</td>';

    echo '<td>'.$row['tat'].'</td>';
    echo '<td>'.$row['parent_task'].'</td>';
    echo '<td>'.$row['system_order'].'</td>';


    echo '<td>'.date('d-m-Y', strtotime($row['current_start'])).'</td>';

    echo '<td>'.date('d-m-Y', strtotime($row['current_end'])).'</td>';
        echo '<td>';

if(!empty($row['current_completed_date']))
{
    echo date(
        'd-m-Y',
        strtotime($row['current_completed_date'])
    );
}

echo '</td>';

    echo '<td>'.date('d-m-Y', strtotime($row['new_start_date'])).'</td>';

    echo '<td>'.date('d-m-Y', strtotime($row['new_end_date'])).'</td>';



echo '<td>';

if(!empty($row['new_completed_date']))
{
    echo date(
        'd-m-Y',
        strtotime($row['new_completed_date'])
    );
}

echo '</td>';


    echo '<td>';

    if($changed)
    {
        echo '<span class="label label-danger">Will Change</span>';
    }
    else
    {
        echo '<span class="label label-success">No Change</span>';
    }

    echo '</td>';

    echo '</tr>';
}


echo '

        </tbody>

    </table>

</div>

<script>

$("#departmentFilter").on("change", function(){

    var dept = $(this).val();

    $("#previewTable tbody tr").each(function(){

        if(dept == "")
        {
            $(this).show();
        }
        else if($(this).attr("data-department") == dept)
        {
            $(this).show();
        }
        else
        {
            $(this).hide();
        }

    });

});

</script>

</body>
</html>';

exit;

}

private function _addWorkingDays($startDate, $daysToAdd, $holidays)
{
    $date = $startDate;

    while ($daysToAdd > 0)
    {
        $date = date('Y-m-d', strtotime($date.' +1 day'));

        if (!in_array($date, $holidays))
        {
            $daysToAdd--;
        }
    }

    return $date;
}

private function _nextWorkingDay($date, $holidays)
{
    while (in_array($date, $holidays))
    {
        $date = date('Y-m-d', strtotime($date.' +1 day'));
    }

    return $date;
}


public function update_tat_correction()
{
    ini_set('memory_limit', '1024M');
    set_time_limit(0);

    $success = [];
    $failed  = []; 

    // -----------------------------------
    // HOLIDAYS
    // -----------------------------------
    $holidayRows = $this->db
        ->select('holiday_date')
        ->get('prestogroup_holidays')
        ->result_array();

    $holidays = array_column($holidayRows, 'holiday_date');

    // -----------------------------------
    // ALL ACTIVE DF
    // -----------------------------------
    $dfs = $this->db
        ->where('df_status', 0)
        ->where('on_hold',0)
        ->where('id!=',202)
        ->get('df_release')
        ->result();

    if (empty($dfs))
    {
        echo "No Active DF Found";
        return;
    }

    foreach ($dfs as $df)
    {
        $this->db->trans_begin();

        try
        {
            $baseTask = $this->db
                ->where('df_id', $df->id)
                ->where('taskid', 2)
                ->get('task_department_wise_scheduling')
                ->row();

            if (!$baseTask)
            {
                throw new Exception(
                    'Task ID 2 Missing'
                );
            }

            $tasks = $this->db
                ->select('
                    s.id,
                    s.df_id,
                    s.taskid as task_id,
                    s.start_date,
                    s.end_date,
                    s.task_status,
                    s.task_completed_on,

                    tm.task_name,
                    tm.tat,
                    tm.tat_start_from,
                    tm.system_created_sort_order
                ')
                ->from('task_department_wise_scheduling s')
                ->join(
                    'task_management tm',
                    'tm.task_id=s.taskid'
                )
                ->where('s.df_id', $df->id)
                ->order_by(
                    'tm.system_created_sort_order',
                    'ASC'
                )
                ->get()
                ->result();

            if (empty($tasks))
            {
                throw new Exception(
                    'No Tasks Found'
                );
            }

            $calculatedDates = [];
            $updatedRows     = 0;

            foreach ($tasks as $task)
            {
                $currentCompletedDate = '';
                $newCompletedDate     = '';

                // -----------------------------
                // TASK 2
                // -----------------------------
                if ($task->task_id == 2)
                {
                    $newStart = $task->start_date;

                    $newEnd = $this->_addWorkingDays(
                        $newStart,
                        max(0, $task->tat - 1),
                        $holidays
                    );
                }
                else
                {
                    $parentTaskId = $task->tat_start_from;

                    if (isset($calculatedDates[$parentTaskId]))
                    {
                        $parentEnd =
                            $calculatedDates[$parentTaskId]['end'];

                        $newStart =
                            $this->_nextWorkingDay(
                                date(
                                    'Y-m-d',
                                    strtotime(
                                        $parentEnd . ' +1 day'
                                    )
                                ),
                                $holidays
                            );
                    }
                    else
                    {
                        $newStart = $task->start_date;
                    }

                    $newEnd = $this->_addWorkingDays(
                        $newStart,
                        max(0, $task->tat - 1),
                        $holidays
                    );
                }

                // -----------------------------
                // COMPLETED TASK
                // -----------------------------
                if (
                    $task->task_status == 1
                    &&
                    !empty($task->task_completed_on)
                    &&
                    $task->task_completed_on != '0000-00-00 00:00:00'
                )
                {
                    $currentCompletedDate = date(
                        'Y-m-d',
                        strtotime(
                            $task->task_completed_on
                        )
                    );

                    $dayDiff = floor(
                        (
                            strtotime(
                                $currentCompletedDate
                            )
                            -
                            strtotime(
                                $task->end_date
                            )
                        ) / 86400
                    );

                    $newCompletedDate = date(
                        'Y-m-d',
                        strtotime(
                            $newEnd . ' ' .
                            $dayDiff . ' days'
                        )
                    );
                }

                $calculatedDates[$task->task_id] = [
                    'start' => $newStart,
                    'end'   => $newEnd
                ];

                $updateData = [
                    'start_date' => $newStart,
                    'end_date'   => $newEnd,
                    'changedOn'  => date('Y-m-d H:i:s'),
                    'changedBy'  => 1
                ];

                if (
                    $task->task_status == 1
                    &&
                    !empty($newCompletedDate)
                )
                {
                    $timePart = date(
                        'H:i:s',
                        strtotime(
                            $task->task_completed_on
                        )
                    );

                    $updateData['task_completed_on']
                        = $newCompletedDate . ' ' . $timePart;
                }

                $this->db
                    ->where('id', $task->id)
                    ->update(
                        'task_department_wise_scheduling',
                        $updateData
                    );

                if ($this->db->error()['code'] != 0)
                {
                    throw new Exception(
                        'Update Failed For Task ID : '
                        . $task->task_id
                    );
                }

                $updatedRows++;
            }

            if ($updatedRows != count($tasks))
            {
                throw new Exception(
                    'Updated ' .
                    $updatedRows .
                    ' of ' .
                    count($tasks) .
                    ' Tasks'
                );
            }

            if ($this->db->trans_status() === FALSE)
            {
                throw new Exception(
                    'Transaction Failed'
                );
            }

            $this->db->trans_commit();

            $success[] = [
                'df_id'         => $df->id,
                'df_no'         => $df->df_no,
                'tasks_updated' => $updatedRows
            ];
        }
        catch (Exception $e)
        {
            $this->db->trans_rollback();

            $failed[] = [
                'df_id' => $df->id,
                'df_no' => $df->df_no,
                'error' => $e->getMessage()
            ];
        }
    }

    echo '<h2 style="color:green">SUCCESS</h2>';
    echo '<pre>';
    print_r($success);
    echo '</pre>';

    echo '<h2 style="color:red">ROLLBACK</h2>';
    echo '<pre>';
    print_r($failed);
    echo '</pre>';

    echo '<hr>';

    echo '<b>Total Success DF :</b> ' .
         count($success);

    echo '<br>';

    echo '<b>Total Rollback DF :</b> ' .
         count($failed);

    exit;
}

} 
