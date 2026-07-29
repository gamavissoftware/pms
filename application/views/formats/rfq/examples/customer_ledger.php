<?php   

$CI =& get_instance();

$CI->load->model('Salescrm_model');

$EI =& get_instance();

$EI->load->model('Sourcing_model');

$lead_id=$this->uri->segment(3);
$won_id=$this->uri->segment(4);
$restey=$this->db->select('company_name,customer_name,email,contact_no,currency_preference,unique_id')->from('leads')->where('id',$lead_id)->get();
if($restey->num_rows()>0)
{
    foreach($restey->result() as $rowss);
    $company_name=$rowss->company_name;
    $customer_name=$rowss->customer_name;
    $contact_no=$rowss->contact_no;
    $email=$rowss->email;
    $currency_preference=$rowss->currency_preference;
    if($currency_preference==0)
    {

        $sign="$";
    }else
    {
        $sign="₹";
    }
    $query_no=$rowss->unique_id;
}else {
    echo "INVALID REQUEST"; exit;
}


$project_name=$CI->Salescrm_model->getProjectName($lead_id);

/** GET PERFORMA INVOICE **/
$query=$this->db->select('a.lead_id, a.option_id, a.supplier_id,b.negotiated_price,b.order_punched_on,b.pi_generated_On')
->from('client_quoted_price a')
->join('order_won b', 'b.final_price=a.id')
->where('b.id',$won_id)
->get();

if($query->num_rows() > 0) {
foreach($query->result() as $rows);
$option_id=$rows->option_id;
$supplier_id=$rows->supplier_id;
$orderOn=date('d M Y',strtotime($rows->order_punched_on));
$pidateday = date('D',strtotime($rows->pi_generated_On));
$pidatedate = date('d',strtotime($rows->pi_generated_On));
$pidateyear= date('Y',strtotime($rows->pi_generated_On));
$pidatemonth= date('M',strtotime($rows->pi_generated_On));
$pidatemonths= date('m',strtotime($rows->pi_generated_On));

}else
{
    echo "INVALID REQUEST"; exit;
}


$getOrderAmt = $EI->Sourcing_model->getOrderAmt($lead_id, $option_id, $supplier_id);
$final_price = '';
if ($getOrderAmt != '') {
    foreach ($getOrderAmt as $row);
        $final_price = $row->final_price;

        if($row->currency_rate > 0) {
            $currency_rate = $row->currency_rate;
        } else {
            $currency_rate = 75;
        }
} else {
    $final_price = '';
    $currency_rate = '';
}


$getShipment = $EI->Sourcing_model->getShipment($this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5), $final_price);
$getServicesAmt = $EI->Sourcing_model->getServicesAmt($this->uri->segment(3));

if ($currency_preference== 1) {
    $amt = $final_price * $currency_rate;
    $word = 'in INR';
} else {
    $amt = $final_price;
    $word = 'in USD';
}


$total_order_amt =  $amt + $getServicesAmt;

$sql1=$this->db->select('b.lead_id')
->from('order_won a')
->join('negotiation_meeting_details b', 'b.lead_id=a.lead_id')
->where('a.id',$won_id)
->get();

if($sql1->num_rows() == 0) {
// foreach($sql1->result() as $row);
$negotiation = 0;
} else {
$negotiation = 1;
}
//echo $total_order_amt; exit;


/** CUSTOMER TOOLING ADVANCE PAYMENT **/
$sql2=$this->db->select('payment_done_amt,recd_date')
->from('payment_recd_details')
->where('lead_id',$lead_id)
->get();


