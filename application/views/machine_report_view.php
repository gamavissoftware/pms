<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }
        .header {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .header h1 {
            font-weight: 600;
            color: #343a40;
        }
        .stat-card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-card .icon { font-size: 2.5rem; margin-bottom: 1rem; }
        .stat-card .value { font-size: 2.2rem; font-weight: 600; }
        .stat-card .title { font-size: 0.9rem; color: #6c757d; font-weight: 300; }
        .report-block {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .chart-wrapper { position: relative; height: 400px; width: 100%; }
        .table-responsive { border: none; }
        .dataTables_wrapper { border: none; padding-top: 1rem; }
        a.btn-custom { border-radius: 50px; font-weight: 600; }
        .select2-container--bootstrap-5 .select2-selection {
            padding: 0.6rem 1rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="header">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <a href="<?php echo page_url.'home'; ?>" class="btn btn-outline-primary btn-custom mb-2 mb-md-0"><i class="bi bi-house-door-fill me-2"></i>Back to Home</a>
                <span class="text-muted small"><?php echo $report_period; ?></span>
            </div>
            <div class="d-flex flex-wrap align-items-center">
                <h1 class="display-5 me-auto"><?php echo html_escape($machine_no); ?></h1>
                <div class="ms-md-4 mt-3 mt-md-0" style="min-width: 300px;">
                    <label for="machine-switcher" class="form-label fw-bold">Switch to another machine:</label>
                    <select id="machine-switcher" class="form-select form-select-lg">
                        <option></option>
                        <?php foreach ($all_machines as $machine): ?>
                            <option value="<?php echo urlencode($machine['machine_no']); ?>" <?php if ($machine['machine_no'] == $machine_no) echo 'selected'; ?>>
                                <?php echo html_escape($machine['machine_no']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4">
            <div class="col">
                <div class="stat-card h-100">
                    <div class="icon text-primary"><i class="bi bi-box-seam"></i></div>
                    <div class="value"><?php echo number_format($kpis['total_production']); ?></div>
                    <div class="title">Total Units Produced</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card h-100">
                    <div class="icon text-success"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="value"><?php echo $kpis['performance_efficiency']; ?>%</div>
                    <div class="title">Performance Efficiency</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card h-100">
                    <div class="icon text-info"><i class="bi bi-patch-check-fill"></i></div>
                    <div class="value"><?php echo $kpis['quality_yield_percentage']; ?>%</div>
                    <div class="title">Quality Yield</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card h-100">
                    <div class="icon text-danger"><i class="bi bi-clock-history"></i></div>
                    <div class="value"><?php echo round($kpis['total_downtime_minutes'] / 60, 1); ?> Hrs</div>
                    <div class="title">Total Downtime</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card h-100">
                    <div class="icon text-warning"><i class="bi bi-trophy-fill"></i></div>
                    <div class="value" style="font-size: 1.5rem;"><?php echo html_escape($kpis['most_produced_part']); ?></div>
                    <div class="title">Most Produced Part</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-3">
            <div class="col-lg-4">
                <div class="report-block">
                    <h5 class="fw-bold mb-3">Downtime Breakdown</h5>
                    <div class="chart-wrapper">
                        <canvas id="downtimePieChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="report-block">
                    <h5 class="fw-bold mb-3">Parts Produced by This Machine</h5>
                    <div class="chart-wrapper">
                        <canvas id="partsBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="report-block">
                    <h5 class="fw-bold">Detailed Production Log</h5>
                    <div class="table-responsive">
                        <table id="logsTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Part Name</th>
                                    <th>Planned</th>
                                    <th>Actual</th>
                                    <th>Rejected</th>
                                    <th>Time (Mins)</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($production_logs as $log): ?>
                                <tr>
                                    <td><?php echo date('d-M-Y', strtotime($log['production_date'])); ?></td>
                                    <td><?php echo html_escape($log['shift']); ?></td>
                                    <td><?php echo html_escape($log['part_name']); ?></td>
                                    <td><?php echo $log['planned_qty']; ?></td>
                                    <td><?php echo $log['actual_qty']; ?></td>
                                    <td><?php echo $log['quantity_rejected']; ?></td>
                                    <td><?php echo $log['total_time_minutes']; ?></td>
                                    <td><?php echo html_escape($log['remarks']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    // Helper function to format minutes into a readable string
    function formatMinutesToHours(minutesStr) {
        const minutes = parseFloat(minutesStr);
        if (isNaN(minutes) || minutes <= 0) {
            return '0m';
        }
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = Math.round(minutes % 60);
        let result = '';
        if (hours > 0) {
            result += hours + 'h ';
        }
        if (remainingMinutes > 0 || hours === 0) {
            result += remainingMinutes + 'm';
        }
        return result.trim();
    }

    $(document).ready(function() {
        $('#logsTable').DataTable();
        
        $('#machine-switcher').select2({
            theme: "bootstrap-5",
            placeholder: "Search for a machine...",
        });
        
        $('#machine-switcher').on('change', function() {
            const selectedMachine = $(this).val();
            if (selectedMachine) {
                const baseUrl = "<?php echo page_url.'reports/machine/'; ?>";
                const startDate = "<?php echo $start_date; ?>";
                const endDate = "<?php echo $end_date; ?>";
                const finalUrl = `${baseUrl}${selectedMachine}?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = finalUrl;
            }
        });
        
        // Chart for Downtime Breakdown (Doughnut Chart)
        const downtimeCtx = document.getElementById('downtimePieChart');
        if (downtimeCtx) {
            const downtimeData = <?php echo json_encode(array_values(array_filter($downtime_breakdown))); ?>;
            const downtimeLabels = <?php echo json_encode(array_keys(array_filter($downtime_breakdown))); ?>;
            const hasDowntime = downtimeData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) > 0;

            if (hasDowntime) {
                new Chart(downtimeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: downtimeLabels,
                        datasets: [{
                            data: downtimeData,
                            backgroundColor: ['#dc3545', '#ffc107', '#fd7e14', '#0dcaf0', '#6c757d'],
                            hoverOffset: 4,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const formattedValue = formatMinutesToHours(value);
                                        return `${label}: ${formattedValue}`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                downtimeCtx.parentElement.innerHTML = '<div class="alert alert-info text-center d-flex align-items-center justify-content-center h-100">No downtime recorded for this machine.</div>';
            }
        }

        // --- RESTORED: Chart for Parts Produced (Bar Chart) ---
        const partsCtx = document.getElementById('partsBarChart');
        if (partsCtx) {
            const partsData = <?php echo json_encode(array_column($parts_produced, 'total_quantity')); ?>;
            const partsLabels = <?php echo json_encode(array_column($parts_produced, 'part_name')); ?>;
            
            if (partsData.length > 0) {
                 new Chart(partsCtx, {
                    type: 'bar',
                    data: {
                        labels: partsLabels,
                        datasets: [{
                            label: 'Total Units Produced',
                            data: partsData,
                            backgroundColor: 'rgba(25, 135, 84, 0.7)',
                            borderColor: 'rgba(25, 135, 84, 1)',
                            borderRadius: 5,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } },
                        plugins: { legend: { display: false } }
                    }
                });
            } else {
                partsCtx.parentElement.innerHTML = '<div class="alert alert-info text-center d-flex align-items-center justify-content-center h-100">No parts were produced by this machine in the selected period.</div>';
            }
        }
    });
    </script>
</body>
</html>