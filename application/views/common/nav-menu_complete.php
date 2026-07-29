<?php 


 $CI =& get_instance();



$CI->load->model('Fms_model');

$VI=& get_instance();

$VI->load->model('Fms_mismodel');

$DI =& get_instance();

$DI->load->model('Lms_model');

$currentweekdates=$VI->Fms_mismodel->currentweekdates();



//$currentweekdates=$VI->Fms_mismodel->getcurrentweekalldates();

$ststst= base64_encode($currentweekdates[0]);

$endddd=base64_encode($currentweekdates[1]);

$user_id =$this->session->userdata['logged_in']['user_id'];

$profile_image =$this->session->userdata['logged_in']['profile_image'];

$user_role =$this->session->userdata['logged_in']['role'];

$first_name =$this->session->userdata['logged_in']['user_name'];

$last_name =$this->session->userdata['logged_in']['last_name'];

$department_id =$this->session->userdata['logged_in']['department_id'];

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



		background:black;



		color:white;



		width:18px;height:18px;



		text-align:center;



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



	#topnav .topbar-main {



  /* background-color: #2986CE; */



  /* background-image: url('https://prestomitr.com/assets/images/pt.jpg'); */

  background-color:whitesmoke;

  position: fixed;
    width: 100%;
    z-index: 50;

  height: 54px;

  box-shadow:1px 1px 5px lightgray;



}

@media (max-width:576px){
	
	#topnav .topbar-main {



/* background-color: #2986CE; */



/* background-image: url('https://prestomitr.com/assets/images/pt.jpg'); */

background-color:whitesmoke;

position: absolute;
  width: 100%;
  z-index: 50;

height: 54px;

box-shadow:1px 1px 5px lightgray;



}
}



.usernamecss {



    margin-top:14px;



  }



@media only screen and (max-width: 600px) {



  .usernamecss {



    margin-top:0px;



  }



}







.secondul



{



    max-height:450px;



    overflow-y:auto;



}



.secondulss{



    max-height:200px;



    overflow-y:auto;



}



 </style>







 <div class="leftmenu" id="mobilemenu">



	<div class="logo" style="background-color: whitesmoke;">



		<a href="#"><img src="<?php echo assets_url;?>images/logo_mitr.png" alt="Logo"></a>



	</div>



	<div id="mobilemenuwrap" >



		<ul >



			<li>

<div class="dash">

				<a <?php if($this->uri->segment(1)=='Dashboard'){?>class="active"<?php }?> href="<?php echo page_url;?>Dashboard"><i class="fa fa-dashboard" ></i> DASHBOARD</a>



</div>



			</li>



			<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','1')->get();



if($module->num_rows()>0)



{



	foreach($module->result() as $moddata);



if($moddata->access=='1')



{



	



	$submoduleid = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40', '41', '42', '43');







 	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



?>	



			<li>

			<div class="mast">	

			<a <?php if($this->uri->segment(1)=='Master'){?>class="active"<?php }else if($this->uri->segment(1)=='Form'){?>class="active"<?php }else if($this->uri->segment(1)=='FMS'){?>class="active"<?php }else if($this->uri->segment(1)=='Store'){?>class="active"<?php }else if($this->uri->segment(1)=='User'){?>class="active"<?php }else if($this->uri->segment(1)=='Hr'){?>class="active"<?php }else if($this->uri->segment(1)=='Hr'){?>class="active"<?php }?> href="#"><i class="fa fa-cogs"></i> MASTER</a>

	</div>

				<ul>



				<li><a href="<?php echo page_url;?>Dashboard/user_report">User Management</a> 



<ul class="secondul">



<?php 



			$submoduleid = array('1','2','3','4','5','6','7','8','9','10','11','12','13');



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','1')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>			



	<li> <a href="<?php echo page_url;?>Master/User_management" title="List of all users"> User Listing</a></li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','2')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li> <a href="<?php echo page_url;?>Master/User_management/user_role" title="User Roles"> User Role Management</a></li>



	<?php }?>	



<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','3')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>		



	<li><a href="<?php echo page_url;?>Master/User_management/Departments" title="User Departments">  Department Management</a></li>



	<?php }?>



<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','4')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>		



	<li> <a href="<?php echo page_url;?>Master/User_management/userwise_permission_dashboard" title="Set user wise permission"> Set Access Permission</a></li>



	<?php }?>



