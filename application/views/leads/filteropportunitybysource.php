<?php
$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

$selectedSource = $this->uri->segment(3);
$selectedExhibition = $this->uri->segment(4);

if ($selectedSource == '') {
    $selectedSource = 'ALL';
}

if ($selectedExhibition == '') {
    $selectedExhibition = 'ALL';
}

/*
 * Fetch active lead stages once
 */
$leadStages = array();

$stage_q = $this->db
    ->select('lead_id, lead_name')
    ->from('lead_stage')
    ->where('status', 1)
    ->order_by('sort_order', 'asc')
    ->get();

if ($stage_q->num_rows() > 0) {
    foreach ($stage_q->result() as $stage) {
        $leadStages[] = array(
            'lead_id' => $stage->lead_id,
            'lead_name' => $stage->lead_name
        );
    }
}

/*
 * Fetch sources once
 */
$leadSources = array();

$source_q = $this->db
    ->select('lead_source, source_id')
    ->from('lead_source')
    ->where('status', 1)
    ->order_by('lead_source', 'asc')
    ->get();

if ($source_q->num_rows() > 0) {
    foreach ($source_q->result() as $source) {
        $leadSources[] = $source;
    }
}

/*
 * Fetch exhibitions once
 */
$exhibitions = array();

$exhibition_q = $this->db
    ->select('id, exhibition')
    ->from('exhibition_info')
    ->order_by('exhibition', 'asc')
    ->get();

if ($exhibition_q->num_rows() > 0) {
    foreach ($exhibition_q->result() as $exhibition) {
        $exhibitions[] = $exhibition;
    }
}

/*
 * Build latest stage subquery.
 * This gives current/latest stage per lead.
 */
$latestStageSubQuery = "
    (
        SELECT pr1.lead_id, pr1.lead_status
        FROM progress_remarks pr1
        INNER JOIN (
            SELECT lead_id, MAX(added_on) AS latest_added_on
            FROM progress_remarks
            GROUP BY lead_id
        ) pr2 
        ON pr1.lead_id = pr2.lead_id 
        AND pr1.added_on = pr2.latest_added_on
    ) latest_stage
";

$rowsData = array();

$totalSources = 0;
$totalLeads = 0;
$totalStageWiseLeads = 0;
$highestSourceName = '';
$highestSourceCount = 0;

function getStageCountForSourceReport($CI, $stageId, $sourceId, $exhibitionId = null)
{
    $CI->db->select('COUNT(DISTINCT latest_stage.lead_id) as total_count', false);
    $CI->db->from('leads l');
    $CI->db->join(
        "
        (
            SELECT pr1.lead_id, pr1.lead_status
            FROM progress_remarks pr1
            INNER JOIN (
                SELECT lead_id, MAX(added_on) AS latest_added_on
                FROM progress_remarks
                GROUP BY lead_id
            ) pr2 
            ON pr1.lead_id = pr2.lead_id 
            AND pr1.added_on = pr2.latest_added_on
        ) latest_stage
        ",
        'latest_stage.lead_id = l.id',
        'inner',
        false
    );

    $CI->db->where('latest_stage.lead_status', $stageId);
    $CI->db->where('l.lead_source_id', $sourceId);

    if ($exhibitionId !== null && $exhibitionId !== '' && $exhibitionId !== 'ALL') {
        $CI->db->where('l.exhibition', $exhibitionId);
    }

    $q = $CI->db->get();
    return ($q->num_rows() > 0) ? (int)$q->row()->total_count : 0;
}

function getLeadCountForSourceReport($CI, $sourceId, $exhibitionId = null)
{
    $CI->db->select('COUNT(id) as total_count', false);
    $CI->db->from('leads');
    $CI->db->where('lead_source_id', $sourceId);

    if ($exhibitionId !== null && $exhibitionId !== '' && $exhibitionId !== 'ALL') {
        $CI->db->where('exhibition', $exhibitionId);
    }

    $q = $CI->db->get();
    return ($q->num_rows() > 0) ? (int)$q->row()->total_count : 0;
}

/*
 * Prepare rows
 */
