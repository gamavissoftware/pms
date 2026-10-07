<?php
$loggedinuserdepartment = $this->session->userdata['logged_in']['department_id'];
$user_id =$this->session->userdata['logged_in']['user_id'];
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
$usertyp = 2;
}
}else{
// normal user
$_SESSION['logged_in']['adminuser'] = 3;
$usertyp = 3;
}
}else{
$usertyp = 1;
}
$counthelptickets = $CI->Dashboard_model->communication_ticket_system_count();
$counthelpticketsforyou = $CI->Dashboard_model->communication_ticket_for_you();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
<title><?php echo sitetitle; ?> PMS Dashboard</title>
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
<link href="https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.css" rel="stylesheet">
<!-- <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="<?php echo dashboard_asset_url; ?>dashboard.css" rel="stylesheet" type="text/css">
<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-noty/2.4.1/packaged/jquery.noty.packaged.min.js"></script>
<style>
        .tasks-table {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .tasks-table th, .tasks-table td {
            text-align: center;
            vertical-align: middle;
        }
        .tasks-table th {
            background-color: #f8f9fa;
        }
        .tasks-table .on-time {
            background-color: #d4edda;
        }
        .tasks-table .late, .tasks-table .not-started {
        background-color: #f8d7da;
        }
        .noty_type__error {
    background-color: #ff4d4d !important; /* Red background */
    color: white !important; /* White text */
    border: 1px solid #cc0000 !important; /* Optional border */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2) !important; /* Optional shadow */
}

.custom-red-theme {
        background-color: #d32f2f !important; /* Dark red background */
        color: white !important;
        padding: 10px;
        border-radius: 5px;
    }
    </style>
      
 <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('orderChart').getContext('2d');

            var orderData = <?php echo json_encode($order_data); ?>;
            var labels = [];
            var data = [];

            orderData.forEach(function(order) {
                labels.push(order.order_type === '1' ? 'Dom' : 'Exp');
                data.push(order.percentage);
            });

            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#FF6384', '#36A2EB'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        datalabels: {
                            formatter: (value, ctx) => {
                                return ctx.chart.data.labels[ctx.dataIndex] + ': ' + value.toFixed(2) + '%';
                            },
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 16
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    </script>
<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
<style>
.select2-container
{
    width:100% !important;
}

.select2-container--default .select2-selection--single
{
    height: 36px !important;
}
</style>
<?PHP

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach ($q->result() as $LOGO);

?>
<style type="text/css">
    table.pretty5 thead th {
text-align: center;
background:#049dd4;
color:#fff;
font-size:12px;
font-weight: bold;
}
table.pretty5 td {
text-align: center;
font-size:11px;
font-weight: bold;
}
.dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: right;
}
    .stats-box1 {
      padding: 10px 10px;
      color: black;
      border: none;
      border-radius: 15px;
      text-align: center;
      margin-bottom: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .stats-box1:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .stats-value1 {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .stats-label1 {
      font-size: 14px;
      color: black;
      font-weight: bold;
    }
</style>


    <script>
    $(document).ready(function() {
        // Capitalize function
        function toCapitalizedCase(str) {
            return str.replace(/\b\w/g, function(c) {
                return c.toUpperCase();
            });
        }

        // Format date function
        function formatDateToDMY(date) {
            var d = new Date(date);
            var day = ("0" + d.getDate()).slice(-2);
            var month = ("0" + (d.getMonth() + 1)).slice(-2);
            var year = d.getFullYear();
            return day + '-' + month + '-' + year;
        }

        // Initialize DataTable
        var table = $('#dfTablealldata').DataTable({
            "ajax": {
                "url": "<?php echo page_url.'Dashboard/get_active_dfs'; ?>",
                "dataSrc": "data"
            },
            "columns": [
                { "data": "df_no" },
                { "data": "df_description" },
                { 
                    "data": "added_on",
                    "render": function(data) {
                        return formatDateToDMY(data);
                    }
                },
                {
                    "data": "po_attachment",
                    "render": function(data) {
                        if (data) {
                            return '<a href="https://pms.shubhampack.in/image_bank/Taskdocument/' + data + '" class="btn btn-warning btn-xs" target="_blank" download>PO Download</a>';
                        } else {
                            return 'No PO';
                        }
                    },
                    "orderable": false
                },
                {
                    "data": "df_upload",
                    "render": function(data) {
                        return '<a href="https://pms.shubhampack.in/image_bank/Taskdocument/dfattachment/' + data + '" class="btn btn-success btn-xs" target="_blank" download>DF Download</a>';
                    },
                    "orderable": false
                },
                {
                    "data": null,
                    "defaultContent": '<button class="btn btn-primary btn-xs view-all-tasks">Click Here to View Tasks Delay</button>',
                    "orderable": false
                }
            ],
            "order": [[0, 'asc']],
            "createdRow": function(row, data) {
                // Fetch tasks and check for overdue status
                $.ajax({
                    url: '<?php echo page_url.'Dashboard/get_tasksNew/'; ?>' + data.id,
                    method: 'GET',
                    success: function(response) {
                        var tasks = JSON.parse(response);
                        var isOverdue = tasks.some(function(task) {
                            if (task.task_status == 1 && task.task_completed_on === '0000-00-00 00:00:00') {
                                var today = new Date();
                                var endDate = new Date(task.end_date).toISOString().split('T')[0];
                                var endDateObj = new Date(endDate);
                                return today > endDateObj;
                            }
                            return false;
                        });

                        // Highlight the row if overdue and not started
                        if (isOverdue) {
                            $(row).css('background-color', '#FF8C00');
                        }
                    }
                });
            }
        });

        // Handle task details popup
        $('#dfTablealldata tbody').on('click', '.view-all-tasks', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var df_id = row.data().id;

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.ajax({
                    url: '<?php echo page_url.'Dashboard/get_tasks_dfwise/'; ?>' + df_id,
                    method: 'GET',
                    success: function(response) {
                        var tasks = JSON.parse(response);
                        var taskHtml = '<table class="table table-bordered tasks-table">';
                        taskHtml += '<thead><tr><th>Task Name</th><th>Due Date</th><th>Completion Date</th><th>Assigned To</th><th>Delay (days)</th><th>Remarks</th></tr></thead>';
                        taskHtml += '<tbody>';
                        
                        tasks.forEach(function(task) {
                            var rowClass = '';
                            var delay = '';
                            var today = new Date();
                            var endDate = new Date(task.end_date).toISOString().split('T')[0];
                            var endDateFormatted = formatDateToDMY(task.end_date);

                            if (task.task_status == 1) {
                                if (task.task_completed_on === '0000-00-00 00:00:00') {
                                    var endDateObj = new Date(endDate);
                                    if (today > endDateObj) {
                                        rowClass = 'overdue';
                                        delay = 'Task not started yet (running late)';
                                    } else {
                                        delay = 'Task not started yet';
                                    }
                                } else {
                                    var completionDate = new Date(task.task_completed_on).toISOString().split('T')[0];
                                    var completionDateFormatted = formatDateToDMY(task.task_completed_on);
                                    var completionDateObj = new Date(completionDate);
                                    var endDateObj = new Date(endDate);
                                    if (completionDateObj > endDateObj) {
                                        rowClass = 'late';
                                        delay = Math.ceil((completionDateObj - endDateObj) / (1000 * 60 * 60 * 24)) + ' days late';
                                    } else {
                                        rowClass = 'on-time';
                                    }
                                }
                            }

                            if (task.remarks == null) {
                                task.remarks = '';
                            }

                            taskHtml += `<tr class="${rowClass}" style="${rowClass === 'overdue' ? 'background-color: #FF8C00;' : ''}">
                                <td>${toCapitalizedCase(task.task_name)}</td>
                                <td>${endDateFormatted}</td>
                                <td>${task.task_completed_on !== '0000-00-00 00:00:00' ? completionDateFormatted : 'Not Started Yet'}</td>
                                <td>${toCapitalizedCase(task.first_name + ' ' + task.last_name)}</td>
                                <td>${delay}</td>
                                <td>${task.remarks}</td>
                            </tr>`;
                        });

                        taskHtml += '</tbody></table>';

                        row.child(taskHtml).show();
                        tr.addClass('shown');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching tasks:", status, error);
                    }
                });
            }
        });
    });
