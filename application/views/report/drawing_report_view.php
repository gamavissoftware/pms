<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Production Report for Drawing: <?php echo html_escape($drawing_no); ?></title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f7f7f7; }
        .no-data { text-align: center; color: #777; padding: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Production Time Report</h1>
        <p>Showing total time spent on <strong>Drawing No: <?php echo html_escape($drawing_no); ?></strong>, broken down by machine.</p>

        <table>
            <thead>
                <tr>
                    <th>Machine Number</th>
                    <th>Total Time Taken (Minutes)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($report_data)): ?>
                    <?php foreach ($report_data as $row): ?>
                        <tr>
                            <td><?php echo html_escape($row->machine_no); ?></td>
                            <td><?php echo html_escape($row->total_minutes); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="no-data">No production data found for this drawing number.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>