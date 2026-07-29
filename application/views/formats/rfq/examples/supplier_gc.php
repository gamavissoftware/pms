<?php
$partname='';
$partcode='';
$partimage='';
define('redirectpath','https://hongyijig.in/index.php/Proposal/preview_macro_plan/');
define('imagepath','https://hongyijig.in/assets/client_bom_data/part_image/');
$order_won_id = $this->uri->segment(3);
$lead_id = $this->uri->segment(4);
$stage_id = $this->uri->segment(5);
$part_id = $this->uri->segment(6);
// ACTUAL IT IS MOULD ID



$CI =& get_instance();
$CI->load->model('Open_model', 'openmodel');

$project_name = $CI->openmodel->getProjectName($lead_id);
$total_moulds = $CI->openmodel->getTotalMoulds($lead_id);
$getLeadDetails = $CI->openmodel->getLeadDetails($lead_id);
$lead_no=$CI->openmodel->getLeadUniqueNo($lead_id);
$checkIfAddSerExist = $CI->openmodel->getServiceAmt($lead_id);
$order_punched_on = $CI->openmodel->getOrderPunchDate($lead_id);
$part_details = $CI->openmodel->getPartDetailInfobyMould($part_id);
$supplier_name = $CI->openmodel->getSupplierName($order_won_id);
$mould_no = $CI->openmodel->getMouldNo($lead_id,$part_id);
$mtype = $CI->openmodel->getMouldName($part_id);
if($mtype==1)
    {
    $mould_type = 'Single';
    $mt="S";
    }else if($mtype==2)
    {
    $mould_type = 'Multi';
    $mt="M";
    }else if($mtype==3)
    {
    $mould_type = 'Family';
    $mt="F";
    }else
    {
    $mould_type='';
    $mt='';
    }

if($part_details<>'')
{
  foreach($part_details as $partDetails)
    {
    $partname.=$partDetails->partname."<br/>";
    $partcode.=$partDetails->code."<br/>";
    $url=assets_url."client_bom_data/part_image/".$partDetails->image;
    $partimage.="<img src='".$url."' style='width:100px;'><br/><br/>";
    }
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
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
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
<b>Supplier Mini Micro Plan</b>
</td>

<td style="text-align:right;">
<img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" width="250px" >
</td>
</tr>
</table>
<br><br>

<table width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<td style="background-color:lightgrey;">Supplier Name</td>
<td>'.$supplier_name.'</td>
<td style="background-color:lightgrey;">Project Name</td>
<td>'.$project_name.'</td>
</tr>

<tr>
<td style="background-color:lightgrey;">Mould No.</td>
<td>'.$lead_no.' | '.$mould_no.$mt.'</td>
<td style="background-color:lightgrey;">Mould Type</td>
<td>'.$mould_type.'</td>
</tr>

<tr>
<td style="background-color:lightgrey;">Part Name</td>
<td>'.$partname.'</td>
<td style="background-color:lightgrey;">Part Code</td>
<td>'.$partcode.'</td>
</tr>
</table>

<br>
<br>
<table width="100%" style="padding:3px; text-align:center;" border="1" ruled="all">
<tr>
<th style="background-color:lightgrey;" width="10%">S.NO.</th>
<th style="background-color:lightgrey;" width="40%">STAGE NAME</th>
<th style="background-color:lightgrey;" width="25%">START DATE</th>
<th style="background-color:lightgrey;" width="25%">END DATE</th>
</tr>';

if($get_po_approved_date<>'')
                                    {
                                    $v=date('d-m-Y',strtotime($get_po_approved_date));
                                   
                                    }else
                                    {
                                      $v='';

                                    }

                           if($get_supplier_stages != '') {
                            $i = 1;

                           
                            $last_common_stage=0;
                                foreach($get_supplier_stages as $row) { 

                                 $startdate='';
                                 $enddate='';
                                 $row123=$this->db->select('id,start_date,end_date')->from('supplier_gc')->where('order_won_id',$order_won_id)->where('part_id',$part_id)->where('supplier_stage_id',$row->id)->get();
                                 if($row123->num_rows()>0)
                                 {
                                    foreach($row123->result() as $row1231);
                                    $startdate=$row1231->start_date;
                                    $enddate=$row1231->end_date;

                                 }



$html.='<tr>
<td>'.$i.'</td>
<td>'.$row->stage_name_level.'</td>
<td>'.date('d-M-Y',strtotime($startdate)).'</td>
<td>'.date('d-M-Y',strtotime($enddate)).'</td>
</tr>';
$i++;
}
}

$html.='</table>';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/macroplanpdf/supplier/';
$fileNL = $filelocation . "gantt_chart_".$this->uri->segment(3)."_".$this->uri->segment(6).".pdf"; //Linux
$pdf->Output($fileNL, 'F');
$pdf->Output($fileNL, 'I');

//header('location:'.redirectpath.$this->uri->segment(3));



//============================================================+
// END OF FILE
//============================================================+
