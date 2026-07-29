<?php 
$lead_stage = $this->uri->segment(3);
$current_url = page_url.'Leads/lead_stages/'.$lead_stage;

$app=array();
$row112=$this->db->select('approval_id')->from('approvals_permission')->where('user_id',$_SESSION['logged_in']['user_id'])->get();
if($row112->num_rows()>0)
{
foreach($row112->result() as $rowss)
{
$app[]=$rowss->approval_id;
}

}else
{}


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
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
 
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

            .btns {
                margin-top: 20px;
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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box col-md-1">
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btns" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="page-title-box col-md-12">
                        <h4 class="page-title text-center">Common Approval Window</h4>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>




      <?php if(in_array(8,$app)){ ?>
                 <div class="row">

                     <div id="showactiondiv">
                                    <div class="col-md-2 pull-right" style="padding-bottom: 20px;">
                                        <select class="form-control" name="action" id="action" onchange="takeaction();" style="height: 36px;" required>
                                        <option value="">Action</option>
                                        <option value="1">Mark Inactive</option>                                       
                                        <option value="2">Approve</option>                                       
                                        </select>
                                    </div>
                                </div>

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">APPROVAL FOR EQUIVALENT CHART</p>
                            <table id="exampleequivalant" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll1" value="0"/></th>
                                     <th>Sr No.</th>
                                                    
                                     <th>Competitor Product</th>
                                      <th>Our Equivalent Product</th>                              
                                     <th>Added On</th>
                                     <th>Added By</th>
                                      <th>Approve</th>
                                     <th>Reject</th>
                                                                  
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
                <!-- end row -->

    
    <?php if(in_array(1,$app)){ ?>
                 <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">APPROVAL FOR SPEC & MSDS FILE</p>
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                     <th>Sr No.</th>
                                     <th>Type</th>
                                     <th>Product</th>
                                     <th>Competitor Product</th>
                                     <th>Spec File</th>
                                     <th>MSDS File</th>                                  
                                     <th>Added On</th>
                                     <th>Added By</th>
                                      <th>Approve</th>
                                     <th>Reject</th>
                                                                  
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
                <!-- end row -->


        <?php if(in_array(2,$app)){ ?>
                <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">QUOTATION PENDING FOR APPROVAL</p>
                                <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                <th>SR NO.</th>
                                <th>DATE</th>
                                <th>BILLING COMPANY NAME</th>
                                <th>CUSTOMER COMPANY NAME</th>
                                <th>CUSTOMER NAME</th>
                                <th>PRODUCT NAME</th>
                                <th>QTY</th>
                                <th>ALLOWED PRICE</th>
                                <th>OFFERED PRICE</th>
                                <th>ACTION</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">ORDERS PENDING FOR APPROVAL</p>
                                <table id="exampleORDERS" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                <th>SR NO.</th>
                                <th>DATE</th>
                                <th>BILLING COMPANY NAME</th>
                                <th>CUSTOMER COMPANY NAME</th>
                                <th>CUSTOMER NAME</th>
                                <th>PRODUCT NAME</th>
                                <th>QTY</th>
                                <th>ALLOWED PRICE</th>
                                <th>OFFERED PRICE</th>
                                <th>ACTION</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
                <!-- end row -->


                <?php if(in_array(3,$app)){ ?>
                  <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">CUSTOMER PENDING FOR PAYMENT TERM APPROVAL</p>
                                <table id="example2" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                <th>SR NO.</th>
                                <th>OUR COMPANY</th>
                                <th>CUSTOMER COMPANY NAME</th>
                                <th>CUSTOMER NAME</th>
                                <th>PREVIOUS TERM</th>
                                <th>PAYMENT TERM</th>
                                <th>ADDED BY</th>
                                <th>ADDED ON</th>
                                <th>ACTION</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <?php if(in_array(4,$app)){ ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">USER CONVEYANCE APPROVAL</p>
                            <table id="example3" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
                                    <th>PERIOD</th>
                                    <th>USER</th>
                                    <th>TYPE</th>
                                    <th>START READING</th>
                                    <th>END READING</th>
                                    <th>PER KM RATE</th>
                                    <th>NET KM(s)</th>
                                    <th>PETROL USED</th>
                                    <th>MISC CHARGES</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>VEHICLE AVERAGE</th>
                                    <th>VIEW BIFURCATION</th>
                                    <th>HOD STATUS</th>
                                
                                
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
                <!-- end row -->


                  <?php if(in_array(5,$app)){ ?>
                <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">PAYMENT ADJUSTMENT APPROVAL</p>
                                <table id="example5" class="table table-striped table-bordered manglesh">
                                <thead>
                                    <tr>
                                    <th>SR NO.</th>
                                    <th>ORDER DATE/INVOICE NO.</th>
                                    <th>SALES AGENT.</th>
                                    <th>BILLING COMPANY</th>
                                    <th>CUSTOMER COMPANY NAME</th>
                                    <th>CUSTOMER NAME</th>
                                    <th>PRODUCTS</th>
                                    <th>BASIC ORDER AMOUNT</th>
                                    <th>GST AMOUNT</th>
                                    <th>TOTAL ORDER AMOUNT</th>
                                    <th>TOTAL ORDER AMOUNT POST TDS DEDUCTION</th>
                                    <th>PAYMENT RECIEVED</th>
                                    <th>PAYMENT DUE</th>
                                    <th>ADJUSTMENT DETAILS</th>
                                   
                                    <th>ACTION</th>

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
                <!-- end row -->



                  <?php if(in_array(6,$app)){ ?>
                <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="col-md-8"><p style="font-size:14px;font-weight:bold;color:red;">ORDERS ON HOLD</p></div>
                            <div class="col-md-4">  <a href="<?php echo page_url;?>Billing/hold_history"> <span class="pull-right btn btn-warning btn-xs" target="_blank">History</span></a></div>
                            
                         
                                <table id="example6" class="table table-striped table-bordered manglesh">
                                <thead>
                                    <tr>
                                        <th>SR NO.</th>
                                        <th>HOLD TYPE</th>
                                        <th>ORDER SOURCE.</th>
                                        <th>ORDER DATE/INVOICE NO.</th>
                                        <th>SALES AGENT.</th>
                                        <th>BILLING COMPANY</th>
                                        <th>CUSTOMER COMPANY NAME</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>TAX DETAILS</th>
                                        <th>PRODUCTS</th>
                                       <!--  <th>SHIPPING ADDRESS</th>
                                        <th>BILLING ADDRESS</th> -->
                                        <th>PAYMENT TERMS</th>
                                        <th>PO DETAILS</th>
                                        <th>REMOVE ORDER ON HOLD</th>

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            <?php } ?>


              <?php if(in_array(7,$app)){ ?>
                <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">BULK PURCHASE (IN KG) DENSITY APPROVAL</p>
                                <table id="example7" class="table table-striped table-bordered manglesh">
                                <thead>
                                    <tr>
                                        <th>SR NO.</th>
                                        <th>PURCHASE DATE</th>
                                        <th>PARTY.</th>
                                    
                                        <th>PRODUCT</th>
                                        <th>ORIGINAL QTY</th>
                                        <th>DENSITY</th>
                                        <th>CONVERTED QTY</th>
                                        <th>APPROVE/EDIT DENSITY</th>

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
                <!-- end row -->

                <?php if(in_array(9,$app)){ ?>
                <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">TRAIL TO BE ASSIGN</p>
                                <table id="example8" class="table table-striped table-bordered manglesh">
                                <thead>
                                        <tr>
                                        <th>SR NO.</th>
                                        <th>CUSTOMER COMPANY NAME</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>TRIALS TO BE DONE</th>
                                        <th>REMARKS</th>
                                        <th>ADDRESS</th>
                                        <th>REQUEST RAISED BY/ON</th>
                                        <th>ASSIGN TO</th>
                                        </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
                <!-- end row -->








                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


        <div id="myModal_files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Leads/reject_upload" enctype="multipart/form-data">
                    <input type="hidden" name="record_id" id="record_id" value="">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Approve/Reject Discount</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Upload <span id="filetype"></span></label>
                                            <span id="" style="color:red;">*</span>
                                            <input type="file" name="upload" id="upload" class="form-control">
                                        </div>
                                    </div>

                                 
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  



             <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/save_discount_remarks/1" enctype="multipart/form-data">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Approve/Reject Discount</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Approve/Reject</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <input type="hidden" name="detail_id" id="detail_id">
                                            <select class="form-control" id="discount_approval" name="discount_approval" required="" onchange="check_for_remarks()">
                                                <option value="">SELECT</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Remarks</label>
                                            <span class="remove_mand" style="color:red;">*</span>
                                            <textarea class="form-control" name="remarks" id="remarks"></textarea>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  



              <div id="payment_approve_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/approve_customer_payment_terms">
                    <input type="hidden" name="payment_app_id" id="payment_app_id">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Payment Term Approval</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Company Name</label>
                                           <input type="text" name="cname" id="cname" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Payment Term</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <select class="form-control" id="payment_term" name="payment_term" required="" onchange="check_for_credit()">
                                                <option value="">SELECT</option>
                                                <option value="2">Cash</option>
                                                <option value="3">Online</option>
                                                <option value="4">PDC</option>
                                                <option value="5">CREDIT</option>
                                                <option value="6">ADVANCE</option>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function check_for_credit()
                                        {
                                             $("#credit_terms_div").css('display','none');
                                            $("#credit_terms").attr('required',false);
                                            var pay=$("#payment_term").val();
                                            if(pay==4 || pay==5)
                                            {
                                                $("#credit_terms_div").css('display','');
                                                $("#credit_terms").attr('required',true);
                                            }else
                                            {
                                                 $("#credit_terms_div").css('display','none');
                                                $("#credit_terms").attr('required',false);
                                            }

                                        }
                                    </script>
                                    <div class="col-md-4" id="credit_terms_div" style="display:none;">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Credit Days</label>
                                            <span class="remove_mand" style="color:red;">*</span>
                                            <input type="text" class="form-control" name="credit_terms" id="credit_terms">
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  



             <div id="payment_approve_modal_r" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/approve_reject_payment_terms_change_req">
                    <input type="hidden" name="payment_app_id_r" id="payment_app_id_r">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Payment Term Approval</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Company Name</label>
                                           <input type="text" name="cname_r" id="cname_r" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Payment Term</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <select class="form-control" id="payment_term_r" name="payment_term_r" required="" onchange="check_for_credit_r()">
                                                <option value="">SELECT</option>
                                                <option value="2">Cash</option>
                                                <option value="3">Online</option>
                                                <option value="4">PDC</option>
                                                <option value="5">CREDIT</option>
                                                <option value="6">ADVANCE</option>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function check_for_credit_r()
                                        {
                                             $("#credit_terms_div_r").css('display','none');
                                            $("#credit_terms_r").attr('required',false);
                                            var pay=$("#payment_term_r").val();
                                            if(pay==4 || pay==5)
                                            {
                                                $("#credit_terms_div_r").css('display','');
                                                $("#credit_terms_r").attr('required',true);
                                            }else
                                            {
                                                 $("#credit_terms_div_r").css('display','none');
                                                $("#credit_terms_r").attr('required',false);
                                            }

                                        }
                                    </script>
                                    <div class="col-md-4" id="credit_terms_div_r" style="display:none;">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Credit Days <span style="color:red">*</span></label>
                                            <span class="remove_mand" style="color:red;">*</span>
                                            <input type="text" class="form-control" name="credit_terms_r" id="credit_terms_r">
                                        </div>
                                    </div>
                                </div> 

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Approve/Reject? <span style="color:red">*</span></label>
                                            <select name="decision" id="decision" class="form-control" onchange="decision_data();" required>
                                                <option value="">Select</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function decision_data()
                                        {
                                           var decision= $("#decision").val();
                                           if(decision==2)
                                           {
                                            $("#rej_rmk").css('display','');
                                            $("#rej_remarks").attr('required',true);
                                           }else
                                           {
                                            $("#rej_rmk").css('display','none');
                                            $("#rej_remarks").attr('required',false);
                                           }
                                        }
                                    </script>
 
                                     <div class="col-md-9" id="rej_rmk" style="display:none">
                                        <div class="form-group">
                                            <label>Reject Remarks? <span style="color:red">*</span></label>
                                           <textarea class="form-control" name="rej_remarks" id="rej_remarks"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  


            <div id="payment_adjust_apporve" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/approve_adjustments">
                    <input type="hidden" name="adjust_order_id" id="adjust_order_id">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Payment Term Approval</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Company Name</label>
                                           <input type="text" name="cname_adr" id="cname_adr" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Invoice No</label>
                                           <input type="text" name="invoice_adr" id="invoice_adr" readonly class="form-control">
                                        </div>
                                    </div>


                                     <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Due Payment</label>
                                           <input type="text" name="due_payment" id="due_payment" readonly class="form-control">
                                        </div>
                                    </div>

                                 

                                </div> 

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Approve/Reject? <span style="color:red">*</span></label>
                                            <select name="decision_p" id="decision_p" class="form-control" onchange="decision_data_p();" required>
                                                <option value="">Select</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function decision_data_p()
                                        {
                                           var decision= $("#decision_p").val();
                                           if(decision==2)
                                           {
                                            $("#rej_rmk_p").css('display','');
                                            $("#rej_remarks_p").attr('required',true);
                                           }else
                                           {
                                            $("#rej_rmk_p").css('display','none');
                                            $("#rej_remarks_p").attr('required',false);
                                           }
                                        }
                                    </script>
 
                                     <div class="col-md-9" id="rej_rmk_p" style="display:none">
                                        <div class="form-group">
                                            <label>Reject Remarks? <span style="color:red">*</span></label>
                                           <textarea class="form-control" name="rej_remarks_p" id="rej_remarks_p"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  


             <div class="modal" id="order_on_hold" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Add Remarks</h4>
                    </div>
                   <form method="post" action="<?php echo page_url;?>Billing/remove_hold_remarks/1" enctype="multipart/form-data">

                     <div class="modal-body">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Remarks</label>
                              <span id="error_order_type" style="color:red;">*</span> 
                                  <input type="hidden" name="order_id" id="order_id">
                                   <input type="hidden" name="hold_type" id="hold_type">
                                  <textarea name="remove_hold_remarks" class="form-control" required=""></textarea>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Evidence</label>
                            
                                 
                                  <input type="file" name="Evidence" value="">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                      </div>
                  </form>
                </div>
              </div>
            </div>



             <div id="density_approve_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Billing/approve_density">
                    <input type="hidden" name="inventory_detail_id" id="inventory_detail_id">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Approve Bulk Purchase Product Density</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Product Name <span style="color:red">*</span></label>
                                           <input type="text" name="pname" id="pname" readonly class="form-control" required>
                                           <input type="hidden" name="prd_name_id" id="prd_name_id">
                                        </div>
                                    </div>


                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Purchased Qty (IN KGS) <span style="color:red">*</span></label>
                                           <input type="text" name="pur_qty" id="pur_qty" readonly class="form-control" required>
                                        </div>
                                    </div>


                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Density <span style="color:red">*</span></label>
                                           <input type="text" name="density" id="density" class="form-control" required onkeyup="calculate_qty();" readonly value="">
                                           <input type="hidden" name="original_density" id="original_density">
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function calculate_qty()
                                        {
                                          
                                           var pur_qty= $("#pur_qty").val();
                                           var density= $("#density").val();

                                           if(parseFloat(density)>0)
                                           {

                                           var converted=parseFloat(pur_qty)/parseFloat(density);
                                           //alert(converted);
                                           $("#con_qty").val(converted);
                                            }else
                                            {
                                            
                                                $("#con_qty").val('');
                                            }
                                        }
                                    </script>



                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Converted Qty (IN LTRS) <span style="color:red">*</span></label>
                                           <input type="text" name="con_qty" id="con_qty" readonly class="form-control" required>
                                        </div>
                                    </div>

                                 
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  



              <div id="myModal_eq_files" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Leads/reject_upload_eu" enctype="multipart/form-data">
                    <input type="hidden" name="record_id_eq" id="record_id_eq" value="">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">EDIT EQUIVALENT DATA</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label>OUR EQUIVALENT</label>
                                            <span id="error_mvalue" style="color:red;">*</span>
                                           
                                            <select class="select3" name="rack_location[]" multiple="" required="">
                                                <option value="">SELECT</option>
                                               <?php $qu=$this->db->select('id,instruments_name')->from('presto_instruments')->where('status',1)->get();
                                                if($qu->num_rows()>0){
                                                    foreach($qu->result() as $row)
                                                    {
                                                    ?>
                                                    <option value="<?php echo $row->instruments_name;?>"><?php echo $row->instruments_name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                 
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  


              <div id="myLeadModal_discount" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/save_lead_discount_remarks" enctype="multipart/form-data">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Approve/Reject Discount</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Approve/Reject</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <input type="hidden" name="lead_detail_id" id="lead_detail_id">
                                            <select class="form-control" id="lead_discount_approval" name="lead_discount_approval" required="" onchange="check_for_lead_remarks()">
                                                <option value="">SELECT</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Remarks</label>
                                            <span class="remove_mand" style="color:red;">*</span>
                                            <textarea class="form-control" name="lead_remarks" id="lead_remarks"></textarea>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal --> 



            <div id="reject_hold" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Leads/reject_hold" enctype="multipart/form-data">
                    <input type="hidden" name="hold_order_id" id="hold_order_id" value="">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Approve/Reject Discount</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Reject Remarks <span id="filetype"></span></label>
                                            <span id="" style="color:red;">*</span>
                                            <textarea name="reject_hold_rmk" id="reject_hold_rmk" class="form-control" required></textarea>
                                        </div>
                                    </div>

                                 
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->  


            <div id="trailapprovalbox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Customer/customer_assignment_for_trail_for_newone/" enctype="multipart/form-data">
                    <input type="hidden" name="trailorderid" id="trailorderid" value="">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Approval Confirmation</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Approval Status<span id="filetype"></span></label>
                                            <span id="" style="color:red;">*</span>
                                           <select class="form-control" name="approvalstatus" id="approvalstatus" onchange="changeapprovalstatus();">
                                               <option value="">Select Option</option>
                                               <option value="1">Yes</option>
                                               <option value="0">No</option>
                                           </select>
                                        </div>
                                    </div>
                                    <?php 
                                    $q11 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',7)->where('user_status',1)->get();
                                    ?>

                                     <div class="col-md-12" id="hideusers" style="display:none">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Select User to Assign<span id="filetype"></span></label>
                                            <span id="" style="color:red;">*</span>
                                           <select class="form-control" name="assigntousers" id="assigntousers">
                                               <option value="">Select User to Assign</option>
                                               <?php 
                                                foreach($q11->result() as $rowssss){
                                               ?>
                                               <option value="<?php echo $rowssss->user_id;?>"><?php echo $rowssss->first_name." ".$rowssss->last_name;?></option>
                                           <?php }?>
                                             
                                           </select>
                                        </div>
                                    </div>

                                     <div class="col-md-12" id="approvalremarksdiv" style="display:none">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Remarks<span id="filetype"></span></label>
                                            <span id="" style="color:red;">*</span>
                                          <textarea class="form-control" name="approvalremarks" id="approvalremarks"></textarea>
                                        </div>
                                    </div>
                                 
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div>




            <div id="changeowner" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                        <form id="loginForm" method="post" action="<?php echo page_url;?>Leads/bulk_equivalent_action">
                                <input type="hidden" name="items" value="" id="items" required>
                                <input type="hidden" name="action_type" id="action_type" value="" required>
                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Take Action On Equivalent Chart Data</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">


                                                 <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Action Type</label>
                                                        <input type="text" class="form-control" name="actype" id="actype" value="" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>No of Record Selected</label>
                                                        <input type="text" class="form-control" name="noofitemsselected" id="noofitemsselected" value="" readonly>
                                                    </div>
                                                </div>

                                          

                                            </div>

                                            

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>

                                            <input type="submit" id="save_filter" class="btn btn-info" value="Submit"> 

                                        </div>

                                    </div>

                                </div>

                                </form>

                            </div><!-- /.modal -->



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
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

    


<?php if(in_array(8,$app)){ ?>   
<script>
$( document ).ready(function() {

$('#exampleequivalant').dataTable({
"bProcessing": true,
"pagination":true,
"pagination":true,
"pageLength": 100,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>Leads/equivalent_file_approval",

"aoColumns": [
 { mData: 'check' } ,
                { mData: 'sr_no' } ,
               
                { mData: 'competitor_prd' },
                { mData: 'product' },               
                { mData: 'addedon' }, 
                { mData: 'addedby' },               
                { mData: 'approve' },
                { mData: 'reject' }

               
                           

             
                
                
        ]
});  

});


 $(document).on('change', '#selectAll1', function(e) {
            if ($(this).prop('checked')) {
                $('.checkBoxClass').prop('checked', true);
                $("#showactiondiv").css('display','');
            } else {
                $('.checkBoxClass').prop('checked', false);
                $("#showactiondiv").css('display','none');
            }
        });
</script>
<?php } ?>


 <?php if(in_array(1,$app)){ ?>   
<script>
$( document ).ready(function() {

$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"pagination":true,
"pageLength": 100,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>Leads/specfile_approval",

"aoColumns": [
				{ mData: 'sr_no' } ,
                { mData: 'type' },
                { mData: 'product' },
                { mData: 'competitor_prd' },
                { mData: 'specfile' },
                { mData: 'msdsfile' },                
                { mData: 'addedon' }, 
                { mData: 'addedby' },               
                { mData: 'approve' },
                { mData: 'reject' }

               
                           

             
				
				
		]
});  

});
</script>
<?php } ?>

<?php if(in_array(2,$app)){ ?>
<script type="text/javascript">

$( document ).ready(function() {
$('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

"sAjaxSource": "<?php echo page_url;?>Customer/discount_approval_list_only_quote",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'date' },
                { mData: 'company_name' },
                { mData: 'cust_company_name' },
                { mData: 'customer_name' },
                { mData: 'product_name' },
                { mData: 'qty' },
                { mData: 'allowed_price' },
                { mData: 'list_price' },
                { mData: 'action' }
            ]

        });



        $('#exampleORDERS').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

"sAjaxSource": "<?php echo page_url;?>Customer/discount_approval_list_only_order",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'date' },
                { mData: 'company_name' },
                { mData: 'cust_company_name' },
                { mData: 'customer_name' },
                { mData: 'product_name' },
                { mData: 'qty' },
                { mData: 'allowed_price' },
                { mData: 'list_price' },
                { mData: 'action' }
            ]

        }); 

        });   


</script>
<?php } ?>

<?php if(in_array(3,$app)){ ?>
<script type="text/javascript">
$( document ).ready(function() {
$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

"sAjaxSource": "<?php echo page_url;?>Customer/payment_term_approval",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'company_name' },
                { mData: 'cust_company_name' },
                { mData: 'customer_name' },
                { mData: 'previous' },
                { mData: 'payment_term' },
                { mData: 'addedOn' },
                { mData: 'addedBy' },
                { mData: 'action' }
            ]

        });   
        });   

