<?php 
$CI =& get_instance();
$CI->load->model('Salescrm_model');
$getTotalUserConveyance = $CI->Salescrm_model->getTotalUserConveyance($this->uri->segment(3), $this->uri->segment(4), $this->uri->segment(5));
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> </title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.  min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                       <div class="row">
                
                <div class="col-sm-12">
<div class="page-title-box">

<div class="col-md-12">
    <h4 class="text-center">CONVEYANCE DASHBOARD</h4>
    <div class="pull-right"></div>
    
   <div class="col-md-3"></div>
        <div class="col-md-6" style="background-color:whitesmoke;">
             <form id="loginForm" method="post" action="<?php echo page_url;?>Sales/filter_accounts_report">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="field-2" class="control-label">User List </label>
                        <span id="error_city" style="color:red;"></span>
                        <select class="form-control" name="user_id" id="user_id">
                            <option value="">Select User</option>
                            <?php 

                            $query = $this->db->select('user_id, first_name, last_name')->from('system_users_view')->where('user_status','1')->get();
                            foreach($query->result() as $row){
                            ?>
                            <option value="<?php echo $row->user_id;?>" <?php if($this->uri->segment(3) == $row->user_id) { echo 'selected';}?>><?php echo $row->first_name." ".$row->last_name;?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <?php 
                    if($this->uri->segment(4) <> '' && $this->uri->segment(5) <> '') {
                        $startdate = date('d-m-Y', strtotime($this->uri->segment(4)));
                        $enddate = date('d-m-Y', strtotime($this->uri->segment(5)));
                    } else {
                        $startdate = '';
                        $enddate = '';
                    }
                ?>
                <div class="col-md-4">
                    <div class="form-group">
                         <label for="field-2" class="control-label">Start Date </label>
                         <span id="error_company" style="color:red;"></span>
                         <input type="text" class="form-control" name="startdate" id="startdate" value="<?php echo $startdate;?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                         <label for="field-2" class="control-label">End Date </label>
                         <span id="address_error" style="color:red;"></span>
                         <input type="text" class="form-control" name="enddate" id="enddate" value="<?php echo $enddate;?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-9"></div>
                <div class="col-md-2 pull-right">
                    <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                </div>
            </div>
            </form>
            
        </div>
        <div class="col-md-3" style="background-color:whitesmoke;">
            <h2 style="color: red;">Total Amount: <?php echo $getTotalUserConveyance;?></h2>
        </div> 
    
</div>
                        
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                        </div>

                    </div>
                </div>
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>PERSON NAME</th>
									<th>DATE</th>
                                    <th>FROM</th>
                                    <th>PROCEED TO</th>
                                    <th>MODE</th>
                                    <th>VISIT DETAIL</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
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
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
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
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/conveyance_data_for_accounts_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",
 "aoColumns": [
						{ mData: 'sr_no' } ,
                        { mData: 'user_name' },
                        { mData: 'travel_date' },
						{ mData: 'from_location' },
						{ mData: 'proceed_to' },
                        { mData: 'mode' },
                        { mData: 'visitdata' }
						
						
                ]
        });   
});

</script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
	
var date = $("#date").val();
if(date=='')
{
	$("#error_date").html('Required!');
}
var from = $("#from").val();
if(from=='')
{
	$("#error_from").html('Required!');
}
var proceed_to = $("#proceed_to").val();
if(proceed_to=='')
{
	
	$("#error_proceed_to").html('Required!');
}

var mode = $("#mode").val();
if(mode=='')
{
	
	$("#error_mode").html('Required!');
}


if(date=='' || from=='' || proceed_to=='' || mode=='')
{
	
	return false;
}

});
});
</script>
 <script> $(document).ready(function() {

                $("#startdate").datepicker({
                    autoclose:"true",
                    changeMonth: true,
                    changeYear: true,
                    format: 'dd-mm-yyyy'
                });
                $("#enddate").datepicker({
                    autoclose:"true",
                    changeMonth: true,
                    changeYear: true,
                    format: 'dd-mm-yyyy'
                });
                $("#datepicker1").datepicker({
					orientation: 'bottom'
				});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>