<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','5')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>		



	<li><a href="<?php echo page_url;?>Master/Business_location" title="Manage Business Locations">  Business Location</a></li><?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','6')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



									



	<li><a tabindex="-1" href="<?php echo page_url;?>Master/User_management/presto_team_list/" title="Create & Manage Team"> Team Management</a></li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','7')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li><a tabindex="-1" href="<?php echo page_url;?>FMS/holidays" title="Yearly Holidays"> Holidays Management</a></li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','8')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li><a tabindex="-1" href="<?php echo page_url;?>Dashboard/body_template" title="Manage Email/SMS content">  Sms Email Template</a></li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','9')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li><a href="<?php echo page_url;?>Reporting/userwisepermisionreport" title="Permissions given to users"> Users Permission Report</a></li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','10')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li><a href="<?php echo page_url;?>Master/User_management/add_new_vendor" title="Manage Suppliers"> Vendor Management</a></li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','11')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li><a href="<?php echo page_url;?>Master/User_management/email_template" title="Email Template"> Email Template</a></li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','12')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li><a href="<?php echo page_url;?>Form/report_mis_master" title="Report for MIS"> Set MIS TAT of Reports</a>



     </li>



	<?php }?>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','13')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>	



	<li><a href="<?php echo page_url;?>User/add_ip" title="Report for MIS"> IP MANAGEMENT</a></li>



	<?php }?>



			<?php }?>



	



	



	</ul>



	</li>



	<?php 



	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','14')->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



	?>



	<li><a href="javascript:void(0);">Delegation Management</a>



					    <ul class="secondul">



					     <li><a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_master"> Delegation Master </a></li></ul>



	</li>



	<?php }?>



	

	

<!-- 	<li><a href="javascript:void(0);">Sales Master</a>



	<ul class="secondul">

<li>

<a tabindex="-1" href="<?php echo page_url;?>Master/Lead_Type/"> Lead Type</a>

</li>



<li>

<a tabindex="-1" href="<?php echo page_url;?>Master/Lead_Type/add_lead_source"> Lead Source</a>

</li>	

<li>

<a tabindex="-1" href="<?php echo page_url;?>Master/Customer_type/add_customer_type"> Customer Type</a></li>



<li><a href="<?php echo page_url;?>Master/Employee_Target"> Set Target of Employees</a></li>

<li>

<a tabindex="-1" href="<?php echo page_url;?>Master/Employee_Target/add_employee_logout_time/">Set Logout Time</a>

</li>

<li>

<a tabindex="-1" href="<?php echo page_url;?>ExcelImport/excel_import">Import Lead</a>

</li>

