<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$theme_color = '#4872b8';
$logo_query = $this->db->select('colorcode')->from('company_information')->get()->row();
if ($logo_query && !empty($logo_query->colorcode)) {
    $theme_color = $logo_query->colorcode;
}

$fy_start = (int) substr($financial_year, 0, 4);
$months = array();
for ($offset = 0; $offset < 12; $offset++) {
    $timestamp = mktime(0, 0, 0, 4 + $offset, 1, $fy_start);
    $months[date('Y-m', $timestamp)] = date('M Y', $timestamp);
}

$summary_total = $summary ? (int) $summary->total : 0;
$summary_dispatched = $summary ? (int) $summary->dispatched : 0;
$summary_at_risk = $summary ? (int) $summary->at_risk : 0;
$summary_overdue = $summary ? (int) $summary->overdue : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> DF Dispatch Morning Meeting</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <style>
        :root { --dispatch-primary: <?php echo $theme_color; ?>; --dispatch-navy: #1f3f7a; }
        .dispatch-shell { margin-top: 20px; }
        .dispatch-hero {
            background: linear-gradient(135deg, var(--dispatch-primary), var(--dispatch-navy));
            color: #fff; border-radius: 14px; padding: 22px; margin-bottom: 18px;
            box-shadow: 0 10px 25px rgba(31, 63, 122, .18);
        }
        .dispatch-hero h2 { color: #fff; margin: 0 0 4px; font-size: 23px; font-weight: 700; }
        .dispatch-hero p { margin: 0; opacity: .9; }
        .hero-actions { text-align: right; }
        .hero-actions .btn { border-radius: 22px; font-weight: 600; margin-left: 6px; }
        .kpi-row { margin-bottom: 18px; }
        .kpi-card {
            background: #fff; border-radius: 12px; padding: 17px 18px; min-height: 104px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .08); border-left: 4px solid var(--dispatch-primary);
        }
        .kpi-card.success { border-color: #16a34a; }
        .kpi-card.warning { border-color: #f59e0b; }
        .kpi-card.danger { border-color: #dc2626; }
        .kpi-label { text-transform: uppercase; font-size: 11px; color: #64748b; letter-spacing: .6px; font-weight: 700; }
        .kpi-value { font-size: 30px; line-height: 1.2; color: #172554; font-weight: 800; }
        .kpi-note { color: #94a3b8; font-size: 11px; }
        .month-strip { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 12px; }
        .month-chip {
            display: inline-block; min-width: 88px; padding: 9px 10px; border-radius: 10px;
            background: #fff; border: 1px solid #dbe3ef; color: #334155; text-align: center;
            font-size: 12px; font-weight: 700;
        }
        .month-chip:hover, .month-chip.active { background: var(--dispatch-primary); color: #fff; border-color: var(--dispatch-primary); }
        .month-chip small { display: block; opacity: .75; margin-top: 2px; }
        .filter-card, .dispatch-table-card {
            background: #fff; border-radius: 12px; padding: 16px; box-shadow: 0 4px 16px rgba(15, 23, 42, .08);
        }
        .filter-card { margin-bottom: 16px; }
        .filter-card .form-control { border-radius: 8px; }
        .dispatch-table-card { border-top: 4px solid var(--dispatch-primary); }
        table.dispatch-table { width: 100% !important; white-space: nowrap; font-size: 12px; }
        table.dispatch-table thead th {
            background: var(--dispatch-primary); color: #fff; text-align: center;
            vertical-align: middle; padding: 10px 8px; border-color: #d9e2f2;
        }
        table.dispatch-table tbody td { vertical-align: middle; border-color: #e7edf5; padding: 8px; }
        .df-number { color: var(--dispatch-primary); font-weight: 800; }
        .model-cell { min-width: 230px; max-width: 320px; white-space: normal; font-weight: 600; color: #27364d; }
        .status-badge { display: inline-block; padding: 5px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .status-planned { background: #e0e7ff; color: #3730a3; }
        .status-in-progress { background: #dbeafe; color: #1d4ed8; }
        .status-at-risk, .status-overdue { background: #fee2e2; color: #b91c1c; }
        .status-ready { background: #dcfce7; color: #166534; }
        .status-dispatched { background: #d1fae5; color: #065f46; }
        .status-on-hold { background: #fef3c7; color: #92400e; }
        .dependency-pill { border: 0; border-radius: 18px; padding: 5px 10px; font-size: 11px; font-weight: 700; }
        .dependency-pill.open { background: #fff1f2; color: #be123c; }
        .dependency-pill.clear { background: #f1f5f9; color: #475569; }
        .progress { margin-bottom: 0; height: 8px; border-radius: 8px; min-width: 90px; }
        .action-btn { border-radius: 18px; margin: 1px; }
        .modal-content { border-radius: 14px; border: 0; overflow: hidden; }
        .modal-header { background: linear-gradient(135deg, var(--dispatch-primary), var(--dispatch-navy)); color: #fff; }
        .modal-title { color: #fff; font-weight: 700; }
        .dependency-item { border: 1px solid #e2e8f0; border-radius: 10px; padding: 11px; margin-bottom: 9px; }
        .dependency-item.blocked { border-left: 4px solid #dc2626; }
        .dependency-item.closed { opacity: .7; background: #f8fafc; }
        .empty-state { padding: 48px 20px; text-align: center; color: #64748b; }
        .dispatch-loader {
            display: none; position: fixed; z-index: 99999; inset: 0;
            background: rgba(248, 250, 252, .78); align-items: center; justify-content: center;
        }
        .dispatch-loader.active { display: flex; }
        .dispatch-loader-box {
            background: #fff; border-radius: 12px; padding: 18px 24px; color: var(--dispatch-primary);
            box-shadow: 0 12px 35px rgba(15, 23, 42, .2); font-weight: 700;
        }
        @media (max-width: 767px) {
            .hero-actions { text-align: left; margin-top: 14px; }
            .hero-actions .btn { margin: 3px 4px 3px 0; }
        }
    </style>
</head>
<body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<?php $this->load->view('common/info-section.php'); ?>

<div class="wrapper">
    <div class="container-fluid dispatch-shell">
        <div class="dispatch-hero">
            <div class="row">
                <div class="col-md-8">
                    <h2><i class="fa fa-calendar-check-o"></i> DF Dispatch Morning Meeting</h2>
                    <p>Financial Year <?php echo htmlspecialchars($financial_year, ENT_QUOTES, 'UTF-8'); ?> · Month-wise planning, dependency tracking and ownership</p>
                </div>
                <div class="col-md-4 hero-actions">
                    <?php if ($tables_ready): ?>
                        <button class="btn btn-default" id="syncScheduleBtn"><i class="fa fa-refresh"></i> Sync PMS Schedule</button>
                        <button class="btn btn-light" onclick="openPlanModal()"><i class="fa fa-plus"></i> Add DF Plan</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!$tables_ready): ?>
            <div class="alert alert-warning">
                <strong>Database setup required.</strong>
                Run <code>Database/df_dispatch_plan_001.sql</code>, then reload this page.
            </div>
        <?php else: ?>
            <div class="row kpi-row">
                <div class="col-md-3 col-sm-6"><div class="kpi-card"><div class="kpi-label">FY Planned DFs</div><div class="kpi-value"><?php echo $summary_total; ?></div><div class="kpi-note">Across April to March</div></div></div>
                <div class="col-md-3 col-sm-6"><div class="kpi-card success"><div class="kpi-label">Dispatched</div><div class="kpi-value"><?php echo $summary_dispatched; ?></div><div class="kpi-note">Completed dispatch plans</div></div></div>
                <div class="col-md-3 col-sm-6"><div class="kpi-card warning"><div class="kpi-label">At Risk</div><div class="kpi-value"><?php echo $summary_at_risk; ?></div><div class="kpi-note">Needs meeting attention</div></div></div>
                <div class="col-md-3 col-sm-6"><div class="kpi-card danger"><div class="kpi-label">Overdue</div><div class="kpi-value"><?php echo $summary_overdue; ?></div><div class="kpi-note">Past plan date, not dispatched</div></div></div>
            </div>

            <div class="month-strip">
                <a class="month-chip <?php echo $selected_month === 'all' ? 'active' : ''; ?>" href="?fy=<?php echo urlencode($financial_year); ?>&month=all">All<small><?php echo $summary_total; ?> DFs</small></a>
                <?php foreach ($months as $month_key => $month_label): ?>
                    <?php $month_count = isset($summary->months[$month_key]) ? $summary->months[$month_key] : 0; ?>
                    <a class="month-chip <?php echo $selected_month === $month_key ? 'active' : ''; ?>" href="?fy=<?php echo urlencode($financial_year); ?>&month=<?php echo urlencode($month_key); ?>">
                        <?php echo $month_label; ?><small><?php echo $month_count; ?> DFs</small>
                    </a>
                <?php endforeach; ?>
            </div>

            <form class="filter-card" method="get">
                <div class="row">
                    <div class="col-md-3">
                        <label>Financial Year</label>
                        <select name="fy" class="form-control">
                            <?php for ($year = $fy_start - 2; $year <= $fy_start + 2; $year++): ?>
                                <?php $fy_label = $year . '-' . substr((string) ($year + 1), -2); ?>
                                <option value="<?php echo $fy_label; ?>" <?php echo $financial_year === $fy_label ? 'selected' : ''; ?>><?php echo $fy_label; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Month</label>
                        <select name="month" class="form-control">
                            <option value="all" <?php echo $selected_month === 'all' ? 'selected' : ''; ?>>All months</option>
                            <?php foreach ($months as $month_key => $month_label): ?>
                                <option value="<?php echo $month_key; ?>" <?php echo $selected_month === $month_key ? 'selected' : ''; ?>><?php echo $month_label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All statuses</option>
                            <?php foreach (array('Planned', 'In Progress', 'At Risk', 'Ready', 'Dispatched', 'On Hold') as $option): ?>
                                <option value="<?php echo $option; ?>" <?php echo $selected_status === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>DF / Model / Owner</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Search...">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block"><i class="fa fa-filter"></i></button>
                    </div>
                </div>
            </form>

            <div class="dispatch-table-card table-responsive">
                <table id="dispatchTable" class="table table-bordered table-striped dispatch-table">
                    <thead>
                        <tr>
                            <th>Priority</th><th>DF No.</th><th>Planned Dispatch</th><th>Status</th>
                            <th>Model / Product</th><th>Completion</th><th>FAT Date</th><th>Frame Status</th>
                            <th>Design</th><th>Marketing</th><th>Dependencies</th><th>Remarks</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($plans as $plan): ?>
                        <?php
                        $is_overdue = $plan->status !== 'Dispatched' && $plan->planned_dispatch_date < date('Y-m-d');
                        $status_class = $is_overdue ? 'status-overdue' : 'status-' . strtolower(str_replace(' ', '-', $plan->status));
                        $plan_payload = htmlspecialchars(json_encode($plan), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td class="text-center"><?php echo (int) $plan->priority; ?></td>
                            <td class="df-number"><?php echo htmlspecialchars($plan->df_no, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td data-order="<?php echo $plan->planned_dispatch_date; ?>">
                                <?php echo date('d M Y', strtotime($plan->planned_dispatch_date)); ?>
                                <?php if ($is_overdue): ?><br><span class="status-badge status-overdue">Overdue</span><?php endif; ?>
                            </td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($plan->status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td class="model-cell">
                                <?php echo htmlspecialchars($plan->model, ENT_QUOTES, 'UTF-8'); ?>
                                <?php if (!empty($plan->product)): ?><br><small class="text-muted"><?php echo htmlspecialchars($plan->product, ENT_QUOTES, 'UTF-8'); ?><?php echo !empty($plan->automation) ? ' · ' . htmlspecialchars($plan->automation, ENT_QUOTES, 'UTF-8') : ''; ?></small><?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo (int) $plan->completion_percent; ?>%</strong>
                                <div class="progress"><div class="progress-bar" style="width:<?php echo (int) $plan->completion_percent; ?>%; background:var(--dispatch-primary);"></div></div>
                            </td>
                            <td><?php echo !empty($plan->fat_date) ? date('d M Y', strtotime($plan->fat_date)) : '-'; ?></td>
                            <td><?php echo !empty($plan->frame_status) ? htmlspecialchars($plan->frame_status, ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                            <td><?php echo !empty($plan->design_owner) ? htmlspecialchars($plan->design_owner, ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                            <td><?php echo !empty($plan->marketing_owner) ? htmlspecialchars($plan->marketing_owner, ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                            <td class="text-center">
                                <button class="dependency-pill <?php echo (int) $plan->open_dependency_count > 0 ? 'open' : 'clear'; ?>" onclick="openDependencies(<?php echo (int) $plan->id; ?>, '<?php echo htmlspecialchars($plan->df_no, ENT_QUOTES, 'UTF-8'); ?>')">
                                    <?php echo (int) $plan->open_dependency_count; ?> open / <?php echo (int) $plan->dependency_count; ?>
                                </button>
                            </td>
                            <td class="model-cell"><?php echo !empty($plan->remarks) ? nl2br(htmlspecialchars($plan->remarks, ENT_QUOTES, 'UTF-8')) : '-'; ?></td>
                            <td>
                                <button class="btn btn-xs btn-primary action-btn edit-plan" data-plan="<?php echo $plan_payload; ?>"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-xs btn-warning action-btn" onclick="openDependencies(<?php echo (int) $plan->id; ?>, '<?php echo htmlspecialchars($plan->df_no, ENT_QUOTES, 'UTF-8'); ?>')"><i class="fa fa-tasks"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($plans)): ?><div class="empty-state"><i class="fa fa-calendar-o fa-3x"></i><h4>No dispatch plans found</h4><p>Add the first DF plan or change the selected filters.</p></div><?php endif; ?>
            </div>
        <?php endif; ?>
        <?php $this->load->view('common/footer'); ?>
    </div>
</div>

<?php if ($tables_ready): ?>
<div id="planModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <form id="planForm">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">DF Dispatch Plan</h4></div>
            <div class="modal-body">
                <input type="hidden" name="plan_id" id="plan_id">
                <div class="row">
                    <div class="col-md-4 form-group"><label>DF from Master</label><select name="df_id" id="df_id" class="form-control"><option value="">Manual / Basic Machine</option><?php foreach ($available_dfs as $df): ?><option value="<?php echo (int) $df->id; ?>" data-df-no="<?php echo htmlspecialchars($df->df_no, ENT_QUOTES, 'UTF-8'); ?>" data-model="<?php echo htmlspecialchars($df->df_description, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($df->df_no . ' - ' . $df->df_description, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4 form-group"><label>DF No. *</label><input name="df_no" id="df_no" class="form-control" required></div>
                    <div class="col-md-2 form-group"><label>Priority</label><input type="number" min="0" name="priority" id="priority" value="0" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>Completion %</label><input type="number" min="0" max="100" name="completion_percent" id="completion_percent" value="0" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Model *</label><input name="model" id="model" class="form-control" required></div>
                    <div class="col-md-3 form-group"><label>Product</label><input name="product" id="product" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>Automation</label><input name="automation" id="automation" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group"><label>Planned Dispatch *</label><input type="date" name="planned_dispatch_date" id="planned_dispatch_date" class="form-control" required></div>
                    <div class="col-md-3 form-group"><label>FAT Date</label><input type="date" name="fat_date" id="fat_date" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>Status</label><select name="status" id="plan_status" class="form-control"><?php foreach (array('Planned', 'In Progress', 'At Risk', 'Ready', 'Dispatched', 'On Hold') as $option): ?><option><?php echo $option; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3 form-group"><label>Frame Status</label><input name="frame_status" id="frame_status" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Design Owner</label><input name="design_owner" id="design_owner" class="form-control"></div>
                    <div class="col-md-6 form-group"><label>Marketing Owner</label><input name="marketing_owner" id="marketing_owner" class="form-control"></div>
                </div>
                <div class="form-group"><label>Meeting Remarks</label><textarea name="remarks" id="plan_remarks" rows="3" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="savePlanBtn">Save Plan</button></div>
        </form>
    </div></div>
</div>

<div id="dependencyModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Dependencies · <span id="dependencyDf"></span></h4></div>
        <div class="modal-body">
            <div id="dependencyList"></div>
            <hr>
            <h5><i class="fa fa-user-plus"></i> Add / Delegate Dependency</h5>
            <form id="dependencyForm">
                <input type="hidden" name="plan_id" id="dependency_plan_id">
                <div class="row">
                    <div class="col-md-3 form-group"><label>Department</label><select name="department" class="form-control"><?php foreach (array('Design','Section 6','Purchase','BOP','Automation','Electrical','Gear Box','Production','Marketing','Other') as $department): ?><option><?php echo $department; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-5 form-group"><label>Dependency *</label><input name="title" class="form-control" required></div>
                    <div class="col-md-4 form-group"><label>Owner</label><select name="owner_user_id" class="form-control"><option value="0">Unassigned</option><?php foreach ($users as $user): ?><option value="<?php echo (int) $user->user_id; ?>"><?php echo htmlspecialchars(trim($user->first_name . ' ' . $user->last_name), ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group"><label>Due Date *</label><input type="date" name="due_date" class="form-control" required></div>
                    <div class="col-md-3 form-group"><label>Priority</label><select name="priority" class="form-control"><option>Low</option><option selected>Medium</option><option>High</option><option>Critical</option></select></div>
                    <div class="col-md-4 form-group"><label>Remarks</label><input name="remarks" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-warning btn-block">Delegate</button></div>
                </div>
            </form>
        </div>
    </div></div>
</div>
<?php endif; ?>

<div class="dispatch-loader" id="dispatchLoader">
    <div class="dispatch-loader-box"><i class="fa fa-spinner fa-spin"></i> Loading dispatch plans...</div>
</div>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<?php if ($tables_ready): ?>
<script>
var dependencyPlanId = 0;
$(function() {
    $('.month-chip').on('click', function() {
        showDispatchLoader();
    });

    $('.filter-card').on('submit', function() {
        showDispatchLoader();
    });

    $('.filter-card select[name="fy"], .filter-card select[name="month"], .filter-card select[name="status"]').on('change', function() {
        showDispatchLoader();
        $(this).closest('form').submit();
    });

    <?php if (!empty($plans)): ?>
    $('#dispatchTable').DataTable({
        scrollX: true, pageLength: 50, order: [[2, 'asc'], [0, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {extend:'excelHtml5', text:'Export Excel', className:'btn btn-success btn-sm', title:'DF Dispatch Plan FY <?php echo $financial_year; ?>'},
            {extend:'print', text:'Print Meeting Sheet', className:'btn btn-primary btn-sm', title:'DF Dispatch Morning Meeting'}
        ],
        columnDefs: [{targets:[10,12], orderable:false}]
    });
    <?php endif; ?>

    $('#df_id').change(function() {
        var option = $(this).find('option:selected');
        if (option.val()) {
            $('#df_no').val(option.data('df-no'));
            $('#model').val(option.data('model'));
        }
    });

    $('#syncScheduleBtn').click(function() {
        var button = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
        $.post('<?php echo page_url; ?>Df_dispatch_plan/sync_schedule', {
            financial_year: '<?php echo htmlspecialchars($financial_year, ENT_QUOTES, 'UTF-8'); ?>'
        }, function(response) {
            alert(response.message);
            if (response.status) {
                location.reload();
            } else {
                button.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync PMS Schedule');
            }
        }, 'json').fail(function() {
            alert('Unable to sync the PMS schedule.');
            button.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync PMS Schedule');
        });
    });

    $(document).on('click', '.edit-plan', function() {
        openPlanModal($(this).data('plan'));
    });

    $('#planForm').submit(function(event) {
        event.preventDefault();
        var button = $('#savePlanBtn').prop('disabled', true).text('Saving...');
        $.post('<?php echo page_url; ?>Df_dispatch_plan/save_plan', $(this).serialize(), function(response) {
            if (response.status) {
                location.reload();
            } else {
                alert(response.message);
                button.prop('disabled', false).text('Save Plan');
            }
        }, 'json').fail(function() {
            alert('Unable to save. Please try again.');
            button.prop('disabled', false).text('Save Plan');
        });
    });

    $('#dependencyForm').submit(function(event) {
        event.preventDefault();
        $.post('<?php echo page_url; ?>Df_dispatch_plan/add_dependency', $(this).serialize(), function(response) {
            if (response.status) {
                $('#dependencyForm')[0].reset();
                $('#dependency_plan_id').val(dependencyPlanId);
                loadDependencies();
            } else {
                alert(response.message);
            }
        }, 'json');
    });
});

function openPlanModal(plan) {
    $('#planForm')[0].reset();
    $('#plan_id').val('');
    $('#priority').val(0);
    $('#completion_percent').val(0);
    $('#plan_status').val('Planned');
    if (plan) {
        $('#plan_id').val(plan.id);
        $('#df_id').val(plan.df_id || '');
        $('#df_no').val(plan.df_no);
        $('#priority').val(plan.priority);
        $('#model').val(plan.model);
        $('#product').val(plan.product);
        $('#automation').val(plan.automation);
        $('#planned_dispatch_date').val(plan.planned_dispatch_date);
        $('#fat_date').val(plan.fat_date);
        $('#completion_percent').val(plan.completion_percent);
        $('#frame_status').val(plan.frame_status);
        $('#design_owner').val(plan.design_owner);
        $('#marketing_owner').val(plan.marketing_owner);
        $('#plan_status').val(plan.status);
        $('#plan_remarks').val(plan.remarks);
    }
    $('#planModal').modal('show');
}

function openDependencies(planId, dfNo) {
    dependencyPlanId = planId;
    $('#dependency_plan_id').val(planId);
    $('#dependencyDf').text(dfNo);
    $('#dependencyModal').modal('show');
    loadDependencies();
}

function loadDependencies() {
    $('#dependencyList').html('<p class="text-muted">Loading dependencies...</p>');
    $.getJSON('<?php echo page_url; ?>Df_dispatch_plan/dependencies/' + dependencyPlanId, function(response) {
        if (!response.status) {
            $('#dependencyList').html('<div class="alert alert-danger">' + escapeHtml(response.message) + '</div>');
            return;
        }
        if (!response.dependencies.length) {
            $('#dependencyList').html('<div class="alert alert-info">No dependencies recorded for this DF.</div>');
            return;
        }
        var html = '';
        $.each(response.dependencies, function(_, item) {
            var owner = $.trim((item.owner_name || '') + ' ' + (item.owner_last_name || '')) || 'Unassigned';
            html += '<div class="dependency-item ' + (item.status === 'Blocked' ? 'blocked' : '') + ' ' + (item.status === 'Closed' ? 'closed' : '') + '">';
            html += '<div class="row"><div class="col-md-8"><strong>' + escapeHtml(item.title) + '</strong><br>';
            html += '<small>' + escapeHtml(item.department) + ' · Owner: ' + escapeHtml(owner) + ' · Due: ' + escapeHtml(item.due_date) + ' · ' + escapeHtml(item.priority) + '</small></div>';
            html += '<div class="col-md-4"><select class="form-control input-sm dependency-status" data-id="' + item.id + '">';
            $.each(['Open','In Progress','Blocked','Closed'], function(_, status) {
                html += '<option' + (item.status === status ? ' selected' : '') + '>' + status + '</option>';
            });
            html += '</select></div></div></div>';
        });
        $('#dependencyList').html(html);
    });
}

$(document).on('change', '.dependency-status', function() {
    var select = $(this);
    $.post('<?php echo page_url; ?>Df_dispatch_plan/update_dependency', {
        dependency_id: select.data('id'), status: select.val()
    }, function(response) {
        if (!response.status) {
            alert(response.message);
        } else {
            loadDependencies();
        }
    }, 'json');
});

function escapeHtml(value) {
    return $('<div>').text(value == null ? '' : value).html();
}

function showDispatchLoader() {
    $('#dispatchLoader').addClass('active');
}
</script>
<?php endif; ?>
</body>
</html>