</script>
<?php } ?>

<?php if(in_array(4,$app)){ ?>
<script type="text/javascript">
    $( document ).ready(function() {
$('#example3').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/approval_for_admin/1",
 "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'period' },
                        { mData: 'user' },
                        { mData: 'type' },
                        { mData: 'start_reading' },
                        { mData: 'end_reading' },
                        { mData: 'per_km_rate' },
                        { mData: 'net_km' },
                        { mData: 'petrol_used' },
                        { mData: 'misc_charges' },
                        { mData: 'total_amount' },
                        { mData: 'average' },
                        { mData: 'bifurcation' },
                        { mData: 'status' }

            
                        
                ]
        }); 
        }); 


</script>
<?php } ?>


<?php if(in_array(5,$app)){ ?>
<script type="text/javascript">
    $( document ).ready(function() {
$('#example5').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

 "sAjaxSource": "<?php echo page_url;?>Billing/pending_order_payment_adjustment_for_approval",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'invoice_no' },
               { mData: 'agent' },
               { mData: 'billing_company' },
               { mData: 'company_name' },
               { mData: 'customer_name' },
               { mData: 'products' },
               { mData: 'basic_order_amount' },
               { mData: 'gst_order_amount' },
               { mData: 'total_order_amount' },
               { mData: 'order_amount_after_tds' },
               { mData: 'payment_recvd' },
               { mData: 'payment_due' },
               { mData: 'adjustment' },
            
                { mData: 'action' }
             

               

                ]

        }); 
        }); 


