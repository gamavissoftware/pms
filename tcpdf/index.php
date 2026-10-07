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
$sel="SELECT * FROM calibrationquotehistory WHERE id='$id'";
 $coures=mysqli_query($con,$sel);
 $row_cnt = mysqli_num_rows($coures);
 
 
 $sel1="SELECT * FROM calibrationmachinehistory WHERE quoteid='$id'";
 $coures1=mysqli_query($con,$sel1);
 $row_cnt1 = mysqli_num_rows($coures1);



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
   
	$company=$data['company'];
	$address=$data['address'];
	$pino=$data['pino'];
	$contact_number=$data['contact_number'];
	$person_name=$data['person_name'];
	$email=$data['email'];
	$type=$data['type'];
	$state=$data['state'];
	$quote=date('d-F-Y',strtotime($data['addedOn']));
  
  }
  
  
  
/** end **/

$html ='
<p></p><p></p><table>
<tr>
<td style="width:250px">'.$company.'<br/> '.$address.'<br/>
Mobile: '.$contact_number.'<br/>
Email: '.$email.'</td>
<td style="width:200px"></td>
<td style="width:200px">Ref No. '.$pino.'<br/>'.$quote.'</td>
</tr>
</table>
<br/><br/>
<table>
<tr>
<td>Kind Attn : '.$person_name.'<br/>Subject :Proforma Invoice against your enquiry for Presto Testing Equipments<br/></td>
</tr>
</table> ';


if($row_cnt1>0)
{


$html.='<table align="center" cellspacing="0" cellpadding="1" border="1">';
if($type=='1')
{

   $html.=' <tr>
	<td width="50"><strong>Sr no</strong></td>
	<td width="350"><strong>Product</strong></td>
	<td width="50"><strong>Qty.</strong></td>
	<td width="100"><strong>HS Code</strong></td>
	<td width="80"><strong>AMC Charge</strong></td>
    </tr>';
    
 }else if($type==2)
 {
 
	$html.='<tr>
	<td width="50"><strong>Sr no</strong></td>
<td width="350"><strong>Product</strong></td>
<td width="50"><strong>Qty.</strong></td>
<td width="100"><strong>HS Code</strong></td>
<td width="80"><strong>Calibration Charge</strong></td>

	</tr>';
	
	

 
 
 }else
 {
 
 
	$html.='<tr>
	<td width="50"><strong>Sr no</strong></td>
<td width="350"><strong>Product</strong></td>
<td width="50"><strong>Qty.</strong></td>
<td width="100"><strong>HS Code</strong></td>
<td width="80"><strong>Visit Charge</strong></td>

	</tr>';

 }
    
    $i=1;
	$totalarr=array();
	$totalarr[]=0;
	$amcarr=array();
	$amcarr[]=0;
    while($data =mysqli_fetch_array($coures1))
    {
 
    $idsss=$data['machineid'];
    $sel11="SELECT instruments_name,hsncode FROM presto_instruments WHERE id='$idsss'";

	$coures111=mysqli_query($con,$sel11);
	$row_cnt11 = mysqli_num_rows($coures111);
	if($row_cnt11>0)
	{
	$dataq=mysqli_fetch_array($coures111);
	$insname=$dataq['instruments_name'];
	$hsncode=$data1['hsncode'];
	}else
	{
	$insname='';
	$hsncode='';
	}

      $visitcharge=$data['visit'];
      $dividevisitcharge=$visitcharge/$row_cnt1;
      $addvisitcharge=$dividevisitcharge;
	if($type==1)
	{
	$html.='<tr>
	<td>'.$i.'</td>
	<td>'.$insname.'</td>
	<td>1</td>
	<td>'.$hsncode.'</td>
	
	<td>'.floatval($data['amc']+$addvisitcharge).'</td>
	</tr>';
	$amcarr[]=floatval($data['amc']+$addvisitcharge);
	}else if($type==2)
	{
	
	$html.='<tr>
	<td>'.$i.'</td>
	<td>'.$insname.'</td>
	<td>1</td>
	<td>'.$hsncode.'</td>
	<td>'.floatval($data['amc']+$addvisitcharge).'</td>
	</tr>';
	$amcarr[]=floatval($data['amc']+$addvisitcharge);
	}else
	{

	$html.='<tr>
	<td >'.$i.'</td>
	<td>'.$insname.'</td>
	<td>1</td>
	<td>'.$hsncode.'</td>
	<td>'.floatval($visitcharge).'</td>
	</tr>';
	
	$amcarr[]=floatval($visitcharge);
	}
	
	
	
	
	$i++;
	}


if(count($amcarr)>0)
	{
	$totalam=array_sum($amcarr);
	}else
	{
	$totalam='';
	}
		
	$html.='<tr>
	<td colspan="4" align="left">Total</td>
	<td>'.floatval($totalam).'</td>
	</tr>';
	
	$cgst = (floatval($totalam)*9)/100;
	$sgst = (floatval($totalam)*9)/100;
	$igst = (floatval($totalam)*18)/100;
	if($state=='11')
	{	
	$grandTotal = floatval($totalam) + floatval($cgst) + floatval($sgst);
	$html.='<tr>
	<td colspan="4" align="left">ADD CGST (9%)</td>
	<td>'.floatval($cgst).'</td>
	</tr>';
	
	$html.='<tr>
	<td colspan="4" align="left">ADD SGST (9%)</td>
	<td>'.floatval($sgst).'</td>
	</tr>';
	}else
	{
	$grandTotal = floatval($totalam) + floatval($igst);
	$html.='<tr>
	<td colspan="4" align="left">ADD IGST (18%)</td>
	<td>'.floatval($igst).'</td>
	</tr>';
	
	
	}
	
	$html.='<tr>
	<td colspan="4" align="left">Grand Total</td>
	<td>'.floatval($grandTotal).'</td>
	</tr>';
	
	/*** CURRENCY CODE **/
	$number=floatval($grandTotal);
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
	<td colspan="5" align="left">Amount in words: '.$words.'</td>
	</tr>';
	
	
	



$html.='</table>';

}

