<?php 
$lead_stage = $this->uri->segment(3);
$current_url = page_url.'Leads/lead_stages/'.$lead_stage;
// echo $current_url;exit;
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Dashboard_model');
$getLeadStageDetails = $CI->Salescrm_model->getLeadStageDetails($lead_stage);
foreach ($getLeadStageDetails as $row);
$quotation_step = $row->quotation_step;
$quotation_revised_step = $row->quotation_revised_step;
$pi_step = $row->pi_step;
$pi_revised_step = $row->pi_revised_step;
$lead_status=$row->lead_name;
$reason=$row->reason;
$quotestep=$CI->Salescrm_model->checkforquotationoraheadstep($lead_stage);
$getConversionLeadStage=$CI->Dashboard_model->getConversionLeadStage();
$selected_source_label = 'All Sources';
$selected_exhibition_label = 'All Exhibitions';
$selected_manager_label = 'All Managers';
$selected_date_label = 'All Dates';
$active_stage_filter_count = 0;
$manager_drilldown_query = '';

if (!empty($stage_filter_options['lead_sources'])) {
    foreach ($stage_filter_options['lead_sources'] as $source_option) {
        if ((string) $source_option->source_id === (string) $stage_filters['source_id']) {
            $selected_source_label = $source_option->lead_source;
            break;
        }
    }
}

if (!empty($stage_filter_options['exhibitions'])) {
    foreach ($stage_filter_options['exhibitions'] as $exhibition_option) {
        if ((string) $exhibition_option->id === (string) $stage_filters['exhibition_id']) {
            $selected_exhibition_label = $exhibition_option->exhibition;
            break;
        }
    }
}

if (!empty($stage_filter_options['managers'])) {
    foreach ($stage_filter_options['managers'] as $manager_option) {
        if ((string) $manager_option->user_id === (string) $stage_filters['manager_id']) {
            $selected_manager_label = ucwords(strtolower(trim($manager_option->title . ' ' . $manager_option->first_name . ' ' . $manager_option->last_name)));
            break;
        }
    }
}

if (!empty($stage_filters['start_date_input']) && !empty($stage_filters['end_date_input'])) {
    $selected_date_label = $stage_filters['start_date_input'] . ' to ' . $stage_filters['end_date_input'];
} else if (!empty($stage_filters['start_date_input'])) {
    $selected_date_label = 'From ' . $stage_filters['start_date_input'];
} else if (!empty($stage_filters['end_date_input'])) {
    $selected_date_label = 'Till ' . $stage_filters['end_date_input'];
}

if (!empty($stage_filters['start_date_input'])) {
    $active_stage_filter_count++;
}
if (!empty($stage_filters['end_date_input'])) {
    $active_stage_filter_count++;
}
if (!empty($stage_filters['source_id'])) {
    $active_stage_filter_count++;
}
if (!empty($stage_filters['exhibition_id'])) {
    $active_stage_filter_count++;
}
if (!empty($stage_filters['manager_id'])) {
    $active_stage_filter_count++;
}