</script>
<?php } ?>



<?php if(in_array(6,$app)){ ?>
<script type="text/javascript">
    $( document ).ready(function() {
$('#example6').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

 "sAjaxSource": "<?php echo page_url;?>Billing/orders_on_hold_list",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'hold_type' },
               { mData: 'source' },
               { mData: 'invoice_no' },
               { mData: 'agent' },
               { mData: 'billing_company' },
               { mData: 'company_name' },
               { mData: 'customer_name' },
               { mData: 'taxdetail' },
               { mData: 'products' },
               // { mData: 'shipaddress' },
               // { mData: 'billingaddress' },
               { mData: 'paymentterm' },
               { mData: 'po_details' },
               { mData: 'remove_hold' }
                // { mData: 'payment_collection' }

                ]

        });
        }); 


</script>
<?php } ?>


<?php if(in_array(7,$app)){ ?>
<script type="text/javascript">
    $( document ).ready(function() {
$('#example7').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

 "sAjaxSource": "<?php echo page_url;?>Billing/purchase_density_approval",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'purchase_date' },
               { mData: 'party' },
               { mData: 'product' },
               { mData: 'qty' },
               { mData: 'density' },
               { mData: 'converted' },
             
               { mData: 'approve' }

                ]


        });
        }); 


</script>
<?php } ?>

