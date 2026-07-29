<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title>Prestogroup</title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

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
        <?php 
         $user_id = $this->uri->segment(3);
        $startdate=date('Y-m-d',strtotime($this->uri->segment(4)));
        $enddate=date('Y-m-d',strtotime($this->uri->segment(5)));
        $query = $this->db->select('a.*, b.first_name, b.last_name')->from('conveyance_voucher_view a')->join('system_users_view b','a.added_by=b.user_id','left')->where_in('a.added_by',$user_id)->where('a.status','0')->where('travel_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->order_by('a.travel_date','DESC')->get();
        $res = $query->result();
        foreach($res as $row1);
        
        $user_name =$this->uri->segment(3);
        $query1 = $this->db->select('b.user_id, b.first_name, b.last_name')->from('system_users_view b')->where('b.user_id',$user_name)->get();
        $res2 = $query1->result();
        foreach($res2 as $row4);
        ?>

        <div class="wrapper">

            <div class="container-fluid">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

                            <h4 class="page-title text-center">Filter Local Conveyance Data</h4>
                            <h4 class="page-title text-center">SERVICE ENGINEER: <?php echo $row4->first_name; ?> <?php echo $row4->last_name; ?></h4>

                        </div>

                    </div>
                    <div class="col-md-6">
                    </div>
                    <form method="post" action="<?php echo page_url; ?>Sales/local_conveyance_filter_data/">
                     <div class="col-md-6 pull-right">

                        
                            <div class="col-md-3"><select class="form-control" name="user" id="user" required="" >
                                <option value="">--Please Select--</option>
                                <?php 
                                $user_idteam =$this->session->userdata['logged_in']['user_id'];

                                $q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_idteam)->get();
                                if($q->num_rows()>0)
                                {
                                foreach($q->result() as $teamdetail);
                                $teammembers = array();
                                $query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();
                                $res = $query->result();
                                foreach($res as $teaminfo){
                                $teammembers[] = $teaminfo->employee_id;

                                }
                                $team = "'" . implode ( "', '", $teammembers ) . "'";
                                }else
                                {
                                $team='NA';
                                }
                                if($team<>'NA')
                                {
                                $query = $this->db->select('b.user_id, b.first_name, b.last_name')->from('system_users_view b')->where_in('b.user_id',$team,false)->get();
                                $res = $query->result();
                                }
                                foreach($res as $row)
                                     {
                                 ?>
                                <option value="<?php echo $row->user_id; ?>"><?php echo $row->first_name ?> <?php echo $row->last_name ?></option>
                                <?php
                            }
                            ?>
                            </select> </div>
                            <div class="col-md-3"><input class="form-control" type="date" name="startdate"  required=""></div>
                            <div class="col-md-3"><input class="form-control" type="date" name="enddate" required=""></div>
                            <div class="col-md-3"><input class="form-control btn btn-success" type="submit" name="submit" value="submit" ></div>
                        </div>
                        </form>
						

						

                </div>

				

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                          <h4 class="page-title">Public Conveyance Data</h4>
                        </div>
                        <div class="col-md-4 pull-right">
                        <?php echo $viewbillpublictour = "<a href='".page_url."Sales/viewbillpublictour/".$user_id."/".$startdate."/".$enddate."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i>Print consolidated Bill</span></a>"; ?>
                        <?php echo $viewvisitreport = "<a href='".page_url."Sales/viewvisitreport/".$user_id."/".$startdate."/".$enddate."/1'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i>View visit report</span></a>"; ?>
                        <?php echo $takeprint = "<a href='".page_url."Sales/take_localconveyance_public_print/".$user_id."/".$startdate."/".$enddate."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i>Public Take Print</span></a>"; ?></div>
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

                                    <th>SR NO.</th>
                                    <th>Travel Date</th>
									<th>PERSON NAME</th>
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

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                          <h4 class="page-title">Own Conveyance Data</h4>
                        </div>
                        <div class="col-md-4 pull-right">
                        <?php echo $viewvisitreport = "<a href='".page_url."Sales/viewvisitreport/".$user_id."/".$startdate."/".$enddate."/2'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i>View visit report</span></a>"; ?>
                        <?php echo $parkingbills = "<a href='".page_url."Sales/viewbillparkingbill/".$user_id."/".$startdate."/".$enddate."/2'><span class='btn btn-success btn-xs'>Download praking Bill</span></a>"; ?>
                        <?php echo $takeprint = "<a href='".page_url."Sales/take_localconveyance_own_print/".$user_id."/".$startdate."/".$enddate."'><span class='btn btn-xs btn-success'><i class='fa fa-print'></i>Own Take Print</span></a>"; ?></div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example1" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

                                    <th>SR NO.</th>
                                    <th>Travel Date</th>
                                    <th>PERSON NAME</th>
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

 "sAjaxSource": "<?php echo page_url;?>Sales/conveyance_voucher_for_hod_filter_public_list/<?php echo  $this->uri->segment(3)?>/<?php echo $this->uri->segment(4)?>/<?php echo $this->uri->segment(5)?>",

 "aoColumns": [

						{ mData: 'sr_no' } ,
                        { mData: 'travel_date' },
                        { mData: 'user_name' },
                        { mData: 'visitdata' }                     

                ]

        });   

});



</script>

 <script>

$( document ).ready(function() {

$('.select2').select2({ });

$('.select3').select2({ });

$('.select4').select2({ });

$('#example1').dataTable({

 "bProcessing": false,

 "pagination":true,

 "sAjaxSource": "<?php echo page_url;?>Sales/conveyance_voucher_for_hod_filter_own_list/<?php echo  $this->uri->segment(3)?>/<?php echo $this->uri->segment(4)?>/<?php echo $this->uri->segment(5)?>",

 "aoColumns": [

                        { mData: 'sr_no' } ,
                        { mData: 'travel_date' },
                        { mData: 'user_name' },
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