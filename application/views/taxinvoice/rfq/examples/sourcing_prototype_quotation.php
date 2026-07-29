<?php
define('image_url', '/var/www/hongyijig.in/assets/prototype_part_picture/');
define('IMAGEPATH', '/var/www/hongyijig.in/assets/client_bom_data/part_image/');

include('mysqlconfig.php');
$order_won_id = $_GET['order_won_id'];
$lead_id = $_GET['lead_id'];
$supplier_id = $_GET['supplier_id'];

$sql20 = "SELECT supplier_id FROM suppliers WHERE supplier_id=$supplier_id AND supplier_location=101";
$result20 = mysqli_query($con, $sql20);

if(mysqli_num_rows($result20) > 0) {
  $symbol = '₹';
  $in_words = 'INR';
} else {
  $symbol = '¥';
  $in_words = 'CNY';
}


require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

}
class MyCustomPDFWithWatermark extends TCPDF {
    public function Header() {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        $img_file = 'tcpdf/images/water.jpg';

        // Render the image
        $this->Image($img_file, 0, 0, 223, 280, '', '', '', false, 300, '', false, false, 0);

        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
    }
}


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MyCustomPDFWithWatermark(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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

// Add a page
// This method has several options, check the source code documentation for more information.
// $pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$pdf->AddPage('L', 'A4');


// Set some content to print

/** quert paet **/


$html .='
<table >
<tr>
<td></td>
<td style="text-align:center;"><img src="/var/www/hongyijig.in/pdf/logo.jpeg" style="width:200px;"></td>
<td></td>
</tr>
</table>
<br><br><br>
<table style="padding:3px; width:100%; border:1px solid lightgrey;">

<tr>
<td style="text-align:center; background-color:lightgrey;">PROTOTYPE QUOTATION
</td>
</tr>
</table>
<p></p>
<br>
<table style="width:100%; font-size:11px; text-align:center; padding:3px;" border="1">
<tr>
<th width="4%" style="background-color:lightgrey;">S.NO.</th>
<th width="18%" style="background-color:lightgrey;">PRODUCT NAME</th>
<th width="10%" style="background-color:lightgrey;">PART PICTURE</th>
<th width="10%" style="background-color:lightgrey;">PART WEIGHT<br><span style="font-size:8px;">(BENCHMARKED)</span></th>
<th width="10%" style="background-color:lightgrey;">QPS<br><span style="font-size:8px;">(Part Quality Per Set)</span></th>
<th width="10%" style="background-color:lightgrey;">TOTAL QTY</th>
<th width="13%" style="background-color:lightgrey;">CFM<br><span style="font-size:8px;">Colour/Finish/Material</span></th>
<th width="15%" style="background-color:lightgrey;">AMOUNT('.$symbol.')</th>
<th width="10%" style="background-color:lightgrey;">REMARKS</th>
</tr>';

       
        $query1 = "SELECT a.id,a.name,a.mould_id,a.code,a.datatype,a.dataimage,a.partfinish,a.matcode,a.partlength, a.partwidth, a.partheight, a.cavity,a.image, b.amount, b.remarks, c.qps, c.monthproduction FROM bom_part_details a JOIN prototype_sourcing_quotation b ON b.part_id=a.id LEFT JOIN bom_assembly_qps_detail c ON c.part_id=b.part_id WHERE a.lead_id=$lead_id AND b.part_type=1 AND b.supplier_id=$supplier_id";
        $result2 = mysqli_query($con,$query1);

        if(mysqli_num_rows($result2) > 0) {
            $i = 1;
        while($data2 =mysqli_fetch_array($result2)) {

            $part_id = $data2['id'];

            if($data2['partfinish']==1)
            {
                $fin1="High Gloss Mirror";
            }else if($data2['partfinish']==2)
            {
                $fin1="High Gloss";
            }else if($data2['partfinish']==3)
            {
                $fin1="Normal Polish";
            }else if($data2['partfinish']==4)
            {
                $fin1="Texture Matt Finish (".$data2['matcode'].")";
            }else if($data2['partfinish']==5)
            {
                $fin1="High Gloss + Texture";
            }

            if($data2['partlength'] == '') {
                $part_length1 = 0;
            } else {
                $part_length1 = $data2['partlength'];
            }
            if($data2['partwidth'] == '') {
                $part_width1 = 0;
            } else {
                $part_width1 = $data2['partwidth'];
            }

            if($data2['partheight'] == '') {
                $part_height1 = 0;
            } else {
                $part_height1 = $data2['partheight'];
            }

            /** Mould wise part detail **/
            $sql12 = "SELECT a.color,a.colortype,a.pantonecode,a.actual_shrinkage,b.colourname, c.partname FROM  bom_mould_wise_part_detail a LEFT JOIN part_colour b ON a.color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.mouldid=".$data2['mould_id'];

            $results12 = mysqli_query($con,$sql12);
            $datas12 = mysqli_fetch_array($results12);
            $color1='';
            if($datas12['colortype']==1)
            {
            $color1=$datas12['colourname'];
            }else
            {
            $color1="Special Color-".$datas12['pantonecode'];
            }
            /** end **/
            $material1 = $datas12['partname'].$datas12['actual_shrinkage'];



            $html .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$data2['name'].'</td>
                    <td><img src="'.IMAGEPATH.$data2['image'].'" width="100px"></td>
                    <td>L-'.$part_length1.'<br/> W-'.$part_width1.'<br/> H-'.$part_height1.'</td>
                    <td>'.$data2['qps'].'</td>
                    <td>'.$data2['monthproduction'].'</td>
                    <td><strong>Color-</strong>'.$color1.'<br/><strong>Finish-</strong> '.$fin1.'<br/><strong>Material-</strong>'.$material1.'</td>
                    <td>'.$data2['amount'].'</td>
                    <td>'.$data2['remarks'].'</td>
                  </tr>';
              $i++;  }
            } 




$sql = "SELECT a.part_name, a.part_picture, a.part_size_length, a.part_size_width, a.part_size_height, a.qps, a.total_qty, a.colour, a.part_color, a.pantoneshade, a.part_material, a.shrinkage, a.actual_shrinkage, a.part_finish, a.matt_code, a.matt_finish_upload, b.colourname, c.partname, d.amount, d.remarks FROM prototype_sourcing_quotation d JOIN prototype_bom_add_parts a ON a.id=d.part_id LEFT JOIN part_colour b ON a.part_color=b.id LEFT JOIN part_master c ON c.id=a.part_material WHERE a.order_won_id=$order_won_id AND d.part_type=2 AND d.supplier_id=$supplier_id";
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) > 0) {
   $i = $i; 
    while ($data = mysqli_fetch_array($result)) {

        $color='';

        if($data['colour']==1) {
            $color = $data['colourname'];
        } else {
            $color = "Special Color-".$data['pantoneshade'];
        }

        $material = $data['partname'].$data['actual_shrinkage'];

        if($data['part_finish']==1)
        {
            $fin="High Gloss Mirror";
        }else if($data['part_finish']==2)
        {
            $fin="High Gloss";
        }else if($data['part_finish']==3)
        {
            $fin="Normal Polish";
        }else if($data['part_finish']==4)
        {
            $fin="Texture Matt Finish (".$data['matt_code'].")";
        }else if($data['part_finish']==5)
        {
            $fin="High Gloss + Texture";
        }

$html .= '<tr>
            <td>'.$i.'</td>
            <td>'.$data['part_name'].'</td>
            <td><img src="'.image_url.$data['part_picture'].'" width="100px"></td>
            <td>L-'.$data['part_size_length'].'<br/> W-'.$data['part_size_width'].'<br/> H-'.$data['part_size_height'].'</td>
            <td>'.$data['qps'].'</td>
            <td>'.$data['total_qty'].'</td>
            <td><strong>Color-</strong>'.$color.'<br/><strong>Finish-</strong> '.$fin.'<br/><strong>Material-</strong>'.$material.'</td>
            <td>'.$data['amount'].'</td>
            <td>'.$data['remarks'].'</td>
          </tr>';
    $i++;
    }
}
$html .= '</table>

';	


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'image_bank/prototype_quotations';
$fileNL = $filelocation . "/quotation_" . $order_won_id ."_". $supplier_id ."_0.pdf"; //Linux
ob_clean();
$pdf->Output($fileNL, 'F');

$filenameee = "quotation_" . $order_won_id ."_". $supplier_id ."_0.pdf";
$filepreviewpath="https://hongyijig.in/image_bank/prototype_quotations/".$filenameee;

$fileloc=base64_encode($filepreviewpath);
header('location:https://hongyijig.in/index.php/FMS/preview_pdf/'.$fileloc.'/'.$order_won_id.'/'.$supplier_id.'/'.$lead_id);


//============================================================+
// END OF FILE
//============================================================+
