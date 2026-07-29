<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; User Performance Report</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    
    <style>
        body { background-color: #f4f7f6; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
        .filter-card { background-color: #f8f9fa; border: 1px solid #dee2e6; }
        .form-inline .form-group { margin-bottom: 10px; vertical-align: middle; }

        /* Star Performer Card */
        .star-performer-card {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(145deg, #fdfbfb, #ebedee);
            border-radius: 12px;
            border: 1px solid #d1d9e6;
            box-shadow: 5px 5px 10px #d1d9e6, -5px -5px 10px #ffffff;
        }
        .star-performer-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            object-fit: cover;
        }
        .star-performer-card h3 {
            font-size: 1.5em;
            font-weight: 600;
            color: #333;
            margin-top: 0;
            margin-bottom: 5px;
        }
        .star-performer-card .designation {
            font-size: 1em;
            font-weight: 500;
            color: #007bff;
            margin-bottom: 20px;
        }
        .star-performer-card .star-badge {
            font-size: 1.2em;
            font-weight: 600;
            color: #ffc107;
            margin-bottom: 25px;
        }
        .star-performer-card .stats-box {
            text-align: left;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .stats-box .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95em;
        }
        .stats-box .stat-row:last-child { margin-bottom: 0; }
        .stats-box .stat-label { color: #555; }
        .stats-box .stat-value { font-weight: 600; color: #333; }
        .stats-box .stat-value.on-time { color: #28a745; font-size: 1.3em; }
        .stats-box .stat-value.delayed-rate { color: #dc3545; }
        .stats-box .stat-value.pending-rate { color: #ffc107; }

        /* Other Performers List */
        .other-performers-list { list-style: none; padding-left: 0; }
        .other-performers-list li {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease-in-out;
        }
        .other-performers-list li:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.07);
            transform: translateY(-2px);
        }
        .other-performers-list .rank {
            font-size: 1.2em;
            font-weight: 600;
            color: #999;
            width: 40px;
        }
        .other-performers-list .user-info {
            flex-grow: 1;
            padding: 0 15px;
        }
        .other-performers-list .user-name {
            font-size: 1.1em;
            font-weight: 600;
            color: #333;
        }
        .other-performers-list .user-designation {
            font-size: 0.9em;
            color: #777;
        }
        .other-performers-list .user-stats {
            display: flex;
            text-align: center;
            min-width: 280px;
        }
        .other-performers-list .stat-item {
            width: 33.33%;
            padding: 0 10px;
        }
        .other-performers-list .stat-item-label {
            font-size: 0.8em;
            color: #888;
            text-transform: uppercase;
        }
        .other-performers-list .stat-item-value {
            font-size: 1.3em;
            font-weight: 600;
        }
        .other-performers-list .on-time-val { color: #28a745; }
        .other-performers-list .delayed-val { color: #dc3545; }
        .other-performers-list .pending-val { color: #ffc107; }

    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-12"><div class="page-title-box"><h4 class="page-title">User Performance Report</h4></div></div></div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box filter-card">
                        <h4 class="m-t-0 header-title"><b><i class="fa fa-filter"></i> Filter Report</b></h4>
                        <p class="text-muted m-b-20">Filter performance data by date range and/or department.</p>
                        
                        <form method="post" action="<?php echo page_url; ?>Df_reports/user_appraisal_report" class="form-inline">
                            <div class="form-group m-r-10">
                                <label for="start_date" class="m-r-10">From:</label>
                                <input type="text" class="form-control datepicker" name="start_date" id="start_date" value="<?php echo htmlspecialchars($selected_start_date); ?>" placeholder="Start Date">
                            </div>
                            <div class="form-group m-r-10">
                                <label for="end_date" class="m-r-10">To:</label>
                                <input type="text" class="form-control datepicker" name="end_date" id="end_date" value="<?php echo htmlspecialchars($selected_end_date); ?>" placeholder="End Date">
                            </div>
                            <div class="form-group m-r-10">
                                <label for="department_id" class="m-r-10">Department:</label>
                                <select name="department_id" id="department_id" class="form-control">
                                    <option value="">All Departments</option>
                                    <?php foreach($departments as $dept): ?>
                                        <option value="<?php echo $dept['department_id']; ?>" <?php echo ($dept['department_id'] == $selected_department_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['department']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-search"></i> View Report</button>
                            <a href="<?php echo page_url; ?>Df_reports/user_appraisal_report" class="btn btn-default waves-effect waves-light m-l-5"><i class="fa fa-refresh"></i> Reset</a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                     <h4 class="text-center"><strong><?php echo $page_title; ?></strong></h4><hr>
                </div>
            </div>

            <?php if (empty($performance_data)): ?>
                <div class="card-box"><div class="text-center"><i class="fa fa-info-circle fa-3x text-muted"></i><h4 class="m-t-20">No Performance Data Found</h4><p class="text-muted">No user performance data was found for the selected criteria.</p></div></div>
            <?php else: ?>
                <?php foreach ($performance_data as $dept_id => $data): ?>
                    <div class="card-box">
                        <h3 class="m-t-0 header-title"><b>Department: <?php echo htmlspecialchars($data['department_name']); ?></b></h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <?php $user = $data['star_performer']; ?>
                                <div class="star-performer-card">
                                    <img src="<?php echo !empty($user['profile_pic']) ? assets_url . 'images/users/' . $user['profile_pic'] : assets_url . 'images/users/default.png'; ?>" alt="Star Performer">
                                    <h3><?php echo htmlspecialchars($user['user_name']); ?></h3>
                                    <p class="designation"><?php echo htmlspecialchars($user['designation']); ?></p>
                                    <div class="star-badge"><i class="fa fa-star"></i> STAR PERFORMER <i class="fa fa-star"></i></div>
                                    
                                    <div class="stats-box">
                                        <div class="stat-row">
                                            <span class="stat-label">On-Time Performance</span>
                                            <span class="stat-value on-time"><?php echo $user['on_time_percent']; ?>%</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">% of Work Delayed</span>
                                            <span class="stat-value delayed-rate"><?php echo $user['percent_work_delayed']; ?>%</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">% of Work Pending</span>
                                            <span class="stat-value pending-rate"><?php echo $user['percent_not_done']; ?>%</span>
                                        </div>
                                        <hr style="margin: 8px 0;">
                                        <div class="stat-row">
                                            <span class="stat-label">Total Completed</span>
                                            <span class="stat-value"><?php echo $user['total_completed']; ?></span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">Completed (On Time)</span>
                                            <span class="stat-value"><?php echo $user['tasks_on_time']; ?></span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-label">Completed (Delayed)</span>
                                            <span class="stat-value"><?php echo $user['tasks_delayed_closed']; ?></span>
                                        </div>
                                        <hr style="margin: 8px 0;">
                                        <div class="stat-row">
                                            <span class="stat-label">Pending (Delayed)</span>
                                            <span class="stat-value"><?php echo $user['tasks_pending_delayed']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <ul class="other-performers-list">
                                    <?php if (empty($data['other_performers'])): ?>
                                        <p class="text-muted">No other users found for this department.</p>
                                    <?php else: ?>
                                        <?php foreach ($data['other_performers'] as $index => $user): ?>
                                            <li>
                                                <div class="rank">#<?php echo $index + 2; ?></div>
                                                <div class="user-info">
                                                    <div class="user-name"><?php echo htmlspecialchars($user['user_name']); ?></div>
                                                    <div class="user-designation"><?php echo htmlspecialchars($user['designation']); ?></div>
                                                </div>
                                                <div class="user-stats">
                                                    <div class="stat-item">
                                                        <div class="stat-item-label">On-Time</div>
                                                        <div class="stat-item-value on-time-val"><?php echo $user['on_time_percent']; ?>%</div>
                                                    </div>
                                                    <div class="stat-item">
                                                        <div class="stat-item-label">% Delayed</div>
                                                        <div class="stat-item-value delayed-val"><?php echo $user['percent_work_delayed']; ?>%</div>
                                                    </div>
                                                    <div class="stat-item">
                                                        <div class="stat-item-label">% Pending</div>
                                                        <div class="stat-item-value pending-val"><?php echo $user['percent_not_done']; ?>%</div>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
    
    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    
   <script>
        $(document).ready(function() {
            // Initialize Datepickers
            $('.datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });
        });
    </script>
</body>
</html>