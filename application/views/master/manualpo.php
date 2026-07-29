<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Units</title>

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
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
    <style>
table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
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
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
             
<div class="card-box">
 <form method="post" action="<?php echo page_url;?>FMS/add_po" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Select Internal Order No</label>
				<span id="error_holidayname" style="color:red;"></span>
                <select class="form-control internal_order_no" name="internal_order_no">
                        <!-- <option value="">Select State</option> -->
                </select>
            </div>
        </div>
			<div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">SO Number</label>
				<span id="error_sonumber" style="color:red;"></span>
                <input type="text" id="sonumber" name="sonumber" class="form-control"required>
            </div>
        </div> 

		 <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Upload PO</label>
				<span id="error_datepicker1" style="color:red;"></span>
                <input type="file" id="fileupload" name="fileupload" class="form-control"required>
            </div>
        </div>     											
    </div>
    <div class="row">
       <div class="col-md-4"> 
            <input type="submit" value="Submit" class="btn btn-success">
        </div>
    </div>
    </form>
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

<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
		
 <script>
$( document ).ready(function() {

    var allNo = "<?php echo page_url;?>FMS/getInternalOrderNo/";

    $('.internal_order_no').select2({ 
        placeholder: 'TYPE TO SELECT',
        minmumInputLength:4,
        allowClear: true,

        ajax: {
          url: allNo,
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
	
// $('#example').dataTable({
//  "bProcessing": false,
//  "pagination":true,
//  "sAjaxSource": "<?php echo page_url;?>Master/Units/unit_list/",
//  "aoColumns": [
// 						{ mData: 'sr_no' } ,
//                         { mData: 'unit' },
// 						{ mData: 'short_name' },
//                         { mData: 'edit' }
						
//                 ]
//         });   
});

</script>

<script language="javascript" type="text/javascript">   
function validateme()
{

var shortname1 = $("#shortnames").val();
if(shortname1=='')
{
	$("#error_datepicker1").html('Required!');
	
}

var unitname = $("#unitname").val();
if(unitname=='')
{
	$("#error_holidayname").html('Required!');

}


if(unitname=='' || shortname1=='')
{
	
	return false;
}

}
</script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
<script> $(document).ready(function() {


                $("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })

            });</script>
</body>
</html>
