<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> Yesterday Checklist Report</title>        
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
         <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
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

table.report thead th {

				background: #2E7DA6;

				color:#fff;

				font-weight:bold;

			}

table.report tbody td {

				font-weight:bold;

				color:#000;

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


<?php $this->load->view('common/info-section.php');?>


<div class="wrapper">


<?php echo $this->session->flashdata('message'); ?>
            <div class="container-fluid" style="background-color:#fff">

            	   <?php 
                $from_date=$this->uri->segment(3); 
                $to_date=$this->uri->segment(4); 
                $user=$this->uri->segment(5);
                ?>

                 <div class="row">
                    <div class="col-sm-12">
					<div class="col-sm-4">
					<div class="page-title-box">
					<h4 class="page-title">ALL DONE TASK </h4>
					</div>
					</div>
					<div class="col-md-4"></div>
					
					<div class="col-md-8">
					<div class="col-md-12">
					<form action="<?php echo page_url;?>Checklist/filtertask" method="post">
							<div class="col-md-12 card-box">
								<div class="col-md-3">
									<label for="field-2" class="control-label">USERS</label>
							<select class="form-control" id="user" name="user" required="">
								<option value="">-------Select User--------</option>
								<?php
								  $qry=$this->db->select('user_id,first_name,last_name,user_status')->from('system_users')->where('user_status',1)->order_by('user_id',asc)->get();
                if($qry->num_rows() > 0){
                	foreach($qry->result() as $user){

                ?>
															<option value="<?php echo $user->user_id; ?>"><?php echo $user->first_name.' '.$user->last_name; ?></option>
															<?php } } ?>
												</select></div>
							<div class="col-md-3">
								<label for="field-2" class="control-label">FROM DATE</label>
							<input type="text" name="from_date" class="form-control datepicker-autoclose" autocomplete="off"  value="<?php echo $from_date;?>"></div>
							<div class="col-md-3">
								<label for="field-2" class="control-label">TO DATE</label>
							<input type="text" name="to_date" class="form-control datepicker-autoclose" autocomplete="off"  value="<?php echo $to_date;?>"></div>
							<div class="col-md-3" style="MARGIN-TOP: 24PX;"><input type="submit" class="btn btn-warning">
							</div>
							</div>
							</form>
					
					</div>
					</div>
                    </div>
					
					
                </div>


		<div class="row">



                    <div class="col-sm-12">



                        <div class="card-box table-responsive">

<form action="<?php echo page_url;?>Checklist/updateremarks" method="post" enctype="multipart/form-data">

                            <table id="example" class="table table-striped table-bordered manglesh">



                                <thead>



                                <tr>



                                    <th style="width:5%">Sr No.</th>

                                    <!-- <th style="width:15%">Department</th> -->
                                    <th style="width:10%">Assigned To</th>

									<th style="font-size:20px; width:50%">Task</th>

									<th >Status</th>
									<th >Evidence</th>
									<th >Date</th>

                                </tr>



                                </thead>

<tbody>



								 </tbody>



                            </table>

						

							</form>



                        </div>



                    </div>



                </div>



                <!-- end row -->



 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">



 <form id="loginForm" method="post" action="<?php echo page_url;?>Checklist/create_checklist" enctype="multipart/form-data">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">ADD NEW CHECKLIST POINT</h4>
</div>
<div class="modal-body">
<div class="row">
<div class="col-md-4">
<div class="form-group">

														 <label for="field-2" class="control-label">BUSINESS LOCATION</label>

														 <span id="error_business_loc" style="color:red;">*</span>

														 <select class="form-control" id="business_loc" name="business_loc" required>

													<option value="">--SELECT BUSINESS LOCATION--</option>

													<?php 

													$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');

													$this->db->order_by('a.company_name','asc');

													$query = $this->db->get();

													$res = $query->result();

													foreach($res as $row){

													?>

													<option value="<?php echo $row->business_loc_id;?>"><?php echo $row->company_name;?> <?php echo strtoupper($row->state_name);?>, <?php echo strtoupper($row->city_name);?></option>

											<?php }?>		

												</select>
<script type="text/javascript">

											

													$("#business_loc").change(function(){

													var business_loc=$("#business_loc").val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Master/User_management/select_department",

													data:"business_loc="+business_loc,

													success:function(data){

													$("#department").html(data);

													}

													});

													});

											

												</script>
												<!--<script type="text/javascript">

											

													$("#business_loc").change(function(){

													var business_loc=$("#business_loc").val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Checklist/user_list",

													data:"business_loc="+business_loc,

													success:function(data){

													$("#user_id").html(data);

													}

													});

													

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Checklist/user_list",

													data:"business_loc="+business_loc,

													success:function(data){

													$("#reporting_head").html(data);

													}

													});

													});

											

												</script>-->

												

													</div>

												</div>
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">DEPARTMENT</label>
														 <span id="error_user_id" style="color:red;">*</span>
														 <select class="form-control" id="department" name="department" required>
													<option value="">--SELECT DEPARTMENT--</option>										

												</select>
 <script type="text/javascript">

											

													$("#department").change(function(){

													var department=$("#department").val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Delegation/user_list_new",

													data:"department="+department,

													success:function(data){

													$("#user_id").html(data);

													}

													});

													});

											

												</script>
													</div>

												</div>
												<div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">USER NAME</label>
														 <span id="error_user_id" style="color:red;">*</span>
														 <select class="form-control select3" id="user_id" name="user_id" required>
													<option value="">--SELECT USER--</option>										

												</select>

													</div>

												</div>

                                                <div class="col-md-12">



                                                    <div class="form-group">



                                                        <label for="field-1" class="control-label">CHECKLIST POINT</label>



														<span id="error_task" style="color:red;">*</span>



                                                        <input type="text" class="form-control" id="task" name="task" placeholder="" style="text-transform:uppercase" required>



                                                    </div>



                                                </div>

												

												<div class="col-md-9">



                                                    <div class="form-group">



                                                        <label for="field-1" class="control-label">VIDEO LINK</label>



														<input type="text" class="form-control" id="video_link" name="video_link" placeholder="" style="text-transform:uppercase">



                                                    </div>



                                                </div>

