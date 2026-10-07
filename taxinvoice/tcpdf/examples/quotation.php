<?php
session_start();
// echo $_SESSION['logged_in']['user_id'];exit;
define('redirect_path','https://crm.sunderindoil.com/index.php/');
// echo 'hi';exit;
include('mysqliconfig.php');
$id=$_GET['quotation_id'];
if($id<>'')
{
        $sel="SELECT a.added_by,a.id, a.ref_id, a.check_terms, a.general_terms, a.bulk_terms, a.validity_date, a.company_id, b.company_name, b.customer_name, b.contact_no, b.email, b.address FROM customer_quotation a JOIN customer_detail b ON b.id=a.customer_id WHERE a.id=$id";
         //echo $sel;exit;
        $coures=mysqli_query($con,$sel);
        $row_cnt = mysqli_num_rows($coures);
        if($row_cnt==0)
        {
        echo "INVALID ACCESS"; exit;
        }else
        {

        $row=mysqli_fetch_array($coures);
            $customer_name = $row['customer_name'];
            $company_name = $row['company_name'];
            $contact_no = $row['contact_no'];
            $email_id = $row['email'];
            $city = $row['city'];
            $address = $row['address'];
            $unique_id= 'QUOTE'.$id;
            $added_by = $row['added_by'];
            $contact_person = '';
            $alt_contact_no = $row['alt_contact'];
            $hpcl_company = $row['company_id'];
            if($row['check_terms'] == 1) {
                $tnc = $row['bulk_terms'];
            } else {
                $tnc = $row['general_terms'];
            }
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

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Akash');
$pdf->SetTitle('Emailer');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
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
                                    <td width="50%">To,</td>
                                    <td width="50%" style="text-align:right;">Date: '.date('d-m-Y').'</td>
                                </tr>
								<tr>
								<td width="40%">'.$customer_name.'<br>
									'.$company_name.'<br/>
									'.$address.'
								</td>
								<td width="30%"></td>
								<td width="30%" style="text-align:right;">REF ID: '.$unique_id.'</td>
							</tr>
</table>
<br>
                            <br>
                            <table style="width: 100%; font-size:13px; padding: 5px;">
                                <tr>
                                    <td width="10%"></td>
                                    <td width="80%" style="text-align:center;">Sub: Quotation of Industrial Lubricants.
                                    </td>
                                    <td width="10%"></td>
                                </tr>
                            </table>
							<table style="width: 100%; font-size:13px; padding: 5px;">
							<tr>
								<td>
									Sir,<br>
									In reference to our meeting discussion held regarding Industrial Oil supply to
									your respective business units therefore, we hereby offer you Quotation for the
									products as required by yourself.
								</td>
							</tr>
						</table>
						<br><br>
						<table style="width: 100%; font-size:13px;" border="1">
						<tr>
                        <th style="padding: 5px; background-color: lightgray;" width="30%">Competitor Product</th>
                            <th style="padding: 5px; background-color: lightgray;" width="30%">Our Equivalent Product</th>
                            <th style="padding: 5px; background-color: lightgray;" width="15%">Qty</th>
                            <th style="padding: 5px; background-color: lightgray;" width="25%">Offered Price</th>
                            
                        </tr>';

                            $sel2="SELECT c.shortname,a.pack_size,a.competitor_product,a.qty, a.list_price, b.instruments_name FROM customer_quotation_detail a JOIN presto_instruments b ON b.id=a.product_id LEFT JOIN units c ON c.id=a.pack_size  WHERE a.quotation_id='$id'";
                           
                            $coures1=mysqli_query($con,$sel2); 
                            $row_cnt1 = mysqli_num_rows($coures1);
                            if($row_cnt1>0)
                            {

                            while($row1=mysqli_fetch_array($coures1))
                            {

                               $p=$row1['shortname'];

                            $html.='<tr>
                            <td style="padding: 5px;">'.$row1['competitor_product'].'</td>
                            <td style="padding: 5px;">'.$row1['instruments_name'].'</td>
                            <td style="padding: 5px;">'.$row1['qty'].' '.$p.'</td>
                            <td style="padding: 5px;">'.$row1['list_price'].'</td>							
                            </tr>';

                            }
                            }


					$html.='</table>
					<table style="width: 100%; font-size:13px; padding: 20px;">
                                <tr>
                                    <td>
                                       <strong>Terms and Conditions</strong>';

                                    $html.=$tnc;
                                        
                                    $html.='</td>
                                </tr>
                            </table>
							<table style="width: 100%; font-size:13px; padding: 5px;">
                                <tr>
                                    <td>
                                        <i>Please feel free to contact us incase of any further queries. We shall be
                                            more than happy to assist/resolve all your queries.</i>
                                    </td>
                                </tr>
                            </table>
							<table style="width: 100%; font-size:13px; padding: 5px;">
                                <tr>
                                    <td style="color: red;">Please Note: - We are the only authorized C&F Agents for Industrial lubricants
                                        for<b>M/s HINDUSTAN PETROLEUM CORPORATION LIMITED</b>in Faridabad district and
                                        that We/HPCL does not take any responsibility for any unauthorized product supplied
                                        by unauthorized/illegitimate supplier.
                                    </td>
                                </tr>
                            </table>';

                            $sel3="SELECT * FROM store_rack_location WHERE id='$hpcl_company'";
                            $coures2=mysqli_query($con,$sel3); 
                            $row_cnt2 = mysqli_num_rows($coures2);
                            if($row_cnt2>0)
                            {
                            $row2=mysqli_fetch_array($coures2);
                            $contact_person=$row2['contact_person'];
                            $companyname=$row2['companyname'];
                            $company_address=$row2['company_address'];
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

							 $html.='<table style="width: 100%; font-size:13px; padding: 5px;">
                                <tr>
                                    <td>
                                       <b>with regards</b><br><br/>'.$sales_fname.' '.$sales_lname.'<br>'.
                                        $companyname.'<br>AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>'.$company_address.'<br> Emails :'.$sales_email.'<br>Office Landline No. '.$landline_number.'<br>MOBILE '.$sales_contact_number.'<br>Please view us on GoogleMap:-'.$googlemap.'<br>Please locate us on HPCL Website:'.$locate_us.'
                                    </td>
                                </tr>
                            </table>';


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$filelocation = $_SERVER['DOCUMENT_ROOT'].'/quotation_pdf';
$fileNL =$filelocation."/".$unique_id.'_'.str_replace('/','',str_replace(' ','_',$company_name)).".pdf"; //Linux
$pdf->Output($fileNL,'F');
// echo redirect_path.'Leads/preview_pdf_quote/'.$id;exit;
header('location:'.redirect_path.'Customer/preview_pdf_quote/'.$id);
//$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
