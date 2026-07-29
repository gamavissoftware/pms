<?php
$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#2f6f9f';

$CI = &get_instance();
$CI->load->model('Dashboard_model');

if (!function_exists('dueTodayReportSentenceCase')) {
    function dueTodayReportSentenceCase($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        return ucwords(strtolower($value));
    }
}

if (!function_exists('dueTodayReportDateLabel')) {
    function dueTodayReportDateLabel($value, $format = 'd-M-Y')
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '';
        }

        return date($format, strtotime($value));
    }
}

$sessionData = $this->session->userdata('logged_in');
$currentUserId = !empty($sessionData['user_id']) ? (int) $sessionData['user_id'] : 0;
$currentUserRole = !empty($sessionData['role']) ? (int) $sessionData['role'] : 0;
$currentDepartmentId = !empty($sessionData['department_id']) ? (int) $sessionData['department_id'] : 0;

$isAdminView = 0;
$adminuserrole = $CI->Dashboard_model->getsuperadminuserole();

if (count($adminuserrole) > 0 && in_array($currentUserRole, $adminuserrole)) {
    $isAdminView = 1;
}

if ($currentUserId == 189 || $currentUserId == 209) {
    $isAdminView = 1;
}

$visibleDepartmentIds = array();
$userType = 3;
$scopeTitle = 'Personal Work View';
$scopeDescription = 'Only the tasks directly assigned to your login are shown here.';

if ($isAdminView == 1) {
    $userType = 1;
    $scopeTitle = 'Full Management View';
    $scopeDescription = 'This report shows every active task whose action is due today.';
} else {
    $team_q = $this->db
        ->select('department_id')
        ->from('prestogroup_teams')
        ->where('team_leader', $currentUserId)
        ->get();

    if ($team_q->num_rows() > 0) {
        foreach ($team_q->result() as $teamRow) {
            $visibleDepartmentIds[] = (int) $teamRow->department_id;
        }

        $visibleDepartmentIds = array_values(array_unique(array_filter($visibleDepartmentIds)));
        $userType = 2;
        $scopeTitle = 'Department / Team View';
        $scopeDescription = 'This report shows due-today tasks for the departments or teams mapped to your login.';
    } elseif ($currentDepartmentId > 0) {
        $visibleDepartmentIds[] = $currentDepartmentId;
    }
}

$today = date('Y-m-d');

