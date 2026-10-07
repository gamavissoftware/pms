<?php
/**
 * Model-wise quote report.
 *
 * Styled after the opportunity dashboard's "Sales Performance & Brand
 * Momentum" section - the same gradient hero, frosted pills and tiles, and
 * the same --primary / --border tokens - so the page reads as part of that
 * dashboard rather than as a separate tool. Shared nav-menu + footer.
 *
 * Select2 is newselect2 (4.0.3); plugins/select2/dist is 3.4.8 despite the
 * name and has a different API.
 */
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

/* INR in lakh/crore grouping, everything else in thousands. */
$money = function ($v, $cur) {
    $v = (float) $v;
    $neg = $v < 0; $v = abs($v);
    $dec = (abs($v - round($v)) > 0.004) ? 2 : 0;
    if ($cur === 'INR') {
        $parts = explode('.', number_format($v, $dec, '.', ''));
        $int = $parts[0];
        if (strlen($int) > 3) {
            $int = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($int, 0, -3)) . ',' . substr($int, -3);
        }
        $s = $int . (isset($parts[1]) ? '.' . $parts[1] : '');
    } else {
        $s = number_format($v, $dec);
    }
    return ($neg ? '-' : '') . $s;
};
$qty = function ($v) { return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$sym = function ($cur) { return $cur === 'INR' ? '&#8377;' : ($cur === 'USD' ? '$' : '&euro;'); };
$initials = function ($name) {
    $p = preg_split('/\s+/', trim($name));
    $i = strtoupper(substr($p[0], 0, 1) . (count($p) > 1 ? substr(end($p), 0, 1) : ''));
    return $i !== '' && $i !== '-' ? $i : '?';
};

$query = http_build_query(array('model' => $model, 'company' => $selected));
$owners = array();
$dates  = array();
foreach ($quotes as $q) {
    $owners[$q['owner']] = 1;
    if ($q['quotation_date']) $dates[] = $q['quotation_date'];
}
$companies_in = array();
foreach ($quotes as $q) $companies_in[$q['company']] = 1;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Model-wise Quote Report</title>

        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css" />

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

        <style>
            :root{
                --primary:#0f62fe; --navy:#16324f; --green:#2f9e44;
                --bg:#f4f5f7; --text:#1f2d3d; --muted:#6c757d;
                --border:#e5e9f2; --soft:#f7faff; --soft-border:#dce8f8;
                --shadow:0 4px 12px rgba(0,0,0,.06);
            }
            body{background:var(--bg);}
            .wrapper .container-fluid{padding:5px 15px;}

            /* ---------- hero ---------- */
            .mq-hero{background:linear-gradient(135deg,#16324f 0%,#0f62fe 58%,#2f9e44 100%);border-radius:18px;
                     box-shadow:0 18px 45px rgba(15,98,254,.18);color:#fff;margin:10px 0 24px;overflow:hidden;
                     padding:24px;position:relative;}
            .mq-hero:before{background:rgba(255,255,255,.08);border-radius:999px;content:"";height:200px;
                            position:absolute;right:-50px;top:-60px;width:200px;}
            .mq-hero > *{position:relative;z-index:1;}
            .mq-eyebrow{color:rgba(255,255,255,.76);display:inline-block;font-size:12px;font-weight:700;
                        letter-spacing:.08em;margin-bottom:6px;text-transform:uppercase;}
            .mq-title{color:#fff;font-size:28px;font-weight:700;margin:0 0 6px;}
            .mq-lead{color:rgba(255,255,255,.88);font-size:14px;margin:0;max-width:760px;}

            .mq-filter{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:16px;
                       padding:16px;margin-top:18px;display:grid;gap:12px;
                       grid-template-columns:minmax(200px,1fr) minmax(260px,2fr) auto;align-items:end;}
            .mq-filter label{color:rgba(255,255,255,.8);display:block;font-size:11px;font-weight:700;
                             letter-spacing:.08em;margin-bottom:6px;text-transform:uppercase;}
            .mq-filter .form-control{border:0;border-radius:12px;min-height:42px;box-shadow:none;}
            .mq-filter .select2-container .select2-selection--single{height:42px;border:0;border-radius:12px;}
            .mq-filter .select2-container .select2-selection--single .select2-selection__rendered{line-height:42px;padding-left:12px;color:#1f2d3d;}
            .mq-filter .select2-container .select2-selection--single .select2-selection__arrow{height:40px;}
            .mq-filter .select2-container--disabled .select2-selection--single{background:rgba(255,255,255,.55);}
            .mq-filter .select2-container .select2-selection--multiple{min-height:42px;border:0;border-radius:12px;padding:4px 6px;}
            .mq-filter .select2-container--disabled .select2-selection--multiple{background:rgba(255,255,255,.55);}
            .mq-filter .select2-selection--multiple .select2-selection__choice{background:#e8f0ff;border:1px solid #c9dafc;color:#16324f;
                border-radius:999px;padding:2px 10px;margin-top:5px;font-size:12px;font-weight:600;}
            .mq-filter .select2-selection--multiple .select2-selection__choice__remove{color:#0f62fe;margin-right:5px;}
            .mq-filter .select2-selection--multiple .select2-search__field{margin-top:7px;}
            .mq-btn{border:0;border-radius:12px;min-height:42px;padding:0 18px;font-weight:700;display:inline-flex;
                    align-items:center;gap:8px;white-space:nowrap;text-decoration:none !important;}
            .mq-btn-light{background:#fff;color:var(--primary) !important;}
            .mq-btn-light:hover{background:#eef4ff;}
            .mq-btn-ghost{background:rgba(255,255,255,.14);color:#fff !important;border:1px solid rgba(255,255,255,.3);}
            .mq-btn-ghost:hover{background:rgba(255,255,255,.24);}
            .mq-btn-excel{background:#1d6f42;color:#fff !important;}
            .mq-btn-excel:hover{background:#185c37;}

            .mq-pills{display:flex;flex-wrap:wrap;gap:10px;margin-top:18px;}
            .mq-pill{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:999px;
                     color:#fff;font-size:12px;padding:8px 14px;}
            .mq-pill strong{font-weight:700;}

            .mq-tiles{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-top:18px;}
            .mq-tile{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.16);border-radius:16px;padding:16px;}
            .mq-tile-label{color:rgba(255,255,255,.74);font-size:11px;font-weight:700;letter-spacing:.06em;
                           margin-bottom:8px;text-transform:uppercase;}
            .mq-tile-value{color:#fff;font-size:24px;font-weight:800;line-height:1.1;margin-bottom:4px;word-break:break-word;}
            .mq-tile-note{color:rgba(255,255,255,.78);font-size:12px;}

            /* ---------- cards ---------- */
            .mq-card{background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);
                     margin-bottom:22px;overflow:hidden;}
            .mq-card-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
                          padding:16px 20px;border-bottom:1px solid var(--border);}
            .mq-card-title{font-size:17px;font-weight:700;color:var(--text);margin:0;}
            .mq-card-sub{font-size:12.5px;color:var(--muted);margin:2px 0 0;}
            .mq-empty{background:linear-gradient(180deg,#f9fbff 0%,#f2f6fb 100%);border:1px dashed #cfd9e6;
                      border-radius:14px;color:#6b778c;padding:40px 24px;text-align:center;font-size:14px;}
            .mq-empty .fa{font-size:28px;color:#9fb3cc;display:block;margin-bottom:10px;}

            table.mq{width:100%;margin:0;table-layout:auto;border-collapse:separate;border-spacing:0;}
            table.mq th{background:#f7f9fc;color:var(--muted);font-size:11px;font-weight:700;letter-spacing:.05em;
                        text-transform:uppercase;padding:10px 14px;border-bottom:1px solid var(--border);white-space:nowrap;}
            table.mq td{padding:10px 14px;border-bottom:1px solid #f0f3f8;font-size:13px;color:var(--text);vertical-align:middle;}
            table.mq tr:last-child td{border-bottom:0;}
            table.mq .num{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums;}
            table.mq .idx{color:#9aa7b8;width:44px;}
            table.mq tr.machine td{background:#f7faff;font-weight:600;}
            table.mq tr.total td{background:#eef4ff;font-weight:800;color:var(--navy);border-top:2px solid #d4e2fb;}
            table.glance tbody tr{cursor:pointer;}
            table.glance tr.grp td{background:#16324f;color:#fff;font-weight:700;font-size:13px;padding:9px 14px;}
            table.glance tr.grp td .fa{margin-right:6px;opacity:.8;}
            table.glance tr.grp .grp-n{font-weight:500;opacity:.75;margin-left:8px;font-size:12px;}
            table.glance tr.grp:hover td{background:#1d4270;}
            table.glance tr.grp-total td{background:#eef4ff;font-weight:800;color:var(--navy);cursor:default;}
            table.glance tr.grp-total:hover td{background:#eef4ff;}

            .cust{background:#fff;border:1px solid var(--border);border-radius:18px;box-shadow:var(--shadow);
                  padding:0 0 4px;margin-bottom:28px;overflow:hidden;}
            .cust-head{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;
                       padding:18px 22px;background:linear-gradient(135deg,#16324f 0%,#0f62fe 100%);color:#fff;}
            .cust-eyebrow{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.72);}
            .cust-name{color:#fff;font-size:22px;font-weight:700;margin:2px 0 2px;}
            .cust-sub{font-size:12.5px;color:rgba(255,255,255,.8);}
            .cust-totals{display:flex;gap:12px;flex-wrap:wrap;}
            .cust-totals div{background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);border-radius:12px;padding:8px 14px;text-align:right;}
            .cust-totals span{display:block;font-size:10.5px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.75);}
            .cust-totals strong{font-size:17px;color:#fff;}
            .cust > .mq-card{margin:18px 18px 0;box-shadow:none;}
            .cust-foot{margin:18px;border-radius:12px;background:#e7f6ec;border:1px solid #bfe5cb;}
            .cust-foot-row{display:flex;align-items:center;gap:22px;flex-wrap:wrap;padding:12px 16px;color:#1b6e34;font-size:13px;}
            .cust-foot-row + .cust-foot-row{border-top:1px solid #bfe5cb;}
            .cust-foot-row .lbl{font-weight:800;flex:1;min-width:200px;}
            .cust-foot-row .lbl em{font-style:normal;font-weight:500;opacity:.8;}
            .cust-foot-row .big{font-size:18px;font-weight:800;}
            table.glance tbody tr:hover td{background:#f7faff;}

            .owner{display:inline-flex;align-items:center;gap:8px;white-space:nowrap;}
            .owner .av{width:26px;height:26px;border-radius:999px;background:#e8f0ff;color:var(--primary);
                       font-size:11px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;}
            .tag{display:inline-block;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700;
                 background:#eef1f5;color:#44546a;}
            .tag-inr{background:#e7f6ec;color:#2f9e44;} .tag-usd{background:#e8f0ff;color:#0f62fe;} .tag-eur{background:#f3ecff;color:#7b3fe4;}

            .q-head{display:grid;grid-template-columns:1fr auto;gap:10px 20px;padding:18px 20px;border-bottom:1px solid var(--border);
                    background:linear-gradient(180deg,#fbfcff 0%,#fff 100%);}
            .q-ref{font-size:16px;font-weight:800;color:var(--primary);}
            .q-meta{display:flex;flex-wrap:wrap;gap:8px 18px;margin-top:6px;font-size:12.5px;color:var(--muted);}
            .q-meta .fa{margin-right:5px;color:#9fb3cc;}
            .q-model{margin-top:8px;font-size:13px;color:var(--text);}
            .q-model strong{color:var(--navy);}
            .q-sec{display:flex;align-items:center;gap:8px;padding:14px 20px 8px;font-size:12px;font-weight:800;
                   letter-spacing:.06em;text-transform:uppercase;color:var(--navy);}
            .q-sec .bar{width:4px;height:14px;border-radius:4px;background:var(--primary);}
            .q-sec.opt .bar{background:var(--green);}
            .q-body{padding:0 20px 6px;}
            .q-body .tbl{border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .q-none{color:var(--muted);font-size:13px;padding:4px 0 10px;}
            .q-foot{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:14px 20px 20px;}
            .q-stat{background:var(--soft);border:1px solid var(--soft-border);border-radius:12px;padding:12px 14px;}
            .q-stat span{display:block;color:var(--muted);font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;}
            .q-stat strong{display:block;color:var(--navy);font-size:17px;margin-top:4px;font-variant-numeric:tabular-nums;}
            .q-stat.main{background:linear-gradient(135deg,#16324f,#0f62fe);border:0;}
            .q-stat.main span{color:rgba(255,255,255,.75);} .q-stat.main strong{color:#fff;}

            .grand{background:linear-gradient(135deg,#16324f 0%,#0f62fe 70%,#2f9e44 100%);border-radius:18px;color:#fff;
                   padding:22px 24px;margin-bottom:30px;box-shadow:0 18px 45px rgba(15,98,254,.18);}
            .grand h3{color:#fff;font-size:20px;font-weight:700;margin:0 0 4px;}
            .grand p{color:rgba(255,255,255,.8);font-size:12.5px;margin:0 0 14px;}
            .grand-grid{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));}
            .grand-cur{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:16px;padding:16px 18px;}
            .grand-cur .cur{font-size:12px;font-weight:800;letter-spacing:.08em;color:rgba(255,255,255,.8);}
            .grand-cur .big{font-size:26px;font-weight:800;margin:6px 0 10px;font-variant-numeric:tabular-nums;}
            .grand-cur .row2{display:flex;justify-content:space-between;font-size:12.5px;color:rgba(255,255,255,.85);padding:3px 0;}

            @media (max-width: 991px){ .mq-filter{grid-template-columns:1fr 1fr;} }
            @media (max-width: 600px){ .mq-filter,.q-foot{grid-template-columns:1fr;} .q-head{grid-template-columns:1fr;} }
            @media print{
                header#topnav,.mq-filter,.footer,.no-print{display:none !important;}
                body{background:#fff;}
                .mq-hero,.grand,.q-stat.main{-webkit-print-color-adjust:exact;print-color-adjust:exact;}
                .mq-card{page-break-inside:avoid;box-shadow:none;}
                .cust-head,.cust-foot{-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            }
        </style>
    </head>

    <body class="fixed-left">

        <div id="wrapper">

            <header id="topnav">
                <?php $this->load->view('common/nav-menu');?>
            </header>

            <div class="wrapper">
                <div class="container-fluid">

                    <!-- ===================== HERO + FILTERS ===================== -->
                    <div class="mq-hero">
                        <span class="mq-eyebrow">Opportunity Intelligence</span>
                        <h3 class="mq-title">Model-wise Quote Report</h3>
                        <p class="mq-lead"><?= !empty($own_only) ? 'Your customers quoted for' : 'Every company quoted for' ?> a machine model, with each quote's Annexure-IV price schedule and optional items. Amounts are the basic cost &mdash; no discount, packing, forwarding, freight or taxes.</p>

                        <form method="get" action="<?= page_url . 'Model_quote_report' ?>" id="mq-form" class="mq-filter">
                            <div>
                                <label>Model</label>
                                <select class="form-control" name="model" id="mq-model">
                                    <option value="">Select model</option>
                                    <?php foreach ($models as $k => $label): ?>
                                        <option value="<?= $e($k) ?>" <?= (string) $k === $model ? 'selected' : '' ?>><?= $e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label>Customers<?= $companies ? ' &middot; ' . count($companies) . ' quoted &middot; pick one or more' : '' ?></label>
                                <select class="form-control" name="company[]" id="mq-company" multiple <?= $companies ? '' : 'disabled' ?>>
                                    <?php if ($companies): ?>
                                        <option value="__all" <?= in_array('__all', $selected, true) ? 'selected' : '' ?>>All companies (<?= (int) array_sum($companies) ?> quotes)</option>
                                        <?php foreach ($companies as $name => $n): ?>
                                            <option value="<?= $e($name) ?>" <?= in_array((string) $name, $selected, true) ? 'selected' : '' ?>><?= $e($name) ?> (<?= (int) $n ?>)</option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <button type="submit" class="mq-btn mq-btn-light"><i class="fa fa-search"></i> Show</button>
                                <?php if ($quotes): ?>
                                    <a class="mq-btn mq-btn-excel" href="<?= page_url . 'Model_quote_report/export?' . $e($query) ?>"><i class="fa fa-file-excel-o"></i> Excel</a>
                                    <button type="button" class="mq-btn mq-btn-ghost" onclick="window.print()" title="Print"><i class="fa fa-print"></i></button>
                                <?php endif; ?>
                            </div>
                        </form>

                        <?php if ($quotes): ?>
                            <div class="mq-pills">
                                <span class="mq-pill">Model &middot; <strong><?= $e($model_label) ?></strong><?= $text !== '' ? ' &middot; &ldquo;' . $e($text) . '&rdquo;' : '' ?></span>
                                <span class="mq-pill">Customers &middot; <strong><?= $e($company_label) ?></strong></span>
                                <?php if ($dates): ?>
                                    <span class="mq-pill"><?= $e(date('d M Y', strtotime(min($dates)))) ?> &ndash; <?= $e(date('d M Y', strtotime(max($dates)))) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="mq-tiles">
                                <div class="mq-tile">
                                    <div class="mq-tile-label">Quotes</div>
                                    <div class="mq-tile-value"><?= count($quotes) ?></div>
                                    <div class="mq-tile-note"><?= count($companies_in) ?> compan<?= count($companies_in) === 1 ? 'y' : 'ies' ?> &middot; <?= count($owners) ?> owner<?= count($owners) === 1 ? '' : 's' ?></div>
                                </div>
                                <?php foreach ($grand as $cur => $g): ?>
                                    <div class="mq-tile">
                                        <div class="mq-tile-label">Basic Cost &middot; <?= $e($cur) ?></div>
                                        <div class="mq-tile-value"><?= $sym($cur) ?> <?= $e($money($g['total'], $cur)) ?></div>
                                        <div class="mq-tile-note"><?= (int) $g['quotes'] ?> quote<?= $g['quotes'] == 1 ? '' : 's' ?> &middot; + <?= $e($money($g['opt'], $cur)) ?> optional</div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($model === '' && $text === ''): ?>
                        <div class="mq-empty"><i class="fa fa-cubes"></i>Choose a model to see every company it was quoted to.</div>
                    <?php elseif (!$companies): ?>
                        <div class="mq-empty"><i class="fa fa-search"></i>No quotation matches this model.</div>
                    <?php elseif (!$selected): ?>
                        <div class="mq-empty"><i class="fa fa-building-o"></i><?= count($companies) ?> companies were quoted <?= $e($model_label) ?>. Pick one or more &mdash; or &ldquo;All companies&rdquo; &mdash; and press Show.</div>
                    <?php elseif (!$quotes): ?>
                        <div class="mq-empty"><i class="fa fa-search"></i>No quotes for these customers.</div>
                    <?php else: ?>

                        <!-- ===================== AT A GLANCE ===================== -->
                        <div class="mq-card">
                            <div class="mq-card-head">
                                <div>
                                    <h4 class="mq-card-title">Quotes at a glance</h4>
                                    <p class="mq-card-sub">Grouped by customer. Click a row to jump to it.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="mq glance">
                                    <thead>
                                        <tr>
                                            <th>Ref No</th>
                                            <th>Date</th>
                                            <th>Contact</th>
                                            <th>Quote Owner</th>
                                            <th>Model</th>
                                            <th class="num">Basic Cost</th>
                                            <th class="num">Optional</th>
                                            <th class="num">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groups as $gname => $grp): ?>
                                            <tr class="grp" data-target="c<?= md5($gname) ?>">
                                                <td colspan="8"><i class="fa fa-building-o"></i> <?= $e($gname) ?> <span class="grp-n"><?= count($grp['quotes']) ?> quote<?= count($grp['quotes']) === 1 ? '' : 's' ?></span></td>
                                            </tr>
                                            <?php foreach ($grp['quotes'] as $q): $s = $schedule[(int) $q['id']]; $cur = $q['cur']; ?>
                                                <tr data-target="q<?= (int) $q['id'] ?>">
                                                    <td style="font-weight:700;color:var(--primary);white-space:nowrap;padding-left:28px;"><?= $e($q['ref_no'] !== '' ? $q['ref_no'] : 'Quote #' . $q['id']) ?></td>
                                                    <td style="white-space:nowrap;"><?= $q['quotation_date'] ? $e(date('d M Y', strtotime($q['quotation_date']))) : '' ?></td>
                                                    <td><?= $e($q['contact']) ?></td>
                                                    <td><span class="owner"><span class="av"><?= $e($initials($q['owner'])) ?></span><?= $e($q['owner']) ?></span></td>
                                                    <td style="max-width:280px;"><?= $e($q['machine_model_no']) ?></td>
                                                    <td class="num"><span class="tag tag-<?= strtolower($cur) ?>"><?= $e($cur) ?></span> <?= $e($money($s['total'], $cur)) ?></td>
                                                    <td class="num"><?= $e($money($s['opt_total'], $cur)) ?></td>
                                                    <td class="num" style="font-weight:800;color:var(--navy);"><?= $e($money($s['total'] + $s['opt_total'], $cur)) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php foreach ($grp['grand'] as $cur => $t): ?>
                                                <tr class="grp-total">
                                                    <td colspan="5">Total &middot; <?= $e($gname) ?> (<?= $e($cur) ?>)</td>
                                                    <td class="num"><?= $e($money($t['total'], $cur)) ?></td>
                                                    <td class="num"><?= $e($money($t['opt'], $cur)) ?></td>
                                                    <td class="num"><?= $sym($cur) ?> <?= $e($money($t['total'] + $t['opt'], $cur)) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ===================== BY CUSTOMER ===================== -->
                        <?php foreach ($groups as $gname => $grp): ?>
                        <div class="cust" id="c<?= md5($gname) ?>">
                            <div class="cust-head">
                                <div>
                                    <span class="cust-eyebrow">Customer</span>
                                    <h3 class="cust-name"><?= $e($gname) ?></h3>
                                    <span class="cust-sub"><?= count($grp['quotes']) ?> quote<?= count($grp['quotes']) === 1 ? '' : 's' ?></span>
                                </div>
                                <div class="cust-totals">
                                    <?php foreach ($grp['grand'] as $cur => $t): ?>
                                        <div><span><?= $e($cur) ?> total</span><strong><?= $sym($cur) ?> <?= $e($money($t['total'] + $t['opt'], $cur)) ?></strong></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php foreach ($grp['quotes'] as $q): $s = $schedule[(int) $q['id']]; $cur = $q['cur']; ?>
                            <div class="mq-card" id="q<?= (int) $q['id'] ?>">
                                <div class="q-head">
                                    <div>
                                        <div class="q-ref"><?= $e($q['ref_no'] !== '' ? $q['ref_no'] : 'Quote #' . $q['id']) ?></div>
                                        <div class="q-meta">
                                            <span><i class="fa fa-calendar"></i><?= $q['quotation_date'] ? $e(date('d M Y', strtotime($q['quotation_date']))) : '-' ?></span>
                                            <?php if ($q['contact'] !== '' && $q['contact'] !== $q['company']): ?>
                                                <span><i class="fa fa-user-o"></i><?= $e($q['contact']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="q-model"><?= $e($q['machine_name']) ?> &middot; <strong><?= $e($q['machine_model_no']) ?></strong></div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div class="owner" style="justify-content:flex-end;">
                                            <span class="av"><?= $e($initials($q['owner'])) ?></span>
                                            <span><span style="display:block;font-size:10.5px;color:var(--muted);font-weight:700;letter-spacing:.05em;text-transform:uppercase;text-align:left;">Quote Owner</span><strong><?= $e($q['owner']) ?></strong></span>
                                        </div>
                                        <div style="margin-top:8px;">
                                            <span class="tag tag-<?= strtolower($cur) ?>"><?= $e($cur) ?></span>
                                            <?php if ((int) $q['version'] > 1): ?><span class="tag">v<?= (int) $q['version'] ?></span><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="q-sec"><span class="bar"></span>Annexure-IV &middot; Price Schedule</div>
                                <div class="q-body">
                                    <?php if (!$s['lines']): ?>
                                        <div class="q-none">No price schedule on this quote.</div>
                                    <?php else: ?>
                                        <div class="tbl table-responsive">
                                            <table class="mq">
                                                <thead>
                                                    <tr>
                                                        <th class="idx">#</th>
                                                        <th>Item</th>
                                                        <th class="num" style="width:110px;">Qty</th>
                                                        <th class="num" style="width:160px;">Price (<?= $e($cur) ?>)</th>
                                                        <th class="num" style="width:170px;">Total (<?= $e($cur) ?>)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 0; foreach ($s['lines'] as $l): $i++; ?>
                                                        <tr class="<?= $l['kind'] === 'machine' ? 'machine' : '' ?>">
                                                            <td class="idx"><?= $i ?></td>
                                                            <td><?= $e($l['name']) ?></td>
                                                            <td class="num"><?= $e($qty($l['qty'])) ?><?= $l['unit'] !== '' ? ' <span style="color:var(--muted)">' . $e($l['unit']) . '</span>' : '' ?></td>
                                                            <td class="num"><?= $e($money($l['price'], $cur)) ?></td>
                                                            <td class="num"><?= $e($money($l['total'], $cur)) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <tr class="total">
                                                        <td></td><td colspan="3">Basic Cost</td>
                                                        <td class="num"><?= $sym($cur) ?> <?= $e($money($s['total'], $cur)) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="q-sec opt"><span class="bar"></span>Optional Items</div>
                                <div class="q-body">
                                    <?php if (!$s['options']): ?>
                                        <div class="q-none">No optional items quoted.</div>
                                    <?php else: ?>
                                        <div class="tbl table-responsive">
                                            <table class="mq">
                                                <thead>
                                                    <tr>
                                                        <th class="idx">#</th>
                                                        <th>Item</th>
                                                        <th class="num" style="width:110px;">Qty</th>
                                                        <th class="num" style="width:160px;">Price (<?= $e($cur) ?>)</th>
                                                        <th class="num" style="width:170px;">Total (<?= $e($cur) ?>)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 0; foreach ($s['options'] as $o): $i++; ?>
                                                        <tr>
                                                            <td class="idx"><?= $i ?></td>
                                                            <td><?= $e($o['name']) ?></td>
                                                            <td class="num"><?= $e($qty($o['qty'])) ?></td>
                                                            <td class="num"><?= $e($money($o['price'], $cur)) ?></td>
                                                            <td class="num"><?= $e($money($o['total'], $cur)) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <tr class="total">
                                                        <td></td><td colspan="3">Optional Items Total</td>
                                                        <td class="num"><?= $sym($cur) ?> <?= $e($money($s['opt_total'], $cur)) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="q-foot">
                                    <div class="q-stat"><span>Basic Cost</span><strong><?= $sym($cur) ?> <?= $e($money($s['total'], $cur)) ?></strong></div>
                                    <div class="q-stat"><span>Optional Items</span><strong><?= $sym($cur) ?> <?= $e($money($s['opt_total'], $cur)) ?></strong></div>
                                    <div class="q-stat main"><span>Basic + Optional</span><strong><?= $sym($cur) ?> <?= $e($money($s['total'] + $s['opt_total'], $cur)) ?></strong></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                            <div class="cust-foot">
                                <?php foreach ($grp['grand'] as $cur => $t): ?>
                                    <div class="cust-foot-row">
                                        <span class="lbl">Total &middot; <?= $e($gname) ?> <em>(<?= (int) $t['quotes'] ?> quote<?= $t['quotes'] == 1 ? '' : 's' ?>, <?= $e($cur) ?>)</em></span>
                                        <span>Basic <strong><?= $e($money($t['total'], $cur)) ?></strong></span>
                                        <span>Optional <strong><?= $e($money($t['opt'], $cur)) ?></strong></span>
                                        <span class="big"><?= $sym($cur) ?> <?= $e($money($t['total'] + $t['opt'], $cur)) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- ===================== TOTAL AMOUNT ===================== -->
                        <div class="grand">
                            <h3>Total Amount</h3>
                            <p><?= count($quotes) ?> quotes &middot; <?= count($groups) ?> customer<?= count($groups) === 1 ? '' : 's' ?> &middot; <?= $e($model_label) ?><?= count($grand) > 1 ? ' &middot; kept per currency, never added across currencies' : '' ?></p>
                            <div class="grand-grid">
                                <?php foreach ($grand as $cur => $g): ?>
                                    <div class="grand-cur">
                                        <div class="cur"><?= $e($cur) ?> &middot; <?= (int) $g['quotes'] ?> QUOTE<?= $g['quotes'] == 1 ? '' : 'S' ?></div>
                                        <div class="big"><?= $sym($cur) ?> <?= $e($money($g['total'] + $g['opt'], $cur)) ?></div>
                                        <div class="row2"><span>Basic Cost</span><strong><?= $e($money($g['total'], $cur)) ?></strong></div>
                                        <div class="row2"><span>Optional Items</span><strong><?= $e($money($g['opt'], $cur)) ?></strong></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php endif; ?>

                    <?php $this->load->view('common/footer');?>

                </div>
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
        <!-- After the foot jQuery: loading it again would wipe $.fn.select2. -->
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js"></script>

        <script>
        (function () {
            var $form = $('#mq-form');
            if ($.fn.select2) {
                $('#mq-model').select2({ width: '100%', minimumResultsForSearch: Infinity });
                $('#mq-company').select2({ width: '100%', placeholder: 'Select one or more customers', closeOnSelect: false });
            }
            /* A new model means a new company list - drop the old company. */
            $('#mq-model').on('change', function () {
                $('#mq-company').val(null);
                $form.submit();
            });
            /* Several customers can be picked, so nothing submits on pick -
               Show does. "All companies" and named customers exclude each
               other: picking All clears the names, picking a name clears All. */
            $('#mq-company').on('select2:select', function (ev) {
                var v = $(this).val() || [];
                if (ev.params.data.id === '__all') v = ['__all'];
                else v = v.filter(function (x) { return x !== '__all'; });
                $(this).val(v).trigger('change.select2');
            });

            $('table.glance tbody tr').on('click', function () {
                var el = document.getElementById($(this).data('target'));
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        })();
        </script>

    </body>
</html>
