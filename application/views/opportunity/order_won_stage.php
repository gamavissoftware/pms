<?php 
    $CI=&get_instance();
    $lead_id=$this->uri->segment(3);
    $CI->load->model('Salescrm_model','salescrm');
    $ldetails=$CI->salescrm->getLeadDetailsNew($lead_id);
    //echo "<pre>"; print_r($ldetails); exit;
    $pdetails=$CI->salescrm->getLeadProducts($lead_id);
    if(count($ldetails)==0 && count($pdetails)==0)
    {
    echo "Invalid Link"; exit;
    }else
    {
    foreach($ldetails as $row);
    $companyName=$row->company_name;
    $CustomerID=$row->customer_id;
    $oppno=$row->unique_id;
    $machine_type=$row->machine_type;
    $country=$row->country;
    foreach($pdetails as $row1);
    $machineName=$row1->instruments_name;
    }


    $record_id=$this->salescrm->getRecordID($this->uri->segment(3));
    if($record_id==0)
    {
        redirect(page_url.'Opportunity/newopportunity/'.$this->uri->segment(3));
    }

    $basicData=$this->salescrm->getquoteBasicData($record_id);
    //echo "<pre>"; print_r($basicData); exit;
    if(count($basicData)>0)
    {
    $ref_no=$basicData[0];
    $quotation_date=$basicData[1];
    $customer_id=$basicData[2];
    $currency=$basicData[3];
    $country=$basicData[4];
    $machine_name=$basicData[5];
    $machine_model_no=$basicData[6];
    $lead_id=$basicData[7];
    $product_id=$basicData[8];
    $user_detail=$this->salescrm->getUserDetails($lead_id);
    if(count($user_detail)>0)
    {
    $agent=$user_detail[0];
    $contact_no=$user_detail[1];
    $email=$user_detail[2];

    }else
    {
    $agent='';
    $contact_no='';
    $email='';
    }
}


$annexture_1=$this->salescrm->getannexture_1($record_id);
// echo "<pre>"; print_r($annexture_1); exit;
if(count($annexture_1)>0)
{
    $product_to_be_packed=$annexture_1[0];
    $product_name=$annexture_1[1];
    $qty_data=$this->salescrm->getQtyPackedData($record_id);
   // echo "<pre>"; print_r($qty_data); exit;
    //$pouch_size_type=$annexture_1[2];
    //$psize=explode('X',$pouch_size_type);
    // if(count($psize)>0)
    // {
    //     $pwidth=$psize[0];
    //     $plength=$psize[1];
    // }else
    // {
    //     $pwidth='';
    //     $plength='';
    // }
    // $qty_to_be_packed=$annexture_1[3];
    // $qtY_packed=explode(' ',$qty_to_be_packed);
    // if(count($qtY_packed)>0)
    // {
    //     $qtY_packed_value=$qtY_packed[0];
    //     $qtY_packed_unit=trim($qtY_packed[1]);
    // }else
    // {
    //     $qtY_packed_value='';
    //     $qtY_packed_unit='';
    // }

    $qtY_packed_value='';
    $qtY_packed_unit='';

    $horizontal_sealing_width=$annexture_1[4];
    $vertical_sealing_width=$annexture_1[5];
    $perforation_pitch=$annexture_1[6];
    $typeofsealing=$annexture_1[7];
    $plc_make=$annexture_1[8];
    $power_supply=$annexture_1[9];
    $liquidviscositydata=$annexture_1[10];
    $liquidconductivitydata=$annexture_1[11];
    $powderdensitydata=$annexture_1[12];
    $powderdfrdata=$annexture_1[13];
    $powdermoisturecontentdata=$annexture_1[14];
    $perforationstyle=$annexture_1[15];
    $batchcut=$annexture_1[16];
}else
{

    $product_to_be_packed='';
    $product_name='';
    $pouch_size_type='';
    $qty_to_be_packed='';
    $horizontal_sealing_width='';
    $vertical_sealing_width='';
    $perforation_pitch='';
    $typeofsealing='';
    $plc_make='';
    $power_supply='';
    $liquidviscositydata='';
    $liquidconductivitydata='';
    $powderdensitydata='';
    $powderdfrdata='';
    $powdermoisturecontentdata='';
    $batchcut= '';
    $perforationstyle='';

}