<?php if(in_array(9,$app)){ ?>
<script type="text/javascript">
    $( document ).ready(function() {
$('#example8').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

"sAjaxSource": "<?php echo page_url;?>Trail/trial_request_for_assign",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                { mData: 'products' },
                { mData: 'remarks' },
                { mData: 'shipaddress' },
                { mData: 'raisedby' },
                { mData: 'status' }

                ]


        });
        }); 


</script>
<?php } ?>


<script type="text/javascript">

        $(document).ready(function() {
             $('#start').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
             });
             $('#end').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
             });
        }); 

function assign_to_trail(id, tableid){
              var employee_id = $("#employeename" + tableid).val();
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Customer/customer_assignment_for_trail_for_new",
                data: "customer_id=" + id + "&employee_id=" + employee_id,
                success: function(data) {
                   
                    $("#datasuccess"+id).html(data);
                }
            });
        }


function approve_data(id)
{
    if(confirm('Do you really want to approve?'))
    {
        document.location="<?php echo page_url;?>Leads/approve_files/"+id;

    }

}

function reject_n_upload(id)
{
   

     $.ajax({
      url: '<?php echo page_url; ?>Leads/get_files_data/'+id,
      type: 'get',
      success: function(data) {
        
        $("#filetype").text(data);
         $("#myModal_files").modal('show');
         $("#record_id").val(id);
      
      }
    });

}


