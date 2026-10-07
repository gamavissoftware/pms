<?php
/**
 * Attendance report - in the PMS's own chrome.
 *
 * Layout copied from exchange_rate/index.php (itself from app_log): shared
 * nav-menu + footer, the house bootstrap/core/components kit, jQuery in the
 * head because nav-menu runs its own $(document).ready() as it renders.
 *
 * The status chips are links, not client-side toggles, so the Excel export
 * - which is built from the same query string - always matches the table.
 */
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$t = function ($dt) { return $dt ? date('g:i a', strtotime($dt)) : ''; };

/* The current filters, with some overridden - for chip and export links. */
$q = function ($over = array()) use ($from, $to, $department, $user, $status) {
    $p = array_merge(array(
        'from'       => $from,
        'to'         => $to,
        'department' => $department ?: '',
        'user'       => $user ?: '',
        'status'     => $status,
    ), $over);
    return http_build_query(array_filter($p, function ($v) { return $v !== '' && $v !== null; }));
};

$chip_class = array(
    'PRESENT'    => 'ar-present',
    'ABSENT'     => 'ar-absent',
    'ON_DUTY'    => 'ar-duty',
    'GATE_PASS'  => 'ar-gate',
    'NOT_MARKED' => 'ar-none',
);

$total = array_sum($summary);
$is_range = $from !== $to;
$yesterday = date('Y-m-d', strtotime($today . ' -1 day'));
$month_start = date('Y-m-01', strtotime($today));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Attendance Report</title>

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
            .sec-head{margin:6px 0 14px;font-size:15px;font-weight:600;color:#4c5667;}
            .sec-head small{font-weight:400;color:#98a6ad;display:block;margin-top:3px;font-size:12px;}
            .muted{color:#98a6ad;}

            .ar-chips{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:18px;}
            .ar-chip{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;
                     background:#fff;border:1px solid #e3e8ec;color:#4c5667;font-weight:600;font-size:13px;
                     box-shadow:0 1px 2px rgba(0,0,0,.05);text-decoration:none !important;}
            .ar-chip:hover{border-color:#b9c3cb;color:#4c5667;}
            .ar-chip .n{display:inline-block;min-width:24px;padding:1px 7px;border-radius:10px;text-align:center;font-size:12px;}
            .ar-chip.zero{opacity:.45;pointer-events:none;}
            .ar-chip.on{color:#fff !important;}
            .ar-chip.on .n{background:rgba(255,255,255,.25) !important;color:#fff !important;}

            .ar-all .n{background:#eef1f3;color:#4c5667;}   .ar-all.on{background:#4c5667;border-color:#4c5667;}
            .ar-present .n{background:#e6f6ef;color:#1a9f68;} .ar-present.on{background:#1a9f68;border-color:#1a9f68;}
            .ar-absent .n{background:#fde8e8;color:#e0524e;}  .ar-absent.on{background:#e0524e;border-color:#e0524e;}
            .ar-duty .n{background:#e8f0fd;color:#3b7ddd;}    .ar-duty.on{background:#3b7ddd;border-color:#3b7ddd;}
            .ar-gate .n{background:#fdf1e3;color:#d9822b;}    .ar-gate.on{background:#d9822b;border-color:#d9822b;}
            .ar-none .n{background:#f1f1f1;color:#7a8690;}    .ar-none.on{background:#7a8690;border-color:#7a8690;}

            .pill{display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600;white-space:nowrap;}
            .pill.ar-present{background:#e6f6ef;color:#1a9f68;}
            .pill.ar-absent{background:#fde8e8;color:#e0524e;}
            .pill.ar-duty{background:#e8f0fd;color:#3b7ddd;}
            .pill.ar-gate{background:#fdf1e3;color:#d9822b;}
            .pill.ar-none{background:#f1f1f1;color:#7a8690;}
            .flag{display:inline-block;padding:1px 6px;border-radius:8px;font-size:10px;font-weight:600;margin-left:4px;}
            .flag-out{background:#fdf1e3;color:#d9822b;}
            .flag-mock{background:#fde8e8;color:#e0524e;}

            .ar-filters .form-group{margin-bottom:10px;}
            .ar-filters label{font-size:12px;color:#98a6ad;font-weight:600;margin-bottom:4px;}
            .ar-filters .select2-container{width:100% !important;}
            .ar-filters .select2-container .select2-selection--single{height:34px;border-color:#e3e3e3;}
            .ar-filters .select2-container .select2-selection--single .select2-selection__rendered{line-height:32px;}
            .ar-filters .select2-container .select2-selection--single .select2-selection__arrow{height:32px;}
            .ar-quick a{margin-right:10px;font-size:12px;}
            table.artable td, table.artable th{font-size:12.5px;vertical-align:middle !important;}
            .ar-plant{display:inline-block;margin-top:2px;padding:0 7px;border-radius:9px;font-size:10.5px;font-weight:600;color:#5b4a9c;background:#efeafc;white-space:nowrap;}
            .ar-plant-out{color:#8a5a00;background:#fdf1e3;}
        </style>
    </head>

    <body class="fixed-left">

        <div id="wrapper">

            <header id="topnav">
                <?php $this->load->view('common/nav-menu');?>
            </header>

            <div class="wrapper">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title text-center">Attendance Report</h4>
                            </div>
                        </div>
                    </div>

                    <?php if ($notice !== ''): ?>
                        <div class="row"><div class="col-sm-12">
                            <div class="alert alert-warning"><?= $e($notice) ?></div>
                        </div></div>
                    <?php endif; ?>

                    <!-- ===================== FILTERS ===================== -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box ar-filters">
                                <form method="get" action="<?= page_url . 'Attendance_report' ?>" id="ar-form">
                                    <input type="hidden" name="status" value="<?= $e($status) ?>">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-6">
                                            <div class="form-group">
                                                <label>From</label>
                                                <input type="date" class="form-control" name="from" value="<?= $e($from) ?>" max="<?= $e($today) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="form-group">
                                                <label>To</label>
                                                <input type="date" class="form-control" name="to" value="<?= $e($to) ?>" max="<?= $e($today) ?>">
                                            </div>
                                        </div>
                                        <?php if ($scope['scope'] !== 'SELF'): ?>
                                            <div class="col-md-3 col-sm-6">
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <select class="form-control" name="department" id="ar-dept">
                                                        <option value="">All departments</option>
                                                        <?php foreach ($departments as $id => $name): ?>
                                                            <option value="<?= (int) $id ?>" <?= (int) $id === (int) $department ? 'selected' : '' ?>><?= $e($name) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <div class="form-group">
                                                    <label>User</label>
                                                    <select class="form-control" name="user" id="ar-user">
                                                        <option value="">All users</option>
                                                        <?php foreach ($people as $p): ?>
                                                            <option value="<?= (int) $p['user_id'] ?>" data-dept="<?= (int) $p['department_id'] ?>"
                                                                <?= (int) $p['user_id'] === (int) $user ? 'selected' : '' ?>>
                                                                <?= $e($p['name']) ?><?= $p['department'] !== '' ? ' — ' . $e($p['department']) : '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label>&nbsp;</label><br>
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                    <i class="fa fa-filter"></i> Apply
                                                </button>
                                                <a class="btn btn-success waves-effect waves-light" href="<?= page_url . 'Attendance_report/export' . '?' . $q() ?>">
                                                    <i class="fa fa-file-excel-o"></i> Excel
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ar-quick">
                                        <span class="muted">Quick:</span>
                                        <a href="<?= page_url . 'Attendance_report' . '?' . $q(array('from' => $today, 'to' => $today, 'status' => '')) ?>">Today</a>
                                        <a href="<?= page_url . 'Attendance_report' . '?' . $q(array('from' => $yesterday, 'to' => $yesterday, 'status' => '')) ?>">Yesterday</a>
                                        <a href="<?= page_url . 'Attendance_report' . '?' . $q(array('from' => date('Y-m-d', strtotime($today . ' -6 days')), 'to' => $today, 'status' => '')) ?>">Last 7 days</a>
                                        <a href="<?= page_url . 'Attendance_report' . '?' . $q(array('from' => $month_start, 'to' => $today, 'status' => '')) ?>">This month</a>
                                        <a href="<?= page_url . 'Attendance_report' ?>" class="muted">Reset</a>
                                        <span class="muted pull-right"><?= $e($scope['label']) ?> &middot; <?= count($people) ?> people</span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== CHIPS ===================== -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="sec-head">
                                <?= $is_range
                                    ? $e(date('d M Y', strtotime($from))) . ' &ndash; ' . $e(date('d M Y', strtotime($to)))
                                    : $e(date('l, d M Y', strtotime($from))) ?>
                                <small>
                                    <?= $is_range ? 'Counts are person-days across the range. ' : '' ?>
                                    Click a chip to show only that status; click it again to clear.
                                </small>
                            </div>
                            <div class="ar-chips">
                                <a class="ar-chip ar-all <?= $status === '' ? 'on' : '' ?>" href="<?= page_url . 'Attendance_report' . '?' . $q(array('status' => '')) ?>">
                                    All <span class="n"><?= (int) $total ?></span>
                                </a>
                                <?php foreach ($labels as $k => $label):
                                    $n = isset($summary[$k]) ? (int) $summary[$k] : 0;
                                    $on = $status === $k; ?>
                                    <a class="ar-chip <?= $e(isset($chip_class[$k]) ? $chip_class[$k] : 'ar-none') ?> <?= $on ? 'on' : '' ?> <?= ($n === 0 && !$on) ? 'zero' : '' ?>"
                                       href="<?= page_url . 'Attendance_report' . '?' . $q(array('status' => $on ? '' : $k)) ?>">
                                        <?= $e($label) ?> <span class="n"><?= $n ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== TABLE ===================== -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <div class="row m-b-10">
                                    <div class="col-sm-8">
                                        <h4 class="header-title m-t-0">
                                            <?= count($rows) ?> row<?= count($rows) === 1 ? '' : 's' ?>
                                            <?php if ($status !== ''): ?>
                                                <span class="muted" style="font-weight:400;">&middot; <?= $e($labels[$status]) ?> only</span>
                                            <?php endif; ?>
                                        </h4>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-sm" id="ar-search" placeholder="Search name, code, department&hellip;">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover artable" id="ar-table">
                                        <thead>
                                            <tr>
                                                <?php if ($is_range): ?><th>Date</th><?php endif; ?>
                                                <th>#</th>
                                                <th>Emp Code</th>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th>Marked at</th>
                                                <th>First in</th>
                                                <th>Last out</th>
                                                <th>Worked</th>
                                                <th>Marked from</th>
                                                <th>Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($rows)): ?>
                                            <tr><td colspan="12" class="muted">No one matches these filters.</td></tr>
                                        <?php else: $i = 0; foreach ($rows as $r): $i++;
                                            $cls = isset($chip_class[$r['status']]) ? $chip_class[$r['status']] : 'ar-none';
                                            $marked = $r['status'] !== 'NOT_MARKED'; ?>
                                            <tr class="ar-row">
                                                <?php if ($is_range): ?><td style="white-space:nowrap;"><?= $e(date('d M Y, D', strtotime($r['date']))) ?></td><?php endif; ?>
                                                <td class="muted"><?= $i ?></td>
                                                <td><?= $e($r['emp_code']) ?></td>
                                                <td>
                                                    <?= $e($r['name']) ?>
                                                    <?php if (!empty($r['plant'])): ?>
                                                        <br><span class="ar-plant<?= $r['plant'] === 'Outside' ? ' ar-plant-out' : '' ?>" title="Where the attendance was marked"><?= $e($r['plant']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $e($r['department']) ?></td>
                                                <td>
                                                    <span class="pill <?= $e($cls) ?>"><?= $e($r['status_label']) ?></span>
                                                    <?php if ($r['changed_count'] > 0): ?>
                                                        <span class="flag flag-out" title="Mark changed <?= (int) $r['changed_count'] ?> time(s)">&#8635;<?= (int) $r['changed_count'] ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $e($t($r['marked_at'])) ?></td>
                                                <td><?= $e($t($r['first_in'])) ?></td>
                                                <td><?= $e($t($r['last_out'])) ?></td>
                                                <td><?= $r['worked'] > 0 ? sprintf('%d:%02d', intdiv($r['worked'], 60), $r['worked'] % 60) : '' ?></td>
                                                <td>
                                                    <?php if ($marked): ?>
                                                        <?php if ($r['plant'] === 'Outside'): ?>
                                                            Outside
                                                            <span class="flag flag-out" title="Distance to the nearest plant"><?= $e($r['plant_distance_m'] >= 1000 ? number_format($r['plant_distance_m'] / 1000, 1) . ' km' : $r['plant_distance_m'] . ' m') ?> from <?= $e($r['nearest_plant']) ?></span>
                                                        <?php elseif ($r['plant'] !== ''): ?>
                                                            Marked from <?= $e($r['plant']) ?>
                                                        <?php else: ?>
                                                            <span class="muted">No location</span>
                                                        <?php endif; ?>
                                                        <?php if ($r['is_mocked']): ?>
                                                            <span class="flag flag-mock" title="The phone reported a mock location">Mock GPS</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $e($r['remark']) ?></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

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
            var $dept = $('#ar-dept');
            var $user = $('#ar-user');
            var form  = document.getElementById('ar-form');

            /* User list follows the department: pick Accounts, see only
               Accounts people. Select2 ignores hidden <option>s, so the list
               is rebuilt from a copy of the full one; a chosen user outside
               the department is cleared. */
            var $allUsers = $user.find('option').clone();
            function syncUsers() {
                if (!$user.length) return;
                var d = $dept.val();
                var keep = $user.val();
                $user.empty().append($allUsers.clone().filter(function () {
                    return !this.value || !d || $(this).attr('data-dept') === d;
                }));
                $user.val($user.find('option[value="' + keep + '"]').length ? keep : '');
            }

            if ($.fn.select2) {
                $dept.select2({ placeholder: 'All departments', allowClear: true, width: '100%' });
                $user.select2({ placeholder: 'All users', allowClear: true, width: '100%' });
            }

            /* Changing a date or a drop-down clears the status chip, so a
               filter chosen for one day cannot silently hide people on the
               next - same rule the app follows on reload. jQuery-bound,
               because Select2 announces its changes through jQuery. */
            function clearStatus() { form.elements['status'].value = ''; }
            $dept.on('change', function () { syncUsers(); $user.trigger('change.select2'); clearStatus(); });
            $user.on('change', clearStatus);
            $(form).find('input[type=date]').on('change', clearStatus);
            syncUsers();
            $user.trigger('change.select2');

            /* In-page search over the rendered rows. */
            var box = document.getElementById('ar-search');
            var rows = document.querySelectorAll('#ar-table tr.ar-row');
            box.addEventListener('input', function () {
                var s = box.value.toLowerCase().trim();
                for (var i = 0; i < rows.length; i++) {
                    rows[i].style.display = (!s || rows[i].textContent.toLowerCase().indexOf(s) !== -1) ? '' : 'none';
                }
            });
        })();
        </script>

    </body>
</html>
