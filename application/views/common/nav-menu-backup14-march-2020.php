<?php 
if(!isset($this->session->userdata['logged_in']['user_id']))
{
$conturi= $this->uri->segment(2);
$iduri= $this->uri->segment(3);

if($conturi=='delegation_task' && $iduri=='119' || $iduri=='3' || $iduri=='67' || $iduri=='63' || $iduri=='66'){
    
    $query = $this->db->select('*')->from('system_users')->where('user_id',$iduri)->get();
    foreach($query->result() as $row);
     $data = array('user_id' => $row->user_id,
				     'user_name' => $row->first_name,
				     'last_name' => $row->last_name,
					 'email'=>$row->email,
					 'role'=>$row->user_role_id,
					 'business_location'=>$row->business_location,
					 'department_id'=>$row->department_id,
					  'profile_image'=>$row->profile_image,
                     'user_status'=>$row->user_status);
					$this->session->set_userdata('logged_in',$data);
}
}

$user_id =$this->session->userdata['logged_in']['user_id'];
 $profile_image =$this->session->userdata['logged_in']['profile_image'];
 $user_role =$this->session->userdata['logged_in']['role'];
 $department_id =$this->session->userdata['logged_in']['department_id'];
 $CI =& get_instance();
$CI->load->model('Fms_model');
$q = $this->db->select('profile_image')->from('system_users')->where('user_id',$user_id)->get();
if($q->num_rows()>0){
	foreach($q->result() as $row);
	$profilepic = $row->profile_image;
}else{
	$profilepic="";
}
 ?>
<style>
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
	.quotecss{
		color: #fff;
    text-align: center;
    padding-top: 26px;
    font-size: 13px;
		font-weight:bold;
	}
 </style>
 <div id="profileimg" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

<form method="post" action="<?php echo page_url;?>User/update_profile/<?php echo $user_id;?>" enctype="multipart/form-data">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">CHANGE YOUR PROFILE IMAGE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
			
										
							<div class="col-md-12">
							<input type="file" class="form-control" name="profile" value="" placeholder="upload" required></div>

                                            </div>
</div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="save" class="btn btn-info" value="Save"> 

                                        </div>

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

 <header id="topnav">
            <div class="topbar-main">
                <div class="container-fluid">

                    <!-- LOGO -->
                    <div class="topbar-left">
                        <a href="javascript:void(0);" class="logo" style="  border-radius: 10px; margin-bottom:10px"><img src="<?php echo assets_url;?>images/logo-1.png" class="img-responsive"></a>
                    </div>
                    <!-- End Logo container-->


                    <div class="menu-extras">

                        <ul class="nav navbar-nav navbar-right pull-right">
                           <li class="quotecss hidden-xs">
							   <?php 
                                $q = $this->db->select('quote,	id')->from('quote_of_the_day')->where('added_by',$user_id)->get();
						if($q->num_rows()>0){
							foreach($q->result() as $quote);
							echo '<span>"'.$quote->quote.'"'." <a href='".page_url."Master/User_management/change_quote/".$quote->id."'><i class='fa fa-pencil-square-o' style='color:yellow'></i></a></span>";
							
						}
							   ?>
                            </li>
                          <!--  <li>
                               
                                <div class="notification-box">
                                    <ul class="list-inline m-b-0">
                                        <li>
                                            <a href="javascript:void(0);" class="right-bar-toggle">
                                                <i class="zmdi zmdi-notifications-none"></i>
                                            </a>
                                            <div class="noti-dot">
                                                <span class="dot"></span>
                                                <span class="pulse"></span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                
                            </li>-->

                            <li class="dropdown user-box">
                                <a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true">
                                    <?php if($profilepic){?>
                                    <img src="<?php echo user_profile;?><?php echo $profilepic;?>" alt="user-img" class="img-circle user-img">
                                    <?php }else{?>
                                     <img src="https://prestomitr.com/image_bank/users/1534487737.jpg" alt="user-img" class="img-circle user-img"> <?php }?>
                                    <div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div>
                                </a>

                                <ul class="dropdown-menu">
                                   
                                    <li><a href="<?php echo page_url;?>Master/User_management/change_password"><i class="ti-settings m-r-5"></i> Change Password</a></li>
                                   
                                    <li><a href="<?php echo page_url;?>User/signout"><i class="ti-power-off m-r-5"></i> Logout</a></li>
                                    <?php 
                                    if($this->uri->segment(1)=='Dashboard'){
                                    ?>
                                    	<li><a href="javascript:void(0);" data-toggle="modal" data-target="#profileimg"><i class="ti-upload m-r-5"></i>Change Your Profile Picture</a></li>
                                    	<?php }?>
                                </ul>
                            </li>
                        </ul>
                        <div class="menu-item">
                            <!-- Mobile menu toggle-->
                            <a class="navbar-toggle">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </div>
                    </div>

                </div>
            </div>

            <div class="navbar-custom">
                <div class="container-fluid">
                    <div id="navigation">
                        <!-- Navigation Menu-->
                        <ul class="navigation-menu">
                            <li>
                                <a href="<?php echo page_url;?>Dashboard"><i class="zmdi zmdi-view-dashboard"></i> <span> Dashboard </span> </a>
                            </li>
							
							 <?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','1')->get();