function accept_reject_remarks(detail_id) {
  $('#detail_id').val(detail_id);
  $('#myModal').modal('show');
}

function approve_terms(custid)
{

      $.ajax({
      url: '<?php echo page_url; ?>Customer/get_payment_term/'+custid,
      type: 'get',
      success: function(data) {
        var d=data.split("|");
        $("#payment_approve_modal").modal('show');
        $("#payment_app_id").val(custid);
        if(d[0]!='')
        {
             $('#payment_term').val(d[0]);
             check_for_credit();
        }

        if(d[1]!='')
        {
             $("#credit_terms").val(d[1]);
        }
        if(d[2]!='')
        {
            $("#cname").val(d[2]);
        } 


      }
    });

    

}



function approve_terms_request(custid)
{

      $.ajax({
      url: '<?php echo page_url; ?>Customer/get_requested_payment_term/'+custid,
      type: 'get',
      success: function(data) {
        var d=data.split("|");
        $("#payment_approve_modal_r").modal('show');
        $("#payment_app_id_r").val(custid);
        if(d[0]!='')
        {
             $('#payment_term_r').val(d[0]);
             check_for_credit_r();
        }

        if(d[1]!='')
        {
             $("#credit_terms_r").val(d[1]);
        }
        if(d[2]!='')
        {
            $("#cname_r").val(d[2]);
        } 


      }
    });

    

}


