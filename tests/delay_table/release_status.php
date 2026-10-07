<?php
// Render the actual status cell: missing closure projections must not say On Time.
$source = file_get_contents(__DIR__ . '/../../application/views/master/dfreleasedashboard.php');
$start = strpos($source, "<?php if (\$row['delay_status'] === 'Delayed')");
if ($start === false) throw new Exception('Current delay status cell missing');
$end = strpos($source, '</td>', $start);
$cell = substr($source, $start, $end - $start);
foreach ([['Delayed', 0, 12], ['Delayed', 37, 3], ['On Time', 0, 0], ['On Time', 10, 0]] as $case) {
    $row = ['delay_status'=>$case[0], 'df_delay_days'=>$case[1], 'overdue_task_count'=>$case[2]];
    ob_start(); eval('?>' . $cell); $html = ob_get_clean();
    if ($case[0] === 'Delayed' && (strpos($html, 'On Time') !== false || strpos($html, $case[2] . ' overdue pending tasks') === false)) {
        throw new Exception('Delayed task status does not match visible badge');
    }
    if ($case[0] === 'On Time' && strpos($html, '>On Time<') === false) throw new Exception('On-time badge missing');
}
echo "PASS: current status badge matches overdue tasks with and without projected closure delay.\n";
