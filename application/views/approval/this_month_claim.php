<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getAllVendors = $CI->Salescrm_model->getAllVendors();
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();
$claim_this_month=$CI->Salescrm_model->this_month_claim_list_consolidated_amount($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5));
$claim_this_month=explode('|',$claim_this_month);
if($this->uri->segment(5)<>'ALL' && $this->uri->segment(5)<>'')
{
$loc=$CI->Salescrm_model->get_location_name($this->uri->segment(5));
}else
{
$loc="ALL";
}


// if($this->uri->segment(5)<>'ALL' && $this->uri->segment(5)<>'')
// {
// $vendor=$CI->Salescrm_model->get_party_name($this->uri->segment(5));
// }else
// {
// $vendor="ALL";
// }

$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="2">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>

               
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($this->uri->segment(3))).' to '.date('d-M-Y',strtotime($this->uri->segment(4))).'</td>
                
               
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-md-12">
                    
                            <h4 class="page-title text-center">THIS MONTH'S CLAIM TYPE-I</h4>
                        
                    </div>
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 card-box ">
                       

                        <form method="post" action="<?php echo page_url;?>Approval/filter_this_month_claim">
                            
                                 <div class="col-md-3"></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo $this->uri->segment(3);?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="to_date" class="form-control" value="<?php echo $this->uri->segment(4);?>" required="">
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Party</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <select class="form-control" name="party">
                                                <option value="ALL">ALL</option>
                                                <?php if($getAllVendors != '') {
                                                        foreach($getAllVendors as $row2) {?>
                                                        <option value="<?php echo $row2->id;?>"<?php if($row2->id == $this->uri->segment(5)) { echo 'selected';};?>><?php echo $row2->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div> -->

                                 <div class="col-md-2" style="display:none;">
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
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                             
                        </form>
                    </div>
                </div>
            
                <div class="row card-box">
                    
                    <div class="col-md-4" style="min-height: 170px;">
                          <h4 class="text-center">Type I CR. Claim</h4>
                          <div class="col-md-12">
                        <p class="text-center"><a href="<?php echo page_url;?>ExcelImport/type_one_claim_without_interest/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/" class="btn btn-danger btn-sm">Download Type-I Claim Format (Without Interest)</a></p>
                    </div>
                   
                    <div class="col-md-12">
                          <p class="text-center"><a href="<?php echo page_url;?>ExcelImport/type_one_claim_with_interest/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/" class="btn btn-danger btn-sm">Download Type-I Claim Format (With Interest)</a></p>
                    </div>

                     <div class="col-md-12">
                          <p class="text-center"><a href="<?php echo page_url;?>ExcelImport/type_one_claim_with_interest_debit_note/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/" class="btn btn-danger btn-sm">Download Type-I Debit Note Format </a></p>
                    </div>

                    </div>
                    <div class="col-md-4" style="min-height: 170px;">  <h4 class="text-center">Filter Criteria</h4>
                        <?php echo $filter_criteria;?>
                    </div>

                    <div class="col-md-4" style="min-height: 170px;">
                        <h4 class="text-center">This Month's Cr. Note Claim Amount</h4>
                        <table class="table table-bordered">
    <thead>
      <tr>
        <th style="border: 1px solid #000;text-align:center;">Claim Amount</th>
        <th style="border: 1px solid #000;text-align:center;">Interest</th>
        <th style="border: 1px solid #000;text-align:center;">Invoice</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="color:red;font-weight: bold;font-size: 30px;width:33%;border: 1px solid #000;text-align:center;">₹<?php echo $claim_this_month[0];?></td>
        <td style="color:red;font-weight: bold;font-size: 30px;width:33%;border: 1px solid #000;text-align:center;">₹<?php echo $claim_this_month[1];?></td>
        <td style="color:red;font-weight: bold;font-size: 30px;width:33%;border: 1px solid #000;text-align:center;">
            <?php if($getHpclLocations != '') {
                                        foreach($getHpclLocations as $row2) {
                                            if($row2->id==2){?>
                                <div class="col-md-4">
                                     <a href="<?php echo page_url;?>Pdfexample/type_1_invoice/<?php echo $row2->id;?>/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>" class="btn btn-xs btn-warning">TYPE 1 INVOICE</a> 
                                </div>
                                <?php } }  } ?>
        </td>
     
      </tr>
     
    </tbody>
  </table>

                       
                        
                    
                       

                    </div>
                </div>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                               <!--  <tr>
                                    <th>Sr No.</th>
                                    <th>Approval ID/ Type</th>
                                
                                    <th>Product</th>
                                    <th>Total Claim On Approval</th>
                                    <th>Validity</th>
                                    <th>Approved Price</th>
                                    <th>Credit Note</th>
                                    <th>MOQ</th>  
                                    <th>Purchased Qty</th>                       
                                    <th>Claim Generated</th>                       
                                    <th>Error/Warning</th>                       
                                    
                                </tr> -->

                                <tr>
                                <th>Sr No.</th>
                                <th>Bill No.</th>
                                <th>Party</th>
                                <th>Purchase Date</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Total</th>
                                <th>GST</th>
                                <th>Grand Total</th>
                                <th>Payment Due Date</th>
                                <th>Payment Status</th>
                                <th>Collection</th>
                                <th>Credit VLI</th>
                                <th>Total Interest</th>
                                <th>Total Claim</th> 
                                <th>Final Claim</th> 
                                <th>Errors</th> 

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

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

        <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/this_month_claim_list_consolidated/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",
// "aoColumns": [
//                 { mData: 'sr_no' } ,
//                 { mData: '' },
               
//                 { mData: 'product_details' },
//                 { mData: 'total_claim' }
//                 // { mData: 'validty' },
//                 // { mData: 'approved_price' },
//                 // { mData: 'creditnote' },
//                 // { mData: 'moq' },
//                 // { mData: 'purchase_qty' },
//                 // { mData: 'claim' },
//                 // { mData: 'error' }

            
//         ]
        
                "aoColumns": [

                { mData: 'sr_no' } ,
                { mData: 'bill_no' } ,
                { mData: 'party' },
                { mData: 'purchase_date' },
                { mData: 'product_details' },
                { mData: 'qty' },
                { mData: 'rate' },
                { mData: 'total_rate' },
                { mData: 'gst_rate' },
                { mData: 'with_tax' },
                { mData: 'due_date' },
                { mData: 'payment_status' },
                { mData: 'collection' },
                { mData: 'credit' },
                { mData: 'total_interest' },
                { mData: 'total_claim' },
                { mData: 'final_claim' },
                { mData: 'errors' }

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
    </body>
</html>