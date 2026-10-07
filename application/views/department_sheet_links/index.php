<?php
if (!function_exists('departmentSheetEscape')) {
    function departmentSheetEscape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$can_add = (int) $viewer['is_admin'] === 1 || (int) $viewer['department_id'] > 0;
$current_department_name = isset($viewer['department']) && $viewer['department'] !== '' ? $viewer['department'] : 'Your department';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Department Sheets</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css">
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body {background:#f3f6f8; color:#263542; letter-spacing:0;}
        .department-sheets-wrapper {margin-top:0; padding:24px 0 36px; min-height:calc(100vh - 130px);}
        #department-sheets {max-width:1320px; margin:0 auto;}
        .dsl-heading {display:flex; align-items:center; justify-content:space-between; gap:18px; margin-bottom:20px;}
        .dsl-heading h1 {font-size:24px; line-height:1.25; margin:0 0 5px; color:#17324d; font-weight:700;}
        .dsl-heading p {margin:0; color:#647584; font-size:13px;}
        .dsl-btn {border-radius:4px; min-height:38px; display:inline-flex; align-items:center; justify-content:center; gap:7px; font-weight:600;}
        .dsl-btn-primary {background:#176b55; border-color:#176b55; color:#fff;}
        .dsl-btn-primary:hover,.dsl-btn-primary:focus {background:#115542; border-color:#115542; color:#fff;}
        .dsl-alert {border-radius:4px; border:1px solid; padding:11px 14px; margin-bottom:16px;}
        .dsl-alert-success {background:#eaf6f0; border-color:#a8d8c2; color:#155a45;}
        .dsl-alert-danger {background:#fff0ef; border-color:#efb7b2; color:#9d3028;}
        .dsl-alert-warning {background:#fff8e5; border-color:#ecd494; color:#765719;}
        .dsl-toolbar {display:grid; grid-template-columns:minmax(210px,280px) minmax(300px,1fr) auto; align-items:end; gap:12px; padding:15px; background:#fff; border:1px solid #dbe3e8; border-radius:6px; margin-bottom:14px;}
        .dsl-filter-field {min-width:0;}
        .dsl-filter-field label {display:block; margin:0 0 6px; color:#516675; font-size:11px; font-weight:700; text-transform:uppercase;}
        .dsl-filter-field .form-control {height:38px; border-color:#ccd8df; border-radius:4px; box-shadow:none;}
        .dsl-filter-field .form-control:focus {border-color:#176b55; box-shadow:0 0 0 2px rgba(23,107,85,.10);}
        .dsl-filter-actions {display:flex; align-items:center; gap:7px;}
        .dsl-filter-actions .dsl-btn {height:38px; white-space:nowrap;}
        .dsl-results-meta {display:flex; align-items:center; justify-content:space-between; gap:12px; margin:0 2px 10px; color:#687986; font-size:12px;}
        .dsl-results-meta strong {color:#334c5d;}
        .dsl-surface {background:#fff; border:1px solid #dbe3e8; border-radius:6px; overflow:hidden;}
        .dsl-table {margin:0; table-layout:fixed;}
        .dsl-table > thead > tr > th {background:#eaf0f3; color:#334c5d; border-bottom:1px solid #cedae1; padding:11px 12px; font-size:11px; text-transform:uppercase; letter-spacing:0; vertical-align:middle;}
        .dsl-table > tbody > tr > td {padding:13px 12px; vertical-align:middle; border-color:#e7ecef; overflow-wrap:anywhere;}
        .dsl-title {font-weight:700; color:#17324d; display:block; line-height:1.35;}
        .dsl-description {color:#71808c; font-size:12px; margin-top:4px; line-height:1.4;}
        .dsl-department {display:inline-block; background:#edf5fa; color:#245b78; padding:5px 8px; border-radius:4px; font-size:11px; font-weight:700;}
        .dsl-owner {color:#425766; font-size:12px;}
        .dsl-date {display:block; color:#87949d; margin-top:3px; font-size:11px;}
        .dsl-actions {display:flex; align-items:center; justify-content:flex-end; gap:5px; white-space:nowrap;}
        .dsl-icon-btn {width:34px; height:34px; padding:0; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; background:#fff; border:1px solid #ccd8df; color:#3e5666;}
        .dsl-icon-btn:hover,.dsl-icon-btn:focus {border-color:#176b55; color:#176b55; background:#f5fbf8;}
        .dsl-icon-btn.danger:hover,.dsl-icon-btn.danger:focus {border-color:#c4493d; color:#a9362d; background:#fff5f4;}
        .dsl-empty {text-align:center; padding:52px 20px; color:#6f7f8a;}
        .dsl-empty i {display:block; color:#9cadb7; font-size:30px; margin-bottom:10px;}
        .modal-content {border-radius:6px; border:0;}
        .modal-header {background:#17324d; color:#fff; border-radius:6px 6px 0 0;}
        .modal-header .close {color:#fff; opacity:.8; text-shadow:none;}
        .modal-title {color:#fff; font-size:17px; font-weight:700;}
        .modal-body {padding:20px;}
        .modal-footer {padding:13px 20px; background:#f7f9fa; border-radius:0 0 6px 6px;}
        .form-control {border-radius:4px;}
        textarea.form-control {resize:vertical; min-height:82px;}
        .dsl-required {color:#b33930;}
        .dsl-help {color:#778792; font-size:11px; margin-top:5px;}
        @media(max-width:991px) {.dsl-toolbar {grid-template-columns:minmax(180px,240px) minmax(240px,1fr);} .dsl-filter-actions {grid-column:1/-1; justify-content:flex-end;}}
        @media(max-width:767px) {.department-sheets-wrapper {padding-top:16px;} .dsl-heading {align-items:stretch; flex-direction:column;} .dsl-heading .btn {width:100%;} .dsl-toolbar {grid-template-columns:1fr;} .dsl-filter-actions {grid-column:auto; justify-content:stretch;} .dsl-filter-actions .btn {flex:1;} .dsl-results-meta {align-items:flex-start; flex-direction:column; gap:3px;} .dsl-table {min-width:780px;} .dsl-surface {overflow-x:auto;}}
    </style>
</head>
<body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<?php if (file_exists(APPPATH . 'views/common/info-section.php')) { $this->load->view('common/info-section'); } ?>
<div class="wrapper department-sheets-wrapper">
    <div class="container-fluid">
        <main id="department-sheets">
            <div class="dsl-heading">
                <div>
                    <h1>Department Google Sheets</h1>
                    <p>One place to find working sheets shared across departments.</p>
                </div>
                <?php if ($can_add && $schema_ready) { ?>
                    <button type="button" class="btn dsl-btn dsl-btn-primary" data-toggle="modal" data-target="#sheetLinkModal" data-mode="add">
                        <i class="fa fa-plus" aria-hidden="true"></i> Share Google Sheet
                    </button>
                <?php } ?>
            </div>

            <?php if ($message = $this->session->flashdata('department_sheet_links_message')) { ?>
                <div class="dsl-alert dsl-alert-success" role="status"><?php echo departmentSheetEscape($message); ?></div>
            <?php } ?>
            <?php if ($error = $this->session->flashdata('department_sheet_links_error')) { ?>
                <div class="dsl-alert dsl-alert-danger" role="alert"><?php echo departmentSheetEscape($error); ?></div>
            <?php } ?>
            <?php if (!$schema_ready) { ?>
                <div class="dsl-alert dsl-alert-warning" role="alert">The module database table could not be created. Run <strong>Database/department_sheet_links_001.sql</strong>, then reload this page.</div>
            <?php } elseif (!$can_add) { ?>
                <div class="dsl-alert dsl-alert-warning" role="alert">Your account is not assigned to a department. You can view shared sheets, but a department assignment is required to add one.</div>
            <?php } ?>

            <form class="dsl-toolbar" method="get" action="<?php echo departmentSheetEscape(page_url . 'department-sheets'); ?>">
                <div class="dsl-filter-field">
                    <label for="sheet-department-filter">Department</label>
                    <select class="form-control" id="sheet-department-filter" name="department_id">
                        <option value="0">All departments</option>
                        <?php foreach ($departments as $department) { ?>
                            <option value="<?php echo (int) $department['department_id']; ?>" <?php echo (int) $department_id === (int) $department['department_id'] ? 'selected' : ''; ?>><?php echo departmentSheetEscape($department['department']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="dsl-filter-field">
                    <label for="sheet-search">Search shared sheets</label>
                    <input id="sheet-search" class="form-control" type="search" name="q" value="<?php echo departmentSheetEscape($search); ?>" maxlength="100" placeholder="Search title, note or department">
                </div>
                <div class="dsl-filter-actions">
                    <button class="btn dsl-btn dsl-btn-primary" type="submit"><i class="fa fa-filter" aria-hidden="true"></i> Apply</button>
                    <?php if ($search !== '' || $department_id > 0) { ?><a class="btn btn-default dsl-btn" href="<?php echo departmentSheetEscape(page_url . 'department-sheets'); ?>"><i class="fa fa-times" aria-hidden="true"></i> Clear</a><?php } ?>
                </div>
            </form>

            <div class="dsl-results-meta">
                <strong><?php echo count($rows); ?> shared <?php echo count($rows) === 1 ? 'sheet' : 'sheets'; ?></strong>
                <span><?php echo $department_id > 0 ? 'Filtered by department' : 'Showing all departments'; ?></span>
            </div>

            <section class="dsl-surface" aria-label="Shared Google Sheets">
                <?php if ($rows) { ?>
                    <table class="table dsl-table">
                        <thead><tr>
                            <th style="width:30%">Sheet</th>
                            <th style="width:17%">Department</th>
                            <th style="width:21%">Shared by</th>
                            <th style="width:14%">Last updated</th>
                            <th style="width:18%" class="text-right">Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($rows as $row) {
                            $updated_at = !empty($row['updated_at']) ? $row['updated_at'] : $row['created_at'];
                            $timestamp = strtotime($updated_at);
                        ?>
                            <tr>
                                <td>
                                    <span class="dsl-title"><?php echo departmentSheetEscape($row['title']); ?></span>
                                    <?php if ($row['description'] !== '') { ?><div class="dsl-description"><?php echo nl2br(departmentSheetEscape($row['description'])); ?></div><?php } ?>
                                </td>
                                <td><span class="dsl-department"><?php echo departmentSheetEscape($row['department']); ?></span></td>
                                <td><span class="dsl-owner"><?php echo departmentSheetEscape($row['created_by_name'] !== '' ? $row['created_by_name'] : 'PMS user'); ?></span></td>
                                <td><?php echo $timestamp ? departmentSheetEscape(date('d M Y', $timestamp)) : '-'; ?><span class="dsl-date"><?php echo $timestamp ? departmentSheetEscape(date('h:i A', $timestamp)) : ''; ?></span></td>
                                <td>
                                    <div class="dsl-actions">
                                        <a class="dsl-icon-btn" href="<?php echo departmentSheetEscape($row['sheet_url']); ?>" target="_blank" rel="noopener noreferrer" title="Open Google Sheet"><i class="fa fa-external-link" aria-hidden="true"></i><span class="sr-only">Open Google Sheet</span></a>
                                        <button class="dsl-icon-btn" type="button" data-copy-url="<?php echo departmentSheetEscape($row['sheet_url']); ?>" title="Copy link"><i class="fa fa-copy" aria-hidden="true"></i><span class="sr-only">Copy link</span></button>
                                        <?php if (!empty($row['can_manage'])) { ?>
                                            <button class="dsl-icon-btn" type="button" data-toggle="modal" data-target="#sheetLinkModal" data-mode="edit" data-id="<?php echo (int) $row['id']; ?>" data-title="<?php echo departmentSheetEscape($row['title']); ?>" data-url="<?php echo departmentSheetEscape($row['sheet_url']); ?>" data-description="<?php echo departmentSheetEscape($row['description']); ?>" data-department-id="<?php echo (int) $row['department_id']; ?>" title="Edit link"><i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">Edit link</span></button>
                                            <form method="post" action="<?php echo departmentSheetEscape(page_url . 'department-sheets/delete/' . (int) $row['id']); ?>" data-delete-form style="display:inline">
                                                <input type="hidden" name="department_sheet_links_csrf" value="<?php echo departmentSheetEscape($csrf); ?>">
                                                <button class="dsl-icon-btn danger" type="submit" title="Remove link"><i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">Remove link</span></button>
                                            </form>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="dsl-empty">
                        <i class="fa fa-table" aria-hidden="true"></i>
                        <strong><?php echo $search !== '' ? 'No shared sheets match your search.' : 'No Google Sheets have been shared here yet.'; ?></strong>
                    </div>
                <?php } ?>
            </section>
        </main>
    </div>
</div>

<?php if ($can_add && $schema_ready) { ?>
<div class="modal fade" id="sheetLinkModal" tabindex="-1" role="dialog" aria-labelledby="sheetLinkModalTitle">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?php echo departmentSheetEscape(page_url . 'department-sheets/save'); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="sheetLinkModalTitle">Share Google Sheet</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="department_sheet_links_csrf" value="<?php echo departmentSheetEscape($csrf); ?>">
                    <input type="hidden" name="id" id="sheet-link-id" value="">
                    <?php if ((int) $viewer['is_admin'] === 1) { ?>
                        <div class="form-group">
                            <label for="sheet-department">Department <span class="dsl-required">*</span></label>
                            <select class="form-control" id="sheet-department" name="department_id" required>
                                <option value="">Select department</option>
                                <?php foreach ($departments as $department) { ?><option value="<?php echo (int) $department['department_id']; ?>"><?php echo departmentSheetEscape($department['department']); ?></option><?php } ?>
                            </select>
                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label>Department</label>
                            <p class="form-control-static"><span class="dsl-department"><?php echo departmentSheetEscape($current_department_name); ?></span></p>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="sheet-title">Title <span class="dsl-required">*</span></label>
                        <input class="form-control" id="sheet-title" name="title" type="text" maxlength="150" required placeholder="Example: Weekly production plan">
                    </div>
                    <div class="form-group">
                        <label for="sheet-url">Google Sheet link <span class="dsl-required">*</span></label>
                        <input class="form-control" id="sheet-url" name="sheet_url" type="url" maxlength="2048" required placeholder="https://docs.google.com/spreadsheets/d/...">
                        <div class="dsl-help">Only HTTPS links from Google Sheets are accepted.</div>
                    </div>
                    <div class="form-group">
                        <label for="sheet-description">Note</label>
                        <textarea class="form-control" id="sheet-description" name="description" maxlength="500" placeholder="What this sheet is used for, owner, or update cycle"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default dsl-btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn dsl-btn dsl-btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Save link</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>

<?php $this->load->view('common/footer'); ?>
<?php foreach (array('detect','fastclick','jquery.slimscroll','jquery.blockUI','waves','wow.min','jquery.nicescroll','jquery.scrollTo.min','jquery.core','jquery.app') as $script) { ?>
<script src="<?php echo assets_url; ?>js/<?php echo $script; ?>.js"></script>
<?php } ?>
<script>
(function($){
    $('#sheetLinkModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var editing = button.data('mode') === 'edit';
        $('#sheetLinkModalTitle').text(editing ? 'Edit Google Sheet' : 'Share Google Sheet');
        $('#sheet-link-id').val(editing ? button.data('id') : '');
        $('#sheet-title').val(editing ? button.attr('data-title') : '');
        $('#sheet-url').val(editing ? button.attr('data-url') : '');
        $('#sheet-description').val(editing ? button.attr('data-description') : '');
        if ($('#sheet-department').length) {
            $('#sheet-department').val(editing ? String(button.data('department-id')) : '');
        }
    });

    $('[data-delete-form]').on('submit', function(event){
        if (!window.confirm('Remove this shared Google Sheet link?')) event.preventDefault();
    });

    $('[data-copy-url]').on('click', function(){
        var button = this;
        var url = button.getAttribute('data-copy-url');
        function copied(){
            var icon = button.querySelector('i');
            icon.className = 'fa fa-check';
            button.title = 'Copied';
            window.setTimeout(function(){ icon.className = 'fa fa-copy'; button.title = 'Copy link'; }, 1500);
        }
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(copied);
            return;
        }
        var input = document.createElement('textarea');
        input.value = url;
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        if (document.execCommand('copy')) copied();
        document.body.removeChild(input);
    });
})(window.jQuery);
</script>
</body>
</html>