if ($selectedSource == 'ALL') {

    foreach ($leadSources as $source) {

        if ((int)$source->source_id == 8) {
            continue;
        }

        $leadCount = getLeadCountForSourceReport($this, $source->source_id);

        $stageCounts = array();
        $rowStageTotal = 0;

        foreach ($leadStages as $stage) {
            $count = getStageCountForSourceReport($this, $stage['lead_id'], $source->source_id);
            $stageCounts[$stage['lead_id']] = $count;
            $rowStageTotal += $count;
        }

        $sourceName = ucwords(strtolower($source->lead_source));

        $rowsData[] = array(
            'source_id' => $source->source_id,
            'source_type' => 'Source',
            'source_name' => $sourceName,
            'source_display_name' => $sourceName,
            'exhibition_id' => '',
            'lead_count' => $leadCount,
            'stage_counts' => $stageCounts,
            'stage_total' => $rowStageTotal,
            'detail_url' => page_url . 'Leads/sourcewiseopportunities/' . $source->source_id
        );

        $totalSources++;
        $totalLeads += $leadCount;
        $totalStageWiseLeads += $rowStageTotal;

        if ($leadCount > $highestSourceCount) {
            $highestSourceCount = $leadCount;
            $highestSourceName = $sourceName;
        }
    }

    /*
     * Exhibition source rows
     */
    foreach ($exhibitions as $exhibition) {

        $leadCount = getLeadCountForSourceReport($this, 8, $exhibition->id);

        $stageCounts = array();
        $rowStageTotal = 0;

        foreach ($leadStages as $stage) {
            $count = getStageCountForSourceReport($this, $stage['lead_id'], 8, $exhibition->id);
            $stageCounts[$stage['lead_id']] = $count;
            $rowStageTotal += $count;
        }

        $sourceName = ucwords(strtolower($exhibition->exhibition));

        $rowsData[] = array(
            'source_id' => 8,
            'source_type' => 'Exhibition',
            'source_name' => $sourceName,
            'source_display_name' => $sourceName,
            'exhibition_id' => $exhibition->id,
            'lead_count' => $leadCount,
            'stage_counts' => $stageCounts,
            'stage_total' => $rowStageTotal,
            'detail_url' => page_url . 'Leads/sourcewiseopportunities/8/' . $exhibition->id
        );

        $totalSources++;
        $totalLeads += $leadCount;
        $totalStageWiseLeads += $rowStageTotal;

        if ($leadCount > $highestSourceCount) {
            $highestSourceCount = $leadCount;
            $highestSourceName = $sourceName;
        }
    }

} else {

    if ((int)$selectedSource != 8) {

        $this->db->select('lead_source, source_id')->from('lead_source')->where('status', 1);

        if ($selectedSource != '') {
            $this->db->where('source_id', $selectedSource);
        }

        $filteredSource_q = $this->db->get();

        if ($filteredSource_q->num_rows() > 0) {
            foreach ($filteredSource_q->result() as $source) {

                $leadCount = getLeadCountForSourceReport($this, $source->source_id);

                $stageCounts = array();
                $rowStageTotal = 0;

                foreach ($leadStages as $stage) {
                    $count = getStageCountForSourceReport($this, $stage['lead_id'], $source->source_id);
                    $stageCounts[$stage['lead_id']] = $count;
                    $rowStageTotal += $count;
                }

                $sourceName = ucwords(strtolower($source->lead_source));

                $rowsData[] = array(
                    'source_id' => $source->source_id,
                    'source_type' => 'Source',
                    'source_name' => $sourceName,
                    'source_display_name' => $sourceName,
                    'exhibition_id' => '',
                    'lead_count' => $leadCount,
                    'stage_counts' => $stageCounts,
                    'stage_total' => $rowStageTotal,
                    'detail_url' => page_url . 'Leads/sourcewiseopportunities/' . $source->source_id
                );

                $totalSources++;
                $totalLeads += $leadCount;
                $totalStageWiseLeads += $rowStageTotal;

                if ($leadCount > $highestSourceCount) {
                    $highestSourceCount = $leadCount;
                    $highestSourceName = $sourceName;
                }
            }
        }

    } else {

        $this->db->select('id, exhibition')->from('exhibition_info');

        if ($selectedExhibition != '' && $selectedExhibition != 'ALL') {
            $this->db->where('id', $selectedExhibition);
        }

        $filteredExhibition_q = $this->db->get();

        if ($filteredExhibition_q->num_rows() > 0) {
            foreach ($filteredExhibition_q->result() as $exhibition) {

                $leadCount = getLeadCountForSourceReport($this, 8, $exhibition->id);

                $stageCounts = array();
                $rowStageTotal = 0;

                foreach ($leadStages as $stage) {
                    $count = getStageCountForSourceReport($this, $stage['lead_id'], 8, $exhibition->id);
                    $stageCounts[$stage['lead_id']] = $count;
                    $rowStageTotal += $count;
                }

                $sourceName = ucwords(strtolower($exhibition->exhibition));

                $rowsData[] = array(
                    'source_id' => 8,
                    'source_type' => 'Exhibition',
                    'source_name' => $sourceName,
                    'source_display_name' => $sourceName,
                    'exhibition_id' => $exhibition->id,
                    'lead_count' => $leadCount,
                    'stage_counts' => $stageCounts,
                    'stage_total' => $rowStageTotal,
                    'detail_url' => page_url . 'Leads/sourcewiseopportunities/8/' . $exhibition->id
                );

                $totalSources++;
                $totalLeads += $leadCount;
                $totalStageWiseLeads += $rowStageTotal;

                if ($leadCount > $highestSourceCount) {
                    $highestSourceCount = $leadCount;
                    $highestSourceName = $sourceName;
                }
            }
        }
    }
}

