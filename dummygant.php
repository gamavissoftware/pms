<?php
$tasks = [
    [
        'id' => 1,
        'name' => 'Task A',
        'department' => 'X',
        'planned_start' => '2023-01-01',
        'planned_end' => '2023-01-10',
        'actual_start' => '2023-01-01',
        'actual_end' => '2023-01-12',
        'progress' => 100,
        'dependencies' => []
    ],
    [
        'id' => 2,
        'name' => 'Task B',
        'department' => 'Y',
        'planned_start' => '2023-01-11',
        'planned_end' => '2023-01-20',
        'actual_start' => '2023-01-13',
        'actual_end' => '2023-01-22',
        'progress' => 80,
        'dependencies' => [1]
    ],
    // Add more tasks as needed
];

function calculateDelay($planned_end, $actual_end) {
    $planned_end_date = new DateTime($planned_end);
    $actual_end_date = new DateTime($actual_end);
    return $actual_end_date->diff($planned_end_date)->days;
}

foreach ($tasks as &$task) {
    $task['delay'] = calculateDelay($task['planned_end'], $task['actual_end']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gantt Chart</title>
    <style>
        .gantt-chart {
            width: 100%;
            border-collapse: collapse;
        }
        .gantt-chart th, .gantt-chart td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }
        .gantt-chart .task {
            position: relative;
            background-color: #a8d5ff;
            height: 20px;
            line-height: 20px;
        }
        .gantt-chart .progress {
            background-color: #0056b3;
            height: 100%;
            display: inline-block;
        }
        .gantt-chart .delay {
            background-color: #ff4d4d;
            height: 100%;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>Gantt Chart</h1>
    <table class="gantt-chart">
        <thead>
            <tr>
                <th>Task</th>
                <th>Department</th>
                <th>Planned Start</th>
                <th>Planned End</th>
                <th>Actual Start</th>
                <th>Actual End</th>
                <th>Progress</th>
                <th>Delay (Days)</th>
                <th>Gantt</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?php echo $task['name']; ?></td>
                    <td><?php echo $task['department']; ?></td>
                    <td><?php echo $task['planned_start']; ?></td>
                    <td><?php echo $task['planned_end']; ?></td>
                    <td><?php echo $task['actual_start']; ?></td>
                    <td><?php echo $task['actual_end']; ?></td>
                    <td><?php echo $task['progress']; ?>%</td>
                    <td><?php echo $task['delay']; ?></td>
                    <td>
                        <div class="task" style="width: 200px;">
                            <div class="progress" style="width: <?php echo $task['progress']; ?>%;"></div>
                            <?php if ($task['delay'] > 0): ?>
                                <div class="delay" style="width: <?php echo $task['delay'] * 2; ?>px;"></div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