</ul>



	</li> -->



	<?php $submoduleid = array('144','145','146','147');



									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



									if($qry->num_rows()>0){



									



									?>



					



					<li><a href="javascript:void(0);">Form Management</a>



					    <ul class="secondul">



					       <?php 



									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','144')->where('submodule_access','1')->get();



									if($qry->num_rows()>0){



									?>



									<li><a href="<?php echo page_url;?>Form/create_new_form" title="Create New Dynamic Forms"> Create New Form</a></li><?php }?>



									



										<?php 



									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','145')->where('submodule_access','1')->get();



									if($qry->num_rows()>0){



									?>



									<li><a href="<?php echo page_url;?>Form/pending_for_review"> Pending Form for Review</a></li><?php }?>



									



										<?php 



									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','147')->where('submodule_access','1')->get();



									if($qry->num_rows()>0){



									?>



								



									<li><a href="<?php echo page_url;?>Form/master_index">  Master Index</a></li>



										<?php }?>



											<?php 



									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','146')->where('submodule_access','1')->get();



									if($qry->num_rows()>0){



									?>



										<li><a href="<?php echo page_url;?>Form/all_forms"> All Dynamic Forms</a></li>



									<?php }?>



					    </ul>



					</li>



					<?php }?>



	



		<?php 



			$submoduleid = array('15','16','17');



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>			



					<li><a href="javascript:void(0);">Checklist Management</a>



					    <ul class="secondul">



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','15')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					       <li><a href="<?php echo page_url;?>Checklist/Turnaroundtime"> Checklist TAT</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','16')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



							<li><a href="<?php echo page_url;?>Checklist/create_checklist"> Checklist Management</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','17')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



							<li><a href="<?php echo page_url;?>Checklist/mispercentage"> Checklist MIS Factor</a></li>



						<?php }?>



									



					    </ul>



					</li>



			<?php }?>



					<?php 



			$submoduleid = array('18','19','20','21','22','23','24','25','26','27','28');



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>	



				    <li><a href="<?php echo page_url;?>Dashboard/fms_report">Fms Management</a>



				     <ul class="secondul">



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','18')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>FMS/production_plan_flow"> Production Flow </a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','19')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									



									<li><a href="<?php echo page_url;?>FMS/fms_flow"> Fms Flow</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','20')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>FMS/machinecostprice"> Machine Cost Price </a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','21')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									



						<li><a href="<?php echo page_url;?>FMS/instruments"> Instruments Management</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','22')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>FMS/add_color_combination"> Conditional Color Management</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','23')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>FMS/order"> Order Management</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','24')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>FMS/sales_orders"> Sales Order Management</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','25')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									



						<li><a href="<?php echo page_url;?>FMS/service_orders"> Service Order Management</a></li><?php }?>



								



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','26')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>								



						<li><a href="<?php echo page_url;?>FMS/salesforceorder/1"> Sales SalesForce Order Management</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','27')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>		



									<li><a href="<?php echo page_url;?>FMS/salesforceorder/2"> Service SalesForce Order Management</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','28')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									



						<li><a href="<?php echo page_url;?>FMS/orderplanninglist"> Order Planning</a></li><?php }?>



									



				    </ul></li>



			<?php }?>



				<?php 



			$submoduleid = array('29','30','31','32','33','34','35','36','37');



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>	    



				<li><a href="javascript:void(0);">Store</a>



				<ul class="secondul">



				<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','29')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				    <li style="color:red;"><a href="<?php echo page_url;?>Store/setpoinstructions">PO Instruction Script</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','30')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li style="color:red;"><a href="<?php echo page_url;?>Store/add_rack_location"> Rack Location</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','31')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li style="color:red;"><a href="<?php echo page_url;?>Store/machineparts">IMS</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','32')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li ><a href="<?php echo page_url;?>Store/bom_materials"> BOM Material</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','33')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li><a href="<?php echo page_url;?>Store/add_general_items">House Keeping Items</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','34')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li><a href="<?php echo page_url;?>Store/ageing">Ageing Factor</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','35')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li><a href="<?php echo page_url;?>Vendor/vendorform">Vendor Form</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','36')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li><a href="<?php echo page_url;?>Vendor/approved_vendors">Approved Vendors</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','37')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					<li><a href="<?php echo page_url;?>Vendor/pending_for_review">Vendor for Approval</a></li>



						<?php }?>



				</ul>



				</li>



			<?php }?>



			<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','38')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				<li><a href="<?php echo page_url;?>Master/Units"> Units</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','39')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				<li><a href="<?php echo page_url;?>Master/Saleszone"> Sales Zone</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','40')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				<li><a href="<?php echo page_url;?>User/edit_home_image"> Login Background Image Update</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','41')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				<li><a href="<?php echo page_url;?>User/edit_top_image"> Top Bar Image Management</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','42')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				<li><a tabindex="-1" href="<?php echo page_url;?>User/Login_ip_tracking">&nbsp; Ip Tracking</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','43')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				<li><a href="<?php echo page_url;?>Hr/employees_birthday" title="Prestogroup Employee">Employees Birthday Dashboard </a></li>



						<?php }?>



						



							<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','141')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



				<li><a href="<?php echo page_url;?>Reporting/show_modules" title="Prestogroup Employee">System Reports Dashboard </a></li>



						<?php }?>



						<?php 



$submoduleid = array('87','88','89','90');



$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



if($qry->num_rows()>0){



?>



						<li><a href="javascript:void(0);">MIS Report</a>



						<ul>



						<?php 



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','87')->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?> 



						<li><a href="<?php echo page_url;?>Reporting/misscore/"> Overall MIS </a></li>



			<?php }?>



			<?php 



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','88')->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>



						<li><a href="<?php echo page_url;?>FMS/fmsmis/"> Fms MIS </a></li>



			<?php }?>



			<?php 



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','89')->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>



						



						<li><a href="<?php echo page_url;?>FMS/nonfmsmis/"> Non Fms MIS </a></li>



			<?php }?>



<?php 



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','90')->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>						



						<li><a href="<?php echo page_url;?>Delegation/audit_dashbaord">Auditor Delegation Report</a></li>



			<?php }?>



						</ul>



						</li>



						



<?php }?>		



				



				</ul>



			</li>



			



<?php }}}?>	



	

	<!-- REPORTING MODULE START HERE-->



	<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','7')->get();



if($module->num_rows()>0)



