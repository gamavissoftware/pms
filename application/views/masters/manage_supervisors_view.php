<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Supervisors</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f5f7; }
        .card-box { border-radius: 8px; border: none; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .select2-container { width: 100% !important; }
        .modal-header { background: #f8f9fa; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row align-items-center mt-3 mb-3">
                <div class="col-6"><h4 class="page-title">Shop Floor Supervisors</h4></div>
                <div class="col-6 text-right">
                    <button class="btn btn-primary btn-rounded" onclick="open_modal()">
                        <i class="fa fa-user-plus"></i> Add Supervisor
                    </button>
                </div>
            </div>

            <div class="card-box">
                <table id="datatable" class="table table-hover dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supervisor Name</th>
                            <th>Emp Code</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($supervisors as $k => $row): ?>
                        <tr>
                            <td><?php echo $k+1; ?></td>
                            <td><strong><?php echo $row->supervisor_name; ?></strong></td>
                            <td><?php echo $row->emp_code; ?></td>
                            <td><?php echo $row->contact_no; ?></td>
                            <td><?php echo $row->email; ?></td>
                            <td>
                                <a href="javascript:void(0)" onclick="edit_supervisor(<?php echo $row->supervisor_id; ?>)" class="text-primary" title="Edit">
                                    <i class="fa fa-pencil-square-o fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="sup-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="sup_form" autocomplete="off">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_title">Add Supervisor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="supervisor_id" id="supervisor_id">
                        <input type="hidden" name="supervisor_name" id="supervisor_name_hidden">
                        
                        <div class="form-group">
                            <label>Department <span class="text-danger">*</span></label>
                            <select id="department_id" class="form-control select2" onchange="fetch_users(this.value)">
                                <option value="">Select Department</option>
                                <?php foreach($departments as $dept): ?>
                                    <option value="<?php echo $dept->department_id; ?>">
                                        <?php echo $dept->department; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select User <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-control select2" required onchange="fill_user_info(this.value)" disabled>
                                <option value="">Select Department First</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Employee Code</label>
                                <input type="text" name="emp_code" id="emp_code" class="form-control" readonly style="background-color: #f1f1f1;">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Contact No</label>
                                <input type="text" name="contact_no" id="contact_no" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control" readonly style="background-color: #f1f1f1;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>
    
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.gritter/1.7.4/js/jquery.gritter.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#datatable').DataTable();
        $('.select2').select2({ placeholder: 'Select Option' });

        // Save Form
        $('#sup_form').on('submit', function(e){
            e.preventDefault();
            
            // Set Hidden Name Field from Selected Option Text
            var selectedText = $("#user_id option:selected").text();
            if(selectedText) {
                $("#supervisor_name_hidden").val(selectedText.trim());
            }

            $.ajax({
                url: "<?php echo page_url.'Masters/save_supervisor'; ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(resp){
                    if(resp.status==1){ 
                        location.reload(); 
                    } else {
                        $.gritter.add({ title:'Error', text:resp.message, class_name:'gritter-danger' });
                    }
                }
            });
        });
        
        // Show Flash Messages
        <?php if($this->session->flashdata('success')){ ?>
            $.gritter.add({ title:'Success', text:'<?php echo $this->session->flashdata('success'); ?>', class_name:'gritter-success' });
        <?php } ?>
    });

    // Step 1: Fetch Users when Department is selected
    function fetch_users(dept_id, selected_user_id = null) {
        if(!dept_id) {
            $('#user_id').html('<option value="">Select Department First</option>').prop('disabled', true);
            return;
        }

        $('#user_id').prop('disabled', true).html('<option>Loading...</option>');

        $.post("<?php echo page_url.'Masters/get_users_by_dept'; ?>", {dept_id: dept_id}, function(data){
            var users = JSON.parse(data);
            var options = '<option value="">Select User</option>';
            
            if(users.length > 0) {
                $.each(users, function(index, user){
                    var isSelected = (selected_user_id && selected_user_id == user.user_id) ? 'selected' : '';
                    options += `<option value="${user.user_id}" ${isSelected}>${user.first_name} ${user.last_name}</option>`;
                });
                $('#user_id').html(options).prop('disabled', false);
            } else {
                $('#user_id').html('<option value="">No Active Users in Department</option>').prop('disabled', true);
            }
        });
    }

    // Step 2: Auto-fill details when User is selected
    function fill_user_info(user_id) {
        if(!user_id) return;
        $.post("<?php echo page_url.'Masters/get_system_user_details'; ?>", {user_id: user_id}, function(data){
            var resp = JSON.parse(data);
            if(resp.status == 1) {
                $('#email').val(resp.data.email);
                $('#contact_no').val(resp.data.mobile); 
                $('#emp_code').val(resp.data.emp_code);
            }
        });
    }

    function open_modal() {
        $('#sup_form')[0].reset();
        $('#supervisor_id').val('');
        $('#department_id').val('').trigger('change');
        $('#user_id').html('<option value="">Select Department First</option>').prop('disabled', true);
        $('#modal_title').text('Add Supervisor');
        $('#sup-modal').modal('show');
    }

    function edit_supervisor(id) {
        $.post("<?php echo page_url.'Masters/get_supervisor_details'; ?>", {id:id}, function(data){
            var obj = JSON.parse(data);
            
            $('#supervisor_id').val(obj.supervisor_id);
            $('#supervisor_name_hidden').val(obj.supervisor_name);
            $('#emp_code').val(obj.emp_code);
            $('#contact_no').val(obj.contact_no);
            $('#email').val(obj.email);

            // Pre-select Department
            if(obj.department_id) {
                $('#department_id').val(obj.department_id).trigger('change');
                
                // Wait slightly for users to load, then select the user
                setTimeout(function(){
                    fetch_users(obj.department_id, obj.user_id);
                }, 100);
            }

            $('#modal_title').text('Edit Supervisor');
            $('#sup-modal').modal('show');
        });
    }
    </script>
</body>
</html>