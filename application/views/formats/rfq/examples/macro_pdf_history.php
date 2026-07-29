<?php
$reason='';
$evidence='';
define('redirectpath','https://hongyijig.in/index.php/Proposal/preview_macro_plan/');
$del=0;
$order_payment_date='';
$lead_id = $this->uri->segment(4);
$version = $this->uri->segment(5);
$CI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$getMacroPlanningData = $CI->salescrm->getMacroPlanningData($lead_id);
$ext_service=$CI->salescrm->getServiceAmt($lead_id);
$getMacroPlanningCommonIDs = $CI->salescrm->getMacroPlanningCommonIDs($lead_id);
$getToolingDays = $CI->salescrm->getToolingDays($lead_id);
$project_name = $CI->salescrm->getProjectName($lead_id);
// $cavitation = $CI->salescrm->getBOMCavity($lead_id);
$total_moulds = $CI->salescrm->getTotalMoulds($lead_id);
$getLeadDetails = $CI->salescrm->getLeadDetails($lead_id);
// $total_components = $CI->salescrm->getTotalComponentsForMacroPlanning($lead_id);
$checkIfAddSerExist = $CI->salescrm->getServiceAmt($lead_id);
$order_punched_on = $CI->salescrm->getOrderPunchDate($lead_id);
$order_payment_date = $CI->salescrm->getOrderfirst_paymentDate($lead_id);

$customer_name = '';
$unique_id = '';

if($getLeadDetails != '') {
foreach ($getLeadDetails as $row3);
$customer_name = $row3->customer_name;
$unique_id = $row3->unique_id;
}

$sql2 = $this->db->select('delivery_type')
->from('bom_pi_sales_project_info')
->where('lead_id', $lead_id)
->get();
if($sql2->num_rows()>0)
{
foreach($sql2->result() as $delrow);
$del=$delrow->delivery_type;

}

$DI =& get_instance();
$DI->load->model('Sourcing_model', 'sourcing');
$getToolingTimeForMacro = $DI->sourcing->getToolingTimeForMacro($this->uri->segment(3));

$b_0_total = ''; 
$b_1_total = ''; 
$b_2_total = ''; 
$b_3_total = ''; 
$b_4_total = ''; 
$b_5_total = ''; 
$kickoffdate = ''; 


$getQuotationToolingDetails=$CI->salescrm->getQuotationToolingDetailsOnlyFinal($lead_id);
$quote_tool=explode('~',$getQuotationToolingDetails);




if($quote_tool[8]==0)
{
echo "Please fill the final timelines negotiated with client <a href='".page_url."Proposal/edit_timelines/".$lead_id."' target='_blank'>here</a>"; exit;
}else
{

if($getToolingTimeForMacro != '') {
foreach($getToolingTimeForMacro as $rows);
$b_0_total = $rows->tooling_days; 

}


// need to make it 10%
$b_0_total = round($quote_tool[9]);;
$b_1_total = round($quote_tool[1]); 
$b_2_total = round($quote_tool[3]); 
$b_3_total = round($quote_tool[5]); 
$b_4_total = round($quote_tool[7]); 
$b_5_total = round($quote_tool[12]); 

$b_0_total_int = round($quote_tool[9]-($quote_tool[9]*0.16));; 
$b_1_total_int = round($quote_tool[1]-($quote_tool[1]*0.16)); 
$b_2_total_int = round($quote_tool[3]-($quote_tool[3]*0.16)); 
$b_3_total_int = round($quote_tool[5]-($quote_tool[5]*0.16)); 
$b_4_total_int = round($quote_tool[7]-($quote_tool[7]*0.16)); 
$b_5_total_int = round($quote_tool[12]-($quote_tool[12]*0.16)); 
$kickoffdate=$quote_tool[13];

$b0_extra=$b_0_total-$b_0_total_int;
$b1_extra=$b_1_total-$b_1_total_int;
$b2_extra=$b_2_total-$b_2_total_int;
$b3_extra=$b_3_total-$b_3_total_int;
$b4_extra=$b_4_total-$b_4_total_int;
$b5_extra=$b_5_total-$b_5_total_int;

}

$note='';
if($kickoffdate!='')
{
if(strtotime($kickoffdate)==strtotime($order_payment_date))
{

$note='';
$payment_date = date('Y-m-d', strtotime($order_payment_date));
}else if(strtotime($kickoffdate)>strtotime($order_payment_date))
{

$note='';
$payment_date = date('Y-m-d', strtotime($order_payment_date));
}else
{

$note='Note* Kick Off Date promised was '.date('d-m-Y',strtotime($kickoffdate))." but payment was received on ".date('d-m-Y',strtotime($order_payment_date))." so project start date will be from Payment Date";
$payment_date = date('Y-m-d', strtotime($order_payment_date));
}

}else
{
$payment_date = date('Y-m-d', strtotime($order_payment_date));
}



$project_leader_name=$CI->salescrm->getProjectManager_name($this->uri->segment(3));


/** QUERY ENDS **/
require_once('tcpdf_include.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
require_once(dirname(__FILE__) . '/lang/eng.php');
$pdf->setLanguageArray($l);
}
$pdf->AddPage('L', 'A4');
// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 10, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.

// Call before the addPage() method
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
// set text shadow effect
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print

/** quert paet **/

$html = '

<table width="100%" style="padding:3px; ">
<tr>
<td style="font-size:25px; text-align:center;margin:auto; border:1px solid black; background-color:lightgrey;">
<b>Macro Planning Sheet Version '.$version.'</b>
</td>

<td style="text-align:right;">
<img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" width="250px" >
</td>
</tr>
</table>
<br><br>