{



	foreach($module->result() as $moddata);



if($moddata->access=='1')



{



	



	$submoduleid = array('65','66','67','68','69','70','71','72','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99','100','101','102','103','104','105','106','107','108','109','110','111','112','113','114','115','116','117','118','119','120','121','122','123','124','125','126','127','128','129','130','131','132','133','134','135','136','137','138','139');







 	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



?>



			<li>

			<div class="reports">	

			<a href="#" <?php if($this->uri->segment(1)=='Reporting'){?>class="active"<?php }else if($this->uri->segment(1)=='Challan'){?>class="active"<?php }else if($this->uri->segment(1)=='Serviceissue'){?>class="active"<?php }?>><i class="fa fa-bar-chart"></i> REPORTING</a>

	</div>

			    <ul>



				<?php 



			$submoduleid = array('65','66','67','68','69','70','71','72','73','74','75','76','77','78','79','80','81','82','83','84','85','86');



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>

                  

				  <li><a href="<?php echo page_url;?>Dashboard/sales_report">Sales </a>

				  <li><a href="<?php echo page_url;?>Dashboard/service_report">Service</a>



			    	<li><a href="<?php echo page_url;?>Dashboard/departmentwise_report">Production</a>



								   <ul class="secondul">



								   <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','65')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



							



						<li class=""><a href="<?php echo page_url;?>FMS/fms_reporting/1"> FMS  Reporting </a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','66')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



							



						<li><a href="<?php echo page_url;?>FMS/view_planned_orders"> Planned Orders </a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','67')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



							



						<li><a href="<?php echo page_url;?>FMS/planned_actual"> Planned to Actual</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','68')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Reporting/pendingorders">Pending Order Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','69')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Reporting/salespendingorders">Sales Pending Order Report</a></li>



						<?php }?>	



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','70')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Reporting/readyforpacking">Machines for Packing</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','71')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



													



									<li><a href="<?php echo page_url;?>Reporting/readyforpackingforservice">Machines for Packing for Service</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','72')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



						<li><a href="<?php echo page_url;?>Reporting/completedorders"> Ready for Dispatch Complete Report</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','73')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/reorderlist">Reorder Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','74')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/completedorders"> View Your Ready for Dispatch Orders</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','75')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Reporting/dispatchfortommorow"> Dispatch for Tomorrow</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','76')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/servicedispatchhistory"> Dispatch for Tomorrow Service History</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','77')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/dispatchforaccounts">Ready For Billing</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','78')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li><a href="<?php echo page_url;?>Reporting/salesdispatchfortommorow">Sales Dispatch for Tomorrow</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','79')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/salesstockvaluelist">Sales Stock List</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','80')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/servicerequest"> Service Request</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','81')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li><a href="<?php echo page_url;?>Reporting/previousorders"> Previous Order History</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','82')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/lotorderlist"> Lot Order List</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','83')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



										<li><a href="<?php echo page_url;?>Reporting/orderstarttoendreport">Orders Start to End Report </a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','84')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/divertedorderlist"> Diverted Order List</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','85')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li><a href="<?php echo page_url;?>Reporting/machinecostprice">WIP Production Cost</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','86')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li><a href="<?php echo page_url;?>Reporting/finishedgoods">Finished Goods Value</a></li>



						<?php }?>



						



					



									



								    </ul>



					</li>			    



			<?php }?>



			



			



			<?php 



			$submoduleid = array('91','92','93','94','95','96','97','98','99','100','101','102','103','104','105','106','107','108','109','110','111','112','113','114','115','116','117','118','119','139');



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>



								



								<li><a href="<?php echo page_url;?>Dashboard/store_report">Store</a>



								        <ul class="secondul">



								     <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','91')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>       



									<li><a href="<?php echo page_url;?>Store/machine_part_data_with_picture"> Machine parts with Picture </a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','93')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Reporting/indentreportforaudit">Indent Report for Audit </a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','94')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Reporting/yourindents"> Your Created Indent(s) </a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','95')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



                                    <li><a href="<?php echo page_url;?>Reporting/itemmrn">MRN</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','96')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



						<li><a href="<?php echo page_url;?>Reporting/accountsmrnreport">ACCOUNTS MRN REPORT</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','97')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



                                   



    									<li><a href="<?php echo page_url;?>Reporting/gateentry">QC</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','98')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Store/storereciept">STORE RECIEPT</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','99')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Store/pendingpoitemsforstore">PENDING PO ITEMS (STORE)</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','100')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Reporting/prduetomaterialdiversion">MATERIAL DIVERSION PR</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','101')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



						<li><a href="<?php echo page_url;?>Reporting/todaysissueditembystore">Todays Issued Items</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','102')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



								<li><a href="<?php echo page_url;?>Reporting/unacknowledgedissueditems">Audit Material Issued not updated</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','103')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li><a href="<?php echo page_url;?>Store/unacknowledgeditems">Material Issued not updated for store</a></li>



						<?php }?>



						



    								



    							   <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','104')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Reporting/qcdonereport">Completed Mrn QC</a></li>



						<?php }?>		



    							



    							



    									



    								<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','105')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Reporting/autopr">Auto PR Request</a></li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','106')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Reporting/rejecteditemreq">QC Rejected Items</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','107')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Reporting/debitnote">Debit Notes</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','108')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



								<li><a href="<?php echo page_url;?>Reporting/rejectionchallanreq">Rejected Challans</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','109')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Reporting/minimumlevelitems">Minimum Level Stock</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','110')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



    									<li><a href="<?php echo page_url;?>Reporting/maximumlevelitem">Maximum Level Stock</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','111')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Challan/nrgp_challan">NRGP Challan</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','112')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    										<li><a href="<?php echo page_url;?>Challan/nrgp_challan_dashboard">NRGP Challan Dashboard</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','113')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Serviceissue/service_request">Open Service Issue for Inward</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','114')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									



									<li><a href="<?php echo page_url;?>Serviceissue/serviceissuerequest">Service Issue Request (History)</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','115')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    									<li><a href="<?php echo page_url;?>Challan/rgp_challan_items">RGP Challan</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','116')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    										<li><a href="<?php echo page_url;?>Challan/rgp_challan_dashboard">RGP Challan Dashboard</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','117')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    										<li><a href="<?php echo page_url;?>Store/anytime_rejection_dashboard">Anytime Rejection Store Dashboard</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','118')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									



    										<li><a href="<?php echo page_url;?>Store/anytime_rejection_rgp_dashboard">Anytime Rejection RGP Dashboard</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','119')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    										<li><a href="<?php echo page_url;?>Store/anytime_rejection_gateentry_dashboard">Anytime Rejection Gate Entry Dashboard</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','139')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



    										<li><a href="<?php echo page_url;?>Store/anytime_rejection_qc_dashboard">Anytime Rejection QC Dashboard</a></li>



						<?php }?>



								        </ul>



								</li>



			<?php }?>



			<?php 



			$submoduleid = array('92','120','121','122','123','124','125','126','127','128','129','130','131','132','133','134','135','136','137','138', '143');



			$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



			if($qry->num_rows()>0){



			?>



			



								<li><a href="<?php echo page_url;?>Dashboard/purchase_report">Purchase</a>



								<ul class="secondul">





                            	<?php 



									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','143')->where('submodule_access','1')->get();



									if($qry->num_rows()>0){



									?>



                                <li><a href="<?php echo page_url;?>Store/indent_form"> Create Indent</a></li>

                                <?php }?>

                                

								    <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','92')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Reporting/pending_indent_dashboard"> Pending Indent(s) </a></li><?php }?>



                                <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','120')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Store/masterrequest">ITEM MASTER REQUEST </a></li><?php }?>



<!--<li><a href="<?php echo page_url;?>Reporting/pendingpr"> Pending PR for PO Genaration</a></li>-->



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','121')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Store/createbulkpo"> Pending PR for PO Genaration</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','122')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>







