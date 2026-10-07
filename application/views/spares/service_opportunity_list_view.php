<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Service Opportunities List</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f8fb; }
        .wrapper { padding-top: 80px; }
        .card-box { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 20px; background: #fff; border: 1px solid #eef2f5; }
        
        /* KPI Widget Styling */
        .widget-box { border-right: 1px solid #eee; padding: 10px 0; transition: 0.3s; }
        .widget-box:last-child { border-right: none; }
        .widget-box:hover { background: #fcfdfe; }
        .widget-box h3 { font-weight: 700; margin: 5px 0; font-size: 24px; }
        .widget-box p { color: #888; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; margin-bottom: 0; font-weight: 600; }
        
        /* Table & Badge Styling */
        .badge-stage { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: 600; padding: 6px 12px; border-radius: 6px; display: block; text-align: center; font-size: 11px; }
        .progress { height: 8px; margin-bottom: 5px; border-radius: 10px; background-color: #f1f5f9; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05); }
        .prob-text { font-size: 11px; font-weight: 600; color: #64748b; }
        
        .table thead th { background-color: #f8fafc; color: #334155; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; }
        .table tbody tr td { vertical-align: middle; padding: 15px 10px; }
        .action-form-inline { display: inline-block; margin: 0; }
        .badge-stage-cancelled { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
        .cancelled-quotation-row td { background: #fffafb; }
        .cancelled-quotation-row td:first-child { box-shadow: inset 4px 0 0 #e11d48; }
        .quote-age-note { display: block; margin-top: 4px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:8px;">
        <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:8px;">
        <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
            
            <div class="card-box">
                <div class="row text-center">
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list" style="text-decoration:none;">
                            <p>Total Pipeline</p>
                            <h3 class="text-dark"><?php echo $kpi['total']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list/1" style="text-decoration:none;">
                            <p>New Leads</p>
                            <h3 class="text-primary"><?php echo $kpi['new']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list/3" style="text-decoration:none;">
                            <p>Drafting/Approvals</p>
                            <h3 class="text-warning"><?php echo $kpi['quote_pending']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list/5" style="text-decoration:none;">
                            <p>Active Quotes</p>
                            <h3 class="text-info"><?php echo $kpi['quote_shared']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list/7" style="text-decoration:none;">
                            <p>Orders Won</p>
                            <h3 class="text-success"><?php echo $kpi['won']; ?></h3>
                        </a>
                    </div>
                    <div class="col-md-2 widget-box">
                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list/<?php echo (int) $cancelled_quotation_stage_id; ?>" style="text-decoration:none;">
                            <p>Cancelled Quotes</p>
                            <h3 class="text-danger"><?php echo $kpi['cancelled']; ?></h3>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row m-b-10">
                <div class="col-sm-8">
                    <h4 class="page-title">Service Pipeline: <span class="text-primary"><?php echo $active_stage_name; ?></span></h4>
                </div>
                <div class="col-sm-4 text-right">
                    <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list" class="btn btn-default btn-sm waves-effect waves-light">
                        <i class="fa fa-list"></i> All Opportunities
                    </a>
                    <button class="btn btn-default btn-sm waves-effect" type="button" data-toggle="collapse" data-target="#filterCollapse">
                        <i class="fa fa-filter"></i> Advanced Search
                    </button>
                    <a href="<?php echo page_url; ?>ServiceLeads/create" class="btn btn-primary btn-sm waves-effect waves-light">
                        <i class="fa fa-plus"></i> New Opportunity
                    </a>
                </div>
            </div>

            <div class="collapse m-b-20 <?php echo ($this->input->get('marketing_person') || $this->input->get('from_date')) ? 'show' : ''; ?>" id="filterCollapse">
                <div class="card-box" style="background: #fdfdfd;">
                    <form method="get" action="<?php echo page_url; ?>ServiceLeads/opportunity_list">
                        <?php if (!empty($current_followup_filter)): ?>
                            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($current_followup_filter); ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="small font-600">Start Date</label>
                                <input type="date" name="from_date" class="form-control input-sm" value="<?php echo $this->input->get('from_date'); ?>">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-600">End Date</label>
                                <input type="date" name="to_date" class="form-control input-sm" value="<?php echo $this->input->get('to_date'); ?>">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-600">Marketing Executive</label>
                                <select name="marketing_person" class="form-control input-sm">
                                    <option value="">-- All Executives --</option>
                                    <?php foreach($marketing_persons as $p): ?>
                                        <option value="<?php echo $p->user_id; ?>" <?php echo ($this->input->get('marketing_person') == $p->user_id) ? 'selected' : ''; ?>>
                                            <?php echo $p->first_name.' '.$p->last_name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-600">Service Type</label>
                                <select name="op_type" class="form-control input-sm">
                                    <option value="">-- All Types --</option>
                                    <option value="1" <?php echo ($this->input->get('op_type') == 1) ? 'selected' : ''; ?>>Domestic</option>
                                    <option value="2" <?php echo ($this->input->get('op_type') == 2) ? 'selected' : ''; ?>>Export/International</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filter Results</button>
                            <a href="<?php echo page_url; ?>ServiceLeads/opportunity_list" class="btn btn-sm btn-default">Clear All Filters</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-box">
                <div class="table-responsive">
                    <table id="opportunityTable" class="table table-hover m-0">
                        <thead>
                            <tr>
                                <th>Opp. Ref & Date</th>
                                <th>Customer Entity</th>
                                <th>Account Manager</th>
                                <th>Current Stage</th>
                                <th width="160">Conversion Prob.</th>
                                <th>Latest Document</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($opportunities as $row): ?>
                            <?php 
                                // Logic for Dynamic Probability Progress Bar
                                $prob = (int)$row->probability;
                                $bar_color = "progress-bar-danger"; 
                                if($prob >= 40 && $prob < 80) { $bar_color = "progress-bar-warning"; }
                                if($prob >= 80) { $bar_color = "progress-bar-success"; }
                                $is_cancelled_quotation = ((int) $row->current_stage_id === (int) $cancelled_quotation_stage_id);
                                $can_reopen_quotation = $is_cancelled_quotation && (
                                    !empty($can_reopen_all_cancelled)
                                    || (int) $row->marketing_person_id === (int) $current_user_id
                                );
                            ?>
                            <tr class="<?php echo $is_cancelled_quotation ? 'cancelled-quotation-row' : ''; ?>">
                                <td>
                                    <strong class="text-dark"><?php echo $row->op_no; ?></strong><br>
                                    <small class="text-muted"><i class="fa fa-calendar-o"></i> <?php echo date('d M, Y', strtotime($row->op_date)); ?></small>
                                </td>
                                <td>
                                    <h5 class="m-0" style="font-weight: 600; font-size: 13px;"><?php echo $row->company_name; ?></h5>
                                    <?php echo ($row->op_type == 1) ? '<span class="text-success small">Domestic</span>' : '<span class="text-danger small">International</span>'; ?>
                                </td>
                                <td>
                                    <div class="text-muted small"><i class="fa fa-user-circle-o"></i> <?php echo strtoupper($row->marketing_person_name); ?></div>
                                </td>
                                <td>
                                    <span class="badge-stage <?php echo $is_cancelled_quotation ? 'badge-stage-cancelled' : ''; ?>"><?php echo $row->current_stage_name; ?></span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar <?php echo $bar_color; ?>" role="progressbar" style="width: <?php echo $prob; ?>%;"></div>
                                    </div>
                                    <span class="prob-text"><?php echo $prob; ?>% Confidence</span>
                                </td>
                                <td>
                                    <?php if($row->latest_quote_id): ?>
                                        <a href="<?php echo page_url; ?>ServiceLeads/view_quotation_pdf/<?php echo $row->latest_quote_id; ?>" target="_blank" class="btn btn-xs btn-link p-0 text-danger">
                                            <i class="fa fa-file-pdf-o"></i> <?php echo $row->op_no; ?>
                                        </a>
                                        <?php if (!empty($row->latest_quote_date)): ?>
                                            <span class="quote-age-note">Quoted <?php echo date('d M Y', strtotime($row->latest_quote_date)); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">No Quote Active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_detail/<?php echo $row->opportunity_id; ?>" class="btn btn-xs btn-primary" title="Pipeline Tracking">
                                            <i class="fa fa-line-chart"></i>
                                        </a>
                                        <a href="<?php echo page_url; ?>ServiceLeads/edit_opportunity/<?php echo $row->opportunity_id; ?>" class="btn btn-xs btn-warning" title="Edit Master Info">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <form method="post" action="<?php echo page_url; ?>ServiceLeads/clone_opportunity/<?php echo $row->opportunity_id; ?>" class="action-form-inline" onsubmit="return confirm('Clone this opportunity as a new stage-1 opportunity?');">
                                            <button type="submit" class="btn btn-xs btn-success" title="Clone Opportunity">
                                                <i class="fa fa-clone"></i> Clone
                                            </button>
                                        </form>
                                        <?php if ($can_reopen_quotation): ?>
                                            <form method="post" action="<?php echo page_url; ?>ServiceLeads/reopen_cancelled_quotation/<?php echo (int) $row->opportunity_id; ?>" class="action-form-inline" onsubmit="return confirm('Reopen this quotation at its previous pipeline stage?');">
                                                <input type="hidden" name="return_url" value="<?php echo page_url; ?>ServiceLeads/opportunity_list/<?php echo (int) $cancelled_quotation_stage_id; ?>">
                                                <button type="submit" class="btn btn-xs btn-info" title="Reopen at previous stage">
                                                    <i class="fa fa-undo"></i> Reopen
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#opportunityTable').DataTable({
                "pageLength": 25,
                "order": [[ 0, "desc" ]],
                "language": {
                    "search": "Quick Search:",
                    "lengthMenu": "Show _MENU_ records",
                    "info": "Displaying _START_ to _END_ of _TOTAL_ opportunities"
                }
            });

            // Style standard search input
            $('.dataTables_filter input').addClass('form-control input-sm').css('width', '250px');
        });
    </script>
</body>
</html>
