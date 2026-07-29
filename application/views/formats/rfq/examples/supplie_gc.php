<?php
echo "hi"; exit;
//define('redirectpath','https://hongyijig.in/index.php/Proposal/preview_macro_plan/');
$del=0;
$order_won_id = $this->uri->segment(3);
$lead_id = $this->uri->segment(4);
$stage_id = $this->uri->segment(5);
$part_id = $this->uri->segment(6);

$CI =& get_instance();
$CI->load->model('Open_model', 'openmodel');

$project_name = $CI->openmodel->getProjectName($lead_id);
$total_moulds = $CI->openmodel->getTotalMoulds($lead_id);
$getLeadDetails = $CI->openmodel->getLeadDetails($lead_id);
$checkIfAddSerExist = $CI->openmodel->getServiceAmt($lead_id);
$order_punched_on = $CI->openmodel->getOrderPunchDate($lead_id);
$part_details = $CI->openmodel->getPartDetailInfo($part_id);
$supplier_name = $CI->openmodel->getSupplierName($order_won_id);
if($part_details<>'')
{
    foreach($part_details as $partDetails);
    $partname=$partDetails->partname;
    $partcode=$partDetails->code;
    $partimage=$partDetails->image;
}else
{
    $partname='';
    $partcode='';
    $partimage='';
}


$holiday_dates = $CI->openmodel->getholiday_dates_china();
$customer_name = '';
$unique_id = '';

if($getLeadDetails != '') {
  foreach ($getLeadDetails as $row3);
    $customer_name = $row3->customer_name;
    $unique_id = $row3->unique_id;
}

$get_supplier_stages = $CI->openmodel->get_supplier_stages();
$get_po_approved_date=$CI->openmodel->get_po_approved_date($order_won_id);




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
<b>Macro Planning Sheet</b>
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
<td style="background-color:lightgrey;">Order Punching Date</td>
<td>'.date('d-M-Y',strtotime($order_punched_on)).'</td>
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
<td><strong>B0- </strong>'.$b_0_total.' Days<br><strong>B1- </strong>'.$b_1_total.' Days<br><strong>B2- </strong>'.$b_2_total.' Days<br><strong>B3- </strong>'.$b_3_total.' Days<br/> <strong>B4- </strong>'.$b_4_total.' Days</td>
</tr>
</table>

<p>Dear Sir/Maam,</p>
<p>Please are pleased to share with you the tentative Macro Plan for your project <b>'.ucwords(strtolower($project_name)).'</b>.</p>

<br>
<br>
<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
<tr>
<th style="background-color:lightgrey;" width="10%">S.NO.</th>
<th style="background-color:lightgrey;" width="30%">STAGE NAME</th>
<th style="background-color:lightgrey;" width="20%">START DATE</th>
<th style="background-color:lightgrey;" width="20%">END DATE</th>
<th style="background-color:lightgrey;" width="20%">TOTAL WORKING DAYS</th>
</tr>';

if($ext_service>0)
                           {
                            if($del==2)
                            {
                           $getMacroPlanningData=array(0=>'B0',1=>'B1',2=>'B2',3=>'B3','4'=>'B4');
                            }else
                            {
                                $getMacroPlanningData=array(0=>'B0',1=>'B1',2=>'B2',3=>'B3');
                            }
                           }else
                           {
                            if($del==2)
                            {
                             $getMacroPlanningData=array(1=>'B1',2=>'B2',3=>'B3','4'=>'B4');
                            }else
                            {
                                 $getMacroPlanningData=array(0=>'B0',1=>'B1',2=>'B2',3=>'B3');
                            }
                           }

                           if(count($getMacroPlanningData)>0) {
                            $i = 1;
                                foreach($getMacroPlanningData as $key=>$row) { 
                                    

                                     if($key == 0) {
                                    $totaldays = $b_0_total;
                                    $stagename="Product Design";
                                  } else if($key == 1) {
                                    $totaldays = $b_1_total;
                                    $stagename="Pre Tooling Task";
                                  } else if($key == 2) {
                                    $totaldays = $b_2_total;
                                    $stagename="Tooling";
                                  } else if($key == 3) {
                                    $totaldays = $b_3_total;
                                    $stagename="Post Tooling Task";
                                  } else if($key == 4) {
                                    $totaldays = $b_4_total;
                                    $stagename="Shipment";
                                  }

                                   $restey=$this->db->select('start_date,end_date')->from('macro_order_planning_new')->where('order_won_id',$this->uri->segment(3))->where('lead_id',$this->uri->segment(4))->where('stage_id',$key)->get();
                                    if($restey->num_rows()>0)
                                    {
                                        foreach($restey->result() as $rrrow);
                                        $start_date=date('d-m-Y',strtotime($rrrow->start_date));
                                        $end_date=date('d-m-Y',strtotime($rrrow->end_date));

                                    }else
                                    {
                                        $start_date='';
                                        $end_date='';
                                    }



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

$html.='</table><br/><br/><p>Thank You</p><p><b>'.$project_leader_name.'<br/>Project Leader</b></p>';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/macroplanpdf/';
$fileNL = $filelocation . "Macro_Plan_".$this->uri->segment(3).".pdf"; //Linux

$pdf->Output($fileNL, 'I');

// /header('location:'.redirectpath.$this->uri->segment(3));



//============================================================+
// END OF FILE
//============================================================+
