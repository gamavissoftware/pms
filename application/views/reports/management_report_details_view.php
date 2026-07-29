<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> Details</title>
    
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        /* Ticket Badges */
        .ticket-badge { background-color: #f0ad4e; color: #fff; padding: 3px 8px; border-radius: 10px; font-size: 12px; font-weight: bold; cursor: pointer; }
        .ticket-badge:hover { background-color: #ec971f; }
        
        /* Row Coloring */
        tr.row-green td { background-color: #dff0d8 !important; color: #3c763d; }
        tr.row-red td { background-color: #f2dede !important; color: #a94442; }
        
        /* Delay Text */
        .text-danger-bold { color: #a94442; font-weight: bold; }
        .text-success-bold { color: #3c763d; font-weight: bold; }

        /* Filter Bar */
        .filter-card { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        
        /* Modal Styles */
        .ticket-status-open { font-weight: bold; color: #d9534f; }
        .ticket-status-closed { font-weight: bold; color: #5cb85c; }
        .modal-body .ticket-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .modal-body .ticket-item:last-child { border-bottom: 0; }
        .modal-body .ticket-meta { font-size: 12px; color: #777; }
        .modal-body .ticket-remarks { margin-top: 5px; padding-left: 10px; border-left: 3px solid #f0ad4e; }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu');?>
    </header>

    <div class="wrapper">
        <div class="container-fluid" style="margin-top: 20px;">
            
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title"><?php echo htmlspecialchars($title); ?></h4>
                        <h5 class="text-muted">
                            From <?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?>
                        </h5>
                    </div>
                </div>
            </div>

            <?php
            // Pre-process data for filter
            $unique_users = [];
            $user_col_key = '';
            if (!empty($results)) {
                if (isset($results[0]['assigned_to'])) $user_col_key = 'assigned_to';
                elseif (isset($results[0]['completed_by'])) $user_col_key = 'completed_by';

                if ($user_col_key) {
                    foreach ($results as $row) {
                        $unique_users[$row[$user_col_key]] = $row[$user_col_key];
                    }
                    sort($unique_users);
                }
            }
            ?>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box filter-card">
                        <h4 class="m-t-0 header-title"><b><i class="fa fa-filter"></i> Live Filter</b></h4>
                        <form class="form-inline">
                            <div class="form-group m-r-10">
                                <label for="filter_start_date" class="m-r-5">From:</label>
                                <input type="text" class="form-control datepicker-filter" id="filter_start_date" placeholder="Start Date" style="width: 120px;">
                            </div>
                            <div class="form-group m-r-10">
                                <label for="filter_end_date" class="m-r-5">To:</label>
                                <input type="text" class="form-control datepicker-filter" id="filter_end_date" placeholder="End Date" style="width: 120px;">
                            </div>
                            <div class="form-group m-r-10">
                                <label for="filter_df_no" class="m-r-5">DF No.:</label>
                                <input type="text" class="form-control" id="filter_df_no" placeholder="Type DF No.">
                            </div>
                            <?php if (!empty($unique_users)): ?>
                            <div class="form-group m-r-10">
                                <label for="filter_user" class="m-r-5">User:</label>
                                <select id="filter_user" class="form-control">
                                    <option value="">All Users</option>
                                    <?php foreach ($unique_users as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user); ?>"><?php echo htmlspecialchars($user); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <button type="button" class="btn btn-default waves-effect" id="filter_reset">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <?php
                        $columns = [];
                        if (!empty($results)) {
                            $columns = array_keys($results[0]);
                        }
                        
                        // Dynamic Date Label
                        $date_label = 'Event Date'; 
                        $is_completed_report = false;
                        
                        if (stripos($title, 'Released') !== false) $date_label = 'Release Date';
                        elseif (stripos($title, 'Assigned') !== false) $date_label = 'Assigned Date';
                        elseif (stripos($title, 'Completed') !== false) {
                            $date_label = 'Completion Date';
                            $is_completed_report = true;
                        }
                        elseif (stripos($title, 'Missed') !== false) $date_label = 'Deadline Date';

                        $header_map = [
                            'date' => $date_label,
                            'start_date' => 'Plan Start Date',
                            'end_date' => 'Plan End Date',
                            'df_no' => 'DF No.',
                            'po_date' => 'PO Date', // NEW
                            'marketing_person' => 'Marketing Person', // NEW
                            'df_description' => 'Description',
                            'task_name' => 'Task Name',
                            'assigned_to' => 'Employee Name',
                            'completed_by' => 'Employee Name',
                            'remarks' => 'Last Remarks',
                            'open_tickets' => 'Open Tickets',
                            'completion_datetime' => 'Actual Completion',
                            'delay_days' => 'Delay (Days)' 
                        ];
                        
                        // Cols to hide from view
                        $skip_cols = ['id', 'df_id', 'task_status', 'raw_end_date', 'raw_completed_date'];
                        
                        // Hide delay column if not relevant
                        if (!$is_completed_report) {
                             $skip_cols[] = 'delay_days';
                        }
                        ?>

                        <table id="report-details-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <?php
                                    foreach ($columns as $col_key) {
                                        if (in_array(strtolower($col_key), $skip_cols)) continue;
                                        
                                        $header_name = $header_map[$col_key] ?? ucfirst(str_replace('_', ' ', $col_key));
                                        $data_key = strtolower($col_key);
                                        if ($data_key == 'assigned_to' || $data_key == 'completed_by') $data_key = 'user';
                                        
                                        echo '<th data-key="' . $data_key . '">' . htmlspecialchars($header_name) . '</th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($results)): ?>
                                    <?php foreach ($results as $row): 
                                        
                                        $row_class = '';
                                        $task_status = $row['task_status'] ?? 0;
                                        $plan_end = isset($row['raw_end_date']) ? strtotime($row['raw_end_date']) : 0;
                                        $completed_on = isset($row['raw_completed_date']) && $row['raw_completed_date'] > '0000-00-00' ? strtotime($row['raw_completed_date']) : 0;
                                        $today = time();

                                        if ($task_status == 1) { 
                                            if ($completed_on > 0 && $plan_end > 0) {
                                                if (date('Y-m-d', $completed_on) <= date('Y-m-d', $plan_end)) {
                                                    $row_class = 'row-green';
                                                } else {
                                                    $row_class = 'row-red';
                                                }
                                            }
                                        } else {
                                            if ($plan_end > 0 && $today > $plan_end) {
                                                $row_class = 'row-red';
                                            }
                                        }
                                    ?>
                                        <tr class="<?php echo $row_class; ?>">
                                            <?php
                                            foreach ($columns as $col_key) {
                                                if (in_array(strtolower($col_key), $skip_cols)) continue;
                                                
                                                if ($col_key == 'open_tickets') {
                                                    if ($row[$col_key] > 0) {
                                                        echo '<td><span class="ticket-badge btn-view-tickets" data-task-id="' . $row['id'] . '" data-task-name="' . htmlspecialchars($row['task_name']) . '"><i class="fa fa-ticket"></i> ' . $row[$col_key] . ' Open</span></td>';
                                                    } else {
                                                        echo '<td>--</td>';
                                                    }
                                                } 
                                                elseif ($col_key == 'completion_datetime') {
                                                    echo '<td>' . $row[$col_key] . '</td>';
                                                }
                                                elseif ($col_key == 'delay_days') {
                                                    $delay = intval($row[$col_key]);
                                                    if ($delay > 0) {
                                                        echo '<td class="text-danger-bold">' . $delay . ' Days</td>';
                                                    } else {
                                                        echo '<td class="text-success-bold">On Time</td>';
                                                    }
                                                }
                                                // Handle PO Date and Marketing Person specifically to show '--' if empty
                                                elseif ($col_key == 'po_date' || $col_key == 'marketing_person') {
                                                    echo '<td>' . (!empty($row[$col_key]) ? htmlspecialchars($row[$col_key]) : '--') . '</td>';
                                                }
                                                else {
                                                    echo '<td>' . htmlspecialchars($row[$col_key]) . '</td>';
                                                }
                                            }
                                            ?>
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

    <div class="modal fade" id="ticket-modal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="ticketModalLabel">Tickets for Task: [Task_Name]</h4>
                </div>
                <div class="modal-body">
                    <div id="ticket-modal-loader" style="text-align: center; padding: 30px;">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p>Loading tickets...</p>
                    </div>
                    <div id="ticket-modal-content" style="max-height: 60vh; overflow-y: auto;"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer');?>

    <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.datepicker-filter').datepicker({ autoclose: true, todayHighlight: true, format: 'dd-mm-yyyy', clearBtn: true });

            var colIdx = { date: -1, start_date: -1, end_date: -1, df_no: -1, user: -1, po_date: -1 };
            $('#report-details-table thead th').each(function(index) {
                var key = $(this).data('key');
                if (key) {
                    if(colIdx[key] === -1) colIdx[key] = index;
                    if(key === 'date' && colIdx.date === -1) colIdx.date = index;
                }
            });

            function parseDate(dateStr) {
                if (!dateStr) return null;
                return moment(dateStr, 'DD-MM-YYYY');
            }

            $.fn.dataTable.ext.search.push(function( settings, data, dataIndex ) {
                var filterStart = parseDate($('#filter_start_date').val());
                var filterEnd = parseDate($('#filter_end_date').val());
                var filterDf = $('#filter_df_no').val().toLowerCase();
                var filterUser = $('#filter_user').val();

                var rowDate = parseDate(data[colIdx.date] || data[colIdx.start_date]);
                var rowDf = (colIdx.df_no > -1) ? data[colIdx.df_no].toLowerCase() : '';
                var rowUser = (colIdx.user > -1) ? data[colIdx.user] : '';

                if (filterStart && rowDate && rowDate.isBefore(filterStart)) return false;
                if (filterEnd && rowDate && rowDate.isAfter(filterEnd)) return false;
                if (filterDf && rowDf.indexOf(filterDf) === -1) return false;
                if (filterUser && rowUser !== filterUser) return false;
                return true;
            });

            var table = $('#report-details-table').DataTable({
                dom: 'Bfrtip',
                buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                // Updated sorting: Sort by PO Date column ASC
                "order": [[ colIdx.po_date, "asc" ]], 
                "pageLength": 25,
                "paging": false
            });

            $('#filter_start_date, #filter_end_date, #filter_user').on('change', function() { table.draw(); });
            $('#filter_df_no').on('keyup', function() { table.draw(); });
            $('#filter_reset').on('click', function() {
                $('.filter-card form')[0].reset();
                $('#filter_start_date, #filter_end_date').datepicker('update', '');
                table.draw();
            });

            $('#report-details-table').on('click', '.btn-view-tickets', function() {
                var $modal = $('#ticket-modal');
                var $loader = $('#ticket-modal-loader');
                var $content = $('#ticket-modal-content');
                var taskId = $(this).data('task-id');
                var taskName = $(this).data('task-name');
                
                $modal.find('#ticketModalLabel').text('Tickets for Task: ' + taskName);
                $content.empty(); $loader.show(); $modal.modal('show');

                $.ajax({
                    url: '<?php echo page_url; ?>Dashboard/ajax_get_task_tickets/' + taskId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $loader.hide();
                        if (response.success && response.tickets.length > 0) {
                            var html = '';
                            $.each(response.tickets, function(index, ticket) {
                                var status_text = (ticket.ticket_status == 0) ? '<span class="ticket-status-open"> (Open)</span>' : '<span class="ticket-status-closed"> (Closed)</span>';
                                html += '<div class="ticket-item">';
                                html += '  <div class="ticket-meta"><strong>Ticket #:</strong> ' + ticket.help_ticket_no + status_text + '</div>';
                                html += '  <div class="ticket-meta"><strong>By:</strong> ' + ticket.added_by_name + ' | <strong>On:</strong> ' + ticket.added_date + '</div>';
                                html += '  <div class="ticket-remarks">' + ticket.remarks + '</div>';
                                html += '</div>';
                            });
                            $content.html(html);
                        } else {
                            $content.html('<div class="alert alert-info">No tickets found for this task.</div>');
                        }
                    },
                    error: function(xhr) {
                        $loader.hide();
                        $content.html('<div class="alert alert-danger">Error loading tickets.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>