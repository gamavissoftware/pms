<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Task Detail</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        body { background-color: #f4f7f6; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
        .table thead th { background-color: #f8f9fa; }
        .filter-card { background-color: #f8f9fa; border: 1px solid #dee2e6; }
        
        /* Stat Box Styles */
        .stat-box {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e9ecef;
            margin-bottom: 15px;
        }
        .stat-box h5 {
            font-size: 0.9em;
            color: #777;
            text-transform: uppercase;
            margin: 0 0 5px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stat-box .stat-value {
            font-size: 1.8em;
            font-weight: 700;
            color: #dc3545;
        }
        .stat-box .stat-df {
            font-size: 1.1em;
            font-weight: 600;
            color: #333;
        }

        /* Ticket Info Styles */
        .ticket-details {
            margin-top: 10px;
            text-align: left;
            padding-top: 10px;
            border-top: 2px dashed #ffe8a1;
            background: #fff9e6;
            padding: 10px;
            border-radius: 4px;
        }
        .ticket-details strong { color: #8a6d3b; }
        .ticket-details .remarks { 
            margin: 5px 0 8px 10px; 
            padding-left: 10px; 
            border-left: 3px solid #ffe8a1; 
            font-style: italic; 
            color: #555; 
        }
        .ticket-details small { color: #777; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title"><?php echo htmlspecialchars($page_title); ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box filter-card">
                        <h4 class="m-t-0 header-title"><b><i class="fa fa-filter"></i> Filter Report</b></h4>
                        
                        <form method="get" action="<?php echo page_url; ?>Dashboard/team_task_detail/<?php echo $user_id; ?>/<?php echo $report_type; ?>" class="form-inline">
                            <div class="form-group m-r-10">
                                <label for="start_date" class="m-r-10">From:</label>
                                <input type="text" class="form-control datepicker" name="start_date" id="start_date" value="<?php echo htmlspecialchars($selected_start_date ?? ''); ?>" placeholder="Start Date">
                            </div>
                            <div class="form-group m-r-10">
                                <label for="end_date" class="m-r-10">To:</label>
                                <input type="text" class="form-control datepicker" name="end_date" id="end_date" value="<?php echo htmlspecialchars($selected_end_date ?? ''); ?>" placeholder="End Date">
                            </div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-search"></i> Filter</button>
                            <a href="<?php echo page_url; ?>Dashboard/team_task_detail/<?php echo $user_id; ?>/<?php echo $report_type; ?>" class="btn btn-default waves-effect waves-light m-l-5"><i class="fa fa-refresh"></i> Reset</a>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (!empty($top_5_delayed_tasks)): ?>
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="m-t-0 header-title"><b>Top 5 Completed Delayed Tasks for <?php echo htmlspecialchars($user_name); ?></b></h4>
                </div>
                <?php foreach ($top_5_delayed_tasks as $task): ?>
                <div class="col-md-4 col-lg-2">
                    <div class="stat-box">
                        <h5><?php echo htmlspecialchars($task['task_name']); ?></h5>
                        <div class="stat-value"><?php echo $task['delay_days']; ?> <span style="font-size: 0.5em;">Days</span></div>
                        <div class="stat-df"><?php echo htmlspecialchars($task['df_no']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <hr>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="table-responsive">
                            <table id="task-detail-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>DF No.</th>
                                        <th>Task Name</th>
                                        <th>Start Date</th>
                                        <th>Planned End</th>
                                        <th>Completed On</th>
                                        <th>Status</th>
                                        <th>Delay (Days)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tasks)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No tasks found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($tasks as $task): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($task['df_no']); ?></strong></td>
                                                <td>
                                                    <?php echo htmlspecialchars($task['task_name']); ?>
                                                    
                                                    <?php if (isset($tickets[$task['id']])): $ticket = $tickets[$task['id']]; ?>
                                                        <div class="ticket-details">
                                                            <strong><i class="fa fa-ticket"></i> Ticket #<?php echo htmlspecialchars($ticket['help_ticket_no']); ?>:</strong>
                                                            <p class="remarks"><?php echo nl2br(htmlspecialchars($ticket['remarks'])); ?></p>
                                                            <small><strong>Created by:</strong> <?php echo htmlspecialchars($ticket['created_by_name']); ?> | <strong>Pending with:</strong> <?php echo htmlspecialchars($ticket['pending_with_name']); ?></small>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d-m-Y', strtotime($task['start_date'])); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($task['end_date'])); ?></td>
                                                <td>
                                                    <?php echo ($task['task_completed_on'] != null && $task['task_completed_on'] != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($task['task_completed_on'])) : 'N/A'; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        if ($task['task_status'] == 1) {
                                                            echo '<span class="badge badge-success">Done</span>';
                                                        } elseif ($task['task_status'] == 2) {
                                                            echo '<span class="badge badge-info">Pending Approval</span>';
                                                        } else {
                                                            echo '<span class="badge badge-warning">Ongoing</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($task['delay_days'] > 0): ?>
                                                        <span class="label label-danger"><?php echo $task['delay_days']; ?></span>
                                                    <?php else: ?>
                                                        <span class="label label-success">0</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    
    <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
    
   <script>
        $(document).ready(function() {
            // Initialize Datepickers
            $('.datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            // Initialize Datatable
            $('#task-detail-table').DataTable({
                dom: 'lBfrtip',
                buttons: ['excelHtml5', 'pdfHtml5'],
                "order": [[ 6, "desc" ]] // Default sort by Delay column
            });
        });
    </script>
</body>
</html>
