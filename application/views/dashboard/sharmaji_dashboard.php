    <?php
    $is_admin = 0;
    $user_role= $this->session->userdata['logged_in']['role'];
    $CI =& get_instance();
    $CI->load->model('Dashboard_model');
    $adminuserrole = $CI->Dashboard_model->getsuperadminuserole();
    if(count($adminuserrole)>0){
    if(in_array($user_role,$adminuserrole)){
    $is_admin = 1;
    $_SESSION['logged_in']['adminuser'] = 1;
    }else{
    $_SESSION['logged_in']['adminuser'] = 0;
    }
    }
    $CIA =& get_instance();
    $CIA->load->model('Task_model');
    $runningdfno = $CIA->Task_model->getallrunningdf();

    if($is_admin==0){
    $department = $this->session->userdata['logged_in']['department_id'];
    $departmentid = array();
    $user_id =$this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
    if($q->num_rows()>0){
    foreach($q->result() as $row){
    $departmentid[] =$row->department_id;
    //HOD 
    $_SESSION['logged_in']['adminuser'] = 2;
    }
    }else{
    // normal user
    $_SESSION['logged_in']['adminuser'] = 3;
    }

    }

    ?>
    <!DOCTYPE html>
    <html>

    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?>Payment Terms</title>
    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="
    https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.css
    " rel="stylesheet">
    <link href="<?php echo dashboard_asset_url; ?>dashboard.css" rel="stylesheet" type="text/css">


    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>

    </style>
    <?PHP

    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

    foreach ($q->result() as $LOGO);

    ?>

    </head>


    <body>


    <!-- Navigation Bar-->
    <header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
    <div class="container-fluid">



    <div class="row">
    <div class="col-sm-12">

    <div class="row">
    <?php echo $this->session->flashdata('message'); ?>
    <h3 class="text-center"> <?php //$q = $this->db->select('department_id, department')->from('departments')->where_in('department_id',$departmentid)->get();
    //foreach($q->result() as $row);
    //echo strtoupper($row->department)." DEPARTMENT";
    ?></h3><hr>
    <?php 
    $oneweek = date('Y-m-d',strtotime('-7 days'));
    ?>
    <div class="col-md-10">
    <!-- <a href="<?php echo page_url;?>MIS/index/ALL/<?php echo $oneweek;?>/<?php echo date('Y-m-d');?>/ALL"><span class="btn btn-primary btn-xs">MIS REPORT</span></a> -->
    </div>
    <div class="col-md-2">

    <a href="<?php echo page_url;?>Task/poreceived" class="pull-right btn btn-success btn-xs"> ADD PO <i class="fa fa-plus"></i></a>
    </div>
    <!--  <?php 
    //$q = $this->db->select('task_id, task_name')->from('task_management')->where_in('department_id',$departmentid,false)->order_by('sortorder','ASC')->get();
    // if($q->num_rows()>0){
    // foreach($q->result() as $row){
    ?>

    <div class="col-sm-3">
    <div class="main__box" style="border: 1px solid red; min-height: 103px;">
    <div class="row" style="align-items: center;">
    <div class="col-sm-12">
    <h2 class="num_blue">21</h2>
    </div>
    <div class="col-sm-12">
    <p style="line-height: 15px; text-align: center;"><?php echo $row->task_name;?></p>
    </div>
    </div>


    </div>
    </div>
    <?php //}}?> -->
    </div>
    <div class="main__box">
    <div class="tab">
    <div class="row">
    <?php 
    if($_SESSION['logged_in']['adminuser']==1 || $_SESSION['logged_in']['adminuser']==2){
    $default1="defaultOpen";
    $default2='';
    }else
    {
    $default2="defaultOpen";
    $default1='';

    }
    if($_SESSION['logged_in']['adminuser']==1 || $_SESSION['logged_in']['adminuser']==2){ ?>
    <div class="col-sm-3">
    <button class="tablinks" onclick="openCity(event, 'unassigned')" id="<?php echo $default1;?>"><img src="<?php echo assets_url; ?>task.png" alt=""> UN-ASSIGNED DF<span class="counter" style="background-color:orange;font-weight: bold;color:white;" id="unassignednotificationcount"></span></button>
    </div>
    <?php } ?>

    <div class="col-sm-3">
    <button class="tablinks" onclick="openCity(event, 'going')" id="<?php echo $default2;?>"><img src="<?php echo assets_url; ?>task.png" alt="">ON GOING TASKS<span class="counter" style="background-color:orange;font-weight: bold;color:white;" id="ongoingtaskcountnotification"></span></button>
    </div>
    <div class="col-sm-3">
    <button class="tablinks" onclick="openCity(event, 'overdue')"><img src="<?php echo assets_url; ?>overdue.png" alt="">OVERDUE TASKS<span class="counter" style="background-color:orange;font-weight: bold;color:white;" id="overduetaskcountnotification"></span></button>
    </div>
    <div class="col-sm-3">
    <button class="tablinks" onclick="openCity(event, 'complete')"><img src="<?php echo assets_url; ?>complete.png" alt="">COMPLETED TASKS<span class="counter" style="background-color:orange;font-weight: bold;color:white;" id="completeddfnotificationcount"></span></button>
    </div>

    </div>
    </div>
    <?php 
    if($_SESSION['logged_in']['adminuser']==1 || $_SESSION['logged_in']['adminuser']==2){ ?>
    <div id="unassigned" class="tabcontent">
    <div class="table__header">
    <table id="example4" class="table table-striped pretty">
    <thead>
    <tr>
    <th>S. NO.</th>
    <th>DF NO.</th>
    <th>DF RELEASE<br> DATE</th>
    <th>DOWNLOAD DF</th>
    <th>ASSIGNMENT</th>

    </tr>
    </thead>
    <tbody>
    </tbody>
    </table>
    </div>
    </div>
    <?php } ?>
    <div id="going" class="tabcontent">
    <div class="row" style="margin-top:20px;">

    <div class="col-md-12" style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

    <div class="col-md-3">
    <div class="form-group">
    <label style="color:#000;">FILTER BY DF NO.</label>
    <select class="form-control" id="task_dfno_filter" onchange="filter_ongoing_task();">
    <option value="ALL">ALL</option>
    <?php if(count($runningdfno)>0){
    foreach($runningdfno as $row){?>
    <option value="<?php echo $row->id;?>"><?php echo strtoupper($row->df_no);?></option>
    <?php  }
    }?>
    </select>
    </div>
    </div>

    <div class="col-md-3">
    <div class="form-group">
    <label style="color:#000;">FILTER BY (DUE DATE)</label>
    <select class="form-control" id="task_date_filter" onchange="filter_ongoing_task();">
    <option value="ALL">ALL</option>
    <option value="1">TASK DUE TODAY</option>
    <option value="2">TASK DUE THIS WEEK</option>
    </select>
    </div>
    </div>

    <div class="col-md-3">
    <div class="form-group">
    <label style="color:#000;">FILTER BY DEPARTMENT</label>
    <select class="form-control" id="task_department_filter" onchange="filter_ongoing_task(); getusers();">
    <?php if($_SESSION['logged_in']['adminuser']==3){}else{?> 
    <option value="ALL">ALL</option>
    <?php }?>
    <?php 
    $leaderdepartment[] = 0;
    $q33 = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
    if($q33->num_rows()>0){
    foreach($q33->result() as $rowssss){
    $leaderdepartment[] = $rowssss->department_id;
    }
    }
    $this->db->select('department, department_id')->from('departments')->where('status',1)->where('department_id!=',10);
    if($_SESSION['logged_in']['adminuser']==3){
    $departmentid = $_SESSION['logged_in']['department_id'];
    $this->db->where('department_id',$departmentid);
    }
    if($_SESSION['logged_in']['adminuser']==2 && $leaderdepartment<>0){


    $this->db->where_in('department_id',$leaderdepartment,false);
    }

    $q = $this->db->where('business_loc_id',2)->order_by('department','ASC')->get();
    foreach($q->result() as $ros){?>
    <option value="<?php echo $ros->department_id;?>"><?php echo strtoupper($ros->department);?></option>
    <?php }?>

    </select>
    </div>
    </div>

    <script type="text/javascript">
    function getusers() {
    var department = $("#task_department_filter").val();
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/getusersofdepartment",
    data:"department="+department,
    success:function(data){
    $("#task_user_filter").html(data);
    }
    });
    }
    </script>
    <div class="col-md-3">
    <div class="form-group">
    <label style="color:#000;">FILTER BY USERS</label>
    <select class="form-control" id="task_user_filter" onchange="filter_ongoing_task();">
    <?php
    $user_id =$this->session->userdata['logged_in']['user_id']; 
    if($_SESSION['logged_in']['adminuser']==3){
    $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $rowss);
    ?>
    <option value="<?php echo $rowss->user_id;?>"><?php echo strtoupper($rowss->first_name." ".$rowss->last_name);?></option>
    <?php }else{?>
    <option value="ALL">ALL</option>
    <?php }?>
    </select>
    </div>
    </div>

    </div>

    </div>
    <div class="table__header">
    <table id="example" class="table table-striped pretty">
    <thead>
    <tr>
    <th>S. NO.</th>
    <th>DF NO.</th>
    <th>DOWNLOAD DF</th>
    <th>DF RELEASE<br>DATE</th>
    <th>DEPARTMENT</th>
    <th>ACCOUNTABLE PERSON</th>
    <th>TASK NAME</th>
    <th>TAT</th>
    <th>TIME REMAINING</th>
    <th>TASK STATUS UPDATE</th>
    <th>LAST REMARKS<br>(If Any)</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
    </table>
    </div>
    </div>



    <div id="overdue" class="tabcontent">

    <div class="row" style="margin-top:20px;">

    <div class="col-md-12" style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

    <div class="col-md-2">
    <div class="form-group">
    <label style="color:#000;">FILTER BYDF NO.</label>
    <select class="form-control" id="task_dfno_filter_overdue" onchange="filter_overdue_task();">
    <option value="ALL">ALL</option>
    <?php if(count($runningdfno)>0){
    foreach($runningdfno as $row){?>
    <option value="<?php echo $row->id;?>"><?php echo strtoupper($row->df_no);?></option>
    <?php  }
    }?>
    </select>
    </div>
    </div>

    <div class="col-md-2">
    <div class="form-group">
    <label style="color:#000;">FILTER BY</label>
    <select class="form-control" id="task_number_filter_overdue" onchange="filter_overdue_task(); checkother();">
    <option value="ALL">ALL</option>
    <option value="1">OTHER</option>
    </select>
    </div>
    </div>
    <script type="text/javascript">
    function  checkother() {
    var task_number_filter_overdue = $("#task_number_filter_overdue").val();
    if(task_number_filter_overdue==1){
    $("#showno").show();
    }else{
    $("#showno").hide();
    }
    }
    </script>
    <div class="col-md-2" style="display: none;" id="showno">
    <div class="form-group">
    <label style="color:#000;">NO OF DAYS</label>
    <input type="number" class="form-control" id="noofdaysdue" value="2" onblur="filter_overdue_task();">
    </div>
    </div>

    <div class="col-md-3">
    <div class="form-group">
    <label style="color:#000;">FILTER BY DEPARTMENT</label>
    <select class="form-control" id="task_department_filter_overdue" onchange="filter_overdue_task(); getusersss();">
    <?php if($_SESSION['logged_in']['adminuser']==3){}else{?> 
    <option value="ALL">ALL</option>
    <?php }?>
    <?php 
    $leaderdepartment[] = 0;
    $q33 = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
    if($q33->num_rows()>0){
    foreach($q33->result() as $rowssss){
    $leaderdepartment[] = $rowssss->department_id;
    }
    }
    $this->db->select('department, department_id')->from('departments')->where('status',1)->where('department_id!=',10);
    if($_SESSION['logged_in']['adminuser']==3){
    $departmentid = $_SESSION['logged_in']['department_id'];
    $this->db->where('department_id',$departmentid);
    }
    if($_SESSION['logged_in']['adminuser']==2 && $leaderdepartment<>0){


    $this->db->where_in('department_id',$leaderdepartment,false);
    }

    $q = $this->db->where('business_loc_id',2)->order_by('department','ASC')->get();
    foreach($q->result() as $ros){?>
    <option value="<?php echo $ros->department_id;?>"><?php echo strtoupper($ros->department);?></option>
    <?php }?>

    </select>

    </div>
    </div>

    <script type="text/javascript">
    function getusers() {
    var department = $("#task_department_filter").val();
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/getusersofdepartment",
    data:"department="+department,
    success:function(data){
    $("#task_user_filter").html(data);
    }
    });
    }

    function getusersss() {
    var department = $("#task_department_filter_overdue").val();
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/getusersofdepartment",
    data:"department="+department,
    success:function(data){
    $("#task_user_filter_overdue").html(data);
    }
    });
    }
    </script>
    <div class="col-md-3">
    <div class="form-group">
    <label style="color:#000;">FILTER BY USERS</label>
    <select class="form-control" id="task_user_filter_overdue" onchange="filter_overdue_task();">
    <?php
    $user_id =$this->session->userdata['logged_in']['user_id']; 
    if($_SESSION['logged_in']['adminuser']==3){
    $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $rowss);
    ?>
    <option value="<?php echo $rowss->user_id;?>"><?php echo strtoupper($rowss->first_name." ".$rowss->last_name);?></option>
    <?php }else{?>
    <option value="ALL">ALL</option>
    <?php }?>
    </select>
    </div>
    </div>

    </div>

    </div>
    <div class="table__header">
    <table id="example2" class="table table-striped pretty">
    <thead>
    <tr>
    <th>S. NO.</th>
    <th>DF NO.</th>
    <th>DOWNLOAD DF</th>
    <th>DF RELEASE<br>DATE</th>
    <th>DEPARTMENT</th>
    <th>ACCOUNTABLE PERSON</th>
    <th>TASK NAME</th>
    <th>TAT</th>
    <th>TIME EXCEEDED</th>
    <th>UPDATE PROGRESS</th>
    <th>LAST REMARKS<br>(If Any)</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
    </table>

    </div>
    </div>


    <div id="complete" class="tabcontent">
    <div class="row" style="margin-top:20px;">

    <div class="col-md-12" style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

    <div class="col-md-4">
    <div class="form-group">
    <label style="color:#000;">FILTER BY DF NO.</label>
    <select class="form-control" id="task_dfno_filter_completed" onchange="filter_completed_task();">
    <option value="ALL">ALL</option>
    <?php if(count($runningdfno)>0){
    foreach($runningdfno as $row){?>
    <option value="<?php echo $row->id;?>"><?php echo strtoupper($row->df_no);?></option>
    <?php  }
    }?>
    </select>
    </div>
    </div>


    <div class="col-md-4">
    <div class="form-group">
    <label style="color:#000;">FILTER BY DEPARTMENT</label>
    <select class="form-control" id="task_department_filter_completed" onchange="filter_completed_task(); getuserscompleted();">
    <?php if($_SESSION['logged_in']['adminuser']==3){}else{?> 
    <option value="ALL">ALL</option>
    <?php }?>
    <?php 
    $leaderdepartment[] = 0;
    $q33 = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
    if($q33->num_rows()>0){
    foreach($q33->result() as $rowssss){
    $leaderdepartment[] = $rowssss->department_id;
    }
    }
    $this->db->select('department, department_id')->from('departments')->where('status',1)->where('department_id!=',10);
    if($_SESSION['logged_in']['adminuser']==3){
    $departmentid = $_SESSION['logged_in']['department_id'];
    $this->db->where('department_id',$departmentid);
    }
    if($_SESSION['logged_in']['adminuser']==2 && $leaderdepartment<>0){


    $this->db->where_in('department_id',$leaderdepartment,false);
    }

    $q = $this->db->where('business_loc_id',2)->order_by('department','ASC')->get();
    foreach($q->result() as $ros){?>
    <option value="<?php echo $ros->department_id;?>"><?php echo strtoupper($ros->department);?></option>
    <?php }?>

    </select>
    </div>
    </div>

    <script type="text/javascript">
    function getuserscompleted() {
    var department = $("#task_department_filter_completed").val();
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/getusersofdepartment",
    data:"department="+department,
    success:function(data){
    $("#task_user_filter_completed").html(data);
    }
    });
    }
    </script>
    <div class="col-md-4">
    <div class="form-group">
    <label style="color:#000;">FILTER BY USER</label>
    <select class="form-control" id="task_user_filter_completed" onchange="filter_completed_task();">
    <?php
    $user_id =$this->session->userdata['logged_in']['user_id']; 
    if($_SESSION['logged_in']['adminuser']==3){
    $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $rowss);
    ?>
    <option value="<?php echo $rowss->user_id;?>"><?php echo strtoupper($rowss->first_name." ".$rowss->last_name);?></option>
    <?php }else{?>
    <option value="ALL">ALL</option>
    <?php }?>
    </select>
    </div>
    </div>

    </div>

    </div>
    <div class="table__header">
    <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6"> 
    <table style="width:100%;border:none !important;"><thead><tr>
    <th style="width:33%;border:none"><span class="colour-box c1">&nbsp;</span>&nbsp;ON TIME</th>
    <th style="width:33%;border:none"><span class="colour-box c2">&nbsp;</span>&nbsp;DELAYED</th>
    <th style="width:33%;border:none"><span class="colour-box c3">&nbsp;</span>&nbsp;EARLY</th>
    </tr>
    </thead>
    </table>
    </div>
    </div>
    <table id="example3" class="table pretty2">
    <thead>
    <tr>
    <th>S. NO.</th>
    <th>DF No.</th>
    <th>DOWNLOAD DF</th>
    <th>DF RELEASE<br> DATE</th>
    <th>DEPARTMENT</th>
    <th>ACCOUNTABLE PERSON</th>
    <th>TASK NAME</th>
    <th>PLANNED COMPLETION DATE</th>
    <th>ACTUAL COMPLETION DATE</th>
    <th>DELAY</th>
    <th>STATUS</th>
    <th>REMARKS</th>
    <th>COMPLETED BY</th>

    </tr>
    </thead>
    <tbody>
    </tbody>
    </table>

    </div>

    </div>

    </div>
    <?php 
    if($this->session->userdata['logged_in']['role']==12){
    $q = $this->db->select('id, df_no, added_on')->from('df_release')->where('df_status',0)->get();
    if($q->num_rows()>0){
    foreach($q->result() as $rows){

    $q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
    if($q1->num_rows()>0){
    foreach($q1->result() as $r);
    $planneddate = date('d-m-Y',strtotime($r->enddate));
    }else{
    $planneddate = '';
    }

    $q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
    $count = $q1->num_rows();
    $delaycount = array();
    $delaycount[] = 0;
    $totaldayscountarray = array();
    $totaldayscountarray[] =0;
    $q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->where('task_status',1)->get();
    $totaldone = $q2->num_rows();
    $percetage =  round($totaldone*100/ $count);

    foreach($q2->result() as $rowss){
    if(date('Y-m-d',strtotime($rowss->task_completed_on))>$rowss->end_date){
    $delaycount[] = 1;

    $daysss = $CIA->Task_model->getDays($rowss->end_date, date('Y-m-d',strtotime($rowss->task_completed_on)),1);

    $totaldayscountarray[] = $daysss;

    }



    }
    $delayed = array_sum($delaycount);
    $totaldaysdelayed = array_sum( $totaldayscountarray);

    $totaldelayedpercentage = round($delayed*100/$totaldone);

    $expecteddate =  date('d-m-Y',strtotime($planneddate.' +'.$totaldaysdelayed.' Days'));
    $skipped_dates = $CIA->Task_model->SKIPsingle_holidays($expecteddate);

    ?>
    <div class="col-sm-4 col-md-4 col-lg-3">

    <a href="<?php echo page_url;?>Task/dfgantchartNew/<?php echo $rows->id;?>" target="_blank">
    <div class="report-box">
    <div class="text-center">
    <h5>DF NO. <?php echo strtoupper($rows->df_no);?></h5><hr>
    </div>

    <h5 class="mt-0">WORK COMPLETED <span class="text-primary float-end"><?php echo  $percetage;?>%</span></h5>
    <div class="progress progress-bar-alt-primary progress-sm mt-0 mb-3">
    <div class="progress-bar bg-primary progress-animated wow animated animated" role="progressbar" aria-valuenow="<?php echo  $percetage;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo  $percetage;?>%; visibility: visible; animation-name: animationProgress;">
    </div><!-- /.progress-bar .progress-bar-danger -->
    </div><!-- /.progress .no-rounded -->

    <h5 class="mt-0">WORK DELAYED <span class="text-danger float-end"><?php echo  $totaldelayedpercentage;?>%</span></h5>
    <div class="progress progress-bar-alt-danger progress-sm mt-0 mb-3">
    <div class="progress-bar bg-danger progress-animated wow animated animated" role="progressbar" aria-valuenow="<?php echo $totaldelayedpercentage;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $totaldelayedpercentage;?>%; visibility: visible; animation-name: animationProgress;">
    </div><!-- /.progress-bar .progress-bar-warning -->
    </div><!-- /.progress .no-rounded -->
    <div class="row">
    <div class="col-md-6"><p>PLANNED DATE <br><?php echo $planneddate;?></p></div>
    <div class="col-md-6"> <p>EXPECTED DATE <br><?php echo date('d-m-Y',strtotime($skipped_dates));?></p></div>
    </div>






    </div>
    </a>
    </div>


    <?php } }?>
    <?php }?>
    </div>

    <div class="col-sm-3" style="display:none">
    <div class="main__box" style="overflow-y:scroll;height: 400px;background-color: whitesmoke;">
    <div class="reminder">
    <h6><i class="fa fa-bell"></i>&nbsp;MISSED </h6>
    </div>

    <?php 
    //$q = $this->db->select('')->from('task_department_wise_scheduling')->join('')->where('')->get();

    for($i=0;$i<1;$i++)
    {
    $back="#d0fffe";
    ?>
    <div class="read_box">
    <div class="top_box" style="background-color:<?php echo $back;?>">
    <p><i>UPCOMING DF MEETING FOR Upcoming DF Meeting For DF-1200 On 4Th March 2024 at 14:00 PM</i></p>
    <!-- <div class="time_btn">
    <div class="row" style="align-items: right;">


    <p>10:00Am</p>

    </div>

    </div> -->
    </div>

    </div>
    <?php } ?>

    </div>
    <!-- <div class="main__box" style="overflow-y:scroll;height: 400px;background-color: whitesmoke;">
    <div class="reminder">
    <h6><i class="fa fa-bullhorn"></i> Notifications</h6>
    </div>

    <div class="read_box" id="showlivenotifications">


    </div>


    </div> -->
    </div>

    <!-- <div class="col-md-12 card-box" style="background-color:aliceblue;">
    <div class="col-md-12">
    <h3 class="page-title text-center" style="font-weight: 600;color:black;">DF Task Progress</h3>
    </div>
    <div class="col-md-12">
    <div class="col-md-3"></div>
    <div class="col-md-6">
    <div class="form-group">
    <select class="form-control">
    <option value="1200">DF 1200</option>
    <option value="1201">DF 1201</option>

    </select>
    </div>
    </div>
    </div>
    <div class="col-md-12"><div id="chart-container"></div></div>

    </div> -->
    </div>


    <div id="updateprogress" class="modal fade" role="dialog">
    <form id="updateprogressform" method="post" action="<?php echo page_url;?>Task/updatetaskremarks"  enctype="multipart/form-data">
    <div id="pageloader1">
    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
    </div>
    <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title" style="font-weight: bold; text-align:center;">Update Task Status</h4>
    </div>
    <div class="modal-body">
    <div class="row">

    <div class="col-md-12">
    <input type="hidden" id="taskkiid" value="" name="taskkiid">
    <input type="hidden" id="mastertaskid" value="" name="mastertaskid">
    <input type="hidden" id="shortorder" value="" name="shortorder">
    <input type="hidden" id="progressdfno" value="" name="progressdfno">
    <div class="form-group">
    <label>Status <span style="color:red" id="error_taskstatus">*</span></label>
    <select name="taskstatus" id="taskstatus" onchange="checkifpendingdue();" class="form-control" required>
    <option value="1">Done</option>
    <option value="0">Pending</option>
    <option value="2">Send to Previous Step</option>
    </select>
    </div>
    </div>
    </div>
    <div class="row" style="display:none" id="sendtopreviousstep">

    <div class="col-md-12">
    <div class="form-group">
    <label>Send Previous Step</label>
    <select class="form-control" name="previousstep" id="previousstep">

    </select>
    </div>
    </div>
    </div>
    <div class="row">

    <div class="col-md-12">
    <div class="form-group">
    <label>Remarks <span style="color:red" id="error_taskremarks"></span></label>
    <textarea name="taskremarks" id="taskremarks" class="form-control"></textarea>
    </div>
    </div>
    </div>



    <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
    <input type="submit" style="width: 100%;" name="" onclick="taskupdationvalidation();" value="Submit" class="btn btn-success">
    </div>
    </div>

    </div>
    <!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div> -->
    </div>

    </div>
    </form>
    </div>

    <div id="updateprogress1" class="modal fade" role="dialog">
    <form id="updateprogressform" method="post" action="<?php echo page_url;?>Task/reassignselectedtask"  enctype="multipart/form-data">
    <div id="pageloader1">
    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
    </div>
    <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title" style="font-weight: bold; text-align:center;">Reassign Task</h4>
    <h5 style="text-align:center; font-weight:bold;" id="currenctlyassigned"></h5>
    </div>
    <div class="modal-body">
    <div class="row">

    <div class="col-md-12">
    <input type="hidden" id="selectedtasktoreassign" value="" name="selectedtasktoreassign">
    <div class="form-group">
    <label>Reassign to  <span style="color:red" id="error_taskstatus">*</span></label>
    <select name="showuserstoreassign" id="showuserstoreassign" class="form-control" required>

    </select>
    </div>
    </div>
    <div class="col-md-12">
    <div class="form-group">
    <label>Remarks</label>
    <textarea class="form-control" name="predefinedmessage" id="predefinedmessage"></textarea>
    </div>
    </div>
    </div>

    <div class="row">

    <div class="col-md-12">
    <div class="form-group">
    <label>Remarks <span style="color:red" id="error_taskremarks"></span></label>
    <textarea name="reassignremarks" id="reassignremarks" class="form-control"></textarea>
    </div>
    </div>
    </div>



    <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
    <input type="submit" style="width: 100%;" name="" value="Submit" class="btn btn-success">
    </div>
    </div>

    </div>
    <!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div> -->
    </div>

    </div>
    </form>
    </div>


    <script type="text/javascript">
    function checkifpendingdue(){
    var taskstatus = $("#taskstatus").val(); 
    var sortorder = $("#shortorder").val();
    if(taskstatus==2){
    $("#sendtopreviousstep").show();
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/getallprevioussteps",
    data:"sortorder="+sortorder,
    success:function(data){
    $("#previousstep").html(data);
    }
    });
    $("#taskremarks").attr('Required',true);
    }else if(taskstatus==0){
    $("#taskremarks").attr('Required',true);
    }else{
    $("#sendtopreviousstep").hide();
    }
    }
    </script>

    <div id="dfmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

    <form id="loginForm" method="post" action="<?php echo page_url;?>Task/dfrelease"  enctype="multipart/form-data">
    <div id="pageloader">
    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
    </div>
    <input type="hidden" name="dfrecordid" id="dfrecordid" value="">
    <input type="hidden" name="poid" id="poid" value="">

    <div class="modal-dialog">

    <div class="modal-content">

    <div class="modal-header">

    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

    <h4 class="modal-title">Release DF</h4>

    </div>

    <div class="modal-body">

    <div class="row">

    <div class="col-md-6">
    <div class="form-group">
    <label for="field-1" class="control-label">Upload DF</label>
    <span id="error_uploaddf" style="color:red;">*</span>
    <input type="file" class="form-control" name="uploaddf" id="uploaddf">
    </div>
    </div>

    <div class="col-md-6">
    <div class="form-group">
    <label for="field-1" class="control-label">DF No.</label>
    <span id="error_dfno" style="color:red;">*</span>
    <input type="text" class="form-control" name="dfno" id="dfno" value="">

    </div>
    </div>
    <div class="col-md-12">
    <div class="form-group">
    <label>DF Description <span id="error_df_description" style="color:red;">*</span></label>

    <input type="text" class="form-control" id="df_description" name="df_description" value="" required>
    </div>
    </div>
    </div>

    </div>

    <div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

    <input type="submit" id="savedata" class="btn btn-info" value="Submit"> 

    </div>

    </div>



    </form>

    </div><!-- /.modal -->




    <!-- Footer -->
    <?php $this->load->view('common/footer'); ?>
    <!-- End Footer -->

    </div> <!-- end container -->
    </div>
    <!-- end wrapper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <!-- jQuery  -->
    <!-- <script src="<?php echo assets_url; ?>js/jquery.min.js"></script> -->
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

    <!-- Datatables-->

    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>




    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>




    <script>
    $(document).ready(function() {
    var a=$("#task_date_filter").val();

    $('#example').dataTable({

    "bProcessing": true,
    "pagination":true,
    "sAjaxSource": "<?php echo page_url;?>Task/ongoingtasklist/",

    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'dfupload' } ,
    { mData: 'df_release_date' } ,
    { mData: 'department' } ,
    { mData: 'membername' } ,
    { mData: 'task_name' } ,
    { mData: 'end_date' }, 
    { mData: 'pendingdays' }, 
    { mData: 'updateprogress' },
    { mData: 'remarks' }, 
    ]

    });

    $('#example2').dataTable({

    "bProcessing": true,
    "pagination":true,
    "sAjaxSource": "<?php echo page_url;?>Task/outdatedtask",

    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'dfupload' } ,
    { mData: 'df_release_date' } ,
    { mData: 'department' } ,
    { mData: 'membername' } ,
    { mData: 'task_name' } ,
    { mData: 'end_date' }, 
    { mData: 'pendingdays' }, 
    { mData: 'updateprogress' },
    { mData: 'remarks' }


    ]

    });

    $('#example3').dataTable({
    "bProcessing": true,
    "pagination":true,
    scrollX: true,
    "sAjaxSource": "<?php echo page_url;?>Task/completeddf",
    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'dfupload' } ,
    { mData: 'df_release_date' } ,
    { mData: 'department' } ,
    { mData: 'membername' } ,
    { mData: 'taskname' } ,
    { mData: 'tasktat' } ,
    { mData: 'addedon' },
    { mData: 'taskdelay' } ,
    { mData: 'status' } ,
    { mData: 'remarks' } ,
    { mData: 'addedby' }
    ],
    "initComplete": function(settings, json) {

    getcolors()
    }

    });


    $('#example').on('draw.dt', function() {
    // do action here

    getcolors();
    });

    $('#example').on('search.dt', function() {

    getcolors();
    });

    <?php 
    if($_SESSION['logged_in']['adminuser']==1 || $_SESSION['logged_in']['adminuser']==2){ ?>
    $('#example4').dataTable({
    "bProcessing": true,
    "pagination":true,
    "sAjaxSource": "<?php echo page_url;?>Task/pendingtoassigndf",
    "aoColumns": [

    { mData: 'sr_no' } ,
    { mData: 'df_no' } ,
    { mData: 'df_release_date' } ,
    { mData: 'downloaddf' },
    { mData: 'assigment' }


    ]

    });
    <?php } ?>
    });


    // function getcolors() {
    // $("#example3 tr").each(function() {
    // var currentRow = $(this);
    // var col1_value = currentRow.find("td:eq(10)").text();
    //  if (col1_value == 'On Time') {
    //                     currentRow.addClass("c11");
    //                 } else if (col1_value == 'Delayed') {
    //                     currentRow.addClass("c22")
    //                 } else if (col1_value == 'Early') {
    //                     currentRow.addClass("c33")
    //                 } 



    // });
    // }
    </script>

    <script>
    function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
    }
    document.getElementById("defaultOpen").click();
    </script>

    <script>

    //setInterval('updateTimer()', 1000);


    function updateTimer1() {
    future = Date.parse("April 22, 2024 11:30:00");
    now = new Date();
    diff =  now-future;

    days = Math.floor(diff / (1000 * 60 * 60 * 24));
    hours = Math.floor(diff / (1000 * 60 * 60));
    mins = Math.floor(diff / (1000 * 60));
    secs = Math.floor(diff / 1000);

    d = days;
    h = hours - days * 24;
    m = mins - hours * 60;
    s = secs - mins * 60;

    document.getElementById("timer1")
    .innerHTML =
    '<div>' + d + '<span> Days </span>' + m + '<span> min</span></div>' +
    // '<div>' + h + '</div>' +
    // '<div>' + m + '<span> min</span></div>';
    '<div>' + s + ' sec</div>';


    }
    //setInterval('updateTimer1()', 1000);



    function show_update_popup()
    {
    if($('input[class=update_task]:checked'))
    {
    $("#myModal").modal('show');
    }else
    {

    }

    }
    </script>


    <script language="javascript" type="text/javascript">
    $(document).ready(function() {
    unassignednotificationcount();
    ongoingtaskcountnotification();
    overduetaskcountnotification();
    completeddfnotificationcount();
    // setInterval(tiggernotification, 100000);
    //setInterval(getnotificationdata, 10000);
    setInterval(unassignednotificationcount, 30000);
    setInterval(ongoingtaskcountnotification, 30000);
    setInterval(overduetaskcountnotification, 30000);
    setInterval(completeddfnotificationcount, 30000);
    // toastr.options.onCloseClick= function() { alert("You clicked close button!"); };
    // tiggernotification();
    $("#savedata").click(function() {
    var uploaddf = $("#uploaddf").val();
    if(uploaddf=='')
    {
    $("#error_uploaddf").html('Required!');
    $("#uploaddf").css("border", "1px solid red");
    }

    var dfno = $("#dfno").val();
    if(dfno=='')
    {
    $("#error_dfno").html('Required!');
    $("#dfno").css("border", "1px solid red");
    }

    if(uploaddf=='' || dfno=='')
    {

    return false;
    }

    });

    });

    function taskupdationvalidation(){
    var taskstatus = $("#taskstatus").val();
    if(taskstatus=='')
    {
    $("#error_taskstatus").html('Required!');
    $("#taskstatus").css("border", "1px solid red");
    }else{

    if(taskstatus==0){
    $("#error_taskremarks").html('Required!');
    $("#taskremarks").css("border", "1px solid red");
    return false;
    }
    }


    if(taskstatus=='')
    {

    return false;
    }
    }
    </script> 

    <script type="text/javascript">
    function updatedfrelease(id,poid){

    $("#dfmodal").modal('show');
    $("#dfrecordid").val(id);
    $("#poid").val(poid);
    }
    </script>

    <script type="text/javascript">
    function updateyourprogressremarks(id,sortorder,dfno,taskid){

    $("#updateprogress").modal('show');
    $("#taskkiid").val(id);
    $("#mastertaskid").val(taskid);
    $("#shortorder").val(sortorder);
    $("#progressdfno").val(dfno);


    }
    </script>
    <script type="text/javascript">
    function reassigntasktoanotheruser(id, departmentid,assigneduser){
    $("#updateprogress1").modal('show');
    $("#selectedtasktoreassign").val(id);
    //$("#currenctlyassigned").html(currenctlyassigned);
    var department = departmentid;
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/fetchreassignuserlist",
    data:"department="+department+"&assigneduser="+assigneduser,
    success:function(data){
    $("#showuserstoreassign").html(data);
    }
    });

    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/currentlyassignedto",
    data:"id="+id,
    success:function(taskinfo){
    $("#currenctlyassigned").html(taskinfo);
    }
    });


    }
    </script>
    <script type="text/javascript">
    function assigntasktousers(id, departmentid){
    $("#assigntaskwindow").modal('show');
    var dfid = id;
    var department = departmentid;
    //alert(department);
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/featch_dynamic_tasks",
    data:"dfid="+dfid+"&department="+department,
    success:function(data){
    $("#showdynamictask").html(data);
    }
    });

    }
    </script>
    <?php 
    $department = $this->session->userdata['logged_in']['department_id'];
    $user_id =$this->session->userdata['logged_in']['user_id'];
    ?>
    <script src="
    https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.js
    "></script>
    <script type="text/javascript">
    function tiggernotification(){
    var departmentid = '<?php echo $department;?>';
    var userid = '<?php echo $user_id;?>';
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/checknotification",
    data:"departmentid="+departmentid+"&userid="+userid,
    dataType: 'json',
    success:function(data){
    //alert(data);
    jQuery.each(data, function(i, item) {
    //alert(data[i].notification);
    const bootoast = window.Bootoast;
    bootoast.toast({
    "message": data[i].notification,
    "type": "danger",
    "position": "top-right",
    "icon": "",
    "animationDuration": "300",
    "dismissable": true
    });
    });
    }
    });



    }

    // function getnotificationdata(){
    //      var departmentid = '<?php echo $department;?>';
    //     var userid = '<?php echo $user_id;?>';
    //      $.ajax({
    //         type:"post",
    //         url:"<?php echo page_url;?>Task/notificationuserwise",
    //         data:"departmentid="+departmentid+"&userid="+userid,
    //         success:function(data){
    //             //alert(data);
    //            $("#showlivenotifications").html(data);

    //         }
    //     });
    // }

    function unassignednotificationcount(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/unassignednotificationcount",
    success:function(data){

    $("#unassignednotificationcount").html(data);

    }
    }); 
    }

    function ongoingtaskcountnotification(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/ongoingtaskcountnotification",
    success:function(data){

    $("#ongoingtaskcountnotification").html(data);

    }
    }); 
    }

    function overduetaskcountnotification(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/overduetaskcountnotification",
    success:function(data){

    $("#overduetaskcountnotification").html(data);

    }
    });  
    }
    function completeddfnotificationcount(){
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Task/completeddfnotificationcount",
    success:function(data){
    // alert(data);
    $("#completeddfnotificationcount").html(data);

    }
    });  
    }
    <?php 
    $activeusertype =  $_SESSION['logged_in']['adminuser'];
    ?>
    function filter_ongoing_task()
    {
    var filter=$("#task_date_filter").val();
    var departmentfilter=$("#task_department_filter").val();
    var usefilter=$("#task_user_filter").val();
    var dfno = $("#task_dfno_filter").val();
    var activeusertype = '<?php echo $activeusertype;?>';
    $('#example').DataTable().ajax.url("<?php echo page_url;?>Task/ongoingtasklist/"+filter+"/"+departmentfilter+"/"+usefilter+"/"+dfno+"/"+activeusertype).load();
    //$('#example').DataTable().ajax.reload();
    }

    function filter_overdue_task()
    {
    var filter=$("#task_number_filter_overdue").val();
    var fiterindays = $("#noofdaysdue").val();
    var departmentfilter=$("#task_department_filter_overdue").val();
    var usefilter=$("#task_user_filter_overdue").val();
    var dfno = $("#task_dfno_filter_overdue").val();
    $('#example2').DataTable().ajax.url("<?php echo page_url;?>Task/outdatedtask/"+filter+"/"+departmentfilter+"/"+usefilter+"/"+dfno+"/"+fiterindays).load();
    //$('#example').DataTable().ajax.reload();
    }

    function filter_completed_task()
    {
    var departmentfilter=$("#task_department_filter_completed").val();
    var usefilter=$("#task_user_filter_completed").val();
    var dfno = $("#task_dfno_filter_completed").val();
    $('#example3').DataTable().ajax.url("<?php echo page_url;?>Task/completeddf/"+departmentfilter+"/"+usefilter+"/"+dfno).load(getcolors);
    //$('#example').DataTable().ajax.reload();
    }
    function getcolors() {
    $("#example3 tr").each(function() {
    var currentRow = $(this);
    var col1_value = currentRow.find("td:eq(10)").text();
    // alert(col1_value);

    if (col1_value == 'On Time') {
    currentRow.addClass("c11");
    } else if (col1_value == 'Delayed') {
    currentRow.addClass("c22")
    } else if (col1_value == 'Early') {
    currentRow.addClass("c33")
    } 



    });
    }

    </script>


    <div id="assigntaskwindow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

    <form id="assigntasktoteammember" method="post" action="<?php echo page_url;?>Task/assigntasktoteammember"  enctype="multipart/form-data">
    <div id="pageloader2">
    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
    </div>
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Assign Task to Your Team Members</h4>
    </div>

    <div class="modal-body">
    <div class="row">
    <div id="showdynamictask"></div>
    </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
    <input type="submit" id="assigntaskdata" class="btn btn-info" value="Submit"> 

    </div>

    </div>

    </form>

    </div>


    <script>
    $(document).ready(function(){
    $("#updateprogressform").on("submit", function(){
    $("#pageloader1").fadeIn();
    });//submit
    });//document ready
    </script>
    <script>
    $(document).ready(function(){
    $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
    });//submit
    });//document ready
    </script>
    <script>
    $(document).ready(function(){
    $("#assigntasktoteammember").on("submit", function(){
    $("#pageloader2").fadeIn();
    });//submit
    });//document ready
    </script>

    </body>

    </html>