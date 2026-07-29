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

.select2-container .select2-selection--single .select2-selection__rendered
{
    white-space:normal !important;
}

.select2-container option {
  white-space: normal; /* Allow text to wrap */
  word-wrap: break-word; /* Handle long words */
}
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

    <h4 class="page-title text-center">You are Editing Quotation For <?php echo ucwords(strtolower($companyName));?> For Opportunity <?php echo $oppno;?></h4>
    </div>
    </div>
    </div>
    <!-- end page title end breadcrumb -->
  

    <div class="row">
    <div class="col-xs-12">

    <div>
    <div class="row">
    <div class="col-sm-12 col-xs-12 col-md-12">
    <form method="post" id="loginForm" action="<?php echo page_url;?>Opportunity/updatenew/<?php echo $this->uri->segment(3);?>/<?php echo $record_id;?>/<?php echo $this->uri->segment(4);?>" enctype="multipart/form-data">
    <div id="pageloader">
    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
    </div>   
    <div class="row card-box" style="border:1px dotted #000;">
    <div class="col-md-12"  style="margin-bottom:10px;">
    <h3 class="page-title text-center">General Information</h3>
    </div>                               
    <div class="col-md-3">
    <div class="form-group">
    <label>Ref No</label>
    <span id="contact_error" style="color:red;">*</span>
    <input type="text" name="refno" id="refno" class="form-control"  value="<?php echo $ref_no;?>" readonly style="color:black;font-weight:bold;" required >
    <?php echo form_error('refno'); ?>
    </div>
    </div>
    <div class="col-md-2">
    <div class="form-group">
    <label>Date</label>
    <span id="contact_error" style="color:red;">*</span>
    <input type="date" class="form-control" name="quote_date" value="<?php echo $quotation_date;?>" required style="font-weight:bold;color:black;">
    <span style="color:red"><?php echo form_error('quote_date'); ?></span>

    </div>
    </div>

    <div class="col-md-4">
    <div class="form-group">
    <label>Customer Name</label>
    <span id="contact_error" style="color:red;">*</span>
    <select class="form-control" name="customername" id="customername" required="" style="font-weight:bold;color:black;">

    <option value="<?php echo $CustomerID;?>"><?php echo $companyName;?></option>

    </select>
    <span style="color:red"><?php echo form_error('customername'); ?></span>
    </div>
    </div>
    <div class="col-md-3">
    <div class="form-group">
    <label>Currency</label><br>
    <select name="cur" id="cur" class="form-control" style="font-weight:bold;color:black;" onchange="change_currency();" required>
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
    <select name="country" id="country" class="form-control" style="font-weight:bold;color:black;" onchange="change_currency();" required>
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
    <input type="text" name="machineNameDFSF" id="machineNameDFSF" class="form-control" value="<?php echo $machineName;?>" style="font-weight:bold;color:black;" required readonly>

    <span style="color:red"><?php echo form_error('machineName'); ?></span>

    </div>
    </div>
     <div class="col-md-4">
    <div class="form-group">
    <label>Machine Type</label>
    <span id="contact_error" style="color:red;">*</span>
   <select class="form-control" name="machineName" id="machineName" required onchange="checkmachinename();">
       <option value="">Select Type</option>
       <option value="VFFS SINGLE TRACK MACHINE" <?php if($machine_name=="VFFS SINGLE TRACK MACHINE"){?> selected <?php } ?>>VFFS SINGLE TRACK MACHINE</option>
       <option value="VFFS MULTI TRACK MACHINE" <?php if($machine_name=="VFFS MULTI TRACK MACHINE"){?> selected <?php } ?>>VFFS MULTI TRACK MACHINE</option>
       <option value="HFFS SINGLE TRACK MACHINE" <?php if($machine_name=="HFFS SINGLE TRACK MACHINE"){?> selected <?php } ?>>HFFS SINGLE TRACK MACHINE</option>
       <option value="HFFS MULTI TRACK MACHINE" <?php if($machine_name=="HFFS MULTI TRACK MACHINE"){?> selected <?php } ?>>HFFS MULTI TRACK MACHINE</option>
       <option value="HFFS FLOW WRAP MACHINE" <?php if($machine_name=="HFFS FLOW WRAP MACHINE"){?> selected <?php } ?>>HFFS FLOW WRAP MACHINE</option>
       <option value="VFFS COLLAR TYPE MACHINE" <?php if($machine_name=="VFFS COLLAR TYPE MACHINE"){?> selected <?php } ?>>VFFS COLLAR TYPE MACHINE</option>
       <option value="VFFS COLLAR TYPE TWIN HEAD MACHINE" <?php if($machine_name=="VFFS COLLAR TYPE TWIN HEAD MACHINE"){?> selected <?php } ?>>VFFS COLLAR TYPE TWIN HEAD MACHINE</option>


        <option value="ASEPTIC TETRA PACK" <?php if($machine_name=="ASEPTIC TETRA PACK"){?> selected <?php } ?>>ASEPTIC TETRA PACK</option>
       <option value="VFFS MULTI COLLAR TYPE MACHINE" <?php if($machine_name=="VFFS MULTI COLLAR TYPE MACHINE"){?> selected <?php } ?>>VFFS MULTI COLLAR TYPE MACHINE</option>
       <option value="HFFS PICK FILL SEAL" <?php if($machine_name=="HFFS PICK FILL SEAL"){?> selected <?php } ?>>HFFS PICK FILL SEAL</option>
       <option value="LINEAR BOTTLE FILLING" <?php if($machine_name=="LINEAR BOTTLE FILLING"){?> selected <?php } ?>>LINEAR BOTTLE FILLING</option>
        <option value="SECONDARY PACKAGING" <?php if($machine_name=="SECONDARY PACKAGING"){?> selected <?php } ?>>SECONDARY PACKAGING</option>




      
       
   </select>

    <span style="color:red"><?php echo form_error('machineName'); ?></span>
    </div>
    </div>

    <div class="col-md-5">
    <div class="form-group">
    <label>Machine Model No.</label>
    <span id="contact_error" style="color:red;">*</span>
    <input type="text" class="form-control mmodel" name="machineModel" id="machineModel" onkeyup="getmachinemodelno();" value="<?php echo $machine_model_no;?>" style="font-weight:bold;color:black;" required>

    <span style="color:red"><?php echo form_error('machineModel'); ?></span>
    </div>
    </div>
    <script type="text/javascript">
    function  getmachinemodelno() {
    var machineModel = $("#machineModel").val();
    if(machineModel!=''){
    $(".mmodel").val(machineModel);
    }else{
    $(".mmodel").val();
    }
    }
    </script>
    </div>
    <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="page-title text-center">Annexture 1</h3>
    </div>

    <div class="col-md-12 table-responsive table-container">
    <table class="table table-bordered">

    <tbody>
    <tr>
    <td>1</td>
    <td>Product to be Packed</td>
    <td>Liquid/Powder/Granules/Pouches</td>
    <td><select id="producttobepacked" class="form-control" name="producttobepacked" onchange="checkproductpacked();" required="">
    <option value="">--Select Select Product --</option>
    <option value="1" <?php if($product_to_be_packed==1){?> selected <?php } ?> >Liquid</option>
    <option value="2" <?php if($product_to_be_packed==2){?> selected <?php } ?>>Powder</option>
    <option value="3" <?php if($product_to_be_packed==3){?> selected <?php } ?>>Granules</option>
    <option value="4" <?php if($product_to_be_packed==4){?> selected <?php } ?>>Pouches</option>

    </select>
    <script type="text/javascript">
        function checkproductpacked(){
           var producttobepacked = $("#producttobepacked").val(); 
           if(producttobepacked==1){
            $("#liquidviscositydata").attr('disabled',false);
            $("#liquidconductivitydata").attr('disabled',false);
            $("liquidviscositydata").attr('Required','true');
            $("#liquidconductivitydata").attr('Required','true');

             $("#powderdensitydata").attr('disabled',true);
            $("#powderdfrdata").attr('disabled',true);
            $("#powdermoisturecontentdata").attr('disabled',true);
             $("#powderdensitydata").attr('Required',false);
            $("#powderdfrdata").attr('Required',false);
            $("#powdermoisturecontentdata").attr('Required',false);


           }else{
            $("#liquidviscositydata").attr('disabled',true);
            $("#liquidconductivitydata").attr('disabled',true);
             $("liquidviscositydata").attr('Required',false);
            $("#liquidconductivitydata").attr('Required',false);

             $("#powderdensitydata").attr('disabled',false);
            $("#powderdfrdata").attr('disabled',false);
            $("#powdermoisturecontentdata").attr('disabled',false);
             $("#powderdensitydata").attr('Required',true);
            $("#powderdfrdata").attr('Required',true);
            $("#powdermoisturecontentdata").attr('Required',true);
           }
        }
    </script>
    <span style="color:red"><?php echo form_error('producttobepacked');?></span></td>

    </tr>
    <tr>
    <td>2</td>
    <td>Product name</td>
    <td></td>
    <td><input type="text" class="form-control" name="productname" id="productname" required onkeyup="getproductname();" value="<?php echo $product_name;?>">
    <span style="color:red"><?php echo form_error('productname');?></span>
    </td>
    <script type="text/javascript">
    function  getproductname() {
    var productname = $("#productname").val();
    if(productname!=''){
    $("#product_tobepacked").val(productname);
    }else{
    $("#product_tobepacked").val();
    }
    }
    </script>

    </tr>


    <tr>
    <td>3</td>
    <td> Qty to be packed & Pouch Size</td>
    <td>W x L (in mm)</td>
    <td>
    <textarea class="form-control"></textarea>

  
   <!--  <div class="row">
    <div class="col-md-6"><input type="text" class="form-control" onkeyup="getpouchsizew();" name="pouchsizew" placeholder="W in mm" id="pouchsizew" value="<?php //echo $pwidth;?>" required>
    <span style="color:red"><?php //echo form_error('pouchsizew');?></span></div>
    <div class="col-md-6"><input type="text" class="form-control" onkeyup="getpouchsizel();" name="pouchsizel" id="pouchsizel" placeholder="L in mm" value="<?php //echo $plength;?>" required>
    <span style="color:red"><?php //echo form_error('pouchsizel');?></span></div>
    </div> -->

    <script type="text/javascript">
    function  getpouchsizew() {

    var pouchsizew = $("#pouchsizew").val();
    if(pouchsizew!=''){
    $("#pouchsizew1").val(pouchsizew);
    }else{
    $("#pouchsizew1").val();
    }
    }
    function  getpouchsizel() {
    var pouchsizel = $("#pouchsizel").val();
    if(pouchsizel!=''){
    $("#pouchsizel1").val(pouchsizel);
    }else{
    $("#pouchsizel1").val();
    }
    }
    </script>


    </td>
    </tr>

    <!-- <tr>
    <td>4</td>
    <td>Quantity to be packed</td>
    <td>ml / gm</td>
    <td>  
    <div class="row">
    <div class="col-md-6"><input type="text" class="form-control" name="qtytobepacked" onkeyup="qtytobepackedfetch();" id="qtytobepacked" value="<?php //echo $qtY_packed_value;?>" required>
    <span style="color:red"><?php //echo form_error('qtytobepacked');?></span></div>
    <div class="col-md-6">
    <select class="form-control" name="qty_unit" id="qty_unit" required>
    <option value="">Select Qty</option>
    <option value="ml" <?php //if($qtY_packed_unit=="ml"){?> selected <?php //} ?>>ml</option>
    <option value="gm" <?php //if($qtY_packed_unit=="gm"){?> selected <?php //} ?>>gm</option>
    </select>
    </div>
    </div>
    <script type="text/javascript">
        function qtytobepackedfetch(){
            var qtytobepacked = $("#qtytobepacked").val(); 
            if(qtytobepacked>0){
                $("#fillingcapacity").val(qtytobepacked);
            }else{
                $("#fillingcapacity").val('');
            }
        }

    </script>

    </td>
    </tr> -->

     <tr>
    <td>4</td>
    <td>Sealing Style </td>
    <td></td>
    <td><input type="text" class="form-control" name="sealingstyle" id="sealingstyle" required value="<?php echo $sealingstyle;?>">
    <span style="color:red"><?php echo form_error('sealingstyle');?></span>
    </td>
    </tr>


    <tr>
    <td>5</td>
    <td>Horizontal Sealing Width</td>
    <td>mm</td>
    <td>  <input type="text" class="form-control" name="horizontalsealingwidth" id="horizontalsealingwidth" value="<?php echo $horizontal_sealing_width;?>" required>
    <span style="color:red"><?php echo form_error('horizontalsealingwidth');?></span>
    </td>
    </tr>

    <tr>
    <td>6</td>
    <td>Vertical Sealing Width</td>
    <td>mm</td>
    <td>   <input type="text" class="form-control" name="verticalsealingwidth" id="verticalsealingwidth" value="<?php echo $vertical_sealing_width;?>" required>
    <span style="color:red"><?php echo form_error('verticalsealingwidth');?></span>
    </td>
    </tr>

    <tr>
    <td>7</td>
    <td>Perforation Pitch</td>
    <td>mm</td>
    <td>   <input type="text" class="form-control" name="perforationpitch" id="perforationpitch" value="<?php echo $perforation_pitch;?>" required>
    <span style="color:red"><?php echo form_error('perforationpitch');?></span>
    </td>
    </tr>

    <tr>
    <td>8</td>
    <td>Perforation Style</td>
    <td>V/Y/Straight</td>
    <td>   <select class="form-control" name="perforationstyle" id="perforationstyle" value="<?php echo set_value('perforationstyle');?>" required>
        <option value="">Select</option>
        <option value="V-Type" <?php if($perforationstyle=='V-Type'){?> selected <?php } ?>>V-Type</option>
        <option value="Y-Type" <?php if($perforationstyle=='Y-Type'){?> selected <?php } ?>>Y-Type</option>
        <option value="Straight" <?php if($perforationstyle=='Straight'){?> selected <?php } ?>>Straight</option>
    </select>
    <span style="color:red"><?php echo form_error('perforationstyle');?></span>
    </td>
    </tr>

    <tr>
    <td>9</td>
    <td>Batch Cut</td>
    <td>String/Straight</td>
    <td>
        <select class="form-control" name="batchcut" id="batchcut" required>
        <option value="">Select</option>
        <option value="Straight" <?php if($batchcut=='Straight'){?> selected <?php } ?>>Straight</option>
        <!-- <option value="Cutting" <?php if($batchcut=='Cutting'){?> selected <?php } ?>>Cutting</option> -->
        <option value="String" <?php if($batchcut=='String'){?> selected <?php } ?>>String</option>
    </select>
    <span style="color:red"><?php echo form_error('batchcut');?></span>
    </td>
    </tr>

    <tr>
    <td>10</td>
    <td>Type of Sealing</td>
    <td>VLining/Butt/K nurling/Plain</td>
    <td>   <select class="form-control" id="typeofsealing" name="typeofsealing" required="">
    <option value="">--Select Type of Sealing--</option>
    <option value="V-Lining" <?php if($typeofsealing=="V-Lining"){?> selected <?php } ?>>V-Lining</option>
    <option value="Butt" <?php if($typeofsealing=="Butt"){?> selected <?php } ?>>Butt</option>
    <option value="Knurling" <?php if($typeofsealing=="Knurling"){?> selected <?php } ?>>Knurling</option>
    <option value="Plain" <?php if($typeofsealing=="Plain"){?> selected <?php } ?>>Plain</option>

    </select>
    <span style="color:red"><?php echo form_error('typeofsealing');?></span>
    </td>
    </tr>

    <tr>
    <td>11</td>
    <td>PLC Make</td>
    <td>Omron/ Allen Bradley/ Mitsubishi/ Schneider</td>
    <td>    
    <select class="form-control" id="plcmake" name="plcmake" required="">
    <option value="">--Select PLC Make--</option>
    <option value="Omron" <?php if($plc_make=="Omron"){?> selected <?php } ?>>Omron</option>
    <option value="Allen Bradley" <?php if($plc_make=="Allen Bradley"){?> selected <?php } ?>>Allen Bradley</option>
    <option value="Mitsubishi" <?php if($plc_make=="Mitsubishi"){?> selected <?php } ?>>Mitsubishi</option>
    <option value="Schneider" <?php if($plc_make=="Schneider"){?> selected <?php } ?>>Schneider</option>

    </select>
    <span style="color:red"><?php echo form_error('plcmake');?></span>
    </td>
    </tr>
    <tr>
    <td>12</td>
    <td>Power Supply</td>
    <td>VAC/Ph/Hz</td>
    <td><input type="text" class="form-control" name="powersupply" id="powersupply" value="<?php echo $power_supply;?>" required>
    <span style="color:red"><?php echo form_error('powersupply');?></span>
    </td>
    </tr>

    <tr id="liquidviscosity">
    <td>13</td>
    <td>Liquid Viscosity</td>
    <td>-</td>
    <td><input type="text" class="form-control" name="liquidviscositydata" id="liquidviscositydata" value="<?php echo $liquidviscositydata;?>">
    <span style="color:red"><?php echo form_error('liquidviscositydata');?></span>
    </td>
    </tr>

    <tr id="liquidconductivity">
    <td>14</td>
    <td>Liquid Conductivity</td>
    <td>-</td>
    <td><input type="text" class="form-control" name="liquidconductivitydata" id="liquidconductivitydata" value="<?php echo $liquidconductivitydata;?>">
    <span style="color:red"><?php echo form_error('liquidconductivitydata');?></span>
    </td>
    </tr>

    <tr id="powderdensity">
    <td>15</td>
    <td>Powder Bulk Density</td>
    <td>-</td>
    <td><input type="text" class="form-control" name="powderdensitydata" id="powderdensitydata" value="<?php echo $powderdensitydata;?>">
    <span style="color:red"><?php echo form_error('powderdensitydata');?></span>
    </td>
    </tr>

    <tr id="powderdfr">
    <td>16</td>
    <td>Power DFR</td>
    <td>-</td>
    <td><input type="text" class="form-control" name="powderdfrdata" id="powderdfrdata" value="<?php echo $powderdfrdata;?>">
    <span style="color:red"><?php echo form_error('powderdfrdata');?></span>
    </td>
    </tr>

     <tr id="powdermoisturecontent">
    <td>17</td>
    <td>Moisture Content </td>
    <td>(%)</td>
    <td><input type="text" class="form-control" name="powdermoisturecontentdata" id="powdermoisturecontentdata" value="<?php echo $powdermoisturecontentdata;?>">
    <span style="color:red"><?php echo form_error('powdermoisturecontentdata');?></span>
    </td>
    </tr>
    <!-- Add more rows as needed -->
    </tbody>
    </table>
    </div>
    </div>

    <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="page-title text-center">Quotation Cum Technical Specification Of The Machine</h3></div>
    <hr>

    <div class=" col-md-12 table-responsive table-container">
    <table class="table table-bordered">
    <thead>
    <tr>
    <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>Model <input type="text" class="form-control mmodel" name="machinemodelno" id="machinemodelno" value="<?php echo $model;?>" required>
    <span style="color:red"><?php echo form_error('machinemodelno');?></span>

    <?php 

    $ul='';

    if($quotation_cum_tech_specdynamic<>'')
    {
    //$ul.='<ul>';
    foreach($quotation_cum_tech_specdynamic as $techdata);
        $ul=$techdata->model;

    ?>
  

    <!-- PREVIOUS ADDED DATA -->
    <div class="row" id="technical<?php echo $techdata->id;?>">
    <input type="hidden" name="technicalDataid[]" value="<?php echo $techdata->id;?>">
    <div class="col-md-12">
    <div class="form-group">
    <label>Technical Specification</label>
    <textarea class="form-control" name="technicalspecedit<?php echo $techdata->id;?>" id="technicalspecedit"><?php echo $ul;?></textarea>
    <script>
    CKEDITOR.replace( 'technicalspecedit' );
    </script>
</div>
    </div>
   <!--  <div class="col-md-1">
    <div class="form-group" style="padding-top:20px">
    <button type="button" class="btn btn-danger" name="close"><i class="fa fa-close" onclick="delete_technical_specs(<?php echo $techdata->id;?>)"></i></button>
    </div>

    </div> -->
    </div>
   <?php 
}
?>
    <!-- END -->

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>Add More Technical Specification</label><br/>
            <input type="checkbox" name="addmoretechnical" id="addmoretechnical" value="1" onchange="addmorespecs();">
            </div>
        </div>
    </div>
    <script>
        function addmorespecs()
        {
                $(".addmorespecs").css('display','none');
                $(".addmorespecs :input").attr('required',false);
                if($('#addmoretechnical').is(':checked')){
                $(".addmorespecs").css('display','');
                $(".addmorespecs :input").attr('required',true);
                }

        }
    </script>

    <div class="row addmorespecs" style="display: none;">
    <div class="col-md-11">
    <div class="form-group">
    <label>Technical Specification</label>
    <input type="text" class="form-control" name="technicalspec[]" id="technicalspec">
    </div>
    </div>
    <div class="col-md-1">
    <div class="form-group" style="padding-top:20px">
    <button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
    </div>

    </div>
    </div>

    <div id="dynamictasks1" class="addmorespecs">

    </div>
    </td>
    </tr>

    <tr>

   <!--  <td>No. of Axis in Machine: <input type="text" class="form-control" name="noofaxisinmachine" id="noofaxisinmachine" value="<?php //echo count($axis_details);?>" required>
    <span style="color:red"><?php //echo form_error('noofaxisinmachine');?></span><br><br>
    <?php 
    //for($t=0;$t<count($axis_details);$t++)
    //{ 
        //echo "<pre>"; print_r($axis_details); exit; 
        ?>
    <div class="row" id="axisDetails<?php //echo $axis_details[$t]['id'];?>">
        <input type="hidden" name="axisid[]" value="<?php //echo $axis_details[$t]['id'];?>">
    <div class="col-md-11">
    <div class="form-group"><input type="text" class="inputField form-control" name="noofaxisinmachineinputedit<?php //echo $axis_details[$t]['id'];?>"  placeholder="Input ' + (i+1) + '" required value="<?php //echo $axis_details[$t]['description'];?>"></div>
    </div>
    <div class="col-md-1">
        <a href="javascript:;" onclick="delete_axis_specs(<?php //echo $axis_details[$t]['id'];?>);"><i class="fa fa-close" class="btn btn-danger"></i></a>
    </div>
