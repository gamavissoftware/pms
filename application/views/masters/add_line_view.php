<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, logo, colorcode')->from('company_information')->get()->row();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Add New Production Line">
    <meta name="author" content="<?php echo htmlspecialchars($company_info->company_name ?? 'Company'); ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> &mdash; Add Production Line</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: none; margin-bottom: 25px; }
        .page-title-box { background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .page-title { font-size: 24px; font-weight: 600; color: <?php echo $company_info->colorcode ?? '#333'; ?>; }
        .card-header-title { display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 600; color: #495057; border-bottom: 1px solid #e9ecef; padding-bottom: 10px; margin-bottom: 20px; }
        .form-control { border-radius: 5px; height: 40px; }
        .btn-success { background-color: <?php echo $company_info->colorcode ?? '#28a745'; ?>; border-color: <?php echo $company_info->colorcode ?? '#28a745'; ?>; border-radius: 5px; padding: 10px 20px; font-weight: 500; transition: all 0.3s ease; }
        .btn-success:hover { opacity: 0.9; transform: translateY(-2px); }
        .required-star { color: #e74c3c; margin-left: 3px; }
        label { font-weight: 500; }
        .error { color: #e74c3c; font-size: 0.875em; margin-top: 5px; }
        input.error, select.error { border-color: #e74c3c !important; }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box text-center">
                        <h4 class="page-title">Add New Production Line</h4>
                    </div>
                </div>
            </div>

            <form name="frm" id="line_form" autocomplete="off">
                <div class="card-box">
                    <h5 class="card-header-title"><span>Line Configuration</span></h5>
                    <div class="row">
                        
                        <div class="col-md-6 form-group">
                            <label>Line Category<span class="required-star">*</span></label>
                            <select name="line_category" id="line_category" class="form-control select2" required>
                                <option value="">Select Category</option>
                                <option value="Assembly Line">Assembly Line</option>
                                <option value="Trial Line">Trial Line</option>
                                <option value="R&D Line">R&D Line</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Line Name / Number<span class="required-star">*</span></label>
                            <input type="text" name="line_name" id="line_name" class="form-control" placeholder="e.g. Line 1, Line A" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Short Code</label>
                            <input type="text" name="line_code" id="line_code" class="form-control" placeholder="e.g. AS-L1">
                        </div>

                         <div class="col-md-6 form-group">
                            <label>Description</label>
                            <input type="text" name="description" id="description" class="form-control" placeholder="Optional details">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-check"></i> Save Line
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
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.gritter/1.7.4/js/jquery.gritter.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
    $(document).ready(function() {
        
        // Initialize Select2
        $('.select2').select2({ placeholder: 'Select Option' });

        // --- VALIDATION & AJAX SUBMIT ---
        $('#line_form').validate({
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('error');
                if (element.hasClass('select2-hidden-accessible')) { 
                    error.insertAfter(element.next('.select2-container')); 
                } else { 
                    error.insertAfter(element); 
                }
            },
            highlight: function(element) { $(element).addClass('error'); },
            unhighlight: function(element) { $(element).removeClass('error'); },
            rules: {
                line_category: "required",
                line_name: "required"
            },
            submitHandler: function(form) {
                $.blockUI({ 
                    message: '<h3><i class="fa fa-spinner fa-spin"></i> Saving...</h3>',
                    css: { border: 'none', padding: '15px', backgroundColor: '#000', opacity: .5, color: '#fff', borderRadius: '10px' } 
                });

                $.ajax({
                    url: "<?php echo page_url.'Masters/save_line'; ?>",
                    type: "POST",
                    data: $(form).serialize(),
                    dataType: "json",
                    success: function(response) {
                    $.unblockUI();
                    if (response.status == 1) {

                    // 1. Show the message IMMEDIATELY on the current page
                    $.gritter.add({
                        title: 'Success!',
                        text: 'Line added successfully! Redirecting...',
                        class_name: 'gritter-success',
                        time: 2000
                    });

                    // 2. Wait 1.5 seconds, THEN redirect
                    setTimeout(function(){
                        window.location.href = response.redirect_url;
                    }, 1500);

                    } else {
                    $.gritter.add({
                        title: 'Error',
                        text: response.message,
                        class_name: 'gritter-danger'
                    });
                    }
                    },
                    error: function() {
                        $.unblockUI();
                        alert('Server error occurred.');
                    }
                });
            }
        });

        // --- GRITTER FLASH MESSAGES ---
        <?php if ($this->session->flashdata('success')) { ?>
            $.gritter.add({
                title: 'Success!',
                text: '<?php echo $this->session->flashdata('success'); ?>',
                class_name: 'gritter-success',
                time: 3000
            });
        <?php } ?>
    });
    </script>
</body>
</html>