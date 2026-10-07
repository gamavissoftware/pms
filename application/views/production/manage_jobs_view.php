<?php defined('BASEPATH') or exit('No direct script access allowed'); 

// PRE-CALCULATION FOR DASHBOARD STATS
$total_jobs    = 0;
$running_jobs  = 0;
$closed_jobs   = 0;

if (!empty($jobs)) {
    $total_jobs = count($jobs);
    foreach ($jobs as $j) {
        if ($j->is_active == 1) {
            $running_jobs++;
        } else {
            $closed_jobs++;
        }
    }
}

$running_percent = ($total_jobs > 0) ? round(($running_jobs / $total_jobs) * 100) : 0;
$closed_percent  = ($total_jobs > 0) ? round(($closed_jobs / $total_jobs) * 100) : 0;
$last_refreshed  = date('d M Y, h:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Production Dashboard</title>
    
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- GLOBAL THEME --- */
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f3f4f6; 
            color: #1f2933; 
        }
        .wrapper { padding-top: 25px; }

        /* Page header */
        .page-title { 
            font-size: 24px; 
            font-weight: 700; 
            color: #111827; 
            letter-spacing: -0.5px; 
        }
        .text-subtitle { 
            font-size: 13px; 
            color: #6b7280; 
            font-weight: 500; 
        }
        .page-header-meta {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 6px;
        }

        .btn-primary-soft {
            background: #e0ecff;
            color: #1d4ed8;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            padding: 8px 18px;
        }
        .btn-primary-soft i { font-size: 13px; }

        /* Gradient Stat Cards */
        .stat-card {
            border: none; 
            border-radius: 16px; 
            color: #fff; 
            position: relative; 
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15,23,42,0.18); 
            transition: transform 0.25s ease, box-shadow 0.25s ease; 
            height: 100%; 
            padding: 20px 20px 18px;
        }
        .stat-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 16px 35px rgba(15,23,42,0.25); 
        }

        /* Use a more product-like color palette (primary ~ #4872b8) */
        .bg-gradient-primary { 
            background: linear-gradient(135deg, #4872b8, #312e81); 
        }
        .bg-gradient-success { 
            background: linear-gradient(135deg, #059669, #16a34a); 
        }
        .bg-gradient-dark { 
            background: linear-gradient(135deg, #111827, #020617); 
        }

        .stat-label { 
            font-size: 11px; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            opacity: 0.8; 
        }
        .stat-value { 
            font-size: 34px; 
            font-weight: 700; 
            margin-top: 6px; 
            line-height: 1.1; 
        }
        .stat-subtext {
            font-size: 12px;
            margin-top: 6px;
            opacity: 0.9;
        }
        .stat-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            font-size: 11px;
            margin-top: 10px;
        }
        .stat-chip i { 
            font-size: 10px; 
            margin-right: 6px; 
        }

        .stat-icon { 
            position: absolute; 
            right: 16px; 
            bottom: 10px; 
            font-size: 45px; 
            opacity: 0.18; 
        }

        /* Tabs & Table container */
        .nav-pills-custom { 
            background: #ffffff; 
            padding: 6px; 
            border-radius: 999px; 
            box-shadow: 0 4px 14px rgba(15,23,42,0.06); 
            display: inline-flex; 
            margin-bottom: 18px;
        }
        .nav-pills-custom .nav-link { 
            border-radius: 999px; 
            padding: 7px 22px; 
            color: #6b7280; 
            font-weight: 600; 
            font-size: 13px; 
            transition: all 0.25s; 
            cursor: pointer;
        }
        .nav-pills-custom .nav-link.active { 
            background-color: #3b82f6; 
            color: #ffffff; 
            box-shadow: 0 8px 18px rgba(59,130,246,0.45); 
        }

        .card-table {
            background: #ffffff; 
            border-radius: 18px; 
            box-shadow: 0 15px 45px rgba(15,23,42,0.1); 
            border: none; 
            overflow: hidden;
        }

        .card-table-header {
            padding: 14px 22px 10px;
        }
        .card-table-header h5 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            color: #111827;
        }
        .card-table-header-meta {
            font-size: 12px;
            color: #9ca3af;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            color: #6b7280;
            margin-left: 12px;
        }
        .legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            margin-right: 6px;
        }
        .legend-running { background: #22c55e; }
        .legend-closed  { background: #9ca3af; }
        .legend-overdue { background: #ef4444; }

        /* DataTable styles */
        table.dataTable { 
            width: 100% !important; 
            margin-top: 0 !important; 
        }
        table.dataTable thead th {
            background-color: #f9fafb; 
            color: #4b5563; 
            font-weight: 700;
            text-transform: uppercase; 
            font-size: 11px; 
            border-bottom: 1px solid #e5e7eb !important; 
            padding: 14px 16px;
            white-space: nowrap;
        }
        table.dataTable tbody td {
            padding: 14px 16px; 
            vertical-align: middle; 
            color: #374151; 
            font-size: 13px; 
            border-top: 1px solid #f3f4f6;
        }
        #datatable tbody tr:hover td {
            background-color: #eff6ff;
        }

        /* Row status stripe */
        .row-running td:first-child {
            border-left: 3px solid #22c55e;
        }
        .row-closed td:first-child {
            border-left: 3px solid #9ca3af;
        }

        .df-number { 
            font-size: 15px; 
            font-weight: 700; 
            color: #2563eb; 
        }
        .df-remark {
            max-width: 260px;
        }

        .status-badge { 
            padding: 5px 12px; 
            border-radius: 999px; 
            font-size: 10px; 
            font-weight: 700; 
            text-transform: uppercase; 
            display: inline-flex; 
            align-items: center; 
            letter-spacing: 0.5px;
        }
        .status-running { 
            background-color: #dcfce7; 
            color: #166534; 
            border: 1px solid #bbf7d0; 
        }
        .status-closed  { 
            background-color: #f3f4f6; 
            color: #4b5563; 
            border: 1px solid #e5e7eb; 
        }

        .badge-overdue {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            color: #b91c1c;
            background: #fee2e2;
            border: 1px solid #fecaca;
            margin-top: 4px;
        }
        .badge-overdue i {
            font-size: 8px;
            margin-right: 4px;
        }

        .avatar-circle {
            width: 34px; 
            height: 34px; 
            background: #4872b8; 
            color: #ffffff;
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-weight: 700; 
            font-size: 12px; 
            margin-right: 10px;
        }

        /* Action buttons */
        .btn-action { 
            width: 32px; 
            height: 32px; 
            border-radius: 10px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            transition: all 0.18s; 
            border: none; 
            margin-left: 4px;
            font-size: 13px;
        }
        .btn-action:hover { 
            transform: translateY(-1px); 
            box-shadow: 0 4px 10px rgba(15,23,42,0.25); 
        }
        .btn-view   { background: #e0f2fe; color: #0369a1; }
        .btn-update { background: #f3e8ff; color: #7e22ce; }
        .btn-close  { background: #fee2e2; color: #b91c1c; }

        /* Modal */
        .modal-content { 
            border: none; 
            border-radius: 18px; 
            overflow: hidden; 
            box-shadow: 0 20px 60px rgba(15,23,42,0.5);
        }
        .modal-header { 
            background: linear-gradient(135deg, #4872b8, #312e81); 
            padding: 18px 24px; 
        }
        .modal-title { 
            color: #ffffff; 
            font-weight: 600; 
            font-size: 16px; 
        }
        .close { 
            color: #ffffff; 
            opacity: 0.8; 
            text-shadow: none; 
        }
        .modal-body { 
            padding: 26px 26px 10px; 
        }

        .custom-file-label { 
            border-radius: 10px; 
        }
        .form-control, .custom-select {
            border-radius: 10px;
            font-size: 13px;
        }

        .modal-footer.bg-light {
            padding: 14px 22px;
        }
    </style>
</head>

<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            
            <div class="row align-items-center mb-4 mt-3">
                <div class="col-md-7">
                    <h4 class="page-title mb-1">Production Job Dashboard</h4>
                    <div class="text-subtitle">Track live jobs, supervisors and machine load in real-time.</div>
                    <div class="page-header-meta">
                        Last refreshed: <?php echo $last_refreshed; ?> · Total Jobs: <?php echo $total_jobs; ?>
                    </div>
                </div>
                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                    <button type="button" class="btn btn-primary-soft mr-2" onclick="location.reload();">
                        <i class="fa fa-refresh mr-1"></i> Refresh
                    </button>
                    <a href="<?php echo page_url.'Masters/assembly_machine_report'; ?>"
                       class="btn btn-primary-soft mr-2">
                        <i class="fa fa-industry mr-1"></i> Assembly Report
                    </a>
                    <a href="<?php echo page_url.'Masters/allocate_job'; ?>" 
                       class="btn btn-dark shadow-sm px-4 py-2" 
                       style="border-radius: 10px; font-weight: 500;">
                        <i class="fa fa-plus-circle mr-2"></i> New Allocation
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="stat-card bg-gradient-primary">
                        <div class="stat-label">Total Jobs</div>
                        <div class="stat-value"><?php echo $total_jobs; ?></div>
                        <div class="stat-subtext">All production jobs currently in the system.</div>
                        <span class="stat-chip">
                            <i class="fa fa-info-circle"></i>
                            Running: <?php echo $running_jobs; ?> · Closed: <?php echo $closed_jobs; ?>
                        </span>
                        <i class="fa fa-cubes stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="stat-card bg-gradient-success">
                        <div class="stat-label">Running Live</div>
                        <div class="stat-value"><?php echo $running_jobs; ?></div>
                        <div class="stat-subtext">
                            <?php echo $total_jobs > 0 ? $running_percent.'% of all jobs are live.' : 'No active jobs currently.'; ?>
                        </div>
                        <span class="stat-chip">
                            <i class="fa fa-bolt"></i>
                            Stable production pipeline
                        </span>
                        <i class="fa fa-bolt stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-gradient-dark">
                        <div class="stat-label">Completed</div>
                        <div class="stat-value"><?php echo $closed_jobs; ?></div>
                        <div class="stat-subtext">
                            <?php echo $total_jobs > 0 ? $closed_percent.'% closed successfully.' : 'No completed jobs yet.'; ?>
                        </div>
                        <span class="stat-chip">
                            <i class="fa fa-check"></i>
                            Ready for dispatch / billing
                        </span>
                        <i class="fa fa-check-circle stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    
                    <div class="nav-pills-custom">
                        <a class="nav-link active" onclick="filterTable(this, 'all')">All Jobs</a>
                        <a class="nav-link" onclick="filterTable(this, 'running')">Running</a>
                        <a class="nav-link" onclick="filterTable(this, 'closed')">Completed</a>
                    </div>

                    <div class="card card-table">
                        <div class="card-table-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Job Allocation Overview</h5>
                                <div class="card-table-header-meta">
                                    Showing latest assignments by DF, machine and supervisor.
                                </div>
                            </div>
                            <div class="d-none d-sm-flex align-items-center">
                                <span class="legend-item">
                                    <span class="legend-dot legend-running"></span> Running
                                </span>
                                <span class="legend-item">
                                    <span class="legend-dot legend-closed"></span> Completed
                                </span>
                                <span class="legend-item">
                                    <span class="legend-dot legend-overdue"></span> Overdue
                                </span>
                            </div>
                        </div>
                        <hr class="m-0">
                        <div class="card-body p-0">
                            <table id="datatable" class="table table-hover dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th width="15%">Status</th>
                                        <th width="25%">Design File (DF)</th>
                                        <th width="20%">Line / Machine</th>
                                        <th width="20%">Supervisor</th>
                                        <th width="10%">Release Date</th>
                                        <th width="10%" class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($jobs)): ?>
                                        <?php foreach($jobs as $row): 
                                            $initials = substr($row->supervisor_name, 0, 1);
                                            $row_status_class = ($row->is_active == 1) ? 'row-running' : 'row-closed';
                                            $is_overdue = ($row->is_active == 1 && strtotime($row->release_date) < strtotime(date('Y-m-d')));
                                        ?>
                                        <tr class="<?php echo $row_status_class; ?>">
                                            <td>
                                                <?php if($row->is_active == 1): ?>
                                                    <span class="status-badge status-running">
                                                        <i class="fa fa-circle mr-2" style="font-size: 8px;"></i> Running
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge status-closed">
                                                        <i class="fa fa-check mr-2"></i> Closed
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="df-number"><?php echo $row->df_number; ?></span>
                                                <small class="d-block text-muted mt-1 text-truncate df-remark">
                                                    <?php echo $row->remarks ? $row->remarks : '-'; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-dark"><?php echo $row->machine_name; ?></div>
                                                <div class="small text-muted"><?php echo $row->line_name; ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle"><?php echo strtoupper($initials); ?></div>
                                                    <div>
                                                        <div class="font-weight-bold text-dark"><?php echo $row->supervisor_name; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">
                                                    <?php echo date('d M, Y', strtotime($row->release_date)); ?>
                                                </div>
                                                <?php if ($is_overdue): ?>
                                                    <div class="badge-overdue">
                                                        <i class="fa fa-exclamation-circle"></i> Overdue
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <div class="d-flex justify-content-end">
                                                    <a href="<?php echo page_url.'Masters/view_job_timeline/'.$row->assignment_id; ?>" 
                                                       class="btn-action btn-view" data-toggle="tooltip" title="View Timeline">
                                                        <i class="fa fa-bar-chart"></i>
                                                    </a>

                                                    <?php if($row->is_active == 1): ?>
                                                        <button class="btn-action btn-update" type="button" onclick="open_update_modal(<?php echo $row->assignment_id; ?>)" data-toggle="tooltip" title="Update Progress">
                                                            <i class="fa fa-camera"></i>
                                                        </button>
                                                        <button class="btn-action btn-close" type="button" onclick="close_job(<?php echo $row->assignment_id; ?>)" data-toggle="tooltip" title="Close Job">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div id="update-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="progress_form" onsubmit="return false;">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-cloud-upload mr-2"></i> Update Progress</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="assignment_id" id="modal_assignment_id">
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">
                                Upload Photo <small class="text-muted">(Optional)</small>
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="site_image" id="site_image" accept="image/*">
                                <label class="custom-file-label" for="site_image">Choose file...</label>
                            </div>
                        </div>

                        <div class="form-group mb-4">
    <label class="font-weight-bold text-dark">
        Report Attachment 
        <small class="text-muted">(Optional - PDF, Excel, Word, Image, CSV)</small>
    </label>
    <div class="custom-file">
        <input 
            type="file" 
            class="custom-file-input" 
            name="report_attachment" 
            id="report_attachment"
            accept=".pdf,.xls,.xlsx,.csv,.doc,.docx,image/*"
        >
        <label class="custom-file-label" for="report_attachment">Choose report attachment...</label>
    </div>
    <small class="text-muted d-block mt-1">
        Allowed files: PDF, Excel, CSV, Word, JPG, PNG. Max size: 10MB.
    </small>
</div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Completion Status</label>
                            <select name="progress_percent" class="form-control custom-select" style="height: 45px;">
                                <option value="10">10% - Setup / Started</option>
                                <option value="25">25% - Assembly in Progress</option>
                                <option value="50">50% - Half Way Done</option>
                                <option value="75">75% - Wiring / Calibration</option>
                                <option value="90">90% - Final Testing</option>
                                <option value="100">100% - Ready for Handover</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">Remarks <span class="text-danger">*</span></label>
                            <textarea name="remarks" id="progress_remarks" class="form-control" rows="3" placeholder="Describe today's work..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light px-4" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary px-4 shadow-sm" id="btn_upload" onclick="uploadData()">Upload Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php $this->load->view('common/footer'); ?>
    
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.gritter/1.7.4/js/jquery.gritter.min.js"></script>

    <script>
    var table; 

    $(document).ready(function() {
        console.log("Script Loaded Correctly");

        // 1. Initialize DataTable
        table = $('#datatable').DataTable({
            "order": [],
            "deferRender": true,
            "stateSave": true,
            "language": { 
                "search": "",
                "searchPlaceholder": "Search DF, supervisor, machine...",
                "lengthMenu": "_MENU_ per page"
            },
            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "drawCallback": function () { 
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded'); 
            }
        });

        $('[data-toggle="tooltip"]').tooltip();

        // 2. Custom File Input Label Fix
       $(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();

    if (fileName) {
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    } else {
        var defaultText = ($(this).attr('id') === 'report_attachment') 
            ? 'Choose report attachment...' 
            : 'Choose file...';

        $(this).siblings(".custom-file-label").removeClass("selected").html(defaultText);
    }
});
    });

    // 3. FAIL-SAFE UPLOAD FUNCTION
    function uploadData() {
        
        // Basic Validation
        if ($.trim($('#progress_remarks').val()) === '') {
            alert("Please enter remarks.");
            $('#progress_remarks').focus();
            return;
        }

        var btn = $('#btn_upload');
        var originalText = btn.text();
        btn.text('Uploading...').prop('disabled', true);

        // Capture Form Data manually using ID
        var form = document.getElementById('progress_form');
        var formData = new FormData(form);

        $.ajax({
            url: "<?php echo page_url.'Masters/save_daily_progress'; ?>",
            type: "POST",
            data: formData,
            contentType: false, // REQUIRED
            processData: false, // REQUIRED
            dataType: "json",
            success: function(resp){
                if(resp.status == 1) {
                    $('#update-modal').modal('hide');
                    // Try Gritter, else fallback alert
                    try {
                        $.gritter.add({ title:'Success', text:'Update saved successfully!', class_name:'gritter-success' });
                    } catch(e) {
                        alert("Update saved successfully!");
                    }
                    setTimeout(function(){ location.reload(); }, 1500);
                } else {
                    alert(resp.message);
                    btn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText); // Log for developer
                alert("Server Error. Please check console (F12) for details.");
                btn.text(originalText).prop('disabled', false);
            }
        });
    }

    function filterTable(element, type) {
        $('.nav-pills-custom .nav-link').removeClass('active');
        $(element).addClass('active');
        
        table.search('').columns().search('').draw();

        if (type === 'all') { 
            table.column(0).search('').draw(); 
        } else if (type === 'running') { 
            table.column(0).search('Running').draw(); 
        } else if (type === 'closed') { 
            table.column(0).search('Closed').draw(); 
        }
    }

    function open_update_modal(id) {
    $('#progress_form')[0].reset();

    $('#site_image').siblings('.custom-file-label').html('Choose file...');
    $('#report_attachment').siblings('.custom-file-label').html('Choose report attachment...');

    $('#modal_assignment_id').val(id);
    $('#update-modal').modal('show');
}

    function close_job(id) {
        if(confirm('Are you sure you want to CLOSE this job?')) {
            $.post("<?php echo page_url.'Masters/close_job'; ?>", {id:id}, function(data){
                location.reload();
            });
        }
    }
    </script>
</body>
</html>