</div>
    <?php  //} ?>
    <div id="inputFields"></div>

    </td> -->

    <td>
    
        <?php 
        if(count($axis_details)>0)
        {
        for($o=0;$o<count($axis_details['description']);$o++)
        {

        // $axis_data.='<li>'.$axis_details['description'][$o].'-'.$axis_details['axiscount'][$o].'</li>';
        ?> 
        <div class="row">
        <div class="col-md-4">
        <div class="form-group">
        <label>Axis Details</label>
        <input type="hidden" name="axis_id[]" value="<?php echo $axis_details['id'][$o];?>">
        <input type="text" name="editnoofaxisinmachine<?php echo $axis_details['id'][$o];?>" class="form-control" id="editnoofaxisinmachine<?php echo $axis_details['id'][$o];?>" value="<?php echo $axis_details['description'][$o];?>">
        </div>
        </div>

        <div class="col-md-4">
        <div class="form-group">
        <label>Count</label>
        <input type="number" name="editnoofaxisincount<?php echo $axis_details['id'][$o];?>" class="form-control" id="editnoofaxisincount<?php echo $axis_details['id'][$o];?>" min="0" value="<?php echo $axis_details['axiscount'][$o];?>">
        </div>
        </div>
        <div class="col-md-2" style="margin-top: 25px;">
        <a href='javascript:;' onclick="delete_axis(<?php echo $axis_details['id'][$o];?>)"><i class="fa fa-close"></i></a>
        </div>
        </div>
        <?php } } ?>

        <div class="row">
            <div class="col-md-12">
            <input type="checkbox" name="more_axis" value="1" id="more_axis" onchange="addmoreaxis();">&nbsp;Add More Axis
        </div>
        </div>


        <div class="row" style="display:none" id="new_axis">
        <div class="col-md-4">
        <div class="form-group">
        <label>Axis Details</label>
        <input type="text" name="noofaxisinmachine[]" class="form-control" id="noofaxisinmachine1">
        </div>
        </div>

        <div class="col-md-4">
        <div class="form-group">
        <label>Count</label>
        <input type="number" name="noofaxisincount[]" class="form-control" id="noofaxisincount1" min="0">
        </div>
        </div>
        <div class="col-md-2" style="margin-top: 25px;">
        <a href='javascript:;' id="addMoreAxis"><i class="fa fa-plus"></i></a>
        </div>

        <div id="moreAxis"></div>

        </div>

        

    </td>
    </tr>

    <script>
    $(document).ready(function(){
        checkproductpacked();
        checkfreightinfo();
        checkmachinename();
        selectProductPacked();
    $('#noofaxisinmachine').on('input', function() {
    var count = parseInt($(this).val());
    if (!isNaN(count)) {
    $('#inputFields').empty(); // Clear previous input fields
    for (var i = 0; i < count; i++) {
    $('#inputFields').append('<div class="form-group"><input type="text" class="inputField form-control" name="noofaxisinmachineinput[]"  placeholder="Input ' + (i+1) + '" required></div>');
    }
    }
    });
    });
    </script>
    </tbody>
    </table>
    </div>
    </div>

    <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="text-center page-title">Annexure- II (Technical Specifications)</h3>
    </div>

    <div class="col-md-12 table-responsive table-container">
    <table class="table table-bordered">

    <tbody>
    <tr>
    <td>Machine Model </td>
    <td><input type="text" class="form-control mmodel" name="machinemodel" onkeyup="getmachinemodelno1();" id="machinemodel" value="<?php echo $machinemodel;?>" required>
    <span style="color:red"><?php echo form_error('machinemodel');?></span>
    </td>
    </tr>
    <script type="text/javascript">
    function  getmachinemodelno1() {
    var machinemodel = $("#machinemodel").val();
    if(machinemodel!=''){
    $(".mmodel").val(machinemodel);
    }else{
    $(".mmodel").val();
    }
    }
    </script>

     <tr>
    <td>No. of Tracks </td>
    <td> <input type="text" class="form-control" name="nooftracks" id="nooftracks" value="<?php echo $no_of_track;?>" required>
    <span style="color:red"><?php echo form_error('nooftracks');?></span>
    </td>
    </tr>



   


     <tr>
    <td>Product to be packed </td>
    <td> 

    <div class="col-md-8">
    <input type="text" class="form-control" name="product_tobepacked" id="product_tobepacked" onkeyup="producttobepacked();" value="<?php echo $product_name;?>" required>
    </div>
    <div class="col-md-4">
    <select class="form-control" name="product_packedUnit" id="product_packedUnit">
    </select>
    </div>
    <span style="color:red"><?php echo form_error('product_tobepacked');?></span>

    </td>
    </tr>
    <script type="text/javascript">
    function producttobepacked() {
    var product_tobepacked = $("#product_tobepacked").val();
    if(product_tobepacked!=''){
    $("#productname").val(product_tobepacked);
    }else{
    $("#productname").val();
    }
    }
    </script>
    <tr>
    <td>Filling capacity </td>
    <td> <input type="text" class="form-control" name="fillingcapacity" id="fillingcapacity" onkeyup="fillingcapacitychangeupdate();" value="<?php echo $filling_capacity;?>" required>
        <script type="text/javascript">
             function fillingcapacitychangeupdate(){
            var fillingcapacity = $("#fillingcapacity").val(); 
            if(fillingcapacity>0){
                $("#qtytobepacked").val(fillingcapacity);
            }else{
                $("#qtytobepacked").val('');
            }
        }
        </script>
    <span style="color:red"><?php echo form_error('fillingcapacity');?></span></td>
    </tr>


    <tr>
    <td>Filling Accuracy</td>
    <td><div class="row">

    <textarea class="" id="fillingaccuracy" name="fillingaccuracy" required><?php echo $filling_accuracy;?><?php echo set_value('fillingaccuracy');?></textarea>
    <script>
    CKEDITOR.replace( 'fillingaccuracy' );
    </script>
    </div>  
    </td>
    </tr>



    <tr>
    <td>Speed</td>
    <td><div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Design Speed</label>
            <textarea class="" id="designspeed" name="designspeed" required><?php echo set_value('designspeed');?><?php echo $speed;?></textarea>
        </div>
 <script>
    CKEDITOR.replace( 'designspeed' );
    </script>
           
            
    
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Actual Speed</label>
        <textarea class="form-control" name="actualspeed" id="actualspeed" required><?php echo set_value('actualspeed');?><?php echo $actual_speed;?></textarea>
    </div>
        <script>
        CKEDITOR.replace( 'actualspeed' );
        </script>

        <span style="color:red"><?php echo form_error('actualspeed');?></span>
        </div>
        <div class="col-md-12">
            <div style="text-align: center !important;">(Speed depends upon product behavior and laminate structure)</div>
        </div>
    </div>  
    </td>
    </tr>
   
    <tr>
    <td>Laminate specification </td>
    <td>
    <div class="row">
    <div class="col-md-4">
    <div class="form-group">
    <label>Reel Width (in mm)</label>
    <input type="text" class="form-control" name="laminatewidth" id="laminatewidth" value="<?php echo $leminate_specification;?>" required>
    <span style="color:red"><?php echo form_error('laminatewidth');?></span>
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
    <label>Max. Reel Dia</label>
    <input type="text" class="form-control" name="laminatereeldia" id="laminatereeldia" value="<?php echo $laminatereeldia;?>" required>
    <span style="color:red"><?php echo form_error('laminatereeldia');?></span>
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
    <label>Reel Core Dia </label>
    <input type="text" class="form-control" name="laminatereelcoredia" id="laminatereelcoredia" value="<?php echo $laminatereelcoredia;?>" required>
    <span style="color:red"><?php echo form_error('laminatereelcoredia');?></span>
    </div>
    </div>
    </div>

    </td>
    </tr>
   

    <?php 
   // $ppsize=explode('X',$pouch_size);
   // if(count($ppsize)>0)
   // {
   //  $pl=$ppsize[0];
   //  $pw=$ppsize[1];
   //  $ph=$ppsize[2];
   // }else
   // {
   //  $pl='';
   //  $pw='';
   //  $ph='';
   // }

    ?>
  <!--   <tr>
    <td>Pouch Size </td>
    <td>
    <div class="row">
    <div class="col-md-4"><input type="text" class="form-control" name="pouchsizew1" placeholder="W" id="pouchsizew1" value="<?php //echo $pl;?>" required>
    <span style="color:red"><?php //echo form_error('pouchsizew1');?></span></div>
    <div class="col-md-4"><input type="text" class="form-control" name="pouchsizel1" placeholder="L" id="pouchsizel1" value="<?php //echo $pw;?>" required>
    <span style="color:red"><?php //echo form_error('pouchsizel1');?></span></div>
    <div class="col-md-4"><input type="text" class="form-control" name="pouchsizeh1" placeholder="H" id="pouchsizeh1" value="<?php //echo $ph;?>" required>
    <span style="color:red"><?php //echo form_error('pouchsizeh1');?></span></div>
    </div>

    </td>
    </tr> -->
    <!-- <tr style="display:none;">
    <td>Sealing drives</td>
    <td> <input type="text" class="form-control" name="sealingdrives" id="sealingdrives" value="<?php //echo set_value('sealingdrives');?>" required>
    <span style="color:red"><?php //echo form_error('sealingdrives');?></span>
    </td>
    </tr> -->
   <!--  <tr style="display:none;">
    <td>Perforation and cutting </td>
    <td> <input type="text" class="form-control" name="perforationandcutting" id="perforationandcutting" value="<?php //echo set_value('perforationandcutting');?>" required>
    <span style="color:red"><?php //echo form_error('perforationandcutting');?></span>
    </td>
    </tr> -->
    <!-- <tr style="display:none">
    <td>Laminate Draw Off system </td>
    <td> <input type="text" class="form-control" name="laminatedrawoffsystem" id="laminatedrawoffsystem" value="<?php //echo set_value('laminatedrawoffsystem');?>" required>
    <span style="color:red"><?php //echo form_error('laminatedrawoffsystem');?></span>
    </td>
    </tr> -->
   <!--  <tr style="display:none;">
    <td>Laminate tracking system </td>
    <td> <input type="text" class="form-control" name="laminatetrackingsystem" id="laminatetrackingsystem" value="<?php //echo set_value('laminatetrackingsystem');?>" required>
    <span style="color:red"><?php //echo form_error('laminatetrackingsystem');?></span></td>

    </tr> -->
    <tr>
    <td>Electrical Spec. </td>
    <td> 
    <textarea class="form-control" name="electricalspec" required id="electricalspec"><?php echo $electrical_spec;?></textarea>
    <script>
    CKEDITOR.replace( 'electricalspec' );
    </script>

    <span style="color:red"><?php echo form_error('electricalspec');?></span>
    </td>
    </tr>
