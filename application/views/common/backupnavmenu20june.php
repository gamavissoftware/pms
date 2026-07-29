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
                                   
                                   <li><a href="<?php echo page_url;?>Master/User_management/mark_your_attendance"><i class="ti-settings m-r-5"></i> Mark Your Attendance</a></li>
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
                                <a href="<?php echo page_url;?>Dashboard"><i class="zmdi zmdi-view-dashboard"></i> <span> DASHBOARD </span> </a>
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
                                <a href="#"><i class="ti-settings"></i><span> MASTERS </span> </a>
                                <ul class="submenu">
                                   
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; USER MANAGEMENT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','1')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li> <a href="<?php echo page_url;?>Master/User_management" title="List of all users"><i class="fa fa-bars"></i> User Listing</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','2')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
										<li> <a href="<?php echo page_url;?>Master/User_management/user_role" title="User Roles"><i class="fa fa-bars"></i> User Role Management</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','3')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									 <li><a href="<?php echo page_url;?>Master/User_management/Departments" title="User Departments"><i class="fa fa-bars"></i>  Department Management</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','4')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li> <a href="<?php echo page_url;?>Master/User_management/userwise_permission_dashboard" title="Set user wise permission"><i class="fa fa-bars"></i> Set Access Permission</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','5')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li> <a href="<?php echo page_url;?>Master/User_management/reference"><i class="fa fa-bars" title="Upload References"></i> Reference Management</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','6')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									 <li><a href="<?php echo page_url;?>Master/Business_location" title="Manage Business Locations"> <i class="fa fa-bars"></i> Business Location</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','7')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									
									<li> <a href="<?php echo page_url;?>Form/stand_alone_dashboard_permission"><i class="fa fa-bars"></i> Stand Alone Form/Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','217')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									
									<li> <a href="<?php echo page_url;?>Form/hod_dashboard_quick_links"><i class="fa fa-bars"></i> Set HOD Quick Access Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','216')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									
									<li> <a href="<?php echo page_url;?>Form/dashboard_quick_links"><i class="fa fa-bars"></i> Set Quick Access Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','8')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									  <li>
									<a tabindex="-1" href="<?php echo page_url;?>Master/User_management/presto_team_list/" title="Create & Manage Team"><i class="fa fa-bars"></i> Team Management</a>
									</li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','9')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>FMS/holidays" title="Yearly Holidays"><i class="fa fa-bars"></i>  Holidays Management</a>
                                    </li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','10')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Dashboard/body_template" title="Manage Email/SMS content"><i class="fa fa-bars"></i>  Sms Email Template</a>
                                    </li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','57')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a href="<?php echo page_url;?>Reporting/userwisepermisionreport" title="Permissions given to users"><i class="fa fa-list"></i> Users Permission Report</a>
                                    </li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','59')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a href="<?php echo page_url;?>Master/User_management/add_new_vendor" title="Manage Suppliers"><i class="fa fa-list"></i> Vendor Management</a>
                                    </li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','106')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a href="<?php echo page_url;?>Master/User_management/email_template" title="Email Template"><i class="fa fa-list"></i> Email Template</a>
                                    </li>
									<?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','203')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a href="<?php echo page_url;?>HOD_Team" title="HOD MANAGEMENT"><i class="fa fa-list"></i> HOD Management</a>
                                    </li>
									<?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','206')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a href="<?php echo page_url;?>Hr/presto_employee_record" title="Employee Planned Report"><i class="fa fa-list"></i>Prestogroup Employees</a>
                                    </li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','204')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
                                    <a href="<?php echo page_url;?>HOD_Team/team_report" title="Employee Planned Report"><i class="fa fa-list"></i>Employee Planned Report</a>
                                    </li>
									<?php }?>
									</ul>
									</li>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> FORM MANAGEMENT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','11')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Form/create_new_form" title="Create New Dynamic Forms"><i class="fa fa-list" aria-hidden="true"></i> Create New Form</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','12')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Form/pending_for_review"><i class="fa fa-list" aria-hidden="true"></i> Pending Form for Review</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','13')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<?php 
									if($user_role=='1' || $user_role=='68'){
									?>
									<li><a href="<?php echo page_url;?>Form/master_index"> <i class="fa fa-list" aria-hidden="true"></i> Master Index</a></li><?php }else{?>
										<li><a href="<?php echo page_url;?>Form/master_index"> <i class="fa fa-list" aria-hidden="true"></i> Master Index</a></li><?php }?>
										<li><a href="<?php echo page_url;?>Form/all_forms"> <i class="fa fa-list" aria-hidden="true"></i> All Dynamic Forms</a></li>
									<?php }?>
									</ul>
									
									</li>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> DELEGATION MANAGEMENT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','14')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									  <li>
                                    <a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_master"><i class="fa fa-list" aria-hidden="true"></i> Delegation Master </a>
                                    </li>
									<?php }?>
									
									</ul>

									</li>
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> CHECKLIST MANAGEMENT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','15')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/Turnaroundtime"><i class="fa fa-list" aria-hidden="true"></i> Checklist TAT</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','16')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/create_checklist"><i class="fa fa-list" aria-hidden="true"></i> Checklist Management</a></li>
									<?php }?>
									
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','135')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/mispercentage"><i class="fa fa-list" aria-hidden="true"></i> Checklist MIS Factor</a></li>
									<?php }?>
									</ul>

									</li>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i>&nbsp; FMS MANAGEMENT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','17')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/production_plan_flow"><i class="fa fa-list" aria-hidden="true"></i> Production Flow </a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','18')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/fms_flow"><i class="fa fa-list" aria-hidden="true"></i> Fms Flow</a></li><?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','104')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/machinecostprice"><i class="fa fa-list" aria-hidden="true"></i>MACHINE COST PRICE </a></li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','20')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/instruments"><i class="fa fa-list" aria-hidden="true"></i> Instruments Management</a></li><?php }?>
									
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','19')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/add_color_combination"><i class="fa fa-list" aria-hidden="true"></i>Conditional Color Management</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','21')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/order"><i class="fa fa-list" aria-hidden="true"></i> Order Management</a></li>
									<?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','167')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/orderplanninglist"><i class="fa fa-list" aria-hidden="true"></i> Order Planning</a></li>
									<?php }?>
									
									</ul>
									</li>

                             	<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);" ><i class="fa fa-list" aria-hidden="true"></i>&nbsp; SALES TOOL MANAGEMENT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','71')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Salestool/salestooltemplate"><i class="fa fa-list"></i> SET SCRIPT TEMPLATE</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','140')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li><a href="<?php echo page_url;?>Salestool/machinedata"><i class="fa fa-list"></i> MACHINES DATA</a></li>
								<?php }?>
								
									
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','141')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li><a href="<?php echo page_url;?>Salestool/industrytype"><i class="fa fa-list"></i>INDUSTRY</a></li><?php }?>
								
								
								
								
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','142')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li><a href="<?php echo page_url;?>Salestool/communicationtype"><i class="fa fa-list"></i> COMMUNICATION TYPE</a></li><?php }?>
								
								
								
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','143')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li><a href="<?php echo page_url;?>Salestool/communication"><i class="fa fa-list"></i> SET COMMUNICATION</a></li><?php }?>
								
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','144')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li><a href="<?php echo page_url;?>Salestool/salestoollibrary"><i class="fa fa-list"></i> LIBRARY</a></li><?php }?>
									
									</ul>
									</li>
									
							
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','145')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							    	<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);" ><i class="fa fa-list" aria-hidden="true"></i>&nbsp; SAMPLE TESTING MANAGEMENT</a>
									<ul class="dropdown-menu">
								
									<li ><a href="<?php echo page_url;?>Sampletesting/sampletestscripts"><i class="fa fa-list"></i> ADD FIXED SCRIPTS</a></li>
								
									</ul>
									</li>
										<?php }
										?>
											<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);" style="color:red;"><i class="fa fa-list" aria-hidden="true"></i>&nbsp; STORE</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','97')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li style="color:red;"><a href="<?php echo page_url;?>Store/add_rack_location"><i class="fa fa-list"></i> Rack Location</a></li>
									<?php }?>
									
								
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','197')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li style="color:red;"><a href="<?php echo page_url;?>Store/machineparts"><i class="fa fa-list"></i> Machine Parts Management</a></li><?php }?>
								
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','198')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li ><a href="<?php echo page_url;?>Store/bom_materials"><i class="fa fa-list"></i> BOM Material</a></li><?php }?>
								
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','199')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Store/add_general_items"><i class="fa fa-list"></i>House Keeping Items</a></li>
									<?php
									}
									?>
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','200')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Store/ageing"><i class="fa fa-list"></i>Ageing Factor</a></li>
									<?php
									}
									?>
									
									
									
									</ul>
									</li>
									
								
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','149')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li>
									<a tabindex="-1" href="<?php echo page_url;?>Master/Units"><i class="fa fa-list" aria-hidden="true"></i>&nbsp; UNITS</a>
								
									</li>
								<?php
									}
									?>
									
										
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','212')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li>
									<a tabindex="-1" href="<?php echo page_url;?>Master/Saleszone"><i class="fa fa-list" aria-hidden="true"></i>&nbsp; SALES ZONE</a>
								
									</li>
									<?php
									}
									?>
									
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','218')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
									<li>
									<a tabindex="-1" href="<?php echo page_url;?>User/edit_home_image"><i class="fa fa-list" aria-hidden="true"></i>&nbsp; LOGIN BACKGROUND IMAGE UPDATE</a>
								
									</li>
									<?php
									}
									?>
									
                                </ul>
                            </li>
							<!-- MASTER END HERE-->
							<?php }}?>
							 <?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','2')->get();