$quotation_cum=$this->salescrm->quotation_cum_tech_spec($record_id);
if(count($quotation_cum)>0)
{
    $model=$quotation_cum[0];
    $no_of_axis_in_machine="";
    $axis_detail="";

}else
{
    $model='';
    $no_of_axis_in_machine='';
    $axis_detail='';
}

$axis_details=$this->salescrm->get_axis_detailswithid($record_id);



$axis_data='';
$axis_sum=array();
$axis_sum[]=0;
$axis_details=$this->salescrm->get_axis_detailsNew($record_id);
if(count($axis_details)>0)
{
    for($o=0;$o<count($axis_details['description']);$o++)
    {

        // $axis_data.='<li>'.$axis_details['description'][$o].'-'.$axis_details['axiscount'][$o].'</li>';
        $axis_sum[]=$axis_details['axiscount'][$o];
    }
}

// $axis_data_new='<b>No. of Axis in Machine: <strong>'.array_sum($axis_sum).'</strong></b>';
// $axis_data_new.='<ul style="padding:0px;">'.$axis_data.'</ul>';




$quotation_cum_tech_specdynamic=$this->salescrm->quotation_cum_tech_spec_dynamic($record_id);


$getannexture_2=$this->salescrm->getannexture_2($record_id);
// echo "<pre>"; print_r($getannexture_2); exit;
if(count($getannexture_2)>0)
{
$machinemodel=$getannexture_2[0];
$sealingstyle=$getannexture_2[1];
$speed=$getannexture_2[2];
$no_of_track=$getannexture_2[3];
$leminate_specification=str_replace('<br>','',$getannexture_2[4]);
$product_to_be_packed1=$getannexture_2[5];
$filling_capacity=$getannexture_2[6];
$pouch_size=$getannexture_2[7];
$sealing_drives=$getannexture_2[8];
$perforation_and_cutting=$getannexture_2[9];
$laminate_draw=$getannexture_2[10];
$laminate_tracking=$getannexture_2[11];
$electrical_spec=$getannexture_2[12];
$layout_dimensions=$getannexture_2[13];
$machine_weight=$getannexture_2[14];
$compressed_air=$getannexture_2[15];
$actual_speed=$getannexture_2[16];
$laminatereeldia=str_replace('<br>','',$getannexture_2[17]);
$laminatereelcoredia=str_replace('<br>','',$getannexture_2[18]);
$gross_weight=$getannexture_2[19];
$compressedairbar=$getannexture_2[20];
$filling_accuracy=$getannexture_2[21];
}else
{
$machinemodel='';
$sealingstyle='';
$speed='';
$no_of_track='';
$leminate_specification='';
$product_to_be_packed='';
$filling_capacity='';
$pouch_size='';
$sealing_drives='';
$perforation_and_cutting='';
$laminate_draw='';;
$laminate_tracking='';
$electrical_spec='';
$layout_dimensions='';
$machine_weight='';
$compressed_air='';
$actual_speed = '';
$laminatereeldia='';
$laminatereelcoredia='';
$gross_weight = '';
$compressedairbar = '';
$filling_accuracy='';
}

