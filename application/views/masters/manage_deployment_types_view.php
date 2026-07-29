<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();
$primary_color = $company_info->colorcode ?? '#4a4a4a';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Deployment Types</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f5f7; }
        .page-title { font-size: 22px; font-weight: 600; color: #343a40; }
        .card-box { border-radius: 10px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05); border: none; margin-bottom: 25px; background: #fff; padding: 25px; }
        .section-title { font-size: 15px; font-weight: 600; margin-bottom: 18px; color: #343a40; }
        .table thead th { border-top: none; border-bottom: 2px solid #eef2f7; font-weight: 600; background-color: #f8f9fa; color: #505d69; padding: 12px; }
        .table td { vertical-align: middle; padding: 12px; border-top: 1px solid #eef2f7; }
        .badge-soft-success { background-color: rgba(40, 167, 69, 0.12); color: #28a745; padding: 5px 10px; border-radius: 30px; font-size: 11px; font-weight: 600; }
        .badge-soft-danger { background-color: rgba(220, 53, 69, 0.12); color: #dc3545; padding: 5px 10px; border-radius: 30px; font-size: 11px; font-weight: 600; }
        .btn-theme { background-color: <?php echo $primary_color; ?>; border-color: <?php echo $primary_color; ?>; color: #fff; }
        .btn-theme:hover, .btn-theme:focus { background-color: #333; border-color: #333; color: #fff; }
        .btn-outline-theme { border: 1px solid <?php echo $primary_color; ?>; color: <?php echo $primary_color; ?>; background: #fff; }
        .btn-outline-theme:hover, .btn-outline-theme:focus { background: <?php echo $primary_color; ?>; color: #fff; }
        .action-icon { font-size: 16px; color: #5b69bc; cursor: pointer; }
        .action-icon:hover { color: #3b499c; }
        .helper-text { font-size: 12px; color: #7a7a7a; margin-top: 6px; }
        .modal-header { background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .modal-title { font-weight: 600; color: #333; }
        .dataTables_filter { text-align: right; }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row align-items-center mt-3 mb-3">
                <div class="col-sm-6">
                    <h4 class="page-title">Deployment Types</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Masters</a></li>
                        <li class="breadcrumb-item active">Deployment Types</li>
                    </ol>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo page_url . 'ServiceLeads/engineer_scheduler'; ?>" class="btn btn-outline-theme">
                        <i class="fa fa-calendar"></i> Back To Scheduler
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card-box">
                        <h4 class="section-title">Add Deployment Type</h4>
                        <form id="add_deployment_type_form">
                            <div class="form-group">
                                <label>Type Value</label>
                                <input type="text" name="deployment_type_value" class="form-control" placeholder="Example: Installation" required>
                                <div class="helper-text">This value will be saved in deployment records.</div>
                            </div>
                            <div class="form-group">
                                <label>Display Label</label>
                                <input type="text" name="deployment_type_label" class="form-control" placeholder="Example: New Installation" required>
                            </div>
                            <div class="form-group">
                                <label>Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-theme btn-block">
                                <i class="fa fa-plus-circle"></i> Save Deployment Type
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-box">
                        <h4 class="section-title">Manage Deployment Type List</h4>
                        <div class="table-responsive">
                            <table id="deployment-type-table" class="table table-hover dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type Value</th>
                                        <th>Display Label</th>
                                        <th>Sort Order</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deployment_types as $index => $type) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><strong><?php echo htmlspecialchars($type->deployment_type_value, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                            <td><?php echo htmlspecialchars($type->deployment_type_label, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo (int) $type->sort_order; ?></td>
                                            <td>
                                                <?php if ((int) $type->status === 1) : ?>
                                                    <span class="badge-soft-success">Active</span>
                                                <?php else : ?>
                                                    <span class="badge-soft-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="edit_deployment_type(<?php echo (int) $type->deployment_type_id; ?>)" title="Edit">
                                                    <i class="fa fa-edit action-icon"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit_deployment_type_form">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Deployment Type</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_deployment_type_id" id="edit_deployment_type_id">
                        <div class="form-group">
                            <label>Type Value</label>
                            <input type="text" name="edit_deployment_type_value" id="edit_deployment_type_value" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Display Label</label>
                            <input type="text" name="edit_deployment_type_label" id="edit_deployment_type_label" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="edit_sort_order" id="edit_sort_order" class="form-control" min="0">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="edit_status" id="edit_status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-theme">Update Deployment Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.gritter/1.7.4/js/jquery.gritter.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#deployment-type-table').DataTable({
                language: {
                    paginate: {
                        previous: "<i class='fa fa-angle-left'>",
                        next: "<i class='fa fa-angle-right'>"
                    }
                },
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
            });

            $('#add_deployment_type_form').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "<?php echo page_url . 'Masters/save_deployment_type'; ?>",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 1) {
                            $.gritter.add({ title: 'Success', text: response.message, class_name: 'gritter-success' });
                            setTimeout(function () { location.reload(); }, 900);
                        } else {
                            $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-danger' });
                        }
                    },
                    error: function () {
                        $.gritter.add({ title: 'Error', text: 'Server error while saving deployment type.', class_name: 'gritter-danger' });
                    }
                });
            });

            $('#edit_deployment_type_form').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "<?php echo page_url . 'Masters/update_deployment_type'; ?>",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 1) {
                            $('#edit-modal').modal('hide');
                            $.gritter.add({ title: 'Success', text: response.message, class_name: 'gritter-success' });
                            setTimeout(function () { location.reload(); }, 900);
                        } else {
                            $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-danger' });
                        }
                    },
                    error: function () {
                        $.gritter.add({ title: 'Error', text: 'Server error while updating deployment type.', class_name: 'gritter-danger' });
                    }
                });
            });
        });

        function edit_deployment_type(id) {
            $.ajax({
                url: "<?php echo page_url . 'Masters/get_deployment_type_details'; ?>",
                type: "POST",
                data: { deployment_type_id: id },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        $('#edit_deployment_type_id').val(data.deployment_type_id);
                        $('#edit_deployment_type_value').val(data.deployment_type_value);
                        $('#edit_deployment_type_label').val(data.deployment_type_label);
                        $('#edit_sort_order').val(data.sort_order);
                        $('#edit_status').val(data.status);
                        $('#edit-modal').modal('show');
                    } else {
                        $.gritter.add({ title: 'Error', text: 'Could not fetch deployment type details.', class_name: 'gritter-danger' });
                    }
                },
                error: function () {
                    $.gritter.add({ title: 'Error', text: 'Could not fetch deployment type details.', class_name: 'gritter-danger' });
                }
            });
        }
    </script>
</body>
</html>