$manager_drilldown_filters = array();
if (!empty($stage_filters['start_date_input'])) {
    $manager_drilldown_filters['start_date'] = $stage_filters['start_date_input'];
}
if (!empty($stage_filters['end_date_input'])) {
    $manager_drilldown_filters['end_date'] = $stage_filters['end_date_input'];
}
if (!empty($stage_filters['source_id'])) {
    $manager_drilldown_filters['source_id'] = $stage_filters['source_id'];
}
if (!empty($stage_filters['exhibition_id'])) {
    $manager_drilldown_filters['exhibition_id'] = $stage_filters['exhibition_id'];
}
$manager_drilldown_query = http_build_query($manager_drilldown_filters);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?></title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
            table.pretty thead th {
                text-align: center;
                background:<?php echo $LOGO->colorcode;?>;
                color:#fff;
				font-size:12px;
            }
			table.pretty td {
                text-align: center;
                font-size:12px;
            }

            .btns {
                margin-top: 20px;
            }

            .stage-hero {
                background: linear-gradient(135deg, <?php echo $LOGO->colorcode;?> 0%, #1e2b3c 100%);
                border-radius: 16px;
                color: #fff;
                margin-bottom: 20px;
                overflow: hidden;
                padding: 24px 28px;
                position: relative;
            }

            .stage-hero:before {
                background: rgba(255,255,255,0.08);
                border-radius: 999px;
                content: "";
                height: 180px;
                position: absolute;
                right: -40px;
                top: -70px;
                width: 180px;
            }

            .stage-hero h2 {
                color: #fff;
                font-size: 28px;
                font-weight: 700;
                margin: 0 0 8px;
                position: relative;
                z-index: 1;
            }

            .stage-hero p {
                color: rgba(255,255,255,0.86);
                font-size: 14px;
                margin: 0;
                max-width: 780px;
                position: relative;
                z-index: 1;
            }

            .stage-context {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 18px;
                position: relative;
                z-index: 1;
            }

            .context-pill {
                background: rgba(255,255,255,0.12);
                border: 1px solid rgba(255,255,255,0.16);
                border-radius: 999px;
                color: #fff;
                display: inline-flex;
                font-size: 12px;
                gap: 6px;
                padding: 8px 12px;
            }

            .context-pill strong {
                font-weight: 700;
            }

            .stage-card {
                background: #fff;
                border: 1px solid #e6ebf2;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(17, 24, 39, 0.06);
                margin-bottom: 20px;
                padding: 18px;
            }

            .stage-card-title {
                color: #172b4d;
                font-size: 16px;
                font-weight: 700;
                margin: 0 0 16px;
            }

            .stage-filter-card .form-group label {
                color: #42526e;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.04em;
                margin-bottom: 6px;
                text-transform: uppercase;
            }

            .stage-filter-card .form-control {
                border-radius: 10px;
                min-height: 40px;
            }

            .filter-hint {
                color: #6b778c;
                font-size: 12px;
                margin-bottom: 14px;
            }

            .filter-actions {
                display: flex;
                gap: 10px;
                margin-top: 24px;
            }

            .stage-toolbar {
                align-items: center;
                background: #fff;
                border: 1px solid #e6ebf2;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(17, 24, 39, 0.06);
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: space-between;
                margin-bottom: 18px;
                padding: 14px 18px;
            }

            .stage-toolbar-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .stage-toggle-btn {
                align-items: center;
                background: #f7faff;
                border: 1px solid #d7e4f5;
                border-radius: 999px;
                color: #16324f;
                display: inline-flex;
                font-size: 13px;
                font-weight: 700;
                gap: 8px;
                padding: 10px 16px;
            }

            .stage-toggle-btn:focus,
            .stage-toggle-btn:hover {
                background: #edf5ff;
                color: #0b63ce;
            }

            .stage-toggle-count {
                background: <?php echo $LOGO->colorcode;?>;
                border-radius: 999px;
                color: #fff;
                font-size: 11px;
                min-width: 22px;
                padding: 2px 7px;
                text-align: center;
            }

            .stage-toggle-chevron {
                font-size: 12px;
                transition: transform 0.2s ease;
            }

            .stage-toggle-btn[aria-expanded="true"] .stage-toggle-chevron {
                transform: rotate(180deg);
            }

            .stage-toolbar-metrics {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .mini-kpi-pill {
                background: #f5f8fc;
                border: 1px solid #dfe7f3;
                border-radius: 999px;
                color: #334e68;
                display: inline-flex;
                gap: 8px;
                padding: 8px 12px;
            }

            .mini-kpi-pill strong {
                color: #172b4d;
            }

            .stage-collapsible-section {
                margin-bottom: 18px;
            }

            .stage-kpi-grid {
                display: grid;
                gap: 16px;
                grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            }

            .stage-kpi-card {
                background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
                border: 1px solid #dfe7f3;
                border-radius: 16px;
                min-height: 128px;
                padding: 18px;
                position: relative;
            }

            .stage-kpi-card:after {
                background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(30,43,60,0.04) 100%);
                border-radius: 16px;
                content: "";
                inset: 0;
                position: absolute;
            }

            .stage-kpi-card > * {
                position: relative;
                z-index: 1;
            }

            .kpi-label {
                color: #6b778c;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.04em;
                margin-bottom: 12px;
                text-transform: uppercase;
            }

            .kpi-value {
                color: #172b4d;
                font-size: 30px;
                font-weight: 800;
                line-height: 1;
                margin-bottom: 8px;
            }

            .kpi-note {
                color: #5e6c84;
                font-size: 12px;
                min-height: 18px;
            }

            .summary-pills {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .summary-pill {
                background: #f5f8fc;
                border: 1px solid #dfe7f3;
                border-radius: 999px;
                color: #334e68;
                display: inline-flex;
                font-size: 12px;
                gap: 8px;
                padding: 8px 12px;
            }

            .summary-pill strong {
                color: #172b4d;
            }

            .leaderboard-list {
                list-style: none;
                margin: 0;
                max-height: 320px;
                overflow-y: auto;
                padding: 0;
            }

            .leaderboard-item {
                align-items: center;
                border-bottom: 1px solid #edf2f7;
                display: flex;
                justify-content: space-between;
                padding: 12px 0;
            }

            .leaderboard-item:last-child {
                border-bottom: none;
            }

            .leaderboard-name a {
                color: #172b4d;
                font-weight: 600;
                text-decoration: none;
            }

            .leaderboard-name small {
                color: #6b778c;
                display: block;
                font-size: 11px;
                margin-top: 4px;
            }

            .leaderboard-count {
                background: #eef4ff;
                border-radius: 999px;
                color: #0b63ce;
                font-size: 13px;
                font-weight: 700;
                min-width: 44px;
                padding: 8px 10px;
                text-align: center;
            }

            .table-stage-shell {
                margin-top: 6px;
            }

            .stage-empty-state {
                background: #fff8e8;
                border: 1px solid #f6dd9b;
                border-radius: 12px;
                color: #8a6d1d;
                margin-bottom: 18px;
                padding: 14px 16px;
            }

            .dataTables_wrapper .dt-buttons {
                margin-bottom: 10px;
            }

            @media (max-width: 767px) {
                .stage-hero {
                    padding: 20px;
                }

                .stage-hero h2 {
                    font-size: 23px;
                }

                .filter-actions {
                    flex-direction: column;
                }

                .stage-toolbar {
                    align-items: flex-start;
                }
            }
				</style>
    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <!--   <div class="row">
                                <div class="col-md-2 pull-left">
                                      <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btns" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                                </div>
                                 <div class="col-md-2 pull-right">
                             <a href="<?php echo page_url;?>ExcelImport/excel_import"><span class="btn btn-primary">Import Leads Via Excel</span></a> 
                        </div>
                        </div> -->
                       
                       
                    </div>
                </div>

                <div class="row">
                    <div class="page-title-box col-md-12">
                        <h4 class="page-title text-center">Opportunities at <?php echo ucwords(strtolower($lead_status));?> Stage</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="stage-hero">
                            <h2><?php echo ucwords(strtolower($lead_status)); ?> Stage Overview</h2>
                            <p>
                                <?php if (!empty($reason)) { ?>
                                    <?php echo $reason; ?>
                                <?php } else { ?>
                                    This dashboard shows the latest opportunities currently sitting in this stage, along with followup health and coverage insights.
                                <?php } ?>
                            </p>
                            <div class="stage-context">
                                <span class="context-pill"><strong>Source:</strong> <?php echo $selected_source_label; ?></span>
                                <span class="context-pill"><strong>Exhibition:</strong> <?php echo $selected_exhibition_label; ?></span>
                                <?php if ($this->session->userdata['logged_in']['role'] == 12 || $this->session->userdata['logged_in']['role'] == 41) { ?>
                                    <span class="context-pill"><strong>Manager:</strong> <?php echo $selected_manager_label; ?></span>
                                <?php } ?>
                                <span class="context-pill"><strong>Date Range:</strong> <?php echo $selected_date_label; ?></span>
                                <span class="context-pill"><strong>Date Basis:</strong> Last stage update date</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="stage-toolbar">
                            <div class="stage-toolbar-actions">
                                <button type="button" class="btn stage-toggle-btn collapsed" data-toggle="collapse" data-target="#stageFiltersPanel" aria-expanded="false" aria-controls="stageFiltersPanel">
                                    <i class="fa fa-filter"></i>
                                    Filters
                                    <?php if ($active_stage_filter_count > 0) { ?>
                                        <span class="stage-toggle-count"><?php echo $active_stage_filter_count; ?></span>
                                    <?php } ?>
                                    <i class="fa fa-angle-down stage-toggle-chevron"></i>
                                </button>
                                <button type="button" class="btn stage-toggle-btn collapsed" data-toggle="collapse" data-target="#stageInsightsPanel" aria-expanded="false" aria-controls="stageInsightsPanel">
                                    <i class="fa fa-line-chart"></i>
                                    Insights & KPIs
                                    <i class="fa fa-angle-down stage-toggle-chevron"></i>
                                </button>
                            </div>
                            <div class="stage-toolbar-metrics">
                                <span class="mini-kpi-pill"><strong>Total</strong> <?php echo $stage_kpis['total_opportunities']; ?></span>
                                <span class="mini-kpi-pill"><strong>Due Today</strong> <?php echo $stage_kpis['due_today']; ?></span>
                                <span class="mini-kpi-pill"><strong>Stale 8+ Days</strong> <?php echo $stage_kpis['stale_over_7_days']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="stageFiltersPanel" class="collapse stage-collapsible-section">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="stage-card stage-filter-card">
                                <h5 class="stage-card-title">Filter This Stage</h5>
                                <p class="filter-hint">Apply filters to refresh both the KPI cards and the table below. Date filters use the latest update date for this stage.</p>
                                <form method="get" action="<?php echo page_url; ?>Leads/lead_stages/<?php echo $lead_stage; ?>">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-6">
                                            <div class="form-group">
                                                <label for="start">From Date</label>
                                                <input type="text" class="form-control" id="start" name="start_date" value="<?php echo htmlspecialchars($stage_filters['start_date_input'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="dd-mm-yyyy">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="form-group">
                                                <label for="end">To Date</label>
                                                <input type="text" class="form-control" id="end" name="end_date" value="<?php echo htmlspecialchars($stage_filters['end_date_input'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="dd-mm-yyyy">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label for="source_id">Lead Source</label>
                                                <select class="form-control" id="source_id" name="source_id">
                                                    <option value="">All Sources</option>
                                                    <?php foreach ($stage_filter_options['lead_sources'] as $source_option) { ?>
                                                        <option value="<?php echo $source_option->source_id; ?>" <?php echo ((string) $stage_filters['source_id'] === (string) $source_option->source_id) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($source_option->lead_source, ENT_QUOTES, 'UTF-8'); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label for="exhibition_id">Exhibition</label>
                                                <select class="form-control" id="exhibition_id" name="exhibition_id">
                                                    <option value="">All Exhibitions</option>
                                                    <?php foreach ($stage_filter_options['exhibitions'] as $exhibition_option) { ?>
                                                        <option value="<?php echo $exhibition_option->id; ?>" <?php echo ((string) $stage_filters['exhibition_id'] === (string) $exhibition_option->id) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($exhibition_option->exhibition, ENT_QUOTES, 'UTF-8'); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if (!empty($stage_filter_options['managers'])) { ?>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="form-group">
                                                    <label for="manager_id">Manager</label>
                                                    <select class="form-control" id="manager_id" name="manager_id">
                                                        <option value="">All Managers</option>
                                                        <?php foreach ($stage_filter_options['managers'] as $manager_option) { ?>
                                                            <?php $manager_name = ucwords(strtolower(trim($manager_option->title . ' ' . $manager_option->first_name . ' ' . $manager_option->last_name))); ?>
                                                            <option value="<?php echo $manager_option->user_id; ?>" <?php echo ((string) $stage_filters['manager_id'] === (string) $manager_option->user_id) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($manager_name, ENT_QUOTES, 'UTF-8'); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="filter-actions">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply Filters</button>
                                        <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $lead_stage; ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="stageInsightsPanel" class="collapse stage-collapsible-section">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="stage-kpi-grid">
                                <div class="stage-kpi-card">
                                    <div class="kpi-label">Total Opportunities</div>
                                    <div class="kpi-value"><?php echo $stage_kpis['total_opportunities']; ?></div>
                                    <div class="kpi-note"><?php echo $stage_kpis['unique_companies']; ?> unique companies</div>
                                </div>
                                <div class="stage-kpi-card">
                                    <div class="kpi-label">Updated Today</div>
                                    <div class="kpi-value"><?php echo $stage_kpis['updated_today']; ?></div>
                                    <div class="kpi-note">
                                        <?php echo !empty($stage_kpis['last_updated_on']) ? 'Latest: ' . date('d M Y h:i A', strtotime($stage_kpis['last_updated_on'])) : 'No recent updates'; ?>
                                    </div>
                                </div>
                                <div class="stage-kpi-card">
                                    <div class="kpi-label">Followup Due Today</div>
                                    <div class="kpi-value"><?php echo $stage_kpis['due_today']; ?></div>
                                    <div class="kpi-note"><?php echo $stage_kpis['overdue_followups']; ?> overdue followups</div>
                                </div>
                                <div class="stage-kpi-card">
                                    <div class="kpi-label">Average Days In Stage</div>
                                    <div class="kpi-value"><?php echo $stage_kpis['avg_days_in_stage']; ?></div>
                                    <div class="kpi-note"><?php echo $stage_kpis['stale_over_7_days']; ?> leads stale for 8+ days</div>
                                </div>
                                <div class="stage-kpi-card">
                                    <div class="kpi-label">Email Coverage</div>
                                    <div class="kpi-value"><?php echo $stage_kpis['with_email']; ?></div>
                                    <div class="kpi-note">Records with an email address</div>
                                </div>
                                <div class="stage-kpi-card">
                                    <div class="kpi-label">Machine Mix</div>
                                    <div class="kpi-value"><?php echo $stage_kpis['liquid_count'] + $stage_kpis['powder_count'] + $stage_kpis['custom_count']; ?></div>
                                    <div class="kpi-note">Liquid <?php echo $stage_kpis['liquid_count']; ?> | Powder <?php echo $stage_kpis['powder_count']; ?> | Custom <?php echo $stage_kpis['custom_count']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="stage-card">
                                <h5 class="stage-card-title">Stage Mix Snapshot</h5>
                                <div class="summary-pills" style="margin-bottom: 14px;">
                                    <span class="summary-pill"><strong>Domestic</strong> <?php echo $stage_kpis['domestic_count']; ?></span>
                                    <span class="summary-pill"><strong>Export</strong> <?php echo $stage_kpis['export_count']; ?></span>
                                    <span class="summary-pill"><strong>0-3 Days</strong> <?php echo $stage_kpis['age_summary']['0-3 Days']; ?></span>
                                    <span class="summary-pill"><strong>4-7 Days</strong> <?php echo $stage_kpis['age_summary']['4-7 Days']; ?></span>
                                    <span class="summary-pill"><strong>8+ Days</strong> <?php echo $stage_kpis['age_summary']['8+ Days']; ?></span>
                                </div>
                                <div class="summary-pills">
                                    <?php if (!empty($stage_kpis['source_summary'])) { ?>
                                        <?php foreach (array_slice($stage_kpis['source_summary'], 0, 8) as $source_summary) { ?>
                                            <span class="summary-pill"><strong><?php echo htmlspecialchars($source_summary['label'], ENT_QUOTES, 'UTF-8'); ?></strong> <?php echo $source_summary['count']; ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="summary-pill"><strong>No source data</strong> 0</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stage-card">
                                <h5 class="stage-card-title">Manager Snapshot</h5>
                                <?php if ($show_stage_manager_summary && !empty($stage_kpis['manager_summary'])) { ?>
                                    <ul class="leaderboard-list">
                                        <?php foreach (array_slice($stage_kpis['manager_summary'], 0, 8) as $manager_summary) { ?>
                                            <?php
                                                $manager_link = page_url . 'Leads/lead_stages_by_selected_user/' . $lead_stage . '/' . $manager_summary['user_id'];
                                                if ($manager_drilldown_query !== '') {
                                                    $manager_link .= '?' . $manager_drilldown_query;
                                                }
                                            ?>
                                            <li class="leaderboard-item">
                                                <div class="leaderboard-name">
                                                    <a href="<?php echo $manager_link; ?>"><?php echo htmlspecialchars($manager_summary['name'], ENT_QUOTES, 'UTF-8'); ?></a>
                                                    <small><?php echo !empty($manager_summary['last_updated_on']) ? 'Last update: ' . date('d M Y h:i A', strtotime($manager_summary['last_updated_on'])) : 'No updates in this filter'; ?></small>
                                                </div>
                                                <div class="leaderboard-count"><?php echo $manager_summary['count']; ?></div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <div class="summary-pills">
                                        <span class="summary-pill"><strong>Current View</strong> Personal stage snapshot</span>
                                        <span class="summary-pill"><strong>Total</strong> <?php echo $stage_kpis['total_opportunities']; ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                <?php if ((int) $stage_kpis['total_opportunities'] === 0) { ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="stage-empty-state">
                                No opportunities matched the current stage filters. Try widening the date range or resetting source and exhibition filters.
                            </div>
                        </div>
                    </div>
                <?php } ?>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive table-stage-shell">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>

                                    
                                     <th>Sr No.</th>
                                     <?php $q = $this->db->select('lead_id')->from('lead_stage')->where('lead_id',$this->uri->segment(3))->where('dead_end',1)->get();
                                     if($q->num_rows()>0){?>
                                        <th>Lost Reason</th>
                                     <?php }else{
                                        if($this->uri->segment(3)==35){}else{
                                        ?>
                                     <th>Update</th>
                                 <?php } }?>
                                     <?php
                                     if($quotestep==1 || $this->uri->segment(3)==36 || $this->uri->segment(3)==37 || $this->uri->segment(3)==38 || $this->uri->segment(3)==39)
                                     {
                                    ?>
                                     <th>Preview Quotation</th>
                                     <th>Quotation Change Reason(if Any)</th>

                                    <?php } ?>

                                     <th>Opp. No/Date</th>
                                     <th>Opp Type</th>
                                     <th>Lead Source</th>
                                     <th>Company Name</th> 
                                     <th>Contact Detail</th> 
                                     <th>Email ID</th>
                                     <th>Machine Type</th>
                                     <th>Machine</th>
                                     <th>Address</th>
                                     <th>Remarks</th>
                                     <th>Manager</th>
                                    
                                     <th>Last Updated On</th>                                    
                                                                      
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->

<div id="updateprogress" class="modal fade" role="dialog">
<form id="updateprogressform" method="post" action="<?php echo page_url;?>Leads/approvalorrejection"  enctype="multipart/form-data">
<div id="pageloader1">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title" style="font-weight: bold; text-align:center;">Approve or Reject Quotation</h4>
</div>
<div class="modal-body">
<div class="row">

<div class="col-md-12">
<input type="hidden" id="leadid" value="" name="leadid">

<div class="form-group">
<label>Change Stage <span style="color:red" id="error_taskstatus">*</span></label>
<select name="taskstatus" id="taskstatus" onchange="checkifrejected();" class="form-control" required>
    <option value="">Select Option</option>
<option value="1">Approve</option>
<option value="0">Reject</option>

</select>
</div>
</div>
</div>

<div class="row"  id="remarkbox" style="display:none;">

<div class="col-md-12">
<div class="form-group">
<label>Remarks <span style="color:red" id="error_taskremarks"></span></label>
<textarea name="taskremarks" id="taskremarks" class="form-control"></textarea>
</div>
</div>
</div>



<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<input type="submit" style="width: 100%;" name="" onclick="taskupdationvalidation();" value="Submit" class="btn btn-success">
</div>
</div>

</div>
<!-- <div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
</div>

</div>
</form>
</div>

<?php if ((int) $lead_stage === 38) { ?>
<div id="quotationRejectionCommentModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="font-weight:bold;">Send Comment to Shubham Sir</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="qrLeadId" value="">
                <div class="alert alert-info" id="qrContext" style="margin-bottom:15px;"></div>
                <div class="form-group">
                    <label>Comment <span style="color:red;">*</span></label>
                    <textarea id="qrComment" class="form-control" rows="5" placeholder="Mention why this rejected quotation should be reviewed."></textarea>
                </div>
                <div id="qrMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendQrComment">Send to Shubham Sir</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
function approvalwindow(id){
$("#updateprogress").modal('show');
$("#leadid").val(id);

}
</script>

<script type="text/javascript">
    function checkifrejected() {
       var taskstatus = $("#taskstatus").val();
       if(taskstatus==0){
        $("#remarkbox").css('display','block');
       // $("#taskremarks").attr('Required','true');
       }else{
        $("#remarkbox").attr('display','none');
        //$("#taskremarks").attr('Required','false');
       }
    }
</script>

                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


         <!-- jQuery  -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
        <script src="<?php echo assets_url;?>js/waves.js"></script>
        <script src="<?php echo assets_url;?>js/wow.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

        <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

       

        <script>
$( document ).ready(function() {

$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"pagination":true,
"pageLength": 100,
"stateSave": true,
dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: '<?php echo addslashes(ucwords(strtolower($lead_status))); ?> Stage Data'
            }
        ],
"sAjaxSource": <?php echo json_encode($stage_ajax_url); ?>,

"aoColumns": [
				{ mData: 'sr_no' } 
                <?php $q = $this->db->select('lead_id')->from('lead_stage')->where('lead_id',$this->uri->segment(3))->where('dead_end',1)->get();
                                     if($q->num_rows()>0){?>
                                         ,{ mData: 'leadlostreason' } 
                                     <?php }else if($this->uri->segment(3)==36){?>
                                        ,{ mData: 'approvereject' } 

                                    <?php  }else{ 
                                        if($this->uri->segment(3)==35){}else{?>
                ,{ mData: 'progress' } 
            <?php } }?>
                <?php
                if($quotestep==1 || $this->uri->segment(3)==36 || $this->uri->segment(3)==37 || $this->uri->segment(3)==38 || $this->uri->segment(3)==39)
                {
                ?>
                ,{ mData: 'quote_step' }
                ,{ mData: 'resonremarks' } 
                <?php } ?>
                ,{ mData: 'oppno' },
                { mData: 'opptype' },
                { mData: 'source' },
                { mData: 'company' },
                { mData: 'customerdetail' },
                { mData: 'email' },
                { mData: 'type' },
                { mData: 'product' },
                { mData: 'address' },
                { mData: 'remarks' },
                { mData: 'manager' },
                { mData: 'updatedOn' }

				
				
		]
});  

        $('#start').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

        $('#end').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

        <?php if ((int) $lead_stage === 38) { ?>
        $(document).on('click', '.quote-rejection-comment-btn', function () {
            $('#qrLeadId').val($(this).data('lead-id'));
            $('#qrComment').val('');
            $('#qrMessage').html('');
            $('#qrContext').html('<strong>Opportunity:</strong> ' + ($(this).data('opp-no') || '') + '<br><strong>Company:</strong> ' + ($(this).data('company') || ''));
            $('#quotationRejectionCommentModal').modal('show');
        });

        $('#sendQrComment').on('click', function () {
            var btn = $(this);
            var comment = $.trim($('#qrComment').val());
            if (comment === '') {
                $('#qrMessage').html('<div class="alert alert-danger">Please enter a comment.</div>');
                return;
            }
            btn.prop('disabled', true).text('Sending...');
            $.ajax({
                url: '<?php echo page_url; ?>Leads/submit_quotation_rejection_comment',
                type: 'POST',
                dataType: 'json',
                data: {
                    lead_id: $('#qrLeadId').val(),
                    comment: comment,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                success: function (res) {
                    if (res && res.ok) {
                        $('#qrMessage').html('<div class="alert alert-success">' + res.message + '</div>');
                        $('#example').DataTable().ajax.reload(null, false);
                        setTimeout(function () {
                            $('#quotationRejectionCommentModal').modal('hide');
                        }, 900);
                    } else {
                        $('#qrMessage').html('<div class="alert alert-danger">' + ((res && res.message) ? res.message : 'Could not send comment.') + '</div>');
                    }
                },
                error: function (xhr) {
                    var msg = 'Could not send comment.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    $('#qrMessage').html('<div class="alert alert-danger">' + msg + '</div>');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Send to Shubham Sir');
                }
            });
        });
        <?php } ?>

});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var department_name = $("#department_name").val();
if(department_name=='')
{
	
	$("#error_department_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || department_name==''|| status=='' )
{
	
	return false;
}

});
});
</script>

<script type="text/javascript">
function deleteLead(id) {
    if (confirm("Are you sure you want to delete this lead?")) {
        $.ajax({
            url: '<?= page_url."Leads/deleteLead"; ?>',
            type: 'POST',
            data: {
                id: id,
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                //alert(response);
                if (response.trim() === 'success') {
                    alert("Lead deleted successfully.");
                    $('#example').DataTable().ajax.reload(null, false); // reload without resetting pagination
                } else {
                    alert("Failed to delete the lead.");
                }
            },
            error: function() {
                alert("Error occurred. Please try again.");
            }
        });
    }
}
</script>



<script>
$(document).ready(function(){
$("#updateprogressform").on("submit", function(){
$("#pageloader1").fadeIn();
});//submit
});//document ready
</script>

<script type="text/javascript">
    function getMemberList() {
        var team_members=$("#team_members").val();
        var startdate=$("#start").val();
        var enddate=$("#end").val();
        location.href = '<?php echo page_url;?>Leads/newleads/'+team_members+'/'+startdate+'/'+enddate;
            
    }
</script>
    </body>
</html>