$layoutimage=$this->salescrm->get_quotation_layout_img($record_id);
$fillingimage=$this->salescrm->get_quotation_machine_filling_image($record_id);
$kldimage=$this->salescrm->get_quotation_machine_kld_image($record_id);
?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright;?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle;?></title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tiny.cloud/1/e5fab2xubroffagp5k06ey24wechrcu4dum004vvi62fmxh1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style type="text/css">

    .select2-container
    {
        width:100% !important;
    }
    #pageloader
    {
    background: rgba( 255, 255, 255, 0.8 );
    display: none;
    height: 100%;
    position: fixed;
    width: 100%;
    z-index: 9999;
    }
    #pageloader img
    {
    left: 30%;
    margin-left: -10px;
    margin-top: -10px;
    position: absolute;
    top: 30%;
    }
    .btn {
    border-radius: 2px;
    padding: 3px 8px;
    }
    label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: 700;
    font-size: 12px !important;
    color: #000 !important;
    }
    .form-control {
    background-color: #FFFFFF;
    border: 1px solid #E3E3E3;
    border-radius: 0px;
    color: #565656;
    padding: 3px 12px;
    height: 28px;
    max-width: 100%;
    -webkit-box-shadow: none;
    box-shadow: none;
    -webkit-transition: all 300ms linear;
    -moz-transition: all 300ms linear;
    -o-transition: all 300ms linear;
    -ms-transition: all 300ms linear;
    transition: all 300ms linear;
    }

    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px;
    line-height: 1.428571;
    /* vertical-align: top; */
    border-top: 1px solid #ddd;
    }
    </style>
    <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.3/classic/ckeditor.js"></script>
    </head>


    <body>


    <!-- Navigation Bar-->
    <header id="topnav">
    <?php $this->load->view('common/nav-menu');?>
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
    <div class="container">

    <!-- Page-Title -->
    <div class="row">
        <div class="col-md-12">
        <?php echo $this->session->flashdata('message'); ?>
        </div>
    </div>
    <div class="row" style="margin-top:20px;">
    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
    <div class="page-title-box">
    <div class="btn-group pull-right">


    </div>

    <h4 class="page-title text-center">Order Won <?php echo ucwords(strtolower($companyName));?> Information <?php echo $oppno;?></h4>
    </div>
    </div>
    </div>
    <!-- end page title end breadcrumb -->
  

    <div class="row">
    <div class="col-xs-12">

    <div>
    <div class="row">
    <div class="col-sm-12 col-xs-12 col-md-12">
    <form method="post" id="loginForm" action="<?php echo page_url;?>Opportunity/leadmarkaswon/<?php echo $this->uri->segment(3);?>/<?php echo $record_id;?>" enctype="multipart/form-data">
        <input type="hidden" name="oldpageurl" value="<?php echo $this->input->server('HTTP_REFERER');?>">
    <div id="pageloader">
    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
    </div>   
    <div class="row card-box" style="border:1px dotted #000;">
    <div class="col-md-12"  style="margin-bottom:10px;">
    <h3 class="page-title text-center">General Information</h3><hr>
    </div>                               
    <div class="col-md-3">
    <div class="form-group">
    <label>Ref No</label>
    <span id="contact_error" style="color:red;">*</span>
    <input type="text" name="refno" id="refno" class="form-control"  value="<?php echo $ref_no;?>" readonly style="color:black;font-weight:bold;" >
    <?php echo form_error('refno'); ?>
    </div>
    </div>
    <div class="col-md-2">
    <div class="form-group">
    <label>Date</label>
    <span id="contact_error" style="color:red;">*</span>
    <input type="date" class="form-control" name="quote_date" value="<?php echo $quotation_date;?>" style="font-weight:bold;color:black;" readonly>
    <span style="color:red"><?php echo form_error('quote_date'); ?></span>

    </div>
    </div>

    <div class="col-md-4">
    <div class="form-group">
    <label>Customer Name</label>
    <span id="contact_error" style="color:red;">*</span>
    <select class="form-control" name="customername" id="customername" readonly style="font-weight:bold;color:black;">

    <option value="<?php echo $CustomerID;?>"><?php echo $companyName;?></option>

    </select>
    <span style="color:red"><?php echo form_error('customername'); ?></span>
    </div>
    </div>
    <div class="col-md-3">
    <div class="form-group">
    <label>Currency</label><br>
    <select name="cur" id="cur" readonly class="form-control" style="font-weight:bold;color:black;">
    <option value="1" <?php if($currency==1){?> selected <?php } ?>>US Dollars</option>
    <option value="2" <?php if($currency==2){?> selected <?php } ?>>INR</option>
    <option value="3" <?php if($currency==3){?> selected <?php } ?>>EURO</option>
    </select>
    <span style="color:red"><?php echo form_error('cur'); ?></span>

    </div>
    </div>

    <div class="col-md-3" >
    <div class="form-group">
    <label>Country</label>
    <select name="country" id="country" class="form-control" style="font-weight:bold;color:black;" readonly>
        <option value="">Select</option>
    <?php 
    $q = $this->db->select('country_id, country_name')->from('countries')->where('country_status',1)->get();
    foreach($q->result() as $rowsss){
    ?>
    <option value="<?php echo $rowsss->country_id;?>"  <?php if($country==$rowsss->country_id){?> selected <?php } ?>><?php echo $rowsss->country_name;?></option>
    <?php }?>
    </select>
    <span style="color:red"><?php echo form_error('country'); ?></span>

    </div>
    </div>


    <div class="col-md-3" style="display:none">
    <div class="form-group">
    <label>Quote for Machine Name</label>
    <span id="contact_error" style="color:red;">*</span>
    <input type="text" name="machineNameDFSF" id="machineNameDFSF" class="form-control" value="<?php echo $machineName;?>" style="font-weight:bold;color:black;" readonly>

    <span style="color:red"><?php echo form_error('machineName'); ?></span>

    </div>
    </div>
     <div class="col-md-4">
    <div class="form-group">
    <label>Machine Type</label>
    <span id="contact_error" style="color:red;">*</span>
  


   <select class="form-control" name="machineName" id="machineName" readonly>
       <option value="">Select Type</option>
       <option value="VFFS SINGLE TRACK MACHINE" <?php if($machine_name=="VFFS SINGLE TRACK MACHINE"){?> selected <?php } ?>>VFFS SINGLE TRACK MACHINE</option>
       <option value="VFFS MULTI TRACK MACHINE" <?php if($machine_name=="VFFS MULTI TRACK MACHINE"){?> selected <?php } ?>>VFFS MULTI TRACK MACHINE</option>
        <option value="VFFS MULTI TRACK STRIP PACK MACHINE" <?php if($machine_name=="HFFS SINGLE TRACK MACHINE"){?> selected <?php } ?>>VFFS MULTI TRACK STRIP PACK MACHINE</option>
       <option value="HFFS SINGLE TRACK MACHINE" <?php if($machine_name=="HFFS SINGLE TRACK MACHINE"){?> selected <?php } ?>>HFFS SINGLE TRACK MACHINE</option>
       <option value="HFFS MULTI TRACK MACHINE" <?php if($machine_name=="HFFS MULTI TRACK MACHINE"){?> selected <?php } ?>>HFFS MULTI TRACK MACHINE</option>
       <option value="HFFS FLOW WRAP MACHINE" <?php if($machine_name=="HFFS FLOW WRAP MACHINE"){?> selected <?php } ?>>HFFS FLOW WRAP MACHINE</option>
       <option value="VFFS COLLAR TYPE MACHINE" <?php if($machine_name=="VFFS COLLAR TYPE MACHINE"){?>  selected <?php } ?>>VFFS COLLAR TYPE MACHINE</option>
       <option value="VFFS COLLAR TYPE TWIN HEAD MACHINE" <?php if($machine_name=="VFFS COLLAR TYPE TWIN HEAD MACHINE"){?> selected <?php } ?>>VFFS COLLAR TYPE TWIN HEAD MACHINE</option>

       <option value="ASEPTIC TETRA PACK" <?php if($machine_name=="ASEPTIC TETRA PACK"){?> selected <?php } ?>>ASEPTIC TETRA PACK</option>
       <option value="VFFS MULTI COLLAR TYPE MACHINE" <?php if($machine_name=="VFFS MULTI COLLAR TYPE MACHINE"){?> selected <?php } ?>>VFFS MULTI COLLAR TYPE MACHINE</option>
       <option value="HFFS PICK FILL SEAL" <?php if($machine_name=="HFFS PICK FILL SEAL"){?> selected <?php } ?>>HFFS PICK FILL SEAL</option>
         <option value="PICK FILL SEAL" <?php if($machine_name=="PICK FILL SEAL"){?> selected <?php } ?>>PICK FILL SEAL</option>
       <option value="LINEAR BOTTLE FILLING" <?php if($machine_name=="LINEAR BOTTLE FILLING"){?> selected <?php } ?>>LINEAR BOTTLE FILLING</option>
        <option value="SECONDARY PACKAGING" <?php if($machine_name=="SECONDARY PACKAGING"){?> selected <?php } ?>>SECONDARY PACKAGING</option>
        <option value="HFFS ROTARY MACHINE" <?php if($machine_name=="HFFS ROTARY MACHINE"){?> selected <?php } ?>>HFFS ROTARY MACHINE</option>


         
   </select>

    <span style="color:red"><?php echo form_error('machineName'); ?></span>
    </div>
    </div>

    <div class="col-md-5">
    <div class="form-group">
    <label>Machine Model No.</label>
    <span id="contact_error" style="color:red;">*</span>
    <input type="text" class="form-control mmodel" name="machineModel" id="machineModel" readonly value="<?php echo $machine_model_no;?>" style="font-weight:bold;color:black;">

    <span style="color:red"><?php echo form_error('machineModel'); ?></span>
    </div>
    </div>
   
    </div>
    


    <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="text-center page-title">Item Information</h3><hr>
   
    </div>

    <div class="col-md-12 table-responsive table-container">

    <table class="table table-bordered" id="myTable">
    <thead>
    <tr>
    <th>Item description</th>
    <th>UOM</th>
    <th>Price</th>
    <th>Total Price in (<span class="pricesymbol">USD</span>)</th>
    </tr>
    </thead>
    <tbody>

     <?php    
