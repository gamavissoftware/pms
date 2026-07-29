<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; All Opportunities</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"/>
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .page-title { color: <?php echo $company_info->colorcode ?? '#333'; ?>; }
        .card-box { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: none; }
        .filter-card { margin-bottom: 25px; }
        .table thead th { background-color: <?php echo $company_info->colorcode ?? '#333'; ?>; color: #fff; font-weight: 600; border: none; white-space: nowrap; }
        .table-hover tbody tr:hover { background-color: #f1f5f9; }
        .badge { font-size: 0.8em; font-weight: 500; padding: 5px 10px; border-radius: 20px; }
        .badge-open { background-color: #3498db; color: #fff; }
        .badge-won { background-color: #2ecc71; color: #fff; }
        .badge-lost { background-color: #e74c3c; color: #fff; }
        .btn-reset { background-color: #6c757d; border-color: #6c757d; color: #fff; }
        .action-icons a { margin: 0 5px; font-size: 1.2em; }
        td.details-control { background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center; cursor: pointer; }
        tr.details td.details-control { background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center; }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row"> <div class="col-sm-12"> <div class="page-title-box"> <div class="btn-group pull-right"> <a href="<?php echo page_url; ?>Spares/leadform" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i> Add New Opportunity </a> </div> <h4 class="page-title">Opportunities List</h4> </div> </div> </div>

            <div class="card-box filter-card">
                <a data-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false" aria-controls="filterCollapse">
                    <h5 class="m-t-0 header-title"><b><i class="fa fa-filter"></i> Filter Options</b> <i class="fa fa-chevron-down pull-right"></i></h5>
                </a>
                <hr>
                <div class="collapse" id="filterCollapse">
                    <form method="GET" action="<?php echo page_url; ?>Spares/opportunity_list">
                        <div class="row">
                            <div class="col-md-3 form-group"> <label>From Date</label> <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($this->input->get('from_date')); ?>"> </div>
                            <div class="col-md-3 form-group"> <label>To Date</label> <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($this->input->get('to_date')); ?>"> </div>
                            <div class="col-md-3 form-group"> <label>Marketing Person</label> <select name="marketing_person" class="form-control"> <option value="">All Persons</option> <?php foreach ($marketing_persons as $person): ?> <option value="<?php echo $person->user_id; ?>" <?php if($this->input->get('marketing_person') == $person->user_id) echo 'selected'; ?>> <?php echo htmlspecialchars($person->first_name . ' ' . $person->last_name); ?> </option> <?php endforeach; ?> </select> </div>
                            <div class="col-md-3 form-group"> <label>Opportunity Type</label> <select name="op_type" class="form-control"> <option value="">All Types</option> <option value="1" <?php if($this->input->get('op_type') == '1') echo 'selected'; ?>>Domestic</option> <option value="2" <?php if($this->input->get('op_type') == '2') echo 'selected'; ?>>Export</option> </select> </div>
                            <div class="col-md-3 form-group"> <label>Status</label> <select name="status" class="form-control"> <option value="">All Statuses</option> <option value="Open" <?php if($this->input->get('status') == 'Open') echo 'selected'; ?>>Open</option> <option value="Won" <?php if($this->input->get('status') == 'Won') echo 'selected'; ?>>Won</option> <option value="Lost" <?php if($this->input->get('status') == 'Lost') echo 'selected'; ?>>Lost</option> </select> </div>
                            <div class="col-md-3 form-group"> <label>Stage</label> <select name="stage" class="form-control"> <option value="">All Stages</option> <?php foreach ($stages as $stage): ?> <option value="<?php echo $stage->lead_id; ?>" <?php if($this->input->get('stage') == $stage->lead_id) echo 'selected'; ?>> <?php echo htmlspecialchars($stage->lead_name); ?> </option> <?php endforeach; ?> </select> </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right"> <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fa fa-check"></i> Apply Filter</button> <a href="<?php echo page_url; ?>Spares/opportunity_list" class="btn btn-reset waves-effect waves-light"><i class="fa fa-refresh"></i> Reset</a> </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="table-responsive">
                            <table id="opportunitiesTable" class="table table-striped table-hover" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th></th> 
                                        <th>Opp. Number</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Marketing Person</th>
                                        <th>Source</th>
                                        <th>Current Stage</th> 
                                        <th>Probability</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Update Progress</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($opportunities)): ?>
                                    <?php foreach ($opportunities as $op): ?>
                                        <tr data-op-id="<?php echo $op->opportunity_id; ?>">
                                            <td class="details-control"></td>
                                            <td data-order="<?php echo (int) $op->opportunity_id; ?>"><b><?php echo htmlspecialchars($op->op_no); ?></b></td>
                                            <td><?php echo date('d M, Y', strtotime($op->op_date)); ?></td>
                                            <td><?php echo htmlspecialchars($op->company_name); ?></td>
                                            <td><?php echo htmlspecialchars($op->marketing_person_name); ?></td>
                                            <td><?php echo htmlspecialchars($op->lead_source); ?></td>
                                            <td><span class="label label-info"><?php echo htmlspecialchars($op->current_stage_name ?? 'N/A'); ?></span></td>
                                            <td>
                                                <div class="progress" style="height: 10px; margin-bottom: 0; border-radius: 5px;"><div class="progress-bar" role="progressbar" style="width: <?php echo $op->probability; ?>%;" ></div></div>
                                                <small><?php echo $op->probability; ?>%</small>
                                            </td>
                                            <td><?php echo ($op->op_type == 1) ? 'Domestic' : 'Export'; ?></td>
                                            <td>
                                                <?php 
                                                    $status_class = 'badge-open';
                                                    if ($op->status == 'Won') $status_class = 'badge-won';
                                                    if ($op->status == 'Lost') $status_class = 'badge-lost';
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($op->status ?? 'Open'); ?></span>
                                            </td>
                                            <td><a href="<?php echo page_url; ?>Spares/opportunity_detail/<?php echo $op->opportunity_id; ?>" class="btn btn-success btn-xs">Update Progress</a></td>
                                            <td class="action-icons">
                                                <?php if (!empty($op->latest_quotation_id)): ?>
                                                    <a href="<?php echo page_url; ?>Spares/view_quotation_pdf/<?php echo $op->latest_quotation_id; ?>" target="_blank" title="View Latest Quotation" class="text-info"><i class="fa fa-file-pdf-o"></i></a>
                                                <?php endif; ?>
                                                <a href="<?php echo page_url; ?>Spares/edit_opportunity/<?php echo $op->opportunity_id; ?>" title="Edit" class="text-warning"><i class="fa fa-pencil"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        var table = $('#opportunitiesTable').DataTable({
            "order": [[ 1, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [0, 10, 11] } 
            ],
            "language": { "search": "Search within results:" }
        });

        $('#opportunitiesTable tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var opId = tr.data('op-id');
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('details');
            } else {
                tr.addClass('details');
                row.child('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading products...</p>').show();
                $.ajax({
                    url: "<?php echo page_url; ?>Spares/get_opportunity_products_ajax/" + opId,
                    method: "GET",
                    success: function(response) {
                        if (row.child.isShown()) { row.child(response).show(); }
                    },
                    error: function() {
                        if (row.child.isShown()) { row.child('<p class="text-center text-danger">Error loading details.</p>').show(); }
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
