<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Users List</title>

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
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
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
				text-align:center;
			}</style>
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
                    <div class="col-sm-12" style="margin-top:20px">
                      
                       
                        <h4 class="page-title text-center">Not Active Users List</h4>
					<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>	
                    </div>
                </div>

 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<table id="example" class="table table-striped table-bordered dt-responsive nowrap manglesh" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Profile Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
										<th>Password</th>
                                        <th>Contact Number</th>
                                        <th>Business Location</th>
                                        <th>Department</th>
                                        <th>Role</th>
                                        <th>Edit</th>
									    
                                    </tr>
                                </thead>
                                <tbody>

									<?php
									$i = 1;
									if(!empty($inactive_users))
									{
										foreach($inactive_users as $row)
										{
											$edit = "<a href='".page_url."Master/User_management/edit_user_profile/".$row->user_id."'><i class='fa fa-pencil' title='Edit Profile'></i></a>";
											if(!empty($row->profile_image))
											{
												$img = "<img src='".user_profile.$row->profile_image."' width='50px' height='50px' class='img-responsive'>";
											}
											else
											{
												$img = "<img src='".user_profile."userplaceholder.jpeg' width='50px' height='50px' class='img-responsive'>";
											}
									?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo $img; ?></td>
										<td><?php echo strtoupper(trim($row->first_name.' '.$row->last_name)); ?></td>
										<td><?php echo strtoupper((string)$row->email); ?></td>
										<td><?php echo (string)$row->password; ?></td>
										<td><?php echo $row->contact_number."<br>".$row->alternate_number; ?></td>
										<td><?php echo strtoupper((string)$row->company_name); ?></td>
										<td><?php echo strtoupper((string)$row->department); ?></td>
										<td><?php echo strtoupper((string)$row->user_role); ?></td>
										<td><?php echo $edit; ?></td>
									</tr>
									<?php
											$i++;
										}
									}
									?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->


                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div>
            <!-- end container -->

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
$('#example').dataTable({
"bProcessing": true,
	fixedHeader: true,
"pagination":true
});   
});

</script>
 <script> $(document).ready(function() {
 var date = new Date();
date.setDate(date.getDate());
   
$('.datepicker').datepicker({
    todayHighlight:true
    
});
$("#date_of_birth").datepicker({ dateFormat: "dd-mm-yy" }).val();

                $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true
				});
			//	$('.datepicker').datepicker({todayHighlight:true});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });</script>
		</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var first_name = $("#first_name").val();
if(first_name=='')
{
	$("#error_first_name").html('Required!');
}
var last_name = $("#last_name").val();
if(last_name=='')
{
	
	$("#error_last_name").html('Required!');
}
var email = $("#email").val();
if(email=='')
{
	
	$("#error_email").html('Required!');
}
var contact_number = $("#contact_number").val();
if(contact_number=='')
{
	
	$("#error_contact_number").html('Required!');
}

var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	
	$("#error_business_loc").html('Required!');
}
var department = $("#department").val();
if(department=='')
{
	
	$("#error_department").html('Required!');
}
var user_role = $("#user_role").val();
if(user_role=='')
{
	
	$("#error_user_role").html('Required!');
}
var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}

var blood_group = $("#blood_group").val();
if(blood_group=='')
{
	
	$("#error_blood_group").html('Required!');
}


if(first_name=='' || last_name==''|| email=='' || contact_number=='' || business_loc=='' || department==''|| user_role=='' || status=='' || blood_group=='')
{
	
	return false;
}

});
});
</script>
    </body>
</html>
