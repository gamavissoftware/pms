<?php
$order_won_id = $this->uri->segment(3);
$CI = &get_instance();
$CI->load->model('TCPDF_model');
$get_buyoff_mould = $CI->TCPDF_model->get_buyoff_mould($order_won_id);


$mould_category = $CI->TCPDF_model->b2_mould_buyoff_category();


// $packing_list_details = $CI->TCPDF_model->get_packing_list_details($packing_list->id);
// echo "<pre>";
// print_r($get_buyoff_mould);
if($get_buyoff_mould){
  $mold_weight = $get_buyoff_mould->mold_weight; 
    $tool_lifting_approval= $get_buyoff_mould->tool_lifting_approval;
}else{
  $mold_weight = ''; 
  $tool_lifting_approval= '';
}




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

<table width="100%" style="padding:3px;">
<tr>
<td>
<h2 style="text-align:center;">Tool Buy Off</h2>
</td>
</tr>
</table>
<p></p>
<table width="100%" border="1" ruled="all" style="padding:3px;">
<tr>
<td width="45%" style="background-color:lightgrey;"><b>TOOLBUY OFF CHECK LIST</b></td>
<td width="55%" style="text-align: right; background-color:lightgrey;"><b>(Before Trial)</b></td>
</tr>
</table>
<br>
<br>
<table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody>' ;
                  $str ='';
                 
                        if($mould_category){

                          foreach($mould_category as $category){
                            $str .= '<tr>
                                    <td width="45%" style="background-color:lightgrey;"><b>'.$category->category_name.'</b> </td>
                                    <td width="5%" style="background-color:lightgrey;"></td>
                                    <td class="text-center" width="5%" style="background-color:lightgrey;"><b>YES</b></td>
                                    <td width="5%" style="background-color:lightgrey;"></td>
                                    <td class="text-center" width="5%" style="background-color:lightgrey;"><b>NO</b></td>
                                    <td width="5%" style="background-color:lightgrey;"></td>
                                    <td class="text-center" width="5%" style="background-color:lightgrey;"><b>NA</b></td>
                                    <td width="5%" style="background-color:lightgrey;"></td>
                                    <td class="text-center" width="20%" style="background-color:lightgrey;"><b>Upload Evidence Pictures</b></td>
                                </tr>';

                                 $mould_subcategory = $CI->TCPDF_model->b2_mould_buyoff_subcategory($category->id);


                            if($mould_subcategory){
                              foreach($mould_subcategory as $subcategory){

                                $query1 = $this->db->select('*')->from('b2_buyoff_mould_buyoff')->where('subcategory_id',$subcategory->id)->where('b2_buyoff_mould_id',$get_buyoff_mould ->id)->get();

                                // $yes_no = $query1->result();
                            if($query1->num_rows() > 0){
                                foreach($query1->result() as $yes_no);

                                // print_r($yes_no) ; 
                                $evidence =  $yes_no->evidence ;
                                if($yes_no->yes_no_na == 1){
                                  $yes = 'YES';
                                  $no = '';
                                  $na = '';
                                }else if($yes_no->yes_no_na == 0){
                                  $yes = '';
                                  $no = 'NO';
                                  $na = '';
                                } 
                                else{
                                  $yes = '';
                                  $no = '';
                                  $na = 'N/A';
                                }
                            }else{
                              $yes = '';
                              $no = '';
                              $na = '';
                              $evidence = '';
                            }

                              
                                $str .= '<tr>
                                    <td class="text-center">'.$subcategory->subcat_name.'</td>
                                    <td></td>
                                   <td class="text-center">'.$yes.'</td>
                                   <td></td>
                                   <td class="text-center">'.$no.'</td>
                                   <td></td>
                                   <td class="text-center">'.$na.'</td>
                                   <td></td>
                                   <td class="text-center"><a href="'.ASSETSPATH.'b2_buyoff_evidence/'.$evidence .'"> '.$evidence .'</a></td>
                                   <td></td>
                                </tr>';

                              }
                            }
                          }
                        }

                                
$html .= $str;
$html .= '     </tbody></table>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%" style="background-color:lightgrey;"><b>Mold Weight</b></td>
                                    <td width="55%">'.$mold_weight.'</td>
                                </tr>
                            </tbody></table>
                            <p></p>
                            <table width="100%" border="1" ruled="all" style="padding:3px;">
                                <tbody><tr>
                                    <td width="45%" style="background-color:lightgrey;"><b>Tool Lifting Approval</b> <span>*</span></td>
                                    <td width="55%">'.$tool_lifting_approval.'</td>
                                </tr>
                            </tbody></table>
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
