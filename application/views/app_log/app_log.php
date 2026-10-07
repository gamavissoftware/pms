<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>App Log</title>

        <!--
            jQuery has to be here, not just at the foot of the page: nav-menu
            emits its own inline $(document).ready() as it renders, so anything
            loaded after the header is already too late for it. Every other page
            in the PMS loads jQuery in the head for the same reason. Loading it
            again at the foot is harmless - DataTables is pulled in after that
            second copy, so it binds to the jQuery that survives.
        -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

        <style>
            .stat-tile{background:#fff;border-radius:3px;padding:18px 20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,.08);}
            .stat-tile .stat-num{font-size:30px;font-weight:600;line-height:1.1;color:#3bafda;}
            .stat-tile.warn .stat-num{color:#d9822b;}
            .stat-tile .stat-lab{font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#98a6ad;margin-top:4px;}
            .stat-tile .stat-sub{font-size:11px;color:#b4bcc4;margin-top:2px;}
            .sec-head{margin:6px 0 14px;font-size:15px;font-weight:600;color:#4c5667;}
            .sec-head small{font-weight:400;color:#98a6ad;display:block;margin-top:3px;font-size:12px;}
            .pill{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;}
            .pill-ok{background:#e6f6ef;color:#1a9f68;}
            .pill-old{background:#fdf1e3;color:#d9822b;}
            .pill-none{background:#f1f2f4;color:#98a6ad;}
            .pill-dead{background:#fdecea;color:#d9534f;}
            .pill-android{background:#e8f5e9;color:#2e7d32;}
            .pill-ios{background:#eceff1;color:#455a64;}
            .plat-chart{position:relative;height:250px;}
            .plat-dot{display:inline-block;width:10px;height:10px;border-radius:50%;margin-right:7px;vertical-align:middle;}
            .plat-none{padding:30px 10px;text-align:center;}
            .muted{color:#98a6ad;}
            .ago{display:block;font-size:11px;color:#b4bcc4;}
            table.applog td, table.applog th{font-size:12.5px;vertical-align:middle !important;}
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
                                <h4 class="page-title text-center">App Log</h4>
                            </div>
                        </div>
                    </div>

                    <?php
                        $latest_v = $latest['version'];
                        $latest_b = $latest['build'];
                        $not_installed = count($without_app);

                        /* Guarded, because a view can legitimately be loaded more than once per request. */
                        if (!function_exists('applog_is_current')) {
                            /* A device counts as current only if both halves match what the update gate advertises. */
                            function applog_is_current($v, $b, $latest_v, $latest_b) {
                                if ($latest_v === '' || $v === '') return false;
                                return ($v === $latest_v && (string) $b === (string) $latest_b);
                            }
                        }

                        if (!function_exists('applog_ago')) {
                            /* "3h ago" under the real timestamp - the exact time is what gets read, this is the glance. */
                            function applog_ago($ts) {
                                if (empty($ts) || $ts === '0000-00-00 00:00:00') return '';
                                $diff = time() - strtotime($ts);
                                if ($diff < 3600)  return 'less than an hour ago';
                                if ($diff < 86400) return floor($diff / 3600) . 'h ago';
                                return floor($diff / 86400) . 'd ago';
                            }
                        }

                        if (!function_exists('applog_platform_key')) {
                            /* The handset sends this string, so it is normalised here rather than trusted. */
                            function applog_platform_key($raw) {
                                $p = strtolower(trim((string) $raw));
                                if ($p === '') return 'unknown';
                                if (strpos($p, 'ios') === 0 || in_array($p, array('iphone', 'ipad', 'darwin'), true)) return 'ios';
                                if (strpos($p, 'android') === 0) return 'android';
                                return 'unknown';
                            }
                        }

                        if (!function_exists('applog_platform_label')) {
                            function applog_platform_label($raw) {
                                $k = applog_platform_key($raw);
                                if ($k === 'ios') return 'iOS';
                                if ($k === 'android') return 'Android';
                                return 'Not reported';
                            }
                        }

                        if (!function_exists('applog_platform_pill')) {
                            function applog_platform_pill($raw) {
                                $k = applog_platform_key($raw);
                                if ($k === 'ios') return 'pill-ios';
                                if ($k === 'android') return 'pill-android';
                                return 'pill-none';
                            }
                        }

                        if (!function_exists('applog_platform_colour')) {
                            /* One source for the slice colour and the dot beside its row, so the
                               chart and the list can never disagree about which is which. */
                            function applog_platform_colour($key) {
                                if ($key === 'ios')     return '#607d8b';
                                if ($key === 'android') return '#43a047';
                                return '#cfd8dc';
                            }
                        }
                    ?>

                    <!-- ============ headline counts ============ -->
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile">
                                <div class="stat-num"><?php echo (int) $total_installs; ?></div>
                                <div class="stat-lab">Installs</div>
                                <div class="stat-sub"><?php echo (int) $total_users; ?> people</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile warn">
                                <div class="stat-num"><?php echo (int) $not_installed; ?></div>
                                <div class="stat-lab">Not installed</div>
                                <div class="stat-sub">active staff with no install</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile">
                                <div class="stat-num"><?php echo (int) $active_today; ?></div>
                                <div class="stat-lab">Opened today</div>
                                <div class="stat-sub"><?php echo (int) $active_week; ?> this week, <?php echo (int) $active_month; ?> this month</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile">
                                <div class="stat-num">
                                    <?php echo $latest_b !== '' ? html_escape($latest_b) : '-'; ?>
                                </div>
                                <div class="stat-lab">Current build</div>
                                <div class="stat-sub">
                                    <?php echo $latest_v !== '' ? 'v' . html_escape($latest_v) . ' - what the update gate serves' : 'App/version.json unreadable'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ android vs ios ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <div class="sec-head">
                                    Android vs iOS
                                    <small>
                                        Every phone reports which platform it is on when it opens the app, so this
                                        counts installs that have actually reported &mdash; not staff, and not
                                        downloads.
                                    </small>
                                </div>

                                <div class="row">
                                    <div class="col-md-5">
                                        <?php if ((int) $platforms['total'] > 0): ?>
                                            <div class="plat-chart">
                                                <canvas id="platform-pie"></canvas>
                                            </div>
                                        <?php else: ?>
                                            <div class="plat-none muted">Nothing has reported a platform yet.</div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-7 table-responsive">
                                        <table class="table table-striped table-bordered applog">
                                            <thead>
                                                <tr>
                                                    <th>Platform</th>
                                                    <th>Installs</th>
                                                    <th>People</th>
                                                    <th>Opened this week</th>
                                                    <th>Share</th>
                                                    <th>Last seen</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($platforms['rows'] as $pl): ?>
                                                <tr>
                                                    <td>
                                                        <span class="plat-dot" style="background:<?php echo applog_platform_colour($pl['key']); ?>;"></span>
                                                        <strong><?php echo html_escape($pl['label']); ?></strong>
                                                    </td>
                                                    <td><?php echo (int) $pl['installs']; ?></td>
                                                    <td><?php echo (int) $pl['people']; ?></td>
                                                    <td><?php echo (int) $pl['active_week']; ?></td>
                                                    <td><?php echo $pl['installs'] > 0 ? html_escape($pl['share']) . '%' : '<span class="muted">-</span>'; ?></td>
                                                    <td>
                                                        <?php if ($pl['newest']): ?>
                                                            <?php echo date('d M Y, h:i A', strtotime($pl['newest'])); ?>
                                                            <span class="ago"><?php echo applog_ago($pl['newest']); ?></span>
                                                        <?php else: ?>
                                                            <span class="muted">never</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th><?php echo (int) $platforms['total']; ?></th>
                                                    <th colspan="4"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <span class="muted" style="font-size:11px;">
                                            An install counts once per device. A zero row is a real answer &mdash; it
                                            means nobody is on that platform yet, not that the figure is missing.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ entries made from the app ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="sec-head">
                                Entries made from the app
                                <small>
                                    Every record created or changed through the mobile API. The website never
                                    goes through it, so anything counted here was definitely done on a phone.
                                    Chat messages are recorded but kept out of these numbers.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile">
                                <div class="stat-num"><?php echo (int) $entries['today']; ?></div>
                                <div class="stat-lab">Entries today</div>
                                <div class="stat-sub"><?php echo (int) $entries['people_today']; ?> people entering</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile">
                                <div class="stat-num"><?php echo (int) $entries['week']; ?></div>
                                <div class="stat-lab">Last 7 days</div>
                                <div class="stat-sub"><?php echo (int) $entries['month']; ?> in the last 30</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile">
                                <div class="stat-num"><?php echo (int) $entries['total']; ?></div>
                                <div class="stat-lab">Entries all time</div>
                                <div class="stat-sub">
                                    <?php echo $entries['ready'] ? 'since recording began 10 Sep 2026' : 'nothing recorded yet'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-tile">
                                <div class="stat-num"><?php echo (int) $entries['people_month']; ?></div>
                                <div class="stat-lab">People entering</div>
                                <div class="stat-sub">last 30 days, <?php echo (int) $entries['chats']; ?> chat messages apart from this</div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ send a push ============ -->
                    <?php $flash = $this->session->flashdata('applog_msg'); ?>
                    <?php if ($flash): ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert <?php echo $this->session->flashdata('applog_ok') ? 'alert-success' : 'alert-warning'; ?>">
                                    <?php echo html_escape($flash); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <div class="sec-head">
                                    Send a push
                                    <small>
                                        Goes to phones directly. There is no undo and no recall, so the
                                        audience is picked deliberately and the button asks first.
                                    </small>
                                </div>

                                <form id="applog-push" method="post" action="<?php echo page_url;?>App_log/broadcast">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>What to send</label>
                                                <select name="push_type" id="push_type" class="form-control">
                                                    <option value="custom">Custom message</option>
                                                    <option value="app_update">App update reminder</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Who gets it</label>
                                                <select name="audience" id="audience" class="form-control">
                                                    <option value="me">Just me &mdash; test (<?php echo (int) $reach['me']['devices']; ?> device<?php echo $reach['me']['devices'] == 1 ? '' : 's'; ?>)</option>
                                                    <option value="all">Everyone with the app (<?php echo (int) $reach['all']['people']; ?> people, <?php echo (int) $reach['all']['devices']; ?> devices)</option>
                                                    <option value="behind">Only people behind the current build (<?php echo (int) $reach['behind']['people']; ?> people, <?php echo (int) $reach['behind']['devices']; ?> devices)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                        Send push
                                                    </button>
                                                    <span class="muted" style="margin-left:10px;font-size:12px;">
                                                        Send to yourself first &mdash; it is the only way to see how it lands.
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="custom-fields">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" name="title" id="push_title" class="form-control" maxlength="150" placeholder="Shubham Pack">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Message</label>
                                                <input type="text" name="message" id="push_message" class="form-control" maxlength="400" placeholder="What do you want them to know?">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="update-preview" style="display:none;">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>What they will see</label>
                                                <div class="well" style="margin-bottom:0;padding:12px 15px;">
                                                    <strong>Update available</strong><br>
                                                    <?php if ($latest_v !== ''): ?>
                                                        Version <?php echo html_escape($latest_v); ?> is ready. Open the app to update.
                                                    <?php else: ?>
                                                        A new version is ready. Open the app to update.
                                                    <?php endif; ?>
                                                </div>
                                                <span class="muted" style="font-size:11px;">
                                                    The version is read from version.json, so this can never advertise a build the download page is not serving.
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ============ builds in the field ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    Builds in the field
                                    <small>Which build each phone is actually running, against the <?php echo $latest_v !== '' ? 'v' . html_escape($latest_v) . ' (build ' . html_escape($latest_b) . ')' : 'build'; ?> the update gate serves.</small>
                                </div>
                                <table class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Version</th>
                                            <th>Build no.</th>
                                            <th>Devices</th>
                                            <th>People</th>
                                            <th>Last seen</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($versions)): ?>
                                        <tr><td colspan="6" class="text-center muted">No device has reported a build yet.</td></tr>
                                    <?php else: foreach ($versions as $v): ?>
                                        <tr>
                                            <td><strong><?php echo html_escape($v->app_version); ?></strong></td>
                                            <td><?php echo $v->build_number !== '' ? html_escape($v->build_number) : '<span class="muted">-</span>'; ?></td>
                                            <td><?php echo (int) $v->devices; ?></td>
                                            <td><?php echo (int) $v->people; ?></td>
                                            <td>
                                                <?php echo date('d M Y, h:i A', strtotime($v->newest)); ?>
                                                <span class="ago"><?php echo applog_ago($v->newest); ?></span>
                                            </td>
                                            <td>
                                                <?php if (applog_is_current($v->app_version, $v->build_number, $latest_v, $latest_b)): ?>
                                                    <span class="pill pill-ok">current</span>
                                                <?php else: ?>
                                                    <span class="pill pill-old">behind</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ who has installed ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    Who has installed it &mdash; <?php echo (int) $total_users; ?> people, <?php echo (int) $total_installs; ?> installs
                                    <small>Sorted by last used, most recent first.</small>
                                </div>
                                <table id="applog-people" class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Version</th>
                                            <th>Build no.</th>
                                            <th>Platform</th>
                                            <th>Installed on</th>
                                            <th>Last used</th>
                                            <th>Opens</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($people as $p): ?>
                                        <tr>
                                            <td>
                                                <?php echo html_escape($p->name ? $p->name : ('user #' . $p->user_id)); ?>
                                                <?php if ((int) $p->user_status !== 1): ?>
                                                    <span class="pill pill-dead">left</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo html_escape($p->department ? $p->department : '-'); ?></td>
                                            <td><?php echo html_escape($p->app_version ? $p->app_version : 'unknown'); ?></td>
                                            <td><?php echo $p->build_number !== '' ? html_escape($p->build_number) : '<span class="muted">-</span>'; ?></td>
                                            <td>
                                                <span class="pill <?php echo applog_platform_pill($p->platform); ?>"><?php echo html_escape(applog_platform_label($p->platform)); ?></span>
                                                <span class="ago"><?php echo html_escape($p->os_version ? $p->os_version : '-'); ?></span>
                                            </td>
                                            <td data-order="<?php echo strtotime($p->first_seen); ?>"><?php echo date('d M Y, h:i A', strtotime($p->first_seen)); ?></td>
                                            <td data-order="<?php echo strtotime($p->last_seen); ?>">
                                                <?php echo date('d M Y, h:i A', strtotime($p->last_seen)); ?>
                                                <span class="ago"><?php echo applog_ago($p->last_seen); ?></span>
                                            </td>
                                            <td><?php echo (int) $p->open_count; ?></td>
                                            <td>
                                                <?php if (applog_is_current($p->app_version, $p->build_number, $latest_v, $latest_b)): ?>
                                                    <span class="pill pill-ok">current</span>
                                                <?php else: ?>
                                                    <span class="pill pill-old">behind</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ who has not ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    Who has not installed it &mdash; <?php echo (int) $not_installed; ?> people
                                    <small>
                                        Active staff with no install reporting. "Had it before" means a push token
                                        exists, so the app was on their phone at some point but is still on a build
                                        too old to report itself.
                                    </small>
                                </div>
                                <table id="applog-without" class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>History</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($without_app as $w): ?>
                                        <tr>
                                            <td><?php echo html_escape($w->name ? $w->name : ('user #' . $w->user_id)); ?></td>
                                            <td><?php echo html_escape($w->department ? $w->department : '-'); ?></td>
                                            <td>
                                                <?php if ((int) $w->has_push_token === 1): ?>
                                                    <span class="pill pill-old">had it before, old build</span>
                                                <?php else: ?>
                                                    <span class="pill pill-none">never installed</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ recent opens ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    Recent activity
                                    <small>Last 30 days. Repeat opens within an hour count once.</small>
                                </div>
                                <table id="applog-recent" class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Date &amp; time</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Version</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($recent as $r): ?>
                                        <tr>
                                            <td data-order="<?php echo strtotime($r->seen_at); ?>"><?php echo date('d M Y, h:i A', strtotime($r->seen_at)); ?></td>
                                            <td><?php echo html_escape($r->name ? $r->name : '-'); ?></td>
                                            <td><?php echo html_escape($r->department ? $r->department : '-'); ?></td>
                                            <td><?php echo html_escape($r->app_version ? $r->app_version : 'unknown'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ app entries: where and who ============ -->
                    <?php
                        if (!function_exists('applog_action_pill')) {
                            /* Reuses the pills already on this page - green for a new
                               record, orange for a change, red for a delete. */
                            function applog_action_pill($action) {
                                switch ($action) {
                                    case 'create': return 'pill-ok';
                                    case 'update': return 'pill-old';
                                    case 'delete': return 'pill-dead';
                                }
                                return 'pill-none';
                            }
                        }
                    ?>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    App entries by module
                                    <small>Last 30 days. Which parts of the PMS are actually being used from the phone.</small>
                                </div>
                                <table class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Module</th>
                                            <th>Entries</th>
                                            <th>People</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($entries['modules'])): ?>
                                        <tr>
                                            <td colspan="3" class="muted">
                                                No entries recorded in the last 30 days.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($entries['modules'] as $m): ?>
                                            <tr>
                                                <td><?php echo html_escape($m->module); ?></td>
                                                <td><?php echo (int) $m->entries; ?></td>
                                                <td><?php echo (int) $m->people; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    App entries by person
                                    <small>Last 30 days. Anyone missing from here is still working on the website only.</small>
                                </div>
                                <table id="applog-entrypeople" class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Entries</th>
                                            <th>Last entry</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($entries['people'] as $p): ?>
                                        <tr>
                                            <td><?php echo html_escape(trim($p->name) !== '' ? $p->name : ('user #' . (int) $p->user_id)); ?></td>
                                            <td><?php echo html_escape($p->department ? $p->department : '-'); ?></td>
                                            <td><?php echo (int) $p->entries; ?></td>
                                            <td data-order="<?php echo strtotime($p->last_at); ?>">
                                                <?php echo date('d M Y, h:i A', strtotime($p->last_at)); ?>
                                                <span class="ago"><?php echo applog_ago($p->last_at); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ every app entry, newest first ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    Recent entries from the app
                                    <small>
                                        Last 300. "Record" is the row that was written, so you can check any one of
                                        them against the module itself. A blank record number means the record was
                                        newly created and its id could not be read back with certainty.
                                    </small>
                                </div>
                                <table id="applog-entries" class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Date &amp; time</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Module</th>
                                            <th>Action</th>
                                            <th>Record</th>
                                            <th>What was sent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($entries['recent'] as $e): ?>
                                        <tr>
                                            <td data-order="<?php echo strtotime($e->created_at); ?>">
                                                <?php echo date('d M Y, h:i A', strtotime($e->created_at)); ?>
                                                <span class="ago"><?php echo applog_ago($e->created_at); ?></span>
                                            </td>
                                            <td><?php echo html_escape(trim($e->name) !== '' ? $e->name : '-'); ?></td>
                                            <td><?php echo html_escape($e->department ? $e->department : '-'); ?></td>
                                            <td>
                                                <?php echo html_escape($e->module); ?>
                                                <span class="ago"><?php echo html_escape($e->endpoint); ?></span>
                                            </td>
                                            <td>
                                                <span class="pill <?php echo applog_action_pill($e->action); ?>">
                                                    <?php echo html_escape($e->action); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo html_escape($e->primary_table); ?>
                                                <span class="ago">
                                                    <?php
                                                        if ((int) $e->record_id > 0) {
                                                            echo '#' . (int) $e->record_id;
                                                        } elseif ($e->ref_key !== '') {
                                                            echo html_escape($e->ref_key . ' ' . $e->ref_id);
                                                        } else {
                                                            echo 'new record';
                                                        }
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="muted"><?php echo html_escape(mb_substr((string) $e->payload, 0, 120)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ what has been sent ============ -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <div class="sec-head">
                                    Pushes sent from here
                                    <small>Last 20. Every send is recorded, including who sent it.</small>
                                </div>
                                <table class="table table-striped table-bordered applog">
                                    <thead>
                                        <tr>
                                            <th>Date &amp; time</th>
                                            <th>Sent by</th>
                                            <th>Audience</th>
                                            <th>Title</th>
                                            <th>Message</th>
                                            <th>Delivered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($broadcasts)): ?>
                                        <tr><td colspan="6" class="text-center muted">Nothing has been sent from this page yet.</td></tr>
                                    <?php else: foreach ($broadcasts as $b): ?>
                                        <tr>
                                            <td><?php echo date('d M Y, h:i A', strtotime($b->created_at)); ?></td>
                                            <td><?php echo html_escape($b->sent_by_name ? $b->sent_by_name : ('user #' . $b->sent_by)); ?></td>
                                            <td>
                                                <?php
                                                    $aud = array('me' => 'test to self', 'all' => 'everyone', 'behind' => 'behind current build');
                                                    echo html_escape(isset($aud[$b->audience]) ? $aud[$b->audience] : $b->audience);
                                                ?>
                                                <?php if ($b->push_type === 'app_update'): ?>
                                                    <span class="pill pill-old">update</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo html_escape($b->title); ?></td>
                                            <td><?php echo html_escape($b->message); ?></td>
                                            <td>
                                                <?php echo (int) $b->sent; ?> of <?php echo (int) $b->devices; ?>
                                                <?php if ((int) $b->failed > 0): ?>
                                                    <span class="pill pill-dead"><?php echo (int) $b->failed; ?> failed</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
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

        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>

        <!--
            Chart.js from the PMS's own assets rather than a CDN: the pie is
            the only thing on this page that needs it, and a page about who
            can reach the app should not itself depend on reaching the
            internet. If it fails to load the list below still stands on its
            own - see the guard in the init.
        -->
        <script src="<?php echo assets_url;?>plugins/chart.js/chart.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {

                /* ---- android vs ios pie ---- */
                var platformData = <?php echo json_encode(array_map(function ($r) {
                    return array(
                        'label'    => $r['label'],
                        'installs' => (int) $r['installs'],
                        'colour'   => applog_platform_colour($r['key']),
                    );
                }, $platforms['rows'])); ?>;

                var pie = document.getElementById('platform-pie');

                /* the table is the source of truth; the chart is the glance,
                   so a missing library or an empty set is not an error here */
                if (pie && window.Chart && platformData.length) {
                    new Chart(pie.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: platformData.map(function (d) { return d.label; }),
                            datasets: [{
                                data: platformData.map(function (d) { return d.installs; }),
                                backgroundColor: platformData.map(function (d) { return d.colour; }),
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: { position: 'bottom' },
                            tooltips: {
                                callbacks: {
                                    label: function (item, data) {
                                        var n = data.datasets[0].data[item.index];
                                        var total = data.datasets[0].data.reduce(function (a, b) { return a + b; }, 0);
                                        var pct = total ? Math.round(n * 1000 / total) / 10 : 0;
                                        return data.labels[item.index] + ': ' + n + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    });
                } else if (pie) {
                    pie.parentNode.innerHTML = '<div class="plat-none muted">Chart could not be drawn &mdash; the figures are in the table.</div>';
                }

                /**
                 * An empty table is left to DataTables' own emptyTable message.
                 * A placeholder <tr><td colspan=N> is what threw the warning
                 * before: DataTables counts that row as one cell, disagrees with
                 * the header, and aborts.
                 */
                var opts = {
                    "paging": true,
                    "pageLength": 25,
                    "lengthChange": false,
                    "info": false,
                    "order": [],
                    "language": {
                        "emptyTable": "Nothing reported yet - this fills in as staff move to the current build.",
                        "zeroRecords": "No match."
                    }
                };

                $('#applog-people').DataTable($.extend({}, opts, {
                    "order": [[6, "desc"]],
                    "language": $.extend({}, opts.language, {
                        "emptyTable": "No install has reported yet. Staff appear here once they are on the current build."
                    })
                }));

                $('#applog-without').DataTable($.extend({}, opts, {
                    "language": $.extend({}, opts.language, {
                        "emptyTable": "Everyone active has the app."
                    })
                }));

                /* An update push writes its own wording, so the free-text fields go away. */
                function syncPushForm() {
                    var isUpdate = $('#push_type').val() === 'app_update';
                    $('#custom-fields').toggle(!isUpdate);
                    $('#update-preview').toggle(isUpdate);
                }
                $('#push_type').on('change', syncPushForm);
                syncPushForm();

                /**
                 * The confirm names the audience out loud. "Send to everyone"
                 * is a different decision from "send to me", and the button
                 * looks identical for both.
                 */
                $('#applog-push').on('submit', function () {
                    var audience = $('#audience option:selected').text().trim();
                    var what = $('#push_type').val() === 'app_update'
                        ? 'the app update reminder'
                        : 'this message';
                    return window.confirm(
                        'Send ' + what + ' to:\n\n' + audience +
                        '\n\nPhones get it immediately and it cannot be recalled.'
                    );
                });

                $('#applog-recent').DataTable($.extend({}, opts, {
                    "order": [[0, "desc"]],
                    "language": $.extend({}, opts.language, {
                        "emptyTable": "No opens recorded yet."
                    })
                }));

                $('#applog-entrypeople').DataTable($.extend({}, opts, {
                    "order": [[2, "desc"]],
                    "language": $.extend({}, opts.language, {
                        "emptyTable": "Nobody has entered anything from the app in the last 30 days."
                    })
                }));

                $('#applog-entries').DataTable($.extend({}, opts, {
                    "order": [[0, "desc"]],
                    "language": $.extend({}, opts.language, {
                        "emptyTable": "No entries from the app yet."
                    })
                }));
            });
        </script>

    </body>
</html>
