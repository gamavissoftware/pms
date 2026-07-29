<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();

// Calculations for the Dashboard metrics
$total_opps = isset($funnel->total_opps) ? (int)$funnel->total_opps : 0;
$won_count = isset($funnel->won_count) ? (int)$funnel->won_count : 0; // Strictly based on Stage 10
$conv_rate = ($total_opps > 0) ? ($won_count / $total_opps) * 100 : 0;
$active_running = isset($running_orders) ? count($running_orders) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Spares Division KPI Report</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f9; color: #444; }
        .page-title { font-weight: 700; color: <?php echo $company_info->colorcode ?? '#333'; ?>; }
        .card-box { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; background: #fff; }
        
        /* KPI Dashboard Cards */
        .kpi-card { text-align: center; padding: 20px; border-radius: 12px; transition: transform 0.3s; position: relative; overflow: hidden; border-bottom: 4px solid #eee; }
        .kpi-card:hover { transform: translateY(-5px); }
        .kpi-card h2 { font-size: 32px; font-weight: 700; margin: 10px 0 5px; }
        .kpi-card p { text-transform: uppercase; font-size: 11px; letter-spacing: 1px; color: #888; font-weight: 600; margin-bottom: 0; }
        .kpi-card .icon-bg { position: absolute; right: -10px; bottom: -10px; font-size: 60px; opacity: 0.05; }
        
        /* Pipeline Breakdown Styling */
        .pipeline-item { padding: 12px 15px; border-radius: 8px; background: #f9f9f9; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #ddd; }
        .pipeline-stage-name { font-size: 13px; font-weight: 500; color: #555; }
        
        /* General Layout Helpers */
        .filter-bar { background: #fff; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .vertical-middle { vertical-align: middle !important; }
        .progress-xs { height: 6px !important; border-radius: 10px; }
        .progress-bar-purple { background-color: #9b59b6; }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Spares Division KPI Performance Insights</h4>
                    </div>
                </div>
            </div>

            <div class="filter-bar">
                <form class="form-inline" method="GET" action="<?php echo page_url; ?>Spares/kpi_report">
                    <div class="form-group m-r-15">
                        <label class="m-r-10">From:</label>
                        <input type="date" name="from_date" class="form-control input-sm" value="<?php echo $from_date; ?>">
                    </div>
                    <div class="form-group m-r-15">
                        <label class="m-r-10">To:</label>
                        <input type="date" name="to_date" class="form-control input-sm" value="<?php echo $to_date; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm waves-effect m-r-5"><i class="fa fa-refresh"></i> Update Dashboard</button>
                    <a href="<?php echo page_url; ?>Spares/kpi_report" class="btn btn-default btn-sm waves-effect">Reset</a>
                </form>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card-box kpi-card" style="border-bottom-color: #4a90e2;">
                        <p>Total Enquiries</p>
                        <h2><?php echo number_format($total_opps); ?></h2>
                        <i class="fa fa-bullseye icon-bg"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-box kpi-card" style="border-bottom-color: #2ecc71;">
                        <p>Orders Won (Stage 10)</p>
                        <h2><?php echo number_format($won_count); ?></h2>
                        <i class="fa fa-trophy icon-bg"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-box kpi-card" style="border-bottom-color: #f39c12;">
                        <p>Conversion Ratio</p>
                        <h2><?php echo number_format($conv_rate, 1); ?>%</h2>
                        <i class="fa fa-line-chart icon-bg"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-box kpi-card" style="border-bottom-color: #9b59b6;">
                        <p>Active Load (Running)</p>
                        <h2><?php echo $active_running; ?></h2>
                        <i class="fa fa-truck icon-bg"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card-box">
                        <h4 class="m-t-0 m-b-20 header-title"><b>Executive Revenue Performance</b></h4>
                        <div class="table-responsive">
                            <table class="table table-hover m-b-0">
                                <thead>
                                    <tr>
                                        <th>Marketing Executive</th>
                                        <th class="text-center">Won</th>
                                        <th class="text-right">Total Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($user_performance)): foreach($user_performance as $user): ?>
                                    <tr>
                                        <td><b style="color: #333;"><?php echo htmlspecialchars($user->name); ?></b></td>
                                        <td class="text-center"><span class="badge badge-inverse"><?php echo $user->sales_count; ?></span></td>
                                        <td class="text-right text-success"><b>₹ <?php echo number_format($user->total_revenue, 2); ?></b></td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="3" class="text-center text-muted">No sales recorded for this period.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card-box">
                        <h4 class="m-t-0 m-b-20 header-title"><b>Active Sales Pipeline</b></h4>
                        <?php if(!empty($pipeline_breakdown)): foreach($pipeline_breakdown as $pipe): ?>
                        <div class="pipeline-item" style="border-left-color: <?php echo $company_info->colorcode ?? '#444'; ?>;">
                            <span class="pipeline-stage-name"><?php echo htmlspecialchars($pipe->stage ?? 'Initial Enquiry'); ?></span>
                            <span class="badge badge-primary"><?php echo $pipe->count; ?></span>
                        </div>
                        <?php endforeach; else: ?>
                        <div class="text-center text-muted p-t-20">No active enquiries.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card-box">
                        <h4 class="m-t-0 m-b-20 header-title"><b>Revenue by Market</b></h4>
                        <?php if(!empty($revenue_split)): foreach($revenue_split as $split): 
                            $is_exp = ($split->op_type != 1); 
                        ?>
                        <div class="m-b-25">
                            <p class="m-b-5"><b><?php echo $is_exp ? 'Export (USD)' : 'Domestic (INR)'; ?></b> <span class="pull-right small text-muted"><?php echo $split->vol; ?> Orders</span></p>
                            <h3 class="m-t-0"><b><?php echo $is_exp ? '$' : '₹'; ?> <?php echo number_format($split->total_val, 2); ?></b></h3>
                            <div class="progress progress-xs m-b-0">
                                <div class="progress-bar <?php echo $is_exp ? 'progress-bar-purple' : 'progress-bar-primary'; ?>" role="progressbar" style="width: 100%;"></div>
                            </div>
                        </div>
                        <?php endforeach; else: ?>
                        <div class="text-center text-muted p-t-20"><p>No revenue split data found.</p></div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info small m-t-10 m-b-0">
                            <i class="fa fa-info-circle"></i> Won Orders are counted based on reaching <b>Stage ID 10</b> in history.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php $this->load->view('common/footer'); ?>
</body>
</html>