<?php 
$ldim=explode("<br>",$layout_dimensions);
if(count($ldim)>0)
{
$al=$ldim[0];
$bw=$ldim[1];
$ch=$ldim[2];
}else
{
$al='';
$bw='';
$ch='';
}
?>
    <tr>
    <td>Machine Layout Dimensions</td>
    <td> 
    <div class="row">
    <div class="col-md-4">
    <div class="form-group">
    <label>Length (in mm)</label>
    <input type="text" class="form-control" name="layoutdimensionslength" id="layoutdimensionslength" value="<?php echo $al;?>" required>
    <span style="color:red"><?php echo form_error('layoutdimensionslength');?></span>
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
    <label>Width (in mm)</label>
    <input type="text" class="form-control" name="layoutdimensionswidth" id="layoutdimensionswidth" value="<?php echo $bw;?>" required>
    <span style="color:red"><?php echo form_error('layoutdimensionswidth');?></span>
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
    <label>Height (in mm)</label>
    <input type="text" class="form-control" name="layoutdimensionsheight" id="layoutdimensionsheight" value="<?php echo $ch;?>" required>
    <span style="color:red"><?php echo form_error('layoutdimensionsheight');?></span>
    </div>
    </div>
    </div>

    </td>
    </tr>
    <tr>
    <td>Machine Weight (in kgs)</td>
    <td>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Net Weight</label>
                    <input type="text" class="form-control" name="netweight" id="netweight" value="<?php echo $machine_weight;?>" required>
                </div>
            </div>
             <div class="col-md-6">
                <div class="form-group">
                    <label>Gross Weight</label>
                    <input type="text" class="form-control" name="grossweight" id="grossweight" value="<?php echo $gross_weight;?>" required>
                </div>
            </div>
        </div>
    
    </td>
    </tr>
    <tr>
    <td>Compressed Air (in kgs)</td>
    <td>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Consumption (in CFM)</label>
                <input type="text" class="form-control" name="compressedaircfa" id="compressedaircfa" value="<?php echo $compressed_air;?>" required>
            </div>
        </div>
         <div class="col-md-6">
            <div class="form-group">
                <label>Operating Pressure (in BAR)</label>
                <input type="text" class="form-control" name="compressedairbar" id="compressedairbar" value="<?php echo $compressedairbar;?>" required>
            </div>
        </div>
    </div>
    </td>
    </tr>

    </tbody>
    </table>
    </div>
    </div>

