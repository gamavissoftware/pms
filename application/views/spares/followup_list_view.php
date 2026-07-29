<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; <?php echo $page_title; ?></title>

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
        .table thead th { background-color: <?php echo $company_info->colorcode ?? '#333'; ?>; color: #fff; font-weight: 600; border: none; white-space: nowrap; }
        .table-hover tbody tr:hover { background-color: #f1f5f9; }
        .action-icons a { margin: 0 5px; font-size: 1.2em; }
        .missed-followup { color: #d9534f; font-weight: bold; }
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
                        <div class="btn-group pull-right">
                             <a href="<?php echo page_url.'dashboard/sparesdashboard'; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
                        </div>
                        <h4 class="page-title"><?php echo $page_title; ?></h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="table-responsive">
                            <table id="followupTable" class="table table-striped table-hover" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Opp. Number</th>
                                        <th>Opp. Date</th>
                                        <th>Followup Date</th> <th>Customer</th>
                                        <th>Marketing Person</th>
                                        <th>Current Stage</th>
                                        <th>Probability</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($opportunities)): ?>
                                    <?php foreach ($opportunities as $op): ?>
                                        <tr>
                                            <td><b><?php echo htmlspecialchars($op->op_no); ?></b></td>
                                            <td><?php echo date('d M, Y', strtotime($op->op_date)); ?></td>
                                            
                                            <td class="<?php echo ($followup_type == 2) ? 'missed-followup' : ''; ?>">
                                                <?php echo date('d M, Y', strtotime($op->next_follow_date)); ?>
                                            </td>
                                            
                                            <td><?php echo htmlspecialchars($op->company_name); ?></td>
                                            <td><?php echo htmlspecialchars($op->marketing_person_name); ?></td>
                                            <td><span class="label label-info"><?php echo htmlspecialchars($op->current_stage_name ?? 'N/A'); ?></span></td>
                                            <td><?php echo $op->probability; ?>%</td>
                                            <td><span class="label label-success"><?php echo htmlspecialchars($op->status); ?></span></td>
                                            <td class="action-icons">
                                               
                                                <a href="<?php echo page_url; ?>Spares/opportunity_detail/<?php echo $op->opportunity_id; ?>" title="Update Progress" class="text-success"><span class="btn btn-warning btn-xs">Update Progress</span></a>
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
        $('#followupTable').DataTable({
            "order": [[ 2, "asc" ]], // Order by the new Followup Date column
            "language": { "search": "Search within results:" }
        });
    });
    </script>
</body>
</html>