</script>

</head>


<body>


<!-- Navigation Bar-->
<header id="topnav">
<?php $this->load->view('common/nav-menu'); ?>



<?php

 $this->load->view('dashboard/preclosertasknotificaiton');

  ?>

  <?php
if($user_id==162 || $user_id==139){}else{
    
 $this->load->view('dashboard/overduenotification');
 } ?>
</header>
<!-- End Navigation Bar-->


<div class="wrapper">
<?php 
if(pms_is_super_admin() || $this->session->userdata['logged_in']['user_id']==139){


        $this->load->view('dashboard/graphicaldata');
    }
?>

<div class="container-fluid">



<div class="row">
<div class="col-sm-12">

<div class="row card-box">
<?php echo $this->session->flashdata('message'); ?>
<h3 class="pull-right">
    <?php

// $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '15')->where('submoduleid', '36')->where('submodule_access', '1')->get();
// if ($qry->num_rows() > 0) {
 ?>
<div class="col-md-2">

<!-- <a href="<?php echo page_url;?>Task/poreceived" class="pull-right btn btn-success btn-xs"> ADD PO <i class="fa fa-plus"></i></a> -->
<a href="<?php echo page_url;?>Maintenance_support/" class="pull-right btn btn-success btn-xs">RAISE HELP TICKET AGAINST DF</a>
</div>
<?php //}?></h3><hr>
<?php 
$oneweek = date('Y-m-d',strtotime('-7 days'));
?>
 <a href="<?php echo page_url;?>Task/dfreleasedashboard/"><div class="col-sm-2 col-md-2">
     <div class="stats-box1"  style="background-color:#d9d9d9 !important;">
       
        <div class="stats-value1"><?php 
        $q = $this->db->select('id')->from('df_release')->where('df_status',0)->where('on_hold',0)->get();
        echo $q->num_rows();
      ?></div><hr style="margin-top: 5px; margin-bottom: 5px; border: 0; border-top: 1px solid #eee;">
      <div class="hrclas"></div>
        <div class="stats-label1">Running DF</div>
      </div>
    </div></a>

    <a href="<?php echo page_url;?>Task/onholdf/"><div class="col-sm-2 col-md-2">
     <div class="stats-box1"  style="background-color:#d9d9d9 !important;">
       
        <div class="stats-value1"><?php 
        $q = $this->db->select('id')->from('df_release')->where('df_status',0)->where('on_hold',1)->get();
        echo $q->num_rows();
      ?></div><hr style="margin-top: 5px; margin-bottom: 5px; border: 0; border-top: 1px solid #eee;">
      <div class="hrclas"></div>
        <div class="stats-label1">On Hold DF</div>
      </div>
    </div></a>
