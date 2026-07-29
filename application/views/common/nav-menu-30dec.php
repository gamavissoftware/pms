<?php 
$total = 0;
$businesslocation =$this->session->userdata['logged_in']['business_location'];
if($businesslocation==2){
$user_id = $this->session->userdata['logged_in']['user_id'];
$st = date('Y-m-d',strtotime('-30 days'));
$et = date('Y-m-d');
$dfid = "ALL";

$CI =& get_instance();
$CI->load->model('MIS_model','mis_model');
$totalassigned = $CI->mis_model->allassignedtask($user_id,$et,$dfid);
$totaldonetask =  $CI->mis_model->totalassignedworkdone($user_id,$et,$dfid);
$diff = $totalassigned-$totaldonetask;
$per2=$CI->mis_model->get_percentage($diff,$totalassigned);

$totaldonecount = $CI->mis_model->totaldonetaskwithdate($user_id, $st, $et,$dfid);
$totaldoneontime = $CI->mis_model->totaldonetaskwithdateontime($user_id, $st, $et,$dfid);
$diff = $totaldonecount-$totaldoneontime;
$per3=$CI->mis_model->get_percentage($diff,$totaldonecount);

$total = ($per2+$per3)/2;

}else
{
	$total = 0;
}
?>
<!-- <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" /> -->
<style type="text/css">
	.list-notificacao{
  min-width: 400px;
  background: #ffffff;
}

.list-notificacao li{
   border-bottom : 1px #d8d8d8 solid;
   text-align    : justify;
   padding       : 5px 10px 5px 10px;
   cursor: pointer;
   font-size: 12px;
}

.list-notificacao li:hover{
background: #f1eeee;
}

.list-notificacao li:hover .exclusaoNotificacao{
/*display: block;*/
}

.list-notificacao li  p{
 color: black;
/* width: 305px;*/
}

.list-notificacao li .exclusaoNotificacao{
    width: 25px;
    min-height: 40px;
    position: absolute;
    right: 0;
/*    display: none;*/
}

.list-notificacao .media img{
    width: 40px;
    height: 40px;
    float:left;
    margin-right: 10px;
}

.badgeAlert {
    display: inline-block;
    min-width: 10px;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    vertical-align: baseline;
    white-space: nowrap;
    text-align: center;
    background-color: #d9534f;
    border-radius: 10px;
    position: absolute;
    margin-top: -10px;
    margin-left: 10px
}

  .quotecss{



    color: #fff;



    text-align: center;



    padding-top: 26px;



    font-size: 13px;



    font-weight:bold;



  }

.badge1 {
    position: relative;
  }
.badge1[data-badge]:after {
content: attr(data-badge);
position: absolute;
top: -10px;
right: -10px;
font-size: 12px;
font-weight: bold;
background: black;
color: white;
width: 18px;
height: 18px;
text-align: center;
border-radius: 50%;
box-shadow: 0 0 1px #333;
}
.quotecss {
color: #fff;
text-align: center;
padding-top: 26px;
font-size: 13px;
font-weight: bold;
}
#topnav .topbar-main {
/* background-color: #2986CE; */
/* background-image: url('https://prestomitr.com/assets/images/pt.jpg'); */
background-color: whitesmoke;
position: fixed;
width: 100%;
z-index: 50;
height: 54px;
box-shadow: 1px 1px 5px lightgray;
}
@media (max-width:576px) {
#topnav .topbar-main {
/* background-color: #2986CE; */
/* background-image: url('https://prestomitr.com/assets/images/pt.jpg'); */
background-color: whitesmoke;
position: absolute;
width: 100%;
z-index: 50;
height: 54px;
box-shadow: 1px 1px 5px lightgray;
}

}
.usernamecss {
margin-top: 14px;
}
@media only screen and (max-width: 600px) {
.usernamecss {
margin-top: 0px;
}
}
.secondul {
max-height: 450px;
overflow-y: auto;
}
.secondulss {
max-height: 200px;
overflow-y: auto;
}