<?php 
if($fillingimage<>'')
{
    $fill=1;

}else
{
    $fill=0;
   
}
?>
    <div class="row card-box" style="border:1px solid #000;">
        <div class="col-md-12">
            <h3 class="text-center page-title">MACHINE FILLING SYSTEM</h5>
               <div class="col-md-6">
                <div class="form-group">
                    <label>Machine Filling System Applicable?</label>
                     <input type="checkbox" name="machinefillingsystem" id="machinefillingsystem" onchange="checkmachinefillingsystem();" value="1" <?php if($fill==1){?> checked <?php } ?> >
            
            <script>
     
                function checkmachinefillingsystem(){
                  var d="<?php echo $fillingimage;?>";
            if($('#machinefillingsystem').is(':checked')){
                    $("#machinefillingsystemdiv").css('display','block');
                    if(d=='')
                    {
                    $("#machinefillingimg").attr('Required',true);
                    }else
                    {
                         $("#machinefillingimg").attr('Required',false);
                    }
                   
                 }else{
                    $("#machinefillingsystemdiv").css('display','none');
                     $("#machinefillingimg").attr('Required',false);
                 }  
                }
                
                
            
       
    </script>
                </div>

               
</div>
<div class="col-md-6" id="machinefillingsystemdiv" style="display: none;">
        <div class="form-group">
            <label>Attach Image</label>
            <input type="file" class="form-control" name="machinefillingimg" id="machinefillingimg" value="machinefillingimg"><br/>
            <?php 
            if($fill==1)
            {

                if(file_exists(UPLOADPATH.'opportunitydocs/'.$fillingimage))
                {
                ?>
                    <img src="<?php echo page_url1;?>image_bank/opportunitydocs/<?php echo $fillingimage;?>" width="50%">

            <?php

                }
            }
            ?>
        </div>
</div>

        </div>
    </div>



    <?php 
if($kldimage<>'')
{
    $fill=1;

}else
{
    $fill=0;
   
}
?>
    <div class="row card-box" style="border:1px solid #000;">
        <div class="col-md-12">
            <h3 class="text-center page-title">KEY LINE DIAGRAM</h5>
               <div class="col-md-6">
                <div class="form-group">
                    <label>KLD Filling System Applicable?</label>
                     <input type="checkbox" name="kld" id="kld" onchange="checkkld();" value="1" <?php if($fill==1){?> checked <?php } ?>>
            
            <script>
     
                function checkkld(){
                  var d="<?php echo $kldimage;?>";
            if($('#kld').is(':checked')){
                    $("#kldsystemdiv").css('display','block');
                    if(d=='')
                    {
                    $("#kldimg").attr('Required',true);
                    }else
                    {
                         $("#kldimg").attr('Required',false);
                    }
                   
                 }else{
                    $("#kldsystemdiv").css('display','none');
                     $("#kldimg").attr('Required',false);
                 }  
                }
                
                
            
       
    </script>
                </div>

               
</div>

<div class="col-md-6" id="kldsystemdiv" style="display: none;">
        <div class="form-group">
            <label>Attach Image</label>
            <input type="file" class="form-control" name="kldimg" id="kldimg" value=""><br/>
            <?php 
            if($fill==1)
            {

                if(file_exists(UPLOADPATH.'opportunitydocs/'.$kldimage))
                {
                ?>
                    <img src="<?php echo page_url1;?>image_bank/opportunitydocs/<?php echo $kldimage;?>" width="50%">

            <?php

                }
            }
            ?>
        </div>
</div>

        </div>
    </div>


    <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="text-center page-title">Electrical & Automation Parts Brand</h3>
    </div>
    <div class="col-md-12 table-responsive table-container">
    <table class="table table-bordered">
    <tbody>
    <?php 
    $rest=$this->db->select('id,name')->from('quote_parts_heading_master')->get();
    if($rest->num_rows()>0)
    {
        foreach($rest->result() as $row)
        {
    ?>
    <tr>
    <td><input type="hidden" name="brands[]" value="<?php echo $row->id;?>">
        <?php echo $row->name;?></td>
    <td>
        <select name="brand_data[]" class="form-control">
            <option value="">Select</option>
            <?php 
             $rest1=$this->db->select('id,name')->from('quote_parts_heading_master_options')->where('head_id',$row->id)->get();
    if($rest->num_rows()>0)
    {
        foreach($rest1->result() as $row1)
        {
            $resty=$this->db->select('value_id')->from('quotation_brand_data')->where('record_id',$record_id)->where('head_id',$row->id)->get();
            if($resty->num_rows()>0)
            {
                foreach($resty->result() as $rrrow);
                $valueid=$rrrow->value_id;
            }else{
                $valueid=0;
            }
    ?>
    <option value="<?php echo $row1->id;?>" <?php if($row1->id==$valueid){ ?> selected <?php } ?>><?php echo $row1->name;?></option>
    <?php 
        } 
        } 
        ?>
        </select>
    </td>
    </tr>
    <?php } } ?>

</tbody>
</table>
</div>
</div>

    <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="text-center page-title">Annexure- IV (Price Schedule)</h3>
    <h4 class="text-center">Line items below will be added as per the requirement</h4>
    </div>

    <div class="col-md-12 table-responsive table-container">

    <table class="table table-bordered" id="myTable">
    <thead>
    <tr>
    <th style="width:50%">Item description</th>
    <th style="width:10%">UOM</th>
    <th style="width:20%">Price</th>
    <th style="width:20%">Total Price (<span class="pricesymbol">USD</span>)</th>
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
    <td><input type="text" class="form-control mmodel" name="modelno" id="modelno" onkeyup="getmachinemodelno2();" placeholder="Model" required value="<?php echo $desc;?>">
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
    <br>
    Price of design, manufacturing, supply of machine as per technical specifications at Annexure-II<br></td>
    <td><input type="text" class="form-control" name="modelqty" id="modelqty" value="<?php echo $qty;?>" required onkeyup="calculatemodelprice();">
    <span style="color:red"><?php echo form_error('modelqty');?></span></td>
    <td><input type="text" class="form-control" name="modelprice" onkeyup="calculatemodelprice();" id="modelprice" value="<?php echo $price;?>" required></td>
    <td><input type="text" class="form-control" name="totalmodelprice" id="totalmodelprice" value="<?php echo $total_price;?>" required readonly>
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
    <select class="form-control descriptionProduct0" name="edittechdescriptioninfo<?php echo $id;?>" id="techdescriptioninfo<?php echo $id;?>" required>
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
    <input type="text" class="form-control" name="edittechdescqty<?php echo $id;?>" id="techdescqty<?php echo $id;?>" onkeyup="calculatepricing(<?php echo $id;?>);" value="<?php echo $qty;?>" required>
    </div>

    </td>
    <td>
        <div class="col-md-12">
            <div class="form-group">
                <label>Price</label>
                <input type="text" class="form-control" name="edittechdescprice<?php echo $id;?>" id="techdescprice<?php echo $id;?>" onkeyup="calculatepricing(0);" value="<?php echo $price;?>" required>
            </div>
        </div>
    </td>
    <td>
    <div class="row">
    <div class="col-md-9">
    <div class="form-group">
    <label>Total Price (<span class="pricesymbol">USD</span>)</label>
    <input type="text" class="form-control" name="edittechdesctotalprice<?php echo $id;?>" id="techdesctotalprice<?php echo $id;?>" value="<?php echo $total_price;?>" readonly required>

    </div>
    </div>
    <div class="col-md-3">
    <div class="form-group" style="margin-top:21px"><span class="btn btn-primary" id="deletesRow" onclick="deleteLineItem(<?php echo $id;?>);">-</span></a>
    </div>
    </div>
    </div>
    </td>
    </tr>
<?php } } ?>

<tr>
    <td colspan="4">
        <div class="col-md-12">
            <div class="form-group">
                <label>Add More Products</label><br/>
                <input type="checkbox" name="optin" value="1" id="optin" onchange="checkfornewoption();">
            </div>
        </div>

    </td>
