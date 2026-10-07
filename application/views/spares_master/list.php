<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();
$spares_master_read_only = isset($this->master_profile_guard) && $this->master_profile_guard->is_master_read_only();
$inventory_summary = isset($inventory_summary) ? $inventory_summary : null;
$recent_stock_uploads = isset($recent_stock_uploads) ? $recent_stock_uploads : array();
$inventory_columns_available = isset($inventory_columns_available) ? $inventory_columns_available : false;
$stock_import_report = $this->session->flashdata('stock_import_report');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Spare Parts Master</title>

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
        .badge { font-size: 0.8em; font-weight: 500; padding: 5px 10px; border-radius: 20px; }
        .badge-success { background-color: #2ecc71; color: #fff; }
        .badge-danger { background-color: #e74c3c; color: #fff; }
        .action-icons a { margin: 0 5px; font-size: 1.2em; }
        .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .inventory-hero { background: linear-gradient(135deg, #ffffff 0%, #eef5ff 100%); border: 1px solid #e3edf7; border-radius: 12px; padding: 20px; margin-bottom: 18px; box-shadow: 0 8px 24px rgba(16, 24, 40, 0.06); }
        .inventory-title { margin: 0 0 5px; font-weight: 600; color: #172033; }
        .inventory-subtitle { color: #667085; margin: 0; }
        .metric-card { background: #fff; border: 1px solid #edf1f5; border-radius: 10px; padding: 16px; min-height: 112px; box-shadow: 0 4px 14px rgba(16, 24, 40, 0.05); }
        .metric-icon { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; color: #fff; }
        .metric-value { font-size: 24px; font-weight: 700; color: #172033; line-height: 1; }
        .metric-label { color: #667085; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; margin-top: 6px; }
        .upload-panel { border: 1px dashed #b7c6d8; border-radius: 10px; padding: 16px; background: #fbfdff; height: 100%; }
        .upload-panel h5 { margin-top: 0; font-weight: 600; color: #172033; }
        .upload-help { color: #667085; font-size: 12px; line-height: 1.5; }
        .stock-pill { display: inline-flex; align-items: center; gap: 8px; border-radius: 18px; padding: 5px 10px; min-width: 122px; justify-content: space-between; font-weight: 600; }
        .stock-qty { color: #172033; }
        .stock-label { font-size: 11px; text-transform: uppercase; letter-spacing: .03em; }
        .stock-ok { background: #e8f7ef; border: 1px solid #bde8ce; }
        .stock-ok .stock-label { color: #178447; }
        .stock-low { background: #fff6df; border: 1px solid #f2d790; }
        .stock-low .stock-label { color: #9b6900; }
        .stock-zero { background: #fdecec; border: 1px solid #f3c3c3; }
        .stock-zero .stock-label { color: #b42318; }
        .recent-upload-table { margin-bottom: 0; }
        .recent-upload-table td, .recent-upload-table th { font-size: 12px; padding: 8px !important; }
        .inventory-hero .text-md-right { text-align: right; }
        @media (max-width: 767px) {
            .inventory-hero .text-md-right { text-align: left; margin-top: 12px; }
            .metric-card { margin-bottom: 12px; }
        }
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
                        <?php if (!$spares_master_read_only) { ?>
                            <div class="btn-group pull-right"> 
                                <a href="<?php echo page_url.'spares_master/add_form'; ?>" class="btn btn-primary waves-effect waves-light"> 
                                    <i class="fa fa-plus"></i> Add New Spare Part
                                </a> 
                            </div>
                        <?php } ?>
                        <h4 class="page-title">Spare Parts Master List</h4> 
                    </div> 
                </div> 
            </div>

            <?php if (!$inventory_columns_available): ?>
                <div class="alert alert-danger">
                    Inventory quantity fields are not available yet. Please run <strong>Database/spares_master_inventory_001.sql</strong> before using stock upload or MRP quantity checks.
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($stock_import_report) && !empty($stock_import_report['messages'])): ?>
                        <div class="alert alert-danger">
                            <strong>Stock import report</strong>
                            <ul style="margin:8px 0 0 18px;">
                                <?php foreach (array_slice($stock_import_report['messages'], 0, 10) as $message): ?>
                                    <li><?php echo $message; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($stock_import_report['messages']) > 10): ?>
                                <div class="text-muted">Showing first 10 issues only. Full summary is saved in upload history.</div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="inventory-hero">
                <div class="row">
                    <div class="col-md-7">
                        <h3 class="inventory-title">Spares Inventory Control</h3>
                        <p class="inventory-subtitle">Maintain available quantity for spare parts. The MRP run will use this quantity to calculate shortage.</p>
                    </div>
                    <div class="col-md-5 text-md-right">
                        <a href="<?php echo page_url.'spares_master/download_stock_format'; ?>" class="btn btn-default">
                            <i class="fa fa-download"></i> Download Format
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="metric-card">
                        <div class="metric-icon" style="background:#2f80ed;"><i class="fa fa-cubes"></i></div>
                        <div class="metric-value"><?php echo $inventory_summary ? number_format($inventory_summary->total_parts) : '0'; ?></div>
                        <div class="metric-label">Total Parts</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="metric-card">
                        <div class="metric-icon" style="background:#27ae60;"><i class="fa fa-check-circle"></i></div>
                        <div class="metric-value"><?php echo $inventory_summary ? number_format($inventory_summary->active_parts) : '0'; ?></div>
                        <div class="metric-label">Active Parts</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="metric-card">
                        <div class="metric-icon" style="background:#9b51e0;"><i class="fa fa-database"></i></div>
                        <div class="metric-value"><?php echo $inventory_summary ? number_format($inventory_summary->available_qty, 3) : '0.000'; ?></div>
                        <div class="metric-label">Available Qty</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="metric-card">
                        <div class="metric-icon" style="background:#eb5757;"><i class="fa fa-exclamation-triangle"></i></div>
                        <div class="metric-value"><?php echo $inventory_summary ? number_format($inventory_summary->zero_stock) : '0'; ?></div>
                        <div class="metric-label">Zero Stock Parts</div>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:15px;">
                <?php if (!$spares_master_read_only) { ?>
                    <div class="col-md-5">
                        <div class="card-box upload-panel">
                            <h5><i class="fa fa-upload"></i> Upload Finsys Stock</h5>
                            <p class="upload-help">Upload the stock file downloaded from Finsys. Required columns are part code and quantity. Accepted headings include Code, Part Code, Item Code, Material Code, Qty, Quantity, Stock, and Closing Stock.</p>
                            <form action="<?php echo page_url.'spares_master/upload_stock'; ?>" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="file" name="stock_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                </div>
                                <button type="submit" class="btn btn-primary" <?php echo $inventory_columns_available ? '' : 'disabled'; ?>>
                                    <i class="fa fa-refresh"></i> Update Available Qty
                                </button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
                <div class="<?php echo !$spares_master_read_only ? 'col-md-7' : 'col-md-12'; ?>">
                    <div class="card-box">
                        <h5 style="margin-top:0;"><i class="fa fa-history"></i> Recent Stock Uploads</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered recent-upload-table">
                                <thead>
                                    <tr>
                                        <th>Uploaded On</th>
                                        <th>File</th>
                                        <th>Rows</th>
                                        <th>Updated</th>
                                        <th>Failed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_stock_uploads)): ?>
                                        <?php foreach ($recent_stock_uploads as $upload): ?>
                                            <tr>
                                                <td><?php echo date('d M, Y h:i A', strtotime($upload->uploaded_on)); ?></td>
                                                <td><?php echo html_escape($upload->file_name); ?></td>
                                                <td><?php echo (int) $upload->total_rows; ?></td>
                                                <td><?php echo (int) $upload->updated_rows; ?></td>
                                                <td><?php echo (int) $upload->failed_rows; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-muted text-center">No stock upload history yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="table-responsive">
                            <table id="sparePartsTable" class="table table-striped table-hover" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Available Qty</th>
                                        <th>Stock Updated</th>
                                        <th>Status</th>
                                        <th>Added On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
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
        $('#sparePartsTable').DataTable({
            "processing": true, // Show processing indicator
            "serverSide": true, // Enable server-side processing
            "order": [[ 0, "desc" ]], // Default order by ID
            "ajax": {
                "url": "<?php echo page_url.'spares_master/get_parts_list_ajax'; ?>",
                "type": "POST"
            },
            "columnDefs": [
                { "orderable": false, "targets": [8] }, // Disable sorting on 'Actions' column
                { "searchable": false, "targets": [0, 3, 4, 5, 6, 7, 8] } // Disable searching on non-text columns
            ],
            "language": { "search": "Search Code or Description:" }
        });
    });

    function confirmDelete() {
        return confirm('Are you sure you want to set this part to Inactive?');
    }
    </script>
</body>
</html>