@media (max-width:576px) {
#mobilemenuwrap ul li ul {
border: 1px solid #d3d3d366;
box-shadow: none;
height: 100%;
overflow-y: unset;
}

.dash {
border-bottom: 1px solid whitesmoke;
}

.logo a img {
width: 100px;
margin-left: 10px;
}

.dash i {
font-size: 20px !important;
text-align: center;
}
}
</style>
<?php
/*Dynachem Menu Items*/
$businesslocation =$this->session->userdata['logged_in']['business_location'];
if($businesslocation==1){
$CI = &get_instance();
$CI->load->model('Fms_model');
$VI = &get_instance();
$VI->load->model('Fms_mismodel');
$DI = &get_instance();
$DI->load->model('Lms_model');
$currentweekdates = $VI->Fms_mismodel->currentweekdates();
//$currentweekdates=$VI->Fms_mismodel->getcurrentweekalldates();
$ststst = base64_encode($currentweekdates[0]);
$endddd = base64_encode($currentweekdates[1]);
$user_id = $this->session->userdata['logged_in']['user_id'];
$profile_image = $this->session->userdata['logged_in']['profile_image'];
$user_role = $this->session->userdata['logged_in']['role'];
$first_name = $this->session->userdata['logged_in']['user_name'];
$last_name = $this->session->userdata['logged_in']['last_name'];
$department_id = $this->session->userdata['logged_in']['department_id'];
$q = $this->db->select('profile_image')->from('system_users')->where('user_id', $user_id)->get();
if ($q->num_rows() > 0) {
	foreach ($q->result() as $row);
	$profilepic = $row->profile_image;
} else {
	$profilepic = "";
}
?>
<div class="leftmenu" id="mobilemenu">
	<div class="logo" style="background-color: whitesmoke;"><a href="#"><img src="<?php echo assets_url; ?>images/logo_mitr.png" alt="Logo"></a></div>
	<div id="mobilemenuwrap">
		<ul>
			<li>

				<div class="dash">

					<a <?php if ($this->uri->segment(1) == 'Dashboard') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Dashboard"><i class="fa fa-dashboard"></i> DASHBOARD</a>
				</div>
			</li>

			<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '1')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array("'1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '31', '32', '23','96','97','119','176'");
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where_in('submoduleid', $submoduleid,false)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
						
						
			?>
						<li>

							<div class="mast">

								<a <?php if ($this->uri->segment(1) == 'Master') { ?>class="active" <?php } else if ($this->uri->segment(1) == 'Form') { ?>class="active" <?php } else if ($this->uri->segment(1) == 'FMS') { ?>class="active" <?php } else if ($this->uri->segment(1) == 'Store') { ?>class="active" <?php } else if ($this->uri->segment(1) == 'User') { ?>class="active" <?php } else if ($this->uri->segment(1) == 'Hr') { ?>class="active" <?php } else if ($this->uri->segment(1) == 'Hr') { ?>class="active" <?php } ?> href="#"><i class="fa fa-cogs"></i> MASTER <span class="add-extra"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

							</div>
							<ul>
								<li><a href="<?php echo page_url; ?>Dashboard/user_report">Master Management</a></li>
								<li><a href="<?php echo page_url; ?>Dashboard/sales_master">Sales Master</a></li>
								
								
							</ul>
						</li>

					<?php } } } ?>


					

					<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '9')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array('77', '78');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '9')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
			?>
						<li>
							<div class="dash">
								<a href="<?php echo page_url; ?>Dashboard/sales_dash_board"><i class="fa fa-bar-chart"></i> SALES DASHBOARD</a>
							</div>
						</li>

					<?php } } } ?>


						<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '11')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					
			?>
						<li>
							<div class="dash">
								<a href="<?php echo page_url; ?>Dashboard/sales_stats"><i class="fa fa-line-chart"></i> SALES REPORTS</a>
							</div>
						</li>

					<?php } }  ?>



						<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '8')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array('70', '71','73','95');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '8')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
			?>
						<li>
							<div class="dash">
								<a <?php if ($this->uri->segment(1) == 'Customer') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Customer/quotation_customer_dashboard"><i class="fa fa-file" aria-hidden="true"></i> QUOTES<BR/>&<BR/>ORDERS</a>
							</div>
						</li>

					<?php } } } ?>


						<!-- <li>
							<div class="dash">
								<a href="<?php echo page_url; ?>Inventory"><i class="fa fa-list"></i> STORE</a>
							</div>
						</li> -->

						<li style="display:none;">
							<div class="dash">

								<a <?php if ($this->uri->segment(1) == 'Checklist') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Checklist/checklist_master"><i class="fa fa-list-ul" aria-hidden="true"></i> CHECKLIST</a>
							</div>
						</li>

						<!-- <li>
							<div class="dash">
								<a <?php if ($this->uri->segment(1) == 'Reminder') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Reminder/taskdashboard"><i class="fa fa-tasks" aria-hidden="true"></i> TASK</a>
							</div>
						</li> -->

					

						<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '7')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array('18', '19','20','21','22','23','40','41','42');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '3')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
			?>


						<li>
							<div class="dash">
								<a <?php if ($this->uri->segment(1) == 'Mom') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Mom/momdashboard"><i class="fa fa-file" aria-hidden="true"></i> MOM</a>
							</div>
						</li>

					<?php } }  }?>


