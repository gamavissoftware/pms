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
                        <h4 class="page-title text-center">YOUR PENDING APPROVALS FROM ADMIN</h4>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>


  
             <!--     <div class="row">

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
                                    
                                                                  
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div> -->
           
                <!-- end row -->


       
                <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">QUOTATION PENDING FOR APPROVAL</p>
                                <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                <th>SR NO.</th>
                                <th>BILLING COMPANY NAME</th>
                                <th>CUSTOMER COMPANY NAME</th>
                                <th>CUSTOMER NAME</th>
                                <th>PRODUCT NAME</th>
                                <th>QTY</th>
                                <th>ALLOWED PRICE</th>
                                <th>OFFERED PRICE</th>
                               
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
          
                <!-- end row -->


               
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
                                <th>ADDED ON</th>
                                <th>ADDED BY</th>


                

                               
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>
           


           
               <!--  <div class="row">
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
                                  
                                
                                
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> -->
          
                <!-- end row -->


                 
             <!--    <div class="row">

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
                                   

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div> -->
           
                <!-- end row -->



                 
               <!--  <div class="row">

                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">ORDERS ON HOLD</p>
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
                                        <th>SHIPPING ADDRESS</th>
                                        <th>BILLING ADDRESS</th>
                                        <th>PAYMENT TERMS</th>
                                        <th>PO DETAILS</th>
                                       

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
                                      

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div> -->
            
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
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    

<script>
$( document ).ready(function() {

$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"pagination":true,
"pageLength": 100,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>Leads/specfile_approval_user",

"aoColumns": [
				{ mData: 'sr_no' } ,
                { mData: 'type' },
                { mData: 'product' },
                { mData: 'competitor_prd' },
                { mData: 'specfile' },
                { mData: 'msdsfile' },                
                { mData: 'addedon' }, 
                { mData: 'addedby' }

               
                           

             
				
				
		]
});  

});
</script>


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

"sAjaxSource": "<?php echo page_url;?>Customer/discount_approval_list_user",

 "aoColumns": [

                { mData: 'sr_no' },
                { mData: 'company_name' },
                { mData: 'cust_company_name' },
                { mData: 'customer_name' },
                { mData: 'product_name' },
                { mData: 'qty' },
                { mData: 'allowed_price' },
                { mData: 'list_price' }
            ]

        }); 

        });   


</script>

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

"sAjaxSource": "<?php echo page_url;?>Customer/payment_term_approval_user",
"aoColumns": [

                { mData: 'sr_no' },
                { mData: 'company_name' },
                { mData: 'cust_company_name' },
                { mData: 'customer_name' },
                { mData: 'previous' },
                { mData: 'payment_term' },
                { mData: 'addedOn' },
                { mData: 'addedBy' }
        ]

        });   
        });   

</script>

<script type="text/javascript">
    $( document ).ready(function() {
$('#example3').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/approval_for_user/1",
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
                        { mData: 'view' }

            
                        
                ]
        }); 
        }); 


</script>

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

 "sAjaxSource": "<?php echo page_url;?>Billing/pending_order_payment_adjustment_for_approval_user",

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
               { mData: 'adjustment' }
             

               

                ]

        }); 
        }); 


</script>

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

 "sAjaxSource": "<?php echo page_url;?>Billing/orders_on_hold_list_user",

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
               { mData: 'shipaddress' },
               { mData: 'billingaddress' },
               { mData: 'paymentterm' },
               { mData: 'po_details' }

                ]

        });
        }); 


</script>



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

 "sAjaxSource": "<?php echo page_url;?>Billing/purchase_density_approval_user",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'purchase_date' },
               { mData: 'party' },
               { mData: 'product' },
               { mData: 'qty' },
               { mData: 'density' },
               { mData: 'converted' }
                ]


        });
        }); 


</script>



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
</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();

</script>


    </body>
</html>