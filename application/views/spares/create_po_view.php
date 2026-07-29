<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Create Purchase Order</title>
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
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); padding: 25px; margin-bottom: 25px; }
        .is-invalid { border-color: #dc3545 !important; }
        .total-row td { font-weight: bold; font-size: 1.1em; }
        #growl-container { position: fixed; top: 80px; right: 20px; z-index: 9999; width: 320px; }
        .growl-notification { padding: 15px; margin-bottom: 10px; border-radius: 8px; color: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.2); opacity: 0; transform: translateX(100%); transition: all 0.5s; font-size: 14px; }
        .growl-notification.show { opacity: 1; transform: translateX(0); }
        .growl-notification.success { background-color: #28a745; }
        .growl-notification.error { background-color: #dc3545; }
        .summary-box { background: #f9f9f9; border: 1px solid #eee; padding: 15px; border-radius: 8px; }
        .table-condensed > tbody > tr > td { padding: 8px; border-top: 1px solid #eee; }
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
                            <a href="<?php echo page_url; ?>Spares/opportunity_detail/<?php echo $details->opportunity_id; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                        <h4 class="page-title">Create Purchase Order</h4>
                    </div>
                </div>
            </div>

            <form id="poForm" method="post" action="<?php echo page_url; ?>Spares/save_po" enctype="multipart/form-data">
                <input type="hidden" name="opportunity_id" value="<?php echo $details->opportunity_id; ?>">
                <input type="hidden" name="customer_id" value="<?php echo $details->customer_id; ?>">

                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Basic Info</b></h4>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Customer PO Number <span class="text-danger">*</span></label>
                            <input type="text" name="po_no" class="form-control required-field" placeholder="Enter PO No.">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>PO Date <span class="text-danger">*</span></label>
                            <input type="text" name="po_date" class="form-control required-field datepicker" value="<?php echo date('d-m-Y'); ?>">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Delivery Date</label>
                            <input type="text" name="delivery_date" class="form-control datepicker" placeholder="dd-mm-yyyy">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Upload PO Copy (PDF/JPG) <span class="text-danger">*</span></label>
                            <input type="file" name="po_file" id="po_file" class="form-control required-field" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Product Items</b></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th>Description</th>
                                    <th style="width: 100px;">Qty</th>
                                    <th style="width: 150px;">Price (INR)</th>
                                    <th style="width: 180px;" class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): foreach ($products as $index => $product): ?>
                                <tr class="product-row">
                                    <td>
                                        <strong><?php echo htmlspecialchars($product->code); ?></strong> - <?php echo htmlspecialchars($product->description); ?>
                                        <input type="hidden" name="products[<?php echo $index; ?>][product_id]" value="<?php echo $product->product_id; ?>">
                                        <input type="hidden" name="products[<?php echo $index; ?>][description]" value="<?php echo htmlspecialchars($product->description); ?>">
                                    </td>
                                    <td><input type="number" name="products[<?php echo $index; ?>][quantity]" class="form-control qty-input required-field" value="<?php echo $product->quantity; ?>" min="1"></td>
                                    <td><input type="number" step="0.01" name="products[<?php echo $index; ?>][price]" class="form-control price-input required-field" value="<?php echo $product->price; ?>"></td>
                                    <td class="text-right line-total">0.00</td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center text-danger">No items found in quotation.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="summary-box">
                                <h5 class="m-t-0"><b>Customer:</b> <?php echo $details->company_name; ?></h5>
                                <p class="m-b-5 text-muted">Opportunity: <?php echo $details->op_no; ?></p>
                                <p class="m-b-0 text-muted">Source: Latest <?php echo htmlspecialchars($details->source_document ?? 'Quotation'); ?><?php echo !empty($details->source_document_no) ? ' - ' . htmlspecialchars($details->source_document_no) : ''; ?></p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="summary-box">
                                <table class="table table-condensed m-b-0">
                                    <tr>
                                        <td>Basic Value</td>
                                        <td class="text-right"><span id="txt_subtotal">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Packing Charges (%) 
                                            <input type="number" step="0.01" name="packing_percent" id="in_pack_per" class="form-control input-sm pull-right" style="width:70px; display:inline;" value="<?php echo $details->packing_percent ?? 0; ?>">
                                        </td>
                                        <td class="text-right"><span id="txt_packing">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Freight Charges</td>
                                        <td class="text-right">
                                            <input type="number" step="0.01" name="freight_charge" id="in_freight" class="form-control input-sm text-right" value="<?php echo $details->freight_charge ?? 0; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Insurance (%)
                                            <input type="number" step="0.01" name="insurance_percent" id="in_ins_per" class="form-control input-sm pull-right" style="width:70px; display:inline;" value="<?php echo $details->insurance_percent ?? 0; ?>">
                                        </td>
                                        <td class="text-right"><span id="txt_insurance">0.00</span></td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td><b>Taxable Value</b></td>
                                        <td class="text-right"><b id="txt_taxable">0.00</b></td>
                                    </tr>
                                    <tr>
                                        <td>GST (18%)</td>
                                        <td class="text-right"><span id="txt_gst">0.00</span></td>
                                    </tr>
                                    <tr class="total-row bg-primary text-white">
                                        <td>GRAND TOTAL</td>
                                        <td class="text-right">
                                            <span id="txt_grandtotal">0.00</span>
                                            <input type="hidden" name="sub_total" id="hidden_subtotal">
                                            <input type="hidden" name="taxable_value" id="hidden_taxable">
                                            <input type="hidden" name="gst_amount" id="hidden_gst">
                                            <input type="hidden" name="grand_total" id="hidden_grandtotal">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row m-t-20">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-inverse btn-lg waves-effect waves-light"><i class="fa fa-save"></i> Save PO</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'dd-mm-yyyy' });

            function calculatePO() {
                var basicTotal = 0;
                $('.product-row').each(function() {
                    var q = parseFloat($(this).find('.qty-input').val()) || 0;
                    var p = parseFloat($(this).find('.price-input').val()) || 0;
                    var line = q * p;
                    $(this).find('.line-total').text(line.toFixed(2));
                    basicTotal += line;
                });

                var packPer = parseFloat($('#in_pack_per').val()) || 0;
                var freight = parseFloat($('#in_freight').val()) || 0;
                var insPer = parseFloat($('#in_ins_per').val()) || 0;

                var packAmt = (basicTotal * packPer) / 100;
                var insAmt = (basicTotal * insPer) / 100;
                var taxable = basicTotal + packAmt + freight + insAmt;
                var gst = (taxable * 18) / 100;
                var grand = taxable + gst;

                $('#txt_subtotal').text(basicTotal.toFixed(2));
                $('#txt_packing').text(packAmt.toFixed(2));
                $('#txt_insurance').text(insAmt.toFixed(2));
                $('#txt_taxable').text(taxable.toFixed(2));
                $('#txt_gst').text(gst.toFixed(2));
                $('#txt_grandtotal').text(grand.toFixed(2));

                $('#hidden_subtotal').val(basicTotal.toFixed(2));
                $('#hidden_taxable').val(taxable.toFixed(2));
                $('#hidden_gst').val(gst.toFixed(2));
                $('#hidden_grandtotal').val(grand.toFixed(2));
            }

            $(document).on('input', '.qty-input, .price-input, #in_pack_per, #in_freight, #in_ins_per', calculatePO);
            calculatePO();

            $('#poForm').on('submit', function(e) {
    e.preventDefault();
    let isValid = true;
    
    $('#poForm .required-field').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else { $(this).removeClass('is-invalid'); }
    });

    if (!isValid) {
        showGrowl('Please fill all required fields and select PO file.', 'error');
        return false;
    }

    var form = $(this);
    var formData = new FormData(form[0]); // Use form[0] to get the DOM element

    // --- ADD CSRF TOKEN MANUALLY IF ENABLED ---
    // This assumes your config has CSRF enabled
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

    $('body').block({ 
        message: '<h5><i class="fa fa-spinner fa-spin"></i> Saving...</h5>', 
        css: { border: 'none', padding: '15px', backgroundColor: '#000', borderRadius: '10px', opacity: .5, color: '#fff' } 
    });

    $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showGrowl(response.message, 'success');
                setTimeout(() => { window.location.href = response.redirect_url; }, 1500);
            } else {
                $('body').unblock();
                showGrowl(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            $('body').unblock();
            // Log the actual error to console for debugging
            console.log(xhr.responseText); 
            showGrowl('Server Error: ' + error, 'error');
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
