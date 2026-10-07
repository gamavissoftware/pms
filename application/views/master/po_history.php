<?php
$po_revisions   = isset($po_revisions) ? $po_revisions : array();
$po_change_logs = isset($po_change_logs) ? $po_change_logs : array();
$po_order       = isset($po_order) ? $po_order : null;
$po_id          = isset($po_id) ? (int) $po_id : 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
<title><?php echo sitetitle; ?>PO History</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
<script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style>
body { background:#f3f6fb; }
.po-panel {
    background:#fff;
    border:1px solid #e3e9f2;
    border-radius:12px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 8px 24px rgba(31,41,55,0.06);
}
.po-panel h4 {
    margin:0 0 4px;
    font-weight:800;
}
.po-panel .po-panel-note {
    color:#6b7280;
    font-size:13px;
    margin-bottom:14px;
}
.po-panel table { font-size:13px; }
.po-panel table th { background:#eef2f8; font-weight:700; }
.po-current-row { background:#f2fbf4; }
.po-empty { color:#6b7280; font-size:13px; padding:10px 0; }
.po-head {
    background:linear-gradient(135deg, #4872b8 0%, #111827 100%);
    border-radius:14px;
    color:#fff;
    padding:22px;
    margin-bottom:20px;
}
.po-head h3 { margin:0; font-weight:900; }
.po-head .po-head-sub { opacity:.9; margin-top:6px; }
</style>
</head>
<body>

<header id="topnav">
<?php $this->load->view('common/nav-menu');?>
</header>

<div class="wrapper">
<div class="container">

<div class="row" style="margin-top:20px;">
<div class="col-md-12">
    <div class="po-head">
        <div class="pull-right">
            <a href="<?php echo page_url;?>Task/myreceivedpolist" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to PO List
            </a>
        </div>
        <h3>PO History</h3>
        <div class="po-head-sub">
            <?php if ($po_order) { ?>
                <?php echo html_escape(ucwords(strtolower((string) $po_order->company_name)));?>
                &nbsp;&middot;&nbsp; PO No. <strong><?php echo html_escape((string) $po_order->pono);?></strong>
            <?php } else { ?>
                This PO could not be found.
            <?php } ?>
        </div>
    </div>
</div>
</div>

<div class="row">
<div class="col-md-12">
    <div class="po-panel">
        <h4>PO Attachments</h4>
        <div class="po-panel-note">
            Every PO file ever attached to this order. The current one is the copy the rest of the system uses; the earlier ones stay available here.
        </div>

        <?php if (!empty($po_revisions)) { ?>
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width:100px;">Revision</th>
                    <th>File</th>
                    <th style="width:200px;">Attached By</th>
                    <th style="width:160px;">Attached On</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($po_revisions as $revision) { ?>
                <tr<?php echo ((int) $revision->is_current === 1) ? ' class="po-current-row"' : '';?>>
                    <td>
                        <?php echo (int) $revision->revision_no;?>
                        <?php if ((int) $revision->is_current === 1) { ?>
                            <span class="label label-success">Current</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="<?php echo sfdocument;?>Taskdocument/<?php echo html_escape($revision->stored_file_name);?>" target="_blank">
                            <i class="fa fa-download"></i>
                            <?php echo html_escape($revision->original_file_name !== '' ? $revision->original_file_name : $revision->stored_file_name);?>
                        </a>
                    </td>
                    <td><?php echo html_escape($this->po_attachment->person_name($revision));?></td>
                    <td><?php echo date('d-m-Y H:i', strtotime($revision->uploaded_on));?></td>
                    <td><?php echo html_escape((string) $revision->remarks);?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
        <?php } else { ?>
        <div class="po-empty">No PO file has been attached to this order yet.</div>
        <?php } ?>
    </div>
</div>
</div>

<div class="row">
<div class="col-md-12">
    <div class="po-panel">
        <h4>Change Log</h4>
        <div class="po-panel-note">
            Who changed what on this PO, and when.
        </div>

        <?php if (!empty($po_change_logs)) { ?>
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width:150px;">Changed On</th>
                    <th style="width:180px;">Changed By</th>
                    <th style="width:170px;">Action</th>
                    <th style="width:170px;">Field</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($po_change_logs as $log) { ?>
                <tr>
                    <td><?php echo date('d-m-Y H:i', strtotime($log->changed_on));?></td>
                    <td><?php echo html_escape($this->po_attachment->person_name($log));?></td>
                    <td><?php echo html_escape(ucwords(strtolower(str_replace('_', ' ', (string) $log->action))));?></td>
                    <td><?php echo html_escape((string) $log->field_label);?></td>
                    <td><?php echo ($log->field_name !== null && trim((string) $log->old_value) !== '') ? html_escape((string) $log->old_value) : '<span style="color:#9ca3af;">&mdash;</span>';?></td>
                    <td><?php echo ($log->field_name !== null && trim((string) $log->new_value) !== '') ? html_escape((string) $log->new_value) : '<span style="color:#9ca3af;">&mdash;</span>';?></td>
                    <td><?php echo html_escape((string) $log->remarks);?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
        <?php } else { ?>
        <div class="po-empty">
            Nothing recorded yet for this PO. Every edit made from here on is logged with the user, the date and the old and new value.
        </div>
        <?php } ?>
    </div>
</div>
</div>

<?php if ($po_id > 0) { ?>
<div class="row" style="margin-bottom:30px;">
<div class="col-md-12">
    <a href="<?php echo page_url;?>Task/editpo/<?php echo $po_id;?>?from=my" class="btn btn-primary">
        <i class="fa fa-pencil"></i> Edit this PO
    </a>
</div>
</div>
<?php } ?>

<?php $this->load->view('common/footer');?>

</div>
</div>

<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/detect.js"></script>
<script src="<?php echo assets_url;?>js/fastclick.js"></script>
<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url;?>js/waves.js"></script>
<script src="<?php echo assets_url;?>js/wow.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
</body>
</html>