<li><a href="<?php echo page_url;?>Reporting/pendingpoforapproval">  Pending PO for Approval Request</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','123')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Reporting/pendingpoforpayment">PENDING PAYMENTS</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','124')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Store/emailnotifications">PENDING EMAILS</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','125')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Store/followup_dashboard">PO Follow-up </a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','126')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Store/po_delayed_report">PO Delayed Report </a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','127')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Reporting/criticalitems">Critical Items</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','128')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>







<li><a href="<?php echo page_url;?>Reporting/closedpo">Closed PO</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','129')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Store/field_boy_scheduler_dashboard">Field boy scheduler dashboard</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','130')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Reporting/rejected_po_report">Rejected PO Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','131')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>







<li><a href="<?php echo page_url;?>Reporting/pendingordersreport">Pending Order Dashboard</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','132')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>







<li><a href="<?php echo page_url;?>Reporting/indent_vs_pr_report">Indent VS PR Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','133')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li><a href="<?php echo page_url;?>Reporting/repeated_item_report">Repeated Items Purchase Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','134')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>







<li><a href="<?php echo page_url;?>Reporting/filterbyvendor">Vendorwise Purchase Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','135')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>







<li><a href="<?php echo page_url;?>Reporting/pendingpoconsolidated">Consolidated PO</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','136')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>







    									<li><a href="<?php echo page_url;?>Reporting/freight_approval_dashboard"> FREIGHT CHARGES DASHBOARD</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','137')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



    									<li><a href="<?php echo page_url;?>Reporting/pr_vs_po_report">PR vs PO Report</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','7')->where('submoduleid','138')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li><a href="<?php echo page_url;?>Store/anytime_rejection_purchase_dashboard"> Anytime Rejection Purchase Dashboard</a></li>



						<?php }?>	



								</ul></li>



			<?php }?>									



									



			    </ul>



			</li>



<?php }}}?>



		<!-- REPORTING MODULE END HERE-->

	