<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '2')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array('13', '14','15','16','17','25');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '7')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
			?>

			

			<li>

				<div class="dash">

					<a <?php if ($this->uri->segment(1) == 'Delegation') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Delegation/delegationdashboard"><i class="fa fa-dashboard"></i> DELEGATION</a>
				</div>
			</li>

			<?php } }  }?>
			
				<li style="display:none">

				<div class="dash">

					<a <?php if ($this->uri->segment(1) == 'Hr') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Hr/leave_attendance_dashboard"><i class="fa fa-dashboard"></i>ATTENDANCE & LEAVE MANAGEMENT</a>
				</div>
			</li>

		</ul>
		<!-- <li>

	<div class="dash">

		<a <?php if ($this->uri->segment(1) == 'Delegation') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Delegation/delegationdashboard"><i class="fa fa-dashboard"></i> DELEGATION</a>
	</div>
</li>
</ul>
</li>
</ul> -->
	</div>


	<a href="javascript:void(0);" class="icon colexpicon" onclick="myFunction()"><i class="fa fa-bars"></i></a>



</div>
<div id="profileimg" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<form method="post" action="<?php echo page_url; ?>User/update_profile/<?php echo $user_id; ?>" enctype="multipart/form-data">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">CHANGE YOUR PROFILE IMAGE</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12"><input type="file" class="form-control" name="profile" value="" placeholder="upload" required></div>
					</div>
				</div>
				<div class="modal-footer"><button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button><input type="submit" id="save" class="btn btn-info" value="Save"> </div>
			</div>
		</div>
	</form>
</div><!-- /.modal -->
<header id="topnav">
	<div class="topbar-main">
		<div class="container-fluid">
			<div class="menu-extras">
				<div class="quotecss hidden-xs" style="display: inline-block;padding-top: 16px;padding-left: 100px;">
					 


					</span>
				</div>
				<ul class="nav navbar-nav navbar-right pull-right">