if($module->num_rows()>0)
{
	foreach($module->result() as $moddata);
if($moddata->access=='1')
{
?>
							
							<!-- MASTER START HERE-->
							<li class="has-submenu">
                                <a href="#"><i class="ti-settings"></i><span> Masters </span> </a>
                                <ul class="submenu">
                                   
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','1')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-map-marker" aria-hidden="true"></i>Business Location</a>
									<ul class="dropdown-menu">
									   <li><a href="<?php echo page_url;?>Master/Business_location"> <i class="fa fa-map-marker"></i> Business Location</a></li>
									<li><a href="<?php echo page_url;?>Master/Business_location/country"> <i class="fa fa-globe"></i> Country</a></li>
									<li><a href="<?php echo page_url;?>Master/Business_location/states"><i class="fa fa-bars"></i> State</a></li>
									<li><a href="<?php echo page_url;?>Master/Business_location/cities"><i class="fa fa-bars"></i> City</a></li>
									
									</ul>
									</li>
									<?php }?>
									
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-users"></i> User Management</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','2')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									
									<li><a href="<?php echo page_url;?>Master/User_management/Departments"><i class="fa fa-bars"></i>  Department Management</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','3')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li> <a href="<?php echo page_url;?>Master/User_management/user_role"><i class="fa fa-bars"></i> User Role Management</a></li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','4')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li> <a href="<?php echo page_url;?>Master/User_management"><i class="fa fa-bars"></i> User Listing</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','46')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li> <a href="<?php echo page_url;?>Master/User_management/userwise_permission_dashboard"><i class="fa fa-bars"></i> Set Access Permission</a></li>
									<?php }?>
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','46')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li> <a href="<?php echo page_url;?>Master/User_management/reference"><i class="fa fa-bars"></i> Reference Management</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','46')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li> <a href="<?php echo page_url;?>Form/stand_alone_dashboard_permission"><i class="fa fa-bars"></i> Stand Alone Form/Dashboard</a></li>
									<?php }?>
									</ul>
									</li>
                                   
                                  <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','5')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li>
									<a tabindex="-1" href="<?php echo page_url;?>Master/User_management/presto_team_list/"><i class="fa fa-users"></i> Team Management</a>
									</li>
									<?php }?>
									
									
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','47')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>FMS/holidays"><i class="fa fa-list"></i>Holidays Management</a>
                                    </li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','96')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Dashboard/body_template"><i class="fa fa-list"></i>SMS EMAIL TEMPLATE</a>
                                    </li>
									<?php }?>
									
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','126')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Reporting/userwisepermisionreport"><i class="fa fa-list"></i>USERS PERMISSION REPORT</a>
                                    </li>
									<?php }?>
									
                                </ul>
                            </li>
							<!-- MASTER END HERE-->
