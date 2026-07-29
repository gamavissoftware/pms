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
				background: <?php echo $LOGO->colorcode;?>;
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
							
						 ?>
                            
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				<?php
				$day=$this->uri->segment(4);
					$res=$this->db->select('s_day')->from('lms_study')->where('s_day',$day)->where('department_id',$department->department_id)->get();
					if($res->num_rows() > 0)
					{
						foreach($res->result() as $stu);
						
					}else
					{
							$stu="";
							//echo "NO DATA FOUND";
					}
					//echo $stu->s_day;
				?>
                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <div class="col-md-12">
								<div class="col-md-12"  style="text-align:center"><h2>Traning Result of <?php echo $department->department;?>| Day <?php echo $day;?> </h2><br></div>
								<div style="font-weight:600;font-size:30px;text-align:center">Your Score Card</div>
								<?php
								
								$res=$this->db->select('*')->from('lms_user_result')->where('s_day',$day)->where('department_id',$department->department_id)->where('user_id',$_SESSION['logged_in']['user_id'])->order_by('id','desc')->limit(1)->get();
								if($res->num_rows() > 0)
								{
								foreach($res->result() as $study);
								}
								
								$que=$this->db->select('q_id')->from('lms_questions')->where('day_qus',$day)->where('department_id',$department->department_id)->get();
								$totalques=$que->num_rows();
								?>
								
								<?php 
								$rightan=$study->total_score;
								$wrongan=$totalques-$rightan;
								$percentage=$study->percentage;
								$percentage=$study->percentage;
								$result=$study->fresult;
								
									if($result=='PASS')
									{	$image='<img width="10%" src="'.assets_url.'/images/happy.jpg" class="image-responsive"/>';
										$statement="Congratulations! You Have Cleared the Test.";
									}else
									{
										$image='<img width="10%" src="'.assets_url.'/images/sad.jpg" class="image-responsive"/>';
										$statement="You score is low please study the material again";
									}
								?>
								<div class="col-md-12" style="font-weight:600;font-size:30px;text-align:center;">
									<div class="col-md-4"></div>
									<div  class="col-md-4"style="border:1px solid #eee; padding:5px; text-align:left; box-shadow: 1px 1px 1px 2px;">
									<div>Total Questions : <span><?php echo $totalques;?></span></div>
									<div>Total Right answer: <span><?php echo $rightan; ?></span></div>
									<div>Total Wrong answer: <span><?php echo $wrongan; ?></span></div>
									<div>Result : <span><?php echo $percentage;?>%,<?php echo  $result;?></span></div>
									</div>
									<div class="col-md-4"></div>
									<div class="col-lg-12"><br/><?php echo $image; ?><?php echo $statement ?></div>
								</div>
								
								<br>
							<div class="col-md-12" style="font-weight:600;font-size:30px;text-align:center">
							<?php
							$mat=$this->db->select('s_day')->from('lms_study')->where('department_id',$department->department_id)->group_by('s_day')->order_by('s_day','asc')->get();
							$allmat=$mat->num_rows();
							
							$daycount=$this->db->select('test_day')->from('lms_result')->where('department_id',$department->department_id)->where('userid',$_SESSION['logged_in']['user_id'])->order_by('test_day','asc')->get();
							$studyday=$daycount->num_rows();
							if($daycount->num_rows()>0)
							{
							foreach($daycount->result() as $final);
							$daynes=$final->test_day;
							}else
							{
							$daynes='0';
							}
							
							if($allmat>$studyday)
							{
								/** check if same day has been passed or not **/
								$daycount122334=$this->db->select('test_day')->from('lms_result')->where('department_id',$department->department_id)->where('userid',$_SESSION['logged_in']['user_id'])->where('test_day',$day)->get();
								/** end **/
								if($daycount122334->num_rows()>0)
								{
								if($daynes==0)
								{
									$label="Restart the training";
								}else{
									$label="Start Next Training";
								}
							$nextday=$daynes+1;
								}else{


								$label="Restart the training";

								$nextday=$day;

								}
							?>
							<a href="<?php echo page_url;?>LMS/Start_training/<?php echo $department->department_id ?>/<?php echo $nextday; ?>"><button class="btn btn-success btn-lg" style="font-size: 18px;font-weight:bold; text-align:center"><?php echo $label;?></button></a>
							
							<?php 
							}else if($allmat==$studyday)
							{
							?>
							<a href="<?php echo page_url;?>LMS/ceritficate/<?php echo $department->department_id ?>"><button class="btn btn-success btn-lg" style="font-size: 18px;font-weight:bold;text-align:center">Genrate Ceritficate</button></a>	
							
							<?php }
							else
							{
							$nextday=1;
							?>
							<a href="<?php echo page_url;?>LMS/Start_training/<?php echo $department->department_id ?>/<?php echo $nextday; ?>"><button class="btn btn-success btn-lg" style="font-size: 18px;font-weight:bold; text-align:center">Again Test Start</button></a>
							
							<?php 
							}				

							?>							
							
							
							
							
							</div>	
							
							
							
							
							
							
							
							</div>
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