$price_data=array();
$price_data[]=0;
$dprice=$this->salescrm->getPriceDataByCategory($record_id,0);
if(count($dprice)>0)
{
    $i=1;
foreach($dprice as $dprice1)
{

    $desc=$dprice1['description'];
    $qty=$dprice1['qty'];
    $price=$dprice1['price'];
    $total_price=$qty*$price;
if($price<>'')
{
    $price_data[]=$total_price;
    $price=floatval($price);
$formatted_price = $price;
$formatted_total_price =$total_price;
}else
{
    $formatted_price='';
    $formatted_total_price='';
}

?>
    <tr>
    <td><input type="text" class="form-control mmodel" name="modelno" id="modelno" onkeyup="getmachinemodelno2();" placeholder="Model"  readonly value="<?php echo $desc;?>">
    <script type="text/javascript">
    function  getmachinemodelno2() {
    var modelno = $("#modelno").val();
    if(modelno!=''){
    $(".mmodel").val(modelno);
    //$("#machinemodel").val(modelno);
    }else{
    $(".mmodel").val();
    //$("#machinemodel").val();
    }
    }
    </script>
    <span style="color:red"><?php echo form_error('modelno');?></span>
    </td>
    <td><input type="text" class="form-control" name="modelqty" id="modelqty" value="<?php echo $qty;?>" onkeyup="calculatemodelprice();" readonly>
    <span style="color:red"><?php echo form_error('modelqty');?></span></td>
    <td><input type="text" class="form-control" name="modelprice" onkeyup="calculatemodelprice();" readonly id="modelprice" value="<?php echo $price;?>"></td>
    <td><input type="text" class="form-control" name="totalmodelprice" readonly id="totalmodelprice" value="<?php echo $total_price;?>" readonly>
    <span style="color:red"><?php echo form_error('modelprice');?></span></td>

    </tr>

<?php } } ?>
    <script type="text/javascript">
       function calculatemodelprice(){
            var modelqty = $("#modelqty").val();
            var modelprice = $("#modelprice").val();
            if(modelqty!=='' && modelprice!==''){
                var modalgrandtotal = parseFloat(modelqty)*parseFloat(modelprice);
                $("#totalmodelprice").val(modalgrandtotal);

            }else{
                var modalgrandtotal = 0;
                $("#totalmodelprice").val(modalgrandtotal);
            }
       }
    </script>
   
   <?php 

   $dprice=$this->salescrm->getPriceDataByCategory($record_id,1);
