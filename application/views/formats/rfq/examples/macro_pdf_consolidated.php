<?php
$prev_end_date='';
define('redirectpath','https://hongyijig.in/index.php/Proposal/preview_macro_plan/');
$del=0;
$order_payment_date='';
$lead_id = $this->uri->segment(4);
$order_won_id=$this->uri->segment(3);
$CI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$CI->load->model('BOM_model', 'bom_model');
$CI->load->model('Sourcing_model', 'sourcing');
$CI->load->model('Open_model', 'openmodel');
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
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage('P', 'A4');
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


$html="";



$reason='';
$evidence='';
$buffer=$CI->salescrm->getBuffer($order_won_id);
$mouldcount=$CI->salescrm->getTotalMouldsNew($lead_id);
$partcount=$CI->sourcing->getPartCountOverall($lead_id);
$lead_no=$CI->openmodel->getLeadUniqueNo($lead_id);
$mould_no='';


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

$b_0_total_int = round($quote_tool[9]-($quote_tool[9]*$buffer));; 
$b_1_total_int = round($quote_tool[1]-($quote_tool[1]*$buffer)); 
$b_2_total_int = round($quote_tool[3]-($quote_tool[3]*$buffer)); 
$b_3_total_int = round($quote_tool[5]-($quote_tool[5]*$buffer)); 
$b_4_total_int = round($quote_tool[7]-($quote_tool[7]*$buffer)); 
$b_5_total_int = round($quote_tool[12]-($quote_tool[12]*$buffer)); 
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



// Set some content to print

/** quert paet **/
$getMacroPlanningData1=array();
if($ext_service>0)
{
if($del==2)
{
$getMacroPlanningData1=array('B0-Product Design'=>$b_0_total,'B1-Pre Tooling'=>$b_1_total,'B2-Tooling Activities'=>$b_2_total,'B3-Post Tooling'=>$b_3_total,'B4-Shipment'=>$b_4_total,'B5-Installation'=>$b_5_total);
}else
{
$getMacroPlanningData1=array('B0-Product Design'=>$b_0_total,'B1-Pre Tooling'=>$b_1_total,'B2-Tooling Activities'=>$b_2_total,'B3-Post Tooling'=>$b_3_total,'B5-Installation'=>$b_5_total);

}
}else
{
if($del==2)
{
$getMacroPlanningData1=array('B1-Pre Tooling'=>$b_1_total,'B2-Tooling Activities'=>$b_2_total,'B3-Post Tooling'=>$b_3_total,'B4-Shipment'=>$b_4_total,'B5-Installation'=>$b_5_total);
}else
{
$getMacroPlanningData1=array('B0-Product Design'=>$b_9_total,'B1-Pre Tooling'=>$b_1_total,'B2-Tooling Activities'=>$b_2_total,'B3-Post Tooling'=>$b_3_total,'B5-Installation'=>$b_5_total);
}
}

$timeline='';
if(count($getMacroPlanningData1)>0)
{
    foreach($getMacroPlanningData1 as $key=>$datas)
    {
        $timeline.="<strong>".$key."-</strong>".$datas." Days<br>";

    }
}

$html='<table width="100%" style="padding:3px; ">
<tr>
<td style="text-align:center;">
<img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" width="250px" >
</td>
</tr>
</table><br/><br/><br/><br/><br/><br/>';


$html.='
<table width="100%" style="padding:3px;margin-top:10px;">
<tr>
<td style="text-align:center;background-color:lightgrey;font-weight:bold;font-size:15px;">Project Details</td>
</tr>
</table><br/><br/>';
$html.='<table width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<td style="background-color:lightgrey;">Query No</td>
<td>'.$unique_id.'</td>
<td style="background-color:lightgrey;">Kick Off Date</td>
<td>'.date('d-M-Y',strtotime($payment_date)).'</td>
</tr>
<tr>
<td style="background-color:lightgrey;">Project Name</td>
<td>'.$project_name.'</td>
<td style="background-color:lightgrey;">Client Name</td>
<td>'.$customer_name.'</td>';
$html.='</tr></table><br/><br/><br/><br/>';
$html.='<table width="100%" style="padding:3px;margin-top:10px;">
<tr>
<td style="text-align:center;background-color:lightgrey;font-weight:bold;font-size:15px;">Total Project All Moulds Macro Plan</td>
</tr>
</table>';


$html.='<table width="100%" style="padding:3px;">
<tr>
<td style="text-align:center;"></td>
</tr>
</table><br/>';

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

