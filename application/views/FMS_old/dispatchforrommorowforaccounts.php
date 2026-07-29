<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> PENDING TO BILLING</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
		<style>
		
table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-size:11px;
				font-weight:bold;
			}
table tbody tr td {
  font-size: 11px;
  color:#000;
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
</style>
    </head>
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						<?php 
						$user_id =$this->session->userdata['logged_in']['user_id'];
						?>
                           <!--<a href="<?php echo page_url;?>Form/data_report/6/<?php echo $user_id;?>" target="_blank"><span class="btn btn-success btn-xs pull-right">Service Dispatch for Tomorrow</span></a>-->
                            <h4 class="page-title text-center">ORDERS READY FOR BILLING</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
							<th>Sr No.</th>
							<th>INTERNAL ORDER NUMBER</th>
							<th>COMPANY NAME</th>
              <th>GST NO.</th>
							<th style="width:10%">INSTRUMENTS</th>

							<th>NO OF PACKETS</th>
							<th>TRANSPORTER NAME</th>
							<th>TRANSPORTER TYPE</th>
							<th>TEMPO NO. (IF ANY)</th>

							<th>FREIGHT TYPE</th>
							<th>PAYMENT TERMS</th>


							<th>SO NO.</th>
							<th>PO COPY</th>
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
                        </div>
                        
            
 
 
                    </div>
                </div>
                
                
             







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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		
	
	  <script>
	  function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }
	  </script>
		<script>
		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
		</script>
 <script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
 function initializeSelect2() {
    $('.select3').select2({ });
  }
  
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3 
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/dispatchfortommorow_order_listforaccounts/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'internal_order_no' },
						{ mData: 'company_name' },
            { mData: 'gst' },
						{ mData: 'itemname' },

						{ mData: 'packet' },
						{ mData: 'transporter' },
						{ mData: 'transportertype' },
						{ mData: 'tempono' },

						{ mData: 'freigntcharges' },
						{ mData: 'payment_terms' },

						{ mData: 'sono' },
						{ mData: 'pocopy' }
                    
                    
						
						
                ]
        });  
        
         $('#example1').dataTable({
 "bProcessing": false,
 "pagination":true
 });
  $('#example3').dataTable({
 "bProcessing": false,
 "pagination":true
 });
});



function markstagedone(orderid,internalorderno,ser)
{
	$("#myModal").modal('show'); 
	$("#setiono").html(internalorderno);

	/** AJAX TO GET SELECTED DETAILS **/

		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>Reporting/getfilleddetails",
		data:"internalorderno="+internalorderno,
		success:function(data){
		var array = data.split("|");
	
		var shipment=array[1];

		var totpack=array[4];
		var dockewtn=array[2];
		$("#shipment").html('<option value="'+shipment+'">'+shipment+'</option>');
		$("#totpack").val(totpack);
		$("#totpack").attr('readonly',true);
		$("#docketnumber").val(dockewtn);



		}
		});

	/** END **/


	/**$("#odid").val(internalorderno);
	$("#ser").val(ser); **/
	var url="<?php echo page_url;?>Reporting/markfinalpacked/"+orderid+"/"+ser;
	 $('#popfrm').attr('action', url);
	
	
}



function markstagedoneoLDD(orderid,compyname,internalorderno,ser)
{
	if(confirm('Confirm you want to close following order? \nCustomer '+compyname+'\n Internal Order No. '+internalorderno))
	{
		document.location="<?php echo page_url;?>Reporting/markfinalpacked/"+orderid+"/"+ser;
		return true;
		
	}else
	{
		return false;
	}
}


function validateForm() {
  var isValid = true;
  $('.mand').each(function() {
    if ($(this).val() =='' )
        isValid = false;
  });

	if(isValid==false)
	{
		alert('All Fields are required');
	return isValid;

	}else
	{
	return isValid;
	}
}


$(".allow_decimal").on("input", function(evt) {
   var self = $(this);
   self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }
 });


(".allow_numeric").on("input", function(evt) {
    var self = $(this);
    self.val(self.val().replace(/[^\d].+/, ""));
    if ((evt.which < 48 || evt.which > 57)) 
     {
	   evt.preventDefault();
     }
 });


function updatedetailsforaccount(orderid,iono,random)
{

$("#myModalforaccounts").modal('show');
$("#orderid").val(orderid);
$("#iono").val(iono);
$("#setiono1").html(iono);


}
</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
</body>
</html>
