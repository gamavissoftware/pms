<?php
$CI =& get_instance();
$CI->load->model('BOM_model');
$project_name=$CI->BOM_model->getProjectName($this->uri->segment(3));
$client_info=$CI->BOM_model->clientinformation($this->uri->segment(3));
$customer_address=$CI->BOM_model->getProjectDeliveryAddress($this->uri->segment(3));
$lead_id=$this->uri->segment(3);
$optionid=$this->uri->segment(4);
$supplierid=$this->uri->segment(5);
$negotiation =$this->uri->segment(7);

if ($negotiation == '') {
    $negotiation = 0;   
}

if(count($client_info)>0)
{
    $customer_name=$client_info['customer_name'];
    $company_name=$client_info['company_name'];
    $unique_id=$client_info['unique_id'];
    $clienttype=$client_info['clienttype'];

}else
{
    $customer_name='';
    $company_name='';
    $unique_id='';
    $clienttype='';
}

$restey=$rest111=$this->db->select('currency_preference')->from('leads')->where('id',$lead_id)->get();
if($restey->num_rows()>0)
{
    foreach($restey->result() as $rowssspref);
    $currency_preference=$rowssspref->currency_preference;
}else
{
    $currency_preference=0;
}

$rest=$this->db->query("SELECT a.service_amt, a.lead_time, a.po_upload, b.service_name, c.company_name, c.supplier_location FROM bom_pi_services_info a LEFT JOIN services b ON a.hjig_services=b.id LEFT JOIN suppliers c ON c.supplier_id=a.external_suppliers WHERE lead_id = $lead_id");


$rest1=$this->db->select('currency_rate')->from('client_quoted_price')->where('id',$this->uri->segment(6))->get();
if($rest1->num_rows()>0)
{
    foreach($rest1->result() as $rowss);
    $conversion_currency_rate=$rowss->currency_rate;

}else
{   
    echo "ROI CANNOT BE GENERATED"; exit;
}   

$rest3=$this->db->select('id,assembly_image,injection_moulding,supportive_machine,fix_month_cost,selling_price,bop_cost,assembly_cost,packaging_cost,transport_cost,per_month_production,profit')->from('roi_data')->where('lead_id',$this->uri->segment(3))->where('option_id',$this->uri->segment(4))->where('quotation_id',$this->uri->segment(6))->get();
if($rest3->num_rows()>0)
{
    foreach($rest3->result() as $rowsss);
    $assembly_image=$rowsss->assembly_image;
    $injection_moulding=$rowsss->injection_moulding;
    $supportive_machine=$rowsss->supportive_machine;
    $fix_month_cost=floatval($rowsss->fix_month_cost);
    $selling_price=$rowsss->selling_price;
    $bop_cost=$rowsss->bop_cost;
    $assembly_cost=$rowsss->assembly_cost;
    $packaging_cost=$rowsss->packaging_cost;
    $transport_cost=$rowsss->transport_cost;
    $per_month_production=$rowsss->per_month_production;
    $roi_id=$rowsss->id;
    $profit=$rowsss->profit;
}else
{
    $assembly_image='';
    $injection_moulding=0;
    $supportive_machine=0;
    $fix_month_cost=0;
    $selling_price=0;
    $bop_cost=0;
    $assembly_cost=0;
    $packaging_cost=0;
    $transport_cost=0;
    $per_month_production=0;
    $roi_id=0;
    $profit=0;
}

$DI =& get_instance();
$DI->load->model('Salescrm_model','salescrm');
$noofmoulds=$DI->salescrm->getTotalMoulds($lead_id);
$noofcomponents=$DI->salescrm->getTotalComponents($lead_id);
$EI =& get_instance();
$EI->load->model('Sourcing_model','sourcingmodel');
$getMoulds = $EI->sourcingmodel->getMoulds($lead_id);


$sql20 = $this->db->query("SELECT supplier_id FROM suppliers WHERE supplier_id=$supplierid AND supplier_location=101");