$html.="<p><b>Total No. of Moulds-".$mouldcount."<br/>Total No. of Components-".$partcount."<b/></p>";
$html.= '<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
<tr>
<th style="background-color:lightgrey;" width="15%">S.no.</th>
<th style="background-color:lightgrey;" width="30%">Stage Name</th>
<th style="background-color:lightgrey;" width="15%">Start Date</th>
<th style="background-color:lightgrey;" width="15%">End Date</th>
<th style="background-color:lightgrey;" width="25%">Total Working Days</th>
</tr>';
if(count($getMacroPlanningData)>0) {
$ii = 1;
foreach($getMacroPlanningData as $key=>$row) { 

if($key == 0) {
$totaldays = $b_0_total;
$stagename="B0-Product Design";
$ext=$b0_extra;
} else if($key == 1) {
$totaldays = $b_1_total;
$stagename="B1-Pre Tooling";
$ext=$b1_extra;
} else if($key == 2) {
$totaldays = $b_2_total;
$stagename="B2-Tooling Activities";
$ext=$b2_extra;
} else if($key == 3) {
$totaldays = $b_3_total;
$stagename="B3-Post Tooling";
$ext=$b3_extra;
} else if($key == 4) {
$totaldays = $b_4_total;
$stagename="B4-Shipment";
$ext=$b4_extra;
}
else if($key == 5) {
$totaldays = $b_5_total;
$stagename="B5-Installation";
$ext=$b5_extra;
}

$data=$CI->salescrm->get_Macro_min_max_dates($key,$order_won_id);
$dt=explode('|',$data);

if($dt[0]<>'' && $dt[1]<>'')
{
$start_date=date('d-m-Y',strtotime($dt[0]));
$end_date=date('d-m-Y',strtotime($dt[1]." +".$ext." days"));
$wdays=$CI->salescrm->dateDiffInDays($dt[0],$end_date);
}else
{
    $end_date='';
    $start_date='';
    $wdays='';
}

$html.='<tr>
<td>'.$ii.'</td>
<td>'.$stagename.'</td>
<td>'.$start_date.'</td>
<td>'.$end_date.'</td>
<td>'.$wdays.'</td>
</tr>';

$ii++;
}
}


$html.='</table><br/><br pagebreak="true">';

$html.='
<table width="100%" style="padding:3px;margin-top:10px;">
<tr>
<td style="text-align:center;background-color:lightgrey;font-weight:bold;font-size:15px;">Mould Wise Macro Plan</td>
</tr>
</table><br/><br/><br/>';


