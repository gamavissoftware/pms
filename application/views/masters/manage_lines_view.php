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
    <title>Manage Production Lines</title>
    
    <!-- Core CSS -->
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f5f7; }
        .page-title { font-size: 22px; font-weight: 600; color: #343a40; }
        .card-box { border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); border: none; margin-bottom: 25px; background: #fff; padding: 25px; }
        
        /* Table Styling */
        .table thead th { border-top: none; border-bottom: 2px solid #eef2f7; font-weight: 600; background-color: #f8f9fa; color: #505d69; padding: 12px; }
        .table td { vertical-align: middle; padding: 12px; border-top: 1px solid #eef2f7; }
        .badge-soft-success { background-color: rgba(40, 167, 69, 0.1); color: #28a745; padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        
        /* Buttons */
        .btn-add-new { background-color: <?php echo $primary_color; ?>; color: #fff; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transition: all 0.3s; }
        .btn-add-new:hover { background-color: #333; color: #fff; transform: translateY(-2px); }
        .action-icon { font-size: 16px; padding: 5px; cursor: pointer; transition: color 0.2s; }
        .edit-icon { color: #5b69bc; }
        .edit-icon:hover { color: #3b499c; }
        
        /* Modal */
        .modal-header { background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .modal-title { font-weight: 600; color: #333; }
        label { font-weight: 500; font-size: 13px; color: #6c757d; }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            
            <!-- Header Section -->
            <div class="row align-items-center mt-3 mb-3">
                <div class="col-sm-6">
                    <h4 class="page-title">Production Lines</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Masters</a></li>
                        <li class="breadcrumb-item active">Lines</li>
                    </ol>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo page_url.'Masters/add_line'; ?>" class="btn btn-add-new btn-rounded waves-effect waves-light">
                        <i class="fa fa-plus-circle mr-1"></i> Add New Line
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <table id="datatable" class="table table-hover table-centered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Line Name</th>
                                    <th>Code</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th class="text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($lines)): ?>
                                    <?php foreach($lines as $key => $row): ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?></td>
                                        <td><span class="text-dark font-weight-bold"><?php echo $row->line_category; ?></span></td>
                                        <td><?php echo $row->line_name; ?></td>
                                        <td><span class="badge badge-light border"><?php echo $row->line_code ? $row->line_code : '-'; ?></span></td>
                                        <td>
                                            <?php if($row->status == 1): ?>
                                                <span class="badge badge-soft-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?php echo $row->description; ?></small></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" onclick="edit_line(<?php echo $row->line_id; ?>)" class="action-icon edit-icon" title="Edit">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
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

    <!-- Edit Modal -->
    <div id="edit-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="edit_line_form">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Line Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_line_id" id="edit_line_id">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Line Category <span class="text-danger">*</span></label>
                                <select name="edit_line_category" id="edit_line_category" class="form-control" required>
                                    <option value="Assembly Line">Assembly Line</option>
                                    <option value="Trial Line">Trial Line</option>
                                    <option value="R&D Line">R&D Line</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Line Name <span class="text-danger">*</span></label>
                                <input type="text" name="edit_line_name" id="edit_line_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Short Code</label>
                                <input type="text" name="edit_line_code" id="edit_line_code" class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Status</label>
                                <select name="edit_status" id="edit_status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Description</label>
                                <input type="text" name="edit_description" id="edit_description" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light" style="background-color: <?php echo $primary_color; ?>; border-color: <?php echo $primary_color; ?>;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <!-- Scripts -->
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.gritter/1.7.4/js/jquery.gritter.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#datatable').DataTable({
                "language": {
                    "paginate": { "previous": "<i class='fa fa-angle-left'>", "next": "<i class='fa fa-angle-right'>" }
                },
                "drawCallback": function () { $('.dataTables_paginate > .pagination').addClass('pagination-rounded'); }
            });

            // Handle Update Form Submit
            $('#edit_line_form').on('submit', function(e) {
                e.preventDefault();
                $.blockUI({ message: '<h3>Processing...</h3>', css: { border: 'none', padding: '15px', backgroundColor: '#000', opacity: .5, color: '#fff', borderRadius: '10px' } });

                $.ajax({
                    url: "<?php echo page_url.'Masters/update_line'; ?>",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.status == 1) {
                            $('#edit-modal').modal('hide');
                            $.gritter.add({ title: 'Success', text: 'Line updated successfully! Reloading...', class_name: 'gritter-success' });
                            setTimeout(function(){ location.reload(); }, 1000);
                        } else {
                            $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-danger' });
                        }
                    },
                    error: function() {
                        $.unblockUI();
                        alert('Server error.');
                    }
                });
            });
            
            // Show Success Flashdata if exists (e.g. from Add page)
            <?php if ($this->session->flashdata('success')) { ?>
                $.gritter.add({ title: 'Success!', text: '<?php echo $this->session->flashdata('success'); ?>', class_name: 'gritter-success', time: 3000 });
            <?php } ?>
        });

        // Function to Fetch Data & Open Modal
        function edit_line(id) {
            $.blockUI({ message: null }); // Minimal blocking
            $.ajax({
                url: "<?php echo page_url.'Masters/get_line_details'; ?>",
                type: "POST",
                data: { line_id: id },
                dataType: "json",
                success: function(data) {
                    $.unblockUI();
                    if(data) {
                        $('#edit_line_id').val(data.line_id);
                        $('#edit_line_category').val(data.line_category);
                        $('#edit_line_name').val(data.line_name);
                        $('#edit_line_code').val(data.line_code);
                        $('#edit_description').val(data.description);
                        $('#edit_status').val(data.status);
                        
                        $('#edit-modal').modal('show');
                    }
                },
                error: function() {
                    $.unblockUI();
                    alert('Could not fetch data.');
                }
            });
        }
    </script>
</body>
</html>