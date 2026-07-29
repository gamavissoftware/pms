<?php 
$user_id =$this->session->userdata['logged_in']['user_id'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php //echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php //echo sitetitle; ?> MOM Dashboard</title>
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
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
<script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
</head>
    <body>
        <!-- Navigation Bar-->
                <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <div class="wrapper ">
<div class="container-fluid">

</div>
</div>
<div class="wrapper ">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>MOM Module Dashboard</h1><hr>
</div>
  
    <div class="row">
    <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','18')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>			
		<!-- <div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom
		"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>purchase_request.png">
		</div>
		<p>Form</p>
		</div></a>
		</div> -->
	<?php }?>

	<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','19')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
									
		<!-- <div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/user_dashboard
		"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>dashboard.png">
		</div>
		<p>Dashboard</p>
		</div></a>
		</div> -->
	<?php }?>

	<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','20')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
            
		<!-- <div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/mom_assigned_to_you"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>assigned.png">
		</div>
		<p>MOM Assigned to You</p>
		</div></a>
		</div> -->
	<?php }?>

	<?php
	$df_change_module_enabled = $this->db->select('id')->from('module_access')->where('role_id', $user_id)->where('moduleid', '3')->where('access', '1')->limit(1)->get()->num_rows() > 0;
	$can_raise_df_change = false;
	$can_view_df_change = false;
	if ($df_change_module_enabled) {
		$can_raise_df_change = $this->db->select('id')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '3')->where('submoduleid', '41')->where('submodule_access', '1')->limit(1)->get()->num_rows() > 0;
		$can_view_df_change = $this->db->select('id')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '3')->where('submoduleid', '42')->where('submodule_access', '1')->limit(1)->get()->num_rows() > 0;
	}
	if ($this->db->table_exists('df_change_control') && ($can_raise_df_change || $can_view_df_change)) {
	    $change_action_count = 0;
	    if ($this->db->table_exists('df_change_control_departments')) {
	        $this->db->select('id')->from('df_change_control_departments');
	        $this->db->group_start();
	        $this->db->group_start()->where('department_head_id', $user_id)->where('status', 'PENDING_HEAD_ACTION')->group_end();
	        $this->db->or_group_start()->where('assigned_user_id', $user_id)->where_in('status', array('ASSIGNED', 'IN_PROGRESS'))->group_end();
	        $this->db->group_end();
	        $change_action_count = $this->db->get()->num_rows();
	    }
	?>
		<?php if ($can_raise_df_change) { ?>
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Df_change_control/create"><div class="report-box">
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>purchase_request.png">
			</div>
			<p>Raise ECN / IOM</p>
			</div></a>
			</div>
		<?php } ?>

		<?php if ($can_view_df_change) { ?>
			<div class="col-sm-4 col-md-4 col-lg-2">
			<a href="<?php echo page_url;?>Df_change_control"><div class="report-box">
			<div class="text-center">
			<img src="<?php echo dashboard_icon;?>dashboard.png">
			</div>
			<p>DF Change Dashboard</p>
			<?php if ($change_action_count > 0) { ?>
			<div style="background: black; color: white;width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%;
		    position: absolute; right: 1px; top: 12px;"><?php echo $change_action_count; ?></div>
			<?php } ?>
			</div></a>
			</div>
		<?php } ?>
	<?php } ?>

	<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','21')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>					
			
		<!-- <div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/dashboard"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>dashboard.png">
		</div>
		<p>MOM Master Dashboard</p>
		</div></a>
		</div> -->
									
	<?php }?>

	<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','22')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>		
		<!-- <div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/form"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>purchase_request.png">
		</div>
		<p>External MOM Form</p>
		</div></a>
		</div> -->
		<?php }?>

		<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','23')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>	
		<!-- <div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/external_dashboard"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>dashboard.png">
		</div>
		<p>External MOM Dashboard</p>
		</div></a>
		</div> -->
	<?php }?>

	<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','18')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>	
		<div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/createiom"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>purchase_request.png">
		</div>
		<p>Create MOM</p>
		</div></a>
		</div>
	<?php }?>

	<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','21')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>	
		<div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/your_iom_dashboard"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>dashboard.png">
		</div>
		<p>Your Created DFwise MOM</p>
		</div></a>
		</div>
	<?php }?>

	<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','19')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>	
		<div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/iom_master_dashboard"><div class="report-box " >
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>dashboard.png">
		</div>
		<p>DF-wise MOM Master Dashboard</p>
		</div></a>
		</div>
	<?php }?>

		<?php 
		$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','20')->where('submodule_access','1')->get();
		if($qry->num_rows()>0){

		?>	
		<div class="col-sm-4 col-md-4 col-lg-2">
		<a href="<?php echo page_url;?>Mom/dfwise_mom_assigned_to_you"><div class="report-box">
		<div class="text-center">
		<img src="<?php echo dashboard_icon;?>assigned.png">
		</div>
		<p>DF-wise MOM Assiged to You</p>
<?php 
    $q = $this->db->select('id')->from('dfwise_iom_points')->where('responsible_person',$user_id)->where('workstatus',0)->get();
    if($q->num_rows()>0){
?>
		<div style="background: black; color: white;width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%;
    position: absolute; right: 1px; top: 12px;"><?php echo count($q->result());?></div>

<?php }?>
		</div></a>
		</div>
		<?php }?>

									
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
		<script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
		<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
		<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
</body>
</html>
