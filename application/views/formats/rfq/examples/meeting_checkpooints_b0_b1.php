<?php
$partname='';
// $design_stages=array('Product Design','DFM','Mould Flow','Tool Design','Tool Manufacturing','Trial','Tool BuyOff');
$CI =& get_instance();
$EI =& get_instance();
$FI =& get_instance();
$GI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$EI->load->model('Master_model', 'master');
$EI->load->model('BOM_model', 'bom');
$FI->load->model('Sourcing_model', 'sourcing');
$GI->load->model('Master_model', 'master');
$GI->load->model('Open_model', 'open');
$order_won=$this->uri->segment(3);
$lead_id=$this->uri->segment(4);
$stage_id=$this->uri->segment(5);
$part_id=$this->uri->segment(6);
$supplier_flag=$this->uri->segment(7);
$lead_detail=$CI->salescrm->getLeadDetails($lead_id);
$lead_no=$GI->open->getLeadUniqueNo($lead_id);
if($lead_detail<>'')
{
    foreach($lead_detail as $ldetails);
    $company_name=$ldetails->company_name;
    $unique_no=$ldetails->unique_id;

}else
{
    echo "Invalid Access"; exit;

}

if($part_id==0)
{
$orien="Assembly Oriented";
$orien1="Assembly Wise Checkpoints";
}else
{
$orien="Part Oriented";
$orien1="Part Wise Checkpoints";
}


if($stage_id==24)
{
$met="Tooling";
}else
{
$met="Product Design";
}
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


$cp=array();
$hp=array();
$ret=$this->db->select('addedOn,client_participants,hjig_participants')->from('meeting_checkpoints')->where('order_won_id',$order_won)->where('lead_id',$lead_id)->where('stage_id',$stage_id)->where('stage_id',$stage_id)->where('part_id',$part_id)->get();
if($ret->num_rows()>0)
{
    foreach($ret->result() as $row);
    $addedDate=date('d M, Y',strtotime($row->addedOn));
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

$pdf->SetMargins(5, 10, 5, true);

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
<br/>';

if($supplier_flag==0 || $supplier_flag=='')
{
$html.='<table style="text-align:center;">
	<tr>
		<td class="cri">CRITICAL TO QUALITY DOCUMENT</td>
	</tr>';

if($stage_id==24)
{
	$html.='<tr>
		<td>Project Brain Stroming Meeting</td>
	</tr>';
	$html.='<tr>
		<td>Date | '.$addedDate.'</td>
	</tr>';
}

if($stage_id!=24)
{
	$html.='<tr>
		<td>Product Design Brain Storming Meeting</td>
	</tr>';

}


$html.='</table>
<br/>
<br/>
<table style="text-align:center;">
	<tr>
		<td>Project Name | '.$project_name.'</td>
	</tr>';

if($stage_id==24)
{
	if($supplier_flag<>1)
	{
	$html.='<tr>
		<td>Customer - '.$company_name.'</td>
	</tr>';
}

		$html.='<tr>
		<td></td>
	</tr>';

	$html.='<tr>
		<td>Number of Components - '.$getTotalComponentsNew.' | Number of Moulds - '.$getTotalMouldsNew.'</td>
	</tr>';
}

$html.='</table>';
}else
{

$html.='<table style="text-align:center;">
	<tr>
		<td class="cri">CRITICAL TO QUALITY DOCUMENT</td>
	</tr>';

if($stage_id==24)
{
	$html.='<tr>
		<td>Important issues to be taken care while Moulds Manufacturing</td>
	</tr>';
}

if($stage_id!=24)
{
	$html.='<tr>
		<td>Important issues to be taken care while Product Design</td>
	</tr>';
}


$html.='</table>
<br/>
<br/>
<table style="text-align:center;">
	<tr>
		<td>Project Name | '.$project_name.'</td>
	</tr>';

if($stage_id==24)
{
	$html.='<tr>
		<td>Number of Components - '.$getTotalComponentsNew.' | Number of Moulds - '.$getTotalMouldsNew.'</td>
	</tr>';
}

$html.='</table>';
}
$dr=array();
$dr[]=0;
    $ret=$this->db->select('stage')->from('meeting_checkpoints')->where('order_won_id',$order_won)->where('lead_id',$lead_id)->where('stage_id',$stage_id)->where('part_id',$part_id)->order_by('stage','ASC')->group_by('stage')->get();
if($ret->num_rows()>0)
{
    $k=0;
    foreach($ret->result() as $row)
    {
    	   $st=$EI->master->getCheckPointNameByID($row->stage);

    	   $ret=$this->db->select('*,description as writtenpoints')->from('meeting_checkpoints')->where('order_won_id',$order_won)->where('lead_id',$lead_id)->where('stage_id',$stage_id)->where('part_id',$part_id)->where('stage',$row->stage)->get();
if($ret->num_rows()>0)
{
    $h=1;
    foreach($ret->result() as $row)
    {
    	$desc='';
					if($row->checklist_id>0)
					{
					$sqll = $this->db->select('id, description')
					->from('fms_checklist_info')
					->where('id', $row->checklist_id)
					->get();
					if($sqll->num_rows()>0)
					{
					foreach($sqll->result() as $sqll1);
					$desc=$sqll1->description;
					}
					}

				$img=$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image;
				$ourpoint=$row->writtenpoints;


$html.='<br pagebreak="true">
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<table style="padding:50px 0px;">';

if($k%2<>0)
{ 
	$html.='<tr>
		<td width="70%" >
		<img src="'.$img.'" width="700px" style="border:1px solid #000;width:700px;height:500px"></td>
		<td width="30%">
			<p style="text-transform:uppercase; text-align:left;"><b>critical to quality</b> <br/>'.$CI->salescrm->TextFormatting($partname).'</p>';
				if($stage_id==24 && $part_id!=0)
			{
				if($k==0)
				{
				$html.='<table class="clr_ful" >
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
				</table>';
				} }
				$html.='<p></p> 
				<table>
					<tr>
						<td><b>'.strtoupper($st).'</b></td>
					</tr>
					<tr>
						<td style="font-size:16px;">'.$ourpoint.'</td> 
					</tr>
				</table>
		</td>
	</tr>';
	 }else{


$html.='<tr>
		
		<td width="30%">';

			if($part_id>0)
			{
			$html.='<p style="text-transform:uppercase; text-align:left;"><b>critical to quality</b><br/>'.$CI->salescrm->TextFormatting($partname).'</p><br/>';
			}else
			{
				$html.='<p style="text-transform:uppercase; text-align:left;"><b>critical to quality</b></p><br/>';
			}

			if($stage_id==24 && $part_id!=0)
			{
				if($k==0)
				{
				$html.='<table class="clr_ful" >
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
				</table>
				<br>
				<br>';
				}
			}

				$html.='<p></p> 
				<table>
					<tr>
						<td><b>'.strtoupper($st).'</b></td>
					</tr>
					<tr>
						<td style="font-size:16px;">'.$ourpoint.'</td> 
					</tr>
				</table>
		</td>
		<td width="2%"></td>
		<td width="68%">
		<img src="'.$img.'" width="700px" style="border:1px solid #000;width:700px;height:550px"></td>
	</tr>';
} 

$html.='</table>';
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