if($module->num_rows()>0)
{
	foreach($module->result() as $moddata);
if($moddata->access=='1')
{
?>
						
							<!--- FORM & DASHBOARD STARTED HERE-->
							
							<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>FORM & DASHBOARD</span> </a>
                                <ul class="submenu">
								
								<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> FORM</a>
									<ul class="dropdown-menu">
									    	<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','151')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Movement_tracking/staff_movement"><i class="fa fa-list" aria-hidden="true"></i> STAFF MOVEMENT/OD</a>
									</li>
									<?php
									}
									?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','152')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Store/imported_stock_form"><i class="fa fa-list" aria-hidden="true"></i> IMPORTED ITEM STOCK FORM</a>
									</li>
									<?php
									}
									?>	
									    
								 <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','150')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Movement_tracking/movement_out"><i class="fa fa-list" aria-hidden="true"></i> MOVEMENT IN OUT FORM</a>
									</li>
									<?php
									}
									?>
									
									
								<?php 
									$query = $this->db->select('id, moduleid, submodule')->from('submodule')->where('moduleid','2')->where('status','1')->get();
								
									foreach($query->result() as $formmodule){
									
									$q = $this->db->select('id,form_title, status')->from('dynamic_forms')->where('status','1')->where('form_title',$formmodule->submodule)->where('form_running_status','0')->get();
									foreach($q->result() as $row){
									?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid',$formmodule->id)->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>"><i class="fa fa-list"></i> <?php echo $row->form_title;?></a></li>
									<?php 
									}
									}
									
									}?>
									
									<?php 
									$q = $this->db->select('id, dashboard_title, status')->from('dynamic_forms')->where('user_id',$user_id)->where('status','1')->where('form_running_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
									?>
									<li><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>"><i class="fa fa-list"></i> <?php echo $row->dashboard_title;?></a></li>
										<?php }
										}
									?>
									
									<?php 
									$q = $this->db->select('b.id,a.form_id, a.user_id, b.dashboard_title')->from('dynamic_form_dashboard_access a')->join('dynamic_forms b','a.form_id=b.id','left')->where('a.user_id',$user_id)->where('b.status','1')->where('b.form_running_status','0')->get();
									if($q->num_rows()>0){
										foreach($q->result() as $row){
									?>
									<li><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>"><i class="fa fa-list"></i> <?php echo $row->dashboard_title;?></a></li>
										<?php }
										}
									?>
									</ul>
									</li>
								
								<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> DELEGATION FORM & DASHBOARD</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','24')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li>
