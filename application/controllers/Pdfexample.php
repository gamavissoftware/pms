<?php 
class Pdfexample extends CI_Controller{
      function __construct() { 
 parent::__construct();

	$this->load->model('Daily_report_model');
	$this->load->model('Salescrm_model');
 } 
//      function index()
//  {
// $this->load->library('Pdf');
// $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
// $pdf->SetTitle('Pdf Example');
// $pdf->SetHeaderMargin(30);
// $pdf->SetTopMargin(20);
// $pdf->setFooterMargin(20);
// $pdf->SetAutoPageBreak(true);
// $pdf->SetAuthor('Author');
// $pdf->SetDisplayMode('real', 'default');
// $pdf->Write(5, 'CodeIgniter TCPDF Integration');

// $pdf->Output('pdfexample.pdf', 'i');
//       }


function daily_reports()
{

$today = $this->uri->segment(3);
if($today<>'')
{
$visit_counts_userwise_today = $this->Daily_report_model->visit_counts_userwise_today($today);
$agent_list = $this->Daily_report_model->visit_agent_list($today);
$on_leave_todays = $this->Daily_report_model->on_leave_today($today);
$early_going_users = $this->Daily_report_model->early_going_user($today);
$early_going_count = count($early_going_users);
$no_punch_out = $this->Daily_report_model->no_punch_out($today);
$no_punch_out_data = count($no_punch_out);
$getTrialsStartedToday = $this->Daily_report_model->getTrialsStartedToday($today);
$getTrialsApprovedToday = $this->Daily_report_model->getTrialsApprovedToday($today);
$total_dispatched = $this->Daily_report_model->total_dispatch_today($today);
$total_dispatch_today = explode("|",$total_dispatched);
$total_dispatch = $total_dispatch_today[0];
$total_dispatch_amount = $total_dispatch_today[1];
$total_dispatch_qty = $total_dispatch_today[2];
$pending_order_for_billing = $this->Daily_report_model->pending_order_for_billing();
$all_payment_overdue = $this->Daily_report_model->all_payment_overdue();
$all_payment_overdue_amount = $this->Daily_report_model->all_payment_overdue_amount();
$getPDCToBeCollected = $this->Daily_report_model->getPDCToBeCollected();
$sampleCollected = $this->Daily_report_model->sampleCollected($today);
$getBulkPurchaseDensity = $this->Daily_report_model->getBulkPurchaseDensity($today);
$getNewPartyProductSupplied = $this->Daily_report_model->getNewPartyProductSupplied($today);
$getPaymentStatusChange = $this->Daily_report_model->getPaymentStatusChange($today);
$getApprovalPendingFromMgt = $this->Daily_report_model->getApprovalPendingFromMgt();
$arr_mgt = explode('|', $getApprovalPendingFromMgt);
$equivalent_chart_approval = $arr_mgt[0];
$specfile_approval = $arr_mgt[1];
$total_discount_approval = $arr_mgt[2];
$total_payment_approval = $arr_mgt[3];
$total_conveyance_approval = $arr_mgt[4];
$total_adjustment_approval = $arr_mgt[5];
$total_orders_on_hold = $arr_mgt[6];
$total_density_approval = $arr_mgt[7];
$totaltrail = $arr_mgt[8];

$getAllSalesUsers = $this->Daily_report_model->getAllSalesUsers();
$getQuotationSentLeadStage = $this->Daily_report_model->getQuotationSentLeadStage();
$getQuotationRevisedLeadStage = $this->Daily_report_model->getQuotationRevisedLeadStage();
$getLeadConversionLeadStage = $this->Daily_report_model->getLeadConversionLeadStage();


$html='<style>li span{ font-weight:bold;}</style>
<table width="100%" border="1" ruled="all" style="text-align:center; padding:3px; background-color:lightgrey;text-transform:capitalize;">
<tr>
<td>
<b>CRM DAILY REPORT</b>
</td>
</tr>
</table>
<table width="100%" border="1" ruled="all" style=" padding:3px;">
<tr>
<td style="font-size:14px !important;">
<ol style="padding:0px; margin:0px;">
<li><strong>Todays Visits: </strong><span style="color:red">'.$visit_counts_userwise_today.'</span>';
if($agent_list) {
$html .= '<br><ul>';
foreach($agent_list as $agent) {
$html .= '<li>'.$agent['name'].' : '.$agent['visit_count'].'</li>';
}
$html .= '</ul>';
}
$html .= '</li>
<li><strong>Todays Absent : </strong><span style="color:red">'.count($on_leave_todays).'</span>';
if($on_leave_todays) {
$html .= '<br><ul>';
foreach($on_leave_todays as $user) {
$html .= '<li>'.$user->first_name.' '.$user->last_name.'</li>';
}
$html .= '</ul>';
}
$html .= '</li>
<li><strong>Todays early going : </strong><span style="color:red">'.$early_going_count.'</span>';
if($early_going_users) {
$html .= '<br><ul>';
foreach($early_going_users as $user) {
$html .= '<li>'.$user->first_name.' '.$user->last_name.'-'.$user->evening_time.'</li>';
}
$html .= '</ul>';
}
$html .= '</li>
<li><strong>Forgot to punch out : </strong><span style="color:red">'.$no_punch_out_data.'</span>';
if($no_punch_out) {
$html .= '<br><ul>';
foreach($no_punch_out as $user) {
$html .= '<li>'.$user->first_name.' '.$user->last_name.'</li>';
}
$html .= '</ul>';
}
$html .= '</li>';
$html .= '<li><strong>Trials Started Today: </strong><span style="color:red">'.$getTrialsStartedToday.'</span></li>
<li><strong>Trials Approved Today: </strong><span style="color:red">'.$getTrialsApprovedToday.'</span></li>
<li><strong>Total Dispatched with value : </strong><span style="color:red">'.$total_dispatch.'</span> : <span style="color:red"> ₹'.$total_dispatch_amount.'</span> : <span style="color:red">'.$total_dispatch_qty.' ltr</span></li>
<li><strong>Pending Orders value : </strong><span style="color:red">'.$pending_order_for_billing.'</span></li>
<li><strong>Payment Overdue count with value : </strong><span style="color:red">'.$all_payment_overdue.'</span> : <span style="color:red"> ₹'.$all_payment_overdue_amount.'</span></li>
<li><strong>Pending Order PDC(s): </strong><span style="color:red">'.$getPDCToBeCollected.'</span></li>
<li><strong>Samples Collected : </strong><span style="color:red">'.$sampleCollected.'</span></li>
<li><strong>Bulk purchase conversion density and purchase data : </strong><span style="color:red">'.count($getBulkPurchaseDensity).'</span>';
if(count($getBulkPurchaseDensity) > 0) {
$html .= '<br><ul>';
foreach($getBulkPurchaseDensity as $row1) {
$html .= '<li>'.$row1->instruments_name.' : '.$row1->original_qty.' KG'.' : '.$row1->density.' : '.$row1->qty.' LTR</li>';
}
$html .= '</ul>';
}
$html .= '</li>
<li><strong>New party product supplied : </strong><span style="color:red">'.$getNewPartyProductSupplied.'</span></li>
<li><strong>Payment Status Change : </strong><span style="color:red">'.$getPaymentStatusChange.'</span></li>
<li><strong>Approvals pending from management : </strong>
 <br><ul>
	<li>Spec & MSDS File: <span style="color:red">'.$specfile_approval.'</span></li>
	<li>Quotations: <span style="color:red">'.$total_discount_approval.'</span></li>
	<li>Customer Payment Terms: <span style="color:red">'.$total_payment_approval.'</span></li>
	<li>User Conveyance Approval: <span style="color:red">'.$total_conveyance_approval.'</span></li>
	<li>Payment Closure Approval: <span style="color:red">'.$total_adjustment_approval.'</span></li>
	<li>Order Hold Approval: <span style="color:red">'.$total_orders_on_hold.'</span></li>
	<li>Bulk Purchase Density Approval: <span style="color:red">'.$total_orders_on_hold.'</span></li>
	<li>Equivalent Chart Approval: <span style="color:red">'.$equivalent_chart_approval.'</span></li>
  <li>Trail Approval: <span style="color:red">'.$totaltrail.'</span></li>
</ul>
</li>
<li><strong>Daily Activity of Sales Persons : </strong>';
if($getAllSalesUsers != '') {
$html .= '<br><ul>';
	foreach($getAllSalesUsers as $row2) {
$html .= '<li>'.$row2->first_name.' '.$row2->last_name;
$getTodaysQuotationSent = $this->Daily_report_model->getTodaysQuotationSent($today, $row2->user_id, $getQuotationSentLeadStage);
$getTodaysQuotationRevised = $this->Daily_report_model->getTodaysQuotationSent($today, $row2->user_id, $getQuotationRevisedLeadStage);
$getTodaysConversion = $this->Daily_report_model->getTodaysQuotationSent($today, $row2->user_id, $getLeadConversionLeadStage);
$getTodaysVisit = $this->Daily_report_model->getTodaysVisit($today,$row2->user_id);

$html .= '<ul>
<li>Today Visits: <span style="color:red">'.$getTodaysVisit.'</span></li>
			<li>Quotations Sent: <span style="color:red">'.$getTodaysQuotationSent.'</span></li>
			<li>Quotations Revised: <span style="color:red">'.$getTodaysQuotationRevised.'</span></li>
			<li>Order Won: <span style="color:red">'.$getTodaysConversion.'</span></li>

</ul>';
$html .= '</li>';
	}
$html .= '</ul>';
}
$html .= '</li>
</ol>
</td>
</tr>
</table>
';

$this->load->library('Pdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("CRM DAILY REPORT");
$pdf->SetSubject('Tax Invoice');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
//$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL = $filelocation . "/CRM_Daily_Report_".date('d-m-Y') . ".pdf"; //Linux
$pdf->Output($fileNL, 'F');

}
}

function monthly_reports()
{

$start_date = $this->uri->segment(3);
$end_date = $this->uri->segment(4);

$total_monthly_sales = $this->Daily_report_model->total_monthly_sales($start_date, $end_date);
$total_billed_today = explode("|",$total_monthly_sales);
$total_billed = $total_billed_today[0];
$total_billed_amount = $total_billed_today[1];
$total_billed_qty = $total_billed_today[2];

$previous_year_start = date("Y-m-d",strtotime($start_date."-1 year"));
$previous_year_end = date("Y-m-d",strtotime($end_date."-1 year"));
$getPreviousYearCurrentSales = $this->Daily_report_model->getPreviousYearCurrentSales($previous_year_start, $previous_year_end);
$arr = explode('|', $getPreviousYearCurrentSales);
$salesno = $arr[0];
$salesqty = $arr[1];

$getCurrentYearCurrentSales = $this->Daily_report_model->getPreviousYearCurrentSales($start_date, $end_date);
$arr1 = explode('|', $getCurrentYearCurrentSales);
$salesno1 = $arr1[0];
$salesqty1 = $arr1[1];

$previous_month_start = date("Y-m-01",strtotime("-1 month"));
$previous_month_end = date("Y-m-t",strtotime("-1 month"));

// echo $previous_month_start.'<br>'.$previous_month_end;exit;

$getPreviousMonthSales = $this->Daily_report_model->getPreviousYearCurrentSales($previous_month_start, $previous_month_end);
$arr2 = explode('|', $getPreviousMonthSales);
$salesno2 = $arr2[0];
$salesqty2 = $arr2[1];

$getAllPreviousMonthCustomers = $this->Daily_report_model->getAllPreviousMonthCustomers();
$getAllCurrentMonthCustomers = $this->Daily_report_model->getAllCurrentMonthCustomers();

$loss = count(array_diff($getAllPreviousMonthCustomers, $getAllCurrentMonthCustomers));
$gain = count(array_diff($getAllCurrentMonthCustomers, $getAllPreviousMonthCustomers));

$getAllUsers = $this->Daily_report_model->getAllUsers();
$getQuotationSentLeadStage = $this->Daily_report_model->getQuotationSentLeadStage();
$getLeadConversionLeadStage = $this->Daily_report_model->getLeadConversionLeadStage();
$getPaymentOverdue = $this->Daily_report_model->getPaymentOverdue();
$getStockNotSold = $this->Daily_report_model->getStockNotSold();


$html='
<table width="100%" border="1" ruled="all" style="text-align:center; padding:3px; background-color:lightgrey;">
<tr>
<td>
<b>MONTHLY REPORT</b>
</td>
</tr>
</table>
<table width="100%" border="1" ruled="all" style=" padding:3px;">
<tr>
<td style="font-size:14px !important;">
<ol style="padding:0px; margin:0px;">
<li><strong>This Month Sales : </strong><span style="color:red">'.$total_billed.'</span> : <span style="color:red"> ₹'.$total_billed_amount.'</span> : <span style="color:red">'.$total_billed_qty.' ltr</span></li>
<li><strong>Last year same month comparison : </strong> Total Sales :<span style="color:red"> ₹'.$salesno.'</span> : Sales Qty : <span style="color:red">'.$salesqty.' ltr</span></li>
<li><strong>Current year same month comparison: </strong> <span style="color:red"> ₹'.$salesno1.'</span> : Sales Qty : <span style="color:red">'.$salesqty1.' ltr</span></li>
<li><strong>Last month comparison : </strong> <span style="color:red"> ₹'.$salesno2.'</span> : Sales Qty : <span style="color:red">'.$salesqty2.' ltr</span></li>
<li><strong>Customers lost & gained this month : </strong>Total Lost : <span style="color:red"> '.$loss.'</span> : Total Gained : <span style="color:red">'.$gain.'</span></li>
<li><strong>Employee attendance and performance : </strong><p></p>
                            <table width="100%" border="1" ruled="all" style=" padding:3px;">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Employee Name</th>
                                  <th>Working Days</th>
                                  <th>Present</th>
                                  <th>Leaves</th>
                                  <th>Leads</th>
                                  <th>Quotations Sent</th>
                                  <th>Leads Converted</th>
                                </tr>
                              </thead>
                              <tbody>';
                            if($getAllUsers != '') {
                                $i = 1; 
                                foreach($getAllUsers as $row3) {

                                	if($this->uri->segment(3)) {
                              			$totaldays = date("t", strtotime($start_date));
                            		} else {
			                            $totaldays =  date('t');
			                        }

                      
                                  $num_sundays=0;
                                  $totalworkingdays = $totaldays-$num_sundays;

                                  $q = $this->db->select('id')->from('mark_your_attendance_view')->where('employee_id',$row3->user_id)->where('attendance_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"')->where('absent_status','0')->get();

                                  $q1 = $this->db->select('id')->from('mark_your_attendance_view')->where('employee_id',$row3->user_id)->where('attendance_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"')->where('absent_status','1')->get();

                                  $query = $this->db->select('')->from('leave_application_view')->where('employee_id',$row3->user_id)->where('from_loc BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"')->get();

                                  $getMonthQuotationSent = $this->Daily_report_model->getMonthQuotationSent($start_date, $end_date, $row3->user_id, $getQuotationSentLeadStage);
                                   $getMonthConversion = $this->Daily_report_model->getMonthQuotationSent($start_date, $end_date, $row3->user_id, $getLeadConversionLeadStage);

                                	$html .= '<tr><td>'.$i.'</td>';
                                	$html .= '<td>'.$row3->first_name.' '.$row3->last_name.'</td>';
                                	$html .= '<td>'.$totalworkingdays.'</td>';
                                	$html .= '<td>'.count($q->result()).'</td>';
                                	$html .= '<td>'.count($q1->result()).'</td>';
                                	$html .= '<td>'.count($query->result()).'</td>';
                                	$html .= '<td>'.$getMonthQuotationSent.'</td>';
                                	$html .= '<td>'.$getMonthConversion.'</td></tr>';

                                	$i++;
                                }
                            }
                    $html .= '</tbody>
                              </table>
</li>
<li><strong>Payment Stuck for More Than 100 days : </strong><span style="color:red"> '.$getPaymentOverdue.'</span></li>
<li><strong>Stock Not Sold for More Than 100 days : </strong><span style="color:red"> '.$getStockNotSold.'</span></li>
</ol>
</td>
</tr>
</table>
';
$this->load->library('Pdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("CRM MONTHLY REPORT");
$pdf->SetSubject('Tax Invoice');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
}

function type_1_invoice()
{

$location_id = $this->uri->segment(3);
// echo $location_id;exit;
$start_date = $this->uri->segment(4);
$end_date = $this->uri->segment(5);

$start_end_month = array();
$month = strtotime($start_date);
$end = strtotime($end_date);


while($month <= $end) {
	$monthstart=date('Y-m-01',$month);
	$monthend=date('Y-m-t',$month);

	$start_end_month[] = date('F Y', strtotime($monthstart));

	// echo $monthstart;
	$month = strtotime("+1 month", $month);

	// echo strtotime($month;exit;
}

$billing_month = '';

if(count($start_end_month) > 0) {
	$billing_month = implode(', ', $start_end_month);
}
// $getHPCLLocation = $this->Daily_report_model->getHPCLLocation($location_id);
// $getApprovalDetails = $this->Daily_report_model->getApprovalDetails($start_date, $end_date);

// $grandtotal_claim = array();
// $grandtotal_claim[] = 0;
// if($getApprovalDetails != '') {
// 	foreach ($getApprovalDetails as $row) {

// 		if($row->combination == 0) {
// 			$totalclaim = array();
//     	 	$totalclaim[] = 0;
// 			$getApprovalProductDetails = $this->Daily_report_model->getApprovalProductDetails($row->id, $location_id);

// 			if($getApprovalProductDetails != '') {
// 				foreach ($getApprovalProductDetails as $row1) {
// 						$product = $row1->product_id;
// 		                $vli = $row1->credit_vli;
// 		                $moq = $row1->moq;
// 		                $product_density = $row1->density;
// 		                $product_pack_type = $row1->pack_type;
// 		                $approval_pack_size = $row1->pack_size;
// 		                $valid_from = $row1->validity_from;
// 		                $valid_to = $row1->validity_to;
// 		                $location = $row1->location;

// 		                $claim = 0;
// 		                $qty = array();
// 		                $getInventoryDetails = $this->Daily_report_model->getInventoryDetails($valid_from, $valid_to, $product, $location);

// 		                if($getInventoryDetails != '') {
// 							foreach ($getInventoryDetails as $row2) {
// 								$qty[] = $row2->qty;
// 							}
// 						}

// 						if($moq > 0) {
// 		                    if($product_pack_type=="BULK" && $approval_pack_size==5) {
// 		                        $converted_moq = $moq/$product_density;
// 		                    } else {
// 		                        $converted_moq = $moq;
// 		                    }
// 		                } else {
// 		                    $converted_moq = 0;
// 		                }

// 		                if(count($qty) > 0) {
// 		                    $purchased_qty = array_sum($qty);
// 		                } else {
// 		                    $purchased_qty = 0;
// 		                }

// 		                if($purchased_qty >= $converted_moq) {
// 		                    $claim=$purchased_qty*$vli;
// 		                } else {
// 		                    $diff=$converted_moq-$purchased_qty;
// 		                }

// 		                $totalclaim[] = $claim;
// 				}
// 			}

// 			$grandtotal_claim[] = array_sum($totalclaim);
// 		} else {
// 			$getApprovalFormProductDetails = $this->Daily_report_model->getApprovalFormProductDetails($row->id);

// 			if($getApprovalFormProductDetails != '') {
// 				foreach ($getApprovalFormProductDetails as $row3) {
// 					$getApprovalCombinationType1 = $this->Daily_report_model->getApprovalCombinationType1($row->id);
// 				}
// 			}
// 		}
// 	}
// }

$getApprovalFormProductDetails = $this->Daily_report_model->getApprovalFormProductDetails($location_id);

// echo "<pre>";print_r($getApprovalFormProductDetails);exit;

if($getApprovalFormProductDetails != '') {
	foreach ($getApprovalFormProductDetails as $row);
		$buyer_name=$row->name;
        $buyer_address=$row->address;
        $buyer_gst=$row->gst;
        $buyer_state=$row->state;

        // echo $buyer_state;exit;

        $getStateNameCode = $this->Daily_report_model->getStateNameCode($buyer_state);

        if($getStateNameCode != '') {
        	$arr = explode('|', $getStateNameCode);
        	$state_name = $arr[0];
        	$state_code = $arr[1];
        } else {
        	$state_name = '';
        	$state_code = '';
        }

        // echo $state_name;exit;

       $getStoreRackLocation = $this->Daily_report_model->getStoreRackLocation(3); 

       // echo "<pre>";print_r($getStoreRackLocation);exit;

       if($getStoreRackLocation != '') {
       		foreach($getStoreRackLocation as $row1);
	       		$billing_company_name = $row1->sunder_company_name;
	        	$billing_company_address = $row1->sunder_company_address;
		        $billing_company_pincode = $row1->sunder_pincode;
		        $billing_company_state = $row1->sunder_state;
		        $billing_company_city = $row1->sundercity;
		        $seller_gst = $row1->sunder_gst;
		        $billing_company_email = $row1->sunder_email;
		        $bank_details = $row1->bank_details;
       } else {
       			$billing_company_name = '';
	        	$billing_company_address = '';
		        $billing_company_pincode = '';
		        $billing_company_state = '';
		        $billing_company_city = '';
		        $seller_gst = '';
		        $billing_company_email = '';
		        $bank_details = '';
       }

        $getStateNameCode1 = $this->Daily_report_model->getStateNameCode($billing_company_state);

        if($getStateNameCode1 != '') {
        	$arr1 = explode('|', $getStateNameCode1);
        	$seller_state = $arr1[0];
        	$seller_state_code = $arr1[1];
        } else {
        	$seller_state = '';
        	$seller_state_code = '';
        }

        $billing_company_city = $this->Daily_report_model->getCityName($billing_company_city);
}



$html = '
<p style="text-align:center; font-size:14px;">Tax-Invoice</p>

<table width="100% " style="padding:0px;" >
<tr>
<td width="50%" style="border:1px solid black;">
<table width="100%" style="padding:3px;" >
<tr>
<td style="font-size:13px; padding:3px; border-bottom:1px solid black; height:140px;"><b>'.$billing_company_name.'</b><br><br>
<span style="font-size:11px; margin:0px;">'.$billing_company_address.'<br>
GSTIN/UIN: '.$seller_gst.'<br>
State Name :  '.$seller_state.', Code : '.$seller_state_code.'<br>
E-Mail : '.$billing_company_email.'
</span>
</td>
</tr>
<tr>
<td style="font-size:13px; padding:3px; height:140px;">
<span style="font-size:11px; margin:0px;">Buyer</span><br>
<b>'.$buyer_name.'</b><br>
<span style="font-size:11px; margin:0px;">'.$buyer_address.'<br/>
GSTIN/UIN	:'.$buyer_gst.'<br>
State Name 	    : '.$state_name.',<br>CODE :- '.$state_code.',

</span>
</td>
</tr>
</table>
</td>
<td width="50%" style="padding:3px;">
<table width="100%" style="padding:3px;" border="1" rule="all">
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Invoice No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.date('d F Y').'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Mode/Terms of Payment</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Supplier’s Ref.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Other Reference(s)</span> <br><b>'.$billing_month.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Buyer’s Order No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.date('d F Y').'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatch Document No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note Date</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatched through</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Destination</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px; height:70px;" colspan="2"><span style="font-size:11px; margin:0px;">Terms of Delivery</span> <br><b></b></td>
</tr>
</table>
</td>
</tr>
</table>';

$claim_this_month=$this->Salescrm_model->this_month_claim_list_consolidated_amount($this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
$claim_this_month=explode('|',$claim_this_month);

$total_claim_amt = $claim_this_month[0] - $claim_this_month[1];

$html .= '<table style="width:100%; padding:3px; font-size:11px; " >
<tr>
<td width="6%" style="text-align:center; border:1px solid black;">S.No.</td>
<td width="30%" style="text-align:center; border:1px solid black;">Description of Goods</td>
<td width="10%" style="text-align:center; border:1px solid black;">HSN/SAC</td>
<td width="10%" style="text-align:center; border:1px solid black;">Quantity</td>
<td width="10%" style="text-align:center; border:1px solid black;">Rate</td>
<td width="10%" style="text-align:center; border:1px solid black;">per</td>
<td width="24%" style="text-align:center; border:1px solid black;">Amount</td>
</tr>';

$html .= '<tr>
<td style="border-left:1px solid black; text-align:center;"></td>
<td style="border-left:1px solid black;"><i><b>PRICE DIFFERENCE M/O '.$billing_month.' soft copy mail for approval</b></i></td>
<td style="border-left:1px solid black;">9986</td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$total_claim_amt.'</b></td>
</tr>';

$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=(($total_claim_amt)*9)/100;
            // $sgst=round(($gst/2),2);
            // $igst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT CGST @9%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>
                        <tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT SGST @9%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.floatval($gst).'</b></td>
                        </tr>';
            $total_gst = $gst+$gst;

        }else
        {
            $gst=(($total_claim_amt)*18)/100;
            // echo $gst;exit;
            $igst=$gst;
            $cgst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>GST @18%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.floatval($gst).'</b></td>
                        </tr>';

            $total_gst = $gst;
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }

    $final_amt = $total_claim_amt + $total_gst;
	$final_amt_round = round($final_amt);
	$final_amt_wo_round = $final_amt;
	$amt_diff = bcsub($final_amt_round, $final_amt_wo_round, 2);

$html .= '<tr>
                <td style="border-left:1px solid black; text-align:center;"></td>
                <td style="border-left:1px solid black; text-align:right;"><b>Round Off</b></td> 
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.floatval($amt_diff).'</b></td>
                </tr>';

$html .= '<tr>
<td style="border:1px solid black; text-align:center;"></td>
<td style="border:1px solid black; text-align:right;">Total</td> 
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black; border-right:1px solid black; text-align:right;"><b>Rs. '.round($final_amt).'</b></td>
</tr>';

$html .= '</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td style="height:80px;">
<span>Amount Chargeable (in words)</span><br>
<b>INR '.ucwords($this->Daily_report_model->getCurrencyCode(round($final_amt))).'</b>
</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td width="40%" style="text-align:center;">HSN/SAC</td>
<td width="10%" style="text-align:center;">Taxable Value</td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=(($total_claim_amt)*9)/100;
$html .= '<td width="15%" style="text-align:center;">CGST 9%</td>
<td width="15%" style="text-align:center;">SGST 9%</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=(($total_claim_amt)*18)/100;
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td width="30%" style="text-align:center;">GST 18%</td>';
        }
        
    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td width="20%" style="text-align:center;">Total Tax Amount</td>
</tr>';

$getType1TransportationProductDetails = $this->Daily_report_model->getType1TransportationProductDetails($location_id);

if($getType1TransportationProductDetails != '') {
    foreach($getType1TransportationProductDetails as $row4);
    
    $hsncode = $row4->hsncode;

$html .= '<tr>
<td>9986</td>
<td>'.$total_claim_amt.'</td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=(($total_claim_amt)*9)/100;
$html .= '<td>'.$gst.'</td>
          <td>'.$gst.'</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=(($total_claim_amt)*18)/100;
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td>'.$total_gst.'</td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td>'.floatval($total_gst).'</td>
</tr>';
}
$html .= '<tr>
<td style="text-align:right;">Total</td>
<td></td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=(($total_claim_amt)*9)/100;
$html .= '<td></td>
          <td></td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=(($total_claim_amt)*18)/100;
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td></td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td>'.floatval($total_gst).'</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr><td><span>Tax Amount (in words)  :</span><b>INR '.ucwords($this->Daily_report_model->getCurrencyCode(round($total_gst))).'</b><br><br><br><br>
<table style="width:100%;  font-size:12px;">
<tr>
<td width="70%">
<table style="width:100%;  font-size:12px;">
<tr>
<td>Company’s PAN  </td>
<td>: <b>'.substr($seller_gst,2,11).'</b></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td>Bank Details</td>
<td>: <b>'.$bank_details.'</b></td>
</tr>
</table>
</td>
</tr>
<br><br><br><br>
<tr>
<td><span style="font-size:9px;">Declaration<br>
We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.

</span></td>
<td style="border:1px solid black;">

<p style="text-align:right; margin-bottom:50px;"><b>for '.$billing_company_name.'</b></p>
<p style="text-align:right; font-size:11px;">Authorised Signatory</p>
</td>
</tr>
</table>
</td></tr>

</table>';

$this->load->library('Pdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("Type I Invoice");
$pdf->SetSubject('Tax Invoice');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
}

function type_2_invoice()
{

$start_date = $this->uri->segment(3);
$end_date = $this->uri->segment(4);
$location_id = $this->uri->segment(5);

$start_end_month = array();
$month = strtotime($start_date);
$end = strtotime($end_date);


while($month <= $end) {
	$monthstart=date('Y-m-01',$month);
	$monthend=date('Y-m-t',$month);

	$start_end_month[] = date('F Y', strtotime($monthstart));

	// echo $monthstart;
	$month = strtotime("+1 month", $month);

	// echo strtotime($month;exit;
}

$billing_month = '';

if(count($start_end_month) > 0) {
	$billing_month = implode(', ', $start_end_month);
}

$getApprovalFormProductDetails = $this->Daily_report_model->getApprovalFormProductDetails($location_id);

// echo "<pre>";print_r($getApprovalFormProductDetails);exit;

if($getApprovalFormProductDetails != '') {
	foreach ($getApprovalFormProductDetails as $row);
		$buyer_name=$row->name;
        $buyer_address=$row->address;
        $buyer_gst=$row->gst;
        $buyer_state=$row->state;

        // echo $buyer_state;exit;

        $getStateNameCode = $this->Daily_report_model->getStateNameCode($buyer_state);

        if($getStateNameCode != '') {
        	$arr = explode('|', $getStateNameCode);
        	$state_name = $arr[0];
        	$state_code = $arr[1];
        } else {
        	$state_name = '';
        	$state_code = '';
        }

        $getStoreRackLocation = $this->Daily_report_model->getStoreRackLocation(3); 

       if($getStoreRackLocation != '') {
       		foreach($getStoreRackLocation as $row1);
	       		$billing_company_name = $row1->sunder_company_name;
	        	$billing_company_address = $row1->sunder_company_address;
		        $billing_company_pincode = $row1->sunder_pincode;
		        $billing_company_state = $row1->sunder_state;
		        $billing_company_city = $row1->sundercity;
		        $seller_gst = $row1->sunder_gst;
		        $billing_company_email = $row1->sunder_email;
		        $bank_details = $row1->bank_details;
       } else {
       			$billing_company_name = '';
	        	$billing_company_address = '';
		        $billing_company_pincode = '';
		        $billing_company_state = '';
		        $billing_company_city = '';
		        $seller_gst = '';
		        $billing_company_email = '';
		        $bank_details = '';
       }

        $getStateNameCode1 = $this->Daily_report_model->getStateNameCode($billing_company_state);

        if($getStateNameCode1 != '') {
        	$arr1 = explode('|', $getStateNameCode1);
        	$seller_state = $arr1[0];
        	$seller_state_code = $arr1[1];
        } else {
        	$seller_state = '';
        	$seller_state_code = '';
        }

        $billing_company_city = $this->Daily_report_model->getCityName($billing_company_city);
}

$html = '<p style="text-align:center; font-size:14px;">Tax-Invoice</p>

<table width="100% " style="padding:0px;" >
<tr>
<td width="50%" style="border:1px solid black;">
<table width="100%" style="padding:3px;" >
<tr>
<td style="font-size:13px; padding:3px; border-bottom:1px solid black; height:140px;"><b>'.$billing_company_name.'</b><br><br>
<span style="font-size:11px; margin:0px;">'.$billing_company_address.'<br>
GSTIN/UIN: '.$seller_gst.'<br>
State Name :  '.$seller_state.', Code : '.$seller_state_code.'<br>
E-Mail : '.$billing_company_email.'
</span>
</td>
</tr>
<tr>
<td style="font-size:13px; padding:3px; height:140px;">
<span style="font-size:11px; margin:0px;">Buyer</span><br>
<b>'.$buyer_name.'</b><br>
<span style="font-size:11px; margin:0px;">'.$buyer_address.'<br/>
GSTIN/UIN	:'.$buyer_gst.'<br>
State Name 	    : '.$state_name.',<br>CODE :- '.$state_code.',

</span>
</td>
</tr>
</table>
</td>
<td width="50%" style="padding:3px;">
<table width="100%" style="padding:3px;" border="1" rule="all">
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Invoice No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.date('d F Y').'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Mode/Terms of Payment</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Supplier’s Ref.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Other Reference(s)</span> <br><b>'.$billing_month.'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Buyer’s Order No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dated</span> <br><b>'.date('d F Y').'</b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatch Document No.</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Delivery Note Date</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Dispatched through</span> <br><b></b></td>
<td style="font-size:12px; padding:3px;"><span style="font-size:11px; margin:0px;">Destination</span> <br><b></b></td>
</tr>
<tr>
<td style="font-size:12px; padding:3px; height:70px;" colspan="2"><span style="font-size:11px; margin:0px;">Terms of Delivery</span> <br><b></b></td>
</tr>
</table>
</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:11px; " >
<tr>
<td width="6%" style="text-align:center; border:1px solid black;">S.No.</td>
<td width="30%" style="text-align:center; border:1px solid black;">Description of Goods</td>
<td width="10%" style="text-align:center; border:1px solid black;">HSN/SAC</td>
<td width="10%" style="text-align:center; border:1px solid black;">Quantity</td>
<td width="10%" style="text-align:center; border:1px solid black;">Rate</td>
<td width="10%" style="text-align:center; border:1px solid black;">per</td>
<td width="24%" style="text-align:center; border:1px solid black;">Amount</td>
</tr>';


$claim_amount=$this->Salescrm_model->check_for_applicable_approvals_new_one($this->uri->segment(3),$this->uri->segment(4),'ALL',$this->uri->segment(6));
$d=$this->db->select('final_price')->from('type_report_final')->where('period',date('Y-m-01',strtotime($this->uri->segment(3))))->where('type',4)->get();
if($d->num_rows()>0)
{
  foreach($d->result() as $ro);
  if($ro->final_price>0)
  {
    $claim_amount=$ro->final_price;
  }else
  {
    $claim_amount=$claim_amount;
  }

}
$grandtotal_claim = round($claim_amount,2);

$html .= '<tr>
<td style="border-left:1px solid black; text-align:center;"></td>
<td style="border-left:1px solid black;"><i><b>PRICE DIFFERENCE M/O '.$billing_month.' soft copy mail for approval</b></i></td>
<td style="border-left:1px solid black;">9986</td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black;"></td>
<td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$grandtotal_claim.'</b></td>
</tr>';

$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=round(($grandtotal_claim*9)/100,2);
            // $sgst=round(($gst/2),2);
            // $igst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT CGST @9%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>
                        <tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>OUTPUT SGST @9%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>';
            $total_gst = $gst+$gst;

        }else
        {
            $gst=round(($grandtotal_claim*18)/100,2);
            $igst=$gst;
            $cgst=0;

            $html .= '<tr>
                        <td style="border-left:1px solid black; text-align:center;"></td>
                        <td style="border-left:1px solid black; text-align:right;"><i><b>GST @18%</b></i></td> 
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black;"></td>
                        <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.$gst.'</b></td>
                        </tr>';

            $total_gst = $gst;
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }


$final_amt = $grandtotal_claim + $total_gst;

$final_amt_round = round($final_amt);

$final_amt_wo_round = $final_amt;
//echo $final_amt_round.'<br>'.$final_amt_wo_round;exit;
// $amt_diff = $final_amt_round - $final_amt_wo_round;
$amt_diff = bcsub($final_amt_round, $final_amt_wo_round, 2);
// echo $amt_diff;exit;

if($amt_diff != 0) {
	$html .= '<tr>
                <td style="border-left:1px solid black; text-align:center;"></td>
                <td style="border-left:1px solid black; text-align:right;"><b>Round Off</b></td> 
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black;"></td>
                <td style="border-left:1px solid black; border-right:1px solid black; text-align:right;"><b>'.floatval($amt_diff).'</b></td>
                </tr>';
}

$html .= '<tr>
<td style="border:1px solid black; text-align:center;"></td>
<td style="border:1px solid black; text-align:right;">Total</td> 
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black; border-right:1px solid black; text-align:right;"><b>Rs. '.$final_amt_round.'</b></td>
</tr>';

$html .= '</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td style="height:80px;">
<span>Amount Chargeable (in words)</span><br>
<b>INR '.ucwords($this->Daily_report_model->getCurrencyCode($final_amt)).'</b>
</td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr>
<td width="20%" style="text-align:center;">HSN/SAC</td>
<td width="30%" style="text-align:center;">Taxable Value</td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=round(($grandtotal_claim*9)/100,2);
$html .= '<td width="15%" style="text-align:center;">CGST 9%</td>
<td width="15%" style="text-align:center;">SGST 9%</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=round(($grandtotal_claim*18)/100,2);
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td width="30%" style="text-align:center;">GST 18%</td>';
        }
        
    }else
    {
        $igst=0;
        $cgst=0;
    }

$html .= '<td width="20%" style="text-align:center;">Total Tax Amount</td>
</tr>';

$getType1TransportationProductDetails = $this->Daily_report_model->getType1TransportationProductDetails($location_id);

if($getType1TransportationProductDetails != '') {
    foreach($getType1TransportationProductDetails as $row4);
    
    $hsncode = $row4->hsncode;

$html .= '<tr>
<td>9986</td>
<td>'.$grandtotal_claim.'</td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=round(($grandtotal_claim*9)/100,2);
$html .= '<td>'.$gst.'</td>
          <td>'.$gst.'</td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=round(($grandtotal_claim*18)/100,2);
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td>'.$total_gst.'</td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }
$html .= '<td>'.floatval($total_gst).'</td>
</tr>';
}
$html .= '<tr>
<td style="text-align:right;">Total</td>
<td></td>';
$total_gst = 0;
    if($buyer_gst<>'' && $seller_gst <>'')
    {
        $bscode=substr($buyer_gst,0,2);
        $sscode=substr($seller_gst,0,2);
        if($bscode==$sscode)
        {
            $gst=round(($grandtotal_claim*9)/100,2);
$html .= '<td></td>
          <td></td>';
 $total_gst = $gst+$gst;
        }else
        {
            $gst=round(($grandtotal_claim*18)/100,2);
            $igst=floatval($gst);
            $cgst=0;
         $total_gst = $gst;

         $html .= '<td></td>';
        }

    }else
    {
        $igst=0;
        $cgst=0;
    }

$html .= '<td></td>
</tr>
</table>
<table style="width:100%; padding:3px; font-size:12px;" border="1">
<tr><td><span>Tax Amount (in words)  :</span><b>INR '.ucwords($this->Daily_report_model->getCurrencyCode($total_gst)).'</b><br><br>
<table style="width:100%;  font-size:12px;">
<tr>
<td width="70%">
<table style="width:100%;  font-size:12px;">
<tr>
<td>Company’s PAN  </td>
<td>: <b>'.substr($seller_gst,2,11).'</b></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td>Bank Details</td>
<td>: <b>'.$bank_details.'</b></td>
</tr>
</table>
</td>
</tr>
<br><br><br><br>
<tr>
<td><span style="font-size:9px;">Declaration<br>
We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.

</span></td>
<td style="border:1px solid black;">

<p style="text-align:right; margin-bottom:50px;"><b>for '.$billing_company_name.'</b></p>
<p style="text-align:right; font-size:11px;">Authorised Signatory</p>
</td>
</tr>
</table>
</td></tr>

</table>';

$this->load->library('Pdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("Type II/III Invoice");
$pdf->SetSubject('Tax Invoice');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
}
}

?>