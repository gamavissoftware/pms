<?php

  $order_won_id = $this->uri->segment(3);
  $stage_id = $this->uri->segment(4);
  $lead_id = $this->uri->segment(5);
  $checklist_id = $this->uri->segment(6);
  $service_id = $this->uri->segment(7);



  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getChecklistQuestions = $CI->salescrm->getChecklistQuestions($checklist_id);
$getMomData = $CI->salescrm->getMomData($lead_id, $stage_id);
$tablename=$CI->salescrm->getchecklisttable($checklist_id);


$added_by = '';
$team_head = '';
$participants = '';
$particular = '';
$mom_date = '';
$mom_time = '';

if($getMomData != '') {
    foreach ($getMomData as $rows);
    $added_by = $rows->added_by;
    $team_head = $rows->team_head;
    $participants = $rows->participants;
    $particular = $rows->particular;
    $mom_date = $rows->mom_date;
    $mom_time = $rows->mom_time;
}

if($service_id=='' || $service_id==0)
{
$getMomPointChecklist = $CI->salescrm->getMomPointChecklist_new($lead_id, $stage_id,$checklist_id,$tablename);
}else
{
	// ONLY FOR STAGE 2 
	$getMomPointChecklist = $CI->salescrm->getMomPointChecklist_new_service($lead_id, $stage_id,$checklist_id,$tablename,$service_id);
}


 $created_by =  $CI->salescrm->getusername($added_by);
 $behalf_team_head =  $CI->salescrm->getusername($team_head);

 $participants = explode(',', $participants);
 $participants_name = array();
 foreach ($participants as  $participant) {
 	$participants_name[] =  $CI->salescrm->getusername($participant);
 }


// print_r($getMomPointChecklist);
/** QUERY ENDS **/


require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins(5, 5, 5, True);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

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
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

/** quert paet **/
//<th width="16%" style="background-color:lightgrey;">Checklist Details</th>
 
$html='
<table width="100%" style="padding:5px;">
<tr>

<td style="text-align:center;" ><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" style="width:150px;"></td>
</tr>
</table>
<table width="100%" style="padding:5px; border:1px solid black; background-color:lightgrey;">
<tr>
<td width="60%" style="font-size:16px; text-transform: uppercase;">
<b>INTERNAL MOM ('.$particular.' )</b>
</td>
<td width="20%" style="text-align:center; font-size:12px;">
DATE: '.date('d-M-Y',strtotime($mom_date)).'
</td>
<td width="20%" style="text-align:center; font-size:12px;">
TIME: '.date('h:i A',strtotime($mom_time)).'
</td>
</tr>
</table>
<table width="100%" border="1" style="padding:5px;">
<tr> 
<td style="width: 50%;">CREATED BY:<br>'.$created_by.'<br><br>CREATED ON THE BEHALF OF <br>'.$behalf_team_head.'</td>
<td style="width:50%">PARTICIPANT:<br>'.implode(', ', $participants_name).' </td>
</tr>
</table>
<table border="1" ruled="all" width="100%" style="padding:5px; text-align:center;">
<tr>
<th width="4%" style="background-color:lightgrey;">S.No.</th>

<th width="20%" style="background-color:lightgrey;">Task Particular</th>
<th width="10%" style="background-color:lightgrey;">MOM Type</th>
<th width="16%" style="background-color:lightgrey;">Department/ Assigned To</th>
<th width="10%" style="background-color:lightgrey;">Customer Name</th>
<th width="15%" style="background-color:lightgrey;">Due Date & Time</th>
<th width="12%" style="background-color:lightgrey;">Completion Time</th>
<th width="13%" style="background-color:lightgrey;">Work Status</th>
</tr>';
$str= '';
	if($getMomPointChecklist != '') {
		$i= 1;
     foreach($getMomPointChecklist as $row1) {
     	$k =1;
			// $checkCountChecklist = $CI->salescrm->checkCountChecklist($row1->checklist_id);
			

if($service_id=='')
{
	$getMomPointChecklistDetails = $CI->salescrm->getMomPointChecklistDetails_new($lead_id, $stage_id,$checklist_id,$row1->checklist_id,$tablename);
}else
{
		$getMomPointChecklistDetails = $CI->salescrm->getMomPointChecklistDetails_new_service($lead_id, $stage_id,$checklist_id,$row1->checklist_id,$tablename,$service_id);
}
	// print_r($getMomPointChecklistDetails);
	 foreach($getMomPointChecklistDetails as $row2) {

	$checkCountChecklist = count($getMomPointChecklistDetails);
	$completion = '';
	if($row2->update_on != ''){
			$completion = date('d-M-Y <br> h:i A',strtotime($row2->update_on));
	}
	if($row2->workstatus == 1){
		$workstatus = 'Done';
	}else{
		$workstatus = '';
	}

	if($row2->mom_type == 1){
	    $mom_type = 'Internal';
	    $department = $row2->department;
	    $assign_to = $row2->first_name.' '.$row2->last_name;
	    $customer_name = '';
	}else if($row2->mom_type == 2){
	    $mom_type = 'External';
	    $department = '';
	    $assign_to = '';
	    $customer_name = $row2->ext_customer_name;
	}else{
	    $mom_type = '';
	    $department = '';
	    $assign_to = '';
	    $customer_name = '';
	}

$str.='<tr>';
// if($checkCountChecklist > 1){
// 	$rowspan = " rowspan=".$checkCountChecklist;
// }else{
// 	$rowspan = ''; 
// }
			// if($k == 1){

				// $str.='	<td rowspan="'.$checkCountChecklist.'">'.$i.'</td>
				// 	<td rowspan="'.$checkCountChecklist.'">
				// 					'.$row1->description.	'										
				// 			</td>';


				$str.='	<td>'.$i.'</td>';


			//}
					
				
		$str .= '<td>'.$row2->particular.'</td>
				<td>'.$mom_type. '</td>
				<td>'.$department. '<br><br>'.$assign_to.'	</td>
				<td>'.$customer_name. '</td>
				<td>'. date('d-M-Y',strtotime($row2->due_date)) .'<br><br> ' . date('h:i A',strtotime($row2->due_time)) . '</td>
				<td>'.$completion. '</td>
				<td>'.$workstatus.'<br><br>'.$row2->task_remarks. '</td>
				</tr>';

				$k++;
		 }

		 // $str .= '</tr>';
		$i++;
		}

	}


$html.= $str;
$html.= '</table>
<table width="100%" style="padding:5px;">
<tr>

								<td width="35%"><b>CHECKED BY:</b><br></td>								

								<td width="65%" style="text-align:right"><b>www.hongyijig.com</b></td>

							</tr>
                            <tr>

								<td style="border-bottom:2px solid #333;"><img src="" ></td>

								<td style="text-align:right"><img src=""></td>

							</tr>
                            <tr>

								<td ><b>jagdeepkhattar@hongyijig.com</b></td>

								<td style="text-align:right">&nbsp;</td>							

							</tr>
</table>
';	

// echo $html ; exit;
//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/popdf';
$fileNL = $filelocation."/akash.pdf"; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
