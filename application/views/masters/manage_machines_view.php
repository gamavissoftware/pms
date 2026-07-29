<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Machines</title>
    <!-- Include all CSS (Bootstrap, DataTables, Gritter) similar to Lines view -->
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f5f7; }
        .card-box { border-radius: 8px; border: none; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row align-items-center mt-3 mb-3">
                <div class="col-6"><h4 class="page-title">Machines</h4></div>
                <div class="col-6 text-right">
                    <a href="<?php echo page_url.'Masters/add_machine'; ?>" class="btn btn-primary btn-rounded"><i class="fa fa-plus-circle"></i> Add Machine</a>
                </div>
            </div>

            <div class="card-box">
                <table id="datatable" class="table table-hover dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Machine Name</th>
                            <th>Line</th>
                            <th>Code/Model</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($machines as $k => $row): ?>
                        <tr>
                            <td><?php echo $k+1; ?></td>
                            <td><strong><?php echo $row->machine_name; ?></strong></td>
                            <td><span class="badge badge-primary"><?php echo $row->line_name; ?></span></td>
                            <td><?php echo $row->machine_code . ' / ' . $row->model_no; ?></td>
                            <td><?php echo ($row->status==1)?'<span class="badge badge-success">Active</span>':'<span class="badge badge-danger">Inactive</span>'; ?></td>
                            <td>
                                <a href="javascript:void(0)" onclick="edit_machine(<?php echo $row->machine_id; ?>)" class="text-primary"><i class="fa fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit_machine_form">
                    <div class="modal-header"><h5 class="modal-title">Edit Machine</h5><button type="button" class="close" data-dismiss="modal">×</button></div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_machine_id" id="edit_machine_id">
                        <div class="form-group">
                            <label>Line</label>
                            <select name="edit_line_id" id="edit_line_id" class="form-control" required>
                                <?php foreach($lines as $line): echo "<option value='".$line->line_id."'>".$line->line_name."</option>"; endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group"><label>Name</label><input type="text" name="edit_machine_name" id="edit_machine_name" class="form-control" required></div>
                        <div class="form-group"><label>Code</label><input type="text" name="edit_machine_code" id="edit_machine_code" class="form-control"></div>
                        <div class="form-group"><label>Model</label><input type="text" name="edit_model_no" id="edit_model_no" class="form-control"></div>
                        <div class="form-group"><label>Status</label><select name="edit_status" id="edit_status" class="form-control"><option value="1">Active</option><option value="0">Inactive</option></select></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.gritter/1.7.4/js/jquery.gritter.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#datatable').DataTable();
        
        // Handle Edit Form
        $('#edit_machine_form').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: "<?php echo page_url.'Masters/update_machine'; ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(resp){
                    if(resp.status==1){ location.reload(); }
                }
            });
        });

        // Flash Message
        <?php if($this->session->flashdata('success')){ ?>
            $.gritter.add({ title:'Success', text:'<?php echo $this->session->flashdata('success'); ?>', class_name:'gritter-success' });
        <?php } ?>
    });

    function edit_machine(id){
        $.post("<?php echo page_url.'Masters/get_machine_details'; ?>", {machine_id:id}, function(data){
            var obj = JSON.parse(data);
            $('#edit_machine_id').val(obj.machine_id);
            $('#edit_line_id').val(obj.line_id);
            $('#edit_machine_name').val(obj.machine_name);
            $('#edit_machine_code').val(obj.machine_code);
            $('#edit_model_no').val(obj.model_no);
            $('#edit_status').val(obj.status);
            $('#edit-modal').modal('show');
        });
    }
    </script>
</body>
</html>