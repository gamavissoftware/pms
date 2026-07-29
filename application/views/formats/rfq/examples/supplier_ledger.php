<?php   
$CI =& get_instance();
$CI->load->model('Salescrm_model');

$EI =& get_instance();
$EI->load->model('Sourcing_model');

$DI =& get_instance();
$DI->load->model('Supplier_model');

$order_won_id=$this->uri->segment(3);
$supplier_id=$this->uri->segment(4);
$lead_id=$this->uri->segment(5);

$project_name = $CI->Salescrm_model->getProjectName($lead_id);
$getSupplierDetails = $DI->Supplier_model->getSupplierDetails($supplier_id);
$getSupplierServices = $CI->Salescrm_model->getSupplierServices($lead_id, $supplier_id);

$getSupplierDetails = $CI->salescrm->getSupplierFinalDetails($order_won_id);

$check_country = $CI->Salescrm_model->check_country($supplier_id);

if($check_country > 0) {
    $supplier_currency = '(INR)';
    $sign = '₹';
     $inr=1;
    $cny=0;
} else {
    $supplier_currency = '(CNY)';
    $sign = '¥';
     $inr=0;
    $cny=1;
}


$final_neg_price = '';

if ($getSupplierDetails != '') {
    foreach ($getSupplierDetails as $row6);
        $final_neg_price = $row6->final_neg_price;
        $cny_to_usd=$row6->cny_to_usd;
        $inr_to_cny=$row6->inr_to_cny;
        $inr_to_usd=$row6->inr_to_usd;
        $selected_currency=$row6->currency;
        $pocurr=$selected_currency;


        if($selected_currency<>0)
        {
        if($selected_currency==1)
        {
        $ssym="$";
        }else if($selected_currency==2)
        {
        $ssym="₹";
        }else
        {
        $ssym="¥";
        }
        }else
        {
        $ssym=$sign;
        }


         if(($pocurr==2 && $inr==1) || ($pocurr==3 && $cny==1))
        {
            $conv=$final_neg_price;
            $conv_rate_now=1;
        }else
        {
            if($inr==1)
            {
                /** indian supplier **/

                if($pocurr==1)
                {
                    /** USD **/

                    $mul=1;
                    $divide=$inr_to_usd;
                    $conv=round(($final_neg_price*$mul)/$divide);
                    $conv_rate_now=$inr_to_usd;

                }else if($pocurr==3)
                {
                    $mul=$inr_to_cny;
                    $divide=1;
                    $conv=round(($final_neg_price*$mul)/$divide);
                     $conv_rate_now=$inr_to_cny;

                }else
                {
                    $conv=$final_neg_price;
                    $conv_rate_now=1;
                }



            }else if($cny==1)
            {
                /** CNY SUPPLIER **/

                if($pocurr==1)
                {
                    $mul=$cny_to_usd;
                    $divide=1;
                    $conv=round(($final_neg_price*$mul)/$divide);
                    $conv_rate_now=$cny_to_usd;

                }else if($pocurr==2)
                {

                    $mul=1;
                    $divide=$inr_to_cny;
                    $conv=round(($final_neg_price*$mul)/$divide);
                     $conv_rate_now=$inr_to_cny;
                }else
                {
                    $conv=$final_neg_price;
                    $conv_rate_now=1;
                }

            }else
            {
                $conv=$final_neg_price;
                $conv_rate_now=1;
            }

        }

    



}

// echo $final_neg_price;exit;



$company_name = '';
$company_spokes_person_name = '';
$spokes_person_mobile = '';
$spokes_person_emailid = '';

if(count($getSupplierDetails) > 0) {
    foreach($getSupplierDetails as $row1);
    $company_name = $row1->company_name;
    $company_spokes_person_name = $row1->company_spokes_person_name;
    $spokes_person_mobile = $row1->spokes_person_mobile;
    $spokes_person_emailid = $row1->spokes_person_emailid;
}