<!--FORM DASHBOARD START HERE-->
<li>
				<div class="mom-dash"><a href="#"><i class="fa fa-wpforms"></i> Form & Dashboard</a>
				</div>
				<ul>
				
				<?php 
									$submoduleid = array('81','87','234','115','155','146','263','72','101');
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
								<li>
									<a href="<?php echo page_url;?>Dashboard/sales_forms"> Sales </a>
									</li>
									<?php }?>
									<?php 
									$submoduleid = array('296','297','298');
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Dashboard/accounts_forms">Accounts</a></li>
									<?php }?>
									<li><a href="<?php echo page_url;?>Dashboard/common_forms">Common Forms</a></li>
									<li><a href="<?php echo page_url;?>Dashboard/staff_forms">Staff Zone</a></li>
									<?php 
									$submoduleid = array('133','210','207','363','195','229');
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Dashboard/purchase_forms">Purchase & Store</a></li>
									<?php }?>

					<?php 
									$submoduleid = array('28','29','30', '255');
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
					<!-- <li><a href="">Checklist </a>
				<ul class="secondul">
					    <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','28')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									<li><a href="<?php echo page_url;?>Checklist/view_your_checklist"> View Your Checklist Report</a></li><?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','29')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
									
									<li><a href="<?php echo page_url;?>Checklist/user_monthly_report"> View Your Checklist</a></li>
									<?php }?>
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','30')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Checklist/checklist_dashboard"> Auditor Checklist Dashboard</a></li>
									<?php }?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','255')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
										<li><a href="<?php echo page_url;?>Checklist/auditor_check_dashboard"> Auditor Checklist Reporting</a></li>
									<?php }?>
					</ul>
					</li> -->
					<?php }?>

						 <?php 
                    $submoduleid = array('50', '153','238', '330', '31', '32', '257');
                    $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();
                    if($qry->num_rows()>0){
                    ?>
					<li><a href="<?php echo page_url;?>Dashboard/service_forms">Service</a>
					
					</li>
					<?php }?>
					
				</ul>
			</li>
		
	<!--FORM DASHBOARD START HERE-->
<li>

						<div class="dele">

						<a href="" <?php if($this->uri->segment(1)=='Form'){?>class="active"<?php }?>><i class="fa fa-users" ></i>HELP TICKET</a>

	</div>



					<ul class="secondul">



					    

<div class="dele-links">

<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/12">Help Ticket for GM Sir</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/11">Help Ticket for Purchase</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/10">Help Ticket for Dispatch</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/8">Help Ticket for Service</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/7">Help Ticket for Maintenance</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/6">Help Ticket for EA</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/4">Help Ticket for HR</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/3">Help Ticket for IT</a></li>
<li><a tabindex="-1" href="<?php echo page_url;?>Form/view/5">Help Ticket for Sales</a></li>

</div>

					</ul>



					</li>	



	<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','2')->get();



if($module->num_rows()>0)



{



	foreach($module->result() as $moddata);



if($moddata->access=='1')



{



	



	$submoduleid = array('44','45','46','47','48','49','50','51','52');







 	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



?>		 			







					<li>

						<div class="dele">

						<a href="" <?php if($this->uri->segment(1)=='Delegation'){?>class="active"<?php }?>><i class="fa fa-users" ></i>DELEGATION</a>

	</div>



					<ul class="secondul">



					    	<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','44')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>

<div class="dele-links">

									<li>



									



<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_task"> Delegation Form </a>



						</li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','45')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegation_dashboard"> Delegation Dashboard </a>



									</li>



						<?php }?>



									



	<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','46')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>								



<li>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegated_task_history"> Delegated Task History </a>



									</li>



<li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','47')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/delegated_task"> Task Delegated to You </a>



									</li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','48')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/calls_delegation"> Calls Delegation Form </a>



									</li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','49')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/total_Calls_delegated"> Overall Calls Delegation Dashboard </a>



									</li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','50')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/your_delegated_calls_dashboard"> Your Delegated Calls Dashboard </a>



						</li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','51')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/calls_delegated_dashboard"> Call Delegated to You </a>



									</li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','52')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



<li>



<a tabindex="-1" href="<?php echo page_url;?>Delegation/Calls_delegation_report_for_auditor"> Delegated Calls Auditor Dashboard </a>



									</li>

						</div>

						<?php }?>



					</ul>



					</li>



					



			<?php }}}?>	



					

					

					

					





					<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','3')->get();



if($module->num_rows()>0)



{



	foreach($module->result() as $moddata);



if($moddata->access=='1')



{



	



	$submoduleid = array('53','54');







 	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



?>		



					<li>

					<div class="task-red">	

					<a href="javascript:void(0);" <?php if($this->uri->segment(1)=='Reminder'){?>class="active"<?php }?>><i class="fa fa-tasks"></i>TASK</a>

	</div>

					<ul class="secondul">



					<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','53')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					   <li class=""><a href="<?php echo page_url;?>Reminder">Task Form </a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','54')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li class=""><a href="<?php echo page_url;?>Reminder/reminder_dashboard">Task Reminder Dashboard</a></li>



						<?php }?>			



							



					</ul>



					</li>



<?php }}}?>



<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','4')->get();



if($module->num_rows()>0)



