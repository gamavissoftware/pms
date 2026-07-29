<?php   
$design_stages=array('Product Design','DFM','Mould Flow','Tool Design','Tool Manufacturing','Trial','Tool BuyOff');
$CI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$order_won=$this->uri->segment(3);
$lead_id=$this->uri->segment(4);
$stage_id=0;
$part_id=0;

$lead_detail=$CI->salescrm->getLeadDetails($lead_id);
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

if($part_id==0)
{
$orien="ECN Checkpoints";
$orien1="ECN Checkpoints";
}else
{
$orien="Part Oriented";
$orien1="Part Wise Checkpoints";
}

$getTotalMouldsNew=$CI->salescrm->getTotalMouldsNew($lead_id);
$project_name=$CI->salescrm->getProjectName($lead_id);


$cp=array();
$hp=array();
$ret=$this->db->select('client_participants,hjig_participants,addedOn')->from('meeting_checkpoints_standalone')->where('order_won_id',$order_won)->where('lead_id',$lead_id)->where('stage_id',$stage_id)->where('stage_id',$stage_id)->where('part_id',$part_id)->get();
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

    require_once('tcpdf_include.php');

    // create new PDF document
    $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);


    // set document information
    $pdf->SetCreator(PDF_CREATOR);
 


     $pdf->SetPrintHeader(false); 
     $pdf->setPrintFooter(false);
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


    // set text shadow effect
    $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

    // Set some content to print

    /** quert paet **/

    $html='<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><p style="margin-top:70%;"></p><p style="font-size:24px;font-weight:bold;text-align:center;">'.$orien1.'</p><br pagebreak="true">';


    $html.= '
    <table style="text-align:center;" >
    <tr>
    <td><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px"><br><br>
    </td></tr></table>
    

    <table width="100%" style=" padding: 3px; text-align: center;font-size: 10px;">
   
    <tr><td style="font-size: 14px;"><strong>Project Name | '.$project_name.'</strong> </td></tr>
    </table>

    <div style="padding-top:20px;"></div>

    <table width="100%" style=" padding: 3px; font-size: 10px;" border="1">

    <tr>
        <th width="25%" style="text-align:center;"><strong>Company Name</strong></th>
        <th width="25%" style="text-align:center;"><strong>Order No.</strong></th>
        <th width="25%" style="text-align:center;"><strong>No. Of Mould</strong></th>
        <th width="25%" style="text-align:center;"><strong>Date</strong></th>
        </tr>
        <tr>
        <td width="25%" style="text-align:center;">'.$company_name.'</td>
        <td width="25%" style="text-align:center;">'.$unique_no.'</td>
        <td width="25%" style="text-align:center;">'.$getTotalMouldsNew.'</td>
        <td width="25%" style="text-align:center;">'.$date.'</td>
        </tr>
        </table>  
           ';

       
    
    $html.='</tr></table>
     <div style="padding-top:20px;"></div>';
 
$dr=array();
$dr[]=0;
//     $ret=$this->db->select('stage')->from('meeting_checkpoints_standalone')->where('ecn_id',$this->uri->segment(5))->order_by('stage','ASC')->group_by('stage')->get();
// if($ret->num_rows()>0)
// {
//     foreach($ret->result() as $row)
//     {
        // $design_stage=$row->stage-1;

        // $st=$design_stages[$design_stage];


    $html.='<span style="padding-top:20px;"></span>
    <hr nobr="true">

    <p style="text-align:center;font-size:15px;padding:0px;margin:0px;"><strong>'.strtoupper($st).'</strong></p>';

 $ret=$this->db->select('*')->from('meeting_checkpoints_standalone')->where('ecn_id',$this->uri->segment(5))->get();
if($ret->num_rows()>0)
{
    $h=1;
    foreach($ret->result() as $row)
    {
        $checklist_data='';
        // if($row->checklist_id>0)
        // {
        //     $sqll = $this->db->select('id, description')
        //                  ->from('fms_checklist_info')
        //                  ->where('id', $row->checklist_id)
        //                  ->get();
        //                  if($sqll->num_rows()>0)
        //                  {
        //                     foreach($sqll->result() as $sqll1);
        //                     $checklist_data="<br/><br/><strong>".$sqll1->description."</strong>";

        //                  }
        // }
        if(count($dr)%2<>0)
        {
            $img='<img src="'.$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image.'" width="320px">';
            $data='<span style="padding-left:20px;">'.trim($row->description).'</span>';
            $context = $row->context;
        }else
        {
            $data='<span style="padding-left:20px;">'.trim($row->description).'</span>';
            $img='<img src="'.$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image.'" width="320px">';
            $context = $row->context;

        }

$html.='<table width="100%">
        <tr>
            <td width="10%"></td>
            <td width="80%" style="text-align:center;">'.$img.'</td>
            <td width="10%"></td>
            
        </tr>
        <tr><td width="100%"><strong>'.$h.') '.$context.'</strong></td></tr><br/>
         <tr><td width="100%">'.$data.'</td></tr>
       
    </table>
    
     <p></p><p></p><p></p>';
$h++; 
$dr[]=0;
}
}
// } 
// }


   
    //echo $footer_logo_html; exit;
    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.



    $filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/ecnpdf';
    $fileNL = $filelocation . "/ECN_Detailed_Presentation".$this->uri->segment(5).".pdf"; //Linux
    $filename = "/ECN_Detailed_Presentation".$this->uri->segment(5).".pdf"; //Linux
    ob_clean();
    $pdf->Output($fileNL, 'F');

$d1=base64_encode(imagespath.'ecnpdf/'.$filename);
$e1=base64_encode('ECN Detailed Presentation');
$f1=0;
header("location:https://hongyijig.in/index.php/FMS1/common_preview22/".$d1."/".$e1."/".$f1);

    //============================================================+
    // END OF FILE
    //============================================================+
