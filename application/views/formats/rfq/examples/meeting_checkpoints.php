<?php   
// $design_stages=array('Product Design','DFM','Mould Flow','Tool Design','Tool Manufacturing','Trial','Tool BuyOff');
$CI =& get_instance();
$EI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$EI->load->model('Master_model', 'master');
$order_won=$this->uri->segment(3);
$lead_id=$this->uri->segment(4);
$stage_id=$this->uri->segment(5);
$part_id=$this->uri->segment(6);
$supplier_flag=$this->uri->segment(7);
$lead_detail=$CI->salescrm->getLeadDetails($lead_id);
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
$project_name=$CI->salescrm->getProjectName($lead_id);


$cp=array();
$hp=array();
$ret=$this->db->select('addedOn,client_participants,hjig_participants')->from('meeting_checkpoints')->where('order_won_id',$order_won)->where('lead_id',$lead_id)->where('stage_id',$stage_id)->where('stage_id',$stage_id)->where('part_id',$part_id)->get();
if($ret->num_rows()>0)
{
    foreach($ret->result() as $row);
    $addedDate=date('d-M-Y',strtotime($row->addedOn));
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


    // set text shadow effect
    $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

    // Set some content to print

    /** quert paet **/

$html='';
    $html.='<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><p style="margin-top:70%;"></p><p style="font-size:24px;font-weight:bold;text-align:center;">'.$orien1.'</p><br pagebreak="true">';

    $html.= '
    <table style="text-align:center;" >
    <tr>
    <td><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px"><br><br>
    </td></tr></table>
    

    <table width="100%" style=" padding: 3px; text-align: center;font-size: 10px;">
    <tr><td style="font-size: 14px;"><strong>Critical To Quality Document</strong></td></tr>
    <tr><td style="font-size: 12px;">((Initial Client Meeting for  '.$met.' before Project Kick Off))</td></tr>
    <tr><td style="font-size: 14px;"><strong>Project | '.$project_name.' | '.$orien.'</strong> </td></tr>
    </table>

    <div style="padding-top:20px;"></div>

     <table width="100%" style=" padding: 3px; font-size: 10px;">
        <tr>
        <td width="10%"></td>';

        if($supplier_flag!=1)
        {
        $html.='<td width="80%">
            <table width="100%" border="1" style=" padding: 3px; text-align: center;;font-size: 10px;">
        <tr>
            <td>Customer/Project Information</td>
        </tr>
        <tr>
        <td width="25%" style="background-color:lightgrey;">Company Name </td>
        <td width="25%" style="background-color:lightgrey;">Order Number</td>
        <td width="25%" style="background-color:lightgrey;">Number of Moulds</td>
        <td width="25%" style="background-color:lightgrey;">Date</td>
        </tr>';

      
        $html.='<tr nobr="true">
        <td width="25%"><strong>'.$company_name.'</strong></td>
        <td width="25%"><strong>'.$unique_no.'</strong></td>
        <td width="25%"><strong>'.$getTotalMouldsNew.'</strong></td>
        <td width="25%"><strong>'.$addedDate.'</strong></td>
        </tr>';
    
    


    $html.=' </table>
           </td>';
       }else
       {
        $html.='<td></td>';
       }

        $html.='</tr>
      
        
    </table><br/><br/><br/><br/><br/>


    <table width="100%" style=" padding: 3px; font-size: 10px;">
        <tr>
        <td width="10%"></td>
           <td width="80%">
            <table width="100%" border="1" style=" padding: 3px; text-align: center;;font-size: 10px;">
        <tr>
            <td>List of Participants</td>
        </tr>
        <tr>
        <td width="50%" style="background-color:lightgrey;">Client Company Name</td>
        <td width="50%" style="background-color:lightgrey;">Hongyi Jig</td>
        </tr>';

        for($i=0;$i<$greter;$i++)
        {
            if(isset($cp[$i])){
                $d=$cp[$i];
            }else
            {
                $d='';
            }

            if(isset($hp[$i])){
                $e=$hp[$i];
            }else
            {
                $e='';
            }
        $html.='<tr nobr="true">
        <td width="50%" nobr="true">'.$d.'</td>
        <td width="50%" nobr="true">'.$e.'</td>
        </tr>';
        }
    


    $html.=' </table>
           </td>

        </tr>
      
        
    </table>


   
     <div style="padding-top:20px;"></div>';


 
$dr=array();
$dr[]=0;
    $ret=$this->db->select('stage')->from('meeting_checkpoints')->where('order_won_id',$order_won)->where('lead_id',$lead_id)->where('stage_id',$stage_id)->where('part_id',$part_id)->order_by('stage','ASC')->group_by('stage')->get();
if($ret->num_rows()>0)
{
    $k=0;
    foreach($ret->result() as $row)
    {
        //$design_stage=$row->stage-1;

        $st=$EI->master->getCheckPointNameByID($row->stage);


   // $html.='<br pagebreak="true"><span style="padding-top:20px;"></span>';
    if($k<>0)
    {
    //$html.='<hr nobr="true">';
    }
    // $html.='<p style="text-align:center;font-size:15px;padding:0px;margin:0px;"><strong>'.strtoupper($st).'</strong></p>
    // <p style="text-align:center;padding:0px;margin:0px;">'.$orien.'</p><p></p>';

 $ret=$this->db->select('*,description as writtenpoints')->from('meeting_checkpoints')->where('order_won_id',$order_won)->where('lead_id',$lead_id)->where('stage_id',$stage_id)->where('part_id',$part_id)->where('stage',$row->stage)->get();
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
            $data='<span style="font-weight:bold;text-align:left;">'.$h.') '.trim($row->writtenpoints).'</span>';
            $css=' <tr>
            <td width="65%">'.$img.'</td>
            <td width="2%"></td>
            <td width="33%">'.$data.'</td>
            
        </tr>';


        //     $img='<img src="'.$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image.'" width="350px">';
        //     $data='<span style="padding-left:20px;text-align:justify;font-weight:bold;">'.$h.') '.trim($row->writtenpoints).'</span>';
        //     $css=' <tr>
        //     <td width="30%">'.$img.'</td>
        //     <td width="5%"></td>
        //     <td width="65%">'.$data.'</td>
            
        // </tr>';
        }else
        {

        //     $data='<img src="'.$_SERVER['DOCUMENT_ROOT'].'image_bank/checkpoint_image/'.$row->image.'" width="350px">';
        //     $img='<span style="padding-left:20px;text-align:justify;font-weight:bold;">'.$h.') '.trim($row->writtenpoints).'</span>';
        //       $css=' <tr>
        //       <td width="65%">'.$img.'</td>
            
        //     <td width="5%"></td>
        //     <td width="30%">'.$data.'</td>
            
            
        // </tr>';

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
    </td></tr></table><br><br><br><br><br><br><br><br>
    <br><br><br><br><table width="100%">';

        $html.=$css;
       

       
    $html.='</table>';

$h++; 
$dr[]=0;
}
}
$k++;
} }




 // echo $html; exit;

    //echo $footer_logo_html; exit;
    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.



    $filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';
    $fileNL = $filelocation . "/akash.pdf"; //Linux
    ob_clean();
    $pdf->Output($fileNL, 'I');



    //============================================================+
    // END OF FILE
    //============================================================+