<?php 
    if($is_admin==1){
        $useriddata = "";
    }else{
       $useriddata = $user_id; 
    }

    $enuser = base64_encode($useriddata);
?>
     <a href="<?php echo page_url;?>Task/viewallrunninghelptickets/<?php echo $enuser;?>"><div class="col-sm-2 col-md-2">
     <div class="stats-box1"  style="background-color:#d9d9d9 !important;">
       
        <div class="stats-value1"><?php 
        echo $counthelptickets;
      ?></div><div class="hrclas"></div><hr style="margin-top: 5px; margin-bottom: 5px; border: 0; border-top: 1px solid #eee;">
        <div class="stats-label1"><?php if($is_admin==1){}else{?><?php echo "Your";}?> PMS Help Tickets</div>
      </div>
    </div></a>

<?php if($is_admin==1){}else{?>
     <a href="<?php echo page_url;?>Task/helpticketsforyou/<?php echo $enuser;?>"><div class="col-sm-2 col-md-2">
     <div class="stats-box1"  style="background-color:#d9d9d9 !important;">
       
        <div class="stats-value1"><?php 
        echo $counthelpticketsforyou;?></div><div class="hrclas"></div><hr style="margin-top: 5px; margin-bottom: 5px; border: 0; border-top: 1px solid #eee;">
        <div class="stats-label1">PMS Help Tickets for <?php if($is_admin==2){echo "Team";}else{ echo "You";};?> </div>
      </div>
    </div></a>
<?php }?>

<?php 
$teammemberid = array();
$teamids = array();

// Get the team IDs for the given team leader
$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();

if($q->num_rows() > 0){
    foreach($q->result() as $teamleaderdata) {
        $teamids[] = $teamleaderdata->team_id;
    }
   // echo "<pre>"; print_r($teamids);

    // Get the employee IDs for the teams
    $q = $this->db->select('employee_id')->from('presto_team_members')->where_in('team_id', $teamids)->get();
    if($q->num_rows() > 0){
        foreach($q->result() as $teammemberdata) {
            $teammemberid[] =  $teammemberdata->employee_id;
        }
    }
   // echo "<pre>"; print_r($teammemberid);

    // Get total tickets for team members if teammemberid is not empty
    if (!empty($teammemberid)) {
        $q33 = $this->db->select('id')->from('communication_ticket_system')->where_in('user_id', $teammemberid, false)->get();
        $totalticketforteam = $q33->num_rows();
    } else {
        $totalticketforteam = 0;
    }
    
    //echo $totalticketforteam; 

?>

 <a href="<?php echo page_url;?>Task/helpticketsforyourteam/<?php echo $enuser;?>"><div class="col-sm-2 col-md-2">
     <div class="stats-box1"  style="background-color:#d9d9d9 !important;">
       
        <div class="stats-value1"><?php 
        echo  $totalticketforteam ;?></div><div class="hrclas"></div><hr style="margin-top: 5px; margin-bottom: 5px; border: 0; border-top: 1px solid #eee;">
        <div class="stats-label1">PMS Help Tickets for your Team </div>
      </div>
    </div></a>
<?php }?>

<?php 
$teammemberid = array();
$teamids = array();

// Get the team IDs for the given team leader
$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();

if($q->num_rows() > 0){
    foreach($q->result() as $teamleaderdata) {
        $teamids[] = $teamleaderdata->team_id;
    }
   // echo "<pre>"; print_r($teamids);

    // Get the employee IDs for the teams
    $q = $this->db->select('employee_id')->from('presto_team_members')->where_in('team_id', $teamids)->get();
    if($q->num_rows() > 0){
        foreach($q->result() as $teammemberdata) {
            $teammemberid[] =  $teammemberdata->employee_id;
        }
    }
   // echo "<pre>"; print_r($teammemberid);

    // Get total tickets for team members if teammemberid is not empty
    if (!empty($teammemberid)) {
        $q33 = $this->db->select('id')->from('communication_ticket_system')->where_in('user_id', $teammemberid, false)->get();
        $totalticketforteam = $q33->num_rows();
    } else {
        $totalticketforteam = 0;
    }
    
    //echo $totalticketforteam; 

?>

 <a href="<?php echo page_url;?>Task/helpticketsforyourteam/<?php echo $enuser;?>"><div class="col-sm-2 col-md-2">
     <div class="stats-box1"  style="background-color:#d9d9d9 !important;">
       
        <div class="stats-value1"><?php 
        echo  $totalticketforteam ;?></div><div class="hrclas"></div><hr style="margin-top: 5px; margin-bottom: 5px; border: 0; border-top: 1px solid #eee;">
        <div class="stats-label1">PMS Help Tickets Raised by Your Team </div>
      </div>
    </div></a>
<?php }?>

