<?php
// Include the main TCPDF library (search for installation path).
define('UPLOADPATH','https://'.$_SERVER['HTTP_HOST'].'/assets/client_bom_data/part_image/');

/** QUERY STARTS **/
include('mysqlconfig.php'); 
$lead_id = $_GET['lead_id'];
$supplier_id = $_GET['supplier_id'];
// echo $lead_id;exit;
$sql20 = "SELECT supplier_id FROM suppliers WHERE supplier_id=$supplier_id AND supplier_location=101";
$result20 = mysqli_query($con, $sql20);

if(mysqli_num_rows($result20) > 0) {
  $symbol = '₹';
  $in_words = 'INR';
} else {
  $symbol = '¥';
  $in_words = 'CNY';
}

require_once('tcpdf/tcpdf_include.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

  


}



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setCellPaddings(0,0,0,0);

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
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

/** quert paet **/
 
  
/** end **/

  
$html='
<table >
<tr>
<td></td>
<td style="text-align:center;"><img src="../tcpdf/tcpdf/images/hong_logo.jpg" style="width:200px;"></td>
<td></td>
</tr>
</table>
<br><br><br>
<table style="font-size:9px; padding:3px;">

<tr>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey; width:70px;"></th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part Code<br>Part Name<br>Part Image</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part CFM</th>
<th  style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part Size (mm)</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Part Weight (Grams)</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Cavitation</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Tool Steel Cavity<br>Tool Steel Core<br>Tool Steel Mould Base </th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey; width:50px;">Runner Type </th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Insert Moulding</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Remarks </th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Mould Size</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Mould Weight in Kg</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Required Machine Tonnage</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Cycle Time in sec</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Price in '.$in_words.'</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Pre-Tooling Lead Time<br>Lead Time in days<br>T-0 to Dispatch Date</th>
<th style="text-align:center; border:1px solid black;   background-color:lightgrey;">Remarks</th>
</tr>';

$sql = "SELECT id, type FROM bom_moulds_detail WHERE lead_id=$lead_id";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
  $mld = 1;
  $mouldcheck = array();
  $mould_weight = array();
  $priceindollar = array();

