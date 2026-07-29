<?php
$loggedinuserdepartment = $this->session->userdata['logged_in']['department_id'];
$user_id = $this->session->userdata['logged_in']['user_id'];
$is_admin = 0;
$user_role = $this->session->userdata['logged_in']['role'];
$CI = &get_instance();
$CI->load->model('Dashboard_model');
$adminuserrole = $CI->Dashboard_model->getsuperadminuserole();

if (count($adminuserrole) > 0) {
    if (in_array($user_role, $adminuserrole)) {
        $is_admin = 1;
        $_SESSION['logged_in']['adminuser'] = 1;
    } else {
        $_SESSION['logged_in']['adminuser'] = 0;
    }
}
$CIA = &get_instance();
$CIA->load->model('Task_model');
$runningdfno = $CIA->Task_model->getallrunningdf();

if ($is_admin == 0) {
    $department = $this->session->userdata['logged_in']['department_id'];
    $departmentid = array();
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
    if ($q->num_rows() > 0) {
        foreach ($q->result() as $row) {
            $departmentid[] = $row->department_id;
            //HOD 
            $_SESSION['logged_in']['adminuser'] = 2;
            $usertyp = 2;
        }
    } else {
        // normal user
        $_SESSION['logged_in']['adminuser'] = 3;
        $usertyp = 3;
    }
} else {
    $usertyp = 1;
}

$DI = &get_instance();
$DI->load->model('Dashboard_model');
$counthelptickets = $CI->Dashboard_model->communication_ticket_system_count();
$counthelpticketsforyou = $CI->Dashboard_model->communication_ticket_for_you();

$USERIDSS = base64_encode($this->session->userdata['logged_in']['user_id']); 
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
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <!-- <link href="<?php echo assets_url; ?>css/html_design.css" rel="stylesheet" type="text/css" /> -->
    <link href="
https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.css
" rel="stylesheet">
    <!-- <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://shubhampack.in/beta1/styleasset/dashboard.css" rel="stylesheet" type="text/css">
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
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery-noty/2.4.1/packaged/jquery.noty.packaged.min.js"></script>
    <style>
        .tasks-table {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .tasks-table th,
        .tasks-table td {
            text-align: center;
            vertical-align: middle;
        }

        .tasks-table th {
            background-color: #f8f9fa;
        }

        .tasks-table .on-time {
            background-color: #d4edda;
        }

        .tasks-table .late,
        .tasks-table .not-started {
            background-color: #f8d7da;
        }

        .noty_type__error {
            background-color: #ff4d4d !important;
            /* Red background */
            color: white !important;
            /* White text */
            border: 1px solid #cc0000 !important;
            /* Optional border */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2) !important;
            /* Optional shadow */
        }

        .custom-red-theme {
            background-color: #d32f2f !important;
            /* Dark red background */
            color: white !important;
            padding: 10px;
            border-radius: 5px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('orderChart').getContext('2d');

            var orderData = [{
                "order_type": "0",
                "count": "6",
                "percentage": 42.857142857142854
            }, {
                "order_type": "1",
                "count": "8",
                "percentage": 57.14285714285714
            }];
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
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 36px !important;
        }
    </style>
    <style type="text/css">
        table.pretty5 thead th {
            text-align: center;
            background: #049dd4;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
        }

        table.pretty5 td {
            text-align: center;
            font-size: 11px;
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stats-box1:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* .stats-value1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        } */

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
                    "url": "https://shubhampack.in/beta1/index.php/Dashboard/get_active_dfs",
                    "dataSrc": "data"
                },
                "columns": [{
                        "data": "df_no"
                    },
                    {
                        "data": "df_description"
                    },
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
                "order": [
                    [0, 'asc']
                ],
                "createdRow": function(row, data) {
                    // Fetch tasks and check for overdue status
                    $.ajax({
                        url: '<?php echo page_url . 'Dashboard/get_tasksNew/'; ?>' + data.id,
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
                        url: '<?php echo page_url . 'Dashboard/get_tasks_dfwise/'; ?>' + df_id,
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
                /* Initially hidden */
            }

            /* Popup content container */
            #blocking-overlay-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 20px;
                border-radius: 10px;
                width: 90%;
                height: 80%;
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
                display: none;
                /* Initially hidden */
            }

            /* Countdown timer */
            #countdown {
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 18px;
                font-weight: bold;
                color: black;
                padding: 5px 10px;
                background: #f8d7da;
                border-radius: 5px;
            }

            /* Table styling */
            #popup-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            #popup-table thead th {
                background-color: #f8f9fa;
                text-align: center;
                padding: 10px;
            }

            #popup-table tbody tr {
                text-align: center;
            }

            #popup-table tbody tr:hover {
                background-color: #f1f1f1;
            }

            /* Pagination controls */
            #pagination-controls button {
                margin: 0 5px;
            }

            /* Button styling */
            .table-button {
                text-align: center;
                margin-top: 10px;
            }
        </style>


 <?php
        if ($user_id == 162 || $user_id == 139) {
        } else {?>

        <?php 
$q = $this->db->select('id')->from('popup_logs')->where('user_id', base64_decode($USERIDSS))->where('closedate', date('Y-m-d'))->get();
if ($q->num_rows() > 0) {
    // Popup already shown for the day
} else {

$q1= $this->db->select('id')->from('task_department_wise_scheduling')->where('assigned_user',base64_decode($USERIDSS))->where('task_status',0)->get();

$q2 = $this->db->select('id')->from('communication_ticket_system')->where('user_id',base64_decode($USERIDSS))->where('ticket_status',0)->get();

if($q->num_rows()>0 || $q2->num_rows()>0){

?>

<script>
$(document).ready(function () {
    const rowsPerPage = 5;
    let currentPageTickets = 1;
    let currentPageTasks = 1;

    // Initialize countdown timer
    let countdown = 60;
    const countdownInterval = setInterval(() => {
        $('#countdown').text(`Close available in ${countdown} seconds`);
        countdown--;

        if (countdown < 0) {
            clearInterval(countdownInterval);
            $('#countdown').hide(); // Hide countdown
            $('#close-popup').fadeIn(); // Show close button
        }
    }, 1000);

    // Hide the close button initially
    $('#close-popup').hide();

    // Search functionality
    const searchTable = (inputId, tableBodyId) => {
        $(`#${inputId}`).on('input', function () {
            const value = $(this).val().toLowerCase();
            $(`#${tableBodyId} tr`).filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    };

    // Fetch unresolved tickets and overdue tasks
    $.ajax({
        url: '<?php echo page_url."Task/fetchUnresolvedTickets"; ?>',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log("Response received:", response);
            if (response.status && (response.tickets.length > 0 || response.overdue_tasks.length > 0)) {
                const tickets = response.tickets;
                const tasks = response.overdue_tasks;

                // Populate tickets table
                tickets.forEach((ticket, index) => {
                    $('#tickets-table-body').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${ticket.help_ticket_no}</td>
                            <td>${ticket.df_no}</td>
                            <td>${ticket.task_name}</td>
                            <td>${ticket.remarks}</td>
                            <td>${ticket.title} ${ticket.first_name} ${ticket.last_name}</td>
                            <td>${ticket.added_on}</td>
                        </tr>
                    `);
                });

                // Populate tasks table
                tasks.forEach((task, index) => {
                    $('#tasks-table-body').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${task.df_no}</td>
                            <td>${task.task_name}</td>
                            <td>${task.end_date}</td>
                        </tr>
                    `);
                });

                // Initialize search functionality
                searchTable('ticket-search-input', 'tickets-table-body');
                searchTable('task-search-input', 'tasks-table-body');

                // Show Popup
                $('#blocking-overlay').fadeIn();

                // Close Popup logic
                $(document).on('click', '#close-popup', function () {
                    $('#blocking-overlay').fadeOut();

                    const today = new Date().toISOString().slice(0, 10);
                    localStorage.setItem('popupDisabled', today);

                    $.ajax({
                        url: '<?php echo page_url."Task/closePopup"; ?>',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ tickets }),
                        success: function (response) {
                            console.log('Popup closed and records sent to the server.');
                        },
                        error: function () {
                            console.error('Failed to send ticket data to the server.');
                        }
                    });
                });

                // Button actions
                $('#resolve-btn').on('click', function () {
                    window.location.href = '<?php echo page_url."Task/helpticketsforyou/<?php echo $USERIDSS;?>"; ?>';
                });

                $('#update-task-btn').on('click', function () {
                    window.location.href = '<?php echo page_url."Dashboard"; ?>';
                });
            } else {
                console.log("No unresolved tickets or overdue tasks to show.");
            }
        },
        error: function () {
            console.error('Failed to fetch unresolved tickets and overdue tasks.');
        }
    });
});
</script>

        <div id="blocking-overlay">
            <div id="blocking-overlay-content">
                <div id="countdown">Close available in 60 seconds</div>
                <span id="close-popup">&times;</span>

                <!-- Tickets Section -->
                <h4>Unresolved Help Tickets</h4>
                <input type="text" id="ticket-search-input" placeholder="Search tickets..."
                    style="margin-bottom: 10px; width: 100%; padding: 10px;">
                <table id="tickets-popup-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ticket No</th>
                            <th>DF No</th>
                            <th>Task Name</th>
                            <th>Remarks</th>
                            <th>Created By</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-table-body"></tbody>
                </table>
                <div class="table-button">
                    <button id="resolve-btn" class="btn btn-primary">Resolve Tickets</button>
                </div>

                <!-- Tasks Section -->
                <h4>Overdue Tasks</h4>
                <input type="text" id="task-search-input" placeholder="Search tasks..."
                    style="margin-bottom: 10px; width: 100%; padding: 10px;">
                <table id="tasks-popup-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>DF No</th>
                            <th>Task Name</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody id="tasks-table-body"></tbody>
                </table>
                <div class="table-button">
                    <button id="update-task-btn" class="btn btn-warning">Update Overdue Task</button>
                </div>
            </div>
        </div>

        <?php }?>

