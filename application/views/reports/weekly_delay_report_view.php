<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Task Delay Report</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
        .table > tbody > tr > td { vertical-align: middle; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; }
        .label-danger { background-color: #dc3545; font-size: 1.1em; padding: .3em .6em .3em; }
        .filter-card { background-color: #f8f9fa; border: 1px solid #dee2e6; }
        .select2-container .select2-selection--single { height: 34px; border: 1px solid #ccc; border-radius: 4px; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 32px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px; }
        .select2-container { min-width: 200px; }
        .form-inline .form-group { margin-bottom: 10px; vertical-align: middle; }
        .label-warning { background-color: #ffc107; color: #212529; font-size: 1.1em; padding: .3em .6em .3em; }
        .ticket-info-row > td { background-color: #fff9e6; border-top: 2px solid #ffe8a1 !important; padding: 10px 15px !important; box-shadow: inset 0 2px 4px rgba(0,0,0,0.04); }
        .ticket-details strong { color: #8a6d3b; }
        .ticket-details .remarks { margin: 5px 0 8px 10px; padding-left: 10px; border-left: 3px solid #ffe8a1; font-style: italic; color: #555; }
        .ticket-details small { color: #777; }
        .text-loss { color: #a94442; font-weight: bold; }
        
        /* --- NEW STYLES FOR ACCORDION --- */
        .collapsible-row {
            display: none; /* Hide rows by default */
        }
        .show-more-row td {
            background-color: #f9f9f9;
            text-align: center;
            cursor: pointer;
            padding: 8px !important;
        }
        .show-more-row:hover td {
            background-color: #f1f1f1;
        }
        .show-more-btn {
            font-weight: bold;
            color: #007bff;
            font-size: 0.9em;
            text-decoration: none !important;
        }
        .show-more-btn .fa {
            margin-right: 5px;
            transition: transform 0.2s ease-in-out;
        }
        .show-more-btn.expanded .fa {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-12"><div class="page-title-box"><h2 class="text-center">Task Delay Report</h2><hr></div></div></div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box filter-card">
                        <h4 class="m-t-0 header-title"><b><i class="fa fa-filter"></i> Filter Report</b></h4>
                        <p class="text-muted m-b-20">Select a Financial Year or a manual date range.</p>
                        
                        <form method="post" action="<?php echo page_url; ?>Df_reports/weekly_delay_report" class="form-inline">
                            
                            <div class="form-group m-r-10">
                                <label for="financial_year" class="m-r-10">Financial Year:</label>
                                <select name="financial_year" id="financial_year" class="form-control" style="min-width: 150px;">
                                    <option value="">-- Select FY --</option>
                                    <?php 
                                    $current_year = date('Y');
                                    for ($i = $current_year + 1; $i >= 2023; $i--) {
                                        $fy_text = ($i-1) . '-' . $i;
                                        $selected = (isset($selected_fy) && $fy_text == $selected_fy) ? 'selected' : '';
                                        echo "<option value='{$fy_text}' {$selected}>{$fy_text}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group m-r-10">
                                <label for="start_date" class="m-r-10">From:</label>
                                <input type="text" class="form-control datepicker" name="start_date" id="start_date" value="<?php echo htmlspecialchars($selected_start_date ?? ''); ?>" placeholder="Start Date">
                            </div>
                            <div class="form-group m-r-10">
                                <label for="end_date" class="m-r-10">To:</label>
                                <input type="text" class="form-control datepicker" name="end_date" id="end_date" value="<?php echo htmlspecialchars($selected_end_date ?? ''); ?>" placeholder="End Date">
                            </div>
                            <div class="form-group m-r-10">
                                <label for="department_id" class="m-r-10">Department:</label>
                                <select name="department_id" id="department_id" class="form-control">
                                    <option value="">All Departments</option>
                                    <?php foreach($departments as $dept): ?>
                                        <option value="<?php echo $dept['department_id']; ?>" <?php echo ($dept['department_id'] == $selected_department_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['department']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group m-r-10">
                                <label for="df_id" class="m-r-10">DF No:</label>
                                <select name="df_id" id="df_id" class="form-control select2">
                                    <option value="">All DFs</option>
                                    <?php foreach($all_dfs as $df): ?>
                                        <option value="<?php echo $df['df_id']; ?>" <?php echo ($df['df_id'] == $selected_df_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($df['df_no']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-search"></i> View Report</button>
                            <a href="<?php echo page_url; ?>Df_reports/weekly_delay_report" class="btn btn-default waves-effect waves-light m-l-5"><i class="fa fa-refresh"></i> Reset</a>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (!empty($graph_data['labels'])): ?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Total Delayed Tasks by Department</b></h4>
                        <p class="text-muted m-b-20">Which departments have the highest volume of delays.</p>
                        <canvas id="totalDelaysChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Average Delay Days by Department</b></h4>
                        <p class="text-muted m-b-20">Which departments have the most severe delays.</p>
                        <canvas id="avgDelayChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($graph_data['total_loss_data'])): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-sm-7">
                                <h4 class="m-t-0 header-title"><b>Total Estimated Loss by Department</b></h4>
                            </div>
                            <div class="col-sm-5 text-right">
                                <h4 class="m-t-0">
                                    Consolidated Loss: <span class="label" style="background-color: #a94442;"><?php echo number_format($grand_total_loss, 0); ?></span>
                                </h4>
                            </div>
                        </div>
                        <p class="text-muted m-b-20">Financial impact of delays based on an 8% opportunity cost.</p>
                        <canvas id="totalLossChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-12">
                     <h4 class="text-center"><strong><?php echo $page_title; ?></strong></h4><hr>
                      <div class="text-center" style="margin-top: -10px; margin-bottom: 10px;">
                        <a href="<?php echo page_url; ?>Df_reports/report_logic_summary" target="_blank" class="btn btn-link btn-sm">
                            <i class="fa fa-question-circle"></i> How are these numbers calculated?
                        </a>
                    </div>
                    <?php if (empty($grouped_report_data)): ?>
                        <div class="card-box"><div class="text-center"><i class="fa fa-info-circle fa-3x text-muted"></i><h4 class="m-t-20">No Delayed Tasks Found</h4><p class="text-muted">No delayed tasks were found for the selected criteria.</p></div></div>
                    <?php else: 
                        foreach ($grouped_report_data as $dept_id => $group):
                            $stats = $department_stats[$dept_id] ?? null;
                            $total_tasks = count($group['tasks']);
                    ?>
                    <div class="card-box">
                        <div class="row">
                           <div class="col-md-8">
                                <h4 class="m-t-0 header-title"><b>Department: <?php echo htmlspecialchars($group['department_name']); ?></b></h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if ($stats && $stats['task_count'] > 0): ?>
                                <h5 class="m-t-0" style="margin-bottom: 5px;">
                                    Est. Loss: <span class="label" style="background-color: #a94442;"><?php echo number_format($stats['total_loss'], 0); ?></span>
                                </h5>
                                <h5 style="margin-bottom: 5px;">
                                    Avg. Delay: <span class="label label-warning"><?php echo $stats['average_delay']; ?> Days</span>
                                </h5>
                                <p class="text-muted m-b-10" style="font-size: 0.9em;">
                                    (From <?php echo $stats['task_count']; ?> delayed task(s) in this report)
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr style="margin-top: 0; margin-bottom: 15px;">
                        
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead>
                                    <tr>
                                        <th>DF No.</th>
                                        <th>DF Description</th>
                                        <th>Assigned To</th>
                                        <th>Planned End Date</th>
                                        <th>Status</th>
                                        <th>Completed On</th>
                                        <th class="text-center">Delay (Days)</th>
                                        <th class="text-center">Est. Loss</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($group['tasks'] as $task_index => $row): 
                                    // Add class and data-attribute to rows after the 10th
                                    $row_class = $task_index >= 10 ? 'collapsible-row' : '';
                                    $row_attr = 'data-dept-group="' . $dept_id . '"';
                                    $row_style = $task_index >= 10 ? 'style="display:none;"' : '';
                                ?>
                                    <tr class="<?php echo $row_class; ?>" <?php echo $row_attr; ?> <?php echo $row_style; ?>>
                                        <td><b><?php echo htmlspecialchars($row['df_no']); ?></b></td>
                                        <td><?php echo htmlspecialchars($row['df_description']); ?></td>
                                        <td><?php echo htmlspecialchars($row['assigned_user_name'] ? $row['assigned_user_name'] : 'N/A'); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($row['end_date'])); ?></td>
                                        <td>
                                            <?php if ($row['task_status'] == 0): ?><span class="badge badge-danger">Pending Delay</span>
                                            <?php elseif ($row['task_status'] == 2): ?><span class="badge badge-info">Pending Approval</span>
                                            <?php else: ?><span class="badge badge-warning">Delay Closed</span><?php endif; ?>
                                        </td>
                                        <td><?php echo ($row['task_status'] == 1 && $row['task_completed_on'] != null && $row['task_completed_on'] != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['task_completed_on'])) : 'N/A'; ?></td>
                                        <td class="text-center"><span class="label label-danger"><?php echo $row['delay_days']; ?></span></td>
                                        <td class="text-center text-loss"><?php echo (isset($row['estimated_loss'])) ? number_format($row['estimated_loss'], 0) : '0'; ?></td>
                                    </tr>
                                    
                                    <?php if (isset($tickets[$row['id']])): $ticket = $tickets[$row['id']]; ?>
                                    <tr class="ticket-info-row <?php echo $row_class; ?>" <?php echo $row_attr; ?> <?php echo $row_style; ?>>
                                        <td colspan="8">
                                            <div class="ticket-details">
                                                <strong><i class="fa fa-ticket"></i> Ticket #<?php echo htmlspecialchars($ticket['help_ticket_no']); ?>:</strong>
                                                <p class="remarks"><?php echo nl2br(htmlspecialchars($ticket['remarks'])); ?></p>
                                                <small><strong>Created by:</strong> <?php echo htmlspecialchars($ticket['created_by_name']); ?> | <strong>Pending with:</strong> <?php echo htmlspecialchars($ticket['pending_with_name']); ?></small>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </tbody>
                                
                                <?php if ($total_tasks > 10): ?>
                                <tfoot>
                                    <tr class="show-more-row" data-target-group="<?php echo $dept_id; ?>">
                                        <td colspan="8">
                                            <a href="#" class="show-more-btn" 
                                               data-text-show="Show <?php echo ($total_tasks - 10); ?> More Tasks" 
                                               data-text-hide="Show Top 10 Only">
                                                <i class="fa fa-chevron-down"></i> Show <?php echo ($total_tasks - 10); ?> More Tasks
                                            </a>
                                        </td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    <?php
                        endforeach;
                    endif; 
                    ?>
                    </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="userReportModal" tabindex="-1" role="dialog" aria-labelledby="userReportModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="userReportModalTitle">User Delay Report</h4>
                </div>
                <div class="modal-body" id="userReportModalBody" style="min-height: 200px;">
                    <div id="userSummarySection">
                        <div class="table-responsive" id="userSummaryTable"></div>
                    </div>
                    <div id="userDetailSection" style="display:none;">
                        <div class="row">
                            <div class="col-sm-7">
                                <h5 style="margin-top:0;"><strong>Task Details for: <span id="detailUserName"></span></strong></h5>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group row" style="margin-bottom: 10px;">
                                    <label for="modalDfFilter" class="col-sm-4 col-form-label text-right" style="padding-top: 5px;">Filter by DF:</label>
                                    <div class="col-sm-8">
                                        <select id="modalDfFilter" class="form-control input-sm" style="height: 30px;"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" id="detailTaskTableContainer"></div>
                        <button type="button" class="btn btn-default btn-sm m-t-10" id="backToSummaryBtn"><i class="fa fa-arrow-left"></i> Back to Summary</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
   <script>
        $(document).ready(function() {
            // Initialize Datepickers
            $('.datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            // Initialize Select2
            $('#df_id').select2({
                placeholder: "Select a DF",
                allowClear: true
            });
            
            // --- FY Filter Logic ---
            var $startDate = $('#start_date');
            var $endDate = $('#end_date');
            var $fySelect = $('#financial_year');

            function toggleDatePickers(disabled) {
                $startDate.prop('disabled', disabled);
                $endDate.prop('disabled', disabled);
            }

            // Check on page load
            if ($fySelect.val() != '') {
                toggleDatePickers(true);
            }

            $fySelect.on('change', function() {
                if ($(this).val() != '') {
                    toggleDatePickers(true);
                    $startDate.val('');
                    $endDate.val('');
                } else {
                    toggleDatePickers(false);
                }
            });

            $startDate.on('changeDate', function() {
                if ($(this).val() != '') {
                    $fySelect.val('');
                }
            });
            $endDate.on('changeDate', function() {
                if ($(this).val() != '') {
                    $fySelect.val('');
                }
            });
            // --- END: FY Filter Logic ---
            
            
            // --- Global Data Stores ---
            var allReportData = <?php echo json_encode($report_data); ?>;
            var allTickets = <?php echo json_encode($tickets); ?>;
            var currentUserTasks = []; 
            var currentDeptDfs = []; 

            // --- Chart.js Initialization ---
            <?php if (!empty($graph_data['labels'])): ?>
            try {
                var graphData = <?php echo json_encode($graph_data); ?>;
                var graphDeptIDs = graphData.dept_ids; 

                // --- Function 1: Chart Click Handler ---
                function showUserReportModal(event, elements) {
                    if (elements.length === 0) return; 
                    const index = elements[0].index;
                    const clickedDeptID = graphDeptIDs[index];
                    const clickedDeptName = graphData.labels[index];
                    var userStats = {};
                    var uniqueDFs = {}; 
                    allReportData.forEach(function(row) {
                        if (row.department_id == clickedDeptID) {
                            var delay = parseInt(row.delay_days);
                            if (delay > 0) {
                                uniqueDFs[row.df_id] = row.df_no; 
                                var userID = row.assigned_user ? row.assigned_user : '0'; 
                                var userName = row.assigned_user_name ? row.assigned_user_name : 'Unassigned';
                                var loss = parseFloat(row.estimated_loss) || 0;
                                if (!userStats[userID]) {
                                    userStats[userID] = {
                                        user_name: userName, task_count: 0, max_delay: 0,
                                        total_delay_sum: 0, user_id: userID,
                                        total_loss: 0 
                                    };
                                }
                                userStats[userID].task_count++;
                                userStats[userID].total_delay_sum += delay; 
                                userStats[userID].total_loss += loss; 
                                if (delay > userStats[userID].max_delay) {
                                    userStats[userID].max_delay = delay;
                                }
                            }
                        }
                    });
                    currentDeptDfs = Object.entries(uniqueDFs).sort((a, b) => a[1].localeCompare(b[1]));
                    var usersArray = Object.values(userStats);
                    usersArray.sort((a, b) => b.total_loss - a.total_loss); 
                    var html = '<table class="table table-hover table-striped">';
                    html += '<thead><tr><th>User Name</th><th class="text-center">Delayed Tasks</th><th class="text-center">Longest Delay</th><th class="text-center">Average Delay</th><th class="text-center">Est. Total Loss</th></tr></thead><tbody>';
                    if (usersArray.length === 0) {
                        html += '<tr><td colspan="5" class="text-center text-muted">No user-specific delay data found.</td></tr>'; 
                    } else {
                        usersArray.forEach(function(user) {
                            var avgDelay = (user.task_count > 0) ? (user.total_delay_sum / user.task_count).toFixed(1) : 0;
                            html += `<tr>
                                <td><strong>${user.user_name}</strong></td>
                                <td class="text-center">
                                    <a href="#" class="view-user-tasks" style="font-size: 1.1em; font-weight: bold;" data-dept-id="${clickedDeptID}" data-user-id="${user.user_id}" data-user-name="${user.user_name}">
                                        ${user.task_count}
                                    </a>
                                </td>
                                <td class="text-center"><span class="label label-danger">${user.max_delay}</span></td>
                                <td class="text-center"><span class="label label-warning">${avgDelay}</span></td>
                                <td class="text-center text-loss">${user.total_loss.toFixed(0)}</td>
                            </tr>`;
                        });
                    }
                    html += '</tbody></table>';
                    $('#userReportModalTitle').text('User Delay Report for: ' + clickedDeptName);
                    $('#userSummaryTable').html(html);
                    $('#userSummarySection').show();
                    $('#userDetailSection').hide();
                    $('#userReportModal').modal('show');
                }

                // Chart 1: Total Delayed Tasks
                var ctxTotal = document.getElementById('totalDelaysChart').getContext('2d');
                if (ctxTotal) { new Chart(ctxTotal, { type: 'bar', data: { labels: graphData.labels, datasets: [{ label: 'Total Delayed Tasks', data: graphData.total_delay_data, backgroundColor: 'rgba(220, 53, 69, 0.6)', borderColor: 'rgba(220, 53, 69, 1)', borderWidth: 1 }] }, options: { responsive: true, onClick: showUserReportModal, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(context) { return ' ' + context.parsed.y + ' Tasks'; } } } } } }); }

                // Chart 2: Average Delay Days
                var ctxAvg = document.getElementById('avgDelayChart').getContext('2d');
                if (ctxAvg) { new Chart(ctxAvg, { type: 'bar', data: { labels: graphData.labels, datasets: [{ label: 'Average Delay (Days)', data: graphData.avg_delay_data, backgroundColor: 'rgba(255, 193, 7, 0.6)', borderColor: 'rgba(255, 193, 7, 1)', borderWidth: 1 }] }, options: { responsive: true, onClick: showUserReportModal, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(context) { return ' ' + context.parsed.y + ' Days Avg'; } } } } } }); }

                // --- Chart 3: Total Estimated Loss ---
                var ctxLoss = document.getElementById('totalLossChart').getContext('2d');
                if (ctxLoss) {
                    new Chart(ctxLoss, {
                        type: 'bar',
                        data: {
                            labels: graphData.labels,
                            datasets: [{
                                label: 'Total Estimated Loss',
                                data: graphData.total_loss_data,
                                backgroundColor: 'rgba(169, 68, 66, 0.6)', 
                                borderColor: 'rgba(169, 68, 66, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true, 
                            onClick: showUserReportModal,
                            scales: { y: { beginAtZero: true } },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return ' Loss: ' + parseInt(context.parsed.y).toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.error("Error initializing charts:", e); }
            <?php endif; ?>
            
            
            // --- Function 2: Helper to build the detail table ---
            function buildTaskDetailTable(tasks) {
                var html = '<table class="table table-condensed" style="margin-bottom:0;">';
                html += '<thead><tr><th>DF No.</th><th>Task Name</th><th>Planned End</th><th class="text-center">Status</th><th class="text-center">Delay</th><th class="text-center">Est. Loss</th></tr></thead><tbody>';
                if (tasks.length === 0) {
                     html += '<tr><td colspan="6" class="text-center text-muted">No tasks found for this DF.</td></tr>'; 
                }
                tasks.forEach(function(task) {
                    var taskStatusHtml = '';
                    if (task.task_status == '1' && task.task_completed_on != null && task.task_completed_on != '0000-00-00 00:00:00') {
                        var completedDate = new Date(task.task_completed_on).toLocaleDateString('en-GB');
                        taskStatusHtml = `<span class="badge badge-warning" style="font-size: 0.9em;">Closed: ${completedDate}</span>`;
                    } else if (task.task_status == '2') {
                        taskStatusHtml = '<span class="badge badge-info" style="font-size: 0.9em;">Pending Approval</span>';
                    } else { 
                        taskStatusHtml = '<span class="badge badge-danger" style="font-size: 0.9em;">Still Pending</span>';
                    }
                    html += `<tr class="info">
                        <td><strong>${task.df_no}</strong></td>
                        <td>${task.task_name}</td> 
                        <td>${new Date(task.end_date).toLocaleDateString('en-GB')}</td>
                        <td class="text-center">${taskStatusHtml}</td>
                        <td class="text-center"><span class="label label-danger">${task.delay_days}</span></td>
                        <td class="text-center text-loss">${(parseFloat(task.estimated_loss) || 0).toFixed(0)}</td>
                    </tr>`;
                    if (allTickets[task.id]) {
                        var ticket = allTickets[task.id];
                        html += `<tr class="ticket-info-row" style="background-color: #fff9e6;">
                            <td colspan="6" style="padding: 10px 15px;">
                                <div class="ticket-details">
                                    <strong><i class="fa fa-ticket"></i> Ticket #${ticket.help_ticket_no}:</strong>
                                    <p class="remarks" style="margin: 5px 0 8px 10px; padding-left: 10px; border-left: 3px solid #ffe8a1; font-style: italic;">
                                        ${ticket.remarks.replace(/\n/g, '<br>')}
                                    </p>
                                    <small><strong>Pending with:</strong> ${ticket.pending_with_name}</small>
                                </div>
                            </td>
                        </tr>`;
                    }
                });
                html += '</tbody></table>';
                return html;
            }

            // --- Function 3: Task Detail Click Handler ---
            $(document).on('click', '.view-user-tasks', function(e) {
                e.preventDefault();
                var deptID = $(this).data('dept-id');
                var userID = $(this).data('user-id').toString();
                var userName = $(this).data('user-name');
                currentUserTasks = allReportData.filter(function(row) {
                    var rowUserID = row.assigned_user ? row.assigned_user.toString() : '0';
                    var delay = parseInt(row.delay_days);
                    return row.department_id == deptID && rowUserID == userID && delay > 0;
                });
                currentUserTasks.sort((a, b) => b.delay_days - a.delay_days);
                var dfFilter = $('#modalDfFilter');
                dfFilter.empty(); 
                dfFilter.append('<option value="">All DFs</option>');
                currentDeptDfs.forEach(function(df) {
                    dfFilter.append(`<option value="${df[0]}">${df[1]}</option>`);
                });
                var tableHtml = buildTaskDetailTable(currentUserTasks);
                $('#detailUserName').text(userName);
                $('#detailTaskTableContainer').html(tableHtml);
                $('#userSummarySection').hide();
                $('#userDetailSection').show();
            });

            // --- Function 4: DF Filter Change Handler ---
            $('#modalDfFilter').on('change', function() {
                var selectedDfId = $(this).val();
                var tasksToDisplay = [];
                if (selectedDfId == "") {
                    tasksToDisplay = currentUserTasks; 
                } else {
                    tasksToDisplay = currentUserTasks.filter(function(task) {
                        return task.df_id == selectedDfId;
                    });
                }
                var tableHtml = buildTaskDetailTable(tasksToDisplay);
                $('#detailTaskTableContainer').html(tableHtml);
            });
            
            // --- Function 5: Back Button Handler ---
            $('#backToSummaryBtn').on('click', function() {
                $('#userDetailSection').hide();
                $('#userSummarySection').show();
            });

            // --- NEW: Accordion for large tables ---
            $('.show-more-btn').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var targetGroupId = $this.closest('.show-more-row').data('target-group');
                var $rowsToToggle = $('.collapsible-row[data-dept-group="' + targetGroupId + '"]');
                
                if ($rowsToToggle.is(':visible')) {
                    // Hide them
                    $rowsToToggle.fadeOut(200);
                    $this.html('<i class="fa fa-chevron-down"></i> ' + $this.data('text-show'));
                    $this.removeClass('expanded');
                } else {
                    // Show them
                    $rowsToToggle.fadeIn(200);
                    $this.html('<i class="fa fa-chevron-up"></i> ' + $this.data('text-hide'));
                    $this.addClass('expanded');
                }
            });

        });
    </script>
</body>
</html>