while ($data = mysqli_fetch_array($result)) {
  $mould_id = $data['id'];
  $type = $data['type'];

      if ($type == 1) {
        $mould_type = 'Single';
      } else if ($type == 2) {
        $mould_type = 'Multi';
      } else if ($type == 3) {
        $mould_type = 'Family';
      }
  $sql1 = "SELECT id, mould_id, code, name, image, partfinish, partlength, partheight, partwidth, cavity FROM bom_part_details WHERE lead_id = $lead_id AND mould_id = $mould_id";
  $result1 = mysqli_query($con, $sql1);
  $partscount = mysqli_num_rows($result1);
  $i = 1;

  while ($data1 = mysqli_fetch_array($result1)) {
  $part_id = $data1['id'];

  if ($data1['partfinish'] == 1) {
        $fin = "High Gloss Mirror";
      } else if ($data1['partfinish'] == 2) {
        $fin = "High Gloss";
      } else if ($data1['partfinish'] == 3) {
        $fin = "Normal Polish";
      } else if ($data1['partfinish'] == 4) {
        $fin = "Texture Matt Finish (" . $data['matcode'] . ")";
      }

        if($data1['partlength'] == '') {
            $part_length = 0;
        } else {
            $part_length = $data1['partlength'];
        }

        if($data1['partwidth'] == '') {
            $part_width = 0;
        } else {
            $part_width = $data1['partwidth'];
        }

        if($data1['partheight'] == '') {
            $part_height = 0;
        } else {
            $part_height = $data1['partheight'];
        }

  $sql2 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage,b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid = $mould_id";
      $result2 = mysqli_query($con, $sql2);
      $data2 = mysqli_fetch_array($result2);
      $color = '';
      if ($data2['colortype'] == 1) {
        $color = $data2['colourname'];
      } else {
        $color = "Special Color-" . $data2['pantonecode'];
      }

      $material = $data2['partname'] . $data2['actual_shrinkage'];

  $html .= '<tr>';

    if (!in_array($mould_id, $mouldcheck)) {
    $html .='<td style="border:1px dotted black;" rowspan="' . $partscount . '"><strong>MOULD ' . $mld . '<br>' . $mould_type . '</strong></td>';
  }

  	$sql3 = "SELECT a.insertmoulding, a.benchmarkweight, b.name as runner_name FROM bom_part_additional_details a LEFT JOIN runner_type b ON b.id=a.runner_type WHERE a.partdetailid = $part_id";
	$result3 = mysqli_query($con, $sql3);
	$data3 = mysqli_fetch_array($result3);


	if ($data3['insertmoulding'] == 1) {
	    $moulding = 'Yes';
	} else {
	    $moulding = 'No';
	}

	$sql4 = "SELECT a.remarks, b.material_grade as tool_cavity, c.material_grade as mould_base, d.material_grade as core_cavity FROM bom_moulds_detail a LEFT JOIN steel_type b ON a.mouldcavitysteel=b.id LEFT JOIN steel_type c ON a.mouldbasesteel=c.id LEFT JOIN steel_type d ON a.mouldcoresteel=d.id WHERE a.id = $mould_id";
	$result4 = mysqli_query($con, $sql4);
	$data4 = mysqli_fetch_array($result4);

	$sql5 = "SELECT x, y, z, mould_weight, req_tonnage, cycle_time, price_in_dollar, pre_tooling_time, tooling_lead_time, dispatch_date, remarks FROM sourcing_quotation WHERE lead_id = $lead_id AND supplier_id=$supplier_id AND mould_id=$mould_id AND option_id=0";
	$result5 = mysqli_query($con, $sql5);
	$data5 = mysqli_fetch_array($result5);



  $html .= '<td style="border:1px dotted black;"><strong>Part Code-</strong>'.$data1['code'].'<hr><strong>Part Name-</strong>'.ucwords($data1['name']).'<hr><img src="'.UPLOADPATH.$data1['image'].'"></td>
  <td style="border:1px dotted black;"><strong>Part Colour</strong><br/>' . $color . '<br/><br/>
      <strong>Surface Finish</strong><br/>' . $fin . '<br/><br/>
      <strong>Material</strong><br/>' . $material . '</td>
  <td style="border:1px dotted black;"><strong>L-</strong>'.$part_length.'<br/><strong>W-</strong>'.$part_width.'<br/><strong>H-</strong>'.$part_height.'</td>
  <td style="border:1px dotted black;">'.$data3['benchmarkweight'].'</td>
  <td style="border:1px dotted black;">'.$data1['cavity'].'</td>
  <td style="border:1px dotted black;"><strong>Tool Steel Cavity</strong><br>'.$data4['tool_cavity'].'<hr><strong>Tool Steel Core</strong><br>'.$data4['core_cavity'].'<hr><strong>Tool Steel Mould Base</strong><br>'.$data4['mould_base'].'</td>
  <td style="border:1px dotted black;">'.$data3['runner_name'].'</td>
  <td style="border:1px dotted black;">'.$moulding.'</td>';
   if (!in_array($mould_id, $mouldcheck)) {
  	$html .= '<td style="border:1px dotted black;" rowspan="' . $partscount . '">'.$data4['remarks'].'</td>
  		<td style="border:1px dotted black;" rowspan="' . $partscount . '"><strong>X-</strong>'.$data5['x'].'<br/><strong>Y-</strong>'.$data5['y'].'<br/><strong>Z-</strong>'.$data5['z'].'</td>
  		<td style="border:1px dotted black;" rowspan="' . $partscount . '">'.$data5['mould_weight'].'</td>
  		<td style="border:1px dotted black;" rowspan="' . $partscount . '">'.$data5['req_tonnage'].'</td>
  		<td style="border:1px dotted black;" rowspan="' . $partscount . '">'.$data5['cycle_time'].'</td>
  		<td style="border:1px dotted black;" rowspan="' . $partscount . '">'.$data5['price_in_dollar'].'</td>
  		<td style="border:1px dotted black;" rowspan="' . $partscount . '"><strong>Pre-Tooling Lead Time-</strong>'.$data5['pre_tooling_time'].'<hr><strong>Lead Time in Days-</strong>'.$data5['tooling_lead_time'].'<hr><strong>T-0 to Dispatch Date-</strong>'.$data5['dispatch_date'].'</td>
  		<td style="border:1px dotted black;" rowspan="' . $partscount . '">'.$data5['remarks'].'</td>
  		';

	}
  $html .= '</tr>';

  if ($partscount > 1) {
        $mouldcheck[] = $mould_id;
      }
    $i++;
    }

      $sql6 = "SELECT id, type,runner_type,runner_brand,gating,tips,steelbrand,steel,id FROM bom_mouldwise_options WHERE lead_id=$lead_id AND mould_id=$mould_id";
  $result6 = mysqli_query($con, $sql6);
  while($data6 = mysqli_fetch_array($result6)) {
        $optionID = $data6['id'];
        $run_brand = $data6['runner_brand'];
        $gatingg = $data6['gating'];
        $steel_brand = $data6['steelbrand'];
        $steel = $data6['steel'];

   if($data6['type']==1) {
      $t="Different Runner Type";
      $runnertype=$data6['runner_type'];
                                
        if($runnertype==1) {
          $trunname="Hot Runner";

          $sql7="SELECT name FROM hot_runner_brand WHERE id=$run_brand";
          $result7 = mysqli_query($con, $sql7);
          $data7 = mysqli_fetch_array($result7);
          $runner_brand = $data7['name'];
                             
          $sql8 = "SELECT name FROM gating_type WHERE id=$gatingg"; 
          $result8 = mysqli_query($con, $sql8);
          $data8 = mysqli_fetch_array($result8);
          $gating = $data8['name'];
          $tips=$data8['tips'];
          $detail=$trunname." | Brand - ".$runner_brand." | Gating - ".$gating." | Tips - ".$tips;

          } else {

          $trunname="Cold Runner";
          $sql8 = "SELECT name FROM gating_type WHERE id=$gatingg"; 
          $result8 = mysqli_query($con, $sql8);
          $data8 = mysqli_fetch_array($result8);
          $gating = $data8['name'];
          $detail=$trunname." | Gating - ".$gating;
          }
        } else if ($data6['type']==2) {
          $sql7="SELECT manufacturer_name FROM manufacturer WHERE id=$steel_brand";
          $result7 = mysqli_query($con, $sql7);
          $data7 = mysqli_fetch_array($result7);
          $steelbrand = $data7['manufacturer_name'];
          $sql8="SELECT material_grade FROM steel_type WHERE id=$steel";
          $result8 = mysqli_query($con, $sql8);
          $data8 = mysqli_fetch_array($result8);
          $steelname = $data8['material_grade'];
          $t="Different Steel";
          $detail=" Brand - ".$steelbrand." | Steel Type - ".$steelname;

        } else if ($data6['type']==3) {
                                
          $detail='';
          $t="Different Steel and Runner Type";
          $runnertype=$data6['runner_type'];
                                
              if ($runnertype == 1) {
                $trunname="Hot Runner";
                $sql7="SELECT name FROM hot_runner_brand WHERE id=$run_brand";
                $result7 = mysqli_query($con, $sql7);
                $data7 = mysqli_fetch_array($result7);
                $runner_brand = $data7['name'];
                $sql8 = "SELECT name FROM gating_type WHERE id=$gatingg"; 
                $result8 = mysqli_query($con, $sql8);
                $data8 = mysqli_fetch_array($result8);
                $gating = $data8['name'];
                $tips=$data8['tips'];
                $detail.=$trunname." | Brand - ".$runner_brand." | Gating - ".$gating." | Tips - ".$tips."<br/>";
                } else {
                $trunname="Cold Runner";
                $sql8 = "SELECT name FROM gating_type WHERE id=$gatingg"; 
                $result8 = mysqli_query($con, $sql8);
                $data8 = mysqli_fetch_array($result8);
                $gating = $data8['name'];
                $detail.=$trunname." | Gating - ".$gating." | ";
                $sql7="SELECT manufacturer_name FROM manufacturer WHERE id=$steel_brand";
                $result7 = mysqli_query($con, $sql7);
                $data7 = mysqli_fetch_array($result7);
                $steelbrand = $data7['manufacturer_name'];
                $sql9="SELECT material_grade FROM steel_type WHERE id=$steel";
                $result9 = mysqli_query($con, $sql9);
                $data9 = mysqli_fetch_array($result9);
                $steelname = $data9['material_grade'];
                $t="Different Steel";
                $detail.=$trunname."| Brand - ".$steelbrand." | Steel Type - ".$steelname;
                }
              } else {
                $t="";
                      }

              $sql10 = "SELECT x, y, z, mould_weight, req_tonnage, cycle_time, price_in_dollar, pre_tooling_time, tooling_lead_time, dispatch_date, remarks FROM sourcing_quotation WHERE lead_id = $lead_id AND supplier_id=$supplier_id AND mould_id=$mould_id AND option_id=$optionID";
              $result10 = mysqli_query($con, $sql10);
              $data10 = mysqli_fetch_array($result10);


    $html .= '<tr>
                <td colspan="10" class="text-align:left whitesmoke" style="color:black;font-weight:bold; border:1px dotted black;" ><div class="col-md-4">Alternate Option for Mould '.$mld.' - '.$t.'</div><div class="col-md-8">'.$detail.'</div></td>
                <td style="border:1px dotted black;" class="whitesmoke"><strong>X-</strong>'.$data10['x'].'<br/><strong>Y-</strong>'.$data10['y'].'<br/><strong>Z-</strong>'.$data10['z'].'</td>
                <td style="border:1px dotted black;" class="whitesmoke">'.$data10['mould_weight'].'</td>
                <td style="border:1px dotted black;" class="whitesmoke">'.$data10['req_tonnage'].'</td>
                <td style="border:1px dotted black;" class="whitesmoke">'.$data10['cycle_time'].'</td>
                <td style="border:1px dotted black;" class="whitesmoke">'.$data10['price_in_dollar'].'</td>
                <td style="border:1px dotted black;" class="whitesmoke"><strong>Pre-Tooling Lead Time-</strong>'.$data10['pre_tooling_time'].'<hr><strong>Lead Time in Days-</strong>'.$data10['tooling_lead_time'].'<hr><strong>T-0 to Dispatch Date-</strong>'.$data10['dispatch_date'].'</td>
                <td style="border:1px dotted black;" class="whitesmoke">'.$data10['remarks'].'</td>
              </tr>';

            }
 $mld++;
  }
}
$html .= '</table>';

    