<table width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<td style="background-color:lightgrey;">Query No</td>
<td>'.$unique_id.'</td>
<td style="background-color:lightgrey;">Kick Off Date</td>
<td>'.date('d-M-Y',strtotime($payment_date)).'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">Project Name</td>
<td>'.$project_name.'</td>
<td style="background-color:lightgrey;">No of Mould</td>
<td>'.$total_moulds.'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">Client Name</td>
<td>'.$customer_name.'</td>
<td style="background-color:lightgrey;">Quotation Tooling Time</td>
<td>

<strong>B0-Product Design- </strong>'.$CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),0).' Days<br><strong>B1-Pre Tooling- </strong>'.$CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),1).' Days<br><strong>B2-Tooling Activities- </strong>'.$CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),2).' Days<br><strong>B3-Post Tooling- </strong>'.$CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),3).' Days<br/><strong>B4-Shipment- </strong>'.$CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),4).' Days<BR/><strong>B5-Installation- </strong>'.$CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),5).' Days

</td>
</tr>
</table>

<p>Dear Sir/Maam,<br/>Please are pleased to share with you the tentative Macro Plan for your project <b>'.ucwords(strtolower($project_name)).'</b>.</p>
<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
<tr>
<th style="background-color:lightgrey;" width="10%">S.NO.</th>
<th style="background-color:lightgrey;" width="30%">STAGE NAME</th>
<th style="background-color:lightgrey;" width="20%">START DATE</th>
<th style="background-color:lightgrey;" width="20%">END DATE</th>
<th style="background-color:lightgrey;" width="20%">TOTAL WORKING DAYS</th>
</tr>';
//echo $del; exit;
if($ext_service>0)
{
if($del==2)
{
$getMacroPlanningData=array(0=>'B0',1=>'B1',2=>'B2',3=>'B3','4'=>'B4','5'=>'B5');
}else
{
$getMacroPlanningData=array(0=>'B0',1=>'B1',2=>'B2',3=>'B3','5'=>'B5');
}
}else
{
if($del==2)
{
$getMacroPlanningData=array(1=>'B1',2=>'B2',3=>'B3','4'=>'B4','5'=>'B5');
}else
{
$getMacroPlanningData=array(0=>'B0',1=>'B1',2=>'B2',3=>'B3','5'=>'B5');
}
}

if(count($getMacroPlanningData)>0) {
$i = 1;
foreach($getMacroPlanningData as $key=>$row) { 


if($key == 0) {
$totaldays = $CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),0);
$stagename="B0-Product Design";
$ext=$b0_extra;
} else if($key == 1) {
$totaldays = $CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),1);
$stagename="B1-Pre Tooling";
$ext=$b1_extra;
} else if($key == 2) {
$totaldays =$CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),2);
$stagename="B2-Tooling Activities";
$ext=$b2_extra;
} else if($key == 3) {
$totaldays = $CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),3);
$stagename="B3-Post Tooling";
$ext=$b3_extra;
} else if($key == 4) {
$totaldays = $CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),4);
$stagename="B4-Shipment";
$ext=$b4_extra;
}
else if($key == 5) {
$totaldays = $CI->salescrm->getMacroDays($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),5);
$stagename="B5-Installation";
$ext=$b5_extra;
}


$restey=$this->db->select('start_date,end_date')->from('macro_order_planning_new_history')->where('order_won_id',$this->uri->segment(3))->where('lead_id',$this->uri->segment(4))->where('version',$this->uri->segment(5))->where('stage_id',$key)->get();
if($restey->num_rows()>0)
{

foreach($restey->result() as $rrrow);


if($key==0)
{
$start_date=date('d-m-Y',strtotime($rrrow->start_date));
$end_date=date('d-m-Y',strtotime($start_date.' +'.$b_0_total.' Days'));
}else
{
if($key==1)
{
$ex=$b_1_total;
}else if($key==2)
{
$ex=$b_2_total;
}else if($key==3)
{
$ex=$b_3_total;
}else if($key==4)
{
$ex=$b_4_total;
}else if($key==5)
{
$ex=$b_5_total;
}else
{
$ex=$b_6_total;
}
if($prev_end_date<>'')
{
$start_date=date('d-m-Y',strtotime($prev_end_date.' +1 Days'));
$end_date=date('d-m-Y',strtotime($start_date.' +'.$ex.' Days'));
}else
{
$start_date=date('d-m-Y',strtotime($rrrow->start_date));
$end_date=date('d-m-Y',strtotime($start_date.' +'.$ex.' Days'));
}
}


}else
{
$start_date='';
$end_date='';
}


$prev_end_date=$end_date;



$html.='<tr>
<td>'.$i.'</td>
<td>'.$stagename.'</td>
<td>'.$start_date.'</td>
<td>'.$end_date.'</td>
<td>'.$totaldays.'</td>
</tr>';
$i++;
}
}

$html.='</table>';

if($reason<>'')
{
$html.='<p><strong>Reason For Macro Plan Change- '.ucwords(strtolower($reason)).'</strong><br/>
    <strong>Evidence - <a href="'.page_url1.'image_bank/macro_files/'.$evidence.'" target="_blank" download>Click Here</a></strong><p>';
}

$html.='<p><strong>'.$project_leader_name.'<b><br/>Project Leader</b></p>';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/macroplanpdf/';
$fileNL = $filelocation . "Macro_Plan_Version_".$this->uri->segment(5)."_".$this->uri->segment(3).".pdf"; //Linux

$pdf->Output($fileNL, 'I');

//header('location:'.redirectpath.$this->uri->segment(3));



//============================================================+
// END OF FILE
//============================================================+
