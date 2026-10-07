    <?php 
    $user_id =$this->session->userdata['logged_in']['user_id'];
    $business_location = $this->session->userdata['logged_in']['business_location'];
    $user_role= $this->session->userdata['logged_in']['role'];
    $projectCoordinatorFieldExists = $this->db->field_exists('project_coordinator_user_id', 'system_users');
    ?>
    <!DOCTYPE html>

    <html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright;?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
    <title><?php echo sitetitle;?> Users List</title>
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
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css" />

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

    ment.style {
    }

    .breadcrumb {
    background-color: white;
    margin-bottom: 15px;
    padding-top: 10px;
    padding-left: 113px;
    box-shadow: 1px 1px 10px lightgrey;
    font-size:14px;
    }

    .click{
    padding: 10px;
    }
    .click .copy{
    background-color: rgba(0,0,0,.8);
    padding: 3px;
    border-radius: 12px;
    position: absolute;
    margin-top: -2px;
    width: 80px;
    cursor: pointer;
    color: #f7cc83;
    display: none;
    text-align: center;
    }
    .click:hover .copy{
    display: block;
    }

    .select2-container {
    width: 100% !important;
    }


    </style>

    </head>





    <body>



    <!-- Navigation Bar-->

    <header id="topnav">

    <?php $this->load->view('common/nav-menu');?>

    </header>





    <!-- 

    <div class="container">

    <ul class="bc">
    <li><a href="" class="first">Master</a></li>
    <li><a href="#">User_Management</a></li>

    <li><a href="#" class="current">User Listing</a></li>
    </ul>

    </div>
    -->



    <!-- End Navigation Bar-->



    <?php $this->load->view('common/info-section.php');?>

    <div class="wrapper">

    <div class="container-fluid">



    <!-- Page-Title -->

    <div class="row">

    <div class="col-sm-12" style="margin-top:20px">

    <button class="btn btn-success waves-effect waves-light pull-right" data-toggle="modal" data-target=".bs-example-modal-lg" >ADD NEW USER</button>

    <a href="<?php echo page_url;?>Master/User_management/not_active_employees"><button class="btn btn-danger waves-effect waves-light pull-right" style="margin-right: 10px;">NOT ACTIVE EMPLOYEES</button></a>

    <h4 class="page-title"><?php //echo sitetitle;?> USER MANAGEMENT</h4>

    <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>	

    </div>

    </div>

    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">

    <form id="loginForm" method="post" action="<?php echo page_url;?>Master/User_management/add_new_user" enctype="multipart/form-data">

    <div class="modal-dialog modal-full">

    <div class="modal-content">

    <div class="modal-header">

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

    <h4 class="modal-title">Add New User</h4>

    </div>

    <div class="modal-body">

    <div class="row">

    <div class="col-md-2">

    <div class="form-group">

        <label>First Name</label>

    	<span id="error_first_name" style="color:red;">*</span>

    	<input type="text" class="form-control" name="first_name" id="first_name" value="">



    </div>

    </div>

    <div class="col-md-2">

    <div class="form-group">

        <label>Last Name</label>

    	<span id="error_last_name" style="color:red;">*</span>

    	<input type="text" class="form-control" name="last_name" id="last_name" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Email-ID</label>

    	<span id="error_email" style="color:red;">*</span>

    	<input type="text" class="form-control" name="email" id="email" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

    	 <label for="field-2" class="control-label">Contact No</label>

    	 <span id="error_contact_number" style="color:red;">*</span>

    	 <input placeholder="" data-mask="(999) 999-9999" class="form-control" type="text" name="contact_number" id="contact_number" value="">

    </div>

    </div>

    <div class="col-md-2">

    <div class="form-group">

        <label>Emergency No.</label>

    	<span id="error_name" style="color:red;"></span>

    	<input type="text" class="form-control" name="alternate_number" id="alternate_number" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Father Name</label>
        <input type="text" class="form-control" name="father_name" id="father_name" value="">
    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Mother Name</label>

    	

    	<input type="text" class="form-control" name="mother_name" id="mother_name" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Spouse name </label>



    	<input type="text" class="form-control" name="spouce_name" id="spouce_name" value="">



    </div>

    </div>





    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Spouse Working in </label>



    	<input type="text" class="form-control" name="spouce_working_in" id="spouce_working_in" value="">



    </div>

    </div>

    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Children Names </label>



    	<input type="text" class="form-control" name="children_names" id="children_names" value="">



    </div>

    </div>





    <div class="col-md-2">

    <div class="form-group">

        <label>Date of Birth</label>



    	<input type="date" class="form-control" name="date_of_birth" placeholder="" id="date_of_births" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Date of Joining</label>

    	

    	<input type="date" class="form-control" name="date_of_joining" id="date_of_joining" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Qualification</label>



    	<input type="text" class="form-control" name="qualification" id="qualification" value="">



    </div>

    </div>

    <div class="col-md-2">

    <div class="form-group">

        <label>Adhar Card</label>



    	<input type="file" class="form-control" name="adharcard" id="adharcard" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Aadhar Number</label>



    	<input type="text" class="form-control" name="adhaar_number" id="adhaar_number" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>PAN Number</label>



    	<input type="text" class="form-control" name="pancard" id="pancard" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Blood Group <span style="color:red"></span></label>



    	<input type="text" class="form-control" name="blood_group" id="blood_group" value="">



    </div>

    </div>





    <div class="col-md-2">

    <div class="form-group">

        <label>Profile Photo</label>



    	<input type="file" class="form-control" name="photo" id="photo" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Employee Code</label>



    	<input type="text" class="form-control" name="employee_code" id="employee_code" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Nominee Name</label>



    	<input type="text" class="form-control" name="nominee_name" id="nominee_name" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Date of Leaving</label>



    	<input type="date" class="form-control" name="date_of_leaving" id="date_of_leaving" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Basic Salary</label>



    	<input type="number" class="form-control" name="basic_salary" id="basic_salary" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Gross Salary</label>



    	<input type="number" class="form-control" name="gross_salary" id="gross_salary" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>CTC</label>



    	<input type="number" class="form-control" name="ctc" id="ctc" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Joining Letter </label>



    	<input type="file" class="form-control" name="joining_letter" id="joining_letter" value="">



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Relieving Letter </label>



    	<input type="file" class="form-control" name="relieving_letter" id="relieving_letter" value="">



    </div>

    </div>



    <div class="col-md-2">

    <div class="form-group">

        <label>Plant Unit</label>

    	<span id="error_plant_unit" style="color:red;">*</span>

    	<select class="form-control" id="plant_unit" name="plant_unit">

    <option value="">--Select Plant Unit--</option>

    <option value="1">Sector 59</option>

    <option value="2">Sector 06</option>

    </select>

    </div>

    </div>

    <div class="col-md-2">

    <div class="form-group">

        <label>Business Location</label>

    	<span id="error_business_loc" style="color:red;">*</span>

    	<select class="form-control" id="business_loc" name="business_loc">

    <option value="">--Select Business Location--</option>

    <?php 

    $this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b','a.state_id=b.state_id','left')->join('cities c','a.city_id=c.city_id','left')->where('business_loc_status','1');
    $this->db->where('a.business_loc_id',$business_location);

    $this->db->order_by('a.company_name','asc');

    $query = $this->db->get();

    $res = $query->result();

    foreach($res as $row){

    ?>

    <option value="<?php echo $row->business_loc_id;?>"><?php echo strtoupper($row->company_name);?>(<?php echo strtoupper($row->state_name);?>, <?php echo strtoupper($row->city_name);?>)</option>

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

    <option value="">--Select Department--</option>
    <?php 
        $q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->get();
        foreach($q->result() as $rowss){
    ?>
    <option value="<?php echo $rowss->department_id;?>"><?php echo $rowss->department;?></option>

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

    <option value="">--Select User role--</option>



    </select>



    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>UIA No.</label>

        <input type="text" class="form-control" name="uia_no" id="uia_no" value="">

    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>PF No.</label>

        <input type="text" class="form-control" name="pf_no" id="pf_no" value="">

    </div>

    </div>

    <div class="col-md-2" style="display:none">

    <div class="form-group">

        <label>Allowed Payment Grace Period<span id="error_status" style="color:red;">*</span></label>

        <input type="number" class="form-control" name="payment_grace_period" id="payment_grace_period" value="">

    </div>

    </div>

    <div class="col-md-2">

    <div class="form-group">

    <label for="field-2" class="control-label">Status</label>

    <span id="error_status" style="color:red;">*</span>

    <select class="form-control" id="status" name="status">

    <option value="">--Select Status--</option>

    <option value="1">Active</option>

    <option value="0">Inactive</option>

    </select>

    </div>

    </div>



    <div class="col-md-3" style="display:none">

    <div class="form-group">

    	 <label for="field-2" class="control-label">Permanent Address </label>

    	 

    	<textarea class="form-control" name="address" id="address" style="overflow:hidden;resize:none;"></textarea>

    	 

    </div>

    </div>



    <div class="col-md-3">

    <div class="form-group">

    	 <label for="field-2" class="control-label">Address </label>

    	 

    	 <textarea class="form-control" name="present_address" id="present_address" style="overflow:hidden;resize:none;"></textarea>

    	 

    </div>

    </div>



    <div class="col-md-3" style="display:none">

    <div class="form-group">

    	 <label for="field-2" class="control-label">Bank Account Detail </label>

    	 

    	 <textarea class="form-control" name="bank_acc_detail" id="bank_acc_detail" style="overflow:hidden;resize:none;"></textarea>

    	 

    </div>

    </div>



    <div class="col-md-3" <?php if($business_location==1){?><?php }else{?> style="display:none"<?php }?>>

    <div class="form-group">

    <label for="field-2" class="control-label">FMS Process</label>

    </div>

    </div>

    <div class="col-md-2" style="display:none">

    <div class="form-group">

    <label>Show in Delegation </label>

    <select class="form-control" name="assign_delegation" id="assign_delegation">

        <option value="1">Yes</option>

        <option value="0">No</option>

    </select>

    </div>

    </div>



    <div class="col-md-2" style="display:none">

    <div class="form-group">

    <label>Show in Call Delegation </label>

    <select class="form-control" name="assign_call_delegation" id="assign_call_delegation">

        <option value="1">Yes</option>

        <option value="0">No</option>

    </select>

    </div>

    </div>





    <div class="col-md-2">

    <div class="form-group">

       <label>Marketing Person?</label> 

       <select class="form-control" name="marketing_person" id="marketing_person" onchange="gettargetdate();">

           <option value="0">--Select Option--</option>

           <option value="1">YES</option>

       </select>

    </div>

    </div>

    <?php if($projectCoordinatorFieldExists){ ?>
    <?php
    $projectCoordinatorUsers = $this->db->select('user_id, title, first_name, last_name')
        ->from('system_users')
        ->where('business_location', $business_location)
        ->where('hide_profile', '0')
        ->where('user_status', '1')
        ->order_by('first_name', 'asc')
        ->order_by('last_name', 'asc')
        ->get()
        ->result();
    ?>
    <div class="col-md-3 project-coordinator-wrapper" style="display:none">

    <div class="form-group">

       <label>Project Coordinator</label>

       <select class="form-control" name="project_coordinator_user_id" id="project_coordinator_user_id">
           <option value="">--Select Project Coordinator--</option>
           <?php foreach($projectCoordinatorUsers as $projectCoordinatorUser){ ?>
           <option value="<?php echo $projectCoordinatorUser->user_id;?>"><?php echo trim($projectCoordinatorUser->title." ".$projectCoordinatorUser->first_name." ".$projectCoordinatorUser->last_name);?></option>
           <?php }?>
       </select>

    </div>

    </div>
    <?php } ?>


    <div class="col-md-2" style="display:none">

    <div class="form-group">

       <label>Conveyance Appl?</label> 

       <select class="form-control" name="convence" id="convence" onchange="get_conveyance_done();">
           <option value="0">NO</option>
           <option value="1">YES</option>
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

    <div class="col-md-2" id="ctype" style="display:none">
    <div class="form-group">
       <label>Conveyance Type?</label> 
       <select class="form-control" name="con_type" id="con_type" onchange="get_km_rate();" >
         <option value="0">Select</option>
           <option value="1">Km Wise</option>
           <option value="2">Petrol Wise</option>
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
       <input type="number" name="crate" id="crate" class="form-control" step="any">
    </div>
    </div>






    <div class="col-md-2 targetwise" style="display:none">

    <div class="form-group">

       <label>Selling Monthly Target?</label> 

       <input type="number" class="form-control" name="sellingtarget" id="sellingtarget" value="">

         

    </div>

    </div>







    <div class="col-md-2 targetwise" style="display:none">

    <div class="form-group">

       <label>Payment Weekly Target?</label> 

       <input type="number" class="form-control" name="paymenttarget" id="paymenttarget" value="">

         

    </div>

    </div>



    </div>





    </div>

    <div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

    <input type="submit" id="usersave" class="btn btn-info" value="Submit"> 

    </div>

    </div>

    </div>

    </form>

    </div><!-- /.modal -->





    <div class="row">

    <div class="col-sm-12">

    <div class="card-box table-responsive">

    <div class="row" style="margin-bottom:10px;">

    <div class="col-md-3 col-sm-4 col-xs-12">

    <label for="filter_plant_unit">Plant Unit</label>

    <select class="form-control" id="filter_plant_unit">

    <option value="">All Plant Units</option>

    <option value="Sector 59">Sector 59</option>

    <option value="Sector 06">Sector 06</option>

    </select>

    </div>

    </div>

    <table id="example" class="table table-striped table-bordered dt-responsive nowrap manglesh" cellspacing="0" width="100%">

    <thead>

    <tr>

    <th>Sr No</th>

    <th>Profile Image</th>

    <th>Name</th>

    <?php 

    $user_role =$this->session->userdata['logged_in']['role'];

    if($user_role=='1' || pms_is_super_admin()){

    ?>

    <th>Login as </th>

    <?php }?>

    <th>Email</th>

    <?php if($user_role=='1' || pms_is_super_admin()){?>

    <th>Password</th>

    <?php }?>

    <th>Contact Number</th>

    <!-- <th>Company Name</th> -->

    <th>Department</th>

    <th>Role</th>

    <th class="plant-unit-col">Plant Unit</th>

    <!-- <th>Open Lead/Visit Form</th> -->

    <!-- <th>Payment Form</th> -->

    <th>Generate Code</th>

    <!-- <th>FMS Process</th> -->

    <!-- <th>Send Login Detail</th> -->

    <!-- <th>Customize</th> -->

    <th>Status</th>



    <th>Action</th>



    </tr>

    </thead>

    <tbody>





    </tbody>

    </table>

    </div>

    </div><!-- end col -->

    </div>

    <!-- end row -->





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

    <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- Datatable init js -->

    <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



    <!-- App js -->

    <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

    <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

    <script>
    $(document).ready(function(){
    $("#usersave").attr('disabled',false);
    $("#usersave").val('submit');
    $("#loginForm").on("submit", function(){
    //alert('hesllo submit');
    // $("#pageloader").fadeIn();
    $("#usersave").attr('disabled',true);
    $("#usersave").val('Please Wait...');
    });//submit

    if($("#project_coordinator_user_id").length){
    $("#project_coordinator_user_id").select2({
    width: '100%',
    allowClear: true,
    dropdownParent: $('.bs-example-modal-lg')
    });
    }

    gettargetdate();
    });//document ready
    </script>

    <script>

    $( document ).ready(function() {

    var userTable = $('#example').dataTable({

    "bProcessing": true,
    dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Data export'
            }
        ],

    fixedHeader: true,

    "pagination":true,

    "sAjaxSource": "<?php echo page_url;?>Master/User_management/all_system_users_list",

    "aoColumns": [

    { mData: 'sr_no' } ,

    { mData: 'profile_image' },

    { mData: 'first_name' },

    <?php 

    $user_role =$this->session->userdata['logged_in']['role'];

    if($user_role=='1' || pms_is_super_admin()){

    ?>

    { mData: 'login' },

    <?php }?>

    { mData: 'email' },

    <?php 

    $user_role =$this->session->userdata['logged_in']['role'];

    if($user_role=='1' || pms_is_super_admin()){

    ?>

    { mData: 'password' },

    <?php }?>


    { mData: 'contact_number' },

    // { mData: 'company_name' },

    { mData: 'department' },

    { mData: 'user_role' },

    { mData: 'plant_unit' },

    // { mData: 'open_lead' },

    // { mData: 'cheque_collection' },

    {mData:'generatecode'},

    // { mData: 'fmsp' },

    // { mData: 'notify' },

    // { mData: 'customize' },

    { mData: 'status' },


    { mData: 'edit' }



    ]

    });   

    // Plant Unit filter. The leading columns of this table are conditional on
    // the signed-in role, so resolve the column by its header class rather
    // than hard-coding an index.
    var plantUnitColumn = $('#example thead th').index($('#example thead th.plant-unit-col'));

    $('#filter_plant_unit').change(function(){

    if(plantUnitColumn < 0){ return; }

    var value = $(this).val();

    userTable.fnFilter(value === '' ? '' : '^' + value + '$', plantUnitColumn, true, false);

    });

    });



    </script>

    <script> $(document).ready(function() {

    var date = new Date();

    date.setDate(date.getDate());



    $('.datepicker').datepicker({

    todayHighlight:true



    });

    $("#date_of_birth").datepicker({ dateFormat: "dd-mm-yy" }).val();



    $("#datepicker1").datepicker({

    orientation: 'bottom',

    todayHighlight:true

    });

    //	$('.datepicker').datepicker({todayHighlight:true});

    $("#datepicker1btn").click(function(event) {

    event.preventDefault();

    $("#datepicker1").focus();



    })



    });</script>

    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script language="javascript" type="text/javascript">   

    jQuery.noConflict();

    $(document).ready(function() {

    $("#usersave").click(function() {

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

    var status = $("#status").val();

    if(status=='')

    {



    $("#error_status").html('Required!');

    }







    var plant_unit = $("#plant_unit").val();

    if(plant_unit=='')

    {

    $("#error_plant_unit").html('Required!');

    }

    if(first_name=='' || last_name==''|| email=='' || contact_number=='' || business_loc=='' || department==''|| user_role=='' || status=='' || plant_unit=='')

    {



    return false;

    }



    });

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


    function copyToClipboard(text) {
    var sampleTextarea = document.createElement("textarea");
    document.body.appendChild(sampleTextarea);
    sampleTextarea.value = text; //save main text in it
    sampleTextarea.select(); //select textarea contenrs
    document.execCommand("copy");
    document.body.removeChild(sampleTextarea);
    }

    function myFunction(user_id){
    var copyText = document.getElementById("myInput"+user_id);
    copyToClipboard(copyText.value);
    }

    function myFunctions(user_id){
    var copyText = document.getElementById("myCollection"+user_id);
    copyToClipboard(copyText.value);
    }

    </script>

    <script>

    function swithuser(path){

    var windowFeatures = "menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes";

    var urlinfo = "<?php echo page_url;?>User/access_user_dashboard/"+path;

    window.open(urlinfo,'userlogin',windowFeatures);

    //chrome.windows.create({"url": urlinfo, "incognito": true});

    //windows.create({"url": urlinfo, "incognito": true});

    return false;

    }

    </script>

    </body>

    </html>
