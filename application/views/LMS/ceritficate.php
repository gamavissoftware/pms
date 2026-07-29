<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> LMS List</title>

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
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
		<style>
		table.manglesh thead th {
				background:<?php echo $LOGO->colorcode;?>;
				color:#fff;
				font-weight:bold;
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


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <?php 
							$res=$this->db->select('department_id,department')->from('departments')->where('department_id',$_SESSION['logged_in']['department_id'])->where('status',1)->get();
							if($res->num_rows() >0)
							{
								foreach($res->result() as $department);
							}
							$que=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$_SESSION['logged_in']['user_id'])->get();
							if($que->num_rows() >0)
							{
								foreach($que->result() as $username)
								{
									$name=$username->first_name.' '.$username->last_name;
								}
							}
							$da=$this->db->select('added_on')->from('lms_result')->where('department_id',$_SESSION['logged_in']['department_id'])->where('userid',$_SESSION['logged_in']['user_id'])->order_by('added_on','desc')->get();
							
							if($da->num_rows()>0){
								foreach($da->result() as $cdate);
								
								$dateaword=date('d-M-Y',strtotime($cdate->added_on));
								
							}
							
						 ?>
                            
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				
                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
							<div class="col-md-3"></div>
							<div class="col-md-6" style="width:800px; height:600px; padding:20px; text-align:center; border: 10px solid #787878">
							<div style="width:750px; height:550px; padding:20px; text-align:center; border: 5px solid #787878">
							<span style="font-size:50px; font-weight:bold">Certificate of Completion</span>
							<br><br>
							<span style="font-size:25px"><i>This is to certify that</i></span>
							<br><br>
							<span style="font-size:30px"><b><?php echo $name; ?></b></span><br/><br/>
							<span style="font-size:25px"><i>has completed the training for</i></span> <br/><br/>
							<span style="font-size:30px"><?php echo $department->department; ?> DEPARTMENT</span> <br/><br/>
							<br/><br/><br/>
							<span style="font-size:25px"><i>Dated</i></span><br>
							
							<span style="font-size:30px"><?php echo $dateaword; ?></span>
							</div>
							</div>
							<div class="col-md-3"></div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 
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
	fixedHeader: true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>LMS/list_data/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'department' },
				{ mData: 's_day' },
				{ mData: 'v_name' },
				{ mData: 'v_link' },
				{ mData: 'p_name' },
				{ mData: 'p_link' },
				{ mData: 'test_qus' },
				{ mData: 'status' },
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
    </body>
</html>