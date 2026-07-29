<?php
if (!function_exists('changeControlStatusClass')) {
    function changeControlStatusClass($status)
    {
        $status = strtoupper((string)$status);
        if ($status === 'COMPLETED') {
            return 'label-success';
        }
        if ($status === 'IN_PROGRESS') {
            return 'label-primary';
        }
        if ($status === 'ASSIGNED') {
            return 'label-info';
        }
        if ($status === 'PENDING_HEAD_ACTION') {
            return 'label-warning';
        }
        return 'label-default';
    }
}

if (!function_exists('changeControlPriorityClass')) {
    function changeControlPriorityClass($priority)
    {
        $priority = strtoupper((string)$priority);
        if ($priority === 'CRITICAL') {
            return 'label-danger';
        }
        if ($priority === 'HIGH') {
            return 'label-warning';
        }
        if ($priority === 'MEDIUM') {
            return 'label-info';
        }
        return 'label-default';
    }
}

$assigned_only_access = !empty($module_nav['assigned_only_access']);
$can_raise_request = !empty($module_nav['can_raise_request']);
$assigned_delayed_count = 0;

if (!empty($assigned_queue)) {
    foreach ($assigned_queue as $assigned_row) {
        if (!empty($assigned_row['target_date']) && $assigned_row['target_date'] !== '0000-00-00' && strtotime($assigned_row['target_date']) < strtotime(date('Y-m-d'))) {
            $assigned_delayed_count++;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> DF Change Control Dashboard</title>

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body {
            background: #f2f6fb;
        }

        .dashboard-hero {
            background: linear-gradient(135deg, #14335f 0%, #0f172a 100%);
            border-radius: 18px;
            padding: 26px;
            color: #fff;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
            margin-bottom: 20px;
        }

        .dashboard-hero h3 {
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 700;
        }

        .dashboard-hero p {
            margin: 0;
            max-width: 920px;
            line-height: 1.8;
            font-size: 15px;
            opacity: 0.95;
        }

        .dashboard-hero .hero-actions {
            margin-top: 18px;
        }

        .hero-actions .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .metric-card {
            border-radius: 18px;
            padding: 18px 20px;
            color: #fff;
            min-height: 128px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
            margin-bottom: 18px;
        }

        .metric-card .metric-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.92;
        }

        .metric-card .metric-value {
            font-size: 34px;
            font-weight: 700;
            margin-top: 8px;
            line-height: 1;
        }

        .metric-card .metric-foot {
            margin-top: 10px;
            font-size: 13px;
            opacity: 0.92;
        }

        .metric-navy { background: linear-gradient(135deg, #17365d 0%, #224b86 100%); }
        .metric-green { background: linear-gradient(135deg, #0f766e 0%, #16a34a 100%); }
        .metric-orange { background: linear-gradient(135deg, #c2410c 0%, #f59e0b 100%); }
        .metric-red { background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%); }
        .metric-slate { background: linear-gradient(135deg, #334155 0%, #0f172a 100%); }
        .metric-blue { background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%); }

        .panel-card {
            background: #fff;
            border: 1px solid #dbe5ef;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .panel-head {
            padding: 18px 22px;
            border-bottom: 1px solid #e5edf5;
            background: #f7fbff;
        }

        .panel-head h4 {
            margin: 0;
            font-size: 18px;
            color: #17365d;
            font-weight: 700;
        }

        .panel-head p {
            margin: 6px 0 0;
            color: #63758a;
        }

        .panel-body {
            padding: 20px;
        }

        .table > thead > tr > th {
            background: #17365d;
            color: #fff;
            border-color: #17365d !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .mini-pill {
            display: inline-block;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            margin: 0 6px 6px 0;
        }

        .df-ref {
            font-weight: 700;
            color: #17365d;
        }

        .section-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .panel-card:target {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16), 0 12px 28px rgba(15, 23, 42, 0.08);
        }
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
                        <h4 class="page-title text-center">DF Change Control Dashboard</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                    <?php $this->load->view('df_change_control/_module_nav', array('module_nav' => $module_nav)); ?>
                </div>
            </div>

            <div class="dashboard-hero">
                <?php if ($assigned_only_access) { ?>
                    <h3>Assigned DF Change Control Tasks</h3>
                    <p>This screen is focused only on the department actions assigned to you, so you can update progress and close work without the extra management overview panels.</p>
                    <div class="hero-actions">
                        <a href="<?php echo page_url; ?>Df_change_control#assigned-queue" class="btn btn-warning">Open My Assignments</a>
                    </div>
                <?php } else { ?>
                    <h3>Rework, Add-On, Revision and ECN / IOM Intelligence</h3>
                    <p>This dashboard gives one place to see change requests raised on DFs, department-head planning queues, team execution queues, and the department load created by rework or client-side changes. Every request remains linked to the DF and is available from the Gantt chart.</p>
                    <div class="hero-actions">
                        <?php if ($can_raise_request) { ?>
                            <a href="<?php echo page_url; ?>Df_change_control/create" class="btn btn-warning">Raise ECN / IOM</a>
                        <?php } ?>
                        <a href="<?php echo page_url; ?>Mom/momdashboard" class="btn btn-default">Open MOM Module</a>
                    </div>
                <?php } ?>
            </div>

            <?php if (!$module_ready) { ?>
                <div class="alert alert-danger">
                    <strong>Migration pending:</strong> Please run <code><?php echo $migration_file; ?></code> before using this module.
                </div>
            <?php } else { ?>
                <?php if ($assigned_only_access) { ?>
                    <div class="section-note">
                        <strong>Focused View:</strong> Only the department actions assigned to you are shown here. Recent requests, department summaries, and broader management panels stay hidden for execution-only users.
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="metric-card metric-green">
                                <div class="metric-label">My Execution Queue</div>
                                <div class="metric-value"><?php echo count($assigned_queue); ?></div>
                                <div class="metric-foot">Assigned to me for completion</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="metric-card metric-red">
                                <div class="metric-label">My Delayed Actions</div>
                                <div class="metric-value"><?php echo $assigned_delayed_count; ?></div>
                                <div class="metric-foot">Need immediate follow-up from me</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-card" id="assigned-queue">
                        <div class="panel-head">
                            <h4>Execution Queue Assigned To Me</h4>
                            <p>Department tasks that need progress update or completion from the assignee.</p>
                        </div>
                        <div class="panel-body">
                            <table id="assignedQueueTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Ref</th>
                                        <th>DF</th>
                                        <th>Department</th>
                                        <th>Target</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assigned_queue as $row) { ?>
                                        <tr>
                                            <td>
                                                <div class="df-ref"><?php echo $row['change_no']; ?></div>
                                                <small><?php echo str_replace('_', ' ', $row['change_category']); ?></small>
                                            </td>
                                            <td><?php echo $row['df_no']; ?><br><small><?php echo ucwords(strtolower($row['df_description'])); ?></small></td>
                                            <td><?php echo ucwords(strtolower($row['department'])); ?></td>
                                            <td>
                                                <?php if (!empty($row['target_date']) && $row['target_date'] !== '0000-00-00') { ?>
                                                    <?php echo date('d-M-Y', strtotime($row['target_date'])); ?>
                                                    <?php if (strtotime($row['target_date']) < strtotime(date('Y-m-d'))) { ?>
                                                        <br><span class="label label-danger">Delayed</span>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </td>
                                            <td><span class="label <?php echo changeControlStatusClass($row['status']); ?>"><?php echo str_replace('_', ' ', $row['status']); ?></span></td>
                                            <td><a href="<?php echo page_url; ?>Df_change_control/view/<?php echo $row['change_id']; ?>" class="btn btn-success btn-xs">Update</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col-md-2 col-sm-6">
                            <div class="metric-card metric-navy">
                                <div class="metric-label">Total Requests</div>
                                <div class="metric-value"><?php echo $stats['total_requests']; ?></div>
                                <div class="metric-foot">All ECN / IOM requests raised</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="metric-card metric-blue">
                                <div class="metric-label">Active Requests</div>
                                <div class="metric-value"><?php echo $stats['active_requests']; ?></div>
                                <div class="metric-foot">Still under planning or execution</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="metric-card metric-orange">
                                <div class="metric-label">Pending HOD Action</div>
                                <div class="metric-value"><?php echo $stats['pending_head_actions']; ?></div>
                                <div class="metric-foot">Awaiting department-head planning</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="metric-card metric-red">
                                <div class="metric-label">Delayed Actions</div>
                                <div class="metric-value"><?php echo $stats['delayed_actions']; ?></div>
                                <div class="metric-foot">Missed their defined target dates</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="metric-card metric-slate">
                                <div class="metric-label">My Head Queue</div>
                                <div class="metric-value"><?php echo $stats['my_head_queue']; ?></div>
                                <div class="metric-foot">Need HOD planning from me</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="metric-card metric-green">
                                <div class="metric-label">My Execution Queue</div>
                                <div class="metric-value"><?php echo $stats['my_execution_queue']; ?></div>
                                <div class="metric-foot">Assigned to me for completion</div>
                            </div>
                        </div>
                    </div>

                    <div class="section-note">
                        <strong>Management View:</strong> <?php echo $stats['completed_requests']; ?> requests are fully completed, while <?php echo $stats['active_requests']; ?> are still affecting DF execution.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel-card" id="hod-queue">
                                <div class="panel-head">
                                    <h4>Department Head Action Queue</h4>
                                    <p>Requests waiting for department heads to define days and assign owners.</p>
                                </div>
                                <div class="panel-body">
                                    <table id="headQueueTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ref</th>
                                                <th>DF</th>
                                                <th>Department</th>
                                                <th>Requester</th>
                                                <th>Priority</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($head_queue as $row) { ?>
                                                <tr>
                                                    <td>
                                                        <div class="df-ref"><?php echo $row['change_no']; ?></div>
                                                        <small><?php echo str_replace('_', ' ', $row['change_category']); ?></small>
                                                    </td>
                                                    <td><?php echo $row['df_no']; ?><br><small><?php echo ucwords(strtolower($row['df_description'])); ?></small></td>
                                                    <td><?php echo ucwords(strtolower($row['department'])); ?></td>
                                                    <td><?php echo $row['creator_name']; ?></td>
                                                    <td><span class="label <?php echo changeControlPriorityClass($row['priority']); ?>"><?php echo $row['priority']; ?></span></td>
                                                    <td><a href="<?php echo page_url; ?>Df_change_control/view/<?php echo $row['change_id']; ?>" class="btn btn-primary btn-xs">Take Action</a></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="panel-card" id="assigned-queue">
                                <div class="panel-head">
                                    <h4>Execution Queue Assigned To Me</h4>
                                    <p>Department tasks that need progress update or completion from the assignee.</p>
                                </div>
                                <div class="panel-body">
                                    <table id="assignedQueueTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ref</th>
                                                <th>DF</th>
                                                <th>Department</th>
                                                <th>Target</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($assigned_queue as $row) { ?>
                                                <tr>
                                                    <td>
                                                        <div class="df-ref"><?php echo $row['change_no']; ?></div>
                                                        <small><?php echo str_replace('_', ' ', $row['change_category']); ?></small>
                                                    </td>
                                                    <td><?php echo $row['df_no']; ?><br><small><?php echo ucwords(strtolower($row['df_description'])); ?></small></td>
                                                    <td><?php echo ucwords(strtolower($row['department'])); ?></td>
                                                    <td>
                                                        <?php if (!empty($row['target_date']) && $row['target_date'] !== '0000-00-00') { ?>
                                                            <?php echo date('d-M-Y', strtotime($row['target_date'])); ?>
                                                            <?php if (strtotime($row['target_date']) < strtotime(date('Y-m-d'))) { ?>
                                                                <br><span class="label label-danger">Delayed</span>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            -
                                                        <?php } ?>
                                                    </td>
                                                    <td><span class="label <?php echo changeControlStatusClass($row['status']); ?>"><?php echo str_replace('_', ' ', $row['status']); ?></span></td>
                                                    <td><a href="<?php echo page_url; ?>Df_change_control/view/<?php echo $row['change_id']; ?>" class="btn btn-success btn-xs">Update</a></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-card" id="recent-requests">
                        <div class="panel-head">
                            <h4>Recent Change Requests</h4>
                            <p>Cross-functional rework, revision, add-on, and ECN / IOM activity linked with DFs.</p>
                        </div>
                        <div class="panel-body">
                            <table id="recentChangesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Ref</th>
                                        <th>DF</th>
                                        <th>Departments</th>
                                        <th>Requester</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Dept Progress</th>
                                        <th>Overdue</th>
                                        <th>Created On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_changes as $row) { ?>
                                        <tr>
                                            <td>
                                                <div class="df-ref"><?php echo $row['change_no']; ?></div>
                                                <div>
                                                    <span class="mini-pill"><?php echo $row['request_type']; ?></span>
                                                    <span class="mini-pill"><?php echo str_replace('_', ' ', $row['change_category']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $row['df_no']; ?><br><small><?php echo ucwords(strtolower($row['df_description'])); ?></small></td>
                                            <td><?php echo ucwords(strtolower($row['departments_list'])); ?></td>
                                            <td><?php echo $row['creator_name']; ?></td>
                                            <td><span class="label <?php echo changeControlPriorityClass($row['priority']); ?>"><?php echo $row['priority']; ?></span></td>
                                            <td><span class="label <?php echo changeControlStatusClass($row['status']); ?>"><?php echo str_replace('_', ' ', $row['status']); ?></span></td>
                                            <td><?php echo (int)$row['completed_department_count']; ?> / <?php echo (int)$row['department_count']; ?></td>
                                            <td>
                                                <?php if ((int)$row['overdue_department_count'] > 0) { ?>
                                                    <span class="label label-danger"><?php echo (int)$row['overdue_department_count']; ?> delayed</span>
                                                <?php } else { ?>
                                                    <span class="label label-success">On track</span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo date('d-M-Y h:i A', strtotime($row['created_on'])); ?></td>
                                            <td>
                                                <a href="<?php echo page_url; ?>Df_change_control/view/<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">View</a>
                                                <a href="<?php echo page_url; ?>Task/finalgantchartWithDetails/<?php echo $row['df_id']; ?>" target="_blank" class="btn btn-default btn-xs">Gantt</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="panel-card">
                                <div class="panel-head">
                                    <h4>My Raised Requests</h4>
                                    <p>Quick access to the DF change requests created by me, including assigned progress and requester / assignee communication.</p>
                                </div>
                                <div class="panel-body">
                                    <table id="myRequestsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ref</th>
                                                <th>DF</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Created On</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($my_requests as $row) { ?>
                                                <tr>
                                                    <td><?php echo $row['change_no']; ?></td>
                                                    <td><?php echo $row['df_no']; ?><br><small><?php echo ucwords(strtolower($row['df_description'])); ?></small></td>
                                                    <td><?php echo $row['request_type']; ?><br><small><?php echo str_replace('_', ' ', $row['change_category']); ?></small></td>
                                                    <td><span class="label <?php echo changeControlStatusClass($row['status']); ?>"><?php echo str_replace('_', ' ', $row['status']); ?></span></td>
                                                    <td><?php echo date('d-M-Y h:i A', strtotime($row['created_on'])); ?></td>
                                                    <td><a href="<?php echo page_url; ?>Df_change_control/view/<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Open</a></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="panel-card">
                                <div class="panel-head">
                                    <h4>Department Load Snapshot</h4>
                                    <p>Where change-control work is hitting the organization most right now.</p>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Department</th>
                                                <th>Total</th>
                                                <th>Pending HOD</th>
                                                <th>Execution</th>
                                                <th>Done</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($department_load as $row) { ?>
                                                <tr>
                                                    <td><?php echo ucwords(strtolower($row['department'])); ?></td>
                                                    <td><?php echo (int)$row['total_actions']; ?></td>
                                                    <td><?php echo (int)$row['pending_head_actions']; ?></td>
                                                    <td><?php echo (int)$row['execution_actions']; ?></td>
                                                    <td><?php echo (int)$row['completed_actions']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php $this->load->view('common/footer'); ?>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>
    <script>
        $(document).ready(function () {
            ['#headQueueTable', '#assignedQueueTable', '#recentChangesTable', '#myRequestsTable'].forEach(function (selector) {
                if ($(selector).length) {
                    $(selector).DataTable({
                        pageLength: 10,
                        order: []
                    });
                }
            });
        });
    </script>
    <?php $this->load->view('df_change_control/_notification_poller'); ?>
</body>
</html>
