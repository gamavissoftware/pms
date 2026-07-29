<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();

// Initialize Summary Stats
$total_inr = 0;
$total_usd = 0;
$order_count = 0; // Ensure this name matches the one used in the widget below

if (!empty($running_orders)) {
    $order_count = count($running_orders);
    foreach ($running_orders as $o) {
        if ($o->op_type != 1) { // Export
            $total_usd += $o->order_value;
        } else { // Domestic
            $total_inr += $o->order_value;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Running Orders</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"/>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f9; }
        .page-title { color: <?php echo $company_info->colorcode ?? '#333'; ?>; font-weight: 600; }
        .card-box { border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: none; padding: 20px; }
        
        /* Dashboard Widgets */
        .widget-inline-box { padding: 20px; text-align: center; border-right: 1px solid #eee; }
        .widget-inline-box:last-child { border-right: none; }
        .widget-inline-box h3 { margin: 5px 0; font-weight: 700; color: #333; }
        .widget-inline-box p { color: #888; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; margin-bottom: 0; }
        .text-custom { color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; }

        /* Table Styling */
        .table thead th { background-color: #f8f9fa; color: #333; font-weight: 600; text-transform: uppercase; font-size: 11px; border-bottom: 2px solid #eee; }
        .status-badge { font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 4px; text-transform: uppercase; }
        .status-running { background-color: #e3f2fd; color: #1976d2; border: 1px solid #bbdefb; }
        .label-export { background-color: #7b1fa2; color: #fff; padding: 2px 5px; border-radius: 3px; font-size: 9px; margin-left: 5px; vertical-align: middle; }
        
        .btn-update { background: #fff; border: 1px solid #ddd; color: #555; transition: 0.2s; }
        .btn-update:hover { background: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; color: #fff; border-color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; }
        .btn-execution { margin-right: 6px; }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                         <div class="btn-group pull-right">
                            <a href="<?php echo base_url('spares/dashboard'); ?>" class="btn btn-default waves-effect"><i class="fa fa-dashboard"></i> Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-primary waves-effect"><i class="fa fa-tasks"></i> Execution Dashboard</a>
                        </div>
                        <h4 class="page-title">Active Running Orders</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-md-4 widget-inline-box">
                                <p>Active Orders</p>
                                <h3 class="text-custom"><?php echo $order_count; ?></h3>
                            </div>
                            <div class="col-md-4 widget-inline-box">
                                <p>Domestic Total (INR)</p>
                                <h3>₹ <?php echo number_format($total_inr, 2); ?></h3>
                            </div>
                            <div class="col-md-4 widget-inline-box">
                                <p>Export Total (USD)</p>
                                <h3 class="text-danger">$ <?php echo number_format($total_usd, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="table-responsive">
                            <table id="runningOrdersTable" class="table table-hover" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>SO Reference</th>
                                        <th>Customer & Opportunity</th>
                                        <th>Marketing</th>
                                        <th class="text-right">Order Value</th>
                                        <th class="text-center">Production Stage</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($running_orders)): ?>
                                    <?php foreach ($running_orders as $order): 
                                        $is_exp = ($order->op_type != 1);
                                        $symbol = $is_exp ? '$' : '₹';
                                    ?>
                                        <tr>
                                            <td>
                                                <b><?php echo date('d M, Y', strtotime($order->order_date)); ?></b>
                                            </td>
                                            <td>
                                                <span class="text-muted">SO-<?php echo $order->order_id; ?></span><br>
                                                <small>PO: <?php echo htmlspecialchars($order->po_no); ?></small>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #444;">
                                                    <?php echo htmlspecialchars($order->company_name); ?>
                                                    <?php if($is_exp): ?><span class="label-export">EXPORT</span><?php endif; ?>
                                                </div>
                                                <small class="text-muted">Opp: <?php echo $order->op_no; ?></small>
                                            </td>
                                            <td>
                                                <i class="fa fa-user-o m-r-5"></i> <?php echo htmlspecialchars($order->marketing_person_name); ?>
                                            </td>
                                            <td class="text-right">
                                                <span style="font-weight: 600; font-size: 14px;"><?php echo $symbol; ?> <?php echo number_format($order->order_value, 2); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge status-running">
                                                    <?php echo !empty($order->current_progress_stage) ? $order->current_progress_stage : 'Order Received'; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo $order->order_id; ?>" class="btn btn-primary btn-xs waves-effect btn-execution">
                                                    <i class="fa fa-tasks"></i> Execution Tasks
                                                </a>
                                                <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo $order->order_id; ?>" class="btn btn-info btn-xs waves-effect">
                                                    <i class="fa fa-bar-chart"></i> Gantt
                                                </a>
                                                <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo $order->order_id; ?>" class="btn btn-update btn-xs waves-effect">
                                                    <i class="fa fa-pencil"></i> Update Progress
                                                </a>
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#runningOrdersTable').DataTable({
            "order": [[ 0, "desc" ]],
            "pageLength": 25,
            "language": { 
                "search": "Filter Orders:",
                "lengthMenu": "Display _MENU_"
            },
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-5"i><"col-sm-7"p>>'
        });
    });
    </script>
</body>
</html>
