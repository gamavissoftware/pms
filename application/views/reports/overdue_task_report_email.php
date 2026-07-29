<?php
$employee_name = isset($employee_name) ? $employee_name : 'Team Member';
$first_name = isset($first_name) ? $first_name : $employee_name;
$task_count = isset($task_count) ? (int) $task_count : 0;
$max_delay_days = isset($max_delay_days) ? (int) $max_delay_days : 0;
$oldest_due_date = isset($oldest_due_date) ? $oldest_due_date : '-';
$generated_on = isset($generated_on) ? $generated_on : date('d M Y h:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Overdue Task Report</title>
</head>
<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f7fb; padding:24px 0;">
        <tr>
            <td align="center">
                <table width="680" cellpadding="0" cellspacing="0" border="0" style="width:680px; max-width:680px; background:#ffffff; border:1px solid #dfe7f1; border-radius:14px;">
                    <tr>
                        <td style="padding:26px 32px; background:#0f4c81; border-radius:14px 14px 0 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="color:#ffffff; font-size:24px; font-weight:bold;">Overdue Task Report</td>
                                    <td align="right" style="color:#d6e6f5; font-size:13px;">Generated on <?php echo htmlspecialchars($generated_on, ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 18px;">
                            <p style="margin:0 0 12px; font-size:15px; line-height:1.7;">Dear <?php echo htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8'); ?>,</p>
                            <p style="margin:0 0 18px; font-size:14px; line-height:1.8; color:#475467;">
                                Please find attached your latest overdue task report from the PMS. This summary has been shared to help you review pending items quickly and close the oldest open tasks on priority.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px;">
                                <tr>
                                    <td width="33.33%" style="padding:0 8px 0 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #dfe7f1; border-radius:10px; background:#f8fbfe;">
                                            <tr>
                                                <td style="padding:14px;">
                                                    <div style="font-size:11px; color:#667085; text-transform:uppercase; letter-spacing:0.4px;">Total Overdue Tasks</div>
                                                    <div style="margin-top:8px; font-size:24px; font-weight:bold; color:#101828;"><?php echo $task_count; ?></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="33.33%" style="padding:0 4px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #dfe7f1; border-radius:10px; background:#f8fbfe;">
                                            <tr>
                                                <td style="padding:14px;">
                                                    <div style="font-size:11px; color:#667085; text-transform:uppercase; letter-spacing:0.4px;">Maximum Delay</div>
                                                    <div style="margin-top:8px; font-size:24px; font-weight:bold; color:#101828;"><?php echo $max_delay_days; ?> Day<?php echo $max_delay_days === 1 ? '' : 's'; ?></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="33.33%" style="padding:0 0 0 8px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #dfe7f1; border-radius:10px; background:#f8fbfe;">
                                            <tr>
                                                <td style="padding:14px;">
                                                    <div style="font-size:11px; color:#667085; text-transform:uppercase; letter-spacing:0.4px;">Oldest Due Date</div>
                                                    <div style="margin-top:8px; font-size:18px; font-weight:bold; color:#101828;"><?php echo htmlspecialchars($oldest_due_date, ENT_QUOTES, 'UTF-8'); ?></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 14px; font-size:14px; line-height:1.8; color:#475467;">
                                The attached PDF contains task-wise details including DF number, due date, remarks, and delay duration for each pending item assigned to you.
                            </p>
                            <p style="margin:0; font-size:14px; line-height:1.8; color:#475467;">
                                Regards,<br>
                                <strong>Task Notification Desk</strong><br>
                                Shubham Pack PMS
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px 26px; border-top:1px solid #e8eef5; color:#667085; font-size:12px; line-height:1.7;">
                            This is a system-generated notification. Please update the PMS after completing the pending tasks or coordinate with your HOD if timeline support is required.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
