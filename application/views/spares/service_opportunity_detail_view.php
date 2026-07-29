<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();

// Currency Detection Logic based on Service Type
$is_export = ($opportunity->op_type != 1); 
$curr_symbol = $is_export ? '$' : '₹';
$pi_currency = !empty($service_pi->currency) ? strtoupper(trim((string) $service_pi->currency)) : ($is_export ? 'USD' : 'INR');
$pi_curr_symbol = $pi_currency === 'USD' ? '$' : '₹';
$payment_permissions = isset($payment_permissions) && is_array($payment_permissions) ? $payment_permissions : array();
$can_create_service_payment = !empty($payment_permissions['can_create']);

// Logic to identify if current stage 8 was an Approval or Rejection
$is_rejected = (strpos(strtolower($opportunity->remarks), 'rejected') !== false);
$is_approved = (strpos(strtolower($opportunity->remarks), 'approved') !== false);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Service Details</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); padding: 25px; margin-bottom: 25px; }
        .page-header h2 { font-weight: 600; color: #343a40; margin: 0; display: inline-block; vertical-align: middle; }
        .nav-tabs > li.active > a { color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-top: 3px solid <?php echo $company_info->colorcode ?? '#4a90e2'; ?> !important; font-weight: 600; }
        .history-timeline { list-style: none; padding-left: 0; }
        .history-timeline li { position: relative; padding: 10px 0 20px 25px; border-left: 2px solid #e9ecef; }
        .history-timeline li::before { content: ''; position: absolute; left: -8px; top: 12px; width: 14px; height: 14px; background: #fff; border: 3px solid <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-radius: 50%; }
        .approval-card { border-left: 5px solid #f9c851; background-color: #fffcf5; }
        .rejection-alert { border-left: 5px solid #ec1a1a; background-color: #fff5f5; }
        .success-alert { border-left: 5px solid #28a745; background-color: #f6fff8; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                             <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back to List</a>
                        </div>
                        <h4 class="page-title">Service Tracking: <?php echo htmlspecialchars($opportunity->op_no); ?></h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="page-header" style="margin-bottom: 15px;">
                        <h2><?php echo htmlspecialchars($opportunity->company_name); ?></h2>
                    </div>

                    <?php if($opportunity->current_stage_id == 3): ?>
                    <div class="card-box approval-card">
                        <h4 class="m-t-0 text-warning"><b><i class="fa fa-hourglass-half"></i> Action Required: Approval</b></h4>
                        <p>A quotation has been generated. Please review and provide your decision.</p>
                        <div class="m-t-20">
                            <button type="button" class="btn btn-success btn-sm approval-btn" data-status="Approved"><i class="fa fa-check"></i> Approve</button>
                            <button type="button" class="btn btn-danger btn-sm approval-btn" data-status="Rejected"><i class="fa fa-times"></i> Reject</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($opportunity->current_stage_id == 8 && $is_rejected): ?>
                    <div class="card-box rejection-alert">
                        <h4 class="m-t-0 text-danger"><b><i class="fa fa-ban"></i> Quotation Rejected</b></h4>
                        <p><strong>Reason:</strong> <?php echo htmlspecialchars($opportunity->remarks); ?></p>
                        <p class="small text-muted">Select "Revised Quotation" below to correct and resubmit.</p>
                    </div>
                    <?php elseif($opportunity->current_stage_id == 8 && $is_approved): ?>
                    <div class="card-box success-alert">
                        <h4 class="m-t-0 text-success"><b><i class="fa fa-check-circle"></i> Quotation Approved</b></h4>
                        <p>The quotation is ready. You can now move to "Quotation Shared & Followup".</p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($missing_quote_warning)): ?>
                    <div class="card-box rejection-alert">
                        <h4 class="m-t-0 text-danger"><b><i class="fa fa-exclamation-triangle"></i> Quotation Record Missing</b></h4>
                        <p>No saved quotation record was found for this opportunity, so the Quotes tab is empty.</p>
                        <p class="small text-muted m-b-15">This usually happens when the lead was moved for approval without a generated quotation being saved.</p>
                        <a href="<?php echo page_url; ?>ServiceLeads/create_quotation/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-danger btn-sm">
                            <i class="fa fa-plus"></i> Create Quotation Now
                        </a>
                    </div>
                    <?php endif; ?>

                    <div class="card-box" id="progressCard">
                        <div class="clearfix m-b-20">
                            <h4 class="m-t-0 header-title pull-left"><b>Move Pipeline Stage</b></h4>
                            <?php if (!empty($has_quotation)): ?>
                                <a href="<?php echo page_url; ?>ServiceLeads/revise_quotation/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-warning btn-sm pull-right">
                                    <i class="fa fa-pencil"></i> Revise Latest Quotation
                                </a>
                            <?php else: ?>
                                <a href="<?php echo page_url; ?>ServiceLeads/create_quotation/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-success btn-sm pull-right">
                                    <i class="fa fa-plus"></i> Create Quotation
                                </a>
                            <?php endif; ?>
                        </div>
                        <form id="progressForm" method="post" action="<?php echo page_url; ?>ServiceLeads/update_opportunity_progress/<?php echo $opportunity->opportunity_id; ?>">
                            <div class="form-group">
                                <label>Current Status</label>
                                <div class="p-10 bg-light rounded"><b><?php echo htmlspecialchars($opportunity->current_stage_name); ?></b></div>
                            </div>
                            <?php if (!empty($next_stages)): ?>
                                <div class="form-group">
                                    <label>Next Stage <span class="text-danger">*</span></label>
                                    <select class="form-control" name="new_stage_id" id="new_stage_select" required>
                                        <option value="">Select Next Step</option>
                                        <?php foreach ($next_stages as $stage): ?>
                                        <option value="<?php echo $stage['lead_id']; ?>"><?php echo htmlspecialchars($stage['lead_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Remarks <span class="text-danger">*</span></label>
                                    <textarea rows="3" name="remarks" class="form-control" required placeholder="Enter progress details..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Next Follow-up <span class="text-danger">*</span></label>
                                    <input class="form-control" name="followup_date" type="date" required value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                </div>
                                <button type="submit" class="btn btn-block btn-primary" style="background-color:<?php echo $company_info->colorcode ?? '#4a90e2'; ?>;">Update Status</button>
                            <?php else: ?>
                                <div class="alert alert-info m-b-0">No next stage configured for this opportunity.</div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <?php if ((int) $opportunity->current_stage_id >= 7 || !empty($service_pi)): ?>
                    <div class="card-box">
                        <div class="clearfix m-b-15">
                            <h4 class="m-t-0 header-title pull-left"><b>Proforma Invoice</b></h4>
                            <span class="label <?php echo !empty($service_pi) ? 'label-success' : 'label-warning'; ?> pull-right">
                                <?php echo !empty($service_pi) ? 'Created' : 'Pending'; ?>
                            </span>
                        </div>

                        <?php if (!empty($service_pi)): ?>
                            <p><b>PI No:</b> <?php echo htmlspecialchars($service_pi->pi_no, ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><b>Date:</b> <?php echo !empty($service_pi->pi_date) ? date('d M Y', strtotime($service_pi->pi_date)) : '-'; ?></p>
                            <p><b>Value:</b> <?php echo $pi_curr_symbol . ' ' . number_format((float) $service_pi->grand_total, 2); ?></p>
                            <div class="m-t-15">
                                <a href="<?php echo page_url; ?>ServiceLeads/create_pi/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-pencil"></i> Edit PI
                                </a>
                                <a href="<?php echo page_url; ?>ServiceLeads/view_pi_pdf/<?php echo (int) $service_pi->id; ?>" target="_blank" class="btn btn-danger btn-sm">
                                    <i class="fa fa-file-pdf-o"></i> View PDF
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Create PI for this won order.</p>
                            <a href="<?php echo page_url; ?>ServiceLeads/create_pi/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Create PI
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_create_service_payment && (int) $opportunity->current_stage_id >= 7): ?>
                    <div class="card-box">
                        <div class="clearfix m-b-15">
                            <h4 class="m-t-0 header-title pull-left"><b>Payment Requests</b></h4>
                            <span class="label label-info pull-right">Won Order Flow</span>
                        </div>
                        <p class="text-muted">Raise service payment requests for this won service order and route them to the Service HOD.</p>
                        <div class="m-t-15">
                            <a href="<?php echo page_url; ?>ServiceLeads/service_payment_request_form?basis=ORDER&opportunity_id=<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-credit-card"></i> New Payment Request
                            </a>
                            <a href="<?php echo page_url; ?>ServiceLeads/service_payment_requests" class="btn btn-default btn-sm">
                                <i class="fa fa-list-alt"></i> View Requests
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-7">
                    <div class="card-box">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
                            <li><a href="#history" data-toggle="tab">Timeline</a></li>
                            <li><a href="#quotations" data-toggle="tab">Quotes (<?php echo count($quotations); ?>)</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="overview">
                                <div class="m-t-20">
                                    <p><b>Customer:</b> <?php echo htmlspecialchars($opportunity->company_name); ?></p>
                                    <p><b>Subject:</b> <?php echo htmlspecialchars($opportunity->remarks); ?></p>
                                    <p><b>Marketing:</b> <?php echo htmlspecialchars($opportunity->marketing_person_name); ?></p>
                                    <p><b>Type:</b> <?php echo ($opportunity->op_type == 1) ? 'Domestic' : 'International'; ?></p>
                                </div>
                            </div>

                            <div class="tab-pane" id="history">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Activity History</b></h4>
                                <ul class="history-timeline">
                                    <?php if (!empty($history)): foreach ($history as $item): ?>
                                    <li>
                                        <p class="m-b-5"><b><?php echo htmlspecialchars($item->stage_name); ?></b></p>
                                        <p class="text-muted small">"<?php echo nl2br(htmlspecialchars($item->remarks)); ?>"</p>
                                        <small class="text-muted">
                                            By: <?php echo htmlspecialchars($item->added_by_name); ?> | <?php echo date('d M Y H:i', strtotime($item->added_on)); ?>
                                            <?php if($item->next_follow_date): ?> | Follow-up: <b><?php echo date('d M Y', strtotime($item->next_follow_date)); ?></b><?php endif; ?>
                                        </small>
                                    </li>
                                    <?php endforeach; else: ?>
                                    <li><p class="text-muted">No history recorded yet.</p></li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                            <div class="tab-pane" id="quotations">
                                <table class="table m-t-20 table-hover">
                                    <thead><tr><th>Quote No</th><th class="text-right">Amount</th><th>Action</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($quotations as $quote): ?>
                                        <tr>
                                            <td><b><?php echo htmlspecialchars($quote->quotation_no); ?></b></td>
                                            <td class="text-right"><?php echo $curr_symbol.' '.number_format($quote->grand_total, 2); ?></td>
                                            <td>
                                                <a href="<?php echo page_url; ?>ServiceLeads/view_quotation_pdf/<?php echo $quote->id; ?>" target="_blank" class="text-danger"><i class="fa fa-file-pdf-o"></i> View PDF</a>
                                                <?php if ((int) $quote->id === (int) $quotations[0]->id): ?>
                                                    <span class="text-muted"> | </span>
                                                    <a href="<?php echo page_url; ?>ServiceLeads/revise_quotation/<?php echo (int) $opportunity->opportunity_id; ?>" class="text-warning"><i class="fa fa-pencil"></i> Revise</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($quotations)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                No quotations created yet.
                                                <br>
                                                <a href="<?php echo page_url; ?>ServiceLeads/create_quotation/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-success btn-xs m-t-10">
                                                    <i class="fa fa-plus"></i> Create Quotation
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="approvalModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="approvalForm">
                    <input type="hidden" name="opportunity_id" value="<?php echo $opportunity->opportunity_id; ?>">
                    <input type="hidden" name="approval_status" id="modal_status">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalTitle">Approval Decision</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label id="remarksLabel">Reason / Remarks</label>
                            <textarea name="approval_remarks" class="form-control" rows="4" required placeholder="Explain why this is being approved or rejected..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="modalSubmitBtn" class="btn">Confirm Decision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // --- 1. APPROVAL LOGIC ---
        $('.approval-btn').click(function() {
            var status = $(this).data('status');
            $('#modal_status').val(status);
            $('#modalTitle').text('Quotation ' + status);
            $('#remarksLabel').text(status == 'Rejected' ? 'Reason for Rejection *' : 'Approval Remarks *');
            $('#modalSubmitBtn').addClass(status == 'Approved' ? 'btn-success' : 'btn-danger').removeClass(status == 'Approved' ? 'btn-danger' : 'btn-success');
            $('#approvalModal').modal('show');
        });

        $('#approvalForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?php echo page_url; ?>ServiceLeads/update_quotation_status",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (!res || res.status !== 'success') {
                        Swal.fire('Unable To Update', (res && res.message) ? res.message : 'The quotation status could not be updated.', 'error');
                        return;
                    }
                    $('#approvalModal').modal('hide');
                    Swal.fire('Decision Recorded', res.message, 'success').then(() => location.reload());
                },
                error: function(xhr) {
                    var response = xhr.responseJSON || {};
                    Swal.fire(
                        'Unable To Update',
                        response.message || 'No saved quotation record was found for this opportunity.',
                        'error'
                    );
                }
            });
        });

        // --- 2. STAGE CHANGE REDIRECTIONS ---
        $('#new_stage_select').on('change', function() {
            var stageId = $(this).val();
            var opportunityId = <?php echo $opportunity->opportunity_id; ?>;
            var createPiStageId = <?php echo (int) ($create_pi_stage_id ?? 0); ?>;
            
            if (stageId == '2') { window.location.href = "<?php echo page_url; ?>ServiceLeads/create_quotation/" + opportunityId; }
            if (stageId == '4') { window.location.href = "<?php echo page_url; ?>ServiceLeads/revise_quotation/" + opportunityId; }
            if (stageId == '6') { window.location.href = "<?php echo page_url; ?>ServiceLeads/po_received/" + opportunityId; }
            if (createPiStageId && stageId == String(createPiStageId)) { window.location.href = "<?php echo page_url; ?>ServiceLeads/create_pi/" + opportunityId; }
        });

        // --- 3. STANDARD PROGRESS UPDATE ---
        $('#progressForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    Swal.fire('Timeline Updated', res.message, 'success').then(() => location.reload());
                }
            });
        });
    });
    </script>
</body>
</html>
