<?php
$order_won_id = $this->uri->segment(3);
$lead_id = $this->uri->segment(5);
$CI = &get_instance();
$CI->load->model('TCPDF_model');
$getMouldPartNames = $CI->TCPDF_model->getMouldPartNames($lead_id);
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
// set text shadow effect
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));
// Set some content to print
/** quert paet **/
$html = '
<table width="100%" style="padding:5px;">
  <tr>
    <td style="text-align:center;" ><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" style="width:150px;"></td>
  </tr>
</table>
<table width="100%" style="padding:3px; font-size:10px; text-align:center;" border="1" ruled="all">
  <tr>
    <td rowspan="2" style="vertical-align:middle; background-color:lightgrey;">Method</td>
    <td style="background-color:lightgrey;">CMM</td>
    <td style="background-color:lightgrey;">Digital Callper</td>
    <td style="background-color:lightgrey;">Veriner Callper</td>
    <td style="background-color:lightgrey;">Micrometer</td>
    <td style="background-color:lightgrey;">Block Gage</td>
    <td style="background-color:lightgrey;">Pin Gage</td>
    <td style="background-color:lightgrey;">Radius Guage</td>
    <td style="background-color:lightgrey;">Projector</td>
    <td style="background-color:lightgrey;">Height Guage</td>
    <td style="background-color:lightgrey;">Eye View</td>
    <td style="background-color:lightgrey;">Depth Gage</td>
    <td style="background-color:lightgrey;">Dial Indicator</td>
  </tr>
  <tr>
    <td>CMM</td>
    <td>DC</td>
    <td>VC</td>
    <td>M</td>
    <td>BG</td>
    <td>PG</td>
    <td>RG</td>
    <td>PJ</td>
    <td>HG</td>
    <td>V</td>
    <td>DG</td>
    <td>DI</td>
  </tr>
