<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_label = isset($workflows[$execution_order->workflow_type]) ? $workflows[$execution_order->workflow_type] : $execution_order->workflow_type;
$sf_status = !empty($sf_form->form_status) ? $sf_form->form_status : 'Draft';
$is_released = $sf_status === 'Released';
$route_departments = array('Design HOD', 'Spares HOD', 'Purchase HOD', 'PPC Spares');
$dispatch_modes = array('COURIER' => 'Courier', 'SELF_PICKUP' => 'Self Pickup');
$prepared_name = !empty($prepared_by_name) ? $prepared_by_name : 'Current user';
$order_type = !empty($order_snapshot->op_type) && (int) $order_snapshot->op_type !== 1 ? 'International' : 'Domestic';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; <?php echo $page_title; ?></title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <style>
        body { background: #f4f6f9; color: #223044; }
        .sf-shell { max-width: 1440px; margin: 0 auto; }
        .sf-panel { background: #fff; border: 1px solid #dfe6ef; border-radius: 8px; margin-bottom: 18px; }
        .sf-panel-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border-bottom: 1px solid #e7edf4; }
        .sf-panel-title { margin: 0; font-size: 15px; font-weight: 700; color: #172033; }
        .sf-panel-body { padding: 16px; }
        .sf-kpi { background: #fff; border: 1px solid #dfe6ef; border-left: 4px solid #2f80ed; border-radius: 8px; padding: 14px; margin-bottom: 18px; min-height: 86px; }
        .sf-kpi-label { font-size: 11px; text-transform: uppercase; color: #6d7b8f; font-weight: 700; }
        .sf-kpi-value { font-size: 18px; font-weight: 700; color: #1c2b3f; margin-top: 5px; word-break: break-word; }
        .sf-status { display: inline-block; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .sf-status-draft { background: #fff4dd; color: #966200; }
        .sf-status-released { background: #e2f7ec; color: #157347; }
        .sf-route-chip { display: inline-block; margin: 0 6px 6px 0; padding: 6px 10px; border-radius: 999px; background: #eef4fb; color: #2e4a66; font-weight: 600; }
        .sf-table th { background: #27374d; color: #fff; font-size: 12px; white-space: nowrap; }
        .sf-table td { vertical-align: top !important; }
        .sf-table textarea { min-width: 240px; resize: vertical; }
        .sf-table input[type="date"] { min-width: 142px; }
        .sf-table .qty-cell { min-width: 90px; }
        .required:after { content: " *"; color: #d93025; }
        .sticky-actions { position: sticky; bottom: 0; background: #fff; border-top: 1px solid #e2e8f0; padding: 12px 16px; margin: 0 -16px -16px; z-index: 5; }
        @media print {
            #topnav, .page-title-box .btn-group, .alert, .sticky-actions, .row-tools, .btn { display: none !important; }
            body { background: #fff; }
            .wrapper, .container-fluid, .sf-shell { padding: 0; margin: 0; max-width: none; }
            .sf-panel, .sf-kpi { border-color: #111; box-shadow: none; }
            input, textarea, select { border: 0 !important; box-shadow: none !important; padding-left: 0 !important; }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid sf-shell">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                            <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Execution Tracker</a>
                            <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-info waves-effect waves-light"><i class="fa fa-cogs"></i> MRP Report</a>
                            <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-file-text-o"></i> Order Detail</a>
                            <a href="#" onclick="window.print(); return false;" class="btn btn-default waves-effect waves-light"><i class="fa fa-print"></i> Print</a>
                        </div>
                        <h4 class="page-title">Spare Form (SF)</h4>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="sf-kpi">
                        <div class="sf-kpi-label">Customer</div>
                        <div class="sf-kpi-value"><?php echo html_escape($order_snapshot->company_name); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="sf-kpi">
                        <div class="sf-kpi-label">Quotation Type</div>
                        <div class="sf-kpi-value"><?php echo html_escape($workflow_label); ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="sf-kpi">
                        <div class="sf-kpi-label">Order Type</div>
                        <div class="sf-kpi-value"><?php echo html_escape($order_type); ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="sf-kpi">
                        <div class="sf-kpi-label">Commit Date</div>
                        <div class="sf-kpi-value"><?php echo !empty($execution_order->commit_date) ? date('d M Y', strtotime($execution_order->commit_date)) : '-'; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="sf-kpi">
                        <div class="sf-kpi-label">SF Status</div>
                        <div class="sf-kpi-value"><span class="sf-status <?php echo $is_released ? 'sf-status-released' : 'sf-status-draft'; ?>"><?php echo html_escape($sf_status); ?></span></div>
                    </div>
                </div>
            </div>

            <form method="post" action="<?php echo page_url; ?>Spares_execution/save_sf_form">
                <input type="hidden" name="order_id" value="<?php echo (int) $order_snapshot->order_id; ?>">

                <div class="sf-panel">
                    <div class="sf-panel-head">
                        <h4 class="sf-panel-title">SF Release Header</h4>
                        <span class="text-muted">From: Spares Department</span>
                    </div>
                    <div class="sf-panel-body">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="required">SF No.</label>
                                <input type="text" name="sf_no" class="form-control" value="<?php echo html_escape($sf_form->sf_no); ?>" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="required">Date of Release</label>
                                <input type="date" name="release_date" class="form-control" value="<?php echo html_escape($sf_form->release_date); ?>" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Prepared By</label>
                                <input type="text" class="form-control" value="<?php echo html_escape($prepared_name); ?>" readonly>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Scope of Supply</label>
                                <input type="text" name="scope_of_supply" class="form-control" value="<?php echo html_escape($sf_form->scope_of_supply); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>To</label>
                                <div>
                                    <?php foreach ($route_departments as $department): ?>
                                        <span class="sf-route-chip"><i class="fa fa-check-circle"></i> <?php echo html_escape($department); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Dispatch Requirement</label>
                                <div class="row">
                                    <div class="col-md-5 form-group">
                                        <select name="dispatch_mode" class="form-control">
                                            <?php foreach ($dispatch_modes as $mode_key => $mode_label): ?>
                                                <option value="<?php echo $mode_key; ?>" <?php echo $sf_form->dispatch_mode === $mode_key ? 'selected' : ''; ?>><?php echo $mode_label; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-7 form-group">
                                        <input type="text" name="document_requirement" class="form-control" value="<?php echo html_escape($sf_form->document_requirement); ?>" placeholder="Country wise document requirement">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Packing Time (Days)</label>
                                <input type="number" min="0" name="packing_duration_days" class="form-control" value="<?php echo (int) $sf_form->packing_duration_days; ?>">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Dispatch Time (Days)</label>
                                <input type="number" min="0" name="dispatch_duration_days" class="form-control" value="<?php echo (int) $sf_form->dispatch_duration_days; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sf-panel">
                    <div class="sf-panel-head">
                        <h4 class="sf-panel-title">Description of the Order</h4>
                    </div>
                    <div class="sf-panel-body">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Purchase Order No.</label>
                                <input type="text" name="po_no" class="form-control" value="<?php echo html_escape($sf_form->po_no); ?>">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Purchase Order Date</label>
                                <input type="date" name="po_date" class="form-control" value="<?php echo html_escape($sf_form->po_date); ?>">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>PO Rev.</label>
                                <input type="text" name="po_revision" class="form-control" value="<?php echo html_escape($sf_form->po_revision); ?>">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Quotation Ref.</label>
                                <input type="text" name="quotation_ref" class="form-control" value="<?php echo html_escape($sf_form->quotation_ref); ?>">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>Rev.</label>
                                <input type="text" name="quotation_revision" class="form-control" value="<?php echo html_escape($sf_form->quotation_revision); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Machine Description</label>
                                <input type="text" name="machine_description" class="form-control" value="<?php echo html_escape($sf_form->machine_description); ?>">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Machine DF</label>
                                <input type="text" name="machine_df" class="form-control" value="<?php echo html_escape($sf_form->machine_df); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Order Description</label>
                                <input type="text" name="order_description" class="form-control" value="<?php echo html_escape($sf_form->order_description); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sf-panel">
                    <div class="sf-panel-head">
                        <h4 class="sf-panel-title">Technical Specs</h4>
                    </div>
                    <div class="sf-panel-body">
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label>No. of Tracks</label>
                                <input type="text" name="no_of_tracks" class="form-control" value="<?php echo html_escape($sf_form->no_of_tracks); ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Product to be Packed</label>
                                <input type="text" name="product_to_be_packed" class="form-control" value="<?php echo html_escape($sf_form->product_to_be_packed); ?>" placeholder="Liq./Powder or as per sample">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Quantity Packed</label>
                                <input type="text" name="quantity_to_be_packed" class="form-control" value="<?php echo html_escape($sf_form->quantity_to_be_packed); ?>">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Pouch Size & Type</label>
                                <input type="text" name="pouch_size_type" class="form-control" value="<?php echo html_escape($sf_form->pouch_size_type); ?>">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>Axis</label>
                                <input type="text" name="no_of_axis" class="form-control" value="<?php echo html_escape($sf_form->no_of_axis); ?>">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>PLC</label>
                                <input type="text" name="plc_make" class="form-control" value="<?php echo html_escape($sf_form->plc_make); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sf-panel">
                    <div class="sf-panel-head">
                        <h4 class="sf-panel-title">Spare Details</h4>
                        <button type="button" id="addSfItemRow" class="btn btn-info btn-sm row-tools"><i class="fa fa-plus"></i> Add Row</button>
                    </div>
                    <div class="sf-panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered sf-table" id="sfItemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 55px;">S.No.</th>
                                        <th>Item Description</th>
                                        <th>Part No./ERP</th>
                                        <th>Drg. Rev.</th>
                                        <th>Qty</th>
                                        <th>Target Date</th>
                                        <th>Dispatch 1</th>
                                        <th>Dispatch 2</th>
                                        <th>Dispatch 3</th>
                                        <th class="row-tools" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td class="line-no"><?php echo $index + 1; ?></td>
                                            <td><textarea name="item_description[]" class="form-control" rows="2"><?php echo html_escape($item->item_description); ?></textarea></td>
                                            <td><input type="text" name="part_no_erp[]" class="form-control" value="<?php echo html_escape($item->part_no_erp); ?>"></td>
                                            <td><input type="text" name="drg_rev_no[]" class="form-control" value="<?php echo html_escape($item->drg_rev_no); ?>"></td>
                                            <td><input type="number" step="0.001" min="0" name="quantity[]" class="form-control qty-cell" value="<?php echo html_escape($item->quantity); ?>"></td>
                                            <td><input type="date" name="target_date[]" class="form-control" value="<?php echo html_escape($item->target_date); ?>"></td>
                                            <td><input type="date" name="dispatch_1_date[]" class="form-control" value="<?php echo html_escape($item->dispatch_1_date); ?>"></td>
                                            <td><input type="date" name="dispatch_2_date[]" class="form-control" value="<?php echo html_escape($item->dispatch_2_date); ?>"></td>
                                            <td><input type="date" name="dispatch_3_date[]" class="form-control" value="<?php echo html_escape($item->dispatch_3_date); ?>"></td>
                                            <td class="row-tools text-center"><button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <label>Be Noted / Specifications</label>
                            <textarea name="specification_note" class="form-control" rows="4"><?php echo html_escape($sf_form->specification_note); ?></textarea>
                        </div>
                        <div class="sticky-actions text-right">
                            <?php if (!empty($can_manage_schedule)): ?>
                                <button type="submit" name="sf_action" value="draft" class="btn btn-default"><i class="fa fa-save"></i> Save Draft</button>
                                <button type="submit" name="sf_action" value="release" class="btn btn-success" onclick="return confirm('Release this SF form and complete the Create SF task?');"><i class="fa fa-check"></i> Release SF</button>
                            <?php else: ?>
                                <span class="text-muted">You can view this SF form, but only the execution owner can update it.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('common/footer'); ?>
    <script>
        (function () {
            function refreshLineNumbers() {
                var rows = document.querySelectorAll('#sfItemsTable tbody tr');
                Array.prototype.forEach.call(rows, function (row, index) {
                    var lineNo = row.querySelector('.line-no');
                    if (lineNo) {
                        lineNo.textContent = index + 1;
                    }
                });
            }

            var addButton = document.getElementById('addSfItemRow');
            var tableBody = document.querySelector('#sfItemsTable tbody');

            if (!addButton || !tableBody) {
                return;
            }

            addButton.addEventListener('click', function () {
                var row = '<tr>' +
                    '<td class="line-no"></td>' +
                    '<td><textarea name="item_description[]" class="form-control" rows="2"></textarea></td>' +
                    '<td><input type="text" name="part_no_erp[]" class="form-control"></td>' +
                    '<td><input type="text" name="drg_rev_no[]" class="form-control"></td>' +
                    '<td><input type="number" step="0.001" min="0" name="quantity[]" class="form-control qty-cell" value="0"></td>' +
                    '<td><input type="date" name="target_date[]" class="form-control"></td>' +
                    '<td><input type="date" name="dispatch_1_date[]" class="form-control"></td>' +
                    '<td><input type="date" name="dispatch_2_date[]" class="form-control"></td>' +
                    '<td><input type="date" name="dispatch_3_date[]" class="form-control"></td>' +
                    '<td class="row-tools text-center"><button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
                tableBody.insertAdjacentHTML('beforeend', row);
                refreshLineNumbers();
            });

            tableBody.addEventListener('click', function (event) {
                var target = event.target;
                while (target && target !== tableBody && !target.classList.contains('remove-row')) {
                    target = target.parentNode;
                }

                if (target && target.classList.contains('remove-row')) {
                    var rows = tableBody.querySelectorAll('tr');
                    if (rows.length > 1) {
                        target.closest('tr').remove();
                        refreshLineNumbers();
                    }
                }
            });
        })();
    </script>
</body>
</html>
