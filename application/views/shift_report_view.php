<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
        .report-block {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .kpi-card {
            border-radius: 15px;
            padding: 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .kpi-card .value {
            font-size: 2.5rem;
            font-weight: 600;
        }
        .kpi-card .title {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .kpi-card .icon {
            position: absolute;
            right: 15px;
            bottom: -10px;
            font-size: 5rem;
            opacity: 0.2;
            transform: rotate(-15deg);
        }
        .shift-a-card { background-color: #0d6efd; }
        .shift-b-card { background-color: #198754; }
        .chart-wrapper { position: relative; height: 400px; width: 100%; }
        a.btn-custom { border-radius: 50px; font-weight: 600; }
        .filter-form { background-color: #ffffff; padding: 1.5rem; border-radius: 15px; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="<?php echo page_url.'home'; ?>" class="btn btn-outline-primary btn-custom"><i class="bi bi-house-door-fill me-2"></i>Back to Home</a>
            </div>
            <h1 class="display-5"><?php echo $page_title; ?></h1>
        </div>

        <div class="filter-form">
            <form action="<?php echo page_url.'reports/shifts'; ?>" method="get" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="start_date" class="form-label fw-bold">Start Date</label>
                    <input type="date" class="form-control form-control-lg" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label fw-bold">End Date</label>
                    <input type="date" class="form-control form-control-lg" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="mb-3 text-center">Shift A</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="kpi-card shift-a-card h-100">
                            <div class="value"><?php echo number_format($kpis['Shift A']['total_produced'] ?? 0); ?></div>
                            <div class="title">Total Production</div>
                            <i class="bi bi-box-seam icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="kpi-card shift-a-card h-100">
                            <div class="value"><?php echo ($kpis['Shift A']['performance_efficiency'] ?? 0); ?>%</div>
                            <div class="title">Performance</div>
                            <i class="bi bi-graph-up-arrow icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="kpi-card shift-a-card h-100">
                            <div class="value"><?php echo ($kpis['Shift A']['quality_rate'] ?? 0); ?>%</div>
                            <div class="title">Quality Rate</div>
                            <i class="bi bi-patch-check-fill icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="kpi-card shift-a-card h-100">
                            <div class="value"><?php echo round(($kpis['Shift A']['total_downtime'] ?? 0) / 60, 1); ?> Hrs</div>
                            <div class="title">Total Downtime</div>
                            <i class="bi bi-clock-history icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="mb-3 text-center">Shift B</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="kpi-card shift-b-card h-100">
                             <div class="value"><?php echo number_format($kpis['Shift B']['total_produced'] ?? 0); ?></div>
                            <div class="title">Total Production</div>
                            <i class="bi bi-box-seam icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="kpi-card shift-b-card h-100">
                            <div class="value"><?php echo ($kpis['Shift B']['performance_efficiency'] ?? 0); ?>%</div>
                            <div class="title">Performance</div>
                            <i class="bi bi-graph-up-arrow icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="kpi-card shift-b-card h-100">
                            <div class="value"><?php echo ($kpis['Shift B']['quality_rate'] ?? 0); ?>%</div>
                            <div class="title">Quality Rate</div>
                            <i class="bi bi-patch-check-fill icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="kpi-card shift-b-card h-100">
                           <div class="value"><?php echo round(($kpis['Shift B']['total_downtime'] ?? 0) / 60, 1); ?> Hrs</div>
                            <div class="title">Total Downtime</div>
                            <i class="bi bi-clock-history icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-3">
            <div class="col-lg-6">
                <div class="report-block">
                    <h5 class="fw-bold mb-3 text-center">Production by Top 5 Parts</h5>
                    <div class="chart-wrapper">
                        <canvas id="topPartsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="report-block">
                    <h5 class="fw-bold mb-3 text-center">Downtime by Reason</h5>
                    <div class="chart-wrapper">
                        <canvas id="downtimeReasonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    $(document).ready(function() {
        // --- Prepare data for charts ---
        const topPartsRaw = <?php echo json_encode($top_parts_data); ?>;
        const downtimeRaw = <?php echo json_encode($downtime_data); ?>;

        // --- Chart 1: Production by Top 5 Parts ---
        const topPartsCtx = document.getElementById('topPartsChart');
        if (topPartsCtx && topPartsRaw.length > 0) {
            const partLabels = [...new Set(topPartsRaw.map(item => item.part_name))];
            const shiftAData = partLabels.map(label => {
                const item = topPartsRaw.find(d => d.part_name === label && d.shift === 'A');
                return item ? item.total_produced : 0;
            });
            const shiftBData = partLabels.map(label => {
                const item = topPartsRaw.find(d => d.part_name === label && d.shift === 'B');
                return item ? item.total_produced : 0;
            });

            new Chart(topPartsCtx, {
                type: 'bar',
                data: {
                    labels: partLabels,
                    datasets: [
                        { label: 'Shift A', data: shiftAData, backgroundColor: 'rgba(13, 110, 253, 0.7)' },
                        { label: 'Shift B', data: shiftBData, backgroundColor: 'rgba(25, 135, 84, 0.7)' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        } else if (topPartsCtx) {
            topPartsCtx.parentElement.innerHTML = '<div class="alert alert-info text-center d-flex align-items-center justify-content-center h-100">No production data for top parts in this period.</div>';
        }

        // --- Chart 2: Downtime by Reason ---
        const downtimeCtx = document.getElementById('downtimeReasonChart');
        if (downtimeCtx && downtimeRaw.length > 0) {
            const shiftA_downtime = downtimeRaw.find(d => d.shift === 'A') || {};
            const shiftB_downtime = downtimeRaw.find(d => d.shift === 'B') || {};
            delete shiftA_downtime.shift;
            delete shiftB_downtime.shift;

            const downtimeLabels = Object.keys(shiftA_downtime).length ? Object.keys(shiftA_downtime) : Object.keys(shiftB_downtime);
            const downtimeAData = Object.values(shiftA_downtime);
            const downtimeBData = Object.values(shiftB_downtime);

            new Chart(downtimeCtx, {
                type: 'bar',
                data: {
                    labels: downtimeLabels,
                    datasets: [
                        { label: 'Shift A (mins)', data: downtimeAData, backgroundColor: 'rgba(13, 110, 253, 0.7)' },
                        { label: 'Shift B (mins)', data: downtimeBData, backgroundColor: 'rgba(25, 135, 84, 0.7)' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        } else if (downtimeCtx) {
            downtimeCtx.parentElement.innerHTML = '<div class="alert alert-info text-center d-flex align-items-center justify-content-center h-100">No downtime data available for this period.</div>';
        }
    });
    </script>
</body>
</html>