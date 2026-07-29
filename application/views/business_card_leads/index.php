<?php
$brandColor = !empty($company_info->colorcode) ? $company_info->colorcode : '#4a81d4';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Exhibition business card leads">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Exhibition Leads</title>

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        table.pretty thead th {
            background: <?php echo $brandColor; ?>;
            color: #fff;
            font-size: 12px;
            text-align: center;
            vertical-align: middle;
        }

        table.pretty td {
            font-size: 12px;
            vertical-align: top;
        }

        .btns {
            margin-top: 20px;
        }

        .page-subtitle {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .lead-type-badge,
        .status-badge {
            border-radius: 999px;
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
        }

        .lead-type-badge {
            background: #edf4ff;
            color: #1f5fbf;
        }

        .status-active {
            background: #e8f7ee;
            color: #1f7a45;
        }

        .status-inactive {
            background: #fdeeee;
            color: #b42318;
        }

        .cell-prewrap {
            max-width: 260px;
            min-width: 180px;
            white-space: normal;
            word-break: break-word;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }

        .dt-buttons .btn {
            margin-right: 8px;
        }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <a href="<?php echo page_url; ?>Dashboard/opportunity_dashboard" class="btn btn-success btns">
                            <i class="fa fa-arrow-left"></i> Back To Opportunity Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center">Exhibition Business Card Leads</h4>
                        <p class="text-center page-subtitle">All records captured in the <code>business_card_leads</code> table.</p>
                    </div>
                </div>
            </div>

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="example" class="table table-striped table-bordered pretty">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Scanned On</th>
                                    <th>Exhibition</th>
                                    <th>Lead Type</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Remarks</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sr = 1; ?>
                                <?php foreach ($business_card_leads as $lead) : ?>
                                    <tr>
                                        <td><?php echo $sr++; ?></td>
                                        <td><?php echo !empty($lead->scanned_at) ? date('d-m-Y h:i A', strtotime($lead->scanned_at)) : ''; ?></td>
                                        <td class="cell-prewrap"><?php echo htmlspecialchars((string) $lead->exhibition, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><span class="lead-type-badge"><?php echo htmlspecialchars((string) $lead->lead_type, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td><?php echo htmlspecialchars((string) $lead->name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="cell-prewrap"><?php echo htmlspecialchars((string) $lead->company, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $lead->mobile, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="cell-prewrap"><?php echo htmlspecialchars((string) $lead->email, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="cell-prewrap"><?php echo nl2br(htmlspecialchars((string) $lead->address, ENT_QUOTES, 'UTF-8')); ?></td>
                                        <td class="cell-prewrap"><?php echo nl2br(htmlspecialchars((string) $lead->remarks, ENT_QUOTES, 'UTF-8')); ?></td>
                                        <td><?php echo htmlspecialchars((string) $lead->created_by_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo !empty($lead->created_at) ? date('d-m-Y h:i A', strtotime($lead->created_at)) : ''; ?></td>
                                        <td>
                                            <span class="status-badge <?php echo ((int) $lead->status === 1) ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo ((int) $lead->status === 1) ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php $this->load->view('common/footer'); ?>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>

    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        $(document).ready(function () {
            $('#example').DataTable({
                dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>rt<'row'<'col-sm-5'i><'col-sm-7'p>>",
                pageLength: 50,
                responsive: true,
                stateSave: true,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Exhibition Business Card Leads',
                        filename: 'Exhibition_Business_Card_Leads_' + new Date().toISOString().slice(0, 10),
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });
        });
    </script>
</body>
</html>