</table>
<br><br>';
$str = '';
if($getMouldPartNames != ''){
      foreach($getMouldPartNames as $partname){

        $get_trial_parts = $CI->TCPDF_model->get_trial_parts_by_id($partname->id,$order_won_id);


$str .='<table width="100%" >
  <tr>
    <td width="50%" style="padding-left:0px !important;">
      <table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Order Number</td>
          <td>'.$get_trial_parts->order_number.'</td>
        </tr>
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Mould Number</td>
          <td>'.$get_trial_parts->mould_number.'</td>
        </tr>
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Part Number/Name</td>
          <td>'.$get_trial_parts->part_name.' </td>
        </tr>
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Total Number of Dims</td>
          <td>'.$get_trial_parts->dims.' </td>
        </tr>
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Total Number of Cavity</td>
          <td>'.$get_trial_parts->cavity.'  </td>
        </tr>
      </table>
    </td>
    <td width="50%">
      <table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Design Part Weight</td>
          <td>'.$get_trial_parts->design_weight.'</td>
        </tr>
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Actual Part Weight</td>
          <td>'.$get_trial_parts->actual_weight.'</td>
        </tr>
        <tr>
          <td style="text-align:right; background-color:lightgrey;">Part Material  </td>
          <td>'.$get_trial_parts->part_material.' </td>
        </tr>
        <tr>
          <td style="text-align:right; background-color:lightgrey;">TBalloon 2D Drawing Link </td>
          <td>'.$get_trial_parts->balloon.'   </td>
        </tr>
      </table>
    </td>
  </tr>
</table>';
$col = $get_trial_parts->cavity*4 + 11;
$sql = $this->db->select('id, name')
                               ->from('bom_part_details')
                               ->where('id', $partname->id)
                               ->get();

                        if ($sql->num_rows() > 0) {
                          $res = $sql->row();
                        }

$str .= '<table width="100%" style="padding:3px; font-size:10px; text-align:center;" border="1" ruled="all">
  <tr>
    <th colspan="'.$col.'" style="background-color:lightgrey;">'.$res->name.'</th>
  </tr>
  <tr>
    <th rowspan="2" style="background-color:lightgrey;">Dim No.</th>
    <th rowspan="2" style="background-color:lightgrey;">Drg. Dim.</th>
    <th colspan="4" style="background-color:lightgrey;"></th>
    <th colspan="2" style="background-color:lightgrey;">Part Observation</th>
    <th rowspan="2" style="background-color:lightgrey;">TOOL</th>
    <th colspan="'.$get_trial_parts->cavity.'" style="background-color:lightgrey;">Trial Samples Dimensions -T-0 (By Supplier)</th>
    <th colspan="'.$get_trial_parts->cavity.'" style="background-color:lightgrey;">Trial Samples Dimensions -T-0 (By Hongyijig)</th>
    <th colspan="'.$get_trial_parts->cavity.'" style="background-color:lightgrey;">Final Samples Dimensions -T-1 (By Supplier)</th>
    <th colspan="'.$get_trial_parts->cavity.'" style="background-color:lightgrey;">Final Samples Dimensions -T-1 (By HJIG)</th>
    <th rowspan="2" style="background-color:lightgrey;">JUDGEMENT</th>
    <th rowspan="2" style="background-color:lightgrey;">Inspection<br>Process</th>
  </tr>
  <tr >
    <th style="background-color:lightgrey;" >Tol (+)</th>
    <th style="background-color:lightgrey;" >Tol (-)</th>
    <th style="background-color:lightgrey;" >Max</th>
    <th style="background-color:lightgrey;" >Min</th>
    <th style="background-color:lightgrey;" >MAX</th>
    <th style="background-color:lightgrey;" >MIN</th>';
     for($i=1; $i<=$get_trial_parts->cavity; $i++){
           $str.= '<th style="background-color:lightgrey;" >CAV '.$i.'</th>';
        }
     for($i=1; $i<=$get_trial_parts->cavity; $i++){
           $str.= '<th style="background-color:lightgrey;" >CAV '.$i.'</th>';
        }
     for($i=1; $i<=$get_trial_parts->cavity; $i++){
           $str.= '<th style="background-color:lightgrey;" >CAV '.$i.'</th>';
        }
     for($i=1; $i<=$get_trial_parts->cavity; $i++){
           $str.= '<th style="background-color:lightgrey;" >CAV '.$i.'</th>';
        }
    $str.= '  </tr>';

        $query = $this->db->select("*")
                    ->from("trial_details a")
                    ->where('a.trial_id', $get_trial_parts->id)
                    ->where('a.order_won_id',$order_won_id)
                    ->where('a.part_id',$partname->id)
                    ->get();

                    // echo $this->db->last_query();
            if($query ->num_rows() > 0){            
            $i =1;
            foreach($query->result() as $row){
                  $str .= '
                        <tr>
                          <td>'.$i.'</td>
                          <td>'.$row->drg_dim.'</td>
                          <td>'.$row->dim_tolp.'</td>
                          <td>'.$row->dim_tolm.'</td>
                          <td>'.$row->dim_max.'</td>
                          <td>'.$row->dim_min.'</td>
                          <td>'.$row->part_max.'</td>
                          <td>'.$row->part_min.'</td>
                          <td>'.$row->tool.'</td>';

 
                    $rest=$this->db->select('id')->from('trial_cavity')->where('trial_detail_id',$row->id)->where('trial_id',$get_trial_parts->id)->get();
                    if($rest->num_rows()>0){
                      foreach($rest->result() as $rttt)
                      {
                      $cav=0;
                      $rest=$this->db->select('cav')->from('trial_cavity')->where('id',$rttt->id)->get();
                          if($rest->num_rows()>0)
                          {
                          foreach($rest->result() as $rowww);
                          $str .= '<td>'.(float)$rowww->cav.'</td>';
                          }
                    
                        }
                     } 
 
                    $rest=$this->db->select('id')->from('trial_cavity')->where('trial_detail_id',$row->id)->where('trial_id',$get_trial_parts->id)->get();
                    if($rest->num_rows()>0){
                      foreach($rest->result() as $rttt)
                      {
                      $cav=0;
                      $rest=$this->db->select('hjig_t1_cav')->from('trial_cavity')->where('id',$rttt->id)->get();
                          if($rest->num_rows()>0)
                          {
                          foreach($rest->result() as $rowww);
                          $str .= '<td>'.(float)$rowww->hjig_t1_cav.'</td>';
                          }
                    
                        }
                     }  
                    $rest=$this->db->select('id')->from('trial_cavity')->where('trial_detail_id',$row->id)->where('trial_id',$get_trial_parts->id)->get();
                    if($rest->num_rows()>0){
                      foreach($rest->result() as $rttt)
                      {
                      $cav=0;
                      $rest=$this->db->select('sup_final_cav')->from('trial_cavity')->where('id',$rttt->id)->get();
                          if($rest->num_rows()>0)
                          {
                          foreach($rest->result() as $rowww);
                          $str .= '<td>'.(float)$rowww->sup_final_cav.'</td>';
                          }
                    
                        }
                     } 
                     $rest=$this->db->select('id')->from('trial_cavity')->where('trial_detail_id',$row->id)->where('trial_id',$get_trial_parts->id)->get();
                    if($rest->num_rows()>0){
                      foreach($rest->result() as $rttt)
                      {
                      $cav=0;
                      $rest=$this->db->select('hjig_final_cav')->from('trial_cavity')->where('id',$rttt->id)->get();
                          if($rest->num_rows()>0)
                          {
                          foreach($rest->result() as $rowww);
                          $str .= '<td>'.(float)$rowww->hjig_final_cav.'</td>';
                          }
                    
                        }
                     } 
 



         $str .='  <td>'.$row->judgement.'</td>
                  <td>'.$row->inspection.'</td>
                  </tr>';
           $i++;
            }
          }


    $str .= '</table>
<br>
<br>

<br>';


      }
    }

$html .= $str;

$html .= '<p></p>
<p></p>
<p></p>
<p></p>
<table width="100%" >
  <tr>
    <td width="40%">
      <table style="padding:3px; font-size:10px;" width="100%" border="1" ruled="all">
        <tr>
          <td style=" background-color:lightgrey;">Date of Test  </td>
          <td>'.date('d-M-Y', strtotime($get_trial_parts->test_date)).'</td>
        </tr>
        <tr>
          <td style="background-color:lightgrey;">Check Date </td>
          <td>'.date('d-M-Y', strtotime($get_trial_parts->check_date)).'</td>
        </tr>
        <tr>
          <td style="background-color:lightgrey;">Checked Date </td>
          <td>'.date('d-M-Y', strtotime($get_trial_parts->checked_date)).' </td>
        </tr>
        <tr>
          <td style=" background-color:lightgrey;">Approved By </td>
          <td>'.$get_trial_parts->approved_by.'</td>
        </tr>
      </table>
    </td>
    <td width="10%"></td>
    <td width="10%" style="padding:3px; font-size:10px;">Remarks</td>
    <td width="40%" style="height:150px; border:1px solid black; padding:3px; font-size:10px;">
    '.$get_trial_parts->remarks.'</td>
  </tr>
</table>
';
//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';
$fileNL = $filelocation . "/akash.pdf"; //Linux
$pdf->Output($fileNL, 'I');
//============================================================+
// END OF FILE
//============================================================+