<div class="col-md-8">
<!-- <a href="<?php echo page_url;?>MIS/index/ALL/<?php echo $oneweek;?>/<?php echo date('Y-m-d');?>/ALL"><span class="btn btn-primary btn-xs">MIS REPORT</span></a> -->

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

<div class="col-sm-3">
<button class="tablinks" onclick="openCity(event, 'completeandpendingforapproval')"><img src="<?php echo assets_url; ?>complete.png" alt="">COMPLETED & PENDING FOR APPROVAL<span class="counter" style="background-color:orange;font-weight: bold;color:white;" id="completeddfnotificationcountforapproval"></span></button>
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
<select class="form-control task_dfno_filter" id="task_dfno_filter" onchange="filter_ongoing_task();">
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
<select class="form-control task_dfno_filter" id="task_department_filter" onchange="filter_ongoing_task(); getusers();">
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
<th>START DATE</th>
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
<select class="form-control task_dfno_filter" id="task_dfno_filter_overdue" onchange="filter_overdue_task();">
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
<select class="form-control task_dfno_filter" id="task_department_filter_overdue" onchange="filter_overdue_task(); getusersss();">
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
<select class="form-control task_dfno_filter" id="task_dfno_filter_completed" onchange="filter_completed_task();">
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
<select class="form-control task_dfno_filter" id="task_department_filter_completed" onchange="filter_completed_task(); getuserscompleted();">
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

<div id="completeandpendingforapproval" class="tabcontent">
<div class="row" style="margin-top:20px;">

<div class="col-md-12" style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

<div class="col-md-4">
<div class="form-group">
<label style="color:#000;">FILTER BY DF NO.</label>
<select class="form-control task_dfno_filter" id="task_dfno_filter_completed" onchange="filter_completed_task();">
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
<select class="form-control task_dfno_filter" id="task_department_filter_completed" onchange="filter_completed_task(); getuserscompleted();">
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
<table id="example8" class="table pretty2">
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
<th>APPROVE/REJECT</th>
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
if(pms_is_super_admin() || $user_id==139){
?>


<div class="row card-box">
    <h4 class="text-center">RUNNING DF TASK WISE DELAY REPORT</h4><hr>

   <table id="dfTablealldata" class="table table-striped table-bordered pretty5" style="width:100%">
        <thead>
            <tr>
                
                <th>DF NO</th>
                <th>DF Description</th>
                <th>Added On</th>
                <th>Download PO</th>
                <th>Download DF</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>



</div>

<?php
}
?>