if($sql20->num_rows()> 0) {
    /** in this case supplier is of india **/

    if($currency_preference == 1) {
    /** IF SUPPLIER QUOTE IS INR AND QUOTE PREFERENCE IS ALSO INR SO DO NOTHING**/
        $symbol = '₹';
        $in_words = 'INR';
        $multiplication_factor=1;
        $division_factor=1;

        $b_multiplication_factor=1;
        $b_division_factor=1;
    } else {

    /** IF SUPPLIER QUOTE IS INR AND QUOTE PREFERENCE IS USD SO CONVERT INR TO USD**/
        $symbol = '$';
        $in_words = 'USD';
        $multiplication_factor=1;
        $division_factor=$conversion_currency_rate;

        $b_multiplication_factor=1;
        $b_division_factor=$conversion_currency_rate;
    }

    $indiansupplier=1;
} else {

/** in this case supplier is outside india **/

    if($currency_preference == 1) {
    /** IF SUPPLIER QUOTE IS USD AND QUOTE PREFERENCE IS INR SO CONVERT USD TO INR**/

    $symbol = '₹';
    $in_words = 'INR';
    $multiplication_factor=$conversion_currency_rate;
    $division_factor=1;

        $b_multiplication_factor=$conversion_currency_rate;
        $b_division_factor=1;
        
    } else {

        /** IF SUPPLIER QUOTE IS USD AND QUOTE PREFERENCE IS USD SO DO NOTHING**/
        $symbol = '$';
        $in_words = 'USD';
        $multiplication_factor=1;
        $division_factor=1;

        $b_multiplication_factor=1;
        $b_division_factor=1;
    }

    $indiansupplier=0;
}



/** FINAL PRICE FOR THIS QUOTE **/

$sqlfinal=$this->db->query("SELECT final_price,cny_to_usd,inr_to_usd,inr_to_cny FROM client_quoted_price WHERE lead_id = $lead_id AND option_id=$optionid AND supplier_id=$supplierid");
if($sqlfinal->num_rows()>0)
    {
foreach($sqlfinal->result() as $datasfinalp);
$finalprice = $datasfinalp->final_price;
$cny_to_usd=$datasfinalp->cny_to_usd;
$inr_to_usd=$datasfinalp->inr_to_usd;
$inr_to_cny=$datasfinalp->inr_to_cny;
// echo $finalprice;exit;
}else
{
echo "Invalid Data"; exit;
}

$sql32 = $this->db->query("SELECT id FROM sourcing_quotation WHERE cny=1 AND lead_id=$lead_id AND supplier_id=$supplierid");
if($sql32->num_rows() > 0) {

        if($indiansupplier==1 && $currency_preference==0)
        {
            // INR TO USD
        $multiplication_factor=1;
        $division_factor=$inr_to_usd;

        }else if($indiansupplier==1 && $currency_preference==1)
        {
        $multiplication_factor=1;
        $division_factor=1;

        }else if($indiansupplier==0 && $currency_preference==0)
        {
            // CNY TO USD
        $multiplication_factor=$cny_to_usd;
        $division_factor=1;
        }else
        {

        $multiplication_factor=1;
        $division_factor=$inr_to_cny;


        }

    } else {
        $multiplication_factor=1;
        $division_factor=1;
    }





/** BUSINESS CASE PRICE **/

$partprice = array();
$partprice[] = 0;
$mouldoverallweight = array();
$mouldoverallweight[] = 0;
$allmoulds = $this->db->query("SELECT id FROM bom_moulds_detail WHERE lead_id=$lead_id");
if($allmoulds->num_rows()>0)
{
foreach($allmoulds->result() as $dataallmould) {
    $allmouldid = $dataallmould->id;
    $sql12allp = $this->db->query("SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$optionid AND mould_id = $allmouldid AND lead_id=$lead_id AND supplier_id=$supplierid ORDER BY id DESC");
   
    if ($sql12allp->num_rows() > 0) {
        foreach($sql12allp->result() as $datas122345allp);
        $partprice[] = ($datas122345allp->price_in_dollar*$multiplication_factor)/$division_factor;
        $mouldoverallweight[] = $datas122345allp->mould_weight;

    } else {

        $sql12zeroallp = $this->db->query("SELECT mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $allmouldid AND lead_id=$lead_id AND supplier_id=$supplierid ORDER BY id DESC");
        if($sql12zeroallp->num_rows()>0)
        {
        foreach($sql12zeroallp->result() as $datas122345zalpp);

        $partprice[] = ($datas122345zalpp->price_in_dollar*$multiplication_factor)/$division_factor;
        $mouldoverallweight[] = $datas122345zalpp->mould_weight;
        }
    }
}
}


