<?php 
$CI=&get_instance();
$lead_id=$this->uri->segment(3);
$CI->load->model('Salescrm_model','salescrm');
$ldetails=$CI->salescrm->getLeadDetailsNew($lead_id);
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

    foreach($pdetails as $row1);
    $machineName=$row1->instruments_name;
}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                         <div class="btn-group pull-right">
                          
                               
                            </div>
                           
                            <h4 class="page-title text-center">You are Creating Quotation For <?php echo ucwords(strtolower($companyName));?> For Opportunity <?php echo $oppno;?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
                                   <form method="post" id="loginForm" action="<?php echo page_url;?>Opportunity/add_quote/<?php echo $this->uri->segment(3);?>">  
                                   <div class="row card-box" style="border:1px dotted #000;">
                                   <div class="col-md-12"  style="margin-bottom:10px;">
                                       <h3 class="page-title text-center">General Information</h3>
                                   </div>                               
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Ref No</label>
                                                <span id="contact_error" style="color:red;">*</span>
                                                 <input type="text" name="refno" id="refno" class="form-control"  value="<?php echo $oppno;?>" readonly style="color:black;font-weight:bold;" required >
                                                 <?php echo form_error('refno'); ?>
                                            </div>
                                        </div>
                                    <div class="col-md-2">
                                    <div class="form-group">
                                    <label>Date</label>
                                    <span id="contact_error" style="color:red;">*</span>
                                    <input type="date" class="form-control" name="quote_date" value="<?php echo set_value('quote_date');?>" required style="font-weight:bold;color:black;">
                                    <span style="color:red"><?php echo form_error('quote_date'); ?></span>

                                    </div>
                                    </div>

                                          <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Customer Name</label>
                                                <span id="contact_error" style="color:red;">*</span>
                                                 <select class="form-control" name="customername" id="customername" required="" style="font-weight:bold;color:black;">
                                                
                                                    <option value="<?php echo $CustomerID;?>"><?php echo $companyName;?></option>
                                                   
                                                </select>
                                                <span style="color:red"><?php echo form_error('customername'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Currency</label><br>
                                                <select name="cur" id="cur" class="form-control" style="font-weight:bold;color:black;" onchange="change_currency();">
                                                    <option value="1">US Dollars</option>
                                                    <option value="2">INR</option>
                                                    <option value="3">EURO</option>
                                                </select>
                                               <span style="color:red"><?php echo form_error('cur'); ?></span>

                                            </div>
                                        </div>

                                         <div class="col-md-3" >
                                            <div class="form-group">
                                                <label>Country</label>
                                                <select name="country" id="country" class="form-control" style="font-weight:bold;color:black;" onchange="change_currency();">
                                                    <?php 
                                                    $q = $this->db->select('country_id, country_name')->from('countries')->where('country_status',1)->get();
                                                    foreach($q->result() as $rowsss){
                                                    ?>
                                                    <option value="<?php echo $rowsss->country_id;?>" <?php if(set_value('country')==$rowsss->country_id){?> selected <?php } ?>><?php echo $rowsss->country_name;?></option>
                                                   <?php }?>
                                                </select>
                                                <span style="color:red"><?php echo form_error('country'); ?></span>

                                            </div>
                                        </div>
                                        <div style="clear: both;height: 10px;"></div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Quote for Machine Name</label>
                                                <span id="contact_error" style="color:red;">*</span>
                                                 <input type="text" name="machineName" id="machineName" class="form-control" value="<?php echo $machineName;?>" style="font-weight:bold;color:black;" required>
                                                 
                                                 <span style="color:red"><?php echo form_error('machineName'); ?></span>

                                            </div>
                                        </div>

                                          <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Machine Model No.</label>
                                                <span id="contact_error" style="color:red;">*</span>
                                                 <input type="text" class="form-control" name="machineModel" id="machineModel" value="<?php echo set_value('machineModel');?>" style="font-weight:bold;color:black;" required>
                                                 
                                                 <span style="color:red"><?php echo form_error('machineModel'); ?></span>
                                            </div>
                                        </div>
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
        <td>Liquid/Powder /Granules</td>
        <td><select id="producttobepacked" class="form-control" name="producttobepacked" required="">
        <option value="">--Select Select Product --</option>
        <option value="1" <?php if(set_value('producttobepacked')==1){?> selected <?php } ?>>Liquid</option>
        <option value="2" <?php if(set_value('producttobepacked')==2){?> selected <?php } ?>>Powder</option>
        <option value="3" <?php if(set_value('producttobepacked')==3){?> selected <?php } ?>>Granules</option>

        </select>
    <span style="color:red"><?php echo form_error('producttobepacked');?></span></td>

        </tr>
        <tr>
        <td>2</td>
        <td>Product name</td>
        <td></td>
        <td><input type="text" class="form-control" name="productname" id="productname" value="<?php echo set_value('productname');?>">
        <span style="color:red"><?php echo form_error('productname');?></span>
    </td>

        </tr>

         <tr>
          <td>3</td>
          <td>Pouch Size & Type</td>
          <td>W x L (in mm)</td>
          <td><input type="text" class="form-control" name="pouchsize" id="pouchsize" value="<?php echo set_value('pouchsize');?>">
           <span style="color:red"><?php echo form_error('pouchsize');?></span>
       </td>
      </tr>

         <tr>
          <td>4</td>
          <td>Quantity to be packed</td>
          <td>ml / gm</td>
          <td>  <input type="text" class="form-control" name="qtytobepacked" id="qtytobepacked" value="<?php echo set_value('qtytobepacked');?>">
           <span style="color:red"><?php echo form_error('qtytobepacked');?></span>
       </td>
        </tr>

          <tr>
          <td>5</td>
          <td>Horizontal Sealing Width</td>
          <td>Mm</td>
          <td>  <input type="text" class="form-control" name="horizontalsealingwidth" id="horizontalsealingwidth" value="<?php echo set_value('horizontalsealingwidth');?>">
             <span style="color:red"><?php echo form_error('horizontalsealingwidth');?></span>
          </td>
      </tr>

        <tr>
          <td>6</td>
          <td>Vertical Sealing Width</td>
          <td>Mm</td>
          <td>   <input type="text" class="form-control" name="verticalsealingwidth" id="verticalsealingwidth" value="<?php echo set_value('verticalsealingwidth');?>">
           <span style="color:red"><?php echo form_error('verticalsealingwidth');?></span>
       </td>
      </tr>

       <tr>
          <td>7</td>
          <td>Perforation Pitch</td>
          <td>Mm</td>
          <td>   <input type="text" class="form-control" name="perforationpitch" id="perforationpitch" value="<?php echo set_value('perforationpitch');?>">
           <span style="color:red"><?php echo form_error('perforationpitch');?></span>
       </td>
      </tr>

      <tr>
          <td>8</td>
          <td>Type of Sealing</td>
          <td>VLining/Butt/K nurling</td>
          <td>   <select class="form-control" id="typeofsealing" name="typeofsealing" required="">
                                                    <option value="">--Select Type of Sealing--</option>
                                                    <option value="V-Lining" <?php if(set_value('typeofsealing')=="V-Lining"){?> selected <?php } ?>>V-Lining</option>
                                                    <option value="Butt" <?php if(set_value('typeofsealing')=="Butt"){?> selected <?php } ?>>Butt</option>
                                                    <option value="Knurling" <?php if(set_value('typeofsealing')=="Knurling"){?> selected <?php } ?>>Knurling</option>
                                                   
                                                </select>
                                                <span style="color:red"><?php echo form_error('typeofsealing');?></span>
                                            </td>
      </tr>

      <tr>
          <td>9</td>
          <td>PLC Make</td>
          <td>Omron / AB</td>
          <td>    
            <select class="form-control" id="plcmake" name="plcmake" required="">
            <option value="">--Select PLC Make--</option>
            <option value="Omron" <?php if(set_value('plcmake')=="Omron"){?> selected <?php } ?>>Omron</option>
            <option value="AB" <?php if(set_value('typeofsealing')=="AB"){?> selected <?php } ?>>AB</option>

            </select>
            <span style="color:red"><?php echo form_error('plcmake');?></span>
        </td>
      </tr>
       <tr>
          <td>10</td>
          <td>Power Supply</td>
          <td>VAC/Ph/Hz</td>
          <td>    <input type="text" class="form-control" name="powersupply" id="powersupply" value="<?php echo set_value('powersupply');?>">
           <span style="color:red"><?php echo form_error('powersupply');?></span>
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
                <th>Sr No.</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
          <td>01</td>
          <td>Model <input type="text" class="form-control" name="machinemodelno" id="machinemodelno" value="<?php echo set_value('machinemodelno');?>">
            <span style="color:red"><?php echo form_error('machinemodelno');?></span>
            <ul>
                <li>Basic machine frame in welded structure.</li>
<li>Detachable Laminate reel mounting unit for two reels mounting with laminate</li>
end detector sensor.
<li>Electronics web aligner for laminate tracking with Dual sensing.</li>
<li>Sealing station for longitudinal sealing.</li>
<li>Pre-determined laminate draw with Servo drive system along with eye mark
sensor.</li>
<li>Servo operated Batch cutter cum perforation Blade with servo driven horizontal
and vertical sealing jaws.</li>
<li>Electrical Equipment for standard machine with AC cooled panel.</li>
<li>Standard front Polycarbonate Guarding with limit switches.</li>
<li>Pack ML Enabled machine</li>
<li>Smart HMI -15 inches</li>
<li>Elmedur Sealers</li>
<li>Temperature control module for heaters</li>
<li>Automatic Lubrication system</li>
<li>Downtime data and performance tracking</li>
            </ul>
          </td>
      </tr>

      <tr>
        <td></td>
          <td>No. of Axis in Machine: <input type="text" class="form-control" name="noofaxisinmachine" id="noofaxisinmachine" value="<?php echo set_value('noofaxisinmachine');?>">
             <span style="color:red"><?php echo form_error('noofaxisinmachine');?></span><br><br>
            <textarea class="form-control" name="axisdetail" id="axisdetail" placeholder="Axis in Machine Detail"><?php echo set_value('axisdetail');?></textarea>
            <span style="color:red"><?php echo form_error('axisdetail');?></span>
          </td>
      </tr>
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
                <td><input type="text" class="form-control" name="machinemodel" id="machinemodel" value="<?php echo set_value('machinemodel');?>">
                    <span style="color:red"><?php echo form_error('machinemodel');?></span>
                </td>
            </tr>
             <tr>
                <td>Sealing Style </td>
                <td><input type="text" class="form-control" name="sealingstyle" id="sealingstyle" value="<?php echo set_value('sealingstyle');?>">
                <span style="color:red"><?php echo form_error('sealingstyle');?></span>
            </td>
            </tr>
             <tr>
                <td>Speed</td>
                <td>  <input type="text" class="form-control" name="speed" id="speed" value="<?php echo set_value('speed');?>">
                <span style="color:red"><?php echo form_error('speed');?></span>
            </td>
            </tr>
             <tr>
                <td>No. of Tracks </td>
                <td> <input type="text" class="form-control" name="nooftracks" id="nooftracks" value="<?php echo set_value('nooftracks');?>">
                <span style="color:red"><?php echo form_error('nooftracks');?></span>
            </td>
            </tr>
             <tr>
                <td>Laminate specification </td>
                <td> <input type="text" class="form-control" name="laminate_specification" id="laminate_specification" value="<?php echo set_value('laminate_specification');?>">
                 <span style="color:red"><?php echo form_error('laminate_specification');?></span>
             </td>
            </tr>
             <tr>
                <td>Product to be packed </td>
                <td> <input type="text" class="form-control" name="producttobepacked" id="producttobepacked" value="<?php echo set_value('producttobepacked');?>">
                     <span style="color:red"><?php echo form_error('producttobepacked');?></span>
                </td>
            </tr>
             <tr>
                <td>Filling capacity </td>
                <td> <input type="text" class="form-control" name="fillingcapacity" id="fillingcapacity" value="<?php echo set_value('fillingcapacity');?>">
                <span style="color:red"><?php echo form_error('fillingcapacity');?></span></td>
            </tr>
             <tr>
                <td>Pouch Size </td>
                <td> <input type="text" class="form-control" name="pouchsize" id="pouchsize" value="<?php echo set_value('pouchsize');?>">
                 <span style="color:red"><?php echo form_error('pouchsize');?></span>
             </td>
            </tr>
             <tr>
                <td>Sealing drives</td>
                <td> <input type="text" class="form-control" name="sealingdrives" id="sealingdrives" value="<?php echo set_value('sealingdrives');?>">
                <span style="color:red"><?php echo form_error('sealingdrives');?></span>
            </td>
            </tr>
             <tr>
                <td>Perforation and cutting </td>
                <td> <input type="text" class="form-control" name="perforationandcutting" id="perforationandcutting" value="<?php echo set_value('perforationandcutting');?>">
                 <span style="color:red"><?php echo form_error('perforationandcutting');?></span>
             </td>
            </tr>
             <tr>
                <td>Laminate Draw Off system </td>
                <td> <input type="text" class="form-control" name="laminatedrawoffsystem" id="laminatedrawoffsystem" value="<?php echo set_value('laminatedrawoffsystem');?>">
                 <span style="color:red"><?php echo form_error('laminatedrawoffsystem');?></span>
             </td>
            </tr>
             <tr>
                <td>Laminate tracking system </td>
                <td> <input type="text" class="form-control" name="laminatetrackingsystem" id="laminatetrackingsystem" value="<?php echo set_value('laminatetrackingsystem');?>">
                <span style="color:red"><?php echo form_error('laminatetrackingsystem');?></span></td>

            </tr>
             <tr>
                <td>Electrical Spec. </td>
                <td> <input type="text" class="form-control" name="electricalspec" id="electricalspec" value="<?php echo set_value('electricalspec');?>">
                <span style="color:red"><?php echo form_error('electricalspec');?></span>
            </td>
            </tr>
             <tr>
                <td>Layout Dimensions</td>
                <td> <input type="text" class="form-control" name="layoutdimensions" id="layoutdimensions" value="<?php echo set_value('layoutdimensions');?>">
                <span style="color:red"><?php echo form_error('layoutdimensions');?></span>
            </td>
            </tr>
             <tr>
                <td>Machine Weight </td>
                <td> <input type="text" class="form-control" name="machineweight" id="machineweight" value="<?php echo set_value('machineweight');?>">
                 <span style="color:red"><?php echo form_error('machineweight');?></span>
             </td>
            </tr>
            <tr>
                <td>Compressed Air</td>
                <td> <span id="contact_error" style="color:red;">*</span>
                <input type="text" class="form-control" name="compressedair" id="compressedair" value="<?php echo set_value('compressedair');?>">
                 <span style="color:red"><?php echo form_error('compressedair');?></span>
                </td>
            </tr>

        </tbody>
    </table>
</div>
 </div>

 <div class="row card-box" style="border:1px solid #000;">
    <div class="col-md-12">
                                            <h3 class="text-center page-title">Annexure- IV (Price Schedule)</h5>
                                            <h4 class="text-center">Line items below will be added as per the requirement</h4>
                                        </div>

    <div class="col-md-12 table-responsive table-container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>S.no.</th>
                <th>Item description</th>
                <th>Qty</th>
                <th>Price in (<span id="pricesymbol">USD</span>)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>A </td>
                <td><input type="text" class="form-control" name="modelno" placeholder="Model" value="<?php echo set_value('modelno');?>">
                    <span style="color:red"><?php echo form_error('modelno');?></span>
                    <br>
                Price of design, manufacturing, supply and installation
of machine as per technical specifications at
Annexure-II<br>
Ladder and Platform.<br>
Guarding with Door switches.</td>
                <td><input type="text" class="form-control" name="modelqty" id="modelqty" value="<?php echo set_value('modelqty');?>">
                <span style="color:red"><?php echo form_error('modelqty');?></span></td>
                <td><input type="text" class="form-control" name="modelprice" id="modelprice" value="<?php echo set_value('modelprice');?>">
                <span style="color:red"><?php echo form_error('modelprice');?></span></td>
               
            </tr>

             <tr>
                <td>B </td>
                <td>
                    <textarea class="form-control" name="auger_filling_system" id="auger_filling_system" placeholder=""><?php echo set_value('auger_filling_system');?></textarea>
                    <span style="color:red"><?php echo form_error('auger_filling_system');?></span>
                      <script>
                        CKEDITOR.replace( 'auger_filling_system' );
                </script>
                <td><input type="text" class="form-control" name="auger_filling_system_qty" id="auger_filling_system_qty" value="<?php echo set_value('auger_filling_system_qty');?>">
                  <span style="color:red"><?php echo form_error('auger_filling_system_qty');?></span>
              </td>
                <td><input type="text" class="form-control" name="auger_filling_system_price" id="auger_filling_system_price" value="<?php echo set_value('auger_filling_system_price');?>">
                 <span style="color:red"><?php echo form_error('auger_filling_system_price');?></span>
             </td>
               
            </tr>

            <tr>
                <td>C </td>
                <td><input type="text" class="form-control" name="discharge" placeholder="" value="Discharge Conveyor with Rejection system" readonly></td>
                <td><input type="text" class="form-control" name="discharge_qty" id="discharge_qty" value="<?php echo set_value('discharge_qty');?>">
                <span style="color:red"><?php echo form_error('discharge_qty');?></span></td>
                <td><input type="text" class="form-control" name="discharge_price" id="discharge_price" value="<?php echo set_value('discharge_price');?>">
                <span style="color:red"><?php echo form_error('discharge_price');?></span></td>
               
            </tr>

            <tr>
                <td>D </td>
                <td><input type="text" class="form-control" name="autocasepacker" placeholder="" value="Auto Case Packer with Transfer Conveyor" readonly></td>
                <td><input type="text" class="form-control" name="autocasepacker_qty" id="autocasepacker_qty" value="<?php echo set_value('autocasepacker_qty');?>"><span style="color:red"><?php echo form_error('autocasepacker_qty');?></span></td>
                <td><input type="text" class="form-control" name="autocasepacker_price" id="autocasepacker_price" value="<?php echo set_value('autocasepacker_price');?>"><span style="color:red"><?php echo form_error('autocasepacker_price');?></span></td>
               
            </tr>

            <tr>
                <td>H </td>
                <td><input type="text" class="form-control" name="packingcharges" placeholder="" value="Packing Charges" readonly></td>
                <td><input type="text" class="form-control" name="packingcharges_qty" id="packingcharges_qty" value="<?php echo set_value('packingcharges_qty');?>"><span style="color:red"><?php echo form_error('packingcharges_qty');?></span></td>
                <td><input type="text" class="form-control" name="packingcharges_price" id="packingcharges_price" value="<?php echo set_value('packingcharges_price');?>"><span style="color:red"><?php echo form_error('packingcharges_price');?></span></td>
               
            </tr>


            <tr>
                <td>I </td>
                <td><input type="text" class="form-control" name="forwarding_charges" placeholder="" value="Forwarding Charges" readonly></td>
                <td><input type="text" class="form-control" name="forwarding_charges_qty" id="forwarding_charges_qty" value="<?php echo set_value('forwarding_charges_qty');?>"><span style="color:red"><?php echo form_error('forwarding_charges_qty');?></span></td>
                <td><input type="text" class="form-control" name="forwarding_charges_price" id="forwarding_charges_price" value="<?php echo set_value('forwarding_charges_price');?>"><span style="color:red"><?php echo form_error('forwarding_charges_price');?></span></td>
               
            </tr>



<tr>
                <td>J </td>
                <td><input type="text" class="form-control" name="insurancecharges" placeholder="" value="Insurance Charges" readonly></td>
                <td><input type="text" class="form-control" name="insurancecharges_qty" id="insurancecharges_qty" value="<?php echo set_value('insurancecharges_qty');?>">
                <span style="color:red"><?php echo form_error('insurancecharges_qty');?></span></td>
                <td><input type="text" class="form-control" name="insurancecharges_price" id="insurancecharges_price" value="<?php echo set_value('insurancecharges_price');?>"><span style="color:red"><?php echo form_error('insurancecharges_price');?></span></td>
               
            </tr>

            <tr>
                <td>K </td>
                <td>Freight until <input type="text" class="form-control" name="freightuntil" id="freightuntil" value="<?php echo set_value('freightuntil');?>">
                <span style="color:red"><?php echo form_error('freightuntil');?></span></td>
                <td><input type="text" class="form-control" name="freightuntil_qty" id="freightuntil_qty" value="<?php echo set_value('freightuntil_qty');?>"><span style="color:red"><?php echo form_error('freightuntil_qty');?></span></td>
                <td><input type="text" class="form-control" name="freightuntil_price" id="freightuntil_price" value="<?php echo set_value('freightuntil_price');?>"><span style="color:red"><?php echo form_error('freightuntil_price');?></span></td>
               
            </tr>

             <tr>
                <td>L </td>
                <td>Total CIF  <input type="text" class="form-control" name="totalcif" id="totalcif" value="<?php echo set_value('totalcif');?>">
                 <span style="color:red"><?php echo form_error('totalcif');?></span>
             </td>
                <td><input type="text" class="form-control" name="totalcif_qty" id="totalcif_qty" value="<?php echo set_value('totalcif_qty');?>">
                    <span style="color:red"><?php echo form_error('totalcif_qty');?></span>
                </td>
                <td><input type="text" class="form-control" name="totalcif_price" id="totalcif_price" value="<?php echo set_value('totalcif_price');?>">
                <span style="color:red"><?php echo form_error('totalcif_price');?></span>
            </td>
               
            </tr>
            <tr>
                <td>M </td>
                <td>Total CIF  <textarea class="form-control" name="totalcostcif" id="totalcostcif"><?php echo set_value('totalcostcif');?></textarea>
                <span style="color:red"><?php echo form_error('totalcostcif');?></span>
                <script>
                        CKEDITOR.replace( 'totalcostcif' );
                </script>
            </td>
                <td><input type="text" class="form-control" name="totalcostcif_qty" id="totalcostcif_qty" value="<?php echo set_value('totalcostcif_qty');?>">
                    <span style="color:red"><?php echo form_error('totalcostcif_qty');?></span>
                </td>
                <td><input type="text" class="form-control" name="totalcostcif_price" id="totalcostcif_price" value="<?php echo set_value('totalcostcif_price');?>">
                <span style="color:red"><?php echo form_error('totalcostcif_price');?></span>
            </td>
               
            </tr>
            <tr>
                <td colspan="4">Optional:-</td>

            </tr>
            <tr id="optionaldata">
                <td>1</td>
                <td><input type="text" class="form-control" name="optionaldata[]" id="optionaldata0"></td>
                <td><input class="form-control" name="optionalqty[]" id="optionalqty" ></td>
                <td><a href='javascript:;' onclick="addMoreOption();"><i class="fa fa-plus"></i></a></td>
            </tr>
             

        </tbody>
    </table>
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
            <tr>
                <td>A </td>
                <td>Terms of Payments</td>
                <td><textarea name="paymentterms" id="paymentterms" class="form-control"><?php echo set_value('paymentterms');?></textarea>
                 <span style="color:red"><?php echo form_error('paymentterms');?></span>

                 <script>
                        CKEDITOR.replace( 'paymentterms' );
                </script>
             </td>
               
               
            </tr>

            <tr>
                <td>B</td>
                <td>Delivery</td>
                <td><textarea name="delivery" id="delivery" class="form-control"><?php echo set_value('delivery');?></textarea>
                <span style="color:red"><?php echo form_error('delivery');?></span>

                 <script>
                        CKEDITOR.replace( 'delivery' );
                </script>

                </td>
               
               
            </tr>

              <tr>
                <td>C</td>
                <td>Out of Pocket Expense</td>
                <td><input type="number" min="1" name="out_of_pocket_expense" id="out_of_pocket_expense" class="form-control" value="<?php echo set_value('out_of_pocket_expense');?>">
                 <span style="color:red"><?php echo form_error('out_of_pocket_expense');?></span>
             </td>
               
               
            </tr>

            <tr>
                <td>D</td>
                <td>Support Statement</td>
                <td>
                    <textarea name="support_statement" id="support_statement" class="form-control">Shubham Pack will Provide Local Engineer Support in INDONESIA. Shubham Pack is planning for an office in INDONESIA to cater demand of service and spares.</textarea><span style="color:red"><?php echo form_error('support_statement');?></span></td>
               
               
            </tr>

            

        </tbody>
    </table>
</div>
</div>

<div class="row card-box" style="border:1px solid #000;">
<div class="col-md-12">
<h3 class="text-center page-title">Next Followup Information</h3>
</div>
<div class="col-md-4"></div>
<div class="col-md-4">
    <div class="form-group">
        <label>Next Followup Date <span style="color:red">*</span></label>
        <input type="date" name="followDate" id="followDate" class="form-control" value="<?php echo set_value('followDate');?>">
        <span style="color:red"><?php echo form_error('followDate');?></span>
    </div>
</div>

</div>
                                <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4 text-center">
                                <div class="form-group">
                                <label>&nbsp;</label>
                                <input type="submit" id="businessupdate" class="btn btn-success" value="Generate Quote" style="width:60%">
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
<script>
$(document).ready(function(){
    $("#loginForm :input").attr('required',false);
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
    $("#pricesymbol").text(cur);
}

function addMoreOption()
{

}

</script>

    </body>
</html>