<?php
defined('BASEPATH') or exit('No direct script access allowed');

$status_map = isset($status_map) && is_array($status_map) ? $status_map : [];
$active_status_slug = isset($active_status_slug) ? $active_status_slug : 'all';
$kpi = isset($kpi) && is_array($kpi) ? $kpi : [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'requested_value' => 0,
    'approved_value' => 0,
];
$payment_permissions = isset($payment_permissions) && is_array($payment_permissions) ? $payment_permissions : array();
$can_create_service_payment = !empty($payment_permissions['can_create']);
$can_approve_service_payment = !empty($payment_permissions['can_approve']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; <?php echo htmlspecialchars($page_title); ?></title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --list-primary: #0f766e;
            --list-primary-dark: #115e59;
            --list-ink: #17324d;
            --list-muted: #64748b;
            --list-bg: #eef5fb;
            --list-card: #ffffff;
            --list-border: #dbe5f0;
            --list-soft-blue: #eff6ff;
            --list-soft-green: #ecfdf5;
            --list-soft-amber: #fffbeb;
            --list-soft-rose: #fff1f2;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.14), transparent 24%),
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.10), transparent 26%),
                var(--list-bg);
            color: var(--list-ink);
        }

        .wrapper {
            padding-top: 74px;
            padding-bottom: 16px;
        }

        .payment-card {
            background: var(--list-card);
            border-radius: 14px;
            border: 1px solid rgba(219, 229, 240, 0.95);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            padding: 16px;
            margin-bottom: 14px;
        }

        .page-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .toolbar-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--list-primary);
        }

        .toolbar-title {
            margin: 6px 0 0;
            font-size: 24px;
            line-height: 1.15;
            font-weight: 700;
            color: var(--list-ink);
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar-actions .btn {
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .kpi-box {
            border-radius: 12px;
            border: 1px solid rgba(219, 229, 240, 0.96);
            background: rgba(255, 255, 255, 0.96);
            padding: 12px;
        }

        .kpi-box small {
            display: block;
            color: var(--list-muted);
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-size: 9px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .kpi-box strong {
            display: block;
            font-size: 24px;
            line-height: 1;
            color: var(--list-ink);
        }

        .register-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .register-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 0;
        }

        .filter-pills .btn {
            border-radius: 999px;
            padding: 7px 14px;
            font-weight: 600;
        }

        .table thead th {
            background: #f8fafc;
            color: #334155;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #edf2f7;
        }

        .table tbody td {
            vertical-align: top;
            padding: 12px 8px;
        }

        .request-code {
            font-weight: 700;
            color: var(--list-ink);
        }

        .subtext {
            color: var(--list-muted);
            font-size: 11px;
            line-height: 1.55;
        }

        .text-limit {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        .text-limit-3 {
            -webkit-line-clamp: 3;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .status-pending-hod-approval {
            background: var(--list-soft-amber);
            color: #b45309;
        }

        .status-approved {
            background: var(--list-soft-green);
            color: #15803d;
        }

        .status-rejected {
            background: var(--list-soft-rose);
            color: #be123c;
        }

        .mini-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: 600;
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .btn-group-flat .btn {
            border-radius: 8px !important;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .empty-state {
            border-radius: 12px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 16px;
            color: var(--list-muted);
            text-align: center;
            line-height: 1.6;
        }

        .modal .form-control {
            border-radius: 10px;
            min-height: 42px;
        }

        .dataTables_filter input {
            width: 220px !important;
        }

        @media (max-width: 991px) {
            .wrapper {
                padding-top: 70px;
            }

            .payment-card {
                padding: 16px;
            }

            .toolbar-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <div class="payment-card page-toolbar">
                <div>
                    <div class="toolbar-eyebrow">
                        <i class="fa fa-credit-card"></i>
                        <span><?php echo !empty($is_approval_view) ? 'Service HOD Approval Queue' : 'Service Payment Tracking'; ?></span>
                    </div>
                    <h2 class="toolbar-title"><?php echo htmlspecialchars($page_title); ?></h2>
                </div>
                <div class="toolbar-actions">
                    <?php if ($can_create_service_payment): ?>
                        <a href="<?php echo page_url; ?>ServiceLeads/service_payment_request_form" class="btn btn-primary">
                            <i class="fa fa-plus"></i> New Request
                        </a>
                    <?php endif; ?>
                    <?php if ($can_approve_service_payment): ?>
                        <a href="<?php echo page_url; ?>ServiceLeads/service_payment_approvals" class="btn btn-default">
                            <i class="fa fa-check-square-o"></i> Approval Queue
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="kpi-grid">
                <div class="kpi-box">
                    <small>Total Requests</small>
                    <strong><?php echo (int) $kpi['total']; ?></strong>
                </div>
                <div class="kpi-box">
                    <small>Pending HOD</small>
                    <strong><?php echo (int) $kpi['pending']; ?></strong>
                </div>
                <div class="kpi-box">
                    <small>Approved</small>
                    <strong><?php echo (int) $kpi['approved']; ?></strong>
                </div>
                <div class="kpi-box">
                    <small>Rejected</small>
                    <strong><?php echo (int) $kpi['rejected']; ?></strong>
                </div>
                <div class="kpi-box">
                    <small>Requested Value</small>
                    <strong>₹ <?php echo number_format((float) $kpi['requested_value'], 0); ?></strong>
                </div>
                <div class="kpi-box">
                    <small>Approved Value</small>
                    <strong>₹ <?php echo number_format((float) $kpi['approved_value'], 0); ?></strong>
                </div>
            </div>

            <div class="payment-card">
                <div class="register-header">
                    <h4>Request Register</h4>
                    <div class="filter-pills">
                        <?php foreach ($status_map as $slug => $label): ?>
                            <a href="<?php echo page_url; ?>ServiceLeads/<?php echo !empty($is_approval_view) ? 'service_payment_approvals' : 'service_payment_requests'; ?>?status=<?php echo urlencode($slug); ?>" class="btn <?php echo $active_status_slug === $slug ? 'btn-primary' : 'btn-default'; ?>">
                                <?php echo htmlspecialchars($label); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="payment-card">
                <?php if (empty($requests)): ?>
                    <div class="empty-state">
                        No payment requests were found for the selected filter.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table id="servicePaymentTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Request</th>
                                    <th>Order / Visit</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Attachment</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td>
                                            <div class="request-code"><?php echo htmlspecialchars($request->request_code); ?></div>
                                            <div class="subtext">Raised on <?php echo date('d M Y', strtotime($request->request_date)); ?></div>
                                            <div class="subtext">By <?php echo htmlspecialchars($request->created_by_name); ?></div>
                                            <?php if (!empty($request->parent_request_code)): ?>
                                                <div class="subtext">Linked to <?php echo htmlspecialchars($request->parent_request_code); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="request-code" style="font-size:14px;"><?php echo htmlspecialchars($request->customer_name); ?></div>
                                            <div class="subtext">
                                                <?php if (!empty($request->customer_company_name) && strcasecmp(trim((string) $request->customer_company_name), trim((string) $request->customer_name)) !== 0): ?>
                                                    <?php echo htmlspecialchars($request->customer_company_name); ?> |
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($request->op_no); ?> | PO <?php echo htmlspecialchars($request->po_number); ?>
                                            </div>
                                            <?php if ((int) $request->visit_id > 0): ?>
                                                <div class="mini-chip"><i class="fa fa-map-marker"></i> Visit #<?php echo (int) $request->visit_id; ?></div>
                                                <?php if (!empty($request->engineer_name)): ?>
                                                    <div class="mini-chip"><i class="fa fa-user-o"></i> <?php echo htmlspecialchars($request->engineer_name); ?></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (!empty($request->service_from_date) || !empty($request->service_to_date)): ?>
                                                <div class="subtext">
                                                    <?php echo !empty($request->service_from_date) ? date('d M Y', strtotime($request->service_from_date)) : '-'; ?>
                                                    to
                                                    <?php echo !empty($request->service_to_date) ? date('d M Y', strtotime($request->service_to_date)) : '-'; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="mini-chip"><i class="fa fa-sitemap"></i> <?php echo htmlspecialchars($request->request_basis); ?></div>
                                            <div class="request-code" style="font-size:14px; margin-top:8px;"><?php echo htmlspecialchars($request->request_type); ?></div>
                                            <?php if (!empty($request->request_title)): ?>
                                                <div class="subtext"><?php echo htmlspecialchars($request->request_title); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="request-code">₹ <?php echo number_format((float) $request->amount, 2); ?></div>
                                            <div class="subtext">PO Value: ₹ <?php echo number_format((float) $request->po_amount, 2); ?></div>
                                        </td>
                                        <td>
                                            <div class="subtext text-limit text-limit-3" title="<?php echo htmlspecialchars($request->purpose); ?>"><?php echo nl2br(htmlspecialchars($request->purpose)); ?></div>
                                            <?php if (!empty($request->extension_reason)): ?>
                                                <div class="subtext text-limit text-limit-3" style="margin-top:8px;" title="<?php echo htmlspecialchars($request->extension_reason); ?>">
                                                    <strong>Linked Request Note:</strong><br>
                                                    <?php echo nl2br(htmlspecialchars($request->extension_reason)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="status-pill status-<?php echo htmlspecialchars($request->status_slug); ?>">
                                                <?php echo htmlspecialchars($request->status); ?>
                                            </div>
                                            <?php if (!empty($request->hod_name)): ?>
                                                <div class="subtext" style="margin-top:8px;">HOD: <?php echo htmlspecialchars($request->hod_name); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($request->hod_action_on)): ?>
                                                <div class="subtext"><?php echo date('d M Y h:i A', strtotime($request->hod_action_on)); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($request->hod_remarks)): ?>
                                                <div class="subtext text-limit text-limit-3" style="margin-top:8px;" title="<?php echo htmlspecialchars($request->hod_remarks); ?>">
                                                    <strong>Remarks:</strong><br>
                                                    <?php echo nl2br(htmlspecialchars($request->hod_remarks)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($request->attachment)): ?>
                                                <a href="<?php echo base_url('uploads/service_payment_requests/' . rawurlencode($request->attachment)); ?>" target="_blank" class="btn btn-default btn-xs">
                                                    <i class="fa fa-paperclip"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="subtext">No file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group-flat">
                                                <a href="<?php echo page_url; ?>ServiceLeads/print_service_payment_request_pdf/<?php echo (int) $request->request_id; ?>" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fa fa-print"></i> Print PDF
                                                </a>
                                                <?php if ($can_create_service_payment): ?>
                                                    <a href="<?php echo page_url; ?>ServiceLeads/service_payment_request_form?opportunity_id=<?php echo (int) $request->opportunity_id; ?>&visit_id=<?php echo (int) $request->visit_id; ?>&parent_request_id=<?php echo (int) $request->request_id; ?>&basis=<?php echo urlencode((string) $request->request_basis); ?>" class="btn btn-warning btn-xs">
                                                        <i class="fa fa-plus-circle"></i> Linked Request
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($can_approve_service_payment && !empty($is_approval_view) && trim((string) $request->status) === 'Pending HOD Approval'): ?>
                                                    <button type="button" class="btn btn-success btn-xs approval-btn" data-id="<?php echo (int) $request->request_id; ?>" data-action="approved">
                                                        <i class="fa fa-check"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs approval-btn" data-id="<?php echo (int) $request->request_id; ?>" data-action="rejected">
                                                        <i class="fa fa-times"></i> Reject
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="approvalForm">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Service Payment Decision</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="request_id" id="approval_request_id">
                        <input type="hidden" name="action" id="approval_action">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" name="remarks" id="approval_remarks" rows="4" placeholder="Add approval note or rejection reason"></textarea>
                        </div>
                        <p class="subtext" id="approvalHelpText" style="margin-bottom:0;"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="approvalSubmitBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#servicePaymentTable').length) {
                $('#servicePaymentTable').DataTable({
                    pageLength: 25,
                    order: [[0, 'desc']],
                    language: {
                        search: 'Quick Search:',
                        lengthMenu: 'Show _MENU_ records',
                        info: 'Displaying _START_ to _END_ of _TOTAL_ requests'
                    }
                });

                $('.dataTables_filter input').addClass('form-control input-sm');
            }

            $('.approval-btn').on('click', function() {
                var requestId = $(this).data('id');
                var action = $(this).data('action');
                var isReject = action === 'rejected';

                $('#approval_request_id').val(requestId);
                $('#approval_action').val(action);
                $('#approval_remarks').val('');
                $('#approvalHelpText').text(isReject ? 'Rejection remarks are required so the requester knows what to fix.' : 'Approval remarks are optional but helpful for audit clarity.');
                $('#approvalSubmitBtn')
                    .removeClass('btn-primary btn-danger btn-success')
                    .addClass(isReject ? 'btn-danger' : 'btn-success')
                    .text(isReject ? 'Reject Request' : 'Approve Request');

                $('#approvalModal').modal('show');
            });

            $('#approvalForm').on('submit', function(e) {
                e.preventDefault();

                var action = $('#approval_action').val();
                var remarks = $.trim($('#approval_remarks').val());
                if (action === 'rejected' && remarks === '') {
                    alert('Please enter rejection remarks.');
                    return;
                }

                $.ajax({
                    url: '<?php echo page_url; ?>ServiceLeads/update_service_payment_request_status',
                    method: 'POST',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#approvalModal').modal('hide');
                            location.reload();
                            return;
                        }

                        alert(res.message || 'The request could not be updated.');
                    },
                    error: function() {
                        alert('The request could not be updated right now.');
                    }
                });
            });
        });
    </script>
</body>
</html>
