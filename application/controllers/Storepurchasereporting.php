<?php 
class Storepurchasereporting extends CI_Controller{
      function __construct() { 
 parent::__construct();
$this->load->model('Fms_model','fmsmodel');
    $this->load->model('Fms_mismodel','fmsmismodel');
    $this->load->model('Delegation_model');
    $this->load->model('Store_model','storemodel');
    $this->load->model('Master_model','master');

 } 

 function filterdatewisereporting(){
  $this->load->view('store/datewisefilterreporting');
 }

      function redirecttostorepurchasereport(){
      $startdate = $this->input->post('startdate');
      $date = date('Y-m-d',strtotime($startdate));

      redirect(page_url.'Storepurchasereporting/dailystorepurchasereports/'.$date);
      }

      function redirecttodailyexpensereport(){
      $startdate = $this->input->post('expensedate');
      $date = date('Y-m-d',strtotime($startdate));

      redirect(page_url.'Pdf_reports/dailyexpensereport/'.$date);
      }

    function redirecttodailyreportingrelatedtoorders(){
    $startdate = $this->input->post('pickadate');
    $date = date('Y-m-d',strtotime($startdate));

    redirect(page_url.'Pdf_reports/daily_reports_view/'.$date);
    }

    function redirecttodailyreportinguserwise(){
    $startdate = $this->input->post('pickadate');
    $date = date('Y-m-d',strtotime($startdate));

    redirect(page_url.'Pdf_reports/daily_reports_user_wise/'.$date);
    }
 function getunit($unitid)
{

$query = $this->db->select('b.shortname')->from('units b')->where('b.id',$unitid)->get();
if($query->num_rows()>0)
{
  
  foreach($query->result() as $query1);
  
    $unit=strtoupper($query1->shortname);
  return $unit;
}else{
  
  $unit='';
  return $unit;
}
  
  
}

function getvendorname($vendorname)
{
  $Restye=$this->db->select('name')->from('vendors')->where('id',$vendorname)->get();
  
  if($Restye->num_rows()>0)
  {
    foreach($Restye->result() as $Restye1);
    
    return $Restye1->name;
    
  }else{
    
    return "";
  }
  
}

function getmachineitemname($itemid)
{
  $iname='';
  $resty=$this->db->select('part')->from('machine_parts_with_picture')->where('id',$itemid)->get();
  if($resty->num_rows()>0)
  {
    foreach($resty->result() as $resty1);
    
    $iname=$resty1->part;
  }
  
  return $iname;
  
}

function getmachineotherdetails($itemid)
{
  
  $iname=array();
  $resty=$this->db->select('a.size_in_mm,a.material,a.conversion_unit,a.conversion_weight,a.fincode,a.specification,b.rack_location,a.part,a.unit, a.colour, a.thikness')->from('machine_parts_with_picture a')->join('store_rack_location b','a.location_id=b.id','left')->where('a.id',$itemid)->get();
  if($resty->num_rows()>0)
  {
    foreach($resty->result() as $resty1);
    $iname['name']=$resty1->part;
    $iname['fincode']=$resty1->fincode;
    $iname['specialization']=$resty1->specification;
    $iname['rack_location']=$resty1->rack_location;
    $iname['unit']=$resty1->unit;
    $iname['conunit']=$resty1->conversion_unit;
    $iname['conweight']=$resty1->conversion_weight;
    $iname['size']=$resty1->size_in_mm;
    $iname['material']=$resty1->material;
    $iname['conweight']=$resty1->conversion_weight;
    $iname['colour']=$resty1->colour;
    $iname['thikness']=$resty1->thikness;
  }
  
  return $iname;
  
}
function getusername($userid)
{

$query=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->get();
if($query->num_rows()>0)
{

  foreach($query->result() as $que);

  return $que->first_name." ".$que->last_name;
}
else
{
  return '';

}


}
function getlocation_details($location)
  {
    $loc='';
    $restey=$this->db->select('rack_location')->from('store_rack_location')->where('id',$location)->get();
    if($restey->num_rows()>0)
    {
      foreach($restey->result() as $row);

      $loc=$row->rack_location;
    }

    return $loc;

  }


function getpodetails($pono)
{
$ponoo=array();
$resteyu=$this->db->select('source,sourceid,unit,potype,prno,pono_forshow, vendor')->from('purchase_order')->where('pono',$pono)->get();
if($resteyu->num_rows()>0)
{
foreach($resteyu->result() as $resteyu1);

$ponoo['source']=$resteyu1->source;
$ponoo['sourceid']=$resteyu1->sourceid;
$ponoo['unit']=$resteyu1->unit;
$ponoo['potype']=$resteyu1->potype;
$ponoo['prno']=$resteyu1->prno;
$ponoo['pono_forshow']=$resteyu1->pono_forshow;
$ponoo['vendor']=$resteyu1->vendor;

}


return $ponoo;
}


function getjobcardid($record)
{

$resteye=$this->db->select('jobcardno')->from('mrn')->where('id',$record)->get();
if($resteye->num_rows()>0)
{

foreach($resteye->result() as $resteye1);

return $resteye1->jobcardno;

}else
{

return 0;

}

}

function getsourceid($pr)
{
$Restweyue=$this->db->select('sourceid')->from('purchase_request')->where('prno',$pr)->get();
if($Restweyue->num_rows()>0)
{
foreach($Restweyue->result() as $Restweyue1);

return $Restweyue1->sourceid;
}else
{
return '';

}


}


function getjobcardno($jobcard)
{
    $restey=$this->db->select('job_card_no')->from('order_instruments')->where('id',$jobcard)->get();
    if($restey->num_rows()>0)
    {
        foreach($restey->result() as $restey12);
        
        return $restey12->job_card_no;
        
    }else
    {
        return '';
    }
    

}

function getgeneralitemname($itemid)
{
  $iname='';
  $resty=$this->db->select('item_name')->from('house_keeping_items')->where('id',$itemid)->get();
  if($resty->num_rows()>0)
  {
    foreach($resty->result() as $resty1);
    
    $iname=$resty1->item_name;
  }
  
  return $iname;
  
}


public function dailystorepurchasereports()
{

$html='<style>li span{ font-weight:bold;}</style>';

$this->load->library('Pdf');
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("DAILY STORE PURCHASE REPORTING");
$pdf->SetSubject('DAILY STORE PURCHASE REPORTING');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetPrintHeader(false);
$pdf->setPrintFooter(false);


$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(5, 2, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('pdfahelvetica', '', 12, '', true);
$pdf->AddPage();

$html='';
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">
<img src="http://www.supremeglow.in/wp-content/uploads/2022/12/Supreme-glow-printing-black-logo-1400x488.png" style="width:150px">
</td>
<td></td>
</tr>
</table>';

$todays = $this->uri->segment(3);
$dateformat = date('d-m-Y',strtotime($todays));
$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="6" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">Indent Report '.$dateformat.'</th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Indent Type</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px; "><b>Indend No</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Item Description</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Remarks</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Created By</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Created On</b></th>

</tr><tbody>';
$m=1;
 $itemdescqty = "";
    $startdate = $todays." 00:00:00";
    $enddate = $todays." 23:59:59";
$rest=$this->db->select('a.id as intendid,a.addedOn,a.unit,a.prefix,a.indendno,e.first_name,e.last_name,a.itemid, a.indent_type,a.remarks,a.approvalstatus,a.approvedOn,a.approvedBy,a.cancelby,a.cancelledOn,a.cancel_status')->where('a.approvalstatus !=','0')->from('intend_request a')->join('system_users e','e.user_id=a.addedBy')->where('a.addedOn BETWEEN "'.$startdate. '" and "'.$enddate.'"')->order_by('a.addedOn','DESC')->group_by('a.indendno')->get();
if($rest->num_rows()>0){
   foreach($rest->result() as $restyui1){

  $unitname=$this->getunit($restyui1->unit);
        if($restyui1->indent_type=='1'){
          $indenttype = "Machine Related Items";
        $rest123=$this->db->select('b.part as item_name,a.qty,a.unit,b.specification,b.size_in_mm,b.colour,b.thikness')->from('intend_request a')->join('machine_parts_with_picture b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();  
        }else{
          $indenttype = "General Items";
        $rest123=$this->db->select('b.item_name,a.qty,a.unit')->from('intend_request a')->join('house_keeping_items b','a.itemid=b.id')->where('a.indendno',$restyui1->indendno)->get();  
        }
        
        
        if($rest123->num_rows()>0)
        {
         foreach($rest123->result() as $rest1231)
          {
          
          $itmdesc = strtoupper($rest1231->item_name)."-".$rest1231->size_in_mm."-".$rest1231->thikness."-".$rest1231->colour;
            $qtyunt = strtoupper($rest1231->qty)." ".$unitname;
              $itemdescqty = $itmdesc."<br/>".$qtyunt;
               
          }
          
    
      }
    $indtno =   $restyui1->prefix.'-'.$restyui1->indendno;
$createdby = ucfirst($restyui1->first_name." ".$restyui1->last_name);
  $html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$m.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$indenttype.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$indtno.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$itemdescqty.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$restyui1->remarks.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$createdby.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.date('d-M-Y g:i A',strtotime($restyui1->addedOn)).'</td>
      
  </tr>';
$m++;
}
}

$html.='</tbody></table><br><br><br>';


$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">

</td>
<td></td>
</tr>
</table>';

$todays = $this->uri->segment(3);
$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="10" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">PO Approved  '.$dateformat.'</th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>PO No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px; "><b>Vendor Name</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Item Name</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Qty</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Price Per Unit</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Last Time Purchase<br/> Rate</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Diff</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Created By</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Created On</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Approved On</b></th>

</tr><tbody>';
$m=1;
    $startdate = $todays." 00:00:00";
    $enddate = $todays." 23:59:59";
 $this->db->select('a.id, a.approvedOn,a.jobcard,a.prno,a.source,a.sourceid,a.potype,a.addedOn,a.pono,e.first_name,e.last_name,a.itemid,e.department_id, v.name, v.address,a.approved,a.pono_forshow')->from('purchase_order a')->join('system_users e','e.user_id=a.addedBy','left')->join('vendors v','a.vendor=v.id','left')->where('a.approved !=','0');

    $this->db->where('a.approvedOn>=',$startdate); 
    $this->db->where('a.approvedOn<=',$enddate);
      
      $rest=$this->db->order_by('a.approvedOn','DESC')->get();
      if($rest->num_rows()>0)
      {
      $i=1;
      foreach($rest->result() as $restyui1)
      {
        $bc='';
        
        
          $jobcard='';

          if($restyui1->source==1)
          {
          $job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
          if($job->num_rows()>0)
          {
          foreach($job->result() as $job1);
          $jobcard='Jobcard-'.$job1->job_card_no;
          }else{
          $jobcard='';
          }
          $polink="po";
          $mrnlink="mrn";
          }else if($restyui1->source==2)
          {
          /** Intend **/
          $jobcard='INDENT- IND'.$restyui1->sourceid;
          $polink="generalpo";
          $mrnlink="generalmrn";
          }else if($restyui1->source==3)
          {
          /** Intend **/
          $jobcard='AUTO PR';
          $polink="po";
          $mrnlink="mrn";
          }         


        
        if($restyui1->potype=='0')
        {
          $polink="po";
          $mrnlink="mrn";
        }else
        {
          $polink="generalpo";
          $mrnlink="generalmrn";
        }


if($restyui1->approved=='1')
{
    $app="Approved";
    $rmk='';
}else
{
    $app="Rejected";
    
    $resteyrttt=$this->db->select('remarks')->from('porejectremarks')->where('pono',$restyui1->pono)->get();
    if($resteyrttt->num_rows()>0)
    {
        foreach($resteyrttt->result() as $resteyrttt1);
    $rmk=$resteyrttt1->remarks;
    }else
    {
        $rmk='';
    }
}
$htm="";
      $indenttype = "Machine Related Items";
        $rest123=$this->db->select('b.id,a.price,a.itemid, b.part as item_name,a.qty,a.unit,c.shortname')->from('purchase_order a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','a.unit=c.id','left')->where('a.pono',$restyui1->pono)->where('b.id',$restyui1->itemid)->get();
        if($rest123->num_rows()>0)
        {
          
          foreach($rest123->result() as $rest1231)
          $price = $rest1231->price;
          $oldprice="";
          $query = $this->db->select('price')->from('purchase_order')->where('itemid',$rest1231->itemid)->where('approved','1')->limit('1')->order_by('id','desc')->get();
          if($query->num_rows()>0){
            foreach($query->result() as $oldata);
            $oldprice = round($oldata->price,2);
            $diff = abs($price-$oldprice);
          }else{
              $oldprice="New Item";
              $diff="0";
          }
          $itemname = strtoupper($rest1231->item_name);
          $qty = $rest1231->qty;
          $price = $rest1231->price;
          $itemid = $rest1231->id;
          $shortname= $rest1231->shortname;
          
          $amendment="";
          $q = $this->db->select('qty')->from('po_amendend_history')->where('pono',$restyui1->pono)->where('item_id',$itemid)->order_by('id','desc')->limit('1')->get();
                    if($q->num_rows()>0){
                        foreach($q->result() as $rowss);
                        $amendment =  "(AMENDED ".floatval($rowss->qty)." ".$shortname." to ".floatval($qty)." ".$shortname.")";
                    }
          
      }
$createdby = ucfirst($restyui1->first_name." ".$restyui1->last_name);

      $pon = $restyui1->pono_forshow." ".$amendment;
      $backtrack = "<a href='javascript:;' class='btn btn-warning btn-xs' onclick='backtrack_po(".$restyui1->id.")'>Backtrack PO</a>";
  $html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$m.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$pon.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$restyui1->name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$itemname.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$qty.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.round($price,2).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$oldprice.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.round($diff,2).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$createdby.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.date('d-M-Y g:i A',strtotime($restyui1->addedOn)).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.date('d-M-Y g:i:A',strtotime($restyui1->approvedOn)).'</td>
      
  </tr>';
$m++;
}
}

$html.='</tbody></table><br><br><br>';


$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">

</td>
<td></td>
</tr>
</table>';

$todays = $this->uri->segment(3);

$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="9" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">MRN '.$dateformat.'</th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>PO No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px; "><b>Item/Size/Thickness/Colour</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Vendor Name</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Req Qty</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Rec Qty</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Purchase Rate</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>GRN No</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Bill No<br>Date<br>File</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Gate Entry By</b></th>


</tr><tbody>';
$m=1;
    $startdate = $todays." 00:00:00";
    $enddate = $todays." 23:59:59";

      $date = $todays;
      //$date = strtotime($date);
      //$date = date('Y-m-d',strtotime("-30 day", $date));
      $scheduler_data = array();

      $start_date = $this->uri->segment(3);
      $end_date = $this->uri->segment(4);
      $vendor = $this->uri->segment(5);
      $item_id = $this->uri->segment(6);

        $this->db->select('a.*,a.id as mrn_id, b.*,a.gateentryno as mrngatentry')
        ->from('mrn a')
        ->join('purchase_order b','a.poid=b.id')
        ->where('a.mrndone','1');
        $this->db->where('a.bill_date',$date);
        $rest = $this->db->order_by('a.bill_date','DESC')->group_by('a.id')->get();
      if($rest->num_rows()>0)
      {
      $i=1;
      foreach($rest->result() as $restyui1)
      {
        $check_store_receipt = $this->storemodel->chk_store_receipt($restyui1->mrn_id);
        $vendorname=$this->getvendorname($restyui1->vendor);
        if($restyui1->potype==0)
        {
          
          $type="Machine Item";
          $itemname=$this->getmachineitemname($restyui1->itemid);
          $otherdetails=$this->getmachineotherdetails($restyui1->itemid);
          if(count($otherdetails)>0)
          {

          $fincode=$otherdetails['fincode'];
          $specification=$otherdetails['specialization'];
          $size=$otherdetails['size'];
          $thickness=$otherdetails['thikness'];
          $colour=$otherdetails['colour'];
          $conunit=$otherdetails['conunit'];
          }else{
          $fincode='';
          $specification='';
          $conunit='';
          $size='';
          $thickness='';
          $colour='';
          }
      
          
        }else if($restyui1->potype==1)
        {
          $type="General Item";
          $itemname=$this->getgeneralitemname($restyui1->itemid);
          $fincode='';
          $specification='';
          $conunit='';
          $size='';
          $thickness='';
          $colour='';
          
        }
        


      $macid=0;
            /** **/
    
              

      $restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
      if($restyu->num_rows()>0)
      {
      foreach($restyu->result() as $restyu1);
      $unival=$restyu1->shortname;
      }else{
      $unival='';             
      }


      if($conunit>0)
      {

  $restyuss=$this->db->select('shortname')->from('units')->where('id',$conunit)->get();
      if($restyuss->num_rows()>0)
      {
      foreach($restyuss->result() as $restyu1dd);
      $unival12345=$restyu1dd->shortname;
      }else{
      $unival12345='';              
      }
    }else
    {
      $unival12345='';
    }


      $gby=$this->getusername($restyui1->gateentryBy);

      if($restyui1->qcstatus=='1')
      {
      $qcstatus="Done";
      $bac="green";
      }else
      {
      $qcstatus="Not Done";
      $bac="red";
      }
      
      
      if($restyui1->account_accepted == 1) {
        $accept = 'YES';
        $accname=$this->getusername($restyui1->account_accepted_by);
        $accon=date('d-M-Y',strtotime($restyui1->account_accepted_on));
      } else {
        $accept = 'NO';
        $accname='';
        $accon='';
      }
    

      if($restyui1->qcstatus == 0) {
        $current_status = '<strong>Pending QC</strong>';
        $location = '';
        $actual_date = '';
        $remarks = '';
        $details = '';
      } else if($restyui1->qcstatus == 1 && $check_store_receipt == 0) {
        $current_status = '<strong>Pending Store Receipt</strong>';
        $location = '';
        $actual_date = '';
        $remarks = '';
        $details = '';
      } else if($restyui1->qcstatus == 1 && $check_store_receipt > 0) {
        $current_status = '<strong>Completed</strong>';
        $getStoreReceiptDetails = $this->storemodel->getStoreReceiptDetails($restyui1->mrn_id);

        if($getStoreReceiptDetails != '') {
          foreach($getStoreReceiptDetails as $row2);
          $location = $row2->stock_location;
          $actual_date = $row2->storerecvon_actual;
          $remarks = $row2->store_reciept_remarks;
        } else {
          $location =0;
          $actual_date = '';
          $remarks = '';
        }

        $location=$this->getlocation_details($location);
        $details = '<strong>Location: </strong>'.$location.'<br><br/><strong>Date: </strong>'.$actual_date.'<br><br/><strong>Remarks: </strong>'.$remarks;
      } else {
        $current_status = '';
        $location = '';
        $actual_date = '';
        $remarks = '';
        $details = '';
      }

      if($restyui1->bill_date != '' && $restyui1->bill_date != '1970-01-01' && $restyui1->bill_date != '0000-00-00') {
        $bill_date = date('d-m-Y',strtotime($restyui1->bill_date));
      } else {
        $bill_date = '';
      }

    $item = $itemname."<br>".$size."<br>".$thickness."<br>".$colour;
    $reqqty = floatval($restyui1->reqty)." ".$unival;
    $recvqty = floatval($restyui1->recqty) ." ".$unival;
    $purchaserate = floatval($restyui1->price);
    $billno =  $restyui1->billno."<br/>".$bill_date."<br/>";
      $backtrack = "<a href='javascript:;' class='btn btn-warning btn-xs' onclick='backtrack_po(".$restyui1->id.")'>Backtrack PO</a>";
  $html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$m.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$restyui1->pono_forshow.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$item.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$vendorname.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$reqqty.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$recvqty.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$purchaserate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$restyui1->mrngatentry.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$billno.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$gby.'</td>
      
  </tr>';
$m++;
}
}

$html.='</tbody></table><br><br><br>';

$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">

</td>
<td></td>
</tr>
</table>';

$todays = $this->uri->segment(3);
$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="10" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">MRN QC '.$dateformat.'</th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>MRN Date</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px; "><b>PO No</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Item Name</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Specification</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Gate Entry No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Bill No</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Received Qty At Gate</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>QC Approved Qty</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>QC Reject Qty</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>QC Rejection Remarks</b></th>

</tr><tbody>';
$m=1;
 $itemdescqty = "";
    $startdate = $todays." 00:00:00";
    $enddate = $todays." 23:59:59";
$date = $todays;
    $date = strtotime($date);
    $date = date('Y-m-d',strtotime("-7 day", $date)).' 00:00:00';
    $scheduler_data = array();
    $genpo=base64_decode($this->uri->segment(3));
    $genp=explode(',',$genpo);

    $start_date = $this->uri->segment(3);
    $end_date = $this->uri->segment(4);
    $vendor = $this->uri->segment(5);
    $item_id = $this->uri->segment(6);

          $this->db->select('a.*,a.gateentryno as gateno,a.id as mrnid,b.*,b.id as poid')
             ->from('mrn a')
             ->join('purchase_order b','a.poid=b.id')
             ->where('b.approved','1')
             ->where('a.qcstatus','1')->where('a.qcdoneOn BETWEEN "'.$startdate. '" and "'.$enddate.'"');
             $rest = $this->db->group_by('a.id')
                   ->order_by('a.qcdoneOn','DESC')
                   ->get();
    //->where('a.completed','0')
      if($rest->num_rows()>0)
      {
      $i=1;
    //  echo "<pre>"; print_r($rest->result()); exit;
      foreach($rest->result() as $restyui1)
      {
$vendorname=$this->getvendorname($restyui1->vendor);
        if($restyui1->potype==0)
        {
          
          $type="Machine Item";
          $itemname=$this->getmachineitemname($restyui1->itemid);
          $otherdetails=$this->getmachineotherdetails($restyui1->itemid);
          if(count($otherdetails)>0)
          {

          $fincode=$otherdetails['fincode'];
          $specification=$otherdetails['specialization'];
          }else{
          $fincode='';
          $specification='';
          }
      
          
        }else if($restyui1->potype==1)
        {
          $type="General Item";
          $itemname=$this->getgeneralitemname($restyui1->itemid);
          $fincode='';
          $specification='';
          
        }
        

            if($restyui1->potype==0)
            {
              
            if($restyui1->source==1)
            {

              $job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$restyui1->jobcard)->get();
              if($job->num_rows()>0)
              {
              foreach($job->result() as $job1);
              $macid=$job1->machineid;
              }else{
              $macid=0;
              }

            }else 
            {
              $macid=0;
            }

            }else 
            {
              $macid=0;
            }
          

            /** **/
            //$prevqty=$this->storemodel->checkifanypreviousqtyisinwarded($restyui1->itemid,$restyui1->pono);

            /** END **/
              

            $restyu=$this->db->select('shortname')->from('units')->where('id',$restyui1->unit)->get();
            if($restyu->num_rows()>0)
            {
            foreach($restyu->result() as $restyu1);
            $unival=$restyu1->shortname;
            }else{
            $unival='';             
            }


$originalleftqty=$restyui1->recqty;
$gateentry=$restyui1->gateno;
$billno=$restyui1->billno;

//$html='<a href="'.page_url.'Reporting/vendorwisepoforgateentry/'.$restyui1->vendor.'" class="btn btn-warning btn-xs">Open PO(s)</a>';
$gateentry=$gateentry;

$recvqty11=floatval($originalleftqty)." ".$unival;
$recvqty=$recvqty11;

$datas=$this->storemodel->getmrnhistorydata($restyui1->mrnid);
if(count($datas)>0)
{

$approved=$datas['accept'];
$reject=$datas['reject'];;
$rejectremarks=$datas['remarks'];  
$rejectfile=$datas['image'];
    
    
}else
{
$approved='Data not available';
$reject='Data not available';
$rejectremarks='Data not available';
$rejectfile='Data not available';
}
        
  $html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$m.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.date('d-m-Y H:i:s',strtotime($restyui1->mrndoneOn)).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$restyui1->pono_forshow.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$itemname.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$specification.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$gateentry.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$billno.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$recvqty.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$approved.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$reject.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$rejectremarks.'</td>
      
  </tr>';
$m++;
}
}


$html.='</tbody></table><br><br><br>';

$todays = $this->uri->segment(3);
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">

</td>
<td></td>
</tr>
</table>';

$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="6" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">Store Physical Receipt '.$dateformat.'</th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>MRN Date</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px; "><b>PO No</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Item Name</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Specification</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Received Qty At Gate</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:12px;"><b>Received By</b></th>

</tr><tbody>';
$m=1;
 $itemdescqty = "";
    $startdate = $todays." 00:00:00";
    $enddate = $todays." 23:59:59";

    $scheduler_data = array();
$this->db->select('a.*,c.mrndoneOn,c.poid,c.unit')->from('mrn_history a')->join('mrn c','a.record_id=c.id')->where('storereciept','1')->where('a.storerecvon BETWEEN "'.$startdate. '" and "'.$enddate.'"');
 $rest = $this->db->order_by('a.id','DESC')->group_by('a.id')->get();
      if($rest->num_rows()>0)
      {
      $i=1;
      foreach($rest->result() as $restyui1)
      {
        
        
        $podetail=$this->getpodetails($restyui1->po_no);
      
      if(count($podetail)>0)
      {
      $potype=$podetail['potype'];
      $sources=$podetail['source'];
      $sourceid=$podetail['sourceid'];
      $unit=$podetail['unit'];
      $pr=$podetail['prno'];
      $pono_forshow=$podetail['pono_forshow'];
      
      }else
      {
      
      $potype='';
      $sources='';
      $sourceid='';
      $unit='';
      $pr='';
      $pono_forshow='';
      
      }
        
        
        
        
        if($potype==0)
        {
        $itemname=$this->getmachineitemname($restyui1->itemid);
        $otherdetails=$this->getmachineotherdetails($restyui1->itemid);
        if(count($otherdetails)>0)
        {
      
        $fincode=$otherdetails['fincode'];
          $specification=$otherdetails['specialization'];
          $rack=$otherdetails['rack_location'];
        }else{
          $fincode='';
          $specification='';
        }
        }else{
          $itemname=$this->getgeneralitemname($restyui1->itemid);
          $fincode='';
          $specification='';
        }

        $storerevon=date('d-M-Y h:i:s',strtotime($restyui1->storerecvon));
        
        $storeby=$this->getusername($restyui1->storerecvby);
        
        $jobcards=$this->getjobcardid($restyui1->record_id);
        
          if($sources=='1')
        {
            $jobcard=$this->getjobcardno($jobcards);
            $source='Jobcard - '.$jobcard;
            
        }else if($sources=='2')
        {
             $sourceid=$this->getsourceid($pr);
              $source='INDENT IND-'.$sourceid;
              
        }else
        {
            
              $source='IMS AUTO PR';
        }
        
        if($vendor != '' && $vendor != 'ALL') {
          $chk_vendor = $this->storemodel->chk_vendor($restyui1->po_id, $vendor);
        } else {
            $chk_vendor = 1;
        }
        
        $uname=$this->getunit($restyui1->unit);


        if($chk_vendor > 0) {
        
  $html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$m.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.date('d-M-Y H:i A',strtotime($restyui1->mrndoneOn)).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$pono_forshow.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$itemname.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$specification.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.floatval($restyui1->accept_qty)." ".$uname.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$storeby.'</td>
      
  </tr>';
$m++;
}
}
}

$html.='</tbody></table><br><br><br>';



$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
//$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=$filelocation."/Store_purchase_report_".date('Y-m-d').".pdf"; //Linux

 //$pdf->Output($fileNL, 'F');
 $pdf->Output($fileNL, 'I');
 
    
}


}


?>