<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_task"><i class="fa fa-list" aria-hidden="true"></i> Delegation Form </a>
									</li><?php }?>
<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','25')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
<li>
<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Delegation Dashboard </a>
									</li><?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','26')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
<li>
<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegated_task_history"><i class="fa fa-list" aria-hidden="true"></i> Delegated Task History </a>
									</li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','27')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
<li>
<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegated_task"><i class="fa fa-list" aria-hidden="true"></i> Task Delegated to You </a>
									</li><?php }?>
									
									</ul>
									</li>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> CHECKLIST</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','28')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/view_your_checklist"><i class="fa fa-list" aria-hidden="true"></i> View Your Checklist/Report</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','29')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									
									<li><a href="<?php echo page_url;?>Checklist/user_monthly_report"><i class="fa fa-list" aria-hidden="true"></i> View Checklist Report</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','30')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Checklist/checklist_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Auditor Checklist Dashboard</a></li>
									<?php }?>
									</ul>
									</li>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> CONVEYANCE FORM & DASHBOARD</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','31')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/conveyance_voucher"><i class="fa fa-list" aria-hidden="true"></i> Local Conveyance</a></li><?php }?>
								
								
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','32')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/sale_service_conveyance_voucher"><i class="fa fa-list" aria-hidden="true"></i> Tour Conveyance</a></li><?php }?>
									
									</ul>
									</li>
								
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> SALES</a>
									<ul class="dropdown-menu">
									    
									    	<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','72')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
									<a tabindex="-1" href="<?php echo page_url;?>Salestool"><i class="fa fa-list" aria-hidden="true"></i> SALES TOOL</a>
									</li>
									<?php
									}
									?>
									
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','101')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Salestool/callhistory"><i class="fa fa-list" aria-hidden="true"></i> SALES TOOL HISTORY</a>
									</li>
									<?php
									}
									?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','154')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Sales/visit_schedule"><i class="fa fa-list" aria-hidden="true"></i> VISIT SCHEDULE FORM</a>
									</li>
									<?php
									}
									?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','155')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Sales/sales_daily_update"><i class="fa fa-list" aria-hidden="true"></i> DAILY UPDATE FORM</a>
									</li>
									<?php
									}
									?>
									    
									    </ul>
									    
									    </li>
								
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> STORE</a>
									<ul class="dropdown-menu">
									    
									    	<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','207')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Store/indent_form"><i class="fa fa-list" aria-hidden="true"></i> Create Indent</a></li><?php }?>
									    
									    
									    </ul>
									    </li>
									
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> SAMPLE TESTING</a>
									<ul class="dropdown-menu">
							
							
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','146')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Sampletesting"><i class="fa fa-list" aria-hidden="true"></i> CREATE SAMPLE REQUEST</a>
									</li>
									<?php
									}
									?>
									
							
                                </ul>
                                </li>
                                
                               
                                
                                 <!-- SERVICE FORM-->
                                	<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> SERVICE</a>
									<ul class="dropdown-menu">
							
							        <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','153')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Sales/visit_form"><i class="fa fa-list" aria-hidden="true"></i>SERVICE ENGINEER VISIT FORM</a>
									</li>
									<?php
									}
									?>
							
                                </ul>
                                </li>
                                <!-- SERVICE FORM-->
                                
                                
                                
								</ul>
								</li>
								
								
							<!--- FORM & DASHBOARD END HERE-->
