<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Trend Details</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        body { background-color: #f4f7f6; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
        .table thead th { background-color: #f8f9fa; }
        .text-loss { color: #a94442; font-weight: bold; }
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
                        <p class="text-muted">Showing all tasks completed late between <?php echo $start_date_str; ?> and <?php echo $end_date_str; ?></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="table-responsive">
                            <table id="trend-detail-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>DF No.</th>
                                        <th>Task Name</th>
                                        <th>Department</th>
                                        <th>Assigned To</th>
                                        <th>Planned End</th>
                                        <th>Completed On</th>
                                        <th class="text-center">Delay (Days)</th>
                                        <th class="text-center">Est. Loss</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tasks)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No tasks found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($tasks as $task): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($task['df_no']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                                <td><?php echo htmlspecialchars($task['department']); ?></td>
                                                <td><?php echo htmlspecialchars($task['assigned_user_name']); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($task['end_date'])); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($task['task_completed_on'])); ?></td>
                                                <td class="text-center">
                                                    <span class="label label-danger"><?php echo $task['delay_days']; ?></span>
                                                </td>
                                                <td class="text-center text-loss">
                                                    <i class="fa fa-inr"></i> <?php echo number_format($task['estimated_loss'], 0); ?>
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
            // Initialize Datatable
            $('#trend-detail-table').DataTable({
                dom: 'lBfrtip',
                buttons: ['excelHtml5', 'pdfHtml5'],
                "order": [[ 7, "desc" ]] // Default sort by Est. Loss column
            });
        });
    </script>
</body>
</html>