<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Admin Friday Reset Utility</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <style>
        .reset-shell {
            margin-top: 24px;
        }
        .reset-hero {
            background: #ffffff;
            border: 1px solid #dbe6f3;
            border-radius: 16px;
            padding: 22px 24px;
            box-shadow: 0 12px 32px rgba(16, 42, 67, 0.06);
        }
        .reset-hero h4 {
            margin: 0 0 8px;
            font-size: 26px;
            font-weight: 700;
            color: #17324d;
        }
        .reset-hero p {
            margin: 0;
            color: #5f7388;
        }
        .reset-chip {
            display: inline-block;
            margin-top: 14px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eef6ff;
            color: #1f5ea8;
            font-weight: 600;
        }
        .reset-chip-warning {
            background: #fff6e6;
            color: #996300;
            margin-left: 8px;
        }
        .reset-card {
            margin-top: 20px;
            background: #ffffff;
            border: 1px solid #dbe6f3;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 12px 32px rgba(16, 42, 67, 0.06);
        }
        .reset-table {
            margin-top: 14px;
            border: 1px solid #d7e0ea;
        }
        .reset-table thead th {
            background: #f5f8fc;
            color: #17324d;
            border-bottom: 1px solid #d7e0ea;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .04em;
        }
        .reset-table th,
        .reset-table td {
            vertical-align: middle !important;
            border-color: #d7e0ea !important;
        }
        .empty-state {
            padding: 26px;
            text-align: center;
            color: #5f7388;
        }
        .df-meta {
            color: #6e8297;
            font-size: 12px;
            margin-top: 4px;
        }
        .btn-toolbar-top {
            margin-top: 16px;
        }
    </style>
</head>
<body>
<header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
</header>

<div class="wrapper">
    <div class="container">
        <div class="reset-shell">
            <div class="reset-hero">
                <div class="pull-right">
                    <a href="<?php echo page_url; ?>Dashboard" class="btn btn-default btn-sm">Back to Dashboard</a>
                </div>
                <h4>Admin Friday Reset Utility</h4>
                <p>Use this backup utility only for legacy running DFs that were created before Friday automation was enabled. New DFs and future MOM updates already move automatically to Friday.</p>
                <span class="reset-chip">Reset target: <?php echo date('d-M-Y', strtotime($target_friday)); ?></span>
                <span class="reset-chip reset-chip-warning">Admin use only</span>
                <div style="clear: both;"></div>
            </div>

            <?php if ($this->session->flashdata('message')) { ?>
                <div style="margin-top:16px;"><?php echo $this->session->flashdata('message'); ?></div>
            <?php } ?>

            <div class="reset-card">
                <form method="post" action="<?php echo page_url; ?>Task/apply_dfmeeting_friday_reset">
                    <div class="btn-toolbar-top">
                        <button type="submit" class="btn btn-primary">Apply Friday Reset</button>
                    </div>

                    <?php if (!empty($meeting_rows)) { ?>
                        <div class="table-responsive">
                            <table class="table table-bordered reset-table">
                                <thead>
                                    <tr>
                                        <th style="width:56px; text-align:center;">
                                            <input type="checkbox" id="check-all-reset">
                                        </th>
                                        <th>DF</th>
                                        <th>Customer</th>
                                        <th>Assigned User</th>
                                        <th>Current Schedule</th>
                                        <th>Friday Reset</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($meeting_rows as $row) { ?>
                                        <tr>
                                            <td style="text-align:center;">
                                                <input type="checkbox" class="reset-task-check" name="task_record_ids[]" value="<?php echo (int) $row->id; ?>" checked>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars((string) $row->df_no, ENT_QUOTES, 'UTF-8'); ?></strong>
                                                <div class="df-meta"><?php echo htmlspecialchars((string) $row->df_description, ENT_QUOTES, 'UTF-8'); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars((string) $row->company_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars(trim((string) $row->first_name . ' ' . $row->last_name), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo date('d-M-Y', strtotime($row->start_date)); ?> to <?php echo date('d-M-Y', strtotime($row->end_date)); ?></td>
                                            <td><?php echo date('d-M-Y', strtotime($row->target_friday)); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state">
                            No legacy running DF meeting MOM task is available for Friday reset.
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>

        <?php $this->load->view('common/footer'); ?>
    </div>
</div>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script>
    $(document).ready(function () {
        $('#check-all-reset').on('change', function () {
            $('.reset-task-check').prop('checked', $(this).is(':checked'));
        });
    });
</script>
</body>
</html>
