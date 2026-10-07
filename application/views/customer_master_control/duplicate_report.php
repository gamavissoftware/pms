<?php
if (!function_exists('customerAuditSafe')) {
    function customerAuditSafe($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$customer_master_read_only = isset($this->master_profile_guard) && $this->master_profile_guard->is_master_read_only();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Customer Duplicate Audit</title>
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <style>
        body{background:#eef3f9}.audit-card{background:#fff;border:1px solid #d8e1ec;border-radius:16px;padding:20px;margin-bottom:20px;box-shadow:0 8px 24px rgba(15,23,42,.07)}
        .summary{border-radius:12px;padding:16px;color:#fff;min-height:92px}.summary strong{display:block;font-size:25px}.s-blue{background:#2563eb}.s-purple{background:#7c3aed}.s-green{background:#059669}.s-orange{background:#d97706}
        .source{display:inline-block;border-radius:99px;padding:4px 9px;font-size:11px;font-weight:700;text-transform:uppercase}.marketing{background:#e0f2fe;color:#075985}.spares{background:#fef3c7;color:#92400e}
        .count{font-size:16px;font-weight:700;text-align:center}.zero{color:#94a3b8}.used{color:#047857}.unused-label{background:#fee2e2;color:#991b1b;padding:5px 9px;border-radius:99px;font-weight:700}.group-start td{border-top:3px solid #94a3b8!important}
        .table thead th{background:#17365d;color:#fff;vertical-align:middle!important}.filter-row{margin-bottom:5px}.muted{color:#64748b;font-size:12px}.page-title-box .btn{margin-top:8px}
    </style>
</head>
<body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<div class="wrapper"><div class="container-fluid">
        <div class="row"><div class="col-sm-12"><div class="page-title-box">
            <a href="<?php echo page_url; ?>Customer_master_control" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Customer Control Center</a>
            <h4 class="page-title">Duplicate Company &amp; Quotation Audit</h4>
            <p class="muted">Likely duplicates ignore punctuation and common legal suffixes such as Pvt, Ltd and LLP. Review records before editing or deleting anything.</p>
        </div></div></div>

        <div class="row">
            <div class="col-md-3 col-xs-6"><div class="summary s-blue"><span>All customer records</span><strong><?php echo (int)$summary['total_records']; ?></strong></div></div>
            <div class="col-md-3 col-xs-6"><div class="summary s-purple"><span>Likely duplicate groups</span><strong><?php echo (int)$summary['duplicate_groups']; ?></strong></div></div>
            <div class="col-md-3 col-xs-6"><div class="summary s-green"><span>Records with quotations</span><strong><?php echo (int)$summary['used_records']; ?></strong></div></div>
            <div class="col-md-3 col-xs-6"><div class="summary s-orange"><span>Records with no quotation</span><strong><?php echo (int)$summary['unused_records']; ?></strong></div></div>
        </div>

        <div class="audit-card">
            <form method="get" action="<?php echo page_url; ?>Customer_master_control/duplicate_report">
                <div class="row filter-row">
                    <div class="col-md-3"><label>Report scope</label><select name="scope" class="form-control"><option value="duplicates" <?php echo $filters['scope']==='duplicates'?'selected':''; ?>>Likely duplicates only</option><option value="all" <?php echo $filters['scope']==='all'?'selected':''; ?>>All customer records</option></select></div>
                    <div class="col-md-3"><label>Quotation usage</label><select name="usage" class="form-control"><option value="all">All</option><option value="used" <?php echo $filters['usage']==='used'?'selected':''; ?>>Has quotation</option><option value="unused" <?php echo $filters['usage']==='unused'?'selected':''; ?>>No quotation</option></select></div>
                    <div class="col-md-4"><label>Company / contact / email</label><input name="keyword" class="form-control" value="<?php echo customerAuditSafe($filters['keyword']); ?>" placeholder="Search records"></div>
                    <div class="col-md-2"><label>&nbsp;</label><button class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Apply</button></div>
                </div>
            </form>
        </div>

        <div class="audit-card"><div class="table-responsive">
            <table id="auditTable" class="table table-bordered table-striped">
                <thead><tr><th>Matched company group</th><th>Source / Record</th><th>Company &amp; contact</th><th>Marketing quotations</th><th>Spares quotations</th><th>Service quotations</th><th>Linked opportunities / Last stage</th><th>Total / Review</th><th>Action</th></tr></thead>
                <tbody>
                <?php $previous_key = null; foreach ($rows as $row) { $group_start = $previous_key !== null && $previous_key !== $row['match_key']; $previous_key = $row['match_key']; ?>
                    <tr class="<?php echo $group_start ? 'group-start' : ''; ?>">
                        <td><strong><?php echo customerAuditSafe($row['company_name']); ?></strong><?php if ($row['is_duplicate']) { ?><br><span class="label label-warning"><?php echo (int)$row['duplicate_count']; ?> matching records</span><?php } ?></td>
                        <td><span class="source <?php echo customerAuditSafe($row['source_type']); ?>"><?php echo customerAuditSafe($row['source_label']); ?></span><br><span class="muted">Record ID: <?php echo (int)$row['record_id']; ?></span></td>
                        <td><strong><?php echo customerAuditSafe($row['company_name']); ?></strong><br><?php echo customerAuditSafe($row['contact_person']); ?><br><span class="muted"><?php echo customerAuditSafe($row['email']); ?></span></td>
                        <td class="count <?php echo $row['marketing_quote_count'] ? 'used':'zero'; ?>"><?php echo (int)$row['marketing_quote_count']; ?></td>
                        <td class="count <?php echo $row['spares_quote_count'] ? 'used':'zero'; ?>"><?php echo (int)$row['spares_quote_count']; ?></td>
                        <td class="count <?php echo $row['service_quote_count'] ? 'used':'zero'; ?>"><?php echo (int)$row['service_quote_count']; ?></td>
                        <td><?php if (empty($row['opportunities'])) { ?><span class="muted">No opportunity</span><?php } else { foreach ($row['opportunities'] as $opportunity) { ?><div style="margin-bottom:7px"><a href="<?php echo page_url . customerAuditSafe($opportunity['url']); ?>" target="_blank"><strong><?php echo customerAuditSafe($opportunity['module'] . ': ' . $opportunity['number']); ?></strong> <i class="fa fa-external-link"></i></a><br><span class="label label-info"><?php echo customerAuditSafe($opportunity['stage']); ?></span></div><?php } } ?></td>
                        <td class="count"><?php echo (int)$row['total_quote_count']; ?><br><?php if (!(int)$row['total_quote_count']) { ?><span class="unused-label">No quotation</span><?php } else { ?><span class="label label-success">In use</span><?php } ?></td>
                        <td><?php if ($customer_master_read_only) { ?><span class="btn btn-default btn-sm disabled">View only</span><?php } else { ?><a class="btn btn-primary btn-sm" href="<?php echo page_url; ?>Customer_master_control/edit/<?php echo customerAuditSafe($row['source_type']); ?>/<?php echo (int)$row['record_id']; ?>"><i class="fa fa-pencil"></i> Review / Edit</a><?php if ($row['is_duplicate'] && !(int)$row['total_quote_count']) { ?><form method="post" action="<?php echo page_url; ?>Customer_master_control/delete_duplicate/<?php echo customerAuditSafe($row['source_type']); ?>/<?php echo (int)$row['record_id']; ?>" style="display:inline" onsubmit="return confirm('Permanently delete this unused duplicate customer and its <?php echo (int)$row['opportunity_count']; ?> linked zero-quotation opportunity record(s)? This action cannot be undone.');"><button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button></form><?php } ?><?php } ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div></div>
    </div></div>
<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script><script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script><script src="<?php echo assets_url; ?>js/jquery.core.js"></script><script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script><script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script><script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script><script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script><script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script><script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
<script>$(function(){$('#auditTable').DataTable({pageLength:50,order:[],dom:'lBfrtip',buttons:[{extend:'excel',title:'Customer Duplicate and Quotation Audit'},'print'],columnDefs:[{orderable:false,targets:[8]}]});});</script>
</body></html>
