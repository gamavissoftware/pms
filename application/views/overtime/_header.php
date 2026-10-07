<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo ot_e(sitetitle . ' ' . $title); ?></title>
<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
<?php foreach (array('bootstrap.min','core','components','icons','pages','menu','responsive') as $stylesheet) { ?>
<link href="<?php echo assets_url; ?>css/<?php echo $stylesheet; ?>.css" rel="stylesheet" type="text/css">
<?php } ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
<style>
#overtime-module { --ink:#172e43;--muted:#5d7183;--line:#dbe4eb;--accent:#087d75 }
#overtime-module * { box-sizing:border-box }
#overtime-module a { color:#096c76 }
#overtime-module .ot-module-nav { display:flex;gap:8px;flex-wrap:wrap;margin-top:16px }
#overtime-module .ot-module-nav a { padding:8px 14px;border-radius:6px;background:#244357;color:#fff;text-decoration:none }
#overtime-module .ot-module-nav a:hover, #overtime-module .ot-module-nav a[aria-current=page] { background:#087d75 }
#overtime-module { padding:56px 10px 24px;color:var(--ink); }
#overtime-module .heading { display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:22px }
#overtime-module h1 { font-size:29px;letter-spacing:-.6px;margin:0 }
#overtime-module h2 { font-size:19px;margin:0 0 16px }
#overtime-module h3 { font-size:16px;margin:0 0 10px }
#overtime-module p { margin:8px 0 16px }
#overtime-module .muted, #overtime-module small { color:var(--muted) }
#overtime-module .eyebrow { text-transform:uppercase;letter-spacing:1.7px;font-size:11px;font-weight:800;color:#087d75;margin-bottom:6px }
#overtime-module .card { background:white;border:1px solid var(--line);border-radius:12px;padding:22px;margin-bottom:20px;box-shadow:0 3px 12px #17354b04 }
#overtime-module .grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px }
#overtime-module .wide { grid-column:1/-1 }
#overtime-module .stats { display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:20px }
#overtime-module .stat { background:white;border:1px solid var(--line);border-radius:10px;padding:18px }
#overtime-module .stat strong { display:block;font-size:27px;margin-top:4px }
#overtime-module .stat span { color:var(--muted);font-size:13px }
#overtime-module .filters { display:flex;flex-wrap:wrap;align-items:end;gap:12px }
#overtime-module .filters>div { flex:1;min-width:145px }
#overtime-module label { display:block;font-weight:650;font-size:13px;margin-bottom:6px }
#overtime-module input, #overtime-module select, #overtime-module textarea { font:inherit;border:1px solid #b8c8d3;border-radius:6px;padding:10px;width:100%;background:#fff;color:var(--ink) }
#overtime-module textarea { resize:vertical;min-height:110px }
#overtime-module input:focus, #overtime-module select:focus, #overtime-module textarea:focus { outline:2px solid #77bfb9;outline-offset:1px }
#overtime-module button, #overtime-module .button { font:600 14px system-ui;cursor:pointer;display:inline-block;border:0;border-radius:6px;background:var(--accent);color:white;padding:11px 17px;text-decoration:none }
#overtime-module button.secondary, #overtime-module .button.secondary { background:#e9f0f4;color:var(--ink) }
#overtime-module button.danger { background:#ae3434 }
#overtime-module button:disabled { opacity:.55;cursor:wait }
#overtime-module .actions { display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-top:18px }
#overtime-module .notice { padding:14px 18px;background:#e9f5f3;border:1px solid #b8dcd6;border-radius:8px;margin-bottom:20px }
#overtime-module .error { background:#fff0ef;border-color:#e8bcb7;color:#8e2424 }
#overtime-module .warning { background:#fff8e8;border-color:#e9d49f;color:#685224 }
#overtime-module .table-wrap { overflow:auto }
#overtime-module table { border-collapse:collapse;width:100%;font-size:13px }
#overtime-module th { text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);background:#f6f8fa }
#overtime-module td, #overtime-module th { padding:12px;border-bottom:1px solid var(--line);vertical-align:top }
#overtime-module .num { text-align:right;white-space:nowrap }
#overtime-module .badge { display:inline-block;border-radius:20px;padding:4px 10px;white-space:nowrap;font-size:12px;background:#edf1f4;color:var(--ink);font-weight:500 }
#overtime-module .badge.APPROVED { background:#dff3e9;color:#146140 }
#overtime-module .badge.PENDING_LEADER, #overtime-module .badge.PENDING_ADMIN { background:#fff1cf;color:#79590e }
#overtime-module .badge.REJECTED { background:#fce7e5;color:#992e2e }
#overtime-module .empty { text-align:center;padding:38px;color:var(--muted) }
#overtime-module .steps { display:flex;gap:12px;margin:15px 0 }
#overtime-module .step { flex:1;padding:14px;background:#f2f6f8;border-radius:8px;border-top:3px solid #b8c9d2 }
#overtime-module .step.done { border-color:#188274;background:#edf8f4 }
#overtime-module .preserve { white-space:pre-wrap;overflow-wrap:anywhere }
#overtime-module dl { margin:0 }
#overtime-module dt { color:var(--muted);font-size:12px }
#overtime-module dd { margin:3px 0 16px;font-weight:550 }
#overtime-module .pagination { display:flex;justify-content:space-between;align-items:center;gap:10px;padding-top:18px }
#overtime-module .inline-form { display:inline }
#overtime-module .inline-form button { padding:5px 9px;font-size:12px }
#overtime-module .loader { position:fixed;inset:0;z-index:9999;background:#f3f6f8ed;place-items:center;text-align:center }
#overtime-module .loader:not([hidden]) { display:grid }
#overtime-module .spinner { border:4px solid #cbdedc;border-top-color:#087d75;border-radius:50%;width:42px;height:42px;margin:0 auto 18px;animation:ot-spin .8s linear infinite }
@keyframes ot-spin{to{transform:rotate(360deg)}}
#overtime-module footer { color:var(--muted);font-size:12px;margin-top:25px }
#overtime-module .print-only { display:none }
@media(max-width:700px){
#overtime-module { padding:65px 0 25px }
#overtime-module .stats { grid-template-columns:repeat(2,1fr) }
#overtime-module .stat strong { font-size:22px }
#overtime-module .grid { grid-template-columns:1fr }
#overtime-module .heading { align-items:flex-start;flex-direction:column }
#overtime-module .steps { flex-direction:column }
#overtime-module .card { padding:16px }
}
@media print{
#overtime-module .ot-module-nav, #overtime-module .no-print, #overtime-module .filters, #overtime-module .actions, #overtime-module .pagination { display:none!important }
#overtime-module .print-only { display:block }
#overtime-module { padding:0;max-width:none }
#overtime-module .card { border:0;padding:0;box-shadow:none }
#overtime-module .table-wrap { overflow:visible }
#overtime-module tr { break-inside:avoid }
#overtime-module .stats { gap:8px }
#overtime-module .stat { padding:8px }
#overtime-module a { color:inherit;text-decoration:none }
}
#overtime-module .ot-module-nav {margin:0 0 16px;}

