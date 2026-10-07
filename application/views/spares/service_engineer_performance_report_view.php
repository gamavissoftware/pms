<?php
defined('BASEPATH') or exit('No direct script access allowed');
$summary = $report['summary'];
$performance_rows = $report['engineers'];
function performance_tone($value) {
    if ($value >= 85) return 'good';
    if ($value >= 65) return 'warn';
    return 'risk';
}
function performance_label($value) {
    if ($value >= 85) return 'Excellent';
    if ($value >= 65) return 'Good';
    return 'Needs attention';
}
$top_engineer = !empty($performance_rows) ? $performance_rows[0] : null;
$attention_rows = array_filter($performance_rows, function ($row) {
    return $row['overdue'] > 0
        || (($row['on_site'] + $row['completed']) > 0 && $row['mom_coverage'] < 65)
        || ($row['completed'] > 0 && $row['document_compliance'] < 65);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Engineer Performance</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root{--ink:#172033;--muted:#667085;--line:#e4e9f1;--brand:#145b83;--bg:#f4f7fb;--good:#16835b;--warn:#b56a00;--risk:#c53939}
        body{background:var(--bg);color:var(--ink)} .wrapper{padding-top:78px;padding-bottom:32px}
        .box{background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;box-shadow:0 8px 22px rgba(16,24,40,.045);margin-bottom:18px}
        .head{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;flex-wrap:wrap}.kicker{color:var(--brand);font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase}
        h2{margin:5px 0 7px;font-size:27px;font-weight:700}.sub{color:var(--muted);font-size:13px;max-width:760px;line-height:1.6;margin:0}.head-actions{display:flex;gap:8px;flex-wrap:wrap}
        .filters{display:grid;grid-template-columns:repeat(5,minmax(130px,1fr));gap:12px;align-items:end}.filters label{display:block;color:var(--muted);font-size:10px;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}.form-control{height:40px;border-radius:9px;border-color:var(--line)}
        .filter-buttons{display:flex;gap:7px}.filter-buttons .btn{height:40px;border-radius:9px;font-weight:600}
        .kpis{display:grid;grid-template-columns:repeat(6,1fr);gap:12px}.kpi{background:#fff;border:1px solid var(--line);border-radius:13px;padding:17px}.kpi-label{color:var(--muted);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em}.kpi-value{font-size:25px;font-weight:700;margin-top:7px}.kpi-note{color:var(--muted);font-size:11px;margin-top:4px}
        .section-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px}.section-head h4{margin:0;font-size:17px;font-weight:700}.formula{color:var(--muted);font-size:11px}
        .insights{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:18px}.insight{display:flex;gap:12px;align-items:flex-start;background:#fff;border:1px solid var(--line);border-radius:13px;padding:16px}.insight-icon{width:38px;height:38px;flex:0 0 38px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#edf6fb;color:var(--brand);font-size:17px}.insight strong{display:block;font-size:13px}.insight span{display:block;color:var(--muted);font-size:11px;line-height:1.5;margin-top:3px}
        .table-wrap{overflow:auto}.performance-table{min-width:1050px;margin:0}.performance-table th{background:#f7f9fc;color:#59677b;font-size:10px;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;border-bottom:1px solid var(--line)!important}.performance-table td{vertical-align:middle!important;border-top:1px solid #edf1f5!important;font-size:12px}.engineer-name{font-weight:700;font-size:13px}.team{color:var(--muted);font-size:11px;margin-top:2px}
        .score{display:inline-flex;align-items:center;justify-content:center;min-width:96px;height:31px;padding:0 10px;border-radius:9px;font-size:11px;font-weight:700}.score small{font-size:10px;margin-left:5px;opacity:.8}.good{color:var(--good);background:#e9f7f1}.warn{color:var(--warn);background:#fff4de}.risk{color:var(--risk);background:#fff0f0}.metric{font-weight:700}.metric small{color:var(--muted);font-weight:400}
        .bar{width:76px;height:6px;background:#edf1f5;border-radius:8px;overflow:hidden;margin-top:5px}.bar span{display:block;height:100%;background:var(--brand);border-radius:8px}.overdue{color:var(--risk);font-weight:700}.zero{color:#98a2b3}
        details summary{cursor:pointer;color:var(--brand);font-weight:600;white-space:nowrap}.visit-list{padding:10px 0 0;min-width:550px}.visit-item{display:grid;grid-template-columns:90px 1fr 120px 90px;gap:10px;padding:8px;border-top:1px solid var(--line);font-size:11px}.method{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.method div{border-left:3px solid #dbe8f0;padding-left:11px}.method strong{display:block;font-size:12px}.method span{display:block;color:var(--muted);font-size:11px;line-height:1.5;margin-top:3px}
        .empty{text-align:center;padding:46px 20px;color:var(--muted)}
        @media(max-width:1100px){.filters{grid-template-columns:repeat(3,1fr)}.kpis{grid-template-columns:repeat(3,1fr)}.method{grid-template-columns:repeat(2,1fr)}.insights{grid-template-columns:1fr}}
        @media(max-width:650px){.filters,.kpis,.method{grid-template-columns:1fr}.filter-buttons{grid-column:1}.head-actions{width:100%}.head-actions .btn{flex:1}}
        @media print{#topnav,.filters,.head-actions,details{display:none!important}.wrapper{padding-top:0}.box,.kpi{box-shadow:none;break-inside:avoid}.performance-table{min-width:0;font-size:9px}.container-fluid{padding:0}}
    </style>
</head>
<body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<div class="wrapper"><div class="container-fluid">
    <div class="box">
        <div class="head">
            <div><span class="kicker">Service Team Overview</span><h2>Engineer Performance Report</h2><p class="sub">See who is handling customer visits well, which visits are delayed, and where follow-up is needed. Select a date range or engineer to focus the report.</p></div>
            <div class="head-actions"><a class="btn btn-default" href="<?php echo page_url; ?>ServiceLeads/engineer_assignment_overview"><i class="fa fa-calendar"></i> Assignment Calendar</a><button class="btn btn-primary" type="button" onclick="window.print()"><i class="fa fa-print"></i> Print / PDF</button></div>
        </div>
    </div>

    <div class="box">
        <form method="get" class="filters" action="<?php echo page_url; ?>ServiceLeads/engineer_performance_report">
            <div><label>From date</label><input class="form-control" type="date" name="from_date" value="<?php echo htmlspecialchars($filters['from_date'], ENT_QUOTES, 'UTF-8'); ?>"></div>
            <div><label>To date</label><input class="form-control" type="date" name="to_date" value="<?php echo htmlspecialchars($filters['to_date'], ENT_QUOTES, 'UTF-8'); ?>"></div>
            <div><label>Engineer</label><select class="form-control" name="engineer_id"><option value="">All engineers</option><?php foreach($engineers as $engineer): ?><option value="<?php echo (int)$engineer->user_id; ?>" <?php echo (int)$filters['engineer_id']===(int)$engineer->user_id?'selected':''; ?>><?php echo htmlspecialchars(trim($engineer->first_name.' '.$engineer->last_name),ENT_QUOTES,'UTF-8'); ?></option><?php endforeach; ?></select></div>
            <div><label>Visit type</label><select class="form-control" name="visit_type"><option value="">All visit types</option><?php foreach($visit_types as $type): ?><option <?php echo $filters['visit_type']===$type?'selected':''; ?>><?php echo htmlspecialchars($type,ENT_QUOTES,'UTF-8'); ?></option><?php endforeach; ?></select></div>
            <div><label>Status</label><select class="form-control" name="status"><option value="">All statuses</option><?php foreach($status_map as $label): ?><option value="<?php echo htmlspecialchars($label,ENT_QUOTES,'UTF-8'); ?>" <?php echo $filters['status']===$label?'selected':''; ?>><?php echo htmlspecialchars($label,ENT_QUOTES,'UTF-8'); ?></option><?php endforeach; ?></select></div>
            <div class="filter-buttons"><button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> Show Report</button><a class="btn btn-default" title="Clear filters" href="<?php echo page_url; ?>ServiceLeads/engineer_performance_report"><i class="fa fa-refresh"></i> Reset</a></div>
        </form>
    </div>

    <div class="kpis">
        <div class="kpi"><div class="kpi-label">Total visits</div><div class="kpi-value"><?php echo $summary['total_visits']; ?></div><div class="kpi-note"><?php echo $summary['engineers']; ?> active engineers</div></div>
        <div class="kpi"><div class="kpi-label">Visits completed</div><div class="kpi-value"><?php echo $summary['completion_rate']; ?>%</div><div class="kpi-note"><?php echo $summary['completed']; ?> of <?php echo $summary['total_visits']; ?> visits</div></div>
        <div class="kpi"><div class="kpi-label">Finished on time</div><div class="kpi-value"><?php echo $summary['on_time_rate']; ?>%</div><div class="kpi-note">Within the planned visit dates</div></div>
        <div class="kpi"><div class="kpi-label">Daily updates added</div><div class="kpi-value"><?php echo $summary['mom_coverage']; ?>%</div><div class="kpi-note">Visits with at least one update</div></div>
        <div class="kpi"><div class="kpi-label">Reports uploaded</div><div class="kpi-value"><?php echo $summary['document_compliance']; ?>%</div><div class="kpi-note">Closed visits with signed files</div></div>
        <div class="kpi"><div class="kpi-label">Delayed visits</div><div class="kpi-value <?php echo $summary['overdue']?'overdue':''; ?>"><?php echo $summary['overdue']; ?></div><div class="kpi-note">Still open after planned end date</div></div>
    </div>

    <div class="insights">
        <div class="insight"><div class="insight-icon"><i class="fa fa-trophy"></i></div><div><strong><?php echo $top_engineer ? htmlspecialchars($top_engineer['engineer_name'], ENT_QUOTES, 'UTF-8') : 'No performance data'; ?></strong><span><?php echo $top_engineer ? 'Leading this period with '.$top_engineer['completed'].' completed visits and an overall '.$top_engineer['score'].' rating.' : 'Change the filters to view engineer performance.'; ?></span></div></div>
        <div class="insight"><div class="insight-icon"><i class="fa fa-exclamation-circle"></i></div><div><strong><?php echo count($attention_rows); ?> engineer<?php echo count($attention_rows) === 1 ? '' : 's'; ?> need follow-up</strong><span>Based on delayed visits, missing daily updates, or missing signed reports.</span></div></div>
        <div class="insight"><div class="insight-icon"><i class="fa fa-clock-o"></i></div><div><strong><?php echo $summary['planned_days']; ?> field days planned</strong><span>Total assigned visit days during the selected period. Use this to compare workload across engineers.</span></div></div>
    </div>

    <div class="box" style="margin-top:18px">
        <div class="section-head"><h4>Engineer-wise summary</h4><span class="formula"><i class="fa fa-info-circle"></i> Overall rating combines completion, punctuality, updates and signed reports.</span></div>
        <?php if(empty($performance_rows)): ?><div class="empty"><i class="fa fa-bar-chart fa-2x"></i><p>No visits match the selected period and filters.</p></div><?php else: ?>
        <div class="table-wrap"><table class="table performance-table"><thead><tr><th>#</th><th>Engineer</th><th>Overall rating</th><th>Visit workload</th><th>Completed</th><th>On time</th><th>Daily updates</th><th>Signed reports</th><th>Field days</th><th>Customers</th><th>Extra days</th><th>Delayed</th><th>Visit details</th></tr></thead><tbody>
        <?php foreach($performance_rows as $index=>$row): ?><tr>
            <td><?php echo $index+1; ?></td><td><div class="engineer-name"><?php echo htmlspecialchars($row['engineer_name'],ENT_QUOTES,'UTF-8'); ?></div><div class="team"><?php echo $row['team']; ?></div></td>
            <td><span class="score <?php echo performance_tone($row['score']); ?>"><?php echo performance_label($row['score']); ?><small><?php echo $row['score']; ?></small></span></td>
            <td><span class="metric"><?php echo $row['total']; ?></span><div class="team"><?php echo $row['on_site']; ?> on-site · <?php echo $row['scheduled']; ?> planned</div></td>
            <td><span class="metric"><?php echo $row['completion_rate']; ?>%</span><div class="bar"><span style="width:<?php echo min(100,$row['completion_rate']); ?>%"></span></div></td>
            <td><span class="metric"><?php echo $row['on_time_rate']; ?>%</span><div class="team"><?php echo $row['on_time']; ?>/<?php echo $row['completed']; ?> closures</div></td>
            <td><span class="metric"><?php echo $row['mom_coverage']; ?>%</span><div class="team"><?php echo $row['mom_entries']; ?> updates entered</div></td>
            <td><span class="metric"><?php echo $row['document_compliance']; ?>%</span><div class="team"><?php echo $row['completed_with_docs']; ?> of <?php echo $row['completed']; ?> closed visits</div></td>
            <td class="metric"><?php echo $row['planned_days']; ?></td><td class="metric"><?php echo $row['customer_count']; ?></td><td><span class="metric"><?php echo $row['extensions']; ?></span><div class="team"><?php echo $row['extension_rate']; ?>% visits</div></td>
            <td class="<?php echo $row['overdue']?'overdue':'zero'; ?>"><?php echo $row['overdue']; ?></td>
            <td><details><summary>See visits</summary><div class="visit-list"><?php foreach($row['visits'] as $visit): ?><div class="visit-item"><strong><?php echo htmlspecialchars($visit->op_no,ENT_QUOTES,'UTF-8'); ?></strong><span><?php echo htmlspecialchars($visit->customer_name,ENT_QUOTES,'UTF-8'); ?></span><span><?php echo date('d M',strtotime($visit->start_date)); ?> – <?php echo date('d M Y',strtotime($visit->end_date)); ?></span><a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_detail/<?php echo (int)$visit->visit_id; ?>"><?php echo htmlspecialchars($visit->visit_status,ENT_QUOTES,'UTF-8'); ?></a></div><?php endforeach; ?></div></details></td>
        </tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
    </div>

    <div class="box"><div class="section-head"><h4>What the numbers mean</h4></div><div class="method">
        <div><strong>Completed</strong><span>How many assigned visits have been closed during the selected period.</span></div>
        <div><strong>On time</strong><span>A visit is on time when it is closed on or before its planned end date.</span></div>
        <div><strong>Daily updates and reports</strong><span>Shows whether engineers are entering work updates and uploading signed customer reports.</span></div>
        <div><strong>Use with context</strong><span>Before evaluating anyone, also consider travel, job difficulty, customer readiness and spare-part availability.</span></div>
    </div></div>
</div></div>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script><script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
</body></html>
