<?php
// Include the main TCPDF library (search for installation path).
include('mysqlconfig.php');

/*** QUERY **/
$id=$_GET['quoteid'];
$flag=0;
if(isset($_GET['flag']))
{
$flag=$_GET['flag'];
} 
$sel="SELECT * FROM vendors WHERE id='$id'";
 $coures=mysqli_query($con,$sel);
 $row_cnt = mysqli_num_rows($coures);
 
 
 $sel1="SELECT a.* FROM purchase_order a WHERE a.vendor='$id' AND a.completed='0' AND a.approved='1' AND a.emailnotified='0'";
 $coures1=mysqli_query($con,$sel1);
 $row_cnt1 = mysqli_num_rows($coures1);


  $sel1sc="SELECT a.* FROM poinstructions a";
 $coures1sc=mysqli_query($con,$sel1sc);
 $row_cnt1sc = mysqli_num_rows($coures1sc);
 if($row_cnt1sc>0)
 {
 	$roe=mysqli_fetch_array($coures1sc);

 	$script=$roe['script'];

 }else
 {
 	$script='';
 }



/** QUERY ENDS **/


require_once('tcpdf/tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);



// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
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
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

/** quert paet **/
 if($row_cnt>0)
  {
  
   $data=mysqli_fetch_array($coures);
   
	$company=$data['name'];
	$address=$data['address'];
	$contact_number=$data['phone'];
	$contactperson=$data['contactperson'];
	$email=$data['email'];
	$paymentterm=$data['paymentterm']; 
	$freight=$data['freight'];
	$vendorgst=$data['gst'];
	$frieghtpercentage=$data['freightpercentage'];
	$packingpercentage=$data['packingpercentage'];
  
  }
  
  
  
/** end **/

$html ='
<p></p><p></p><table>
<tr>
<td style="width:250px">'.$company.'<br/> '.$address.'<br/>
Mobile: '.$contact_number.'<br/>
Email: '.$email.'<br/>
GST: '.$vendorgst.'</td>
<td style="width:200px"></td>
<td style="width:200px">Date: '.date('d-M-Y').'</td>
</tr>
</table>
<br/><br/>
<table>
<tr>
<td>Kind Attn : '.$contactperson.'<br/>Subject :Purchase Order<br/></td>
</tr>
</table> ';


if($row_cnt1>0)
{


$html.='<table align="center" cellspacing="0" cellpadding="1" border="1">';

   $html.=' <tr>
	<td width="20"><strong>Sr</strong></td>
	<td width="80"><strong>Po No.</strong></td>
	<td width="200"><strong>Product</strong></td>
	<td width="40"><strong>Qty.</strong></td>
	<td width="50"><strong>Price.</strong></td>
	<td width="30"><strong>Dis.</strong></td>
	<td width="40"><strong>Disc. Rate</strong></td>
	<td width="60"><strong>Total Amt.</strong></td>
	<td width="60"><strong>GST %.</strong></td>
	<td width="60"><strong>GST Total.</strong></td>';

   $html.='</tr>';
    
  
    $i=1;

$amcarr[]=0;
$gsttot[]=0;
    while($data =mysqli_fetch_array($coures1))
    {
		$itemid=$data['itemid'];
		$unit=$data['unit'];
		$pono=$data['pono'];
		if($data['potype']=='0')
		{
			$sel1="SELECT a.part,a.specification,a.size_in_mm,a.gst FROM machine_parts_with_picture a WHERE a.id='$itemid'";
			$coures111=mysqli_query($con,$sel1);
			$row_cnt111 = mysqli_num_rows($coures1);
			if($row_cnt111>0)
			{
				$data12324 =mysqli_fetch_array($coures111);
				$itemname=$data12324['part'];
				$specification=$data12324['specification'];
				$size=$data12324['size_in_mm'];
				$gst=$data12324['gst'];
				
			}else
			{
				$itemname='';
				$specification='';
				$size='';
				$gst='';

			}



		}else
		{

			$sel1="SELECT a.itemname,a.hsn,a.gst FROM house_keeping_items a WHERE a.id='$itemid'";
			$coures111=mysqli_query($con,$sel1);
			$row_cnt111 = mysqli_num_rows($coures1);
			if($row_cnt111>0)
			{
				$data12324 =mysqli_fetch_array($coures111);
				$itemname=$data12324['part'];
				$specification='';
				$size='';
				$hsn=$data12324['hsn'];
				$gst=$data12324['gst'];
			}else
			{
				$itemname='';
				$specification='';
				$size='';
				$hsn='';
				$gst='';
			}

		}



		$sel123="SELECT shortname FROM units WHERE id='$unit'";
			$coures11122=mysqli_query($con,$sel123);
			$row_cnt11111 = mysqli_num_rows($coures11122);
			if($row_cnt11111>0)
			{
				$data1232412323 =mysqli_fetch_array($coures11122);
				$unitname=$data1232412323['shortname'];
				
			}else
			{
				$unitname='';

			}



				$sel1234="SELECT listprice FROM vendors_price WHERE vendorid='$id' AND itemid='$itemid'";
			$coures111224=mysqli_query($con,$sel1234);
			$row_cnt111114 = mysqli_num_rows($coures111224);
			if($row_cnt111114>0)
			{
				$data12324123234=mysqli_fetch_array($coures111224);
				$listprice=$data12324123234['listprice'];
				
			}else
			{
				$listprice='';

			}



$as='';
if($specification<>'')
{
	$as.='<br/>'.$specification;
}

if($size<>'')
{
	$as.=$size;
}
	$totwithqty=$data['price']*$data['qty'];

	$ggst=$gst/100;
	$gstamt=$totwithqty*$ggst;
$gsttot[]=$gstamt;
	$grandtot=$totwithqty+$gstamt;
	$cost[]=$totwithqty;
	$html.='<tr>
	<td>'.$i.'</td>
	<td>'.$pono.'</td>
	<td>'.$itemname.$as.'</td>
	<td>'.floatval($data['qty']).' '.$unitname.'</td>
	<td>'.floatval($listprice).'</td>
	<td>'.floatval($data['discount']).'%</td>
	<td>'.floatval($data['price']).'</td>
	<td>'.floatval($totwithqty).'</td>
	<td>'.floatval($gst).'</td>
	<td>'.floatval($gstamt).'</td>

	</tr>';
	$amcarr[]=floatval($totwithqty);
	$i++;
	}


if(count($amcarr)>0)
	{
	$totalam=array_sum($amcarr);
	}else
	{
	$totalam='';
	}
		
		if(count($gsttot)>0)
	{
	$totalgst=array_sum($gsttot);
	}else
	{
	$totalgst='0';
	}
		

	
	$html.='<tr>
	<td colspan="6" align="left"></td>
	<td>Total</td>

<td>'.floatval($totalam).'</td>
	
	<td></td>
	<td>'.floatval($totalgst).'</td>
	
	
	</tr>'; 

	

	if($frieghtpercentage<>'')
	{
		$fr=$frieghtpercentage/100;
		$frightcharge=floatval($totalam)*$fr;

	$html.='<tr>
	<td colspan="7" align="right"><strong>Add Freight Charges @ '.$frieghtpercentage.'%</strong></td>
	<td><strong>'.floatval($frightcharge).'</strong></td>
	<td></td>
	<td></td>
	</tr>'; 
	}else
	{
$frightcharge=0;
	}


	if($packingpercentage<>'')
	{
	$fr=$packingpercentage/100;
	$packingcharge=floatval($totalam)*$fr;

	$html.='<tr>
	
	<td colspan="7" align="right"><strong>Add Freight Charges @ '.$packingpercentage.'%</strong></td>
	<td><strong>'.floatval($packingcharge).'</strong></td>
	<td></td>
	<td></td>
	</tr>'; 
	}else
	{
	$packingcharge=0;
	}

	if($vendorgst<>''){								
	$statecode = substr($vendorgst,0,2);
	if($statecode=='06'){

$csgt=$totalgst/2;

	$html.='<tr>

	<td colspan="7" align="right"><strong>Add CGST Charges</strong></td>
	<td><strong>'.floatval($csgt).'</strong></td>
	<td></td>
	<td></td>
	</tr><tr>
	
	<td colspan="7" align="right"><strong>Add SGST Charges</strong></td>
	<td><strong>'.floatval($csgt).'</strong></td>
	<td></td>
	<td></td>
	</tr>'; 


		}else{

			$csgt=$totalgst;
	$html.='<tr>

	<td colspan="7" align="right"><strong>Add IGST Charges</strong></td>
	<td><strong>'.floatval($csgt).'</strong></td>
	<td></td>  
	<td></td>
	</tr>';

		}

	}else
	{
		$cgst=0;
	}
									
								//	echo floatval($totalam).'<br/>'.$frightcharge.'<br/>'.$packingcharge.'<br/>'.$totalgst; exit;
	
	$grandtot=floatval($totalam)+$frightcharge+$packingcharge+$totalgst;
		$html.='<tr>

	<td colspan="7" align="right"><strong>Grand Total</strong></td>
	<td><strong>'.round(floatval($grandtot),2).'</strong></td>
	<td></td>
	<td></td>
	</tr>'; 					






	
	/*** CURRENCY CODE **/
	$number=floatval($grandtot);
	$decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');

    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }

    $rupees = implode('', array_reverse($str));
    $paise = '';

    if ($decimal) {
        $paise = 'and ';
        $decimal_length = strlen($decimal);

        if ($decimal_length == 2) {
            if ($decimal >= 20) {
                $dc = $decimal % 10;
                $td = $decimal - $dc;
                $ps = ($dc == 0) ? '' : '-' . $words[$dc];

                $paise .= $words[$td] . $ps;
            } else {
                $paise .= $words[$decimal];
            }
        } else {
            $paise .= $words[$decimal % 10];
        }

        $paise .= ' paise';
    }

   $words=$rupees.'rupees '. $paise ;
	
	/** END ***/
	
$html.='<tr>
	<td colspan="10" align="center" style="text-align:center"><strong>In Words: '.ucwords($words).'</strong></td>
	</tr>';
	
	
	



$html.='</table>';

}

$html.='<hr style=" border: 0;
    height: 1px;
    background: #333;"><p></p>
     <table style="width:700px">
<tr>
<td><strong>Instructions: </strong>'.$script.'</td>
</tr></table>


   

<table style="width:700px">
<tr>
<td><strong>Remarks: </strong>'.$data['remarks'].'</td>
</tr>

</table>';	


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/popdf';
$fileNL = $filelocation."/".$company.".pdf"; //Linux
if($flag=='1')
{
$pdf->Output($fileNL, 'F');
}else
{
//$pdf->Output($fileNL, 'I');
$pdf->Output($fileNL,'F');
header('location:https://prestomitr.com/Store/previewpo/'.$id);
 }



//============================================================+
// END OF FILE
//============================================================+