<li class="dropdown user-box">
						<div>
							<ul class="list-inline m-b-0">
								<li style="margin-left: -8px;display:none;">

									<div style="    display: flex;">

										<i class="fa fa-clock-o" aria-hidden="true" style="margin-right: 6px; margin-top: 6px; font-size: 15px; color: #188ae2;"></i>

										<p id="demo" style="font-weight:600; margin-top: 4px;">

										</p>

									</div>

									<!-- <script>
										var myVar = setInterval(myTimer, 1000);

										function myTimer() {

											var d = new Date();

											document.getElementById("demo").innerHTML = d.toLocaleTimeString();

										}
									</script> -->

								</li>
								<li><a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true" style="font-size: 20px;color: #0000009e;line-height: 56px; margin-top: -6px;"><i class="fa fa-angle-down" aria-hidden="true"></i>
										<ul class="dropdown-menu">

											<?php
											$user_id = $this->session->userdata['logged_in']['user_id'];
											?>
											<!-- <li><a href="<?php echo page_url; ?>Master/User_management/mark_your_attendance"><i class="ti-settings m-r-5"></i> Mark Your Attendance</a></li> -->
											<li><a href="<?php echo page_url; ?>Master/User_management/change_password"><i class="ti-settings m-r-5"></i> Change Password</a></li>
											<!-- <li><a href="<?php echo page_url; ?>User/edit_registration"><i class="ti-settings m-r-5"></i> Edit Company Profile</a></li> -->
											<li><a href="<?php echo page_url; ?>User/signout"><i class="ti-power-off m-r-5"></i> Logout</a></li>
											<?php
											if ($this->uri->segment(1) == 'Dashboard') {
											?>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#profileimg"><i class="ti-upload m-r-5"></i>Change Your Profile Picture</a></li>
											<?php } ?>
											<?php
											if ($this->uri->segment(1) == 'Dashboard') {
											?>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#con-close-modal"><i class="ti-upload m-r-5"></i>Add Profile Status</a></li>
											<?php } ?>
										</ul>
									</a>
								</li>
							</ul>
						</div>
					</li>
					<li class="usernamecss"><span style="color:#0000009e ;"><?php echo $first_name; ?> <?php echo $last_name; ?></span></li>
					<li class="dropdown user-box">
						<a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true">
							<?php if ($profilepic) { ?>
								<img src="<?php echo user_profile; ?><?php echo $profilepic; ?>" alt="user-img" class="img-circle user-img"><?php } else { ?><img src="https://prestomitr.com/image_bank/users/1534487737.jpg" alt="user-img" class="img-circle user-img"> <?php } ?><div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div></a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>


<?php }else{?>
	<!--Shubham Pack Menu Start from here-->

<?php 
$CI = &get_instance();
$CI->load->model('Fms_model');
$VI = &get_instance();
$VI->load->model('Fms_mismodel');
$DI = &get_instance();
$DI->load->model('Lms_model');
$currentweekdates = $VI->Fms_mismodel->currentweekdates();
//$currentweekdates=$VI->Fms_mismodel->getcurrentweekalldates();
$ststst = base64_encode($currentweekdates[0]);
$endddd = base64_encode($currentweekdates[1]);
$user_id = $this->session->userdata['logged_in']['user_id'];
$profile_image = $this->session->userdata['logged_in']['profile_image'];
$user_role = $this->session->userdata['logged_in']['role'];
$first_name = $this->session->userdata['logged_in']['user_name'];
$last_name = $this->session->userdata['logged_in']['last_name'];
$department_id = $this->session->userdata['logged_in']['department_id'];
$q = $this->db->select('profile_image')->from('system_users')->where('user_id', $user_id)->get();
if ($q->num_rows() > 0) {
	foreach ($q->result() as $row);
	$profilepic = $row->profile_image;
} else {
	$profilepic = "";
}
?>

<div class="leftmenu" id="mobilemenu">
	<div class="logo" style="background-color: whitesmoke;"><a href="#"><img src="<?php echo assets_url; ?>images/shubhampack.png" alt="Logo"></a></div>
	<div id="mobilemenuwrap">
		<ul>
			<li>

				<div class="dash">

					<a <?php if ($this->uri->segment(1) == 'Dashboard') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Dashboard"><i class="fa fa-dashboard"></i> DASHBOARD</a>
				</div>
			</li>

			<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '1')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {
					
			?>
				<li>
				<div class="mast">
					<a class="active" href="#"><i class="fa fa-cogs"></i> MASTER <span class="add-extra"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

							</div>
							<ul>
					<?php 
					$submoduleid = array('1', '2','3','4','5','6','24','26');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
								?>
								<li><a href="<?php echo page_url; ?>Dashboard/user_report">User Management</a></li>
							<?php }?>
							<?php 
					$submoduleid = array('7', '8','9','10','11','12');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
								?>
								<li><a href="<?php echo page_url; ?>Dashboard/task_master_dashboard">Task Management</a></li>
							<?php }?>

							<?php 
					$submoduleid = array('31', '32','33','34','35');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
								?>
								<li><a href="<?php echo page_url; ?>Dashboard/Sales_master">Sales Management</a></li>
							<?php }?>
								
							</ul>
						</li>
					<?php }}?>

				<?php
				$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','15')->get();
				if($module->num_rows()>0)
				{
				foreach($module->result() as $moddata);
				if($moddata->access=='1')
				{
				?>
				<li>
				<div class="dash">
				<a <?php if ($this->uri->segment(1) == 'Mom') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Dashboard/opportunity_dashboard"><i class="fa fa-file" aria-hidden="true"></i>OPPORTUNITY & QUOTATION</a>
				</div>
				</li>
				<?php } } ?>

