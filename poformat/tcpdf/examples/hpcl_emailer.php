<?php
session_start();
// echo $_SESSION['logged_in']['user_id'];exit;
define('redirect_path','https://gamavis.com/dynachem/index.php/');
// echo 'hi';exit;
include('mysqliconfig.php');
$id=$_GET['lead_id'];
if($id<>'')
{
        $sel="SELECT added_by,unique_id,unique_no, contact_person, company_name, customer_name, email_id, city, state, contact_no, postal_address, check_terms, validity_date, general_terms, bulk_terms, alt_contact_no, hpcl_company FROM leads WHERE id='$id'";
        $coures=mysqli_query($con,$sel);
        $row_cnt = mysqli_num_rows($coures);
        if($row_cnt==0)
        {
        echo "INVALID ACCESS"; exit;
        }else
        {
        $row=mysqli_fetch_array($coures);
            $unique_id=$row['unique_id'];
            $unique_no=$row['unique_no'];
            $contact_person = $row['contact_person'];
            $alt_contact_no = $row['alt_contact_no'];
            $customer_name = $row['customer_name'];
            $company_name = $row['company_name'];
            $city = $row['city'];
            $contact_no = $row['contact_no'];
            $address = $row['postal_address'];
            $general_terms = $row['general_terms'];
            $bulk_terms = $row['bulk_terms'];
            $email_id = $row['email_id'];
            $hpcl_company = $row['hpcl_company'];
            $added_by = $row['added_by'];
             $tnc = $row['general_terms'];
            // if($row['check_terms'] == 1) {
            //     $tnc = $row['bulk_terms'];
            // } else {
            //     $tnc = $row['general_terms'];
            // }
            if($row['validity_date'] == '0000-00-00') {
                  $validity_date = '';
              } else {
                  $validity_date = date('d-m-Y',strtotime($row['validity_date']));
              }


        }




            $sel31="SELECT first_name,last_name,contact_number,email FROM system_users WHERE user_id='$added_by'";
            $coures23=mysqli_query($con,$sel31); 
            $row_cnt23 = mysqli_num_rows($coures23);
            if($row_cnt23>0)
            {
            $row23=mysqli_fetch_array($coures23);
            $sales_fname=$row23['first_name'];
            $sales_lname=$row23['last_name'];
            $sales_contact_number=$row23['contact_number'];
            $sales_email=$row23['email'];
            }else
            {
             $sales_fname='';
            $sales_lname='';
            $sales_contact_number='';
            $sales_email='';
            }


}else
{
    echo "Invalid Request"; exit;
}

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

class MYPDF extends TCPDF {
    // Page footer
    public function Footer() {
       // echo "hi"; exit;
    $this->SetY(-25);
    $logoX = 0; // 
    $logoFileName = K_PATH_IMAGES.'footer-img.png';

    //echo  $logoFileName ; exit;
    $logoWidth = 210; // 15mm
    $logoY = 272;
    $logo = $this->Image($logoFileName, $logoX, $logoY, $logoWidth);
    $this->SetX($this->w - 18 - $logoWidth); // documentRightMargin = 18
    $this->Cell(10,10, $logo, 0, 0, 'C');
    }
}



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Akash');
$pdf->SetTitle('Emailer');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
 // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.'', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0,0,0), array(255,255,255));
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

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
// $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = '
<table style="width:100%; font-size:13px; padding:5px;">
<tr>
                                    <td width="50%">To <br/>'.ucwords(strtolower($customer_name)).',</td>
                                    <td width="50%" style="text-align:right;">Quotation Date: '.date('d-m-Y').'</td>
                                </tr>
								<tr>
								<td width="40%">'.ucwords(strtolower($company_name)).'<br/>
									'.ucwords(strtolower($address)).'
								</td>
								<td width="30%"></td>
								<td width="30%" style="text-align:right;">Quotation ID: QUOTE'.$unique_no.'<br/>Validity: '.$validity_date.'</td>
							</tr>
</table>
<br>
                            <br>
                            <table style="width: 100%; font-size:13px; padding: 5px;">
                                <tr>
                                    <td width="10%"></td>
                                    <td width="80%" style="text-align:center; font-weight:bold;">Sub: Quotation of our Products.
                                    </td>
                                    <td width="10%"></td>
                                </tr>
                            </table>
							<table style="width: 100%; font-size:13px; padding: 5px;">
							<tr>
								<td>
									Dear Sir/ Madam,<br>

Thank you for taking out time to meet us. In reference to our meeting discussion held regarding cleaning chemicals supply to your respective business units, we hereby offer you Quotation for the products below. <br>

