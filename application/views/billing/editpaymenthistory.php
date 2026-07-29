<?php 
$q = $this->db->select('a.id as customerpart,b.type as billtype,b.bills,b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy,c.company_name')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->join('customer_detail c','b.customer_id=c.id')->where('a.id',$this->uri->segment(3))->get();
foreach($q->result() as $row);


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Edit Payment History</title>

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
		<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

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
                           
                            <h4 class="page-title">Edit Payment History </h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

					<form method="post" action="<?php echo page_url;?>Master/Saleszone/update_zone/"  onsubmit="return validateme();">
								
                   		 <div class="row add_payment">
                   
                    <div class="col-md-12 text-center">
                    <div class="col-md-2">
                        <label>Payment Type<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <select class="form-control" name="payment_type" id="payment_type" onchange="getPaymentInfo();" required>
                          <option value="">SELECT PAYMENT TYPE</option>
                          <option value="1" <?php if($row->billtype==1){echo "selected";}?>>Cheque</option>
                          <option value="2" <?php if($row->billtype==2){echo "selected";}?>>Cash</option>
                          <option value="3" <?php if($row->billtype==3){echo "selected";}?>>NEFT</option>
                        </select>
                        </span>
                      </div>
                      <div id="show_cheque_no" style="display: none;">
                        <div class="col-md-3">
                            <label>Cheque No<span style="color: red;">*</span></label>
                            <span class="input-icon icon-right" style="margin-bottom:10px">
                            <input type="text" class="form-control" name="cheque_no" id="cheque_no0" autocomplete="nope">
                            </span>
                        </div>
                        <div class="col-md-2">
                            <label>Cheque Date<span style="color: red;">*</span></label>
                            <span class="input-icon icon-right" style="margin-bottom:10px">
                            <input type="date" class="form-control" name="cheque_date" id="cheque_date0" autocomplete="nope">
                            </span>
                        </div>
                      </div>
                      <div class="col-md-3" id="show_trans_no" style="display: none;">
                        <label>Transaction No.<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <input type="text" class="form-control" name="transaction_no" id="transaction_no0" autocomplete="nope">
                        </span>
                      </div>
                      <div class="col-md-4">
                        <label>Amount<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <input type="text" class="form-control" name="total_amount" value="<?php echo $row->amount;?>" id="total_amount0" autocomplete="off" required>
                        </span>
                      </div>
                     
                </div>

				<div class="col-md-9"></div>
				<div class="col-md-3">
				<div class="form-group pull-right" style="padding-top:24px;">
				<label>&nbsp;</label>
				<input type="submit" class="btn btn-success" value="Update">
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
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
  pageLength:50,
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
		<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>


<script language="javascript" type="text/javascript">   
function validateme()
{

var zone = $("#zone").val();
if(zone=='')
{
	$("#error_holidayname").html('Required!');
	
}

var assuser = $("#assuser").val();
if(assuser=='' || assuser==null)
{
	$("#error_datepicker1").html('Required!');

}


if(zone=='' || assuser=='' || assuser==null)
{
	
	return false;
}

}
</script>
<script>
	 function areAnyChecked(formID) {
  return !!$('#'+formID+' input[type=checkbox]:checked').length;
}


    function showCustomerOrders(comp) {
        var cust_id = $('#customers').val();
        $('#show_orders').css('display', '');
    
        location.href = '<?php echo page_url;?>Open_leads/getCustomerOrders/<?php echo $user_id;?>/'+cust_id+"/"+comp;
    }

    function getPaymentInfo() {
    	alert('test');
          var payment_type = $("#payment_type").val();
          $("#show_cheque_no").css('display', 'none');
          $("#cheque_no").removeClass('mand');
          $("#show_trans_no").css('display', 'none');
          $("#transaction_no").removeClass('mand');

          if (payment_type == 1) {
            $("#show_cheque_no").css('display', '');
            $("#cheque_no").addClass('mand');
          } else if (payment_type == 3) {
            $("#show_trans_no").css('display', '');
            $("#transaction_no").addClass('mand');
          }
        }

        function check_adjustment()
        {
            var ad=$("#adjust").val();
            if(ad==1)
            {
                $(".selectbill").attr('disabled',true);

            }else
            {
                $(".selectbill").attr('disabled',false);
            }

        }
</script>
    </body>
</html>