$html.='<hr style=" border: 0;
    height: 1px;
    background: #333;"><p></p>
     <table style="width:250px">
<tr>
<td><strong>Terms & Conditions:</strong></td>
</tr></table>


    <table>
<tr>
<td><strong>1:</strong> <strong>Charges</strong> :The above given charges are for Calibration only & not for servicing of the instrument</td>
</tr>
<tr>
<td><strong>2:</strong> <strong>Payment Terms</strong> :100% prior to execution of work</td>
</tr>
<tr> 
<td><strong>3:</strong> <strong>Spares Cost</strong> : pares are not included in the cost, they will be charged extra ( if needed ).</td>
</tr>
<tr>
<td><strong>4:</strong> <strong> Important Note</strong> :For effective calibration the machine should be in working condition. The above charges are on per day basis.</td>
</tr>
<tr>
<td><strong>5:</strong><strong>Payment</strong> :Please remit the payment in favour of M/s for Presto Stantest Pvt. Ltd. to enable us to schedule the calibration visit .</td>
</tr>
<tr>
<td><strong>6:</strong><strong>Validity</strong> :30 Days.</td>
</tr>

    </table>

<p></p>
     <table style="width:350px">
<tr>
<td><strong>Bank Details:<br>Bank Name : ICICI Bank Ltd<br>Bank Account No : 008305002578<br>Account Name : Presto Stantest Pvt Ltd<br>Bank Branch Address : Booth 104-105, District Centre, Sector -16, Faridabad - 121007<br/>MICR No - 110229010<br/>RTGC/NEFT IFSC Code :- ICIC0000083</strong></td>
</tr><

</table>

<table style="width:350px">
<tr>
<td>Team customer care<br/>service@prestogroup.com<br/>+91-129-4272727<br/>+91-9810449839</td>
</tr>

</table>';	


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'].'/image_bank/quotationpi/';
$fileNL = $filelocation."/".str_replace('/','-',$pino).'.pdf'; //Linux
if($flag=='1')
{
$pdf->Output($fileNL, 'F');
}else
{
$pdf->Output($fileNL, 'F');
$pdf->Output($fileNL,'FD');
 }



//============================================================+
// END OF FILE
//============================================================+