<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '3')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array('18', '19','20','21','22','23','40','41','42');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '3')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
			?>
					<li>
							<div class="dash">
								<a <?php if ($this->uri->segment(1) == 'Mom') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Mom/momdashboard"><i class="fa fa-file" aria-hidden="true"></i> MOM/IOM</a>
							</div>
						</li>
				<?php }}}?>

			<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '2')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array('13', '14','15','16','17','25');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '2')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
			?>
			<li>

				<div class="dash">

					<a <?php if ($this->uri->segment(1) == 'Delegation') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Delegation/delegationdashboard"><i class="fa fa-dashboard"></i> DELEGATION</a>
				</div>
			</li>
		<?php }}}?>

			<?php
			if($_SESSION['logged_in']['adminuser']==1)
			{
			?>
				<li>

				<div class="dash">

					<a href="<?php echo page_url;?>MIS/index/ALL/<?php echo $st;?>/<?php echo $et;?>/ALL"><i class="fa fa-star"></i> MIS REPORT</a>
				</div>
			</li>
			<?php } ?>
		<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '4')->get();
			if ($module->num_rows() > 0) {?>
				<li>

				<div class="dash">

					<a href="<?php echo page_url;?>ExcelImport/bomimport"><i class="fa fa-star"></i>BOM CORRECTION TOOL</a>
				</div>
			</li>
		<?php }?>

		<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '4')->get();
			if ($module->num_rows() > 0) {?>
				<li>

				<div class="dash">

					<a href="<?php echo page_url;?>Maintenance_support"><i class="fa fa-support"></i>DF HELP TICKET</a>
				</div>
			</li>
		<?php }?>

		<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '5')->get();
			if ($module->num_rows() > 0) {?>

<li>

<div class="mast">

<a <?php if ($this->uri->segment(1) == 'Accounts') { ?>class="active" <?php } ?> href="#"><i class="fa fa-inr"></i> FINANCE <span class="add-extra"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

</div>
							<?php 
					$threeMonthsAgo = date('Y-m-d', strtotime("-11 months"));
					$todaysdate = date('Y-m-d');
					?>
							<ul>
							<?php 

							$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','28')->where('submodule_access','1')->get();

							if($qry->num_rows()>0){

							?>	
							<li><a href="<?php echo page_url;?>Accounts/allrunningdf/<?php echo $threeMonthsAgo;?>/<?php echo $todaysdate;?>/ALL">FINANCE MASTER DASHBOARD</a></li>
							<?php }?>

							<?php 

							$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','29')->where('submodule_access','1')->get();

							if($qry->num_rows()>0){

							?>	
							<li><a href="<?php echo page_url;?>Accounts/paymentdashboard/<?php echo $threeMonthsAgo;?>/<?php echo $todaysdate;?>/ALL">PAYMENT COLLECTION DASHBOARD</a></li>
							<?php }?>

							<?php 

							$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','30')->where('submodule_access','1')->get();

							if($qry->num_rows()>0){

							?>	
							<li><a href="<?php echo page_url;?>Accounts/marketingpaymentdashboard/<?php echo base64_encode($user_id);?>">MARKETING PAYMENT DASHBOARD</a></li>
							<?php }?>
								
								
							</ul>
						</li>




			
		<?php }?>

		</ul>
		<!-- <li>

	<div class="dash">

		<a <?php if ($this->uri->segment(1) == 'Delegation') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Delegation/delegationdashboard"><i class="fa fa-dashboard"></i> DELEGATION</a>
	</div>