</tr>
 <tr>
    <td>
    <div class="row newoptional" style="display: none;">
    <div class="col-md-12">
    <div class="form-group">
    <label>Description</label><br/>
    <select class="form-control  moreLineItems" name="techdescriptioninfo[]" id="techdescriptioninfo0">
        <option value="">Select Option</option>
        <?php $q = $this->db->select('id, instruments_name')->from('presto_instruments')->where('status',1)->where('type!=',0)->order_by('instruments_name','asc')->get(); if($q->num_rows()>0){ foreach($q->result() as $row){?>

            <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option>
        <?php } } ?>
        
    </select>
    </div>
    </div>
    </div>
    <td>
    <div class="col-md-12 newoptional" style="display: none;">

    <div class="form-group">
    <label>UOM</label>
    <input type="text" class="form-control" name="techdescqty[]" id="techdescqty0" onkeyup="calculatepricing(0);" value="">
    </div>

    </td>
    <td>
        <div class="col-md-12 newoptional"  style="display: none;">
            <div class="form-group">
                <label>Price</label>
                <input type="text" class="form-control" name="techdescprice[]" id="techdescprice0" onkeyup="calculatepricing(0);" value="">
            </div>
        </div>
    </td>
    <td>
    <div class="row newoptional"  style="display: none;">
    <div class="col-md-9">
    <div class="form-group">
    <label>Total Price (<span class="pricesymbol">USD</span>)</label>
    <input type="text" class="form-control" name="techdesctotalprice[]" id="techdesctotalprice0" value="" readonly>

    </div>
    </div>
    <div class="col-md-3 newoptional"  style="display: none;">
    <div class="form-group" style="margin-top:21px">
    <span class="btn btn-primary" id="addRow">+</span>
    </div>
    </div>
    </div>
    </td>
    </tr>

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
    $port = $othercharge['port'];
    
    //echo $forwarding_charges."<br/>".$packing_charges."<br/>".$insurance; exit;
}else
{

    $ftypedata='';
    $freight='';
    $freight_charges='';
    $packing_charges='';
    $forwarding_charges='';;
    $insurance='';
    $port = '';

}
?>

    <table class="table table-bordered">
    <tbody>

     <tr>
        <td>Freight Charges</td>
        <td>
            <select class="form-control" name="frightinfo" id="frightinfo" onchange="checkfreightinfo();" required>
                <option value="">Select Option</option>
                <option value="1" <?php if($freight==1){?> selected <?php } ?>>Extra at Actual</option>
                <option value="2" <?php if($freight==2){?> selected <?php } ?>>In Customer Scope</option>
                <option value="3" <?php if($freight==3){?> selected <?php } ?>>Additional</option>
            </select>
        </td>
        <script type="text/javascript">
            function checkfreightinfo(){
                var frightinfo = $("#frightinfo").val();
                if(frightinfo==3){
                    $("#freightamount").attr('disabled',false);
                    $("#freightamount").attr('Required',true);

                }else{
                    $("#freightamount").attr('disabled',true);
                    $("#freightamount").attr('Required',false);
                } 

                if(frightinfo==1)
                {
                  $("#freightType option[value!='EX WORKS']").prop("disabled",true);
                  $("#freightType").val('EX WORKS');
                }else
                {
                    $("#freightType option").prop("disabled",false);
                }

                getPort();
            }
        </script>

         <td>
            <div class="col-md-6">
            <select class="form-control" name="freightType" id="freightType" required onchange="getPort();">
            <option value="">Select Freight Type</option>
            <option value="EX WORKS" <?php if($ftypedata=="EX WORKS"){?> selected <?php } ?>>EX WORKS</option>
            <option value="FOB"  <?php if($ftypedata=="FOB"){?> selected <?php } ?>>FOB</option>
            <option value="CIF"  <?php if($ftypedata=="CIF"){?> selected <?php } ?>>CIF</option>
            <option value="CFR"  <?php if($ftypedata=="CFR"){?> selected <?php } ?>>CFR</option>
            <option value="DAP"  <?php if($ftypedata=="DAP"){?> selected <?php } ?>>DAP</option>
            </select>
            </div>
            <div class="col-md-6">
            <select class="form-control port" name="port" id="port" disabled>
            <option value="">Select Port</option>
            <?php 
            $re=$this->db->select('id,name')->from('ports')->get();
            if($re->num_rows()>0)
            {
                foreach($re->result() as $row)
                {

            ?>
            <option value="<?php echo $row->id;?>" <?php if($port==$row->id){echo "selected";}?> ><?php echo $row->name;?></option>
            <?php } }?>
            </select>
            </div>
        </td>
        <script type="text/javascript">
            function getPort()
            {
                var freightType= $("#freightType").val();
                $("#port").attr('disabled',true);
                    $("#port").attr('required',false);
                if(freightType=="FOB" || freightType=='CFR' || freightType=='CIF')
                {
                
                    $("#port").attr('disabled',false);
                    $("#port").attr('required',true);
                }

            }
        </script>

        <td>
            <input type="text" class="form-control" name="freightamount" placeholder="Freight Charges" id="freightamount" disabled value="<?php echo $freight_charges;?>">
        </td>
    </tr>

    <tr>
        <td>Packaging</td>
        <td><input type="checkbox" name="packagingapplicable" id="packagingapplicable" onchange="packingcheck();" value="1" <?php if($packing_charges>0){?> checked <?php } ?>>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                function packingcheck(){
                if($('#packagingapplicable').is(':checked')){
                        $("#packagingpercentage").attr('readonly',true);
                        var packingcharge = "2.5%";
                        $("#packagingpercentage").val(packingcharge);
                     }else{
                        $("#packagingpercentage").attr('readonly',true);
                        $("#packagingpercentage").val('');
                     }  
                    }
    </script>
        </td>
        <td colspan="2"><input type="text" class="form-control" name="packagingpercentage" id="packagingpercentage" placeholder="Packaging Charges"></td>
    </tr>

    <tr>
        <td>Forwarding</td>
        <td><input type="checkbox" name="forwardingapplicable" id="forwardingapplicable" onchange="forwardingcheck();" value="1" <?php if($forwarding_charges>0){?> checked <?php } ?>>
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
        <td colspan="2"><input type="text" class="form-control" name="forwardingpercentage" id="forwardingpercentage" placeholder="Forwarding Charges"></td>
    </tr>

    <tr>
        <td>Insurance</td>
        <td><input type="checkbox" name="insuranceapplicable"  <?php if($insurance>0){?> checked <?php } ?> id="insuranceapplicable" onchange="insurancecheck();" value="1">
            
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
        <td colspan="2"><input type="text" class="form-control" name="insurancepercentage" id="insurancepercentage" placeholder="Insurance Charges" value="<?php echo $insurance;?>"></td>
    </tr>

    </tbody>

    </table>



   <table class="table table-bordered" id="myTableoptional">
    <thead>
    <tr>
    <th colspan="3"><h4 class="text-center"><strong>Optional</strong></h4></th>

    </tr>
    </thead>


    <tbody>

        <?php 
$optional=$this->salescrm->getOptionalData($record_id);
if(count($optional)>0)
{
    foreach($optional as $option)
{
$desc=$option['description'];
$id=$option['id'];
$price=$option['price'];
$qty= $option['value'];
if($price<>'')
{
$price_data[]=$option['value']*$price;
$price=floatval($price);
$formatted_price = $price;
$formatted_price_total = $option['value']*$price;
}else
{
$formatted_price='';
$formatted_price_total='';
}
?>
    <tr id="optionalData<?php echo $id;?>">

    <td style="width:50%">
    <div class="row">
    <div class="col-md-12">
    <label>Product</label><br>
    <input type="hidden" name="optionaldatainfo[]" value="<?php echo $id;?>">
    <select class="form-control optionalselect" name="optionalitem<?php echo $id;?>" id="optionalitem<?php echo $id;?>">
        <option value="">Select Item</option>
        <?php $q = $this->db->select('id, instruments_name')->from('presto_instruments')->where('status',1)->order_by('instruments_name','asc')->get();
        if($q->num_rows()>0){
            foreach($q->result() as $row){?>
                <option value="<?php echo $row->id;?>" <?php if($desc==$row->id){ ?> selected <?php } ?>><?php echo $row->instruments_name;?></option>
          <?php  } }?>
    </select>

    </div>
    </div>


    </td>
    <td>
    <div class="row">
    <div class="col-md-12">
    <label>UOM</label>
    <input type="text" class="form-control" name="optionalqty<?php echo $id;?>" id="optionalqty<?php echo $id;?>" onkeyup="calculateoptionalitems(<?php echo $id;?>);" value="<?php echo $qty;?>">
    </div>
    </div>

    </td>
    <td>
    <div class="row">
    <div class="col-md-12">
    <label>PRICE</label>
    <input type="text" class="form-control" name="optionalprice<?php echo $id;?>" id="optionalprice<?php echo $id;?>" onkeyup="calculateoptionalitems(<?php echo $id;?>);" value="<?php echo $price;?>">
    </div>
    </div>

    </td>
    <script type="text/javascript">
        function calculateoptionalitems(i){
            var optionalqty = $("#optionalqty"+i).val();
            var optionalprice = $("#optionalprice"+i).val();
            if(optionalqty!=='' && optionalprice!==''){
                var optionaltotalprice = parseFloat(optionalqty)*parseFloat(optionalprice);
                $("#optionaltotalprice"+i).val(optionaltotalprice);
            }else{
                $("#optionaltotalprice"+i).val('');
            }
        }
    </script>
    <td>
    <div class="row">
    <div class="col-md-9">
    <label>Total Price (<span class="pricesymbol">USD</span>)</label>
    <input type="text" class="form-control" name="optionaltotalprice<?php echo $id;?>" id="optionaltotalprice<?php echo $id;?>" readonly value="<?php echo $formatted_price_total;?>">
    </div>
    <div class="col-md-3">
    <div class="form-group" style="margin-top: 21px;">
    <span class="btn btn-danger" id="" onclick="delete_op_data(<?php echo $id;?>)">-</span>
    </div>
    </div>
    </div>

    </td>

    </tr>

<?php } } ?>
<tr>
    <td colspan="4">
        <div class="col-md-12">
            <div class="form-group">
                <label>Add More Optional Products</label><br/>
                <input type="checkbox" name="optin1" value="1" id="optin1" onchange="checkfornewoption1();">
            </div>
        </div>

    </td>
</tr>

