<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Checklist</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
            .requiredclass{
                border: 2px solid #E12830;
            }
        </style>
		<style>
table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-weight:bold;
			}

body{margin-top:40px;}
.widget {
    margin: 0 0 25px 0;
    display: block;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
}
.widget .widget-heading {
    padding: 7px 15px;
    -webkit-border-radius: 2px 2px 0 0;
    -moz-border-radius: 2px 2px 0 0;
    border-radius: 2px 2px 0 0;
    text-transform: uppercase;
    text-align: center;
    background: #351b86;
    color: white;
}
.widget .widget-body {
    padding: 10px 3px;
    font-size: 36px;
    font-weight: 300;
    background: #f3ff8f;
}

.widget1 .widget1-body {
    padding: 10px 3px;
    font-size: 36px;
    font-weight: 300;
    background: #cccf2f;
}
.widget1 .widget1-heading {
    padding: 7px 15px;
    -webkit-border-radius: 2px 2px 0 0;
    -moz-border-radius: 2px 2px 0 0;
    border-radius: 2px 2px 0 0;
    text-transform: uppercase;
    text-align: center;
    background: green;
    color: white;
}
.feedback {
  background-color : #31B0D5;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  border-color: #46b8da;
}
			</style>
    </head>


   <body>
<header id="topnav">
<?php $this->load->view('common/nav-menu.php');?>
</header>
<div class="wrapper">

            <div class="container-fluid">
 <div class="row">
				
				<div class="col-sm-12">

                        <div class="page-title-box">
                             
						<h4 class="page-title text-center"> <?php 
						$query = $this->db->select('turnaroundtime, id')->from('compliance_tat')->where('id',$this->uri->segment(3))->get();
						foreach($query->result() as $tatinfo){
							echo "<span style='color:red; font-weight:bold'>".$tatinfo->turnaroundtime."</span>";
						}
						?> Checklist</h4>

                        </div>
                        

                    </div><hr>
					
					

                </div>

                <!-- end page title end breadcrumb -->

			<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">
<form action="<?php echo page_url;?>Checklist/updateremarks/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" method="post" enctype="multipart/form-data">
                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>Sr No.</th>
									<th>Assigned To</th>
									<th>Task</th>
									<th>Turnaround Time</th>
									<th>Update Remarks</th>
									<th>Due Date</th>
									
									
                                </tr>

                                </thead>
<tbody>

								 </tbody>

                            </table>
                            	<center><button class="feedback" id="checklist">Update checklist</button></center>
</form>
                        </div>

                    </div>

                </div>

                <!-- end row -->
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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   
jQuery.noConflict();</script>
<?php $tatid  = $this->uri->segment('3');
$userid = $this->uri->segment(4);
?>
<script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"lengthMenu": [[100, 150, 200, -1], [100, 150, 200, "All"]],
fixedHeader:{
    header:true,
    headerOffset:$('#topnav').outerHeight()
},
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Checklist/checklist_task_list_tatwise/<?php echo $tatid;?>/<?php echo $userid;?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'user_name' },
				{mData:'task'},
				{mData:'turnaroundtime'},
				{mData:'updateremarks'},
				{mData:'nextduedate'}
                
				
				
				
		]
});   
});

</script>
<script>
function display_qtybox(id,taskid){
$('#makenewhidden'+id).html("");
var status=$("input[name='pick_items"+taskid+"']:checked").val();
if(status=='0'){
    alert('Remark is Required!');
    $('#qty'+id).addClass('requiredclass');
	$("#checklist").attr('disabled',false);
	$('#qty'+id).css('display','block');
	$('#report'+id).css('display','block');
	$('#qty'+id).attr('required',true);

	$("#makenewhidden"+id).append('<input type="hidden" value="'+taskid+'" name="countall[]">');
	
} else{
	$('#qty'+id).css('display','block');
	 $('#qty'+id).removeClass('requiredclass');
	$('#report'+id).css('display','none');
	$('#qty'+id).attr('required',false);
$("#makenewhidden"+id).append('<input type="hidden" value="'+taskid+'" name="countall[]">');
}
                
}
</script>

</body></html>