if(count($dprice)>0)
{
    $i=$i;
   // echo "<pre>"; print_r($dprice); exit;
foreach($dprice as $dprice1)
{

    $desc=$dprice1['description'];
    $qty=$dprice1['qty'];
    $price=$dprice1['price'];
    $total_price=$qty*$price;
    $id=$dprice1['id'];
if($price<>'')
{
    $price_data[]=$total_price;
    $price=floatval($price);
$formatted_price = $price;
$formatted_total_price = $total_price;
}else
{
    $formatted_price='';
    $formatted_total_price='';
}

$instruments=$this->salescrm->getProductName($desc);

?>
    <tr id="LineItem<?php echo $id;?>">
    <td>
    <div class="row">
    <div class="col-md-12">
    <div class="form-group">
    <label>Description</label><br/>
    <input type="hidden" name="existing_optional[]" value="<?php echo $id;?>">
    <select class="form-control descriptionProduct0" name="edittechdescriptioninfo<?php echo $id;?>" id="techdescriptioninfo<?php echo $id;?>" readonly>
        <option value="">Select Option</option>
        <?php $q = $this->db->select('id, instruments_name')->from('presto_instruments')->where('status',1)->where('type!=',0)->order_by('instruments_name','asc')->get(); if($q->num_rows()>0){ foreach($q->result() as $row){?>

            <option value="<?php echo $row->id;?>" <?php if($desc==$row->id){?> selected <?php } ?>><?php echo $row->instruments_name;?></option>
        <?php } } ?>
        
    </select>
    </div>
    </div>
    </div>
    <td>
    <div class="col-md-12">

    <div class="form-group">
    <label>UOM</label>
    <input type="text" class="form-control" name="edittechdescqty<?php echo $id;?>" id="techdescqty<?php echo $id;?>" onkeyup="calculatepricing(<?php echo $id;?>);" value="<?php echo $qty;?>" readonly>
    </div>

    </td>
    <td>
        <div class="col-md-12">
            <div class="form-group">
                <label>Price</label>
                <input type="text" class="form-control" name="edittechdescprice<?php echo $id;?>" id="techdescprice<?php echo $id;?>" onkeyup="calculatepricing(0);" readonly value="<?php echo $price;?>">
            </div>
        </div>
    </td>
    <td>
    <div class="row">
    <div class="col-md-12">
    <div class="form-group">
    <label>Total Price in (<span class="pricesymbol">USD</span>)</label>
    <input type="text" class="form-control" name="edittechdesctotalprice<?php echo $id;?>" id="techdesctotalprice<?php echo $id;?>" value="<?php echo $total_price;?>" readonly readonly>

    </div>
    </div>

    </div>
    </td>
    </tr>
<?php } } ?>