<tr>

    <td>
    <div class="row optionaldataifneeded" style="display:none;">
    <div class="col-md-12">
    <label>Product</label><br>
    <select class="form-control" name="optionalitem[]" id="optionalitem0" style="width: 500px;">
        <option value="">Select Item</option>
        <?php $q = $this->db->select('id, instruments_name')->from('presto_instruments')->where('status',1)->order_by('instruments_name','asc')->get();
        if($q->num_rows()>0){
            foreach($q->result() as $row){?>
                <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option>
          <?php  } }?>
    </select>

    </div>
    </div>


    </td>
    <td>
    <div class="row optionaldataifneeded" style="display:none;">
    <div class="col-md-12">
    <label>UOM</label>
    <input type="text" class="form-control" name="optionalqty[]" id="optionalqty0" onkeyup="calculateoptionalitems(0);">
    </div>
    </div>

    </td>
    <td>
    <div class="row optionaldataifneeded" style="display:none;">
    <div class="col-md-12">
    <label>PRICE</label>
    <input type="text" class="form-control" name="optionalprice[]" id="optionalprice0" onkeyup="calculateoptionalitems(0);">
    </div>
    </div>

    </td>
    <script type="text/javascript">
        function calculateoptionalitems(i){
            var optionalqty = $("#optionalqty"+i).val();
            var optionalprice = $("#optionalprice"+i).val();
            if(optionalqty!=='' && optionalprice!==''){
                var optionaltotalprice = parseFloat(optionalqty)*parseFloat(optionalprice);
                $("#optionaltotalprice"+i).val(optionaltotalprice);
            }else{
                $("#optionaltotalprice"+i).val('');
            }
        }
    </script>
    <td>
    <div class="row optionaldataifneeded" style="display:none;"> 
    <div class="col-md-9">
    <label>Total Price (<span class="pricesymbol">USD</span>)</label>
    <input type="text" class="form-control" name="optionaltotalprice[]" id="optionaltotalprice0" readonly value="">
    </div>
    <div class="col-md-3">
    <div class="form-group" style="margin-top: 21px;">
    <span class="btn btn-primary" id="addoptionalcharges">+</span>
    </div>
    </div>
    </div>

    </td>

    </tr>
</tbody>

    </table>


    </div>
    </div>

    <?php 
        $checked = '';
        $distype = '';
        $disper = '';
        $disamt = '';
    $q99 = $this->db->select('*')->from('quotation_discount_info')->where('record_id',$record_id)->get();
    //echo "<pre>"; print_r($q99->result()); exit;
    if($q99->num_rows()>0){
        $checked = 'checked="checked"';
        foreach($q99->result() as $discountdatainfo);
        $distype = $discountdatainfo->discount_type;
        $disper = $discountdatainfo->discount_in_percent;
        $disamt = $discountdatainfo->discount_in_value;
    }else{
        $checked = '';
        $distype = '';
        $disper = '';
        $disamt = '';

    }
    ?>
    <div class="row card-box">
        <div class="col-md-12">
            <div class="row">
                 <h3>Discount (If Applicable)</h3><hr>
                <div class="col-md-3">
                <label>Discount Applicable?</label> <br>   
            <input type="checkbox" name="discountapplicable" <?php echo $checked;?> id="discountapplicable" onchange="checkifdiscountapplicable();" value="1" ><br/>
            <script>
     
            function checkifdiscountapplicable(){
                  
            if($('#discountapplicable').is(':checked')){
                    $("#discountapplicablediv").css('display','block');
                    }else{
                   $("#discountapplicablediv").css('display','');
                 }  
                }
                </script>

                </div>
                <div <?php if($q99->num_rows()>0){}else{?> style="display:none;"<?php }?> id="discountapplicablediv">
                <div class="col-md-9">
                   <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Discount Type</label>
                            <select class="form-control" name="discunttype" id="discunttype" onchange="checkdiscounttypeselection();">
                                <option value="" <?php if($distype==''){echo "selected";}?>>Select Discount Type</option>
                                <option value="1" <?php if($distype==1){echo "selected";}?>>In Percent(%)</option>
                                <option value="2" <?php if($distype==2){echo "selected";}?>>Fix Discount</option>
                            </select>
                        </div>
                    </div>
                    <script type="text/javascript">
                        function checkdiscounttypeselection(){
                            var discunttype = $("#discunttype").val();
                            if(discunttype==1){
                                $("#discountinamount").attr('disabled',true);
                                $("#discountinpercent").attr('disabled',false);
                            }else if(discunttype==2){
                                $("#discountinpercent").attr('disabled',true);
                                $("#discountinamount").attr('disabled',false);
                            }else{
                                $("#discountinpercent").attr('disabled',false);
                                $("#discountinamount").attr('disabled',false);
                            }
                        }
                    </script>
                    <div class="col-md-4">
                    <div class="form-group">
                        <label>Discount in Percent(%)</label>
                    <input type="number" class="form-control" <?php if($disper<>''){}else{?>disabled<?php }?> name="discountinpercent" id="discountinpercent" value="<?php echo $disper;?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Fix Discount</label>
                         <input type="number" class="form-control" <?php if($disamt<>''){}else{?>disabled<?php }?> name="discountinamount" id="discountinamount" value="<?php echo $disamt;?>">
                    </div>
                </div>
                   </div>
                </div>
            </div>
                
            </div>
            

        </div>

    </div>

    <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="text-center page-title">Other Information</h5>

    </div>

    <div class="col-md-12 table-responsive table-container">
    <table class="table table-bordered">
    <thead>
    <tr>
    <th>S.no.</th>
    <th>Item description</th>
    <th>Detail</th>

    </tr>
    </thead>
    <tbody>
   <?php
   $odata=$this->salescrm->getotherinformation($record_id);
//echo "<pre>"; print_r($odata); exit;
    if(count($odata)>0)
    {
    $terms_value=$odata[0];
    $delivery_value=$odata[1];
    $pocket_expense=$odata[2];
    $support_statement_value=$odata[3];
    }else
    {
    $terms_value='';
    $delivery_value='';
    $pocket_expense='';
    $support_statement_value='';
    }

?>
    <tr>
    <td>A </td>
    <td>Terms of Payments</td>
    <td><div class="row">
        <div class="col-md-12">
            <div class="form-group">
                
                <select class="form-control" id="paymentterms" name="paymentterms" required>
                    <option value="">Select Payment Term</option>
                    <?php 
                    $q = $this->db->select('id, payment_terms')->from('payment_terms')->where('status',1)->get();
                    foreach($q->result() as $row){
                    ?>
                    <option value="<?php echo $row->id;?>" <?php if($terms_value==$row->id){?> selected <?php } ?>><?php echo $row->payment_terms;?></option>
                <?php }?>
                </select>
            </div>
        </div>
    </div>
    </td>


    </tr>

    <tr>
    <td>B</td>
    <td>Delivery</td>
    <td><textarea name="delivery" id="delivery" class="form-control" required><?php echo $delivery_value;?></textarea>
    <span style="color:red"><?php echo form_error('delivery');?></span>

    <script>
    CKEDITOR.replace( 'delivery' );
    </script>

    </td>


    </tr>

    <tr>
    <td>C</td>
    <td>Engineer Expense</td>
    <td><input type="number" min="1" name="out_of_pocket_expense" id="out_of_pocket_expense" class="form-control" value="<?php echo $pocket_expense;?>" required>
    <span style="color:red"><?php echo form_error('out_of_pocket_expense');?></span>
    </td>


    </tr>



    <tr style="display:none">
    <td>D</td>
    <td>Support Statement</td>
    <td>
    <textarea name="support_statement" id="support_statement" class="form-control">Shubham Pack will Provide Local Engineer Support in INDONESIA. Shubham Pack is planning for an office in INDONESIA to cater demand of service and spares.</textarea><span style="color:red"><?php echo form_error('support_statement');?></span></td>


    </tr>

    </tbody>
    </table>
    </div>
    </div>

    <div class="row card-box">
        <div class="col-md-12">
            <h3>Annexure-VI</h3>
        </div>
        <div class="col-md-12">
             <table class="table table-bordered">
    <thead>
    <tr>
    <th>Description</th>
    <th>Detail</th>

    </tr>
    </thead>
    <tbody>
    <?php
    $layout=0;
    $layoutimage=$CI->salescrm->get_quotation_layout_img($record_id);
    if($layoutimage<>'')
{
    if(file_exists(UPLOADPATH.'opportunitydocs/layoutimg/'.$layoutimage))
    {
        $layout=1;
    }
}
    ?>
           <tr>
        <td>Layout Applicable <input type="checkbox" name="layoutapplicable" id="layoutapplicable" onchange="checklayoutapplicable();" value="1" <?php if($layout==1){?> checked  <?php } ?>><br/>
           
            
            <script>
     
                function checklayoutapplicable(){
                  var l="<?php echo $layoutimage;?>";
            if($('#layoutapplicable').is(':checked')){
                    $("#layoutdiv").css('display','block');
                    if(l=='')
                    {
                    $("#uploadlayout").attr('Required',true);
                    }else
                    {
                    $("#uploadlayout").attr('Required',false);
                    }
                   
                 }else{
                    $("#layoutdiv").css('display','none');
                    $("#uploadlayout").attr('Required',false);
                 }  
                }
                </script></td>
        

        <td>
            <div class="row" id="layoutdiv" style="display:none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Upload Layout</label>
                        <input type="file" class="form-control" name="uploadlayout" id="uploadlayout" value=""><br/>
                          <?php 
            if($layout==1)
            {

                if(file_exists(UPLOADPATH.'opportunitydocs/layoutimg/'.$layoutimage))
                {
                ?>
                    <img src="<?php echo page_url1;?>image_bank/opportunitydocs/layoutimg/<?php echo $layoutimage;?>" width="30%">

            <?php

                }
            }
            ?>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
        </div>
    </div>

<?php 
$optional=$this->salescrm->getConsumableData($record_id);
?>

     <div class="row card-box">
        <div class="col-md-12">
            <h3>Annexure-VII <br></h3>
            <span>1 YEAR CONSUMABLE SPARES &nbsp; &nbsp; <input type="checkbox" name="consumablespare" id="consumablespare" onchange="checkconsumablespare();" value="1" <?php if(count($optional)>0){?> checked <?php } ?>>
            <script>
     
                function checkconsumablespare(){
                  
            if($('#consumablespare').is(':checked')){
                    $("#consumablesparetable").css('display','block');
                    //$("#partname0").attr('Required',true);
                    $("#spareqty0").attr('Required',true);
                    $("#spareprice0").attr('Required',true);
                    
                   
                 }else{
                    $("#consumablesparetable").css('display','none');
                    //$("#partname0").attr('Required',false);
                    $("#spareqty0").attr('Required',false);
                    $("#spareprice0").attr('Required',false);
                    
                 }  
                }
                
                
            
       
    </script></span>
        </div>
        <div class="col-md-12" id="consumablesparetable" style="display: none;">
             <table class="table table-bordered" id="myTableconsumablesparetable">
    <thead>
    <tr>
    <th>UOM (IN SETS)</th>
    <th>PRICE/SET</th>
    </tr>
    </thead>
    <tbody>

        <?php 
        // echo "<pre>";print_r($optional); exit;
        if(count($optional)>0)
        {
        foreach($optional as $option)
        {
        $price=$option['price'];
        $qty=$option['value'];
        $id=$option['id'];
        ?>
           <tr>
           <!-- <td>
            <div class="col-md-12">
                <div class="form-group">
                    <select class="form-control" name="partname[]" id="partname0" style="width: 100%;">
                        <option value="">Select Part</option>
                        <?php 
                        //$q = $this->db->select('id, spare_name')->from('consumablespares')->order_by('spare_name','asc')->get();
                        //foreach($q->result() as $row){
                            //$spr = str_replace('.', ' ', $row->spare_name); 
                        ?>
                        <option value="<?php //echo $row->id;?>"><?php //echo $spr;?></option>
                    <?php //}?>
                    </select>
                </div>
            </div>
        </td> -->
        <td>
            <div class="col-md-12">
                <div class="form-group">
                    <input type="hidden" name="consumanbleid[]" value="<?php echo $id;?>">
                    <input type="number" class="form-control" name="spareqty<?php echo $id;?>" id="spareqty<?php echo $id;?>" placeholder="No. Of Set" onkeyup="calculatepricingSpare(0);" value="<?php echo $qty;?>">
                </div>
            </div>
        </td>

         <td>
            <div class="col-md-12">
                <div class="form-group">
                    <input type="number" class="form-control" name="spareprice<?php echo $id;?>" id="spareprice<?php echo $id;?>" placeholder="Price/SET" min="0" onkeyup="calculatepricingSpare(0);" value="<?php echo $price;?>">
                </div>
            </div>
        </td>

       <!--  <td>
            <div class="col-md-12">
                <div class="form-group">
                    <input type="number" class="form-control" id="SparetotalPrice0" readonly value="" placeholder="Total Price" min="0">
                </div>
            </div>
        </td> -->
        

        

     <!--    <td>
          <span class="btn btn-primary" id="addcosumablepartscharges">+</span>
        </td> -->
    </tr>

<?php } } ?>
    </tbody>