<?php }?>

  <?php } ?>

    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <style>
            .chart-container {
                width: 100%;
                max-width: 800px;
                margin: auto;
                background-color: #ffffff;
                padding: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .stats-box {
                padding: 10px 10px;
                color: white;
                border: none;
                border-radius: 15px;
                text-align: center;
                margin-bottom: 30px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s, box-shadow 0.3s;
            }

            .stats-box:hover {
                transform: translateY(-10px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            }

            .stats-box1 {
                padding: 10px 10px;
                color: black;
                border: none;
                border-radius: 15px;
                text-align: center;
                margin-bottom: 30px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s, box-shadow 0.3s;
            }

            .stats-box1:hover {
                transform: translateY(-10px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            }

            /* .stats-value1 {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 10px;
            } */

            .stats-label1 {
                font-size: 14px;
                color: black;
                font-weight: bold;
            }

            .stats-icon {
                font-size: 26px;
                /*      margin-bottom: 20px;*/
                color: rgba(255, 255, 255, 0.8);
            }

            /* .stats-value {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 10px;
            } */

            .stats-label {
                font-size: 14px;
                color: rgba(255, 255, 255, 0.8);
                font-weight: bold;
            }

            hr {
                margin-top: 0px;
                margin-bottom: 10px;
                color: black !important;
                border: 0;
                border-top: 1px solid #c6c2c2;
            }

            .hrclas {
                margin-top: 0px;
                margin-bottom: 10px;
                color: black !important;
                border: 0;
                border-top: 1px solid #000;
            }
        </style>

        <div class="container-fluid">
             <!----************************=================================*******************----->
               <?php
        if ($this->session->userdata['logged_in']['role'] == 12 || $this->session->userdata['logged_in']['user_id'] == 139) {
            ?>
            <div class="row">

                <div class="col-sm-12 change_box">
                    <h3 class="global_heading">
                        DF & Opportunity Information <a
                            href="<?php echo page_url; ?>Dashboard/closeddf"><span
                                class="btn btn-danger btn-xs">Closed
                                DF</span></a>
                    </h3>

                    <div class="row">

                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="<?php echo page_url; ?>Task/dfreleasedashboard/">
                                        <div class="card_box">

                                            <p>Running DF</p>
                                            <div class="stats-value1"><?php
                                                                        $q = $this->db->select('id')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
                                                                        echo $q->num_rows();
                                                                        ?></div>



                                            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m7,13h10v1H7v-1Zm0,5h7v-1h-7v1Zm15-10.707v16.707H2V2.5c0-1.378,1.122-2.5,2.5-2.5h10.207l7.293,7.293Zm-7-.293h5.293L15,1.707v5.293Zm6,16v-15h-7V1H4.5c-.827,0-1.5.673-1.5,1.5v20.5h18Z" />
                                            </svg>

                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6">
                                    <a href="<?php echo page_url; ?>Task/dfreleasedashboard/1">
                                        <div class="card_box">
                                            <p>DF Delayed</p>
                                            <div class="stats-value1"><?php
                                                                        $show = array();
                                                                        $show[] = 0;
                                                                        $m = 1;
                                                                        $reportid = $this->uri->segment(3);
                                                                        $q = $this->db->select('id, df_no, added_on, df_upload, added_on')->from('df_release')->where('on_hold', 0)->where('df_status', 0)->get();
                                                                        if ($q->num_rows() > 0) {
                                                                            foreach ($q->result() as $rows) {

                                                                                $q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
                                                                                if ($q1->num_rows() > 0) {
                                                                                    foreach ($q1->result() as $r);
                                                                                    $planneddate = date('d-m-Y', strtotime($r->enddate));
                                                                                } else {
                                                                                    $planneddate = '';
                                                                                }


                                                                                $q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
                                                                                $count = $q1->num_rows();
                                                                                $delaycount = array();
                                                                                $delaycount[] = 0;
                                                                                $totaldayscountarray = array();
                                                                                $totaldayscountarray[] = 0;
                                                                                $q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->where('task_status', 1)->get();
                                                                                $totaldone = $q2->num_rows();
                                                                                $percetage =  round($totaldone * 100 / $count);

                                                                                foreach ($q2->result() as $rowss) {
                                                                                    if (date('Y-m-d', strtotime($rowss->task_completed_on)) > $rowss->end_date) {
                                                                                        $delaycount[] = 1;

                                                                                        $daysss = $CIA->Task_model->getDays($rowss->end_date, date('Y-m-d', strtotime($rowss->task_completed_on)), 1);

                                                                                        $totaldayscountarray[] = $daysss;
                                                                                    }
                                                                                }
                                                                                $delayed = array_sum($delaycount);
                                                                                $totaldaysdelayed = array_sum($totaldayscountarray);

                                                                                $totaldelayedpercentage = round($delayed * 100 / $totaldone);

                                                                                $expecteddate =  date('d-m-Y', strtotime($planneddate . ' +' . $totaldaysdelayed . ' Days'));
                                                                                $skipped_dates = $CIA->Task_model->SKIPsingle_holidays($expecteddate);
                                                                                if ($totaldelayedpercentage > 0) {
                                                                                    $show[] = 1;
                                                                                } else {
                                                                                    $show[] = 0;
                                                                                }
                                                                            }
                                                                        }

                                                                        echo array_sum($show);
                                                                        ?></div>
                                            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m7,13h10v1H7v-1Zm0,5h7v-1h-7v1Zm15-10.707v16.707H2V2.5c0-1.378,1.122-2.5,2.5-2.5h10.207l7.293,7.293Zm-7-.293h5.293L15,1.707v5.293Zm6,16v-15h-7V1H4.5c-.827,0-1.5.673-1.5,1.5v20.5h18Z" />
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card_box">
                                        <p>Dept. Delay</p>
                                        <div class="stats-value1">0</div>
                                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="m7,13h10v1H7v-1Zm0,5h7v-1h-7v1Zm15-10.707v16.707H2V2.5c0-1.378,1.122-2.5,2.5-2.5h10.207l7.293,7.293Zm-7-.293h5.293L15,1.707v5.293Zm6,16v-15h-7V1H4.5c-.827,0-1.5.673-1.5,1.5v20.5h18Z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card_box">
                                        <p>DF with IOM</p>
                                        <div class="stats-value1">0</div>
                                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="m7,13h10v1H7v-1Zm0,5h7v-1h-7v1Zm15-10.707v16.707H2V2.5c0-1.378,1.122-2.5,2.5-2.5h10.207l7.293,7.293Zm-7-.293h5.293L15,1.707v5.293Zm6,16v-15h-7V1H4.5c-.827,0-1.5.673-1.5,1.5v20.5h18Z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card_box">
                                        <p>Loss Against Delay</p>
                                        <div class="stats-value1">0</div>
                                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="m7,13h10v1H7v-1Zm0,5h7v-1h-7v1Zm15-10.707v16.707H2V2.5c0-1.378,1.122-2.5,2.5-2.5h10.207l7.293,7.293Zm-7-.293h5.293L15,1.707v5.293Zm6,16v-15h-7V1H4.5c-.827,0-1.5.673-1.5,1.5v20.5h18Z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <a href="<?php echo page_url; ?>Reporting/penalitydf">
                                        <div class="card_box">
                                            <p>Penality DF</p>
                                            <div class="stats-value1"><?php
                                                                        $q = $this->db->select('id')->from('poreceived')->where('penalityamount!=', 0)->get();
                                                                        echo $q->num_rows();
                                                                        ?></div>
                                            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m7,13h10v1H7v-1Zm0,5h7v-1h-7v1Zm15-10.707v16.707H2V2.5c0-1.378,1.122-2.5,2.5-2.5h10.207l7.293,7.293Zm-7-.293h5.293L15,1.707v5.293Zm6,16v-15h-7V1H4.5c-.827,0-1.5.673-1.5,1.5v20.5h18Z" />
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-12">

                                    <div class="card_box">
                                        <p>Running DF(s) Value</p>
                                        <div class="stats-value1"><?php
                                                                    $amount = array();
                                                                    $amount[] = 0;
                                                                    $q = $this->db->select('b.order_value')->from('df_release a')->join('poreceived b', 'a.id=b.df_id')->where('df_status', 0)->get();
                                                                    if ($q->num_rows() > 0) {
                                                                        foreach ($q->result() as $row) {
                                                                            $amount[] = $row->order_value;
                                                                        }
                                                                    }

                                                                    function formatIndianNumber($number)
                                                                    {
                                                                        $number_parts = explode(".", $number);
                                                                        $integer_part = $number_parts[0];
                                                                        $decimal_part = isset($number_parts[1]) ? '.' . $number_parts[1] : '';

                                                                        // Handle negative numbers
                                                                        $negative = '';
                                                                        if ($integer_part[0] == '-') {
                                                                            $negative = '-';
                                                                            $integer_part = substr($integer_part, 1);
                                                                        }

                                                                        // Split the integer part into 3 digits for the last group and 2 digits thereafter
                                                                        $lastThree = substr($integer_part, -3);
                                                                        $restUnits = substr($integer_part, 0, -3);

                                                                        if ($restUnits != '') {
                                                                            $lastThree = ',' . $lastThree;
                                                                        }

                                                                        $result = $negative . preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits) . $lastThree . $decimal_part;
                                                                        return $result;
                                                                    }

                                                                    $totalamount = array_sum($amount);
                                                                    $val = formatIndianNumber($totalamount);
                                                                    echo $val;
                                                                    ?></div>
                                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="m7,13h10v1H7v-1Zm0,5h7v-1h-7v1Zm15-10.707v16.707H2V2.5c0-1.378,1.122-2.5,2.5-2.5h10.207l7.293,7.293Zm-7-.293h5.293L15,1.707v5.293Zm6,16v-15h-7V1H4.5c-.827,0-1.5.673-1.5,1.5v20.5h18Z" />
                                        </svg>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <h3 class="global_heading">
                        New Business Opportunity
                    </h3>
                    <div class="row days">
                        <div class="col-sm-6">
                            <a href="<?php echo page_url; ?>">
                                <div class="card_box">
                                    <p>New Opportunity in Last 15 days</p>
                                    <div class="stats-value"> <?php
                                                                $date = new DateTime();
                                                                $date->modify('-15 days');
                                                                $createdate =  $date->format('Y-m-d');
                                                                $q = $this->db->select('id')->from('leads')->where('create_date>=', $createdate)->get();
                                                                echo $q->num_rows();
                                                                ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <?php
                            $date = new DateTime();
                            $date->modify('-15 days');
                            $createdate =  $date->format('Y-m-d');
                            ?>
                            <a href="<?php echo page_url; ?>Leads/lead_stages/33">
                                <div class="card_box">
                                    <p>Quotation Sent in Last 15 days</p>
                                    <div class="stats-value"><?php
                                                                $stageid = 33;
                                                                $last15daysquotationcount = $DI->Dashboard_model->lead_stage_counts_in_15days($createdate, $stageid);
                                                                echo $last15daysquotationcount;
                                                                ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-12">
                            <?php
                            $date = new DateTime();
                            $date->modify('-15 days');
                            $enddate =  $date->format('Y-m-d');
                            $startdate = date('Y-m-d');
                            ?>
                            <a
                                href="<?php echo page_url; ?>Task/receivedpolist/<?php echo base64_encode($startdate); ?>/<?php echo base64_encode($enddate); ?>">
                                <div class="card_box">
                                    <p>Opportunity Converted to Sales in last 15 Days</p>
                                    <div class="stats-value"> <?php
                                                                $orderval[] = 0;
                                                                $date = new DateTime();
                                                                $date->modify('-15 days');
                                                                $enddate =  $date->format('Y-m-d');
                                                                $startdate = date('Y-m-d');

                                                                $q = $this->db->select('a.lead_id')->from('progress_remarks a')->where('lead_status', 35)->where('a.added_on BETWEEN "' . $startdate . '" and "' . $enddate . '"')->group_by('lead_id')->get();
                                                                foreach ($q->result() as $row) {
                                                                    $q2 = $this->db->select('order_value')->from('poreceived')->where('lead_id', $row->lead_id)->get();
                                                                    if ($q2->num_rows() > 0) {
                                                                        foreach ($q2->result() as $row2) {
                                                                            $orderval[] = $row2->order_value;
                                                                        }
                                                                    }
                                                                }
                                                                echo array_sum($orderval);
                                                                ?></div>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>

                <div class="col-sm-6">
                    <h3 class="global_heading">
                        Factory Information
                    </h3>
                    <div class="row machine">
                        <div class="col-sm-6">
                            <a href="<?php echo page_url; ?>Reporting/machinereadyonfloor">
                                <div class="card_box">
                                    <p>Ready Machine(s) on Floor</p>
                                    <div class="stats-value"><?php
                                                                $readyonfloor = array();
                                                                $readyonfloor[] = 0;
                                                                $q = $this->db->select('task_id')->from('task_management')->where('machinereadyonfloor', 1)->get();
                                                                if ($q->num_rows() > 0) {
                                                                    foreach ($q->result() as $row);
                                                                    $q2 = $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b', 'a.df_id=b.id', 'left')->where('a.taskid', $row->task_id)->where('a.task_status', 1)->where('b.df_status', 0)->get();
                                                                    if ($q2->num_rows() > 0) {
                                                                        foreach ($q2->result() as $row2) {
                                                                            $readyonfloor[] = 1;
                                                                        }
                                                                    }
                                                                ?>
                                        <?php
                                                                }
                                                                echo array_sum($readyonfloor);
                                        ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="<?php echo page_url; ?>Reporting/dispatchinnext15days">
                                <div class="card_box">
                                    <p>Dispatch in Next 15 Days</p>
                                    <div class="stats-value"><?php
                                                                $dispatchcount = array();
                                                                $dispatchcount[] = 0;
                                                                $originalDate = date('Y-m-d');
                                                                $date = new DateTime($originalDate);
                                                                $date->modify('+15 days');
                                                                $newDate = $date->format('Y-m-d');
                                                                $q = $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid', 103)->where('end_date BETWEEN "' . date('Y-m-d', strtotime($originalDate)) . '" and "' . date('Y-m-d', strtotime($newDate)) . '"')->get();
                                                                if ($q->num_rows() > 0) {
                                                                    foreach ($q->result() as $row) {
                                                                        $dispatchcount[] = 1;
                                                                    }
                                                                }
                                                                echo array_sum($dispatchcount);

                                                                ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-12">
                            <a href="<?php echo page_url; ?>Reporting/fatsinnext15days">
                                <div class="card_box">
                                    <p>FAT(s) in Next 15 Days</p>
                                    <div class="stats-value"><?php
                                                                $dispatchcount = array();
                                                                $dispatchcount[] = 0;
                                                                $originalDate = date('Y-m-d');
                                                                $date = new DateTime($originalDate);
                                                                $date->modify('+15 days');
                                                                $newDate = $date->format('Y-m-d');
                                                                $q = $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid', 93)->where('end_date BETWEEN "' . date('Y-m-d', strtotime($originalDate)) . '" and "' . date('Y-m-d', strtotime($newDate)) . '"')->get();
                                                                if ($q->num_rows() > 0) {
                                                                    foreach ($q->result() as $row) {
                                                                        $dispatchcount[] = 1;
                                                                    }
                                                                }
                                                                echo array_sum($dispatchcount);

                                                                ?></div>
                                </div>
                            </a>
                        </div>

                    </div>

                </div>


                <div class="col-sm-6">

                    <h3 class="global_heading">
                        Sales (Export/Domestic)
                    </h3>
                    <div class="card_box" style="height: 390px;">
                        <div style="width: 60%; margin: auto;">
                            <canvas id="orderChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
  <?php 
                $year = date('Y');
                $month = date('m');
                $date = new DateTime("$year-$month-01");
                $date->modify('last day of this month');
                $ldate = $date->format('Y-m-d');
                $ssdate = date('Y-m')."-01";
                
            ?>
                    <h3 class="global_heading">
                        Company Revenue Information <a
                            href="<?php echo page_url;?>Dashboard/df_dispatch_report/<?php echo $ssdate;?>/<?php echo $ldate;?>"><span
                                class="btn btn-danger btn-xs">DF Dispatch Report</span></a>
                    </h3>

                    <div class="card_box" style="height: 390px;">

                        <div class="row company">
                            <div class="col-sm-6">
                                <p>Monthly Target - 10cr.</p>
                            </div>
                            <div class="col-sm-6">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="red"></div>
                                        </td>
                                        <td>UP TO 5 CR.</td>
                                        <td>
                                            <div class="yellow"></div>
                                        </td>
                                        <td>UPTO 9 CR.</td>
                                        <td>
                                            <div class="green"></div>
                                        </td>
                                        <td>ABOVE 9 CR.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div style="width: 100%; margin: auto; margin-top: 20px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');

            const salesData = <?php echo json_encode($sales_data); ?>;
            const months = salesData.map(data => data.month);
            const orderValues = salesData.map(data => parseFloat(data.order_value));

            const colors = orderValues.map(value => {
                if (value <= 50000000) {
                    return '#FF5733';
                } else if (value < 90000000) {
                    return '#E8EC00';
                } else {
                    return '#2B8B3B';
                }
            });

            const formatIndianNumber = (num) => {
                num = num.toString();
                let lastThree = num.substring(num.length - 3);
                let otherNumbers = num.substring(0, num.length - 3);
                if (otherNumbers !== '') {
                    lastThree = ',' + lastThree;
                }
                let res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
                return '₹' + res;
            };

            const chartData = {
                labels: months,
                datasets: [{
                    label: 'Monthly Sales (in ₹)',
                    data: orderValues,
                    backgroundColor: colors
                }]
            };

            const config = {
                type: 'bar',
                data: chartData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatIndianNumber(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += formatIndianNumber(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>

        <div class="margin_btw"></div>
        <div class="container-fluid">



            <div class="row">

                <div class="col-sm-7">
                    <h3 class="global_heading">
                        Payment Information
                    </h3>

                    <div class="row machine">
                        <div class="col-sm-5">

                            <div class="card_box">
                                <p>Payment Credited in Last 15 Days</p>
                                <div class="stats-value"><?php
                                                            $totalrecaivedamount[] = 0;
                                                            $paymentcreditedin15days = $DI->Dashboard_model->paymentcreditedin15days();
                                                            foreach ($paymentcreditedin15days as $row) {
                                                                $totalrecaivedamount[] = $row->amount_received;
                                                            }
                                                            $ttlrcvamt =  array_sum($totalrecaivedamount);

                                                            $AMT = $DI->Dashboard_model->formatIndianNumber($ttlrcvamt);
                                                            echo $AMT;

                                                            ?></div>
                            </div>

                        </div>
                        <div class="col-sm-4">
                            <a href="<?php echo page_url; ?>Reporting/paymentsinnext15days">
                                <div class="card_box">
                                    <p>Payment in Next 15 Days</p>
                                    <div class="stats-value"><?php
                                                                // $upcomingtotalamount[] = 0;
                                                                // $paymentinnext15days = $DI->Dashboard_model->paymentinnext15days();
                                                                // foreach($paymentinnext15days as $row){
                                                                //   $upcomingtotalamount[] = $row->amount_received;
                                                                // }
                                                                // $upcomingttlamount =  array_sum($upcomingtotalamount);

                                                                // $Upcomettlamt = $DI->Dashboard_model->formatIndianNumber($upcomingttlamount);
                                                                // echo $Upcomettlamt;
                                                                $next15dayspayment = array();
                                                                $next15dayspayment[] = 0;

                                                                $date = new DateTime();
                                                                $date->modify('+15 days');
                                                                $createdate =  $date->format('Y-m-d');
                                                                $enddate = $createdate;
                                                                $startdate = date('Y-m-d');
                                                                $q = $this->db->select('b.id, b.df_no, b.added_on, b.df_upload, b.added_on')->from('task_department_wise_scheduling a')->join('df_release b', 'a.df_id=b.id', 'left')->where('a.paymentstage', 1)->where('a.end_date BETWEEN "' . date('Y-m-d', strtotime($startdate)) . '" and "' . date('Y-m-d', strtotime($enddate)) . '"')->where('b.df_status', 0)->get();
                                                                if ($q->num_rows() > 0) {
                                                                    foreach ($q->result() as $row) {
                                                                        $next15dayspayment[] = 1;
                                                                    }
                                                                }

                                                                echo array_sum($next15dayspayment);
                                                                ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-3">

                            <div class="card_box">
                                <p>Payment Overdue</p>
                                <div class="stats-value">1</div>
                            </div>

                        </div>

                    </div>
                </div>
 <? }
        ?>
                <!----************************=================================*******************----->
                <?php

$class = ($this->session->userdata('logged_in')['user_id'] == 161 || $this->session->userdata('logged_in')['user_id'] == 61) ? 'col-md-5' : 'col-md-12';
?>
                <div class="<?php echo $class; ?>">
                    <h3 class="global_heading">
                        <a href="<?php echo page_url; ?>Maintenance_support/"
                            class=" btn btn-success btn-xs">Raise Help Ticket Against DF</a>
                    </h3>
                    <?php
                    $oneweek = date('Y-m-d', strtotime('-7 days'));
                    ?>
                    <div class="row machine">
                        <div class="col-sm-4">
                            <a href="<?php echo page_url; ?>Task/dfreleasedashboard/">
                                <div class="card_box">
                                    <p>Running DF</p>
                                    <div class="stats-value"><?php
                                                                $q = $this->db->select('id')->from('df_release')->where('df_status', 0)->where('on_hold', 0)->get();
                                                                echo $q->num_rows();
                                                                ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="<?php echo page_url; ?>Task/onholdf/">
                                <div class="card_box">
                                    <p>On Hold DF</p>
                                    <div class="stats-value"><?php
                                                                $q = $this->db->select('id')->from('df_release')->where('df_status', 0)->where('on_hold', 1)->get();
                                                                echo $q->num_rows();
                                                                ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            if ($is_admin == 1) {
                                $useriddata = "";
                            } else {
                                $useriddata = $user_id;
                            }

                            $enuser = base64_encode($useriddata);
                            ?>
                            <a href="<?php echo page_url; ?>Task/viewallrunninghelptickets/<?php echo $enuser; ?>">
                                <div class="card_box">
                                    <p><?php if ($is_admin == 1) {
                                        } else { ?><?php echo "Your";
                                                                    } ?> PMS Help Tickets</p>
                                    <div class="stats-value"><?php
                                                                echo $counthelptickets;
                                                                ?></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <?php if ($is_admin == 1) {
                            } else { ?>
                                <a href="<?php echo page_url; ?>Task/helpticketsforyou/<?php echo $enuser; ?>">
                                    <div class="card_box">
                                        <p>PMS Help Tickets for <?php if ($is_admin == 2) {
                                                                    echo "Team";
                                                                } else {
                                                                    echo "You";
                                                                }; ?> </p>
                                        <div class="stats-value"><?php
                                                                    echo $counthelpticketsforyou; ?></div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="col-sm-4">



                            <?php
                            $teammemberid = array();
                            $teamids = array();

                            // Get the team IDs for the given team leader
                            $q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();

                            if ($q->num_rows() > 0) {
                                foreach ($q->result() as $teamleaderdata) {
                                    $teamids[] = $teamleaderdata->team_id;
                                }
                                // echo "<pre>"; print_r($teamids);

                                // Get the employee IDs for the teams
                                $q = $this->db->select('employee_id')->from('presto_team_members')->where_in('team_id', $teamids)->get();
                                if ($q->num_rows() > 0) {
                                    foreach ($q->result() as $teammemberdata) {
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
                                <a href="<?php echo page_url; ?>Task/helpticketsforyourteam/<?php echo $enuser; ?>">
                                    <div class="card_box">
                                        <p>PMS Help Tickets for your Team </p>
                                        <div class="stats-value"><?php
                                                                    echo  $totalticketforteam; ?></div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            $teammemberid = array();
                            $teamids = array();

                            // Get the team IDs for the given team leader
                            $q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();

                            if ($q->num_rows() > 0) {
                                foreach ($q->result() as $teamleaderdata) {
                                    $teamids[] = $teamleaderdata->team_id;
                                }
                                // echo "<pre>"; print_r($teamids);

                                // Get the employee IDs for the teams
                                $q = $this->db->select('employee_id')->from('presto_team_members')->where_in('team_id', $teamids)->get();
                                if ($q->num_rows() > 0) {
                                    foreach ($q->result() as $teammemberdata) {
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
                                <a href="<?php echo page_url; ?>Task/helpticketsforyourteam/<?php echo $enuser; ?>">
                                    <div class="card_box">
                                        <p>PMS Help Tickets Raised by Your Team</p>
                                        <div class="stats-value"><?php
                                                                    echo  $totalticketforteam; ?></div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>

                    </div>
                </div>



                <div class="col-sm-12">

                    <div class="margin_btw"></div>
                    <div class="card_box">
                        <div class="tab">
                            <div class="row no-gutters">
                                <?php
                                if ($_SESSION['logged_in']['adminuser'] == 1 || $_SESSION['logged_in']['adminuser'] == 2) {
                                    $default1 = "defaultOpen";
                                    $default2 = '';
                                } else {
                                    $default2 = "defaultOpen";
                                    $default1 = '';
                                }
                                if ($_SESSION['logged_in']['adminuser'] == 1 || $_SESSION['logged_in']['adminuser'] == 2) { ?>
                                <div class="col">
                                    <button class="tablinks" onclick="openCity(event, 'unassigned')" id="<?php echo $default1; ?>">
                                        <!-- <img src="<?php echo assets_url; ?>task.png"alt=""> -->
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m4,7h3c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2Zm-1-5c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1v-3Zm6,12v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2h3c1.103,0,2-.897,2-2Zm-6,0v-3c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1Zm10-7h3c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2Zm-1-5c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1v-3Zm1.5,3h2c.276,0,.5-.224.5-.5v-2c0-.276-.224-.5-.5-.5h-2c-.276,0-.5.224-.5.5v2c0,.276.224.5.5.5Zm.5-2h1v1h-1v-1Zm10,20.5c0,.276-.224.5-.5.5s-.5-.224-.5-.5c0-1.637-.994-3.026-2.596-3.627l-5.08-1.905c-.195-.073-.324-.26-.324-.468v-5.893c0-.789-.535-1.471-1.244-1.587-.45-.07-.886.046-1.227.336-.337.286-.529.703-.529,1.144v8.424c0,.421-.235.796-.615.979-.379.18-.819.132-1.146-.13,0,0-1.716-1.367-1.719-1.371-.606-.562-1.553-.529-2.115.073-.565.604-.534,1.557.064,2.118l1.633,1.551c.325.309.107.856-.342.856-.127,0-.249-.048-.341-.135l-1.64-1.548c-1-.937-1.048-2.518-.106-3.524.928-.994,2.482-1.054,3.49-.149.003.002,1.698,1.347,1.698,1.347l.138-.066v-8.424c0-.734.321-1.429.881-1.905.561-.476,1.307-.68,2.035-.561,1.188.193,2.084,1.3,2.084,2.573v5.546l4.756,1.784c2.001.75,3.244,2.498,3.244,4.562Z" />
                                            </svg></div>
                                        <div>
                                            Un-Assigned DF<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="unassignednotificationcount"></span>
                                        </div>
                                    </button>
                                </div>
  <?php } ?>
                                <div class="col">
                                    <button class="tablinks" onclick="openCity(event, 'going')" id="<?php echo $default2; ?>">
                                        <!-- <img src="<?php echo assets_url; ?>task.png" alt=""> -->
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m13,11h4v1h-4v-1ZM21,2v19.5c0,1.378-1.121,2.5-2.5,2.5H5.5c-1.379,0-2.5-1.122-2.5-2.5V2h5.05c.232-1.14,1.243-2,2.45-2h3c1.207,0,2.218.86,2.45,2h5.05Zm-1,1h-5v-.5c0-.827-.673-1.5-1.5-1.5h-3c-.827,0-1.5.673-1.5,1.5v.5h-5v18.5c0,.827.673,1.5,1.5,1.5h13c.827,0,1.5-.673,1.5-1.5V3Zm-11.216,7.952c-.056.056-.18.055-.241-.006l-1.778-1.721-.695.719,1.772,1.716c.221.22.514.341.825.341s.604-.122.821-.339l3.362-3.305-.701-.713-3.365,3.308Zm4.216,7.048h4v-1h-4v1Zm-4.216-1.048c-.056.057-.18.055-.241-.006l-1.778-1.721-.695.719,1.772,1.716c.221.22.514.341.825.341s.604-.122.821-.339l3.362-3.305-.701-.713-3.365,3.308Z" />
                                            </svg>
                                        </div>
                                        <div>
                                            On Going
                                            Tasks<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="ongoingtaskcountnotification"></span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col">
                                    <button class="tablinks" onclick="openCity(event, 'overdue')">

                                        <!-- <img src="<?php echo assets_url; ?>overdue.png" alt=""> -->

                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m13,12h-6v-1h6v1ZM0,17h5v5H0v-5Zm1,4h3v-3H1v3Zm6-1h10v-1H7v1Zm0-17v1h17v-1H7ZM0,9h5v5H0v-5Zm1,4h3v-3H1v3ZM0,1h5v5H0V1Zm1,4h3v-3H1v3Zm17.439,1.439l-3.442,3.442.707.707,3.296-3.295v16.707h1V7.292l3.276,3.277.707-.707-3.423-3.424c-.586-.583-1.537-.583-2.121,0Z" />
                                            </svg></div>
                                        <div>
                                            Overdue
                                            tasks<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="overduetaskcountnotification"></span>
                                        </div>


                                    </button>
                                </div>
                                <div class="col">
                                    <button class="tablinks" onclick="openCity(event, 'complete')">
                                        <!-- <img src="<?php echo assets_url; ?>complete.png" alt=""> -->

                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m15.95,2c-.232-1.14-1.243-2-2.45-2h-3c-1.208,0-2.217.86-2.45,2H3v19.5c0,1.378,1.122,2.5,2.5,2.5h13c1.379,0,2.5-1.122,2.5-2.5V2h-5.05Zm4.05,19.5c0,.827-.673,1.5-1.5,1.5H5.5c-.827,0-1.5-.673-1.5-1.5V3h5v-.5c0-.827.673-1.5,1.5-1.5h3c.827,0,1.5.673,1.5,1.5v.5h5v18.5Zm-9.021-6.714l5.901-5.797.701.714-5.898,5.793c-.336.335-.778.503-1.22.503s-.887-.168-1.225-.506l-2.815-2.71.693-.721,2.822,2.717c.292.292.756.292,1.041.007Z" />
                                            </svg>
                                        </div>
                                        <div>Completed
                                            Tasks<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="completeddfnotificationcount">20</span></div>
                                    </button>
                                </div>

                                <div class="col">
                                    <button class="tablinks" onclick="openCity(event, 'completeandpendingforapproval')">
                                        <!-- <img src="<?php echo assets_url; ?>complete.png" alt=""> -->

                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="m17.5,7c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h3.692c-2.023-3.091-5.474-5-9.192-5C5.935,1,1,5.935,1,12s4.935,11,11,11c1.5,0,2.289-.22,2.901-.387.276-.07.542.086.614.351.072.267-.085.542-.351.614-.668.182-1.665.422-3.165.422C5.383,24,0,18.617,0,12S5.383,0,12,0c4.04,0,7.789,2.066,10,5.414V1.5c0-.276.224-.5.5-.5s.5.224.5.5v4c0,.827-.673,1.5-1.5,1.5h-4Zm6,4.5c-.276,0-.5.224-.5.5,0,.64-.055,1.28-.163,1.903-.048.272.135.531.406.578.029.006.058.008.087.008.238,0,.449-.171.491-.414.119-.681.179-1.378.179-2.075,0-.276-.224-.5-.5-.5Zm-.855,4.891c-.25-.118-.548-.009-.664.242-.269.577-.589,1.134-.953,1.655-.158.227-.103.538.124.696.087.061.187.09.286.09.157,0,.313-.074.41-.214.397-.569.747-1.177,1.039-1.806.116-.251.008-.548-.242-.664Zm-3.547,4.013c-.486.412-1.011.782-1.56,1.103-.238.139-.319.444-.18.684.093.159.26.248.432.248.086,0,.173-.021.252-.068.599-.349,1.171-.753,1.702-1.202.211-.179.237-.494.059-.705-.178-.209-.494-.234-.705-.059Zm-2.357-6.342l-4.24-2.32v-6.241c0-.276-.224-.5-.5-.5s-.5.224-.5.5v6.538c0,.183.1.351.26.438l4.5,2.462c.076.042.158.062.24.062.177,0,.348-.094.438-.26.133-.242.044-.546-.198-.679Z" />
                                            </svg></div>
                                        <div>
                                            Completed &
                                            Pending for Approval<span class="counter"
                                                style="background-color:orange;font-weight: bold;color:white;"
                                                id="completeddfnotificationcountforapproval">35</span>
                                        </div>

                                    </button>
                                </div>



                            </div>
                        </div>
                         <?php
                        if ($_SESSION['logged_in']['adminuser'] == 1 || $_SESSION['logged_in']['adminuser'] == 2) { ?>
                        <div id="unassigned" class="tabcontent">
                            <div class="table__header table-responsive">
                                <table id="example4" class="table table-striped pretty">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>DF No.</th>
                                            <th>DF Realease Date</th>
                                            <th>Download DF</th>
                                            <th>Assignment</th>

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

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DF NO.</label>
                                            <select class="form-control task_dfno_filter" id="task_dfno_filter"
                                                onchange="filter_ongoing_task();">
                                                <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY (DUE DATE)</label>
                                            <select class="form-control" id="task_date_filter"
                                                onchange="filter_ongoing_task();">
                                                <option value="ALL">ALL</option>
                                                <option value="1">TASK DUE TODAY</option>
                                                <option value="2">TASK DUE THIS WEEK</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                            <select class="form-control task_dfno_filter" id="task_department_filter"
                                                onchange="filter_ongoing_task(); getusers();">

                                               <?php if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
                                                <?php
                                                $leaderdepartment[] = 0;
                                                $q33 = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                                                if ($q33->num_rows() > 0) {
                                                    foreach ($q33->result() as $rowssss) {
                                                        $leaderdepartment[] = $rowssss->department_id;
                                                    }
                                                }
                                                $this->db->select('department, department_id')->from('departments')->where('status', 1)->where('department_id!=', 10);
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $departmentid = $_SESSION['logged_in']['department_id'];
                                                    $this->db->where('department_id', $departmentid);
                                                }
                                                if ($_SESSION['logged_in']['adminuser'] == 2 && $leaderdepartment <> 0) {


                                                    $this->db->where_in('department_id', $leaderdepartment, false);
                                                }

                                                $q = $this->db->where('business_loc_id', 2)->order_by('department', 'ASC')->get();
                                                foreach ($q->result() as $ros) { ?>
                                                    <option value="<?php echo $ros->department_id; ?>"><?php echo strtoupper($ros->department); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getusers() {
                                            var department = $("#task_department_filter").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
                                                    $("#task_user_filter").html(data);
                                                }
                                            });
                                        }
                                    </script>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY USERS</label>
                                            <select class="form-control" id="task_user_filter"
                                                onchange="filter_ongoing_task();">
                                                  <?php
                                                $user_id = $this->session->userdata['logged_in']['user_id'];
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id', $user_id)->get();
                                                    foreach ($q->result() as $rowss);
                                                ?>
                                                    <option value="<?php echo $rowss->user_id; ?>"><?php echo strtoupper($rowss->first_name . " " . $rowss->last_name); ?></option>
                                                <?php } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
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

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BYDF NO.</label>
                                            <select class="form-control task_dfno_filter" id="task_dfno_filter_overdue"
                                                onchange="filter_overdue_task();">
                                               <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY</label>
                                            <select class="form-control" id="task_number_filter_overdue"
                                                onchange="filter_overdue_task(); checkother();">
                                                <option value="ALL">ALL</option>
                                                <option value="1">OTHER</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function checkother() {
                                            var task_number_filter_overdue = $("#task_number_filter_overdue").val();
                                            if (task_number_filter_overdue == 1) {
                                                $("#showno").show();
                                            } else {
                                                $("#showno").hide();
                                            }
                                        }
                                    </script>
                                    <div class="col-md-2" style="display: none;" id="showno">
                                        <div class="form-group">
                                            <label style="color:#000;">NO OF DAYS</label>
                                            <input type="number" class="form-control" id="noofdaysdue" value="2"
                                                onblur="filter_overdue_task();">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                            <select class="form-control task_dfno_filter"
                                                id="task_department_filter_overdue"
                                                onchange="filter_overdue_task(); getusersss();">

                                               <?php if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
                                                <?php
                                                $leaderdepartment[] = 0;
                                                $q33 = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                                                if ($q33->num_rows() > 0) {
                                                    foreach ($q33->result() as $rowssss) {
                                                        $leaderdepartment[] = $rowssss->department_id;
                                                    }
                                                }
                                                $this->db->select('department, department_id')->from('departments')->where('status', 1)->where('department_id!=', 10);
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $departmentid = $_SESSION['logged_in']['department_id'];
                                                    $this->db->where('department_id', $departmentid);
                                                }
                                                if ($_SESSION['logged_in']['adminuser'] == 2 && $leaderdepartment <> 0) {


                                                    $this->db->where_in('department_id', $leaderdepartment, false);
                                                }

                                                $q = $this->db->where('business_loc_id', 2)->order_by('department', 'ASC')->get();
                                                foreach ($q->result() as $ros) { ?>
                                                    <option value="<?php echo $ros->department_id; ?>"><?php echo strtoupper($ros->department); ?></option>
                                                <?php } ?>

                                            </select>

                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getusers() {
                                            var department = $("#task_department_filter").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
                                                    $("#task_user_filter").html(data);
                                                }
                                            });
                                        }

                                        function getusersss() {
                                            var department = $("#task_department_filter_overdue").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
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
                                                $user_id = $this->session->userdata['logged_in']['user_id'];
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id', $user_id)->get();
                                                    foreach ($q->result() as $rowss);
                                                ?>
                                                    <option value="<?php echo $rowss->user_id; ?>"><?php echo strtoupper($rowss->first_name . " " . $rowss->last_name); ?></option>
                                                <?php } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
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

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DF NO.</label>
                                            <select class="form-control task_dfno_filter" id="task_dfno_filter_completed" onchange="filter_completed_task();">
                                                <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                           <select class="form-control task_dfno_filter" id="task_department_filter_completed" onchange="filter_completed_task(); getuserscompleted();">
                                                <?php if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
                                                <?php
                                                $leaderdepartment[] = 0;
                                                $q33 = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                                                if ($q33->num_rows() > 0) {
                                                    foreach ($q33->result() as $rowssss) {
                                                        $leaderdepartment[] = $rowssss->department_id;
                                                    }
                                                }
                                                $this->db->select('department, department_id')->from('departments')->where('status', 1)->where('department_id!=', 10);
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $departmentid = $_SESSION['logged_in']['department_id'];
                                                    $this->db->where('department_id', $departmentid);
                                                }
                                                if ($_SESSION['logged_in']['adminuser'] == 2 && $leaderdepartment <> 0) {


                                                    $this->db->where_in('department_id', $leaderdepartment, false);
                                                }

                                                $q = $this->db->where('business_loc_id', 2)->order_by('department', 'ASC')->get();
                                                foreach ($q->result() as $ros) { ?>
                                                    <option value="<?php echo $ros->department_id; ?>"><?php echo strtoupper($ros->department); ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getuserscompleted() {
                                            var department = $("#task_department_filter_completed").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
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
                                                $user_id = $this->session->userdata['logged_in']['user_id'];
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id', $user_id)->get();
                                                    foreach ($q->result() as $rowss);
                                                ?>
                                                    <option value="<?php echo $rowss->user_id; ?>"><?php echo strtoupper($rowss->first_name . " " . $rowss->last_name); ?></option>
                                                <?php } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="table__header">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        <table style="width:100%;border:none !important;">
                                            <thead>
                                                <tr>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c1">&nbsp;</span>&nbsp;ON TIME</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c2">&nbsp;</span>&nbsp;DELAYED</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c3">&nbsp;</span>&nbsp;EARLY</th>
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

                                <div class="col-md-12"
                                    style="border:1px solid #afafaf;padding:10px; background-color: #f2f2f2;">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DF NO.</label>
                                          <select class="form-control task_dfno_filter" id="task_dfno_filter_completed" onchange="filter_completed_task();">
                                                <option value="ALL">ALL</option>
                                                <?php if (count($runningdfno) > 0) {
                                                    foreach ($runningdfno as $row) { ?>
                                                        <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->df_no); ?></option>
                                                <?php  }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label style="color:#000;">FILTER BY DEPARTMENT</label>
                                             <select class="form-control task_dfno_filter" id="task_department_filter_completed" onchange="filter_completed_task(); getuserscompleted();">
                                                <?php if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
                                                <?php
                                                $leaderdepartment[] = 0;
                                                $q33 = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                                                if ($q33->num_rows() > 0) {
                                                    foreach ($q33->result() as $rowssss) {
                                                        $leaderdepartment[] = $rowssss->department_id;
                                                    }
                                                }
                                                $this->db->select('department, department_id')->from('departments')->where('status', 1)->where('department_id!=', 10);
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $departmentid = $_SESSION['logged_in']['department_id'];
                                                    $this->db->where('department_id', $departmentid);
                                                }
                                                if ($_SESSION['logged_in']['adminuser'] == 2 && $leaderdepartment <> 0) {


                                                    $this->db->where_in('department_id', $leaderdepartment, false);
                                                }

                                                $q = $this->db->where('business_loc_id', 2)->order_by('department', 'ASC')->get();
                                                foreach ($q->result() as $ros) { ?>
                                                    <option value="<?php echo $ros->department_id; ?>"><?php echo strtoupper($ros->department); ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        function getuserscompleted() {
                                            var department = $("#task_department_filter_completed").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getusersofdepartment",
                                                data: "department=" + department,
                                                success: function(data) {
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
                                                $user_id = $this->session->userdata['logged_in']['user_id'];
                                                if ($_SESSION['logged_in']['adminuser'] == 3) {
                                                    $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id', $user_id)->get();
                                                    foreach ($q->result() as $rowss);
                                                ?>
                                                    <option value="<?php echo $rowss->user_id; ?>"><?php echo strtoupper($rowss->first_name . " " . $rowss->last_name); ?></option>
                                                <?php } else { ?>
                                                    <option value="ALL">ALL</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="table__header">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        <table style="width:100%;border:none !important;">
                                            <thead>
                                                <tr>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c1">&nbsp;</span>&nbsp;ON TIME</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c2">&nbsp;</span>&nbsp;DELAYED</th>
                                                    <th style="width:33%;border:none"><span
                                                            class="colour-box c3">&nbsp;</span>&nbsp;EARLY</th>
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
                    if ($this->session->userdata['logged_in']['role'] == 12 || $user_id == 139) {
                    ?>
                     <div class="margin_btw"></div>
                    <div>
                        <h3 class="global_heading">
                            Running DF Task Wise Delay Report
                        </h3>
                        <div class=" card_box">

                            <div class="table-responsive">
                                <table id="dfTablealldata" class="table table-striped table-bordered pretty5"
                                    style="width:100%">
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


                        </div>
                    </div>
  <?php
                    }
                    ?>


                    
                      <?php
                    if ($this->session->userdata['logged_in']['role'] == 12) {

                    ?>
                    <div class="margin_btw"></div>
                    <div class="row ">
                        <div class="col-sm-12">
                            <h3 class="global_heading">
                                All Running DFs
                            </h3>
                            <div class="card_box">
                                <div class="table-responsive">
                                    <table id="example5" class="table pretty5 table-striped table-bordered ">
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
                                    $m = 1;
                                    $q = $this->db->select('a.id, a.df_no, a.added_on, a.df_upload, b.title, b.first_name, b.last_name')->from('df_release a')->join('system_users b', 'a.added_by=b.user_id', 'left')->where('a.df_status', 0)->order_by('a.id', 'desc')->get();
                                    if ($q->num_rows() > 0) {
                                        foreach ($q->result() as $rows) {
                                            $dfowner = $rows->title . " " . $rows->first_name . " " . $rows->last_name;
                                            $q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
                                            if ($q1->num_rows() > 0) {
                                                foreach ($q1->result() as $r);
                                                $planneddate = date('d-m-Y', strtotime($r->enddate));
                                            } else {
                                                $planneddate = '';
                                            }

                                            $q5 = $this->db->select('a.company_name, a.podate, b.title, b.first_name, b.last_name, po_attachment')->from('poreceived a')->join('system_users b', 'a.added_by=b.user_id', 'left')->where('a.df_id', $rows->id)->get();

                                            if ($q5->num_rows() > 0) {
                                                foreach ($q5->result() as $row5);
                                                $companyname = $row5->company_name;
                                                $podate = date('d-m-Y', strtotime($row5->podate));
                                                //$dfowner = $row5->title." ".$row5->first_name." ".$row5->last_name;
                                                $po_attachment = '<a href="' . sfdocument . 'Taskdocument/' . $row5->po_attachment . '" download><span class="btn btn-warning btn-xs">Click to download PO</span></a>';
                                            } else {
                                                $companyname = "";
                                                $podate = "";
                                                $dfowner = "";
                                                $po_attachment  = '';
                                            }

                                            $q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
                                            $count = $q1->num_rows();
                                            $delaycount = array();
                                            $delaycount[] = 0;
                                            $totaldayscountarray = array();
                                            $totaldayscountarray[] = 0;
                                            $q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->where('task_status', 1)->get();
                                            $totaldone = $q2->num_rows();
                                            $percetage =  @round($totaldone * 100 / $count);

                                            foreach ($q2->result() as $rowss) {
                                                if (date('Y-m-d', strtotime($rowss->task_completed_on)) > $rowss->end_date) {
                                                    $delaycount[] = 1;

                                                    $daysss = $CIA->Task_model->getDays($rowss->end_date, date('Y-m-d', strtotime($rowss->task_completed_on)), 1);

                                                    $totaldayscountarray[] = $daysss;
                                                }
                                            }
                                            $delayed = array_sum($delaycount);
                                            $totaldaysdelayed = array_sum($totaldayscountarray);

                                            $totaldelayedpercentage = round($delayed * 100 / $totaldone);

                                            $expecteddate =  date('d-m-Y', strtotime($planneddate . ' +' . $totaldaysdelayed . ' Days'));
                                            $skipped_dates = $CIA->Task_model->SKIPsingle_holidays($expecteddate);

                                    ?>
                                            <tr>
                                                <td><?php echo $m; ?></td>
                                                <td><?php echo strtoupper($rows->df_no); ?></td>
                                                <td><a href="<?php echo sfdocument; ?>Taskdocument/dfattachment/<?php echo $rows->df_upload; ?>" download><span class="btn btn-primary btn-xs">DOWNLOAD DF</span></a><br><br><?php echo $po_attachment; ?></td>

                                                <td><?php echo $podate; ?></td>
                                                <td><?php echo strtoupper($companyname); ?> <br><a href="<?php echo page_url; ?>Task/viewdfmeetingmom/<?php echo $rows->id; ?>"><span class="btn btn-success btn-xs">DF MEETING MOM</span></a></td>
                                                <td><?php echo strtoupper($dfowner); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($rows->added_on)); ?></td>
                                                <td><?php echo $planneddate; ?></td>
                                                <?php
                                                $maxdays = $CIA->Task_model->workDelayed($rows->id, 0);
                                                ?>
                                                <td><?php
                                                    $planneddateofcompletion = date('Y-m-d', strtotime($planneddate));
                                                    //echo $planneddateofcompletion; exit;
                                                    if ($maxdays > 0) {

                                                        $date = new DateTime($planneddateofcompletion);
                                                        $date->modify("+$maxdays days");
                                                        $newDate = $date->format('Y-m-d');

                                                        $nextcompletiondate = date('d-m-Y', strtotime($newDate));
                                                    } else {
                                                        $nextcompletiondate = date('d-m-Y', strtotime($planneddate));
                                                    }
                                                    echo $nextcompletiondate; ?></td>
                                                <td><?php echo  $percetage; ?>%</td>
                                                <td>
                                                    <?php if ($maxdays > 0) {
                                                        echo "<strong style='color:red; font-weight:bold;'>" . $maxdays . " DAYS</strong>";
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php



                                                    $q1 = $this->db->select('MIN(start_date) as stdate')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->order_by('id', 'asc')->limit(1)->get();
                                                    if ($q1->num_rows() > 0) {
                                                        foreach ($q1->result() as $row1);
                                                        $startdate = $row1->stdate;
                                                    }

                                                    $q2 = $this->db->select('MAX(end_date) as edndate ')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->order_by('id', 'desc')->limit(1)->get();
                                                    if ($q2->num_rows() > 0) {
                                                        foreach ($q2->result() as $row2);
                                                        $end_date = $row2->edndate;
                                                    }

                                                    $startDate = new DateTime($startdate);
                                                    $endDate = new DateTime($end_date);

                                                    $interval = $startDate->diff($endDate);

                                                    $totaldays =  $interval->days;


                                                    $q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->group_by('po_id')->get();
                                                    foreach ($q->result() as $poinfo);
                                                    $pono = $poinfo->po_id;

                                                    $q6 = $this->db->select('order_value')->from('poreceived')->where('id', $pono)->get();
                                                    if ($q6->num_rows() > 0) {
                                                        foreach ($q6->result() as $calculatevalue);
                                                        $orderamount = $calculatevalue->order_value;
                                                        $onedayloss =  floatval($orderamount / $totaldays);
                                                        $loss = round($onedayloss * $maxdays);
                                                        if ($loss <> '') {
                                                            $lossamount =  $CIA->Task_model->formatIndianCurrency($loss);
                                                            echo '<i class="fa fa-inr"></i> ' . $lossamount;
                                                        }
                                                    }



                                                    ?>


                                                </td>
                                                <td><a href="<?php echo page_url; ?>Task/finalgantchart/<?php echo $rows->id; ?>" target="_blank"><span class="btn btn-warning btn-xs">GANTT CHART</span></a></td>
                                            </tr>

                                    <?php $m++;
                                        }
                                    } ?>


                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div>



                    </div>

  <?php } ?>

 

   <?php
                    if ($user_id == 139) {
                    } else {
                        $Q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                        if ($Q->num_rows() > 0) {
                    ?>
<div class="margin_btw"></div>
 <div class="row ">
                        <div class="col-sm-12">
                            <h3 class="global_heading">
                                DF Wise Task Due of Your Team
                            </h3>
                            <div class="card_box">
                                <div class="table-responsive">
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

                            </div>
                        </div>



                    </div>

      <?php
                        }
                    } ?>



   <?php
                    $Q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                    if ($Q->num_rows() > 0) {

                    ?>
                    <div class="margin_btw"></div>
 <div class="row ">
                        <div class="col-sm-12">
                            <h3 class="global_heading">
                                All Running DFs
                            </h3>
                            <div class="card_box">
                                <div class="table-responsive">
                                    <table id="example5" class="table pretty5 table-striped table-bordered ">
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
                                    $m = 1;
                                    $q = $this->db->select('a.id, a.df_no, a.added_on, a.df_upload, b.title, b.first_name, b.last_name')->from('df_release a')->join('system_users b', 'a.added_by=b.user_id', 'left')->where('a.df_status', 0)->order_by('a.id', 'desc')->get();
                                    if ($q->num_rows() > 0) {
                                        foreach ($q->result() as $rows) {

                                            $dfowner = $rows->title . " " . $rows->first_name . " " . $rows->last_name;

                                            $q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
                                            if ($q1->num_rows() > 0) {
                                                foreach ($q1->result() as $r);
                                                $planneddate = date('d-m-Y', strtotime($r->enddate));
                                            } else {
                                                $planneddate = '';
                                            }

                                            $q5 = $this->db->select('a.company_name, a.podate, b.title, b.first_name, b.last_name')->from('poreceived a')->join('system_users b', 'a.added_by=b.user_id', 'left')->where('id', $rows->id)->get();

                                            if ($q5->num_rows() > 0) {
                                                foreach ($q5->result() as $row5);
                                                $companyname = $row5->company_name;
                                                $podate = date('d-m-Y', strtotime($row5->podate));
                                                //$dfowner = $row5->title." ".$row5->first_name." ".$row5->last_name;
                                            } else {
                                                $companyname = "";
                                                $podate = "";
                                                $dfowner = "";
                                            }

                                            $q1 = $this->db->select('id')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->get();
                                            $count = $q1->num_rows();
                                            $delaycount = array();
                                            $delaycount[] = 0;
                                            $totaldayscountarray = array();
                                            $totaldayscountarray[] = 0;
                                            $q2 = $this->db->select('id, task_completed_on, end_date')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->where('task_status', 1)->get();
                                            $totaldone = $q2->num_rows();
                                            $percetage =  @round($totaldone * 100 / $count);

                                            foreach ($q2->result() as $rowss) {
                                                if (date('Y-m-d', strtotime($rowss->task_completed_on)) > $rowss->end_date) {
                                                    $delaycount[] = 1;

                                                    $daysss = $CIA->Task_model->getDays($rowss->end_date, date('Y-m-d', strtotime($rowss->task_completed_on)), 1);

                                                    $totaldayscountarray[] = $daysss;
                                                }
                                            }
                                            $delayed = array_sum($delaycount);
                                            $totaldaysdelayed = array_sum($totaldayscountarray);

                                            $totaldelayedpercentage = round($delayed * 100 / $totaldone);

                                            $expecteddate =  date('d-m-Y', strtotime($planneddate . ' +' . $totaldaysdelayed . ' Days'));
                                            $skipped_dates = $CIA->Task_model->SKIPsingle_holidays($expecteddate);

                                    ?>
                                            <tr>
                                                <td><?php echo $m; ?></td>
                                                <td><?php echo strtoupper($rows->df_no); ?></td>
                                                <td><a href="<?php echo sfdocument; ?>Taskdocument/dfattachment/<?php echo $rows->df_upload; ?>" download><span class="btn btn-primary btn-xs">DOWNLOAD DF</span></a></td>

                                                <td><?php echo $podate; ?></td>
                                                <td><?php echo strtoupper($companyname); ?> <br><a href="<?php echo page_url; ?>Task/viewdfmeetingmom/<?php echo $rows->id; ?>"><span class="btn btn-success btn-xs">DF MEETING MOM</span></a></td>
                                                <td><?php echo strtoupper($dfowner); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($rows->added_on)); ?></td>
                                                <td><?php echo $planneddate; ?></td>
                                                <?php
                                                $maxdays = $CIA->Task_model->workDelayed($rows->id, 0);
                                                ?>
                                                <td><?php
                                                    $planneddateofcompletion = date('Y-m-d', strtotime($planneddate));
                                                    //echo $planneddateofcompletion; exit;
                                                    if ($maxdays > 0) {

                                                        $date = new DateTime($planneddateofcompletion);
                                                        $date->modify("+$maxdays days");
                                                        $newDate = $date->format('Y-m-d');

                                                        $nextcompletiondate = date('d-m-Y', strtotime($newDate));
                                                    } else {
                                                        $nextcompletiondate = date('d-m-Y', strtotime($planneddate));
                                                    }
                                                    echo $nextcompletiondate; ?></td>
                                                <td><?php echo  $percetage; ?>%</td>
                                                <td>
                                                    <?php if ($maxdays > 0) {
                                                        echo "<strong style='color:red; font-weight:bold;'>" . $maxdays . " DAYS</strong>";
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php



                                                    $q1 = $this->db->select('MIN(start_date) as stdate')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->order_by('id', 'asc')->limit(1)->get();
                                                    if ($q1->num_rows() > 0) {
                                                        foreach ($q1->result() as $row1);
                                                        $startdate = $row1->stdate;
                                                    }

                                                    $q2 = $this->db->select('MAX(end_date) as edndate ')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->order_by('id', 'desc')->limit(1)->get();
                                                    if ($q2->num_rows() > 0) {
                                                        foreach ($q2->result() as $row2);
                                                        $end_date = $row2->edndate;
                                                    }

                                                    $startDate = new DateTime($startdate);
                                                    $endDate = new DateTime($end_date);

                                                    $interval = $startDate->diff($endDate);

                                                    $totaldays =  $interval->days;


                                                    $q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('df_id', $rows->id)->group_by('po_id')->get();
                                                    foreach ($q->result() as $poinfo);
                                                    $pono = $poinfo->po_id;

                                                    $q6 = $this->db->select('order_value')->from('poreceived')->where('id', $pono)->get();
                                                    if ($q6->num_rows() > 0) {
                                                        foreach ($q6->result() as $calculatevalue);
                                                        $orderamount = $calculatevalue->order_value;
                                                        $onedayloss =  floatval($orderamount / $totaldays);
                                                        $loss = round($onedayloss * $maxdays);
                                                        if ($loss <> '') {
                                                            $lossamount =  $CIA->Task_model->formatIndianCurrency($loss);
                                                            echo '<i class="fa fa-inr"></i> ' . $lossamount;
                                                        }
                                                    }



                                                    ?>


                                                </td>

                                                <td><a href="<?php echo page_url; ?>Task/dfgantchartNew/<?php echo $rows->id; ?>" target="_blank"><span class="btn btn-warning btn-xs">GANTT CHART</span></a></td>

                                            </tr>

                                    <?php $m++;
                                        }
                                    } ?>


                                </tbody>
                                    </table>

                                </div>

                            </div>
                        </div>



                    </div>

  <?php } ?>
                </div>

                <div class="col-sm-3" style="display:none">
                    <div class="main__box" style="overflow-y:scroll;height: 400px;background-color: whitesmoke;">
                        <div class="reminder">
                            <h6><i class="fa fa-bell"></i>&nbsp;MISSED </h6>
                        </div>
  <?php
                        //$q = $this->db->select('')->from('task_department_wise_scheduling')->join('')->where('')->get();

                        for ($i = 0; $i < 1; $i++) {
                            $back = "#d0fffe";
                        ?>
                        <div class="read_box">
                            <div class="top_box" style="background-color:#d0fffe">
                                <p><i>UPCOMING DF MEETING FOR Upcoming DF Meeting For DF-1200 On 4Th March 2024 at 14:00
                                        PM</i></p>
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
                <form id="updateprogressform" method="post"
                    action="<?php echo page_url; ?>Task/updatetaskremarks"
                    enctype="multipart/form-data">
                    <div id="pageloader1">
                        <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
                    </div>
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" style="font-weight: bold; text-align:center;">Update Task Status
                                </h4>
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
                                            <select name="taskstatus" id="taskstatus" onchange="checkifpendingdue(); <?php if ($_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) { ?>checkifdoneDate(); <?php } ?>" class="form-control" required>
                                                <option value="1">Done</option>
                                                <option value="0">Pending</option>
                                                <!-- <option value="2">Send to Previous Step</option> -->
                                            </select>
                                        </div>
                                    </div>

                                </div>

                               <?php if ($_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) { ?>
                                    <div class="row" id="doneDiv" style="">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="date" name="doneDate" id="doneDate" class="form-control" max="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row" id="helpticketdiv" style="display:none">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Do you want to Raise help ticket?</label>
                                            <select class="form-control" name="ticketcondition" id="ticketcondition"
                                                onchange="checkifticket();">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function checkifticket() {
                                            var ticketcondition = $("#ticketcondition").val();

                                            if (ticketcondition == 1) {
                                                $("#departmentdiv").show();
                                                $("#userdiv").show();

                                            } else {
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
                                                $q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id', 2)->where('status', 1)->order_by('department', 'asc')->get();
                                                foreach ($q->result() as $dpt) { ?>
                                                    <option value="<?php echo $dpt->department_id; ?>"><?php echo ucfirst(strtolower($dpt->department)); ?></option>

                                                <?php  } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        function getalldepartmentwiseuser() {
                                            var departmentid = $("#selectdepartment").val();
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Task/getdepartmentwiseusers",
                                                data: "departmentid=" + departmentid,
                                                success: function(data) {
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
                                            <select class="form-control task_dfno_filter" name="previousstep"
                                                id="previousstep">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Remarks <span style="color:red"
                                                    id="error_taskremarks"></span></label>
                                            <textarea name="taskremarks" id="taskremarks" class="form-control"
                                                required></textarea>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <input type="submit" style="width: 100%;" name=""
                                            onclick="taskupdationvalidation();" value="Submit" class="btn btn-success">
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
                <form id="updateprogressform" method="post"
                    action="<?php echo page_url; ?>Task/reassignselectedtask"
                    enctype="multipart/form-data">
                    <div id="pageloader1">
                        <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
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
                                        <input type="hidden" id="selectedtasktoreassign" value=""
                                            name="selectedtasktoreassign">
                                        <div class="form-group">
                                            <label>Reassign to <span style="color:red"
                                                    id="error_taskstatus">*</span></label>
                                            <select name="showuserstoreassign" id="showuserstoreassign"
                                                class="form-control" required>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="display:none">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea class="form-control" name="predefinedmessage"
                                                id="predefinedmessage"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Remarks <span style="color:red"
                                                    id="error_taskremarks"></span></label>
                                            <textarea name="reassignremarks" id="reassignremarks"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <input type="submit" style="width: 100%;" name="" value="Submit"
                                            class="btn btn-success">
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
                function checkifpendingdue() {
                    var taskstatus = $("#taskstatus").val();
                    var sortorder = $("#shortorder").val();
                    $("#doneDiv").css('display', 'none');
                    $("#doneDate").attr('required', false);
                    if (taskstatus == 0) {
                        $("#helpticketdiv").show();
                    } else {
                        $("#helpticketdiv").hide();
                        $("#doneDiv").css('display', '');
                        $("#doneDate").attr('required', true);
                    }
                    if (taskstatus == 2) {
                        $("#sendtopreviousstep").show();

                        $.ajax({
                            type: "post",
                            url: "<?php echo page_url; ?>Task/getallprevioussteps",
                            data: "sortorder=" + sortorder,
                            success: function(data) {
                                $("#previousstep").html(data);
                            }
                        });
                        $("#taskremarks").attr('Required', true);
                    } else if (taskstatus == 0) {

                        $("#taskremarks").attr('Required', true);
                    } else {
                        $("#sendtopreviousstep").hide();

                    }
                }


                function checkifdoneDate() {
                    var taskstatus = $("#taskstatus").val();
                    $("#doneDiv").css('display', 'none');
                    $("#doneDate").attr('required', false);
                    if (taskstatus == 0) {
                        // $("#helpticketdiv").show();
                    } else {
                        $("#doneDiv").css('display', '');
                        $("#doneDate").attr('required', true);
                    }

                }
            </script>

            <div id="dfmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" style="display: none;">

                <form id="loginForm" method="post" action="<?php echo page_url; ?>Task/dfrelease"
                    enctype="multipart/form-data">
                    <div id="pageloader">
                        <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
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
                                            <label>DF Description <span id="error_df_description"
                                                    style="color:red;">*</span></label>

                                            <input type="text" class="form-control" id="df_description"
                                                name="df_description" value="" required>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="button" class="btn btn-default waves-effect"
                                    data-dismiss="modal">Close</button>

                                <input type="submit" id="savedata" class="btn btn-info" value="Submit">

                            </div>

                        </div>



                </form>

            </div><!-- /.modal -->



   <?php $this->load->view('common/footer'); ?><!-- End Footer -->

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
        function changeDoneDate(id) {

            // alert('hi'); 
            var ddate = $("#donedate" + id).val();
            alert(ddate);
            if (ddate != '') {
                $.ajax({
                    url: '<?php echo page_url . 'Task/changeTaskCompleteDate/'; ?>' + id + "/" + ddate,
                    method: 'GET',
                    success: function(response) {}

                });
            }


        }



        $(document).ready(function() {
            var a = $("#task_date_filter").val();

            $('#example').dataTable({

                "bProcessing": true,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>Task/ongoingtasklist/",
                stateSave: true,
                "aoColumns": [

                    {
                        mData: 'sr_no'
                    },
                    {
                        mData: 'df_no'
                    },
                    {
                        mData: 'dfupload'
                    },
                    {
                        mData: 'df_release_date'
                    },
                    {
                        mData: 'department'
                    },
                    {
                        mData: 'membername'
                    },
                    {
                        mData: 'task_name'
                    },
                    {
                        mData: 'startdate'
                    },
                    {
                        mData: 'end_date'
                    },
                    {
                        mData: 'pendingdays'
                    },
                    {
                        mData: 'updateprogress'
                    },
                    {
                        mData: 'remarks'
                    },
                ]

            });

            $('#example2').dataTable({

                "bProcessing": true,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>Task/outdatedtask",
                stateSave: true,
                "aoColumns": [

                    {
                        mData: 'sr_no'
                    },
                    {
                        mData: 'df_no'
                    },
                    {
                        mData: 'dfupload'
                    },
                    {
                        mData: 'df_release_date'
                    },
                    {
                        mData: 'department'
                    },
                    {
                        mData: 'membername'
                    },
                    {
                        mData: 'task_name'
                    },
                    {
                        mData: 'end_date'
                    },
                    {
                        mData: 'pendingdays'
                    },
                    {
                        mData: 'updateprogress'
                    },
                    {
                        mData: 'remarks'
                    }


                ]

            });

            $('#example3').dataTable({
                "bProcessing": true,
                "pagination": true,
                scrollX: true,
                stateSave: true,
                "sAjaxSource": "<?php echo page_url; ?>Task/completeddf",
                "aoColumns": [

                    {
                        mData: 'sr_no'
                    },
                    {
                        mData: 'df_no'
                    },
                    {
                        mData: 'dfupload'
                    },
                    {
                        mData: 'df_release_date'
                    },
                    {
                        mData: 'department'
                    },
                    {
                        mData: 'membername'
                    },
                    {
                        mData: 'taskname'
                    },
                    {
                        mData: 'tasktat'
                    },
                    {
                        mData: 'addedon'
                    },
                    {
                        mData: 'taskdelay'
                    },
                    {
                        mData: 'status'
                    },
                    {
                        mData: 'remarks'
                    },
                    {
                        mData: 'addedby'
                    }
                ],
                "initComplete": function(settings, json) {

                    getcolors()
                }

            });


            $('#example8').dataTable({
                "bProcessing": true,
                "pagination": true,
                scrollX: true,
                stateSave: true,
                "sAjaxSource": "<?php echo page_url; ?>Task/completeddfpendingforapproval",
                "aoColumns": [

                    {
                        mData: 'sr_no'
                    },
                    {
                        mData: 'df_no'
                    },
                    {
                        mData: 'dfupload'
                    },
                    {
                        mData: 'df_release_date'
                    },
                    {
                        mData: 'department'
                    },
                    {
                        mData: 'membername'
                    },
                    {
                        mData: 'taskname'
                    },
                    {
                        mData: 'tasktat'
                    },
                    {
                        mData: 'addedon'
                    },
                    {
                        mData: 'approveorreject'
                    },
                    {
                        mData: 'taskdelay'
                    },
                    {
                        mData: 'status'
                    },
                    {
                        mData: 'remarks'
                    },
                    {
                        mData: 'addedby'
                    }
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




            $('#example5').dataTable({
                "bProcessing": true,
                "pagination": true,
                stateSave: true,
                "dom": '<"top"lf>rt<"bottom"ip><"clear">'
            });

            $('#example6').dataTable({
                "bProcessing": true,
                "pagination": true,
                stateSave: true,
                "dom": '<"top"lf>rt<"bottom"ip><"clear">'
            });


            $('#example').on('draw.dt', function() {
                // do action here

                getcolors();
            });

            $('#example').on('search.dt', function() {

                getcolors();
            });

            $('#example4').dataTable({
                "bProcessing": true,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>Task/pendingtoassigndf",
                "aoColumns": [

                    {
                        mData: 'sr_no'
                    },
                    {
                        mData: 'df_no'
                    },
                    {
                        mData: 'df_release_date'
                    },
                    {
                        mData: 'downloaddf'
                    },
                    {
                        mData: 'assigment'
                    }


                ]

            });
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
            diff = now - future;

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



        function show_update_popup() {
            if ($('input[class=update_task]:checked')) {
                $("#myModal").modal('show');
            } else {

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
                if (uploaddf == '') {
                    $("#error_uploaddf").html('Required!');
                    $("#uploaddf").css("border", "1px solid red");
                }

                var dfno = $("#dfno").val();
                if (dfno == '') {
                    $("#error_dfno").html('Required!');
                    $("#dfno").css("border", "1px solid red");
                }

                if (uploaddf == '' || dfno == '') {

                    return false;
                }

            });

        });

        function taskupdationvalidation() {
            var taskstatus = $("#taskstatus").val();
            if (taskstatus == '') {
                $("#error_taskstatus").html('Required!');
                $("#taskstatus").css("border", "1px solid red");
            } else {

                if (taskstatus == 0) {
                    $("#error_taskremarks").html('Required!');
                    $("#taskremarks").css("border", "1px solid red");
                    return false;
                }
            }


            if (taskstatus == '') {

                return false;
            }
        }
    </script>

    <script type="text/javascript">
        function updatedfrelease(id, poid) {

            $("#dfmodal").modal('show');
            $("#dfrecordid").val(id);
            $("#poid").val(poid);
        }
    </script>

    <script type="text/javascript">
        function updateyourprogressremarks(id, sortorder, dfno, taskid, start_date) {

            $("#updateprogress").modal('show');
            $("#taskkiid").val(id);
            $("#mastertaskid").val(taskid);
            $("#shortorder").val(sortorder);
            $("#progressdfno").val(dfno);
            $("#start_date").val(start_date);


        }
    </script>
    <script type="text/javascript">
        function reassigntasktoanotheruser(id, departmentid, assigneduser) {
            $("#updateprogress1").modal('show');
            $("#selectedtasktoreassign").val(id);
            //$("#currenctlyassigned").html(currenctlyassigned);
            var department = departmentid;

            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/fetchreassignuserlist",
                data: "department=" + department + "&assigneduser=" + assigneduser,
                success: function(data) {
                    $("#showuserstoreassign").html(data);
                }
            });

            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/currentlyassignedto",
                data: "id=" + id,
                success: function(taskinfo) {
                    $("#currenctlyassigned").html(taskinfo);
                }
            });


        }
    </script>
    <script type="text/javascript">
        function assigntasktousers(id, departmentid) {
            $("#assigntaskwindow").modal('show');
            var dfid = id;
            var department = departmentid;
            //alert(department);
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/featch_dynamic_tasks",
                data: "dfid=" + dfid + "&department=" + department,
                success: function(data) {
                    $("#showdynamictask").html(data);
                }
            });

        }
    </script>

    <script src="
https://cdn.jsdelivr.net/npm/bootoast@1.1.4/dist/bootoast.min.js
"></script>
    <script type="text/javascript">
        function tiggernotification() {
            var departmentid = '10';
            var userid = '161';
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/checknotification",
                data: "departmentid=" + departmentid + "&userid=" + userid,
                dataType: 'json',
                success: function(data) {
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
        //      var departmentid = '10';
        //     var userid = '161';
        //      $.ajax({
        //         type:"post",
        //         url:"https://shubhampack.in/beta1/index.php/Task/notificationuserwise",
        //         data:"departmentid="+departmentid+"&userid="+userid,
        //         success:function(data){
        //             //alert(data);
        //            $("#showlivenotifications").html(data);

        //         }
        //     });
        // }

        function unassignednotificationcount() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/unassignednotificationcount",
                success: function(data) {

                    $("#unassignednotificationcount").html(data);

                }
            });
        }

        function ongoingtaskcountnotification() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/ongoingtaskcountnotification",
                success: function(data) {

                    $("#ongoingtaskcountnotification").html(data);

                }
            });
        }

        function overduetaskcountnotification() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/overduetaskcountnotification",
                success: function(data) {

                    $("#overduetaskcountnotification").html(data);

                }
            });
        }

        function completeddfnotificationcount() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/completeddfnotificationcount",
                success: function(data) {
                    // alert(data);
                    $("#completeddfnotificationcount").html(data);

                }
            });
        }

        function completeddfnotificationcountforapproval() {
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Task/completeddfnotificationcountpendingforapproval",
                success: function(data) {
                    // alert(data);
                    $("#completeddfnotificationcountforapproval").html(data);

                }
            });
        }

        function filter_ongoing_task() {
            var filter = $("#task_date_filter").val();
            var departmentfilter = $("#task_department_filter").val();
            var usefilter = $("#task_user_filter").val();
            var dfno = $("#task_dfno_filter").val();
            var activeusertype = '1';
            $('#example').DataTable().ajax.url("<?php echo page_url; ?>Task/ongoingtasklist/" + filter + "/" + departmentfilter + "/" + usefilter + "/" + dfno + "/" + activeusertype).load();
            //$('#example').DataTable().ajax.reload();
        }

        function filter_overdue_task() {
            var filter = $("#task_number_filter_overdue").val();
            var fiterindays = $("#noofdaysdue").val();
            var departmentfilter = $("#task_department_filter_overdue").val();
            var usefilter = $("#task_user_filter_overdue").val();
            var dfno = $("#task_dfno_filter_overdue").val();
            $('#example2').DataTable().ajax.url("<?php echo page_url; ?>Task/outdatedtask/" + filter + "/" + departmentfilter + "/" + usefilter + "/" + dfno + "/" + fiterindays).load();
            //$('#example').DataTable().ajax.reload();
        }

        function filter_completed_task() {
            var departmentfilter = $("#task_department_filter_completed").val();
            var usefilter = $("#task_user_filter_completed").val();
            var dfno = $("#task_dfno_filter_completed").val();
            $('#example3').DataTable().ajax.url("<?php echo page_url; ?>Task/completeddf/" + departmentfilter + "/" + usefilter + "/" + dfno).load(getcolors);
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


    <div id="assigntaskwindow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">

        <form id="assigntasktoteammember" method="post"
            action="<?php echo page_url; ?>Task/assigntasktoteammember" enctype="multipart/form-data">
            <div id="pageloader2">
                <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing..." />
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
        $(document).ready(function() {
            $("#updateprogressform").on("submit", function() {
                $("#pageloader1").fadeIn();
            }); //submit
        }); //document ready
    </script>
    <script>
        $(document).ready(function() {
            $("#loginForm").on("submit", function() {
                $("#pageloader").fadeIn();
            }); //submit
        }); //document ready
    </script>
    <script>
        $(document).ready(function() {
            $("#assigntasktoteammember").on("submit", function() {
                $("#pageloader2").fadeIn();
            }); //submit
        }); //document ready

        function applysameUser() {
            if ($('#appall').is(':checked')) {
                var d = $(".firstClass").val();
                if (d != '') {
                    $(".allassignUser").val(d);
                } else {
                    $("#appall").prop('checked', false);
                }

            } else {
                //alert('bye');
            }

        }
    </script>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $('.task_dfno_filter').select2();
        });
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <script>
        $(document).ready(function() {
            function fetchNotifications() {
                $.ajax({
                    url: "<?php echo page_url . 'Task/fetchNotifications'; ?>",
                    method: "GET",
                    success: function(data) {
                        const notifications = JSON.parse(data);

                        // Show Growl Notifications
                        notifications.forEach(notification => {
                            toastr.info(notification.message, "New Notification", {
                                timeOut: 0, // Persistent until clicked
                                closeButton: true,
                                onHidden: function() {
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
                    url: "<?php echo page_url . 'Task/markNotificationAsRead'; ?>",
                    method: "POST",
                    data: {
                        notification_id: notification_id
                    },
                    success: function(response) {
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
                url: "<?php echo page_url . 'Maintenance_support/fetch_notificationsofusers'; ?>",
                method: "GET",
                dataType: "json",
                success: function(data) {
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
                        markdfNotificationsAsRead(`${notification.id}`);
                    }
                }
            });
        }

        function markNotificationRead(notificationId) {
            $.ajax({
                url: '<?php echo page_url . "Maintenance_support/mark_notifications_read"; ?>',
                type: 'POST',
                data: {
                    notification_id: notificationId
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        alert(data.message);
                        // Optionally refresh the notifications list or UI
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
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

    <script>
        $(document).ready(function() {
            // Handle collapse opening and closing
            $('.right_arrow').click(function() {
                var icon = $(this).find('.collapse_arrow');
                var collapseElement = $(this).next('.collapse');

                // Toggle the collapse
                collapseElement.on('show.bs.collapse', function() {
                    icon.css('transform', 'rotate(90deg)'); // Rotate the arrow when opening
                });

                collapseElement.on('hide.bs.collapse', function() {
                    icon.css('transform', 'rotate(0deg)'); // Reset the arrow when closing
                });
            });
        });
    </script>
</body>

</html>