<?php }}?>							
							
						<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','3')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 	
							<!--- REPORTING STARTED HERE-->
							
							<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>REPORTING</span> </a>
                                <ul class="submenu">
								<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list" aria-hidden="true"></i> PRODUCTION REPORT</a>
									<ul class="dropdown-menu">
									
									
										<?php
										$reslongre=$this->db->select('id,production_flow')->from('production_flow')->where('longreport','1')->order_by('sortorder','ASC')->get();
										if($reslongre->num_rows()>0)
										{
										foreach($reslongre->result() as $reslongre1)
										{
											$term="FMS ".strtoupper($reslongre1->production_flow)." REPORTING";
										$qry = $this->db->select('a.id')->from('module_capablity a')->join('submodule b','a.submoduleid=b.id')->where('a.role_id',$user_id)->where('a.moduleid','3')->where('b.submodule',$term)->where('a.submodule_access','1')->get();
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
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','45')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>FMS/view_planned_orders"><i class="fa fa-list"></i> PLANNED ORDERS</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','46')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/planned_actual"><i class="fa fa-list"></i> PLANNED TO ACTUAL</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','47')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/pendingorders"><i class="fa fa-list"></i> PENDING ORDERS REPORT</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','48')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/readyforpacking"><i class="fa fa-list"></i> MACHINES FOR PACKING</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','49')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/completedorders"><i class="fa fa-list"></i> READY ORDERS COMPLETE REPORT</a></li><?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','178')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/completedorders"><i class="fa fa-list"></i> VIEW YOUR READY ORDERS</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','50')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/dispatchfortommorow"><i class="fa fa-list"></i> DISPATCH FOR TOMMOROW</a></li><?php }?>
									
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','51')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/servicerequest"><i class="fa fa-list"></i> SERVICE REQUEST</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','52')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/previousorders"><i class="fa fa-list"></i> PREVIOUS ORDER HISTORY</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','53')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/lotorderlist"><i class="fa fa-list"></i> LOT ORDER LIST</a></li>
									<?php }?>
									
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','105')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/machinecostprice"><i class="fa fa-list"></i> WIP PRODUCTION COST</a></li>
									<?php }?>
									
									</ul>
									</li>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> MIS REPORT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','54')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Reporting/misscore"><i class="fa fa-list"></i> Overall MIS </a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','55')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/fmsmis"><i class="fa fa-list"></i> Fms MIS </a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','56')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>FMS/nonfmsmis"><i class="fa fa-list"></i> Non Fms MIS </a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','58')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Delegation/audit_dashbaord"><i class="fa fa-list"></i>Auditor Delegation Report</a></li>	
									<?php }?>
									
									</ul>
									</li>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> STORE REPORT</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','103')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Store/machine_part_data_with_picture"><i class="fa fa-list"></i> Machine parts with Picture </a></li><?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','181')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/pendingindend"><i class="fa fa-list"></i> Pending Indent(s) </a></li><?php }?>
									
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','182')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/pendingpr"><i class="fa fa-list"></i> Pending PR Request(s) </a></li><?php }?>
    									
    									
    									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','183')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/pendingpoforapproval"><i class="fa fa-list"></i>PENDING PO FOR APPROVAL REQUEST</a></li>
    								<?php
									}
									?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','184')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/gateentry"><i class="fa fa-list"></i>PENDING GATE ENTRY</a></li>
    								<?php
									}
									?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','185')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/pendinggateentrymrn"><i class="fa fa-list"></i> Pending MRN Request(s) </a></li><?php }?>
    										
    										
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','186')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/mrnqc"><i class="fa fa-list"></i>MRN QC REQUEST(s) </a></li><?php } ?>
    									
    									
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','208')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/issuegeneralitems"><i class="fa fa-list"></i>ISSUE GENERAL ITEMS </a></li>
    							    <?php
									}
									?>
    									
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','187')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/qcdonereport"><i class="fa fa-list"></i>COMPLETED MRN QC</a></li>
    									
    							<?php
									}
									?>
    									
    										
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','188')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/autopr"><i class="fa fa-list"></i>AUTO PR REQUEST </a></li><?php } ?>
    									
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','189')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/rejecteditemreq"><i class="fa fa-list"></i>QC REJECTED ITEMS</a></li>
    								<?php
									}
									?>
    									
    									
    									
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','190')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/debitnote"><i class="fa fa-list"></i>DEBIT NOTES</a></li>
    								<?php
									}
									?>
    								
    								
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','191')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
							<li><a href="<?php echo page_url;?>Reporting/rejectionchallanreq"><i class="fa fa-list"></i>REJECTION CHALLANS</a></li>
    							<?php
									}
									?>
    										
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','192')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/closedpo"><i class="fa fa-list"></i>CLOSED PO(s)</a></li><?php } ?>
    									
    										
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','192')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/indent_vs_pr_report"><i class="fa fa-list"></i>INDENT VS PR REPORT</a></li>
    									<?php
									}
									?>
									
										
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','193')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Reporting/pr_vs_po_report"><i class="fa fa-list"></i>PR VS PO REPORT</a></li>
    									<?php
									}
									?>
									
										
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','194')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    									<li><a href="<?php echo page_url;?>Challan/nrgp_challan"><i class="fa fa-list"></i>NRGP Challan</a></li>
    									<?php
									}
									?>
									
										
    										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','195')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
    										<li><a href="<?php echo page_url;?>Challan/nrgp_challan_dashboard"><i class="fa fa-list"></i>NRGP Challan Dashboard</a></li>
									<?php
									}
									?>
									</ul>
									</li>
									
									<!-- MDO DASHBOARD-->
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> MDO</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','148')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Store/imported_items"><i class="fa fa-list"></i> Imported Items Dashboard</a></li><?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','175')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Store/issued_item_dashboard"><i class="fa fa-list"></i> Issued Items Dashboard</a></li><?php }?>
									
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','176')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Store/received_item_dashboard"><i class="fa fa-list"></i> Received Items Dashboard</a></li><?php }?>
                                    
                                    <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','156')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Movement_tracking/movement_out_dashboard"><i class="fa fa-list"></i> Material Movement In Out Dashboard</a></li><?php }?>	
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','157')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Movement_tracking/staff_movement_dashboard"><i class="fa fa-list"></i> Staff Movement Dashboard</a></li><?php }?>	
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','158')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Store/imported_stock_request"><i class="fa fa-list"></i> Imported Item Request</a></li><?php }?>	
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','159')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Movement_tracking/billing_to_booking"><i class="fa fa-list"></i> Booking to Billing </a></li><?php }?>	
									
									</ul>
									</li>
									<!-- MDO DASHBOARD-->
									
									<!-- SERVICE DASHBOARD-->
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> SERVICE</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','160')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>/Sales/engineer_visit_dashboard"><i class="fa fa-list"></i> Hod Dashboard of all Engineers</a></li><?php }?>
                                    
                                    <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','161')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/view_your_dashboard"><i class="fa fa-list"></i> View Your Dashboard</a></li><?php }?>	
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','162')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/hod_engineervisit_dashboard"><i class="fa fa-list"></i> FOC Dashboard</a></li><?php }?>	
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','163')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/payment_followup_dashboard"><i class="fa fa-list"></i> Payment Follow-up Dashboard</a></li><?php }?>	
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','164')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/technical_support_dashboard"><i class="fa fa-list"></i> Technical Support Dashboard </a></li><?php }?>	
									
									</ul>
									</li>
									<!-- SERVICE DASHBOARD-->
									
										<!-- CONVEYANCE DASHBOARD-->
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> CONVEYANCE</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','33')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/local_conveyance_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Local Conveyance Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','34')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/local_conveyance_hod_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Local Conveyance HOD Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','35')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/local_conveyance_account_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Local Conveyance Account/HR Dashboard</a></li><?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','37')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>

									<li><a href="<?php echo page_url;?>Sales/sale_service_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Tour Conveyance Dashboard</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','38')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/sale_service_conveyance_hod_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Tour Conveyance HOD Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','39')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Sales/sale_service_conveyance_account_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Tour Conveyance Account/HR Dashboard</a></li><?php }?>	
									
									</ul>
									</li>
									<!-- CONVEYANCE DASHBOARD-->
									
										<!-- SALES DASHBOARD-->
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> SALES</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','165')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/visit_schedule_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Sales Visit Dashboard</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','166')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sales/sales_daily_update_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Daily Update Dashboard</a></li>
									<?php }?>
									
									</ul>
									</li>
									<!-- SALES DASHBOARD-->
									
									<!-- SAMPLE TESTING DASHBOARD-->
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> SAMPLE TESTING</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','147')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Sampletesting/sampletestrequests"><i class="fa fa-list" aria-hidden="true"></i> Sample Testing Dashboard</a></li>
									<?php }?>
									
									
									</ul>
									</li>
									<!-- SAMPLE TESTING DASHBOARD-->
									
										<!-- EMPLOYEE ATTENDANCE DASHBOARD-->
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> EMPLOYEE ATTENDANCE</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','168')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Master/User_management/attendance_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Employee Attendance Dashboard</a></li>
									<?php }?>
									
									
									</ul>
									</li>
									<!-- EMPLOYEE ATTENDANCE DASHBOARD-->
									
										<!-- HR MODULE DASHBOARD-->
										<li class="dropdown-submenu">
									<a tabindex="-1" href="javascript:void(0);"><i class="fa fa-list"></i> HR LEAVE APPLICATION</a>
									<ul class="dropdown-menu">
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','169')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                    <li><a href="<?php echo page_url;?>Hr/leave_application_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Leave Application Dashboard</a></li>
									<?php }?>
									
									
									</ul>
									</li>
									<!-- HR MODULE DASHBOARD-->
								
								</ul>
								</li>
							
							<!--- REPORTING END HERE-->