<?php }
}?> 
                           
                                  
							 <?php
                                    $module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','2')->get();
                                    if($module->num_rows()>0)
                                    { foreach($module->result() as $moddata);
								     if($moddata->access=='1')
                                    {?>
							<!-- Competitor Analysis start-->
							
							<li class="has-submenu">
                                <a href="#"><i class="fa fa-cubes"></i><span> Competitor Analysis </span> </a>
                                <ul class="submenu">
                                 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','7')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	   
								<li>
								<a tabindex="-1" href="<?php echo page_url;?>Competitor_analysis/product_category"><i class="fa fa-plus-square-o" aria-hidden="true"></i>&nbsp; 
								Product Category</a></li>
									<?php }?>	
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','8')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
															
								<li><a tabindex="-1" href="<?php echo page_url;?>Competitor_analysis/our_products"><i class="fa fa-tasks"></i>&nbsp; Prestogroup Products</a></li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','9')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									
							<li><a tabindex="-1" href="<?php echo page_url;?>Competitor_analysis/add_zone"><i class="fa fa-tasks"></i>&nbsp; Add Zones</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','10')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
								<li>
									<a tabindex="-1" href="<?php echo page_url;?>Competitor_analysis/add_compititors"><i class="fa fa-tasks"></i>&nbsp; Competitor Management</a>
									</li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','11')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>	
									<li><a tabindex="-1" href="<?php echo page_url;?>Competitor_analysis/compare_competitors_products"><i class="fa fa-tasks"></i>&nbsp; Compare Compititor Products</a></li>
									<?php }?>
                                 <!--<li><a tabindex="-1" href="<?php echo page_url;?>Competitor_analysis/zone_comparision"><i class="fa fa-tasks"></i>&nbsp; Zone Comparision</a></li>-->
                                </ul>
                            </li>
									
        <!-- Competitor Analysis end-->
		<?php }
									}?>	
									
<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','3')->get();
if($module->num_rows()>0)
{foreach($module->result() as $moddata);
if($moddata->access=='1')
{?>
		
<!-- Audit Report Start Here-->
                            <li class="has-submenu">
                                <a href="javascript:void(0);"><i class="dripicons-clipboard "></i><span>Audit Report</span> </a>
                                <ul class="submenu">
                                  <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','12')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								 <li><a href="<?php echo page_url;?>Audit_report/audit_type"><i class="dripicons-document"></i> Audit Department</a></li>
									<?php }?>
									 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','13')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>	
									<li><a href="<?php echo page_url;?>Audit_report/audit_section"><i class="dripicons-duplicate"></i> Audit Section</a></li>
									<?php }?>
									 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','14')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								 <li><a href="<?php echo page_url;?>Audit_report/scheduler"><i class="glyphicon glyphicon-th"></i>&nbsp; Turnaround Time (TAT) </a>
                                </li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','15')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                 <li><a href="<?php echo page_url;?>Audit_report/"><i class="dripicons-list "></i> List All Reports</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','17')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                  <li><a href="<?php echo page_url;?>Audit_report/upload_audit_report"><i class="dripicons-list"></i> Upload Audit Report</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','48')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                  <li><a href="<?php echo page_url;?>Audit_report/not_active_task"><i class="dripicons-list"></i> Not Active Audit List</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','49')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                   <li><a href="<?php echo page_url;?>Audit_report/pmsreport"><i class="dripicons-list"></i>MIS Report</a></li>
									<?php }?>
                                </ul>
                            </li>
							
                       <!-- Audit Report End Here-->