{



	foreach($module->result() as $moddata);



if($moddata->access=='1')



{



	



	$submoduleid = array('55','56','57','58');







 	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



?>		



					<li>

					<div class="check-list">	

					<a href="<?php echo page_url;?>Dashboard/checklist_dashboard" <?php if($this->uri->segment(1)=='Checklist'){?>class="active"<?php }?>><i class="fa fa-list"></i>CHECKLIST </a>

	</div>

				<ul class="secondul">



					    <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','55')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Checklist/view_your_checklist"> View Your Checklist/Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','56')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li><a href="<?php echo page_url;?>Checklist/user_monthly_report"> View Checklist Report</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','57')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



										<li><a href="<?php echo page_url;?>Checklist/checklist_dashboard"> Auditor Checklist Dashboard</a></li>



						<?php }?>



									



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','4')->where('submoduleid','58')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



						<li><a href="<?php echo page_url;?>Checklist/auditor_check_dashboard"> Auditor Checklist Reporting</a></li>



						<?php }?>			



					</ul>



					</li>



<?php }}}?>



<!--FORM DASHBOARD START HERE-->



<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','5')->get();



if($module->num_rows()>0)



{



	foreach($module->result() as $moddata);



if($moddata->access=='1')



{



	



	$submoduleid = array('59','60');







 	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



?>	



			<li>

<div class="dar-list">

				<a href="#" <?php if($this->uri->segment(1)=='Daily_reporting'){?>class="active"<?php }?>><i class="fa fa-calendar-check-o"></i> DAR</a>

	</div>

				<ul>									



						

 <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','140')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					 <li class="">



								<a href="<?php echo page_url;?>Daily_reporting">Daily Reporting Form</a>



									</li>



						<?php }?>



					 <?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','59')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



					 <li class="">



								<a href="<?php echo page_url;?>Daily_reporting/dashboard">Daily Reporting Dashboard</a>



									</li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','60')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li class="">



								<a href="<?php echo page_url;?>Daily_reporting/reporting_dashboard">Staff Reporting Dashboard for Sir</a>



									</li>



						<?php }?>		



					



				</ul>



			</li>







<?php }}}?>



<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','6')->get();



if($module->num_rows()>0)



{



	foreach($module->result() as $moddata);



if($moddata->access=='1')



{



	



	$submoduleid = array('61','62','63','64');







 	$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where_in('submoduleid',$submoduleid)->where('submodule_access','1')->get();



	if($qry->num_rows()>0){



?>







					<li>

					<div class="mom-dash">	

					<a href="javascript:void(0);" <?php if($this->uri->segment(1)=='Mom'){?>class="active"<?php }?>><i class="fa fa-pencil-square-o"></i>MOM</a>

	</div>

					<ul class="secondul">



					<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','61')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>	



									<li><a href="<?php echo page_url;?>Mom">MOM Form</a></li>



						<?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','62')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									<li class=""><a href="<?php echo page_url;?>Mom/dashboard">MOM SIR Dashboard</a></li>



						<?php }?>



<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','63')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>						



									<li class="">



								<a href="<?php echo page_url;?>Mom/user_dashboard">MOM Dashboard</a>



						</li><?php }?>



						<?php 



						$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','6')->where('submoduleid','64')->where('submodule_access','1')->get();



						if($qry->num_rows()>0){



						?>



									



									<li class="">



									<a href="<?php echo page_url;?>Mom/mom_assigned_to_you">MOM Assigned to You</a>



									</li>



						<?php }?>	



					</ul>



					</li>



<?php }}}?>			



	<!--FORM DASHBOARD START HERE-->



	



	



<?php



$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','9')->get();



if($module->num_rows()>0)



