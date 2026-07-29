<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();

// Currency Detection Logic
// Check if the related opportunity is Export or Domestic
$opp_type = $this->db->select('op_type')->from('opportunities')->where('opportunity_id', $order_details->opportunity_id)->get()->row();
$is_export = ($opp_type && $opp_type->op_type != 1); 
$curr_symbol = $is_export ? '$' : '₹';
$curr_text = $is_export ? 'USD' : 'INR';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Order Details</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
        .page-header h2 { font-weight: 600; color: #343a40; }
        .page-header .op-no { font-size: 1rem; color: #6c757d; font-weight: 400; margin-left: 10px; }
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus { color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-top: 3px solid <?php echo $company_info->colorcode ?? '#4a90e2'; ?> !important; }
        .info-list .info-item { display: flex; align-items: center; margin-bottom: 12px; font-size: 14px; }
        .info-list .info-item .info-icon { color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; font-size: 16px; width: 30px; text-align: center; margin-right: 10px; }
        .info-list .info-item .info-label { font-weight: 600; color: #495057; width: 160px; }
        .history-timeline { list-style: none; padding-left: 0; }
        .history-timeline li { position: relative; padding: 10px 0 20px 25px; border-left: 2px solid #e9ecef; }
        .history-timeline li::before { content: ''; position: absolute; left: -8px; top: 12px; width: 14px; height: 14px; background: #fff; border: 3px solid <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-radius: 50%; }
        .is-invalid { border-color: #dc3545 !important; }
        #growl-container { position: fixed; top: 80px; right: 20px; z-index: 9999; width: 320px; }
        .growl-notification { padding: 15px; margin-bottom: 10px; border-radius: 8px; color: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.2); opacity: 0; transform: translateX(100%); transition: all 0.5s; font-size: 14px; }
        .growl-notification.show { opacity: 1; transform: translateX(0); }
        .growl-notification.success { background-color: #28a745; }
        .growl-notification.error { background-color: #dc3545; }
        .finance-card { background: #fcfcfc; border: 1px solid #eee; padding: 15px; border-radius: 8px; margin-top: 15px; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div id="growl-container"></div>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-dashboard"></i> Execution Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo $order_details->order_id; ?>" class="btn btn-primary waves-effect waves-light"><i class="fa fa-tasks"></i> Execution Tasks</a>
                            <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo $order_details->order_id; ?>" class="btn btn-info waves-effect waves-light"><i class="fa fa-bar-chart"></i> Gantt Chart</a>
                            <a href="<?php echo page_url; ?>Spares/running_orders_list" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back to List</a>
                        </div>
                        <h4 class="page-title">Order Tracking & Fulfillment</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="page-header" style="margin-bottom: 25px;">
                        <h2><?php echo htmlspecialchars($order_details->company_name); ?></h2>
                        <span class="op-no">(SO-<?php echo htmlspecialchars($order_details->order_id); ?>)</span>
                    </div>

                    <div class="card-box" id="progressCard">
                        <h4 class="m-t-0 m-b-20 header-title"><b>Update Progress</b></h4>
                        <form id="progressForm" method="post" action="<?php echo page_url; ?>Spares/save_order_progress/<?php echo $order_details->order_id; ?>">
                            <div class="form-group">
                                <label>Current Stage</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order_details->current_stage_name ?? 'Order Confirmed'); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>New Stage <span class="text-danger">*</span></label>
                                <select class="form-control required-field" name="new_stage_id" id="new_stage_select">
                                    <option value="">Select New Stage</option>
                                    <?php foreach ($all_stages as $stage): ?>
                                    <option value="<?php echo $stage->stage_id; ?>"><?php echo htmlspecialchars($stage->stage_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks <span class="text-danger">*</span></label>
                                <textarea rows="4" name="remarks" class="form-control required-field" placeholder="Describe the current status or any roadblocks..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Next Review/Follow-up Date</label>
                                <input class="form-control datepicker" name="followup_date" id="followup_date" type="text" placeholder="dd-mm-yyyy">
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary waves-effect waves-light" style="background-color:<?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-color:<?php echo $company_info->colorcode ?? '#4a90e2'; ?>;">Update Stage</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card-box">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
                            <li><a href="#products" data-toggle="tab">Order Items</a></li>
                            <li><a href="#history" data-toggle="tab">Timeline</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="overview">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Order Summary</b></h4>
                                <div class="info-list">
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-hashtag"></i></div><div class="info-label">Internal SO No.</div><div class="info-value">SO-<?php echo htmlspecialchars($order_details->order_id); ?></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-file-text-o"></i></div><div class="info-label">Customer PO No.</div><div class="info-value"><?php echo htmlspecialchars($order_details->po_no); ?></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-calendar"></i></div><div class="info-label">Confirmation Date</div><div class="info-value"><?php echo date('d M, Y', strtotime($order_details->order_date)); ?></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-user"></i></div><div class="info-label">Marketing Owner</div><div class="info-value"><?php echo htmlspecialchars($order_details->marketing_person_name); ?></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-info-circle"></i></div><div class="info-label">Global Status</div><div class="info-value"><span class="label label-inverse"><?php echo htmlspecialchars($order_details->status); ?></span></div></div>
                                </div>

                                <div class="finance-card">
                                    <h5 class="m-t-0"><b>Financial Breakdown (<?php echo $curr_text; ?>)</b></h5>
                                    <table class="table table-condensed m-b-0">
                                        <tr><td>Total Order Value</td><td class="text-right"><b><?php echo $curr_symbol; ?> <?php echo number_format($order_details->order_value, 2); ?></b></td></tr>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="products">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Items to Fulfill</b></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-light">
                                                <th>#</th><th>Description</th><th class="text-center">Qty</th><th class="text-right">Rate</th><th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($products)): $i = 1; foreach ($products as $product): ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><b><?php echo htmlspecialchars($product->product_code); ?></b><br><small><?php echo htmlspecialchars($product->description); ?></small></td>
                                                <td class="text-center"><?php echo $product->quantity; ?></td>
                                                <td class="text-right"><?php echo number_format($product->price, 2); ?></td>
                                                <td class="text-right"><b><?php echo number_format($product->total, 2); ?></b></td>
                                            </tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="5" class="text-center">No product data found for this order.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="history">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Production Timeline</b></h4>
                                <ul class="history-timeline">
                                    <?php if (!empty($history)): foreach ($history as $item): ?>
                                    <li>
                                        <p class="m-b-5">Update: <b><?php echo htmlspecialchars($item->stage_name); ?></b></p>
                                        <p class="text-muted small">"<?php echo nl2br(htmlspecialchars($item->remarks)); ?>"</p>
                                        <small class="text-muted">
                                            <i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($item->added_by_name); ?> | 
                                            <i class="fa fa-calendar-check-o"></i> <?php echo date('d M Y, h:i A', strtotime($item->added_on)); ?>
                                            <?php if($item->next_follow_date && $item->next_follow_date != '0000-00-00'): ?>
                                                <br><span class="text-warning"><i class="fa fa-bell"></i> Next Step By: <?php echo date('d M Y', strtotime($item->next_follow_date)); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </li>
                                    <?php endforeach; else: ?>
                                    <li><p class="text-muted">Timeline starting... update progress to record the first step.</p></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'dd-mm-yyyy', startDate: new Date() });
            
            $('#progressForm').on('submit', function(e) {
                e.preventDefault();
                let isValid = true;
                $('#progressForm .required-field').each(function() { if (!$(this).val()) { $(this).addClass('is-invalid'); isValid = false; } else { $(this).removeClass('is-invalid'); } });
                if (!isValid) { showGrowl('Please fill all mandatory fields.', 'error'); return false; }
                
                $('#progressCard').block({ message: 'Updating Stage...' });
                var form = $(this);
                $.ajax({
                    type: 'POST', url: form.attr('action'), data: form.serialize(), dataType: 'json',
                    success: function(response) { 
                        if (response.status === 'success') { showGrowl(response.message, 'success'); setTimeout(() => location.reload(), 1500); } 
                        else { $('#progressCard').unblock(); showGrowl(response.message, 'error'); } 
                    },
                    error: function() { $('#progressCard').unblock(); showGrowl('Server communication error.', 'error'); }
                });
            });
        });
        function showGrowl(message, type) {
            var $growl = $('<div class="growl-notification ' + type + '">' + message + '</div>');
            $('#growl-container').append($growl);
            setTimeout(() => { $growl.addClass('show'); }, 10);
            setTimeout(() => { $growl.removeClass('show'); setTimeout(() => { $growl.remove(); }, 500); }, 4000);
        }
    </script>
</body>
</html>
