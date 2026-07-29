<?php
$CI =& get_instance();
$CI->load->model('Fms_model');
$user_id=$_SESSION['logged_in']['user_id'];
 $user_role =$this->session->userdata['logged_in']['role'];
$first_name =$this->session->userdata['logged_in']['user_name'];
$last_name =$this->session->userdata['logged_in']['last_name'];
$profile_image =$this->session->userdata['logged_in']['profile_image'];
$q = $this->db->select('profile_image')->from('system_users')->where('user_id',$user_id)->get();
if($q->num_rows()>0){
	foreach($q->result() as $row);
	$profilepic = $row->profile_image;
}else{
	$profilepic="";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <title>Prestogroup Dashboard</title>

         <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
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

        <script src="assets/js/modernizr.min.js"></script>

<style>
.share-it{
	position:fixed;
	min-height:200px;
	width:40px;
	left:0;
	z-index:9;
	top:20%;
}	
.share-it i{
	font-size:16px;
}
	
a.multipage{background:#ee3046; border:2px #ee3046 solid; color:#fff;} 	
a.multipage:hover{background:#fff; border:2px #fff solid; color:#333;} 	
	
	
	.facebook{margin:10px auto; float:left; margin-right:4px;}
.facebook  a{
	color:#fff;
	padding:1px 1px;
	background-color:#77cdf1;
	display:inline-block;
	transition:0.5s ease;
}

.twitter{margin:10px auto; float:left; margin-right:4px;}
.twitter  a{
	color:#fff;
	padding:1px 1px;
	background-color:#006dac;
	display:inline-block;
	text-align:center;
	transition:0.5s ease;
}


.google{margin:0px auto; float:left; margin-right:4px;}
.google  a{
	color:#fff;
	padding:1px 1px;
	background-color:#ef7f1a;
	display:inline-block;
	transition:0.5s ease;
}
.finsys{margin:10px auto; float:left; margin-right:4px;}
.finsys  a{
	color:#fff;
	padding:1px 1px;
	background-color:#ef7f1a;
	display:inline-block;
	transition:0.5s ease;
}



.rss{margin:10 auto; margin-top:10px; float:left; margin-right:4px;}
.rss a{
	color:#fff;
	padding:1px 1px;
	background-color:#bdcc1f;
	display:inline-block;
	transition:0.5s ease;
}
.panel-group {
    margin-bottom: 0px;
}

.badge1 {
		position:relative;
		
	}
	.badge1[data-badge]:after {
		content:attr(data-badge);
		position:absolute;
		top:-10px;
		right:-10px;
		font-size:12px;
		font-weight:bold;
		background:green;
		color:white;
		width:18px;height:18px;
		text-align:center;
		line-height:18px;
		border-radius:50%;
		box-shadow:0 0 1px #333;
	}
	.card-box {
	    padding:0px;
	    min-height: 65px;
	}
	.minheightspace{
	    min-height:100px;
	}
</style>
    </head>


    <body>

        <!-- Navigation Bar-->
       <?php $this->load->view('common/nav-menu');?>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid" style="background-color:#fff; min-height:463px;">

                <!-- Page-Title -->
               
			<div class="col-md-1 hidden-xs" style="background-color:#fff;">
				<div class="share-it">
		<div class="facebook">
		 <a href="http://www.prestoquality.com/" target="_blank"><img src="assets/images/logo-salesforce.png" width="110px"></a>
		</div>
		<!--<div class="twitter">
		 <a href="https://trello.com/" target="_blank"><img src="assets/images/trello-logo-blue.png" width="110px" height="68px"></a>
		</div>-->
	
		<div class="google hidden-xs">
		 <a href="http://chanakya/" target="_blank"><img src="assets/images/Chanakya_logo.jpg" width="110px" height="68px"></a>
		</div>
		<!--<div class="rss">
		 <a href="https://sites.google.com/site/chanakyapresto/" target="_blank"><img src="assets/images/intra_logo.png" width="110px" height="68px"></a>
		</div>-->
		<div class="rss">
		 <a href="http://www.fieldsense.in/" target="_blank"><img src="assets/images/fieldSense.png" width="110px" height="68px"></a>
		</div>
	  </div>
			</div>
             <div class="col-md-11 col-xs-12 col-sm-12">
					<div class="row minheightspace">
				  <h4 class="page-title">Dashboard 
				  
						 <span><?php echo $this->session->flashdata('message'); ?></span></h4><hr>
						 
                    <?php
                    $module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','1')->get();
                    if($module->num_rows()>0)
                    {
                    foreach($module->result() as $moddata);
                    if($moddata->access=='1')
                    {
                    ?>
					<div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 ">
                        <?php if($user_role=='1'){?>
                        
                        <a href="<?php echo page_url;?>Form/master_index/"><div class="card-box badge1" data-badge="">
                            <div class="text-center">
                                 <div class="row">
								
								<div class="col-md-12"><h4>MASTER INDEX</h4></div>
								
							</div>
                            </div>
                        </div></a>
                        <?php }else{?>
                        <a href="<?php echo page_url;?>Form/view_master_index/"><div class="card-box badge1" data-badge="">
                            <div class="text-center">
                                 <div class="row">
								
								<div class="col-md-12"><h4>MASTER INDEX</h4></div>
								
							</div>
                            </div>
                        </div></a>
                        <?php }?>
                    </div>
						<?php 
						}
						}?>
						
						
				  <?php 
						$this->db->select('a.id')->from('delegation_task a');
						$q = $this->db->where('a.delegate_to',$user_id)->where('a.task_status','0')->get();
						$counttotal = count($q->result());
						if($q->num_rows()>0){
						
					?>
					<div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>Delegation/delegated_task/<?php echo $user_id;?>"><div class="card-box badge1" data-badge="<?php echo $counttotal;?>">
                            <div class="text-center">
                                 <div class="row">
								
								<div class="col-md-12"><h4>TASK DELEGATED TO YOU</h4></div>
								
							</div>
                            </div>
                        </div></a>
                    </div>
						<?php 
						}?>
						
					<?php 
						$this->db->select('a.task_id')->from('compliance_task_report a')->join('compliance_set_date b','a.task_id=b.task_id','left');
						$q = $this->db->where('a.user_id',$user_id)->where('a.status','1')->where('b.dateforemail',date('Y-m-d'))->get();
						$counttotal = count($q->result());
						if($q->num_rows()>0){
						
					?>
					<div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>Checklist/view_your_checklist"><div class="card-box badge1" data-badge="<?php echo $counttotal;?>">
                            <div class="text-center">
                                 <div class="row">
								<div class="col-md-12"><h4>VIEW YOUR CHECKLIST</h4></div>
							</div>
                            </div>
                        </div></a>
                    </div>
						<?php 
						}?>
						
						
					<?php 
						$query=$this->db->select('a.id, a.user_id, a.dashboard_title, a.description, a.status')->from('dynamic_forms a')->where('a.user_id',$user_id)->where('a.status','1')->where('a.form_running_status','0')->get();
						foreach($query->result() as $row){
					?>
					<div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>"><div class="card-box badge1" data-badge="
<?php 
$query = $this->db->select('id,work_status')->from('dynamic_form_data')->where('form_id',$row->id)->where('work_status','0')->get();
$res = $query->result();
echo count($res);
?>">
                            <div class="text-center">
                                 <div class="row">
							
								<div class="col-md-12"><h4><?php echo ucfirst($row->dashboard_title);?></h4><p><?php echo ucfirst($row->description);?></p></div>
								
							</div>
                            </div>
                        </div></a>
                    </div>
						<?php }?>

						<?php
$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access,madd')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','21')->where('submodule_access','1')->get();
if($qry->num_rows()>0){	

foreach($qry->result() as $qry1);

   $notrcvdcount=$this->Fms_model->getnotrecievedorder($user_id);

?>		
	<div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 ">
                        <a href="<?php echo page_url;?>FMS/notplannedorders"><div class="card-box badge1" data-badge="<?php echo $notrcvdcount;?>">
                    <div class="text-center">
                                 <div class="row">
							
								<div class="col-md-12"><h4>Order(s) Not Clear</h4></div>
								
							</div>
                            </div>
                        </div></a>
                    </div>

<?php
}
?>
       
       </div>
       <div class="row">
         
          <?php 
          $query = $this->db->select('id,image')->from('dashboard_image')->limit(4)->order_by('id','desc')->get();
              foreach($query->result() as $row){
              ?>
              <div class="col-md-4"><img src="<?php echo dashboardimg;?><?php echo $row->image;?>" width="350px" class="img-responsive text-center  card-box"><?php
            
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','202')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?><a href="<?php echo page_url;?>Dashboard/remove_dashboard_image/<?php echo $row->id;?>"><i class="fa fa-trash" style="position: absolute;left: 13px;top: 3px;color: red;font-size: 17px;"></i></a><?php }?></div>
              
              <?php }?>
              <div class="clearfix"></div>
              <div class="col-md-12" style="padding:10px 0px 0px 0px">
                  <div class="col-md-11"></div>
                  <div class="col-md-1">
                   <?php
            
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','202')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
  
    <span class="btn btn-success btn-xs text-center" data-toggle="modal" data-target="#uploadnewimg">UPLOAD IMAGE</span>
    <?php
									}

        ?>
        </div>
              </div>        
       
                  
					
				
                </div>
                <!-- end row -->
			</div>

           


          <div class="row">
              
             <div class="col-md-1"></div>
			  <div class="col-md-11" style="margin-top: 25px;">
						
<h3 class="text-center">News/Events&nbsp; &nbsp;   <?php
            
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','202')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?><span class="btn btn-warning btn-xs text-center" data-toggle="modal" data-target="#con-close-modal1">ADD NEWS/EVENTS</span> <a href="<?php echo page_url;?>Dashboard/view_all_events"></a><?php }?><span class="pull-right"> <?php 
						$q = $this->db->select('quote')->from('quote_of_the_day')->where('added_by',$user_id)->get();
						if($q->num_rows()>0){
							
						}else{
						?>
						
						
							<div class="col-md-12">
								<div class="form-group">
									
									<center><span class="btn btn-warning btn-xs text-center" data-toggle="modal" data-target="#con-close-modal">ADD QUOTE OF THE DAY</span></center>
								</div>
							</div>
							
						<?php }?></span></h3>  	<hr> 
			</div>
			
			  <marquee width="100%" direction="left" height="100px"><?php 
			$i=1;
			$q = $this->db->select('event_date, news_events, image')->from('presto_news_events')->order_by('event_date','desc')->get();
			$count = count($q->result());
			foreach($q->result() as $row){
			?>
		<?php 	if($row->image){?>
		<img src="<?php echo eventimgpath;?><?php echo $row->image;?>" width="100px">
		    <?php 
		    }?>
<strong style="color:red;"> <?php echo $row->news_events;?></strong> &nbsp; &nbsp; &nbsp; <?php if($count>1){?>| <?php }?>  &nbsp; &nbsp; &nbsp;<?php $i++;}?>

<?php 
				  $query = $this->db->select('first_name, last_name,profile_image')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where("MONTH(date_of_birth) = MONTH(NOW()) AND DAY(date_of_birth) = DAY(NOW())")->get();
				  if($query->num_rows()>0){
				      foreach($query->result() as $birthday){
				  ?>
				  
				   
				   <span style="font-size:14px; color:green"> <img src="<?php echo user_profile;?><?php echo $birthday->profile_image;?>" alt="user-img" class="img-circle user-img" width="100px">Happy Birthday <?php echo $birthday->first_name." ".$birthday->last_name;?> </span>
				  <?php }
				  }?>
</marquee>
			
			  </div>
			  
			 
  <?php
            
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','202')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>


			    <div class="row">
			        <div class="col-md-1 hidden-xs"></div>
                    <div class="col-md-11 col-sm-12 col-xs-12 col-lg-11">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>SR. NO.</th>
									<th>Date</th>
                                    <th>News Events</th>
                                    <th>Image</th>
                                    <th>Edit</th>
                                    <th>Remove</th>
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                <?php }?>
			  
			  <div id="con-close-modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="<?php echo page_url;?>Dashboard/add_news_events" enctype="multipart/form-data">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">ADD NEWS/EVENTS</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Date</label>
												<input type="date" name="event_date" id="event_date" value="<?php echo date('Y-m-d');?>" class="form-control">
											</div>
												<div class="form-group">
												<label>Upload Image</label>
												<input type="file" name="event_file" id="event_file" value="" class="form-control">
											</div>
										</div>
										<div class="col-md-8">
											<div class="form-group">
											<label>News/Events</label>
												<textarea class="form-control" name="news_events" required></textarea>
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
                            
                            
                            <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                <form id="loginForm" method="post" action="<?php echo page_url;?>Dashboard/add_Quote" enctype="multipart/form-data">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">ADD QUOTE OF THE DAY</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
			
										<div class="col-md-12">
											<div class="form-group">
												<textarea class="form-control" name="quote_of_the_day" required></textarea>
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
                            
                             <div id="uploadnewimg" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                <form id="loginForm" method="post" action="<?php echo page_url;?>Dashboard/add_new_images" enctype="multipart/form-data">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">UPLOAD IMAGE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
			
										<div class="col-md-12">
											<div class="form-group">
												<input type="file" name="image" id="image" value="" class="form-control" required>
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
"scroll-X":false,
"sAjaxSource": "<?php echo page_url;?>Dashboard/view_all_events_list/",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'date' },
				{ mData: 'event_name' },
			    {mData:'image'},
				{ mData: 'edit' },
				{mData:'remove'}
				
				
		]
});   
});

</script>



    </body>
</html>