</tbody>
    </table>

    <script type="text/javascript">
        function calculatepricing(i) {
            var techdescqty = $("#techdescqty"+i).val();
            var techdescprice = $("#techdescprice"+i).val();
            if(techdescqty!='' && techdescprice!=''){
                var grandtotalamount = parseFloat(techdescqty)*parseFloat(techdescprice);
                $("#techdesctotalprice"+i).val(grandtotalamount);
            }else{
               
               $("#techdesctotalprice"+i).val(''); 
            }
        }
    </script>

<?php 
$ftype='';
$othercharge=$this->salescrm->quotation_freight_packing_forwarding($record_id);
//echo "<pre>"; print_r($othercharge); exit;
if(count($othercharge)>0)
{
    $ftypedata=$othercharge['freight_type'];
    $freight=$othercharge['freight'];
    $freight_charges=$othercharge['freight_charges'];
    $packing_charges=$othercharge['packing_charges'];
    $forwarding_charges=$othercharge['forwarding_charges'];
    $insurance=$othercharge['insurance'];
}else
{

    $ftypedata='';
    $freight='';
    $freight_charges='';
    $packing_charges='';
    $forwarding_charges='';;
    $insurance='';

}
?>

    <table class="table table-bordered">
    <tbody>

     <tr>
        <td>Freight Charges</td>
        <td>
            <select class="form-control" name="frightinfo" id="frightinfo" onchange="checkfreightinfo();" readonly>
                <option value="">Select Option</option>
                <option value="1" <?php if($freight==1){?> selected <?php } ?>>Extra at Actual</option>
                <option value="2" <?php if($freight==2){?> selected <?php } ?>>In Customer Scope</option>
                <option value="3" <?php if($freight==3){?> selected <?php } ?>>Exclusive</option>
            </select>
        </td>
        

         <td>
           <select class="form-control" name="freightType" id="freightType" readonly>
                <option value="">Select Freight Type</option>
                <option value="EX WORKS" <?php if($ftypedata=="EX WORKS"){?> selected <?php } ?>>EX WORKS</option>
                <option value="FOB"  <?php if($ftypedata=="FOB"){?> selected <?php } ?>>FOB</option>
                <option value="CIF"  <?php if($ftypedata=="CIF"){?> selected <?php } ?>>CIF</option>
                <option value="CFR"  <?php if($ftypedata=="CFR"){?> selected <?php } ?>>CFR</option>
            </select>
        </td>

        <td>
            <input type="text" class="form-control" name="freightamount" readonly placeholder="Freight Charges" id="freightamount" disabled value="<?php echo $freight_charges;?>">
        </td>
    </tr>

    <tr>
        <td>Packaging</td>
        <td><input type="checkbox" name="packagingapplicable" disabled id="packagingapplicable" readonly onchange="packingcheck();" value="1">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            
        </td>
        <td colspan="2"><input type="text" class="form-control" name="packagingpercentage" readonly id="packagingpercentage" disabled placeholder="Packaging Charges"></td>
    </tr>

    <tr>
        <td>Forwarding</td>
        <td><input type="checkbox" name="forwardingapplicable" id="forwardingapplicable" readonly onchange="forwardingcheck();" disabled value="1">
            
            <script>
     
                function forwardingcheck(){
            if($('#forwardingapplicable').is(':checked')){
                    $("#forwardingpercentage").attr('readonly',true);
                    var forwardingcharge = "1%";
                    $("#forwardingpercentage").val(forwardingcharge);
                 }else{
                    $("#forwardingpercentage").attr('readonly',true);
                    $("#forwardingpercentage").val('');
                 }  
                }
            </script>
        </td>
        <td colspan="2"><input type="text" class="form-control" name="forwardingpercentage" readonly id="forwardingpercentage" disabled placeholder="Forwarding Charges"></td>
    </tr>

    <tr>
        <td>Insurance</td>
        <td><input type="checkbox" disabled name="insuranceapplicable" readonly  id="insuranceapplicable" onchange="insurancecheck();" value="1">
            
            <script>
     
                function insurancecheck(){
            if($('#insuranceapplicable').is(':checked')){
                    $("#insurancepercentage").attr('readonly',true);
                    var insurancecharges = "0.5%";
                    $("#insurancepercentage").val(insurancecharges);
                 }else{
                    $("#insurancepercentage").attr('readonly',true);
                    $("#insurancepercentage").val('');
                 }  
                }
            </script>
        </td>
        <td colspan="2"><input type="text" class="form-control" readonly name="insurancepercentage" id="insurancepercentage" placeholder="Insurance Charges" value="<?php echo $insurance;?>"></td>
    </tr>

    </tbody>

    </table>

    </div>
    </div>

