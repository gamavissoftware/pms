<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Salesforce Order Details</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
    <style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
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
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}

.addMore {
margin-top: 22px;
}

.remove {
margin-top: 22px;
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
 <div class="card-box">
    <?php echo $this->session->flashdata('message'); ?>
    <h3 style="text-align: center;">Salesforce Order</h3>
 <form method="post" action="<?php echo page_url;?>Serviceissue/add_request" id="frm" onsubmit="return validate()">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">State</label>
                <span id="error_order_type" style="color:red;">*</span>
                <select class="form-control states" name="state" id="state" required="">
                </select>
            </div>
        </div> 
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">GST Shipping</label>
                <span id="error_order_type" style="color:red;">*</span>
                <input type="text" id="gst_shipping" name="gst_shipping" class="form-control">
            </div>
        </div> 
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">GST Billing</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="gst_billing" name="gst_billing" class="form-control">
            </div>
        </div> 
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">PAN Shipping</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="pan_shipping" name="pan_shipping" class="form-control">
            </div>
        </div> 
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">PAN Billing</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="pan_billing" name="pan_billing" class="form-control">
            </div>
        </div> 

        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Road Permit</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="road_permit" name="road_permit" class="form-control">
            </div>
        </div> 

        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Part No.</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="part_no" name="part_no" class="form-control">
            </div>
        </div> 
    </div>
  
    <div class="row">
       <div class="col-md-4"> 
            
            <input type="submit" value="Submit" id="sub" class="btn btn-success">
        </div>
    </div>
    </form>
</div>
</div>
                            <!-- /.modal -->


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
        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
		
 <script>
$( document ).ready(function() {
  $(".mand").attr('required', false);	

    var state_url = "<?php echo page_url;?>FMS/getStates";

    $('.states').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,

        ajax: {
          url: state_url,
          dataType: 'json',
          delay: 250,

          processResults: function (data) {
            return {
              results: data
            };

          },

          cache: true

        }

    });
  
});

</script>

<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

</script>

      <script>

function validate()
{
  $("#sub").attr('disabled',true);
  $("#sub").val('Please Wait..');
 var isValid=0;
 $(".mand").each(function() {
   var element = $(this).val();
  if (element=="") {
     
    isValid=1;
   }
});
 
 
 if(isValid==0)
 {
  $("#sub").attr('disabled',true);
  $("#sub").val('Please Wait..');
     return true;
 }else
 {
  $("#sub").attr('disabled',false);
  $("#sub").val('Submit');
     alert('All Fields marked with * are mandatory');
     return false;
 }
    
}
</script>
</body>
</html>
