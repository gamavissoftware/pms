<?php   
$CI =& get_instance();
$EI =& get_instance();
$FI =& get_instance();
$GI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$EI->load->model('BOM_model', 'bom');
$FI->load->model('Sourcing_model', 'sourcing');
$GI->load->model('Master_model', 'master');
$lead_id=$this->uri->segment(3);
$stage_id=0;
$part_id=$this->uri->segment(5);
$mould_id = $this->uri->segment(4);
$recordid = $this->uri->segment(6);
$partDetails=$CI->salescrm->getPartDetailsByPartID($part_id);
$partAddDetails=$CI->sourcing->getPartAddDetails($part_id);
$partweight=$EI->bom->getcadweight($part_id);
$mould_details=$EI->bom->getmouldsdetailsBYID($mould_id);
if($partDetails=='')
{
    echo "Invalid Link"; exit;
}
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
$getTotalMouldsNew=$CI->salescrm->getTotalMouldsNew($lead_id);
$project_name=$CI->salescrm->getProjectName($lead_id);
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

    require_once('tcpdf_include.php');

    // create new PDF document
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


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
    $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
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


    // set text shadow effect
    $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

    // Set some content to print

    /** quert paet **/

    $html='<table style="text-align:center;" >
    <tr>
    <td><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px">
    </td></tr></table><br/><br/><br/><br/><br/><br/><br/><p style="font-size:24px;font-weight:bold;text-align:center;">'.$orien1.' For Project '.ucwords(strtolower($project_name)).'</p>';


       if($partDetails!='')
{
    //echo "<pre>"; print_r($mould_details); exit;
    foreach($partDetails as $partdata);
    foreach($partAddDetails as $partadddata);
    //echo "<pre>"; print_r($mould_details); exit;
    if($partadddata->runner_type==1)
    {
        $runner="Hot Runner";
    }else{
        $runner="Cold Runner";
    }
        $html.='<table width="100%" style=" padding: 3px; font-size: 10px;" border="1">

        <tr>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Part Name</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Part Weight</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Part Material</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Cavities</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Runner Type</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Gating Type</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Number Gates (Tips) </strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Core Material</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Cavity Material </strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Heat Treatment</strong></th>
        <th width="9%" style="text-align:center;background-color:lightgrey;"><strong>Mould base Material </strong></th>
        </tr>';

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


        $html.='<tr>
        <td width="9%" style="text-align:center;">'.$partdata->name.'</td>
        <td width="9%" style="text-align:center;">'.$partweight.' gm</td>
        <td width="9%" style="text-align:center;">'.$material_name.'</td>
        <td width="9%" style="text-align:center;">'.$partdata->cavity.'</td>
        <td width="9%" style="text-align:center;">'.$partadddata->runner_name.'</td>
        <td width="9%" style="text-align:center;">'.$partadddata->runner_brand."<br/>".$partadddata->gatetype.'</td>
        <td width="9%" style="text-align:center;">'.$partadddata->tips.'</td>
        <td width="9%" style="text-align:center;">'.$mould_details['mouldcoresteename'].'</td>
        <td width="9%" style="text-align:center;">'.$mould_details['mouldcavitysteename'].'</td>
        <td width="9%" style="text-align:center;">'.$harden.'<br/>'.$treatment.'</td>
        <td width="9%" style="text-align:center;">'.$mould_details['mouldbasesteelname'].'</td>
        </tr>
        </table>';
    }  
        


 
$dr=array();
$dr[]=0;
//     $ret=$this->db->select('stage')->from('meeting_checkpoints_standalone')->where('ecn_id',$this->uri->segment(5))->order_by('stage','ASC')->group_by('stage')->get();
// if($ret->num_rows()>0)
// {
//     foreach($ret->result() as $row)
//     {
        // $design_stage=$row->stage-1;

        // $st=$design_stages[$design_stage];


    // $html.='<span style="padding-top:20px;"></span>
    // ';

 $ret=$this->db->select('stage')->from('part_data_ppt_checkpoints')->where('ppt_id',$recordid)->group_by('stage')->get();
if($ret->num_rows()>0)
{
    $k=1;
    foreach($ret->result() as $row)
    {
         $st=$EI->master->getCheckPointNameByID($row->stage);

        $checklist_data='';
       

         


          
    $img='';
       
        
 $ret=$this->db->select('*,description as writtenpoints')->from('part_data_ppt_checkpoints')->where('lead_id',$lead_id)->where('part_id',$part_id)->where('stage',$row->stage)->where('ppt_id',$recordid)->get();
if($ret->num_rows()>0)
{
    $h=1;
    foreach($ret->result() as $row)
    {
        $checklist_data='';

         if($row->checklist_id>0)
         {
             $sqll = $this->db->select('id, description')
                          ->from('fms_checklist_info')
                          ->where('id', $row->checklist_id)
                          ->get();
                          if($sqll->num_rows()>0)
                          {
                             foreach($sqll->result() as $sqll1);
                             $checklist_data="<strong>".$sqll1->description."</strong>";

                          }
         }
       
        if(count($dr)%2<>0)
        {
            $img='<img src="'.$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image.'" width="450px">';
            $data='<span style="font-weight:bold;text-align:right;">'.$h.') '.trim($row->writtenpoints).'</span>';
            $css=' <tr>
            <td width="65%">'.$img.'</td>
            <td width="2%"></td>
            <td width="33%">'.$data.'</td>
            
        </tr>';
        }else
        {

            $data='<img src="'.$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image.'" width="450px">';
            $img='<span style="font-weight:bold;text-align:left;">'.$h.') '.trim($row->writtenpoints).'</span>';
              $css=' <tr>
              <td width="33%">'.$img.'</td>
              <td width="2%"></td>
              <td width="65%">'.$data.'</td>
            
            
        </tr>';
        }
if($h!=1)
{
$hw='<hr>';
}else
{
    $hw='';
}
 $html.='<br pagebreak="true"/>';


          $html.='<table style="text-align:center;" >
    <tr>
    <td>
    </td></tr></table><br><p style="text-align:center;font-size:28px;padding:0px;margin:auto;"><strong>'.strtoupper($st).'</strong></p>';

$html.='<table style="text-align:center;" >
    <tr>
    <td>
    </td></tr></table><br><br><br><br>
    <br><br><br><br><table width="100%">
<tr>
            <td width="10%"></td>
            <td width="80%"></td>
            <td width="10%"></td>
            
        </tr>';

        $html.=$css;
       

       
    $html.='</table>';

$h++; 
$dr[]=0;
}
}
 $k++;
} 
 }


   //echo $html; exit;
    //echo $footer_logo_html; exit;
    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.



    $filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/ecnpdf';
    $fileNL = $filelocation . "/checkpointsPPT".$this->uri->segment(5).".pdf"; //Linux
    $filename = "/checkpointsPPT".$this->uri->segment(5).".pdf"; //Linux
    ob_clean();
    $pdf->Output($fileNL, 'I');

// $d1=base64_encode(imagespath.'ecnpdf/'.$filename);
// $e1=base64_encode($orien1);
// $f1=base64_encode(0);
// header("location:https://hongyijig.in/index.php/FMS1/common_preview22/".$d1."/".$e1."/".$f1);

    //============================================================+
    // END OF FILE
    //============================================================+
