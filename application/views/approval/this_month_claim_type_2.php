<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getAllVendors = $CI->Salescrm_model->getAllVendors();
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();
$getAllProducts = $CI->Salescrm_model->getAllProducts();

if($this->uri->segment(5)<>'ALL' && $this->uri->segment(5)<>'')
{
$loc=$CI->Salescrm_model->get_location_name($this->uri->segment(5));
}else
{
$loc="ALL";
}
if($this->uri->segment(6)<>'ALL')
{
$product=$CI->Salescrm_model->get_product_name($this->uri->segment(6));
}else
{
$product="ALL";
}
$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="3">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>
        
                <th style="border: 1px solid black;text-align:center; width:200px;">Location</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Product</th>
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($this->uri->segment(3))).' to '.date('d-M-Y',strtotime($this->uri->segment(4))).'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$loc.'</td>
              
                <td style="border: 1px solid black;text-align:center;color:black;">'.$product.'</td>
                </tr>
                </tbody></table>';




$claim_amount=$CI->Salescrm_model->get_months_credit_claim_amount_type2_new($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
$claim_amount_without_pay=$CI->Salescrm_model->get_months_credit_claim_amount_type2_new_without_payment($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));



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
            .select2-container .select2-selection--single
            {
                height: 38px !important;
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-md-12">
                    
                            <h4 class="page-title text-center">THIS MONTH'S CLAIM TYPE-II/III</h4>
                        
                    </div>
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 card-box ">
                       

                        <form method="post" action="<?php echo page_url;?>Approval/filter_this_month_claim_type2">
                            
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo $this->uri->segment(3);?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="to_date" class="form-control" value="<?php echo $this->uri->segment(4);?>" required="">
                                        </div>
                                    </div>
                                  

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <select class="form-control" name="hpcl_locations">
                                                <option value="ALL">ALL</option>
                                                <?php if($getHpclLocations != '') {
                                                        foreach($getHpclLocations as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $this->uri->segment(5)){?> selected <?php } ?>><?php echo $row1->name;?></option>
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
                                                <option value="<?php echo $row2->id;?>" <?php if($row2->id == $this->uri->segment(6)) { echo 'selected';};?>><?php echo $row2->instruments_name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                             
                        </form>
                    </div>
                </div>
            
                <div class="row  card-box">
                    
                
                    <div class="col-md-6" style="min-height: 184px;"><?php echo $filter_criteria;?>
                     <p class="text-center"><a href="<?php echo page_url;?>ExcelImport/type_two_claim_format_new
/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>" class="btn btn-success btn-sm">Download Type-II/III Claim Format</a></p>

 <p class="text-center"><a href="<?php echo page_url;?>ExcelImport/type_two_claim_format_new_internal_working
/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>" class="btn btn-warning btn-sm" style='color:black !important;'>Download Type-II/III Claim Format For Internal Working</a></p>

                    </div>

                    <div class="col-md-6">
                    <table class="table table-bordered">
                    <thead>
                    <tr>
                    <th style="border: 1px solid #000;text-align:center;">Estimated Total Commission</th>
                    <th style="border: 1px solid #000;text-align:center;">Actual Total Commission</th>
        
                    <th style="border: 1px solid #000;text-align:center;">Invoice(s)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                         <td style="color:red;font-weight: bold;font-size: 30px;width:20%;border: 1px solid #000;text-align:center;">₹<?php echo $claim_amount_without_pay;?></td>
                    <td style="color:red;font-weight: bold;font-size: 30px;width:20%;border: 1px solid #000;text-align:center;">₹<?php echo $claim_amount;?></td>
                    <td style="width:20%;border: 1px solid #000;text-align:center;">
                        <?php if($getHpclLocations != '') {
                                        foreach($getHpclLocations as $row2) {?>
                                    <?php if($row2->id == 2) { ?>
                                <p>
                                    <a href="<?php echo page_url;?>Pdfexample/type_2_invoice/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $row2->id;?>/<?php echo $this->uri->segment(6);?>"><?php echo $row2->name;?></a>
                                </p>
                                <?php } } } ?>
                    </td>

                    </tr>

                    </tbody>
                    </table>
                    </div>
                    <!-- <div class="col-md-2 card-box" style="min-height: 184px;">
                        <h4 class="text-center">CFA Commission</h4>
                        <p style="color:red;font-weight: bold;font-size: 30px;text-align: center;">₹<?php echo $claim_amount;?></p>
                       
                          <div class="row">
                            <div class="col-md-12">
                                <?php if($getHpclLocations != '') {
                                        foreach($getHpclLocations as $row2) {?>
                                <div class="col-md-3">
                                    <a href="<?php echo page_url1;?>taxinvoice/tcpdf/examples/type2_invoice.php?location_id=<?php echo $row2->id;?>&start_date=<?php echo $this->uri->segment(3);?>&end_date=<?php echo $this->uri->segment(4);?>"><?php echo $row2->name;?></a>
                                </div>
                                <?php } } ?>
                            </div>
                        </div>
                    </div> -->

                 <!--     <div class="col-md-2 card-box" style="min-height: 184px;">
                        <h4 class="text-center">Transportation Claim</h4>
                        <p style="color:red;font-weight: bold;font-size: 30px;text-align: center;">₹<?php echo $transport_claim_amount;?></p>
                       

                    </div> -->

                    <!--  <div class="col-md-2 card-box" style="min-height: 184px;">
                        <h4 class="text-center">Total Claim</h4>
                        <p style="color:red;font-weight: bold;font-size: 30px;text-align: center;">₹<?php echo $claim_amount+   $transport_claim_amount;?></p>
                       

                    </div> -->
                </div>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                               <tr>
                                   <th>Sr No.</th>
                                    <th>Type</th>
                                    <th>Invoice No./Date/Attachment</th>
                                    <th>Customer Name</th>
                                    <th>Location</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>Total</th>
                                    <th>Payment Status</th>
                                    <th>Approvals Applicable</th>
                                    <th>Commission</th>
                                    <th>Transportation</th>
                                    <th>Total</th>
                                    <th>Warning/Messages</th>
                                    
                    
                                
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
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/this_month__cr_note_claim_list_t2_new/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",
"aoColumns": [
                { mData: 'sr_no' } ,
            { mData: 'type' } ,
            { mData: 'current_date' },
            { mData: 'customer_name' },
            { mData: 'location' },
            { mData: 'product_name' },
            { mData: 'product_qty' },
            { mData: 'product_rate' },
            { mData: 'total_r' },
            // { mData: 'tds' },
            // { mData: 'amount' },
            // { mData: 'gst' },
            // { mData: 'final_base_amount' },
            // { mData: 'tds_amount' },
            // { mData: 'tcs' },
            // { mData: 'tcs_amt' },
            // { mData: 'tamount' },
            // { mData: 'alreadypaid' },
            { mData: 'balancepayable' },
            { mData: 'approval_applicable' },
            { mData: 'commission' },
            { mData: 'transportation' },
            { mData: 'total' },
            { mData: 'error'}

               
                
                
        ]
        
});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
         <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
    $('#products').select2(); 

});
</script>
    </body>
</html>