<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .kpi-box {
            display: block;
            background-color: #fff; /* White background looks cleaner with charts */
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            text-decoration: none;
            color: #333;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .kpi-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
        }
        .kpi-box .kpi-value {
            font-size: 3.5em;
            font-weight: 700;
            line-height: 1.1;
        }
        .kpi-box .kpi-label {
            font-size: 1.2em;
            font-weight: 500;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        .kpi-box.blue .kpi-value { color: #007bff; }
        .kpi-box.green .kpi-value { color: #28a745; }
        .kpi-box.orange .kpi-value { color: #fd7e14; }
        .kpi-box.red .kpi-value { color: #dc3545; }

        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e3e3e3;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .chart-title {
            font-size: 1.1em;
            font-weight: 600;
            color: #555;
            margin-bottom: 15px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu');?>
    </header>

    <div class="wrapper">
        <div class="container-fluid" style="margin-top: 20px;">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h3 class="page-title text-center"><?php echo $page_title; ?></h3><hr>
                    </div>
                </div>
            </div>

            <?php
            $query_string = http_build_query([
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            ?>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <a class="kpi-box blue" href="<?php echo page_url;?>Dashboard/management_report_details?type=released&<?php echo $query_string;?>" target="_blank">
                        <div class="kpi-value"><?php echo $kpis['released']; ?></div>
                        <div class="kpi-label">DFs Released</div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a class="kpi-box orange" href="<?php echo page_url;?>Dashboard/management_report_details?type=assigned&<?php echo $query_string;?>" target="_blank">
                        <div class="kpi-value"><?php echo $kpis['assigned']; ?></div>
                        <div class="kpi-label">Tasks Assigned</div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a class="kpi-box green" href="<?php echo page_url;?>Dashboard/management_report_details?type=completed&<?php echo $query_string;?>" target="_blank">
                        <div class="kpi-value"><?php echo $kpis['completed']; ?></div>
                        <div class="kpi-label">Tasks Completed</div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a class="kpi-box red" href="<?php echo page_url;?>Dashboard/management_report_details?type=missed&<?php echo $query_string;?>" target="_blank">
                        <div class="kpi-value"><?php echo $kpis['missed']; ?></div>
                        <div class="kpi-label">Tasks Missed</div>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-container">
                        <h5 class="chart-title">Task Distribution Overview</h5>
                        <div style="height: 300px; position: relative;">
                            <canvas id="taskStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="chart-container">
                        <h5 class="chart-title">Daily Task Completion Trend</h5>
                        <div style="height: 300px; position: relative;">
                            <canvas id="taskTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div> 
    </div>

    <?php $this->load->view('common/footer');?>
    <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // --- DATA PREPARATION FOR CHARTS ---
            
            // 1. Pie Chart Data
            var assigned = <?php echo $kpis['assigned']; ?>;
            var completed = <?php echo $kpis['completed']; ?>;
            var missed = <?php echo $kpis['missed']; ?>;
            // Calculate 'Pending' as Assigned - Completed (Approximate for visualization)
            var pending = Math.max(0, assigned - completed); 

            // 2. Line Chart Data (Daily Trend)
            var trendLabels = [];
            var trendData = [];
            <?php 
            if(!empty($trend_data)) {
                foreach($trend_data as $row) {
                    // Format date for JS
                    echo "trendLabels.push('" . date('d M', strtotime($row['date'])) . "');\n";
                    echo "trendData.push(" . $row['count'] . ");\n";
                }
            }
            ?>

            // --- CHART 1: PIE CHART ---
            var ctxPie = document.getElementById('taskStatusChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut', // Doughnut looks more modern than Pie
                data: {
                    labels: ['Completed', 'Missed', 'Other Assigned'],
                    datasets: [{
                        data: [completed, missed, pending],
                        backgroundColor: [
                            '#28a745', // Green
                            '#dc3545', // Red
                            '#fd7e14'  // Orange
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // --- CHART 2: LINE CHART ---
            var ctxLine = document.getElementById('taskTrendChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Tasks Completed',
                        data: trendData,
                        borderColor: '#007bff', // Blue line
                        backgroundColor: 'rgba(0, 123, 255, 0.1)', // Light blue fill
                        borderWidth: 2,
                        tension: 0.4, // Smooth curves
                        fill: true,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 } // Ensure whole numbers for tasks
                        },
                        x: {
                            grid: { display: false } // Cleaner look
                        }
                    },
                    plugins: {
                        legend: { display: false } // Hide legend since there's only 1 line
                    }
                }
            });
        });
    </script>
</body>
</html>