function payment_adjustment_decision(orderid,due)
{

     $.ajax({
      url: '<?php echo page_url; ?>Billing/getcustomerdata/'+orderid,
      type: 'get',
      success: function(data) {
       
        $("#payment_adjust_apporve").modal('show');
        var d=data.split('|');
        if(d[0]!='')
        {
             $("#cname_adr").val(d[0]);
        }

         if(d[1]!='')
        {
             $("#invoice_adr").val(d[1]);
        }

        $("#due_payment").val(due);
        $("#adjust_order_id").val(orderid);
        
         
    
      
      }
    });

    

}

 function remove_hold(id,type) {
    $("#order_on_hold").modal('show');
    $("#order_id").val(id);
    $("#hold_type").val(type);

  }

  function approve_density(id)
  {


    $.ajax({
      url: '<?php echo page_url; ?>Billing/get_inventory_details/'+id,
      type: 'get',
      success: function(data) {
        var d=data.split("|");

         $("#inventory_detail_id").val(id);
         $("#pname").val(d[0]);
         $("#pur_qty").val(d[1]);
         $("#density").val(d[2]);
         $("#con_qty").val(d[3]);
         $("#original_density").val(d[2]);
         $("#prd_name_id").val(d[4]);
          $("#density_approve_modal").modal('show');
      
      }
    });

   
  }


  function approve_data_equ(id)
{
    if(confirm('Do you really want to approve?'))
    {
        document.location="<?php echo page_url;?>Leads/approve_equivalent/"+id;

    }

}

