<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DF Wise Gantt Chart - SHUBHAM PACK</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-transform: uppercase; background-color: #f8f9fa; }
        .wrapper { padding: 30px; }
        h1 { position: relative; display: inline-block; margin: 0 0 20px; font-weight: 700; color: #343a40; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background-color: #ffffff; margin-bottom: 25px; }
        .card-header { background-color: #495057; color: white; font-weight: 600; border-top-left-radius: 10px; border-top-right-radius: 10px; padding: 1rem 1.25rem; }
        .info-table th { font-size: 12px; font-weight: bold; color: #6c757d; }
        .info-table span { color: #8d4f00; font-weight: normal; font-size: 14px; }
        
        /* Gantt Chart Styling */
        .table-scroll { overflow-x: auto; overflow-y: auto; height: 700px; border: 1px solid #dee2e6; border-radius: 10px; }
        .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main-table th, .main-table td { font-size: 10px; text-align: center; vertical-align: middle; border: 1px solid #dee2e6; padding: 8px; box-sizing: border-box; white-space: nowrap; }
        .main-table thead th { position: sticky; top: 0; background-color: #f1f3f5; z-index: 5; font-weight: 600; }
        .main-table .fixed-col { position: sticky; left: 0; background-color: white; z-index: 4; }
        .main-table .fixed-col-left { position: sticky; left: 0; background-color: white; z-index: 4; }
        .main-table .fixed-col-middle { position: sticky; left: 10%; background-color: white; z-index: 4; }
        .main-table .fixed-col-right { position: sticky; left: 25%; background-color: white; z-index: 4; }
        
        /* Color Indicators */
        .blue { background-color: #afcbe3; }
        .green { background-color: #a2d2ff; }
        .red { background-color: #FAA0A0; }
        .dark-green { background-color: #73d673; }

        /* Custom Headers & Rows */
        .gantt-header-row { background-color: #e9ecef; }
        .date-cell { writing-mode: vertical-rl; transform: rotate(180deg); height: 120px; vertical-align: bottom; }
        .department-row, .sub-task-row { transition: background-color 0.3s ease; }
        .department-row:hover { background-color: #e6f7ff; cursor: pointer; }
        .sub-task-row { display: none; }
        .toggle-icon { margin-left: 5px; color: #6c757d; transition: transform 0.3s ease; }
        
        /* Legends */
        .legend-box { display: inline-block; width: 15px; height: 15px; border-radius: 3px; margin-right: 5px; vertical-align: middle; }
        .legend-planned { background-color: #afcbe3; }
        .legend-actual { background-color: #a2d2ff; }
        .legend-delayed { background-color: #FAA0A0; }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <select id="switch" onchange="switchData();" class="custom-select w-auto mb-3">
                        <option value="2">Day Wise</option>
                        <option value="1">Week Wise</option>
                        <option value="3">Department Wise</option>
                        <option value="4" selected>Department & Week Wise</option>
                    </select>
                    <h1>Progress Gantt Chart with Details</h1>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">DF Details</div>
                <div class="card-body">
                    <div class="row info-table">
                        <div class="col-md-3"><strong>DF NO:</strong> <br><span><?php echo $df_details['df_no']; ?></span></div>
                        <div class="col-md-3"><strong>Company Name:</strong> <br><span><?php echo $df_details['company_name']; ?></span></div>
                        <div class="col-md-3"><strong>DF Release Date:</strong> <br><span><?php echo date('d-M-Y', strtotime($df_details['added_on'])); ?></span></div>
                        <div class="col-md-3"><strong>Marketing Person:</strong> <br><span><?php echo $df_details['marketing_person']; ?></span></div>
                    </div>
                </div>
            </div>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-warning text-center">
                    <strong>Warning!</strong> <?php echo $error; ?>
                </div>
            <?php } ?>

            <?php if (empty($error) && !empty($dates)) { ?>
                <div class="table-scroll">
                    <table class="main-table" border="1">
                        <thead>
                            <tr class="gantt-header-row">
                                <th class="fixed-col" style="width: 5%;" rowspan="3">#</th>
                                <th class="fixed-col" style="width: 15%;" rowspan="3">Department</th>
                                <th class="fixed-col" style="width: 25%;" rowspan="3">Task</th>
                                <th style="width: 5%;" rowspan="3">PLN<br>ACT</th>
                                <th style="width: 10%;" rowspan="3">Start Date</th>
                                <th style="width: 10%;" rowspan="3">End Date</th>
                                <th style="width: 5%;" rowspan="3">Delay</th>
                                <th style="width: 5%;" rowspan="3">% Done</th>
                                <?php foreach ($days_count as $month => $days) { ?>
                                    <th colspan="<?php echo $days; ?>" class="text-center lighter_grey"><?php echo $month; ?></th>
                                <?php } ?>
                            </tr>
                            <tr class="gantt-header-row">
                                <?php foreach ($dates as $date) { ?>
                                    <th class="text-center lighter_grey"><?php echo $this->report->get_week_number_from_date($date); ?></th>
                                <?php } ?>
                            </tr>
                            <tr class="gantt-header-row">
                                <?php foreach ($dates as $date) { ?>
                                    <td class="light_grey date-cell"><div style="writing-mode: vertical-rl; transform: rotate(180deg);"><?php echo date('d M', strtotime($date)); ?></div></td>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($report_data as $main_task) { ?>
                                <tr class="department-row" data-toggle-id="<?php echo $main_task['main_task_data']['id']; ?>">
                                    <td class="fixed-col" rowspan="2"><?php echo $i; ?></td>
                                    <td class="fixed-col text-left" rowspan="2"><?php echo ucfirst(strtolower($main_task['department_name'])); ?> <i class="fa fa-plus-circle toggle-icon"></i></td>
                                    <td class="fixed-col text-left" rowspan="2"><?php echo ucwords(strtolower($main_task['main_task_data']['taskname'])); ?></td>
                                    <td>PLN</td>
                                    <td><?php echo date('d-M-Y', strtotime($main_task['min_start_date'])); ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($main_task['max_end_date'])); ?></td>
                                    <td><?php echo $main_task['delay_days']; ?></td>
                                    <td><?php echo $main_task['completion_percent'] . "%"; ?></td>
                                    <?php foreach ($dates as $date) {
                                        $currentDate = strtotime($date);
                                        $plannedStart = strtotime($main_task['min_start_date']);
                                        $plannedEnd = strtotime($main_task['max_end_date']);
                                        $css_class = ($currentDate >= $plannedStart && $currentDate <= $plannedEnd) ? 'blue' : '';
                                    ?>
                                        <td class="<?php echo $css_class; ?>"></td>
                                    <?php } ?>
                                </tr>
                                <tr class="department-row" data-toggle-id="<?php echo $main_task['main_task_data']['id']; ?>">
                                    <td>ACT</td>
                                    <td></td>
                                    <td><?php echo $main_task['main_task_completion_date'] ? date('d-M-Y', strtotime($main_task['main_task_completion_date'])) : ''; ?></td>
                                    <td></td>
                                    <td></td>
                                    <?php foreach ($dates as $date) {
                                        $currentDate = strtotime($date);
                                        $plannedEnd = strtotime($main_task['max_end_date']);
                                        $actualStart = strtotime($main_task['min_start_date']);
                                        $actualEnd = strtotime($main_task['main_task_completion_date'] ?? date('Y-m-d'));
                                        $css_class = '';
                                        
                                        if ($currentDate >= $actualStart && $currentDate <= $plannedEnd) {
                                            $css_class = 'dark-green';
                                        }
                                        if ($actualEnd > $plannedEnd && $currentDate > $plannedEnd && $currentDate <= $actualEnd) {
                                            $css_class = 'red';
                                        }
                                    ?>
                                        <td class="<?php echo $css_class; ?>"></td>
                                    <?php } ?>
                                </tr>
                                <?php 
                                $ty = 1;
                                foreach ($main_task['sub_tasks'] as $sub_task) { 
                                ?>
                                    <tr class="sub-task-row" data-parent-id="<?php echo $main_task['main_task_data']['id']; ?>">
                                        <td class="fixed-col" rowspan="2"><?php echo $i . "." . $ty; ?></td>
                                        <td class="fixed-col" rowspan="2"></td>
                                        <td class="fixed-col text-left" rowspan="2"><?php echo ucwords(strtolower($sub_task['task_name'])); ?></td>
                                        <td>PLN</td>
                                        <td><?php echo date('d-M-Y', strtotime($sub_task['start_date'])); ?></td>
                                        <td><?php echo date('d-M-Y', strtotime($sub_task['end_date'])); ?></td>
                                        <td><?php echo $this->report->get_delay_days($sub_task['end_date'], $sub_task['task_completed_on']); ?></td>
                                        <td><?php echo $sub_task['task_status'] == 1 ? '100.00%' : '0.00%'; ?></td>
                                        <?php foreach ($dates as $date) {
                                            $currentDate = strtotime($date);
                                            $plannedStart = strtotime($sub_task['start_date']);
                                            $plannedEnd = strtotime($sub_task['end_date']);
                                            $css_class = ($currentDate >= $plannedStart && $currentDate <= $plannedEnd) ? 'blue' : '';
                                        ?>
                                            <td class="<?php echo $css_class; ?>"></td>
                                        <?php } ?>
                                    </tr>
                                    <tr class="sub-task-row" data-parent-id="<?php echo $main_task['main_task_data']['id']; ?>">
                                        <td>ACT</td>
                                        <td colspan="2" class="text-center">
                                            <?php echo $sub_task['task_completed_on'] ? date('d-M-Y', strtotime($sub_task['task_completed_on'])) : ''; ?>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <?php foreach ($dates as $date) {
                                            $currentDate = strtotime($date);
                                            $plannedEnd = strtotime($sub_task['end_date']);
                                            $actualStart = strtotime($sub_task['start_date']);
                                            $actualEnd = $sub_task['task_completed_on'] ? strtotime($sub_task['task_completed_on']) : null;
                                            $css_class = '';
                                            
                                            if ($sub_task['task_status'] == 1) { // Task is completed
                                                if ($currentDate >= $actualStart && $currentDate <= $plannedEnd) {
                                                    $css_class = 'dark-green';
                                                }
                                                if ($actualEnd && $actualEnd > $plannedEnd && $currentDate > $plannedEnd && $currentDate <= $actualEnd) {
                                                    $css_class = 'red';
                                                }
                                            } else { // Task is pending
                                                if ($currentDate >= $actualStart && $currentDate <= strtotime(date('Y-m-d'))) {
                                                    $css_class = 'dark-green';
                                                }
                                                if ($currentDate > $plannedEnd) {
                                                    $css_class = 'red';
                                                }
                                            }
                                        ?>
                                            <td class="<?php echo $css_class; ?>"></td>
                                        <?php } ?>
                                    </tr>
                                <?php $ty++; } ?>
                            <?php $i++; } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js"></script>
    <script>
        var lastOpenedId = null;

        function toggleRow(groupId) {
            var subRows = $('tr[data-parent-id="' + groupId + '"]');
            var icon = $('tr[data-toggle-id="' + groupId + '"] .toggle-icon').first();

            if (lastOpenedId && lastOpenedId !== groupId) {
                $('tr[data-parent-id="' + lastOpenedId + '"]').hide();
                $('tr[data-toggle-id="' + lastOpenedId + '"] .toggle-icon').first().removeClass('fa-minus-circle').addClass('fa-plus-circle');
            }

            if (subRows.is(':hidden')) {
                subRows.show();
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                lastOpenedId = groupId;
            } else {
                subRows.hide();
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                lastOpenedId = null;
            }
        }
        
        $('.department-row').on('click', function() {
            var groupId = $(this).data('toggle-id');
            toggleRow(groupId);
        });

        function switchData() {
            var swi = $("#switch").val();
            var dfId = <?php echo json_encode($df_id); ?>;
            var baseUrl = "<?php echo page_url; ?>";
            if (swi == 1) {
                window.location = baseUrl + "Task/dfgantchartSharmaji/" + dfId;
            } else if (swi == 2) {
                window.location = baseUrl + "Task/dfgantchartNew/" + dfId;
            } else if (swi == 3) {
                window.location = baseUrl + "Task/dfgantchartDepartmentwise/" + dfId;
            } else {
                window.location = baseUrl + "Task/finalgantchart/" + dfId;
            }
        }
    </script>
</body>
</html>