<div class="col-md-3">
	<div class="form-group">
		<label>Evidence Required?</label>
		<select class="form-control" name="evidence" id="evidence">
			<option value="1">Yes</option>
			<option value="0">No</option>
		</select>
	</div>
</div>

												<div class="col-md-6">



                                                    <div class="form-group">



                                                        <label for="field-1" class="control-label">Turnaround Time</label>



														<span id="error_turnaroundtime" style="color:red;">*</span>


                                        <select class="form-control" id="turnaroundtime" name="turnaroundtime" onChange="shownext();" style="text-transform:uppercase" required>



														<option value="">--Select Turnaround Time--</option>

														<?php 

														$query = $this->db->select('id,turnaroundtime, status')->from('compliance_tat')->where('status','1')->get();


                                                    if($query->num_rows() > 0) {
														foreach($query->result() as $turnaroundtime){

														?>

														<option value="<?php echo $turnaroundtime->id;?>"><?php echo strtoupper($turnaroundtime->turnaroundtime);?></option>

														<?php } }?>



														</select>



                                                    </div>



                                                </div>

                                                

                                                
												<div class="col-md-3" id="firstdaterow" style="display:none">

												<div class="form-group">

												<label>FIRST</label>

												<select class="form-control" id="first_date" name="first_date" style="text-transform:uppercase">



													<option value="">--Select First Date--</option>

													<?php 

													for($i=1; $i<=31; $i++){

													?>

													<option value="<?php echo $i;?>"><?php echo $i;?></option>

													<?php }?>

												</select>

												</div>

												</div>

												<div class="col-md-3" id="seconddaterow" style="display:none">

												<div class="form-group">

												<label>SECOND</label>

												<select class="form-control" id="second_date" name="second_date" style="text-transform:uppercase">

												<option value="">--Select Second Date--</option>

													<?php 

													for($i=1; $i<=31; $i++){

													?>

													<option value="<?php echo $i;?>"><?php echo $i;?></option>

													<?php }?>

												</select>

												</div>

												</div>

                                                

                                                 <div class="col-md-3">
													<div class="form-group">
													<label for="field-1" class="control-label">Due Date</label>
													<span id="error_duedate" style="color:red;">*</span>
													<input type="text" class="form-control" id="duedate" name="duedate" value="<?php echo date('d-m-Y');?>" placeholder="Due Date" style="text-transform:uppercase" required>
													</div>
													</div>

                                                  <div class="col-md-3">
													<div class="form-group">
													<label for="field-1" class="control-label">Due Time</label>
													<span id="error_duedate" style="color:red;">*</span>
													<input type="time" class="form-control" id="duetime" name="duetime" value="" placeholder="Due Time" style="text-transform:uppercase" required>
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

dom: 'Bfrtip',

        buttons: [

            'excel'

        ],

"lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],

fixedHeader:{

    header:true,

    headerOffset:$('#topnav').outerHeight()

},

"searchHighlight": true,

"pagination":true,

"sAjaxSource": "<?php echo page_url;?>Checklist/checklist_yesterday_report_list",

"aoColumns": [

				{ mData: 'sr_no' } ,

				{mData:'assigned_to'},

				{mData:'task'},

				{mData:'status'},
				{mData:'evidence'},		
				{mData:'date'}		

		]

});   

});



</script>

 <script type="text/javascript">

    //         $(function () {

    //             $('.datepicker-autoclose').datepicker({

    //                 autoclose:"true",

    //                 orientation: "bottom",

    //                 changeMonth: true,

    // changeYear: true,

    // format: 'dd-mm-yyyy'

    //             });

    //         });
    
    $(document).ready(function() {
            $("#duedate").datepicker({
					todayHighlight:true,
		        	startDate: '-0m',
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

<script>
function setwhatsappreminder(i){
	
	var permission = $("#setwhatsapp"+i).val();
	var checklist_id = i;
	$.ajax({
	type:"post",
	url:"<?php echo page_url;?>Checklist/setwhatsappreminder",
	 data: {permission : permission,checklist_id:checklist_id},
	success:function(data){
		alert(data);
	$("#success"+i).html(data);
	}
	});
}
</script>

<script src="<?php echo plugins_url;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
            $(function () {
                $('.datepicker-autoclose').datepicker({
                    autoclose:"true",
                    orientation: "top",
                    changeMonth: true,
    changeYear: true,
    format: 'dd-mm-yyyy',
    endDate: '+365d'
                });
            });

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});
</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body></html>