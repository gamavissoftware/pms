<?php
$CI =& get_instance();
$CI->load->model('Fms_mismodel','fms_mismodel');
$getcurrentweek=$CI->fms_mismodel->currentweekdates();
$sdate=$getcurrentweek[0];
$edate=$getcurrentweek[1];

?>
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
 <style>
            .requiredclass{
                border: 2px solid #E12830;
            }
        </style>
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
				table.manglesh tbody td {
					
			}
			table.report tbody td {
				font-weight:bold;
				color:#000;
			}
table.report thead th {
				background: #2E7DA6;
				color:#fff;
				font-weight:bold;
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

            <div class="container-fluid" style="background-color:#fff;">
 <!-- Page-Title -->
<h3 class="text-center"><?php 
$q = $this->db->select('department')->from('departments')->where('department_id',$this->uri->segment(3))->get();
foreach($q->result() as $row);
echo $row->department;
?> Checklist </h3>
                <div class="row">
				
				<div class="col-sm-12">
                    </div><hr>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped table-bordered report">
								<thead style="text-align:center">
								<tr>
								<?php 
							
							
								
								
								$user_id=$_SESSION['logged_in']['user_id'];
								$query = $this->db->select('id, turnaroundtime, status,sortbynumber')->from('compliance_tat')->where('status','1')->order_by('sortbynumber','asc')->get();
								$count = count($query->result());
								foreach($query->result() as $timing);
								?>
								
									<th class="text-center" colspan="<?php echo $count;?>">TAT</th>
								
									</tr>
								</thead>
								<tbody>
								<tr>
								<?php 
								$k=1;
								$user_id=$_SESSION['logged_in']['user_id'];
								$query = $this->db->select('id, turnaroundtime, status,sortbynumber')->from('compliance_tat')->where('status','1')->order_by('sortbynumber','asc')->get();
								foreach($query->result() as $timing){
								?>
								
									<th class="text-center"><a style="text-decoration:none; color:#000"  href="javascript:void(0);"><?php echo $timing->turnaroundtime;?></a> (<?php 
										$query = $this->db->select('task_id, tat_id, status,company_id')->from('compliance_task_report')->where('tat_id',$timing->id)->where('user_id',$user_id)->where('status','1')->get();
										$res = $query->result();
										echo count($res);

										?>)</th>
									
							
								<?php $k++;}?>
									</tr>
								
								
								</tbody>
							</table>
						</div>
						
					</div>
														
															

                </div>

                <!-- end page title end breadcrumb -->

		

		<div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">
<form action="<?php echo page_url;?>Checklist/updateremarks/" method="post" enctype="multipart/form-data">
                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th style="width:5%">Sr No.</th>
									<th style="font-size:20px; width:50%">Task</th>
									<th style="width:10%">Turnaround Time</th>
									<th style="width:10%">Next Due Date</th>
									<th style="width:25%">Update Remarks</th>
									

                                </tr>

                                </thead>
<tbody>

								 </tbody>

                            </table>
						<!--	<center><button class="feedback" id="checklist">Update checklist</button></center>-->
							</form>

                        </div>

                    </div>

                </div>

                <!-- end row -->



                <!-- Footer -->

                <?php $this->load->view('common/footer');?>

                <!-- End Footer -->
            </div> <!-- end container -->

        </div>

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


<script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
$('#example').dataTable({
"bProcessing": true,
"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
fixedHeader:{
    header:true,
    headerOffset:$('#topnav').outerHeight()
},
"searchHighlight": true,
"pagination":true,
	fixedHeader: true,
"sAjaxSource": "<?php echo page_url;?>Checklist/view_department_wise_checklist_data/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{mData:'task'},
				{mData:'turnaroundtime'},
				{mData:'nextduedate'},
				{mData:'updateremarks'}
				
				
		]
});   
});

</script>
 <script type="text/javascript">
            $(function () {
                $('.datepicker-autoclose').datepicker({
                    autoclose:"true",
                    orientation: "bottom",
                    changeMonth: true,
    changeYear: true,
    format: 'dd-mm-yyyy'
                });
            });
        </script>


<script language="javascript" type="text/javascript">   


$(document).ready(function() {

$("#save").click(function() {

var business_loc = $("#business_loc").val();
if(business_loc=='')
{
$("#error_business_loc").html('Required!');
}
var user_id = $("#user_id").val();

if(user_id=='')

{
$("#error_user_id").html('Required!');

}
var task = $("#task").val();
if(task=='')
{
$("#error_task").html('Required!');
}


var turnaroundtime = $("#turnaroundtime").val();
if(turnaroundtime=='')
{
$("#error_turnaroundtime").html('Required!');
}
var duedate = $("#duedate").val();
if(duedate=='')
{
$("#error_duedate").html('Required!');
}


if(business_loc=='' || task=='' || turnaroundtime=='' ||  user_id=='' || duedate=='')

{

	

	return false;

}



});

});

</script>

<script>
function display_qtybox(id,taskid){
$('#makenewhidden'+id).html("");
var status=$("input[name='pick_items"+taskid+"']:checked").val();
if(status=='0'){
    //alert('Remark is required!');
    //$('#qty'+id).addClass('requiredclass');
	$("#checklist").attr('disabled',false);
	$('#qty'+id).css('display','block');
	$('#report'+id).css('display','block');
	$('#qty'+id).attr('required',true);

	$("#makenewhidden"+id).append('<input type="hidden" value="'+taskid+'" name="countall[]">');
	
} else{
	$('#qty'+id).css('display','block');
	//$('#qty'+id).removeClass('requiredclass');
	$('#report'+id).css('display','none');
	$('#qty'+id).attr('required',false);
	$("#makenewhidden"+id).append('<input type="hidden" value="'+taskid+'" name="countall[]">');
}
                
}
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body></html>
