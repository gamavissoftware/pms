<?php
$CI =& get_instance();
$CI->load->model('Master_model');
// $mode = $CI->Master_model->getsettings();
// if(count($mode)>0)
// {
// $mode=$mode['mode'];
// }else
// {
//     $mode='';
// }
 
$DI =& get_instance();
$DI->load->model('Salescrm_model', 'salescrm');
$checkRolePermission = $DI->salescrm->checkRolePermission($_SESSION['logged_in']['user_id'], 70);

if ($checkRolePermission != '') {
    foreach ($checkRolePermission as $row);
    $madd = $row->madd;
    $mremove = $row->mremove;
} else {
    $madd = '';
    $mremove = '';
}

?>
<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle;?> Spares Lead Stage</title>



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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button" style="background-color: ;"><i class="fa fa-arrow-left"></i>Back</button></a>
						 <div class="btn-group pull-right">
						   <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal" style="background-color: ;">Add Lead Stage</button>
                        </div>
                        <h4 class="page-title text-center">Lead Stage List</h4>
                        </div>
                    </div>
                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty" style="background-color: ;">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Lead Name</th>
                                    <th>Sort Order</th>
                                    <th>Relation</th>
                                    <th>Action</th>                                  
                                </tr>
                                </thead>                               
                            </table>
                        </div>
                    </div>
                </div>

                <!-- end row -->

 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Master/Lead_Type/add_lead_type_spares" enctype="multipart/form-data">
       <input type="hidden" name="mode" value="">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Add Lead Stage</h4>
            </div>
        <div class="modal-body">
            <div class="row">
				<div class="col-md-4">
					<div class="form-group">
    					 <label for="field-2" class="control-label">Lead Stage Name<span style="color:red;">*</span></label>
    					 <input type="text" class="form-control" name="lead_type" id="lead_type" required="">
					</div>
			    </div>
                <div class="col-md-4">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Sort Order<span style="color:red;">*</span></label><br>
                         <input type="number" class="form-control" name="sort_order" id="sort_order" onblur="checkifsortnoexists()" required="">
                    </div>
                </div>

    <?php
          // if($mode==2)
          //                           {
                                    ?>
                <div class="col-md-4">
                    <div class="form-group">
                         <label for="field-2" class="control-label">User Role<span style="color:red;">*</span></label><br>
                         <select class="form-control" name="user_role" id="user_role" required="">
                             <option value="">Please Select</option>
                             <?php
                             $res=$this->db->select('user_role_id,user_role')->from('user_role')->where('status',1)->get();
                             if($res->num_rows() >0)
                             {
                                foreach($res->result() as $row){                             
                             ?>
                             <option value="<?php echo $row->user_role_id ?>"><?php echo $row->user_role;?></option>
                         <?php }}?>
                         </select> 
                    </div>
                </div>
            <?php //} ?>



                <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Show in comparison report?</label><br>
                         <input type="checkbox" name="comparison_report" id="comparison_report" value="1">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Quotation step?</label><br>
                         <input type="checkbox" name="quotation_step" id="quotation_step" value="1">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Quotation Revised step?</label><br>
                         <input type="checkbox" name="quotation_step" id="quotation_revised_step" value="1">
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Conversion Step?</label><br>
                         <input type="checkbox" name="conversion_step" id="conversion_step" value="1">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Reason?</label><br>
                         <input type="checkbox" name="reason" id="reason" value="1">
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Followup Date?</label><br>
                         <input type="checkbox" name="followup_date" id="followup_date" value="1">
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Dead End?</label><br>
                         <input type="checkbox" name="dead_end" id="dead_end" value="1">
                    </div>
                </div>

                 <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Visit Step?</label><br>
                         <input type="checkbox" name="visit_step" id="visit_step" value="1">
                    </div>
                </div>

                 <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Demo Scheduled?</label><br>
                         <input type="checkbox" name="demo_scheduled" id="demo_scheduled" value="1">
                    </div>
                </div>

                 <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Sample Req. Step?</label><br>
                         <input type="checkbox" name="sample" id="sample" value="1">
                    </div>
                </div>

                 <div class="col-md-3">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Trail Req. Step</label><br>
                         <input type="checkbox" name="trail" id="trail" value="1">
                    </div>
                </div>
                <div class="col-md-3" style="display: none;">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Upload Icon</label><br>
                         <input type="file" name="icon" id="icon">
                    </div>
                </div>
                <div class="col-md-3" style="display:none;">
                    <div class="form-group">
                         <label for="field-2" class="control-label">MIS Applicable</label><br>
                         <input type="checkbox" name="mis" id="mis" value="1" onchange="checkMis()">
                    </div>
                </div>
                <div class="col-md-3" id="tat_type" style="display:none;">
                    <div class="form-group">
                         <label for="field-2" class="control-label">TAT Type</label><br>
                         <select class="form-control" name="tat_type" id="tat" onchange="checkTatType()">
                             <option value="">SELECT</option>
                             <option value="1">Fixed TAT</option>
                             <option value="2">Followup</option>
                         </select>
                    </div>
                </div>
                <div class="col-md-3" id="day_time" style="display:none;">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Day</label>
                         <input type="radio" name="day_time" class="day_time" value="1" onchange="checkDayTime()">
                         <label for="field-2" class="control-label">Time</label>
                         <input type="radio" name="day_time" class="day_time" value="2" onchange="checkDayTime()">
                    </div>
                </div>
                <div class="col-md-3" id="days_text" style="display:none;">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Day</label>
                         <input type="text" class="form-control" name="day_text">
                    </div>
                </div>
                <div class="col-md-3" id="times_text" style="display:none;">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Time (In Hours)</label>
                         <input type="text" class="form-control" name="time_text">
                    </div>
                </div>
				<div class="col-md-3" style="display:none;">
				    <div class="form-group">
				        <label for="field-2" class="control-label">Status<span style="color:red;">*</span></label><br>
				        <select class="form-control" id="status" name="status">
					       <option value="">--Select Status--</option>
					       <option value="1">Active</option>
					       <option value="0">Inactive</option>
				        </select>
				    </div>
			    </div>
            </div>
        </div>
        <div class="modal-footer">
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