<?php 
if(pms_is_super_admin()){

?>
<div class="row card-box">
    <h4 class="text-center">All Running DFs</h4><hr>
<table id="example5" class="table pretty5 table-striped table-bordered">
<thead>
<tr>
<th>S. NO.</th>
<th>DF No.</th>
<th>DOWNLOAD</th>
<th>PO DATE</th>
<th>COMPANY NAME</th>
<th>MARKETING PERSON</th>
<th>DF RELEASE DATE</th>
<th>PROJECTED COMPLETION DATE</th>
<th>ACTUAL COMPLETION DATE</th>
<th>DF STATUS</th>
<th>DF DELAYED</th>
<th>LOSS AGAINST DF</th>
<th>VIEW GANTT CHART</th>
</tr>
</thead>
<tbody>
<?php 
$m=1;
$q = $this->db->select('a.id, a.df_no, a.added_on, a.df_upload, b.title, b.first_name, b.last_name')->from('df_release a')->join('system_users b','a.added_by=b.user_id','left')->where('a.df_status',0)->order_by('a.id','desc')->get();
if($q->num_rows()>0){
foreach($q->result() as $rows){
$dfowner = $rows->title." ".$rows->first_name." ".$rows->last_name;
$q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
if($q1->num_rows()>0){
foreach($q1->result() as $r);
$planneddate = date('d-m-Y',strtotime($r->enddate));
}else{
$planneddate = '';
}

$q5 = $this->db->select('a.company_name, a.podate, b.title, b.first_name, b.last_name, po_attachment')->from('poreceived a')->join('system_users b','a.added_by=b.user_id','left')->where('a.df_id',$rows->id)->get();

if($q5->num_rows()>0){
    foreach($q5->result() as $row5);
    $companyname = $row5->company_name;
    $podate = date('d-m-Y',strtotime($row5->podate));
    //$dfowner = $row5->title." ".$row5->first_name." ".$row5->last_name;
    $po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row5->po_attachment.'" download><span class="btn btn-warning btn-xs">Click to download PO</span></a>';
}else{
    $companyname = "";
    $podate = "";
    $dfowner = "";
    $po_attachment  = '';
}

$q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
$count = $q1->num_rows();
$delaycount = array();
$delaycount[] = 0;
$totaldayscountarray = array();
$totaldayscountarray[] =0;
$q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->where('task_status',1)->get();
$totaldone = $q2->num_rows();
$percetage =  @round($totaldone*100/ $count);

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
<tr>
    <td><?php echo $m;?></td>
    <td><?php echo strtoupper($rows->df_no);?></td>
    <td><a href="<?php echo sfdocument;?>Taskdocument/dfattachment/<?php echo $rows->df_upload;?>" download><span class="btn btn-primary btn-xs">DOWNLOAD DF</span></a><br><br><?php echo $po_attachment;?></td>
    
    <td><?php echo $podate;?></td>
    <td><?php echo strtoupper($companyname);?> <br><a href="<?php echo page_url;?>Task/viewdfmeetingmom/<?php echo $rows->id;?>"><span class="btn btn-success btn-xs">DF MEETING MOM</span></a></td>
    <td><?php echo strtoupper($dfowner);?></td>
    <td><?php echo date('d-m-Y',strtotime($rows->added_on));?></td>
    <td><?php echo $planneddate;?></td>
    <?php 
        $maxdays = $CIA->Task_model->workDelayed($rows->id,0);
    ?> 
    <td><?php
    $planneddateofcompletion = date('Y-m-d',strtotime($planneddate));
    //echo $planneddateofcompletion; exit;
    if($maxdays>0){
        
    $date = new DateTime($planneddateofcompletion);
    $date->modify("+$maxdays days");
    $newDate = $date->format('Y-m-d');
    
    $nextcompletiondate = date('d-m-Y',strtotime($newDate));


    }else{
        $nextcompletiondate = date('d-m-Y',strtotime($planneddate));
    }
     echo $nextcompletiondate;?></td>
    <td><?php echo  $percetage;?>%</td>
    <td>
        <?php if($maxdays>0){
            echo "<strong style='color:red; font-weight:bold;'>".$maxdays." DAYS</strong>";
        }?>
    </td>
    <td>
        <?php 
        


        $q1 = $this->db->select('MIN(start_date) as stdate')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->order_by('id','asc')->limit(1)->get();
        if($q1->num_rows()>0){
            foreach($q1->result() as $row1);
            $startdate = $row1->stdate;
        }

        $q2 = $this->db->select('MAX(end_date) as edndate ')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->order_by('id','desc')->limit(1)->get();
        if($q2->num_rows()>0){
            foreach($q2->result() as $row2);
            $end_date = $row2->edndate;
        }

        $startDate = new DateTime($startdate);
        $endDate = new DateTime($end_date);

        $interval = $startDate->diff($endDate);

        $totaldays =  $interval->days;
        

        $q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->group_by('po_id')->get();
        foreach($q->result() as $poinfo);
        $pono = $poinfo->po_id;

        $q6 = $this->db->select('order_value')->from('poreceived')->where('id',$pono)->get();
        if($q6->num_rows()>0){
            foreach($q6->result() as $calculatevalue);
            $orderamount = $calculatevalue->order_value;
            $onedayloss =  floatval($orderamount/$totaldays);
             $loss = round($onedayloss*$maxdays);
             if($loss<>''){
               $lossamount =  $CIA->Task_model->formatIndianCurrency($loss);
                echo '<i class="fa fa-inr"></i> '.$lossamount;
             }
        }



        ?>


    </td>
    <td><a href="<?php echo page_url;?>Task/finalgantchart/<?php echo $rows->id;?>" target="_blank"><span class="btn btn-warning btn-xs">GANTT CHART</span></a></td>
</tr>

<?php $m++;} 
}?>


</tbody>
</table>
</div>


<?php }?>

<?php 
if($user_id==139){}else{
$Q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
if($Q->num_rows()>0){
?>

<div class="row card-box">
    <h4 class="text-center">DF WISE TASK DUE OF YOUR TEAM</h4>

   <table id="dfTable" class="table table-striped table-bordered pretty5" style="width:100%">
        <thead>
            <tr>
                
                <th>DF NO</th>
                <th>DF Description</th>
                <th>Added On</th>
                <th>Download</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>



</div>

<?php
}
}?>