<div class="row card-box">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
        <div class="form-group">
            <label style="font-size:20px; font-weight:bold;">Basic Machine</label>
            <select class="form-control" name="basicmachine" id="basicmachine" onchange="ifbasicmachine();">
                <option value="0">NO</option>
                <option value="1">YES</option>
            </select>
        </div>
    </div>
    <div class="col-md-8"></div>
        </div>
    </div>

    <script type="text/javascript">
    function ifbasicmachine(){
        var basicmachine = $("#basicmachine").val();
        if(basicmachine == "1"){
            // If YES, remove 'required'
            $("#attachpo").removeAttr("required");
            $("#pono").removeAttr("required");
        } else {
            // If NO, add 'required'
            $("#attachpo").attr("required", true);
            $("#pono").attr("required", true);
        }
    }
</script>

    
<div class="col-md-2">
<div class="form-group">
<label for="field-1" class="control-label">Upload PO</label>
<span id="error_attachpo" style="color:red;">*</span>
<input type="file" name="attachpo" id="attachpo" value="" class="form-control" required>
</div>
</div>
 <?php
   $odata=$this->salescrm->getotherinformation($record_id);
//echo "<pre>"; print_r($odata); exit;
    if(count($odata)>0)
    {
    $terms_value=$odata[0];
   
    }else
    {
    $terms_value='';
    
    }

?>
<div class="col-md-10">
<div class="form-group">
<label for="field-1" class="control-label">Payment Term </label>
<span id="error_existingpaymentterms" style="color:red;">*</span>
<select class="form-control" name="existingpaymentterms" id="existingpaymentterms" readonly required>

<?php 
$this->db->select('id,payment_terms')->from('payment_terms')->where('id',$terms_value);
$q = $this->db->get();
//echo "<pre>"; print_r($q->result()); exit;
foreach($q->result() as $row){
?>
<option value="<?php echo $row->id;?>"><?php echo $row->payment_terms;?></option>
<?php }?>
</select>
</div>
</div>

<div class="col-md-3">
<div class="form-group">
<label>Company Name</label>
<span style="color:red" id="error_companyname">*</span>
<input type="text" class="form-control" name="companyname" readonly id="companyname" value="<?php echo $companyName;?>">
</div>
</div>

<div class="col-md-3">
<div class="form-group">
<label>PO Number</label>
<span style="color:red" id="error_pono">*</span>
<input type="text" class="form-control" name="pono" id="pono" required value="">
</div>
</div>

<div class="col-md-3">
<div class="form-group">
<label>PO Date</label>
<span style="color:red" id="error_podate">*</span>
<input type="date" class="form-control" name="podate" id="podate" required value="">
</div>
</div>