"sAjaxSource": "<?php echo page_url;?>Master/Lead_Type/spares_Lead_list",

"aoColumns": [

				{ mData: 'sr_no' } ,
				{ mData: 'lead_stage' },
                { mData: 'sort_order' },
                { mData: 'relation' },
                { mData: 'edit' }

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

<script type="text/javascript">
    function checkifsortnoexists() {
        var sort_order = $("#sort_order").val();
            $.ajax({
                type:"post",
                url:"<?php echo page_url;?>Master/Lead_Type/checksparessortno",
                data:{sort_order: sort_order},
                success:function(data){
                    if (data == 1) {
                        alert('Sort Order No. Already Exits!');
                        $("#sort_order").val('');
                    }
                }
            });
    }

    function checkMis() {
        $("#tat_type").css('display', 'none');

        if($("#mis").is(':checked')) {
            $("#tat_type").css('display', '');
        }

    }

    function checkTatType() {
        $("#day_time").css('display', 'none');
        if($("#tat").val() == 1) {
            $("#day_time").css('display', '');
        } else if($("#tat").val() == 2) {
            $("#day_time").css('display', 'none');
        }
    }

    function checkDayTime() {
        $("#days_text").css('display', 'none');
        $("#times_text").css('display', 'none');
        if($(".day_time").val() == 1) {
            $("#days_text").css('display', '');
            $("#times_text").css('display', 'none');
        } else {
            $("#days_text").css('display', 'none');
            $("#times_text").css('display', '');
        }
    }
</script>

    </body>

</html>