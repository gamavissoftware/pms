<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Modify Purchase Order</title>
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
        .is-invalid { border-color: #dc3545 !important; }
        .total-row td { font-weight: bold; font-size: 1.1em; }
        /* Growl Notification Styles */
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
                            <a href="<?php echo page_url; ?>Spares/opportunity_detail/<?php echo $po_details->opportunity_id; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back to Opportunity</a>
                        </div>
                        <h4 class="page-title">Modify Purchase Order</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form id="poForm" method="post" action="<?php echo page_url; ?>Spares/update_po">
                        <input type="hidden" name="po_id" value="<?php echo $po_details->po_id; ?>">
                        <input type="hidden" name="opportunity_id" value="<?php echo $po_details->opportunity_id; ?>">
                        
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>PO Details</b></h4>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="po_no">Customer PO Number <span class="text-danger">*</span></label>
                                    <input type="text" id="po_no" name="po_no" class="form-control required-field" value="<?php echo htmlspecialchars($po_details->po_no); ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="po_date">PO Date <span class="text-danger">*</span></label>
                                    <input type="text" id="po_date" name="po_date" class="form-control required-field datepicker" value="<?php echo date('d-m-Y', strtotime($po_details->po_date)); ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="delivery_date">Expected Delivery Date</label>
                                    <input type="text" id="delivery_date" name="delivery_date" class="form-control datepicker" value="<?php echo $po_details->expected_delivery_date ? date('d-m-Y', strtotime($po_details->expected_delivery_date)) : ''; ?>">
                                </div>
                            </div>

                            <h4 class="m-t-20 header-title"><b>Products</b></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Product</th>
                                            <th style="width: 15%;">Quantity</th>
                                            <th style="width: 20%;">Unit Price (INR)</th>
                                            <th style="width: 25%;" class="text-right">Line Total (INR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $index => $product): ?>
                                        <tr class="product-row">
                                            <td>
                                                <?php echo htmlspecialchars($product->product_code . ' - ' . $product->description); ?>
                                                <input type="hidden" name="products[<?php echo $index; ?>][product_id]" value="<?php echo $product->product_id; ?>">
                                                <input type="hidden" name="products[<?php echo $index; ?>][description]" value="<?php echo htmlspecialchars($product->description); ?>">
                                            </td>
                                            <td><input type="number" name="products[<?php echo $index; ?>][quantity]" class="form-control quantity-input required-field" value="<?php echo $product->quantity; ?>" min="1"></td>
                                            <td><input type="number" name="products[<?php echo $index; ?>][price]" class="form-control price-input required-field" value="<?php echo $product->price; ?>" min="0" step="0.01"></td>
                                            <td class="text-right line-total">0.00</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="3" class="text-right"><strong>Sub-Total</strong></td>
                                            <td class="text-right"><span id="subTotal">0.00</span></td>
                                            <input type="hidden" name="sub_total" id="sub_total_input">
                                        </tr>
                                        <tr class="total-row" style="background-color: #f0f2f5;">
                                            <td colspan="3" class="text-right"><strong>Grand Total</strong></td>
                                            <td class="text-right"><span id="grandTotal">0.00</span></td>
                                            <input type="hidden" name="grand_total" id="grand_total_input">
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fa fa-check"></i> Save Changes</button>
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
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'dd-mm-yyyy' });

            function calculateTotals() {
                var subTotal = 0;
                $('.product-row').each(function() {
                    var qty = parseFloat($(this).find('.quantity-input').val()) || 0;
                    var price = parseFloat($(this).find('.price-input').val()) || 0;
                    var lineTotal = qty * price;
                    $(this).find('.line-total').text(lineTotal.toFixed(2));
                    subTotal += lineTotal;
                });
                $('#subTotal').text(subTotal.toFixed(2));
                $('#grandTotal').text(subTotal.toFixed(2));
                $('#sub_total_input').val(subTotal.toFixed(2));
                $('#grand_total_input').val(subTotal.toFixed(2));
            }
            $('.quantity-input, .price-input').on('input', calculateTotals);
            calculateTotals();

            $('#poForm').on('submit', function(e) {
                e.preventDefault();
                let isValid = true;
                $('#poForm .required-field').each(function() {
                    if (!$(this).val() || ($(this).is('input[type=number]') && parseFloat($(this).val()) < 0)) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                if (!isValid) {
                    showGrowl('Please fill all required fields correctly.', 'error');
                    return false;
                }
                var form = $(this);
                $('body').block({ 
                    message: '<h5><i class="fa fa-spinner fa-spin"></i> Updating PO...</h5>', 
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
                            setTimeout(() => { window.location.href = response.redirect_url; }, 2000);
                        } else {
                            $('body').unblock();
                            showGrowl(response.message || 'An unknown error occurred.', 'error');
                        }
                    },
                    error: function() {
                        $('body').unblock();
                        showGrowl('Could not connect to the server.', 'error');
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