@media print {.overtime-wrapper {margin-left:0!important;padding:0!important;} #overtime-module {padding:0;}}

/* Compact settings layout, scoped so the PMS shell retains its own styling. */
#overtime-module .ot-settings .heading {margin-bottom:12px;gap:12px;}
#overtime-module .ot-settings h1 {font-size:25px;}
#overtime-module .ot-settings .heading p {margin:5px 0 0;}
#overtime-module .ot-settings-tag {display:inline-flex;gap:7px;align-items:center;padding:6px 10px;background:#e6f2f0;color:#23756d;border:1px solid #cce5df;border-radius:20px;font-size:11px;white-space:nowrap;}
#overtime-module .ot-approval-strip {display:flex;flex-wrap:wrap;align-items:center;gap:8px 18px;margin-bottom:14px;padding:9px 13px;background:#edf6f5;border:1px solid #d3e8e4;border-left:3px solid var(--accent);border-radius:6px;font-size:12px;}
#overtime-module .ot-approval-strip>span {color:var(--accent);font-weight:600;}
#overtime-module .ot-approval-strip strong span {padding:0 8px;color:#719b96;}
#overtime-module .ot-approval-strip small {margin-left:auto;}
#overtime-module .ot-settings .card {padding:16px;margin-bottom:14px;border-radius:9px;box-shadow:0 2px 7px #17354b05;}
#overtime-module .ot-section-heading {display:flex;align-items:center;gap:10px;margin-bottom:14px;}
#overtime-module .ot-section-icon {display:flex;align-items:center;justify-content:center;width:34px;height:34px;flex:0 0 34px;border-radius:8px;background:#edf4f7;color:#247889;font-size:16px;}
#overtime-module .ot-settings h2 {font-size:16px;font-weight:600;margin:0;}
#overtime-module .ot-section-heading p {font-size:12px;margin:3px 0 0;}
#overtime-module .ot-settings-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;align-items:end;}
#overtime-module .ot-settings-grid>.col-md-3 {width:auto;float:none;padding:0;min-width:0;}
#overtime-module .ot-settings label {font-size:12px;margin-bottom:5px;}
#overtime-module .ot-settings input:not([type=hidden]), #overtime-module .ot-settings select {height:38px;padding:8px 10px;font-size:13px;border-color:#d1dce4;border-radius:5px;}
#overtime-module .ot-unit-input {position:relative;}
#overtime-module .ot-unit-input input {padding-right:78px!important;font-variant-numeric:tabular-nums;}
#overtime-module .ot-unit-input span {position:absolute;right:29px;top:0;height:38px;display:flex;align-items:center;color:#738594;font-size:11px;pointer-events:none;}
#overtime-module .ot-settings button {min-height:38px;padding:9px 14px;font-size:12px;white-space:normal;}
#overtime-module .ot-settings button i {margin-right:5px;}
#overtime-module .ot-settings-actions {display:flex;align-items:center;justify-content:space-between;gap:15px;border-top:1px solid #edf1f4;padding-top:12px;margin-top:14px;}
#overtime-module .ot-settings-actions small {font-size:11px;}
#overtime-module .ot-settings-actions button {flex-shrink:0;}
#overtime-module .ot-settings-submit button {width:100%;}
#overtime-module .ot-field-help {font-size:11px;color:var(--muted);margin:10px 0 0;}
#overtime-module .ot-settings-tables {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;align-items:start;}
#overtime-module .ot-settings-tables .card {min-width:0;}
#overtime-module .ot-settings td, #overtime-module .ot-settings th {padding:9px 10px;font-size:11px;}
#overtime-module .ot-settings .empty {padding:22px 10px;}
#overtime-module .ot-count {display:inline-block;font-size:11px;line-height:20px;min-width:20px;text-align:center;background:#e9f1f5;color:#577083;border-radius:12px;margin-left:5px;vertical-align:middle;}
@media(max-width:991px){
#overtime-module .ot-settings-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
#overtime-module .ot-settings-tables {grid-template-columns:1fr;gap:0;}
#overtime-module .ot-settings-tag {display:none;}
}
@media(max-width:575px){
#overtime-module .ot-settings-grid {grid-template-columns:1fr;gap:12px;}
#overtime-module .ot-settings .card {padding:13px;}
#overtime-module .ot-settings-actions {align-items:stretch;flex-direction:column;gap:10px;}
#overtime-module .ot-approval-strip small {margin-left:0;flex-basis:100%;}
}
#overtime-module .ot-create-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;align-items:start;}
#overtime-module .ot-create-grid>.col-md-3 {float:none;width:auto;padding:0;min-width:0;}
#overtime-module .ot-create-grid label {font-size:12px;}
#overtime-module .ot-create-grid input {min-height:38px;padding:8px 10px;font-size:13px;}
#overtime-module .ot-create-grid input[readonly] {background:#f3f7fa;color:#425e72;}
#overtime-module .ot-create-grid small {display:block;font-size:11px;margin-top:4px;}
#overtime-module .ot-create-grid #ot-duration {margin:0;min-height:38px;padding:8px 10px;font-size:12px;}
#overtime-module .ot-create-grid .wide {grid-column:1/-1;}
@media(max-width:991px){#overtime-module .ot-create-grid {grid-template-columns:repeat(2,minmax(0,1fr));}}
@media(max-width:575px){#overtime-module .ot-create-grid {grid-template-columns:1fr;}}
</style></head><body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<?php if (file_exists(APPPATH . 'views/common/info-section.php')) { $this->load->view('common/info-section'); } ?>
<div class="wrapper overtime-wrapper"><div class="container-fluid">
<main id="overtime-module">
<nav class="ot-module-nav no-print" aria-label="Overtime navigation">
<?php foreach (array('requests'=>array('','Requests'), 'create'=>array('create','New request'), 'approvals'=>array('approvals','Approval inbox'), 'reports'=>array('reports','Reports')) as $cap=>$link) { if (!empty($permissions[$cap])) { ?><a href="<?php echo ot_e(ot_link($link[0])); ?>"><?php echo ot_e($link[1]); ?></a><?php } } ?>
<?php if ((int)$viewer['is_admin'] === 1 && (!empty($permissions['policy']) || !empty($permissions['leaders']) || !empty($permissions['costs']))) { ?><a href="<?php echo ot_e(page_url . 'Dashboard/task_master_dashboard'); ?>">Overtime masters</a><?php } ?>
<a href="<?php echo ot_e(page_url . 'Dashboard'); ?>">Back to PMS</a></nav>
<?php if ($message = $this->session->flashdata('overtime_message')) { ?><div class="notice" role="status"><?php echo ot_e($message); ?></div><?php } ?>
<?php if ($message = $this->session->flashdata('overtime_error')) { ?><div class="notice error" role="alert"><?php echo ot_e($message); ?></div><?php } ?>
