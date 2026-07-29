<?php
$employee_name = isset($employee_name) ? $employee_name : 'Team Member';
$report_date = isset($report_date) ? $report_date : date('d M Y');
$generated_on = isset($generated_on) ? $generated_on : date('d M Y h:i A');
$task_count = isset($task_count) ? (int) $task_count : 0;
$max_delay_days = isset($max_delay_days) ? (int) $max_delay_days : 0;
$oldest_due_date = isset($oldest_due_date) ? $oldest_due_date : '-';
$logo_url = isset($logo_url) ? $logo_url : '';
$tasks = isset($tasks) && is_array($tasks) ? $tasks : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Overdue Task Report</title>
    <style>
        @page {
            margin: 26px 24px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
        }
        .header-shell {
            border: 1px solid #d7e3ef;
            border-radius: 12px;
            background: #f8fbfe;
            padding: 18px 20px;
            margin-bottom: 18px;
        }
        .brand-table,
        .summary-table,
        .task-table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            text-align: right;
        }
        .brand-title h1 {
            margin: 0;
            font-size: 22px;
            color: #0f4c81;
        }
        .brand-title p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #5b6b79;
        }
        .intro {
            margin-top: 16px;
            line-height: 1.6;
        }
        .summary-wrap {
            margin: 14px 0 18px;
        }
        .summary-table td {
            width: 33.33%;
            border: 1px solid #d7e3ef;
            background: #ffffff;
            padding: 12px 14px;
            vertical-align: top;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.4px;
        }
        .summary-value {
            display: block;
            margin-top: 6px;
            font-size: 17px;
            font-weight: bold;
            color: #111827;
        }
        .section-title {
            margin: 0 0 10px;
            font-size: 14px;
            color: #0f4c81;
        }
        .task-table thead th {
            background: #0f4c81;
            color: #ffffff;
            border: 1px solid #c9d8e7;
            padding: 9px 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .task-table tbody td {
            border: 1px solid #d7e3ef;
            padding: 9px 8px;
            vertical-align: top;
            line-height: 1.45;
        }
        .task-table tbody tr:nth-child(even) td {
            background: #f8fbfe;
        }
        .delay-badge {
            display: inline-block;
            background: #fde8e8;
            color: #b42318;
            border: 1px solid #f5c2c7;
            border-radius: 10px;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
        }
        .footer-note {
            margin-top: 16px;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="header-shell">
        <table class="brand-table">
            <tr>
                <td style="width: 34%; vertical-align: middle;">
                    <?php if ($logo_url !== '') : ?>
                        <img src="<?php echo htmlspecialchars($logo_url, ENT_QUOTES, 'UTF-8'); ?>" alt="Shubham Pack" style="max-width: 150px; max-height: 56px;">
                    <?php endif; ?>
                </td>
                <td class="brand-title" style="width: 66%; vertical-align: middle;">
                    <h1>Overdue Task Report</h1>
                    <p>Report Date: <?php echo htmlspecialchars($report_date, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p>Generated On: <?php echo htmlspecialchars($generated_on, ENT_QUOTES, 'UTF-8'); ?></p>
                </td>
            </tr>
        </table>

        <div class="intro">
            <strong>Dear <?php echo htmlspecialchars($employee_name, ENT_QUOTES, 'UTF-8'); ?>,</strong><br>
            This report lists your pending overdue tasks that require immediate follow-up. Please review each item carefully and update the PMS once the action is completed.
        </div>
    </div>

    <div class="summary-wrap">
        <table class="summary-table">
            <tr>
                <td>
                    <span class="summary-label">Total Overdue Tasks</span>
                    <span class="summary-value"><?php echo $task_count; ?></span>
                </td>
                <td>
                    <span class="summary-label">Maximum Delay</span>
                    <span class="summary-value"><?php echo $max_delay_days; ?> Day<?php echo $max_delay_days === 1 ? '' : 's'; ?></span>
                </td>
                <td>
                    <span class="summary-label">Oldest Due Date</span>
                    <span class="summary-value" style="font-size: 15px;"><?php echo htmlspecialchars($oldest_due_date, ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="section-title">Task Details</h2>
    <table class="task-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 11%;">DF No.</th>
                <th style="width: 12%;">DF Release</th>
                <th style="width: 19%;">Task Name</th>
                <th style="width: 11%;">Start Date</th>
                <th style="width: 11%;">Due Date</th>
                <th style="width: 21%;">Latest Remarks</th>
                <th style="width: 10%;">Delay</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task_row) : ?>
                <tr>
                    <td><?php echo (int) $task_row['sr_no']; ?></td>
                    <td><?php echo htmlspecialchars($task_row['df_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($task_row['df_release_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($task_row['task_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($task_row['start_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($task_row['due_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($task_row['remarks'], ENT_QUOTES, 'UTF-8')); ?></td>
                    <td><span class="delay-badge"><?php echo htmlspecialchars($task_row['delay_label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer-note">
        This is a system-generated document from Shubham Pack PMS.<br>
        Please coordinate with your department head if any task requires additional support or revised timelines.
    </div>
</body>
</html>