<?php }
}?>
<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','4')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{
?>	
<!-- Quotation start Here-->
						 <li class="has-submenu">
                                <a href="#"><i class="ti-folder"></i><span> Quotation</span> </a>
                               <ul class="submenu">
							   
							     <li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-map-marker" aria-hidden="true"></i> Masters</a>
									
								<ul class="dropdown-menu">
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','50')->where('submodule_access','1')->get();
								
									if($qry->num_rows()>0){
									?>	
									 <li><a href="<?php echo page_url;?>Quotation/our_products">Our Products</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','51')->where('submodule_access','1')->get();
								
									if($qry->num_rows()>0){
									?>
									 <li><a href="<?php echo page_url;?>Quotation/customers">Our Customers </a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','52')->where('submodule_access','1')->get();
								
									if($qry->num_rows()>0){
									?>
                                     <li><a href="<?php echo page_url;?>Quotation/standard_subject">Standard Subject</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','53')->where('submodule_access','1')->get();
								
									if($qry->num_rows()>0){
									?>
                                 <li><a href="<?php echo page_url;?>Quotation/email_signature">Email Signature</a></li>
									<?php }?>
                                     <!--<li><a href="<?php// echo page_url;?>Quotation/add_clients">Our Clients</a></li> -->
									 
									 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','54')->where('submodule_access','1')->get();
								
									if($qry->num_rows()>0){
									?>
									 <li>
									<a href="<?php echo page_url;?>Master/TermsManagement/view_all_terms/"> Terms and Conditions Master</a>
									</li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','55')->where('submodule_access','1')->get();
								
									if($qry->num_rows()>0){
									?>
									 <li>
									<a href="<?php echo page_url;?>Master/R_client"> Our Client</a>
									</li><?php }?>
									
</ul>
									</li>
									
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','18')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>	
                                     <li><a href="<?php echo page_url;?>Quotation/Add_quotation">Create New</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','19')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
                                    <li><a href="<?php echo page_url;?>Quotation/list_quotations">List All Quotation</a></li>
									<?php }?>
                                    
                                     <li><a href="http://crm.packingtest.com/presto.html" target="_blank">Quotation Template</a></li>
                                    
                                </ul>
                            </li>
<!-- Quotation End Here-->
<?php }
}?>
<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','5')->get();
if($module->num_rows()>0)
{foreach($module->result() as $moddata);
if($moddata->access=='1')
{?> 
<!-- IT SUPPORT START HERE-->
                            
                             <li class="has-submenu">
                                <a href="#"><i class="ti-support "></i><span> Support </span> </a>
                                <ul class="submenu">
                                    <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','20')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-map-marker" aria-hidden="true"></i>
									IT Asset Masters</a>
									<ul class="dropdown-menu">
									 <li><a href="<?php echo page_url;?>Tech_support/asset_type">Asset Type</a></li>
									 
									 <li><a href="<?php echo page_url;?>IT_Assets/brand">Brand </a></li>
									 
                                     <li><a href="<?php echo page_url;?>IT_Assets/microsoft_office">Microsoft Office</a></li>
									 
                                     <li><a href="<?php echo page_url;?>IT_Assets/ms_office_license">Microsoft Office Licence Types</a></li>
									 
                                    <li><a href="<?php echo page_url;?>IT_Assets/window_license_type">Windows Licence Types </a></li>
									
									 <li><a href="<?php echo page_url;?>IT_Assets/operating_system">Operating System</a></li>
									 
									  
									</ul>
									</li>
<?php }?>



									<li class="dropdown-submenu">
									<a tabindex="-1" href="<?php echo page_url;?>Master/Business_location"><i class="fa fa-map-marker" aria-hidden="true"></i>
									IT Support</a>
									<ul class="dropdown-menu">
									  <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','20')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
									 <li><a href="<?php echo page_url;?>Tech_support/location">Add IT Asset Location</a></li>
                                     <li><a href="<?php echo page_url;?>Tech_support/items">Add IT Assets</a></li>
                                     <li><a href="<?php echo page_url;?>IT_Assets">IT Assets</a></li>
                                     
                                     <li><a href="<?php echo page_url;?>IT_Assets/expiry_warranty">Asset Warranty Expire</a></li>
                                    <?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','21')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>	
                                    <li><a href="<?php echo page_url;?>Tech_support">Raise New Ticket</a></li>
                                   	<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','22')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>	
									 <li><a href="<?php echo page_url;?>Tech_support/view_status">View Tickets</a></li>
									
									  <li><a href="<?php echo page_url;?>Tech_support/closed_ticket_list">View Closed Tickets</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','23')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									  <li><a href="<?php echo page_url;?>Tech_support/tech_faqs">IT FAQs</a></li>
									<?php }?>
									</ul>
									</li>
									
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-cubes"></i> Dispatch Support</a>
									<ul class="dropdown-menu">
									 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','24')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
									<li><a href="<?php echo page_url;?>Dispatch_support">Raise Ticket</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','25')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Dispatch_support/view_status/<?php echo $user_id;?>">View Tickets</a></li>
									
									<li><a href="<?php echo page_url;?>Dispatch_support/closed_ticket_list/<?php echo $user_id;?>">View Closed Tickets</a></li>
									<?php }?>
									
									</ul>
									</li>
									
									
                                   <li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-industry"></i> Service Support</a>
									<ul class="dropdown-menu">
									  <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','26')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?> 
									<li><a href="<?php echo page_url;?>Service_support">Raise Ticket</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','27')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Service_support/view_status/<?php echo $user_id;?>">View Tickets</a></li>
									<li><a href="<?php echo page_url;?>Service_support/closed_ticket_list/">View Closed Tickets</a></li>
									<?php }?>
									
									</ul>
									</li>
									
									  <li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-wrench"></i> Maintenance Support</a>
									<ul class="dropdown-menu">
									 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','28')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>   
									<li><a href="<?php echo page_url;?>Maintenance_support">Raise Ticket</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','29')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Maintenance_support/view_status/">View Status</a></li>
										<li><a href="<?php echo page_url;?>Maintenance_support/closed_ticket_list/">View Closed Ticket</a></li>
									<?php }?>	
									
									</ul>
									</li>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','30')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
									<a href="<?php echo page_url;?>Master_emails"><i class="fa fa-envelope"></i> Master Emails</a>
									
									</li>
									<li>
									<a href="<?php echo page_url;?>Master_access"><i class="fa fa-lock"></i> Support Master Access</a>
									
									</li><?php }?>
									
                                  
								  
                                </ul>
                            </li>
								