function reject_n_upload_equ(id)
{
   
      
         $("#myModal_eq_files").modal('show');
         $("#record_id_eq").val(id);
      


}

function accept_reject_lead_remarks(detail_id) {
  $('#lead_detail_id').val(detail_id);
  $('#myLeadModal_discount').modal('show');
}



function reject_hold(orderid)
{

  $('#hold_order_id').val(orderid);
  $('#reject_hold').modal('show');

}
function trailapprovalbox(orderid){
     $('#trailorderid').val(orderid);
  $('#trailapprovalbox').modal('show');

}

function changeapprovalstatus(){
    $("#hideusers").css('display','none');
    $("#approvalremarksdiv").css('display','');
    var approvalstatus = $("#approvalstatus").val();
    if(approvalstatus==1){
        $("#hideusers").css('display','');
        $("#approvalremarksdiv").css('display','none');
        $("#assigntousers").attr('required',true);
        $("#approvalremarks").attr('required',false);
    }else{
         $("#hideusers").css('display','none');
         $("#approvalremarksdiv").css('display','');
         $("#approvalremarks").attr('required',true);
         $("#assigntousers").attr('required',false);
    }
}


 function takeaction(){

            var action = $('#action').val();
            var selecteditem = $.map($(':checkbox[name=myCheckboxes\\[\\]]:checked'), function(n, i){
    return n.value;
}).join(',');

            if(action==1 || action==2)
            {
                if(selecteditem!=''){
                $("#items").val(selecteditem);
                var noofitemsselected = selecteditem.split(',');
                var countitem = noofitemsselected.length;
                $("#noofitemsselected").val(countitem);
                $("#action_type").val(action);
                $("#changeowner").modal('show');
                if(action==1)
                {
                    $("#actype").val('Mark Inactive');
                  
                }else if(action==2)
                {
                     $("#actype").val('Mark Approved');
                }
            }else{
                alert('No Item selected');
                $("#action").val('');
                $("#noofitemsselected").val();
                 $("#noofitemsselected").val();
                $("#action_type").val();
            }
            }
            

            }
</script>
 <script>
        $(document).ready(function() {
           
            $('.select3').select2();
        }); //document ready
    </script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
         

<script language="javascript" type="text/javascript">   
jQuery.noConflict();

</script>


    </body>
</html>