</li>
</ul>
</li>
</ul> -->
	</div>
<a href="javascript:void(0);" class="icon colexpicon" onclick="myFunction()"><i class="fa fa-bars"></i></a></div>
<div id="profileimg" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<form method="post" action="<?php echo page_url; ?>User/update_profile/<?php echo $user_id; ?>" enctype="multipart/form-data">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">CHANGE YOUR PROFILE IMAGE</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12"><input type="file" class="form-control" name="profile" value="" placeholder="upload" required></div>
					</div>
				</div>
				<div class="modal-footer"><button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button><input type="submit" id="save" class="btn btn-info" value="Save"> </div>
			</div>
		</div>
	</form>
</div><!-- /.modal -->
<header id="topnav">
	<div class="topbar-main">
		<div class="container-fluid">
			<div class="menu-extras">
				<div class="quotecss hidden-xs" style="display: inline-block;padding-top: 16px;padding-left: 100px;">
					 


					</span>
				</div>
					<?php 
					$st = date('Y-m-d',strtotime('-30 days'));
					$et = date('Y-m-d');
                		if($total<=20)
                		{
                			$icon='😀';
                		}else
                		{
                			$icon='😟';
                		}
                		//if($_SESSION['logged_in']['business_location']!=1 && $user_role!=12){ 
                			if($_SESSION['logged_in']['business_location']!=1){ 
                			?>
                			<div class="col-md-2"></div>
                			<div class="col-md-2" style="padding-left: 0px; padding-top:10px; color:red;text-align: left;"><a href="<?php echo page_url;?>Maintenance_support/meetinglog"><span class="btn btn-success">Log Your Meeting Info <i class="fa fa-handshake-o"></i></span></a></div>

                            <div class="col-md-4 col-sm-12 col-xs-12 hidden-xs" id="blink_text" style="padding-left: 0px; padding-top:10px; color:red;text-align: left;"> <a style="background-color: #049dd4; padding: 10px 10px 10px 10px; color:#fff;" href='<?php echo page_url;?>MIS/index/ALL/<?php echo $st;?>/<?php echo $et;?>/<?php echo $_SESSION['logged_in']['user_id'];?>' target="_blank">YOUR WORK PENDING/DELAYED MIS: <span style="color:#fff;font-weight: bold;font-size:18px" id="blink_text1"><?php echo $total;?>% &nbsp;<span style="font-size:20px;"><?php echo $icon;?></span></a></span>
                            </div>
                        <?php } ?>
				<ul class="nav navbar-nav navbar-right pull-right">

					<li class="dropdown user-box saurabh" style="margin-top:13px;">
						<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
               <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <img src="<?php echo page_url1;?>bell.png" class='img-responsive' style='width:20px;'>
                    <span class='badgeAlert' id="notifyBadge"></span>
                    <span class="caret"></span></a>
                  <ul class="list-notificacao dropdown-menu" id="livenotifications" style="height:383px;">
                      
                      <!-- <li id='item_notification_2'>
                        <div class="media">
                           
                           <div class="media-body">
                              <div class='exclusaoNotificacao'><button class='btn btn-danger btn-xs' id='2' onclick='excluirItemNotificacao(this)'>x</button>
                              </div>
                              <h4 class="media-heading">ITEM 2</h4>
                              <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                           </div>
                        </div>
                     </li>  -->
                  </ul>
               </li>
               
            </ul>
         </div>
					</li>
					<li class="dropdown user-box">
						<div>
							<ul class="list-inline m-b-0">
								<li style="margin-left: -8px;display:none;">

									<div style="    display: flex;">

										<i class="fa fa-clock-o" aria-hidden="true" style="margin-right: 6px; margin-top: 6px; font-size: 15px; color: #188ae2;"></i>

										<p id="demo" style="font-weight:600; margin-top: 4px;">

										</p>

									</div>

									<!-- <script>
										var myVar = setInterval(myTimer, 1000);

										function myTimer() {

											var d = new Date();

											document.getElementById("demo").innerHTML = d.toLocaleTimeString();

										}
									</script> -->

								</li>
								<li><a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true" style="font-size: 20px;color: #0000009e;line-height: 56px; margin-top: -6px;"><i class="fa fa-angle-down" aria-hidden="true"></i>
										<ul class="dropdown-menu">

											<?php
											$user_id = $this->session->userdata['logged_in']['user_id'];
											?>
											<li><a href="<?php echo page_url; ?>Master/User_management/change_password"><i class="ti-settings m-r-5"></i> Change Password</a></li>
											<li><a href="<?php echo page_url; ?>User/signout"><i class="ti-power-off m-r-5"></i> Logout</a></li>
											<?php
											if ($this->uri->segment(1) == 'Dashboard') {
											?>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#profileimg"><i class="ti-upload m-r-5"></i>Change Your Profile Picture</a></li>
											<?php } ?>
											<?php
											if ($this->uri->segment(1) == 'Dashboard') {
											?>
												<!-- <li><a href="javascript:void(0);" data-toggle="modal" data-target="#con-close-modal"><i class="ti-upload m-r-5"></i>Add Profile Status</a></li> -->
											<?php } ?>
										</ul>
									</a>
								</li>
							</ul>
						</div>
					</li>
					<li class="usernamecss"><span style="color:#0000009e ;"><?php echo $first_name; ?> <?php echo $last_name; ?></span></li>
					<li class="dropdown user-box">
						<a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true">
							<?php if ($profilepic) { ?>
								<img src="<?php echo user_profile; ?><?php echo $profilepic; ?>" alt="user-img" class="img-circle user-img"><?php } else { ?><img src="https://prestomitr.com/image_bank/users/1534487737.jpg" alt="user-img" class="img-circle user-img"> <?php } ?><div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div></a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>