{



foreach($module->result() as $moddata);



if($moddata->access=='1')



{







?>		



		<li>

<div class="last-product">

	<a href="javascript:void();"><i class="fa fa-product-hunt" aria-hidden="true"></i>







PRODUCTION</a>

</div>

	<ul>



	    



	    



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



<li>



                                <a href="#"><?php echo $respp->production_flow;?> FMS</span> </a>



								<?php



								if($process->num_rows()>0)



								{



								?>



	                                <ul>



                                  <?php



									$donarr=array();



								  foreach($process->result() as $proc)



								  {



                            $mmer=$CI->Fms_model->checkifmsmerge($proc->flow_id);



                            



                            $skkipl=$CI->Fms_model->checkifstepisskippable($proc->flow_id);



                            



                            /** CHECK FOR DEPENDENCY **/



                            $checkdepend=$this->db->select('dependency')->from('fms_flow')->where('flow_id',$proc->flow_id)->where('dependency','1')->get();



                            $depend=$checkdepend->num_rows();



	



	



	 



		if($mmer==0 && $skkipl==0 && $depend==0)



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











<?php



/** CHECK FOR OBSERVER **/



$Restyyuuuu=$this->db->select('a.flowid,b.production_flow_id,fms_flow')->from('flowobserver a')->join('fms_flow b','a.flowid=b.flow_id')->where('a.userid',$_SESSION['logged_in']['user_id'])->get();



if($Restyyuuuu->num_rows()>0)



{



	foreach($Restyyuuuu->result() as $Restyyuuuu123)



	{



?>



	<li><a href="<?php echo page_url;?>FMS/pendingorder/<?php echo $Restyyuuuu123->flowid;?>/0">OBSERVER <?php echo strtoupper($Restyyuuuu123->fms_flow);?></a></li>







<?php



}



}



/** END **/



?>							



	</ul>



	</li>



	<?php } }?>







	</ul>



	</li>



			</ul>



	</div>

	



	<a href="javascript:void(0);" class="icon colexpicon" onclick="myFunction()">



		<i class="fa fa-bars"></i>



	</a>



</div>





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







                   <div class="menu-extras">



                        



                        <div class="quotecss hidden-xs" style="display: inline-block;padding-top: 16px;padding-left: 100px;">



							   <?php 



                                $q = $this->db->select('quote,	id')->from('quote_of_the_day')->where('added_by',$user_id)->get();



						if($q->num_rows()>0){



							foreach($q->result() as $quote);



							echo '<a style="color:#0000009e" href="'.page_url.'Master/User_management/change_quote/"'.$quote->id.'"><span>"'.$quote->quote.'"'."</a></span>";



							



						}



							   ?>



                            </div>



							



                        <ul class="nav navbar-nav navbar-right pull-right">



                           



                          <li class="dropdown user-box">



                               



                                <div >



                                    <ul class="list-inline m-b-0">



									<li style="margin-left: -8px;">

					<div style="    display: flex;">

									<i class="fa fa-clock-o" aria-hidden="true" style="margin-right: 6px; margin-top: 6px; font-size: 15px; color: #188ae2;"></i>

									<p id="demo" style="font-weight:600; margin-top: 4px;">

</p>

					</div>

							<script>



var myVar = setInterval(myTimer, 1000);



function myTimer() {

  var d = new Date();

  document.getElementById("demo").innerHTML = d.toLocaleTimeString();

}

							</script>

									</li>



                                        <li>



                                             <a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true" style="font-size: 20px;color: #0000009e;line-height: 56px; margin-top: -6px;">



                                                <i class="fa fa-angle-down" aria-hidden="true"></i>



                                    <ul class="dropdown-menu">



                                   



                                   <?php



    $user_id =$this->session->userdata['logged_in']['user_id'];	



  



    ?>



                                    <li><a href="<?php echo page_url;?>Master/User_management/mark_your_attendance"><i class="ti-settings m-r-5"></i> Mark Your Attendance</a></li>



                                  



                                    <li><a href="<?php echo page_url;?>Master/User_management/change_password"><i class="ti-settings m-r-5"></i> Change Password</a></li>



                                    <li><a href="<?php echo page_url;?>User/edit_registration"><i class="ti-settings m-r-5"></i> Edit Company Profile</a></li>



                                    <li><a href="<?php echo page_url;?>User/signout"><i class="ti-power-off m-r-5"></i> Logout</a></li>



                                    <?php 



                                    if($this->uri->segment(1)=='Dashboard'){



                                    ?>



                                    	<li><a href="javascript:void(0);" data-toggle="modal" data-target="#profileimg"><i class="ti-upload m-r-5"></i>Change Your Profile Picture</a></li>



                                    	<?php }?>



                                    	



                                    	<?php 



                                    if($this->uri->segment(1)=='Dashboard'){



                                      



                                    ?>



                                    	<li><a href="javascript:void(0);" data-toggle="modal" data-target="#con-close-modal"><i class="ti-upload m-r-5"></i>Add Profile Status</a></li>



                                    	<?php }?>



                                </ul>







                                            </a>



                                           



                                        </li>



                                    </ul>



                                </div>



                                



                            </li>



                            <li class="usernamecss"><span style="color:#0000009e ;"><?php echo $first_name;?> <?php echo $last_name;?></span></li>







                            <li class="dropdown user-box">



                                <a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true">



                                    <?php if($profilepic){?>



                                    <img src="<?php echo user_profile;?><?php echo $profilepic;?>" alt="user-img" class="img-circle user-img">



                                    <?php }else{?>



                                     <img src="https://prestomitr.com/image_bank/users/1534487737.jpg" alt="user-img" class="img-circle user-img"> <?php }?>



                                    <div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div>



                                </a>







                               



                            </li>



                        </ul>



                       



                    </div>







                </div>



            </div>







           



        </header>