$mDetails=$CI->salescrm->getAllMoulds($lead_id);
if(count($mDetails)>0)
{
    $t=0;
    $pname=array();
    $pimage=array();
    
    foreach($mDetails as $mould_id)
    {
        $prev_end_date='';

        $mouuid=$mould_id;

        if($mould_id>0)
{
    $mould_no=$CI->bom_model->getMouldNo($lead_id,$mould_id);
    $mould_details=$CI->bom_model->getmouldsdetailsBYID($mould_id);
if(count($mould_details)>0)
{
$ttype=$mould_details['type'];

}else
{
echo "Invalid Link";
}
$pname=array();
$pimage=array();
$getPartDetails = $CI->sourcing->getPartDetails($lead_id, $mould_id);
if($getPartDetails != '') {
$partscount= (array) $getPartDetails;
$partscount=count($partscount);
foreach($getPartDetails as $row1) {
$pname[]=$row1->name;
$pimage[]=$row1->image;
}
}

if(count($pname)>0)
{
$p=implode(', ',$pname);
}else
{
$p='';
}

$dddd='';
if(count($pimage)>0)
{
    $r=1;
    foreach($pimage as $pimages)
    {
       //$dddd.='<img src="'.SITE_ROOT.'assets/client_bom_data/part_image/'.$pimages.'" width="10%" style="width:100px;"><br/>';
        //$dddd.="<a href='".page_url1."assets/client_bom_data/part_image/".$pimages."'>Part ".$r." Image</a> , ";
    $r++;
    }
}

if($ttype == 1) {
$mould_type = 'Single';
$mt="S";
} else if($ttype == 2) {
$mould_type = 'Multi';
$mt="M";
} else if($ttype == 3) {
$mould_type = 'Family';
$mt="F";
}
}else
{
    $mt='';
    $mould_type='';
    $p='';
}
//<b>Part Picture-'.$dddd.'</b>
// echo $dddd; exit;
if($t<>0)
{
$html.='<br/>';
}


$html.='<p><b>Part Details</b></p><table width ="100%" border="1" style="padding:3px;margin-top:10px;">
<tr>
<th style="text-align:center;background-color:lightgrey;" nobr="true" >Part Name</th>
<th style="text-align:center;background-color:lightgrey;" nobr="true" >Mould Type</th>
<th style="text-align:center;background-color:lightgrey;" nobr="true" >Mould No.</th>
<th style="text-align:center;background-color:lightgrey;" nobr="true" >Part Image</th>
</tr>';
$t=0;
foreach($pname as $pname1)
{
   // echo SITE_ROOT.'assets/client_bom_data/part_image/'.$pimage[$t]; exit;
    $img='<img src="'.SITE_ROOT.'assets/client_bom_data/part_image/'.$pimage[$t].'" style="width:100px;height:100px;">';
$html.='<tr>
<td style="text-align:center;" nobr="true" >'.$pname1.'</td>
<td style="text-align:center;" nobr="true" >'.$mould_type.'</td>
<td style="text-align:center;" nobr="true" >'.$lead_no.' | '.$mould_no.$mt.'</td>
<td style="text-align:center;" nobr="true" >'.$img.'</td>
</tr>';
$t++;
}
$html.='</table><br/><br/>';

// $html.='<table width="100%" style="padding:3px;margin-top:10px;">
// <tr>
// <td style="text-align:left;"><b>Part Name-'.$p.'</b></td>
// <td style="text-align:left;"></td>
// </tr>
// <tr>
// <td style="text-align:left;"><b>Mould Type-'.$mould_type.'</b></td>
// <td style="text-align:left;"></td>
// </tr>
// <tr>
// <td style="text-align:left;"><b>Mould Number- '.$lead_no.' | '.$mould_no.$mt.'</b></td>
// <td style="text-align:left;"></td>
// </tr>
// </table>
// <br/>';


$html.='<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
<tr>
<th style="background-color:lightgrey;" width="10%" nobr="true" >S.no.</th>
<th style="background-color:lightgrey;" width="30%" nobr="true" >Stage Name</th>
<th style="background-color:lightgrey;" width="20%" nobr="true" >Start Date</th>
<th style="background-color:lightgrey;" width="20%" nobr="true" >End Date</th>
<th style="background-color:lightgrey;" width="20%" nobr="true" >Total Working Days</th>
</tr>';
//echo $del; exit;


if(count($getMacroPlanningData)>0) {
$i = 1;
foreach($getMacroPlanningData as $key=>$row) { 


if($key == 0) {
$totaldays = $b_0_total;
$stagename="B0-Product Design";
$ext=$b0_extra;
} else if($key == 1) {
$totaldays = $b_1_total;
$stagename="B1-Pre Tooling";
$ext=$b1_extra;
} else if($key == 2) {
$totaldays = $b_2_total;
$stagename="B2-Tooling Activities";
$ext=$b2_extra;
} else if($key == 3) {
$totaldays = $b_3_total;
$stagename="B3-Post Tooling";
$ext=$b3_extra;
} else if($key == 4) {
$totaldays = $b_4_total;
$stagename="B4-Shipment";
$ext=$b4_extra;
}
else if($key == 5) {
$totaldays = $b_5_total;
$stagename="B5-Installation";
$ext=$b5_extra;
}

$this->db->select('start_date,end_date,reason,evidence')->from('macro_order_planning_new')->where('order_won_id',$this->uri->segment(3))->where('lead_id',$this->uri->segment(4));

if($mouuid>0){
    if($mouuid==3279)
    {
        $a="mould_id";
    }else{
        $a="mould_id";
    }
    $this->db->where($a,$mouuid);
}

$restey=$this->db->where('stage_id',$key)->get();
if($restey->num_rows()>0)
{

foreach($restey->result() as $rrrow);

if($i==1)
{
    $reason=$rrrow->reason;
    $evidence=$rrrow->evidence;
}


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
<td nobr="true" >'.$i.'</td>
<td nobr="true" >'.$stagename.'</td>
<td nobr="true" >'.$start_date.'</td>
<td nobr="true" >'.$end_date.'</td>
<td nobr="true" >'.$totaldays.'</td>
</tr>';
$i++;
}
}

$html.='</table>';

if($reason<>'')
{
$html.='<p><strong>Reason For Macro Plan Change- '.ucwords(strtolower($reason)).'</strong><br/>
    <strong>Evidence - <a href="'.page_url1.'image_bank/macro_files/'.$evidence.'" target="_blank" download>Click Here</a></strong>';
}

$t++;
}
}
$html.='<p><strong>'.$project_leader_name.'<b><br/>Project Leader</b></p>';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/macroplanpdf/';
$fileNL = $filelocation . "Macro_Plan_".$this->uri->segment(3).".pdf"; //Linux

$pdf->Output($fileNL, 'I');

//header('location:'.redirectpath.$this->uri->segment(3));



//============================================================+
// END OF FILE
//============================================================+
