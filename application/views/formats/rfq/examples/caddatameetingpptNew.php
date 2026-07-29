<?php
$CI =& get_instance();
$EI =& get_instance();
$FI =& get_instance();
$GI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$EI->load->model('BOM_model', 'bom');
$FI->load->model('Sourcing_model', 'sourcing');
$GI->load->model('Master_model', 'master');
$CI->load->model('Open_model', 'open');
$lead_id=$this->uri->segment(3);
$stage_id=0;
$part_id=$this->uri->segment(5);
$mould_id = $this->uri->segment(4);
$recordid = $this->uri->segment(6);
$partDetails=$CI->salescrm->getPartDetailsByPartID($part_id);
$partAddDetails=$CI->sourcing->getPartAddDetails($part_id);
// echo "<pre>";print_r($partAddDetails); exit;
$mould_details=$EI->bom->getmouldsdetailsBYID($mould_id);
if($partDetails=='')
{
    echo "Invalid Link"; exit;
}
$lead_detail=$CI->salescrm->getLeadDetails($lead_id);
$lead_no=$CI->open->getLeadUniqueNo($lead_id);
$getmoulddetails=$CI->sourcing->getmouldbasedetails($lead_id,$mould_id);
$getmouldcoredetails=$CI->sourcing->getmouldcoredetails($lead_id,$mould_id);
//echo "<pre>"; print_r($lead_detail); exit;
if($lead_detail<>'')
{
    foreach($lead_detail as $ldetails);
    $company_name=$ldetails->company_name;
    $unique_no=$ldetails->unique_id;

}else
{
    echo "Invalid Access"; exit;

}
$getTotalMouldsNew=$CI->salescrm->getTotalMouldsNew($lead_id);
$project_name=$CI->salescrm->getProjectName($lead_id);


$getTotalMouldsNew=$CI->salescrm->getTotalMouldsNew($lead_id);
$getTotalComponentsNew=$CI->salescrm->getTotalComponents($lead_id);
$project_name=$CI->salescrm->getProjectName($lead_id);
if($part_id>0)
{
$partDetails=$CI->salescrm->getPartDetailsByPartID($part_id);
$partAddDetails=$CI->sourcing->getPartAddDetails($part_id);
$partweight=$EI->bom->getcadweight($part_id);
$mould_id=$CI->salescrm->getMould_id_by_Part($part_id);
$mould_no=$EI->bom->getMouldNo($lead_id,$mould_id);
$mould_details=$EI->bom->getmouldsdetailsBYID($mould_id);
if($partDetails=='')
{
    echo "Invalid Link"; exit;
}

$getPartDetails = $CI->sourcing->getPartDetails($lead_id, $mould_id);
if($getPartDetails != '') {
$i = 1;

$partscount= (array) $getPartDetails;
$partscount=count($partscount);
foreach($getPartDetails as $row1) {

	if($row1->colortype == 1) {
				          $color = $row1->colourname;
				      } else {
				          $color = "Special Color-".$row1->pantonecode;
				      }

				       if($row1->partfinish == 1) {
				        $fin = "High Gloss Mirror";
				        } else if($row1->partfinish == 2) {
				        $fin = "High Gloss";
				        } else if($row1->partfinish == 3) {
				        $fin = "Normal Polish";
				        } else if($row1->partfinish == 4) {
				        $fin = "Texture Matt Finish (".$row1->matcode.")";
				        }else if($row1->partfinish == 5) {
				        $fin = "High Gloss+Texture";
				        }else if($row1->partfinish == 7) {
				        $fin = "Grain Finish";
				        }else
				        {
				            $fin="";
				        }


				       
}
}
 if($partDetails!='')
{

    foreach($partDetails as $partdata);
    foreach($partAddDetails as $partadddata);
    if($partadddata->runner_type==1)
    {
        $runner="Hot Runner";
    }else{
        $runner="Cold Runner";
    }
    $benchmark=$partadddata->benchmarkweight;
   

    $material_name=$EI->bom->getPartMaterialBYID($partdata->part_material_id);
        if($mould_details['hardening']==1)
        {
            $harden="Yes";
            $treatment=$CI->salescrm->get_heat_treatment($mould_details['cavity_hardening']);
        }else
        {
            $harden="NA";
            $treatment='';
        }

        $partname=$partdata->name;
        $cavity=$partdata->cavity;
        $gate=$partadddata->gatetype;

		$ttype=$mould_details['type'];
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


 }
}



