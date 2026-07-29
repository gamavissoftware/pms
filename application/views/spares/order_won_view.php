<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Confirm Order Won</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
        .summary-label { font-weight: 600; color: #495057; }
        .summary-value { color: #28a745; font-weight: 700; font-size: 1.4rem; }
        .table-items thead { background: #f4f8fb; }
        .finance-summary { background: #fcfcfc; border: 1px solid #eee; padding: 15px; border-radius: 8px; margin-top: 20px; }
        #growl-container { position: fixed; top: 80px; right: 20px; z-index: 9999; width: 320px; }
        .growl-notification { padding: 15px; margin-bottom: 10px; border-radius: 8px; color: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.2); opacity: 0; transform: translateX(100%); transition: all 0.5s; font-size: 14px; }
        .growl-notification.show { opacity: 1; transform: translateX(0); }
        .growl-notification.success { background-color: #28a745; }
        .growl-notification.error { background-color: #dc3545; }
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
                            <a href="<?php echo page_url; ?>Spares/opportunity_detail/<?php echo $this->uri->segment(3); ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                        <h4 class="page-title">Final Order Confirmation</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <form id="orderWonForm" method="post" action="<?php echo page_url; ?>Spares/save_won_order">
                        <input type="hidden" name="opportunity_id" value="<?php echo $this->uri->segment(3); ?>">
                        <input type="hidden" name="po_id" value="<?php echo $order_data->po_id; ?>">
                        <input type="hidden" name="po_no" value="<?php echo htmlspecialchars($order_data->po_no); ?>">
                        <input type="hidden" name="customer_id" value="<?php echo $order_data->customer_id; ?>">
                        <input type="hidden" name="marketing_person_id" value="<?php echo $order_data->marketing_person_id; ?>">
                        <input type="hidden" name="order_value" value="<?php echo $order_data->grand_total; ?>">
                        
                        <div class="card-box">
                            <div class="text-center m-b-30">
                                <h3 class="m-t-0" style="color: #28a745;"><i class="fa fa-check-circle"></i> <b>Confirm Won Order</b></h3>
                                <p class="text-muted">Review the final Purchase Order details before finalizing the sale.</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="m-b-5"><span class="summary-label">Customer:</span> <?php echo htmlspecialchars($order_data->company_name); ?></p>
                                    <p class="m-b-5"><span class="summary-label">PO Number:</span> <?php echo htmlspecialchars($order_data->po_no); ?></p>
                                    <p class="m-b-5"><span class="summary-label">PO Date:</span> <?php echo date('d-M-Y', strtotime($order_data->po_date)); ?></p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <span class="summary-label">Total Order Value:</span><br>
                                    <span class="summary-value">₹ <?php echo number_format($order_data->grand_total, 2); ?></span>
                                </div>
                            </div>

                            <h4 class="m-t-30 header-title"><b>Items Included in Order</b></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-items">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item Description</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Unit Rate</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach($order_data->items as $item): ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><b><?php echo $item->product_code; ?></b> - <?php echo $item->description; ?></td>
                                            <td class="text-center"><?php echo $item->quantity; ?></td>
                                            <td class="text-right"><?php echo number_format($item->price, 2); ?></td>
                                            <td class="text-right"><b><?php echo number_format($item->total, 2); ?></b></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-md-offset-6">
                                    <div class="finance-summary">
                                        <table class="table m-b-0">
                                            <tr>
                                                <td>Basic Total</td>
                                                <td class="text-right"><?php echo number_format($order_data->sub_total, 2); ?></td>
                                            </tr>
                                            <?php if($order_data->packing_percent > 0): ?>
                                            <tr>
                                                <td>Packing (<?php echo $order_data->packing_percent; ?>%)</td>
                                                <td class="text-right"><?php echo number_format(($order_data->sub_total * $order_data->packing_percent)/100, 2); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($order_data->freight_charge > 0): ?>
                                            <tr>
                                                <td>Freight Charges</td>
                                                <td class="text-right"><?php echo number_format($order_data->freight_charge, 2); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr class="bg-light">
                                                <td><b>Taxable Value</b></td>
                                                <td class="text-right"><b><?php echo number_format($order_data->taxable_value, 2); ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>GST (18%)</td>
                                                <td class="text-right"><?php echo number_format($order_data->gst_amount, 2); ?></td>
                                            </tr>
                                            <tr class="success">
                                                <td><h4><b>Grand Total</b></h4></td>
                                                <td class="text-right"><h4><b>₹ <?php echo number_format($order_data->grand_total, 2); ?></b></h4></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 form-group">
                                     <label for="order_date">Confirmation Date <span class="text-danger">*</span></label>
                                     <input type="text" id="order_date" name="order_date" class="form-control datepicker text-center" value="<?php echo date('d-m-Y'); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center m-t-20">
                                    <button type="submit" class="btn btn-success btn-lg waves-effect waves-light"><i class="fa fa-trophy"></i> Confirm & Close Opportunity</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'dd-mm-yyyy' });

            $('#orderWonForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $('body').block({ 
                    message: '<h5><i class="fa fa-spinner fa-spin"></i> Processing Order...</h5>', 
                    css: { border: 'none', padding: '15px', backgroundColor: '#000', borderRadius: '10px', opacity: .5, color: '#fff' } 
                });

                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showGrowl(response.message, 'success');
                            setTimeout(() => { window.location.href = response.redirect_url; }, 1500);
                        } else {
                            $('body').unblock();
                            showGrowl(response.message || 'Error occurred.', 'error');
                        }
                    },
                    error: function() {
                        $('body').unblock();
                        showGrowl('Connection failed.', 'error');
                    }
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