</table>
        </div>
    </div>

   <!--  <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
    <h3 class="text-center page-title">Next Followup Information</h3>
    </div>
    <div class="col-md-4"></div>
    <div class="col-md-4">
    <div class="form-group">
    <label>Next Followup Date <span style="color:red">*</span></label>
    <input type="date" name="followDate" id="followDate" class="form-control" value="<?php echo date('Y-m-d',strtotime('+1 days'));?>"readonly required>
    <span style="color:red"><?php echo form_error('followDate');?></span>
    </div>
    </div>

    </div> -->
    <div class="row card-box">
        <div class="col-md-12">
            <div class="form-group">
                <label>Reason for Change <span style="color:red;">*</span></label>
                <textarea class="form-control" name="reasonforchange" id="reasonforchange" required></textarea>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 text-center">
    <div class="form-group">
    <label>&nbsp;</label>
    <input type="submit" id="businessupdate" class="btn btn-success" value="Update Quote" style="width:60%">
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
<script>
$( document ).ready(function() {
    getPort();
checkmachinefillingsystem();
checkkld();
checkfreightinfo();
packingcheck();
forwardingcheck();
insurancecheck();
checklayoutapplicable();
checkconsumablespare();
$('#optionalitem0').select2({ tags:true });
$('.optionalselect').select2({});
$('.moreLineItems').select2({ tags:true });
$('.port').select2({ tags:true });
$(".descriptionProduct0").select2({});
});

$( document ).ready(function() {
$('#partname0').select2({ tags:true });
});

</script>
    <script>
    $(document).ready(function(){
    //$("#loginForm :input").attr('required',false);
    $("#businessupdate").attr('disabled',false);
    $("#businessupdate").val('Update');
    $("#loginForm").on("submit", function(){
    // $("#pageloader").fadeIn();
    $("#businessupdate").attr('disabled',true);
    $("#businessupdate").val('Please Wait...');
    });//submit
    });//document ready
    </script>


    <script>
    $( document ).ready(function() {
    $('#example').dataTable({
    "bProcessing": true,
    "pagination":true,
    "sAjaxSource": "<?php echo page_url;?>Master/Business_location/business_loc_listing",
    "aoColumns": [
    { mData: 'sr_no' } ,
    { mData: 'country_name' },
    { mData: 'state_name' },
    { mData: 'city_name' },
    { mData: 'company_name' },
    { mData: 'address' },
    { mData: 'contact_number' },
    { mData: 'status' },
    { mData: 'edit' }

    ]
    });   
    });

    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script language="javascript" type="text/javascript">   
    jQuery.noConflict();
    $(document).ready(function() {
    $("#businessupdate").click(function() {
    var country_name = $("#country_name").val();
    if(country_name=='')
    {
    $("#error_name").html('Required!');
    } else {
    $("#error_name").html('');
    }
    var state = $("#state").val();
    if(state=='')
    {

    $("#error_state").html('Required!');
    } else {
    $("#error_state").html('');
    }

    var status = $("#status").val();
    if(status=='')
    {

    $("#status_error").html('Required!');
    } else {
    $("#status_error").html('');
    }

    var city_name = $("#city_name").val();
    if(city_name=='')
    {

    $("#error_city").html('Required!');
    } else {
    $("#error_city").html('');
    }

    var company_name = $("#company_name").val();
    if(company_name=='')
    {

    $("#error_company").html('Required!');
    } else {
    $("#error_company").html('');
    }

    var address = $("#address").val();
    if(address=='')
    {

    $("#address_error").html('Required!');
    } else {
    $("#address_error").html('');
    }

    var contact_number = $("#contact_number").val();
    if(contact_number=='')
    {

    $("#contact_error").html('Required!');
    } else {
    $("#contact_error").html('');
    }

    if(country_name=='' || state=='' || city_name=='' || company_name=='' || address=='' || contact_number=='' || status=='')
    {

    return false;
    }

    });
    });

    function change_currency()
    {
    var cur=$("#cur option:selected").text();
    $(".pricesymbol").text(cur);
    }


    </script>
    <script type="text/javascript">
    $(document).ready(function(){
    change_currency();
    var i=1;
    $('#addmore_btn1').click(function(){
    i++;

    $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-11"><div class="form-group"><label>Technical Specification</label><input type="text" class="form-control" name="technicalspec[]" id="technicalspecs" required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:20px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
    change_currency();

    });


    $(document).on('click', '.btn_remove', function(){
    var button_id = $(this).attr("id");
    $('#row'+button_id+'').remove();
    });

    });
    </script>

    <script>
    $(document).ready(function(){
    var i = 1;
    $('#addRow').click(function(){
    var newRow = $('<tr>');
    newRow.append('<td><div class="row newoptional"><div class="col-md-12"><div class="form-group"><label>Description</label><br/><select class="form-control  descriptionProduct'+i+'" name="techdescriptioninfo[]" id="techdescriptioninfo'+i+'"><option value="">Select Option</option><?php $q = $this->db->select('id, instruments_name')->from('presto_instruments')->where('status',1)->where('type!=',0)->order_by('instruments_name','asc')->get(); if($q->num_rows()>0){ foreach($q->result() as $row){?><option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option><?php } } ?></select> </div></div></div></td>');
    newRow.append('<td><div class="row newoptional"><div class="col-md-12"><div class="form-group"><label>UOM</label><input type="text" class="form-control" name="techdescqty[]" id="techdescqty'+i+'" onkeyup="calculatepricing('+i+');" required value=""></div></div></td>');
    newRow.append('<td><div class="row newoptional"><div class="col-md-12"><div class="form-group"><label>Price</label><input type="text" class="form-control" name="techdescprice[]" id="techdescprice'+i+'" onkeyup="calculatepricing('+i+');" required value=""></div></div></td>');
    newRow.append('<td><div class="row newoptional"><div class="col-md-9"><div class="form-group"><label>Total Price (<span class="pricesymbol">USD</span>)</label><input type="text" class="form-control" name="techdesctotalprice[]" id="techdesctotalprice'+i+'" readonly value="" required></div></div><div class="col-md-3"><div class="form-group" style="margin-top:21px"><button class="removeRow btn-danger btn-xs">X</button></div></div></td>');
    $('#myTable tbody').append(newRow);
    intialzeselect2commonWithTags("descriptionProduct"+i);
    //CKEditorChange('techdescriptioninfo'+i);  
    change_currency();
    i++;
    });
    $('#myTable').on('click', '.removeRow', function(){
    $(this).closest('tr').remove();
    change_currency();
    });
    });
    </script>
    <script type="text/javascript">
    function CKEditorChange(name) {
    CKEDITOR.replace(name);
    }
    </script>
    <script>
    $(document).ready(function(){
    var i=1;
    $('#addoptionalcharges').click(function(){
    var newRow = $('<tr>');
    newRow.append('<td><div class="row optionaldataifneeded"><div class="col-md-12"><label>Product</label> <br> <select class="form-control optionalItems'+i+'" name="optionalitem[]" id="optionalitem'+i+'"><option value="">Select Item</option><?php $q = $this->db->select('id, instruments_name')->from('presto_instruments')->where('status',1)->order_by('instruments_name','asc')->get(); if($q->num_rows()>0){ foreach($q->result() as $row){?>
        <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option><?php  } }?>
    </select></div></div></td>');
    newRow.append('  <td><div class="row optionaldataifneeded"><div class="col-md-12"><label>UOM</label><input type="text" class="form-control" name="optionalqty[]" id="optionalqty'+i+'" onkeyup="calculateoptionalitems('+i+');" ></div></div></td>');
     newRow.append('  <td><div class="row optionaldataifneeded"><div class="col-md-12"><label>Price</label><input type="text" class="form-control" name="optionalprice[]" id="optionalprice'+i+'" onkeyup="calculateoptionalitems('+i+');"></div></div></td>');
    newRow.append('<td> <div class="row optionaldataifneeded"><div class="col-md-9"><label>Total Price (<span class="pricesymbol">USD</span>)</label><input type="text" class="form-control" name="optionaltotalprice[]" readonly id="optionaltotalprice'+i+'" value=""></div><div class="col-md-3"><div class="form-group" style="margin-top:21px"><button class="removeRow btn btn-danger">X</button></div></div></div></td>');
    $('#myTableoptional tbody').append(newRow);
    intialzeselect2commonWithTags('optionalItems'+i); 
    //CKEditorChange('optionaldescription'+i); 
    change_currency();
    i++;

    });
    $('#myTableoptional').on('click', '.removeRow', function(){
    $(this).closest('tr').remove();
    change_currency();
    });
    });

  function intialzeselect2(i){
    $('#optionalitem'+i).select2({ tags:true });
 }


  function intialzeselect2common(obj){
    $('.'+obj).select2({});
 }

 function intialzeselect2commonWithTags(obj){
    $('.'+obj).select2({ tags:true });
 }
    </script>

