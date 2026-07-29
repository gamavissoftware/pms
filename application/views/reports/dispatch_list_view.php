<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Dispatch & Accounts Report</title>

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
        .widget-box h3 { font-weight: 700; margin: 5px 0; font-size: 20px; }
        .widget-box p { color: #888; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; margin-bottom: 0; font-weight: 600; }
        
        /* Table & Badge Styling */
        .badge-stage { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: 600; padding: 4px 8px; border-radius: 4px; font-size: 10px; }
        .table thead th { background-color: #f8fafc; color: #334155; font-weight: 600; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; }
        .table tbody tr td { vertical-align: middle; padding: 12px 8px; font-size: 13px; }
        
        .text-due { color: #ef4444; font-weight: 600; }
        .text-paid { color: #10b981; font-weight: 600; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            
            <!-- KPI Summary Widgets based on Mcs Dispatch Data -->
            <div class="card-box">
                <div class="row text-center">
                    <div class="col-md-2 widget-box">
                        <p>Total Machines</p>
                        <h3 class="text-dark"><?php echo number_format($kpi['total_qty']); ?></h3>
                    </div>
                    <div class="col-md-3 widget-box">
                        <p>Total Invoice Value</p>
                        <h3 class="text-primary">₹<?php echo number_format($kpi['total_inv_amt'], 2); ?></h3>
                    </div>
                    <div class="col-md-2 widget-box">
                        <p>Taxable Sale</p>
                        <h3 class="text-muted">₹<?php echo number_format($kpi['total_taxable'], 2); ?></h3>
                    </div>
                    <div class="col-md-3 widget-box">
                        <p>Payment Received</p>
                        <h3 class="text-success">₹<?php echo number_format($kpi['total_received'], 2); ?></h3>
                    </div>
                    <div class="col-md-2 widget-box">
                        <p>Pending Balance</p>
                        <h3 class="text-danger">₹<?php echo number_format($kpi['total_balance'], 2); ?></h3>
                    </div>
                </div>
            </div>

            <div class="row m-b-10">
                <div class="col-sm-8">
                    <h4 class="page-title">Dispatch & Commissioning Report</h4>
                </div>
                <div class="col-sm-4 text-right">
                    <button class="btn btn-default btn-sm waves-effect" type="button" data-toggle="collapse" data-target="#filterCollapse">
                        <i class="fa fa-filter"></i> Filters
                    </button>
                    <a href="<?php echo page_url; ?>DispatchReport/export_excel" class="btn btn-success btn-sm waves-effect waves-light">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </a>
                </div>
            </div>

            <!-- Advanced Filter Section -->
            <div class="collapse m-b-20 <?php echo ($this->input->get('from_date')) ? 'show' : ''; ?>" id="filterCollapse">
                <div class="card-box" style="background: #fdfdfd;">
                    <form method="get">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="small font-600">Invoice From</label>
                                <input type="date" name="from_date" class="form-control input-sm" value="<?php echo $this->input->get('from_date'); ?>">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-600">Invoice To</label>
                                <input type="date" name="to_date" class="form-control input-sm" value="<?php echo $this->input->get('to_date'); ?>">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-600">Payment Status</label>
                                <select name="payment_status" class="form-control input-sm">
                                    <option value="">-- All --</option>
                                    <option value="Due">Due</option>
                                    <option value="Not Due">Not Due</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-600">&nbsp;</label>
                                <button type="submit" class="btn btn-block btn-sm btn-primary"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-box">
                <div class="table-responsive">
                    <table id="dispatchTable" class="table table-hover m-0">
                        <thead>
                            <tr>
                                <th>DF / Invoice Ref</th>
                                <th>Customer & Machine</th>
                                <th>Financials (INR)</th>
                                <th>Payment Details</th>
                                <th>Commissioning Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($reports)): foreach($reports as $row): ?>
                            <tr>
                                <td>
                                    <span class="text-muted small">DF: <?php echo $row->df_no; ?></span><br>
                                    <strong class="text-dark"><?php echo $row->inv_no; ?></strong><br>
                                    <small class="text-muted"><?php echo date('d M, Y', strtotime($row->invoice_date)); ?></small>
                                </td>
                                <td>
                                    <h5 class="m-0" style="font-weight: 600; font-size: 13px;"><?php echo $row->customer_name; ?></h5>
                                    <span class="text-primary small"><?php echo $row->machine_model; ?> (Qty: <?php echo $row->qty; ?>)</span><br>
                                    <small class="text-muted">PO: <?php echo $row->po_no; ?></small>
                                </td>
                                <td>
                                    <table class="table-condensed p-0 m-0" style="width:100%; background:transparent;">
                                        <tr><td class="p-0 border-0 small">Inv Amt:</td><td class="p-0 border-0 text-right"><strong><?php echo number_format($row->inv_amt, 2); ?></strong></td></tr>
                                        <tr><td class="p-0 border-0 small">Taxable:</td><td class="p-0 border-0 text-right"><?php echo number_format($row->taxable_sale, 2); ?></td></tr>
                                    </table>
                                </td>
                                <td>
                                    <span class="text-success">Recd: ₹<?php echo number_format($row->payment_recd, 2); ?></span><br>
                                    <span class="<?php echo ($row->balance_amt > 0) ? 'text-due' : 'text-paid'; ?>">
                                        Bal: ₹<?php echo number_format($row->balance_amt, 2); ?>
                                    </span><br>
                                    <span class="badge-stage"><?php echo $row->payment_status; ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo ($row->comm_status == 'completed') ? 'success' : 'warning'; ?>">
                                        <?php echo strtoupper($row->comm_status); ?>
                                    </span><br>
                                    <small class="text-muted">I&C: <?php echo ($row->ic_date != '0000-00-00') ? date('d-m-Y', strtotime($row->ic_date)) : 'N/A'; ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo page_url; ?>DispatchReport/view/<?php echo $row->id; ?>" class="btn btn-xs btn-white" title="View Detail"><i class="fa fa-eye"></i></a>
                                        <a href="<?php echo page_url; ?>DispatchReport/edit/<?php echo $row->id; ?>" class="btn btn-xs btn-white" title="Update Payment"><i class="fa fa-money text-success"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center">No dispatch records found.</td></tr>
                            <?php endif; ?>
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
            $('#dispatchTable').DataTable({
                "pageLength": 25,
                "order": [[ 0, "desc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 5 }
                ],
                "language": {
                    "search": "Quick Find:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
            $('.dataTables_filter input').addClass('form-control input-sm').css('width', '200px');
        });
    </script>
</body>
</html>