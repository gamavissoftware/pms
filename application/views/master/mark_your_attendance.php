<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title>Mark Your Attendance</title>      
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
    #pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader img
{
  left: 30%;
  margin-left: -10px;
  margin-top: -10px;
  position: absolute;
  top: 30%;
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
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">Mark Your Attendance</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">


    <?php
    $user_id =$this->session->userdata['logged_in']['user_id'];	
    $currenttime =  date('H:i:s');
    $query = $this->db->select('id,morning_time')->from('mark_your_attendance ')->where('employee_id',$user_id)->where('attendance_date',date('Y-m-d'))->where('evening_time','00:00:00')->get();
    if($query->num_rows()>0){
        foreach($query->result() as $row);
        $morningtime = $row->morning_time;
        
        $currenttime="13:30:00";
    if($currenttime>='13:30:00'){
    ?>

								<?php $message = $this->session->flashdata('message');
								?>
								
                                   <form id="attnform" action="<?php echo page_url;?>Master/User_management/mark_your_attendance/" method="post"  enctype="multipart/form-data" onsubmit="return validate();">
                                        <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
									<div class="row" style="border:1px solid #000">
										<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Mark Your Attendance</label>
														 <span id="error_attendance" style="color:red;">*</span><br>
														 <input type="checkbox" name="attendance" value="1">
													</div>
												</div>
												
												
											<div class="col-md-3">
											<div class="form-group" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" id="save" value="Submit">
											</div>
										</div>
										</div>
                                            </div>
									</form>
                                   <?php }else{
                                   echo '<div class="alert alert-success" style="color:#000;">Your morning Attedance has been marked. Next Attendance will  open at 1:30 PM onward. </div>';
                                   }}else{?>
                                   
                                   <?php $message = $this->session->flashdata('message');
								?>
								
                                   <form id="attnform" action="<?php echo page_url;?>Master/User_management/mark_your_attendance/" method="post"  enctype="multipart/form-data" onsubmit="return validate();">
                                        <div id="pageloader">
                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                                    </div>
									<div class="col-md-6">
									<div class="row" style="border:1px solid #000">
									
										<div class="col-md-8">
													<div class="form-group">
														 <label for="field-2" class="control-label">Mark Your Attendance</label>
														 <span id="error_attendance" style="color:red;">*</span><br>
														 <input type="checkbox" name="attendance" value="1">
													</div>
												</div>
												
												
											<div class="col-md-3">
											<div class="form-group" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" id="save" value="Submit">
											</div>
										</div>
                                            </div>
											</div>
											</div>
									</form>
									<?php }?>

                                </div><hr>
								
								<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<table id="example" class="table table-striped table-bordered dt-responsive nowrap manglesh" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Date</th>
                                        <th>Morning Time</th>
                                        <th>Evening Time</th>
                                        <th>Total Working</th>
                                        <th>Attendance Status</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1;
                                    $year = date('Y');
                                    $month = date('m');
                                    $firstdate = $year."-".$month."-"."01";
                                    $lastdate = $year."-".$month."-"."31";
									
									
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$attendance_date= array();
		$this->db->select('a.*,b.first_name as hrfname, b.last_name as hrlname')->from('mark_your_attendance a')->join('system_users b','a.hr_id=b.user_id','left')->where('a.employee_id',$user_id)->where('a.attendance_date BETWEEN "'. date('Y-m-d', strtotime($firstdate)). '" and "'. date('Y-m-d', strtotime($lastdate)).'"')->order_by('id','desc');
		$query = $this->db->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res); exit;
		foreach($res as $row){
			$empname = $row->employee_name;
		   
			if($row->morning_selfie!==''){
				$morningselfie = "<img src='".selfiepath.$row->morning_selfie."' width='100px'>";
			}else{
				$morningselfie="";
			}
			if($row->evening_selfie!==''){
				$eveningselfie = "<img src='".selfiepath.$row->evening_selfie."' width='100px'>";
			}else{
				$eveningselfie="";
			}
			
			if($row->absent_status=='1'){
			   $evntime="";
			   $morningtime = "";
			   
			}else{
			   $evntime="";
			   if($row->morning_time=='00:00:00'){
			      $morningtime=""; 
			   }else{
			      $morningtime = date('h:i a',strtotime($row->morning_time)); 
			   }
			   
			    if($row->evening_time!=='00:00:00'){
			        $evntime=date('h:i A',strtotime($row->evening_time));
			        
			    }
			    
			    
			    
			}
			
			$remarks="";
			    $markattendance="";
			    if($row->remarks==''){
			    $markattendance  ="Pending at HR";
			    }else{
			        $remarks= $row->remarks."<br><br> <strong>UPDATED BY </strong>".$row->hrfname." ".$row->hrlname;
			    }
			  
		
			
            if($row->evening_time!=='00:00:00' && $row->morning_time!=='00:00:00'){
            $time1 = $row->morning_time;
            $time2 =$row->evening_time;  
            
            $diff = abs(strtotime($time1) - strtotime($time2));

            $tmins = $diff/60;
            $hours = floor($tmins/60);
            $mins = $tmins%60;
            $difference= "<b>$hours</b> hours and <b>$mins</b> minutes</b>";
            
            }else{
            $difference="";
            }
			
		if($row->absent_status=='1'){
		    $attendace_status="Absent";
		}else if($row->onleave=='1'){
		    $attendace_status="Leave";
		}else{
		    $attendace_status="Present";
		}?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><?php echo date('d-m-Y',strtotime($row->attendance_date));?></td>
                                        <td><?php echo $morningtime;?></td>
                                       
                                        <td><?php echo $evntime;?></td>
                                        <td><?php echo $difference;?></td>
                                        <td><?php echo $attendace_status;?></td>
                                    </tr>
						<?php $i++;}?>			
                                </tbody>
                            </table>
                        </div>
                    </div><!-- end col -->
                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
		<!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

     <script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
function validate()
{
   jQuery("input[type='submit']").attr("disabled", false);
   jQuery("#save").val('Submit');
 var isValid=0;
 $("#attnform :input").each(function() {
   var element = $(this).val();
  if (element=="") {
     
	  isValid=1;
   }
});
 
 
 if(isValid==0)
 {
     jQuery("input[type='submit']").attr("disabled", true);
    jQuery("input[type='submit']").val("Please Wait...");
     return true;
 }else
 {
     alert('All Fields are mandatory');
     jQuery("input[type='submit']").attr("disabled", false);
       jQuery("#save").val('Submit');
     return false;
 }
    
}
</script>

    </body>
</html>