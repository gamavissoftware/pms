<?php 
$business_location = $this->session->userdata['logged_in']['business_location'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright;?>">
<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
<title><?php echo sitetitle;?> Edit User List</title>
<!-- Table Responsive css -->
<script src="<?php echo assets_url;?>js/angular.min.js"></script>
<!-- DataTables -->
<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
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
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->

<script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style type="text/css">
.select2-container
{
width: 100% !important;
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
<div class="container-fluid">
<!-- Page-Title -->
<div class="row" style="margin-top:20px;">
<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
<div class="page-title-box">
<div class="btn-group pull-right">

</div>

<h4 class="page-title">Edit User Profile</h4>
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
$id = $this->uri->segment(4);
$this->db->select('*')->from('system_users')->where('user_id',$id);
$query = $this->db->get();
$res = $query->result();
foreach($res as $row){
}
$projectCoordinatorFieldExists = $this->db->field_exists('project_coordinator_user_id', 'system_users');
$selectedProjectCoordinatorId = ($projectCoordinatorFieldExists && isset($row->project_coordinator_user_id)) ? (int)$row->project_coordinator_user_id : 0;
?>

<form method="post" id="loginForm" action="<?php echo page_url;?>Master/User_management/update_user_profile/<?php echo $row->user_id;?>" enctype="multipart/form-data">
<input type="hidden" name="old_img" value="<?php echo $row->profile_image;?>" >
<input type="hidden" name="old_adharcard" value="<?php echo $row->aadharcard;?>">
<div class="row">
<div class="col-md-2">
<div class="form-group">
<label>First Name</label>
<span id="error_first_name" style="color:red;">*</span>
<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $row->first_name;?>">

</div>
</div>
<div class="col-md-2">
<div class="form-group">
<label>Last Name</label>
<span id="error_last_name" style="color:red;">*</span>
<input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $row->last_name;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Email-ID</label>
<span id="error_email" style="color:red;">*</span>
<input type="text" class="form-control" name="email" id="email" value="<?php echo $row->email;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label for="field-2" class="control-label">Contact No</label>
<span id="error_contact_number" style="color:red;">*</span>
<input placeholder="" data-mask="(999) 999-9999" class="form-control" type="text" name="contact_number" id="contact_number" value="<?php echo $row->contact_number;?>">
</div>
</div>
<div class="col-md-2">
<div class="form-group">
<label>Emergency Contact No.</label>
<span id="error_name" style="color:red;"></span>
<input type="text" class="form-control" name="alternate_number" id="alternate_number" value="<?php echo $row->alternate_number;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Father Name</label>
<span id="error_father_name" style="color:red;"></span>
<input type="text" class="form-control" name="father_name" id="father_name" value="<?php echo $row->father_name;?>">

</div>
</div>
</div>
<div class="row">
<div class="col-md-2" style="display:none;">
<div class="form-group">
<label>Mother Name</label>
<span id="error_mother_name" style="color:red;">*</span>
<input type="text" class="form-control" name="mother_name" id="mother_name" value="<?php echo $row->mother_name;?>">

</div>
</div>

<div class="col-md-2" style="display:none;">
<div class="form-group">
<label>Spouse name </label>

<input type="text" class="form-control" name="spouce_name" id="spouce_name" value="<?php echo $row->spouce_name;?>">

</div>
</div>

<div class="col-md-2" style="display:none;">
<div class="form-group">
<label>Spouse Working in  </label>

<input type="text" class="form-control" name="spouce_working_in" id="spouce_working_in" value="<?php echo $row->spouce_company_name;?>">

</div>
</div>

<div class="col-md-2" style="display:none;">
<div class="form-group">
<label>Children Names </label>

<input type="text" class="form-control" name="children_names" id="children_names" value="<?php echo $row->children_name;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Date of Birth</label>
<span id="error_date_of_birth" style="color:red;">*</span>
<input type="date" class="form-control" name="date_of_birth" id="date_of_birth" placeholder="mm/dd/yyyy" id="date_of_birth" value="<?php echo $row->date_of_birth;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Date of Joining</label>
<span id="error_date_of_joining" style="color:red;">*</span>
<input type="date" class="form-control" name="date_of_joining" id="date_of_joining" value="<?php echo $row->date_of_joining;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Qualification</label>
<span id="error_qualification" style="color:red;"></span>
<input type="text" class="form-control" name="qualification" id="qualification" value="<?php echo $row->qualification;?>">

</div>
</div>
<div class="col-md-2">
<div class="form-group">
<label>Adhar Card</label>
<span id="error_adharcards" style="color:red;"></span>
<div class="row">
<div class="col-md-9">
<input type="file" class="form-control" name="adharcard" id="adharcards" value="<?php echo $row->aadharcard;?>">
</div>
<div class="col-md-3">
<img src="<?php echo user_profile;?>document/<?php echo $row->aadharcard;?>" width="25%" class="img-circle">
</div>
</div>



</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Aadhar Number</label>
<span style="color:red;">*</span>
<input type="text" class="form-control" name="aadhar_number" id="aadhar_number" value="<?php echo $row->aadhar_number;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>PAN Number</label>
<span id="error_pancard" style="color:red;"></span>
<input type="text" class="form-control" name="pancard" id="pancard" value="<?php echo $row->pancard;?>">
</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Blood Group <span style="color:red">*</span></label>
<input type="text" class="form-control" name="blood_group" id="blood_group" value="<?php echo $row->blood_group;?>">
</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Profile Photo</label>
<span id="error_photo" style="color:red;">*</span>
<div class="row">
<div class="col-md-6">
	<input type="file" class="form-control" name="photo" id="photo" value="<?php echo $row->profile_image;?>">
</div>
<div class="col-md-6">
	<img src="<?php echo user_profile;?><?php echo $row->profile_image;?>" width="25%" class="img-circle">
</div>
</div>
</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Employee Code</label>

<input type="text" class="form-control" name="employee_code" id="employee_code" value="<?php echo $row->employee_code;?>">

</div>
</div>
<div class="col-md-2" style="display:none">
<div class="form-group">
<label>Nominee Name</label>

<input type="text" class="form-control" name="nominee_name" id="nominee_name" value="<?php echo $row->nominee_name;?>">

</div>
</div>
<div class="col-md-2" style="display:none">
<div class="form-group">
<label>Date of Leaving</label>

<input type="date" class="form-control" name="date_of_leaving" id="date_of_leaving" value="<?php echo $row->date_of_leaving;?>">

</div>
</div>

<div class="col-md-2" style="display:none">
<div class="form-group">
<label>Basic Salary</label>

<input type="number" class="form-control" name="basic_salary" id="basic_salary" value="<?php echo $row->basic_salary;?>">

</div>
</div>

<div class="col-md-2" style="display:none">
<div class="form-group">
<label>Gross Salary</label>

<input type="number" class="form-control" name="gross_salary" id="gross_salary" value="<?php echo $row->gross_salary;?>">

</div>
</div>


<div class="col-md-2" style="display:none">
<div class="form-group">
<label>CTC</label>

<input type="number" class="form-control" name="ctc" id="ctc" value="<?php echo $row->ctc;?>">

</div>
</div>

<div class="col-md-2" style="display:none">
<div class="form-group">
<label>Joining Letter </label>

<input type="file" class="form-control" name="joining_letter" id="joining_letter" value="">
<input type="hidden" name="old_joining_letter" value="<?php echo $row->joining_letter;?>">
<a href="<?php echo user_profile;?><?php echo $row->joining_letter;?>" download><span class="btn btn-success btn-xs">Click here to download</span></a>

</div>
</div>

<div class="col-md-2" style="display:none">
<div class="form-group">
<label>Relieving Letter </label>

<input type="file" class="form-control" name="relieving_letter" id="relieving_letter" value="">
<input type="hidden" name="old_relieving_letter" value="<?php echo $row->relieving_letter;?>">
<a href="<?php echo user_profile;?><?php echo $row->relieving_letter;?>" download><span class="btn btn-success btn-xs">Click here to download</span></a>

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>Business Location</label>
<span id="error_business_loc" style="color:red;">*</span>
<select class="form-control" id="business_loc" name="business_loc" readonly>

<?php 
$this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1')->where('a.business_loc_id',$business_location);
$this->db->order_by('a.company_name','asc');
$query = $this->db->get();
$res = $query->result();
foreach($res as $business_location){
?>
<option value="<?php echo $business_location->business_loc_id;?>" <?php if($row->business_location==$business_location->business_loc_id){echo "selected";}?>><?php echo $business_location->company_name;?>(<?php echo $business_location->state_name;?>, <?php echo $business_location->city_name;?>)</option>
<?php }?>		
</select>

<script type="text/javascript">

$("#business_loc").change(function(){
var business_loc=$("#business_loc").val();
$.ajax({
type:"post",
url:"<?php echo page_url;?>Master/User_management/select_department",
data:"business_loc="+business_loc,
success:function(data){
$("#department").html(data);
}
});
});

</script>

</div>
</div>


<div class="col-md-2">
<div class="form-group">
<label>Department</label>
<span id="error_department" style="color:red;">*</span>
<select class="form-control" id="department" name="department">
<?php 
$q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->get();
foreach($q->result() as $rowss){
?>
<option value="<?php echo $rowss->department_id;?>" <?php if($row->department_id==$rowss->department_id){echo "selected";}?> ><?php echo $rowss->department;?></option>

<?php }?>

</select>
<script type="text/javascript">

$("#department").change(function(){
var department=$("#department").val();
$.ajax({
type:"post",
url:"<?php echo page_url;?>Master/User_management/select_user_roles",
data:"department="+department,
success:function(data){
$("#user_role").html(data);
}
});
});

</script>
</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label>User Role</label>
<span id="error_user_role" style="color:red;">*</span>
<select class="form-control" id="user_role" name="user_role">
<option value="<?php echo $row->user_role_id;?>"><?php 
$query = $this->db->select(' user_role_id, user_role')->from('user_role')->where('user_role_id',$row->user_role_id)->get();
foreach($query->result() as $user_role){
echo $user_role->user_role;
}
?></option>

</select>

</div>
</div>

</div>



<div class="row">



<div class="col-md-2" style="display:none;">
<div class="form-group">
<label>UIA No.</label>
<input type="text" class="form-control" name="uia_no" id="uia_no" value="<?php echo $row->uia_no;?>">
</div>
</div>

<div class="col-md-2" style="display:none;">
<div class="form-group">
<label>PF No.</label>
<input type="text" class="form-control" name="pf_no" id="pf_no" value="<?php echo $row->pf_no;?>">
</div>
</div>
<div class="col-md-2" style="display:none">
<div class="form-group">
<label>Allowed Payment Grace Period<span id="error_status" style="color:red;">*</span></label>
<input type="number" class="form-control" name="payment_grace_period" id="payment_grace_period" value="<?php echo $row->payment_grace_period;?>" required="">
</div>
</div>
<div class="col-md-2">

<div class="form-group">
<label>Marketing Person?</label> 
<select class="form-control" name="marketing_person" id="marketing_person" onchange="gettargetdate();">
<option value="0" <?php if($row->marketing_person=='0'){echo "selected";}?>>--Select Option--</option>
<option value="1" <?php if($row->marketing_person=='1'){echo "selected";}?>>YES</option>
</select>
</div>

</div>
<?php if($projectCoordinatorFieldExists){ ?>
<?php
$this->db->select('user_id, title, first_name, last_name')->from('system_users');
$this->db->where('business_location', $row->business_location);
$this->db->group_start();
$this->db->group_start();
$this->db->where('hide_profile', '0');
$this->db->where('user_status', '1');
$this->db->group_end();
if($selectedProjectCoordinatorId > 0){
$this->db->or_where('user_id', $selectedProjectCoordinatorId);
}
$this->db->group_end();
$this->db->order_by('first_name', 'asc');
$this->db->order_by('last_name', 'asc');
$projectCoordinatorOptions = $this->db->get()->result();
?>
<div class="col-md-3 project-coordinator-wrapper" style="display:none">
<div class="form-group">
<label>Project Coordinator</label>
<select class="form-control" name="project_coordinator_user_id" id="project_coordinator_user_id">
<option value="">--Select Project Coordinator--</option>
<?php foreach($projectCoordinatorOptions as $projectCoordinatorOption){ ?>
<option value="<?php echo $projectCoordinatorOption->user_id;?>" <?php if($selectedProjectCoordinatorId === (int)$projectCoordinatorOption->user_id){echo "selected";}?>><?php echo trim($projectCoordinatorOption->title." ".$projectCoordinatorOption->first_name." ".$projectCoordinatorOption->last_name);?></option>
<?php }?>
</select>
</div>
</div>
<?php } ?>
<div class="col-md-2 targetwise" style="display:none">
<div class="form-group">
<label>Selling Monthly Target?</label> 
<input type="number" class="form-control" name="sellingtarget" id="sellingtarget" value="<?php echo $row->salestarget;?>">

</div>
</div>
<div class="col-md-2 targetwise" style="display:none">
<div class="form-group">
<label>Payment Weekly Target?</label> 
<input type="number" class="form-control" name="paymenttarget" id="paymenttarget" value="<?php echo $row->paymenttarget;?>">

</div>
</div>

<div class="col-md-2">
<div class="form-group">
<label for="field-2" class="control-label">Status</label>
<span id="error_status" style="color:red;">*</span>
<select class="form-control" id="status" name="status">
<option value="<?php echo $row->user_status;?>">--Select Status--</option>
<option value="1" <?php if($row->user_status=='1'){echo "selected";}?>>Active</option>
<option value="0" <?php if($row->user_status=='0'){echo "selected";}?>>Inactive</option>
</select>
</div>
</div>

<div class="col-md-3" style="display:none;">
<div class="form-group">
<label for="field-2" class="control-label">Permanent Address </label>

<textarea class="form-control" name="address" id="address" style="overflow:hidden;resize:none;"><?php echo $row->address;?></textarea>

</div>
</div>

<div class="col-md-3">
<div class="form-group">
<label for="field-2" class="control-label">Address </label>

<textarea class="form-control" name="present_address" id="present_address" style="overflow:hidden;resize:none;"><?php echo $row->present_address;?></textarea>

</div>
</div>

<div class="col-md-3" style="display:none;">
<div class="form-group">
<label for="field-2" class="control-label">Bank Account Detail </label>

<textarea class="form-control" name="bank_acc_detail" id="bank_acc_detail" style="overflow:hidden;resize:none;"><?php echo $row->bank_acc_detail;?></textarea>

</div>
</div>
<input type="hidden" name="fmsp" value='0'>
</div>
<div class="row">
<div class="col-md-3" style="display:none">
<div class="form-group">
<label>Show in Delegation </label>
<select class="form-control" name="assign_delegation" id="assign_delegation">
<option value="1" <?php if($row->assign_delegation=='1'){echo "selected";}?>>Yes</option>
<option value="0" <?php if($row->assign_delegation=='0'){echo "selected";}?>>No</option>
</select>
</div>
</div>

<div class="col-md-3" style="display:none">
<div class="form-group">
<label>Show in Call Delegation </label>
<select class="form-control" name="assign_call_delegation" id="assign_call_delegation">
<option value="1" <?php if($row->assign_call_delegation=='1'){echo "selected";}?>>Yes</option>
<option value="0" <?php if($row->assign_call_delegation=='0'){echo "selected";}?>>No</option>
</select>
</div>
</div>

<div class="col-md-2" style="display:none">

<div class="form-group">

<label>Conveyance Appl?</label> 

<select class="form-control" name="convence" id="convence" onchange="get_conveyance_done();" required>
<option value="0" <?php if($row->convence==0){?> selected <?php } ?>>NO</option>
<option value="1"  <?php if($row->convence==1){?> selected <?php } ?>>YES</option>
</select>

</div>

</div>



<script>
function get_conveyance_done()
{
$("#ctype").css('display','none');
$("#convence").attr('required',false);
$("#ctype_km").css('display','none');
$("#crate").attr('required',false);
var c=$("#convence").val();

if(c==1)
{
$("#ctype").css('display','');
$("#convence").attr('required',true);
}
}
</script>

</div>




<div class="col-md-2" id="ctype" style="display:none">
<div class="form-group">
<label>Conveyance Type?</label> 
<select class="form-control" name="con_type" id="con_type" onchange="get_km_rate();" >
<option value="0">Select</option>
<option value="1"  <?php if($row->convence_type==1){?> selected <?php } ?>>Km Wise</option>
<option value="2" <?php if($row->convence_type==2){?> selected <?php } ?>>Petrol Wise</option>
</select>
</div>
</div>
<script>
function get_km_rate()
{
$("#ctype_km").css('display','none');
$("#crate").attr('required',false);
var c=$("#con_type").val();
if(c==1)
{
$("#ctype_km").css('display','');
$("#crate").attr('required',true);
}
}
</script>

<div class="col-md-2" id="ctype_km" style="display:none">
<div class="form-group">
<label>Per Km Rate <span style="color: red">*</span></label> 
<input type="number" step="any" name="crate" id="crate" class="form-control" value="<?php echo $row->convence_rate;?>">
</div>
</div>



<?php if($row->user_role_id==1){?>
<div class="col-md-2" style="display:none">

<div class="form-group">
<label>Approvals Appl?</label> 
<select class="form-control" name="approvals" id="approvals" onchange="get_approval_data();" required>
<option value="0" <?php if($row->approvals==0){?> selected <?php } ?>>No</option>
<option value="1" <?php if($row->approvals==1){?> selected <?php } ?>>Yes</option>
</select>

</div>

</div>


<?php 
$app=array();
$row112=$this->db->select('approval_id')->from('approvals_permission')->where('user_id',$id)->get();
if($row112->num_rows()>0)
{
foreach($row112->result() as $rowss)
{
$app[]=$rowss->approval_id;
}

}
?>
<div class="col-md-6 approval_item" style="display:none">

<div class="form-group">
<label>Type of Approvals</label> 
<select class="form-control select2" name="approvals_item[]" id="approvals_item" multiple>
<option value="1" <?php  if(in_array('1',$app)){?> selected<?php } ?>>Spec & MSDS File</option>
<option value="2" <?php  if(in_array('2',$app)){?> selected<?php } ?>>Quotations</option>
<option value="3" <?php  if(in_array('3',$app)){?> selected<?php } ?>>Customer Payment Terms</option>
<option value="4" <?php  if(in_array('4',$app)){?> selected<?php } ?>>User Conveyance Approval</option>
<option value="5" <?php  if(in_array('5',$app)){?> selected<?php } ?>>Payment Closure Approval</option>
<option value="6" <?php  if(in_array('6',$app)){?> selected<?php } ?>>Order Hold Approval</option>
<option value="7" <?php  if(in_array('7',$app)){?> selected<?php } ?>>Bulk Purchase Density Approval</option>
<option value="8" <?php  if(in_array('8',$app)){?> selected<?php } ?>>Equivalent Chart Approval</option>
<option value="9" <?php  if(in_array('9',$app)){?> selected<?php } ?>>Trail Request Assign</option>
</select>

</div>

</div>


<script type="text/javascript">
function get_approval_data()
{
$(".approval_item").css('display','none');
$("#approvals_item").attr('required',false);
var a=$("#approvals").val();

if(a==1)
{
$(".approval_item").css('display','');
$("#approvals_item").attr('required',true);
}

}
</script>
<?php }else
{ ?>
<input type="hidden" name="approvals" value="0">
<?php } ?>


<div class="row">
<div class="col-md-9"></div>
<div class="col-md-3">
<div class="form-group pull-right" style="padding-top:24px;">
<label>&nbsp;</label>
<input type="submit" id="userupdate" class="btn btn-success" value="Update">
</div>
</div>
</div>
</form>
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

<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/detect.js"></script>
<script src="<?php echo assets_url;?>js/fastclick.js"></script>
<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url;?>js/waves.js"></script>
<script src="<?php echo assets_url;?>js/wow.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>
<!-- App js -->
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<?php if($row->user_role_id==1){?>
<script type="text/javascript">
$(document).ready(function(){
get_approval_data();
});
</script>
<?php  } ?>
<script>
$(document).ready(function(){

get_conveyance_done();
get_km_rate();
$("#userupdate").attr('disabled',false);
$("#userupdate").val('Update');
$("#loginForm").on("submit", function(){
// $("#pageloader").fadeIn();
$("#userupdate").attr('disabled',true);
$("#userupdate").val('Please Wait...');
});//submit
});//document ready
</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">   
$(document).ready(function() {

gettargetdate();
});

$(document).ready(function() {

$("#approvals_item").select2();
if($("#project_coordinator_user_id").length)
{
$("#project_coordinator_user_id").select2({
width: '100%',
allowClear: true
});
}

});


$(document).ready(function() {
$("#userupdate").click(function() {
var first_name = $("#first_name").val();
if(first_name=='')
{
$("#error_first_name").html('Required!');
}
var last_name = $("#last_name").val();
if(last_name=='')
{

$("#error_last_name").html('Required!');
}
var email = $("#email").val();
if(email=='')
{

$("#error_email").html('Required!');
}
var contact_number = $("#contact_number").val();
if(contact_number=='')
{

$("#error_contact_number").html('Required!');
}

var date_of_birth = $("#date_of_birth").val();
if(date_of_birth=='')
{

$("#error_date_of_birth").html('Required!');
}
var date_of_joining = $("#date_of_joining").val();
if(date_of_joining=='')
{

$("#error_date_of_joining").html('Required!');
}
var business_loc = $("#business_loc").val();
if(business_loc=='')
{

$("#error_business_loc").html('Required!');
}
var department = $("#department").val();
if(department=='')
{

$("#error_department").html('Required!');
}
var user_role = $("#user_role").val();
if(user_role=='')
{

$("#error_user_role").html('Required!');
}
var address = $("#addressss").val();
if(address=='')
{

$("#error_address").html('Required!');
}
var status = $("#status").val();
if(status=='')
{

$("#error_status").html('Required!');
}


if(first_name=='' || last_name==''|| email=='' || contact_number=='' || father_name=='' || mother_name=='' || date_of_birth=='' || date_of_joining=='' || qualification=='' || adharcards=='' || pancard=='' || business_loc=='' || department==''|| user_role=='' || address=='' || status=='')
{

return false;
}

});
});
</script>
<script>
// Date Picker
jQuery('#date_of_birth').datepicker();
jQuery('#date_of_joining').datepicker();
jQuery('#datepicker-autoclose').datepicker({
autoclose: true,
todayHighlight: true
});


function gettargetdate()
{
var mark=$("#marketing_person").val();
if(mark=='1')
{
$(".targetwise").css('display','');
$(".targetwise :input").attr('required',true);
$(".project-coordinator-wrapper").css('display','');

}else{

$(".targetwise").css('display','none');
$(".targetwise :input").attr('required',false);
$(".project-coordinator-wrapper").css('display','none');
if($("#project_coordinator_user_id").length)
{
$("#project_coordinator_user_id").val('').trigger('change');
}
}

}

</script>



</body>
</html>
