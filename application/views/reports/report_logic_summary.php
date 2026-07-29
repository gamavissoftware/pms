<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Report Logic Summary</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body { background-color: #f4f7f6; }
        .card-box { 
            background-color: #ffffff; 
            border-radius: 12px; 
            border: 1px solid #e9ecef; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            padding: 25px; 
            margin-bottom: 25px; 
            height: 100%; /* Makes cards in a row equal height */
        }
        .metric-card {
            background: #fdfdfd;
            border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .metric-card.metric-loss { border-left-color: #a94442; }
        .metric-card.metric-time { border-left-color: #ffc107; }
        .metric-card.metric-report { border-left-color: #28a745; }

        .metric-card h3 {
            font-size: 1.25em;
            font-weight: 600;
            color: #333;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .metric-card p {
            color: #555;
            margin-bottom: 10px;
        }
        .metric-card code {
            background-color: #e9ecef;
            color: #4b5563;
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: 600;
            font-family: monospace;
        }
        .metric-card ul {
            padding-left: 20px;
            color: #555;
        }
        .report-section {
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .report-section:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Report Logic & Calculations Guide</h4>
                    </div>
                </div>
            </div>

            <div class="report-section">
                <h2 class="m-t-0 header-title"><b><i class="fa fa-line-chart"></i> Task Delay Report</b></h2>
                <p class="text-muted m-b-20">This report provides a financial and statistical overview of all task delays across the organization.</p>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card-box metric-card">
                            <h3 class="text-primary"><i class="fa fa-filter"></i> Filters Explained</h3>
                            <p>The report uses a primary filter logic:</p>
                            <ul>
                                <li><b>Default View:</b> Shows the <b>Current Financial Year</b> automatically.</li>
                                <li><b>Financial Year:</b> Overrides date pickers and finds all tasks with a <code>Planned End Date</code> in that FY.</li>
                                <li><b>Date Pickers:</b> Overrides the FY filter for a custom date range.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box metric-card metric-report">
                            <h3 style="color: #28a745;"><i class="fa fa-calendar-check-o"></i> FY Summary Stats</h3>
                            <ul>
                                <li><b>Total Released:</b> A count of all DFs with a <code>DF Release Date</code> within the selected FY.</li>
                                <li><b>Dispatched (in FY):</b> Count of "Total Released" DFs where the "Dispatch" task (`taskid = 103`) was completed before the FY end date.</li>
                                <li><b>Carried Forward:</b> <code>Total Released - Dispatched</code>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box metric-card metric-loss">
                            <h3 style="color: #a94442;"><i class="fa fa-calculator"></i> Est. Loss (Opportunity Cost)</h3>
                            <p>Estimates the financial impact of a delay based on an <strong>8% annual opportunity cost</strong> of the project's capital.</p>
                            <p><b>Formula:</b></p>
                            <code>(Order Value * 0.08 / 365) * Delay Days</code>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card-box metric-card metric-time">
                            <h3 style="color: #ffc107;"><i class="fa fa-bell"></i> Proactive "At-Risk"</h3>
                            <p>This widget shows the Top 5 tasks that are <b>not yet late</b> but are due for completion within the <b>next 7 days</b>, allowing managers to act before they become a problem.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box metric-card metric-report">
                            <h3 style="color: #28a745;"><i class="fa fa-pie-chart"></i> Root Cause Analysis</h3>
                            <p>This chart groups all <b>open tickets on delayed tasks</b> by their "Reason for Delay" (e.g., "Material Shortage"). This shows *why* delays are happening, not just who is delayed.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box metric-card">
                            <h3 class="text-primary"><i class="fa fa-hand-paper-o"></i> Bottleneck ID</h3>
                            <p>The "Owner / Bottleneck" column identifies who is responsible for a task. If the status is <code>Pending Approval</code>, it will show the <b>Team Leader</b> who needs to approve it, rather than the employee who has already finished.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="report-section">
                <h2 class="m-t-0 header-title"><b><i class="fa fa-users"></i> Team Dashboard</b></h2>
                <p class="text-muted m-b-20">This dashboard provides a performance summary of all users, grouped by department. Access is restricted by user role.</p>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card-box metric-card">
                            <h3 class="text-primary"><i class="fa fa-shield"></i> Access Logic</h3>
                            <ul>
                                <li><b>Super Admins:</b> Can see all departments or filter to a specific one.</li>
                                <li><b>User 167 (Special):</b> View is locked to the "Design" department (ID 11).</li>
                                <li><b>Team Leaders/Dept. Heads:</b> View is locked to their single department.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box metric-card metric-time">
                            <h3 style="color: #ffc107;"><i class="fa fa-percent"></i> Key MIS Metrics</h3>
                            <ul>
                                <li><b>On-Time %:</b> <code>(Tasks Done On-Time / Total Done) * 100</code></li>
                                <li><b>% Work Delayed:</b> <code>(Tasks Done Late / Total Done) * 100</code></li>
                                <li><b>% Work Pending:</b> <code>(Total Pending Tasks / Total Assigned Tasks) * 100</code></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box metric-card">
                            <h3 class="text-primary"><i class="fa fa-mouse-pointer"></i> Drill-Down Reports</h3>
                            <p>Clicking the numbers (Assigned, Done, Pending, Delayed) opens a <b>new report tab</b> showing the exact list of tasks, including start/end dates, status, and any open tickets.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="m-t-0 header-title"><b><i class="fa fa-archive"></i> Closed DF Report</b></h2>
                <p class="text-muted m-b-20">This report provides a summary of all fully completed DFs and their overall project timeline.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-box metric-card metric-time">
                            <h3 style="color: #ffc107;"><i class="fa fa-calendar"></i> Key Date Logic</h3>
                            <ul>
                                <li><b>DF Release Date:</b> The <code>added_on</code> date from the <code>df_release</code> table. Used by the FY filter.</li>
                                <li><b>System Planned Date of Closer:</b> The original <code>end_date</code> of the "Dispatch" task (`taskid = 103`).</li>
                                <li><b>Actual Completion Date:</b> The <code>task_completed_on</code> date of the *very last task* to be completed for that DF.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-box metric-card metric-loss">
                            <h3 style="color: #a94442;"><i class="fa fa-calculator"></i> DF Delay & Loss</h3>
                            <p>These are calculated for the *entire project*, not individual tasks.</p>
                            <ul>
                                <li><b>DF Delayed:</b> <code>(Actual Completion Date) - (System Planned Date of Closer)</code></li>
                                <li><b>Loss Against DF:</b> Uses the same 8% Opportunity Cost formula, applied over the total number of days the *entire project* was delayed.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>