Kindly let us know when we can meet and close the contract. <br> 
								</td>
							</tr>
						</table>
						<br><br>
						<table style="width: 100%; font-size:13px;" border="1">
						<tr>
                        
							<th style="padding: 5px; background-color: lightgray; text-align:center; font-weight:bold;" width="30%">Product</th>
                             <th style="padding: 5px; background-color: lightgray;  text-align:center; font-weight:bold;" width="20%">List Price / Unit</th>
							<th style="padding: 5px; background-color: lightgray;  text-align:center; font-weight:bold;" width="10%">Qty</th>
                            <th style="padding: 5px; background-color: lightgray;  text-align:center; font-weight:bold;" width="20%">Discount(%)</th>
							<th style="padding: 5px; background-color: lightgray;  text-align:center; font-weight:bold;" width="20%">Offered Price</th>
							
						</tr>';
                            $gradtotalamount[] = 0;
                            $sel2="SELECT b.id, b.unit, a.competitor_product,a.qty, a.percent_amt, a.net_price, a.price, b.instruments_name, c.shortname FROM lead_products a JOIN presto_instruments b ON b.id=a.product_id LEFT JOIN units c ON c.id=a.packsize WHERE a.lead_id='$id'";
                            $coures1=mysqli_query($con,$sel2); 
                            $row_cnt1 = mysqli_num_rows($coures1);
                            if($row_cnt1>0)
                            {

                            while($row1=mysqli_fetch_array($coures1))
                            {
                                //echo "<pre>"; print_r($row1); exit;
                                $discountpercent = round($row1['percent_amt'],1);

                                $price = round($row1['price']);
                                $unitname = $row1['unit'];
                                $gradtotalamount[] = $row1['net_price'];
                              
                                //echo $unitname; exit;

                            $html.='<tr>
                            <td style="padding: 5px;  text-align:center;">'.$row1['instruments_name'].'</td>
                            <td style="padding: 5px; text-align:center;">'.$price.' / '.$unitname.'</td>    
                            <td style="padding: 5px; text-align:center;">'.$row1['qty'].' '.$unitname.'</td>
                            <td style="padding: 5px; text-align:center;">'.$discountpercent.'</td>                            
                            <td style="padding: 5px; text-align:center;">'.$row1['net_price'].'</td>                            
                            </tr>';

                            }
                            $html.='<tr>
                            <td colspan="4" style="text-align:right; font-weight:bold;">Final Total </td>
                            <td style="text-align:center;">'.array_sum($gradtotalamount).'</td>
                            </tr>';
                            }


					$html.='</table><br><br>
					<table style="width: 100%; font-size:13px;">
                                <tr>
                                    <td>
                                       <b>Terms and Conditions</b>';

                                    $html.=$tnc;
                                        
                                    $html.='</td>
                                </tr>
                            </table><br/><br/>';


                            $html.='<table style="width: 100%; font-size:13px;">
                                <tr>
                                    <td><i>Please feel free to contact us incase of any further queries. We shall be
                                            more than happy to assist/resolve all your queries.</i>
                                    </td>
                                </tr>
                            </table><br><br>';

                            $sel3="SELECT * FROM store_rack_location WHERE id='$hpcl_company'";
                            $coures2=mysqli_query($con,$sel3); 
                            $row_cnt2 = mysqli_num_rows($coures2);
                            if($row_cnt2>0)
                            {
                            $row2=mysqli_fetch_array($coures2);
                            $contact_person=$row2['contact_person'];
                            $companyname=$row2['companyname'];
                            $company_address=$row2['address'];
                            $email_id=$row2['email_id'];
                            $landline_number=$row2['landline_number'];
                            $mobile=$row2['mobile'];
                            $alt_mobile=$row2['alt_mobile'];
                            $googlemap=$row2['googlemap'];
                            $locate_us=$row2['locate_us'];
                            }else
                            {
                            $contact_person='';
                            $companyname='';
                            $company_address='';
                            $email_id='';
                            $landline_number='';
                            $mobile='';
                            $alt_mobile='';
                            $googlemap='';
                            $locate_us='';

                            }

							$html.='<br><table style="width: 100%; font-size:13px;">
                                <tr>
                                    <td>'.$sales_fname.' '.$sales_lname.'<br>'.$companyname.'<br>'.$company_address.'<br/>MOBILE '.$sales_contact_number.'<br/>Email :'.$email_id.'
                                    </td>
                                </tr>
                            </table>';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//echo $_SERVER['DOCUMENT_ROOT']; exit;
$filelocation = $_SERVER['DOCUMENT_ROOT'].'/dynachem/quotation_pdf';
$fileNL =$filelocation."/".$unique_id.'_'.str_replace('/','',str_replace(' ','_',$company_name)).".pdf"; //Linux
$pdf->Output($fileNL,'F');
// echo redirect_path.'Leads/preview_pdf_quote/'.$id;exit;
header('location:'.redirect_path.'Leads/preview_pdf_quote/'.$id);
//$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