$suppliergivenprice = array_sum($partprice);

 // echo array_sum($mouldoverallweight);exit;

$businesscaseprice = ($finalprice*$b_multiplication_factor)/$b_division_factor;
$suppliergivenweight = array_sum($mouldoverallweight);

if ($negotiation == 1) {
        $sqlneg = $this->db->query("SELECT negotiated_price FROM order_won WHERE lead_id = $lead_id");
       if($sqlneg->num_rows()>0)
       {
        foreach($sqlneg->result() as $rowss);
        
            $businesscaseprice = ($rowss->negotiated_price*$multiplication_factor)/$division_factor;
        }
    }


    if($indiansupplier==1 && $currency_preference==0)
    {
        $businesscaseprice=$finalprice;
        $suppliergivenprice=$suppliergivenprice;

    }else if($indiansupplier==1 && $currency_preference==1)
    {
        // echo $conversion_currency_rate;exit;
        $businesscaseprice=$finalprice*$conversion_currency_rate;
        $suppliergivenprice=$suppliergivenprice;
    }else if($indiansupplier==0 && $currency_preference==0)
    {

        $businesscaseprice=$finalprice;
        $suppliergivenprice=$suppliergivenprice;


    }else
    {

        $businesscaseprice=$finalprice*$conversion_currency_rate;
        $suppliergivenprice=$suppliergivenprice;

    }



    //convert to USD AGAIN
//$suppliergivenprice=$suppliergivenprice*$rmb_usd_rate;


if ($businesscaseprice >= $suppliergivenprice) {

    $diff = $businesscaseprice - $suppliergivenprice;
    $getpercentage = $diff * 100;
    if($suppliergivenprice>0){
    $getextraper = $getpercentage / $suppliergivenprice;
    }else
    {
         $getextraper = 0;
    }

    $percentfactor = $getextraper;


} else {
    echo "BUSINESS CASE PRICE IS LESS THAN SUPPLIER PRICE! HENCE QUOTATION CANNOT BE CREATED";
    //exit;
}
$monthproduction=$CI->BOM_model->getMonthlyProduction($lead_id);

$monthproduction=$per_month_production;

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
$pdf->SetMargins(5, 5, 5, true);
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

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
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
$pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

// Set some content to print

/** quert paet **/

$html = '
<style>
table{
width:100%;
padding:4px;
}