<?php }}?>


<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','4')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 	

<!-- IT ASSETS START HERE-->
							
							<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>IT ASSETS</span> </a>
                                <ul class="submenu">
                                  	<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','60')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Tech_support/asset_type"><i class="fa fa-list" aria-hidden="true"></i> ASSET TYPE</a></li>
								<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','61')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>IT_Assets/brand"><i class="fa fa-list"></i> ASSET BRANDS</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','62')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>IT_Assets/microsoft_office"><i class="fa fa-list"></i> MICROSOFT OFFICE</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','63')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>IT_Assets/ms_office_license"><i class="fa fa-list"></i> MICROSOFT OFFICE LICENCE TYPE</a></li>
								<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','64')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>IT_Assets/window_license_type"><i class="fa fa-list"></i> WINDOWS LICENCE TYPE</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','65')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>IT_Assets/operating_system"><i class="fa fa-list"></i> OPERATING SYSTEM</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','66')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Tech_support/location"><i class="fa fa-list"></i> IT ASSET LOCATIONS</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','67')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Tech_support/items"><i class="fa fa-list"></i> ADD NEW IT ASSET</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','68')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>IT_Assets"><i class="fa fa-list"></i> IT ASSETS MANAGEMENT</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','69')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>IT_Assets/expiry_warranty"><i class="fa fa-list"></i> ASSET WARRANTY EXPIRE MANAGEMENT</a></li>
								<?php }?>
								</ul>
								</li>
							
							<!-- IT ASSETS END HERE-->
