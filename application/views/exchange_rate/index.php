<?php
/**
 * Exchange rate - in the PMS's own chrome.
 *
 * Header, nav and footer are the shared ones, and the styling is the house
 * kit (bootstrap + core/components/pages css) rather than anything invented
 * here - the page-title box, the stat tiles and the table below are the same
 * shapes App Log uses, so this reads as part of the software and not as a
 * form someone bolted on.
 */
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) {
    $d = (float) $v;
    return $d > 0 ? number_format($d, 2) : '-';
};
/* 83.2 rather than 83.2000 - four decimals is what the column holds, not what
   anyone wants to retype into a box. */
$plain = function ($v) {
    $d = (float) $v;
    if ($d <= 0) return '';
    return rtrim(rtrim(number_format($d, 4, '.', ''), '0'), '.');
};
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Exchange Rate</title>

        <!--
            jQuery in the head, not just at the foot: nav-menu emits its own
            inline $(document).ready() as it renders, so anything loaded after
            the header is already too late for it. Every other page does the
            same for the same reason.
        -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

        <style>
            .fx-tile{background:#fff;border-radius:3px;padding:18px 20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,.08);}
            .fx-tile .fx-num{font-size:30px;font-weight:600;line-height:1.1;color:#3bafda;}
            .fx-tile.notset .fx-num{font-size:18px;color:#d9822b;}
            .fx-tile .fx-lab{font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#98a6ad;margin-top:4px;}
            .fx-tile .fx-sub{font-size:11px;color:#b4bcc4;margin-top:2px;}
            .sec-head{margin:6px 0 14px;font-size:15px;font-weight:600;color:#4c5667;}
            .sec-head small{font-weight:400;color:#98a6ad;display:block;margin-top:3px;font-size:12px;}
            .pill{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;}
            .pill-ok{background:#e6f6ef;color:#1a9f68;}
            .pill-warn{background:#fdf1e3;color:#d9822b;}
            .muted{color:#98a6ad;}
            table.fxtable td, table.fxtable th{font-size:12.5px;vertical-align:middle !important;}
        </style>
    </head>

    <body class="fixed-left">

        <div id="wrapper">

            <!-- Navigation Bar-->
            <header id="topnav">
                <?php $this->load->view('common/nav-menu');?>
            </header>
            <!-- End Navigation Bar-->

            <div class="wrapper">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title text-center">Exchange Rate</h4>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($flash)): ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert <?= !empty($flash_ok) ? 'alert-success' : 'alert-danger' ?>">
                                    <?= $e($flash) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ===================== TODAY ===================== -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="sec-head">
                                Today &mdash; <?= $e(date('l, d M Y', strtotime($today))) ?>
                                <small>Rupees per unit of foreign currency.</small>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($rate)): ?>
                        <div class="row">
                            <?php foreach (array('USD' => 'usd', 'EUR' => 'eur', 'AED' => 'aed') as $code => $key): ?>
                                <div class="col-sm-4">
                                    <div class="fx-tile">
                                        <div class="fx-num">&#8377; <?= $e($money($rate[$key])) ?></div>
                                        <div class="fx-lab"><?= $e($code) ?></div>
                                        <div class="fx-sub">
                                            <?= $e(date('d M, g:i a', strtotime($rate['updated_at']))) ?>
                                            <?php if ((int) $rate['changed_count'] > 0): ?>
                                                &middot; corrected <?= (int) $rate['changed_count'] ?>&times;
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (trim((string) $rate['remark']) !== ''): ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="muted" style="margin-top:-8px;">
                                        <i class="fa fa-comment-o"></i> <?= $e($rate['remark']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="fx-tile notset">
                                    <div class="fx-num">Not set today</div>
                                    <div class="fx-sub">
                                        Nobody has entered today's rate yet. Yesterday's figure is
                                        not today's &mdash; it is in the table below, for reference only.
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ===================== ENTRY ===================== -->
                    <?php if (!empty($me['can_update'])): ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0 m-b-20">
                                        <?= empty($rate) ? "Enter today's rate" : "Correct today's rate" ?>
                                    </h4>

                                    <form method="post" action="<?= site_url('Exchange_rate/save') ?>" class="form-horizontal">
                                        <div class="row">
                                            <?php foreach (array(
                                                'usd' => array('USD', 'US Dollar'),
                                                'eur' => array('EUR', 'Euro'),
                                                'aed' => array('AED', 'UAE Dirham'),
                                            ) as $key => $meta): ?>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label><?= $e($meta[0]) ?> &mdash; <?= $e($meta[1]) ?></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">&#8377;</span>
                                                            <input type="text" class="form-control" name="<?= $e($key) ?>"
                                                                   inputmode="decimal" autocomplete="off"
                                                                   value="<?= $e(!empty($rate) ? $plain($rate[$key]) : '') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label>Remark (optional)</label>
                                                    <input type="text" class="form-control" name="remark" maxlength="190" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>&nbsp;</label><br>
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                        <?= empty($rate) ? 'Save rate' : 'Update rate' ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box">
                                    <p class="muted m-b-0">
                                        <i class="fa fa-lock"></i>
                                        Accounts enter this rate each morning.
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ===================== HISTORY ===================== -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-20">Last 30 days</h4>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover fxtable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>USD</th>
                                                <th>EUR</th>
                                                <th>AED</th>
                                                <th>Set by</th>
                                                <th>Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($history)): ?>
                                            <tr><td colspan="6" class="muted">Nothing recorded yet.</td></tr>
                                        <?php else: foreach ($history as $h): ?>
                                            <tr>
                                                <td>
                                                    <?= $e(date('d M Y', strtotime($h['rate_date']))) ?>
                                                    <?php if ($h['rate_date'] === $today): ?>
                                                        <span class="pill pill-ok">today</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>&#8377; <?= $e($money($h['usd'])) ?></td>
                                                <td>&#8377; <?= $e($money($h['eur'])) ?></td>
                                                <td>&#8377; <?= $e($money($h['aed'])) ?></td>
                                                <td><?= $e($h['updated_name']) ?></td>
                                                <td>
                                                    <?= $e($h['remark']) ?>
                                                    <?php if ((int) $h['changed_count'] > 0): ?>
                                                        <span class="pill pill-warn">corrected <?= (int) $h['changed_count'] ?>&times;</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php $this->load->view('common/footer');?>

                </div> <!-- end container -->
            </div>
            <!-- end wrapper -->

        </div>
        <!-- end #wrapper -->

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