.vert_middle{
    position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
</style>

<table width="100%" style="padding:5px;">
<tr>

<td style="text-align:center;" ><img src="https://www.hongyijig.com/wp-content/uploads/2021/01/HongyiJig-main-logo.png" style="width:200px;"></td>
</tr>
</table>
<br>
<br>
<table>
<tr>
<td style=" text-align:center; font-size:24px;">
<b>PROJECTION FOR RETURN ON INVESTMENT</b> 
</td>
</tr>
</table>

<table>
<tr>
<td style=" text-align:center; font-size:18px;">
FOR THE PROJECT OF 
</td>
</tr>
</table>

<table>
<tr>
<td style=" text-align:center; font-size:20px;">
<b>'.strtoupper($project_name).' MOULDS</b> 
</td>
</tr>
</table>
<br><br>
<table>
<tr>
<td style="line-height:25px;">
Dear '.ucwords(strtolower($customer_name)).' ji,
<br>
<br>
<b>'.$company_name.'</b><br>
'.$customer_address.'
<br><br>
<span style="text-align:justify;">I am pleased to offer you a potential investment opportunity with our ROI plan for design development
and injection mould manufacturing for '.$project_name.'. Our team has significant
experience in these areas and is committed to delivering high-quality results that meet your
specifications and exceed your expectations.
</span>
</td>
</tr>
</table>
<br><br>
<table>
<tr>
<td style="text-align:center; border-top:1px solid black; border-bottom:1px solid black;">
<b>PROJECT DETAILS</b>
</td>
</tr>
</table><BR/><BR/>
<table>
<tr>
<td>
<b>DESIGN & DEVELOPMENT</b><br>R&D Cost | Excluded Pre-Tooling Activities
</td>
</tr>
</table>
<br><br>
<table style="padding:0px;">

<tr>
<td width="100%">
<table border="1" ruled="all" >
<tr>
<td style="text-align:center;" width="20%"><b>S.NO</b></td>
<td style="text-align:center;" width="40%"><b>DESCRIPTION</b></td>
<td style="text-align:center;" width="40%"><b>AMOUNT</b></td>

</tr>';

$service_amt_arr=array();
$service_amt_arr[]=0;
if($rest->num_rows()>0)
{
    $i=1;
    foreach($rest->result() as $rowss)
    {

        $supplier_location = $rowss->supplier_location;
        if($supplier_location == 101) {
        $service_amt = $rowss->service_amt/$inr_to_usd;
        }else
        {
        $service_amt=$rowss->service_amt*$cny_to_usd;
        }

        /** GET BUSINESS CASE **/



$rest2=$this->db->query("SELECT low_price, low_percent, high_price, high_percent FROM business_case_client WHERE $service_amt BETWEEN low_price AND high_price");
if($rest2->num_rows()>0)
{
    foreach($rest2->result() as $bcase);

     if($bcase->low_price == $service_amt) {
                $client_quotation = $service_amt + (($service_amt * $bcase->low_percent)/100);
            } else if($bcase->high_price == $service_amt) {
                $client_quotation = $service_amt + (($service_amt *  $bcase->high_percent)/100);
            } else if(($bcase->low_price < $service_amt) && ( $bcase->high_price > $service_amt)) {
                $total_percentage =  $bcase->low_percent +  $bcase->high_percent;
                $total_c_percentage=$total_percentage/2;
                
                $client_quotation = $service_amt + (($service_amt * $total_c_percentage)/100);
            } else if($bcase->low_price > $service_amt)
            {
                echo $client_quotation = 'NO RANGE FOUND FOR BUSINESS CASE';exit;
                
            }else
                {
                $client_quotation = '';
            }
        } else {
            echo $client_quotation = 'NO RANGE FOUND FOR BUSINESS CASE';exit;
        }

        
        
        $client_quotation = round($client_quotation)*$inr_to_usd;
  

$html.='<tr>
<td style="text-align:center;">'.$i.'</td>
<td>'.$rowss->service_name.'</td>
<td style="text-align:center">₹'.round($client_quotation).'</td>';
// if($i==1)
// {
// $html.='<td rowspan="'.$rest->num_rows().'" style="text-align:center"><img src="'.$img.'"></td>';
// }
$html.='</tr>';

$service_amt_arr[]=round($client_quotation);
    $i++;
    } 
}else
{
    $html.='<tr style="text-align:center"><td colspan="3">No Design & Development Services</td></tr>';
}
 
if($rest->num_rows()>0)
{
$html.='<tr>
<td></td>

<td><b>Total</b></td>
<td style="text-align:center"><b>₹'.array_sum($service_amt_arr).'</b></td>

</tr>';
}
$html.='</table>
</td>';


        if($assembly_image<>'')
{
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/roi_image/'.$assembly_image))
    {
        $img=$_SERVER['DOCUMENT_ROOT'].'/roi_image/'.$assembly_image;
    }else
    {
         $img=$_SERVER['DOCUMENT_ROOT'].'/roi_image/notfound.jpg';
    }
}else
{
     $img=$_SERVER['DOCUMENT_ROOT'].'/roi_image/notfound.jpg';
}

//echo $img; exit;
$html.='
</tr>
</table><br/><br/><br/>
';     

$html.='<table>
<tr>
<td><b>PRODUCT IMAGE</b></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td><div style="padding-top:20px;"><img src="'.$img.'" style="width:200px;"></div></td>
<td></td>
</tr>
</table>';


//echo $html;exit;
$html.='<br pagebreak="true" />
<table>
<tr>
<td>
<b>TOOLING & PRODUCTION</b><br>Total Number of Components - '.$noofcomponents.' | Total Number of Moulds - '.$noofmoulds.'</td>
</tr>
</table>
<br><br>
<table border="1" ruled="all" >
<tr>
<td style="text-align:center;" width="10%"><b>S.NO</b></td>
<td style="text-align:center;" width="30%"><b>PART DESCRIPTION</b></td>
<td style="text-align:center;" width="20%"><b>CAVITATION</b></td>
<td style="text-align:center;" width="20%"><b>MOULD COST</b></td>
<td style="text-align:center;" width="20%"><b>PART COST</b></td>
</tr>';

if(!empty($getMoulds)) { 
$mld=1;
$mouldcheck=array();
$mouldcost=array();
$mouldcost[]=0;
$partcost=array();
$partcost[]=0;
foreach($getMoulds as $moulds) {

$mould_wise_part_detail=$CI->BOM_model->getmouldwisepartdetailsforedit($moulds->id,$lead_id);
//echo "<pre>"; print_r($mould_wise_part_detail); exit;
$getPartDetails = $EI->sourcingmodel->getPartDetails($lead_id,$moulds->id);
$partscount= (array) $getPartDetails;
$partscount=count($partscount);
$t=1;
foreach($getPartDetails as $details) {

$getPartAddDetails = $CI->sourcingmodel->getPartAddDetails($details->id);    
foreach($getPartAddDetails as $addDetails);

$part_weight=$addDetails->benchmarkweight;

$html.='<tr>
<td style="text-align:center;">'.$t.'</td>';

if ($this->uri->segment(4) > 0) {
$sql12 = $this->db->query("SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=$this->uri->segment(4) AND mould_id = $moulds->id AND lead_id=$lead_id AND supplier_id=$supplierid ORDER BY id DESC");
if($sql12->num_rows()>0)
{
    foreach($sql12->result() as $datas122345);
    $mouldweight = $datas122345->mould_weight;
    $priceinusd =  ($datas122345->price_in_dollar*$multiplication_factor)/$division_factor;
    // echo $priceinusd.'<br>';
    $revamp = $percentfactor / 100;
    // echo $revamp;exit;
    $revamp1 = $priceinusd * $revamp;
    $priceinusd = $priceinusd + $revamp1;
}else
{
    echo "invalid"; exit;
}

}else
{


  $sql12zero =$this->db->query("SELECT mould_size,x, y, z, mould_weight,price_in_dollar FROM sourcing_quotation WHERE option_id=0 AND mould_id = $moulds->id AND lead_id=$lead_id AND supplier_id=$supplierid ORDER BY id DESC");
                
                if($sql12zero->num_rows()>0)
                {
                foreach($sql12zero->result() as $datas122345z);
                $mouldweight = $datas122345z->mould_weight;
                $priceinusd = ($datas122345z->price_in_dollar*$multiplication_factor)/$division_factor;
                // echo $percentfactor;exit;  
                $revamp = $percentfactor / 100;
                $revamp1 = $priceinusd * $revamp;
                $priceinusd = $priceinusd + $revamp1;

            }else
            {
                echo "INVALID DATA"; exit;
            }

}


$sql21 = $this->db->query("SELECT id FROM client_quoted_price WHERE lead_id=$lead_id AND option_id=$optionid AND supplier_id=$supplierid AND override_price=1");
            if($sql21->num_rows() > 0) {
               foreach($sql21->result() as $data21);
                $quotation_idss = $data21->id;
                $sql22 =$this->db->query("SELECT mould_price FROM new_mould_prices WHERE quotation_id=$quotation_idss AND lead_id=$lead_id AND option_id=$optionid AND supplier_id=$supplierid AND mould_id=$moulds->id");
                if($sql22->num_rows()>0)
                {
                foreach($sql22->result() as $sql21);
                $priceinusd = $sql21->mould_price;
                }else
                {
                    $priceinusd=0;
                }
            } else {
                $priceinusd = $priceinusd;
            }

            if($currency_preference==0)
            {
                /** CONVERT TO INDIAN RUPEES **/

                //echo $inr_to_usd; exit;
                //echo $inr_to_usd; exit;
                $priceinusd=round($priceinusd*$inr_to_usd);
            }else
            {
                $priceinusd=round($priceinusd);
            }


/** GET PART COST **/
$Roi_master=$CI->BOM_model->getroimaster();
$roimaster=explode('|',$Roi_master);
$machine_cost=1.5;
$profit_overhed=$profit;
$rm_cost_per_kg=$roimaster[2];
$cad_weight=$part_weight;
$cavity=$details->cavity;
if($addDetails->runner_type==1)
{
/** HOT RUNNER **/
$tips=$addDetails->tips;
if($tips==$cavity)
{
    $per=0;
    $atrate=0;
}else if($tips<$cavity)
{
    $per=1.5/100;
    $atrate=1.5;
}else
{
    $per=0;
    $atrate=0;
}

}else
{
/** COLD RUNNER **/
$per=3/100;
$atrate=3;
}

$increment_weight=$cad_weight*$cavity*$per;
$final_shot_weight=round(($cad_weight*$cavity)+$increment_weight,2);
$rm_cost_per_kg=$CI->BOM_model->getPartMaterialRMCOST($mould_wise_part_detail['part_material'],$roi_id);
if($rm_cost_per_kg<>0)
{
    $convert_rm_to_gram=$rm_cost_per_kg/1000;
}else
{
    $convert_rm_to_gram=0;
}

$rmcost=round($convert_rm_to_gram*$final_shot_weight,2);
//echo $rmcost; exit;


$machine_tonnage=$mould_wise_part_detail['machineselection'];
preg_match_all('!\d+!', $machine_tonnage, $tonnage);
foreach($tonnage as $tonnage1)
if(count($tonnage1)>0)
{

    $ton=$tonnage1[0];
}else
{
    $ton=0;
}

$machine_cost_per_hour=$ton*$machine_cost;
$cycle_time=$mould_wise_part_detail['cycletime'];
if($machine_cost_per_hour>0)
{
$process_cost=round($machine_cost_per_hour/(3600/$cycle_time)/0.9,2);
}else
{
    $process_cost=0;
}

// PART MATERIAL TYPE 
$part_percentage=$CI->BOM_model->part_material_type($mould_wise_part_detail['part_material']);
if($part_percentage>0)
{
    $p_per=3/100;
    $p_rate="3%";
}else
{
    $p_per=2/100;
    $prate="2%";
}

$rejection=round(($rmcost+$process_cost)*$p_per,2);

$profitoverhead=round(($rmcost+$process_cost)*($profit_overhed/100),2);

$totalcost=round($rmcost+$process_cost+$rejection+$profitoverhead,2);
$totalcost_per_part=round($totalcost/$cavity,2);
/** END **/
$part_material_name=$CI->BOM_model->getPartMaterialBYID($mould_wise_part_detail['part_material']);
/** END **/

$html.='<td style="text-align:center;">'.$details->name.' | '.$details->partname.'</td>
<td style="text-align:center;">'.$details->cavity.'</td>';
if($partscount>1)
{
    if($t==1)
    {
       // echo "hi"; exit;
$html.='<td style="text-align:center;" rowspan="'.$partscount.'">₹'.$priceinusd.'</td>';
$mouldcost[]=$priceinusd;
    }
}else
{
$html.='<td style="text-align:center;" class="vert_middle">₹'.$priceinusd.'</td>';
$mouldcost[]=$priceinusd;
}

$html.='<td style="text-align:center;">₹'.number_format((float)round($totalcost_per_part,2), 2, '.', '').'</td>
</tr>';
$partcost[]=round($totalcost_per_part,2);
$t++;
}

$mld++;
}
}

//echo $html; exit;

$html.='<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
<td style="text-align:center;"><b>Total</b></td>
<td style="text-align:center; "><div style="position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);"><b>₹'.array_sum($mouldcost).'</b></div></td>
<td style="text-align:center;"><b>₹'.array_sum($partcost).'</b></td>
</tr>


</table><br/><br/>
<table>
<tr>
<td width="48%">
<b>TOTAL INVESTMENT</b><br>R&D Cost | Excluded Pre-Tooling Activities</td>
<td width="4%"></td>
<td width="48%">
<b>REVENUE PLAN
</b><br>R&D Cost | Excluded Pre-Tooling Activities</td>
</tr>
</table>

<table style="padding:0px;">
<tr>
<td width="48%">
<table border="1" ruled="all" >
<tr>
<td style="text-align:center;" width="12%"><b>Sr.</b></td>
<td style="text-align:center;" width="58%"><b>DESCRIPTION</b></td>
<td style="text-align:center;" width="30%"><b>AMOUNT</b></td>
</tr>';

$total_investment=floatval(array_sum($service_amt_arr))+floatval(array_sum($mouldcost))+floatval($supportive_machine)+floatval($injection_moulding);
$html.='<tr>
<td style="text-align:center;">1</td>
<td>Design & Development</td>
<td style="text-align:center;"><b>₹'.floatval(array_sum($service_amt_arr)).'</b></td>
</tr>
<tr>
<td style="text-align:center;">2</td>
<td >Moulds</td>
<td style="text-align:center;"><b>₹'.floatval(array_sum($mouldcost)).'</b></td>
</tr>
<tr>
<td style="text-align:center;">3</td>
<td>Injection Moulding Machines</td>
<td style="text-align:center;"><b>₹'.floatval($injection_moulding).'</b></td>
</tr>
<tr>
<td style="text-align:center;">4</td>
<td>Supportive Machine</td>
<td style="text-align:center;"><b>₹'.floatval($supportive_machine).'</b></td>
</tr>

<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
</tr>
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"><b></b></td>
<td style="text-align:center;"><b></b></td>
</tr>
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"><b></b></td>
<td style="text-align:center;"><b></b></td>
</tr>
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"><b></b></td>
<td style="text-align:center;"><b></b></td>
</tr>
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"><b></b></td>
<td style="text-align:center;"><b></b></td>
</tr>
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"><b></b></td>
<td style="text-align:center;"><b></b></td>
</tr>
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"><b>Total</b></td>
<td style="text-align:center;"><b>₹'.$total_investment.'</b></td>
</tr>



</table>
</td>
<td width="4%"></td>
<td style="border:1px solid black;" width="48%">
<table border="1" ruled="all" >
<tr>

<td style="text-align:center;" width="70%"><b>DESCRIPTION</b></td>
<td style="text-align:center;" width="30%"><b>AMOUNT</b></td>
</tr>
<tr>

<td>Moulded Part Cost </td>
<td style="text-align:center;">₹'.floatval(array_sum($partcost)).'</td>
</tr>
<tr>

<td>BOP (Except Moulded Parts) </td>
<td style="text-align:center;">₹'.floatval($bop_cost).'</td>
</tr>
<tr>

<td>Assy and Conversion Cost</td>
<td style="text-align:center;">₹'.floatval($assembly_cost).'</td>
</tr>

<tr>

<td>Packaging Cost</td>
<td style="text-align:center;">₹'.floatval($packaging_cost).'</td>
</tr>';

$fpc=floatval(array_sum($partcost))+$bop_cost+$assembly_cost+$packaging_cost;

$html.='<tr>

<td><strong>Total Finished Product Cost</strong></td>
<td style="text-align:center;"><strong>₹'.floatval($fpc).'</strong></td>
</tr>

<tr>

<td>Transportation Cost</td>
<td style="text-align:center;">₹'.floatval($transport_cost).'</td>
</tr>';

$lfp=$fpc+$transport_cost;
$html.='<tr>

<td><strong>Landed Final Product Cost</strong></td>
<td style="text-align:center;"><strong>₹'.floatval($lfp).'</strong></td>
</tr>
<tr>

<td>Selling Cost</td>
<td style="text-align:center;">₹'.number_format((float)($selling_price)).'</td>
</tr>';

$gp=$selling_price-$lfp;
$html.='<tr>

<td>Per Unit GP</td>
<td style="text-align:center;">'.$gp.'</td>
</tr>
<tr>

<td>Month Production</td>
<td style="text-align:center;">'.$monthproduction.'</td>
</tr>

<tr>

<td>Fix Cost /Month</td>
<td style="text-align:center;">'.$fix_month_cost.'</td>
</tr>


</table>
</td>
</tr>
</table>
<br><br>
<br><br>
<table>
<tr>
<td width="10%"></td>
<td width="80%">
<b>RETURN ON INVESTMENT PLAN</b><br>Including product development, Die moulds | Supportive machines etc</td>
</tr>
</table>
<br>
<table style="padding:0px;">
<tr>
<td width="10%"></td>
<td width="80%">
<table border="1" ruled="all" >
<tr>
<td style="text-align:center;" width="10%"><b>SR.</b></td>
<td style="text-align:center;" width="50%"><b>DESCRIPTION</b></td>
<td style="text-align:center;" width="40%"><b>AMOUNT</b></td>
</tr>
<tr>
<td style="text-align:center;">1</td>
<td>Total Investment</td>
<td style="text-align:center;"><b>₹'.$total_investment.'</b></td>
</tr>';


$revenue=floatval(round($selling_price*12*$per_month_production));
$html.='<tr>
<td style="text-align:center;">2</td>
<td>Reveneue / Year</td>
<td style="text-align:center;"><b>₹'.$revenue.'</b></td>
</tr>';



$gp_year=$gp*$per_month_production*12;
$html.='<tr>
<td style="text-align:center;">3</td>
<td>Gross Profit / Year</td>
<td style="text-align:center;"><b>₹'.$gp_year.'</b></td>
</tr>';

$accur=$fix_month_cost*12;
$html.='<tr>
<td style="text-align:center;">4</td>
<td>Fix Cost / Year </td>
<td style="text-align:center;"><b>₹'.$accur.'</b></td>
</tr>';

$netprofit=round($gp_year-$accur);
$html.='
<tr>
<td style="text-align:center;">5</td>
<td>Net Profit / Year</td>
<td style="text-align:center;"><b>₹'.$netprofit.'</b></td>
</tr>';

$roiper=($netprofit/$total_investment)*100;
$html.='<tr>
<td></td>
<td><b>ROI (In the First Year)</b></td>
<td style="text-align:center;"><b>'.round(floatval($roiper),2).'%</b></td>
</tr>
</table>
</td>
<td width="5%"></td>
<td  width="35%">

</td>

</tr>
</table><br pagebreak="true" />';

$html.='<p></p><p></p><p></p><p></p><p>Based on our projected ROI plan, we estimate that this project can generate a return of <b>'.round(floatval($roiper),2).'% </b> in the first
year. We are committed to transparency and open communication throughout the project, and we will
work closely with you to ensure that you are fully informed and involved in the process.</p>';

 $title = "";
 $name = "";

$rq=$this->db->query("SELECT assign_to FROM lead_meeting_schedule WHERE lead_id = $lead_id");
if($rq->num_rows()>0){
foreach($rq->result() as $data19);
$member_id = $data19->assign_to;
if($member_id == 0 || $member_id == '') {
$rq1=$this->db->query("SELECT member_id FROM lead_assigned_to_team_member WHERE lead_id = $lead_id");
foreach($rq1->result() as $data17);
$member_id = $data17->member_id;
$title = 'Sales Manager';
} else {
    $member_id = $member_id;
    $title = 'Business Development Manager';
}

$rq1 = $this->db->query("SELECT first_name, last_name FROM  system_users WHERE user_id = $member_id");
foreach($rq1->result() as $sql18);
$name = $sql18->first_name . ' ' . $sql18->last_name;
}

$html.='<p><span style="text-align:justify;">Thank you for considering this investment opportunity. Please do not hesitate to contact us if you have any
questions or would like to discuss this opportunity further.</span>
<br><br>
Sincerely,
<br><br>
'.$name.'<br/>'.$title.'
</p>
';


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/popdf';

$filename="ROI_".str_replace(' ','_',$project_name)."_PROJECT.pdf";
$fileNL = $filename; //Linux

$pdf->Output($fileNL, 'I');



//============================================================+
// END OF FILE
//============================================================+
