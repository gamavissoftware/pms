<!DOCTYPE html>
<html>
<head>
    <title>Production Logs Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <style>
        /* Aesthetic Improvements */
        body { background-color: #f4f7f6; }
        .container-fluid { width: 98%; margin-top: 20px; }
        .filter-box { 
            background-color: #ffffff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
            margin-bottom: 20px;
            border-left: 5px solid #007bff;
        }
        .filter-control { 
            margin-bottom: 15px; 
        }
        /* Dashboard Cards */
        .summary-card {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
            margin-bottom: 15px;
        }
        .summary-card h3 {
            margin-top: 0;
            font-weight: 700;
        }
        .summary-card p {
            font-size: 1.5em;
            margin-bottom: 0;
            font-weight: 500;
        }
        .planned-card { border-bottom: 3px solid #007bff; }
        .actual-card { border-bottom: 3px solid #28a745; }
        
        /* Chart container styling for side-by-side charts */
        .chart-container-group {
            margin-bottom: 20px;
        }
        .chart-box {
            background-color: #ffffff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
            height: 480px; /* Fixed height for both charts */
        }
        .chart-box h4 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-top: 0;
            margin-bottom: 15px;
        }

        /* Highlight editable fields */
        [contenteditable="true"] {
            border: 1px dashed #ced4da;
            padding: 3px;
            min-width: 50px;
            display: inline-block;
            transition: background-color 0.3s;
        }
        [contenteditable="true"]:focus {
            outline: 2px solid #28a745;
            background-color: #e9ecef;
        }
        /* Color feedback for saving */
        .saving { background-color: #fff3cd !important; }
        .success { background-color: #d4edda !important; }
        .error { background-color: #f8d7da !important; }
    </style>
</head>
<body>

<div class="container-fluid">
    <h2 class="text-primary" style="margin-bottom: 25px;">
        🏭 Production Logs Report 
        <small class="text-muted" style="font-size: 60%;">High Volume Data Management</small>
    </h2>

    <div class="row">
        <div class="col-md-3">
             <div class="filter-box" style="height: 480px;">
                <h4 style="margin-top: 0;">Filter Records</h4>
                <div class="row">
                    <div class="col-sm-12 filter-control">
                        <label for="filter_month">Month</label>
                        <select id="filter_month" class="form-control">
                            <option value="">All Months</option>
                            <?php 
                            $months = array_unique(array_column($monthly_summary, 'month'));
                            foreach ($months as $month_key) {
                                $month_label = date('M Y', strtotime($month_key . '-01'));
                                echo "<option value='{$month_key}'>{$month_label}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-sm-12 filter-control">
                        <label for="filter_machine">Machine</label>
                        <select id="filter_machine" class="form-control">
                            <option value="">All Machines</option>
                            <?php 
                            if (!empty($machines)) {
                                foreach ($machines as $item) {
                                    echo "<option value='{$item->machine_no}'>{$item->machine_no}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-sm-12 filter-control">
                        <label for="filter_operator">Operator</label>
                        <select id="filter_operator" class="form-control">
                            <option value="">All Operators</option>
                            <?php 
                            if (!empty($operators)) {
                                foreach ($operators as $item) {
                                    if (!empty($item->operator_name)) {
                                        echo "<option value='{$item->operator_name}'>{$item->operator_name}</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-sm-12 filter-control">
                        <label for="filter_df_no">DF No.</label>
                        <select id="filter_df_no" class="form-control">
                            <option value="">All DF Nos.</option>
                             <?php 
                            if (!empty($df_nos)) {
                                foreach ($df_nos as $item) {
                                    if (!empty($item->df_no)) {
                                        echo "<option value='{$item->df_no}'>{$item->df_no}</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-sm-12 filter-control">
                        <label for="filter_drawing_no">Drawing No.</label>
                        <select id="filter_drawing_no" class="form-control">
                            <option value="">All Drawing Nos.</option>
                            <?php 
                            if (!empty($drawing_nos)) {
                                foreach ($drawing_nos as $item) {
                                     if (!empty($item->drawing_no)) {
                                        echo "<option value='{$item->drawing_no}'>{$item->drawing_no}</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-sm-6 filter-control">
                        <button id="btn_filter" class="btn btn-success btn-block">Apply Filters</button>
                    </div>
                    <div class="col-sm-6 filter-control">
                        <button id="btn_reset" class="btn btn-warning btn-block">Reset</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9 chart-container-group">
            <div class="row">
                
                <div class="col-md-6">
                    <div class="chart-box">
                        <h4>Monthly Production Summary (Planned vs Actual)</h4>
                        
                        <div class="row">
                            <?php
                                $total_planned_all_time = array_sum(array_column($monthly_summary, 'total_planned'));
                                $total_actual_all_time = array_sum(array_column($monthly_summary, 'total_actual'));
                            ?>
                            <div class="col-xs-6">
                                <div class="summary-card planned-card">
                                    <h3>Planned Qty</h3>
                                    <p class="text-primary"><?php echo number_format($total_planned_all_time); ?></p>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="summary-card actual-card">
                                    <h3>Actual Qty</h3>
                                    <p class="text-success"><?php echo number_format($total_actual_all_time); ?></p>
                                </div>
                            </div>
                        </div>
                        <div style="height:280px;">
                            <canvas id="monthlyProductionChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="chart-box">
                        <h4>Top 5 Machine Performance (Actual vs. Planned)</h4>
                        <div style="height:420px;">
                            <canvas id="top5MachineChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">Production Data</div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="production_logs_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DATE</th>
                            <th>SHIFT</th>
                            <th>MACHINE</th>
                            <th>OPERATOR NAME</th>
                            <th>AVAILABLE TIME</th>
                            <th>DRAWING NO</th>
                            <th>PART NAME</th>
                            <th>DF NO</th>
                            <th>CYCLE TIME (MIN)</th>
                            <th>PLANNED QTY</th>
                            <th>ACTUAL QTY</th>
                            <th>QTY REJECTED</th>
                            <th>REJECTION REASON</th>
                            <th>BD (MIN)</th>
                            <th>SETTING (MIN)</th>
                            <th>RM SHORT (MIN)</th>
                            <th>NO OPERATOR (MIN)</th>
                            <th>OTHER (MIN)</th>
                            <th>TOTAL TIME (MIN)</th>
                            <th>SETUP</th>
                            <th>TOOLS USED</th>
                            <th>OPERATION</th>
                            <th>REMARKS</th>
                            <th>CURRENT TIME</th>
                            <th>CREATED AT</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass PHP arrays to JavaScript using JSON encoding
    var monthlySummaryData = <?php echo json_encode($monthly_summary); ?>;
    var top5MachineData = <?php echo json_encode($top_5_machines); ?>;
</script>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap.min.js"></script>

<script>
    var table;

    $(document).ready(function() {
        
        // --- 1. MONTHLY CHART INITIALIZATION ---
        if (monthlySummaryData && monthlySummaryData.length > 0) {
            
            var labels = monthlySummaryData.map(function(item) {
                var date = new Date(item.month + '-01');
                return date.toLocaleString('en-US', { month: 'short', year: 'numeric' });
            });

            var plannedData = monthlySummaryData.map(function(item) {
                return item.total_planned;
            });
            
            var actualData = monthlySummaryData.map(function(item) {
                return item.total_actual;
            });

            var ctx = document.getElementById('monthlyProductionChart').getContext('2d');
            var monthlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Planned Quantity',
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1,
                        data: plannedData
                    }, {
                        label: 'Actual Quantity',
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        data: actualData
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'bottom' },
                    title: { display: false },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.9,
                            categoryPercentage: 0.7,
                            display: true,
                            scaleLabel: { display: true, labelString: 'Month' }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: { display: true, labelString: 'Quantity' },
                            ticks: { beginAtZero: true }
                        }]
                    }
                }
            });
        }
        
        // --- 2. TOP 5 MACHINE CHART INITIALIZATION ---
        if (top5MachineData && top5MachineData.length > 0) {
            
            var machineLabels = top5MachineData.map(function(item) {
                return item.machine_no;
            });

            var plannedTop5 = top5MachineData.map(function(item) {
                return item.total_planned;
            });
            
            var actualTop5 = top5MachineData.map(function(item) {
                return item.total_actual;
            });

            var ctx2 = document.getElementById('top5MachineChart').getContext('2d');
            var top5Chart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: machineLabels,
                    datasets: [{
                        label: 'Planned Quantity',
                        backgroundColor: 'rgba(255, 193, 7, 0.7)', // Yellow/Amber
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        data: plannedTop5
                    }, {
                        label: 'Actual Quantity',
                        backgroundColor: 'rgba(220, 53, 69, 0.7)', // Red/Danger
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        data: actualTop5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'bottom' },
                    title: { display: false },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.9,
                            categoryPercentage: 0.7,
                            display: true,
                            scaleLabel: { display: true, labelString: 'Machine' }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: { display: true, labelString: 'Quantity' },
                            ticks: { beginAtZero: true }
                        }]
                    }
                }
            });
        }


        // --- 3. DATATABLES INITIALIZATION (REMAINS THE SAME) ---
        table = $('#production_logs_table').DataTable({ 
            "dom": 'lBfrtip',
            "processing": true,
            "serverSide": true,
            "order": [[0, 'desc']],
            "scrollX": true,
            
            "ajax": {
                "url": "<?php echo page_url.'production/datatable_ajax';?>",
                "type": "POST",
                "data": function(d) {
                    d.custom_filters = {
                        month: $('#filter_month').val(),
                        machine: $('#filter_machine').val(),
                        operator: $('#filter_operator').val(),
                        df_no: $('#filter_df_no').val(),
                        drawing_no: $('#filter_drawing_no').val()
                    };
                }
            },

            "columns": [
                {"data": 0}, {"data": 1}, {"data": 2}, {"data": 3}, {"data": 4}, 
                {"data": 5}, {"data": 6}, {"data": 7}, {"data": 8}, {"data": 9}, 
                {"data": 10}, {"data": 11}, {"data": 12}, {"data": 13}, {"data": 14}, 
                {"data": 15}, {"data": 16}, {"data": 17}, {"data": 18}, {"data": 19},
                {"data": 20}, {"data": 21}, {"data": 22}, {"data": 23}, {"data": 24},
                {"data": 25}
            ],

            "columnDefs": [
                { 
                    "targets": [3, 5, 6, 7, 8, 13, 19, 20, 21, 22, 23, 24, 25], 
                    "orderable": false,
                    "searchable": false
                },
                { "width": "100px", "targets": [1, 2, 24, 25] }, 
                { "width": "60px", "targets": [0, 11, 12] } 
            ],
            
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        });

        // --- 4. INLINE EDITING LOGIC (REMAINS THE SAME) ---
        $('#production_logs_table').on('blur', 'span[contenteditable="true"]', function() {
            var $this = $(this);
            var log_id = $this.data('id');
            var field_name = $this.data('name');
            var old_value = $this.data('old-value'); 
            var new_value = $this.text();
            
            if (new_value === old_value) {
                $this.removeClass('saving success error');
                return;
            }

            $this.removeClass('success error').addClass('saving'); 

            $.ajax({
                url: "<?php echo page_url.'production/update_record_ajax';?>",
                type: 'POST',
                data: {
                    pk: log_id,
                    name: field_name,
                    value: new_value
                },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        $this.removeClass('saving error').addClass('success');
                        setTimeout(function() {
                             $this.removeClass('success');
                        }, 2000);
                        $this.data('old-value', new_value); 
                    } else {
                        alert("Error saving record: " + response.message);
                        $this.removeClass('saving success').addClass('error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('AJAX Error: Could not connect to server or server error.');
                    $this.removeClass('saving success').addClass('error');
                }
            });
        });
        
        $('#production_logs_table').on('focus', 'span[contenteditable="true"]', function() {
            $(this).data('old-value', $(this).text());
        });
        
        $('#production_logs_table').on('keypress', 'span[data-name="actual_qty"], span[data-name="quantity_rejected"], span[data-name="b_d"], span[data-name="setting_s"], span[data-name="rm_short"], span[data-name="no_operator"], span[data-name="other"]', function(e) {
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
    });
</script>

</body>
</html>