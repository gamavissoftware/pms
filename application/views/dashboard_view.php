<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .kpi-card { border-left: 5px solid #0d6efd; padding: 1.5rem; margin-bottom: 1rem; background-color: #f8f9fa; }
        .kpi-card .value { font-size: 2.5rem; font-weight: bold; }
        .kpi-card .title { font-size: 1rem; color: #6c757d; }
        .chart-container { padding: 2rem; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.05); min-height: 400px; display: flex; align-items: center; justify-content: center; }
        .filter-form { background-color: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-2"><?php echo $page_title; ?></h1>
        <p class="text-muted mb-4"><?php echo $report_period; ?></p>

        <div class="filter-form shadow-sm">
            <form action="<?php echo page_url.'Reporting_dashboard'; ?>" method="post" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                </div>
            </form>
        </div>

        <div class="row row-cols-1 row-cols-md-5 g-4">
            <div class="col">
                <div class="kpi-card shadow-sm h-100">
                    <div class="value"><?php echo number_format($kpis['total_produced']); ?></div>
                    <div class="title">Total Production</div>
                </div>
            </div>
            <div class="col">
                <div class="kpi-card shadow-sm h-100" style="border-left-color: #198754;">
                    <div class="value"><?php echo $kpis['performance_efficiency']; ?>%</div>
                    <div class="title">Overall Performance</div>
                </div>
            </div>
            <div class="col">
                <div class="kpi-card shadow-sm h-100" style="border-left-color: #20c997;">
                    <div class="value"><?php echo $kpis['quality_yield_percentage']; ?>%</div>
                    <div class="title">Overall Quality</div>
                </div>
            </div>
            <div class="col">
                <div class="kpi-card shadow-sm h-100" style="border-left-color: #ffc107;">
                    <div class="value"><?php echo $kpis['plan_achievement_percentage']; ?>%</div>
                    <div class="title">Plan Achievement</div>
                </div>
            </div>
            <div class="col">
                <div class="kpi-card shadow-sm h-100" style="border-left-color: #dc3545;">
                    <div class="value"><?php echo round($kpis['total_downtime_minutes'] / 60, 1); ?> Hrs</div>
                    <div class="title">Total Downtime</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="chart-container">
                    <h5>Daily Production Trend</h5>
                    <canvas id="dailyProductionChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h5>Quality Overview</h5>
                    <canvas id="qualityPieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="row mt-4 mb-5">
            <div class="col-md-12">
                <div class="chart-container">
                    <h5>Top 5 Downtime Reasons (in Minutes)</h5>
                    <canvas id="downtimeBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    // 1. Chart for Daily Production Trend (Line Chart)
    const dailyTrendCanvas = document.getElementById('dailyProductionChart');
    const dailyTrendData = <?php echo json_encode($daily_trend_data); ?>;
    
    if (dailyTrendData && dailyTrendData.data && dailyTrendData.data.length > 0) {
        new Chart(dailyTrendCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: dailyTrendData.labels,
                datasets: [{
                    label: 'Units Produced',
                    data: dailyTrendData.data,
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: { 
                scales: { 
                    y: { 
                        beginAtZero: true 
                    } 
                } 
            }
        });
    } else {
        dailyTrendCanvas.parentElement.innerHTML = '<p class="text-muted">No production data available for this period.</p>';
    }

    // 2. Chart for Quality Overview (Pie Chart)
    const qualityCanvas = document.getElementById('qualityPieChart');
    const kpiData = <?php echo json_encode($kpis); ?>;

    if (kpiData && kpiData.total_produced > 0) {
        new Chart(qualityCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Good Parts', 'Rejected Parts'],
                datasets: [{
                    data: [ kpiData.total_good_parts, kpiData.total_rejected ],
                    backgroundColor: ['#20c997', '#dc3545'],
                    hoverOffset: 4
                }]
            }
        });
    } else {
        qualityCanvas.parentElement.innerHTML = '<p class="text-muted">No quality data to display.</p>';
    }

    // 3. Chart for Downtime Reasons (Bar Chart)
    const downtimeCanvas = document.getElementById('downtimeBarChart');
    const downtimeData = <?php echo json_encode($downtime_data); ?>;
    
    const hasDowntimeData = downtimeData && Object.values(downtimeData).some(v => parseFloat(v) > 0);

    if (hasDowntimeData) {
        new Chart(downtimeCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: Object.keys(downtimeData),
                datasets: [{
                    label: 'Downtime in Minutes',
                    data: Object.values(downtimeData),
                    backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(255, 159, 64, 0.5)', 'rgba(255, 205, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(54, 162, 235, 0.5)'],
                    borderColor: ['rgb(255, 99, 132)','rgb(255, 159, 64)','rgb(255, 205, 86)','rgb(75, 192, 192)','rgb(54, 162, 235)'],
                    borderWidth: 1
                }]
            },
            options: { 
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    } else {
        downtimeCanvas.parentElement.innerHTML = '<p class="text-muted">No downtime recorded for this period.</p>';
    }
    </script>
</body>
</html>