$getMockUpServicePayment = $CI->Salescrm_model->getMockUpServicePayment($order_won_id, $supplier_id);
$getPrototypeServicePayment = $CI->Salescrm_model->getPrototypeServicePayment($order_won_id);
$checkToolingSuppAdvPayment = $CI->Salescrm_model->checkToolingSuppAdvPayment($order_won_id);

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

    <table width="100%">
        <tr>
            <td width="80%" style="text-align:left;"><h2> Ledger for '.ucwords(strtolower($project_name)).' Project</h2></td>
            <td width="20%">
                <img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px">
            </td>
        </tr>
    </table>

     <table width="100%">
        <tr>
            <td width="80%" style="text-align:left;"><p><b>Company Name: '.$company_name.'</b></p>
            <p><b>Spokesperson Name: '.$company_spokes_person_name.'</b></p>
            <p><b>Spokesperson Mobile Number: '.$spokes_person_mobile.'</b></p>
            <p><b>Spokesperson Email ID: '.$spokes_person_emailid.'</b></p></td>
            <td width="20%">
               
            </td>
        </tr>
    </table>

    <div></div>
    <table width="100%" border="1" ruled="all"  style=" padding: 3px; text-align: center;;font-size: 10px;">
        <tr>
            <th width="5%" style="background-color:lightgrey;"><b>S.No.</b></th>
            <th width="25%" style="background-color:lightgrey;"><b>Description</b></th>
            <th width="25%" style="background-color:lightgrey;"><b>Total Neg. Amount '.$supplier_currency.'</b></th>
            <th width="25%" style="padding: 0; background-color:lightgrey;"><div style="margin-bottom:0px;"><b>Amount Paid</b></div>
                <table width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;" border="1" ruled="all">';


         $html .=  '<tr>
                        <th width="60%"><b>Amt Paid</b></th>
                        <th width="40%"><b>Balance Amt</b></th>
                    </tr>';
         $html .=  '</table>
            </th>
            <th width="25%" style="background-color:lightgrey;"><b>Balance</b></th>
        </tr>';
       $service_name = '';
       $service_amt = '';

       $balanace_array=array();
       $paid_array=array();
       $paid_array[]=0;
       $balanace_array[]=0;

        if($getSupplierServices != '') {
            $i=1;
            foreach($getSupplierServices as $row2) {
        
        $html.='<tr>
                    <td></td>
                    <td><b>'.$row2->service_name.'</b></td>
                    <td>'.floatval($row2->service_amt).'</td>
                    <td style="padding: 0;"><b></b>
                        <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';

                        $paid_array[]=$row2->amount_paid;
                        $balanace_array[]=$row2->balance_amt;
                            $html.='<tr>
                                <td width="60%">'.$sign.floatval($row2->amount_paid).'</td>
                                <td width="40%">'.$sign.floatval($row2->balance_amt).'</td>
                            </tr>';
                           
                        $html.='</table>
                    </td>
                    <td>'.$sign.floatval($row2->balance_amt).'</td>
                </tr>';
             $i++;
               } 
           }


            if($getMockUpServicePayment != '') {
                foreach($getMockUpServicePayment as $row3);
               $html.='<tr>
                <td></td>
                <td><b>MOCKUP SERVICE</b></td>
                <td>'.floatval($row3->service_amt).'</td>
                <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';

                    $mockup_balance = $row3->service_amt - $row3->amount_paid;

                    $paid_array[]=$row3->amount_paid;
                    $balanace_array[]=$mockup_balance;
                        $html.='<tr>
                            <td width="60%">'.$sign.floatval($row3->amount_paid).'</td>
                            <td width="40%">'.$sign.floatval($mockup_balance).'</td>
                        </tr>
                    </table>
                </td>
                <td>'.$sign.$mockup_balance.'</td>
            </tr>';
             }

            if($getPrototypeServicePayment != '') {
                foreach($getPrototypeServicePayment as $row4);
               $html.='<tr>
                <td></td>
                <td><b>PROTOTYPE SERVICE</b></td>
                <td>'.$sign.floatval($row4->supplier_amt).'</td>
                <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';

                        $paid_array[]=$row4->amt_recd;
                        $balanace_array[]=$row4->balance_amt;

                        $html.='<tr>
                            <td width="60%">'.$sign.floatval($row4->amt_recd).'</td>
                            <td width="40%">'.$sign.floatval($row4->balance_amt).'</td>
                        </tr>
                    </table>
                </td>
                <td>'.$sign.floatval($row4->balance_amt).'</td>
            </tr>';
             }

             if($checkToolingSuppAdvPayment != '') {
                foreach($checkToolingSuppAdvPayment as $row5);
               $html.='<tr>
                <td></td>
                <td><b>TOOLING SERVICES</b></td>
                <td>'.$ssym.floatval($conv).'</td>
                <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';
                            $paid_array[]=$row5->amount_paid;
                            $balanace_array[]=$row5->amount_balance;
                        $html.='<tr>
                            <td width="60%">'.$ssym.floatval($row5->amount_paid).'</td>
                            <td width="40%">'.$ssym.floatval($row5->amount_balance).'</td>
                        </tr>
                    </table>
                </td>
                <td>'.$ssym.floatval($row5->amount_balance).'</td>
            </tr>';
             }

              $html.='<tr>';
              $html.='<td></td>';
              $html.='<td></td>';
              $html.='<td></td>';
               $html.=' <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';

                        $html.='<tr>
                            <td width="60%" style="background-color:lightgrey;"><b>'.$sign.floatval(array_sum($paid_array)).'</b></td>
                            <td width="40%" style="background-color:lightgrey;"><b></b></td>
                        </tr>
                    </table>
                </td>';
              $html.='<td style="background-color:lightgrey;"><b>'.$sign.floatval(array_sum($balanace_array)).'</b></td>';

              $html.='</tr>';





           

    $html.='</table>
    ';


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