$Restey=$this->db->select('id,name,code')->from('bom_part_details')->where('id',$part_id)->get();
foreach($Restey->result() as $ros);

$orien="Part Oriented";
$orien1="PPT Presentation";






$cp=array();
$hp=array();
$ret=$this->db->select('*')->from('meeting_checkpoints_standalone_for_PPT')->where('lead_id',$lead_id)->where('mould_id',$mould_id)->where('part_id',$part_id)->get();
if($ret->num_rows()>0)
{
    foreach($ret->result() as $row);
    $date=date('d-M-Y',strtotime($row->addedOn));
    $ret=explode(',',$row->client_participants);
    if(count($ret)>0)
    {
    foreach($ret as $participant)
    {
    $cp[]=trim($participant);
    }
    }

    $rete=explode(',',$row->hjig_participants);
    if(count($rete)>0)
    {
    foreach($rete as $participant1)
    {

    $hjig_users=$CI->salescrm->getusername($participant1);
    $hp[]=trim($hjig_users);
    }
    }
}

if(count($hp)>=count($cp))
{
    $greter=count($hp);
}

if(count($hp)<=count($cp))
{
    $greter=count($cp);
}


$ret=$this->db->select('top,bottom,lhs,rhs,front,back')->from('part_data_images')->where('ppt_id',$recordid)->get();
if($ret->num_rows()>0)
{
	foreach($ret->result() as $view_images);
	$front=$_SERVER['DOCUMENT_ROOT'].'/image_bank/checkpoint_image/'.$view_images->front;
	$top=$_SERVER['DOCUMENT_ROOT'].'/image_bank/checkpoint_image/'.$view_images->top;
	$bottom=$_SERVER['DOCUMENT_ROOT'].'/image_bank/checkpoint_image/'.$view_images->bottom;
	$lhs=$_SERVER['DOCUMENT_ROOT'].'/image_bank/checkpoint_image/'.$view_images->lhs;
	$rhs=$_SERVER['DOCUMENT_ROOT'].'/image_bank/checkpoint_image/'.$view_images->rhs;
	$back=$_SERVER['DOCUMENT_ROOT'].'/image_bank/checkpoint_image/'.$view_images->back;
}else
{
	$front="";
	$top="";
	$bottom="";
	$lhs="";
	$rhs="";
	$back="";
}

//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);





// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->SetMargins(10, 10, 10, true);

// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
	require_once(dirname(__FILE__) . '/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 16, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage('L');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set some content to print
$html = '

<style>

.cri{
	text-align:center;
	text-transform:uppercase;
	font-size:30px;
}

table{
	width:100%;
}

.clr_ful{
	padding:5px;
}

.clr_ful th{
border:1px solid white;
background-color:#4573c0;
padding:5px;
}

.clr_ful td{
	border:1px solid white;
	padding:5px;
	font-size:18px;
	background-color:#d0d5eb;
}

.clr_ful td:nth-child(even) {
  background-color: #dddddd;
}

</style>

<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<table style="text-align:center;">
	<tr>
		<td class="cri">REFERENCE FOR QUOTE</td>
	</tr>
	<tr>
		<td class="cri">FOR INJECTION MOULD MANUFACTURING</td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>Project Name | '.ucwords(strtolower($project_name)).'</td>
	</tr>
	<tr>
		<td>Mould Type | '.$mould_type.'</td>
	</tr>
</table>
<br pagebreak="true">';

$html.='<table style="padding:50px 0px;">
	<tr>

	<td width="33%">
			<p style="text-transform:uppercase; text-align:center;"><b>Reference for Quote</b> <br/>'.$CI->salescrm->TextFormatting($partname).'</p>
				<table class="clr_ful" >
					<tr>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<td>Part Name</td>
						<td>'.$CI->salescrm->TextFormatting($partname).'</td>
					</tr>
					<tr>
						<td>Part Material</td>
						<td>'.$CI->salescrm->TextFormatting($material_name).'</td>
					</tr>
					<tr>
						<td>Mould No.</td>
						<td>'.$lead_no.' | '.$mould_no.$mt.'</td>
					</tr>
					<tr>
						<td>Cavitation</td>
						<td>'.$CI->salescrm->TextFormatting($cavity).'</td>
					</tr>
					<tr>
						<td>Runner Type</td>
						<td>'.$CI->salescrm->TextFormatting($runner).'</td>
					</tr>
					<tr>
						<td>Gate Type</td>
						<td>'.$CI->salescrm->TextFormatting($gate).'</td>
					</tr>
					<tr>
						<td>Surface Finish</td>
						<td>'.$fin.'</td>
					</tr>
					<tr>
						<td>Part Weight</td>
						<td>'.$benchmark.' gm</td>
					</tr>
					<tr>
						<td>Mould Base</td>
						<td>'.$getmoulddetails.'</td>
					</tr>
					<tr>
						<td>Tool Steel</td>
						<td>'.$getmouldcoredetails.'</td>
					</tr>

					<tr>
						<td>Color</td>
						<td>'.$color.'</td>
					</tr>
				</table>
				<br>
				<br>
				<p></p> 
				
		</td>

		<td width="2%"></td>
		<td width="65%" >
		<img src="'.$front.'" width="550px"></td>
		
	</tr>
</table><br pagebreak="true">';

$html.='<table>
	<tr>
	<td width="32%">
	<img src="'.$top.'" width="275px" style="width:260px;height:260px;"><span style="text-align:center;padding:0px;margin:0px;"><b>Top View</b></span>
	</td>
	
	<td width="2%"></td>
	<td width="32%">
	<img src="'.$bottom.'"  width="275px" style="width:260px;height:260px;"><span style="text-align:center;padding:0px;margin:0px;"><b>Bottom View</b></span>
	</td>
	<td width="2%"></td>
	<td width="32%">
	<img src="'.$lhs.'"  width="275px" style="width:260px;height:260px;"><span style="text-align:center;padding:0px;margin:0px;"><b>Left Side View</b></span>
	</td>

	</tr>
	<tr>
	<td></td>
	<td></td>
	<td></td>
	</tr>	
	<tr>
	<td width="32%">
	<img src="'.$rhs.'" width="275px" style="width:260px;height:260px;"><span style="text-align:center;padding:0px;margin:0px;"><b>Right Side View</b></span>
	</td>
	
	<td width="2%"></td>
	<td width="32%">
	<img src="'.$front.'"  width="275px" style="width:260px;height:260px;"><span style="text-align:center;padding:0px;margin:0px;"><b>Front View</b></span>
	</td>
	<td width="2%"></td>
	<td width="32%">
	<img src="'.$back.'"  width="275px" style="width:260px;height:260px;"><span style="text-align:center;padding:0px;margin:0px;"><b>Back Side View</b></span>
	</td>		
	</tr>
</table><br pagebreak="true">';


 $k=1;
 $ret=$this->db->select('stage')->from('part_data_ppt_checkpoints')->where('ppt_id',$recordid)->group_by('stage')->get();
if($ret->num_rows()>0)
{
   
    foreach($ret->result() as $row)
    {
         $st=$EI->master->getCheckPointNameByID($row->stage);

         $ret=$this->db->select('*,description as writtenpoints')->from('part_data_ppt_checkpoints')->where('lead_id',$lead_id)->where('part_id',$part_id)->where('stage',$row->stage)->where('ppt_id',$recordid)->get();
if($ret->num_rows()>0)
{
    $h=1;
    foreach($ret->result() as $row)
    {
	
	if($k<>1)
	{
		$html.='<br pagebreak="true">';
	}
	$html.='<table style="padding:50px 0px;">';

	if($k%2<>0)
	{
		$img=$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image;

	$html.='<tr>
		<td width="65%" >
		<img src="'.$img.'" width="600px"></td>
		<td width="2%"></td>
		<td width="33%">';
			

				$html.='<table>
					<tr>
						<td><b>'.ucwords(strtolower($st)).'</b><hr></td>
					</tr>
					<tr>
						<td style="font-size:16px;">'.trim(ucwords(strtolower($row->writtenpoints))).'</td> 
					</tr>
				</table>';
			

		$html.='</td>
	</tr>';
}else
{

	$html.='<tr>

		<td width="33%">';

		
				$html.='<table>
					<tr>
						<td><b>'.ucwords(strtolower($st)).'</b><hr></td>
					</tr>
					<tr>
						<td style="font-size:16px;">'.trim(ucwords(strtolower($row->writtenpoints))).'</td> 
					</tr>
				</table>';
				
				$img=$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image;

		$html.='</td><td width="2%"></td>
		<td width="65%" >
		<img src="'.$img.'" width="600px"></td>
	
	</tr>';

}

$html.='</table>';

$h++;
$k++;
}
}

}
}


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