//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
//$pdf->writeHTML($footertext, false, true, false, true);

//$footer_logo_html='Hello';

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$version = '';
$sql11 = "SELECT id FROM sourcing_quotation WHERE lead_id=$lead_id AND supplier_id=$supplier_id";
$result11 = mysqli_query($con, $sql11);
if(mysqli_num_rows($result11) > 0) {
  $version = 0;
}


$filelocation = $_SERVER['DOCUMENT_ROOT'].'image_bank/supplier/quotations';
$fileNL = $filelocation . "/quotation_" . $lead_id ."_". $supplier_id ."_0.pdf"; //Linux
ob_clean();
$pdf->Output($fileNL,'F');


$filenameee = "quotation_" . $lead_id ."_". $supplier_id ."_0.pdf"; //Linux

$filepreviewpath="https://hongyijig.in/image_bank/supplier/quotations/".$filenameee;
// header('location:https://hongyijig.in/index.php/Master/Supplier/send_supplier_quotation/'.$lead_id.'/'.$supplier_id);
$fileloc=base64_encode($filepreviewpath);
header('location:https://hongyijig.in/index.php/Sourcing/preview_pdf/'.$fileloc.'/'.$lead_id.'/'.$supplier_id);







//============================================================+
// END OF FILE
//============================================================+