/** GET FIRST PAYMENT TERM **/
$payable=0;
$restey=$this->db->select('payment_stage,stage,percentage')->from('payment_terms')->where('lead_id',$this->uri->segment(3))->order_by('id','ASC')->limit(1)->get();
if($restey->num_rows()>0)
{
    foreach($restey->result() as $pterm);
    $pay_term=$pterm->percentage;
    $term=$pterm->percentage/100;

    $payable=$amt*$term;
    $payable=$payable+$getServicesAmt;

}else
{
    echo "NO PAYMENT TERM FOUND PLEASE ADD PAYMENT TERM AND TRY AGAIN"; exit;
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

    <table width="100%">
        <tr>
            <td width="80%" style="text-align:left;"><h2>'.ucwords(strtolower($company_name)).' Ledger for '.ucwords(strtolower($project_name)).' Project</h2></td>
            <td width="20%">
                <img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" alt="logo" width="200px">
            </td>
        </tr>
    </table>

     <table width="100%">
        <tr>
            <td width="80%" style="text-align:left;"><p><b>Customer Name: '.$customer_name.'</b></p>
            <p><b>Mobile Number: '.$contact_no.'</b></p>
            <p><b>Email ID: '.$email.'</b></p></td>
            <td width="20%">
               
            </td>
        </tr>
    </table>

    <div></div>
    <table width="100%" border="1" ruled="all"  style=" padding: 3px; text-align: center;;font-size: 10px;">
        <tr>
            <th width="5%" style="background-color:lightgrey;"><b>S.No.</b></th>
            <th width="20%" style="background-color:lightgrey;"><b>Description</b></th>
            <th width="8%" style="background-color:lightgrey;"><b>Total Amount</b></th>
            <th width="30%" style="padding: 0; background-color:lightgrey;"><div style="margin-bottom:10px;"><b>Performa Invoice</b></div>
                <table width="100%" style=" padding: 3px; text-align: center;font-size: 10px;" border="1" ruled="all">
                    <tr>
                        <th width="60%"><b>Date | PI Number</b></th>
                        <th width="40%"><b>Amount USD</b></th>
                    </tr>
                </table>
            </th>
            <th width="30%" style="padding: 0; background-color:lightgrey;"><div style="margin-bottom:0px;"><b>Amount Received</b></div>
                <table width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;" border="1" ruled="all">
                    <tr>
                        <th width="40%"><b>Date</b></th>
                        <th width="30%"><b>INR</b></th>
                        <th width="30%"><b>Balance</b></th>
                    </tr>
                </table>
            </th>
            <th width="7%" style="background-color:lightgrey;"><b>Balance</b></th>
        </tr>';
        $total_bal=array();
        $total_bal[]=0;
        $html.='<tr>
                    <td>1</td>
                    <td><b>Tooling Services</b><br/>
                        <a href="'.page_url1.'pdf/rfq/examples/quotation.php?lead_id='.$lead_id.'&option='.$option_id.'&suplier='.$supplier_id.'">Quotation</a> | <a href="'.page_url1.'pdf/rfq/examples/performa.php?lead_id='.$lead_id.'&option='.$option_id.'&suplier='.$supplier_id.'&negotiation='.$negotiation.'">Sales Agreement</a></td>
                    <td>'.$sign.$total_order_amt.'</td>
                    <td style="padding: 0;"><b></b>
                        <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';

                       

                        $html.='<tr>
                                <td width="60%">'.$orderOn.'| PI/'.$pidatedate.$pidatemonths.$pidateyear.'/'.$query_no.'</td>
                                <td width="40%">'.$sign.$payable.'</td>
                            </tr>';

                        
                             $html.='
                        </table>
                    </td>
                    <td style="padding: 0;"><b></b>
                        <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';


                        if($sql2->num_rows()>0) {
                       //     echo "<pre>"; print_r($sql2->result()); exit;
                            $oldbal=0;
                        foreach($sql2->result() as $rowsss)
                        {
                            if($currency_preference==0)
                            {
                            $recv_inr=$rowsss->payment_done_amt*$currency_rate;
                            }else
                            {
                                $recv_inr=$rowsss->payment_done_amt;
                            }
                            $bal=$payable-$rowsss->payment_done_amt;

                        $html.='<tr>
                        <td width="40%">'.date('d M Y',strtotime($rowsss->recd_date)).'<br/></td>
                        <td width="30%">₹'.round($recv_inr,2).'<br/></td>
                        <td width="30%">$'.$bal.'<br/></td>
                        </tr>';
                        $total_bal[]=$bal;
                        $oldbal=$bal;
                        }
                        }

                        /** MOCKUP SERVICE **/





                        /** END **/
                           
                        $html.='</table>
                    </td>
                    <td>$'.array_sum($total_bal).'</td>
                </tr>';


                $mbal=array();
                $mbal[]=0;
                $rest=$this->db->select('*')->from('mockup_pi_for_client')->where('order_won_id',$won_id)->get();
                if($rest->num_rows()>0)
                {
                    foreach($rest->result() as $mockup);
                    if($mockup->currency_type==0)
                    {
                        $msign="$";
                    }else
                    {
                        $msign="₹";
                    }
                    $total_amount=floatval($mockup->order_amt+$mockup->shipment_amt);

                    $mpayable=0;
                   $getClientPIPaymentTerms = $CI->salescrm->getClientPIPaymentTerms_mockup($mockup->id);
                    if($getClientPIPaymentTerms != '') {
                    foreach ($getClientPIPaymentTerms as $row2);
                    $paybaleper=$row2->percentage;
                    $mpayable=$total_amount*($paybaleper/100);
                    }

               $html.='<tr>
                <td>2</td>
                <td><b>Mockup Service</b><br/>
                    Performa Invoice</td>
                <td>'.$msign.$total_amount.'</td>
                <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">
                        <tr>
                            <td width="60%">'.date('d M , Y',strtotime($mockup->added_on)).' | </td>
                            <td width="40%">'.$msign.$mpayable.'</td>
                        </tr>
                     
                    </table>
                </td>
                <td style="padding: 0;"><b></b>
                    <table border="1" ruled="all" width="100%" style=" padding: 3px; text-align: center;;font-size: 10px;">';
                                if($mockup->currency_type==0)
                                {
                                $recv_m_inr=$mockup->paid_amount*$mockup->currency_rate;
                                }else
                                {
                                $recv_m_inr=$mockup->paid_amount;
                                }
                               

                                if($mockup->currency_type==0)
                                {
                                $bal_m=$mpayable-$mockup->paid_amount;
                                }else
                                {
                                $bal_m=($mpayable-$mockup->paid_amount)/$mockup->currency_rate;
                                }

                                $mbal[]=$bal_m;

                        $html.='<tr>
                            <td width="40%">'.date('d M , Y',strtotime($mockup->payment_date)).'</td>
                            <td width="30%">'.$msign.$mockup->paid_amount.'</td>
                            <td width="30%">$'.round($bal_m,2).'</td>
                        </tr>
                    </table>
                </td>
                <td>$'.round($bal_m,2).'</td>
            </tr>';
            } 
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