$this->db->select('
    t.id as task_record_id,
    t.df_id,
    t.department_id,
    t.start_date,
    t.end_date,
    t.assigned_on,
    t.assigned_user,
    df.df_no,
    df.df_description,
    tm.task_name,
    d.department,
    u.title,
    u.first_name,
    u.last_name,
    p.company_name,
    m.title as marketing_title,
    m.first_name as marketing_first_name,
    m.last_name as marketing_last_name
');
$this->db->from('task_department_wise_scheduling t');
$this->db->join('df_release df', 't.df_id = df.id', 'inner');
$this->db->join('task_management tm', 't.taskid = tm.task_id', 'left');
$this->db->join('departments d', 't.department_id = d.department_id', 'left');
$this->db->join('system_users u', 't.assigned_user = u.user_id', 'left');
$this->db->join('poreceived p', 't.po_id = p.id', 'left');
$this->db->join('system_users m', 'p.added_by = m.user_id', 'left');
$this->db->where('t.df_id >', 0);
$this->db->where('df.df_status', 0);
$this->db->where('df.on_hold', 0);
$this->db->where('t.on_hold', 0);
$this->db->where('t.task_status', 0);
$this->db->where('t.end_date', $today);

if ($userType == 2) {
    if (!empty($visibleDepartmentIds)) {
        $this->db->where_in('t.department_id', $visibleDepartmentIds);
    } else {
        $this->db->where('t.department_id', 0);
    }
} elseif ($userType == 3) {
    $this->db->where('t.assigned_user', $currentUserId);
}

$this->db->order_by('d.department', 'ASC');
$this->db->order_by('df.df_no', 'ASC');
$this->db->order_by('tm.task_name', 'ASC');

$query = $this->db->get();

$rowsData = array();
$uniqueDf = array();
$uniqueDepartment = array();
$uniqueAssigned = array();
$unassignedCount = 0;

if ($query->num_rows() > 0) {
    foreach ($query->result() as $row) {
        $assignedTo = dueTodayReportSentenceCase(trim($row->title . ' ' . $row->first_name . ' ' . $row->last_name));
        if ($assignedTo === '') {
            $assignedTo = 'Unassigned';
            $unassignedCount++;
        } else {
            $uniqueAssigned[$assignedTo] = 1;
        }

        $marketingPerson = dueTodayReportSentenceCase(trim($row->marketing_title . ' ' . $row->marketing_first_name . ' ' . $row->marketing_last_name));
        if ($marketingPerson === '') {
            $marketingPerson = 'N/A';
        }

        $departmentName = !empty($row->department) ? dueTodayReportSentenceCase($row->department) : 'N/A';
        if ((int) $row->department_id > 0) {
            $uniqueDepartment[(int) $row->department_id] = 1;
        }

        $openFromRaw = '';
        if (!empty($row->assigned_on) && $row->assigned_on != '0000-00-00 00:00:00') {
            $openFromRaw = date('Y-m-d', strtotime($row->assigned_on));
        } elseif (!empty($row->start_date) && $row->start_date != '0000-00-00') {
            $openFromRaw = $row->start_date;
        }

        $openDays = 0;
        if ($openFromRaw !== '') {
            $openDays = max(0, (int) floor((strtotime($today) - strtotime($openFromRaw)) / 86400));
        }

        $uniqueDf[(int) $row->df_id] = 1;

        $rowsData[] = array(
            'df_no' => $row->df_no,
            'df_description' => $row->df_description,
            'company_name' => $row->company_name,
            'department_name' => $departmentName,
            'task_name' => $row->task_name,
            'assigned_to' => $assignedTo,
            'open_from' => $openFromRaw,
            'start_date' => $row->start_date,
            'due_date' => $row->end_date,
            'open_days' => $openDays,
            'marketing_person' => $marketingPerson
        );
    }
}

$totalTasks = count($rowsData);
$totalDf = count($uniqueDf);
$totalDepartments = count($uniqueDepartment);
$totalAssignedUsers = count($uniqueAssigned);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Due Today Tasks</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

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

    <style>
        body {
            background: #f3f6fb;
        }

        .report-hero {
            background: linear-gradient(135deg, <?php echo $themeColor; ?> 0%, #1f2937 100%);
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 22px;
            color: #fff;
            box-shadow: 0 18px 42px rgba(0, 0, 0, 0.14);
        }

        .report-hero h2 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 800;
            color: #fff;
        }

        .report-hero p {
            margin: 0;
            max-width: 860px;
            color: rgba(255, 255, 255, 0.88);
            font-size: 15px;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 12px;
            font-weight: 700;
        }

        .hero-actions {
            margin-top: 18px;
        }

        .hero-actions .btn {
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 700;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #dfe7f0;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 12px 30px rgba(35, 59, 86, 0.08);
            margin-bottom: 22px;
            min-height: 140px;
        }

        .summary-label {
            color: #6d7b8e;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .summary-value {
            display: block;
            margin-top: 10px;
            color: #203246;
            font-size: 34px;
            line-height: 1;
            font-weight: 900;
        }

        .summary-note {
            display: block;
            margin-top: 10px;
            color: #8290a3;
            font-size: 13px;
            line-height: 1.5;
        }

        .report-panel {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #dfe7f0;
            box-shadow: 0 12px 30px rgba(35, 59, 86, 0.08);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .report-panel-head {
            padding: 22px 24px 10px;
        }

        .report-panel-head h3 {
            margin: 0;
            color: #203246;
            font-size: 24px;
            font-weight: 800;
        }

        .report-panel-head p {
            margin: 8px 0 0;
            color: #6f7f92;
            font-size: 14px;
        }

        .report-panel-body {
            padding: 0 24px 24px;
        }

        .report-table thead th {
            background: #24384f;
            color: #fff;
            font-size: 12px;
            border-color: #24384f;
        }

        .report-table tbody td {
            vertical-align: top;
            font-size: 12px;
            color: #24384f;
        }

        .df-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            background: #e8f1fb;
            color: #1f5a8b;
            font-weight: 800;
            letter-spacing: .3px;
        }

        .meta-note {
            display: block;
            margin-top: 5px;
            color: #7d8ca0;
            font-size: 11px;
            line-height: 1.5;
        }

        .status-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            background: #fff3d9;
            color: #9f6500;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .status-pill.status-unassigned {
            background: #fde8e8;
            color: #bb2d3b;
        }

        .empty-state {
            padding: 28px 10px 6px;
            text-align: center;
            color: #6f7f92;
            font-size: 15px;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d0dae5;
            border-radius: 10px;
            min-height: 38px;
            padding: 6px 10px;
        }

        .dt-buttons .btn {
            border-radius: 10px;
            border: none;
            background: #244e75;
            color: #fff;
            font-weight: 700;
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="report-hero">
                <h2>Due Today Task Report</h2>
                <p><?php echo htmlspecialchars($scopeDescription, ENT_QUOTES, 'UTF-8'); ?> This list follows the same visibility logic as the main dashboard card.</p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="fa fa-calendar-check-o"></i> Report Date: <?php echo date('d M Y'); ?></span>
                    <span class="hero-badge"><i class="fa fa-shield"></i> Scope: <?php echo htmlspecialchars($scopeTitle, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="hero-badge"><i class="fa fa-tasks"></i> Open Tasks Due Today: <?php echo $totalTasks; ?></span>
                </div>

                <div class="hero-actions">
                    <a href="<?php echo page_url; ?>Dashboard" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
                    <a href="<?php echo page_url; ?>Task/helpticketsforyou/<?php echo base64_encode($currentUserId); ?>" class="btn btn-warning"><i class="fa fa-life-ring"></i> View Help Tickets</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <span class="summary-label">Due Today Tasks</span>
                        <span class="summary-value"><?php echo $totalTasks; ?></span>
                        <span class="summary-note">All open task rows whose expected action date is today.</span>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <span class="summary-label">Active DF Impacted</span>
                        <span class="summary-value"><?php echo $totalDf; ?></span>
                        <span class="summary-note">Distinct running DFs represented in this due-today workload.</span>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <span class="summary-label">Departments Visible</span>
                        <span class="summary-value"><?php echo $totalDepartments; ?></span>
                        <span class="summary-note">Departments contributing due-today tasks inside your report scope.</span>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="summary-card">
                        <span class="summary-label">Unassigned / Assigned</span>
                        <span class="summary-value"><?php echo $unassignedCount; ?> / <?php echo $totalAssignedUsers; ?></span>
                        <span class="summary-note">Unassigned items are useful for fast management attention today.</span>
                    </div>
                </div>
            </div>

            <div class="report-panel">
                <div class="report-panel-head">
                    <h3>Task List</h3>
                    <p>Open task items that require action today. Search, export, and review department-wise execution load.</p>
                </div>

                <div class="report-panel-body">
                    <?php if ($totalTasks === 0) { ?>
                        <div class="empty-state">
                            No open task is due today in your current dashboard scope.
                        </div>
                    <?php } ?>

                    <div class="table-responsive">
                        <table id="dueTodayTable" class="table table-striped table-bordered report-table">
                            <thead>
                                <tr>
                                    <th>SR NO.</th>
                                    <th>DF No.</th>
                                    <th>Company</th>
                                    <th>Department</th>
                                    <th>Task</th>
                                    <th>Assigned To</th>
                                    <th>Open Since</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Open Days</th>
                                    <th>Marketing Person</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $srNo = 1;
                                foreach ($rowsData as $row) {
                                    $isUnassigned = ($row['assigned_to'] === 'Unassigned');
                                ?>
                                <tr>
                                    <td><?php echo $srNo; ?></td>
                                    <td>
                                        <span class="df-badge"><?php echo htmlspecialchars($row['df_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars((string) $row['company_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['department_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars((string) $row['task_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if (!empty($row['df_description'])) { ?>
                                            <span class="meta-note"><?php echo htmlspecialchars((string) $row['df_description'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($row['assigned_to'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($isUnassigned) { ?>
                                            <span class="meta-note">Assignment pending</span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo ($row['open_from'] !== '') ? dueTodayReportDateLabel($row['open_from']) : '-'; ?></td>
                                    <td><?php echo dueTodayReportDateLabel($row['start_date']); ?></td>
                                    <td><?php echo dueTodayReportDateLabel($row['due_date']); ?></td>
                                    <td><?php echo (int) $row['open_days']; ?></td>
                                    <td><?php echo htmlspecialchars($row['marketing_person'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="status-pill<?php echo $isUnassigned ? ' status-unassigned' : ''; ?>">
                                            <?php echo $isUnassigned ? 'Due Today - Unassigned' : 'Due Today'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                                    $srNo++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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

    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        $(document).ready(function () {
            $('#dueTodayTable').DataTable({
                pageLength: 50,
                fixedHeader: true,
                responsive: true,
                order: [],
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Due Today Tasks <?php echo date('d-m-Y'); ?>'
                    },
                    {
                        extend: 'print',
                        title: 'Due Today Tasks'
                    }
                ]
            });
        });
    </script>
</body>
</html>
