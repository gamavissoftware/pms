<?php 
$CIA =& get_instance();
$CIA->load->model('Task_model');
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?>Precloser Task Dashboard</title>
        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
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
                table.manglesh tbody td {
                    text-align:center;
                }

        </style>

        <?php 
$USERIDSS = base64_encode($this->session->userdata['logged_in']['user_id']); 
$loggedinuserid = $this->session->userdata['logged_in']['user_id'];
// Fetch team ID of the logged-in user
$teamQuery = $this->db->select('team_id')
    ->from('prestogroup_teams')
    ->where('team_leader', $loggedinuserid)
    ->get();

    //echo "<pre>"; print_r($teamQuery->result_array()); exit;

if ($teamQuery->num_rows() > 0) {
    $teamid = $teamQuery->row()->team_id;

    // Fetch all team members
    $teamMembersQuery = $this->db->select('employee_id')
        ->from('presto_team_members')
        ->where('team_id', $teamid)
        ->get();

      // echo "<pre>"; print_r($teamMembersQuery->result_array()); exit;

    if ($teamMembersQuery->num_rows() > 0) {
        $teamMemberIDs = array_column($teamMembersQuery->result_array(), 'employee_id');

        // Fetch tasks for team members
$taskQuery = $this->db->select('a.id, a.start_date, a.end_date, a.task_completed_on, b.df_no, c.task_name, d.title, d.first_name, d.last_name, e.department')
    ->from('task_department_wise_scheduling a')
    ->join('df_release b', 'a.df_id=b.id', 'left')
    ->join('task_management c', 'a.taskid=c.task_id', 'left')
    ->join('system_users d', 'a.task_completed_by=d.user_id', 'left')
    ->join('departments e', 'd.department_id=e.department_id', 'left')
    ->where('a.task_status', 2)
    ->group_start() // Start grouping conditions
        ->where_in('a.task_completed_by', $teamMemberIDs) // Include team members
        ->or_where('a.task_completed_by', $loggedinuserid) // Include logged-in user
    ->group_end() // End grouping conditions
    ->get();

echo "<pre>"; print_r($taskQuery->result_array()); exit;
        if ($taskQuery->num_rows() > 0) {
            $tasks = $taskQuery->result_array();
?>

<style>
    /* Full-screen overlay */
    #blocking-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: none; 
    }

    /* Popup content container */
    #blocking-overlay-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 12px;
        width: 90%;
        height: 80%;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        overflow: auto;
    }

    /* Close button */
    #close-popup {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        color: black;
    }

    /* Table styling */
    #popup-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    #popup-table thead th {
        background-color: #4872b8;
        color: #fff;
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
    }

    #popup-table tbody tr {
        text-align: center;
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
    }

    #popup-table tbody tr:nth-child(even) {
        background-color: #f1f1f1;
    }

    #popup-table tbody tr:hover {
        background-color: #f3f3f3;
    }

    .btn-approve, .btn-reject {
        padding: 5px 10px;
        font-size: 14px;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-approve {
        background-color: #28a745;
    }

    .btn-reject {
        background-color: #dc3545;
    }

    .btn-approve:disabled, .btn-reject:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .loader {
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid #3498db;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        display: none; /* Initially hidden */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .search-box {
        margin-bottom: 15px;
    }

    #search-input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .confirmation-message {
        margin-top: 15px;
        padding: 10px;
        font-size: 16px;
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        display: none; /* Hidden by default */
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
                         <div class="btn-group pull-right"></div>
                         <?php if($this->uri->segment(3)<>''){?>
                            <h4 class="text-center" style="padding:10px; 10px; 10px; 10px; color:Red;">DFs LIST RUNNING DELAY</h4>
                        <?php  }else{?>
                            <h4 class="text-center" style="padding:10px; 10px; 10px; 10px;">All Running DF</h4>

                         <?php }?>
                           
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                

                <div id="blocking-overlay">
    <div id="blocking-overlay-content">
        <span id="close-popup">&times;</span>
        
        <h2>Pending Tasks for Approval</h2>

        <div class="search-box">
            <input type="text" id="search-input" placeholder="Search tasks by DF No, Task Name, or Department...">
        </div>

        <table id="popup-table">
            <thead>
                <tr>
                    <th style="color:#000;">#</th>
                    <th style="color:#000;">DF No.</th>
                    <th style="color:#000;">Task Name</th>
                    <th style="color:#000;">Department</th>
                    <th style="color:#000;">Start Date</th>
                    <th style="color:#000;">End Date</th>
                    <th style="color:#000;">Completed On</th>
                    <th style="color:#000;">Completed By</th>
                    <th style="color:#000;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($tasks as $task) { 
                ?>
                    <tr id="task-row-<?= $task['id']; ?>">
                        <td><?= $i++; ?></td>
                        <td><?= $task['df_no']; ?></td>
                        <td><?= ucwords(strtolower($task['task_name'])); ?></td>
                        <td><?= ucwords(strtolower($task['department'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['start_date'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['end_date'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['task_completed_on'])); ?></td>
                        <td><?php echo ucwords(strtolower($task['title']));?> <?php echo ucwords(strtolower($task['first_name']));?> <?php echo ucwords(strtolower($task['last_name']));?></td>
                        <td>
                            <button class="btn-approve" data-id="<?= $task['id']; ?>">Approve</button>
                            <button class="btn-reject" data-id="<?= $task['id']; ?>">Reject</button>
                            <div class="loader" id="loader-<?= $task['id']; ?>"></div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="confirmation-message" id="confirmation-message"></div>

        <button id="close-popup-btn">Close</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#blocking-overlay').fadeIn();

    $('#close-popup, #close-popup-btn').on('click', function() {
        $('#blocking-overlay').fadeOut();
    });

    $(document).on('click', '.btn-approve, .btn-reject', function() {
        var taskId = $(this).data('id');
        var row = $('#task-row-' + taskId);
        var action = $(this).hasClass('btn-approve') ? 'approved' : 'rejected';
        var messageText = `Task has been successfully ${action}.`;

        row.find('.btn-approve, .btn-reject').prop('disabled', true);
        $('#loader-' + taskId).show();

        $.ajax({
            url: $(this).hasClass('btn-approve') ? '<?= page_url.'Task/directmarkaspendingtoapprove'; ?>' : '<?= page_url.'Task/directmarkaspendingtoreject'; ?>',
            type: 'POST',
            data: { task_id: taskId },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    row.fadeOut();
                    showConfirmationMessage(messageText);
                } else {
                    alert(res.message);
                }
            },
            complete: function() {
                $('#loader-' + taskId).hide();
            }
        });
    });

    function showConfirmationMessage(message) {
        $('#confirmation-message').text(message).fadeIn();
        setTimeout(function() {
            $('#confirmation-message').fadeOut();
        }, 5000);
    }
});
</script>

<?php 
        }
    }
}
?>
 

                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   
        <script>

$( document ).ready(function() {

$('#example8').dataTable({

"bProcessing": true,

    fixedHeader: true,

"pagination":true

});   

});



</script>

</body>
</html>