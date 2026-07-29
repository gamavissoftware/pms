<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .main-header {
            color: #343a40;
        }
        .report-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .report-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .report-card .card-body {
            padding: 2rem;
        }
        .card-icon {
            font-size: 3rem;
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin: 0 auto 1.5rem auto;
            display: block;
            background-color: rgba(0,0,0,0.05);
            color: var(--bs-primary);
        }
        .card-title {
            font-weight: 600;
        }
        .btn-custom {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .select2-container--bootstrap-5 .select2-selection {
            padding: 0.6rem 1rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold main-header">Factory Production Dashboard</h1>
            <p class="lead text-muted">Navigate to any report for a detailed performance analysis.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="report-card bg-primary text-white text-center shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-pie-chart-fill" style="font-size: 3rem;"></i>
                        <h2 class="card-title mt-3">Executive Summary</h2>
                        <p class="card-text opacity-75">High-level overview of all key performance indicators.</p>
                        <a href="<?php echo page_url.'Reporting_dashboard'; ?>" class="btn btn-light btn-custom mt-3">View Master Report</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="report-card bg-dark text-white text-center shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-people-fill" style="font-size: 3rem;"></i>
                        <h2 class="card-title mt-3">Shift Comparison</h2>
                        <p class="card-text opacity-75">Compare the performance and output of Shift A vs. Shift B.</p>
                        <button onclick="goToShiftReport()" class="btn btn-info btn-custom mt-3">Compare Shifts</button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title text-center mb-3"><i class="bi bi-calendar-range me-2"></i>Select Date Range for Detailed Reports</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control form-control-lg" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control form-control-lg" id="end_date" name="end_date" value="<?php echo date('Y-m-t'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="report-card bg-white shadow">
                    <div class="card-body text-center">
                        <span class="card-icon text-success"><i class="bi bi-gear-wide-connected"></i></span>
                        <h4 class="card-title">Machine Reports</h4>
                        <p class="card-text text-muted">Drill down into the performance and downtime of a specific machine.</p>
                        <div class="mt-4">
                            <select id="machine-select" class="form-select">
                                <option></option> <?php foreach ($machines as $machine): ?>
                                    <option value="<?php echo urlencode($machine['machine_no']); ?>"><?php echo html_escape($machine['machine_no']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button onclick="goToReport('machine')" class="btn btn-success btn-custom w-100 mt-3">Analyze Machine</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="report-card bg-white shadow">
                    <div class="card-body text-center">
                        <span class="card-icon text-info"><i class="bi bi-box-seam"></i></span>
                        <h4 class="card-title">Part Reports</h4>
                        <p class="card-text text-muted">Analyze the quality, efficiency, and volume for a specific product.</p>
                        <div class="mt-4">
                            <select id="part-select" class="form-select">
                                <option></option> <?php foreach ($parts as $part): ?>
                                    <option value="<?php echo urlencode($part['part_name']); ?>"><?php echo html_escape($part['part_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button onclick="goToReport('part')" class="btn btn-info btn-custom w-100 mt-3">Analyze Part</button>
                    </div>
                </div>
            </div>

          <div class="col-lg-4 col-md-12">
                 <div class="report-card bg-white shadow h-100">
                    <div class="card-body text-center">
                        <span class="card-icon text-warning"><i class="bi bi-hash"></i></span>
                        <h4 class="card-title">Search by Drawing No.</h4>
                        <div class="mt-4">
                            <select id="drawing-no-select" class="form-select">
                                <option></option>
                                <?php foreach ($parts as $part): ?>
                                    <option value="<?php echo urlencode($part['drawing_no']); ?>"><?php echo html_escape($part['drawing_no']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button onclick="goToReport('part', 'drawing-no-select')" class="btn btn-warning btn-custom w-100 mt-3">Analyze Part</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#machine-select').select2({
                theme: "bootstrap-5",
                placeholder: "Type to search for a machine...",
            });
            $('#part-select').select2({
                theme: "bootstrap-5",
                placeholder: "Type to search for a part...",
            });
            $('#operator-select').select2({
                theme: "bootstrap-5",
                placeholder: "Type to search for an operator...",
            });
        });

        // Function for the new Shift Report button
        function goToShiftReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            if (!startDate || !endDate) {
                alert('Please select both a Start Date and an End Date.');
                return;
            }
            const baseUrl = "<?php echo page_url.'reports/shifts'; ?>";
            const finalUrl = `${baseUrl}?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = finalUrl;
        }

        // Function for the individual detailed reports
        function goToReport(reportType) {
            const selectElement = document.getElementById(reportType + '-select');
            const selectedValue = selectElement.value;
            
            // Get date values
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (!startDate || !endDate) {
                alert('Please select both a Start Date and an End Date.');
                return;
            }

            if (selectedValue) {
                const baseUrl = "<?php echo page_url.'reports'; ?>";
                // Append dates as query parameters
                const finalUrl = `${baseUrl}/${reportType}/${selectedValue}?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = finalUrl;
            } else {
                alert('Please select an option from the dropdown menu.');
            }
        }
    </script>
</body>
</html>