<div class="col-md-3">
<div class="form-group">
<label>Customer Currency</label>
<span style="color:red" id="error_currency">*</span>
<select class="form-control" name="currency" id="currency" readonly required onchange="calculatecurrency();">
<option value="">Select Customer Currency</option> 
<option value="INR" <?php if($currency==2){?> selected <?php } ?>>INR</option>
<option value="USD" <?php if($currency==1){?> selected <?php } ?>>USD</option>
<option value="EUR" <?php if($currency==3){?> selected <?php } ?>>EUR</option>
</select>
</div>
</div>
<?php 
$desc = "";
$qty = "";
$q = $this->db->select('id, description,qty')->from('quotation_annexture_4')->where('record_id',$record_id)->where('category_type',0)->get();
if($q->num_rows()>0)
{
    foreach($q->result() as $machinedata);
    $desc = $machinedata->description;
    $machineqty = $machinedata->qty;
}else{
    $desc = '';
    $machineqty  = 0;
}
for($i=1; $i<=$machineqty; $i++){
?>
<div class="col-md-4">
    <div class="form-group">
        <label>Machine</label>
        <input type="text" class="form-control" name="orderwonmachinename[]" id="machinename" value="<?php echo $desc;?>" readonly>
    </div>
</div>
<div class="col-md-4">
    <div class="form-group">
        <label>Amount in Customer Currency</label>
        <input class="form-control" type="number" name="ordervalueincustomercurrency[]" id="ordervalueincustomercurrency<?php echo $i;?>" value="" onkeyup="calculatecurrency('<?php echo $i;?>');" required>
    </div>
</div>

<script type="text/javascript">
    function calculatecurrency(i) {
        //getcurrencyvalue();
    var currency = $("#currency").val();
    var basecurrency = "INR";
    var order_value = $("#ordervalueincustomercurrency"+i).val();

    // Show the loading message
    $("#loadingMessage").show();

    $.ajax({
        type: "post",
        url: "<?php echo page_url;?>Task/googlecurrencyconvertertool",
        data: "currency=" + currency + "&basecurrency=" + basecurrency + "&order_value=" + order_value,
        success: function(data) {
            $("#order_value"+i).val(data);
        },
        complete: function() {
            // Hide the loading message
            $("#loadingMessage"+i).hide();
        }
    });
}


</script>

<div id="loadingMessage<?php echo $i;?>" style="display: none; color:red;">Please wait while calculating...</div>
<!-- <input type="text" name="currencyvalue" id="currencyvalue" value=""> -->
<div class="col-md-4">
<div class="form-group">
<label>Order Value in INR</label>
<span style="color:red" id="error_podate">*</span>
<input type="number" class="form-control" name="order_value[]" id="order_value<?php echo $i;?>" steps="any" value="" readonly required>
</div>
</div>
<?php }?>

<div class="col-md-3">
        <div class="form-group">
            <label>Penality DF?</label>
            <input type="checkbox" name="Penalitydf" id="Penalitydf" onchange="checkPenalitydf();" value="1">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <!-- <script>
                function checkPenalitydf(){
                    $("#checkPenalitydfbox").css('display','none');  
                     $("#penalityamount").attr('required',false); 
                    if($('#Penalitydf').is(':checked')){
                        $("#checkPenalitydfbox").css('display','block');
                        $("#penalityamount").attr('required',true);
                       
                         }else{
                         $("#penalityamount").attr('required',false);
                        $("#checkPenalitydfbox").css('display','none');                     }  
                    }
    </script> -->
        </div>
    </div>
    <div class="col-md-3" id="checkPenalitydfbox" style="display:none">
        <div class="form-group">
            <label>Penality Amount</label>
            <input type="number" name="penalityamount" id="penalityamount" class="form-control" value="0">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Prof. Inv. No. <span style="color:red;">* (This is for PI)</span> </label>
            <input type="text" class="form-control" name="prof_inv_no" id="prof_inv_no" required>
        </div>
    </div>
</div>




    <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 text-center">
    <div class="form-group">
    <label>&nbsp;</label>
    <input type="submit" id="businessupdate" class="btn btn-success" value="Mark as Order Won" style="width:60%">
    </div>
    </div>
    </div>
     </form>


    </div>

    </div>
    <!-- end row -->
    </div> <!-- end ard-box -->
    </div><!-- end col-->

    </div>
    <!-- end row -->


    <!-- Footer -->
    <?php $this->load->view('common/footer');?>
    <!-- End Footer -->

    </div> <!-- end container -->
    </div>
    <!-- end wrapper -->


    <!-- jQuery  -->
    <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>js/detect.js"></script>
    <script src="<?php echo assets_url;?>js/fastclick.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url;?>js/waves.js"></script>
    <script src="<?php echo assets_url;?>js/wow.min.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

    <!-- Datatables-->
    <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

    <!-- Datatable init js -->
    <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
       <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>


    </body>
    </html>