<?php } 
}?>
<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','6')->get();
if($module->num_rows()>0)
{foreach($module->result() as $moddata);
if($moddata->access=='1')
{?> 

							<li class="has-submenu">
                                <a href="#"><i class="fa fa-building-o"></i><span> Office Maintenance</span> </a>
                                <ul class="submenu">
                                     <!--<li><a href="<?php echo page_url;?>Office_maintenance">Office Maintenance</a></li>-->
                                    <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','31')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									 <li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-wrench"></i> Power Factor</a>
									<ul class="dropdown-menu">
									<li><a href="<?php echo page_url;?>Power_factor">Add/View Company</a></li>
									
									<li><a href="<?php echo page_url;?>Power_factor/electricity_dashboard">Electricity Reading</a></li>
									<li><a href="<?php echo page_url;?>Power_factor/generator_dashboard">Generator Reading</a></li>
									<li><a href="<?php echo page_url;?>Power_factor/diesel_dashboard">Diesel Record</a></li>
									
									
									</ul>
									</li>
									<?php }?>
                                   
                                </ul>
                            </li>
<?php }}?>


<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','7')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 									

<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>FMS</span> </a>
                                <ul class="submenu">
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','33')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>FMS/production_plan_flow">PRODUCTION FLOW</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','34')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/fms_flow">FMS FLOW</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','35')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/instruments">INSTRUMENTS MASTER</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','36')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/add_color_combination">CONDITIONAL COLOR MASTER</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','37')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/order">ORDER MANAGEMENT</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','38')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/view_planned_orders">PLANNED ORDERS</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','39')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/planned_actual">PLANNED TO ACTUAL</a></li>
									<?php }?>
									
								</ul>
                            </li>
<?php }
}?>

<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','8')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 
 <li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>DELEGATION</span> </a>
                                <ul class="submenu">
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','8')->where('submoduleid','41')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_master"><i class="fa fa-list"></i> Delegation Master </a>
                                    </li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','8')->where('submoduleid','42')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_task"><i class="fa fa-list"></i> Delegation Form </a>
                                    </li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','8')->where('submoduleid','43')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_dashboard"><i class="fa fa-list"></i> Delegation Dashboard </a>
                                    </li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','8')->where('submoduleid','44')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Delegation/delegated_task"><i class="fa fa-list"></i> Task delegated to You </a>
                                    </li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','8')->where('submoduleid','45')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Delegation/delegated_task_history"><i class="fa fa-list"></i> Delegated Task History </a>
                                    </li><?php }?>
								</ul>
                            </li>	
<?php }
}?>	
							<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','9')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 									

<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>CHECKLIST</span> </a>
                                <ul class="submenu">
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','9')->where('submoduleid','56')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Checklist/Turnaroundtime">Checklist TAT</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','9')->where('submoduleid','57')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/create_checklist">Checklist Management</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','9')->where('submoduleid','58')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/view_your_checklist">View Your Checklist/Report</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','9')->where('submoduleid','59')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/user_monthly_report">View Checklist Report</a></li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','9')->where('submoduleid','66')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/checklist_dashboard">Auditor Checklist Dashboard</a></li>
									<?php }?>
								</ul>
                            </li>
<?php }
}
?>	

<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','13')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 									

