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
                <div class="me-auto">
                    <h1 class="display-5"><?php echo $page_title; ?></h1>
                    <p class="text-muted mb-0">Drawing No: <?php echo $drawing_no; ?></p>
                </div>
                <div class="ms-md-4 mt-3 mt-md-0" style="min-width: 300px;">
                    <label for="part-switcher" class="form-label fw-bold">Switch to another part:</label>
                            <select id="part-switcher" class="form-select form-select-lg">
                            <option></option>
                            <?php foreach ($all_parts as $part): ?>
                            <option value="<?php echo urlencode($part['part_name']); ?>" <?php if ($part['part_name'] == $part_name) echo 'selected'; ?>>
                            <?php echo html_escape($part['part_name']); ?>
                            </option>
                            <?php endforeach; ?>
                            </select>
                </div>
            </div>
        </div>

        <?php if ($kpis): // --- START OF THE ROBUST FIX IN THE VIEW --- ?>

            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4">
                <div class="col">
                    <div class="stat-card h-100">
                        <div class="icon text-primary"><i class="bi bi-boxes"></i></div>
                        <div class="value"><?php echo number_format($kpis['total_produced']); ?></div>
                        <div class="title">Total Quantity Produced</div>
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
                        <div class="icon text-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div class="value"><?php echo $kpis['rejection_rate_percentage']; ?>%</div>
                        <div class="title">Rejection Rate</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-card h-100">
                        <div class="icon text-warning"><i class="bi bi-speedometer2"></i></div>
                        <div class="value"><?php echo $kpis['avg_cycle_time_per_unit']; ?></div>
                        <div class="title">Avg. Mins / Unit</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-card h-100">
                        <div class="icon text-info"><i class="bi bi-gear-fill"></i></div>
                        <div class="value" style="font-size: 1.5rem;"><?php echo html_escape($kpis['primary_machine']); ?></div>
                        <div class="title">Primary Machine</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-3">
                <div class="col-lg-6">
                    <div class="report-block">
                        <h5 class="fw-bold mb-3">Production by Machine</h5>
                        <div class="chart-wrapper">
                            <canvas id="productionByMachineChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="report-block">
                        <h5 class="fw-bold mb-3">Rejections by Machine</h5>
                        <div class="chart-wrapper">
                            <canvas id="rejectionsByMachineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 mb-5">
                <div class="col-12">
                    <div class="report-block">
                        <h5 class="fw-bold">Detailed Production Log for this Part</h5>
                        <div class="table-responsive">
                            <table id="logsTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Shift</th>
                                        <th>Machine No</th>
                                        <th>Operator</th>
                                        <th>Planned</th>
                                        <th>Actual</th>
                                        <th>Rejected</th>
                                        <th>Rejection Reason</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($production_logs as $log): ?>
                                    <tr>
                                        <td><?php echo date('d-M-Y', strtotime($log['production_date'])); ?></td>
                                        <td><?php echo html_escape($log['shift']); ?></td>
                                        <td><?php echo html_escape($log['machine_no']); ?></td>
                                        <td><?php echo html_escape($log['operator_name']); ?></td>
                                        <td><?php echo $log['planned_qty']; ?></td>
                                        <td><?php echo $log['actual_qty']; ?></td>
                                        <td><?php echo $log['quantity_rejected']; ?></td>
                                        <td><?php echo html_escape($log['rejection_reason']); ?></td>
                                        <td><?php echo html_escape($log['remarks']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: // --- What to show if there is no data --- ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        <h4 class="alert-heading">No Data Found</h4>
                        <p>There are no production records for <strong><?php echo html_escape($drawing_no); ?></strong> within the selected date range.</p>
                        <hr>
                        <p class="mb-0">Please try selecting a different part or adjusting the date range.</p>
                    </div>
                </div>
            </div>
        <?php endif; // --- END OF THE FIX IN THE VIEW --- ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        // Only initialize the table and charts if there is data
        <?php if ($kpis): ?>
            $('#logsTable').DataTable();

            $('#part-switcher').select2({
                theme: "bootstrap-5",
                placeholder: "Search for a part...",
            });

            $('#part-switcher').on('change', function() {
                const selectedPart = $(this).val();
                if (selectedPart) {
                    const baseUrl = "<?php echo page_url.'reports/part/'; ?>";
                    const startDate = "<?php echo $start_date; ?>";
                    const endDate = "<?php echo $end_date; ?>";
                    const finalUrl = `${baseUrl}${selectedPart}?start_date=${startDate}&end_date=${endDate}`;
                    window.location.href = finalUrl;
                }
            });

            // Chart for Production by Machine
            const prodCtx = document.getElementById('productionByMachineChart');
            const prodData = <?php echo json_encode(array_column($production_by_machine, 'total_quantity')); ?>;
            if (prodData.length > 0) {
                new Chart(prodCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_column($production_by_machine, 'machine_no')); ?>,
                        datasets: [{
                            label: 'Units Produced', data: prodData, backgroundColor: 'rgba(25, 135, 84, 0.7)',
                            borderColor: 'rgba(25, 135, 84, 1)', borderRadius: 5
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });
            } else {
                prodCtx.parentElement.innerHTML = '<div class="alert alert-info text-center d-flex align-items-center justify-content-center h-100">No production data available for this part.</div>';
            }

            // Chart for Rejections by Machine
            const rejectCtx = document.getElementById('rejectionsByMachineChart');
            const rejectData = <?php echo json_encode(array_column($rejections_by_machine, 'total_rejected')); ?>;
            if (rejectData.length > 0) {
                new Chart(rejectCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_column($rejections_by_machine, 'machine_no')); ?>,
                        datasets: [{
                            label: 'Units Rejected', data: rejectData, backgroundColor: 'rgba(220, 53, 69, 0.7)',
                            borderColor: 'rgba(220, 53, 69, 1)', borderRadius: 5
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });
            } else {
                rejectCtx.parentElement.innerHTML = '<div class="alert alert-info text-center d-flex align-items-center justify-content-center h-100">No rejections recorded for this part.</div>';
            }
        <?php else: ?>
            // Still initialize the dropdown even if there's no data for this specific part
            $('#part-switcher').select2({
                theme: "bootstrap-5",
                placeholder: "Search for a part...",
            });

            $('#part-switcher').on('change', function() {
                const selectedPart = $(this).val();
                if (selectedPart) {
                    const baseUrl = "<?php echo page_url.'reports/part/'; ?>";
                    const startDate = "<?php echo $start_date; ?>";
                    const endDate = "<?php echo $end_date; ?>";
                    const finalUrl = `${baseUrl}${selectedPart}?start_date=${startDate}&end_date=${endDate}`;
                    window.location.href = finalUrl;
                }
            });
        <?php endif; ?>
    });
    </script>
</body>
</html>