<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Engineer Visits</title>

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
        body { font-family: 'Poppins', sans-serif; background: #f4f7fb; }
        .wrapper { padding-top: 80px; }
        .card-box { border-radius: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); padding: 24px; margin-bottom: 20px; background: #fff; border: 1px solid #edf2f7; }
        .widget-box { border-right: 1px solid #eef2f7; padding: 10px 0; transition: 0.25s; }
        .widget-box:last-child { border-right: none; }
        .widget-box h3 { font-weight: 700; margin: 5px 0; font-size: 24px; }
        .widget-box p { color: #64748b; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; margin-bottom: 0; font-weight: 600; }
        .page-title { font-weight: 700; }
        .filter-pills { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .filter-pills .btn { border-radius: 999px; padding: 8px 16px; font-weight: 600; }
        .table thead th { background: #f8fafc; color: #334155; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; }
        .table tbody td { vertical-align: middle; padding: 16px 10px; }
        .visit-ref { font-weight: 700; color: #0f172a; }
        .visit-subtext { color: #64748b; font-size: 12px; line-height: 1.6; }
        .status-pill { display: inline-block; border-radius: 999px; padding: 6px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-on-site { background: #fef3c7; color: #b45309; }
        .status-completed { background: #dcfce7; color: #15803d; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
        .meta-chip { display: inline-block; padding: 5px 10px; border-radius: 999px; background: #f8fafc; color: #475569; font-size: 11px; font-weight: 600; margin-right: 6px; margin-bottom: 6px; }
        .doc-count, .mom-count { font-weight: 700; font-size: 15px; color: #0f172a; }
        .doc-note, .mom-note { color: #64748b; font-size: 12px; }
        .btn-group .btn { border-radius: 6px !important; }
        .dataTables_filter input { width: 240px !important; }
    </style>
</head>
<body>
    <?php
    $payment_permissions = isset($payment_permissions) && is_array($payment_permissions) ? $payment_permissions : array();
    $can_create_service_payment = !empty($payment_permissions['can_create']);
    ?>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:8px;">
                    <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:8px;">
                    <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card-box">
                <div class="row text-center">
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list" style="text-decoration:none;">
                            <p>Total Visits</p>
                            <h3 class="text-dark"><?php echo (int) $visit_kpi['total']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list/scheduled" style="text-decoration:none;">
                            <p>Scheduled</p>
                            <h3 class="text-primary"><?php echo (int) $visit_kpi['scheduled']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list/on-site" style="text-decoration:none;">
                            <p>On-Site</p>
                            <h3 class="text-warning"><?php echo (int) $visit_kpi['on_site']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list/completed" style="text-decoration:none;">
                            <p>Completed</p>
                            <h3 class="text-success"><?php echo (int) $visit_kpi['completed']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list/cancelled" style="text-decoration:none;">
                            <p>Cancelled</p>
                            <h3 class="text-danger"><?php echo (int) $visit_kpi['cancelled']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list/completed" style="text-decoration:none;">
                            <p>Pending Signed Docs</p>
                            <h3 class="text-info"><?php echo (int) $visit_kpi['pending_docs']; ?></h3>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row m-b-15">
                <div class="col-sm-8">
                    <h4 class="page-title">Engineer Visit MOM List: <span class="text-primary"><?php echo $active_status_label; ?></span></h4>
                    <div class="filter-pills">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list" class="btn <?php echo $active_status_slug === 'all' ? 'btn-primary' : 'btn-default'; ?>">All Visits</a>
                        <?php foreach ($status_map as $slug => $label): ?>
                            <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list/<?php echo $slug; ?>" class="btn <?php echo $active_status_slug === $slug ? 'btn-primary' : 'btn-default'; ?>">
                                <?php echo $label; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-sm-4 text-right">
                    <a href="<?php echo page_url; ?>ServiceLeads/engineer_scheduler" class="btn btn-default btn-sm waves-effect waves-light">
                        <i class="fa fa-calendar"></i> Scheduler
                    </a>
                </div>
            </div>

            <div class="card-box">
                <div class="table-responsive">
                    <table id="visitTable" class="table table-hover m-0">
                        <thead>
                            <tr>
                                <th>Visit Ref</th>
                                <th>Customer</th>
                                <th>Engineer</th>
                                <th>Visit Plan</th>
                                <th>Status</th>
                                <th>Daily MOM</th>
                                <th>Signed Docs</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visits as $visit): ?>
                                <tr>
                                    <td>
                                        <div class="visit-ref"><?php echo htmlspecialchars((string) $visit->op_no, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="visit-subtext">Visit #<?php echo (int) $visit->visit_id; ?></div>
                                        <div class="visit-subtext">
                                            <?php
                                            if (!empty($visit->op_date)) {
                                                echo ($visit->reference_type === 'Order' ? 'Order: ' : 'DF released: ') . date('d M, Y', strtotime($visit->op_date));
                                            } else {
                                                echo $visit->reference_type === 'Other DF' ? 'External DF' : 'Reference date not available';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="visit-ref" style="font-size:14px;"><?php echo htmlspecialchars((string) $visit->customer_name, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="visit-subtext"><?php echo $visit->customer_contact_name !== '' ? htmlspecialchars((string) $visit->customer_contact_name, ENT_QUOTES, 'UTF-8') : 'Contact not available'; ?></div>
                                        <div class="visit-subtext"><?php echo $visit->customer_contact_no !== '' ? htmlspecialchars((string) $visit->customer_contact_no, ENT_QUOTES, 'UTF-8') : 'Phone not available'; ?></div>
                                    </td>
                                    <td>
                                        <div class="visit-ref" style="font-size:14px;"><?php echo $visit->engineer_full_name; ?></div>
                                        <div class="visit-subtext"><?php echo $visit->visit_type; ?></div>
                                    </td>
                                    <td>
                                        <span class="meta-chip"><i class="fa fa-calendar-check-o"></i> <?php echo date('d M, Y', strtotime($visit->start_date)); ?></span>
                                        <span class="meta-chip"><i class="fa fa-calendar"></i> <?php echo date('d M, Y', strtotime($visit->end_date)); ?></span>
                                        <div class="visit-subtext"><?php echo $visit->duration_label; ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill status-<?php echo $visit->status_slug; ?>"><?php echo $visit->visit_status; ?></span>
                                        <?php if (!empty($visit->completed_on)): ?>
                                            <div class="visit-subtext m-t-5">Closed on <?php echo date('d M, Y h:i A', strtotime($visit->completed_on)); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="mom-count"><?php echo (int) $visit->mom_count; ?> Update<?php echo (int) $visit->mom_count === 1 ? '' : 's'; ?></div>
                                        <div class="mom-note">
                                            <?php if (!empty($visit->latest_work_date)): ?>
                                                Last entry: <?php echo date('d M, Y', strtotime($visit->latest_work_date)); ?>
                                            <?php else: ?>
                                                No MOM uploaded yet
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="doc-count"><?php echo (int) $visit->document_count; ?> File<?php echo (int) $visit->document_count === 1 ? '' : 's'; ?></div>
                                        <div class="doc-note">
                                            <?php echo (int) $visit->document_count > 0 ? 'Signed docs available' : 'Pending signed documents'; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_detail/<?php echo (int) $visit->visit_id; ?>" class="btn btn-xs btn-primary" title="Open Visit">
                                                <i class="fa fa-folder-open"></i>
                                            </a>
                                            <?php if ($can_create_service_payment && (int) $visit->opportunity_id > 0): ?>
                                                <a href="<?php echo page_url; ?>ServiceLeads/service_payment_request_form?basis=VISIT&opportunity_id=<?php echo (int) $visit->opportunity_id; ?>&visit_id=<?php echo (int) $visit->visit_id; ?>" class="btn btn-xs btn-warning" title="Request Payment">
                                                    <i class="fa fa-credit-card"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo page_url; ?>ServiceLeads/view_visit_mom_pdf/<?php echo (int) $visit->visit_id; ?>" target="_blank" class="btn btn-xs btn-danger" title="View PDF">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#visitTable').DataTable({
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    search: 'Quick Search:',
                    lengthMenu: 'Show _MENU_ records',
                    info: 'Displaying _START_ to _END_ of _TOTAL_ visits'
                }
            });

            $('.dataTables_filter input').addClass('form-control input-sm');
        });
    </script>
</body>
</html>