$avgLeadsPerSource = ($totalSources > 0) ? round($totalLeads / $totalSources, 2) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright;?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle;?> Filter Opportunity by Source</title>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/our.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #f3f6fb;
        }

        h3{
            color:#fff !important;
        }
        .report-hero {
            background: linear-gradient(135deg, <?php echo $themeColor;?> 0%, #111827 100%);
            border-radius: 18px;
            padding: 26px;
            margin-bottom: 22px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(0,0,0,0.13);
            position: relative;
            overflow: hidden;
        }

        .report-hero:before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            right: -90px;
            top: -100px;
        }

        .report-title {
            font-size: 27px;
            font-weight: 900;
            margin: 0;
            letter-spacing: .2px;
        }

        .report-subtitle {
            margin-top: 8px;
            opacity: .9;
            font-size: 14px;
        }

        .report-pill {
            display: inline-block;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 8px 14px;
            border-radius: 30px;
            font-weight: 700;
            margin-top: 12px;
        }

        .back-btn {
            border-radius: 20px;
            font-weight: 800;
            margin-top: 12px;
        }

        .kpi-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.07);
            min-height: 118px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:after {
            content: "";
            position: absolute;
            right: -25px;
            bottom: -25px;
            width: 92px;
            height: 92px;
            border-radius: 50%;
            background: rgba(72,114,184,0.08);
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: <?php echo $themeColor;?>;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            right: 16px;
            top: 16px;
            font-size: 19px;
        }

        .kpi-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .kpi-value {
            font-size: 30px;
            font-weight: 900;
            color: #111827;
            margin-top: 9px;
            line-height: 1.1;
        }

        .kpi-hint {
            color: #8a94a6;
            font-size: 12px;
            margin-top: 7px;
        }

        .filter-panel {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.05);
        }

        .filter-title {
            font-size: 15px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 12px;
        }

        .modern-table-card {
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 10px 28px rgba(31,41,55,0.07);
        }

        table.pretty thead th {
            background: <?php echo $themeColor;?> !important;
            color: #fff !important;
            font-size: 12px;
            font-weight: 800;
            text-align: center;
            vertical-align: middle !important;
            white-space: nowrap;
        }

        table.pretty tbody td {
            text-align: center;
            vertical-align: middle !important;
            font-size: 12px;
            color: #374151;
        }

        .source-box {
            text-align: left;
            min-width: 180px;
        }

        .source-name {
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
        }

        .source-type {
            margin-top: 4px;
            font-size: 11px;
            color: #6b7280;
        }

        .lead-count-badge {
            display: inline-block;
            background: #eef4ff;
            color: <?php echo $themeColor;?>;
            border: 1px solid rgba(72,114,184,0.18);
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 900;
            min-width: 55px;
        }

        .stage-count-link {
            display: inline-block;
            min-width: 42px;
            padding: 6px 10px;
            border-radius: 18px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            background: #f8fafc;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .stage-count-link.has-count {
            background: #ecfdf3;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .stage-count-link:hover {
            text-decoration: none;
            background: #111827;
            color: #fff;
            border-color: #111827;
        }

        .status-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .pill-success {
            background: #ecfdf3;
            color: #15803d;
        }

        .pill-warning {
            background: #fff7ed;
            color: #c2410c;
        }

        .pill-info {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .dataTables_wrapper .dt-buttons .btn {
            border-radius: 20px !important;
            margin-right: 5px;
            font-weight: 800;
        }

        .dataTables_filter input,
        .dataTables_length select,
        .filter-panel input,
        .filter-panel select {
            border-radius: 20px;
            border: 1px solid #d8dee9;
            padding: 6px 12px;
        }

        .summary-strip {
            background: #f8fafc;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 14px;
            color: #374151;
            font-weight: 700;
        }

        @media(max-width: 767px) {
            .report-title {
                font-size: 22px;
            }

            .kpi-value {
                font-size: 25px;
            }

            .report-hero {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<header id="topnav">
    <?php $this->load->view('common/nav-menu');?>
</header>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <div class="report-hero">
                    <div class="row">
                        <div class="col-md-8">
                            <a href="javascript:history.go(-1)" class="btn btn-success btn-sm back-btn">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>

                            <h3 class="report-title" style="margin-top:14px;">Opportunity Current Stage by Source</h3>

                            <div class="report-subtitle">
                                Source-wise and exhibition-wise opportunity distribution with current stage tracking.
                            </div>

                            <div class="report-pill">
                                <i class="fa fa-filter"></i>
                                Current Filter:
                                <?php echo ($selectedSource == 'ALL') ? 'All Sources' : (($selectedSource == 8) ? 'Exhibition' : 'Selected Source'); ?>
                            </div>
                        </div>

                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Total Leads</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <?php echo number_format($totalLeads); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-info" style="border-radius:12px;">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-sitemap"></i></div>
                    <div class="kpi-label">Sources / Exhibitions</div>
                    <div class="kpi-value"><?php echo number_format($totalSources); ?></div>
                    <div class="kpi-hint">Visible source rows in report</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-users"></i></div>
                    <div class="kpi-label">Total Leads</div>
                    <div class="kpi-value"><?php echo number_format($totalLeads); ?></div>
                    <div class="kpi-hint">Total leads in visible sources</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Avg. Leads / Source</div>
                    <div class="kpi-value"><?php echo $avgLeadsPerSource; ?></div>
                    <div class="kpi-hint">Average leads per visible source</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-trophy"></i></div>
                    <div class="kpi-label">Top Source</div>
                    <div class="kpi-value" style="font-size:18px; padding-right:38px;">
                        <?php echo !empty($highestSourceName) ? $highestSourceName : '-'; ?>
                    </div>
                    <div class="kpi-hint"><?php echo number_format($highestSourceCount); ?> leads</div>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Report Filters
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Filter By Source</label>
                    <select class="form-control" id="sourcewise" name="sourcewise" onchange="filterbysource();">
                        <option value="ALL" <?php if ($selectedSource == 'ALL') { echo "selected"; } ?>>ALL</option>
                        <?php foreach ($leadSources as $source) { ?>
                            <option value="<?php echo $source->source_id;?>" <?php if ($selectedSource == $source->source_id) { echo "selected"; } ?>>
                                <?php echo ucwords(strtolower($source->lead_source));?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6" id="exhibitionFilterBox" <?php if ((int)$selectedSource != 8) { ?>style="display:none;"<?php } ?>>
                    <label>Filter By Exhibition</label>
                    <select class="form-control" id="exhibitionid" onchange="filterbyexhibition();">
                        <option value="ALL" <?php if ($selectedExhibition == 'ALL') { echo "selected"; } ?>>ALL</option>
                        <?php foreach ($exhibitions as $exhibition) { ?>
                            <option value="<?php echo $exhibition->id;?>" <?php if ($selectedExhibition == $exhibition->id) { echo "selected"; } ?>>
                                <?php echo ucwords(strtolower($exhibition->exhibition));?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Search in Table</label>
                    <input type="text" id="customSearch" class="form-control" placeholder="Search source, stage count...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Source Type</label>
                    <select id="sourceTypeFilter" class="form-control">
                        <option value="">All Types</option>
                        <option value="Source">Source</option>
                        <option value="Exhibition">Exhibition</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="modern-table-card table-responsive">
            <div class="summary-strip">
                <i class="fa fa-info-circle"></i>
                Stage numbers are calculated from the latest progress remark of each lead. Click any number to open its related opportunity list.
            </div>

            <table id="example1" class="table table-striped table-bordered pretty" width="100%">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Source</th>
                        <th>Source Type</th>
                        <th>No of Leads</th>
                        <?php foreach ($leadStages as $stage) { ?>
                            <th><?php echo ucwords(strtolower($stage['lead_name'])); ?></th>
                        <?php } ?>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1; foreach ($rowsData as $row) { ?>
                        <tr data-source-type="<?php echo $row['source_type']; ?>">
                            <td><?php echo $i; ?></td>

                            <td>
                                <div class="source-box">
                                    <div class="source-name">
                                        <?php echo $row['source_display_name']; ?>
                                    </div>
                                    <div class="source-type">
                                        <?php echo $row['source_type']; ?>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <?php if ($row['source_type'] == 'Exhibition') { ?>
                                    <span class="status-pill pill-warning">Exhibition</span>
                                <?php } else { ?>
                                    <span class="status-pill pill-info">Source</span>
                                <?php } ?>
                            </td>

                            <td>
                                <a href="<?php echo $row['detail_url']; ?>" class="lead-count-badge">
                                    <?php echo number_format($row['lead_count']); ?>
                                </a>
                            </td>

                            <?php foreach ($leadStages as $stage) { 
                                $stageCount = isset($row['stage_counts'][$stage['lead_id']]) ? (int)$row['stage_counts'][$stage['lead_id']] : 0;

                                if ($row['source_id'] == 8 && !empty($row['exhibition_id'])) {
                                    $stageUrl = page_url . 'Leads/lead_stages/' . $stage['lead_id'] . '/8/' . $row['exhibition_id'];
                                } else {
                                    $stageUrl = page_url . 'Leads/lead_stages/' . $stage['lead_id'] . '/' . $row['source_id'];
                                }
                            ?>
                                <td>
                                    <a href="<?php echo $stageUrl; ?>" class="stage-count-link <?php echo ($stageCount > 0) ? 'has-count' : ''; ?>">
                                        <?php echo number_format($stageCount); ?>
                                    </a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>

        <?php $this->load->view('common/footer');?>

    </div>
</div>

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

<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>
function filterbysource() {
    var sourcewise = $("#sourcewise").val();

    if (sourcewise !== '') {
        window.location.href = '<?php echo page_url;?>Dashboard/filteropportunitybysource/' + sourcewise;
    }
}

function filterbyexhibition() {
    var exhibitionid = $("#exhibitionid").val();

    if (exhibitionid !== '') {
        window.location.href = '<?php echo page_url;?>Dashboard/filteropportunitybysource/8/' + exhibitionid;
    }
}

$(document).ready(function () {

    var table = $('#example1').DataTable({
        processing: true,
        fixedHeader: true,
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, 500, -1], [25, 50, 100, 250, 500, 'All']],
        responsive: false,
        scrollX: true,
        stateSave: true,
        order: [[3, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'Opportunity Current Stage by Source',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            var div = document.createElement('div');
                            div.innerHTML = data;
                            return div.textContent || div.innerText || '';
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'Opportunity Current Stage by Source',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            var div = document.createElement('div');
                            div.innerHTML = data;
                            return div.textContent || div.innerText || '';
                        }
                    }
                }
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'example1') {
            return true;
        }

        var sourceTypeFilter = $('#sourceTypeFilter').val();

        if (sourceTypeFilter === '') {
            return true;
        }

        var rowNode = table.row(dataIndex).node();
        var rowType = $(rowNode).data('source-type');

        return rowType === sourceTypeFilter;
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#sourceTypeFilter').on('change', function () {
        table.draw();
    });

});
</script>

</body>
</html>