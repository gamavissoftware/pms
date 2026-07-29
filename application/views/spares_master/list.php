<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();
$spares_master_read_only = isset($this->master_profile_guard) && $this->master_profile_guard->is_master_read_only();
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
                { "orderable": false, "targets": [6] }, // Disable sorting on 'Actions' column
                { "searchable": false, "targets": [0, 3, 4, 5, 6] } // Disable searching on non-text columns
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