<?php $q2 = $this->db->select("id, spare_name")->from("consumablespares")->order_by("spare_name","asc")->get(); ?>
    <script>
    $(document).ready(function(){
    var i=1;
    $('#addcosumablepartscharges').click(function(){
    var newRow = $('<tr>');
    newRow.append('<td><div class="col-md-12"><div class="form-group"><select class="form-control" name="partname[]" id="partname'+i+'" style="width: 100%;"><option value="">Select Part</option><?php foreach($q2->result() as $row){ ?><option value="<?php echo $row->id;?>"><?php echo trim($row->spare_name);?></option><?php } ?></select></div></div></td>');

    newRow.append('<td><div class="col-md-12"><div class="form-group"><input type="number" class="form-control" name="spareqty[]" id="spareqty'+i+'" onkeyup="calculatepricingSpare('+i+')" value="" placeholder="Spare Qty"></div></div></td><td><div class="col-md-12"><div class="form-group"><input type="number" class="form-control" name="spareprice[]" id="spareprice'+i+'" value="" placeholder="Spare Price" onkeyup="calculatepricingSpare('+i+')" min="0"></div></div></td><td><div class="col-md-12"><div class="form-group"><input type="number" class="form-control" id="SparetotalPrice'+i+'" readonly value="" placeholder="Total Price" min="0"></div></div></td>');
     newRow.append('  <td><div class="col-md-12"><div class="form-group"><button class="removeRow btn btn-danger">X</button></div></div></td>');
   
    $('#myTableconsumablesparetable tbody').append(newRow);
    intialzeselect2(i); 
    //CKEditorChange('optionaldescription'+i); 
    
    i++;
    change_currency();
    });
    $('#myTableconsumablesparetable').on('click', '.removeRow', function(){
    $(this).closest('tr').remove();
    change_currency();
    });
    });

  function intialzeselect2(i){
    $('#partname'+i).select2({ tags:true });
 }
    </script>

    <script>
    $(document).ready(function(){
    var i=1;
    $('#addmorecharges').click(function(){
    var newRow = $('<tr>');
    newRow.append('<td><div class="col-md-12"><div class="form-group"><label>Description</label><textarea class="form-control" name="othercharges[]" id="othercharges'+i+'" required></textarea></div></div></td>');
    newRow.append('<td><div class="col-md-12"><div class="form-group"><label>UOM</label><input type="text" class="form-control" name="otherchargesqty[]" id="otherchargesqty" value="" required></div></td>');
    newRow.append('<td> <div class="row"><div class="col-md-9"><label>Total Price (<span class="pricesymbol">USD</span>)</label><input type="text" class="form-control" name="otherchargesprice[]" id="otherchargesprice" value="" required></div><div class="col-md-3"><div class="form-group" style="margin-top:21px"><button class="removeRow btn btn-danger">X</button></div></div></div></td>');
    $('#myTableothercharges tbody').append(newRow);
    CKEditorChange('othercharges'+i);  
    i++;
    change_currency();
    });
    $('#myTableothercharges').on('click', '.removeRow', function(){
    $(this).closest('tr').remove();
    change_currency();
    });
    });
    </script>

    <script type="text/javascript">
    $(document).ready(function(){
    var i=2;
    $('#addmore_btn2').click(function(){

    $('#dynamictasks2').append('<tr id="row'+i+'"><td>'+i+'</td><td><div class="row"><div class="col-md-12"><div class="form-group"><label>Description</label><textarea class="form-control" name="auger_filling_system" id="auger_filling_system" placeholder=""</textarea></div></div></div><td><div class="col-md-12"><div class="form-group"><label>UOM</label><input type="text" class="form-control" name="auger_filling_system_qty" id="auger_filling_system_qty" value=""></div></td><td><div class="col-md-10"><div class="form-group"><label>Price</label><input type="text" class="form-control" name="auger_filling_system_price" id="auger_filling_system_price" value=""></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:20px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></td></tr><br/>');

    i++;
    change_currency();
    });


    $(document).on('click', '.btn_remove', function(){
    var button_id = $(this).attr("id");
    $('#row'+button_id+'').remove();
    change_currency();
    });

    });
    </script>
    <script type="text/javascript">
    
    $(document).ready(function(){
    $("#businessupdate").attr('disabled',false);
    $("#businessupdate").val('Submit');
    $("#loginForm").on("submit", function(){
    // $("#pageloader").fadeIn();
    $("#businessupdate").attr('disabled',true);
    $("#businessupdate").val('Please Wait...');
    });//submit
    });//document ready
    
    </script>
 
     <script type="text/javascript">
        function calculatepricingSpare(i) {
            var techdescqty = $("#spareqty"+i).val();
            var techdescprice = $("#spareprice"+i).val();
            if(techdescqty!='' && techdescprice!=''){
                var grandtotalamount = parseFloat(techdescqty)*parseFloat(techdescprice);
                $("#SparetotalPrice"+i).val(grandtotalamount);
            }else{
               
               $("#SparetotalPrice"+i).val(''); 
            }
        }
    </script>

 <script>
            function checkfornewoption()
            {
                $(".newoptional").css('display','none');
                $(".newoptional :input").attr('required',false);
                if($('#optin').is(":checked"))
                {
                    $(".newoptional").css('display','');
                    $(".newoptional :input").attr('required',true);
                }
            }
        </script>

        <script>
            function checkfornewoption1()
            {
                $(".optionaldataifneeded").css('display','none');
                $(".optionaldataifneeded :input").attr('required',false);
                if($('#optin1').is(":checked"))
                {
                    $(".optionaldataifneeded").css('display','');
                    $(".optionaldataifneeded :input").attr('required',true);
                }
            }

            function delete_technical_specs(id)
            {
                if(confirm('Do you really want to delete'))
                {
                        $.ajax({
                        type:"post",
                        url:"<?php echo page_url;?>Opportunity/deletespecification",
                        data:"id="+id,
                        success:function(data){
                        $("#technical"+id).remove();
                        }
                        });

                }
            
            }


            function checkfornewoption1()
            {
                $(".optionaldataifneeded").css('display','none');
                $(".optionaldataifneeded :input").attr('required',false);
                if($('#optin1').is(":checked"))
                {
                    $(".optionaldataifneeded").css('display','');
                    $(".optionaldataifneeded :input").attr('required',true);
                }
            }

            function delete_axis_specs(id)
            {
                if(confirm('Do you really want to delete'))
                {
                        $.ajax({
                        type:"post",
                        url:"<?php echo page_url;?>Opportunity/deleteaxisspecification",
                        data:"id="+id,
                        success:function(data){
                        $("#axisDetails"+id).remove();



                        getcount();


                        }
                        });

                }
            
            }

            function getcount()
            {

                $.ajax({
                        type:"post",
                        url:"<?php echo page_url;?>Opportunity/getAxisCount",
                        data:"record_id=<?php echo $record_id;?>",
                        success:function(data){
                        $("#noofaxisinmachine").val(data);
                        }
                        });
            }

            function deleteLineItem(id)
            {
                 $.ajax({
                        type:"post",
                        url:"<?php echo page_url;?>Opportunity/deleteLineItem",
                        data:"id="+id,
                        success:function(data){
                        $("#LineItem"+id).remove();
                        }
                        });

            }

            function delete_op_data(id)
            {
                 $.ajax({
                        type:"post",
                        url:"<?php echo page_url;?>Opportunity/delete_op_data",
                        data:"id="+id,
                        success:function(data){
                        $("#optionalData"+id).remove();
                        }
                        });
            }

            function addmoreprdpacked()
            {
                if($('#addMorePRDPacked').is(":checked"))
                {
                    $("#MorePRDPacked").css('display','');
                    $("#MorePRDPacked :input").attr('required',true);
                }else
                {
                     $("#MorePRDPacked").css('display','none');
                    $("#MorePRDPacked :input").attr('required',false);
                }

            }


        </script>
        <script type="text/javascript">
            $(document).ready(function(){
            var i=2;
            $('.addPouches').click(function(){
            $('#dynamicPouches').append('<div class="row" style="padding-top:30px;" id="additionalPouches'+i+'"><div class="col-md-12"><div class="col-md-3"><input type="text" class="form-control numbers-only" name="qtytobepacked[]" id="qtytobepacked'+i+'" value=""  placeholder="Packing Qty" required><span style="color:red"></span></div><div class="col-md-3"><select class="form-control" name="qty_unit[]" id="qty_unit'+i+'" required><option value="">Select Qty</option><option value="ml">ml</option><option value="gm">gm</option></select></div><div class="col-md-2"><input type="text" class="form-control numbers-only" onkeyup="getpouchsizew();" name="pouchsizew[]" placeholder="W" id="pouchsizew'+i+'" value="" required><span style="color:red"></span></div><div class="col-md-2"><input type="text" class="form-control numbers-only" onkeyup="getpouchsizel();" name="pouchsizel[]" id="pouchsizel'+i+'" placeholder="L" value="" required><span style="color:red"></span></div><div class="col-md-2"><input type="text" class="form-control numbers-only" onkeyup="getpouchsizel();" name="pouchsizeh[]" id="pouchsizeh'+i+'" placeholder="H" value="" required><span style="color:red"></span></div></div></div><div style="clear:both; height:20px"></div><div class="row"><div class="col-md-12"><div class="col-md-2"><input type="text" class="form-control collarfields numbers-only" onkeyup="getpouchsizel();" name="gusset[]" disabled id="gusset'+i+'" placeholder="GUSSET" value="" required><span style="color:red"></span></div><div class="col-md-2"><select class="form-control collarfields" disabled name="punch_hole[]" id="punch_hole'+i+'" required><option value="">Punch Hole</option><option value="Yes">Yes</option><option value="No">No</option></select></div><div class="col-md-1" style="margin-top:5px;"><a href="javascript:;" class="removePouches" id="'+i+'"><i class="fa fa-minus"></i></a></div></div></div>');
            checkmachinename();
            change_currency();
            i++;
            });


            $(document).on('click', '.removePouches', function(){
            var button_id = $(this).attr("id");
            $('#additionalPouches'+button_id+'').remove();
            change_currency();
            }); 
            });


            function checkmachinename()
            {
            $(".collarfields").attr('disabled',true);
            var mname=$("#machineName").val();
            if(mname=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || mname=="VFFS COLLAR TYPE MACHINE")
            {
            $(".collarfields").attr('disabled',false);
            }
            }

            function addmoreaxis()
            {

                if($('#more_axis').is(":checked"))
                {
                    $("#new_axis").css('display','');
                    $("#new_axis :input").attr('required',true);
                }else
                {
                     $("#new_axis").css('display','none');
                    $("#new_axis :input").attr('required',false);
                }

            }
        </script>
        <script type="text/javascript">
              $(document).ready(function(){
    var j=2;
    $('#addMoreAxis').click(function(){
   // alert('fi');
    $('#moreAxis').append('<div style="clear:both;"></div><div class="row" id="additionalAxis'+j+'"><div class="col-md-12"><div class="col-md-4"><div class="form-group"><label>Axis Details</label><input type="text" name="noofaxisinmachine[]" class="form-control" id="noofaxisinmachine'+j+'"></div></div><div class="col-md-4"><div class="form-group"><label>Count</label><input type="number" min="0" name="noofaxisincount[]" class="form-control" id="noofaxisincount'+j+'"></div></div><div class="col-md-2" style="margin-top: 25px;"><a href="javascript:;" class="removeMoreAxis" id="'+j+'"><i class="fa fa-minus"></i></a></div></div></div>');
    j++;
    change_currency();
    });


    $(document).on('click', '.removeMoreAxis', function(){
    var button_id = $(this).attr("id");
    $('#additionalAxis'+button_id+'').remove();
    change_currency();
    });

    });

    function selectProductPacked()
    {
    var qdata=$(".qtyunitdata").val();
    $("#product_packedUnit").html('<option value="'+qdata+'">'+qdata+'</option>');

    }
        </script>

    </body>
    </html>