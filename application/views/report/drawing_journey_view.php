<!DOCTYPE html>
<!-- application/views/reports/drawing_journey_view.php -->
<!-- NEW: Integrated DataTables.js for a searchable, sortable, and paginated table view. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drawing Journey: <?php echo html_escape($drawing_no); ?></title>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS for Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        body { 
            background-color: #f0f2f5; 
            font-family: 'Poppins', sans-serif;
            color: #495057;
        }
        .main-header {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .main-header .drawing-no {
            font-size: 2rem;
            font-weight: 700;
            color: #1a253c;
        }
        .main-header .part-name {
            font-size: 1.25rem;
            font-weight: 400;
            color: #6c757d;
        }
        .summary-card {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .summary-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: -webkit-linear-gradient(45deg, #007bff, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .summary-card .value {
            font-size: 1.75rem;
            font-weight: 600;
            color: #343a40;
        }
        .summary-card .label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 300;
        }
        
        /* Timeline Styles */
        .timeline {
            position: relative;
            padding: 2rem 0;
            list-style: none;
        }
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            left: 20px;
            height: 100%;
            width: 4px;
            background: #e9ecef;
            border-radius: 2px;
        }
        .timeline-item {
            margin-bottom: 2rem;
            position: relative;
            padding-left: 50px;
        }
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            z-index: 1;
        }
        .timeline-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .timeline-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .timeline-card h5 {
            font-weight: 600;
            color: #343a40;
        }
        .timeline-card .date {
            font-weight: 600;
            color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .detail-item {
            background-color: #f8f9fa;
            padding: 0.75rem;
            border-radius: 8px;
        }
        .detail-item .label {
            font-size: 0.8rem;
            color: #6c757d;
            display: block;
        }
        .detail-item .value {
            font-size: 1rem;
            font-weight: 600;
            color: #343a40;
        }
        
        /* Table View Styles */
        .table-view-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        /* Custom styling for DataTables search and length controls */
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.5rem;
        }

    </style>
</head>
<body>
    <div class="container my-5">
        <!-- Main Header -->
        <div class="main-header">
            <h1 class="drawing-no"><?php echo html_escape($drawing_no); ?> <a href="<?php echo page_url;?>Report"><span class="btn btn-primary" style="float:right" >Search Another Drawing </span></a></h1>
            <p class="part-name mb-0"><?php echo html_escape($part_name); ?></p>

        </div>

        <!-- Summary Cards -->
        <?php if (!empty($journey)): ?>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="summary-card">
                    <i class="bi bi-clock-history"></i>
                    <div class="value"><?php echo floor($total_journey_time / 60); ?>h <?php echo $total_journey_time % 60; ?>m</div>
                    <div class="label">Total Time Spent</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <i class="bi bi-box-seam"></i>
                    <div class="value"><?php echo html_escape($total_actual_qty); ?></div>
                    <div class="label">Total Parts Produced</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <i class="bi bi-list-ol"></i>
                    <div class="value"><?php echo html_escape($operation_count); ?></div>
                    <div class="label">Production Steps</div>
                </div>
            </div>
        </div>

        <!-- View Toggle Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0" style="font-weight: 600;">Production Journey</h3>
            <div class="btn-group" role="group" aria-label="View toggle">
                <button type="button" class="btn btn-primary" id="timeline-btn"><i class="bi bi-bar-chart-steps"></i> Timeline View</button>
                <button type="button" class="btn btn-outline-primary" id="table-btn"><i class="bi bi-table"></i> Table View</button>
            </div>
        </div>

        <!-- Timeline View Container -->
        <div id="timeline-view">
            <ul class="timeline">
                <?php foreach ($journey as $entry): ?>
                    <li class="timeline-item">
                        <div class="timeline-icon"><i class="bi bi-gear-fill"></i></div>
                        <div class="timeline-card">
                            <div class="timeline-card-header">
                                <h5><?php echo html_escape($entry->machine_no); ?> <small class="text-muted fw-normal">by <?php echo html_escape($entry->operator_name); ?></small></h5>
                                <span class="date"><?php echo html_escape(date('d-M-Y', strtotime($entry->working_date))); ?></span>
                            </div>
                            <p class="mb-3"><strong>Operation:</strong> <?php echo html_escape($entry->operations); ?></p>
                            <div class="details-grid">
                                <div class="detail-item"><span class="label">Time Taken</span><span class="value"><?php echo html_escape($entry->cycle_time); ?> min</span></div>
                                <div class="detail-item"><span class="label">DF No.</span><span class="value"><?php echo html_escape($entry->df_no); ?></span></div>
                                <div class="detail-item"><span class="label">Planned Qty</span><span class="value"><?php echo html_escape($entry->planned_qty); ?></span></div>
                                <div class="detail-item"><span class="label">Actual Qty</span><span class="value"><?php echo html_escape($entry->actual_qty); ?></span></div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- Table View Container -->
        <div id="table-view" class="table-view-container" style="display: none;">
            <div class="table-responsive">
                <table id="production-table" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Machine No.</th>
                            <th>Operator</th>
                            <th>Operation</th>
                            <th>Time (Min)</th>
                            <th>DF No.</th>
                            <th>Planned</th>
                            <th>Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($journey as $entry): ?>
                            <tr>
                                <td><?php echo html_escape(date('Y-m-d', strtotime($entry->working_date))); ?></td>
                                <td><?php echo html_escape($entry->machine_no); ?></td>
                                <td><?php echo html_escape($entry->operator_name); ?></td>
                                <td><?php echo html_escape($entry->operations); ?></td>
                                <td><?php echo html_escape($entry->cycle_time); ?></td>
                                <td><?php echo html_escape($entry->df_no); ?></td>
                                <td><?php echo html_escape($entry->planned_qty); ?></td>
                                <td><?php echo html_escape($entry->actual_qty); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
            <div class="card p-5 text-center">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No Data Found</h4>
                <p class="text-muted">No production data was found for drawing number: <strong><?php echo html_escape($drawing_no); ?></strong></p>
            </div>
        <?php endif; ?>

         <div class="text-center mt-5">
             <a href="javascript:history.back()" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Go Back</a>
        </div>
    </div>

    <!-- Core JS libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS for Bootstrap 5 -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        const productionTable = $('#production-table').DataTable({
            "pageLength": 10, // Default number of rows to show
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ] // Options for number of rows
        });

        // Cache jQuery selectors
        const $timelineBtn = $('#timeline-btn');
        const $tableBtn = $('#table-btn');
        const $timelineView = $('#timeline-view');
        const $tableView = $('#table-view');

        // Event listener for timeline button
        $timelineBtn.on('click', function() {
            $timelineView.show();
            $tableView.hide();
            
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $tableBtn.removeClass('btn-primary').addClass('btn-outline-primary');
        });

        // Event listener for table button
        $tableBtn.on('click', function() {
            $tableView.show();
            $timelineView.hide();

            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $timelineBtn.removeClass('btn-primary').addClass('btn-outline-primary');
        });
    });
    </script>
</body>
</html>