<?php }}?>

<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','5')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 	

<!-- Master START HERE-->
							<?php if($user_role=='1' || $user_role=='68'){?>
							<li class="has-submenu">
                                <a href="<?php echo page_url;?>Form/master_index"><i class="fa fa-list"></i><span>MASTER INDEX</span> </a></li>
							<?php }else{?>
							<li class="has-submenu">
                                <a href="<?php echo page_url;?>Form/view_master_index"><i class="fa fa-list"></i><span>MASTER INDEX</span> </a></li>
							<?php }?>
							<!-- Master END HERE-->
<?php }}?>


<?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','6')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 	

<!-- HELP TICKET START HERE-->
							
							<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>HELP TICKET</span> </a>
                                <ul class="submenu">
                                  	<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','93')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Form/view/18"><i class="fa fa-list" aria-hidden="true"></i> TICKET - ACCOUNTS</a></li>
								<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','94')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Form/view/17"><i class="fa fa-list"></i> TICKET - SERVICE</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','95')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Form/view/1"><i class="fa fa-list"></i> TICKET - EA</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','96')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Form/view/2"><i class="fa fa-list"></i> TICKET FOR IT</a></li><?php }?>
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','100')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Form/view/12"><i class="fa fa-list"></i> TICKET FOR MAINTENANCE</a></li><?php }?>
								
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','90')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Form/view/14"><i class="fa fa-list"></i> TICKET - GM (SIR)</a></li><?php }?>
								</ul>
								
								</li>
							
							<!-- HELP TICKET END HERE-->
