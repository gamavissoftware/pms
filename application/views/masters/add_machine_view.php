<?php defined('BASEPATH') or exit('No direct script access allowed'); 
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Machine</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { border-radius: 10px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-success { background-color: <?php echo $company_info->colorcode ?? '#28a745'; ?>; border:none; }
        .error { color: #e74c3c; font-size: 0.85em; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row mt-4 mb-3">
                <div class="col-12 text-center">
                    <h4 class="page-title">Add New Machine</h4>
                </div>
            </div>
            
            <form id="machine_form" autocomplete="off">
                <div class="card-box">
                    <div class="row">
                        <!-- Select Line -->
                        <div class="col-md-6 form-group">
                            <label>Production Line <span class="text-danger">*</span></label>
                            <select name="line_id" class="form-control select2" required>
                                <option value="">Select Line</option>
                                <?php foreach($lines as $line): ?>
                                    <option value="<?php echo $line->line_id; ?>">
                                        <?php echo $line->line_name . ' (' . $line->line_category . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Machine Name <span class="text-danger">*</span></label>
                            <input type="text" name="machine_name" class="form-control" placeholder="e.g. Robot Arm 01" required>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Machine Code</label>
                            <input type="text" name="machine_code" class="form-control" placeholder="e.g. MC-001">
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Model Number</label>
                            <input type="text" name="model_no" class="form-control" placeholder="Optional">
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-save"></i> Save Machine
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php $this->load->view('common/footer'); ?>
    
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.gritter/1.7.4/js/jquery.gritter.min.js"></script>

    <script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#machine_form').validate({
            submitHandler: function(form) {
                $.blockUI({ message: '<h3>Saving...</h3>', css: { padding: '10px', background: '#000', color: '#fff', border: 'none' } });
                $.ajax({
                    url: "<?php echo page_url.'Masters/save_machine'; ?>",
                    type: "POST",
                    data: $(form).serialize(),
                    dataType: "json",
                    success: function(resp) {
                        $.unblockUI();
                        if(resp.status == 1) {
                            $.gritter.add({ title:'Success', text:'Redirecting...', class_name:'gritter-success' });
                            setTimeout(function(){ window.location.href = resp.redirect_url; }, 1000);
                        } else {
                            $.gritter.add({ title:'Error', text:resp.message, class_name:'gritter-danger' });
                        }
                    }
                });
            }
        });
    });
    </script>
</body>
</html>