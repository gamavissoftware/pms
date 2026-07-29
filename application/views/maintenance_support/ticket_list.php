
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?> Help Ticket List</title>
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
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        	

		<style>

		table.manglesh thead th {

				
			background-color: #332b24 !important;
				color:#fff;

				font-weight:bold;

				text-align:center;

			}
				table.manglesh tbody td {
				    text-align:center;
				}

		</style>

    </head>





    <body>
<!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <?php $this->load->view('common/info-section.php');?>
        <div class="wrapper">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  <!-- <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">ADD NEW DEPARTMENT</button> -->
						</div>
			<h4 class="page-title">HELP TICKET LIST</h4>

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
									<th>DF No.</th>
									<th>Department</th>
									<th>User</th>
                                   	<th>Ticket No</th>
									<th>Description</th>
									<th>Attachment</th>
									<th>Ticket Status</th>
                                </tr>

                                </thead>
                                <tbody>
                                	<?php 
                                	$i=1;
                                	$q = $this->db->select('a.*, b.df_no')->from('maintenance_support a')->join('df_release b','a.df_id=b.id','left')->where('a.status',0)->order_by('a.id','desc')->get();
                                	foreach ($q->result() as $row) {
                                		if($row->department_id==0){
                                			$dept = 'ALL';
                                		}else{
                                			$q = $this->db->select('department')->from('departments')->where('department_id',$row->department_id)->get();
                                		foreach($q->result() as $row1){
                                			$dept = $row1->department;
                                		}
                                		}
                                		$selecteduser= '';
                                	if($row->user_id<>''){
                                		$q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where_in('user_id',$row->user_id,false)->get();
                                		foreach($q->result() as $row3){
                                			$selecteduser.= $row3->first_name." ".$row3->last_name." <br>";
                                		}
                                	}else{
                                		$selecteduser = '';
                                	}

                                	
                                		
                                		
                                	?>
                                	<tr>
                                		<td><?php echo $i;?></td>
                                		<td><?php echo $row->df_no;?></td>
                                		<td><?php echo $dept;?></td>
                                		<td><?php echo $selecteduser;?></td>
                                		<td><?php echo $row->ticket_id;?></td>
                                		<td><?php echo $row->ticket;?></td>
                                		<td><?php if($row->screenshot<>''){?>
                                			<span>Click To Download</span>
                                			<?php }?></td>
                                		<td><a href="<?php echo page_url;?>Maintenance_support/view_history/<?php echo $row->id;?>"><span class="btn btn-primary btn-xs">Click to View Status</span></a></td>
                                	</tr>
                                <?php $i++;}?>
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



        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>



<script>

$(document).ready(function(){

	   $("#depsave").attr('disabled',false);

	   $("#depsave").val('submit');

  $("#loginForm").on("submit", function(){

   // $("#pageloader").fadeIn();

   $("#depsave").attr('disabled',true);

     $("#depsave").val('Please Wait...');

  });//submit

});//document ready

</script>  



        <script>

$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,

	fixedHeader: true,

"pagination":true


});   

});



</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#depsave").click(function() {

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

    </body>

</html>