<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>SALES</span> </a>
                                <ul class="submenu">
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','116')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/conveyance_voucher">Conveyance Vouchers</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','121')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/local_conveyance_dashboard">Conveyance Vouchers Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','122')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/local_conveyance_hod_dashboard">Conveyance Vouchers HOD Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','123')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/local_conveyance_account_dashboard">Conveyance Vouchers Account Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','124')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/local_conveyance_hr_dashboard">Conveyance Vouchers HR Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','117')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									 <li><a href="<?php echo page_url;?>Sales/sale_service_conveyance_voucher">Sale Service Conveyance Vouchers</a></li>
									<?php }?>
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','127')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
								<li><a href="<?php echo page_url;?>Sales/sale_service_dashboard">Sale Service Conveyance Dashboard</a></li>
									<?php }?>
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','128')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
							<li><a href="<?php echo page_url;?>Sales/sale_service_conveyance_hod_dashboard">Sale Service Conveyance HOD Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','129')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
							<li><a href="<?php echo page_url;?>Sales/sale_service_conveyance_account_dashboard">Sale Service Conveyance Account Dashboard</a></li>
									<?php }?>
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','13')->where('submoduleid','130')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
						<li><a href="<?php echo page_url;?>Sales/sale_service_conveyance_hr_dashboard">Sale Service Conveyance HR Dashboard</a></li>
									<?php }?>
								</ul>
                            </li>
<?php }
}
?>
							<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','10')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 									

<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>FORM</span> </a>
                                <ul class="submenu">
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid','60')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Form/create_new_form">CREATE NEW FORM</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid','61')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Form/pending_for_review">PENDING FORMS FOR REVIEW</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid','63')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Form/master_index">MASTER INDEX</a></li>
									<?php }?>
									<?php 
									$query = $this->db->select('id, moduleid, submodule')->from('submodule')->where('moduleid','10')->limit(1000,2)->get();
									//echo "<pre>"; print_r($query->result()); exit;
									foreach($query->result() as $formmodule){
									
									$q = $this->db->select('id,form_title, status')->from('dynamic_forms')->where('status','1')->where('form_title',$formmodule->submodule)->get();
									foreach($q->result() as $row){
									?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','10')->where('submoduleid',$formmodule->id)->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>"><?php echo $row->form_title;?></a></li>
									<?php 
									}
									}
									
									
									}?>
									
									<?php 
									$q = $this->db->select('id, dashboard_title, status')->from('dynamic_forms')->where('user_id',$user_id)->where('status','1')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
									?>
									<li><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>"><?php echo $row->dashboard_title;?></a></li>
										<?php }
										}?>
									
									<?php 
									$q = $this->db->select('b.id,a.form_id, a.user_id, b.dashboard_title')->from('dynamic_form_dashboard_access a')->join('dynamic_forms b','a.form_id=b.id','left')->where('a.user_id',$user_id)->where('b.status','1')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
									?>
									<li><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>"><?php echo $row->dashboard_title;?></a></li>
										<?php }
										}?>
								</ul>
                            </li>