<?php 
$Q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
if($Q->num_rows()>0){

?>
<div class="row card-box">
    <h4 class="text-center">All Running DFs</h4><hr>
<table id="example5" class="table pretty5 table-striped table-bordered">
<thead>
<tr>
<th>S. NO.</th>
<th>DF No.</th>
<th>DOWNLOAD</th>
<th>PO DATE</th>
<th>COMPANY NAME</th>
<th>MARKETING PERSON</th>
<th>DF RELEASE DATE</th>
<th>PROJECTED COMPLETION DATE</th>
<th>ACTUAL COMPLETION DATE</th>
<th>DF STATUS</th>
<th>DF DELAYED</th>
<th>LOSS AGAINST DF</th>
<th>VIEW GANTT CHART</th>
</tr>
</thead>
<tbody>
<?php 
$m=1;
$q = $this->db->select('a.id, a.df_no, a.added_on, a.df_upload, b.title, b.first_name, b.last_name')->from('df_release a')->join('system_users b','a.added_by=b.user_id','left')->where('a.df_status',0)->order_by('a.id','desc')->get();
if($q->num_rows()>0){
foreach($q->result() as $rows){

    $dfowner = $rows->title." ".$rows->first_name." ".$rows->last_name;

$q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
if($q1->num_rows()>0){
foreach($q1->result() as $r);
$planneddate = date('d-m-Y',strtotime($r->enddate));
}else{
$planneddate = '';
}

$q5 = $this->db->select('a.company_name, a.podate, b.title, b.first_name, b.last_name')->from('poreceived a')->join('system_users b','a.added_by=b.user_id','left')->where('id',$rows->id)->get();

if($q5->num_rows()>0){
    foreach($q5->result() as $row5);
    $companyname = $row5->company_name;
    $podate = date('d-m-Y',strtotime($row5->podate));
    //$dfowner = $row5->title." ".$row5->first_name." ".$row5->last_name;
}else{
    $companyname = "";
    $podate = "";
    $dfowner = "";
}

$q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
$count = $q1->num_rows();
$delaycount = array();
$delaycount[] = 0;
$totaldayscountarray = array();
$totaldayscountarray[] =0;
$q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->where('task_status',1)->get();
$totaldone = $q2->num_rows();
$percetage =  @round($totaldone*100/ $count);

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
<tr>
    <td><?php echo $m;?></td>
    <td><?php echo strtoupper($rows->df_no);?></td>
    <td><a href="<?php echo sfdocument;?>Taskdocument/dfattachment/<?php echo $rows->df_upload;?>" download><span class="btn btn-primary btn-xs">DOWNLOAD DF</span></a></td>
    
    <td><?php echo $podate;?></td>
    <td><?php echo strtoupper($companyname);?> <br><a href="<?php echo page_url;?>Task/viewdfmeetingmom/<?php echo $rows->id;?>"><span class="btn btn-success btn-xs">DF MEETING MOM</span></a></td>
    <td><?php echo strtoupper($dfowner);?></td>
    <td><?php echo date('d-m-Y',strtotime($rows->added_on));?></td>
    <td><?php echo $planneddate;?></td>
    <?php 
        $maxdays = $CIA->Task_model->workDelayed($rows->id,0);
    ?> 
    <td><?php
    $planneddateofcompletion = date('Y-m-d',strtotime($planneddate));
    //echo $planneddateofcompletion; exit;
    if($maxdays>0){
        
    $date = new DateTime($planneddateofcompletion);
    $date->modify("+$maxdays days");
    $newDate = $date->format('Y-m-d');
    
    $nextcompletiondate = date('d-m-Y',strtotime($newDate));


    }else{
        $nextcompletiondate = date('d-m-Y',strtotime($planneddate));
    }
     echo $nextcompletiondate;?></td>
    <td><?php echo  $percetage;?>%</td>
    <td>
        <?php if($maxdays>0){
            echo "<strong style='color:red; font-weight:bold;'>".$maxdays." DAYS</strong>";
        }?>
    </td>
    <td>
        <?php 
        


        $q1 = $this->db->select('MIN(start_date) as stdate')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->order_by('id','asc')->limit(1)->get();
        if($q1->num_rows()>0){
            foreach($q1->result() as $row1);
            $startdate = $row1->stdate;
        }

        $q2 = $this->db->select('MAX(end_date) as edndate ')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->order_by('id','desc')->limit(1)->get();
        if($q2->num_rows()>0){
            foreach($q2->result() as $row2);
            $end_date = $row2->edndate;
        }

        $startDate = new DateTime($startdate);
        $endDate = new DateTime($end_date);

        $interval = $startDate->diff($endDate);

        $totaldays =  $interval->days;
        

        $q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->group_by('po_id')->get();
        foreach($q->result() as $poinfo);
        $pono = $poinfo->po_id;

        $q6 = $this->db->select('order_value')->from('poreceived')->where('id',$pono)->get();
        if($q6->num_rows()>0){
            foreach($q6->result() as $calculatevalue);
            $orderamount = $calculatevalue->order_value;
            $onedayloss =  floatval($orderamount/$totaldays);
             $loss = round($onedayloss*$maxdays);
             if($loss<>''){
               $lossamount =  $CIA->Task_model->formatIndianCurrency($loss);
                echo '<i class="fa fa-inr"></i> '.$lossamount;
             }
        }



        ?>


    </td>
  
    <td><a href="<?php echo page_url;?>Task/dfgantchartNew/<?php echo $rows->id;?>" target="_blank"><span class="btn btn-warning btn-xs">GANTT CHART</span></a></td>

</tr>

<?php $m++;} 
}?>


</tbody>
</table>
</div>


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
<input type="hidden" id="start_date" value="" name="start_date">
<div class="form-group">
<label>Status <span style="color:red" id="error_taskstatus">*</span></label>
<select name="taskstatus" id="taskstatus" onchange="checkifpendingdue(); <?php if($_SESSION['logged_in']['user_id']==61 || $_SESSION['logged_in']['user_id']==161){ ?>checkifdoneDate(); <?php } ?>" class="form-control" required>
<option value="1">Done</option>
<option value="0">Pending</option>
<!-- <option value="2">Send to Previous Step</option> -->
</select>
</div>
</div>

</div>

<?php if($_SESSION['logged_in']['user_id']==61 || $_SESSION['logged_in']['user_id']==161)
{ ?>
    <div class="row" id="doneDiv" style="">
    <div class="col-md-12">
    <div class="form-group">
    <input type="date" name="doneDate" id="doneDate" class="form-control" max="<?php echo date('Y-m-d');?>">
    </div>
    </div>
    </div>
<?php } ?>

<div class="row" id="helpticketdiv" style="display:none">
    <div class="col-md-12">
        <div class="form-group">
            <label>Do you want to Raise help ticket?</label>
            <select class="form-control" name="ticketcondition" id="ticketcondition" onchange="checkifticket();">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>
    <script type="text/javascript">
        function  checkifticket() {
            var ticketcondition = $("#ticketcondition").val();
            
            if(ticketcondition==1){
                $("#departmentdiv").show();
                $("#userdiv").show();
               
            }else{
                 $("#departmentdiv").hide();
                 $("#userdiv").hide();
                 
            }
        }
    </script>

    <div class="col-md-6" style="display:none" id="departmentdiv">
        <div class="form-group">
            <label>Department</label>
            <select class="form-control" name="selectdepartment" id="selectdepartment" onchange="getalldepartmentwiseuser();">
                <option value="">Select Department</option>
                <?php 
                $q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id',2)->where('status',1)->order_by('department','asc')->get();
                foreach($q->result() as $dpt){?>
                    <option value="<?php echo $dpt->department_id;?>"><?php echo ucfirst(strtolower($dpt->department));?></option>

               <?php  } ?>
            </select>
        </div>
    </div>
    <script type="text/javascript">
        function getalldepartmentwiseuser(){
            var departmentid = $("#selectdepartment").val();
            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Task/getdepartmentwiseusers",
            data:"departmentid="+departmentid,
            success:function(data){
            $("#departmentuser").html(data);
            }
            });
        }
    </script>
    <div class="col-md-6" style="display:none" id="userdiv">
        <div class="form-group">
            <label>Select User</label>
            <select class="form-control" name="departmentuser" id="departmentuser"> 
            </select>
        </div>
    </div>