<?php }}?>

	<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>LEAVE APPLICATION</span> </a>
                                <ul class="submenu">
                                  
								<li><a href="<?php echo page_url;?>Hr/leave_application"><i class="fa fa-list" aria-hidden="true"></i> Leave Application</a></li>
								
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','171')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li class="">
								<a href="<?php echo page_url;?>Hr/hod_leave_dashboard"><i class="fa fa-list" aria-hidden="true"></i> Leave Application HOD dashboard</a>
									</li>
									<?php
									}
									?>
							
								</ul>
								
								</li>
								
			
								
								<?php 
								$user_id =$this->session->userdata['logged_in']['user_id'];
								$query = $this->db->select('team_name, employee_id, id')->from('presto_hod')->where('employee_id',$user_id)->get();
								if($query->num_rows()>0){
								?>
								<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>EMPLOYEE PLANNING</span> </a>
                                <ul class="submenu">
                                  
								<li><a href="<?php echo page_url;?>HOD_Team/HOD_panel"><i class="fa fa-list" aria-hidden="true"></i>Dashboard</a></li>
							
								</ul>
								
								</li>
                                <?php }?>
                                
                                <?php
$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','8')->get();
if($module->num_rows()>0)
{
foreach($module->result() as $moddata);
if($moddata->access=='1')
{

?> 	
								
								<li class="has-submenu">
                                <a href="#"><i class="fa fa-list"></i><span>INDIVIDUAL REPORT</span> </a>
                                <ul class="submenu">
                                	<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','8')->where('submoduleid','219')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
								<li><a href="<?php echo page_url;?>Dashboard/user_dashboard"><i class="fa fa-list" aria-hidden="true"></i>View Your Dashboard</a></li>
								<?php }?>
								
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','8')->where('submoduleid','220')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
							<li><a href="<?php echo page_url;?>Dashboard/hod_consolidate_dashboard"><i class="fa fa-list" aria-hidden="true"></i>View HOD Dashboard</a></li>
							<?php }?>
								</ul>
								
								</li>
                               
                           <?php }}?>     
                                


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
									   $mmer=$CI->Fms_model->checkifmsmerge($proc->flow_id);
									  
									   
									  if($mmer==0)
									  {
									  $flowtottask=$CI->Fms_model->menunotifications($proc->flow_id,$proc->dependency);
									  }else{
										  
										   $flowtottask=$CI->Fms_model->menunotificationsformergeflow($proc->flow_id,$proc->production_flow_id);
									  }
									
									  
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


                   </ul>
                        <!-- End navigation menu  -->
                    </div>
                </div>
            </div>
        </header>