<?php }
}
?>
<?php
if($user_role=='1')
{
?>                
<?php
}else
{
$fmsp=array();
	$fmsassined='0';
/** Check for Any FMS Role **/
	$rep=$this->db->select('b.production_flowid,a.production_flow')->from('flowtousers b')->join('production_flow a','a.id=b.production_flowid')->where('b.userid',$user_id)->get();
									if($rep->num_rows()>0)
									{										
									foreach($rep->result() as $respp)
										{
									$fmsp[]=$respp->production_flowid;
									}
																	
									$fmsassined = "'" . implode ( "', '", $fmsp ) . "'";
									//echo $fmsassined;exit;
									}else
									{
									$fmsassined='0';
									}
						
if($fmsassined<>'0')
{
	
	
	foreach($rep->result() as $respp)
									{
										
										//echo "hi"; exit;
	/** Get Process Name **/
	$process=$this->db->select('fms_flow,flow_id,production_flow_id,dependency')->from('fms_flow')->where('production_flow_id',$respp->production_flowid)->where('who_wedo',$user_id)->get();
	/** End **/
?>
<li class="has-submenu">
                                <a href="#"><i class="fa fa-google-plus"></i><span><?php echo $respp->production_flow;?> FMS</span> </a>
								<?php
								if($process->num_rows()>0)
								{
								?>
	                                <ul class="submenu">
                                  <?php
									$donarr=array();
								  foreach($process->result() as $proc)
								  {
									  
									  $flowtottask=$CI->Fms_model->menunotifications($proc->flow_id,$proc->dependency);
									
									  
								  ?>
									<li><a href="<?php echo page_url;?>Orderstage/process/<?php echo $proc->flow_id;?>/<?php echo $proc->production_flow_id;?>"><div class="badge1" <?php if($proc->production_flow_id!='2' && $proc->production_flow_id!='3') {?> data-badge="<?php echo $flowtottask;?>" <?php } ?>"><?php echo $proc->fms_flow;?></div></a></li>
									<?php
									}
									?>
								</ul>
								<?php
								}
								?>
                            </li>	
	 
	
                  

<?php
									}
}
}
?>	
								<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','11')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 									
										<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>REPORTING</span> </a>
										<ul class="submenu">
										
										<?php
										$reslongre=$this->db->select('id,production_flow')->from('production_flow')->where('longreport','1')->order_by('sortorder','ASC')->get();
										if($reslongre->num_rows()>0)
										{
										foreach($reslongre->result() as $reslongre1)
										{
											$term="FMS ".strtoupper($reslongre1->production_flow)." REPORTING";
										$qry = $this->db->select('a.id')->from('module_capablity a')->join('submodule b','a.submoduleid=b.id')->where('a.role_id',$user_id)->where('a.moduleid','11')->where('b.submodule',$term)->where('a.submodule_access','1')->get();
										//$res = $qry->result();
										if($qry->num_rows()>0){
											
										?>
										
										<li class=""><a href="<?php echo page_url;?>FMS/fms_reporting/<?php echo $reslongre1->id;?>"><i class="fa fa-list"></i> FMS <?php echo strtoupper($reslongre1->production_flow);?> REPORTING </a></li>
										<?php
										}
										
										}
										}
										?>
											<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','83')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/misscore"><i class="fa fa-list"></i> OVERALL MIS </a></li>
										<?php } ?>
										
											<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','82')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>FMS/fmsmis"><i class="fa fa-list"></i> FMS MIS </a></li>
									<?php } ?>	
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','125')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>FMS/nonfmsmis"><i class="fa fa-list"></i> NON FMS MIS </a></li>
									<?php } ?>	
									
									<?php
									 $qry=$this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','120')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>FMS/nonfmsmis"><i class="fa fa-list"></i> DASHBOARD MIS </a></li>
									<?php } ?>	
										
										
											<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','87')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Delegation/audit_dashbaord"><i class="fa fa-list"></i>AUDITOR DELEGATION REPORT</a></li>
									<?php } ?>	
										
											<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','91')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/pendingorders"><i class="fa fa-list"></i>PENDING ORDERS REPORT</a></li>
									<?php } ?>	
										
											
												<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','99')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/readyforpacking"><i class="fa fa-list"></i>MACHINES FOR PACKING</a></li>
									<?php } ?>	
										
											<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','100')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/completedorders"><i class="fa fa-list"></i>CRM READY ORDERS</a></li>
									<?php } ?>
										
											
												<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','109')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/dispatchfortommorow"><i class="fa fa-list"></i>DISPATCH FOR TOMMOROW</a></li>
									<?php } ?>
										
										
												<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','110')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/servicerequest"><i class="fa fa-list"></i>SERVICE REQUEST</a></li>
									<?php } ?>
										
										
												<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','101')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/previousorders"><i class="fa fa-list"></i>PREVIOUS ORDER HISTORY</a></li>
									<?php } ?>
									
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','118')->where('submodule_access','1')->get();
									//$res = $qry->result();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Reporting/lotorderlist"><i class="fa fa-list"></i>LOT ORDER LIST</a></li>
									<?php } ?>
										
										</ul>
										</li>
										
<?php
} 
}
?>


	<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','12')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 									
										<li class="has-submenu">
                                <a href="<?php echo page_url;?>/Salestool"><i class="fa fa-list"></i><span>SALES TOOL</span> </a></li>
										
<?php
} 
}
?>
                   </ul>
                        <!-- End navigation menu  -->
                    </div>
                </div>
            </div>
        </header>