<?php }?>

<!--Shubham Pack Menu closed here-->
<script>
	 $( document ).ready(function() {
	 	getnotificationdata();
	 	notificationuserwiseCount();
	 });
// setInterval(getnotificationdata, 1000);
 function getnotificationdata(){
 	$("#livenotifications").html('');
     var departmentid = '<?php echo $_SESSION['logged_in']['department_id'];?>';
    var userid = '<?php echo $_SESSION['logged_in']['user_id'];?>';
     $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Task/notificationuserwise",
        data:"departmentid="+departmentid+"&userid="+userid,
        success:function(data){
          
           $("#livenotifications").html(data);
    
        }
    });
}
function markasread(id,flag)
{
	$.ajax({
	type:"post",
	url:"<?php echo page_url;?>Task/markasreadNotifications",
	data:"id="+id,
	success:function(data){
		notificationuserwiseCount();
	$("#item_notification_"+flag).hide();

	}
	});
}

function notificationuserwiseCount()
{
	   var departmentid = '<?php echo $_SESSION['logged_in']['department_id'];?>';
    var userid = '<?php echo $_SESSION['logged_in']['user_id'];?>';
     $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Task/notificationuserwiseCount",
        data:"departmentid="+departmentid+"&userid="+userid,
        success:function(data){
           $("#notifyBadge").text(data);
    
        }
    });
}
</script>