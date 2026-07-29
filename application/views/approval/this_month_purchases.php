<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();
$getVendors = $CI->Salescrm_model->getVendors();
$getAllProducts = $CI->Salescrm_model->getAllProducts();

if($start_date <> '' && $end_date <> '') {
    $starting_date = $start_date;
    $ending_date = $end_date;
    $hpcl_locations = $hpcl_locations;
    $vendors=$vendors;
    $products = $products;
} else {
    $starting_date = date('Y-m-01');
    $ending_date = date('Y-m-t');
    $hpcl_locations = 'ALL';
    $products = 'ALL';
    $vendors="ALL";
}


$purchase_data=$CI->Salescrm_model->IND_money_format($CI->Salescrm_model->months_purchase_type1($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6),$this->uri->segment(7)));


if($this->uri->segment(5)<>'ALL')
{
$loc=$CI->Salescrm_model->get_location_name($this->uri->segment(5));
}else
{
$loc="ALL";
}

if($this->uri->segment(6)<>'ALL')
{
$party=$CI->Salescrm_model->get_party_name($this->uri->segment(6));
}else
{
$party="ALL";
}


if($this->uri->segment(7)<>'ALL')
{
$product=$CI->Salescrm_model->get_product_name($this->uri->segment(7));
}else
{
$product="ALL";
}
$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="6">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Location</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Party</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Product</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Total Purchase Value</th>
              
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($this->uri->segment(3))).' to '.date('d-M-Y',strtotime($this->uri->segment(4))).'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$loc.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$party.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$product.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;font-size: 25px;color:red;font-weight: bold;">₹'.$purchase_data.'</td>
               
                </tr>
                </tbody></table>';
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
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
            table.pretty thead th {
                text-align: center;
                background:<?php echo $LOGO->colorcode;?>;
                color:#fff;
                font-size:12px;
            }
            table.pretty td {
                text-align: center;
                font-size:12px;
            }
            </style>
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
                            <h4 class="page-title text-center">THIS MONTH'S PURCHASES</h4>
                        </div>

                    <div class="page-title-box"> 
                        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        </div>

                    </div> 
                </div>

                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <!-- <div class="page-title-box col-md-4"> -->
                         <!-- <div class="btn-group pull-right">
                          <a href="<?php echo page_url;?>Approval/approval" class="btn btn-success waves-effect waves-light">Add Data</a>
                               
                            </div> -->
                           
                            <!-- <h4 class="page-title text-center">Approval List</h4>
                        </div> -->
                        <form method="post" action="<?php echo page_url;?>Approval/filter_purchase_list">
                            <div class="card-box col-md-12">
                                <div class="">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo $starting_date;?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="to_date" class="form-control" value="<?php echo $ending_date;?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <select class="form-control" name="hpcl_locations">
                                                <option value="ALL">ALL</option>
                                                <?php if($getHpclLocations != '') {
                                                        foreach($getHpclLocations as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $hpcl_locations) { echo 'selected';};?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Party</label>
                                            <select class="form-control" name="vendors">
                                                <option value="ALL" <?php if("ALL" == $vendors) { echo 'selected';};?>>ALL</option>
                                                <?php if($getVendors != '') {
                                                        foreach($getVendors as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $vendors) { echo 'selected';};?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Product</label>
                                            <select class="form-control" name="products" id="products">
                                                <option value="ALL">ALL</option>
                                                <?php if($getAllProducts != '') {
                                                        foreach($getAllProducts as $row2) {?>
                                                <option value="<?php echo $row2->id;?>" <?php if($row2->id == $products) { echo 'selected';};?>><?php echo $row2->instruments_name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 card-box"><?php echo $filter_criteria;?></div>
                                            </div>
                </div>



              

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example1" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Approval ID</th>
                                    <th>HPCL Location</th>
                                    <th>Bill No</th>
                                    <th>Party</th>
                                    <th>Product Name</th>
                                    <th>MOQ</th>
                                    <th>Qty/Pack Size</th>
                                    <th>Approved Price</th>
                                    <th>Total Amount</th>
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
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
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       

        <script>
$( document ).ready(function() {
$('#products').select2(); 
$('#example1').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/this_month_purchases_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>",
"aoColumns": [
                { mData: 'sr_no' },
                { mData: 'ref_code' },
                { mData: 'hpcl_location' },
                { mData: 'bill_no' },
                { mData: 'party' },
                { mData: 'product_name' },
                { mData: 'moq' },
                { mData: 'qty' },
                { mData: 'approved_price' },
                { mData: 'credit_note_sum' }
                
                
        ]
}); 
});

</script>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
    $("#error_business_loc").html('Required!');
}
var department_name = $("#department_name").val();
if(department_name=='')
{
    
    $("#error_department_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
    
    $("#error_status").html('Required!');
}


if(business_loc=='' || department_name==''|| status=='' )
{
    
    return false;
}

});
});
</script>
<script type="text/javascript">
    function chk_item_picked_up(id) {
        window.location.href = "<?php echo page_url;?>Approval/chk_item_picked_up/"+id;
    }
</script>
    </body>
</html>