<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$interest_due=$CI->Salescrm_model->payment_late_with_interest($this->uri->segment(3),date('Y-m-d',strtotime($this->uri->segment(4))),date('Y-m-d',strtotime($this->uri->segment(5))));
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

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                           
                            <h4 class="page-title text-center">LATE PAYMENTS & INTEREST CHARGES</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                
                 <form method="post" action="<?php echo page_url;?>Approval/filter_payment_due_late">
                            <div class="card-box col-md-12">
                                <div class="">
                                   
                                
                                 <div class="col-md-3"></div>
                                    <div class="col-md-3" style="display: none;">
                                        <div class="form-group">
                                            <label>Filter by Vendor/Party</label>
                                            <select class="form-control" name="vendor" required>
                                                <option value="ALL" <?php if($this->uri->segment(3)=='' || $this->uri->segment(3)=='ALL'){?> selected <?php } ?>>ALL</option>
                                                <?php $res=$this->db->select('id,name')->from('vendors')->get();
                                                if($res->num_rows()>0)
                                                 {
                                                        foreach($res->result() as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($this->uri->segment(3)==$row1->id){?> selected <?php } ?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Start Date <span style="color:red;">*</span></label>
                                            <input type="date" name="start_date" id="start_date" required class="form-control" value="<?php echo $this->uri->segment(4);?>" max="<?php echo date('Y-m-d');?>">
                                        </div>
                                    </div>
                                       <div class="col-md-3">
                                        <div class="form-group">
                                            <label>End Date <span style="color:red;">*</span></label>
                                            <input type="date" name="end_date" id="end_date" required class="form-control" value="<?php echo $this->uri->segment(5);?>" max="<?php echo date('Y-m-d');?>">
                                        </div>
                                    </div>

                                    
                                  
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                                </div>
                            </div>
                        </form>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>

                          <div class="col-md-4 card-box">
                            <h4 class="text-center">Total Interest Charges</h4>
                            <p style="color:red;font-weight: bold;font-size: 22px;text-align: center;"><?php echo $interest_due;?></p>
                        </div>
                    </div>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                               
                                    <th>Sr No.</th>
                                    <th>Purchase Date</th>
                                    <th>Bill No.</th>
                                    <th>Vendor</th>
                                    <th>Payment Term/Credit Day</th>                       
                                    <th>Product Detail</th>   
                                    <th>Basic Amount</th>  
                                    <th>Total Amount With GST</th>
                                     <th style="width:100px;">Due Date</th> 
                                     <th style="width:100px;">Interest %</th> 
                                    <th>Payment Details</th>
                                    <th>Total Interest</th>
                                   
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
"sAjaxSource": "<?php echo page_url;?>Approval/payment_due_late_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",
"aoColumns": [


  { mData: 'sr_no' } ,
                { mData: 'purchase_date' },
                { mData: 'bill_no' },
                { mData: 'party' },
                { mData: 'credit_days' },
                { mData: 'product_detail' },
                { mData: 'basic_amount' },
                { mData: 'total_amount' },
                 { mData: 'due_date' },
                 { mData: 'interest_per' },
                { mData: 'payment_details' },
                { mData: 'total_interest' }
              



                // { mData: 'sr_no' } ,
                // { mData: 'current_date' },
                // { mData: 'hpcl_location' },
                // { mData: 'vendor' },
                // { mData: 'bill_no' },
                // { mData: 'product_details' },
                // { mData: 'payment_terms' },
                // { mData: 'credit_period' },
            
                // { mData: 'csra_export' },
                // { mData: 'interest' },
                // { mData: 'total_amount' }
                
                
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