</div>

<div class="row" style="display:none" id="sendtopreviousstep">

<div class="col-md-12">
<div class="form-group">
<label>Send Previous Step</label>
<select class="form-control task_dfno_filter" name="previousstep" id="previousstep">

</select>
</div>
</div>
</div>
<div class="row">

<div class="col-md-12">
<div class="form-group">
<label>Remarks <span style="color:red" id="error_taskremarks"></span></label>
<textarea name="taskremarks" id="taskremarks" class="form-control" required></textarea>
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
<div class="col-md-12" style="display:none">
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
$("#doneDiv").css('display','none');
            $("#doneDate").attr('required',false);
if(taskstatus==0){
    $("#helpticketdiv").show();
}else{
    $("#helpticketdiv").hide();
    $("#doneDiv").css('display','');
            $("#doneDate").attr('required',true);
}
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


function checkifdoneDate(){
var taskstatus = $("#taskstatus").val(); 
$("#doneDiv").css('display','none');
$("#doneDate").attr('required',false);
if(taskstatus==0){
   // $("#helpticketdiv").show();
}else{
        $("#doneDiv").css('display','');
        $("#doneDate").attr('required',true);
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

function changeDoneDate(id)
{

// alert('hi'); 
var ddate=$("#donedate"+id).val();
alert(ddate); 
if(ddate!='')
{
        $.ajax({
        url: '<?php echo page_url.'Task/changeTaskCompleteDate/';?>'+id+"/"+ddate,
        method: 'GET',
        success: function(response) {
        }

        });
}


}



$(document).ready(function() {
var a=$("#task_date_filter").val();

$('#example').dataTable({

"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Task/ongoingtasklist/",
stateSave: true,
"aoColumns": [

{ mData: 'sr_no' } ,
{ mData: 'df_no' } ,
{ mData: 'dfupload' } ,
{ mData: 'df_release_date' } ,
{ mData: 'department' } ,
{ mData: 'membername' } ,
{ mData: 'task_name' } ,
{ mData: 'startdate' }, 
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
stateSave: true,
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
stateSave: true,
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


$('#example8').dataTable({
"bProcessing": true,
"pagination":true,
scrollX: true,
stateSave: true,
"sAjaxSource": "<?php echo page_url;?>Task/completeddfpendingforapproval",
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
{ mData: 'approveorreject' } ,
{ mData: 'taskdelay' } ,
{ mData: 'status' } ,
{ mData: 'remarks' } ,
{ mData: 'addedby' }
],
"initComplete": function(settings, json) {

getcolors()
}

});

  $('#example3').on('draw.dt', function() {
                // do action here

                getcolors();
            });

            $('#example3').on('search.dt', function() {

                getcolors();
            });



            
<?php 
if(pms_is_super_admin()){
?>
$('#example5').dataTable({
"bProcessing": true,
"pagination":true,
stateSave: true,
 "dom": '<"top"lf>rt<"bottom"ip><"clear">'
});

$('#example6').dataTable({
"bProcessing": true,
"pagination":true,
stateSave: true,
 "dom": '<"top"lf>rt<"bottom"ip><"clear">'
});
<?php }?>

<?php 

$Q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
if($Q->num_rows()>0){
?>
$('#example5').dataTable({
"bProcessing": true,
"pagination":true,
stateSave: true,
 "dom": '<"top"lf>rt<"bottom"ip><"clear">'
});

$('#example6').dataTable({
"bProcessing": true,
"pagination":true,
stateSave: true,
 "dom": '<"top"lf>rt<"bottom"ip><"clear">'
});
<?php }?>

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
completeddfnotificationcountforapproval();
// setInterval(tiggernotification, 100000);
//setInterval(getnotificationdata, 10000);
setInterval(unassignednotificationcount, 30000);
setInterval(ongoingtaskcountnotification, 30000);
setInterval(overduetaskcountnotification, 30000);
setInterval(completeddfnotificationcount, 30000);
setInterval(completeddfnotificationcountforapproval, 30000);
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
function updatedfrelease(id,poid, dfno){
$("#dfmodal").modal('show');
$("#dfrecordid").val(id);
$("#poid").val(poid);
$("#dfno").val(dfno);
}
</script>

<script type="text/javascript">
function updateyourprogressremarks(id,sortorder,dfno,taskid,start_date){

$("#updateprogress").modal('show');
$("#taskkiid").val(id);
$("#mastertaskid").val(taskid);
$("#shortorder").val(sortorder);
$("#progressdfno").val(dfno);
$("#start_date").val(start_date);


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

function completeddfnotificationcountforapproval(){
$.ajax({
type:"post",
url:"<?php echo page_url;?>Task/completeddfnotificationcountpendingforapproval",
success:function(data){
// alert(data);
$("#completeddfnotificationcountforapproval").html(data);

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

function applysameUser()
{
    if($('#appall').is(':checked'))
    {
        var d=$(".firstClass").val();
        if(d!='')
        {  $(".allassignUser").val(d);
        }else
        {
           $("#appall").prop('checked', false);
        }
       
    }else
    {
        //alert('bye');
    }

}
</script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$( document ).ready(function() {
$('.task_dfno_filter').select2();
});


</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

<script>
$(document).ready(function () {
    function fetchNotifications() {
        $.ajax({
            url: "<?php echo page_url.'Task/fetchNotifications'; ?>",
            method: "GET",
            success: function (data) {
                const notifications = JSON.parse(data);

                // Show Growl Notifications
                notifications.forEach(notification => {
                    toastr.info(notification.message, "New Notification", {
                        timeOut: 0, // Persistent until clicked
                        closeButton: true,
                        onHidden: function () {
                            markNotificationAsRead(notification.id);
                        }
                    });
                });
            }
        });
    }

    // Mark a notification as read
    function markNotificationAsRead(notification_id) {
        $.ajax({
            url: "<?php echo page_url.'Task/markNotificationAsRead'; ?>",
            method: "POST",
            data: { notification_id: notification_id },
            success: function (response) {
                console.log(response); // Debug: Show server response
            }
        });
    }

    // Fetch notifications on page load
    fetchNotifications();
    //dfsupportfetchNotifications();

    // Optional: Poll for new notifications periodically
    setInterval(fetchNotifications, 30000);
});

  

    function dfsupportfetchNotificationsusewise() {
        $.ajax({
            url: "<?php echo page_url.'Maintenance_support/fetch_notificationsofusers'; ?>",
            method: "GET",
            dataType: "json",
            success: function (data) {
                if (data.length > 0) {
                    data.forEach(notification => {
                        noty({
                             text: `<strong>New Ticket:</strong> ${notification.message}`,
                            type: 'info',
                            layout: 'topRight',
                            timeout: 5000
                        });
                    });

                    // Mark notifications as read
                    markdfNotificationsAsRead(${notification.id});
                }
            }
        });
    }

   function markNotificationRead(notificationId) {
    $.ajax({
        url: '<?php echo page_url."Maintenance_support/mark_notifications_read"; ?>',
        type: 'POST',
        data: { notification_id: notificationId },
        success: function (response) {
            const data = JSON.parse(response);
            if (data.status === 'success') {
                alert(data.message);
                // Optionally refresh the notifications list or UI
            } else {
                alert(data.message);
            }
        },
        error: function () {
            alert('An error occurred while marking the notification as read.');
        }
    });
}

   
     setInterval(dfsupportfetchNotificationsusewise, 30000);
    dfsupportfetchNotificationsusewise();
</script>

<?php if ($this->session->userdata('growl_notification')): ?>
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.info("<?php echo $this->session->userdata('growl_notification'); ?>");
        });
    </script>
    <?php $this->session->unset_userdata('growl_notification'); ?>
<?php endif; ?>

</body>
</html>